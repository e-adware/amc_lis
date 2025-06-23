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

function convert_date_only_sm_year($date)
{
	if($date)
	{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
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
	$supp=$_POST['supp'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	?>
	<table class="table table-condensed">
		<tr>
			<th>#</th>
			<th>Bill Date</th>
			<th style="text-align:right;">Amount</th>
			<th>View</th>
			<th>Add Items</th>
		</tr>
	<?php
	$j=1;
	$tot=0;
	$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE `supp_code`='$supp' AND `recpt_date` BETWEEN '$fdate' AND '$tdate'");
	while($r=mysqli_fetch_assoc($q))
	{
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td style="text-align:right;"><?php echo number_format($r['net_amt'],2);?></td>
			<td>
				<button type="button" class="btn btn-info btn-mini" onclick="view_bill('<?php echo $r['order_no'];?>')">View</button>
			</td>
			<td>
				<button type="button" class="btn btn-success btn-mini" onclick="add_to_bill('<?php echo $r['order_no'];?>')">Add</button>
			</td>
		</tr>
		<?php
		$tot+=$r['net_amt'];
		$j++;
	}
	?>
		<tr>
			<th></th>
			<th>Total :</th>
			<th style="text-align:right;"><?php echo number_format($tot,2);?></th>
			<th colspan="2" style="text-align:right;"></th>
		</tr>
	</table>
	<?php
}

if($type==2)
{
	$rcv=$_POST['rcv'];
	
	?>
	<table class='table table-condensed table-bordered table-report' id='mytable'>
		<tr>
			<th width='5%'>#</th>
			<th>Description</th>
			<th>Batch</th>
			<th>Expiry</th>
			<th style='text-align:right;'>Quantity</th>
			<th style='text-align:right;'>Free</th>
			<th style='text-align:right;'>GST %</th>
			<th style='text-align:right;'>Pkd Qnt</th>
			<th style='text-align:right;'>Strip MRP</th>
			<th style='text-align:right;'>Strip Cost</th>
			<th style='text-align:right;'>Discount %</th>
			<th style='text-align:right;'>Amount</th>
			<th width='5%'>Remove</th>
		</tr>
	<?php
	$j=1;
	$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_details` WHERE `order_no`='$rcv'");
	while($r=mysqli_fetch_assoc($q))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
		$gst=explode(".",$r['gst_per']);
		$gst=$gst[0];
		$mrp=($r['recpt_mrp']*$r['strip_quantity']);
		$cost=$r['cost_price'];
		$disc=explode(".",$r['dis_per']);
		$disc=$disc[0];
		?>
		<tr class="all_tr">
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['recept_batch'];?></td>
			<td><?php echo date("Y-m", strtotime($r['expiry_date']));?></td>
			<td style="text-align:right;"><?php echo $r['recpt_quantity']/$r['strip_quantity'];?></td>
			<td style="text-align:right;"><?php echo $r['free_qnt']/$r['strip_quantity'];?></td>
			<td style="text-align:right;"><input type="hidden" class="gst" value="<?php echo $gst;?>" /><input type="hidden" class="all_gst" value="<?php echo $r['gst_amount'];?>" /><?php echo $gst;?></td>
			<td style="text-align:right;"><input type="hidden" value="<?php echo $r['strip_quantity'];?>" /><?php echo $r['strip_quantity'];?></td>
			<td style="text-align:right;"><input type="hidden" class="mrp" value="<?php echo $mrp;?>" /><?php echo number_format($mrp,2);?></td>
			<td style="text-align:right;"><input type="hidden" class="cost" value="<?php echo $cost;?>" /><?php echo $cost;?></td>
			<td style="text-align:right;"><input type="hidden" class="disc" value="<?php echo $disc;?>" /><?php echo $disc;?></td>
			<td style="text-align:right;"><input type="hidden" class="all_rate" value="<?php echo $r['item_amount'];?>" /><?php echo number_format($r['item_amount'],2);?></td>
			<td style="text-align:center;"></td>
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
	$rcv_no=$_POST['rcv_no'];
	$bildate=$_POST['billdate'];
	$billamt=$_POST['bill_amt'];
	$total=$_POST['total'];
	$disamt=$_POST['discount'];
	$gstamt=$_POST['all_gst'];
	$ttlamt=$_POST['net_amt'];
	$user=$userid=$_POST['user'];
	$all=$_POST['all'];
	$btn_val=$_POST['btn_val'];
	$entrydate=$date;
	$rad=0;
	$adj=0;
	$branch_id=1;
	$substore_id=1;
	
	$ord=mysqli_fetch_assoc(mysqli_query($link,"SELECT `supp_code`,`bill_no` FROM `ph_purchase_receipt_master` WHERE `order_no`='$rcv_no'"));
	if($ord)
	{
		$orderno=$rcv_no;
		$spplrid=$ord['supp_code'];
		$splrblno=$ord['bill_no'];
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@@",$a);
			$itm=$v[0];
			$bch=$v[1];
			$exp_dt=$v[2];
			$exp_dt.="-01";
			$exp_dt=date('Y-m-t', strtotime($exp_dt));
			$qnt=$v[3];
			$free=$v[4];
			$gst=$v[5];
			$gst_amt=$v[6];
			$pkd_qnt=$v[7];
			$strip_mrp=$v[8];
			$mrp=$strip_mrp/$pkd_qnt;
			$unit_sale=$v[9];
			$strip_cost=$v[10];
			$unit_cost=$v[11];
			$dis_per=$v[12];
			$dis_amt=$v[13];
			$itm_amt=$v[14];
			$hsn=mysqli_real_escape_string($link,$v[15]);
			$rack_no=mysqli_real_escape_string($link,$v[16]);
			$qnt=$qnt*$pkd_qnt;
			$free=$free*$pkd_qnt;
			if($itm && $bch)
			{
				mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `strip_quantity`, `recpt_mrp`, `cost_price`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','0000-00-00','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$pkd_qnt','$mrp','$strip_cost','$unit_cost','$unit_sale','0','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
				$txt.="\nINSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `strip_quantity`, `recpt_mrp`, `cost_price`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','0000-00-00','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$pkd_qnt','$mrp','$strip_cost','$unit_cost','$unit_sale','0','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt');";
				
				mysqli_query($link,"UPDATE `item_master` SET `hsn_code`='$hsn',`rack_no`='$rack_no',`gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
				
				$vqnt=$qnt+$free;
				$vst=0;
				$vstkqnt=0;
				
				$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and  batch_no='$bch' AND `substore_id`='$substore_id' order by date desc"));
				if($last_stock) // last stock of current date
				{
					$txt.="\nSELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and batch_no='$bch' AND `substore_id`='$substore_id' order by date desc;";
					$opening=$last_stock['s_remain'];
					if(!$opening){$opening=0;}
					$add_qnt=$last_stock['added']+$vqnt;
					$close_qnt=$opening+$vqnt;
					
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$orderno','$itm','$bch','$opening','$vqnt','$close_qnt','1','$date','$time','$user')");
					$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$orderno','$itm','$bch','$opening','$vqnt','$close_qnt','1','$date','$time','$user')";
				
					mysqli_query($link,"UPDATE `ph_stock_process` SET `added`='$add_qnt',`s_remain`='$close_qnt' WHERE `date`='$date' AND `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
					$txt.="\nUPDATE `ph_stock_process` SET `added`='$add_qnt',`s_remain`='$close_qnt' WHERE `date`='$date' AND `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id';";
					
					$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'"));
					if($stk)
					{
						mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`='$close_qnt' WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
					}
					else
					{
						mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$exp_dt')");
					}
				}
				else // last stock desc
				{
					$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * from `ph_stock_process` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id' ORDER BY `date` DESC LIMIT 0,1"));
					$txt.="\nSELECT * from `ph_stock_process` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id' ORDER BY `date` DESC LIMIT 0,1;";
					
					$opening=$last_stock['s_remain'];
					if(!$opening){$opening=0;}
					$close_qnt=$opening+$vqnt;
					
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$orderno','$itm','$bch','$opening','$vqnt','$close_qnt','1','$date','$time','$user')");
					$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$orderno','$itm','$bch','$opening','$vqnt','$close_qnt','1','$date','$time','$user')";
				
					mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substore_id','$orderno','$itm','$bch','$opening','$vqnt',0,0,0,'$close_qnt','$date')");
					$txt.="\nINSERT INTO `ph_stock_process`(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substore_id','$orderno','$itm','$bch','$opening','$vqnt',0,0,0,'$close_qnt','$date');";
					
					if($stk)
					{
						mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`='$close_qnt' WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
					}
					else
					{
						mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$exp_dt')");
					}
				}
			}
		}
		mysqli_query($link,"UPDATE `ph_purchase_receipt_master` SET `bill_amount`='$total', `gst_amt`='$gstamt',`dis_amt`='$disamt',`net_amt`='$ttlamt' WHERE `branch_id`='$branch_id' AND `order_no`='$orderno' AND `supp_code`='$spplrid' AND `bill_no`='$splrblno'");
		mysqli_query($link,"UPDATE `ph_supplier_transaction` SET `debit_amt`='$ttlamt',`balance_amt`='$ttlamt' WHERE `branch_id`='$branch_id' AND `process_no`='$orderno' AND `supp_code`='$spplrid'");
		echo "Item Added";
	}
	else
	{
		echo "Error";
	}
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
