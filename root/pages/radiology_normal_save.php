<?php
	include("../../includes/connection.php");
	
	
	$testid=$_POST[testid];
	$doctor=$_POST[doctor];
	$normal=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST[normal]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	
	echo "insert into radiology_normal(testid,normal,doctor) values('$testid','$normal','$doctor')";
	
	mysqli_query($GLOBALS["___mysqli_ston"], "delete from radiology_normal where testid='$testid' and doctor='$doctor'");
	mysqli_query($GLOBALS["___mysqli_ston"], "insert into radiology_normal(testid,normal,doctor) values('$testid','$normal','$doctor')");
?>
