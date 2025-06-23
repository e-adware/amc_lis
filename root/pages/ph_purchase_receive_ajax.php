<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");
$datetime=mktime();

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

function nextId($prefix,$table,$idno,$start="100") 
{
	$idRes=mysqli_query($GLOBALS["___mysqli_ston"], "Select max( cast( right(".$idno.",length(".$idno.")-".strlen($prefix).") AS signed ) ) from ".$table);
				//SELECT max( cast( right( r_id, length( r_id ) -2 ) AS signed ) ) 

//echo "Select max( cast( right(".$idno.",length(".$idno.")-".strlen($prefix).") AS signed ) ) from ".$table;
	$idArr=mysqli_fetch_array($idRes);
		if ($idArr[0]==null) {
			$NewId=$start;
		}
		else {
			$NewId=$idArr[0]+1;
		}
	$NewId=$prefix.$NewId;
	return $NewId;
	//return "Select max( cast( right(".$idno.",length(".$idno.")-".strlen($prefix).") AS signed ) ) from ".$table."<br>SELECT max( cast( right( r_id, length( r_id ) -2 ) AS signed ) )";
}

$type=$_POST['type'];

if($type==1) // for load sale price gst calculation
{
	$rate=$_POST['rate'];
	$pktqnt=$_POST['pktqnt'];
	$gst=$_POST['gst'];
	$vunitprice=$rate/$pktqnt;
	$vslprice1=$vunitprice-($vunitprice*(100/(100+$gst)));///Remove gst Calculattion
	$vslprice2=round($vslprice1,2);
	$vslprice3=$vunitprice-$vslprice2;
	$vslprice=$vslprice3;
	
	$val=$vslprice.'@'.$vslprice;
	echo $val;
} // 1

if($type==2)
{
	$spplrid=$_POST['supp'];
	$splrblno=$_POST['bill_no'];
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
	$branch_id=1;
	$substore_id=1;
	$rad=0;
	$adj=0;
	
	if($btn_val=="Done")
	{
		$txt="----------------------------------------------------------------------";
		$orderno="RCV".date("YmdHis").$userid;
		$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`order_no`) as tot FROM `ph_purchase_receipt_master`"));
		$bill_tot_num=$bill_no_qry["tot"];
		$bill_no=$bill_tot_num+1;
		$orderno="RCV".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
		
		if(mysqli_query($link,"INSERT INTO `ph_purchase_receipt_master`(`branch_id`, `order_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `fid`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$branch_id','$orderno','$bildate','$entrydate','$total','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','0','$userid','$time','$rad','$adj')"))
		{
			$txt.="\nINSERT INTO `ph_purchase_receipt_master`(`branch_id`, `order_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `fid`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$branch_id','$orderno','$bildate','$entrydate','$total','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','0','$userid','$time','$rad','$adj');";
			
			$supp_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_supplier_transaction` WHERE `branch_id`='$branch_id' AND `supp_code`='$spplrid' ORDER BY `slno` DESC LIMIT 0,1"));
			$balance=$supp_bal['balance_amt']+$ttlamt;
			mysqli_query($link,"INSERT INTO `ph_supplier_transaction`(`branch_id`, `process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`,`adjust_type`, `balance`, `balance_amt`, `type`, `date`, `time`, `datetime`, `user`) VALUES ('$branch_id','$orderno','$spplrid','$ttlamt','0','0','0','0','$balance','1','$date','$time','$datetime','$user')");
			$txt.="\nINSERT INTO `ph_supplier_transaction`(`branch_id`, `process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`,`adjust_type`, `balance`, `balance_amt`, `type`, `date`, `time`, `datetime`, `user`) VALUES ('$branch_id','$orderno','$spplrid','$ttlamt','0','0','0','0','$balance','1','$date','$time','$datetime','$user');";
			
			$al=explode("#%#",$all);
			foreach($al as $a)
			{
				$v=explode("@@",$a);
				$itm=$v[0];
				$bch=mysqli_real_escape_string($link,$v[1]);
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
				if($itm && $bch && $qnt)
				{
					mysqli_query($link,"INSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `strip_quantity`, `recpt_mrp`, `cost_price`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','0000-00-00','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$pkd_qnt','$mrp','$strip_cost','$unit_cost','$unit_sale','0','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
					$txt.="\nINSERT INTO `ph_purchase_receipt_details`(`order_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `strip_quantity`, `recpt_mrp`, `cost_price`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','0000-00-00','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$pkd_qnt','$mrp','$strip_cost','$unit_cost','$unit_sale','0','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt');";
					
					mysqli_query($link,"UPDATE `item_master` SET `hsn_code`='$hsn',`rack_no`='$rack_no',`gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
					$txt.="\nUPDATE `item_master` SET `hsn_code`='$hsn',`rack_no`='$rack_no',`gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm';";
					
					$vqnt=$qnt+$free;
					$vst=0;
					$vstkqnt=0;
					
					$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and  batch_no='$bch' order by date desc"));
					if($last_stock) // last stock of current date
					{
						$txt.="\nSELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and batch_no='$bch' order by date desc;";
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
				}
			}
			text_query($txt,"purchase_receive.txt");
			echo "Saved";
		}
		else
		{
			echo "Error";
		}
	}
}

