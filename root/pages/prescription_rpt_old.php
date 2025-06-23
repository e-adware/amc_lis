<?php
include("../../includes/connection.php");
//require('../../includes/global.function.php');
$uhid=$_GET['uhid'];
$opd_id=$_GET['opdid'];
$user=$_GET["user"];

$dt_tm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt_tm[type]' "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$pat_other_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));

$company_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_master` limit 0,1 "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$con_id=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$con=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_id[consultantdoctorid]' "));  

$adv_paid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));

$dpt_name=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `department` WHERE `dept_id`='$con[dept_id]' "));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}
$regd_no=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `qualification`,`regd_no` FROM `employee` WHERE `emp_id` IN (SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_id[consultantdoctorid]') "));

$doc_qua=mysqli_fetch_array(mysqli_query($link," SELECT `qualification` FROM `employee` WHERE `emp_id`='$con[emp_id]' "));

$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));

$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$add_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$add_info[district]' "));

$address="";
//~ if($pat_info["gd_name"])
//~ {
	//~ $address="C/O: ".$pat_info["gd_name"].", ";
//~ }
if($add_info["city"])
{
	$address.=" Town/Vill- ".$add_info["city"].", ";
}
if($pat_info["address"])
{
	$address.=$pat_info["address"].", ";
}
if($add_info["police"])
{
	$address.=" &nbsp; P.S- ".$add_info["police"].", ";
}
//~ if($dist_info["name"])
//~ {
	//~ $address.=" &nbsp; District- ".$dist_info["name"].", ";
//~ }
//~ if($st_info["name"])
//~ {
	//~ $address.=" &nbsp; State- ".$st_info["name"].", ";
//~ }
if($add_info["pin"])
{
	$address.=" &nbsp; PIN-".$add_info["pin"];
}
 
function convert_date($date)
{
	if($date)
	{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-Y', $timestamp);
	return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
// Age Calculator
function age_calculator($dob)
{
	$from = new DateTime($dob);
	$to   = new DateTime('today');
	$year=$from->diff($to)->y;
	$month=$from->diff($to)->m;
	if($year==0)
	{
		//$month=$from->diff($to)->m;
		if($month==0)
		{
			$day=$from->diff($to)->d;
			return $day." Days";
		}else
		{
			return $month." Months";
		}
	}else
	{
		return $year.".".$month." Years";
	}
}
$visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `visit_fee` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($visit_fee["visit_fee"]==0)
{
	$visit_fee=0;
	$visit_type="Free";
}else
{
	$visit_fee=$visit_fee["visit_fee"];
	$visit_fee_num_qry=mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `visit_fee`>0 order by `slno` ASC ");
	$n=1;
	while($visit_fee=mysqli_fetch_array($visit_fee_num_qry))
	{
		if($visit_fee["opd_id"]==$opd_id)
		{
			$visit_fee_num=$n;
			break;
		}else
		{
			$n++;
		}
	}
	function numToOrdinalWord($num)
	{
		$first_word = array('eth','First','Second','Third','Fouth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Elevents','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
		$second_word =array('','','Twenty','Thirthy','Forty','Fifty');

		if($num <= 20)
			return $first_word[$num];

		$first_num = substr($num,-1,1);
		$second_num = substr($num,-2,1);

		return $string = str_replace('y-eth','ieth',$second_word[$second_num].'-'.$first_word[$first_num]);
	}
	//echo $visit_fee_num;
	$visit_type=numToOrdinalWord($visit_fee_num)." visit";
}
$dept_name=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$con[dept_id]'"));
$vit=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
$nurse=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$vit[user]' "));
$page_name="<center style='margin-right:40px;'><b>OP CASE SHEET</b></center>";
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
<body style="width:100%" onafterprint="window.close();" onkeypress="close_window(event)">
<div class="container-fluid">
	<!--<div class="row" style="text-align:right;" >
		<?php //include('page_header.php'); ?>
		<b style="font-size:22px;">OPD CASE SHEET</b><br>
		<b>Consultant : <?php echo $con["Name"].",&nbsp;&nbsp;".$regd_no["qualification"]; ?></b><br>
		<b>Regd No : <?php echo $regd_no['regd_no']; ?></b>
	</div>-->
	<div>
		<?php //include('patient_header.php'); ?>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<hr>
		<table class="table table-no-top-border">
			<tr>
				<th>UHID</th>
				<!--<td>: <?php echo $pat_info["uhid"]; ?></td>-->
				<th style="font-size: 15px;">: <?php echo $uhid; ?></th>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<th style="font-size: 15px;">: <?php echo $opd_id; ?></th>
				<!--<th>Bill No</th>
				<td>: <?php echo $adv_paid["bill_no"]; ?></td>-->
				<th>Date Time</th>
				<td>: <?php echo convert_date($dt_tm["date"]); ?> <?php echo convert_time($dt_tm["time"]); ?></td>
			</tr>
			<tr>
				<th>Name</th>
				<td>: <?php echo $pat_info["name"]; ?></td>
				<th>Age / Sex</th>
				<td>: <?php echo $age; ?> / <?php echo $pat_info["sex"]; ?></td>
				<th>Mobile</th>
				<td>: <?php echo $pat_info["phone"]; ?></td>
			</tr>
		<?php if($pat_info["gd_name"]){ ?>
			<tr>
				<th>Guardian Name</th>
				<td>: <?php echo $pat_info["gd_name"]; ?></td>
				<th>Relationship</th>
				<td>: <?php echo $pat_other_info["relation"]; ?></td>
			</tr>
		<?php } ?>
			<tr>
				<th>Department</th>
				<td>: <?php echo $dept_name['name']; ?></td>
				<th>Consultant</th>
				<td>: <?php echo $con["Name"].",&nbsp;&nbsp;".$regd_no["qualification"]; ?></td>
				<th>Regd No</th>
				<td>: <?php echo $regd_no['regd_no']; ?>
			</tr>
			<tr>
				<th>Consultant Type</th>
				<td>: <?php echo $visit_type; ?></td>
				<th>Address</th>
				<td colspan="3">: <?php echo $address; ?></td>
			</tr>
			<tr>
				<th>District</th>
				<td>: <?php echo $dist_info["name"]; ?></td>
				<th>State</th>
				<td>: <?php echo $st_info["name"]; ?></td>
				<th>Ref By</th>
				<td>: <?php echo $ref_doc["ref_name"]; ?></td>
			</tr>
			</tr>
		</table>
		<hr/>
		<br/>
	</div>
	<table class="table table-no-top-border" style="border-bottom:2px dashed;">
		<!--<tr>
			<th colspan="5"><u>Nursing Assessment</u> <i style="font-size:10px;">(Done by <?php echo $nurse['name'];?>)</i> :</th>
		</tr>
		<tr>
			<th colspan="5">&nbsp;</th>
		</tr>-->
		<tr>
			<th>BP : <?php echo $vit['systolic']."/".$vit['diastolic']?></th>
			<th>Pulse : <?php echo $vit['pulse'];?></th>
			<th>Height : <?php echo $vit['height'];?> CM</th>
			<th>Weight : <?php echo $vit['weight'];?> KG</th>
			<th>BMI : <?php echo $vit['BMI_1'].".".$vit['BMI_2'];?></th>
		</tr>
	</table>
	<!--<br/><b><u>Treatment / Plan of care</u> :</b>-->
	<b style="clear:both;position:fixed;bottom:10%;left:30px;">Next follow up : </b>
</div>
<span id="user" style="display:none;"><?php echo $user; ?></span>
<script>
	$(document).ready(function(){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			window.print();
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
</style>
