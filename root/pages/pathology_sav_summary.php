<?php
session_start();

include('../../includes/connection.php');

$c_user=trim($_SESSION['emp_id']);

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$tid=$_POST['testid'];
//$tech=$_POST[tech];
$fdoc=$_POST['fdoc'];
$tech=0;
$user=$_POST['user'];
$validate=$_POST['validate'];

$date=date("Y-m-d");
$time=date('H:i:s');

$tech=0;
$for_doc=0;

mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'");

$level=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select levelid from Employee where ID='$c_user'"));
//if($level[levelid]=="7")

if($validate=="1")
{
	$tech=$c_user;
	$for_doc=$fdoc;
	
	mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,opd_id,ipd_id,batch_no,t_time,t_date,testid) values('$uhid','$opd_id','$ipd_id','$batch_no','$time','$date','$testid')");
}


$summ=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST[summ]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

if($summ=="<p><br></p>" || $summ=="<br>")
{
	mysqli_query($GLOBALS["___mysqli_ston"], "delete from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tid'");
}
else
{
	mysqli_query($GLOBALS["___mysqli_ston"], "delete from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tid'");
	mysqli_query($GLOBALS["___mysqli_ston"], "insert into patient_test_summary (`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `summary`, `time`, `date`, `user`, `doc`, `main_tech`, `for_doc`) VALUES('$uhid','$opd_id','$ipd_id','$batch_no','$tid','$summ','$time','$date','$c_user','0','$tech','$for_doc')");
}

/*
mysqli_query($GLOBALS["___mysqli_ston"],"delete from approve_details where patient_id='$uhid' and visit_no='$visit' and testid='$tid'");
mysqli_query($GLOBALS["___mysqli_ston"],"insert into approve_details(patient_id,visit_no,t_time,t_date,d_time,d_date,testid) values('$uhid','$visit','$time','$date','','','$tid')");
*/

//$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_reg_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' "));

$dt_usr=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT distinct `date`,`time`,`user` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' "));

$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$uhid'"));

//echo "@".$uhid."@".$opd_id."@".$ipd_id."@".$pinfo[name]."@".$pinfo[age]." ".$pinfo[age_type]."@".$pinfo[sex]."@".$reg[date];

echo "@".$uhid."@".$opd_id."@".$ipd_id."@".$pat_info['name']."@".$pat_info['age']." ".$pat_info['age_type']."@".$pat_info['sex']."@".$dt_usr['date']."@".$dt_usr['time']."@".$batch_no;

?>
