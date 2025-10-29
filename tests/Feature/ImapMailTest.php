<?php

use Effectra\LaravelEmail\Services\Imap\Connection;
use Effectra\LaravelEmail\Services\Imap\MailRetriever;
beforeEach(function () {
    
});

test('can fetch emails', function ()  {
    $imap = new MailRetriever(new Connection(
        "{imap.hostinger.com':993/imap/ssl}INBOX",
        "test@walaa.host",
        "2aH1+W$>!3=",
    ));

    $mails = $imap->getMails(5, 'ALL');

    dd($mails);
    
    $this->assertNotEmpty($mails);
});