<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d");
$time=date('H:i:s');

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

if($_POST["type"]=="inv_item_stock_maintain")
{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select `item_id`,`item_name`,`mrp`,`rack_no` from item_master where `item_name`!='' AND `item_name` like '$val%'  order by `item_name` limit 0,30";
	 }
	 else
	 {
	   	 $q="select `item_id`,`item_name`,`mrp`,`rack_no` from item_master where `item_name`!='' order by `item_name` limit 0,30";
	 }
	 
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Code</th>
			<th>Name</th>
		</tr>
	<?php
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	while($r=mysqli_fetch_array($qrpdct))
	{
	?>
	<tr style="cursor:pointer"  onclick="val_load_new('<?php echo $r['item_id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $r['item_id'];?></td>
		<td><?php echo $r['item_name'];?></td>
		<!--<td><?php echo $r['rack_no'];?></td>-->
	</tr>
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="inv_stock_item_load")
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name`,gst,mrp FROM `item_master` WHERE `item_id`='$id'"));
	echo $id."#g#".$d['item_name']."#g#".$d['gst']."#g#".$d['mrp']."#g#";
}

if($_POST["type"]=="stock_item_bch_load")
{
	$id=$_POST['id'];
	$val="<select id='bch' onchange='load_exp()'><option value='0'>Select</option>";
	$q=mysqli_query($link,"SELECT `batch_no` FROM `ph_stock_master` WHERE `item_code`='$id'");
	while($r=mysqli_fetch_array($q))
	{
		$val.="<option value='$r[batch_no]'>$r[batch_no]</option>";
	}
	$val.="</select>";
	echo $val;
}

if($_POST["type"]=="stock_item_exdate_load")
{
	$id=$_POST['id'];
	$bch=$_POST['bch'];
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity`,`exp_date` FROM `ph_stock_master` WHERE `item_code`='$id' AND `batch_no`='$bch'"));
	echo $f['exp_date']."#g#".$f['quantity'];
}

