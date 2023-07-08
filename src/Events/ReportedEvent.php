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

class ReportedEvent
{
    public ChannelContract $channel;

    public mixed $result;

    public function __construct(ChannelContract $channel, $result)
    {
        $this->channel = $channel;
        $this->result = $result;
    }
}
