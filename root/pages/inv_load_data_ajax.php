<?php
include'../../includes/connection.php';

session_start();
$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];


function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}

function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}


$type=$_POST['type'];
?>
 <table class="table table-striped table-bordered">
   
 <?php

if($type=="indent_ord_temp") ///For load purchase ordertemp item
	{
		$orderno=$_POST['orderno'];
		
		$qrpdct=mysqli_query($link,"select a.*,b.name from inv_indent_order_details_temp a,inv_indent_master b  where a.item_code=b.id and a.order_no='$orderno' order by b.name ");
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr>
             <td><?php echo $qrpdct1['order_no'];?></td>
             <td><?php echo $qrpdct1['item_code'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             <td><?php echo $qrpdct1['order_qnt'];?></td>
             
          
           <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_code'];?>','<?php echo $qrpdct1['order_no'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		   ;}
	  }	
	  
 
///////////////////////////////////////////////////////////	  
elseif($type=="indent_order") ///For Indent Order
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from inv_indent_master where name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from inv_indent_master  order by name";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
          
         </tr>	
         <?php	
		   $i++;}
	  }

//////////////////////////
if($type=="splr_bill_load")  //// inv Bill Load
{
	$spplrid=$_POST['spplrid'];
	
	?>
	
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Bill No</th><th>Bill Date</th><th>Bill Amount</th><th>Already Paid</th><th>Now Paid</th><th>&nbsp;</th>
		</tr>
	<?php
	$i=1;
	$class="pats";
	$q=mysqli_query($link,"select bill_no,bill_date,net_amt from inv_main_stock_received_master where  supp_code='$spplrid' and bill_no not in(select bill_no from inv_supplier_payment_master where supp_code='$spplrid' and balance=0 )  order by slno");
	
	
	while($r=mysqli_fetch_array($q))
	{
		$valrdypaid="0.00";
		$qinvpaid=mysqli_fetch_array(mysqli_query($link,"select paid from inv_supplier_payment_master where supp_code='$spplrid' and bill_no='$r[bill_no]'"));
		if($qinvpaid)
		{
			$valrdypaid=$qinvpaid['paid'];
		}
		
		$vnwpaid=$r['net_amt']-$valrdypaid;
		$vpaid=$valrdypaid+$vnwpaid;
		$chk="";
		if($vpaid>$r[net_amt])
		{
			$chk="disabled";
			$class="n_pats";
		}
		
		
	?>
		<tr>
			<td><input type="checkbox"  id="<?php echo $i;?>" class="<?php echo $class;?>" value="<?php echo $r['bill_no'];?>" onclick="add_netamt('<?php echo $spplrid;?>','<?php echo $vnwpaid;?>','<?php echo $i;?>')" <?php echo $chk;?>/><label><span></span></label></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo $r['net_amt'];?></td>
			<td><?php echo $valrdypaid;?></td>
			<td><input type="text" style="width:100px" id="txtnwpaid_<?php echo $i;?>" value="<?php echo $vnwpaid;?>" onkeyup="chkstockanble(this.value,'<?php echo $r[net_amt];?>','<?php echo $valrdypaid;?>',<?php echo $i;?>)" ></td>
			<td><input type="hidden" style="width:40px" id="txtnetamt<?php echo $i;?>" value="<?php echo $r[net_amt];?>" readonly ></td>
		</tr>
	<?php
	$i++;
	}
	?>
	
	
	</table>
	<?php
}

//////////////////////////
if($type=="invmainstkavailrpt")  //// inv main stock report
{
	$catid=$_POST['catid'];
	
	?>
	<button type="button" class="btn btn-success" onclick="stk_exp()">Export to excel</button>
	<button type="button" class="btn btn-info" onclick="stk_prr()">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>Expiry Date</th><th>Available Stock</th>
		</tr>
	<?php
	$i=1;
    $qry="SELECT a.*,b.item_name FROM `inv_maincurrent_stock` a,item_master b WHERE a.`closing`>0 and a.item_id=b.item_id  order by b.item_name";
	//echo $qry;
	
	$q=mysqli_query($link,$qry);
	while($r=mysqli_fetch_assoc($q))
	{
		$vclsngqnt=0;
		$vitmid="";
		$vclsngqnt=$r['closing'];
		$vitmid=$r['item_id'];
		$vexpiry=$r['exp_date'];
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $vitmid;?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo date("Y-m", strtotime($vexpiry));?></td>
			<td><?php echo $vclsngqnt;?></td>
		</tr>
	<?php
	$i++;
	}
	?>
	</table>
	<?php
}

//////////////////////////
if($type=="itemreorder")  //// inv main stock report
{
	$catid=$_POST['catid'];
	
	?>
	<button type="button" class="btn btn-default" onclick="stk_reorder_exp()">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="stk_prr_reorder()">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Reorder Qnty</th><th>Available Stock</th>
		</tr>
	<?php
	$i=1;
	
		
	$q=mysqli_query($link,"SELECT item_id,item_name,re_order FROM `item_master`  WHERE re_order>0  and need=0  order by item_name");
	
	while($r=mysqli_fetch_array($q))
	{
		$qphstk=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(quantity),0) as mxphstk from ph_stock_master where item_code='$r[item_id]' "));
		$qcntlstk=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(closing),0) as mxcntrlstk from inv_maincurrent_stock where item_id='$r[item_id]' "));				
		
		$vttlstk=$qphstk['mxphstk']+$qcntlstk['mxcntrlstk'];
		if($vttlstk<$r['re_order'])
		{
			
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $r['re_order'];?></td>
			<td><?php echo $vttlstk;?></td>
				
		</tr>
		
		<?php
	$i++;
	}
	?>
	
		<?php
	}?>
		
	
	
	
	</table>
	<?php
}


//////////////////////////
if($type=="invmainstkavailrpt_itemwise")  //// inv main stock report
{
	$itemid1=$_POST['itemid'];
	//$itemid=explode("-#",$itemid1);
	$itemid[1]=$itemid1;
	?>
	<!--<button type="button" class="btn btn-default" onclick="stk_exp()">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="stk_prr()">Print</button>-->
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Pharmacy</th>
			<th>Store</th>
			<th>Total <small>(Tab/Caps)</small></th>
		</tr>
		</thead>
	<?php
	$i=1;
	
	  
	  $qitmname=mysqli_fetch_array(mysqli_query($link,"select item_name from item_master where item_id='$itemid[1]'"));
	  
	  $qcntrlstk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(closing),0) as maxcntrlstk FROM `inv_maincurrent_stock`  WHERE `closing`>0 and item_id='$itemid[1]'   "));
      $phr=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(quantity),0) as maxphstk FROM `ph_stock_master`  WHERE `quantity`>0  and item_code='$itemid[1]' and substore_id='1'"));
		
	
	 $qcntrldtl=mysqli_query($link,"select * from inv_maincurrent_stock where  `closing`>0 and item_id='$itemid[1]'");
	 while($qntrldtl1=mysqli_fetch_array($qcntrldtl))
	 {
		 $vcntrlbatch.=$qntrldtl1['batch_no'].' - '.$qntrldtl1['closing']."</br>";
	 }
	 
	 $qphdtl=mysqli_query($link,"select * from ph_stock_master where  `quantity`>0 and item_code='$itemid[1]' and substore_id='1'");
	 while($qphdtl1=mysqli_fetch_array($qphdtl))
	 {
		 $vphbatch.=$qphdtl1['batch_no'].' - '.$qphdtl1['quantity']."</br>";
	 }
		//$vcntrlbatch1="(".$vcntrlbatch.")";
		
		$vttl=$qcntrlstk['maxcntrlstk']+$phr['maxphstk'];
		
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $itemid[1];?></td>
			<td><?php echo $qitmname['item_name'];?></td>
			<td><?php echo $phr['maxphstk'];?></td>
			<td><?php echo $qcntrlstk['maxcntrlstk'];?></td>
			<td><?php echo $vttl;?></td>
			
		</tr>
	
	
	
	</table>
	<?php
}

