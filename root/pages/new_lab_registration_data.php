<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date("y", $timestamp);
	return $new_date;
}

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

if(!$c_user)
{
	echo "Error";
}

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `branch_id`,`levelid`,`edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_access["branch_id"];

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST["type"];
$p_type_id=$_POST["p_type_id"];

$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));
$bill_name=$pat_typ_text["bill_name"];

$prefix_name=$pat_typ_text["prefix"];

if($type=="uhid_search")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	if(strlen($uhid)>2)
	{
		$pat_info_qry=mysqli_query($link, "SELECT `patient_id`,`uhid`,`name`,`gd_name`,`sex`,`dob`,`age`,`age_type`,`phone`,`address`,`state`,`district`,`city` FROM `patient_info` WHERE `uhid`='$uhid' ORDER BY `slno` DESC LIMIT 20");
		$pat_info_num=mysqli_num_rows($pat_info_qry);
		if($pat_info_num>0)
		{
?>
			<table class="table table-condensed table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>UHID</th>
						<th>Name</th>
						<th>Age</th>
						<th>Sex</th>
						<th>Phnoe</th>
						<th>Address</th>
					</tr>
				</thead>
<?php
			$n=1;
			while($pat_info=mysqli_fetch_array($pat_info_qry))
			{
				if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
				
				$address="";
				if($pat_info["address"]!="")
				{
					$address=$pat_info["address"];
				}
				else
				{
					$address=$pat_info["city"];
				}
?>
				<tr id="pat_tr<?php echo $n; ?>" class="pat_tr" onclick="load_emp_details('<?php echo $pat_info["patient_id"]; ?>','uhid')" style="cursor:pointer;">
					<td>
						<?php echo $n; ?>
						<input type="hidden" id="pat_uhid<?php echo $n; ?>" value="<?php echo $pat_info["patient_id"]; ?>">
					</td>
					<td><?php echo $pat_info["uhid"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $age; ?></td>
					<td><?php echo $pat_info["sex"]; ?></td>
					<td><?php echo $pat_info["phone"]; ?></td>
					<td><?php echo $address; ?></td>
				</tr>
<?php
				$n++;
			}
?>
			</table>
<?php
		}
	}
}
if($type=="load_center")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$qry=mysqli_query($link, "SELECT `centreno`,`centrename` FROM `centremaster` WHERE `branch_id`='$branch_id' ORDER BY `centrename` ASC");
	while($data=mysqli_fetch_array($qry))
	{
		if($data["centreno"]==$pat_reg["center_no"]){ $sel="selected"; }else{ $sel=""; }
		echo "<option value='$data[centreno]' $sel>$data[centrename]</option>";
	}
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
	
	$address="";
	if($pat_info["address"]!="")
	{
		$address=$pat_info["address"];
	}
	else
	{
		$address=$pat_info["city"];
	}
	
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
			<th>Phone</th>
			<th>Address</th>
		</tr>
		<tr>
			<td><?php echo $pat_info["uhid"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
			<td><?php echo $pat_info["phone"]; ?></td>
			<td><?php echo $address; ?></td>
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

if($type=="search_test")
{
	$test=mysqli_real_escape_string($link, $_POST["test"]);
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	$category_id=mysqli_real_escape_string($link, $_POST["category_id"]);
	$dept_id=mysqli_real_escape_string($link, $_POST["dept_id"]);
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	$sex=mysqli_real_escape_string($link, $_POST["sex"]);
	
	$chk_refer_doc=mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$refbydoctorid'"));
	if(!$chk_refer_doc)
	{
		//echo "<div style='font-weight: bold;color: red;'>Select Refer doctor first</div>";
		//exit();
	}
	
	$reg_category_str="";
	$reg_dept_str="";
	if($category_id>0)
	{
		$reg_category_str=" and category_id='$category_id'";
	}
	if($dept_id>0)
	{
		$reg_dept_str=" and type_id='$dept_id'";
	}

	$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `sex` FROM `patient_info` WHERE `patient_id`='$patient_id' "));
	if($pat_info)
	{
		$pat_sex=$pat_info["sex"];
	}
	else
	{
		$pat_sex=$sex;
	}
	$sex_str="";
	if($pat_sex=="Male")
	{
		$sex_str=" AND (sex='M' OR sex='all')";
	}
	if($pat_sex=="Female")
	{
		$sex_str=" AND (sex='F' OR sex='all')";
	}
	
	if($test=="")
	{
		//$q="select * from testmaster where testid>0 $reg_category_str $reg_dept_str $sex_str order by testname";
	}
	else
	{
		$q="select * from testmaster where testname like '%$test%' $reg_category_str $reg_dept_str $sex_str order by testname limit 20";
	}

	//echo $q;

	$data=mysqli_query($link, $q);
	
	$data_num=mysqli_num_rows($data);
	if($data_num>0)
	{
?>

		<table class="table table-bordered table-condensed" border="1" id="test_table" width="100%">
			<tr>
				<th></th>
				<th>Test Name</th>
				<th style="display:none;">Rate</th>
				<!--<th>Department</th>-->
			</tr>
	<?php
		$i=1;
		
		while($test_info=mysqli_fetch_array($data))
		{
			$display=0;
			
			if($display==0)
			{
				$testid=$test_info["testid"];
				$testname=$test_info["testname"];
				$test_rate=$test_info["rate"];
				
				$dept_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `test_department` WHERE `id`='$test_info[type_id]'"));
			?>
				<tr <?php echo "id=td".$i;?> onclick="load_test_click('<?php echo $testid;?>','<?php echo mysqli_real_escape_string($link, $testname);?>','<?php echo $test_rate;?>')" style="cursor:pointer">
					<td width="1%" class=test<?php echo $i;?> id=test<?php echo $i;?>>
						<?php echo $i;?><input type="hidden" class="test<?php echo $i;?>" value="<?php echo $testid;?>"/>
					</td>
					<td style="text-align:left" width="35%" <?php echo "class=test".$i;?>>
						<?php echo $testname;?>
					</td>
			<?php
				echo "<td width=30% class=test$i style='display:none;'>$test_rate</td>";
				//echo "<td>$dept_info[name]</td>";
				echo "</tr>";
				$i++;
			}
		}
	?>
		</table>
<?php
	}
}


if($type=="load_item_table")
{
?>
	<div>
		<table class="table table-responsive table-bordered table-condensed" id="test_list">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 5%">#</th>
					<th>Test Name</th>
					<th style="width: 15%;display:none;">Rate</th>
					<th class="test_discount" style="width: 15%;display:none;">Discount</th>
					<th style="width: 20%;">
						<i class="icon-cogs"></i>
						<b style="float:right;display:none;">Total: <span id="item_total_amount_tbl">0</span></b>
					</th>
				</tr>
			</thead>
			<tr id="item_footer"></tr>
		</table>
	</div>
<?php
}

if($type=="add_items")
{
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	$test_name=mysqli_real_escape_string($link, $_POST["test_name"]);
	$test_rate=mysqli_real_escape_string($link, $_POST["test_rate"]);
	$tr_counter=mysqli_real_escape_string($link, $_POST["tr_counter"]);
	$c_discount=mysqli_real_escape_string($link, $_POST["c_discount"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	//$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `c_discount` FROM `centremaster` WHERE `centreno`='$center_no' "));
	$centre_info["c_discount"]=0;
	$c_discount=$centre_info["c_discount"];
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$user' "));
	
	$rate_attribute="";
	
	$discount_attribute="";
	if($emp_access["discount_permission"]==0)
	{
		$discount_attribute="readonly";
	}
	if($c_discount>0)
	{
		$discount_attribute="readonly";
	}
	
	$discount_each=0;
	
	$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `testname`,`rate`,`category_id`,`type_id` FROM `testmaster` WHERE `testid`='$testid' "));
	$test_name=$test_info["testname"];
	$test_rate=$test_info["rate"];
	
	$test_centre=mysqli_fetch_array(mysqli_query($link, " SELECT `testid`,`rate` FROM `testmaster_rate` WHERE `testid`='$testid' AND `centreno`='$center_no' "));
	if($test_centre)
	{
		$test_rate=$test_centre["rate"];
	}
	else
	{
		if($centre_info["c_discount"]>0)
		{
			$centre_test_discount["com_per"]=$centre_info["c_discount"];
		}
		else
		{
			$centre_test_discount=mysqli_fetch_array(mysqli_query($link, " SELECT `com_per` FROM `centre_test_discount_setup` WHERE `centreno`='$center_no' AND `testid`='$testid' "));
			if(!$centre_test_discount)
			{
				$centre_test_discount=mysqli_fetch_array(mysqli_query($link, " SELECT `com_per` FROM `centre_test_discount_setup` WHERE `centreno`='$center_no' AND `type_id`='$test_info[type_id]' AND `testid`='0' "));
				if(!$centre_test_discount)
				{
					$centre_test_discount=mysqli_fetch_array(mysqli_query($link, " SELECT `com_per` FROM `centre_test_discount_setup` WHERE `centreno`='$center_no' AND `category_id`='$test_info[category_id]' AND `type_id`='0' AND `testid`='0' "));
				}
			}
		}
		if($centre_test_discount)
		{
			$discount_each=round(($test_rate*$centre_test_discount["com_per"])/100);
		}
	}
?>
	<tr id="tbl_tr<?php echo $tr_counter; ?>" class="item_cls">
		<td>
			<?php echo $tr_counter; ?>
			<input class="form-control each_row list_cls" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
		</td>
		<td>
			<?php echo $test_name; ?>
			<input class="form-control test_name list_cls" type="hidden" name="test_name<?php echo $tr_counter; ?>" id="test_name<?php echo $tr_counter; ?>" value="<?php echo $test_name; ?>" onkeyup="test_name_each(event,'<?php echo $tr_counter; ?>')" disabled>
			
			<input type="hidden" class="form-control testid" id="testid<?php echo $tr_counter; ?>" value="<?php echo $testid; ?>">
		</td>
		<td style="display:none;">
			<input class="form-control span1 numericc test_rate list_cls" type="text" name="test_rate<?php echo $tr_counter; ?>" id="test_rate<?php echo $tr_counter; ?>" value="<?php echo $test_rate; ?>" onkeyup="test_rate_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $rate_attribute; ?>>
		</td>
		<td class="test_discount" style="display:none;">
			<input class="form-control span1 numericc discount_each list_cls" type="text" name="discount_each<?php echo $tr_counter; ?>" id="discount_each<?php echo $tr_counter; ?>" value="<?php echo $discount_each; ?>" onkeyup="discount_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $discount_attribute; ?>>
		</td>
		<td>
			<button class="btn btn-danger btn-mini remove_btn_cls" id="remove_btn<?php echo $tr_counter; ?>" onclick="remove_tr('<?php echo $tr_counter; ?>')"><i class="icon-remove"></i></button>
		</td>
	</tr>
<?php
}

if($type=="load_test_list")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	$test_ids=mysqli_real_escape_string($link, $_POST["test_ids"]);
	$c_discount=mysqli_real_escape_string($link, $_POST["c_discount"]);
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$user' "));
	
	//$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `c_discount` FROM `centremaster` WHERE `centreno`='$center_no' "));
	$centre_info["c_discount"]=0;
	$c_discount=$centre_info["c_discount"];
	
	$rate_attribute="";
	
	$discount_attribute="";
	if($emp_access["discount_permission"]==0)
	{
		$discount_attribute="readonly";
	}
	if($c_discount>0)
	{
		$discount_attribute="readonly";
	}
	
	$discount_each=0;
	
	$tr_counter=1;
	
	$add_vaccu=0;
?>
	<div>
		<table class="table table-responsive table-bordered table-condensed" id="test_list">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 5%;text-align:center;">#</th>
					<th>Test Name</th>
					<th style="width: 15%;display:none;">Rate</th>
					<th class="test_discount" style="width: 15%;display:none;">Discount</th>
					<th style="width: 20%;">
						<i class="icon-cogs"></i>
						<b style="float:right;display:none;">Total: <span id="item_total_amount_tbl">0</span></b>
					</th>
				</tr>
			</thead>
<?php
		$test_ids=explode("##", $test_ids);
		foreach($test_ids AS $test_idd)
		{
			if($test_idd)
			{
				$test_idd=explode("@", $test_idd);
				$testid=$test_idd[0];
				$discount_each=$test_idd[1];
				
				$discount_each=0;
				
				$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `testid`,`testname`,`rate`,`rate_bc`,`category_id`,`type_id`,`vac_charge` FROM `testmaster` WHERE `testid`='$testid' "));
				$test_name=$test_info["testname"];
				$test_rate=$test_info["rate"];
				
?>
				<tr id="tbl_tr<?php echo $tr_counter; ?>" class="item_cls">
					<td style="text-align:center;">
						<?php echo $tr_counter; ?>
						<input class="form-control each_row list_cls" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
					</td>
					<td>
						<?php echo $test_name; ?>
						<input class="form-control test_name list_cls" type="hidden" name="test_name<?php echo $tr_counter; ?>" id="test_name<?php echo $tr_counter; ?>" value="<?php echo $test_name; ?>" onkeyup="test_name_each(event,'<?php echo $tr_counter; ?>')" disabled>
						
						<input type="hidden" class="form-control testid" id="testid<?php echo $tr_counter; ?>" value="<?php echo $testid; ?>">
					</td>
					<td style="display:none;">
						<input class="form-control span1 numericc test_rate list_cls" type="text" name="test_rate<?php echo $tr_counter; ?>" id="test_rate<?php echo $tr_counter; ?>" value="<?php echo $test_rate; ?>" onkeyup="test_rate_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $rate_attribute; ?>>
					</td>
					<td class="test_discount" style="display:none;">
						<input class="form-control span1 numericc discount_each list_cls" type="text" name="discount_each<?php echo $tr_counter; ?>" id="discount_each<?php echo $tr_counter; ?>" value="<?php echo $discount_each; ?>" onkeyup="discount_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $discount_attribute; ?>>
					</td>
					<td>
						<button class="btn btn-danger btn-mini remove_btn_cls" id="remove_btn<?php echo $tr_counter; ?>" onclick="remove_tr('<?php echo $tr_counter; ?>','<?php echo $testid; ?>')"><i class="icon-remove"></i></button>
					</td>
				</tr>
<?php
				$tr_counter++;
			}
		}
?>
			<tr id="item_footer"></tr>
		</table>
	</div>
<?php
}

if($type=="load_saved_test_list")
{
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$booking_id=mysqli_real_escape_string($link, $_POST["booking_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	$test_ids=mysqli_real_escape_string($link, $_POST["test_ids"]);
	$c_discount=mysqli_real_escape_string($link, $_POST["c_discount"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$user' "));
	
	$rate_attribute="";
	
	$discount_attribute="";
	if($emp_access["discount_permission"]==0)
	{
		$discount_attribute="readonly";
	}
	if($c_discount>0)
	{
		$discount_attribute="readonly";
	}
	
	$discount_each=0;
	
	$tr_counter=1;
	
?>
	<div>
		<table class="table table-responsive table-bordered table-condensed" id="test_list">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 5%">#</th>
					<th>Test Name</th>
					<th style="width: 15%;">Rate</th>
					<th class="test_discount" style="width: 15%;">Discount</th>
					<th style="width: 20%;">
						<i class="icon-cogs"></i>
						<b style="float:right;display:none;">Total: <span id="item_total_amount_tbl">0</span></b>
					</th>
				</tr>
			</thead>
<?php
			$test_str=" SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `addon_testid`=0 ";
			if($opd_id=="0" && $booking_id>0)
			{
				$test_str=" SELECT * FROM `test_advance_booking_details` WHERE `patient_id`='$patient_id' and `opd_id`='$booking_id' ";
			}
			$test_qry=mysqli_query($link, $test_str);
			while($test_val=mysqli_fetch_array($test_qry))
			{
				$testid=$test_val["testid"];
				$test_rate=$test_val["test_rate"];
				$discount_each=$test_val["test_discount"];
				
				$test_det=mysqli_fetch_array(mysqli_query($link, " SELECT `testid`,`testname`,`rate` FROM `testmaster` WHERE `testid`='$testid' "));
				
				$test_name=$test_det["testname"];
				
				
				$report_done=0;
				$testresult_path_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults` WHERE `patient_id`='$patient_id'  AND `opd_id`='$opd_id' AND `testid`='$testid' "));
				$testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_card` WHERE `patient_id`='$patient_id'  AND `opd_id`='$opd_id' AND `testid`='$testid' "));
				$testresult_radi_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_rad` WHERE `patient_id`='$patient_id'  AND `opd_id`='$opd_id' AND `testid`='$testid' "));
				$testresult_wild_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `widalresult` WHERE `patient_id`='$patient_id'  AND `opd_id`='$opd_id' AND `testid`='$testid' "));
				$testresult_summ_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$patient_id'  AND `opd_id`='$opd_id' AND `testid`='$testid' "));
				
				if($testresult_path_num>0 || $testresult_card_num>0 || $testresult_radi_num>0 || $testresult_wild_num>0 || $testresult_summ_num>0)
				{
					$report_done=1;
				}
				
