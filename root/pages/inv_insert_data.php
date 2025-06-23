<?php
session_start();
include'../../includes/connection.php';
include('../../includes/global.function.php');
$time=date('h:i:s A');
$date=date('Y-m-d'); // impotant
///////Date difference in php
	 /*$vendat=strtotime($mnufctr);
	 $vpaydat=strtotime($expiry);	
	 $vda=$vpaydat-$vendat;
	 echo  floor($vda/3600/24); */
	//////end///////////////
	


function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}	


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
	
$fid=mysqli_fetch_array(mysqli_query($link,"select max(FID) as maxfid from financialyear_master "));
$fd=$fid['maxfid'];
$fd=0;
$type=$_POST['type'];
$date1=date('Y/m/d');

session_start();

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

if($type=="itemtype") ///For  Item Type Master
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
		
}

////////////////////////////////
elseif($type=="supplier") //for  Supplier
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
	$vadrss=$_POST['vadrss'];
	$vphone=$_POST['vphone'];
	$vemail=$_POST['vemail'];
		
	$qrch=mysqli_fetch_array(mysqli_query($link,"select id from supplier_master where id='$csid'"));
	if($qrch)
	{
		mysqli_query($link,"update supplier_master set name='$csname',address='$vadrss',phone_no='$vphone',email_add='$vemail' where id='$csid'");
		
	}
	else
	{
	  mysqli_query($link,"insert into supplier_master values('$csid','$csname','$vadrss','$vphone','$vemail')");
  
	}
}



////////////////////////////////
elseif($type=="invindntcatgry") //for  inv  category
{
	
	$id=$_POST['id'];
	$name=$_POST['name'];
	
		
	$qrch=mysqli_fetch_array(mysqli_query($link,"select inv_cate_id from inv_indent_type where inv_cate_id='$id'"));
	if($qrch)
	{
		mysqli_query($link,"update inv_indent_type set name='$name' where inv_cate_id='$id'");		
	}
	else
	{
	  mysqli_query($link,"insert into inv_indent_type values('$id','$name')");
  
	}
	echo"Saved";
}


////////////////////////////////
elseif($type=="invsubcatgry") //for  inv sub category
{
	$subcatid=$_POST['subcatid'];
	$id=$_POST['id'];
	$name=$_POST['name'];
	
		
	$qrch=mysqli_fetch_array(mysqli_query($link,"select sub_cat_id from inv_subcategory where sub_cat_id='$id'"));
	if($qrch)
	{
		mysqli_query($link,"update inv_subcategory set sub_cat_name='$name',inv_cate_id='$subcatid' where sub_cat_id='$id'");		
	}
	else
	{
	  mysqli_query($link,"insert into inv_subcategory(inv_cate_id,sub_cat_id,sub_cat_name) values('$subcatid','$id','$name')");
	  
  
	}
}


////////////////////////////////
elseif($type=="saveindnt") //for  indent master
{
	$catid=$_POST['catid'];
	$subcatid=$_POST['subcatid'];
	$indid=$_POST['indid'];
	$name=$_POST['name'];
	$unit=$_POST['unit'];
	$reorderqnty=$_POST['reorderqnty'];
	$gstprcnt=$_POST['gstprcnt'];
	$stkinhnd=$_POST['stkinhnd'];
	$price=$_POST['price'];
	$vitmtype=$_POST['vitmtype'];

	$qrch=mysqli_fetch_array(mysqli_query($link,"select name from inv_indent_master where id='$indid'"));
	if($qrch)
	{
		
		mysqli_query($link,"update inv_indent_master set name='$name',inv_cate_id='$catid',sub_cat_id='$subcatid',unit='$unit',re_order_qnty='$reorderqnty',gst='$gstprcnt',stock_in_hand='$stkinhnd',specific_type='$vitmtype',price='$price' where id='$indid'");		
	}
	else
	{
	  mysqli_query($link,"insert into inv_indent_master(inv_cate_id,sub_cat_id,id,name,unit,re_order_qnty,gst,specific_type,stock_in_hand,price) values('$catid','$subcatid','$indid','$name','$unit','$reorderqnty','$gstprcnt','$vitmtype','$stkinhnd','$price')");
  
	}
}


////////////////////////////////
elseif($type=="financial") //for  Financialmaster
{
	$fid=$_POST['fid'];
	$ffrom=$_POST['ffrom'];
	$fto=$_POST['fto'];
	
	mysqli_query($link,"delete from financialyear_master where FID='$fid'");
	mysqli_query($link,"insert into financialyear_master values('$fid','$ffrom','$fto')");
	
}

if($type=="save_supplier_master")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$name= str_replace("'", "''", "$name");
	$contact=$_POST['contact'];
	$cperson=$_POST['cperson'];
	$email=$_POST['email'];
	$fax=$_POST['fax'];
	$addr=$_POST['addr'];
	$addr= str_replace("'", "''", "$addr");
	$gstno=$_POST['gstno'];
	$bnkid=$_POST['bnkid'];
	$bnkacno=$_POST['bnkacno'];
	$branch=$_POST['branch'];
	$ifsc=$_POST['ifsc'];
	$condition=$_POST['condition'];
	$condition2=$_POST['condition2'];
	$condition=str_replace("'", "''", "$condition");
	$condition2=str_replace("'", "''", "$condition2");
	$vigst=$_POST['vigst'];
	if($id>0)
	{
		mysqli_query($link,"UPDATE `inv_supplier_master` SET `name`='$name',`contact`='$contact',`contact_person`='$cperson',`email`='$email',`fax`='$fax',`address`='$addr',gst_no='$gstno' WHERE `id`='$id'");
		
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `inv_supplier_master`(`name`, `contact`, `contact_person`, `email`, `fax`, `address`, `gst_no`) VALUES ('$name','$contact','$cperson','$email','$fax','$addr','$gstno')");
		
		echo "Saved";
	}
}


////////////////////////////////
elseif($type=="stockentry") //for  Item Entry
{
	$itmid=$_POST['itmid'];
	$expiry=$_POST['expiry'];
	$batch=$_POST['batch'];
	$qnt=$_POST['qnt'];
	$mrp=$_POST['mrp'];
	$vcstprice=$_POST['vcstprice'];
	$vdate=date('Y/m/d');
	$blno=1;
	mysqli_query($link,"insert into item_stock_entry values('$itmid','$batch','$vdate','$qnt','$mrp','$vcstprice','$expiry')");


     ////////////For stock''''''''''''''''''''
    
	 
	 $vstkqnt=0;
	 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from stock_process where  date='$vdate' and item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
	 if($qrstkmaster['item_code']!='')
	 {
	    $vstkqnt=$qrstkmaster['s_remain']+$qnt;
	    $slqnt=$qrstkmaster['added']+$qnt;
	    mysqli_query($link,"update stock_process set s_remain='$vstkqnt',added='$slqnt' where  date='$vdate' and item_code='$itmid' and  batch_no='$batch'");
	   
	    mysqli_query($link,"update stock_master set quantity='$vstkqnt' where  item_code='$itmid' and batch_no='$batch'");
			
	 }
	 else///for if data not found
	 {
		 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from stock_process where  item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
          
		 $vstkqnt=$qrstkmaster['s_remain']+$qnt;
		 
		 mysqli_query($link,"insert into stock_process(process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$blno','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','$vqnt','$vstkqnt','$vdate')");
		 
		 $ichk=mysqli_fetch_array(mysqli_query($link,"select item_code from stock_master where item_code='$itmid' and batch_no='$batch'"));
		 if($ichk['item_code']!='')
		 
		 {
		      mysqli_query($link,"update stock_master set quantity='$vstkqnt' where  item_code='$itmid' and batch_no='$batch'");
		 }
		 else
		 {
			 mysqli_query($link,"insert into stock_master(item_code,batch_no,quantity,mfc_date,exp_date) values('$itmid','$batch','$vstkqnt','','$expiry')");
		 }
	 }
 
}

