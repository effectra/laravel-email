<?php

use Effectra\LaravelEmail\Models\EmailTemplate;

return [
    'driver'=>[
        'protocol'=>env('EMAIL_DRIVER','imap'), 
        'host'=>env('EMAIL_HOST'), 
        'port'=>env('EMAIL_PORT',993), 
        'username'=>env('EMAIL_USERNAME'), 
        'password'=>env('EMAIL_PASSWORD'), 
    ],
    'models'=>[
        'template' => EmailTemplate::class
    ]
];