<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$date=date("Y-m-d");
$time=date("H:i:s");

$branch_id=$_POST['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

if($_POST["type"]=="load_doctors")
{
	echo "<option value='0'>Select Doctors</option>";
	
	$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` order by `Name` ");
	while($con_doc=mysqli_fetch_array($con_doc_qry))
	{
		echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
	}
}

if($_POST["type"]=="doctor_account")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$consultantdoctorid=$_POST['con_cod_id'];
	$dept_id=$_POST['dept_id'];
	
	$all_docs="0";
	
	$qry=" SELECT DISTINCT a.`consultantdoctorid` FROM `appointment_book` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`branch_id`='$branch_id' ";
	
	if($dept_id>0)
	{
		$qry.=" AND b.`type`='$dept_id' ";
	}
	
	if($consultantdoctorid>0)
	{
		$qry.=" AND a.`consultantdoctorid`='$consultantdoctorid' ";
	}
	
	//echo $qry;
	$doc_qry=mysqli_query($link, $qry );
	while($doc=mysqli_fetch_array($doc_qry))
	{
		$all_docs.=",".$doc["consultantdoctorid"];
	}
	
	$qry=" SELECT DISTINCT a.`doc_id` AS `consultantdoctorid` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`branch_id`='$branch_id' ";
	
	if($dept_id>0)
	{
		$qry.=" AND b.`type`='$dept_id' ";
	}
	
	if($consultantdoctorid>0)
	{
		$qry.=" AND a.`doc_id`='$consultantdoctorid' ";
	}
	
	//echo $qry;
	$doc_qry=mysqli_query($link, $qry );
	while($doc=mysqli_fetch_array($doc_qry))
	{
		$all_docs.=",".$doc["consultantdoctorid"];
	}
	
	//echo $all_docs;
	
	$all_doc_str="SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN($all_docs)";
	
	$all_doc_qry=mysqli_query($link, $all_doc_str);
	
?>	<p style="margin-top: 2%;" id="print_div">
		<b>Accounts from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $consultantdoctorid;?>','<?php echo $dept_id;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print Payment Receipt</button>
	</p>
	<table class="table table-hover" style="background-color:white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<!--<th>UHID</th>-->
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Fees Name</th>
				<th>Amount</th>
				<th>Date</th>
				<!--<th>User</th>-->
			</tr>
		</thead>
<?php
		$i=1;
		$n=1;
		$all_total=0;
		while($all_doc=mysqli_fetch_array($all_doc_qry))
		{
			$pat_reg_num1=mysqli_num_rows(mysqli_query($link, "SELECT * FROM `appointment_book` WHERE `date` between '$date1' and '$date2' and `consultantdoctorid`='$all_doc[consultantdoctorid]'"));
			
			$pat_reg_num2=mysqli_num_rows(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `date` between '$date1' and '$date2' and `doc_id`='$all_doc[consultantdoctorid]'"));
			
			$total_num=$pat_reg_num1+$pat_reg_num2;
			
			if($total_num>0)
			{
				echo "<tr><th colspan='8' style='background-color:#fde0e0;'>$all_doc[Name]</th></tr>";
				
				$encounter_str="SELECT * FROM `patient_type_master` WHERE `p_type_id`>0";
				if($dept_id>0)
				{
					$encounter_str.=" AND `p_type_id`='$dept_id' ";
				}
				
				$each_doctor_total=0;
				$each_doctor_num=0;
				$encounter_qry=mysqli_query($link, $encounter_str);
				while($encounter=mysqli_fetch_array($encounter_qry))
				{
					$encounter_val=$encounter["type"];
					$encounter_type=$encounter["p_type_id"];
					
					if($encounter_val==1) // OPD
					{
						$pat_reg_qry=mysqli_query($link, "SELECT * FROM `appointment_book` WHERE `date` between '$date1' and '$date2' and `consultantdoctorid`='$all_doc[consultantdoctorid]'");
						$pat_reg_num=mysqli_num_rows($pat_reg_qry);
						if($pat_reg_num>0)
						{
							echo "<tr><th colspan='8'><i>$encounter[p_type] Account</i></th></tr>";
						}
						$counter_total=0;
						while($pat_reg=mysqli_fetch_array($pat_reg_qry))
						{
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
							
							$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
							
							$balance=$pat_pay_detail["balance"];
							
							$opd_pat_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_free` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
							if($opd_pat_fee)
							{
								$fees_amount=0;
							}else
							{
								$fees_amount=$pat_pay_detail["visit_fee"];
							}
							
							if($fees_amount>=0)
							{
								$fees_name="Consultation Fees";
								
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
?>
								<tr>
									<td>
										<label name="name">
											<?php echo $n; ?>
								<?php if($consultantdoctorid>0 && $balance==0){
										if($fees_amount>0)
										{
								?>
											<input type="checkbox" class="chk<?php echo $all_doc["consultantdoctorid"]; ?>" onclick="each_chk(this,'<?php echo $all_doc["consultantdoctorid"]; ?>')" value="<?php echo $pat_reg["patient_id"]."##".$pat_reg["opd_id"]."##".$pat_pay_detail["slno"]."##".$all_doc["consultantdoctorid"]."##".$fees_amount; ?>">
								<?php
											$i++;
										}
										else
										{
											$doc_pay=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `payment_settlement_doc` WHERE `rel_slno`='$pat_pay_detail[slno]' AND `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
											if($doc_pay)
											{
												echo '<i class="icon-ok"></i>';
											}
										}
									}
								?>
											
										</label>
									</td>
									<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
									<td><?php echo $pat_reg["opd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $fees_name; ?></td>
									<td><?php echo number_format($fees_amount,2); ?></td>
									<td><?php echo convert_date($pat_reg["date"]); ?></td>
									<!--<td><?php echo $user_name["name"]; ?></td>-->
								</tr>
<?php
								$n++;
								
								$counter_total+=$fees_amount;
								$each_doctor_total+=$fees_amount;
								$all_total+=$fees_amount;
							}
						}
						if($pat_reg_num>0)
						{
							$each_doctor_num+=$pat_reg_num;
	?>
							<tr>
								<td colspan="3"></td>
								<th style="text-align:right;">Total &nbsp;</th>
								<th><?php echo number_format($counter_total,2); ?></th>
								<td></td>
							</tr>
	<?php
						}
					}
					if($encounter_val==3) // IPD Or Other(Casualty, Daycare,....)
					{
						$pat_reg_qry=mysqli_query($link, "SELECT a.* FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`doc_id`='$all_doc[consultantdoctorid]' AND b.`type`='$encounter_type'");
						
						$pat_reg_num=mysqli_num_rows($pat_reg_qry);
						if($pat_reg_num>0)
						{
							echo "<tr><th colspan='8'><i>$encounter[p_type] Account</i></th></tr>";
						}
						$counter_total=0;
						while($pat_reg=mysqli_fetch_array($pat_reg_qry))
						{
							$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
							
							$fees_name=$pat_reg["service_text"];
							$fees_amount=$pat_reg["amount"];
							
							if($fees_amount>=0)
							{
								$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
								
								$show_chk=0;
								$final_pay=mysqli_fetch_array(mysqli_query($link, " SELECT `pay_id` FROM `payment_detail_all` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[ipd_id]' AND `payment_type`='Final' "));
								if($final_pay)
								{
									$balance_pay=mysqli_fetch_array(mysqli_query($link, " SELECT `bal_amount` FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[ipd_id]' "));
									if($balance_pay)
									{
										if($balance_pay["bal_amount"]==0)
										{
											$show_chk++;
										}
									}
									else
									{
										$show_chk++;
									}
								}
	?>
								<tr>
									<td>
										<label name="name">
											<?php echo $n; ?>
								<?php if($consultantdoctorid>0 && $show_chk>0){
										if($fees_amount>0)
										{
								?>
											<input type="checkbox" class="chk<?php echo $all_doc["consultantdoctorid"]; ?>" onclick="each_chk(this,'<?php echo $all_doc["consultantdoctorid"]; ?>')" value="<?php echo $pat_reg["patient_id"]."##".$pat_reg["ipd_id"]."##".$pat_reg["slno"]."##".$all_doc["consultantdoctorid"]."##".$fees_amount; ?>">
								<?php
											$i++;
										}
										else
										{
											$doc_pay=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `payment_settlement_doc` WHERE `rel_slno`='$pat_reg[slno]' AND `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[ipd_id]' "));
											if($doc_pay)
											{
												echo '<i class="icon-ok"></i>';
											}
										}
									}
								?>
											
										</label>
									</td>
									<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
									<td><?php echo $pat_reg["ipd_id"]; ?></td>
									<td><?php echo $pat_info["name"]; ?></td>
									<td><?php echo $fees_name; ?></td>
									<td><?php echo number_format($fees_amount,2); ?></td>
									<td><?php echo convert_date($pat_reg["date"]); ?></td>
									<!--<td><?php echo $user_name["name"]; ?></td>-->
								</tr>
<?php
								$n++;
								
								$counter_total+=$fees_amount;
								$each_doctor_total+=$fees_amount;
								$all_total+=$fees_amount;
							}
						}
						if($pat_reg_num>0)
						{
							$each_doctor_num+=$pat_reg_num;
?>
							<tr>
								<td colspan="3"></td>
								<th style="text-align:right;">Total &nbsp;</th>
								<th><?php echo number_format($counter_total,2); ?></th>
								<td></td>
							</tr>
<?php
						}
					}
				}
				if($each_doctor_num>0 && $i>1)
				{
?>
					<tr>
						<td colspan="3">
						<?php if($consultantdoctorid>0){ ?>
							<button class="btn btn-search btn-mini" id="select_btn<?php echo $all_doc["consultantdoctorid"]; ?>1" onclick="select_all(1,'<?php echo $all_doc["consultantdoctorid"]; ?>')">Select All</button>
							<button class="btn btn-search btn-mini" id="select_btn<?php echo $all_doc["consultantdoctorid"]; ?>2" onclick="select_all(2,'<?php echo $all_doc["consultantdoctorid"]; ?>')" style="display:none;">De-select All</button>
						<?php } ?>
						</td>
						<th style="text-align:right;">Grand Total &nbsp;</th>
						<th><?php echo number_format($each_doctor_total,2); ?></th>
						<td></td>
					</tr>
<?php
				}
				if($consultantdoctorid>0)
				{
?>
					<tr>
						<th></th>
						<th></th>
						<th style="text-align:right;">Amount to pay &nbsp;</th>
						<th id="total_amount_ech_doc<?php echo $all_doc["consultantdoctorid"]; ?>">0.00</th>
						<th></th>
						<th></th>
					</tr>
					<tr>
						<td colspan="3" style="text-align:right;">
							<br>
							<button class="btn btn-save" id="save_btn<?php echo $all_doc["consultantdoctorid"]; ?>" onclick="save('<?php echo $all_doc["consultantdoctorid"]; ?>')"><i class="icon-save"></i> Save</button>
						</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
<?php
				}
			}
		}
		if($n>1 && $consultantdoctorid==0)
		{
?>
			<tr>
				<td colspan="3"></td>
				<th style="text-align:right;">All Total &nbsp;</th>
				<th><?php echo number_format($all_total,2); ?></th>
				<td></td>
			</tr>
<?php
		}
