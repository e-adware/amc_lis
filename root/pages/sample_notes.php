<?php
session_start();
include("../../includes/connection.php");
$i=(int)$_SESSION['emp_id'];

$date=date('Y-m-d');
$time=date("H:i:s");

$test=$_POST['test_id'];
$patient_id=$_POST['patient_id'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$note=$_POST['note'];
$user=$_POST['user'];

$qry=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], " select * from sample_note where patient_id='$patient_id' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and test_id='$test' and user='$user' "));
if($qry==0)
{
	if(mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO `sample_note`( `patient_id`, `opd_id`, `ipd_id`, `batch_no`, `test_id`, `note`, `user`, `date`, `time`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$test','$note','$user','$date','$time')"))
	{
		echo "Saved";
		$s=1;
		//mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO `sample_note_record`( `test_id`, `status`, `user`, `date`) VALUES ('$test','$s','$i','$date','$time')");
	}
	else
	{
		echo "Error";
	}
}
else
{
	mysqli_query($GLOBALS["___mysqli_ston"], " UPDATE `sample_note` SET `note`='$note',`user`='$user',`date`='$date',`time`='$time' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and `test_id`='$test' ");
	
	echo "Updated";
}


?>
