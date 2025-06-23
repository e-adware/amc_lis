<?php
include("../../includes/connection.php");
$date=date('Y-m-d');
$time=date('H:i:s');
$type=$_POST['type'];

if($type=="load_types")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_moving_item_master` WHERE `name` like '$srch%' ORDER BY `name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ph_moving_item_master` ORDER BY `name`");
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Type Name</th><th>Quantity</th><th></th>
		</tr>
		<?php
		$j=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['name'];?></td>
			<td><?php echo $r['quantity'];?></td>
			<td>
				<button type="button" class="btn btn-info btn-mini" onclick="edit_type('<?php echo $r['move_id'];?>')"><b class="icon-edit icon-large"></b></button>
				<button type="button" class="btn btn-danger btn-mini" onclick="del_type('<?php echo $r['move_id'];?>')"><b class="icon-remove icon-large"></b></button>
			</td>
		</tr>
		<?php
		$j++;
			//echo "<br/>".$r['item_code'];
		}
		?>
	</table>
	<?php
}

if($type=="save_type")
{
	$id=$_POST['id'];
	$name=$_POST['name'];
	$qnt=$_POST['qnt'];
	if($id>0)
	{
		mysqli_query($link,"UPDATE `ph_moving_item_master` SET `name`='$name',`quantity`='$qnt' WHERE `move_id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ph_moving_item_master`(`name`, `quantity`) VALUES ('$name','$qnt')");
		echo "Saved";
	}
}

if($type=="edit_type")
{
	$id=$_POST['id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ph_moving_item_master` WHERE `move_id`='$id'"));
	echo $id."#@#".$v['name']."#@#".$v['quantity']."#@#";
}

if($type=="del_type")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `ph_moving_item_master` WHERE `move_id`='$id'");
	mysqli_query($link,"DELETE FROM `ph_moving_item_master_list` WHERE `move_id`='$id'");
	echo "Deleted";
}

if($type=="oo")
{
	$type=$_POST['type'];
}
?>
