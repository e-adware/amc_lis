<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$now_time=date('H:i:s');
$now_date=date("Y-m-d");


$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$edit_ipd_style="style='display:none;'";
if($emp_info["edit_payment"]=="1")
{
	$edit_ipd_style="";
}

$discount_element_disable="";
if($emp_info["levelid"]!=1)
{
	$discount_element_disable="readonly";
}

$time=date('H:i:s');
$date=date("Y-m-d");

$type=$_POST['type'];


if($type==1)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$process=$_POST['process'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$centre_info=mysqli_fetch_array(mysqli_query($link,"SELECT `allow_credit` FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]'"));
	
	$baby_serv_tot=0;
	$baby_ot_total=0;
	$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
	while($delivery_check=mysqli_fetch_array($delivery_qry))
	{
		$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
		$baby_serv_tot+=$baby_tot_serv["tots"];
		
		// OT Charge Baby
		$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
		$baby_ot_total+=$baby_ot_tot_val["g_tot"];
		
	}
	
	$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
	$no_of_days=$no_of_days_val["ser_quantity"];
	
	$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
	$tot_serv_amt1=$tot_serv1["tots"];
	//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
	
	$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
	$tot_serv_amt2=$tot_serv2["tots"];
	
	// OT Charge
	$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
	$ot_total=$ot_tot_val["g_tot"];
	
	// Total
	$tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
	
	$adv_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as advs FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Advance' "));
	$adv_serv_amt=$adv_serv["advs"];
	
	$bal_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as advs,sum(`discount`) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Balance' "));
	$bal_serv_amt=$bal_serv["advs"];
	$bal_serv_dis=$bal_serv["discnt"];
	
	$pat_refund=mysqli_fetch_array(mysqli_query($link," SELECT sum(`refund`) as rfnd FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
	$pat_refund_amt=$pat_refund["rfnd"];
	
	$final_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as final,sum(`discount`) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
	
	$final_serv_amt=$final_serv["final"];
	$adv_serv_dis=$final_serv["discnt"];
	
	$user_access=mysqli_fetch_array(mysqli_query($link," SELECT `edit_ipd`,`cancel_pat` FROM `employee` WHERE `emp_id`='$usr' "));
	if($user_access['edit_ipd']==0)
	{
		$dis_none_cancel_discharge="style='display:none;'";
		$dis_none="style='display:none;'";
	}else
	{
		$dis_none_cancel_discharge="";
		$dis_none="";
	}
	if($user_access['cancel_pat']==0)
	{
		$cancel_pat_btn_dis_none="style='display:none;'";
	}else
	{
		$cancel_pat_btn_dis_none="";
	}
	$final_payment_slno_qry=mysqli_query($link," SELECT `slno` FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' ");
	$final_payment_num=mysqli_num_rows($final_payment_slno_qry);
	$f_pay=0;
	if($final_payment_num>0)
	{
		//$dis_none="style='display:none;'";
		$final_payment_slno=mysqli_fetch_array($final_payment_slno_qry);
		$f_slno=$final_payment_slno['slno'];
		$f_pay=1;
	}
	// Discharge Patient
	$dish_pat_num=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd'"));
?>
	<div>
	<div id="payment_list" class="payment_class">
	<table class="table">
		<tr>
			<th>Total Amount</th>
			<!--<th>Advance Paid</th>-->
			<th>Paid Amount</th>
			<th>Discount Amount</th>
			<th>Total Due</th>
		<?php if($pat_refund_amt>0){ ?>
			<th>Refunded</th>
		<?php } ?>
		</tr>
		<tr>
			<th><?php echo number_format($tot_serv_amt,2); ?></th>
			<th><?php echo number_format($adv_serv_amt+$bal_serv_amt+$final_serv_amt,2); ?></th>
			<th><?php echo number_format($adv_serv_dis+$bal_serv_dis,2); ?></th>
			<th><?php echo number_format(($tot_serv_amt-$adv_serv_amt-$bal_serv_amt-$final_serv_amt-$adv_serv_dis-$bal_serv_dis+$pat_refund_amt),2); ?></th>
		<?php if($pat_refund_amt>0){ ?>
			<th><?php echo number_format(($pat_refund_amt),2); ?></th>
		<?php } ?>
		</tr>
	</table>
	<table class="table">
	<tr><th>#</th><th>Bill No.</th><th>Amount</th><th>Bill Type</th><th>Bill Mode</th><th>Date/Time</th><th>User</th><th></th></tr>	
	<?php
	$i=1;	
	$qry=mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'");
	while($q=mysqli_fetch_array($qry))
	{
		if($dis_none=="")
		{
			$discharge_num=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
			if($discharge_num>0)
			{
				if($f_pay==0)
				{
					$dis_none="";
				}else
				{
					if($f_slno==$q["slno"]){ $en_final=""; }else{ $en_final="style='display:none;'"; }
				}
			}else
			{
				$dis_none="style='display:none;'";
			}
		}
		$user=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$q[user]'"));
		
		if($q["pay_mode"]=="Credit")
		{
			$display_paid_amount=$q["balance"];
		}
		else
		{
			$display_paid_amount=$q["amount"];
		}
		
		echo "<tr><td>$i</td><td>$q[bill_no]</td><td>$display_paid_amount</td><td>$q[pay_type]</td><td>$q[pay_mode]</td><td>".convert_date($q[date])."/".convert_time($q[time])."</td><td>$user[name]</td><td> <input type='button' value='Print' class='btn btn-info' onclick='print_payment_receipt(\"$q[bill_no]\",100)'> <input type='button' value='Edit' class='btn btn-warning' onclick='edit_ipd_paymentmode(\"$q[slno]\")' $edit_ipd_style> <input type='button' value='Cancel Payment' class='btn btn-danger' onclick='cancel_payment_receipt(\"$q[bill_no]\")'/ $dis_none $en_final> </td></tr>";
		$i++;
		//<input type='button' value='Print' class='btn btn-info' onclick='print_payment_receipt(\"$q[bill_no]\",100)'/>
	}
	?>	
	</table>	
	</div>
	
	
	<div id="final_bill" style="display:none" class="payment_class">
	<?php
	
	$final_bill=mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and pay_type='final'");
	$chk_final=mysqli_num_rows($final_bill);
	
		
	if($chk_final>0)
	{
		$btn="display:inline-block";
		$det=mysqli_fetch_array($final_bill);	
		$tot_paid=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot, sum(refund) as ref from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'"));
		
		$tot_bill_paid=$tot_paid["tot"];
		$tot_refunded=$tot_paid["ref"];
		
		$tot_disc=mysqli_fetch_array(mysqli_query($link,"select sum(discount) as tot from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'"));
		
		
		?>
		<table class="table">
			<tr>
				<th>Total Bill Amount</th><th> : <span id="pat_total"><?php echo round($det[tot_amount]);?></span></th>
			</tr>
			<tr>
				<th>Discount</th><th>: <span id="pat_disc"><?php echo $tot_disc[tot];?></span></th>
			</tr>
			<tr>
				<th>Paid</th><th>: <span id="pay_advance"><?php echo $tot_bill_paid;?></span> 
				<input type='hidden' id='bill_id' value="<?php echo $det[bill_no];?>"/></th>
			</tr>
			<tr>
				<th>Balance</th><th>: <span id="pat_balance"><?php echo $det[balance];?></span></th>
			</tr>
	<?php
		if($tot_refunded>0)
		{
	?>
			<tr>
				<th>Refunded</th><th>: <span id="pat_refund_amt"><?php echo $tot_refunded;?></span></th>
			</tr>
	<?php
		}
	?>
			<tr>
				<th>Payment Mode</th>
				<th>: <?php echo $det[pay_mode];?> </th>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<!--<input type="button" id="save_ipd_pay" value="Delete & Re-enter Final Bill" class="btn btn-info" onclick=""/>
					<input type="button" value="View Earlier Payments" class="btn btn-info" onclick="view_ipd_bills()"/>
					<input type="button" value="Generate Final Bill" onclick="print_receipt()" class="btn btn-info"/>-->
					
					<input type="button" value="Print Receipt" class="btn btn-info" onclick="print_payment_receipt('<?php echo $det[bill_no];?>',100)"/>
					
				</td>
			</tr>
			
		</table>
		<?php
	}
	else
	{
		$btn="display:none";
		
		//~ $tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
		//~ $tot_serv_amt1=$tot_serv1["tots"];
		
		//~ $tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
		//~ $tot_serv_amt2=$tot_serv2["tots"];
		
		//~ // OT Charge
		//~ $ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
		//~ $ot_total=$ot_tot_val["g_tot"];
		
		//~ $tot_amount=$tot_serv_amt1+$tot_serv_amt2+$ot_total;
		
		$tot_adv=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'"));
		
		$tot_phar=0;
		
		$tot_bill_paid=$tot_adv[tot]+$tot_phar;
		
		$balance=$tot_serv_amt-$tot_bill_paid;
		
		// Pharmacy
		$tot_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`total_amt`) AS `tot_amount`, SUM(`discount_amt`) AS `dis_amt`, SUM(`paid_amt`) AS `tot_paid`, SUM(`balance`) AS `tot_bal` FROM `ph_sell_master` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
		$ph_pat_bal=$tot_val["tot_bal"];
		
		// Baby Pharmacy
		$delivery_check_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
		if($delivery_check_val)
		{
			$baby_tot_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`total_amt`) AS `tot_amount`, SUM(`discount_amt`) AS `dis_amt`, SUM(`paid_amt`) AS `tot_paid`, SUM(`balance`) AS `tot_bal` FROM `ph_sell_master` WHERE `ipd_id`='$delivery_check_val[baby_uhid]' AND `opd_id`='$delivery_check_val[baby_ipd_id]' "));
			$ph_baby_bal=$baby_tot_val["tot_bal"];
			
			$ph_pat_bal=$ph_pat_bal+$ph_baby_bal;
		}
		
		if($centre_info["allow_credit"]==0)
		{
			$now_pay_vall=$balance;
			$balance_vall=0;
			$credit_check="";
			$now_pay_input_dis="";
			$pay_mode_center="";
		}
		if($centre_info["allow_credit"]==1)
		{
			$now_pay_vall=0;
			$balance_vall=$balance;
			$credit_check="selected";
			$now_pay_input_dis="disabled";
			$pay_mode_center="Credit";
		}
	?>
		<table class="table">
	<?php
		if($delivery_check_val)
		{
	?>
			<tr>
				<th>Patient Bill Amount</th><th> : <span id="pat_total1"><?php echo $tot_serv_amt;?></span></th>
			</tr>
			<tr>
				<th>Baby Bill Amount</th><th> : <span id="pat_baby"><?php echo $baby_serv_tot;?></span></th>
			</tr>
			<tr>
				<th>Total Bill Amount</th><th> : <span id="pat_total"><?php echo ($tot_serv_amt);?></span></th>
			</tr>
	<?php
		}else
		{
	?>
			<tr>
				<th>Total Bill Amount</th><th> : <span id="pat_total"><?php echo $tot_serv_amt;?></span></th>
			</tr>
	<?php
		}
	?>
			<tr>
				<th>Advance Paid</th><th> : <span id="pat_advance"><?php echo $tot_bill_paid;?></span></th>
			</tr>
			<tr>
				<th>Balance</th><th>: <span id="pat_balance"><?php echo $balance_vall; ?></span></th>
			</tr>
		<?php
			if($ph_pat_bal>0)
			{
				$save_btn_dis="style='display:none'";
		?>
			<tr style="color: red;">
				<th>Pharmacy Balance</th><th>: <span id="pharmacy_balance"><?php echo number_format($ph_pat_bal,2);?></span></th>
			</tr>
		<?php
			}
		?>
			<tr>
				<th>Discount</th><th><input type="text" id="pat_disc" onkeyup="pat_discount(this)"/></th>
			</tr>
			<tr id="discount_tr" style="display:none;">
				<th>Discount Reason</th><th><input type="text" id="pat_disc_res" onkeyup="pat_discount_reason(this)"/></th>
			</tr>
		<?php
			if($balance<0)
			{
				$refund_amount_val=($balance*(-1));
				$p_mode_dis="disabled";
		?>
			<tr>
				<th>Refund</th><th>: <span id="pat_refund_amt"><?php echo $refund_amount_val;?></span></th>
			</tr>
			<tr style="display:none;">
				<th>To be paid</th><th><input type="text" id="pay_advance" onkeyup="pat_advance(this)" value="0" readonly /> 
				<input type='hidden' id='bill_id'/></th>
			</tr>
		<?php
			}else
			{
				$refund_amount_val=0;
				$p_mode_dis="";
		?>
			<tr>
				<th>Now Pay</th>
				<th>
					<input type="text" id="pay_advance" onkeyup="pat_advance(this)" value="<?php echo $now_pay_vall;?>" <?php echo $now_pay_input_dis; ?> /> 
					<input type="hidden" id="now_balance_backend" value="<?php echo $balance;?>" disabled /> 
					<input type='hidden' id='bill_id'/>
				</th>
			</tr>
			<tr style="display:none;">
				<th>Refund</th><th>: <span id="pat_refund_amt"><?php echo $refund_amount_val;?></span></th>
			</tr>
		<?php } ?>
			<tr>
				<th>Payment Mode</th>
				<th>
					<select id="p_mode" onchange="pay_mode_change_fn_bal()" <?php echo $p_mode_dis; ?>>
						<!--<option value="Cash">Cash</option>
						<option value="Card">Card</option>
						<option value="Cheque">Cheque</option>
						<option value="Credit" <?php echo $credit_check; ?>>Credit</option>-->
					<?php
						$pay_mode_qry=mysqli_query($link, " SELECT `p_mode_id`,`p_mode_name` FROM `payment_mode_master` WHERE `status`=0 ORDER BY `sequence` ");
						while($pay_mode=mysqli_fetch_array($pay_mode_qry))
						{
							if($pay_mode_center==$pay_mode["p_mode_name"]){ $sel_f="selected"; }else{ $sel_f=""; }
							echo "<option value='$pay_mode[p_mode_name]' $sel_f>$pay_mode[p_mode_name]</option>";
						}
					?>
					</select>
				</th>
			</tr>
			<tr id="fn_bal_reference_no_tr">
				<th>Cheque/Reference No</th>
				<th>
					<input type="text" class="span" id="fn_bal_reference_no">
				</th>
			</tr>
			<tr <?php echo $save_btn_dis; ?>>
				<td colspan="2" style="text-align:center">
					<!--<input type="button" id="save_ipd_pay" value="Save" class="btn btn-info" onclick="save_ipd_payment(this.value)"/>-->
					<input type="button" id="save_ipd_pay" value="Save" class="btn btn-info" onclick="save_ipd_payment_final(this.value)"/>
					
				</td>
			</tr>
			<!--
			<tr>
				<th colspan="2" style="text-align:center"> 
					<input type="button" value="View/Print Bill Summary" onclick="bill_summary()" class="btn btn-info"/>
					<input type="button" value="View Earlier Payments" class="btn btn-info" onclick="view_ipd_bills()"/>
					<input type="button" value="Generate Final Bill" onclick="bill_summary(1)" class="btn btn-info"/>
				</th>
			</tr>
			-->
		</table>
		<?php
		}
		
	?>
	</div>
	
	<div id="recv_advance" class="payment_class" style="display:none">
		<table class="table table-bordered">
			<tr>
				<th style="width: 38%;">Advance</th>
				<th><input type="text" id="adv_payment" /> <input type='hidden' id='adv_bill_id'</th>
			</tr>
			<tr>
				<th>Payment Mode</th>
				<th>
					<select id="p_mode_ad" onchange="pay_mode_change_adv()">
						<!--<option value="Cash">Cash</option>
						<option value="Card">Card</option>
						<option value="Cheque">Cheque</option>
						<option value="Draft">Draft</option>-->
					<?php
						$pay_mode_qry=mysqli_query($link, " SELECT `p_mode_id`,`p_mode_name` FROM `payment_mode_master` WHERE `status`=0 AND `operation`=1 ORDER BY `sequence` ");
						while($pay_mode=mysqli_fetch_array($pay_mode_qry))
						{
							echo "<option value='$pay_mode[p_mode_name]'>$pay_mode[p_mode_name]</option>";
						}
					?>
					</select>
				</th>
			</tr>
			<tr id="adv_reference_no_tr">
				<th>Cheque/Reference No</th>
				<th>
					<input type="text" class="span" id="reference_no_adv">
				</th>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<button class="btn btn-info" onclick="save_ipd_payment(this.value)" value="Save"><i class="icon-save"></i> Save</button>
				</td>
			</tr>
		</table>
	</div>
	<div id="discharge_balance_div" style="display:none" class="payment_class">
		<table class="table">
			<tr>
				<th>Total Bill Amount</th><th> : <span id="pat_totalbd"><?php echo $tot_serv_amt;?></span></th>
			</tr>
			<tr>
				<th>Advance Paid</th><th> : <span id="pat_advancebd"><?php echo $tot_bill_paid;?></span></th>
			</tr>
			<tr>
				<th>Discount</th><th><input type="text" id="pat_discbd" onkeyup="pat_discountbd(this)" value="0" /></th>
			</tr>
		<?php
			if($centre_info["allow_credit"]==0)
			{
				$now_pay_val=$balance;
				$balance_val=0;
			}
			if($centre_info["allow_credit"]==1)
			{
				$now_pay_val=0;
				$balance_val=$balance;
			}
		?>
			<tr>
				<th>Now pay</th><th><input type="text" id="pay_advancebd" onkeyup="pat_advancebd(this)" value="<?php echo $now_pay_val;?>" /> 
				<input type='hidden' id='bill_id' /></th>
			</tr>
			<tr>
				<th>Balance</th><th>: <span id="pat_balancebd"><?php echo $balance_val;?></span></th>
			</tr>
			<tr>
				<th>Payment Mode</th>
				<th>
					<select id="p_modebd">
						<option value="Cash">Cash</option>
						<option value="Card">Card</option>
						<option value="Cheque">Cheque</option>
						<option value="Draft">Draft</option>
					</select>
				</th>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" id="save_ipd_paybd" value="Save & Discharge" class="btn btn-info" onclick="discharge_balance(this.value)"/>
					
				</td>
			</tr>
		</table>
	</div>
	
	
	<hr/>
	
	<?php
	if($chk_final==0)
	{
	?>
	<input type="button" id="gen_final_bill" value="Summary Bill" onclick="bill_summary(2)" class="btn btn-info" />
	<input type="button" id="gen_final_bill" value="Detail Bill" onclick="bill_summary(3)" class="btn btn-info" />
	<button class="btn btn-info" id="recv_adv" onclick="load_payment_div('recv_advance')">Receive Advance</button>
	<?php
		$dis_req_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
		if($process==1)
		{
	?>
	<!--<button class="btn btn-info" onclick="load_payment_div('final_bill')">Receive Final Payment</button>-->
	<button class="btn btn-warning" onclick="load_payment_div('final_bill')">Discharge Patient</button>
	<?php } ?>
	<input type="button" id="gen_final_bill" value="Generate Final Bill" onclick="bill_summary(1)" class="btn btn-info" style="<?php echo $btn;?>"/>
	<?php if($dish_pat_num>0){ ?>
	<!--<input type="button" id="discharge_pat" value="Discharge Patient" onclick="discharge_pat()" class="btn btn-danger" style="<?php echo $btn;?>"/>-->
	<?php
		}
		$adv_pay_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Advance' "));
		if($adv_pay_num==0)
		{
			if($dis_req_num==0)
			{
	?>
		<button class="btn btn-danger" onclick="cancel_casual()" <?php echo $dis_none; ?> >Cancel Patient</button>
	<?php } }
		if($process==1)
		{
	?>
		<!--<button class="btn btn-warning" onclick="load_payment_div('discharge_balance_div')" >Discharge Patient</button>-->
	<?php } ?>
	</div>
	<?php
	}
	else
	{
		$disch=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_discharge_details where patient_id='$uhid' and ipd_id='$ipd'"));
		if($disch>0)
		{
			$btn="display:none";
		}
	?>
		<!--<button class="btn btn-info" id="recv_adv" onclick="load_payment_div('payment_list')">View Payments</button>
		<button class="btn btn-info" onclick="load_payment_div('final_bill')">View Final Payment</button>-->
		<input type="button" id="gen_final_bill" value="Summary Bill" onclick="bill_summary(2)" class="btn btn-info" />
		<input type="button" id="gen_final_bill" value="Detail Bill" onclick="bill_summary(3)" class="btn btn-info" />
		<!--<input type="button" id="gen_final_bill" value="Generate Final Bill" onclick="bill_summary(1)" class="btn btn-info" style="<?php echo $btn;?>"/>-->
		<?php if($dish_pat_num>0){ ?>
		<input type="button" id="discharge_pat" value="Discharge Patient" onclick="discharge_pat()" class="btn btn-warning" style="<?php echo $btn;?>"/>
		<?php }else{ ?>
			<input type="button" id="discharge_pat" value="Cancel Discharged" onclick="cancel_discharge_pat()" class="btn btn-danger" <?php echo $dis_none_cancel_discharge; ?> />
		<?php } ?>
		</div>
	<?php
	}	
		
}
else if($type==2)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$pay_ad=mysqli_real_escape_string($link,$_POST['pay_ad']);
	$reference_no=mysqli_real_escape_string($link,$_POST['reference_no']);
	$mode=$_POST['mode'];
	$user=$_POST['user'];
	
	$val=$_POST['val'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
	$bill_name=$pat_typ_text["bill_name"];
	
	if($val=="Save")
	{
		$bill_no=101;
		$date2=date("Y-m-d");
		$date1=explode("-",$date2);	
		$c_var=$date1[0]."-".$date1[1];
		$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
		
		if($chk['tot_bill']>0)
		{
			$bill_no=$bill_no+$chk['tot_bill'];
		}
		$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
		$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
		
		$date4=date("y-m-d");
		$date3=explode("-",$date4);
		
		$random_no=rand(1,9);
		
		$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
		
		//$test_tot=mysqli_fetch_array(mysqli_query($link,"select sum(test_rate) as tot from bill_patient_test_details where patient_id='$uhid' and ipd_id='$ipd' and deleted='0'"));
		$serv_tot=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd'"));
		//$phr_tot=mysqli_fetch_array(mysqli_query($link,"select sum(net_amount) as tot from bill_ph_sell_details where patient_id='$uhid' and ipd_id='$ipd' and deleted='0' and manip='0'"));
		
		//$tot_amount=$test_tot[tot]+$serv_tot[tot]+$phr_tot[tot];
		$tot_amount=$serv_tot[tot];
		
		$tot_adv=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'"));
		
		$tot_phar=0;
		//~ $pharm=mysqli_query($link,"select distinct(bill_no) from bill_ph_sell_details where patient_id='$uhid' and ipd_id='$ipd' and deleted='0' and manip='0'");
		//~ while($phr=mysqli_fetch_array($pharm))
		//~ {
			//~ $paid=mysqli_fetch_array(mysqli_query($link,"select paid from bill_ph_sell_details where bill_no='$phr[bill_no]'"));
			//~ $tot_phar=$tot_phar+$paid[paid];
		//~ }
		
		
		$tot_paid=$tot_adv[tot]+$pay_ad+$tot_phar;
		$bal=$tot_amount-$tot_paid;
		
		
		
		if(mysqli_query($link,"INSERT INTO `ipd_advance_payment_details`(patient_id, ipd_id, bill_no, tot_amount, discount, amount, balance, refund, pay_type, pay_mode, reference_no, time, date, user) VALUES ('$uhid','$ipd','$bill_id','$tot_amount','0','$pay_ad','$bal','0','Advance','$mode','$reference_no','$time','$date','$user')"))
		{
			echo $bill_id;
		}
		
	}
	else
	{
		$bill=$_POST['bill'];
		mysqli_query($link,"update `ipd_advance_payment_details` set amount='$pay_ad' where bill_no='$bill'");
	}
}
else if($type=="final_2")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$tot=$_POST['tot'];
	$disc=$_POST['disc'];
	$pay=$_POST['pay'];
	$balance=$_POST['balance'];
	$pat_refund_amt=$_POST['pat_refund_amt'];
	$mode=$_POST['mode'];
	$user=$_POST['user'];
	$val=$_POST['val'];
	
	$dis_reason=mysqli_real_escape_string($link,$_POST['dis_reason']);
	$reference_no=mysqli_real_escape_string($link,$_POST['reference_no']);
	
	if($mode=="Credit")
	{
		//$balance=$pay;
		$pay=0;
		$pat_refund_amt=0;
	}
	
	if($pay==0 && $balance>0)
	{
		$mode="Credit";
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
	$bill_name=$pat_typ_text["bill_name"];
	
	if($pat_refund_amt>0)
	{
		$pay=0;
		$mode="Cash";
		$balance=0;
	}
	
	if($pat_refund_amt<0)
	{
		$pat_refund_amt=0;
	}
	
	if($val=="Save")
	{
		if($pay>0 && $balance>0)
		{
			// Two row insert- one --> Pay - two --> Credit(Balance)
			if($pay>0)
			{
				$bill_no=101;
				$date2=date("Y-m-d");
				$date1=explode("-",$date2);	
				$c_var=$date1[0]."-".$date1[1];
				$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
				if($chk['tot_bill']>0)
				{
					$bill_no=$bill_no+$chk['tot_bill'];
				}
				$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
				$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
				
				$date4=date("y-m-d");
				$date3=explode("-",$date4);
				
				$random_no=rand(1,9);
				
				$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
				
				$chk_final=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='$mode' "));
				
				if(!$chk_final)
				{
					$balance_now=0;
					mysqli_query($link,"insert into ipd_advance_payment_details(patient_id, ipd_id, bill_no, tot_amount, discount, amount, balance, refund, pay_type, pay_mode, reference_no, time, date, user) values('$uhid','$ipd','$bill_id','$tot','$disc','$pay','$balance_now','$pat_refund_amt','Final','$mode','$reference_no','$time','$date','$user')");
				
					if($disc>0)
					{
						mysqli_query($link," INSERT INTO `patient_discount_reason`(`patient_id`, `ipd_id`, `bill_no`, `reason`) VALUES ('$uhid','$ipd','$bill_id','$dis_reason') ");
					}
				}
				$qwerty=1;
			}
			if($balance>0)
			{
				$mode="Credit";
				
				$bill_no=101;
				$date2=date("Y-m-d");
				$date1=explode("-",$date2);	
				$c_var=$date1[0]."-".$date1[1];
				$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
				if($chk['tot_bill']>0)
				{
					$bill_no=$bill_no+$chk['tot_bill'];
				}
				$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
				$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
				
				$date4=date("y-m-d");
				$date3=explode("-",$date4);
				
				$random_no=rand(1,9);
				
				$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
				
				$chk_final=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='$mode' "));
				
				if(!$chk_final)
				{
					$pay_now=0;
					$disc_now=0;
					mysqli_query($link,"insert into ipd_advance_payment_details(patient_id, ipd_id, bill_no, tot_amount, discount, amount, balance, refund, pay_type, pay_mode, reference_no, time, date, user) values('$uhid','$ipd','$bill_id','$tot','$disc_now','$pay_now','$balance','$pat_refund_amt','Final','$mode','','$time','$date','$user')");
				
					if($balance>0)
					{
						mysqli_query($link," INSERT INTO `ipd_discharge_balance_pat`(`patient_id`, `ipd_id`, `bal_amount`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$balance','$user','$date','$time') ");
					}
				}
			}
		}
		else
		{
			$bill_no=101;
			$date2=date("Y-m-d");
			$date1=explode("-",$date2);	
			$c_var=$date1[0]."-".$date1[1];
			$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
			if($chk['tot_bill']>0)
			{
				$bill_no=$bill_no+$chk['tot_bill'];
			}
			$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
			$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
			
			$date4=date("y-m-d");
			$date3=explode("-",$date4);
			
			$random_no=rand(1,9);
			
			$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
			
			$chk_final=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' "));
			
			if(!$chk_final)
			{
				mysqli_query($link,"insert into ipd_advance_payment_details(patient_id, ipd_id, bill_no, tot_amount, discount, amount, balance, refund, pay_type, pay_mode, reference_no, time, date, user) values('$uhid','$ipd','$bill_id','$tot','$disc','$pay','$balance','$pat_refund_amt','Final','$mode','$reference_no','$time','$date','$user')");
			
				if($disc>0)
				{
					mysqli_query($link," INSERT INTO `patient_discount_reason`(`patient_id`, `ipd_id`, `bill_no`, `reason`) VALUES ('$uhid','$ipd','$bill_id','$dis_reason') ");
				}
				if($pat_refund_amt>0)
				{
					$adv=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'"));
					$paid_amount=$adv["tot"];
					
					$reason="Advance is greater than bill amount";
					mysqli_query($link," INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','','$paid_amount','$pat_refund_amt','$reason','$date','$time','$user') ");
				}
				
				if($balance>0)
				{
					mysqli_query($link," INSERT INTO `ipd_discharge_balance_pat`(`patient_id`, `ipd_id`, `bal_amount`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$balance','$user','$date','$time') ");
				}
			}
		}
		// Check double entry
		$pay_mode_master_qry=mysqli_query($link," SELECT `p_mode_name` FROM `payment_mode_master` WHERE `status`=0 ORDER BY `p_mode_name` ");
		while($pay_mode_master=mysqli_fetch_array($pay_mode_master_qry))
		{
			$cash_final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='$pay_mode_master[p_mode_name]' ");
			$cash_final_pay_num=mysqli_num_rows($cash_final_pay_qry);
			
			if($cash_final_pay_num>1)
			{
				$h=1;
				while($cash_final_pay_val=mysqli_fetch_array($cash_final_pay_qry))
				{
					if($h>1)
					{
						$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' "));
						if(!$check_pay_mode_change)
						{
							mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `slno`='$cash_final_pay_val[slno]' ");
						}
					}
					$h++;
				}
			}
		}
		////// End
	}
}
else if($type==3)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$name=mysqli_fetch_array(mysqli_query($link,"select name from patient_info where patient_id='$uhid'"));
	?>
	<table class="table">
	<tr><th colspan="2">UHID: <?php echo $uhid;?></th><th colspan="2">IPD ID: <?php echo $ipd;?></th><th colspan="2">Name: <?php echo $name[name];?></th></tr>	
	<tr><th>#</th><th>Bill No.</th><th>Amount</th><th>Date/Time</th><th>User</th><th></th></tr>	
	<?php
	$i=1;	
	$qry=mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'");
	while($q=mysqli_fetch_array($qry))
	{
		$user=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$q[user]'"));
		echo "<tr><td>$i</td><td>$q[bill_no]</td><td>$q[amount]</td><td>".convert_date($q[date])."/".convert_time($q[time])."</td><td>$user[name]</td><td><input type='button' value='Print' class='btn btn-info'/></td></tr>";
		$i++;
	}
	?>	
	</table>	
	<?php
}
else if($type==4)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `bill_patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `batch_no` desc");
	$q=mysqli_query($link,"SELECT * FROM `bill_patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	$no=mysqli_num_rows($ds);
	
	
	$chk_batch=mysqli_num_rows(mysqli_query($link,"select distinct(batch_no) from bill_patient_test_details where batch_no like 'N%'"));
	$n_batch=$chk_batch+1;
	$n_batch="N".$n_batch;
	?>
		<button class="btn btn-info" id="ad" onclick="ad_tests('<?php echo $n_batch;?>')"><i class="icon-plus"></i> Add New Batch</button>
	<?php
	
	if($num>0)
	{
		$total_amount=mysqli_fetch_array(mysqli_query($link,"select sum(test_rate) as tot from bill_patient_test_details where `patient_id`='$uhid' AND `ipd_id`='$ipd' and deleted='0'"));	
		
	?>
	<div class="row">
		<!--<div class="span3">
			<input type="button" class="btn btn-info" id="adm" value="Add New Batch" onclick="ad_tests()" style="" />
		</div>-->
		<div class="span8">
			<b><i>
				<table width="100%">
					<tr><td>Total Amount: <?php echo $total_amount[tot];?></td>
					<td>Total Test  : <?php echo $num;?></td></tr>
				</table>
			</i></b>	
		</div>
	</div>
	<?php
	}
	else
	{
	
	
	}
	
	?>
	
	<div class="span10" style="margin-left:0px;">
		<div class="accordion" id="collapse-group">
		<?php
		if($no>0)
		{
			while($res=mysqli_fetch_array($ds))
			{
				$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `bill_patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
				$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `bill_patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
				$tot=mysqli_fetch_array(mysqli_query($link,"select sum(test_rate) as tot from bill_patient_test_details where `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' and deleted='0'"));
				//echo "<button type='button' class='btn btn-default bt' id='ad".$res['batch_no']."' onclick='view_batch(".$res['batch_no'].")'><span class='sp'>Batch-".$res['batch_no']."</span><span class='sp'>Tests=".$nn."</span><span class='sp'>Date: ".$dt['date']."</span><span class='sp'>Time: ".$dt['time']."</span></button><br/>";
				?>
					<div class="accordion-group widget-box"><!--box 1-->
						<div class="accordion-heading">
							<div class="widget-title">
								<a data-parent="#collapse-group_sub" href="#collapse_sub<?php echo $res[batch_no];?>" data-toggle="collapse" onclick="show_sub_icon('<?php echo $res[batch_no];?>',3)">
									<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;"><?php echo "<span class='sp'>Batch-".$res['batch_no']."</span><span class='sp'>Tests=".$nn."</span><span class='sp'>Date: ".$dt['date']."</span><span class='sp'>Time: ".$dt['time']."</span>";?></b><span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Amount:<?php echo $tot[tot];?></b></i></span><i class="icon-arrow-down" id="ard_sub1"></i><i class="icon-arrow-up" id="aru_sub1" style="display:none;"></i></span>
									<span class="text-right" style="padding:10px;font-size:18px;">
										<span class="iconp_sub" id="plus_sign_sub<?php echo $res[batch_no];?>" style="float:right;"><i class="icon-plus"></i></span>
										<span class="iconm_sub" id="minus_sign_sub<?php echo $res[batch_no];?>" style="float:right;display:none;"><i class="icon-minus"></i></span>
									</span>
								</a>
							</div>
						</div>
						<div class="accordion-body collapse_sub" id="collapse_sub<?php echo $res[batch_no];?>" style="height:0px;overflow-y:scroll;">
							<div class="widget-content hidden_div_sub" id="cl_sub<?php echo $res[batch_no];?>" style="display:none;">
								
							</div>
						</div>
					</div>
				
				
				<?php
			}
		}
		?>
		</div>
	</div>
	<!--
	<div class="span5" style="margin-left:0px;">
	<?php
	if($no>0)
	{
		while($res=mysqli_fetch_array($ds))
		{
			$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
			$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
			echo "<button type='button' class='btn btn-default bt' id='ad".$res['batch_no']."' onclick='view_batch(".$res['batch_no'].")'><span class='sp'>Batch-".$res['batch_no']."</span><span class='sp'>Tests=".$nn."</span><span class='sp'>Date: ".$dt['date']."</span><span class='sp'>Time: ".$dt['time']."</span></button><br/>";
		}
	}
	
	if($num>0)
	{
	?>
	<input type="button" class="btn btn-info" id="adm" value="Add New Batch" onclick="ad_tests()" style="" />
	<?php
	}
	else
	{
	?>
	<input type="button" class="btn btn-info" id="ad" value="Add" onclick="ad_tests()" style="" />
	<?php
	}
	?>
	</div>
	-->
	<div id="batch_details" class="span5" style="margin-left:-40px;max-width:550px;min-width:540px;"></div>
	<style>
		.widget-content{border-bottom:none;}
		.sp{margin-left:10px;}
		.bt{margin-bottom:5px;}
	</style>
	<?php
}
else if($type==5)
{
	//$val=explode(",",$_POST['val']);
	//echo $ar=sizeof($val);
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	?>
	<div class="row">
		<div class="span7">
			<div id="test_sel">
				<div id="list_all_test" style="" class="up_div"></div>
				<!--<h5 class="text-left" onClick="load_tab(2,'a')">Test Details For</h5>-->
				<table class="table" style='border-right:1px solid'>
					<tr>
					<th colspan='2'><label for="test">Select Test</label>
						<input type="text" name="test" id="test" class="span6" onFocus="test_enable()" onKeyUp="select_test_new(this.value,event)" /><input type="text" name="batch" id="batch" style="display:none;" value="<?php echo $batch;?>" />
					</th>
					</tr>
					<tr>
						<td colspan="4">
							<div id="test_d">
								
							</div>
						</td>
					</tr>
				</table>
				</div>
				
			</div>
	
		<div class="span6" style="text-align:right">
			<div id="ss_tests">
					<?php
					$tot_am=0;
					$q=mysqli_query($link,"SELECT * FROM `bill_patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' and deleted='0'");
					$num=mysqli_num_rows($q);
					if($num>0)
					{
					?>
					<table class='table table-condensed table-bordered' style='style:none' id='test_list'>
						<tr><th colspan='3' style='background-color:#cccccc'>Tests</th><th style='text-align:right;background-color:#cccccc'>Total:<span id='test_total'></span></th></tr>
						<?php
						$tot_am=0;
						$i=1;
						while($r=mysqli_fetch_array($q))
						{
							$t=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `testmaster` WHERE `testid`='$r[testid]'"));
						?>
						<tr>
							<td><?php echo $i;?></td>
							<td width='50%'><?php echo $t['testname'];?><input type='hidden' value='<?php echo $r['testid'];?>' class='test_id'/></td>
							<td contentEditable='true' onkeyup='load_cost(2)'><span class='test_f'><?php echo $r[test_rate];?></span></td>
							<td onclick='delete_rows(this,2)'><span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span></td></tr>
						<?php
						$tot_am=$tot_am+$t[rate];
						$i++;
						}
						?>
					</table>
					<script>$("#test_total").text("<?php echo $tot_am;?>")</script>
					<?php
					}
					?>
				</div>
		</div>	
	</div>
	<div class="modal-footer" id="foot">
		<a data-dismiss="modal" onclick="save_test();" class="btn btn-primary" href="#">Save</a>
		<a data-dismiss="modal" onclick="$('#foot').hide();" class="btn btn-info" href="#">Cancel</a>
	</div>
	<?php
	
}
else if($type==6) /*------------Room/Bed Status-----------------*/
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	$user=$_POST['user'];
	
	
	$al_bed=mysqli_query($link,"select * from bill_ipd_bed_details where patient_id='$uhid' and ipd_id='$ipd' and manip!='1' order by date_from asc");
	$chk_stat=mysqli_num_rows($al_bed);
	if($chk_stat>0)
	{
		?>
		<span id="tot_bed_cost"></span>
		
		<table class="table table-bordered table-condensed">
		<tr>
			<th>Ward No</th><th> Bed No</th> <th>Occupied On</th><th>Released On</th><th>Days</th><th>Cost</th><th></th>
		</tr>
		<?php
		$tot=0;
		while($al_b=mysqli_fetch_array($al_bed))
		{
			$bed_no=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$al_b[bed_id]'"));
			$ward=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_no[ward_id]'"));
			echo "<tr><th>$ward[name]</th><th>$bed_no[bed_no]</th><th>".convert_date($al_b[date_from])." </th>";
			echo "<th>".convert_date($al_b[date_to])." </th>";
			
			
			$diff=abs(strtotime($al_b[date_from])-strtotime($al_b[date_to]));
			$diff=$diff/60/60/24;
			
			$tot=$tot+$al_b[tot_amount];
			echo "<th>$diff</th><th>$al_b[tot_amount]</th><th><button' class='btn btn-danger btn-mini' onclick='bed_edit($al_b[slno])'><i class=icon-edit></i> Edit</button></th></tr>";
			?>
				
				<!--
				<table class="table table-condensed">
					<tr>
						<th>Requested Bed</th>
					</tr>
					<tr>
						<th>
							<div id="bed_info">
								Ward No: <?php echo $al_b[ward_id];?> <br/>
								Bed No : <?php echo $bed_no[bed_no];?> 
							</div>
						</th>
					</tr>
					<tr>
						<th>
							<input type="button" class="btn btn-info" value="Request Different Bed" onclick="load_bed_details()"/>
							<input type="button" class="btn btn-info" value="Allocate Bed" onclick="allocate_bed()"/>
						</th>
					</tr>
				</table>
				-->
			<?php
		}
		?> 
		
		</table> 
		<!--<input type="button" class="btn btn-info" value="Bed Tranfer" onclick="load_bed_details()"/>-->
		<button class="btn btn-info" onclick="add_more_bed()"><i class="icon-plus"></i> Add More</button>
		<script>$("#tot_bed_cost").html("<b><i>Total Bed Cost: <?php echo $tot;?></i></b><br/><br/>");</script>
		<?php
	}
}
else if($type==7)
{
	?>
	<h3>Bed Details</h3>
	
	<?php
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$ward=mysqli_query($link,"select * from ward_master order by ward_id");
	while($w=mysqli_fetch_array($ward))
	{
		echo "<div class='ward'>";
		echo "<b>$w[name]</b> <br/>";
		
		
		$i=0;
		$beds=mysqli_query($link,"select distinct room_id,room_no from room_master where ward_id='$w[ward_id]' order by room_no");
		while($b=mysqli_fetch_array($beds))
		{
			echo "<div style='margin:10px 0px 0px 10px'>";
			echo "<b>Room No: $b[room_no]</b> <br/>";
			$room_det=mysqli_query($link,"select * from bed_master where room_id='$b[room_id]'");
			
			while($rd=mysqli_fetch_array($room_det))
			{
				$style="width:50px;margin-left:10px;";
				$chk_bd=mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
				if(mysqli_num_rows($chk_bd)>0)
				{
					if(mysqli_num_rows(mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]' and patient_id='$uhid'"))>0)
					{
						$style.="background-color:#5bc0de";
						echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$b[bed_no]</span>";
					}
					else
					{
						$style.="background-color:#ff8a80";
						echo "<span class='btn' style='$style' id='$rd[bed_id]' >$rd[bed_no]</span>";
					}
				}
				else if($rd[status]==1)
				{
					$style.="background-color:#ffbb33";
					echo "<span class='btn' style='$style' id='$rd[bed_id]' >$rd[bed_no]</span>";
				}
				else
				{
					$chk_bd_main=mysqli_query($link,"select * from ipd_pat_bed_details where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
					
					if(mysqli_num_rows($chk_bd_main)>0)
					{
						
						$chk_bd_ipd=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd' and ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'"));
						if($chk_bd_ipd>0)
						{
							$style.="background-color:#5cb85c;font-weight:bold;text-decoration:underline";
							echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$rd[bed_no]</span>";
						}
						else
						{
							$style.="background-color:#5cb85c";
							echo "<span class='btn' style='$style' id='$rd[bed_id]'>$rd[bed_no]</span>";
						}
						
					}
					else
					{
						echo "<span class='btn' style='$style' id='$b[bed_id]' onclick=\"bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$rd[bed_no]</span>";
					}
				}
				
				
				
				if($i==10)
				{
					$i=0;
					echo "<br/>";
				}
				else
				{
					$i++;
				}
				
			}
			echo "</div>";
		}
		echo "<br/>";
		echo "</div> <hr/>";
	}
	?>
	
	
	<?php
}
else if($type==8)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$user=$_POST[user];
	$w_id=$_POST[w_id];
	$b_id=$_POST[b_id];
	
	
	$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd'"));
	mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,time,date,user) values('$uhid','$ipd','$bed_det[ward_id]','$bed_det[bed_id]','0','$time','$date','$user')");
	mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,time,date,user) values('$uhid','$ipd','$w_id','$b_id','1','$time','$date','$user')");
	mysqli_query($link,"delete from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd'");
	mysqli_query($link,"insert into ipd_pat_bed_details(patient_id,ipd_id,ward_id,bed_id,user,time,date) values('$uhid','$ipd','$w_id','$b_id','$user','$time','$date')");
}
else if($type==9)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$user=$_POST[user];
	
	
	$bd=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd'"));
	mysqli_query($link,"insert into ipd_bed_alloc_details(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`) values('$uhid','$ipd','$bd[ward_id]','$bd[bed_id]','1','$time')");
}
else if($type==10)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd_id'];
	$batch=$_POST['batch_no'];
	$usr=$_POST['user'];
	$q=mysqli_query($link,"SELECT * FROM `bill_patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' order by deleted");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `bill_patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch'"));
	?>
	<table class="table table-condensed table-bordered" style="margin-bottom: 2px;">
		<tr>
			<th>SN</th><th width="40%">Test Name</th><th>Rate</th><th></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$tst_cat=mysqli_fetch_array(mysqli_query($link," SELECT `category_id` FROM `testmaster` WHERE `testid`='$r[testid]' "));
			$tst=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `testmaster` WHERE `testid`='$r[testid]'"));
			$del="";
			$rate=$tst[rate];
			if($r[deleted]==1)
			{
				$del="";
				$rate="<i style='color:red'>Removed</i>";
			}
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $tst['testname'];?></td><td><?php echo $rate;?></td><td><?php echo $del;?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<input type="button" class="btn btn-info" id="adb" value="Update Batch(<?php echo $batch;?>)" onclick="ad_tests('<?php echo $batch;?>')" style="" />
	<?php
	}
}

else if($type==11)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	$usr=$_POST['usr'];
	
	$tot=$_POST[tot];
	$disc=$_POST[disc];
	$adv=$_POST[adv];
	$bal=$_POST[bal];
	
	$tst=$_POST['tst'];
	$test=explode(",",$tst);
	$ar=sizeof($test);
	if($batch!='')
	{
		$bch=$batch;
	}
	else
	{
		$bt=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(batch_no) as max FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$bch=$bt['max']+1;
	}
	//mysqli_query($link," DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch'");
	
	$test_tab=mysqli_query($link,"select * from bill_patient_test_details WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' and batch_no='$batch'");
	while($t_t=mysqli_fetch_array($test_tab))
	{
		if(!in_array($t_t[testid],$test))
		{
			mysqli_query($link,"update bill_patient_test_details set deleted='1' where `patient_id`='$uhid' AND `ipd_id`='$ipd' and batch_no='$batch' and testid='$t_t[testid]'");
		}
	}
	
	
	foreach($test as $test)
	{
		if($test)
		{
			$chk_test=mysqli_num_rows(mysqli_query($link,"select * from bill_patient_test_details WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' and batch_no='$bch' and testid='$test'"));
			
			if($chk_test==0)
			{
				$rt=mysqli_fetch_array(mysqli_query($link,"SELECT `rate` FROM `testmaster` WHERE `testid`='$test'"));
				mysqli_query($link,"INSERT INTO `bill_patient_test_details`(`patient_id`, `ipd_id`, `batch_no`, `testid`,`test_rate`, `date`, `time`, `user` ) VALUES ('$uhid','$ipd','$bch','$test','$rt[rate]','$date','$time','$usr')");
				
				// Delete
				mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' and `testid`='$test' ");
				// Sample ID
				$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test' "));
				// Insert
				mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$bch','$test','$smpl[SampleId]','$rt[rate]','$date','$time','$user','5') ");
				
			}
			else
			{
				$chk_del=mysqli_fetch_array(mysqli_query($link,"select deleted from bill_patient_test_details WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' and batch_no='$bch' and testid='$test'"));
				
				if($chk_del[deleted]==1)
				{
					mysqli_query($link,"update bill_patient_test_details set deleted='0' where patient_id='$uhid' AND `ipd_id`='$ipd' and batch_no='$bch' and testid='$test'");
				}				
				
			}
		}
	}
	
}
else if($type==12)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	$test=$_POST['test'];

	if($test=="")
	{
		$q="select * from testmaster order by testname";
	}
	else
	{
		$q="select * from testmaster where testname like '$test%' order by testname";
	}
	
	$data=mysqli_query($link, $q);
	?>
	<div style="position:absolute;width:50%;" id="test_list_ab">
	<table class="table   table-bordered table-condensed" border="1" id="test_table" width="100%">
		<tr>
			<th>Sl No</th>
			<th>Test Name</th>
			<th>Rate</th><div id="msgg" style="display:none;position:absolute;top:15%;left:45%;font-size:22px;color:#d00;"></div>
		</tr>
	<?php
	$i=1;
	while($d=mysqli_fetch_array($data))
	{
		$drate=$d['rate'];
		
		?>
		<tr <?php echo "id=td".$i;?> onclick="$('#test').focus()" style="cursor:pointer">
			<td width="10%" class=test<?php echo $i;?> id=test<?php echo $i;?>>
				<?php echo $i;?><input type="hidden" class="test<?php echo $i;?>" value="<?php echo $d['testid'];?>"/>
			</td>
			<td style="text-align:left" width="65%" <?php echo "class=test".$i;?>>
				<?php echo $d['testname'];?>
			</td>
			<td class="test<?php echo $i;?>">
				<?php echo $drate=$d['rate'];?>
			</td>
		</tr>
		<?php
		$i++;
	}
		
	?>
	</table>
	</div>
	<?php
	
}
else if($type==13)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$bch=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `batch_no` DESC");
	while($res=mysqli_fetch_array($bch))
	{
		$qq=mysqli_query($link,"SELECT * FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'");
		$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
		?>
		<span><b>Plan: <?php echo $res['batch_no'];?> Date: <?php echo $dt['date']." ".$dt['time'];?></b></span>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th><th width="40%">Drug Name</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Total</th><th>Instructon</th><th>Start Date</th>
			</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($qq))
		{
			$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
			if($r['frequency']==1)
			$fq="Immediately";
			if($r['frequency']==2)
			$fq="Once a day";
			if($r['frequency']==3)
			$fq="Twice a day";
			if($r['frequency']==4)
			$fq="Thrice a day";
			if($r['frequency']==5)
			$fq="Four times a day";
			if($r['frequency']==6)
			$fq="Five times a day";
			if($r['frequency']==7)
			$fq="Every Hour";
			if($r['frequency']==8)
			$fq="Every 2 Hours";
			if($r['frequency']==9)
			$fq="Every 3 Hours";
			if($r['frequency']==10)
			$fq="Every 4 Hours";
			if($r['frequency']==11)
			$fq="Every 5 Hours";
			if($r['frequency']==12)
			$fq="Every 6 Hours";
			if($r['frequency']==13)
			$fq="Every 7 Hours";
			if($r['frequency']==14)
			$fq="Every 8 Hours";
			if($r['frequency']==15)
			$fq="Every 10 Hours";
			if($r['frequency']==16)
			$fq="Every 12 Hours";
			
			if($r['instruction']==1)
			$ins="As Directed";
			if($r['instruction']==2)
			$ins="Before Meal";
			if($r['instruction']==3)
			$ins="Empty Stomach";
			if($r['instruction']==4)
			$ins="After Meal";
			if($r['instruction']==5)
			$ins="In the Morning";
			if($r['instruction']==6)
			$ins="In the Evening";
			if($r['instruction']==7)
			$ins="At Bedtime";
			if($r['instruction']==8)
			$ins="Immediately";
			$sn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_given` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' AND `item_code`='$r[item_code]' and `status`='3'"));
			if($sn>0)
			{
				$ds="disabled='disabled'";
				$val="Stopped";
				$cl="btn-danger";
			}else
			{
				$ds="";
				$val="Update";
				$cl="btn-primary";
			}
			?>
			<tr>
				<td><?php echo $n;?></td>
				<td><?php echo $m['item_name'];?><span class="text-right"><input type="button" class="btn btn-mini <?php echo $cl;?>" onclick="change_med('<?php echo $r['id'];?>')" value="<?php echo $val;?>" style="border-radius:10%;font-weight:bold;" <?php echo $ds;?> /></span></td>
				<td><?php echo $r['dosage'];?></td>
				<td><?php echo $fq;?></td>
				<td><?php echo $r['duration']." ".$r['unit_days'];?></td>
				<td><?php echo $r['total_drugs'];?></td>
				<td><?php echo $ins;?></td>
				<td><?php echo $r['start_date'];?></td>
			</tr>
			<?php
			$n++;
		}
		?>
		</table>
		<?php
	}
	$num=mysqli_num_rows($qq);
	if($num>0)
	{
		?>
		<input type="button" class="btn btn-info" id="ad" value="Add More Plan" onclick="ad_med('','0')" style="" />
		<?php
	}
	else
	{
		?>
		<input type="button" class="btn btn-info" id="ad" value="Add Medication" onclick="ad_med('','0')" style="" />
		<?php
	}
	?>
	<style>
		.widget-content{border-bottom:none;}
	</style>
	<script>
		$("#st_date").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
	</script>
		<?php
}
else if($type==14)
{
	$batch=$_POST['batch'];
	$plan=$_POST['plan'];
	?>
	<div style="height:350px;overflow:scroll;overflow-x:hidden">
	<table class="table table-condensed">
		<tr>
			<th width="15%">Drug Name</th>
			<td colspan="5">
				<input type="text" name="medi" id="medi" class="span5" onFocus="load_medi_list()" onKeyUp="load_medi_list1(this.value,event)" onBlur="javascript:$('#med_div').fadeOut(500)" /> <span id="g_name" style="display:none;"><b>Generic Name</b> <input type="text" id="generic" class="span3" /></span>
				<input type="hidden" id="medid" />
				<div id="med_info"></div>
				<div id="med_div" align="center" style="">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="450px">
						<th>Drug Name</th>
						<?php
							$d=mysqli_query($link, " SELECT * FROM `item_master` where category_id='1' order by `item_name` ");
							$i=1;
							while($d1=mysqli_fetch_array($d))
							{
							?>
								<tr onclick="select_med('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type_id'];?>','<?php echo $d1['generic_name'];?>')" style="cursor:pointer" <?php echo "id=med".$i;?>>
									<td><?php echo $d1['item_name'];?>
										<div <?php echo "id=mdname".$i;?> style="display:none;">
										<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$d1['item_type_id']."#".$d1['generic_name'];?>
										</div>
									</td>
								</tr>
						<?php
								$i++;
							}
						?>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<span id="med_dos" style="display:none;">
						<table class="table table-condensed">
							<tr>
								<th>Dosage</th>
								<td>
									<select id="dos" onkeyup="meditab(this.id,event)" onchange="calc_totday()" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=5;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit</th>
								<td><input type="text" id="unit" readonly="readonly" class="span2" placeholder="Units" /></td>
								<th>Frequency</th>
								<td>
									<select id="freq" onkeyup="meditab(this.id,event)" onchange="calc_totday()" class="span2">
										<option value="0">Select</option>
										<option value="1">Immediately</option>
										<option value="2">Once a day</option>
										<option value="3">Twice a day</option>
										<option value="4">Thrice a day</option>
										<option value="5">Four times a day</option>
										<option value="6">Five times a day</option>
										<option value="7">Every hour</option>
										<option value="8">Every 2 hours</option>
										<option value="9">Every 3 hours</option>
										<option value="10">Every 4 hours</option>
										<option value="11">Every 5 hours</option>
										<option value="12">Every 6 hours</option>
										<option value="13">Every 7 hours</option>
										<option value="14">Every 8 hours</option>
										<option value="15">Every 10 hours</option>
										<option value="16">Every 12 hours</option>
										<!--<option value="17">On alternate days</option>
										<option value="18">Once a week</option>
										<option value="19">Twice a week</option>
										<option value="20">Thrice a week</option>
										<option value="21">Every 2 weeks</option>
										<option value="22">Every 3 weeks</option>
										<option value="23">Once a month</option>-->
									</select>
								</td>
								<th>Start Date</th>
								<td><input type="text" id="st_date" style="width:100px;" onkeyup="meditab(this.id,event)" /></td>
							</tr>
							<tr>
								<th>Duration</th>
								<td>
									<select id="dur" onkeyup="meditab(this.id,event)" onchange="calc_totday()" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=100;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit Days</th>
								<td>
									<select id="unit_day" style="width:80px;" onchange="calc_totday()" onkeyup="meditab(this.id,event)">
										<option value="0">select</option>
										<option value="Days">Days</option>
										<option value="Weeks">Weeks</option>
										<option value="Months">Months</option>
									</select>
								</td>
								<th>Total</th>
								<td><input type="text" id="totl" class="span2" readonly="readonly" /></td>
								<th>Instruction</th>
								<td>
									<select id="inst" style="width:120px;" onkeyup="meditab(this.id,event)">
										<option value="1">As Directed</option>
										<option value="2">Before Meal</option>
										<option value="3">Empty Stomach</option>
										<option value="4">After Meal</option>
										<option value="5">In the Morning</option>
										<option value="6">In the Evening</option>
										<option value="7">At Bedtime</option>
										<option value="8">Immediately</option>
									</select>
								</td>
							</tr>
							<tr>
								<th>SOS</th>
								<td>
									<input type="checkbox" id="sos" class="checkbox" value="sos" />
								</td>
								<th>Consultant Doctor</th>
								<td>
									<select id="con_doc" onkeyup="meditab(this.id,event)">
										<option value="0">Select</option>
										<?php
										$q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master`");
										while($r=mysqli_fetch_array($q))
										{
										?>
										<option value="<?php echo $r['consultantdoctorid'];?>"><?php echo $r['Name'];?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Total Amount</th>
								<td colspan="2">
									<input type="text" id="med_tot_amount" readonly/>
								</td>
							</tr>
						</table>
						<center><input type="button" id="add_medi" class="btn btn-info" value="Add" onclick="set_medi()" /></center>
						<!--<table class="table table-condensed">
							<tr>
								<th>Dosage</th>
								<td>
									<select id="dos" onkeyup="meditab(this.id,event)" style="width:80px;">
										<option value="0">select</option>
										<?php
										for($j=1;$j<=10;$j++)
										{
										?>
										<option value="<?php echo $j;?>"><?php echo $j;?></option>
										<?php
										}
										?>
									</select>
								</td>
								<th>Unit</th>
								<td><input type="text" id="unit" readonly="readonly" class="span2" placeholder="Units" /></td>
								<th>Instruction</th>
								<td><input type="text" id="inst" onkeyup="meditab(this.id,event)" class="span5" placeholder="Instruction" /></td>
							</tr>
						</table>
						<center>
							<input type="button" id="add_medi" class="btn btn-info" value="Add" onclick="set_medi('<?php echo $batch;?>')" /> <!--insert_medi-->
							<!--<input type="button" id="" class="btn btn-danger" value="Close" data-dismiss="modal" onclick="$('#med_list').css('height','100px')" />
						</center>-->
					</span>
				</td>
			</tr>
			<tr id="medi_list" style="display:none;">
				<td id="medi_list_data" colspan="2">
				
				</td>
			</tr>
		</table>
		
		</div>
		
		<div class="modal-footer">
				<a data-dismiss="modal" id="ins_med" onclick="insert_medi();$('#med_list').css('height','100px')" style="display:none;" class="btn btn-primary" href="#">Save</a>
				<a data-dismiss="modal" onclick="$('#med_list').css('height','100px')" class="btn btn-info" href="#">Cancel</a>
			</div>
		
		
		<style>
			.table tr:hover{background:none;}
		</style>
		<script>
			$("#st_date").datepicker(
			{
				dateFormat: 'yy-mm-dd',
				minDate: '0',
			});
		</script>
	<?php
}
else if($type==15)
{
	$dname=$_POST['val'];
	?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="450px">
	<th>Drug Name</th>
<?php

	if($dname)
	{
		$d=mysqli_query($link, "SELECT * FROM `item_master` where category_id='1' and `item_name` like '$dname%' and `item_name`!='' order by `item_name`");
	}
	else
	{
		$d=mysqli_query($link, "SELECT * FROM `item_master` where category_id='1' order by `item_name` and `item_name`!='' ");
	}
	
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		?>
		<tr onclick="select_med('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type_id'];?>','<?php echo $d1['generic_name'];?>')" style="cursor:pointer" <?php echo "id=med".$i;?>>
			<td><?php echo $d1['item_name'];?>
				<div <?php echo "id=mdname".$i;?> style="display:none;">
				<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$d1['item_type_id']."#".$d1['generic_name'];?>
				</div>
			</td>
		</tr>
		<?php
		$i++;
	}
	?>
	</table>
	<?php	
}
else if($type==16)
{
	$id=$_POST['id'];
	$gen=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `generic` WHERE `id`='$id'"));
	echo $gen['name'];
}
else if($type==17) /*-----------insert_medi_ipd---------------*/
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$det=$_POST['det'];
	$det= str_replace("'", "''", "$det");
	$batch=$_POST['batch'];
	$usr=$_POST['usr'];
	if($batch>0)
	{
		$bch=$batch;
	}
	else
	{
		$bt=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(batch_no) as max FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$bch=$bt['max']+1;
	}
	
	$det=explode("#@#",$det);
	foreach($det as $dtt)
	{
		if($dtt)
		{
			$dt=explode("@@",$dtt);
			$medi=$dt[0];
			$plan=$dt[1];
			$dos=$dt[2];
			$unit=$dt[3];
			$freq=$dt[4];
			$dur=$dt[5];
			$unit_day=$dt[6];
			$tot=$dt[7];
			$inst=$dt[8];
			$st_date=$dt[9];
			$con_doc=$dt[10];
			$sos="";
			if($medi && $dos && $freq && $dur && $unit_day && $tot && $inst && $st_date)
			{
				//$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `item_code`='$medi'"));
				//if($num>0)
				//$dos=mysqli_fetch_array(mysqli_query($link,"SELECT `dosage` FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `item_code`='$v1'"));
				//$v2=$dos['dosage']+$v2;
				//mysqli_query($link,"UPDATE `ipd_pat_medicine_check` SET `dosage`='$v2',`instruction`='$v3' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `item_code`='$v1'");
				if($unit_day=="Days")
				{
					$dd=1*$dur;
					$drgs=$tot/$dd;
				}
				else if($unit_day=="Weeks")
				{
					$dd=7*$dur;
					$drgs=$tot/$dd;
				}
				else if($unit_day=="Months")
				{
					$dd=30*$dur;
					$drgs=$tot/$dd;
				}
				if($dd==1)
				{
					$ed=$st_date;
				}
				else
				{
					for($jj=1;$jj<$dd;$jj++)
					$ed=date('Y-m-d', strtotime($st_date . ' +'.$jj.' days'));
				}
				mysqli_query($link,"INSERT INTO `ipd_pat_medicine`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `dosage`, `units`, `frequency`, `start_date`, `end_date`, `duration`, `unit_days`, `total_drugs`, `instruction`, `sos`, `plan`, `consultantdoctorid`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$medi','$dos','$unit','$freq','$st_date','$ed','$dur','$unit_day','$tot','$inst','$sos','$plan','$con_doc','$date','$time','$usr')");
				for($ii=0;$ii<$dd;$ii++)
				{
					$fdt=date('Y-m-d', strtotime($st_date . ' +'.$ii.' days'));
					mysqli_query($link,"INSERT INTO `ipd_pat_medicine_details`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `drugs`, `dosage_date`, `dosage_given`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$medi','$drgs','$fdt','0','$date','$time','$usr')");
				}
			}
		}
	}
}
else if($type==18) /*-------IP Consultation(pat_ipd_ip_consult)-------------*/
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['user'];
	
	/*
	$cons_det=mysqli_query($link,"select * from ipd_ip_consultation where patient_id='$uhid' and ipd_id='$ipd'");
	while($c_d=mysqli_fetch_array($cons_det))
	{
		$chk_cons=mysqli_num_rows(mysqli_query($link,"select * from bill_ipd_ip_consultation where patient_id='$uhid' and ipd_id='$ipd' and consultantdoctorid='$c_d[consultantdoctorid]' and date='$c_d[date]' and time='$c_d[time]'"));
		
		if($chk_cons==0)
		{
			mysqli_query($link,"insert into bill_ipd_ip_consultation(id,patient_id,ipd_id,consultantdoctorid,ipd_fees,date,time,user,deleted) values('$c_d[id]','$uhid','$ipd','$c_d[consultantdoctorid]','$c_d[ipd_fees]','$c_d[date]','$c_d[time]','$usr','0')");
		}
	}
	*/
	
	$ipd_cons_tot=mysqli_fetch_array(mysqli_query($link,"select sum(ipd_fees) as tot from bill_ipd_ip_consultation where patient_id='$uhid' AND ipd_id='$ipd' and deleted='0'"));
	
	echo "<b><i>Total IPD Consultation Amount: <span id='total_ipd_cons_amount'>$ipd_cons_tot[tot]</span></i></b><br/><br/>";
	?>
	
	<?php
	$i=1;
	$qry=mysqli_query($link,"SELECT DISTINCT consultantdoctorid FROM `bill_ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `date`");
	while($res=mysqli_fetch_array($qry))
	{
		
		$tab=5;
		
		$q=mysqli_query($link,"SELECT * FROM `bill_ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND consultantdoctorid='$res[consultantdoctorid]' and deleted='0'");
		
		$num=mysqli_num_rows($q);
		if($num>0)
		{
			$n_id=$tab."_".$i;
			$con=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$res[consultantdoctorid]'"));	
			$vis_tot=mysqli_fetch_array(mysqli_query($link,"select sum(ipd_fees) as tot from bill_ipd_ip_consultation where patient_id='$uhid' AND ipd_id='$ipd' AND consultantdoctorid='$res[consultantdoctorid]' and deleted='0'"));
			?>
				<div class="accordion-group widget-box">
						<div class="accordion-heading">
							<div class="widget-title">
								<a data-parent="#collapse-group_sub" href="#collapse_sub<?php echo $n_id;?>" data-toggle="collapse" onclick="show_sub_icon('<?php echo $n_id;?>',5)">
									<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;"><?php echo "<div class='sub_tab_main'>$con[Name]</div>";?></b><span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Visit: <span class="ipd_cons_total_visit<?php echo $n_id;?>"><?php echo $num;?></span></b></i></span><span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Amount: <span class="ipd_cons_total_amount_sub<?php echo $n_id;?>"><?php echo $vis_tot[tot];?></span></b></i></span><i class="icon-arrow-down" id="ard_sub1"></i><i class="icon-arrow-up" id="aru_sub1" style="display:none;"></i><input type="hidden" id="date_<?php echo $n_id;?>" value="<?php echo $res[consultantdoctorid];?>"/></span>
									<span class="text-right" style="padding:10px;font-size:18px;">
										<span class="iconp_sub" id="plus_sign_sub<?php echo $n_id;?>" style="float:right;"><i class="icon-plus"></i></span>
										<span class="iconm_sub" id="minus_sign_sub<?php echo $n_id;?>" style="float:right;display:none;"><i class="icon-minus"></i></span>
									</span>
								</a>
							</div>
						</div>
						<div class="accordion-body collapse_sub" id="collapse_sub<?php echo $n_id;?>" style="height:0px;overflow-y:scroll;">
							<div class="widget-content hidden_div_sub" id="cl_sub<?php echo $n_id;?>" style="display:none;">
								
							</div>
						</div>
					</div>
			
			<?php
			$i++;
		}
		/*
		if($num>0)
		{
		?>
		<div><b>Date: <?php echo convert_date_g($res['date']);?></b></div>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="5%">#</th><th width="40%">Note</th><th>Doctor</th><th width="15%">Date</th><th width="20%"></th>
			</tr>
			<?php
			$n=1;
			if($res['date']==$date)
			$dis="";
			else
			$dis="disabled='disabled'";
			while($r=mysqli_fetch_array($q))
			{
				$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[consultantdoctorid]'"));
				if($r['note']=="")
				$btn1="Add Note";
				else
				$btn1="Edit Note";
			?>
			<tr>
				<td><?php echo $n;?></td><td><?php echo $r['note'];?></td><td><?php echo $doc['Name'];?></td><td><?php echo convert_date_g($r['date'])." ".convert_time($r['time']);?></td><td><button type="button" class="btn btn-mini btn-info" onclick="ip_note('<?php echo $r['id'];?>')" <?php echo $dis;?>><?php echo $btn1;?></button></td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
		<?php
		}
		if($res['date']==$date)
		{
			?>
			<button type="button" class="btn btn-primary" onclick="ipd_save_note()">Add Doctor</button>
			<?php
		}
		*/
	}
}
else if($type==19) /*-------IP Consultation Tab Details-------------*/
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$val=$_POST['val'];
	$tab_id=$_POST['tab_id'];
	
	$q=mysqli_query($link,"SELECT * FROM `bill_ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `consultantdoctorid`='$val'");
	?>
		<input type="hidden" id="cons_sub_id" value="<?php echo $tab_id;?>"/>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="5%">#</th><th>Date</th><th>Visit Charge</th><th></th>
			</tr>
			<?php
			$n=1;
			if($val==$date)
			$dis="";
			else
			$dis="disabled='disabled'";
			while($r=mysqli_fetch_array($q))
			{
				//$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name`,ipd_visit_fee FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[consultantdoctorid]'"));
				
			?>
			<tr>
				<td><?php echo $n;?></td><td><?php echo convert_date_g($r['date'])." ".convert_time($r['time']);?></td><td><?php echo $r['ipd_fees'];?></td> <td>
					<?php
						if($r[deleted]==1)
						{
							?> <span style="color:red">Deleted</span> <?php
						}
						else if($r[added]==1)
						{
							?> <span style="color:green">Added</span> <?php
						}
					?>
				</td>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
		
		<input type="button" id="update_con" value="Update" class="btn btn-info" onclick="update_consult_details('<?php echo $uhid;?>','<?php echo $ipd;?>',<?php echo $val;?>)"/>
	<?php
	
}
else if($type==20) /*-------------pat_ipd_equipment----------*/
{
	
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$qry=mysqli_query($link,"SELECT distinct equipment_id FROM `bill_ipd_pat_equipment` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($qry);
	
	if($num>0)
	{
		$i=1;
		$tab=7;
		while($q=mysqli_fetch_array($qry))	
		{
			$eq=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_equipment` WHERE `equipment_id`='$q[equipment_id]'"));
			$n_id=$tab."_".$i;
			$eq_time=mysqli_fetch_array(mysqli_query($link,"select sum(hours) as tot_time from bill_ipd_pat_equipment WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' and `equipment_id`='$q[equipment_id]'"));
			
			$eq_cost=mysqli_fetch_array(mysqli_query($link,"select sum(tot_amount) as tot from bill_ipd_pat_equipment WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' and `equipment_id`='$q[equipment_id]'"));
			
			$tot_amount=$eq_cost[tot];
			?>
			<div class="accordion-group widget-box"><!--box 1-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group_sub" href="#collapse_sub<?php echo $n_id;?>" data-toggle="collapse" onclick="show_sub_icon('<?php echo $n_id;?>',7)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;"><div class='sub_tab_main'><?php echo $eq[equipment_name];?></div></b><span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Hours: <span id="eqp_tot_hours<?php echo $n_id;?>"> <?php echo $eq_time[tot_time];?> </span></b></i></span><span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Amount: <span id="eqp_tot_amount<?php echo $n_id;?>"><?php echo $tot_amount;?></span></b></i></span><i class="icon-arrow-down" id="ard_sub1"></i><i class="icon-arrow-up" id="aru_sub1" style="display:none;"></i><input type="hidden" id="equip_<?php echo $n_id;?>" value="<?php echo $q[equipment_id];?>"/></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp_sub" id="plus_sign_sub<?php echo $n_id;?>" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm_sub" id="minus_sign_sub<?php echo $n_id;?>" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse_sub" id="collapse_sub<?php echo $n_id;?>" style="height:0px;overflow-y:scroll;">
					<div class="widget-content hidden_div_sub" id="cl_sub<?php echo $n_id;?>" style="display:none;">
						
					</div>
				</div>
			</div>
			
			
			
			<?php
			$i++;
			
		}
		
	}
	/*
	if($num>0)
	{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Equipment</th><th>No of Hour(s)</th><th>Date</th>
		</tr>
		<?php
		$n=1;
		while($res=mysqli_fetch_array($qry))
		{
			$eq=mysqli_fetch_array(mysqli_query($link,"SELECT `equipment_name` FROM `ipd_equipment` WHERE `equipment_id`='$res[equipment_id]'"));
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $eq['equipment_name'];?></td><td><?php echo $res['hours'];?></td><td><?php echo convert_date_g($res['date'])." ".convert_time($res['time']);?></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<button type="button" id="add_eq" class="btn btn-info" onclick="add_equip()">Add More Equipment</button>
	<?php
	}
	else
	{
	?>
		<button type="button" id="add_eq" class="btn btn-info" onclick="add_equip()">Add Equipment</button>
	<?php
	}
	?>
	<div id="add_equip" style="display:none;">
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Select</th>
				<td>
					<select id="equip">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT `equipment_id`,`equipment_name` FROM `ipd_equipment` ORDER BY `equipment_name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['equipment_id'];?>"><?php echo $r['equipment_name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
				<th>No of Hours</th>
				<td>
					<select id="hour">
						<option value="0">Select</option>
						<?php
						for($i=1;$i<=24;$i++)
						{
						?>
						<option value="<?php echo $i;?>"><?php echo $i;?> Hour(s)</option>
						<?php
						}
						?>
					</select>
				</td>
				<td>
					<button type="button" class="btn btn-info" onclick="save_equip()">Save</button>
					<button type="button" class="btn btn-danger" onclick="close_equip()">Cancel</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
	*/
}

else if($type==21) /*-------------pat_ipd_equipment----------*/
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$val=$_POST['val'];
	$tab=$_POST['tab'];
	?>
		<input type="hidden" value="<?php echo $tab;?>"  id="eq_tab_id"/>
		<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>No of Hour(s)</th><th>Date</th><th>Amount</th><th></th>
		</tr>
	
	<?php
	$n=1;
	$tot_eq_amount=0;
	$tot_eq_hr=0;
	$qry=mysqli_query($link,"SELECT * FROM `bill_ipd_pat_equipment` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' and equipment_id ='$val' and manip='0'");
	while($q=mysqli_fetch_array($qry))
	{
		$eq=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_equipment` WHERE `equipment_id`='$q[equipment_id]'"));
		$amount=$q[tot_amount];
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $q['hours'];?></td><td><?php echo convert_date_g($q['date'])." ".convert_time($q['time']);?></td><td><?php echo $amount;?></td>
			<td><button class="btn btn-danger btn-mini" onclick="edit_equipment(<?php echo $q[slno];?>)"><i class="icon-edit"></i> Edit</button></td>
		</tr>
		<?php
		$n++;
		$tot_eq_amount+=$amount;
		$tot_eq_hr+=$q['hours'];
	}
	?>
	
	</table>
	<script>
		$("#eqp_tot_hours<?php echo $tab;?>").text(<?php echo $tot_eq_hr;?>);
		$("#eqp_tot_amount<?php echo $tab;?>").text(<?php echo $tot_eq_amount;?>);
	</script>
	<button class="btn btn-info"><i class="icon-plus"></i> Add More</button>
	
	<?php
}
else if($type==22) /*-------------pat_ipd_consumable----------*/
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$tab=8;
	$i=0;
	
	$qry_gen=mysqli_query($link,"select * from bill_ipd_pat_consumable where patient_id='$uhid' and ipd_id='$ipd' and type_id='TYPE2'");
	$num_gen=mysqli_num_rows($qry_gen);
	if($num_gen>0)
	{
		$i++;
		$n_id=$tab."_".$i;
		
		$tot_gen=mysqli_fetch_array(mysqli_query($link,"select sum(tot_amount) as tot from bill_ipd_pat_consumable where patient_id='$uhid' and ipd_id='$ipd' and type_id='TYPE2' and manip='0' and deleted='0'"));
		?>
			<div class="accordion-group widget-box">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group_sub" href="#collapse_sub<?php echo $n_id;?>" data-toggle="collapse" onclick="show_sub_icon('<?php echo $n_id;?>',8)">
							<span class="icon" style="width:90%;">
								<b style="padding:10px;font-size:16px;">
									<div class='sub_tab_main'>General</div>
								</b>
								<span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Amount: <span class="ipd_consm_total_amount_sub<?php echo $n_id;?>"><?php echo $tot_gen[tot];?></span></b></i></span><i class="icon-arrow-down" id="ard_sub1"></i><i class="icon-arrow-up" id="aru_sub1" style="display:none;"></i><input type="hidden" id="consm_<?php echo $n_id;?>" value="TYPE2"/></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp_sub" id="plus_sign_sub<?php echo $n_id;?>" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm_sub" id="minus_sign_sub<?php echo $n_id;?>" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse_sub" id="collapse_sub<?php echo $n_id;?>" style="height:0px;overflow-y:scroll;">
					<div class="widget-content hidden_div_sub" id="cl_sub<?php echo $n_id;?>" style="display:none;">
						
					</div>
				</div>
			</div>
		
		<?php		
	}
	
	$qry_sur=mysqli_query($link,"select * from bill_ipd_pat_consumable where patient_id='$uhid' and ipd_id='$ipd' and type_id='TYPE1'");
	$num_sur=mysqli_num_rows($qry_sur);
	if($num_sur>0)
	{
		$i++;
		$n_id=$tab."_".$i;
		
		$tot_sur=mysqli_fetch_array(mysqli_query($link,"select sum(tot_amount) as tot from bill_ipd_pat_consumable where patient_id='$uhid' and ipd_id='$ipd' and type_id='TYPE1' and manip='0' and deleted='0'"));
		?>
			<div class="accordion-group widget-box">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group_sub" href="#collapse_sub<?php echo $n_id;?>" data-toggle="collapse" onclick="show_sub_icon('<?php echo $n_id;?>',8)">
							<span class="icon" style="width:90%;">
								<b style="padding:10px;font-size:16px;">
									<div class='sub_tab_main'>Surgical</div>
								</b>
								<span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Amount: <span class="ipd_consm_total_amount_sub<?php echo $n_id;?>"><?php echo $tot_sur[tot];?></span></b></i></span><i class="icon-arrow-down" id="ard_sub1"></i><i class="icon-arrow-up" id="aru_sub1" style="display:none;"></i><input type="hidden" id="consm_<?php echo $n_id;?>" value="TYPE1"/></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp_sub" id="plus_sign_sub<?php echo $n_id;?>" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm_sub" id="minus_sign_sub<?php echo $n_id;?>" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse_sub" id="collapse_sub<?php echo $n_id;?>" style="height:0px;overflow-y:scroll;">
					<div class="widget-content hidden_div_sub" id="cl_sub<?php echo $n_id;?>" style="display:none;">
						
					</div>
				</div>
			</div>
		
		<?php		
	}
	
}
else if($type==23) /*-------------Update ipd consultation details----------*/
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$doc=$_POST[doc];
	$user=$_POST[usr];
	$tab=$_POST[tab];
	
	$vis=mysqli_fetch_array(mysqli_query($link,"SELECT count(*) as tot FROM `bill_ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND consultantdoctorid='$doc' and deleted='0'"));
	$tot_am=mysqli_fetch_array(mysqli_query($link,"select sum(ipd_fees) as tot from bill_ipd_ip_consultation where patient_id='$uhid' AND ipd_id='$ipd' AND consultantdoctorid='$doc' and deleted='0'"));
	
	?>
	<b>
	Total Visits: <?php echo $vis[tot];?><br/>
	Total Amount: <?php echo $tot_am[tot];?>
	</b>
	<br/><br/>
	
	<script>
		$(".ipd_cons_total_visit<?php echo $tab;?>").text("<?php echo $vis[tot];?>");
		$(".ipd_cons_total_amount_sub<?php echo $tab;?>").text("<?php echo $tot_am[tot];?>");
		
		var total_con_amount=0;
		var total_con_tab=$("[class^=ipd_cons_total_amount_sub]");
		for(var i=0;i<total_con_tab.length;i++)
		{
			var am=parseInt($(total_con_tab[i]).text());
			total_con_amount=total_con_amount+am;
		}
		$("#total_ipd_cons_amount").text(total_con_amount);
	</script>
	
	<div class="row">
		<div class="span4">
			<b><i>Add Consultation</i></b>
			<table class="table table-bordered">
				<tr>
					<td>Doctor</td>
					<td>
						<?php
							$dnm=mysqli_fetch_array(mysqli_query($link,"select * from consultant_doctor_master where consultantdoctorid='$doc'"));
							echo $dnm[Name];
						?>
						
					</td>
				</tr>	
				<tr>
					<td>Date:</td>
					<td><input type="text" name="" id="con_date" class="pin input-group datepicker span2" /></td>
				</tr>
				<tr>
					<td>IPD Fees</td>
					<td><input type="text" id="con_fees" value="<?php echo $dnm[ipd_visit_fee];?>" /></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center"><input type="button" value="Add" class="btn btn-info" onclick="add_bill_consult('<?php echo $uhid;?>','<?php echo $ipd;?>',<?php echo $doc;?>)"/></td>
				</tr>
			</table>
		</div>
		<div class="span6">
	<?php
		$q=mysqli_query($link,"SELECT * FROM `bill_ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `consultantdoctorid`='$doc' order by date");
	?>
		<b><i>Consultation Details</i></b>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="5%">#</th><th>Date</th><th>Visit Charge</th><th></th>
			</tr>
			<?php
			$n=1;
			if($val==$date)
			$dis="";
			else
			$dis="disabled='disabled'";
			while($r=mysqli_fetch_array($q))
			{
				//$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name`,ipd_visit_fee FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[consultantdoctorid]'"));
				
			?>
			<tr>
				<td><?php echo $n;?></td><td><?php echo convert_date_g($r['date'])." ".convert_time($r['time']);?></td><td><?php echo $r['ipd_fees'];?></td>
				<?php if($r[deleted]==0){ $styl=$added=""; if($r[added]==1){ $styl="color:green;"; $added=" - Added"; } ?>
				<td><span class='text-danger' style='cursor:pointer'><i class='icon-remove' style="<?php echo $styl;?>" onclick="remove_consult_details('<?php echo $uhid;?>','<?php echo $ipd;?>',<?php echo $doc;?>,<?php echo $r[slno];?>)"> <?php echo $added;?></i></span></td>
				<?php } else { ?> <td><span style='color:red'>Removed </span></td>            <?php } ?>
			</tr>
			<?php
			$n++;
			}
			?>
		</table>
		
		
		</div>
	</div>
	<script>$(".datepicker").datepicker({ dateFormat: 'yy-mm-dd',maxDate: '0',});</script>
	<?php
}
else if($type==24) /*-------------Update Bill ipd consultation details----------*/
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$doc=$_POST[doc];
	$c_date=$_POST[c_date];
	$c_fees=$_POST[c_fees];
	$user=$_POST[usr];
	
	$id=mysqli_fetch_array(mysqli_query($link,"select max(id) as m from bill_ipd_ip_consultation"));
	$nid=$id[m]+1;
	
	mysqli_query($link,"insert into bill_ipd_ip_consultation(id,patient_id,ipd_id,consultantdoctorid,ipd_fees,date,time,user,added) values('$nid','$uhid','$ipd','$doc','$c_fees','$c_date','$time','$user','1')");
}
else if($type==25) /*-------------Delete Bill ipd consultation details----------*/
{
	$sln=$_POST[slno];
	
	mysqli_query($link,"update bill_ipd_ip_consultation set deleted='1',added='0' where slno='$sln'");
}
else if($type==26) /*-------------Bed Edit Bill ipd bed details----------*/
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$id=$_POST[id];
	
	$det=mysqli_fetch_array(mysqli_query($link,"select * from bill_ipd_bed_details where slno='$id'"));
	$bed_ms=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$det[bed_id]'"));
	?>
		<table class="table table-bordered">
			<tr>
				<th>Ward</th>
				<th>
					<select id="edit_ward" onchange="change_room_bed(this.value,1)">
						<?php
						$wrd=mysqli_query($link,"select * from ward_master");
						while($w=mysqli_fetch_array($wrd))
						{
							if($bed_ms[ward_id]==$w[ward_id]){ $sel="Selected='selected'";} else { $sel="";}
							echo "<option value='$w[ward_id]' $sel>$w[name]</option>";
						}
						?>
					</select>
				</th>
			</tr>
			<tr>
				<th>Room</th>
				<th id="edit_room_th">
					<select id="edit_room" onchange="change_room_bed(this.value,2)">
						<?php
						$room=mysqli_query($link,"select * from room_master where ward_id='$bed_ms[ward_id]'");
						while($rm=mysqli_fetch_array($room))
						{
							if($bed_ms[room_id]==$rm[ward_id]){ $sel1="Selected='selected'";} else { $sel1="";}
							echo "<option value='$rm[room_id]' $sel1>$rm[room_no]</option>";
						}
						?>
					</select>
				</th>
			</tr>
			<tr>
				<th>Bed No</th>
				<th id="edit_bed_th">
					<select id="edit_bed">
						<?php
						$bed=mysqli_query($link,"select * from bed_master where room_id='$bed_ms[room_id]'");
						while($bd=mysqli_fetch_array($bed))
						{
							if($bed_ms[bed_id]==$bd[bed_id]){ $sel2="Selected='selected'";} else { $sel2="";}
							echo "<option value='$bd[bed_id]' $sel2>$bd[bed_no]</option>";
						}
						?>
					</select>
				</th>
			</tr>
			<tr>
				<th>Occupied On</th>
				<th><input type="text" value="<?php echo $det[date_from];?>" id="edit_date_from"/></th>
			</tr>
			<tr>
				<th>Vacated On</th>
				<th><input type="text" value="<?php echo $det[date_to];?>" id="edit_date_to"/></th>
			</tr>
			<tr>
				<th>Total Cost</th>
				<th><input type="text" value="<?php echo $det[tot_amount];?>" id="edit_total_cost"/></th>
			</tr>
			<tr>
				<th colspan="2" style="text-align:center">
					<button class="btn btn-info" onclick="update_bed_details(<?php echo $id;?>)"><i class="icon-save"></i> Update</button>
					<button class="btn btn-danger" onclick="$('#mod').click()"><i class="icon-remove-sign"></i> Cancel</button>
				</th>
			</tr>
		</table>
		
		<script>
		$("#edit_date_from,#edit_date_to").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
	</script>
	<?php
}
else if($type==27) /*-------------Load Rooms/Beds----------*/
{
	$chk=$_POST[chk];
	$val=$_POST[val];
	
	if($chk==1)
	{
		echo "<select id='edit_room' onchange='change_room_bed(this.value,2)'>";
		$room=mysqli_query($link,"select * from room_master where ward_id='$val'");
		while($rm=mysqli_fetch_array($room))
		{
			echo "<option value='$rm[room_id]' $sel1>$rm[room_no]</option>";
		}
		echo "</select>";
		
		echo "@#@penguin#@#";
		
		echo "<select id='edit_bed'>";
		$bed=mysqli_query($link,"select * from bed_master where ward_id='$val'");
		while($bd=mysqli_fetch_array($bed))
		{
			echo "<option value='$bd[bed_id]' $sel2>$bd[bed_no]</option>";
		}
		
		echo "</select>";
	}
	else if($chk==2)
	{
		echo "<select id='edit_bed'>";
		$bed=mysqli_query($link,"select * from bed_master where room_id='$val'");
		while($bd=mysqli_fetch_array($bed))
		{
			echo "<option value='$bd[bed_id]' $sel2>$bd[bed_no]</option>";
		}
	}
}
else if($type==28) /*-------------Load Rooms/Beds----------*/
{
	$id=$_POST[id];
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$e_ward=$_POST[edit_ward];
	$e_room=$_POST[edit_room];
	$e_bed=$_POST[edit_bed];
	$date_f=$_POST[date_f];
	$date_t=$_POST[date_t];
	$amount=$_POST[amount];
	
	mysqli_query($link,"update bill_ipd_bed_details set manip='1' where slno='$id'");
	mysqli_query($link,"insert into bill_ipd_bed_details(patient_id,ipd_id,bed_id,tot_amount,date_from,date_to,manip,old_slno) values('$uhid','$ipd','$e_bed','$amount','$date_f','$date_t','0','$id')");
}
else if($type==29) /*-------------Add More Rooms/Beds----------*/
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	
	?>
		<table class="table table-bordered">
			<tr>
				<th>Ward</th>
				<th>
					<select id="edit_ward" onchange="change_room_bed(this.value,1)" class="imp">
						<option value="0">--Select--</option>
						<?php
						$wrd=mysqli_query($link,"select * from ward_master");
						while($w=mysqli_fetch_array($wrd))
						{
							echo "<option value='$w[ward_id]'>$w[name]</option>";
						}
						?>
					</select>
				</th>
			</tr>
			<tr>
				<th>Room</th>
				<th id="edit_room_th">
					<select id="edit_room" onchange="change_room_bed(this.value,2)" class="imp">
						
					</select>
				</th>
			</tr>
			<tr>
				<th>Bed No</th>
				<th id="edit_bed_th">
					<select id="edit_bed" class="imp">
						
					</select>
				</th>
			</tr>
			<tr>
				<th>Occupied On</th>
				<th><input type="text" id="edit_date_from" class="imp"/></th>
			</tr>
			<tr>
				<th>Vacated On</th>
				<th><input type="text" id="edit_date_to" class="imp"/></th>
			</tr>
			<tr>
				<th>Total Cost</th>
				<th><input type="text" id="edit_total_cost" onfocus="load_bed_cost()" class="imp"/></th>
			</tr>
			<tr>
				<th colspan="2" style="text-align:center">
					<button class="btn btn-info" onclick="save_bed_details()"><i class="icon-save"></i> Save</button>
					<button class="btn btn-danger" onclick="$('#mod').click()"><i class="icon-remove-sign"></i> Cancel</button>
				</th>
			</tr>
		</table>
		
		<script>
		$("#edit_date_from,#edit_date_to").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			//minDate: '0',
		});
	</script>
	<?php	
}
else if($type==30) /*-------------Bed Cost----------*/
{
	$bed_id=$_POST[bed_id];
	$occ=$_POST[occ];
	$vac=$_POST[vac];
	
	$bd=mysqli_fetch_array(mysqli_query($link,"SELECT `charges` FROM `bed_master` WHERE `bed_id`='$bed_id'"));
	
	$diff=abs(strtotime($occ)-strtotime($vac));
	$diff=$diff/60/60/24;
	$chrg=$diff*$bd['charges'];
	
	echo $chrg;
}
else if($type==31) /*-------------Save Bed Details----------*/
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$e_ward=$_POST[edit_ward];
	$e_room=$_POST[edit_room];
	$e_bed=$_POST[edit_bed];
	$date_f=$_POST[date_f];
	$date_t=$_POST[date_t];
	$amount=$_POST[amount];
	
	mysqli_query($link,"insert into bill_ipd_bed_details(patient_id,ipd_id,bed_id,tot_amount,date_from,date_to,manip) values('$uhid','$ipd','$e_bed','$amount','$date_f','$date_t','2')");	
}
else if($type==32) /*-------------Save Bed Details----------*/
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$sl=$_POST[sl];	
	
	$inf=mysqli_fetch_array(mysqli_query($link,"select * from bill_ipd_pat_equipment where slno='$sl'"));
	$e_nm=mysqli_fetch_array(mysqli_query($link,"select * from ipd_equipment where equipment_id='$inf[equipment_id]'"));
	?>
		<table class="table table-bordered">
		<tr>
			<th>Equipment</th>
			<th><?php echo $e_nm[equipment_name];?></th>
		</tr>
		<tr>
			<th>Hours</th>
			<td><input type="text" id="edit_hours" value="<?php echo $inf[hours];?>"/></td>
		</tr>
		<tr>
			<th>Date</th>
			<td><input type="text" id="edit_date" value="<?php echo $inf[date];?>"/></td>
		</tr>
		<tr>
			<th>Cost</th>
			<td><input type="text" value="<?php echo $inf[tot_amount];?>" id="edit_cost"/></td>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center">
				<button class="btn btn-info" onclick="save_eqp_details(<?php echo $sl;?>)"><i class="icon-save"></i> Save</button>
				<button class="btn btn-danger" onclick="$('#mod').click()"><i class="icon-remove-sign"></i> Cancel</button>
			</th>
		</tr>
		</table>
	<?php
}
else if($type==33) /*-------------Save Eq Details----------*/
{
	$id=$_POST[id];
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$edit_h=$_POST[edit_h];
	$edit_d=$_POST[edit_d];
	$amount=$_POST[amount];
	$user=$_POST[user];
	
	$inf=mysqli_fetch_array(mysqli_query($link,"select * from bill_ipd_pat_equipment where slno='$id'"));
	mysqli_query($link,"insert into bill_ipd_pat_equipment(patient_id,ipd_id,equipment_id,hours,tot_amount,date,time,user,old_slno) values('$uhid','$ipd','$inf[equipment_id]','$edit_h','$amount','$date','$time','$user','$id')");
	
	mysqli_query($link,"update bill_ipd_pat_equipment set manip='1' where slno='$id'");
}
else if($type==34) /*-------------Pharmacy----------*/
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	
	
	$ipd_med_tot=mysqli_fetch_array(mysqli_query($link,"select sum(net_amount) as tot from bill_ph_sell_details where ipd_id='$ipd' and deleted='0' and manip='0'"));
	
	if($ipd_med_tot[tot]>0)
	{
		echo "<b><i>Total Amount: <span id='total_ph_amount'>$ipd_med_tot[tot]</span></i></b><br/>";
	}
	?>
	<!--<button id="add_phar" class="btn btn-info" onclick="add_pharmacy_bill_new('')"><i class="icon-plus"></i> Add New</button> <br/><br/>-->
	<?php
	
	$i=1;
	$qry=mysqli_query($link,"SELECT DISTINCT bill_no FROM `bill_ph_sell_details` WHERE `ipd_id`='$ipd'");
	while($res=mysqli_fetch_array($qry))
	{
		
		$tab=9;
		
		$q=mysqli_query($link,"SELECT * FROM `bill_ph_sell_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND bill_no='$res[bill_no]' and deleted='0' and manip='0'");
		
		$num=mysqli_num_rows($q);
		if($num>0)
		{
			$n_id=$tab."_".$i;
			//$med=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_item_master` WHERE `item_code`='$res[item_code]'"));	
			$med_tot=mysqli_fetch_array(mysqli_query($link,"select sum(net_amount) as tot from bill_ph_sell_details where patient_id='$uhid' AND ipd_id='$ipd' AND bill_no='$res[bill_no]' and deleted='0' and manip='0'"));
			?>
				<div class="accordion-group widget-box">
						<div class="accordion-heading">
							<div class="widget-title">
								<a data-parent="#collapse-group_sub" href="#collapse_sub<?php echo $n_id;?>" data-toggle="collapse" onclick="show_sub_icon('<?php echo $n_id;?>',9)">
									<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;"><?php echo "<div class='sub_tab_main'>Bill No: $res[bill_no]</div>";?></b><span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Item: <span class="ipd_sub_ph_total<?php echo $n_id;?>"><?php echo $num;?></span></b></i></span><span style="padding:0px 10px 0px 10px;" class="sp"><i><b>Total Amount: <span class="ipd_sub_ph_total_amount<?php echo $n_id;?>"><?php echo $med_tot[tot];?></span></b></i></span><i class="icon-arrow-down" id="ard_sub1"></i><i class="icon-arrow-up" id="aru_sub1" style="display:none;"></i><input type="hidden" id="bill_<?php echo $n_id;?>" value="<?php echo $res[bill_no];?>"/></span>
									<span class="text-right" style="padding:10px;font-size:18px;">
										<span class="iconp_sub" id="plus_sign_sub<?php echo $n_id;?>" style="float:right;"><i class="icon-plus"></i></span>
										<span class="iconm_sub" id="minus_sign_sub<?php echo $n_id;?>" style="float:right;display:none;"><i class="icon-minus"></i></span>
									</span>
								</a>
							</div>
						</div>
						<div class="accordion-body collapse_sub" id="collapse_sub<?php echo $n_id;?>" style="height:0px;overflow-y:scroll;">
							<div class="widget-content hidden_div_sub" id="cl_sub<?php echo $n_id;?>" style="display:none;">
								
							</div>
						</div>
					</div>
			
			<?php
			$i++;
		}
	}
}
else if($type==35) /*-------------Pharmacy Sub----------*/
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$user=$_POST[user];
	$bill=$_POST[val];
	$tab=$_POST[tab_id];
	
	?>
	
	<input type="hidden" id="ph_sub_id" value="<?php echo $tab;?>"/>	
	<table class="table table-bordered table-condensed table-report btn-mini">
	<tr><th>#</th><th>Item</th><th>Batch No</th><th>Quantity</th><th>MRP</th><th>GST</th><th>Total Amount</th></tr>
	