////////////////////////////////
elseif($type=="substore_order_tmp") //for  substore order temp
{
	$ordrdate=$_POST['ordrdate'];
	$substoreid=$_POST['substoreid'];
	$orderno=$_POST['orderno'];
	$itmcode=$_POST['itmcode'];
	$orqnt=$_POST['orqnt'];
	$itmcode1=explode("-#",$itmcode);
		
	mysqli_query($link,"delete from inv_substore_order_details_temp where order_no ='$orderno' and substore_id='$substoreid' and item_code='$itmcode1[1]' and user='$userid'");
	mysqli_query($link,"insert into inv_substore_order_details_temp values('$orderno','$itmcode1[1]','$substoreid','$orqnt','$orqnt','$ordrdate',0,'$userid')");
	
}

////////////////////////////////
elseif($type=="purcahse_order_final") //for  Purchase order temp
{
	$supplrid=$_POST['supplrid'];
	$orderno=$_POST['orderno'];
	$ordrdate=$_POST['ordrdate'];
	$btnvalue=$_POST['btnvalue'];
	
	if($btnvalue=="Done")
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
		
		$orderno=$bill_no."/".$dis_year_sm;
	  
	  
	  
	  mysqli_query($link,"update inv_purchase_order_details_temp set order_no='$orderno' where order_date='$ordrdate' and user='$userid'");
     }
	
	
	
	mysqli_query($link,"delete from inv_purchase_order_details where order_no ='$orderno' ");
	mysqli_query($link,"delete from inv_purchase_order_master where order_no ='$orderno' ");
	
	mysqli_query($link,"insert into inv_purchase_order_details(`order_no`,`item_id`,`supplier_id`,`order_qnt`,`order_date`,`stat`,`user`) select * from inv_purchase_order_details_temp  where order_no='$orderno' and supplier_id='$supplrid' ");
	mysqli_query($link,"insert into inv_purchase_order_master(`order_no`,`supplier_id`,`order_date`,`stat`,`del`,`user`,`time`) values('$orderno','$supplrid','$ordrdate',0,0,'$userid','$time')");
	
	mysqli_query($link,"delete from inv_purchase_order_details_temp where user='$userid' ");
	
}

////////////////////////////////
elseif($type=="purchase_order_tmp") //for  substore order temp
{
	$ordrdate=$_POST['ordrdate'];
	$supplrid=$_POST['supplrid'];
	$orderno=$_POST['orderno'];
	$itmcode=$_POST['itmcode'];
	$orqnt=$_POST['orqnt'];
	$itmcode1=explode("-#",$itmcode);
	
	
	mysqli_query($link,"delete from inv_purchase_order_details_temp where order_no ='$orderno'  and item_id='$itmcode1[1]' and  user='$userid' ");
	mysqli_query($link,"insert into inv_purchase_order_details_temp values('$orderno','$itmcode1[1]','$supplrid','$orqnt','$ordrdate',0,'$userid')");
	
}

////////////////////////////////
elseif($type=="supplier_payment") //for  supplier payment
{
	//~ $date=$_POST['date'];
	//~ $supplrid=$_POST['supplrid'];
	//~ $billno=$_POST['billno'];
	//~ $ttlamt=$_POST['ttlamt'];
	//~ $alrdypaid=$_POST['alrdypaid'];
	//~ $nwpaid=$_POST['nwpaid'];
	//~ $balance=$_POST['balance'];
	//~ $ptype=$_POST['ptype'];
	//~ $chqno=$_POST['chqno'];
	//~ $adjst=0;
	
	//~ $qchk=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_payment_master where supp_code='$supplrid' and bill_no='$billno'"));	
	//~ if($qchk)
	//~ {
		//~ $vpaid=$qchk['paid']+$nwpaid;
		//~ $vbal=$qchk['total_amount']-$qchk['adjustment']-$vpaid;
		//~ mysqli_query($link,"update inv_supplier_payment_master set paid='$vpaid',balance='$vbal' where supp_code='$supplrid' and bill_no='$billno' ");
		//~ mysqli_query($link,"insert into inv_supplier_payment_details (`supp_code`, `bill_no`, `amount`, `payment_mode`, `cheque_no`, `user`, `date`, `time`) values('$supplrid','$billno','$nwpaid','$ptype','$chqno','$userid','$date','$time')");
	//~ }
	//~ else
	//~ {
		//~ mysqli_query($link,"insert into inv_supplier_payment_master (`supp_code`, `bill_no`, `total_amount`, `paid`, `adjustment`, `balance`, `date`) values('$supplrid','$billno','$ttlamt','$nwpaid','$adjst','$balance','$date')");
	    //~ mysqli_query($link,"insert into inv_supplier_payment_details (`supp_code`, `bill_no`, `amount`, `payment_mode`, `cheque_no`, `user`, `date`, `time`) values('$supplrid','$billno','$nwpaid','$ptype','$chqno','$userid','$date','$time')");
	//~ }
	
	
	
	$billno=$_POST['blno'];
	$spplirid=$_POST['spplirid'];
	$paydate=$_POST['paydate'];
	$ptype=$_POST['ptype'];
	$chqno=$_POST['chqno'];
	$acno=$_POST['acno'];
	$date1=date('Y-m-d');
	
	$billno=$_POST['blno'];
	$billno=explode("@#",$billno);
	$bnk=mysqli_fetch_array(mysqli_query($link,"select bank_id from bank_ac_no where  account_no='$acno' "));
	foreach($billno as $blno)
	{		
		if($blno)
		{
		   $blnonw=explode("%%",$blno);
		  		
			$ttlamt=$blnonw[2];
			$adjst=0;  
			
			$qchk=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_payment_master where supp_code='$spplirid' and bill_no='$blnonw[0]'"));	
			if($qchk)
			{
				$vpaid=$qchk['paid']+$blnonw[1];
				$vbal=$qchk['total_amount']-$qchk['adjustment']-$vpaid;
				mysqli_query($link,"update inv_supplier_payment_master set paid='$vpaid',balance='$vbal' where supp_code='$spplirid' and bill_no='$blnonw[0]' ");
				mysqli_query($link,"insert into inv_supplier_payment_details (`supp_code`, `bill_no`, `amount`, `payment_mode`, `cheque_no`, `bank_id`, `account_no`, `user`, `date`, `entry_date`, `time`) values('$spplirid','$blnonw[0]','$blnonw[1]','$ptype','$chqno','$bnk[bank_id]','$acno','$userid','$paydate','$date1','$time')");
			}
			else
			{
				$vbal=$ttlamt-$blnonw[1];
				mysqli_query($link,"insert into inv_supplier_payment_master (`supp_code`, `bill_no`, `total_amount`, `paid`, `adjustment`, `balance`, `date`) values('$spplirid','$blnonw[0]','$ttlamt','$blnonw[1]','$adjst','$vbal','$paydate')");
				mysqli_query($link,"insert into inv_supplier_payment_details (`supp_code`, `bill_no`, `amount`, `payment_mode`, `cheque_no`, `bank_id`, `account_no`, `user`, `date`, `entry_date`, `time`) values('$spplirid','$blnonw[0]','$blnonw[1]','$ptype','$chqno','$bnk[bank_id]','$acno','$userid','$paydate','$date1','$time')");
			}
		
			   
		}
	}
	
	
}


///////////////////////////////
elseif($type=="supplier_op_balance") //for  supplier payment
{
	$date=$_POST['date'];
	$supplrid=$_POST['supplrid'];
	$opbalance=$_POST['opbalance'];
	
	
	$qchk=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_opening_balance where supp_code='$supplrid' "));	
	if($qchk)
	{
		
		mysqli_query($link,"update inv_supplier_opening_balance set op_balance='$opbalance' where supp_code='$supplrid' ");
		
	}
	else
	{
		mysqli_query($link,"insert into inv_supplier_opening_balance (`supp_code`, `op_balance`, `date`, `user`, `time`) values('$supplrid','$opbalance','$date','$userid','$time')");
	    
	}
	
	
}
////////////////////////////////
elseif($type=="invsubstritmissue") //for  substore item issue temp
{
	$issuedate=$_POST['issuedate'];
	$substoreid=$_POST['substoreid'];
	$issueno=$_POST['issueno'];
	$issueto=$_POST['issueto'];
	$itmcode=$_POST['itmcode'];
	$isueqnt=$_POST['isueqnt'];
	
	$itmcode1=explode("-#",$itmcode);
		
	mysqli_query($link,"delete from inv_substore_issue_details_temp where issu_no ='$issueno' and substore_id='$substoreid' and item_code='$itmcode1[1]'");
	mysqli_query($link,"insert into inv_substore_issue_details_temp values('$issueno','$itmcode1[1]','$substoreid','$issueto','$isueqnt','$issuedate','$userid','$time')");
	
}

