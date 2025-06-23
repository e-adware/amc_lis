<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date('H:i:s');

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('M Y', $timestamp);
		return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}


$type=$_POST['type'];

if($type==1)
{
	$ph=$_POST['ph'];
	$val=mysqli_real_escape_string($link,$_POST['val']);
	if($val)
	{
		if($ph=="1")
		{
			$q="select * from `item_master` where `sub_category_id`='1' AND `item_name`!='' and (`item_name` like '$val%' OR `short_name` like '$val%' OR `item_id` like '$val%') order by `item_name` limit 0,20";
		}
		if($ph=="3")
		{
			$q="select * from `item_master` where `sub_category_id`='10' AND `item_name`!='' and (`item_name` like '$val%' OR `short_name` like '$val%' OR `item_id` like '$val%') order by `item_name` limit 0,20";
		}
	}
	else
	{
		if($ph=="1")
		{
			$q="select * from `item_master` where `sub_category_id`='1' AND `item_name`!='' order by `item_name` limit 0,20";
		}
		if($ph=="3")
		{
			$q="select * from `item_master` where `sub_category_id`='10' AND `item_name`!='' order by `item_name` limit 0,20";
		}
	}
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>Code</th>
			<th>Item Name</th>
			<th>Stock</th>
		</tr>
		<?php
		while($qrpdct1=mysqli_fetch_array($qrpdct))
		{
			$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS `qnt` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$qrpdct1[item_id]'"));
			$stock=($stk['qnt']/$qrpdct1['strip_quantity']);
			if(strpos($stock, "."))
			{
				$stock=number_format($stock,1);
			}
		?>
		<tr style="cursor:pointer" onclick="val_load_new('<?php echo $qrpdct1['item_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_id'];?></td>
			<td width="70%"><?php echo $qrpdct1['item_name'];?></td>
			<td><?php echo $stock;?></td>
		</tr>	
		<?php	
		$i++;
		}
		?>
	</table>
	<?php
}

