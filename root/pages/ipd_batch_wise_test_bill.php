<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET["ipdid"]));
$batch_no=mysqli_real_escape_string($link, base64_decode($_GET["batch"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$page_head_name="IPD Test Slip";

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$pat_reg=$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_reg)
{
	$reg_date=$pat_reg["date"];
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
}


$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$pat_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$pat_info[district]' "));

$address="";
//~ if($pat_info["gd_name"])
//~ {
	//~ $address="C/O: ".$pat_info["gd_name"].", ";
//~ }
if($pat_info["city"])
{
	$address.="Town/Vill- ".$pat_info["city"].", ";
}
if($pat_info["address"])
{
	$address.=$pat_info["address"].", ";
}
if($pat_info["police"])
{
	$address.="P.S- ".$pat_info["police"].", ";
}
if($dist_info["name"])
{
	//$address.=" &nbsp; District- ".$dist_info["name"].", ";
}
if($st_info["name"])
{
	//$address.=" &nbsp; State- ".$st_info["name"].", ";
}
//~ if($pat_info["pin"])
//~ {
	//~ $address.="PIN-".$pat_info["pin"];
//~ }

//if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

//if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo $page_head_name; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
<?php
$zz=0;

$str=" SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id'";
if($batch_no>0)
{
	$str.=" AND `batch_no`='$batch_no'";
}
$str.="  ORDER BY `batch_no` ASC";

$test_batch_qry=mysqli_query($link, $str);
$test_batch_num=mysqli_num_rows($test_batch_qry);
while($test_batch=mysqli_fetch_array($test_batch_qry))
{
	$batch=$test_batch["batch_no"];
	
	$test_batch_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' AND `batch_no`='$batch' "));
	
	$inv_pat_test_detail_qry=mysqli_query($link, " SELECT a.`test_rate`,b.`testname` FROM `patient_test_details` a,`testmaster` b  WHERE a.`testid`=b.`testid` and a.`patient_id`='$uhid' and a.`ipd_id`='$opd_id' and a.`batch_no`='$batch' ");
	
	//$ward_info=mysqli_fetch_array(mysqli_query($link, " SELECT `ward_name` FROM `ipd_test_ward_master` WHERE `ward_id`='$test_batch_info[ward_id]' "));
	
	$ward_info=mysqli_fetch_array(mysqli_query($link, " SELECT b.`name` FROM `ipd_pat_bed_details` a,ward_master b WHERE a.`patient_id`='$uhid' and a.ipd_id='$opd_id' and a.ward_id =b.ward_id  "));

	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$test_batch_info[refbydoctorid]' "));
	if(!$ref_doc)
	{
		//$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
		
		$ref_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ) "));
		
		$ref_doc["ref_name"]=$ref_doc['Name'];
		
		if(!$ref_doc)
		{
			$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
		}
	}
	
	if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$test_batch_info["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	
	$tot="";
?>
	<div class="container-fluid">
		<div class="">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		
		<table class="table table-no-top-border">
			<tr>
				<th>Unit No.</th>
				<!--<td>: <?php echo $pat_info["uhid"]; ?></td>-->
				<th style="font-size: 15px;">: <?php echo $uhid; ?></th>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<th style="font-size: 15px;">: <?php echo $opd_id; ?></th>
				<th>Test Time</th>
				<td>: <?php echo convert_date($test_batch_info["date"]); ?> <?php echo convert_time($test_batch_info["time"]); ?></td>
			</tr>
	<?php
		if($pat_reg["type"]==8)
		{
			$prefix_dett=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='3' "));
			$delivery_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `baby_uhid`='$uhid' AND `baby_ipd_id`='$opd_id'"));
			
			$ward_info=mysqli_fetch_array(mysqli_query($link, " SELECT b.`name` FROM `ipd_pat_bed_details` a,ward_master b WHERE a.`patient_id`='$delivery_det[patient_id]' and a.ipd_id='$delivery_det[ipd_id]' and a.ward_id =b.ward_id  "));
			
            
	?>
			<tr>
				<th>Mother Unit No.</th>
				<th style="font-size: 15px;">: <?php echo $delivery_det["patient_id"]; ?></th>
				<th>Mother <?php echo $prefix_dett["prefix"]; ?></th>
				<th style="font-size: 15px;">: <?php echo $delivery_det["ipd_id"]; ?></th>
			</tr>
	<?php
		}
	?>
			<tr>
				<th>Name</th>
				<td>: <?php echo $pat_info["name"]; ?></td>
				<th>Age / Sex</th>
				<td>: <?php echo $age; ?> / <?php echo $pat_info["sex"]; ?></td>
				<th>Phone</th>
				<td>: <?php echo $pat_info["phone"]; ?></td>
			</tr>
			<tr>
				<th>Ref By</th>
				<td>: <?php echo $ref_doc["ref_name"]; ?></td>
				<th>Address</th>
				<td colspan="3">: <?php echo $address; ?></td>
			</tr>
			<tr>
				<th>Batch No</th>
				<td>: <?php echo $batch; ?></td>
				<th>Ward</th>
				<td colspan="3">: <?php echo $ward_info["name"]; ?></td>
			</tr>
		</table>
		
		<hr>
		<center><b><?php echo $page_head_name; ?></b> <?php if($usg_num>0){ ?><span style="float: right;">USG No: <?php echo $usg_num; ?></span><?php } ?></center>
		<!--<p>Received with thanks from: <b><?php echo $pat_info["name"]; ?></b></p>
		<p>The sum of Rs: <b><?php echo number_format(($pat_pay_detail["advance"]),2); ?></b></p>
		<p>Details</p>-->
		<table class="table table-condensed">
		<?php
			$j=1;
			while($inv_pat_test_detail=mysqli_fetch_array($inv_pat_test_detail_qry))
			{
				$j = str_pad($j,2,"0",STR_PAD_LEFT);
		?>
			<tr>
				<td style="width:2%;"><?php echo $j; ?>. </td>
				<td><?php echo $inv_pat_test_detail["testname"]; ?></td>
				<td><span class="text-right"><?php echo $inv_pat_test_detail["test_rate"]; ?></span></td>
			</tr>
		<?php 
				$j++;
				$tot+=$inv_pat_test_detail["test_rate"];
			}
		?>
			<tr>
				<td></td>
				<td></td>
				<th>Total Amount: <span class="text-right"><?php echo number_format($tot,2); ?></span></th>
			</tr>
		</table>
		<hr>
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 1%;line-height: 12px;">
			<b>FOR <?php echo $company_info["name"]; ?></b>
			<br>
			(<?php echo $emp["name"]; ?>)
		</div>
	</div>
<?php
	$zz++;
	if($zz<$test_batch_num)
	{
		echo '<div class="pagebreak"></div>';
	}
}
?>
	<span id="user" style="display:none;"><?php echo $user; ?></span>
	<span id="uhid" style="display:none;"><?php echo $uhid; ?></span>
	<span id="opd_id" style="display:none;"><?php echo $opd_id; ?></span>
	<span id="url_redirect" style="display:none;"><?php echo $url_redirect; ?></span>
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
			//window.close();
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
	margin-bottom:1px;
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
	font-size:11px;
}
@page
{
	margin:0.2cm;
}
@media print {
	.pagebreak {
		clear: both;
		page-break-after: always;
	}
}
</style>
