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

function text_query($txt,$file)
{
	if($txt)
	{
		$myfile = file_put_contents('../../log/'.$file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}

$type=$_POST['type'];
$branch_id=1;

if($type==1)
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	if($val)
	{
		$q="select distinct b.`item_id` from `item_master` a, `inv_maincurrent_stock` b where";
		$q.=" a.item_id=b.item_id and b.`branch_id`='$branch_id' and b.closing>0 and a.item_name!='' and a.item_name like '$val%'";
		$q.=" order by a.item_name limit 0,30";
	}
	else
	{
		$q="select distinct b.`item_id` from `item_master` a, `inv_maincurrent_stock` b where";
		$q.=" a.item_id=b.item_id and b.`branch_id`='$branch_id' and b.closing>0 and a.item_name!=''";
		$q.=" order by a.item_name limit 0,30";
	}
	//echo $q;
	$d=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Code</th><th>Item Name</th><th>Stock</th><th>Box No</th>
		</tr>
	<?php
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `item_master` WHERE `item_id`='$d1[item_id]'"));
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`closing`),0) AS stock FROM `inv_maincurrent_stock` WHERE `branch_id`='$branch_id' AND `item_id`='$d1[item_id]'"));
		$gst=explode(".",$itm['gst']);
		$gst=$gst[0];
		?>
		<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>','<?php echo $gst;?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
			<td><?php echo $itm['item_id'];?></td>
			<td><?php echo $itm['item_name'];?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name']."#".$gst;?>
				</div>
			</td>
			<td><?php echo $stk['stock'];?></td>
			<td><?php echo $itm['rack_no'];?></td>
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
	$itm=$_POST['itm'];
	$val="";
	$q=mysqli_query($link,"SELECT `batch_no` FROM `inv_maincurrent_stock` WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `closing`>0 ORDER BY `exp_date`");
	while($r=mysqli_fetch_assoc($q))
	{
		if($val)
		{
			$val.="@@".$r['batch_no'];
		}
		else
		{
			$val=$r['batch_no'];
		}
	}
	echo $val;
}

if($type==3)
{
	$itmid=$_POST['itmid'];
	//$itmid1=explode("-#",$itmid);
	$batchno=$_POST['batchno'];
	//$qmrp=mysqli_fetch_array(mysqli_query($link,"select recept_cost_price from inv_main_stock_received_detail where item_id='$itmid' and recept_batch='$batchno' order by slno desc"));
	$qmrp=mysqli_fetch_array(mysqli_query($link,"SELECT a.`strip_quantity`, a.`recpt_mrp`, a.`recept_cost_price` FROM `inv_main_stock_received_detail` a, `inv_main_stock_received_master` b WHERE a.`order_no`=b.`order_no` AND b.`branch_id`='$branch_id' AND a.`item_id`='$itmid' AND a.`batch_no`='$batchno' ORDER BY a.`slno` DESC LIMIT 0,1"));
	$q=mysqli_fetch_array(mysqli_query($link,"SELECT `closing`, `exp_date` FROM `inv_maincurrent_stock` WHERE `branch_id`='$branch_id' AND `item_id`='$itmid' AND `batch_no`='$batchno'"));
	if($q['exp_date']!="0000-00-00" && $q['exp_date']!="")
	{
		$exp_date=date("Y-m", strtotime($q['exp_date']));
	}
	else
	{
		$exp_date="";
	}
	$val=$q['closing'].'@'.$qmrp['recept_cost_price'].'@'.$exp_date."@".$qmrp['recpt_mrp'];
	echo $val;
}

