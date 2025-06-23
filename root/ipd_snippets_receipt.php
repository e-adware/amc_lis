<?php
	$iq=mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details` WHERE `date`='$date'");
	$i_num=mysqli_num_rows($iq);
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;">IPD</button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
		<tr>
			<td>Total Admitted Patients</td>
			<td><?php echo $i_num;?></td>
		</tr>
		<tr>
			<th>Ward Name</th>
			<th>Number of patients</th>
		</tr>
		<?php
		$ward=mysqli_query($link,"SELECT distinct(`ward_id`) FROM `ipd_pat_bed_details` WHERE `ipd_id` in (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`=3) ");
		while($w=mysqli_fetch_array($ward))
		{
			$c=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(`patient_id`) AS total FROM `ipd_pat_bed_details` WHERE `ward_id`='$w[ward_id]' AND `date`='$date'"));
			$wd=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$w[ward_id]'"));
		?>
		<tr>
			<td><?php echo $wd['name'];?></td>
			<td><?php echo $c['total'];?></td>
		</tr>
		<?php
		}?>
	</table>
</div>
</div>
