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


$type=$_POST['type'];

if($type==1)
{
	$id=$_POST['id'];
	$supp=$_POST['supp'];
	$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`hsn_code`,`mrp`,`gst` FROM `item_master` WHERE `item_id`='$id'"));
	echo $itm['item_name'];
}

if($type==2)
{
	$supp=$_POST['supp'];
	$all=$_POST['all'];
	$final_gst=$_POST['final_gst'];
	$final_rate=$_POST['final_rate'];
	$user=$_POST['user'];
	
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link,"SELECT count(`order_no`) as tot FROM `inv_purchase_order_master`"));
	$bill_tot_num=$bill_no_qry["tot"];
	
	$bill_no=$bill_tot_num+1;
	
	$orderno="ORD".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
	if(mysqli_query($link,"INSERT INTO `inv_purchase_order_master`(`order_no`, `supplier_id`, `stat`, `del`, `user`, `date`, `time`) VALUES ('$orderno','$supp','0','0','$user','$date','$time')"))
	{
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			if($a)
			{
				$v=explode("@@",$a);
				$itm=$v[0];
				$qnt=$v[1];
				$rate=$v[2];
				$amt=$v[3];
				$notes=$v[4];
				if($itm && $qnt)
				{
					mysqli_query($link,"INSERT INTO `inv_purchase_order_details`(`order_no`, `item_id`, `qnt`, `rate`, `amount`, `stat`, `notes`) VALUES ('$orderno','$itm','$qnt','$rate','$amt','0','$notes')");
				}
			}
		}
		echo "1";
	}
	else
	{
		echo "2";
	}
}

if($type==3)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$supp=$_POST['spplrid'];
	$orderno=$_POST['orderno'];
	
	if($orderno)
	{
		$q="SELECT * FROM `inv_purchase_order_master` WHERE del=0 and `order_no`='$orderno'";
	}
	else
	{
		if($supp)
		{
			$q="SELECT * FROM `inv_purchase_order_master` WHERE del=0 and `supplier_id`='$supp'";
			if($fdate && $tdate)
			{
				$q.=" AND `date` BETWEEN '$fdate' AND '$tdate'";
			}
		}
		else
		{
			$q="SELECT * FROM `inv_purchase_order_master`";
			if($fdate && $tdate)
			{
				$q.=" WHERE del=0 and `date` BETWEEN '$fdate' AND '$tdate'";
			}
		}
	}
	$q.=" ORDER BY `slno` DESC";
	//echo $q;
	$qry=mysqli_query($link,$q);
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Supplier</th>
			<th>Order Date</th>
			<th>PO Amount</th>
			<th>User</th>
			<th>Action</th>
		</tr>
		<?php
		$j=1;
		while($res=mysqli_fetch_assoc($qry))
		{
			$qsum=mysqli_fetch_assoc(mysqli_query($link,"select ifnull(sum(amount),0) as maxorderamt from inv_purchase_order_details where order_no='$res[order_no]'"));
			$supp=mysqli_fetch_assoc(mysqli_query($link,"select name from inv_supplier_master where id='$res[supplier_id]'"));
			$user=mysqli_fetch_assoc(mysqli_query($link,"select name from employee where emp_id='$res[user]'"));
			if($res['stat']=="0" && $res['del']=="0")
			{
				$edit="redirect_sale_frm('".$res['order_no']."')";
				$del="del_ord('".$res['order_no']."')";
				$prints="inv_order_print('".$res['order_no']."')";
				$btn_disb="";
			}
			else if($res['stat']=="0" && $res['del']=="1")
			{
				$edit="";
				$del="";
				$prints="";
				$btn_disb="disabled='disabled'";
			}
			else if($res['stat']=="1" && $res['del']=="0")
			{
				$edit="";
				$del="";
				$prints="inv_order_print('".$res['order_no']."')";
				$btn_disb="disabled='disabled'";
			}
			else
			{
				$edit="";
				$del="";
				$prints="";
				$btn_disb="";
			}
			$vttlorderamt+=$qsum['maxorderamt'];
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $res['order_no'];?></td>
			<td><?php echo $supp['name'];?></td>
			<td><?php echo convert_date($res['date']);?></td>
			<td><?php echo $qsum['maxorderamt'];?></td>
			<td><?php echo $user['name'];?></td>
			<td>
				<button type="button" class="btn btn-success btn-mini" onclick="<?php echo $edit;?>" <?php echo $btn_disb;?>><i class="icon-edit icon-large"></i></button>
				<!--<button type="button" class="btn btn-danger btn-mini" onclick="<?php echo $del;?>" <?php echo $btn_disb;?>><i class="icon-remove icon-large"></i></button>-->
				<button type="button" class="btn btn-danger btn-mini" onclick="delete_data('<?php echo $res["order_no"]; ?>')" <?php echo $btn_disb;?>><i class="icon-remove icon-large"></i></button>
				<button type="button" class="btn btn-info btn-mini" onclick="<?php echo $prints;?>"><i class="icon-print icon-large"></i></button>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
		<tr>
			<th colspan="4" style="text-align:right">Total :</th>
			<th><?php echo number_format($vttlorderamt,2);?> </th>
			<th colspan="2"></th>
		</tr>
	</table>
	<?php
}

