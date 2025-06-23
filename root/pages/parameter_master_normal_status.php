<?php
include("../../includes/connection.php");

$slno=$_POST['slno'];

$stat=mysqli_fetch_array(mysqli_query($link,"select status from parameter_normal_check where slno='$slno'"));

$nstat=0;
if($stat[status]==0)
{
	$nstat=1;
}

mysqli_query($link,"update parameter_normal_check set status='$nstat' where slno='$slno'");
?>