?>
				<tr id="tbl_tr<?php echo $tr_counter; ?>" class="item_cls">
					<td>
						<?php echo $tr_counter; ?>
						<input class="form-control each_row list_cls" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
					</td>
					<td>
						<?php echo $test_name; ?>
						<input class="form-control test_name list_cls" type="hidden" name="test_name<?php echo $tr_counter; ?>" id="test_name<?php echo $tr_counter; ?>" value="<?php echo $test_name; ?>" onkeyup="test_name_each(event,'<?php echo $tr_counter; ?>')" disabled>
						
						<input type="hidden" class="form-control testid" id="testid<?php echo $tr_counter; ?>" value="<?php echo $testid; ?>">
					</td>
					<td>
						<input class="form-control span1 numericc test_rate list_cls" type="text" name="test_rate<?php echo $tr_counter; ?>" id="test_rate<?php echo $tr_counter; ?>" value="<?php echo $test_rate; ?>" onkeyup="test_rate_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $rate_attribute; ?>>
					</td>
					<td class="test_discount">
						<input class="form-control span1 numericc discount_each list_cls" type="text" name="discount_each<?php echo $tr_counter; ?>" id="discount_each<?php echo $tr_counter; ?>" value="<?php echo $discount_each; ?>" onkeyup="discount_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $discount_attribute; ?>>
					</td>
					<td>
				<?php
					if($report_done==0)
					{
						if($emp_access["edit_payment"]==1){
				?>
						<button class="btn btn-danger btn-mini remove_btn_cls" id="remove_btn<?php echo $tr_counter; ?>" onclick="remove_tr('<?php echo $tr_counter; ?>','<?php echo $testid; ?>')"><i class="icon-remove"></i></button>
				<?php
						}
					}else{
				?>
						<b style="color:green">Reported</b>
				<?php
					}
				?>
					</td>
				</tr>
<?php
				$tr_counter++;
			}
?>
			<tr id="item_footer"></tr>
		</table>
	</div>
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

