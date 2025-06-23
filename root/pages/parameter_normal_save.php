<?php
include("../../includes/connection.php");

$id=$_POST["tid"];
$dep=$_POST["dep"];
$sex=$_POST["sex"];
$a_from=$_POST["a_from"];
$a_from_typ=$_POST["a_from_typ"];
$a_to=$_POST["a_to"];
$a_to_typ=$_POST["a_to_typ"];
$val_f=$_POST["val_f"];
$val_t=$_POST["val_t"];
$nrange=$_POST["n_range"];
$instrument_id=$_POST["instrument_id"];

$type=$_POST["type"];

if(!$dep){ $dep=0; }
if(!$na_from){ $na_from=0; }
if(!$na_to){ $na_to=0; }
if(!$sign){ $sign=0; }
if(!$status){ $status=0; }
if(!$instrument_id){ $instrument_id=0; }

if($type==1)
{
	$na_from=$a_from*$a_from_typ;
	$na_to=$a_to*$a_to_typ;
	
	$age_ft="Years";
	if($a_from_typ==1)
	{
		$age_ft="Days";
	}
	else if($a_from_typ==30)
	{
		$age_ft="Months";
	}
	
	$age_tt="Years";
	if($a_to_typ==1)
	{
		$age_tt="Days";
	}
	else if($a_to_typ==30)
	{
		$age_tt="Months";
	}
	
	$normal_r=$nrange;
	
	/*
	if($nrange)
	{
		$normal_r=$nrange;
	}
	else
	{
		$normal_r=$a_from." ".$age_ft." - ".$a_to." ".$age_tt;
	}
	*/
	//mysqli_query($link, "insert into parameter_normal_check(parameter_id,dep_id,age_from,age_to,age_type,sex,value_from,value_to,normal_range) values('$id','$dep','$na_from','$na_to','Days','$sex','$val_f','$val_t','$normal_r')");
	
	mysqli_query($link, "INSERT INTO `parameter_normal_check`(`parameter_id`, `dep_id`, `age_from`, `age_to`, `age_type`, `sex`, `value_from`, `value_to`, `normal_range`, `sign`, `status`, `instrument_id`) VALUES ('$id','$dep','$na_from','$na_to','Days','$sex','$val_f','$val_t','$normal_r','$sign','$status','$instrument_id')");
	
	
	//echo "insert into parameter_normal_check(parameter_id,dep_id,age_from,age_to,age_type,sex,value_from,value_to,normal_range) v 
}
else if($type==2)
{
	$na_from=$a_from*$a_from_typ;
	$na_to=$a_to*$a_to_typ;
	
	
	
	$age_ft="Years";
	if($a_from_typ==1)
	{
		$age_ft="Days";
	}
	else if($a_from_typ==30)
	{
		$age_ft="Months";
	}
	
	$age_tt="Years";
	if($a_to_typ==1)
	{
		$age_tt="Days";
	}
	else if($a_to_typ==30)
	{
		$age_tt="Months";
	}
	
	$normal_r=$nrange;
	
	$sl=$_POST["sl"];
	
	mysqli_query($link, "update parameter_normal_check set dep_id='$dep',age_from='$na_from',age_to='$na_to',sex='$sex',value_from='$val_f',value_to='$val_t',normal_range='$normal_r',instrument_id='$instrument_id' where slno='$sl' and parameter_id='$id'");
}
?>
