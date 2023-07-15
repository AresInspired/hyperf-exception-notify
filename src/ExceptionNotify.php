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
namespace AresInspired\HyperfExceptionNotify;

use Hyperf\Di\Annotation\Inject;
use Throwable;

class ExceptionNotify
{
    #[Inject]
    protected CollectorManager $collectorManager;

    public function __construct()
    {
        stdoutLogger()->warning(__CLASS__ . ' __construct');
    }

    /**
     * get ExceptionNotify object.
     *
     * @return \AresInspired\HyperfExceptionNotify\ExceptionNotify
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function get(): ExceptionNotify
    {
        return app()->get(self::class);
    }

    public function report(Throwable $throwable)
    {
        $val = $this->collectorManager->toReport($throwable);
        stdoutLogger()->debug($val);
    }
}
