<?php
session_start();
include'../../includes/connection.php';

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

$type=$_POST['type'];

if($type=="phcategory")  //For indent order
{
	$phid=$_POST['phid'];
	$qrm=mysqli_query($GLOBALS["___mysqli_ston"], "select * from ph_category_master where ph_cate_id='$phid' ");
	$qrm1=mysqli_fetch_array($qrm);
	
	$val=$phid.'@'.$qrm1[ph_cate_name];
	echo $val;
	
}
///////////////////////////////////////
else if($type=="checkretrun") // check Retrun Bill
{
	$vck=0;
	$bilno=$_POST['bilno'];
	$qrm=mysqli_fetch_array(mysqli_query($link,"select * from ph_item_return_creditnote where bill_no='$bilno' "));
	if($qrm)
	{
		$vck=1;
	}
	$val=$bilno.'@'.$vck;
	echo $val;
}

//////////////////////
if($_POST["type"]=="item_name") // gov
{
	$pid=$_POST['pid'];
	$qruser=mysqli_fetch_array(mysqli_query($link,"select * from item_master  where item_id='$pid'"));
	$val=$pid.'@'.$qruser['item_name'].'@'.$qruser['item_mrp'];
	echo $val;
}
///////////////////////
if($type=="load_running_stock")
{
	$itm=$_POST['itm'];
	$itm=explode("-#",$itm);
	$id=trim($itm[1]);
	$batch=$_POST['batch'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `item_code`='$id' AND `batch_no`='$batch'"));
	if($v['quantity'])
	{
		echo $v['quantity'];
	}
	else
	{
		echo 0;
	}
}

if($type=="load_pat_det")
{
	$billno=$_POST['billno'];
	$pchk=mysqli_fetch_array(mysqli_query($link,"SELECT `bill_no` FROM `ph_item_return_master` WHERE `bill_no`='$billno'"));
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `total_amt`,`customer_name`,discount_amt,paid_amt,balance,discount_perchant FROM `ph_sell_master` WHERE `bill_no`='$billno'"));
	//~ if($pchk)
	//~ {
		
	//~ }
	//~ else
	//~ {
	   //~ echo $p['customer_name'];
    //~ }
    $dis_per=explode(".",$p['discount_perchant']);
    $dis_per=$dis_per[0];
    echo $p['total_amt']."@@@".$p['customer_name']."@@@".$p['paid_amt']."@@@".$p['discount_amt']."@@@".$p['balance']."@@@".$dis_per."@@@";
}

/////////////////////////////////
elseif($type=="ph_ipd_credit") //for ph ipd credit
{
	$orderid=$_POST['orderid'];
	$sbstrid=$_POST['sbstrid'];
	
	$q=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where ipd_id='$orderid' "));
	$val=$orderid.'#'.$q['substore_id'];
	echo $val;
}
///////////////////////////////////////
else if($type=="edit_item_master") // gov
{
	$rid=$_POST['rid'];
	$qrm=mysqli_query($link,"select * from ph_item_master where item_code='$rid' ");
	$qrm1=mysqli_fetch_array($qrm);
	$val=$rid.'@'.$qrm1['item_name'].'@'.$qrm1['generic'].'@'.$qrm1['item_strength'].'@'.$qrm1['item_mrp'].'@'.$qrm1['cost_price'].'@'.$qrm1['gst_percent'].'@'.$qrm1['item_type_id'].'@'.$qrm1['strip_qnty'].'@'.$qrm1['ph_cate_id'].'@'.$qrm1['hsn_code'];
	echo $val;
}
/////////////////////////////////
if($type=="searchbill")
{
	$blno=$_POST['blno'];
	mysqli_query($link,"delete from ph_sell_details_temp where user='$userid'");
	
	mysqli_query($link,"insert into ph_sell_details_temp select a.*,'$userid' from ph_sell_details a where a.bill_no='$blno'");
	$qpdct=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$blno'"));
	$qgstamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(gst_amount),0) as maxgstamt from ph_sell_details where bill_no='$blno'"));
	$aftrdisamt=$qpdct['total_amt']-$qpdct['discount_amt'];
			
	$val=$blno.'@'.$qpdct['entry_date'].'@'.$qpdct['customer_name'].'@'.$qpdct['customer_phone'].'@'.$qpdct['total_amt'].'@'.$qgstamt['maxgstamt'].'@'.$qpdct['total_amt'].'@'.$qpdct['discount_perchant'].'@'.$aftrdisamt.'@'.$qpdct['paid_amt'].'@'.$qpdct['balance'].'@'.$qpdct['cabin_no'];
    echo  $val;
}

