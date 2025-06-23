<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=$_GET['uhid'];
$ipd=$opd_id=$_GET['ipd'];
$bill_no=$_GET['bill'];
$user=$_GET['user'];

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$consult_fee=mysqli_fetch_array(mysqli_query($link, "SELECT `regd_fee` FROM `ipd_registration_fees`"));

$consult_name=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' ) "));

$adv_paid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' and bill_no='$bill_no' "));

$bill_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' and bill_no='$bill_no'"));

//~ $dt_tm["date"]=$bill_det["date"];
//~ $dt_tm["time"]=$bill_det["time"];

$paid_amount=$bill_det["amount"];
$refunded_amount=$bill_det["refund"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>IPD Payment Receipt</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body  onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<?php include('patient_header.php'); ?>
		<hr/>
		<center><b>Receipt</b></center>
		
		<br/>
			<p>
			<?php
				if($bill_det["pay_type"]=='Final' && $refunded_amount==0)
				{
					$amount_str=$bill_det["amount"];
			?>
					<b>Received an amount of &#X20b9;<?php echo $bill_det["amount"];?> against Final Bill settlement for In Patient ID <?php echo $ipd;?> </b>
			<?php
				}else if($bill_det["pay_type"]=='Final' && $refunded_amount>0)
				{
					$amount_str=$bill_det["refund"];
			?>
					<b>Refunded an amount of &#X20b9;<?php echo $bill_det["refund"];?> against Final Bill settlement for In Patient ID <?php echo $ipd;?> </b>
			<?php
				}
				else if($bill_det["pay_type"]=='Advance')
				{
					$amount_str=$bill_det["amount"];
			?>
					<b>Received an amount of &#X20b9;<?php echo $bill_det["amount"];?> against Advance Payment for In Patient ID <?php echo $ipd;?> </b>
				<?php	
				}
				else if($bill_det["pay_type"]=='Balance')
				{
					$amount_str=$bill_det["amount"];
			?>
					<b>Received an amount of &#X20b9;<?php echo $bill_det["amount"];?> against Balance Payment for In Patient ID <?php echo $ipd;?> </b>
				<?php	
				}
			?>
			</p>
		<br/>
		<p>Indian Rupees: <b><?php echo convert_number($amount_str); ?> Only </b></p>
		<div class="span7"></div>
		<div class="span5 text-right" style="margin-top: 2%;line-height: 12px;">
			<b>FOR <?php echo $company_info["name"]; ?></b>
			<br>
			<br>
			(<?php echo $emp["name"]; ?>)
		</div>
	</div>
	<!--<br>
	<br>
	<center>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>-->
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
	
	window.print();
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
*
{
	font-size:13px;
}
</style>

