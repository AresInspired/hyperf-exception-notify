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

use AresInspired\HyperfExceptionNotify\Support\JsonFixer;

class FixPrettyJsonSanitizer
{
    protected \AresInspired\HyperfExceptionNotify\Support\JsonFixer $jsonFixer;

    public function __construct(JsonFixer $jsonFixer)
    {
        $this->jsonFixer = $jsonFixer;
    }

    public function handle(string $report, \Closure $next, string $missingValue = '"..."'): string
    {
        try {
            $fixedJson = $this->jsonFixer->silent(false)->missingValue($missingValue)->fix($report);

            return $next(json_encode(
                json_decode($fixedJson, true),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            ));
        } catch (\Throwable $throwable) {
            return $next($report);
        }
    }
}
