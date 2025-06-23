<?php
include("../../includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];
$branch_id = $_POST['branch_id'];


// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date);
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}
if ($_POST["type"] == "head_wise_detail_pat") {
	$ward = $_POST["ward"];
	$encounter = $_POST["encounter"];
	$head_id = $_POST["head_id"];

	$head = mysqli_fetch_array(mysqli_query($link, " select type_name from testmaster where type_id='$head_id' "));

	$ward_str = "";
	if ($ward) {
		$ward_str = " AND b.`wardName`='$ward'";
	}

	if ($encounter == 3) {
		$str = " SELECT DISTINCT a.`opd_id`,a.`ipd_id` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`ipd_id`=b.`opd_id` and `testid` in ( SELECT `testid` FROM `testmaster` WHERE `type_id`='$head_id' ) AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`ipd_id` IN( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$encounter' ) and b.`branch_id`='$branch_id' $ward_str ORDER BY b.`date`";
	} else {
		$str = " SELECT DISTINCT a.`opd_id`,a.`ipd_id` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`testid` in ( SELECT `testid` FROM `testmaster` WHERE `type_id`='$head_id' ) AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`ipd_id`='' and b.`branch_id`='$branch_id' $ward_str ORDER BY b.`date`"; // AND `opd_id` IN( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$encounter' )
	}

	?>
	<p style="margin-top: 2%;"><b>Test Details from:</b> <?php echo convert_date($date1) . " to " . convert_date($date2); ?>
	</p>

	<b>Department Name: <?php echo $head["type_name"]; ?></b>
	<button type="button" class="btn btn-print btn-mini text-right print_div"
		onclick="print_page('<?php echo $_POST['type']; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $head_id; ?>','<?php echo $encounter; ?>','<?php echo $branch_id; ?>','<?php echo $ward; ?>')"
		style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>

	<a class="btn btn-excel btn-mini text-right print_div"
		href="pages/headwise_patwise_report_excel.php?date1=<?php echo $date1; ?>&date2=<?php echo $date2; ?>&head_id=<?php echo $head_id; ?>&encounter=<?php echo $encounter; ?>&branch_id=<?php echo $branch_id; ?>&ward=<?php echo $ward; ?>"
		style="margin-right: 1%;"><i class="icon-file icon-large"></i> Excel</a>
	</p>
	<table class="table table-condensed" id="patientDetailsTable">
		<thead class="table_header_fix">
			<tr>
				<th onclick="sortTable(0, 'patientDetailsTable', true)">#</th>
				<th onclick="sortTable(1, 'patientDetailsTable', false, 'date')">Date</th>
				<th onclick="sortTable(2, 'patientDetailsTable')">Hospital No</th>
				<!--<th onclick="sortTable(3, 'patientDetailsTable')">Bill No</th>-->
				<th onclick="sortTable(3, 'patientDetailsTable')">Patient Name</th>
				<th onclick="sortTable(4, 'patientDetailsTable')">Ward</th>
				<th onclick="sortTable(5, 'patientDetailsTable')">Test Name</th>
				<th onclick="sortTable(6, 'patientDetailsTable', false, 'datetime')">Collection Time</th>
				<th onclick="sortTable(7, 'patientDetailsTable', false, 'datetime')">Reporting Time</th>
				<th onclick="sortTable(8, 'patientDetailsTable')">Status</th>
				<!--<th onclick="sortTable(9, 'patientDetailsTable', true)">Total Amount</th>
				<th onclick="sortTable(10, 'patientDetailsTable')">Encounter</th>-->
			</tr>
		</thead>
		<tbody>
			<?php
			$n = 1;
			$qry = mysqli_query($link, $str);
			while ($dis_ipd = mysqli_fetch_array($qry)) {
				if ($dis_ipd["opd_id"]) {
					$pin = $dis_ipd["opd_id"];
				}
				if ($dis_ipd["ipd_id"]) {
					$pin = $dis_ipd["ipd_id"];
				}

				$pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$pin' "));

				$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));

				$ref_doc = mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));

				$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				$Encounter = $pat_typ_text['p_type'];

				$test_qry = mysqli_query($link, " SELECT a.`test_rate`,b.`testid`, b.`testname` FROM `patient_test_details` a, `testmaster` b WHERE a.`patient_id`='$pat_reg[patient_id]' AND a.`opd_id`='$dis_ipd[opd_id]' AND a.`ipd_id`='$dis_ipd[ipd_id]' AND b.`type_id`='$head_id' AND a.`testid`=b.`testid` ");
				while ($test = mysqli_fetch_array($test_qry)) {
					$collectionTime = "";
					$collTime = mysqli_fetch_array(mysqli_query($link, "SELECT `time`,`date` FROM `phlebo_sample` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$dis_ipd[opd_id]' AND `testid`='$test[testid]'"));
					if ($collTime) {
						$collectionTime = date("d-m-y", strtotime($collTime["date"])) . " " . date("h:i A", strtotime($collTime["time"]));
						$collectionSortValue = strtotime($collTime["date"] . " " . $collTime["time"]);
					}
					$reportingTime = "";
					$reportStatus = "pending";
					$repTime = mysqli_fetch_array(mysqli_query($link, "SELECT `time`,`date` FROM `testresults` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$dis_ipd[opd_id]' AND `testid`='$test[testid]' AND `result`!='' ORDER BY `slno` DESC LIMIT 1"));
					if ($repTime) {
						$reportingTime = date("d-m-y", strtotime($repTime["date"])) . " " . date("h:i A", strtotime($repTime["time"]));
						$reportStatus = "Reported";
						$reportingSortValue = strtotime($repTime["date"] . " " . $repTime["time"]);
					}
					?>
					<tr>
						<td><?php echo $n; ?></td>
						<td data-sort="<?php echo strtotime($pat_reg["date"]); ?>">
							<?php echo date("d-m-Y", strtotime($pat_reg["date"])); ?>
						</td>
						<td><?php echo $pat_info['patient_id']; ?></td>
						<!--<td><?php echo $pin; ?></td>-->
						<td><?php echo $pat_info['name']; ?></td>
						<td><?php echo $pat_reg["wardName"]; ?></td>
						<td><?php echo $test["testname"]; ?></td>
						<td data-sort="<?php echo isset($collectionSortValue) ? $collectionSortValue : 0; ?>">
							<?php echo $collectionTime; ?>
						</td>
						<td data-sort="<?php echo isset($reportingSortValue) ? $reportingSortValue : 0; ?>">
							<?php echo $reportingTime; ?>
						</td>
						<td><?php echo $reportStatus; ?></td>
						<!--<td style="text-align:right;"><?php echo number_format($tot_test, 2); ?></td>
					<td><?php echo $Encounter; ?></td>-->
					</tr>
					<?php
					$n++;
				}
			}
			?>
		</tbody>
	</table>
	<?php
}
if ($_POST["type"] == "head_wise_test_detail") {
	//print_r($_POST);
	$encounter = $_POST["encounter"];
	$testid = $_POST["testid"];
	$head_id = $_POST["head_id"];

	$test_str = "";
	if ($testid) {
		$test_str = " AND a.`testid`='$testid'";
	}

	$head = mysqli_fetch_array(mysqli_query($link, " select type_name from testmaster where type_id='$head_id' "));

	//$encounter_str=" AND c.`type`='$encounter'";

	if ($encounter == 3) {
		$encounter_str = " AND a.opd_id='' AND a.ipd_id!=''";
	} else {
		$encounter_str = " AND a.opd_id!='' AND a.ipd_id=''";
	}

	?>
	<p style="margin-top: 2%;"><b>Test Wise Details from:</b>
		<?php echo convert_date($date1) . " to " . convert_date($date2); ?>
		<br>
		<b>Department Name: <?php echo $head["type_name"]; ?></b>
		<button type="button" class="btn btn-print btn-mini text-right print_div"
			onclick="print_page('<?php echo $_POST["type"]; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $head_id; ?>','<?php echo $encounter; ?>','<?php echo $branch_id; ?>','<?php echo $testid; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>

		<a class="btn btn-excel btn-mini text-right print_div"
			href="pages/headwise_testwise_report_excel.php?date1=<?php echo $date1; ?>&date2=<?php echo $date2; ?>&head_id=<?php echo $head_id; ?>&encounter=<?php echo $encounter; ?>&branch_id=<?php echo $branch_id; ?>&testid=<?php echo $testid; ?>"
			style="margin-right: 1%;"><i class="icon-file icon-large"></i> Excel</a>
	</p>
	<table class="table table-condensed" id="testDetailsTable">
		<thead class="table_header_fix">
			<tr>
				<th onclick="sortTable(0, 'testDetailsTable')">#</th>
				<th onclick="sortTable(1, 'testDetailsTable')">Test Name</th>
				<th onclick="sortTable(2, 'testDetailsTable', true)">No. of Test</th>
				<!--<th onclick="sortTable(3, 'testDetailsTable', true)">Total Amount</th>-->
			</tr>
		</thead>
		<tbody>
			<?php

			$n = 1;
			$total_test_num = 0;
			$grand_total = 0;

			$qry = mysqli_query($link, " SELECT DISTINCT a.`testid` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`type_id`='$head_id' $encounter_str and c.`branch_id`='$branch_id' $test_str ORDER BY b.`testname` ");

			//~ $qry=mysqli_query($link, " SELECT DISTINCT a.`testid` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`type_id`='$head_id' $encounter_str ORDER BY b.`testname` ");
			while ($dist_testid = mysqli_fetch_array($qry)) {
				$test_info = mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$dist_testid[testid]' "));

				$test_num = mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details`  a, `uhid_and_opdid` c WHERE a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND a.`testid`='$dist_testid[testid]' AND a.`date` BETWEEN '$date1' AND '$date2' $encounter_str and c.`branch_id`='$branch_id' "));

				$test_amount = mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`test_rate`) AS `test_sum` FROM `patient_test_details` a, `uhid_and_opdid` c WHERE a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND a.`testid`='$dist_testid[testid]' AND a.`date` BETWEEN '$date1' AND '$date2' $encounter_str and c.`branch_id`='$branch_id' "));
				?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $test_info['testname']; ?></td>
					<td><?php echo $test_num; ?></td>
					<!--<td style="text-align:right;"><?php echo number_format($test_amount["test_sum"], 2); ?></td>-->
				</tr>
				<?php
				$total_test_num += $test_num;
				$grand_total += $test_amount["test_sum"];
				$n++;
			}
			?>
		</tbody>
		<!--<tfoot>
			<tr>
				<th colspan="2"><span class="text-right">Grand Total: </span></th>
				<td><?php echo $total_test_num; ?></td>
				<td style="text-align:right;"><?php echo number_format($grand_total, 2); ?></td>
			</tr>
		</tfoot>-->
	</table>

	<?php
}

if ($_POST["type"] == "tat_non_confirmity_test_detail") {
	//print_r($_POST);
	$encounter = $_POST["encounter"];
	$testid = $_POST["testid"];
	$head_id = $_POST["head_id"];
	$user = $_POST["user"];

	$test_str = "";
	if ($testid) {
		$test_str = " AND a.`testid`='$testid'";
	}
	$str = "";
	if ($date1 && $date2) {
		$str = "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`date`,a.`time`,a.`testid` FROM `testresults` a, `testmaster` b 
                WHERE a.`testid`=b.`testid` AND (a.`doc`>'0' OR a.`main_tech`>'0') $test_str
                AND b.`category_id` = 1 AND a.`date` BETWEEN '$date1' AND '$date2'";
	}
	$str .= " GROUP BY a.patient_id,a.opd_id,a.ipd_id,a.batch_no,a.testid";
	//echo $str;


	?>
	<table class="table table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Hospital No</th>
				<th>Patient Name</th>
				<th>Test Name</th>
				<th>Expected TAT</th>
				<th>Actual TAT</th>
				<th>Action</th>
			</tr>
		</thead>
		<?php
		$j = 1;
		$qry = mysqli_query($link, $str);
		while ($data = mysqli_fetch_array($qry)) {
			$patient_id = $data["patient_id"];
			$opd_id = $data["opd_id"];
			$ipd_id = $data["ipd_id"];
			$batch_no = $data["batch_no"];
			$testid = $data["testid"];
			
			$sampleDate=mysqli_fetch_array(mysqli_query($link,"SELECT `date`, `time` FROM `phlebo_sample` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `ipd_id` = '$ipd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid'"));

			$reg_date = $sampleDate["date"];
			$reg_time = date("H:i:s", strtotime($sampleDate["time"]));
			$reg_date_time = $sampleDate["date"] . " " . date("H:i:s", strtotime($sampleDate["time"]));

			$approve_date = "0000-00-00";
			$approve_time = "00:00:00";
			$approve_date_time = "";
			$doc_approve["date"] = "0000-00-00";
			$doc_approve["time"] = "00:00:00";
			$approve_date_time = "0000-00-00 00:00:00";
	
			//$doc_approve = mysqli_fetch_array(mysqli_query($link, "SELECT `time`, `date` FROM `testresults` WHERE `patient_id` = '$patient_id' AND (`opd_id` = '$opd_id' OR `ipd_id` = '$ipd_id') AND `batch_no` = '$batch_no' AND `testid` = '$testid' order by `slno` desc limit 1"));
			$doc_approve = mysqli_fetch_array(mysqli_query($link, "SELECT t_time, t_date, `d_time`, `d_date` FROM `approve_details` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id' AND `ipd_id` = '$ipd_id' AND `batch_no` = '$batch_no' AND `testid` = '$testid'"));


			if ($doc_approve) {
				if ($doc_approve['d_date'] != "0000-00-00" && $doc_approve['d_time'] != "00:00:00") {
					$doc_approve["date"] = $doc_approve['d_date'];
					$doc_approve["time"] = $doc_approve['d_time'];
				} else {
					$doc_approve["date"] = $doc_approve['t_date'];
					$doc_approve["time"] = $doc_approve['t_time'];
				}
			}

			if ($doc_approve && $doc_approve["date"] != "0000-00-00") {
				$approve_date = $doc_approve["date"];
				$approve_time = date("H:i:s", strtotime($doc_approve["time"]));
				$approve_date_time = $doc_approve["date"] . " " . $doc_approve["time"];
			}

			$datetime1 = new DateTime($reg_date_time);
			$datetime2 = new DateTime($approve_date_time);
			$interval = $datetime1->diff($datetime2);

			$tat_str = $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes";
			$tat_hour = $interval->format('%h');
			$tat_minutes = $interval->format('%i') + ($tat_hour * 60);

			// uhid_and_opdid
			$urgent = mysqli_fetch_array(mysqli_query($link, "SELECT `urgent` FROM `uhid_and_opdid` WHERE `patient_id` = '$patient_id' AND `opd_id` = '$opd_id'"));
			$urgent_status = $urgent["urgent"];

			if ($doc_approve && $doc_approve["date"] != "0000-00-00") {

				$status = 0;
				if ($urgent_status == 0) {
					$test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `turn_around_time_routine` AS `tat_time` FROM `testmaster` WHERE `testid` = '$testid'"));
				} else {
					// urgent test
					$test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `turn_around_time_urgent` AS `tat_time` FROM `testmaster` WHERE `testid` = '$testid'"));
				}

				if ($tat_minutes > $test_info["tat_time"]) {
					$status = 2; // Exceeded
				} else {
					$status = 1; // Within
				}
	
				if ($status == 2) {
					$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
					$tst = mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$testid' "));
					$tat_max_time = $test_info["tat_time"];
					$total_tat_minutes = $tat_minutes;//Total minutes
	
					$hours = intdiv($tat_max_time, 60);
					$remainingMinutes = $tat_max_time % 60;

					$class = "btn-info";
					$chk = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `tat_nc_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid' AND (`delay`!='' OR `nc_action`!='')"));
					if ($chk) {
						$class = "btn-success";
					}
					?>
					<tr>
						<td><?php echo $j; ?></td>
						<td><?php echo $pat_info['hosp_no']; ?></td>
						<td><?php echo $pat_info['name']; ?></td>
						<td><?php echo $tst['testname']; ?></td>
						<td><?php echo "$hours Hours";
						if ($remainingMinutes > 0) {
							echo " $remainingMinutes Minutes";
						} ?></td>
						<td><?php echo $tat_str; ?></td>
						<td>
							<button type="button" class="btn <?php echo $class; ?> btn-mini"
								onclick="action_cnf('<?php echo base64_encode($patient_id); ?>','<?php echo base64_encode($opd_id); ?>','<?php echo base64_encode($testid); ?>')"><i
									class="icon-cogs icon-large"></i></button>
						</td>
					</tr>
					<?php
					$j++;
				}
			}
		}
		?>
	</table>
	<?php
}


