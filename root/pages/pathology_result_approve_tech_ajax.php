<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user = $_SESSION["emp_id"];

$main_tech = $c_user;

$result_date = $date = date("Y-m-d");
$result_time = $time = date("H:i:s");

$type = $_POST['type'];

//~ print_r($_POST);
//~ exit();

if ($type == "each_param_save") {
	//print_r($_POST);
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$for_doc = $_POST["for_doc"];
	$testid = $_POST["testid"];
	$paramid = $_POST["paramid"];
	$iso_no = $_POST["iso_no"];
	$instrument_id = $_POST["instrument_id"];
	$result = mysqli_real_escape_string($link, $_POST["result"]);
	$result_hide = $_POST["dontPrint"];

	if (!$iso_no || $iso_no == "") {
		$iso_no = 0;
	}

	$testresults = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));

	if ($result) {
		include("pathology_normal_range_new.php");

		$range_status = 0;
		$range_id = 0;
		$status = 0;

		$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));
		if ($param_info["ResultType"] == 1 || $param_info["ResultType"] == 6 || $param_info["ResultType"] == 8) // 1=Numeric,6=formula,8=sub_param
		{
			$param_ranges = load_normal($patient_id, $paramid, $result, $instrument_id);
			$param_rangez = explode("#", $param_ranges);
			$range_id = $param_rangez[2];
			if ($param_rangez[1] == "Error") {
				$range_status = 1;
			}
		}

		if (!$range_status) {
			$range_status = 0;
		}
		if (!$range_id) {
			$range_id = 0;
		}

		if ($testresults) {
			//if ($testresults["result"] != $result || $testresults["range_status"] != $range_status || $testresults["range_id"] != $range_id) {
			if ($testresults["result"] != $result) {
				mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$testresults[patient_id]','$testresults[opd_id]','$testresults[ipd_id]','$testresults[batch_no]','$testresults[testid]','$testresults[paramid]','$testresults[iso_no]','$testresults[sequence]','$testresults[result]','$testresults[range_status]','$testresults[range_id]','$testresults[status]','$testresults[tech_note]','$testresults[doc_note]','$testresults[instrument_id]','$testresults[result_hide]','$testresults[time]','$testresults[date]','$testresults[doc]','$testresults[tech]','$testresults[main_tech]','$testresults[for_doc]', '$c_user', '$date', '$time')");
			}

			// Update
			if (mysqli_query($link, "UPDATE `testresults` SET `result`='$result',`range_status`='$range_status',`range_id`='$range_id',`result_hide`='$result_hide' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'")) {
				$return["error"] = 0;
				$return["message"] = "Updated";
			} else {
				$return["error"] = 1;
				$error_message = "Failed, try again later(1)";
			}
		} else {
			$status = 0; // User Entry

			$iso_no_test_sample = $iso_no;
			if ($iso_no == 0) {
				$iso_no_test_sample = "";
			}

			$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no_test_sample'"));
			if ($test_sample_result["result"] != "") {
				$status = 3; // LIIS Entry
			} else {
				if ($param_info["ResultType"] == 6) // Formula
				{
					$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`!='$paramid' AND `iso_no`='$iso_no_test_sample'"));
					if ($test_sample_result) {
						$status = 3; // LIIS Entry
					}
				}
			}

			if ($test_sample_result["result"] != "" && $test_sample_result["result"] != $result) {
				$test_sample_result["sequence"] = 0;
				$test_sample_result["range_status"] = 0;
				$test_sample_result["range_id"] = 0;
				$test_sample_result["status"] = 0;
				$test_sample_result["tech_note"] = "";
				$test_sample_result["doc_note"] = "";
				$test_sample_result["instrument_id"] = 0;
				$test_sample_result["result_hide"] = 0;
				$test_sample_result["doc"] = 0;
				$test_sample_result["tech"] = 0;
				$test_sample_result["main_tech"] = 0;
				$test_sample_result["for_doc"] = 0;

				mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$test_sample_result[patient_id]','$test_sample_result[opd_id]','$test_sample_result[ipd_id]','$test_sample_result[batch_no]','$test_sample_result[testid]','$test_sample_result[paramid]','$test_sample_result[iso_no]','$test_sample_result[sequence]','$test_sample_result[result]','$test_sample_result[range_status]','$test_sample_result[range_id]','$test_sample_result[status]','$test_sample_result[tech_note]','$test_sample_result[doc_note]','$test_sample_result[instrument_id]','$test_sample_result[result_hide]','$test_sample_result[time]','$test_sample_result[date]','$test_sample_result[doc]','$test_sample_result[tech]','$test_sample_result[main_tech]','$test_sample_result[for_doc]', '$c_user', '$date', '$time')");

				$status = 4; // Result change from LIIS
			}

			$sequence = 0;
			if (!$result_hide) {
				$result_hide = 0;
			}
			$test_param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `sequence`,`status` FROM `Testparameter` WHERE `TestId`='$testid' AND `ParamaterId`='$paramid'"));

			$sequence = $test_param_info["sequence"];
			//$result_hide = $test_param_info["status"];


			$date_time_chk = mysqli_fetch_array(mysqli_query($link, "SELECT `update_timestamp` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND  `paramid`='$paramid' AND `result` != ''"));
			if ($date_time_chk) {
				$result_date = date('Y-m-d', strtotime($date_time_chk['update_timestamp']));
				$result_time = date('H:i:s', strtotime($date_time_chk['update_timestamp']));
			}
			// Insert
			if (mysqli_query($link, "INSERT INTO `testresults`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$sequence','$result','$range_status','$range_id','$status','$tech_note','$doc_note','$instrument_id','$result_hide','$result_time','$result_date','0','$c_user','0','0')")) {
				$return["error"] = 0;
				$return["message"] = "Saved";
			} else {
				$return["error"] = 1;
				$error_message = "Failed, try again later(2)";
			}
		}
	} else {
		if ($testresults) {
			mysqli_query($link, "INSERT INTO `testresults_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) SELECT `patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND  `paramid`='$paramid' AND `iso_no`='$iso_no'");

			mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$testresults[patient_id]','$testresults[opd_id]','$testresults[ipd_id]','$testresults[batch_no]','$testresults[testid]','$testresults[paramid]','$testresults[iso_no]','$testresults[sequence]','$testresults[result]','$testresults[range_status]','$testresults[range_id]','$testresults[status]','$testresults[tech_note]','$testresults[doc_note]','$testresults[instrument_id]','$testresults[result_hide]','$testresults[time]','$testresults[date]','$testresults[doc]','$testresults[tech]','$testresults[main_tech]','$testresults[for_doc]', '$c_user', '$date', '$time')");

			mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND  `paramid`='$paramid' AND `iso_no`='$iso_no'");

			$return["error"] = 1;
			$return["message"] = "Deleted";
		} else {
			$return["error"] = 1;
			$error_message = "Nothing to save";
		}
	}

	echo json_encode($return);
}

if ($type == "each_param_sample_result_delete") {
	//print_r($_POST);
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$testid = $_POST["testid"];
	$paramid = $_POST["paramid"];
	$iso_no = $_POST["iso_no"];

	if (!$iso_no || $iso_no == "") {
		$iso_no = "";
	}

	//$test_sample_result=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));

	if (mysqli_query($link, "UPDATE `test_sample_result` SET `result`='' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'")) {
		$return["error"] = 0;
		$return["message"] = "Removed";
	} else {
		$return["error"] = 1;
		$return["message"] = "Failed, try again later.";
	}

	echo json_encode($return);
}

if ($type == "each_test_param_result_save") {
	//print_r($_POST);
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$for_doc = $_POST["for_doc"];
	$save_data = $_POST["save_data"];
	$widal_data = $_POST["widal_data"];
	$test_summary_data = $_POST["test_summary_data"];
	$val = $_POST["val"];

	$save_data = json_decode($save_data, true);

	$tech_note = "";
	$doc_note = "";

	$zz = 0;

	$approve_success = 0;
	$error_message = "";

	$return = array();

	$approve_msg = "Saved";
	$main_tech = $c_user;

	if (sizeof($save_data) > 0) {
		include("pathology_normal_range_new.php");

		foreach ($save_data as $param_data) {
			if ($param_data) {
				$testid = $param_data["testid"];
				$paramid = $param_data["paramid"];
				$iso_no = $param_data["iso_no"];
				$instrument_id = $param_data["instrument_id"];
				$result = mysqli_real_escape_string($link, $param_data["result"]);
				$padd = $param_data["padd"];
				$iso_no_total = $param_data["iso_no_total"];
				$result_hide = $param_data["dontPrint"];

				if (!$iso_no || $iso_no == "") {
					$iso_no = 0;
				}

				if ($iso_no_total > 0) {
					// Zero ISO Delete
					$chk_result = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='0'"));
					if ($chk_result) {
						mysqli_query($link, "INSERT INTO `testresults_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) SELECT `patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='0'");

						mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='0'");
					}
				}
				// Greater ISO Delete
				$chk_result = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`>$iso_no_total"));
				if ($chk_result) {
					mysqli_query($link, "INSERT INTO `testresults_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) SELECT `patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`>$iso_no_total");

					mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`>$iso_no_total");
				}

				$range_status = 0;
				$range_id = 0;
				$status = 0;

				$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));
				if ($param_info["ResultType"] == 1 || $param_info["ResultType"] == 6 || $param_info["ResultType"] == 8) // 1=Numeric,6=formula,8=sub_param
				{
					$param_ranges = load_normal($patient_id, $paramid, $result, $instrument_id);
					$param_rangez = explode("#", $param_ranges);
					$range_id = $param_rangez[2];
					if ($param_rangez[1] == "Error") {
						$range_status = 1;
					}
				}

				if (!$range_status) {
					$range_status = 0;
				}
				if (!$range_id) {
					$range_id = 0;
				}

				$testresults = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));

				if ($testresults) {
					//if ($testresults["result"] != $result || $testresults["range_status"] != $range_status || $testresults["range_id"] != $range_id) {
					if ($testresults["result"] != $result) {
						mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$testresults[patient_id]','$testresults[opd_id]','$testresults[ipd_id]','$testresults[batch_no]','$testresults[testid]','$testresults[paramid]','$testresults[iso_no]','$testresults[sequence]','$testresults[result]','$testresults[range_status]','$testresults[range_id]','$testresults[status]','$testresults[tech_note]','$testresults[doc_note]','$testresults[instrument_id]','$testresults[result_hide]','$testresults[time]','$testresults[date]','$testresults[doc]','$testresults[tech]','$testresults[main_tech]','$testresults[for_doc]', '$c_user', '$date', '$time')");
					}

					// Update
					if (mysqli_query($link, "UPDATE `testresults` SET `result`='$result',`range_status`='$range_status',`range_id`='$range_id',`doc`='0',`tech`='$c_user',`main_tech`='0',`for_doc`='$for_doc',`result_hide`='$result_hide' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'")) {
						$return["error"] = 0;
						$return["message"] = $approve_msg;

						$approve_success++;
					} else {
						$error_message = "Failed, try again later(4)";
					}
				} else {
					$status = 0; // User Entry

					$iso_no_test_sample = $iso_no;
					if ($iso_no == 0) {
						$iso_no_test_sample = "";
					}

					$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no_test_sample'"));
					if ($test_sample_result["result"] != "") {
						$status = 3; // LIIS Entry
					} else {
						if ($param_info["ResultType"] == 6) // Formula
						{
							$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`!='$paramid' AND `iso_no`='$iso_no_test_sample'"));
							if ($test_sample_result) {
								$status = 3; // LIIS Entry
							}
						}
					}

					if ($test_sample_result["result"] != "" && $test_sample_result["result"] != $result) {
						$test_sample_result["sequence"] = 0;
						$test_sample_result["range_status"] = 0;
						$test_sample_result["range_id"] = 0;
						$test_sample_result["status"] = 0;
						$test_sample_result["tech_note"] = "";
						$test_sample_result["doc_note"] = "";
						$test_sample_result["instrument_id"] = 0;
						$test_sample_result["result_hide"] = 0;
						$test_sample_result["doc"] = 0;
						$test_sample_result["tech"] = 0;
						$test_sample_result["main_tech"] = 0;
						$test_sample_result["for_doc"] = 0;

						mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$test_sample_result[patient_id]','$test_sample_result[opd_id]','$test_sample_result[ipd_id]','$test_sample_result[batch_no]','$test_sample_result[testid]','$test_sample_result[paramid]','$test_sample_result[iso_no]','$test_sample_result[sequence]','$test_sample_result[result]','$test_sample_result[range_status]','$test_sample_result[range_id]','$test_sample_result[status]','$test_sample_result[tech_note]','$test_sample_result[doc_note]','$test_sample_result[instrument_id]','$test_sample_result[result_hide]','$test_sample_result[time]','$test_sample_result[date]','$test_sample_result[doc]','$test_sample_result[tech]','$test_sample_result[main_tech]','$test_sample_result[for_doc]', '$c_user', '$date', '$time')");

						$status = 4; // Result change from LIIS
					}

					$sequence = 0;
					if (!$result_hide) {
						$result_hide = 0;
					}
					$test_param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `sequence`,`status` FROM `Testparameter` WHERE `TestId`='$testid' AND `ParamaterId`='$paramid'"));

					$sequence = $test_param_info["sequence"];
					//$result_hide = $test_param_info["status"];

					$date_time_chk = mysqli_fetch_array(mysqli_query($link, "SELECT `update_timestamp` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND  `paramid`='$paramid' AND `result` != ''"));
					if ($date_time_chk) {
						$result_date = date('Y-m-d', strtotime($date_time_chk['update_timestamp']));
						$result_time = date('H:i:s', strtotime($date_time_chk['update_timestamp']));
					}

					// Insert
					if (mysqli_query($link, "INSERT INTO `testresults`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$sequence','$result','$range_status','$range_id','$status','$tech_note','$doc_note','$instrument_id','$result_hide','$result_time','$result_date','0','$c_user','0','$for_doc')")) {
						$return["error"] = 0;
						$return["message"] = $approve_msg;

						$approve_success++;
					} else {
						$error_message = "Failed, try again later(5)";
					}
				}
			}
		}
	}

	if ($approve_success > 0 || $widal_success > 0 || $summary_success > 0) {
		$return["error"] = 0;
		$return["message"] = $approve_msg;
	} else {
		$return["error"] = 1;
		$return["message"] = "Failed, try again later(0)"; // Nothing to approve
	}

	echo json_encode($return);
}

