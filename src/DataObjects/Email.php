<?php


namespace Effectra\LaravelEmail\DataObjects;

use Effectra\EmailAddressFormatter\Address;

class Email
{

    /**
     * Constructor.
     * @param int $uid
     * @param int $msgNo
     * @param string $subject
     * @param string $date
     * @param array<Address> $from
     * @param array<Address> $to
     * @param array<Address> $cc
     * @param array<Address> $bcc
     * @param array<Address> $replayTo
     * @param array<Address> $sender
     * @param array<string> $flags
     * @param EmailBody $body
     * @param array<Attachment> $attachments
     * @param bool $hasAttachments
     */
    public function __construct(
        public int $uid,
        public int $msgNo,
        public string $subject,
        public string $date,
        public array $from,
        public array $to,
        public array $cc,
        public array $bcc,
        public array $replayTo,
        public array $sender,
        public array $flags,
        public EmailBody $body,
        public array $attachments,
        public bool $hasAttachments
    ) {
        $this->uid = $uid;
        $this->msgNo = $msgNo;
        $this->subject = $subject;
        $this->date = $date;
        $this->from = $from;
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->flags = $flags;
        $this->body = $body;
        $this->attachments = $attachments;
        $this->hasAttachments = $hasAttachments;
    }

    public function toArray(): array
    {
        return [
            'uid' => $this->uid,
            'msgNo' => $this->msgNo,
            'subject' => $this->subject,
            'date' => $this->date,
            'from' => $this->from,
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'flags' => $this->flags,
            'body' => $this->body,
            'attachments' => $this->attachments,
            'hasAttachments' => $this->hasAttachments,
        ];
    }
}