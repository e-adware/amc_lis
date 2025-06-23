<?php
include("../../includes/connection.php");
$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$testid=$_POST['testid'];

$all=$_POST[all];
$all=explode("@",$all);

$range_str="";

foreach($all as $a)
{
	if($a)
	{
		$val=explode("$",$a);
		$nval=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $val[0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$nval=trim($nval);
		if($nval)
		{
			$norm=mysqli_fetch_array(mysqli_query($link,"select range_id from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$val[1]'"));
			$par_ran=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$norm[range_id]'"));
			$range_str.="#@rangechk@#".$val[1]."#@rangechkpenguin@#".nl2br($par_ran[normal_range]);
		}
	}
}
echo $range_str;