<?php
	$i=1;
	$tot_amount=0;
	$meds=mysqli_query($link,"select * from bill_ph_sell_details where patient_id='$uhid' and ipd_id='$ipd' and bill_no='$bill' and manip='0'");
	while($m=mysqli_fetch_array($meds))
	{
		$name=mysqli_fetch_array(mysqli_query($link,"select * from ph_item_master where item_code='$m[item_code]'"));
		echo "<tr><td>$i</td><td>$name[item_name]</td><td>$m[batch_no]</td><td>$m[sale_qnt]</td><td>$m[mrp]</td><td>$m[gst_percent]</td><td>$m[net_amount]</td>";
		?> <!--<td><button' class='btn btn-danger btn-mini' onclick='edit_med(<?php echo $m[slno];?>)'><i class=icon-edit></i> Edit</button></td>--></tr> <?php
		$i++;
		$tot_amount+=$m[net_amount];
	}
	?> 
	</table>
	
	<script>
		$(".ipd_sub_ph_total<?php echo $tab;?>").text("<?php echo $i-1;?>")
		$(".ipd_sub_ph_total_amount<?php echo $tab;?>").text("<?php echo $tot_amount;?>")
		
		var all_phar=0;
		var tot_phar=$("[class^=ipd_sub_ph_total_amount]");
		for(var i=0;i<tot_phar.length;i++)
		{
			var ph_cost=parseInt($(tot_phar[i]).text());
			all_phar=all_phar+ph_cost;
		}
		
		$("#total_ph_amount").text(all_phar);
		
	</script>
	
	<!--<button class='btn btn-info' onclick='add_more_pharmacy(<?php echo $bill;?>)'><i class=icon-plus></i> Add More</button> -->
	<?php
}
else if($type==36) /*-------------Edit Pharmacy Row----------*/
{
	$sl=$_POST[sl];
	
	$det=mysqli_fetch_array(mysqli_query($link,"select * from bill_ph_sell_details where slno='$sl'"));
	
	?>
	<table class="table table-condensed table-bordered">
	<tr>
		<th>Item</th>
		<th>
			<select id="edit_item" onchange="load_med_batch(this.value)">
				<?php
				$item=mysqli_query($link,"select * from ph_item_master order by item_name");
				while($it=mysqli_fetch_array($item))
				{
					if($it[item_code]==$det[item_code]) { $sel="Selected='selected'";} else { $sel="";}
					echo "<option value='$it[item_code]' $sel>$it[item_name]</option>";
				}
				?>
				
			</select>
		</th>
	</tr>
	<tr>
		<th>Batch</th>
		<th>
			<select id="edit_batch">
				<?php
				$batch=mysqli_query($link,"select * from ph_stock_master where item_code='$det[item_code]' order by batch_no");
				while($bt=mysqli_fetch_array($batch))
				{
					if($bt[batch_no]==$det[batch_no]) { $sel1="Selected='selected'";} else { $sel1="";}
					echo "<option value='$bt[batch_no]' $sel1>$bt[batch_no]</option>";
				}
				?>
			</select>
		</th>
	</tr>
	<tr>
		<th>Quantity</th>
		<th><input type="text" id="edit_quan" value="<?php echo $det[sale_qnt];?>"  onkeyup="calc_ph_item_cost(this.value)"/></th>
	</tr>
	<tr>
		<th>MRP</th>
		<th><input type="text" id="edit_mrp" value="<?php echo $det[mrp];?>" /></th>
	</tr>
	<tr>
		<th>GST</th>
		<th><input type="text" id="edit_gst" value="<?php echo $det[gst_percent];?>"/></th>
	</tr>
	<tr>
		<th>Net Amount</th>
		<th><input type="text" id="edit_amount" value="<?php echo $det[net_amount];?>"/></th>
	</tr>
	<tr>
		<th colspan="2" style="text-align:center">
			<button class="btn btn-info" onclick="edit_med_row(<?php echo $sl;?>)"><i class="icon-save"></i> Update</button>
			<button class="btn btn-danger" onclick="$('#mod').click()"><i class="icon-remove-sign"></i> Cancel</button>
		</th>
	</tr>
	</table>
	
	<?php
}
else if($type==37) /*-------------Update Pharmacy Row----------*/
{
	$sl=$_POST[sl];
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$item=$_POST[item];
	$batch=$_POST[batch];
	$quan=$_POST[quan];
	$mrp=$_POST[mrp];
	$gst=$_POST[gst];
	$amount=$_POST[amount];
	
	$bill=mysqli_fetch_array(mysqli_query($link,"select * from bill_ph_sell_details where slno='$sl'"));
	
	mysqli_query($link,"INSERT INTO `bill_ph_sell_details`(`bill_no`, `patient_id`, `ipd_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`) VALUES ('$bill[bill_no]','$uhid','$ipd','$bill[entry_date]','$item','$batch','$bill[expiry_date]','$quan','$bill[free_qnt]','$mrp','','$amount','$gst')");
	
	mysqli_query($link,"update bill_ph_sell_details set manip='1' where slno='$sl'");
	
}
else if($type==38)
{
		$bill=$_POST[bill];
		?>
		<table class="table table-condensed table-bordered">
		<tr>
			<th>Item</th>
			<th>
				<select id="edit_item" onchange="load_med_batch(this.value)">
					<option value="0">--Select--</option>
					<?php
					$item=mysqli_query($link,"select * from ph_item_master order by item_name");
					while($it=mysqli_fetch_array($item))
					{
						echo "<option value='$it[item_code]' $sel>$it[item_name]</option>";
					}
					?>
					
				</select>
			</th>
		</tr>
		<tr>
			<th>Batch</th>
			<th>
				<select id="edit_batch">
					<option value="0">--Select--</option>
				</select>
			</th>
		</tr>
		<tr>
			<th>Quantity</th>
			<th><input type="text" id="edit_quan" onkeyup="calc_ph_item_cost(this.value)"/></th>
		</tr>
		<tr>
			<th>MRP</th>
			<th><input type="text" id="edit_mrp" value="<?php echo $det[mrp];?>" /></th>
		</tr>
		<tr>
			<th>GST</th>
			<th><input type="text" id="edit_gst" value="<?php echo $det[gst_percent];?>"/></th>
		</tr>
		<tr>
			<th>Net Amount</th>
			<th><input type="text" id="edit_amount" value="<?php echo $det[net_amount];?>"/></th>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center">
				<button class="btn btn-info" onclick="save_ph_row(<?php echo $bill;?>)"><i class="icon-save"></i> Save</button>
				<button class="btn btn-danger" onclick="$('#mod').click()"><i class="icon-remove-sign"></i> Close</button>
			</th>
		</tr>
		</table>
		<?php
}

