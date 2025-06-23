<?php
session_start();
include'../../includes/connection.php';

$date=date('Y-m-d'); // impotant
$time=date('h:i:s A');


function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

///////Date difference in php
	 /*$vendat=strtotime($mnufctr);
	 $vpaydat=strtotime($expiry);	
	 $vda=$vpaydat-$vendat;
	 echo  floor($vda/3600/24); */
	//////end///////////////
	

function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
	
$fid=mysqli_fetch_array(mysqli_query($link,"select max(FID) as maxfid from financialyear_master "));
$fd=$fid['maxfid'];
$fd=0;

$type=$_POST['type'];


 

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

if($type=="itemtype") ///For  Item Type Master
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
		
}

////////////////////////////////
elseif($type=="ap_center_test_save") //for testwise 
{
	$tstid=$_POST['tstid'];
	$centerid=$_POST['centerid'];
	$vcomamt=$_POST['vcomamt'];
	
	
	$qchk=mysqli_fetch_array(mysqli_query($link,"select rate from testmaster_rate where `centreno`='$centerid' and testid='$tstid' "));
	if($qchk)
	{
	    
	   mysqli_query($link,"UPDATE `testmaster_rate` SET rate='$vcomamt' WHERE `centreno`='$centerid' and testid='$tstid' ");
	   
	  
	
    }
    else
    {
				 
		 mysqli_query($link,"insert into testmaster_rate ( `testid`, `rate`, `centreno`) values('$tstid','$vcomamt','$centerid')");
	}
	
	mysqli_query($link,"delete from testmaster_rate where rate=0 ");
	mysqli_query($link,"delete from testmaster_rate where centreno='0' ");
	
}




?>

