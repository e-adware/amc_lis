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

if($type=="search_patients")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	$name=mysqli_real_escape_string($link, $_POST["name"]);
	$address=mysqli_real_escape_string($link, $_POST["address"]);
	$father_name=mysqli_real_escape_string($link, $_POST["father_name"]);
	$mother_name=mysqli_real_escape_string($link, $_POST["mother_name"]);
	$guardian_name=mysqli_real_escape_string($link, $_POST["guardian_name"]);
	$phone=mysqli_real_escape_string($link, $_POST["phone"]);
	
	$str="SELECT * FROM `patient_info` WHERE `slno`>0";
	
	if($uhid)
	{
		$str.=" AND `patient_id` LIKE '$uhid'";
	}
	if(strlen($name)>2)
	{
		$str.=" AND `name` LIKE '%$name%'";
	}
	if(strlen($address)>2)
	{
		$str.=" AND `city` LIKE '%$address%'";
	}
	if(strlen($father_name)>2)
	{
		$str.=" AND `father_name` LIKE '%$father_name%'";
	}
	if(strlen($mother_name)>2)
	{
		$str.=" AND `mother_name` LIKE '%$mother_name%'";
	}
	if(strlen($guardian_name)>2)
	{
		$str.=" AND `gd_name` LIKE '%$guardian_name%'";
	}
	if(strlen($phone)>2)
	{
		$str.=" AND `phone` LIKE '$phone'";
	}
	
	if(strlen($opd_id)>2)
	{
		$str.=" AND `patient_id` IN (SELECT DISTINCT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id` like '$opd_id')";
	}
	
	$str.=" ORDER BY `slno` DESC LIMIT 50";
	
	//echo $str;

	$qry=mysqli_query($link, $str);
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Name</th>
			<th>Father Name</th>
			<th>Mother Name</th>
			<th>Guardian Name</th>
			<th style="display:none;">Occupation</th>
			<th>Address</th>
			<th>Phone</th>
			<th style="display:none;">File Create</th>
		</tr>
<?php
	$i=1;
	while($q=mysqli_fetch_array($qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','<?php echo $typ;?>','<?php echo $pat_info['file_create'];?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@".$typ."@@".$pat_info['file_create'];?>"/></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $pat_info['father_name'];?></td>
			<td><?php echo $pat_info['mother_name'];?></td>
			<td><?php echo $pat_info['gd_name'];?></td>
			<td style="display:none;"><?php echo $pat_info['occupation'];?></td>
			<td><?php echo $pat_info['city'];?></td>
			<td><?php echo $pat_info['phone'];?></td>
			<td style="display:none;">
			<?php
				if($pat_info['file_create']=="0")
				{
					echo "No";
				}
				else
				{
					echo "Yes";
				}
			?>
			</td>
		</tr>
<?php
		$i++;
	}
?>
	</table>
<?php
}

