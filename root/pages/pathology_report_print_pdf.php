<?php
session_start();

ob_start();

include ("../../includes/connection.php");
include ("../../includes/global.function.php");
//include("pathology_normal_range_new.php");

$whatsapp = 0;

$top_line_break = 0;
$doc_in_a_line = 5;
$max_line_in_a_page = 25;
$single_page_test_param_num = 4;
$div_height = "height: 650px;";
$method_max_characters = 30;

$only_result_testid = ""; // seperated by , // Test Like Urine RE

$nabl_star_symbol = "";

$date = date("Y-m-d");
$time = date("H:i:s");

$c_user = trim($_SESSION['emp_id']);
if (!$c_user) {
	exit();
}
$emp_info = mysqli_fetch_array(mysqli_query($link, " SELECT `branch_id`,`name`,`levelid` FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id = $emp_info["branch_id"];

$nb_text_patho = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));

$uhid = mysqli_real_escape_string($link, base64_decode($_GET['uhid']));
$opd_id = mysqli_real_escape_string($link, base64_decode($_GET['opd_id']));
$ipd_id = mysqli_real_escape_string($link, base64_decode($_GET['ipd_id']));
$batch_no = mysqli_real_escape_string($link, base64_decode($_GET['batch_no']));
$tests = mysqli_real_escape_string($link, base64_decode($_GET['tests']));
$lab_doc_id = mysqli_real_escape_string($link, base64_decode($_GET['doc']));
$user = mysqli_real_escape_string($link, base64_decode($_GET['user']));
$view = mysqli_real_escape_string($link, base64_decode($_GET['view']));
$iso_no = mysqli_real_escape_string($link, base64_decode($_GET['iso_no']));

if (!$iso_no) {
	$iso_no = 0;
}

$page_breaker = "@@@@";

$doc_sign = mysqli_real_escape_string($link, base64_decode($_GET['sel_doc']));
$docc = explode(",", $doc_sign);

$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

$pat_reg = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `opd_id`='$ipd_id')"));
$bill_id = $pat_reg["opd_id"];

$reg_date = $pat_reg["date"];

$centre_info = mysqli_fetch_array(mysqli_query($link, "SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]'"));

if ($pat_info["dob"] != "") {
	$age = age_calculator_date_only($pat_info["dob"], $reg_date);
} else {
	$age = $pat_info["age"] . " " . $pat_info["age_type"];
}

