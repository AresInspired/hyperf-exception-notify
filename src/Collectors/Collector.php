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
namespace AresInspired\HyperfExceptionNotify\Collectors;

use AresInspired\HyperfExceptionNotify\Contracts\CollectorContract;
use Hyperf\Stringable\Str;

abstract class Collector implements CollectorContract
{
    public function name(): string
    {
        return ucwords(Str::snake(Str::beforeLast(\Hyperf\Support\class_basename($this), 'Collector'), ' '));
    }
}
