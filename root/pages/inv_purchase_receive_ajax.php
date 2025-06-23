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

function text_query($txt)
{
	if($txt)
	{
		$myfile = file_put_contents('log/inv_received_txt.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
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
	$ord_no=mysqli_real_escape_string($link,$_POST['ord_no']);
	$spplrid=$_POST['supp'];
	$splrblno=$_POST['bill_no'];
	$bildate=$_POST['billdate'];
	$billamt=$_POST['bill_amt'];
	$total=$_POST['total'];
	$disamt=$_POST['discount'];
	$gstamt=$_POST['all_gst'];
	$ttlamt=$_POST['net_amt'];
	$userid=$_POST['user'];
	$gds_rcv_no=$_POST['gds_rcv_no'];
	$delivrychrge=$_POST['delivrychrge'];
	$all=$_POST['all'];
	$btn_val=$_POST['btn_val'];
	$entrydate=$date;
	$rad=0;
	$adj=0;
	$ph=1;
	$vdel=0;
	
	$count_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`receipt_no`) as tot FROM `inv_main_stock_received_master`"));
	$bill_tot_num=$count_qry["tot"];
	$bill_no=$bill_tot_num+1;
	
	$rcv_no="RCV".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
	
	
	if(mysqli_query($link,"INSERT INTO `inv_purchase_receipt_master`(`order_no`, `rcv_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `fid`, `user`, `time`, `adjust_type`, `adjust_amt`) VALUES ('$ord_no','$rcv_no','$bildate','$date','$billamt','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','0','$userid','$time','$rad','$adj')"))
	{
		mysqli_query($link,"INSERT INTO `inv_main_stock_received_master`(`order_no`, `receipt_no`, `bill_date`, `recpt_date`, `bill_amount`, `gst_amt`, `dis_amt`, `net_amt`, `supp_code`, `bill_no`, `user`, `time`, `adjust_type`,`adjust_amt`,`goods_rcv_no`,`del`) VALUES ('$ord_no','$rcv_no','$bildate','$date','$billamt','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','$userid','$time','$rad','$adj','$gds_rcv_no','$vdel')");
		
		mysqli_query($link,"insert into inv_main_stock_received_charge_master( `receipt_no`, `bill_date`, `recpt_date`, `freight_charge`, `delivery_charge`, `packing_charge`, `supp_code`, `bill_no`, `remarks`) values('$rcv_no','$bildate','$date',0,'$delivrychrge',0,'$spplrid','$splrblno','')");
		
		$balance_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$spplrid' ORDER BY `slno` DESC LIMIT 0,1"));
		$final_balance=$balance_det['balance_amt']+$ttlamt;
		mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$rcv_no','$spplrid','$ttlamt','0','0','$final_balance','1','$date','$time','$user')");
		
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
			$qnt=$qnt*$pkd_qnt;
			$free=$free*$pkd_qnt;
			/*----------------------------for central store-------------------------------*/
			if($itm && $qnt)
			{
				mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ord_no','$rcv_no','$splrblno','$itm','','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
				$txt.="\nINSERT INTO `inv_main_stock_received_detail`(`order_no`, `rcv_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ord_no','$rcv_no','$splrblno','$itm','','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')";
				
				$vqnt=$qnt+$free;
				$vst=0;
				mysqli_query($link,"UPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
				$txt.="\nUPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'";
				$vstkqnt=0;
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc "));
				$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc";
				if($qrstkmaster['item_id']!='')
				{
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
				mysqli_query($link,"UPDATE `inv_purchase_order_details` SET `stat`='1' WHERE `order_no`='$ord_no' AND `item_id`='$itm'");
				$txt.="\n-------------------------------------------------------------------------------------------";
			}
			/*----------------------------for pharmacy-------------------------------*/
			/*
			if($itm && $bch && $qnt)
			{
				mysqli_query($link,"INSERT INTO `inv_purchase_receipt_details`(`order_no`, `rcv_no`, `bill_no`, `item_code`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `fid`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ord_no','$rcv_no','$splrblno','$itm','','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$unit_sale','0','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
				mysqli_query($link,"UPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
				
				$stock=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$ph' and item_code='$itm' and batch_no='$bch' and date='$date'"));
				$vqnt=$qnt+$free;
				$vst=0;
				if($stock['item_code']!='')
				{
					$vstkqnt=$stock['s_remain']+$vqnt;
					$rcvqnt=$stock['added']+$vqnt;
					mysqli_query($link,"update ph_stock_process set s_remain='$vstkqnt',added='$rcvqnt' where substore_id='$ph' and item_code='$itm' and  batch_no='$bch' and date='$date'");
					mysqli_query($link,"delete from ph_stock_master where substore_id='$ph' and item_code='$itm' and batch_no='$bch'");
					mysqli_query($link,"insert into ph_stock_master(substore_id,item_code,batch_no,quantity,mfc_date,exp_date) values('$ph','$itm','$bch','$vstkqnt','$stock[manufactre_date]','$exp_dt')");
				}
				else
				{
					$stock=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$ph' and item_code='$itm' and batch_no='$bch' order by slno desc"));
					$vstkqnt=$stock['s_remain']+$vqnt;
					mysqli_query($link,"insert into ph_stock_process(substore_id,process_no,item_code,batch_no,s_available,added,sell,s_remain,date) values('$ph','$rcv_no','$itm','$bch','$stock[s_remain]','$vqnt',0,'$vstkqnt','$date')");
					mysqli_query($link,"delete from ph_stock_master where substore_id='$ph' and item_code='$itm' and batch_no='$bch'");
					mysqli_query($link,"insert into ph_stock_master(substore_id,item_code,batch_no,quantity,mfc_date,exp_date) values('$ph','$itm','$bch','$vstkqnt','$stock[manufactre_date]','$exp_dt')");
				}
				mysqli_query($link,"UPDATE `ph_purchase_order_details` SET `stat`='1' WHERE `order_no`='$ord_no' AND `item_id`='$itm'");
			}
			*/
		}
		/*----------------------------for pharmacy-------------------------------*/
		//~ $chk_all_rcv=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_purchase_order_details` WHERE `order_no`='$ord_no' AND `stat`='0'"));
		//~ if(!$chk_all_rcv)
		//~ {
			//~ mysqli_query($link,"UPDATE `ph_purchase_order_master` SET `stat`='1' WHERE `order_no`='$ord_no'");
		//~ }
		/*----------------------------for central store-------------------------------*/
		$chk_all_rcv=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord_no' AND `stat`='0'"));
		if(!$chk_all_rcv)
		{
			mysqli_query($link,"UPDATE `inv_purchase_order_master` SET `stat`='1' WHERE `order_no`='$ord_no'");
		}
		echo "Saved";
	}
	else
	{
		echo "Error";
	}
}

if($type==2-2000)
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
			$txt="insert into inv_main_stock_received_master(`receipt_no`,`bill_date`,`recpt_date`,`bill_amount`,`gst_amt`,`dis_amt`,`net_amt`,`supp_code`,`bill_no`,`user`,`time`,`adjust_type`,`adjust_amt`) values('$orderno','$bildate','$entrydate','$billamt','$gstamt','$disamt','$ttlamt','$spplrid','$splrblno','$userid','$time','$rad','$adj')";
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
				$qnt=$qnt*$pkd_qnt;
				$free=$free*$pkd_qnt;
				if($itm && $bch && $qnt)
				{
					mysqli_query($link,"INSERT INTO `inv_main_stock_received_detail`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')");
					$txt.="\nINSERT INTO `inv_main_stock_received_detail`(`order_no`, `bill_no`, `item_id`, `manufactre_date`, `expiry_date`, `recpt_date`, `recpt_quantity`, `free_qnt`, `recept_batch`, `SuppCode`, `recpt_mrp`, `recept_cost_price`, `sale_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$orderno','$splrblno','$itm','','$exp_dt','$date','$qnt','$free','$bch','$spplrid','$mrp','$unit_cost','$unit_sale','$itm_amt','$dis_per','$dis_amt','$gst','$gst_amt')";
					
					$vqnt=$qnt+$free;
					$vst=0;
					mysqli_query($link,"UPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'");
					$txt.="\nUPDATE `item_master` SET `gst`='$gst',`strip_quantity`='$pkd_qnt' WHERE `item_id`='$itm'";
					$vstkqnt=0;
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc "));
					$txt.="\nselect * from inv_mainstock_details where date='$date' and item_id='$itm' and  batch_no='$bch' order by date desc";
					if($qrstkmaster['item_id']!='')
					{
						$vstkqnt=$qrstkmaster['closing_qnty']+$vqnt;
						$rcvqnt=$qrstkmaster['recv_qnty']+$vqnt;
						mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where date='$date' and item_id='$itm' and batch_no='$bch'");
						$txt.="\nupdate inv_mainstock_details set closing_qnty='$vstkqnt',recv_qnty='$rcvqnt' where date='$date' and item_id='$itm' and batch_no='$bch'";
						mysqli_query($link,"delete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'");
						$txt.="\ndelete from inv_maincurrent_stock where item_id='$itm' and batch_no='$bch'";
						mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`expiry_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
						$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`expiry_date`) values('$itm','$bch','$vstkqnt','$exp_dt')";
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
						mysqli_query($link,"insert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`expiry_date`) values('$itm','$bch','$vstkqnt','$exp_dt')");
						$txt.="\ninsert into inv_maincurrent_stock(`item_id`,`batch_no`,`closing_stock`,`expiry_date`) values('$itm','$bch','$vstkqnt','$exp_dt')";
					}
					$txt.="\n-------------------------------------------------------------------------------------------";
				}
			}
			text_query($txt);
		}
		echo "Saved";
	}
} // 2

if($type==3)
{
	$ord_no=mysqli_real_escape_string($link,$_POST['ord_no']);
	$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT `supplier_id` FROM `inv_purchase_order_master` WHERE `order_no`='$ord_no'"));
	if($sid)
	{
		$igst=mysqli_fetch_assoc(mysqli_query($link,"SELECT `igst` FROM `inv_supplier_master` WHERE `id`='$sid[supplier_id]'"));
		echo $sid['supplier_id']."@@".$igst['igst'];
	}
	else
	{
		echo "0@@0";
	}
} // 3

if($type==4)
{
	$val=mysqli_real_escape_string($link,$_POST['val']);
	$ord_no=mysqli_real_escape_string($link,$_POST['ord_no']);
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th width="5%">#</th><th>Item Name</th>
		</tr>
	<?php
		
		if(strlen($val)>0)
		{
			$q="SELECT i.`item_id`,i.`item_name`,i.`gst`,i.`strip_quantity` FROM `item_master` i, `inv_purchase_order_details` o WHERE i.`item_id`=o.`item_id` AND o.`order_no`='$ord_no' AND o.`stat`='0' AND i.`item_name` like '$val%' ORDER BY i.`item_name` LIMIT 0,20";
		}
		else
		{
			//$q="SELECT `item_id`,`item_name`,`gst`,`strip_quantity` FROM `ph_item_master` ORDER BY `item_name` LIMIT 0,20";
			$q="SELECT i.`item_id`,i.`item_name`,i.`gst`,i.`strip_quantity` FROM `item_master` i, `inv_purchase_order_details` o WHERE i.`item_id`=o.`item_id` AND o.`order_no`='$ord_no' AND o.`stat`='0' ORDER BY i.`item_name` LIMIT 0,20";
		}
		//echo $q;
		$d=mysqli_query($link, $q);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$gst=explode(".",$d1['gst']);
			$gst=$gst[0];
			?>
			<tr onclick="doc_load('<?php echo $d1['item_id'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['gst'];?>','<?php echo $d1['strip_quantity'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
				<td><?php echo $d1['item_id'];?></td>
				<td><?php echo $d1['item_name'];?>
					<div <?php echo "id=dvdoc".$i;?> style="display:none;">
					<?php echo "#".$d1['item_id']."#".$d1['item_name']."#".$gst."#".$d1['strip_quantity']."#";?>
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

if($type==999)
{
	$id=$_POST['id'];
} // 999
?>
