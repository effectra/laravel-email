<?php

declare(strict_types=1);

namespace Effectra\LaravelEmail\Contracts;

use Carbon\Carbon;

/**
 * Marker interface for EmailMessageService
 * Extend this interface with method signatures if you want stronger typing.
 */
interface EmailMessageServiceInterface
{
    
    public function saveEmailsFetchedFromExternalSource(): array;
    public function send(?Carbon $sendDate = null): bool;
    
}
