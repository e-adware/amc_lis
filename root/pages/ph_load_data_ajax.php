<?php
session_start();
include'../../includes/connection.php';

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

$type=$_POST['type'];

function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}

?>
 <table class="table table-striped table-bordered">
   
 <?php

if($type=="aa") ///For load purchase ordertemp item
	{
		$orderno=$_POST['orderno'];
		
		$qrpdct=mysqli_query($link,"select a.*,b.name from inv_indent_order_details_temp a,inv_indent_master b  where a.item_code=b.id and a.order_no='$orderno' order by b.name ");
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr>
             <td><?php echo $qrpdct1['order_no'];?></td>
             <td><?php echo $qrpdct1['item_id'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             <td><?php echo $qrpdct1['order_qnt'];?></td>
                   
           <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_id'];?>','<?php echo $qrpdct1['order_no'];?>','<?php echo $qrpdct1['order_no'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		   ;}
	  }	
	  
 
///////////////////////////////////////////////////////////	  
elseif($type=="loadsubcatgry") ///For substore form
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from ph_category_master where ph_cate_name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from ph_category_master  order by ph_cate_name";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ph_cate_id'];?>')" id="rad_test<?php echo $i;?>">
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ph_cate_id'];?></td>
             <td><?php echo $qrpdct1['ph_cate_name'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['ph_cate_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
         </tr>	
         <?php	
		   $i++;}
	  }
/////////////////////////////////////
  
if($_POST["type"]=="item_wise_aval_report")
{
	?>
	<button type="button" class="btn btn-default" onclick="stk_exp()">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="stk_prr()">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>Available Stock</th><th>MRP</th><th>MRP Value</th><th>Cost Value</th>
		</tr>
	<?php
	$i=1;
	$q=mysqli_query($link,"SELECT a.*,b.item_name FROM `ph_stock_master` a,ph_item_master b WHERE a.`quantity`>0 and a.item_code=b.item_code order by b.item_name");
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
		$qrate=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,recept_cost_price FROM `ph_purchase_receipt_details` WHERE `item_code`='$r[item_code]' and  recept_batch='$r[batch_no]'"));
		
		$vmrpvlue=0;
		$vcostvalue=0;
		$vmrpvlue=$qrate['recpt_mrp']*$r['quantity'];
		$vcostvalue=$qrate['recept_cost_price']*$r['quantity'];
		$vttlmrp=$vttlmrp+$vmrpvlue;
		$vttlcstprice=$vttlcstprice+$vcostvalue
	?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo $r['quantity'];?></td>
			<td><?php echo $qrate['recpt_mrp'];?></td>
			<td><?php echo number_format($vmrpvlue,2);?></td>
			<td><?php echo number_format($vcostvalue,2);?></td>
		</tr>
	<?php
	$i++;
	}
	?>
	<tr>
		<th colspan="6" style="text-align:right">Total</th>
		<th><?php echo number_format($vttlmrp,2);?></th>
		<th><?php echo number_format($vttlcstprice,2);?></th>
	</tr>
	</table>
	<?php
}

///////////////////////////////////////////////////////////	  
if($_POST["type"]=="ph_ipd_credit") ///For Indent Received form
	{
		$ph=1;
	 ?>
	 <tr>
			<th>IPD No</th>
			<th>Patient Name</th>
			<th>Admited Date</th>
			<th>Balance Amount</th>
	
		</tr>
		<?php	
		
		
	 $val=$_POST['val'];
	 if($val)
	 {
		// $q="select * from inv_substore_indent_order_master where name like '$val%'";
		 $q="SELECT DISTINCT `ipd_id` FROM `ph_sell_master` WHERE ipd_id like '$val%' and `balance`>0 and substore_id='$ph' and ipd_id !='' order by slno";
	 }
	 else
	 {
	   	 $q="SELECT DISTINCT `ipd_id` FROM `ph_sell_master` WHERE `balance`>0 and substore_id='$ph' and ipd_id !='' order by slno";
	 }
	  
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 $qpname=mysqli_fetch_array(mysqli_query($link,"select customer_name from ph_sell_master where ipd_id='$qrpdct1[ipd_id]' "));
		 $qadmit=mysqli_fetch_array(mysqli_query($link,"select date from uhid_and_opdid where opd_id='$qrpdct1[ipd_id]' "));
		 $qbalance=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(balance),0) as maxbal from ph_sell_master where ipd_id='$qrpdct1[ipd_id]' and balance>0 and substore_id='$ph'  "));		
		 $qpatient_bed=mysqli_fetch_array(mysqli_query($link,"SELECT a.`bed_id`,b.bed_no FROM `ipd_bed_alloc_details` a,bed_master b WHERE a.`bed_id`=b.`bed_id` and a.`ipd_id` ='$qrpdct1[ipd_id]' order by `slno` desc limit 0,1  "));		
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ipd_id'];?>','<?php echo $ph;?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ipd_id'];?></td>
             <td><?php echo $qpname['customer_name'].' / '.$qpatient_bed['bed_no'];?></td>
             <td><?php echo convert_date($qadmit['date']);?></td>
             <td><?php echo $qbalance['maxbal'];?></td>
          
         </tr>	
         <?php	
		   $i++;}
	  }

//////////////////////////////

if($_POST["type"]=="loadstockproduct") // gov
{
	$val=$_POST['val'];
	$patient_id=$_POST['patient_id'];
	$pin=$_POST['pin'];
	$indno=$_POST['indno'];
	$typ=$_POST['typ'];
	
	if($val)
	{
		if($patient_id!='0')
		{
			$q="select a.*,b.quantity from  item_master a,patient_medicine_detail b where a.item_id=b.item_code and b.`patient_id`='$patient_id' AND b.`pin`='$pin' and b.type='$typ' and b.status=0 and indent_num='$indno'  and a.item_name like '$val%' order by a.item_name";
			
		}else
		{
			$q="select * from  item_master where item_id in(select item_code from ph_stock_master where quantity>0 and substore_id='1')  and item_name like '$val%' order by item_name";
		}
	}
	else
	{
		if($patient_id!='0')
		{
			
			$q="select a.*,b.quantity from  item_master a,patient_medicine_detail b where a.item_id=b.item_code and b.`patient_id`='$patient_id' AND b.`pin`='$pin' and b.type='$typ' and b.status=0 and indent_num='$indno' order by a.item_name";
		}else
		{
			$q="select * from  item_master where item_id in(select item_code from ph_stock_master where quantity>0 and substore_id='1' ) order by item_name";
		}
	}
	
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	?>
	<table class="table table-condensed table-bordered">
	<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
	?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_id'];?></td>
		<td><?php echo $qrpdct1['item_name'];?></td>
		<td><?php echo $qrpdct1['quantity'];?></td>
	</tr>	
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}
////////////////////////////////

