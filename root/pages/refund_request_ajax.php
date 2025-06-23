<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$type=$_POST["type"];

$date=date("Y-m-d");
$time=date("H:i:s");


if($type==1)
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	$branch_id=$_POST['branch_id'];
	
	
	$qry=" SELECT a.*, b.`name`,b.`sex`,b.`phone` FROM `uhid_and_opdid` a, `patient_info` b WHERE a.`patient_id`=b.`patient_id` "; // and a.`type` IN(4,5,6,7,9)
	
	if($typ=="name" && strlen($val)>2)
	{
		$qry.=" AND b.`name` like '%$val%' ";
	}
	if($typ=="pin" && strlen($val)>2)
	{
		$qry.=" AND a.`opd_id` like '$val%' ";
	}
	if($typ=="uhid")
	{
		$qry.=" AND b.`patient_id` like '$val' ";
	}
	if($typ=="phone" && strlen($val)>2)
	{
		$qry.=" AND b.`phone` like '$val%' ";
	}
	
	$qry.=" AND a.`type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type` IN(1,2,3)) AND a.`type`!='3' "; // OPD & Investigation
	
	$qry.=" AND a.`branch_id`='$branch_id' ORDER BY a.`slno` DESC LIMIT 100";
	
	//echo $qry;
	
	$qry=mysqli_query($link, $qry);
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No.</th>
			<th>Name</th>
			<th>Sex</th>
			<th>Phone</th>
			<th>Encounter</th>
		</tr>
<?php
	$i=1;
	while($pat_info=mysqli_fetch_array($qry))
	{
		//$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		//$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
		
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_info[type]' "));
		$pat_typ=$pat_typ_text['p_type'];
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $pat_info['patient_id'];?>','<?php echo $pat_info['opd_id'];?>','<?php echo $typ;?>','<?php echo $pat_info["type"];?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $pat_info['patient_id']."@@".$pat_info['opd_id']."@@".$typ."@@".$pat_info["type"];?>"/></td>
			<td><?php echo $pat_info['opd_id'];?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $pat_info['sex'];?></td>
			<td><?php echo $pat_info['phone'];?></td>
			<td><?php echo $pat_typ;?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
<?php
}

if($type=="service_list")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$refund_request_id=$_POST["refund_request_id"];
	$val=$_POST["val"];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
	
	if($val==1 || $val==2)
	{
		if($prefix_det["type"]==1)
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
			
			$bill_amount=$pat_pay_det["tot_amount"];
			$discount_amount=$pat_pay_det["dis_amt"];
		}
		if($prefix_det["type"]==2)
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
			
			$bill_amount=$pat_pay_det["tot_amount"];
			$discount_amount=$pat_pay_det["dis_amt"];
		}
		if($prefix_det["type"]==3)  // Other
		{
			$baby_serv_tot=0;
			$baby_ot_total=0;
			$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$opd_id' ");
			while($delivery_check=mysqli_fetch_array($delivery_qry))
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot+=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total+=$baby_ot_tot_val["g_tot"];
				
			}
			
			$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$opd_id' and `group_id`='141' "));
			$no_of_days=$no_of_days_val["ser_quantity"];
			
			$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id='141' "));
			$tot_serv_amt1=$tot_serv1["tots"];
			//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
			
			$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id!='141' "));
			$tot_serv_amt2=$tot_serv2["tots"];
			
			// OT Charge
			$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$opd_id' "));
			$ot_total=$ot_tot_val["g_tot"];
			
			// Total
			$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
			
			$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			
			$paid_amount       =$check_paid["paid"];
			$discount_amount   =$check_paid["discount"];
			$refund_amount     =$check_paid["refund"];
			
			$pat_bal=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`bal_amount`),0) AS `bal` FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' "));
			
			$balance_amount=$pat_bal["bal"];
		}
		
		$discount_per=($discount_amount*100)/$bill_amount;
		
?>
	<table class="table table-condensed sub_table1">
		<tr>
			<th>Service Name</th>
			<th>Service Amount</th>
			<th>Discount</th>
			<th class="r_type r_type2">Refund Amount</th>
			<th class="r_type r_type2">After Refund</th>
			<th class="r_type r_type1">After Discount</th>
		</tr>
