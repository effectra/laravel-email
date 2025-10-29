<?php

declare(strict_types=1);

namespace Effectra\LaravelEmail\Services\Imap;

use Effectra\LaravelEmail\DataObjects\Attachment;
use Effectra\LaravelEmail\Exceptions\ImapException;

class AttachmentHandler
{

    private ?string $attachmentsDir;

    public function __construct(private readonly Attachment $attachment)
    {
    }

    private function sanitizeFilename(string $name): string
    {
        return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $name);
    }

    public function save(): bool|string
    {
        if (empty($this->attachment->filename) || empty($this->attachment->content_base64)) {
            return false;
        }

        if (!$this->attachmentsDir) {
            throw new ImapException('Attachments directory is not set.');
        }

        if (!is_dir($this->attachmentsDir)) {
            mkdir($this->attachmentsDir, 0755, true);
        }

        $filename = $this->sanitizeFilename($this->attachment->filename);
        $path = rtrim($this->attachmentsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
        $content = base64_decode($this->attachment->content_base64);

        if(!file_put_contents($path, $content)){
            throw new ImapException("Saving file failed");
        }

        return $path;
    }

    /**
     * Get the value of attachmentsDir
     */ 
    public function getAttachmentsDir(): string|null
    {
        return $this->attachmentsDir;
    }

    /**
     * Set the value of attachmentsDir
     *
     * @return  self
     */ 
    public function setSavedDir(?string $attachmentsDir): static
    {
        $this->attachmentsDir = $attachmentsDir;

        return $this;
    }
}
