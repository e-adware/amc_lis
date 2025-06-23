<?php
session_start();

include("../../includes/connection.php");

$c_user=trim($_SESSION["emp_id"]);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

if(!$c_user)
{
	echo "Error";
	exit;
}

$date=date("Y-m-d");
$time=date("H:i:s");

//~ print_r($_POST);
//~ exit;

$uhid=mysqli_real_escape_string($link, $_POST["eye_patient_id"]);
$opd_id=mysqli_real_escape_string($link, $_POST["eye_opd_id"]);
$consultantdoctorid=mysqli_real_escape_string($link, $_POST["eye_consultantdoctorid"]);

$case_history=mysqli_real_escape_string($link, $_POST["case_history_eye"]);

if($emp_info["levelid"]==5) // Doctor Login
{
	$doc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$c_user'"));
	$consultantdoctorid=$doc_info["consultantdoctorid"];
}

$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));

$branch_id=$pat_reg["branch_id"];

//Case History
$patient_case_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_case_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_case_history)
{
	if(mysqli_query($link, " UPDATE `patient_case_history` SET `case_history`='$case_history' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "))
	{
		echo "Updated";
	}
	else
	{
		echo "Failed, try again later";
	}
}
else
{
	if($case_history)
	{
		if(mysqli_query($link, " INSERT INTO `patient_case_history`(`patient_id`, `opd_id`, `consultantdoctorid`, `case_history`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$case_history','$c_user','$date','$time') "))
		{
			echo "Saved";
		}
		else
		{
			echo "Failed, try again later.";
		}
	}
}

// History
$present_history=mysqli_real_escape_string($link, $_POST["present_history"]);
$past_history=mysqli_real_escape_string($link, $_POST["past_history"]);
$family_history=mysqli_real_escape_string($link, $_POST["family_history"]);
$personal_history=mysqli_real_escape_string($link, $_POST["personal_history"]);
$birth_history=mysqli_real_escape_string($link, $_POST["birth_history"]);
$nutrition_history=mysqli_real_escape_string($link, $_POST["nutrition_history"]);
$pedigree_chart=mysqli_real_escape_string($link, $_POST["pedigree_chart"]);
$surgeries_lasers=mysqli_real_escape_string($link, $_POST["surgeries_lasers"]);
$allergies=mysqli_real_escape_string($link, $_POST["allergies"]);
$nutrition_status=mysqli_real_escape_string($link, $_POST["nutrition_status"]);
$differently_able=mysqli_real_escape_string($link, $_POST["differently_able"]);
$general_examination=mysqli_real_escape_string($link, $_POST["general_examination"]);
$systemic_examination=mysqli_real_escape_string($link, $_POST["systemic_examination"]);
$psychosocial_assessment=mysqli_real_escape_string($link, $_POST["psychosocial_assessment"]);
$management_plan=mysqli_real_escape_string($link, $_POST["management_plan"]);

