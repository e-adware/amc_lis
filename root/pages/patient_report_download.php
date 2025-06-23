<?php
include("../../includes/connection.php");

$patient_id=base64_decode($_GET['pd']);
$pin=base64_decode($_GET['pn']);
$batch=base64_decode($_GET['bt']);
$report_day=base64_decode($_GET['rd']);

$tests="";

$tst=mysqli_query($link,"select a.testid,a.opd_id,a.ipd_id from patient_test_details a,testmaster b where a.testid=b.testid and a.patient_id='$patient_id' and (a.opd_id='$pin' or a.ipd_id='$pin') and a.batch_no='$batch' and b.report_delivery_2='$report_day' order by a.slno");
while($ts=mysqli_fetch_array($tst))
{
	$tests.="@".$ts[testid];
	$opd_id=$ts[opd_id];
	$ipd_id=$ts[ipd_id];
}


$nurl="report_print_path_pdf.php?uhid=".$patient_id."&opd_id=".$opd_id."&ipd_id=".$ipd_id."&batch_no=".$batch."&tests=".$tests;

header("Location: ".$nurl);
?>
