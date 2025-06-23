<?php
include("includes/connection.php");
$val=$_POST['val'];

$not_accountant = array();
array_push($not_accountant, 0);
$not_accountant = join(',',$not_accountant);

$name=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `emp_id`,`name` FROM `employee` WHERE `name` like '%$val%' AND `levelid` NOT IN ($not_accountant) order by `name` ");
?>
<table class="table table-bordered user-table">
	<th>ID</th><th>Name</th>
	<?php
	$i=1;
	while($user=mysqli_fetch_array($name))
	{
		echo "<tr id='upd_psim$i' onclick=\"get_user_detail('@$user[name]@$user[emp_id]')\" style='cursor:pointer;'><td>$user[emp_id]</td><td>$user[name]";
		?>	
		<div id="log_det<?php echo $i;?>" style="display:none">
			<?php echo "@".$user['name']."@".$user['emp_id'];?>	
		</div>
		<?php
		echo "</td></tr>";
		$i++;
	}
	
	?>
</table>
