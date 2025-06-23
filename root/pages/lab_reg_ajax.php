<?php
session_start();

$c_user=$_SESSION["emp_id"];

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$date=date("Y-m-d");
$time=date("H:i:s");
$type=$_POST['type'];

if($type=="load_district")
{
	$state=$_POST['state'];
	$patient_id=$_POST['patient_id'];
	
	$q=mysqli_query($link,"SELECT * FROM `district` WHERE `state_id`='$state' ORDER BY `name`");
?>
	<select class="span2" id="dist" onkeyup="tab(this.id,event)">
		<option value="0">Select</option>
		<?php
		while($r=mysqli_fetch_array($q))
		{
			if($patient_id>0)
			{
				$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT `district` FROM `patient_info_rel` WHERE `patient_id`='$patient_id' "));
				if($pat_info_rel["district"]==$r['district_id']){ $sel_state="selected"; }else{ $sel_state=""; }
			}else
			{
				$company_detaill=mysqli_fetch_array(mysqli_query($link, " SELECT `city` FROM `company_name` "));
				//$company_detaill["city"]="Sivasagar";
				if($company_detaill["city"]==$r['name']){ $sel_state="selected"; }else{ $sel_state=""; }
			}
		?>
		<option value="<?php echo $r['district_id']; ?>" <?php echo $sel_state; ?>><?php echo $r['name']; ?></option>
		<?php
		}
		?>
	</select>
	<?php
}

