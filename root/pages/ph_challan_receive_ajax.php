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
	$time = date("g:i A", strtotime($time));
	return $time;
}
$userid=$_POST['user'];
function text_query($txt,$file)
{
	if($txt)
	{
		//$myfile = file_put_contents('../../log/'.$file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
	
	
	$txt_file="../../log/".$file;
	if(file_exists($txt_file))
	{
		file_put_contents($txt_file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
	else
	{
		$fp = fopen($txt_file, 'w');
		file_put_contents($txt_file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
	//~ $file_name="../../log/challan_received".$userid.".json";
	//~ if(file_exists($file_name))
	//~ {
		//~ $inp = file_get_contents($file_name);
		//~ $tempArray = json_decode($inp);
		//~ array_push($tempArray, $txt);
		//~ $jsonData = json_encode($tempArray);
		//~ file_put_contents($file_name, $jsonData);
	//~ }
	//~ else
	//~ {
		//~ $fp = fopen($file_name, 'w');
		//~ fwrite($fp, json_encode($txt));
		//~ fclose($fp);
	//~ }
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
	if($bildate=="")
	{
		$bildate=$date;
	}
	$billamt=$_POST['bill_amt'];
	$total=$_POST['total'];
	$disamt=$_POST['discount'];
	$gstamt=$_POST['all_gst'];
	$adj=$_POST['adj'];
	$ttlamt=$_POST['net_amt'];
	$userid=$_POST['user'];
	$all=$_POST['all'];
	$btn_val=$_POST['btn_val'];
	$entrydate=$date;
	$rad=0;
	//$adj=0;
	$substore_id=1;
	if($btn_val=="Done")
	{
		$challan_qry=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_challan_receipt_master` WHERE `challan_no`='$splrblno' AND `supp_code`='$spplrid'"));
		if($challan_qry)
		{
			echo 3;
		}
		else
		{
			//$orderno=nextId("","inv_main_stock_received_master","receipt_no","1");
			//$orderno.="/".date("y");
			//echo $orderno;
			$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`slno`) as tot FROM `ph_challan_receipt_master`"));
			$bill_tot_num=$bill_no_qry["tot"];
			$bill_no=$bill_tot_num+1;
			$orderno="PCH".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
			
			//if(mysqli_query($link,"insert into inv_main_stock_received_master(`order_no`,`receipt_no`,`bill_date`,`recpt_date`,`bill_amount`,`gst_amt`,`dis_amt`,`net_amt`,`supp_code`,`bill_no`,`user`,`time`,`adjust_type`,`adjust_amt`) values('$orderno','$orderno','$bildate','$entrydate','$total','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','$userid','$time','$rad','$adj')"))
			if(mysqli_query($link,"INSERT INTO `ph_challan_receipt_master`(`order_no`, `challan_no`, `bill_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `date`, `time`, `adjust_type`, `adjust_amt`, `stat`) VALUES ('$orderno','$splrblno','$bildate','$total','$gstamt','$disamt','$ttlamt','$spplrid','','$userid','$date','$time','$rad','$adj','0')"))
			{
				$txt="\nINSERT INTO `ph_challan_receipt_master`(`order_no`, `challan_no`, `bill_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `date`, `time`, `adjust_type`, `adjust_amt`, `stat`) VALUES ('$orderno','$splrblno','$bildate','$total','$gstamt','$disamt','$ttlamt','$spplrid','','$userid','$date','$time','$rad','$adj','0')";
				//$balance_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$spplrid' ORDER BY `slno` DESC LIMIT 0,1"));
				//$txt.="\nSELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$spplrid' ORDER BY `slno` DESC LIMIT 0,1";
				//$final_balance=$balance_det['balance_amt']+$ttlamt;
				//mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$splrblno','$spplrid','$ttlamt','0','0','$final_balance','1','$date','$time','$user')");
				//$txt.="\nINSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$splrblno','$spplrid','$ttlamt','0','0','$final_balance','1','$date','$time','$user')";
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
					$hsn_code=$v[15];
					$qnt=$qnt*$pkd_qnt;
					$free=$free*$pkd_qnt;
					if($itm && $bch)
					{
						//mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$orderno','$splrblno','$itm','','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
						mysqli_query($link,"INSERT INTO `ph_challan_receipt_details`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','','$exp_dt','$date','$pkd_qnt','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$strip_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
						$txt.="\nINSERT INTO `ph_challan_receipt_details`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','','$exp_dt','$date','$pkd_qnt','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$strip_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')";
						$vqnt=$qnt+$free;
						$vst=0;
						mysqli_query($link,"UPDATE `item_master` SET `hsn_code`='$hsn_code', `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
						$txt.="\nUPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'";
						mysqli_query($link,"DELETE FROM `ph_challan_receipt_details_temp` WHERE `bill_no`='$splrblno' AND `item_id`='$itm' AND `recept_batch`='$bch' AND `supp_code`='$spplrid'");
						$txt.="\nDELETE FROM `ph_challan_receipt_details_temp` WHERE `bill_no`='$splrblno' AND `item_id`='$itm' AND `recept_batch`='$bch' AND `supp_code`='$spplrid'";
						$vstkqnt=0;
						/*
						//--------------------------------pharmacy stock-------------------------//
						$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and  batch_no='$bch' AND `substore_id`='$substore_id' order by date desc"));
						if($last_stock) // last stock of current date
						{
							$txt.="\nSELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and batch_no='$bch' AND `substore_id`='$substore_id' order by date desc;";
							$add_qnt=$last_stock['added']+$vqnt;
							$close_qnt=$last_stock['s_remain']+$vqnt;
							
							mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$splrblno','$itm','$bch','$last_stock[s_remain]','$vqnt','$close_qnt','1','$date','$time','$user')");
							$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$splrblno','$itm','$bch','$last_stock[s_remain]','$vqnt','$close_qnt','1','$date','$time','$user')";
						
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
							
							$close_qnt=$last_stock['s_remain']+$vqnt;
							
							mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$splrblno','$itm','$bch','$last_stock[s_remain]','$vqnt','$close_qnt','1','$date','$time','$user')");
							$txt.="\nINSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$splrblno','$itm','$bch','$last_stock[s_remain]','$vqnt','$close_qnt','1','$date','$time','$user')";
						
							mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substore_id','$orderno','$itm','$bch','$last_stock[s_remain]','$vqnt',0,0,0,'$close_qnt','$date')");
							$txt.="\nINSERT INTO `ph_stock_process`(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substore_id','$orderno','$itm','$bch','$last_stock[s_remain]','$vqnt',0,0,0,'$close_qnt','$date');";
							
							mysqli_query($link,"DELETE FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
							$txt.="\nDELETE FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id';";
							
							mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$exp_dt')");
							$txt.="\nINSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$exp_dt');";
						}
						//*/
						//---------------------------------------------------------------------------//
						
						//------------------------------central store--------------------------------//
						$vstkqnt=0;
						$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc"));
						if($qrstkmaster['item_id']!='')
						{
							$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc";
							$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
							$rcvqnt=$qrstkmaster['recv_qnty']+$vqnt;
							mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where date='$date' and item_id='$itm' and batch_no='$bch'");
							$txt.="\nupdate inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where date='$date' and item_id='$itm' and batch_no='$bch'";
							mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
							$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
							mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
							$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')";
						}
						else///for if data not found
						{
							$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
							$txt.="\nselect * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc";
							$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
							mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$qrstkmaster[closing_qnty]','$vqnt',0,'$vstkqnt')");
							$txt.="\ninsert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$qrstkmaster[closing_qnty]','$vqnt',0,'$vstkqnt')";
							mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
							$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
							mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
							$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')";
						}
						mysqli_query($link,"INSERT INTO `inv_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('0','$orderno','$itm','$bch','$qrstkmaster[closing_qnty]','$vqnt','$vstkqnt','1','$date','$time','$userid')");
						$txt.="\nINSERT INTO `inv_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('0','$orderno','$itm','$bch','$qrstkmaster[closing_qnty]','$vqnt','$vstkqnt','1','$date','$time','$userid')";
						//------------------------------------------------------------------------------//
						mysqli_query($link,"UPDATE `item_master` SET `re_order`='0' WHERE `item_id`='$itm'");
						$txt.="\nUPDATE `item_master` SET `re_order`='0' WHERE `item_id`='$itm'";
						mysqli_query($link,"DELETE FROM `inv_item_require` WHERE `item_id`='$itm'");
						$txt.="\nDELETE FROM `inv_item_require` WHERE `item_id`='$itm'";
					}
				}
				$txt.="\n---------------------------------------------------------------------------------------------------";
				text_query($txt,'challan_received.txt');
				echo 1;
			}
			else
			{
				echo 2;
			}
		}
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
			$q="select `item_id`,`item_name`,`hsn_code`,`gst`,`strip_quantity` from item_master where";
			$q.=" item_name!='' and item_name like '$val%' and `need`='0'";
			$q.=" or item_name!='' and item_id like '$val%' and `need`='0'";
			$q.=" or item_name!='' and short_name like '$val%' and `need`='0'";
			$q.=" order by item_name limit 0,30";
		}
		else
		{
			$q="select `item_id`,`item_name`,`hsn_code`,`gst`,`strip_quantity` from item_master where";
			$q.=" item_name!='' and `need`='0'";
			$q.=" order by item_name limit 0,30";
		}
		//echo $q;
		$d=mysqli_query($link, $q);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$gst=explode(".",$d1['gst']);
			$gst=$gst[0];
			?>
			<tr onclick="doc_load('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['gst'];?>','<?php echo $d1['strip_quantity'];?>','<?php echo $d1['hsn_code'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
				<td><?php echo $i;?></td>
				<td><b><?php echo $d1['item_name'];?></b>
					<div <?php echo "id=dvdoc".$i;?> style="display:none;">
					<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$gst."#".$d1['strip_quantity']."#".$d1['hsn_code']."#";?>
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
	$splrid=$_POST['splrid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$billno=$_POST['billno'];
	 
	
	if($billno !="")
	{
		$qry="SELECT * FROM inv_main_stock_received_master  WHERE bill_no='$billno' order by bill_date desc";
	}
	else if($splrid==0)
	{
	  $qry="SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate'  order by bill_date desc";
	}
	else
	{
		$qry="SELECT * FROM inv_main_stock_received_master  WHERE bill_date between '$fdate' and '$tdate' and supp_code='$splrid' order by slno";
	}
	//echo $qry;
	$q=mysqli_query($link,$qry);

	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered">
		
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Supplier</th>
			<th style="text-align:right">Net Amount</th>
			<th >User</th>
			<th>View</th>
			
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$qsupplier=mysqli_fetch_array(mysqli_query($link,"select name from inv_supplier_master where id='$r[supp_code]'"));
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			$vttl=$vttl+$r['net_amt'];
			//$vttl=$vttl+$r['net_amt'];
			
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['bill_no'];?></td>
			<td><?php echo convert_date($r['bill_date']);?></td>
			<td><?php echo $qsupplier['name'];?></td>
			<td style="text-align:right"><?php echo $r['net_amt'];?></td>
			<td ><?php echo $quser['name'];?></td>
			<td>
				<button type="button" class="btn btn-info btn-mini" onclick="rcv_rep_prr('<?php echo $r['receipt_no'];?>')">View</button>
				<!--<button type="button" class="btn btn-success" onclick="bill_report_xls('<?php echo $r['receipt_no'];?>')">Export to excel</button>-->
				<?php
				if($qsl['levelid']=="1" || $qsl['levelid']=="24")
				{
				?>
				<button type="button" class="btn btn-primary btn-mini" onclick="rcv_edit('<?php echo base64_encode($r['receipt_no']);?>')"><i class="icon-edit icon-large"></i></button>
				<?php
				}
				if($qsl['levelid']=="1" || $qsl['levelid']=="24")
				{
				?>
				<span style="float:right;cursor:pointer;" onclick="change_bill_amount('<?php echo $r['slno'];?>')"><i class="icon-edit icon-large"></i></span>
				<?php
				}
				?>
			</td>
				
		</tr>
		
		
		<?php
		$i++;
		}
		?>
		<tr>
			<td colspan="4" style="text-align:right;font-weight:bold">Total :</td>
			<td style="text-align:right;font-weight:bold"><?php echo number_format($vttl,2);?></td>
			<td colspan="2"></td>
		</tr>
	</table>
	<?php
}
if($type==555)
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
			<th>Order No</th>
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
			<td><?php echo $r['order_no'];?></td>
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
				<button type="button" class="btn btn-info btn-mini" onclick="rcv_item_edit('<?php echo base64_encode($r['order_no']);?>')"><i class="icon-edit icon-large"></i></button>
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
			<td colspan="6" style="text-align:right;font-weight:bold"> Total </td>
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
	$ord=$_POST['ord'];
	$ord=base64_decode($_POST['ord']);
	?>
	<input type="hidden" id="edit_ord" value="<?php echo $ord;?>" />
	<table class="table table-condensed table-bordered table-report" id="mytable">
		
		<tr>
			<th>#</th>
			<th>Description</th>
			<th>Batch</th>
			<th>Expiry</th>
			<th style="text-align:right;">Quantity</th>
			<th style="text-align:right;">Free</th>
			<th style="text-align:right;">GST %</th>
			<th style="text-align:right;">Pkd Qnt</th>
			<th style="text-align:right;">Strip MRP</th>
			<th style="text-align:right;">Strip Cost</th>
			<th style="text-align:right;">Discount %</th>
			<th style="text-align:right;">Amount</th>
			<th>Remove</th>
		</tr>
		<?php
		$j=1;
		$q=mysqli_query($link,"SELECT * FROM `ph_challan_receipt_details` WHERE `order_no`='$ord'");
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`strip_quantity` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
			$gst_per=explode(".",$r['gst_per']);
			$gst_per=$gst_per[0];
			$dis_per=explode(".",$r['dis_per']);
			$dis_per=$dis_per[0];
		?>
		<tr class="all_tr">
			<td><?php echo $j;?></td>
			<td>
				<?php echo $itm['item_name'];?>
				<input type="hidden" value="<?php echo $r['item_id'];?>" class="itm_id" />
				<input type="hidden" value="<?php echo $r['item_id'].$r['recept_batch'];?>" class="test_id" />
			</td>
			<td>
				<?php echo $r['recept_batch'];?>
				<input type="hidden" value="<?php echo $r['recept_batch'];?>" class="bch" />
			</td>
			<td>
				<?php echo date("Y-m", strtotime($r['expiry_date']));?>
				<input type="hidden" class="exp_dt" value="<?php echo date("Y-m", strtotime($r['expiry_date']));?>" maxlength="7" />
			</td>
			<td style="text-align:right;">
				<?php echo $r['recpt_quantity']/$itm['strip_quantity'];?>
				<input type="hidden" class="qnt" value="<?php echo $r['recpt_quantity']/$itm['strip_quantity'];?>" />
			</td>
			<td style="text-align:right;">
				<?php echo $r['free_qnt'];?>
				<input type='hidden' value="<?php echo $r['free_qnt'];?>" class='free' />
			</td>
			<td style="text-align:right;">
				<?php echo $gst_per;?>
				<input type='hidden' value="<?php echo $gst_per;?>" class='gst' />
				<input type='hidden' value="<?php echo $r['gst_amount'];?>" class='all_gst' />
			</td>
			<td style="text-align:right;">
				<?php echo $itm['strip_quantity'];?>
				<input type="hidden" value="<?php echo $itm['strip_quantity'];?>" class="pkd_qnt" />
			</td>
			<td style="text-align:right;">
				<?php echo number_format($r['recpt_mrp']*$itm['strip_quantity'],2,'.','');?>
				<input type='hidden' value="<?php echo $r['recpt_mrp'];?>" class='mrp' />
				<input type='hidden' value="<?php echo $r['sale_price'];?>" class='unit_sale' />
			</td>
			<td style="text-align:right;">
				<?php echo number_format($r['recept_cost_price']*$itm['strip_quantity'],2,'.','');?>
				<input type='hidden' value="<?php echo $r['recept_cost_price'];?>" class='cost' />
				<input type='hidden' value="<?php echo ($r['recept_cost_price']/$r['recpt_quantity']);?>" class='unit_cost' />
			</td>
			<td style="text-align:right;">
				<?php echo $dis_per;?>
				<input type='hidden' value="<?php echo $dis_per;?>" class='disc' />
				<input type="hidden" value="<?php echo $r['dis_amt'];?>" class="d_amt" />
			</td>
			<td style="text-align:right;">
				<span><?php echo ($r['item_amount']);?></span>
				<input type="hidden" value="<?php echo ($r['item_amount']);?>" class="all_rate" />
			</td>
			<td style="text-align:center;"><i class="icon-ok icon-large text-success"></i></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
} // 10

if($type==11)
{
	$ord_no=base64_decode($_POST['ord_no']);
	$bildate=$_POST['billdate'];
	$billamt=$_POST['bill_amt'];
	$total=$_POST['total'];
	$disamt=$_POST['discount'];
	$gstamt=$_POST['all_gst'];
	$adjust=$_POST['adjust'];
	$ttlamt=$_POST['net_amt'];
	$userid=$_POST['user'];
	$all=$_POST['all'];
	$btn_val=$_POST['btn_val'];
	$entrydate=$date;
	
	$ord=mysqli_fetch_assoc(mysqli_query($link,"SELECT `supp_code`,`challan_no` FROM `ph_challan_receipt_master` WHERE `order_no`='$ord_no'"));
	if($ord)
	{
		$orderno=$ord_no;
		$spplrid=$ord['supp_code'];
		$splrblno=$ord['challan_no'];
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
				mysqli_query($link,"INSERT INTO `ph_challan_receipt_details`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','','$exp_dt','$date','$pkd_qnt','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$strip_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
				$vqnt=$qnt+$free;
				$vst=0;
				mysqli_query($link,"UPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
				$vstkqnt=0;
				
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc"));
				if($qrstkmaster['item_id']!='')
				{
					$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc";
					$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
					$rcvqnt=$qrstkmaster['recv_qnty']+$vqnt;
					mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where date='$date' and item_id='$itm' and batch_no='$bch'");
					$txt.="\nupdate inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where date='$date' and item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
					$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')";
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
					$txt.="\nselect * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc";
					$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
					mysqli_query($link,"insert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$qrstkmaster[closing_qnty]','$vqnt',0,'$vstkqnt')");
					$txt.="\ninsert into inv_mainstock_details(`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) values('$itm','$bch','$date','$qrstkmaster[closing_qnty]','$vqnt',0,'$vstkqnt')";
					mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
					$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
					mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
					$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`exp_date`) values('$itm','$bch','$vstkqnt','$exp_dt')";
				}
				mysqli_query($link,"INSERT INTO `inv_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('0','$orderno','$itm','$bch','$qrstkmaster[closing_qnty]','$vqnt','$vstkqnt','1','$date','$time','$userid')");
			}
		}
		mysqli_query($link,"UPDATE `ph_challan_receipt_master` SET `bill_amount`='$total', `gst_amt`='$gstamt',`dis_amt`='$disamt',`net_amt`='$ttlamt',`adjust_amt`='$adjust' WHERE `order_no`='$ord_no'");
		echo "Item Added";
	}
	else
	{
		echo "Error";
	}
} // 11

if($type==12)
{
	$id=$_POST['id'];
	$options="";
	$q=mysqli_query($link, "SELECT DISTINCT `batch_no` FROM `ph_stock_master` WHERE `item_code`='$id' AND `exp_date`>='$date' ORDER BY `exp_date` LIMIT 0,10");
	while($r=mysqli_fetch_assoc($q))
	{
		$options.="<option value='".$r['batch_no']."' />";
	}
	echo $options;
} // 12

if($type==13)
{
	$itm=$_POST['itm'];
	$options="";
	$q=mysqli_query($link, "SELECT `bill_no`, `expiry_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `cost_price`, `dis_per` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `SuppCode`!='0' ORDER BY `slno` DESC LIMIT 0,10");
	if(mysqli_num_rows($q)>0)
	{
	?>
	<table class="table table-condensed table-hover" style="margin:0;color:black;">
	<tr>
		<th>#</th>
		<th>Supplier</th>
		<th>Bill No</th>
		<th>Batch No</th>
		<th>Expiry Date</th>
		<th>Qnt</th>
		<th>Free</th>
		<th>MRP</th>
		<th>Cost</th>
		<th>Discount %
		<span style="float:right;"><button type="button" class="btn btn-mini btn-danger" onclick="rem_old_list(1)"><i class="icon-remove"></i></button></span>
		</th>
	</tr>
	<?php
	$j=1;
	while($r=mysqli_fetch_assoc($q))
	{
	//$pkd=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
	$sup=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[SuppCode]'"));
	?>
	<tr>
		<td><?php echo $j;?></td>
		<td><?php echo $sup['name'];?></td>
		<td><?php echo $r['bill_no'];?></td>
		<td><?php echo $r['recept_batch'];?></td>
		<td><?php echo date("Y-m", strtotime($r['expiry_date']));?></td>
		<td><?php echo number_format($r['recpt_quantity']/$r['strip_quantity'],0);?></td>
		<td><?php echo number_format($r['free_qnt']/$r['strip_quantity'],0);?></td>
		<td><?php echo number_format($r['recpt_mrp']*$r['strip_quantity'],2);?></td>
		<td><?php echo $r['cost_price'];?></td>
		<td><?php echo number_format($r['dis_per'],0)." %";?></td>
	</tr>
	<?php
	$j++;
	}
	?>
	</table>
	<?php
	}
} // 13

if($type==14)
{
	$id=$_POST['id'];
	$options="";
	//$q=mysqli_query($link, "SELECT DISTINCT `batch_no` FROM `ph_stock_master` WHERE `item_code`='$id' AND `exp_date`>='$date' ORDER BY `exp_date` LIMIT 0,10");
	$q=mysqli_query($link, "SELECT DISTINCT `recept_batch` AS `batch_no` FROM `inv_main_stock_received_detail` WHERE `item_id`='$id' AND `SuppCode`!='0' AND `expiry_date`>='$date' ORDER BY `slno` DESC LIMIT 0,10");
	while($r=mysqli_fetch_assoc($q))
	{
		$options.="<option value='".$r['batch_no']."' />";
	}
	echo $options;
} // 14

if($type==15)
{
	$itm=$_POST['itm'];
	$options="";
	$q=mysqli_query($link, "SELECT `bill_no`, `expiry_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `cost_price`, `dis_per` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `SuppCode`!='0' ORDER BY `slno` DESC LIMIT 0,10");
	if(mysqli_num_rows($q)>0)
	{
	?>
	<table class="table table-condensed table-hover" style="margin:0;color:black;">
	<tr>
		<th>#</th>
		<th>Supplier</th>
		<th>Bill No</th>
		<th>Batch No</th>
		<th>Expiry Date</th>
		<th>Qnt</th>
		<th>Free</th>
		<th>MRP</th>
		<th>Cost</th>
		<th>Discount %
		<span style="float:right;"><button type="button" class="btn btn-mini btn-danger" onclick="rem_old_list(1)"><i class="icon-remove"></i></button></span>
		</th>
	</tr>
	<?php
	$j=1;
	while($r=mysqli_fetch_assoc($q))
	{
	//$pkd=mysqli_fetch_assoc(mysqli_query($link,"SELECT `strip_quantity` FROM `item_master` WHERE `item_id`='$itm'"));
	$sup=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `inv_supplier_master` WHERE `id`='$r[SuppCode]'"));
	?>
	<tr id="bchtr<?php echo $r['recept_batch'];?>">
		<td><?php echo $j;?></td>
		<td><?php echo $sup['name'];?></td>
		<td><?php echo $r['bill_no'];?></td>
		<td><?php echo $r['recept_batch'];?><input type="hidden" value="<?php echo $r['recept_batch'];?>" /></td>
		<td><?php echo date("Y-m", strtotime($r['expiry_date']));?><input type="hidden" value="<?php echo date("Y-m", strtotime($r['expiry_date']));?>" /></td>
		<td><?php echo number_format($r['recpt_quantity']/$r['strip_quantity'],0);?></td>
		<td><?php echo number_format($r['free_qnt']/$r['strip_quantity'],0);?></td>
		<td><?php echo number_format($r['recpt_mrp']*$r['strip_quantity'],2);?><input type="hidden" value="<?php echo $r['recpt_mrp']*$r['strip_quantity'];?>" /></td>
		<td><?php echo number_format($r['cost_price'],2,'.','');?><input type="hidden" value="<?php echo number_format($r['cost_price'],2,'.','');?>" /></td>
		<td><?php echo number_format($r['dis_per'],0)." %";?></td>
	</tr>
	<?php
	$j++;
	}
	?>
	</table>
	<?php
	}
} // 15

if($type==16)
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
	$user=$_POST['user'];
	
	mysqli_query($link,"INSERT INTO `ph_challan_receipt_details_temp`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `strip_quantity`, `recpt_quantity`, `free_qnt`, `recept_batch`, `supp_code`, `recpt_mrp`, `recept_cost_price`, `cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$user','$bill_no','$itm_id','','$exp_dt','$date','$pkd_qnt','$qnt','$free','$bch','$supp','$mrp','$unit_cost','$cost','$unit_sale','$amt','$disc','$d_amt','$gst','$gstamt')");
} // 16

if($type==17)
{
	$supp=$_POST['supp'];
	$bill_no=$_POST['bill_no'];
	$user=$_POST['user'];
	$qry="SELECT * FROM `ph_challan_receipt_details_temp` WHERE `bill_no`='$bill_no' AND `supp_code`='$supp'";
	$q=mysqli_query($link,$qry);
	if(mysqli_num_rows($q)>0)
	{
		?>
	<table class="table table-condensed table-bordered table-report" id="mytable">
		<tbody>
			<tr>
				<th width="5%">#</th>
				<th>Description</th>
				<th>Batch</th>
				<th>Expiry</th>
				<th style="text-align:right;">Quantity</th>
				<th style="text-align:right;">Free</th>
				<th style="text-align:right;">GST %</th>
				<th style="text-align:right;">Pkd Qnt</th>
				<th style="text-align:right;">Strip MRP</th>
				<th style="text-align:right;">Strip Cost</th>
				<th style="text-align:right;">Discount %</th>
				<th style="text-align:right;">Amount</th>
				<th width="5%">Remove</th>
			</tr>
		<?php
		$j=1;
		$tot=0;
		while($r=mysqli_fetch_assoc($q))
		{
			$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
			?>
			<tr class="all_tr">
				<td><?php echo $j;?></td>
				<td><b><?php echo $itm['item_name'];?></b><input type="hidden" value="<?php echo $r['item_id'];?>" class="itm_id"><input type="hidden" value="<?php echo $r['item_id'].$r['recept_batch'];?>" class="test_id"></td>
				<td><?php echo $r['recept_batch'];?><input type="hidden" value="<?php echo $r['recept_batch'];?>" class="bch"></td>
				<td><?php echo date("Y-m", strtotime($r['expiry_date']));?><input type="hidden" value="<?php echo date("Y-m", strtotime($r['expiry_date']));?>" class="exp_dt"></td>
				<td style="text-align:right;"><input type="text" value="<?php echo $r['recpt_quantity'];?>" class="qnt span1" onkeyup="chk_num(this,event);manage_qnt(this,event)"></td>
				<td style="text-align:right;"><input type="text" value="<?php echo $r['free_qnt'];?>" class="free span1" onkeyup="chk_num(this,event);manage_free(this,event)"></td>
				<td style="text-align:right;"><?php echo number_format($r['gst_per'],0);?><input type="hidden" value="<?php echo number_format($r['gst_per'],0);?>" class="gst"><input type="hidden" value="<?php echo $r['gst_amount'];?>" class="all_gst"></td>
				<td style="text-align:right;"><?php echo $r['strip_quantity'];?><input type="hidden" value="<?php echo $r['strip_quantity'];?>" class="pkd_qnt"></td>
				<td style="text-align:right;"><input type="text" value="<?php echo $r['recpt_mrp'];?>" class="mrp span1" onkeyup="chk_dec(this,event);manage_qnt(this,event)"><input type="hidden" value="<?php echo $r['sale_price'];?>" class="unit_sale"></td>
				<td style="text-align:right;"><input type="text" value="<?php echo $r['cost_price'];?>" class="cost span1" onkeyup="chk_dec(this,event);manage_qnt(this,event)"><input type="hidden" value="<?php echo $r['recept_cost_price'];?>" class="unit_cost"></td>
				<td style="text-align:right;"><input type="text" value="<?php echo number_format($r['dis_per'],0);?>" class="disc span1" onkeyup="chk_num(this,event);manage_qnt(this,event)"><input type="hidden" value="<?php echo $r['dis_amt'];?>" class="d_amt"></td>
				<td style="text-align:right;"><span class="rate_str"><?php echo $r['item_amount'];?></span><input type="hidden" value="<?php echo $r['item_amount'];?>" class="all_rate"></td><td style="text-align:center;"><span onclick="itm_rem(this);$(this).parent().parent().remove();set_sl();set_amt();item_focus()" style="cursor:pointer;color:#c00;"><i class="icon-remove icon-large text-danger"></i></span></td>
			</tr>
			<?php
			$tot+=$r['item_amount'];
			$j++;
		}
		?>
			<tr id="new_tr">
				<th colspan="11" style="text-align:right;">Total</th>
				<td style="text-align:right;" id="final_rate"><?php echo number_format($tot,2);?></td>
				<td></td>
			</tr>
		</tbody>
	</table>
		<?php
	}
} // 17

if($type==18)
{
	$itm=$_POST['itm'];
	$bch=$_POST['bch'];
	$supp=$_POST['supp'];
	$bill_no=$_POST['bill_no'];
	mysqli_query($link,"DELETE FROM `ph_challan_receipt_details_temp` WHERE `bill_no`='$bill_no' AND `item_id`='$itm' AND `recept_batch`='$bch' AND `supp_code`='$supp'");
} // 18

if($type==19)
{
	$val=$_POST['val'];
	$rcv=$_POST['rcv'];
	$ord=$_POST['ord'];
	$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from ph_challan_receipt_master WHERE order_no='$ord'")); 
	$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_master where id='$qrcv[supp_code]'"));
	$qemname=mysqli_fetch_array(mysqli_query($link,"select a.name from employee a,ph_challan_receipt_master b where a.emp_id=b.user and b.order_no='$ord'")); 
	$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name"));

	if($splr['igst']==1)
	{
		$gsttxt="IGST";
	}
	else
	{
		$gsttxt="GST";
	}
	?>
	<table width="100%">
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">#</td>
				<td style="font-weight:bold;font-size:13px">Item Code</td>
				<td style="font-weight:bold;font-size:13px">Item Name</td>
				<td style="font-weight:bold;font-size:13px">Batch No</td>
				<td style="font-weight:bold;font-size:13px">Exp Date</td>
				<td style="font-weight:bold;font-size:13px">MRP</td>
				<td style="font-weight:bold;font-size:13px">Rate</td>
				<td style="font-weight:bold;font-size:13px"><?php echo $gsttxt;?> %</td>
				<td style="font-weight:bold;font-size:13px">Dis %</td>
				<td align="right" style="font-weight:bold;font-size:13px">Qnty.</td>
				<td align="right" style="font-weight:bold;font-size:13px">Free.</td>
				<td align="right" style="font-weight:bold;font-size:13px">Amount.</td>
           </tr>
             <?php 
              $i=1;
              $tot=0;
              
			  $qrslctitm=mysqli_query($link,"SELECT a.*,b.item_name from ph_challan_receipt_details a,item_master b,ph_challan_receipt_master c WHERE a.order_no='$ord' and a.item_id=b.item_id and a.order_no=c.order_no and c.supp_code='$qrcv[supp_code]' and c.date='$qrcv[date]' and b.`item_name` like '$val%'");  // ORDER BY b.item_name 
			  
			  
			  while($qrslctitm1=mysqli_fetch_array($qrslctitm)){
				$vitemamount=0;  
				$vitemamount=$qrslctitm1['recpt_quantity']*$qrslctitm1['recept_cost_price'];  
				$vitmttl=$vitmttl+$vitemamount;
				$vgstamt=$vgstamt+$qrslctitm1['gst_amount'];
			
			 ?>
             <tr class="line" onclick="chk(this)">
					<td style="font-size:13px"><?php echo $i;?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_id'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['item_name'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['recept_batch'];?></td>
					<td style="font-size:13px"><?php echo date("Y-m", strtotime($qrslctitm1['expiry_date']));?></td>
					<td style="font-size:13px"><?php echo number_format($qrslctitm1['recpt_mrp']*$qrslctitm1['strip_quantity'],2,'.','');?></td>
					<td style="font-size:13px"><?php echo number_format($qrslctitm1['cost_price'],2,'.','');?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['gst_per'];?></td>
					<td style="font-size:13px"><?php echo $qrslctitm1['dis_per'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['recpt_quantity']/$qrslctitm1['strip_quantity'];?></td>
					<td align="right" style="font-size:13px"><?php echo $qrslctitm1['free_qnt']/$qrslctitm1['strip_quantity'];?></td>
					<td align="right" style="font-size:13px"><?php echo number_format($vitemamount,2);?></td>
                
             </tr>  
             
             
                    
                  <?php
			$i++ ;}
			
			  /*$cgst=$vgstamt/2;
			  $tot1=$vitmttl+$cgst+$cgst;
			  $tot=round($tot1);*/
			  
			  $cgst=$vgstamt;
			  $tot1=$vitmttl+$cgst+$qrcvcharge['delivery_charge']-$qrcv['dis_amt'];
			  $tot=round($tot1);
			  ?>
             
             
               
              
<tr class="line">   
	      
	<td colspan="11" align="right" style="font-size:13px;font-weight:bold">Total :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($vitmttl,2);?></td>
</tr>

<tr class="line">   
	<td colspan="11" align="right" style="font-size:13px;font-weight:bold"><?php echo $gsttxt;?> :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

</tr>
<?php
if($qrcv['dis_amt']>0)
{
?>
<tr class="line">   
	<td colspan="11" align="right" style="font-size:13px;font-weight:bold">Discount:</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['dis_amt'],2);?></td>

</tr>
<?php
}
if($qrcv['adjust_amt']>0)
{
?>
<tr class="line">   
	<td colspan="11" align="right" style="font-size:13px;font-weight:bold">Adjustment:</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['adjust_amt'],2);?></td>

</tr>
<?php
}
?>
<!--<tr class="line">   
	<td colspan="11" align="right" style="font-size:13px;font-weight:bold">CGST :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

</tr>

<tr class="line">   
	<td colspan="11" align="right" style="font-size:13px;font-weight:bold">SGST :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($cgst,2);?></td>

</tr>
<tr class="line">   
	<td colspan="11" align="right" style="font-size:13px;font-weight:bold">Transport Charge :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcvcharge['delivery_charge'],2);?></td>

