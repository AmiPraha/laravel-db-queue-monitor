# Laravel Queue Monitor

This package offers monitoring like [Laravel Horizon](https://laravel.com/docs/horizon) for database queue.

## Features

* Monitor all jobs like [Laravel Horizon](https://laravel.com/docs/horizon), but not only for redis
* Handles failed jobs with exception
* Support for milliseconds
* Model for Queue Monitorings

## Installation
```
composer require ami-praha/laravel-db-queue-monitor
```

Or add `ami-praha/laravel-db-queue-monitor` to your `composer.json`

```
"ami-praha/laravel-db-queue-monitor": "1.1.0"
```

Run composer update to pull the latest version.

**If you use Laravel 5.5+ you are already done, otherwise continue:**

```php
AmiPraha\LaravelDbQueueMonitor\Providers\LaravelDbQueueMonitorProvider::class,
```

Add Service Provider to your app.php configuration file:

## Configuration

Copy configuration to config folder:

```
$ php artisan vendor:publish --provider="AmiPraha\LaravelDbQueueMonitor\Providers\LaravelDbQueueMonitorProvider"  --tag="config"
```

Copy Vue components to resources folder if you want to modify components (not needed):

```
$ php artisan vendor:publish --provider="AmiPraha\LaravelDbQueueMonitor\Providers\LaravelDbQueueMonitorProvider" --tag="vue-components"
```
and add component to admin/app.js like this:

```
Vue.component('basic-laravel-db-queue-monitor', require('./components/BasicLaravelDbQueueMonitor'));
```

or if you yre going to use component directly from the package, then like this:

```
Vue.component('basic-laravel-db-queue-monitor', require('../../../../vendor/ami-praha/laravel-db-queue-monitor/resources/assets/js/admin/components/BasicLaravelDbQueueMonitor'));
```

Migrate the Queue Monitoring table. The table name itself can be configured in the config file.

```
$ php artisan migrate
```

## Usage

To monitor a job, add the `AmiPraha\LaravelDbQueueMonitor\Traits\LaravelDbQueueMonitor` Trait.

### Update Job Progress / Custom Data

You can update the progress of the current job, like supported by FFMpeg

```php
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AmiPraha\LaravelDbQueueMonitor\Traits\LaravelDbQueueMonitor; // <---

class ExampleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LaravelDbQueueMonitor; // <---

    public function handle()
    {
        // Save progress, if job driver supports
        $ffmpeg->on('progress', function ($percentage) {

            $this->queueProgress($percentage);
        });

        // Save data if finished. Must be type of array
        $this->queueData(['foo' => 'bar']);
    }
}
```

### Retrieve processed Jobs

```php
use AmiPraha\LaravelDbQueueMonitor\Models\Monitor;

$jobs = Monitor::ordered()->get();

foreach ($jobs as $job) {

    // Exact start & finish dates with milliseconds
    $job->startedAtExact();
    $job->finishedAtExact();
}
```

### Model Scopes

```php
// Filter by Status
Monitor::failed();
Monitor::succeeded();

// Filter by Date
Monitor::lastHour();
Monitor::today();

// Chain Scopes
Monitor::today()->failed();

// Get parsed custom Monitor data

$monitor = Monitor::find(1);

$monitor->data; // Raw data string

$monitor->parsed_data; // JSON decoded data, always array
```

## ToDo

* Add Job & Artisan Command for automatic cleanup of old database entries

----

The Idea has been inspirated by gilbitron's [laravel-queue-monitor](https://github.com/gilbitron/laravel-queue-monitor) package.