////////////////////////////////
elseif($type=="invmainstritmissue") //for  mainstore item issue temp
{
	$issuedate=$_POST['issuedate'];
	$substoreid=$_POST['substoreid'];
	$issueno=$_POST['issueno'];
	$issueto=$_POST['issueto'];
	$itmcode=$_POST['itmcode'];
	$btchno=$_POST['btchno'];
	$isueqnt=$_POST['isueqnt'];
	
	$itmcode1=explode("-#",$itmcode);
		
	mysqli_query($link,"delete from inv_mainstore_issue_details_tmp where issu_no ='$issueno' and substore_id='$substoreid' and item_id='$itmcode1[1]' and batch_no='$btchno' and  user='$userid'");
	mysqli_query($link,"insert into inv_mainstore_issue_details_tmp values('$issueno','$itmcode1[1]','$btchno','$substoreid','$issueto','$isueqnt','$issuedate','$userid','$time')");
	
}


////////////////////////////////
elseif($type=="invitemretruntospplr_tmp") //for  Item Return To Supplier temp
{
	$date=$_POST['date'];
	$spplrid=$_POST['spplrid'];
	$returnno=$_POST['returnno'];
	$reason=$_POST['reason'];
	$itmcode=$_POST['itmcode'];
	$btchno=$_POST['btchno'];
	$rtrnqnt=$_POST['rtrnqnt'];
	$expiry=$_POST['expiry'];
	
	$itmcode1=explode("-#",$itmcode);
	
	
	$qchk=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price,gst_per from inv_main_stock_received_detail where item_id='$itmcode1[1]' and recept_batch='$btchno' and SuppCode='$spplrid'"));
	if(!$qchk)
	{
		$qchk=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price,gst_per from inv_main_stock_received_detail where item_id='$itmcode1[1]' and recept_batch='$btchno' "));
		
	}
	$i=1;	
	$vitmamt=$qchk['recept_cost_price']*$rtrnqnt;
	$gstamt=$vitmamt*$qchk['gst_per']/100;
	
	$qmax=mysqli_fetch_array(mysqli_query($link,"select max(slno) as maxsl from inv_item_return_supplier_detail_temp where 	returnr_no='$returnno'"));
	$i=$qmax['maxsl']+1;
	
	mysqli_query($link,"delete from inv_item_return_supplier_detail_temp where returnr_no ='$returnno' and  supplier_id='$spplrid' and item_id='$itmcode1[1]' and batch_no='$btchno' and  user='$userid'");
	mysqli_query($link,"insert into inv_item_return_supplier_detail_temp values('$i','$returnno','$reason','$itmcode1[1]','$expiry','$date','$rtrnqnt',0,'$btchno','$spplrid','$qchk[recpt_mrp]','$qchk[recept_cost_price]','$vitmamt',0,0,'$qchk[gst_per]','$gstamt','$userid')");
	
}


//////////////////////////////////////////////
if($type=="inv_bill_cancel")
{
	
	$rcptno=$_POST['rcptno'];
	$blno=$_POST['blno'];
	$spllrid=$_POST['spllrid'];
	$billentrydate=$_POST['billentrydate'];
	$reason=$_POST['reason'];
	
	$qselct=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_main_stock_received_master` WHERE receipt_no='$rcptno' and `bill_no`='$blno' and `recpt_date`='$billentrydate' and `supp_code`='$spllrid' "));
	
	mysqli_query($link,"insert into inv_main_bill_cancel_master(`receipt_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `cancel_by_user`, `cancel_date`, `cancel_reason`, `cancel_time`) values('$qselct[receipt_no]','$qselct[bill_date]','$qselct[recpt_date]','$qselct[bill_amount]','$qselct[gst_amt]','$qselct[dis_amt]','$qselct[net_amt]','$spllrid','$blno','$qselct[user]','$qselct[time]','$userid','$date','$reason','$time') ");
	
	mysqli_query($link,"insert into `inv_main_bill_cancel_detail` (`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) select  `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`  FROM `inv_main_stock_received_detail` WHERE rcv_no='$rcptno' and `bill_no`='$blno' and `SuppCode`='$spllrid' and `recpt_date`='$billentrydate'");
			
		
	////////////For stock''''''''''''''''''''
		
	$qrstk=mysqli_query($link,"select * from inv_main_stock_received_detail  where rcv_no='$rcptno' and bill_no ='$blno' and SuppCode='$spllrid' and recpt_date='$billentrydate' order by item_id");
	
	while($qrstk1=mysqli_fetch_array($qrstk))
	{
	  $vqnt=$qrstk1['recpt_quantity']+$qrstk1['free_qnt'];
      $vst=0;
       
	 	$vstkqnt=0;
		$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$qrstk1[item_id]' and   batch_no='$qrstk1[recept_batch]'  order by  date "));
		
		if($qrstkmaster['item_id']!='')
		{
			$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			$isuqnt=$qrstkmaster['issu_qnty']+$vqnt;
			
			mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[recept_batch]' ");
			
			mysqli_query($link,"UPDATE `inv_maincurrent_stock` SET `closing_stock`='$vstkqnt' WHERE `item_id`='$qrstk1[item_id]' and `batch_no`='$qrstk1[recept_batch]' ");
		}
		else///for if data not found
		{
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_id='$qrstk1[item_id]' and batch_no='$qrstk1[recept_batch]' order by slno desc limit 0,1"));
			
						
			$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
				
			mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$qrstk1[item_id]','$qrstk1[recept_batch]','$date','$qrstkmaster[closing_qnty]',0,'$vqnt','$vstkqnt')");
			
			mysqli_query($link,"UPDATE `inv_maincurrent_stock` SET `closing_stock`='$vstkqnt' WHERE `item_id`='$qrstk1[item_id]' and `batch_no`='$qrstk1[recept_batch]' ");
			
		}
	}
	
	mysqli_query($link,"update `inv_main_stock_received_master` set del='1' WHERE receipt_no='$rcptno' and `bill_no`='$blno' and `recpt_date`='$billentrydate' and `supp_code`='$spllrid'");
	mysqli_query($link,"update `inv_purchase_order_master` set stat='0' WHERE order_no='$qselct[order_no]' and `supplier_id`='$spllrid' ");
	mysqli_query($link,"update `inv_purchase_order_details` set stat='0' WHERE order_no='$qselct[order_no]' ");
	mysqli_query($link,"DELETE FROM `inv_main_stock_received_detail` WHERE  rcv_no='$rcptno' and `bill_no`='$blno' and `recpt_date`='$billentrydate' and `SuppCode`='$spllrid' ");
}

//////////////////////////////////////////////
if($type=="invsubstritmissue_final")
{
	
	$issuedate=$_POST['issuedate'];
	$substoreid=$_POST['substoreid'];
	$issueno=$_POST['issueno'];
	$issueto=$_POST['issueto'];
	
	mysqli_query($link,"delete from inv_substore_issue_details where issu_no ='$issueno' and substore_id='$substoreid' ");
	mysqli_query($link,"insert into inv_substore_issue_details select * from inv_substore_issue_details_temp where issu_no ='$issueno' and substore_id='$substoreid' ");
			
	//////////end mrp///////////////////////////
	mysqli_query($link,"delete from inv_substore_issue_details_temp where issu_no ='$issueno' and substore_id='$substoreid' ");
	////////////For stock''''''''''''''''''''
		
	$qrstk=mysqli_query($link,"select * from inv_substore_issue_details  where issu_no ='$issueno' and substore_id='$substoreid' order by item_code");
	while($qrstk1=mysqli_fetch_array($qrstk))
	{
	  $vqnt=$qrstk1['issue_qnt'];
             
       $vst=0;
       
	 	$vstkqnt=0;
		$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_substorestock_details where substore_id='$substoreid' and  date='$issuedate' and item_code='$qrstk1[item_code]'  order by  date "));
		if($qrstkmaster['item_code']!='')
		{
			$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			$isuqnt=$qrstkmaster['issu_qnty']+$vqnt;
			
			mysqli_query($link,"update inv_substorestock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$issuedate' and item_code='$qrstk1[item_code]' ");
			mysqli_query($link,"delete from inv_substorecurrent_stock where  item_code='$qrstk1[item_code]' and substore_id='$substoreid' ");
			mysqli_query($link,"insert into inv_substorecurrent_stock values('$qrstk1[item_code]','$vstkqnt','$substoreid')");
		}
		else///for if data not found
		{
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_substorestock_details where  item_code='$qrstk1[item_code]' and substore_id='$substoreid'  order by slno desc"));
			$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			mysqli_query($link,"insert into inv_substorestock_details(substore_id,item_code,date,op_qnty,recv_qnty,issu_qnty,closing_qnty) values('$substoreid','$qrstk1[item_code]','$issuedate','$qrstkmaster[closing_qnty]',0,'$vqnt','$vstkqnt')");
			mysqli_query($link,"delete from inv_substorecurrent_stock where  item_code='$qrstk1[item_code]' and substore_id='$substoreid' ");
			mysqli_query($link,"insert into inv_substorecurrent_stock values('$qrstk1[item_code]','$vstkqnt','$substoreid')");
		}
	}
}

