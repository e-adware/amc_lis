<br/>
<div>
	<table class="table table-condensed patient_header">
		<tr>
			<th style="width: 15%;">Sample No.</th>
			<th style="width: 45%;">: <?php echo $pat_reg['type_prefix'] . $pat_reg['sample_serial'] ; ?></th>
			<th style="width: 15%;">Hospital No.</th>
			<th>: <?php echo $pat_info["hosp_no"]; ?></th>
		</tr>
		<tr>
			<th>Name</th>
			<th>: <?php echo $pat_info["name"]; ?></th>
			<td>Ward</td>
			<td>: <?php echo $wardName["ward_name"]; ?></td>
		</tr>
		<tr>
			<td>Age - Sex</td>
			<td>: <?php echo $age; ?> - <?php echo $pat_info["sex"]; ?></td>
			<td>Collection Time</td>
			<td>: <?php echo date("d-m-Y",strtotime($sample_collection_date))." / ".date("h:i A",strtotime($sample_collection_time));?></td>
		</tr>
		<tr>
			<td>Primary Sample(s)</td>
			<td>: <?php echo $sample_names; ?></td>
			<td>Completion Time</td>
			<td>: <?php echo date("d-m-Y",strtotime($report_time['date']))." / ".date("h:i A",strtotime($report_time['time']));?></td>
		</tr>
	</table>
</div>
<?php
if($dept_info)
{
	$dept_info["name"]=str_replace("(Culture)","",$dept_info["name"]);
	
	echo "<center><u><b style='font-size: 15px;'>Department of $dept_info[name]</b></u></center>";
	echo "<br>";
}
?>
