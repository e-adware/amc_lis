<?php
session_start();

include("../../includes/connection.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

if(!$c_user)
{
	echo "Error";
	exit;
}

$date=date("Y-m-d");
$time=date("H:i:s");

//print_r($_POST);

$uhid=mysqli_real_escape_string($link, $_POST["pac_patient_id"]);
$opd_id=mysqli_real_escape_string($link, $_POST["pac_opd_id"]);
$consultantdoctorid=mysqli_real_escape_string($link, $_POST["pac_consultantdoctorid"]);

$propose_procedure=mysqli_real_escape_string($link, $_POST["propose_procedure"]);
$previous_surgeries=mysqli_real_escape_string($link, $_POST["previous_surgeries"]);
$prescribed_medication=mysqli_real_escape_string($link, $_POST["prescribed_medication"]);
$blood_thinner=mysqli_real_escape_string($link, $_POST["blood_thinner"]);
$food_allergies=mysqli_real_escape_string($link, $_POST["food_allergies"]);
$advice=mysqli_real_escape_string($link, $_POST["advice"]);
$final_plan=mysqli_real_escape_string($link, $_POST["final_plan"]);

//if(!$blood_thinner){ $blood_thinner=0; }

$patient_pac_details=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details)
{
	mysqli_query($link, "UPDATE `patient_pac_details` SET `consultantdoctorid`='$consultantdoctorid',`propose_procedure`='$propose_procedure',`previous_surgeries`='$previous_surgeries',`prescribed_medication`='$prescribed_medication',`blood_thinner`='$blood_thinner',`food_allergies`='$food_allergies',`advice`='$advice',`final_plan`='$final_plan' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
}
else
{
	if($propose_procedure || $previous_surgeries || $prescribed_medication || $blood_thinner || $food_allergies || $advice || $final_plan)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details`(`patient_id`, `opd_id`, `consultantdoctorid`, `propose_procedure`, `previous_surgeries`, `prescribed_medication`, `blood_thinner`, `food_allergies`, `advice`, `final_plan`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$propose_procedure','$previous_surgeries','$prescribed_medication','$blood_thinner','$food_allergies','$advice','$final_plan','$c_user','$date','$time')");
	}
}


// Cardiovascular Disease
$chest_pain=mysqli_real_escape_string($link, $_POST["chest_pain"]);
if(!$chest_pain){ $chest_pain=0; }
$irregu_hear_beat=mysqli_real_escape_string($link, $_POST["irregu_hear_beat"]);
if(!$irregu_hear_beat){ $irregu_hear_beat=0; }
$pacemeaker=mysqli_real_escape_string($link, $_POST["pacemeaker"]);
if(!$pacemeaker){ $pacemeaker=0; }
$pacemeaker_brand=mysqli_real_escape_string($link, $_POST["pacemeaker_brand"]);
if($pacemeaker_brand){ $pacemeaker=1; }
$problem_circulation=mysqli_real_escape_string($link, $_POST["problem_circulation"]);
if(!$problem_circulation){ $problem_circulation=0; }
$blood_clot=mysqli_real_escape_string($link, $_POST["blood_clot"]);
if(!$blood_clot){ $blood_clot=0; }
$high_bp=mysqli_real_escape_string($link, $_POST["high_bp"]);
if(!$high_bp){ $high_bp=0; }

$cd_other_details=mysqli_real_escape_string($link, trim($_POST["cd_other_details"]));

$patient_pac_details_cd=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_cd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_cd)
{
	mysqli_query($link, "UPDATE `patient_pac_details_cd` SET `consultantdoctorid`='$consultantdoctorid',`chest_pain`='$chest_pain',`irregu_hear_beat`='$irregu_hear_beat',`pacemeaker`='$pacemeaker',`pacemeaker_brand`='$pacemeaker_brand',`problem_circulation`='$problem_circulation',`blood_clot`='$blood_clot',`high_bp`='$high_bp',`cd_other_details`='$cd_other_details' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($chest_pain==0 && $irregu_hear_beat==0 && $pacemeaker==0 && !$pacemeaker_brand && $problem_circulation==0 && $blood_clot==0 && $high_bp==0 && !$cd_other_details)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_cd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($chest_pain || $irregu_hear_beat || $pacemeaker || $pacemeaker_brand || $problem_circulation || $blood_clot || $high_bp || $cd_other_details)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_cd`(`patient_id`, `opd_id`, `consultantdoctorid`, `chest_pain`, `irregu_hear_beat`, `pacemeaker`, `pacemeaker_brand`, `problem_circulation`, `blood_clot`, `high_bp`, `cd_other_details`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$chest_pain','$irregu_hear_beat','$pacemeaker','$pacemeaker_brand','$problem_circulation','$blood_clot','$high_bp','$cd_other_details','$c_user','$date','$time')");
	}
}


// Respiratory Disease
$smoking=mysqli_real_escape_string($link, $_POST["smoking"]);
if(!$smoking){ $smoking=0; }
$smoking_pack=mysqli_real_escape_string($link, trim($_POST["smoking_pack"]));
$smoking_quit=mysqli_real_escape_string($link, trim($_POST["smoking_quit"]));
if($smoking_pack || $smoking_quit){ $smoking=1; }

$asthma=mysqli_real_escape_string($link, $_POST["asthma"]);
if(!$asthma){ $asthma=0; }
$emphysema=mysqli_real_escape_string($link, $_POST["emphysema"]);
if(!$emphysema){ $emphysema=0; }
$breath_shortness=mysqli_real_escape_string($link, $_POST["breath_shortness"]);
if(!$breath_shortness){ $breath_shortness=0; }
$respiratory_infection=mysqli_real_escape_string($link, $_POST["respiratory_infection"]);
if(!$respiratory_infection){ $respiratory_infection=0; }
$sleep_apnea=mysqli_real_escape_string($link, $_POST["sleep_apnea"]);
if(!$sleep_apnea){ $sleep_apnea=0; }
$cpap=mysqli_real_escape_string($link, $_POST["cpap"]);
if(!$cpap){ $cpap=0; }
$tb=mysqli_real_escape_string($link, $_POST["tb"]);
if(!$tb){ $tb=0; }

$patient_pac_details_rd=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_rd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_rd)
{
	mysqli_query($link, "UPDATE `patient_pac_details_rd` SET `consultantdoctorid`='$consultantdoctorid',`smoking`='$smoking',`smoking_pack`='$smoking_pack',`smoking_quit`='$smoking_quit',`asthma`='$asthma',`emphysema`='$emphysema',`breath_shortness`='$breath_shortness',`respiratory_infection`='$respiratory_infection',`sleep_apnea`='$sleep_apnea',`cpap`='$cpap',`tb`='$tb' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($smoking==0 && !$smoking_pack && !$smoking_quit && $asthma==0 && $emphysema==0 && $breath_shortness==0 && $respiratory_infection==0 && $sleep_apnea==0 && $cpap==0 && $tb==0)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_rd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($smoking || $smoking_pack || $smoking_quit || $asthma || $emphysema || $breath_shortness || $respiratory_infection || $sleep_apnea || $cpap || $tb)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_rd`(`patient_id`, `opd_id`, `consultantdoctorid`, `smoking`, `smoking_pack`, `smoking_quit`, `asthma`, `emphysema`, `breath_shortness`, `respiratory_infection`, `sleep_apnea`, `cpap`, `tb`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$smoking','$smoking_pack','$smoking_quit','$asthma','$emphysema','$breath_shortness','$respiratory_infection','$sleep_apnea','$cpap','$tb','$c_user','$date','$time')");
	}
}

