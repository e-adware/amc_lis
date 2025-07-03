<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date);
	$new_date = date('y', $timestamp);
	return $new_date;
}

function gen_bar($param)  //------------Barcode Updation---------------//
{
	if ($param == 21 || $param == 645) {
		$bar_id = "F## Fasting";
		return $bar_id;
	} else if ($param == 293) {
		$bar_id = "A## GTT 1|2 hour";
		return $bar_id;
	} else if ($param == 294) {
		$bar_id = "B## GTT 1 hr";
		return $bar_id;
	} else if ($param == 295) {
		$bar_id = "C## GTT 1 1|2 hr";
		return $bar_id;
	} else if ($param == 296) {
		$bar_id = "D## GTT 2 hr";
		return $bar_id;
	} else if ($param == 297) {
		$bar_id = "E## GTT 2 1|5 hr";
		return $bar_id;
	} else if ($param == 588) {
		$bar_id = "A## GTT 1 hr";
		return $bar_id;
	} else if ($param == 589) {
		$bar_id = "B## GTT 2 hr";
		return $bar_id;
	} else if ($param == 298) {
		$bar_id = "C## GTT 3 hr";
		return $bar_id;
	} else if ($param == 646) {
		$bar_id = "A## GTT 1 hr";
		return $bar_id;
	} else if ($param == 647) {
		$bar_id = "B## GTT 2 hr";
		return $bar_id;
	} else if ($param == 564) {
		$bar_id = "B## GTT 2 hr";
		return $bar_id;
	} else {
		$bar_id = "U## Other";
		return $bar_id;
	}

}

function calculateDOB($years, $months, $days)
{
	$currentDate = new DateTime();
	$dob = clone $currentDate;
	$dob->sub(new DateInterval("P{$years}Y{$months}M{$days}D"));
	return $dob->format('Y-m-d');
}

/*
function convert_date($date)
{
	 if($date)
	 {
		 $timestamp = strtotime($date); 
		 $new_date = date('d-M-Y', $timestamp);
		 return $new_date;
	 }
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
*/
$date = date("Y-m-d"); // important
$date1 = date("Y-m-d");
$time = date("H:i:s");

$type = $_POST['type'];

