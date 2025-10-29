<?php

declare(strict_types=1);

namespace Effectra\LaravelEmail\Services\Imap;


trait DebugTrait
{
    public static bool $DEBUG_MODE = false;

    /**
     * Make value safe for debug mode
     * @param $value
     * @return string
     */
    public function makeOnDebug($value)
    {
        if ($value) {
            return '';
        }
        if (!static::$DEBUG_MODE) {
            return $value;
        }
        return substr($value, 0, 30) . '... (hidden in debug mode)';
    }

    /**
     * Enable debug mode
     * @return void
     */
    public static function enableDebugMode()
    {
        static::$DEBUG_MODE = true;
    }
}