<?php
		if($prefix_det["type"]==1) // OPD
		{
			$group_id=101;
			for($w=1;$w<3;$w++)
			{
				if($w==1)
				{
					$service_id=1;
					$service_name="Registration Fee";
					$service_rate=$pat_pay_det["regd_fee"];
				}
				if($w==2)
				{
					$service_id=2;
					$service_name="Consultation Fee";
					$service_rate=$pat_pay_det["visit_fee"];
				}
				
				$service_discount=round(($service_rate*$discount_per)/100);
				$service_rate_after_discount=$service_rate-$service_discount;
				
				$ref_checked="";
				$ref_element_dis="";
				//$ref_amount=0;
				
				$ref_amount=$service_rate_after_discount;
				
				$ref_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' AND `status`=0 "));
				if($ref_request)
				{
					$ref_element_dis="disabled";
					
					$ref_request_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request_details` WHERE `refund_request_id`='$refund_request_id' AND `service_id`='$service_id' "));
					if($ref_request_det)
					{
						$ref_amount=$ref_request_det["refund_amount"];
						
						$ref_checked="checked";
					}
				}
?>
				<tr>
					<td>
						<label>
							<input type="checkbox" class="refund_select" id="refund_select<?php echo $service_id; ?>" value="<?php echo $service_id; ?>" onchange="refund_check_change(this,'ref_amount_each<?php echo $service_id; ?>','ref_amount_each_fix<?php echo $service_id; ?>')" <?php echo $ref_checked." ".$ref_element_dis." ".$check_dis; ?> > <?php echo $service_name; ?>
						</label>
					</td>
					<td><?php echo $service_rate;?></td>
					<td><?php echo number_format($service_discount,2);?></td>
					<td class="r_type r_type2">
						<input type="text" class="span1 ref_amount_each numericc" id="ref_amount_each<?php echo $service_id; ?>" value="<?php echo $ref_amount; ?>" onkeyup="ref_amount_each_up(this,'ref_amount_each_fix<?php echo $service_id; ?>','<?php echo $service_id; ?>')" readonly>
						
						<input type="hidden" class="span1 ref_amount_each_fix" id="ref_amount_each_fix<?php echo $service_id; ?>" value="<?php echo $service_rate_after_discount;?>">
						<input type="hidden" class="span1 ref_amount_each_actual" id="ref_amount_each_actual<?php echo $service_id; ?>" value="<?php echo $service_rate;?>">
						<input type="hidden" class="span1 group_id" id="group_id<?php echo $service_id; ?>" value="<?php echo $group_id;?>">
					</td>
					<td class="r_type r_type2" id="res_val2<?php echo $service_id; ?>"><?php echo number_format($service_rate_after_discount-$ref_amount,2);?></td>
					<td class="r_type r_type1" id="res_val1<?php echo $service_id; ?>"><?php echo number_format($service_rate_after_discount,2);?></td>
				</tr>
<?php
			}
		}
		if($prefix_det["type"]==2) // Investigation
		{
			$test_qry=mysqli_query($link,"SELECT a.`testid`,a.`testname`,b.`test_rate`,b.`test_discount`,a.`category_id`,a.`type_id`,b.`addon_testid` FROM `testmaster` a, `patient_test_details` b WHERE a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$opd_id' ");
			while($test=mysqli_fetch_array($test_qry))
			{
				if($test["category_id"]==1){ $group_id=104; }
				if($test["category_id"]==2){ $group_id=151; }
				if($test["category_id"]==3){ $group_id=150; }
				if($test["category_id"]==4)
				{
					$dept_info=mysqli_fetch_array(mysqli_query($link, "SELECT `group_id` FROM `test_department` WHERE `category_id`='$test[category_id]' AND `id`='$test[type_id]'"));
					
					$group_id=$dept_info["group_id"];
				}
				
				$service_id=$test["testid"];
				$service_name=$test["testname"];
				$service_rate=$test["test_rate"];
				$service_discount=round($test["test_discount"]);
				
				if($service_discount==0)
				{
					$service_discount=round(($service_rate*$discount_per)/100);
				}
				$service_rate_after_discount=$service_rate-$service_discount;
				
				$ref_checked="";
				$ref_element_dis="";
				$ref_amount=0;
				
				$ref_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' ")); // AND `status`=0
				if($ref_request)
				{
					$ref_element_dis="disabled";
					
					$ref_request_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request_details` WHERE `refund_request_id`='$refund_request_id' AND `service_id`='$service_id' "));
					if($ref_request_det)
					{
						$ref_amount=$ref_request_det["refund_amount"];
						
						$ref_checked="checked";
					}
				}
				
				$addon_cls="";
				if($test["addon_testid"]>0)
				{
					$ref_element_dis="disabled";
					
					$addon_cls="addon_".$test["addon_testid"];
				}
?>
				<tr>
					<td>
						<label>
							<input type="checkbox" class="refund_select <?php echo $addon_cls; ?>" id="refund_select<?php echo $service_id; ?>" value="<?php echo $service_id; ?>" onchange="refund_check_change(this,'ref_amount_each<?php echo $service_id; ?>','ref_amount_each_fix<?php echo $service_id; ?>')" <?php echo $ref_checked." ".$ref_element_dis." ".$check_dis; ?> > <?php echo $service_name; ?>
						</label>
					</td>
					<td><?php echo $service_rate;?></td>
					<td><?php echo number_format($service_discount,2);?></td>
					<td class="r_type r_type2">
						<input type="text" class="span1 ref_amount_each numericc" id="ref_amount_each<?php echo $service_id; ?>" value="<?php echo $ref_amount; ?>" onkeyup="ref_amount_each_up(this,'ref_amount_each_fix<?php echo $service_id; ?>','<?php echo $service_id; ?>')" readonly>
						
						<input type="hidden" class="span1 ref_amount_each_fix" id="ref_amount_each_fix<?php echo $service_id; ?>" value="<?php echo $service_rate_after_discount;?>">
						<input type="hidden" class="span1 ref_amount_each_actual" id="ref_amount_each_actual<?php echo $service_id; ?>" value="<?php echo $service_rate;?>">
						<input type="hidden" class="span1 group_id" id="group_id<?php echo $service_id; ?>" value="<?php echo $group_id;?>">
					</td>
					<td class="r_type r_type2" id="res_val2<?php echo $service_id; ?>"><?php echo number_format($service_rate_after_discount-$ref_amount,2);?></td>
					<td class="r_type r_type1" id="res_val1<?php echo $service_id; ?>"><?php echo number_format($service_rate_after_discount,2);?></td>
				</tr>
<?php
			}
		}
		if($prefix_det["type"]==3) // Other
		{
			$serv_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' ");
			while($service_det=mysqli_fetch_array($serv_qry))
			{
				$group_id=$service_det["group_id"];
				$service_id=$service_det["service_id"];
				$service_name=$service_det["service_text"];
				$service_rate=$service_det["amount"];
				$service_discount=0;
				
				if($service_discount==0)
				{
					$service_discount=round(($service_rate*$discount_per)/100);
				}
				$service_rate_after_discount=$service_rate-$service_discount;
				
				$ref_checked="";
				$ref_element_dis="";
				$ref_amount=0;
				
				$ref_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' ")); // AND `status`=0
				if($ref_request)
				{
					$ref_element_dis="disabled";
					
					$ref_request_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request_details` WHERE `refund_request_id`='$refund_request_id' AND `service_id`='$service_id' "));
					if($ref_request_det)
					{
						$ref_amount=$ref_request_det["refund_amount"];
						
						$ref_checked="checked";
					}
				}
?>
				<tr>
					<td>
						<label>
							<input type="checkbox" class="refund_select" id="refund_select<?php echo $service_id; ?>" value="<?php echo $service_id; ?>" onchange="refund_check_change(this,'ref_amount_each<?php echo $service_id; ?>','ref_amount_each_fix<?php echo $service_id; ?>')" <?php echo $ref_checked." ".$ref_element_dis." ".$check_dis; ?> > <?php echo $service_name; ?>
						</label>
					</td>
					<td><?php echo $service_rate;?></td>
					<td><?php echo number_format($service_discount,2);?></td>
					<td class="r_type r_type2">
						<input type="text" class="span1 ref_amount_each numericc" id="ref_amount_each<?php echo $service_id; ?>" value="<?php echo $ref_amount; ?>" onkeyup="ref_amount_each_up(this,'ref_amount_each_fix<?php echo $service_id; ?>','<?php echo $service_id; ?>')" readonly>
						
						<input type="hidden" class="span1 ref_amount_each_fix" id="ref_amount_each_fix<?php echo $service_id; ?>" value="<?php echo $service_rate_after_discount;?>">
						<input type="hidden" class="span1 ref_amount_each_actual" id="ref_amount_each_actual<?php echo $service_id; ?>" value="<?php echo $service_rate;?>">
						<input type="text" class="span1 group_id" id="group_id<?php echo $service_id; ?>" value="<?php echo $group_id;?>">
					</td>
					<td class="r_type r_type2" id="res_val2<?php echo $service_id; ?>"><?php echo number_format($service_rate_after_discount-$ref_amount,2);?></td>
					<td class="r_type r_type1" id="res_val1<?php echo $service_id; ?>"><?php echo number_format($service_rate_after_discount,2);?></td>
				</tr>
<?php
			}
		}
?>
	</table>
<?php
	}
}

if($type=="save_refund_request")
{
	//~ print_r($_POST);
	//~ exit();
	
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$pateint_type=$_POST["pateint_type"];
	$refund_type=$_POST["refund_type"];
	$sel_services=$_POST["sel_services"];
	$refund_reason=mysqli_real_escape_string($link, $_POST["refund_reason"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["payment_mode"]);
	$user=$_POST["user"];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$branch_id=$pat_reg["branch_id"];
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
	
	if($prefix_det["type"]==1)  // OPD
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
		$bill_amount=$pat_pay_det["tot_amount"];
	}
	if($prefix_det["type"]==2)  // Investgation
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
		$bill_amount=$pat_pay_det["tot_amount"];
	}
	if($prefix_det["type"]==3)  // Other
	{
		$baby_serv_tot=0;
		$baby_ot_total=0;
		$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$opd_id' ");
		while($delivery_check=mysqli_fetch_array($delivery_qry))
		{
			$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			$baby_serv_tot+=$baby_tot_serv["tots"];
			
			// OT Charge Baby
			$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			$baby_ot_total+=$baby_ot_tot_val["g_tot"];
			
		}
		
		$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$opd_id' and `group_id`='141' "));
		$no_of_days=$no_of_days_val["ser_quantity"];
		
		$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id='141' "));
		$tot_serv_amt1=$tot_serv1["tots"];
		//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
		
		$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id!='141' "));
		$tot_serv_amt2=$tot_serv2["tots"];
		
		// OT Charge
		$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$opd_id' "));
		$ot_total=$ot_tot_val["g_tot"];
		
		// Total
		$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
	}
	
	$pateint_type=$prefix_det["type"];
	
	$group_id=0;
	$quantity=1;
	$batch_no=1;
	
	$total_refund_amount=0;
	
	if(mysqli_query($link," INSERT INTO `refund_request`(`patient_id`, `opd_id`, `pateint_type`, `refund_type`, `status`, `refund_reason`, `tot_amount`, `refund_amount`, `payment_mode`, `user`, `date`, `time`, `branch_id`) VALUES ('$uhid','$opd_id','$pateint_type','$refund_type','0','$refund_reason','$bill_amount','0','$payment_mode','$user','$date','$time','$branch_id') "))
	{
		$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `refund_request_id` FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `user`='$user' AND `date`='$date' AND `time`='$time' ORDER BY `refund_request_id` DESC LIMIT 1"));
		
		$refund_request_id=$last_row["refund_request_id"];
		
		$sel_services=explode("@@", $sel_services);
		foreach($sel_services AS $each_service)
		{
			if($each_service)
			{
				$each_service=explode("$$", $each_service);
				
				$group_id=$each_service[0];
				$service_id=$each_service[1];
				$refund_amount=$each_service[2];
				$serv_amount=$each_service[3];
				$ser_rate=$each_service[4];
				
				if($prefix_det["type"]==1)  // OPD
				{
					$group_id=101;
				}
				if($prefix_det["type"]==2)  // Investgation
				{
					$test_info=mysqli_fetch_array(mysqli_query($link," SELECT `category_id`,`type_id` FROM `testmaster` WHERE `testid`='$service_id' "));
					
					if($test_info["category_id"]==1){ $group_id=104; }
					if($test_info["category_id"]==2){ $group_id=151; }
					if($test_info["category_id"]==3){ $group_id=150; }
					if($test_info["category_id"]==4)
					{
						$dept_info=mysqli_fetch_array(mysqli_query($link, "SELECT `group_id` FROM `test_department` WHERE `category_id`='$test_info[category_id]' AND `id`='$test_info[type_id]'"));
						
						$group_id=$dept_info["group_id"];
					}
				}
				if($prefix_det["type"]==3)  // Other
				{
					$charge_info=mysqli_fetch_array(mysqli_query($link," SELECT `group_id` FROM `charge_master` WHERE `charge_id`='$service_id' "));
					
					$group_id=$charge_info["group_id"];
				}
				
				if($prefix_det["type"]==1)
				{
					if($service_id==1)
					{
						$service_name="Registration fee";
					}
					if($service_id==2)
					{
						$service_name="Consultation fee";
					}
				}
				
				if($prefix_det["type"]==2)
				{
					$test_info=mysqli_fetch_array(mysqli_query($link," SELECT `testname` FROM `testmaster` WHERE `testid`='$service_id' "));
					$service_name=$test_info["testname"];
				}
				
				if($prefix_det["type"]==3)
				{
					$service_info=mysqli_fetch_array(mysqli_query($link," SELECT `service_text` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' AND `service_id`='$service_id' "));
					$service_name=$service_info["service_text"];
				}
				
				if($refund_amount<=$serv_amount) //  && $refund_amount!=0
				{
					mysqli_query($link," INSERT INTO `refund_request_details`(`refund_request_id`, `batch_no`, `group_id`, `service_id`, `quantity`, `service_name`, `ser_rate`, `refund_amount`, `serv_amount`) VALUES ('$refund_request_id','$batch_no','$group_id','$service_id','$quantity','$service_name','$ser_rate','$refund_amount','$serv_amount') ");
					
					$total_refund_amount+=$refund_amount;
				}
				
			}
		}
		
		$check_entry=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request_details` WHERE `refund_request_id`='$refund_request_id'"));
		if($check_entry)
		{
			mysqli_query($link," UPDATE `refund_request` SET `refund_amount`='$total_refund_amount' WHERE `refund_request_id`='$refund_request_id' ");
			
			$ref_request_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `status`='0' "));
			if($ref_request_num>1)
			{
				mysqli_query($link," DELETE FROM `refund_request` WHERE `refund_request_id`='$refund_request_id' ");
				mysqli_query($link," DELETE FROM `refund_request_details` WHERE `refund_request_id`='$refund_request_id' ");
				
				echo "Failed, try again later.@@0";
			}
			else
			{
				echo "Refund request is sent.@@".$refund_request_id;
			}
		}
		else
		{
			echo "Failed, try again later..@@0";
			
			mysqli_query($link," DELETE FROM `refund_request` WHERE `refund_request_id`='$refund_request_id' ");
		}
	}
	else
	{
		echo "Failed, try again later..@@0";
	}
}

if($type=="load_ref_requested_data")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$refund_request_id=$_POST["refund_request_id"];
	
	$ref_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' AND `status`=0 "));
	
	$user_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$ref_request[user]'"));
?>
	<p>
		Refund request is sent by <b><?php echo $user_info["name"]; ?></b> on <?php echo date("d-M-Y", strtotime($ref_request["date"])); ?> <?php echo date("h:i A", strtotime($ref_request["time"])); ?>
		<br>
		Status : <?php if($ref_request["status"]==0){ echo "Not Approved"; }else{ echo "Approved"; } ?>
	</p>
<?php if($ref_request["status"]==0){ ?>
	<center>
		<button class="btn btn-delete" id="cancel_refund_btn" onclick="cancel_refund_request()"><i class="icon-remove"></i> Cancel Request</button>
		<button class="btn btn-print" id="print_refund_btn" onclick="print_refund_request('<?php echo $refund_request_id; ?>')"><i class="icon-print"></i> Print Request</button>
	</center>
<?php
	}
}
if($type=="old_refund_request")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	
	$ref_request_qry=mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `status`!=0 ");
	while($ref_request=mysqli_fetch_array($ref_request_qry))
	{
		$user_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$ref_request[user]'"));
?>
		<p>
			Refund request by <b><?php echo $user_info["name"]; ?></b> on <?php echo date("d-M-Y", strtotime($ref_request["date"])); ?> <?php echo date("h:i A", strtotime($ref_request["time"])); ?>
			<br>
			Status : <?php if($ref_request["status"]==0){ echo "Pending"; }else if($ref_request["status"]==1){ echo "<b style='color:green;'>Approved</b>"; }else if($ref_request["status"]==2){
				
				$reject_refund=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_refund_reject` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$ref_request[refund_request_id]' "));
				
				echo "<b style='color:red;'>Rejected</b>"."<br>Reason : ".$reject_refund["reason"];
			}
			?>
			<br>
			<button class="btn btn-print btn-mini" id="opd_print_refund_btn" onclick="print_refund_request('<?php echo $ref_request["refund_request_id"]; ?>')"><i class="icon-print"></i> Print Request</button>
		</p>
<?php
	}
}
if($type=="cancel_refund_request")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$refund_request_id=$_POST["refund_request_id"];
	
	$ref_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' AND `status`=0 "));
	if($ref_request)
	{
		mysqli_query($link," DELETE FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' AND `status`=0 ");
		
		mysqli_query($link," DELETE FROM `refund_request_details` WHERE `refund_request_id`='$refund_request_id' ");
		
		echo "Refund request has been cancelled.@@1";
	}
	else
	{
		echo "Error. Try again later.@@0";
	}
}
if($type=="refund_requested_pat_list")
{
	$val=mysqli_real_escape_string($link, $_POST["val"]);
	$list_start=$_POST["list_start"];
	$branch_id=$_POST["branch_id"];
	
	$str="SELECT * FROM `refund_request` WHERE `refund_request_id`>0";
	
	if(strlen($val))
	{
		$str.=" AND (`patient_id` LIKE '$val%' OR `opd_id` LIKE '$val%' OR `refund_reason` LIKE '$val%' OR `date` LIKE '$val%' OR `patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `name` LIKE '%$val%'))";
	}
	
	$str.=" AND `branch_id`='$branch_id' ORDER BY `refund_request_id` DESC limit ".$list_start;
	
	$qry=mysqli_query($link, $str);