if ($type == "approve_param_save") {
	//print_r($_POST);
	//exit;
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$for_doc = $_POST["for_doc"];
	$approve_data = $_POST["approve_data"];
	$widal_data = $_POST["widal_data"];
	$test_summary_data = $_POST["test_summary_data"];
	$val = $_POST["val"];

	$approve_data = json_decode($approve_data, true);
	$widal_data = json_decode($widal_data, true);
	$test_summary_data = json_decode($test_summary_data, true);

	$tech_note = "";
	$doc_note = "";

	$zz = 0;

	$approve_success = 0;
	$widal_success = 0;
	$summary_success = 0;
	
	$error_message = "";

	$return = array();

	if ($val == 0) {
		$approve_msg = "In-validated";
		$main_tech = 0;

		foreach ($approve_data as $param_data) {
			if ($param_data) {
				$testid = $param_data["testid"];
				$paramid = $param_data["paramid"];
				$iso_no = $param_data["iso_no"];
				$instrument_id = $param_data["instrument_id"];
				$result = mysqli_real_escape_string($link, $param_data["result"]);
				$padd = $param_data["padd"];
				$iso_no_total = $param_data["iso_no_total"];
				$result_hide = $param_data["dontPrint"];

				if (!$iso_no || $iso_no == "") {
					$iso_no = 0;
				}

				if (mysqli_query($link, "UPDATE `testresults` SET `doc`='0',`main_tech`='$main_tech',`for_doc`='0',`result_hide`='$result_hide' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'")) {
					// Record
					mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$c_user','$date','$time','$val')");

					$approve_details = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
					if ($approve_details) {
						mysqli_query($link, "UPDATE `approve_details` SET `t_time`='00:00:00',`t_date`='0000-00-00' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
					}

					$approve_success++;
				} else {
					$return["error"] = 1;
					$error_message = "Failed, try again later(1)";
				}
			}
		}

		if (sizeof($widal_data) > 0) // If Widal Test Available
		{
			foreach ($widal_data as $widal_result) {
				if ($widal_result) {
					$testid = $widal_result["testid"];

					if (mysqli_query($link, "UPDATE `widalresult` SET `main_tech`='$main_tech',`doc`='0',`for_doc`='0' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'")) {
						if ($approve_success == 0) {
							$widal_success++;

							// Record
							mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','0','0','$c_user','$date','$time','$val')");

							$approve_details = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
							if ($approve_details) {
								mysqli_query($link, "UPDATE `approve_details` SET `t_time`='00:00:00',`t_date`='0000-00-00' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
							}
						}
					} else {
						if ($approve_success == 0) {
							$return["error"] = 1;
							$error_message = "Failed, try again later(2)";
						}
					}
				}
			}
		}

		if (sizeof($test_summary_data) > 0) // If Test Summary
		{
			foreach ($test_summary_data as $test_summary) {
				if ($test_summary) {
					$testid = $test_summary["testid"];

					if (mysqli_query($link, "UPDATE `patient_test_summary` SET `main_tech`='$main_tech',`doc`='0',`for_doc`='0' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'")) {
						if ($approve_success == 0 && $widal_success == 0) {
							$summary_success++;

							// Record
							mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','0','0','$c_user','$date','$time','$val')");
						}
					} else {
						if ($approve_success == 0 && $widal_success == 0) {
							$return["error"] = 1;
							$error_message = "Failed, try again later(3)";
						}
					}
				}
			}
		}
	} else {
		$approve_msg = "Validated";
		$main_tech = $c_user;
		
		$doc=0;
		if($_SESSION['levelid']==13)
		{
			$doc = $c_user;
		}

		if (sizeof($approve_data) > 0) {
			include("pathology_normal_range_new.php");

			foreach ($approve_data as $param_data) {
				if ($param_data) {
					$testid = $param_data["testid"];
					$paramid = $param_data["paramid"];
					$iso_no = $param_data["iso_no"];
					$instrument_id = $param_data["instrument_id"];
					$result = mysqli_real_escape_string($link, $param_data["result"]);
					$padd = $param_data["padd"];
					$iso_no_total = $param_data["iso_no_total"];
					$result_hide = $param_data["dontPrint"];

					if (!$iso_no || $iso_no == "") {
						$iso_no = 0;
					}

					if ($iso_no_total > 0) {
						// Zero ISO Delete
						$chk_result = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='0'"));
						if ($chk_result) {
							mysqli_query($link, "INSERT INTO `testresults_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) SELECT `patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='0'");

							mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='0'");
						}
					}
					// Greater ISO Delete
					$chk_result = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`>$iso_no_total"));
					if ($chk_result) {
						mysqli_query($link, "INSERT INTO `testresults_delete`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) SELECT `patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`>$iso_no_total");

						mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`>$iso_no_total");
					}

					$range_status = 0;
					$range_id = 0;
					$status = 0;

					$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));
					if ($param_info["ResultType"] == 1 || $param_info["ResultType"] == 6 || $param_info["ResultType"] == 8) // 1=Numeric,6=formula,8=sub_param
					{
						$param_ranges = load_normal($patient_id, $paramid, $result, $instrument_id);
						$param_rangez = explode("#", $param_ranges);
						$range_id = $param_rangez[2];
						if ($param_rangez[1] == "Error") {
							$range_status = 1;
						}
					}

					if (!$range_status) {
						$range_status = 0;
					}
					if (!$range_id) {
						$range_id = 0;
					}

					$testresults = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));

					if ($testresults) {
						if ($testresults["result"] != $result) {
							mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$testresults[patient_id]','$testresults[opd_id]','$testresults[ipd_id]','$testresults[batch_no]','$testresults[testid]','$testresults[paramid]','$testresults[iso_no]','$testresults[sequence]','$testresults[result]','$testresults[range_status]','$testresults[range_id]','$testresults[status]','$testresults[tech_note]','$testresults[doc_note]','$testresults[instrument_id]','$testresults[result_hide]','$testresults[time]','$testresults[date]','$testresults[doc]','$testresults[tech]','$testresults[main_tech]','$testresults[for_doc]', '$c_user', '$date', '$time')");
						}

						if ($result) {
							// Update
							if (mysqli_query($link, "UPDATE `testresults` SET `result`='$result',`range_status`='$range_status',`range_id`='$range_id',`doc`='$doc',`main_tech`='$main_tech',`for_doc`='$for_doc',`result_hide`='$result_hide' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'")) {
								// Record
								mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$c_user','$date','$time','$val')");
								
								$approve_details = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
								if ($approve_details) {
									mysqli_query($link, "UPDATE `approve_details` SET `t_time`='$time',`t_date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
								} else {
									mysqli_query($link, "INSERT INTO `approve_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `t_time`, `t_date`, `d_time`, `d_date`, `testid`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$time','$date','00:00:00','0000-00-00','$testid')");
								}
								
								if($_SESSION['levelid']==13)
								{
									mysqli_query($link, "INSERT INTO `doctor_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$c_user','$date','$time','$val')");
									
									$approve_details=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
									if($approve_details)
									{
										mysqli_query($link, "UPDATE `approve_details` SET `d_time`='$time',`d_date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
									}else
									{
										mysqli_query($link, "INSERT INTO `approve_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `t_time`, `t_date`, `d_time`, `d_date`, `testid`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','00:00:00','0000-00-00','$time','$date','$testid')");
									}
								}

								$return["error"] = 0;
								$return["message"] = $approve_msg;

								$approve_success++;
							} else {
								$error_message = "Failed, try again later(4)";
							}
						} else {
							mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'");
						}
					} else {


						$status = 0; // User Entry

						$iso_no_test_sample = $iso_no;
						if ($iso_no == 0) {
							$iso_no_test_sample = "";
						}

						$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no_test_sample'"));
						if ($test_sample_result["result"] != "") {
							$status = 3; // LIIS Entry
						} else {
							if ($param_info["ResultType"] == 6) // Formula
							{
								$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`!='$paramid' AND `iso_no`='$iso_no_test_sample'"));
								if ($test_sample_result) {
									$status = 3; // LIIS Entry
								}
							}
						}

						if ($test_sample_result["result"] != "" && $test_sample_result["result"] != $result) {
							$test_sample_result["sequence"] = 0;
							$test_sample_result["range_status"] = 0;
							$test_sample_result["range_id"] = 0;
							$test_sample_result["status"] = 0;
							$test_sample_result["tech_note"] = "";
							$test_sample_result["doc_note"] = "";
							$test_sample_result["instrument_id"] = 0;
							$test_sample_result["result_hide"] = 0;
							$test_sample_result["doc"] = 0;
							$test_sample_result["tech"] = 0;
							$test_sample_result["main_tech"] = 0;
							$test_sample_result["for_doc"] = 0;

							mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$test_sample_result[patient_id]','$test_sample_result[opd_id]','$test_sample_result[ipd_id]','$test_sample_result[batch_no]','$test_sample_result[testid]','$test_sample_result[paramid]','$test_sample_result[iso_no]','$test_sample_result[sequence]','$test_sample_result[result]','$test_sample_result[range_status]','$test_sample_result[range_id]','$test_sample_result[status]','$test_sample_result[tech_note]','$test_sample_result[doc_note]','$test_sample_result[instrument_id]','$test_sample_result[result_hide]','$test_sample_result[time]','$test_sample_result[date]','$test_sample_result[doc]','$test_sample_result[tech]','$test_sample_result[main_tech]','$test_sample_result[for_doc]', '$c_user', '$date', '$time')");

							$status = 4; // Result change from LIIS
						}

						$sequence = 0;
						if (!$result_hide) {
							$result_hide = 0;
						}
						$test_param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `sequence`,`status` FROM `Testparameter` WHERE `TestId`='$testid' AND `ParamaterId`='$paramid'"));

						$sequence = $test_param_info["sequence"];
						//$result_hide = $test_param_info["status"];

						$date_time_chk = mysqli_fetch_array(mysqli_query($link, "SELECT `update_timestamp` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND  `paramid`='$paramid' AND `result` != ''"));
						if ($date_time_chk) {
							$result_date = date('Y-m-d', strtotime($date_time_chk['update_timestamp']));
							$result_time = date('H:i:s', strtotime($date_time_chk['update_timestamp']));
						}

						if ($result) {
							// Insert
							if (mysqli_query($link, "INSERT INTO `testresults`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$sequence','$result','$range_status','$range_id','$status','$tech_note','$doc_note','$instrument_id','$result_hide','$result_time','$result_date','$doc','$c_user','$c_user','0')")) {
								// Record
								mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$c_user','$date','$time','$val')");

								$approve_details = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
								if ($approve_details) {
									mysqli_query($link, "UPDATE `approve_details` SET `t_time`='$time',`t_date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
								} else {
									mysqli_query($link, "INSERT INTO `approve_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `t_time`, `t_date`, `d_time`, `d_date`, `testid`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$time','$date','00:00:00','0000-00-00','$testid')");
								}

								$return["error"] = 0;
								$return["message"] = $approve_msg;

								$approve_success++;
							} else {
								$error_message = "Failed, try again later(5)";
							}
						}
					}
				}
			}
		}
		if (sizeof($widal_data) > 0) // If Widal Test Available
		{
			foreach ($widal_data as $widal_result) {
				if ($widal_result) {
					$testid = $widal_result["testid"];

					$oval = mysqli_real_escape_string($link, $widal_result["oval"]);
					$hval = mysqli_real_escape_string($link, $widal_result["hval"]);
					$ahval = mysqli_real_escape_string($link, $widal_result["ahval"]);
					$bhval = mysqli_real_escape_string($link, $widal_result["bhval"]);

					$DETAILS = mysqli_real_escape_string($link, $widal_result["impression"]);

					$specimen = mysqli_real_escape_string($link, $widal_result["specimen"]);
					$incubation_temp = mysqli_real_escape_string($link, $widal_result["incubation_temp"]);
					$method = mysqli_real_escape_string($link, $widal_result["method"]);

					$ov = explode("@@", $oval);
					if (sizeof($ov) > 0) {
						$slno = 1;

						$F1 = $ov[1];
						$F2 = $ov[2];
						$F3 = $ov[3];
						$F4 = $ov[4];
						$F5 = $ov[5];
						$F6 = $ov[6];

						$w1 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno='$slno'"));
						if ($w1) {
							if ($w1["F1"] != $F1 || $w1["F2"] != $F2 || $w1["F3"] != $F3 || $w1["F4"] != $F4 || $w1["F5"] != $F5 || $w1["F6"] != $F6 || $w1["DETAILS"] != $DETAILS || $w1["specimen"] != $specimen || $w1["incubation_temp"] != $incubation_temp || $w1["method"] != $method) {
								mysqli_query($link, "INSERT INTO `widalresult_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$w1[patient_id]','$w1[opd_id]','$w1[ipd_id]','$w1[batch_no]','$w1[testid]','$w1[slno]','$w1[F1]','$w1[F2]','$w1[F3]','$w1[F4]','$w1[F5]','$w1[F6]','$w1[DETAILS]','$w1[specimen]','$w1[incubation_temp]','$w1[method]','$w1[v_User]','$w1[main_tech]','$w1[doc]','$w1[for_doc]','$w1[time]','$w1[date]','$w1[counter]','$c_user','$date','$time')");
							}

							if (mysqli_query($link, "UPDATE `widalresult` SET `F1`='$F1',`F2`='$F2',`F3`='$F3',`F4`='$F4',`F5`='$F5',`F6`='$F6',`DETAILS`='$DETAILS',`specimen`='$specimen',`incubation_temp`='$incubation_temp',`method`='$method',`main_tech`='$c_user',`doc`='0',`for_doc`='$for_doc' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno='$slno'")) {
								if ($approve_success == 0) {
									$return["error"] = 0;
									$return["message"] = $approve_msg;

									$widal_success++;

									// Record
									mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");

									$approve_details = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
									if ($approve_details) {
										mysqli_query($link, "UPDATE `approve_details` SET `t_time`='$time',`t_date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
									} else {
										mysqli_query($link, "INSERT INTO `approve_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `t_time`, `t_date`, `d_time`, `d_date`, `testid`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$time','$date','00:00:00','0000-00-00','$testid')");
									}
								}
							} else {
								if ($approve_success == 0) {
									$error_message = "Failed, try again later(W10)";
								}
							}
						} else {
							if ($F1 != "" || $F2 != "" || $F3 != "" || $F4 != "" || $F5 != "" || $F6 != "" || $DETAILS != "" || $specimen != "" || $incubation_temp != "" || $method != "") {
								if (mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','$F1','$F2','$F3','$F4','$F5','$F6','$DETAILS','$specimen','$incubation_temp','$method','$c_user','$c_user','0','$for_doc','$time','$date','0')")) {
									if ($approve_success == 0) {
										$return["error"] = 0;
										$return["message"] = $approve_msg;

										$widal_success++;

										// Record
										mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");

										// Record
										mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");

										$approve_details = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
										if ($approve_details) {
											mysqli_query($link, "UPDATE `approve_details` SET `t_time`='$time',`t_date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
										} else {
											mysqli_query($link, "INSERT INTO `approve_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `t_time`, `t_date`, `d_time`, `d_date`, `testid`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$time','$date','00:00:00','0000-00-00','$testid')");
										}
									}
								} else {
									if ($approve_success == 0) {
										$error_message = "Failed, try again later(W11)";
									}
								}
							}
						}
					}

					$hv = explode("@@", $hval);
					if (sizeof($hv) > 0) {
						$slno = 2;

						$F1 = $hv[1];
						$F2 = $hv[2];
						$F3 = $hv[3];
						$F4 = $hv[4];
						$F5 = $hv[5];
						$F6 = $hv[6];

						$w2 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno='$slno'"));
						if ($w2) {
							if ($w2["F1"] != $F1 || $w2["F2"] != $F2 || $w2["F3"] != $F3 || $w2["F4"] != $F4 || $w2["F5"] != $F5 || $w2["F6"] != $F6) {
								mysqli_query($link, "INSERT INTO `widalresult_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$w2[patient_id]','$w2[opd_id]','$w2[ipd_id]','$w2[batch_no]','$w2[testid]','$w2[slno]','$w2[F1]','$w2[F2]','$w2[F3]','$w2[F4]','$w2[F5]','$w2[F6]','$w2[DETAILS]','$w2[specimen]','$w2[incubation_temp]','$w2[method]','$w2[v_User]','$w2[main_tech]','$w2[doc]','$w2[for_doc]','$w2[time]','$w2[date]','$w2[counter]','$c_user','$date','$time')");
							}

							if (mysqli_query($link, "UPDATE `widalresult` SET `F1`='$F1',`F2`='$F2',`F3`='$F3',`F4`='$F4',`F5`='$F5',`F6`='$F6',`DETAILS`='$DETAILS',`main_tech`='$c_user',`doc`='0',`for_doc`='$for_doc' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno='$slno'")) {
								if ($approve_success == 0) {
									$return["error"] = 0;
									$return["message"] = $approve_msg;

									$widal_success++;

									// Record
									mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");
								}
							} else {
								if ($approve_success == 0) {
									$error_message = "Failed, try again later(W20)";
								}
							}
						} else {
							if ($F1 != "" || $F2 != "" || $F3 != "" || $F4 != "" || $F5 != "" || $F6 != "") {
								if (mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','$F1','$F2','$F3','$F4','$F5','$F6','$DETAILS',NULL,NULL,NUll,'$c_user','$c_user','0','$for_doc','$time','$date','0')")) {
									if ($approve_success == 0) {
										$return["error"] = 0;
										$return["message"] = $approve_msg;

										$widal_success++;

										// Record
										mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");
									}
								} else {
									if ($approve_success == 0) {
										$error_message = "Failed, try again later(W21)";
									}
								}
							}
						}
					}

					$ah = explode("@@", $ahval);
					if (sizeof($ah) > 0) {
						$slno = 3;

						$F1 = $ah[1];
						$F2 = $ah[2];
						$F3 = $ah[3];
						$F4 = $ah[4];
						$F5 = $ah[5];
						$F6 = $ah[6];

						$w3 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno='$slno'"));
						if ($w3) {
							if ($w3["F1"] != $F1 || $w3["F2"] != $F2 || $w3["F3"] != $F3 || $w3["F4"] != $F4 || $w3["F5"] != $F5 || $w3["F6"] != $F6) {
								mysqli_query($link, "INSERT INTO `widalresult_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$w3[patient_id]','$w3[opd_id]','$w3[ipd_id]','$w3[batch_no]','$w3[testid]','$w3[slno]','$w3[F1]','$w3[F2]','$w3[F3]','$w3[F4]','$w3[F5]','$w3[F6]','$w3[DETAILS]','$w3[specimen]','$w3[incubation_temp]','$w3[method]','$w3[v_User]','$w3[main_tech]','$w3[doc]','$w3[for_doc]','$w3[time]','$w3[date]','$w3[counter]','$c_user','$date','$time')");
							}

							if (mysqli_query($link, "UPDATE `widalresult` SET `F1`='$F1',`F2`='$F2',`F3`='$F3',`F4`='$F4',`F5`='$F5',`F6`='$F6',`DETAILS`='$DETAILS',`main_tech`='$c_user',`doc`='0',`for_doc`='$for_doc' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno='$slno'")) {
								if ($approve_success == 0) {
									$return["error"] = 0;
									$return["message"] = $approve_msg;

									$widal_success++;

									// Record
									mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");
								}
							} else {
								if ($approve_success == 0) {
									$error_message = "Failed, try again later(W30)";
								}
							}
						} else {
							if ($F1 != "" || $F2 != "" || $F3 != "" || $F4 != "" || $F5 != "" || $F6 != "") {
								if (mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','$F1','$F2','$F3','$F4','$F5','$F6','$DETAILS',NULL,NULL,NUll,'$c_user','$c_user','0','$for_doc','$time','$date','0')")) {
									if ($approve_success == 0) {
										$return["error"] = 0;
										$return["message"] = $approve_msg;

										$widal_success++;

										// Record
										mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");
									}
								} else {
									if ($approve_success == 0) {
										$error_message = "Failed, try again later(W31)";
									}
								}
							}
						}
					}

					$bh = explode("@@", $bhval);
					if (sizeof($bh) > 0) {
						$slno = 4;

						$F1 = $bh[1];
						$F2 = $bh[2];
						$F3 = $bh[3];
						$F4 = $bh[4];
						$F5 = $bh[5];
						$F6 = $bh[6];

						$w4 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno='$slno'"));
						if ($w4) {
							if ($w4["F1"] != $F1 || $w4["F2"] != $F2 || $w4["F3"] != $F3 || $w4["F4"] != $F4 || $w4["F5"] != $F5 || $w4["F6"] != $F6) {
								mysqli_query($link, "INSERT INTO `widalresult_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$w4[patient_id]','$w4[opd_id]','$w4[ipd_id]','$w4[batch_no]','$w4[testid]','$w4[slno]','$w4[F1]','$w4[F2]','$w4[F3]','$w4[F4]','$w4[F5]','$w4[F6]','$w4[DETAILS]','$w4[specimen]','$w4[incubation_temp]','$w4[method]','$w4[v_User]','$w4[main_tech]','$w4[doc]','$w4[for_doc]','$w4[time]','$w4[date]','$w4[counter]','$c_user','$date','$time')");
							}

							if (mysqli_query($link, "UPDATE `widalresult` SET `F1`='$F1',`F2`='$F2',`F3`='$F3',`F4`='$F4',`F5`='$F5',`F6`='$F6',`DETAILS`='$DETAILS',`main_tech`='$c_user',`doc`='0',`for_doc`='$for_doc' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno='$slno'")) {
								if ($approve_success == 0) {
									$return["error"] = 0;
									$return["message"] = $approve_msg;

									$widal_success++;

									// Record
									mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");
								}
							} else {
								if ($approve_success == 0) {
									$error_message = "Failed, try again later(W40)";
								}
							}
						} else {
							if ($F1 != "" || $F2 != "" || $F3 != "" || $F4 != "" || $F5 != "" || $F6 != "") {
								if (mysqli_query($link, "INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `specimen`, `incubation_temp`, `method`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`, `counter`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','$F1','$F2','$F3','$F4','$F5','$F6','$DETAILS',NULL,NULL,NUll,'$c_user','$c_user','0','$for_doc','$time','$date','0')")) {
									if ($approve_success == 0) {
										$return["error"] = 0;
										$return["message"] = $approve_msg;

										$widal_success++;

										// Record
										mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$slno','0','$c_user','$date','$time','$val')");
									}
								} else {
									if ($approve_success == 0) {
										$error_message = "Failed, try again later(W41)";
									}
								}
							}
						}
					}
				}
			}
		}
		if (sizeof($test_summary_data) > 0) // If Test Summary
		{
			foreach ($test_summary_data as $test_summary) {
				if ($test_summary) {
					$testid = $test_summary["testid"];

					$patient_test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_test_summary` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));

					if ($patient_test_summary) {
						if (mysqli_query($link, "UPDATE `patient_test_summary` SET `main_tech`='$main_tech',`doc`='0',`for_doc`='0' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'")) {
							if ($approve_success == 0 && $widal_success == 0) {
								$summary_success++;

								// Record
								mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','0','0','$c_user','$date','$time','$val')");

								$approve_details = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
								if ($approve_details) {
									mysqli_query($link, "UPDATE `approve_details` SET `t_time`='$time',`t_date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
								} else {
									mysqli_query($link, "INSERT INTO `approve_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `t_time`, `t_date`, `d_time`, `d_date`, `testid`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$time','$date','00:00:00','0000-00-00','$testid')");
								}
							}
						} else {
							if ($approve_success == 0 && $widal_success == 0) {
								$return["error"] = 1;
								$error_message = "Failed, try again later(6)";
							}
						}
					} else {
						$summary_data = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `test_summary` WHERE `testid`='$testid'"));

						$summary = $summary_data["summary"];

						if (mysqli_query($link, "INSERT INTO `patient_test_summary`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `summary`, `time`, `date`, `user`, `doc`, `main_tech`, `for_doc`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$summary','$time','$date','$c_user','0','$main_tech','$for_doc')")) {
							if ($approve_success == 0 && $widal_success == 0) {
								$summary_success++;

								// Record
								mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','0','0','$c_user','$date','$time','$val')");

								$approve_details = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
								if ($approve_details) {
									mysqli_query($link, "UPDATE `approve_details` SET `t_time`='$time',`t_date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
								} else {
									mysqli_query($link, "INSERT INTO `approve_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `t_time`, `t_date`, `d_time`, `d_date`, `testid`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$time','$date','00:00:00','0000-00-00','$testid')");
								}
							}
						} else {
							if ($approve_success == 0 && $widal_success == 0) {
								$return["error"] = 1;
								$error_message = "Failed, try again later(6)";
							}
						}
					}
				}
			}
		}
	}

	if ($approve_success > 0 || $widal_success > 0 || $summary_success > 0) {
		$return["error"] = 0;
		$return["message"] = $approve_msg;
	} else {
		$return["error"] = 1;
		$return["message"] = "Failed, try again later(0)"; // Nothing to approve
	}

	echo json_encode($return);
}

