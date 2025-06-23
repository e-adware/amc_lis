<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$uhid=mysqli_real_escape_string($link, base64_decode($_GET['uhid']));
$opd_id=$ipd=mysqli_real_escape_string($link, base64_decode($_GET['ipd']));
$bill_no=mysqli_real_escape_string($link, base64_decode($_GET['bill']));
$user=mysqli_real_escape_string($link, base64_decode($_GET['user']));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));

$hopital_name=$company_info["name"];

$pat_info=$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

$dt_tm=$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));

$reg_date=$pat_reg["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

$pat_other_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));

$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));

$doc_id=mysqli_fetch_array(mysqli_query($link, " SELECT `attend_doc`, `admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));

$attend_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_id[attend_doc]' "));

$admit_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_id[admit_doc]' "));


$consult_name=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' ) "));

$dist=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `district` WHERE `district_id`='$pat_info_rel[district]' "));
$state=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `state` WHERE `state_id`='$pat_info_rel[state]' "));

$pat_discharge=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));

if($pat_discharge)
{
	$admit_text="Was admitted in: ";
}else
{
	$admit_text="Admitted in: ";
}

$pat_admit=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' ORDER BY `slno` DESC limit 0,1 "));

$ward=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

$bed=mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

$room=mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>GENERAL CONSENT FORM</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body>
	<div class="container-fluid">
		<div class="">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<?php include('patient_header.php'); ?>
		<hr>
		<!--<center><b>IPD Registration Receipt</b></center>table-no-top-border-->
		<br>
		<table class="table table-no-top-border table-bordered" style="border-left: 1px solid #000;padding: 5px;border-radius: 0;">
			<tr>
				<td><b>Attending Doctor:</b> <?php echo $attend_doc["Name"]; ?></td>
				<td><b>Admitted Doctor:</b> <?php echo $admit_doc["Name"]; ?></td>
				<td><b>Referred By:</b> <?php echo $ref_doc["ref_name"]; ?></td>
			</tr>
			<tr>
				<td colspan="3">
					<b><?php echo $admit_text; ?> </b>
					<span><?php echo $ward["name"]; ?></span>, <span>Room No: <?php echo $room["room_no"]; ?></span>, <span>Bed No: <?php echo $bed["bed_no"]; ?></span>
				</td>
			</tr>
			<?php if($pat_info["phone"]){ ?>
			<tr>
				<td colspan="3">
					<b>Phone Number: </b><?php echo $pat_info["phone"]; ?>
				</td>
			</tr>
			<?php } ?>
			<?php if($pat_info["gd_name"]){ ?>
			<tr>
				<td colspan="3">
					<b>Guardian Name: </b><?php echo $pat_info["gd_name"]; ?>
				</td>
			</tr>
			<?php } ?>
			<?php if($pat_info["address"]){ ?>
			<tr>
				<td colspan="3">
					<b>Address: </b><?php echo $pat_info["address"]; ?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="3">
					<b>City/Vill: </b><?php echo $pat_info_rel["city"]; ?>
				</td>
			</tr>
			<?php if($pat_info_rel["police"]){ ?>
			<tr>
				<td colspan="3">
					<b>Police Station: </b><?php echo $pat_info_rel["police"]; ?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="3">
					<b>District: </b><?php echo $dist["name"]; ?>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<b>State: </b><?php echo $state["name"]; ?>
				</td>
			</tr>
			<?php if($pat_info_rel["pin"]){ ?>
			<tr>
				<td colspan="3">
					<b>PIN: </b><?php echo $pat_info_rel["pin"]; ?>
				</td>
			</tr>
			<?php } ?>
		</table>
		<br>
		
		<center><b>GENERAL CONSENT</b></center>
		<br>
		<p style="text-align:justify;">
			
		</p>
		<p style="text-align:justify;">
			(If required) Name of the interpreter __________________________________ Signature of interpreter_________________
		</p>
		<p style="text-align:justify;">
			Name of patient/ legally authorized representative __________________________________________________________<br>
			Signature of patient / legally authorized representative _______________________________________________________
		</p>
		<p>
			Name of the Councillor __________________________________ <span class="text-right">Signature ________________</span>
		</p>
		<p>
			Date _____________ Time _________
		</p>
<!--
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 6%;">
			<?php echo $company_info["name"]; ?>
		</div>
-->
	</div>
<!--
	<center>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>
-->
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
.table-bordered
{
	border: 1px solid #000;
}
.table-bordered th, .table-bordered td
{
	border-left: 1px solid #fff;
}
</style>