if($type==2-222222)
{
	$spplrid=$_POST['supp'];
	$splrblno=$_POST['bill_no'];
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
	if($btn_val=="Done")
	{
		$orderno=nextId("","inv_main_stock_received_master","receipt_no","1");
		$orderno.="/".date("y");
		//echo $orderno;
		
		if(mysqli_query($link,"insert into inv_main_stock_received_master(`receipt_no`,`bill_date`,`recpt_date`,`bill_amount`,`gst_amt`,`dis_amt`,`net_amt`,`supp_code`,`bill_no`,`user`,`time`,`adjust_type`,`adjust_amt`) values('$orderno','$bildate','$entrydate','$billamt','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','$userid','$time','$rad','$adj')"))
		{
			$supp_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$spplrid' ORDER BY `slno` DESC LIMIT 0,1"));
			$balance=$supp_bal['balance_amt']+$billamt;
			mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$orderno','$spplrid','$billamt','0','0','$balance','1','$date','$time','$user')");
			$al=explode("#%#",$all);
			foreach($al as $a)
			{
				$v=explode("@@",$a);
				$itm=$v[0];
				$bch=mysqli_real_escape_string($link,$v[1]);
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
				if($itm && $bch && $qnt)
				{
					mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `strip_quantity`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$pkd_qnt','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
					
					$vqnt=$qnt+$free;
					$vst=0;
					mysqli_query($link,"UPDATE `item_master` SET `hsn_code`='$hsn',`rack_no`='$rack_no',`gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
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
				}
			}
		}
		echo "Saved";
	}
} // 2

if($type==3)
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
			<th>Print</th>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			$splr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supp_code]'"));
			
			$vttlamt=$vttlamt+$r['bill_amount'];
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $splr['name'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo convert_date($r['recpt_date']);?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td style="text-align:right" ><?php echo $r['bill_amount'];?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="rcv_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>-->
			<td>
				<button type="button" class="btn btn-info btn-mini" onclick="rcv_rep_prr('<?php echo base64_encode($r['order_no']);?>')">Print</button>
				<button type="button" class="btn btn-info btn-mini" onclick="rcv_edit('<?php echo base64_encode($r['order_no']);?>')">Edit</button>
			</td>
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
		</tr>
	</table>
	<?php
} // 3

