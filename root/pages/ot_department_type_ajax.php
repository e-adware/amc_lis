<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");
$type=$_POST['type'];

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
// Date format convert
function convert_date_g($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

if($type==1)
{
	$srch=$_POST['srch'];
	if($srch)
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_dept_master` WHERE `ot_dept_name` like '%$srch%' ORDER BY `ot_dept_name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_dept_master` ORDER BY `ot_dept_name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
		<tr>
			<th>SN</th><th>Department Name</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr class="nm">
			<td style="cursor:pointer;" onclick="det('<?php echo $r['ot_dept_id'];?>')"><?php echo $n;?></td>
			<td style="cursor:pointer;" onclick="det('<?php echo $r['ot_dept_id'];?>')"><?php echo $r['ot_dept_name'];?></td>
			<td><i class="icon-remove icon-large" disabled="disabled" style="color:#bb0000;cursor:pointer;"></i></td>
		</tr>
		<?php
		$n++;
		}
		?>
	</table>
	<?php
}

if($type==2)
{
	$id=$_POST['id'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$id'"));
	//$rate=explode(".",$d['rate']);
	echo $id."#@#".$d['ot_dept_name']."#@#";
}

if($type==3)
{	
	$id=$_POST['id'];
	$dept=$_POST['dept'];
	$typ=$_POST['typ'];
	if($id>0)
	{
		mysqli_query($link,"UPDATE `ot_dept_master` SET `ot_dept_name`='$dept' WHERE `ot_dept_id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_dept_master`(`ot_dept_name`) VALUES ('$dept')");
		echo "Saved";
	}
}

if($type==4)
{
	$id=$_POST['id'];
	//mysqli_query($link,"DELETE FROM `ot_service_master` WHERE `ot_dept_id`='$id'");
	echo "Deleted";
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