else if($type==39)
{
	$val=$_POST[val];
	?>
	<select id="edit_batch">
		<?php
				$batch=mysqli_query($link,"select * from ph_stock_master where item_code='$val' order by batch_no");
				while($bt=mysqli_fetch_array($batch))
				{
					echo "<option value='$bt[batch_no]'>$bt[batch_no]</option>";
				}
		?>
	</select>
	<?php
	
	$det=mysqli_fetch_array(mysqli_query($link,"select * from ph_item_master where item_code='$val'"));
	
	
	echo "@#koushik#@".$det[item_mrp]."@#koushik#@".$det[gst_percent];
}
else if($type==40)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$item=$_POST[item];
	$batch=$_POST[batch];
	$quan=$_POST[quan];
	$mrp=$_POST[mrp];
	$gst=$_POST[gst];
	$amount=$_POST[amount];
	$bill=$_POST[bill];
	
	
	
	mysqli_query($link,"INSERT INTO `bill_ph_sell_details`(`bill_no`, `patient_id`, `ipd_id`, `entry_date`, `item_code`, `batch_no`, `expiry_date`, `sale_qnt`, `free_qnt`, `mrp`, `total_amount`, `net_amount`, `gst_percent`) VALUES ('$bill','$uhid','$ipd','$bill[entry_date]','$item','$batch','$bill[expiry_date]','$quan','$bill[free_qnt]','$mrp','','$amount','$gst')");
}
else if($type==41)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$type_id=$_POST[val];
	$tab=$_POST[tab_id];
	
	
	?>
	<input type="hidden" id="consm_sub_tab" value="<?php echo $tab;?>"/>
	<table class="table table-bordered table-condensed btn-default">
	<tr><th>#</th><th>Item</th><th>Quantity</th><th>MRP</th><th>GST</th><th>Total Amount</th><th>Date</th><th>Time</th><th></th></tr>	
	<?php
	$tot_consm_am=0;
	$i=1;
	$consm=mysqli_query($link,"select * from bill_ipd_pat_consumable where patient_id='$uhid' and ipd_id='$ipd' and type_id='$type_id' and manip='0' and deleted='0'");
	while($cn=mysqli_fetch_array($consm))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_master where slno='$cn[consumable_id]'"));
		echo "<tr><td>$i</td><td>$itm[name]</td><td>$cn[quantity]</td><td>$cn[mrp]</td><td>$cn[gst]</td><td>$cn[tot_amount]</td><td>$cn[date]</td><td>$cn[time]</td><td><button class='btn btn-danger btn-mini' onclick='edit_consm($cn[slno])'><i class='icon-edit'></i> Edit</button></td></tr>";
		
		$i++;
		$tot_consm_am=$tot_consm_am+$cn[tot_amount];
	}
	?>
	</table>
	<script>$(".ipd_consm_total_amount_sub<?php echo $tab;?>").text("<?php echo $tot_consm_am;?>");</script>
	<?php
	
}
else if($type==42)
{
	$val=$_POST[val];
	$cn=mysqli_fetch_array(mysqli_query($link,"select * from bill_ipd_pat_consumable where slno='$val'"));
	?>
	<table class="table table-bordered">
	<tr>
		<th>Item</th>
		<th>
			<select id="gen_item_edit" onchange="load_item_det(this)">
				<?php
				$gen=mysqli_query($link,"select * from inv_indent_master where type_id='$cn[type_id]'");
				while($gn=mysqli_fetch_array($gen))
				{
					if($cn[consumable_id]==$gn[slno]){ $gen_sel="Selected='selected'";} else { $gen_sel=""; }
					echo "<option value='$gn[slno]' $gen_sel>$gn[name]</option>";
				}
				?>
			</select>
		</th>
	</tr>
	<tr>
		<th>Quantity</th>
		<th><input type="text" id="gen_quantity" value="<?php echo $cn[quantity];?>" onkeyup="load_consm_quan(this)"/></th>
	</tr>
	<tr>
		<th>MRP</th>
		<th><input type="text" id="gen_mrp" value="<?php echo $cn[mrp];?>" onkeyup="load_consm_mrp(this)"/></th>
	</tr>
	<tr>
		<th>GST</th>
		<th><input type="text" id="gen_gst" value="<?php echo $cn[gst];?>"  onkeyup="load_consm_gst(this)"/></th>
	</tr>
	<tr>
		<th>Total Amount</th>
		<th><input type="text" id="gen_tot_amount" value="<?php echo $cn[tot_amount];?>"/></th>
	</tr>
	<tr>
		<th>Date</th>
		<th><input type="text" id="gen_date" value="<?php echo $cn[date];?>"/></th>
	</tr>
	<tr>
		<th>Time</th>
		<th><input type="text" id="gen_time" class="timepicker" value="<?php echo $cn[time];?>"/></th>
	</tr>
	<tr>
		<td colspan="2">
			<button class="btn btn-info" onclick="update_consm(<?php echo $val;?>,'<?php echo $cn[type_id];?>')"><i class="icon-save"></i> Update</button>
			<button class="btn btn-danger" onclick="$('#mod').click()"><i class="icon-remove-sign"></i> Close</button>
		</td>
	</tr>
	</table>
	<script>
		$("#gen_date").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
		
		$(".timepicker").timepicker({minutes:{ starts:0, interval:05 }});
	</script>	
	
	<?php
}
else if($type==43)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$item=$_POST[item];
	$quan=$_POST[quan];
	$mrp=$_POST[mrp];
	$gst=$_POST[gst];
	$tot=$_POST[tot];
	$date=$_POST[date];
	$time=$_POST[time];
	$val=$_POST[val];
	$type_id=$_POST[type_id];
	$user=$_POST[user];
	
		
	mysqli_query($link,"update bill_ipd_pat_consumable set manip='1' where slno='$val'");
	
	mysqli_query($link,"insert into bill_ipd_pat_consumable(`patient_id`, `ipd_id`, `consumable_id`, `type_id`, `quantity`, `mrp`, `gst`, `tot_amount`, `date`, `time`, `user`) values('$uhid','$ipd','$item','$type_id','$quan','$mrp','$gst','$tot','$date','$time','$user')");
}