if($type=="load_district")
{
	$state=$_POST['state'];
	$patient_id=$_POST['patient_id'];
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `district` FROM `patient_info` WHERE `patient_id`='$patient_id'"));
	
	$qry=mysqli_query($link,"SELECT * FROM `district` WHERE `state_id`='$state' ORDER BY `name`");
?>
	<option value="0">Select</option>
<?php
	while($district=mysqli_fetch_array($qry))
	{
		$company_detaill=mysqli_fetch_array(mysqli_query($link, " SELECT `city` FROM `company_name` "));
		//$company_detaill["city"]="Sivasagar";
		if($company_detaill["city"]==$district['name']){ $sel_district="selected"; }else{ $sel_district=""; }
		
		if($pat_info)
		{
			if($pat_info["district"]==$district['district_id']){ $sel_district="selected"; }else{ $sel_district=""; }
		}
?>
		<option value="<?php echo $district['district_id']; ?>" <?php echo $sel_district; ?>><?php echo $district['name']; ?></option>
<?php
	}
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
	$religion_id=mysqli_real_escape_string($link, $_POST["religion_id"]);
	$marital_status=mysqli_real_escape_string($link, $_POST["marital_status"]);
	$email=mysqli_real_escape_string($link, $_POST["email"]);
	$father_name=mysqli_real_escape_string($link, $_POST["father_name"]);
	$mother_name=mysqli_real_escape_string($link, $_POST["mother_name"]);
	$gd_name=mysqli_real_escape_string($link, $_POST["gd_name"]);
	$g_relation=mysqli_real_escape_string($link, $_POST["g_relation"]);
	$gd_phone=mysqli_real_escape_string($link, $_POST["gd_phone"]);
	$occupation=mysqli_real_escape_string($link, $_POST["occupation"]);
	$gurdian_Occupation=mysqli_real_escape_string($link, $_POST["gurdian_Occupation"]);
	$income_id=mysqli_real_escape_string($link, $_POST["income_id"]);
	$education=mysqli_real_escape_string($link, $_POST["education"]);
	$state=mysqli_real_escape_string($link, $_POST["state"]);
	$district=mysqli_real_escape_string($link, $_POST["district"]);
	$city=mysqli_real_escape_string($link, $_POST["city"]);
	$police=mysqli_real_escape_string($link, $_POST["police"]);
	$post_office=mysqli_real_escape_string($link, $_POST["post_office"]);
	$pin=mysqli_real_escape_string($link, $_POST["pin"]);
	$address=mysqli_real_escape_string($link, $_POST["address"]);
	$file_create=mysqli_real_escape_string($link, $_POST["file_create"]);
	
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
	
	$blood_group="";
	$credit="";
	$fileno="";
	$esi_ip_no="";
	
	if(!$ptype){ $ptype=0; }
	if(!$religion_id){ $religion_id=0; }
	if(!$refbydoctorid){ $refbydoctorid=101; }
	if(!$crno){ $crno=0; }
	if(!$state){ $state=0; }
	if(!$district){ $district=0; }
	if(!$income_id){ $income_id=0; }
	
	
	
	$from = new DateTime($dob);
	$to   = new DateTime('today');
	$year =$from->diff($to)->y;
	$month=$from->diff($to)->m;
	if($year==0)
	{
		if($month==0)
		{
			$day=$from->diff($to)->d;
			$age=$day;
			$age_type="Days";
		}else
		{
			$age=$month;
			$age_type="Months";
		}
	}
	else
	{
		$age=$year;
		$age_type="Years";
	}
	
	if(!$education)
	{
		$education="";
	}
	
	if(!$religion_id){ $religion_id=0; }
	if(!$marital_status){ $marital_status=0; }
	if(!$income_id){ $income_id=0; }
	if(!$state){ $state=0; }
	if(!$district){ $district=0; }
	if(!$file_create){ $file_create=0; }
	
	$dob=date("Y-m-d",strtotime($dob));
	
	if($patient_id=="0")
	{
		
		if(mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time`) VALUES (NULL,'','$pat_name_full','$gd_name','$g_relation','$sex','$dob','$age','$age_type','$phone','$address','$email','$religion_id','$blood_group','$marital_status','$occupation','$gurdian_Occupation','$income_id','$education','$gd_phone','$pin','$police','$state','$district','$city','$post_office','$father_name','$mother_name','$file_create','$user','$date','$time') "))
		{
			$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `patient_info` WHERE `name`='$pat_name_full' AND `phone`='$phone' AND `user`='$user' AND `date`='$date' ORDER BY `slno` DESC LIMIT 0,1 "));
			
			$last_row_num=$last_row["slno"];
			
			$patient_reg_type=$p_type_id;
			include("patient_id_generator.php");
			
			$patient_id=$new_patient_id;
			$uhid_serial=$uhid_serial;
			
			mysqli_query($link," UPDATE `patient_info` SET `patient_id`='$patient_id',`uhid`='$uhid_serial' WHERE `slno`='$last_row_num' ");
			
			$patient_id=$new_patient_id;
		}
		else
		{
			$patient_id=0;
		}
	}
	else
	{
		// Edit Counter
		$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT max(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$patient_id' AND `opd_id`='' AND `type`='11' "));
		$edit_counter_num=$edit_counter["cntr"];
		$counter_num=$edit_counter_num+1;
		
		// edit counter record
		mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','','$date','$time','$user','11','$counter_num') ");
		
		// Patient Info
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
		if($pat_info)
		{
			$name_edit=mysqli_real_escape_string($link, $pat_info["name"]);
			$gd_name_edit=mysqli_real_escape_string($link, $pat_info["gd_name"]);
			$relation_edit=mysqli_real_escape_string($link, $pat_info["relation"]);
			$address_edit=mysqli_real_escape_string($link, $pat_info["address"]);
			
			mysqli_query($link," INSERT INTO `patient_info_edit`(`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `income_id`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `user`, `date`, `time`, `counter`) VALUES ('$pat_info[patient_id]','$pat_info[uhid]','$name_edit','$gd_name_edit','$relation_edit','$pat_info[sex]','$pat_info[dob]','$pat_info[age]','$pat_info[age_type]','$pat_info[phone]','$address_edit','$pat_info[email]','$pat_info[religion_id]','$pat_info[blood_group]','$pat_info[marital_status]','$pat_info[income_id]','$pat_info[gd_phone]','$pat_info[pin]','$pat_info[police]','$pat_info[state]','$pat_info[district]','$pat_info[city]','$pat_info[post_office]','$pat_info[father_name]','$pat_info[mother_name]','$pat_info[user]','$pat_info[date]','$pat_info[time]','$counter_num') ");
		}
		
		if(mysqli_query($link," UPDATE `patient_info` SET `name`='$pat_name_full',`gd_name`='$gd_name',`relation`='$g_relation',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`email`='$email',`religion_id`='$religion_id',`blood_group`='$blood_group',`marital_status`='$marital_status',`occupation`='$occupation',`gurdian_Occupation`='$gurdian_Occupation',`income_id`='$income_id',`education`='$education',`gd_phone`='$gd_phone',`pin`='$pin',`police`='$police',`state`='$state',`district`='$district',`city`='$city',`post_office`='$post_office',`father_name`='$father_name',`mother_name`='$mother_name',`file_create`='$file_create' WHERE `patient_id`='$patient_id' "))
		{
			echo "101@".$patient_id."@Updated";
		}
		else
		{
			echo "404@".$patient_id."@Failed, try again later";
		}
	}
}

?>

