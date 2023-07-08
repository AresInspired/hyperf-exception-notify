<?php

namespace AresInspired\HyperfExceptionNotify\Jobs;

use AresInspired\HyperfExceptionNotify\Channels\AbstractChannel;
use AresInspired\HyperfExceptionNotify\Events\ReportedEvent;
use AresInspired\HyperfExceptionNotify\Events\ReportingEvent;
use Hyperf\Pipeline\Pipeline;

class ReportExceptionJob {

	protected AbstractChannel $channel;

	protected string $report;

	protected string $pipedReport = '';

	public function __construct(AbstractChannel $channel, string $report) {
		$this->channel = $channel;
		$this->report = $report;
		$this->pipedReport = $this->pipelineReport($report);
	}

	/**
	 * @return array
	 */
	protected function getChannelPipeline(): array {
		return \Hyperf\Config\config(
			sprintf('exception_notify.channels.%s.sanitizers', $this->channel->getName()),
			[]
		);
	}

	protected function pipelineReport(string $report): string {
		return (new Pipeline(app()))
			->send($report)
			->through($this->getChannelPipeline())
			->then(static fn($report) => $report);
	}

	public function handle() {

		$this->fireReportingEvent($this->pipedReport);
		$result = $this->channel->report($this->pipedReport);
		$this->fireReportedEvent($result);
	}

	protected function fireReportingEvent(string $report) {
		event(new ReportingEvent($this->channel, $report));
	}

	protected function fireReportedEvent($result) {
		event(new ReportedEvent($this->channel, $result));
	}


}