//~ //////////////////////////////////////////////
//~ if($type=="invmainstritmissue_final")
//~ {
	
	//~ $issuedate=$_POST['issuedate'];
	//~ $substoreid=$_POST['substoreid'];
	//~ $issueno=$_POST['issueno'];
	//~ $issueto=$_POST['issueto'];
	
	//~ $vid=nextId("ISU","inv_mainstore_direct_issue_master","issue_no","100");
	//~ $issueno=$vid;
	
	//~ mysqli_query($link,"update inv_mainstore_issue_details_tmp set issu_no='$issueno' where substore_id='$substoreid' and user='$userid' and issue_date='$issuedate'");
	//~ mysqli_query($link,"delete from inv_mainstore_issue_details where order_no ='$issueno' and substore_id='$substoreid' ");
	//~ mysqli_query($link,"insert into inv_mainstore_issue_details(`order_no`,`item_id`,`batch_no`,`substore_id`,`issue_to`,`issue_qnt`,`issue_date`,`user`,`time`) select * from inv_mainstore_issue_details_tmp where issu_no ='$issueno' and substore_id='$substoreid' and user='$userid' ");
	
	//~ mysqli_query($link,"delete from inv_mainstore_issue_details_tmp where issu_no ='$issueno' and substore_id='$substoreid' ");
	//~ mysqli_query($link,"delete from inv_mainstore_direct_issue_master where issue_no ='$issueno'  ");
	//~ mysqli_query($link,"insert into inv_mainstore_direct_issue_master(`issue_no`,`substore_id`,`user`,`date`,`time`) values('$issueno','$substoreid','$userid','$issuedate','$time')");
	
	//~ //////////end mrp///////////////////////////
	
	//~ ////////////For stock''''''''''''''''''''
		
	//~ $qrstk=mysqli_query($link,"select * from inv_mainstore_issue_details  where order_no ='$issueno' and substore_id='$substoreid' order by item_id");
		
	//~ while($qrstk1=mysqli_fetch_array($qrstk))
	//~ {
		//~ $vqnt=$qrstk1['issue_qnt'];
		//~ $vst=0;
		//~ $vstkqnt=0;
		//~ $gstamt=0;
	 	
	 	//~ //////////////for sub store///
	 	
	 	//~ $qdetails=mysqli_fetch_array(mysqli_query($link,"select * from inv_main_stock_received_detail where item_id='$qrstk1[item_id]' and recept_batch='$qrstk1[batch_no]' order by slno desc"));
	 	//~ $vitmamt=$qrstk1['issue_qnt']*$qdetails['recept_cost_price'];
	 	
	 	//~ mysqli_query($link,"insert into ph_purchase_receipt_details(`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$issueno','$issueno','$qrstk1[item_id]','0000-00-00','$qdetails[expiry_date]','$issuedate','$vqnt',0,'$qrstk1[batch_no]','','$qdetails[recpt_mrp]','$qdetails[recept_cost_price]','$qdetails[sale_price]',0,'$vitmamt',0,0,'$qdetails[gst_per]','$gstamt')");
	 	
	 	//~ //mysqli_query($link,"insert into inv_substorestock_rcv_details (item_code,order_no,substore_id,rcv_qnt,date) values('$qrstk1[item_code]','$issueno','$substoreid','$vqnt','$issuedate')");
	 	
	 	//~ $qsubstr1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$substoreid' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and date='$issuedate' "));
	 	
			//~ if($qsubstr1['item_code']!='')
			//~ {
				//~ $vsubsrstkqnt=$qsubstr1['s_remain']+$vqnt;
				//~ $vsubsrrcvqnt=$qsubstr1['added']+$vqnt;
				//~ mysqli_query($link,"update ph_stock_process set s_remain='$vsubsrstkqnt',added='$vsubsrrcvqnt' where  date='$issuedate' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='$substoreid' ");
				
				//~ mysqli_query($link,"delete from ph_stock_master where item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='$substoreid'");
				//~ mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substoreid','$qrstk1[item_id]','$qrstk1[batch_no]','$vsubsrstkqnt','0000-00-00','$qdetails[expiry_date]') ");
			//~ }
			//~ else
			//~ {
					//~ $vclqnt=0;
					//~ $vopqnt=0;
					//~ $qchk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$substoreid' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' order by slno desc "));
					//~ if($qchk)
					//~ {
						//~ $vopqnt=$qchk['s_remain'];
						//~ $vclsnqnt=$qchk['s_remain']+$vqnt;
					//~ }
					//~ else
					//~ {
						//~ $vclsnqnt=$vqnt;
						
					//~ }
					
					//~ mysqli_query($link,"insert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substoreid','Direct','$qrstk1[item_id]','$qrstk1[batch_no]','$qchk[s_remain]','$vqnt',0,0,0,'$vclsnqnt','$issuedate')");
					
					//~ mysqli_query($link,"delete from ph_stock_master where item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='$substoreid'");
				//~ mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substoreid','$qrstk1[item_id]','$qrstk1[batch_no]','$vclsnqnt','0000-00-00','$qdetails[expiry_date]') ");
			//~ }
	 	//~ /////end////////////////
	 	
		//~ $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$issuedate' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  order by  date desc "));
		//~ if($qrstkmaster['item_id']!='')
		//~ {
			//~ $vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			//~ $isuqnt=$qrstkmaster['issu_qnty']+$vqnt;
			
			//~ mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$issuedate' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
			
			//~ mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
			//~ mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$qrstk1[item_id]','$qrstk1[batch_no]','$vstkqnt','$qdetails[expiry_date]')");
		//~ }
		//~ else///for if data not found
		//~ {
			//~ $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_id='$qrstk1[item_id]' and  batch_no='$qrstk1[batch_no]'   order by slno desc"));
			//~ $vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			 //~ mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$qrstk1[item_id]','$qrstk1[batch_no]','$issuedate','$qrstkmaster[closing_qnty]',0,'$vqnt','$vstkqnt') ");
			
			//~ mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
			//~ mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$qrstk1[item_id]','$qrstk1[batch_no]','$vstkqnt','$qdetails[expiry_date]')");
		//~ }
	//~ }
