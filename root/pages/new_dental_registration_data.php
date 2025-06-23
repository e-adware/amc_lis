<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST['type'];
$p_type_id=$_POST['p_type_id'];

$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));
$bill_name=$pat_typ_text["bill_name"];

$prefix_name=$pat_typ_text["prefix"];

if($type=="load_center")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$qry=mysqli_query($link, "SELECT `centreno`,`centrename` FROM `centremaster` WHERE `branch_id`='$branch_id' ORDER BY `centrename` ASC");
	while($data=mysqli_fetch_array($qry))
	{
		if(!$pat_reg){ if($branch_id==1){ $pat_reg["center_no"]="C100"; }else{ $pat_reg["center_no"]="C102"; } }
		if($data["centreno"]==$pat_reg["center_no"]){ $sel="selected"; }else{ $sel=""; }
		echo "<option value='$data[centreno]' $sel>$data[centrename]</option>";
	}
}

if($type=="get_access_detail")
{
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$user' "));
	$emp_access_str="#".$emp_access["edit_info"]."#".$emp_access["edit_payment"]."#".$emp_access["cancel_pat"]."#".$emp_access["discount_permission"]."#";
	
	echo $emp_access_str;
}
if($type=="search_patients")
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	if($typ=="name")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `name` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="phone")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `phone` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="uhid")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` like '$val%' order by `slno` DESC ";
		}
	}
	if($typ=="opd_serial")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` IN ( SELECT `patient_id` FROM `uhid_and_opdid` WHERE `uhid` like '$val%' ) order by `slno` DESC ";
		}
		
	}
	if($typ=="pin")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` IN ( SELECT DISTINCT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id` like '$val%' ) order by `slno` DESC ";
		}
	}
	
	//echo $q;

	$qry=mysqli_query($link, $q);
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>UHID</th>
			<th>Phone</th>
		</tr>
<?php
	$i=1;
	while($q=mysqli_fetch_array($qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','<?php echo $typ;?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@".$typ;?>"/></td>
			<td><?php echo $pat_info['phone'];?></td>
		</tr>
<?php
		$i++;
	}
?>
	</table>
<?php
}
if($type=="load_patient_info")
{
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
	
	if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
?>
	<table id="patient_info_tbl_load" class="table table-condensed" style="background-color:#FFF">
		<tr>
			<th colspan="4" style="text-align:center;">
				<h4>Patient Information</h4>
			</th>
		</tr>
		<tr>
			<th>UHID</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
		</tr>
		<tr>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
		</tr>
	</table>
<?php
}
if($type=="load_district")
{
	$state=$_POST['state'];
	
	$qry=mysqli_query($link,"SELECT * FROM `district` WHERE `state_id`='$state' ORDER BY `name`");
?>
	<option value="0">Select</option>
<?php
	while($district=mysqli_fetch_array($qry))
	{
		$company_detaill=mysqli_fetch_array(mysqli_query($link, " SELECT `city` FROM `company_name` "));
		//$company_detaill["city"]="Sivasagar";
		if($company_detaill["city"]==$district['name']){ $sel_district="selected"; }else{ $sel_district=""; }
?>
		<option value="<?php echo $district['district_id']; ?>" <?php echo $sel_district; ?>><?php echo $district['name']; ?></option>
<?php
	}
}
if($type=="load_centres")
{
	$source_id=$_POST["val"];
	
	$val=mysqli_fetch_array(mysqli_query($link, "SELECT `centreno` FROM `patient_source_master` WHERE `source_id`='$source_id'"));
	
	echo $val["centreno"];
}

if($type=="load_admit_doctor")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	$dname=$_POST["val"];
	
?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
		<th>ID</th><th>Doctor Name</th>
<?php
		if($dname)
		{
			$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `branch_id`='$branch_id' AND `Name` like '%$dname%' AND `status`='0' order by `Name` ");  // `dept_id`=46
		}
		else
		{
			$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `branch_id`='$branch_id' AND `status`='0' order by `Name` ");  // `dept_id`=46
		}
		$d1_num=mysqli_num_rows($d);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