if ($pat_reg["type"] == 3) {
	$ipd_ref_test = mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid`,`refbydoctorid` FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no'"));

	$refbydoctorid = $ipd_ref_test["refbydoctorid"];
} else {
	$refbydoctorid = $pat_reg["refbydoctorid"];
}

$pat_pay_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
$pat_pay_det["balance"] = 0;
if ($pat_pay_det["balance"] > 0 && $emp_info["levelid"] != 1) {
	$tests = "";
}

$ref_doc = mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name`,`qualification` FROM `refbydoctor_master` WHERE `refbydoctorid`='$refbydoctorid'"));

$barcode_data = $uhid . "-" . $bill_id . "-" . $batch_no . "-" . $pat_info["name"];


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

$test_doc = explode("@", $tests);

foreach ($test_doc as $testid_doc) {
	if ($testid_doc) {
		mysqli_query($link, "INSERT INTO `pathology_report_print_sequence`(`testid`, `user`, `ip_addr`) VALUES ('$testid_doc','$c_user','$ip_addr')");

		$testall_array[] = $testid_doc;

		if ($iso_no == 0) {
			$doc_result = mysqli_fetch_array(mysqli_query($link, "select `doc` from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid_doc' limit 1"));
		} else {
			$doc_result = mysqli_fetch_array(mysqli_query($link, "select `doc` from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid_doc' and iso_no='$iso_no' limit 1"));
		}

		$doc_summary = mysqli_fetch_array(mysqli_query($link, "select `doc` from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid_doc' limit 1"));
		$doc_widal = mysqli_fetch_array(mysqli_query($link, "select `doc` from widalresult where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' limit 1"));

		if ($doc_result["doc"] == "" || !$doc_result["doc"]) {
			$doc_result["doc"] = 0;
		}

		if ($doc_summary["doc"] == "" || !$doc_summary["doc"]) {
			$doc_summary["doc"] = 0;
		}

		if ($doc_widal["doc"] == "" || !$doc_widal["doc"]) {
			$doc_widal["doc"] = 0;
		}

		$doctors[] = $doc_result["doc"];

		$doctors[] = $doc_summary["doc"];

		$doctors[] = $doc_widal["doc"];
	}
}

$testall = implode(",", $testall_array);

$doctors = array_unique($doctors);
//print_r($doctors);
//echo sizeof($doctors);

$page = 1;
foreach ($doctors as $doctor) {
	if ($doctor >= 0) {
		//break;
		$test_serial = 0;
		$profile_serial = 0;
		$line_no = 0;
		$dist_dept_qry = mysqli_query($link, "SELECT DISTINCT `type_id` FROM `testmaster` WHERE `testid` IN($testall) AND `category_id`=1 ORDER BY `type_name` ASC");
		while ($dist_dept = mysqli_fetch_array($dist_dept_qry)) {
			$type_id = $dist_dept["type_id"];

			//$test_dept_qry=mysqli_query($link, "SELECT `testid`,`testname` FROM `testmaster` WHERE `testid` IN($testall) AND `category_id`=1 AND `type_id`='$type_id' AND `testid` IN(SELECT DISTINCT a.`TestId` FROM `Testparameter` a, `nabl_test_param` b WHERE a.`ParamaterId`=b.`paramid`) ORDER BY `testid` ASC");

			$nabl_val = 1;
			$test_dept_qry = mysqli_query($link, "SELECT a.`testid`,a.`testname`,a.`type_id` FROM `testmaster` a, `pathology_report_print_sequence` b WHERE a.`testid`=b.`testid` AND a.`testid` IN($testall) AND a.`category_id`=1  AND a.`type_id`='$type_id' AND a.`testid` IN(SELECT DISTINCT a.`TestId` FROM `Testparameter` a, `nabl_test_param` b WHERE a.`ParamaterId`=b.`paramid`) ORDER BY b.`slno` ASC");
			$test_dept_num = mysqli_num_rows($test_dept_qry);
			if ($test_dept_num > 0) {
				if ($non_nabl_test_num > 0) {
					$page++;
					$line_no = 0;
				}
			}

			while ($test_dept = mysqli_fetch_array($test_dept_qry))
			{
				$nabl_test_num++;
				$non_nabl_test_num = 0;

				$testid = $test_dept["testid"];

				$culture = 0;

				if (strpos($test_dept['testname'], 'culture') !== false) {
					$culture = 1;
				}

				if (strpos($test_dept['testname'], 'CULTURE') !== false) {
					$culture = 1;
				}

				if (strpos($test_dept['testname'], 'Culture') !== false) {
					$culture = 1;
				}

				if ($culture == 1) {
					//$test_page[]=$testid;

					$test_result_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'"));
					if ($test_result_num > 0)
					{
						if ($test_serial > 0 || $profile_serial > 0) {
							$page++;

							$test_serial = 0;
							$profile_serial = 0;
						}
						$line_no = 0;
						//mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','5','$c_user','$ip_addr','$nabl_val')");

						//$page++;

						if ($iso_no == 0) {
							$iso_qry = mysqli_query($link, "SELECT DISTINCT `iso_no` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");
						} else {
							$iso_qry = mysqli_query($link, "SELECT DISTINCT `iso_no` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='$iso_no' AND `doc`='$doctor'");
						}

						$iso_qry = mysqli_query($link, "SELECT DISTINCT `iso_no` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");
						while ($iso_info = mysqli_fetch_array($iso_qry)) {
							mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','$iso_info[iso_no]','0','$doctor','$page','5','$c_user','$ip_addr','$nabl_val')");

							$page++;
						}
					}else
					{
						$test_serial=0;
						$profile_serial=0;
						$line_no=0;
						
						$test_sum_qry=mysqli_query($link,"SELECT * FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");
						
						$test_sum=mysqli_fetch_array($test_sum_qry);
						$summary_text=$test_sum["summary"];
						
						if(strpos($summary_text, $page_breaker) !== false)
						{
							$part=1;
							$summary_texts=explode($page_breaker,$summary_text);
							foreach($summary_texts AS $summary_parts)
							{
								if($summary_parts)
								{
									mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','$part','0','$doctor','$page','3','$c_user','$ip_addr','$nabl_val')");
									
									$part++;
									$page++;
								}
							}
						}
						else if($summary_text)
						{
							mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','3','$c_user','$ip_addr','$nabl_val')");
							
							$page++;
						}
					}
				} else {
					$test_result_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'"));

					$test_sum_qry = mysqli_query($link, "SELECT * FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");

					$test_sum_num = mysqli_num_rows($test_sum_qry);

					//$test_sum_num_tst=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `test_summary` WHERE `testid`='$testid'"));
					$test_sum_num_tst = 0;

					$test_note = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`>0 "));

					if (($test_result_num > 0 && $test_sum_num > 0) || ($test_result_num > 0 && $test_sum_num_tst > 0) || ($test_result_num > 0 && $test_note["note"] != "")) {
						//$test_page[]=$testid;

						if ($test_serial > 0 || $profile_serial > 0) {
							$page++;

							$test_serial = 0;
							$profile_serial = 0;
						}
						$line_no = 0;
						//mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','2','$c_user','$ip_addr','$nabl_val')");

						//$page++;

						$test_param_qry = mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ASC");
						while ($test_param = mysqli_fetch_array($test_param_qry)) {
							$paramid = $test_param["ParamaterId"];

							$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `doc`='$doctor'"));

							$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));

							if ($param_info["ResultType"] == 0) {
								$test_result = 1;
							}

							if ($test_result) {
								$range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));
								$normal_range_line = substr_count($range["normal_range"], "\n") + 1;
								//echo $normal_range_line." = ".$range["normal_range"];
								if ($normal_range_line <= 0) {
									$normal_range_line = 1;
								}

								$result_line = substr_count($test_result["result"], "\n") + 1;

								if ($result_line > $normal_range_line) {
									$normal_range_line = $result_line;
								}

								$line_no += $normal_range_line;

								if ($line_no > $max_line_in_a_page) {
									$line_no = $normal_range_line;
									$page++;
								}

								mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','$paramid','0','0','$doctor','$page','2','$c_user','$ip_addr','$nabl_val')");

								$test_serial = 1;
								$profile_serial = 1;

								if ($page >= 100) {
									break;
								}
							}
						}
					} else if ($test_sum_num > 0 && $testid != 1227) {
						//$test_page[]=$testid;

						if ($test_serial > 0 || $profile_serial > 0) {
							$page++;

							$test_serial = 0;
							$profile_serial = 0;
						}
						$line_no = 0;

						$test_sum = mysqli_fetch_array($test_sum_qry);
						$summary_text = $test_sum["summary"];

						if (strpos($summary_text, $page_breaker) !== false) {
							$part = 1;
							$summary_texts = explode($page_breaker, $summary_text);
							foreach ($summary_texts as $summary_parts) {
								if ($summary_parts) {
									mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','$part','0','$doctor','$page','3','$c_user','$ip_addr','$nabl_val')");

									$part++;
									$page++;
								}
							}
						} else if($summary_text) {
							mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','3','$c_user','$ip_addr','$nabl_val')");

							$page++;
						}
					} else {
						if ($testid == 1227) {
							$widal_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `doc`='$doctor' limit 1"));
							if ($widal_num > 0) {
								//$test_page[]=$testid;

								if ($test_serial > 0 || $profile_serial > 0) {
									$page++;

									$test_serial = 0;
									$profile_serial = 0;
								}
								$line_no = 0;
								mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','4','$c_user','$ip_addr','$nabl_val')");

								$page++;
							}
						} else {
							//$test_result_qry=mysqli_query($link, "SELECT `paramid`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor' ORDER BY `sequence` ASC");

							$test_result_qry = mysqli_query($link, "SELECT a.`paramid`,a.`range_id` FROM `testresults` a, `Testparameter` b WHERE a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND a.`testid`='$testid' AND a.`doc`='$doctor' AND a.`testid`=b.`TestId` AND a.`paramid`=b.`ParamaterId` ORDER BY a.`sequence` ASC");

							$test_result_num = mysqli_num_rows($test_result_qry);
							if ($test_result_num >= $single_page_test_param_num) // Single Page Test
							{
								$page++;
								$line_no = 0;
								if ($test_serial > 0 || $profile_serial > 0) {
									$test_serial = 0;
									$profile_serial = 0;
								}

								//mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','1','$c_user','$ip_addr','$nabl_val')");

								$line_no = 0;

								$non_nabl_params = array();

								$test_param_qry = mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ASC");
								while ($test_param = mysqli_fetch_array($test_param_qry)) {
									$paramid = $test_param["ParamaterId"];

									$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `doc`='$doctor'"));

									$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));

									if ($param_info["ResultType"] == 0) {
										$test_result = 1;
									}

									if ($test_result) {
										$nabl_chk = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `nabl_test_param` WHERE `paramid`='$paramid'"));
										if ($nabl_chk) {
											$range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));
											$normal_range_line = substr_count($range["normal_range"], "\n") + 1;
											//echo $normal_range_line." = ".$range["normal_range"];
											if ($normal_range_line <= 0) {
												$normal_range_line = 1;
											}

											$result_line = substr_count($test_result["result"], "\n") + 1;

											if ($result_line > $normal_range_line) {
												$normal_range_line = $result_line;
											}

											$line_no += $normal_range_line;

											if ($line_no > $max_line_in_a_page) {
												$line_no = $normal_range_line;
												$page++;
											}

											mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','$paramid','0','0','$doctor','$page','1','$c_user','$ip_addr','$nabl_val')");

											$test_serial = 1;
											$profile_serial = 1;

											if ($page >= 100) {
												break;
											}
										} else {
											$non_nabl_params[] = $paramid;
										}
									}
								}
								$non_nabl_params = array_unique($non_nabl_params);
								$non_nabl_paramids = implode(",", $non_nabl_params);

								$page++;
								$line_no = 0;

								$test_param_qry = mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' AND `ParamaterId` IN($non_nabl_paramids) ORDER BY `sequence` ASC");
								while ($test_param = mysqli_fetch_array($test_param_qry)) {
									$paramid = $test_param["ParamaterId"];

									$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `doc`='$doctor'"));

									$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));

									if ($param_info["ResultType"] == 0) {
										$test_result = 1;
									}

									if ($test_result) {
										$range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));
										$normal_range_line = substr_count($range["normal_range"], "\n") + 1;
										//echo $normal_range_line." = ".$range["normal_range"];
										if ($normal_range_line <= 0) {
											$normal_range_line = 1;
										}

										$result_line = substr_count($test_result["result"], "\n") + 1;

										if ($result_line > $normal_range_line) {
											$normal_range_line = $result_line;
										}

										$line_no += $normal_range_line;

										if ($line_no > $max_line_in_a_page) {
											$line_no = $normal_range_line;
											$page++;
										}

										mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','$paramid','0','0','$doctor','$page','1','$c_user','$ip_addr','0')");

										$test_serial = 1;
										$profile_serial = 1;

										if ($page >= 100) {
											break;
										}
									}
								}
							} else if ($test_result_num > 0 && $test_result_num < $single_page_test_param_num) {
								if ($profile_serial > 0) {
									$page++;
									$profile_serial = 0;
									$line_no = 0;
								}

								$test_param_qry = mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ASC");
								while ($test_param = mysqli_fetch_array($test_param_qry)) {
									$paramid = $test_param["ParamaterId"];

									$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `doc`='$doctor'"));

									$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));

									if ($param_info["ResultType"] == 0) {
										$test_result = 1;
									}

									if ($test_result) {
										$range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));
										$normal_range_line = substr_count($range["normal_range"], "\n") + 1;
										//echo $normal_range_line." = ".$range["normal_range"];
										if ($normal_range_line <= 0) {
											$normal_range_line = 1;
										}

										$line_no += $normal_range_line;

										if ($line_no > $max_line_in_a_page) {
											$line_no = $normal_range_line;
											$page++;
										}

										mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','$paramid','$line_no','0','$doctor','$page','1','$c_user','$ip_addr','$nabl_val')");

										$test_serial = 1;

										if ($page >= 100) {
											break;
										}
									}
								}
							}
						}
					}
				}
			}
			//$page++; // Non NABL
			//$line_no=0;

			//$test_dept_qry=mysqli_query($link, "SELECT `testid`,`testname` FROM `testmaster` WHERE `testid` IN($testall) AND `category_id`=1 AND `type_id`='$type_id' AND `testid` NOT IN(SELECT DISTINCT a.`TestId` FROM `Testparameter` a, `nabl_test_param` b WHERE a.`ParamaterId`=b.`paramid`) ORDER BY `testid` ASC");

			$nabl_val = 0;
			$test_dept_qry = mysqli_query($link, "SELECT a.`testid`,a.`testname`,a.`type_id` FROM `testmaster` a, `pathology_report_print_sequence` b WHERE a.`testid`=b.`testid` AND a.`testid` IN($testall) AND a.`category_id`=1  AND a.`type_id`='$type_id' AND a.`testid` NOT IN(SELECT DISTINCT a.`TestId` FROM `Testparameter` a, `nabl_test_param` b WHERE a.`ParamaterId`=b.`paramid`) ORDER BY b.`slno` ASC");
			$test_dept_num = mysqli_num_rows($test_dept_qry);
			if ($test_dept_num > 0) {
				if ($nabl_test_num > 0) {
					$page++;
					$line_no = 0;
				}
			}
			while ($test_dept = mysqli_fetch_array($test_dept_qry)) {
				$non_nabl_test_num++;
				$nabl_test_num = 0;

				$testid = $test_dept["testid"];

				$culture = 0;

				if (strpos($test_dept['testname'], 'culture') !== false) {
					$culture = 1;
				}

				if (strpos($test_dept['testname'], 'CULTURE') !== false) {
					$culture = 1;
				}

				if (strpos($test_dept['testname'], 'Culture') !== false) {
					$culture = 1;
				}

				if ($culture == 1) {
					//$test_page[]=$testid;

					$test_result_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'"));
					if ($test_result_num > 0)
					{
						if ($test_serial > 0 || $profile_serial > 0) {
							$page++;

							$test_serial = 0;
							$profile_serial = 0;
						}
						$line_no = 0;
						//mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','5','$c_user','$ip_addr','$nabl_val')");

						//$page++;

						if ($iso_no == 0) {
							$iso_qry = mysqli_query($link, "SELECT DISTINCT `iso_no` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");
						} else {
							$iso_qry = mysqli_query($link, "SELECT DISTINCT `iso_no` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='$iso_no' AND `doc`='$doctor'");
						}

						while ($iso_info = mysqli_fetch_array($iso_qry)) {
							mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','$iso_info[iso_no]','0','$doctor','$page','5','$c_user','$ip_addr','$nabl_val')");

							$page++;
						}
					}else
					{
						$test_serial=0;
						$profile_serial=0;
						$line_no=0;
						
						$test_sum_qry=mysqli_query($link,"SELECT * FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");
						
						$test_sum=mysqli_fetch_array($test_sum_qry);
						$summary_text=$test_sum["summary"];
						
						if(strpos($summary_text, $page_breaker) !== false)
						{
							$part=1;
							$summary_texts=explode($page_breaker,$summary_text);
							foreach($summary_texts AS $summary_parts)
							{
								if($summary_parts)
								{
									mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','$part','0','$doctor','$page','3','$c_user','$ip_addr','$nabl_val')");
									
									$part++;
									$page++;
								}
							}
						}
						else if($summary_text)
						{
							mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','3','$c_user','$ip_addr','$nabl_val')");
							
							$page++;
						}
					}
				} else {
					$test_result_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'"));

					$test_sum_qry = mysqli_query($link, "SELECT * FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");

					$test_sum_num = mysqli_num_rows($test_sum_qry);

					//$test_sum_num_tst=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `test_summary` WHERE `testid`='$testid'"));
					$test_sum_num_tst = 0;

					$test_note = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`>0 "));

					if (($test_result_num > 0 && $test_sum_num > 0) || ($test_result_num > 0 && $test_sum_num_tst > 0) || ($test_result_num > 0 && $test_note["note"] != "")) {
						//$test_page[]=$testid;

						if ($test_serial > 0 || $profile_serial > 0) {
							$page++;

							$test_serial = 0;
							$profile_serial = 0;
						}
						$line_no = 0;
						//mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','2','$c_user','$ip_addr','$nabl_val')");

						//$page++;

						$test_param_qry = mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ASC");
						while ($test_param = mysqli_fetch_array($test_param_qry)) {
							$paramid = $test_param["ParamaterId"];

							$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `doc`='$doctor'"));

							$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));

							if ($param_info["ResultType"] == 0) {
								$test_result = 1;
							}

							if ($test_result) {
								$range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));
								$normal_range_line = substr_count($range["normal_range"], "\n") + 1;
								//echo $normal_range_line." = ".$range["normal_range"];
								if ($normal_range_line <= 0) {
									$normal_range_line = 1;
								}

								$result_line = substr_count($test_result["result"], "\n") + 1;

								if ($result_line > $normal_range_line) {
									$normal_range_line = $result_line;
								}

								$line_no += $normal_range_line;

								if ($line_no > $max_line_in_a_page) {
									$line_no = $normal_range_line;
									$page++;
								}

								mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','$paramid','0','0','$doctor','$page','2','$c_user','$ip_addr','$nabl_val')");

								$test_serial = 1;
								$profile_serial = 1;

								if ($page >= 100) {
									break;
								}
							}
						}
					} else if ($test_sum_num > 0 && $testid != 1227) {
						//$test_page[]=$testid;

						if ($test_serial > 0 || $profile_serial > 0) {
							$page++;

							$test_serial = 0;
							$profile_serial = 0;
						}
						$line_no = 0;

						$test_sum = mysqli_fetch_array($test_sum_qry);
						$summary_text = $test_sum["summary"];

						if (strpos($summary_text, $page_breaker) !== false) {
							$part = 1;
							$summary_texts = explode($page_breaker, $summary_text);
							foreach ($summary_texts as $summary_parts) {
								if ($summary_parts) {
									mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','$part','0','$doctor','$page','3','$c_user','$ip_addr','$nabl_val')");

									$part++;
									$page++;
								}
							}
						} else if($summary_text) {
							mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','3','$c_user','$ip_addr','$nabl_val')");

							$page++;
						}
					} else {
						if ($testid == 1227) {
							$widal_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `doc`='$doctor' limit 1"));
							if ($widal_num > 0) {
								//$test_page[]=$testid;

								if ($test_serial > 0 || $profile_serial > 0) {
									$page++;

									$test_serial = 0;
									$profile_serial = 0;
								}
								$line_no = 0;
								mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','4','$c_user','$ip_addr','$nabl_val')");

								$page++;
							}
						} else {
							//$test_result_qry=mysqli_query($link, "SELECT `paramid`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor' ORDER BY `sequence` ASC");

							$test_result_qry = mysqli_query($link, "SELECT a.`paramid`,a.`range_id` FROM `testresults` a, `Testparameter` b WHERE a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND a.`testid`='$testid' AND a.`doc`='$doctor' AND a.`testid`=b.`TestId` AND a.`paramid`=b.`ParamaterId` ORDER BY a.`sequence` ASC");

							$test_result_num = mysqli_num_rows($test_result_qry);
							if ($test_result_num >= $single_page_test_param_num) // Single Page Test
							{
								$page++;
								$line_no = 0;
								if ($test_serial > 0 || $profile_serial > 0) {
									$test_serial = 0;
									$profile_serial = 0;
								}

								//mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','1','$c_user','$ip_addr','$nabl_val')");

								$line_no = 0;

								$test_param_qry = mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ASC");
								while ($test_param = mysqli_fetch_array($test_param_qry)) {
									$paramid = $test_param["ParamaterId"];

									$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `doc`='$doctor'"));

									$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));

									if ($param_info["ResultType"] == 0) {
										$test_result = 1;
									}

									if ($test_result) {
										$range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));
										$normal_range_line = substr_count($range["normal_range"], "\n") + 1;
										//echo $normal_range_line." = ".$range["normal_range"];
										if ($normal_range_line <= 0) {
											$normal_range_line = 1;
										}

										$result_line = substr_count($test_result["result"], "\n") + 1;

										if ($result_line > $normal_range_line) {
											$normal_range_line = $result_line;
										}

										$line_no += $normal_range_line;

										if ($line_no > $max_line_in_a_page) {
											$line_no = $normal_range_line;
											$page++;
										}

										mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','$paramid','0','0','$doctor','$page','1','$c_user','$ip_addr','$nabl_val')");

										$test_serial = 1;
										$profile_serial = 1;

										if ($page >= 100) {
											break;
										}
									}
								}
							} else if ($test_result_num > 0 && $test_result_num < $single_page_test_param_num) {
								if ($profile_serial > 0) {
									$page++;
									$profile_serial = 0;
									$line_no = 0;
								}

								$test_param_qry = mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ASC");
								while ($test_param = mysqli_fetch_array($test_param_qry)) {
									$paramid = $test_param["ParamaterId"];

									$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `doc`='$doctor'"));

									$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType` FROM `Parameter_old` WHERE `ID`='$paramid'"));

									if ($param_info["ResultType"] == 0) {
										$test_result = 1;
									}

									if ($test_result) {
										$range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));
										$normal_range_line = substr_count($range["normal_range"], "\n") + 1;
										//echo $normal_range_line." = ".$range["normal_range"];
										if ($normal_range_line <= 0) {
											$normal_range_line = 1;
										}

										$line_no += $normal_range_line;

										if ($line_no > $max_line_in_a_page) {
											$line_no = $normal_range_line;
											$page++;
										}

										mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','$paramid','$line_no','0','$doctor','$page','1','$c_user','$ip_addr','$nabl_val')");

										$test_serial = 1;

										if ($page >= 100) {
											break;
										}
									}
								}
							}
						}
					}
				}
			}
			$page++; // Department change
			$line_no = 0;
		}
		$page++; // doctor change
		$line_no = 0;
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Pathology Report-<?php echo $bill_id . "-" . $batch_no; ?></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="../../css/custom.css" type="text/css" rel="stylesheet" />
	<script src="../../js/jquery.min.js"></script>
	<!--<link href="../../css/report.css" rel="stylesheet" type="text/css">-->
	<!--<link href="../../css/loader.css" rel="stylesheet" type="text/css">-->
	<script>
		$(document).on("contextmenu", function (e) {
			if ($("#user").text().trim() != '102' || $("#user").text().trim() != '102') {
				e.preventDefault();
			}
		});
		$(document).ajaxStop(function () {
			$("#loader").hide();
		});
		$(document).ajaxStart(function () {
			$("#loader").show();
		});

	</script>
	<style>

	</style>
</head>

<body onkeyup="close_window(event)"
	onafterprint="save_print_test('<?php echo $tests; ?>','<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $bill_id; ?>')">
	<?php
	$nabl_true = 0;

	$total_pages = mysqli_num_rows(mysqli_query($link, "SELECT DISTINCT `page_no` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `user`='$c_user' AND `ip_addr`='$ip_addr'"));

	// Test Result and summary
	
	$result_table = "1,2";

	$report_doc_qry = mysqli_query($link, "SELECT DISTINCT `doc_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");

	$page = 1;
	while ($report_doc = mysqli_fetch_array($report_doc_qry)) {
		$doc_id = $report_doc["doc_id"];

		$report_page_qry = mysqli_query($link, "SELECT DISTINCT `page_no` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");
		$report_page_num = mysqli_num_rows($report_page_qry);
		while ($report_page = mysqli_fetch_array($report_page_qry)) {
			$report_page_num--;
			$page_no = $report_page["page_no"];

			$only_result_testid_num = mysqli_num_rows(mysqli_query($link, "SELECT DISTINCT `page_no` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($only_result_testid) AND `doc_id`='$doc_id' AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) AND `page_no`='$page_no' ORDER BY `slno` ASC"));

			$dept_info = mysqli_fetch_array(mysqli_query($link, "SELECT `id`,`name` FROM `test_department` WHERE `id` IN(SELECT `type_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no')"));
			$type_id = $dept_info["type_id"];

			if ($page > 1) {
				echo '<div class="pagebreak"></div>';
			}

			$sample_names = "";
			//$sample_qry=mysqli_query($link, "SELECT DISTINCT a.`sampleid` FROM `phlebo_sample` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
	
			$sample_qry = mysqli_query($link, "SELECT DISTINCT a.`SampleId` FROM `TestSample` a, `pathology_report_print` b WHERE a.`TestId`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");

			while ($samples = mysqli_fetch_array($sample_qry)) {
				$sample_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Sample` WHERE `ID`='$samples[SampleId]'"));

				$sample_names .= $sample_info["Name"] . ",";
			}
			if ($sample_names == "") {
				$sample_qry = mysqli_query($link, "SELECT DISTINCT a.`SampleId` FROM `TestSample` a, `pathology_report_print` b WHERE a.`TestId`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
				while ($samples = mysqli_fetch_array($sample_qry)) {
					$sample_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Sample` WHERE `ID`='$samples[SampleId]'"));
					if ($sample_info) {
						$sample_names .= $sample_info["Name"] . ",";
					}
				}
			}

			// Sample Collection Time
			$sample_collection = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `phlebo_sample` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if ($sample_collection) {
				$sample_collection_date = $sample_collection["date"];
				$sample_collection_time = $sample_collection["time"];
			} else {
				$sample_collection_date = $pat_reg["date"];
				$sample_collection_time = $pat_reg["time"];
			}

			// Sample Receive Time
			$sample_receive = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `lab_sample_receive` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if ($sample_receive) {
				$sample_receive_date = $sample_receive["date"];
				$sample_receive_time = $sample_receive["time"];
			} else if ($sample_collection) {
				$sample_receive_date = $sample_collection["date"];
				$sample_receive_time = $sample_collection["time"];
			} else {
				$sample_receive_date = $pat_reg["date"];
				$sample_receive_time = $pat_reg["time"];
			}

			// Reporting Time
			$report_time = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `testresults` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if (!$report_time) {
				$report_time = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `patient_test_summary` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));

				if (!$report_time) {
					$report_time = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `widalresult` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
				}
			}

			// Report entry and checked by
			$data_entry_users = array();
			$data_checked_users = array();

			$report_entry_qry = mysqli_query($link, "SELECT DISTINCT a.`tech`, a.`main_tech` FROM `testresults` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
			while ($report_entry = mysqli_fetch_array($report_entry_qry)) {
				$data_entry_users[] = $report_entry["tech"];
				$data_checked_users[] = $report_entry["main_tech"];
			}

			$report_entry_qry = mysqli_query($link, "SELECT DISTINCT a.`user`, a.`main_tech` FROM `patient_test_summary` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
			while ($report_entry = mysqli_fetch_array($report_entry_qry)) {
				$data_entry_users[] = $report_entry["user"];
				$data_checked_users[] = $report_entry["main_tech"];
			}

			$data_entry_users = array_unique($data_entry_users);
			$data_entry_user_ids = implode(",", $data_entry_users);

			$data_entry_names = "";
			$tech_info_qry = mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` IN($data_entry_user_ids)");
			while ($tech_info = mysqli_fetch_array($tech_info_qry)) {
				$data_entry_names .= $tech_info["name"] . ",";
			}

			$data_checked_users = array_unique($data_checked_users);
			$data_checked_user_ids = implode(",", $data_checked_users);

			$data_checked_names = "";
			$tech_info_qry = mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` IN($data_checked_user_ids)");
			while ($tech_info = mysqli_fetch_array($tech_info_qry)) {
				$data_checked_names .= $tech_info["name"] . ",";
			}

			$page_param_chk = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(*) AS `param_num`, `result_table` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC"));
			?>
			<?php
			$br = 0;
			while ($br < $top_line_break) {
				echo "<br>";
				$br++;
			}
			?>
			<div class="container-fluid"> <!-- style="border: 2px solid #000;height: 1080px;" -->
				<div class="row">
					<?php
					//include("pathology_report_page_header_pdf.php");
					include ("pathology_report_header_pdf.php");
					?>
				</div>
				<div class="row report_div" style="<?php echo $div_height; ?>">
					<table class="table table-condensed table-no-top-border report_table">
						<tr class="report_header">
							<th class="test_name" style="width: 35%;">TEST</th>
							<th class="test_result">RESULTS &nbsp; &nbsp;</th>
							<?php
							if ($only_result_testid_num == 0) {
								?>
								<th class="test_unit">UNIT &nbsp; &nbsp;</th>
								<th class="test_ref">BIOLOGICAL REF.INTERVAL &nbsp; &nbsp;</th>
								<th class="test_method">METHOD</th>
								<?php
							}
							?>
						</tr>
						<?php
						$report_test_qry = mysqli_query($link, "SELECT DISTINCT `testid` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
						while ($report_test = mysqli_fetch_array($report_test_qry)) {
							$testid = $report_test["testid"];

							// Record print
							if ($view == 0) {
								mysqli_query($link, "INSERT INTO `testreport_print`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `date`, `time`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$date','$time')");
							}

							$test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));

							$param_td_th = "th";
							$left_space = "";

							//$param_num=mysqli_num_rows(mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid'"));
							$param_num = mysqli_num_rows(mysqli_query($link, "SELECT a.* FROM `testresults` a, `Testparameter` b WHERE a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND a.`testid`=b.`TestId` AND a.`paramid`=b.`ParamaterId` AND a.`testid`='$testid' AND a.`doc`='$doc_id'"));
							if ($param_num > 1) {
								if ($testid != 53) // CREATININE
								{
									$param_td_th = "td";
									$left_space = " &nbsp;&nbsp;&nbsp;";
									?>
									<tr>
										<th colspan="5" class="test_name no_top_border" style="border-top: 1px solid #fff !important;">
											<?php echo $test_info["testname"]; ?></th>
									</tr>
									<?php
								}
							}
							$report_qry = mysqli_query($link, "SELECT * FROM `pathology_report_print` WHERE `testid`='$testid' AND `param_id`>0 AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
							$report_num = mysqli_num_rows($report_qry);
							if ($report_num > 0) {
								while ($report = mysqli_fetch_array($report_qry)) {
									$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType`,`Name`,`UnitsID`,`sample`,`method` FROM `Parameter_old` WHERE `ID`='$report[param_id]'"));

									if ($param_info["ResultType"] != 0) {
										$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id' AND `paramid`='$report[param_id]'"));

										if ($test_result) {
											$normal_range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));

											$unit_info = mysqli_fetch_array(mysqli_query($link, "SELECT `unit_name` FROM `Units` WHERE `ID`='$param_info[UnitsID]'"));

											$method = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `test_methods` WHERE `id`='$param_info[method]'"));

											$result_td_th = "td";
											if ($test_result["range_status"] > 0) {
												$result_td_th = "th";
											}

											$result_td_span = "1";
											if ($param_info["ResultType"] == 27) {
												$result_td_span = "4";
											}

											// NABL
											$nabl_star = "";
											$nabl_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `nabl` WHERE `nabl`>0"));
											if ($nabl_num > 0 && $report["nabl"] == 1) {
												$nabl_star = $nabl_star_symbol;
												$nabl_check_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `nabl_test_param` WHERE `paramid`='$report[param_id]'"));
												if ($nabl_check_num > 0) {
													$nabl_true++;
													$nabl_star = "";
												}
											}

											$test_result["result"] = str_replace("\\", "", $test_result["result"]);
											?>
											<tr>
												<<?php echo $param_td_th; ?> class="test_name no_top_border" style="border-top: 1px solid #fff
													!important;padding: 1px 1px
													!important;"><?php echo $left_space . $nabl_star . $param_info["Name"]; ?></<?php echo $param_td_th; ?>>
												<<?php echo $result_td_th; ?> class="test_result no_top_border"
													colspan="<?php echo $result_td_span; ?>" style="border-top: 1px solid #fff !important;padding:
						1px 1px !important;"><?php echo $test_result["result"]; ?></<?php echo $result_td_th; ?>>
												<?php
												if ($only_result_testid_num == 0) {
													if ($result_td_span == 1) {
														?>
														<td class="test_unit no_top_border"
															style="border-top: 1px solid #fff !important;padding: 1px 1px !important;">
															<?php echo $unit_info["unit_name"]; ?></td>
														<td class="test_ref no_top_border"
															style="border-top: 1px solid #fff !important;padding: 1px 1px !important;">
															<?php echo nl2br($normal_range["normal_range"]); ?></td>
														<td class="test_method test_method_td no_top_border"
															style="font-size: 8px !important;border-top: 1px solid #fff !important;padding: 1px 1px !important;">
															<?php if ($method["name"]) {
																echo substr($method["name"], 0, $method_max_characters) . "";
															} ?></td>
														<?php
													}
												}
												?>
											</tr>
											<?php
										}
									} else {
										echo "<tr><th colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;padding: 1px 1px !important;'>$left_space$param_info[Name]</th></tr>";
									}
								}

								$more_report_test_num = mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc_id`='$doc_id' AND `page_no`>'$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC"));
								if ($more_report_test_num == 0) // Last Page
								{
									$test_summary_text = "";
									$pat_test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id'"));
									if ($pat_test_summary) {
										$test_summary_text = $pat_test_summary["summary"];
									} else {
										$test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `test_summary` WHERE `testid`='$testid'"));
										if ($test_summary) {
											//$test_summary_text=$test_summary["summary"];
										}
									}
									if ($test_summary_text) {
										echo "<tr><td colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;padding: 1px 1px !important;'><br>$test_summary_text</td></tr>";
									}

									// Test Notes
									$pat_test_notes = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`>0"));
									if ($pat_test_notes["note"]) {
										echo "<tr><td colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;padding: 1px 1px !important;'><br>$pat_test_notes[note]</td></tr>";
									}
								}
							} else {
								// Single Page report
								$report_qry = mysqli_query($link, "SELECT * FROM `pathology_report_print` WHERE `testid`='$testid' AND `param_id`=0 AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
								$report = mysqli_fetch_array($report_qry);

								$param_td_th = "td";
								//$left_space="";
								$test_param_qry = mysqli_query($link, "SELECT `ParamaterId` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ASC");
								//$test_param_qry=mysqli_query($link, "SELECT a.`ParamaterId` FROM `Testparameter` a, `testresults` b WHERE a.`ParamaterId`=b.`paramid` AND a.`TestId`=b.`testid` AND a.`TestId`='$testid' AND b.`patient_id`='$uhid' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no' AND b.`doc`='$doc_id' ORDER BY a.`sequence` ASC");
								while ($test_param = mysqli_fetch_array($test_param_qry)) {
									$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ResultType`,`Name`,`UnitsID`,`sample`,`method` FROM `Parameter_old` WHERE `ID`='$test_param[ParamaterId]'"));

									if ($param_info["ResultType"] != 0) {
										$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id' AND `paramid`='$test_param[ParamaterId]'"));

										if ($test_result) {
											$normal_range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));

											$unit_info = mysqli_fetch_array(mysqli_query($link, "SELECT `unit_name` FROM `Units` WHERE `ID`='$param_info[UnitsID]'"));

											$method = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `test_methods` WHERE `id`='$param_info[method]'"));

											$result_td_th = "td";
											if ($test_result["range_status"] > 0) {
												$result_td_th = "th";
											}

											$result_td_span = "1";
											if ($param_info["ResultType"] == 27) {
												$result_td_span = "4";
											}

											// NABL
											$nabl_star = "";
											$nabl_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `nabl` WHERE `nabl`>0"));
											if ($nabl_num > 0 && $report["nabl"] == 1) {
												$nabl_star = $nabl_star_symbol;
												$nabl_check_num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `nabl_test_param` WHERE `paramid`='$test_param[param_id]'"));
												if ($nabl_check_num > 0) {
													$nabl_true++;
													$nabl_star = "";
												}
											}

											$test_result["result"] = str_replace("\\", "", $test_result["result"]);
											?>
											<tr>
												<<?php echo $param_td_th; ?> class="test_name no_top_border" style="border-top: 1px solid #fff
													!important;padding: 1px 1px !important;"><?php echo $left_space . $param_info["Name"]; ?></<?php echo $param_td_th; ?>>
												<<?php echo $result_td_th; ?> class="test_result no_top_border"
													colspan="<?php echo $result_td_span; ?>" style="border-top: 1px solid #fff !important;padding:
						1px 1px !important;"><?php echo $test_result["result"]; ?></<?php echo $result_td_th; ?>>
												<?php
												if ($only_result_testid_num == 0) {
													if ($result_td_span == 1) {
														?>
														<td class="test_unit no_top_border"
															style="border-top: 1px solid #fff !important;padding: 1px 1px !important;">
															<?php echo $unit_info["unit_name"]; ?></td>
														<td class="test_ref no_top_border"
															style="border-top: 1px solid #fff !important;padding: 1px 1px !important;">
															<?php echo nl2br($normal_range["normal_range"]); ?></td>
														<td class="test_method test_method_td no_top_border"
															style="font-size: 8px !important;border-top: 1px solid #fff !important;padding: 1px 1px !important;">
															<?php if ($method["name"]) {
																echo substr($method["name"], 0, $method_max_characters) . "";
															} ?></td>
														<?php
													}
												}
												?>
											</tr>
											<?php
										}
									} else {
										echo "<tr><th colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;padding: 1px 1px !important;'>$left_space$param_info[Name]</th></tr>";
									}
								}
								$test_summary_text = "";
								$pat_test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id'"));
								if ($pat_test_summary) {
									$test_summary_text = $pat_test_summary["summary"];
								} else {
									$test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `test_summary` WHERE `testid`='$testid'"));
									if ($test_summary) {
										//$test_summary_text=$test_summary["summary"];
									}
								}
								if ($test_summary_text) {
									echo "<tr><td colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;padding: 1px 1px !important;'><br>$test_summary_text</td></tr>";
								}

								// Test Notes
								$pat_test_notes = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`>0"));
								if ($pat_test_notes["note"]) {
									echo "<tr><td colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;padding: 1px 1px !important;'><br>$pat_test_notes[note]</td></tr>";
								}
							}
						}
						?>
					</table>
				</div>
				<?php
				include ("pathology_report_footer_pdf.php");
				?>
			</div>
			<?php
			$page++;
		} // End page_no
	} // End doc
	
	// Only Test summary
	
	$result_table = "3";

	$report_doc_qry = mysqli_query($link, "SELECT DISTINCT `doc_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");

	while ($report_doc = mysqli_fetch_array($report_doc_qry)) {
		$doc_id = $report_doc["doc_id"];

		$report_page_qry = mysqli_query($link, "SELECT DISTINCT `page_no` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");
		while ($report_page = mysqli_fetch_array($report_page_qry)) {
			$page_no = $report_page["page_no"];

			$dept_info = mysqli_fetch_array(mysqli_query($link, "SELECT `id`,`name` FROM `test_department` WHERE `id` IN(SELECT `type_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no')"));
			$type_id = $dept_info["type_id"];

			if ($page > 1) {
				echo '<div class="pagebreak"></div>';
			}

			$sample_names = "";
			$sample_qry = mysqli_query($link, "SELECT DISTINCT a.`SampleId` FROM `TestSample` a, `pathology_report_print` b WHERE a.`TestId`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");

			while ($samples = mysqli_fetch_array($sample_qry)) {
				$sample_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Sample` WHERE `ID`='$samples[SampleId]'"));

				$sample_names .= $sample_info["Name"] . ",";
			}

			// Sample Collection Time
			$sample_collection = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `phlebo_sample` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if ($sample_collection) {
				$sample_collection_date = $sample_collection["date"];
				$sample_collection_time = $sample_collection["time"];
			} else {
				$sample_collection_date = $pat_reg["date"];
				$sample_collection_time = $pat_reg["time"];
			}

			// Sample Receive Time
			$sample_receive = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `lab_sample_receive` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if ($sample_receive) {
				$sample_receive_date = $sample_receive["date"];
				$sample_receive_time = $sample_receive["time"];
			} else if ($sample_collection) {
				$sample_receive_date = $sample_collection["date"];
				$sample_receive_time = $sample_collection["time"];
			} else {
				$sample_receive_date = $pat_reg["date"];
				$sample_receive_time = $pat_reg["time"];
			}

			// Reporting Time
			$report_time = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `patient_test_summary` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));

			// Report entry and checked by
			$data_entry_users = array();
			$data_checked_users = array();

			$report_entry_qry = mysqli_query($link, "SELECT DISTINCT a.`user`, a.`main_tech` FROM `patient_test_summary` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
			while ($report_entry = mysqli_fetch_array($report_entry_qry)) {
				$data_entry_users[] = $report_entry["user"];
				$data_checked_users[] = $report_entry["main_tech"];
			}

			$data_entry_users = array_unique($data_entry_users);
			$data_entry_user_ids = implode(",", $data_entry_users);

			$data_entry_names = "";
			$tech_info_qry = mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` IN($data_entry_user_ids)");
			while ($tech_info = mysqli_fetch_array($tech_info_qry)) {
				$data_entry_names .= $tech_info["name"] . ",";
			}

			$data_checked_users = array_unique($data_checked_users);
			$data_checked_user_ids = implode(",", $data_checked_users);

			$data_checked_names = "";
			$tech_info_qry = mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` IN($data_checked_user_ids)");
			while ($tech_info = mysqli_fetch_array($tech_info_qry)) {
				$data_checked_names .= $tech_info["name"] . ",";
			}
			?>
			<?php
			$br = 0;
			while ($br < $top_line_break) {
				echo "<br>";
				$br++;
			}
			?>
			<div class="container-fluid"> <!-- style="border: 2px solid #000;height: 1080px;" -->
				<div class="row">
					<?php
					//include("pathology_report_page_header_pdf.php");
					include ("pathology_report_header_pdf.php");
					?>
				</div>
				<div class="row report_div" style="<?php echo $div_height; ?>">
					<table class="table table-condensed table-no-top-border report_table">
						<?php
						$report_test_qry = mysqli_query($link, "SELECT DISTINCT `testid` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
						while ($report_test = mysqli_fetch_array($report_test_qry)) {
							$testid = $report_test["testid"];

							// Record print
							if ($view == 0) {
								mysqli_query($link, "INSERT INTO `testreport_print`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `date`, `time`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$date','$time')");
							}

							$test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));

							?>
							<tr>
								<th colspan="6" class="test_name no_top_border"
									style="text-align:center;border-top: 1px solid #fff !important;">
									<?php echo $test_info["testname"]; ?></th>
							</tr>
							<?php
							$report_qry = mysqli_query($link, "SELECT * FROM `pathology_report_print` WHERE `testid`='$testid' AND `param_id`=0 AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");

							while ($report = mysqli_fetch_array($report_qry)) {
								$test_summary_text = "";
								$pat_test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id'"));
								if ($pat_test_summary) {
									$test_summary_text = $pat_test_summary["summary"];
								} else {
									//~ $test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `test_summary` WHERE `testid`='$testid'"));
									//~ if ($test_summary) {
										//~ $test_summary_text = $test_summary["summary"];
									//~ }
								}

								if ($report["part"] > 0) {
									$position = $report["part"] - 1;

									$summary_texts = explode($page_breaker, $test_summary_text);

									$test_summary_text = $summary_texts[$position];
								}

								$summary_image_qry = mysqli_query($link, "SELECT * FROM `image_temp` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' ORDER BY `img_no`");
								$summary_image_num = mysqli_num_rows($summary_image_qry);
								$summary_image = mysqli_fetch_array($summary_image_qry);

								if ($test_summary_text && $summary_image_num > 0) {
									echo "<tr><td colspan='3' class='no_top_border' style='border-top: 1px solid #fff !important;'><br>$test_summary_text</td>";
									echo "<td colspan='2' class='no_top_border' style='border-top: 1px solid #fff !important;'><br>";
									?>
									<img src="../<?php echo $summary_image["Path"]; ?>" style="border:1px solid #CCC;height:170px;" /> <br />
									<?php
									echo "</td></tr>";
								} else if ($test_summary_text && $summary_image_num == 0) {
									echo "<tr><td colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;'><br>$test_summary_text</td></tr>";
								} else {
									echo "<tr><td colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;'><br>";
									?>
										<img src="../<?php echo $summary_image["Path"]; ?>" style="border:1px solid #CCC;height:170px;" /> <br />
										<?php
										echo "</td></tr>";
								}
							}
						}
						?>
					</table>
				</div>
				<?php
				include ("pathology_report_footer_pdf.php");
				?>
			</div>
			<?php
			$page++;
		} // End page_no
	} // End doc
	
	// Widal Test
	
	$result_table = "4";

	$report_doc_qry = mysqli_query($link, "SELECT DISTINCT `doc_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");

	while ($report_doc = mysqli_fetch_array($report_doc_qry)) {
		$doc_id = $report_doc["doc_id"];

		$report_page_qry = mysqli_query($link, "SELECT DISTINCT `page_no` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");
		while ($report_page = mysqli_fetch_array($report_page_qry)) {
			$page_no = $report_page["page_no"];

			$dept_info = mysqli_fetch_array(mysqli_query($link, "SELECT `id`,`name` FROM `test_department` WHERE `id` IN(SELECT `type_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no')"));
			$type_id = $dept_info["type_id"];

			if ($page > 1) {
				echo '<div class="pagebreak"></div>';
			}

			$sample_names = "";
			$sample_qry = mysqli_query($link, "SELECT DISTINCT a.`SampleId` FROM `TestSample` a, `pathology_report_print` b WHERE a.`TestId`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");

			while ($samples = mysqli_fetch_array($sample_qry)) {
				$sample_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Sample` WHERE `ID`='$samples[SampleId]'"));

				$sample_names .= $sample_info["Name"] . ",";
			}

			// Sample Collection Time
			$sample_collection = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `phlebo_sample` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if ($sample_collection) {
				$sample_collection_date = $sample_collection["date"];
				$sample_collection_time = $sample_collection["time"];
			} else {
				$sample_collection_date = $pat_reg["date"];
				$sample_collection_time = $pat_reg["time"];
			}

			// Sample Receive Time
			$sample_receive = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `lab_sample_receive` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if ($sample_receive) {
				$sample_receive_date = $sample_receive["date"];
				$sample_receive_time = $sample_receive["time"];
			} else if ($sample_collection) {
				$sample_receive_date = $sample_collection["date"];
				$sample_receive_time = $sample_collection["time"];
			} else {
				$sample_receive_date = $pat_reg["date"];
				$sample_receive_time = $pat_reg["time"];
			}

			// Reporting Time
			$report_time = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `widalresult` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));

			// Report entry and checked by
			$data_entry_users = array();
			$data_checked_users = array();

			$report_entry_qry = mysqli_query($link, "SELECT DISTINCT a.`user`, a.`main_tech` FROM `patient_test_summary` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
			while ($report_entry = mysqli_fetch_array($report_entry_qry)) {
				$data_entry_users[] = $report_entry["user"];
				$data_checked_users[] = $report_entry["main_tech"];
			}

			$report_entry_qry = mysqli_query($link, "SELECT DISTINCT a.`v_User`, a.`main_tech` FROM `widalresult` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
			while ($report_entry = mysqli_fetch_array($report_entry_qry)) {
				$data_entry_users[] = $report_entry["v_User"];
				$data_checked_users[] = $report_entry["main_tech"];
			}

			$data_entry_users = array_unique($data_entry_users);
			$data_entry_user_ids = implode(",", $data_entry_users);

			$data_entry_names = "";
			$tech_info_qry = mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` IN($data_entry_user_ids)");
			while ($tech_info = mysqli_fetch_array($tech_info_qry)) {
				$data_entry_names .= $tech_info["name"] . ",";
			}

			$data_checked_users = array_unique($data_checked_users);
			$data_checked_user_ids = implode(",", $data_checked_users);

			$data_checked_names = "";
			$tech_info_qry = mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` IN($data_checked_user_ids)");
			while ($tech_info = mysqli_fetch_array($tech_info_qry)) {
				$data_checked_names .= $tech_info["name"] . ",";
			}
			?>
			<?php
			$br = 0;
			while ($br < $top_line_break) {
				echo "<br>";
				$br++;
			}
			?>
			<div class="container-fluid"> <!-- style="border: 2px solid #000;height: 1080px;" -->
				<div class="row">
					<?php
					//include("pathology_report_page_header_pdf.php");
					include ("pathology_report_header_pdf.php");
					?>
				</div>
				<div class="row report_div" style="<?php echo $div_height; ?>">
					<table class="table table-condensed table-no-top-border report_table">

						<?php
						$report_test_qry = mysqli_query($link, "SELECT DISTINCT `testid` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
						while ($report_test = mysqli_fetch_array($report_test_qry)) {
							$testid = $report_test["testid"];

							// Record print
							if ($view == 0) {
								mysqli_query($link, "INSERT INTO `testreport_print`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `date`, `time`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$date','$time')");
							}

							$test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));

							?>
							<tr>
								<th colspan="5" class="test_name no_top_border"
									style="text-align:center;border-top: 1px solid #fff !important;">
									<?php echo $test_info["testname"]; ?></th>
							</tr>
							<?php

							$report_qry = mysqli_query($link, "SELECT * FROM `pathology_report_print` WHERE `testid`='$testid' AND `param_id`=0 AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
							$report_num = mysqli_num_rows($report_qry);
							if ($report_num > 0) {
								$w1 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `doc`='$doc_id' AND `slno`='1'"));
								$w2 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `doc`='$doc_id' AND `slno`='2'"));
								$w3 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `doc`='$doc_id' AND `slno`='3'"));
								$w4 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `doc`='$doc_id' AND `slno`='4'"));
								?>
								<tr>
									<th colspan="5">
										<table class="table table-condensed table-bordered">
											<tr class="tr_border">
												<td><strong>Dilution:</strong></td>
												<td><strong>1:20</strong></td>
												<td><strong>1:40</strong></td>
												<td><strong>1:80</strong></td>
												<td><strong>1:160</strong></td>
												<td><strong>1:320</strong></td>
												<td><strong>1:640</strong></td>
											</tr>
											<tr>
												<td><strong>Antigen 'O'</strong></td>
												<td><?php echo $w1[F1] ?></td>
												<td><?php echo $w1[F2] ?></td>
												<td><?php echo $w1[F3] ?></td>
												<td><?php echo $w1[F4] ?></td>
												<td><?php echo $w1[F5] ?></td>
												<td><?php echo $w1[F6] ?></td>
											</tr>
											<tr>
												<td><strong>Antigen 'H'</strong></td>
												<td><?php echo $w2[F1] ?></td>
												<td><?php echo $w2[F2] ?></td>
												<td><?php echo $w2[F3] ?></td>
												<td><?php echo $w2[F4] ?></td>
												<td><?php echo $w2[F5] ?></td>
												<td><?php echo $w2[F6] ?></td>
											</tr>
											<tr>
												<td><strong>Antigen 'A(H)'</strong></td>
												<td><?php echo $w3[F1] ?></td>
												<td><?php echo $w3[F2] ?></td>
												<td><?php echo $w3[F3] ?></td>
												<td><?php echo $w3[F4] ?></td>
												<td><?php echo $w3[F5] ?></td>
												<td><?php echo $w3[F6] ?></td>
											</tr>
											<tr>
												<td><strong>Antigen 'B(H)'</strong></td>
												<td><?php echo $w4[F1] ?></td>
												<td><?php echo $w4[F2] ?></td>
												<td><?php echo $w4[F3] ?></td>
												<td><?php echo $w4[F4] ?></td>
												<td><?php echo $w4[F5] ?></td>
												<td><?php echo $w4[F6] ?></td>
											</tr>
											<tr>
												<td><strong>IMPRESSION</strong></td>
												<td colspan="6"><?php echo nl2br($w4[DETAILS]); ?></td>
											</tr>
										</table>
									</th>
								</tr>
								<?php
								$test_summary_text = "";
								$pat_test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id'"));
								if ($pat_test_summary) {
									$test_summary_text = $pat_test_summary["summary"];
								} else {
									//~ $test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `test_summary` WHERE `testid`='$testid'"));
									//~ if ($test_summary) {
										//~ $test_summary_text = $test_summary["summary"];
									//~ }
								}
								if ($test_summary_text) {
									echo "<tr><td colspan='5' class='no_top_border' style='border-top: 1px solid #fff !important;'><br>$test_summary_text</td></tr>";
								}
							}
						}
						?>
					</table>
				</div>
				<?php
				include ("pathology_report_footer_pdf.php");
				?>
			</div>
			<?php
			$page++;
		} // End page_no
	} // End doc
	

	// Culture Test
	
	$result_table = "5";

	$report_doc_qry = mysqli_query($link, "SELECT DISTINCT `doc_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");

	while ($report_doc = mysqli_fetch_array($report_doc_qry)) {
		$doc_id = $report_doc["doc_id"];

		$report_page_qry = mysqli_query($link, "SELECT DISTINCT `page_no` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");
		while ($report_page = mysqli_fetch_array($report_page_qry)) {
			$page_no = $report_page["page_no"];

			$dept_info = mysqli_fetch_array(mysqli_query($link, "SELECT `id`,`name` FROM `test_department` WHERE `id` IN(SELECT `type_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no')"));
			$type_id = $dept_info["type_id"];

			if ($page > 1) {
				echo '<div class="pagebreak"></div>';
			}

			$sample_names = "";
			$sample_qry = mysqli_query($link, "SELECT DISTINCT a.`SampleId` FROM `TestSample` a, `pathology_report_print` b WHERE a.`TestId`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");

			while ($samples = mysqli_fetch_array($sample_qry)) {
				$sample_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `Sample` WHERE `ID`='$samples[SampleId]'"));

				$sample_names .= $sample_info["Name"] . ",";
			}

			// Sample Collection Time
			$sample_collection = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `phlebo_sample` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if ($sample_collection) {
				$sample_collection_date = $sample_collection["date"];
				$sample_collection_time = $sample_collection["time"];
			} else {
				$sample_collection_date = $pat_reg["date"];
				$sample_collection_time = $pat_reg["time"];
			}

			// Sample Receive Time
			$sample_receive = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `lab_sample_receive` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if ($sample_receive) {
				$sample_receive_date = $sample_receive["date"];
				$sample_receive_time = $sample_receive["time"];
			} else if ($sample_collection) {
				$sample_receive_date = $sample_collection["date"];
				$sample_receive_time = $sample_collection["time"];
			} else {
				$sample_receive_date = $pat_reg["date"];
				$sample_receive_time = $pat_reg["time"];
			}

			// Reporting Time
			$report_time = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `testresults` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			if (!$report_time) {
				$report_time = mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `patient_test_summary` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
			}

			// Report entry and checked by
			$data_entry_users = array();
			$data_checked_users = array();

			$report_entry_qry = mysqli_query($link, "SELECT DISTINCT a.`tech`, a.`main_tech` FROM `testresults` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
			while ($report_entry = mysqli_fetch_array($report_entry_qry)) {
				$data_entry_users[] = $report_entry["tech"];
				$data_checked_users[] = $report_entry["main_tech"];
			}

			$report_entry_qry = mysqli_query($link, "SELECT DISTINCT a.`user`, a.`main_tech` FROM `patient_test_summary` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
			while ($report_entry = mysqli_fetch_array($report_entry_qry)) {
				$data_entry_users[] = $report_entry["user"];
				$data_checked_users[] = $report_entry["main_tech"];
			}

			$data_entry_users = array_unique($data_entry_users);
			$data_entry_user_ids = implode(",", $data_entry_users);

			$data_entry_names = "";
			$tech_info_qry = mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` IN($data_entry_user_ids)");
			while ($tech_info = mysqli_fetch_array($tech_info_qry)) {
				$data_entry_names .= $tech_info["name"] . ",";
			}

			$data_checked_users = array_unique($data_checked_users);
			$data_checked_user_ids = implode(",", $data_checked_users);

			$data_checked_names = "";
			$tech_info_qry = mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id` IN($data_checked_user_ids)");
			while ($tech_info = mysqli_fetch_array($tech_info_qry)) {
				$data_checked_names .= $tech_info["name"] . ",";
			}
			?>
			<?php
			$br = 0;
			while ($br < $top_line_break) {
				echo "<br>";
				$br++;
			}
			?>
			<div class="container-fluid"> <!-- style="border: 2px solid #000;height: 1080px;" -->
				<div class="row">
					<?php
					//include("pathology_report_page_header_pdf.php");
					include ("pathology_report_header_pdf.php");
					?>
				</div>
				<div class="row report_div" style="<?php echo $div_height; ?>">
					<table class="table table-condensed table-no-top-border report_table">
						<?php
						$report_test_qry = mysqli_query($link, "SELECT DISTINCT `testid` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testall) AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
						while ($report_test = mysqli_fetch_array($report_test_qry)) {
							$testid = $report_test["testid"];

							// Record print
							if ($view == 0) {
								mysqli_query($link, "INSERT INTO `testreport_print`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `date`, `time`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$date','$time')");
							}

							$test_info = mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));

							?>
							<tr>
								<th colspan="6" class="test_name no_top_border"
									style="text-align:center;border-top: 1px solid #fff !important;">
									<?php echo $test_info["testname"]; ?></th>
							</tr>
							<?php
							$report_qry = mysqli_query($link, "SELECT * FROM `pathology_report_print` WHERE `testid`='$testid' AND `param_id`=0 AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");

							while ($report = mysqli_fetch_array($report_qry)) {
								$iso_no = $report["part"];

								$cult_result_qry = mysqli_query($link, "SELECT a.`result`,a.`paramid`,b.`Name` FROM `testresults` a,`Parameter_old` b WHERE a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND a.`testid`='$testid' AND a.`iso_no`='$iso_no' AND a.`paramid`=b.`ID` AND b.`ResultOptionID`!='68' AND b.ID NOT IN(312) ORDER BY a.`sequence`"); // AND b.ID NOT IN(311,312)
								while ($cult_result = mysqli_fetch_array($cult_result_qry)) {
									$cult_result_colony_power = "";
									if ($cult_result["paramid"] == 311) // COLONY COUNT
									{
										$cult_result_colony_power = mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `testresults` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `iso_no`='$iso_no' AND `paramid`='312'")); // POWER
									}
									?>
									<tr>
										<th style="width: 30%;border-top: 1px solid #fff !important;" class="no_top_border">
											<?php echo $cult_result["Name"]; ?></th>
										<td colspan="5" style="text-align:left;border-top: 1px solid #fff !important;"
											class="no_top_border">
											<b>: </b>
											<?php echo $cult_result["result"]; ?>
											<?php if ($cult_result_colony_power["result"]) {
												echo "<sup>" . $cult_result_colony_power["result"] . "</sup>";
											} ?>
										</td>
									</tr>
									<?php
								}

								if ($iso_no > 1) {
									?>
									<tr>
										<th style="width: 30%;border-top: 1px solid #fff !important;" class="no_top_border">Comments</th>
										<td colspan="5" style="text-align:left;border-top: 1px solid #fff !important;"
											class="no_top_border">
											<b>: </b>
											This is for testing ISO No. <?php echo $iso_no; ?>
										</td>
									</tr>
									<?php
								}

								$growth_qry = mysqli_query($link, "SELECT a.`paramid`,a.`result`,b.`Name` FROM `testresults` a,`Parameter_old` b WHERE a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND a.`testid`='$testid' AND a.`iso_no`='$iso_no' AND a.`paramid`=b.`ID` AND b.`ResultOptionID`='68' ORDER BY b.`Name` ASC");
								$growth_num = mysqli_num_rows($growth_qry);
								if ($growth_num > 0) // if Growth
								{
									?>
									<tr>
										<th colspan="6" style="border-top: none;"><br></th>
									</tr>
									<tr>
										<td colspan="6" style="border-top: none;">
											<table class="table table-condensed table-bordered cult_table"
												style="border-bottom: 1px solid #000;border-right: 1px solid #000;">
												<tr>
													<th style="padding: 1px 1px !important;font-size: 11px !important;width: 200px !important;">ANTIMICROBIAL</th>
													<th class="mic_cls" style="text-align:center;font-size: 11px !important;">MIC</th>
													<th style="padding: 1px 1px !important;font-size: 11px !important;">INTERPRETATION</th>
													<th style="padding: 1px 1px !important;font-size: 11px !important;width: 200px !important;">ANTIMICROBIAL</th>
													<th class="mic_cls" style="text-align:center;font-size: 11px !important;">MIC</th>
													<th style="padding: 1px 1px !important;font-size: 11px !important;">INTERPRETATION</th>
												</tr>
												<tr>
													<?php
													$n = 0;
													$i = 0;
													$mic_chk = 0;
													while ($growth_val = mysqli_fetch_array($growth_qry)) {
														$result = $growth_val["result"];

														if (strpos($result,"RESISTANT (") !== false || strpos($result,"R (") !== false || strpos($result,"r (") !== false) 
														{
															$interpretation="RESISTANT";
														}
														if (strpos($result,"SENSITIVE (") !== false || strpos($result,"S (") !== false || strpos($result,"s (") !== false)
														{
															$interpretation="SENSITIVE";
														}
														if (strpos($result,"INTERMEDIATE (") !== false || strpos($result,"I (") !== false || strpos($result,"i (") !== false) 
														{
															$interpretation="INTERMEDIATE";
														}

														$results = explode(" (", $result);
														$resultz = explode(")", $results[1]);
														$mic_value = $resultz[0];
														if ($mic_value) {
															$mic_chk++;
														}
														?>
														<td style="padding: 1px 1px !important;font-size: 11px !important;"><?php echo $growth_val["Name"]; ?></td>
														<td class="mic_cls" style="text-align:center;padding: 1px 1px !important;font-size: 11px !important;"><?php echo $mic_value; ?></td>
														<td style="padding: 1px 1px !important;font-size: 11px !important;"><?php echo $interpretation; ?></td>
														<?php
														$i++;
														$n++;
														if ($i == 2) {
															$i = 0;

															echo "</tr>";
															echo "<tr>";
														}
														if ($n == $growth_num && $i != 0) {
															echo "<td></td>";
															echo "<td></td>";
															echo "<td></td>";
															echo "</tr>";
														}
													}
													if ($mic_chk == 0) {
														echo "<script>$('.mic_cls').remove();</script>";
													}
													?>
											</table>
										</td>
									</tr>
									<?php
								}

								$result_note = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`>0"));
								if ($result_note["note"]) {
									echo "<tr><td colspan='5' style='border-top: none;'><br>N:B:- $result_note[note]</td></tr>";
								}

								$test_summary_text = "";
								$pat_test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id'"));
								if ($pat_test_summary) {
									$test_summary_text = $pat_test_summary["summary"];
								} else {
									//~ $test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `test_summary` WHERE `testid`='$testid'"));
									//~ if ($test_summary) {
										//~ $test_summary_text=$test_summary["summary"];
									//~ }
								}
								if ($test_summary_text) {
									echo "<tr><td colspan='5' style='border-top: none;'><br>$test_summary_text</td></tr>";
								}
							}
						}
						?>
					</table>
				</div>
				<?php
				include ("pathology_report_footer_pdf.php");
				?>
			</div>
			<?php
			$page++;
		} // End page_no
	} // End doc
	?>
	<span id="user" style="display:none;"><?php echo $c_user; ?></span>
	<div id="loader"></div>
</body>
<input type="hidden" id="uhid" value="<?php echo $uhid; ?>">
<input type="hidden" id="opd_id" value="<?php echo $opd_id; ?>">
<input type="hidden" id="ipd_id" value="<?php echo $ipd_id; ?>">
<input type="hidden" id="batch_no" value="<?php echo $batch_no; ?>">
<input type="hidden" id="tests" value="<?php echo $tests; ?>">
<input type="hidden" id="bill_id" value="<?php echo $bill_id; ?>">
<input type="hidden" id="view" value="<?php echo $view; ?>">

</html>
<style>
	* {
		line-height: 16px !important;
	}

	.extra_lines {
		//line-height: 10px !important;
	}

	li {
		line-height: 10px !important;
	}

	h3 {
		margin: 0;
	}

	h4 {
		margin: 0;
	}

	.patient_header {
		font-size: 12px !important;
		border-bottom: 1.5px solid #000;
	}

	.span_doc {
		margin-left: 0 !important;
		width:
			<?php echo $span_doc_width; ?>
			% !important;
		font-size: 10px !important;
		line-height: 10px !important;
	}

	.span_nabl {
		margin-left: 0 !important;
		margin-right: 0 !important;
		width: 7% !important;
	}

	.report_footer {
		//position: fixed;
		//bottom: 50px;
		width: 100%;

		//position: relative;
		//top: 700px;
	}

	.table {
		margin-bottom: 0 !important;
	}

	.report_table th,
	.report_table td {
		padding: 1px 1px !important;
		font-size: 13px !important;
	}

	.report_header {
		border-bottom: 1.5px solid #000 !important;
	}
	.cult_table th, .cult_table td{
		border-top: 1px solid #000 !important;
		border-left: 1px solid #000 !important;
	}

	.checked_by {
		font-size: 11px !important;
	}

	.checked_by_table th,
	.checked_by_table td,
	.patient_header th,
	.patient_header td {
		padding: 0px !important;
	}

	.table-no-top-border th,
	.table-no-top-border td {
		border-top: 1px solid #000;
	}

	.no_top_border {
		border-top: 1px solid #fff !important;
	}

	@page {
		margin-top: 0cm;
		margin-bottom: 0cm;
		//margin-left:0.8cm;
		//margin-right:0.1cm;
	}

	.test_method {
		//display:none;
	}

	@media print {
		.pagebreak {
			clear: both;
			page-break-after: always;
		}

		<?php
		if ($view > 0) {
			?>
			* {
				display: none;
			}

			<?php
		}
		?>
	}
</style>
<script>

	$(document).ready(function () {
		$("#loader").hide();
		//$(".test_method").remove();

		if ($("#view").val() == 0) {
			//window.print();
		}
	});

	function save_print_test(tst, uhid, opd_id, ipd_id, batch_no, bill_id) {
		window.opener.load_test_detail(uhid, bill_id, batch_no);
		setTimeout(function () {
			window.close();
		}, 100);
	}
	function close_window(e) {
		if (e.which == 27) {
			window.close();
		}
	}
</script>
<?php
// Delete from Temp
mysqli_query($link, "DELETE FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr'");
mysqli_query($link, "DELETE FROM `pathology_report_print_sequence` WHERE `user`='$c_user' AND `ip_addr`='$ip_addr'");
?>
<?php
$rg = str_replace("/", "_", $bill_id);
$gen_time = date('H_i_s');

$output = ob_get_contents();

include ("../../MPDF/mpdf60/mpdf.php");

$mpdf = new mPDF('c', 'A4', '', '', 2, 0, 0, 0, 2, 5);

$mpdf->mirrorMargins = 1;

$mpdf->SetWatermarkImage('../../images/watermark.jpg', 0.15, 'F');
$mpdf->showWatermarkImage = true;

$mpdf->WriteHTML($output);

ob_clean();
//$mpdf->Output($file_name, 'I');

if ($whatsapp == 1) {
	$pdf_dir = "../../pdf_files";
	if (!file_exists($pdf_dir)) {
		mkdir($pdf_dir, 0777, true);
	}

	$pdf_dir_Y = $pdf_dir . "/" . date("Y", strtotime($pat_reg["date"]));
	if (!file_exists($pdf_dir_Y)) {
		mkdir($pdf_dir_Y, 0777, true);
	}

	$pdf_dir_Y_m = $pdf_dir_Y . "/" . date("m", strtotime($pat_reg["date"]));
	if (!file_exists($pdf_dir_Y_m)) {
		mkdir($pdf_dir_Y_m, 0777, true);
	}

	$pdf_dir_Y_m_d = $pdf_dir_Y_m . "/" . date("d", strtotime($pat_reg["date"]));
	if (!file_exists($pdf_dir_Y_m_d)) {
		mkdir($pdf_dir_Y_m_d, 0777, true);
	}

	$pdf_dir_Y_m_d_index = $pdf_dir_Y_m_d . "/index.html";
	if (!file_exists($pdf_dir_Y_m_d_index)) {
		touch($pdf_dir_Y_m_d_index);
	}

	$file_name = $pdf_dir_Y_m_d . "/" . $rg . ".pdf";
	$m_chk = "F"; //--Save File--//
} else {
	$file_name = $rg . "_" . $gen_time . ".pdf";
	$m_chk = "I"; //---ONly Generates--//
}

$mpdf->Output($file_name, $m_chk);
?>
