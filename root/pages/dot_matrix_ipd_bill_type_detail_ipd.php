<?php
//include("../../includes/connection.php");
//require('../../includes/global.function.php');

$uhid=$_GET['uhid'];
$ipd=$_GET['ipd'];
$user=$_GET['user'];
$bill=$_GET['bill'];

$IP = $_SERVER['REMOTE_ADDR'];        // Obtains the IP address
$computerName = gethostbyaddr($IP); 

$target_file="http://".$computerName."/dotmatrixprint/root/pages/dot_matrix_ipd_bill_type_detail_ipd.php?UhPoiID=".$uhid."&pOhsIn=".$ipd."&BpoIlUL=".$bill;

header("Location: $target_file");
die();


?>
