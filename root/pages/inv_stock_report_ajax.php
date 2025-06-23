<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("h:i A", strtotime($time));
	return $time;
}

$user_access=array(101,102,103,154);

$type=$_POST['type'];

if($type==1)
{
	$itm=$_POST['itm'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$pkd=mysqli_fetch_array(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
	?>
	<table class="table table-condensed" style="background:#FFF;">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th>Opening</th>
			<th>Add</th>
			<th>Deduct</th>
			<th>Closing</th>
			<th>Date</th>
		</tr>
		</thead>
		<tbody>
	<?php
	$qry=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `inv_mainstock_details` WHERE `item_id`='$itm' AND `date` BETWEEN '$fdate' AND '$tdate'");
	while($rr=mysqli_fetch_assoc($qry))
	{
	$exp_date=mysqli_fetch_assoc(mysqli_query($link,"SELECT `exp_date` FROM `inv_maincurrent_stock` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
	$j=1;
	$q=mysqli_query($link,"SELECT * FROM `inv_mainstock_details` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' AND `date` BETWEEN '$fdate' AND '$tdate'");
	while($r=mysqli_fetch_assoc($q))
	{
		$process="";
		$added=0;
		$deduct=0;
		$added=($r['receive']/$pkd['strip_quantity']);
		$deduct=($r['issue']/$pkd['strip_quantity']);
		if($added>$deduct)
		{
			$color="#073D00";
		}
		else if($added<$deduct)
		{
			$color="#AA1500";
		}
		else
		{
			$color="#444";
		}
		?>
		<tr style="color:<?php echo $color;?>;">
			<td><?php echo $j;?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo date("Y-m", strtotime($exp_date['exp_date']));?></td>
			<td><?php echo ($r['opening']/$pkd['strip_quantity']);?></td>
			<td><?php echo $added;?></td>
			<td><?php echo $deduct;?></td>
			<td><?php echo ($r['closing']/$pkd['strip_quantity']);?></td>
			<td><?php echo convert_date($r['date']);?></td>
		</tr>
		<?php
		$j++;
	}
	?>
		<tr>
			<td colspan="8" style="background:#888;"></td>
		</tr>
	<?php
	}
	?>
	</tbody>
	</table>
	<?php
}

if($type==2)
{
	$days=$_POST['days'];
	$user=$_POST['user'];
	$fdate=date("Y-m-d", strtotime("-$days days ".$date));
	//$qry="SELECT `item_id`,`item_name`, `rack_no`,`strip_quantity` FROM `item_master` WHERE `item_id` IN (SELECT DISTINCT `item_id` FROM `inv_main_stock_received_detail` WHERE `recpt_date` BETWEEN '$fdate' AND '$date') AND `item_id` NOT IN (SELECT DISTINCT `item_id` FROM `ph_item_process` WHERE `process_type`='7' AND `date` BETWEEN '$fdate' AND '$date') ORDER BY `item_name`";
	//$qry="SELECT `item_id`,`item_name`, `rack_no`,`strip_quantity` FROM `item_master` WHERE `item_id` NOT IN (SELECT DISTINCT `item_id` FROM `ph_item_process` WHERE `process_type`='7' AND `date` BETWEEN '$fdate' AND '$date') ORDER BY `item_name`";
	//echo $qry;
	$ids="";
	$qq=mysqli_query($link,"SELECT DISTINCT `item_id` FROM `inv_maincurrent_stock` WHERE `closing` > 0");
	while($rr=mysqli_fetch_assoc($qq))
	{
		$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_id` FROM `inv_item_process` WHERE `item_id`='$rr[item_id]' AND (`process_type`='1' OR  `process_type`='7') AND `date`>='$fdate'"));
		if(!$chk)
		{
			if($ids)
			{
				$ids.=",".$rr['item_id'];
			}
			else
			{
				$ids=$rr['item_id'];
			}
		}
	}
	//echo $ids;
	$qry="SELECT `item_id`,`item_name`, `rack_no`,`strip_quantity` FROM `item_master` WHERE `item_id` IN ($ids)";
	//echo $qry;
	?>
	<div class="noprint">
	<select class="span2" onchange="dump_stock(this.value)">
		<option value="30" <?php if($days=="30"){echo "selected='selected'";}?>>30 Days</option>
		<option value="45" <?php if($days=="45"){echo "selected='selected'";}?>>45 Days</option>
		<option value="60" <?php if($days=="60"){echo "selected='selected'";}?>>60 Days</option>
		<option value="90" <?php if($days=="90"){echo "selected='selected'";}?>>90 Days</option>
		<option value="120" <?php if($days=="120"){echo "selected='selected'";}?>>120 Days</option>
		<option value="150" <?php if($days=="150"){echo "selected='selected'";}?>>150 Days</option>
		<option value="180" <?php if($days=="180"){echo "selected='selected'";}?>>180 Days</option>
	</select>
	<?php
	$p_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$user' "));
	$user_access=array(101,102,103,154);
	if(in_array($p_info['emp_id'],$user_access))
	{
	?>
	<button type="button" class="btn btn-primary" onclick="print_dump_stock('<?php echo $days;?>')">Print</button>
	<?php
	}
	?>
	</div>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Code</th>
			<th>Name</th>
			<th>Stock</th>
			<th>Rack No</th>
		</tr>
		<?php
		$j=1;
		$q=mysqli_query($link,$qry);
		while($r=mysqli_fetch_assoc($q))
		{
			$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(sum(`closing`),0) AS `stock` FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_id]'"));
		?>
		<tr>
			<td width="5%"><?php echo $j;?></td>
			<td><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo round($stk['stock']/$r['strip_quantity']);?></td>
			<td><?php echo $r['rack_no'];?></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
}

if($type==3)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$user=$_POST['user'];
	$q=mysqli_query($link,"SELECT * FROM `ph_stock_transfer_master` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	?>
	<div class="noprint">
		<?php
		$p_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$user' "));
		$user_access=array(101,102,103,154);
		if(in_array($p_info['emp_id'],$user_access))
		{
		?>
		<button type="button" class="btn btn-primary btn_act" onclick="transfer_report_print('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
		<?php
		}
		?>
	</div>
	<table class="table table-condensed table-bordered" style="background:#FFF;">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Counter</th>
			<th style="text-align:right;">Amount</th>
			<th style="text-align:right;">GST</th>
			<th>Date / Time</th>
			<th>User</th>
			<th><span class="noprint btn_act">Print</span></th>
		</tr>
		</thead>
		<?php
		$j=1;
		while($r=mysqli_fetch_assoc($q))
		{
			$dept=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$r[substore_id]'"));
			$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
			$icon="<i class='icon-minus-sign icon-large' title='Not Received' style='color:#DA3E3E;'></i>";
			if($r['stat']>0)
			{
				$icon="<i class='icon-ok-sign icon-large' title='Received' style='color:#2A7632;'></i>";
			}
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $dept['substore_name'];?></td>
			<td style="text-align:right;"><?php echo $r['amount'];?></td>
			<td style="text-align:right;"><?php echo $r['gst_amount'];?></td>
			<td><?php echo convert_date($r['date'])." ".convert_time($r['time']);?></td>
			<td><?php echo $usr['name'];?></td>
			<td>
				<?php echo $icon;?>
				<span class="noprint btn_act">
				<button type="button" class="btn btn-primary btn-mini" onclick="transfer_details_print('<?php echo $r['issue_no'];?>')">Print</button>
				</span>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
}

if($type==4)
{
	$ino=$_POST['ino'];
	$q=mysqli_query($link,"SELECT * FROM `ph_stock_transfer_details` WHERE `issue_no`='$ino'");
	?>
	<table class="table table-condensed table-bordered" style="background:#FFF;">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Description</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th style="text-align:right;">MRP</th>
			<th style="text-align:right;">Rate</th>
			<th style="text-align:right;">Qnt</th>
			<th style="text-align:right;">Amount</th>
			<th style="text-align:right;">GST Amount</th>
		</tr>
		</thead>
		<?php
		$j=1;
		$amt=0;
		$gst=0;
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
			$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
			$icon="<i class=' icon-minus-sign icon-large' style='color:#DA3E3E;'></i>";
			if($r['stat']>0)
			{
				$icon="<i class=' icon-ok-sign icon-large' style='color:#2A7632;'></i>";
			}
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo date("Y-m", strtotime($r['exp_date']));?></td>
			<td style="text-align:right;"><?php echo number_format($r['mrp']*$itm['strip_quantity'],2);?></td>
			<td style="text-align:right;"><?php echo number_format($r['rate']*$itm['strip_quantity'],2);?></td>
			<td style="text-align:right;"><?php echo $r['issue_qnt']/$itm['strip_quantity'];?></td>
			<td style="text-align:right;"><?php echo $r['amount'];?></td>
			<td style="text-align:right;"><?php echo $r['gst_amount'];?></td>
		</tr>
		<?php
		$j++;
		$amt+=$r['amount'];
		$gst+=$r['gst_amount'];
		}
		?>
		<tr>
			<td colspan="7"></td>
			<th style="text-align:right;"><?php echo number_format($amt,2);?></th>
			<th style="text-align:right;"><?php echo number_format($gst,2);?></th>
		</tr>
	</table>
	<?php
}

if($type==5)
{
	$itm=$_POST['itm'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$pkd=mysqli_fetch_array(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Batch No</th>
			<th>Opening</th>
			<th>Add</th>
			<th>Deduct</th>
			<th>Closing</th>
			<th>Process Type</th>
			<th>Description</th>
			<th>Date</th>
			<th>User</th>
		</tr>
		<?php
		$qry=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `inv_item_process` WHERE `item_id`='$itm' AND `date` BETWEEN '$fdate' AND '$tdate'");
		while($rr=mysqli_fetch_assoc($qry))
		{
		$exp_date=mysqli_fetch_assoc(mysqli_query($link,"SELECT `exp_date` FROM `inv_maincurrent_stock` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
		$j=1;
		$q=mysqli_query($link,"SELECT * FROM `inv_item_process` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' AND `date` BETWEEN '$fdate' AND '$tdate'");
		while($r=mysqli_fetch_assoc($q))
		{
		$added=0;
		$deduct=0;
		$process="";
		if($r['process_type']==1)
		{
			$process="Purchase";
			$added=$r['qnt'];
		}
		if($r['process_type']==4)
		{
			$process="Supplier Return";
			$deduct=$r['qnt'];
		}
		if($r['process_type']==5)
		{
			$process="Stock Add";
			$added=$r['qnt'];
		}
		if($r['process_type']==6)
		{
			$process="Stock Deduct";
			$deduct=$r['qnt'];
		}
		if($r['process_type']==7)
		{
			$process="Stock Issue";
			$deduct=$r['qnt'];
		}
		if($r['process_type']==8)
		{
			$process="Stock Return";
			$added=$r['qnt'];
		}
		if($r['process_type']==9)
		{
			$process="Bill Added";
			$added=$r['qnt'];
		}
		if($r['process_type']==9)
		{
			$process="Bill Deduct";
			$deduct=$r['qnt'];
		}
		$to="";
		if($r['substore_id']>0)
		{
		$dept=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$r[substore_id]'"));
		$to=$dept['substore_name'];
		}
		else
		{
		$bill=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_no` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$r[process_no]'"));
		if($bill)
		{
		$to=$bill['bill_no'];
		}
		else
		{
		$bill=mysqli_fetch_assoc(mysqli_query($link,"SELECT `challan_no` FROM `ph_challan_receipt_master` WHERE `order_no`='$r[process_no]'"));
		if($bill)
		{
		$to=$bill['challan_no'];
		}
		else
		{
		$to="Mannual";
		}
		}
		}
		if($r['user'])
		{
		$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		$u_name=$u['name'];
		}
		else
		{
		$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `user` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$r[process_no]'"));
		if($usr)
		{
		$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$usr[user]'"));
		$u_name=$u['name'];
		}
		else
		{
		$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `user` FROM `ph_challan_receipt_master` WHERE `order_no`='$r[process_no]'"));
		$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$usr[user]'"));
		$u_name=$u['name'];
		}
		}
		if($added>$deduct)
		{
			$color="#073D00";
		}
		else if($added<$deduct)
		{
			$color="#AA1500";
		}
		else
		{
			$color="#444";
		}
		?>
		<tr style="color:<?php echo $color;?>;">
			<td><?php echo $j;?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo ($r['opening']/$pkd['strip_quantity']);?></td>
			<td onclick="<?php echo $onclick_a;?>" style="<?php if($onclick_a){echo "cursor:pointer;";}?>" class="<?php if($onclick_a){echo "td_box";}?>"><?php echo $added/$pkd['strip_quantity'];?></td>
			<td onclick="<?php echo $onclick_b;?>" style="<?php if($onclick_b){echo "cursor:pointer;";}?>" class="<?php if($onclick_b){echo "td_box";}?>"><?php echo $deduct/$pkd['strip_quantity'];?></td>
			<td><?php echo ($r['closing']/$pkd['strip_quantity']);?></td>
			<td><?php echo $process;?></td>
			<td><?php echo $to;?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $u_name;?></td>
		</tr>
		<?php
		$j++;
		}
		?>
		<tr>
			<td colspan="10" style="background:#888;"></td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
}

if($type==5555)
{
	$itm=$_POST['itm'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$pkd=mysqli_fetch_array(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
	?>
	<table class="table table-condensed" style="background:#FFF;">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Batch No</th>
			<!--<th>Expiry</th>-->
			<th>Opening</th>
			<th>Add</th>
			<th>Deduct</th>
			<th>Closing</th>
			<th>Description</th>
			<th>Date</th>
			<th>User</th>
		</tr>
		</thead>
		<tbody>
	<?php
	$qry=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `inv_mainstock_details` WHERE `item_id`='$itm' AND `date` BETWEEN '$fdate' AND '$tdate'");
	while($rr=mysqli_fetch_assoc($qry))
	{
	$exp_date=mysqli_fetch_assoc(mysqli_query($link,"SELECT `exp_date` FROM `inv_maincurrent_stock` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
	$j=1;
	$q=mysqli_query($link,"SELECT * FROM `inv_mainstock_details` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' AND `date` BETWEEN '$fdate' AND '$tdate'");
	while($r=mysqli_fetch_assoc($q))
	{
		$process="";
		$added=0;
		$deduct=0;
		$added=($r['recv_qnty']/$pkd['strip_quantity']);
		$deduct=($r['issu_qnty']/$pkd['strip_quantity']);
		$to="";
		$u_name="";
		$onclick_a="";
		$onclick_b="";
		if($r['recv_qnty'])
		{
			$bl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `rcv_no`,`bill_no` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `recept_batch`='$rr[batch_no]' AND `recpt_date`='$r[date]' ORDER BY `slno` DESC LIMIT 0,1"));
			if($bl)
			{
				$to="Bill No : ".$bl['bill_no'];
				$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `user` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$bl[rcv_no]'"));
				$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$usr[user]'"));
				$u_name=$u['name'];
				$onclick_a="load_list('".$bl['rcv_no']."','2')"; // bill entry
			}
			else
			{
				$bl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `rcv_no`,`bill_no`,`SuppCode` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `recept_batch`='$rr[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
				if($bl['SuppCode']==0)
				{
					$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `user` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$bl[rcv_no]'"));
					if($usr)
					{
						$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$usr[user]'"));
						$u_name=$u['name'];
						$to="Stock Entry";
						$onclick_a="";
					}
					else
					{
						$ch=mysqli_fetch_assoc(mysqli_query($link,"SELECT `order_no`,`bill_no` FROM `ph_challan_receipt_details` WHERE `item_id`='$itm' AND `recept_batch`='$rr[batch_no]' AND `recpt_date`='$r[date]' ORDER BY `slno` DESC LIMIT 0,1"));
						$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `challan_no`,`user` FROM `ph_challan_receipt_master` WHERE `order_no`='$ch[order_no]'"));
						$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$usr[user]'"));
						$u_name=$u['name'];
						$to="Challan Receipt : ".$usr['challan_no'];
						$onclick_a="load_list('".$ch['order_no']."','3')"; // challan
					}
				}
				else
				{
					$bl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `user`,`type` FROM `inv_item_stock_entry` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' AND `entry_date`='$r[date]' ORDER BY `slno` DESC LIMIT 0,1"));
					if($bl)
					{
						$to="Stock Maintain";
						$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$bl[user]'"));
						$u_name=$u['name'];
						$onclick_a="";
					}
					else
					{
						$ch=mysqli_fetch_assoc(mysqli_query($link,"SELECT `order_no`,`bill_no` FROM `ph_challan_receipt_details` WHERE `item_id`='$itm' AND `recept_batch`='$rr[batch_no]' AND `recpt_date`='$r[date]' ORDER BY `slno` DESC LIMIT 0,1"));
						$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `challan_no`,`user` FROM `ph_challan_receipt_master` WHERE `order_no`='$ch[order_no]'"));
						$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$usr[user]'"));
						$u_name=$u['name'];
						$to="Challan Receipt : ".$usr['challan_no'];
						$onclick_a="load_list('".$ch['order_no']."','3')"; // challan
					}
				}
			}
		}
		if($r['issu_qnty'])
		{
			$ph=mysqli_fetch_assoc(mysqli_query($link,"SELECT a.`issue_no`,a.`substore_id`,a.`user` FROM `ph_stock_transfer_master` a, `ph_stock_transfer_details` b WHERE a.`issue_no`=b.`issue_no` AND b.`item_id`='$itm' AND b.`batch_no`='$rr[batch_no]' AND b.`date`='$r[date]'"));
			if($ph)
			{
				$dept=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$ph[substore_id]'"));
				$to.=" -> (".$dept['substore_name'].")";
				$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$ph[user]'"));
				$u_name=$u['name'];
				$onclick_b="load_list('".$ph['issue_no']."','1')"; // department issue
			}
			else
			{
				$bl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `user`,`type` FROM `inv_item_stock_entry` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' AND `entry_date`='$r[date]' ORDER BY `slno` DESC LIMIT 0,1"));
				if($bl)
				{
					$to.=" Stock Maintain";
					$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$bl[user]'"));
					$u_name=$u['name'];
					$onclick_b="";
				}
				else
				{
					$u_name=".";
					$onclick_b="";
				}
			}
		}
		if($added>$deduct)
		{
			$color="#073D00";
		}
		else if($added<$deduct)
		{
			$color="#AA1500";
		}
		else
		{
			$color="#444";
		}
		?>
		<tr style="color:<?php echo $color;?>;">
			<td><?php echo $j;?></td>
			<td><?php echo $r['batch_no'];?></td>
			<!--<td><?php echo date("Y-m", strtotime($exp_date['exp_date']));?></td>-->
			<td><?php echo ($r['op_qnty']/$pkd['strip_quantity']);?></td>
			<td onclick="<?php echo $onclick_a;?>" style="<?php if($onclick_a){echo "cursor:pointer;";}?>" class="<?php if($onclick_a){echo "td_box";}?>"><?php echo $added;?></td>
			<td onclick="<?php echo $onclick_b;?>" style="<?php if($onclick_b){echo "cursor:pointer;";}?>" class="<?php if($onclick_b){echo "td_box";}?>"><?php echo $deduct;?></td>
			<td><?php echo ($r['closing']/$pkd['strip_quantity']);?></td>
			<td><?php echo $to;?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $u_name;?></td>
		</tr>
		<?php
		$j++;
	}
	?>
		<tr>
			<td colspan="9" style="background:#888;"></td>
		</tr>
	<?php
	}
	?>
	</tbody>
	</table>
	<?php
}

if($type==6)
{
	$id=$_POST['id'];
	?>
	<table class="table table-condensed table-report" style="background:#FFF;">
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th>Stock</th>
			<th>Rack No</th>
		</tr>
	<?php
	$j=1;
	$q=mysqli_query($link,"SELECT a.*,b.`item_name`,b.`rack_no` FROM `inv_maincurrent_stock` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND a.`closing`>'0' AND a.`exp_date`<'$date' ORDER BY b.`item_name`");
	while($r=mysqli_fetch_assoc($q))
	{
		$pkd=mysqli_fetch_array(mysqli_query($link,"SELECT `gst`,`strip_quantity` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$stk=($r['closing']/$pkd['strip_quantity']);
		if(strpos($stk,'.') == true)
		{
			$stk=$r['closing']." <small>(tabs)</small>";
		}
		$exp_date="";
		if($r['exp_date']!="0000-00-00" && $r['exp_date']!="1970-01-31")
		{
			$exp_date=date("Y-m",strtotime($r['exp_date']));
		}
		$a="";
		if($exp_date=="")
		{
		$n1=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno`,`expiry_date` FROM `inv_main_stock_received_detail` WHERE `item_id`='$r[item_id]' AND `recept_batch`='$r[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
		if($n1 && $n1['expiry_date'] > $r['exp_date'])
		{
			$a="1";
		}
		else
		{
			$n2=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno`,`expiry_date` FROM `ph_challan_receipt_details` WHERE `item_id`='$r[item_id]' AND `recept_batch`='$r[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
			if($n2 && $n2['expiry_date'] > $r['exp_date'])
			{
				$a="2";
			}
			else
			{
				$n3=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno`,`exp_date` FROM `ph_stock_master` WHERE `item_code`='$r[item_id]' AND `batch_no`='$r[batch_no]'"));
				if($n3 && $n3['exp_date'] > $r['exp_date'])
				{
					$a="3";
					//$qmrp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recpt_mrp`,`recept_cost_price`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$r[item_id]' AND `recept_batch`='$r[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
					//$a="3 INSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('ENT001','ENT001','ENT001','$r[item_id]','','$n3[exp_date]','$date','$pkd[strip_quantity]','0','0','$r[batch_no]','0','$qmrp[recpt_mrp]','$qmrp[recept_cost_price]','$qmrp[recept_cost_price]','$qmrp[sale_price]','0','0','0','$pkd[gst]','0');";
				}
				else
				{
					$a="4";
				}
			}
		}
		}
		?>
		<tr style="color:<?php //echo $color;?>;">
			<td><?php echo $j;?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo $exp_date;?></td>
			<td><?php echo $stk;?></td>
			<td><?php echo $r['rack_no'];?></td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<?php
}

if($type==7)
{
	$tdate=$date;
	$fdate=date('Y-m-d', strtotime('-6 days'));
	$limits=10;
	
	
	$labels = array();
	$qq=mysqli_query($link,"SELECT DISTINCT `entry_date` FROM `ph_sell_details` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate'");
	while($rr=$qq->fetch_assoc())
	{
		array_push($labels, $rr['entry_date']);
	}
	//echo $labls[0];
	
	$ids = array();
	$q=mysqli_query($link,"SELECT DISTINCT `item_code`, COUNT(`item_code`) AS `sells` FROM `ph_sell_details` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate' GROUP BY `item_code` HAVING `sells`>0 ORDER BY `sells` DESC LIMIT 0,$limits");
	while($r=$q->fetch_assoc())
	{
		array_push($ids, $r['item_code']);
	}
	//print_r($ids);
	
	$datasets=array();
	$label=array();
	for($i=0; $i<7; $i++)
	{
	$events = array();
	$e = array();
	$id=$ids[$i];
	//echo "SELECT `item_name` FROM `item_master` WHERE `item_id`='$id'<br/>";
	if($id)
	{
	$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$id'"));
	//$e['id'] = $ids[$i];
	$label['label'] = $itm['item_name'];
	array_push($datasets, $label);
	for($j=0; $j<7; $j++)
	{
		$entry_date=$labels[$j];
		if($id)
		{
		$sell=mysqli_fetch_assoc(mysqli_query($link,"SELECT IFNULL(SUM(`sale_qnt`),0) AS `sells` FROM `ph_sell_details` WHERE `entry_date`='$entry_date' AND `item_code`='$id'"));
		$e['data'] = $sell['sells'];
		
		array_push($events, $e);
		}
	}
	array_push($datasets, $events);
	}
	}
	
	//echo json_encode($labels)."@@";
	echo json_encode($datasets);
}

if($type==8)
{
	$user=$_POST['user'];
	$prev_date=date('Y-m-d', strtotime('-30 days'));
	//$qry="SELECT DISTINCT a.`item_id`,b.`item_name`,b.`rack_no`,b.`strip_quantity` FROM `inv_maincurrent_stock` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND a.`closing`>'0' ORDER BY b.`item_name`";
	$qry="SELECT DISTINCT a.`item_id`,b.`item_name`,b.`rack_no`,b.`strip_quantity` FROM `inv_maincurrent_stock` a, `item_master` b WHERE a.`item_id`=b.`item_id` ORDER BY b.`item_name`";
	if(in_array($user,$user_access))
	{
	?>
	<button type="button" class="btn btn-primary noprint" onclick="stock_analysis_print()">Print</button>
	<?php
	}
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Issued Qty (Last 30 days)</th>
			<th>Purchased Qty (Last Bill)</th>
			<th>Expected Qty (Next 30 days)</th>
			<th>Current Stock</th>
			<th>Rack No</th>
		</tr>
		<?php
		$j=1;
		$q=mysqli_query($link,$qry);
		while($r=mysqli_fetch_assoc($q))
		{
			$isu=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(a.`issue_qnt`),0) AS `issue_qnt` FROM `ph_stock_transfer_details` a, `ph_stock_transfer_master` b WHERE a.`issue_no`=b.`issue_no` AND a.`item_id`='$r[item_id]' AND b.`date` BETWEEN '$prev_date' AND '$date'"));
			//$rcv=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recpt_quantity` FROM `inv_main_stock_received_detail` WHERE `item_id`='$r[item_id]' ORDER BY `slno` DESC LIMIT 0,1"));
			$rcv=mysqli_fetch_assoc(mysqli_query($link,"SELECT a.`recpt_quantity` FROM `inv_main_stock_received_detail` a, `inv_main_stock_received_master` b WHERE a.`rcv_no`=b.`receipt_no` AND b.`supp_code`>'0' AND a.`item_id`='$r[item_id]' ORDER BY a.`slno` DESC LIMIT 0,1"));
			if(!$rcv)
			{
				$rcv['recpt_quantity']=0;
			}
			$per_day_qnt=(($isu['issue_qnt']/$r['strip_quantity'])/30);
			$exp_qnt_new=($per_day_qnt*30);
			$exp_next_50=$exp_qnt_new/2;
			$exp_qnt=($exp_qnt_new+$exp_next_50);
			$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT SUM(`closing`) AS `stk` FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_id]' AND `closing`>'0'"));
			$style="color:#000000;";
			if(round($stk['stk']/$r['strip_quantity'])<=round($isu['issue_qnt']/$r['strip_quantity']))
			{
				$style="color:#0000FF;";
			}
			else
			{
				if(round($stk['stk']/$r['strip_quantity'])>=$exp_qnt)
				{
					$style="color:#F10806;";
				}
				else
				{
					$style="color:#056913;";
				}
			}
		?>
		<tr style="<?php echo $style;?>">
			<td><?php echo $j;?></td>
			<td><?php echo $r['item_id'];?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo round($isu['issue_qnt']/$r['strip_quantity']);?></td>
			<td><?php echo round($rcv['recpt_quantity']/$r['strip_quantity']);?></td>
			<td><?php echo round($exp_qnt);?></td>
			<td><?php echo round($stk['stk']/$r['strip_quantity']);?></td>
			<td><?php echo $r['rack_no'];?></td>
		</tr>
		<?php
		$j++;
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
