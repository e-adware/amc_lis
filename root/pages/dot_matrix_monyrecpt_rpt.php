<?php
//include("../../includes/connection.php");
//require('../../includes/global.function.php');

$uhid=$_GET["uhid"];
$opd_id=$_GET["opdid"];

$IP = $_SERVER['REMOTE_ADDR'];        // Obtains the IP address
$computerName = gethostbyaddr($IP); 

$target_file="http://".$computerName."/dotmatrixprint/root/pages/dot_matrix_monyrecpt_rpt.php?UhPoiID=".$uhid."&pOhsIn=".$opd_id;

header("Location: $target_file");
die();

?>
