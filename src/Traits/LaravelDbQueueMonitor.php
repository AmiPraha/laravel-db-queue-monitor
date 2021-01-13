<?php

namespace AmiPraha\LaravelDbQueueMonitor\Traits;

use Exception;
use AmiPraha\LaravelDbQueueMonitor\Models\Monitor;
use AmiPraha\LaravelDbQueueMonitor\LaravelDbQueueMonitorHandler;

trait LaravelDbQueueMonitor
{
	/**
	 * Update progress
	 * @param  int $progress Progress as integer 0-100
	 * @return void
	 */
	public function queueProgress(int $progress): void
	{
		if ($progress < 0 || $progress > 100) {
			throw new Exception('Progress value must be between 0 and 100');
		}

		if (!$monitor = $this->getQueueMonitor()) {
			return;
		}

		$monitor->update([
			'progress' => $progress,
		]);
	}

	/**
	 * Set Monitor data
	 * @param  array $data Custom data
	 * @return void
	 */
	public function queueData(array $data): void
	{
		if (!$monitor = $this->getQueueMonitor()) {
			return;
		}

		$monitor->update([
			'data' => json_encode($data),
		]);
	}

	/**
	 * Delete Queue Monitor object
	 * @return void
	 */
	protected function deleteQueueMonitor(): void
	{
		if ($monitor = $this->getQueueMonitor()) {
			$monitor->delete();
		}
	}

	/**
	 * Return Queue Monitor Model
	 * @return Monitor|null
	 */
	protected function getQueueMonitor()
	{
		if (!property_exists($this, 'job')) {
			return null;
		}

		if (!$this->job) {
			return null;
		}

		if (!$jobId = LaravelDbQueueMonitorHandler::getJobId($this->job)) {
			return null;
		}

		return Monitor::whereJob($jobId)
			->orderBy('started_at', 'desc')
			->limit(1)
			->first();
	}
}