if($type==2)
{
	$ph=$_POST['ph'];
	$id=$_POST['id'];
	$vl=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name`,`gst`,`rack_no`,`strip_quantity` FROM `item_master` WHERE `item_id`='$id'"));
	$rack=mysqli_fetch_assoc(mysqli_query($link,"SELECT `rack_no` FROM `ph_item_rack_no` WHERE `substore_id`='$ph' AND `item_id`='$id'"));
	echo $id."#g#".$vl['item_name']."#g#".number_format($vl['gst'],0)."#g#".$rack['rack_no']."#g#".$vl['strip_quantity']."#g#";
}

if($type==3)
{
	$ph=$_POST['ph'];
	$itmid=$_POST['itmid'];
	$expiry=$_POST['expiry'];
	$rack_no=mysqli_real_escape_string($link,$_POST['rack_no']);
	$batch=mysqli_real_escape_string($link,$_POST['batch']);
	$qnt=$_POST['qnt'];
	$mrp=$_POST['mrp'];
	$cost=$_POST['cost'];
	$gst=$_POST['gst'];
	$saleprice=$_POST['saleprice'];
	$pack_qnt=$_POST['pack_qnt'];
	
	$mrp=($mrp/$pack_qnt);
	$cost=($cost/$pack_qnt);
	$qnt=($qnt*$pack_qnt);
	
	$gst_amt=($mrp-$saleprice);
	$gst_amt=($gst_amt*$qnt);
	
	$user=$_POST['user'];
	$vdate=date('Y-m-d');
	$blno="STOCK";
	//$ph=1;
	$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_item_rack_no` WHERE `substore_id`='$ph' AND `item_id`='$itmid'"));
	if($chk)
	{
		mysqli_query($link,"UPDATE `ph_item_rack_no` SET `rack_no`='$rack_no' WHERE `slno`='$chk[slno]'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ph_item_rack_no`(`substore_id`, `item_id`, `rack_no`) VALUES ('$ph','$itmid','$rack_no')");
	}
	mysqli_query($link,"UPDATE `item_master` SET `strip_quantity`='$pack_qnt' WHERE `item_id`='$itmid'");
	mysqli_query($link,"INSERT INTO `ph_item_stock_entry`(`item_code`, `batch_no`, `entry_date`, `return_qnt`, `item_mrp`, `item_cost_price`, `expiry_date`, `user`) VALUES ('$itmid','$batch','$vdate','$qnt','$mrp','$cost','$expiry','$user')");
	//mysqli_query($link,"insert into ph_purchase_receipt_details values('RCV1','1','$itmid','','$expiry','$vdate','$qnt',0,'$batch','','$mrp','$cost','$saleprice',0,0,0,0,'$gst',0)");
	mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('RCV1','1','$itmid','','$expiry','$vdate','$qnt','0','$batch','0','$mrp','$cost','$saleprice','0','0','0','0','$gst','$gst_amt')");
	//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qnt','5','$vdate','$time','$user')");
     //----------------------------------For stock---------------------------------//
	 $vstkqnt=0;
	 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$vdate' and item_code='$itmid' and batch_no='$batch' and `substore_id`='$ph' order by slno desc limit 0,1"));
	 if($qrstkmaster)
	 {
		 if($qnt>$qrstkmaster['s_remain'])
		 {
			$c_qnt=$qnt-$qrstkmaster['s_remain'];
			$add=$qrstkmaster['added']+$c_qnt;
			$vstkqnt=$qrstkmaster['s_remain']+$c_qnt;
			$process_type=5;
			
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$c_qnt','$vstkqnt','$process_type','$vdate','$time','$user')");
			mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',added='$add' where date='$vdate' and item_code='$itmid' and  batch_no='$batch' and `substore_id`='$ph'");
		 }
		 if($qnt<$qrstkmaster['s_remain'])
		 {
			$c_qnt=$qrstkmaster['s_remain']-$qnt;
			$return_supplier=$stk['return_supplier']+$c_qnt;
			$vstkqnt=$qrstkmaster['s_remain']-$c_qnt;
			$process_type=6;
			
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$c_qnt','$vstkqnt','$process_type','$vdate','$time','$user')");
			mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',return_supplier='$return_supplier' where date='$vdate' and item_code='$itmid' and  batch_no='$batch' and `substore_id`='$ph'");
		 }
		 if($qnt==$qrstkmaster['s_remain'])
		 {
			 $vstkqnt=$qnt;
		 }
		 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt', exp_date='$expiry' where `substore_id`='$ph' and item_code='$itmid' and batch_no='$batch'");
			
	 }
	 else///for if data not found
	 {
		 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itmid' and  batch_no='$batch' and `substore_id`='$ph' order by slno desc limit 0,1"));
		 
		 if($qnt>$qrstkmaster['s_remain'])
		 {
			$c_qnt=$qnt-$qrstkmaster['s_remain'];
			$add=$qrstkmaster['added']+$c_qnt;
			$vstkqnt=$qrstkmaster['s_remain']+$c_qnt;
			$process_type=5;
			
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$c_qnt','$vstkqnt','$process_type','$vdate','$time','$user')");
			mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','0','$vstkqnt','$vdate')");
		 }
		 if($qnt<$qrstkmaster['s_remain'])
		 {
			$c_qnt=$qrstkmaster['s_remain']-$qnt;
			$return_supplier=$stk['return_supplier']+$c_qnt;
			$vstkqnt=$qrstkmaster['s_remain']-$c_qnt;
			$process_type=6;
			
			mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$c_qnt','$vstkqnt','$process_type','$vdate','$time','$user')");
			mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','0','$vstkqnt','$vdate')");
		 }
		 if($qnt==$qrstkmaster['s_remain'])
		 {
			 $vstkqnt=$qnt;
		 }
		 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt', exp_date='$expiry' where `substore_id`='$ph' and item_code='$itmid' and batch_no='$batch'");
		 
		 $ichk=mysqli_fetch_array(mysqli_query($link,"select item_code from ph_stock_master where `substore_id`='$ph' and item_code='$itmid' and batch_no='$batch'"));
		 if($ichk)
		 {
		      mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt', exp_date='$expiry' where `substore_id`='$ph' and item_code='$itmid' and batch_no='$batch'");
		 }
		 else
		 {
			 mysqli_query($link,"insert into ph_stock_master(substore_id,item_code,batch_no,quantity,mfc_date,exp_date) values('$ph','$itmid','$batch','$vstkqnt','','$expiry')");
		 }
	 }
	 echo "Saved";
}

