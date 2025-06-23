<?php
include("../../includes/connection.php");

$pid=$_POST['pid'];
$opd=$_POST['opd_id'];
$ipd=$_POST['ipd'];
$batch_no=$_POST['batch_no'];
$disease_id=$_POST['disease_id'];
$medication=$_POST['medication'];
$user=$_POST['user'];

$date=date('Y-m-d');
$time=date("H:i:s");

$ds=join(",",$disease_id);
				
	$dup_entry=mysqli_query($link,"SELECT * FROM `phlebo_disease_patient_disease` WHERE `pid`='$pid' AND `opd_id`='$opd' AND `batch_no`='$batch_no'");
	if(mysqli_num_rows($dup_entry)>0)
	{
		if(mysqli_query($link,"update phlebo_disease_patient_disease set disease='$ds',medication='$medication' WHERE `pid`='$pid' AND `opd_id`='$opd' AND `batch_no`='$batch_no'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Failed.";
		}
	}
	else
	{	
		$sql="INSERT INTO `phlebo_disease_patient_disease`(`pid`, `opd_id`,`batch_no`,`disease`,`medication`, `user`, `date`, `time`) VALUES ('$pid','$opd','$batch_no','$ds','$medication','$user','$date','$time')";
		if(mysqli_query($link,$sql))
		{
			echo "Saved";
		}
		else
		{
			echo "Failed.";
		}
    }	
?>