//////////////////////////////////
else if($type=="loadphitmdirct") // for purchase direct Received
{
	$rid=$_POST['id'];
	//$rid=explode("-#",$rid1);
	$qrm=mysqli_query($link,"select * from item_master where item_id='$rid' ");
	$qrm1=mysqli_fetch_array($qrm);
	$val=$rid[1].'@'.$qrm1['item_name'].'@'.$qrm1['strip_quantity'].'@'.$qrm1['gst'];
	echo $val;
}
///////////////////////////////
if($_POST["type"]=="load_quant_return_item")///for item return
{
	$itm=$_POST['itm'];
	$billno=$_POST['billno'];
	
	$qry=mysqli_query($link,"select a.item_name,b.item_code,b.sale_qnt,b.mrp,b.batch_no from item_master a,ph_sell_details b where a.item_id=b.item_code and b.bill_no='$billno' and a.item_id='$itm' ");
	$q=mysqli_fetch_array($qry);
	$ret=mysqli_fetch_array(mysqli_query($link,"SELECT sum(`return_qnt`) as ret_qnt FROM `ph_item_return_master` WHERE `bill_no`='$billno' AND `item_code`='$q[item_code]' AND `batch_no`='$q[batch_no]'"));
	$rem=$q['sale_qnt']-$ret['ret_qnt'];
	$val=$itm.'@'.$q['item_name'].'@'.$q['mrp'].'@'.$rem;
	echo $val;
}

///////////////

elseif($type=="chk_sale_qnty")
{
	$blno=$_POST['blno'];
	$batchno=$_POST['batchno'];
	$vt=0;
	
	$qchqnty=mysqli_query($link,"select * from ph_sell_details_temp  where  bill_no ='$blno' ");
	while($qchqnty1=mysqli_fetch_array($qchqnty))
	{
		$qchqnty=mysqli_fetch_array(mysqli_query($link,"select quantity  from ph_stock_master  where  item_code ='$qchqnty1[item_code]' and batch_no  ='$qchqnty1[batch_no]' and substore_id ='1'"));
		if($qchqnty1['sale_qnt']>$qchqnty['quantity'])
		{
		 
		 $vt=1;
	   }
	}
		
	echo $vt;
}

///////////////////////////////////
if($type=="load_pat_det_chk")
{
	$billno=$_POST['billno'];
	$vbilchk=0;
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `bill_no` FROM `ph_item_return_master` WHERE `bill_no`='$billno'"));
	if($p)
	{
		$vbilchk=1;
	}
	echo $vbilchk;
}
////
///////////////////////////////
if($_POST["type"]=="load_quant_return_item_mrp")///for item return MRP
{
	$itmid=$_POST['itmid'];
	$btchno=$_POST['btchno'];
	$q=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where item_code='$itmid' and recept_batch='$btchno' "));
	
	$val=$itmid.'@'.$q['recpt_mrp'];
	echo $val;
}
///////////////////////////////
if($_POST["type"]=="supplr_item_rtrn")///for item return
{
	$itm=$_POST['itm'];
	$qry=mysqli_query($link,"select item_name from ph_item_master  where item_code='$itm' ");
	$q=mysqli_fetch_array($qry);
	$val=$itm.'@'.$q['item_name'];
	echo $val;
}

