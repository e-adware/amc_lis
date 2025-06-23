<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d");
$time=date('H:i:s');

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

if($_POST["type"]=="item_stock_maintain")
{
	 $val=$_POST['val'];
	 //----------central stock-----------------------
	 //~ if($val)
	 //~ {
		 //~ $q="select `item_id`,`item_name`,`mrp` from item_master where `item_name` like '$val%'  AND `item_id` IN (SELECT DISTINCT `item_id` FROM `inv_maincurrent_stock`) order by `item_name` LIMIT 0,20";
	 //~ }
	 //~ else
	 //~ {
	   	 //~ $q="select `item_id`,`item_name`,`mrp` from item_master where  `item_id` in (SELECT DISTINCT `item_id` FROM `inv_maincurrent_stock`  ) order by `item_name` LIMIT 0,20";
	 //~ }
	 //----------pharmacy stock-----------------------
	 if($val)
	 {
		 $q="select `item_id`,`item_name`,`mrp` from item_master where `item_name` like '$val%'  AND `item_id` IN (SELECT DISTINCT `item_code` FROM `ph_stock_master`) order by `item_name` LIMIT 0,20";
	 }
	 else
	 {
	   	 $q="select `item_id`,`item_name`,`mrp` from item_master where `item_id` in (SELECT DISTINCT `item_code` FROM `ph_stock_master`) order by `item_name` LIMIT 0,20";
	 }
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Id</th>
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
	</tr>
	<?php	
	$i++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]=="stock_item_load")
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$id'"));
	echo $id."#g#".$d['item_name']."#g#";
}

if($_POST["type"]=="stock_item_bch_load")
{
	$id=$_POST['id'];
	//------------------------------------------------
	//~ $val="<select id='bch' onchange='load_exp()'><option value='0'>Select</option>";
	//~ $q=mysqli_query($link,"SELECT `batch_no` FROM `inv_maincurrent_stock` WHERE `item_id`='$id' ");
	//~ while($r=mysqli_fetch_array($q))
	//~ {
		//~ $val.="<option value='$r[batch_no]'>$r[batch_no]</option>";
	//~ }
	//~ $val.="</select>";
	//------------------------------------------------
	$val="";
	$q=mysqli_query($link,"SELECT `batch_no` FROM `ph_stock_master` WHERE `item_code`='$id' ");
	while($r=mysqli_fetch_array($q))
	{
		if($val)
		{
			$val.="@govinda@".$r['batch_no'];
		}
		else
		{
			$val=$r['batch_no'];
		}
	}
	//------------------------------------------------
	echo $val;
}

if($_POST["type"]=="stock_item_exdate_load")
{
	$id=$_POST['id'];
	$bch=$_POST['bch'];
	//---------------central store---------------------------------
	//~ $f=mysqli_fetch_array(mysqli_query($link,"SELECT `closing_stock`,`exp_date` FROM `inv_maincurrent_stock` WHERE `item_id`='$id' AND `batch_no`='$bch' "));
	//~ echo $f['exp_date']."#g#".$f['closing_stock'];
	
	//---------------pharmacy store--------------------------------
	$ph=1;
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity`,`exp_date` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$id' AND `batch_no`='$bch' "));
	
	echo $f['exp_date']."#g#".$f['quantity'];
}

if($_POST["type"]=="purchs_bil_edit")
{
	$id=$_POST['id'];
	$bch=$_POST['bch'];
	$qgst=mysqli_fetch_array(mysqli_query($link,"select gst from item_master where `item_id`='$id' "));
	//---------------central store---------------------------------
	//$q=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,sale_price from inv_main_stock_received_detail where `item_id`='$id' AND `recept_batch`='$bch'"));
	//$f=mysqli_fetch_array(mysqli_query($link,"SELECT `closing_stock`,`exp_date` FROM `inv_maincurrent_stock` WHERE `item_id`='$id' AND `batch_no`='$bch'"));
	//echo $f['exp_date']."#g#".$q['recpt_mrp']."#g#".$q['sale_price']."#g#".$qgst['gst'];
	
	//---------------pharmacy store--------------------------------
	$ph=1;
	$q=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$id' AND `recept_batch`='$bch'"));
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity`,`exp_date` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$id' AND `batch_no`='$bch' "));
	echo $f['exp_date']."#g#".$q['recpt_mrp']."#g#".$q['sale_price']."#g#".$qgst['gst'];
}