?>
	<table class="table table-bordered text-center table-bg-white">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Age-Sex</th>
				<th>Refund Type</th>
				<th>Reason</th>
				<th>Request By</th>
				<th>Request Time</th>
				<th><i class="icon-cogs"></i></th>
			</tr>
		</thead>
<?php
	$n=1;
	while($data=mysqli_fetch_array($qry))
	{
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT name FROM `employee` WHERE `emp_id`='$data[user]' "));
		
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]' "));
		
		if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
		
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $data["patient_id"]; ?></td>
			<td><?php echo $data["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age." - ".$pat_info["sex"]; ?></td>
			<td>
			<?php
				if($data["refund_type"]==1){ echo "Service Refund"; }else if($data["refund_type"]==2){ echo "Payment Refund"; }
			?>
			</td>
			<td><?php echo $data["refund_reason"]; ?></td>
			<td><?php echo $user_info["name"]; ?></td>
			<td><?php echo date("d-M-Y", strtotime($data["date"]))." ".date("h:i A", strtotime($data["time"])); ?></td>
			<td>
			<?php
				if($data["status"]==0)
				{
			?>
				<button class="btn btn-process btn-mini" onclick="refund_request_process('<?php echo $data["patient_id"]; ?>','<?php echo $data["opd_id"]; ?>','<?php echo $data["refund_request_id"]; ?>')"><i class="icon-forward"></i> Approve</button>
			<?php
				}
				else if($data["status"]==1)
				{
			?>
				<button class="btn btn-search btn-mini" onclick="refund_request_process('<?php echo $data["patient_id"]; ?>','<?php echo $data["opd_id"]; ?>','<?php echo $data["refund_request_id"]; ?>')"><i class="icon-ok"></i> Approved</button>
			<?php
				}
				else
				{
			?>
				<button class="btn btn-close btn-mini" onclick="refund_request_process('<?php echo $data["patient_id"]; ?>','<?php echo $data["opd_id"]; ?>','<?php echo $data["refund_request_id"]; ?>')"><i class="icon-remove"></i> Rejected</button>
			<?php
				}
			?>
			</td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}
