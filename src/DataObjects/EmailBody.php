<?php


namespace Effectra\LaravelEmail\DataObjects;

use Effectra\LaravelEmail\Services\Imap\DebugTrait;

/**
 * @property-read ?string $plain
 * @property-read ?string $html
 */
class EmailBody
{
    use DebugTrait;

    public readonly ?string $plain;
    public readonly ?string $html;

    /**
     * @param ?string $plain
     * @param ?string $html
     */
    public function __construct(?string $plain, ?string $html)
    {
        $this->plain = $this->makeOnDebug($plain);
        $this->html = $this->makeOnDebug($html);
    }

    public function toArray(): array
    {
        return [
            'plain' => $this->plain,
            'html' => $this->html,
        ];
    }
}