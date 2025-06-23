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

if(!$doc){ $doc=0; }
if(!$iso_no){ $iso_no=0; }
if(!$iso_no_total){ $iso_no_total=0; }
if(!$instrument_id){ $instrument_id=0; }

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

$all=mysqli_real_escape_string($link, $_POST["all"]);

$all=explode("@",$all);

$old_res=mysqli_query($link,"select * from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and result!=''");
while($od=mysqli_fetch_array($old_res))
{
	mysqli_query($link, "insert into testresults_update(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `update_by`, `update_user_type`) values('$od[patient_id]', '$od[opd_id]', '$od[ipd_id]', '$od[batch_no]', '$od[testid]', '$od[paramid]', '$od[iso_no]', '$od[sequence]', '$od[result]', '$od[time]', '$od[date]', '$od[doc]', '$od[tech]', '$od[main_tech]', '$od[for_doc]', '$c_user', '1')");
}

if($iso_no>0)
{
	mysqli_query($link, "delete from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='0'");
}
mysqli_query($link, "delete from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no'");
mysqli_query($link, "delete from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no>'$iso_no_total'");

foreach($all as $a)
{
	if($a)
	{
		$val=explode("$",$a);
		$nval=((isset($link) && is_object($link)) ? mysqli_real_escape_string($link, $val[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$nval=trim($nval);
		if($nval)
		{
			$nr=load_normal($uhid,$val[1],$nval,$instrument_id);
			$nr1=explode("#",$nr);
			$range_id=$nr1[2];
			$stat=0;
			if($nr1[1]=="Error")
			{
				$stat=1;
			}
			
			$chk_res=mysqli_fetch_array(mysqli_query($link,"select result from testresults_update where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$val[1]' order by slno desc"));
			if($chk_res && $chk_res[result]==$nval)
			{
				mysqli_query($link,"delete from testresults_update where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and iso_no='$iso_no' and paramid='$val[1]' and result='$nval'");
			}
			
			$seq=mysqli_fetch_array(mysqli_query($link, "select sequence from Testparameter where TestId='$testid' and ParamaterId='$val[1]'"));
			$status=0;
			
			if(!$range_id)
			{
				$range_id=0;
			}
			
			mysqli_query($link, "insert into testresults(patient_id,opd_id,ipd_id,batch_no,testid,paramid,iso_no,sequence,result,range_status,range_id,status,time,date,doc,tech,main_tech,for_doc) values('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$val[1]','$iso_no','$seq[sequence]','$nval','$stat','$range_id','$status','$time','$date','0','$c_user','$tech','$for_doc')");
		}
	}
}

//---Check Status---//
$chk_stat=mysqli_query($link,"select * from test_param_mandatory where testid='$testid'");
while($ck=mysqli_fetch_array($chk_stat))
{
	$mand=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from testresults where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$ck[paramid]'"));
	if($mand[tot]==0)
	{
		mysqli_query($link,"update testresults set status='1' where patient_id='$uhid' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'");
		break;
	}	
}
//-----------------//

$dt_usr=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `date`,`time`,`user` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' "));

$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));

//echo "@".$uhid."@".$opd_id."@".$ipd_id."@".$pinfo['name']."@".$pinfo['age']." ".$pinfo['age_type']."@".$pinfo['sex']."@".$reg[date];

//echo "@".$uhid."@".$opd_id."@".$ipd_id."@".$pinfo['name']."@".$pinfo['age']." ".$pinfo['age_type']."@".$pinfo['sex']."@".$dt_usr['date']."@".$dt_usr['time']."@".$batch_no;
?>