if($type==4)
{
	$val=$_POST['val'];
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th width="5%">#</th><th>Item Name</th>
		</tr>
	<?php
		
		if(strlen($val)>0)
		{
			$q="SELECT `item_id`,`item_name`,`hsn_code`,`rack_no`,`gst`,`strip_quantity` FROM `item_master` WHERE `item_name` like '$val%' ORDER BY `item_name` LIMIT 0,20";
		}
		else
		{
			$q="SELECT `item_id`,`item_name`,`hsn_code`,`rack_no`,`gst`,`strip_quantity` FROM `item_master` ORDER BY `item_name` LIMIT 0,20";
		}
		//echo $q;
		$d=mysqli_query($link, $q);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$gst=explode(".",$d1['gst']);
			$gst=$gst[0];
			?>
			<tr onclick="doc_load('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['gst'];?>','<?php echo $d1['strip_quantity'];?>','<?php echo $d1['hsn_code'];?>','<?php echo $d1['rack_no'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
				<td><?php echo $d1['item_id'];?></td>
				<td><?php echo $d1['item_name'];?>
					<div <?php echo "id=dvdoc".$i;?> style="display:none;">
					<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$gst."#".$d1['strip_quantity']."#".$d1['hsn_code']."#".$d1['rack_no']."#";?>
					</div>
				</td>
			</tr>
		<?php
			$i++;
		}
		?>
		</table>
	<?php
} // 4

if($type==5)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$spplrid=$_POST['spplrid'];
	if($spplrid)
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_item_return_supplier_master` WHERE `date` BETWEEN '$fdate' AND '$tdate' AND `supplier_id`='$spplrid' ORDER BY `slno`");
    }
    else
    {
		$q=mysqli_query($link,"SELECT * FROM `ph_item_return_supplier_master` WHERE `date` BETWEEN '$fdate' AND '$tdate' ORDER BY `slno`");
	}
	$n=mysqli_num_rows($q);
	?>
		
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Return No</th>
			<th>Supplier</th>
			<th>Return Date</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">GST Amount</th>
			<th style="text-align:right">Net Amount</th>
			<!--<th>Export</th>-->
			<th>Print</th>
		</tr>
		<?php
		$i=1;
		$bill='';
		$dt='';
		while($r=mysqli_fetch_array($q))
		{
			$splr=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[supplier_id]'"));
			
			$vttlamt=$vttlamt+$r['net_amount'];
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['returnr_no'];?></td>
			<td><?php echo $splr['name'];?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td style="text-align:right" ><?php echo $r['amount'];?></td>
			<td style="text-align:right" ><?php echo $r['gst_amount'];?></td>
			<td style="text-align:right" ><?php echo $r['net_amount'];?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="rcv_rep_exp('<?php echo $r[order_no];?>')">Export to excel</button></td>-->
			<td><button type="button" class="btn btn-info btn-mini" onclick="ret_rep_prr('<?php echo base64_encode($r['returnr_no']);?>')">Print</button></td>
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
		</tr>
	</table>
	<?php
} // 5

if($type==6)
{
	$ord=base64_decode($_POST['ord']);
	?>
	<input type="hidden" id="edit_ord" value="<?php echo $ord;?>" />
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th style="text-align:right">MRP</th>
			<th style="text-align:right">Rate</th>
			<th style="text-align:right">GST %</th>
			<th style="text-align:right">Qnty</th>
			<th style="text-align:right">Free Qnt</th>
			<th style="text-align:right">Dis %.</th>
			<th>Amount</th>
		</tr>
		<?php
		$i=0;
		$j=1;
		$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_details` WHERE `order_no`='$ord'");
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			$gst_per=explode(".",$r['gst_per']);
			$gst_per=$gst_per[0];
			$dis_per=explode(".",$r['dis_per']);
			$dis_per=$dis_per[0];
		?>
		<tr class="all_tr" id="tr<?php echo $j;?>">
			<td><?php echo $j;?></td>
			<td>
				<?php echo $itm['item_name'];?>
				<input type="hidden" class="input" id="itm" value="<?php echo $r['item_code'];?>" readonly />
			</td>
			<td>
				<span><?php echo $r['recept_batch'];?></span>
			</td>
			<td>
				<?php echo date("Y-m", strtotime($r['expiry_date']));?>
				<input type="hidden" class="input" id="exp_date" value="<?php echo date("Y-m", strtotime($r['expiry_date']));?>" maxlength="7" />
			</td>
			<td style="text-align:right;"><input type="text" class="input" id="mrp" onkeyup="chk_dec(this,event)" value="<?php echo $r['recpt_mrp'];?>" /></td>
			<td style="text-align:right;"><input type="text" class="input" id="cost" onkeyup="chk_dec(this,event);change_val('<?php echo $j;?>')" value="<?php echo $r['recept_cost_price'];?>" /></td>
			<td style="text-align:right;">
				<input type="text" class="input" id="gst" onkeyup="chk_num(this,event);change_val('<?php echo $j;?>')" value="<?php echo $gst_per;?>" maxlength="2" />
				<input type="hidden" class="input" id="gst_amt" value="<?php echo $r['gst_amount'];?>" readonly />
			</td>
			<td style="text-align:right;">
				<?php echo $r['recpt_quantity'];?>
				<input type="hidden" class="input" id="qnt" onkeyup="chk_num(this,event);change_val('<?php echo $j;?>')" value="<?php echo $r['recpt_quantity'];?>" readonly />
			</td>
			<td style="text-align:right;">
				<?php echo $r['free_qnt'];?>
				<input type="hidden" class="input" id="free" onkeyup="chk_num(this,event)" value="<?php echo $r['free_qnt'];?>" readonly />
			</td>
			<td style="text-align:right;">
				<input type="text" class="input" id="disc" onkeyup="chk_num(this,event);change_val('<?php echo $j;?>')" value="<?php echo $dis_per;?>" />
				<input type="hidden" class="input" id="dis_amt" value="<?php echo $r['dis_amt'];?>" />
			</td>
			<td style="text-align:right;">
				<span><?php echo ($r['item_amount']+$r['gst_amount']);?></span>
				<input type="hidden" class="input" id="item_amount" value="<?php echo ($r['item_amount']+$r['gst_amount']);?>" readonly />
			</td>
		</tr>
		<?php
		$i++;
		$j++;
		}
		?>
		<tr>
			<td colspan="11" style="text-align:center;">
				<button type="button" class="btn btn-primary" id="sav_btn" onclick="update_purchase('<?php echo $i;?>')">Save</button>
				<button type="button" class="btn btn-danger" id="clos_btn" data-dismiss="modal">Cancel</button>
			</td>
		</tr>
	</table>
	<style>
		.input
		{
			width:60px;
		}
	</style>
	<?php
} // 6

