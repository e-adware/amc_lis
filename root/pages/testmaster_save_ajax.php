<?php
include("../../includes/connection.php");

$time = date("H:i:s");

$date = date('Y-m-d'); // impotant
$user = $_POST["user"];

if ($_POST["typ"] == "save") {
	$testid = mysqli_real_escape_string($link, $_POST["testid"]);
	$testname = mysqli_real_escape_string($link, $_POST["testname"]);
	$test_code = mysqli_real_escape_string($link, $_POST["test_code"]);
	$category_id = mysqli_real_escape_string($link, $_POST["category_id"]);
	$type_id = mysqli_real_escape_string($link, $_POST["type_id"]);
	$instruction = mysqli_real_escape_string($link, $_POST["instruction"]);
	$rd_day = mysqli_real_escape_string($link, $_POST["rd_day"]);
	$rd_hour = mysqli_real_escape_string($link, $_POST["rd_hour"]);
	$rd_minute = mysqli_real_escape_string($link, $_POST["rd_minute"]);
	$max_tat_day = mysqli_real_escape_string($link, $_POST["max_day"]);
	$max_tat_hour = mysqli_real_escape_string($link, $_POST["max_hour"]);
	$max_tat_minute = mysqli_real_escape_string($link, $_POST["max_minute"]);
	$report_delivery_2 = mysqli_real_escape_string($link, $_POST["report_delivery_2"]);
	$samp = mysqli_real_escape_string($link, $_POST["sample_details"]);
	$out_sample = mysqli_real_escape_string($link, $_POST["out_sample"]);
	$vacc = mysqli_real_escape_string($link, $_POST["vacc"]);
	$rate = mysqli_real_escape_string($link, $_POST["rate"]);
	$sex = mysqli_real_escape_string($link, $_POST["sex"]);
	$equipment = mysqli_real_escape_string($link, $_POST["equipment"]);

	$turn_around_time_min_str = $rd_day . "@" . $rd_hour . "#" . $rd_minute;

	$turn_around_time_min = ($rd_day * 24 * 60) + ($rd_hour * 60) + $rd_minute;

	$turn_around_time_max_str = $max_tat_day . "@" . $max_tat_hour . "#" . $max_tat_minute;

	$turn_around_time_max = ($max_tat_day * 24 * 60) + ($max_tat_hour * 60) + $max_tat_minute;

	$notes = "";
	$lineno = 0;
	$vac_charge = 0;
	$sequence = 0;

	if (!$rate) {
		$rate = 0;
	}

	$dept_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `test_department` WHERE `id`='$type_id'"));

	$type_name = $dept_info["name"];

	if ($testid == 0) {
		if (mysqli_query($link, "INSERT INTO `testmaster`(`test_code`, `testname`, `rate`, `instruction`, `notes`, `turn_around_time_routine_str`, `turn_around_time_routine`, `turn_around_time_urgent_str`, `turn_around_time_urgent`, `report_delivery`, `sample_details`, `category_id`, `type_id`, `type_name`, `equipment`, `sex`, `lineno`, `vac_charge`, `out_sample`,`suffix`) VALUES ('$test_code','$testname','$rate','$instruction','$notes','$turn_around_time_min_str','$turn_around_time_min','$turn_around_time_max_str','$turn_around_time_max','$report_delivery_2','$sample_details','$category_id','$type_id','$type_name','$equipment','$sex','$lineno','$vac_charge','$out_sample','')")) {
			mysqli_query($link, "DELETE FROM `TestSample` WHERE `TestId`='$testid'");
			if ($samp != 1) {
				mysqli_query($link, "insert into TestSample values('$testid','$samp')");
			}

			$vacc = explode("@", $vacc);
			foreach ($vacc as $v) {
				if ($v) {
					mysqli_query($link, "insert into test_vaccu values('$testid','$v')");
				}
			}

			echo "Saved";

			if ($master_changes_record == 1) {
				$last_ids = mysqli_fetch_array(mysqli_query($link, "SELECT `testid` FROM `testmaster` ORDER BY `testid` DESC LIMIT 1"));
				$last_id = $last_ids['testid'];

				mysqli_query($link, "INSERT INTO `testmaster_changes`(`testid`, `test_code`, `testname`, `rate`, `instruction`, `notes`, `report_delivery`, `report_delivery_2`, `sample_details`, `category_id`, `type_id`, `type_name`, `equipment`, `sex`, `lineno`, `vac_charge`, `out_sample`, `process`, `date`, `time`, `user`) VALUES ('$last_id','$test_code','$testname','$rate','$instruction','$notes','$turn_around_time','$report_delivery_2','$sample_details','$category_id','$type_id','$type_name','$equipment','$sex','$lineno','$vac_charge','$out_sample','NEW ENTRY','$date','$time','$user')");
			}
		} else {
			echo "Faild, try again later";
		}
	} else {
		$old = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `testmaster` WHERE `testid`='$testid'"));

		if (mysqli_query($link, "UPDATE `testmaster` SET `testname`='$testname',`rate`='$rate',`instruction`='$instruction',`notes`='$notes',`turn_around_time_routine_str`='$turn_around_time_min_str',`turn_around_time_routine`='$turn_around_time_min',`turn_around_time_urgent_str`='$turn_around_time_max_str',`turn_around_time_urgent`='$turn_around_time_max',`report_delivery`='$report_delivery_2',`sample_details`='$sample_details',`category_id`='$category_id',`type_id`='$type_id',`type_name`='$type_name',`equipment`='$equipment',`sex`='$sex',`lineno`='$lineno',`vac_charge`='$vac_charge',`out_sample`='$out_sample' WHERE `testid`='$testid'")) {
			mysqli_query($link, "delete from TestSample where TestId='$testid'");

			if ($samp != 1) {
				mysqli_query($link, "insert into TestSample values('$testid','$samp')");

				//mysqli_query($link, "update patient_test_details set sample_id='$samp' where testid='$testid'");

			} else {
				//mysqli_query($link, "update patient_test_details set sample_id='0' where testid='$testid'");
			}

			mysqli_query($link, "delete from test_vaccu where testid='$testid'");

			$vacc = explode("@", $vacc);
			foreach ($vacc as $v) {
				if ($v) {
					mysqli_query($link, "insert into test_vaccu values('$testid','$v')");
				}
			}

			echo "Updated";

			if ($master_changes_record == 1) {
				mysqli_query($link, "INSERT INTO `testmaster_changes`(`testid`, `test_code`, `testname`, `rate`, `instruction`, `notes`, `report_delivery`, `report_delivery_2`, `sample_details`, `category_id`, `type_id`, `type_name`, `equipment`, `sex`, `lineno`, `vac_charge`, `out_sample`, `process`, `date`, `time`, `user`) VALUES ('$old[testid]','$old[test_code]','$old[testname]','$old[rate]','$old[instruction]','$old[notes]','$old[report_delivery]','$old[report_delivery_2]','$old[sample_details]','$old[category_id]','$old[type_id]','$old[type_name]','$old[equipment]','$old[sex]','$old[lineno]','$old[vac_charge]','$old[out_sample]','UPDATE','$date','$time','$user')");
			}
		} else {
			echo "Faild, try again later";
		}
	}
} else if ($_POST[typ] == "del") {
	$id = $_POST[tid];

	$check_entry = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_test_details` WHERE `testid`='$id'"));

	$check_entry_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `service_id`='$id'"));

	$record = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `testmaster` WHERE `testid`='$id'"));

	if ($record && !$check_entry && !$check_entry_det) {
		$test_code = $record['test_code'];
		$testname = $record['testname'];
		$rate = $record['rate'];
		$instruction = $record['instruction'];
		$notes = $record['notes'];
		$report_delivery = $record['report_delivery'];
		$report_delivery_2 = $record['report_delivery_2'];
		$sample_details = $record['sample_details'];
		$category_id = $record['category_id'];
		$type_id = $record['type_id'];
		$type_name = $record['type_name'];
		$equipment = $record['equipment'];
		$sex = $record['sex'];
		$lineno = $record['lineno'];
		$vac_charge = $record['vac_charge'];
		$out_sample = $record['out_sample'];

		if (mysqli_query($link, "DELETE FROM `testmaster` WHERE `testid`='$id'")) {
			mysqli_query($link, "DELETE FROM `Testparameter` WHERE `TestId`='$id'");
			mysqli_query($link, "DELETE FROM `TestSample` WHERE `TestId`='$id'");
			mysqli_query($link, "DELETE FROM `CategoryTest` WHERE `id`='$id'");

			echo "1";

			if ($master_changes_record == 1) {
				mysqli_query($link, "INSERT INTO `testmaster_changes`(`testid`, `test_code`, `testname`, `rate`, `instruction`, `notes`, `report_delivery`, `report_delivery_2`, `sample_details`, `category_id`, `type_id`, `type_name`, `equipment`, `sex`, `lineno`, `vac_charge`, `out_sample`, `process`, `date`, `time`, `user`) VALUES ('$id','$test_code','$testname','$rate','$instruction','$notes','$report_delivery','$report_delivery_2','$sample_details','$category_id','$type_id','$type_name','$equipment','$sex','$lineno','$vac_charge','$out_sample','DELETED','$date','$time','$user')");
			}
		} else {
			echo "404";
		}

	}

	// if (!$check_entry) {
	// 	mysqli_query($link, "delete from testmaster where testid='$id'");
	// 	mysqli_query($link, "delete from Testparameter where TestId='$id'");
	// 	mysqli_query($link, "delete from TestSample where TestId='$id'");
	// 	mysqli_query($link, "delete from CategoryTest where id='$id'");

	// 	echo "1";
	// } else {
	// 	echo "404";
	// }
} else if ($_POST[typ] == "load_dept") {
	$category_id = $_POST['category_id'];
	$val = "";
	$q = mysqli_query($link, "SELECT * FROM `test_department` WHERE `category_id`='$category_id'");
	while ($r = mysqli_fetch_assoc($q)) {
		if ($val) {
			$val .= "#%#" . $r['id'] . "@@" . $r['name'];
		} else {
			$val = $r['id'] . "@@" . $r['name'];
		}
	}
	echo $val;
}
?>