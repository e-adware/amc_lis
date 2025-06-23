<?php
	$pq=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`total_amt`) AS total, SUM(`discount_amt`) AS dis_tot, SUM(`adjust_amt`) AS adj_tot FROM `ph_sell_master` WHERE `entry_date`='$date'"));
	
	$pq1=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`total_amt`) AS maxgrnd_flr_sale FROM `ph_sell_master` WHERE `entry_date`='$date' and substore_id='1' "));
	
	$pq2=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`total_amt`) AS maxuprflrsale FROM `ph_sell_master` WHERE `entry_date`='$date' and substore_id='2' "));
	
	$cashsale=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`paid_amt`) AS totalcash FROM `ph_sell_master` WHERE `entry_date`='$date' and bill_type_id='1'"));
	$creditsale=mysqli_fetch_array(mysqli_query($link,"SELECT ifNull(SUM(`total_amt`),0) AS totalcredit, ifNull(SUM(`discount_amt`),0) AS cr_dis, ifNull(SUM(`adjust_amt`),0) AS cr_adj FROM `ph_sell_master` WHERE `entry_date`='$date' and bill_type_id='2'"));
	$credit_sale=$creditsale['totalcredit']-$creditsale['cr_dis']-$creditsale['cr_adj'];
	$totlrcpt=mysqli_fetch_array(mysqli_query($link,"SELECT SUM(`amount`) AS ttlrcpt FROM `ph_payment_details` WHERE `entry_date`='$date' "));
	$crr_rcvd=mysqli_fetch_array(mysqli_query($link,"SELECT ifNull(SUM(`amount`),0) AS crr_rcv FROM `ph_payment_details` WHERE `entry_date`='$date' AND `type_of_payment`='B'"));
	$rec=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `entry_date`='$date'"));
	
	//~ $qitemrtrn=mysqli_query($link,"select * from ph_item_return_master where return_date='$date' ");
	//~ while($qitemrtrn1=mysqli_fetch_array($qitemrtrn))
	//~ {
		//~ $qrate=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where item_code='$qitemrtrn1[item_code]' and recept_batch='$qitemrtrn1[batch_no]'"));
		//~ $vrtrnamt1=0;
		//~ $vrtrnamt1=$qitemrtrn1['return_qnt']*$qrate['recpt_mrp'];
		//~ $vrtrnamt=$vrtrnamt+$vrtrnamt1;
	//~ }
	
	$qitemrtrn=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxrtrn from ph_item_return_master where return_date='$date' "));
	$vrtrnamt=$qitemrtrn['maxrtrn'];
	
	//$vttlcrcpt=$totlrcpt['ttlrcpt']-$vrtrnamt;
	$vttlcrcpt=$cashsale['totalcash']+$crr_rcvd['crr_rcv'];
	$ph_bal=$creditsale['totalcredit']-$creditsale['cr_dis']-$creditsale['cr_adj'];
	
	$overall_bill+=$pq['total'];
	$overall_disc+=$pq['dis_tot'];
	$overall_amt_rcv+=$vttlcrcpt;
	$overall_bal_rcv+=$crr_rcvd['crr_rcv'];
	//~ $overall_ref+=$opd_ref_det['tot'];
	$overall_net+=$vttlcrcpt;
	//~ $overall_free+=$opd_free_det['tot'];
	$overall_bal+=$ph_bal;
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;">PHARMACY</button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
		<tr>
			<th>Total Sale</th>
			<th><?php echo "&#8377 ".number_format($pq['total'],2);?></th>
		</tr>
		<tr>
			<th>Ground Floor Sale</th>
			<th><?php echo "&#8377 ".number_format($pq1['maxgrnd_flr_sale'],2);?></th>
		</tr>
		<tr>
			<th>Second Floor Sale</th>
			<th><?php echo "&#8377 ".number_format($pq2['maxuprflrsale'],2);?></th>
		</tr>
		
		<tr>
			<td>Cash Sale</td>
			<td class="green"><?php echo "&#8377 ".number_format($cashsale['totalcash'],2);?></td>
		</tr>
		<tr>
			<td>Credit Sale</td>
			<td class="red"><?php echo "&#8377 ".number_format($ph_bal,2);?></td>
		</tr>
		<tr>
			<td>Credit Received</td>
			<td class="green"><?php echo "&#8377 ".number_format($crr_rcvd['crr_rcv'],2);?></td>
		</tr>
		<tr>
			<td>Total Return</td>
			<td class="red"><?php echo "&#8377 ".number_format($vrtrnamt,2);?></td>
		</tr>
		<tr>
			<td>Total Discount</td>
			<td class="red"><?php echo "&#8377 ".number_format($pq['dis_tot'],2);?></td>
		</tr>
		<tr>
			<td>Total Adjustment</td>
			<td class="red"><?php echo "&#8377 ".number_format($pq['adj_tot'],2);?></td>
		</tr>
		<tr>
			<td>Total Receipt Amount</td>
			<td class="green"><?php echo "&#8377 ".number_format($vttlcrcpt,2);?></td>
		</tr>
		<!--<tr>
			<th>Consultant Doctor</th>
			<th>Number of patients</th>
		</tr>
		<?php
			//$qr=mysqli_query($link,"SELECT DISTINCT `consultantdoctorid` FROM `appointment_book` WHERE `date`='$date'");
			//while($d=mysqli_fetch_array($qr))
			//{
				//$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$d[consultantdoctorid]'"));
				//$p=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `consultantdoctorid`='$d[consultantdoctorid]' AND `appointment_date`='$date'"));
			?>
			<tr>
				<td><?php //echo $doc['Name'];?></td>
				<td><?php //echo $p;?></td>
			</tr>
			<?php
			//}
			?>
			-->
	</table>
</div>
</div>
