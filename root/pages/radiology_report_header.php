
<div>
	<table class="table table-condensed patient_header">
		<tr>
			<th style="width: 15%;">UHID</th>
			<th>: <?php echo $uhid; ?></th>
			<th style="width: 15%;">Bill No.</th>
			<th>: <?php echo $bill_id; ?></th>
		</tr>
		<tr>
			<th>Name</th>
			<th>: <?php echo $pat_info["name"]; ?></th>
			<th>Age/Sex</th>
			<td>: <?php echo $age; ?> / <?php echo $pat_info["sex"]; ?></td>
		</tr>
		<tr>
			<th>Ref Doctor</th>
			<td colspan="3">: <?php echo $ref_doc["ref_name"]; ?></td>
		</tr>
		<tr>
			<td>Reporting Time</td>
			<td colspan="3">: <?php echo date("d-m-Y",strtotime($report_time["date"])); ?> <?php echo date("h:i A",strtotime($report_time["time"])); ?></td>
		</tr>
		<tr>
			<td colspan="4">
				<div>
					<script src="../../JsBarcode/dist/JsBarcode.all.min.js"></script>
					<script src="../../JsBarcode/dist/JsBarcode.all.js"></script>
					<center>
						<div style="margin-left: -1%;margin-bottom: -4px;">
							<svg id="barcode3"></svg>
							<script>
								var val="<?php echo $barcode_data; ?>";
								JsBarcode("#barcode3", val, {
									format:"CODE128",
									displayValue:false,
									fontSize:10,
									width:1,
									height:20,
								});
							</script>
						</div>
					</center>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php
if($dept_info)
{
	echo "<center><u><b style='font-size: 15px;'>$dept_info[name]</b></u></center>";
	echo "<br>";
}
?>
