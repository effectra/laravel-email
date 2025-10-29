<?php

namespace Effectra\LaravelEmail\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Effectra\LaravelEmail\Models\EmailMessage;

class EmailMessageMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public EmailMessage $email)
    {
        //
    }

    public function build(): static
    {
        $mail = $this->subject($this->email->subject)
            ->html($this->email->body);

        if ($this->email->attachments) {
            $attachments = is_string($this->email->attachments)
                ? json_decode($this->email->attachments, true)
                : $this->email->attachments;

            foreach ($attachments as $attachment) {
                if (isset($attachment['path']) && file_exists($attachment['path'])) {
                    $mail->attach($attachment['path'], [
                        'as' => $attachment['name'] ?? null
                    ]);
                }
            }
        }

        return $mail;
    }
}
