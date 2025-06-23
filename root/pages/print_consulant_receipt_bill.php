<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$uhid=$_GET["uhid"];
$opd_id=$_GET["opdid"];
$user=$_GET["user"];
$val=$_GET["v"];

if($val==0)
{
	$page_head_name="OPD Consultation Receipt";
}
if($val==1)
{
	$page_head_name="Bill";
}

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

$final_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' ");
$final_pay_num=mysqli_num_rows($final_pay_qry);

if($final_pay_num>1)
{
	$h=1;
	while($final_pay_val=mysqli_fetch_array($final_pay_qry))
	{
		if($h>1)
		{
			mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$final_pay_val[slno]' ");
		}
		$h++;
	}
}

$url_redirect=0;
// Double Entry Check
$pin_double_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_double_check` WHERE `patient_id`='$uhid' AND `old_opd_id`='$opd_id' "));
if($pin_double_check)
{
	$opd_id=$pin_double_check["opd_id"];
}
$check_opd_id_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_id' "));
if($check_opd_id_num>1)
{
	$url_redirect=1;
	
	$check_pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
	$date=$check_pat_reg["date"];
	$time=$check_pat_reg["time"];
	
	//mysqli_query($link, " INSERT INTO `uhid_and_opdid` (`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('0', '0', '$date', '$time', '0', '0', '0', '0', '0') ");
	
	$opd_idds=100;
	
	$date_str=explode("-", $date);
	$dis_year=$date_str[0];
	$dis_month=$date_str[1];
	$dis_year_sm=convert_date_only_sm_year($date);
	
	$c_m_y=$dis_year."-".$dis_month;
	$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
	$opd_id_num=$opd_id_qry["tot"];
	
	$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
	$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
	
	$pat_tot_num=$opd_id_num+$opd_id_cancel_num;
	
	if($pat_tot_num==0)
	{
		$opd_idd=$opd_idds+1;
	}else
	{
		$opd_idd=$opd_idds+$pat_tot_num+1;
	}
	$opd_id_new=$opd_idd."/".$dis_month.$dis_year_sm;
	
	mysqli_query($link, " UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `consult_patient_payment_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `consult_payment_detail` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `appointment_book` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `pat_regd_fee` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `cross_consultation` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `discount_approve` SET `pin`='$opd_id_new' WHERE `patient_id`='$uhid' and `pin`='$opd_id' ");
	mysqli_query($link, " UPDATE `consult_payment_refund_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `invest_payment_refund` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `invest_payment_free` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	
	mysqli_query($link, " INSERT INTO `pin_double_check`(`opd_id`, `patient_id`, `old_opd_id`) VALUES ('$opd_id_new','$uhid','$opd_id') ");
	
	$opd_id=$opd_id_new;
	
	//echo "<script>window.location='cash_memo_lab.php?uhid=".$uhid."&opdid=".$opd_id."&user=".$user."'</script>";
	
}

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$consult_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$appointment_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$consult_name=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment_info[consultantdoctorid]' "));

$doc_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$consult_name[emp_id]' "));

$adv_paid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));

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

<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
	<div class="container-fluid">
		<div class="text-center">
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<center><b style="font-size: 18px;"><?php echo $page_head_name; ?></b></center>
		<hr>
		<div>
			<?php include('patient_header.php'); ?>
			<hr/>
		</div>
		
		<table class="table">
			<?php
			$reg_check=0;
			if($consult_fee["regd_fee"]>0){
				
			?>
			<tr>
				<td colspan="2"><b>Registration Fee</b> <span class="text-right"><?php echo number_format($consult_fee["regd_fee"],2); ?></span></td>
			</tr>
			<?php }
				$emr_check=0;
			?>
			<?php if($consult_fee["emergency_fee"]>0){
				
			?>
			<tr>
				<td colspan="2"><b>Emergency Fee</b> <span class="text-right"><?php echo number_format($consult_fee["emergency_fee"],2); ?></span></td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="2"><b>Consultation Fee</b> (<?php echo $consult_name["Name"]." ,".$doc_info["qualification"]; ?>) <span class="text-right"><?php echo number_format($consult_fee["visit_fee"],2); ?></span></td>
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
				<td style="width:50%;"></td>
				<th>Total <span class="text-right"><?php echo number_format($consult_fee["tot_amount"],2); ?></span></th>
			</tr>
			<tr>
				<td></td>
				<th>Discount <span class="text-right"><?php echo number_format($consult_fee["dis_amt"],2); ?></span></th>
			</tr>
			<tr>
				<td></td>
				<th>Grand Total <span class="text-right"><?php echo number_format($consult_fee["tot_amount"]-$consult_fee["dis_amt"],2); ?></span></th>
			</tr>
		</table>
		<hr>
		<p>Indian Rupees <?php echo convert_number($adv_paid["amount"]); ?> Only</p>
		<br>
		<br>
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
