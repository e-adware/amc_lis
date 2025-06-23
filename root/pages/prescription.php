<?php
session_start(); 
include("../../includes/connection.php");

$q=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `prescription_line_master` WHERE `id`=1 "));
$i=0;
$space="";
while($i<$q["line"])
{
	$space.="<br>";
	$i++;
}
//echo $space;
$uhid=$_GET['uhid'];
$opd_id=$opd=$_GET['opd'];
$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$uhid=$pat_info['patient_id'];
$company_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `company_master` limit 0,1 "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
$con_id=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
$con=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_id[consultantdoctorid]' "));  
$adv_paid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));
$dpt_name=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `department` WHERE `dept_id`='$con[dept_id]' "));
//$ref_doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `pat_ref_doc` WHERE `patient_id`='$uhid' AND `pin`='$opd_id' ) "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}
$regd_no=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `qualification`,`regd_no` FROM `employee` WHERE `emp_id` IN (SELECT `emp_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_id[consultantdoctorid]') "));

$doc_qua=mysqli_fetch_array(mysqli_query($link," SELECT `qualification` FROM `employee` WHERE `emp_id`='$con[emp_id]' "));
$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$add_info[state]' "));
$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$add_info[district]' "));
 $address="";
 if($pat_info["gd_name"])
 {
	 $address="C/O: ".$pat_info["gd_name"];
 }
 if($add_info["city"])
 {
	 $address.="<br>Town/Vill- ".$add_info["city"];
 }
 if($pat_info["address"])
 {
	$address.="<br>".$pat_info["address"];
}
 if($add_info["police"])
 {
	$address.="<br>P.S- ".$add_info["police"];
}
 if($st_info["name"])
 {
	$address.=", Dist- ".$dist_info["name"];
}
if($dist_info["name"])
{
	$address.="<br>State- ".$st_info["name"];
}
if($add_info["pin"])
{
	$address.=", PIN-".$add_info["pin"];
}
 
function convert_date($date)
{
	if($date)
	{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-Y', $timestamp);
	return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
// Age Calculator
function age_calculator($dob)
{
	$from = new DateTime($dob);
	$to   = new DateTime('today');
	$year=$from->diff($to)->y;
	$month=$from->diff($to)->m;
	if($year==0)
	{
		//$month=$from->diff($to)->m;
		if($month==0)
		{
			$day=$from->diff($to)->d;
			return $day." Days";
		}else
		{
			return $month." Months";
		}
	}else
	{
		return $year.".".$month." Years";
	}
}
$visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `visit_fee` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($visit_fee["visit_fee"]==0)
{
	$visit_fee=0;
	$visit_type="Free";
}else
{
	$visit_fee=$visit_fee["visit_fee"];
	$visit_fee_num=mysqli_num_rows(mysqli_query($link, " SELECT `visit_fee` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `visit_fee`>0 "));
	//~ if($visit_fee_num==1)
	//~ {
		//~ $visit_type="First visit";
	//~ }
	//~ if($visit_fee_num==2)
	//~ {
		//~ $visit_type="Second visit";
	//~ }
	//~ if($visit_fee_num==3)
	//~ {
		//~ $visit_type="Third visit";
	//~ }
	function numToOrdinalWord($num)
	{
		$first_word = array('eth','First','Second','Third','Fouth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Elevents','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
		$second_word =array('','','Twenty','Thirthy','Forty','Fifty');

		if($num <= 20)
			return $first_word[$num];

		$first_num = substr($num,-1,1);
		$second_num = substr($num,-2,1);

		return $string = str_replace('y-eth','ieth',$second_word[$second_num].'-'.$first_word[$first_num]);
	}
	//echo $visit_fee_num;
	$visit_type=numToOrdinalWord($visit_fee_num)." visit";
}
$dept_name=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$con[dept_id]'"));
$vit=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
$nurse=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$vit[user]' "));
$page_name="<center style='margin-right:40px;'><b>OP CASE SHEET</b></center>";
//=================
$complaints=mysqli_query($link," Select * from pat_complaints where patient_id='$uhid' and opd_id='$opd' ");
$exam=mysqli_fetch_array(mysqli_query($link," Select * from pat_examination where patient_id='$uhid' and opd_id='$opd' "));
$comp_num=mysqli_num_rows($complaints);
$vital=mysqli_query($link," Select * from pat_vital where patient_id='$uhid' and opd_id='$opd' ");
$vit_num=mysqli_num_rows($vital);
$consultation=mysqli_query($link," Select * from pat_consultation where patient_id='$uhid' and opd_id='$opd' ");
$conslt_num=mysqli_num_rows($consultation);

$conslt_doc_id=mysqli_fetch_array(mysqli_query($link," SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd' "));
$conslt_doc_name=mysqli_fetch_array(mysqli_query($link," SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$conslt_doc_id[consultantdoctorid]' "));
$disp_note=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `pat_disposition` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
if($disp_note['disposition']==0)
{
	$note="";
}
if($disp_note['disposition']==1)
{
	$note="Admit Patient";
}
if($disp_note['disposition']==2)
{
	if($disp_note['ref_doctor_to'])
	{
		$note="Refer to ".$disp_note['ref_doctor_to'];
	}
	else
	{
		$note="Refer";
	}
		
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prescription</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>

</head>
<body style="width:100%">
<div class="container-fluid">
	<div class="row" style="text-align:right;" >
		<?php //include('page_header.php'); ?>
		<b style="font-size:22px;">OPD CASE SHEET</b><br>
		<b>Consultant : <?php echo $con["Name"].",&nbsp;&nbsp;".$regd_no["qualification"]; ?></b><br>
		<b>Regd No : <?php echo $regd_no['regd_no']; ?></b>
	</div>
	<div>
		<?php //include('patient_header.php'); ?>
		<br>
		<br>
		<hr>
		<table class="table table-no-top-border">
			<tr>
				<th width="10%">UHID</th>
				<td width="45%">: <?php echo $pat_info["uhid"]; ?></td>
<!--
				<th>Bill No</th>
				<td>: <?php echo $adv_paid["bill_no"]; ?></td>
-->
				<th width="15%">Date Time</th>
				<td>: <?php echo convert_date($adv_paid["date"]); ?> <?php echo convert_time($adv_paid["time"]); ?></td>
			</tr>
			<tr>
				<th>Name</th>
				<td>: <?php echo $pat_info["name"]; ?></td>
				<th>Department</th>
				<td>: <?php echo $dept_name['name']; ?></td>
			</tr>
			<tr>
				<th>Sex / Age</th>
				<td>: <?php echo $pat_info["sex"]." / ".$age; ?></td>
				<!--<th>Consultant</th>
				<td>: <?php echo $con["Name"].",&nbsp;&nbsp;".$regd_no["qualification"]; ?></td>-->
			</tr>
			<?php
				if($regd_no['regd_no'])
				{
				?>
<!--
				<tr>
				<th></th>
				<td></td>
				<th>Regd No</th>
				<td>: <?php echo $regd_no['regd_no']; ?>
				</tr>
-->
				<?php
				}
				?>
			<tr>
				<th>Address</th>
				<td>: <?php echo $address; ?></td>
				<th>Consultant Type</th>
				<td>: <?php echo $visit_type; ?></td>
			</tr>
			<tr>
				<th>PIN</th>
				<td>: <?php echo $opd_id; ?></td>
				<th>Mobile</th>
				<td>: <?php echo $pat_info["phone"]; ?></td>
			</tr>
		</table>
		<hr/>
		<br/>
	</div>
	<table class="table table-no-top-border" style="border-bottom:2px dashed;">
		<tr>
			<th colspan="5"><u>Nursing Assessment</u> <i style="font-size:10px;">(Done by <?php echo $nurse['name'];?>)</i> :</th>
		</tr>
		<tr>
			<th colspan="5">&nbsp;</th>
		</tr>
		<tr>
			<th>BP : <?php echo $vit['systolic']."/".$vit['diastolic']?></th>
			<th>Pulse : <?php echo $vit['pulse'];?></th>
			<th>Height : <?php echo $vit['height'];?> CM</th>
			<th>Weight : <?php echo $vit['weight'];?> KG</th>
			<th>BMI : <?php echo $vit['BMI_1'].".".$vit['BMI_2'];?></th>
		</tr>
	</table>
			<div class="col-lg-12 col-md-12 col-sm-12" style="padding:0;">
				<br/>
				<?php if($comp_num>0)
				{
					?>
				<p><b>Chief Complaints:</b><br/>
					<?php
					while($comp=mysqli_fetch_array($complaints))
					{ ?>
				<span style="text-indent:0px;text-align:justify;"><?php echo $comp['comp_one'].' for '.$comp['comp_two'].' '.$comp['comp_three'].'.'; ?></span><br/>
				<?php
					}
				?>
				</p>
				<?php
				}
				if($exam['history']!='')
				{?>
				<p><b>History:</b> 
				<span style="text-indent:0px;text-align:justify;"><?php echo $exam['history']; ?></span>
				</p>
				<?php
				}
				if($exam['examination']!='')
				{?>
				<p><b>Physical Examination:</b> 
				<span style="text-indent:0px;text-align:justify;"><?php echo $exam['examination']; ?></span>
				</p>
				<?php
				}
								
					$diag=mysqli_query($link," Select * from pat_diagnosis where patient_id='$uhid' and opd_id='$opd' ");
					$dg_num=mysqli_num_rows($diag);
					if($dg_num>0)
					{
						$mm=1;
					?>
					<p><b>Diagnosis:</b><br/>
						<table class="table" style="">
						<tr>
							<th>Sl No.</th>
							<th>Diagnosis</th>
							<th>Order</th>
							<th>Certainity</th>
						</tr>
						<?php
						while($dg=mysqli_fetch_array($diag))
						{
						?>
						<tr>
							<td><?php echo $mm;?></td>
							<td><?php echo $dg['diagnosis'];?></td>
							<td><?php echo $dg['order'];?></td>
							<td><?php echo $dg['certainity'];?></td>
						</tr>
						<?php
						}
						?>
						</table>
					</p>
					<?php
					}
					
				$cons=mysqli_fetch_array($consultation);
				if($cons['con_note']!='')
				{
				?>
				<p><b>Consultation Notes:</b>
				<?php
					$array = preg_split('/$\R?^/m', $cons['con_note']);
					foreach($array as $treatment)
					{
						if($treatment)
						{
				?>
						<span style="text-indent:0px;text-align:justify;"><?php echo $treatment; ?></span>
				<?php
						}
					}
					?>
					</p>
					<?php
				}
					
					
					$medi=mysqli_query($link," Select * from medicine_check where patient_id='$uhid' and opd_id='$opd' ");
					$medi_num=mysqli_num_rows($medi);
					if($medi_num>0)
					{
					?>
					<p><b>Rx</b>
					<table class="table" style="">
						<tr>
							<th>Sl No.</th>
							<th>Medication</th>
							<th>Dosage</th>
							<th>Instruction</th>
						</tr>
					<?php
						$i=1;
						while($medi_detail=mysqli_fetch_array($medi))
						{
							$medi_name=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ph_item_master` WHERE `item_code`='$medi_detail[item_code]' "));
							if($medi_detail["frequency"]==1)
							$freq="Immediately";
							if($medi_detail["frequency"]==2)
							$freq="Once a day";
							if($medi_detail["frequency"]==3)
							$freq="Twice a day";
							if($medi_detail["frequency"]==4)
							$freq="Thrice a day";
							if($medi_detail["frequency"]==5)
							$freq="Four times a day";
							if($medi_detail["frequency"]==6)
							$freq="Five times a day";
							if($medi_detail["frequency"]==7)
							$freq="Every hour";
							if($medi_detail["frequency"]==8)
							$freq="Every 2 hours";
							if($medi_detail["frequency"]==9)
							$freq="Every 3 hours";
							if($medi_detail["frequency"]==10)
							$freq="Every 4 hours";
							if($medi_detail["frequency"]==11)
							$freq="Every 5 hours";
							if($medi_detail["frequency"]==12)
							$freq="Every 6 hours";
							if($medi_detail["frequency"]==13)
							$freq="Every 7 hours";
							if($medi_detail["frequency"]==14)
							$freq="Every 8 hours";
							if($medi_detail["frequency"]==15)
							$freq="Every 10 hours";
							if($medi_detail["frequency"]==16)
							$freq="Every 12 hours";
							if($medi_detail["frequency"]==17)
							$freq="On alternate days";
							if($medi_detail["frequency"]==18)
							$freq="Once a week";
							if($medi_detail["frequency"]==19)
							$freq="Twice a week";
							if($medi_detail["frequency"]==20)
							$freq="Thrice a week";
							if($medi_detail["frequency"]==21)
							$freq="Every 2 weeks";
							if($medi_detail["frequency"]==22)
							$freq="Every 3 weeks";
							if($medi_detail["frequency"]==23)
							$freq="Once a month";
							
							if($medi_detail['instruction']==1)
							$ins="As Directed";
							if($medi_detail['instruction']==2)
							$ins="Before Meal";
							if($medi_detail['instruction']==3)
							$ins="Empty Stomach";
							if($medi_detail['instruction']==4)
							$ins="After Meal";
							if($medi_detail['instruction']==5)
							$ins="In the Morning";
							if($medi_detail['instruction']==6)
							$ins="In the Evening";
							if($medi_detail['instruction']==7)
							$ins="At Bedtime";
							if($medi_detail['instruction']==8)
							$ins="Immediately";
					?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $medi_name["item_name"]; ?></td>
							<td><?php echo $medi_detail["dosage"]; ?></td>
							<td><?php echo $ins; ?></td>
						</tr>
					<?php
						$i++;
						}
					?>
				</table>
				</p>
				<?php
				}
				
					$test=mysqli_query($link,"Select * from patient_test_details where patient_id='$uhid' and opd_id='$opd' ");
					$test_num=mysqli_num_rows($test);
					$all_tests="";
					if($test_num!=0)
					{
				?>
						<p><b>Investigation:</b><br/>
						<span style="text-indent:0px;">
				<?php
						$i=1;
						while($test_detail=mysqli_fetch_array($test))
						{
							$test_name=mysqli_fetch_array(mysqli_query($link,"Select distinct testname from testmaster where testid='$test_detail[testid]'"));
							if($all_tests)
							{
								$all_tests.=", ".$test_name['testname'];
							}
							else
							{
								$all_tests=$test_name['testname'];
							}
						}
						echo $all_tests;
						?>
						</span>
						</p>
						<?php
					}
				?>
		</div>
		<?php
		if($note)
		{
		?>
		<b>Disposition : </b><?php echo $note;
		}
		if($disp_note['disp_note'])
		{
			$nots=str_replace(",", "<br/>", "$disp_note[disp_note]");
		?>
		<br/>
		<b>Advice : </b><br/><?php echo $nots;
		
		}
		?>
	</div>
	<span style="position:fixed;bottom:2%;right:0%;text-align:center;" id="un"><?php echo $conslt_doc_name['Name'].", ".$qly['qualification']."<br/>".$qly['designation']; ?></span>
</body>
</html>
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
<script>//window.print()</script>