///////////////////////////////
if($_POST["type"]=="stkqnt_splr_rtrn")///for item return
{
	$itm=$_POST['itm'];
	$btchno=$_POST['btchno'];
	$qry=mysqli_query($link,"select batch_no,quantity from ph_stock_master where item_code='$itm' and batch_no='$btchno' ");
	$q=mysqli_fetch_array($qry);
	$val=$itm.'@'.$q['quantity'];
	echo $val;
}

/////////////////////////
if($_POST["type"]=="lod_batchno") //for item return batch
{
	$itm=$_POST['itm'];
	$billno=$_POST['billno'];
	$qry=mysqli_query($link,"select batch_no from ph_sell_details where item_code='$itm' and bill_no='$billno'");
	while($q=mysqli_fetch_array($qry))
	{
	  $val=$q['batch_no'].'@'.$q['batch_no'].'#';
	  echo  $val;
	}
}

if($_POST["type"]=="val_load_sell_qnty")
{
	$itm=$_POST['itm'];
	$billno=$_POST['billno'];
	$btchno=$_POST['btchno'];
	
	$qry=mysqli_query($link,"select a.*,b.sale_qnt,b.mrp,b.batch_no from item_master a,ph_sell_details b where a.item_id=b.item_code and b.bill_no='$billno' and b.item_code='$itm' and b.batch_no='$btchno'");
	
	$q=mysqli_fetch_array($qry);
	
	$ret=mysqli_fetch_array(mysqli_query($link,"SELECT sum(`return_qnt`) as ret_qnt FROM `ph_item_return_master` WHERE `bill_no`='$billno' AND `item_code`='$itm' AND `batch_no`='$q[batch_no]'"));
	$rem=$q['sale_qnt']-$ret['ret_qnt'];
	$val=$itm.'@'.$q['item_name'].'@'.$q['mrp'].'@'.$rem;
	echo $val;
}

///////////////////////////////////
if($_POST["type"]=="ph_itm_rtrnto_store") // 
{
	$prdctid=$_POST['prdctid'];
	$itmid1=explode("-#",$prdctid);
	$qpdct=mysqli_query($link,"select * from ph_stock_master where item_code='$itmid1[1]' and substore_id='1' and quantity>0 order by exp_date");
	while($qpdct1=mysqli_fetch_array($qpdct))
	{
	 $val=$qpdct1['batch_no'].'@'.$qpdct1['batch_no'].'#';
	 echo  $val;
    }
	
}

/////////////////////////
if($_POST["type"]=="supplr_item_rtrn_btch") //for item return batch to supplier
{
	$itm=$_POST['itm'];
	
	$qry=mysqli_query($link,"select batch_no,quantity from ph_stock_master where item_code='$itm' ");
	while($q=mysqli_fetch_array($qry))
	{
	  $val=$q['batch_no'].'@'.$q['batch_no'].'#';
	  echo  $val;
	}
}

/////////////////////////////////
elseif($type=="load_ph_item_return") //for 
{
	$itmid=$_POST['itmid'];
	$itmid1=explode("-#",$itmid);
	$batchno=$_POST['batchno'];
	$qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where  item_code='$itmid1[1]' and  recept_batch='$batchno'"));
	$q=mysqli_fetch_array(mysqli_query($link,"select quantity,exp_date from ph_stock_master where  item_code='$itmid1[1]' and batch_no='$batchno' and substore_id='1' "));
	$val=$substrid.'@'.$q['quantity'].'@'.$qmrp['recpt_mrp'].'@'.$q['exp_date'];
	echo $val;
}

