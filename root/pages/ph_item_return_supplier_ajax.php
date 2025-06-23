<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date('H:i:s');

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('M Y', $timestamp);
		return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}


$dname=mysqli_real_escape_string($link,$_POST['val']);
$ph=1;
if(trim($_POST['type'])=="item")
{
if($dname)
{
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Code</th><th>Item Name</th><th>Stock</th>
		</tr>
<?php
	
	if($dname)
	{
		//$q="select a.* from item_master a, ph_stock_master b where a.item_name!='' and a.item_name like '$dname%' or a.item_name!='' and a.item_id like '$dname%' or a.item_name!='' and a.short_name like '$dname%' order by a.item_name limit 0,30";
		
		$q="select distinct b.`item_code` from item_master a, ph_stock_master b where";
		$q.=" a.item_id=b.item_code and b.quantity>0  and b.substore_id='$ph' and a.item_name!='' and a.item_name like '$dname%'";
		$q.=" or a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.item_id like '$dname%'";
		$q.=" or a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph' and a.item_name!='' and a.short_name like '$dname%'";
		$q.=" order by a.item_name limit 0,30";
	}
	else
	{
		$q="select distinct b.`item_code` from item_master a, ph_stock_master b where a.item_name!='' and a.item_id=b.item_code and b.quantity>0 and b.substore_id='$ph'";
		$q.=" order by a.item_name limit 0,30";
	}
	//echo $q;
	$d=mysqli_query($link, $q);
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `item_master` WHERE `item_id`='$d1[item_code]'"));
		$stk=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`quantity`),0) AS stock FROM `ph_stock_master` WHERE `item_code`='$d1[item_code]' AND `substore_id`='$ph'"));
		?>
		<tr onclick="doc_load('<?php echo $itm['item_id'];?>','<?php echo $itm['item_name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
			<td><?php echo $itm['item_id'];?></td>
			<td><?php echo $itm['item_name'];?>
				<div <?php echo "id=dvdoc".$i;?> style="display:none;">
				<?php echo "#".$itm['item_id']."#".$itm['item_name'];?>
				</div>
			</td>
			<td><?php echo $stk['stock'];?></td>
		</tr>
	<?php
		$i++;
	}
	?>
	</table>
<?php
}
}

