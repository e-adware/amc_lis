<?php
	if($_GET["branch_id"])
	{
		$branch_id=$_GET["branch_id"];
		
		$branch_str=" AND `branch_id`='$branch_id'";
		$branch_str_b=" AND b.`branch_id`='$branch_id'";
	}
	else if($p_info["branch_id"])
	{
		$branch_id=$p_info["branch_id"];
		
		$branch_str=" AND `branch_id`='$branch_id'";
		$branch_str_b=" AND b.`branch_id`='$branch_id'";
	}
	else
	{
		$branch_str="";
		$branch_str_b="";
	}
	
	$patient_visit_type=3;
	
	$total_bill_amount_ipd=$total_discount_amount_ipd=$total_advance_amount_ipd=$total_final_amount_ipd=$total_bal_recv_amount_ipd=$total_refund_amount_ipd=$total_credit_amount_ipd=$net_amount_ipd=0;
	
	$pat_typ_val=mysqli_fetch_array(mysqli_query($link," SELECT `p_type` FROM `patient_type_master` WHERE `p_type_id`='$patient_visit_type' "));
	$pat_typ_text=$pat_typ_val["p_type"];

	$tq3=mysqli_query($link,"SELECT a.* FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND b.`type`='$patient_visit_type' AND a.`date`='$date' $branch_str_b");
	$ipd_test_num=mysqli_num_rows($tq3);
	
	$ipd_new_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='$patient_visit_type' $branch_str "));
	
	$ipd_discharge_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `slno`>0 $branch_str) "));
	
	$ipd_admit_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `slno`>0 $branch_str) "));
	
	$paid_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`payment_type`='Final' AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b");
	while($paid_pat=mysqli_fetch_array($paid_pat_qry))
	{
		$uhid=$paid_pat['patient_id'];
		$ipd=$paid_pat['opd_id'];
		
		$baby_serv_tot=0;
		$baby_ot_total=0;
		$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
		while($delivery_check=mysqli_fetch_array($delivery_qry))
		{
			$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			$baby_serv_tot+=$baby_tot_serv["tots"];
			
			// OT Charge Baby
			$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			$baby_ot_total+=$baby_ot_tot_val["g_tot"];
			
		}
		
		$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
		$no_of_days=$no_of_days_val["ser_quantity"];
		
		$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
		$tot_serv_amt1=$tot_serv1["tots"];
		
		$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
		$tot_serv_amt2=$tot_serv2["tots"];
		
		$ot_total=0;
		
		// OT Charge
		$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
		$ot_total=$ot_tot_val["g_tot"];
		
		// Total Amount
		$tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
		
		$total_bill_amount_ipd+=$tot_serv_amt;
		
		/// Payment
		$check_final=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' AND `payment_type`='Final' AND `payment_mode`!='Credit' AND `date`='$date' "));
		
		$total_final_amount_ipd+=$check_final["paid"];
		$total_discount_amount_ipd+=$check_final["discount"];
		$total_refund_amount_ipd+=$check_final["refund"];
		$total_tax_amount_ipd+=$check_final["tax"];
	}
	
	/// Advance
	$check_advance=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`payment_type`='Advance' AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b "));
	
	$advance_paid_ipd      =$check_advance["paid"];
	$advance_discount_ipd  =$check_advance["discount"];
	$advance_refund_ipd    =$check_advance["refund"];
	$advance_tax_ipd       =$check_advance["tax"];
	
	$total_discount_amount_ipd+=$advance_discount_ipd;
	$total_advance_amount_ipd+=$advance_paid_ipd;
	$total_refund_amount_ipd+=$advance_refund_ipd;
	
	// Balance Received
	$check_balance=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`payment_type`='Balance' AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b "));
	
	$balance_paid_ipd      =$check_balance["paid"];
	$balance_discount_ipd  =$check_balance["discount"];
	$balance_refund_ipd    =$check_balance["refund"];
	$balance_tax_ipd       =$check_balance["tax"];
	
	$total_discount_amount_ipd+=$balance_discount_ipd;
	$total_bal_recv_amount_ipd+=$balance_paid_ipd;
	$total_refund_amount_ipd+=$balance_refund_ipd;
	
	// Refund
	$check_refund=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`payment_type`='Refund' AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b "));
	
	$refund_paid_ipd      =$check_refund["paid"];
	$refund_discount_ipd  =$check_refund["discount"];
	$refund_refund_ipd    =$check_refund["refund"];
	$refund_tax_ipd       =$check_refund["tax"];
	
	$total_discount_amount_ipd+=$refund_discount_ipd;
	$total_bal_recv_amount_ipd+=$refund_paid_ipd;
	$total_refund_amount_ipd+=$refund_refund_ipd;
	
	// Net Amount
	$net_amount_ipd=$advance_paid_ipd+$total_final_amount_ipd+$balance_paid_ipd-$total_refund_amount_ipd;
	
	// Credit
	$credit_pat_qry=mysqli_query($link,"SELECT a.* FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b AND a.`payment_mode`='Credit'");
	while($credit_pat=mysqli_fetch_array($credit_pat_qry))
	{
		$credit_amount_opd=0;
		// Same day balance receive
		$pat_pay_bal=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount` FROM `payment_detail_all` WHERE `patient_id`='$credit_pat[patient_id]' and `opd_id`='$credit_pat[opd_id]' AND `payment_type`='Balance' AND `date`='$date' "));
		
		$credit_amount_opd=$credit_pat["balance_amount"]-$pat_pay_bal["paid"]-$pat_pay_bal["discount"];
		$total_credit_amount_ipd+=$credit_amount_opd;
		
	}
	
	// Other Refund
	$ipd_pat_ref=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`refund`),0) AS `tot_refund` FROM `ipd_advance_payment_details` WHERE `pay_type`='Refund' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$patient_visit_type') "));
	$ipd_total_refund+=$ipd_pat_ref["tot_refund"];
	
	$ipd_net_received=$ipd_total_final_paid+$ipd_total_advance_paid+$ipd_total_balance_paid-$ipd_total_refund;
	
	$overall_bill+=$total_bill_amount_ipd;
	$overall_disc+=$total_discount_amount_ipd;
	$overall_amt_rcv+=$total_advance_amount_ipd+$total_final_amount_ipd;
	$overall_bal_rcv+=$total_bal_recv_amount_ipd;
	$overall_ref+=$total_refund_amount_ipd;
	$overall_net+=$net_amount_ipd;
	//$overall_free+=$ipd_all_free['free'];
	$overall_bal+=$total_credit_amount_ipd;
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;"><?php echo $pat_typ_text; ?></button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
		<tr>
			<td>Bill Amount</td>
			<td><?php echo "&#8377 ".number_format($total_bill_amount_ipd,2);?></td>
		</tr>
		<tr>
			<td>Discount</td>
			<td class="red"><?php echo "&#8377 ".number_format($total_discount_amount_ipd,2);?></td>
		</tr>
		<tr>
			<td>Advance Received</td>
			<td class="normal"><?php echo "&#8377 ".number_format($advance_paid_ipd,2);?></td>
		</tr>
		<tr>
			<td>Balance Received</td>
			<td class="normal"><?php echo "&#8377 ".number_format($balance_paid_ipd,2);?></td>
		</tr>
		<tr>
			<td>Final Received</td>
			<td class="normal"><?php echo "&#8377 ".number_format($total_final_amount_ipd,2);?></td>
		</tr>
		<tr>
			<td>Refund</td>
			<td class="red"><?php echo "&#8377 ".number_format($total_refund_amount_ipd,2);?></td>
		</tr>
		<tr>
			<th>Net Amount</th>
			<td class="green"><?php echo "&#8377 ".number_format($net_amount_ipd,2);?></td>
		</tr>
		<tr>
			<td>Credit</td>
			<td class="red"><?php echo "&#8377 ".number_format($total_credit_amount_ipd,2);?></td>
		</tr>
		<tr>
			<td>No of IPD investigation</td>
			<td><?php echo $ipd_test_num;?></td>
		</tr>
		<tr>
			<th>No. of Admission</th>
			<th><?php echo $ipd_new_pat_num;?></th>
		</tr>
		<tr>
			<th>No. Discharged Patients</th>
			<th><?php echo $ipd_discharge_pat_num;?></th>
		</tr>
		<tr>
			<th>No. Admitted Patients</th>
			<th><?php echo $ipd_admit_pat_num;?></th>
		</tr>
		
		<tr>
			<th>Ward Name</th>
			<th>Number of patients</th>
		</tr>
	<?php
		$ward=mysqli_query($link,"SELECT DISTINCT a.`ward_id` FROM `ipd_pat_bed_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`='$date' $branch_str_b");
		while($w=mysqli_fetch_array($ward))
		{
			$c=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(`patient_id`) AS total FROM `ipd_pat_bed_details` WHERE `ward_id`='$w[ward_id]' AND `date`='$date'"));
			$wd=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$w[ward_id]'"));
		?>
		<tr>
			<td><?php echo $wd['name'];?></td>
			<td><?php echo $c['total'];?></td>
		</tr>
	<?php
		}
		?>
	</table>
</div>
</div>
