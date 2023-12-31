<?php

/** @noinspection PhpUnused */

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

use AresInspired\HyperfExceptionNotify\Channels\FeiShuChannel;
use AresInspired\HyperfExceptionNotify\Channels\LogAbstractChannel;
use AresInspired\HyperfExceptionNotify\Jobs\ReportExceptionJob;
use AresInspired\HyperfExceptionNotify\Support\Manager;
use AresInspired\HyperfExceptionNotify\Support\RateLimiter;
use Guanguans\Notify\Factory;
use Hyperf\Collection\Arr;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Stringable\Str;
use Throwable;

class ExceptionNotify extends Manager
{
    public function __construct(
        protected CollectorManager $collectorManager,
        protected ConfigInterface $config,
        protected RateLimiter $rateLimiter
    ) {
    }

    public function reportIf($condition, Throwable $throwable): void
    {
        \Hyperf\Collection\value($condition) and $this->report($throwable);
    }

    public function report(Throwable $throwable): void
    {
        try {
            if ($this->shouldntReport($throwable)) {
                return;
            }
            $this->dispatchReportExceptionJob($throwable);
        } catch (Throwable $throwable) {
            stdoutLogger()->error($throwable->getMessage(), ['exception' => $throwable]);
        }
    }

    public function shouldntReport(Throwable $throwable): bool
    {
        if (! $this->config->get('exception_notify.enabled')) {
            return true;
        }

        if (! Str::is($this->config->get('exception_notify.env'), (string) \Hyperf\Support\env('APP_ENV'))) {
            return true;
        }

        foreach ($this->config->get('exception_notify.dont_report') as $type) {
            if ($throwable instanceof $type) {
                return true;
            }
        }

        return ! $this->rateLimiter->attempt(
            md5($throwable->getFile() . $throwable->getLine() . $throwable->getCode() . $throwable->getMessage() . $throwable->getTraceAsString()),
            \Hyperf\Config\config('exception_notify.rate_limiter.max_attempts'),
            static fn (): bool => true,
            \Hyperf\Config\config('exception_notify.rate_limiter.decay_seconds')
        );
    }

    public function shouldReport(Throwable $throwable): bool
    {
        return ! $this->shouldntReport($throwable);
    }

    public function getDefaultDriver(): string
    {
        return \Hyperf\Config\config('exception_notify.default');
    }

    public function onChannel(...$channels): self
    {
        foreach ($channels as $channel) {
            $this->driver($channel);
        }
        return $this;
    }

    protected function dispatchReportExceptionJob(Throwable $throwable): void
    {
        $report = $this->collectorManager->toReport($throwable);

        $drivers = $this->getDrivers() ?: Arr::wrap($this->driver());

        foreach ($drivers as $driver) {
            (new ReportExceptionJob($driver, $report))->handle();
        }
    }

    protected function createLogDriver(): LogAbstractChannel
    {
        return new LogAbstractChannel(
            \Hyperf\Config\config('exception_notify.channels.log.channel'),
            \Hyperf\Config\config('exception_notify.channels.log.level'),
        );
    }

    protected function createFeiShuDriver(): FeiShuChannel
    {
        return new FeiShuChannel(
            Factory::feiShu(array_filter_filled([
                'token' => \Hyperf\Config\config('exception_notify.channels.feiShu.token'),
                'secret' => \Hyperf\Config\config('exception_notify.channels.feiShu.secret'),
            ]))
        );
    }
}