if ($type == "save_test_summary") {
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$testid = $_POST["testid"];
	$for_doc = $_POST["for_doc"];
	$summary = mysqli_real_escape_string($link, $_POST["summary"]);

	$return = array();

	$patient_test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_test_summary` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));

	if ($summary == "" || $summary == "<br>" || $summary == "<br><br>" || $summary == "<p></p>" || $summary == "<p><br></p>" || $summary == "<p><br></p>" || $summary == '<p><br type=\"_moz\"></p>') {
		if (mysqli_query($link, "DELETE FROM `patient_test_summary` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'")) {
			mysqli_query($link, "INSERT INTO `patient_test_summary_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `summary`, `time`, `date`, `user`, `doc`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$patient_test_summary[patient_id]','$patient_test_summary[opd_id]','$patient_test_summary[ipd_id]','$patient_test_summary[batch_no]','$patient_test_summary[testid]','$patient_test_summary[summary]','$patient_test_summary[time]','$patient_test_summary[date]','$patient_test_summary[user]','$patient_test_summary[doc]','$patient_test_summary[main_tech]','$patient_test_summary[for_doc]','$c_user','$date','$time')");

			$return["error"] = 0;
			$return["message"] = "Removed";
		} else {
			$return["error"] = 1;
			$return["message"] = "Failed, try again later(3)";
		}
	} else {
		if ($patient_test_summary) {
			if ($patient_test_summary["summary"] != $summary) {
				mysqli_query($link, "INSERT INTO `patient_test_summary_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `summary`, `time`, `date`, `user`, `doc`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$patient_test_summary[patient_id]','$patient_test_summary[opd_id]','$patient_test_summary[ipd_id]','$patient_test_summary[batch_no]','$patient_test_summary[testid]','$patient_test_summary[summary]','$patient_test_summary[time]','$patient_test_summary[date]','$patient_test_summary[user]','$patient_test_summary[doc]','$patient_test_summary[main_tech]','$patient_test_summary[for_doc]','$c_user','$date','$time')");
			}

			if (mysqli_query($link, "UPDATE `patient_test_summary` SET `summary`='$summary',`user`='$c_user',`doc`='0',`main_tech`='$c_user',`for_doc`='$for_doc' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'")) {
				$return["error"] = 0;
				$return["message"] = "Saved";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(2)";
			}
		} else {
			if (mysqli_query($link, "INSERT INTO `patient_test_summary`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `summary`, `time`, `date`, `user`, `doc`, `main_tech`, `for_doc`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$summary','$time','$date','$c_user','0','$c_user','$for_doc')")) {
				$return["error"] = 0;
				$return["message"] = "Saved";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(1)";
			}
		}
	}

	echo json_encode($return);
}

