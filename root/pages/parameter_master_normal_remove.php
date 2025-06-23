<?php
	include("../../includes/connection.php");
	
	$slno=$_POST[slno];
	
	mysqli_query($GLOBALS["___mysqli_ston"], "delete from parameter_normal_check where slno='$slno'");
?>
