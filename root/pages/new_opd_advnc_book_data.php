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
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info` FROM `employee` WHERE `emp_id`='$user' "));
	$emp_access_str="#".$emp_access["edit_info"];
	
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

if($type=="load_con_doctor")
{
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	$patient_id=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$dept_id=$_POST["dept_id"];
	$dname=$_POST["val"];
	
	$day_number=convert_date_to_day_num($date);
	
	$edit_consultantdoctorid=0;
	$edit_visit_fee=0;
	$edit_regd_fee=0;
	$edit_m_fee=0;
	if($opd_id!="0")
	{
		$edit_appointment=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`visit_fee` FROM `appointment_book` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		$edit_consultantdoctorid=$edit_appointment["consultantdoctorid"];
		
		$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		$edit_visit_fee=$con_pat_pay_detail["visit_fee"];
		$edit_regd_fee=$con_pat_pay_detail["regd_fee"];
		$edit_m_fee=$con_pat_pay_detail["m_fee"];
	}
?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
		<th>ID</th><th>Doctor Name</th>
<?php
		if($dname)
		{
			$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `Name` like '%$dname%' and `dept_id`='$dept_id' AND `status`='0' order by `Name` ");
		}
		else
		{
			$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `dept_id`='$dept_id' AND `status`='0' order by `Name` ");
		}
		//`branch_id`='$branch_id' AND 
		$d1_num=mysqli_num_rows($d);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			if($edit_consultantdoctorid==$d1['consultantdoctorid'])
			{
				$visitt_fee=$edit_visit_fee;
				$regdd_fee=$edit_regd_fee;
				$m_fee=$edit_m_fee;
				
				$visit_type_info=mysqli_fetch_array(mysqli_query($link, " SELECT `visit_type_id` FROM `patient_visit_type_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				
				$visit_type_id=$visit_type_info["visit_type_id"];
			}else
			{
				$m_fee=$d1["m_fee"];
				// Visit Fee
				$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$patient_id' AND `consultantdoctorid`='$d1[consultantdoctorid]' AND `visit_fee`>0 order by `slno` DESC "));
				
				$check_last_visit_fee_date=$check_last_visit_fee["date"];
				if(!$check_last_visit_fee_date)
				{
					$visitt_fee=$d1["opd_visit_fee"];
					$visit_type_id=1; // NEW PATIENT
				}else
				{
					$dates_array = getDatesFromRange($check_last_visit_fee_date, $date);
					$visit_fee_day_diff=sizeof($dates_array);
					if($visit_fee_day_diff<=$d1["opd_visit_validity"])
					{
						$visitt_fee=0;
						$visit_type_id=3; // FREE FOLLOW UP
					}else
					{
						$visitt_fee=$d1["opd_visit_fee"];
						$visit_type_id=6; // PAID FOLLOW UP
					}
				}
				
				if($opd_id==0) // if regd fee not doctor wise
				{
    				// Regd Fee
    				//~ $check_last_regd_fee_qry=mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$patient_id' AND `consultantdoctorid`='$d1[consultantdoctorid]' AND `regd_fee`>0 ORDER BY `slno` DESC LIMIT 1 "); // AND `dept_id`='$d1[dept_id]'
    				
    				$check_last_regd_fee_qry=mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$patient_id' AND `regd_fee`>0 AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `branch_id`='$branch_id') ORDER BY `slno` DESC LIMIT 1 "); // AND `dept_id`='$d1[dept_id]'
    				$check_last_regd_fee_num=mysqli_num_rows($check_last_regd_fee_qry);
    				
    				if($check_last_regd_fee_num==0)
    				{
    					$regdd_fee=$d1["opd_reg_fee"];
    				}else
    				{
    					$check_last_regd_fee=mysqli_fetch_array($check_last_regd_fee_qry);
    					$dates_array = getDatesFromRange($check_last_regd_fee["date"], $date);
    					$day_diff=sizeof($dates_array);
    					//~ if($day_diff<=$d1["opd_reg_validity"])
    					if($day_diff<=180)
    					{
    						$regdd_fee=0;
    					}else
    					{
    						$regdd_fee=$d1["opd_reg_fee"];
    					}
    				}
				}
				else
				{
					$regdd_fee=$edit_regd_fee;
				}
				
				// Centre Wise Fee
				$centre_rate=mysqli_fetch_array(mysqli_query($link, " SELECT a.`consultantdoctorid`,a.`Name`,a.`opd_visit_fee` AS `m_visit`,a.`opd_reg_fee` AS `m_regd`,b.`visit_fee` AS `c_visit`,b.`reg_fee` AS `c_regd` FROM `consultant_doctor_master` a,`opd_doc_rate` b WHERE a.`consultantdoctorid`=b.`consultantdoctorid` AND a.`consultantdoctorid`='$d1[consultantdoctorid]' AND b.`centreno`='$center_no' "));
				if($centre_rate)
				{
					if($visitt_fee>0)
					{
						$visitt_fee=$centre_rate["c_visit"];
					}
					if($regdd_fee>0)
					{
						$regdd_fee=$centre_rate["c_regd"];
					}
				}
				
				if($visitt_fee>0)
				{
					if($day_number==1) // Sunday=1, Monday=2,.....
					{
						$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_sunday_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]'"));
						if($check_extra)
						{
							$visitt_fee=$check_extra["opd_visit_fee"];
							$regdd_fee=$check_extra["opd_reg_fee"];
						}
					}
				}
				
				// Check Extra Visit Fee
				$not_session = array();
				$time_now=date("H:i:s");
				$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'>=`timer_one` AND '$time_now'<`timer_two` "));
				if($check_extra)
				{
					$visitt_fee=$check_extra["opd_visit_fee"];
				}else
				{
					array_push($not_session, 1); // Add Session One
					$not_session = join(',',$not_session);
					
					$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'>=`timer_one` AND `session` NOT IN($not_session) "));
					if($check_extra)
					{
						$visitt_fee=$check_extra["opd_visit_fee"];
					}else
					{
						$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'<=`timer_two` AND `session` NOT IN($not_session) "));
						if($check_extra)
						{
							$visitt_fee=$check_extra["opd_visit_fee"];
						}
					}
				}
			}
		?>
			<tr onClick="addoc_load('<?php echo $d1['consultantdoctorid'];?>','<?php echo $d1['Name'];?>','<?php echo $visitt_fee;?>','<?php echo $regdd_fee;?>','<?php echo $visit_type_id;?>','<?php echo $m_fee;?>')" style="cursor:pointer" <?php echo "id=addoc".$i;?>>
				<td>
					<?php echo $d1['consultantdoctorid'];?>
				</td>
				<td>
					<?php echo $d1['Name'];?>
					<div <?php echo "id=addvdoc".$i;?> style="display:none;">
						<?php echo "#".$d1['consultantdoctorid']."#".$d1['Name']."#".$visitt_fee."#".$regdd_fee."#".$visit_type_id."#".$m_fee;?>
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
	//~ print_r($_POST);
	//~ exit();
	
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
	
	$visit_source_id=mysqli_real_escape_string($link, $_POST["visit_source_id"]);
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	$dept_id=mysqli_real_escape_string($link, $_POST["dept_id"]);
	$hguide_id=mysqli_real_escape_string($link, $_POST["hguide_id"]);
	//$sel_center=mysqli_real_escape_string($link, $_POST["sel_center"]);
	$appoint_date=mysqli_real_escape_string($link, $_POST["appoint_date"]);
	$consultantdoctorid=mysqli_real_escape_string($link, $_POST["consultantdoctorid"]);
	$doctor_session=mysqli_real_escape_string($link, $_POST["doctor_session"]);
	$visit_type_id=mysqli_real_escape_string($link, $_POST["visit_type_id"]);
	
	$executive_id=mysqli_real_escape_string($link, $_POST["executive_id"]);
	$pharmacy_id=mysqli_real_escape_string($link, $_POST["pharmacy_id"]);
	
	$visit_fee=mysqli_real_escape_string($link, $_POST["visit_fee"]);
	$regd_fee=mysqli_real_escape_string($link, $_POST["regd_fee"]);
	$m_fee=mysqli_real_escape_string($link, $_POST["m_fee"]);
	$total=mysqli_real_escape_string($link, $_POST["total"]);
	
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
	if(!$dept_id){ $dept_id=0; }
	if(!$consultantdoctorid){ $consultantdoctorid=0; }
	if(!$doctor_session){ $doctor_session=0; }
	if(!$visit_type_id){ $visit_type_id=0; }
	if(!$visit_fee){ $visit_fee=0; }
	if(!$regd_fee){ $regd_fee=0; }
	if(!$m_fee){ $m_fee=0; }
	if(!$total){ $total=0; }
	
	$appoint_day =convert_date_to_day_num($appoint_date);
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info` FROM `employee` WHERE `emp_id`='$user' "));
	$emp_access_str=$emp_access["edit_info"];
	
	include("patient_info_save.php");
	
	if($patient_id==0 || $patient_id=="0")
	{
		echo $patient_id."@".$opd_id."@".$emp_access_str."@Failed, try again later..";
	}
	else
	{
		if($opd_id==0 || $opd_id=="0")
		{
			// Save
			if(mysqli_query($link, "INSERT INTO `advance_booking`(`booking_no`, `branch_id`, `patient_id`, `refbydoctorid`, `hguide_id`, `center_no`, `visit_source_id`, `executive_id`, `pharmacy_id`, `dept_id`, `consultantdoctorid`, `appointment_date`, `visit_fee`, `regd_fee`, `total`, `advance`, `balance`, `status`, `user`, `date`, `time`) VALUES (NULL,'$branch_id','$patient_id','$refbydoctorid','$hguide_id','$center_no','$visit_source_id','$executive_id','$pharmacy_id','$dept_id','$consultantdoctorid','$appoint_date','$visit_fee','$regd_fee','$total','0','$total','0','$user','$date','$time')"))
			{
				$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `booking_id` FROM `advance_booking` WHERE `patient_id`='$patient_id' AND `consultantdoctorid`='$consultantdoctorid' AND `user`='$user' AND `date`='$date' AND `time`='$time' ORDER BY `booking_id` DESC LIMIT 0,1 "));
				
				$opd_id=$last_row["booking_id"];
				
				mysqli_query($link, " INSERT INTO `advance_booking_activity`(`booking_id`, `opd_id`, `status`, `user`, `date`, `time`) VALUES ('$opd_id','','0','$user','$date','$time') "); // 0 = Pending
				
				echo $patient_id."@".$opd_id."@".$emp_access_str."@Booking Saved";
			}
			else
			{
				echo $patient_id."@0@".$emp_access_str."@INSERT INTO `advance_booking`(`booking_no`, `branch_id`, `patient_id`, `refbydoctorid`, `hguide_id`, `center_no`, `visit_source_id`, `executive_id`, `pharmacy_id`, `dept_id`, `consultantdoctorid`, `appointment_date`, `visit_fee`, `regd_fee`, `total`, `advance`, `balance`, `status`, `user`, `date`, `time`) VALUES (NULL,'$branch_id','$patient_id','$refbydoctorid','$hguide_id','$center_no','$visit_source_id','$executive_id','$pharmacy_id','$dept_id','$consultantdoctorid','$appoint_date','$visit_fee','$regd_fee','$total','0','$total','0','$user','$date','$time')";
			}
		}
		else
		{
			// Update
			$pat_book_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `advance_booking` WHERE `patient_id`='$patient_id' AND `booking_id`='$opd_id' "));
			
			if(mysqli_query($link, "UPDATE `advance_booking` SET `refbydoctorid`='$refbydoctorid',`hguide_id`='$hguide_id',`center_no`='$center_no',`visit_source_id`='$visit_source_id',`executive_id`='$executive_id',`pharmacy_id`='$pharmacy_id',`dept_id`='$dept_id',`consultantdoctorid`='$consultantdoctorid',`appointment_date`='$appoint_date',`user`='$user',`date`='$date',`time`='$time' WHERE `booking_id`='$opd_id' AND `patient_id`='$patient_id'"))
			{
				
				if($pat_book_det["consultantdoctorid"]!=$consultantdoctorid || $pat_book_det["appointment_date"]!=$appoint_date)
				{
					mysqli_query($link, " INSERT INTO `advance_booking_activity`(`booking_id`, `opd_id`, `status`, `user`, `date`, `time`) VALUES ('$opd_id','','3','$user','$date','$time') "); // 3 = Reschedule
				}
				
				echo $patient_id."@".$opd_id."@".$emp_access_str."@Booking Updated";
			}
			else
			{
				echo $patient_id."@".$opd_id."@".$emp_access_str."@Failed, try again later";
			}
		}
	}
}


?>
