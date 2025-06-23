<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=$_GET["uhid"];
$opd_id=$_GET["opdid"];
$user=$_GET["user"];

$date=date('Y-m-d');

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$check_opd_id_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_id' "));
if($check_opd_id_num>1)
{
	$opd_idds=100;
	
	$date_str=explode("-", $date);
	$dis_year=$date_str[0];
	$dis_month=$date_str[1];
	$dis_year_sm=convert_date_only_sm_year($date);
	
	//~ $dis_month=date("m");
	//~ $dis_year=date("Y");
	//~ $dis_year_sm=date("y");
	
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
	mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `invest_payment_detail` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " UPDATE `patient_test_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	$opd_id=$opd_id_new;
}

//$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));

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
	<title>Laboratory Delivery Receipt</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div>
			<?php include('page_header.php'); ?>
		</div>
		<?php include('patient_header.php'); ?>
		<hr>
		<center><b>Laboratory Delivery Receipt</b></center>
		<table class="table table-condensed">
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
			<tr>
				<td><b>Test(s): </b> <span class=""><?php echo $all_test_patho." , ".$all_test_other." , ".$all_vaccu; ?></span></td>
			</tr>
		</table>
		
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 6%;">
			<?php echo $company_info["name"]; ?>
		</div>
	</div>
	<br><br>
	<center>
		<p><?php echo $company_info["name"]; ?> is not responsible for reports not collected within one month from the date of testing.</p><br>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>
	<br>
	<p>- ✂-----------✂-----------✂---------✂----------✂----------✂---------✂---------✂-----------✂----------✂----------✂-------✂----------✂-------✂------✂-------✂------✂-------✂------✂-</p>
	
	<div class="container-fluid">
		<center><h5><u>Laboratory  Copy</u></h5></center>
		<hr/>
		<?php include('patient_header.php'); ?>
		<hr/>
		<table class="table table-bordered table-condensed large_text">
			<tr>
				<th>#</th>
				<th>Testname</th>
			</tr>
			<?php
				$i=1;
				$t_list=mysqli_query($link,"select a.*,b.testname from patient_test_details a,testmaster b where a.patient_id='$uhid' and a.opd_id='$opd_id' and a.testid=b.testid and b.type_id !='132' order by b.category_id,b.type_id ");
				while($tl=mysqli_fetch_array($t_list))
				{
					
					$req=mysqli_query($link,"select * from testmaster where testid='$tl[testid]'");
					while($rq=mysqli_fetch_array($req))
					{
						echo "<tr><td>$i</td><td>$rq[testname]</td></tr>";	
						$i++;
					}
							
				}
				?>
		</table>
	</div>
	<span id="user" style="display:none;"><?php echo $user; ?></span>
</body>
</html>
<script>
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
	padding:5px;
}
*
{
	font-size:13px;
}
</style>
