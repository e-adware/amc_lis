<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

if(!$c_user)
{
	echo "Error";
}

$date=date("Y-m-d");
$time=date("H:i:s");

//print_r($_POST);

$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
$ipd_id=mysqli_real_escape_string($link, $_POST["ipd_id"]);

$admission_reason=mysqli_real_escape_string($link, $_POST["admission_reason"]);
$case_history=mysqli_real_escape_string($link, $_POST["case_history"]);
$examination=mysqli_real_escape_string($link, $_POST["examination"]);
$procedure_performed=mysqli_real_escape_string($link, $_POST["procedure_performed"]);
$course_hospital=mysqli_real_escape_string($link, $_POST["course_hospital"]);
$final_diagnosis=mysqli_real_escape_string($link, $_POST["final_diagnosis"]);
$discharge_instruction=mysqli_real_escape_string($link, $_POST["discharge_instruction"]);
$hospital_report=mysqli_real_escape_string($link, $_POST["hospital_report"]);

//Vitals
$weight=mysqli_real_escape_string($link, $_POST["weight"]);
$height=mysqli_real_escape_string($link, $_POST["height"]);
$BMI_1=mysqli_real_escape_string($link, $_POST["BMI_1"]);
$BMI_2=mysqli_real_escape_string($link, $_POST["BMI_2"]);
$temp=mysqli_real_escape_string($link, $_POST["temp"]);
$pulse=mysqli_real_escape_string($link, $_POST["pulse"]);
$spo2=mysqli_real_escape_string($link, $_POST["spo2"]);
$systolic=mysqli_real_escape_string($link, $_POST["systolic"]);
$diastolic=mysqli_real_escape_string($link, $_POST["diastolic"]);
$RR=mysqli_real_escape_string($link, $_POST["RR"]);
$fbs=mysqli_real_escape_string($link, $_POST["fbs"]);
$rbs=mysqli_real_escape_string($link, $_POST["rbs"]);

$revisit_id=mysqli_real_escape_string($link, $_POST["revisit_id"]);
$discharge_id=mysqli_real_escape_string($link, $_POST["discharge_id"]);
$consultantdoctorid=mysqli_real_escape_string($link, $_POST["discharge_doc_id"]);
$emergency_contact=mysqli_real_escape_string($link, $_POST["emergency_contact"]);

$booked=mysqli_real_escape_string($link, $_POST["booked"]);
if(!$booked){ $booked=0; }
$unbooked=mysqli_real_escape_string($link, $_POST["unbooked"]);
if(!$unbooked){ $unbooked=0; }
$booked_elsewhere=mysqli_real_escape_string($link, $_POST["booked_elsewhere"]);
if(!$booked_elsewhere){ $booked_elsewhere=0; }
$gravida=mysqli_real_escape_string($link, $_POST["gravida"]);
$para=mysqli_real_escape_string($link, $_POST["para"]);
$live=mysqli_real_escape_string($link, $_POST["live"]);
$abortion=mysqli_real_escape_string($link, $_POST["abortion"]);
$risk_factor=mysqli_real_escape_string($link, $_POST["risk_factor"]);
$antenatal_complications=mysqli_real_escape_string($link, $_POST["antenatal_complications"]);
$baby_all=mysqli_real_escape_string($link, $_POST["baby_all"]);


$last_menstrual_period=mysqli_real_escape_string($link, $_POST["last_menstrual_period"]);
$est_delivery_date=mysqli_real_escape_string($link, $_POST["est_delivery_date"]);
$gestational_age=mysqli_real_escape_string($link, $_POST["gestational_age"]);
$gestational_age_usg=mysqli_real_escape_string($link, $_POST["gestational_age_usg"]);
$fundal_height=mysqli_real_escape_string($link, $_POST["fundal_height"]);
$presentation=mysqli_real_escape_string($link, $_POST["presentation"]);
$fetal_heart_rate=mysqli_real_escape_string($link, $_POST["fetal_heart_rate"]);

$death_date=mysqli_real_escape_string($link, $_POST["death_date"]);
$death_time=mysqli_real_escape_string($link, $_POST["death_time"]);
$death_cause=mysqli_real_escape_string($link, $_POST["death_cause"]);

$save_num=0;

