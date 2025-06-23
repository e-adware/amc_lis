<?php
	if($_GET["branch_id"])
	{
		$branch_id=$_GET["branch_id"];
		
		$branch_str_b=" AND b.`branch_id`='$branch_id'";
	}
	else if($p_info["branch_id"])
	{
		$branch_id=$p_info["branch_id"];
		
		$branch_str_b=" AND b.`branch_id`='$branch_id'";
	}
	else
	{
		$branch_str_b="";
	}
	
	$patient_visit_type=1;
	
	$total_bill_amount_opd=$total_discount_amount_opd=$total_advance_amount_opd=$total_bal_recv_amount_opd=$total_refund_amount_opd=$total_credit_amount_opd=$net_amount_opd=0;
	
	$pat_typ_val=mysqli_fetch_array(mysqli_query($link," SELECT `p_type` FROM `patient_type_master` WHERE `p_type_id`='$patient_visit_type' "));
	$pat_typ_text=$pat_typ_val["p_type"];

	$oq=mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b");
	$opd_pat_num=mysqli_num_rows($oq);
	
	// Bill
	$tot_bill_date=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`tot_amount`),0) AS `tot_bill` FROM `consult_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b "));
	
	$total_bill_amount_opd=$tot_bill_date["tot_bill"];
	
	// Advance Received
	$check_advance=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b "));
	
	$advance_paid_opd      =$check_advance["paid"];
	$advance_discount_opd  =$check_advance["discount"];
	$advance_refund_opd    =$check_advance["refund"];
	$advance_tax_opd       =$check_advance["tax"];
	
	$total_discount_amount_opd+=$advance_discount_opd;
	$total_advance_amount_opd+=$advance_paid_opd;
	$total_refund_amount_opd+=$advance_refund_opd;
	
	// Balance Received
	$check_balance=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b "));
	
	$balance_paid_opd      =$check_balance["paid"];
	$balance_discount_opd  =$check_balance["discount"];
	$balance_refund_opd    =$check_balance["refund"];
	$balance_tax_opd       =$check_balance["tax"];
	
	$total_discount_amount_opd+=$balance_discount_opd;
	$total_bal_recv_amount_opd+=$balance_paid_opd;
	$total_refund_amount_opd+=$balance_refund_opd;
	
	// Net Amount
	$net_amount_opd=$advance_paid_opd+$balance_paid_opd-$total_refund_amount_opd;
	
	// Credit
	$credit_pat_qry=mysqli_query($link,"SELECT a.* FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b AND a.`payment_mode`='Credit'");
	while($credit_pat=mysqli_fetch_array($credit_pat_qry))
	{
		$bal_amount=$credit_pat["balance_amount"];
		
		$credit_amount_opd=0;
		// Same day balance receive
		$pat_pay_bal=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount` FROM `payment_detail_all` WHERE `patient_id`='$credit_pat[patient_id]' and `opd_id`='$credit_pat[opd_id]' AND `payment_type`='Balance' AND `date`='$date' AND `pay_id`>$credit_pat[pay_id] "));
		
		$bal_paid_amount=$pat_pay_bal["paid"]+$pat_pay_bal["discount"];
		
		if($bal_paid_amount>$bal_amount)
		{
			$bal_paid_amount=$bal_amount;
		}
		
		$credit_amount_opd=$credit_pat["balance_amount"]-$bal_paid_amount;
		$total_credit_amount_opd+=$credit_amount_opd;
	}
	
	
	$overall_bill+=$total_bill_amount_opd;
	$overall_disc+=$total_discount_amount_opd;
	$overall_amt_rcv+=$advance_paid_opd;
	$overall_bal_rcv+=$balance_paid_opd;
	$overall_ref+=$total_refund_amount_opd;
	$overall_net+=$net_amount_opd;
	//$overall_free+=$tot_free_date_amount;
	$overall_bal+=$total_credit_amount_opd;
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;"><?php echo $pat_typ_text; ?></button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
		<tr>
			<th>Total Registration</th>
			<th><?php echo $opd_pat_num;?></th>
		</tr>
		<tr>
			<td>Bill Amount</td>
			<td><?php echo "&#8377 ".number_format($total_bill_amount_opd,2);?></td>
		</tr>
		<tr>
			<td>Discount</td>
			<td class="red"><?php echo "&#8377 ".number_format($total_discount_amount_opd,2);?></td>
		</tr>
		<tr>
			<td>Amount Received</td>
			<td class="green"><?php echo "&#8377 ".number_format($advance_paid_opd,2);?></td>
		</tr>
		<tr>
			<td>Balance Received</td>
			<td class="green"><?php echo "&#8377 ".number_format($balance_paid_opd,2);?></td>
		</tr>
		<tr>
			<td>Refund</td>
			<td class="red"><?php echo "&#8377 ".number_format($total_refund_amount_opd,2);?></td>
		</tr>
		<tr>
			<th>Net Amount</th>
			<td class="green"><?php echo "&#8377 ".number_format($net_amount_opd,2);?></td>
		</tr>
		<tr>
			<td>Credit Amount</td>
			<td class="red"><?php echo "&#8377 ".number_format($total_credit_amount_opd,2);?></td>
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
