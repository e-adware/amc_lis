<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, $_GET["v"]);

$dbl=0;
if($_GET["dbl"])
{
	$dbl=$_GET["dbl"];
}

$page_head_name="Bill";

$final_pay_qry=mysqli_query($link," SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
$final_pay_num=mysqli_num_rows($final_pay_qry);

if($final_pay_num>1)
{
	$h=1;
	while($final_pay_val=mysqli_fetch_array($final_pay_qry))
	{
		if($h>1)
		{
			mysqli_query($link," DELETE FROM `consult_patient_payment_details` WHERE `slno`='$final_pay_val[slno]' ");
		}
		$h++;
	}
}

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_reg=$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
$visit_fee=$pat_pay_det["visit_fee"];

$doc_visit_fee=0;
if($dbl==1)
{
	$doc_pay=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `payment_settlement_doc` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$doc_visit_fee=$doc_pay["tot_amount"];
}

$appointment_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$consult_name=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment_info[consultantdoctorid]' "));

$doc_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$consult_name[emp_id]' "));


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title><?php echo $page_head_name; ?> | <?php echo $company_info["name"]; ?></title>
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
		<table class="table">
			<?php
			$reg_check=0;
			if($pat_pay_det["regd_fee"]>0){
				$reg_check=1;
			?>
			<tr>
				<th>Registration Fee</th>
				<td><span class="text-right"><?php echo number_format($pat_pay_det["regd_fee"],2); ?></span></td>
			</tr>
			<?php } ?>
			<?php if($pat_pay_det["emergency_fee"]>0){
				$emr_check=1;
			?>
			<tr>
				<th>Emergency Fee</th>
				<td><span class="text-right"><?php echo number_format($pat_pay_det["emergency_fee"],2); ?></span></td>
			</tr>
			<?php } ?>
			<tr>
				<th>Consultation Fee (<?php echo $consult_name["Name"]." ,".$doc_info["qualification"]; ?>)</th>
				<th><span class="text-right"><?php echo number_format($visit_fee+$doc_visit_fee,2); ?></span></td>
			</tr>
		<?php
			$j=1;
			while($j<32)
			{
				echo "<tr><td colspan='2'> &nbsp;</td></tr>";
				$j++;
			}
			echo "<tr><td colspan='2'><hr></td></tr>";
		?>
			<tr>
				<th style="text-align:right;">Total</th>
				<th><span class="text-right"><?php echo number_format($pat_pay_det["tot_amount"]+$doc_visit_fee,2); ?></span></th>
			</tr>
			<?php
				$dis_check=0;
			if($pat_pay_det["dis_amt"]>0){
				$dis_check=1;
				
				$dis_per=round(($pat_pay_det["dis_amt"]/($pat_pay_det["tot_amount"]+$doc_visit_fee))*100,2);
			?>
			<tr>
				<th style="text-align:right;">Discount (<?php echo $dis_per."%"; ?>)</th>
				<th><span class="text-right"><?php echo number_format($pat_pay_det["dis_amt"],2); ?></span></th>
			</tr>
			<?php } ?>
			<tr>
				<th style="text-align:right;">Paid</th>
				<th><span class="text-right"><?php echo number_format($pat_pay_det["advance"]+$doc_visit_fee,2); ?></span></th>
			</tr>
			<tr>
				<th style="text-align:right;">Balance</th>
				<th><span class="text-right"><?php echo number_format($pat_pay_det["balance"],2); ?></span></th>
			</tr>
		</table>
		<p>Indian Rupees <?php echo convert_number($pat_pay_det["advance"]+$doc_visit_fee); ?> Only</p>
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
	border-top: 1px solid #fff;
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
