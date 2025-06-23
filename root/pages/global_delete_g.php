<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

if($_POST["type"]=="delete_consult_doc")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$id'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_dept")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `doctor_specialist_list` WHERE `speciality_id`='$id'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_level")
{
	$sl=$_POST['sl'];
	if(mysqli_query($link,"DELETE FROM `level_master` WHERE `slno`='$sl'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="del_menu")
{
	$pid=$_POST['pid'];
	if(mysqli_query($link,"DELETE FROM `menu_master` WHERE `par_id`='$pid'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_user")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `employee` WHERE `emp_id`='$id'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_refer_doc")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `refbydoctor_master` WHERE `refbydoctorid`='$id'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_ward")
{
	$sl=$_POST['sl'];
	
	$ward=mysqli_fetch_array(mysqli_query($link, " SELECT `ward_id` FROM `ward_master` WHERE `sl_no`='$sl' "));
	
	if(mysqli_query($link,"DELETE FROM `ward_master` WHERE `sl_no`='$sl'"))
	{
		mysqli_query($link,"DELETE FROM `bed_other_charge` WHERE `bed_id` IN(SELECT `bed_id` FROM `bed_master` WHERE `room_id` IN (SELECT `room_id` FROM `room_master` WHERE `ward_id`='$ward[ward_id]'))");
		
		mysqli_query($link,"DELETE FROM `charge_master` WHERE `charge_id` IN(SELECT `charge_id` FROM `bed_master` WHERE `room_id` IN (SELECT `room_id` FROM `room_master` WHERE `ward_id`='$ward[ward_id]'))");
		
		mysqli_query($link,"DELETE FROM `bed_master` WHERE `room_id` IN (SELECT `room_id` FROM `room_master` WHERE `ward_id`='$ward[ward_id]')");
		
		mysqli_query($link,"DELETE FROM `room_master` WHERE `ward_id`='$ward[ward_id]'");
		
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_room")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `room_master` WHERE `room_id`='$id'"))
	{
		mysqli_query($link,"DELETE FROM `bed_other_charge` WHERE `bed_id` IN(SELECT `bed_id` FROM `bed_master` WHERE `room_id`='$id')");
		
		mysqli_query($link,"DELETE FROM `charge_master` WHERE `charge_id` IN(SELECT `charge_id` FROM `bed_master` WHERE `room_id`='$id')");
		
		mysqli_query($link,"DELETE FROM `bed_master` WHERE `room_id`='$id'");
		
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_bed")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `bed_master` WHERE `bed_id`='$id'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_test")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$test=$_POST['test'];
	mysqli_query($link,"DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' AND `testid`='$test'");
}

if($_POST["type"]=="delete_medi")
{
	$uhid=$_POST['uhid'];
	$opd=$_POST['opd'];
	$medi=$_POST['medi'];
	mysqli_query($link,"DELETE FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$opd' AND `item_code`='$medi'");
}

if($_POST["type"]=="delete_test_master")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `testmaster` WHERE `testid`='$id'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_medicine_master")
{
	$id=$_POST['id'];
	if(mysqli_query($link,"DELETE FROM `medicine_master` WHERE `medicine_id`='$id'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="delete_item_type")
{
	$rid=$_POST['rid'];
	if(mysqli_query($link,"DELETE FROM `ph_item_type_master` WHERE `item_type_id`='$rid'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="item_master_delete")
{
	$rid=$_POST['rid'];
	if(mysqli_query($link,"DELETE FROM `ph_item_master` WHERE `item_code`='$rid'"))
	{
		echo "Deleted";
	}
	else
	{
		echo "Error";
	}
}

if($_POST["type"]=="purchase_order_temp_del")
{
	$itmid=$_POST['itmid'];
	$ord=$_POST['orderno'];
	mysqli_query($link,"DELETE FROM `ph_purchase_order_details_temp` WHERE `order_no`='$ord' AND `item_code`='$itmid'");
}

if($_POST["type"]=="sale_item_delete")
{
	$itmcode=$_POST['itmcode'];
	$btchno=$_POST['btchno'];
	$bill=$_POST['billno'];
	mysqli_query($link,"delete from ph_sell_details_temp where bill_no='$bill' and item_code='$itmcode' and batch_no='$btchno'");
}

if($_POST["type"]=="del_comp")
{
	$sl=$_POST['sl'];
	mysqli_query($link,"DELETE FROM `pat_complaints` WHERE `slno`='$sl'");
}

if($_POST["type"]=="del_diag")
{
	$sl=$_POST['sl'];
	mysqli_query($link,"DELETE FROM `pat_diagnosis` WHERE `slno`='$sl'");
}

if($_POST["type"]=="del_disp")
{
	$sl=$_POST['sl'];
	mysqli_query($link,"DELETE FROM `pat_disposition` WHERE `slno`='$sl'");
}

if($_POST["type"]=="delete_ind_type")
{
	$sl=$_POST['sl'];
	mysqli_query($link,"DELETE FROM `inv_indent_type` WHERE `sl_no`='$sl'");
	echo "Deleted";
}

if($_POST["type"]=="delete_ind_master")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `inv_indent_master` WHERE `id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="delete_inv_supp_master")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `inv_supplier_master` WHERE `id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="delete_donor_type")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `blood_donor_type` WHERE `type_id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="delete_pack_master")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `blood_pack_master` WHERE `pack_id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="delete_ot_type")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `ot_type_master` WHERE `type_id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="delete_res_details")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `ot_resource_master` WHERE `id`='$id'");
	echo "Deleted";
}


if($_POST["type"]=="ipd_pat_post_medi_del")
{
	$id=$_POST['id'];
	
	mysqli_query($link,"DELETE FROM `patient_medicine_detail` WHERE `slno`='$id'");
}

if($_POST["type"]=="oo")
{
	
}
?>
