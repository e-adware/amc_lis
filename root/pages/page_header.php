<?php

if ($pat_reg["branch_id"]) {
	$company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));
	$cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));

	$branch_id = $pat_reg["branch_id"];
} else {
	if ($branch_id) {
		$company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$branch_id' limit 0,1 "));
		$cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$branch_id' limit 0,1 "));
	} else {
		$company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
		$cer = mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` limit 0,1 "));
	}
}

$signature = "For " . $company_info['name'];
$phon = "";
if ($company_info["phone1"])
	$phon .= $company_info["phone1"];
if ($company_info["phone2"])
	$phon .= ", " . $company_info["phone2"];
if ($company_info["phone3"])
	$phon .= ", " . $company_info["phone3"];

$header2 = "                       " . $company_info["address"] . ", " . $company_info["city"] . ", " . $company_info["state"] . "-" . $company_info["pincode"];
//$header3="     Phone Number(s): ".$phon." Email: ".$company_info["email"];
$header3 = "     Phone Number(s): " . $phon;

if ($qr_code_file_path) {
	?>
	<div style="position: absolute;left: 85%;top:1%;border: 1px solid #000;">
		<img src="<?php echo $qr_code_file_path; ?>" style="width:80px;" alt="QR Code">
	</div>
<?php } elseif ($opd_id) {
	?>
	<div style="position: absolute;left: 80%;top:1%;">
		<script src="../../JsBarcode/dist/JsBarcode.all.min.js"></script>
		<script src="../../JsBarcode/dist/JsBarcode.all.js"></script>
		<center>
			<div style="margin-left: -1%;">

				<svg id="barcode3"></svg>
				<script>

					var val = "<?php echo $opd_id; ?>";
					JsBarcode("#barcode3", val, {
						format: "CODE128",
						displayValue: true,
						fontSize: 10,
						width: 1,
						height: 20,
					});
				</script>
			</div>
		</center>
	</div>
<?php } ?>
<div class="row head_space">
	<div class="span2">
		<img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png"
			style="width:80px;margin-top:0px;margin-bottom:-70px;" />
	</div>
	<div class="span10 text-center" style="margin-left:0px;">
		<span style="font-size:12px;"><?php echo $page_head_line;
		; ?></span>
		<h4>
			<?php echo $company_info["name"]; ?><br>
			<small>
				<!--<?php echo $company_info["city"] . "-" . $company_info["pincode"] . ", " . $company_info["state"]; ?><br/>-->
				<?php echo $company_info["address"]; ?>
				<?php if ($company_info["phone1"]) { ?>
					<br>
					Contact: <?php echo $company_info["phone1"]; ?>
				<?php } ?>
				<?php echo $company_info["phone2"]; ?>

				<?php if ($company_info["email"]) { ?>
					<br>
					Email: <?php echo $company_info["email"]; ?>
				<?php } ?>
			</small>
		</h4>
	</div>
</div>
<style>
	h4 {
		margin: 0 0 10px 0;
	}

	.head_space {
		margin-bottom: 80px;
	}
</style>