if($_POST["type"]=="purchs_bil_edit")
{
	$id=$_POST['id'];
	$bch=$_POST['bch'];
	$qgst=mysqli_fetch_array(mysqli_query($link,"select gst_percent from ph_item_master where `item_code`='$id' "));
	$q=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,sale_price from ph_purchase_receipt_details where `item_code`='$id' AND `recept_batch`='$bch'"));
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity`,`exp_date` FROM `ph_stock_master` WHERE `item_code`='$id' AND `batch_no`='$bch'"));
	echo $f['exp_date']."#g#".$q['recpt_mrp']."#g#".$q['sale_price']."#g#".$qgst['gst_percent'];
}


if($_POST["type"]=="purchs_bil_edit_save")
{
	$id=$_POST['id'];
	$bch=$_POST['bch'];
	$vexpirydat=$_POST['vexpirydat'];
	$vmrp=$_POST['vmrp'];
	$vsaleprice=$_POST['vsaleprice'];
	
	mysqli_query($link,"update ph_purchase_receipt_details set recpt_mrp='$vmrp',sale_price='$vsaleprice',expiry_date='$vexpirydat'  where `item_code`='$id' AND `recept_batch`='$bch'");
	mysqli_query($link,"update ph_stock_master set exp_date='$vexpirydat'  where `item_code`='$id' AND `batch_no`='$bch'");
	
	
}


if($_POST["type"]=="inv_stock_item_update")
{
	$id=$_POST['id'];
	$bch=$_POST['bch'];
	$vgst=$_POST['vgst'];
	$vmrp=$_POST['vmrp'];
	$expirydt=$_POST['expirydt'];
	$qnt=$_POST['qnt'];
	$gstamt=0;
	
	if($expirydt=="")
	{
		$expirydt=$date;
	}
	$expirydt=date("Y-m-t", strtotime($expirydt));
	
	
	$vslprice1=$vmrp-($vmrp*(100/(100+$vgst)));///Remove gst Calculattion
	$vslprice2=round($vslprice1,2);
	$vslprice3=$vmrp-$vslprice2;
	$vslprice=$vslprice3;
	
	mysqli_query($link,"update item_master set gst='$vgst',mrp='$vmrp' where item_id='$id' ");
	mysqli_query($link,"INSERT INTO `inv_item_stock_entry`(`item_id`, `batch_no`, `entry_date`, `entry_qnt`, `user`, `time`, `type`) VALUES('$id','$bch','$date','$qnt','$userid','$time','1')");
	$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  date='$date' and item_id='$id' and  batch_no='$bch'  order by slno desc limit 0,1"));
	if($qrstkmaster['item_id']!='')
	{	
		
			$add=$qrstkmaster['recv_qnty']+$qnt;
			$vstkqnt=$qrstkmaster['closing_qnty']+$qnt;
			
			mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$add' where date='$date' and item_id='$id' and  batch_no='$bch'  ");
			mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where  item_ids='$id' and batch_no='$bch' ");
		    
		    mysqli_query($link,"insert into inv_centrl_stock_update_record(`item_id`,`batch_no`,`qnty`,`user`,`date`,`time`) values('$id','$bch','$qnt','$userid','$date','$time')");
		    
		    mysqli_query($link,"insert into inv_main_stock_received_detail(`order_no`,`bill_no`,`item_id`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('001','02','$id','0000-00-00','$expirydt','$date','$qnt',0,'$bch','109','$vmrp','$q1[cost_price]','$vslprice','0',0,0,'$vgst','$gstamt')");
		
	}
	else///for if data not found
	{
		   $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_id='$id' and  batch_no='$bch'   order by slno desc limit 0,1"));
		
			$vstkqnt=$qrstkmaster['closing_qnty']+$qnt;
			
			mysqli_query($link,"INSERT INTO `inv_mainstock_details`(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) VALUES ('$id','$bch','$date','$qrstkmaster[closing_qnty]','$qnt',0,'$vstkqnt')");
			mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$id' and batch_no='$bch' ");
			mysqli_query($link,"insert into inv_maincurrent_stock (`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$id','$bch','$vstkqnt','$expirydt')");
		    
		     mysqli_query($link,"insert into inv_centrl_stock_update_record(`item_id`,`batch_no`,`qnty`,`user`,`date`,`time`) values('$id','$bch','$qnt','$userid','$date','$time')");
		     
		     mysqli_query($link,"insert into inv_main_stock_received_detail(`order_no`,`bill_no`,`item_id`,`manufactre_date`,`expiry_date`,`recpt_date`,`recpt_quantity`,`free_qnt`,`recept_batch`,`SuppCode`,`recpt_mrp`,`recept_cost_price`,`sale_price`,`item_amount`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount`) values('001','02','$id','0000-00-00','$expirydt','$date','$qnt',0,'$bch','109','$vmrp','$q1[cost_price]','$vslprice','0',0,0,'$vgst','$gstamt')");
	}
	
	echo "Saved";

}

if($_POST["type"]=="item_require_report")
{
	$user=$_POST['user'];
	$p_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$user' "));
	$user_access=array(101,102,103,154);
	if(in_array($p_info['emp_id'],$user_access))
	{
	?>
	<button type="button" class="btn btn-default btn_act" onclick="require_item_print('<?php echo $ph;?>')">Print</button>
	<?php
	}
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Requires</th>
			<th class="btn_act"><i class="icon-trash icon-large"></i></th>
		</tr>
	<?php
	$j=1;
	$q=mysqli_query($link,"SELECT a.`slno`,a.`requires`,a.`item_id`,b.`item_name` FROM `inv_item_require` a, `item_master` b WHERE a.`item_id`=b.`item_id` ORDER BY b.`item_name`");
	while($r=mysqli_fetch_assoc($q))
	{
		?>
		<tr class="req_tr">
			<td><?php echo $j;?></td>
			<td><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $r['requires'];?>x</td>
			<td class="btn_act">
				<button type="button" class="btn btn-danger btn-mini" onclick="remove_list(this,'<?php echo base64_encode($r['slno']);?>')"><i class="icon-remove icon-large"></i></button>
			</td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="remove_required_list")
{
	$sl=base64_decode($_POST['sl']);
	mysqli_query($link,"DELETE FROM `inv_item_require` WHERE `slno`='$sl'");
}

if($_POST["type"]=="oo")
{
	
}
?>
