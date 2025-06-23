<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		
<style>
	input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	/*font-size:15px; font-family: "Courier New", Courier, monospace; line-height: 18px;*/
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
.line td{border-top:1px dotted;}
</style>		
<?php
include('../../includes/connection.php');
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];

$date=date('Y-m-d');
$time=date('h:i:s A');

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-y', $timestamp);
		return $new_date;
	}
}


function convert_date1($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('Y-m-d', $timestamp);
		return $new_date;
	}
}



/*

$qmrp=mysqli_query($link,"select * from ph_purchase_receipt_details where recpt_mrp=0");
while($qmrp1=mysqli_fetch_array($qmrp))
{
	mysqli_query($link,"update ph_purchase_receipt_details set recpt_mrp='$qmrp1[recept_cost_price]' where item_code='$qmrp1[item_code]' and recept_batch='$qmrp1[recept_batch]' ");
}


$qmrp=mysqli_query($link,"select * from inv_main_stock_received_detail where recpt_mrp=0");
while($qmrp1=mysqli_fetch_array($qmrp))
{
	mysqli_query($link,"update inv_main_stock_received_detail set recpt_mrp='$qmrp1[recept_cost_price]' where item_id='$qmrp1[item_id]' and recept_batch='$qmrp1[recept_batch]' ");
}

$q1=mysqli_query($link,"select * from ph_purchase_receipt_details where sale_price=0");
while($q2=mysqli_fetch_array($q1))
{
	
	$vunitprice=$q2['recpt_mrp'];
	$gst=$q2['gst_per'];
	$vslprice1=$vunitprice-($vunitprice*(100/(100+$gst)));///Remove gst Calculattion
	$vslprice2=round($vslprice1,2);
	$vslprice3=$vunitprice-$vslprice2;
	$vslprice=$vslprice3;
	
	mysqli_query($link,"update ph_purchase_receipt_details set sale_price='$vslprice' where item_code='$q2[item_code]' and recept_batch='$q2[recept_batch]' ");
}


$q1=mysqli_query($link,"select * from inv_main_stock_received_detail where sale_price=0");
while($q2=mysqli_fetch_array($q1))
{
	
	$vunitprice=$q2['recpt_mrp'];
	$gst=$q2['gst_per'];
	$vslprice1=$vunitprice-($vunitprice*(100/(100+$gst)));///Remove gst Calculattion
	$vslprice2=round($vslprice1,2);
	$vslprice3=$vunitprice-$vslprice2;
	$vslprice=$vslprice3;
	
	mysqli_query($link,"update inv_main_stock_received_detail set sale_price='$vslprice' where item_id='$q2[item_id]' and recept_batch='$q2[recept_batch]' ");
}
echo "done";
*/
/*
///for main Store////
$qitm=mysqli_query($link,"select item_id,item_name from item_master order by item_name ");
while($qitm1=mysqli_fetch_array($qitm))
{
	mysqli_query($link,"update item_stk_cntrl set item_id='$qitm1[item_id]' where item_name='$qitm1[item_name]'");
}
 echo "Done";
 
 
 

$q=mysqli_query($link,"SELECT a.*,b.item_id,b.gst FROM `item_stk_cntrl` a,item_master b WHERE a.qnty>0 and a.`item_id`=b.item_id and a.`item_id` not in(select `item_id` from inv_main_stock_received_detail)");
while($q1=mysqli_fetch_array($q))
{
	$gst=$q1[gst];
	if($gst==0)	
	{
		$gst=12;
	}
	
	$gstamt=0;	
	$vitmamt=0;
	$vitmamt=$q1['qnty']*$q1['cost_price'];
	$vorderno="101/18";
	$vexpridate=convert_date1($q1['exp_date']);
	
	$gstamt=$vitmamt*$gst/100;
	
	$vunitprice=$q1['mrp'];
	$vslprice1=$vunitprice-($vunitprice*(100/(100+$gst)));///Remove gst Calculattion
	$vslprice2=round($vslprice1,2);
	$vslprice3=$vunitprice-$vslprice2;
	$vslprice=$vslprice3;
	
	mysqli_query($link,"delete from inv_main_stock_received_detail where item_id='$q1[item_id]' and recept_batch='$q1[batch]'");
	
	mysqli_query($link,"insert into inv_main_stock_received_detail(`order_no`,`bill_no`,`item_id`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$vorderno','1','$q1[item_id]','0000-00-00','$vexpridate','$date','$q1[qnty]',0,'$q1[batch]','109','$q1[mrp]','$q1[cost_price]','$vslprice','$vitmamt',0,0,'$gst','$gstamt')");
	
	mysqli_query($link,"update item_master set mrp='$q1[mrp]',gst='$gst' where item_id='$q1[item_id]'");
	
	mysqli_query($link,"delete from inv_mainstock_details where item_id='$q1[item_id]' and batch_no='$q1[batch]' ");
	mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$q1[item_id]','$q1[batch]','$date','$q1[qnty]',0,0,'$q1[qnty]') ");
	
	
	mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$q1[item_id]' and batch_no='$q1[batch]' ");
	mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$q1[item_id]','$q1[batch]','$q1[qnty]','$vexpridate')");
	
	
}
echo "Done";
 // mysqli_query($link,"delete from inv_main_stock_receied_master where receipt_no='$vorderno'");
  //mysqli_query($link,"insert into inv_main_stock_receied_master(`receipt_no`,`supplier_code`,`supplier_bill_no`,`total_amount`,`paid`,`discount`,`balance`,`user`,`date`,`time`) values('$vorderno','109','1',0,0,0,0,'101','$date','$time') ");
  
  
 //////End////
 * /
 
 ///for Pharmacy///
 /*

$q=mysqli_query($link,"SELECT a.*,b.item_id FROM `item_temp_ph` a,item_master b WHERE a.`name`=b.item_name");
while($q1=mysqli_fetch_array($q))
{
	$gst=6;	
	$gstamt=0;
	$vitmamt=0;
	$vitmamt=$q1['qnty']*$q1['cost_price'];
	$vorderno="101/18";
	$vexpridate=convert_date1($q1['expiry']);
	
	$gstamt=$vitmamt*$gst/100;
	
	$vunitprice=$q1['mrp'];
	$vslprice1=$vunitprice-($vunitprice*(100/(100+$gst)));///Remove gst Calculattion
	$vslprice2=round($vslprice1,2);
	$vslprice3=$vunitprice-$vslprice2;
	$vslprice=$vslprice3;
	
	mysqli_query($link,"delete from ph_purchase_receipt_details where item_code='$q1[item_id]' and recept_batch='$q1[batch]'");
	
	mysqli_query($link,"insert into ph_purchase_receipt_details(`order_no`,`bill_no`,`item_code`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`fid`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('$vorderno','1','$q1[item_id]','0000-00-00','$vexpridate','$date','$q1[qnty]',0,'$q1[batch]','109','$q1[mrp]','$q1[cost_price]','$vslprice',0,'$vitmamt',0,0,'$gst','$gstamt')");
	
	
	mysqli_query($link,"delete from ph_stock_process where item_code='$q1[item_id]' and batch_no='$q1[batch]' ");
	mysqli_query($link,"insert into ph_stock_process(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('1','Stock','$q1[item_id]','$q1[batch]','$q1[qnty]',0,0,0,0,'$q1[qnty]','$date') ");
	
	
	mysqli_query($link,"delete from ph_stock_master where item_code='$q1[item_id]' and batch_no='$q1[batch]' ");
	mysqli_query($link,"insert into ph_stock_master(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values('1','$q1[item_id]','$q1[batch]','$q1[qnty]','0000-00-00','$vexpridate')");
	
	
}
 
 
  mysqli_query($link,"delete from ph_purchase_receipt_master where order_no='$vorderno'");
  mysqli_query($link,"insert into ph_purchase_receipt_master(`order_no`,`bill_date`,`recpt_date`,`bill_amount`,`gst_amt`,`dis_amt`,`net_amt`,`supp_code`,`bill_no`,`fid`,`user`,`time`,`adjust_type`,`adjust_amt`) values('$vorderno','$date','$date',0,0,0,0,'109','1',0,'101','$time',0,0) ");
   
 
 
 ////end///////
 
  
  
echo "done";


*/