if($type==4)
{
	$ord=$_POST['ord'];
	$sid=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$ord'"));
	?>
	<input type="hidden" id="ord_id" value="<?php echo $ord;?>" />
	<input type="hidden" id="supp_id" value="<?php echo $sid['supplier_id'];?>" />
	<table class="table table-condensed table-bordered table-report" id="mytable">
		<tr>
			<th>#</th>
			<th>Description</th>
			<th>Quantity</th>
			<th>Rate</th>
			<th>Amount</th>
			<th>Notes</th>
			<th width="5%">Remove</th>
		</tr>
	<?php
	$i=1;
	$gst_tot=0;
	$net_tot=0;
	$qry=mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$ord'");
	while($r=mysqli_fetch_assoc($qry))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$id=$r['item_id'];
		$qnt=$r['qnt'];
		$rate=$r['rate'];
		$amt=$r['amount'];
	?>
		<tr class="all_tr">
			<td><?php echo $i;?></td>
			<td><?php echo $itm['item_name'];?><input type='hidden' value="<?php echo $id;?>" class=''/><input type='hidden' value="<?php echo $id;?>" class='test_id'/></td>
			<td><?php echo $qnt;?><input type='hidden' value="<?php echo $qnt;?>" /></td>
			<td><?php echo $rate;?><input type='hidden' value="<?php echo $rate;?>" /></td>
			<td><?php echo $amt;?><input type='hidden' value="<?php echo $amt;?>" /></td>
			<td><?php echo $r['notes'];?><input type='hidden' value="<?php echo $r['notes'];?>" /></td>
			<?php
			if($r['stat']>0)
			{
			?>
			<td style='text-align:center;'><span style='cursor:pointer;color:#0C0;'><i class='icon-ok icon-large text-success'></i></span></td>
			<?php
			}
			else
			{
			?>
			<td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_sl_no()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td>
			<?php
			}
			?>
		</tr>
	<?php
	$i++;
	}
	?>
	</table>
	<?php
}

if($type==5)
{
	$orderno=$_POST['ord_id'];
	$supp=$_POST['supp_id'];
	$all=mysqli_real_escape_string($link,$_POST['all']);
	$final_gst=$_POST['final_gst'];
	$final_rate=$_POST['final_rate'];
	$user=$_POST['user'];
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_purchase_order_master` WHERE `order_no`='$orderno' AND `supplier_id`='$supp'"));
	if($det['stat']=="0" && $det['del']=="0")
	{
		$cnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`slno`) AS `cnt` FROM `inv_purchase_order_master_edit` WHERE `order_no`='$orderno' AND `supplier_id`='$supp'"));
		$count=$cnt['cnt']+1;
		if(mysqli_query($link,"INSERT INTO `inv_purchase_order_master_edit`(`order_no`, `supplier_id`, `user`, `date`, `time`, `counter`) VALUES ('$orderno','$det[supplier_id]','$det[user]','$det[date]','$det[time]','$count')"))
		{
			mysqli_query($link,"UPDATE `inv_purchase_order_master` SET `supplier_id`='$supp', `user`='$user' WHERE `order_no`='$orderno'");
			$q=mysqli_query($link,"SELECT * FROM `inv_purchase_order_details` WHERE `order_no`='$orderno'");
			while($r=mysqli_fetch_assoc($q))
			{
				mysqli_query($link,"INSERT INTO `inv_purchase_order_details_edit`(`order_no`, `item_id`, `qnt`, `rate`, `amount`, `notes`, `counter`) VALUES ('$orderno','$r[item_id]','$r[qnt]','$r[rate]','$r[amount]','$r[notes]','$count')");
			}
			mysqli_query($link,"DELETE FROM `inv_purchase_order_details` WHERE `order_no`='$orderno'");
			$al=explode("#%#",$all);
			foreach($al as $a)
			{
				if($a)
				{
					$v=explode("@@",$a);
					$itm=$v[0];
					$qnt=$v[1];
					$rate=$v[2];
					$amt=$v[3];
					$notes=$v[4];
					if($itm && $qnt)
					{
						mysqli_query($link,"INSERT INTO `inv_purchase_order_details`(`order_no`, `item_id`, `qnt`, `rate`, `amount`, `stat`, `notes`) VALUES ('$orderno','$itm','$qnt','$rate','$qnt','0','$notes')");
					}
				}
			}
			echo "1";
		}
		else
		{
			echo "2";
		}
	}
	else if($det['stat']=="1" && $det['del']=="0")
	{
		echo "3"; // already received
	}
	else if($det['stat']=="0" && $det['del']=="1")
	{
		echo "4"; // canceled
	}
	else
	{
		echo "2";
	}
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
