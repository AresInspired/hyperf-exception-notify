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
namespace AresInspired\HyperfExceptionNotify\Channels;

use AresInspired\HyperfExceptionNotify\Contracts\ChannelContract;
use Hyperf\Stringable\Str;

abstract class AbstractChannel implements ChannelContract
{
    public function getName(): string
    {
        return Str::lower(Str::beforeLast(\Hyperf\Support\class_basename($this), 'AbstractChannel'));
    }

    abstract public function report(string $report);
}
