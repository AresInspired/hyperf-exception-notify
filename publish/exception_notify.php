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
use AresInspired\HyperfExceptionNotify\Sanitizers\AppendContentSanitizer;
use AresInspired\HyperfExceptionNotify\Sanitizers\FixPrettyJsonSanitizer;
use AresInspired\HyperfExceptionNotify\Sanitizers\LengthLimitSanitizer;

return [
    /*
    |--------------------------------------------------------------------------
    | Enable exception notification report switch.
    |--------------------------------------------------------------------------
    |
    | If set to false, the exception notification report will not be enabled.
    |
    */
    'enabled' => (bool) \Hyperf\Support\env('EXCEPTION_NOTIFY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Enable command exception notification report switch.
    |--------------------------------------------------------------------------
    |
    | If set to false or enabled set to false, the exception notification report will not be enabled.
    |
    */
    'enabled_cli' => (bool) \Hyperf\Support\env('EXCEPTION_NOTIFY_ENABLED_CLI', true),

    /*
    |--------------------------------------------------------------------------
    | A list of the application environments that are reported.
    |--------------------------------------------------------------------------
    |
    | Here you may specify a list of the application environments that should
    | be reported.
    |
    | ```
    | [production, local]
    | ```
    */
    'env' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | A list of the exception types that are not reported.
    |--------------------------------------------------------------------------
    |
    | Here you may specify a list of the exception types that should not be
    | reported.
    |
    | ```
    | [
    |     HttpResponseException::class,
    |     HttpException::class,
    | ]
    | ```
    */
    'dont_report' => [],

    /*
    |--------------------------------------------------------------------------
    | List of collectors.
    |--------------------------------------------------------------------------
    |
    | Responsible for collecting the exception data.
    |
    */
    'collector' => [
        \AresInspired\HyperfExceptionNotify\Collectors\ExceptionTraceCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\ExceptionBasicCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\ChoreCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\ApplicationCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\PhpInfoCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestBasicCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestSessionCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestCookieCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestFileCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestHeaderCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestMiddlewareCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestPostCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestQueryCollector::class,
        \AresInspired\HyperfExceptionNotify\Collectors\RequestServerCollector::class,
    ],

    /*
     |--------------------------------------------------------------------------
     | Exception notification rate limiter.
     |--------------------------------------------------------------------------
     |
     | The exception notification rate limiter is used to prevent sending
     | exception notification to the same channel too frequently.
     |
     */
    'rate_limiter' => [
        'max_attempts' => (int) \Hyperf\Support\env('EXCEPTION_NOTIFY_LIMIT', \Hyperf\Support\env('APP_ENV') === 'prod' ? 1 : 50),
        'decay_seconds' => 300,
    ],

    /*
    |--------------------------------------------------------------------------
    | Report title.
    |--------------------------------------------------------------------------
    |
    | The title of the exception notification report.
    |
    */
    'title' => \Hyperf\Support\env('EXCEPTION_NOTIFY_REPORT_TITLE', sprintf('%s application exception report', \Hyperf\Support\env('APP_ENV'))),

    /*
    |--------------------------------------------------------------------------
    | default channel.
    |--------------------------------------------------------------------------
    |
    | The default channel of the exception notification report.
    |
    */
    'default' => \Hyperf\Support\env('EXCEPTION_NOTIFY_DEFAULT_CHANNEL', 'log'),

    /*
     |--------------------------------------------------------------------------
     | Supported channels.
     |--------------------------------------------------------------------------
     |
     | Here you may specify a list of the supported channels.
     |
     */
    'channels' => [
        // Log
        'log' => [
            'driver' => 'log',
            'channel' => \Hyperf\Support\env('EXCEPTION_NOTIFY_LOG_CHANNEL', 'default'),
            'level' => \Hyperf\Support\env('EXCEPTION_NOTIFY_LOG_LEVEL', 'error'),
            'sanitizers' => [
            ],
        ],

        // 飞书群机器人
        'feiShu' => [
            'driver' => 'feiShu',
            'token' => \Hyperf\Support\env('EXCEPTION_NOTIFY_FEISHU_TOKEN'),
            'secret' => \Hyperf\Support\env('EXCEPTION_NOTIFY_FEISHU_SECRET'),
            'keyword' => \Hyperf\Support\env('EXCEPTION_NOTIFY_FEISHU_KEYWORD'),
            'sanitizers' => [
                sprintf('%s:%s', LengthLimitSanitizer::class, 30720),
                FixPrettyJsonSanitizer::class,
                sprintf('%s:%s', AppendContentSanitizer::class, \Hyperf\Support\env('EXCEPTION_NOTIFY_FEISHU_KEYWORD')),
            ],
        ],
    ],
];