if ($_POST["type"] == "action_cnf") {
	//print_r($_POST);
	$pid = base64_decode($_POST["pid"]);
	$opd = base64_decode($_POST["opd"]);
	$tid = base64_decode($_POST["tid"]);
	$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name`, `hosp_no` FROM `patient_info` WHERE `patient_id`='$pid'"));
	$test = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$tid'"));
	$chk = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `tat_nc_details` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `testid`='$tid'"));
	?>
	<table class="table table-condensed">
		<tr>
			<th>Hospital No</th>
			<th>Patient Name</th>
			<th>Test Name</th>
		</tr>
		<tr>
			<td><?php echo $pat_info['hosp_no']; ?></td>
			<td><?php echo $pat_info['name']; ?></td>
			<td><?php echo $test['testname']; ?></td>
		</tr>
		<tr>
			<th colspan="3">Reason for delay</th>
		</tr>
		<tr>
			<td colspan="3">
				<textarea class="span10" id="delayNote" style="resize:none;"><?php echo $chk['delay']; ?></textarea>
			</td>
		</tr>
		<tr>
			<th colspan="3">Corrective action</th>
		</tr>
		<tr>
			<td colspan="3">
				<textarea class="span10" id="correctiveNote"
					style="resize:none;"><?php echo $chk['nc_action']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center;">
				<button type="button" class="btn btn-primary" id="btnSave"
					onclick="non_tat_action_save('<?php echo base64_encode($pid); ?>','<?php echo base64_encode($opd); ?>','<?php echo base64_encode($tid); ?>')">Save</button>
				<button type="button" class="btn btn-danger" id="btnClose" data-dismiss="modal">Close</button>
			</td>
		</tr>
	</table>
	<?php
}



if ($_POST["type"] == "non_tat_action_save") {
	//print_r($_POST);
	$pid = base64_decode($_POST["pid"]);
	$opd = base64_decode($_POST["opd"]);
	$tid = base64_decode($_POST["tid"]);
	$delayNote = mysqli_real_escape_string($link, $_POST["delayNote"]);
	$correctiveNote = mysqli_real_escape_string($link, $_POST["correctiveNote"]);
	$user = $_POST["user"];

	$chk = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `tat_nc_details` WHERE `patient_id`='$pid' AND `opd_id`='$opd' AND `testid`='$tid'"));
	if ($chk) {
		mysqli_query($link, "UPDATE `tat_nc_details` SET `delay`='$delayNote', `nc_action`='$correctiveNote' WHERE `slno`='$chk[slno]'");
	} else {
		mysqli_query($link, "INSERT INTO `tat_nc_details`(`patient_id`, `opd_id`, `testid`, `delay`, `nc_action`, `date`, `time`, `user`) VALUES ('$pid','$opd','$tid','$delayNote','$correctiveNote','$date','$time','$user')");
		//echo "INSERT INTO `tat_nc_details`(`patient_id`, `opd_id`, `testid`, `delay`, `nc_action`, `date`, `time`, `user`) VALUES ('$pid','$opd','$tid','$delayNote','$correctiveNote','$date','$time','$user')";
	}
}


if ($_POST["type"] == "non_conformity_report") {
	//print_r($_POST);
	$qry = "SELECT * FROM `tat_nc_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `delay`!='' AND `nc_action`!=''";
	//echo $qry;
	?>
	<div class="print_div">
		<button type="button" class="btn btn-primary btn-mini"
			onclick="report_print('<?php echo base64_encode($date1); ?>','<?php echo base64_encode($date2); ?>')"><i
				class="icon-print icon-large"></i> Print</button>
		<button type="button" class="btn btn-success btn-mini"
			onclick="report_export('<?php echo base64_encode($date1); ?>','<?php echo base64_encode($date2); ?>')"><i
				class="icon-file icon-large"></i> Export</button>
	</div>
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Hospital No</th>
				<th>Patient Name</th>
				<th>Test Name</th>
				<th style="width:25%;">Reason for delay</th>
				<th style="width:25%;">Corrective action</th>
				<th>Date Time</th>
				<th>User</th>
			</tr>
		</thead>
		<?php
		$j = 1;
		$q = mysqli_query($link, $qry);
		while ($r = mysqli_fetch_array($q)) {
			$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name`,`hosp_no` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			$test = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
			$emp = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
			?>
			<tr>
				<td><?php echo $j; ?></td>
				<td><?php echo $pat_info['hosp_no']; ?></td>
				<td><?php echo $pat_info['name']; ?></td>
				<td><?php echo $test['testname']; ?></td>
				<td class="text-justify"><?php echo $r['delay']; ?></td>
				<td class="text-justify"><?php echo $r['nc_action']; ?></td>
				<td><?php echo date("d-m-y", strtotime($r['date'])) . " " . date("h:i A", strtotime($r['time'])); ?></td>
				<td><?php echo $emp['name']; ?></td>
			</tr>
			<?php
			$j++;
		} ?>
	</table>
	<?php
}


if ($_POST["type"] == "qwqwqwqwqwq") {
	?>
	<p style="margin-top: 2%;"><b>Details from:</b> <?php echo convert_date($date1) . " to " . convert_date($date2); ?></p>

	<?php
}
?>