if($_POST["type"]=="load_user_smry")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="show_user_smry('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th style="text-align:right">Sale (After Return)</th>
			<th style="text-align:right">Received Amount</th>
			<th style="text-align:right">Return Amount</th>
			<th style="text-align:right">Net Amount</th>
		
		</tr>
		
		<?php
		$n=1;
		
		$qbill=mysqli_query($link,"select distinct a.user,b.name from ph_payment_details a,employee b where entry_date between '$fdate' and '$tdate' and a.user=b.emp_id order by b.name ");
		
		while($qbill1=mysqli_fetch_array($qbill))
		{
			$vrtrnamt=0;
			$qsaleamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amt),0) as maxsaleamt,ifnull(sum(paid_amt),0) as maxadvamt from ph_sell_master  where user='$qbill1[user]' and entry_date between '$fdate' and '$tdate'"));
			$qamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxamt from ph_payment_details  where user='$qbill1[user]' and entry_date between '$fdate' and '$tdate'"));
			$qreturn=mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_item_return_master where  user='$qbill1[user]' and return_date between '$fdate' and '$tdate'");
			while($qreturn1=mysqli_fetch_array($qreturn))
			{
				$qrate=mysqli_fetch_array(mysqli_query($link,"select a.recpt_mrp,b.item_name from ph_purchase_receipt_details a,ph_item_master b where a.item_code='$qreturn1[item_code]' and a.recept_batch='$qreturn1[batch_no]' and a.item_code=b.item_code"));
				$vrtrnamt1=$qreturn1['return_qnt']*$qrate['recpt_mrp'];
				$vrtrnamt=$vrtrnamt+$vrtrnamt1;
		    }
		    $vttlsaleamt+=$qsaleamt['maxsaleamt'];
		    $vnetamt=$qamt['maxamt']-$vrtrnamt;
		    $vttlrcpt=$vttlrcpt+$qamt['maxamt'];
		    $vttlrtrn=$vttlrtrn+$vrtrnamt;
		    $vttlnet=$vttlnet+$vnetamt;
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $qbill1['name'];?></td>
			<td style="text-align:right"><?php echo number_format($qsaleamt['maxsaleamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt['maxamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($vrtrnamt,2);?></td>
			<td style="text-align:right"><?php echo number_format($vnetamt,2);?></td>
			
		</tr>
		<?php
		$n++;
		}
		?>
		<tr>
			<th colspan="2" style="text-align:right">Total :</th>
			<th style="text-align:right"><?php echo number_format($vttlsaleamt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlrcpt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlrtrn,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlnet,2)?> </th>
		</tr>
	</table>
	<?php
}
////////////////////////////////

if($_POST["type"]=="loadusersmry")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="show_user_smry('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th style="text-align:right">Sale (After Return)</th>
			<th style="text-align:right">Received Amount</th>
			<th style="text-align:right">Return Amount</th>
			<th style="text-align:right">Net Amount</th>
		
		</tr>
		
		<?php
		$n=1;
		
		$qbill=mysqli_query($link,"select distinct a.user,b.name from ph_payment_details a,employee b where entry_date between '$fdate' and '$tdate' and a.user=b.emp_id order by b.name ");
		
		while($qbill1=mysqli_fetch_array($qbill))
		{
			$vrtrnamt=0;
			$qsaleamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amt),0) as maxsaleamt,ifnull(sum(paid_amt),0) as maxadvamt from ph_sell_master  where user='$qbill1[user]' and entry_date between '$fdate' and '$tdate'"));
			$qamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxamt from ph_payment_details  where user='$qbill1[user]' and entry_date between '$fdate' and '$tdate'"));
			$qreturn=mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_item_return_master where  user='$qbill1[user]' and return_date between '$fdate' and '$tdate'");
			while($qreturn1=mysqli_fetch_array($qreturn))
			{
				$qrate=mysqli_fetch_array(mysqli_query($link,"select a.recpt_mrp,b.item_name from ph_purchase_receipt_details a,ph_item_master b where a.item_code='$qreturn1[item_code]' and a.recept_batch='$qreturn1[batch_no]' and a.item_code=b.item_code"));
				$vrtrnamt1=$qreturn1['return_qnt']*$qrate['recpt_mrp'];
				$vrtrnamt=$vrtrnamt+$vrtrnamt1;
		    }
		    $vttlsaleamt+=$qsaleamt['maxsaleamt'];
		    $vnetamt=$qamt['maxamt']-$vrtrnamt;
		    $vttlrcpt=$vttlrcpt+$qamt['maxamt'];
		    $vttlrtrn=$vttlrtrn+$vrtrnamt;
		    $vttlnet=$vttlnet+$vnetamt;
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $qbill1['name'].' /'.$qbill1[user];?></td>
			<td style="text-align:right"><?php echo number_format($qsaleamt['maxsaleamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($qamt['maxamt'],2);?></td>
			<td style="text-align:right"><?php echo number_format($vrtrnamt,2);?></td>
			<td style="text-align:right"><?php echo number_format($vnetamt,2);?></td>
			
		</tr>
		<?php
		$n++;
		}
		?>
		<tr>
			<th colspan="2" style="text-align:right">Total :</th>
			<th style="text-align:right"><?php echo number_format($vttlsaleamt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlrcpt,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlrtrn,2)?> </th>
			<th style="text-align:right"><?php echo number_format($vttlnet,2)?> </th>
		</tr>
	</table>
	<?php
}



