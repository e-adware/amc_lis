<?php
include("../../includes/connection.php");

$type=$_POST[type];

if($type==1)
{
	$id=$_POST[id];
	$formula=$_POST[formula];
	$dec=$_POST[dec];
	
	if($formula)
	{
		mysqli_query($GLOBALS["___mysqli_ston"], "delete from  parameter_formula where ParameterID='$id'");
		mysqli_query($GLOBALS["___mysqli_ston"], "insert into parameter_formula values('$id','$formula','$dec')");
	}
}
else if($type==2)
{
	$id=$_POST[id];
	mysqli_query($GLOBALS["___mysqli_ston"], "delete from  parameter_formula where ParameterID='$id'");
}
?>
