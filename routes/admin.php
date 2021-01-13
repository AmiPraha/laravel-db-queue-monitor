<?php

Route::group(array('prefix' => 'admin', 'middleware' => ['admin'], 'as' => 'admin.'), function() {

	Route::group(array('middleware' => ['auth.admin']), function() {

		Route::namespace('Admin')
			->prefix('laravel-db-queue-monitor')
			->group(function () {
				Route::get('/basic', '\AmiPraha\LaravelDbQueueMonitor\Controllers\LaravelDbQueueMonitorController@basic');
			});

		Route::namespace('Admin')
			->prefix('laravel-db-queue-monitor')
			->group(function () {
				Route::post('/jobs', '\AmiPraha\LaravelDbQueueMonitor\Controllers\LaravelDbQueueMonitorController@jobs');
			});

	});

});