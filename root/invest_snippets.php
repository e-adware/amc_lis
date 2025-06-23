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
	
	$patient_visit_type="2";
	
	//~ $pat_typ_val=mysqli_fetch_array(mysqli_query($link," SELECT `p_type` FROM `patient_type_master` WHERE `p_type_id`='$patient_visit_type' "));
	//~ $pat_typ_text=$pat_typ_val["p_type"];
	$pat_typ_text="OPD INVESTIGATION";
	
	$tq1=mysqli_query($link," SELECT DISTINCT b.`opd_id` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b");
	$opd_pat_num=mysqli_num_rows($tq1);
	
	$tq1=mysqli_query($link," SELECT a.* FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b");
	$opd_test_num=mysqli_num_rows($tq1);
	
	$card=mysqli_query($link,"SELECT * FROM `testresults_card` WHERE `date`='$date'");
	$rc=mysqli_num_rows($card);
	$radio=mysqli_query($link,"SELECT * FROM `testresults_rad` WHERE `date`='$date'");
	$rr=mysqli_num_rows($card);
	$radio=mysqli_query($link,"SELECT * FROM `testresults_rad` WHERE `date`='$date'");
	$rr=mysqli_num_rows($card);
	$test=mysqli_query($link,"SELECT DISTINCT `patient_id` FROM `testresults` WHERE `date`='$date'");
	$rt=mysqli_num_rows($test);
	$widal=mysqli_query($link,"SELECT DISTINCT `patient_id` FROM `widalresult` WHERE `date`='$date'");
	$rw=mysqli_num_rows($widal);
	
	$tot_done=$rc+$rr+$rt+$rw;
	
	
	$total_bill_amount_lab=$total_discount_amount_lab=$total_advance_amount_lab=$total_bal_recv_amount_lab=$total_refund_amount_lab=$total_credit_amount_lab=$net_amount_lab=0;
	
	// Bill
	$tot_bill_date=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`tot_amount`),0) AS `tot_bill` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b "));
	
	$total_bill_amount_lab=$tot_bill_date["tot_bill"];
	
	// Advance Received
	$check_advance=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b"));
	
	$advance_paid_lab      =$check_advance["paid"];
	$advance_discount_lab  =$check_advance["discount"];
	$advance_refund_lab    =$check_advance["refund"];
	$advance_tax_lab       =$check_advance["tax"];
	
	$total_discount_amount_lab+=$advance_discount_lab;
	$total_advance_amount_lab+=$advance_paid_lab;
	$total_refund_amount_lab+=$advance_refund_lab;
	
	// Balance Received
	$check_balance=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b"));
	
	$balance_paid_lab      =$check_balance["paid"];
	$balance_discount_lab  =$check_balance["discount"];
	$balance_refund_lab    =$check_balance["refund"];
	$balance_tax_lab       =$check_balance["tax"];
	
	$total_discount_amount_lab+=$balance_discount_lab;
	$total_bal_recv_amount_lab+=$balance_paid_lab;
	$total_refund_amount_lab+=$balance_refund_lab;
	
	// Net Amount
	$net_amount_lab=$advance_paid_lab+$balance_paid_lab-$total_refund_amount_lab;
	
	// Credit
	$credit_pat_qry=mysqli_query($link,"SELECT a.* FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND b.`type` IN($patient_visit_type) AND a.`date`='$date' $branch_str_b AND a.`payment_mode`='Credit'");
	while($credit_pat=mysqli_fetch_array($credit_pat_qry))
	{
		$bal_amount=$credit_pat["balance_amount"];
		
		$credit_amount_lab=0;
		// Same day balance receive
		$pat_pay_bal=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount` FROM `payment_detail_all` WHERE `patient_id`='$credit_pat[patient_id]' and `opd_id`='$credit_pat[opd_id]' AND `payment_type`='Balance' AND `date`='$date' AND `pay_id`>$credit_pat[pay_id] "));
		
		$bal_paid_amount=$pat_pay_bal["paid"]+$pat_pay_bal["discount"];
		
		if($bal_paid_amount>$bal_amount)
		{
			$bal_paid_amount=$bal_amount;
		}
		
		$credit_amount_lab=$credit_pat["balance_amount"]-$bal_paid_amount;
		$total_credit_amount_lab+=$credit_amount_lab;
	}
	
	
	$overall_bill+=$total_bill_amount_lab;
	$overall_disc+=$total_discount_amount_lab;
	$overall_amt_rcv+=$advance_paid_lab;
	$overall_bal_rcv+=$balance_paid_lab;
	$overall_ref+=$total_refund_amount_lab;
	$overall_net+=$net_amount_lab;
	//$overall_free+=$tot_free_date_amount;
	$overall_bal+=$total_credit_amount_lab;
	
	if($p_info['levelid']==1)
	{
		$net_det="net_det()";
		$net_clr="net_clr()";
		$bal_det="bal_det()";
		$bal_clr="bal_clr()";
	}
	else
	{
		$net_det="";
		$net_clr="";
		$bal_det="";
		$bal_clr="";
	}
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;"><?php echo $pat_typ_text; ?></button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
		<tr>
			<td>Bill Amount</td>
			<td><?php echo "&#8377 ".number_format($total_bill_amount_lab,2);?></td>
		</tr>
		<tr>
			<td>Discount <span class="bal text-right" style="display:none;color:#EB1B16;"></td>
			<td class="red"><?php echo "&#8377 ".number_format($total_discount_amount_lab,2);?></td>
		</tr>
		<tr>
			<td>
				Amount Received
				<span class="bal text-right" style="display:none;color:#EB1B16;"><b><i class="icon-minus"></i></b></span>
				<span class="net text-right" style="display:none;color:#146C16;"><b><i class="icon-plus"></i></b></span>
			</td>
			<td class="green"><?php echo "&#8377 ".number_format($advance_paid_lab,2);?></td>
		</tr>
		<tr>
			<td>
				Balance Received
				<span class="text-right" style="display:none;color:#146C16;"><b><i class="icon-plus"></i></b></span>
				<span class="net text-right" style="display:none;color:#146C16;"><b><i class="icon-plus"></i></b></span>
			</td>
			<td class="green"><?php echo "&#8377 ".number_format($balance_paid_lab,2);?></td>
		</tr>
		<tr>
			<td>Refunds <span class="bal net text-right" style="display:none;color:#EB1B16;"><b><i class="icon-minus"></i></b></span></td>
			<td class="red"><?php echo "&#8377 ".number_format($total_refund_amount_lab,2);?></td>
		</tr>
		<!--<tr onmouseover="<?php echo $net_det;?>" onmouseout="<?php echo $net_clr;?>">-->
		<tr>
			<th>Net Amount <span class="net text-right" style="display:none;color:#1B25B6;"><b>=</b></span></th>
			<td class="green"><?php echo "&#8377 ".number_format($net_amount_lab,2);?></td>
		</tr>
		<!--<tr onmouseover="<?php echo $bal_det;?>" onmouseout="<?php echo $bal_clr;?>">-->
		<tr>
			<th>Credit <span class="bal text-right" style="display:none;color:#1B25B6;"><b>=</b></span></th>
			<td class="red"><?php echo "&#8377 ".number_format($total_credit_amount_lab,2);?></td>
		</tr>
		<tr>
			<td>No of OPD Investigation Registration</td>
			<td><?php echo $opd_pat_num;?></td>
		</tr>
		<tr>
			<td>No of OPD investigation</td>
			<td><?php echo $opd_test_num;?></td>
		</tr>
	
		<!--<tr>
			<td>No of IPD investigation</td>
			<td><?php echo $ipd_test_num;?></td>
		</tr>-->
	
	<?php if($tot_test>0){ ?>
		<tr>
			<td>Report Done</td>
			<td><?php echo $tot_done;?></td>
		</tr>
		<tr>
			<td>Report Pending</td>
			<td><?php echo $tot_test-$tot_done;?></td>
		</tr>
	<?php } ?>
	</table>
</div>
</div>
<script>
	function bal_det()
	{
		$(".bal").show();
	}
	function bal_clr()
	{
		$(".bal").hide();
	}
	function net_det()
	{
		$(".net").show();
	}
	function net_clr()
	{
		$(".net").hide();
	}
</script>
