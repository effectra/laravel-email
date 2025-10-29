<?php

use Effectra\LaravelEmail\Services\EmailMessageService;
use Effectra\LaravelEmail\Enums\EmailTypeEnum;


if (!function_exists('defaultEmailMEssageData')) {
    /**
     * @return array{attachments: array, bcc: null, cc: null, replay_to: null, sended_at: null, template_id: null, type: EmailTypeEnum}
     */
    function defaultEmailMEssageData(): array
    {
        return EmailMessageService::defaultData();
    }
}