// Neurological Disease
$stroke=mysqli_real_escape_string($link, $_POST["stroke"]);
if(!$stroke){ $stroke=0; }
$seizures=mysqli_real_escape_string($link, $_POST["seizures"]);
if(!$seizures){ $seizures=0; }
$back_neck=mysqli_real_escape_string($link, $_POST["back_neck"]);
if(!$back_neck){ $back_neck=0; }
$phys_resctriction=mysqli_real_escape_string($link, $_POST["phys_resctriction"]);
if(!$phys_resctriction){ $phys_resctriction=0; }
$memory_loss=mysqli_real_escape_string($link, $_POST["memory_loss"]);
if(!$memory_loss){ $memory_loss=0; }
$sclerosis=mysqli_real_escape_string($link, $_POST["sclerosis"]);
if(!$sclerosis){ $sclerosis=0; }
$nerve_injury=mysqli_real_escape_string($link, $_POST["nerve_injury"]);
if(!$nerve_injury){ $nerve_injury=0; }
$neuropathy=mysqli_real_escape_string($link, $_POST["neuropathy"]);
if(!$neuropathy){ $neuropathy=0; }

$patient_pac_details_nd=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_nd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_nd)
{
	mysqli_query($link, "UPDATE `patient_pac_details_nd` SET `consultantdoctorid`='$consultantdoctorid',`stroke`='$stroke',`seizures`='$seizures',`back_neck`='$back_neck',`phys_resctriction`='$phys_resctriction',`memory_loss`='$memory_loss',`sclerosis`='$sclerosis',`nerve_injury`='$nerve_injury',`neuropathy`='$neuropathy' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($stroke==0 && $seizures==0 && $back_neck==0 && $phys_resctriction==0 && $memory_loss==0 && $sclerosis==0 && $nerve_injury==0 && $neuropathy==0)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_nd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($stroke || $seizures || $back_neck || $phys_resctriction || $memory_loss || $sclerosis || $nerve_injury || $neuropathy)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_nd`(`patient_id`, `opd_id`, `consultantdoctorid`, `stroke`, `seizures`, `back_neck`, `phys_resctriction`, `memory_loss`, `sclerosis`, `nerve_injury`, `neuropathy`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$stroke','$seizures','$back_neck','$phys_resctriction','$memory_loss','$sclerosis','$nerve_injury','$neuropathy','$c_user','$date','$time')");
	}
}