$patient_discharge_summary=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
if($patient_discharge_summary)
{
	if(mysqli_query($link, "UPDATE `patient_discharge_summary` SET `admission_reason`='$admission_reason',`case_history`='$case_history',`examination`='$examination',`procedure_performed`='$procedure_performed',`course_hospital`='$course_hospital',`final_diagnosis`='$final_diagnosis',`discharge_instruction`='$discharge_instruction',`hospital_report`='$hospital_report',`revisit_id`='$revisit_id',`discharge_id`='$discharge_id',`consultantdoctorid`='$consultantdoctorid',`emergency_contact`='$emergency_contact' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id'"))
	{
		$save_num=1;
	}
	else
	{
		$save_num=0;
	}
}
else
{
	if(mysqli_query($link, "INSERT INTO `patient_discharge_summary`(`patient_id`, `ipd_id`, `admission_reason`, `case_history`, `examination`, `procedure_performed`, `course_hospital`, `final_diagnosis`, `discharge_instruction`, `hospital_report`, `revisit_id`, `discharge_id`, `consultantdoctorid`, `emergency_contact`, `user`, `date`, `time`) VALUES ('$uhid','$ipd_id','$admission_reason','$case_history','$examination','$procedure_performed','$course_hospital','$final_diagnosis','$discharge_instruction','$hospital_report','$revisit_id','$discharge_id','$consultantdoctorid','$emergency_contact','$c_user','$date','$time')"))
	{
		$save_num=2;
	}
	else
	{
		$save_num=0;
	}
}

if($revisit_id!="")
{
	mysqli_query($link, "INSERT INTO `revisit_master`(`revisit_val`, `revisit_name`, `status`) VALUES ('0','$revisit_id','0')");
}

