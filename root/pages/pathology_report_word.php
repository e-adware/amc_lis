<?php
include("../../includes/connection.php");

$uhid=$_GET['uhid'];
$opd_id=$_GET['opd_id'];
$ipd_id=$_GET['ipd_id'];
$batch_no=$_GET['batch_no'];

header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=$uhid.doc");


$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$uhid'"));
////////// ***** //////////
$bill=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from payment_detail where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' "));

//$doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select first_name,last_name from ref_doc_master where id in(select refbydoctorid from patient_details where patient_id='$uhid' and visit_no='$visit')"));
$doc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select refbydoctorid,ref_name from refbydoctor_master where refbydoctorid in(select refbydoctorid from patient_info where patient_id='$uhid' )"));
if($doc[refbydoctorid]!="937")
{
	$dname="Dr. ".$doc[ref_name];
}
else
{
	$dname="Self";
}
$phl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and testid='$tst'"));
$lab=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select time,date from lab_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and testid='$tst'"));


function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-Y', $timestamp);
	return $new_date;
}
?>


<table width="100%">
<tr>
	<td>Name</td>
	<td>: <?php echo $pinfo[name];?></td>
	<td>Gender</td>
	<td>: <?php if($pinfo[sex]=="M") {echo "Male";} else { echo "Female";} ?></td>
	<td>Age</td>
	<td>: <?php echo $pinfo[age]." ".$pinfo[age_type];?> </td>
</tr>	
<tr>
	<td>UHID</td>
	<td>: <?php echo $uhid;?></td>
	<!--<td>Bill No</td>
	<td>: <?php echo $bill[payment_prefix].$bill[payment_no];?></td>-->
	<td></td>
	<td></td>
</tr>
<tr>
	<td>Ref. By</td>
	<td>: <?php echo $dname;?></td>
	<td>Sample Col.Dt</td>
	<td>: <?php echo convert_date($phl[date]);?></td>
	<td>Time</td>
	<td>: <?php echo $phl[time];?></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td>Test Done Date</td>
	<td>: <?php echo convert_date($lab[date]);?></td>
	<td>Time</td>
	<td>: <?php echo $lab[time];?></td>
</tr>
</table>


<?php
echo "\n\n\n\n\n\n";
?>


<table width="100%" id="t_res">
<tr id='t_bold'>
	<td>TEST</td>
	<td>RESULTS</td>
	<td>BIOLOGICAL REFERENCE INTERVAL</td>
</tr>
