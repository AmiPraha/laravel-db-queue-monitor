<?php
namespace AmiPraha\LaravelDbQueueMonitor\Controllers;

use App\Http\Controllers\Controller;
use DB;
use AmiPraha\LaravelDbQueueMonitor\Models\Job;
use AmiPraha\LaravelDbQueueMonitor\Models\Monitor;
use Illuminate\Http\Request;
use \Exception;

class LaravelDbQueueMonitorController extends Controller
{
	public function basic() {

		$monitors = DB::select("SELECT name as 'job_name', count(*) as 'count', sum(`failed`) as 'sum_failed', sum(`attempt`) as 'sum_attempt', sum(`time_elapsed`) as 'sum_time_elapsed', avg(`time_elapsed`) as 'avg_time_elapsed' FROM `queue_monitor` group by name");

		if(config('queue.default') === 'redis') {
			$queue_driver = config('queue.default');
			$queue_connection = config('queue.connections.' . $queue_driver . '.connection');
			$queue_name = config('queue.connections.' . $queue_driver . '.queue');
			$waiting_jobs_count = \Redis::connection($queue_connection)->llen('queues:default');
			$waiting_jobs = \Redis::connection($queue_connection)->lrange('queues:' . $queue_name, 0, ($waiting_jobs_count > 0) ? $waiting_jobs_count - 1 : 1);
			foreach($monitors as &$monitor) {
				$cnt = 0;
				foreach($waiting_jobs as $waiting_job) {
					if(json_decode($waiting_job)->displayName === $monitor->job_name) {
						$cnt++;
					}
				}
				$monitor->count_waiting_jobs = $cnt;
			}
		} elseif(config('queue.default') === 'database') {
			//$queue_driver = config('queue.default');
			foreach($monitors as &$monitor) {
				$waiting_jobs = $waiting_jobs = Job::whereNull('reserved_at')->where('payload', 'like', '%' . str_replace('\\', '\\\\\\\\', $monitor->job_name) . '%')->count();
				$monitor->count_waiting_jobs = $waiting_jobs;
			}
		} else {
			throw new Exception('ERROR: LaravelDbQueueMonitor can work only with redis or database queue driver. "' . config('queue.default') . '" is set.');
		}

		return $monitors;
	}

	public function jobs(Request $request) {

		//$jobs = Monitor::whereJobName($request->job_name)->orderBy('id','DESC')->get();
		$jobs = Monitor::where('name', $request->job_name)->orderBy('id','DESC')->get();

		return $jobs;
	}
}