if($_POST["type"]=="purchs_bil_edit_save")
{
	$id=$_POST['id'];
	$bch=$_POST['bch'];
	$vexpirydat=$_POST['vexpirydat'];
	$vmrp=$_POST['vmrp'];
	$vsaleprice=$_POST['vsaleprice'];
	
	//---------------central store---------------------------------
	//~ mysqli_query($link,"update inv_main_stock_received_detail set recpt_mrp='$vmrp',sale_price='$vsaleprice',expiry_date='$vexpirydat'  where `item_id`='$id' AND `recept_batch`='$bch'");
	//~ mysqli_query($link,"update inv_maincurrent_stock set exp_date='$vexpirydat'  where `item_id`='$id' AND `batch_no`='$bch'");
	//~ mysqli_query($link,"update item_master set mrp='$vmrp'  where `item_id`='$id' ");
	
	//---------------pharmacy store--------------------------------
	$ph=1;
	mysqli_query($link,"UPDATE `ph_purchase_receipt_details` SET `recpt_mrp`='$vmrp', `sale_price`='$vsaleprice', `expiry_date`='$vexpirydat' WHERE `item_code`='$id' AND `recept_batch`='$bch'");
	mysqli_query($link,"UPDATE `ph_stock_master` SET `exp_date`='$vexpirydat' WHERE `substore_id`='$ph' AND `item_code`='$id' AND `batch_no`='$bch'");
	mysqli_query($link,"UPDATE `item_master` SET `mrp`='$vmrp' WHERE `item_id`='$id' ");
}