if($type==7)
{
	$ord=$_POST['ord'];
	$all=$_POST['all'];
	$val=0;
	
	$all_amt=0;
	$all_gst=0;
	$all_dis=0;
	$al=explode("#penguin#",$all);
	foreach($al as $a)
	{
		$v=explode("@@@",$a);
		$itm=$v[0];
		$bch=$v[1];
		$qnt=$v[2];
		$mrp=$v[3];
		$rate=$v[4];
		$gst_per=$v[5];
		$gst_amt=$v[6];
		$dis_per=$v[7];
		$dis_amt=$v[8];
		
		$amt=$rate*$qnt;
		$sale_price=($mrp*(100/(100+$gst_per)));///Remove gst Calculattion
		if($itm)
		{
			mysqli_query($link,"UPDATE `ph_purchase_receipt_details` SET `recpt_mrp`='$mrp',`recept_cost_price`='$rate',`sale_price`='$sale_price',`item_amount`='$amt',`dis_per`='$dis_per',`dis_amt`='$dis_amt',`gst_per`='$gst_per',`gst_amount`='$gst_amt' WHERE `order_no`='$ord' AND `item_code`='$itm' AND `recept_batch`='$bch'");
			$all_amt+=$amt;
			$all_gst+=$gst_amt;
			$all_dis+=$dis_amt;
			$val++;
		}
	}
	if($val)
	{
		$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE `order_no`='$ord'"));
		$net_amt=($all_gst+$det['bill_amount']);
		mysqli_query($link,"UPDATE `ph_purchase_receipt_master` SET `gst_amt`='$all_gst', `dis_amt`='$all_dis', `net_amt`='$net_amt' WHERE `order_no`='$ord'");
		echo "Done";
	}
	else
	{
		echo "DONE";
	}
} // 7

