<?php


namespace Effectra\LaravelEmail\DataObjects;

use Effectra\LaravelEmail\Services\Imap\DebugTrait;

/**
 * class Attachment
 * 
 * @property string $filename
 * @property string $mime
 * @property string $size
 * @property string $content_base64
 */
class Attachment
{

    use DebugTrait;

    public readonly string $filename;
    public readonly string $mime;
    public readonly string $size;
    public readonly string $content_base64;

    /**
     * @param string $filename
     * @param string $mime
     * @param string $size
     * @param string $content_base64
     */
    public function __construct(string $filename, string $mime, string $size, string $content_base64)
    {
        $this->filename = $filename;
        $this->mime = $mime;
        $this->size = $size;
        $this->content_base64 = $this->makeOnDebug($content_base64);
    }

    public function toArray(): array
    {
        return [
            'filename' => $this->filename,
            'mime' => $this->mime,
            'size' => $this->size,
            'content_base64' => $this->content_base64,
        ];
    }
}