if($_POST["type"]=="stock_item_update")
{
	$id=$_POST['id'];
	$bch=$_POST['bch'];
	$extqnt=$_POST['extqnt'];
	$qnt=$_POST['qnt'];
	$opp=$_POST['opp'];
	$user=$_POST['user'];
	//~ if($opp==1)
	//~ {
		//~ $qn=$qnt+$extqnt;
	//~ }
	//~ if($opp==0)
	//~ {
		//~ $qn=$extqnt-$qnt;
	//~ }
	
	//------------------------central stock--------------------------------------
	//~ $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  date='$date' and item_id='$id' and  batch_no='$bch'  order by slno desc limit 0,1"));
	//~ if($qrstkmaster['item_id']!='')
	//~ {	
		//~ if($opp==1)
		//~ {
			//~ $add=$qrstkmaster['recv_qnty']+$qnt;
			//~ $vstkqnt=$qrstkmaster['closing_qnty']+$qnt;
			//~ $m_type=1;
			
			//~ mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$add' where date='$date' and item_id='$id' and  batch_no='$bch'  ");
			//~ mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where  item_id='$id' and  batch_no='$bch' ");
		//~ }
		//~ else if($opp==0)
		//~ {
			//~ $add=$qrstkmaster['issu_qnty']+$qnt;
			//~ $vstkqnt=$qrstkmaster['closing_qnty']-$qnt;
			//~ $m_type=0;
			//~ mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$add' where date='$date' and item_id='$id' and  batch_no='$bch' ");
			//~ mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where  item_id='$id' and  batch_no='$bch'  ");
		//~ }
		
		//~ mysqli_query($link,"INSERT INTO `inv_item_stock_entry`( `item_id`, `batch_no`, `entry_date`, `entry_qnt`, `user`, `time`, `type`) VALUES ('$id','$bch','$date','$qnt','$userid','$time','$m_type')");
	//~ }
	//~ else///for if data not found
	//~ {
		//~ $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where  item_id='$id' and  batch_no='$bch'   order by slno desc limit 0,1"));
		//~ if($opp==1)
		//~ {
			//~ $vstkqnt=$qrstkmaster['closing_qnty']+$qnt;
			//~ $m_type=1;
			
			//~ mysqli_query($link,"INSERT INTO `inv_mainstock_details`(`item_id`, `batch_no`, `date`, `op_qnty`, `recv_qnty`, `issu_qnty`, `closing_qnty`) VALUES ('$id','$bch','$date','$qrstkmaster[closing_qnty]','$qnt','','$vstkqnt')");
			//~ mysqli_query($link,"update inv_maincurrent_stock set  closing_stock='$vstkqnt' where  item_id='$id' and  batch_no='$bch'  ");
		//~ }
		//~ else if($opp==0)
		//~ {
			//~ $vstkqnt=$qrstkmaster['closing_qnty']-$qnt;
			//~ $m_type=0;
			//~ mysqli_query($link,"INSERT INTO `inv_mainstock_details`(`item_id`, `batch_no`, `date`, `op_qnty`, `recv_qnty`, `issu_qnty`, `closing_qnty`) VALUES ('$id','$bch','$date','$qrstkmaster[closing_qnty]','0','$qnt','$vstkqnt')");
			//~ mysqli_query($link,"update inv_maincurrent_stock set  closing_stock='$vstkqnt' where  item_id='$id' and  batch_no='$bch' ");
		//~ }
		
		//~ mysqli_query($link,"INSERT INTO `inv_item_stock_entry`( `item_id`, `batch_no`, `entry_date`, `entry_qnt`, `user`, `time`, `type`) VALUES ('$id','$bch','$date','$qnt','$userid','$time','$m_type')");
	//~ }
	
	//------------------------pharmacy stock--------------------------------------
	$itm=$id;
	$ph=1;
	$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
	if($stk)
	{
		if($opp>0) // add=1
		{
			$add_qnt=$stk['added']+$qnt;
			$s_remain=$stk['s_remain']+$qnt;
			//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','MAINTAIN','$itm','$bch','$qnt','5','$date','$time','$user')");
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','MAINTAIN','$itm','$bch','$stk[s_remain]','$qnt','$s_remain','5','$date','$time','$user')");
			mysqli_query($link,"update ph_stock_process set s_remain='$s_remain',added='$add_qnt' where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
			mysqli_query($link,"update ph_stock_master set quantity='$s_remain' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
		}
		else // deduct=0
		{
			$s_remain=$stk['s_remain']-$qnt;
			$sell=$stk['sell']+$qnt;
			//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','MAINTAIN','$itm','$bch','$qnt','6','$date','$time','$user')");
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','MAINTAIN','$itm','$bch','$stk[s_remain]','$qnt','$s_remain','6','$date','$time','$user')");
			mysqli_query($link,"update ph_stock_process set s_remain='$s_remain',sell='$sell' where date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
			mysqli_query($link,"update ph_stock_master set quantity='$s_remain' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
		}
	}
	else // for if data not found
	{
		$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
		
		if($opp>0) // add=1
		{
			$s_remain=$stk['s_remain']+$qnt;
			//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','MAINTAIN','$itm','$bch','$qnt','5','$date','$time','$user')");
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','MAINTAIN','$itm','$bch','$stk[s_remain]','$qnt','$s_remain','5','$date','$time','$user')");
			mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`, `process_no`, `item_code`, `batch_no`, `s_available`, `added`, `sell`, `return_cstmr`, `return_supplier`, `s_remain`, `date`) VALUES ('$ph','MAINTAIN','$itm','$bch','$stk[s_remain]','$qnt','0','0','0','$s_remain','$date')");
			mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`='$s_remain' WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$ph'");
		}
		else // deduct=0
		{
			$s_remain=$stk['s_remain']-$qnt;
			//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','MAINTAIN','$itm','$bch','$qnt','6','$date','$time','$user')");
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','MAINTAIN','$itm','$bch','$stk[s_remain]','$qnt','$s_remain','6','$date','$time','$user')");
			mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`, `process_no`, `item_code`, `batch_no`, `s_available`, `added`, `sell`, `return_cstmr`, `return_supplier`, `s_remain`, `date`) VALUES ('$ph','MAINTAIN','$itm','$bch','$stk[s_remain]','0','$qnt','0','0','$s_remain','$date')");
			mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`='$s_remain' WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$ph'");
		}
	}
	
	echo "Saved";

}

if($_POST["type"]=="oo")
{
	
}
?>
