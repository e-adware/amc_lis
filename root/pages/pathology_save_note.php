<?php

	include("../../includes/connection.php");
	
	$pin=$_POST[pin];
	$batch=$_POST[batch];
	$tst=$_POST[tid];
	$user=trim($_POST[user]);
	$note=trim(mysqli_real_escape_string($link,$_POST[note]));
	
	$time=date('H:i:s');	
	$date=date("Y-m-d");

	mysqli_query($link,"delete from testresults_note where (opd_id='$pin' or ipd_id='$pin') and batch_no='$batch' and testid='$tst'");
	
	$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$pin'"));
	
	$pin_id=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_details where (opd_id='$pin' or ipd_id='$pin')"));
	
	
	mysqli_query($link,"insert into testresults_note(patient_id,opd_id,ipd_id,batch_no,testid,note,user,time,date) values('$det[patient_id]','$pin_id[opd_id]','$pin_id[ipd_id]','$batch','$tst','$note','$user','$time','$date')");
	
	
?>