// Diabetes Disease
$diabetes=mysqli_real_escape_string($link, $_POST["diabetes"]);
if(!$diabetes){ $diabetes=0; }
$insulin=mysqli_real_escape_string($link, $_POST["insulin"]);
if(!$insulin){ $insulin=0; }
$oha=mysqli_real_escape_string($link, $_POST["oha"]);
if(!$oha){ $oha=0; }
$thyroid=mysqli_real_escape_string($link, $_POST["thyroid"]);
if(!$thyroid){ $thyroid=0; }
$thyroid_disorder_details=mysqli_real_escape_string($link, trim($_POST["thyroid_disorder_details"]));
if($thyroid_disorder_details){ $thyroid=1; }
$kidney_disorder=mysqli_real_escape_string($link, $_POST["kidney_disorder"]);
if(!$kidney_disorder){ $kidney_disorder=0; }
$kidney_disorder_details=mysqli_real_escape_string($link, trim($_POST["kidney_disorder_details"]));
if($kidney_disorder_details){ $kidney_disorder=1; }
$urinate=mysqli_real_escape_string($link, $_POST["urinate"]);
if(!$urinate){ $urinate=0; }
$dialysis_schedule=mysqli_real_escape_string($link, $_POST["dialysis_schedule"]);
if(!$dialysis_schedule){ $dialysis_schedule=0; }
$dialysis_schedule_details=mysqli_real_escape_string($link, trim($_POST["dialysis_schedule_details"]));
if($dialysis_schedule_details){ $dialysis_schedule=1; }

