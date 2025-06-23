<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=base64_decode($_GET['uhid']);
$ipd=$opd_id=base64_decode($_GET['ipd']);
$user=base64_decode($_GET['user']);
$company_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_master` limit 0,1 "));
$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$company_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_master` limit 0,1 "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
$u=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$user'"));
$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
//$dis_dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
$dis_dt=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
$pat_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_info` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
$adv_paid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' and `pay_type`='Final' "));
$con_doc=mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `consultantdoctorid` FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `consultantdoctorid` NOT IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'))");
$ref_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
$ipd_serial_val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
$consultants="";
$addr="";
if($addr)
{
	$addr.=", ".$pat_det['add_1'];
}
else
{
	$addr=$pat_det['add_1'];
}
if($addr)
{
	$addr.=", ".$pat_det['add_2'];
}
else
{
	$addr=$pat_det['add_2'];
}
if($addr)
{
	$addr=", ".$pat_det['city'];
}
else
{
	$addr=$pat_det['city'];
}
if($pat_det['state'])
{
	$st=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `state` WHERE `state_id`='$pat_det[state]'"));
	$addr.=", ".$st['name'];
}
if($pat_det['country'])
$addr.=", ".$pat_det['country'];
while($c=mysqli_fetch_array($con_doc))
{
	$consultants.=$c['Name'].", ";
}
function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
function currency($rs)
{
	setlocale(LC_MONETARY, 'en_IN');
				
	$amount = money_format('%!i', $rs);
	return $amount;
}
$row=0;
$grand_tot=0;
ob_start();
include "page_header.php";
$my_head=ob_get_clean();

$self_file = basename($_SERVER['PHP_SELF']); 
$no_of_lines = count(file($self_file));
//echo $no_of_lines;
//echo $lines = count(file($self_file)) - 1;
$lines=15;
?>
<html lang="en">
	<head>
		<title>Discharge Certificate</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<style>
		.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
		{
			padding: 0 0 0 0;
		}
		.table
		{
			margin-bottom:5px;
			font-size:12px;
		}
		hr
		{
			margin:0;
			border-bottom:0;
			border-top: 1px solid #000;
		}

		.page-break {page-break-after: always; padding-top: 5px;}
		.req_slip{ min-height:520px;}
		.f_req_slip{ min-height:670px;}
		.rad_req_slip{ min-height:300px;}
		
	</style>
	</head>
	<body  onafterprint="window.close();" onkeypress="close_window(event)">
		<div class="container-fluid">
			<div class="text-center" style="">
				<?php
				include('page_header.php');
				?>
				<span class="" style="font-size: 16px;font-weight: bold;">DISCHARGE SUMMARY</span>
				<br><br>
			</div>
			<div>
				<table class="table table-condensed table-bordered" style="font-size:12px" id="d">
					<tr>
						<th>UHID</th><td><?php echo $pat['patient_id'];?></td>
						<th>IPD</th><td><?php echo $ipd;?></td>
						<th>Admission Date</th><td><?php echo convert_date_g($dt['date'])." ".convert_time($dt['time']);?></td>
					</tr>
					<tr>
						<th>Name</th><td><?php echo $pat['name'];?></td>
						<th>Age/Sex</th><td><?php echo $pat['age']." ".$pat['age_type']."/".$pat['sex'];?></td>
						<th>Discharge Date</th><td><?php if($dis_dt){ echo convert_date_g($dis_dt['date'])." ".convert_time($dis_dt['time']);}?></td>
					</tr>
				<?php if($ipd_serial_val["ipd_serial"]){ ?>
					<tr>
						<th style="display:none;">IPD Serial</th><td colspan="" style="display:none;"><?php echo $ipd_serial_val["ipd_serial"];?></td>
						<th>Address</th><td colspan="5"><?php echo $addr;?></td>
					</tr>
				<?php }else{ ?>
					<tr>
						<th>Address</th><td colspan="5"><?php echo $addr;?></td>
					</tr>
				<?php } ?>
				</table>
				<table class="table table-no-top-border">
					<tr>
						<th style="width: 15%;">Treating Doctor</th>
						<th>: <?php echo $doc['Name'];?></th>
					</tr>
				
				<!--<b>Attending Doctor : <?php echo $doc['Name'];?></b><br/>-->
				<?php
					$surgeon=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `doctor_service_done` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='817'"));
					if($surgeon)
					{
						$surgeon_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$surgeon[consultantdoctorid]' "));
						//echo "<b>Surgeon : ".$surgeon_doc['Name']."</b><br>";
						echo "<tr><th>Surgeon</th><th>: $surgeon_doc[Name]</th></tr>";
					}
					$anaest=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `doctor_service_done` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='819'"));
					if($anaest)
					{
						$anaest_doc=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$anaest[consultantdoctorid]' "));
						//echo "<b>Anaesthetist : ".$anaest_doc['Name']."</b><br>";
						echo "<tr><th>Anaesthetist</th><th>: $anaest_doc[Name]</th></tr>";
					}
				?>
				</table>
<!--
				<b>Consultants: <?php echo $consultants;?></b><br/><br/>
-->
				<?php
				$o=0;
				$ll=40;
				$brk=40;
				$z=1;
				
				$admit_reason_val=mysqli_fetch_array(mysqli_query($link,"SELECT `admit_reason` FROM `ipd_pat_admit_reason` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
				if($admit_reason_val)
				{
					echo "<p align='justify'><b>Reason for admission</b> : $admit_reason_val[admit_reason]</p>";
					//echo "<p align='justify'><b>Case Summary :</b> ".$hhhh."</p>";
				}
				
				$q=mysqli_query($link,"SELECT * FROM `ipd_pat_complaints` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
				if(mysqli_num_rows($q)>0)
				{
				?>
				<b>Chief complaints</b><br/>
				<table class="table table-condensed" style="font-size:12px;">
				<?php
				$j=1;
				while($rr=mysqli_fetch_array($q))
				{
			?>
				<tr>
					<td><?php echo $j;?></td>
					<td>
						<?php echo $rr['comp_one']; ?> for <?php echo $rr['comp_two']." ".$rr['comp_three']; ?>
					</td>
				</tr>
				<?php
				$j++;
				$ll++;
				if($o==18)
				{
					$lines++;
					$o=0;
				}
				else
				{
					$o++;
				}
				}
				?>
				</table>
				<?php
				}
				$h=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' ORDER BY 'slno' DESC limit 0,1"));
				$arr=explode(" ",$h['history']);
				$hhhh="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$hhhh.=$ss." ";
					//if($ll==500)
					if($lines==30)
					{
						$hhhh.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Past History</b><br/>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				$brk=$ll;
				if($h['history'])
				{
				?>
				<!--<b>Case Summary</b>-->
				<?php
				//echo $ll;
				echo "<p align='justify'><b>Case Summary :</b> ".$hhhh."</p>";
				}
				?>
				<!--<div style="page-break-after:always" align="justify"><?php echo $h['history']; ?></div>-->
				<?php
				$arr=explode(" ",$h['examination']);
				$eee="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$eee.=$ss." ";
					//if($ll==500)
					if($lines==30)
					{
						$eee.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Examination</b><br/>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				$brk=$ll;
				//echo $lines;
				if($h['examination'])
				{
				?>
				<!--<b>Examination</b>-->
				<?php
				//echo $ll;
				echo "<p align='justify'> <b>Examination</b> : ".$eee."</p>";
				}
				
				//////////////
				$sig_inv=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_significant_investigation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
				$arr=explode(" ",$sig_inv['significant_finding']);
				$hhhh="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$hhhh.=$ss." ";
					//if($ll==500)
					if($lines==30)
					{
						$hhhh.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Past History</b><br/>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				$brk=$ll;
				if($sig_inv['significant_finding'])
				{
				?>
				<!--<b>Significant Finding</b>-->
				<?php
				//echo $ll;
				echo "<p align='justify'><b>Significant Finding : </b>".$hhhh."</p>";
				}
				?>
				<!--<div style="page-break-after:always" align="justify"><?php echo $sig_inv['significant_finding']; ?></div>-->
				<?php
				$arr=explode(" ",$sig_inv['investigation_result']);
				$eee="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$eee.=$ss." ";
					//if($ll==500)
					if($lines==30)
					{
						$eee.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Examination</b><br/>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				$brk=$ll;
				//echo $lines;
				if($sig_inv['investigation_result'])
				{
				?>
				<!--<b>Investigation Result</b>-->
				<?php
				//echo $ll;
				echo "<p align='justify'><b>Investigation Result : </b>".$eee."</p>";
				}
				/////////////
				
				$cc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
				$arr=explode(" ",$cc['course']);
				$cccc="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$cccc.=$ss." ";
					//if(($z==1 && $ll==300) || ($z==1 && $ll==450) || ($ll==600))
					if($lines==31)
					{
						$cccc.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Course in hospital</b><br/>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				$q=mysqli_query($link,"SELECT * FROM `ipd_pat_diagnosis` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
				if(mysqli_num_rows($q)>0)
				{
				?>
				<!--<b>Diagnosis</b>-->
				<table class="table table-condensed" style="font-size:12px;">
					<tr>
						<th>#</th>
						<th>Provisional Diagnosis</th>
						<th>Order</th>
						<th>Certainity</th>
					</tr>
				<?php
				$jj=1;
				$trr="";
				while($rr=mysqli_fetch_array($q))
				{
					$certainty="";
					if($rr['certainity']!=0)
					{
						$certainty=$rr['certainity'];
					}
					$trr.="<tr><td>".$jj."</td><td>".$rr['diagnosis']."</td><td>".$rr['order']."</td><td>".$rr['certainity']."</td></tr>";
					//if(($ll>=300 && $ll<500 && $rows>10) || ($ll>=500 && $ll<600 && $rows>25) || ($ll>=600 && $ll<700 && $rows>40))
					if($lines==30)
					{
						$trr.='</table><p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Treatment at home</b><br/><table class="table table-condensed" style="font-size:12px;"><tr><th>Sl No</th><th>Medicine Name</th><th>Dosage</th><th>Frequency</th><th>Start Date</th><th>End Date</th><th>Total</th><th>Instruction</th></tr>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				$jj++;
				}
				echo $trr;
				?>
				</table>
				<?php
				}
				//echo $lines;
				if($cc['course'])
				{
				?>
				<!--<b>Course in hospital</b>-->
				<?php
				//echo $ll;
				echo "<p align='justify'><b>Course in hospital : </b>".$cccc."</p>";
				}
				//$cc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
				$arr=explode(" ",$cc['final_diagnosis']);
				$ffff="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$ffff.=$ss." ";
					//if(($z==1 && $ll==300) || ($z==1 && $ll==500) || ($ll==600))
					if($lines==30)
					{
						$ffff.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Lists of procedures performed with date</b><br/>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				//echo $lines;
				if($cc['final_diagnosis']!="")
				{
				?>
				<!--<b>Lists of procedures performed with date</b>-->
				<!--<b>Final diagnosis</b>-->
				<?php
				//echo $ll;
				echo "<p align='justify'><b>Final diagnosis</b> : ".$ffff."</p>";
				}
				
				$arr=explode(" ",$cc['procedure_with_date']);
				$ppp="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$ppp.=$ss." ";
					//if(($z==1 && $ll==300) || ($z==1 && $ll==500) || ($ll==600))
					if($lines==30)
					{
						$ppp.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Lists of procedures performed with date</b><br/>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				
				if($cc['procedure_with_date']!="")
				{
				?>
				<b>Lists of procedures performed with date</b>
				<?php
				//echo $ll;
				echo "<p align='justify'>".$ppp."</p>";
				}
				
				$fol=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
				$arr=explode(" ",$fol['follow_up']);
				$folll="";
				foreach($arr as $ss)
				{
					if($ss=str_replace("\n","<br/>",$ss))
					$line++;
					$folll.=$ss." ";
					//if(($z==1 && $ll==300) || ($z==1 && $ll==500) || ($ll==500))
					if($lines==30)
					{
						$folll.='<p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Discharge Instruction</b><br/>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				}
				
				if($fol['report_hospital'])
				{
				?>
					<!--<b>Discharge Instruction</b>-->
					<?php
					//echo $ll;
					echo "<p align='justify'><b>Report to Hospital if you have : </b>".$fol['report_hospital']."</p>";
				}
				
				$vit=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
				if($vit['final_bp'] || $vit['final_pulse'] || $vit['final_temp'] || $vit['final_weight'])
				{
				?>
				<b>Vital at time of discharge</b>
				<table class="table table-condensed">
					<!--<tr>
						<th>BP</th>
						<th>Pulse</th>
						<th>Temp</th>
						<th>Weight</th>
					</tr>-->
					<tr>
						<td><b>BP : </b><?php echo $vit['final_bp'];?></td>
						<td><b>Pulse : </b><?php echo $vit['final_pulse'];?></td>
						<td><b>Temp : </b><?php if($vit['final_temp']){ echo $vit['final_temp'];?><sup>0</sup>F<?php } ?></td>
						<td><b>Weight : </b><?php if($vit['final_weight']){ echo $vit['final_weight']." KG"; }?> </td>
					</tr>
				</table>
				<?php
				$l++;
				}
				//echo $lines;
				if($fol['follow_up'])
				{
				?>
				<b>Discharge Instruction</b>
				<?php
				//echo $ll;
				echo "<p align='justify'>".$folll."</p>";
				}
				if($fol['next_visit'])
				{
					echo "<p align='justify'><b>Next visit : </b>".convert_date_f($fol['next_visit'])."</p>";
				}
				//$q=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final_discharge` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
				$q=mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `type`='3'");
				$rows=mysqli_num_rows($q);
				if($rows>0)
				{
				?>
				<b>Medicine Prescribed</b>
				<table class="table table-condensed" style="font-size:12px;">
					<tr>
						<th>#</th>
						<th>Medicine Name</th>
						<th>Dosage / Instruction</th>
						<th>Quantity</th>
					</tr>
				<?php
				$j=1;
				$tdata="";
				//echo $ll;
				while($rr=mysqli_fetch_array($q))
				{
					$md=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$rr[item_code]'"));
					//~ if($rr['frequency']==1)
					//~ $fq="Immediately";
					//~ if($rr['frequency']==2)
					//~ $fq="Once a day";
					//~ if($rr['frequency']==3)
					//~ $fq="Twice a day";
					//~ if($rr['frequency']==4)
					//~ $fq="Thrice a day";
					//~ if($rr['frequency']==5)
					//~ $fq="Four times a day";
					//~ if($rr['frequency']==6)
					//~ $fq="Five times a day";
					//~ if($rr['frequency']==7)
					//~ $fq="Every Hour";
					//~ if($rr['frequency']==8)
					//~ $fq="Every 2 Hours";
					//~ if($rr['frequency']==9)
					//~ $fq="Every 3 Hours";
					//~ if($rr['frequency']==10)
					//~ $fq="Every 4 Hours";
					//~ if($rr['frequency']==11)
					//~ $fq="Every 5 Hours";
					//~ if($rr['frequency']==12)
					//~ $fq="Every 6 Hours";
					//~ if($rr['frequency']==13)
					//~ $fq="Every 7 Hours";
					//~ if($rr['frequency']==14)
					//~ $fq="Every 8 Hours";
					//~ if($rr['frequency']==15)
					//~ $fq="Every 10 Hours";
					//~ if($rr['frequency']==16)
					//~ $fq="Every 12 Hours";
					//~ if($rr['instruction']==1)
					//~ $ins="As Directed";
					//~ if($rr['instruction']==2)
					//~ $ins="Before Meal";
					//~ if($rr['instruction']==3)
					//~ $ins="Empty Stomach";
					//~ if($rr['instruction']==4)
					//~ $ins="After Meal";
					//~ if($rr['instruction']==5)
					//~ $ins="In the Morning";
					//~ if($rr['instruction']==6)
					//~ $ins="In the Evening";
					//~ if($rr['instruction']==7)
					//~ $ins="At Bedtime";
					//~ if($rr['instruction']==8)
					//~ $ins="Immediately";
					
					//$tdata.="<tr><td>".$j."</td><td>".$rr['medicine']."</td><td>".$rr['dosage']."</td><td>".$fq."</td><td>".convert_date_small($rr['start_date'])."</td><td>".convert_date_small($rr['end_date'])."</td><td>".$rr['duration']." ".$rr['unit_days']."</td><td>".$ins."</td></tr>";
					
					$tdata.="<tr><td>".$j."</td><td>".$md['item_name']."</td><td>".$rr['dosage']."</td><td>".$rr['quantity']."</td><td>".$ins."</td></tr>";
					
					if($lines==30)
					{
						$tdata.='</table><p class="text-center">----------------------------------------Continue to next page------------------------------------------<br/>Page '.$z.' of '.$_SESSION["page_no"].'</p></div></div><div class="page-break"></div><div class="container-fluid"><div class="text-center" style="">'.$my_head.'<h5>DISCHARGE CERTIFICATE</h5></div><div style="font-size:12px;"><table class="table table-condensed table-bordered" style="font-size:12px" id="d"><tr><th>UHID</th><td>'.$pat[uhid].'</td><th>IPD</th><td>'.$ipd.'</td><th>Admission Date</th><td>'.convert_date_g($dt[date]).' '.convert_time($dt[time]).'</td></tr><tr><th>Name</th><td>'.$pat[name].'</td><th>Age/Sex</th><td>'.$pat[age].' '.$pat[age_type].'/'.$pat[sex].'</td><th>Discharge Date</th><td>'.convert_date_g($dis_dt[date]).' '.convert_time($dis_dt[time]).'</td></tr><tr><th>Address</th><td colspan="5">'.$addr.'</td></tr></table><b>Attending Doctor : '.$doc[Name].'</b><br/><br/><b>Medicine Prescribed</b><br/><table class="table table-condensed" style="font-size:12px;"><tr><th>Sl No</th><th>Medicine Name</th><th>Dosage</th><th>Frequency</th><th>Start Date</th><th>End Date</th><th>Total</th><th>Instruction</th></tr>';
						$ll=1;
						$z++;
						$lines=15;
					}
					else
					{
						$ll++;
					}
					if($o==18)
					{
						$lines++;
						$o=0;
					}
					else
					{
						$o++;
					}
				$j++;
				}
				echo $tdata;
				//echo $ll;
				?>
				</table>
				<?php
				}
				$m_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
				if(mysqli_num_rows($m_qry)>0)
				{
					$medi="";
					$mdc=mysqli_fetch_array($m_qry);
					$medicine=explode("\n",$mdc['medicine']);
					?>
					<table class="table table-condensed" style="font-size:12px;">
						<tr>
							<th>Medicine Prescribed / Description / Instruction</th>
						</tr>
						<?php
						foreach($medicine as $mm)
						{
							if($mm)
							{
								echo "<tr><td>".$mm."</td></tr>";
								$line++;
							}
						}
						?>
					</table>
					<?php
				}
				
				$drug_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_post_discharge` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
				if(mysqli_num_rows($drug_qry)>0)
				{
				?>
				<table class="table table-condensed">
					<tr>
						<th>#</th><th>Medicine Prescribed</th><th>Instruction</th>
					</tr>
					<?php
					$pl=1;
					while($drg=mysqli_fetch_array($drug_qry))
					{
						$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$drg[item_code]'"));
					?>
					<tr>
						<td><?php echo $pl;?></td>
						<td><?php echo $itm['item_name'];?></td>
						<td><?php echo $drg['dosage'];?></td>
					</tr>
					<?php
					$line++;
					$pl++;
					}
					?>
				</table>
				<?php
				}
				
				$typ=mysqli_fetch_array(mysqli_query($link,"SELECT `type`,`diagnosed_by` FROM `ipd_dischage_type` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
				$dis_typ=mysqli_fetch_array(mysqli_query($link," SELECT `discharge_name` FROM `discharge_master` WHERE `discharge_id`='$typ[type]' "));
				if($dis_typ)
				{
					$tp=$dis_typ["discharge_name"];
				}else
				{
					$tp="N/A";
				}
				//~ if($typ['type']==0)
				//~ $tp="N/A";
				//~ if($typ['type']==1)
				//~ $tp="Routine";
				//~ if($typ['type']==2)
				//~ $tp="Transfer to other type of healthcare facility";
				//~ if($typ['type']==3)
				//~ $tp="Home Health Care";
				//~ if($typ['type']==4)
				//~ $tp="Transfer to short term hospital";
				//~ if($typ['type']==5)
				//~ $tp="In hospital death";
				//~ if($typ['type']==6)
				//~ $tp="Left against medical advice";
				//~ if($typ['type']==7)
				//~ $tp="Discharged alive, destination unknown";
				$_SESSION['page_no']=$z;
				?>
				<b>Discharge Type</b> : <?php echo $tp; ?><br/>
				<?php
				if($typ['diagnosed_by'])
				{
					$dr=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$typ[diagnosed_by]'"));
					$prepared=$dr['Name'];
				?>
				<!--<span style="float:right;margin-top:100px;right:20px;margin-right:40px;"><b>Prepared By</b> : <?php echo $dr['Name']; ?></span>-->
				
				<!--<table class="table table-no-top-border">
					<tr>
						<th style="width: 13%;">Explained By</th>
						<td style="width: 50%;">:</td>
						<th>Understood By:</th>
					</tr>
					<tr>
						<th>Doctor Name</th>
						<td colspan="2">:</td>
					</tr>
					<tr>
						<th>Registration No</th>
						<td colspan="2">:</td>
					</tr>
					<tr>
						<th>Date Time</th>
						<td colspan="2">:</td>
					</tr>
				</table>-->
				<hr/>
				<p>I have received the above instruction &amp; was given the opportunity to ask questions.</p><br>
				<span class="text-left">Treating Doctor Name &amp; Sign:</span><br><br>
				<span class="text-right">Name &amp; Sign of patient relative</span>
				<span class="text-left">Name &amp; Sign of Discharging Nurse:</span>
				<?php
				}
				//echo $lines;
				?>
			</div>
		</div>
		<footer class="text-center" style="font-size:12px;"><?php echo 'Page '.$z.' of '.$_SESSION["page_no"]; ?></footer>
	</body>
</html>
<script>
	//window.print();
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
p {
    margin: 0 0 3px;
}
.table {
    margin-bottom: 0px;
}
*
{
	font-size:13px;
}
</style>
