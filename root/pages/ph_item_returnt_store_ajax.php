<?php
include("../../includes/connection.php");

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}

$date=date('Y-m-d');
$time=date('H:i:s');
$type=$_POST['type'];

if($type==1)
{
	$supp=$_POST['supp'];
	$val=$_POST['val'];
	if(trim($_POST['typ'])=="item")
	{
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Item Code</th><th>Item Name</th><th>GST %</th><th>Stock</th>
			</tr>
	<?php
		
		if($val)
		{
			$q="select distinct b.`item_code` from item_master a, ph_stock_master b where a.`item_id`=b.`item_code` and a.item_name!='' and a.item_name like '$val%' and b.`quantity`>0 or a.`item_id`=b.`item_code` and a.item_name!='' and a.item_id like '$val%' and b.`quantity`>0 order by a.item_name limit 0,30";
			
			//~ $q="select distinct b.`item_id` from item_master a, inv_main_stock_received_detail b, inv_maincurrent_stock c where";
			//~ $q.=" a.item_id=b.item_id and b.SuppCode='$supp' and a.item_id=c.item_id and c.closing_stock>0 and a.item_name!='' and a.item_name like '$val%'";
			//~ $q.=" or a.item_id=b.item_id and b.SuppCode='$supp' and a.item_id=c.item_id and c.closing_stock>0 and a.item_name!='' and a.item_id like '$val%'";
			//~ $q.=" order by a.item_name limit 0,30";
		}
		else
		{
			$q="select distinct b.`item_code` from item_master a, ph_stock_master b where a.`item_id`=b.`item_code` and a.item_name!='' and b.`quantity`>0 order by a.item_name limit 0,30";
			//~ $q="select distinct b.`item_id` from item_master a, inv_main_stock_received_detail b, inv_maincurrent_stock c where a.item_name!='' and a.item_id=b.item_id and b.SuppCode='$supp' and a.item_id=c.item_id and c.closing_stock>0 ";
			//~ $q.=" order by a.item_name limit 0,30";
		}
		//echo $q;
		$d=mysqli_query($link, $q);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `item_master` WHERE `item_id`='$d1[item_code]'"));
			$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS stock FROM `ph_stock_master` WHERE `item_code`='$d1[item_code]'"));
			$gst=explode(".",$itm['gst']);
			$gst=$gst[0];
			?>
			<tr onclick="doc_load('<?php echo $itm['item_code'];?>','<?php echo $itm['item_name'];?>','<?php echo $itm['gst_percent'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
				<td><?php echo $d1['item_code'];?></td>
				<td><?php echo $itm['item_name'];?>
					<div <?php echo "id=dvdoc".$i;?> style="display:none;">
					<?php echo "#".$d1['item_code']."#".$itm['item_name']."#".$gst;?>
					</div>
				</td>
				<td><?php echo $itm['gst'];?></td>
				<td><?php echo $stk['stock'];?></td>
			</tr>
		<?php
			$i++;
		}
		?>
		</table>
	<?php
	}
} // 1

if($type==2)
{
	$supp=$_POST['supp'];
	$reason=$_POST['reason'];
	$final_gst=$_POST['final_gst'];
	$final_rate=$_POST['final_rate'];
	$user=$_POST['user'];
	$all=$_POST['all'];
	$net_amount=$final_gst+$final_rate;
	$ph=1;
	
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`returnr_no`) as tot FROM `inv_item_return_supplier_master`"));
	$bill_num=$bill_no_qry["tot"];
	$bill_no=$bill_num+1;
	
	$ret_no="RET".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
	
	if(mysqli_query($link,"INSERT INTO `inv_item_return_supplier_master`(`returnr_no`, `supplier_id`, `amount`, `gst_amount`, `net_amount`, `date`, `stat`, `del`, `user`, `time`) values('$ret_no','$supp','$final_rate','$final_gst','$net_amount','$date',0,0,'$user','$time')"))
	{
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@@",$a);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			$free=$v[3];
			$cost=$v[4];
			$amt=$v[5];
			$gst_per=$v[6];
			$gst_amt=$v[7];
			if($itm && $qnt)
			{
				$vqnt=$qnt+$free;
				$itm_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recpt_mrp`,`recept_cost_price`,`expiry_date`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `recept_batch`='$bch' AND `SuppCode`='$supp'"));
				$vitmamt=$cost*$qnt;
				
				mysqli_query($link,"INSERT INTO `inv_item_return_supplier_detail`(`returnr_no`, `reason`, `item_id`, `expiry_date`, `date`, `quantity`, `free_qnt`, `batch_no`, `supplier_id`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ret_no','$reason','$itm','$itm_det[expiry_date]','$date','$qnt','$free','$bch','$supp','$itm_det[recpt_mrp]','$cost','$vitmamt','$itm_det[dis_per]','$itm_det[dis_amt]','$gst_per','$gst_amt')");
				
				$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where  date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
				if($stk)// if data found on same date
				{
					$s_remain=$stk['s_remain']-$vqnt;
					$return_supplier=$stk['return_supplier']+$vqnt;
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$ret_no','$itm','$bch','$vqnt','4','$date','$time','$user')");
					mysqli_query($link,"update ph_stock_process set s_remain='$s_remain',return_supplier='$return_supplier' where date='$date' and item_code='$itm' and batch_no='$bch' and substore_id='$ph'");
					mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`='$s_remain' WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$ph'");
				}
				else/// if data not found on same date
				{
					$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where item_code='$itm' and  batch_no='$bch' and substore_id='$ph' order by slno desc limit 0,1"));
					$s_available=$stk['s_available'];
					$s_remain=$stk['s_remain']-$vqnt;
					$return_supplier=$vqnt;
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$ret_no','$itm','$bch','$vqnt','4','$date','$time','$user')");
					mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`, `process_no`, `item_code`, `batch_no`, `s_available`, `added`, `sell`, `return_cstmr`, `return_supplier`, `s_remain`, `date`) VALUES ('$ph','RETURN','$itm','$bch','$s_available','0','0','0','$return_supplier','$s_remain','$date')");
					mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`='$s_remain' WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$ph'");
				}
			}
		}
		echo "Done";
	}
	else
	{
		echo "Error";
	}
} // 2