$jaundice=mysqli_real_escape_string($link, $_POST["jaundice"]);
if(!$jaundice){ $jaundice=0; }
$hepatitis=mysqli_real_escape_string($link, $_POST["hepatitis"]);
if(!$hepatitis){ $hepatitis=0; }
$hernia=mysqli_real_escape_string($link, $_POST["hernia"]);
if(!$hernia){ $hernia=0; }
$gastro_other_details=mysqli_real_escape_string($link, trim($_POST["gastro_other_details"]));

$patient_pac_details_dtkg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_dtkg` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_dtkg)
{
	mysqli_query($link, "UPDATE `patient_pac_details_dtkg` SET `consultantdoctorid`='$consultantdoctorid',`diabetes`='$diabetes',`insulin`='$insulin',`oha`='$oha',`thyroid`='$thyroid',`thyroid_disorder_details`='$thyroid_disorder_details',`kidney_disorder`='$kidney_disorder',`kidney_disorder_details`='$kidney_disorder_details',`urinate`='$urinate',`dialysis_schedule`='$dialysis_schedule',`dialysis_schedule_details`='$dialysis_schedule_details',`jaundice`='$jaundice',`hepatitis`='$hepatitis',`hernia`='$hernia',`gastro_other_details`='$gastro_other_details' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($diabetes==0 && $insulin==0 && $oha==0 && $thyroid==0 && !$thyroid_disorder_details && $kidney_disorder==0 && !$kidney_disorder_details && $urinate==0 && $dialysis_schedule==0 && !$dialysis_schedule_details && $jaundice==0 && $hepatitis==0 && $hernia==0 && !$gastro_other_details)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_dtkg` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($diabetes || $insulin || $oha || $thyroid || $thyroid_disorder_details || $kidney_disorder || $kidney_disorder_details || $urinate || $dialysis_schedule || $dialysis_schedule_details || $jaundice || $hepatitis || $hernia || $gastro_other_details)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_dtkg`(`patient_id`, `opd_id`, `consultantdoctorid`, `diabetes`, `insulin`, `oha`, `thyroid`, `thyroid_disorder_details`, `kidney_disorder`, `kidney_disorder_details`, `urinate`, `dialysis_schedule`, `dialysis_schedule_details`, `jaundice`, `hepatitis`, `hernia`, `gastro_other_details`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$diabetes','$insulin','$oha','$thyroid','$thyroid_disorder_details','$kidney_disorder','$kidney_disorder_details','$urinate','$dialysis_schedule','$dialysis_schedule_details','$jaundice','$hepatitis','$hernia','$gastro_other_details','$c_user','$date','$time')");
	}
}

// Blood Disease
$bleeding_tendency=mysqli_real_escape_string($link, $_POST["bleeding_tendency"]);
if(!$bleeding_tendency){ $bleeding_tendency=0; }
$cell_disease=mysqli_real_escape_string($link, $_POST["cell_disease"]);
if(!$cell_disease){ $cell_disease=0; }
$blood_transfusion=mysqli_real_escape_string($link, $_POST["blood_transfusion"]);
if(!$blood_transfusion){ $blood_transfusion=0; }
$blood_transfusion_objection=mysqli_real_escape_string($link, $_POST["blood_transfusion_objection"]);
if(!$blood_transfusion_objection){ $blood_transfusion_objection=0; }
$hcv=mysqli_real_escape_string($link, $_POST["hcv"]);
if(!$hcv){ $hcv=0; }
$hbsag=mysqli_real_escape_string($link, $_POST["hbsag"]);
if(!$hbsag){ $hbsag=0; }
$hiv=mysqli_real_escape_string($link, $_POST["hiv"]);
if(!$hiv){ $hiv=0; }
$patient_pac_details_bd=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_bd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_bd)
{
	mysqli_query($link, "UPDATE `patient_pac_details_bd` SET `consultantdoctorid`='$consultantdoctorid',`bleeding_tendency`='$bleeding_tendency',`cell_disease`='$cell_disease',`blood_transfusion`='$blood_transfusion',`blood_transfusion_objection`='$blood_transfusion_objection',`hcv`='$hcv',`hbsag`='$hbsag',`hiv`='$hiv' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($bleeding_tendency==0 && $cell_disease==0 && $blood_transfusion==0 && $blood_transfusion_objection==0 && $hcv==0)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_bd` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($bleeding_tendency || $cell_disease || $blood_transfusion || $blood_transfusion_objection || $hcv)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_bd`(`patient_id`, `opd_id`, `consultantdoctorid`, `bleeding_tendency`, `cell_disease`, `blood_transfusion`, `blood_transfusion_objection`, `hcv`, `hbsag`, `hiv`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$bleeding_tendency','$cell_disease','$blood_transfusion','$blood_transfusion_objection','$hcv','$hbsag','$hiv','$c_user','$date','$time')");
	}
}


// Eye Disease
$eye_disorder=mysqli_real_escape_string($link, $_POST["eye_disorder"]);
if(!$eye_disorder){ $eye_disorder=0; }
$ear_disorder=mysqli_real_escape_string($link, $_POST["ear_disorder"]);
if(!$ear_disorder){ $ear_disorder=0; }
$cancer_therapy=mysqli_real_escape_string($link, $_POST["cancer_therapy"]);
if(!$cancer_therapy){ $cancer_therapy=0; }
$cancer_therapy_reason=mysqli_real_escape_string($link, trim($_POST["cancer_therapy_reason"]));
if($cancer_therapy_reason){ $cancer_therapy=1; }
$psychiatric_disorder=mysqli_real_escape_string($link, $_POST["psychiatric_disorder"]);
if(!$psychiatric_disorder){ $psychiatric_disorder=0; }
$psychiatric_disorder_reason=mysqli_real_escape_string($link, trim($_POST["psychiatric_disorder_reason"]));
if($psychiatric_disorder_reason){ $psychiatric_disorder=1; }
$other_disease=mysqli_real_escape_string($link, $_POST["other_disease"]);
if(!$other_disease){ $other_disease=0; }
$other_disease_name=mysqli_real_escape_string($link, trim($_POST["other_disease_name"]));
if($other_disease_name){ $other_disease=1; }

$patient_pac_details_ed=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_ed` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_ed)
{
	mysqli_query($link, "UPDATE `patient_pac_details_ed` SET `consultantdoctorid`='$consultantdoctorid',`eye_disorder`='$eye_disorder',`ear_disorder`='$ear_disorder',`cancer_therapy`='$cancer_therapy',`cancer_therapy_reason`='$cancer_therapy_reason',`psychiatric_disorder`='$psychiatric_disorder',`psychiatric_disorder_reason`='$psychiatric_disorder_reason',`other_disease`='$other_disease',`other_disease_name`='$other_disease_name' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($eye_disorder==0 && $ear_disorder==0 && $cancer_therapy==0 && !$cancer_therapy_reason && $psychiatric_disorder==0 && !$psychiatric_disorder_reason && $other_disease==0 && !$other_disease_name)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_ed` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($eye_disorder || $ear_disorder || $cancer_therapy || $cancer_therapy_reason || $psychiatric_disorder || $psychiatric_disorder_reason || $other_disease || $other_disease_name)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_ed`(`patient_id`, `opd_id`, `consultantdoctorid`, `eye_disorder`, `ear_disorder`, `cancer_therapy`, `cancer_therapy_reason`, `psychiatric_disorder`, `psychiatric_disorder_reason`, `other_disease`, `other_disease_name`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$eye_disorder','$ear_disorder','$cancer_therapy','$cancer_therapy_reason','$psychiatric_disorder','$psychiatric_disorder_reason','$other_disease','$other_disease_name','$c_user','$date','$time')");
	}
}

