<?php


require __DIR__ . '/../vendor/autoload.php';


use Effectra\LaravelEmail\Services\Imap\AttachmentHandler;
use Effectra\LaravelEmail\Services\Imap\Connection;
use Effectra\LaravelEmail\Services\Imap\Debug;
use Effectra\LaravelEmail\Services\Imap\MailRetriever;
use Symfony\Component\VarDumper\VarDumper;

try {
    $imap = new Connection(
        "{imap.hostinger.com:993/imap/ssl}INBOX",
        "bmt@walaa.host",
        "Ll^5lKAosfw",
    );

    Debug::enableDebugMode();

    $mailer = new MailRetriever($imap);
    $mails = $mailer->getMails(10, 'ALL');

    foreach ($mails as $mail) {
        
        if ($mail->hasAttachments) {
            $attachments = $mail->attachments;
            foreach ($attachments as $attachment) {
                (new AttachmentHandler($attachment))
                    ->setSavedDir(__DIR__)
                    ->save();
            }
        }
    }

} catch (\Throwable $e) {
    VarDumper::dump($e);
}
