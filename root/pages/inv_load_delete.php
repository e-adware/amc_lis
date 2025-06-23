<?php
session_start();
include'../../includes/connection.php';
$type=$_POST['type'];
$shopcode='SHop101';

$time=date('h:i:s A');
$date=date('Y-m-d'); // impotant

if($type=="itemtype") //for Item Type
{
	$rid=$_POST['rid'];
	mysqli_query($link,"delete from item_type_master where item_type_id='$rid' ");
}

//////////////////////////////////////
elseif($type=="indent_ord_temp")  ////For Delete order Item
{
	$itmid=$_POST['itmid'];
	$orderno=$_POST['orderno'];
	mysqli_query($link,"delete from inv_indent_order_details_temp where order_no='$orderno' and item_code='$itmid'");
	
}
//////////////////////////////////////
elseif($type=="indentrcvtmp")  ////For indentd rcv temp
{
	$invid=$_POST['invid'];
	$mid=$_POST['mid'];
	mysqli_query($link,"delete from indent_rcvdetails_temp where invoiceno='$invid' and item_code='$mid'");
	
}
//////////////////////////////////////
elseif($type=="subtore")  ////For sub store
{
	$itmid=$_POST['itmid'];
	mysqli_query($link,"delete from inv_sub_store where substore_id='$itmid' ");
	
}

//////////////////////////////////////
elseif($type=="loadsubcatgry")  ////For sub category delete
{
	$subcatid=$_POST['subcatid'];
	mysqli_query($link,"delete from inv_subcategory where sub_cat_id='$subcatid' ");
	
}
//////////////////////////////////////
elseif($type=="purchaseorderdel")  ////For sub order delete
{
	$orderno=$_POST['rid'];
	mysqli_query($link,"delete from inv_purchase_order_details where order_no='$orderno' ");
	mysqli_query($link,"update inv_purchase_order_master set del='1' where order_no='$orderno' ");
	$qsplr=mysqli_fetch_array(mysqli_query($link,"select supplier_id from inv_purchase_order_master where order_no='$orderno'"));
	mysqli_query($link,"INSERT INTO `inv_purchase_order_master_delete`(`order_no`, `supplier_id`, `user`, `date`, `time`) VALUES ('$orderno','$qsplr[supplier_id]','$_SESSION[emp_id]','$date','$time')");
	
}

//////////////////////////////////////
elseif($type=="substore_order_tmp")  ////For sub store order temp
{
	$itmid=$_POST['itmid'];
	$orderno=$_POST['orderno'];
	$sbstrid=$_POST['sbstrid'];
	mysqli_query($link,"delete from inv_substore_order_details_temp where order_no='$orderno' and item_id='$itmid' and substore_id='$sbstrid' ");
	
}

//////////////////////////////////////
elseif($type=="purchase_order_tmp")  ////For purchase temp
{
	$itmid=$_POST['itmid'];
	$orderno=$_POST['orderno'];
	$spplrid=$_POST['spplrid'];
	
	mysqli_query($link,"delete from inv_purchase_order_details_temp where order_no='$orderno' and item_id='$itmid' and supplier_id='$spplrid' ");
	
}

//////////////////////////////////////
elseif($type=="invsubstritmissue")  ////For sub store order temp
{
	$itmid=$_POST['itmid'];
	$issueno=$_POST['issueno'];
	$sbstrid=$_POST['sbstrid'];
	mysqli_query($link,"delete from inv_substore_issue_details_temp where issu_no='$issueno' and item_code='$itmid' and substore_id='$sbstrid' ");
	
}
//////////////////////////////////////
elseif($type=="invitemretruntospplr_tmp")  ////For item retrun to supplier
{
	$itmid=$_POST['itmid'];
	$btchno=$_POST['btchno'];
	$rtrnno=$_POST['rtrnno'];
	$splrid=$_POST['splrid'];
	mysqli_query($link,"delete from inv_item_return_supplier_detail_temp where returnr_no='$rtrnno' and item_id='$itmid' and batch_no='$btchno' and supplier_id='$splrid' ");
	
}

//////////////////////////////////////
elseif($type=="invmainstritmissue")  ////For main store order temp
{
	$itmid=$_POST['itmid'];
	$issueno=$_POST['issueno'];
	$sbstrid=$_POST['sbstrid'];
	mysqli_query($link,"delete from inv_mainstore_issue_details_tmp where issu_no='$issueno' and item_id='$itmid' and substore_id='$sbstrid' ");
	
}
//////////////////////////////////////
elseif($type=="indmaster")  ////For for indent master
{
	$itmid=$_POST['id'];
	mysqli_query($link,"delete from inv_indent_master where id='$itmid' ");
	
}
//////////////////////////////////////
elseif($type=="mainstkentry_tmp")  ////For delete purchase receipt item from Temp table
{
	$orderno=$_POST['orderno'];
	$billno=$_POST['billno'];
	$itmid=$_POST['itmid'];
	
	mysqli_query($link,"delete from inv_main_stock_receied_detail_temp where receipt_no='$orderno' and item_code='$itmid' and supplier_bill_no='$billno' ");
}
//////////////////////////////////////
elseif($type=="salesdel")  ////For Sales Bill Delete
{
	$blno=$_POST['rid'];
	$fd=$_POST['fd'];
	$vdate=date('Y/m/d');
	
	 ////for update stock
	$qitem=mysqli_query($link,"select * from sell_details where FID='$fd' and bill_no='$blno'");
	while($qitem1=mysqli_fetch_array($qitem))
	{
		     $slqnt=0;
		     $slqnt=$qitem1['sale_qnt']+$qitem1['free_qnt'];
			 	 
	          $vstkqnt=0;
	          $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from stock_process where item_code='$qitem1[item_code]' and  batch_no='$qitem1[batch_no]' and date='$vdate'"));
	       if($qrstkmaster['item_code']!='')
	        {
	           $vstkqnt=$qrstkmaster['s_remain']+$slqnt;
			   $vrcvqnt=$qrstkmaster['added']+$slqnt;
	 	 
	           mysqli_query($link,"update stock_process set s_remain='$vstkqnt',added='$vrcvqnt' where item_code='$qitem1[item_code]' and  batch_no='$qitem1[batch_no]' and date='$vdate'");
	           mysqli_query($link,"update stock_master set quantity='$vstkqnt' where item_code='$qitem1[item_code]' and  batch_no='$qitem1[batch_no]'");
			  
			   
	          }
	       else
	       {
			    $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from stock_process where  item_code='$qitem1[item_code]' and  batch_no='$qitem1[batch_no]' order by date desc"));
		       $vstkqnt=$qrstkmaster['s_remain']+$slqnt;
		 
		       mysqli_query($link,"insert into stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$blno','$qitem1[item_code]','$qitem1[batch_no]','$qrstkmaster[s_remain]','$slqnt',0,'$vstkqnt''$vdate')");
		    
		
		      mysqli_query($link,"update stock_master set quantity='$vstkqnt' where item_code='$qitem1[item_code]' and  batch_no='$qitem1[batch_no]'");
		      
		   }
	}
	/////For delete billno
	$vdat=date('Y/m/d');
	mysqli_query($link,"insert into cancel_bill select * from sell_master where FID='$fd' and bill_no='$blno'");
	mysqli_query($link,"delete from sell_details where  FID='$fd' and bill_no='$blno'");
	
	
	
}

?>
