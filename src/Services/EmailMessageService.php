<?php

namespace Effectra\LaravelEmail\Services;

use Carbon\Carbon;
use Effectra\EmailAddressFormatter\Address;
use Effectra\LaravelEmail\DataObjects\Attachment;
use Effectra\LaravelEmail\Services\Imap\AttachmentHandler;
use Illuminate\Support\Facades\Mail;
use Effectra\LaravelEmail\DataObjects\Email;
use Effectra\LaravelEmail\Enums\EmailTypeEnum;
use Effectra\LaravelEmail\Exception\EmailFetchException;
use Effectra\LaravelEmail\Exception\EmailSendException;
use Effectra\LaravelEmail\Mails\EmailMessageMailable;
use Effectra\LaravelEmail\Models\EmailMessage;
use Effectra\LaravelEmail\Services\Imap\MailRetriever;

class EmailMessageService implements \Effectra\LaravelEmail\Contracts\EmailMessageServiceInterface
{
    public function __construct(
        private readonly MailRetriever $mailRetriever,
        private readonly EmailMessage $emailModel
    ) {

    }

    /**
     * @return array{attachments: array, bcc: null, cc: null, replay_to: null, sended_at: null, template_id: null, type: EmailTypeEnum}
     */
    public static function defaultData(): array
    {
        return [
            'attachments' => [],
            'cc' => null,
            'bcc' => null,
            'replay_to' => null,
            'template_id' => null,
            'type' => EmailTypeEnum::INTERNAL,
            'sended_at' => null,
        ];
    }

    /**
     * @return EmailMessage[]
     */
    public function saveEmailsFetchedFromExternalSource(): array
    {
        try {
            $emails = $this->mailRetriever->getMails();

            $savedEmails = [];

            foreach ($emails as $email) {
                $attributes = $this->parseExternalEmailDataToModelAttributes($email);

                $savedEmails[] = $this->emailModel::create($attributes);
            }

            return $savedEmails;

        } catch (\Throwable $e) {
            throw new EmailFetchException(
                "Error fetching emails from external source: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    public function parseExternalEmailDataToModelAttributes(Email $email): array
    {

        /**
         * @param Address[] $addresses
         */
        function formatAddress(array $addresses)
        {
            return implode(', ', array_map(fn(Address $m) => (string) $m, $addresses));
        }

        return [
            'subject' => $email->subject ?? '(No Subject)',
            'body' => $email->body->html ?? $email->body->plain ?? '',
            'attachments' => array_map(fn(Attachment $attachment) => (new AttachmentHandler($attachment))->save() ?? '', $email->attachments),
            'to' => formatAddress($email->to),
            'from' => formatAddress($email->from),
            'cc' => formatAddress($email->cc),
            'bcc' => formatAddress($email->bcc),
            'template_id' => null,
            'type' => EmailTypeEnum::EXTERNAL->value,
            'sended_at' => $email->date
                ? Carbon::parse($email->date)->timezone('UTC')
                : now(),
        ];
    }

    public function send(?Carbon $sendDate = null): bool
    {
        try {
            $model = $this->emailModel;

            // Handle recipients
            $to = array_filter(explode(',', (string) $model->to));
            $cc = array_filter(explode(',', (string) $model->cc));
            $bcc = array_filter(explode(',', (string) $model->bcc));

            $mailable = new EmailMessageMailable($model);

            // Scheduled send — use queue delay
            if ($sendDate instanceof Carbon && $sendDate->isFuture()) {
                Mail::to($to)
                    ->cc($cc)
                    ->bcc($bcc)
                    ->later($sendDate, $mailable);
            } else {
                Mail::to($to)
                    ->cc($cc)
                    ->bcc($bcc)
                    ->send($mailable);
            }

            // Update email status/time
            $model->update([
                'sended_at' => now(),
                'type' => EmailTypeEnum::INTERNAL->value
            ]);

            return true;

        } catch (\Throwable $e) {
            throw new EmailSendException(
                "Failed to send email: " . $e->getMessage(),
                0,
                $e
            );
        }
    }
}
