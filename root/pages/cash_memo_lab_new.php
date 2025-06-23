<?php
session_start();

include ("../../includes/connection.php");
require ("../../includes/global.function.php");

$c_user = $_SESSION["emp_id"];

$patient_id = $uhid = mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id = mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$ipd_id = "";
$batch_no = 1;
$user = mysqli_real_escape_string($link, base64_decode($_GET["user"]));
$v = mysqli_real_escape_string($link, base64_decode($_GET["v"]));

if ($val == 0) {
	$page_head_name = "Delivery Slip";
}
if ($val == 1) {
	$page_head_name = "Bill";
}


$final_pay_qry = mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
$final_pay_num = mysqli_num_rows($final_pay_qry);

if ($final_pay_num > 1) {
	$h = 1;
	while ($final_pay_val = mysqli_fetch_array($final_pay_qry)) {
		if ($h > 1) {
			mysqli_query($link, " DELETE FROM `invest_patient_payment_details` WHERE `slno`='$final_pay_val[slno]' ");
		}
		$h++;
	}
}

$emp = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user' "));

$company_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$pat_reg = $dt_tm = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$centre_info = mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]' "));

$ref_doc = mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if (!$ref_doc) {
	$ref_doc = mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$pat_pay_detail = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$inv_pat_test_detail_qry = mysqli_query($link, " SELECT a.`test_rate`,b.`testname`,a.`testid` FROM `patient_test_details` a,`testmaster` b  WHERE a.`testid`=b.`testid` and a.`patient_id`='$uhid' and a.`opd_id`='$opd_id' and a.`addon_testid`=0");
$inv_pat_test_detail_num = mysqli_num_rows($inv_pat_test_detail_qry);

$tot = 0;

$ss = 1;
$sample_names = "";
$sample_qry = mysqli_query($link, "SELECT DISTINCT a.`sample` FROM `Parameter_old` a, `Testparameter` b, `patient_test_details` c WHERE a.`ID`=b.`ParamaterId` AND b.`TestId`=c.`testid` AND a.`sample`>0 AND c.`patient_id`='$uhid' AND c.`opd_id`='$opd_id'");
while ($samples = mysqli_fetch_array($sample_qry)) {
	$sample_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Sample` WHERE `ID`='$samples[sample]'"));

	if ($ss == 1) {
		$sample_names = $sample_info["Name"];
	} else {
		$sample_names .= ", " . $sample_info["Name"];
	}

	$ss++;
}


// QR code Data for Online Reports
$randomString = generateRandomString(10);
$qr_code_data = "https://lcdonline.in/lcd/QrReprts/?".$randomString."=".base64_encode($randomString)."&U14H25I36D=".base64_encode($uhid)."&O47I58D=".base64_encode($opd_id)."&I74I85D=".base64_encode($ipd_id)."&B69N96O=".base64_encode($batch_no)."&P69A96THO=".base64_encode(1);

// QR Code Start
include('../../phpqrcode/qrlib.php');
$tempDir = '../../phpqrcode/temp/'; 

$filename = $c_user.str_replace("/", "", $pat_reg["opd_id"]).'.png';

$target_file="../../phpqrcode/temp/".$c_user."*.*";

foreach (glob($target_file) as $filename_del) {
	unlink($filename_del);
}

QRcode::png($qr_code_data, $tempDir.''.$filename, QR_ECLEVEL_S, 8);

$qr_code_file_path="../../phpqrcode/temp/".$filename;
// QR Code End

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>
		<?php echo $page_head_name; ?>
	</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>

</head>

<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="">
			<?php include ('page_header.php'); ?>
		</div>
		<hr>
		<div style="text-align: center;font-weight: bold;font-size: 15px;">
			<?php echo $page_head_name; ?>
		</div>
		<?php include ('patient_header_lab.php'); ?>
		<hr>
		<table class="table table-condensed">
			<tr>
				<th style="padding: 0px 10px 0px 10px;width: 20px;">#</th>
				<th>Service Particulars</th>
				<th style='text-align:right;'>Amount</th>
				<?php if ($inv_pat_test_detail_num > 1) { ?>
					<th style="padding: 0px 10px 0px 10px;width: 20px;">#</th>
					<th>Service Particulars</th>
					<th style='text-align:right;'>Amount</th>
				<?php } ?>
			</tr>
			<?php
			$i = 1;
			$j = 1;
			while ($inv_pat_test_detail = mysqli_fetch_array($inv_pat_test_detail_qry)) {
				if ($i == 1) {
					echo "<tr>";
				}

				$j = str_pad($j, 2, "0", STR_PAD_LEFT);

				echo "<td style='padding: 0px 10px 0px 10px;'>$j. </td>";
				echo "<td>$inv_pat_test_detail[testname] </td>";
				echo "<td style='text-align:right;'>$inv_pat_test_detail[test_rate]</td>";

				if ($i == 2) {
					echo "</tr>";
					$i = 1;
				} else {
					$i++;
				}

				$tot = $tot + $inv_pat_test_detail["test_rate"];

				$j++;
			}
			?>
		</table>
		<table class="table table-condensed">
			<tr>
				<td rowspan="4" style="vertical-align: middle;">
					<?php
					$pmode_qry = mysqli_query($link, "SELECT DISTINCT `payment_mode` FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `payment_mode`!='Credit'");
					while ($pmode = mysqli_fetch_array($pmode_qry)) {
						$pmpde_sum = mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`amount`) AS `paid`, SUM(`refund_amount`) AS `refund` FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `payment_mode`='$pmode[payment_mode]'"));

						$pmpde_paid_amount = $pmpde_sum["paid"] - $pmpde_sum["refund"];

						echo "<div>Received a sum of " . number_format($pmpde_paid_amount, 2) . " in $pmode[payment_mode]. </div>";
					}
					?>
				</td>
				<th style='text-align:right;'>Total Amount</th>
				<th style='text-align:right;'>
					<?php echo number_format($pat_pay_detail["tot_amount"], 2); ?>
				</th>
			</tr>
			<?php
			if ($pat_pay_detail["dis_amt"] > 0) {
				$dis_per = round(($pat_pay_detail["dis_amt"] / $pat_pay_detail["tot_amount"]) * 100, 2);
				?>
				<tr>
					<th style='text-align:right;'>Discount Amount</th>
					<th style='text-align:right;'>
						<?php echo number_format($pat_pay_detail["dis_amt"], 2); ?>
					</th>
				</tr>
			<?php } ?>
			<?php
			if ($pat_pay_detail["advance"] > 0) {
				?>
				<tr>
					<th style='text-align:right;'>Paid Amount</th>
					<th style='text-align:right;'>
						<?php echo number_format($pat_pay_detail["advance"], 2); ?>
					</th>
				</tr>
			<?php } ?>
			<?php
			if ($pat_pay_detail["balance"] >= 0) {
				?>
				<tr>
					<th style='text-align:right;'>Balance Amount</th>
					<th style='text-align:right;'>
						<?php echo number_format($pat_pay_detail["balance"], 2); ?>
					</th>
				</tr>
			<?php } ?>
		</table>
		<?php
		if ($pat_reg["report_delivery_time"]) {
			echo '<div style="font-weight: bold;font-size: 14px;">Report Delivery: ' . $pat_reg["report_delivery_time"] . '</div>';
		}
		?>
		<div>
			* Open Between 8AM to 8PM : Sunday 8AM to 2PM
		</div>
		<div class="row">
			<div class="span6">
				<ul style="margin: 0 0 0px 1px;">
					NB:
					<li style="list-style-type: none;"># We are not responsible for reports not collected within three
						months</li>
					<li style="list-style-type: none;"># Kindly cross check your patient details of the report at the
						time of report collection.</li>
				</ul>
			</div>
			<div class="span3 text-right">
				<b>For
					<?php echo $company_info["name"]; ?>
				</b>
				<br>
				(
				<?php echo $emp["name"]; ?>)
				<br>
				<span style="float:right;font-size: 7px;">
					<?php echo date("d-m-Y h:i:s A"); ?>
				</span>
			</div>
		</div>
		<hr>
		<div>Indian Rupees
			<?php echo convert_number($pat_pay_detail["advance"]); ?> Only
		</div>
	</div>
	<?php

	$str = "SELECT a.`testid`,a.`category_id`,a.`type_id`,a.`lineno` FROM `testmaster` a, `patient_test_details` b WHERE a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id'";

	$str .= " AND a.`type_id`!='132'"; // Extra
	
	$str .= " ORDER BY a.`type_id`,a.`lineno`,a.`testid` ASC";

	$type_id = 0;
	$lineno = 0;
	$page_no = 1;

	$qry = mysqli_query($link, $str);
	while ($data = mysqli_fetch_array($qry)) {
		$page_break = 0;

		$testid = $data["testid"];
		$category_id = $data["category_id"];

		if ($type_id != $data["type_id"]) {
			$type_id = $data["type_id"];

			$page_no++;
		} else if ($data["lineno"] != $lineno) {
			$lineno = $data["lineno"];

			$page_no++;
		} else {
			$test_param = mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(COUNT(`ParamaterId`),0) AS `total` FROM `Testparameter` WHERE `TestId`='$testid'"));
			$test_param_num = $test_param["total"];

			if ($test_param_num > 40) {
				$page_no++;

				$page_break++;
			}
		}
		
		mysqli_query($link, "INSERT INTO `trf_print`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `category_id`, `type_id`, `testid`, `page_no`, `user`, `ip_addr`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$category_id','$type_id','$testid','$page_no','$c_user','$ip_addr')");

		if ($page_break > 0) {
			$page_no++;
		}
	}

	$page_qry = mysqli_query($link, "SELECT DISTINCT `page_no` FROM `trf_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `page_no` ASC");
	while ($pages = mysqli_fetch_array($page_qry)) {
		$page_no = $pages["page_no"];

		$page_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `trf_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no'"));
		$type_id = $page_info["type_id"];

		$dept_info = mysqli_fetch_array(mysqli_query($link, "SELECT `id`,`name` FROM `test_department` WHERE `id`='$type_id'"));

		// Sample
		$sample_names = "";

		$sample_qry = mysqli_query($link, "SELECT DISTINCT a.`Name` FROM `Sample` a, `patient_test_details` b WHERE a.`ID`=b.`sampleid` AND b.`patient_id`='$patient_id' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no' AND b.`testid` IN(SELECT DISTINCT `testid` FROM `trf_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no')");

		while ($samples = mysqli_fetch_array($sample_qry)) {
			$sample_names .= $samples["Name"] . ",";
		}

		$phlb_receive = mysqli_fetch_array(mysqli_query($link, "SELECT b.* FROM `testmaster` a,`phlebo_sample` b WHERE b.`patient_id`='$patient_id' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no' AND a.`testid`=b.`testid` AND a.`type_id`='$type_id' ORDER BY `slno` ASC LIMIT 1"));
		$lab_receive = mysqli_fetch_array(mysqli_query($link, "SELECT b.* FROM `testmaster` a,`patient_test_details` b WHERE b.`patient_id`='$patient_id' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no' AND a.`testid`=b.`testid` AND a.`type_id`='$type_id' ORDER BY `slno` ASC LIMIT 1"));

		$phlb_user = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$phlb_receive[user]'"));
		$lab_user = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$lab_receive[user]'"));
		?>
		<div class="pagebreak"></div>

		<?php
	}
	?>
	</div>
	<span id="user" style="display:none;">
		<?php echo $user; ?>
	</span>
</body>

</html>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim() != '101' && $("#user").text().trim() != '102') { // Prevent F12
			return false;
		} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim() != '101' && $("#user").text().trim() != '102') { // Prevent Ctrl+Shift+I        
			return false;
		}
	});
	$(document).on("contextmenu", function (e) {
		if ($("#user").text().trim() != '101' && $("#user").text().trim() != '102') {
			e.preventDefault();
		}
	});
	$(document).ready(function () {
		if ($("#user").text().trim() != '101' && $("#user").text().trim() != '102') {
			window.print();
		}
	});
	function close_window(e) {
		var unicode = e.keyCode ? e.keyCode : e.charCode;

		if (unicode == 27) {
			window.close();
		}
	}