//////////////////////////
if($type=="inv_substr_stk_availble")  //// inv main stock report
{
	?>
	<!--<button type="button" class="btn btn-default" onclick="stk_exp()">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="stk_prr()">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Available Stock</th>
		</tr>
		<?php
		$qsubstrnm=mysqli_query($link,"SELECT distinct a.substore_id,b.substore_name FROM inv_substorecurrent_stock a,inv_sub_store b WHERE a.substore_id=b.substore_id and a.closing>0 order by b.substore_name");
		while($qsubstrnm1=mysqli_fetch_array($qsubstrnm))
		{
		?>
		<tr>
			<td colspan="4"><b><i>Substore Name : <?php echo $qsubstrnm1['substore_name'];?></i></b></td>
		</tr>
	<?php
	$i=1;
	
	$q=mysqli_query($link,"SELECT * FROM `inv_substorecurrent_stock` WHERE  substore_id='$qsubstrnm1[substore_id]' and  `closing`>0");
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_indent_master` WHERE `id`='$r[item_code]'"));
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['name'];?></td>
			<td><?php echo $r['closing'];?></td>
		</tr>
	<?php
	$i++;
	}
	?>
	<?php
  }?>
	</table>
	<?php
}	  
////////////////////////////////////////////////////////////

elseif($type=="load_mainstkentry_tmp")
{
	$orderno=$_POST['orderno'];
	$splrid=$_POST['splrid'];
	$billno=$_POST['billno'];
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Receipt No</th><th>Bill No</th><th>Item Code</th><th>Item Name</th><th>Quantity</th><th>Amount</th><th></th>
			</tr>
		<?php
		
		$qrpdct=mysqli_query($link,"select a.*,b.name from inv_main_stock_receied_detail_temp a,inv_indent_master b  where a.item_code=b.id and a.receipt_no='$orderno' and a.supplier_bill_no='$billno'  order by b.name ");
		
		while($qrpdct1=mysqli_fetch_array($qrpdct))
		{
			//$vttl=$qrpdct1['recpt_quantity']*$qrpdct1['recept_cost_price'];
			$vttl=0;
	     ?>
         <tr>
             <td><?php echo $qrpdct1['receipt_no'];?> </td>
             <td><?php echo $billno;?> </td>
             <td><?php echo $qrpdct1['item_code'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             <td><?php echo $qrpdct1['qnty'];?></td>
             <td><?php echo $qrpdct1['total_amount'];?></td>
             <td><a href="javascript:delete_data('<?php echo $orderno;?>','<?php echo $billno;?>','<?php echo $qrpdct1['item_code'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
         </tr>	
         <?php	
		  }
		 ?>
		 </table>
		 <?php
}	  
//////////////////////
elseif($type=="substore_order_tmp")///////////for substore
{
	$orderno=$_POST['orderno'];
	$substrid=$_POST['substrid'];
		?>
		 <tr>
			 <td>Order No</td>
			 <td>Item No</td>
			 <td>Item Name</td>
			 <td>Qnty</td>
			 <td>Remove</td>
		 </tr>
		<?php
		$qrpdct=mysqli_query($link,"select a.*,b.item_name from inv_substore_order_details_temp a,item_master b  where a.item_id=b.item_id and a.order_no='$orderno' and a.substore_id='$substrid' order by b.item_name ");
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr>
             <td><?php echo $qrpdct1['order_no'];?></td>
             <td><?php echo $qrpdct1['item_id'];?></td>
             <td><?php echo $qrpdct1['item_name'];?></td>
             <td><?php echo $qrpdct1['order_qnt'];?></td>
          
           <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_id'];?>','<?php echo $qrpdct1['order_no'];?>','<?php echo $qrpdct1['substore_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		   ;}
}


//////////////////////
elseif($type=="purchase_order_tmp")///////////for Purchase Order
{
	$orderno=$_POST['orderno'];
	$spplrid=$_POST['spplrid'];
		?>
		 <tr>
			 <td>Order No</td>
			 <td>Item No</td>
			 <td>Item Name</td>
			 <td>Qnty</td>
			 <td>Remove</td>
		 </tr>
		<?php
		
		$qrpdct=mysqli_query($link,"select a.*,b.item_name from inv_purchase_order_details_temp a,item_master b  where a.item_id=b.item_id and a.order_no='$orderno' and a.supplier_id='$spplrid' and user='$userid' order by b.item_name ");
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr>
             <td><?php echo $qrpdct1['order_no'];?></td>
             <td><?php echo $qrpdct1['item_id'];?></td>
             <td><?php echo $qrpdct1['item_name'];?></td>
             <td><?php echo $qrpdct1['order_qnt'];?></td>
          
           <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_id'];?>','<?php echo $qrpdct1['order_no'];?>','<?php echo $qrpdct1['supplier_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		   ;}
}
//////////////////////
elseif($type=="invsubstritmissue")///////////from substore Item Issue
{
	$issueno=$_POST['issueno'];
	$substrid=$_POST['substrid'];
		?>
		 <tr>
			 <td>Order No</td>
			 <td>Item No</td>
			 <td>Item Name</td>
			 <td>Qnty</td>
			 <td>Remove</td>
		 </tr>
		<?php
		$qrpdct=mysqli_query($link,"select a.*,b.name from inv_substore_issue_details_temp a,inv_indent_master b  where a.item_code=b.id and a.issu_no='$issueno' and a.substore_id='$substrid' order by b.name ");
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr>
             <td><?php echo $issueno;?></td>
             <td><?php echo $qrpdct1['item_code'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             <td><?php echo $qrpdct1['issue_qnt'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_code'];?>','<?php echo $issueno;?>','<?php echo $qrpdct1['substore_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		   ;}
}

//////////////////////
elseif($type=="invmainstritmissue")///////////from main Item Issue
{
	$issueno=$_POST['issueno'];
	$substrid=$_POST['substrid'];
		?>
		 <tr>
			 <td>Order No</td>
			 <td>Item No</td>
			 <td>Item Name</td>
			 <td>Item Batch</td>
			 <td>MRP</td>
			 <td>Expiry date</td>
			 <td>Qnty.</td>
			 <td>Remove</td>
		 </tr>
		<?php
		$qrpdct=mysqli_query($link,"select a.*,b.item_name from inv_mainstore_issue_details_tmp a,item_master b  where a.item_id=b.item_id and a.issu_no='$issueno' and a.substore_id='$substrid' order by b.item_name ");
		
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		$qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from inv_main_stock_received_detail where  item_id='$qrpdct1[item_id]' and recept_batch='$qrpdct1[batch_no]'  "));
		$qexpiry=mysqli_fetch_array(mysqli_query($link,"select exp_date from inv_maincurrent_stock where  item_id='$qrpdct1[item_id]' and batch_no='$qrpdct1[batch_no]'  "));
		$vttl=0;
		 
	     ?>
         <tr>
             <td><?php echo $issueno;?></td>
             <td><?php echo $qrpdct1['item_id'];?></td>
             <td><?php echo $qrpdct1['item_name'];?></td>
             <td><?php echo $qrpdct1['batch_no'];?></td>
             <td><?php echo $qmrp['recpt_mrp'];?></td>
             <td><?php echo $qexpiry['exp_date'];?></td>
             <td><?php echo $qrpdct1['issue_qnt'];?></td>
        
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_id'];?>','<?php echo $issueno;?>','<?php echo $qrpdct1['substore_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		   ;}
		?>
		
		
		<?php
		   
}



//////////////////////
elseif($type=="invitemretruntospplr_tmp")///////////from Item Return
{
	$rtrnno=$_POST['rtrnno'];
	$spplrid=$_POST['spplrid'];
		?>
		 <tr>
			 <td>#</td>
			 <td>Retrun No</td>
			 <td>Item No</td>
			 <td>Item Name</td>
			 <td>Item Batch</td>
			 <td>Expiry </td>
			 <td>MRP</td>
			 <td>Rate</td>
			 <td>Qnty.</td>
			 <td>GST Amt.</td>
			 <td>Amount</td>
			 <td>Remove</td>
		 </tr>
		<?php
		$i=1;
		$qrpdct=mysqli_query($link,"select a.*,b.item_name from inv_item_return_supplier_detail_temp a,item_master b  where a.item_id=b.item_id and a.returnr_no='$rtrnno' and a.supplier_id='$spplrid' order by b.item_name ");
		
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		$itmamt=$qrpdct1['item_amount']+$qrpdct1['gst_amount'];		
		$vttl=$vttl+$qrpdct1['item_amount']+$qrpdct1['gst_amount'];	
		 
	     ?>
         <tr>
             <td><?php echo $i;?></td>
             <td><?php echo $rtrnno;?></td>
             <td><?php echo $qrpdct1['item_id'];?></td>
             <td><?php echo $qrpdct1['item_name'];?></td>
             <td><?php echo $qrpdct1['batch_no'];?></td>
             <td><?php echo $qrpdct1['expiry_date'];?></td>
             <td><?php echo $qrpdct1['recpt_mrp'];?></td>
             <td><?php echo $qrpdct1['recept_cost_price'];?></td>
             <td><?php echo $qrpdct1['quantity'];?></td>
             <td><?php echo $qrpdct1['gst_amount'];?></td>
             <td><?php echo number_format($itmamt,2);?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_id'];?>','<?php echo $qrpdct1['batch_no'];?>','<?php echo $rtrnno;?>','<?php echo $spplrid;?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		  $i++ ;}
		?>
		
		<tr>
			<td colspan="10" style="font-weight:bold;font-size:11px;text-align:right">Total </td>
			<td style="font-weight:bold;font-size:11px;text-align:right"><?php echo number_format($vttl,2);?> </td>
			<td>&nbsp;</td>
			
		</tr>
		
		<?php
		   
}

///////////////////////////////////////
if($type=="load_spplr_billwise")
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$billno=$_POST['billno'];
	 
	$qry_type=1;
	if($qry_type==1)
	{
		if($billno!="")
		{
			$q=mysqli_query($link,"SELECT * FROM ph_purchase_receipt_master WHERE bill_no='$billno' order by bill_date desc");
		}
		else if($splrid==0)
		{
			$q=mysqli_query($link,"SELECT * FROM ph_purchase_receipt_master WHERE bill_date between '$fdate' and '$tdate' order by bill_date desc");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM ph_purchase_receipt_master WHERE bill_date between '$fdate' and '$tdate' and supp_code='$splrid' order by bill_date desc");
		}
		?>
		<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Received No</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Bill Amount</th>
			<th>User</th>
			<th>View / Excel</th>
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			$vttl=$vttl+$r['net_amt'];
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td style="text-align:right"><?php echo $r['net_amt'];?></td>
			<td ><?php echo $quser['name'];?></td>
			<td>
				<button type="button" class="btn btn-info" onclick="ph_rcv_print('<?php echo base64_encode($r['order_no']);?>')">View</button>
				<!--<button type="button" class="btn btn-success" onclick="bill_report_xls('<?php echo $r[bill_no];?>','<?php echo $r[supp_code];?>','<?php echo $r[recpt_date];?>')">Export to excel</button>-->
			</td>
				
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		<tr>
			<td colspan="5" style="text-align:right;font-weight:bold;">Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttl,2);?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
		<?php
	}
	if($qry_type==2)
	{
		if($billno !="")
		{
			$q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_no='$billno' and del =0 order by bill_date desc");
		}
		elseif($splrid==0)
		{
          $q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and del =0  order by bill_date desc");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and supp_code='$splrid' and del =0 order by bill_date desc");
		}

	
	?>
	<button type="button" class="btn btn-success" onclick="supplier_summery_print_excel('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Excel</button>
	<button type="button" class="btn btn-info" onclick="supplier_summery_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Received No</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Bill Amount</th>
			<th >User</th>
			<th>View / Excel</th>
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			$vttl=$vttl+$r['net_amt'];
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $r['receipt_no'];?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td style="text-align:right"><?php echo $r['net_amt'];?></td>
			<td ><?php echo $quser['name'];?></td>
			<td><button type="button" class="btn btn-info" onclick="report_print_billwise('<?php echo $r[bill_no];?>','<?php echo $r[supp_code];?>','<?php echo $r[recpt_date];?>')">View</button> <button type="button" class="btn btn-success" onclick="bill_report_xls('<?php echo $r[bill_no];?>','<?php echo $r[supp_code];?>','<?php echo $r[recpt_date];?>')">Export to excel</button></td>
				
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		<tr>
			<td colspan="6" style="text-align:right;font-weight:bold">Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttl,2);?></td>
			<td></td>
		</tr>
	</table>
	<?php
	}
}



///////////////////////////////////////
if($type=="load_spplr_payment") ///////load supplier payment
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$billno=$_POST['billno'];
	 
	
	if($billno !="")
	{
		$q=mysqli_query($link,"SELECT * FROM inv_supplier_payment_master  WHERE bill_no='$billno' order by date desc");
	}
	elseif($splrid==0)
		{
          $q=mysqli_query($link,"SELECT * FROM inv_supplier_payment_master  WHERE date between '$fdate' and '$tdate'  order by date desc");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM inv_supplier_payment_master  WHERE date between '$fdate' and '$tdate' and supp_code='$splrid' order by slno");
		}

	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="pay_detail_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $billno;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Pay Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Bill Amount</th>
			<th style="text-align:right">Paid</th>
			<th style="text-align:right">Balance</th>
			<th style="text-align:right">Cheque No</th>
			<th >User</th>
			
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$qbill=mysqli_fetch_array(mysqli_query($link,"select * from inv_main_stock_received_master where bill_no='$r[bill_no]' and supp_code='$r[supp_code]'"));
			$qbilldetail=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_payment_details where bill_no='$r[bill_no]' and supp_code='$r[supp_code]'"));
			
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$qbilldetail[user]'"));
			$vttl=$vttl+$qbill['net_amt'];
			$vttlpaid=$vttlpaid+$r['paid'];
			$vttlbal=$vttlbal+$r['balance'];
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($qbill['bill_date']);?></td>
			<td><?php echo convert_date($qbilldetail['date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td style="text-align:right"><?php echo $qbill['net_amt'];?></td>
			<td style="text-align:right"><?php echo $r['paid'];?></td>
			<td style="text-align:right"><?php echo $r['balance'];?></td>
			<td style="text-align:right"><?php echo $qbilldetail['cheque_no'];?></td>
			<td ><?php echo $quser['name'];?></td>
			
				
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		<tr>
			<td colspan="5" style="text-align:right;font-weight:bold">Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttl,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttlpaid,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttlbal,2);?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<?php
}



///////////////////////////////////////
if($type=="load_bill_cancel") ///////load supplier payment
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	 
	
	if($splrid==0)
		{
          $q=mysqli_query($link,"SELECT * FROM inv_main_bill_cancel_master  WHERE cancel_date between '$fdate' and '$tdate'  order by cancel_date desc");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM inv_main_bill_cancel_master  WHERE cancel_date between '$fdate' and '$tdate' and supp_code='$splrid' order by slno");
			
		}

	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="srch_cancel_bill_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $billno;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Receipt No</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Cancelled Date</th>
			<th>Cancelled Reason</th>
			<th>Supplier</th>
			<th style="text-align:right">Bill Amount</th>
			<th style="text-align:right">Cancel By</th>
			<th style="text-align:right">Time</th>
			
			
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			
			
			
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[cancel_by_user]'"));
			$vttl=$vttl+$r['net_amt'];
			
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['receipt_no'];?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo convert_date($r['cancel_date']);?></td>
			<td><?php echo $r['cancel_reason'];?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td style="text-align:right"><?php echo $r['net_amt'];?></td>
			<td style="text-align:right"><?php echo $quser['name'];?></td>
			<td style="text-align:right"><?php echo $r['cancel_time'];?></td>
		
				
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		<tr>
			<td colspan="7" style="text-align:right;font-weight:bold">Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttl,2);?></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<?php
}

///////////////////////////////////////
if($type=="load_spplr_rtrn_gst") ///////load supplier payment
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="supplier_return_gst_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $billno;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Return Date</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">CGST</th>
			<th style="text-align:right">SGST</th>
		
		</tr>
		
		
		<?php
		
		
		
		$qgst=mysqli_query($link,"SELECT distinct gst_per FROM inv_item_return_supplier_detail  WHERE date between '$fdate' and '$tdate' order by gst_per");
		while($qgst1=mysqli_fetch_array($qgst))
		{
			$i=1;
			$subttlamt=0;
			$subttlcgst=0;
		
		?>
		  <tr>
			  <td colspan="5" style="font-weight:bold">Gst <?php echo number_format($qgst1['gst_per'],0);?> %</td>
		  </tr>
		<?php
        
               
        $q=mysqli_query($link,"SELECT distinct date FROM inv_item_return_supplier_detail   WHERE date between '$fdate' and '$tdate' and gst_per='$qgst1[gst_per]' order by date");
		while($r=mysqli_fetch_array($q))
		{
			
			$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt FROM inv_item_return_supplier_detail   WHERE date='$r[date]'  and gst_per='$qgst1[gst_per]' "));
			$qbilldetail=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_payment_details where bill_no='$r[bill_no]' and supp_code='$r[supp_code]'"));
			$vgstamt1=$qamt['maxamt']*$qgst1['gst_per']/100;
			$vgstamt=round($vgstamt1);
			$cgst=$vgstamt/2;
			
			$subttlamt+=$qamt['maxamt'];
			$subttlcgst+=$cgst;
			
			?>
			 <tr>
			  <td ><?php echo $i;?> </td>
			  <td ><?php echo convert_date($r['date']);?> </td>
			  <td style="text-align:right"><?php echo $qamt['maxamt'];?> </td>
			  <td style="text-align:right"><?php echo number_format($cgst,2);?> </td>
			  <td style="text-align:right"><?php echo number_format($cgst,2);?> </td>
		  </tr>
			
		<?php
	  $i++; }	
		?>
		
		  <tr>
			<td colspan="2" style="text-align:right;font-weight:bold">Sub Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlamt,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlcgst,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlcgst,2);?></td>
			
		</tr>
		
		<?php
	 }?>
		
		</tr>
	</table>
	<?php
}



///////////////////////////////////////
if($type=="load_spplr_received_gst") ///////load received gst
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="supplier_rcvd_gst_print('<?php echo $splrid;?>','<?php echo $fdate;?>','<?php echo $tdate;?>','<?php echo $billno;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Bill Date</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">CGST</th>
			<th style="text-align:right">SGST</th>
		
		</tr>
		
		
		<?php
		//////////for Pharmacy Item Only
		
		
		//$qgst=mysqli_query($link,"SELECT distinct a.gst_per FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' order by gst_per");
		 $qgst=mysqli_query($link,"SELECT distinct a.gst_per FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' and a.item_id=c.item_id and c.category_id='1' order by a.gst_per");
		while($qgst1=mysqli_fetch_array($qgst))
		{
			$i=1;
			$subttlamt=0;
			$subttlcgst=0;
		
		?>
		  <tr>
			  <td colspan="5" style="font-weight:bold">Gst <?php echo number_format($qgst1['gst_per'],0);?> %</td>
		  </tr>
		<?php
        
               
        $q=mysqli_query($link,"SELECT distinct b.bill_date FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date between '$fdate' and '$tdate' and a.gst_per='$qgst1[gst_per]' order by b.bill_date");
		while($r=mysqli_fetch_array($q))
		{
			
			//$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt FROM inv_main_stock_received_detail a,inv_main_stock_received_master b  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date='$r[bill_date]'  and a.gst_per='$qgst1[gst_per]' "));
			//$qbilldetail=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_payment_details where bill_no='$r[bill_no]' and supp_code='$r[supp_code]'"));
			
			$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(a.item_amount),0) as maxamt FROM inv_main_stock_received_detail a,inv_main_stock_received_master b,item_master c  WHERE a.bill_no=b.bill_no and a.SuppCode=b.supp_code and b.bill_date='$r[bill_date]'  and a.gst_per='$qgst1[gst_per]' and a.item_id=c.item_id and c.category_id='1' "));
			$vgstamt1=$qamt['maxamt']*$qgst1['gst_per']/100;
			$vgstamt=round($vgstamt1);
			$cgst=$vgstamt/2;
			
			$subttlamt+=$qamt['maxamt'];
			$subttlcgst+=$cgst;
			
			?>
			 <tr>
			  <td ><?php echo $i;?> </td>
			  <td ><?php echo convert_date($r['bill_date']);?> </td>
			  <td style="text-align:right"><?php echo $qamt['maxamt'];?> </td>
			  <td style="text-align:right"><?php echo number_format($cgst,2);?> </td>
			  <td style="text-align:right"><?php echo number_format($cgst,2);?> </td>
		  </tr>
			
		<?php
	  $i++; }	
		?>
		
		  <tr>
			<td colspan="2" style="text-align:right;font-weight:bold">Sub Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlamt,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlcgst,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($subttlcgst,2);?></td>
			
		</tr>
		
		<?php
	 }?>
		
		</tr>
	</table>
	<?php
}

///////////////////////////////////////
if($type=="load_spplr_return")
{
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	 
	
	if($splrid==0)
		{
          $q=mysqli_query($link,"SELECT * FROM inv_item_return_supplier_master  WHERE date between '$fdate' and '$tdate'  order by slno");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM inv_item_return_supplier_master  WHERE  date between '$fdate' and '$tdate' and  supplier_id='$splrid' order by slno");
		}
	
	
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Return No</th>
			<th> Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">GST</th>
			<th>View</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supplier_id]'"));
			
			$qamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(item_amount),0) as maxamt,ifnull(sum(gst_amount),0) as maxgstamt from inv_item_return_supplier_detail where returnr_no='$r[returnr_no]' and supplier_id='$r[supplier_id]'"));
			$vreturnamt=0;
			$vreturnamt=$qamt['maxamt']+$qamt['maxgstamt'];
			$vgstamtttl1+=$qamt['maxgstamt'];
			$vttl1=$vttl1+$vreturnamt;
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['returnr_no'];?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td style="text-align:right"><?php echo number_format($vreturnamt,2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt['maxgstamt'],2);?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>-->
			<td><button type="button" class="btn btn-info" onclick="report_print_return('<?php echo $r[returnr_no];?>','<?php echo $r[supplier_id];?>')">View</button></td>
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		<?php
		$vttl=round($vttl1);
		$vgstamtttl=round($vgstamtttl1);
		?>
		<tr>
			<td colspan="4" style="text-align:right;font-weight:bold">Total(Rounded) :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttl,2);?></td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vgstamtttl,2);?></td>
			<td></td>
			
		</tr>
	</table>
	<?php
}

/////////////////////////////////
if($_POST["type"]=="load_item_expiry_supplier")
{
	//~ $fdate=date('Y-m-d');
	//~ $tdate=date('Y-m-d', strtotime($fdate. ' + 90 days'));
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$days=strtotime($tdate)-strtotime($fdate);
	$days=abs(round($days/86400));
	
	$qry="SELECT distinct c.SuppCode,d.`name` FROM inv_maincurrent_stock a,item_master b, inv_main_stock_received_detail c, `inv_supplier_master` d WHERE a.item_id=b.item_id AND a.item_id=c.item_id AND a.`batch_no`=c.`recept_batch` AND c.SuppCode=d.id AND c.SuppCode!='0' and a.closing>0 and a.exp_date between '$fdate' and '$tdate' ORDER BY d.`name` ";
	//echo $qry;
	$qq=mysqli_query($link,$qry);
	?>
	<b><?php echo $days;?> days expiry items</b>
	<?php
	$user=$_POST['user'];
	$p_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$user' "));
	$user_access=array(101,102,103,154);
	if(in_array($p_info['emp_id'],$user_access))
	{
	?>
	<span class="noprint" style="float:right;">
		<button type="button" class="btn btn-primary" id="act_btn" onclick="print_item_expiry_supplier('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	</span>
	<?php
	}
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>MRP</th>
			<th>Rate</th>
			<th>Batch No</th>
			<th>Stock <small>(Tab/Caps)</small></th>
			<th>Expiry Date</th>
			<th>Bill No.</th>
		</tr>
		<?php
		while($rr=mysqli_fetch_assoc($qq))
		{
		?>
		<tr>
			<th colspan="9"><?php echo $rr['name'];?></th>
		</tr>
		<?php
		$j=1;
		$tot=0;
		$q=mysqli_query($link,"SELECT distinct(a.item_id),b.item_name,a.`batch_no`,c.`strip_quantity` FROM inv_maincurrent_stock a,item_master b, inv_main_stock_received_detail c WHERE a.item_id=b.item_id AND a.item_id=c.item_id and c.`SuppCode`='$rr[SuppCode]' and a.closing>0 and a.exp_date between '$fdate' and '$tdate' ORDER BY b.item_name");
		//echo "SELECT distinct(a.item_id),b.item_name,a.`batch_no` FROM inv_maincurrent_stock a,item_master b, inv_main_stock_received_detail c WHERE a.item_id=b.item_id AND a.item_id=c.item_id and c.`SuppCode`='$rr[SuppCode]' and a.closing>0 and a.exp_date between '$fdate' and '$tdate' ORDER BY b.item_name<br/>";
		while($r=mysqli_fetch_assoc($q))
		{
			$d=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `SuppCode`='$rr[SuppCode]' AND `item_id`='$r[item_id]' AND `recept_batch`='$r[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
			if(!$d)
			{
				$d=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_challan_receipt_details` WHERE `SuppCode`='$rr[SuppCode]' AND `item_id`='$r[item_id]' AND `recept_batch`='$r[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
				//$d=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `item_id`='$r[item_id]' AND `recept_batch`='$r[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
			}
			$qnt=mysqli_fetch_assoc(mysqli_query($link,"select `closing` from inv_maincurrent_stock  where item_id='$r[item_id]' and `batch_no`='$r[batch_no]' and exp_date between '$fdate' and '$tdate' and  closing>0"));
			if($d)
			{
		?>
		<tr class="rets<?php echo $rr['SuppCode'];?>">
			<td><?php echo $j;?></td>
			<td><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo number_format($d['recpt_mrp']*$r['strip_quantity'],2);?></td>
			<td><?php echo $d['cost_price'];?></td>
			<td><?php echo $d['recept_batch'];?></td>
			<td><?php echo $qnt['closing']/$r['strip_quantity'];?></td>
			<td><?php if($d['expiry_date']!="" && $d['expiry_date']!="0000-00-00" && $d['expiry_date']!="1970-01-01" && $d['expiry_date']!="1970-01-31"){echo date("Y-m", strtotime($d['expiry_date']));}?></td>
			<td><?php echo $d['bill_no'];?></td>
		</tr>
		<?php
		$tot+=$d['recept_cost_price']*$qnt['closing'];
		$j++;
		}
		}
		?>
		<tr>
			<th colspan="4" style="text-align:right;">Total</th>
			<td><?php echo number_format($tot,2);?></td>
			<td colspan="3"></td>
			<td>
				<!--<button type="button" class="btn btn-danger btn-mini" onclick="item_return('<?php echo $rr['SuppCode'];?>')">Return</button>-->
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="load_item_expiry")
{
	//~ $fdate=date('Y-m-d');
	//~ $tdate=date('Y-m-d', strtotime($fdate. ' + 90 days'));
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	$fid=0;
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-info" onclick="item_expiry_rep('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>MRP</th>
			<th>Batch No</th>
			<th>Stock</th>
			<th>Expiry Date</th>
			<th>Bill No.</th>
			<th>Supplier</th>
		</tr>
		<?php
		$n=1;
		
		$q=mysqli_query($link,"SELECT distinct(a.item_id),b.item_name FROM inv_maincurrent_stock a,item_master  b WHERE a.item_id=b.item_id and  a.closing>0 and a.exp_date between '$fdate' and '$tdate' ORDER BY a.exp_date");
		while($r=mysqli_fetch_array($q))
		{
			$vstk=0;
			$itmttlamt=0;
			$qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price from inv_main_stock_received_detail where item_id ='$r[item_id]' order by slno desc"));	
					
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $qmrp['recpt_mrp'];?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			
		</tr>
		
               <?php
			    $vrcprt=0;
			    $qbatch=mysqli_query($link,"select * from inv_maincurrent_stock  where item_id='$r[item_id]' and  exp_date between '$fdate' and '$tdate' and  closing>0");
				while($qbatch1=mysqli_fetch_array($qbatch))
				{
				$rec=mysqli_fetch_array(mysqli_query($link,"SELECT `order_no`,`bill_no`,SuppCode FROM `inv_main_stock_received_detail` WHERE `item_id`='$qbatch1[item_id]' AND `expiry_date`='$qbatch1[exp_date]' AND `recept_batch`='$qbatch1[batch_no]'"));
				$qsupplier=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$rec[SuppCode]' "));
				
				$vrcptamt=$qmrp['recept_cost_price']*$qbatch1['closing'];
				$itmttlamt=$itmttlamt+$vrcptamt;
				$vttlamt=$vttlamt+$vrcptamt;
				$vstk=$vstk+$qbatch1['closing'];
				?>	
				<tr >  
               
                    <td></td> 
                    <td></td> 
                    <td align="right"></td>
                    <td align="right"></td>
                    <td align="right" style="font-size:12px"><?php echo $qbatch1['batch_no'];?></td>
                    <td align="right" style="font-size:12px"><?php echo $qbatch1['closing'];?></td>
                    <td align="right" style="font-size:12px"><?php echo convert_date($qbatch1['exp_date']);?></td>
					<td><?php echo $rec['bill_no'];?></td>
                    <td><?php echo $qsupplier['name'];?></td>
                  </tr> 
                  <?php
					 ;}?>
                  
		<?php
		$n++;
		}
			
		?>
		
	</table>
	<?php
}

///////////////////////////////////////
if($type=="load_rcv_itm_wise")
{
	$itmid1=$_POST['itmid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$itmid=explode("-#",$itmid1);
	
		
	$q=mysqli_query($link,"SELECT a.*,b.item_name FROM inv_main_stock_received_detail a,item_master b  WHERE a.recpt_date between '$fdate' and '$tdate' and a.item_id='$itmid[1]' and a.item_id=b.item_id order by slno");
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Bill No</th>
			<th>Date</th>
			<th>Supplier</th>
			<th>Batch</th>
			<th>Qnty</th>
			<th>Free</th>
			<th style="text-align:right">MRP</th>
			<th style="text-align:right">Rate</th>
			
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">GST Amount</th>
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[SuppCode]'"));
			
			$itmamt=0;
			$itmamt=$r['recpt_quantity']*$r['recept_cost_price'];
			
			$vttl=$vttl+$itmamt;
			$vttgst=$vttgst+$r['gst_amount'];
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($r['recpt_date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td><?php echo $r['recept_batch'];?></td>
			<td><?php echo $r['recpt_quantity'];?></td>
			<td><?php echo $r['free_qnt'];?></td>
			<td style="text-align:right"><?php echo $r['recpt_mrp'];?></td>
			<td style="text-align:right"><?php echo $r['recept_cost_price'];?></td>
			<td style="text-align:right"><?php echo number_format($itmamt,2);?></td>
			<td style="text-align:right"><?php echo $r['gst_amount'];?></td>
			<!--<td><button type="button" class="btn btn-info" onclick="report_print_billwise('<?php echo $r[bill_no];?>','<?php echo $r[supp_code];?>')">View</button></td>-->
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		
		<tr>
			<td colspan="10" style="font-weight:bold;text-align:right">Total </td>
			<td style="font-weight:bold;text-align:right"><?php echo number_format($vttl,2);?> </td>
			<td style="font-weight:bold;text-align:right"><?php echo number_format($vttgst,2);?> </td>
		</tr>
		
	</table>
	<?php
}

//////////////////////////////////////////

if($_POST["type"]=="load_purcahse_order_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$spplrid=$_POST['spplrid'];
	$orderno=$_POST['orderno'];
	if($orderno !="")
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE  order_no='$orderno' and del=0  order by `order_no`");
	}
	else
	{
		if($spplrid=='0')
		{
		$q=mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE order_date between'$fdate' and '$tdate' and del=0 order by `order_no`");
		}
		else
		{
			$q=mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE order_date between'$fdate' and '$tdate' and supplier_id='$spplrid' and del=0  order by `order_no`");
		}
	}
	$n=mysqli_num_rows($q);
	?>
		
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Supplier</th>
			<th>Order Date</th>
			<th>Edit</th>
			<th>Delete</th>
			<th>Print</th>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			$splr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supplier_id]'"));
			
			$vttlamt=$vttlamt+$r['net_amt'];
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $splr['name'];?></td>
			<td><?php echo convert_date($r['order_date']);?></td>
			
			<td><button class="btn btn-mini btn-success" onClick="redirect_sale_frm('<?php echo $r[order_no];?>')"><i class="icon-edit"></i></button></td>
			<td><button class="btn btn-mini btn-danger" onClick="delete_data('<?php echo $r["order_no"]; ?>')" ><i class="icon-remove"></i></button></td>
			<td><button type="button" class="btn btn-info" onclick="rcv_rep_prr('<?php echo $r[order_no];?>')"><i class="icon-print"></i></button></td>
		</tr>
		<?php
		$n=0;
		$i++;
		}
		?>
		
	</table>
	<?php
}

///////////////////////////////////////
if($type=="load_sbtr_indntorder")
{
	$sbstorid=$_POST['sbstorid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	if($sbstorid!=0)
	{
		$q=mysqli_query($link,"SELECT a.*,b.substore_name FROM inv_substore_indent_order_master a,inv_sub_store b WHERE order_date between'$fdate' and '$tdate' and a.substore_id=b.substore_id and a.substore_id='$sbstorid' order by order_no");
	}
	else
	{
		$q=mysqli_query($link,"SELECT a.*,b.substore_name FROM inv_substore_indent_order_master a,inv_sub_store b WHERE order_date between'$fdate' and '$tdate' and a.substore_id=b.substore_id order by order_no");
	}
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Order Date</th>
			<th>Sub Store</th>
			<th>Order By</th>
			<!--<th>Export To Excel</th>-->
			<th>View</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			//$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id`='$r[SuppCode]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo convert_date($r['order_date']);?></td>
			<td><?php echo $r['substore_name'];?></td>
			<td><?php echo $quser['name'];?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>-->
			<td><button type="button" class="btn btn-info" onclick="ord_rep_prr('<?php echo $r[order_no];?>')">View</button></td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

///////////////////////////////////////
if($type=="load_sbtr_itmissue")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$sbstrid=$_POST['sbstrid'];
	$itmid1=$_POST['itmid'];
	$itmid=explode("-#",$itmid1);
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Issue No</th>
			<th>Issue Date</th>
			<th>Issue To</th>
					
			<!--<th>Export To Excel</th>-->
			<th>View</th>
		</tr>
		<?php
		if($itmid[1]!="")
		{
			$qdate=mysqli_query($link,"select distinct issue_date from inv_mainstore_issue_details where issue_date between '$fdate' and '$tdate' and item_id='$itmid[1]'  ");
		}
		else
		{
			$qdate=mysqli_query($link,"select distinct issue_date from inv_mainstore_issue_details where issue_date between '$fdate' and '$tdate' and substore_id='$sbstrid'  ");
		}
			
			while($qdate1=mysqli_fetch_array($qdate))
			{
			?>
			 <tr>
				 <td colspan="5"><i>Issue Date : <?php echo  convert_date($qdate1['issue_date']);?></i></td>
			 </tr>
			<?php	
				
		
		$i=1;
		//$q=mysqli_query($link,"SELECT a.*,b.name,b.price FROM inv_mainstore_issue_details a,inv_indent_master b WHERE issue_date='$qdate1[issue_date]' and a.substore_id='$sbstrid' and a.item_code=b.id order by issue_date,issu_no");
		if($itmid[1]!="")
		{
			$q=mysqli_query($link,"SELECT distinct order_no  FROM inv_mainstore_issue_details  WHERE issue_date='$qdate1[issue_date]' and  item_id='$itmid[1]'  order by issue_date,slno");
			
		}
		else
		{
			$q=mysqli_query($link,"SELECT distinct order_no  FROM inv_mainstore_issue_details  WHERE issue_date='$qdate1[issue_date]' and substore_id='$sbstrid'  order by issue_date,slno");
		}
		
		
		while($r=mysqli_fetch_array($q))
		{
			$vttlgst=0;
			$vamtttl=0;
			$vitmttl=0;
			$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM inv_mainstore_issue_details  WHERE issue_date='$qdate1[issue_date]' and order_no='$r[order_no]'  order by issue_date,order_no"));
			$qsubstor=mysqli_fetch_array(mysqli_query($link,"SELECT substore_name FROM inv_sub_store  WHERE substore_id='$qamt[substore_id]' "));
			//$qamt=mysqli_query($link,"SELECT a.*,b.item_name,b.gst FROM inv_mainstore_issue_details a,item_master b WHERE a.issue_date='$qdate1[issue_date]' and a.order_no='$r[order_no]'  and a.item_id=b.id order by issue_date,order_no");
			
			
			
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo convert_date($qamt['issue_date']);?></td>
			<td><?php echo $qamt['issue_to'];?></td>
			
			
			<!--<td><button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>-->
			<td><button type="button" class="btn btn-info" onclick="substr_itm_rep_prr('<?php echo $r[order_no];?>')">View</button></td>
		</tr>
		<?php
		$i++;
		}
		?>
	  <?php
	}?>
	
		
	</table>
	<?php
}



///////////////////////////////////////
if($type=="load_sbtr_itmissue_itemwise")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$sbstrid=$_POST['sbstrid'];
	$itmid1=$_POST['itmid'];
	$itmid=explode("-@",$itmid1);
	
	
	?>
	<button type="button" class="btn btn-success" onclick="issue_summery_print_excel('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-info" onclick="issue_summery_print('<?php echo $ord;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Issue No</th>
			<th>Issue Date</th>
			<th>Time</th>
			<th>Issue To</th>
			<th>Department</th>
			<th>Batch</th>
			<th>Quantity </th>
			<th>User</th>
		</tr>
		
		<tr>
			<th colspan="9" ><i>Item Name : <?php echo  $itmid[0];?></i></th>
		</tr>
		<?php
			$vttl=0;
			$i=1;
			if($sbstrid!=0)
			{
				$qdate=mysqli_query($link,"select distinct a.date from inv_substore_issue_details a,inv_substore_issue_master b where a.date between '$fdate' and '$tdate' and a.item_id='$itmid[1]' and a.issue_no=b.issue_no and  b.substore_id='$sbstrid'  ");
			}
			else
			{
				$qdate=mysqli_query($link,"select distinct date  from `inv_substore_issue_details`  where date between '$fdate' and '$tdate' and item_id='$itmid[1]'  ");
			}
			
				
			while($qdate1=mysqli_fetch_array($qdate))
			{
			?>
			 <tr>
				 <td colspan="9"><i>Issue Date : <?php echo  $qdate1['date'];?></i></td>
			 </tr>
			<?php	
				
		if($sbstrid!=0)
		{
		 $q=mysqli_query($link,"SELECT a.*,b.substore_id,b.issue_to,b.user  FROM inv_substore_issue_details a,inv_substore_issue_master b  WHERE a.date='$qdate1[date]' and  a.item_id='$itmid[1]' and  a.issue_no=b.issue_no and b.substore_id='$sbstrid'  order by a.date");
		}
		else
		{
			$q=mysqli_query($link,"SELECT a.*,b.substore_id,b.issue_to,b.user  FROM inv_substore_issue_details a,inv_substore_issue_master b  WHERE a.date='$qdate1[date]' and  a.item_id='$itmid[1]' and  a.issue_no=b.issue_no  order by a.date");
		}	
		while($r=mysqli_fetch_array($q))
		{
			$qsubstor=mysqli_fetch_array(mysqli_query($link,"SELECT substore_name FROM inv_sub_store  WHERE substore_id='$r[substore_id]' "));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			$vttl+=$r['issue_qnt'];
			
			
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['issue_no'];?></td>
			<td><?php echo $r['date'];?></td>
			<td><?php echo convert_time($r['time']);?></td>
			<td><?php echo $r['issue_to'];?></td>
			<td><?php echo $qsubstor['substore_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo number_format($r['issue_qnt'],0);?></td>
			<td><?php echo $quser['name'];?></td>
			
		</tr>
		<?php
		$i++;
		}
		?>
	  <?php
	}?>
	<tr>
		<th colspan="7" style="text-align:right">Total Issued </th>
		<th><?php echo number_format($vttl,0);?></th>
		<th></th>
	</tr>
		
	</table>
	<?php
}


///////////////////////////////////////
if($type=="load_sbtr_stkaprove")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$ord=$_POST['ord'];
	
	$q=mysqli_query($link,"SELECT a.*,b.substore_name FROM inv_substorestock_aproved_details a,inv_sub_store b WHERE date between'$fdate' and '$tdate' and a.substore_id=b.substore_id order by order_no");
	
	?>
	
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Order Date</th>
			<th>Approved Date</th>
			<th>Sub Store</th>
			<!--<th>Export To Excel</th>-->
			<th>Print</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `order_date` FROM `inv_substore_indent_order_master` WHERE `order_no`='$r[order_no]'"));
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id`='$r[SuppCode]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $dt['order_date'];?></td>
			<td><?php echo $r['date'];?></td>
			<td><?php echo $r['substore_name'];?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>-->
			<td><button type="button" class="btn btn-info" onclick="ord_rep_prr('<?php echo $r[order_no];?>')">Print</button></td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}
///////////////////////////////////////////////////////////	  
elseif($type=="indent_pndng_order") ///For Indent Received form
	{
	 ?>
	 <tr>
			<th>Order No</th>
			<th>Sub Store</th>
			<th>Order Date</th>
			
			
			
		</tr>
		<?php	
		
		
	 $val=$_POST['val'];
	 if($val)
	 {
		 //$q="select * from inv_substore_indent_order_master where name like '$val%'";
	 }
	 else
	 {
	   	 $q="select a.*,b.substore_name from inv_substore_indent_order_master a,inv_sub_store b where a.substore_id=b.substore_id and a.stat=0 order by a.slno desc";
	 }
	 
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['order_no'];?>','<?php echo $qrpdct1['substore_id'];?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['order_no'];?></td>
             <td><?php echo $qrpdct1['substore_name'];?></td>
             <td><?php echo convert_date($qrpdct1['order_date']);?></td>
          
         </tr>	
         <?php	
		   $i++;}
	  }
	  
///////////////////////////////////////////////////////////	  
elseif($type=="sbstrindnrcv") ///For Indent Received form
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 //$q="select * from inv_substore_indent_order_master where name like '$val%'";
	 }
	 else
	 {
		 
	   	 //$q="select a.*,b.substore_name from inv_substore_indent_order_master a,inv_sub_store b,inv_substore_order_aprv_details c where a.substore_id=b.substore_id and a.stat=1 and a.order_no=c.order_no and a.substore_id=c.substore_id and c.stat=0 order by a.order_no";
	   	  $q="select distinct(a.order_no),a.substore_id,a.order_date,b.substore_name from inv_substore_indent_order_master a,inv_sub_store b,inv_substore_order_aprv_details c where a.substore_id=b.substore_id and a.stat=1 and a.order_no=c.order_no and a.substore_id=c.substore_id and c.stat=0 order by a.order_no";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['order_no'];?>','<?php echo $qrpdct1['substore_id'];?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['order_no'];?></td>
             <td><?php echo $qrpdct1['substore_name'];?></td>
             <td><?php echo $qrpdct1['order_date'];?></td>
          
         </tr>	
         <?php	
		   $i++;}
	  }	  
///////////////////////////////////////////////////////////	  
elseif($type=="indentrcv") ///For Indent Received form
	{
	 $val=$_POST['val'];
	 $orderno=$_POST['orderno'];
	
	 if($val)
	 {
		 $q="select a.* from inv_indent_master a,inv_indent_order_details b where a.id=b.item_code and b.order_no='$orderno' and a.name like '$val%'";
	 }
	 else
	 {
	   	 $q="select a.* from inv_indent_master a,inv_indent_order_details b where a.id=b.item_code and b.order_no='$orderno'  order by a.name";
	 }
	
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
          
         </tr>	
         <?php	
		   $i++;}
	  }

///////////////////////////////////////////////////////////	  
elseif($type=="subtore") ///For substore form
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from inv_sub_store where substore_name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from inv_sub_store  order by substore_name";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['substore_id'];?>')" id="rad_test<?php echo $i;?>">
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['substore_id'];?></td>
             <td><?php echo $qrpdct1['substore_name'];?></td>
             <!--<td><a href="javascript:delete_data('<?php echo $qrpdct1['substore_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>-->
         </tr>	
         <?php	
		   $i++;}
	  }

///////////////////////////////////////////////////////////	  
elseif($type=="invindntcatgry") ///For Inv Category form
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from inv_indent_type where name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from inv_indent_type  order by name";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['inv_cate_id'];?>')" id="rad_test<?php echo $i;?>">
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['inv_cate_id'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['inv_cate_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
         </tr>	
         <?php	
		   $i++;}
	  }

///////////////////////////////////////////////////////////	  
elseif($type=="loadsubcatgry") ///For Inv sub Category form
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from inv_subcategory where sub_cat_name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from inv_subcategory  order by sub_cat_name";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['sub_cat_id'];?>')" id="rad_test<?php echo $i;?>">
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['sub_cat_id'];?></td>
             <td><?php echo $qrpdct1['sub_cat_name'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['sub_cat_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
         </tr>	
         <?php	
		   $i++;}
	  }	  

///////////////////////////////////////////////////////////	  
elseif($type=="indmaster") ///For inv Item Master form
	{
	 $val=$_POST['srch'];
	 $cateid=$_POST['cateid'];
	 $subcateid=$_POST['subcateid'];
	 
	 if($val)
	 {
		 $q="select * from inv_indent_master where inv_cate_id='$cateid' and sub_cat_id='$subcateid' and name like '$val%'";
		
	 }
	 else
	 {
	   	 $q="select * from inv_indent_master where inv_cate_id='$cateid' and sub_cat_id='$subcateid'  order by name";
	 }
	 

		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
         </tr>	
         <?php	
		   $i++;}
  }	  	  
///////////////////////////////////////////////////////////	  
elseif($type=="billloadtmp") ///For Indent Received temp
	{
		?>
		 <tr>
			 <td>Slno</td>
			 <td>Name</td>
			 <td>Qnty.</td>
			 <td>Rate.</td>
			 <td>Amount</td>
			 <td>Del</td>
		 </tr>
		<?php
	    $invno=$_POST['invno'];
		$qrpdct=mysqli_query($link,"select a.*,b.name from inv_indent_rcvdetails_temp a,inv_indent_master b where a.item_code=b.id and a.invoiceno='$invno' order by b.name");
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer" >
         
             <td ><?php echo $i;?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             <td><?php echo $qrpdct1['quantity'];?></td>
             <td><?php echo $qrpdct1['rate'];?></td>
             <td><?php echo $qrpdct1['amount'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_code'];?>','<?php echo $qrpdct1['invoiceno'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
         </tr>	
         <?php	
		   $i++;}
	  }
	  	  	  	    	  	 	  	    	  	  	     	      
?>
      
  </table>
