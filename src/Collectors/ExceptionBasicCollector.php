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

use AresInspired\HyperfExceptionNotify\Contracts\ExceptionAwareContract;
use AresInspired\HyperfExceptionNotify\Support\ExceptionContext;
use AresInspired\HyperfExceptionNotify\Traits\ExceptionAwareTrait;

class ExceptionBasicCollector extends Collector implements ExceptionAwareContract
{
    use ExceptionAwareTrait;

    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    public function collect(): array
    {
        return [
            'class' => get_class($this->exception),
            'message' => $this->exception->getmessage(),
            'code' => $this->exception->getCode(),
            'file' => $this->exception->getfile(),
            'line' => $this->exception->getLine(),
            'preview' => ExceptionContext::getformattedcontext($this->exception),
        ];
    }
}
