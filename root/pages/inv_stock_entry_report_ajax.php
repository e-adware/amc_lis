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


$type=$_POST['type'];

if($type==1)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	?>
	<button type="button" class="btn btn-primary" onclick="entry_print('<?php echo $fdate;?>','<?php echo $tdate;?>')">Print</button>
	<table class="table table-condensed table-report">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Expiry</th>
			<th><i class="icon-wrench"></i></th>
			<th>Quantity</th>
			<th>User</th>
		</tr>
		</thead>
		<tbody>
		<tr></tr>
		<?php
		$j=1;
		$qry=mysqli_query($link,"SELECT DISTINCT `entry_date` FROM `inv_item_stock_entry` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate'");
		while($res=mysqli_fetch_assoc($qry))
		{
		?>
		<tr>
			<th colspan="7">Entry Date : <?php echo convert_date($res['entry_date']);?></th>
		</tr>
		<?php
		$qq=mysqli_query($link,"SELECT DISTINCT `item_id` FROM `inv_item_stock_entry` WHERE `entry_date`='$res[entry_date]'");
		while($rr=mysqli_fetch_assoc($qq))
		{
		$q=mysqli_query($link,"SELECT * FROM `inv_item_stock_entry` WHERE `entry_date`='$res[entry_date]' AND `item_id`='$rr[item_id]'");
		while($r=mysqli_fetch_assoc($q))
		{
		if($r['type']>0)
		{
			$style="#178419";
			$qnt_type="(<i class='icon-plus'></i>)";
		}
		else
		{
			$style="#D40A0A";
			$qnt_type="(<i class='icon-minus'></i>)";
		}
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		$exp_date=mysqli_fetch_assoc(mysqli_query($link,"SELECT `exp_date` FROM `inv_maincurrent_stock` WHERE `item_id`='$r[item_id]' AND `batch_no`='$r[batch_no]'"));
		$emp=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr style="color:<?php echo $style;?> !important;">
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['batch_no'];?></td>
			<td><?php echo date("Y-m", strtotime($exp_date['exp_date']));?></td>
			<td><?php echo $qnt_type;?></td>
			<td><?php echo $r['entry_qnt'];?></td>
			<td><?php echo $emp['name'];?></td>
		</tr>
		<?php
		$j++;
		}
		}
		}
		?>
		</tbody>
	</table>
	<?php
}

if($type==999)
{
	$orderno=$_POST['ord_id'];
}
?>