else if($type==100)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$reg=mysqli_fetch_array(mysqli_query($link,"SELECT `regd_fee` FROM `ipd_registration_fees`"));
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<td>Total</td>
			<td><input type="text" id="r_tot" readonly="readonly" value="<?php echo $reg['regd_fee']; ?>" /></td>
		</tr>
		<!--<tr>
			<td>Discount</td>
			<td><input type="text" id="r_disc" placeholder="Discount" /></td>
		</tr>-->
		<tr>
			<td>Paid Amount</td>
			<td><input type="text" id="r_pay" value="<?php echo $reg['regd_fee']; ?>" placeholder="Paid" /></td>
		</tr>
		<tr>
			<td>Payment Mode</td>
			<td>
				<select id="r_pmode">
					<option value="0">Select</option>
					<option value="1">Cash</option>
					<option value="2">Card</option>
					<option value="3">Cheque</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button type='button' class='btn btn-primary' onclick='save_reg_fees()'>Save</button>
				<button type='button' class='btn btn-danger bootbox-close-button' data-dismiss="modal">Cancel</button>
			</td>
		</tr>
	</table>
	<?php
}

else if($type==101)
{
	$n=100;
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$r_tot=$_POST['r_tot'];
	$r_disc=$_POST['r_disc'];
	$r_pay=$_POST['r_pay'];
	$r_pmode=$_POST['r_pmode'];
	$usr=$_POST['usr'];
	$sn=mysqli_fetch_array(mysqli_query($link,"SELECT count(`patient_id`) AS max FROM `ipd_pat_reg_fees`"));
	
	$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(`patient_id`) as max_cancel from `ipd_pat_reg_fees_cancel` "));
	$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
	
	$typ=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	if($typ['type']==3)
	$t="IP/REG";
	if($typ['type']==4)
	$t="CA/REG";
	$s=$sn['max']+$chk_cancel['max_cancel']+$n+1;
	$bill=$s."/".date('m')."/".date('y')."/".$t;
	mysqli_query($link,"INSERT INTO `ipd_pat_reg_fees`(`patient_id`, `ipd_id`, `bill_no`, `total`, `discount`, `paid`, `mode`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bill','$r_tot','$r_disc','$r_pay','$r_pmode','$date','$time','$usr')");
	echo "Saved";
}

