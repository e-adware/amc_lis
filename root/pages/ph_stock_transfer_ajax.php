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

if($type==1)
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	$ph=1;
	$exp_date=date("Y-m-t",strtotime($date));
	if($val)
	{
		$q="select distinct b.`item_id` AS `item_code` from `item_master` a, `inv_maincurrent_stock` b where";
		$q.=" a.item_id=b.item_id and b.closing>0 and a.item_name!='' and a.item_name like '$val%' and b.`exp_date`>'$exp_date'";
		$q.=" or a.item_id=b.item_id and b.closing>0 and a.item_name!='' and a.short_name like '$val%' and b.`exp_date`>'$exp_date'";
		$q.=" or a.item_id=b.item_id and b.closing>0 and a.item_name!='' and a.item_id like '$val%' and b.`exp_date`>'$exp_date'";
		$q.=" order by a.item_name limit 0,30";
		
		//~ $q="select distinct b.`item_code` from item_master a, ph_stock_master b where";
		//~ $q.=" a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.item_name like '$val%'";
		//~ $q.=" or a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.item_id like '$val%'";
		//~ $q.=" or a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.short_name like '$val%'";
		//~ $q.=" order by a.item_name limit 0,30";
	}
	else
	{
		$q="select distinct b.`item_id` AS `item_code` from `item_master` a, `inv_maincurrent_stock` b where";
		$q.=" a.item_id=b.item_id and b.closing>0 and a.item_name!='' and b.`exp_date`>'$exp_date'";
		$q.=" order by a.item_name limit 0,30";
		
		//~ $q="select distinct b.`item_code` from item_master a, ph_stock_master b where a.item_name!='' and a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph'";
		//~ $q.=" order by a.item_name limit 0,30";
	}
	//echo $q;
	$d=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Code</th><th>Item Name</th><th>Stock</th><th>Rack No</th>
		</tr>
	<?php
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_id`,`item_name`,`item_type_id`,`gst`,`rack_no`,`strip_quantity` FROM `item_master` WHERE `item_id`='$d1[item_code]'"));
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`closing`),0) AS stock FROM `inv_maincurrent_stock` WHERE `item_id`='$d1[item_code]'"));
		//$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS stock FROM `ph_stock_master` WHERE `item_code`='$d1[item_code]' AND `substore_id`='$ph'"));
		$gst=explode(".",$itm['gst']);
		$gst=$gst[0];
		$i_type="";
		if($itm['item_type_id'])
		{
			$i_typ=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_type_name` FROM `item_type_master` WHERE `item_type_id`='$itm[item_type_id]'"));
			$i_type=" <small style='float:right;'><b><i>(".$i_typ['item_type_name'].")</i></b></small>";
		}
		?>
		<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>','<?php echo $gst;?>','<?php echo $itm['strip_quantity'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
			<td><?php echo $itm['item_id'];?></td>
			<td><?php echo $itm['item_name'].$i_type;?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name']."#".$gst."#".$itm['strip_quantity']."#";?>
				</div>
			</td>
			<td><?php echo $stk['stock']/$itm['strip_quantity'];?></td>
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
	$ph=1;
	$exp_date=date("Y-m-t",strtotime($date));
	$val="";
	$q=mysqli_query($link,"SELECT `batch_no` FROM `inv_maincurrent_stock` WHERE `item_id`='$itm' AND `closing`>0 AND `exp_date`>'$exp_date' ORDER BY `exp_date`");
	//$q=mysqli_query($link, "SELECT * FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$itm' AND `quantity`>0 ORDER BY `exp_date`");
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
	//$qmrp=mysqli_fetch_array(mysqli_query($link,"select recept_cost_price from ph_purchase_receipt_details where item_code='$itmid' and recept_batch='$batchno' order by slno desc"));
	$qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price,dis_per from inv_main_stock_received_detail where item_id='$itmid' and recept_batch='$batchno' order by slno desc limit 0,1"));
	if(!$qmrp)
	{
		//$qmrp=mysqli_fetch_array(mysqli_query($link,"select recpt_mrp,recept_cost_price,dis_per from ph_purchase_receipt_details where item_code='$itmid' and recept_batch='$batchno' order by slno desc limit 0,1"));
	}
	//$q=mysqli_fetch_array(mysqli_query($link,"select quantity,exp_date from ph_stock_master where item_code='$itmid' and batch_no='$batchno'"));
	$q=mysqli_fetch_array(mysqli_query($link,"SELECT `closing` AS `quantity`, `exp_date` FROM `inv_maincurrent_stock` WHERE `item_id`='$itmid' AND `batch_no`='$batchno'"));
	if($q['exp_date']!="0000-00-00" && $q['exp_date']!="")
	{
		$exp_date=date("Y-m", strtotime($q['exp_date']));
	}
	else
	{
		$exp_date="";
	}
	$val=$q['quantity'].'@'.$qmrp['recept_cost_price'].'@'.$exp_date.'@'.$qmrp['recpt_mrp'].'@'.number_format($qmrp['dis_per'],0);
	echo $val;
}

