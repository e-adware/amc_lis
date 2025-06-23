<?php
session_start();

include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=mysqli_real_escape_string($link, base64_decode($_GET['bid']));
$speciality_id=mysqli_real_escape_string($link, base64_decode($_GET['spid']));

if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

$str="SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`>0 AND `branch_id`='$branch_id'";

if($speciality_id>0)
{
	$str.=" AND `dept_id`='$speciality_id'";
}

$str.=" ORDER BY `consultantdoctorid` ASC";

$qry=mysqli_query($link, $str);

?>
<html>
<head>
	<title>Consultant Doctor List</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<hr>
		<center>
			<h4>Consultant Doctor List</h4>
			<span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
			<!--<div class="noprint">
				<button class="btn btn-print" onclick="javascript:window.print()"><i class="icon-print"></i> Print</button>
				<button class="btn btn-close" onclick="javascript:window.close()"><i class="icon-off"></i> Exit</button>
			</div>-->
		</center>
		<div id="load_data">
			<table class="table table-condensed table-hover table-bordered" style="background-color: white;">
				<thead class="table_header_fix">
					<tr>
						<th rowspan="2">#</th>
						<th rowspan="2">Name</th>
						<th rowspan="2">Contact</th>
						<th rowspan="2">Qualification</th>
						<th rowspan="2">Designation</th>
						<th rowspan="2">Department</th>
						<th colspan="2" style="text-align:center;">Consultation</th>
						<th colspan="2" style="text-align:center;">Registration</th>
					</tr>
					<tr>
						<th>Fee</th>
						<th>Validity(Days)</th>
						<th>Fee</th>
						<th>Validity(Days)</th>
					</tr>
				</thead>
		<?php
				$n=1;
				while($data=mysqli_fetch_array($qry))
				{
					$dept_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$data[dept_id]' "));
		?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $data["Name"]; ?></td>
						<td><?php echo $data["phone"]; ?></td>
						<td><?php echo $data["qualification"]; ?></td>
						<td><?php echo $data["designation"]; ?></td>
						<td><?php echo $dept_info["name"]; ?></td>
						<td><?php echo $data["opd_visit_fee"]; ?></td>
						<td><?php echo $data["opd_visit_validity"]; ?></td>
						<td><?php echo $data["opd_reg_fee"]; ?></td>
						<td><?php echo $data["opd_reg_validity"]; ?></td>
					</tr>
		<?php
					$n++;
				}
		?>
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
		
		window.print();
	});
	
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
<style>
.table
{
	margin-bottom: 0px;
	font-size: 11px;
}
.table-condensed th, .table-condensed td
{
	//padding: 0;
	padding: 0 10px 0 0;
}
.table th, .table td
{
	border-top: 1px solid #000;
}
.table-bordered th, .table-bordered td {
	border-left: 1px solid #000;
}
@page{
	margin: 0.2cm;
}
</style>