/////////////////////////////
else if($type=="loadsaleprice") // for load sale price gst calculation
{
	$rate=$_POST['rate'];
	$pktqnt=$_POST['pktqnt'];
	$gst=$_POST['gst'];
	$vunitprice=$rate/$pktqnt;
	$vslprice1=$vunitprice-($vunitprice*(100/(100+$gst)));///Remove gst Calculattion
	$vslprice2=round($vslprice1,2);
	$vslprice3=$vunitprice-$vslprice2;
	$vslprice=$vslprice3;
	
	$val=$vslprice.'@'.$vslprice;
	echo $val;
}

/////////////////////////////
else if($type=="loadrcvdttlamt") // for load total amount
{
	$orderno=$_POST['orderno'];
	$splrid=$_POST['splrid'];
	$billno=$_POST['billno'];
	$vamt1=0;
	$vnetamt=0;
	$vgstamt=0;
	$disamt=0;
	$q=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(item_amount),0) as maxttl,ifnull(sum(dis_amt),0) as maxdis,ifnull(sum(gst_amount),0) as maxgst from inv_main_stock_received_detail_temp where order_no='$orderno' and SuppCode='$splrid' and bill_no='$billno' "));	
	//$q=mysqli_query($link,"select item_code,recpt_quantity,recept_cost_price,dis_amt,gst_amount from ph_purchase_receipt_temp where order_no='$orderno' and SuppCode='$splrid' and bill_no='$billno' ");	
	/*while($q1=mysqli_fetch_array($q))	
	{
		
		$vamt=0;
		$vamt=$q1['recpt_quantity']*$q1['recept_cost_price'];
		$vgst=$q1['gst_amount'];
		$vamt1=$vamt1+$vamt;
		$vgstamt=$vgstamt+$vgst;
		$disamt=$disamt+$q1['dis_amt'];
	}*/
	
	//$vnetamt=round($vamt1+$vgstamt-$disamt);
	$vnetamt=round($q['maxttl']+$q['maxgst']-$q['maxdis']);
	$val=$q['maxttl'].'@'.$q['maxgst'].'@'.$q['maxdis'].'@'.$vnetamt;
	echo $val;
}
//////////////////////////////////////

if($_POST["type"]=="manufactre") // load Batch
{
	$itmcode=$_POST['itmcode'];
	$btchno=$_POST['btchno'];
	$itmrate=0;
	$date=date("Y-m-d");
	$qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where item_code='$itmcode' and recept_batch='$btchno' order by recpt_date desc"));
	if($qmrp['recpt_mrp']>0)
	{
		$itmrate=$qmrp['recpt_mrp'];
	}
	else
	{
		$mrp1=mysqli_fetch_array(mysqli_query($link,"select item_mrp from ph_item_master where item_code='$itmcode'"));
		$itmrate=$mrp1['item_mrp'];
	} 
	 
	$qpdct1=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_master  where item_code='$itmcode' and batch_no='$btchno' and substore_id='1' "));
	$start_ts = strtotime($date);
	$end_ts = strtotime($qpdct1['exp_date']);
	$diff = $end_ts - $start_ts;
	$diff = round($diff / 86400);
	if($qpdct1['mfc_date']!='0000-00-00')
	{
		$mfg=$qpdct1['mfc_date'];
	}
	else
	{
		$mfg="";
	}
    $val=$mfg.'@'.$qpdct1['exp_date'].'@'.$qpdct1['quantity'].'@'.$itmrate.'@'.$diff;
    //$val=$qpdct1['mfc_date'].'@'.$qpdct1['exp_date'].'@'.$qpdct1['quantity'].'@'.$itmrate;
	echo  $val;
}
///////////////////////////////////
if($_POST["type"]=="batchload") // gov
{
	$prdctid=$_POST['prdctid'];
	$qpdct=mysqli_query($link,"select * from ph_stock_master where item_code='$prdctid' and quantity>0 and substore_id='1' order by exp_date");
	while($qpdct1=mysqli_fetch_array($qpdct))
	{
	 $val=$qpdct1['batch_no'].'@'.$qpdct1['batch_no'].'#';
	 echo  $val;
    }
	
}
///////////////////////////////////

