<?php
session_start();
include'../../includes/connection.php';
$time=date('h:i:s A');
///////Date difference in php
	 /*$vendat=strtotime($mnufctr);
	 $vpaydat=strtotime($expiry);	
	 $vda=$vpaydat-$vendat;
	 echo  floor($vda/3600/24); */
	//////end///////////////
	

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

 

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

if($type=="itemtype") ///For  Item Type Master
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
		
}

////////////////////////////////
elseif($type=="phcategory") //for  pharmacy Category
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	
	$qrch=mysqli_fetch_array(mysqli_query($link,"select ph_cate_id from ph_category_master where ph_cate_id='$id'"));
	if($qrch)
	{
		mysqli_query($link,"update ph_category_master set ph_cate_name='$name' where ph_cate_id='$id'");
	
	}
	else
	{
	  mysqli_query($link,"insert into ph_category_master values('$id','$name')");
  
	}
}
///////////////////////////////

if($_POST["type"]=="insert_item_master")  //////////for Pharmacy Item
{
	$csid=$_POST['csid'];
	$csname=$_POST['csname'];
	$catid=$_POST['catid'];
	$csname= str_replace("'", "''", "$csname");
	$vtpeye=$_POST['vtpeye'];
	$gen_name=$_POST['gen_name'];
	$vmrp=$_POST['vmrp'];
	$vmrp= str_replace("'", "''", "$vmrp");
	$vstrngth=$_POST['vstrngth'];
	$vstrngth= str_replace("'", "''", "$vstrngth");
	$vvat=0;
	$costprice=$_POST['costprice'];
	$gst=$_POST['gst'];
	$strpqnty=$_POST['strpqnty'];
	$hsncode=$_POST['hsncode'];
	
	$qrch=mysqli_num_rows(mysqli_query($link,"select * from ph_item_master where item_code='$csid'"));
	
	if($qrch>0)
	{
		if(mysqli_query($link,"update ph_item_master set item_name='$csname',generic='$gen_name',item_strength='$vstrngth',item_mrp='$vmrp',cost_price='$costprice',vat='$vvat',item_type_id='$vtpeye',ph_cate_id='$catid',strip_qnty='$strpqnty',gst_percent='$gst',hsn_code='$hsncode' where item_code='$csid'"))
		{
			echo "Updated";
		}
	}
	else
	{
	  if(mysqli_query($link,"insert into  ph_item_master values('$csid','$csname',$gen_name,'$vstrngth','$vmrp','$costprice','$vvat','$vtpeye','$catid','$strpqnty','$gst','$hsncode')"))
	  {
		  echo "Saved";
	  }
	}
}

//////////////////////////////////

