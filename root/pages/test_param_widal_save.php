<?php
session_start();

include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

$ov=$_POST['ov'];
$hv=$_POST['hv'];
$ahv=$_POST['ahv'];
$bhv=$_POST['bhv'];
$imp=$_POST['imp'];
//$tech=$_POST[tech];
$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$testid=$_POST['testid'];

$specimen=$_POST['specimen'];
$incubation_temp=$_POST['incubation_temp'];
$method=$_POST['method'];

$date=date("Y-m-d");
$time=date('H:i:s');


$uname=$_POST['user'];
$doc=$_POST['doc'];
$validate=$_POST['validate'];
$tech=0;

mysqli_query($link,"delete from approve_details where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'");

$for_doc=0;
$level=mysqli_fetch_array(mysqli_query($link, "select levelid from Employee where ID='$uname'"));

if($validate=="1")
{
	$tech=$c_user;
	$for_doc=$doc;
	
	mysqli_query($link,"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','$testid')");
}

mysqli_query($link, "delete from widalresult where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and `testid`='$testid' ");


$ov=explode("@@",$ov);
if(sizeof($ov)>0)
{
	mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','1','$ov[1]','$ov[2]','$ov[3]','$ov[4]','$ov[5]','$ov[6]','$imp','$specimen','$incubation_temp','$method','$c_user','$tech','0','$for_doc','$time','$date','0')");
}
$hv=explode("@@",$hv);
if(sizeof($hv)>0)
{
	//mysqli_query($link, "insert into widalresult values('$uhid','$opd_id','$ipd_id','$batch_no','2','$hv[1]','$hv[2]','$hv[3]','$hv[4]','$hv[5]','$hv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
	
	mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','2','$hv[1]','$hv[2]','$hv[3]','$hv[4]','$hv[5]','$hv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
}
$ahv=explode("@@",$ahv);
if(sizeof($ahv)>0)
{
	//mysqli_query($link, "insert into widalresult values('$uhid','$opd_id','$ipd_id','$batch_no','3','$ahv[1]','$ahv[2]','$ahv[3]','$ahv[4]','$ahv[5]','$ahv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
	
	mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','3','$ahv[1]','$ahv[2]','$ahv[3]','$ahv[4]','$ahv[5]','$ahv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
}
$bhv=explode("@@",$bhv);
if(sizeof($bhv)>0)
{
	//mysqli_query($link, "insert into widalresult values('$uhid','$opd_id','$ipd_id','$batch_no','4','$bhv[1]','$bhv[2]','$bhv[3]','$bhv[4]','$bhv[5]','$bhv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
	
	mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','4','$bhv[1]','$bhv[2]','$bhv[3]','$bhv[4]','$bhv[5]','$bhv[6]','$imp','$c_user','$tech','0','$for_doc','$time','$date','0')");
}

/*
mysqli_query($link,"delete from approve_details where patient_id='$uhid' and visit_no='$visit' and testid='1227'");
mysqli_query($link,"insert into approve_details(patient_id,visit_no,t_time,t_date,d_time,d_date,testid) values('$uhid','$visit','$time','$date','','','1227')");
*/

//$reg=mysqli_fetch_array(mysqli_query($link, "select * from patient_reg_details where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' "));

$dt_usr=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `date`,`time`,`user` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' "));

$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));

//echo "@".$uhid."@".$visit."@".$reg[reg_no]."@".$pinfo[name]."@".$pinfo[age]." ".$pinfo[age_type]."@".$pinfo[sex]."@".$reg[date];

echo "@".$uhid."@".$opd_id."@".$ipd_id."@".$pat_info['name']."@".$pat_info['age']." ".$pat_info['age_type']."@".$pat_info['sex']."@".$dt_usr['date']."@".$dt_usr['time']."@".$batch_no;

?>
