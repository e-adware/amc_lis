<?php
	$casq=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='5'");
	$cas_num=mysqli_num_rows($casq);
	//$casamt=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `bill_no` like '%/CA'"));
	$casamt_qty=mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='5') ");
	$casamt=mysqli_fetch_array($casamt_qty);
?>
<div class="spap_dash span5">
	<button style="width:100%;background-color:#ddd;padding:5px;border:0;">Physiotherapy</button>
	<table class="table table-condensed">
		<tr>
			<td>Total Patients</td>
			<td><?php echo $cas_num;?></td>
		</tr>
		<tr>
			<td>Total Amount</td>
			<td><?php echo "&#8377 ".number_format($casamt['total'],2);?></td>
		</tr>
	</table>
</div>

<style>
.spap_dash
{
	border: 1px solid #ddd;
	margin-top: 3%;
	padding: 2px;
	margin-left: 1%;
}
</style>
