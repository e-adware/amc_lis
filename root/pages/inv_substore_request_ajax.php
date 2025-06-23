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
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$dept=$_POST['dept'];
	if($dept)
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_substore_indent_order_master` WHERE `substore_id`='$dept' AND `order_date` BETWEEN '$fdate' AND '$tdate'");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_substore_indent_order_master` WHERE `order_date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Request No</th>
			<th>Department</th>
			<th>Request date</th>
			<th>User</th>
			<th>View</th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_assoc($q))
		{
			$dep=mysqli_fetch_array(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$r[substore_id]'"));
			$user=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
			if($r['stat']=="0")
			{
				$edit_func="edit_order('".$r['order_no']."')";
				$edit_disb="";
			}
			else
			{
				$edit_func="";
				$edit_disb="disabled='disabled'";
			}
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['order_no'];?></td>
			<td><?php echo $dep['substore_name'];?></td>
			<td><?php echo convert_date($r['order_date']);?></td>
			<td><?php echo $user['name'];?></td>
			<td>
				<button type="button" class="btn btn-success btn-mini" onclick="<?php echo $edit_func;?>" <?php echo $edit_disb;?>>Edit</button>
				<button type="button" class="btn btn-primary btn-mini" onclick="view_order('<?php echo $r['order_no'];?>')">View</button>
			</td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
} // 1

if($type==9899)
{
	
}
?>
