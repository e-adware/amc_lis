<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;">OPD</button>
</div>
<div class="child_snip">
	<table class="table table-condensed table-report">
		<tr>
			<th>Consultant Doctor</th>
			<th>Number of patients</th>
		</tr>
		<?php
			$qr=mysqli_query($link,"SELECT DISTINCT `consultantdoctorid` FROM `appointment_book` WHERE `date`='$date'");
			while($d=mysqli_fetch_array($qr))
			{
				$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$d[consultantdoctorid]'"));
				$p=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `consultantdoctorid`='$d[consultantdoctorid]' AND `appointment_date`='$date'"));
			?>
			<tr>
				<td><?php echo $doc['Name'];?></td>
				<td><?php echo $p;?></td>
			</tr>
			<?php
			}
			?>
	</table>
</div>
</div>
