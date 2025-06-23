<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date('H:i:s');

// Date format convert    t-21653
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
	if($time)
	{
		$time = date("g:i A", strtotime($time));
		return $time;
	}
}

$type=$_POST['type'];

$not_id="101,102";

if($type==1)
{
	//$id=$_POST['id'];
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>Level Name</th>
			<th>No of users</th>
		</tr>
	<?php
	$q=mysqli_query($link,"SELECT DISTINCT b.`levelid`, b.`name` FROM `employee` a, `level_master` b WHERE a.`levelid`=b.`levelid` AND a.`emp_id` NOT IN ($not_id) ORDER BY b.`name`");
	while($r=mysqli_fetch_assoc($q))
	{
		$cnt=mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(`emp_id`) AS `users` FROM `employee` WHERE `emp_id` NOT IN ($not_id) AND `levelid`='$r[levelid]'"));
		?>
		<tr>
			<td><?php echo $r['name'];?></td>
			<td><?php echo $cnt['users'];?></td>
		</tr>
		<?php
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
