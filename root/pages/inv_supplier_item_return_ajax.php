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
			//$q="select a.* from item_master a, ph_stock_master b where a.item_name!='' and a.item_name like '$dname%' or a.item_name!='' and a.item_id like '$dname%' or a.item_name!='' and a.short_name like '$dname%' order by a.item_name limit 0,30";
			
			$q="select distinct b.`item_id` from item_master a, inv_main_stock_received_detail b, inv_maincurrent_stock c where";
			$q.=" a.item_id=b.item_id and b.SuppCode='$supp' and a.item_id=c.item_id and c.closing_stock>0 and a.item_name!='' and a.item_name like '$val%'";
			$q.=" or a.item_id=b.item_id and b.SuppCode='$supp' and a.item_id=c.item_id and c.closing_stock>0 and a.item_name!='' and a.item_id like '$val%'";
			$q.=" order by a.item_name limit 0,30";
		}
		else
		{
			$q="select distinct b.`item_id` from item_master a, inv_main_stock_received_detail b, inv_maincurrent_stock c where a.item_name!='' and a.item_id=b.item_id and b.SuppCode='$supp' and a.item_id=c.item_id and c.closing_stock>0 ";
			$q.=" order by a.item_name limit 0,30";
		}
		//echo $q;
		$d=mysqli_query($link, $q);
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$itm=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `item_master` WHERE `item_id`='$d1[item_id]'"));
			$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`closing_stock`),0) AS stock FROM `inv_maincurrent_stock` WHERE `item_id`='$d1[item_id]'"));
			$gst=explode(".",$itm['gst']);
			$gst=$gst[0];
			?>
			<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>','<?php echo $itm['gst_percent'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
				<td><?php echo $itm['item_id'];?></td>
				<td><?php echo $itm['item_name'];?>
					<div <?php echo "id=dvdoc".$i;?> style="display:none;">
					<?php echo "#".$itm['item_id']."#".$itm['item_name']."#".$gst;?>
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
				$itm_det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recpt_mrp`,`recept_cost_price`,`expiry_date`,`dis_per`,`dis_amt`,`gst_per`,`gst_amount` FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `recept_batch`='$bch' AND `SuppCode`='$supp'"));
				$vitmamt=$cost*$qnt;
				$vst=0;
				$vstkqnt=0;
				$gstamt=0;
				
				mysqli_query($link,"INSERT INTO `inv_item_return_supplier_detail`(`returnr_no`, `reason`, `item_id`, `expiry_date`, `date`, `quantity`, `free_qnt`, `batch_no`, `supplier_id`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$ret_no','$reason','$itm','$itm_det[expiry_date]','$date','$qnt','$free','$bch','$supp','$itm_det[recpt_mrp]','$cost','$vitmamt','$itm_det[dis_per]','$itm_det[dis_amt]','$gst_per','$gst_amt')");
				
				$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where date='$date' and item_id='$itm' and batch_no='$bch' order by date desc "));
				if($qrstkmaster['item_id']!='')
				{
					$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
					$isuqnt=$qrstkmaster['issu_qnty']+$vqnt;
					mysqli_query($link,"update inv_mainstock_details set closing_qnty='$vstkqnt',issu_qnty='$isuqnt' where  date='$date' and item_id='$itm' and batch_no='$bch' ");
					mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$itm' and batch_no='$bch'");
				}
				else///for if data not found
				{
					$qrstkmaster=mysqli_fetch_array(mysqli_query($link,"select * from inv_mainstock_details where item_id='$itm' and batch_no='$bch' order by slno desc"));
					$vstkqnt=$qrstkmaster['closing_qnty']-$vqnt;
					mysqli_query($link,"insert into inv_mainstock_details (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) value('$itm','$bch','$date','$qrstkmaster[closing_qnty]',0,'$vqnt','$vstkqnt') ");
					mysqli_query($link,"update inv_maincurrent_stock set closing_stock='$vstkqnt' where item_id='$itm' and batch_no='$bch'");
				}
				mysqli_query($link,"INSERT INTO `inv_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('0','$ret_no','$itm','$bch','$qrstkmaster[closing_qnty]','$vqnt','$vstkqnt','9','$date','$time','$user')");
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
	$d=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_main_stock_received_detail` WHERE `item_id`='$itm' AND `recept_batch`='$bch' AND `SuppCode`='$supp' ORDER BY `order_no` DESC "));
	$dt=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_date` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$d[rcv_no]'"));
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
			$d=mysqli_query($link, "SELECT * FROM `inv_maincurrent_stock` WHERE `item_id`='$item_id' AND `batch_no` like '$val%' AND `closing_stock`>0 ORDER BY `exp_date`");
		}
		else
		{
			$d=mysqli_query($link, "SELECT * FROM `inv_maincurrent_stock` WHERE `item_id`='$item_id' AND `closing_stock`>0 ORDER BY `exp_date`");
		}
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			$cost=mysqli_fetch_assoc(mysqli_query($link,"SELECT `recept_cost_price` FROM `inv_main_stock_received_detail` WHERE `item_id`='$item_id' AND `recept_batch`='$d1[batch_no]' AND `SuppCode`='$supp'"));
			if($cost)
			{
				$cost_price=$cost['recept_cost_price'];
			}
			else
			{
				$cost=mysqli_fetch_assoc(mysqli_query($link,"SELECT `cost_price` FROM `inv_supplier_items` WHERE `item_id`='$item_id' AND `supp_id`='$supp'"));
				$cost_price=$cost['cost_price'];
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
			<tr onClick="hguide_load('<?php echo $d1['batch_no'];?>','<?php echo $d1['closing_stock'];?>','<?php echo $exp_dt;?>','<?php echo $bch_val;?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
				<td><?php echo $d1['batch_no'];?></td>
				<td>
					<?php echo $d1['closing_stock'];?>
					<div <?php echo "id=dvhguide".$i;?> style="display:none;">
						<?php echo "#".$d1['batch_no']."#".$d1['closing_stock']."#".$cost_price."#".$exp_dt."#".$bch_val;?>
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