if ($type == 1) {
	$val = trim(mysqli_real_escape_string($link, $_POST['val']));
	if ($val != '') {
		$test = mysqli_query($link, "select * from testmaster where type_id in(20,23,26,29,33) and testname like '%$val%' order by sequence,testname");
	} else {
		$test = mysqli_query($link, "select * from testmaster where type_id in (20,23,26,29,33) order by sequence,testname");
	}
	?>
	<div id='tst_list'>

		<?php
		$i = 1;
		while ($tst = mysqli_fetch_array($test)) {
			?>
			<div class="tst_span <?php echo strtolower($tst[testname]); ?> <?php echo $tst[type_id]; ?>"
				id="div_<?php echo $i; ?>" onclick="select_check(<?php echo $i; ?>)"> <input type="checkbox"
					value="<?php echo $tst[testid]; ?>" id="check_<?php echo $i; ?>" class="tst_check" />
				<label><span></span></label> <?php echo $tst[testname]; ?>
			</div>
			<?php
			$i++;
		}
		?>
	</div> <?php
} else if ($type == 2) {
	//print_r($_POST);
	//exit;

	$p_type = $_POST['p_type'];
	$hosp_no = mysqli_real_escape_string($link, $_POST['hosp_no']);
	$bill_no = mysqli_real_escape_string($link, $_POST['bill_no']);
	$name = mysqli_real_escape_string($link, $_POST['name']);
	$age = mysqli_real_escape_string($link, $_POST['age']);
	$sex = $_POST['sex'];
	$age_type = $_POST['age_type'];
	$date_serial = $_POST['date_serial'];
	$ward = $_POST['ward'];
	$dept = $_POST['dept'];

	$address = mysqli_real_escape_string($link, $_POST['add']);
	$phone = $_POST['phone'];
	$dis = $_POST['dis'];

	$recp_samp = $_POST['recp_samp']; //-----sample reception---//

	$tst = $_POST['tst'];

	$pat_type = $_POST['pat_type'];
	$pat_type_covid = $_POST['pat_type_covid'];
	if (!$pat_type_covid) {
		$pat_type_covid = 0;
	}
	$pat_type_nrhm = $_POST['pat_type_nrhm'];
	if (!$pat_type_nrhm) {
		$pat_type_nrhm = 0;
	}
	$samp_no = $_POST['samp_no'];
	$free = $_POST['free'];
	$auth = $_POST['auth'];
	$auth_disc = $_POST['auth_disc'];

	//$entry_type=$_POST['entry_type'];

	$years = 0;
	$months = 0;
	$days = 0;
	if ($age_type == "Years") {
		$years = $age;
	}
	if ($age_type == "Months") {
		$months = $age;
	}
	if ($age_type == "Days") {
		$days = $age;
	}
	$dob = calculateDOB($years, $months, $days);

	$val = $_POST['val'];
	$user = $_POST['user'];

	if ($p_type == 1) {
		$prefix = "OPD_BIO/";
	} else if ($p_type == 2) {
		$prefix = "IPD_BIO/";
		//$p_type='';
	} else if ($p_type == 3) {
		$prefix = "NRHM/";
		//$p_type='';
	} else if ($p_type == 4) {
		$prefix = "EC/";
		//$p_type='';
		$nr = $_POST[nr];
	} else if ($p_type == 5) {
		$prefix = "NRM_EMRG/";
	}

	$arr = array();
	if ($val == "save") {
		//-----------------Patient ID---------------//

		include("patient_id_generator.php");
		$new_patient_id = trim($new_patient_id);
		//----------------------------------------//

		$opd_idds = 100;

		$date_str = explode("-", $date);
		$dis_year = $date_str[0];
		$dis_month = $date_str[1];
		$dis_year_sm = convert_date_only_sm_year($date);

		$c_m_y = $dis_year . "-" . $dis_month;

		$current_month = date("Y-m");
		if ($c_m_y < $current_month) {
			$opd_id_qry = mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
			$opd_id_num = $opd_id_qry["tot"];

			$opd_id_qry_cancel = mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
			$opd_id_cancel_num = $opd_id_qry_cancel["tot"];

			$pat_tot_num = $opd_id_num + $opd_id_cancel_num;

			if ($pat_tot_num == 0) {
				$opd_idd = $opd_idds + 1;
			} else {
				$opd_idd = $opd_idds + $pat_tot_num + 100;
			}
			$opd_id = $opd_idd . "/" . $dis_month . $dis_year_sm;
		} else {
			$c_data = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
			if (!$c_data) {
				mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
			}

			mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$new_patient_id','1','$user','$date','$time') ");

			$last_slno = mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));

			$last_slno = $last_slno["slno"];
			$opd_idd = $opd_idds + $last_slno;
			$opd_id = $opd_idd . "/" . $dis_month . $dis_year_sm;
		}

		if (mysqli_query($link, "INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `ward`, `dept`, `disease_id`,`hosp_no`, `bill_no`, `urgent`, `date`, `time`, `user`, `type`, `type_prefix`, `sample_serial`, `pat_type`, `free`, `auth`, `auth_disc`, `date_serial`, `emer_nr`, `nr_pat_type`, `nr_covid`) VALUES ('$new_patient_id','$opd_id','$ward','$dis','$dept','$hosp_no','$bill_no','0','$date','$time','$user','$p_type','$prefix','$samp_no','$pat_type','$free','$auth','$auth_disc','$date_serial','0','$pat_type_nrhm','$pat_type_covid') ")) {
			mysqli_query($link, "INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `date`, `time`, `user`) VALUES ('$new_patient_id','$hosp_no','$name','$sex','$dob','$age','$age_type','$phone','$address','$date','$time','$user')");

			$test = explode("@koushik@", $tst);
			foreach ($test as $test) {
				if ($test) {
					$smpl = mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test' "));
					if (!$smpl['SampleId']) {
						$smpl['SampleId'] = 0;
					}
					$vc = 0;
					$tstRate = mysqli_fetch_array(mysqli_query($link, "SELECT `rate` FROM `testmaster` WHERE `testid`='$test'"));
					$rate = $tstRate['rate'];

					mysqli_query($link, "INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$new_patient_id','$opd_id','','1','$test','$smpl[SampleId]','$rate','0','','0','$date','$time','$user','2')");
				}
			}

			// mysqli_query($link, "delete from patient_disease_details where patient_id='$new_patient_id' and opd_id='$opd_id'");
			if ($dis > 0) {
				// mysqli_query($link, "insert into patient_disease_details(patient_id,opd_id,disease_id,user,date,time) value('$new_patient_id','$opd_id','$dis','$user','$date','$time')");
			}

			$arr['pid'] = $new_patient_id;
			$arr['opd'] = $opd_id;
			$arr['bch'] = 1;
			$arr['response'] = 1;
			$arr['msg'] = "Saved";
		} else {
			$tt = "INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `ward`, `disease_id`,`dept`, `hosp_no`, `bill_no`, `urgent`, `date`, `time`, `user`, `type`, `type_prefix`, `sample_serial`, `pat_type`, `free`, `auth`, `auth_disc`, `date_serial`, `emer_nr`, `nr_pat_type`, `nr_covid`) VALUES ('$new_patient_id','$opd_id','$ward','$dis','$dept','$hosp_no','$bill_no','0','$date','$time','$user','$p_type','$prefix','$samp_no','$pat_type','$free','$auth','$auth_disc','$date_serial','0','$pat_type_nrhm','$pat_type_covid') ";
			$arr['pid'] = "";
			$arr['opd'] = "";
			$arr['bch'] = "";
			$arr['response'] = 0;
			$arr['msg'] = "Error $tt";
		}

	} else if ($val == "Update") {
		$opd_id = $_POST['opd_id'];

		$emp = mysqli_fetch_array(mysqli_query($link, "select * from employee where emp_id='$user'"));

		$det = mysqli_fetch_array(mysqli_query($link, "select * from uhid_and_opdid where opd_id='$opd_id'"));

		//----Check Old Patient Info/Test Info---//
		$old_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$det[patient_id]'"));

		if ($old_info[name] != $name || $old_info[age] != $age || $old_info[age_type] != $age_type || $old_info[sex] != $sex) {
			mysqli_query($link, "INSERT INTO `patient_info_upd`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$old_info[patient_id]', '$old_info[hosp_no]', '$old_info[name]', '$old_info[sex]', '$old_info[dob]', '$old_info[age]', '$old_info[age_type]', '$old_info[phone]', '$old_info[address]', '$user', '$date', '$time')");
		}

		$updNo = mysqli_fetch_array(mysqli_query($link, "SELECT MAX(`upd`) AS `max` FROM `patient_test_details_update` WHERE patient_id='$det[patient_id]' and opd_id='$opd_id'"));
		$upd = ($updNo['max'] + 1);

		$old_test = mysqli_query($link, "select * from patient_test_details where patient_id='$det[patient_id]' and opd_id='$opd_id'");
		while ($old_tst = mysqli_fetch_array($old_test)) {
			mysqli_query($link, "INSERT INTO `patient_test_details_update`(`patient_id`, `opd_id`, `hosp_no`, `testid`, `sample_id`, `test_rate`, `date`, `time`, `user`, `upd`) VALUES ('$old_tst[patient_id]', '$old_tst[opd_id]', '$old_tst[hosp_no]', '$old_tst[testid]', '$old_tst[sample_id]', '$old_tst[test_rate]', '$date', '$time', '$user','$upd')");
		}

		//------------------------------//

		mysqli_query($link, "update uhid_and_opdid set type='$pat_type', hosp_no='$hosp_no',pat_type='$free',ward='$ward',dept='$dept', disease_id='$dis' where patient_id='$det[patient_id]' and opd_id='$opd_id'");

		$chk_info = mysqli_num_rows(mysqli_query($link, "select * from patient_info where patient_id='$det[patient_id]'"));
		if ($chk_info > 0) {
			if ($emp[levelid] == 1 || $emp[levelid] == 13) {
				mysqli_query($link, "update patient_info set name='$name',sex='$sex',age='$age',age_type='$age_type',hosp_no='$hosp_no',phone='$phone',address='$address' where patient_id='$det[patient_id]'");
			}
		} else {
			mysqli_query($link, "INSERT INTO `patient_info`(`patient_id`, `hosp_no`, `name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `user`, `date`, `time`) VALUES ('$det[patient_id]','$hosp_no','$name','$sex','','$age','$age_type','$phone','$address','$user','$date','$time')");
		}

		if ($emp[levelid] == 1 || $emp[levelid] == 13) {
			mysqli_query($link, "update patient_info set name='$name',sex='$sex',age='$age',age_type='$age_type' where hosp_no='$hosp_no'");
		}

		if ($emp[levelid] == 1 || $emp[levelid] == 13) {
			mysqli_query($link, "delete from patient_test_details where patient_id='$det[patient_id]' and opd_id='$opd_id'");
			//mysqli_query($link,"delete from phlebo_sample where patient_id='$det[patient_id]' and opd_id='$opd_id'");

			$date = $det[date];
			$time = $det[time];

			$test = explode("@koushik@", $tst);
			foreach ($test as $test) {
				if ($test) {
					$smpl = mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test' "));
					if (!$smpl['SampleId']) {
						$smpl['SampleId'] = 0;
					}
					$tstRate = mysqli_fetch_array(mysqli_query($link, "SELECT `rate` FROM `testmaster` WHERE `testid`='$test'"));
					$rate = $tstRate['rate'];

					mysqli_query($link, "INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `dept_serial`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$det[patient_id]','$opd_id','','1','$test','$smpl[SampleId]','$rate','0','','0','$date','$time','$user','2')");
				}
			}


			mysqli_query($link, "delete from patient_disease_details where patient_id='$det[patient_id]' and opd_id='$opd_id'");
			if ($dis > 0) {
				mysqli_query($link, "insert into patient_disease_details(patient_id,opd_id,disease_id,user,date,time) value('$det[patient_id]','$opd_id','$dis','$user','$date','$time')");
			}
		}
		$arr['pid'] = $det['patient_id'];
		$arr['opd'] = $opd_id;
		$arr['bch'] = 1;
		$arr['response'] = 1;
		$arr['msg'] = "Updated";
	}
	echo json_encode($arr);
} else if ($type == 3) {
	$hosp_no = mysqli_real_escape_string($link, $_POST['hosp_no']);

	$info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where hosp_no='$hosp_no'"));

	if ($info[slno] > 0) {
		echo $info[name] . "@kk@" . $info[sex] . "@kk@" . $info[age] . "@kk@" . $info[age_type] . "@";
	}
} else if ($type == 4) {
	$bill_no = $_POST['bill_no'];
	$hosp_no = $_POST['hosp_no'];

	$patient_no = $_POST['patient_no'];
	$name = $_POST['name'];

	$pat_type = $_POST['pat_type'];

	$fdate = $_POST['fdate'];
	$tdate = $_POST['tdate'];

	if ($bill_no == '' && $hosp_no == '' && $patient_no == '' && $name == '') {
		$qry = "select * from uhid_and_opdid where date between '$fdate' and '$tdate' and type='$pat_type' order by slno desc";
	} else {
		if ($bill_no != '') {
			$qry = "select * from uhid_and_opdid where type='$pat_type' and bill_no='$bill_no' order by slno desc";
		} else if ($hosp_no != '') {
			$qry = "select * from uhid_and_opdid where hosp_no='$hosp_no' and type='$pat_type' order by slno desc";
		} else if ($patient_no != '') {
			$qry = "select * from uhid_and_opdid where opd_id='$patient_no' and type='$pat_type' order by slno desc";
		} else if ($name != '') {
			$qry = "select * from uhid_and_opdid where type='$pat_type' and patient_id in(select patient_id from patient_info where name like '%$name%') order by slno desc";
		}

	}

	?>
				<table class="table table-bordered table-condensed table-report">
					<tr>
						<th>#</th>
						<th>Sample No</th>
						<th>Hosp No</th>
						<th>Patient No</th>
						<th>Name</th>
						<th>Age/Sex</th>
						<th>Date/Time</th>
						<!--<th>Serial No</th>-->
					</tr>
			<?php
			$i = 1;
			$qr = mysqli_query($link, $qry);
			while ($q = mysqli_fetch_array($qr)) {
				$info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$q[patient_id]'"));
				?>
						<tr onclick="load_pat_details('<?php echo $q[opd_id]; ?>')">
							<th><?php echo $i; ?></th>
							<th><?php echo $q[sample_serial]; ?></th>
							<th><?php echo $q[hosp_no]; ?></th>
							<th><?php echo $q[opd_id]; ?></th>
							<th><?php echo $info[name]; ?></th>
							<th><?php echo $info[age] . " " . $info[age_type] . " / " . $info[sex]; ?></th>
							<th><?php echo convert_date($q[date]) . " / " . convert_time($q[time]); ?></th>
							<!--<th><?php echo $q[date_serial]; ?></th>-->
						</tr>
				<?php
				$i++;
			}
			?>
				</table>
	<?php
} else if ($type == 5) {
	$opd_id = $_POST['opdid'];


	$det = mysqli_fetch_array(mysqli_query($link, "select * from uhid_and_opdid where opd_id='$opd_id'"));

	$ndate = convert_date($det[date]);

	$info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$det[patient_id]'"));

	$dis = mysqli_fetch_array(mysqli_query($link, "select * from patient_disease_details where patient_id='$det[patient_id]' and opd_id='$opd_id'"));

	$pat_info = $det[hosp_no] . "@k_details@" . $info[name] . "@k_details@" . $info[age] . "@k_details@" . $info[age_type] . "@k_details@" . $info[sex] . "@k_details@@k_details@" . $det[type] . "@k_details@@k_details@@k_details@" . $det[date_serial] . "@k_details@" . $ndate . "@k_details@@k_details@" . $det[ward] . "@k_details@" . $info[phone] . "@k_details@" . $info[address] . "@k_details@" . $dis[disease_id] . "@k_details@@k_details@" . $det[bill_no] . "@k_details@" . $det[pat_type] . "@k_details@" . $info[patient_id];

	$tst_det = mysqli_query($link, "select * from patient_test_details where patient_id='$det[patient_id]' and opd_id='$opd_id'");
	while ($tst_l = mysqli_fetch_array($tst_det)) {
		//$tst.=$tst_l[testid]."@tst@";		
	}

	$s_det = "";
	$samp = mysqli_query($link, "select * from patient_sample_details where patient_id='$det[patient_id]' and opd_id='$opd_id'");
	while ($smp = mysqli_fetch_array($samp)) {
		$s_det .= "@@" . $smp[sample_id];
	}
	$pat_info .= "@k_details@" . $s_det . "@k_details@" . $det['sample_serial'];

	echo $pat_info . "#test_det#" . $tst;
} else if ($type == 6) {
	$entry_type = $_POST['entry_type'];
	$val = $_POST['val'];
	$date = date("Y-m-d");


	$chk_samp = mysqli_fetch_array(mysqli_query($link, "select count(*) as tot from uhid_and_opdid where date='$date' and type='$entry_type' and sample_serial='$val'"));
	echo $chk_samp[tot];

} else if ($type == 7) {
	$opd = $_POST['opd_id'];
	?>
							<div style="height:300px;overflow:scroll;overflow-x:hidden">
								<table class="table table-bordered table-condensed table-report">
									<tr>
										<th></th>
										<th>Vaccu</th>
										<th>Barcode ID</th>
									</tr>

				<?php
				$barcs = mysqli_query($link, "select distinct(barcode_id) from test_sample_result where opd_id='$opd'");
				while ($barc = mysqli_fetch_array($barcs)) {
					$vacc = mysqli_fetch_array(mysqli_query($link, "select vaccus from test_sample_result where barcode_id='$barc[barcode_id]' limit 1"));
					?>
										<tr>
											<td><input type="checkbox" value="<?php echo $barc[barcode_id]; ?>" class="barc_id" /></td>
											<td><?php echo $vacc[vaccus]; ?></td>
											<td><?php echo $barc[barcode_id]; ?></td>
										</tr>
				<?php
				}
				?>
								</table>
							</div>
							<div style="text-align:center">
								<button class="btn btn-info" id="sel_all" onclick="sel_all()" value="Select All">Select All</button>
								<button class="btn btn-info" onclick="print_barc_sel()">Print</button>
								<button class="btn btn-info" onclick="print_barc_sticker()">Print Bill Sticker</button>
							</div>
	<?php
} else if ($type == 8) {
	$val = $_POST['val'];
	$samp_no = mysqli_fetch_array(mysqli_query($link, "select count(*) as mx from uhid_and_opdid where type='$val' and date='$date'"));
	$n_serial = $samp_no[mx] + 1;
	echo $n_serial;
} else if ($type == 9) {
	$bill = $_POST['bill'];

	$bill_det = mysqli_query($link, "select * from uhid_and_opdid where bill_no='$bill'");

	$chk_bill = mysqli_num_rows($bill_det);

	if ($chk_bill > 0) {
		$bill_info = mysqli_fetch_array($bill_det);
		$info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$bill_info[patient_id]' and hosp_no='$bill_info[hosp_no]'"));

		echo $info[name] . "@#@" . $bill_info[hosp_no];
	}

} else if ($type == 10) {
	$opd = $_POST['opd_id'];
	$tst = $_POST['tst'];

	$chk_res = mysqli_fetch_array(mysqli_query($link, "select count(*) as tot from test_sample_result where opd_id='$opd' and testid='$tst' and result!=''"));
	$chk_t_res = mysqli_fetch_array(mysqli_query($link, "select count(*) as tot from testresults where opd_id='$opd' and testid='$tst' and result!=''"));

	$tot = $chk_res[tot] + $chk_t_res[tot];
	echo $tot;

} else if ($type == 11) {
	$ip = $_POST['ip'];
	$val = mysqli_fetch_array(mysqli_query($link, "select * from barcode_setting where ip_address='$ip'"));
	$minus = "";
	if ($val[fo_val] == 0) {
		$minus = "disabled";
	}
	$plus = "";
	if ($val[fo_val] == 250) {
		$plus = "disabled";
	}
	?>
											<div class="btn-group">
												<button class="btn btn-success" <?php echo $plus; ?> onclick="bar_change('<?php echo $ip; ?>',1)" id="bar_plus"><i
														class="icon-plus"></i></button>
												<input type="text" value="<?php echo $val[fo_val]; ?>" id="bar_value"
													onkeyup="change_bar(event,'<?php echo $ip; ?>')" class="span1" style="margin-top:10px" />
												<button class="btn btn-success" <?php echo $minus; ?> onclick="bar_change('<?php echo $ip; ?>',2)" id="bar_plus"><i
														class="icon-minus"></i></button>
												<button class="btn btn-success" onclick="load_bar_set('<?php echo $ip; ?>')"><i class="icon-ok"></i></button>
											</div>
	<?php
} else if ($type == 12) {
	$ip = $_POST['ip'];
	$typ = $_POST['typ'];
	$bar_value = $_POST['bar_value'];

	if ($typ == 1) {
		$nbar = $bar_value + 5;
		mysqli_query($link, "update barcode_setting set fo_val='$nbar' where ip_address='$ip'");
	} elseif ($typ == 2) {
		$nbar = $bar_value - 5;
		mysqli_query($link, "update barcode_setting set fo_val='$nbar' where ip_address='$ip'");
	}
} else if ($type == 13) {
	$ip = $_POST['ip'];
	$bar_value = $_POST['bar_value'];
	echo "update barcode_setting set fo_val='$bar_value' where ip_address='$ip'";
	mysqli_query($link, "update barcode_setting set fo_val='$bar_value' where ip_address='$ip'");
}
?>