if($type==3)
{
	$itm=$_POST['itm'];
	$bch=$_POST['bch'];
	$supp=$_POST['supp'];
	$d=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `recept_batch`='$bch' AND `SuppCode`='$supp' ORDER BY `order_no` DESC "));
	$dt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_date` FROM `ph_purchase_receipt_master` WHERE `order_no`='$d[order_no]'"));
	if($d)
	{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Invoice</th>
			<th>Invoice date</th>
			<th>Rcv Qty</th>
			<th>Free Qty</th>
			<th>Received</th>
		</tr>
		<tr>
			<td><?php echo $d['bill_no'];?></td>
			<td><?php echo convert_date($dt['bill_date']);?></td>
			<td><?php echo $d['recpt_quantity'];?></td>
			<td><?php echo $d['free_qnt'];?></td>
			<td><?php echo convert_date($d['recpt_date']);?></td>
		</tr>
	</table>
	<?php
	}
} // 3

if($type==4)
{
	$item_id=$_POST['item_id'];
	$supp=$_POST['supp'];
	$val=$_POST['val'];
	
	if(trim($_POST['typ'])=="batch")
	{
		$item_id=$_POST['item_id'];
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Batch No</th><th>Stock</th><th>Rate</th><th>Expiry</th>
			</tr>
	<?php
		
		if($val)
		{
			$d=mysqli_query($link, "SELECT * FROM `ph_stock_master` WHERE `item_code`='$item_id' AND `batch_no` like '$val%' AND `quantity`>0 ORDER BY `exp_date`");
		}
		else
		{
			$d=mysqli_query($link, "SELECT * FROM `ph_stock_master` WHERE `item_code`='$item_id' AND `quantity`>0 ORDER BY `exp_date`");
		}
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$cost=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recept_cost_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$item_id' AND `recept_batch`='$d1[batch_no]' AND `SuppCode`='$supp' ORDER BY `slno` DESC LIMIT 0,1"));
			if($cost)
			{
				$cost_price=$cost['recept_cost_price'];
			}
			else
			{
				//$cost=mysqli_fetch_assoc(mysqli_query($link,"SELECT `cost_price` FROM `inv_supplier_items` WHERE `item_id`='$item_id' AND `supp_id`='$supp'"));
				$cost=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recept_cost_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$item_id' AND `recept_batch`='$d1[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
				$cost_price=$cost['recept_cost_price'];
			}
			
			if($d1['exp_date']!="0000-00-00")
			{
				$exp_dt=date('Y-m', strtotime($d1['exp_date']));
			}
			else
			{
				$exp_dt="";
			}
			if($d1['batch_no']=="")
			{
				$bch_val=0;
			}
			else
			{
				$bch_val=1;
			}
			?>
			<tr onClick="hguide_load('<?php echo $d1['batch_no'];?>','<?php echo $d1['quantity'];?>','<?php echo $exp_dt;?>','<?php echo $bch_val;?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
				<td><?php echo $d1['batch_no'];?></td>
				<td>
					<?php echo $d1['quantity'];?>
					<div <?php echo "id=dvhguide".$i;?> style="display:none;">
						<?php echo "#".$d1['batch_no']."#".$d1['quantity']."#".$cost_price."#".$exp_dt."#".$bch_val;?>
					</div>
				</td>
				<td><?php echo $cost_price;?></td>
				<td><?php echo $exp_dt;?></td>
			</tr>
		<?php
			$i++;
		}
		?>
		</table>
	<?php
	}
} // 4

if($type==9899)
{
	
}
?>