if($_POST["type"]=="load_patient_sale")
{
	$opd_id=$_POST['uhid'];
	$pat_type=$_POST['pat_type'];
	
	$opd=mysqli_fetch_array(mysqli_query($link,"SELECT patient_id,refbydoctorid,type  FROM `uhid_and_opdid` where `opd_id`='$opd_id' order by `opd_id` desc limit 0,1"));
	$vpid=$opd['patient_id'];
	
	/*if($pat_type==1) // opd_id
	{
		$opd=mysqli_fetch_array(mysqli_query($link,"SELECT patient_id  FROM `uhid_and_opdid` where `opd_id`='$opd_id' order by `opd_id` desc limit 0,1"));
		$vpid=$opd['patient_id'];
		
	}
	if($pat_type==2) // ipd_id
	{
		$ipd=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id` FROM `ipd_pat_details` WHERE `ipd_id`='$opd_id' order by `ipd_id` desc limit 0,1"));
		$vpid=$ipd['patient_id'];
		
	}*/
	
	
	
	$qwrdname=mysqli_fetch_array(mysqli_query($link,"select a.ward_id,b.name from ipd_pat_bed_details a,ward_master b where a.ipd_id='$uhid' and a.ward_id=b.ward_id"));
	
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name`,`phone` FROM `patient_info` WHERE `patient_id`='$vpid'"));
	$vl=$pat['name']."@".$pat['phone']."@".$qwrdname['name']."@".$opd['refbydoctorid']."@".$opd['type'];
	echo $vl;
}

///////////////////////////////////

if($_POST["type"]=="loadhsnno")
{
	$uhid=$_POST['uhid'];
	$qhsnname=mysqli_fetch_array(mysqli_query($link,"select * from hsn_master where hsn_code='$uhid' "));
	
	$vl=$uhid."@".$qhsnname['gst_percent'];
	echo $vl;
}
////////////////////////////////
elseif($type=="phpndngbill") //for indend approved
{
	$orderid=$_POST['orderid'];
	$sbstrid=$_POST['sbstrid'];
	
	//$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_substore_indent_order_master where order_no='$orderid' and substore_id='$sbstrid'"));
	$val=$orderid.'#'.$q['substore_id'];
	echo $val;
}
/////////////////
elseif($type=="calc_vat")
{
	$bill=$_POST['bill'];
	$vt=0;
	$nt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(net_amount),0)as net from ph_sell_details_temp  where bill_no='$bill' and user='$userid'"));
	//$t=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amount),0)as tot from ph_sell_details_temp  where bill_no='$bill'"));
	$gstt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(gst_amount),0)as gstamt from ph_sell_details_temp  where bill_no='$bill' and user='$userid'"));
	
	//$vt=number_format(($nt['net']-$t['tot']),2);
	$vt=number_format($gstt['gstamt'],2);
	echo $vt;
}

if($type=="srch_prev_bill")
{
	$supp=$_POST['supp'];
	$bill=$_POST['bill'];
	$q=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE `bill_no`='$bill' AND `supp_code`='$supp'"));
	mysqli_query($link,"DELETE FROM `ph_purchase_receipt_temp` WHERE `order_no`='$q[order_no]' AND `bill_no`='$bill' AND `SuppCode`='$supp'");
	mysqli_query($link,"INSERT INTO `ph_purchase_receipt_temp` SELECT * FROM `ph_purchase_receipt_details` WHERE `order_no`='$q[order_no]' AND `bill_no`='$bill' AND `SuppCode`='$supp'");
	//echo "DELETE FROM `ph_purchase_receipt_temp` WHERE `order_no`='$q[order_no]' AND `bill_no`='$bill' AND `SuppCode`='$supp'";
	echo $q["order_no"]."@@".$q["recpt_date"]."@@".$q["bill_amount"];
}
?>