if($type==4)
{
	$sub_dept=$_POST['sub_dept'];
	$issue_to=mysqli_real_escape_string($link,$_POST['issue_to']);
	$issue_no=mysqli_real_escape_string($link,$_POST['issue_no']);
	$items=$_POST['items'];
	$user=$_POST['user'];
	//$c_date=date("")
	$cnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS cnt FROM `inv_substore_issue_master`"));
	$count=$cnt['cnt']+1;
	$issueno="ISU".str_pad($count, 4, 0, STR_PAD_LEFT);
	
	if($sub_dept=="1")
	{
		$ph=1;
	}
	
	if(mysqli_query($link,"INSERT INTO `inv_substore_issue_master`(`issue_no`, `req_no`, `issue_to`, `issue_num`, `substore_id`, `amount`, `gst_amount`, `user`, `date`, `time`) VALUES ('$issueno','','$issue_to','$issue_no','$sub_dept','0','0','$user','$date','$time')"))
	{
		$txt="\n-------------------------------------------------------------------------------------------------";
		$txt.="\nINSERT INTO `inv_substore_issue_master`(`issue_no`, `req_no`, `issue_to`, `issue_num`, `substore_id`, `amount`, `gst_amount`, `user`, `date`, `time`) VALUES ('$issueno','','$issue_to','$issue_no','$sub_dept','0','0','$user','$date','$time')";
		/*
		if($ph==1)
		{
			mysqli_query($link,"INSERT INTO `ph_purchase_receipt_master`(`order_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `fid`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$issueno','$date','$date','0','0','0','0','0','$issueno','0','$user','$time','0','0')");
		}
		//*/
		$tot_amount=0;
		$gst_amount=0;
		//$al=explode("#%#",$all);
		//foreach($al as $a)
		foreach($items as $v)
		{
			//$v=explode("@@",$a);
			$itm		=$v['itm'];
			$bch		=$v['bch'];
			$qnt		=$v['qnt'];
			$mrp		=$v['mrp'];
			$rate		=$v['rate'];
			$amount		=$v['amt'];
			$gst_per	=$v['gst_per'];
			$gst_amt	=$v['gst_amt'];
			$exp_dt		=$v['exp_dt'];
			$exp_dt		=$exp_dt."-01";
			$exp_dt		=date("Y-m-t",strtotime($exp_dt));
			$net		=($amount+$gst_amt);
			
			$tot_amount+=$amount;
			$gst_amount+=$gst_amt;
			if($itm && $bch && $qnt)
			{
				mysqli_query($link,"INSERT INTO `inv_substore_issue_details`(`issue_no`, `item_id`, `batch_no`, `exp_date`, `issue_qnt`, `rate`, `amount`, `gst_per`, `gst_amount`, `total_amount`, `date`, `time`) VALUES ('$issueno','$itm','$bch','$exp_dt','$qnt','$rate','$amount','$gst_per','$gst_amt','$net','$date','$time')");
				$txt.="\nINSERT INTO `inv_substore_issue_details`(`issue_no`, `item_id`, `batch_no`, `exp_date`, `issue_qnt`, `rate`, `amount`, `gst_per`, `gst_amount`, `total_amount`, `date`, `time`) VALUES ('$issueno','$itm','$bch','$exp_dt','$qnt','$rate','$amount','$gst_per','$gst_amt','$net','$date','$time')";
				
				$vqnt=$qnt;
				$vstkqnt=0;
				
				$substore_id=$sub_dept;
				$process_no=$issueno;
				$process_type=7;
				include("inv_stock_deduct.php");
				include("substore_stock_add.php");
				$process_type=1;
				$testid=0;
				include("test_count_add.php");
				
				//------------------------pharmacy stock---------------------------------------
				/*
				if($ph==1)
				{
					$substore_id=1;
					$q_rates=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recpt_mrp`,`recept_cost_price`,`sale_price` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `batch_no`='$bch' ORDER BY `slno` DESC LIMIT 0,1"));
					$txt.="\nSELECT `recpt_mrp`,`recept_cost_price`,`sale_price` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `batch_no`='$bch' ORDER BY `slno` DESC LIMIT 0,1";
					
					mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `batch_no`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$issueno','$issueno','$itm','','$exp_dt','$date','$qnt','0','$bch','0','$q_rates[recpt_mrp]','$q_rates[recept_cost_price]','$q_rates[sale_price]','0','$amount','0','0','$gst_per','$gst_amt')");
					$txt.="\nINSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `batch_no`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$issueno','$issueno','$itm','','$exp_dt','$date','$qnt','0','$bch','0','$q_rates[recpt_mrp]','$q_rates[recept_cost_price]','$q_rates[sale_price]','0','$amount','0','0','$gst_per','$gst_amt')";
					
					$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and  batch_no='$bch' order by date desc"));
					if($last_stock) // last stock of current date
					{
						$txt.="\nSELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and batch_no='$bch' order by date desc;";
						$add_qnt=$last_stock['added']+$qnt;
						$close_qnt=$last_stock['s_remain']+$qnt;
						
						mysqli_query($link,"UPDATE `ph_stock_process` SET `added`='$add_qnt',`s_remain`='$close_qnt' WHERE `date`='$date' AND `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
						$txt.="\nUPDATE `ph_stock_process` SET `added`='$add_qnt',`s_remain`='$close_qnt' WHERE `date`='$date' AND `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id';";
						
						mysqli_query($link,"DELETE FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
						$txt.="\nDELETE FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id';";
						
						mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$exp_dt')");
						$txt.="\nINSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$exp_dt');";
					}
					else // last stock desc
					{
						$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * from `ph_stock_process` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id' ORDER BY `date` DESC LIMIT 0,1"));
						$txt.="\nSELECT * from `ph_stock_process` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id' ORDER BY `date` DESC LIMIT 0,1;";
						
						$add_qnt=$last_stock['added'];
						$close_qnt=$last_stock['s_remain']+$qnt;
						
						mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substore_id','$issueno','$itm','$bch','$last_stock[s_remain]','$qnt',0,0,0,'$close_qnt','$date')");
						$txt.="\nINSERT INTO `ph_stock_process`(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substore_id','$issueno','$itm','$bch','$last_stock[s_remain]','$qnt',0,0,0,'$close_qnt','$date');";
						
						mysqli_query($link,"DELETE FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
						$txt.="\nDELETE FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id';";
						
						mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$exp_dt')");
						$txt.="\nINSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$exp_dt');";
					}
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$issueno','$itm','$bch','$qnt','7','$date','$time','$user')"); // central store issue=7
					$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$issueno','$itm','$bch','$qnt','7','$date','$time','$user')";
				}
				//*/
			}
		}
		mysqli_query($link,"UPDATE `inv_substore_issue_master` SET `amount`='$tot_amount', `gst_amount`='$gst_amount' WHERE `issue_no`='$issueno'");
		$txt.="\nUPDATE `inv_substore_issue_master` SET `amount`='$tot_amount', `gst_amount`='$gst_amount' WHERE `issue_no`='$issueno'";
		/*
		if($ph==1)
		{
			$net_amt=$tot_amount+$gst_amount;
			mysqli_query($link,"UPDATE `ph_purchase_receipt_master` SET `bill_amount`='$tot_amount', `gst_amt`='$gst_amount', `net_amt`='$net_amt' WHERE `order_no`='$issueno' AND `bill_no`='$issueno'");
			$txt.="\nUPDATE `ph_purchase_receipt_master` SET `bill_amount`='$tot_amount', `gst_amt`='$gst_amount', `net_amt`='$net_amt' WHERE `order_no`='$issueno' AND `bill_no`='$issueno'";
		}
		//*/
		text_query($txt,"item_issue.txt");
		echo "Done";
	}
	else
	{
		echo "Error";
	}
}