//~ }
//////////////////////////////////////////////
if($type=="invmainstritmissue_final")
{
	
	$issuedate=$_POST['issuedate'];
	$substoreid=$_POST['substoreid'];
	$issueno=$_POST['issueno'];
	$issueto=$_POST['issueto'];
	
	$vid=nextId("ISU","inv_mainstore_direct_issue_master","issue_no","100");
	$issueno=$vid;
	
	mysqli_query($link,"update inv_mainstore_issue_details_tmp set issu_no='$issueno' where substore_id='$substoreid' and user='$userid' and issue_date='$issuedate'");
	mysqli_query($link,"delete from inv_mainstore_issue_details where order_no ='$issueno' and substore_id='$substoreid' ");
	mysqli_query($link,"insert into inv_mainstore_issue_details(`order_no`,`item_id`,`batch_no`,`substore_id`,`issue_to`,`issue_qnt`,`issue_date`,`user`,`time`) select * from inv_mainstore_issue_details_tmp where issu_no ='$issueno' and substore_id='$substoreid' and user='$userid' ");
	
	mysqli_query($link,"delete from inv_mainstore_issue_details_tmp where issu_no ='$issueno' and substore_id='$substoreid' ");
	mysqli_query($link,"delete from inv_mainstore_direct_issue_master where issue_no ='$issueno'  ");
	mysqli_query($link,"insert into inv_mainstore_direct_issue_master(`issue_no`,`substore_id`,`user`,`date`,`time`) values('$issueno','$substoreid','$userid','$issuedate','$time')");
	
	//////////end mrp///////////////////////////
	
	////////////For stock''''''''''''''''''''
		
	$qrstk=mysqli_query($link,"select * from inv_mainstore_issue_details  where order_no ='$issueno' and substore_id='$substoreid' order by item_id");
	//////////////////////////
	if($substoreid=="1")
	{
		while($qrstk1=mysqli_fetch_array($qrstk))
		{
			$vqnt=$qrstk1['issue_qnt'];
			$issue_to=$qrstk1['issue_to'];
			$batch_no=$qrstk1['batch_no'];
			$item_id=$qrstk1['item_id'];
			mysqli_query($link,"insert into inv_mainstore_issue_details_cnf(`order_no`,`item_id`,`batch_no`,`substore_id`,`issue_to`,`issue_qnt`,`issue_date`,`user`,`time`, `status`, `rcv_by`, `rcv_date`, `rcv_time`)  VALUES ('$issueno','$item_id','$batch_no','$substoreid','$issue_to','$vqnt','$issuedate','$userid','$time','0','0','','')");
		}
		//mysqli_query($link,"delete from inv_mainstore_issue_details where order_no ='$issueno' and substore_id='$substoreid' ");
	}
	else
	{
		///////////////	
	while($qrstk1=mysqli_fetch_array($qrstk))
	{
		$vqnt=$qrstk1['issue_qnt'];
		$vst=0;
		$vstkqnt=0;
		$gstamt=0;
	 	
	 	//////////////for sub store///
	 	
	 	$qdetails=mysqli_fetch_array(mysqli_query($link,"select * from inv_main_stock_received_detail where item_id='$qrstk1[item_id]' and recept_batch='$qrstk1[batch_no]' order by slno desc"));
	 	$vitmamt=$qrstk1['issue_qnt']*$qdetails['recept_cost_price'];
	 	
	 	mysqli_query($link,"insert into ph_purchase_receipt_details(`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$issueno','$issueno','$qrstk1[item_id]','0000-00-00','$qdetails[expiry_date]','$issuedate','$vqnt',0,'$qrstk1[batch_no]','','$qdetails[recpt_mrp]','$qdetails[recept_cost_price]','$qdetails[sale_price]',0,'$vitmamt',0,0,'$qdetails[gst_per]','$gstamt')");
	 	
	 	//mysqli_query($link,"insert into inv_substorestock_rcv_details (item_code,order_no,substore_id,rcv_qnt,date) values('$qrstk1[item_code]','$issueno','$substoreid','$vqnt','$issuedate')");
	 	
	 	$qsubstr1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$substoreid' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and date='$issuedate' "));
	 	
			if($qsubstr1['item_code']!='')
			{
				$vsubsrstkqnt=$qsubstr1['s_remain']+$vqnt;
				$vsubsrrcvqnt=$qsubstr1['added']+$vqnt;
				mysqli_query($link,"update ph_stock_process set s_remain='$vsubsrstkqnt',added='$vsubsrrcvqnt' where  date='$issuedate' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='$substoreid' ");
				
				mysqli_query($link,"delete from ph_stock_master where item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='$substoreid'");
				mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substoreid','$qrstk1[item_id]','$qrstk1[batch_no]','$vsubsrstkqnt','0000-00-00','$qdetails[expiry_date]') ");
			}
			else
			{
					$vclqnt=0;
					$vopqnt=0;
					$qchk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$substoreid' and item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' order by slno desc "));
					if($qchk)
					{
						$vopqnt=$qchk['s_remain'];
						$vclsnqnt=$qchk['s_remain']+$vqnt;
					}
					else
					{
						$vclsnqnt=$vqnt;
						
					}
					
					mysqli_query($link,"insert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substoreid','Direct','$qrstk1[item_id]','$qrstk1[batch_no]','$qchk[s_remain]','$vqnt',0,0,0,'$vclsnqnt','$issuedate')");
					
					mysqli_query($link,"delete from ph_stock_master where item_code='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' and substore_id='$substoreid'");
				mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substoreid','$qrstk1[item_id]','$qrstk1[batch_no]','$vclsnqnt','0000-00-00','$qdetails[expiry_date]') ");
			}
	 	/////end////////////////
	 	
		$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$issuedate' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  order by  date desc "));
		if($qrstkmaster['item_id']!='')
		{
			$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			$isuqnt=$qrstkmaster['issu_qnty']+$vqnt;
			
			mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$issuedate' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
			
			mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
			mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$qrstk1[item_id]','$qrstk1[batch_no]','$vstkqnt','$qdetails[expiry_date]')");
		}
		else///for if data not found
		{
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_id='$qrstk1[item_id]' and  batch_no='$qrstk1[batch_no]'   order by slno desc"));
			$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			 mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$qrstk1[item_id]','$qrstk1[batch_no]','$issuedate','$qrstkmaster[closing_qnty]',0,'$vqnt','$vstkqnt') ");
			
			mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
			mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$qrstk1[item_id]','$qrstk1[batch_no]','$vstkqnt','$qdetails[expiry_date]')");
		}
	}
 }
}


//////////////////////////////////////////////
if($type=="invitemretruntospplr_final")
{
	
	$date=$_POST['date'];
	$spplrid=$_POST['spplrid'];
	$returnno=$_POST['returnno'];
	$reason=$_POST['reason'];
	
	
	mysqli_query($link,"delete from inv_item_return_supplier_detail where returnr_no ='$issueno' and  supplier_id='$substoreid' ");
	mysqli_query($link,"insert into inv_item_return_supplier_detail( `returnr_no`, `reason`, `item_id`, `expiry_date`, `date`, `quantity`, `free_qnt`, `batch_no`, `supplier_id`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) select `returnr_no`, `reason`, `item_id`, `expiry_date`, `date`, `quantity`, `free_qnt`, `batch_no`, `supplier_id`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount` from inv_item_return_supplier_detail_temp where returnr_no ='$returnno' and supplier_id='$spplrid' and user='$userid' ");
	
	mysqli_query($link,"delete from inv_item_return_supplier_detail_temp where returnr_no ='$returnno' and supplier_id='$spplrid' ");
	mysqli_query($link,"delete from inv_item_return_supplier_master where returnr_no ='$returnno'  ");
	mysqli_query($link,"insert into inv_item_return_supplier_master(`returnr_no`, `supplier_id`, `date`, `stat`, `del`, `user`, `time`) values('$returnno','$spplrid','$date',0,0,'$userid','$time')");
	
	//////////end mrp///////////////////////////
	
	////////////For stock''''''''''''''''''''
		
	$qrstk=mysqli_query($link,"select * from inv_item_return_supplier_detail  where returnr_no ='$returnno' and supplier_id='$spplrid' order by item_id");
		
	while($qrstk1=mysqli_fetch_array($qrstk))
	{
		$vqnt=$qrstk1['quantity'];
		$vitmamt=$qrstk1['item_amount'];
		$vst=0;
		$vstkqnt=0;
		$gstamt=0;
	 	
	 	
		$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'  order by  date desc "));
		if($qrstkmaster['item_id']!='')
		{
			$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			$isuqnt=$qrstkmaster['issu_qnty']+$vqnt;
			
			mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$date' and item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]' ");
			
			mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'");
			
			
		}
		else///for if data not found
		{
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_id='$qrstk1[item_id]' and  batch_no='$qrstk1[batch_no]'   order by slno desc"));
			$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
			 mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$qrstk1[item_id]','$qrstk1[batch_no]','$date','$qrstkmaster[closing_qnty]',0,'$vqnt','$vstkqnt') ");
			
			 mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$qrstk1[item_id]' and batch_no='$qrstk1[batch_no]'");
		}
	}
}
////////////////////////////////
elseif($type=="indent_ord_temp") //for  purchase order temp
{
	$supplr=$_POST['supplr'];
	$orderno=$_POST['orderno'];
	$itmcode=$_POST['itmcode'];
	$orqnt=$_POST['orqnt'];
	$ordrdate=$_POST['ordrdate'];
	$itmcode1=explode("-",$itmcode);
	//echo $itmcode1['1'];
	mysqli_query($link,"delete from inv_indent_order_details_temp where order_no ='$orderno' and item_code='$itmcode1[1]'");
	mysqli_query($link,"insert into inv_indent_order_details_temp values('$orderno','$itmcode1[1]','$supplr','$orqnt','$orqnt','$ordrdate','','$fd')");
	
}

