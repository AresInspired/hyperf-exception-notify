<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-exception-notify.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace AresInspired\HyperfExceptionNotify\Channels;



class LogAbstractChannel extends AbstractChannel
{
    protected string $level;

    protected string $channel;

    public function __construct(string $channel, string $level)
    {
        $this->channel = $channel;
        $this->level = $level;
    }

    public function report(string $report): void
    {
		stdoutLogger()->{$this->level}($report);
    }
}