else if($type==102)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_reg_fees` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	echo $num;
}

/*--------------------Services Entry--------------------*/

else if($type==110)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$user=$_POST[user];
	
	$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE baby_uhid='$uhid' and baby_ipd_id='$ipd' "));
	if($delivery_check)
	{
		$dischr=mysqli_num_rows(mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$delivery_check[patient_id]' and ipd_id='$delivery_check[ipd_id]' and `pay_type`='Final'"));
	}else
	{
		$dischr=mysqli_num_rows(mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_type`='Final'"));
	}
	
	$selected_group_id="";
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	if($pat_reg["type"]==4)
	{
		$selected_group_id=" AND group_id IN(186)";
	}
	if($pat_reg["type"]==5)
	{
		$selected_group_id=" AND group_id IN(167)";
	}
	if($pat_reg["type"]==6)
	{
		$selected_group_id=" AND group_id IN(192)";
	}
	if($pat_reg["type"]==7)
	{
		$selected_group_id=" AND group_id IN(187)";
	}
	if($pat_reg["type"]==9)
	{
		$selected_group_id=" AND group_id IN(191)";
	}
	if($pat_reg["type"]==14)
	{
		$selected_group_id=" AND group_id IN(193)";
	}
	if($pat_reg["type"]==15)
	{
		$selected_group_id=" AND group_id IN(176,177,185,179,182,184,181,178)";
	}
