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
namespace AresInspired\HyperfExceptionNotify\Exceptions\Handler;

use AresInspired\HyperfExceptionNotify\ExceptionNotifyManager;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ExceptionNotifyHandler extends ExceptionHandler
{
    #[Inject]
    protected ExceptionNotifyManager $exceptionNotifyManager;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->exceptionNotifyManager->report($throwable);

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
