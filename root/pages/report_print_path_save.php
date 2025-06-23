<?php
include("../../includes/connection.php");

$time=date('H:i:s');	
$date=date("Y-m-d");

$type=$_POST['type'];

$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$uhid=$_POST['uhid'];
$batch_no=$_POST['batch_no'];
//$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_reg_details where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' "));

//$nbill=$reg[reg_no];

if($type=="sing")
{
	$tst=$_POST['tst'];	
	mysqli_query($GLOBALS["___mysqli_ston"], "delete from testreport_print where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'");

	mysqli_query($GLOBALS["___mysqli_ston"], "insert into testreport_print(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `date`, `time`) values('$uhid','$opd_id','$ipd_id','$batch_no','$tst','$date','$time')");
}
else if($type=="all")
{
	$tests=$_POST[tst];	
	$test=explode("@",$tests);
	foreach($test as $tst)
	{
		if($tst)
		{
			mysqli_query($GLOBALS["___mysqli_ston"], "delete from testreport_print where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and `batch_no`='$batch_no' and testid='$tst'");
			//mysqli_query($GLOBALS["___mysqli_ston"], "insert into testreport_print(patient_id,opd_id,ipd_id,reg_no,testid) values('$uhid','$opd_id','$ipd_id','$nbill','$tst')");
			mysqli_query($GLOBALS["___mysqli_ston"], "insert into testreport_print(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `date`, `time`) values('$uhid','$opd_id','$ipd_id','$batch_no','$tst','$date','$time')");
		}	
	}
}
$dt_usr=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT distinct `date`,`time`,`user` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' "));

$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$uhid'"));
//echo "@".$uhid."@".$opd_id."@".$ipd_id."@".$pinfo[name]."@".$pinfo[age]." ".$pinfo[age_type]."@".$pinfo[sex];
echo "@".$uhid."@".$opd_id."@".$ipd_id."@".$pat_info['name']."@".$pat_info['age']." ".$pat_info['age_type']."@".$pat_info['sex']."@".$dt_usr['date']."@".$dt_usr['time']."@".$batch_no;
?>