if($type==4)
{
	$sub_dept=$_POST['sub_dept'];
	$issue_typ=$_POST['issue_typ'];
	$bed_no=mysqli_real_escape_string($link,$_POST['bed_no']);
	$issue_to=mysqli_real_escape_string($link,$_POST['issue_to']);
	$issue_no=mysqli_real_escape_string($link,$_POST['issue_no']);
	$all=$_POST['all'];
	$user=$_POST['user'];
	//$c_date=date("")
	$cnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS cnt FROM `ph_stock_transfer_master`"));
	$count=$cnt['cnt']+1;
	$issueno="ISU".str_pad($count, 4, 0, STR_PAD_LEFT);
	//$ph=1;
	
	$less="";
	$al=explode("#%#",$all);
	foreach($al as $a)
	{
		$v=explode("@@",$a);
		$itm=$v[0];
		$bch=$v[1];
		$qnt=$v[2];
		
		$pkd=mysqli_fetch_array(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
		$qnt=($qnt*$pkd['strip_quantity']);
					
		$chk=mysqli_fetch_assoc(mysqli_query($link,"SELECT `closing` FROM `inv_maincurrent_stock` WHERE `item_id`='$itm' AND `batch_no`='$bch'"));
		if($chk['closing']<$qnt)
		{
			if($less)
			{
				$less.="@ead@".$itm.$bch;
			}
			else
			{
				$less=$itm.$bch;
			}
		}
	}
	
	if($less=="")
	{
		$txt="";
		//if(mysqli_query($link,"INSERT INTO `inv_mainstore_direct_issue_master`(`issue_no`, `issue_to`, `substore_id`, `user`, `date`, `time`) VALUES ('$issueno','$issue_to','$sub_dept','$user','$date','$time')"))
		if(mysqli_query($link,"INSERT INTO `ph_stock_transfer_master`(`issue_no`, `substore_id`, `amount`, `gst_amount`, `user`, `date`, `time`, `stat`) VALUES ('$issueno','$sub_dept','0','0','$user','$date','$time','0')"))
		{
			$tot_amount=0;
			$gst_amount=0;
			$al=explode("#%#",$all);
			foreach($al as $a)
			{
				$v=explode("@@",$a);
				$itm=$v[0];
				$bch=$v[1];
				$qnt=$v[2];
				$mrp=$v[3];
				$rate=$v[4];
				$amount=$v[5];
				$gst_per=$v[6];
				$gst_amt=$v[7];
				$exp_dt=$v[8]."-01";
				$exp_dt=date("Y-m-t",strtotime($exp_dt));
				$pack=$v[9];
				$net=$amount+$gst_amt;
				$tot_amount+=$amount;
				$gst_amount+=$gst_amt;
				
				if($itm && $bch && $qnt)
				{
					if($pack)
					{
						mysqli_query($link,"UPDATE `item_master` SET `strip_quantity`='$pack' WHERE `item_id`='$itm'");
					}
					$qmrp=mysqli_fetch_array(mysqli_query($link,"select cost_price from inv_main_stock_received_detail where item_id='$itm' and recept_batch='$bch' order by slno desc limit 0,1"));
					if(!$qmrp)
					{
						$qmrp=mysqli_fetch_array(mysqli_query($link,"select cost_price from ph_challan_receipt_details where item_id='$itm' and recept_batch='$bch' order by slno desc limit 0,1"));
					}
					$rate=$qmrp['cost_price'];
					$pkd=mysqli_fetch_array(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
					$qnt=($qnt*$pkd['strip_quantity']);
					$mrp=($mrp/$pkd['strip_quantity']);
					$rate=($rate/$pkd['strip_quantity']);
					mysqli_query($link,"INSERT INTO `ph_stock_transfer_details`(`issue_no`, `item_id`, `batch_no`, `exp_date`, `mrp`, `issue_qnt`, `rate`, `amount`, `gst_per`, `gst_amount`, `total_amount`, `date`, `time`) VALUES ('$issueno','$itm','$bch','$exp_dt','$mrp','$qnt','$rate','$amount','$gst_per','$gst_amt','$net','$date','$time')");
					
					$vqnt=$qnt;
					$vst=0;
					$vstkqnt=0;
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by date desc "));
					$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by date desc";
					if($qrstkmaster['item_id']!='')
					{
						$vstkqnt=$qrstkmaster['closing']-$vqnt;
						//$rcvqnt=$qrstkmaster['recv_qnty']+$vqnt;
						$issu_qnty=$qrstkmaster['issu_qnty']+$vqnt;
						mysqli_query($link,"update inv_mainstock_details set closing='$vstkqnt',issu_qnty='$issu_qnty' where date='$date' and item_id='$itm' and batch_no='$bch'");
						$txt.="\nupdate inv_mainstock_details set closing='$vstkqnt',issu_qnty='$issu_qnty' where date='$date' and item_id='$itm' and batch_no='$bch'";
						mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
						$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
						mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
						$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')";
					}
					else///for if data not found
					{
						$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
						$txt.="\nselect * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc";
						$vstkqnt=$qrstkmaster['closing']-$vqnt;
						$issu_qnty=$vqnt;
						mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing`) values('$itm','$bch','$date','$qrstkmaster[closing]','0','$issu_qnty','$vstkqnt')");
						$txt.="\ninsert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing`) values('$itm','$bch','$date','$qrstkmaster[closing]','0','$issu_qnty','$vstkqnt')";
						mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
						$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
						mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
						$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')";
					}
					mysqli_query($link,"INSERT INTO `inv_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('$sub_dept','$issueno','$itm','$bch','$qrstkmaster[closing]','$vqnt','$vstkqnt','7','$date','$time','$user')");
					mysqli_query($link,"DELETE FROM `ph_stock_transfer_details_temp` WHERE `issue_no`='$sub_dept' AND `item_id`='$itm' AND `batch_no`='$bch'");
					//----------------------------------------------------------------------------------------------------//
					/*
					//-------------------------------------main pharmacy stock-----------------------------------------//
					$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
					if($stk)
					{
						$vstkqnt=$stk['s_remain']-$vqnt;
						$slqnt=$stk['sell']+$vqnt;
						
						mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$issueno','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','11','$date','$time','$user')");
						$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$issueno','$itm','$bch','$qnt','11','$date','$time','$user')";
						
						mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
						
						$txt.="\nupdate ph_stock_process set s_remain='$vstkqnt',sell='$slqnt' where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'";
						
						mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
						
						$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
					}
					else // for if data not found
					{
						$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  item_code='$itm' and batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
						
						$vstkqnt=$stk['s_remain']-$vqnt;
						
						mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$issueno','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','11','$date','$time','$user')");
						$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$issueno','$itm','$bch','$stk[s_remain]','$qnt','$vstkqnt','11','$date','$time','$user')";
						
						mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','0','$vqnt','$vstkqnt','$date')");
						
						$txt.="\ninsert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$bill_no','$itm','$bch','$stk[s_remain]','0','$vqnt','$vstkqnt','$date')";
						
						mysqli_query($link,"update ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
						
						$txt.="\nupdate ph_stock_master set quantity='$vstkqnt' where item_code='$itm' and batch_no='$bch' and substore_id='$ph'";
					}
					//*/
					//------------------------sub department stock (if direct issue)-----------------------------------//
					/*
					if($ph)
					{
						$substore_id=$sub_dept;
						//$q_rates=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recpt_mrp`,`recept_cost_price`,`sale_price` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `recept_batch`='$bch' ORDER BY `slno` DESC LIMIT 0,1"));
						$q_rates=mysqli_fetch_array(mysqli_query($link,"select `recpt_mrp`,`recept_cost_price`,`sale_price` from ph_purchase_receipt_details where item_code='$itm' and recept_batch='$bch' order by slno desc"));
						$txt.="\nselect `recpt_mrp`,`recept_cost_price`,`sale_price` from ph_purchase_receipt_details where item_code='$itm' and recept_batch='$bch' order by slno desc";
						
						//mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$issueno','$issueno','$itm','','$exp_dt','$date','$qnt','0','$bch','0','$q_rates[recpt_mrp]','$q_rates[recept_cost_price]','$q_rates[sale_price]','0','$amount','0','0','$gst_per','$gst_amt')");
						//$txt.="\nINSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$issueno','$issueno','$itm','','$exp_dt','$date','$qnt','0','$bch','0','$q_rates[recpt_mrp]','$q_rates[recept_cost_price]','$q_rates[sale_price]','0','$amount','0','0','$gst_per','$gst_amt')";
						
						$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `date`='$date' and item_code='$itm' and  batch_no='$bch' order by date desc"));
						if($last_stock) // last stock of current date
						{
							$txt.="\nSELECT * FROM `ph_stock_process` WHERE `substore_id`='$substore_id' AND `date`='$date' and item_code='$itm' and batch_no='$bch' order by date desc;";
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
			mysqli_query($link,"UPDATE `ph_stock_transfer_master` SET `amount`='$tot_amount', `gst_amount`='$gst_amount' WHERE `issue_no`='$issueno'");
			$txt.="\nUPDATE `ph_stock_transfer_master` SET `amount`='$tot_amount', `gst_amount`='$gst_amount' WHERE `issue_no`='$issueno'";
			echo "1@_@".$issueno;
		}
		else
		{
			echo "0@_@0";
		}
	}
	else
	{
		echo "2@_@0@_@".$less;
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
	$issue_no=base64_decode($_POST['is_no']);
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_stock_transfer_master` WHERE `issue_no`='$issue_no'"));
	$q=mysqli_query($link,"SELECT * FROM `ph_stock_transfer_details` WHERE `issue_no`='$issue_no'");
	?>
	<table class='table table-condensed table-bordered' id='mytable'>
		<tr style='background-color:#cccccc'>
			<th>#</th>
			<th>Description</th>
			<th>Batch No</th>
			<th>Quantity</th>
			<th>Rate</th>
			<th>Amount</th>
			<th style='width:5%;'>Remove</th>
		</tr>
		<?php
		$tot=0;
		$j=1;
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		?>
		<tr class="all_tr <?php echo $r['item_id'].$r['batch_no'];?>">
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?><input type='hidden' value='<?php echo $r['item_id'];?>' class='itm' /><input type='hidden' value='<?php echo $r['item_id'].$r['batch_no'];?>' class='test_id' /></td>
			<td><?php echo $r['batch_no'];?><input type='hidden' value='<?php echo $r['batch_no'];?>' class='batch' /></td>
			<td><?php echo $r['issue_qnt'];?><input type='hidden' value='<?php echo $r['issue_qnt'];?>' class='qnt' /></td>
			<td><?php echo $r['rate'];?><input type='hidden' value='<?php echo $r['rate'];?>' class='mrp' /></td>
			<td><?php echo $r['amount'];?><input type='hidden' value='<?php echo $r['amount'];?>' class='all_rate' /><input type='hidden' value='<?php echo $r['gst_per'];?>' class='gst_per' /></td>
			<td style='text-align:center;'><input type='hidden' value='<?php echo $r['gst_amount'];?>' class='all_gst' /><input type='hidden' value='<?php echo $r['exp_date'];?>' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span></td>
		</tr>
		<?php
		$tot+=$r['amount'];
		$j++;
		}
		?>
		<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'><?php echo number_format($tot,2);?></td></tr>
	</table>
	<?php
}

if($type==8)
{
	$issue_no=base64_decode($_POST['is_no']);
	$all=$_POST['all'];
	$user=$_POST['user'];
	$al=explode("#%#",$all);
	
	$dbs=array();
	$q=mysqli_query($link,"SELECT * FROM `ph_stock_transfer_details` WHERE `issue_no`='$issue_no'");
	while($r=mysqli_fetch_assoc($q))
	{
		$temp=$r['item_id']."@@".$r['batch_no'];
		array_push($dbs, $temp); // old items
	}
	echo "DBs ";
	print_r($dbs);
	
	$new=array();
	$rem=array();
	$old=explode("#%#",$all);
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
	echo "New ";
	print_r($new);
	echo "After Remove ";
	print_r($rem);
	
	$less=array();
	$remv=array_diff($dbs,$rem); // after removed items
	$substore_id=1;
	foreach($remv as $rm)
	{
		$t=explode("@@",$rm);
		$itm=$t[0];
		$bch=$t[1];
		$qnt=$t[2];
		$stock_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `substore_id`='$substore_id' AND `item_code`='$itm' AND `batch_no`='$bch'"));
		$entry_qnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `issue_qnt` FROM `ph_stock_transfer_details` WHERE `issue_no`='$issue_no' AND `item_id`='$itm' AND `batch_no`='$bch'"));
		
		//if($qnt!=$entry_qnt['issue_qnt'])
		{
			if($stock_qnt['quantity'] + $entry_qnt['issue_qnt'] < $qnt)
			{
				array_push($less, $rm);
			}
		}
	}
	
	$dels=array_diff($dbs,$rem);
	echo "Dels ";
	print_r($dels);
	
	echo "Less ";
	print_r($less);
	if(sizeof($less)>0)
	{
		echo "less@penguin@".$less;
	}
	else
	{
		echo "ok";
	}
	
	foreach($al as $a)
	{
		$v=explode("@@",$a);
		$itm=$v[0];
		$bch=$v[1];
		$qnt=$v[2];
		$rate=$v[3];
		$amount=$v[4];
		$gst_per=$v[5];
		$gst_amt=$v[6];
		$exp_dt=$v[7]."-01";
		$exp_dt=date("Y-m-t",strtotime($exp_dt));
		$net=$amount+$gst_amt;
		$tot_amount+=$amount;
		$gst_amount+=$gst_amt;
	}
}

if($type==9)
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	$ph=1;
	if($val)
	{
		$q="select distinct b.`item_id` AS `item_code`, a.`manufacturer_id` from `item_master` a, `inv_maincurrent_stock` b where";
		$q.=" a.item_id=b.item_id and a.item_name!='' and a.item_name like '$val%'";
		$q.=" or a.item_id=b.item_id and a.item_name!='' and a.short_name like '$val%'";
		$q.=" or a.item_id=b.item_id and a.item_name!='' and a.item_id like '$val%'";
		$q.=" order by a.item_name limit 0,30";
		
		//~ $q="select distinct b.`item_code` from item_master a, ph_stock_master b where";
		//~ $q.=" a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.item_name like '$val%'";
		//~ $q.=" or a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.item_id like '$val%'";
		//~ $q.=" or a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.short_name like '$val%'";
		//~ $q.=" order by a.item_name limit 0,30";
	}
	else
	{
		$q="select distinct b.`item_id` AS `item_code`, a.`manufacturer_id` from `item_master` a, `inv_maincurrent_stock` b where";
		$q.=" a.item_id=b.item_id and a.item_name!=''";
		$q.=" order by a.item_name limit 0,30";
		
		//~ $q="select distinct b.`item_code` from item_master a, ph_stock_master b where a.item_name!='' and a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph'";
		//~ $q.=" order by a.item_name limit 0,30";
	}
	//echo $q;
	$d=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Code</th><th>Item Name</th><th>Manufacturer</th><th>Stock</th><th>Rack No</th>
		</tr>
	<?php
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_id`,`item_name`,`item_type_id`,`gst`,`rack_no`,`strip_quantity` FROM `item_master` WHERE `item_id`='$d1[item_code]'"));
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`closing`),0) AS stock FROM `inv_maincurrent_stock` WHERE `item_id`='$d1[item_code]'"));
		//$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS stock FROM `ph_stock_master` WHERE `item_code`='$d1[item_code]' AND `substore_id`='$ph'"));
		$gst=explode(".",$itm['gst']);
		$gst=$gst[0];
		$i_type="";
		if($itm['item_type_id'])
		{
			$i_typ=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_type_name` FROM `item_type_master` WHERE `item_type_id`='$itm[item_type_id]'"));
			$i_type=" <small style='float:right;'><b><i>(".$i_typ['item_type_name'].")</i></b></small>";
		}
		$mfg=mysqli_fetch_assoc(mysqli_query($link,"SELECT `manufacturer_name` FROM `manufacturer_company` WHERE `manufacturer_id`='$d1[manufacturer_id]'"));
		?>
		<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>','<?php echo $gst;?>','<?php echo $itm['strip_quantity'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
			<td><?php echo $itm['item_id'];?></td>
			<td><?php echo $itm['item_name'].$i_type;?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name']."#".$gst."#".$itm['strip_quantity']."#";?>
				</div>
			</td>
			<td><?php echo $mfg['manufacturer_name'];?></td>
			<td><?php echo $stk['stock']/$itm['strip_quantity'];?></td>
			<td><?php echo $itm['rack_no'];?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
	<?php
} // 9

if($type==10)
{
	$itm_id=$_POST['itm_id'];
	$bch=$_POST['bch'];
	$exp_dt=$_POST['exp_dt'];
	$exp_dt=date('Y-m-t', strtotime($exp_dt));
	$qnt=$_POST['qnt'];
	$free=$_POST['free'];
	$gst=$_POST['gst'];
	$gstamt=$_POST['gstamt'];
	$pkd_qnt=$_POST['pkd_qnt'];
	$mrp=$_POST['mrp'];
	//$mrp=$strip_mrp/$pkd_qnt;
	$unit_sale=$_POST['unit_sale'];
	$cost=$_POST['cost'];
	$unit_cost=$_POST['unit_cost'];
	$disc=$_POST['disc'];
	$d_amt=$_POST['d_amt'];
	$amt=$_POST['amt'];
	$supp=$_POST['supp'];
	$bill_no=$_POST['bill_no'];
	$billdate=$_POST['billdate'];
	$sub_dept=$_POST['sub_dept'];
	$user=$_POST['user'];
	$net_amt=$amt+$gstamt;
	
	mysqli_query($link,"INSERT INTO `ph_stock_transfer_details_temp`(`issue_no`, `item_id`, `batch_no`, `exp_date`, `mrp`, `issue_qnt`, `rate`, `amount`, `gst_per`, `gst_amount`, `total_amount`, `date`, `time`) VALUES ('$sub_dept','$itm_id','$bch','$exp_dt','$mrp','$qnt','$unit_cost','$amt','$gst','$gstamt','$net_amt','$date','$time')");
} // 10

if($type==11)
{
	$itm=$_POST['itm'];
	$bch=$_POST['bch'];
	$sub_dept=$_POST['sub_dept'];
	mysqli_query($link,"DELETE FROM `ph_stock_transfer_details_temp` WHERE `issue_no`='$sub_dept' AND `item_id`='$itm' AND `batch_no`='$bch'");
} // 11

if($type==12)
{
	$sub_dept=$_POST['sub_dept'];
	$q=mysqli_query($link,"SELECT * FROM `ph_stock_transfer_details_temp` WHERE `issue_no`='$sub_dept'");
	if(mysqli_num_rows($q)>0)
	{
	?>
	<input type="hidden" id="sub_dept" value="<?php echo $sub_dept;?>" />
	<table class="table table-condensed table-bordered" id="mytable">
		
		<tr style="background-color:#cccccc">
			<th>#</th>
			<th>Description</th>
			<th>Batch No</th>
			<th>Quantity</th>
			<th>MRP</th>
			<th>Pack size</th>
			<th></th>
			<th style="width:5%;">Remove</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
			$gst_per=explode(".",$r['gst_per']);
			$gst_per=$gst_per[0];
			$dis_per=explode(".",$r['dis_per']);
			$dis_per=$dis_per[0];
		?>
		<tr class="all_tr <?php echo $r['item_id'].$r['batch_no'];?>">
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?><input type="hidden" value="<?php echo $r['item_id'];?>" class="itm"><input type="hidden" value="<?php echo $r['item_id'].$r['batch_no'];?>" class="test_id"></td>
			<td><?php echo $r['batch_no'];?><input type="hidden" value="<?php echo $r['batch_no'];?>" class="batch"></td>
			<td><?php echo $r['issue_qnt'];?><input type="hidden" value="<?php echo $r['issue_qnt'];?>" class="qnt"></td>
			<td><?php echo $r['mrp'];?><input type="hidden" value="<?php echo $r['mrp'];?>" class="mrp"></td>
			<td><?php echo $itm['strip_quantity'];?><input type="hidden" value="<?php echo $r['rate'];?>" class="rate"><input type="hidden" value="<?php echo $itm['strip_quantity'];?>" class="pkd"></td>
			<td><input type="hidden" value="<?php echo $r['amount'];?>" class="all_rate"><input type="hidden" value="<?php echo $gst_per;?>" class="gst_per"></td>
			<td style="text-align:center;"><input type="hidden" value="<?php echo $r['gst_amount'];?>" class="all_gst"><input type="hidden" value="<?php echo date("Y-m", strtotime($r['exp_date']));?>" class="expdt"><span onclick="itm_rem(this);$(this).parent().parent().remove();set_amt()" style="cursor:pointer;color:#c00;"><i class="icon-remove icon-large text-danger"></i></span><span></span></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
	}
} // 12

if($type==999)
{
	$id=$_POST['id'];
} // 999
?>
