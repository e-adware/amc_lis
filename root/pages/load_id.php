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
if($type=="labdoctor")//// Lab Doctor Master
{
	$vid=nextId("","lab_doctor","id","1");
}
////////////////////
if($type=="subtore")//// 
{
	$vid=nextId("","inv_sub_store","substore_id","1");
}
/////////////////////////////////// 
elseif($type=="substore_order") ////sub indent central order  
{
	//$vid=nextId("","inv_substore_indent_order_master","order_no","1");
	
	    $billnos=100;
		
		$date_str=explode("-", $date);
		$dis_year=$date_str[0];
		$dis_month=$date_str[1];
		$dis_year_sm=convert_date_only_sm_year($date);

		//$c_m_y=$dis_year."-".$dis_month;
		$c_m_y=$dis_year;
		$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`order_no`) as tot FROM `inv_substore_indent_order_master` WHERE `order_date` like '$c_m_y%' "));
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
	
}
//////////////////////////////////// Category inventory
elseif($type=="invindntcatgry")
{
	$vid=nextId("","inv_indent_type","inv_cate_id","1");
}
//////////////////////////////////// sub Category inventory
elseif($type=="invsubcatgory")
{
	$vid=nextId("","inv_subcategory","sub_cat_id","1");
}
//////////////////////////////////// 
elseif($type=="indmaster") ////sub indent Master inventory
{
	$vid=nextId("","inv_indent_master","id","1");
}
//////////////////////////////////// 
elseif($type=="cntrlorder") ////sub indent central order
{
	$vid=nextId("CORD","inv_indent_order_master","order_no","1");
}
//////////////////////////////////// 
elseif($type=="invstkentry") ////sub indent central order  
{
	$vid=nextId("","inv_main_stock_receied_master","receipt_no","1");
}

//////////////////////////////////// 
elseif($type=="invsubstritmissue") ////sub indent central order  
{
	$vid=nextId("","inv_substore_issue_details","issu_no","1");
}
//////////////////////////////////// 
elseif($type=="invmainstritmissue") ////sub indent central order  
{
	$vid=nextId("ISU","inv_mainstore_direct_issue_master","issue_no","100");
	
}
//////////////////////////////////// 
elseif($type=="purchase_order") ////Purchase order central order  
{
	
		$billnos=100;
		
		$date_str=explode("-", $date);
		$dis_year=$date_str[0];
		$dis_month=$date_str[1];
		$dis_year_sm=convert_date_only_sm_year($date);

		//$c_m_y=$dis_year."-".$dis_month;
		$c_m_y=$dis_year;
		$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`order_no`) as tot FROM `inv_purchase_order_master` WHERE `order_date` like '$c_m_y%' "));
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

//////////////////////////////////// 
elseif($type=="invitmrtrntospplr") ////Item retrun to supplier  
{
	
		$billnos=100;
		
		$date_str=explode("-", $date);
		$dis_year=$date_str[0];
		$dis_month=$date_str[1];
		$dis_year_sm=convert_date_only_sm_year($date);

		//$c_m_y=$dis_year."-".$dis_month;
		$c_m_y=$dis_year;
		$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`returnr_no`) as tot FROM `inv_item_return_supplier_master` WHERE `date` like '$c_m_y%' "));
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
echo $vid;
?>
