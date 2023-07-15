<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace AresInspired\HyperfExceptionNotify\Sanitizers;

use AresInspired\HyperfExceptionNotify\Support\JsonFixer;
use Closure;
use Throwable;

class FixPrettyJsonSanitizer
{
    protected \AresInspired\HyperfExceptionNotify\Support\JsonFixer $jsonFixer;

    public function __construct(JsonFixer $jsonFixer)
    {
        $this->jsonFixer = $jsonFixer;
    }

    public function handle(string $report, Closure $next, string $missingValue = '"..."'): string
    {
        try {
            $fixedJson = $this->jsonFixer->silent(false)->missingValue($missingValue)->fix($report);

            return $next(json_encode(
                json_decode($fixedJson, true),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            ));
        } catch (Throwable $throwable) {
            return $next($report);
        }
    }
}
