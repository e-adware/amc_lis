<div class="img_header"><img src="../../images/header.jpg" style="width:100%;height:50px;"/></div>
<div>
	<table class="table table-condensed patient_header">
		<tr>
			<th style="width: 15%;">Bill No.</th>
			<th>: <?php echo $bill_id; ?></th>
			<th style="width: 15%;">ULID</th>
			<th>: <?php echo $uhid; ?></th>
		</tr>
		<tr>
			<th>Name</th>
			<th>: <?php echo $pat_info["name"]; ?></th>
			<th>Age/Sex</th>
			<td>: <?php echo $age; ?> / <?php echo $pat_info["sex"]; ?></td>
		</tr>
		<tr>
			<th>Address</th>
			<td colspan="3">: <?php echo $pat_info["city"]; ?></td>
		</tr>
		<tr>
			<th>Ref Doctor</th>
			<td colspan="3">: <?php echo $ref_doc["ref_name"]; ?></td>
		</tr>
		<tr>
			<td>Primary Sample(s)</td>
			<td colspan="3">: <?php echo $sample_names; ?></td>
		</tr>
		<tr>
			<td colspan="4">
				<table class="table table-condensed">
					<tr>
						<td style="text-align:center;">
							<div>Reg. Date/Time</div>
							<div><?php echo date("d-m-Y",strtotime($pat_reg["date"])); ?> <?php echo date("h:i A",strtotime($pat_reg["time"])); ?></div>
						</td>
						<td style="text-align:center;">
							<div>Sample Collection. Date/Time</div>
							<div><?php if($sample_collection_date){ echo date("d-m-Y",strtotime($sample_collection_date)); ?> <?php echo date("h:i A",strtotime($sample_collection_time)); } ?></div>
						</td>
						<td style="text-align:center;">
							<div>Sample Receive. Date/Time</div>
							<div><?php if($sample_receive_date){ echo date("d-m-Y",strtotime($sample_receive_date)); ?> <?php echo date("h:i A",strtotime($sample_receive_time)); } ?></div>
						</td>
						<td style="text-align:center;">
							<div>Reporting Date/Time</div>
							<div><?php echo date("d-m-Y",strtotime($report_time["date"])); ?> <?php echo date("h:i A",strtotime($report_time["time"])); ?></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<!--<tr>
			<td colspan="4">
				<div>
					<center>
					<img src="../../barcode-master/barcode.php?f=jpg&s=code-128&d=<?php echo $barcode_data;?>&h=45ms=r&tc=white"/>
					</center>
				</div>
			</td>
		</tr>-->
	</table>
</div>
