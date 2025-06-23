<?php
include("../../includes/connection.php");
include('../../includes/global.function.php');

$date=date('Y-m-d'); // impotant
$time=date('H:i:s');


function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$type=$_POST['type'];

////////////////////
if($type=="phcategory")//// Lab Doctor Master
{
	$vid=nextId("","ph_category_master","ph_cate_id","1");
}
/////////////////////////////////////
elseif($type=="indent_order")  ///for indent order
{
	$vid=nextId("ORD","ph_category_master","order_no","1");
}
/////////////////////////////////////
elseif($type=="loadphitmdirct")  ///for indent order
{
	$vid=nextId("RCV","ph_purchase_receipt_master","order_no","1");
}
/////////////////////////////////////
elseif($type=="loaditmrtncrdt")  ///for Item Return Credit no 
{
	$vid=nextId("CR","ph_item_return_creditnote","credit_no","100");
}
////////////////////
elseif($type=="dailyexpnse")////Daily Expense
{
	$vid=nextId1("","expensedetail","SlNo","1","$date");

}
//////////////////////////////////// 
elseif($type=="ph_itm_rtrnto_store") ////Item retrun to supplier  
{
	
		$billnos=100;
		
		$date_str=explode("-", $date);
		$dis_year=$date_str[0];
		$dis_month=$date_str[1];
		$dis_year_sm=convert_date_only_sm_year($date);

		//$c_m_y=$dis_year."-".$dis_month;
		$c_m_y=$dis_year;
		$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`returnr_no`) as tot FROM `ph_item_return_store_master` WHERE `date` like '$c_m_y%' "));
		$bill_num=$bill_no_qry["tot"];
	

		$bill_tot_num=$bill_num;

		if($bill_tot_num==0)
		{
			$bill_no=$billnos+1;
		}else
		{
			$bill_no=$billnos+$bill_tot_num+1;
		}
		
		$vid=$bill_no."/".$dis_year_sm;
		
	//$vid=nextId("","inv_mainstore_issue_details","issu_no","1");
}

////////////////////
elseif($type=="load_sale_id")////
{
	///$vid=nextId("","ph_sell_master","bill_no","101");
	
		$billnos=100;
		
		$date_str=explode("-", $date);
		$dis_year=$date_str[0];
		$dis_month=$date_str[1];
		$dis_year_sm=convert_date_only_sm_year($date);

		//$c_m_y=$dis_year."-".$dis_month;
		$c_m_y=$dis_year;
		$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`bill_no`) as tot FROM `ph_sell_master` WHERE `entry_date` like '$c_m_y%' "));
		$bill_num=$bill_no_qry["tot"];
	

		$bill_tot_num=$bill_num;

		if($bill_tot_num==0)
		{
			$bill_no=$billnos+1;
		}else
		{
			$bill_no=$billnos+$bill_tot_num+1;
		}
		
		$vid=$bill_no."/".$dis_year_sm;
		
		$qchk=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$vid' "));
		if(!$qchk)
		{
			mysqli_query($link,"delete from ph_sell_details where bill_no='$vid'");
			mysqli_query($link,"delete from ph_payment_details where bill_no='$vid'");
		}
		
}


echo $vid;
?>