</tr>-->

<tr class="line">   
	<td colspan="11" align="right" style="font-size:13px;font-weight:bold">Net Amount(Rounded) :</td>
	<td align="right" style="font-size:13px;font-weight:bold"><?php echo number_format($qrcv['net_amt'],2);?></td>

</tr>
<tr class="no_line">
	<td style="font-size:13px;font-weight:bold;">Class %</td>
	<td style="font-size:13px;font-weight:bold;">Amount</td>
	<td style="font-size:13px;font-weight:bold;">Gst</td>
	<td colspan="8" style="font-size:13px"></td>
</tr>
<?php
$all_gst=array(0,5,12,18,28);
foreach($all_gst as $gst)
{
$qamt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(sum(item_amount),0) as maxamt, ifnull(sum(`gst_amount`),0) as maxgst FROM ph_challan_receipt_details WHERE `order_no`='$ord' and `gst_per`='$gst'"));
?>
<tr class="no_line">
	<td style="font-size:13px"><?php echo $gst." %";?></td>
	<td style="font-size:13px"><?php echo number_format($qamt['maxamt'],2);?></td>
	<td style="font-size:13px"><?php echo number_format($qamt['maxgst'],2);?></td>
	<td colspan="9" style="font-size:13px"></td>
</tr>
<?php
}
?>
<tr>
	<td colspan="11">&nbsp;</td>
</tr>
<tr class="no_line">
	<td colspan="5" style="font-size:13px">Received By : <?php echo $qemname['name'];?></td>
	
	<td></td>
	<td></td>
	<td colspan="4" style="font-size:13px;text-align:right;">Authourised Signatory </td>
	
</tr>

</table>
	<?php
} // 19

if($type==20)
{
	$supp=$_POST['supp'];
	$bill_no=$_POST['bill_no'];
	$user=$_POST['user'];
	$v=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `ph_challan_receipt_master` WHERE `challan_no`='$bill_no' AND `supp_code`='$supp'"));
	if($v)
	{
		echo 1;
	}
	else
	{
		echo 0;
	}
} // 20

if($type==999)
{
	$id=$_POST['id'];
} // 999
?>
