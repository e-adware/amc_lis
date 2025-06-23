<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=base64_decode($_GET["uhid"]);
$opd_id=base64_decode($_GET["opd_id"]);
$user=base64_decode($_GET["EpMl"]);
$refund_request_id=base64_decode($_GET["rrid"]);

$page_head_name="Refund Request";

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }


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
	$address.="Town/Vill- ".$add_info["city"].", ";
}
if($pat_info["address"])
{
	$address.=$pat_info["address"].", ";
}
if($add_info["police"])
{
	$address.="P.S- ".$add_info["police"].", ";
}
if($dist_info["name"])
{
	//$address.=" &nbsp; District- ".$dist_info["name"].", ";
}
if($st_info["name"])
{
	//$address.=" &nbsp; State- ".$st_info["name"].", ";
}
//~ if($add_info["pin"])
//~ {
	//~ $address.="PIN-".$add_info["pin"];
//~ }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$adv_paid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));

$inv_pat_test_detail_qry=mysqli_query($link, " SELECT a.`test_rate`,b.`testname` FROM `patient_test_details` a,`testmaster` b  WHERE a.`testid`=b.`testid` and a.`patient_id`='$uhid' and a.`opd_id`='$opd_id' ");

// Cash Pay
$cash_pay=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='Cash' "));
$cash_amount=$cash_pay["tot"];

// Card Pay
$card_pay=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='Card' "));
$card_amount=$card_pay["tot"];

$inv_pat_vaccu_qry=mysqli_query($link, " SELECT * FROM `vaccu_master` WHERE `id` in ( SELECT `vaccu_id` FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");

// USG Serial
$w=1;
$usg_num=0;
$usg_test_detail_qry=mysqli_query($link, " SELECT a.* FROM `patient_test_details` a,`testmaster` b  WHERE a.`testid`=b.`testid` and b.`type_id`='128' and a.`date`='$dt_tm[date]' ");
while($usg_test_detail=mysqli_fetch_array($usg_test_detail_qry))
{
	if($usg_test_detail["opd_id"]==$opd_id)
	{
		$usg_num=$w;
		break;
	}else
	{
		$w++;
	}
}

$tot="";
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
		<center>
			<h4>Refund Request</h4>
			<div class="noprint ">
				<input type="button" class="btn btn-default" id="Name1" value="Print" onclick="javascript:window.print()">
				<input type="button" class="btn btn-default" id="Name2" value="Exit" onclick="javascript:window.close()"></div>
		</center>
		<hr>
		<table class="table table-no-top-border">
			<tr>
				<th>UHID</th>
				<th style="font-size: 15px;">: <?php echo $uhid; ?></th>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<th style="font-size: 15px;">: <?php echo $opd_id; ?></th>
				<th>Reg Date</th>
				<td>: <?php echo convert_date($dt_tm["date"]); ?> <?php echo convert_time($dt_tm["time"]); ?></td>
			</tr>
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
		<?php
			if($appointment_info)
			{
				if($page_head_name!="Bill")
				{
					$appointment_no = str_pad($appointment_info["appointment_no"],2,"0",STR_PAD_LEFT);
					echo "<tr><th>Appointment No</th><td>: $appointment_no</td></tr>";
				}
			}
		?>
		<?php
			if($bill_no)
			{
				$payment_time=convert_date($bill_det["date"])." ".convert_time($bill_det["time"]);
				echo "<tr><th>Payment Time</th><td>: $payment_time</td></tr>";
			}
		?>
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
				$tot=$tot+$inv_pat_test_detail["test_rate"];
				
				$j = str_pad($j,2,"0",STR_PAD_LEFT);
		?>
			<tr>
				<td style="width:2%;"><?php echo $j; ?></td>
				<td><?php echo $inv_pat_test_detail["testname"]; ?></td>
				<td><span class="text-right"><?php echo $inv_pat_test_detail["test_rate"]; ?></span></td>
			</tr>
		<?php 
				$j++;
			}
		?>
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
				<td></td>
				<th>Total Amount: <span class="text-right"><?php echo number_format($tot,2); ?></span></th>
			</tr>
		<?php
			if($pat_pay_detail["dis_amt"]>0)
			{
		?>
			<tr>
				<td></td>
				<td></td>
				<th>Discount Amount: <span class="text-right"><?php echo number_format($pat_pay_detail["dis_amt"],2); ?></span></th>
			</tr>
		<?php } ?>
		<?php
			if($cash_amount>0)
			{
		?>
			<tr>
				<td></td>
				<td></td>
				<th>Cash Amount: <span class="text-right"><?php echo number_format($cash_amount,2); ?></span></th>
			</tr>
		<?php } ?>
		<?php
			if($card_amount>0)
			{
		?>
			<tr>
				<td></td>
				<td></td>
				<th>Card Amount: <span class="text-right"><?php echo number_format($card_amount,2); ?></span></th>
			</tr>
		<?php } ?>
		<?php
			if($pat_pay_detail["balance"]>0)
			{
		?>
			<tr>
				<td></td>
				<td></td>
				<th>Credit Amount: <span class="text-right"><?php echo number_format($pat_pay_detail["balance"],2); ?></span></th>
			</tr>
		<?php } ?>
		</table>
		<hr>
		<p>Indian Rupees <?php echo convert_number($pat_pay_detail["advance"]); ?> Only</p>
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
