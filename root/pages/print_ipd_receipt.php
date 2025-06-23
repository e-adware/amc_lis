<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=base64_decode($_GET['uhid']);
$ipd=$opd_id=base64_decode($_GET['ipd']);

$company_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_name` limit 0,1 "));

$dt_tm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));

$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

//$ref_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `pat_ref_doc` WHERE `patient_id`='$uhid' AND `pin`='$opd_id' ) "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$consult_fee=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT `regd_fee` FROM `ipd_registration_fees`"));

$consult_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' ) "));

$adv_paid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' and `typeofpayment`='A' "));

$bill=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_reg_fees` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `slno` DESC"));

$company_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_master` limit 0,1 "));

$dt_tm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));

$pat_pay_detail=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$adv_paid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `ipd_pat_reg_fees` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));

$inv_pat_test_detail_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `testname`,`rate` FROM `testmaster` WHERE `testid` in ( SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");

$inv_pat_vaccu_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `vaccu_master` WHERE `id` in ( SELECT `vaccu_id` FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Consultation Receipt</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body>
	<div class="container-fluid">
		<div>
			<!--<img src="../../images/header_logo.jpg" style="width: 100%;">
			<h4>
				<?php echo $company_info["name"]; ?><br>
				<small><?php echo $company_info["address"]; ?> ,
				Phone Numbers: <?php echo $company_info["phone"]; ?> </small>             
			</h4>-->
			<?php include('page_header.php'); ?>
		</div>
		<div>
		<?php include('patient_header.php');?>
		<hr>
		</div>
		<center><b>Receipt</b></center>
		<table class="table">
			<!--<?php if($consult_fee["regd_fee"]>0){ ?>
			<tr>
				<td><b>Registration Fee</b> <span class="text-right"><?php echo $consult_fee["regd_fee"]; ?>.00</span></td>
			</tr>
			<?php } ?>-->
			<tr>
				<td><b>Ipd Registration Fee</b> <span class="text-right"><?php echo $consult_fee["regd_fee"]; ?>.00</span></td>
			</tr>
			<!--<tr>
				<td><b>Total</b> <span class="text-right"><?php echo $bill["total"]; ?>.00</span></td>
			</tr>-->
			<?php if($consult_fee["dis_amt"]>0){ ?>
			<tr>
				<td><b>Discount</b> <span class="text-right"><?php echo $bill["discount"]; ?>.00</span></td>
			</tr>
			<?php } ?>
			<tr>
				<td><b>Advance</b> <span class="text-right"><?php echo $bill["paid"]; ?>.00</span></td>
			</tr>
			<tr>
				<td><b>Balance</b> <span class="text-right"><?php echo (($bill["total"])-($bill["paid"])-($bill["discount"])); ?>.00</span></td>
			</tr>
		</table>
		<p>Indian Rupees <?php echo convert_number($bill["paid"]); ?> Only</p>
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 6%;">
			<?php echo $company_info["name"]; ?>
		</div>
	</div>
	<center>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>
</body>
</html>
<script>//window.print()</script>
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
