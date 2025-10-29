<?php

namespace App\Services;

// Override imap_* functions for tests
function imap_open($mailbox, $username, $password) {
    return 'imap_connection_resource';
}

function imap_close($connection) {
    return true;
}

function imap_search($connection, $criteria, $options = null) {
    return [101, 102];
}

function imap_msgno($connection, $uid) {
    return $uid; // echo back UID
}

function imap_headerinfo($connection, $msgno) {
    return (object) [
        'from' => [(object)['mailbox' => 'john', 'host' => 'example.com']],
        'to' => [
            (object)['mailbox' => 'mary', 'host' => 'example.com'],
            (object)['mailbox' => 'peter', 'host' => 'example.com'],
        ],
        'subject' => 'Test Subject',
        'date' => '01-Oct-2025 10:00:00 +0000',
    ];
}

function imap_fetchstructure($connection, $msgno) {
    return (object)[
        'parts' => [(object)['subtype' => 'PLAIN']]
    ];
}

function imap_fetchbody($connection, $msgno, $partNumber) {
    return "Hello this is plain text";
}

function imap_qprint($text) {
    return $text;
}