//////////////////////
elseif($type=="load_ph_item_return")///////////from Item Return
{
	$rtrnno=$_POST['rtrnno'];
	
		?>
		 <tr>
			 <td>#</td>
			 <td>Retrun No</td>
			 <td>Item No</td>
			 <td>Item Name</td>
			 <td>Item Batch</td>
			 <td>Expiry </td>
			 <td>MRP</td>
			 <td>Qnty.</td>
			 <td>Amount</td>
			 
			 <td>Remove</td>
		 </tr>
		<?php
		$i=1;
		$qrpdct=mysqli_query($link,"select a.*,b.item_name from ph_item_return_store_detail_temp a,item_master b  where a.item_id=b.item_id and a.returnr_no='$rtrnno'  order by b.item_name ");
		
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		$vitmttl=0;
		$itmamt=$qrpdct1['item_amount']+$qrpdct1['gst_amount'];		
		//$vttl=$vttl+$qrpdct1['item_amount']+$qrpdct1['gst_amount'];	
		$vitmttl=$qrpdct1['recpt_mrp']*$qrpdct1['quantity'];	
		$vttl=$vttl+$vitmttl;	
		 
	     ?>
         <tr>
             <td><?php echo $i;?></td>
             <td><?php echo $rtrnno;?></td>
             <td><?php echo $qrpdct1['item_id'];?></td>
             <td><?php echo $qrpdct1['item_name'];?></td>
             <td><?php echo $qrpdct1['batch_no'];?></td>
             <td><?php echo $qrpdct1['expiry_date'];?></td>
             <td><?php echo $qrpdct1['recpt_mrp'];?></td>
             
             <td><?php echo $qrpdct1['quantity'];?></td>
             <td><?php echo number_format($vitmttl,2);?></td>
             
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_id'];?>','<?php echo $qrpdct1['batch_no'];?>','<?php echo $rtrnno;?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		  $i++ ;}
		?>
		
		<tr>
			<td colspan="8" style="font-weight:bold;font-size:11px;text-align:right">Total </td>
			<td style="font-weight:bold;font-size:11px;text-align:right"><?php echo number_format($vttl,2);?> </td>
			<td>&nbsp;</td>
			
		</tr>
		
		<?php
		   
}
  
///////////////////////////////////////////////////////////	  
elseif($type=="loadphitmdirct") ///For substore form
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from ph_item_master where item_name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from ph_item_master  order by item_name";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_code'];?>')" id="rad_test<?php echo $i;?>">
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_code'];?></td>
             <td><?php echo $qrpdct1['item_name'];?></td>
            
         </tr>	
         <?php	
		   $i++;}
	  }
	  
////////////////////////////////////////////////////////////

elseif($type=="phitmrtrncrdt")/// pharmacy item retrun bill no
{
	$date=$_POST['date'];
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Sl No</th><th>Bill NO</th><th>Credit No</th><th>Customaer Name</th><th>Date</th>
			</tr>
		<?php
		$i=1;
		$qrpdct=mysqli_query($link,"select a.*,b.customer_name from ph_item_return_creditnote a,ph_sell_master b  where a.bill_no=b.bill_no and date='$date' order by bill_no ");
		while($qrpdct1=mysqli_fetch_array($qrpdct))
		{
	     ?>
         <tr>
             <td><?php echo $i;?> </td>
             <td><?php echo $qrpdct1['bill_no'];?></td>
             <td><?php echo $qrpdct1['credit_no'];?></td>
             <td><?php echo $qrpdct1['customer_name'];?></td>
             <td><?php echo $qrpdct1['date'];?></td>
             
         </tr>	
         <?php	
		  $i++;}
		 ?>
		 </table>
		 <?php
}
//////////////////////////////////////////////////

if($_POST["type"]=="purchse_ord_tmp")
{
	$ord=$_POST['orderno'];
	$qry=mysqli_query($link,"SELECT * FROM `ph_purchase_order_details_temp` WHERE `order_no`='$ord'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Item Code</th><th>Item Name</th><th>Order Qnt</th><th>Remove</th>
			</tr>
			<?php
			while($r=mysqli_fetch_array($qry))
			{
				$i=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
				
			?>
			<tr>
				<td><?php echo $r['item_code'];?></td>
				<td><?php echo $i['item_name'];?></td>
				<td><?php echo $r['order_qnt'];?></td>
				<td><a href="javascript:delete_data('<?php echo $ord;?>','<?php echo $r['item_code'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
	}
}
	  
////////////////////////////////////////////////////////////

elseif($type=="load_purchase_rcpt_tmp")
{
	$orderno=$_POST['orderno'];
	$splrid=$_POST['splrid'];
	$billno=$_POST['billno'];
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>#</th><th>Order No</th><th>Item Code</th><th>Item Name</th><th>Expire</th><th>MRP</th><th>Rate</th><th>Dis</th><th>Batch No</th><th>Quantity</th><th>Free</th><th>Total</th><th></th>
			</tr>
		<?php
		$i=1;
		$qrpdct=mysqli_query($link,"select a.*,b.item_name from inv_main_stock_received_detail_temp a,item_master b  where a.item_id=b.item_id and a.order_no='$orderno' and a.bill_no='$billno' and a.SuppCode='$splrid' and a.user='$userid' order by a.slno ");
		
		
		while($qrpdct1=mysqli_fetch_array($qrpdct))
		{
			$vttl=number_format($qrpdct1['item_amount']+$qrpdct1['gst_amount']-$qrpdct1['dis_amt'],2);
			//$dt=mysqli_fetch_array(mysqli_query($link,"SELECT date FROM `ph_stock_process` WHERE `process_no`='$orderno' AND `item_code`='$qrpdct1[item_code]' AND `batch_no`='$qrpdct1[recept_batch]'"));
			$sell_chk=mysqli_fetch_array(mysqli_query($link,"SELECT sum(`sell`) as sell FROM `ph_stock_process` WHERE `item_code`='$qrpdct1[item_code]' AND `batch_no`='$qrpdct1[recept_batch]' AND `date`>='$dt[date]'"));
			if($sell_chk['sell']>0)
			{
				$dis="";
				//$dis="disabled='disabled'";
			}
			else
			{
				$dis="";
			}
	     ?>
         <tr>
			 <td><?php echo $i;?></td>
             <td><?php echo $qrpdct1['order_no'];?></td>
             <td><?php echo $qrpdct1['item_id'];?></td>
             <td><?php echo $qrpdct1['item_name'];?></td>
             <td><?php echo $qrpdct1['expiry_date'];?></td>
             <td><?php echo $qrpdct1['recpt_mrp'];?></td>
             <td><?php echo $qrpdct1['recept_cost_price'];?></td>
             <td><?php echo $qrpdct1['dis_amt'];?></td>
             <td><?php echo $qrpdct1['recept_batch'];?></td>
             <td><?php echo $qrpdct1['recpt_quantity'];?></td>
             <td><?php echo $qrpdct1['free_qnt'];?></td>
             <td><?php echo $vttl;?></td>
             <!--<td>
				<button type="button" class="btn btn-mini" onclick="" <?php echo $dis;?>><b class="icon-remove icon-large"></b></button>
             </td>
             href="javascript:delete_data('<?php //echo $orderno;?>','<?php //echo $qrpdct1['item_code'];?>')" 
             -->
             <td><button type="button" class="btn btn-mini" <?php echo $dis;?> onclick="javascript:if(confirm('Are you sure want to delete it..')){delete_data('<?php echo $orderno;?>','<?php echo $qrpdct1['item_id'];?>','<?php echo $qrpdct1['recept_batch'];?>');return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></button></td>
         </tr>	
         <?php	
		  $i++;}
		 ?>
		 </table>
		 <?php
}
////////////////////////////////////////////////////////////

elseif($type=="load_order_item")
{
	$val=$_POST['val'];
	$ord=$_POST['orderno'];
	if($val)
	 {
		 $q="SELECT a.* FROM `ph_purchase_order_details` a,ph_item_master b WHERE `order_no`='$ord' AND `stat`='0' and a.item_code=b.item_code and b.item_name like '$val%'";
	 }
	 else
	 {
	   	 $q="SELECT * FROM `ph_purchase_order_details` WHERE `order_no`='$ord' AND `stat`='0' ";
	 }
	 
	$i=1;
	$qry=mysqli_query($link,$q);
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Item Code</th><th>Item Name</th><th>Order Qnt</th><th>Balance Qnt</th>
			</tr>
			<?php
			while($r=mysqli_fetch_array($qry))
			{
				$itmnm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
			?>
			<tr style="cursor:pointer"  onclick="val_load_new('<?php echo $r['item_code'];?>')" id="rad_test<?php echo $i;?>">
				<td id="prod<?php echo $i;?>"><?php echo $r['item_code'];?></td>
				
				<td><?php echo $itmnm['item_name'];?></td>
				<td><?php echo $r['order_qnt'];?></td>
				<td><?php echo $r['bl_qnt'];?></td>
			</tr>
			<?php
			$i++;}
			?>
		</table>
		<?php
	}
}
/////////////////////////////////////

