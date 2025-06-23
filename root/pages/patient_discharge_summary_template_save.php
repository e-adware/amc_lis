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

$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid`,`dept_id` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
if($doc_info)
{
	$consultantdoctorid=$doc_info["consultantdoctorid"];
	$dept_id=$doc_info["dept_id"];
}
else
{
	$consultantdoctorid=0;
	$dept_id=0;
}


//print_r($_POST);

$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
$ipd_id=mysqli_real_escape_string($link, $_POST["ipd_id"]);

$template_id=mysqli_real_escape_string($link, $_POST["template_id"]);
$template_name=mysqli_real_escape_string($link, $_POST["template_name"]);

$admission_reason=mysqli_real_escape_string($link, $_POST["admission_reason"]);
$case_history=mysqli_real_escape_string($link, $_POST["case_history"]);
$examination=mysqli_real_escape_string($link, $_POST["examination"]);
$procedure_performed=mysqli_real_escape_string($link, $_POST["procedure_performed"]);
$course_hospital=mysqli_real_escape_string($link, $_POST["course_hospital"]);
$final_diagnosis=mysqli_real_escape_string($link, $_POST["final_diagnosis"]);
$discharge_instruction=mysqli_real_escape_string($link, $_POST["discharge_instruction"]);
$hospital_report=mysqli_real_escape_string($link, $_POST["hospital_report"]);

$risk_factor=mysqli_real_escape_string($link, $_POST["risk_factor"]);
$antenatal_complications=mysqli_real_escape_string($link, $_POST["antenatal_complications"]);

$save_num=0;

$patient_discharge_summary=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_discharge_summary_template` WHERE `template_id`='$template_id' "));
if($patient_discharge_summary)
{
	if(mysqli_query($link, "UPDATE `patient_discharge_summary_template` SET `template_name`='$template_name',`dept_id`='$dept_id',`consultantdoctorid`='$consultantdoctorid',`admission_reason`='$admission_reason',`case_history`='$case_history',`examination`='$examination',`procedure_performed`='$procedure_performed',`course_hospital`='$course_hospital',`final_diagnosis`='$final_diagnosis',`discharge_instruction`='$discharge_instruction',`hospital_report`='$hospital_report',`risk_factor`='$risk_factor',`antenatal_complications`='$antenatal_complications' WHERE `template_id`='$template_id'"))
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
	if(mysqli_query($link, "INSERT INTO `patient_discharge_summary_template`(`template_name`, `dept_id`, `consultantdoctorid`, `admission_reason`, `case_history`, `examination`, `procedure_performed`, `course_hospital`, `final_diagnosis`, `discharge_instruction`, `hospital_report`, `risk_factor`, `antenatal_complications`, `user`, `date`, `time`) VALUES ('$template_name','$dept_id','$consultantdoctorid','$admission_reason','$case_history','$examination','$procedure_performed','$course_hospital','$final_diagnosis','$discharge_instruction','$hospital_report','$risk_factor','$antenatal_complications','$c_user','$date','$time')"))
	{
		$save_num=2;
	}
	else
	{
		$save_num=0;
	}
}

if($save_num==0)
{
	echo "404@Failed, try again later.";
}
else if($save_num==1)
{
	echo "101@Updated";
}
else
{
	echo "101@Saved";
}
?>
