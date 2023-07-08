<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-exception-notify.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace AresInspired\HyperfExceptionNotify\Sanitizers;

class AppendContentSanitizer
{
    public function handle(string $report, \Closure $next, string $content): string
    {
        return $next($report.$content);
    }
}
