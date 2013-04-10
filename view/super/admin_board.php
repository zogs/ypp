<div class="container-fluid band-stripped-left">
	<span class="adaptive-title">
		<span class="title-container">
            <h1><?php echo $title;?></h1>                                      
        </span> 
	</span>
</div>

<div class="container">


	<h3>Users</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<th>Total</th>
			<th>Today</th>
			<th>Week</th>
			<th>Month</th>
		</thead>
		<tbody>
			<td><?php echo $totalUsers;?></td>
			<td><?php echo $totalTodayConnexion;?></td>
			<td><?php echo $totalWeekConnexion;?></td>
			<td><?php echo $totalMonthConnexion;?></td>
		</tbody>
	</table>

	<h3>Registration</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<th>Today</th>
			<th>Week</th>
			<th>Month</th>
		</thead>
		<tbody>
			<td><?php echo $totalTodayRegistration;?></td>
			<td><?php echo $totalWeekRegistration;?></td>
			<td><?php echo $totalMonthRegistration;?></td>
		</tbody>
	</table>


	<h3>Protest</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<th>Total</th>
			<th>Online</th>
			<th>Offline</th>
			<th>Moderate</th>
		</thead>
		<tbody>
			<td><?php echo $totalProtests;?></td>
			<td><?php echo $totalOnlineProtests;?></td>
			<td><?php echo $totalOfflineProtests;?></td>
			<td><?php echo $totalModerateProtests;?></td>
		</tbody>
	</table>

	<h3>Comments</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<th>Total</th>
			<th>Online</th>
			<th>Offline</th>
			<th>Moderate</th>
		</thead>
		<tbody>
			<td><?php echo $totalComments;?></td>
			<td><?php echo $totalOnlineComments;?></td>
			<td><?php echo $totalOfflineComments;?></td>
			<td><?php echo $totalModerateComments;?></td>
		</tbody>
	</table>

	<h3>Reports</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<th>Untreated</th>
			<th>Treated</th>
			<th>Total</th>
		</thead>
		<tbody>
			<td><?php echo $totalUntreatedReports;?></td>
			<td><?php echo $totalTreatedReports;?></td>
			<td><?php echo $totalReports;?></td>
		</tbody>
	</table>
</div>