///////////////////////////////////////////////////////////	  
elseif($type=="phbalncercv") ///For Indent Received form
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 //$q="select * from inv_substore_indent_order_master where name like '$val%'";
	 }
	 else
	 {
	   	 //$q="select a.*,b.substore_name from inv_substore_indent_order_master a,inv_sub_store b where a.substore_id=b.substore_id and a.status=1 order by a.order_no";
	   	 $q="select distinct  ipd_id,customer_name from ph_sell_master  where balance>0 ";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ipd_id'];?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ipd_id'];?></td>
             <td><?php echo $qrpdct1['customer_name'];?></td>
             <td><?php echo $qrpdct1['entry_date'];?></td>
          
         </tr>	
         <?php	
		   $i++;}
	  }	
///////////////////////////////

if($_POST["type"]=="load_order_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$ord=$_POST['ord'];
	$s=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id` IN (SELECT `SuppCode` FROM `ph_purchase_order_master` WHERE `order_no`='$ord')"));
	$q=mysqli_query($link,"SELECT * FROM `ph_purchase_order_master` WHERE order_date between'$fdate' and '$tdate' order by order_no");
	$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `order_date` FROM `ph_purchase_order_master` WHERE `order_no`='$ord'"));
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Order Date</th>
			<th>Supplier</th>
			<th>Export To Excel</th>
			<th>Print</th>
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			//$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id`='$r[SuppCode]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $r['order_date'];?></td>
			<td><?php echo $itm['name'];?></td>
			<td><button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>
			<td><button type="button" class="btn btn-info" onclick="ord_rep_prr('<?php echo $r[order_no];?>')">Print</button></td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

//////////////////////////////////////////

if($_POST["type"]=="loadsalesbill") ////For load sales Bill
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$billno=$_POST['billno'];
	
	//$s=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id` IN (SELECT `SuppCode` FROM `ph_purchase_order_master` WHERE `order_no`='$ord')"));
	if($billno !="")
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE bill_no='$billno'  ");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE entry_date between'$fdate' and '$tdate' order by slno desc");
	}
	
	$n=mysqli_num_rows($q);
	?>
		
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Bill date</th>
			<th>Customer</th>
			<th>Total Amount</th>
			<th>Discount</th>
			<th>Paid</th>
			<th>Balance</th>
			<th>View</th>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			
			$vipd="";
			if($r['ipd_id']!=="")
			{
				$vipd=" / ".$r['ipd_id'];
			}			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($r['entry_date']);?></td>
			<td><?php echo $r['customer_name'].$vipd;?></td>
			<td><?php echo $r['total_amt'];?></td>
			<td><?php echo $r['discount_amt'];?></td>
			<td><?php echo $r['paid_amt'];?></td>
			<td><?php echo $r['balance'];?></td>
			<td><button type="button" class="btn btn-primary btn-mini" onclick="rcv_rep_prr('<?php echo $r['bill_no'];?>','<?php echo $r['substore_id'];?>')">View</button></td>
		</tr>
		<?php
		$n=0;
		$i++;
		}
		?>
	</table>
	<?php
}

//////////////////////////////////////////

if($_POST["type"]=="ph_load_payment") ////For load sales Bill
{
	
	$billno=$_POST['billno'];
	
	
	$q=mysqli_query($link,"SELECT * FROM `ph_payment_details` WHERE  `bill_no`='$billno'");
	
	?>
		
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Amount</th>
			<th>Received date </th>
			<th>Time </th>
			<th>User </th>
			
			
		</tr>
		<?php
		$qpay=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$billno'"));
		
		?>
		<tr>
			<td colspan="6" style="font-weight:bold;font-size:12px">Name : <?php echo $qpay['customer_name'];?> &nbsp; Total Amount :<?php echo $qpay['total_amt'];?> &nbsp; Paid :<?php echo $qpay['paid_amt'];?> &nbsp; Discount :<?php echo $qpay['discount_amt'];?> &nbsp;Adjustment :<?php echo $qpay['adjust_amt'];?> &nbsp;Balance :<?php echo $qpay['balance'];?> </td>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]' "));
			
			$vipd="";
			if($r['ipd_id']!=="")
			{
				$vipd=" / ".$r['ipd_id'];
			}			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $r['amount'];?></td>
			<td><?php echo convert_date($r['entry_date']);?></td>
			<td><?php echo $r['time'];?></td>
			<td><?php echo $quser['name'];?></td>
			
			
		</tr>
		<?php
		$n=0;
		$i++;
		}
		?>
	</table>
	<?php
}
////////////////////////////////////

