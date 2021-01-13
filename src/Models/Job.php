<?php

namespace AmiPraha\LaravelDbQueueMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class Job extends Model
{

	protected $casts = [
		'payload' => 'array',
	];

	protected $dates = [
		'reserved_at',
		'available_at',
		'created_at',
	];

	public $timestamps = false;

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$this->setTable(config('laravel-db-queue-monitor.jobs_table'));
	}

}
