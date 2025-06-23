<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");
$time=date("H:i:s");

//---------------------------------------------------------------------------------------------------//
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
//-------------------------------------------------------------------------------------------------//


if($_POST["type"]=="search_patient_list_ipd")
{
	$ward=$_POST['ward'];
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	if($ward==0)
	{
		$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' ORDER BY `date` DESC LIMIT 0,20";
	}
	else
	{
		$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `patient_id` IN (SELECT `patient_id` FROM `ipd_pat_bed_details` WHERE `ward_id`='$ward') ORDER BY `date` DESC LIMIT 0,20";
	}
	if($uhid)
	{
		if($ward==0)
		{
			$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `patient_id`='$uhid') ORDER BY `date` DESC LIMIT 0,20";
		}
		else
		{
			$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `patient_id` IN (SELECT `patient_id` FROM `ipd_pat_bed_details` WHERE `patient_id`IN(SELECT `patient_id` FROM `patient_info` WHERE `patient_id`='$uhid') AND `ward_id`='$ward') ORDER BY `date` DESC LIMIT 0,20";
		}
	}
	if($ipd)
	{
		if($ward==0)
		{
			$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `ipd_id`='$ipd' ORDER BY `date` DESC LIMIT 0,20";
		}
		else
		{
			$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `ipd_id` IN (SELECT `ipd_id` FROM `ipd_pat_bed_details` WHERE `ipd_id`='$ipd' AND `ward_id`='$ward') ORDER BY `date` DESC LIMIT 0,20";
		}
	}
	if($name)
	{
		if($ward==0)
		{
			$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%') ORDER BY `date` DESC LIMIT 0,20";
		}
		else
		{
			$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%' AND `patient_id` IN (SELECT `patient_id` FROM `ipd_pat_bed_details` WHERE `ward_id`='$ward')) ORDER BY `date` DESC LIMIT 0,20";
		}
	}
	if($dat)
	{
		if($ward==0)
		{
			$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `date`='$dat' ORDER BY `date` DESC LIMIT 0,20";
		}
		else
		{
			$q="SELECT * FROM `ot_book` WHERE `pac_status`='0' AND `date`='$dat' AND `patient_id` IN (SELECT `patient_id` FROM `ipd_pat_bed_details` WHERE `ward_id`='$ward') ORDER BY `date` DESC LIMIT 0,20";
		}
	}
	//echo $q;
	$num=mysqli_num_rows(mysqli_query($link,$q));
	if($num>0)
	{
		$qq=mysqli_query($link,$q);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>UHID</th>
				<th>PIN</th>
				<th>Name</th>
				<th>Sex</th>
				<th>Age (DOB)</th>
				<th>Contact</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				if($p["dob"]!=""){ $age=age_calculator($p["dob"])." (".$p["dob"].")"; }else{ $age=$p["age"]." ".$p["age_type"]; }
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['ipd_id'];?>','<?php echo $r['schedule_id'];?>')" style="cursor:pointer;">
					<td><?php echo $r['patient_id'];?></td>
					<td><?php echo $r['ipd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $age;?></td>
					<td><?php echo $p['phone'];?></td>
				</tr>
			<?php
			}
		?>
		</table>
		<?php
	}
}

