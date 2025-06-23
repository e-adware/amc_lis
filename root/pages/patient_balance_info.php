<?php
include("../../includes/connection.php");
$uhid=$_POST['uhid'];
$opd_id=$_POST['vis'];
$q1=mysqli_query($GLOBALS["___mysqli_ston"], "select * from invest_patient_payment_details where patient_id='$uhid' and opd_id='$opd_id'");
$q=mysqli_fetch_array($q1);

 $pay=$q['tot_amount']."$".$q['dis_amt']."$".$q['balance']."$".$q['advance'];
 
 
$det=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$q[patient_id]'"));
//$did=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select a.refbydoctorid,b.centrename from patient_details a,centremaster b where a.patient_id='$q[patient_id]' and a.visit_no='$vis' and a.centreno=b.centreno"));
//$dname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ref_name from refbydoctor_master where refbydoctorid='$did[refbydoctorid]'"));



 $details=$q['date']."$".$opd_id."$".$det['name']."$".$det['age']." ".$det['age_type']."-".$det['sex'];





echo $pay."%".$details;
?>
