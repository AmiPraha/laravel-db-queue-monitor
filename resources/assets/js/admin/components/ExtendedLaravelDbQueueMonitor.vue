<template>
	<div>
		<div class="col-12 pl-5">
			<table class="table-responsive mb-3">
				<tr>
					<th>Job</th>
					<th class="pr-3">Count</th>
					<th class="pr-3">Sum failed</th>
					<th class="pr-3">Sum attempt</th>
					<th class="pr-3">Sum time elapsed</th>
					<th>Avg time elapsed</th>
					<th>Count waiting jobs</th>
				</tr>
				<tr v-for="item in job_stats">
					<td class="pr-5"><a @click="getJobData(item.job_name)" >{{ item.job_name }}</a></td>
					<td class="pr-5">{{ item.count }}</td>
					<td class="pr-5">{{ item.sum_failed }}</td>
					<td class="pr-5">{{ item.sum_attempt }}</td>
					<td class="pr-5">{{ item.sum_time_elapsed }}</td>
					<td class="pr-5">{{ item.avg_time_elapsed }}</td>
					<td>{{ item.count_waiting_jobs }}</td>
				</tr>
			</table>
			<button class="btn btn-primary" v-on:click="refreshAndClean">Refresh & Clean</button>
		</div>

		<h4 class="pl-5" v-if="detail_job_name">{{ detail_job_name }}</h4>
		<div class="col-12 pl-5 pt-3">
			<table class="table-responsive mb-3">
				<tr>
					<th>ID</th>
					<th class="pr-3">Job ID</th>
					<th class="pr-3">Started At</th>
					<th class="pr-3">Elapsed time</th>
					<th class="pr-3">Attempts</th>
					<th class="pr-3">Failed</th>
					<th class="pr-3">Exception</th>
					<th>Data</th>
				</tr>
				<tr v-for="item in job_details" style="vertical-align: top;">
					<td class="pr-5">{{ item.id }}</td>
					<td class="pr-5">{{ item.job_id}}</td>
					<td class="pr-5">{{ item.started_at }}</td>
					<td class="pr-5">{{ item.time_elapsed }}</td>
					<td class="pr-5">{{ item.attempt }}</td>
					<td class="pr-5">{{ item.failed }}</td>
					<td class="pr-5">
						<div style="width: 250px; height: 100px; overflow-y: auto;">{{ item.exception }}</div>
					</td>
					<td class="pr-5">
						<div style="width: 250px; height: 100px; overflow-y: auto;">{{ item.data }}</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</template>

<script>
	import axios from 'axios'
	export default {
		name: "BasicLaravelDbQueueMonitor",
		created() {
			this.loadBasicData();
			this.interval = setInterval(this.loadBasicData, 10000)
		},
		beforeDestroy() {
			if (this.interval) {
				clearIntervall(this.interval);
				this.interval = undefined
			}
		},
		data: function () {
			return {
				job_stats: [],
				job_details: [],
				interval: undefined,
				detail_job_name: null
			}
		},
		methods: {
			loadBasicData: function () {
				axios.get('/admin/laravel-db-queue-monitor/basic').then(result => {
					this.job_stats = result.data;
			}).catch(function (error) {
					this.job_stats = {"error" : error};
				});
			},
			getJobData: function (job_name) {
				//alert(job_name);
				axios.post('/admin/laravel-db-queue-monitor/jobs', {
					job_name: job_name
				}).then(result => {
					this.job_details = result.data;
					this.detail_job_name = job_name;
				}).catch(function (error) {
					console.log('job_details: ' + error.message);
				});
			},
			cleanJobData: function () {
				this.job_details = [];
			},
			refreshAndClean: function () {
				this.cleanJobData();
				this.loadBasicData();
				this.detail_job_name = null;
			}
		}
	}
</script>

<style scoped>

</style>