?>
			<tr onClick="atdoc_load('<?php echo $d1['consultantdoctorid'];?>','<?php echo $d1['Name'];?>')" style="cursor:pointer" <?php echo "id=atdoc".$i;?>>
				<td>
					<?php echo $d1['consultantdoctorid'];?>
				</td>
				<td>
					<?php echo $d1['Name'];?>
					<div <?php echo "id=atdvdoc".$i;?> style="display:none;">
						<?php echo "#".$d1['consultantdoctorid']."#".$d1['Name'];?>
					</div>
				</td>
			</tr>
<?php
			$i++;
		}
		if($d1_num==0)
		{
			echo "<tr><td colspan='2'>No Doctor Available</td></tr>";
		}
?>
	</table>
<?php
}

if($type=="load_con_doctor")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	$dname=$_POST["val"];
	
?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
		<th>ID</th><th>Doctor Name</th>
<?php
		if($dname)
		{
			$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `branch_id`='$branch_id' AND `Name` like '%$dname%' AND `status`='0' order by `Name` ");
		}
		else
		{
			$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `branch_id`='$branch_id' AND `status`='0' order by `Name` ");
		}
		$d1_num=mysqli_num_rows($d);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
?>
			<tr onClick="addoc_load('<?php echo $d1['consultantdoctorid'];?>','<?php echo $d1['Name'];?>')" style="cursor:pointer" <?php echo "id=addoc".$i;?>>
				<td>
					<?php echo $d1['consultantdoctorid'];?>
				</td>
				<td>
					<?php echo $d1['Name'];?>
					<div <?php echo "id=addvdoc".$i;?> style="display:none;">
						<?php echo "#".$d1['consultantdoctorid']."#".$d1['Name'];?>
					</div>
				</td>
			</tr>
<?php
			$i++;
		}
		if($d1_num==0)
		{
			echo "<tr><td colspan='2'>No Doctor Available</td></tr>";
		}
?>
	</table>
<?php
}

if($type=="load_ref_doctor")
{
	$dname=$_POST['val'];

?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
	<th>ID</th><th>Doctor Name</th>
<?php
	
	if($dname)
	{

		$d=mysqli_query($link, "select * from refbydoctor_master where ref_name like '%$dname%'  order by ref_name");
	}
	else
	{
		$d=mysqli_query($link, "select * from refbydoctor_master where ref_name='Self' order by ref_name");
	}
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
?>
		<tr onclick="doc_load('<?php echo $d1['refbydoctorid'];?>','<?php echo $d1['ref_name'];?>','<?php echo $spec['Name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>><td>
		<?php echo $d1['refbydoctorid'];?></td><td><?php echo $d1['ref_name'].' / '.$d1['qualification'];?>
		<div <?php echo "id=dvdoc".$i;?> style="display:none;">
		<?php echo "#".$d1['refbydoctorid']."#".$d1['ref_name']."#".$d1['Name'];?>
		</div>
		</td></tr>
<?php
		$i++;
	}
?>
	</table>
<?php
}