if($type==4)
{
	$ph=$_POST['ph'];
	$itmid=$_POST['itmid'];
	$gst=$_POST['gst'];
	$pack_qnt=$_POST['pack_qnt'];
	$rack_no=mysqli_real_escape_string($link,$_POST['rack_no']);
	$user=$_POST['user'];
	
	$vdate=date('Y-m-d');
	$blno="STOCK";
	
	$j=0;
	$all=$_POST['all'];
	$al=explode("#%#",$all);
	foreach($al as $a)
	{
		$v=explode("@@@",$a);
		$expiry=$v[0];
		$batch=$v[1];
		$batch=mysqli_real_escape_string($link,$batch);
		$mrp=$v[2];
		$saleprice=$v[3];
		$cost=$v[4];
		$qnt=$v[5];
		if($batch && $mrp && $qnt)
		{
			$mrp=($mrp/$pack_qnt);
			$cost=($cost/$pack_qnt);
			$qnt=($qnt*$pack_qnt);
			
			$gst_amt=($mrp-$saleprice);
			$gst_amt=($gst_amt*$qnt);
			
			$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_item_rack_no` WHERE `substore_id`='$ph' AND `item_id`='$itmid'"));
			if($chk)
			{
				mysqli_query($link,"UPDATE `ph_item_rack_no` SET `rack_no`='$rack_no' WHERE `slno`='$chk[slno]'");
			}
			else
			{
				mysqli_query($link,"INSERT INTO `ph_item_rack_no`(`substore_id`, `item_id`, `rack_no`) VALUES ('$ph','$itmid','$rack_no')");
			}
			mysqli_query($link,"UPDATE `item_master` SET `strip_quantity`='$pack_qnt' WHERE `item_id`='$itmid'");
			mysqli_query($link,"INSERT INTO `ph_item_stock_entry`(`item_code`, `batch_no`, `entry_date`, `return_qnt`, `item_mrp`, `item_cost_price`, `expiry_date`, `user`) VALUES ('$itmid','$batch','$vdate','$qnt','$mrp','$cost','$expiry','$user')");
			//mysqli_query($link,"insert into ph_purchase_receipt_details values('RCV1','1','$itmid','','$expiry','$vdate','$qnt',0,'$batch','','$mrp','$cost','$saleprice',0,0,0,0,'$gst',0)");
			mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('RCV1','1','$itmid','','$expiry','$vdate','$qnt','0','$batch','0','$mrp','$cost','$saleprice','0','0','0','0','$gst','$gst_amt')");
			//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qnt','5','$vdate','$time','$user')");
			 //----------------------------------For stock---------------------------------//
			 $vstkqnt=0;
			 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$vdate' and item_code='$itmid' and batch_no='$batch' and `substore_id`='$ph' order by slno desc limit 0,1"));
			 if($qrstkmaster)
			 {
				 if($qnt>$qrstkmaster['s_remain'])
				 {
					$c_qnt=$qnt-$qrstkmaster['s_remain'];
					$add=$qrstkmaster['added']+$c_qnt;
					$vstkqnt=$qrstkmaster['s_remain']+$c_qnt;
					$process_type=5;
					
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$c_qnt','$vstkqnt','$process_type','$vdate','$time','$user')");
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',added='$add' where date='$vdate' and item_code='$itmid' and  batch_no='$batch' and `substore_id`='$ph'");
				 }
				 if($qnt<$qrstkmaster['s_remain'])
				 {
					$c_qnt=$qrstkmaster['s_remain']-$qnt;
					$return_supplier=$stk['return_supplier']+$c_qnt;
					$vstkqnt=$qrstkmaster['s_remain']-$c_qnt;
					$process_type=6;
					
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$c_qnt','$vstkqnt','$process_type','$vdate','$time','$user')");
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',return_supplier='$return_supplier' where date='$vdate' and item_code='$itmid' and  batch_no='$batch' and `substore_id`='$ph'");
				 }
				 if($qnt==$qrstkmaster['s_remain'])
				 {
					 $vstkqnt=$qnt;
				 }
				 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt', exp_date='$expiry' where `substore_id`='$ph' and item_code='$itmid' and batch_no='$batch'");
					
			 }
			 else///for if data not found
			 {
				 $qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itmid' and  batch_no='$batch' and `substore_id`='$ph' order by slno desc limit 0,1"));
				 
				 if($qnt>$qrstkmaster['s_remain'])
				 {
					$c_qnt=$qnt-$qrstkmaster['s_remain'];
					$add=$qrstkmaster['added']+$c_qnt;
					$vstkqnt=$qrstkmaster['s_remain']+$c_qnt;
					$process_type=5;
					
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$c_qnt','$vstkqnt','$process_type','$vdate','$time','$user')");
					mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','0','$vstkqnt','$vdate')");
				 }
				 if($qnt<$qrstkmaster['s_remain'])
				 {
					$c_qnt=$qrstkmaster['s_remain']-$qnt;
					$return_supplier=$stk['return_supplier']+$c_qnt;
					$vstkqnt=$qrstkmaster['s_remain']-$c_qnt;
					$process_type=6;
					
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$c_qnt','$vstkqnt','$process_type','$vdate','$time','$user')");
					mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$blno','$itmid','$batch','$qrstkmaster[s_remain]','$qnt','0','$vstkqnt','$vdate')");
				 }
				 if($qnt==$qrstkmaster['s_remain'])
				 {
					 $vstkqnt=$qnt;
				 }
				 mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt', exp_date='$expiry' where `substore_id`='$ph' and item_code='$itmid' and batch_no='$batch'");
				 
				 $ichk=mysqli_fetch_array(mysqli_query($link,"select item_code from ph_stock_master where `substore_id`='$ph' and item_code='$itmid' and batch_no='$batch'"));
				 if($ichk)
				 {
					  mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt', exp_date='$expiry' where `substore_id`='$ph' and item_code='$itmid' and batch_no='$batch'");
				 }
				 else
				 {
					 mysqli_query($link,"insert into ph_stock_master(substore_id,item_code,batch_no,quantity,mfc_date,exp_date) values('$ph','$itmid','$batch','$vstkqnt','','$expiry')");
				 }
			 }
			 $j++;
		}
	}
	echo $j;
}

if($type==5)
{
	$ph=$_POST['ph'];
	$id=$_POST['id'];
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>Batch No</th>
			<th>Quantity</th>
			<th>Expiry</th>
		</tr>
	<?php
	$v=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$id'"));
	$q=mysqli_query($link,"SELECT `batch_no`, `quantity`, `exp_date` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$id' AND `quantity`>'0'");
	while($r=mysqli_fetch_assoc($q))
	{
		?>
		<tr>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo ($r['quantity']/$v['strip_quantity']);?></td>
			<td><?php echo date("Y-m", strtotime($r['exp_date']));?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
