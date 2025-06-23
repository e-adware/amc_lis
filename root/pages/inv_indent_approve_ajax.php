<?php
include("../../includes/connection.php");
$date=date("Y-m-d");
$time=date("H:i:s");

function nextID($prefix,$table,$idno,$start="100") 
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
	if($time)
	{
		$time = date("h:i A", strtotime($time));
		return $time;
	}
}

$type=$_POST['type'];

if($type==1)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$srch=mysqli_real_escape_string($link,$_POST['srch']);
	
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Order No</th>
			<!--<th>Request Type</th>-->
			<th>Sub Department</th>
			<th>Order Date</th>
			<th>Order Time</th>
			<th>User</th>
		</tr>
	<?php
	$j=1;
	if($srch)
	{
		$q="select a.*,b.substore_name from inv_substore_indent_order_master a,inv_sub_store b where a.substore_id=b.substore_id and a.stat=0 and a.order_no like '%$srch%'";
		$q.=" or a.substore_id=b.substore_id and a.stat=0 and b.substore_name like '%$srch%'";
		$q.=" order by a.slno desc limit 0,10";
	}
	else
	{
		$q="select a.*,b.substore_name from inv_substore_indent_order_master a,inv_sub_store b where a.substore_id=b.substore_id and a.stat=0 and a.order_date BETWEEN '$fdate' and '$tdate' order by a.slno desc";
	}
	//echo $q;
	$qry=mysqli_query($link,$q);
	while($r=mysqli_fetch_assoc($qry))
	{
		$issue_typ="";
			if($r['issue_typ']=="2")
			{
				$issue_typ="60 BD";
			}
			if($r['issue_typ']=="3")
			{
				$issue_typ="SON";
			}
			if($r['issue_typ']=="4")
			{
				$issue_typ="Hospital a/c";
			}
			
		$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr class="order_list" onclick="redirect_page('<?php echo $r['order_no'];?>')">
			<td><?php echo $j;?></td>
			<td><?php echo $r['order_no'];?></td>
			<!--<td><?php echo $issue_typ;?></td>-->
			<td><?php echo $r['substore_name'];?></td>
			<td><?php echo convert_date($r['order_date']);?></td>
			<td><?php echo convert_time($r['time']);?></td>
			<td><?php echo $u['name'];?></td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<?php
}

if($type==2)
{
	$ordno=$_POST['ordno'];
	$val=$_POST['val'];
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>Item Code</th><th>Item Name</th><th>Stock</th><th>Order</th>
		</tr>
	<?php
	$i=1;
	if($val)
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_substore_order_details` a, `item_master` b WHERE a.`item_id`=b.`item_id` AND a.`stat`='0' AND a.`order_no`='$ordno' AND b.`item_name` like '%$val%'");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_substore_order_details` WHERE `order_no`='$ordno' AND `stat`='0'");
	}
	while($r=mysqli_fetch_assoc($q))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_id`,`item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`closing_stock`),0) AS stock FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_id]'"));
		?>
		<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>','<?php echo $r['order_qnt'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
			<td><?php echo $r['item_id'];?></td>
			<td>
				<?php echo $itm['item_name'];?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name']."#".$r['order_qnt'];?>
				</div>
			</td>
			<td><?php echo $stk['stock'];?></td>
			<td><?php echo $r['order_qnt'];?></td>
		</tr>
		<?php
		$i++;
	}
	?>
	</table>
	<?php
}

if($type==3)
{
	$item_id=$_POST['item_id'];
	$val=$_POST['val'];
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Batch No</th><th>Stock</th><th>MRP</th><th>GST</th><th>Expiry</th>
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
		$mrp=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,`gst_per` FROM `inv_main_stock_received_detail` WHERE `item_id`='$item_id' AND `recept_batch`='$d1[batch_no]'"));
		?>
		<tr onClick="hguide_load('<?php echo $d1['batch_no'];?>','<?php echo $d1['closing_stock'];?>','<?php echo $mrp['recpt_mrp'];?>','<?php echo $mrp['gst_per'];?>','<?php echo $d1['exp_date'];?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
			<td><?php echo $d1['batch_no'];?></td>
			<td>
				<?php echo $d1['closing_stock'];?>
				<div <?php echo "id=dvhguide".$i;?> style="display:none;">
					<?php echo "#".$d1['batch_no']."#".$d1['closing_stock']."#".$mrp['recpt_mrp']."#".$mrp['gst_per']."#".$d1['exp_date'];?>
				</div>
			</td>
			<td><?php echo $mrp['recpt_mrp'];?></td>
			<td><?php echo $mrp['gst_per'];?></td>
			<td><?php echo convert_date($d1['exp_date']);?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
<?php
}