if(trim($_POST['type'])=="batch")
{
	$item_id=$_POST['item_id'];
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Batch No</th><th>Stock</th><th>MRP</th><th>GST</th><th>Expiry</th>
		</tr>
<?php
	
	if($dname)
	{
		$d=mysqli_query($link, "SELECT * FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$item_id' AND `batch_no` like '$dname%' AND `quantity`>0 ORDER BY `exp_date`");
	}
	else
	{
		$d=mysqli_query($link, "SELECT * FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$item_id' AND `quantity`>0 ORDER BY `exp_date`");
	}
	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		$mrp=mysqli_fetch_array(mysqli_query($link,"SELECT `recpt_mrp`,`gst_per` FROM `ph_purchase_receipt_details` WHERE `item_code`='$item_id' AND `recept_batch`='$d1[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
		?>
		<tr onClick="hguide_load('<?php echo $d1['batch_no'];?>','<?php echo $d1['quantity'];?>','<?php echo $mrp['recpt_mrp'];?>','<?php echo $mrp['gst_per'];?>','<?php echo $d1['exp_date'];?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
			<td><?php echo $d1['batch_no'];?></td>
			<td>
				<?php echo $d1['quantity'];?>
				<div <?php echo "id=dvhguide".$i;?> style="display:none;">
					<?php echo "#".$d1['batch_no']."#".$d1['quantity']."#".$mrp['recpt_mrp']."#".$mrp['gst_per']."#".$d1['exp_date'];?>
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


if($_POST['type']=="check_all_items")
{
	$all=$_POST['all'];
	$btnvalue=$_POST['btnvalue'];
	$ph=$_POST['ph'];
	$less="";
	$val=explode("#@#",$all);
	if($btnvalue=="Done")
	{
		foreach($val as $vl)
		{
			$v=explode("@@",$vl);
			$itm=$v[0];
			$bch=$v[1];
			$qnt=$v[2];
			if($itm && $bch)
			{
				$stk=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'"));
				if($stk['quantity']<$qnt)
				{
					$less.=$itm."@@".$bch."@@#@#";
				}
			}
		}
	}
	echo $less;
}

if($_POST['type']=="save_items")
{
	//$issue_to=mysqli_real_escape_string($link,$_POST['issue_to']);
	$supp=$_POST['supp'];
	$user=$_POST['user'];
	$all=$_POST['all'];
	$ph=1;
	$less="";
	$val=explode("#@#",$all);
	foreach($val as $vl)
	{
		$v=explode("@@",$vl);
		$itm=$v[0];
		$bch=$v[1];
		$qnt=$v[2];
		$free=$v[3];
		$qnt=($qnt+$free);
		if($itm && $bch)
		{
			$stk=mysqli_fetch_array(mysqli_query($link,"SELECT `quantity` FROM `ph_stock_master` WHERE `substore_id`='$ph' AND `item_code`='$itm' AND `batch_no`='$bch'"));
			if($stk['quantity']<$qnt)
			{
				$less.=$itm."@@".$bch."@@#@#";
			}
		}
	}
	if($less=="")
	{
		$bill_tot=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS cnt FROM `ph_item_return_supplier_master`"));
		$bill_no=$bill_tot['cnt']+1;
		$return_no="RET".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
		//$issueno=date("YmdHis").$user;
		
		if(mysqli_query($link,"INSERT INTO `ph_item_return_supplier_master`(`returnr_no`, `supplier_id`, `amount`, `gst_amount`, `net_amount`, `date`, `stat`, `del`, `user`, `time`) VALUES ('$return_no','$supp','0','0','0','$date','0','0','$user','$time')"))
		{
			$val=explode("#@#",$all);
			$all_gst=0;
			$all_amt=0;
			$net_amt=0;
			foreach($val as $vl)
			{
				$v=explode("@@",$vl);
				$itm=$v[0];
				$bch=$v[1];
				$qnt=$v[2];
				$free=$v[3];
				$mrp=$v[4];
				$amt=$v[5];
				$gst_per=$v[6];
				$gst_amt=$v[7];
				$expdt=$v[8];
				$vqnt=$qnt+$free;
				if($itm && $bch)
				{
					$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$ph' and item_code='$itm' and batch_no='$bch' and date='$date' order by slno desc limit 0,1"));
					if($stk)
					{
						$return=$stk['return_supplier']+$vqnt;
						$remain=$stk['s_remain']-$vqnt;
						//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$return_no','$itm','$bch','$vqnt','4','$date','$time','$user')");
						mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$return_no','$itm','$bch','$stk[s_remain]','$vqnt','$remain','4','$date','$time','$user')");
						mysqli_query($link,"update ph_stock_process set s_remain='$remain',return_supplier='$return' where date='$date' and item_code='$itm' and  batch_no='$bch' and substore_id='$ph' and slno='$stk[slno]'");
						mysqli_query($link,"update ph_stock_master set quantity='$remain' where substore_id='$ph' and item_code='$itm' and batch_no='$bch'");
					}
					else
					{
						$stk=mysqli_fetch_array(mysqli_query($link,"select * from ph_stock_process where substore_id='$ph' and item_code='$itm' and batch_no='$bch' order by slno desc limit 0,1"));
						$remain=$stk['s_remain']-$vqnt;
						//mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `qnt`, `process_type`, `date`, `time`, `user`) VALUES ('$ph','$return_no','$itm','$bch','$vqnt','4','$date','$time','$user')");
						mysqli_query($link,"INSERT INTO `ph_item_process`(`substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`,  `process_type`, `date`, `time`, `user`) VALUES ('$ph','$return_no','$itm','$bch','$stk[s_remain]','$vqnt','$remain','4','$date','$time','$user')");
						mysqli_query($link,"INSERT INTO `ph_stock_process`(`substore_id`, `process_no`, `item_code`, `batch_no`, `s_available`, `added`, `sell`, `return_cstmr`, `return_supplier`, `s_remain`, `date`) VALUES ('$ph','$return_no','$itm','$bch','$stk[s_remain]','0','0','0','$vqnt','$remain','$date')");
						mysqli_query($link,"update ph_stock_master set quantity='$remain' where substore_id='$ph' and item_code='$itm' and batch_no='$bch'");
					}
					
					$price=mysqli_fetch_array(mysqli_query($link,"SELECT `recept_cost_price`,`sale_price` FROM `ph_purchase_receipt_details` WHERE `item_code`='$itm' AND `recept_batch`='$bch' AND `SuppCode`='$supp'"));
					
					$amt=($price['recept_cost_price']*$qnt);
					$gst_amt=(($amt*$gst_per)/100);
					mysqli_query($link,"INSERT INTO `ph_item_return_supplier_details`(`returnr_no`, `item_id`, `expiry_date`, `quantity`, `free_qnt`, `batch_no`, `recpt_mrp`, `recept_cost_price`, `item_amount`, `dis_per`, `dis_amt`, `gst_per`, `gst_amount`) VALUES ('$return_no','$itm','$expdt','$qnt','$free','$bch','$mrp','$price[recept_cost_price]','$amt','0','0','$gst_per','$gst_amt')");
					$all_gst+=$gst_amt;
					$all_amt+=$amt;
				}
			}
			$net_amt=($all_gst+$all_amt);
			mysqli_query($link,"UPDATE `ph_item_return_supplier_master` SET `amount`='$all_amt',`gst_amount`='$all_gst',`net_amount`='$net_amt' WHERE `returnr_no`='$return_no' AND `supplier_id`='$supp' AND `user`='$user'");
			
			$supp_bal=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_supplier_transaction` WHERE `supp_code`='$supp' ORDER BY `slno` DESC LIMIT 0,1"));
			$balance=$supp_bal['balance_amt']-$net_amt;
			mysqli_query($link,"INSERT INTO `inv_supplier_transaction`(`process_no`, `supp_code`, `debit_amt`, `credit_amt`, `adjust_amt`, `balance_amt`, `type`, `date`, `time`, `user`) VALUES ('$return_no','$supp','0','$net_amt','0','$balance','3','$date','$time','$user')");
			
			echo "1@govin@Done";
		}
	}
	else
	{
		echo "0@govin@".$less;
	}
}

if($_POST['type']=="oo")
{
	
}
?>