$patient_discharge_summary_obs=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary_obs` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
if($patient_discharge_summary_obs)
{
	if(mysqli_query($link, "UPDATE `patient_discharge_summary_obs` SET `booked`='$booked',`unbooked`='$unbooked',`booked_elsewhere`='$booked_elsewhere',`gravida`='$gravida',`para`='$para',`live`='$live',`abortion`='$abortion',`risk_factor`='$risk_factor',`antenatal_complications`='$antenatal_complications' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id'"))
	{
		$save_num=1;
	}
	else
	{
		$save_num=0;
	}
}
else
{
	if($booked || $unbooked || $booked_elsewhere || $gravida || $para || $live || $abortion || $risk_factor || $antenatal_complications)
	{
		if(mysqli_query($link, "INSERT INTO `patient_discharge_summary_obs`(`patient_id`, `ipd_id`, `booked`, `unbooked`, `booked_elsewhere`, `gravida`, `para`, `live`, `abortion`, `risk_factor`, `antenatal_complications`, `user`, `date`, `time`) VALUES ('$uhid','$ipd_id','$booked','$unbooked','$booked_elsewhere','$gravida','$para','$live','$abortion','$risk_factor','$antenatal_complications','$c_user','$date','$time')"))
		{
			$save_num=3;
		}
		else
		{
			$save_num=0;
		}
	}
}

if($baby_all)
{
	mysqli_query($link, " DELETE FROM `patient_discharge_summary_baby` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id'");
	
	$baby_alls=explode("@#@",$baby_all);
	foreach($baby_alls AS $each_baby)
	{
		if($each_baby)
		{
			$each_babys=explode("@@",$each_baby);
			
			$live_birth=$each_babys[0];
			$fresh_birth=$each_babys[1];
			$macerated_birth=$each_babys[2];
			$term=$each_babys[3];
			$preterm=$each_babys[4];
			$iugr=$each_babys[5];
			$baby_uhid=$each_babys[6];
			$baby_ipd_id=$each_babys[7];
			
			if($live_birth || $fresh_birth || $macerated_birth || $term || $preterm || $iugr)
			{
				mysqli_query($link, "INSERT INTO `patient_discharge_summary_baby`(`patient_id`, `ipd_id`, `baby_uhid`, `baby_ipd_id`, `live_birth`, `fresh_birth`, `macerated_birth`, `term`, `preterm`, `iugr`, `user`, `date`, `time`) VALUES ('$uhid','$ipd_id','$baby_uhid','$baby_ipd_id','$live_birth','$fresh_birth','$macerated_birth','$term','$preterm','$iugr','$c_user','$date','$time')");
			}
		}
	}
	
}

// Vitals
$patient_vitals=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_vitals)
{
	if(mysqli_query($link, "UPDATE `pat_vital` SET `weight`='$weight',`height`='$height',`medium_circumference`='$medium_circumference',`BMI_1`='$BMI_1',`BMI_2`='$BMI_2',`spo2`='$spo2',`pulse`='$pulse',`head_circumference`='$head_circumference',`PR`='$PR',`RR`='$RR',`temp`='$temp',`systolic`='$systolic',`diastolic`='$diastolic',`rbs`='$rbs',`note`='$note' WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id'"))
	{
		//$save_num++;
	}
	else
	{
		//$save_num=0;
	}
}
else
{
	if($weight || $height || $spo2 || $pulse || $RR || $temp || $systolic || $diastolic || $fbs || $rbs)
	{
		if(mysqli_query($link, "INSERT INTO `pat_vital`(`patient_id`, `opd_id`, `consultantdoctorid`, `weight`, `height`, `medium_circumference`, `BMI_1`, `BMI_2`, `spo2`, `pulse`, `head_circumference`, `PR`, `RR`, `temp`, `systolic`, `diastolic`, `fbs`, `rbs`, `note`, `date`, `time`, `user`) VALUES ('$uhid','$ipd_id','$consultantdoctorid','$weight','$height','$medium_circumference','$BMI_1','$BMI_2','$spo2','$pulse','$head_circumference','$PR','$RR','$temp','$systolic','$diastolic','$fbs','$rbs','$note','$date','$time','$c_user')"))
		{
			//$save_num++;
		}
		else
		{
			//$save_num=0;
		}
	}
}

$patient_antenatal_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_antenatal_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id' "));

if($patient_antenatal_detail)
{
	if(mysqli_query($link, "UPDATE `patient_antenatal_detail` SET `last_menstrual_period`='$last_menstrual_period',`est_delivery_date`='$est_delivery_date',`gestational_age`='$gestational_age',`gestational_age_usg`='$gestational_age_usg',`fundal_height`='$fundal_height',`presentation`='$presentation',`fetal_heart_rate`='$fetal_heart_rate' WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id'"))
	{
		//$save_num++;
	}
	else
	{
		//$save_num=0;
	}
}
else
{
	if($last_menstrual_period || $gestational_age_usg || $fundal_height || $presentation || $fetal_heart_rate)
	{
		if(mysqli_query($link, "INSERT INTO `patient_antenatal_detail`(`patient_id`, `opd_id`, `consultantdoctorid`, `last_menstrual_period`, `est_delivery_date`, `gestational_age`, `gestational_age_usg`, `fundal_height`, `presentation`, `fetal_heart_rate`, `date`, `time`, `user`) VALUES ('$uhid','$ipd_id','$consultantdoctorid','$last_menstrual_period','$est_delivery_date','$gestational_age','$gestational_age_usg','$fundal_height','$presentation','$fetal_heart_rate','$date','$time','$c_user')"))
		{
			//$save_num++;
		}
		else
		{
			//$save_num=0;
		}
	}
}

if($discharge_id==105) // Death
{
	if($death_date=="" || $death_date=="0000-00-00")
	{
		$death_date="0000-00-00";
	}
	else
	{
		$death_date=date("Y-m-d",strtotime($death_date));
	}
	if($death_time=="" || $death_time=="00:00:00")
	{
		$death_time="00:00:00";
	}
	{
		$death_time=date("H:i:s",strtotime($death_time));
	}
	
	$ipd_pat_death_details=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_death_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
	if($ipd_pat_death_details)
	{
		mysqli_query($link, "UPDATE `ipd_pat_death_details` SET `type`='$discharge_id',`diagnosed_by`='$consultantdoctorid',`death_date`='$death_date',`death_time`='$death_time',`death_cause`='$death_cause' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id'");
	}
	else
	{
		mysqli_query($link, "INSERT INTO `ipd_pat_death_details`(`patient_id`, `ipd_id`, `type`, `diagnosed_by`, `death_date`, `death_time`, `death_cause`, `date`, `time`, `user`) VALUES ('$uhid','$ipd_id','$discharge_id','$consultantdoctorid','$death_date','$death_time','$death_cause','$date','$time','$c_user')");
	}
}
else
{
	mysqli_query($link, "DELETE FROM `ipd_pat_death_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id'");
}

if($save_num==0)
{
	echo "Failed, try again later.";
}
else if($save_num==1)
{
	echo "Updated";
}
else
{
	echo "Saved";
}
?>
