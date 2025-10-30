<?php

use Effectra\LaravelEmail\Services\EmailMessageService;
use Effectra\LaravelEmail\Enums\EmailTypeEnum;


if (!function_exists('defaultEmailMessageData')) {
    /**
     * get default Email model attributes
     * @return array{attachments: array, bcc: null, cc: null, replay_to: null, sended_at: null, template_id: null, type: EmailTypeEnum}
     */
    function defaultEmailMessageData(): array
    {
        return EmailMessageService::defaultData();
    }
}