// For Women
$pregnant=mysqli_real_escape_string($link, $_POST["pregnant"]);
if(!$pregnant){ $pregnant=0; }
$last_menses=mysqli_real_escape_string($link, $_POST["last_menses"]);
if(!$last_menses){ $last_menses=0; }
$last_menses_details=mysqli_real_escape_string($link, trim($_POST["last_menses_details"]));
if($last_menses_details){ $last_menses=1; }
$post_menopause=mysqli_real_escape_string($link, $_POST["post_menopause"]);
if(!$post_menopause){ $post_menopause=0; }

$patient_pac_details_women=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_women` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_women)
{
	mysqli_query($link, "UPDATE `patient_pac_details_women` SET `consultantdoctorid`='$consultantdoctorid',`pregnant`='$pregnant',`last_menses`='$last_menses',`last_menses_details`='$last_menses_details',`post_menopause`='$post_menopause' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($pregnant==0 && $last_menses==0 && !$last_menses_details && $post_menopause==0)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_women` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($pregnant || $last_menses || $last_menses_details || $post_menopause)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_women`(`patient_id`, `opd_id`, `consultantdoctorid`, `pregnant`, `last_menses`, `last_menses_details`, `post_menopause`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$pregnant','$last_menses','$last_menses_details','$post_menopause','$c_user','$date','$time')");
	}
}

// Anesthesia Related Information
$anesthesia_year=mysqli_real_escape_string($link, $_POST["anesthesia_year"]);
if(!$anesthesia_year){ $anesthesia_year=0; }
$intubation=mysqli_real_escape_string($link, $_POST["intubation"]);
if(!$intubation){ $intubation=0; }
$epidural_anesthesia=mysqli_real_escape_string($link, $_POST["epidural_anesthesia"]);
if(!$epidural_anesthesia){ $epidural_anesthesia=0; }
$advesre_reaction=mysqli_real_escape_string($link, $_POST["advesre_reaction"]);
if(!$advesre_reaction){ $advesre_reaction=0; }
$hyperthermia=mysqli_real_escape_string($link, $_POST["hyperthermia"]);
if(!$hyperthermia){ $hyperthermia=0; }
$nausea=mysqli_real_escape_string($link, $_POST["nausea"]);
if(!$nausea){ $nausea=0; }
$anesthesia_eat=mysqli_real_escape_string($link, $_POST["anesthesia_eat"]);
if(!$anesthesia_eat){ $anesthesia_eat=0; }

$patient_pac_details_ari=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_ari` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_ari)
{
	mysqli_query($link, "UPDATE `patient_pac_details_ari` SET `consultantdoctorid`='$consultantdoctorid',`anesthesia_year`='$anesthesia_year',`intubation`='$intubation',`epidural_anesthesia`='$epidural_anesthesia',`advesre_reaction`='$advesre_reaction',`hyperthermia`='$hyperthermia',`nausea`='$nausea',`anesthesia_eat`='$anesthesia_eat' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($anesthesia_year==0 || $intubation==0 || $epidural_anesthesia==0 || $advesre_reaction==0 || $hyperthermia==0 || $nausea==0 || $anesthesia_eat==0)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_ari` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($anesthesia_year || $intubation || $epidural_anesthesia || $advesre_reaction || $hyperthermia || $nausea || $anesthesia_eat)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_ari`(`patient_id`, `opd_id`, `consultantdoctorid`, `anesthesia_year`, `intubation`, `epidural_anesthesia`, `advesre_reaction`, `hyperthermia`, `nausea`, `anesthesia_eat`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$anesthesia_year','$intubation','$epidural_anesthesia','$advesre_reaction','$hyperthermia','$nausea','$anesthesia_eat','$c_user','$date','$time')");
	}
}

