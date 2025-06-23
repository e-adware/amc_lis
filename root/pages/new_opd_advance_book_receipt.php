<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$booking_id=mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$page_head_name="Money Receipt";

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$booking_info=$pat_reg=$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `advance_booking` WHERE `patient_id`='$uhid' and `booking_id`='$booking_id' "));

$reg_date=$booking_info["date"];

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$consult_name=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$booking_info[consultantdoctorid]' "));
$doc_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$consult_name[emp_id]' "));

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
	<div class="container-fluid">
		<div class="">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<?php include('patient_header_opd_advance.php'); ?>
		<hr>
		<table class="table table-condensed">
		<?php
			//if($booking_info["visit_fee"]>0)
			//{
		?>
			<tr>
				<th>Consultation Fee (<?php echo $consult_name["Name"]." ,".$doc_info["qualification"]; ?>)</th>
				<th><span class="text-right"><?php echo number_format($booking_info["visit_fee"],2); ?></span></td>
			</tr>
		<?php
			//}
		?>
		<?php
			if($booking_info["regd_fee"]>0)
			{
		?>
			<tr>
				<th>Registration Fee</th>
				<th><span class="text-right"><?php echo number_format($booking_info["regd_fee"],2); ?></span></td>
			</tr>
		<?php
			}
		?>
			<tr>
				<td></td>
				<th>Total Amount: <span class="text-right"><?php echo number_format($booking_info["total"],2); ?></span></th>
			</tr>
		<?php
			//if($booking_info["advance"]>0)
			//{
		?>
			<tr>
				<td></td>
				<th>Paid Amount: <span class="text-right"><?php echo number_format($booking_info["advance"],2); ?></span></th>
			</tr>
		<?php //}
			$balance_amount=$booking_info["total"]-$booking_info["advance"];
		?>
		<?php
			if($balance_amount>0)
			{
		?>
			<tr>
				<td></td>
				<th>Balance Amount: <span class="text-right"><?php echo number_format($balance_amount,2); ?></span></th>
			</tr>
		<?php } ?>
		</table>
		<hr>
		<p>Indian Rupees <?php echo convert_number($booking_info["advance"]); ?> Only</p>
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 1%;line-height: 12px;">
			<b>FOR <?php echo $company_info["name"]; ?></b>
			<br>
			(<?php echo $emp["name"]; ?>)
		</div>
	</div>
	<!--<br>
	<center>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>-->
	
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
	function refreshParent()
	{
		var uhid=$("#uhid").text().trim();
		var opd_id=$("#opd_id").text().trim();
		var url_redirect=$("#url_redirect").text().trim();
		if(url_redirect==1)
		{
			//window.opener.location.reload(true);
			window.opener.location.href="../processing.php?param=3&uhid="+uhid+"&lab=1&opd="+opd_id;
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
</style>