$patient_eye_history=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_history` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_eye_history)
{
	mysqli_query($link, "UPDATE `patient_eye_history` SET `consultantdoctorid`='$consultantdoctorid',`present_history`='$present_history',`past_history`='$past_history',`family_history`='$family_history',`personal_history`='$personal_history',`birth_history`='$birth_history',`nutrition_history`='$nutrition_history',`pedigree_chart`='$pedigree_chart',`surgeries_lasers`='$surgeries_lasers',`allergies`='$allergies',`nutrition_status`='$nutrition_status',`differently_able`='$differently_able',`general_examination`='$general_examination',`systemic_examination`='$systemic_examination',`psychosocial_assessment`='$psychosocial_assessment',`management_plan`='$management_plan' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
}
else
{
	if($present_history || $past_history || $family_history || $personal_history || $birth_history || $nutrition_history || $pedigree_chart || $surgeries_lasers || $allergies || $nutrition_status || $differently_able || $general_examination || $systemic_examination || $psychosocial_assessment || $management_plan)
	{
		mysqli_query($link, "INSERT INTO `patient_eye_history`(`patient_id`, `opd_id`, `consultantdoctorid`, `present_history`, `past_history`, `family_history`, `personal_history`, `birth_history`, `nutrition_history`, `pedigree_chart`, `surgeries_lasers`, `allergies`, `nutrition_status`, `differently_able`, `general_examination`, `systemic_examination`, `psychosocial_assessment`, `management_plan`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$present_history','$past_history','$family_history','$personal_history','$birth_history','$nutrition_history','$pedigree_chart','$surgeries_lasers','$allergies','$nutrition_status','$differently_able','$general_examination','$systemic_examination','$psychosocial_assessment','$management_plan','$c_user','$date','$time')");
	}
}

// Visual Acuity
$unaided_right_distance=mysqli_real_escape_string($link, $_POST["unaided_right_distance"]);
$unaided_right_near=mysqli_real_escape_string($link, $_POST["unaided_right_near"]);
$unaided_left_distance=mysqli_real_escape_string($link, $_POST["unaided_left_distance"]);
$unaided_left_near=mysqli_real_escape_string($link, $_POST["unaided_left_near"]);
$aided_right_distance=mysqli_real_escape_string($link, $_POST["aided_right_distance"]);
$aided_right_near=mysqli_real_escape_string($link, $_POST["aided_right_near"]);
$aided_left_distance=mysqli_real_escape_string($link, $_POST["aided_left_distance"]);
$aided_left_near=mysqli_real_escape_string($link, $_POST["aided_left_near"]);
$miscellaneous_right=mysqli_real_escape_string($link, $_POST["miscellaneous_right"]);
$miscellaneous_left=mysqli_real_escape_string($link, $_POST["miscellaneous_left"]);

$patient_eye_visual=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_visual` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_eye_visual)
{
	if(!$unaided_right_distance || !$unaided_right_near || !$unaided_left_distance || !$unaided_left_near || !$aided_right_distance || !$aided_right_near || !$aided_left_distance || !$aided_left_near || !$miscellaneous_right || !$miscellaneous_left)
	{
		mysqli_query($link, "DELETE FROM `patient_eye_visual` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
	else
	{
		mysqli_query($link, "UPDATE `patient_eye_visual` SET `consultantdoctorid`='$consultantdoctorid',`unaided_right_distance`='$unaided_right_distance',`unaided_right_near`='$unaided_right_near',`unaided_left_distance`='$unaided_left_distance',`unaided_left_near`='$unaided_left_near',`aided_right_distance`='$aided_right_distance',`aided_right_near`='$aided_right_near',`aided_left_distance`='$aided_left_distance',`aided_left_near`='$aided_left_near',`miscellaneous_right`='$miscellaneous_right',`miscellaneous_left`='$miscellaneous_left' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($unaided_right_distance || $unaided_right_near || $unaided_left_distance || $unaided_left_near || $aided_right_distance || $aided_right_near || $aided_left_distance || $aided_left_near || $miscellaneous_right || $miscellaneous_left)
	{
		mysqli_query($link, "INSERT INTO `patient_eye_visual`(`patient_id`, `opd_id`, `consultantdoctorid`, `unaided_right_distance`, `unaided_right_near`, `unaided_left_distance`, `unaided_left_near`, `aided_right_distance`, `aided_right_near`, `aided_left_distance`, `aided_left_near`, `miscellaneous_right`, `miscellaneous_left`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$unaided_right_distance','$unaided_right_near','$unaided_left_distance','$unaided_left_near','$aided_right_distance','$aided_right_near','$aided_left_distance','$aided_left_near','$miscellaneous_right','$miscellaneous_left','$c_user','$date','$time')");
	}
}

// AUTO REFRACTOMETER
$fractometer_right_sph=mysqli_real_escape_string($link, $_POST["fractometer_right_sph"]);
$fractometer_right_cyl=mysqli_real_escape_string($link, $_POST["fractometer_right_cyl"]);
$fractometer_right_axis=mysqli_real_escape_string($link, $_POST["fractometer_right_axis"]);
$fractometer_left_sph=mysqli_real_escape_string($link, $_POST["fractometer_left_sph"]);
$fractometer_left_cyl=mysqli_real_escape_string($link, $_POST["fractometer_left_cyl"]);
$fractometer_left_axis=mysqli_real_escape_string($link, $_POST["fractometer_left_axis"]);

$patient_eye_fractometer=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_fractometer` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_eye_fractometer)
{
	if(!$fractometer_right_sph || !$fractometer_right_cyl || !$fractometer_right_axis || !$fractometer_left_sph || !$fractometer_left_cyl || !$fractometer_left_axis)
	{
		mysqli_query($link, "DELETE FROM `patient_eye_fractometer` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
	else
	{
		mysqli_query($link, "UPDATE `patient_eye_fractometer` SET `consultantdoctorid`='$consultantdoctorid',`fractometer_right_sph`='$fractometer_right_sph',`fractometer_right_cyl`='$fractometer_right_cyl',`fractometer_right_axis`='$fractometer_right_axis',`fractometer_left_sph`='$fractometer_left_sph',`fractometer_left_cyl`='$fractometer_left_cyl',`fractometer_left_axis`='$fractometer_left_axis' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($fractometer_right_sph || $fractometer_right_cyl || $fractometer_right_axis || $fractometer_left_sph || $fractometer_left_cyl || $fractometer_left_axis)
	{
		mysqli_query($link, "INSERT INTO `patient_eye_fractometer`(`patient_id`, `opd_id`, `consultantdoctorid`, `fractometer_right_sph`, `fractometer_right_cyl`, `fractometer_right_axis`, `fractometer_left_sph`, `fractometer_left_cyl`, `fractometer_left_axis`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$fractometer_right_sph','$fractometer_right_cyl','$fractometer_right_axis','$fractometer_left_sph','$fractometer_left_cyl','$fractometer_left_axis','$c_user','$date','$time')");
	}
}

// Present Power
$present_power_right=mysqli_real_escape_string($link, $_POST["present_power_right"]);
$present_power_left=mysqli_real_escape_string($link, $_POST["present_power_left"]);

$patient_eye_present_power=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_present_power` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_eye_present_power)
{
	if(!$present_power_right || !$present_power_left)
	{
		mysqli_query($link, "DELETE FROM `patient_eye_present_power` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
	else
	{
		mysqli_query($link, "UPDATE `patient_eye_present_power` SET `consultantdoctorid`='$consultantdoctorid',`present_power_right`='$present_power_right',`present_power_left`='$present_power_left' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($present_power_right || $present_power_left)
	{
		mysqli_query($link, "INSERT INTO `patient_eye_present_power`(`patient_id`, `opd_id`, `consultantdoctorid`, `present_power_right`, `present_power_left`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$present_power_right','$present_power_left','$c_user','$date','$time')");
	}
}


// Prescription For Glasses
$distance_right_sph=mysqli_real_escape_string($link, $_POST["distance_right_sph"]);
$distance_right_cyl=mysqli_real_escape_string($link, $_POST["distance_right_cyl"]);
$distance_right_axis=mysqli_real_escape_string($link, $_POST["distance_right_axis"]);
$distance_right_vision=mysqli_real_escape_string($link, $_POST["distance_right_vision"]);
$distance_left_sph=mysqli_real_escape_string($link, $_POST["distance_left_sph"]);
$distance_left_cyl=mysqli_real_escape_string($link, $_POST["distance_left_cyl"]);
$distance_left_axis=mysqli_real_escape_string($link, $_POST["distance_left_axis"]);
$distance_left_vision=mysqli_real_escape_string($link, $_POST["distance_left_vision"]);

$near_right_sph=mysqli_real_escape_string($link, $_POST["near_right_sph"]);
$near_right_cyl=mysqli_real_escape_string($link, $_POST["near_right_cyl"]);
$near_right_axis=mysqli_real_escape_string($link, $_POST["near_right_axis"]);
$near_right_vision=mysqli_real_escape_string($link, $_POST["near_right_vision"]);
$near_left_sph=mysqli_real_escape_string($link, $_POST["near_left_sph"]);
$near_left_cyl=mysqli_real_escape_string($link, $_POST["near_left_cyl"]);
$near_left_axis=mysqli_real_escape_string($link, $_POST["near_left_axis"]);
$near_left_vision=mysqli_real_escape_string($link, $_POST["near_left_vision"]);

$pupillary_distance=mysqli_real_escape_string($link, $_POST["pupillary_distance"]);
$power_remarks=mysqli_real_escape_string($link, $_POST["power_remarks"]);
$power_refraction=mysqli_real_escape_string($link, $_POST["power_refraction"]);

$patient_eye_prescribe_power=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_prescribe_power` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_eye_prescribe_power)
{
	if(!$distance_right_sph || !$distance_right_cyl || !$distance_right_axis || !$distance_right_vision || !$distance_left_sph || !$distance_left_cyl || !$distance_left_axis || !$distance_left_vision || !$near_right_sph || !$near_right_cyl || !$near_right_axis || !$near_right_vision || !$near_left_sph || !$near_left_cyl || !$near_left_axis || !$near_left_vision || !$pupillary_distance || !$power_remarks || !$power_refraction)
	{
		mysqli_query($link, "DELETE FROM `patient_eye_prescribe_power` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
	else
	{
		mysqli_query($link, "UPDATE `patient_eye_prescribe_power` SET `consultantdoctorid`='$consultantdoctorid',`distance_right_sph`='$distance_right_sph',`distance_right_cyl`='$distance_right_cyl',`distance_right_axis`='$distance_right_axis',`distance_right_vision`='$distance_right_vision',`distance_left_sph`='$distance_left_sph',`distance_left_cyl`='$distance_left_cyl',`distance_left_axis`='$distance_left_axis',`distance_left_vision`='$distance_left_vision',`near_right_sph`='$near_right_sph',`near_right_cyl`='$near_right_cyl',`near_right_axis`='$near_right_axis',`near_right_vision`='$near_right_vision',`near_left_sph`='$near_left_sph',`near_left_cyl`='$near_left_cyl',`near_left_axis`='$near_left_axis',`near_left_vision`='$near_left_vision',`pupillary_distance`='$pupillary_distance',`power_remarks`='$power_remarks',`power_refraction`='$power_refraction' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($distance_right_sph || $distance_right_cyl || $distance_right_axis || $distance_right_vision || $distance_left_sph || $distance_left_cyl || $distance_left_axis || $distance_left_vision || $near_right_sph || $near_right_cyl || $near_right_axis || $near_right_vision || $near_left_sph || $near_left_cyl || $near_left_axis || $near_left_vision || $pupillary_distance || $power_remarks || $power_refraction)
	{
		mysqli_query($link, "INSERT INTO `patient_eye_prescribe_power`(`patient_id`, `opd_id`, `consultantdoctorid`, `distance_right_sph`, `distance_right_cyl`, `distance_right_axis`, `distance_right_vision`, `distance_left_sph`, `distance_left_cyl`, `distance_left_axis`, `distance_left_vision`, `near_right_sph`, `near_right_cyl`, `near_right_axis`, `near_right_vision`, `near_left_sph`, `near_left_cyl`, `near_left_axis`, `near_left_vision`, `pupillary_distance`, `power_remarks`, `power_refraction`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$distance_right_sph','$distance_right_cyl','$distance_right_axis','$distance_right_vision','$distance_left_sph','$distance_left_cyl','$distance_left_axis','$distance_left_vision','$near_right_sph','$near_right_cyl','$near_right_axis','$near_right_vision','$near_left_sph','$near_left_cyl','$near_left_axis','$near_left_vision','$pupillary_distance','$power_remarks','$power_refraction','$c_user','$date','$time')");
	}
}

// External Examination
$facial_symmetry=mysqli_real_escape_string($link, $_POST["facial_symmetry"]);
$external_face=mysqli_real_escape_string($link, $_POST["external_face"]);
$head_posture=mysqli_real_escape_string($link, $_POST["head_posture"]);
$ocular_position=mysqli_real_escape_string($link, $_POST["ocular_position"]);
$ocular_alignment=mysqli_real_escape_string($link, $_POST["ocular_alignment"]);
$ocular_motility_right=mysqli_real_escape_string($link, $_POST["ocular_motility_right"]);
$ocular_motility_left=mysqli_real_escape_string($link, $_POST["ocular_motility_left"]);
$intraocular_pressure_right=mysqli_real_escape_string($link, $_POST["intraocular_pressure_right"]);
$intraocular_pressure_left=mysqli_real_escape_string($link, $_POST["intraocular_pressure_left"]);

$patient_eye_external_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_external_exam` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_eye_external_exam)
{
	mysqli_query($link, "UPDATE `patient_eye_external_exam` SET `consultantdoctorid`='$consultantdoctorid',`facial_symmetry`='$facial_symmetry',`external_face`='$external_face',`head_posture`='$head_posture',`ocular_position`='$ocular_position',`ocular_alignment`='$ocular_alignment',`ocular_motility_right`='$ocular_motility_right',`ocular_motility_left`='$ocular_motility_left',`intraocular_pressure_right`='$intraocular_pressure_right',`intraocular_pressure_left`='$intraocular_pressure_left' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
}
else
{
	if($facial_symmetry || $external_face || $head_posture || $ocular_position || $ocular_alignment || $ocular_motility_right || $ocular_motility_left || $intraocular_pressure_right || $intraocular_pressure_left)
	{
		mysqli_query($link, "INSERT INTO `patient_eye_external_exam`(`patient_id`, `opd_id`, `consultantdoctorid`, `facial_symmetry`, `external_face`, `head_posture`, `ocular_position`, `ocular_alignment`, `ocular_motility_right`, `ocular_motility_left`, `intraocular_pressure_right`, `intraocular_pressure_left`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$facial_symmetry','$external_face','$head_posture','$ocular_position','$ocular_alignment','$ocular_motility_right','$ocular_motility_left','$intraocular_pressure_right','$intraocular_pressure_left','$c_user','$date','$time')");
	}
}


// Slit Lamp Examination & Diagnosis
$eyelids_right=mysqli_real_escape_string($link, $_POST["eyelids_right"]);
$eyelids_left=mysqli_real_escape_string($link, $_POST["eyelids_left"]);
$conjunctiva_right=mysqli_real_escape_string($link, $_POST["conjunctiva_right"]);
$conjunctiva_left=mysqli_real_escape_string($link, $_POST["conjunctiva_left"]);
$sclera_right=mysqli_real_escape_string($link, $_POST["sclera_right"]);
$sclera_left=mysqli_real_escape_string($link, $_POST["sclera_left"]);
$cornea_right=mysqli_real_escape_string($link, $_POST["cornea_right"]);
$cornea_left=mysqli_real_escape_string($link, $_POST["cornea_left"]);
$interior_chamber_right=mysqli_real_escape_string($link, $_POST["interior_chamber_right"]);
$interior_chamber_left=mysqli_real_escape_string($link, $_POST["interior_chamber_left"]);
$iris_right=mysqli_real_escape_string($link, $_POST["iris_right"]);
$iris_left=mysqli_real_escape_string($link, $_POST["iris_left"]);
$pupil_right=mysqli_real_escape_string($link, $_POST["pupil_right"]);
$pupil_left=mysqli_real_escape_string($link, $_POST["pupil_left"]);
$diagnosis_right=mysqli_real_escape_string($link, $_POST["diagnosis_right"]);
$diagnosis_left=mysqli_real_escape_string($link, $_POST["diagnosis_left"]);

$patient_eye_lamp_exam=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_eye_lamp_exam` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_eye_lamp_exam)
{
	mysqli_query($link, "UPDATE `patient_eye_lamp_exam` SET `consultantdoctorid`='$consultantdoctorid',`eyelids_right`='$eyelids_right',`eyelids_left`='$eyelids_left',`conjunctiva_right`='$conjunctiva_right',`conjunctiva_left`='$conjunctiva_left',`sclera_right`='$sclera_right',`sclera_left`='$sclera_left',`cornea_right`='$cornea_right',`cornea_left`='$cornea_left',`interior_chamber_right`='$interior_chamber_right',`interior_chamber_left`='$interior_chamber_left',`iris_right`='$iris_right',`iris_left`='$iris_left',`pupil_right`='$pupil_right',`pupil_left`='$pupil_left',`diagnosis_right`='$diagnosis_right',`diagnosis_left`='$diagnosis_left' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
}
else
{
	if($eyelids_right || $eyelids_left || $conjunctiva_right || $conjunctiva_left || $sclera_right || $sclera_left || $cornea_right || $cornea_left || $interior_chamber_right || $interior_chamber_left || $iris_right || $iris_left || $pupil_right || $pupil_left || $diagnosis_right || $diagnosis_left)
	{
		mysqli_query($link, "INSERT INTO `patient_eye_lamp_exam`(`patient_id`, `opd_id`, `consultantdoctorid`, `eyelids_right`, `eyelids_left`, `conjunctiva_right`, `conjunctiva_left`, `sclera_right`, `sclera_left`, `cornea_right`, `cornea_left`, `interior_chamber_right`, `interior_chamber_left`, `iris_right`, `iris_left`, `pupil_right`, `pupil_left`, `diagnosis_right`, `diagnosis_left`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$eyelids_right','$eyelids_left','$conjunctiva_right','$conjunctiva_left','$sclera_right','$sclera_left','$cornea_right','$cornea_left','$interior_chamber_right','$interior_chamber_left','$iris_right','$iris_left','$pupil_right','$pupil_left','$diagnosis_right','$diagnosis_left','$c_user','$date','$time')");
	}
}

?>