// Drugs Interact Adverselt Anesthesia
$alcohol=mysqli_real_escape_string($link, $_POST["alcohol"]);
if(!$alcohol){ $alcohol=0; }
$steroids=mysqli_real_escape_string($link, $_POST["steroids"]);
if(!$steroids){ $steroids=0; }
$street_drug=mysqli_real_escape_string($link, $_POST["street_drug"]);
if(!$street_drug){ $street_drug=0; }
$crapped_teeth=mysqli_real_escape_string($link, $_POST["crapped_teeth"]);
if(!$crapped_teeth){ $crapped_teeth=0; }

$patient_pac_details_drug=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_drug` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_drug)
{
	mysqli_query($link, "UPDATE `patient_pac_details_drug` SET `consultantdoctorid`='$consultantdoctorid',`alcohol`='$alcohol',`steroids`='$steroids',`street_drug`='$street_drug',`crapped_teeth`='$crapped_teeth' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($alcohol==0 && $steroids==0 && $street_drug==0 && $crapped_teeth==0)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_drug` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($alcohol || $steroids || $street_drug || $crapped_teeth)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_drug`(`patient_id`, `opd_id`, `consultantdoctorid`, `alcohol`, `steroids`, `street_drug`, `crapped_teeth`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$alcohol','$steroids','$street_drug','$crapped_teeth','$c_user','$date','$time')");
	}
}


// Intubation Assessment
$dentures=mysqli_real_escape_string($link, $_POST["dentures"]);
if(!$dentures){ $dentures=0; }
$crowns=mysqli_real_escape_string($link, $_POST["crowns"]);
if(!$crowns){ $crowns=0; }
$overbites=mysqli_real_escape_string($link, $_POST["overbites"]);
if(!$overbites){ $overbites=0; }
$loose_teeth=mysqli_real_escape_string($link, $_POST["loose_teeth"]);
if(!$loose_teeth){ $loose_teeth=0; }
$oedentululous=mysqli_real_escape_string($link, $_POST["oedentululous"]);
if(!$oedentululous){ $oedentululous=0; }
$tm_joint_rom=mysqli_real_escape_string($link, trim($_POST["tm_joint_rom"]));
$neck_rom=mysqli_real_escape_string($link, trim($_POST["neck_rom"]));

$patient_pac_details_intubation=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_details_intubation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_details_intubation)
{
	mysqli_query($link, "UPDATE `patient_pac_details_intubation` SET `consultantdoctorid`='$consultantdoctorid',`dentures`='$dentures',`crowns`='$crowns',`overbites`='$overbites',`loose_teeth`='$loose_teeth',`oedentululous`='$oedentululous',`tm_joint_rom`='$tm_joint_rom',`neck_rom`='$neck_rom' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($dentures==0 && $crowns==0 && $overbites==0 && $loose_teeth==0 && $oedentululous==0 && !$tm_joint_rom && !$neck_rom)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_details_intubation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($dentures || $crowns || $overbites || $loose_teeth || $oedentululous || $tm_joint_rom || $neck_rom)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_details_intubation`(`patient_id`, `opd_id`, `consultantdoctorid`, `dentures`, `crowns`, `overbites`, `loose_teeth`, `oedentululous`, `tm_joint_rom`, `neck_rom`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$dentures','$crowns','$overbites','$loose_teeth','$oedentululous','$tm_joint_rom','$neck_rom','$c_user','$date','$time')");
	}
}