</script>
<style>
	.table>thead>tr>th,
	.table>tbody>tr>th,
	.table>tfoot>tr>th,
	.table>thead>tr>td,
	.table>tbody>tr>td,
	.table>tfoot>tr>td {
		padding: 0 0 0 0;
	}

	.table {
		margin-bottom: 1px;
	}

	hr {
		margin: 0;
		border-bottom: 0;
		border-top: 1px solid #000;
	}

	.page_break {
		page-break-before: always;
		padding-top: 5px;
	}

	.req_slip {
		min-height: 520px;
	}

	.f_req_slip {
		min-height: 670px;
	}

	.rad_req_slip {
		min-height: 300px;
	}

	* {
		font-size: 11px;
	}

	@page {
		margin: 0.2cm;
	}

	@media print {
		.pagebreak {
			clear: both;
			page-break-after: always;
		}
	}

	.div1 {
		width: 400px;
	}

	.div2 {
		width: 300px;
	}

	.sing_par {
		display: inline-block;
		padding: 0px;
		font-size: 12px;
	}

	.sing_par_i {
		display: inline-block;
		padding-bottom: 5px;
		font-style: italic;
		font-size: 13px;
	}
</style>
<?php
mysqli_query($link, "DELETE FROM `trf_print` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr'");
?>
