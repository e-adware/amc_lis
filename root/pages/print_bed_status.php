<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$branch_id=$emp_info['branch_id'];

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Bed Status</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<script src="../../js/matrix.js"></script>
</head>

<body onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<?php
			include('page_header.php');
			?>
		</div>
<?php
	//$bq=mysqli_query($link,"SELECT * FROM `bed_master`");
	$bq=mysqli_query($link,"SELECT b.* FROM `bed_master` b, `ward_master` c WHERE b.`ward_id`=c.`ward_id` AND c.`branch_id`='$branch_id' AND b.`share_bed`=0");
?>
		<hr>
		<center><h4>IPD Patient Details</h4></center>
		<center><h6>Print Date Time : <?php echo date("d-F-Y"); ?> <?php echo date("h:i A"); ?></h6></center>
		<table class="table table-condensed table-bordered" style="font-size:13px;">
			<thead>
				<tr>
					<th>Unit No.</th>
					<th>Bill No.</th>
					<th>Ward</th>
					<th>Bed No</th>
					<th>Patient Name</th>
					<th>Doctor</th>
					<th>Age-Sex</th>
					<th>Type</th>
					<th>Admission Date</th>
				</tr>
			</thead>
	<?php
		$pat_bed_det_qry=mysqli_query($link," SELECT a.*, b.`bed_no`, c.`name` AS `ward_name` FROM `ipd_pat_bed_details` a, `bed_master` b, `ward_master` c WHERE a.`bed_id`=b.`bed_id` AND b.`ward_id`=c.`ward_id` AND c.`branch_id`='$branch_id' ORDER BY b.`sequence` ASC ");
		while($pat_bed_det=mysqli_fetch_array($pat_bed_det_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pat_bed_det[patient_id]'"));
			
			$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pat_bed_det[patient_id]' AND `opd_id`='$pat_bed_det[ipd_id]' "));
			
			$doc_info=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN(SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$pat_bed_det[patient_id]' AND `ipd_id`='$pat_bed_det[ipd_id]' ) "));
			
			$centre=mysqli_fetch_array(mysqli_query($link," SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]' "));
			
			//if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".convert_date_g($pat_info["dob"]).")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$pat_reg["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	?>
		<tr>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $pat_bed_det["ipd_id"]; ?></td>
			<td><?php echo $pat_bed_det["ward_name"]; ?></td>
			<td><?php echo $pat_bed_det["bed_no"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $doc_info["Name"]; ?></td>
			<td><?php echo $age."-".$pat_info["sex"]; ?></td>
			<td><?php echo $centre["centrename"]; ?></td>
			<td><?php echo date("d-m-Y",strtotime($pat_reg["date"])); ?></td>
		</tr>
	<?php
		}
	?>
		</table>
	</div>
</body>
</html>
<script>
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
	window.print();
</script>
<style>
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 1px 1px 1px 5px;
}
.table-bordered
{
	border-radius: 0;
}
hr
{
	border-top: 1px solid #000;
	margin: 10px 0;
}
.b_st
{
	display:inline-block;
	padding-left: 5px;
	padding-right: 5px;
	box-shadow:2px 2px #aaaaaa;
	margin-left: 30px;
}
.wards
{
	background:linear-gradient(-90deg, #cccccc, #eeeeee);
}
*
{
	font-size:10px;
}
@page
{
	margin:0.2cm;
}
</style>
