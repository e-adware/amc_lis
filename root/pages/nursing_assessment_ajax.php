<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");
$type=$_POST['type'];

if($type=="search_patient_list_ipd")
{
	$ward=$_POST['ward'];
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	
	$q="SELECT `patient_id`,`opd_id` FROM `uhid_and_opdid` WHERE `type`='1' AND `opd_id` IN (SELECT `opd_id` FROM `appointment_book` WHERE `appointment_date`='$date') AND `opd_id` IN (SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `balance`='0.00') ORDER BY `date` DESC LIMIT 0,20";
	if($uhid)
	{
		$q="SELECT `patient_id`,`opd_id` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `type`='1' AND `opd_id` IN (SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `balance`='0.00') ORDER BY `date` DESC LIMIT 0,20";
	}
	if($ipd)
	{
		$q="SELECT `patient_id`,`opd_id` FROM `uhid_and_opdid` WHERE `opd_id`='$ipd' AND `type`='1' AND `opd_id` IN (SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `balance`='0.00') ORDER BY `date` DESC LIMIT 0,20";
	}
	if($name)
	{
		$q="SELECT `patient_id`,`opd_id` FROM `uhid_and_opdid` WHERE `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%') AND `type`='1' AND `opd_id` IN (SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `balance`='0.00') ORDER BY `date` DESC LIMIT 0,20";
	}
	if($dat)
	{
		$q="SELECT `patient_id`,`opd_id` FROM `uhid_and_opdid` WHERE `opd_id` IN (SELECT `opd_id` FROM `appointment_book` WHERE `appointment_date`='$dat') AND `opd_id` IN (SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `balance`='0.00') ORDER BY `date` DESC LIMIT 0,20";
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
				<th>Doctor</th>
			</tr>
		<?php
			while($r=mysqli_fetch_array($qq))
			{
				$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
				$d=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]')"));
				if($p["dob"]!="")
				{ $age=age_calculator($p["dob"])." (".$p["dob"].")"; }
				else
				{ $age=$p["age"]." ".$p["age_type"]; }
				$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]'"));
				if($nn>0)
				{
					$bg="background:#AFD5BE;";
					if($uhid || $ipd || $name || $dat)
					$a="1";
					else
					$a="";
				}
				else
				{$bg="";$a="1";}
				if($a)
				{
			?>
				<tr onclick="redirect_page('<?php echo $r['patient_id'];?>','<?php echo $r['opd_id'];?>')" style="cursor:pointer;color:#333333;<?php echo $bg;?>">
					<td><?php echo $p['patient_id'];?></td>
					<td><?php echo $r['opd_id'];?></td>
					<td><?php echo $p['name'];?></td>
					<td><?php echo $p['sex'];?></td>
					<td><?php echo $age;?></td>
					<td><?php echo $d['Name'];?></td>
				</tr>
			<?php
				}
			}
		?>
		</table>
		<?php
	}
}

if($type=="pat_opd_vital_save")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$weight=$_POST['weight'];
	$height=$_POST['height'];
	$mid_cum=$_POST['mid_cum'];
	$hd_cum=$_POST['hd_cum'];
	$bmi1=$_POST['bmi1'];
	$bmi2=$_POST['bmi2'];
	$spo=$_POST['spo'];
	$pulse=$_POST['pulse'];
	$temp=$_POST['temp'];
	$pr=$_POST['pr'];
	$rr=$_POST['rr'];
	$systolic=$_POST['systolic'];
	$diastolic=$_POST['diastolic'];
	$vit_note=$_POST['vit_note'];
	$vit_note=str_replace("'", "''", "$vit_note");
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `pat_vital` SET `weight`='$weight',`height`='$height',`medium_circumference`='$mid_cum',`BMI_1`='$bmi1',`BMI_2`='$bmi2',`spo2`='$spo',`pulse`='$pulse',`head_circumference`='$hd_cum',`PR`='$pr',`RR`='$rr',`temp`='$temp',`systolic`='$systolic',`diastolic`='$diastolic',`note`='$vit_note',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
		echo "Saved";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `pat_vital`(`patient_id`, `opd_id`, `weight`, `height`, `medium_circumference`, `BMI_1`, `BMI_2`, `spo2`, `pulse`, `head_circumference`, `PR`, `RR`, `temp`, `systolic`, `diastolic`, `note`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$weight','$height','$mid_cum','$bmi1','$bmi2','$spo','$pulse','$hd_cum','$pr','$rr','$temp','$systolic','$diastolic','$vit_note','$date','$time','$usr')");
		echo "Saved";
	}
}

if($type=="oo")
{
	
}

?>
