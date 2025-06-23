<?php
	$casq=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' AND `type`='4'");
	$cas_num=mysqli_num_rows($casq);
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;">CASUALITY</button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
		<tr>
			<td>Total Patients</td>
			<td><?php echo $cas_num;?></td>
		</tr>
	</table>
</div>
</div>
