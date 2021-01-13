<?php

namespace AmiPraha\LaravelDbQueueMonitor\Providers;

use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use AmiPraha\LaravelDbQueueMonitor\LaravelDbQueueMonitorHandler;

class LaravelDbQueueMonitorProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			dirname(__DIR__) . '/../config/laravel-db-queue-monitor.php' =>
				config_path('laravel-db-queue-monitor.php')
		], 'config');

		$this->publishes([
			dirname(__DIR__) . '/../resources/assets/js/admin/components' =>
				resource_path('assets/js/admin/components')
		], 'vue-components');

		$this->loadMigrationsFrom(
			dirname(__DIR__) . '/../migrations'
		);

		$this->loadRoutesFrom(dirname(__DIR__) . '/../routes/admin.php');

		app(QueueManager::class)->before(function (JobProcessing $event) {
			app(LaravelDbQueueMonitorHandler::class)->handleJobProcessing($event);
		});

		app(QueueManager::class)->after(function (JobProcessed $event) {
			app(LaravelDbQueueMonitorHandler::class)->handleJobProcessed($event);
		});

		app(QueueManager::class)->failing(function (JobFailed $event) {
			app(LaravelDbQueueMonitorHandler::class)->handleJobFailed($event);
		});

		app(QueueManager::class)->exceptionOccurred(function (JobExceptionOccurred $event) {
			app(LaravelDbQueueMonitorHandler::class)->handleJobExceptionOccurred($event);
		});
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			dirname(__DIR__) . '/../config/laravel-db-queue-monitor.php', 'laravel-db-queue-monitor'
		);
	}
}
