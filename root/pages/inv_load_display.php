<?php
include'../../includes/connection.php';

session_start();
$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

$type=$_POST['type'];

if($type=="indent_order")  //For indent order
{
	$rid=$_POST['id'];
	$qrm=mysqli_query($GLOBALS["___mysqli_ston"], "select * from inv_indent_master where id='$rid' ");
	$qrm1=mysqli_fetch_array($qrm);
	
	$val=$rid.'@'.$qrm1[name];
	echo $val;
	
}


/////////////////////////////////
elseif($type=="mainordersrch") //for order Serch for main store
{
	$orderno=$_POST['orderno'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_order_details where order_no='$orderno' and stat=0"));
	mysqli_query($link,"delete from inv_indent_order_details_temp ");
	
	mysqli_query($link,"insert into inv_indent_order_details_temp select * from inv_indent_order_details where order_no='$orderno' and stat=0 and SuppCode='$q[SuppCode]' ");
	$val=$orderno.'@'.$q['SuppCode'].'@'.$q['order_date'];
	echo $val;
}

////////////////////////////
else if($type=="invloadrcvdttlamt") // for load total amount
{
	$orderno=$_POST['orderno'];
	$billno=$_POST['billno'];
	$vamt1=0;
	
	$q=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(gst_amount),0) as mxgst,ifnull(sum(total_amount),0) as maxttl from inv_main_stock_receied_detail_temp where receipt_no='$orderno' and supplier_bill_no='$billno'  "));	
	
	$vgstamt=$q['mxgst'];
	$vnetamt=round($q['maxttl']+$q['mxgst']);
	$val=$vgstamt.'@'.$vnetamt;
	echo $val;
}

//////////////////////////////////
else if($type=="invloadphitmdirct") // for purchase direct Received
{
	$rid1=$_POST['id'];
	$rid=explode("-",$rid1);
	$qrm=mysqli_query($link,"select * from inv_indent_master where id='$rid[1]' ");
	$qrm1=mysqli_fetch_array($qrm);
	$val=$rid[1].'@'.$qrm1['price'].'@'.$qrm1['gst'];
	echo $val;
}

