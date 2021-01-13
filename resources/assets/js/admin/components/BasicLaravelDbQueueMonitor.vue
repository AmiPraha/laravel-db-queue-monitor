<template>
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
			<tr v-for="item in items">
				<td class="pr-5">{{ item.job_name }}</td>
				<td class="pr-5">{{ item.count }}</td>
				<td class="pr-5">{{ item.sum_failed }}</td>
				<td class="pr-5">{{ item.sum_attempt }}</td>
				<td class="pr-5">{{ item.sum_time_elapsed }}</td>
				<td class="pr-5">{{ item.avg_time_elapsed }}</td>
				<td>{{ item.count_waiting_jobs }}</td>
			</tr>
		</table>
		<button class="btn btn-primary" v-on:click="loadData">Refresh</button>
	</div>
</template>

<script>
	import axios from 'axios'
	export default {
		name: "BasicLaravelDbQueueMonitor",
		created() {
			this.loadData();
			this.interval = setInterval(this.loadData, 30000)
		},
		beforeDestroy() {
			if (this.interval) {
				clearIntervall(this.interval)
				this.interval = undefined
			}
		},
		data: function () {
			return {
				items: [],
				interval: undefined
			}
		},
		methods: {
			loadData: function () {
				axios.get('/admin/laravel-db-queue-monitor/basic').then(result => {
					this.items = result.data;
			}).catch(function (error) {
					this.items = {"error" : error};
				});
			}
		}
	}
</script>

<style scoped>

</style>