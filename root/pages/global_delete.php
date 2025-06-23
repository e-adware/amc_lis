<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

if($_POST["type"]=="labdoctor_del")
{
	$subp=$_POST['subp'];
	mysqli_query($link, "delete from lab_doctor where id='$subp' ");
}
if($_POST["type"]=="vaccumaster")
{
	$smplid=$_POST['smplid'];
	mysqli_query($link, "delete from vaccu_master where id='$smplid' ");
}
if($_POST["type"]=="samplemastr")
{
	$smplid=$_POST['smplid'];
	mysqli_query($link, "delete from Sample where ID='$smplid' ");
}
if($_POST["type"]=="testmethod")
{
	$docid=$_POST['docid'];
	mysqli_query($link, "delete from test_methods where id='$docid' ");
}
if($_POST["type"]=="resultoption")
{
	$subp=$_POST['docid'];
	mysqli_query($link, "delete from ResultOption where id='$subp' ");
}
if($_POST["type"]=="option")
{
	$subp=$_POST['docid'];
	mysqli_query($link, "delete from Options where id='$subp' ");
}


// After accuRate
if($_POST["type"]=="cleaning_item")
{
	$item_id=$_POST['smplid'];
	mysqli_query($link, " DELETE FROM `cleaning_item_master` WHERE `item_id`='$item_id' ");
}
if($_POST["type"]=="cleaning_material")
{
	$item_id=$_POST['smplid'];
	mysqli_query($link, " DELETE FROM `cleaning_material_master` WHERE `item_mat_id`='$item_id' ");
}
if($_POST["type"]=="delete_selected_area_data")
{
	$slno=$_POST['slno'];
	mysqli_query($link, " DELETE FROM `cleaning_area` WHERE `slno`='$slno' ");
}
if($_POST["type"]=="charge_group")
{
	$group_id=$_POST['smplid'];
	mysqli_query($link, " DELETE FROM `charge_group_master` WHERE `group_id`='$group_id' ");
	mysqli_query($link, " DELETE FROM `charge_master` WHERE `group_id`='$group_id' ");
	
}
if($_POST["type"]=="charges")
{
	$charge_id=$_POST['smplid'];
	mysqli_query($link, " DELETE FROM `charge_master` WHERE `charge_id`='$charge_id' ");
}
if($_POST["type"]=="cntermaster")
{
	$subp=$_POST['subp'];
	mysqli_query($link, " DELETE FROM `centremaster` WHERE `centreno`='$subp' ");
}
if($_POST["type"]=="load_daily_expense")
{
	$slno=$_POST['slno'];
	mysqli_query($link, " DELETE FROM `expense_detail` WHERE `slno`='$slno' ");
}
if($_POST["type"]=="opd_room")
{
	$subp=$_POST['subp'];
	mysqli_query($link, " DELETE FROM `opd_doctor_room` WHERE `room_id`='$subp' ");
}
if($_POST["type"]=="ipd_discharge")
{
	$subp=$_POST['subp'];
	
	$check_entry=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_dischage_type` WHERE `type`='$subp' "));
	if($check_entry)
	{
		echo "404";
	}else
	{
		//mysqli_query($link, " DELETE FROM `discharge_master` WHERE `discharge_id`='$subp' ");
		
		echo "1";
	}
}
?>
