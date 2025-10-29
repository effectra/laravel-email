<?php

declare(strict_types=1);

namespace Effectra\LaravelEmail\Services\Imap;

use Effectra\EmailAddressFormatter\Address;
use Effectra\LaravelEmail\DataObjects\Attachment;
use Effectra\LaravelEmail\DataObjects\Email;
use Effectra\LaravelEmail\DataObjects\EmailBody;
use Effectra\LaravelEmail\Exceptions\ImapException;

class MailRetriever
{

    private \IMAP\Connection $connection;

    public function __construct(private readonly Connection $imapConnector)
    {
        $this->connection = $imapConnector->getConnection();
    }

    /**
     * Retrieve emails from the IMAP server.
     * @param int $limit
     * @param string $criteria
     * @return array<\Effectra\LaravelEmail\DataObjects\Email>
     */
    public function getMails(int $limit = 10, string $criteria = 'ALL'): array
    {
        try {
            $connection = $this->connection;

            $uids = imap_search($connection, $criteria, SE_UID);

            if (!$uids) {
                return [];
            }

            rsort($uids);
            $uids = array_slice($uids, 0, $limit);

            $messages = [];

            foreach ($uids as $uid) {
                $msgno = imap_msgno($connection, $uid);
                if ($msgno <= 0) {
                    continue;
                }

                $overviewArr = imap_fetch_overview($connection, (string) $uid, FT_UID);
                $overview = $overviewArr[0] ?? null;
                $header = @imap_headerinfo($connection, $msgno);

                $subject = $this->decodeMimeStr($overview->subject ?? $header->subject ?? '');

                $date = isset($overview->date)
                    ? date('Y-m-d H:i:s', strtotime($overview->date))
                    : null;

                $from = array_map(fn($item) => $this->makeAddress($item), $header->from ?? []);

                $to = array_map(fn($item) => $this->makeAddress($item), $header->to ?? []);

                $cc = array_map(fn($item) => $this->makeAddress($item), $header->cc ?? []);

                $bcc = array_map(fn($item) => $this->makeAddress($item), $header->bcc ?? []);

                $replayTo = array_map(fn($item) => $this->makeAddress($item), $header->reply_to ?? []);

                $sender = array_map(fn($item) => $this->makeAddress($item), $header->sender ?? []);

                $flags = $this->parseFlags($overview);


                $structure = imap_fetchstructure($connection, $msgno);
                $resultStructure = $this->parseStructure($msgno, $structure);

                $body = new EmailBody(
                    plain: $resultStructure['plain'] ?? '',
                    html: $resultStructure['html'] ?? '',
                );

                $attachments = [];
                if (!empty($resultStructure['attachments'])) {
                    $attachments = array_map(fn($item) => new Attachment(
                        filename: $item->filename,
                        mime: $item->mime,
                        size: $item->size,
                        content_base64: $item->content_base64 ?? null,
                    ), $resultStructure['attachments'] ?? []);
                }

                $messages[] = new Email(
                    uid: $uid,
                    msgNo: $msgno,
                    subject: $subject,
                    date: $date,
                    from: $from,
                    to: $to,
                    cc: $cc,
                    bcc: $bcc,
                    replayTo: $replayTo,
                    sender: $sender,
                    flags: $flags,
                    body: $body,
                    attachments: $attachments,
                    hasAttachments: empty($attachments)
                );

            }

            return $messages;
        } catch (\Throwable $e) {
            throw new ImapException("Retrieving emails failed: {$e->getMessage()}", $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * Create an Address object from IMAP header parameters.
     * @param object{personal:string|null,mailbox:string,host:string} $params
     * @return Address|null
     */
    public function makeAddress($params): Address|null
    {
        $email = ($params->mailbox ?? '') . '@' . ($params->host ?? '');
        $name = $this->decodeMimeStr(isset($params->personal) ? $params->personal : '');

        return new Address($email, $name);
    }

    /**
     * Decode a MIME encoded string.
     * @param string $string
     * @param string $charset
     * @return string
     */
    private function decodeMimeStr(string $string, string $charset = 'UTF-8'): string
    {
        $elements = imap_mime_header_decode($string);
        $out = '';
        foreach ($elements as $el) {
            $text = $el->text;
            $fromCharset = $el->charset;
            $out .= ($fromCharset && strtoupper($fromCharset) !== 'DEFAULT')
                ? mb_convert_encoding($text, $charset, $fromCharset)
                : $text;
        }
        return $out;
    }

    /**
     * parse email flags from overview object
     * @param mixed $overview
     * @return string[]
     */
    private function parseFlags($overview): array
    {
        $flags = [];
        if (!$overview)
            return $flags;

        if (!empty($overview->seen))
            $flags[] = 'SEEN';
        if (!empty($overview->answered))
            $flags[] = 'ANSWERED';
        if (!empty($overview->flagged))
            $flags[] = 'FLAGGED';
        if (!empty($overview->deleted))
            $flags[] = 'DELETED';
        if (!empty($overview->draft))
            $flags[] = 'DRAFT';
        if (!empty($overview->recent))
            $flags[] = 'RECENT';

        return $flags;
    }

    /**
     * 
     * @param int $msgno
     * @param mixed $structure
     * @param string $partPrefix
     * @return array[]|array{attachments: array, html: string, text: string}
     */
    public function parseStructure(int $msgno, $structure, string $partPrefix = '')
    {

        $result = ['plain' => '', 'html' => '', 'attachments' => []];

        if (!$structure) {
            return $result;
        }

        if (isset($structure->parts) && count($structure->parts) > 0) {
            foreach ($structure->parts as $index => $subStruct) {
                $partNum = $partPrefix === '' ? (string) ($index + 1) : $partPrefix . '.' . ($index + 1);

                if (isset($subStruct->parts)) {
                    $subParsed = $this->parseStructure($msgno, $subStruct, $partNum);
                    $result['plain'] .= $subParsed['plain'];
                    $result['html'] .= $subParsed['html'];
                    $result['attachments'] = array_merge($result['attachments'], $subParsed['attachments']);
                    continue;
                }

                $isText = ($subStruct->type === 0);
                $subtype = strtoupper($subStruct->subtype ?? '');

                if ($isText && in_array($subtype, ['PLAIN', 'HTML'])) {
                    $raw = imap_fetchbody($this->connection, $msgno, $partNum);
                    $decoded = $this->decodePart($raw, $subStruct->encoding);

                    $result[strtolower($subtype)] .= $decoded;
                }

                $params = $this->getParams($subStruct);
                $filename = $params['filename'] ?? $params['name'] ?? null;

                if ($filename) {
                    $raw = imap_fetchbody($this->connection, $msgno, $partNum);
                    $decodedData = $this->decodePart($raw, $subStruct->encoding);

                    $attachment = (object) [
                        'filename' => $this->decodeMimeStr($filename),
                        'mime' => $this->getMimeFromType($subStruct),
                        'size' => strlen($decodedData),
                    ];


                    $result['attachments'][] = $attachment;
                }
            }
        }

        return $result;
    }

    private function decodePart(string $data, int $encoding): string
    {
        return match ($encoding) {
            3 => base64_decode($data),
            4 => quoted_printable_decode($data),
            default => $data,
        };
    }

    private function getMimeFromType($part): string
    {
        $map = [
            0 => 'text',
            1 => 'multipart',
            2 => 'message',
            3 => 'application',
            4 => 'audio',
            5 => 'image',
            6 => 'video',
            7 => 'other',
        ];

        $primary = $map[$part->type] ?? 'application';
        $sub = strtolower($part->subtype ?? 'octet-stream');

        return "{$primary}/{$sub}";
    }

    private function getParams($part): array
    {
        $params = [];

        if (!empty($part->parameters)) {
            foreach ($part->parameters as $p) {
                $params[strtolower($p->attribute)] = $p->value;
            }
        }

        if (!empty($part->dparameters)) {
            foreach ($part->dparameters as $p) {
                $params[strtolower($p->attribute)] = $p->value;
            }
        }

        return $params;
    }
}