if($type=="pat_save")
{
	//print_r($_POST);
	
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	
	$save_type=mysqli_real_escape_string($link, $_POST["save_type"]);
	$p_type_id=mysqli_real_escape_string($link, $_POST["p_type_id"]);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	
	$pat_reg_type=mysqli_real_escape_string($link, $_POST["pat_reg_type"]);
	
	$source_id=mysqli_real_escape_string($link, $_POST["patient_type"]);
	$name_title=mysqli_real_escape_string($link, $_POST["name_title"]);
	$pat_name=mysqli_real_escape_string($link, $_POST["pat_name"]);
	
	$pat_name_full=trim($name_title." ".$pat_name);
	
	$sex=mysqli_real_escape_string($link, $_POST["sex"]);
	$dob=mysqli_real_escape_string($link, $_POST["dob"]);
	$phone=mysqli_real_escape_string($link, $_POST["phone"]);
	$marital_status=mysqli_real_escape_string($link, $_POST["marital_status"]);
	$email=mysqli_real_escape_string($link, $_POST["email"]);
	$father_name=mysqli_real_escape_string($link, $_POST["father_name"]);
	$mother_name=mysqli_real_escape_string($link, $_POST["mother_name"]);
	$gd_name=mysqli_real_escape_string($link, $_POST["gd_name"]);
	$g_relation=mysqli_real_escape_string($link, $_POST["g_relation"]);
	$gd_phone=mysqli_real_escape_string($link, $_POST["gd_phone"]);
	$income_id=mysqli_real_escape_string($link, $_POST["income_id"]);
	$state=mysqli_real_escape_string($link, $_POST["state"]);
	$district=mysqli_real_escape_string($link, $_POST["district"]);
	$city=mysqli_real_escape_string($link, $_POST["city"]);
	$police=mysqli_real_escape_string($link, $_POST["police"]);
	$post_office=mysqli_real_escape_string($link, $_POST["post_office"]);
	$pin=mysqli_real_escape_string($link, $_POST["pin"]);
	$address=mysqli_real_escape_string($link, $_POST["address"]);
	
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	$hguide_id=mysqli_real_escape_string($link, $_POST["hguide_id"]);
	$visit_type_id=mysqli_real_escape_string($link, $_POST["visit_type_id"]);
	
	$sel_center=$center_no;
	
	$pat_source=mysqli_fetch_array(mysqli_query($link, " SELECT `source_id` FROM `patient_source_master` WHERE `centreno`='$center_no' "));
	if($pat_source)
	{
		$source_id=$pat_source["source_id"];
	}
	else
	{
		$source_id=1;
	}
	
	// Insurance card no( if any)
	$card_id=0;
	
	if(!$hguide_id)
	{
		$hguide_id=101; // Self
	}
	
	$blood_group="";
	$credit="";
	$fileno="";
	$esi_ip_no="";
	
	if(!$ptype){ $ptype=0; }
	if(!$visit_type_id){ $visit_type_id=0; }
	if(!$refbydoctorid){ $refbydoctorid=101; }
	if(!$crno){ $crno=0; }
	if(!$state){ $state=0; }
	if(!$district){ $district=0; }
	if(!$income_id){ $income_id=0; }
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$user' "));
	$emp_access_str="#".$emp_access["edit_info"]."#".$emp_access["edit_payment"]."#".$emp_access["cancel_pat"]."#".$emp_access["discount_permission"]."#";
	
	include("patient_info_save.php");
	
	if($patient_id=="0")
	{
		echo $patient_id."@".$opd_id."@".$emp_access_str."@Failed, try again later.";
	}
	else
	{
		if($opd_id=="0")
		{
			// Save
			
			if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`) VALUES ('$patient_id',NULL,'$date','$time','$user','$p_type_id','','$refbydoctorid','$sel_center','$hguide_id','$branch_id') "))
			{
				$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `date`='$date' AND `time`='$time' AND `user`='$user' AND `type`='$p_type_id' AND `refbydoctorid`='$refbydoctorid' AND `hguide_id`='$hguide_id' ORDER BY `slno` DESC LIMIT 0,1 "));

				$last_row_num=$last_row["slno"];
				
				$patient_reg_type=$p_type_id;
				include("opd_id_generator.php");
				
				if(mysqli_query($link," UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id',`ipd_serial`='$opd_serial' WHERE `slno`='$last_row_num' "))
				{
					
					if($visit_type_id>0)
					{
						mysqli_query($link, " INSERT INTO `patient_visit_type_details`(`patient_id`, `opd_id`, `p_type_id`, `visit_type_id`,`date`,`time`) VALUES ('$patient_id','$opd_id','$p_type_id','$visit_type_id','$date','$time') ");
					}
					
					if($card_id>0)
					{
						mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$patient_id','$opd_id','$card_id','$card_no','$card_details') ");
					}
					
					echo $patient_id."@".$opd_id."@".$emp_access_str."@Saved";
					
				}
				else
				{
					mysqli_query($link," DELETE FROM `uhid_and_opdid` WHERE `slno`='$last_row_num' ");
					
					$opd_id=0;
					echo $patient_id."@".$opd_id."@".$emp_access_str."@Failed, try again later.";
				}
			}
			else
			{
				echo $patient_id."@".$opd_id."@".$emp_access_str."@Failed, try again later..";
			}
		}
		else
		{
			if($emp_access["edit_info"]==1)
			{
				// Edit Counter
				$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `type`='$p_type_id' "));
				$edit_counter_num=$edit_counter["cntr"];
				$counter_num=$edit_counter_num+1;
				
				// edit counter record
				mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','$opd_id','$date','$time','$user','$p_type_id','$counter_num') ");
				
				// Visit info
				$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				
				mysqli_query($link, " INSERT INTO `uhid_and_opdid_edit`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`, `counter`) VALUES ('$pat_reg[patient_id]','$pat_reg[opd_id]','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]','$pat_reg[type]','$pat_reg[ipd_serial]','$pat_reg[refbydoctorid]','$pat_reg[center_no]','$pat_reg[hguide_id]','$pat_reg[branch_id]','$counter_num') ");
				
				mysqli_query($link, " UPDATE `uhid_and_opdid` SET `refbydoctorid`='$refbydoctorid', `center_no`='$sel_center', `hguide_id`='$hguide_id', `branch_id`='$branch_id' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
				
				if($visit_type_id>0)
				{
					$check_visit_type_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
					
					if($check_visit_type_entry)
					{
						mysqli_query($link, " UPDATE `patient_visit_type_details` SET `visit_type_id`='$visit_type_id' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
					}
					else
					{
						mysqli_query($link, " INSERT INTO `patient_visit_type_details`(`patient_id`, `opd_id`, `p_type_id`, `visit_type_id`,`date`,`time`) VALUES ('$patient_id','$opd_id','$p_type_id','$visit_type_id','$date','$time') ");
					}
				}
				
				$check_card_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_card_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				if($check_card_entry)
				{
					if($card_id>0)
					{
						mysqli_query($link, " UPDATE `patient_card_details` SET `card_id`='$card_id',`card_no`='$card_no',`card_details`='$card_details' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
					}else
					{
						mysqli_query($link, " DELETE FROM `patient_card_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
					}
				}else
				{
					if($card_id>0)
					{
						mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$patient_id','$opd_id','$card_id','$card_no','$card_details') ");
					}
				}
				
				echo $patient_id."@".$opd_id."@".$emp_access_str."@Updated";
			}
		}
	}
}

if($type=="load_paid_info")
{
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$pay_id=mysqli_real_escape_string($link, $_POST["pay_id"]);
	
	$discount_attribute="";
	if($emp_access["discount_permission"]==0)
	{
		$discount_attribute="readonly";
	}
	
	$pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `pay_id`='$pay_id' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
	
	if($pay_det)
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		
		$bill_amount=$pat_pay_det["tot_amount"];
		//~ $disount_amount=$pat_pay_det["dis_amt"];
		//~ $paid_amount=$pat_pay_det["advance"];
		$due_amount_str=$pat_pay_det["balance"];
		
		$paid_amount=$pay_det["amount"];
		$discount_amount=$pay_det["discount_amount"];
		$refund_amount=$pay_det["ref_amount"];
		$tax_amount=$pay_det["tax_amount"];
		$balance_amount=$pay_det["balance_amount"];
		
		$discount_per=round(($discount_amount/$bill_amount)*100,2);
		
		$discount_reason_style="hidden";
		if($discount_amount>0)
		{
			$discount_reason_style="text";
		}
		
		
		$balance_style="display:none;";
		if($balance_amount>0)
		{
			$balance_style="";
		}
		
		$refund_style="display:none;";
		if($refund_amount>0)
		{
			$refund_style="";
		}
		
		$cheque_ref_no_style="display:none;";
		if($pat_det["cheque_ref_no"]!="")
		{
			$cheque_ref_no_style="";
		}
?>
		<table class="table table-condensed">
			<tr>
				<th style="width: 25%;">Total Bill Amount</th>
				<td>
					<span id="opd_bill_amount_str"><?php echo $bill_amount; ?></span>
					<input type="hidden" id="opd_bill_amount" value="<?php echo $bill_amount; ?>">
				</td>
			</tr>
			<tr id="now_discount_tr">
				<th>Now Discount</th>
				<td>
					<input type="text" class="span1 numericcfloat discount_cls" id="opd_now_discount_per" value="<?php echo $discount_per; ?>" onkeyup="opd_discount_per(event)" placeholder="%" onpaste="return false;" ondrop="return false;" maxlength="4" <?php echo $discount_attribute; ?>>%
					
					<input type="text" class="span1 numericc discount_cls" id="opd_now_discount_amount" value="<?php echo $discount_amount; ?>" onkeyup="opd_discount_amount(event)" onpaste="return false;" ondrop="return false;" <?php echo $discount_attribute; ?>>INR
					<br>
					<input type="<?php echo $discount_reason_style; ?>" class="span2" id="opd_now_discount_reason" onkeyup="opd_now_discount_reason(event)" value="<?php echo $pay_det["discount_reason"]; ?>" placeholder="Discount Reason">
				</td>
			</tr>
			<tr id="opd_now_pay_tr">
				<th>Now Pay</th>
				<td>
					<input type="text" class="span2" id="opd_now_pay" value="<?php echo $paid_amount; ?>" onkeyup="opd_now_pay(event)" onpaste="return false;" ondrop="return false;">
				</td>
			</tr>
			<tr id="opd_now_refund_tr" style="color:red;<?php echo $refund_style; ?>">
				<th>Amount To Refund</th>
				<td>
					<b id="opd_now_refund_str"><?php echo $refund_amount; ?></b>
					<input type="hidden" class="span2" id="opd_now_refund" value="<?php echo $refund_amount; ?>" disabled>
				</td>
			</tr>
			<tr id="opd_now_payment_mode_tr">
				<th>Payment Mode</th>
				<td>
					<select class="span2" id="opd_now_payment_mode" onchange="opd_payment_mode_change(this.value)" onkeyup="opd_payment_mode_up(event)">
				<?php
					$pay_mode_master_qry=mysqli_query($link, "SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='' $operation_str ORDER BY `sequence` ASC");
					while($pay_mode_master=mysqli_fetch_array($pay_mode_master_qry))
					{
						if($pay_det["payment_mode"]==$pay_mode_master["p_mode_name"]){ $p_mode_sel="selected"; }{ $p_mode_sel=""; }
						echo "<option value='$pay_mode_master[p_mode_name]' $p_mode_sel>$pay_mode_master[p_mode_name]</option>";
					}
				?>
					</select>
					<br>
					<input type="hidden" class="span1" id="opd_now_ref_field">
					<input type="hidden" class="span1" id="opd_now_operation">
				</td>
			</tr>
			<tr class="opd_now_balance_tr" style="<?php echo $balance_style; ?>">
				<th>Now Balanace</th>
				<td>
					<span id="opd_now_balance"><?php echo $balance_amount; ?></span>
				</td>
			</tr>
			<tr class="opd_now_balance_tr" style="<?php echo $balance_style; ?>">
				<th>Balance Reason</th>
				<td>
					<input type="text" class="span2" id="now_balance_reason" onkeyup="now_balance_reason(event)" value="<?php echo $pay_det["balance_reason"]; ?>">
				</td>
			</tr>
			<tr id="opd_now_cheque_ref_no_tr" style="<?php echo $cheque_ref_no_style; ?>">
				<th>Cheque/Reference No</th>
				<td>
					<input type="text" class="span2" id="opd_now_cheque_ref_no" onkeyup="opd_now_cheque_ref_no(event)" value="<?php echo $pay_det["cheque_ref_no"]; ?>">
				</td>
			</tr>
			<tr id="save_tr">
				<td colspan="2" style="text-align:center;">
					<button class="btn btn-save" id="pat_save_btn" onClick="save_payment_edit('<?php echo $pay_id; ?>')"><i class="icon-save"></i> Update</button>
					<button class="btn btn-back" onclick="load_payment_info()"><i class="icon-backward"></i> Back</button>
				</td>
			</tr>
		</table>
		
<?php
	}
	else
	{
		echo "<h4>Payment no found.</h4>";
	}
}

if($type=="save_payment_edit")
{
	//print_r($_POST);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$pay_id=mysqli_real_escape_string($link, $_POST["pay_id"]);
	$total=mysqli_real_escape_string($link, $_POST["total"]);
	$discount_amount=mysqli_real_escape_string($link, $_POST["now_discount"]);
	$discount_reason=mysqli_real_escape_string($link, $_POST["opd_now_discount_reason"]);
	$now_pay=mysqli_real_escape_string($link, $_POST["opd_now_pay"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["opd_now_payment_mode"]);
	$balance_reason=mysqli_real_escape_string($link, $_POST["now_balance_reason"]);
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["opd_now_cheque_ref_no"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$pay_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Credit' "));
	if($pay_num==1)
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		
		$bill_amount=$pat_pay_det["tot_amount"];
		$balance=$bill_amount-$discount_amount-$now_pay;
		
		if($payment_mode=="Credit")
		{
			$now_pay=0;
			$balance=$bill_amount-$discount_amount-$now_pay;
		}
		if($now_pay==0)
		{
			$payment_mode="Credit";
			if($bill_amount==0)
			{
				$payment_mode="Cash";
				
				$balance=0;
				$now_pay=0;
				$discount_amount=0;
			}
			else if($discount_amount==0)
			{
				$payment_mode="Credit";
			}
			else
			{
				$payment_mode="Cash";
			}
		}
		
		if($balance<0)
		{
			echo "Failed.@405";
		}
		
		if($now_pay>0 || $discount_amount>0 || $balance>0)
		{
			// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `type`='$p_type_id' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			// edit counter record
			mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','$opd_id','$date','$time','$user','$p_type_id','$counter_num') ");
			
			$payment_detail_all_qry=mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
			while($payment_detail_all=mysqli_fetch_array($payment_detail_all_qry))
			{
				mysqli_query($link, " INSERT INTO `payment_detail_all_edit`(`pay_id`, `patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`, `counter`) VALUES ('$payment_detail_all[pay_id]','$payment_detail_all[patient_id]','$payment_detail_all[opd_id]','$payment_detail_all[transaction_no]','$payment_detail_all[bill_amount]','$payment_detail_all[already_paid]','$payment_detail_all[amount]','$payment_detail_all[discount_amount]','$payment_detail_all[discount_reason]','$payment_detail_all[refund_amount]','$payment_detail_all[refund_reason]','$payment_detail_all[tax_amount]','$payment_detail_all[tax_reason]','$payment_detail_all[balance_amount]','$payment_detail_all[balance_reason]','$payment_detail_all[payment_type]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$payment_detail_all[user]','$payment_detail_all[date]','$payment_detail_all[time]','$payment_detail_all[encounter]','$counter_num') ");
			}
			
			$discount_percetage=round(($discount_amount/$bill_amount)*100,2);
			
			// Update
			mysqli_query($link, " UPDATE `consult_patient_payment_details` SET `dis_per`='$discount_percetage',`dis_amt`='$discount_amount',`dis_reason`='$discount_reason',`advance`='$now_pay',`balance`='$balance',`bal_reason`='$balance_reason' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
			
			mysqli_query($link, " UPDATE `payment_detail_all` SET `amount`='$now_pay',`discount_amount`='$discount_amount',`discount_reason`='$discount_reason',`balance_amount`='$balance',`balance_reason`='$balance_reason',`payment_mode`='$payment_mode',`cheque_ref_no`='$cheque_ref_no' WHERE `pay_id`='$pay_id' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
			
			if($balance==0)
			{
				mysqli_query($link, " DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' ");
			}
			else
			{
				$payment_mode="Credit";
				
				$pay_det_credit=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' "));
				if($pay_det_credit)
				{
					// Update
					mysqli_query($link, " UPDATE `payment_detail_all` SET `balance_amount`='$balance',`balance_reason`='$balance_reason' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' ");
				}
				else
				{
					// Insert
					$bill_no=generate_bill_no_new($bill_name,$p_type_id);
					
					mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$bill_amount','$now_pay','0','0','','0','','0','','$balance','$balance_reason','Advance','$payment_mode','','$pat_reg[user]','$pat_reg[date]','$pat_reg[time]','$p_type_id') ");
				}
			}
			echo "Updated@101";
		}
		else
		{
			echo "Wrong input.@405";
		}
	}
	else
	{
		echo "Failed.@405";
	}
}



?>