if($type==8)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$spplrid=$_POST['spplrid'];
	$user=$_POST['user'];
	if($spplrid=='0')
	{
	$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE bill_date between'$fdate' and '$tdate' order by `order_no`");
    }
    else
    {
		$q=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE bill_date between'$fdate' and '$tdate' and supp_code='$spplrid'  order by `order_no`");
	}
	$u_level=mysqli_fetch_assoc(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$user'"));
	$n=mysqli_num_rows($q);
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<!--<th>Order No</th>-->
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
			<!--<td><?php echo $r['order_no'];?></td>-->
			<td><?php echo $splr['name'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo convert_date($r['recpt_date']);?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td style="text-align:right" ><?php echo $r['net_amt'];?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="rcv_rep_exp('<?php echo $r['order_no'];?>')">Export to excel</button></td>-->
			<td><?php echo $quser['name'];?></td>
			<td>
				<button type="button" class="btn btn-primary btn-mini" onclick="rcv_rep_prr('<?php echo base64_encode($r['order_no']);?>')">Print</button>
				<?php
				if($u_level['levelid']=="1" || $u_level['levelid']=="22")
				{
				?>
				<button type="button" class="btn btn-info btn-mini" onclick="rcv_item_edit('<?php echo base64_encode($r['order_no']);?>')"><i class="icon-plus icon-large"></i></button>
				<?php
				}
				?>
			</td>
		</tr>
		<?php
		$n=0;
		$i++;
		}
		?>
		<tr>
			<td colspan="5" style="text-align:right;font-weight:bold"> Total </td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttlamt,2);?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<?php
} // 8

if($type==9)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Qnt</th>
			<th>MRP</th>
			<th>Expiry Date</th>
			<th>User</th>
		</tr>
	<?php
	$j=1;
	$date_qry=mysqli_query($link,"SELECT DISTINCT `entry_date` FROM `ph_item_stock_entry` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate'");
	while($r_date=mysqli_fetch_assoc($date_qry))
	{
	?>
		<tr>
			<th></th>
			<th colspan="7"><i>Entry Date : <?php echo convert_date($r_date['entry_date']);?></i></th>
		</tr>
	<?php
	$itm_qry=mysqli_query($link,"SELECT DISTINCT `item_code` FROM `ph_item_stock_entry` WHERE `entry_date`='$r_date[entry_date]'");
	while($r_itm=mysqli_fetch_assoc($itm_qry))
	{
	$bch_qry=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `ph_item_stock_entry` WHERE `item_code`='$r_itm[item_code]' AND `entry_date`='$r_date[entry_date]'");
	while($r_bch=mysqli_fetch_assoc($bch_qry))
	{
	$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r_itm[item_code]'"));
	$q=mysqli_query($link,"SELECT * FROM `ph_item_stock_entry` WHERE `item_code`='$r_itm[item_code]' AND `batch_no`='$r_bch[batch_no]' AND `entry_date`='$r_date[entry_date]'");
	while($r=mysqli_fetch_assoc($q))
	{
	$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r_itm['item_code'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo $r['return_qnt'];?></td>
			<td><?php echo $r['item_mrp'];?></td>
			<td><?php echo date("Y-m", strtotime($r['expiry_date']));?></td>
			<td><?php echo $usr['name'];?></td>
		</tr>
		<?php
		$j++;
	}
	}
	}
	}
	?>
	</table>
	<?php
} // 9