?>

<div class="container-fluid">
			<div class="" style="">
				<?php include('page_header_ph.php'); ?>
				<center><h5><u>Sale Return Report</u></h5></center>
				
			</div>


<table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
      </tr>
      </table>
<table>
<table>

<tr><td colspan="5" style="font-weight:bold;font-size:13px">From : <?php echo convert_date($fdate);?></td></tr>
<tr><td colspan="5" style="font-weight:bold;font-size:13px">To &nbsp;&nbsp;&nbsp;&nbsp; : <?php echo convert_date($tdate);?></td></tr>
</table>	
	
<table class="table table-condensed table-bordered">
	
	<tr bgcolor="#EAEAEA" class="bline" >
		<td style="font-weight:bold;font-size:13px">Sl No</td>
		<td style="font-weight:bold;font-size:13px">Item Code</td>
		<td style="font-weight:bold;font-size:13px">Item Name</td>
		<td style="font-weight:bold;font-size:13px">Batch No</td>
		<td style="font-weight:bold;font-size:13px">Date</td>
		<td style="font-weight:bold;font-size:13px">Return Qnty</td>
		<td style="font-weight:bold;font-size:13px;text-align:right">MRP</td>
		<td style="font-weight:bold;font-size:13px;text-align:right">Amount</td>
		<td style="font-weight:bold;font-size:13px">User</td>
	</tr>
	<?php
	$qbilno=mysqli_query($link,"select distinct bill_no  FROM ph_item_return_master WHERE `return_date` BETWEEN '$fdate' AND '$tdate'");
	while($qbilno1=mysqli_fetch_array($qbilno))
	{
	?>
	  <tr>
			<td colspan="9" style="font-weight:bold;font-size:13px">Bill No : <?php echo $qbilno1['bill_no'];?></td>
			
		</tr>
		
<?php
$n=1;
$qry=mysqli_query($link,"SELECT  distinct `item_code` FROM `ph_item_return_master` WHERE  bill_no='$qbilno1[bill_no]' and return_date BETWEEN '$fdate' AND '$tdate'");
while($res=mysqli_fetch_array($qry))
{
	$q=mysqli_query($link,"SELECT * FROM `ph_item_return_master` WHERE bill_no='$qbilno1[bill_no]' and `item_code`='$res[item_code]' AND `return_date` BETWEEN '$fdate' AND '$tdate'");
	$num=mysqli_num_rows($q);
	while($r=mysqli_fetch_array($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
		
		$qmrm=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp from ph_purchase_receipt_details where item_code='$r[item_code]' and recept_batch='$r[batch_no]'"));
		$vmrp1=0;
		$vmrp1=$r['return_qnt']*$qmrm['recpt_mrp'];
		$vttlcstmrrtrn=$vttlcstmrrtrn+$vmrp1;
	?>
	<tr>
		<?php if($num>0){echo "<td  style='font-size:13px' rowspan='".$num."'>".$n."</td><td style='font-size:13px' rowspan='".$num."'>".$r['item_code']."</td><td style='font-size:13px' rowspan='".$num."'>".$itm['item_name']."</td><td style='font-size:13px' rowspan='".$num."'>".$r['batch_no']."</td>";}?>
		<td style="font-size:13px"><?php echo convert_date($r['return_date']);?></td>
		<td style="font-size:13px"><?php echo $r['return_qnt'];?></td>
		<td style="font-size:13px;text-align:right"><?php echo $qmrm['recpt_mrp'];?></td>
		<td style="font-size:13px;text-align:right"><?php echo number_format($vmrp1,2);?></td>
		<td style="font-size:13px"><?php echo $quser['name'];?></td>
	</tr>
	<?php
	$num=0;
	}
$n++;
}

?>

<?php
  }?>
  
  <tr>
	  <td colspan="7" style="font-weight:bold;font-size:13px;text-align:right">Total</td>
	  <td style="font-weight:bold;font-size:13px;text-align:right"><?php echo number_format($vttlcstmrrtrn,2);?></td>
	  <td>&nbsp;</td>
 </tr> 
 
</table>
</div>
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<script>
window.print();
</script>
