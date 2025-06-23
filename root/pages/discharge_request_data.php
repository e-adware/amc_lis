<?php
include("../../includes/connection.php");

$time=date('H:i:s');
$date=date("Y-m-d");

$type=$_POST['type'];

if($type=="discharge_req")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$user=$_POST['usr'];
	
	$dis_req_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
	if($dis_req_num==0)
	{
?>
	<button type="button" id="otad" class="btn btn-info" onclick="dis_req()"><i class="icon icon-plus"></i>Request</button>
<?php
	}else
	{
		$dis_cancel_btn="";
		$final_pay_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' "));
		$final_pay_num=1;
		if($final_pay_num>0)
		{
			$dis_cancel_btn="disabled";
		}
	?>
	<button type="button" id="otad" class="btn btn-danger" onclick="dis_req_cancel()" <?php echo $dis_cancel_btn; ?>><i class="icon icon-remove"></i>Cancel Request</button>
	<?php
	}
}
if($type=="discharge_req_send")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$user=$_POST['usr'];
	
	mysqli_query($link," INSERT INTO `discharge_request`(`patient_id`, `ipd_id`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$user','$date','$time') ");
	
}
if($type=="discharge_req_cancel")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$user=$_POST['usr'];
	
	mysqli_query($link," DELETE FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	
}

?>
