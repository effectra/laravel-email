<?php

use Effectra\LaravelEmail\Services\Imap\Connection;
use Effectra\LaravelEmail\Services\Imap\MailRetriever;

beforeEach(function () {
    require_once __DIR__ . '/../Imap/ImapMailMocks.php';
});
test('it retrieves mails as clean objects', function () {
    $imap = new MailRetriever(new Connection('{mockhost}INBOX', 'user@example.com', 'password'));
    $mails = $imap->getMails(2);

    expect($mails)->toBeArray();
    expect($mails)->toHaveCount(2);

    $first = $mails[0];
    expect($first)->toHaveProperties(['id', 'from', 'to', 'subject', 'date', 'body']);
    expect($first->from)->toBe('john@example.com');
    expect($first->to)->toBeArray()->toContain('mary@example.com', 'peter@example.com');
    expect($first->subject)->toBe('Test Subject');
    expect($first->body)->toContain('Hello this is plain text');
});