if($type==10)
{
	$splrid=$_POST['supp'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$ph=$_POST['ph'];
	?>
	<span class="noprint" style="float:right;display:none;">
		<button type="button" class="btn btn-primary act_btn" onclick="supplier_rcvd_gst_print('<?php echo base64_encode($ph);?>','<?php echo base64_encode($splrid);?>','<?php echo base64_encode($fdate);?>','<?php echo base64_encode($tdate);?>')">Print</button>
		<button type="button" class="btn btn-success act_btn" onclick="supplier_rcvd_gst_export('<?php echo base64_encode($ph);?>','<?php echo base64_encode($splrid);?>','<?php echo base64_encode($fdate);?>','<?php echo base64_encode($tdate);?>')">Export</button>
	</span>
	<table class="table table-condensed table-bordered table-report">
		<tr style="background:#DFE6E4;">
			<th>#</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Party Name</th>
			<th>GSTIN</th>
			<th style="text-align:right">Bill Value</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">Discount</th>
			<th style="text-align:right">Credit Note</th>
			<th style="text-align:right">Adjustment</th>
			<th style="text-align:right">Round Off</th>
			<th style="text-align:right">GST Amount</th>
			<th colspan="2">Amount <span style="float:right">0 %</span></th>
			<th colspan="2">Amount <span style="float:right">5 %</span></th>
			<th colspan="2">Amount <span style="float:right">12 %</span></th>
			<th colspan="2">Amount <span style="float:right">18 %</span></th>
			<th style="text-align:right">Tax Amount</th>
		</tr>
		<?php
		if($splrid)
		{
			$bil=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE `supp_code`='$splrid' AND `bill_date` BETWEEN '$fdate' and '$tdate' ORDER BY `bill_date`");
		}
		else
		{
			$bil=mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE `bill_date` BETWEEN '$fdate' and '$tdate' ORDER BY `bill_date`");
		}
		$j=1;
		$tot_disc=0;
		$tot_gst=0;
		$bill_amount=0;
		$tot_amt=0;
		$cred_amt=0;
		$adj_amt=0;
		$tot_amt_0=0;
		$tot_gst_0=0;
		$tot_amt_5=0;
		$tot_gst_5=0;
		$tot_amt_12=0;
		$tot_gst_12=0;
		$tot_amt_18=0;
		$tot_gst_18=0;
		while($bl=mysqli_fetch_assoc($bil))
		{
			$s_name=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name`,`address`,`gst_no` FROM `inv_supplier_master` WHERE `id`='$bl[supp_code]'"));
			
			$qamt_0=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM ph_purchase_receipt_details  WHERE `order_no`='$bl[order_no]' and gst_per='0.00'")); // 0%
			$qamt_5=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM ph_purchase_receipt_details  WHERE `order_no`='$bl[order_no]' and gst_per='5.00'")); // 5%
			$qamt_12=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM ph_purchase_receipt_details  WHERE `order_no`='$bl[order_no]' and gst_per='12.00'")); // 12%
			$qamt_18=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM ph_purchase_receipt_details  WHERE `order_no`='$bl[order_no]' and gst_per='18.00'")); // 18%
			
			$tot_amt_0+=$qamt_0['maxamt'];
			$tot_gst_0+=$qamt_0['maxgst'];
			
			$tot_amt_5+=$qamt_5['maxamt'];
			$tot_gst_5+=$qamt_5['maxgst'];
			
			$tot_amt_12+=$qamt_12['maxamt'];
			$tot_gst_12+=$qamt_12['maxgst'];
			
			$tot_amt_18+=$qamt_18['maxamt'];
			$tot_gst_18+=$qamt_18['maxgst'];
			?>
			<tr>
				<td><?php echo $j;?></td>
				<td><?php echo $bl['bill_no'];?></td>
				<td><?php echo convert_date($bl['bill_date']);?></td>
				<td><?php echo $s_name['name']; /*if($s_name['address']){echo ", ".$s_name['address'];}*/?></td>
				<td><?php echo $s_name['gst_no'];?></td>
				<td style="text-align:right"><?php echo round($bl['net_amt']).".00";?></td>
				<td style="text-align:right"><?php echo round($bl['bill_amount']).".00";?></td>
				<td style="text-align:right"><?php echo $bl['dis_amt'];?></td>
				<td style="text-align:right"><?php echo $bl['credit_amt'];?></td>
				<td style="text-align:right"><?php echo $bl['adjust_amt'];?></td>
				<td style="text-align:right">0.00</td>
				<td style="text-align:right"><?php echo $bl['gst_amt'];?></td>
				<td style="text-align:right"><?php echo number_format($qamt_0['maxamt'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_0['maxgst'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_5['maxamt'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_5['maxgst'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_12['maxamt'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_12['maxgst'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_18['maxamt'],2);?></td>
				<td style="text-align:right"><?php echo number_format($qamt_18['maxgst'],2);?></td>
				<td style="text-align:right"><?php echo number_format($bl['gst_amt'],2);?></td>
			</tr>
			<?php
			$j++;
			
			$tot_disc+=$bl['dis_amt'];
			$tot_amt+=round($bl['net_amt']);
			$bill_amount+=round($bl['bill_amount']);
			$cred_amt+=$bl['credit_amt'];
			$adj_amt+=$bl['adjust_amt'];
			$tot_gst+=$bl['gst_amt'];
			
			//~ $net_disc+=$tot_disc;
			//~ $net_amount+=$tot_amt;
			//~ $net_amt_0+=$tot_amt_0;
			//~ $net_gst_0+=$tot_gst_0;
			//~ $net_amt_5+=$tot_amt_5;
			//~ $net_gst_5+=$tot_gst_5;
			//~ $net_amt_12+=$tot_amt_12;
			//~ $net_gst_12+=$tot_gst_12;
			//~ $net_amt_18+=$tot_amt_18;
			//~ $net_gst_18+=$tot_gst_18;
		}
		?>
		<tr>
			<th colspan="5" style="text-align:right">Grand Total :</th>
			<th style="text-align:right"><?php echo number_format($tot_amt,2);?></th>
			<th style="text-align:right"><?php echo number_format($bill_amount,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_disc,2);?></th>
			<th style="text-align:right"><?php echo number_format($cred_amt,2);?></th>
			<th style="text-align:right"><?php echo number_format($adj_amt,2);?></th>
			<th style="text-align:right"><?php echo number_format(0,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_0,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_0,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_5,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_5,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_12,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_12,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_amt_18,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst_18,2);?></th>
			<th style="text-align:right"><?php echo number_format($tot_gst,2);?></th>
		</tr>
	</table>
	<?php
} // 10

if($type==11)
{
	$ph=$_POST['ph'];
	$itmid=$_POST['itm'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	//$itmid=explode("-#",$itmid1);
	$q=mysqli_query($link,"SELECT a.*,c.item_name FROM ph_purchase_receipt_details a, ph_purchase_receipt_master b, item_master c  WHERE a.order_no=b.order_no AND a.recpt_date between '$fdate' and '$tdate' and a.item_code='$itmid' and a.item_code=c.item_id and a.`supp_code`>'0' order by slno");
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Bill No</th>
			<th>Date</th>
			<th>Supplier</th>
			<th>Batch</th>
			<th>Expiry</th>
			<th>Qnty</th>
			<th>Free</th>
			<th style="text-align:right">MRP</th>
			<th style="text-align:right">Rate</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">GST Amount</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$bill_date=mysqli_fetch_array(mysqli_query($link,"select `bill_date` from ph_purchase_receipt_master where order_no='$r[order_no]'"));
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
			
			$itmamt=0;
			$itmamt=$r['recpt_quantity']*$r['recept_cost_price'];
			
			$vttl=$vttl+$itmamt;
			$vttgst=$vttgst+$r['gst_amount'];
		?>
		<tr onclick="rcv_rep_prr('<?php echo base64_encode($r['order_no']);?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $r['item_name'];?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($bill_date['bill_date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td><?php echo $r['recept_batch'];?></td>
			<td><?php echo date("Y-m", strtotime($r['expiry_date']));?></td>
			<td><?php echo $r['recpt_quantity']/$r['strip_quantity'];?></td>
			<td><?php echo $r['free_qnt']/$r['strip_quantity'];?></td>
			<td style="text-align:right"><?php echo number_format($r['recpt_mrp']*$r['strip_quantity'],2);?></td>
			<td style="text-align:right"><?php echo number_format($r['recept_cost_price']*$r['strip_quantity'],2);?></td>
			<td style="text-align:right"><?php echo number_format($itmamt,2);?></td>
			<td style="text-align:right"><?php echo number_format($r['gst_amount'],2);?></td>
			<!--<td><button type="button" class="btn btn-info" onclick="report_print_billwise('<?php echo $r[bill_no];?>','<?php echo $r[supp_code];?>')">View</button></td>-->
		</tr>
		<?php
		$i++;
		}
		?>
		
		<tr>
			<td colspan="11" style="font-weight:bold;text-align:right">Total </td>
			<td style="font-weight:bold;text-align:right"><?php echo number_format($vttl,2);?> </td>
			<td style="font-weight:bold;text-align:right"><?php echo number_format($vttgst,2);?> </td>
		</tr>
		
	</table>
	<?php
} // 11

if($type==12)
{
	$ph=$_POST['ph'];
	$supp=$_POST['supp'];
	$itmid=$_POST['itmid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$qry="SELECT a.`expiry_date`, a.`quantity`, a.`free_qnt`, a.`batch_no`, a.`recpt_mrp`, a.`recept_cost_price`, a.`item_amount`, a.`gst_per`, a.`gst_amount`, b.`date`, b.`supplier_id` FROM `ph_item_return_supplier_details` a, `ph_item_return_supplier_master` b WHERE a.`returnr_no`=b.`returnr_no` AND b.`date` BETWEEN '$fdate' AND '$tdate' AND a.`item_id`='$itmid'";
	if($supp)
	{
		$qry.=" AND b.`supplier_id`='$supp'";
	}
	//echo $qry;
	$q=mysqli_query($link,$qry);
	?>
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Date</th>
			<th>Supplier</th>
			<th>Batch</th>
			<th>Expiry</th>
			<th>Qnty</th>
			<th style="text-align:right">MRP</th>
			<th style="text-align:right">Rate</th>
			<th style="text-align:right">Amount</th>
			<th style="text-align:right">GST Amount</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"select `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$itmid'"));
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supplier_id]'"));
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo date("Y-m", strtotime($r['expiry_date']));?></td>
			<td><?php echo $r['quantity']/$itm['strip_quantity'];?></td>
			<td style="text-align:right"><?php echo number_format($r['recpt_mrp']*$itm['strip_quantity'],2);?></td>
			<td style="text-align:right"><?php echo number_format($r['recept_cost_price']*$itm['strip_quantity'],2);?></td>
			<td style="text-align:right"><?php echo number_format($r['item_amount'],2);?></td>
			<td style="text-align:right"><?php echo number_format($r['gst_amount'],2);?></td>
		</tr>
		<?php
		$i++;
		}
		?>
		<!--
		<tr>
			<td colspan="8" style="font-weight:bold;text-align:right">Total </td>
			<td style="font-weight:bold;text-align:right"><?php echo number_format($vttl,2);?> </td>
			<td style="font-weight:bold;text-align:right"><?php echo number_format($vttgst,2);?> </td>
		</tr>
		-->
	</table>
	<?php
} // 12

if($type==13)
{
	$supp=$_POST['supp'];
	$bill_no=$_POST['bill_no'];
	$branch_id=1;
	
	$v=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_purchase_receipt_master` WHERE `branch_id`='$branch_id' AND `supp_code`='$supp' AND `bill_no`='$bill_no'"));
	if($v)
	{
		echo 1;
	}
	else
	{
		echo 0;
	}
}
if($type==999)
{
	$id=$_POST['id'];
} // 999
?>
