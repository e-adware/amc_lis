<?php
	if($dt_tm)
	{
		$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt_tm[type]' "));
	}
	if($reg)
	{
		$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$reg[type]' "));
	}
	$v_text=$prefix_det["prefix"];

	if($ipd_id)
	{
		// Test Ref Doctor
		$ref_doc_val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' "));
		if($ref_doc_val["refbydoctorid"]>0)
		{
			$ref_doc_val=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND batch_no='$batch_no' ) "));
			if($ref_doc_val)
			{
				$dname=$ref_doc_val["ref_name"];
			}
		}
	}
	if($pinfo["dob"]!=""){ $age=age_calculator($pinfo["dob"]); }else{ $age=$pinfo["age"]." ".$pinfo["age_type"]; }
?>

<table class="table borderless bordert-top-bottom">
	<tr>
		<td><b>UHID</b></td>
		<td><b>: <?php echo $uhid_id;?></b></td>
		<td><b><?php echo $v_text; ?></b></td>
		<td><b>: <?php echo $v_id; ?></b></td>
	</tr>
	<tr>
		<td width="15%"><b>Name</b></td>
		<td><b>: <?php echo $pinfo['name'];?></b></td>
		<td>Collection Date:</td>
		<td>: <?php echo convert_date($collection_date)."/".convert_time($collection_time);?></td>
<!--
		<td>: <?php echo convert_date($collection_date);?></td>
-->
	</tr>
	<tr>
		<td>Age/Sex</td>
		<td>: <?php echo $age." / ".$pinfo[sex];?> </td>
		<td>Completion Date</td>
		<td>: <?php echo convert_date($res_time[date])."/".convert_time($res_time[time]);?></td>
<!--
		<td>: <?php echo convert_date($res_time[date]);?></td>
-->
	</tr>
	<tr>
		<td>Ref. by Doctor</td>
		<td colspan="3">: <?php echo $dname;?></td>
	</tr>
	
	<tr>
		<td>Primary Sample</td>
		<td colspan="3">: <?php echo $sample_name;?></td>
	</tr>
	<tr style="border: 1px solid #000;">
		<td colspan="4">
			<div>
				<script src="../../JsBarcode/dist/JsBarcode.all.min.js"></script>
				<script src="../../JsBarcode/dist/JsBarcode.all.js"></script>
				<center>
					<div style="margin-left: -1%;">
						
						<svg id="barcode3"></svg>
						<script>
							
							var val="<?php echo $uhid_id.'-'.$v_id.'-'.$pinfo['name']; ?>";
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
