<?php
include("../includes/connection.php");

$type=$_POST['type'];

if($type=="statistics")
{
	$dt=$_POST['dt'];
	$opd=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$dt' AND `type`='1'"));
	$lab=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$dt' AND `type`='2'"));
	$ipd=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$dt' AND `type`='3'"));
	$o_amt=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`tot_amount`) AS total FROM `consult_patient_payment_details` WHERE `date`='$dt'"));
	$i_amt=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$dt'"));
	$l_amt=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`tot_amount`) AS total FROM `invest_patient_payment_details` WHERE `date`='$dt'"));
	?>
	<table class="table table-condensed table-bordered ct">
		<tr>
			<th style="text-align:center;">Department</th>
			<th style="text-align:center;">Patient Registration</th>
			<th style="text-align:center;">Collection</th>
		</tr>
		<tr>
			<td>OPD</td>
			<td style="text-align:center;"><?php echo $opd; ?></td>
			<td><?php echo "&#8377; ".number_format($o_amt['total'],2); ?></td>
		</tr>
		<tr>
			<td>IPD</td>
			<td style="text-align:center;"><?php echo $ipd; ?></td>
			<td><?php echo "&#8377; ".number_format($i_amt['total'],2); ?></td>
		</tr>
		<tr>
			<td>LAB</td>
			<td style="text-align:center;"><?php echo $lab; ?></td>
			<td><?php echo "&#8377; ".number_format($l_amt['total'],2); ?></td>
		</tr>
		<tr>
			<th>TOTAL</th>
			<th style="text-align:center;"><?php echo $opd+$ipd+$lab; ?></th>
			<th><?php echo "&#8377; ".number_format(($o_amt['total']+$i_amt['total']+$l_amt['total']),2); ?></th>
		</tr>
	</table>
	<?php
}

if($type=="oo")
{
	
}
?>