if($type=="load_payment_info")
{
	$patient_id=$_POST["patient_id"];
	$opd_id=$_POST["opd_id"];
	$p_type_id=mysqli_real_escape_string($link, $_POST["p_type_id"]);
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id'"));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
	
	$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
	
	$save_type_str="Save";
	if($pat_reg)
	{
		$save_type_str="Update";
	}
	if($pat_reg)
	{
		// Department Wise Test Serial
		$dept_serial_generation=0;
		if($dept_serial_generation==1)
		{
			$dept_qry=mysqli_query($link, "SELECT DISTINCT a.`type_id` FROM `testmaster` a, `patient_test_details` b WHERE a.testid=b.testid AND a.category_id=1 AND b.patient_id='$patient_id' AND b.opd_id='$opd_id' AND b.batch_no='1'");
			while($dept_val=mysqli_fetch_array($dept_qry))
			{
				$type_id=$dept_val["type_id"];
				
				$table_name="test_dept_serial_generator_".$type_id;
				
				$check_serial=mysqli_fetch_array(mysqli_query($link, "SELECT `dept_serial` FROM `patient_test_dept_serial` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `type_id`='$type_id'"));
				if(!$check_serial)
				{
					$val = mysqli_query($link,"SELECT 1 FROM `$table_name` LIMIT 1");
					if($val == FALSE)
					{
						// Create The Table
						mysqli_query($link,"CREATE TABLE `$table_name` (`slno` int(11) NOT NULL, `patient_id` varchar(50) NOT NULL, `opd_id` varchar(50) NOT NULL, `type_id` int(11) NOT NULL, `user` int(11) NOT NULL, `date` date NOT NULL, `time` time NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
						mysqli_query($link,"ALTER TABLE `$table_name` ADD PRIMARY KEY (`slno`)");
						mysqli_query($link,"ALTER TABLE `$table_name` MODIFY `slno` int(11) NOT NULL AUTO_INCREMENT");
						mysqli_query($link,"ALTER TABLE `$table_name` ADD INDEX(`patient_id`)");
						mysqli_query($link,"ALTER TABLE `$table_name` ADD INDEX(`opd_id`)");
						mysqli_query($link,"ALTER TABLE `$table_name` ADD INDEX(`type_id`)");
						mysqli_query($link,"ALTER TABLE `$table_name` ADD INDEX(`user`)");
						mysqli_query($link,"ALTER TABLE `$table_name` ADD INDEX(`date`)");
					}
					
					$opd_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `$table_name` WHERE `date`='$date'"));
					if(!$opd_data)
					{
						mysqli_query($link, " TRUNCATE TABLE `$table_name` ");
					}
					
					mysqli_query($link, " INSERT INTO `$table_name`(`patient_id`, `opd_id`, `type_id`, `user`, `date`, `time`) VALUES ('$patient_id','$opd_id','$type_id','$c_user','$date','$time') ");
					
					$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `$table_name` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `type_id`='$type_id' AND `user`='$c_user' ORDER BY `slno` DESC LIMIT 0,1 "));
					
					//$dept_serial=$last_slno["slno"];
					
					$dept_info=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `test_department` WHERE `id`='$type_id'"));
					if(!$dept_info["prefix"])
					{
						$dept_info["prefix"]=$dept_info["prefix"][0];
					}
					$dept_serial=$dept_info["prefix"].$last_slno["slno"];
					
					mysqli_query($link, "INSERT INTO `patient_test_dept_serial`(`patient_id`, `opd_id`, `type_id`, `dept_serial`, `user`, `date`, `time`) VALUES ('$patient_id','$opd_id','$type_id','$dept_serial','$c_user','$date','$time')");
				}else
				{
					$dept_serial=$check_serial["dept_serial"];
				}
				
				$dept_test_qry=mysqli_query($link,"SELECT a.`slno`, a.`patient_id`, a.`opd_id`, a.`ipd_id`, a.`batch_no`, a.`testid` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`type_id`='$type_id' AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id'");
				while($dept_test=mysqli_fetch_assoc($dept_test_qry))
				{
					mysqli_query($link,"UPDATE `patient_test_details` SET `dept_serial`='$dept_serial' WHERE `patient_id`='$dept_test[patient_id]' AND `opd_id`='$dept_test[opd_id]' AND `ipd_id`='$dept_test[ipd_id]' AND `batch_no`='$dept_test[batch_no]' AND `testid`='$dept_test[testid]'");
				}
			}
		}
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Patient ID</th>
			<th>Bill No.</th>
			<th>UHID</th>
		<?php
			if($pat_reg["hospital_no"])
			{
				echo "<th>IP No.</th>";
			}
		?>
			<th>Receipt No.</th>
			<th>Serial No.</th>
			<th>Entry By</th>
			<th>Entry Time</th>
		</tr>
		<tr>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["uhid"]; ?></td>
		<?php
			if($pat_reg["hospital_no"])
			{
				echo "<td>".$pat_reg["hospital_no"]."</td>";
			}
		?>
			<td><?php echo $pat_reg["receipt_no"]; ?></td>
			<td><?php echo $pat_reg["serial_no"]; ?></td>
			<td><?php echo $user_info["name"]; ?></td>
			<td><?php echo date("d-m-Y",strtotime($pat_reg["date"])); ?> <?php echo date("h:i:s A",strtotime($pat_reg["time"])); ?></td>
		</tr>
	</table>
<?php
	}
?>
	<div id="save_tr" style="text-align:center;">
		<button class="btn btn-save" id="pat_save_btn" onClick="pat_save()"><i class="icon-save"></i> <?php echo $save_type_str; ?></button>
		<input type="hidden" class="span1" id="save_type" value="<?php echo $save_type_str; ?>">
<?php
	if($pat_reg)
	{
?>
		<button class="btn btn-edit" id="load_sample_btn" onclick="load_sample('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','','1')"><i class="icon-beaker"></i> Sample Receive</button>
<?php
	}
?>
		<button class="btn btn-new" id="opd_new_reg_btn" onclick="new_registration()"><i class="icon-edit"></i> New Registration</button>
	</div>
<?php
}


if($type=="pat_save")
{
	//~ print_r($_POST);
	//~ exit();
	
	$user=$c_user;
	
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	
	$save_type=mysqli_real_escape_string($link, $_POST["save_type"]);
	$p_type_id=mysqli_real_escape_string($link, $_POST["p_type_id"]);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	
	$pat_reg_type=mysqli_real_escape_string($link, $_POST["pat_reg_type"]);
	
	$name_title=mysqli_real_escape_string($link, $_POST["name_title"]);
	$pat_name=mysqli_real_escape_string($link, $_POST["pat_name"]);
	
	$pat_name_full=trim($name_title." ".$pat_name);
	
	$sex=mysqli_real_escape_string($link, $_POST["sex"]);
	$dob=mysqli_real_escape_string($link, $_POST["dob"]);
	$phone=mysqli_real_escape_string($link, $_POST["phone"]);
	$city=mysqli_real_escape_string($link, $_POST["city"]);
	$uhid=mysqli_real_escape_string($link, $_POST["pat_uhid"]);
	
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	$reg_type=mysqli_real_escape_string($link, $_POST["reg_type"]);
	$hospital_no=mysqli_real_escape_string($link, $_POST["hospital_no"]);
	$ward_id=mysqli_real_escape_string($link, $_POST["ward_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	$receipt_no=mysqli_real_escape_string($link, $_POST["receipt_no"]);
	
	$test_all=mysqli_real_escape_string($link, $_POST["test_all"]);
	
	if(!$urgent){ $urgent=0; }
	
	$serial_no_prefix="";
	if($reg_type==1)
	{
		$serial_no_prefix="O/";
	}
	if($reg_type==2)
	{
		$serial_no_prefix="I/";
	}
	
	include("patient_info_save.php");
	
	if($patient_id=="0")
	{
		echo $patient_id."@".$opd_id."@Failed, try again later.";
	}
	else
	{
		if($opd_id=="0")
		{
			// Save
			if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `hospital_no`, `refbydoctorid`, `center_no`, `ward_id`, `branch_id`, `urgent`, `receipt_no`, `serial_no`, `reg_type`, `type`, `user`, `date`, `time`) VALUES ('$patient_id',NULL,'$hospital_no','$refbydoctorid','$center_no','$ward_id','$branch_id','$urgent','$receipt_no','','$reg_type','$p_type_id','$user','$date','$time') "))
			{
				$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `reg_type`='$reg_type' AND `user`='$user' AND `date`='$date' ORDER BY `slno` DESC LIMIT 0,1 "));

				$last_row_num=$last_row["slno"];
				
				$patient_reg_type=$p_type_id;
				include("opd_id_generator.php");
				
				if(mysqli_query($link," UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id',`serial_no`='$serial_no' WHERE `slno`='$last_row_num' "))
				{
					$test_all=explode("##",$test_all);
					foreach($test_all AS $test)
					{
						if($test)
						{
							$test=explode("@",$test);
							$test_id=$test[0];
							$test_rate=$test[1];
							$test_discount=$test[2];
							
							if($test_id)
							{
								if(!$test_rate){ $test_rate=0; }
								if(!$test_discount){ $test_discount=0; }
								
								// Delete
								mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `batch_no`='1' and `testid`='$test_id' ");
								
								// Sample ID
								$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test_id' "));
								
								$sample_id=$smpl["SampleId"];
								if(!$sample_id){ $sample_id=0; }
								
								// Insert
								mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$test_id','$sample_id','$test_rate','$test_discount','','0','$date','$time','$user','2') ");
								
								// Add On Test
								$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test_id' ");
								while($s_t=mysqli_fetch_array($sub_tst))
								{
									$check_test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `testid`='$s_t[sub_testid]' "));
									if($check_test_num>0)
									{
										mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `testid`='$s_t[sub_testid]' ");
									}
									
									$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
									
									$sample_id=$samp_sb["SampleId"];
									if(!$sample_id){ $sample_id=0; }
									
									mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$s_t[sub_testid]','$sample_id','0','0','','$test_id','$date','$time','$user','2') ");
								}
							}
						}
					}
					$check_entry_test=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
					if($check_entry_test>0)
					{
						echo $patient_id."@".$opd_id."@Saved@101";
					}
					else
					{
						mysqli_query($link," DELETE FROM `uhid_and_opdid` WHERE `slno`='$last_row_num' ");
						
						$opd_id=0;
						echo $patient_id."@".$opd_id."@Failed, try again later@102";
					}
				}
			}else
			{
				$opd_id=0;
				echo $patient_id."@".$opd_id."@Failed, try again later.@103";
			}
		}else
		{
			$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
			
			mysqli_query($link, "INSERT INTO `uhid_and_opdid_edit`(`patient_id`, `opd_id`, `hospital_no`, `refbydoctorid`, `center_no`, `ward_id`, `branch_id`, `urgent`, `receipt_no`, `serial_no`, `reg_type`, `type`, `user`, `date`, `time`, `edit_user`, `edit_date`, `edit_time`) SELECT `patient_id`, `opd_id`, `hospital_no`, `refbydoctorid`, `center_no`, `ward_id`, `branch_id`, `urgent`, `receipt_no`, `serial_no`, `reg_type`, `type`, `user`, `date`, `time`,'$user','$date','$time' FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
			
			mysqli_query($link, "INSERT INTO `patient_test_details_edit`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type`, `edit_user`, `edit_date`, `edit_time`) SELECT `patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type`,'$user','$date','$time' FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
			
			// Delete
			mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `batch_no`='1'");
			
			$test_all=explode("##",$test_all);
			foreach($test_all AS $test)
			{
				if($test)
				{
					$test=explode("@",$test);
					$test_id=$test[0];
					$test_rate=$test[1];
					$test_discount=$test[2];
					
					if($test_id)
					{
						if(!$test_rate){ $test_rate=0; }
						if(!$test_discount){ $test_discount=0; }
						
						// Sample ID
						$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test_id' "));
						
						$sample_id=$smpl["SampleId"];
						if(!$sample_id){ $sample_id=0; }
						
						// Insert
						mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$test_id','$sample_id','$test_rate','$test_discount','','0','$date','$time','$user','2') ");
						
						// Add On Test
						$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test_id'");
						while($s_t=mysqli_fetch_array($sub_tst))
						{
							$check_test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `testid`='$s_t[sub_testid]' "));
							if($check_test_num>0)
							{
								mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$s_t[sub_testid]' AND `testid`!='$test_id' ");
							}
							
							$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
							
							$sample_id=$samp_sb["SampleId"];
							if(!$sample_id){ $sample_id=0; }
							
							mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$s_t[sub_testid]','$sample_id','0','0','','$test_id','$date','$time','$user','2') ");
						}
					}
				}
			}
			$check_entry_test=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
			if($check_entry_test>0)
			{
				echo $patient_id."@".$opd_id."@Updated@101";
			}
			else
			{
				echo $patient_id."@".$opd_id."@Failed, try again later..@104";
			}
		}
	}
}

if($type=="load_payment_info_old")
{
	$patient_id=$_POST["patient_id"];
	$opd_id=$_POST["opd_id"];
	$p_type_id=mysqli_real_escape_string($link, $_POST["p_type_id"]);
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$centre_info=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]'"));
	
	if($pat_reg)
	{
		
	}
	else
	{
		$pat_reg["report_delivery_time"]="6 PM";
	}
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));
	
	$edit_payment=$emp_access["edit_payment"];
	$discount_permission=$emp_access["discount_permission"];
	$discount_attribute="";
	if($emp_access["discount_permission"]==0)
	{
		$discount_attribute="readonly";
		$discount_permission=0;
	}
	
	echo "<input type='hidden' id='discount_permission' value='$discount_permission'>";
	echo "<input type='hidden' id='edit_payment' value='$edit_payment'>";
	
	$save_element_style="";
	if($patient_id=="0")
	{
		
	}
	
	if($opd_id=="0" || $opd_id=="")
	{
		$save_type_str="Save";
		$transaction_table_style="display:none;";
		$save_element_style="display:none;";
		$operation_str="";
	}
	else
	{
		$save_type_str="Update";
		$transaction_table_style="";
		
		$operation_str=" AND `operation`=1";
	}
	
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$bill_amount=$pat_pay_det["tot_amount"];
	$vaccu_charge=$pat_pay_det["regd_fee"];
	
	$discount_amount_master=$pat_pay_det["dis_amt"];
	$paid_amount_master=$pat_pay_det["advance"];
	$due_amount_master=$pat_pay_det["balance"];
	
	$discount_per_master=round(($discount_amount_master/($bill_amount-$vaccu_charge))*100,2);
	
	$pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid_amount`,ifnull(SUM(`discount_amount`),0) AS `dis_amount`,ifnull(SUM(`refund_amount`),0) AS `ref_amount`,ifnull(SUM(`tax_amount`),0) AS `tax_amount` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$paid_amount=$pay_det["paid_amount"];
	$discount_amount=$pay_det["dis_amount"];
	$tax_amount=$pay_det["tax_amount"];
	$refund_amount=$pay_det["ref_amount"];
	
	//$due_amount=$bill_amount-$paid_amount-$discount_amount-$tax_amount+$refund_amount;
	// Discount Refund
	//$pat_refund_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`ser_rate`),0) AS `tot_rate`, ifnull(SUM(a.`refund_amount`),0) AS `tot_refund` FROM `patient_refund_details` a, `patient_refund` b WHERE a.`refund_id`=b.`refund_id` AND b.`patient_id`='$patient_id' and b.`opd_id`='$opd_id' AND `refund_request_id` IN(SELECT `refund_request_id` FROM `refund_request` WHERE `refund_type`=1) "));
	
	//$discount_refund=$pat_refund_det["tot_rate"]-$pat_refund_det["tot_refund"];
	
	//$discount_amount-=$discount_refund;
	
	$due_amount=$bill_amount-$paid_amount-$discount_amount-$tax_amount+$refund_amount;
?>
	<table class="table table-condensed" style="<?php echo $transaction_table_style; ?>">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th><?php echo $prefix_name; ?></th>
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
		$payment_det_qry=mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND (`amount`>0 OR `discount_amount`>0 OR `discount_amount`<0 OR `refund_amount`>0) ORDER BY `pay_id` ASC"); //  AND `payment_mode`!='Credit'
		$payment_det_num=mysqli_num_rows($payment_det_qry);
		if($payment_det_num==0)
		{
			$payment_det_qry=mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' ORDER BY `pay_id` ASC");
			$payment_det_num=mysqli_num_rows($payment_det_qry);
		}
		while($payment_det=mysqli_fetch_array($payment_det_qry))
		{
			$pay_mode_type=mysqli_fetch_array(mysqli_query($link, "SELECT `operation` FROM `payment_mode_master` WHERE `p_mode_name`='$payment_det[payment_mode]'"));
			
			$user_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$payment_det[user]'"));
			
			//$pat_refund_det_each=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`ser_rate`),0) AS `tot_rate`, ifnull(SUM(a.`refund_amount`),0) AS `tot_refund` FROM `patient_refund_details` a, `patient_refund` b WHERE a.`refund_id`=b.`refund_id` AND b.`patient_id`='$patient_id' and b.`opd_id`='$opd_id' AND `pay_id`='$payment_det[pay_id]' "));
			
			//$discount_refund_each=$pat_refund_det_each["tot_rate"]-$pat_refund_det_each["tot_refund"];
			
			$discount_per_str="";
			if($payment_det["discount_amount"]>0)
			{
				//$discount_per_str="(".round(($payment_det["discount_amount"]/$payment_det["bill_amount"])*100,2)."%)";
			}
			
			$opd_payment_mode_trans_disable="";
			if($payment_det["amount"]==0 && $payment_det["refund_amount"]==0)
			{
				$opd_payment_mode_trans_disable="disabled";
			}
	?>
			<tr id="opd_trans<?php echo $zz; ?>">
				<td><?php echo $zz; ?></td>
				<td><?php echo $payment_det["patient_id"]; ?></td>
				<td><?php echo $payment_det["opd_id"]; ?></td>
				<td><?php echo $payment_det["transaction_no"]; ?></td>
				<td><?php echo $payment_det["amount"]; ?></td>
			<?php if($discount_refund_each>0){ ?>
				<td><?php echo number_format($discount_refund_each,2); ?></td>
			<?php }else{ ?>
				<td><?php echo $payment_det["discount_amount"].$discount_per_str; ?></td>
			<?php } ?>
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
			<?php } ?>
			<?php
				if($payment_det_num==1 && $payment_det["payment_type"]=="Advance")
				{
					if($emp_access["edit_payment"]==1){
			?>
					<button class="btn btn-edit btn-mini" style="float:right;" onclick="edit_receipt('<?php echo $payment_det["pay_id"]; ?>')"><i class="icon-edit"></i></button>
			<?php
					}
				}
			?>
			<?php
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
	<div id="advance_paid_div" style="display:none;">
		
	</div>
	<div id="res_payment_div">
		<table class="table table-condensed">
			<tr>
				<th style="width: 25%;">Total Bill Amount</th>
				<td>
					<span id="opd_bill_amount_str"><?php echo $bill_amount; ?></span>
					<input type="hidden" id="opd_bill_amount" value="<?php echo $bill_amount; ?>">
					<input type="hidden" id="opd_bill_amount_old" value="<?php echo $bill_amount; ?>">
					<input type="hidden" id="discount_amount_master" value="<?php echo $discount_amount_master; ?>">
					<input type="hidden" id="discount_per_master" value="<?php echo $discount_per_master; ?>">
				</td>
			</tr>
			<tr style="<?php echo $save_element_style; ?>">
				<th>Discount Amount</th>
				<td>
					<span id="opd_disount_amount_str"><?php echo number_format($discount_amount,2); ?></span>
					<input type="hidden" id="opd_disount_amount" value="<?php echo $discount_amount; ?>">
				</td>
			</tr>
			<tr style="<?php echo $save_element_style; ?>">
				<th>Paid Amount</th>
				<td>
					<span id="opd_paid_amount_str"><?php echo $paid_amount; ?></span>
					<input type="hidden" id="opd_paid_amount" value="<?php echo $paid_amount; ?>">
					<input type="hidden" id="paid_amount_master" value="<?php echo $paid_amount_master; ?>">
				</td>
			</tr>
	<?php
		if($refund_amount>0)
		{
	?>
			<tr>
				<th>Refunded Amount</th>
				<td>
					<span id="opd_refunded_amount_str"><?php echo $refund_amount; ?></span>
					<input type="hidden" id="opd_refunded_amount" value="<?php echo $refund_amount; ?>">
				</td>
			</tr>
	<?php
		}
	?>
			<tr style="<?php echo $save_element_style; ?>">
				<th>Balance Amount</th>
				<td>
					<span id="opd_balance_amount_str"><?php echo number_format($due_amount,2); ?></span>
					<input type="hidden" id="opd_balance_amount" value="<?php echo $due_amount; ?>">
					<input type="hidden" id="opd_balance_amount_old" value="<?php echo $due_amount; ?>">
				</td>
			</tr>
			<tr id="now_discount_tr">
				<th>Now Discount</th>
				<td>
					<input type="text" class="span1 numericcfloat discount_cls discount_cls1 " id="opd_now_discount_per" value="0" onkeyup="opd_discount_per(event)" placeholder="%" onpaste="return false;" ondrop="return false;" maxlength="4" <?php echo $discount_attribute; ?>>%
					
					<input type="text" class="span1 numericc discount_cls discount_cls1" id="opd_now_discount_amount" value="0" onkeyup="opd_discount_amount(event)" onpaste="return false;" ondrop="return false;" <?php echo $discount_attribute; ?>>INR
					<br>
					<input type="hidden" class="span2" id="opd_now_discount_reason" onkeyup="opd_now_discount_reason(event)" value="<?php echo $pat_pay_det["dis_reason"]; ?>" placeholder="Discount Reason">
					<label style="display:none;" class="doc_discount_div">
						<input type="checkbox" id="doc_discount" <?php if(isset($pat_reg)){ if($pat_reg["doc_discount"]==1){ echo "checked"; } } ?>> Doctor Discount
					</label>
				</td>
			</tr>
			<tr id="opd_now_pay_tr">
				<th>Now Pay</th>
				<td>
					<input type="text" class="span2 numericc" id="opd_now_pay" value="0" onkeyup="opd_now_pay(event)" onpaste="return false;" ondrop="return false;">
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
					$pay_mode_master_qry=mysqli_query($link, "SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='' $operation_str ORDER BY `sequence` ASC");
					while($pay_mode_master=mysqli_fetch_array($pay_mode_master_qry))
					{
						echo "<option value='$pay_mode_master[p_mode_name]'>$pay_mode_master[p_mode_name]</option>";
					}
				?>
					</select>
					<br>
					<!--<span id="opd_now_balance_reason_str" style="display:none;">
						<input type="text" class="span2" id="opd_now_balance_reason" value="" placeholder="Credit Reason">
					</span>-->
					<input type="hidden" class="span1" id="opd_now_ref_field">
					<input type="hidden" class="span1" id="opd_now_operation">
					<input type="hidden" class="span1" id="pay_id" value="0">
				</td>
			</tr>
			<tr class="opd_now_balance_tr" style="display:none;">
				<th>Now Balanace</th>
				<td>
					<span id="opd_now_balance">0</span>
				</td>
			</tr>
			<tr class="opd_now_balance_tr" style="display:none;">
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
			<tr>
				<th></th>
				<td>
					<label>
						<input type="checkbox" id="urgent" <?php if(isset($pat_reg)){ if($pat_reg["urgent"]==1){ echo "checked"; } } ?>> Urgent
					</label>
				</td>
			</tr>
			<tr>
				<th>Remarks</th>
				<td>
					<input type="text" class="span5" id="remarks" value="<?php echo $pat_reg["remarks"]; ?>">
				</td>
			</tr>
			<tr>
				<th>Report Delivery After :</th>
				<td>
					<input type="text" class="span5" id="report_delivery_time" value="<?php echo $pat_reg["report_delivery_time"]; ?>">
				</td>
			</tr>
			<tr id="save_tr">
				<td colspan="2" style="text-align:center;">
					<button class="btn btn-save" id="pat_save_btn" onClick="pat_save()"><i class="icon-save"></i> <?php echo $save_type_str; ?></button>
					<input type="hidden" class="span1" id="save_type" value="<?php echo $save_type_str; ?>">
				<?php if($patient_id!="0" && $opd_id!="0"){ ?>
					<button class="btn btn-print" id="print_con_receipt_btn" onClick="print_receipt('pages/cash_memo_lab_new.php?v=0')"><i class="icon-print"></i> Receipt</button>
				<?php if($centre_info["moneyreceipt"]==1){ ?>
					<button class="btn btn-print" id="print_con_receipt_btn" onClick="print_receipt('pages/cash_memo_lab_new_full.php?v=0')"><i class="icon-print"></i> Full Receipt</button>
				<?php } ?>
					<!--
					<button class="btn btn-print" id="print_con_bill_btn" onClick="print_receipt('pages/cash_memo_lab_bill_new.php?v=1')"><i class="icon-print"></i> Bill</button>
					
					<button class="btn btn-print" id="print_con_bill_btn" onClick="print_receipt('pages/cash_memo_lab_bill_new_full.php?v=1')"><i class="icon-print"></i> Full Bill</button>
					-->
					<div class="btn-group">
						<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Lab Slip <span class="caret"></span></button>
						<ul class="dropdown-menu">
						<?php
							$deps=mysqli_query($link,"select distinct a.id,a.name from test_department a,testmaster b,patient_test_details c where a.id=b.type_id and b.testid=c.testid and c.patient_id='$patient_id' and c.opd_id='$opd_id' order by a.id");
							if(mysqli_num_rows($deps)>1)
							{
						?>
								<li onclick='print_req(0)'><a>All</a></li>
								<li class="divider"></li>
						<?php 
							}
							while($dp=mysqli_fetch_array($deps))
							{
								echo "<li onclick='print_req($dp[id])'><a>$dp[name]</a></li>";
							}
						?>
						</ul>
					</div>
					<!--<button class="btn btn-print" id="load_test_btn" onClick="load_test_print()"><i class="icon-print"></i> Print Ind</button>-->
					
					<button class="btn btn-print" id="print_trf_btn" onClick="print_receipt('pages/test_request_form.php?v=0')"><i class="icon-print"></i> TRF</button>
					
				<?php } ?>
					<button class="btn btn-new" id="opd_new_reg_btn" onclick="new_registration()"><i class="icon-edit"></i> New Registration</button>
				</td>
			</tr>
		</table>
	</div>
	
<?php
}


if($type=="pat_save_old")
{
	//~ print_r($_POST);
	//~ exit();
	
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	$user=$c_user;
	
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
	
	$executive_id=mysqli_real_escape_string($link, $_POST["executive_id"]);
	$pharmacy_id=mysqli_real_escape_string($link, $_POST["pharmacy_id"]);
	$collection_id=mysqli_real_escape_string($link, $_POST["collection_id"]);
	$visit_source_id=mysqli_real_escape_string($link, $_POST["visit_source_id"]);
	
	$test_all=mysqli_real_escape_string($link, $_POST["test_all"]);
	
	$total=mysqli_real_escape_string($link, $_POST["total"]);
	$vaccu_charge=mysqli_real_escape_string($link, $_POST["vaccu_charge"]);
	$discount_amount=mysqli_real_escape_string($link, $_POST["now_discount"]);
	$discount_reason=mysqli_real_escape_string($link, $_POST["opd_now_discount_reason"]);
	$now_pay=mysqli_real_escape_string($link, $_POST["opd_now_pay"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["opd_now_payment_mode"]);
	$balance_reason=mysqli_real_escape_string($link, $_POST["now_balance_reason"]);
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["opd_now_cheque_ref_no"]);
	
	$remarks=mysqli_real_escape_string($link, $_POST["remarks"]);
	$report_delivery_time=mysqli_real_escape_string($link, $_POST["report_delivery_time"]);
	
	$urgent=mysqli_real_escape_string($link, $_POST["urgent"]);
	if(!$urgent){ $urgent=0; }
	
	$doc_discount=mysqli_real_escape_string($link, $_POST["doc_discount"]);
	if(!$doc_discount){ $doc_discount=0; }
	
	$grand_total=$total+$vaccu_charge;
	
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
	
	if($emp_access["levelid"]==8)
	{
		$coll_det=mysqli_fetch_array(mysqli_query($link, " SELECT `collection_id`, `centreno` FROM `collection_master` WHERE `emp_id`='$c_user' "));
		$collection_id=$coll_det["collection_id"];
		$sel_center=$center_no=$coll_det["centreno"];
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
	if(!$visit_source_id){ $visit_source_id=1; }
	if(!$refbydoctorid){ $refbydoctorid=101; }
	if(!$executive_id){ $executive_id=1; }
	if(!$pharmacy_id){ $pharmacy_id=1; }
	if(!$collection_id){ $collection_id=1; }
	if(!$crno){ $crno=0; }
	if(!$state){ $state=0; }
	if(!$district){ $district=0; }
	if(!$income_id){ $income_id=0; }
	if(!$regd_fee){ $regd_fee=0; }
	if(!$total){ $total=0; }
	if(!$vaccu_charge){ $vaccu_charge=0; }
	if(!$discount_amount){ $discount_amount=0; }
	if(!$now_pay){ $now_pay=0; }
	
	$dis_per=round(($discount_amount*100)/$total,2);

	$balance=$grand_total-$now_pay-$discount_amount;
	
	$refund_amount=0;
	$refund_reason="";
	$tax_amount=0;
	$tax_reason="";
	
	if($total==0)
	{
		$payment_mode="Cash";
		
		$balance=0;
		$now_pay=0;
		$discount_amount=0;
		$dis_per=0;
		$refund_amount=0;
		$tax_amount=0;
	}
	
	if(!$test_all)
	{
		$emp_access_str="#1#1#1#1#";
		echo $patient_id."@".$opd_id."@".$emp_access_str."@None test selected";
		exit();
	}
	
	
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
			
			if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`, `urgent`, `remarks`, `doc_discount`, `report_delivery_time`) VALUES ('$patient_id',NULL,'$date','$time','$user','$p_type_id','','$refbydoctorid','$sel_center','$hguide_id','$branch_id','$urgent','$remarks','$doc_discount','$report_delivery_time') "))
			{
				$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `date`='$date' AND `time`='$time' AND `user`='$user' AND `type`='$p_type_id' AND `refbydoctorid`='$refbydoctorid' AND `hguide_id`='$hguide_id' ORDER BY `slno` DESC LIMIT 0,1 "));

				$last_row_num=$last_row["slno"];
				
				$patient_reg_type=$p_type_id;
				include("opd_id_generator.php");
				
				if(mysqli_query($link," UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id',`ipd_serial`='$opd_serial' WHERE `slno`='$last_row_num' "))
				{
					mysqli_query($link, " DELETE FROM `patient_centre_test` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
					
					$test_all=explode("##",$test_all);
					foreach($test_all AS $test)
					{
						if($test)
						{
							$test=explode("@",$test);
							$test_id=$test[0];
							$test_rate=$test[1];
							$test_discount=$test[2];
							
							if($test_id)
							{
								$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `rate`,`rate_bc` FROM `testmaster` WHERE `testid`='$test_id' "));
								$testmaster_rate=$test_info["rate"];
								
								if($branch_id==2) // Baihata
								{
									$testmaster_rate=$test_info["rate_bc"];
								}
								
								if(!$test_rate){ $test_rate=0; }
								if(!$test_discount){ $test_discount=0; }
								
								// Delete
								mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `batch_no`='1' and `testid`='$test_id' ");
								
								// Sample ID
								$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test_id' "));
								
								$sample_id=$smpl["SampleId"];
								if(!$sample_id){ $sample_id=0; }
								
								// Insert
								mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$test_id','$sample_id','$test_rate','$test_discount','$testmaster_rate','0','$date','$time','$user','2') ");
								
								$process_no=$opd_id;
								$process_type=2;
								$testid=$test_id;
								include("test_count_deduct.php");
								
								// Add On Test
								$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test_id' ");
								while($s_t=mysqli_fetch_array($sub_tst))
								{
									$check_test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `testid`='$s_t[sub_testid]' "));
									if($check_test_num>0)
									{
										mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `testid`='$s_t[sub_testid]' ");
									}
									
									$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
									
									$sample_id=$samp_sb["SampleId"];
									if(!$sample_id){ $sample_id=0; }
									
									mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$s_t[sub_testid]','$sample_id','0','0','0','$test_id','$date','$time','$user','2') ");
									
									$process_no=$opd_id;
									$process_type=2;
									$testid=$s_t['sub_testid'];
									include("test_count_deduct.php");
								}
							}
						}
					}
					
					$check_entry_test=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
					if($check_entry_test>0)
					{
						$test_sum=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`test_rate`),0) AS `tot` FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
						$total_test_amount=$test_sum["tot"];
						
						if($total_test_amount!=$total)
						{
							$total=$total_test_amount;
							
							$non_vaccu_total=$total-$vaccu_charge;
							
							$discount_amount=round($non_vaccu_total*$dis_per/100);
							
							if($now_pay>$total)
							{
								$now_pay=$total;
								$discount_amount=0;
							}
							
							$balance=$total-$now_pay-$discount_amount;
						}
						
						mysqli_query($link, " INSERT INTO `invest_patient_payment_details`(`patient_id`, `opd_id`,`regd_fee`,`tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `refund_amount`, `tax_amount`, `balance`, `bal_reason`, `date`, `time`, `user`) VALUES ('$patient_id','$opd_id','$vaccu_charge','$total','$dis_per','$discount_amount','$discount_reason','$now_pay','$refund_amount','$tax_amount','$balance','$balance_reason','$date','$time','$user') ");
						
						// payment_detail_all
						
						$check_double_entry_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
						if($check_double_entry_pay_detail==0)
						{
							if($payment_mode=="Credit")
							{
								$now_pay=0;
								$balance=$total-$now_pay-$discount_amount;
							}
							if($now_pay==0)
							{
								$payment_mode="Credit";
								if($total==0)
								{
									$payment_mode="Cash";
									
									$balance=0;
									$now_pay=0;
									$discount_amount=0;
									$dis_per=0;
									$refund_amount=0;
									$tax_amount=0;
								}
							}
							
							if($now_pay>0 && $balance>0)
							{
								if($now_pay>0)
								{
									$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
									$already_paid=$check_paid["paid"];
									
									$bill_no=generate_bill_no_new($bill_name,$p_type_id);
									$balance_now=0;
									$balance_reason_now="";
									
									mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','$now_pay','$discount_amount','$discount_reason','$refund_amount','$refund_reason','$tax_amount','$tax_reason','$balance','$balance_reason','Advance','$payment_mode','$cheque_ref_no','$user','$date','$time','$p_type_id') ");
									
									$discount_amount=0;
									$discount_reason="";
									$cheque_ref_no="";
									$tax_amount=0;
									$tax_reason="";
								}
								if($balance>0)
								{
									$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
									$already_paid=$check_paid["paid"];
									
									$bill_no=generate_bill_no_new($bill_name,$p_type_id);
									$now_pay_now=0;
									$payment_mode="Credit";
									
									mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','$now_pay_now','$discount_amount','$discount_reason','$refund_amount','$refund_reason','$tax_amount','$tax_reason','$balance','$balance_reason','Advance','$payment_mode','$cheque_ref_no','$user','$date','$time','$p_type_id') ");
								}
							}
							else
							{
								$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
								$already_paid=$check_paid["paid"];
								
								$bill_no=generate_bill_no_new($bill_name,$p_type_id);
								
								mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','$now_pay','$discount_amount','$discount_reason','$refund_amount','$refund_reason','$tax_amount','$tax_reason','$balance','$balance_reason','Advance','$payment_mode','$cheque_ref_no','$user','$date','$time','$p_type_id') ");
							}
						}
						
						// Check double entry
						$pay_mode_qry=mysqli_query($link," SELECT `p_mode_name` FROM `payment_mode_master` ORDER BY `p_mode_name` ASC ");
						while($pay_mode=mysqli_fetch_array($pay_mode_qry))
						{
							$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Advance' AND `payment_mode`='$pay_mode[p_mode_name]' ");
							$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

							if($cash_adv_pay_num>1)
							{
								$h=1;
								while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
								{
									if($h>1)
									{
										$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$patient_id' and `ipd_id`='$opd_id' "));
										if(!$check_pay_mode_change)
										{
											mysqli_query($link," DELETE FROM `payment_detail_all` WHERE `pay_id`='$cash_adv_pay_val[pay_id]' ");
										}
									}
									$h++;
								}
							}
						}
						
						if($discount_amount>0)
						{
							// Discount Approve
							mysqli_query($link, " INSERT INTO `discount_approve`(`patient_id`, `pin`, `bill_amount`, `dis_amount`, `reason`, `user`, `approve_by`, `date`, `time`) VALUES ('$patient_id','$opd_id','$total','$discount_amount','$discount_reason','$user','0','$date','$time') ");
						}
						
						if($visit_type_id)
						{
							mysqli_query($link, " INSERT INTO `patient_visit_type_details`(`patient_id`, `opd_id`, `p_type_id`, `visit_type_id`,`date`,`time`) VALUES ('$patient_id','$opd_id','$p_type_id','$visit_type_id','$date','$time') ");
						}
						
						if($executive_id>0 || $pharmacy_id>0 || $collection_id>0 || $visit_source_id>0)
						{
							mysqli_query($link, " INSERT INTO `patient_refer_details`(`patient_id`, `opd_id`, `refbydoctorid`, `executive_id`, `pharmacy_id`, `collection_id`, `visit_source_id`, `date`, `time`) VALUES ('$patient_id','$opd_id','$refbydoctorid','$executive_id','$pharmacy_id','$collection_id','$visit_source_id','$date','$time') ");
						}
						
						// Insurance card no( if any)
						if($card_id>0)
						{
							mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$patient_id','$opd_id','$card_id','$card_no','$card_details') ");
						}
						
						$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name`,`phone` FROM `patient_info` WHERE `patient_id`='$patient_id' "));
						$phone=$pat_info["phone"];
						
						if($phone && $whatsapp==1)
						{
							// check opt-in
							$phone_check=mysqli_fetch_array(mysqli_query($link, " SELECT `phone` FROM `whatsapp_optin_numbers` WHERE `phone`='$phone' "));
							if(!$phone_check)
							{
								mysqli_query($link, " INSERT INTO `whatsapp_optin_numbers`(`phone`, `user`, `date`, `time`) VALUES ('$phone','$c_user','$date','$time') ");
								
								include("../../whatsapp/whatsapp_optin.php");
							}
							
							$pat_name_full=$pat_info["name"];
							//include("../../whatsapp/whatsapp_sms_send.php");
							
							//include("../../whatsapp/whatsapp_file_send.php");
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
			$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
			
			// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(max(`counter`),0) as cntr FROM `edit_counter` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `type`='$p_type_id' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			$edit_record=0;
			// edit counter record
			if(mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','$opd_id','$date','$time','$user','$p_type_id','$counter_num') "))
			{
				$edit_record=1;
				
				mysqli_query($link, " INSERT INTO `uhid_and_opdid_edit`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`, `urgent`, `remarks`, `doc_discount`, `report_delivery_time`, `counter`) VALUES ('$pat_reg[patient_id]','$pat_reg[opd_id]','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]','$pat_reg[type]','$pat_reg[ipd_serial]','$pat_reg[refbydoctorid]','$pat_reg[center_no]','$pat_reg[hguide_id]','$pat_reg[branch_id]','$pat_reg[urgent]','$pat_reg[remarks]','$pat_reg[doc_discount]','$pat_reg[report_delivery_time]','$counter_num') ");
			}
			
			mysqli_query($link, " UPDATE `uhid_and_opdid` SET `refbydoctorid`='$refbydoctorid', `center_no`='$sel_center', `hguide_id`='$hguide_id', `branch_id`='$branch_id', `urgent`='$urgent', `remarks`='$remarks', `doc_discount`='$doc_discount', `report_delivery_time`='$report_delivery_time' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
			
			if($emp_access["edit_payment"]==1)
			{
				$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				$earlier_bill_amount=$inv_pat_pay_detail["tot_amount"];
				
				// Test Entry
				if($edit_record==1)
				{
					$test_qry=mysqli_query($link, "  SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
					while($test_val=mysqli_fetch_array($test_qry))
					{
						mysqli_query($link, "  INSERT INTO `patient_test_details_edit`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type` ,`counter`) VALUES ('$test_val[patient_id]','$test_val[opd_id]','$test_val[ipd_id]','$test_val[batch_no]','$test_val[testid]','$test_val[sample_id]','$test_val[test_rate]','$test_val[test_discount]','$test_val[date]','$test_val[amount]','$test_val[addon_testid]','$test_val[time]','$test_val[user]','$test_val[type]','$counter_num') ");
					}
					
					// invest_patient_payment_details_edit
					$check_double_entry_pat_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details_edit` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `counter`='$counter_num' "));
					if($check_double_entry_pat_pay_detail==0)
					{
						$dis_reason_edit=mysqli_real_escape_string($link, $inv_pat_pay_detail["dis_reason"]);
						$bal_reason_edit=mysqli_real_escape_string($link, $inv_pat_pay_detail["bal_reason"]);
						
						mysqli_query($link, "  INSERT INTO `invest_patient_payment_details_edit`(`patient_id`, `opd_id`, `regd_fee`, `tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `refund_amount`, `tax_amount`, `balance`, `bal_reason`, `date`, `time`, `user` ,`counter`) VALUES ('$inv_pat_pay_detail[patient_id]','$inv_pat_pay_detail[opd_id]','$inv_pat_pay_detail[regd_fee]','$inv_pat_pay_detail[tot_amount]','$inv_pat_pay_detail[dis_per]','$inv_pat_pay_detail[dis_amt]','$dis_reason_edit','$inv_pat_pay_detail[advance]','$inv_pat_pay_detail[refund_amount]','$inv_pat_pay_detail[tax_amount]','$inv_pat_pay_detail[balance]','$bal_reason_edit','$inv_pat_pay_detail[date]','$inv_pat_pay_detail[time]','$inv_pat_pay_detail[user]','$counter_num') ");
					}
					
					// payment_detail_all_edit
					$check_double_entry_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `payment_detail_all_edit` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' and `counter`='$counter_num' "));
					if($check_double_entry_pay_detail==0)
					{
						$payment_detail_all_qry=mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
						while($payment_detail_all=mysqli_fetch_array($payment_detail_all_qry))
						{
							$discount_reason_edit=mysqli_real_escape_string($link, $payment_detail_all["discount_reason"]);
							$tax_reason_edit=mysqli_real_escape_string($link, $payment_detail_all["tax_reason"]);
							$balance_reason_edit=mysqli_real_escape_string($link, $payment_detail_all["balance_reason"]);
							
							mysqli_query($link, " INSERT INTO `payment_detail_all_edit`(`pay_id`, `patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`, `counter`) VALUES ('$payment_detail_all[pay_id]','$payment_detail_all[patient_id]','$payment_detail_all[opd_id]','$payment_detail_all[transaction_no]','$payment_detail_all[bill_amount]','$payment_detail_all[already_paid]','$payment_detail_all[amount]','$payment_detail_all[discount_amount]','$discount_reason_edit','$payment_detail_all[refund_amount]','$payment_detail_all[refund_reason]','$payment_detail_all[tax_amount]','$tax_reason_edit','$payment_detail_all[balance_amount]','$balance_reason_edit','$payment_detail_all[payment_type]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$payment_detail_all[user]','$payment_detail_all[date]','$payment_detail_all[time]','$payment_detail_all[encounter]','$counter_num') ");
						}
					}
				}
				// Delete
				mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `batch_no`='1' ");
				
				$test_all=explode("##",$test_all);
				foreach($test_all AS $test)
				{
					if($test)
					{
						$test=explode("@",$test);
						$test_id=$test[0];
						$test_rate=$test[1];
						$test_discount=$test[2];
						
						if($test_id)
						{
							$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `rate`,`rate_bc` FROM `testmaster` WHERE `testid`='$test_id' "));
							$testmaster_rate=$test_info["rate"];
							
							if($branch_id==2) // Baihata
							{
								$testmaster_rate=$test_info["rate_bc"];
							}
							
							if(!$test_rate){ $test_rate=0; }
							if(!$test_discount){ $test_discount=0; }
							
							// Sample ID
							$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test_id' "));
							
							$sample_id=$smpl["SampleId"];
							if(!$sample_id){ $sample_id=0; }
							
							// Insert
							mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$test_id','$sample_id','$test_rate','$test_discount','$testmaster_rate','0','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]','2') ");
							
							
							// Add On Test
							$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test_id' ");
							while($s_t=mysqli_fetch_array($sub_tst))
							{
								$check_test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `testid`='$s_t[sub_testid]' "));
								if($check_test_num>0)
								{
									mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `testid`='$s_t[sub_testid]' ");
								}
								
								$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
								
								$sample_id=$samp_sb["SampleId"];
								if(!$sample_id){ $sample_id=0; }
								
								mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$patient_id','$opd_id','','1','$s_t[sub_testid]','$sample_id','0','0','0','$test_id','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]','2') ");
							}
						}
					}
				}
				
				$process_no=$opd_id;
				//include("test_count_check.php");
				
				$test_sum=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`test_rate`),0) AS `tot` FROM `patient_test_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				$total_test_amount=$test_sum["tot"];
				
				if($total_test_amount!=$total)
				{
					$total=$total_test_amount;
					if($now_pay>$total)
					{
						$now_pay=0;
						$discount_now=0;
					}
				}
				
				// Payment
				$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				$earlier_bill_amount=$inv_pat_pay_detail["tot_amount"];
				$earlier_paid_amount=$inv_pat_pay_detail["advance"];
				$earlier_discount_amount=$inv_pat_pay_detail["dis_amt"];
				
				$bill_diff_amount=$total-$earlier_bill_amount;
				
				if($earlier_bill_amount!=$total)
				{
					$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
					
					$already_paid      =$check_paid["paid"];
					$already_discount  =$check_paid["discount"];
					$already_refund    =$check_paid["refund"];
					$already_tax       =$check_paid["tax"];
					
					$settle_amount=$already_paid+$already_discount+$already_tax-$already_refund;
					
					$net_paid=$already_paid-$already_refund;
					
					$payment_type="Balance";
					
					$discount_refund=0;
					
					$dis_per=round(($discount_amount*100)/$total,2);
					
					$balance_amount=$total-$discount_amount-$earlier_paid_amount;
					
					if($balance_amount<0)
					{
						$payment_type="Refund";
						
						$refund_amount  =abs($balance_amount);
						$refund_reason  ="Bill amount has been reduced";
						
						$balance_amount=0;
						$now_pay=0;
					}
					else
					{
						$balance_amount=$balance_amount-$now_pay;
					}
					$discount_now=$bill_diff_amount*$dis_per/100;
					if($discount_now<0)
					{
						$discount_refund  =abs($discount_now);
						$discount_reason="Bill amount has been reduced";
					}
					if($discount_now>0)
					{
						$discount_reason_val=mysqli_fetch_array(mysqli_query($link, " SELECT `discount_reason` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Advance' "));
						
						$discount_reason=$discount_reason_val["discount_reason"];
					}
					
					if($balance_amount<0)
					{
						echo $patient_id."@".$opd_id."@".$emp_access_str."@Wrong input1";
						exit();
					}
					
					//$total_paid=$already_paid+$now_pay-$refund_amount-$already_refund;
					//$total_discount=$already_discount+$discount_amount;
					$total_refund=$already_refund+$refund_amount;
					$total_tax=$already_tax+$tax_amount;
					
					$total_paid=$earlier_paid_amount+$now_pay-$refund_amount;
					
					mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `vaccu_charge`='$vaccu_charge',`tot_amount`='$total',`dis_per`='$dis_per',`dis_amt`='$discount_amount',`advance`='$total_paid',`refund_amount`='$total_refund',`tax_amount`='$total_tax',`balance`='$balance_amount',`bal_reason`='$bal_reason' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
					
					$bill_no=generate_bill_no_new($bill_name,$p_type_id);
					
					if($now_pay>0 || $discount_now>0 || $refund_amount>0)
					{
						mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','$now_pay','$discount_now','$discount_reason','$refund_amount','$refund_reason','$tax_amount','$tax_reason','$balance_amount','$balance_reason','$payment_type','$payment_mode','$cheque_ref_no','$user','$date','$time','$p_type_id') ");
					}
					
					if($balance_amount>0)
					{
						$pay_det_credit=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `date`='$date' AND `payment_mode`='Credit' "));
						if($pay_det_credit)
						{
							//mysqli_query($link, " UPDATE `payment_detail_all` SET `balance_amount`='$balance_amount',`balance_reason`='$balance_reason' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' ");
						}
						else
						{
							$bill_no=generate_bill_no_new($bill_name,$p_type_id);
							
							$payment_mode="Credit";
							
							mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','0','0','','0','','0','','$balance_amount','$balance_reason','Advance','$payment_mode','','$user','$date','$time','$p_type_id') ");
						}
					}
					else
					{
						if($pat_reg["date"]==$date)
						{
							//mysqli_query($link, " UPDATE `payment_detail_all` SET `balance_amount`='0' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' ");
						}
					}
				}
				else
				{
					$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
					
					$bill_amount=$inv_pat_pay_detail["tot_amount"];
					$discount_amount=$inv_pat_pay_detail["dis_amt"];
					$paid_amount=$inv_pat_pay_detail["advance"];
					
					$balance_amount=$bill_amount-$discount_amount-$paid_amount-$now_pay;
					
					$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
					
					$already_paid      =$check_paid["paid"];
					$already_discount  =$check_paid["discount"];
					$already_refund    =$check_paid["refund"];
					$already_tax       =$check_paid["tax"];
					
					$total_paid        =$paid_amount+$now_pay;
					
					$refund_amount=0;
					$refund_reason="";
					$tax_amount=0;
					$tax_reason="";
					
					$payment_type="Balance";
					
					if($balance_amount<0)
					{
						echo $patient_id."@".$opd_id."@".$emp_access_str."@Wrong input2";
						exit();
					}
					
					if($now_pay>0)
					{
						$discount_now=0;
						
						mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `advance`='$total_paid',`balance`='$balance_amount' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
						
						$bill_no=generate_bill_no_new($bill_name,$p_type_id);
						
						mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$bill_amount','$already_paid','$now_pay','$discount_now','$discount_reason','$refund_amount','$refund_reason','$tax_amount','$tax_reason','$balance_amount','$balance_reason','$payment_type','$payment_mode','$cheque_ref_no','$user','$date','$time','$p_type_id') ");
					}
				}
				
				
				if($discount_now>0)
				{
					// Discount Approve
					//mysqli_query($link, " INSERT INTO `discount_approve`(`patient_id`, `pin`, `bill_amount`, `dis_amount`, `reason`, `user`, `approve_by`, `date`, `time`) VALUES ('$patient_id','$opd_id','$total','$discount_now','$dis_reason','$user','0','$date','$time') ");
				}
				
				$check_refer=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_refer_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				if($check_refer)
				{
					//mysqli_query($link, " UPDATE `patient_refer_details` SET `refbydoctorid`='$refbydoctorid',`executive_id`='$executive_id',`pharmacy_id`='$pharmacy_id',`collection_id`='$collection_id',`visit_source_id`='$visit_source_id' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
				}
				else
				{
					if($executive_id>0 || $pharmacy_id>0 || $collection_id>0 || $visit_source_id>0)
					{
						//mysqli_query($link, " INSERT INTO `patient_refer_details`(`patient_id`, `opd_id`, `refbydoctorid`, `executive_id`, `pharmacy_id`, `collection_id`, `visit_source_id`, `date`, `time`) VALUES ('$patient_id','$opd_id','$refbydoctorid','$executive_id','$pharmacy_id','$collection_id','$visit_source_id','$date','$time') ");
					}
				}
				
				$check_card_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_card_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				if($check_card_entry)
				{
					if($card_id>0)
					{
						//mysqli_query($link, " UPDATE `patient_card_details` SET `card_id`='$card_id',`card_no`='$card_no',`card_details`='$card_details' WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
					}else
					{
						//mysqli_query($link, " DELETE FROM `patient_card_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' ");
					}
				}else
				{
					if($card_id>0)
					{
						//mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$patient_id','$opd_id','$card_id','$card_no','$card_details') ");
					}
				}
			}
			echo $patient_id."@".$opd_id."@".$emp_access_str."@Updated";
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
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		
		$bill_amount=$pat_pay_det["tot_amount"];
		$vaccu_charge=$pat_pay_det["regd_fee"];
		//~ $disount_amount=$pat_pay_det["dis_amt"];
		//~ $paid_amount=$pat_pay_det["advance"];
		$due_amount_str=$pat_pay_det["balance"];
		
		$paid_amount=$pay_det["amount"];
		$discount_amount=$pay_det["discount_amount"];
		$refund_amount=$pay_det["ref_amount"];
		$tax_amount=$pay_det["tax_amount"];
		$balance_amount=$pay_det["balance_amount"];
		
		$discount_per=round(($discount_amount/($bill_amount-$vaccu_charge))*100,2);
		
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
					<input type="text" class="span2 numericc" id="opd_now_pay" value="<?php echo $paid_amount; ?>" onkeyup="opd_now_pay(event)" onpaste="return false;" ondrop="return false;">
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
						if($pay_det["payment_mode"]==$pay_mode_master["p_mode_name"]){ $p_mode_sel="selected"; }else{ $p_mode_sel=""; }
						echo "<option value='$pay_mode_master[p_mode_name]' $p_mode_sel>$pay_mode_master[p_mode_name]</option>";
					}
				?>
					</select>
					<br>
					<input type="hidden" class="span1" id="opd_now_ref_field">
					<input type="hidden" class="span1" id="opd_now_operation">
					<input type="hidden" class="span1" id="pay_id" value="<?php echo $pay_id; ?>">
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
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$pay_id=mysqli_real_escape_string($link, $_POST["pay_id"]);
	$total=mysqli_real_escape_string($link, $_POST["total"]);
	$vaccu_charge=mysqli_real_escape_string($link, $_POST["vaccu_charge"]);
	$discount_amount=mysqli_real_escape_string($link, $_POST["now_discount"]);
	$discount_reason=mysqli_real_escape_string($link, $_POST["opd_now_discount_reason"]);
	$now_pay=mysqli_real_escape_string($link, $_POST["opd_now_pay"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["opd_now_payment_mode"]);
	$balance_reason=mysqli_real_escape_string($link, $_POST["now_balance_reason"]);
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["opd_now_cheque_ref_no"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	$user=$c_user;
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$pay_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Credit' "));
	
	$pay_num_credit=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit' "));
	if($pay_num==1 || $pay_num_credit==1)
	{
		$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		
		$bill_amount=$pat_pay_det["tot_amount"];
		$vaccu_charge=$pat_pay_det["regd_fee"];
		
		$bill_amount_non_vaccu_charge=$bill_amount-$vaccu_charge;
		
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
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(max(`counter`),0) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `type`='$p_type_id' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			// edit counter record
			if(mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','$opd_id','$date','$time','$user','$p_type_id','$counter_num') "))
			{
				$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				
				$dis_reason_edit=mysqli_real_escape_string($link, $inv_pat_pay_detail["dis_reason"]);
				$bal_reason_edit=mysqli_real_escape_string($link, $inv_pat_pay_detail["bal_reason"]);
				
				mysqli_query($link, "  INSERT INTO `invest_patient_payment_details_edit`(`patient_id`, `opd_id`, `regd_fee`, `tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `refund_amount`, `tax_amount`, `balance`, `bal_reason`, `date`, `time`, `user` ,`counter`) VALUES ('$inv_pat_pay_detail[patient_id]','$inv_pat_pay_detail[opd_id]','$inv_pat_pay_detail[regd_fee]','$inv_pat_pay_detail[tot_amount]','$inv_pat_pay_detail[dis_per]','$inv_pat_pay_detail[dis_amt]','$dis_reason_edit','$inv_pat_pay_detail[advance]','$inv_pat_pay_detail[refund_amount]','$inv_pat_pay_detail[tax_amount]','$inv_pat_pay_detail[balance]','$bal_reason_edit','$inv_pat_pay_detail[date]','$inv_pat_pay_detail[time]','$inv_pat_pay_detail[user]','$counter_num') ");
				
				$payment_detail_all_qry=mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
				while($payment_detail_all=mysqli_fetch_array($payment_detail_all_qry))
				{
					$discount_reason_edit=mysqli_real_escape_string($link, $payment_detail_all["discount_reason"]);
					$tax_reason_edit=mysqli_real_escape_string($link, $payment_detail_all["tax_reason"]);
					$balance_reason_edit=mysqli_real_escape_string($link, $payment_detail_all["balance_reason"]);
					
					mysqli_query($link, " INSERT INTO `payment_detail_all_edit`(`pay_id`, `patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`, `counter`) VALUES ('$payment_detail_all[pay_id]','$payment_detail_all[patient_id]','$payment_detail_all[opd_id]','$payment_detail_all[transaction_no]','$payment_detail_all[bill_amount]','$payment_detail_all[already_paid]','$payment_detail_all[amount]','$payment_detail_all[discount_amount]','$discount_reason_edit','$payment_detail_all[refund_amount]','$payment_detail_all[refund_reason]','$payment_detail_all[tax_amount]','$tax_reason_edit','$payment_detail_all[balance_amount]','$balance_reason_edit','$payment_detail_all[payment_type]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$payment_detail_all[user]','$payment_detail_all[date]','$payment_detail_all[time]','$payment_detail_all[encounter]','$counter_num') ");
				}
			}
			
			$discount_percetage=round(($discount_amount/$bill_amount_non_vaccu_charge)*100,2);
			
			// Update
			mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `dis_per`='$discount_percetage',`dis_amt`='$discount_amount',`dis_reason`='$discount_reason',`advance`='$now_pay',`balance`='$balance',`bal_reason`='$balance_reason' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
			
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

if($type=="delete_payment")
{
	//print_r($_POST);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$pay_id=mysqli_real_escape_string($link, $_POST["pay_id"]);
	$del_reason=mysqli_real_escape_string($link, $_POST["del_reason"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	$user=$c_user;
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
	
	$payment_detail_all=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `pay_id`='$pay_id' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
	
	
	$pay_id_amount=$payment_detail_all["amount"];
	$pay_id_discount=$payment_detail_all["discount_amount"];
	$pay_id_tax_amount=$payment_detail_all["tax_amount"];
	
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	
	$bill_amount=$pat_pay_det["tot_amount"];
	
	$paid_amount=$pat_pay_det["advance"]-$pay_id_amount;
	$discount_amount=$pat_pay_det["dis_amt"]-$pay_id_discount;
	$balance_amount=$pat_pay_det["balance"]+$pay_id_amount+$pay_id_discount+$pay_id_tax_amount;
	
	$discount_percetage=round(($discount_amount/$bill_amount)*100,2);
	
	if(mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `dis_per`='$discount_percetage',`dis_amt`='$discount_amount',`advance`='$paid_amount',`balance`='$balance_amount' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "))
	{
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
		
		mysqli_query($link, " DELETE FROM `payment_detail_all` WHERE `pay_id`='$pay_id' AND `patient_id`='$patient_id' AND `opd_id`='$opd_id' ");
		
		echo "Deleted";
	}
	else
	{
		echo "Failed, try gain later.";
	}
}

if($type=="payment_mode_change")
{
	//print_r($_POST);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$pay_id=mysqli_real_escape_string($link, $_POST["pay_id"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["payment_mode"]);
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["cheque_ref_no"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	$user=$c_user;
	
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
?>