//////////////////////////////////
else if($type=="searchprcaseorder") // for purchase direct Received
{
	$orderno=$_POST['orderno'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_purchase_order_master where order_no='$orderno'"));
	mysqli_query($link,"delete from inv_purchase_order_details_temp where order_no='$orderno'");
	mysqli_query($link,"insert into inv_purchase_order_details_temp select `order_no`,`item_id`,`supplier_id`,`order_qnt`,`order_date`,`stat`,'$userid' from inv_purchase_order_details where order_no='$orderno' and supplier_id='$q[supplier_id]' ");
	$val=$orderno.'@'.$q['supplier_id'].'@'.$q['order_date'];
	echo $val;
}
/////////////////////////////////
elseif($type=="invsubstritmissue") //for order Serch for main store
{
	$itmid=$_POST['itmid'];
	$itmid1=explode("-#",$itmid);
	$substrid=$_POST['substrid'];
	$q=mysqli_fetch_array(mysqli_query($link,"select closing_stock from inv_substorecurrent_stock where substore_id='$substrid' and item_code='$itmid1[1]'"));
	$val=$substrid.'@'.$q['closing_stock'];
	echo $val;
}

/////////////////////////////////
elseif($type=="purchasercvdorderno") //for order Serch for main store
{
	$orderno=$_POST['orderno'];
	
	$q=mysqli_fetch_array(mysqli_query($link,"select a.order_no,a.supplier_id,b.name from inv_purchase_order_master a,inv_supplier_master b where a.order_no='$orderno' and a.supplier_id=b.id "));
	$val=$orderno.'@'.$q['name'].'@'.$q['supplier_id'];
	
	echo $val;
	
	
}


/////////////////////////////////
elseif($type=="inv_load_bill_cancel") //for inv bill type
{
	$rcptno=$_POST['rcptno'];
	$vchkdone=1;
	$q=mysqli_fetch_array(mysqli_query($link,"select a.bill_no,a.supp_code,a.recpt_date,b.name from inv_main_stock_received_master a,inv_supplier_master b where a.receipt_no='$rcptno' and a.supp_code=b.id "));
	
	$qchekitm=mysqli_query($link,"select * from inv_main_stock_received_detail where order_no='$rcptno' and  `bill_no`='$q[bill_no]' and `recpt_date`='$q[recpt_date]' and `SuppCode`='$q[supp_code]' order by item_id ");
	
	
	while($qchekitm1=mysqli_fetch_array($qchekitm))
	{
		
		$vttlrcptqnt=0;
		$vttlrcptqnt=$qchekitm1['recpt_quantity']+$qchekitm1['free_qnt'];
		$qstkchk=mysqli_fetch_array(mysqli_query($link,"select closing_stock from inv_maincurrent_stock where item_id='$qchekitm1[item_id]' and batch_no='$qchekitm1[recept_batch]'"));
		
		if($qstkchk['closing_stock']<$vttlrcptqnt)
		{
			$vchkdone=2;
		}
		
		
    }
	
	if($vchkdone==1)
	{
	  $val=$q['bill_no'].'@'.$q['name'].'@'.$q['recpt_date'].'@'.$q['supp_code'];
    }
    else
    {
		$val=2;
	}
	echo $val;
	
	
}

///////////////////////////////////
if($_POST["type"]=="mainstrbatchload") // gov
{
	$prdctid=$_POST['prdctid'];
	$itmid1=explode("-#",$prdctid);
	$qpdct=mysqli_query($link,"select * from inv_maincurrent_stock where item_id='$itmid1[1]' and closing_stock>0 order by exp_date");
	while($qpdct1=mysqli_fetch_array($qpdct))
	{
	 $val=$qpdct1['batch_no'].'@'.$qpdct1['batch_no'].'#';
	 echo  $val;
    }
	
}
///////////////////////////////////
if($_POST["type"]=="splr_bill_load") // 
{
	$splirid=$_POST['splirid'];
	
	$qpdct=mysqli_query($link,"select bill_no from inv_main_stock_received_master where  supp_code='$splirid' and bill_no not in(select bill_no from inv_supplier_payment_master where supp_code='$splirid' )  order by slno");
	while($qpdct1=mysqli_fetch_array($qpdct))
	{
	 $val=$qpdct1['bill_no'].'@'.$qpdct1['bill_no'].'#';
	 echo  $val;
    }
	
}

///////////////////////////////////
if($_POST["type"]=="orderitmload") // gov
{
	$orderno=$_POST['orderno'];
	
	$qpdct = mysqli_query($link," SELECT a.item_id,b.item_name FROM `inv_purchase_order_details` a,item_master b where a.order_no='$orderno' and a.item_id=b.item_id order by `item_name` ");
	while($qpdct1=mysqli_fetch_array($qpdct))
	{
	 $val=$qpdct1['item_id'].'@'.$qpdct1['item_name'].'#';
	 echo  $val;
    }
	
}
/////////////////////////////////
/*elseif($type=="purchasercvdorderno") //for order Serch for main store
{
	$orderno=$_POST['orderno'];
	
	$q=mysqli_fetch_array(mysqli_query($link,"select a.order_no,a.supplier_id,b.name from inv_purchase_order_master a,inv_supplier_master b where a.order_no='$orderno' and a.supplier_id=b.id "));
	$val=$orderno.'@'.$q['name'].'@'.$q['supplier_id'];
	
	
	
	ob_start();
	?>
		<select  name="txtcntrname"  id="txtcntrname" >

		<?php


		//$pid = mysqli_query($link," SELECT item_code,item_name FROM `ph_item_master` order by `item_name` ");

		$pid = mysqli_query($link," SELECT 	a.item_id,b.item_name FROM `inv_purchase_order_details` a,item_master b where a.order_no='$orderno' and a.item_id=b.item_id order by `item_name` ");
		while($pat1=mysqli_fetch_array($pid))
		{
		  echo "<option value='$pat1[item_id]'>$pat1[item_name]";

		  
		}
		?>
		</select>
		
		<?php
		$sel=ob_get_clean();
		$val.='@'.$sel;
		echo $val;
}*/


/////////////////////////////////
elseif($type=="supplier_payment") //for order Serch for main store
{
	$billno=$_POST['blno'];
	$spplirid=$_POST['spplirid'];
	$billno=explode("@#",$billno);
	$vnwpaid=0;
	foreach($billno as $blno)
	{		
		if($blno)
		{
		   $blnonw=explode("%%",$blno);
		   $qmrp=mysqli_fetch_array(mysqli_query($link,"select bill_no,bill_date,net_amt from inv_main_stock_received_master where  supp_code='$spplirid' and bill_no='$blnonw[0]'  order by slno"));
		   $vnwpaid+=$blnonw[1];
		   
	   }
	  } 
	
	
	
	echo $vnwpaid;
}
/////////////////////////////////
elseif($type=="invmainstritmissue") //for order Serch for main store
{
	$itmid=$_POST['itmid'];
	$itmid1=explode("-#",$itmid);
	$batchno=$_POST['batchno'];
	$qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from inv_main_stock_received_detail where  item_id='$itmid1[1]' and  	recept_batch='$batchno'"));
	$q=mysqli_fetch_array(mysqli_query($link,"select closing_stock,exp_date from inv_maincurrent_stock where  item_id='$itmid1[1]' and batch_no='$batchno' "));
	$val=$substrid.'@'.$q['closing_stock'].'@'.$qmrp['recpt_mrp'].'@'.$q['exp_date'];
	echo $val;
}

/////////////////////////////////
elseif($type=="orddtls") //for order Serch for main store Receipt
{
	$orderno=$_POST['orderno'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_order_details where order_no='$orderno' and stat=0"));
	$qsplr=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$q[SuppCode]'"));
	$val=$orderno.'@'.$qsplr['name'].'@'.$q['order_date'];
	echo $val;
}
/////////////////////////////////
elseif($type=="splr_bill_detail") //Bill detail
{
	$splirid=$_POST['splirid'];
	$billno=$_POST['billno'];
	$vpaid=0;
	
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_main_stock_received_master where  supp_code='$splirid' and bill_no='$billno'"));
	$qsplrpaymnt=mysqli_fetch_array(mysqli_query($link,"select paid from inv_supplier_payment_master where  supp_code='$splirid' and bill_no='$billno'"));
	
	if(!$qsplrpaymnt)
	{
		$vpaid=0;
	}
	else
	{
		$vpaid=$qsplrpaymnt['paid'];
	}
	
	$val=$splirid.'@'.$q['net_amt'].'@'.$vpaid;
	echo $val;
}

/////////////////////////////////
elseif($type=="substrordersrch") //for order Serch for Sub store order
{
	$orderno=$_POST['orderno'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_substore_order_details where order_no='$orderno' and stat=0"));
	mysqli_query($link,"delete from inv_substore_order_details_temp ");
	
	mysqli_query($link,"insert into inv_substore_order_details_temp select * from inv_substore_order_details where order_no='$orderno' and stat=0 and substore_id='$q[substore_id]' ");
	$val=$orderno.'@'.$q['substore_id'].'@'.$q['order_date'];
	echo $val;
}

/////////////////////////////////
elseif($type=="amountdisplay") //for indent received
{
	$ordno=$_POST['ordno'];
	$invid=$_POST['invid'];
	$q=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxamt from inv_indent_rcvdetails_temp where 	invoiceno='$invid' and order_no='$ordno'"));
	
	$val=$q['maxamt'].'@'.$q['maxamt'];
	echo $val;
}


/////////////////////////////////
elseif($type=="indentrcv") //for indent received
{
	$pid=$_POST['pid'];
	$ordrno=$_POST['ordrno'];
	$splrid=$_POST['splrid'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_master where id='$pid'"));
	
	$q2=mysqli_fetch_array(mysqli_query($link,"select order_qnt from inv_indent_order_details where order_no='$ordrno' and item_code='$pid'"));
	$val=$pid.'@'.$q['name'].'@'.$q['price'].'@'.$q2['order_qnt'];
	echo $val;
}


/////////////////////////////////
elseif($type=="loadsubcatgry") //for sub category
{
	$subcatid=$_POST['subcatid'];
	
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_subcategory where sub_cat_id='$subcatid'"));
	
	$val=$subcatid.'@'.$q['sub_cat_name'].'@'.$q['inv_cate_id'];
	echo $val;
}
/////////////////////////////////
elseif($type=="indmaster") //for sub indent
{
	$id=$_POST['id'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_master where id='$id'"));
	
	$val=$id.'@'.$q['inv_cate_id'].'@'.$q['sub_cat_id'].'@'.$q['name'].'@'.$q['unit'].'@'.$q['re_order_qnty'].'@'.$q['gst'].'@'.$q['specific_type'].'@'.$q['price'];
	echo $val;
}

/////////////////////////////////
elseif($type=="subtore") //for sub store
{
	$id=$_POST['id'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_sub_store where substore_id='$id'"));
	$val=$id.'@'.$q['substore_name'];
	echo $val;
}


/////////////////////////////////
elseif($type=="invindntcatgry") //for inv categorry
{
	$id=$_POST['id'];
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_indent_type where inv_cate_id='$id'"));
	$val=$id.'@'.$q['name'];
	echo $val;
}


/////////////////////////////////
elseif($type=="indent_pndng_order") //for indend approved
{
	$orderid=$_POST['orderid'];
	$sbstrid=$_POST['sbstrid'];
	
	$q=mysqli_fetch_array(mysqli_query($link,"select * from inv_substore_indent_order_master where order_no='$orderid' and substore_id='$sbstrid'"));
	$val=$orderid.'#'.$q['substore_id'];
	echo $val;
}

///////////////////////////////////
elseif($type=="subcatload") // sub category load indent master
{
	$cateid=$_POST['cateid'];
	$qpdct=mysqli_query($link,"select * from inv_subcategory where inv_cate_id='$cateid' order by sub_cat_name ");
	while($qpdct1=mysqli_fetch_array($qpdct))
	{
	 $val=$qpdct1['sub_cat_id'].'@'.$qpdct1['sub_cat_name'].'#';
	 echo  $val;
    }
	
}

?>