if($_POST["type"]=="loadeditbill")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<!--<button type="button" class="btn btn-default" onclick="edit_bill_show('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>-->
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Customer Name</th>
			<th>Previous</th>
			<th>Current Amount</th>
			<th>Last Edit Date</th>
			<th>Edit By</th>
			
		</tr>
		<?php
		$n=1;
		
		$qbill=mysqli_query($link,"select distinct bill_no from ph_sell_master_edit where entry_date between '$fdate' and '$tdate' and 	entry_no>1 ");
		
		while($qbill1=mysqli_fetch_array($qbill))
		{
			$qname=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master_edit  where bill_no='$qbill1[bill_no]'"));
			$qfrstamt=mysqli_fetch_array(mysqli_query($link,"select total_amt from ph_sell_master_edit  where bill_no='$qbill1[bill_no]' order by slno"));
			$qlstamt=mysqli_fetch_array(mysqli_query($link,"select total_amt,entry_date,user from ph_sell_master_edit  where bill_no='$qbill1[bill_no]' order by slno DESC"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$qlstamt[user]'"));
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $qbill1['bill_no'];?></td>
			<td><?php echo $qname['customer_name'];?></td>
			<td><?php echo $qfrstamt['total_amt'];?></td>
			<td><?php echo $qlstamt['total_amt'];?></td>
			<td><?php echo convert_date($qlstamt['entry_date']);?></td>
			<td><?php echo $quser['name'];?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="edit_bill_show('<?php echo $qbill1['bill_no'];?>')">Print</button></td>-->
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}


////////////////////////////////////

if($_POST["type"]=="loadipdcrdt")
{
	$ipdno=$_POST['ipdno'];
	$opdno=$_POST['opdno'];
	$pname=$_POST['pname'];
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="sale_rep_det_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="sale_rep_det_prr('<?php echo $ipdno;?>','<?php echo $pname;?>','<?php echo $opdno;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Bill No</th>
				<th>Customer Name</th>
				<th>Item Details</th>
				<th>MRP</th>
				<th>Quantity</th>
				<th>Return Qnty</th>
				
				<th>Amount(Round)</th>
				<th>Paid</th>
				<th>Balance</th>
				<th>Date</th>
			</tr>
		</thead>
			
			<?php
			$n=1;
			$vttlbal=0;
		if($ipdno!="")	
		{
			$qry=mysqli_query($link,"SELECT distinct bill_no FROM `ph_sell_master` WHERE `ipd_id`='$ipdno'  ");
		}
		elseif($opdno!="")
		{
			$qry=mysqli_query($link,"SELECT distinct bill_no FROM `ph_sell_master` WHERE `opd_id`='$opdno'  ");
		}
		else
		{
			$qry=mysqli_query($link,"SELECT distinct bill_no FROM `ph_sell_master` WHERE `customer_name`='$pname'  ");
		}
		
		
		while($res=mysqli_fetch_array($qry))
		{
			$vbal=0;
			$qreturnamt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as maxrtrn from `ph_item_return_master` where `bill_no`='$res[bill_no]'  "));
			$vrtrnamt1=0;
			$vrtrnamt1+=round($qreturnamt['maxrtrn']);
			$vrtrnamt+=$vrtrnamt1;
		
			$cus=mysqli_fetch_array(mysqli_query($link,"SELECT `customer_name`,`total_amt`,paid_amt,balance FROM `ph_sell_master` WHERE `bill_no`='$res[bill_no]'"));
			if($cus['balance']>0)
			{
				$vbal=$cus['balance'];
				$vttlbal=$vttlbal+$cus['balance'];
			}
			
			$q=mysqli_query($link,"SELECT * FROM `ph_sell_details` WHERE `bill_no`='$res[bill_no]'");
			$num=mysqli_num_rows($q);
			while($r=mysqli_fetch_array($q))
			{
				$vcsrprice=$r['sale_qnt']*$r['item_cost_price'];
				$qreturn=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(return_qnt),0) as maxrtrnqnt from `ph_item_return_master` where `bill_no`='$res[bill_no]' and `item_code`='$r[item_code]' and `batch_no`='$r[batch_no]' "));
				
				$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
				
		?>
		<tr>
			<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['bill_no']."</td><td rowspan='".$num."'>".$cus['customer_name']."</td>";}?>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['mrp'];?></td>
			<td><?php echo $r['sale_qnt'];?></td>
			<td><?php echo $qreturn['maxrtrnqnt'];?></td>
					
			<td><?php echo round($r['net_amount']);?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<?php if($num>0){echo "<td rowspan='".$num."'>".convert_date($r['entry_date'])."</td>";}?>
			
			
		</tr>
		
		<?php
			$num=0;
			}
			$n++;
		?>
		<tr>
			<?php
			if($cus['paid_amt']>0)
			{
			$vbillpaid=$cus['paid_amt']-$vrtrnamt1;
			}
			else
			{
			$vbillpaid=$cus['paid_amt'];
			}
			if($qreturnamt['maxrtrn']>0)
			{
			echo "<td colspan='5' style='text-align:right;font-weight:bold;font-size:12px'>Return Amount :$vrtrnamt1</td>";
			}
			else
			{
			?>
			<td colspan="5"></td>
			<?php
			}?>
			<td colspan="2" style="font-weight:bold">Total</td>
			<td style="font-weight:bold"><?php echo $cus['total_amt'];?></td>
			<td style="font-weight:bold"><?php echo number_format($vbillpaid,2);?></td>
			<td style="font-weight:bold"><?php echo number_format($vbal,2);?></td>
			<td style="font-weight:bold"></td>
		</tr>
		<tr>
			<td colspan="" style="background:#ccc;"></td>
		</tr>
		
		<?php
		 $grndttl=$grndttl+$cus['total_amt'];
		 $paidttl=$paidttl+$vbillpaid;
		 
	  }?>
	
	<tr>
		    <td colspan="5"></td>
			<td colspan="2" style="font-weight:bold;font-size:12px">Total Return</td>
			<td style="font-weight:bold">&nbsp;</td>
			<td style="font-weight:bold">&nbsp;</td>
			<td colspan="2" style="font-weight:bold;font-size:12px"><?php echo number_format($vrtrnamt,2);?></td>
			
			
	</tr>		
	<tr>
		    <td colspan="5"></td>
			<td colspan="2" style="font-weight:bold">Grand Total</td>
			<td style="font-weight:bold"><?php echo number_format($grndttl,2);?></td>
			<td style="font-weight:bold"><?php echo number_format($paidttl,2);?></td>
			<td style="font-weight:bold"><?php echo number_format($vttlbal,2);?></td>
			<td style="font-weight:bold">&nbsp;</td>
	</tr>	
	</table>
	<?php
}

///////////////////////////////////
if($_POST["type"]=="load_dis_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<button type="button" class="btn btn-default" onclick="ret_dis_exl_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ret_dis_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		
		<tr>
		<th>#</th>
			<th>Bill No</th>
			<th>Customer Name</th>
			<th>Amount</th>
			<th>Discount</th>
			<th>Adjust. </th>
			<th>Paid</th>
			<th>Balance</th>
			<th>Date</th>
	   </tr>		
	<?php
	$i=1;
	$qbilno=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' and discount_amt>0 ");
	while($qbilno1=mysqli_fetch_array($qbilno))
	{
	
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $qbilno1['bill_no'];?></td>
			<td><?php echo $qbilno1['customer_name'];?></td>
			<td><?php echo $qbilno1['total_amt'];?></td>
			<td><?php echo $qbilno1['discount_amt'];?></td>
			<td><?php echo $qbilno1['adjust_amt'];?></td>
			<td><?php echo $qbilno1['paid_amt'];?></td>
			<td><?php echo $qbilno1['balance'];?></td>
			<td><?php echo convert_date($qbilno1['entry_date']);?></td>
		</tr>
		<?php
	$i++;}?>
  
    <tr>
	  <td colspan="7" style="font-weight:bold;font-size:13px;text-align:right">Total</td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlcstmrrtrn,2);?></td>
	  <td>&nbsp;</td>
	  
  </tr> 
	</table>
	<?php
}

