<?php
session_start();
include'../../includes/connection.php';

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];


$type=$_POST['type'];
$shopcode='SHop101';

if($type=="phcategory") //for Item Type
{
	$phcatid=$_POST['phcatid'];
	mysqli_query($link,"delete from ph_category_master where ph_cate_id='$phcatid' ");
}
///////////////////////
elseif($type=="loadphitmdirct") ///For delet Direct Receipt item for Pharmacy
{
	$orderno=$_POST['orderno'];
	$itmid=$_POST['itmid'];
	$bch=$_POST['bch'];
	mysqli_query($link,"delete from ph_purchase_receipt_temp where 	order_no='$orderno' and item_code='$itmid' and recept_batch='$bch'");
}

///////////////////////
elseif($type=="loadpurchasercpt_del") ///For delete 
{
	$orderno=$_POST['orderno'];
	$itmid=$_POST['itmid'];
	$bch=$_POST['bch'];
	mysqli_query($link,"delete from inv_main_stock_received_detail_temp where 	order_no='$orderno' and item_id='$itmid' and recept_batch='$bch'");
}
///////////////////////
elseif($type=="purchse_ord_temp") ///For delet Direct Receipt item for Pharmacy
{
	$orderno=$_POST['orderno'];
	$itmid=$_POST['itmid'];
	mysqli_query($link,"delete from ph_purchase_order_details_temp where order_no='$orderno' and item_code='$itmid' ");
}

//////////////////////////////////////
elseif($type=="load_ph_item_return_tmp")  ////For item retrun to stock
{
	$itmid=$_POST['itmid'];
	$btchno=$_POST['btchno'];
	$rtrnno=$_POST['rtrnno'];
	
	mysqli_query($link,"delete from ph_item_return_store_detail_temp where returnr_no='$rtrnno' and item_id='$itmid' and batch_no='$btchno'  ");
	
}

if($_POST["type"]=="sale_item_delete")
{
	$itmcode=$_POST['itmcode'];
	$btchno=$_POST['btchno'];
	$bill=$_POST['billno'];
	mysqli_query($link,"delete from ph_sell_details_temp where bill_no='$bill' and item_code='$itmcode' and batch_no='$btchno' and  user='$userid'");
}

?>