if($type=="lab_pat_insert")
{
	$typ=$_POST['typ'];
	$ptype=$_POST['ptype'];
	if(!$ptype){ $ptype=0; }
	$credit=$_POST['credit'];
	$crno=$_POST['crno'];
	if(!$crno){ $crno=0; }
	$name_title=$_POST['name_title'];
	$pat_name=mysqli_real_escape_string($link, $_POST['pat_name']);
	$pat_name=trim($name_title." ".$pat_name);
	$dob=$_POST['dob'];
	$age=$_POST['age'];
	$age_type=$_POST['age_type'];
	$sex=$_POST['sex'];
	$phone=$_POST['phone'];
	$r_doc=$_POST['r_doc'];
	if(!$r_doc){ $r_doc=0; }
	$g_name=$_POST['g_name'];
	$g_name=mysqli_real_escape_string($link, $_POST['g_name']);
	$g_ph=$_POST['g_ph'];
	$address=mysqli_real_escape_string($link, $_POST['address']);
	$pin=$_POST['pin'];
	$police=mysqli_real_escape_string($link, $_POST['police']);
	$state=$_POST['state'];
	if(!$state){ $state=0; }
	$dist=$_POST['dist'];
	if(!$dist){ $dist=0; }
	$city=mysqli_real_escape_string($link, $_POST['city']);
	$fileno=$_POST['fileno'];
	$user=$_POST['usr'];
	$blood_group=$email="";
	
	$hguide_id=$_POST['hguide_id'];
	
	$source_id=$_POST['patient_type'];
	if(!$source_id){ $source_id=0; }
	$g_relation=$_POST['g_relation'];
	$marital_status=$_POST['marital_status'];
	if(!$marital_status){ $marital_status=0; }
	$income_id=$_POST['income_id'];
	if(!$income_id){ $income_id=0; }
	
	//$esi_ip_no=mysqli_real_escape_string($link, $_POST['esi_ip_no']);
	$esi_ip_no="";
	$post_office=mysqli_real_escape_string($link, $_POST['post_office']);
	
	$father_name="";
	$mother_name="";
	
	$entry_date=$_POST['entry_date'];
	$entry_time=$_POST['entry_time'];
	
	$age_str=age_calculator_save($dob);
	$age_str=explode(" ",$age_str);
	
	$age=$age_str[0];
	$age_type=$age_str[1];
	
	if($typ=="save_lab_pat_info")
	{
		$date=$entry_date;
		$time=$entry_time;
		
		$patient_reg_type=2;
		include("patient_id_generator.php");
		
		//$new_patient_id=generate_uhid($user);
		
		$check_double_entry=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$new_patient_id' "));
		if($check_double_entry==0)
		{
			if(mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`,`uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$new_patient_id','$uhid_serial','$pat_name','$g_name','$sex','$dob','$age','$age_type','$phone','$address','$email','$r_doc','','$user','$ptype','$blood_group','$date','$time') "))
			{
				mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`) VALUES ('$new_patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno','$post_office','$father_name','$mother_name')");
				
				mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`) VALUES ('$new_patient_id','$marital_status','$g_relation','$source_id','$esi_ip_no','$income_id') ");
			
				mysqli_query($link," INSERT INTO `pat_health_guide`(`patient_id`, `hguide_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$hguide_id','$user','$date','$time') ");
				
				echo "0@@".$new_patient_id;
			}
		}else
		{
			//mysqli_query($link," INSERT INTO `patient_info` (`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('0', '0', '', '', '', '', '', '', '', '', '', '0', '', '0', '', '', '$date', '$time') ");
			
			echo "4@@0"; // Already exists
		}
	}
	if($typ=="update_lab_pat_info")
	{
		$patient_id=trim($_POST["patient_id"]);
		
		// Edit Counter
		$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$patient_id' AND `opd_id`='' AND `type`='11' "));
		$edit_counter_num=$edit_counter["cntr"];
		$counter_num=$edit_counter_num+1;
		
		// edit counter record
		mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','','$date','$time','$user','11','$counter_num') ");
		
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
		if($pat_info)
		{
			mysqli_query($link," INSERT INTO `patient_info_edit`(`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`, `counter`) VALUES ('$pat_info[patient_id]','$pat_info[uhid]','$pat_info[name]','$pat_info[gd_name]','$pat_info[sex]','$pat_info[dob]','$pat_info[age]','$pat_info[age_type]','$pat_info[phone]','$pat_info[address]','$pat_info[email]','$pat_info[refbydoctorid]','$pat_info[center_no]','$pat_info[user]','$pat_info[payment_mode]','$pat_info[blood_group]','$pat_info[date]','$pat_info[time]','$counter_num') ");
		}
		
		mysqli_query($link," UPDATE `patient_info` SET  `name`='$pat_name',`gd_name`='$g_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`refbydoctorid`='$r_doc',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$patient_id' ");
		
		$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$patient_id' "));
		if($pat_info_rel)
		{
			mysqli_query($link," INSERT INTO `patient_info_rel_edit`(`patient_id`, `credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`, `counter`) VALUES ('$pat_info_rel[patient_id]','$pat_info_rel[credit]','$pat_info_rel[gd_phone]','$pat_info_rel[crno]','$pat_info_rel[pin]','$pat_info_rel[police]','$pat_info_rel[state]','$pat_info_rel[district]','$pat_info_rel[city]','$pat_info_rel[file_no]','$pat_info_rel[post_office]','$pat_info_rel[father_name]','$pat_info_rel[mother_name]','$counter_num') ");
			
			mysqli_query($link," UPDATE `patient_info_rel` SET `credit`='$credit',`gd_phone`='$g_ph',`crno`='$crno',`pin`='$pin',`police`='$police',`state`='$state',`district`='$dist',`city`='$city',`file_no`='$fileno',`post_office`='$post_office',`father_name`='$father_name',`mother_name`='$mother_name' WHERE `patient_id`='$patient_id' ");
		}else
		{
			mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`) VALUES ('$patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno','$post_office','$father_name','$mother_name')");
		}
		
		$pat_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$patient_id' "));
		if($pat_other)
		{
			mysqli_query($link," INSERT INTO `patient_other_info_edit`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`, `counter`) VALUES ('$pat_other[patient_id]','$pat_other[marital_status]','$pat_other[relation]','$pat_other[source_id]','$pat_other[esi_ip_no]','$pat_other[income_id]','$counter_num') ");
			
			mysqli_query($link," UPDATE `patient_other_info` SET `marital_status`='$marital_status',`relation`='$g_relation',`source_id`='$source_id',`esi_ip_no`='$esi_ip_no',`income_id`='$income_id' WHERE `patient_id`='$patient_id'");
		}else
		{
			mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`) VALUES ('$patient_id','$marital_status','$g_relation','$source_id','$esi_ip_no','$income_id') ");
		}
		
		mysqli_query($link," UPDATE `pat_health_guide` SET `hguide_id`='$hguide_id' WHERE `patient_id`='$patient_id' ");
		
		echo "0@@".$patient_id;
		
	}
}

if($type=="oo")
{
	
}

?>
