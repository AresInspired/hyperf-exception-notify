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
namespace AresInspired\HyperfExceptionNotify\Events;

use AresInspired\HyperfExceptionNotify\Contracts\ChannelContract;

class ReportingEvent
{
    public ChannelContract $channel;

    public string $report;

    public function __construct(ChannelContract $channel, string $report)
    {
        $this->channel = $channel;
        $this->report = $report;
    }
}
