<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));

$branch_id=$pat_reg["branch_id"];

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$pat_reg["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$appoint_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$doctor_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appoint_info[consultantdoctorid]' "));  
if($doctor_info["main_doc_id"]>0)
{
	$doctor_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doctor_info[main_doc_id]' "));
}

$doctor_room=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `opd_doctor_room` WHERE `room_id`='$doctor_info[room_id]' "));  

$dpt_name=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `department` WHERE `dept_id`='$doctor_info[dept_id]' "));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));

$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$pat_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$pat_info[district]' "));

$address="";
//~ if($pat_info["gd_name"])
//~ {
	//~ $address="C/O: ".$pat_info["gd_name"].", ";
//~ }
if($pat_info["city"])
{
	//$address.="Town/Vill- ".$pat_info["city"]."<br>";
	$address.="".$pat_info["city"]."<br>";
}
if($pat_info["address"])
{
	$address.=$pat_info["address"].", ";
}
if($pat_info["police"])
{
	$address.="P.S- ".$pat_info["police"]."<br>";
}
if($dist_info["name"])
{
	//$address.=" &nbsp; District- ".$dist_info["name"]."<br>";
}
if($st_info["name"])
{
	//$address.=" &nbsp; State- ".$st_info["name"]."<br>";
}
if($pat_info["pin"])
{
	$address.="PIN-".$pat_info["pin"];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prescription</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>

</head>
<body style="width:100%" onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
<div class="container-fluid">
	<div class="">
		<div class="">
			<?php include('page_header.php');?>
		</div>
	</div>
	<hr/>
	<div>
		<table class="table table-no-top-border">
			<tr>
				<th>Unit No.</th>
				<td>: <?php echo $uhid; ?></td>
				<th>Patient Name</th>
				<th>: <?php echo $pat_info["name"]; ?></th>
			</tr>
			<tr>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<td>: <?php echo $opd_id; ?></td>
				<th>Admission Date</th>
				<td>: <?php echo date("j M Y", strtotime($pat_reg["date"])); ?></td>
			</tr>
			<tr>
				<th>Phone No</th>
				<td>: <?php echo $pat_info["phone"]; ?></td>
				<th>Gender/Age</th>
				<th>: <?php echo $pat_info["sex"]; ?>/<?php echo $age; ?></th>
			</tr>
			<!--<tr>
				<th>Doctor Name</th>
				<td>: <?php echo $doctor_info["Name"].", ".$doctor_info["qualification"]; ?></td>
				<th>Doc. Room No.</th>
				<td>: <?php echo $doctor_room["room_name"]; ?></td>
			</tr>-->
			<tr>
				<th>Address</th>
				<td colspan="3">: <?php echo $address; ?></td>
				<!--<th>Guardian Name</th>
				<td>: <?php echo $pat_info["gd_name"]; ?></td>
				<th>Relationship</th>
				<td>: <?php echo $pat_other_info["relation"]; ?></td>-->
			</tr>
			
			<tr>
				<th>District</th>
				<td>: <?php echo $dist_info["name"]; ?></td>
				<th>State</th>
				<td>: <?php echo $st_info["name"]; ?></td>
			</tr>
			<!--<tr>
				<th>Ref By</th>
				<td>: <?php echo $ref_doc['ref_name']; ?>
			</tr>-->
		</table>
		<hr/>
	</div>
	<div class="" style="height:820px;line-height: 16px;">
		<div class="" style="margin-left: 0;">
		
		<?php
			
			$right_click_image='<img src="../../images/right.png" class="right_click">';
			
			include("pac_prescription_part.php");
		?>
		
		</div>
	</div>
</div>
<span id="user" style="display:none;"><?php echo $user; ?></span>
<span id="uhid" style="display:none;"><?php echo $uhid; ?></span>
<span id="opd_id" style="display:none;"><?php echo $opd_id; ?></span>
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
	$(document).ready(function(){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			//window.print();
		}
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
</body>
</html>
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
*
{
	font-size:12px;
}
.results
{
	margin-left: 20px;
}
.table-bordered
{
	//border: 1px solid #000 !important;
}
.table-bordered th, .table-bordered td
{
	//border-left: 1px solid #000 !important;
	//border-top: 1px solid #000 !important;
}
.table th, .table td
{
	line-height: 15px;
}
@media print {
	@page {
		margin-top: 0;
		margin-bottom: 0;
	}
}
.span7 {
	width: 580px !important;
}
</style>
