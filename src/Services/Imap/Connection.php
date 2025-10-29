<?php

declare(strict_types=1);

namespace Effectra\LaravelEmail\Services\Imap;

use Effectra\LaravelEmail\Exceptions\ImapException;


class Connection implements \Effectra\LaravelEmail\Contracts\ConnectionInterface
{

    private $connection;

    public function __construct(
        private readonly string $mailBox,
        private readonly string $username,
        private readonly string $password
    ) {
        $this->connect();
    }

    /**
     * Establish the IMAP connection.
     * @throws ImapException
     * @return void
     */
    public function connect()
    {
        $this->connection = imap_open($this->mailBox, $this->username, $this->password);

        if (!$this->connection) {
            throw new ImapException('IMAP Connection failed: ' . imap_last_error());
        }

    }

    /**
     * Close the IMAP connection.
     * @return void
     */
    public function close()
    {
        if ($this->connection) {
            imap_close($this->connection);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Get Imap Connection
     * @return bool|\IMAP\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * build MailBox path 
     * @param string $host
     * @param int $port
     * @param string $protocol
     * @return string
     */
    public static function buildMailBox(string $host, int $port,string $protocol = 'imap'): string
    {
        return sprintf("{%s:%s/%s/ssl}INBOX", $host, $port, strtolower($protocol));
    }
}