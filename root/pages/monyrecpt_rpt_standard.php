<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=$_GET["uhid"];
$opd_id=$_GET["opdid"];
$user=$_GET["user"];

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

//$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}


$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$adv_paid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));

//$inv_pat_test_detail_qry=mysqli_query($link, " SELECT `testname`,`rate` FROM `testmaster` WHERE `testid` in ( SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");
$inv_pat_test_detail_qry=mysqli_query($link, " SELECT a.`test_rate`,b.`testname` FROM `patient_test_details` a,`testmaster` b  WHERE a.`testid`=b.`testid` and a.`patient_id`='$uhid' and a.`opd_id`='$opd_id' ");

$inv_pat_vaccu_qry=mysqli_query($link, " SELECT * FROM `vaccu_master` WHERE `id` in ( SELECT `vaccu_id` FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");

$tot="";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Test Receipt</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	
</head>

<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<?php include('patient_header.php'); ?>
		<hr>
		<center><b>Money Receipt</b></center>
		<p>Received with thanks from: <b><?php echo $pat_info["name"]; ?></b></p>
		<p>The sum of Rs: <b><?php echo number_format(($pat_pay_detail["advance"]),2); ?></b></p>
		<p>Details</p>
		<table class="table">
		<?php
			while($inv_pat_test_detail=mysqli_fetch_array($inv_pat_test_detail_qry))
			{
				$tot=$tot+$inv_pat_test_detail["test_rate"];
		?>
			<tr>
				<td><?php echo $inv_pat_test_detail["testname"]; ?></td>
				<td><span class="text-right"><?php echo $inv_pat_test_detail["test_rate"]; ?></span></td>
			</tr>
		<?php } ?>
		<?php
			while($inv_pat_vaccu=mysqli_fetch_array($inv_pat_vaccu_qry))
			{
				$tot=$tot+$inv_pat_vaccu["rate"];
		?>
			<tr>
				<td><?php echo $inv_pat_vaccu["type"]; ?></td>
				<td><span class="text-right"><?php echo $inv_pat_vaccu["rate"]; ?></span></td>
			</tr>
		<?php } ?>
			<tr>
				<td></td>
				<th>Total Amount: <span class="text-right"><?php echo number_format($tot,2); ?></span></th>
			</tr>
		<?php
			if($pat_pay_detail["dis_amt"]>0)
			{
		?>
			<tr>
				<td></td>
				<th>Discount Amount: <span class="text-right"><?php echo number_format($pat_pay_detail["dis_amt"],2); ?></span></th>
			</tr>
		<?php } ?>
			<tr>
				<td></td>
				<th>Paid Amount: <span class="text-right"><?php echo number_format(($pat_pay_detail["advance"]),2); ?></span></th>
			</tr>
			<?php
			if($pat_pay_detail["balance"]>0)
			{
		?>
			<tr>
				<td></td>
				<th>Balance Amount: <span class="text-right"><?php echo number_format($pat_pay_detail["balance"],2); ?></span></th>
			</tr>
		<?php } ?>
		</table>
		<hr>
		<p>Indian Rupees <?php echo convert_number($pat_pay_detail["advance"]); ?> Only</p>
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 6%;line-height: 12px;">
			<b>FOR <?php echo $company_info["name"]; ?></b>
			<br>
			(<?php echo $emp["name"]; ?>)
		</div>
	</div>
	<br>
	<center>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>
	
	<!--<div style="position:absolute;top:1%;right:4%;">
		<svg id="barcode3"></svg>
		<script>
			var val="<?php echo $pat_info["patient_id"]; ?>";
			JsBarcode("#barcode3", val, {
				format:"CODE128",
				displayValue:false,
				fontSize:10,
				width:1,
				height:50,
			});
		</script>
	</div>-->
	
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
</style>
