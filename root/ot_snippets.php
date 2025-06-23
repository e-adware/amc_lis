<?php
	$oq=mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `date`='$date'");
	$on=mysqli_num_rows($oq);
?>
<div class="spap_dash span5">
	<button style="width:100%;background-color:#ddd;padding:5px;border:0;">IPD</button>
	<table class="table table-condensed">
		<tr>
			<th>Surgeon</th>
			<th>Schedule Time</th>
		</tr>
		<?php
		while($ro=mysqli_fetch_array($oq))
		{
			$d=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `emp_id`='$ro[requesting_doc]'"));
		?>
		<tr>
			<td><?php echo $d['Name'];?></td>
			<td><?php echo $ro['start_time'];?></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td>Total</td>
			<td><?php echo $on;?></td>
		</tr>
	</table>
</div>

<style>
.spap_dash
{
	border: 1px solid #ddd;
	margin-top: 3%;
	padding: 2px;
	margin-left: 1%;
}
</style>
