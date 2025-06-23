<?php
	include("../../includes/connection.php");
	$id=$_POST[id];
	
	$doctor=$_POST[doctor];
	
	if($doctor==0)
	{
		$normal=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select normal from radiology_normal where testid='$id' and doctor='0'"));
	}
	else
	{
		$normal=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select normal from radiology_normal where testid='$id' and doctor='$doctor'"));
		if($normal[normal]=="<p><br></p>" || !$normal[normal])
		{
			$normal=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select normal from radiology_normal where testid='$id'"));
		}
	}
	
	
	echo $normal[normal];
?>