if($type==4)
{
	$ordno=$_POST['ordno'];
	$issueto=mysqli_real_escape_string($link,$_POST['issueto']);
	$issue_num=mysqli_real_escape_string($link,$_POST['issue_num']);
	$issue_typ=$_POST['issue_typ'];
	$bed_no=$_POST['bed_no'];
	$user=$_POST['user'];
	$all=$_POST['all'];
	$cnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS cnt FROM `inv_substore_issue_master`"));
	$count=$cnt['cnt']+1;
	$vid="ISU".str_pad($count, 4, 0, STR_PAD_LEFT);
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_substore_indent_order_master` WHERE `order_no`='$ordno'"));
	$substore_id=$det['substore_id'];
	
	
	if(mysqli_query($link,"INSERT INTO `inv_substore_issue_master`(`issue_no`, `req_no`, `issue_to`, `issue_num`, `substore_id`, `amount`, `gst_amount`, `user`, `date`, `time`) VALUES ('$vid','$ordno','$issueto','$issue_num','$substore_id',0,0,'$user','$date','$time')"))
	{
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@@",$a);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			$mrp=$v[3];
			$amount=$v[4];
			$gst_per=$v[5];
			$gst_amt=$v[6];
			$expdt=$v[7];
			$exp_dt=$v[7]."-01";
			$exp_dt=date("Y-m-t",strtotime($exp_dt));
			$net=$amount+$gst_amt;
			$tot_amount+=$amount;
			$gst_amount+=$gst_amt;
			if($itm)
			{
				mysqli_query($link,"INSERT INTO `inv_substore_issue_details`(`issue_no`, `item_id`, `batch_no`, `exp_date`, `issue_qnt`, `rate`, `amount`, `gst_per`, `gst_amount`, `total_amount`) VALUES ('$vid','$itm','$bch','$exp_dt','$qnt','$mrp','$amount','$gst_per','$gst_amt','$net')");
				//--------------------store---------------------//
				$inv_qry=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_mainstock_details` WHERE `date`='$date' AND `item_id`='$itm' AND `batch_no`='$bch' ORDER BY `date` DESC"));
				if($inv_qry)
				{
					$closing_qnty=$inv_qry['closing_qnty']-$qnt;
					$issu_qnty=$inv_qry['issu_qnty']+$qnt;
					mysqli_query($link,"UPDATE `inv_mainstock_details` SET `closing_qnty`='$closing_qnty',`issu_qnty`='$issu_qnty' WHERE `date`='$date' AND `item_id`='$itm' AND `batch_no`='$bch' ");
					mysqli_query($link,"DELETE FROM `inv_maincurrent_stock` WHERE `item_id`='$itm' AND `batch_no`='$bch'");
					mysqli_query($link,"INSERT INTO `inv_maincurrent_stock` (`item_id`,`batch_no`,`closing_stock`,`exp_date`) VALUES ('$itm','$bch','$closing_qnty','$expdt')");
				}
				else
				{
					$inv_qry=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_mainstock_details` WHERE `item_id`='$itm' AND `batch_no`='$bch' ORDER BY `slno` DESC"));
					$closing_qnty=$inv_qry['closing_qnty']-$qnt;
					mysqli_query($link,"INSERT INTO `inv_mainstock_details` (`item_id`,`batch_no`,`date`,`op_qnty`,`recv_qnty`,`issu_qnty`,`closing_qnty`) VALUES ('$itm','$bch','$date','$inv_qry[closing_qnty]',0,'$qnt','$closing_qnty') ");
					mysqli_query($link,"DELETE FROM `inv_maincurrent_stock` WHERE `item_id`='$itm' AND `batch_no`='$bch'");
					mysqli_query($link,"INSERT INTO `inv_maincurrent_stock` (`item_id`,`batch_no`,`closing_stock`,`exp_date`) VALUES ('$itm','$bch','$closing_qnty','$expdt')");
				}
				//-----------------sub store stock------------------------//
				$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_stock_process` WHERE `date`='$date' and item_code='$itm' and  batch_no='$bch' order by date desc"));
				if($last_stock) // last stock of current date
				{
					$add_qnt=$last_stock['added']+$qnt;
					$close_qnt=$last_stock['s_remain']+$qnt;
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$vid','$itm','$bch','$last_stock[s_remain]','$qnt','$close_qnt','7','$date','$time','$user')");
					mysqli_query($link,"UPDATE `ph_stock_process` SET `added`='$add_qnt',`s_remain`='$close_qnt' WHERE `date`='$date' AND `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
					mysqli_query($link,"DELETE FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
					mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$expdt')");
				}
				else // last stock desc
				{
					$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * from `ph_stock_process` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id' ORDER BY `date` DESC LIMIT 0,1"));
					$close_qnt=$last_stock['s_remain']+$qnt;
					mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$substore_id','$vid','$itm','$bch','$last_stock[s_remain]','$qnt','$close_qnt','7','$date','$time','$user')");
					mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`,`process_no`,`item_code`,`batch_no`,`s_available`,`added`,`sell`,`return_cstmr`,`return_supplier`,`s_remain`,`date`) values('$substore_id','$vid','$itm','$bch','$last_stock[s_remain]','$qnt',0,0,0,'$close_qnt','$date')");
					mysqli_query($link,"DELETE FROM `ph_stock_master` WHERE `item_code`='$itm' AND `batch_no`='$bch' AND `substore_id`='$substore_id'");
					mysqli_query($link,"INSERT INTO `ph_stock_master`(`substore_id`,`item_code`,`batch_no`,`quantity`,`mfc_date`,`exp_date`) values ('$substore_id','$itm','$bch','$close_qnt','0000-00-00','$expdt')");
				}
				
				mysqli_query($link,"UPDATE `inv_substore_order_details` SET `stat`='1' WHERE `order_no`='$ordno' AND `item_id`='$itm' AND `substore_id`='$substore_id'");
			}
		}
		$check_remain_item=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_substore_order_details` WHERE `order_no`='$ordno' AND `substore_id`='$substore_id' AND `stat`='0'"));
		if(!$check_remain_item)
		{
			mysqli_query($link,"UPDATE `inv_substore_indent_order_master` SET `stat`='1' WHERE `order_no`='$ordno' AND `substore_id`='$substore_id'");
		}
		mysqli_query($link,"UPDATE `inv_substore_issue_master` SET `amount`='$tot_amount', `gst_amount`='$gst_amount' WHERE `issue_no`='$vid'");
		echo "1#@#".$ordno;
	}
	else
	{
		echo "0#@#0";
	}
} // 4

if($type==5)
{
	$substore_id=$_POST['substore_id'];
	$sub_name=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$substore_id'"));
	?>
	<span style="float:right;">
		<button type="button" class="btn btn-primary" onclick="stk_prr('<?php echo base64_encode($substore_id);?>')">Print</button>
	</span>
	<b>Sub store : <?php echo $sub_name['substore_name'];?></b>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th><th>Item Code</th><th>Item Name</th><th>Batch No</th><th>Expiry</th><th>Stock</th>
		</tr>
	<?php
	$j=1;
	$qq=mysqli_query($link,"SELECT DISTINCT a.`item_id`, a.`item_name` FROM `item_master` a, `ph_stock_master` b WHERE a.`item_id`=b.`item_code` AND b.`substore_id`='$substore_id' ORDER BY a.`item_name`");
	while($rr=mysqli_fetch_assoc($qq))
	{
		$q=mysqli_query($link,"SELECT `batch_no`, `quantity`, `exp_date` FROM `ph_stock_master` WHERE `substore_id`='$substore_id' AND `item_code`='$rr[item_id]' AND `quantity`>'0'");
		while($r=mysqli_fetch_assoc($q))
		{
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $rr['item_id'];?></td>
			<td><?php echo $rr['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo date("Y-m", strtotime($r['exp_date']));?></td>
			<td><?php echo $r['quantity'];?></td>
		</tr>
		<?php
		$j++;
		}
	}
	?>
	</table>
	<?php
} // 5

if($type==6)
{
	
}
?>
