<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `branch_id`,`edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$_POST['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_access["branch_id"];
}

$discount_attribute="";
if($emp_access["discount_permission"]==0)
{
	$discount_attribute="readonly";
}

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="load_all_pat")
{
	$search_data=mysqli_real_escape_string($link, $_POST["search_data"]);
	$list_start=mysqli_real_escape_string($link, $_POST["list_start"]);
	
	$all_pin="";
	
	$encounter_pay_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type=$pat_typ_text["type"];
	}
	
	$i=1;
	if($encounter==0 || $encounter_pay_type==1)
	{
		$con_pay_qry=mysqli_query($link, " SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `balance`>0");
		while($con_pay=mysqli_fetch_array($con_pay_qry))
		{
			if($all_pin=="")
			{
				$all_pin.="'".$con_pay["opd_id"]."'";
			}
			else
			{
				$all_pin.=",'".$con_pay["opd_id"]."'";
			}
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==2)
	{
		$inv_pay_qry=mysqli_query($link, " SELECT `opd_id` FROM `invest_patient_payment_details` WHERE `balance`>0");
		while($inv_pay=mysqli_fetch_array($inv_pay_qry))
		{
			if($all_pin=="")
			{
				$all_pin.="'".$inv_pay["opd_id"]."'";
			}
			else
			{
				$all_pin.=",'".$inv_pay["opd_id"]."'";
			}
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==3)
	{
		$ipd_pay_qry=mysqli_query($link, " SELECT `ipd_id` FROM `ipd_discharge_balance_pat` WHERE `bal_amount`>0");
		while($ipd_pay=mysqli_fetch_array($ipd_pay_qry))
		{
			if($all_pin=="")
			{
				$all_pin.="'".$ipd_pay["ipd_id"]."'";
			}
			else
			{
				$all_pin.=",'".$ipd_pay["ipd_id"]."'";
			}
			$i++;
		}
	}
	
	//echo $all_pin;
	
	$str=" SELECT * FROM `uhid_and_opdid` WHERE `opd_id` IN($all_pin) ";
	
	
	if(strlen($search_data)>2)
	{
		$str=" SELECT * FROM `uhid_and_opdid` WHERE `opd_id`!='' AND (`opd_id` LIKE '$search_data%')"; // `patient_id` LIKE '%$search_data%' OR 
	}
	
	$str.=" AND `branch_id`='$branch_id' ORDER BY `slno` DESC LIMIT ".$list_start;
	
	//echo $str;
?>
	<table class="table table-bordered table-condensed" id="tblData">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<!--<th>Age</th>
			<th>Sex</th>-->
			<th>Bill Amount</th>
			<th>Balance Amount</th>
			<th>Encounter</th>
			<th>Reg Date</th>
		</tr>
<?php
	$qry=mysqli_query($link, $str); // 
	$n=1;
	while($pat_reg=mysqli_fetch_array($qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
		
		$reg_date=$pat_reg["date"];
		
		if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
		
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
		$encounter=$pat_typ_text["p_type"];
		
		if($pat_typ_text["type"]==1)
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
			
			$bill_amount=$pat_pay_det["tot_amount"];
			$discount_amount=$pat_pay_det["dis_amt"];
			$paid_amount=$pat_pay_det["advance"];
			$tax_amount=$pat_pay_det["tax_amount"];
			$refund_amount=$pat_pay_det["refund_amount"];
			$balance_amount=$pat_pay_det["balance"];
		}
		
		if($pat_typ_text["type"]==2)
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
			
			$bill_amount=$pat_pay_det["tot_amount"];
			$discount_amount=$pat_pay_det["dis_amt"];
			$paid_amount=$pat_pay_det["advance"];
			$tax_amount=$pat_pay_det["tax_amount"];
			$refund_amount=$pat_pay_det["refund_amount"];
			$balance_amount=$pat_pay_det["balance"];
		}
		
		if($pat_typ_text["type"]==3)
		{
			$uhid=$pat_reg["patient_id"];
			$ipd=$pat_reg["opd_id"];
			
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
			$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
			
			$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));
			
			$already_paid      =$check_paid["paid"];
			$already_discount  =$check_paid["discount"];
			$already_refund    =$check_paid["refund"];
			$already_tax       =$check_paid["tax"];
			
			$settle_amount=$already_paid+$already_discount+$already_tax-$already_refund;
			
			$balance_amount=$bill_amount-$settle_amount;
		}
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' AND `type`='2' "));
		if($cancel_request)
		{
			$td_function="";
			$td_style="";
			$tr_back_color="style='background-color: #ff000021'";
			
			$emp_info_del=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request[user]' "));
			
			$tr_title="title='Cancel request by $emp_info_del[name]' id='cancel_request_tr'";
		}
		else
		{
			$td_function="onclick=\"redirect_page('$pat_reg[patient_id]','$pat_reg[opd_id]')\"";
			$td_style="style='cursor:pointer;'";
			$tr_back_color="";
			$tr_title="";
		}
	?>
		<!--<tr onClick="redirect_ipd_balance('<?php echo $pat_reg["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')" style="cursor:pointer;">-->
		<tr <?php echo $tr_back_color." ".$tr_title." ".$td_style." ".$td_function; ?> >
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_reg["patient_id"]; ?></td>
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<!--<td><?php echo $age; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>-->
			<td><?php echo indian_currency_format($bill_amount); ?></td>
			<td><?php echo indian_currency_format($balance_amount); ?></td>
			<td><?php echo $encounter; ?></td>
			<td><?php echo convert_date_g($pat_reg["date"]); ?></td>
		</tr>
	<?php
		$n++;
	}
?>
	</table>
<?php
}

if($_POST["type"]=="load_payment_info")
{
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
	
	$prefix_name=$pat_typ_text["prefix"];
	
	$payment_det_qry=mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Credit' ORDER BY `pay_id` ASC");
	$payment_det_num=mysqli_num_rows($payment_det_qry);
	if($payment_det_num>0)
	{
?>
		<table class="table table-condensed">
			<tr>
				<th>#</th>
				<!--<th>UHID</th>
				<th><?php echo $prefix_name; ?></th>-->
				<th>Transaction No</th>
				<th>Amount</th>
				<th>Discount</th>
				<th>Refund</th>
				<th>Payment Type</th>
				<th>Payment Mode</th>
				<th>Date-Time</th>
				<th>User</th>
			</tr>
		<?php
			$zz=1;
			while($payment_det=mysqli_fetch_array($payment_det_qry))
			{
				$pay_mode_type=mysqli_fetch_array(mysqli_query($link, "SELECT `operation` FROM `payment_mode_master` WHERE `p_mode_name`='$payment_det[payment_mode]'"));
				
				$user_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$payment_det[user]'"));
				
				$opd_payment_mode_trans_disable="";
				if($payment_det["amount"]==0 && $payment_det["refund_amount"]==0)
				{
					$opd_payment_mode_trans_disable="disabled";
				}
		?>
				<tr id="opd_trans<?php echo $zz; ?>">
					<td><?php echo $zz; ?></td>
					<!--<td><?php echo $payment_det["patient_id"]; ?></td>
					<td><?php echo $payment_det["opd_id"]; ?></td>-->
					<td><?php echo $payment_det["transaction_no"]; ?></td>
					<td><?php echo $payment_det["amount"]; ?></td>
					<td><?php echo $payment_det["discount_amount"]; ?></td>
					<td><?php echo $payment_det["refund_amount"]; ?></td>
					<td><?php echo $payment_det["payment_type"]; ?></td>
					<td>
						<select class="span1" id="opd_payment_mode_trans<?php echo $payment_det["pay_id"]; ?>" onchange="payment_mode_change_trans('<?php echo $payment_det["pay_id"]; ?>')" <?php echo $opd_payment_mode_trans_disable; ?>>
						<?php
							$pay_mode_master_qry=mysqli_query($link, "SELECT `p_mode_name` FROM `payment_mode_master` WHERE `operation`='$pay_mode_type[operation]' ORDER BY `sequence` ASC");
							while($pay_mode_master=mysqli_fetch_array($pay_mode_master_qry))
							{
								if($pay_mode_master["p_mode_name"]==$payment_det["payment_mode"]){ $sel="selected"; }else{ $sel=""; }
								echo "<option value='$pay_mode_master[p_mode_name]' $sel>$pay_mode_master[p_mode_name]</option>";
							}
						?>
						</select>
						<br>
						<input type="hidden" class="span2" id="opd_cheque_ref_no<?php echo $payment_det["pay_id"]; ?>" value="<?php echo $payment_det["cheque_ref_no"]; ?>" placeholder="cheque_ref_no">
					</td>
					<td><?php echo date("d-M-Y", strtotime($payment_det["date"])); ?> - <?php echo date("h:i A", strtotime($payment_det["time"])); ?></td>
					<td>
						<?php echo $user_info["name"]; ?>
				<?php if($payment_det["amount"]!=0 || $payment_det["refund_amount"]!=0){ ?>
						<button class="btn btn-print btn-mini" style="float:right;" onclick="print_transaction('<?php echo $payment_det["pay_id"]; ?>')"><i class="icon-print"></i></button>
				<?php
					}
					if($payment_det_num==$zz && $payment_det_num>0 && $payment_det["payment_type"]=="Balance" && $payment_det["amount"]>0)
					{
						if($emp_access["edit_payment"]==1){
				?>
						<button class="btn btn-delete btn-mini" style="float:right;" onclick="delete_receipt('<?php echo $payment_det["pay_id"]; ?>')"><i class="icon-remove"></i></button>
				<?php
						}
					}
				?>
					</td>
				</tr>
		<?php
				$zz++;
			}
		?>
		</table>
<?php
	}
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
	$encounter=$pat_typ_text["p_type"];

	if($pat_typ_text["type"]==1)
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
		
		$bill_amount=$pat_pay_det["tot_amount"];
		$discount_amount=$pat_pay_det["dis_amt"];
		$paid_amount=$pat_pay_det["advance"];
		$tax_amount=$pat_pay_det["tax_amount"];
		$refund_amount=$pat_pay_det["refund_amount"];
		$balance_amount=$pat_pay_det["balance"];
	}

	if($pat_typ_text["type"]==2)
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
		
		$bill_amount=$pat_pay_det["tot_amount"];
		$discount_amount=$pat_pay_det["dis_amt"];
		$paid_amount=$pat_pay_det["advance"];
		$tax_amount=$pat_pay_det["tax_amount"];
		$refund_amount=$pat_pay_det["refund_amount"];
		$balance_amount=$pat_pay_det["balance"];
	}

	if($pat_typ_text["type"]==3)
	{
		$uhid=$pat_reg["patient_id"];
		$ipd=$pat_reg["opd_id"];
		
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
		$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
		
		$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));
		
		$paid_amount      =$check_paid["paid"];
		$discount_amount  =$check_paid["discount"];
		$refund_amount    =$check_paid["refund"];
		$tax_amount       =$check_paid["tax"];
		
		$settle_amount=$paid_amount+$discount_amount+$tax_amount-$refund_amount;
		
		$balance_amount=$bill_amount-$settle_amount;
	}
	
	if(!$bill_amount){ $bill_amount=0; }
	if(!$paid_amount){ $paid_amount=0; }
	if(!$disount_amount){ $disount_amount=0; }
	if(!$refund_amount){ $refund_amount=0; }
	if(!$refund_amount){ $refund_amount=0; }
	if(!$tax_amount){ $tax_amount=0; }
	if(!$balance_amount){ $balance_amount=0; }
	
	$paid_amount+=$refund_amount;
?>
	<div id="res_payment_div">
		<table class="table table-condensed">
			<tr>
				<th style="width: 25%;">Total Bill Amount</th>
				<td>
					<span id="opd_bill_amount_str"><?php echo number_format($bill_amount,2); ?></span>
					<input type="hidden" id="total" value="<?php echo $bill_amount; ?>">
				</td>
			</tr>
			<tr>
				<th>Discount Amount</th>
				<td>
					<span id="opd_disount_amount_str"><?php echo number_format($discount_amount,2); ?></span>
					<input type="hidden" id="opd_disount_amount" value="<?php echo $discount_amount; ?>">
				</td>
			</tr>
			<tr>
				<th>Paid Amount</th>
				<td>
					<span id="opd_paid_amount_str"><?php echo number_format($paid_amount,2); ?></span>
					<input type="hidden" id="opd_paid_amount" value="<?php echo $paid_amount; ?>">
				</td>
			</tr>
	<?php
		if($refund_amount>0)
		{
	?>
			<tr>
				<th>Refunded Amount</th>
				<td>
					<span id="opd_refunded_amount_str"><?php echo number_format($refund_amount,2); ?></span>
					<input type="hidden" id="opd_refunded_amount" value="<?php echo $refund_amount; ?>">
				</td>
			</tr>
	<?php
		}
	?>
			<tr>
				<th>Balance Amount</th>
				<td>
					<span id="opd_balance_amount_str"><?php echo number_format($balance_amount,2); ?></span>
					<input type="hidden" id="opd_balance_amount" value="<?php echo $balance_amount; ?>">
				</td>
			</tr>
			<tr id="now_discount_tr">
				<th>Now Discount</th>
				<td>
					<input type="text" class="span1 numericcfloat discount_cls" id="opd_now_discount_per" value="" onkeyup="opd_discount_per(event)" placeholder="%" onpaste="return false;" ondrop="return false;" maxlength="4" <?php echo $discount_attribute; ?>>%
					
					<input type="text" class="span1 numericc discount_cls" id="opd_now_discount_amount" value="0" onkeyup="opd_discount_amount(event)" onpaste="return false;" ondrop="return false;" <?php echo $discount_attribute; ?>>INR
					<br>
					<input type="hidden" class="span2" id="opd_now_discount_reason" onkeyup="opd_now_discount_reason(event)" value="" placeholder="Discount Reason">
				</td>
			</tr>
			<tr id="opd_now_pay_tr">
				<th>Now Pay</th>
				<td>
					<input type="text" class="span2 numericc" id="opd_now_pay" value="<?php echo $balance_amount; ?>" onkeyup="opd_now_pay(event)" onpaste="return false;" ondrop="return false;">
				</td>
			</tr>
			<tr id="opd_now_refund_tr" style="color:red;display:none;">
				<th>Amount To Refund</th>
				<td>
					<b id="opd_now_refund_str">0.00</b>
					<input type="hidden" class="span2" id="opd_now_refund" value="0" disabled>
				</td>
			</tr>
			<tr id="opd_now_payment_mode_tr">
				<th>Payment Mode</th>
				<td>
					<select class="span2" id="opd_now_payment_mode" onchange="opd_payment_mode_change(this.value)" onkeyup="opd_payment_mode_up(event)">
				<?php
					$pay_mode_master_qry=mysqli_query($link, "SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='' AND `operation`=1 ORDER BY `sequence` ASC");
					while($pay_mode_master=mysqli_fetch_array($pay_mode_master_qry))
					{
						echo "<option value='$pay_mode_master[p_mode_name]'>$pay_mode_master[p_mode_name]</option>";
					}
				?>
					</select>
					<br>
					<input type="hidden" class="span1" id="opd_now_ref_field">
					<input type="hidden" class="span1" id="opd_now_operation">
				</td>
			</tr>
			<tr class="opd_now_balance_tr" style="display:none;">
				<th>Now Balanace</th>
				<td>
					<span id="opd_now_balance">0</span>
				</td>
			</tr>
			<tr class="" style="display:none;">
				<th>Balance Reason</th>
				<td>
					<input type="text" class="span2" id="now_balance_reason" onkeyup="now_balance_reason(event)">
				</td>
			</tr>
			<tr id="opd_now_cheque_ref_no_tr" style="display:none;">
				<th>Cheque/Reference No</th>
				<td>
					<input type="text" class="span2" id="opd_now_cheque_ref_no" onkeyup="opd_now_cheque_ref_no(event)">
				</td>
			</tr>
			<tr id="save_tr">
				<td colspan="2" style="text-align:center;">
					<button class="btn btn-save" id="pat_save_btn" onClick="save_payment()"><i class="icon-save"></i> Save</button>
				<?php if($pat_typ_text["type"]==1){ ?>
					<button class="btn btn-print" id="print_con_receipt_btn" onClick="print_receipt('pages/print_consulant_receipt_new.php?v=0')"><i class="icon-print"></i> Consultation Receipt</button>
					
					<button class="btn btn-print" id="print_con_bill_btn" onClick="print_receipt('pages/print_consulant_receipt_bill_new.php?v=1')"><i class="icon-print"></i> Bill</button>
				<?php } ?>
				<?php if($pat_typ_text["type"]==2){ ?>
					<button class="btn btn-print" id="print_con_receipt_btn" onClick="print_receipt('pages/cash_memo_lab_new.php?v=0')"><i class="icon-print"></i> Receipt</button>
					
					<button class="btn btn-print" id="print_con_bill_btn" onClick="print_receipt('pages/cash_memo_lab_bill_new.php?v=1')"><i class="icon-print"></i> Bill</button>
				<?php } ?>
				<?php if($pat_typ_text["type"]==3){ ?>
					<button class="btn btn-print" id="print_con_bill_btn" onClick="print_receipt('pages/ipd_bill_type_detail_casual_new.php?v=1')"><i class="icon-print"></i> Bill</button>
				<?php } ?>
					<button class="btn btn-back" onclick="back_page()"><i class="icon-backward"></i> Back</button>
				</td>
			</tr>
		</table>
	</div>
<?php
}

if($_POST["type"]=="payment_mode_change")
{
	//print_r($_POST);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$pay_id=mysqli_real_escape_string($link, $_POST["pay_id"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["payment_mode"]);
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["cheque_ref_no"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$payment_detail_all=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `pay_id`='$pay_id' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
	
	if(mysqli_query($link, " UPDATE `payment_detail_all` SET `payment_mode`='$payment_mode', `cheque_ref_no`='$cheque_ref_no' WHERE `pay_id`='$pay_id' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' "))
	{
		mysqli_query($link, " INSERT INTO `payment_mode_change`(`patient_id`, `ipd_id`, `bill_no`, `pay_mode`, `cheque_ref_no`, `user`, `date`, `time`) VALUES ('$patient_id','$opd_id','$payment_detail_all[transaction_no]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$user','$date','$time') ");
		
		echo "Payment mode changed";
	}
	else
	{
		echo "Failed, try again later.";
	}
}

if($_POST["type"]=="save_payment")
{
	//print_r($_POST);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$p_type_id=mysqli_real_escape_string($link, $_POST["p_type_id"]);
	
	$total=mysqli_real_escape_string($link, $_POST["total"]);
	$discount_now=mysqli_real_escape_string($link, $_POST["now_discount"]);
	$discount_reason=mysqli_real_escape_string($link, $_POST["opd_now_discount_reason"]);
	$now_pay=mysqli_real_escape_string($link, $_POST["opd_now_pay"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["opd_now_payment_mode"]);
	$balance_reason=mysqli_real_escape_string($link, $_POST["now_balance_reason"]);
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["opd_now_cheque_ref_no"]);
	
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	if($now_pay>0 || $discount_now>0)
	{
		$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));
		$bill_name=$pat_typ_text["bill_name"];
		
		if(!$total){ $total=0; }
		if(!$discount_now){ $discount_now=0; }
		if(!$now_pay){ $now_pay=0; }
		
		$refund_amount=0;
		$refund_reason="";
		$tax_amount=0;
		$tax_reason="";
		
		$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		
		$already_paid      =$check_paid["paid"];
		$already_discount  =$check_paid["discount"];
		$already_refund    =$check_paid["refund"];
		$already_tax       =$check_paid["tax"];
		
		$settle_amount=$already_paid+$already_discount+$already_tax-$already_refund;
		
		$payment_type="Balance";
		
		$balance_amount=$total-$settle_amount-$now_pay-$discount_now;
		
		$total_paid=$already_paid+$now_pay;
		$total_discount=$already_discount+$discount_now;
		$total_refund=$already_refund+$refund_amount;
		$total_tax=$already_tax+$tax_amount;
		
		$discount_percetage=round(($total_discount/$total)*100,2);
		
		$bill_no=generate_bill_no_new($bill_name,$p_type_id);
		
		if($pat_typ_text["type"]==1)
		{
			$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
			
			$bill_amount=$inv_pat_pay_detail["tot_amount"];
			$discount_amount=$inv_pat_pay_detail["dis_amt"];
			$paid_amount=$inv_pat_pay_detail["advance"];
			
			$total_paid=$paid_amount+$now_pay;
			$total_discount=$discount_amount+$discount_now;
			
			$discount_percetage=round(($total_discount/$bill_amount)*100,2);
			
			$balance_amount=$bill_amount-$discount_amount-$paid_amount-$now_pay-$discount_now;
			
			if($balance_amount<0)
			{
				echo "Failed, try again later.";
				exit();
			}
			
			if(mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','$now_pay','$discount_now','$discount_reason','$refund_amount','$refund_reason','$tax_amount','$tax_reason','$balance_amount','$balance_reason','$payment_type','$payment_mode','$cheque_ref_no','$user','$date','$time','$p_type_id') "))
			{
				mysqli_query($link, " UPDATE `consult_patient_payment_details` SET `dis_per`='$discount_percetage',`dis_amt`='$total_discount',`advance`='$total_paid',`refund_amount`='$total_refund',`tax_amount`='$total_tax',`balance`='$balance_amount' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
			}
		}
		
		if($pat_typ_text["type"]==2)
		{
			$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
			
			$bill_amount=$inv_pat_pay_detail["tot_amount"];
			$discount_amount=$inv_pat_pay_detail["dis_amt"];
			$paid_amount=$inv_pat_pay_detail["advance"];
			
			$total_paid=$paid_amount+$now_pay;
			$total_discount=$discount_amount+$discount_now;
			
			$discount_percetage=round(($total_discount/$bill_amount)*100,2);
			
			$balance_amount=$bill_amount-$discount_amount-$paid_amount-$now_pay-$discount_now;
			
			if($balance_amount<0)
			{
				echo "Failed, try again later.";
				exit();
			}
			
			if(mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','$now_pay','$discount_now','$discount_reason','$refund_amount','$refund_reason','$tax_amount','$tax_reason','$balance_amount','$balance_reason','$payment_type','$payment_mode','$cheque_ref_no','$user','$date','$time','$p_type_id') "))
			{
				mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `dis_per`='$discount_percetage',`dis_amt`='$total_discount',`advance`='$total_paid',`refund_amount`='$total_refund',`tax_amount`='$total_tax',`balance`='$balance_amount' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
			}
		}
		
		if($pat_typ_text["type"]==3)
		{
			if(mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','$now_pay','$discount_now','$discount_reason','$refund_amount','$refund_reason','$tax_amount','$tax_reason','$balance_amount','$balance_reason','$payment_type','$payment_mode','$cheque_ref_no','$user','$date','$time','$p_type_id') "))
			{
				mysqli_query($link, " UPDATE `ipd_discharge_balance_pat` SET `bal_amount`='$balance_amount' WHERE `patient_id`='$patient_id' and `ipd_id`='$opd_id' ");
			}
		}
		
		echo "Saved";
		
	}
	else
	{
		echo "Nothing to save";
	}
}

if($_POST["type"]=="delete_payment")
{
	//print_r($_POST);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$pay_id=mysqli_real_escape_string($link, $_POST["pay_id"]);
	$del_reason=mysqli_real_escape_string($link, $_POST["del_reason"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
	
	$payment_detail_all=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `pay_id`='$pay_id' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
	
	if($payment_detail_all)
	{
		if($pat_typ_text["type"]==1)
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
			
			$bill_amount=$pat_pay_det["tot_amount"];
			$discount_amount=$pat_pay_det["dis_amt"];
			$paid_amount=$pat_pay_det["advance"];
			$tax_amount=$pat_pay_det["tax_amount"];
			$refund_amount=$pat_pay_det["refund_amount"];
			$balance_amount=$pat_pay_det["balance"];
		}

		if($pat_typ_text["type"]==2)
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
			
			$bill_amount=$pat_pay_det["tot_amount"];
			$discount_amount=$pat_pay_det["dis_amt"];
			$paid_amount=$pat_pay_det["advance"];
			$tax_amount=$pat_pay_det["tax_amount"];
			$refund_amount=$pat_pay_det["refund_amount"];
			$balance_amount=$pat_pay_det["balance"];
		}

		if($pat_typ_text["type"]==3)
		{
			$uhid=$pat_reg["patient_id"];
			$ipd=$pat_reg["opd_id"];
			
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
			$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
			
			$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));
			
			$paid_amount      =$check_paid["paid"];
			$discount_amount  =$check_paid["discount"];
			$refund_amount    =$check_paid["refund"];
			$tax_amount       =$check_paid["tax"];
			
			$settle_amount=$paid_amount+$discount_amount+$tax_amount-$refund_amount;
			
			$balance_amount=$bill_amount-$settle_amount;
		}
		
		$pay_id_amount=$payment_detail_all["amount"];
		$pay_id_discount=$payment_detail_all["discount_amount"];
		$pay_id_tax_amount=$payment_detail_all["tax_amount"];
		
		
		$paid_amount     =$paid_amount-$pay_id_amount;
		$discount_amount =$discount_amount-$pay_id_discount;
		$balance_amount  =$balance_amount+$pay_id_amount+$pay_id_discount+$pay_id_tax_amount;
		
		$discount_percetage=round(($discount_amount/$bill_amount)*100,2);
		
		if($pat_reg["date"]==$date)
		{
			$credit_pay_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' "));
			if($credit_pay_check)
			{
				mysqli_query($link, " UPDATE `payment_detail_all` SET `balance_amount`='$balance_amount' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' ");
			}
			else
			{
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				
				$already_paid      =$check_paid["paid"];
				
				$bill_no=generate_bill_no_new($bill_name,$p_type_id);
				
				$payment_mode="Credit";
				$balance_reason="Payment canceled";
				
				mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$bill_amount','$already_paid','0','0','','0','','0','','$balance_amount','$balance_reason','Advance','$payment_mode','','$user','$date','$time','$p_type_id') ");
			}
		}
		
		if($payment_detail_all)
		{
			mysqli_query($link, " INSERT INTO `payment_detail_all_delete`(`pay_id`, `patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`, `del_reason`, `del_user`, `del_date`, `del_time`) VALUES ('$payment_detail_all[pay_id]','$payment_detail_all[patient_id]','$payment_detail_all[opd_id]','$payment_detail_all[transaction_no]','$payment_detail_all[bill_amount]','$payment_detail_all[already_paid]','$payment_detail_all[amount]','$payment_detail_all[discount_amount]','$payment_detail_all[discount_reason]','$payment_detail_all[refund_amount]','$payment_detail_all[refund_reason]','$payment_detail_all[tax_amount]','$payment_detail_all[tax_reason]','$payment_detail_all[balance_amount]','$payment_detail_all[balance_reason]','$payment_detail_all[payment_type]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$payment_detail_all[user]','$payment_detail_all[date]','$payment_detail_all[time]','$payment_detail_all[encounter]','$del_reason','$user','$date','$time') ");
		}
		
		if($pat_typ_text["type"]==1)
		{
			mysqli_query($link, " UPDATE `consult_patient_payment_details` SET `dis_per`='$discount_percetage',`dis_amt`='$discount_amount',`advance`='$paid_amount',`balance`='$balance_amount' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
		}
		
		if($pat_typ_text["type"]==2)
		{
			mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `dis_per`='$discount_percetage',`dis_amt`='$discount_amount',`advance`='$paid_amount',`balance`='$balance_amount' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
		}
		
		if($pat_typ_text["type"]==3)
		{
			$check_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id' "));
			if($check_entry)
			{
				mysqli_query($link, " UPDATE `ipd_discharge_balance_pat` SET `bal_amount`='$balance_amount' WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id' ");
			}
			else
			{
				mysqli_query($link, " INSERT INTO `ipd_discharge_balance_pat`(`patient_id`, `ipd_id`, `bal_amount`, `user`, `date`, `time`) VALUES ('$patient_id','$opd_id','$balance_amount','$user','$date','$time') ");
			}
		}
		
		
		mysqli_query($link, " DELETE FROM `payment_detail_all` WHERE `pay_id`='$pay_id' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
		
		echo "Deleted";
	}
	else
	{
		echo "Error, try again later.";
	}
}
