<?php

declare(strict_types=1);

namespace Effectra\LaravelEmail\Services\Imap;

use Effectra\LaravelEmail\DataObjects\Email;
use Effectra\LaravelEmail\Exception\ImapException;


class MarkEmail {

    public function __construct(private \IMAP\Connection $connection, private Email $email) {
    }

    /**
     * Mark a message as seen.
     */
    public function markSeen(): void
    {
        imap_setflag_full($this->connection, (string) $this->email->uid, "\\Seen", ST_UID);
    }

    /**
     * Delete a message.
     */
    public function delete(): void
    {
        imap_delete($this->connection, (string) $this->email->uid, FT_UID);
        imap_expunge($this->connection);
    }

    /**
     * Move a message to another folder.
     */
    public function move( string $folder): void
    {
        if (!imap_mail_move($this->connection, (string) $this->email->uid, $folder, CP_UID)) {
            throw new ImapException('Failed to move message: ' . imap_last_error());
        }
        imap_expunge($this->connection);
    }
}