if ($type == "save_param_summary") {
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$testid = $_POST["testid"];
	$paramid = $_POST["paramid"];
	$iso_no = $_POST["iso_no"];
	$instrument_id = $_POST["instrument_id"];
	$for_doc = $_POST["for_doc"];
	$result = mysqli_real_escape_string($link, $_POST["summary"]);

	if (!$iso_no || $iso_no == "") {
		$iso_no = 0;
	}

	$val = 1; // Approved

	$tech_note = "";
	$doc_note = "";

	$return = array();

	if ($result == "" || $result == "<br>" || $result == "<br><br>" || $result == "<p></p>" || $result == "<p><br></p>" || $result == "<p><br></p>" || $result == '<p><br type=\"_moz\"></p>') {
		if (mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'")) {
			$return["error"] = 0;
			$return["message"] = "Removed";
		} else {
			$return["error"] = 1;
			$return["message"] = "Failed, try again later(3)";
		}
	} else {
		$testresults = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));

		if ($testresults) {
			if ($testresults["result"] != $result) {
				mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$testresults[patient_id]','$testresults[opd_id]','$testresults[ipd_id]','$testresults[batch_no]','$testresults[testid]','$testresults[paramid]','$testresults[iso_no]','$testresults[sequence]','$testresults[result]','$testresults[range_status]','$testresults[range_id]','$testresults[status]','$testresults[tech_note]','$testresults[doc_note]','$testresults[instrument_id]','$testresults[result_hide]','$testresults[time]','$testresults[date]','$testresults[doc]','$testresults[tech]','$testresults[main_tech]','$testresults[for_doc]', '$c_user', '$date', '$time')");
			}

			// Update
			if (mysqli_query($link, "UPDATE `testresults` SET `result`='$result',`doc`='0',`main_tech`='$c_user',`for_doc`='$for_doc' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'")) {
				// Record
				mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$c_user','$date','$time','$val')");

				$return["error"] = 0;
				$return["message"] = "Saved & Approved";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(2)";
			}
		} else {
			$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));

			if ($test_sample_result["result"] != "" && $test_sample_result["result"] != $result) {
				$test_sample_result["sequence"] = 0;
				$test_sample_result["range_status"] = 0;
				$test_sample_result["range_id"] = 0;
				$test_sample_result["status"] = 0;
				$test_sample_result["tech_note"] = "";
				$test_sample_result["doc_note"] = "";
				$test_sample_result["instrument_id"] = 0;
				$test_sample_result["result_hide"] = 0;
				$test_sample_result["doc"] = 0;
				$test_sample_result["tech"] = 0;
				$test_sample_result["main_tech"] = 0;
				$test_sample_result["for_doc"] = 0;

				mysqli_query($link, "INSERT INTO `testresults_update`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`, `edit_user`, `edit_date`, `edit_time`) VALUES ('$test_sample_result[patient_id]','$test_sample_result[opd_id]','$test_sample_result[ipd_id]','$test_sample_result[batch_no]','$test_sample_result[testid]','$test_sample_result[paramid]','$test_sample_result[iso_no]','$test_sample_result[sequence]','$test_sample_result[result]','$test_sample_result[range_status]','$test_sample_result[range_id]','$test_sample_result[status]','$test_sample_result[tech_note]','$test_sample_result[doc_note]','$test_sample_result[instrument_id]','$test_sample_result[result_hide]','$test_sample_result[time]','$test_sample_result[date]','$test_sample_result[doc]','$test_sample_result[tech]','$test_sample_result[main_tech]','$test_sample_result[for_doc]', '$c_user', '$date', '$time')");

				$status = 3; // Result change from LIIS
			}

			$range_status = 0;
			$range_id = 0;
			$status = 0;

			$sequence = 0;
			if (!$result_hide) {
				$result_hide = 0;
			}
			$test_param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `sequence`,`status` FROM `Testparameter` WHERE `TestId`='$testid' AND `ParamaterId`='$paramid'"));

			$sequence = $test_param_info["sequence"];
			$result_hide = $test_param_info["status"];

			$date_time_chk = mysqli_fetch_array(mysqli_query($link, "SELECT `update_timestamp` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND  `paramid`='$paramid' AND `result` != ''"));
			if ($date_time_chk) {
				$result_date = date('Y-m-d', strtotime($date_time_chk['update_timestamp']));
				$result_time = date('H:i:s', strtotime($date_time_chk['update_timestamp']));
			}
			// Insert
			if (mysqli_query($link, "INSERT INTO `testresults`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `sequence`, `result`, `range_status`, `range_id`, `status`, `tech_note`, `doc_note`, `instrument_id`, `result_hide`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$sequence','$result','$range_status','$range_id','$status','$tech_note','$doc_note','$instrument_id','$result_hide','$result_time','$result_date','0','$c_user','$c_user','0')")) {
				// Record
				mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$c_user','$date','$time','$val')");
				// Record
				mysqli_query($link, "INSERT INTO `tech_approval_record`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `iso_no`, `user`, `date`, `time`, `type`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','$iso_no','$c_user','$date','$time','$val')");

				$return["error"] = 0;
				$return["message"] = "Saved & Approved";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(1)";
			}
		}
	}

	echo json_encode($return);
}

