<?php
include'../includes/connection.php';

$date=date("Y-m-d");
$time=date("H:i:s");
$mkTime=mktime();
function text_query($txt)
{
	if($txt)
	{
		$myfile = file_put_contents('barcodes.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}

$barcodes=$_POST['barcodes'];
$bCode=$_POST['bCode'];

text_query($bCode."-".$mkTime);

mysqli_query($link,"INSERT INTO `access_ip`(`levelid`, `counter_name`, `ip_addr`) VALUES ('1','$bCode','$mkTime')");

echo $bCode;
?>