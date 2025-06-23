<?php
include("../../includes/connection.php");

$pid=$_POST['pid'];
$opd=$_POST['opd_id'];
$ipd=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$vac=$_POST['vac'];
$user=$_POST['user'];

$note=mysqli_real_escape_string($link,trim($_POST['note']));

$date=date('Y-m-d');
$time=date("H:i:s");


$entry=0;

mysqli_query($link,"delete from phlebo_sample_note where patient_id='$pid' and opd_id='$opd' and ipd_id='$ipd' and batch_no='$batch_no' and vaccu='$vac'");

if($note!='')
{
	mysqli_query($link,"INSERT INTO `phlebo_sample_note`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu`, `note`, `user`, `time`, `date`) VALUES ('$pid','$opd','$ipd','$batch_no','$vac','$note','$user','$time','$date') ");
	
	$entry=1;
}

echo $entry;
?>
