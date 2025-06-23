<?php
session_start();
include("../../includes/connection.php");
$ses=$_SESSION['emp_id'];

$test=$_POST['test_id'];
$patient_id=$_POST['patient_id'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$user=$_POST['user'];

$qry=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], " select * from sample_note where patient_id='$patient_id' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and test_id='$test' and user='$user' "));
if($qry>0)
{
	$val=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " select * from sample_note where patient_id='$patient_id' and opd_id='$opd_id'and ipd_id='$ipd_id' and `batch_no`='$batch_no' and test_id='$test' and user='$user' "));
	echo $val['note'];
}
else
{
	echo "";
}
?>
