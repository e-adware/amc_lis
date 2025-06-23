<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$uhid=mysqli_real_escape_string($link, base64_decode($_GET['uhid']));

$user=mysqli_real_escape_string($link, base64_decode($_GET['user']));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));

$hopital_name=$company_info["name"];

$pat_info_rel=$pat_other_info=$pat_info=$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

$pat_marital=mysqli_fetch_array(mysqli_query($link,"SELECT `status_name` FROM `marital_status` WHERE `status_id`='$pat_info[marital_status]'"));

$pat_income=mysqli_fetch_array(mysqli_query($link,"SELECT `income` FROM `income_master` WHERE `income_id`='$pat_info[income_id]' AND `income_id`>0"));

$pat_mreligion=mysqli_fetch_array(mysqli_query($link,"SELECT `religion_name` FROM `religion_master` WHERE `religion_id`='$pat_info[religion_id]' AND `religion_id`>0"));

$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));

$centre_info=mysqli_fetch_array(mysqli_query($link,"SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]'"));

$reg_date=$pat_reg["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

//$pat_other_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));

$source_name=mysqli_fetch_array(mysqli_query($link, " SELECT `source_type` FROM `patient_source_master` WHERE `source_id`='$pat_other_info[source_id]' "));

//$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));

$doc_id=mysqli_fetch_array(mysqli_query($link, " SELECT `attend_doc`, `admit_doc`, `dept_id` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));

$attend_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_id[attend_doc]' "));

$admit_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_id[admit_doc]' "));

$dept_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$doc_id[dept_id]' "));

$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$pat_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$pat_info[district]' "));

$address="";
//~ if($pat_info["gd_name"])
//~ {
	//~ $address="C/O: ".$pat_info["gd_name"].", ";
//~ }
if($pat_info["city"])
{
	$address.=" ".$pat_info["city"]." ";
}
if($pat_info["address"])
{
	//$address.=$pat_info["address"].", ";
}
if($pat_info["police"])
{
	$address.=" &nbsp; P.S- ".$pat_info["police"].", ";
}
//~ if($dist_info["name"])
//~ {
	//~ $address.=" &nbsp; District- ".$dist_info["name"].", ";
//~ }
//~ if($st_info["name"])
//~ {
	//~ $address.=" &nbsp; State- ".$st_info["name"].", ";
//~ }
if($pat_info["pin"])
{
	$address.=" &nbsp; PIN-".$pat_info["pin"];
}

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
	<title>IPD Admission Sheet</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close()" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<?php //include('patient_header.php'); ?>
		<div class="">
			<center>
				<h5>Patient Demographic Details</h5>
				Print Time : <?php echo convert_date_g(date("Y-m-d"))." ".convert_time(date("H:i:m")); ?>
			</center>
			<hr>
			<center>
				<div class="noprint ">
					<input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
					<input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
				</div>
			</center>
			<table class="table table-no-top-border">
				<tr>
					<th>UHID</th>
					<!--<td>: <?php echo $pat_info["uhid"]; ?></td>-->
					<td>: <?php echo $uhid; ?></td>
					<td></td>
					<td></td>
				</tr>
				
				<tr>
					<th>Name</th>
					<td>: <?php echo $pat_info["name"]; ?></td>
					<th>Age</th>
					<td>: <?php echo $age; ?></td>
				</tr>
				<tr>
					<th>Gender</th>
					<td>: <?php echo $pat_info["sex"]; ?></td>
					<th>Phone</th>
					<td>: <?php echo $pat_info["phone"]; ?></td>
				</tr>
				<tr>
					<td><b>Marital Status </b></td>
					<td>: <?php echo $pat_marital["status_name"]; ?></td>
					<th>Religion</th>
					<td>: <?php echo $pat_mreligion["religion_name"]; ?></td>
				</tr>
				<tr>
					<td><b>Occupation </b></td>
					<td>: <?php echo $pat_info["occupation"]; ?></td>
					<th>Education</th>
					<td>: <?php echo $pat_info["education"]; ?></td>
				</tr>
				<tr>
					<td><b>Father's Name </b></td>
					<td>: <?php echo $pat_info["father_name"]; ?></td>
					<th>Mother's Name</th>
					<td>: <?php echo $pat_info["mother_name"]; ?></td>
				</tr>
				<tr>
					<td><b>Guardian's Name </b></td>
					<td>: <?php echo $pat_info["gd_name"]; ?></td>
					<th>Relationship</th>
					<td>: <?php echo $pat_info["relation"]; ?></td>
				</tr>
				<tr>
					<td><b>Guardian's Phone </b></td>
					<td>: <?php echo $pat_info["gd_phone"]; ?></td>
					<td><b>Guardian's Occupation </b></td>
					<td>: <?php echo $pat_info["gurdian_Occupation"]; ?></td>
				</tr>
				<tr>
					<th>Income Groups</th>
					<td colspan="3">: <?php echo $pat_income["income"]; ?></td>
				</tr>
				<tr>
					<th>Address</th>
					<td colspan="3">: <?php echo $address; ?></td>
				</tr>
				<tr>
					<th>District</th>
					<td>: <?php echo $dist_info["name"]; ?></td>
					<th>State</th>
					<td>: <?php echo $st_info["name"]; ?></td>
				</tr>
				
				
				<tr>
					<td colspan="6"><hr></td>
				</tr>
				
			</table>
		</div>
		<div class="span7"></div>
		<div class="span5 text-right" style="margin-top: 6%;line-height: 12px;">
			<b>FOR <?php echo $company_info["name"]; ?></b>
			<br>
			(<?php echo $emp["name"]; ?>)
		</div>
	</div>
	<span id="user" style="display:none;"><?php echo $user; ?></span>
</body>
</html>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
			return false;
		} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent Ctrl+Shift+I        
			return false;
		}
	});
	$(document).on("contextmenu",function(e){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			e.preventDefault();
		}
	});
	
	//window.print();
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
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 5px 0 0 0;
}
.table
{
	margin-bottom:5px;
}
hr
{
	margin:10px 0;
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
.doc_sign
{
	
}
@media print{
	.noprint{
		display:none;
	}
}
@page{
	margin: 0.2cm;
}
</style>