?>
	
	<div class="row">
	<?php if($dischr==0) { ?>
		
		<div class="span4">
			
			Select Service Group <br/>
			<select id="group" onchange="load_service_list(this)">
				<option value="0">--Select--</option>
				<?php
					$s_master=mysqli_query($link,"select * from charge_group_master WHERE group_id>0 $selected_group_id order by group_name");
					while($s_m=mysqli_fetch_array($s_master))
					{
						echo "<option value='$s_m[group_id]'>$s_m[group_name]</option>";
					}
				?>
			</select> <br/>
			
			
			Select Services <br/>
			<div id="serv_master_list">
				<select id="services" style="width:300px" onchange="load_serv_det(this)">
					<option value="0">--Select--</option>
					<?php
					
					?>
				</select>
			</div>
			<div id="serv_det"></div>
		</div>
		
		<div class="span6">
			<div id="serv_list">
					<script>serv_list()</script>
			</div>
		</div>
		<?php	} else { ?>
			
		<div class="span10">
			<div id="serv_list">
					<script>serv_list()</script>
			</div>
		</div>
		
<?php } ?>	
</div>
	
	
	
	<?php
}
else if($type==109)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$group=$_POST['serv'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	$pat_type=$pat_reg["type"];
	
?>
	<select id="services" style="width:300px" onchange="load_serv_det(this,'pathology')">
		<option value="0">--Select--</option>
<?php
	if($group=='104') // Pathology
	{
		$ser=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='1' ORDER BY `testname`");	
		while($s=mysqli_fetch_array($ser))
		{
			echo "<option value='$s[testid]' $ser_sel>$s[testname]</option>";
		}
	}else if($group=='150') // Cardiology
	{
		$ser=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='3' ORDER BY `testname`");	
		while($s=mysqli_fetch_array($ser))
		{
			echo "<option value='$s[testid]' $ser_sel>$s[testname]</option>";
		}
	}else if($group=='151') // Radiology
	{
		$ser=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='2' ORDER BY `testname`");	
		while($s=mysqli_fetch_array($ser))
		{
			echo "<option value='$s[testid]' $ser_sel>$s[testname]</option>";
		}
	}
	else if($group=='141') // Bed charge
	{
		if($pat_type!=8)
		{
			$ser=mysqli_query($link," SELECT * FROM `charge_master` WHERE `group_id`='$group' AND `charge_id` in (SELECT `charge_id` FROM `bed_master` WHERE `bed_id` in (SELECT DISTINCT(`bed_id`) FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')) ");
		}else
		{
			//$ser=mysqli_query($link,"select * from charge_master where group_id='$group' order by charge_name");
			$ser=mysqli_query($link,"SELECT * FROM `charge_master` WHERE `charge_id` IN(SELECT `charge_id` FROM `bed_master` WHERE `ward_id`='6') order by charge_name");
		}
		while($s=mysqli_fetch_array($ser))
		{
			if($det["service_id"]==$s["charge_id"]){ $ser_sel="Selected='selected'";} else { $ser_sel="";}
			echo "<option value='$s[charge_id]' $ser_sel>$s[charge_name]</option>";
		}
	}
	else
	{
		$ser=mysqli_query($link,"select * from charge_master where group_id='$group' order by charge_name");
		while($s=mysqli_fetch_array($ser))
		{
			if($det["service_id"]==$s["charge_id"]){ $ser_sel="Selected='selected'";} else { $ser_sel="";}
			echo "<option value='$s[charge_id]' $ser_sel>$s[charge_name]</option>";
		}
	}
?>
	</select>
<?php
}
else if($type==111)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$today=date("Y-m-d");
	$serv=$_POST['serv'];
	$group_id=$_POST['group_id'];
	$typ=$_POST['typ'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	$center_no=$pat_reg["center_no"];
	
	if($group_id=='104' || $group_id=='151' || $group_id=='150')
	{
		$centre_rate=mysqli_fetch_array(mysqli_query($link,"SELECT a.`testname`,a.`rate` AS `m_rate`,b.`rate` AS `c_rate` FROM `testmaster` a, `testmaster_rate` b WHERE a.`testid`=b.`testid` AND b.`centreno`='$center_no' AND b.`testid`='$serv'"));
		if($centre_rate)
		{
			$ser_name=$centre_rate['testname'];
			$ser_rate=$centre_rate['c_rate'];
		}
		else
		{
			$det=mysqli_fetch_array(mysqli_query($link,"SELECT `testname`,`rate` FROM `testmaster` WHERE `testid`='$serv'"));
			$ser_name=$det['testname'];
			$ser_rate=$det['rate'];
		}
	}else
	{
		$centre_rate=mysqli_fetch_array(mysqli_query($link,"SELECT a.`charge_name`,a.`amount` AS `m_rate`, b.`rate` AS `c_rate` FROM `charge_master` a,`service_rate` b WHERE a.`charge_id`=b.`charge_id` AND b.`centreno`='$center_no' AND b.`charge_id`='$serv'"));
		if($centre_rate)
		{
			$ser_name=$centre_rate['charge_name'];
			$ser_rate=$centre_rate['c_rate'];
		}
		else
		{
			$det=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$serv'"));
			$ser_name=$det['charge_name'];
			$ser_rate=$det['amount'];
		}
	}
	
	if($det["doc_link"]==1)
	{
		$check_doc=1;
	}else
	{
		$check_doc=0;
	}
	$rate_dis="";
	if($group_id=='141')
	{
		//$rate_dis="disabled";
	}
	?>
	<table class="table table-bordered table-condensed">
	<?php
	
	
	echo "<tr><th>Text</th><th><input type='text' id='serv_text' value='$ser_name'/></th></tr>";
	if($group_id=="142" && $serv!='820')
	{
	?>
		<tr>
			<th>Doctor</th>
			<th>
				<select id="consultantdoctorid">
					<option value="0">Select</option>
				<?php
					$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` order by `Name` ");
					while($con_doc=mysqli_fetch_array($con_doc_qry))
					{
						echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
					}
				?>
				</select>
			</th>
		</tr>
	<?php
	//}else if($group_id=="155" && ($serv=="1357" || $serv=="1358" || $serv=="1359"))
	}else if($check_doc==1)
	{
	?>
		<tr>
			<th>Doctor/Employee</th>
			<th>
				<select id="consultantdoctorid">
					<option value="0">Select</option>
				<?php
					$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` order by `Name` ");
					while($con_doc=mysqli_fetch_array($con_doc_qry))
					{
						echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
					}
				?>
				</select>
			</th>
		</tr>
	<?php
	}
	else
	{
		echo "<input type='hidden' id='consultantdoctorid' value='99999'>";
	}
	if($det[days]==1)
	{
		echo "<tr><th>No of Days</th><th><input type='text' id='days'/></th></tr>";
	}
	
	echo "<tr><th>Rate</th><th><input type='text' id='rate' value='$ser_rate' $rate_dis /></th></tr>";
	echo "<tr><th>Date</th><th><input type='text' class='datepicker' id='ser_entry_date' value='$today'/></th></tr>";
	//echo "<tr><th>Quantity</th><th><input type='text' id='ser_quantity' value='1'/></th></tr>";
	?>
	<tr>
		<th>Quantity</th>
		<td>
			<select id='ser_quantity'>
			<?php
				for($i=1;$i<101;$i++)
				{
					echo "<option value='$i'>$i</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center">
			<input type="button" class="btn btn-info" value="Add" onclick="save_services(this)"/>
		</td>
	</tr>
	</table>
	<?php
}
else if($type==112)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$user=$_POST[user];
	$group=$_POST[group];
	$serv=$_POST[serv];
	$serv_text=mysqli_real_escape_string($link,$_POST[serv_text]);
	$days=$_POST[days];
	$rate=$_POST[rate];
	$ser_entry_date=$_POST[ser_entry_date];
	$ser_quantity=$_POST[ser_quantity];
	$consultantdoctorid=$_POST[consultantdoctorid];
	
	$amount=$rate*$ser_quantity;
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$bed=mysqli_fetch_array(mysqli_query($link," SELECT `bed_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
	
	if(!$days)
	{
		$days=0;
		if($group==141)
		{
			$days=1;
		}
		
	}
	
	$last_bed=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and alloc_type='1' order by slno DESC"));
	
	$bed_idd=$last_bed["bed_id"];
	
	mysqli_query($link,"insert into ipd_pat_service_details(patient_id,ipd_id,group_id,service_id,service_text,ser_quantity,rate,amount,days,user,time,date,bed_id) values('$uhid','$ipd','$group','$serv','$serv_text','$ser_quantity','$rate','$amount','$days','$user','$time','$ser_entry_date','$bed[bed_id]')");
	
	if($group==141)
	{
		$bed_sr=mysqli_fetch_array(mysqli_query($link," SELECT `bed_id` FROM `bed_master` WHERE `charge_id`='$serv' "));
		$bed_idd=$bed_sr['bed_id'];
		
		$other_charge=mysqli_query($link,"select * from bed_other_charge where bed_id='$bed_idd'");
		while($ot_ch=mysqli_fetch_array($other_charge))
		{
			$charge=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$ot_ch[charge_id]'"));
			
			mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','$charge[group_id]','$ot_ch[charge_id]','$charge[charge_name]','1','$charge[amount]','$charge[amount]','1','$user','$time','$ser_entry_date','$bed_idd')");
		}
		
		// IPD Service Free
		//~ mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and `group_id`='173' and service_id='1354' ");
		
		//~ $tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
		//~ $tot_serv_amt1=$tot_serv1["tots"];
		
		//~ $charge_master_val=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where `group_id`='173' and charge_id='1354' "));
		
		//~ $charge_amount=($tot_serv_amt1/100)*$charge_master_val["amount"];
		
		//~ $first_bed=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and alloc_type='1' order by slno"));
		
		//~ mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','173','1354','$charge_master_val[charge_name]','1','$charge_amount','$charge_amount','1','$_SESSION[emp_id]','$pat_reg[time]','$pat_reg[date]','$first_bed[bed_id]')");
		
	}
	
	$last_slno=mysqli_fetch_array(mysqli_query($link," SELECT MAX(`slno`) AS `maxslno` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group' AND `service_id`='$serv' AND `user`='$user' "));
	$rel_slno=$last_slno["maxslno"];
	
	if($consultantdoctorid!='99999')
	{
		mysqli_query($link," INSERT INTO `doctor_service_done`(`patient_id`, `ipd_id`, `service_id`, `consultantdoctorid`, `user`, `date`, `time`, `rel_slno`) VALUES ('$uhid','$ipd','$serv','$consultantdoctorid','$user','$ser_entry_date','$time','$rel_slno') ");
	}
	
	if($group=='104' || $group=='151' || $group=='150')
	{
		$test=$serv;
		
		$bt=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(batch_no) as max FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$bch=$bt['max']+1;
		
		$sam=mysqli_fetch_array(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$test'"));
		$rt=mysqli_fetch_array(mysqli_query($link,"SELECT `rate` FROM `testmaster` WHERE `testid`='$test'"));
		
		$sample_id=$sam["SampleId"];
		if(!$sample_id){ $sample_id=0; }
		
		mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$bch','$test','$sample_id','$rt[rate]','0','$date','$time','$user','5')");
		
		$last_slno_test=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `testid`='$test' AND `user`='$user' ORDER BY `slno` DESC "));
			
		$last_slno_service=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='104' AND `service_id`='$test' ORDER BY `slno` DESC "));
		
		mysqli_query($link," INSERT INTO `link_test_service`(`test_slno`, `service_slno`) VALUES ('$last_slno_test[slno]','$last_slno_service[slno]') ");
		
		// Add On Test
		$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test' ");
		while($s_t=mysqli_fetch_array($sub_tst))
		{
			$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
			
			$sample_id=$samp_sb["SampleId"];
			if(!$sample_id){ $sample_id=0; }
			
			mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','','1','$s_t[sub_testid]','$sample_id','0','0','$date','$time','$user','4') ");
		}
		
		// 5=IPD Dashboad Test add
	}
	
	if($group=="194")
	{
		mysqli_query($link," DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='141' "); // Bed Charge
		mysqli_query($link," DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='148' "); // Bed Charge Plus
	}
	
}
else if($type==113)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$rupees_symbol="&#x20b9; ";
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE patient_id='$uhid' and opd_id='$ipd' "));
	
?>
	<div class="accordion" id="collapse-group_ser">	
<?php
	$dictinct_group_qry=mysqli_query($link," SELECT DISTINCT(`group_id`) FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' ORDER BY `group_id` ");
	
	$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE baby_uhid='$uhid' and baby_ipd_id='$ipd' "));
	if($delivery_check)
	{
		$dischr=mysqli_num_rows(mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$delivery_check[patient_id]' and ipd_id='$delivery_check[ipd_id]' and `pay_type`='Final'"));
	}else
	{
		$dischr=mysqli_num_rows(mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and `pay_type`='Final'"));
	}
	
	$onclk='load_edit($q[slno])';
	$grand_tot=0;
	while($dictinct_group=mysqli_fetch_array($dictinct_group_qry))
	{
		$grp=mysqli_fetch_array(mysqli_query($link," SELECT `group_name` FROM `charge_group_master` WHERE `group_id`='$dictinct_group[group_id]' "));
		$grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='$dictinct_group[group_id]'"));
		$grp_tot=$grp_tot_val["g_tot"];
	?>
		<div class="accordion-group widget-box"> 
			<div class="accordion-heading">
				<div class="widget-title">
					<a data-parent="#collapse-group_ser" href="#collapse<?php echo $dictinct_group["group_id"]; ?>_ser" data-toggle="collapse" onclick="show_sub_ser('<?php echo $dictinct_group["group_id"]; ?>')">
						<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;"><?php echo $grp["group_name"]; ?><span class="text-right"><?php echo $rupees_symbol.number_format($grp_tot,2); ?></span></b><i class="icon-arrow-down_ser" id="ard<?php echo $dictinct_group["group_id"]; ?>_ser"></i><i class="icon-arrow-up_ser" id="aru<?php echo $dictinct_group["group_id"]; ?>_ser" style="display:none;"></i></span>
						<span class="text-right" style="padding:10px;font-size:18px;">
							<span class="iconp_ser" id="plus_sign<?php echo $dictinct_group["group_id"]; ?>_ser" style="float:right;"><i class="icon-plus"></i></span>
							<span class="iconm_ser" id="minus_sign<?php echo $dictinct_group["group_id"]; ?>_ser" style="float:right;display:none;"><i class="icon-minus"></i></span>
						</span>
					</a>
				</div>
			</div>
			<div class="accordion-body collapse" id="collapse<?php echo $dictinct_group["group_id"]; ?>_ser" style="height:0px;max-height:450px;overflow-y:scroll;">
				<div class="widget-content hidden_div_ser" id="cl<?php echo $dictinct_group["group_id"]; ?>_ser" style="display:none;">
					<table class="table table-condensed table-bordered table-report">
						<tr>
							<th>#</th><th>Service</th><th>Quantity</th><th>Amount</th><th>Time</th><th>Date</th>
						</tr>
					<?php
						$i=1;
						$tot_amount=0;
						$qry=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='$dictinct_group[group_id]' order by date,slno");
						while($q=mysqli_fetch_array($qry))
						{
							if($dictinct_group['group_id']==141)
							{
								$tot_days=$q['ser_quantity'];
							}else
							{
								$tot_days=$q['days'];
							}
							$tot_days=$q['ser_quantity'];
							$snm=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$q[service_id]'"));
							if($dischr>0)
							{
								if($emp_info["levelid"]==1)
								{
									echo "<tr onclick='load_edit($q[slno])' style='cursor:pointer'><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";
								}
								else
								{
									echo "<tr><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";	
								}
							}
							else
							{
								echo "<tr onclick='load_edit($q[slno])' style='cursor:pointer'><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";
							}
							
							$i++;
							
							$tot_amount=$tot_amount+$q[amount];
							
						}
					?>
					<tr>
						<th colspan="3"><span class="text-right">Total</span></th>
						<th colspan="3"><?php echo $rupees_symbol.number_format($tot_amount,2);?></th>
					</tr>
					</table>
				</div>
			</div>
		</div>
	<?php
	$grand_tot+=$tot_amount;
	}
	if($pat_reg["type"]==3)
	{
	// OT Services
	
		//$grp=mysqli_fetch_array(mysqli_query($link," SELECT `group_name` FROM `charge_group_master` WHERE `group_id`='$dictinct_group[group_id]' "));
		$grp["group_name"]="OT Charge";
		$dictinct_group["group_id"]=155;
		
		$grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
		$grp_tot=$grp_tot_val["g_tot"];
		if($grp_tot>0)
		{
	?>
		<div class="accordion-group widget-box"> 
			<div class="accordion-heading">
				<div class="widget-title">
					<a data-parent="#collapse-group_ser" href="#collapse155555_ser" data-toggle="collapse" onclick="show_sub_ser('155555')">
						<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;"><?php echo $grp["group_name"]; ?><span class="text-right"><?php echo $rupees_symbol.number_format($grp_tot,2); ?></span></b><i class="icon-arrow-down_ser" id="ard155555_ser"></i><i class="icon-arrow-up_ser" id="aru155555_ser" style="display:none;"></i></span>
						<span class="text-right" style="padding:10px;font-size:18px;">
							<span class="iconp_ser" id="plus_sign155555_ser" style="float:right;"><i class="icon-plus"></i></span>
							<span class="iconm_ser" id="minus_sign155555_ser" style="float:right;display:none;"><i class="icon-minus"></i></span>
						</span>
					</a>
				</div>
			</div>
			<div class="accordion-body collapse" id="collapse155555_ser" style="height:0px;max-height:450px;overflow-y:scroll;">
				<div class="widget-content hidden_div_ser" id="cl155555_ser" style="display:none;">
					<table class="table table-condensed table-bordered table-report">
						<tr>
							<th>#</th><th>Service</th><th>Quantity</th><th>Amount</th><th>Time</th><th>Date</th>
						</tr>
					<?php
						$i=1;
						$tot_amount=0;
						$qry=mysqli_query($link,"select * from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' order by date,slno");
						while($q=mysqli_fetch_array($qry))
						{
							if($dictinct_group['group_id']==141)
							{
								$tot_days=$q['ser_quantity'];
							}else
							{
								$tot_days=$q['days'];
							}
							
							$tot_days=$q['ser_quantity'];
							
							$snm=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$q[service_id]'"));
							if($dischr>0)
							{
								echo "<tr><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";	
							}
							else
							{
								echo "<tr onclick='ot_load_edit($q[slno])' style='cursor:pointer'><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";
							}
							
							$i++;
							
							$tot_amount=$tot_amount+$q[amount];
							
						}
					?>
					<tr>
						<th colspan="2"><span class="text-right">Total</span></th>
						<th colspan="4"><?php echo $rupees_symbol.number_format($tot_amount,2);?></th>
					</tr>
					</table>
				</div>
			</div>
		</div>
<?php
		$grand_tot+=$tot_amount;
		}
	}
	// Baby Services
	$baby_num=1;
	$delivery_check_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
	$delivery_check_num=mysqli_num_rows($delivery_check_qry);
	while($delivery_check_val=mysqli_fetch_array($delivery_check_qry))
	{
		if($delivery_check_num==1)
		{
			$baby_num="";
		}
		$dictinct_group_qry=mysqli_query($link," SELECT DISTINCT(`group_id`) FROM `ipd_pat_service_details` WHERE `patient_id`='$delivery_check_val[baby_uhid]' and `ipd_id`='$delivery_check_val[baby_ipd_id]' ORDER BY `group_id` ");
		$dictinct_group_num=mysqli_num_rows($dictinct_group_qry);
		if($dictinct_group_num>0)
		{
	?>
			<button class="btn btn-default" style="width: 100%;font-size: 18px;"><b><span class="text-left" style="margin-left: 2%;">Baby <?php echo $baby_num; ?>'s Services</span> </b></button>
	<?php
		}
		while($dictinct_group=mysqli_fetch_array($dictinct_group_qry))
		{
			$grp=mysqli_fetch_array(mysqli_query($link," SELECT `group_name` FROM `charge_group_master` WHERE `group_id`='$dictinct_group[group_id]' "));
			$grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and `group_id`='$dictinct_group[group_id]'"));
			$grp_tot=$grp_tot_val["g_tot"];
		?>
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group_ser" href="#collapse<?php echo $dictinct_group["group_id"]; ?>99_ser" data-toggle="collapse" onclick="show_sub_ser('<?php echo $dictinct_group["group_id"]; ?>99')">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;"><?php echo $grp["group_name"]; ?><span class="text-right"><?php echo $rupees_symbol.number_format($grp_tot,2); ?></span></b><i class="icon-arrow-down_ser" id="ard<?php echo $dictinct_group["group_id"]; ?>99_ser"></i><i class="icon-arrow-up_ser" id="aru<?php echo $dictinct_group["group_id"]; ?>99_ser" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp_ser" id="plus_sign<?php echo $dictinct_group["group_id"]; ?>99_ser" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm_ser" id="minus_sign<?php echo $dictinct_group["group_id"]; ?>99_ser" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse<?php echo $dictinct_group["group_id"]; ?>99_ser" style="height:0px;max-height:450px;overflow-y:scroll;">
					<div class="widget-content hidden_div_ser" id="cl<?php echo $dictinct_group["group_id"]; ?>99_ser" style="display:none;">
						<table class="table table-condensed table-bordered table-report">
							<tr>
								<th>#</th><th>Service</th><th>Days</th><th>Amount</th><th>Time</th><th>Date</th>
							</tr>
						<?php
							$i=1;
							$tot_amount=0;
							$qry=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$delivery_check_val[baby_uhid]' and ipd_id='$delivery_check_val[baby_ipd_id]' and `group_id`='$dictinct_group[group_id]' order by date,slno");
							while($q=mysqli_fetch_array($qry))
							{
								if($dictinct_group['group_id']==141)
								{
									$tot_days=$q['ser_quantity'];
								}else
								{
									$tot_days=$q['days'];
								}
								$snm=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$q[service_id]'"));
								if($dischr>0)
								{
									echo "<tr><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";	
								}
								else
								{
									echo "<tr onclick='load_edit($q[slno])' style='cursor:pointer'><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";
								}
								
								$i++;
								
								$tot_amount=$tot_amount+$q[amount];
								
							}
						?>
						<tr>
							<th colspan="2"><span class="text-right">Total</span></th>
							<th colspan="4"><?php echo $rupees_symbol.number_format($tot_amount,2);?></th>
						</tr>
						</table>
					</div>
				</div>
			</div>
		<?php
		$grand_tot+=$tot_amount;
		}
		
		// Baby OT Services
	
		//$grp=mysqli_fetch_array(mysqli_query($link," SELECT `group_name` FROM `charge_group_master` WHERE `group_id`='$dictinct_group[group_id]' "));
		$grp["group_name"]="OT Charge";
		$dictinct_group["group_id"]=1551;
		
		$grp_tot=0;
		$grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where `patient_id`='$delivery_check_val[baby_uhid]' and `ipd_id`='$delivery_check_val[baby_ipd_id]' "));
		$grp_tot=$grp_tot_val["g_tot"];
		if($grp_tot>0)
		{
			if($dictinct_group_num==0)
			{
		?>
			<button class="btn btn-default" style="width: 100%;font-size: 18px;"><b><span class="text-left" style="margin-left: 2%;">Baby's Services</span> </b></button>
	<?php
			}
	?>
		<div class="accordion-group widget-box"> 
			<div class="accordion-heading">
				<div class="widget-title">
					<a data-parent="#collapse-group_ser" href="#collapse<?php echo $dictinct_group["group_id"]; ?>_ser" data-toggle="collapse" onclick="show_sub_ser('<?php echo $dictinct_group["group_id"]; ?>')">
						<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;"><?php echo $grp["group_name"]; ?><span class="text-right"><?php echo $rupees_symbol.number_format($grp_tot,2); ?></span></b><i class="icon-arrow-down_ser" id="ard<?php echo $dictinct_group["group_id"]; ?>_ser"></i><i class="icon-arrow-up_ser" id="aru<?php echo $dictinct_group["group_id"]; ?>_ser" style="display:none;"></i></span>
						<span class="text-right" style="padding:10px;font-size:18px;">
							<span class="iconp_ser" id="plus_sign<?php echo $dictinct_group["group_id"]; ?>_ser" style="float:right;"><i class="icon-plus"></i></span>
							<span class="iconm_ser" id="minus_sign<?php echo $dictinct_group["group_id"]; ?>_ser" style="float:right;display:none;"><i class="icon-minus"></i></span>
						</span>
					</a>
				</div>
			</div>
			<div class="accordion-body collapse" id="collapse<?php echo $dictinct_group["group_id"]; ?>_ser" style="height:0px;max-height:450px;overflow-y:scroll;">
				<div class="widget-content hidden_div_ser" id="cl<?php echo $dictinct_group["group_id"]; ?>_ser" style="display:none;">
					<table class="table table-condensed table-bordered table-report">
						<tr>
							<th>#</th><th>Service</th><th>Quantity</th><th>Amount</th><th>Time</th><th>Date</th>
						</tr>
					<?php
						$i=1;
						$tot_amount=0;
						$qry=mysqli_query($link,"select * from ot_pat_service_details where `patient_id`='$delivery_check_val[baby_uhid]' and `ipd_id`='$delivery_check_val[baby_ipd_id]' order by date,slno");
						while($q=mysqli_fetch_array($qry))
						{
							if($dictinct_group['group_id']==141)
							{
								$tot_days=$q['ser_quantity'];
							}else
							{
								$tot_days=$q['days'];
							}
							
							$tot_days=$q['ser_quantity'];
							
							$snm=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$q[service_id]'"));
							if($dischr>0)
							{
								echo "<tr><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";	
							}
							else
							{
								echo "<tr onclick='ot_load_edit($q[slno])' style='cursor:pointer'><td>$i</td><td>$q[service_text]</td><td>$tot_days</td><td>$q[amount]</td><td>".convert_time($q["time"])."</td><td>".convert_date_g($q["date"])."</td></tr>";
							}
							
							$i++;
							
							$tot_amount=$tot_amount+$q[amount];
							
						}
					?>
					<tr>
						<th colspan="2"><span class="text-right">Total</span></th>
						<th colspan="4"><?php echo $rupees_symbol.number_format($tot_amount,2);?></th>
					</tr>
					</table>
				</div>
			</div>
		</div>
<?php
			$grand_tot+=$tot_amount;
		}
		$baby_num++;
	}
?>
		<button class="btn btn-default" style="width: 100%;font-size: 18px;"><b><span class="text-left" style="margin-left: 2%;">Grand total</span> <span class="text-right" style="margin-right: 6%;"><?php echo $rupees_symbol.number_format($grand_tot,2);?></span></b></button>
	<!-- Pharmacy Start -->
<?php
		$grp_tot=0;
		
		$tot_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`total_amt`) AS `tot_amount`, SUM(`discount_amt`) AS `dis_amt`, SUM(`paid_amt`) AS `tot_paid`, SUM(`balance`) AS `tot_bal` FROM `ph_sell_master` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
		$ph_pat_bal=$tot_val["tot_bal"];
		
		// Baby Pharmacy
		$delivery_check_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
		if($delivery_check_val)
		{
			$baby_tot_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`total_amt`) AS `tot_amount`, SUM(`discount_amt`) AS `dis_amt`, SUM(`paid_amt`) AS `tot_paid`, SUM(`balance`) AS `tot_bal` FROM `ph_sell_master` WHERE `ipd_id`='$delivery_check_val[baby_uhid]' AND `opd_id`='$delivery_check_val[baby_ipd_id]' "));
			$ph_baby_bal=$baby_tot_val["tot_bal"];
			
			$ph_pat_bal=$ph_pat_bal+$ph_baby_bal;
		}
		if($ph_pat_bal>0)
		{
			$group_id="99"; // Pharmacy
	?>
		<div class="accordion-group widget-box"> 
			<div class="accordion-heading">
				<div class="widget-title">
					<a data-parent="#collapse-group_ser" href="#collapse<?php echo $group_id; ?>_ser" data-toggle="collapse" onclick="show_sub_ser('<?php echo $group_id; ?>')">
						<span class="icon" style="width:90%;color: red;"><b style="padding:10px;font-size:16px;">Pharmacy Balance<span class="text-right"><?php echo $rupees_symbol.number_format($ph_pat_bal,2); ?></span></b><i class="icon-arrow-down_ser" id="ard<?php echo $group_id; ?>_ser"></i><i class="icon-arrow-up_ser" id="aru<?php echo $group_id; ?>_ser" style="display:none;"></i></span>
						<span class="text-right" style="padding:10px;font-size:18px;">
							<span class="iconp_ser" id="plus_sign<?php echo $group_id; ?>_ser" style="float:right;"><i class="icon-plus"></i></span>
							<span class="iconm_ser" id="minus_sign<?php echo $group_id; ?>_ser" style="float:right;display:none;"><i class="icon-minus"></i></span>
						</span>
					</a>
				</div>
			</div>
			<div class="accordion-body collapse" id="collapse<?php echo $group_id; ?>_ser" style="height:0px;max-height:450px;overflow-y:scroll;">
				<div class="widget-content hidden_div_ser" id="cl<?php echo $group_id; ?>_ser" style="display:none;">
					<table class="table table-condensed table-bordered table-report">
						<tr>
							<th>#</th><th>Bill No</th><th>Item Name</th><th>Quantity</th><th>Amount</th><th>Time</th><th>Date</th>
						</tr>
					<?php
						$i=1;
						$tot_amount=0;
						$qry=mysqli_query($link,"select * from ph_sell_master where patient_id='$uhid' and ipd_id='$ipd' and `balance`>0 order by bill_no ");
						while($bill=mysqli_fetch_array($qry))
						{
							$ph_sell_det_qry=mysqli_query($link," SELECT * FROM `ph_sell_details` WHERE `bill_no`='$bill[bill_no]' ");
							while($ph_sell_det=mysqli_fetch_array($ph_sell_det_qry))
							{
								$item_name=mysqli_fetch_array(mysqli_query($link," SELECT `item_name` FROM `item_master` WHERE `item_id`='$ph_sell_det[item_code]' "));
								
								$tot_days=$ph_sell_det['sale_qnt'];
								echo "<tr><td>$i</td><td>$bill[bill_no]</td><td>$item_name[item_name]</td><td>$tot_days</td><td>$ph_sell_det[total_amount]</td><td>".convert_time($bill["time"])."</td><td>".convert_date_g($bill["entry_date"])."</td></tr>";	
								
								$i++;
							
								$tot_amount=$tot_amount+$ph_sell_det["total_amount"];
								
							}
						}
					?>
					<tr>
						<th colspan="4"><span class="text-right">Total</span></th>
						<th colspan="3"><?php echo $rupees_symbol.number_format($tot_amount,2);?></th>
					</tr>
					</table>
				</div>
			</div>
		</div>
		<!-- Pharmacy End -->
	<?php } ?>
	</div>
	<?php
}
else if($type==114)
{
	$slno=$_POST["val"];
	$user=$_POST["user"];
	
	$emp_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$user' "));
	
	$det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where slno='$slno'"));
	$serv=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$det[charge_id]'"));
	
	$rel_slno_qry=mysqli_query($link," SELECT * FROM `doctor_service_done` WHERE `rel_slno`='$slno' ");	
	$rel_slno_num=mysqli_num_rows($rel_slno_qry);
	$rel_slno_val=mysqli_fetch_array($rel_slno_qry);
	
	if($det["group_id"]=='104') // Pathology
	{
		$test_info=mysqli_fetch_array(mysqli_query($link," SELECT `patient_id`,`ipd_id`,`batch_no`,`testid` FROM `patient_test_details` WHERE `slno` in ( SELECT `test_slno` FROM `link_test_service` WHERE `service_slno`='$slno' ) "));
		
		$testresult_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `testresults` WHERE `patient_id`='$test_info[patient_id]' AND `ipd_id`='$test_info[ipd_id]' AND `batch_no`='$test_info[batch_no]' AND `testid`='$test_info[testid]' "));
		
		$widalresult_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `widalresult` WHERE `patient_id`='$test_info[patient_id]' AND `ipd_id`='$test_info[ipd_id]' AND `batch_no`='$test_info[batch_no]' "));
		
		$test_sumry_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `patient_test_summary` WHERE `patient_id`='$test_info[patient_id]' AND `ipd_id`='$test_info[ipd_id]' AND `batch_no`='$test_info[batch_no]' AND `testid`='$test_info[testid]' "));
		
		$tot_num=$testresult_num+$widalresult_num+$test_sumry_num;
	}
	if($det["group_id"]=='150') // Cardiology
	{
		$test_info=mysqli_fetch_array(mysqli_query($link," SELECT `patient_id`,`ipd_id`,`batch_no`,`testid` FROM `patient_test_details` WHERE `slno` in ( SELECT `test_slno` FROM `link_test_service` WHERE `service_slno`='$slno' ) "));
		
		$testresult_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `testresults_card` WHERE `patient_id`='$test_info[patient_id]' AND `ipd_id`='$test_info[ipd_id]' AND `batch_no`='$test_info[batch_no]' AND `testid`='$test_info[testid]' "));
		
		$test_sumry_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `patient_test_summary` WHERE `patient_id`='$test_info[patient_id]' AND `ipd_id`='$test_info[ipd_id]' AND `batch_no`='$test_info[batch_no]' AND `testid`='$test_info[testid]' "));
		
		$tot_num=$testresult_num+$widalresult_num+$test_sumry_num;
	}
	if($det["group_id"]=='151') // Radiology
	{
		$test_info=mysqli_fetch_array(mysqli_query($link," SELECT `patient_id`,`ipd_id`,`batch_no`,`testid` FROM `patient_test_details` WHERE `slno` in ( SELECT `test_slno` FROM `link_test_service` WHERE `service_slno`='$slno' ) "));
		
		$testresult_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `testresults_rad` WHERE `patient_id`='$test_info[patient_id]' AND `ipd_id`='$test_info[ipd_id]' AND `batch_no`='$test_info[batch_no]' AND `testid`='$test_info[testid]' "));
		
		$test_sumry_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `patient_test_summary` WHERE `patient_id`='$test_info[patient_id]' AND `ipd_id`='$test_info[ipd_id]' AND `batch_no`='$test_info[batch_no]' AND `testid`='$test_info[testid]' "));
		
		$tot_num=$testresult_num+$widalresult_num+$test_sumry_num;
	}
	$dis_field="";
	if($det["group_id"]=='141' || $det["group_id"]=='148')
	{
		$dis_field="disabled";
		if($emp_info["edit_ipd"]==1)
		{
			$dis_edit="";
		}else
		{
			$dis_edit="disabled";
		}
	}
	?>
	<input type="hidden" id="slno" value="<?php echo $slno;?>"/>
	<table class="table table-condensed table-bordered">
	<tr>
		<th>Service</th>
		<td>
			<select id="serv_edit" disabled>
			<?php
			if($det["group_id"]=='104') // Pathology
			{
				$ser=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='1' ORDER BY `testname`");	
				while($s=mysqli_fetch_array($ser))
				{
					if($det['service_id']==$s['testid']){ $ser_sel="Selected='selected'";} else { $ser_sel="";}
					echo "<option value='$s[testid]' $ser_sel>$s[testname]</option>";
				}
			}else if($det["group_id"]=='150') // Cardiology
			{
				$ser=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='3' ORDER BY `testname`");	
				while($s=mysqli_fetch_array($ser))
				{
					if($det['service_id']==$s['testid']){ $ser_sel="Selected='selected'";} else { $ser_sel="";}
					echo "<option value='$s[testid]' $ser_sel>$s[testname]</option>";
				}
			}else if($det["group_id"]=='151') // Radiology
			{
				$ser=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='2' ORDER BY `testname`");	
				while($s=mysqli_fetch_array($ser))
				{
					if($det['service_id']==$s['testid']){ $ser_sel="Selected='selected'";} else { $ser_sel="";}
					echo "<option value='$s[testid]' $ser_sel>$s[testname]</option>";
				}
			}else
			{
				$ser=mysqli_query($link,"select * from charge_master where group_id='$det[group_id]' order by charge_name");
				//$ser=mysqli_query($link,"select * from charge_master order by charge_name");	
				while($s=mysqli_fetch_array($ser))
				{
					if($det[service_id]==$s[charge_id]){ $ser_sel="Selected='selected'";} else { $ser_sel="";}
					echo "<option value='$s[charge_id]' $ser_sel>$s[charge_name]</option>";
				}
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<th>Text</th>
		<th><input type="text" id="edit_text" value="<?php echo $det[service_text];?>"/></th>
	</tr>
	<?php
	
	if($rel_slno_num>0)
	{
	?>
		<tr>
			<th>Doctor</th>
			<th>
				<select id="consultantdoctorid_edit">
					<option value="0">Select</option>
				<?php
					$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` order by `Name` ");
					while($con_doc=mysqli_fetch_array($con_doc_qry))
					{
						if($rel_slno_val["consultantdoctorid"]==$con_doc["consultantdoctorid"]){ $sel_doc="selected"; }else{ $sel_doc=""; }
						echo "<option value='$con_doc[consultantdoctorid]' $sel_doc >$con_doc[Name]</option>";
					}
				?>
				</select>
			</th>
		</tr>
	<?php
	}else
	{
		echo "<input type='hidden' id='consultantdoctorid_edit' value='99999'>";
	}
	
	if($det[days]>0)
	{
	?>
	<tr>
		<th>Days</th>
		<td><input type="text" id="edit_days" value="<?php echo $det[days];?>" <?php echo $dis_field; ?>/></td>
	</tr>
	<?php
	}
	else
	{
		echo "<input type='hidden' id='edit_days' value='0'>";
	}
	?>
	<tr>
		<th>Quantity</th>
		<th>
			<!--<input type="text" id="ser_quantity" value="<?php echo $det[ser_quantity];?>"/>-->
			<select id='ser_quantity_edit' <?php echo $dis_field; ?>>
			<?php
				for($i=1;$i<101;$i++)
				{
					if($det['ser_quantity']==$i){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$i' $sel>$i</option>";
				}
			?>
			</select>
		</th>
	</tr>
	<tr>
		<th>Rate</th>
		<td><input type="text" id="rate_edit" value="<?php echo $det[rate];?>" <?php echo $dis_edit; ?>/></td>
	</tr>
	<tr>
		<th>Time</th>
		<td><input type="text" id="edit_time" value="<?php echo $det[time];?>" <?php echo $dis_field; ?>/></td>
	</tr>
	<tr>
		<th>Date</th>
		<td><input type="text" class="datepicker" id="edit_date" value="<?php echo $det[date];?>" <?php echo $dis_field; ?>/></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center">
		<?php if($tot_num==0){ ?>
			<button class="btn btn-info" onclick="service_edit(1)"><i class="icon-save"></i> Update</button>
			<button class="btn btn-danger" onclick="service_edit(2)"><i class="icon-trash"></i> Delete</button>
		<?php } ?>
			<button class="btn btn-default" onclick="$('#mod').click()"><i class="icon-remove-sign"></i> Close</button>
		</td>
	</tr>
	</table>
	<?php
	
}
else if($type==115)
{
	$slno=$_POST['slno'];
	$typ=$_POST['typ'];
	
	$time=$_POST['time'];
	$date=$_POST['date'];
	$user=$_POST["user"];
	
	$ipd_service=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `slno`='$slno' "));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$ipd_service[patient_id]' AND `opd_id`='$ipd_service[ipd_id]'"));
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
	$bill_name=$pat_typ_text["bill_name"];
	
	$b_sum=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot_bill` FROM `ipd_pat_service_details` WHERE `patient_id`='$ipd_service[patient_id]' AND `ipd_id`='$ipd_service[ipd_id]' "));
	$b_sum_amount=$b_sum["tot_bill"];
	
	if($typ==1)
	{
		$serv=$_POST['serv'];
		$serv_text=mysqli_real_escape_string($link,$_POST['serv_text']);
		$days=$_POST['days'];
		$ser_quantity=$_POST['ser_quantity'];
		$rate=$_POST['rate'];
		$consultantdoctorid=$_POST["consultantdoctorid"];
		
		$amount=$rate*$ser_quantity;
		
		if($ipd_service)
		{
			// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$ipd_service[patient_id]' AND `opd_id`='$ipd_service[ipd_id]' AND `type`='3' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			mysqli_query($link," INSERT INTO `ipd_pat_service_details_edit`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `counter`, `bed_id`) VALUES ('$ipd_service[patient_id]','$ipd_service[ipd_id]','$ipd_service[group_id]','$ipd_service[service_id]','$ipd_service[service_text]','$ipd_service[ser_quantity]','$ipd_service[rate]','$ipd_service[amount]','$ipd_service[days]','$ipd_service[user]','$ipd_service[time]','$ipd_service[date]','$counter_num','$ipd_service[bed_id]') ");
		}
		mysqli_query($link," UPDATE `doctor_service_done` SET `service_id`='$serv',`consultantdoctorid`='$consultantdoctorid',`user`='$user' WHERE `rel_slno`='$slno' ");
		
		mysqli_query($link,"update ipd_pat_service_details set service_id='$serv',service_text='$serv_text',ser_quantity='$ser_quantity',rate='$rate',days='$days',amount='$amount',time='$time',date='$date' where slno='$slno'");
		
		// IPD Service Fee
		
		$uhid=$ipd_service["patient_id"];
		$ipd=$ipd_service["ipd_id"];
		
		//~ $service_fee_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='173' and service_id='1354' "));

		//~ $tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
		//~ $tot_serv_amt=$tot_serv["tots"];

		//~ $charge_master_val=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where `group_id`='173' and charge_id='1354' "));

		//~ $charge_amount=($tot_serv_amt/100)*$charge_master_val["amount"];

		//~ if($charge_amount!=$service_fee_check["amount"])
		//~ {
			//~ $first_bed=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and alloc_type='1' order by slno"));
			
			//~ mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and `group_id`='173' and service_id='1354' ");
			
			//~ mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','173','1354','$charge_master_val[charge_name]','1','$charge_amount','$charge_amount','1','$_SESSION[emp_id]','$pat_reg[time]','$pat_reg[date]','$first_bed[bed_id]')");
		//~ }
		
		
	}
	else if($typ==2)
	{
		
		$slno_test=mysqli_fetch_array(mysqli_query($link," SELECT `test_slno` FROM `link_test_service` WHERE `service_slno`='$slno' "));
		
		$ipd_service=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `slno`='$slno' "));
		
		$other_service_qry=(mysqli_query($link," SELECT `charge_id` FROM `bed_other_charge` WHERE `bed_id` in ( SELECT `bed_id` FROM `bed_master` WHERE `charge_id`='$ipd_service[service_id]' ) "));
		
		$time=date('H:i:s');
		$date=date("Y-m-d");
		
		mysqli_query($link,"insert into ipd_pat_service_delete(patient_id,ipd_id,group_id,service_id,service_text,ser_quantity,rate,amount,days,user,time,date,bed_id) values('$ipd_service[patient_id]','$ipd_service[ipd_id]','$ipd_service[group_id]','$ipd_service[service_id]','$ipd_service[service_text]','$ipd_service[ser_quantity]','$ipd_service[rate]','$ipd_service[amount]','$ipd_service[days]','$user','$time','$date','$ipd_service[bed_id]')");
		
		while($other_service=mysqli_fetch_array($other_service_qry))
		{
			$ch=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_name`,`group_id` FROM `charge_master` WHERE `charge_id`='$other_service[charge_id]'"));
			mysqli_query($link,"insert into  ipd_pat_service_delete(patient_id,ipd_id,group_id,service_id,service_text,ser_quantity,rate,amount,days,user,time,date,bed_id) values('$ipd_service[patient_id]','$ipd_service[ipd_id]','$ch[group_id]','$other_service[charge_id]','$ch[charge_name]','$ipd_service[ser_quantity]','$ipd_service[rate]','$ipd_service[amount]','$ipd_service[days]','$user','$time','$date','$ipd_service[bed_id]')");
			
			mysqli_query($link," DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$ipd_service[patient_id]' AND `ipd_id`='$ipd_service[ipd_id]' AND `service_id`='$other_service[charge_id]' AND `date`='$ipd_service[date]' ");
		}
		
		mysqli_query($link," DELETE FROM `ipd_pat_service_details` WHERE `slno`='$slno' ");
		
		if($ipd_service["group_id"]==141)
		{
			$uhid=$ipd_service["patient_id"];
			$ipd=$ipd_service["ipd_id"];
			
			// IPD Service Fee
			//~ mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and `group_id`='173' and service_id='1354' ");
			
			//~ $tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
			//~ $tot_serv_amt1=$tot_serv1["tots"];
			
			//~ $charge_master_val=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where `group_id`='173' and charge_id='1354' "));
			
			//~ $charge_amount=($tot_serv_amt1/100)*$charge_master_val["amount"];
			
			//~ $first_bed=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and alloc_type='1' order by slno"));
			
			//~ mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','173','1354','$charge_master_val[charge_name]','1','$charge_amount','$charge_amount','1','$_SESSION[emp_id]','$pat_reg[time]','$pat_reg[date]','$first_bed[bed_id]')");
		}
		
		//mysqli_query($link,"delete from ipd_pat_service_details where slno='$slno'");
		mysqli_query($link,"delete from doctor_service_done where rel_slno='$slno'");
		mysqli_query($link,"delete from patient_test_details where slno='$slno_test[test_slno]'");
		mysqli_query($link,"delete from link_test_service where test_slno='$slno_test[test_slno]'");
		
	}
	
	
	$patient_id=$ipd_service["patient_id"];
	$ipd_id=$ipd_service["ipd_id"];
	
	$pat_discharge=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND pay_type='Final' "));
	if($pat_discharge)
	{
		// Manage Account
		
		$bill_no=101;
		$date2=date("Y-m-d");
		$date1=explode("-",$date2);	
		$c_var=$date1[0]."-".$date1[1];
		$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
		if($chk['tot_bill']>0)
		{
			$bill_no=$bill_no+$chk['tot_bill'];
		}
		$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
		$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
		
		$date4=date("y-m-d");
		$date3=explode("-",$date4);
		
		$random_no=rand(1,9);
		
		$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
		
		$a_sum=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot_bill` FROM `ipd_pat_service_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
		$a_sum_amount=$a_sum["tot_bill"];
		
		$paym_final_dis=mysqli_fetch_array(mysqli_query($link,"select ifnull(SUM(`discount`),0) from ipd_advance_payment_details where patient_id='$patient_id' and ipd_id='$ipd_id' AND `pay_type`='Final' "));
		$final_discount=$paym_final_dis["discount"];
		
		// $summ_diff_amount=$b_sum_amount-$a_sum_amount-$final_discount;
		
		$summ_diff_amount=$b_sum_amount-$a_sum_amount;
		if($summ_diff_amount==0) // Same Bill Amount
		{
			//~ mysqli_query($link," DELETE FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ");
			
			//~ mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `pay_type`='Advance' ");
			
			//~ mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `pay_type`='Refund' ");
			
			//~ mysqli_query($link," DELETE FROM `invest_payment_refund` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' ");
			
			//~ mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `pay_type`='Balance' ");
			
			//~ mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `amount`='0' WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `pay_type`='Final' ");
		}
		else if($summ_diff_amount>0) // Bill Amount Reduce
		{
			$check_bal=mysqli_fetch_array(mysqli_query($link," SELECT `bal_amount` FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
			
			$pat_balance=$check_bal["bal_amount"];
			
			if($pat_balance>0) // If balance
			{
				if($pat_balance>=$summ_diff_amount)
				{
					$pat_balance=$pat_balance-$summ_diff_amount;
					
					mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `balance`='$pat_balance' WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `pay_type`='Final' ");
					
					mysqli_query($link," UPDATE `ipd_discharge_balance_pat` SET `bal_amount`='$pat_balance' WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ");
				}
				else
				{
					$refund_amount=$summ_diff_amount-$pat_balance;
					$pat_balance=0;
					$refund_reason="Bill amount has been reduced";
					
					mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `balance`='$pat_balance' WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `pay_type`='Final' ");
					
					mysqli_query($link," UPDATE `ipd_discharge_balance_pat` SET `bal_amount`='$pat_balance' WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ");
					
					mysqli_query($link," INSERT INTO `ipd_advance_payment_details`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `time`, `date`, `user`) VALUES ('$patient_id','$ipd_id','$bill_id','$a_sum_amount','0','0','0','$refund_amount','Refund','Cash','$now_time','$now_date','$user') ");
					
					mysqli_query($link," INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$patient_id','$ipd_id','','$a_sum_amount','$refund_amount','$refund_reason','$now_date','$now_time','$user') ");
				}
			}
			else
			{
				// No Balance
				$refund_amount=$summ_diff_amount;
				
				$refund_reason="Bill amount has been reduced";
				
				mysqli_query($link," INSERT INTO `ipd_advance_payment_details`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `time`, `date`, `user`) VALUES ('$patient_id','$ipd_id','$bill_id','$a_sum_amount','0','0','0','$refund_amount','Refund','Cash','$now_time','$now_date','$user') ");
				
				mysqli_query($link," INSERT INTO `invest_payment_refund`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$patient_id','$ipd_id','','$a_sum_amount','$refund_amount','$refund_reason','$now_date','$now_time','$user') ");
			}
		}
		else if($summ_diff_amount<0) // Bill amount is increased
		{
			// Add balance
			$check_bal=mysqli_fetch_array(mysqli_query($link," SELECT `bal_amount` FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
			
			$pat_balance=$summ_diff_amount*(-1);
			if($check_bal)
			{
				$pat_balance=$pat_balance+$check_bal["bal_amount"];
				
				mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `balance`='$pat_balance' WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `pay_type`='Final' ");
				
				mysqli_query($link," UPDATE `ipd_discharge_balance_pat` SET `bal_amount`='$pat_balance' WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ");
			}
			else
			{
				$pat_balance=$check_bal["bal_amount"];
				
				mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `balance`='$pat_balance' WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `pay_type`='Final' ");
				
				mysqli_query($link," INSERT INTO `ipd_discharge_balance_pat`(`patient_id`, `ipd_id`, `bal_amount`, `user`, `date`, `time`) VALUES ('$patient_id','$ipd_id','$pat_balance','$user','$now_date','$now_time') ");
			}
		}
	}
}
else if($type==116)
{
	$uhid=$_POST[uhid];
	$ipd=$_POST[ipd];
	$user=$_POST[user];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	mysqli_query($link,"insert into ipd_pat_discharge_details(`patient_id`, `ipd_id`, `time`, `date`, `user`) values('$uhid','$ipd','$time','$date','$user')");
	
	$det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd'"));
	
	mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,time,date,user) values('$uhid','$ipd','$det[ward_id]','$det[bed_id]','0','$time','$date','$user')");
	
	mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
	
	mysqli_query($link,"delete from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd'");
	
	// Time between registration and discharge time
	
	$now_date_time_str=date("Y-m-d H:i:s");
	$reg_date_time_str=$pat_reg["date"]." ".$pat_reg["time"];
	
	$seconds = strtotime("$now_date_time_str") - strtotime("$reg_date_time_str");
	
	$hours   = floor(($seconds) / 3600);
	$minutes = floor(($seconds - ($hours * 3600))/60);
	$seconds = floor(($seconds - ($hours * 3600) - ($minutes*60)));
	
	$hours   = str_pad($hours,2,"0",STR_PAD_LEFT);
	$minutes = str_pad($minutes,2,"0",STR_PAD_LEFT);
	$seconds = str_pad($seconds,2,"0",STR_PAD_LEFT);
	
	$time_str=$hours.":".$minutes.":".$seconds;
	
	mysqli_query($link,"delete from ipd_pat_staying_time where patient_id='$uhid' and ipd_id='$ipd'");
	mysqli_query($link,"INSERT INTO `ipd_pat_staying_time`(`patient_id`, `ipd_id`, `hour`, `minute`, `second`, `time_str`) VALUES ('$uhid','$ipd','$hours','$minutes','$seconds','$time_str')");
	
}
else if($type==117)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$user=$_POST['user'];
	
	$pat_bed_num=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ipd_id='$ipd'"));
	if($pat_bed_num==0)
	{
		$last_row1=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_bed_alloc_details` WHERE `slno` in (SELECT MAX(`slno`) FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ) "));
		$max_slno1=$last_row1['slno'];
		
		$last_row0=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_bed_alloc_details` WHERE `slno` in (SELECT MAX(`slno`) FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='0' ) "));
		$max_slno0=$last_row0['slno'];
		$ward_id=$last_row0['ward_id'];
		$bed_id=$last_row0['bed_id'];
		
		$already_pat_bed_num=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_bed_details where `bed_id`='$bed_id'"));
		if($already_pat_bed_num==0)
		{
		
			mysqli_query($link," DELETE FROM `ipd_bed_alloc_details` WHERE `slno`='$max_slno0' ");
			
			mysqli_query($link," DELETE FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
			
			mysqli_query($link," DELETE FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
			
			mysqli_query($link," INSERT INTO `ipd_pat_bed_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`) VALUES ('$uhid','$ipd','$ward_id','$bed_id','$last_row1[user]','$last_row1[time]','$last_row1[date]') ");
			
			//mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `pay_type`='Advance',`discount`='0' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' ");
			mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `pay_type`='Advance',`discount`='0',`balance`='0',`refund`='0' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' ");
			
			mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_mode`='Credit' ");
			
			// Record
			mysqli_query($link," INSERT INTO `discharge_cancel_record`(`patient_id`, `ipd_id`, `user`, `date`, `time`, `alloc_slno`) VALUES ('$uhid','$ipd','$user','$date','$time','$max_slno1') ");
			
			echo "2";
		}else
		{
			echo "3";
		}
		
	}else
	{
		echo "1";
	}
}
else if($type==1001)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE patient_id='$uhid' and opd_id='$ipd' "));
	
	echo "<table class='table table-bordered table-condensed table-report'>";
	echo "<tr><th>#</th><th>Service</th><th>Amount</th></tr>";
	$i=1;
	$tot=0;
	$ipd_check=0;
	$cas=mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd'");
	while($cs=mysqli_fetch_array($cas))
	{
		$s_amount=number_format($cs['amount'],2);
		echo "<tr><td>$i</td><td>$cs[service_text]</td><td>$s_amount</td></tr>";
		$i++;
		$tot=$tot+$cs[amount];
		$ipd_check=1;
	}
	if($ipd_check==0) // If Caualty or day care and has entry ot, skip ot
	{
		$cas=mysqli_query($link,"select * from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd'");
		while($cs=mysqli_fetch_array($cas))
		{
			$s_amount=number_format($cs['amount'],2);
			echo "<tr><td>$i</td><td>$cs[service_text]</td><td>$s_amount</td></tr>";
			$i++;
			$tot=$tot+$cs[amount];
		}
	}
	$tott=number_format($tot,2);
	echo "<tr><th colspan='2' style='text-align:right'>Total Amount</th><th>$tot</th></tr>";
	
	$check_payment_entry=mysqli_fetch_array(mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'"));
	
	$adv=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as tot from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd'"));
	
	$discountt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(discount),0) as dis from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
	$discountt=$discountt["dis"];
	
	$balance_discount=mysqli_fetch_array(mysqli_query($link,"select sum(discount) as dis from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and pay_type='Balance' "));
	$discountt+=$balance_discount["dis"];
	
	$pat_refund=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(refund),0) as refund from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' "));
	$refund_amount=$pat_refund["refund"];
	
	if($discountt>0)
	{
		echo "<tr><th colspan='2' style='text-align:right'>Discount Amount</th><th>$discountt</th>";
	}
	
	echo "<tr><th colspan='2' style='text-align:right'>Paid Amount</th><th>";
	
	$pay=0;
	if($adv[tot])
	{ 
		echo number_format($adv['tot'],2);
		$tot_p=$adv[tot];
	}
	else
	{ 
		echo '0';
		$tot_p=$adv[tot];
	}
	
	$rem=$tot-$tot_p-$discountt;
	
	if($rem>0 && $check_payment_entry)
	{
		echo "<tr><th colspan='2' style='text-align:right'>Balance</th><th>$rem</th>";
	}
	
	if($refund_amount>0)
	{
		echo "<tr><th colspan='2' style='text-align:right'>Refunded Amount</th><th>$refund_amount</th>";
	}
	
	$bill=mysqli_fetch_array(mysqli_query($link,"select bill_no from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' order by slno desc limit 1"));
	
	$user_access=mysqli_fetch_array(mysqli_query($link," SELECT `edit_payment`, `cancel_pat` FROM `employee` WHERE `emp_id`='$usr' "));
	if($user_access['edit_payment']==0)
	{
		$cancel_payment_btn="style='display:none;'";
	}else
	{
		$final_payment_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
		$final_payment_cancel_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details_cancel` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
		if($final_payment_num>0)
		{
			$cancel_payment_btn="";
		}else
		{
			$cancel_payment_btn="style='display:none;'";
		}
	}
	if($user_access['cancel_pat']==0)
	{
		$dis_none="style='display:none;'";
	}else
	{
		$final_payment_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
		$final_payment_cancel_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details_cancel` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
		if($final_payment_num>0)
		{
			$dis_none="style='display:none;'";
		}else
		{
			$dis_none="";
		}
	}
	
	echo "<input type='hidden' value='$tot' id='tot_amount'/>";
	echo "<input type='hidden' value='$rem' id='to_be_paid'/>";
	echo "<input type='hidden' value='$bill[bill_no]' id='casual_bill_id'/>";
	echo "</th></tr>";
	
	//echo "<tr><th colspan='3' style='text-align:center'>";
	
	$payment_entry=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
	
	//if($rem>0)
	if($payment_entry==0)
	{
	?>
		<tr>
			<th colspan='2' style='text-align:right'>Balance</th>
			<th><?php echo number_format($rem,2); ?></th>
		</tr>
		<tr>
			<th colspan='2' style='text-align:right'>Discount</th>
			<td><input type="text" id="casual_discount" value="0" class="span2" onKeyup="casual_discount_amt(this.value)" <?php echo $discount_element_disable; ?>></td>
		</tr>
		<tr id="discount_tr" style="display:none;">
				<th colspan='2' style='text-align:right'>Discount Reason</th>
				<th><input type="text" id="pat_disc_res" onkeyup="pat_discount_reason(this)"/></th>
			</tr>
		<tr>
			<th colspan='2' style='text-align:right'>To be paid</th>
			<td><input type="text" id="casual_to_pay" class="span2" value="<?php echo $rem; ?>" disabled></td>
		</tr>
		
		<tr>
			<th colspan='2' style='text-align:right'>Cash</th>
			<td><input type="text" id="cash_pay" class="span2" value="<?php echo $rem; ?>" onKeyup="cash_amt(this.value)" ></td>
		</tr>
		<tr>
			<th colspan='2' style='text-align:right'>Card</th>
			<td><input type="text" id="card_pay" class="span2" value="0" onKeyup="card_amt(this.value)" ></td>
		</tr>
		<tr>
			<th colspan='2' style='text-align:right'>Payment Option</th>
			<td>
				<select class="span2" id="payment_option" onchange="payment_option_change()">
					<option value="Paid">Paid</option>
					<option value="Credit">Credit</option>
				</select>
			</td>
		</tr>
		<!--<tr>
			<th colspan='2' style='text-align:right'>Payment Mode</th>
			<td>
				<select id="casual_pay_mode" class="span2">
					<option value="Cash">Cash</option>
					<option value="Card">Card</option>
					<option value="Cheque">Cheque</option>
					<option value="Draft">Draft</option>
				</select>
			</td>
		</tr>-->
	<?php
		echo "<tr><th colspan='3' style='text-align:center'>";
		//echo "<input type='button' value='Save' class='btn btn-info' onclick='save_casual()'/>";
		echo "<button class='btn btn-save' onclick='save_casual()'><i class='icon-save'></i> Save</button> ";
		//echo " <input type='button' value='Cancel Patient' class='btn btn-danger' onclick='cancel_casual()' $dis_none />";
		echo "<button class='btn btn-delete' onclick='cancel_casual()' $dis_none><i class='icon-remove'></i> Cancel Patient</button>";
		
		echo "</th></tr>";
	}
	else
	{
		echo "<tr><th colspan='3' style='text-align:center'>";
		//echo "<input type='button' value='Print Receipt' class='btn btn-info' onclick='print_casual(100)'/>";
		echo "<button class='btn btn-print' onclick='print_casual(100)'><i class='icon-print'></i> Receipt</button>";
		echo "&nbsp;";
	if($pat_reg["type"]==4)
	{
		//echo "<input type='button' value='Print Assessment Form' class='btn btn-success' onclick='print_casual(400)'/>";
		echo "<button class='btn btn-print print_assess lite' onclick='print_casual(400)'><i class='icon-print'></i> Assessment Form</button>";
		echo "&nbsp;";
		//echo "<input type='button' value='Print Prescription' class='btn btn-inverse' onclick='print_casual_prescription(400)'/>";
		echo "<button class='btn btn-print' onclick='print_casual_prescription(400)'><i class='icon-print'></i> Prescription</button>";
	}
		echo "&nbsp;";
		//echo "<input type='button' value='Edit' class='btn btn-warning' onclick='edit_other_paymentmode(\"$uhid\",\"$ipd\")' $edit_ipd_style>";
		echo "<button class='btn btn-edit' onclick='edit_other_paymentmode(\"$uhid\",\"$ipd\")' $edit_ipd_style><i class='icon-edit'></i> Edit Payment Mode</button>";
		//echo " <input type='button' value='Print' class='btn btn-info' onclick='print_casual(101)'/>";
		//echo " <input type='button' value='Cancel Patient' class='btn btn-danger' onclick='cancel_casual()' $dis_none />";
		echo " <button class='btn btn-delete' onclick='cancel_casual()' $dis_none><i class='icon-remove'></i> Cancel Patient</button>";
		//echo " <input type='button' value='Cancel Payment' class='btn btn-danger' onclick='cancel_casual_payment()' $cancel_payment_btn />";
		echo " <button class='btn btn-delete' onclick='cancel_casual_payment()' $cancel_payment_btn><i class='icon-remove'></i> Cancel Payment</button>";
		if($usr==101)
		{
			//echo " <input type='button' value='Print Dotmatrix' class='btn btn-info' onclick='print_casual(101)'/>";
		}
		echo "</th></tr>";
	}
	
	echo "</table>"; 
}
else if($type==1002)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$discount=$_POST['discount'];
	$paid=$_POST['paid'];
	$cash_pay=$_POST['cash_pay'];
	$card_pay=$_POST['card_pay'];
	$tot=$_POST['tot'];
	$pay_mode=$_POST['pay_mode'];
	$payment_option=$_POST['payment_option'];
	$user=$_POST['user'];
	
	$dis_reason=mysqli_real_escape_string($link,$_POST['dis_reason']);
	
	$pat_balance=$tot-$discount;
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
	$bill_name=$pat_typ_text["bill_name"];
	
	$insert_val=0;
	
	if($payment_option=="Credit" && $pat_balance>0)
	{
		$bill_no=101;
		$date2=date("Y-m-d");
		$date1=explode("-",$date2);	
		$c_var=$date1[0]."-".$date1[1];
		$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
		if($chk['tot_bill']>0)
		{
			$bill_no=$bill_no+$chk['tot_bill'];
		}
		$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
		$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
		
		$date4=date("y-m-d");
		$date3=explode("-",$date4);
		
		$random_no=rand(1,9);
		
		$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
		
		$pay_mode="Credit";
		
		$chk_card=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='$pay_mode' "));
		
		if(!$chk_card)
		{
			mysqli_query($link,"insert into ipd_advance_payment_details(patient_id, ipd_id, bill_no, tot_amount, discount, amount, balance, refund, pay_type, pay_mode, time, date, user) values('$uhid','$ipd','$bill_id','$tot','$discount','0','$pat_balance','0','Final','$pay_mode','$time','$date','$user')");
			
			//$dis_reason="";
			if($discount>0)
			{
				mysqli_query($link," INSERT INTO `patient_discount_reason`(`patient_id`, `ipd_id`, `bill_no`, `reason`) VALUES ('$uhid','$ipd','$bill_id','$dis_reason') ");
			}
			
			if($pat_balance>0)
			{
				mysqli_query($link," INSERT INTO `ipd_discharge_balance_pat`(`patient_id`, `ipd_id`, `bal_amount`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$pat_balance','$user','$date','$time') ");
			}
			
			$insert_val=1;
			$discount=0;
			$card_pay=0;
			$cash_pay=0;
		}
	}
	
	if($card_pay>0)
	{
		$bill_no=101;
		$date2=date("Y-m-d");
		$date1=explode("-",$date2);	
		$c_var=$date1[0]."-".$date1[1];
		$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
		if($chk['tot_bill']>0)
		{
			$bill_no=$bill_no+$chk['tot_bill'];
		}
		$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
		$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
		
		$date4=date("y-m-d");
		$date3=explode("-",$date4);
		
		$random_no=rand(1,9);
		
		$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
		
		$pay_mode="Card";
		
		$chk_card=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='$pay_mode' "));
		
		if(!$chk_card)
		{
			mysqli_query($link,"insert into ipd_advance_payment_details(patient_id, ipd_id, bill_no, tot_amount, discount, amount, balance, refund, pay_type, pay_mode, time, date, user) values('$uhid','$ipd','$bill_id','$tot','$discount','$card_pay','0','0','Final','$pay_mode','$time','$date','$user')");
			
			//$dis_reason="";
			if($discount>0)
			{
				mysqli_query($link," INSERT INTO `patient_discount_reason`(`patient_id`, `ipd_id`, `bill_no`, `reason`) VALUES ('$uhid','$ipd','$bill_id','$dis_reason') ");
			}
			$insert_val=1;
			$discount=0;
		}
	}
	
	if($cash_pay>0 || $insert_val==0)
	{
		$bill_no=101;
		$date2=date("Y-m-d");
		$date1=explode("-",$date2);	
		$c_var=$date1[0]."-".$date1[1];
		$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
		if($chk['tot_bill']>0)
		{
			$bill_no=$bill_no+$chk['tot_bill'];
		}
		$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
		$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
		
		$date4=date("y-m-d");
		$date3=explode("-",$date4);
		
		$random_no=rand(1,9);
		
		$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
		
		$pay_mode="Cash";
		
		$chk_cash=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='$pay_mode' "));
		
		if(!$chk_cash)
		{
			mysqli_query($link,"insert into ipd_advance_payment_details(patient_id, ipd_id, bill_no, tot_amount, discount, amount, balance, refund, pay_type, pay_mode, time, date, user) values('$uhid','$ipd','$bill_id','$tot','$discount','$cash_pay','0','0','Final','$pay_mode','$time','$date','$user')");
			
			//$dis_reason="";
			if($discount>0)
			{
				mysqli_query($link," INSERT INTO `patient_discount_reason`(`patient_id`, `ipd_id`, `bill_no`, `reason`) VALUES ('$uhid','$ipd','$bill_id','$dis_reason') ");
			}
			
			$discount=0;
		}
	}
			
	//////////////////// Payment Double Row Insert
	$cash_final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='Cash' ");
	$cash_final_pay_num=mysqli_num_rows($cash_final_pay_qry);

	if($cash_final_pay_num>1)
	{
		$h=1;
		while($cash_final_pay_val=mysqli_fetch_array($cash_final_pay_qry))
		{
			if($h>1)
			{
				mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `slno`='$cash_final_pay_val[slno]' ");
			}
			$h++;
		}
	}

	$card_final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='Card' ");
	$card_final_pay_num=mysqli_num_rows($card_final_pay_qry);

	if($card_final_pay_num>1)
	{
		$h=1;
		while($card_final_pay_val=mysqli_fetch_array($card_final_pay_qry))
		{
			if($h>1)
			{
				mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `slno`='$card_final_pay_val[slno]' ");
			}
			$h++;
		}
	}

	$card_final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' AND `pay_mode`='Credit' ");
	$card_final_pay_num=mysqli_num_rows($card_final_pay_qry);

	if($card_final_pay_num>1)
	{
		$h=1;
		while($card_final_pay_val=mysqli_fetch_array($card_final_pay_qry))
		{
			if($h>1)
			{
				mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `slno`='$card_final_pay_val[slno]' ");
			}
			$h++;
		}
	}
	///////////////////////
	
	echo $bill_id;
	
}
else if($type==5001)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$val=$_POST['val'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE patient_id='$uhid' and opd_id='$ipd' "));
	
	// Bed Charge Add
	
	$final_pay_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' "));

	$package_charge_entry_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='194' "));

	if($final_pay_num==0 && $val!="Update Remove")
	{
		if($package_charge_entry_num==0)
		{
			$bed_list=mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and alloc_type='1' order by slno");
			while($bd_lst=mysqli_fetch_array($bed_list))
			{
				
				$st_date="";
				$end_date="";
				$bed_d=mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and bed_id='$bd_lst[bed_id]' and slno>='$bd_lst[slno]' order by slno limit 2");
				
				while($bd_d=mysqli_fetch_array($bed_d))
				{
					if($bd_d['alloc_type']==1)
					{
						$st_date=$bd_d[date];	
					}
					else if($bd_d['alloc_type']==0)
					{
						$end_date=$bd_d[date];
					}
				}
				
				if($end_date=="")
				{
					$end_date=date("Y-m-d");
				}
				//echo $end_date;
				$diff=abs(strtotime($st_date)-strtotime($end_date));
				$diff=$diff/60/60/24;
				$n_diff=$diff+1;
			
				$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bd_lst[bed_id]'"));
						
				for($i=0;$i<$diff;$i++)
				{
					$n_date=date('Y-m-d', strtotime($st_date. ' + '.$i.' days'));
					$chk_serv=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and service_id='$bed_det[charge_id]' and date='$n_date'"));
					
					if($chk_serv==0)
					{
						$charge_det=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$bed_det[charge_id]'"));
						
						// Bed Charge
						mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','$charge_det[group_id]','$bed_det[charge_id]','$charge_det[charge_name]','1','$charge_det[amount]','$charge_det[amount]','1','$_SESSION[emp_id]','$al_time','$n_date','$bd_lst[bed_id]')");
						
						// Bed Charge Plus
						$other_charge=mysqli_query($link,"select * from bed_other_charge where bed_id='$bed_det[bed_id]'");
						while($ot_ch=mysqli_fetch_array($other_charge))
						{
							$charge=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_id='$ot_ch[charge_id]'"));
							
							mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$uhid','$ipd','$charge[group_id]','$ot_ch[charge_id]','$charge[charge_name]','1','$charge[amount]','$charge[amount]','1','$_SESSION[emp_id]','$al_time','$n_date','$bd_lst[bed_id]')");
						}
					}
				}
			}
		}
	}

	
	
	$baby_serv_tot=0;
	$baby_ot_total=0;
	$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
	while($delivery_check=mysqli_fetch_array($delivery_qry))
	{
		$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
		$baby_serv_tot+=$baby_tot_serv["tots"];
		
		// OT Charge Baby
		$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
		$baby_ot_total+=$baby_ot_tot_val["g_tot"];
		
	}
	
	$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
	$no_of_days=$no_of_days_val["ser_quantity"];
	
	$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
	$tot_serv_amt1=$tot_serv1["tots"];
	//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
	
	$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
	$tot_serv_amt2=$tot_serv2["tots"];
	
	$ot_total=0;
	if($pat_reg["type"]==3) // If Caualty or day care and has entry ot, skip ot
	{
		// OT Charge
		$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
		$ot_total=$ot_tot_val["g_tot"];
	}
	// Total Amount
	$tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
	
	$adv_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as advs FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Advance' "));
	$adv_serv_amt=$adv_serv["advs"];
	
	//$bal_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as advs FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Balance' "));
	$bal_serv_amt=$bal_serv["advs"];
	
	$pat_refund=mysqli_fetch_array(mysqli_query($link," SELECT sum(`refund`) as rfnd FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
	$pat_refund_amt=$pat_refund["rfnd"];
	
	$final_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as final,sum(`discount`) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
	$final_serv_amt=$final_serv["final"];
	$adv_serv_dis=$final_serv["discnt"];
	
	$balance_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as final,sum(`discount`) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Balance' "));
	$balance_serv_amt=$balance_serv["final"];
	$balance_serv_dis=$balance_serv["discnt"];
	
	//echo number_format(($tot_serv_amt-$adv_serv_amt-$bal_serv_amt-$final_serv_amt-$adv_serv_dis+$pat_refund_amt),2);
	
	$delivery_check_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE baby_uhid='$uhid' and baby_ipd_id='$ipd' "));
	if($delivery_check_val)
	{
		$final_serv_rel=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE patient_id='$delivery_check_val[patient_id]' and ipd_id='$delivery_check_val[ipd_id]' and pay_type='Final' "));
		
		if($final_serv_rel)
		{
			$pending_amount=0;
		}else
		{
			$pending_amount=($tot_serv_amt-$adv_serv_amt-$final_serv_amt-$adv_serv_dis-$balance_serv_amt-$balance_serv_dis+$pat_refund_amt);
		}

	}else
	{
		$pending_amount=($tot_serv_amt-$adv_serv_amt-$final_serv_amt-$adv_serv_dis-$balance_serv_amt-$balance_serv_dis+$pat_refund_amt);
	}
	
	echo number_format($pending_amount,2);
	
}
else if($type==6001)
{
	$uhid=$_POST['uhid'];
	$ipd_id=$_POST['ipd'];
	$bill=$_POST['bill'];
	$usr=$_POST['usr'];
	$reason=mysqli_real_escape_string($link, $_POST['reason']);
	$type=3; // IPD Patient && $type=1 for OPD && &type=2 for Lab && $type=4 for Casualty
	
	$cancel_pat_bill=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `bill_no`='$bill' "));
	if($cancel_pat_bill["pay_type"]=="Final")
	{
		mysqli_query($link, " DELETE FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' ");
		mysqli_query($link, " DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `pay_mode`='Credit' ");
		mysqli_query($link, " DELETE FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' ");
	}
	
	mysqli_query($link, " DELETE FROM `ipd_advance_payment_details_cancel` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `bill_no`='$bill' ");
	mysqli_query($link, " INSERT INTO `ipd_advance_payment_details_cancel`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `time`, `date`, `user`) VALUES ('$cancel_pat_bill[patient_id]','$cancel_pat_bill[ipd_id]','$cancel_pat_bill[bill_no]','$cancel_pat_bill[tot_amount]','$cancel_pat_bill[discount]','$cancel_pat_bill[amount]','$cancel_pat_bill[balance]','$cancel_pat_bill[refund]','$cancel_pat_bill[pay_type]','$cancel_pat_bill[pay_mode]','$cancel_pat_bill[time]','$cancel_pat_bill[date]','$cancel_pat_bill[user]') ");
	
	$discount_cancel=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_discount_reason` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `bill_no`='$bill' "));
	if($discount_cancel)
	{
		mysqli_query($link," INSERT INTO `patient_discount_reason_cancel`(`patient_id`, `ipd_id`, `bill_no`, `reason`) VALUES ('$discount_cancel[patient_id]','$discount_cancel[ipd_id]','$discount_cancel[bill_no]','$discount_cancel[reason]') ");
		
		mysqli_query($link, " DELETE FROM `patient_discount_reason` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `bill_no`='$bill' ");
	}
	
	$pay_refund=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id' "));
	if($pay_refund)
	{
		mysqli_query($link," DELETE FROM `invest_payment_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id' ");
		mysqli_query($link," DELETE FROM `invest_payment_refund_cancel` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id' ");
		mysqli_query($link," INSERT INTO `invest_payment_refund_cancel`(`patient_id`, `opd_id`, `ipd_id`, `tot_amount`, `refund_amount`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$ipd_id','','$pay_refund[tot_amount]','$pay_refund[refund_amount]','$pay_refund[reason]','$pay_refund[date]','$pay_refund[time]','$pay_refund[user]') ");
		
	}
	
	mysqli_query($link," INSERT INTO `cancel_payment`(`patient_id`, `ipd_id`, `bill_no`, `amount`, `discount`, `reason`, `type`, `user`, `date`, `time`) VALUES ('$uhid','$ipd_id','$bill','$cancel_pat_bill[amount]','$cancel_pat_bill[discount]','$reason','$type','$usr','$date','$time') ");
	mysqli_query($link, " DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `bill_no`='$bill' ");
	
}
else if($type==6002)
{
	$uhid=$_POST['uhid'];
	$ipd_id=$_POST['ipd'];
	$usr=$_POST['user'];
	$reason=mysqli_real_escape_string($link, $_POST['reason']);
	
	$uhid_opd_id=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd_id' "));
	
	$type=$uhid_opd_id['type']; // Casualty Patient && $type=1 for OPD && &type=2 for Lab & $type=3 for IPD
	
	$cancel_pat_bill_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' ");
	while($cancel_pat_bill=mysqli_fetch_array($cancel_pat_bill_qry))
	{
		
		//mysqli_query($link, " DELETE FROM `ipd_advance_payment_details_cancel` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' ");
		mysqli_query($link, " INSERT INTO `ipd_advance_payment_details_cancel`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `time`, `date`, `user`) VALUES ('$cancel_pat_bill[patient_id]','$cancel_pat_bill[ipd_id]','$cancel_pat_bill[bill_no]','$cancel_pat_bill[tot_amount]','$cancel_pat_bill[discount]','$cancel_pat_bill[amount]','$cancel_pat_bill[balance]','$cancel_pat_bill[refund]','$cancel_pat_bill[pay_type]','$cancel_pat_bill[pay_mode]','$cancel_pat_bill[time]','$cancel_pat_bill[date]','$cancel_pat_bill[user]') ");
		
		mysqli_query($link," INSERT INTO `cancel_payment`(`patient_id`, `ipd_id`, `bill_no`, `amount`, `discount`, `reason`, `type`, `user`, `date`, `time`) VALUES ('$uhid','$ipd_id','$cancel_pat_bill[bill_no]','$cancel_pat_bill[amount]','$cancel_pat_bill[discount]','$reason','$type','$usr','$date','$time') ");
	}
	mysqli_query($link, " DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' ");
	
	$discount_cancel=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_discount_reason` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
	if($discount_cancel)
	{
		mysqli_query($link," INSERT INTO `patient_discount_reason_cancel`(`patient_id`, `ipd_id`, `bill_no`, `reason`) VALUES ('$discount_cancel[patient_id]','$discount_cancel[ipd_id]','$discount_cancel[bill_no]','$discount_cancel[reason]') ");
		
		mysqli_query($link, " DELETE FROM `patient_discount_reason` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' ");
	}
	
}
else if($type=="admit_reason_save")
{
	$uhid=$_POST['uhid'];
	$ipd_id=$_POST['ipd'];
	$user=$_POST['user'];
	$admit_reason=mysqli_real_escape_string($link, $_POST['admit_reason']);
	
	$check_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_admit_reason` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' "));
	if($check_num==0)
	{
		mysqli_query($link, " INSERT INTO `ipd_pat_admit_reason`(`patient_id`, `ipd_id`, `admit_reason`, `date`, `time`, `user`) VALUES ('$uhid','$ipd_id','$admit_reason','$date','$time','$user') ");
		echo "Saved";
	}else
	{
		mysqli_query($link, " UPDATE `ipd_pat_admit_reason` SET `admit_reason`='$admit_reason',`date`='$date',`time`='$time',`user`='$user' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' ");
		echo "Updated";
	}
}

if($type==6010)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$medi=mysqli_real_escape_string($link, $_POST['medi']);
	$usr=$_POST['usr'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($n>0)
	{
		mysqli_query($link,"UPDATE `ipd_pat_medicine_final` SET `medicine`='$medi',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_medicine_final`(`patient_id`, `ipd_id`, `medicine`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$medi','$date','$time','$usr')");
	}
	echo "Saved";
}

if($type==6011)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$user=$_POST['user'];
	$pat_totalbd=$_POST['pat_totalbd'];
	$pat_discbd=$_POST['pat_discbd'];
	if(!$pat_discbd)
	{
		$pat_discbd=0;
	}
	$pay_advancebd=$_POST['pay_advancebd'];
	if(!$pay_advancebd)
	{
		$pay_advancebd=0;
	}
	$pat_balancebd=$_POST['pat_balancebd'];
	$p_modebd=$_POST['p_modebd'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
	$bill_name=$pat_typ_text["bill_name"];
	
	//~ $tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
	//~ $tot_serv_amt1=$tot_serv1["tots"];
	//~ $tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
	//~ $tot_serv_amt2=$tot_serv2["tots"];
	
	//~ $tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2;
	
	//~ $adv_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as advs FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Advance' "));
	//~ $adv_serv_amt=$adv_serv["advs"];
	
	//~ $final_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as final,sum(`discount`) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final' "));
	//~ $final_serv_amt=$final_serv["final"];
	//~ $adv_serv_dis=$final_serv["discnt"];
	
	//~ $balance_amt=$tot_serv_amt-$adv_serv_amt-$final_serv_amt-$adv_serv_dis;
	
	$final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' ");
	$final_pay_num=mysqli_num_rows($final_pay_qry);
	if($final_pay_num==0)
	{
		if($pat_balancebd>0)
		{
			mysqli_query($link," INSERT INTO `ipd_discharge_balance_pat`(`patient_id`, `ipd_id`, `bal_amount`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$pat_balancebd','$user','$date','$time') ");
		}
		
		$bill_no=101;
		$date2=date("Y-m-d");
		$date1=explode("-",$date2);	
		$c_var=$date1[0]."-".$date1[1];
		$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from ipd_advance_payment_details where date like '$c_var%'"));
		
		if($chk['tot_bill']>0)
		{
			$bill_no=$bill_no+$chk['tot_bill'];
		}
		$chk_cancel=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill_cancel from ipd_advance_payment_details_cancel where date like '$c_var%'"));
		$bill_no=$bill_no+$chk_cancel['tot_bill_cancel'];
		
		$date4=date("y-m-d");
		$date3=explode("-",$date4);
		
		$random_no=rand(1,9);
		
		$bill_id=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0])."/".$bill_name;
		
		mysqli_query($link," INSERT INTO `ipd_advance_payment_details`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `time`, `date`, `user`) VALUES ('$uhid','$ipd','$bill_id','$pat_totalbd','$pat_discbd','$pay_advancebd','$pat_balancebd','0','Final','$p_modebd','$time','$date','$user') ");
	}
	$final_pay_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `pay_type`='Final' ");
	$final_pay_num=mysqli_num_rows($final_pay_qry);
	if($final_pay_num>1)
	{
		$h=1;
		while($final_pay_val=mysqli_fetch_array($final_pay_qry))
		{
			if($h>1)
			{
				mysqli_query($link," DELETE FROM `ipd_advance_payment_details` WHERE `slno`='$final_pay_val[slno]' ");
			}
			$h++;
		}
	}
	
}
else if($type==701)
{
	$slno=$_POST['val'];
	
	$det=mysqli_fetch_array(mysqli_query($link,"select * from ot_pat_service_details where slno='$slno'"));
	
	$tot_num=1;
	
?>
	<input type="hidden" id="slno" value="<?php echo $slno;?>"/>
		<table class="table table-condensed table-bordered">
		<tr>
			<th>Service</th>
			<th><input type="text" id="edit_text" value="<?php echo $det["service_text"];?>"/></th>
		</tr>
	<?php
	
	if($rel_slno_num>0)
	{
	?>
		<tr>
			<th>Doctor</th>
			<th>
				<select id="consultantdoctorid">
					<option value="0">Select</option>
				<?php
					$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` order by `Name` ");
					while($con_doc=mysqli_fetch_array($con_doc_qry))
					{
						if($rel_slno_val["consultantdoctorid"]==$con_doc["consultantdoctorid"]){ $sel_doc="selected"; }else{ $sel_doc=""; }
						echo "<option value='$con_doc[consultantdoctorid]' $sel_doc >$con_doc[Name]</option>";
					}
				?>
				</select>
			</th>
		</tr>
	<?php
	}else
	{
		echo "<input type='hidden' id='consultantdoctorid' value='99999'>";
	}
	
	if($det[days]>0)
	{
	?>
	<tr>
		<th>Days</th>
		<td><input type="text" id="edit_days" value="<?php echo $det[days];?>" <?php echo $dis_field; ?>/></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<th>Quantity</th>
		<th>
			<!--<input type="text" id="ser_quantity" value="<?php echo $det[ser_quantity];?>"/>-->
			<select id='ser_quantity' <?php echo $dis_field; ?>>
			<?php
				for($i=1;$i<101;$i++)
				{
					if($det['ser_quantity']==$i){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$i' $sel>$i</option>";
				}
			?>
			</select>
		</th>
	</tr>
	<tr>
		<th>Rate</th>
		<td><input type="text" id="rate" value="<?php echo $det[rate];?>" <?php echo $dis_field; ?>/></td>
	</tr>
	<tr>
		<th>Time</th>
		<td><input type="text" id="edit_time" value="<?php echo $det[time];?>" <?php echo $dis_field; ?>/></td>
	</tr>
	<tr>
		<th>Date</th>
		<td><input type="text" id="edit_date" value="<?php echo $det[date];?>" <?php echo $dis_field; ?>/></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center">
		<?php if($tot_num==0){ ?>
			<button class="btn btn-info" onclick="service_edit(1)"><i class="icon-save"></i> Update</button>
			<button class="btn btn-danger" onclick="service_edit(2)"><i class="icon-trash"></i> Delete</button>
		<?php } ?>
			<button class="btn btn-default" onclick="$('#mod').click()"><i class="icon-remove-sign"></i> Close</button>
		</td>
	</tr>
	</table>
	<?php
}

///////// Consultant Doctor Transfer Start

if($type==901)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd'"));
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd'"));
	if($n>0)
	{
		$btn_val="Transfer";
		$btn_cls="btn-primary";
		$dis="";
		$func="upd_doc()";
	}
	else
	{
		$btn_val="Discharged";
		$btn_cls="btn-danger";
		$dis="Disabled";
		$func="";
	}
	$pat_doc_trans_qry=mysqli_query($link," SELECT * FROM `ipd_pat_doc_transfer` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `status`=1 ");
	$pat_doc_trans_num=mysqli_num_rows($pat_doc_trans_qry);
	
?>
	<table class="table table-condensed">
		<tr>
			<th style="width: 1%;">#</th>
			<th>Doctor Name</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
<?php
	$n=1;
	while($pat_doc_trans=mysqli_fetch_array($pat_doc_trans_qry))
	{
		$doc_doc_name=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_doc_trans[attend_doc]' "));
		
		$user_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$pat_doc_trans[user]' "));
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $doc_doc_name["Name"]; ?></td>
			<td>
				<?php echo convert_date_g($pat_doc_trans["date"]); ?> <?php echo convert_time($pat_doc_trans["time"]); ?>
			</td>
			<td>
				<?php echo $user_info["name"]; ?>
			<?php if($n==$pat_doc_trans_num && $n!=1){ ?>
				<button class="btn btn-mini btn-danger" style="float:right;" onClick="delete_ipd_con_doc('<?php echo $pat_doc_trans["slno"]; ?>','<?php echo $pat_doc_trans["attend_doc"]; ?>')"><i class="icon-remove"></i></button>
			<?php } ?>
			</td>
		</tr>
<?php
		$n++;
	}
?>
		<tr id="transfer_tr_btn">
			<td colspan="4">
				<button class="btn btn-success btn-mini" onClick="$('#transfer_tr').show();$('#transfer_tr_btn').hide();">Transfer</button>
			</td>
		</tr>
		<tr id="transfer_tr" style="display:none;">
			<th colspan="3">Select Doctor
				<select class="span3" id="adm_doc">
					<option value="0">Select</option>
					<?php
					//$dq=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5' ORDER BY `name`");
					$dq=mysqli_query($link,"SELECT `consultantdoctorid`, `Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($dr=mysqli_fetch_array($dq))
					{
					?>
						<option value="<?php echo $dr['consultantdoctorid'];?>" <?php if($v['attend_doc']==$dr['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $dr['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="btn <?php echo $btn_cls;?>" onclick="<?php echo $func;?>" <?php echo $dis;?>><?php echo $btn_val;?></button>
				<button type="button" class="btn btn-warning" onClick="$('#transfer_tr').hide();$('#transfer_tr_btn').show();" >Cancel</button>
			</td>
		</tr>
	</table>
	<?php
}

if($type==902)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$adm_doc=$_POST['adm_doc'];
	$usr=$_POST['usr'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($v['attend_doc']==$adm_doc)
	{
		echo "<h5>Same doctor selected</h5>";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$v[attend_doc]','0','$date','$time','$usr')");
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$adm_doc','1','$date','$time','$usr')");
		mysqli_query($link,"UPDATE `ipd_pat_doc_details` SET `attend_doc`='$adm_doc' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		
		echo "<h5>Doctor Transferred</h5>";
	}
}
if($type==903)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$slno=$_POST['slno'];
	$attend_doc=$_POST['attend_doc'];
	
	$ipd_pat_doc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_doc_transfer` WHERE `slno`='$slno' "));
	if($ipd_pat_doc)
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer_delete`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`, `del_user`) VALUES ('$uhid','$ipd','$ipd_pat_doc[attend_doc]','$ipd_pat_doc[status]','$ipd_pat_doc[date]','$ipd_pat_doc[time]','$ipd_pat_doc[user]','$usr')");
		
		mysqli_query($link," DELETE FROM `ipd_pat_doc_transfer` WHERE `slno`='$slno' ");
		
		$last_slno=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_doc_transfer` WHERE `slno` IN(SELECT MAX(`slno`) FROM `ipd_pat_doc_transfer` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `slno`<$slno ) "));
		
		$ipd_pat_doc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_doc_transfer` WHERE `slno`='$last_slno[slno]' "));
		
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer_delete`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`, `del_user`) VALUES ('$uhid','$ipd','$ipd_pat_doc[attend_doc]','$ipd_pat_doc[status]','$ipd_pat_doc[date]','$ipd_pat_doc[time]','$ipd_pat_doc[user]','$usr')");
		
		mysqli_query($link," UPDATE `ipd_pat_doc_details` SET `attend_doc`='$last_slno[attend_doc]' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
		
		mysqli_query($link," DELETE FROM `ipd_pat_doc_transfer` WHERE `slno`='$last_slno[slno]' ");
		
	}
}

///////// Consultant Doctor Transfer End
if($type=="payment_mode_change")
{
	$slno=$_POST['slno'];
	
	$pat_adv_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_advance_payment_details` WHERE `slno`='$slno' "));
?>
	<table class="table table-bordered">
		<tr>
			<th>Payment Mode</th>
			<td>
			<?php
				if($pat_adv_det["pay_mode"]=="Credit")
				{
					echo $pat_adv_det["pay_mode"];
				}else
				{
			?>
				<select id="edit_payment_mode">
					<option value="Cash" <?php if($pat_adv_det["pay_mode"]=="Cash"){ echo "selected"; } ?>>Cash</option>
					<option value="Card" <?php if($pat_adv_det["pay_mode"]=="Card"){ echo "selected"; } ?>>Card</option>
					<option value="Cheque" <?php if($pat_adv_det["pay_mode"]=="Cheque"){ echo "selected"; } ?>>Cheque</option>
				</select>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<center>
				<?php if($pat_adv_det["pay_mode"]!="Credit"){ ?>
					<button class="btn btn-info" onclick="update_payment_mode('<?php echo $slno; ?>')">Update</button>
				<?php } ?>
					<button type="button" class="btn btn-inverse" data-dismiss="modal">Close</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}
if($type=="payment_mode_update")
{
	$slno=$_POST['slno'];
	$payment_mode=$_POST['payment_mode'];
	$user=$_POST['user'];
	
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE slno='$slno' "));
	
	if($payment_mode=="Credit")
	{
		$balance_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$pat_pay_det[patient_id]' AND `ipd_id`='$pat_pay_det[ipd_id]' "));
		if($balance_check)
		{
			// update
			$total_balance=$balance_check["bal_amount"]+$pat_pay_det["amount"];
			mysqli_query($link," UPDATE `ipd_discharge_balance_pat` SET `bal_amount`='$total_balance' WHERE `patient_id`='$pat_pay_det[patient_id]' AND `ipd_id`='$pat_pay_det[ipd_id]' ");
		}
		else
		{
			// insert
			mysqli_query($link," INSERT INTO `ipd_discharge_balance_pat`(`patient_id`, `ipd_id`, `bal_amount`, `user`, `date`, `time`) VALUES ('$pat_pay_det[patient_id]','$pat_pay_det[ipd_id]','$pat_pay_det[amount]','$user','$date','$time') ");
		}
		mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `amount`='0',`balance`='$pat_pay_det[amount]',`pay_mode`='$payment_mode' WHERE `slno`='$slno' ");
	}
	else
	{
		mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `pay_mode`='$payment_mode' WHERE `slno`='$slno' ");
	}
	
	mysqli_query($link," INSERT INTO `payment_mode_change`(`patient_id`, `ipd_id`, `bill_no`, `pay_mode`, `user`, `date`, `time`) VALUES ('$pat_pay_det[patient_id]','$pat_pay_det[ipd_id]','$pat_pay_det[bill_no]','$pat_pay_det[pay_mode]','$user','$date','$time') ");
}

if($type=="payment_mode_change_other")
{
	$uhid=$_POST['uhid'];
	$ipd_id=$_POST['ipd_id'];
	
	$pat_pay_det_qry=mysqli_query($link,"SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `pay_type`='Final' ");
	
?>
	<table class="table table-bordered">
		<tr>
			<th>Amount</th>
			<th>Payment Mode</th>
			<th></th>
		</tr>
<?php
	while($pat_pay_det=mysqli_fetch_array($pat_pay_det_qry))
	{
		if($pat_pay_det["pay_mode"]=="Credit")
		{
?>
			<tr>
				<td><?php echo $pat_pay_det["balance"]; ?></td>
				<td><?php echo $pat_pay_det["pay_mode"]; ?></td>
				<td></td>
			</tr>
<?php
		}
		else
		{
?>
			<tr>
				<td><?php echo $pat_pay_det["amount"]; ?></td>
				<td>
					<select class="span2" id="edit_payment_mode">
						<!--<option value="Cash" <?php if($pat_pay_det["pay_mode"]=="Cash"){ echo "selected"; } ?> >Cash</option>
						<option value="Card" <?php if($pat_pay_det["pay_mode"]=="Card"){ echo "selected"; } ?> >Card</option>
						<option value="Cheque" <?php if($pat_pay_det["pay_mode"]=="Cheque"){ echo "selected"; } ?> >Cheque</option>
						<option value="Credit" <?php if($pat_pay_det["pay_mode"]=="Credit"){ echo "selected"; } ?> >Credit</option>-->
					<?php
						$pay_mode_qry=mysqli_query($link, " SELECT `p_mode_id`,`p_mode_name` FROM `payment_mode_master` WHERE `status`=0 ORDER BY `sequence` ");
						while($pay_mode=mysqli_fetch_array($pay_mode_qry))
						{
							if($pat_pay_det["pay_mode"]==$pay_mode["p_mode_name"]){ $sel="selected"; }else{ $sel=""; }
							echo "<option value='$pay_mode[p_mode_name]' $sel>$pay_mode[p_mode_name]</option>";
						}
					?>
					</select>
				</td>
				<td>
					<button class="btn btn-save" onclick="update_payment_mode_other('<?php echo $pat_pay_det["slno"]; ?>')"><i class="icon-save"></i> Update</button>
				</td>
			</tr>
<?php
		}
	}
	/// If Balance received
	$pat_pay_det_qry=mysqli_query($link,"SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `pay_type`='Balance' ");
	while($pat_pay_det=mysqli_fetch_array($pat_pay_det_qry))
	{
?>
		<tr>
			<td><?php echo $pat_pay_det["amount"]; ?></td>
			<td>
				<select class="span2" id="edit_payment_mode">
					<!--<option value="Cash" <?php if($pat_pay_det["pay_mode"]=="Cash"){ echo "selected"; } ?> >Cash</option>
					<option value="Card" <?php if($pat_pay_det["pay_mode"]=="Card"){ echo "selected"; } ?> >Card</option>-->
				<?php
					$pay_mode_qry=mysqli_query($link, " SELECT `p_mode_id`,`p_mode_name` FROM `payment_mode_master` WHERE `operation`=1 AND `status`=0 ORDER BY `sequence` ");
					while($pay_mode=mysqli_fetch_array($pay_mode_qry))
					{
						if($pat_pay_det["pay_mode"]==$pay_mode["p_mode_name"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$pay_mode[p_mode_name]' $sel>$pay_mode[p_mode_name]</option>";
					}
				?>
				</select>
			</td>
			<td>
				<button class="btn btn-save" onclick="update_payment_mode_other('<?php echo $pat_pay_det["slno"]; ?>')"><i class="icon-save"></i> Update</button>
			</td>
		</tr>
<?php
	}
?>
		<tr>
			<td colspan="3">
				<center>
					<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}

if($type=="payment_mode_change_other_old")
{
	$uhid=$_POST['uhid'];
	$ipd_id=$_POST['ipd_id'];
	
	$pat_cash_amount=$pat_card_amount=$pat_discount=$total_paid_amount=0;
	
	$pat_adv_det_cash=mysqli_fetch_array(mysqli_query($link,"SELECT `amount`,`discount` FROM `ipd_advance_payment_details` WHERE `ipd_id`='$ipd_id' AND `pay_mode`='Cash' AND `pay_type`='Final' "));
	if($pat_adv_det_cash)
	{
		$pat_cash_amount=$pat_adv_det_cash["amount"];
		$total_paid_amount+=$pat_adv_det_cash["amount"];
		$pat_discount+=$pat_adv_det_cash["discount"];
	}
	
	$pat_adv_det_card=mysqli_fetch_array(mysqli_query($link,"SELECT `amount`,`discount` FROM `ipd_advance_payment_details` WHERE `ipd_id`='$ipd_id' AND `pay_mode`='Card' AND `pay_type`='Final' "));
	if($pat_adv_det_card)
	{
		$pat_card_amount=$pat_adv_det_card["amount"];
		$total_paid_amount+=$pat_adv_det_card["amount"];
		$pat_discount+=$pat_adv_det_card["discount"];
	}
	
	$tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE ipd_id='$ipd_id' "));
	$tot_serv_amt=$tot_serv["tots"];
	
?>
	<table class="table table-bordered">
		<tr>
			<th>Bill Amount</th>
			<td id="edit_bill_amount">
				<?php echo number_format($tot_serv_amt,2); ?>
			</td>
		</tr>
		<tr>
			<th>Discount</th>
			<td id="edit_discount_amount">
				<?php echo number_format($pat_discount,2); ?>
			</td>
		</tr>
		<tr>
			<th>Paid Amount</th>
			<td>
				<input type="text" class="span1" id="edit_cash_mode" onkeyup="edit_cash_mode_up()" value="<?php echo $total_paid_amount; ?>" readonly >
				<select class="span2" id="edit_payment_mode">
					<option value="Cash" <?php if($pat_cash_amount>0){ echo "selected"; } ?> >Cash</option>
					<option value="Card" <?php if($pat_card_amount>0){ echo "selected"; } ?>>Card</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<center>
			<?php if(!($pat_cash_amount>0 && $pat_card_amount>0)){ ?>
					<button class="btn btn-info" onclick="update_payment_mode_other('<?php echo $ipd_id; ?>')">Update</button>
			<?php } ?>
					<button type="button" class="btn btn-inverse" data-dismiss="modal">Close</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}
if($type=="payment_mode_update_other")
{
	$ipd=$ipd_id=$_POST['ipd_id'];
	$payment_mode=$_POST['payment_mode'];
	$user=$_POST['user'];
	
	//~ $pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE opd_id='$ipd_id' "));
	//~ $uhid=$pat_reg["patient_id"];
	
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE ipd_id='$ipd_id' "));
	
	mysqli_query($link," UPDATE `ipd_advance_payment_details` SET `pay_mode`='$payment_mode' WHERE `ipd_id`='$ipd_id' AND `pay_type`='Final' ");
	
	mysqli_query($link," INSERT INTO `payment_mode_change`(`patient_id`, `ipd_id`, `bill_no`, `pay_mode`, `user`, `date`, `time`) VALUES ('$pat_pay_det[patient_id]','$ipd','$pat_pay_det[bill_no]','$pat_pay_det[pay_mode]','$user','$date','$time') ");
	
}
if($type=="datepicker_min_max")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd'"));
	
	$pat_discharge=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' and pay_type='Final'"));
	if($pat_discharge["date"])
	{
		$discharge_date=$pat_discharge["date"];
	}
	else
	{
		$discharge_date=0;
	}
	
	echo $pat_reg["date"]."@@".$discharge_date;
}
?>