////////////////////////////////
elseif($type=="substore_order") //for  Indent sub store order Complete
{
	$substoreid=$_POST['substoreid'];
	$orderno=$_POST['orderno'];
	$vdate=$_POST['ordrdate'];
	
	mysqli_query($link,"delete from  inv_substore_order_details where order_no ='$orderno' and 	substore_id='$substoreid' ");
	mysqli_query($link,"delete from  inv_substore_indent_order_master where order_no ='$orderno' and substore_id='$substoreid' ");
	
	mysqli_query($link,"insert into inv_substore_order_details(`order_no`,`item_id`,`substore_id`,`order_qnt`,`bl_qnt`,`order_date`,`stat`) select `order_no`,`item_id`,`substore_id`,`order_qnt`,`bl_qnt`,`order_date`,`stat` from inv_substore_order_details_temp where order_no='$orderno' and substore_id='$substoreid' and user='$userid'");
	
	mysqli_query($link,"insert into inv_substore_indent_order_master ( `order_no`,`substore_id`,`order_date`,`stat`,`user`,`time`) values('$orderno','$substoreid','$vdate',0,'$userid','$time' )");
	
	mysqli_query($link,"delete from  inv_substore_order_details_temp where 	order_no ='$orderno' and substore_id='$substoreid' ");
	
}

////////////////////////////////
elseif($type=="indent_order_final") //for  Indent order Complete
{
	$supplr=$_POST['supplr'];
	$orderno=$_POST['orderno'];
	$vdate=$_POST['ordrdate'];
	$fid=$_POST['fid'];
		
	mysqli_query($link,"delete from  inv_indent_order_details where order_no ='$orderno' and SuppCode='$supplr' ");
	mysqli_query($link,"delete from  inv_indent_order_master where order_no ='$orderno' and SuppCode='$supplr' ");
	
	mysqli_query($link,"insert into inv_indent_order_details select * from inv_indent_order_details_temp where order_no='$orderno' and SuppCode='$supplr'");
	mysqli_query($link,"insert into inv_indent_order_master values('$orderno','$supplr','$vdate','$fid' )");
	
	mysqli_query($link,"delete from  inv_indent_order_details_temp where order_no ='$orderno' and SuppCode='$supplr' ");
	
	
}

////////////////////////////////
elseif($type=="subtore") //for  sub store
{
	$vid=$_POST['vid'];
	$name=$_POST['name'];
	$name=str_replace("'", "''", "$name");
	
	mysqli_query($link,"delete from  inv_sub_store where substore_id ='$vid'  ");
	mysqli_query($link,"insert into inv_sub_store values('$vid','$name') ");
	
}

////////////////////////////////////

if($_POST["type"]=="mainstkentry_tmp")
{
	$splrcode=$_POST['spplrid'];
	$orderno=$_POST['orderno'];
	$billno=$_POST['billno'];
	$itmid=$_POST['itmid'];
	$qnt=$_POST['qnt'];
	$freeqnt=$_POST['freeqnt'];
	$mrp=$_POST['mrp'];
	$rate=$_POST['rate'];
	$gst=$_POST['gst'];
	$ttl=$_POST['itmamt'];
	$vdate=date('Y-m-d');
	$itmcode1=explode("-",$itmid);
    $gstamt=$ttl*$gst/100;
	mysqli_query($link,"delete from inv_main_stock_receied_detail_temp where receipt_no='$orderno' and item_code='$itmcode1[1]' ");
	mysqli_query($link,"insert into inv_main_stock_receied_detail_temp values('$orderno','$billno','$itmcode1[1]','$qnt','$freeqnt','$mrp','$rate','$gst','$gstamt','$ttl','$vdate')");
	
}

//////////////////////////////////////////////
if($_POST["type"]=="mainstkentry_final")
{
	
	$orderno=$_POST['orderno'];
	$splrblno=$_POST['splrblno'];
	$spplrid=$_POST['spplrid'];
	$itm=$_POST['itm'];
	$entrydate=date('Y-m-d');
	$fid=0;
	$vttlamt=$_POST['billamt'];
	$gstamt=$_POST['gstamt'];
	$paid=0;
	$discount=0;
	$balance=0;
	
	
	mysqli_query($link,"delete from inv_main_stock_receied_detail where receipt_no ='$orderno' and supplier_bill_no='$splrblno' ");
	mysqli_query($link,"insert into inv_main_stock_receied_detail select * from inv_main_stock_receied_detail_temp where receipt_no ='$orderno' and supplier_bill_no='$splrblno' ");
	mysqli_query($link,"insert into inv_main_stock_receied_master(receipt_no,supplier_code,supplier_bill_no,total_amount,paid,discount,balance,user,date,time) values('$orderno','$spplrid','$splrblno','$vttlamt','$paid','$discount','$balance','$userid','$entrydate','$time')");
	
	
	//////////end mrp///////////////////////////
	mysqli_query($link,"delete from inv_main_stock_receied_detail_temp where receipt_no ='$orderno' and supplier_bill_no='$splrblno' ");
	////////////For stock''''''''''''''''''''
		
	$qrstk=mysqli_query($link,"select * from inv_main_stock_receied_detail  where receipt_no ='$orderno'  and supplier_bill_no='$splrblno' order by item_code");
	while($qrstk1=mysqli_fetch_array($qrstk))
	{
	  $vqnt=$qrstk1['qnty']+$qrstk1['free_qnty'];
             
       $vst=0;
       
	 	$vstkqnt=0;
		$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$entrydate' and item_code='$qrstk1[item_code]'  order by  date "));
		if($qrstkmaster['item_code']!='')
		{
			$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
			$rcvqnt=$qrstkmaster['recv_qnty']+$vqnt;
			
			mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where  date='$entrydate' and item_code='$qrstk1[item_code]' ");
			mysqli_query($link,"delete from inv_maincurrent_stock where  item_code='$qrstk1[item_code]' ");
			mysqli_query($link,"insert into inv_maincurrent_stock(item_code,closing_stock) values('$qrstk1[item_code]','$vstkqnt')");
		}
		else///for if data not found
		{
			$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_code='$qrstk1[item_code]'  order by slno desc"));
			$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
			mysqli_query($link,"insert into inv_mainstock_details(item_code,date,op_qnty,recv_qnty,issu_qnty,closing_qnty) values('$qrstk1[item_code]','$entrydate','$qrstkmaster[closing_qnty]','$vqnt',0,'$vstkqnt')");
			mysqli_query($link,"delete from inv_maincurrent_stock where  item_code='$qrstk1[item_code]' ");
			mysqli_query($link,"insert into inv_maincurrent_stock(item_code,closing_stock) values('$qrstk1[item_code]','$vstkqnt')");
		}
	}
}


