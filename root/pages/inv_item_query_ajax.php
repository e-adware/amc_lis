<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";

function convert_date($date)
{
	if($date)
	{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-y', $timestamp);
	return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("h:i A", strtotime($time));
	return $time;
}

if($_POST["type"]==1)
{
	$fdate=$_POST["fdate"];
	$tdate=$_POST["tdate"];
	$itm=$_POST["itm"];
	$user=$_POST["user"];
	$bid=mysqli_fetch_assoc(mysqli_query($link,"SELECT `branch_id` FROM `employee` WHERE `emp_id`='$user'"));
	$branch_id=$bid['branch_id'];
	?>
	<table class="table table-condensed" style="background:#FFF;">
		<thead class="table_header_fix">
		<tr>
			<th>#</th>
			<th>Date</th>
			<th>Opening</th>
			<th>Add</th>
			<th>Deduct</th>
			<th>Closing</th>
		</tr>
		</thead>
		<tbody>
	<?php
	$j=1;
	$qry=mysqli_query($link,"SELECT DISTINCT `date` FROM `inv_mainstock_details` WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `date` BETWEEN '$fdate' AND '$tdate'");
	while($rr=mysqli_fetch_assoc($qry))
	{
	//$exp_date=mysqli_fetch_assoc(mysqli_query($link,"SELECT `exp_date` FROM `ph_stock_master` WHERE `substore_id`='1' AND `item_code`='$itm' AND `batch_no`='$rr[batch_no]' ORDER BY `slno` DESC LIMIT 0,1"));
	//$q=mysqli_query($link,"SELECT * FROM `inv_mainstock_details` WHERE `item_id`='$itm' AND `batch_no`='$rr[batch_no]' AND `date` BETWEEN '$fdate' AND '$tdate'");
	$q=mysqli_query($link,"SELECT DISTINCT a.`item_id`, b.`item_name` FROM `inv_mainstock_details` a, `item_master` b WHERE a.`branch_id`='$branch_id' AND a.`item_id`=b.`item_id` AND a.`item_id`='$itm' AND a.`date`='$rr[date]'");
	while($r=mysqli_fetch_assoc($q))
	{
		$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_mainstock_details` WHERE `branch_id`='$branch_id' AND `item_id`='$r[item_id]' AND `date`='$rr[date]'"));
		
		$recv_txt="";
		$issu_txt="";
		if($stk['recv_qnty']>0)
		{
			$dep=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_item_process` WHERE `item_id`='$itm' AND `process_type`='1' AND `date`='$rr[date]'"));
			$bl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_no` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$dep[process_no]'"));
			$recv_txt=" (<small>Bill: ".$bl['bill_no']."</small>)";
		}
		if($stk['issu_qnty']>0)
		{
			$dep=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_item_process` WHERE `item_id`='$itm' AND `process_type`='7' AND `date`='$rr[date]'"));
			$dName=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$dep[substore_id]'"));
			$issu_txt=" (<small>".$dName['substore_name']."</small>)";
		}
		if($stk['recv_qnty']>0)
		{
			$dep=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_item_process` WHERE `item_id`='$itm' AND `process_type`='8' AND `date`='$rr[date]'"));
			$dName=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$dep[substore_id]'"));
			if($recv_txt && $dep)
			{
				$recv_txt.=" (<small>Add: ".$dName['substore_name']."</small>)";
			}
		}
		//$usr=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr style="color:<?php echo $color;?>;">
			<td><?php echo $j;?></td>
			<td><?php echo convert_date($rr['date']);?></td>
			<td><?php echo $stk['op_qnty'];?></td>
			<td><?php echo $stk['recv_qnty'].$recv_txt;?></td>
			<td><?php echo $stk['issu_qnty'].$issu_txt;?></td>
			<td><?php echo $stk['closing_qnty'];?></td>
		</tr>
		<?php
		$j++;
	}
	?>
		<tr>
			<td colspan="10" style="background:#888;padding:1px;"></td>
		</tr>
	<?php
	}
	?>
		</tbody>
	</table>
	<?php
}
?>