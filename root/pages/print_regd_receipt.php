<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=$_GET["uhid"];

$company_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_master` limit 0,1 "));

$dt_tm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' order by `slno` DESC limit 0,1 "));

$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Registration Receipt</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body>
	<div class="container-fluid">
		<div class="text-center">
			<h4>
				<?php echo $company_info["name"]; ?><br>
				<small><?php echo $company_info["address"]; ?> ,
				Phone Numbers: <?php echo $company_info["phone"]; ?> </small>             
			</h4>
		</div>
		<hr>
		<table class="table table-no-top-border">
			<tr>
				<th>UHID</th>
				<td>: <?php echo $uhid; ?></td>
				<th>Date</th>
				<td>: <?php echo convert_date($dt_tm["date"]); ?></td>
				<th>Time</th>
				<td>: <?php echo $dt_tm["time"]; ?></td>
			</tr>
			<tr>
				<th>Name</th>
				<td>: <?php echo $pat_info["name"]; ?></td>
				<th>Age/Sex</th>
				<td>: <?php echo $age."/".$pat_info["sex"]; ?></td>
				<th>Ref By</th>
				<td>: <?php echo $ref_doc["ref_name"]; ?></td>
			</tr>
		</table>
		<hr>
		<center><b>Money Receipt</b></center>
		<table class="table">
			<tr>
				<td><b>Registration Fee</b> <span class="text-right"><?php echo $dt_tm["regd_fee"]; ?>.00</span></td>
			</tr>
		</table>
	</div>
</body>
</html>
<script>//window.print()</script>
<style>
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-bottom:5px;
}
hr
{
	margin:0;
	border-bottom:0;
	border-top: 1px solid #000;
}
</style>
