<?php

include("../../includes/connection.php");
$uhid=trim($_POST['uhid']);
$opd_id=trim($_POST['opd_id']);
$ipd_id=trim($_POST['ipd_id']);
$batch_no=trim($_POST['batch_no']);
$tst=$_POST['tstid'];
$obsrv=mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['detail']);
$sl=mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST[sl]);
$testname=mysqli_real_escape_string($link,trim($_POST[test_name]));
$doc=$_POST['doc'];
$category_id=$_POST['category_id'];

$date=date("Y-m-d");
$time=date('H:i:s');


$chk=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));

if($chk>0)
{
	$nobserv=$obsrv;	
	if($obsrv=="<p><br></p>")
	{
		$nobserv="";
	}	
	mysqli_query($GLOBALS["___mysqli_ston"], "update testresults_rad set observ='$nobserv',film_no='$sl',testname='$testname' where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'");	
}
else
{
	mysqli_query($GLOBALS["___mysqli_ston"], "insert into testresults_rad(patient_id,opd_id,ipd_id,batch_no,testid,testname,observ,doc,film_no,time,date) values('$uhid','$opd_id','$ipd_id','$batch_no','$tst','$testname','$obsrv','$doc','$sl','$time','$date')");
}


/*
if($category_id==2)
{
	$chk=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));

	if($chk>0)
	{
		$nobserv=$obsrv;	
		if($obsrv=="<p><br></p>")
		{
			$nobserv="";
		}	
		mysqli_query($GLOBALS["___mysqli_ston"], "update testresults_rad set observ='$nobserv',doc='$doc' where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'");	
	}
	else
	{
		mysqli_query($GLOBALS["___mysqli_ston"], "insert into testresults_rad(patient_id,opd_id,ipd_id,batch_no,testid,observ,doc,time,date) values('$uhid','$opd_id','$ipd_id','$batch_no','$tst','$obsrv','$doc','$time','$date')");
	}
}
if($category_id==3)
{
	$chk=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults_card where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));

	if($chk>0)
	{
		$nobserv=$obsrv;	
		if($obsrv=="<p><br></p>")
		{
			$nobserv="";
		}	
		mysqli_query($GLOBALS["___mysqli_ston"], "update testresults_card set observ='$nobserv',doc='$doc' where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'");	
	}
	else
	{
		mysqli_query($GLOBALS["___mysqli_ston"], "insert into testresults_card(patient_id,opd_id,ipd_id,batch_no,testid,observ,doc,time,date) values('$uhid','$opd_id','$ipd_id','$batch_no','$tst','$obsrv','$doc','$time','$date')");
	}
}
*/
?>
