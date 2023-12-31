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