// ASA GRADE
$asa_grade_1=mysqli_real_escape_string($link, $_POST["asa_grade_1"]);
if(!$asa_grade_1){ $asa_grade_1=0; }
$asa_grade_2=mysqli_real_escape_string($link, $_POST["asa_grade_2"]);
if(!$asa_grade_2){ $asa_grade_2=0; }
$asa_grade_3=mysqli_real_escape_string($link, $_POST["asa_grade_3"]);
if(!$asa_grade_3){ $asa_grade_3=0; }
$asa_grade_4=mysqli_real_escape_string($link, $_POST["asa_grade_4"]);
if(!$asa_grade_4){ $asa_grade_4=0; }
$asa_grade_5=mysqli_real_escape_string($link, $_POST["asa_grade_5"]);
if(!$asa_grade_5){ $asa_grade_5=0; }
$asa_grade_6=mysqli_real_escape_string($link, $_POST["asa_grade_6"]);
if(!$asa_grade_6){ $asa_grade_6=0; }

$patient_pac_asa_grade=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_asa_grade` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_asa_grade)
{
	mysqli_query($link, "UPDATE `patient_pac_asa_grade` SET `consultantdoctorid`='$consultantdoctorid',`asa_grade_1`='$asa_grade_1',`asa_grade_2`='$asa_grade_2',`asa_grade_3`='$asa_grade_3',`asa_grade_4`='$asa_grade_4',`asa_grade_5`='$asa_grade_5',`asa_grade_6`='$asa_grade_6' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($asa_grade_1==0 && $asa_grade_2==0 && $asa_grade_3==0 && $asa_grade_4==0 && $asa_grade_5==0 && $asa_grade_6==0)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_asa_grade` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($asa_grade_1 || $asa_grade_2 || $asa_grade_3 || $asa_grade_4 || $asa_grade_5 || $asa_grade_6)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_asa_grade`(`patient_id`, `opd_id`, `consultantdoctorid`, `asa_grade_1`, `asa_grade_2`, `asa_grade_3`, `asa_grade_4`, `asa_grade_5`, `asa_grade_6`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$asa_grade_1','$asa_grade_2','$asa_grade_3','$asa_grade_4','$asa_grade_5','$asa_grade_6','$c_user','$date','$time')");
	}
}

// PLAN
$plan_ga=mysqli_real_escape_string($link, $_POST["plan_ga"]);
if(!$plan_ga){ $plan_ga=0; }
$plan_sa=mysqli_real_escape_string($link, $_POST["plan_sa"]);
if(!$plan_sa){ $plan_sa=0; }
$plan_epidural=mysqli_real_escape_string($link, $_POST["plan_epidural"]);
if(!$plan_epidural){ $plan_epidural=0; }
$plan_tiva=mysqli_real_escape_string($link, $_POST["plan_tiva"]);
if(!$plan_tiva){ $plan_tiva=0; }
$plan_bb=mysqli_real_escape_string($link, $_POST["plan_bb"]);
if(!$plan_bb){ $plan_bb=0; }
$plan_other=mysqli_real_escape_string($link, trim($_POST["plan_other"]));
if(!$plan_other){ $plan_other=0; }

$patient_pac_plan=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_pac_plan` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($patient_pac_plan)
{
	mysqli_query($link, "UPDATE `patient_pac_plan` SET `consultantdoctorid`='$consultantdoctorid',`plan_ga`='$plan_ga',`plan_sa`='$plan_sa',`plan_epidural`='$plan_epidural',`plan_tiva`='$plan_tiva',`plan_bb`='$plan_bb',`plan_other`='$plan_other' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	
	if($plan_ga==0 && $plan_sa==0 && $plan_epidural==0 && $plan_tiva==0 && $plan_bb==0 && !$plan_other)
	{
		mysqli_query($link, "DELETE FROM `patient_pac_plan` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'");
	}
}
else
{
	if($plan_ga || $plan_sa || $plan_epidural || $plan_tiva || $plan_bb || $plan_other)
	{
		mysqli_query($link, "INSERT INTO `patient_pac_plan`(`patient_id`, `opd_id`, `consultantdoctorid`, `plan_ga`, `plan_sa`, `plan_epidural`, `plan_tiva`, `plan_bb`, `plan_other`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$consultantdoctorid','$plan_ga','$plan_sa','$plan_epidural','$plan_tiva','$plan_bb','$plan_other','$c_user','$date','$time')");
	}
}

echo "Saved";
?>
