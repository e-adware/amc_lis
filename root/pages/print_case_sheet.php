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
		<title>MEDICAL CASE SHEET</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<style>
		.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
		{
			padding: 0 0 0 0;
		}
	</style>
	</head>
	<body  onafterprint="window.close();" onkeyup="close_window(event)">
		<div class="container-fluid">
			<div class="text-center" style="">
				<?php
				include('page_header.php');
				?>
				<span class="" style="font-size:16px;font-weight:bold;text-decoration:underline;">IPD PATIENT MEDICAL CASE SHEET</span>
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
				</table>
			</div>
			
	<?php
	$f1=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_AB` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$f2=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_CF` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$f3=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_GL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$f4=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_LL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	?>
	<table class="table table-condensed" style="display:none;">
		<tr>
			<th colspan="2" style="text-align:center;background:#dddddd;">Clinical notes &amp; Case summary</th>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;">History</th>
		</tr>
		<tr>
			<td colspan="2">
				<b>A. Complaints with duration / Illness or injury</b><br/>
				<?php $ill=str_replace("\n","<br/>",$f1['illness']); echo $ill;?>
			</td>
		</tr>
		<tr>
			<th colspan="2">B. Accident <?php if($f1['accident']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?></th>
		</tr>
		<?php
		if($f1['accident']>0)
		{
			$tr_accid="";
		}
		else
		{
			$tr_accid="display:none;";
		}
		?>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td>
				<b>Date of injury : </b>
				<?php if($f1['inj_dt']!='0000-00-00'){echo convert_date_g($f1['inj_dt']);}?>
			</td>
			<td>
				<b>Time of injury : </b>
				<?php if($f1['inj_tm']!='00:00:00'){echo convert_time($f1['inj_tm']);}?>
			</td>
		</tr>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<th colspan="2">
				Type of injury :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php if($f1['blunt']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Blunt
				<?php if($f1['penet']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Penetrating
				<?php if($f1['burns']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Burns
				<?php if($f1['inhal']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Inhalation Injury
				<?php if($f1['inj_oth']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Others
			</th>
		</tr>
		<?php
		if($f1['accident']>0)
		{
			if($f1['blunt']>0 || $f1['penet']>0 || $f1['burns']>0 || $f1['inhal']>0 || $f1['inj_oth']>0)
			{
				$tr_injury="";
			}
			else
			{
				$tr_injury="display:none;";
			}
		}
		else
		{
			$tr_injury="display:none;";
		}
		?>
		<tr id="tr_injury" style="<?php echo $tr_injury;?>">
			<td colspan="2">
				<?php $injury_hist=str_replace("\n","<br/>",$f1['injury_hist']); echo $injury_hist;?>
			</td>
		</tr>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td colspan="2">
				<b>Place of Occurence :</b><br/>
				<?php $occ=str_replace("\n","<br/>",$f1['occur']); echo $occ;?>
			</td>
		</tr>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td colspan="2">
				<b>Mechanism of Injury :</b><br/>
				<?php $inj=str_replace("\n","<br/>",$f1['mechan']); echo $inj;?>
			</td>
		</tr>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td colspan="2">
				<b>Pre-Hospital Care :</b><br/>
				<?php $car=str_replace("\n","<br/>",$f1['care']); echo $car;?>
			</td>
		</tr>
		<tr>
			<th colspan="2">C. Past Medical History with Duration</th>
		</tr>
		<tr>
			<td colspan="2">
				<?php if($f2['no_past']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> No past history
				<?php if($f2['copd']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> COPD or Lung Disorder
				<?php if($f2['cva']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> CVA / Stroke
				<?php if($f2['hyper']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Hypertension
				<?php if($f2['unknown']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Unknown<br/>
				<?php if($f2['heart']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Heart Condition
				<?php if($f2['cancer']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Cancer
				<?php if($f2['diabetes']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Diabetes
				<?php if($f2['seizure']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Seizures
				<?php if($f2['past_oth']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Others
			</td>
		</tr>
		<?php
		if($f2['no_past']>0 || $f2['copd']>0 || $f2['cva']>0 || $f2['hyper']>0 || $f2['unknown']>0 || $f2['heart']>0 || $f2['cancer']>0 || $f2['diabetes']>0 || $f2['seizure']>0 || $f2['past_oth']>0)
		{
			$tr_dur="";
		}
		else
		{
			$tr_dur="display:none;";
		}
		?>
		<tr id="tr_dur" style="<?php echo $tr_dur;?>">
			<td colspan="2">
				<?php $dur_hist=str_replace("\n","<br/>",$f2['dur_hist']); echo $dur_hist;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>D. Any Operation :</b><br/>
				<?php $oper=str_replace("\n","<br/>",$f2['operation']); echo $oper;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>E. Drug History :</b><br/>
				<?php if($f2['steroid']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Steroids
				<?php if($f2['hormone']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Hormones
				<?php if($f2['drug']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Thyroid Drugs
				<?php if($f2['pills']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Contraceptive Pills
				<?php if($f2['analgesic']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Analgesics
			</td>
		</tr>
		<?php
		if($f2['steroid']>0 || $f2['hormone']>0 || $f2['drug']>0 || $f2['pills']>0 || $f2['analgesic']>0)
		{
			$tr_drug="";
		}
		else
		{
			$tr_drug="display:none;";
		}
		?>
		<tr id="tr_drug" style="<?php echo $tr_drug;?>">
			<td colspan="2">
				<?php $drug_hist=str_replace("\n","<br/>",$f2['drug_hist']); echo $drug_hist;?>
			</td>
		</tr>
		<?php
		if($pat['sex']=="Male")
		{
			$sex_disb="text-decoration:line-through;";
		}
		if($pat['sex']=="Female")
		{
			$sex_disb="";
		}
		?>
		<tr>
			<td colspan="2">
				<b>F. <span style="<?php echo $sex_disb;?>">Menstrual :</span></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php
				if($pat['sex']=="Female")
				{
				if($f2['regular']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Regular
				<?php if($f2['irregular']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Irregular<br/>
				<b>History : </b>
				<?php echo $f2['mens_hist'];
				}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>G. Transfussion History :</b><br/>
				<?php $tran=str_replace("\n","<br/>",$f3['tran_hist']); echo $tran;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>H. Known Allergy (if any) :</b><br/>
				<?php $aller=str_replace("\n","<br/>",$f3['allergy']); echo $aller;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>I. Personal History :</b><br/>
				<?php if($f3['alcohol']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Alcohol
				<?php if($f3['smoking']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Smoking
				<?php if($f3['oth_addict']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Other Addiction
			</td>
		</tr>
		<?php
		if($f3['alcohol']>0 || $f3['smoking']>0 || $f3['oth_addict']>0)
		{
			$tr_per="";
		}
		else
		{
			$tr_per="display:none;";
		}
		?>
		<tr id="tr_per" style="<?php echo $tr_per;?>">
			<td colspan="2">
				<?php $per_hist=str_replace("\n","<br/>",$f2['per_hist']); echo $per_hist;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>J. Family History :</b><br/>
				<?php if($f3['htn']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> HTN
				<?php if($f3['dm']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> DM
				<?php if($f3['cva']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> CVA
				<?php if($f3['ihd']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> CA / IHD
				<?php if($f3['f_cancer']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Cancer
				<?php if($f3['asthma']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Br.Asthma
				<?php if($f3['f_oth']==1){echo "<b class='icon-check icon-large'></b> ";}else{echo "<b class='icon-check-empty icon-large'></b>";}?> Others
			</td>
		</tr>
		<?php
		if($f3['htn']>0 || $f3['dm']>0 || $f3['cva']>0 || $f3['ihd']>0 || $f3['f_cancer']>0 || $f3['asthma']>0 || $f3['f_oth']>0)
		{
			$tr_family="";
		}
		else
		{
			$tr_family="display:none;";
		}
		?>
		<tr id="tr_family" style="<?php echo $tr_family;?>">
			<td colspan="2">
				<?php $fam_hist=str_replace("\n","<br/>",$f2['fam_hist']); echo $fam_hist;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>K. Treatment History :</b><br/>
				<?php $t_hist=str_replace("\n","<br/>",$f3['treat_hist']); echo $t_hist;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>L. Past Investigation :</b><br/>
				<?php $p_hist=str_replace("\n","<br/>",$f3['past_inv']); echo $p_hist;?>
			</td>
		</tr>
	</table>
	<table class="table table-condensed">
		<tr>
			<th colspan="2" style="text-align:center;background:#dddddd;">Physical Examination</th>
		</tr>
		<tr>
			<th>General Examination</th>
			<th>Local Examination</th>
		</tr>
		<tr>
			<td>Height (cm)</td>
			<td><?php echo $f4['height'];?></td>
		</tr>
		<tr>
			<td>Weight (kg)</td>
			<td><?php echo $f4['weight'];?></td>
		</tr>
		<tr>
			<td>Respiratory Rate/min : </td>
			<td><?php echo $f4['rr'];?></td>
		</tr>
		<tr>
			<td>Blood Pressure : </td>
			<td><?php echo $f4['bp'];?></td>
		</tr>
		<tr>
			<td>Pulse/min : </td>
			<td><?php echo $f4['pulse'];?></td>
		</tr>
		<tr>
			<td>Temperature : </td>
			<td><?php echo $f4['temp'];?></td>
		</tr>
		<tr>
			<td>Pallor : </td>
			<td><?php echo $f4['pallor'];?></td>
		</tr>
		<tr>
			<td>Cyanosis : </td>
			<td><?php echo $f4['cyanosis'];?></td>
		</tr>
		<tr>
			<td>Clubbing : </td>
			<td><?php echo $f4['club'];?></td>
		</tr>
		<tr>
			<th colspan="2">Others</th>
		</tr>
		<tr>
			<td>O2 Saturation : </td>
			<td><?php echo $f4['saturation'];?></td>
		</tr>
		<tr>
			<td>APVU : </td>
			<td><?php echo $f4['apvu'];?></td>
		</tr>
		<tr>
			<td>GCS : </td>
			<td><?php echo $f4['gcs'];?></td>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;background:#dddddd;">Systemic Examination :</th>
		</tr>
		<tr>
			<td colspan="2">
				<b>CENTRAL NERVOUS SYSTEM :</b><br/>
				<?php $nerv=str_replace("\n","<br/>",$f4['nervous']); echo $nerv;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>RESPIRATORY SYSTEM :</b><br/>
				<?php $resp=str_replace("\n","<br/>",$f4['respiratory']); echo $resp;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>CARDIO VASCULAR SYSTEM :</b><br/>
				<?php $vasc=str_replace("\n","<br/>",$f4['vascular']); echo $vasc;?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>ABDOMEN AND GENITALIA :</b><br/>
				<?php $adob=str_replace("\n","<br/>",$f4['abdomen']); echo $adob;?>
			</td>
		</tr>
		<tr>
			<th colspan="2">Provisional Diagnosis :</th>
		</tr>
		<tr>
			<td rowspan="2">Case history recorded by :<br/>
				<?php
				$doct=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$f4[rec_doc]'"));
				echo $doct['Name'];
				?>
			</td>
			<td>
				History Informant : Patient/Attendant<br/>
				<?php $infom=str_replace("\n","<br/>",$f4['informant']); echo $infom;?>
			</td>
		</tr>
		<tr>
			<td>
				Name of the patient or attendand<br/>
				<?php echo $f4['patient'];?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				Relation with patient (in case of attendant)<br/>
				<?php echo $f4['rel'];?>
			</td>
		</tr>
	</table>
		</div>
		<!--<footer class="text-center" style="font-size:12px;"><?php echo 'Page '.$z.' of '.$_SESSION["page_no"]; ?></footer>-->
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
.table {
    margin-bottom: 0px;
}
*
{
	font-size:13px;
}
.icon-check, .icon-check-empty
{
	margin-left:20px;
}
</style>
