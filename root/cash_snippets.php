<?php
	$o_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`tot_amount`) AS total FROM `consult_patient_payment_details` WHERE `date`='$date'"));
	$i_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND	`bill_no` like '%/IP'"));
	$casualty=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='4')"));
	$dental=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='7')"));
	$emergncy=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='6')"));
	$physio=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ipd_advance_payment_details` WHERE `date`='$date' AND `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='5')"));
	$l_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `invest_payment_detail` WHERE `date`='$date'"));
	$p_c=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS total FROM `ph_payment_details` WHERE `entry_date`='$date' and bill_type_id='1'"));
	//$all_total=$o_c['total']+$i_c['total']+$casualty['total']+$dental['total']+$emergncy['total']+$physio['total']+$l_c['total']+$p_c['total'];
	
	$snp=mysqli_fetch_array(mysqli_query($link,"SELECT `snippets` FROM `level_master` WHERE `levelid`='$p_info[levelid]'"));
	$all_total=0;
	
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;">CASHIER</button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
	<?php
		if (strpos($snp['snippets'], '1@') !== false)
		{
			$all_total+=$o_c['total'];
	?>
		<tr>
			<th>OPD</th>
			<td><?php if($o_c['total']){echo "&#8377 ".number_format($o_c['total'],2);}else{echo "&#8377 0.00";}?></td>
		</tr>
	<?php } ?>
	<?php
		if (strpos($snp['snippets'], '3@') !== false)
		{
			$all_total+=$l_c['total'];
	?>
		<tr>
			<th>LAB</th>
			<td><?php if($l_c['total']){echo "&#8377 ".number_format($l_c['total'],2);}else{echo "&#8377 0.00";}?></td>
		</tr>
	<?php } ?>
	<?php
		if (strpos($snp['snippets'], '2@') !== false)
		{
			$all_total+=$i_c['total'];
	?>
		<tr>
			<th>IPD</th>
			<td><?php if($i_c['total']){echo "&#8377 ".number_format($i_c['total'],2);}else{echo "&#8377 0.00";}?></td>
		</tr>
	<?php } ?>
	<?php
		if (strpos($snp['snippets'], '7@') !== false)
		{
			$all_total+=$casualty['total'];
	?>
		<tr>
			<th>Casualty</th>
			<td><?php if($casualty['total']){echo "&#8377 ".number_format($casualty['total'],2);}else{echo "&#8377 0.00";}?></td>
		</tr>
	<?php } ?>
	<?php
		if (strpos($snp['snippets'], '8@') !== false)
		{
			$all_total+=$dental['total'];
	?>
		<tr>
			<th>Dental</th>
			<td><?php if($dental['total']){echo "&#8377 ".number_format($dental['total'],2);}else{echo "&#8377 0.00";}?></td>
		</tr>
	<?php } ?>
	<?php
		if (strpos($snp['snippets'], '9@') !== false)
		{
			$all_total+=$emergncy['total'];
	?>
		<tr>
			<th>Emergency</th>
			<td><?php if($emergncy['total']){echo "&#8377 ".number_format($emergncy['total'],2);}else{echo "&#8377 0.00";}?></td>
		</tr>
	<?php } ?>
	<?php
		if (strpos($snp['snippets'], '10@') !== false)
		{
			$all_total+=$physio['total'];
	?>
		<tr>
			<th>Physiotherapy</th>
			<td><?php if($physio['total']){echo "&#8377 ".number_format($physio['total'],2);}else{echo "&#8377 0.00";}?></td>
		</tr>
	<?php } ?>
	<?php
		if (strpos($snp['snippets'], '4@') !== false)
		{
			$all_total+=$p_c['total'];
	?>
		<tr>
			<th>Pharmacy</th>
			<td><?php if($p_c['total']){echo "&#8377 ".number_format($p_c['total'],2);}else{echo "&#8377 0.00";}?></td>
		</tr>
	<?php } ?>
	<?php
		if ($snp['snippets'])
		{
	?>
		<tr>
			<th>Total</th>
			<td><?php echo "&#8377 ".number_format($all_total,2);?> <span class="text-right"><button type="button" id="totbtn" class="btn btn-mini" style="background:#85C24C;color:#ffffff;" onclick="cash_rep_print('<?php echo $p_info['emp_id']?>')">Print</button></span></td>
		</tr>
	<?php } ?>
	</table>
</div>
</div>
