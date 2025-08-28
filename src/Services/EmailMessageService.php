<?php

namespace Effectra\LaravelEmail\Services;

use Carbon\Carbon;
use Effectra\LaravelEmail\Exception\EmailFetchException;
use Effectra\LaravelEmail\Models\EmailMessage;
use Effectra\LaravelStatus\Enums\EmailTypeEnum;

class EmailMessageService
{
    public function __construct(protected EmailMessage $article)
    {

    }

    public static function defaultData(): array
    {
        return [
            'attachments' => [],
            'cc' => null,
            'bb' => null,
            'template_id' => null,
            'type' => EmailTypeEnum::INTERNAL
        ];
    }

    public function send(?Carbon $sendDate = null): bool
    {
        return true;
    }

    /**
     * @return EmailMessage[]
     */
    public function saveEmailsFetchedFromExternalSource(): array
    {
        try {
            $emails = [];
            return array_map(function ($email) {
                return EmailMessage::create($this->parseExternalEmailDataToModelAttributes($email));
            }, $emails);

        } catch (\Throwable $th) {
            throw new EmailFetchException("Error Processing Fetch");
        }
    }

    public function parseExternalEmailDataToModelAttributes(array $email): array
    {
        return [
            'subject',
            'body',
            'attachments',
            'to',
            'from',
            'cc',
            'bb',
            'template_id'=>null,
            'type'=>EmailTypeEnum::EXTERNAL
        ];
    }
}
