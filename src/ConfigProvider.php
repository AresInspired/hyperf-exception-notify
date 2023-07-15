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

class ConfigProvider
{
    public function __invoke(): array
    {
        if ((bool) \Hyperf\Support\env('EXCEPTION_NOTIFY_ENABLED', true) === false) {
            return [];
        }

        $config = [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'exceptions' => [
                'handler' => [
                    'http' => [
                        \AresInspired\HyperfExceptionNotify\Exceptions\Handler\ExceptionNotifyHandler::class,
                    ],
                ],
            ],
        ];

        if (class_exists('\Hyperf\Command\Event\FailToHandle')) {
            $config['listeners'] = [
                \AresInspired\HyperfExceptionNotify\Listener\CommandFailToHandleListener::class,
            ];
        }

        return $config;
    }
}
