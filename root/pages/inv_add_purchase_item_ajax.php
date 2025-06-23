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

function text_query($txt)
{
	if($txt)
	{
		$myfile = file_put_contents('../../log/purchase_edit.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
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
	$q=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_master` WHERE `supp_code`='$supp' AND `recpt_date` BETWEEN '$fdate' AND '$tdate'");
	while($r=mysqli_fetch_assoc($q))
	{
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td style="text-align:right;"><?php echo number_format($r['net_amt'],2);?></td>
			<td>
				<button type="button" class="btn btn-info btn-mini" onclick="view_bill('<?php echo $r['order_no'];?>','<?php echo $r['receipt_no'];?>')">View</button>
			</td>
			<td>
				<button type="button" class="btn btn-success btn-mini" onclick="add_to_bill('<?php echo $r['receipt_no'];?>')">Add</button>
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
	$q=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `rcv_no`='$rcv'");
	while($r=mysqli_fetch_assoc($q))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$gst=explode(".",$r['gst_per']);
		$gst=$gst[0];
		$mrp=$r['recpt_mrp'];
		$cost=$r['recept_cost_price'];
		$disc=explode(".",$r['dis_per']);
		$disc=$disc[0];
		?>
		<tr class="all_tr">
			<td><?php echo $j;?></td>
			<td>
				<?php echo $itm['item_name'];?>
				<input type='hidden' value="<?php echo $r['item_id'];?>" class='itm_id'/>
				<input type='hidden' value="<?php echo $r['item_id'].$r['recept_batch'];?>" class='test_id'/>
			</td>
			<td>
				<?php echo $r['recept_batch'];?>
				<input type='hidden' value="<?php echo $r['recept_batch'];?>" class='bch' />
			</td>
			<td>
				<?php echo date("Y-m", strtotime($r['expiry_date']));?>
				<input type='hidden' value="<?php echo date("Y-m", strtotime($r['expiry_date']));?>" class='exp_dt' />
			</td>
			<td style="text-align:right;">
				<?php echo $r['recpt_quantity']/$itm['strip_quantity'];?>
				<input type='hidden' value="<?php echo $r['recpt_quantity']/$itm['strip_quantity'];?>" class='qnt' />
			</td>
			<td style="text-align:right;">
				<?php echo $r['free_qnt']/$itm['strip_quantity'];?>
				<input type='hidden' value="<?php echo $r['free_qnt']/$itm['strip_quantity'];?>" class='free' />
			</td>
			<td style="text-align:right;">
				<?php echo $gst;?>
				<input type="hidden" class="gst" value="<?php echo $gst;?>" /><input type="hidden" class="all_gst" value="<?php echo $r['gst_amount'];?>" />
			</td>
			<td style="text-align:right;">
				<?php echo $itm['strip_quantity'];?>
				<input type="hidden" value="<?php echo $itm['strip_quantity'];?>" />
			</td>
			<td style="text-align:right;">
				<?php echo $mrp;?>
				<input type="hidden" class="mrp" value="<?php echo $mrp;?>" />
			</td>
			<td style="text-align:right;">
				<?php echo $cost;?>
				<input type="hidden" class="cost" value="<?php echo $cost;?>" />
			</td>
			<td style="text-align:right;">
				<?php echo $disc;?>
				<input type="hidden" class="disc" value="<?php echo $disc;?>" />
			</td>
			<td style="text-align:right;">
				<?php echo $r['item_amount'];?>
				<input type="hidden" class="all_rate" value="<?php echo $r['item_amount'];?>" />
			</td>
			<td style="text-align:center;">
				<!--<span onclick="$(this).parent().parent().remove();set_sl();set_amt()" style="cursor:pointer;color:#c00;"><i class="icon-remove icon-large text-danger"></i></span>-->
			</td>
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
	$all=$_POST['all']; // old
	$olds=$_POST['olds'];
	$btn_val=$_POST['btn_val'];
	$entrydate=$date;
	$rad=0;
	$adj=0;
	$all=$olds;
	$ph=1;
	$bill_no=mysqli_fetch_assoc(mysqli_query($link,"SELECT `supp_code`,`bill_no` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$rcv_no'"));
	$supp_code=$bill_no['supp_code'];
	$bill_no=$bill_no['bill_no'];
	
	$dbs=array();
	$q=mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `order_no`='$rcv_no'");
	while($r=mysqli_fetch_assoc($q))
	{
		$temp=$r['item_id']."@@".$r['recept_batch'];
		array_push($dbs, $temp); // old items
	}
	
	$new=array();
	$rem=array();
	$old=explode("#%#",$olds);
	foreach($old as $o)
	{
		if($o)
		{
			$v=explode("@@",$o);
			$itm=$v[0];
			$bch=$v[1];
			$temp=$itm."@@".$bch;
			if(!in_array($temp, $dbs))
			{
				array_push($new, $temp); // new items
			}
			else
			{
				array_push($rem, $temp);
			}
		}
	}
	
	$less=array();
	$remv=array_diff($dbs,$rem); // removed items
	foreach($remv as $rm)
	{
		$t=explode("@@",$rm);
		$itm=$t[0];
		$bch=$t[1];
		
		$stock_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'"));
		$entry_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recpt_quantity`,`free_qnt` FROM `ph_purchase_receipt_details` WHERE `order_no`='$rcv_no' AND `item_code`='$itm' AND `recept_batch`='$bch'"));
		
		$qnt=$entry_qnt['recpt_quantity']+$entry_qnt['free_qnt'];
		
		if($stock_qnt['quantity'] < $qnt)
		{
			array_push($less, $rm);
		}
	}
	$less=array();
	if(sizeof($less)>0)
	{
		echo "less@penguin@".$less;
	}
	else
	{
		//------------------------------------removed items---------------------
		$txt="\n=============================================================================================";
		foreach($remv as $rm)
		{
			$t=explode("@@",$rm);
			$itm=$t[0];
			$bch=$t[1];
			
			$entry_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno`,`recpt_quantity`,`free_qnt` FROM `ph_purchase_receipt_details` WHERE `order_no`='$rcv_no' AND `item_code`='$itm' AND `recept_batch`='$bch'"));
			$txt.="\nSELECT `slno`,`recpt_quantity`,`free_qnt` FROM `ph_purchase_receipt_details` WHERE `order_no`='$rcv_no' AND `item_code`='$itm' AND `recept_batch`='$bch'";
			
			$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
			if($stk)
			{
				$txt.="\nselect * from ph_stock_process where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1";
				$qnt=($entry_qnt['recpt_quantity']+$entry_qnt['free_qnt']);
				$closing=($stk['s_remain']-$qnt);
				$return_supplier=$stk['return_supplier']+$qnt;
				mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$qnt','$closing','10','$date','$time','$user')");
				$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$qnt','$closing','10','$date','$time','$user')";
				mysqli_query($link,"update ph_stock_process set s_remain='$closing',return_supplier='$return_supplier' where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
				$txt.="\nupdate ph_stock_process set s_remain='$closing',return_supplier='$return_supplier' where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'";
				mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
				$txt.="\nupdate ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
			}
			else
			{
				$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
				$txt.="\nselect * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1";
				$qnt=($entry_qnt['recpt_quantity']+$entry_qnt['free_qnt']);
				$closing=($stk['s_remain']-$qnt);
				$sell=$stk['sell']+$qnt;
				mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$qnt','$closing','10','$date','$time','$user')");
				$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$qnt','$closing','10','$date','$time','$user')";
				mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_supplier,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','0','0','$qnt','$closing','$date')");
				$txt.="\ninsert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_supplier,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','0','0','$qnt','$closing','$date')";
				mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
				$txt.="\nupdate ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
			}
			mysqli_query($link,"DELETE FROM `ph_purchase_receipt_details` WHERE `slno`='$entry_qnt[slno]'");
			$txt.="\nDELETE FROM `ph_purchase_receipt_details` WHERE `slno`='$entry_qnt[slno]'";
		}
		//------------------------removed items end----------------------
		//--------------------------add new items----------------------
		$old=explode("#%#",$olds);
		foreach($old as $a)
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
			$qnt=$qnt*$pkd_qnt;
			$free=$free*$pkd_qnt;
			$tmp="";
			if($itm && $bch)
			{
				$tmp=$itm."@@".$bch;
				if(in_array($tmp, $new)) // if exists
				{
					$process_type=0;
					$vqnt=$qnt+$free;
					$entry_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_details` WHERE `order_no`='$rcv_no' AND `item_code`='$itm' AND `recept_batch`='$bch'"));
					$txt.="\nSELECT * FROM `ph_purchase_receipt_details` WHERE `order_no`='$rcv_no' AND `item_code`='$itm' AND `recept_batch`='$bch'";
					$ent_qnt=$entry_qnt['recpt_quantity']+$entry_qnt['free_qnt'];
					if($entry_qnt)
					{
						if($entry_qnt['recpt_quantity']!=$qnt || $entry_qnt['free_qnt']!=$free)
						{
							if($vqnt>$ent_qnt)
							{
								$process_type=9; // add
							}
							else if($vqnt<$ent_qnt)
							{
								$process_type=10; // deduct
							}
							else
							{
								$process_type=0; // no operation
							}
							if($process_type)
							{
								$txt.="\n----".$process_type;
								$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
								$txt.="\nselect * from ph_stock_process where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1";
								if($stk)
								{
									if($process_type==9)
									{
										$closing=($stk['s_remain']+$vqnt);
										$added=$stk['added']+$vqnt;
										$return_supplier=$stk['return_supplier'];
									}
									if($process_type==10)
									{
										$closing=($stk['s_remain']-$vqnt);
										$added=$stk['added'];
										$return_supplier=$stk['return_supplier']+$vqnt;
									}
									mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$vqnt','$closing','$process_type','$date','$time','$user')");
									$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$vqnt','$closing','$process_type','$date','$time','$user')";
									mysqli_query($link,"update ph_stock_process set added='$added',return_supplier='$return_supplier',s_remain='$closing' where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
									$txt.="\nupdate ph_stock_process set added='$added',return_supplier='$return_supplier',s_remain='$closing' where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'";
									mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
									$txt.="\nupdate ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
								}
								else
								{
									$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
									$txt.="\nselect * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1";
									if($process_type==9)
									{
										$closing=($stk['s_remain']+$vqnt);
										$added=$stk['added']+$vqnt;
										$return_supplier=$stk['return_supplier'];
									}
									if($process_type==10)
									{
										$closing=($stk['s_remain']-$vqnt);
										$added=$stk['added'];
										$return_supplier=$stk['return_supplier']+$vqnt;
									}
									mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$vqnt','$closing','$process_type','$date','$time','$user')");
									$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$vqnt','$closing','$process_type','$date','$time','$user')";
									mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_supplier,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','0','0','$return_supplier','$closing','$date')");
									$txt.="\ninsert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_supplier,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','0','0','$return_supplier','$closing','$date')";
									mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
									$txt.="\nupdate ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
								}
							}
							mysqli_query($link,"UPDATE `ph_purchase_receipt_details` SET `expiry_date`='$exp_dt',`recpt_quantity`='$qnt',`free_qnt`='$free',`recpt_mrp`='$mrp',`recept_cost_price`='$unit_cost',`sale_price`='$unit_sale',`item_amount`='$itm_amt',`dis_per`='$dis_per',`dis_amt`='$dis_amt',`gst_per`='$gst',`gst_amount`='$gst_amt' WHERE `slno`='$entry_qnt[slno]'");
							$txt.="\nUPDATE `ph_purchase_receipt_details` SET `expiry_date`='$exp_dt',`recpt_quantity`='$qnt',`free_qnt`='$free',`recpt_mrp`='$mrp',`recept_cost_price`='$unit_cost',`sale_price`='$unit_sale',`item_amount`='$itm_amt',`dis_per`='$dis_per',`dis_amt`='$dis_amt',`gst_per`='$gst',`gst_amount`='$gst_amt' WHERE `slno`='$entry_qnt[slno]'+";
						}
						else
						{
							mysqli_query($link,"UPDATE `ph_purchase_receipt_details` SET `expiry_date`='$exp_dt',`recpt_mrp`='$mrp',`recept_cost_price`='$unit_cost',`sale_price`='$unit_sale',`item_amount`='$itm_amt',`dis_per`='$dis_per',`dis_amt`='$dis_amt',`gst_per`='$gst',`gst_amount`='$gst_amt' WHERE `slno`='$entry_qnt[slno]'");
							$txt.="\nUPDATE `ph_purchase_receipt_details` SET `expiry_date`='$exp_dt',`recpt_mrp`='$mrp',`recept_cost_price`='$unit_cost',`sale_price`='$unit_sale',`item_amount`='$itm_amt',`dis_per`='$dis_per',`dis_amt`='$dis_amt',`gst_per`='$gst',`gst_amount`='$gst_amt' WHERE `slno`='$entry_qnt[slno]'-";
						}
					}
					else // add new items
					{
						$txt.="\n----------------------------------------new-------------------------------------";
						mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$rcv_no','$bill_no','$itm','','$exp_dt','$date','$qnt','$free','$bch','$supp_code','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
						$txt.="\nINSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$rcv_no','$bill_no','$itm','','$exp_dt','$date','$qnt','$free','$bch','$supp_code','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')";
						
						$vqnt=$qnt+$free;
						$vst=0;
						mysqli_query($link,"UPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
						$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
						if($stk)
						{
							$txt.="\nselect * from ph_stock_process where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1";
							$closing=($stk['s_remain']+$vqnt);
							$added=$stk['added']+$vqnt;
							mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$vqnt','$closing','9','$date','$time','$user')");
							$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$vqnt','$closing','9','$date','$time','$user')";
							mysqli_query($link,"update ph_stock_process set added='$added',s_remain='$closing' where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
							$txt.="\nupdate ph_stock_process set added='$added',s_remain='$closing' where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'";
							mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
							$txt.="\nupdate ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
						}
						else///for if data not found
						{
							$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
							$txt.="\nselect * from ph_stock_process where item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1";
							$closing=($stk['s_remain']+$vqnt);
							$added=$vqnt;
							mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$vqnt','$closing','9','$date','$time','$user')");
							$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$vqnt','$closing','9','$date','$time','$user')";
							mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_supplier,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$added','0','0','$closing','$date')");
							$txt.="\ninsert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,return_supplier,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','$added','0','0','$closing','$date')";
							$stock_master=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$ph'"));
							if($stock_master)
							{
								mysqli_query($link,"update ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
								$txt.="\nupdate ph_stock_master set quantity='$closing' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
							}
							else
							{
								mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`, `item_code`, `batch_no`, `quantity`, `mfc_date`, `exp_date`) VALUES ('$ph','$itm','$bch','$closing','','$exp_dt')");
								$txt.="\nINSERT INTO `ph_stock_master`(`substore_id`, `item_code`, `batch_no`, `quantity`, `mfc_date`, `exp_date`) VALUES ('$ph','$itm','$bch','$closing','','$exp_dt')";
							}
						}
					}
					//--------------------------add new items end----------------------
				}
			}
		}
		$bill_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(`item_amount`),0) AS `item_amount`, ifnull(SUM(`dis_amt`),0) AS `dis_amt`, ifnull(SUM(`gst_amount`),0) AS `gst_amount` FROM `ph_purchase_receipt_details` WHERE `order_no`='$rcv_no'"));
		$txt.="\n--------------- ------------------ ----------------";
		$txt.="\nSELECT ifnull(SUM(`item_amount`),0) AS `item_amount`, ifnull(SUM(`dis_amt`),0) AS `dis_amt`, ifnull(SUM(`gst_amount`),0) AS `gst_amount` FROM `ph_purchase_receipt_details` WHERE `order_no`='$rcv_no'";
		$net_amt=$bill_det['item_amount']+$bill_det['gst_amount'];
		
		$prev_check=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_amount`,`gst_amt`,`dis_amt`,`net_amt` FROM `ph_purchase_receipt_master` WHERE `order_no`='$rcv_no'"));
		if($prev_check['bill_amount']!=$bill_det['item_amount'] || $prev_check['gst_amt']!=$bill_det['gst_amount'] || $prev_check['dis_amt']!=$bill_det['dis_amt'] || $prev_check['net_amt']!=$net_amt)
		{
			$supp_balance=mysqli_fetch_assoc(mysqli_query($link,"SELECT `balance`,`balance_amt` FROM `inv_supplier_transaction` WHERE `supp_code`='$supp_code' ORDER BY `slno` DESC LIMIT 0,1"));
			if($net_amt>$prev_check['net_amt']) // add
			{
				$debit_amt=$net_amt-$prev_check['net_amt'];
				$new_balance=$supp_balance['balance_amt']+$debit_amt;
				mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `adjust_type`, `balance`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$bill_no','$supp_code','$debit_amt','0','0','0','$supp_balance[balance]','$new_balance','8','$date','$time','$user')");
			}
			if($net_amt<$prev_check['net_amt']) // deduct
			{
				$credit_amt=$prev_check['net_amt']-$net_amt;
				$new_balance=$supp_balance['balance_amt']-$credit_amt;
				mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `adjust_type`, `balance`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$bill_no','$supp_code','0','$credit_amt','0','0','$supp_balance[balance]','$new_balance','7','$date','$time','$user')");
			}
		}
		mysqli_query($link,"UPDATE `ph_purchase_receipt_master` SET `bill_amount`='$bill_det[item_amount]', `gst_amt`='$bill_det[gst_amount]', `dis_amt`='$bill_det[dis_amt]', `net_amt`='$net_amt' WHERE `order_no`='$rcv_no'");
		$txt.="\nUPDATE `ph_purchase_receipt_master` SET `bill_amount`='$bill_det[item_amount]', `gst_amt`='$bill_det[gst_amount]', `dis_amt`='$bill_det[dis_amt]', `net_amt`='$net_amt' WHERE `order_no`='$rcv_no'";
		text_query($txt);
		echo "ok@penguin@Done";
	}
	
	//print_r($new);
}

if($type==4)
{
	$rcv_no=$_POST['rcv_no'];
	$bildate=$_POST['billdate'];
	$billamt=$_POST['bill_amt'];
	$total=$_POST['total'];
	$disamt=$_POST['discount'];
	$gstamt=$_POST['all_gst'];
	$ttlamt=$_POST['net_amt'];
	$userid=$_POST['user'];
	$all=$_POST['all'];
	$btn_val=$_POST['btn_val'];
	$entrydate=$date;
	$rad=0;
	$adj=0;
	
	$ord=mysqli_fetch_assoc(mysqli_query($link,"SELECT `receipt_no`,`supp_code`,`bill_no` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$rcv_no'"));
	if($ord)
	{
		$orderno=$ord['receipt_no'];
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
			$qnt=$qnt*$pkd_qnt;
			$free=$free*$pkd_qnt;
			if($itm && $bch)
			{
				mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$orderno','$splrblno','$itm','','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
					
				$vqnt=$qnt+$free;
				$vst=0;
				mysqli_query($link,"UPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
				$vstkqnt=0;
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc "));
				if($qrstkmaster['item_id']!='')
				{
					$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
					$rcvqnt=$qrstkmaster['recv_qnty']+$vqnt;
					mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where date='$date' and item_id='$itm' and batch_no='$bch'");
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
					$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
					mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$qrstkmaster[closing_qnty]','$vqnt',0,'$vstkqnt')");
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
				}
				mysqli_query($link,"INSERT INTO `inv_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('0','$orderno','$itm','$bch','$qrstkmaster[closing_qnty]','$vqnt','$vstkqnt','9','$date','$time','$user')");
			}
		}
		mysqli_query($link,"UPDATE `inv_main_stock_received_master` SET `gst_amt`='$gstamt',`dis_amt`='$disamt',`net_amt`='$ttlamt' WHERE `receipt_no`='$orderno' AND `supp_code`='$spplrid' AND `bill_no`='$splrblno'");
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
