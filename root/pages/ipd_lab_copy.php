<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=$_GET["uhid"];
$opd_id=$_GET["ipd"];
$batch_no=$_GET["batch"];

$user=$_GET['user'];

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE baby_uhid='$uhid' and baby_ipd_id='$ipd' "));
if($delivery_check)
{
	$uhid=$delivery_check["patient_id"];
	$opd_id=$delivery_check["ipd_id"];
	
}

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' AND batch_no='$batch_no' ) "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
	if(!$ref_doc)
	{
		$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
	}
}

$inv_pat_test_detail_qry=mysqli_query($link, " SELECT a.`test_rate`,b.`testname` FROM `patient_test_details` a,`testmaster` b  WHERE a.`testid`=b.`testid` and a.`patient_id`='$uhid' and a.`ipd_id`='$opd_id' and a.`batch_no`='$batch_no' ");
$inv_pat_test_detail_num=mysqli_num_rows($inv_pat_test_detail_qry);
$test_colspan=(4-($inv_pat_test_detail_num%3))%4;

//$inv_pat_vaccu_qry=mysqli_query($link, " SELECT DISTINCT a.`id`,a.`type` FROM `vaccu_master` a, `test_vaccu` b, `patient_test_details` c WHERE a.`id`=b.`vac_id` AND b.`testid`=c.`testid` AND c.`patient_id`='$uhid' and c.`ipd_id`='$opd_id' and c.`batch_no`='$batch_no' ");
$inv_pat_vaccu_num=mysqli_num_rows($inv_pat_vaccu_qry);
$vaccu_colspan=(4-($inv_pat_vaccu_num%3))%4;

$pat_bed=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));

$bed=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `bed_master` WHERE `bed_id`='$pat_bed[bed_id]' "));

$room=mysqli_fetch_array(mysqli_query($link," SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

$ward=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_bed[ward_id]' "));

$tot="";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>IPD Lab Requisation From</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="text-center">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<table class="table table-no-top-border">
			<tr>
				<th>UHID</th>
				<td>: <?php echo $pat_info["patient_id"]; ?></td>
				<th>IPD ID</th>
				<td>: <?php echo $opd_id; ?></td>
				<th>Reg Date Time</th>
				<td>: <?php echo convert_date($dt_tm["date"]); ?> <?php echo convert_time($dt_tm["time"]); ?></td>
			</tr>
			<tr>
				<th>Name</th>
				<td>: <?php echo $pat_info["name"]; ?></td>
				<th>Age-Sex</th>
				<td>: <?php echo $age; ?> - <?php echo $pat_info["sex"]; ?></td>
				<th>Referred By</th>
				<td>: <?php echo $ref_doc["ref_name"]; ?></td>
			</tr>
			<tr>
				<th>Batch No</th>
				<td>: <?php echo $batch_no; ?></td>
			<?php if($bed["bed_no"]){ ?>
				<th>Bed No</th>
				<td>: <?php echo $bed["bed_no"]."(".$ward["name"]." ".$room["room_no"].")"; ?></td>
			<?php } ?>
			</tr>
		</table>
		<hr>
		<center><b>IPD Test Requisation Form</b></center>
		
		<table class="table table-condensed table-bordered">
		<?php
			$i=$test=1;
			echo "<tr><th colspan='3'>Test Details</th></tr>";
			while($inv_pat_test_detail=mysqli_fetch_array($inv_pat_test_detail_qry))
			{
				$tot=$tot+$inv_pat_test_detail["test_rate"];
				if($test==1)
				{
					echo "<tr>";
				}
				if($i==$inv_pat_test_detail_num)
				{
					$t_span=$test_colspan;
				}else
				{
					$t_span="";
				}
				echo "<td colspan='$t_span'><b>$i.</b> $inv_pat_test_detail[testname]</td>";
				if($test==3)
				{
					echo "</tr>";
					$test=1;
				}else
				{
					$test++;
				}
		?>
			<!--<tr>
				<td style="width:3%;"><?php echo $i; ?></td>
				<td><?php echo $inv_pat_test_detail["testname"]; ?></td>
			</tr>-->
		<?php
				$i++;
			}
		?>
		<?php
			//echo "<tr><th colspan='3'>Vaccu Details</th></tr>";
			$j=$vaccu=1;
			while($inv_pat_vaccu=mysqli_fetch_array($inv_pat_vaccu_qry))
			{
				if($vaccu==1)
				{
					echo "<tr>";
				}
				if($j==$inv_pat_vaccu_num)
				{
					$v_span=$vaccu_colspan;
				}else
				{
					$v_span="";
				}
				echo "<td colspan='$v_span'><b>$j.</b> $inv_pat_vaccu[type]</td>";
				if($vaccu==3)
				{
					echo "</tr>";
					$vaccu=1;
				}else
				{
					$vaccu++;
				}
				$j++;
			}
		?>
		</table>
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 3%;line-height: 12px;">
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

.page_break {page-break-before: always; padding-top: 5px;}
.req_slip{ min-height:520px;}
.f_req_slip{ min-height:670px;}
.rad_req_slip{ min-height:300px;}
*
{
	font-size:13px;
}
@page{
	margin: 0.2cm;
}
</style>
