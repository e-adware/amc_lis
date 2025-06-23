<?php
session_start();

include("../../includes/connection.php");
include("pathology_normal_range_new.php");

$c_user=trim($_SESSION['emp_id']);

//~ print_r($_POST);
//~ exit();

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$user=$_POST['user'];
$testid=$_POST['testid'];
$validate=$_POST['validate'];
$doc=$_POST["doc"];
$iso_no=$_POST["iso_no"];
$iso_no_total=$_POST["iso_no_total"];
$instrument_id=$_POST["instrument_id"];

$tech_note="";
$doc_note="";

if(!$doc){ $doc=0; }
if(!$iso_no){ $iso_no=0; }
if(!$iso_no_total){ $iso_no_total=0; }
if(!$instrument_id){ $instrument_id=0; }
if(!$result_hide){ $result_hide=0; }

//$tech=$_POST[tech];
$date=date("Y-m-d");
$time=date('H:i:s');

$tech=0;
$for_doc=0;
$level=mysqli_fetch_array(mysqli_query($link, "select levelid from Employee where ID='$c_user'"));

mysqli_query($link,"delete from approve_details where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'");

//if($level[levelid]=="7")
if($validate=="1")
{
	$tech=$c_user;
	$for_doc=$doc;
	
	mysqli_query($link,"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,d_time,d_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','$time','$date','$testid')");
}

if($_POST["type"]=="save_test_param_result")
{
	$all=mysqli_real_escape_string($link, $_POST["all"]);
	
	$all_test_results=explode("##TPR##",$all);

	$old_res=mysqli_query($link,"select * from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no'");
	while($od=mysqli_fetch_array($old_res))
	{
		// Need to check
		mysqli_query($link, "insert into testresults_update(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `update_by`, `update_user_type`) values('$od[patient_id]', '$od[opd_id]', '$od[ipd_id]', '$od[batch_no]', '$od[testid]', '$od[paramid]', '$od[iso_no]', '$od[sequence]', '$od[result]', '$od[time]', '$od[date]', '$od[doc]', '$od[tech]', '$od[main_tech]', '$od[for_doc]', '$c_user', '1')");
	}

	if($iso_no>0)
	{
		mysqli_query($link, "delete from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='0'");
	}
	mysqli_query($link, "delete from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no'");
	mysqli_query($link, "delete from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no>'$iso_no_total'");

	foreach($all_test_results as $all_test_result)
	{
		if($all_test_result)
		{
			$val=explode("(*TPR*)",$all_test_result);
			
			//$nval=((isset($link) && is_object($link)) ? mysqli_real_escape_string($link, $val[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
			
			$test_result=$val[0];
			$paramid=$val[1];
			$test_result=trim($test_result);
			if($test_result)
			{
				$nr=load_normal($uhid,$paramid,$test_result,$instrument_id);
				$nr1=explode("#",$nr);
				$range_id=$nr1[2];
				$stat=0;
				if($nr1[1]=="Error")
				{
					$stat=1;
				}
				
				$chk_res=mysqli_fetch_array(mysqli_query($link,"select result from testresults_update where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$paramid' order by slno desc"));
				
				if($chk_res && $chk_res["result"]==$test_result)
				{
					//mysqli_query($link,"delete from testresults_update where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$paramid' and result='$test_result'");
				}
				
				$seq=mysqli_fetch_array(mysqli_query($link, "select sequence from Testparameter where TestId='$testid' and ParamaterId='$paramid'"));
				$sequence=$seq["sequence"];
				$status=0;
				
				if(!$range_id) { $range_id=0; }
				if(!$sequence) { $sequence=0; }
				
				mysqli_query($link, "INSERT INTO `testresults`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$sequence','$test_result','$stat','$range_id','$status','$tech_note','$doc_note','$instrument_id','$result_hide','$time','$date','0','$c_user','$tech','$for_doc')");
			}
		}
	}

	//---Check Status---//
	$chk_stat=mysqli_query($link,"select * from test_param_mandatory where testid='$testid'");
	while($ck=mysqli_fetch_array($chk_stat))
	{
		$mand=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$ck[paramid]'"));
		if($mand["tot"]==0)
		{
			mysqli_query($link,"update testresults set status='1' where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'");
			break;
		}
	}
}

if($_POST["type"]=="save_test_summary")
{
	$summary=mysqli_real_escape_string($link, $_POST["summary"]);
	
	if($summary=="<p><br></p>" || $summary=="<br>")
	{
		mysqli_query($link, "DELETE FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND testid='$testid'");
	}
	else
	{
		mysqli_query($link, "DELETE FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND testid='$testid'");
		
		mysqli_query($link, "INSERT INTO `patient_test_summary`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `summary`, `time`, `date`, `user`, `doc`, `main_tech`, `for_doc`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$summary','$time','$date','$c_user','0','$tech','$for_doc')");
	}
}

if($_POST["type"]=="save_widal_result")
{
	$specimen=$_POST['specimen'];
	$incubation_temp=$_POST['incubation_temp'];
	$method=$_POST['method'];
	
	$ov=$_POST['ov'];
	$hv=$_POST['hv'];
	$ahv=$_POST['ahv'];
	$bhv=$_POST['bhv'];
	$imp=$_POST['imp'];
	
	$ov=explode("@@",$ov);
	if(sizeof($ov)>0)
	{
		mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','1','$ov[1]','$ov[2]','$ov[3]','$ov[4]','$ov[5]','$ov[6]','$imp','$specimen','$incubation_temp','$method','$c_user','$tech','0','$for_doc','$time','$date','0')");
	}
	$hv=explode("@@",$hv);
	if(sizeof($hv)>0)
	{
		mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','2','$hv[1]','$hv[2]','$hv[3]','$hv[4]','$hv[5]','$hv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
	}
	$ahv=explode("@@",$ahv);
	if(sizeof($ahv)>0)
	{
		mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','3','$ahv[1]','$ahv[2]','$ahv[3]','$ahv[4]','$ahv[5]','$ahv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
	}
	$bhv=explode("@@",$bhv);
	if(sizeof($bhv)>0)
	{
		mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','4','$bhv[1]','$bhv[2]','$bhv[3]','$bhv[4]','$bhv[5]','$bhv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
	}
}

mysqli_close($link);
?>
