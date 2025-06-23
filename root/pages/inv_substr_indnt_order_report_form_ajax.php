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
	$sbstorid=$_POST['sbstorid'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	
	if($sbstorid!=0)
	{
		$q=mysqli_query($link,"SELECT a.*,b.substore_name FROM inv_substore_indent_order_master a,inv_sub_store b WHERE order_date between'$fdate' and '$tdate' and a.substore_id=b.substore_id and a.substore_id='$sbstorid' order by order_no");
	}
	else
	{
		$q=mysqli_query($link,"SELECT a.*,b.substore_name FROM inv_substore_indent_order_master a,inv_sub_store b WHERE order_date between'$fdate' and '$tdate' and a.substore_id=b.substore_id order by order_no");
	}
	
	
	?>
	<!--<button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $ord;?>')">Export to excel</button>
	<button type="button" class="btn btn-default" onclick="ord_rep_prr('<?php echo $ord;?>')">Print</button>-->
	<table class="table table-condensed table-bordered table-report">
		
		<tr>
			<th>#</th>
			<th>Order No</th>
			<th>Order Date</th>
			<th>Department</th>
			<th>Order By</th>
			<!--<th>Export To Excel</th>-->
			<th>Edit / View</th>
		</tr>
		<?php
		$i=1;
		while($r=mysqli_fetch_array($q))
		{
			$quser=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
			if($r['stat']=="0")
			{
				$item_issue=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `inv_substore_order_details` WHERE `order_no`='$r[order_no]' AND `stat`='1'"));
				if($item_issue)
				{
					$edit_func="";
					$edit_disb="disabled='disabled'";
				}
				else
				{
					$edit_func="edit_order('".$r['order_no']."')";
					$edit_disb="";
				}
			}
			else
			{
				$edit_func="";
				$edit_disb="disabled='disabled'";
			}
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo convert_date($r['order_date']);?></td>
			<td><?php echo $r['substore_name'];?></td>
			<td><?php echo $quser['name'];?></td>
			<!--<td><button type="button" class="btn btn-default" onclick="ord_rep_exp('<?php echo $r['order_no'];?>')">Export to excel</button></td>-->
			<td>
				<button type="button" class="btn btn-success btn-mini" onclick="<?php echo $edit_func;?>" <?php echo $edit_disb;?>>Edit</button>
				<button type="button" class="btn btn-primary btn-mini" onclick="ord_rep_prr('<?php echo $r['order_no'];?>')">View</button>
			</td>
		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
}

if($type==2)
{
	$substore=$_POST['substore'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	if($substore)
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_item_return_store_master` WHERE `substore_id`='$substore' AND `date` BETWEEN '$fdate' AND '$tdate'");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_item_return_store_master` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Return No</th>
			<th>Department</th>
			<th>Return Date</th>
			<th>User</th>
			<th>Print</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_assoc($q))
		{
			$dept=mysqli_fetch_assoc(mysqli_query($link,"select substore_name FROM `inv_sub_store` where substore_id='$r[substore_id]'"));
			$user=mysqli_fetch_assoc(mysqli_query($link,"select name from employee where emp_id='$r[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['returnr_no'];?></td>
			<td><?php echo $dept['substore_name'];?></td>
			<td><?php echo convert_date($r['date']);?></td>
			<td><?php echo $user['name'];?></td>
			<td>
				<button type="button" class="btn btn-mini btn-primary" onclick="ord_rep_prr('<?php echo $r['returnr_no'];?>')"><i class="icon-print icon-large"></i> Print</button>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
