<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$pay_id=mysqli_real_escape_string($link, base64_decode($_GET["pid"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));

$dbl=0;
if($_GET["dbl"])
{
	$dbl=$_GET["dbl"];
}

$pat_reg=$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
$bill_name=$pat_typ_text["p_type"];

$pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `pay_id`='$pay_id' AND `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$doc_amount=0;

if($dbl==1)
{
	$doc_pay=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `payment_settlement_doc` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$doc_amount=$doc_pay["tot_amount"];
}

if($v==1)
{
	
}

if($v==2)
{
	//$pat_pay_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
}

if($v==3)
{
	
}

$page_head_name=$bill_name." MONEY RECEIPT";
if($pay_det["refund_amount"]>0)
{
	$page_head_name=$bill_name." REFUND RECEIPT";
}


$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title><?php echo $page_head_name; ?></title>
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()" ondblclick="reload_page()">
	<div class="container-fluid">
		<div class="text-center">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<div>
			<?php include('patient_header.php'); ?>
			<hr/>
		</div>
		<center><b><?php echo $page_head_name; ?></b></center>
<?php
	if(($pay_det["amount"]+$doc_amount)>0)
	{
		$amount=$pay_det["amount"]+$doc_amount;
?>
		<p>
			Received with thanks of amount <?php echo $amount; ?> by <?php echo $pay_det["payment_mode"]; ?>
		</p>
<?php
	}
?>
<?php
	if($pay_det["refund_amount"]>0)
	{
		$amount=$pay_det["refund_amount"];
?>
		<p>
			Refunded of amount <?php echo $amount; ?> by <?php echo $pay_det["payment_mode"]; ?>
		</p>
<?php
	}
?>
		<p>Indian Rupees <?php echo convert_number($amount); ?> Only</p>
		
		<br>
		<br>
		
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 1%;line-height: 12px;">
			<b>FOR <?php echo $company_info["name"]; ?></b>
			<br>
			(<?php echo $emp["name"]; ?>)
			<br>
			Printed on <?php echo date("d-M-Y h:i:s A"); ?>
		</div>
	</div>
	<!--<br>
	<center>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>-->
	<span id="user" style="display:none;"><?php echo $user; ?></span>
	<span id="uhid" style="display:none;"><?php echo $uhid; ?></span>
	<span id="opd_id" style="display:none;"><?php echo $opd_id; ?></span>
	<span id="url_redirect" style="display:none;"><?php echo $url_redirect; ?></span>
	<span id="dbl" style="display:none;"><?php echo $dbl; ?></span>
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
			window.opener.location.href="../processing.php?param=3&uhid="+uhid+"&consult=1&opd="+opd_id;
		}
	}
	
	function reload_page()
	{
		var dbl=$("#dbl").text().trim();
		if(dbl==0)
		{
			dbl=1;
			
			var url = window.location.href;
			
			window.location.href=url+"&dbl="+dbl;
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
	margin-top:0px;
	margin-bottom:10px;
}
hr
{
	margin:0;
	padding:0;
	border-bottom:0;
	border-top: 1px solid #000;
}
*{
	font-size:12px;
}
@page
{
	margin:0.2cm;
}
</style>
