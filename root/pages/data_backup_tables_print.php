<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$branch_id=$emp_info["branch_id"];

?>
<html>
<head>
	<title>Backup Tables</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Backup Tables</h4>
			<!--<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>-->
			<div class="noprint ">
				<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
			</div>
		</center>
		<div id="load_data">
			<table class="table table-bordered data-table table-condensed">
				<thead style="background: #ddd;">
					<tr style="cursor:pointer;">
						<th>#</th>
						<th>Table Name</th>
						<th>Date Check</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$n=1;
					$table_qry=mysqli_query($link, " SELECT * FROM `backup_tables` ORDER BY `table_name` ");
					while($table=mysqli_fetch_array($table_qry))
					{
						if($table["date_type"]==1)
						{
							$date_check_str="Yes";
						}
						if($table["date_type"]==0)
						{
							$date_check_str="No";
						}
				?>
					<tr class="gradeX">
						<td><?php echo $n; ?></td>
						<td><?php echo $table["table_name"]; ?></td>
						<td>
							<?php echo $date_check_str; ?>
						</td>
					</tr>
				<?php
						$n++;
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
	});
</script>
<style type="text/css" media="print">
  @page { size: landscape; }
  
</style>
<style>
.table
{
	font-size: 11px;
}
@media print
{
	.noprint{
		display:none;
	 }
}
.table
{
	margin-bottom: 0px;
}
.table-condensed th, .table-condensed td
{
	//padding: 0;
	padding: 0 10px 0 0;
}
hr {
    margin: 0;
}
</style>
