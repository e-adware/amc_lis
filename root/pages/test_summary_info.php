<?php
	include("../../includes/connection.php");
	$id=$_POST[id];
	
	if($_POST[type]=="test")
	{
		$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$id'"));
		
		$summ=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select summary from test_summary where testid='$id'"));
		
		echo $summ[summary];
	}
	else
	{
		$summ=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select summary from test_summary where paramid='$id'"));		
		echo $summ[summary];
	}
?>