///////////////////////////////////
if($_POST["type"]=="load_return_to_store_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<!--<button type="button" class="btn btn-default" onclick="ret_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="ret_rep_prr_to_store('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Date</th>
			<th>Return.qnty</th>
			<th>MRP</th>
			<th style='text-align:right'>Amount</th>
			<th>Reason</th>
			<th>User</th>
		</tr>
	<?php
	$qbilno=mysqli_query($link,"select distinct returnr_no  FROM ph_item_return_store_detail WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	while($qbilno1=mysqli_fetch_array($qbilno))
	{
	?>
	  <tr>
			<td colspan="10" style="font-weight:bold">Return No : <?php echo $qbilno1['returnr_no'];?></td>
			
		</tr>
	<?php	
	$n=1;
	$qry=mysqli_query($link,"SELECT  * FROM `ph_item_return_store_detail` WHERE  returnr_no='$qbilno1[returnr_no]' and date BETWEEN '$fdate' AND '$tdate'");
	$sum=0;
	//$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `discount_perchant`,`customer_name` FROM `ph_sell_master` WHERE `bill_no`='$qbilno1[bill_no]'"));
	while($res=mysqli_fetch_array($qry))
	{
		
		    $quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
			$q=mysqli_query($link,"SELECT * FROM `ph_item_return_store_detail` WHERE returnr_no='$qbilno1[returnr_no]' and `item_id`='$res[item_id]' AND `date` BETWEEN '$fdate' AND '$tdate'");
			$num=mysqli_num_rows($q);
			
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$res[item_id]'"));
			
			
			$vmrp1=0;
			$vmrp1=$res['quantity']*$res['recpt_mrp'];
			
			$sum+=$vmrp1;
		?>
		<tr>

<!--
			<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$res['item_id']."</td><td rowspan='".$num."'>".$itm['item_name']."</td><td rowspan='".$num1."'>".$res['batch_no']."</td>";}?>
-->


			<td><?php echo $n;?></td>
			<td><?php echo $res['item_id'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $res['batch_no'];?></td>
			<td><?php echo convert_date($res['date']);?></td>
			<td><?php echo $res['quantity'];?></td>
			<td><?php echo $res['recpt_mrp'];?></td>
			<td style='text-align:right'><?php echo number_format($vmrp1,2);?></td>
			<td><?php echo $res['reason'];?></td>
			<td><?php echo $quser['name'];?></td>
		</tr>
		<?php
		$num=0;
		$n++;
		}
	
	
	$disc=0;
	if($pat['discount_perchant']>0)
	{
		$disc=($sum*$pat['discount_perchant'])/100;
		$sum=$sum-$disc;
	}
	$sum=floor($sum);
	$vttlcstmrrtrn+=$sum;
	?>
	<tr>
		<td colspan="3" style="font-weight:bold;font-size:13px;text-align:right"></td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo $pat['discount_perchant'];?></td>
		<td colspan="3" style="font-weight:bold;font-size:13px;text-align:right">Return Amount</td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($sum,2);?></td>
		<td></td>
		<td></td>
	</tr>
	<?php
  }?>
  
    <tr>
	  <td colspan="7" style="font-weight:bold;font-size:13px;text-align:right">Total Return</td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlcstmrrtrn,2);?></td>
	  <td>&nbsp;</td>
	   <td>&nbsp;</td>
	  
  </tr> 
	</table>
	<?php
}

