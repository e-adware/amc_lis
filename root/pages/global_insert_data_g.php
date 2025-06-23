<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d"); // important
$time=date("H:i:s");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}
//----------------------------------------------------------------------------------------------------------//
function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
//--------------------------------------------------------------------------------------------------------//
if($_POST["type"]=="save_consult_doc")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	$quali=$_POST['quali'];
	$quali= str_replace("'", "''", "$quali");
	$dept=$_POST['dept'];
	$add=$_POST['add'];
	$add= str_replace("'", "''", "$add");
	$contact=$_POST['contact'];
	$email=$_POST['email'];
	$pass=$_POST['pass'];
	$doc_type=$_POST['doc_type'];
	$vfee1=$_POST['vfee1'];
	$vfee2=$_POST['vfee2'];
	$valid=$_POST['valid'];
	$usr=$_POST['usr'];
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `consultant_doctor_master` SET `Name`='$name',`Designation`='$quali',`pass`='$pass',`Address`='$add',`Phone_number`='$contact',`email`='$email',`doc_type`='$doc_type',`opd_visit_fee`='$vfee1',`ipd_visit_fee`='$vfee2',`dept_id`='$dept',`validity`='$valid' WHERE `consultantdoctorid`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `employee`(`name`, `phone`, `address`, `Qualification`, `password`, `levelid`, `user`) VALUES ('$name','$contact','$add','$quali','$pass','5','$usr')")) // levelid 5=doctor
		{
			$e=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(emp_id) as max FROM `employee` WHERE `user`='$usr'"));
			mysqli_query($link,"INSERT INTO `consultant_doctor_master`(`Name`, `Designation`, `pass`, `Address`, `Phone_number`, `email`, `doc_type`, `opd_visit_fee`, `ipd_visit_fee`, `dept_id`, `validity`, `emp_id`, `user`) VALUES ('$name','$quali','$pass','$add','$contact','$email','$doc_type','$vfee1','$vfee2','$dept','$valid','$e[max]','$usr')");
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_dept")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `doctor_specialist_list` WHERE `speciality_id`='$id'"));
	if($n>0)
	{
		if(mysqli_query($link,"UPDATE `doctor_specialist_list` SET `name`='$name' WHERE `speciality_id`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `doctor_specialist_list`(`speciality_id`,`name`) VALUES ('$id','$name')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_level")
{
	$sl=$_POST['sl'];
	$id=$_POST['id'];
	$name=$_POST['name'];
	$ch=$_POST['ch'];
	if($sl>0)
	{
		if(mysqli_query($link,"UPDATE `level_master` SET `levelid`='$id', `name`='$name', `snippets`='$ch' WHERE `slno`='$sl'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `level_master`(`levelid`,`name`,`snippets`) VALUES ('$id','$name','$ch')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_ward")
{
	$sl=$_POST['sl'];
	$id=$_POST['id'];
	$name=$_POST['name'];
	$floor_name=$_POST['floor_name'];
	if($sl>0)
	{
		if(mysqli_query($link,"UPDATE `ward_master` SET `ward_id`='$id',`name`='$name',`floor_name`='$floor_name' WHERE `sl_no`='$sl'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `ward_master`(`ward_id`,`name`,`floor_name`) VALUES ('$id','$name','$floor_name')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_room")
{
	$id=$_POST['id'];
	$ward=$_POST['ward'];
	$room=$_POST['room'];
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `room_master` SET `ward_id`='$ward',`room_no`='$room' WHERE `room_id`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `room_master`(`ward_id`, `room_no`) VALUES ('$ward','$room')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_menu")
{
	$menu=$_POST['menu'];
	$par=$_POST['par'];
	$head=$_POST['head'];
	$access_to=$_POST['access_to'];
	$seq=$_POST['seq'];
	if(!$seq)
	{
		$max_seq=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(`sequence`) AS max FROM `menu_master` WHERE `header`='$head'"));
		$seq=$max_seq["max"];
	}
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `menu_master` WHERE `sequence`='$seq' AND `header`='$head' AND `par_id`!='$par'"));
	if($num>0)
	{
		$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `menu_master` WHERE `par_id`='$par'"));
		if($nn>0)
		{
			echo "Parameter Id Already Exists";
		}
		else
		{
			$qq=mysqli_query($link,"SELECT `par_id`,`sequence` AS sn FROM `menu_master` WHERE `header`='$head' AND `sequence`>='$seq'");
			while($rr=mysqli_fetch_array($qq))
			{
				$new=($rr['sn']+1);
				mysqli_query($link,"UPDATE `menu_master` SET `sequence`='$new' WHERE `par_id`='$rr[par_id]'");
			}
			if(mysqli_query($link,"INSERT INTO `menu_master`(`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('$par','$menu','$head','$seq','0')"))
			{
				foreach($access_to as $level)
				{
					mysqli_query($link," INSERT INTO `menu_access_detail`(`levelid`, `par_id`) VALUES ('$level','$par') ");
				}
				
				echo "Saved";
			}
			else
			{
				echo "Error";
			}
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `menu_master`(`par_id`, `par_name`, `header`, `sequence`, `hidden`) VALUES ('$par','$menu','$head','$seq','0')"))
		{
			foreach($access_to as $level)
			{
				mysqli_query($link," INSERT INTO `menu_access_detail`(`levelid`, `par_id`) VALUES ('$level','$par') ");
			}
			
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
	
}

if($_POST["type"]=="update_menu")
{
	$pid=$_POST['pid'];
	$pname=$_POST['pname'];
	$phead=$_POST['phead'];
	$seq=$_POST['seq'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `menu_master` WHERE `sequence`='$seq' AND `header`='$phead' AND `par_id`!='$pid'"));
	if($num>0)
	{
		$qq=mysqli_query($link,"SELECT `par_id`,`sequence` AS sn FROM `menu_master` WHERE `header`='$phead' AND `sequence`>='$seq'");
		while($rr=mysqli_fetch_array($qq))
		{
			$new=($rr['sn']+1);
			mysqli_query($link,"UPDATE `menu_master` SET `sequence`='$new' WHERE `par_id`='$rr[par_id]'");
		}
		mysqli_query($link,"UPDATE `menu_master` SET `par_name`='$pname',`header`='$phead',`sequence`='$seq' WHERE `par_id`='$pid'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"UPDATE `menu_master` SET `par_name`='$pname',`header`='$phead',`sequence`='$seq' WHERE `par_id`='$pid'");
		echo "Updated";
	}
}

if($_POST["type"]=="update_header")
{
	$hid=$_POST['hid'];
	$head=$_POST['hname'];
	$seq=$_POST['seq'];
	
	$o=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `menu_header_master` WHERE `id`='$hid'"));
	$q=mysqli_query($link,"SELECT * FROM `menu_header_master` WHERE `sequence`='$seq'");
	$n=mysqli_num_rows($q);
	if($n>0)
	{
		$a=mysqli_fetch_array($q);
		$i=$a['id'];
		$s=$o['sequence'];
		mysqli_query($link,"UPDATE `menu_header_master` SET `sequence`='$s' WHERE `id`='$i'");
	}
	if(mysqli_query($link,"UPDATE `menu_header_master` SET `name`='$head',`sequence`='$seq' WHERE `id`='$hid'"))
	{
		echo "Updated";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="save_header")
{
	$head=$_POST['hname'];
	$seq=$_POST['seq'];
	
	$q=mysqli_query($link,"SELECT * FROM `menu_header_master` WHERE `sequence`='$seq'");
	$n=mysqli_num_rows($q);
	if($n>0)
	{
		$a=mysqli_fetch_array($q);
		$i=$a['id'];
		$s=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(sequence) as max FROM `menu_header_master`"));
		$sq=$s['max']+1;
		mysqli_query($link,"UPDATE `menu_header_master` SET `sequence`='$sq' WHERE `id`='$i'");
	}
	if(mysqli_query($link,"INSERT INTO `menu_header_master`(`name`, `sequence`) VALUES ('$head','$seq')"))
	{
		echo "Saved";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="save_user")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	$quali=$_POST['quali'];
	$quali= str_replace("'", "''", "$quali");
	$staff=$_POST['staff'];
	$add=$_POST['add'];
	$add= str_replace("'", "''", "$add");
	$contact=$_POST['contact'];
	$pass=$_POST['pass'];
	$edit_opd=$_POST['edit_opd'];
	$edit_lab=$_POST['edit_lab'];
	$cancel_pat=$_POST['cancel_pat'];
	$usr=$_POST['usr'];
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `employee` SET `name`='$name',`phone`='$contact',`address`='$add',`Qualification`='$quali',`staff_type`='$staff',`password`='$pass',`levelid`='$staff',`edit_opd`='$edit_opd',`edit_lab`='$edit_lab',`cancel_pat`='$cancel_pat' WHERE `emp_id`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `employee`(`name`, `phone`, `address`, `Qualification`, `staff_type`, `password`, `levelid`, `user`, `edit_opd`, `edit_lab`, `cancel_pat`) VALUES ('$name','$contact','$add','$quali','$staff','$pass','$staff','$usr','$edit_opd','$edit_lab','$cancel_pat')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_refer_doc")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	$quali=$_POST['quali'];
	$quali= str_replace("'", "''", "$quali");
	$add=$_POST['add'];
	$add= str_replace("'", "''", "$add");
	$contact=$_POST['contact'];
	$usr=$_POST['usr'];
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `refbydoctor_master` SET `ref_name`='$name',`qualification`='$quali',`address`='$add',`phone`='$contact' WHERE `refbydoctorid`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `refbydoctor_master`(`ref_name`, `qualification`, `address`, `phone`) VALUES ('$name','$quali','$add','$contact')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_bed")
{
	$id=$_POST['id'];
	$ward=$_POST['ward'];
	$room=$_POST['room'];
	$bed=$_POST['bed'];
	$charge=$_POST['charge'];
	$othr_chrge=$_POST['othr_chrge'];
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `bed_master` SET `ward_id`='$ward',`room_id`='$room',`bed_no`='$bed',`charges`='$charge',`other_charges`='$othr_chrge' WHERE `bed_id`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `bed_master` WHERE `ward_id`='$ward' AND `room_id`='$room' AND `bed_no`='$bed'"));
		if($num<1)
		{
			if(mysqli_query($link,"INSERT INTO `bed_master`(`ward_id`, `room_id`, `bed_no`, `charges`, `other_charges`) VALUES ('$ward','$room','$bed','$charge','$othr_chrge')"))
			{
				echo "Saved";
			}
			else
			{
				echo "Error";
			}
		}
		else
		{
			echo "Saved";
		}
	}
}

if($_POST["type"]=="save_diag")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$diag=$_POST['diag'];
	$diag=str_replace("'", "''", "$diag");
	$usr=$_POST['usr'];
	
	$val=explode("#g#",$diag);
	$ar=sizeof($val);
	$nm=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($nm>0)
	mysqli_query($link,"DELETE FROM `pat_diagnosis` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	for($i=0; $i<$ar-1; $i++)
	{
		$v=explode("@",$val[$i]);
		$c1=$v[0];
		$c1=str_replace("'", "''", "$c1");
		$c2=$v[1];
		$c3=$v[2];
		mysqli_query($link, " INSERT INTO `pat_diagnosis`(`patient_id`, `opd_id`, `diagnosis`, `order`, `certainity`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$c1','$c2','$c3','$date','$time','$usr') ");
	}
	echo "Saved";
}

if($_POST["type"]=="save_disp")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$disp=$_POST['disp'];
	$ref_to=trim($_POST['ref_to']);
	$ref_to=str_replace("'", "''", "$ref_to");
	$oth_doc=$_POST['oth_doc'];
	$disp_note=$_POST['disp_note'];
	$disp_note=str_replace("'", "''", "$disp_note");
	$usr=$_POST['usr'];
	$appoint_day=convert_date_to_day_num($date);
	
	$disp_note=trim($disp_note);
	$disp_note=explode(",",$disp_note);
	$disp_note=array_unique($disp_note);
	$note="";
	
	foreach($disp_note as $not)
	{
		if($not)
		{
			$not=trim($not);
			$note.=$not.", ";
		}
	}
	
	if($disp==0)
	{
		$ref_to="";
		$oth_doc="";
	}
	if($disp==1)
	{
		$ref_to="";
		$oth_doc="";
	}
	if($disp==2 && $ref_to!="other")
	{
		$oth_doc="";
	}
	$qq=mysqli_query($link,"SELECT * FROM `pat_disposition` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	$nm=mysqli_num_rows($qq);
	if($nm>0)
	{
		$vl=mysqli_fetch_array($qq);
		mysqli_query($link,"UPDATE `pat_disposition` SET `disposition`='$disp',`disp_note`='$note',`ref_doctor_to`='$ref_to',`ref_opd`='',`doctor_name`='$oth_doc',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
		if($vl['ref_opd'])
		{
			//mysqli_query($link,"DELETE FROM `appointment_book` WHERE `patient_id`='$vl[patient_id]' AND `opd_id`='$vl[ref_opd]' AND `user`='$usr'");
			//mysqli_query($link,"DELETE FROM `uhid_and_opdid` WHERE `patient_id`='$vl[patient_id]' AND `opd_id`='$vl[ref_opd]' AND `user`='$usr'");
		}
	}
	else
	{
		mysqli_query($link, " INSERT INTO `pat_disposition`(`patient_id`, `opd_id`, `disposition`, `disp_note`, `ref_doctor_to`, `ref_opd`, `doctor_name`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$disp','$note','$ref_to','','$oth_doc','$date','$time','$usr') ");
	}
	$nt=explode(",",$note);
	foreach($nt as $all)
	{
		$all=trim($all);
		if($all)
		{
			$d_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `disposition_master` WHERE `disposition`='$all'"));
			if($d_num==0)
			{
				mysqli_query($link,"INSERT INTO `disposition_master`(`disposition`) VALUES ('$all')");
			}
		}
	}
	if($disp!=0 && $ref_to!="" && $ref_to!="other")
	{
		$alrdy_appoint_num=mysqli_num_rows(mysqli_query($link, " SELECT `appointment_no` FROM `appointment_book` WHERE `patient_id`='$uhid' and `consultantdoctorid`='$ref_to' and `appointment_date`='$date' "));
		if($alrdy_appoint_num==0)
		{
			$start=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid_start`,`pin_start` FROM `company_name`"));
			$opd_id=$start['pin_start'];
			//$opd_id=100;
			$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` "));
			$opd_id_num=$opd_id_qry["tot"];
			
			$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` "));
			$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
			
			$opd_id=$opd_id+$opd_id_num+$opd_id_cancel_num+1;
			
			
			$appnt_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`appointment_no`) as mx FROM `appointment_book` WHERE `consultantdoctorid`='$ref_to' and `appointment_date`='$date' "));
			$appnt_num=$appnt_qry["mx"];
			if($appnt_num==0)
			{
				$appoint_no=1;
			}else
			{
				$appoint_no=$appnt_num+1;
			}
			
			// uhid_and_opdid
			$check_double_entry_opdid=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			if($check_double_entry_opdid==0)
			{
				//mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`) VALUES ('$uhid','$opd_id','$date','$time','$usr','1') ");
			}
			
			// appointment_book
			$check_double_entry_appointment=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			$check_double_entry_appointment=0;
			$opd_id="";
			if($check_double_entry_appointment==0)
			{
				//mysqli_query($link, " INSERT INTO `appointment_book`(`patient_id`, `opd_id`, `consultantdoctorid`, `appointment_date`, `appointment_day`, `appointment_no`, `user`, `date`, `time`, `emergency`, `visit_fee`) VALUES ('$uhid','$opd_id','$ref_to','$date','$appoint_day','$appoint_no','$usr','$date','$time','','') ");
				mysqli_query($link,"UPDATE `pat_disposition` SET `ref_opd`='$opd_id' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
			}
			
		}
	}
		
	echo "Saved";
}

if($_POST["type"]=="save_note")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$usr=$_POST['usr'];
	$con_note=$_POST['con_note'];
	$con_note=str_replace("'", "''", "$con_note");
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_consultation` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `pat_consultation` SET `con_note`='$con_note',`date`='$date',`time`='$time' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `pat_consultation`(`patient_id`, `opd_id`, `con_note`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$con_note','$date','$time','$usr')");
	}
	echo "Saved";
}

if($_POST["type"]=="save_exam")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$chief=$_POST['chief'];
	$chief= str_replace("'", "''", "$chief");
	$usr=$_POST['usr'];
	$history=$_POST['history'];
	$history= str_replace("'", "''", "$history");
	$exam=$_POST['exam'];
	$exam= str_replace("'", "''", "$exam");
	//$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `physical_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `pat_examination` SET `history`='$history',`examination`='$exam' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `pat_examination`(`patient_id`, `opd_id`, `history`, `examination`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$history','$exam','$date','$time','$usr')");
	}
	
	$val=explode("#g#",$chief);
	$ar=sizeof($val);
	$nm=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_complaints` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($nm>0)
	mysqli_query($link,"DELETE FROM `pat_complaints` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	for($i=0; $i<$ar-1; $i++)
	{
		$v=explode("@",$val[$i]);
		$c1=$v[0];
		$c1= str_replace("'", "''", "$c1");
		$c2=$v[1];
		$c3=$v[2];
		mysqli_query($link, " INSERT INTO `pat_complaints`(`patient_id`, `opd_id`, `comp_one`, `comp_two`, `comp_three`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$c1','$c2','$c3','$date','$time','$usr') ");
	}
	echo "Saved";
}

if($_POST["type"]=="save_vital")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$weight=$_POST['weight'];
	$height=$_POST['height'];
	$mid_cum=$_POST['mid_cum'];
	$bmi1=$_POST['bmi1'];
	$bmi2=$_POST['bmi2'];
	$hd_cum=$_POST['hd_cum'];
	$pr=$_POST['pr'];
	$rr=$_POST['rr'];
	$spo=$_POST['spo'];
	$pulse=$_POST['pulse'];
	$temp=$_POST['temp'];
	$usr=$_POST['usr'];
	$systolic=$_POST['systolic'];
	$diastolic=$_POST['diastolic'];
	$note=$_POST['vit_note'];
	$note= str_replace("'", "''", "$note");
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `pat_vital` SET `weight`='$weight',`height`='$height',`medium_circumference`='$mid_cum',`BMI_1`='$bmi1',`BMI_2`='$bmi2',`spo2`='$spo',`pulse`='$pulse',`head_circumference`='$hd_cum',`PR`='$pr',`RR`='$rr',`temp`='$temp',`systolic`='$systolic',`diastolic`='$diastolic',`note`='$note',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `pat_vital`(`patient_id`, `opd_id`, `weight`, `height`, `medium_circumference`, `BMI_1`, `BMI_2`, `spo2`, `pulse`, `head_circumference`, `PR`, `RR`, `temp`, `systolic`, `diastolic`, `note`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$weight','$height','$mid_cum','$bmi1','$bmi2','$spo','$pulse','$hd_cum','$pr','$rr','$temp','$systolic','$diastolic','$note','$date','$time','$usr')");
	}
	echo "Saved";
}

if($_POST["type"]=="select_test")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$test=$_POST['test'];
	$rate=$_POST['rate'];
	$usr=$_POST['usr'];
	$bch=1;
	if($test>0)
	{
		$opd_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `opdid_link_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' "));
		if($opd_num==0)
		{
			mysqli_query($link," INSERT INTO `opdid_link_opdid`(`patient_id`, `opd_id`, `new_opd_id`) VALUES ('$uhid','$opd','') ");
		}
		$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$test'"));
		if($num==0)
		{
			// Sample ID
			$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test' "));
			// Insert
			mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `user`, `date`, `time`,`type`) VALUES ('$uhid','$opd','','$bch','$test','$smpl[SampleId]','$rate','$usr','$date','$time','1')");
		}
	}
}

if($_POST["type"]=="insert_medi")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$medi=$_POST['medi'];
	$dos=$_POST['dos'];
	$dos=str_replace("'", "''", "$dos");
	$ph_quantity=$_POST['ph_quantity'];
	if(!$ph_quantity)
	{
		$ph_quantity=0;
	}
	$inst=$_POST['inst'];
	$usr=$_POST['usr'];
	
	$type=1; // OPD Doctor
	
	if($medi!="" && $dos!="")
	{
		$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$opd' AND `item_code`='$medi'"));
		$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `dosage_master` WHERE `dosage`='$dos'"));
		if($nn==0)
		{
			mysqli_query($link,"INSERT INTO `dosage_master`(`dosage`) VALUES ('$dos')");
		}
		if($num>0)
		{
			mysqli_query($link,"UPDATE `patient_medicine_detail` SET `dosage`='$dos',`instruction`='$inst',`quantity`='$ph_quantity',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `pin`='$opd' AND `item_code`='$medi'");
		}
		else
		{
			mysqli_query($link," INSERT INTO `patient_medicine_detail`(`patient_id`, `pin`, `indent_num`, `item_code`, `dosage`, `instruction`, `quantity`, `status`, `date`, `time`, `user`, `type`, `bill_no`) VALUES ('$uhid','$opd','1','$medi','$dos','$inst','$ph_quantity','0','$date','$time','$usr','$type','') ");
		}
	}
}

if($_POST["type"]=="save_test_master")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	$rate=$_POST['rate'];
	$rate= str_replace("'", "''", "$rate");
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `testmaster` WHERE `testid`='$id'"));
	if($n>0)
	{
		if(mysqli_query($link,"UPDATE `testmaster` SET `testname`='$name', `rate`='$rate' WHERE `testid`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `testmaster`(`testid`,`testname`,`rate`) VALUES ('$id','$name','$rate')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_medicine_master")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `medicine_master` WHERE `medicine_id`='$id'"));
	if($n>0)
	{
		if(mysqli_query($link,"UPDATE `medicine_master` SET `medicine_name`='$name' WHERE `medicine_id`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `medicine_master`(`medicine_id`,`medicine_name`) VALUES ('$id','$name')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="saveitemtype")
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
	$csname= str_replace("'", "''", "$csname");
	
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ph_item_type_master` WHERE `item_type_id`='$csid'"));
	if($n>0)
	{
		if(mysqli_query($link,"UPDATE `ph_item_type_master` SET `item_type`='$csname' WHERE `item_type_id`='$csid'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `ph_item_type_master` (`item_type_id`, `item_type`) values('$csid','$csname')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="insert_item_master")
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
	$csname= str_replace("'", "''", "$csname");
	$vtpeye=$_POST['vtpeye'];
	$gen_name=$_POST['gen_name'];
	$vmrp=$_POST['vmrp'];
	$vmrp= str_replace("'", "''", "$vmrp");
	$vstrngth=$_POST['vstrngth'];
	$vstrngth= str_replace("'", "''", "$vstrngth");
	$vvat=$_POST['vvat'];
	$costprice=$_POST['costprice'];
	
	$qrch=mysqli_num_rows(mysqli_query($link,"select * from ph_item_master where item_code='$csid'"));
	$typ=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_item_type_master` WHERE `item_type_id`='$vtpeye'"));
	if($qrch>0)
	{
		if(mysqli_query($link,"update ph_item_master set item_name='$csname',generic='$gen_name',item_type='$typ[item_type]',item_strength='$vstrngth',item_mrp='$vmrp',cost_price='$costprice',vat='$vvat',item_type_id='$vtpeye' where item_code='$csid'"))
		{
			echo "Updated";
		}
	}
	else
	{
	  if(mysqli_query($link,"insert into  ph_item_master values('$csid','$csname',$gen_name,'$typ[item_type]','$vstrngth','$vmrp','$costprice','$vvat','$vtpeye')"))
	  {
		  echo "Saved";
	  }
	}
}

if($_POST["type"]=="purchse_ord_temp")
{
	$ordrdate=$_POST['ordrdate'];
	$supplr=$_POST['supplr'];
	$orderno=$_POST['orderno'];
	$itmcode=$_POST['itmcode'];
	$orqnt=$_POST['orqnt'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ph_purchase_order_details_temp` WHERE `order_no`='$orderno' AND `item_code`='$itmcode'"));
	if($num>0)
	{
		if(mysqli_query($link,"UPDATE `ph_purchase_order_details_temp` SET `order_qnt`='$orqnt',`bl_qnt`='$orqnt' WHERE `order_no`='$orderno' AND `item_code`='$itmcode' AND `SuppCode`='$supplr'"))
		{
			echo "1";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `ph_purchase_order_details_temp`(`order_no`, `item_code`, `SuppCode`, `order_qnt`, `bl_qnt`, `order_date`, `stat`) VALUES ('$orderno','$itmcode','$supplr','$orqnt','$orqnt','$ordrdate','0')"))
		{
			echo "1";
		}
	}
}

if($_POST["type"]=="purchse_ord_final")
{
	$supplr=$_POST['supplr'];
	$orderno=$_POST['orderno'];
	$vdate=$_POST['ordrdate'];
	$fid=$_POST['fid'];
		
	mysqli_query($link,"delete from  ph_purchase_order_details where order_no ='$orderno' and SuppCode='$supplr' ");
	if(mysqli_query($link,"insert into ph_purchase_order_details select * from ph_purchase_order_details_temp where order_no='$orderno' and SuppCode='$supplr'"))
	{
		echo "Saved";
	}
	$qchk=mysqli_num_rows(mysqli_query($link,"select * from ph_purchase_order_details_temp where order_no ='$orderno' and SuppCode='$supplr' "));
	if($qchk>0)
	{
		mysqli_query($link,"delete from ph_purchase_order_master where order_no ='$orderno' and SuppCode='$supplr'  ");
		
		mysqli_query($link,"insert into ph_purchase_order_master values('$orderno','$supplr','$vdate' )");
		
		mysqli_query($link,"delete from ph_purchase_order_details_temp where order_no ='$orderno' and SuppCode='$supplr'  ");
	}
}

if($_POST["type"]=="purchase_rcv_tmp")
{
	$orderno=$_POST['orderno'];
	$itmid=$_POST['itmid'];
	$mnufctr=$_POST['mnufctr'];
	$expiry=$_POST['expiry'];
	$batch=$_POST['batch'];
	$qnt=$_POST['qnt'];
	$bal=$_POST['bal'];
	$freeqnt=$_POST['freeqnt'];
	$mrp=$_POST['mrp'];
	$vcstprice=$_POST['vcstprice'];
	$vdate=date('Y-m-d');
	
	$qrsplr=mysqli_fetch_array(mysqli_query($link,"select SuppCode from ph_purchase_order_master where order_no='$orderno'"));
	mysqli_query($link,"delete from ph_purchase_receipt_temp where order_no='$orderno' and item_code='$itmid' and recept_batch='$batch'");
	mysqli_query($link,"insert into ph_purchase_receipt_temp values('$orderno','$itmid','$mnufctr','$expiry','$vdate','$qnt','$freeqnt','$batch','$qrsplr[SuppCode]','$mrp','$vcstprice')");
	$bl=$bal-$qnt;
	if($bl>0)
	$st=0;
	else
	$st=1;
	mysqli_query($link,"UPDATE `ph_purchase_order_details` SET `bl_qnt`='$bl', `stat`='$st' WHERE order_no='$orderno' and item_code='$itmid'");
}

if($_POST["type"]=="purchase_rcv_final")
{
	$orderno=$_POST['orderno'];
	$splrblno=$_POST['splrblno'];
	$itm=$_POST['itm'];
	$shopcode=0;
	$entrydate=date('Y-m-d');
	
	//mysqli_query($link,"delete from purchase_receipt_details where order_no ='$orderno' and FID='$fd' ");
	mysqli_query($link,"insert into ph_purchase_receipt_details select * from ph_purchase_receipt_temp where order_no ='$orderno'");
	
	$qsuplrcode=mysqli_fetch_array(mysqli_query($link,"select SuppCode from ph_purchase_order_master where order_no='$orderno' "));
	$numdt=mysqli_num_rows(mysqli_query($link,"select * from ph_purchase_order_details where order_no='$orderno'"));
	$numr=mysqli_num_rows(mysqli_query($link,"select * from ph_purchase_order_details where order_no='$orderno' and `stat`=1"));
	if($numdt==$numr)
	{
		//mysqli_query($link,"delete from ph_purchase_receipt_master where order_no ='$orderno' and supp_code='$qsuplrcode[SuppCode]' ");
		//mysqli_query($link,"insert into ph_purchase_receipt_master values('$orderno','$entrydate','$shopcode','$qsuplrcode[SuppCode]','$splrblno')");
	}
	
	//mysqli_query($link,"delete from ph_purchase_receipt_master where order_no ='$orderno' and supp_code='$qsuplrcode[SuppCode]' ");
	mysqli_query($link,"insert into ph_purchase_receipt_master values('$orderno','$entrydate','$shopcode','$qsuplrcode[SuppCode]','$splrblno')");
	
	//////////end mrp///////////////////////////
	mysqli_query($link,"delete from ph_purchase_receipt_temp where order_no ='$orderno'");
	////////////For stock''''''''''''''''''''
	$qrstk=mysqli_query($link,"select * from ph_purchase_receipt_details where order_no ='$orderno' order by item_code");
	while($qrstk1=mysqli_fetch_array($qrstk))
	{

		$vqnt=$qrstk1['recpt_quantity']+$qrstk1['free_qnt'];

		$vstkqnt=0;
		$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$entrydate' and item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[recept_batch]' order by  date "));
		if($qrstkmaster['item_code']!='')
		{
			$vstkqnt=$qrstkmaster['s_remain']+$vqnt;
			$rcvqnt=$qrstkmaster['added']+$vqnt;
			mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',added='$rcvqnt' where  date='$entrydate' and item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[recept_batch]'");
			mysqli_query($link,"delete from ph_stock_master where  item_code='$qrstk1[item_code]' and batch_no='$qrstk1[recept_batch]'");
			mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$qrstk1[item_code]','$qrstk1[recept_batch]','$vstkqnt','$qrstk1[manufactre_date]','$qrstk1[expiry_date]')");
		}
		else///for if data not found
		{
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[recept_batch]' order by slno desc"));
			$vstkqnt=$qrstkmaster['s_remain']+$vqnt;
			mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$qrstk1[order_no]','$qrstk1[item_code]','$qrstk1[recept_batch]','$qrstkmaster[s_remain]','$vqnt',0,'$vstkqnt','$entrydate')");
			mysqli_query($link,"delete from ph_stock_master where item_code='$qrstk1[item_code]' and batch_no='$qrstk1[recept_batch]'");
			mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$qrstk1[item_code]','$qrstk1[recept_batch]','$vstkqnt','$qrstk1[manufactre_date]','$qrstk1[expiry_date]')");
		}
	}
}

if($_POST["type"]=="cust_credit")
{
	$blno=$_POST['blno'];
	$ptype=$_POST['ptype'];
	$amtpaid=val_con($_POST['amtpaid']);
	$amtblnce=val_con($_POST['amtblnce']);
	$txtcrdtamt=val_con($_POST['txtcrdtamt']);
	$pymtdate=$_POST['pymtdate'];
	$chk_no=$_POST['chk_no'];
	$vpaid=0;
	
	mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no) values('$blno','$pymtdate','$txtcrdtamt','$ptype','$chk_no')");
	$qpaid=mysqli_fetch_array(mysqli_query($link,"select paid_amt from ph_sell_master where bill_no='$blno'"));
	$vpaid=$qpaid['paid_amt']+$txtcrdtamt;
	$bal=$amtblnce-$txtcrdtamt;
	mysqli_query($link,"update ph_sell_master set paid_amt='$vpaid',balance='$bal' where bill_no='$blno'");
	echo "Paid";
}

if($_POST["type"]=="itemreturn")
{
	$batch=$_POST['batch'];
	$itmid=$_POST['itmid'];
	$ordrdate=$_POST['ordrdate'];
	$qnt=$_POST['qnt'];
	$prss="RETURN";
	mysqli_query($link,"insert into ph_item_return_master values('$itmid','$batch','$ordrdate','$qnt')");
	//------------------For stock--------------------------//
	 $vstkqnt=0;
	 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$ordrdate' and item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
	 if($qrstkmaster['ItemCode']!='')
	 {
	    $vstkqnt=$qrstkmaster['s_remain']+$qnt;
	    $slqnt=$qrstkmaster['sell']-$qnt;
	    mysqli_query($link,"update ph_stock_process set process_no='$prss',s_remain='$vstkqnt',sell='$slqnt' where  date='$ordrdate' and item_code='$itmid' and  batch_no='$batch'");
	    mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itmid' and batch_no='$batch'");
	 }
	 else///for if data not found
	 {
		 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
		 $vstkqnt=$qrstkmaster['s_remain']+$qnt;
		 mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$prss','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','$vqnt','$vstkqnt','$ordrdate')");
		 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$itmid' and batch_no='$batch'");
	 }
	 echo "Saved";
}

if($_POST["type"]=="saletemp")
{
	$bill=$_POST['billno'];
	$entrydate=$_POST['entrydate'];
	$itmcode=$_POST['itmcode'];
	$batchno=$_POST['batchno'];
	$expiry=$_POST['expiry'];
	$quantity=$_POST['quantity'];
	$mrp=$_POST['rate'];
	
	$vt=mysqli_fetch_array(mysqli_query($link,"select `vat` from `ph_item_master` where `item_code`='$itmcode'"));
	$vttlamt=val_con($quantity*$mrp);
	$tot=$vttlamt+($vttlamt*($vt['vat']/100));
	
	mysqli_query($link,"delete from ph_sell_details_temp where bill_no='$bill' and item_code='$itmcode' and batch_no='$batchno'");
	mysqli_query($link,"insert into ph_sell_details_temp values('$bill','$entrydate','$itmcode','$batchno','$expiry','$quantity','$free','$mrp','$vttlamt','$tot','$vt[vat]')");
 
   $dt=mysqli_fetch_array(mysqli_query($link,"SELECT distinct entry_date FROM ph_sell_details_temp where  bill_no='$bill'  order by entry_date asc "));
   mysqli_query($link,"update ph_sell_details_temp set entry_date='$dt[entry_date]' where  bill_no='$bill' ");
}

if($_POST["type"]=="salefinal")
{
	$blno=$_POST['blno'];
	$entrydate=$_POST['entrydate'];
	$ttlamt=val_con($_POST['net']);
	$discountprcnt=val_con($_POST['discountprcnt']);
	$aftrdscnt=val_con($_POST['aftrdscnt']);
	$paidamont=val_con($_POST['paidamont']);
	$customername=$_POST['customername'];
	$customername= str_replace("'", "''", "$customername");
	$custphone=$_POST['custphone'];
	$disamt=$ttlamt-$aftrdscnt;	
	$balance=val_con(round($_POST['balance']));
	$uhid=$_POST['uhid'];
	$opd_id=$_POST['opd_id'];
	$ipd_id=$_POST['ipd_id'];
	$usr=$_POST['usr'];
	
	$num_r=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$blno'"));
	if($num_r>0)
	{
		$qchkbl=mysqli_fetch_array(mysqli_query($link,"select bill_no,entry_date from ph_sell_master where bill_no='$blno'"));
		$entrydate1=$qchkbl['EntryDate'];
		if($qchkbl)
		{
		//---------------------------for existing bill stock update------------------------------//
			$qry=mysqli_query($link,"select * from ph_sell_details where bill_no='$blno' order by item_code,batch_no");
			while($qrupdt1=mysqli_fetch_array($qry))
			{
				$slqnt=0;
				$slqnt=$qrupdt1['sale_qnt']+$qrupdt1['free_qnt'];					
				$vstkqnt=0;
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$entrydate' and item_code='$qrupdt1[item_code]' and  batch_no='$qrupdt1[batch_no]' order by slno desc limit 0,1"));
				if($qrstkmaster['item_code']!='')
				{
					$vchkqnt=0; 
					$vdif=0;
					$vrcvqnt=0;
					$qntchk=mysqli_fetch_array(mysqli_query($link,"select sale_qnt from ph_sell_details_temp where item_code='$qrupdt1[item_code]' and  batch_no='$qrupdt1[batch_no]'")); 
					$vchkqnt=$qntchk['sale_qnt'];
					if($slqnt>$vchkqnt)
					{
						$vdif=$slqnt-$vchkqnt;
						$vrcvqnt=$qrstkmaster['added']+$vdif;
						$vsel=$qrstkmaster['sell']-$vdif;
					}
					else
					{
						$vsel=$qrstkmaster['sell']+$slqnt;
					}
					$vstkqnt=$qrstkmaster['s_remain']+$slqnt;
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$vsel' where date='$entrydate' and item_code='$qrupdt1[item_code]' and  batch_no='$qrupdt1[batch_no]' and  slno='$qrstkmaster[slno]'");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$qrupdt1[item_code]' and batch_no='$qrupdt1[batch_no]'");
				}
				else
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$qrupdt1[item_code]' and  batch_no='$qrupdt1[batch_no]' order by slno desc limit 0,1"));
					$vstkqnt=$qrstkmaster['s_remain']+$slqnt;

					mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$blno','$qrupdt1[item_code]','$qrupdt1[batch_no]','$qrstkmaster[s_remain]','$slqnt',0,'$vstkqnt','$entrydate')");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$qrupdt1[item_code]' and batch_no='$qrupdt1[batch_no]'");
				}
			}

			//----------------------end---------------------------------------//
			mysqli_query($link,"delete from ph_sell_details where  bill_no='$blno'");
			mysqli_query($link,"insert into ph_sell_details select * from  ph_sell_details_temp where bill_no='$blno' ");
			mysqli_query($link,"delete from ph_sell_master where bill_no='$blno'");
			$qitmchk=mysqli_fetch_array(mysqli_query($link,"select item_code from ph_sell_details where bill_no='$blno'"));
			if($qitmchk['item_code'] !='')
			{
				mysqli_query($link,"INSERT INTO `ph_sell_master`(`bill_no`, `entry_date`, `customer_name`, `customer_phone`, `total_amt`, `discount_perchant`, `discount_amt`, `paid_amt`, `balance`) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$paidamont','$balance')");
				mysqli_query($link,"INSERT INTO `ph_sell_master_edit`(`bill_no`, `entry_date`, `customer_name`, `customer_phone`, `total_amt`, `discount_perchant`, `discount_amt`, `paid_amt`, `balance`) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$paidamont','$balance')"); 
				mysqli_query($link,"delete from ph_payment_details where bill_no='$blno'");
				mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode) values('$blno','$entrydate','$paidamont','Cash')");
			}
			//--------------------------For stock---------------------------//
			$qrstk=mysqli_query($link,"select * from ph_sell_details where bill_no='$blno' order by item_code");
			while($qrstk1=mysqli_fetch_array($qrstk))
			{
				$slqnt=$qrstk1['sale_qnt'];
				$freqnt=$qrstk1['free_qnt'];
				$vqnt=$slqnt+$freqnt;
				$vbtch=$qrstk1['batch_no'];
				$vstkqnt=0;
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$entrydate' and item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' order by slno desc limit 0,1"));
				if($qrstkmaster['item_code']!='')
				{
					$vstkqnt=$qrstkmaster['s_remain']-$vqnt;				 
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$vqnt' where date='$entrydate' and item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' and slno='$qrstkmaster[slno]'");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]'");
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' order by slno desc limit 0,1"));
					$vstkqnt=$qrstkmaster['s_remain']-$vqnt;
					mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$blno','$qrstk1[item_code]','$qrstk1[batch_no]','$qrstkmaster[s_remain]','0','$vqnt','$vstkqnt','$entrydate')");
					mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$qrstk1[item_code]' and  batch_no='$qrstk1[batch_no]' ");
				}
			}
		}
	}
//-------------------------------new entry----------------------------------------------//
	else
	{
		mysqli_query($link,"delete from ph_sell_details where  bill_no='$blno'");
		mysqli_query($link,"insert into ph_sell_details select * from  ph_sell_details_temp where bill_no='$blno'");
		mysqli_query($link,"delete from ph_sell_master where bill_no='$blno'"); 
		mysqli_query($link,"INSERT INTO `ph_sell_master`(`bill_no`, `entry_date`, `customer_name`, `customer_phone`, `total_amt`, `discount_perchant`, `discount_amt`, `paid_amt`, `balance`) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$paidamont','$balance')");
		mysqli_query($link,"INSERT INTO `ph_sell_master_edit`(`bill_no`, `entry_date`, `customer_name`, `customer_phone`, `total_amt`, `discount_perchant`, `discount_amt`, `paid_amt`, `balance`) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$paidamont','$balance')");
		mysqli_query($link,"delete from ph_payment_details where  bill_no='$blno'");
		mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode) values('$blno','$entrydate','$paidamont','Cash')");
		//---------------------------------For stock-------------------------------------//
		$qry=mysqli_query($link,"select * from ph_sell_details where bill_no='$blno' order by item_code");
		while($run=mysqli_fetch_array($qry))
		{
			$slqnt=$run['sale_qnt'];
			$freqnt=$run['free_qnt'];
			$vqnt=$slqnt+$freqnt;
			$vbtch=$run['batch_no'];

			$vstkqnt=0;
			$num=mysqli_num_rows(mysqli_query($link,"select * from ph_stock_process where  date='$entrydate' and item_code='$run[item_code]' and  batch_no='$run[batch_no]'"));
			$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$entrydate' and item_code='$run[item_code]' and  batch_no='$run[batch_no]' order by slno desc limit 0,1"));
			if($num>0)
			{
				$vstkqnt=$stk['s_remain']-$vqnt;
				$slqnt=$stk['sell']+$vqnt;
				mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$entrydate' and item_code='$run[item_code]' and  batch_no='$run[batch_no]' and slno='$stk[slno]'");
				mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$run[item_code]' and  batch_no='$run[batch_no]' ");
			}
			else // for if data not found
			{
				$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$run[item_code]' and  batch_no='$run[batch_no]' order by slno desc limit 0,1"));
				$vstkqnt=$stk['s_remain']-$vqnt;
				mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$blno','$run[item_code]','$run[batch_no]','$stk[s_remain]','0','$vqnt','$vstkqnt','$entrydate')");
				mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$run[item_code]' and batch_no='$run[batch_no]'");
			}
		}
		mysqli_query($link,"delete from ph_sell_details_temp where bill_no='$blno' ");
	}
//-----------------if patient's uhid exists---------------------------//
	if($uhid>0)
	{
		mysqli_query($link,"INSERT INTO `patient_bill_record`(`patient_id`, `opd_id`, `ipd_id`, `bill_no`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$ipd_id','$blno','$entrydate','$time','$usr')");
	}
//--------------------------------------------------------------------//
}

if($_POST["type"]=="insert_supplier")
{
	$id=$_POST['id'];
	$sname=$_POST['sname'];
	$sname= str_replace("'", "''", "$sname");
	$addr=$_POST['addr'];
	$addr= str_replace("'", "''", "$addr");
	$contact=$_POST['contact'];
	$email=$_POST['email'];
	$email= str_replace("'", "''", "$email");
	$gstno=$_POST['gstno'];
	$dlno=$_POST['dlno'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ph_supplier_master` WHERE `id`='$id'"));
	if($num>0)
	{
		if(mysqli_query($link,"UPDATE `ph_supplier_master` SET `name`='$sname',`address`='$addr',`phone_no`='$contact',`email_add`='$email',gst_no='$gstno',dl_no='$dlno' WHERE `id`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `ph_supplier_master`(`id`, `name`, `address`, `phone_no`, `email_add`,gst_no,dl_no) VALUES ('$id','$sname','$addr','$contact','$email','$gstno','$dlno')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_stock_entry")
{
	$itmid=$_POST['itmid'];
	$expiry=$_POST['expiry'];
	$batch=$_POST['batch'];
	$qnt=$_POST['qnt'];
	$mrp=$_POST['mrp'];
	$cost=$_POST['cost'];
	$gst=$_POST['gst'];
	$saleprice=$_POST['saleprice'];
	$vdate=date('Y-m-d');
	$blno="STOCK";
	
	mysqli_query($link,"INSERT INTO `ph_item_stock_entry`(`item_code`, `batch_no`, `entry_date`, `return_qnt`, `item_mrp`, `item_cost_price`, `expiry_date`) VALUES ('$itmid','$batch','$vdate','$qnt','$mrp','$cost','$expiry')");
	mysqli_query($link,"insert into ph_purchase_receipt_details values('RCV1','1','$itmid','','$expiry','$vdate','$qnt',0,'$batch','','$mrp','$cost','$saleprice',0,0,0,0,'$gst',0)");
     //----------------------------------For stock---------------------------------//
	 $vstkqnt=0;
	 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$vdate' and item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
	 if($qrstkmaster['ItemCode']!='')
	 {
	    $vstkqnt=$qrstkmaster['s_remain']+$qnt;
	    $slqnt=$qrstkmaster['added']+$qnt;
	    mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',added='$slqnt' where  date='$vdate' and item_code='$itmid' and  batch_no='$batch'");
	    mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itmid' and batch_no='$batch'");
			
	 }
	 else///for if data not found
	 {
		 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
		 $vstkqnt=$qrstkmaster['s_remain']+$qnt;
		 mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$blno','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','$vqnt','$vstkqnt','$vdate')");
		 $ichk=mysqli_fetch_array(mysqli_query($link,"select item_code from ph_stock_master where item_code='$itmid' and batch_no='$batch'"));
		 if($ichk['item_code']!='')
		 {
		      mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itmid' and batch_no='$batch'");
		 }
		 else
		 {
			 mysqli_query($link,"insert into ph_stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$itmid','$batch','$vstkqnt','','$expiry')");
		 }
	 }
	 echo "Saved";
}


if($_POST["type"]=="save_ind_type")
{
	$sl=$_POST['sl'];
	$id=$_POST['id'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	if($sl>0)
	{
		mysqli_query($link,"UPDATE `inv_indent_type` SET `name`='$name' WHERE `sl_no`='$sl'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `inv_indent_type`(`type_id`, `name`) VALUES ('$id','$name')");
		echo 'Saved';
	}
}

if($_POST["type"]=="save_indent_master")
{
	$id=$_POST['id'];
	$ind_type=$_POST['ind_type'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	$vat=$_POST['vat'];
	$sp_type=$_POST['sp_type'];
	if($id>0)
	{
		mysqli_query($link,"UPDATE `inv_indent_master` SET `indent_type`='$ind_type',`name`='$name',`vat`='$vat',`specific_type`='$sp_type' WHERE `id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `inv_indent_master`(`indent_type`, `name`, `vat`, `specific_type`) VALUES ('$ind_type','$name','$vat','$sp_type')");
		echo "Saved";
	}
}


if($_POST["type"]=="save_inv_supp_rate")
{
	$ind_supp=$_POST['ind_supp'];
	$iid=$_POST['iid'];
	$rate=$_POST['rate'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `inv_supplier_rate` WHERE `supplier_id`='$ind_supp' AND `indent_id`='$iid'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `inv_supplier_rate` SET `rate`='$rate' WHERE `supplier_id`='$ind_supp' AND `indent_id`='$iid'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `inv_supplier_rate`(`supplier_id`, `indent_id`, `rate`) VALUES ('$ind_supp','$iid','$rate')");
	}
}

if($_POST["type"]=="save_ipd_pat_diag_nurse")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$diag=$_POST['diag'];
	$diag= str_replace("'", "''", "$diag");
	$usr=$_POST['usr'];
	$val=explode("#g#",$diag);
	$ar=sizeof($val);
	for($i=0; $i<$ar-1; $i++)
	{
		$v=explode("@",$val[$i]);
		$c1=$v[0];
		$c1= str_replace("'", "''", "$c1");
		$c2=$v[1];
		$c3=$v[2];
		$c4=$v[3];
		mysqli_query($link, " INSERT INTO `ipd_pat_diagnosis`(`patient_id`, `ipd_id`, `diagnosis`, `order`, `certainity`, `consultantdoctorid`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$c1','$c2','$c3','$c4','$date','$time','$usr') ");
	}
	if($diag)
	echo "Saved";
	else
	echo "Empty value cannot saved";
}

if($_POST["type"]=="save_ipd_pat_test")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	$consultantdoctorid=0;
	$refbydoctorid=$_POST['refbydoctorid'];
	$usr=$_POST['usr'];
	$tst=$_POST['tst'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	$center_no=$pat_reg["center_no"];
	
	$test=explode(",",$tst);
	$ar=sizeof($test);
	if($batch>0)
	{
		$bch=$batch;
	}
	else
	{
		$bt=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(batch_no) as max FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$bch=$bt['max']+1;
	}
	
	$test_entry_date=$date;
	$test_entry_time=$time;
	
	// Ref Doctor
	$ref_doc_val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' "));
	if($ref_doc_val)
	{
		if($ref_doc_val["refbydoctorid"]!=$refbydoctorid)
		{
			mysqli_query($link," UPDATE `ipd_test_ref_doc` SET `refbydoctorid`='$refbydoctorid' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' ");
		}
	}else
	{
		mysqli_query($link," DELETE FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' ");
		
		mysqli_query($link," INSERT INTO `ipd_test_ref_doc`(`patient_id`, `ipd_id`, `batch_no`, `consultantdoctorid`, `refbydoctorid`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$consultantdoctorid','$refbydoctorid','$date','$time','$usr') ");
	}
	
	$del_test_qry=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' ");
	while($del_test=mysqli_fetch_array($del_test_qry))
	{
		$test_entry_date=$del_test["date"];
		$test_entry_time=$del_test["time"];
		
		$slno_service=mysqli_fetch_array(mysqli_query($link," SELECT `service_slno` FROM `link_test_service` WHERE `test_slno`='$del_test[slno]' "));
		
		mysqli_query($link," DELETE FROM `patient_test_details` WHERE `slno`='$del_test[slno]'");
		mysqli_query($link," DELETE FROM `ipd_pat_service_details` WHERE `test_slno`='$del_test[slno]'");
		mysqli_query($link," DELETE FROM `link_test_service` WHERE `slno_service`='$slno_service[service_slno]'");
	}
	
	//mysqli_query($link," DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch'");
	foreach($test as $test)
	{
		if($test)
		{
			$sam=mysqli_fetch_array(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$test'"));
			$rt=mysqli_fetch_array(mysqli_query($link,"SELECT `rate`,`category_id` FROM `testmaster` WHERE `testid`='$test'"));
			
			$group_id=104;
			if($rt["category_id"]==2)
			{
				$group_id=151;
			}
			if($rt["category_id"]==3)
			{
				$group_id=150;
			}
			
			$centre_rate=mysqli_fetch_array(mysqli_query($link,"SELECT a.`testname`,a.`rate` AS `m_rate`,b.`rate` AS `c_rate` FROM `testmaster` a, `testmaster_rate` b WHERE a.`testid`=b.`testid` AND b.`centreno`='$center_no' AND b.`testid`='$test'"));
			if($centre_rate)
			{
				$ser_name=$centre_rate['testname'];
				$ser_rate=$centre_rate['c_rate'];
			}
			else
			{
				$det=mysqli_fetch_array(mysqli_query($link,"SELECT `testname`,`rate` FROM `testmaster` WHERE `testid`='$test'"));
				$ser_name=$det['testname'];
				$ser_rate=$det['rate'];
			}
			
			$sample_id=$sam["SampleId"];
			if(!$sample_id){ $sample_id=0; }
			
			if(mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$bch','$test','$sample_id','$ser_rate','0','$test_entry_date','$test_entry_time','$usr','4')")) // 4 = nursing dashboard
			{
				$last_slno_test=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `testid`='$test' AND `user`='$usr' ORDER BY `slno` DESC "));
				
				$test_name=mysqli_fetch_array(mysqli_query($link," SELECT `testname` FROM `testmaster` WHERE `testid`='$test' "));
				
				mysqli_query($link," INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$uhid','$ipd','$group_id','$test','$ser_name','1','$ser_rate','$ser_rate','0','$usr','$test_entry_time','$test_entry_date','0','$consultantdoctorid','$refbydoctorid','$last_slno_test[slno]') ");
				
				$last_slno_service=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='104' AND `service_id`='$test' ORDER BY `slno` DESC "));
				
				mysqli_query($link," INSERT INTO `link_test_service`(`test_slno`, `service_slno`) VALUES ('$last_slno_test[slno]','$last_slno_service[slno]') ");
				
				// Add On Test
				$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test' ");
				while($s_t=mysqli_fetch_array($sub_tst))
				{
					$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
					
					$sample_id=$samp_sb["SampleId"];
					if(!$sample_id){ $sample_id=0; }
					
					mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$bch','$s_t[sub_testid]','$sample_id','0','0','$date','$time','$user','4') ");
				}
			}
			
		}
	}
}


if($_POST["type"]=="ipd_pat_notes")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$test_id=$_POST['test_id'];
	$batch=$_POST['batch'];
	$note=$_POST['note'];
	$note= str_replace("'", "''", "$note");
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `sample_note` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `test_id`='$test_id' AND `user`='$usr'"));
	if($n>0)
	{
		mysqli_query($link,"UPDATE `sample_note` SET `note`='$note' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `test_id`='$test_id' AND `user`='$usr'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `sample_note`(`patient_id`, `ipd_id`, `batch_no`, `test_id`, `note`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$batch','$test_id','$note','$usr','$date','$time')");
		echo "Saved";
	}
}

if($_POST["type"]=="phlebo_save_sample_ipd")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$all=$_POST['all'];
	$batch=$_POST['batch'];
	$usr=$_POST['usr'];
	
	mysqli_query($link, "delete from phlebo_sample where patient_id='$uhid' and ipd_id='$ipd' and batch_no='$batch'");

	$all=explode("#",$all);
	foreach($all as $al)
	{
		if($al)
		{
			$al=explode("$",$al);
			$samp=$al[0];
			mysqli_query($link, "delete from phlebo_sample where patient_id='$uhid' and ipd_id='$ipd' and sampleid='$samp' and batch_no='$batch'");
			$tst=explode("@",$al[1]);
			foreach($tst as $t)
			{
				if($t)
				{
					mysqli_query($link, "insert into phlebo_sample(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sampleid`, `user`, `time`, `date`) values('$uhid','','$ipd','$batch','$t','$samp','$usr','$time','$date')");
				}
			}
			
		}
	}
}

if($_POST["type"]=="insert_medi_ipd")
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
				mysqli_query($link,"INSERT INTO `ipd_pat_medicine`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `dosage`, `units`, `frequency`, `start_date`, `end_date`, `duration`, `unit_days`, `total_drugs`, `instruction`, `sos`, `consultantdoctorid`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$medi','$dos','$unit','$freq','$st_date','$ed','$dur','$unit_day','$tot','$inst','$sos','$con_doc','$date','$time','$usr')");
				for($ii=0;$ii<$dd;$ii++)
				{
					$fdt=date('Y-m-d', strtotime($st_date . ' +'.$ii.' days'));
					mysqli_query($link,"INSERT INTO `ipd_pat_medicine_details`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `drugs`, `dosage_date`, `dosage_given`, `plan`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$medi','$drgs','$fdt','0','$plan','$date','$time','$usr')");
				}
			}
		}
	}
}

if($_POST["type"]=="ipd_pat_medi_given")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$id=$_POST['id'];
	$sl=$_POST['sl'];
	$stat=$_POST['stat'];
	$usr=$_POST['usr'];
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_details` WHERE `id`='$id'"));
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `dosage` FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$f[batch_no]' AND `item_code`='$f[item_code]'"));
	if($stat!=0)
	{
		$m=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_details` WHERE `id`='$id'"));
		if($m['drug']!=$m['dosage_given'])
		{
			mysqli_query($link,"INSERT INTO `ipd_pat_medicine_given`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `serial_num`, `status`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$f[batch_no]','$f[item_code]','$sl','$stat','$date','$time','$usr')");
			if($stat==1)
			$dos=$d['dosage'];
			if($stat==2)
			$dos=0;
			if($stat==3)
			$dos=0;
			$dos=$dos+$m['dosage_given'];
			mysqli_query($link,"UPDATE `ipd_pat_medicine_details` SET `dosage_given`='$dos' WHERE `id`='$id'");
			$msg="Saved";
		}
	}
}

if($_POST["type"]=="pat_ipd_med_plan_update")
{
	$medi=$_POST['medi'];
	$freq=$_POST['freq'];
	$st_date=$_POST['st_date'];
	$dur=$_POST['dur'];
	$unit_day=$_POST['unit_day'];
	$dose=$_POST['dose'];
	$inst=$_POST['inst'];
	$con_doc=$_POST['con_doc'];
	$id=$_POST['id'];
	$usr=$_POST['usr'];
	
	if($freq=="1")
	$tot=1;
	if($freq=="2")
	$tot=1;
	if($freq=="3")
	$tot=2;
	if($freq=="4")
	$tot=3;
	if($freq=="5")
	$tot=4;
	if($freq=="6")
	$tot=5;
	if($freq=="7")
	$tot=24;
	if($freq=="8")
	$tot=12;
	if($freq=="9")
	$tot=8;
	if($freq=="10")
	$tot=6;
	if($freq=="11")
	$tot=5;
	if($freq=="12")
	$tot=4;
	if($freq=="13")
	$tot=3;
	if($freq=="14")
	$tot=3;
	if($freq=="15")
	$tot=2;
	if($freq=="16")
	$tot=2;
	
	if($unit_day=="Days")
	{
		$total=$dose*$tot*$dur;
		$dd=$dur*1;
	}
	if($unit_day=="Weeks")
	{
		$total=$dose*$tot*$dur*7;
		$dd=$dur*7;
	}
	if($unit_day=="Months")
	{
		$total=$dose*$tot*$dur*30;
		$dd=$dur*30;
	}
	$drug=$dose*$tot;
	if($dur==1)
	{
		$ed=$st_date;
	}
	else
	{
		for($jj=1;$jj<$dd;$jj++)
		$ed=date('Y-m-d', strtotime($st_date . ' +'.$jj.' days'));
	}
	if($dose && $freq && $st_date && $dur && $inst)
	{
		mysqli_query($link,"UPDATE `ipd_pat_medicine` SET `item_code`='$medi', `dosage`='$dose', `frequency`='$freq', `start_date`='$st_date', `end_date`='$ed', `total_drugs`='$total', `duration`='$dur', `unit_days`='$unit_day', `instruction`='$inst' WHERE `id`='$id'");
		$f=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine` WHERE `id`='$id'"));
		
		mysqli_query($link,"DELETE FROM `ipd_pat_medicine_details` WHERE `patient_id`='$f[patient_id]' AND `ipd_id`='$f[ipd_id]' AND `batch_no`='$f[batch_no]' AND `item_code`='$medi'");
		for($ii=0;$ii<$dd;$ii++)
		{
			$fdt=date('Y-m-d', strtotime($st_date . ' +'.$ii.' days'));
			mysqli_query($link,"INSERT INTO `ipd_pat_medicine_details`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `drugs`, `dosage_date`, `dosage_given`, `plan`, `date`, `time`, `user`) VALUES ('$f[patient_id]','$f[ipd_id]','$f[batch_no]','$medi','$drug','$fdt','0','0','$date','$time','$usr')");
		}
	}
}

if($_POST["type"]=="pat_ipd_vital_save")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$weight=$_POST['weight'];
	$height=$_POST['height'];
	$mid_cum=$_POST['mid_cum'];
	$hd_cum=$_POST['hd_cum'];
	$bmi1=$_POST['bmi1'];
	$bmi2=$_POST['bmi2'];
	$spo=$_POST['spo'];
	$pulse=$_POST['pulse'];
	$temp=$_POST['temp'];
	$pr=$_POST['pr'];
	$rr=$_POST['rr'];
	$systolic=$_POST['systolic'];
	$diastolic=$_POST['diastolic'];
	$vit_note=$_POST['vit_note'];
	$vit_note=str_replace("'", "''", "$vit_note");
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_vital` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ipd_pat_vital` SET `weight`='$weight',`height`='$height',`medium_circumference`='$mid_cum',`BMI_1`='$bmi1',`BMI_2`='$bmi2',`spo2`='$spo',`pulse`='$pulse',`head_circumference`='$hd_cum',`PR`='$pr',`RR`='$rr',`temp`='$temp',`systolic`='$systolic',`diastolic`='$diastolic',`note`='$vit_note',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_vital`(`patient_id`, `ipd_id`, `weight`, `height`, `medium_circumference`, `BMI_1`, `BMI_2`, `spo2`, `pulse`, `head_circumference`, `PR`, `RR`, `temp`, `systolic`, `diastolic`, `note`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$weight','$height','$mid_cum','$bmi1','$bmi2','$spo','$pulse','$hd_cum','$pr','$rr','$temp','$systolic','$diastolic','$vit_note','$date','$time','$usr')");
		echo "Saved";
	}
}

if($_POST["type"]=="ipd_save_ip_note")
{
	$id=$_POST['id'];
	$ip_note=$_POST['ip_note'];
	$ip_note=str_replace("'", "''", "$ip_note");
	$usr=$_POST['usr'];
	mysqli_query($link,"UPDATE `ipd_ip_consultation` SET `note`='$ip_note',`user`='$usr' WHERE `id`='$id'");
}

if($_POST["type"]=="ipd_auto_save_ip_note")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$bed=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid'"));
	if($bed==0)
	{
		$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_ip_consultation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'"));
		if($num==0)
		{
			$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid`,`ipd_visit_fee` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
			mysqli_query($link,"INSERT INTO `ipd_ip_consultation`(`patient_id`, `ipd_id`, `note`, `consultantdoctorid`, `ipd_fees`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','','$doc[consultantdoctorid]','$doc[ipd_visit_fee]','$date','$time','$usr')");
		}
	}
}

if($_POST["type"]=="ipd_pat_save_equip")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$equip=$_POST['equip'];
	$hour=$_POST['hour'];
	$usr=$_POST['usr'];
	mysqli_query($link,"INSERT INTO `ipd_pat_equipment`(`patient_id`, `ipd_id`, `equipment_id`, `hours`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$equip','$hour','$date','$time','$usr')");
}

if($_POST["type"]=="ipd_pat_save_consumable")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$consume=$_POST['consume'];
	$consume_qnt=$_POST['consume_qnt'];
	$usr=$_POST['usr'];
	mysqli_query($link,"INSERT INTO `ipd_pat_consumable`(`patient_id`, `ipd_id`, `consumable_id`, `quantity`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$consume','$consume_qnt','$date','$time','$usr')");
}
if($_POST["type"]=="ipd_pat_save_sur_consumable")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$consume=$_POST['consume'];
	$consume_qnt=$_POST['consume_qnt'];
	$usr=$_POST['usr'];
	mysqli_query($link,"INSERT INTO `ipd_pat_sur_consumable`(`patient_id`, `ipd_id`, `consumable_id`, `quantity`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$consume','$consume_qnt','$date','$time','$usr')");
}
if($_POST["type"]=="pat_ipd_med_emer_set")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$medi=$_POST['medi'];
	$freq=$_POST['freq'];
	$st_date=date('Y-m-d');
	$dur=$_POST['dur'];
	$unit_day=$_POST['unit_day'];
	$inst=$_POST['inst'];
	$dose=$_POST['dose'];
	$sos="";
	$usr=$_POST['usr'];
	$bt=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(batch_no) as max FROM `ipd_pat_medicine` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$bch=$bt['max']+1;
	
	if($freq=="1")
	$tot=1;
	if($freq=="2")
	$tot=1;
	if($freq=="3")
	$tot=2;
	if($freq=="4")
	$tot=3;
	if($freq=="5")
	$tot=4;
	if($freq=="6")
	$tot=5;
	if($freq=="7")
	$tot=24;
	if($freq=="8")
	$tot=12;
	if($freq=="9")
	$tot=8;
	if($freq=="10")
	$tot=6;
	if($freq=="11")
	$tot=5;
	if($freq=="12")
	$tot=4;
	if($freq=="13")
	$tot=3;
	if($freq=="14")
	$tot=3;
	if($freq=="15")
	$tot=2;
	if($freq=="16")
	$tot=2;
	
	if($unit_day=="Days")
	{
		$total=$dose*$tot*$dur;
		$dd=$dur*1;
	}
	if($unit_day=="Weeks")
	{
		$total=$dose*$tot*$dur*7;
		$dd=$dur*7;
	}
	if($unit_day=="Months")
	{
		$total=$dose*$tot*$dur*30;
		$dd=$dur*30;
	}
	$drug=$dose*$tot;
	if($dur==1)
	{
		$ed=$st_date;
	}
	else
	{
		for($jj=1;$jj<$dd;$jj++)
		$ed=date('Y-m-d', strtotime($st_date . ' +'.$jj.' days'));
	}
	if($dose && $freq && $st_date && $dur && $inst)
	{
		$unit=mysqli_fetch_array(mysqli_query($link,"SELECT `item_type` FROM `ph_item_master` WHERE `item_code`='$medi'"));
		$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$con_doc=$doc['attend_doc'];
		mysqli_query($link,"INSERT INTO `ipd_pat_medicine`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `dosage`, `units`, `frequency`, `start_date`, `end_date`, `duration`, `unit_days`, `total_drugs`, `instruction`, `sos`, `consultantdoctorid`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$medi','$dose','$unit[item_type]','$freq','$st_date','$ed','$dur','$unit_day','$total','$inst','$sos','$con_doc','$date','$time','$usr')");
		for($ii=0;$ii<$dd;$ii++)
		{
			$fdt=date('Y-m-d', strtotime($st_date . ' +'.$ii.' days'));
			mysqli_query($link,"INSERT INTO `ipd_pat_medicine_details`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `drugs`, `dosage_date`, `dosage_given`, `plan`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$medi','$drug','$fdt','0','1','$date','$time','$usr')");
		}
	}
}

if($_POST["type"]=="ipd_save_note")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ip_note=$_POST['ip_note'];
	$ip_note=str_replace("'", "''", "$ip_note");
	$con_doc=$_POST['con_doc'];
	$usr=$_POST['usr'];
	if($con_doc)
	{
		$fee=mysqli_fetch_array(mysqli_query($link,"SELECT `ipd_visit_fee` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_doc'"));
		mysqli_query($link,"INSERT INTO `ipd_ip_consultation`(`patient_id`, `ipd_id`, `note`, `consultantdoctorid`, `ipd_fees`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$ip_note','$con_doc','$fee[ipd_visit_fee]','$date','$time','$usr')");
	}
}

if($_POST["type"]=="ipd_pat_insert_complain")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$det=$_POST['det'];
	$det=str_replace("'", "''", "$det");
	$usr=$_POST['usr'];
	$d=explode("#govin#",$det);
	//mysqli_query($link,"DELETE FROM `ipd_pat_complaints` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	foreach($d as $ds)
	{
		$val=explode("@",$ds);
		$comp=$val[0];
		$comp=str_replace("'", "''", "$comp");
		$d1=$val[1];
		$d2=$val[2];
		if($comp && $d1 && $d2)
		mysqli_query($link,"INSERT INTO `ipd_pat_complaints`(`patient_id`, `ipd_id`, `comp_one`, `comp_two`, `comp_three`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$comp','$d1','$d2','$date','$time','$usr')");
	}
	
}

if($_POST["type"]=="ipd_pat_update_hist")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$p_hist=$_POST['p_hist'];
	$p_hist=str_replace("'", "''", "$p_hist");
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `pat_examination` SET `history`='$p_hist' WHERE `patient_id`='$uhid'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `pat_examination`(`patient_id`, `opd_id`, `ipd_id`, `history`, `examination`, `date`, `time`, `user`) VALUES ('$uhid','','$ipd','$p_hist','','$date','$time','$usr')");
		echo "Saved";
	}
}

if($_POST["type"]=="ipd_pat_examination")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$exam=$_POST['exam'];
	$exam=str_replace("'", "''", "$exam");
	$usr=$_POST['usr'];
	mysqli_query($link,"INSERT INTO `ipd_pat_examination`(`patient_id`, `ipd_id`, `examination`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$exam','$date','$time','$usr')");
	echo "Saved";
}

if($_POST["type"]=="ipd_pat_save_disc_summary")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$course=$_POST['course'];
	$course=str_replace("'", "''", "$course");
	$final_diag=$_POST['final_diag'];
	$final_diag=str_replace("'", "''", "$final_diag");
	$foll=$_POST['foll'];
	$foll=str_replace("'", "''", "$foll");
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ipd_pat_discharge_summary` SET `course`='$course',`final_diagnosis`='$final_diag',`follow_up`='$foll',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_discharge_summary`(`patient_id`, `ipd_id`, `course`, `final_diagnosis`, `follow_up`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$course','$final_diag','$foll','$date','$time','$usr')");
		echo "Saved";
	}
}

if($_POST["type"]=="insert_medi_ipd_post")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$det=$_POST['det'];
	$det= str_replace("'", "''", "$det");
	$usr=$_POST['usr'];
	
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
				mysqli_query($link,"INSERT INTO `ipd_pat_medicine_post_discharge`(`patient_id`, `ipd_id`, `item_code`, `dosage`, `units`, `frequency`, `start_date`, `end_date`, `duration`, `unit_days`, `total_drugs`, `instruction`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$medi','$dos','$unit','$freq','$st_date','$ed','$dur','$unit_day','$tot','$inst','$date','$time','$usr')");
				/*for($ii=0;$ii<$dd;$ii++)
				{
					$fdt=date('Y-m-d', strtotime($st_date . ' +'.$ii.' days'));
					//mysqli_query($link,"INSERT INTO `ipd_pat_medicine_details`(`patient_id`, `ipd_id`, `batch_no`, `item_code`, `drugs`, `dosage_date`, `dosage_given`, `plan`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$medi','$drgs','$fdt','0','$plan','$date','$time','$usr')");
				}*/
			}
		}
	}
}

if($_POST["type"]=="save_donor_type")
{
	$did=$_POST['did'];
	$dname=$_POST['dname'];
	$dname= str_replace("'", "''", "$dname");
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_donor_type` WHERE `type_id`='$did'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `blood_donor_type` SET `name`='$dname' WHERE `type_id`='$did'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `blood_donor_type`(`type_id`, `name`) VALUES ('$did','$dname')");
		echo "Saved";
	}
}

if($_POST["type"]=="save_donor_reg")
{
	$did=$_POST['did'];
	$dtype=$_POST['dtype'];
	$dname=$_POST['dname'];
	$weight=$_POST['weight'];
	$age=$_POST['age'];
	$sex=$_POST['sex'];
	$contact=$_POST['contact'];
	$abo=$_POST['abo'];
	$rh=$_POST['rh'];
	$ldt=$_POST['ldt'];
	$remark=$_POST['remark'];
	$usr=$_POST['usr'];
	if($did>0)
	{
		mysqli_query($link,"UPDATE `blood_donor_reg` SET `type_id`='$dtype',`name`='$dname',`weight`='$weight',`age`='$age',`sex`='$sex',`contact`='$contact',`abo`='$abo',`rh`='$rh',`last_donate`='$ldt',`remarks`='$remark',`date`='$date',`time`='$time',`user`='$usr' WHERE `donor_id`='$did'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `blood_donor_reg`(`type_id`, `name`, `weight`, `age`, `sex`, `contact`, `abo`, `rh`, `last_donate`, `remarks`, `date`, `time`, `user`) VALUES ('$dtype','$dname','$weight','$age','$sex','$contact','$abo','$rh','$ldt','$remark','$date','$time','$usr')");
		echo "Saved";
	}
}

if($_POST["type"]=="save_blood_pack_master")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$usr=$_POST['usr'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_pack_master` WHERE `pack_id`='$id'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `blood_pack_master` SET `name`='$name' WHERE `pack_id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `blood_pack_master`(`name`) VALUES ('$name')");
		echo "Saved";
	}
}

if($_POST["type"]=="blood_receipt_save")
{
	$donor=$_POST['donor'];
	$bag=$_POST['bag'];
	$vol=$_POST['vol'];
	$bar=$_POST['bar'];
	$usr=$_POST['usr'];
	mysqli_query($link,"INSERT INTO `blood_receipt`(`donor_id`, `pack_id`, `volume`, `bar_code`, `status`, `date`, `time`, `user`) VALUES ('$donor','$bag','$vol','$bar','0','$date','$time','$usr')");
	mysqli_query($link,"UPDATE `blood_donor_reg` SET `last_donate`='$date' WHERE `donor_id`='$donor'");
	echo "Saved";
}

if($_POST["type"]=="save_donor_screw")
{
	$did=$_POST['did'];
	$bar=$_POST['bar'];
	$abo=$_POST['abo'];
	$rh=$_POST['rh'];
	$hiv=$_POST['hiv'];
	$hepb=$_POST['hepb'];
	$hepc=$_POST['hepc'];
	$mp=$_POST['mp'];
	$vdrl=$_POST['vdrl'];
	$usr=$_POST['usr'];
	
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_screwing_details` WHERE `donor_id`='$did' AND `bar_code`='$bar'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `blood_screwing_details` SET `abo`='$abo',`rh`='$rh',`hiv`='$hiv',`hep_b`='$hepb',`hep_c`='$hepc',`mp`='$mp',`vdrl`='$vdrl',`date`='$date',`time`='$time',`user`='$usr' WHERE `donor_id`='$did' AND `bar_code`='$bar'");
		mysqli_query($link,"UPDATE `blood_donor_reg` SET `abo`='$abo',`rh`='$rh' WHERE `donor_id`='$did'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `blood_screwing_details`(`donor_id`, `bar_code`, `abo`, `rh`, `hiv`, `hep_b`, `hep_c`, `mp`, `vdrl`, `date`, `time`, `user`) VALUES ('$did','$bar','$abo','$rh','$hiv','$hepb','$hepc','$mp','$vdrl','$date','$time','$usr')");
		mysqli_query($link,"UPDATE `blood_donor_reg` SET `abo`='$abo',`rh`='$rh' WHERE `donor_id`='$did'");
		echo "Saved";
	}
	if($hiv=="negative" && $hepb=="negative" && $hepc=="negative" && $mp=="negative" && $vdrl=="negative")
	{
		$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_donor_inventory` WHERE `donor_id`='$did'"));
		if($n==0)
		mysqli_query($link,"INSERT INTO `blood_donor_inventory`(`donor_id`, `abo`, `rh`, `entry_date`) VALUES ('$did','$abo','$rh','$date')");
		mysqli_query($link,"DELETE FROM `blood_donor_rejected` WHERE `donor_id`='$did'");
	}
	else
	{
		$n1=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_donor_inventory` WHERE `donor_id`='$did'"));
		$n2=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_donor_rejected` WHERE `donor_id`='$did'"));
		if($n1>0)
		{
			mysqli_query($link,"DELETE FROM `blood_donor_inventory` WHERE `donor_id`='$did'");
		}
		if($n2==0)
		{
			mysqli_query($link,"INSERT INTO `blood_donor_rejected`(`donor_id`, `abo`, `rh`, `entry_date`) VALUES ('$did','$abo','$rh','$date')");
		}
		mysqli_query($link,"UPDATE `blood_receipt` SET `status`='2' WHERE `donor_id`='$did' AND `bar_code`='$bar'");
	}

}

if($_POST["type"]=="save_component_stock")
{
	$did=$_POST['did'];
	$bar=$_POST['bar'];
	$bagid=$_POST['bagid'];
	$all=$_POST['all'];
	$usr=$_POST['usr'];
	$det=explode("#@#",$all);
	foreach($det as $vl)
	{
		$exdate="";
		$v=explode("@",$vl);
		$id=$v[0];
		$chk=$v[1];
		$value=$v[2];
		if($id=="rbc" && $chk=="1")
		{
			if($bagid==1 || $bagid==2)
			{
				for($jj=1;$jj<=35;$jj++)
				$exdate=date('Y-m-d', strtotime($date . ' +'.$jj.' days'));
			}
			if($bagid==3 || $bagid==4)
			{
				for($jj=1;$jj<=42;$jj++)
				$exdate=date('Y-m-d', strtotime($date . ' +'.$jj.' days'));
			}
		}
		if($id=="ffp" && $chk=="1")
		{
			for($jj=1;$jj<=365;$jj++)
			$exdate=date('Y-m-d', strtotime($date . ' +'.$jj.' days'));
		}
		if($id=="plat" && $chk=="1")
		{
			for($jj=1;$jj<=5;$jj++)
			$exdate=date('Y-m-d', strtotime($date . ' +'.$jj.' days'));
		}
		if($id=="cpp" && $chk=="1" || $id=="cryo" && $chk=="1")
		{
			for($jj=1;$jj<=365;$jj++)
			$exdate=date('Y-m-d', strtotime($date . ' +'.$jj.' days'));
		}
		if($chk)
		{
			mysqli_query($link,"INSERT INTO `blood_component_stock`(`component_id`, `bar_code`, `expiry_date`, `date`, `time`, `user`) VALUES ('$value','$bar','$exdate','$date','$time','$usr')");
			mysqli_query($link,"UPDATE `blood_receipt` SET `status`='1' WHERE `bar_code`='$bar'");
		}
	}
	echo "Saved";
}

if($_POST["type"]=="save_blood_request")
{
	$uhid=$_POST['uhid'];
	$abo=$_POST['abo'];
	$rh=$_POST['rh'];
	$usr=$_POST['usr'];
	$all=$_POST['all'];
	$unit=$_POST['unit'];
	$det=explode("#@#",$all);
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id` FROM `patient_info` WHERE `uhid`='$uhid'"));
	foreach($det as $vl)
	{
		$v=explode("@",$vl);
		$id=$v[0];
		$chk=$v[1];
		$value=$v[2];
		if($chk)
		mysqli_query($link,"INSERT INTO `blood_request`(`patient_id`, `abo`, `rh`, `component_id`, `units`, `issued`, `date`, `time`, `user`) VALUES ('$p[patient_id]','$abo','$rh','$value','$unit','0','$date','$time','$usr')");
	}
	echo "Submitted";
}

if($_POST["type"]=="blood_submit_crossmatch")
{
	$uhid=$_POST['uhid'];
	$bar=$_POST['bar'];
	$cross=$_POST['cross'];
	$agg=$_POST['agg'];
	$usr=$_POST['usr'];
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id` FROM `patient_info` WHERE `uhid`='$uhid'"));
	mysqli_query($link,"INSERT INTO `blood_crossmatch`(`patient_id`, `bar_code`, `crossmatch_type`, `agglutination`, `date`, `time`, `user`) VALUES ('$p[patient_id]','$bar','$cross','$agg','$date','$time','$usr')");
	echo "Saved";
}

if($_POST["type"]=="blood_pat_issue_save")
{
	$uhid=$_POST['uhid'];
	$all=$_POST['all'];
	$usr=$_POST['usr'];
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id` FROM `patient_info` WHERE `uhid`='$uhid'"));
	$det=explode("#",$all);
	foreach($det as $vl)
	{
		$v=explode("@",$vl);
		$comp=$v[0];
		$bar=$v[1];
		$req=$v[2];
		if($comp && $bar)
		{
			mysqli_query($link,"INSERT INTO `blood_issue`(`patient_id`, `component_id`, `request_id`, `bar_code`, `date`, `time`, `user`) VALUES ('$p[patient_id]','$comp','$req','$bar','$date','$time','$usr')");
			$is=mysqli_fetch_array(mysqli_query($link,"SELECT `issued` FROM `blood_request` WHERE `request_id`='$req'"));
			$issue=$is['issued']+1;
			mysqli_query($link,"UPDATE `blood_request` SET `issued`='$issue' WHERE `request_id`='$req'");
			mysqli_query($link,"DELETE FROM `blood_component_stock` WHERE `component_id`='$comp' AND `bar_code`='$bar'");
		}
	}
	echo "Issued";
}

if($_POST["type"]=="save_company_name")
{
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	$bed=$_POST['bed'];
	$addr=$_POST['addr'];
	$city=$_POST['city'];
	$pin=$_POST['pin'];
	$state=$_POST['state'];
	$ph1=$_POST['ph1'];
	$ph2=$_POST['ph2'];
	$ph3=$_POST['ph3'];
	$email=$_POST['email'];
	$web=$_POST['web'];
	$cer=$_POST['cer'];
	$gst=$_POST['gst'];
	$trade_licence=$_POST['trade_licence'];
	$narcotics=$_POST['narcotics'];
	$bmw=$_POST['bmw'];
	$spirit=$_POST['spirit'];
	$mtp=$_POST['mtp'];
	$fire=$_POST['fire'];
	$pharmacy=$_POST['pharmacy'];
	$usr=$_POST['usr'];
	//mysqli_query($link,"DELETE FROM `company_name`");
	mysqli_query($link,"DELETE FROM `company_documents`");
	//mysqli_query($link,"INSERT INTO `company_name`(`name`, `no_of_bed`, `address`, `phone1`, `phone2`, `phone3`, `email`, `website`, `pincode`, `city`, `state`) VALUES ('$name','$bed','$addr','$ph1','$ph2','$ph3','$email','$web','$pin','$city','$state')");
	mysqli_query($link,"INSERT INTO `company_documents`(`cer`, `gst`, `trade_licence`, `narcotics`, `bmw`, `spirit`, `mtp`, `fire`, `pharmacy`, `lab`, `radiology`) VALUES ('$cer','$gst','$trade_licence','$narcotics','$bmw','$spirit','$mtp','$fire','$pharmacy','$lab','$radio')");
	
	mysqli_query($link," UPDATE `company_name` SET `name`='$name',`no_of_bed`='$bed',`address`='$addr',`phone1`='$ph1',`phone2`='$ph2',`phone3`='$ph3',`email`='$email',`website`='$web',`pincode`='$pin',`city`='$city',`state`='$state' ");
	echo "Saved";
}

if($_POST["type"]=="pat_ipd_delivery_save")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$all=mysqli_real_escape_string($link, $_POST['all']);
	$usr=$user=$_POST['usr'];
	$det=explode("##",$all);
	$ar=sizeof(array_filter($det));
	if($ar>1)
	{
		$j=1;
	}
	else
	{
		$j="";
	}
	$pat_name=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	
	$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
	
	$pat_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc`,`admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	$error=0;
	
	foreach($det as $vl)
	{
		$v=explode("@",$vl);
		$dob=$v[0];
		$sex=$v[1];
		//$b_time=$v[2];
		$b_time=date("H:i:s", strtotime($v[2]));
		$wt=$v[3];
		$blood_group=$blood=$v[4];
		$mode=$v[5];
		$conduct=$v[6];
		$tag=$v[7];
		//$bed=$v[6];
		$bed="";
		$father_name=$fat_name=$v[8];
		$dob1=age_calculator($dob);
		$dt=explode(" ",$dob1);
		$age=$dt[0];
		$age_type=$dt[1];
		if($dob && $sex && $b_time && $wt)
		{
			$patient_reg_type=$p_type_id=8;
			//include("patient_id_generator.php");
			
			$pat_name_full=$name="BABY ".$j." OF ".$pat_name['name'];
			$refbydoctorid=$ref=$pat_reg['refbydoctorid'];
			$centre=$pat_reg['center_no'];
			$hguide_id=$pat_reg['hguide_id'];
			$branch_id=$pat_reg['branch_id'];
			
			$gd_name=$pat_name["gd_name"];
			$gd_phone=$phone=$pat_name["phone"];
			$address=mysqli_real_escape_string($link, $pat_name["address"]);
			$email=$pat_name["address"];
			$crno=$ptype=0;
			$credit="";
			
			$pin=$pat_info_rel["pin"];
			$police=$pat_info_rel["police"];
			$state=$pat_info_rel["state"];
			$district=$pat_info_rel["district"];
			$city=mysqli_real_escape_string($link, $pat_info_rel["city"]);
			$fileno="";
			$post_office=mysqli_real_escape_string($link, $pat_info_rel["post_office"]);
			$mother_name=mysqli_real_escape_string($link, $pat_name["name"]);
			$post_office=mysqli_real_escape_string($link, $pat_info_rel["post_office"]);
			
			$marital_status=2;
			$g_relation="Mother";
			$source_id=$pat_other["source_id"];
			$esi_ip_no="";
			$income_id=0;
			
			$patient_id=0;
			include("patient_info_save.php");
			
			if($patient_id!="0")
			{
				if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`) VALUES ('$patient_id',NULL,'$date','$time','$usr','8','$baby_serial','$refbydoctorid','$centre','$hguide_id','$branch_id') "))
				{
					$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `date`='$date' AND `time`='$time' AND `user`='$user' AND `type`='$p_type_id' AND `refbydoctorid`='$refbydoctorid' AND `hguide_id`='$hguide_id' ORDER BY `slno` DESC LIMIT 0,1 "));
	
					$last_row_num=$last_row["slno"];
					
					$patient_reg_type=$p_type_id=8;
					include("opd_id_generator.php");
					
					mysqli_query($link," UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id',`ipd_serial`='$opd_serial' WHERE `slno`='$last_row_num' ");
					
					mysqli_query($link,"INSERT INTO `ipd_pat_delivery_det`(`patient_id`, `ipd_id`, `sex`, `dob`, `born_time`, `weight`, `blood_group`, `bed_id`, `baby_uhid`, `baby_ipd_id`, `father_name`, `delivery_mode`, `conducted_by`, `dead_tag`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$sex','$dob','$b_time','$wt','$blood','$bed','$patient_id','$opd_id','$fat_name','$mode','$conduct','$tag','$date','$time','$usr')");
					if($j)
					$j++;
				}
				else
				{
					$error=1;
				}
			}
			else
			{
				$error=2;
			}
		}
	}
	if($error==0)
	{
		echo "Saved";
	}
	else
	{
		echo "Failed, try again later.".$error;
	}
}
if($_POST["type"]=="pat_ipd_delivery_save_old")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$all=mysqli_real_escape_string($link, $_POST['all']);
	$usr=$user=$_POST['usr'];
	$det=explode("##",$all);
	$ar=sizeof(array_filter($det));
	if($ar>1)
	{
		$j=1;
	}
	else
	{
		$j="";
	}
	$pat_name=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc`,`admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	foreach($det as $vl)
	{
		$v=explode("@",$vl);
		$dob=$v[0];
		$sex=$v[1];
		//$b_time=$v[2];
		$b_time=date("H:i:s", strtotime($v[2]));
		$wt=$v[3];
		$blood=$v[4];
		$mode=$v[5];
		$conduct=$v[6];
		$tag=$v[7];
		//$bed=$v[6];
		$bed="";
		$fat_name=$v[8];
		$dob1=age_calculator($dob);
		$dt=explode(" ",$dob1);
		$age=$dt[0];
		$age_type=$dt[1];
		if($dob && $sex && $b_time && $wt)
		{
			$patient_reg_type=8;
			include("patient_id_generator.php");
			
			$name="BABY ".$j." OF ".$pat_name['name'];
			$ref=$pat_reg['refbydoctorid'];
			$centre=$pat_reg['center_no'];
			
			$check_double_entry=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$new_patient_id' "));
			if($check_double_entry==0)
			{
				mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`,`uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$new_patient_id','$uhid_serial','$name','$pat_name[gd_name]','$sex','$dob','$age','$age_type','$pat_name[phone]','$pat_name[address]','$pat_name[email]','$r_doc','','$user','0','$blood','$date','$time') ");
			}
			else
			{
				$patient_reg_type=8;
				include("patient_id_generator.php");
				
				$name="BABY ".$j." OF ".$pat_name['name'];
				$ref=$pat_reg['refbydoctorid'];
				$centre=$pat_reg['center_no'];
				
				mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`,`uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$new_patient_id','$uhid_serial','$name','$pat_name[gd_name]','$sex','$dob','$age','$age_type','$pat_name[phone]','$pat_name[address]','$pat_name[email]','$r_doc','','$user','0','$blood','$date','$time') ");
			}
			
			// Baby Serial
			$ipd_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `baby_serial_generator` "));
			if(!$ipd_data)
			{
				//mysqli_query($link, " TRUNCATE TABLE `baby_serial_generator` ");
			}

			mysqli_query($link, " INSERT INTO `baby_serial_generator`(`patient_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$user','$date','$time') ");
			
			$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `baby_serial_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
			
			$baby_serial=$last_slno["slno"];
			
			// OPD ID Generate
			$opd_idds=100;
			
			$date_str=explode("-", $date);
			$dis_year=$date_str[0];
			$dis_month=$date_str[1];
			$dis_year_sm=convert_date_only_sm_year($date);
			
			$c_m_y=$dis_year."-".$dis_month;
			
			$current_month=date("Y-m");
			if($c_m_y<$current_month)
			{
				$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
				$opd_id_num=$opd_id_qry["tot"];
				
				$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
				$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
				
				$pat_tot_num=$opd_id_num+$opd_id_cancel_num;
				
				if($pat_tot_num==0)
				{
					$opd_idd=$opd_idds+1;
				}else
				{
					$opd_idd=$opd_idds+$pat_tot_num+1;
				}
				$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
			}else
			{
				$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
				if(!$c_data)
				{
					mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
				}

				mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$new_patient_id','8','$usr','$date','$time') ");
				
				$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$usr' ORDER BY `slno` DESC LIMIT 0,1 "));
				
				$last_slno=$last_slno["slno"];
				
				mysqli_query($link, " DELETE FROM `pin_generator` WHERE `slno`='$last_slno' ");
				
				$opd_idd=$opd_idds+$last_slno;
				$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
			}
			
			$ipd_id=trim($opd_id);
			
			if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('$new_patient_id','$ipd_id','$date','$time','$usr','8','baby_serial','$ref','$centre') "))
			{
				mysqli_query($link,"INSERT INTO `ipd_pat_delivery_det`(`patient_id`, `ipd_id`, `sex`, `dob`, `born_time`, `weight`, `blood_group`, `bed_id`, `baby_uhid`, `baby_ipd_id`, `father_name`, `delivery_mode`, `conducted_by`, `dead_tag`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$sex','$dob','$b_time','$wt','$blood','$bed','$new_patient_id','$ipd_id','$fat_name','$mode','$conduct','$tag','$date','$time','$usr')");
				if($j)
				$j++;
			}else
			{
				$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
				if(!$c_data)
				{
					mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
				}

				mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$new_patient_id','8','$usr','$date','$time') ");
				
				$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$usr' ORDER BY `slno` DESC LIMIT 0,1 "));
				
				$last_slno=$last_slno["slno"];
				
				mysqli_query($link, " DELETE FROM `pin_generator` WHERE `slno`='$last_slno' ");
				
				$opd_idd=$opd_idds+$last_slno;
				$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				
				$ipd_id=trim($opd_id);
				
				if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('$new_patient_id','$ipd_id','$date','$time','$usr','8','baby_serial','$ref','$centre') "))
				{
					mysqli_query($link,"INSERT INTO `ipd_pat_delivery_det`(`patient_id`, `ipd_id`, `sex`, `dob`, `born_time`, `weight`, `blood_group`, `bed_id`, `baby_uhid`, `baby_ipd_id`, `father_name`, `delivery_mode`, `conducted_by`, `dead_tag`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$sex','$dob','$b_time','$wt','$blood','$bed','$new_patient_id','$ipd_id','$fat_name','$mode','$conduct','$tag','$date','$time','$usr')");
					if($j)
					$j++;
				}
			}
		}
	}
	echo "Saved";
}

if($_POST["type"]=="baby_bed_assign")
{
	$uhid=$_POST['uhid'];
	$ward=$_POST['ward'];
	$room=$_POST['room'];
	$bed=$_POST['bed'];
	$bno=$_POST['bno'];
	$sn=$_POST['sn'];
	$usr=$_POST['usr'];
	mysqli_query($link,"DELETE FROM `ipd_baby_bed_temp` WHERE `patient_id`='$uhid' AND `baby_no`='$sn'");
	mysqli_query($link,"INSERT INTO `ipd_baby_bed_temp`(`patient_id`, `baby_no`, `ward_id`, `bed_id`) VALUES ('$uhid','$sn','$ward','$bed')");
}

if($_POST["type"]=="save_ot_resource_type")
{
	$id=$_POST['id'];
	$tname=$_POST['tname'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_type_master` WHERE `type_id`='$id'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ot_type_master` SET `type`='$tname' WHERE `type_id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_type_master`(`type_id`, `type`) VALUES ('$id','$tname')");
		echo "Saved";
	}
}

if($_POST["type"]=="save_ot_resource")
{
	$id=$_POST['id'];
	$typ=$_POST['typ'];
	$emp=$_POST['emp'];
	$fee=$_POST['fee'];
	$user=$_POST['user'];
	$max_charge=mysqli_fetch_array(mysqli_query($link," SELECT MAX(`charge_id`) AS MAX FROM `charge_master` "));
	$charge_id=$max_charge["MAX"]+1;
	$e=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$emp' "));
	$r=mysqli_fetch_array(mysqli_query($link," SELECT `type` FROM `ot_type_master` WHERE `type_id`='$typ' "));
	$resourse=$r['type']." Fees (".$e['name'].")";
	$qry=mysqli_query($link,"SELECT * FROM `ot_resource_master` WHERE `id`='$id'");
	$num=mysqli_num_rows($qry);
	/*
	if($num>0)
	{
		$vl=mysqli_fetch_array($qry);
		$qq=mysqli_query($link,"SELECT * FROM `charge_master` WHERE `charge_id`='$vl[charge_id]'");
		if(mysqli_num_rows($qq)>0)
		{
			mysqli_query($link,"UPDATE `charge_master` SET `amount`='$fee' WHERE `charge_id`='$vl[charge_id]'");
			$charge=$vl['charge_id'];
		}
		else
		{
			mysqli_query($link,"INSERT INTO `charge_master`(`charge_id`, `charge_name`, `group_id`, `charge_type`, `amount`, `user`) VALUES ('$charge_id','$resourse','144','0','$fee','$user')");
			$charge=$charge_id;
		}
		mysqli_query($link,"UPDATE `ot_resource_master` SET `type_id`='$typ',`emp_id`='$emp',`charge_id`='$charge' WHERE `id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `charge_master`(`charge_id`, `charge_name`, `group_id`, `charge_type`, `amount`, `user`) VALUES ('$charge_id','$resourse','144','0','$fee','$user')");
		mysqli_query($link,"INSERT INTO `ot_resource_master`(`type_id`, `emp_id`, `charge_id`) VALUES ('$typ','$emp','$charge_id')");
		echo "Saved";
	}
	*/
	if($id>0)
	{
		if(mysqli_query($link,"UPDATE `ot_resource_master` SET `type_id`='$typ',`emp_id`='$emp',`charge_id`='$fee' WHERE `id`='$id'"))
		{
			echo "Updated";
		}
		else
		{
			echo "Error";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `ot_resource_master`(`type_id`, `emp_id`, `charge_id`) VALUES ('$typ','$emp','$fee')"))
		{
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($_POST["type"]=="save_ipd_ot_book")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ot=$_POST['ot'];
	$pr=$_POST['pr'];
	$ot_date=$_POST['ot_date'];
	$doc=$_POST['doc'];
	$usr=$_POST['usr'];
	$tp=$_POST['tp'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($num==0)
	{
		if($tp==1)
		{
			mysqli_query($link,"UPDATE `ot_book` SET `ot_area_id`='$ot',`procedure_id`='$pr',`consultantdoctorid`='$doc',`ot_date`='$ot_date' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
			echo "Updated";
		}
		else if($tp==0)
		{
			mysqli_query($link,"INSERT INTO `ot_book`(`patient_id`, `ipd_id`, `ot_area_id`, `procedure_id`, `consultantdoctorid`, `ot_date`, `scheduled`, `date`, `time`, `user`, `schedule_id`) VALUES ('$uhid','$ipd','$ot','$pr','$doc','$ot_date','0','$date','$time','$usr','0')");
			echo "Saved";
		}
	}
	else
	{
		echo "Allready booked";
	}
}

if($_POST["type"]=="ot_save_shed")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ot=$_POST['ot'];
	$pr=$_POST['pr'];
	$ot_date=$_POST['ot_date'];
	$st_time=$_POST['st_time'];
	$en_time=$_POST['en_time'];
	$rem=$_POST['rem'];
	$doc=$_POST['doc'];
	$usr=$_POST['usr'];
	$det=$_POST['det'];
	$det=explode("#@#",$det);
	
	mysqli_query($link,"INSERT INTO `ot_schedule`(`patient_id`, `ipd_id`, `ot_date`, `start_time`, `end_time`, `ot_no`, `remarks`, `requesting_doc`, `procedure_id`, `date`, `time`, `user`, `leaved`) VALUES ('$uhid','$ipd','$ot_date','$st_time','$en_time','$ot','$rem','$doc','$pr','$date','$time','$usr','0')");
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(schedule_id) as max FROM `ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `user`='$usr'"));
	foreach($det as $dtt)
	{
		if($dtt)
		{
			$dt=explode("@",$dtt);
			$rss=$dt[0];
			$rdoc=$dt[1];
			if($rss && $rdoc)
			mysqli_query($link,"INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$i[max]','$rss','$rdoc')");
			//echo "INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$i[max]','$rss','$rdoc')";
		}
	}
	//mysqli_query($link,"UPDATE `ot_book` SET `scheduled`='1', `schedule_id`='$i[max]' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	mysqli_query($link,"INSERT INTO `ot_book`(`patient_id`, `ipd_id`, `ot_area_id`, `procedure_id`, `consultantdoctorid`, `ot_date`, `scheduled`, `pac_status`, `date`, `time`, `user`, `schedule_id`) VALUES ('$uhid','$ipd','$ot','$pr','$doc','$ot_date','1','0','$date','$time','$usr','$i[max]')");
	mysqli_query($link,"INSERT INTO `ot_process`(`patient_id`, `ipd_id`, `schedule_id`, `user`, `time`, `date`) VALUES ('$uhid','$ipd','$i[max]','$usr','$time','$date')");
	echo "Saved";
}

if($_POST["type"]=="save_reg_fees")
{
	$opd_fee=$_POST['opd_fee'];
	$opd_emer_fee=$_POST['opd_emer_fee'];
	$opd_val=$_POST['opd_val'];
	$ipd_fee=$_POST['ipd_fee'];
	$ipd_val=$_POST['ipd_val'];
	$vaccu=$_POST['vaccu'];
	$uhidnum=$_POST['uhidnum'];
	$pinnum=$_POST['pinnum'];
	$usr=$_POST['usr'];
	mysqli_query($link,"DELETE FROM `opd_registration_fees`");
	mysqli_query($link,"DELETE FROM `ipd_registration_fees`");
	mysqli_query($link,"INSERT INTO `opd_registration_fees`(`regd_fee`, `emerg_fee`, `validity`, `user`) VALUES ('$opd_fee','$opd_emer_fee','$opd_val','$usr')");
	mysqli_query($link,"INSERT INTO `ipd_registration_fees`(`regd_fee`, `validity`, `user`) VALUES ('$ipd_fee','$ipd_val','$usr')");
	mysqli_query($link,"UPDATE `company_name` SET `vaccu_charge`='$vaccu',`uhid_start`='$uhidnum',`pin_start`='$pinnum'");
	echo "Saved";
}

if($_POST["type"]=="save_conf")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$conf=$_POST['conf'];
	$usr=$_POST['usr'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_confidential` WHERE `patient_id`='$uhid' AND `opd_id`='$opd'"));
	if($n>0)
	{
		mysqli_query($link,"UPDATE `pat_confidential` SET `confident`='$conf',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `opd_id`='$opd'");
		echo "Saved";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `pat_confidential`(`patient_id`, `opd_id`, `confident`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$conf','$date','$time','$usr')");
		echo "Saved";
	}
}

if($_POST["type"]=="pat_ipd_drug_update")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$id=$_POST['id'];
	$medi=$_POST['medi'];
	$freq=$_POST['freq'];
	$st_date=$_POST['st_date'];
	$dur=$_POST['dur'];
	$unit_day=$_POST['unit_day'];
	$total=$_POST['total'];
	$inst=$_POST['inst'];
	$dos=$_POST['dos'];
	$usr=$_POST['usr'];
	if($unit_day=="Days")
	{
		$dd=1*$dur;
		//$drgs=$tot/$dd;
	}
	else if($unit_day=="Weeks")
	{
		$dd=7*$dur;
		//$drgs=$tot/$dd;
	}
	else if($unit_day=="Months")
	{
		$dd=30*$dur;
		//$drgs=$tot/$dd;
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
	mysqli_query($link,"UPDATE `ipd_pat_medicine_final_discharge` SET `medicine`='$medi',`dosage`='$dos',`frequency`='$freq',`start_date`='$st_date',`end_date`='$ed',`total_drugs`='$total',`duration`='$dur',`unit_days`='$unit_day',`instruction`='$inst',`date`='$date',`time`='$time',`user`='$usr' WHERE `id`='$id'");
}

if($_POST["type"]=="insert_medicine_final")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$itm=$_POST['itm'];
	$new_medi=$_POST['new_medi'];
	$new_medi=str_replace("'", "''", "$new_medi");
	$dos=$_POST['dos'];
	$dos=str_replace("'", "''", "$dos");
	$ph_quantity=$_POST['ph_quantity'];
	$user=$_POST['user'];
	
	$ind=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(indent_num) as max FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd'"));
	$in=$ind['max']+1;
	
	$type=3; // IPD Discharge
	
	if($itm)
	{
		$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `item_code`='$itm'"));
		if($n>0)
		{
			mysqli_query($link,"UPDATE `patient_medicine_detail` SET `dosage`='$dos',`instruction`='0',`quantity`='$ph_quantity',`date`='$date',`time`='$time',`user`='$user' WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `item_code`='$itm'");
		}
		else
		{
			mysqli_query($link," INSERT INTO `patient_medicine_detail`(`patient_id`, `pin`, `indent_num`, `item_code`, `dosage`, `instruction`, `quantity`, `status`, `date`, `time`, `user`, `type`, `bill_no`) VALUES ('$uhid','$ipd','$in','$itm','$dos','0','$ph_quantity','0','$date','$time','$user','$type','') ");
		}
	}
	else
	{
		$new_id=nextID("","item_master","item_id","");
		
		//mysqli_query($link,"INSERT INTO `item_master`(`item_code`, `item_name`, `generic`, `item_mrp`, `cost_price`, `ph_cate_id`, `strip_qnty`, `gst_percent`) VALUES ('$new_id','$new_medi','0','0.00','0.00','0','1','12.00')");
		
		mysqli_query($link," INSERT INTO `item_master`(`item_id`, `short_name`, `item_name`, `hsn_code`, `category_id`, `sub_category_id`, `item_type_id`, `re_order`, `critical_stock`, `generic_name`, `rack_no`, `manufacturer_id`, `mrp`, `gst`, `strength`, `strip_quantity`, `unit`, `specific_type`, `class`) VALUES ('$new_id','','$new_medi','','1','1','0','0','0','','','0','0','0','0','0','0','0','') ");
		
		//mysqli_query($link,"INSERT INTO `ipd_pat_medicine_post_discharge`(`patient_id`, `ipd_id`, `item_code`, `dosage`, `frequency`, `total_drugs`, `instruction`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$new_id','$dos','0','0','0','$date','$time','$user')");
		
		mysqli_query($link," INSERT INTO `patient_medicine_detail`(`patient_id`, `pin`, `indent_num`, `item_code`, `dosage`, `instruction`, `quantity`, `status`, `date`, `time`, `user`, `type`, `bill_no`) VALUES ('$uhid','$ipd','$in','$new_id','$dos','0','$ph_quantity','0','$date','$time','$user','$type','') ");
		
		mysqli_query($link,"INSERT INTO `item_master_changes`(`item_id`, `old_name`, `new_name`, `process`, `date`, `time`, `user`) VALUES ('$new_id','','$new_medi','NEW ENTRY DISCHARGE','$date','$time','$user')");
	}
	$dn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `dosage_master` WHERE `dosage`='$dos'"));
	if($dn==0)
	{
		mysqli_query($link,"INSERT INTO `dosage_master`(`dosage`) VALUES ('$dos')");
	}
	echo "Saved";
}

if($_POST["type"]=="save_new_medicine")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$new_medi=strtoupper($_POST['new_medi']);
	$new_medi=str_replace("'", "''", "$new_medi");
	$dos=$_POST['dos'];
	$dos=str_replace("'", "''", "$dos");
	$inst=$_POST['inst'];
	$usr=$_POST['usr'];
	$it_id=nextID('ITM','ph_item_master','item_code','');
	mysqli_query($link,"INSERT INTO `ph_item_master`(`item_code`, `item_name`, `strip_qnty`) VALUES ('$it_id','$new_medi','1')");
	mysqli_query($link,"INSERT INTO `medicine_check`(`patient_id`, `opd_id`, `item_code`, `dosage`, `instruction`, `date`, `time`, `user`) VALUES ('$uhid','$opd','$it_id','$dos','$inst','$date','$time','$usr')");
}

if($_POST["type"]=="oo")
{
	
}
?>
