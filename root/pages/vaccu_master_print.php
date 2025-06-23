<?php include('../../includes/connection.php'); ?>
<html>
	<style>
		 input[type="text"]
         {
         border:none;
         }
         @media print{
         .noprint{
         display:none;
         }
         }
		 @media screen
         {
         body {padding: 20px 0;}
         }
	</style>
<head>
	<title>Vaccu Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="text-center">
					<h3>Vaccu Report</h3>
					<div class="noprint "><input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">  <input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()"></div>
				</div>
				<table class="table table-bordered table-condensed table-report">
					<tr>
						<td>Vaccu ID</td>
						<td>Vaccu Name</td>
						<td>Vaccu Rate</td>
					</tr>
					<?php 
						$query=mysqli_query($GLOBALS["___mysqli_ston"],"select * from vaccu_master order by id");
						while($query1=mysqli_fetch_array($query)){
					?>
					<tr>
						<td><?php echo $query1['id']; ?></td>
						<td><?php echo $query1['type']; ?></td>
						<td><?php echo $query1['rate']; ?></td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>

