<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=$_GET["uhid"];
$opd_id=$_GET["opdid"];
$user=$_GET["user"];

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

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

//$inv_pat_test_detail_qry=mysqli_query($link, " SELECT a.`testname`,b.`test_rate` FROM `testmaster` a,`patient_test_details` b WHERE a.`testid`=b.`testid` and b.`patient_id`='$uhid' and b.`opd_id`='$opd_id' and b.`test_rate`!=0 ");

$inv_pat_vaccu_qry=mysqli_query($link, " SELECT * FROM `vaccu_master` WHERE `id` in ( SELECT `vaccu_id` FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");


$inv_pat_test_detail_qry_patho=mysqli_query($link, " SELECT `testname`,`rate` FROM `testmaster` WHERE category_id='1' and `testid` in ( SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");
$inv_pat_test_detail_qry_other=mysqli_query($link, " SELECT `testname`,`rate` FROM `testmaster` WHERE category_id>1 and `testid` in ( SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");

$all_test_patho="";
$all_test_other="";
$n=1;
$m=1;
while($inv_pat_test_detail_patho=mysqli_fetch_array($inv_pat_test_detail_qry_patho))
{
	if($n==1)
	{
		$all_test_patho=$inv_pat_test_detail_patho["testname"];
	}else
	{
		$all_test_patho.=" , ".$inv_pat_test_detail_patho["testname"];
	}
	$n++;
}
while($inv_pat_test_detail_other=mysqli_fetch_array($inv_pat_test_detail_qry_other))
{
	if($m==1)
	{
		$all_test_other=$inv_pat_test_detail_other["testname"];
	}else
	{
		$all_test_other.=" , ".$inv_pat_test_detail_other["testname"];
	}
	$m++;
}
$all_vaccu="";
while($inv_pat_vaccu=mysqli_fetch_array($inv_pat_vaccu_qry))
{
	$all_vaccu.=$inv_pat_vaccu["type"].", ";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Lab Receipt,Office Copy,Requisition Slip</title>
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
		<center><b>Laboratory Delivery Receipt</b></center>
		<table class="table">
			<?php
				/*
				$n=1;
				while($inv_pat_test_detail=mysqli_fetch_array($inv_pat_test_detail_qry))
				{
				?>
					<tr>
						<td><?php echo $inv_pat_test_detail["testname"]; ?> <span class="text-right"><?php echo number_format($inv_pat_test_detail["test_rate"],2); ?></span></td>
					</tr>
				<?php
					$n++;
				}
				*/
			?>
			<?php
				/*$n=1;
				while($inv_pat_vaccu=mysqli_fetch_array($inv_pat_vaccu_qry))
				{
				?>
					<tr>
						<td><?php echo $inv_pat_vaccu["type"]; ?> <span class="text-right"><?php echo number_format($inv_pat_vaccu["rate"],2); ?></span></td>
					</tr>
				<?php
					$n++;
				}*/
			?>
			<tr>
				<td><b>Total Amount</b> <span class="text-right"><?php echo number_format($pat_pay_detail["tot_amount"],2); ?></span></td>
			</tr>
			<?php if($pat_pay_detail["dis_amt"]>0){ ?>
			<tr>
				<td><b>Discount Amount</b> <span class="text-right"><?php echo number_format($pat_pay_detail["dis_amt"],2); ?></span></td>
			</tr>
			<?php } ?>
			<tr>
				<td><b>Advance Amount</b> <span class="text-right"><?php echo number_format($pat_pay_detail["advance"],2); ?></span></td>
			</tr>
			<tr>
				<td><b>Balance</b> <span class="text-right"><?php echo number_format(($pat_pay_detail["balance"]),2); ?></span></td>
			</tr>
			<tr>
				<td><b>Test(s): </b> <span class=""><?php echo $all_test_patho." , ".$all_test_other." , ".$all_vaccu; ?></span></td>
			</tr>
		</table>
		<p>Indian Rupees <?php echo convert_number($adv_paid["amount"]); ?> Only</p>
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 6%;line-height: 12px;">
			<b>FOR <?php echo $company_info["name"]; ?></b>
			<br>
			(<?php echo $emp["name"]; ?>)
		</div>
	</div>
	<br><br>
	<center>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>
	<br>
	<p> &nbsp;&nbsp; ✂-----------✂-----------✂---------✂----------✂----------✂---------✂---------✂-----------✂----------✂----------✂-------✂----------✂-------✂------✂</p>
	
	<div class="container-fluid">
		<center><h5><u>Laboratory Office Copy</u></h5></center>
		<hr/>
		<?php include('patient_header.php'); ?>
		<hr/>
		<table class="table">
			<tr>
				<td><b>Total Amount</b> <span class="text-right"><?php echo number_format($pat_pay_detail["tot_amount"],2); ?></span></td>
			</tr>
			<?php if($pat_pay_detail["dis_amt"]>0){ ?>
			<tr>
				<td><b>Discount Amount</b> <span class="text-right"><?php echo number_format($pat_pay_detail["dis_amt"],2); ?></span></td>
			</tr>
			<?php } ?>
			<tr>
				<td><b>Advance Amount</b> <span class="text-right"><?php echo number_format($adv_paid["amount"],2); ?></span></td>
			</tr>
			<tr>
				<td><b>Balance</b> <span class="text-right"><?php echo number_format(($pat_pay_detail["tot_amount"])-($adv_paid["amount"])-($pat_pay_detail["dis_amt"]),2); ?></span></td>
			</tr>
			<tr>
				<td>
					<table class="table">
						<tr>
							<th>Pathoogy Test(s)</th>
							<!--<th>Radiology Test(s):</th>-->
						</tr>
						<td><?php echo $all_test_patho; ?></td>
						<!--<td><?php echo $all_test_other; ?></td>-->
					</table>
				</td>
			</tr>
		</table>
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
.f_req_slip{ min-height:500px;}
.rad_req_slip{ min-height:300px;}
.large_text tbody > tr > td
{
	padding:10px;
}
*
{
	font-size:13px;
}
</style>