if($type==5)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$sbstrid=$_POST['sbstrid'];
	?>
	<button type="button" class="btn btn-success" onclick="dept_issue_summery_print_excel('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-info" onclick="dept_issue_summery_print('<?php echo $ord;?>')">Print</button>
	
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Issue Id</th>
			<th>Issue To</th>
			<th>Issue No</th>
			<th>Issue Date</th>
			<th>Department</th>
			<th>Amount</th>
			<th>GST Amount</th>
			<th>Net Amount</th>
			<th>User</th>
			<th>View</th>
		</tr>
	<?php
	$j=1;
	if($sbstrid)
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_substore_issue_master` WHERE `substore_id`='$sbstrid' AND `date` BETWEEN '$fdate' AND '$tdate'");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_substore_issue_master` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	}
	while($r=mysqli_fetch_assoc($q))
	{
		$dep=mysqli_fetch_array(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$r[substore_id]'"));
		$user=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['issue_no'];?></td>
			<td><?php echo $r['issue_to'];?></td>
			<td><?php echo $r['issue_num'];?></td>
			<td><?php echo $r['date'].' / '.convert_time($r['time']);?></td>
			<td><?php echo $dep['substore_name'];?></td>
			<td><?php echo $r['amount'];?></td>
			<td><?php echo $r['gst_amount'];?></td>
			<td><?php echo number_format($r['amount']+$r['gst_amount'],2);?></td>
			<td><?php echo $user['name'];?></td>
			<td>
				<button type="button" class="btn btn-primary btn-mini" onclick="issue_print('<?php echo $r['issue_no'];?>')"><i class="icon-print icon-large"></i> View</button>
				<button type="button" class="btn btn-primary btn-mini" onclick="issue_print_excel('<?php echo $r['issue_no'];?>')"><i class="icon-print icon-large"></i> Excel</button>
			</td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<?php
}

if($type==6)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	$val=array();
	$qry="SELECT DISTINCT b.`substore_id`,b.`substore_name` FROM `inv_substore_issue_master` a, `inv_sub_store` b WHERE a.`substore_id`=b.`substore_id` AND a.`date` BETWEEN '$fdate' AND '$tdate'";
	$q=mysqli_query($link,$qry);
	while($r=mysqli_fetch_assoc($q))
	{
		$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS amount, ifnull(SUM(`gst_amount`),0) AS gst_amount FROM `inv_substore_issue_master` WHERE `substore_id`='$r[substore_id]' AND `date` BETWEEN '$fdate' AND '$tdate'"));
		//$temp=$r['substore_id']."@".$det['amount']."@".$det['gst_amount'];
		
		$temp=array();
		$temp['dept']=$r['substore_name'];
		$temp['amount']=$det['amount'];
		$temp['gst']=$det['gst_amount'];
		array_push($val, $temp);
		
	}
	echo json_encode($val);
}

if($type==7)
{
	$dept=$_POST['dept'];
	$itm=$_POST['itm'];
	$qry="SELECT a.`batch_no`, a.`exp_date`, a.`issue_qnt`, a.`date` FROM `inv_substore_issue_details` a, `inv_substore_issue_master` b WHERE a.`issue_no`=b.`issue_no` AND b.`substore_id`='$dept' AND a.`item_id`='$itm' ORDER BY a.`slno` LIMIT 0,5";
	//echo $qry;
	$q=mysqli_query($link,$qry);
	if(mysqli_num_rows($q)>0)
	{
	?>
	<table class="table table-condensed table-bordered" style="margin-bottom:0px;">
		<tr>
			<th colspan="5" style="text-align:center;">Previous Issue Details</th>
		</tr>
		<tr>
			<th>#</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th>Quantity</th>
			<th>Issue Date</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_assoc($q))
		{
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo date("Y-m", strtotime($r['exp_date']));?></td>
			<td><?php echo $r['issue_qnt'];?></td>
			<td><?php echo convert_date($r['date']);?></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
	}
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
