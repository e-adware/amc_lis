<html>
<head>

</head>
<body>

<h2>Generating Barcode </h2>

<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");


$uhid=$_GET['uhid'];
$pin=$_GET['pin'];
$user=$_GET['user'];
$barcode_no=$_GET['barcode_no'];

$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' "));

$str=$pat_info["patient_id"]."@".$pat_info["name"]."@".$age."@".$pat_info["sex"]."@".$pin."@".$pat_reg["date"]."@".$pat_reg["time"];

$IP = $_SERVER['REMOTE_ADDR'];   

// Obtains the IP address
echo $computerName = gethostbyaddr($IP); 

$target_file="http://".$computerName."/barcodeprinter/barcode_generate_recp.php?PoiU=".$str."&barcode_no=".$barcode_no;

header("Location: $target_file");
die();

?>
</body>
</html>