if($_POST["type"]=="save_pac_details")
{
//bp@rr@temp@weight@hr@aps@hb@tlc@dlc@esr@pcv@blood@fbs@ppbs@rbs@b_other@urea@creat@sod@pot@cl@mg@s_other@bt@ct@pt@aptt@inr@plat@protein@alb@biliru@ldh@amyl@alkphos@choles@trigl@ldl@hdl@vldl@hbs@hiv@t3@t4@tsh@dvt@nmb@consent@consult@sent_date@sent_time@prophylaxis@drugs@invest@others@fit@
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['shed'];
	$systolic=$_POST['systolic'];
	$diastolic=$_POST['diastolic'];
	$rr=$_POST['rr'];
	$temp=$_POST['temp'];
	$weight=$_POST['weight'];
	$hr=$_POST['hr'];
	$aps=$_POST['aps'];
	$hb=$_POST['hb'];
	$tlc=$_POST['tlc'];
	$dlc=$_POST['dlc'];
	$esr=$_POST['esr'];
	$pcv=$_POST['pcv'];
	$blood=$_POST['blood'];
	$fbs=$_POST['fbs'];
	$ppbs=$_POST['ppbs'];
	$rbs=$_POST['rbs'];
	$urea=$_POST['urea'];
	$creat=$_POST['creat'];
	$sod=$_POST['sod'];
	$pot=$_POST['pot'];
	$cl=$_POST['cl'];
	$ca=$_POST['ca'];
	$mg=$_POST['mg'];
	$lab_other=$_POST['lab_other'];
	$l_other=$_POST['l_other'];
	$l_other= str_replace("'", "''", "$l_other");
	$bt=$_POST['bt'];
	$ct=$_POST['ct'];
	$pt=$_POST['pt'];
	$aptt=$_POST['aptt'];
	$inr=$_POST['inr'];
	$plat=$_POST['plat'];
	$protein=$_POST['protein'];
	$alb=$_POST['alb'];
	$biliru=$_POST['biliru'];
	$ldh=$_POST['ldh'];
	$amyl=$_POST['amyl'];
	$alkphos=$_POST['alkphos'];
	$choles=$_POST['choles'];
	$trigl=$_POST['trigl'];
	$ldl=$_POST['ldl'];
	$hdl=$_POST['hdl'];
	$vldl=$_POST['vldl'];
	$hbs=$_POST['hbs'];
	$hiv=$_POST['hiv'];
	$t3=$_POST['t3'];
	$t4=$_POST['t4'];
	$tsh=$_POST['tsh'];
	$dvt=$_POST['dvt'];
	$dvt= str_replace("'", "''", "$dvt");
	$nmb=$_POST['nmb'];
	$nmb= str_replace("'", "''", "$nmb");
	$consent=$_POST['consent'];
	$consult=$_POST['consult'];
	$consult= str_replace("'", "''", "$consult");
	$sent_date=$_POST['sent_date'];
	$sent_time=$_POST['sent_time'];
	$prophylaxis=$_POST['prophylaxis'];
	$prophylaxis= str_replace("'", "''", "$prophylaxis");
	$drugs=$_POST['drugs'];
	$drugs= str_replace("'", "''", "$drugs");
	$invest=$_POST['invest'];
	$others=$_POST['others'];
	$others= str_replace("'", "''", "$others");
	$fit=$_POST['fit'];
	$usr=$_POST['usr'];

	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_pre_anaesthesia` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ot_pre_anaesthesia` SET `systolic`='$systolic',`diastolic`='$diastolic',`rr`='$rr',`temp`='$temp',`weight`='$weight',`hr`='$hr',`aps`='$aps',`hb`='$hb',`tlc`='$tlc',`dlc`='$dlc',`esr`='$esr',`pcv`='$pcv',`fbs`='$fbs',`ppbs`='$ppbs',`rbs`='$rbs',`urea`='$urea',`creatinine`='$creat',`sodium`='$sod',`potassium`='$pot',`chlorine`='$cl',`calcium`='$ca',`magnesium`='$mg',`lab_other`='$l_other',`bt`='$bt',`ct`='$ct',`pt`='$pt',`aptt`='$aptt',`inr`='$inr',`platelets`='$plat',`protein`='$protein',`alb`='$alb',`biliru`='$biliru',`ldh`='$ldl',`amyl`='$amyl',`alk_phos`='$alkphos',`cholestrol`='$choles',`trigl`='$trigl',`ldl`='$ldl',`hdl`='$hdl',`vldl`='$vldl',`hbs`='$hbs',`hiv`='$hiv',`t3`='$t3',`t4`='$t4',`tsh`='$tsh',`dvt`='$dvt',`nmb`='$nmb',`consent`='$consent',`consult`='$consult',`sent_date`='$sent_date',`sent_time`='$sent_time',`prophylaxis`='$prophylaxis',`drugs`='$drugs',`invest`='$invest',`others`='$others',`fit`='$fit' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		mysqli_query($link,"UPDATE `patient_info` SET `blood_group`='$blood' WHERE `patient_id`='$uhid'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_pre_anaesthesia`(`patient_id`, `ipd_id`, `schedule_id`, `systolic`, `diastolic`, `rr`, `temp`, `weight`, `hr`, `aps`, `hb`, `tlc`, `dlc`, `esr`, `pcv`, `fbs`, `ppbs`, `rbs`, `urea`, `creatinine`, `sodium`, `potassium`, `chlorine`, `calcium`, `magnesium`, `lab_other`, `bt`, `ct`, `pt`, `aptt`, `inr`, `platelets`, `protein`, `alb`, `biliru`, `ldh`, `amyl`, `alk_phos`, `cholestrol`, `trigl`, `ldl`, `hdl`, `vldl`, `hbs`, `hiv`, `t3`, `t4`, `tsh`, `dvt`, `nmb`, `consent`, `consult`, `sent_date`, `sent_time`, `prophylaxis`, `drugs`, `invest`, `others`, `fit`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$shed','$systolic','$diastolic','$rr','$temp','$weight','$hr','$aps','$hb','$tlc','$dlc','$esr','$pcv','$fbs','$ppbs','$rbs','$urea','$creat','$sod','$pot','$cl','$ca','$mg','$l_other','$bt','$ct','$pt','$aptt','$inr','$plat','$protein','$alb','$biliru','$ldh','$amyl','$alkphos','$choles','$trigl','$ldl','$hdl','$vldl','$hbs','$hiv','$t3','$t4','$tsh','$dvt','$nmb','$consent','$consult','$sent_date','$sent_time','$prophylaxis','$drugs','$invest','$others','$fit','$date','$time','$usr')");
		mysqli_query($link,"UPDATE `patient_info` SET `blood_group`='$blood' WHERE `patient_id`='$uhid'");
		mysqli_query($link,"UPDATE `ot_book` SET `pac_status`='1' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	echo "Saved";
}
?>
