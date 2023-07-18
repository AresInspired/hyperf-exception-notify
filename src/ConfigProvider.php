<?php

/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

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
        ];

        if ((bool) \Hyperf\Support\env('EXCEPTION_NOTIFY_ENABLED_CLI', true) === false) {
            return $config;
        }

        if (class_exists('\Hyperf\Command\Event\FailToHandle')) {
            $config['listeners'] = [
                \AresInspired\HyperfExceptionNotify\Listeners\CommandFailToHandleListener::class,
            ];
        }
        return $config;
    }
}