if($_POST["type"]=="purchse_ord_temp")
{
	$ordrdate=$_POST['ordrdate'];
	$supplr=$_POST['supplr'];
	$orderno=$_POST['orderno'];
	$itmcode=$_POST['itmcode'];
	$orqnt=$_POST['orqnt'];
	$fid=$_POST['fid'];
	$itmcode1=explode("-",$itmcode);
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ph_purchase_order_details_temp` WHERE `order_no`='$orderno' AND `item_code`='$itmcode1[1]'"));
	if($num>0)
	{
		if(mysqli_query($link,"UPDATE `ph_purchase_order_details_temp` SET `order_qnt`='$orqnt',`bl_qnt`='$orqnt' WHERE `order_no`='$orderno' AND `item_code`='$itmcode1[1]' AND `SuppCode`='$supplr'"))
		{
			echo "1";
		}
	}
	else
	{
		if(mysqli_query($link,"INSERT INTO `ph_purchase_order_details_temp`(`order_no`, `item_code`, `SuppCode`, `order_qnt`, `bl_qnt`, `order_date`, `stat`,fid) VALUES ('$orderno','$itmcode1[1]','$supplr','$orqnt','$orqnt','$ordrdate','0','$fid')"))
		{
			echo "1";
		}
	}
}
///////////////////////////////////////

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
		
		mysqli_query($link,"insert into ph_purchase_order_master values('$orderno','$supplr','$vdate','$fid' )");
		
		mysqli_query($link,"delete from ph_purchase_order_details_temp where order_no ='$orderno' and SuppCode='$supplr'  ");
	}
}
////////////////////////////////////

if($_POST["type"]=="itemreturn")  //for item Return from customer
{
	$billno=$_POST['billno'];
	$all=$_POST['all'];
	$rtndate=$_POST['rtndate'];
	$reason=$_POST['reason'];
	$prss="RETURN";
	$all=explode("#%#",$all);
	foreach($all as $al)
	{
		$al=explode("@@",$al);
		$itmid=$al[0];
		$batch=$al[1];
		$qnt=$al[2];
		if($itmid && $batch && $qnt)
		{
			mysqli_query($link,"insert into ph_item_return_master values('$billno','$itmid','$batch','$rtndate','$qnt','$reason','$userid','$time')");
			////////////////for bill//////////////////
			$qmrp=mysqli_fetch_array(mysqli_query($link,"select mrp,sale_qnt,gst_percent from ph_sell_details where bill_no='$billno' and item_code='$itmid' and batch_no='$batch'"));
			
			$vslqnt=$qmrp['sale_qnt']-$qnt;
			$vmrp=$qmrp['mrp']*$vslqnt;
			$vgstamt=$vmrp-($vmrp*(100/(100+$qmrp['gst_percent'])));
			
			mysqli_query($link,"update ph_sell_details set sale_qnt='$vslqnt',total_amount='$vmrp',	net_amount='$vmrp',gst_amount='$vgstamt' where bill_no='$billno' and item_code='$itmid' and batch_no='$batch' ");
			$q1=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$billno'  "));
			$vamt=$qmrp['mrp']*$qnt;
			$vttlamt=round($q1['total_amt']-$vamt);
			$vpaid=$q1['paid_amt']-$vamt;
			if($vpaid<0)
			{
				$vpaid=0;
			}
			$vbal=$vttlamt-$vpaid-$q1['discount_amt']-$q1['adjust_amt'];
            mysqli_query($link,"update ph_sell_master set total_amt='$vttlamt',paid_amt='$vpaid',balance='$vbal' where bill_no='$billno'  ");
			////////////////////////
			
			//------------------For stock--------------------------//
			 $vstkqnt=0;
			 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$rtndate' and item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
			 if($qrstkmaster['item_code']!='')
			 {
				$vstkqnt=$qrstkmaster['s_remain']+$qnt;
				$slqnt=$qrstkmaster['return_cstmr']+$qnt;
				mysqli_query($link,"update ph_stock_process set process_no='$prss',s_remain='$vstkqnt',return_cstmr='$slqnt' where  date='$rtndate' and item_code='$itmid' and  batch_no='$batch'");
				mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itmid' and batch_no='$batch'");
			 }
			 else///for if data not found
			 {
				 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
				 $vstkqnt=$qrstkmaster['s_remain']+$qnt;
				 mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,return_cstmr,return_supplier,s_remain,date) values('$prss','$itmid','$batch','$qrstkmaster[s_remain]',0,0,'$qnt',0,'$vstkqnt','$rtndate')");
				 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$itmid' and batch_no='$batch'");
			 }
		}
	}
	echo "Saved";
}
//////////////////////////////////////
if($_POST["type"]=="item_return_to_splr")  //for item Return to supplier
{
	$spplrid=$_POST['spplrid'];
	$batch=$_POST['batch'];
	$itmid=$_POST['itmid'];
	$rtndate=$_POST['rtndate'];
	$qnt=$_POST['qnt'];
	$reason=$_POST['reason'];
	$prss="RETURN";
	mysqli_query($link,"insert into ph_item_return_supplier(supplier_id,item_code,batch_no,return_date,return_qnt,return_reason,user,time) values('$spplrid','$itmid','$batch','$rtndate','$qnt','$reason','$userid','$time')");
	//------------------For stock--------------------------//
	 $vstkqnt=0;
	 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$rtndate' and item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
	 if($qrstkmaster['item_code']!='')
	 {
	    $vstkqnt=$qrstkmaster['s_remain']-$qnt;
	    $rtrnqnt=$qrstkmaster['return_supplier']+$qnt;
	    mysqli_query($link,"update ph_stock_process sets_remain='$vstkqnt',return_supplier='$rtrnqnt' where  date='$rtndate' and item_code='$itmid' and  batch_no='$batch'");
	    mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itmid' and batch_no='$batch'");
	 }
	 else///for if data not found
	 {
		 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$itmid' and  batch_no='$batch' order by slno desc limit 0,1"));
		 $vstkqnt=$qrstkmaster['s_remain']-$qnt;
		 mysqli_query($link,"insert into ph_stock_process(process_no,item_code,batch_no,s_available,added,sell,return_cstmr,return_supplier,s_remain,date) values('$prss','$itmid','$batch','$qrstkmaster[s_remain]',0,0,0,'$qnt','$vstkqnt','$rtndate')");
		 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where  item_code='$itmid' and batch_no='$batch'");
	 }
	 echo "Saved";
}
///////////////////////////////////////////
if($_POST["type"]=="purchase_rcv_tmp")
{
	$splrcode=$_POST['spplrid'];
	$orderno=$_POST['orderno'];
	$billno=$_POST['billno'];
	$itmid=$_POST['itmid'];
	$mnufctr=$_POST['mnufctr'];
	$expiry=$_POST['vexpiry'];
	$batch=$_POST['batch'];
	$qnt=$_POST['qnt'];
	$bal=$_POST['bal'];
	$freeqnt=$_POST['freeqnt'];
	$Vmrp=$_POST['mrp'];
	$pktqnt=$_POST['pktqnt'];
	$slprice=$_POST['slprice'];
	$vcstprice=$_POST['vcstprice'];
	$vscstamt=$_POST['vscstamt'];
	$gst=$_POST['gst'];
	$disprchnt=$_POST['disprchnt'];
	
	if($disprchnt=="")
	{
		$disprchnt=0;
	}
	
	$itmid1=explode("-#",$itmid);
	$vitmid=$itmid1['1'];
	
	$mrp=$Vmrp/$pktqnt;
	
	$vdate=date('Y-m-d');
	$fid=0;
	
    
    $frstdy='01';
	$vdt=$expiry.'-'.'01';
	$d = new DateTime( $vdt ); 
    $vexpiry=$d->format( 'Y-m-t' );
    
    $gstamt=0;
    
    $vdisamt=0;
    $vdisamt=round($vscstamt*$disprchnt/100);
    $aftrdis=$vscstamt-$vdisamt;
    
    $gstamt=number_format($aftrdis*$gst/100,2);
    
    
	mysqli_query($link,"delete from ph_purchase_receipt_temp where order_no='$orderno' and item_code='$vitmid' and recept_batch='$batch'");
	mysqli_query($link,"insert into ph_purchase_receipt_temp values('$orderno','$billno','$vitmid','$mnufctr','$vexpiry','$vdate','$qnt','$freeqnt','$batch','$splrcode','$mrp','$vcstprice','$slprice','$fid','$vscstamt','$disprchnt','$vdisamt','$gst','$gstamt')");
	mysqli_query($link,"update ph_item_master set gst_percent='$gst',strip_qnty='$pktqnt' where item_code='$vitmid'");
}

//////////////////////////////////////////////
if($_POST["type"]=="purchase_rcv_final")
{
	
	$orderno=$_POST['orderno'];
	$splrblno=$_POST['splrblno'];
	$spplrid=$_POST['spplrid'];
	$gstamt=$_POST['gstamt'];
	$billamt=$_POST['billamt'];
	$disamt=$_POST['disamt'];
	$ttlamt=$_POST['ttlamt'];
	$bildate=$_POST['bildate'];
	
	$itm=$_POST['itm'];
	$shopcode=0;
	$entrydate=date('Y-m-d');
	$fid=0;
	
	mysqli_query($link,"delete from purchase_receipt_details where order_no ='$orderno' and bill_no='$splrblno' ");
	mysqli_query($link,"insert into ph_purchase_receipt_details select * from ph_purchase_receipt_temp where order_no ='$orderno' and bill_no='$splrblno' ");
	
	
	$numdt=mysqli_num_rows(mysqli_query($link,"select * from ph_purchase_order_details where order_no='$orderno'"));
	$numr=mysqli_num_rows(mysqli_query($link,"select * from ph_purchase_order_details where order_no='$orderno' and `stat`=1"));
	if($numdt==$numr)
	{
		//mysqli_query($link,"delete from ph_purchase_receipt_master where order_no ='$orderno' and supp_code='$qsuplrcode[SuppCode]' ");
		//mysqli_query($link,"insert into ph_purchase_receipt_master values('$orderno','$entrydate','$shopcode','$qsuplrcode[SuppCode]','$splrblno')");
	}
	
	//mysqli_query($link,"delete from ph_purchase_receipt_master where order_no ='$orderno' and supp_code='$qsuplrcode[SuppCode]' ");
	mysqli_query($link,"insert into ph_purchase_receipt_master values('$orderno','$bildate','$entrydate','$billamt','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','$fid','$userid','$time')");
	
	//////////end mrp///////////////////////////
	mysqli_query($link,"delete from ph_purchase_receipt_temp where order_no ='$orderno' and bill_no='$splrblno' ");
	////////////For stock''''''''''''''''''''
		
	$qrstk=mysqli_query($link,"select * from ph_purchase_receipt_details  where order_no ='$orderno'  and bill_no='$splrblno' and SuppCode='$spplrid' order by item_code");
	while($qrstk1=mysqli_fetch_array($qrstk))
	{
	  $vqnt=$qrstk1['recpt_quantity']+$qrstk1['free_qnt'];
             
       $vst=0;
       //mysqli_query($link,"UPDATE `ph_purchase_order_details` SET `bl_qnt`='$vbalqnt', `stat`='1' WHERE order_no='$orderno' and item_code='$qrstk1[item_code]'");
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
////////////////////////////

if($_POST["type"]=="saletemp")
{
	$bill=$_POST['billno'];
	$entrydate=$_POST['entrydate'];
	$itmcode=$_POST['itmcode'];
	$batchno=$_POST['batchno'];
	$expiry=$_POST['expiry'];
	$quantity=$_POST['quantity'];
	$mrp=$_POST['rate'];
	$fid=0;
	
	$vt=mysqli_fetch_array(mysqli_query($link,"select gst_percent,item_mrp from `ph_item_master` where `item_code`='$itmcode'"));
	$vcstprice=mysqli_fetch_array(mysqli_query($link,"select recept_cost_price,sale_price from `ph_purchase_receipt_details` where `item_code`='$itmcode' and recept_batch='$batchno'"));
	$vslprice=0;
	$vslprice=$vcstprice['sale_price'];
	if($vslprice==0)
	{
		$vslprice1=$vt['item_mrp']-($vt['item_mrp']*(100/(100+$vt['gst_percent'])));
		$vslprice=round($vt['item_mrp']-$vslprice1,2);
	}
	$vttlamt=val_con($quantity*$mrp);
	$vttlcstprc=val_con($quantity*$vcstprice['recept_cost_price']);
	$gstamt=$vttlamt-($vttlamt*(100/(100+$vt['gst_percent'])));
	
	mysqli_query($link,"delete from ph_sell_details_temp where bill_no='$bill' and item_code='$itmcode' and batch_no='$batchno'");
	mysqli_query($link,"insert into ph_sell_details_temp values('$bill','$entrydate','$itmcode','$batchno','$expiry','$quantity','$free','$mrp','$vttlamt','$vttlamt','$vt[gst_percent]','$fid','$gstamt','$vcstprice[recept_cost_price]','$vslprice')");
 
   $dt=mysqli_fetch_array(mysqli_query($link,"SELECT distinct entry_date FROM ph_sell_details_temp where  bill_no='$bill'  order by entry_date asc "));
   mysqli_query($link,"update ph_sell_details_temp set entry_date='$dt[entry_date]' where  bill_no='$bill' ");
}
/////////////////////////////////////
elseif($type=="phitmrtrn1111") ///for item return from Customer
{

	$pbillno=$_POST['pbillno'];
	$date1=$_POST['date1'];
	$billno=$_POST['bildtl'];
	$reason=$_POST['reason'];
	$crdtno=$_POST['crdtno'];
	
	$billno=explode("@#",$billno);
	foreach($billno as $blno)
	{		
		if($blno)
		{
		   $blnonw=explode("%%",$blno);
		   mysqli_query($link,"insert into ph_item_return_master values('$pbillno','$blnonw[0]','$blnonw[2]','$date1','$blnonw[1]','$reason','$_SESSION[emp_id]')");
		   
		   $qstk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_master where item_code='$blnonw[0]' and batch_no ='$blnonw[2]'"));
		   $vstkqnty=$qstk['quantity']+$blnonw[1];
		   mysqli_query($link,"update ph_stock_master set quantity='$vstkqnty' where item_code='$blnonw[0]' and batch_no ='$blnonw[2]'");
		   $q1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$blnonw[0]' and batch_no='$blnonw[2]' and date='$date1'"));
		   if($q1)
		    {
				 $vadded=$q1['added']+$blnonw[1];
				 mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnty',added='$vadded' where item_code='$blnonw[0]' and batch_no ='$blnonw[2]' and date='$date1'");
			}
			else
			{
			}
		  
		}
	}
	mysqli_query($link,"delete from ph_item_return_creditnote where bill_no='$pbillno'");
	mysqli_query($link,"insert into ph_item_return_creditnote value('$pbillno','$crdtno','$date1')");
	
}

////////////////////////////////////////////////////////////////////

if($type=="cust_credit") ////Balance Received Pharmacy
{
	$blno=$_POST['blno'];
	$ptype=$_POST['ptype'];
	$amtpaid=val_con($_POST['amtpaid']);
	$amtblnce=val_con($_POST['amtblnce']);
	$txtcrdtamt=val_con($_POST['txtcrdtamt']);
	$pymtdate=$_POST['pymtdate'];
	$chk_no=$_POST['chk_no'];
	$vpaid=0;
	
	mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no,type_of_payment,user,time) values('$blno','$pymtdate','$txtcrdtamt','$ptype','$chk_no','B','$_SESSION[emp_id]','$time')");
	$qpaid=mysqli_fetch_array(mysqli_query($link,"select paid_amt from ph_sell_master where bill_no='$blno'"));
	$vpaid=$qpaid['paid_amt']+$txtcrdtamt;
	$bal=$amtblnce-$txtcrdtamt;
	mysqli_query($link,"update ph_sell_master set paid_amt='$vpaid',balance='$bal' where bill_no='$blno'");
	echo "Paid";
}

//////////////////////////////////////
if($_POST["type"]=="salefinal")
{
	$billtype=$_POST['billtype'];
	$ref_by=$_POST['ref_by'];
	$pat_typ=$_POST['pat_typ'];
	$ptype=$_POST['ptype'];
	$blno=$_POST['blno'];
	$entrydate=$_POST['entrydate'];
	$ttlamt=val_con($_POST['net']);
	$discountprcnt=val_con($_POST['discountprcnt']);
	$aftrdscnt=val_con($_POST['aftrdscnt']);
	$paidamont=val_con($_POST['paidamont']);
	$customername=$_POST['customername'];
	$customername= str_replace("'", "''", "$customername");
	//$customername=$_POST['customername'];
	$custphone=$_POST['custphone'];
	$disamt=$ttlamt-$aftrdscnt;	
	$balance=round($_POST['balance']);
	$uhid=$_POST['uhid'];
	$cabinno=$_POST['cabinno'];
	$adjustmt=$_POST['adjustmt'];
	$fid=0;
	
	
	$balance=$paidamont;
	$paidamont=0.0;
	
	$opd_id="";
	$ipd_id="";
	$ipd_id=$uhid;
	
	if($billtype==2)	
	{
		$ptype=2;
	}
	else
	{
		$ptype=1;
	}
	
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
				$qchkno=mysqli_fetch_array(mysqli_query($link,"select ifnull(count(bill_no),0) as maxentry from ph_sell_master_edit where bill_no='$blno' "));
				$vno=$qchkno[maxentry]+1;
				mysqli_query($link,"insert into ph_sell_details_edit select a.*,'$vno' from  ph_sell_details a where a.bill_no='$blno'");
				
				mysqli_query($link,"INSERT INTO `ph_sell_master` VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$adjustmt','$paidamont','$balance','$billtype','$opd_id','$ipd_id','$ptype','$fid','$ref_by','$pat_typ','$userid','$time')");
				mysqli_query($link,"INSERT INTO `ph_sell_master_edit`(bill_no,entry_date,customer_name,customer_phone,total_amt,discount_perchant,discount_amt,adjust_amt,paid_amt,balance,bill_type_id,opd_id,ipd_id,patient_type,fid,refbydoctorid,pat_type,user,time,entry_no) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$adjustmt','$paidamont','$balance','$billtype','$opd_id','$ipd_id','$ptype','$fid','$ref_by','$pat_typ','$userid','$time','$vno')");
				//mysqli_query($link,"delete from ph_payment_details where bill_no='$blno' and entry_date='$entrydate'");
				//mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no,type_of_payment,user,time) values('$blno','$entrydate','$paidamont','Cash','','A','$_SESSION[emp_id]','$time')");
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
		mysqli_query($link,"insert into ph_sell_details_edit select a.*,'1' from  ph_sell_details a where a.bill_no='$blno'");
		
		mysqli_query($link,"delete from ph_sell_master where bill_no='$blno'"); 
		mysqli_query($link,"INSERT INTO `ph_sell_master` VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$adjustmt','$paidamont','$balance','$billtype','$opd_id','$ipd_id','$ptype','$fid','$ref_by','$pat_typ','$userid','$time')");
		mysqli_query($link,"INSERT INTO `ph_sell_master_edit`(bill_no,entry_date,customer_name,customer_phone,total_amt,discount_perchant,discount_amt,adjust_amt,paid_amt,balance,bill_type_id,opd_id,ipd_id,patient_type,fid,refbydoctorid,pat_type,user,time,entry_no) VALUES ('$blno','$entrydate','$customername','$custphone','$ttlamt','$discountprcnt','$disamt','$adjustmt','$paidamont','$balance','$billtype','$opd_id','$ipd_id','$ptype','$fid','$ref_by','$pat_typ','$userid','$time','1')");
		//mysqli_query($link,"delete from ph_payment_details where  bill_no='$blno'");
		//mysqli_query($link,"insert into ph_payment_details (bill_no,entry_date,amount,payment_mode,check_no,type_of_payment,user,time) values('$blno','$entrydate','$paidamont','Cash','','A','$userid','$time')");
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

/////////////////////////////////////
elseif($type=="phbalancercv") ///for indent substore rcv
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
		   
		   mysqli_query($link,"insert into ph_payment_details(bill_no,entry_date,amount,payment_mode,check_no,payment_type) values('$cid','$blnonw[0]','$date1','$sbstrid','$blnonw[1]',0)");
		   mysqli_query($link,"update inv_substore_order_details set stat='1' where order_no='$cid' and item_code='$blnonw[0]' and substore_id='$sbstrid'");
		}
	}
	
	$qchk=mysqli_fetch_array(mysqli_query($link,"select item_code from inv_substore_order_details where order_no='$cid' and substore_id='$sbstrid' and stat='0' "));
	if(!$qchk)
	{
		mysqli_query($link,"update inv_substore_indent_order_master set status='1' where order_no='$cid' and substore_id='$sbstrid' ");
	}
}


?>