if($type=="load_requested_details")
{
	//print_r($_POST);
	
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$refund_request_id=$_POST["refund_request_id"];
	
	$ref_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' "));
	
	$user_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$ref_request[user]'"));
?>
	<p>
		Refund request is sent by <b><?php echo $user_info["name"]; ?></b> on <?php echo date("d-M-Y", strtotime($ref_request["date"])); ?> <?php echo date("h:i A", strtotime($ref_request["time"])); ?>
		<br>
		Status : <?php if($ref_request["status"]==0){ echo "Not Approved"; }else if($ref_request["status"]==1){ echo "<b style='color:green;'>Approved</b>"; }else{ echo "<b style='color:red;'>Rejected</b>"; } ?>
	</p>
<?php if($ref_request["status"]==0){ ?>
	<center>
		<button class="btn btn-save" id="approve_refund_btn" onclick="approve_refund_request()"><i class="icon-save"></i> Approve</button>
		<button class="btn btn-delete" id="reject_refund_btn" onclick="reject_refund_request()"><i class="icon-remove"></i> Reject</button>
	</center>
<?php
	}
	else
	{
?>
	<p>
<?php
		if($ref_request["status"]==1)
		{
			$pat_refund=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' "));
		
			$user_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$pat_refund[user]'"));
			
			echo "By ".$user_info["name"]." on ".date("d-M-Y", strtotime($pat_refund["date"]))." ".date("h: A", strtotime($pat_refund["time"]));
		}
		else if($ref_request["status"]==2)
		{
			$reject_refund=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_refund_reject` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' "));
		
			$user_info=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$reject_refund[user]'"));
			
			echo "By ".$user_info["name"]." on ".date("d-M-Y", strtotime($reject_refund["date"]))." ".date("h: A", strtotime($reject_refund["time"]))."<br>Reason : ".$reject_refund["reason"];
		}
?>
	</p>
	<center>
		<button class="btn btn-print" id="print_refund_btn" onclick="print_refund_receipt('<?php echo $refund_request_id; ?>')"><i class="icon-print"></i> Print Refund</button>
	</center>
<?php
	}
}

if($type=="approve_refund_request")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$refund_request_id=$_POST["refund_request_id"];
	$refund_type=$_POST["refund_type"];
	$sel_services=$_POST["sel_services"];
	$refund_reason=mysqli_real_escape_string($link, $_POST["refund_reason"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["payment_mode"]);
	$user=$_POST["user"];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	
	$pat_pay_num=mysqli_num_rows(mysqli_query($link, " SELECT `pay_id` FROM `ipd_pat_daily_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' AND `pay_id`>0 "));
	
	if(!$payment_mode)
	{
		$payment_mode="Cash";
	}
	
	$quantity=1;
	$batch_no=1;
	$group_id=0;
	
	$p_type_id=$pateint_type=$pat_reg["type"];
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));
	$bill_name=$prefix_det["bill_name"];
	
	if($prefix_det["type"]==1)
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
		
		$pat_visit_fee=$pat_pay_det["visit_fee"];
		$pat_regd_fee=$pat_pay_det["regd_fee"];
		$bill_amount_old=$bill_amount=$pat_pay_det["tot_amount"];
		
		$balance_amount_old=$pat_pay_det["balance"];
	}
	if($prefix_det["type"]==2)
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
		
		$bill_amount_old=$bill_amount=$pat_pay_det["tot_amount"];
		
		$balance_amount_old=$pat_pay_det["balance"];
	}
	if($prefix_det["type"]==3)  // Other
	{
		$baby_serv_tot=0;
		$baby_ot_total=0;
		$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$opd_id' ");
		while($delivery_check=mysqli_fetch_array($delivery_qry))
		{
			$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			$baby_serv_tot+=$baby_tot_serv["tots"];
			
			// OT Charge Baby
			$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			$baby_ot_total+=$baby_ot_tot_val["g_tot"];
			
		}
		
		$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$opd_id' and `group_id`='141' "));
		$no_of_days=$no_of_days_val["ser_quantity"];
		
		$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id='141' "));
		$tot_serv_amt1=$tot_serv1["tots"];
		//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
		
		$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id!='141' "));
		$tot_serv_amt2=$tot_serv2["tots"];
		
		// OT Charge
		$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$opd_id' "));
		$ot_total=$ot_tot_val["g_tot"];
		
		// Total
		$bill_amount_old=$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
		
		$pat_balance=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' "));
		
		$balance_amount_old=$pat_balance["bal_amount"];
	}
	
	$ref_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' "));
	if($ref_request)
	{
		$refund_user=$ref_request["user"];
		
		$tot_amount=$ref_request["tot_amount"];
		$refund_reason=mysqli_real_escape_string($link, $ref_request["refund_reason"]);
		
		if(mysqli_query($link," INSERT INTO `patient_refund`(`patient_id`, `opd_id`, `tot_amount`, `refund_amount`, `payment_mode`, `reason`, `date`, `time`, `user`, `refund_request_id`, `pay_id`) VALUES ('$uhid','$opd_id','$tot_amount','0','$payment_mode','$refund_reason','$date','$time','$user','$refund_request_id','0') "))
		{
			$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `refund_id` FROM `patient_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `user`='$user' AND `date`='$date' AND `time`='$time' ORDER BY `refund_id` DESC LIMIT 1"));
			$refund_id=$last_row["refund_id"];
			
			$total_refund_bill_amount=0;
			$total_refund_amount=0;
			$total_refund_discount=0;
			$ref_request_det_qry=mysqli_query($link,"SELECT * FROM `refund_request_details` WHERE `refund_request_id`='$refund_request_id' ");
			while($ref_request_det=mysqli_fetch_array($ref_request_det_qry))
			{
				$group_id   =$ref_request_det["group_id"];
				$batch_no   =$ref_request_det["batch_no"];
				$service_id =$ref_request_det["service_id"];
				
				$service_name=mysqli_real_escape_string($link, $ref_request_det["service_name"]);
				
				$refund_discount=$ref_request_det["ser_rate"]-$ref_request_det["refund_amount"];
				
				if(mysqli_query($link," INSERT INTO `patient_refund_details`(`refund_id`, `batch_no`, `group_id`, `service_id`, `quantity`, `service_name`, `ser_rate`, `refund_amount`, `serv_amount`, `refund_discount`) VALUES ('$refund_id','$batch_no','$group_id','$service_id','$ref_request_det[quantity]','$service_name','$ref_request_det[ser_rate]','$ref_request_det[refund_amount]','$ref_request_det[serv_amount]','$refund_discount') "))
				{
					$total_refund_bill_amount+=$ref_request_det["ser_rate"];
					$total_refund_amount+=$ref_request_det["refund_amount"];
					$total_refund_discount+=$refund_discount;
					
					if($ref_request["refund_type"]==1) // Service(Delete)
					{
						if($prefix_det["type"]==2)
						{
							$test_qry=mysqli_query($link, "  SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `ipd_id`='$opd_id') AND `batch_no`='$batch_no' AND `testid`='$service_id' ");
							while($test_val=mysqli_fetch_array($test_qry))
							{
								mysqli_query($link, "  INSERT INTO `patient_test_details_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$test_val[patient_id]','$test_val[opd_id]','$test_val[ipd_id]','$test_val[batch_no]','$test_val[testid]','$test_val[sample_id]','$test_val[test_rate]','$test_val[test_discount]','$test_val[date]','$test_val[time]','$test_val[user]','$test_val[type]') ");
							}
							
							mysqli_query($link," DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `ipd_id`='$opd_id') AND `batch_no`='$batch_no' AND `testid`='$service_id' ");
							$testid=$service_id;
							$process_type=4;
							include("test_count_increase.php");
						}
						if($prefix_det["type"]==3)
						{
							$test_qry=mysqli_query($link, "  SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' AND `group_id`='$group_id' AND `service_id`='$service_id' ");
							while($test_val=mysqli_fetch_array($test_qry))
							{
								mysqli_query($link,"INSERT INTO `ipd_pat_service_delete`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$test_val[patient_id]','$test_val[ipd_id]','$test_val[group_id]','$test_val[service_id]','$test_val[service_text]','$test_val[ser_quantity]','$test_val[rate]','$test_val[amount]','$test_val[days]','$test_val[user]','$test_val[time]','$test_val[date]','$test_val[bed_id]')");
							}
							
							mysqli_query($link," DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' AND `group_id`='$group_id' AND `service_id`='$service_id' ");
							
							if($group_id=="104" || $group_id=="150" || $group_id=="151")
							{
								$test_qry=mysqli_query($link, "  SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `ipd_id`='$opd_id') AND `batch_no`='$batch_no' AND `testid`='$service_id' ");
								while($test_val=mysqli_fetch_array($test_qry))
								{
									mysqli_query($link, "  INSERT INTO `patient_test_details_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$test_val[patient_id]','$test_val[opd_id]','$test_val[ipd_id]','$test_val[batch_no]','$test_val[testid]','$test_val[sample_id]','$test_val[test_rate]','$test_val[test_discount]','$test_val[date]','$test_val[time]','$test_val[user]','$test_val[type]') ");
								}
								
								mysqli_query($link," DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `ipd_id`='$opd_id') AND `batch_no`='$batch_no' AND `testid`='$service_id' ");
							}
						}
					}
					if($ref_request["refund_type"]==2) // Payment(Update)
					{
						$service_rate=$ref_request_det["ser_rate"];
						$service_rate-=$ref_request_det["refund_amount"];
						
						if($prefix_det["type"]==1) // OPD
						{
							if($ref_request_det["service_id"]==1) // Regd Fee
							{
								//$pat_regd_fee-=$ref_request_det["refund_amount"];
								mysqli_query($link," UPDATE `consult_patient_payment_details` SET `regd_fee`='$service_rate' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
							}
							if($ref_request_det["service_id"]==2) // Visit Fee
							{
								//$pat_visit_fee-=$ref_request_det["refund_amount"];
								mysqli_query($link," UPDATE `consult_patient_payment_details` SET `visit_fee`='$service_rate' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
								
								mysqli_query($link," UPDATE `appointment_book` SET `visit_type_id`='0' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
							}
						}
						if($prefix_det["type"]==2)
						{
							mysqli_query($link," UPDATE `patient_test_details` SET `test_rate`='$service_rate' WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `ipd_id`='$opd_id') AND `batch_no`='$batch_no' AND `testid`='$service_id' ");
						}
						if($prefix_det["type"]==3)
						{
							mysqli_query($link," UPDATE `ipd_pat_service_details` SET `ser_quantity`='1',`rate`='$service_rate',`amount`='$service_rate' WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' AND `group_id`='$group_id' AND `service_id`='$service_id' ");
							
							if($group_id=="104" || $group_id=="150" || $group_id=="151")
							{
								mysqli_query($link," UPDATE `patient_test_details` SET `test_rate`='$service_rate' WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `ipd_id`='$opd_id') AND `batch_no`='$batch_no' AND `testid`='$service_id' ");
							}
						}
					}
				}
			}
			
			$check_entry=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_refund_details` WHERE `refund_id`='$refund_id'"));
			if($check_entry)
			{
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				
				$already_paid      =$check_paid["paid"];
				$already_discount  =$check_paid["discount"];
				$already_refund    =$check_paid["refund"];
				$already_tax       =$check_paid["tax"];
				
				$advance_paid=$already_paid-$already_refund;
				
				// Discount Refund
				$pat_refund_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`ser_rate`),0) AS `tot_rate`, ifnull(SUM(a.`refund_amount`),0) AS `tot_refund`, ifnull(SUM(a.`refund_discount`),0) AS `tot_dis` FROM `patient_refund_details` a, `patient_refund` b WHERE a.`refund_id`=b.`refund_id` AND b.`patient_id`='$uhid' and b.`opd_id`='$opd_id' AND b.`refund_request_id` IN(SELECT `refund_request_id` FROM `refund_request` WHERE `refund_request_id`='$refund_request_id') ")); // ='$refund_request_id' ,, `refund_type`=1 AND 
				
				$service_refund_amount=$pat_refund_det["tot_rate"];
				$refund_amount=$pat_refund_det["tot_refund"];
				$discount_refund=$pat_refund_det["tot_dis"];
				
				$bill_amount=$bill_amount_old-$service_refund_amount; // final
				$discount_amount=$already_discount-$discount_refund; // final
				
				$discount_per=round(($discount_amount*100)/$bill_amount,2);
				
				$discount_now=$discount_refund*(-1);
				
				$balance_amount=$balance_amount_old-$refund_amount; // final
				
				$refund_now=0;
				$now_pay=0;
				$tax_amount=0;
				$tax_reason="";
				
				if($balance_amount<0)
				{
					$refund_now=abs($balance_amount);
					$balance_amount=0;
					
					$discount_reason="";
					if($discount_now>0)
					{
						$discount_reason="Bill amount has been reduced";
					}
					//$refund_reason="Bill amount has been reduced";
				}
				
				$advance_paid=$advance_paid-$refund_now; // final
				
				$total_refund=$already_refund-$refund_now;
				
				$payment_type="Refund";
				
				if($prefix_det["type"]==1)
				{
					//$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
					
					//$bill_amount=$pat_pay_det["visit_fee"]+$pat_pay_det["regd_fee"]+$pat_pay_det["emergency_fee"];
					
					mysqli_query($link," UPDATE `consult_patient_payment_details` SET `tot_amount`='$bill_amount',`dis_per`='$discount_per',`dis_amt`='$discount_amount',`advance`='$advance_paid',`refund_amount`='$total_refund',`balance`='$balance_amount' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
				}
				if($prefix_det["type"]==2)
				{
					//$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
					
					mysqli_query($link," UPDATE `invest_patient_payment_details` SET `tot_amount`='$bill_amount',`dis_per`='$discount_per',`dis_amt`='$discount_amount',`advance`='$advance_paid',`refund_amount`='$total_refund',`balance`='$balance_amount' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
				}
				if($prefix_det["type"]==3)
				{
					//$balance_amount=$bill_amount-$advance_paid-$discount_amount;
					
					mysqli_query($link," UPDATE `ipd_discharge_balance_pat` SET `bal_amount`='$balance_amount' WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' ");
				}
				
				if($balance_amount>0)
				{
					if($pat_reg["date"]==$date)
					{
						mysqli_query($link, " UPDATE `payment_detail_all` SET `balance_amount`='$balance_amount' WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' ");
					}
					else
					{
						$bill_no=generate_bill_no_new($bill_name,$p_type_id);
						
						$payment_mode="Credit";
						
						$balance_reason_val=mysqli_fetch_array(mysqli_query($link, " SELECT `balance_reason` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `balance_reason`!='' "));
						$balance_reason=mysqli_real_escape_string($link, $balance_reason_val["balance_reason"]);
						
						//mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$uhid','$opd_id','$bill_no','$bill_amount','$already_paid','0','0','','0','','0','','$balance_amount','$balance_reason','Advance','$payment_mode','','$refund_user','$date','$time','$p_type_id') ");
					}
				}
				
				$bill_no=generate_bill_no_new($bill_name,$p_type_id);
				
				mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$uhid','$opd_id','$bill_no','$bill_amount','$already_paid','$now_pay','$discount_now','$discount_reason','$refund_now','$refund_reason','$tax_amount','$tax_reason','$balance_amount','','$payment_type','$payment_mode','','$refund_user','$date','$time','$p_type_id') ");
				
				$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `pay_id` FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `transaction_no`='$bill_no' AND `user`='$refund_user' AND `date`='$date' ORDER BY `pay_id` DESC LIMIT 1"));
				$pay_id=$last_row["pay_id"];
				
				mysqli_query($link," UPDATE `patient_refund` SET `refund_amount`='$total_refund_amount',`pay_id`='$pay_id' WHERE `refund_id`='$refund_id' ");
				
				mysqli_query($link," UPDATE `refund_request` SET `status`='1' WHERE `refund_request_id`='$refund_request_id' ");
				
				// Add Cancel/Refund to Headwise Collection
				if($pat_pay_num>0)
				{
					if($prefix_det["type"]==1 || $prefix_det["type"]==2 || $prefix_det["type"]==3)
					{
						$ref_request_det_qry=mysqli_query($link,"SELECT * FROM `refund_request_details` WHERE `refund_request_id`='$refund_request_id' ");
						while($ref_request_det=mysqli_fetch_array($ref_request_det_qry))
						{
							$group_id   =$ref_request_det["group_id"];
							$service_id =$ref_request_det["service_id"];
							
							if($group_id==104 || $group_id==150 || $group_id==151) //PCR
							{
								$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `category_id`,`type_id` FROM `testmaster` WHERE `testid`='$service_id'"));
								$dept_info=mysqli_fetch_array(mysqli_query($link, "SELECT `group_id`,`service_id` FROM `test_department` WHERE `category_id`='$test_info[category_id]' AND `id`='$test_info[type_id]'"));
								
								$group_id=$dept_info["group_id"];
								$service_id=$dept_info["service_id"];
							}
							
							if($group_id==101) // OPD
							{
								$service_id=553;
							}
							
							if($group_id==186) // CASUALTY PROCEDURE or EMERGENCY
							{
								$service_id=562;
							}
							
							$service_amount =$ref_request_det["ser_rate"];
							$refund_amount =$ref_request_det["refund_amount"];
							
							mysqli_query($link, "INSERT INTO `ipd_pat_daily_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_amount`, `pay_amount`, `refund_amount`, `balance_amount`, `pay_id`, `break_no`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$group_id','$service_id','$service_amount','0','$refund_amount','0','$pay_id','0','$refund_user','$date','$time')");
						}
					}
				}
				
				echo "Refund request has been approved.@@".$refund_id;
			}
			else
			{
				echo "Failed, try again later...@@0";
				
				mysqli_query($link," DELETE FROM `patient_refund` WHERE `request_id`='$request_id' ");
			}
		}
		else
		{
			echo "Failed, try again later..@@0";
		}
	}
	else
	{
		echo "Failed, try again later..@@0";
	}
}

if($type=="reject_refund_request")
{
	//print_r($_POST);
	
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$refund_request_id=$_POST["refund_request_id"];
	$reason=mysqli_real_escape_string($link, $_POST["reason"]);
	$user=$_POST["user"];
	
	if(mysqli_query($link," INSERT INTO `patient_refund_reject`(`patient_id`, `opd_id`, `reason`, `date`, `time`, `user`, `refund_request_id`) VALUES ('$uhid','$opd_id','$reason','$date','$time','$user','$refund_request_id') "))
	{
		mysqli_query($link," UPDATE `refund_request` SET `status`='2' WHERE `refund_request_id`='$refund_request_id' ");
		
		echo "Rejected";
	}
	else
	{
		echo "Failed";
	}
}

?>
