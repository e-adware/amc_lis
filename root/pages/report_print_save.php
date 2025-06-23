<?php
include("../../includes/connection.php");

$uhid		=$_POST['uhid'];
$opd_id		=$_POST['opd_id'];
$ipd_id		=$_POST['ipd_id'];
$batch_no	=$_POST['batch_no'];
$dept_id	=$_POST['dept_id'];
$user		=$_POST['user'];

$date		=date('Y-m-d');
$time		=date("H:i:s");

if($dept_id)
{
	mysqli_query($link,"INSERT INTO `testreport_print`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `dept_id`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$dept_id','$date','$time','$user')");
}
?>
