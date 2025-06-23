<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v=mysqli_real_escape_string($link, base64_decode($_GET["v"]));
$dept_id=mysqli_real_escape_string($link, base64_decode($_GET["dept_id"]));

if(!$dept_id)
{
	$appointment_info=mysqli_fetch_array(mysqli_query($link, " SELECT `dept_id` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

	$dept_id=$appointment_info["dept_id"];
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

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

//if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$pat_reg["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));
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
	<title>INITIAL ASSESSMENT FORM</title>
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
<?php 
if($dept_id==2){ ?>
	<div class="container-fluid">
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<div class="text-center">
				<?php include('page_header.php'); ?>
			</div>
			
			<center>
				<b>INITIAL ASSESSMENT FORM</b>
				<br>
				<b>(ENT Department)</b>
			</center>
			<hr>
			<br>
			<!--<p><b>Name : ................................................................................................................................................................................................................</b></p>-->
			<p>
				<b>Name : <?php echo $pat_info["name"]; ?></b>
				
				<span style="float: right;">
					<b>Age : <?php echo $age; ?></b>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					<b>Sex : <?php echo $pat_info["sex"]; ?></b>
				</span>
			</p>
			<p>
				<b>UHID : <?php echo $pat_info["patient_id"]; ?></b>
				<b style="float:right;margin-left: 10px;">Time : <?php echo date("H:i A", strtotime($pat_reg["time"])); ?></b>
				
				<b style="float:right;">Date : <?php echo date("j F Y", strtotime($pat_reg["date"])); ?></b>
			</p>
			<hr>
			<br>
			<p>
				<span>Height : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span>Weight : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span>BMI : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="float:right;">Insurance Patient- Yes/No</span>
			</p>
			<br>
			<p>Chief Complains :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>H/O PRESENT ILLNESS :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>H/O PAST ILLNESS :</p>
			<br>
			<br>
			<br>
			<br>
			<p>H/O FAMILY ILLNESS : </p>
			<br>
			<br>
			<br>
			<br>
			<p>H/O ADDICTIONS &amp; ALLERGIES :</p>
			<br>
			<br>
			<br>
			<p>General Examination :</p>
			<div class="row">
				<div class="span3">
					<p>Pulse:</p>
				</div>
				<div class="span2">
					<p>BP:</p>
				</div>
			</div>
			<p>LOCAL EXAM:</p>
			<div class="row">
				<div class="span5">
					<p>LEFT EAR:</p>
					<br>
					<p>RIGHT EAR:</p>
				</div>
				<div class="span3">
					<p>
						<img src="../../images/ent/ent_ear.jpg">
					</p>
				</div>
			</div>
			<div class="row">
				<div class="span5">
					<p>NOSE:</p>
				</div>
				<div class="span2">
					<p>
						<img src="../../images/ent/ent_nose.jpg">
					</p>
				</div>
			</div>
			<div class="row">
				<div class="span5">
					<p>THROAT:</p>
				</div>
				<div class="span2">
					<p>
						<img src="../../images/ent/ent_throat.jpg" style="width:75%;">
					</p>
				</div>
			</div>
		</div>
		<div class="page_break"></div>
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<div class="row">
				<div class="span5">
					<p>NECK:</p>
				</div>
				<div class="span2">
					<p>
						<img src="../../images/ent/ent_nec.jpg">
					</p>
				</div>
			</div>
			<div class="row">
				<div class="span5">
					<p>THROAT:</p>
				</div>
				<div class="span2">
					<p>
						<img src="../../images/ent/ent_larynx.jpg">
					</p>
				</div>
			</div>
			<br>
			<p>DIAGNOSIS / INVESTIGATION:</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>Follow up Date :</p>
		</div>
	</div>
<?php }
else if($dept_id==4){ ?>
	<div class="container-fluid">
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<div class="text-center">
				<?php include('page_header.php'); ?>
			</div>
			
			<center>
				<b>INITIAL ASSESSMENT FORM</b>
				<br>
				<b>(Medicine Department)</b>
			</center>
			<hr>
			<br>
			<!--<p><b>Name : ................................................................................................................................................................................................................</b></p>-->
			<p>
				<b>Name : <?php echo $pat_info["name"]; ?></b>
				
				<span style="float: right;">
					<b>Age : <?php echo $age; ?></b>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					<b>Sex : <?php echo $pat_info["sex"]; ?></b>
				</span>
			</p>
			<p>
				<b>UHID : <?php echo $pat_info["patient_id"]; ?></b>
				<b style="float:right;margin-left: 10px;">Time : <?php echo date("H:i A", strtotime($pat_reg["time"])); ?></b>
				
				<b style="float:right;">Date : <?php echo date("j F Y", strtotime($pat_reg["date"])); ?></b>
			</p>
			<hr>
			<br>
			<p>
				<span>Height : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span>Weight : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span>BMI : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="float:right;">Insurance Patient- Yes/No</span>
			</p>
			<br>
			<br>
			<p>Chief Complaints :</p>
			<br>
			<br>
			<br>
			<p>Personal History : <span style="float:right;">Allergy : Yes/No</span></p>
			<br>
			<br>
			<p>Habits :</p>
			<br>
			<p>Ongoing Medication :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>General Examination :</p>
			<br>
			<div class="row">
				<div class="span3" style="border-right: 1px solid #000;">
					<p>Febrile / Afebrile</p>
					<br>
					<br>
					<p>Pulse &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; min</p>
					<br>
					<br>
					<p>BP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; mmHg</p>
				</div>
				<div class="span2" style="border-right: 1px solid #000;">
					<p>PR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; min</p>
					<br>
					<br>
					<p>Pallor</p>
					<br>
					<br>
					<p>Icterus</p>
				</div>
				<div class="span2" style="border-right: 1px solid #000;">
					<p>Oedema</p>
					<br>
					<br>
					<p>LN Pathy</p>
					<br>
					<br>
					<p> &nbsp;</p>
				</div>
				<div class="span2">
					<p>Other :</p>
					<br>
					<br>
					<p> &nbsp;</p>
					<br>
					<br>
					<p> &nbsp;</p>
				</div>
			</div>
			<br>
			<br>
			<p>Systemic Examination :</p>
			<br>
			<div class="row">
				<div class="span4">
					<p>RS</p>
					<br>
					<br>
					<p>CVS</p>
				</div>
				<div class="span4">
					<p>P/A</p>
					<br>
					<br>
					<p>CNS</p>
				</div>
			</div>
		</div>
		<div class="page_break"></div>
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<p>Investigation Advised :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>Impression / Final Diagnosis</p>
			<br>
			<br>
			<br>
			<p>Treatment Plan</p>
			<b>Rx</b>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>Follow up Date :</p>
		</div>
	</div>
<?php }
	else if($dept_id==5){
?>
	<div class="container-fluid">
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<div class="text-center">
				<?php include('page_header.php'); ?>
			</div>
			
			<center>
				<b>INITIAL ASSESSMENT FORM</b>
				<br>
				<b>(OBGY Department)</b>
			</center>
			<hr>
			<br>
			<!--<p><b>Name : ................................................................................................................................................................................................................</b></p>-->
			<p>
				<b>Name : <?php echo $pat_info["name"]; ?></b>
				
				<span style="float: right;">
					<b>Age : <?php echo $age; ?></b>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					<b>Sex : <?php echo $pat_info["sex"]; ?></b>
				</span>
			</p>
			<p>
				<b>UHID : <?php echo $pat_info["patient_id"]; ?></b>
				<b style="float:right;margin-left: 10px;">Time : <?php echo date("H:i A", strtotime($pat_reg["time"])); ?></b>
				
				<b style="float:right;">Date : <?php echo date("j F Y", strtotime($pat_reg["date"])); ?></b>
			</p>
			<hr>
			<br>
			<p>
				<span>Height : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span>Weight : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="float:right;">Insurance Patient- Yes/No</span>
			</p>
			<br>
			<br>
			<p style="font-weight:bold;">Chief Complaints :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">
				Menstrual History : 
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				LMP
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				EDD
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Expected Pregnancy
			</p>
			<p>PMC:</p>
			<br>
			<br>
			<p style="font-weight:bold;">
				Obstetric History :
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				G
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				P
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				L
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				A
			</p>
			<br>
			<p style="font-weight:bold;">Past Medical / Surgical History :</p>
			<br>
			<br>
			<p>
				<span style="float:right;border: 1px solid #000;padding: 5px;">Nutritional Screening</span>
			</p>
			<br>
			<br>
			<p style="font-weight:bold;">Family History :</p>
			<br>
			<p>
				<b>General Examination :</b>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Pallor
			</p>
			<br>
			<div class="row">
				<div class="span2">
					<p>Temp.</p>
				</div>
				<div class="span2">
					<p>Pulse</p>
				</div>
				<div class="span2">
					<p>B.P.</p>
				</div>
				<div class="span2">
					<p>RR :</p>
				</div>
			</div>
			<br>
			<br>
			<p style="font-weight:bold;">Systemic Examination :</p>
			<br>
			<div class="row">
				<div class="span5">
					<p>RS</p>
					<br>
					<br>
					<p>P/A</p>
				</div>
				<div class="span4">
					<p>CVS</p>
					<br>
					<br>
					<p>CNS</p>
				</div>
			</div>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">P/S :</p>
			<br>
			<br>
			<p style="font-weight:bold;">P/V :</p>
			<br>
			<br>
			<p style="font-weight:bold;">Investigation Advised :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">Laboratory :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">Radiology :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">Treatment Plan :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">
				Follow up Date :
				<span style="float:right;">Signature of the Consultant</span>
			</p>
		</div>
		<div class="page_break"></div>
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			
		</div>
	</div>
<?php }
else if($dept_id==10){ ?>
	<div class="container-fluid">
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<div class="text-center">
				<?php include('page_header.php'); ?>
			</div>
			
			<center>
				<b>INITIAL ASSESSMENT FORM</b>
				<br>
				<b>(Surgery Department)</b>
			</center>
			<hr>
			<br>
			<!--<p><b>Name : ................................................................................................................................................................................................................</b></p>-->
			<p>
				<b>Name : <?php echo $pat_info["name"]; ?></b>
				
				<span style="float: right;">
					<b>Age : <?php echo $age; ?></b>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					<b>Sex : <?php echo $pat_info["sex"]; ?></b>
				</span>
			</p>
			<p>
				<b>UHID : <?php echo $pat_info["patient_id"]; ?></b>
				<b style="float:right;margin-left: 10px;">Time : <?php echo date("H:i A", strtotime($pat_reg["time"])); ?></b>
				
				<b style="float:right;">Date : <?php echo date("j F Y", strtotime($pat_reg["date"])); ?></b>
			</p>
			<hr>
			<br>
			<p>
				<span>Weight : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<!--<span>Weight : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span>BMI : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>-->
				<span style="float:right;">Insurance Patient- Yes/No</span>
			</p>
			<br>
			<p style="font-weight:bold;">Chief Complaints :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">History of Present Illness : </p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">Past Medical / Surgical History :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">Medication Details :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">General Examination :</p>
			<br>
			<div class="row">
				<div class="span2">
					<p>Temp.</p>
					<br>
					<br>
					<p>Pallor</p>
				</div>
				<div class="span2">
					<p>Pulse</p>
					<br>
					<br>
					<p>Icterus</p>
				</div>
				<div class="span2">
					<p>B.P.</p>
					<br>
					<br>
					<p>Oedema</p>
				</div>
				<div class="span2">
					<p>RR :</p>
					<br>
					<br>
					<p>Lymphadenopathy</p>
				</div>
			</div>
			<br>
			<br>
			<p style="font-weight:bold;">On Examination</p>
			<br>
			<br>
			<p style="font-weight:bold;">Systemic Examination :</p>
			<br>
			<div class="row">
				<div class="span3">
					<p>RS-</p>
					<br>
					<br>
					<p>P/A</p>
				</div>
				<div class="span3">
					<p>CVS-</p>
				</div>
				<div class="span3">
					<p>CNS-</p>
				</div>
			</div>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">Skull, Spine, Pelvis, Extremities</p>
			<br>
			<p style="font-weight:bold;">Clinical Impression :</p>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">Investigation Advised :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">Treatment Plan :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p style="font-weight:bold;">
				Follow up Date :
				<span style="float:right;">Signature of the Consultant</span>
			</p>
		</div>
		<div class="page_break"></div>
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			
		</div>
	</div>
<?php }
else if($dept_id==400){ ?>
	<div class="container-fluid">
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<div class="text-center">
				<?php include('page_header.php'); ?>
			</div>
			
			<center>
				<b>INITIAL ASSESSMENT FORM</b>
				<br>
				<b>(Emergency Department)</b>
			</center>
			<hr>
			
			<!--<p><b>Name : ................................................................................................................................................................................................................</b></p>-->
			<p>
				<b>Name : <?php echo $pat_info["name"]; ?></b>
				
				<span style="float: right;">
					<b>Age : <?php echo $age; ?></b>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					<b>Sex : <?php echo $pat_info["sex"]; ?></b>
				</span>
			</p>
			<p>
				<b>UHID : <?php echo $pat_info["patient_id"]; ?></b>
				<b style="float:right;margin-left: 10px;">Time : <?php echo date("H:i A", strtotime($pat_reg["time"])); ?></b>
				
				<b style="float:right;">Date : <?php echo date("j F Y", strtotime($pat_reg["date"])); ?></b>
			</p>
			<hr>
			<br>
			<p>
				<span>S/B. Dr. : 
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				</span>
				<span>
					MLC : YES/NO
					&nbsp;&nbsp;&nbsp;&nbsp;
					Insurance Patient- Yes/No
				</span>
			</p>
			<br>
			<p>
				<span>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				</span>
				<span>
					MLC No. :
				</span>
			</p>
			<p>
				Condition at Arrival: 
				<span>Critical <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Unstable <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Stable <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>DOA <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
			</p>
			<br>
			<p>
				<span><b>C/O :</b></span>
			</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p><b>Past Medical/Surgical History:</b></p>
			<br>
			<br>
			<p><b>History of Present illness:</b> </p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>
				<b>Addiction :</b>
				<span>Tobacco <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Alcohol <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Drugs <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Others <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
			</p>
			<p>
				<b>OBG History:</b>
				
				LMP_______________Pregnancy____________________Child_________________Delivery__________
			</p>
			<p>
				<u><b>General Examination:</b></u>
				<br>
				<span>Pallor <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Cyanosis <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Edema <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Icterus <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Clubbing <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>Lymph Node <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
			</p>
			<p>
				<u><b>Vital Parameters:</b></u>
				<br>
				<br>
				P/R : __________/MIN &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; B/P : _______________mm of Hg &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SPO<sub>2</sub> :___________________RBS:______________________
				<br>
				<br>
				Temp:_____________________RR:____________________Weight:__________________ Height:__________________
			</p>
			<br>
			<div class="text-center">
				<?php //include('page_header.php'); ?>
			</div>
			<p style="font-weight:bold;">Systemic Examination :</p>
			<br>
			<p>
				R/S : 
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				CVS :
			</p>
			<br>
			<p>
				P/A : 
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				CNS :
			</p>
			<br>
			<p>
				Skin and Soft tissue:
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;
				
				Urogential:
			</p>
			
			<br>
			<br>
			<br>
			<br>
			<p>
				<b>Recent Investigation done outside:</b>
			</p>
			<br>
			<br>
			<p>
				<b>Provisional Diagnosis:</b>
			</p>
			<br>
			<br>
			<br>
			<p><b>Management Plan</b> (Investigations/Drugs /Mounting/Surgery/Any other)</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p><b>Management Goal:</b></p>
			<br>
			<br>
			<p>
				<b>Disposition:</b>
				<br>
				<span>ICU <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>WARD <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>D/S <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>LAMA <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
				<span>REFERED <img src="../../images/uncheck_box.png" style="width: 40px;height: 35px;"></span>
			</p>
			<p><b>Family Counseling done to:</b></p>
			<br>
			<p><b>Relationship with patient:</b></p>
			<br>
			<p><b>Date and time:</b></p>
			<br>
			<p><b>Refer/Informed to Consultant:</b></p>
		</div>
		<div class="page_break"></div>
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			
		</div>
	</div>
<?php }
else if($dept_id==40000){ ?>
	<div class="container-fluid">
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<div class="text-center">
				<?php include('page_header.php'); ?>
			</div>
			
			<center>
				<b>INITIAL ASSESSMENT FORM</b>
				<br>
				<b>(Casualty Department)</b>
			</center>
			<hr>
			<br>
			<!--<p><b>Name : ................................................................................................................................................................................................................</b></p>-->
			<p>
				<b>Name : <?php echo $pat_info["name"]; ?></b>
				
				<span style="float: right;">
					<b>Age : <?php echo $age; ?></b>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					<b>Sex : <?php echo $pat_info["sex"]; ?></b>
				</span>
			</p>
			<p>
				<b>UHID : <?php echo $pat_info["patient_id"]; ?></b>
				<b style="float:right;margin-left: 10px;">Time : <?php echo date("H:i A", strtotime($pat_reg["time"])); ?></b>
				
				<b style="float:right;">Date : <?php echo date("j F Y", strtotime($pat_reg["date"])); ?></b>
			</p>
			<hr>
			<br>
			<p>
				<span>S/B. Dr. : 
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				</span>
				<span>
					MLC : YES/NO
					&nbsp;&nbsp;&nbsp;&nbsp;
					Insurance Patient- Yes/No
				</span>
			</p>
			<br>
			<p>
				<span>H/N :
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				</span>
				<span>
					MLC No. :
				</span>
			</p>
			<br>
			<p>
				<span>C/O :
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				</span>
				<span>
					Wt :
				</span>
			</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>P/H/O :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>PT. IS On T/T : </p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>Addiction : Yes / No</p>
			<br>
			<p>
				O/E : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				A Febrile / Febrile
			</p>
			<br>
			<p>
				P/R : 
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				/min
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				B/P : 
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				mm of Hg
			</p>
			<br>
			<p>
				R/S : 
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				CVS :
			</p>
			<br>
			<p>
				P/A : 
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				CNS :
			</p>
			
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>
				Adv. :
			</p>
			<br>
			<p>
				RBS By GM : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				mg%
			</p>
			<br>
			<p>ECG :</p>
			<br>
			<br>
			<br>
			<br>
			<p>SPO<sub>2</sub> :</p>
			<br>
			<p>X-Ray :</p>
			<br>
			<br>
			<p>INVESTIGATION OUT SIDE :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>PROVISIONAL DIAGNOSIS :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>Treatment :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>
				Refer/Informed to Dr. :
				<span style="float:right;">Sign (CMO)</span>
			</p>
		</div>
		<div class="page_break"></div>
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			
		</div>
	</div>
<?php }
else { ?>
	<div class="container-fluid">
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<div class="text-center">
				<?php include('page_header.php'); ?>
			</div>
			
			<center>
				<b>INITIAL ASSESSMENT FORM</b>
				
			</center>
			<hr>
			<br>
			<!--<p><b>Name : ................................................................................................................................................................................................................</b></p>-->
			<p>
				<b>Name : <?php echo $pat_info["name"]; ?></b>
				
				<span style="float: right;">
					<b>Age : <?php echo $age; ?></b>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					<b>Sex : <?php echo $pat_info["sex"]; ?></b>
				</span>
			</p>
			<p>
				<b>UHID : <?php echo $pat_info["patient_id"]; ?></b>
				<b style="float:right;margin-left: 10px;">Time : <?php echo date("H:i A", strtotime($pat_reg["time"])); ?></b>
				
				<b style="float:right;">Date : <?php echo date("j F Y", strtotime($pat_reg["date"])); ?></b>
			</p>
			<hr>
			<br>
			<p>
				<span>Height : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span>Weight : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span>BMI : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="float:right;">Insurance Patient- Yes/No</span>
			</p>
			<br>
			<br>
			<p>Chief Complaints :</p>
			<br>
			<br>
			<br>
			<p>Personal History : <span style="float:right;">Allergy : Yes/No</span></p>
			<br>
			<br>
			<p>Habits :</p>
			<br>
			<p>Ongoing Medication :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>General Examination :</p>
			<br>
			<div class="row">
				<div class="span3" style="border-right: 1px solid #000;">
					<p>Febrile / Afebrile</p>
					<br>
					<br>
					<p>Pulse &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; min</p>
					<br>
					<br>
					<p>BP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; mmHg</p>
				</div>
				<div class="span2" style="border-right: 1px solid #000;">
					<p>PR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; min</p>
					<br>
					<br>
					<p>Pallor</p>
					<br>
					<br>
					<p>Icterus</p>
				</div>
				<div class="span2" style="border-right: 1px solid #000;">
					<p>Oedema</p>
					<br>
					<br>
					<p>LN Pathy</p>
					<br>
					<br>
					<p> &nbsp;</p>
				</div>
				<div class="span2">
					<p>Other :</p>
					<br>
					<br>
					<p> &nbsp;</p>
					<br>
					<br>
					<p> &nbsp;</p>
				</div>
			</div>
			<br>
			<br>
			<p>Systemic Examination :</p>
			<br>
			<div class="row">
				<div class="span4">
					<p>RS</p>
					<br>
					<br>
					<p>CVS</p>
				</div>
				<div class="span4">
					<p>P/A</p>
					<br>
					<br>
					<p>CNS</p>
				</div>
			</div>
		</div>
		<div class="page_break"></div>
		<div style="border: 2px solid #000;padding: 3px;height: 1100px;">
			<p>Investigation Advised :</p>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>Impression / Final Diagnosis</p>
			<br>
			<br>
			<br>
			<p>Treatment Plan</p>
			<b>Rx</b>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<p>Follow up Date :</p>
		</div>
	</div>
<?php } ?>
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
.page_break{ page-break-after: always;}
@page
{
	margin-top:0.8cm;
}
.box {
  float: left;
  width: 15px;
  height: 15px;
  border: 1px solid #000;
}

</style>