///////////////////////////////////
if($_POST["type"]=="load_return_item_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<button type="button" class="btn btn-default" onclick="ret_rep_exp('<?php echo $fdate;?>','<?php echo $tdate;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ret_rep_prr('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Date</th>
			<th>Return.Quantity</th>
			<th>MRP</th>
			<th style='text-align:right'>Amount</th>
			<th>User</th>
		</tr>
	<?php
	$qbilno=mysqli_query($link,"select distinct bill_no  FROM ph_item_return_master WHERE `return_date` BETWEEN '$fdate' AND '$tdate'");
	while($qbilno1=mysqli_fetch_array($qbilno))
	{
	?>
	  <tr>
			<td colspan="9" style="font-weight:bold">Bill No : <?php echo $qbilno1['bill_no'];?></td>
			
		</tr>
	<?php	
	$n=1;
	$qry=mysqli_query($link,"SELECT  distinct `item_code` FROM `ph_item_return_master` WHERE  bill_no='$qbilno1[bill_no]' and return_date BETWEEN '$fdate' AND '$tdate'");
	$sum=0;
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `discount_perchant`,`customer_name` FROM `ph_sell_master` WHERE `bill_no`='$qbilno1[bill_no]'"));
	while($res=mysqli_fetch_array($qry))
	{
		$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
		$q=mysqli_query($link,"SELECT * FROM `ph_item_return_master` WHERE bill_no='$qbilno1[bill_no]' and `item_code`='$res[item_code]' AND `return_date` BETWEEN '$fdate' AND '$tdate'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			
			$qmrm=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where item_code='$r[item_code]' and recept_batch='$r[batch_no]'"));
			if($qmrm)
			{
				//$qmrm['recpt_mrp'];
			}
			else
			{
				$qmrm=mysqli_fetch_array(mysqli_query($link,"select mrp from ph_sell_details where bill_no='$qbilno1[bill_no]' and item_code='$r[item_code]' and batch_no='$r[batch_no]'"));
				$qmrm['recpt_mrp']=$qmrm['mrp'];
			}
			$vmrp1=0;
			$vmrp1=$r['return_qnt']*$qmrm['recpt_mrp'];
			//$vttlcstmrrtrn=$vttlcstmrrtrn+$vmrp1;
			$sum+=$vmrp1;
		?>
		<tr>
			<?php if($num>0){echo "<td rowspan='".$num."'>".$n."</td><td rowspan='".$num."'>".$r['item_code']."</td><td rowspan='".$num."'>".$itm['item_name']."</td><td rowspan='".$num."'>".$r['batch_no']."</td>";}?>
			<td><?php echo convert_date($r['return_date']);?></td>
			<td><?php echo $r['return_qnt'];?></td>
			<td><?php echo $qmrm['recpt_mrp'];?></td>
			<td style='text-align:right'><?php echo number_format($vmrp1,2);?></td>
			<td><?php echo $quser['name'];?></td>
		</tr>
		<?php
		$num=0;
		}
	$n++;
	}
	$disc=0;
	if($pat['discount_perchant']>0)
	{
		$disc=($sum*$pat['discount_perchant'])/100;
		$sum=$sum-$disc;
	}
	$sum=floor($sum);
	$vttlcstmrrtrn+=$sum;
	?>
	<tr>
		<td colspan="3" style="font-weight:bold;font-size:13px;text-align:right">Discount %</td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo $pat['discount_perchant'];?></td>
		<td colspan="3" style="font-weight:bold;font-size:13px;text-align:right">Return Amount</td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($sum,2);?></td>
		<td></td>
	</tr>
	<?php
  }?>
  
    <tr>
	  <td colspan="7" style="font-weight:bold;font-size:13px;text-align:right">Total Return</td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlcstmrrtrn,2);?></td>
	  <td>&nbsp;</td>
	  
  </tr> 
	</table>
	<?php
}
////////////////////////////////////
if($type=="load_return_item") // For Item Return
{
	 $val=$_POST['val'];
	 $billno=$_POST['billno'];
	 
	 if($val)
	 {
		 $q="select b.`item_code`,a.`item_name`,b.sale_qnt,b.batch_no,b.mrp from item_master a,ph_sell_details b where a.item_id=b.item_code and b.bill_no='$billno' and a.item_name like '$val%'";
	 }
	 else
	 {
	   	 $q="select b.`item_code`,a.`item_name`,b.sale_qnt,b.batch_no,b.mrp from item_master a,ph_sell_details b where a.item_id=b.item_code and b.bill_no='$billno'  order by a.item_name";
	 }
	 //echo $q;
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Id</th>
			<th>Name</th>
			<th>MRP</th>
			<th>Qnt</th>
		</tr>
	<?php
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
	$ret=mysqli_fetch_array(mysqli_query($link,"SELECT sum(`return_qnt`) as ret_qnt FROM `ph_item_return_master` WHERE `bill_no`='$billno' AND `item_code`='$qrpdct1[item_code]' AND `batch_no`='$qrpdct1[batch_no]'"));
	$rem=$qrpdct1['sale_qnt']-$ret['ret_qnt'];
	if($rem>0)
	{
	?>
	<tr style="cursor:pointer"  onclick="val_load_new('<?php echo $qrpdct1['item_code'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_code'];?></td>
		<td><?php echo $qrpdct1['item_name'];?></td>
		<td><?php echo $qrpdct1['mrp'];?></td>
		<td><?php echo $rem;?></td>
	</tr>
	<?php	
	$i++;
	}
	}
	?>
	</table>
	<?php
}

////////////////////////////////////
if($type=="supplr_item_rtrn") // For Item Return To supplier
{
	 $val=$_POST['val'];
	
	 
	 if($val)
	 {
		 $q="select item_code,item_name,item_mrp from ph_item_master  where item_name like '$val%'";
	 }
	 else
	 {
	   	 $q="select item_code,item_name,item_mrp from ph_item_master  order by item_name";
	 }
	 
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Id</th>
			<th>Name</th>
			<th>MRP</th>
			
		</tr>
	<?php
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
	?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_code'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_code'];?></td>
		<td><?php echo $qrpdct1['item_name'];?></td>
		<td><?php echo $qrpdct1['item_mrp'];?></td>
		
	</tr>
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}
//////////////////////////////////////////