if ($type == "load_test_note") {
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$testid = $_POST["testid"];

	$test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));

	$test_note_btn = "Save Note";
	$testresults_note = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `main_tech`>0"));
	if ($testresults_note["note"]) {
		$test_note_btn = "Update Note";
	}
	?>
	<div>
		<table class="table table-condensed table-bordered">
			<tr>
				<th>NOTE OF : <?php echo strtoupper($test_info["testname"]); ?></th>
			</tr>
			<tr>
				<td>
					<textarea id="test_note_val"
						style="height: 100px;width: 99%;resize: none;"><?php echo $testresults_note["note"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td style="text-align:center;">
					<button class="btn btn-save" onclick="save_test_note('<?php echo $testid; ?>')"><i
							class="icon-save"></i> <?php echo $test_note_btn; ?></button>
					<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

if ($type == "save_test_note") {
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$testid = $_POST["testid"];
	$test_note = mysqli_real_escape_string($link, $_POST["test_note"]);

	$testresults_note = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `main_tech`>0"));

	if ($test_note) {
		if ($testresults_note) {
			if (mysqli_query($link, "UPDATE `testresults_note` SET `note`='$test_note', `main_tech`='$c_user', `time`='$time', `date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `main_tech`>0")) {
				$return["error"] = 0;
				$return["message"] = "Updated";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(1)";
			}
		} else {
			if (mysqli_query($link, "INSERT INTO `testresults_note`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `note`, `main_tech`, `doc`, `time`, `date`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$test_note','$c_user','0','$time','$date')")) {
				$return["error"] = 0;
				$return["message"] = "Saved";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(2)";
			}
		}
	} else {
		if ($testresults_note) {
			if (mysqli_query($link, "DELETE FROM `testresults_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `main_tech`>0")) {
				$return["error"] = 1;
				$return["message"] = "Deleted";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(3)";
			}
		} else {
			$return["error"] = 2;
			$return["message"] = "Nothing to save";
		}
	}
	echo json_encode($return);
}

// Flag Start
if ($type == "flag_patient") {
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];

	$flag = 0;

	$save_btn_name = "Save";

	$patient_flagged_details = mysqli_fetch_array(mysqli_query($link, "SELECT `cause`,`remarks` FROM `patient_flagged_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id'"));
	if ($patient_flagged_details) {
		$save_btn_name = "Update";

		$flag = 1;
	}
	?>
	<div>
		<table class="table table-condensed table-bordered">
			<tr>
				<td>
					<b>Cause <?php if (!$patient_flagged_details) { ?><b style="color:red;">*</b><?php } ?>:</b>
					<textarea id="flag_cause"
						style="height: 100px;width: 99%;resize: none;"><?php echo $patient_flagged_details["cause"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<b>Remarks :</b>
					<textarea id="flag_remarks"
						style="height: 100px;width: 99%;resize: none;"><?php echo $patient_flagged_details["remarks"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<td style="text-align:center;">
					<button class="btn btn-save" onclick="save_flag_patient('<?php echo $flag; ?>')"><i
							class="icon-save"></i> <?php echo $save_btn_name; ?></button>
					<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
}
if ($type == "save_flag_patient") {
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$cause = mysqli_real_escape_string($link, $_POST["flag_cause"]);
	$remarks = mysqli_real_escape_string($link, $_POST["flag_remarks"]);

	$patient_flagged_details = mysqli_fetch_array(mysqli_query($link, "SELECT `cause` FROM `patient_flagged_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id'"));

	if ($cause) {
		if ($patient_flagged_details) {
			if (mysqli_query($link, "UPDATE `testresults_note` SET `cause`='$cause', `remarks`='$remarks', `cause_user`='$c_user', `time`='$time', `date`='$date' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id'")) {
				$return["error"] = 0;
				$return["message"] = "Updated";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(1)";
			}
		} else {
			if (mysqli_query($link, "INSERT INTO `patient_flagged_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `dept_id`, `cause`, `cause_user`, `remarks`, `remarks_user`, `time`, `date`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$dept_id','$cause','$c_user','$remarks','$c_user','$time','$date')")) {
				mysqli_query($link,"INSERT INTO `patient_flagged_records`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `dept_id`, `cause`, `cause_user`, `remarks`, `remarks_user`, `time`, `date`, `flag`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$dept_id','$cause','$c_user','$remarks','$c_user','$time','$date','1')");
				$return["error"] = 0;
				$return["message"] = "Saved";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(2)";
			}
		}
	} else {
		if ($patient_flagged_details) {
			if (mysqli_query($link, "DELETE FROM `patient_flagged_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id'")) {
				$return["error"] = 1;
				$return["message"] = "Flag removed";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(3)";
			}
		} else {
			$return["error"] = 2;
			$return["message"] = "Nothing to save";
		}
	}
	echo json_encode($return);
}
// Flag End

if ($type == "check_normal_range") {
	$patient_id = $_POST["patient_id"];
	$testid = $_POST["testid"];
	$paramid = $_POST["paramid"];
	$result = $_POST["result"];
	$instrument_id = $_POST["instrument_id"];

	include("pathology_normal_range_new.php");

	$param_ranges = load_normal($patient_id, $paramid, $result, $instrument_id);

	$param_rangez = explode("#", $param_ranges);

	if ($param_rangez[1] == "Error") {
		echo "1";
	} else {
		echo "0";
	}
}

// Test Param Sample Status Start
if ($type == "paramSampleStatus")
{
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$testid = $_POST["testid"];
	$paramid = $_POST["paramid"];

	//$test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));
	$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Parameter_old` WHERE `ID`='$paramid'"));

	$btn_name = "Save";
	$param_sample_stat = mysqli_fetch_array(mysqli_query($link, "SELECT `sample_status`, `sample_note`, `print_result` FROM `testresults_sample_stat` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid'"));
	
	if ($param_sample_stat)
	{
		$btn_name = "Update";
	}
	?>
	<div>
		<table class="table table-condensed table-bordered">
			<tr>
				<th colspan="2" style="text-align:center;">
					Enter Sample Status for : <?php echo strtoupper($param_info["Name"]); ?>
				</th>
			</tr>
			<tr>
				<th style="width:100px;">Select Status</th>
				<td>
					<select id="sampleStatus_sample_status">
						<option value="">None</option>
				<?php
					$qry=mysqli_query($link, "SELECT `status_name` FROM `sample_status` WHERE `status_name`!='' ORDER BY `status_name` ASC");
					while($data=mysqli_fetch_assoc($qry))
					{
						$sel="";
						if($data["status_name"]==$param_sample_stat["sample_status"]){ $sel="selected"; }
						
						echo "<option value='{$data["status_name"]}' $sel>{$data["status_name"]}</option>";
					}
				?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Print Result</th>
				<td>
					<select id="sampleStatus_print_result">
						<option value="0" <?php if($param_sample_stat["print_result"]=="0"){ echo "selected"; } ?>>Yes</option>
						<option value="1" <?php if($param_sample_stat["print_result"]=="1"){ echo "selected"; } ?>>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Note</th>
				<td>
					<textarea id="sampleStatus_sample_note" style="height: 100px;width: 99%;resize: none;"><?php echo $param_sample_stat["sample_note"]; ?></textarea>
				</td>
			</tr>
			<tr>
				<th colspan="2" style="text-align:center;">
					<button class="btn btn-save" onclick="paramSampleStatusSave('<?php echo $testid; ?>','<?php echo $paramid; ?>')"><i class="icon-save"></i> <?php echo $btn_name; ?></button>
					<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

if ($type == "paramSampleStatusSave")
{
	$patient_id = $_POST["patient_id"];
	$opd_id = $_POST["opd_id"];
	$ipd_id = $_POST["ipd_id"];
	$batch_no = $_POST["batch_no"];
	$dept_id = $_POST["dept_id"];
	$testid = $_POST["testid"];
	$paramid = $_POST["paramid"];
	
	$sample_status	 = mysqli_real_escape_string($link, $_POST["sample_status"]);
	$print_result 	 = mysqli_real_escape_string($link, $_POST["print_result"]);
	$sample_note	 = mysqli_real_escape_string($link, $_POST["sample_note"]);
	
	$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `vaccu` FROM `Parameter_old` WHERE `ID`='$paramid'"));
	
	$param_sample_stat = mysqli_fetch_array(mysqli_query($link, "SELECT `sample_status`, `sample_note`, `print_result` FROM `testresults_sample_stat` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid'"));

	if($sample_status)
	{
		if ($param_sample_stat)
		{
			if (mysqli_query($link, "UPDATE `testresults_sample_stat` SET `vac_id`='$param_info[vaccu]',`testid`='$testid',`paramid`='$paramid',`sample_status`='$sample_status',`sample_note`='$sample_note',`print_result`='$print_result' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid'"))
			{
				mysqli_query($link, "UPDATE `testresults` SET `result_hide`='$print_result' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid'");
				
				$return["error"] = 0;
				$return["message"] = "Updated";
			} else {
				$return["error"] = 1;
				$return["message"] = "Failed, try again later(1)";
			}
		} else {
			if (mysqli_query($link, "INSERT INTO `testresults_sample_stat`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vac_id`, `testid`, `paramid`, `sample_status`, `sample_note`, `print_result`, `user`, `time`, `date`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$param_info[vaccu]','$testid','$paramid','$sample_status','$sample_note','$print_result','$c_user','$time','$date')"))
			{
				mysqli_query($link, "UPDATE `testresults` SET `result_hide`='$print_result' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid'");
				
				$return["error"] = 0;
				$return["message"] = "Saved";
			} else {
				$return["error"] = 2;
				$return["message"] = "Failed, try again later(2)";
			}
		}
	}else
	{
		if (mysqli_query($link, "DELETE FROM `testresults_sample_stat` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid'"))
		{
			mysqli_query($link, "UPDATE `testresults` SET `result_hide`='0' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid'");
			
			$return["error"] = 0;
			$return["message"] = "Removed";
		} else {
			$return["error"] = 1;
			$return["message"] = "Failed, try again later(1)";
		}
		
		//$return["error"] = 3;
		//$return["message"] = "Nothing to save";
	}
	
	//$return["print_result"] = $print_result;
	
	echo json_encode($return);
}
// Test Param Sample Status End

mysqli_close($link);
?>