////////////////////////////////
elseif($type=="indentrcvtmp") //for  Indent receipt temp
{
	
	$orderno=$_POST['orderno'];
	$mid=$_POST['mid'];
	$invc=$_POST['invc'];
	$invdate=$_POST['invdate'];
	$rcvdate=$_POST['rcvdate'];
	$btchno=$_POST['btchno'];
	$expridt=$_POST['expridt'];
	$qnt=$_POST['qnt'];
	$rate=$_POST['rate'];
	$prvqnt=$_POST['prvqnt'];
	$amount=$qnt*$rate;
	$vdate=date('Y/m/d');
	$qspplr=mysqli_fetch_array(mysqli_query($link,"select SuppCode from inv_indent_order_master where order_no='$orderno'"));
	
	mysqli_query($link,"delete from inv_indent_rcvdetails_temp where invoiceno='$invc' and item_code='$mid' and order_no='$orderno'");
	mysqli_query($link,"insert into inv_indent_rcvdetails_temp values('$orderno','$invc','$rcvdate','$invdate','$mid','$qnt','$rate','$amount','$prvqnt','$btchno','$expridt','$qspplr[SuppCode]')");
	
}
/////////////////////////////////////
elseif($type=="indntsubstfinlaprv") ///for indent substore approved
{

	$cid=$_POST['cid'];
	$date1=$_POST['date1'];
	$sbstrid=$_POST['sbstrid'];
	$billno=$_POST['blno'];
	$billno=explode("@#",$billno);
	foreach($billno as $blno)
	{		
		if($blno)
		{
		   $blnonw=explode("%%",$blno);
		   
		   mysqli_query($link,"insert into inv_substore_order_aprv_details values('$cid','$blnonw[0]','$sbstrid','$blnonw[1]','$blnonw[1]','$date1',0,'$fd')");
		   mysqli_query($link,"update inv_substore_order_details set stat='1' where order_no='$cid' and item_code='$blnonw[0]' and substore_id='$sbstrid'");
		   
		}
	}
	
	$qchk=mysqli_fetch_array(mysqli_query($link,"select item_code from inv_substore_order_details where order_no='$cid' and substore_id='$sbstrid' and stat='0' "));
	if(!$qchk)
	{
		mysqli_query($link,"update inv_substore_indent_order_master set stat='1' where order_no='$cid' and substore_id='$sbstrid' ");
	}
}

/////////////////////////////////////
elseif($type=="indntsubstrrcv") ///for indent substore received
{

	$cid=$_POST['cid'];
	$date1=$_POST['date1'];
	$sbstrid=$_POST['sbstrid'];
	$billno=$_POST['blno'];
	$billno=explode("@#",$billno);
	foreach($billno as $blno)
	{		
		if($blno)
		{
		   $blnonw=explode("%%",$blno);
		  
		  
		      $qexpiry=mysqli_fetch_array(mysqli_query($link,"select  expiry_date from inv_main_stock_received_detail where  item_id='$blnonw[0]' and  recept_batch='$blnonw[2]' order by slno desc limit 1"));
		    
			   $qstkrcv1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$date1' and item_code='$blnonw[0]' and  	batch_no='$blnonw[2]' and substore_id='$sbstrid'"));
			   
			   $qdetail=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price,sale_price,gst_per from inv_main_stock_received_detail where item_id='$blnonw[0]' and recept_batch='$blnonw[2]' order by slno desc limit 1"));
			   
			   $vitmamt=$blnonw[1]*$qdetail['recept_cost_price'];
			   
			   if($qstkrcv1)
			   {
				   $vrcvqnt=$qstkrcv1['added']+$blnonw[1];
				   $vclsnqnt=$qstkrcv1['s_remain']+$blnonw[1];
				   
			       mysqli_query($link,"update ph_stock_process set added='$vrcvqnt',s_remain='$vclsnqnt' where date='$date1' and item_code='$blnonw[0]' and batch_no='$blnonw[2]' and substore_id='$sbstrid'  ");
			       
			       mysqli_query($link,"insert into inv_mainstore_issue_details (`order_no`,`item_id`,`batch_no`,`substore_id`,`issue_to`,`issue_qnt`,`issue_date`,`user`,`time`) values('$cid','$blnonw[0]','$blnonw[2]','$sbstrid','','$blnonw[1]','$date1','$userid','$time')");
			       
			      
			       
			        mysqli_query($link,"insert into ph_purchase_receipt_details (`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$cid','$cid','$blnonw[0]','','$qexpiry[expiry_date]','$date1','$blnonw[1]',0,'$blnonw[2]','','$qdetail[recpt_mrp]','$qdetail[recept_cost_price]','$qdetail[sale_price]',0,'$vitmamt',0,0,'$qdetail[gst_per]',0)");
			      
			       mysqli_query($link,"delete from ph_stock_master where item_code='$blnonw[0]' and batch_no='$blnonw[2]' and substore_id='$sbstrid'");
			       mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$sbstrid','$blnonw[0]','$blnonw[2]','$vclsnqnt','0000-00-00','$qexpiry[expiry_date]') ");
			       mysqli_query($link,"update inv_substore_order_details set stat='1' where order_no='$cid' and  item_id='$blnonw[0]' and substore_id='$sbstrid'");
			       
			       ////for main stock////
			       $qmainstk=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$blnonw[0]' and batch_no='$blnonw[2]' and date='$date1'"));
			          if($qmainstk)
			             {
							 $vissuqnt=$qmainstk['issu_qnty']+$blnonw[1];
							 $Vminclsnqnt=$qmainstk['closing_qnty']-$blnonw[1];
							 mysqli_query($link,"update inv_mainstock_details set issu_qnty='$vissuqnt',closing_qnty='$Vminclsnqnt' where item_id='$blnonw[0]' and batch_no='$blnonw[2]' and date='$date1' ");
							 mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$blnonw[0]' and batch_no='$blnonw[2]' ");
							 mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$blnonw[0]','$blnonw[2]','$Vminclsnqnt','$qexpiry[expiry_date]')");
						 }
						 else
						 {
							$qmainstk1=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$blnonw[0]' and batch_no='$blnonw[2]'  order by slno DESC limit 1")); 
							   if($qmainstk1)
							     {
									 $Vminclsnqnt=$qmainstk1['closing_qnty']-$blnonw[1];
									 mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$blnonw[0]','$blnonw[2]','$date1','$qmainstk1[closing_qnty]',0,'$blnonw[1]','$Vminclsnqnt') ");
									 
									 mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$blnonw[0]' and batch_no='$blnonw[2]' ");
							         mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$blnonw[0]','$blnonw[2]','$Vminclsnqnt','$qexpiry[expiry_date]')");
								 }
								else
								{
									//mysqli_query($link,"insert into inv_mainstock_details (item_code,date,op_qnty,recv_qnty,issu_qnty,closing_qnty) value('$blnonw[0]','$date1','$qmainstk1[closing_qnty]',0,'$blnonw[1]','$Vminclsnqnt') ");
								} 
						 }
			       /////end/////
			   }
			   else
			   {
				   $qstkrcv2=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$blnonw[0]' and batch_no='$blnonw[2]' and substore_id='$sbstrid' order by date DESC limit 1"));
				   if($qstkrcv2)
				   {
					 $vclsnqnt=$qstkrcv2['s_remain']+$blnonw[1];  
					 mysqli_query($link,"insert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$sbstrid','$cid','$blnonw[0]','$blnonw[2]','$qstkrcv2[s_remain]','$blnonw[1]',0,0,0,'$vclsnqnt','$date1')");
					 
					mysqli_query($link,"insert into inv_mainstore_issue_details (`order_no`,`item_id`,`batch_no`,`substore_id`,`issue_to`,`issue_qnt`,`issue_date`,`user`,`time`) values('$cid','$blnonw[0]','$blnonw[2]','$sbstrid','','$blnonw[1]','$date1','$userid','$time')");
					
					mysqli_query($link,"insert into ph_purchase_receipt_details (`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$cid','$cid','$blnonw[0]','','$qexpiry[expiry_date]','$date1','$blnonw[1]',0,'$blnonw[2]','','$qdetail[recpt_mrp]','$qdetail[recept_cost_price]','$qdetail[sale_price]',0,'$vitmamt',0,0,'$qdetail[gst_per]',0)");
					
					  mysqli_query($link,"delete from ph_stock_master where item_code='$blnonw[0]' and batch_no='$blnonw[2]' and substore_id='$sbstrid'");
			          mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$sbstrid','$blnonw[0]','$blnonw[2]','$vclsnqnt','0000-00-00','$qexpiry[expiry_date]') ");
			          
			         mysqli_query($link,"update inv_substore_order_details set stat='1' where order_no='$cid' and item_id='$blnonw[0]' and substore_id='$sbstrid'");
				   }
				   else
				   {
				     mysqli_query($link,"insert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$sbstrid','$cid','$blnonw[0]','$blnonw[2]',0,'$blnonw[1]',0,0,0,'$blnonw[1]','$date1')");
				     
				     mysqli_query($link,"insert into inv_mainstore_issue_details (`order_no`,`item_id`,`batch_no`,`substore_id`,`issue_to`,`issue_qnt`,`issue_date`,`user`,`time`) values('$cid','$blnonw[0]','$blnonw[2]','$sbstrid','','$blnonw[1]','$date1','$userid','$time')");
				     
				     mysqli_query($link,"insert into ph_purchase_receipt_details (`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$cid','$cid','$blnonw[0]','','$qexpiry[expiry_date]','$date1','$blnonw[1]',0,'$blnonw[2]','','$qdetail[recpt_mrp]','$qdetail[recept_cost_price]','$qdetail[sale_price]',0,'$vitmamt',0,0,'$qdetail[gst_per]',0)");
				     
					  mysqli_query($link,"delete from ph_stock_master where item_code='$blnonw[0]' and batch_no='$blnonw[2]' and substore_id='$sbstrid'");
			          mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$sbstrid','$blnonw[0]','$blnonw[2]','$blnonw[1]','0000-00-00','$qexpiry[expiry_date]') ");
			       
			         mysqli_query($link,"update inv_substore_order_details set stat='1' where order_no='$cid' and item_id='$blnonw[0]' and substore_id='$sbstrid'");
			         
			         ////for main stock////
			       $qmainstk=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$blnonw[0]' and batch_no='$blnonw[2]' and date='$date1'"));
			          if($qmainstk)
			             {
							 $vissuqnt=$qmainstk['issu_qnty']+$blnonw[1];
							 $Vminclsnqnt=$qmainstk['closing_qnty']-$blnonw[1];
							 mysqli_query($link,"update inv_mainstock_details set issu_qnty='$vissuqnt',closing_qnty='$Vminclsnqnt' where item_id='$blnonw[0]' and batch_no='$blnonw[2]' and date='$date1'");
							 
							 mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$blnonw[0]' and batch_no='$blnonw[2]' ");
							 mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$blnonw[0]','$blnonw[2]','$Vminclsnqnt','$qexpiry[expiry_date]')");
						 }
						 else
						 {
							$qmainstk1=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$blnonw[0]' and batch_no='$blnonw[2]' order by date DESC limit 1")); 
							   if($qmainstk1)
							     {
									 $Vminclsnqnt=$qmainstk1['closing_qnty']-$blnonw[1];
									  mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$blnonw[0]','$blnonw[2]','$date1','$qmainstk1[closing_qnty]',0,'$blnonw[1]','$Vminclsnqnt') ");
									  
									 mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$blnonw[0]' and batch_no='$blnonw[2]' ");
							         mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$blnonw[0]','$blnonw[2]','$Vminclsnqnt','$qexpiry[expiry_date]')");
								 }
								else
								{
									// mysqli_query($link,"insert into inv_mainstock_details (item_code,date,op_qnty,recv_qnty,issu_qnty,closing_qnty) value('$blnonw[0]','$date1',0,'$blnonw[1]','$Vminclsnqnt') ");
								} 
						 }
			       /////end/////
			         
			        } 
			   }
		}
	}
	mysqli_query($link,"insert into inv_substorestock_aproved_details(order_no,substore_id,user,date,time) values('$cid','$sbstrid','$userid','$date1','$time')");
	$qchk=mysqli_fetch_array(mysqli_query($link,"select item_id from inv_substore_order_details where order_no='$cid' and substore_id='$sbstrid' and stat='0' "));
	if(!$qchk)
	{
		mysqli_query($link,"update inv_substore_indent_order_master set stat='1' where order_no='$cid' and substore_id='$sbstrid' ");
	}
}

