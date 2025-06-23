<?php
//include("../../includes/connection.php");
//require('../../includes/global.function.php');

$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$user=base64_decode($_GET['user']);

$IP = $_SERVER['REMOTE_ADDR'];        // Obtains the IP address
$computerName = gethostbyaddr($IP); 

$target_file="http://".$computerName."/dotmatrixprint/root/pages/dot_matrix_ipd_bill_type_detail_casual.php?UhPoiID=".$uhid."&pOhsIn=".$ipd;

header("Location: $target_file");
die();


?>