?>
	</table>
<?php
}

if($_POST["type"]=="save")
{
	//print_r($_POST);
	$branch_id=$_POST['branch_id'];
	$consultantdoctorid=$_POST['con_cod_id'];
	$all_fees=$_POST['all_fees'];
	$user=$_POST['user'];
	
	$success=0;
	
	$all_fees=explode("@$@", $all_fees);
	foreach($all_fees AS $all_fee)
	{
		if($all_fee)
		{
			// value="<?php echo $pat_reg["patient_id"]."##".$pat_reg["ipd_id"]."##".$pat_reg["slno"]."##".$all_doc["consultantdoctorid"]."##".$fees_amount;"
			$all_fee=explode("##", $all_fee);
			
			$patient_id=$all_fee[0];
			$opd_id=$all_fee[1];
			$rel_slno=$all_fee[2];
			$consultantdoctorid=$all_fee[3];
			$fees_amount=$all_fee[4];
			
			$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT `type`,`branch_id` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
			if($pat_reg)
			{
				$branch_id=$pat_reg['branch_id'];
				
				$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				
				if($pat_type["type"]==1)
				{
					$group_id=101; // OPD
					$charge_id=1; // Consultation fee
					$charge_name="Consultation fee";
				}
				if($pat_type["type"]==3)
				{
					$pat_serv=mysqli_fetch_array(mysqli_query($link, " SELECT `group_id`,`service_id`,`service_text` FROM `ipd_pat_service_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id' AND `slno`='$rel_slno' "));
					
					$group_id=$pat_serv["group_id"];
					$charge_id=$pat_serv["service_id"];
					$charge_name=$pat_serv["service_text"];
				}
				
				if(mysqli_query($link, " INSERT INTO `payment_settlement_doc`(`rel_slno`, `patient_id`, `opd_id`, `group_id`, `charge_id`, `charge_name`, `amount`, `consultantdoctorid`, `branch_id`, `user`, `date`, `time`) VALUES ('$rel_slno','$patient_id','$opd_id','$group_id','$charge_id','$charge_name','$fees_amount','$consultantdoctorid','$branch_id','$user','$date','$time') "))
				{
					if($pat_type["type"]==1)
					{
						$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `slno`='$rel_slno' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
						
						$bill_amount=$pat_pay_det["tot_amount"]-$fees_amount;
						$visit_fee=$pat_pay_det["visit_fee"]-$fees_amount;
						$advance=$pat_pay_det["advance"]-$fees_amount;
						
						if($advance>=0 && $visit_fee>=0)
						{
							$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
							
							$already_paid      =$check_paid["paid"];
							$already_discount  =$check_paid["discount"];
							$already_refund    =$check_paid["refund"];
							$already_tax       =$check_paid["tax"];
							
							$paid_amount=$bill_amount-$already_discount-$already_tax;
							
							if($paid_amount>=0)
							{
								mysqli_query($link, " UPDATE `consult_patient_payment_details` SET `visit_fee`='$visit_fee',`tot_amount`='$bill_amount',`advance`='$advance' WHERE `slno`='$rel_slno' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
								
								mysqli_query($link, " DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`!='Advance' ");
								
								mysqli_query($link, " DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Advance' AND `payment_mode`='Credit' ");
								
								mysqli_query($link, " UPDATE `payment_detail_all` SET `bill_amount`='$bill_amount',`already_paid`='0',`amount`='$paid_amount',`discount_amount`='$already_discount',`tax_amount`='$already_tax',`balance_amount`='0' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Advance' ");
								
								$success++;
							}
							else
							{
								$success--;
							}
						}
						else
						{
							$success--;
							
							mysqli_query($link, " DELETE FROM `payment_settlement_doc` WHERE `rel_slno`='$rel_slno' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `group_id`='$group_id' AND `charge_id`='$charge_id' AND `consultantdoctorid`='$consultantdoctorid' AND `user`='$user' AND `date`='$date' AND `time`='$time' ");
						}
					}
					if($pat_type["type"]==3)
					{
						$pat_serv=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `ipd_pat_service_details` WHERE `slno`='$rel_slno' AND `patient_id`='$patient_id' AND `ipd_id`='$opd_id' "));
						if($pat_serv)
						{
							$charge_amount=$pat_serv["amount"]-$fees_amount;
							if($charge_amount>=0)
							{
								$pat_serv_sum=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot_bill` FROM `ipd_pat_service_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id' "));
								
								$bill_amount=$pat_serv_sum["tot_bill"]-$fees_amount;
								
								$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
								
								$already_paid      =$check_paid["paid"];
								$already_discount  =$check_paid["discount"];
								$already_refund    =$check_paid["refund"];
								$already_tax       =$check_paid["tax"];
								
								$paid_amount=$bill_amount-$already_discount-$already_tax;
								
								if($paid_amount>=0)
								{
									mysqli_query($link, " UPDATE `ipd_pat_service_details` SET `amount`='$charge_amount' WHERE `slno`='$rel_slno' AND `patient_id`='$patient_id' AND `ipd_id`='$opd_id' ");
									
									mysqli_query($link, " DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`!='Final' ");
									
									mysqli_query($link, " DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Final' AND `payment_mode`='Credit' ");
									
									mysqli_query($link, " UPDATE `payment_detail_all` SET `bill_amount`='$bill_amount',`already_paid`='0',`amount`='$paid_amount',`discount_amount`='$already_discount',`tax_amount`='$already_tax',`balance_amount`='0' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Final' ");
									
									$success++;
								}
								else
								{
									$success--;
									
									mysqli_query($link, " DELETE FROM `payment_settlement_doc` WHERE `rel_slno`='$rel_slno' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `group_id`='$group_id' AND `charge_id`='$charge_id' AND `consultantdoctorid`='$consultantdoctorid' AND `user`='$user' AND `date`='$date' AND `time`='$time' ");
								}
							}
							else
							{
								$success--;
								
								mysqli_query($link, " DELETE FROM `payment_settlement_doc` WHERE `rel_slno`='$rel_slno' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `group_id`='$group_id' AND `charge_id`='$charge_id' AND `consultantdoctorid`='$consultantdoctorid' AND `user`='$user' AND `date`='$date' AND `time`='$time' ");
							}
						}
						else
						{
							$success--;
							
							mysqli_query($link, " DELETE FROM `payment_settlement_doc` WHERE `rel_slno`='$rel_slno' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `group_id`='$group_id' AND `charge_id`='$charge_id' AND `consultantdoctorid`='$consultantdoctorid' AND `user`='$user' AND `date`='$date' AND `time`='$time' ");
						}
					}
				}
				else
				{
					$success--;
				}
			}
		}
	}
	
	if($success>0)
	{
		echo "Saved";
	}
	else
	{
		echo "Failed, try again later.";
	}
}
?>