//////////////////////////////////////////////
elseif($type=="billentry")//////////for  Bill entry
{
	$ordno=$_POST['ordno'];
	$invid=$_POST['invid'];
	$invdate=$_POST['invdate'];
	$rcpdate=$_POST['rcpdate'];
	$ttlamt=$_POST['ttlamt'];
	$discount=$_POST['ddction'];
	$netamt=$_POST['netamt'];
	
    $qspllr=mysqli_fetch_array(mysqli_query($link,"select SuppCode from inv_indent_order_master where order_no='$ordno'"));
	mysqli_query($link,"delete from inv_indent_rcv_master where inv_no='$invid' and order_no='$ordno' ");
	mysqli_query($link,"delete from inv_indent_rcvdetails where invoiceno='$invid' and order_no='$ordno'");
	
	mysqli_query($link,"insert into inv_indent_rcv_master values('$ordno','$invid','$rcpdate','$invdate','$qspllr[SuppCode]','$ttlamt','$discount','$netamt')");
	mysqli_query($link,"insert into inv_indent_rcvdetails select * from inv_indent_rcvdetails_temp where invoiceno='$invid' and order_no='$ordno'");
	mysqli_query($link,"update inv_indent_order_details set stat='1' where order_no='$ordno' ");
  /* ////for debit note///
     if($details !="")
	 {
		 $vdbamt=$billamt-$netamt;
		 $slno=mysqli_fetch_array(mysqli_query($link,"select max(sL_no)as maxsl from debit_note_master"));
		 $vslno=$slno['maxsl']+1;
		 $vdescprtion="Being Amount Debeited/ Creedited to your Account for  $details  against your Bill No  $invid Dated  $invdate  For Rs $vdbamt";
		 
		 mysqli_query($link,"delete from debit_note_master where inv_no='$invid' and companyid='$cid'");
		 mysqli_query($link,"insert into debit_note_master values('$invid','$cid','$invdate','$vdescprtion','$netamt','$billamt','$vdbamt','$vslno')");
	
	 }
   ///end debit////// */
   
  
	
	////stock entry/////
	$qrstk=mysqli_query($link,"select * from inv_indent_rcvdetails where invoiceno='$invid' and order_no='$ordno' order by item_code");
	while($qrstk1=mysqli_fetch_array($qrstk)){
	      mysqli_query($link,"delete from inv_maincurrent_stock where item_code='$qrstk1[item_code]'");	
	      $qnty=$qrstk1['quantity'];
		
	       $q=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_code='$qrstk1[item_code]' and date='$rcpdate' order by date "));
	       $q1=mysql_num_rows($q);
	         if($q)
	             {
		            $vclsng=$q['closing_qnty'];
		            $vrcv=$q['recv_qnty']+$qnty;
		            $ttalqnt=$vclsng+$qnty;
		
		     mysqli_query($link,"update inv_mainstock_details set recv_qnty='$vrcv',closing_qnty='$ttalqnt' where item_code='$qrstk1[item_code]' and date='$rcpdate'");
		     mysqli_query($link,"insert into inv_maincurrent_stock values('$qrstk1[item_code]','$ttalqnt')");
	             }
	          else
	             {
					
		            $qch=mysqli_query($link,"select * from inv_mainstock_details where item_code='$qrstk1[item_code]'  order by date desc limit 1");
		            $qch1=mysqli_fetch_array($qch);
			        $qch2=mysql_num_rows($qch);
			           if($qch1)
			               {
		                      $vop=$qch1['closing_qnty'];
			                  $ttalqnt=$vop+$qnty;
			                  
	                           mysqli_query($link,"insert into inv_mainstock_details(item_code,date,op_qnty,recv_qnty,issu_qnty,closing_qnty) values ('$qrstk1[item_code]','$rcpdate','$vop','$qnty','0','$ttalqnt')");
							   mysqli_query($link,"insert into inv_maincurrent_stock values('$qrstk1[item_code]','$ttalqnt')");
			                }
			           else
			              {
							
				             $ttalqnt=$qnty;
				             mysqli_query($link,"insert into inv_mainstock_details(item_code,date,op_qnty,recv_qnty,issu_qnty,closing_qnty) values ('$qrstk1[item_code]','$rcpdate',0,'$qnty',0,'$qnty')");
							 mysqli_query($link,"insert into inv_maincurrent_stock values('$qrstk1[item_code]','$ttalqnt')");
				
			              }
				 }
	   }
	////end stock/////////
    
}



?>

