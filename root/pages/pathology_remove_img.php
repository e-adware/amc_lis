<?php
include("../../includes/connection.php");

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$test=$_POST['tid'];
$slno=$_POST['slno'];

mysqli_query($GLOBALS["___mysqli_ston"], "delete from image_temp where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$test' and img_no='$slno'");



?>