if($_POST["type"]=="loadsalesbillupdate") ////For load sales Bill
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	$s=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id` IN (SELECT `SuppCode` FROM `ph_purchase_order_master` WHERE `order_no`='$ord')"));
	$q=mysqli_query($link,"SELECT * FROM `ph_sell_master` WHERE entry_date between'$fdate' and '$tdate' order by `bill_no`");
	$n=mysqli_num_rows($q);
	?>
		
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Bill date</th>
			<th>Customer</th>
			<th>Total Amount</th>
			<th>Discount</th>
			<th>Paid</th>
			<th>Balance</th>
			<th>Print</th>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
						
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $r['entry_date'];?></td>
			<td><?php echo $r['customer_name'];?></td>
			<td><?php echo $r['total_amt'];?></td>
			<td><?php echo $r['discount_amt'];?></td>
			<td><?php echo $r['paid_amt'];?></td>
			<td><?php echo $r['balance'];?></td>
			<td><button type="button" class="btn btn-info" onclick="redirect_sale_frm('<?php echo $r[bill_no];?>')">Update</button></td>
		</tr>
		<?php
		$n=0;
		$i++;
		}
		?>
	</table>
	<?php
}
//////////////////////////////////////////

if($_POST["type"]=="load_receive_report")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$spplrid=$_POST['spplrid'];
	if($spplrid=='0')
	{
	$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE bill_date between'$fdate' and '$tdate' order by `order_no`");
    }
    else
    {
		$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE bill_date between'$fdate' and '$tdate' and supp_code='$spplrid'  order by `order_no`");
	}
	
	$n=mysqli_num_rows($q);
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Supplier</th>
			<th>Bill Date</th>
			<th>Receipt Date</th>
			<th>Bill No</th>
			<th style="text-align:right">Amount</th>
			<!--<th>Export</th>-->
			<th>User</th>
			<th>Print</th>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			$splr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supp_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			$vttlamt=$vttlamt+$r['net_amt'];
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $splr['name'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo convert_date($r['recpt_date']);?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td style="text-align:right" ><?php echo $r['net_amt'];?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="rcv_rep_exp('<?php echo $r['order_no'];?>')">Export to excel</button></td>-->
			<td><?php echo $quser['name'];?></td>
			<td><button type="button" class="btn btn-primary btn-mini" onclick="rcv_rep_prr('<?php echo base64_encode($r['order_no']);?>')">Print</button></td>
		</tr>
		<?php
		$n=0;
		$i++;
		}
		?>
		<tr>
			<td colspan="6" style="text-align:right;font-weight:bold"> Total </td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttlamt,2);?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<?php
}
//////////////////////////////////////////

if($_POST["type"]=="item_rtrn_to_splr")
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$splr=$_POST['splr'];
	$q=mysqli_query($link,"SELECT distinct return_date FROM `ph_item_return_supplier` WHERE supplier_id='$splr' and return_date between'$fdate' and '$tdate' order by `return_date`");
	$n=mysqli_num_rows($q);
	?>
		
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Supplier</th>
			<th>Return Date</th>
			<th>Export</th>
			<th>Print</th>
		</tr>
		
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			$splr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ph_supplier_master` WHERE `id`='$r[supp_code]'"));
					
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $splr['name'];?></td>
			<td><?php echo $r['recpt_date'];?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo $r['bill_amount'];?></td>
			<td><button type="button" class="btn btn-default" onclick="rcv_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>
			<td><button type="button" class="btn btn-info" onclick="rcv_rep_prr('<?php echo $r[order_no];?>')">Print</button></td>
		</tr>
		<?php
		$n=0;
		$i++;
		}
		?>
	</table>
	<?php
}
/////////////////////////////
if($_POST["type"]=="load_gst_sale_report")
{
	$gst=$_POST['gst'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	if($fdate=="" && $tdate=="")
	{
	 $q="SELECT * FROM `ph_sell_details` WHERE `gst_percent`='$gst'";
	}
	else
	{
	 $q="SELECT distinct item_code FROM `ph_sell_details` WHERE `gst_percent`='$gst' AND `entry_date` BETWEEN '$fdate' AND '$tdate'";
	}
	?>
	<!--<button type="button" class="btn btn-default" onclick="report_xls('<?php echo $gst; ?>','<?php echo $fdate; ?>','<?php echo $tdate; ?>')"><b class="icon-save"></b> Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="report_print('<?php echo $gst; ?>','<?php echo $fdate; ?>','<?php echo $tdate; ?>')"><b class="icon-print"></b> Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Sl No</th>
			<th>Item Details</th>
			<th>MRP</th>
			<th>Quantity</th>
			<th style="text-align:right">Total Amount</th>
			<th style="text-align:right">GST Amount</th>
			
		</tr>
	<?php
	$qry=mysqli_query($link,$q);
	$i=1;
	while($r=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT a.mrp,a.item_cost_price,b.`item_name` FROM ph_sell_details a,`item_master` b WHERE a.item_code=b.item_id and a.`item_code`='$r[item_code]' and a.`gst_percent`='$gst' AND a.`entry_date` BETWEEN '$fdate' AND '$tdate'"));
		$qslqnt=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amount),0) as maxsaleamt,ifnull(sum(item_cost_price),0) as maxcstprice,ifnull(sum(sale_qnt),0) as maxsale,ifnull(sum(gst_amount),0) as maxgstamt from ph_sell_details where `item_code`='$r[item_code]' and `gst_percent`='$gst' AND `entry_date` BETWEEN '$fdate' AND '$tdate'"));
		
		$vttl=$vttl+$qslqnt['maxsaleamt'];
		$vttlcstprice=$vttlcstprice+$qslqnt['maxcstprice'];
		$vttlgstamt=$vttlgstamt+$qslqnt['maxgstamt'];
		
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $itm['item_name']; ?></td>
		<td><?php echo $itm['mrp']; ?></td>
		<td><?php echo $qslqnt['maxsale']; ?></td>
		<td style="text-align:right"><?php echo number_format($qslqnt['maxsaleamt'],2); ?></td>
		<td style="text-align:right"><?php echo $qslqnt['maxgstamt']; ?></td>
	 </tr>
	 <?php	
	   $i++;
	}
	?>
	<tr>
		<td colspan="4" style="font-weight:bold;font-size:13px;text-align:right">Total</td>
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttl,2);?></td>
		
		<td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlgstamt,2);?></td>
		
	</tr>
	 </table>
	<?php
}
////////////////////////////////////////


/////////////////////////////////
if($_POST["type"]=="showselectedsale_product")
{
	$bill=$_POST['billno'];
	$user=$_POST['user'];
	$q="select *from ph_sell_details_temp where bill_no='$bill' and user='$userid'";
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Item Id</th>
			<th>Name</th>
			<th>Qnt.</th>
			<th>MRP</th>
			<th>Total</th>
			<th>GST</th>
			<th></th>
		</tr>
	<?php
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name`,`mrp`,gst FROM `item_master` WHERE `item_id`='$qrpdct1[item_code]'"));
		$vttl=$qrpdct1['mrp']*$qrpdct1['sale_qnt'];
		$vcstttl=$qrpdct1['net_amount'];
		$vgstamt1=$vcstttl-($vcstttl*(100/(100+$itm['gst'])));
		$vgstamt=number_format($vgstamt1,2);
		
		$qchqnty=mysqli_fetch_array(mysqli_query($link,"select quantity  from ph_stock_master  where  item_code ='$qrpdct1[item_code]' and batch_no  ='$qrpdct1[batch_no]' and substore_id ='1'"));
		$tr_style='';
		if($qrpdct1['sale_qnt']>$qchqnty['quantity'])
		{
			$tr_style='style="cursor:pointer;background-color: red;"';
		}
		
	?>
	<!--<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_code'];?>')" id="rad_test<?php echo $i;?>">-->
	<tr <?php echo $tr_style;?>>
		<td><?php echo $i;?></td>
		<td><?php echo $qrpdct1['item_code'];?></td>
		<td><?php echo $itm['item_name'];?></td>
		<td><?php echo $qrpdct1['sale_qnt'];?></td>
		<td><?php echo $qrpdct1['mrp'];?></td>
		<td><?php echo $vttl;?></td>
		<td><?php echo $vgstamt;?></td>
		<td><button type="button" class="btn btn-mini btn-danger" onclick="delete_data('<?php echo $qrpdct1[item_code];?>','<?php echo $qrpdct1[batch_no];?>')"><span class="icon icon-remove"></span></button></td>
	</tr>
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}


	      	  	  	    	  	 	  	    	  	  	     	      
?>
      
  </table>
