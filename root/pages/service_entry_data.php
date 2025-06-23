<?php
session_start();
include("../../includes/connection.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

$date=date("Y-m-d");

$system_id=99;

if($_POST["type"]=="add_auto_chargeXXXX")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$centreno=$pat_reg["center_no"];
	
	$pat_doc_det=mysqli_fetch_array(mysqli_query($link, " SELECT `attend_doc`, `admit_doc`, `dept_id` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
	
	// Auto Charge (Bed & Doctor Visit Charge)
	$discharge_request=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if(!$discharge_request)
	{
		$pay_final=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' AND `payment_type`='Final'"));
		if(!$pay_final)
		{
			$pat_bed_alloc1_qry=mysqli_query($link,"SELECT *  FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1' ORDER BY `slno` ASC");
			while($pat_bed_alloc1=mysqli_fetch_array($pat_bed_alloc1_qry))
			{
				$ward_id=$pat_bed_alloc1["ward_id"];
				$bed_id=$pat_bed_alloc1["bed_id"];
				
				$bed_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `bed_master` WHERE `bed_id`='$bed_id' AND `ward_id`='$ward_id'"));
				if($bed_info && $ward_id>0)
				{
					$start_date="";
					$end_date="";
					
					$pat_bed_alloc_qry=mysqli_query($link,"SELECT *  FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `bed_id`='$bed_id' AND `slno`>='$pat_bed_alloc1[slno]' ORDER BY `slno` ASC LIMIT 2");
					while($pat_bed_alloc=mysqli_fetch_array($pat_bed_alloc_qry))
					{
						if($pat_bed_alloc['alloc_type']==1)
						{
							$start_date=$pat_bed_alloc["date"];
						}
						else if($pat_bed_alloc['alloc_type']==0)
						{
							$end_date=$pat_bed_alloc["date"];
						}
					}
					if($end_date=="")
					{
						$end_date=date("Y-m-d");
					}
					
					$first_date  = new DateTime($start_date);
					$last_date   = new DateTime($end_date);

					$day_diff = $last_date->diff($first_date)->format("%a");

					for($d=0;$d<$day_diff;$d++)
					{
						$service_date=date("Y-m-d", strtotime("+".$d." days", strtotime($start_date)));
						
						//echo $service_date."<br>";
						
						// Bed Charge
						if($ward_id!=0)
						{
							$charge_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `charge_master` WHERE `charge_id`='$bed_info[charge_id]'"));
							
							$chk_serv=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$charge_info[group_id]' AND `service_id`='$charge_info[charge_id]' AND `date`='$service_date'"));
							
							if($chk_serv==0)
							{
								$service_amount=$charge_info["amount"];
								
								$center_rate=mysqli_fetch_array(mysqli_query($link,"SELECT `rate` FROM `service_rate` WHERE `centreno`='$centreno' AND `charge_id`='$charge_info[charge_id]'"));
								if($center_rate)
								{
									$service_amount=$center_rate["rate"];
								}
								
								if($service_amount>0)
								{
									mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$uhid','$ipd','$charge_info[group_id]','$charge_info[charge_id]','$charge_info[charge_name]','1','$service_amount','$service_amount','1','$system_id','$time','$service_date','$bed_id','0','0',NULL)");
								}
								
								// Bed Charge Plus
								$other_charge_qry=mysqli_query($link,"select * from bed_other_charge where bed_id='$bed_info[bed_id]'");
								while($other_charge=mysqli_fetch_array($other_charge_qry))
								{
									$sub_charge_info=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where charge_info='$other_charge[charge_id]' AND `main_charge_id`>0"));
									if($sub_charge_info)
									{
										$service_amount=$sub_charge_info["amount"];
										
										$center_rate=mysqli_fetch_array(mysqli_query($link,"SELECT `rate` FROM `service_rate` WHERE `centreno`='$centreno' AND `charge_id`='$sub_charge_info[charge_id]'"));
										if($center_rate)
										{
											$service_amount=$center_rate["rate"];
										}
										
										if($service_amount>0)
										{
											mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$uhid','$ipd','$sub_charge_info[group_id]','$other_charge[charge_id]','$sub_charge_info[charge_name]','1','$service_amount','$service_amount','1','$system_id','$time','$service_date','$bed_id','0','0',NULL)");
										}
									}
								}
								
							}
						}
						// Doctor Visit Charge
						$group_id=142;
						
						$charge_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `charge_master` WHERE `charge_id`='$charge_id' AND `group_id`='$group_id'"));
						$charge_info["amount"]=0;
						if($charge_info["amount"]>0)
						{
							$service_amount=$charge_info["amount"];
							
							$center_rate=mysqli_fetch_array(mysqli_query($link,"SELECT `rate` FROM `service_rate` WHERE `centreno`='$centreno' AND `charge_id`='$charge_info[charge_id]'"));
							if($center_rate)
							{
								$service_amount=$center_rate["rate"];
							}
							
							if($service_amount>0)
							{
								$chk_serv=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$charge_info[group_id]' AND `service_id`='$charge_info[charge_id]' AND `date`='$service_date'"));
								
								if($chk_serv==0)
								{
									mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$uhid','$ipd','$charge_info[group_id]','$charge_info[charge_id]','$charge_info[charge_name]','1','$service_amount','$service_amount','1','$system_id','$time','$service_date','$bed_id','$pat_doc_det[attend_doc]','0',NULL)");
								}
							}
						}
					}
				}
			}
		}
	}
}
if($_POST["type"]=="load_service_entry_form")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	
	$pay_final=mysqli_fetch_array(mysqli_query($link, "SELECT `pay_id` FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
?>
	<div class="row">
		<div class="span4" style="width: 25%;">
			<table class="table table-condensed table-bordered">
				<tr>
					<td>
						<span class="side_name"> &nbsp;&nbsp;&nbsp;&nbsp;Group</span>
						<select id="entry_group_id" style="margin-left: 73px;width: 80%;" onchange="entry_group_change()">
							<option value="0">Select</option>
					<?php
						$qry=mysqli_query($link, "SELECT `group_id`, `group_name` FROM `charge_group_master` WHERE `group_id` NOT IN(101,104,150,151) AND `group_id` IN(SELECT DISTINCT `group_id` FROM `charge_master` WHERE  `branch_id`='$branch_id') ORDER BY `group_name` ASC");
						while($data=mysqli_fetch_array($qry))
						{
							echo "<option value='$data[group_id]'>$data[group_name]</option>";
						}
					?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<span class="side_name"> &nbsp;&nbsp;Service</span>
						<select id="entry_service_id" style="margin-left: 73px;width: 80%;" onchange="entry_service_change(0)">
							<option>Select</option>
						</select>
					</td>
				</tr>
			</table>
			<div id="load_service_entry_fields"></div>
		</div>
		<div class="span7" style="width: 68%;max-height: 400px;overflow-y: scroll;">
			<div id="load_selected_services"></div>
		</div>
	</div>
<?php
}
if($_POST["type"]=="load_service_list")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	$group_id=$_POST['group_id'];
	
	echo "<option value='0'>Select</option>";
	
	$qry=mysqli_query($link, "SELECT `charge_id`,`charge_name` FROM `charge_master` WHERE `group_id`='$group_id' AND `branch_id`='$branch_id' ORDER BY `charge_name` ASC");
	while($data=mysqli_fetch_array($qry))
	{
		echo "<option value='$data[charge_id]'>$data[charge_name]</option>";
	}
}
if($_POST["type"]=="load_service_entry_fields")
{
	//print_r($_POST);
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	$group_id=$_POST['group_id'];
	$service_id=$_POST['service_id'];
	$entry_service_slno=$_POST['entry_service_slno'];
	
	if($service_id==0)
	{
		exit;
	}
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd'"));
	$centreno=$pat_reg["center_no"];
	
	$service_info=mysqli_fetch_array(mysqli_query($link, "SELECT `charge_id`,`charge_name`,`amount`,`doc_link` FROM `charge_master` WHERE `group_id`='$group_id' AND `charge_id`='$service_id'"));
	
	$charge_rate=$service_info["amount"];
	
	$centre_rate=mysqli_fetch_array(mysqli_query($link,"SELECT `rate` FROM `service_rate` WHERE `centreno`='$centreno' AND `charge_id`='$service_id'"));
	if($centre_rate)
	{
		$charge_rate=$centre_rate["rate"];
	}
	
	$edit_fields_dis="disabled";
	if($emp_info["levelid"]==1)
	{
		$edit_fields_dis="";
	}
	$edit_fields_dis="";
	
	$doc_link_dis="display:none;";
	if($service_info["doc_link"]==1)
	{
		$doc_link_dis="";
	}
	
	$btn_name="Save";
	$del_btn_disabled="";
	$save_btn_disabled="";
	
	$quantity=1;
	$service_doc_id=0;
	
	if($entry_service_slno>0)
	{
		$pat_serv=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `slno`='$entry_service_slno' AND `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group_id' AND `service_id`='$service_id'"));
		if($pat_serv)
		{
			$service_info["charge_name"]=$pat_serv["service_text"];
			$charge_rate=$pat_serv["rate"];
			
			$quantity=$pat_serv["ser_quantity"];
			$service_doc_id=$pat_serv["doc_id"];
			
			$btn_name="Update";
			
			// check if bill generated of this service, then block update and delete
			
			$charge_info=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `charge_master` WHERE `main_charge_id`='$service_id'"));
			
			$pat_serv_generate=mysqli_fetch_array(mysqli_query($link, "SELECT `service_amount`,`pay_amount` FROM `ipd_pat_daily_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$charge_info[group_id]' AND `service_id`='$charge_info[charge_id]' AND `pay_id`>0"));
			if($pat_serv_generate)
			{
				$btn_disabled="disabled";
				$save_btn_disabled="disabled";
			}
		}
	}
?>
	<table class="table table-condensed table-bordered">
		<tr style="display:none;">
			<td>
				<span class="side_name"> &nbsp;&nbsp;&nbsp; Name</span>
				<input type="text" id="entry_service_name" value="<?php echo $service_info["charge_name"]; ?>" style="margin-left: 73px;width: 77%;" <?php echo $edit_fields_dis; ?>>
			</td>
		</tr>
		<tr>
			<td>
				<span class="side_name"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rate</span>
				<input type="text" id="entry_service_rate" value="<?php echo $charge_rate; ?>" style="margin-left: 73px;width: 77%;" <?php echo $edit_fields_dis; ?>>
			</td>
		</tr>
		<tr>
			<td>
				<span class="side_name">Quantity</span>
				<select id="entry_service_quantity" style="margin-left: 73px;width: 80%;">
				<?php
					for($n=1;$n<=500;$n++)
					{
						if($n==$quantity){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$n' $sel>$n</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<span class="side_name"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date</span>
				<input type="text" class="datepicker" id="entry_service_date" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 73px;width: 77%;" readonly>
			</td>
		</tr>
		<tr style="<?php echo $doc_link_dis; ?>">
			<td>
				<span class="side_name"> &nbsp;&nbsp; Doctor</span>
				<select id="entry_service_doc_id" style="margin-left: 73px;width: 80%;">
					<option value="0">None</option>
			<?php
				$qry=mysqli_query($link, "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `branch_id`='$branch_id' AND `status`=0 ORDER BY `Name` ASC");
				while($data=mysqli_fetch_array($qry))
				{
					if($service_doc_id==$data["consultantdoctorid"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$data[consultantdoctorid]' $sel>$data[Name]</option>";
				}
			?>
				</select>
			</td>
		</tr>
		<tr>
			<td style="text-align:center;">
				<input type="hidden" id="entry_service_slno" value="<?php echo $entry_service_slno; ?>">
				
				<button class="btn btn-save" id="save_service_entry_btn" onclick="save_service_entry()" <?php echo $save_btn_disabled; ?>><i class="icon-save"></i> <?php echo $btn_name; ?></button>
				
				<button class="btn btn-reset" onclick="service_entry_reset()"><i class="icon-refresh"></i> Reset</button>
				
				<button class="btn btn-delete" onclick="service_entry_delete('<?php echo $entry_service_slno; ?>')" <?php echo $btn_disabled; ?>><i class="icon-remove"></i> Delete</button>
			</td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="datepicker_min_max")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd'"));
	
	$pat_discharge=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' and payment_type='Final'"));
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

if($_POST["type"]=="save_service_entry")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	$service_slno=$_POST['service_slno'];
	$group_id=$_POST['group_id'];
	$service_id=$_POST['service_id'];
	$service_name=mysqli_real_escape_string($link, $_POST['service_name']);
	$service_rate=mysqli_real_escape_string($link, $_POST['service_rate']);
	$service_quantity=$_POST['service_quantity'];
	$service_date=$_POST['service_date'];
	$service_doc_id=$_POST['service_doc_id'];
	
	$service_amount=$service_rate*$service_quantity;
	
	$main_charge_info=mysqli_fetch_array(mysqli_query($link, "SELECT `main_charge_id` FROM `charge_master` WHERE `charge_id`='$service_id'"));
	$charge_info=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `charge_master` WHERE `charge_id`='$main_charge_info[main_charge_id]'"));
	
	if($service_slno==0)
	{
		if(mysqli_query($link, "INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$uhid','$ipd','$group_id','$service_id','$service_name','$service_quantity','$service_rate','$service_amount','0','$c_user','$time','$service_date','0','$service_doc_id','0',NULL)"))
		{
			echo "101@#@Saved";
		}
		else
		{
			echo "202@#@Failed, try again later.";
		}
	}
	else
	{
		$pat_serv_generate=mysqli_fetch_array(mysqli_query($link, "SELECT `service_amount`,`pay_amount` FROM `ipd_pat_daily_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$charge_info[group_id]' AND `service_id`='$charge_info[charge_id]' AND `pay_id`>0"));
		if(!$pat_serv_generate)
		{
			mysqli_query($link, "INSERT INTO `ipd_pat_service_details_update`(`slno`, `patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`, `edit_user`, `edit_date`, `edit_time`) SELECT *,'$c_user','$date','$time' FROM `ipd_pat_service_details` WHERE `slno`='$service_slno' AND `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group_id' AND `service_id`='$service_id' ");
			
			if(mysqli_query($link, "UPDATE `ipd_pat_service_details` SET `service_text`='$service_name',`ser_quantity`='$service_quantity',`rate`='$service_rate',`amount`='$service_amount',`user`='$c_user',`date`='$service_date',`doc_id`='$service_doc_id' WHERE `slno`='$service_slno' AND `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group_id' AND `service_id`='$service_id'"))
			{
				echo "101@#@Updated";
			}
			else
			{
				echo "202@#@Failed, try again later.";
			}
		}
		else
		{
			echo "303@#@Can't update, payment already received";
		}
	}
}

if($_POST["type"]=="add_services_to_bill")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	$service_slno=$_POST['service_slno'];
	$group_id=$_POST['group_id'];
	$service_id=$_POST['service_id'];
	
	// No Need
}

if($_POST["type"]=="load_selected_services")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];	
	
?>
	<table class="table table-condensed table-bordered table-hover" style="background-color: white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Service Name</th>
				<th style="text-align: center;">Quantity</th>
				<th style="text-align: center;">Rate</th>
				<th style="text-align: center;">Amount</th>
				<th style="text-align: center;">Service Date</th>
				<th>Entry By</th>
			</tr>
		</thead>
		<tbody>
<?php
		$m=1;
		$all_total=0;
		$group_qry=mysqli_query($link, "SELECT DISTINCT `group_id` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id` NOT IN(104,150,151,201) ORDER BY `date`,`slno` ASC");
		while($group_val=mysqli_fetch_array($group_qry))
		{
			$group_total=0;
			$group_id=$group_val["group_id"];
			$group_info=mysqli_fetch_array(mysqli_query($link, "SELECT `group_name` FROM `charge_group_master` WHERE `group_id`='$group_id'"));
?>
			<tr>
				<th colspan="7" style="text-align: center;background-color: #8fbc8fa3;"><?php echo $group_info["group_name"]; ?></th>
			</tr>
<?php
			$service_qry=mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group_id' ORDER BY `date`,`slno` ASC");
			while($service_info=mysqli_fetch_array($service_qry))
			{
				$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$service_info[user]' "));
?>
			<tr onclick="load_service_entry_each('<?php echo $service_info["slno"] ?>','<?php echo $service_info["group_id"] ?>','<?php echo $service_info["service_id"] ?>')" style="cursor:pointer;">
				<td><?php echo $m; ?></td>
				<td><?php echo $service_info["service_text"]; ?></td>
				<td style="text-align: center;"><?php echo $service_info["ser_quantity"]; ?></td>
				<td style="text-align: right;"><?php echo $service_info["rate"]; ?></td>
				<td style="text-align: right;"><?php echo $service_info["amount"]; ?></td>
				<td style="text-align: center;"><?php echo date("d-m-Y",strtotime($service_info["date"])); ?></td>
				<td><?php echo $user_info["name"]; ?></td>
			</tr>
<?php
				$m++;
				
				$group_total+=$service_info["amount"];
				$all_total+=$service_info["amount"];
			}
?>
			<tr>
				<th colspan="4" style="text-align: right;">Total</th>
				<th style="text-align: right;"><?php echo number_format($group_total,2); ?></th>
				<td></td>
				<td></td>
			</tr>
<?php
		}
?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="4" style="text-align: right;">Grand Total</th>
				<th style="text-align: right;"><?php echo number_format($all_total,2); ?></th>
				<td></td>
				<td></td>
			</tr>
		</tfoot>
	</table>
<?php
}
if($_POST["type"]=="service_entry_delete")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$group_id=$_POST['group_id'];
	$service_id=$_POST['service_id'];
	$slno=$_POST['slno'];
	
	$pay_final=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' AND `payment_type`='Final'"));
	
	if($pay_final)
	{
		echo "303@#@Can't delete, Already discharged";
	}
	else
	{
		mysqli_query($link, "INSERT INTO `ipd_pat_service_details_delete`(`slno`, `patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`, `del_user`, `del_date`, `del_time`) SELECT *,'$c_user','$date','$time' FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group_id' AND `service_id`='$service_id' AND `slno`='$slno' ");
		
		mysqli_query($link, "DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='$group_id' AND `service_id`='$service_id' AND `slno`='$slno'");
		
		echo "101@#@Deleted";
	}
}
?>
