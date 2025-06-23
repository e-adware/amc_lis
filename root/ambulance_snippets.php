<?php

	$patient_visit_type=14;
	$pat_typ_val=mysqli_fetch_array(mysqli_query($link," SELECT `p_type` FROM `patient_type_master` WHERE `p_type_id`='$patient_visit_type' "));
	$pat_typ_text=$pat_typ_val["p_type"];
	
	$daycare_pat_num=$daycare_bill_amount=$daycare_total_discount=$daycare_total_final_paid=$daycare_total_balance_paid=$daycare_total_credit=0;
	$paid_pat_qry=mysqli_query($link,"SELECT DISTINCT `patient_id`,`opd_id` FROM `payment_detail_all` WHERE `payment_type`='Final' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$patient_visit_type')");
	while($paid_pat=mysqli_fetch_array($paid_pat_qry))
	{
		$daycare_pat_num++;
		
		/// Bill
		$tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE `patient_id`='$paid_pat[patient_id]' and `ipd_id`='$paid_pat[opd_id]' "));
		$daycare_bill_amount+=$tot_serv["tots"];
		
		/// Payment
		$daycare_pat_pay=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`discount_amount`),0) AS `tot_dis`, ifnull(SUM(`amount`),0) AS `tot_paid`, ifnull(SUM(`refund_amount`),0) AS `tot_refund` FROM `payment_detail_all` WHERE `patient_id`='$paid_pat[patient_id]' and `opd_id`='$paid_pat[opd_id]' AND `payment_type`='Final' AND `payment_mode`!='Credit' AND `date`='$date' "));
		
		$daycare_total_discount+=$daycare_pat_pay["tot_dis"];
		$daycare_total_final_paid+=$daycare_pat_pay["tot_paid"];
	}
	
	/// Balance
	$daycare_pat_bal_pay=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`discount_amount`),0) AS `tot_dis`, ifnull(SUM(`amount`),0) AS `tot_paid` FROM `payment_detail_all` WHERE  `payment_type`='Balance' AND `payment_mode`!='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$patient_visit_type') "));
	$daycare_total_balance_paid+=$daycare_pat_bal_pay["tot_paid"];
	$daycare_total_discount+=$daycare_pat_bal_pay["tot_dis"];
	
	$credit_pat_qry=mysqli_query($link,"SELECT * FROM `payment_detail_all` WHERE `payment_type`='Final' AND `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$patient_visit_type')");
	while($credit_pat=mysqli_fetch_array($credit_pat_qry))
	{
		// Same day balance receive
		$casy_pat_pay=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`discount_amount`),0) AS `tot_dis`, ifnull(SUM(`amount`),0) AS `tot_paid` FROM `payment_detail_all` WHERE `patient_id`='$credit_pat[patient_id]' and `opd_id`='$credit_pat[opd_id]' AND `payment_type`='Balance' AND `date`='$date' "));
		
		$daycare_total_credit+=$credit_pat["balance"]-$casy_pat_pay["tot_paid"]-$casy_pat_pay["tot_dis"];
		
		//$daycare_total_discount+=$casy_pat_pay["tot_dis"];
		
		// Discount in Credit
		$daycare_pat_pay_credit_disaount=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`discount_amount`),0) AS `tot_dis` FROM `payment_detail_all` WHERE `patient_id`='$credit_pat[patient_id]' and `opd_id`='$credit_pat[opd_id]' AND `payment_mode`='Credit' AND `date`='$date' "));
		
		$daycare_total_discount+=$daycare_pat_pay_credit_disaount["tot_dis"];
	}
	
	// Other Refund
	$ipd_pat_ref=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`refund_amount`),0) AS `tot_refund` FROM `payment_detail_all` WHERE `payment_type`='Refund' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$patient_visit_type') "));
	$daycare_total_refund+=$ipd_pat_ref["tot_refund"];
	
	$daycare_net=$daycare_total_final_paid+$daycare_total_balance_paid-$daycare_total_refund;
	
	
	$overall_bill+=$daycare_bill_amount;
	$overall_disc+=$daycare_total_discount;
	$overall_amt_rcv+=$daycare_total_final_paid;
	$overall_bal_rcv+=$daycare_total_balance_paid;
	$overall_ref+=$daycare_total_refund;
	$overall_net+=$daycare_net;
	//$overall_free+=$daycare_free['free'];
	$overall_bal+=$daycare_total_credit;
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;"><?php echo $pat_typ_text; ?></button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
		<tr>
			<td>Bill Amount</td>
			<td><?php echo "&#8377 ".number_format($daycare_bill_amount,2);?></td>
		</tr>
		<tr>
			<td>Discount</td>
			<td class="red"><?php echo "&#8377 ".number_format($daycare_total_discount,2);?></td>
		</tr>
		<tr>
			<td>Amount Received</td>
			<td class="green"><?php echo "&#8377 ".number_format($daycare_total_final_paid,2);?></td>
		</tr>
		<tr>
			<td>Balance Received</td>
			<td class="green"><?php echo "&#8377 ".number_format($daycare_total_balance_paid,2);?></td>
		</tr>
		<tr>
			<td>Refund</td>
			<td class="red"><?php echo "&#8377 ".number_format($daycare_total_refund,2);?></td>
		</tr>
		<tr>
			<th>Net Amount</th>
			<td class="green"><?php echo "&#8377 ".number_format($daycare_net,2);?></td>
		</tr>
		<tr>
			<td>Credit</td>
			<td class="red"><?php echo "&#8377 ".number_format($daycare_total_credit,2);?></td>
		</tr>
		<tr>
			<th>Total Patients</th>
			<th><?php echo $daycare_pat_num;?></th>
		</tr>
	</table>
</div>
</div>
