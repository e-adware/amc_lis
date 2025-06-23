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
		$q=mysqli_query($link,"SELECT * FROM `ot_service_master` WHERE `service_name` like '%$srch%' ORDER BY `service_name`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `ot_service_master` ORDER BY `service_name`");
	}
	?>
	<table class="table table-condensed table-bordered" id="">
		<tr>
			<th>SN</th><th>Charge Name</th><th>Rate</th><th><i class="icon-trash icon-large" style="color:#bb0000;"></i></th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
		?>
		<tr class="nm">
			<td style="cursor:pointer;" onclick="det('<?php echo $r['ot_service_id'];?>')"><?php echo $n;?></td>
			<td style="cursor:pointer;" onclick="det('<?php echo $r['ot_service_id'];?>')"><?php echo $r['service_name'];?></td>
			<td style="cursor:pointer;" onclick="det('<?php echo $r['ot_service_id'];?>')"><?php echo $r['rate'];?></td>
			<td><i class="icon-remove icon-large" onclick="confirmm('<?php echo $r['ot_service_id'];?>')" style="color:#bb0000;cursor:pointer;"></i></td>
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
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `service_name`,`rate` FROM `ot_service_master` WHERE `ot_service_id`='$id'"));
	$rate=explode(".",$d['rate']);
	echo $id."#@#".$d['service_name']."#@#".$rate[0]."#@#";
}

if($type==3)
{	
	$id=$_POST['id'];
	$chname=$_POST['chname'];
	$rate=$_POST['rate'];
	$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_service_master` WHERE `ot_service_id`='$id'"));
	if($num>0)
	{
		mysqli_query($link,"UPDATE `ot_service_master` SET `service_name`='$chname',`rate`='$rate' WHERE `ot_service_id`='$id'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ot_service_master`(`service_name`, `rate`) VALUES ('$chname','$rate')");
		echo "Saved";
	}
}

if($type==4)
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `ot_service_master` WHERE `ot_service_id`='$id'");
	echo "Deleted";
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
