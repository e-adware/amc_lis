<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user = $_SESSION["emp_id"];

$date = date("Y-m-d");
$time = date("H:i:s");

$type = $_POST['type'];

if ($type == "load_dept_tests") {
	$dept_id = $_POST['dept_id'];

	$test_str = "SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='1'";

	echo '<option value="0">--All(Test)--</option>';

	if ($dept_id > 0) {
		$test_str .= " AND `type_id`='$dept_id'";
	}

	$test_str .= " ORDER BY `testname` ASC";

	$test_qry = mysqli_query($link, $test_str);

	while ($test_info = mysqli_fetch_array($test_qry)) {
		echo "<option value='$test_info[testid]'>$test_info[testname]</option>";
	}
}

if ($type == "load_pat_list")
{
	$fdate = $_POST['fdate'];
	$tdate = $_POST['tdate'];
	$dept_id = $_POST['dept_id'];
	$testid = $_POST['testid'];
	$ward = $_POST['ward'];

	$bill_no = $_POST['bill_no'];
	$name = $_POST['name'];
	$hosp_no = $_POST['uhid'];
	$barcode_id = $_POST['barcode_id'];

	$patType = $_POST["patType"];
	
	$list_start = $_POST["list_start"];

	$sample_type = $_POST['sample_type'];
	$sample_serial = $_POST['sample_serial'];

	$sel_doc = $_POST['sel_doc'];

	$zz = 0;
	
	$sel_doc_str = "SELECT DISTINCT a.`opd_id` FROM `testresults` a , `uhid_and_opdid` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`=d.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid`";
	
	//$test_str = "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no` FROM `phlebo_sample` a , `uhid_and_opdid` b, `testmaster` c, `patient_info` d WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`=d.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid`";
	
	$test_str = "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no` FROM `patient_test_details` a , `uhid_and_opdid` b, `testmaster` c, `patient_info` d WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`=d.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid`";
	
	if ($dept_id > 0) {
		$test_str .= " AND c.`type_id`='$dept_id'";
		
		$sel_doc_str .= " AND c.`type_id`='$dept_id'";
	}

	if ($testid > 0) {
		$test_str .= " AND c.`testid`='$testid'";
		
		$sel_doc_str .= " AND c.`testid`='$testid'";
	}

	if ($ward > 0) {
		$test_str .= " AND b.`ward`='$ward'";
		
		$sel_doc_str .= " AND b.`ward`='$ward'";
	}

	if ($sample_type) {
		$test_str .= " AND b.`type_prefix`='$sample_type'";
		
		$sel_doc_str .= " AND b.`type_prefix`='$sample_type'";

		if ($sample_serial != "") {
			$test_str .= " AND b.`sample_serial`='$sample_serial'";
			
			$sel_doc_str .= " AND b.`sample_serial`='$sample_serial'";
		}
	}

	if ($hosp_no != "") {
		$test_str .= " AND b.`hosp_no`='$hosp_no'";
		
		$sel_doc_str .= " AND b.`hosp_no`='$hosp_no'";
		
		$zz++;
	}
	if ($bill_no != "") {
		$test_str .= " AND b.`cashMemoNo`='$bill_no'";
		
		$sel_doc_str .= " AND b.`cashMemoNo`='$bill_no'";
		
		$zz++;
	}

	if (strlen($name) > 2) {
		$test_str .= " AND d.`name` LIKE '%$name%'";

		$zz++;
	}

	
	if ($zz == 0) {
		$test_str .= " AND a.`date` BETWEEN '$fdate' AND '$tdate'";
	}
	
	$test_str .= " AND c.`category_id`=1";
	
	if($sel_doc>0)
	{
		$sel_doc_str.=" AND a.doc = '$sel_doc'";
		
		$test_str.=" AND a.`opd_id` IN($sel_doc_str)";
	}
	
	$test_str .= " GROUP BY a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`";
	
	//$test_str .= " ORDER BY a.`slno` DESC LIMIT " . $list_start;
	$test_str .= " ORDER BY b.`slno` DESC";
	
	$test_str;
	
	$test_qry = mysqli_query($link, $test_str);
?>
	<table class="table table-bordered table-condensed" style="background-color:white;" id="myTable">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Hospital No.</th>
				<th>Sample No.</th>
				<th>Name</th>
				<th>Date</th>
				<th>Test</th>
				<th>Result Status</th>
				<th style="width: 15%;">
					<label><input type="checkbox" id="chkall" onchange="select_all()" /> Select All</label>
					<button type="button" class="btn btn-primary btn-mini" onclick="print_selected()">Print Selected</button>
				</th>
			</tr>
		</thead>
<?php
		$sameID="";
		$i = 1;
		while ($test_det = mysqli_fetch_array($test_qry))
		{
			$patient_id = $test_det["patient_id"];
			$opd_id = $test_det["opd_id"];
			$ipd_id = $test_det["ipd_id"];
			$batch_no = $test_det["batch_no"];
			
			$pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND (`opd_id`='$opd_id' OR `opd_id`='$ipd_id') "));
			
			$tr_id = str_replace("/", "", $pat_reg["opd_id"]) . $batch_no;
			
			$urgent_patient = 0;

			$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT `hosp_no`,`name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$patient_id' "));
			
			$reg_date=$pat_reg["date"];

			/*if ($pat_info["dob"] != "") {
				$age = age_calculator_date_only($pat_info["dob"], $reg_date);
			} else {
				$age = $pat_info["age"] . " " . $pat_info["age_type"];
			}*/

			$printClass = "not_printed";
			$printChk = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `testreport_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no'"));
			if ($printChk) {
				$printClass = "printed";
			}
			
			$authClass="NoResult";
			
			$result_chkPat=mysqli_fetch_assoc(mysqli_query($link, "SELECT `doc`,`tech`,`main_tech` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND (`doc`>0 OR `main_tech`>0)"));
			if($result_chkPat)
			{
				$authClass="Authenticated";
			}else
			{
				$result_chkPat=mysqli_fetch_assoc(mysqli_query($link, "SELECT `result` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `result`!=''"));
				if($result_chkPat)
				{
					$authClass="Unverified";
				}
			}
			
			$testQry=mysqli_query($link, "SELECT b.testid,b.testname FROM `patient_test_details` a, `testmaster` b WHERE a.testid=b.testid AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no'");
			$zz=$testNum=mysqli_num_rows($testQry);
			
			$n=1;
			while($test_info=mysqli_fetch_assoc($testQry))
			{
				$testid=$test_info["testid"];
				
				$provisional_print=0;
				$view_report=0;
				$result_status_color="color:red;";
				$result_status="No Result";
				
				$result_chk=mysqli_fetch_assoc(mysqli_query($link, "SELECT `doc`,`tech`,`main_tech` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND (`doc`>0 OR `main_tech`>0)"));
				
				if($result_chk)
				{
					$result_status="Authenticated";
					$result_status_color="color:green;";
					
					$view_report=1;
					$provisional_print++;
				}else
				{
					$result_chk=mysqli_fetch_assoc(mysqli_query($link, "SELECT `result` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `result`!=''"));
					if($result_chk)
					{
						$result_status="Unverified";
						$result_status_color="color:#f89406;";
						
						$view_report=2;
						$provisional_print++;
					}
					
					$zz--;
				}
?>
				<tr class="trClass <?php echo $printClass; ?> <?php echo $authClass; ?> <?php echo $pat_reg["opd_id"]; ?>" id="<?php echo $tr_id; ?>">
				<?php
					if($n==1)
					{
				?>
					<td rowspan="<?php echo $testNum; ?>"><?php echo $i; ?></td>
					<td rowspan="<?php echo $testNum; ?>"><?php echo $pat_reg["hosp_no"]; ?></td>
					<td rowspan="<?php echo $testNum; ?>"><?php echo $pat_reg['type_prefix'] . $pat_reg['sample_serial']; ?></td>
					<td rowspan="<?php echo $testNum; ?>"><?php echo $pat_info["name"]; ?></td>
					<td rowspan="<?php echo $testNum; ?>"><?php echo $pat_reg['date']; ?></td>
				<?php
					}
				?>
					<td><?php echo $test_info['testname']; ?></td>
					<td style="<?php echo $result_status_color; ?>">
						<?php echo "<b>".$result_status."</b>"; ?>
						
						<?php
						if($provisional_print>0){
						?>
							<a class="btn btn-link" style="float:right;padding: 0;font-size: 12px;" onclick="view_report('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $view_report; ?>')">View Report</a>
						<?php
							}
						?>
					</td>
				<?php
					if($n==1)
					{
						$chkID="chk".str_replace("/","",$pat_reg["opd_id"]);
				?>
						<td rowspan="<?php echo $testNum; ?>">
							<label id="tdLabel<?php echo $chkID; ?>"><input type="checkbox" class="checks" id="<?php echo $chkID; ?>" onchange="check_all_chks()" value="<?php echo $patient_id . "@" . $pat_reg['opd_id']; ?>" /> Select</label>
						</td>
				<?php
					}
				?>
				</tr>
<?php
				$n++;
			}
			
			if($zz==0)
			{
				echo "<script>$('#tdLabel".$chkID."').remove();</script>";
			}
			
			$i++;
		}
?>
	</table>
<?php
}

if ($type == "view_report")
{
	$patient_id	 = $_POST["patient_id"];
	$opd_id		 = $_POST["opd_id"];
	$ipd_id		 = $_POST["ipd_id"];
	$batch_no	 = $_POST["batch_no"];
	$testid		 = $_POST["testid"];
	$val		 = $_POST["val"]; // 1 = testresult, 2 = test_sample_result
	
	$test_info=mysqli_fetch_assoc(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));
?>
	<div>
		<div><b>Test Name :</b> <?php echo $test_info["testname"]; ?></div>
		<table class="table table-condensed">
			<tr>
				<th>#</th>
				<th>Parameter Name</th>
				<th>Result</th>
				<th>Unit</th>
				<th>Normal Range</th>
			</tr>
	<?php
		if($val==1)
		{
			$str="SELECT a.paramid,b.ResultType,b.Name,b.UnitsID,a.result,a.range_status,a.range_id,a.instrument_id,a.date,a.time FROM `testresults` a, `Parameter_old` b WHERE a.paramid=b.ID AND a.patient_id='$patient_id' AND a.opd_id='$opd_id' AND a.ipd_id='$ipd_id' AND a.batch_no='$batch_no' AND a.testid='$testid' AND a.paramid NOT IN(639,640,641)";
		}
		if($val==2)
		{
			$str="SELECT a.paramid,b.ResultType,b.Name,b.UnitsID,a.result,a.equip_name,a.update_timestamp FROM `test_sample_result` a, `Parameter_old` b WHERE a.paramid=b.ID AND a.patient_id='$patient_id' AND a.opd_id='$opd_id' AND a.ipd_id='$ipd_id' AND a.batch_no='$batch_no' AND a.testid='$testid' AND a.result!='' AND a.paramid NOT IN(639,640,641)";
		}
		
		$qry=mysqli_query($link, $str);
		
		$n=1;
		while($data=mysqli_fetch_assoc($qry))
		{
			$unit_info=mysqli_fetch_assoc(mysqli_query($link, "SELECT `unit_name` FROM `Units` WHERE `ID`='$data[UnitsID]'"));
			
			if($data["range_id"]>0)
			{
				$range=mysqli_fetch_assoc(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$data[range_id]' ORDER BY `slno` DESC"));
			}else
			{
				$range=mysqli_fetch_assoc(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `parameter_id`='$data[paramid]' ORDER BY `slno` DESC"));
			}
	?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $data["Name"]; ?></td>
				<td><?php echo $data["result"]; ?></td>
				<td><?php echo $unit_info["unit_name"]; ?></td>
				<td><?php echo $range["normal_range"]; ?></td>
			</tr>
	<?php
			$n++;
		}
	?>
		</table>
	</div>
<?php
}


if ($type == "load_pat_list_old")
{
	$fdate = $_POST['fdate'];
	$tdate = $_POST['tdate'];
	$dept_id = $_POST['dept_id'];
	$testid = $_POST['testid'];
	$ward = $_POST['ward'];

	$bill_no = $_POST['bill_no'];
	$name = $_POST['name'];
	$hosp_no = $_POST['uhid'];
	$barcode_id = $_POST['barcode_id'];

	$patType = $_POST["patType"];
	
	$list_start = $_POST["list_start"];

	$sample_type = $_POST['sample_type'];
	$sample_serial = $_POST['sample_serial'];

	$sel_doc = $_POST['sel_doc'];

	$zz = 0;
	
	$sel_doc_str = "SELECT DISTINCT a.`opd_id` FROM `testresults` a , `uhid_and_opdid` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`=d.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid`";
	
	//$test_str = "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no` FROM `phlebo_sample` a , `uhid_and_opdid` b, `testmaster` c, `patient_info` d WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`=d.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid`";
	
	$test_str = "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`testid` FROM `patient_test_details` a , `uhid_and_opdid` b, `testmaster` c, `patient_info` d WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`=d.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid`";
	
	if ($dept_id > 0) {
		$test_str .= " AND c.`type_id`='$dept_id'";
		
		$sel_doc_str .= " AND c.`type_id`='$dept_id'";
	}

	if ($testid > 0) {
		$test_str .= " AND c.`testid`='$testid'";
		
		$sel_doc_str .= " AND c.`testid`='$testid'";
	}

	if ($ward > 0) {
		$test_str .= " AND b.`ward`='$ward'";
		
		$sel_doc_str .= " AND b.`ward`='$ward'";
	}

	if ($sample_type) {
		$test_str .= " AND b.`type_prefix`='$sample_type'";
		
		$sel_doc_str .= " AND b.`type_prefix`='$sample_type'";

		if ($sample_serial != "") {
			$test_str .= " AND b.`sample_serial`='$sample_serial'";
			
			$sel_doc_str .= " AND b.`sample_serial`='$sample_serial'";
		}
	}

	if ($hosp_no != "") {
		$test_str .= " AND b.`hosp_no`='$hosp_no'";
		
		$sel_doc_str .= " AND b.`hosp_no`='$hosp_no'";
		
		$zz++;
	}
	if ($bill_no != "") {
		$test_str .= " AND b.`cashMemoNo`='$bill_no'";
		
		$sel_doc_str .= " AND b.`cashMemoNo`='$bill_no'";
		
		$zz++;
	}

	if (strlen($name) > 2) {
		$test_str .= " AND d.`name` LIKE '%$name%'";

		$zz++;
	}

	
	if ($zz == 0) {
		$test_str .= " AND a.`date` BETWEEN '$fdate' AND '$tdate'";
	}
	
	$test_str .= " AND c.`category_id`=1";
	
	if($sel_doc>0)
	{
		$sel_doc_str.=" AND a.doc = '$sel_doc'";
		
		$test_str.=" AND a.`opd_id` IN($sel_doc_str)";
	}
	
	$test_str .= " GROUP BY a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`testid`";
	
	//$test_str .= " ORDER BY a.`slno` DESC LIMIT " . $list_start;
	$test_str .= " ORDER BY b.`slno` DESC";
	
	$test_str;
	
	$test_qry = mysqli_query($link, $test_str);
	?>
	<table class="table table-bordered table-condensed" style="background-color:white;" id="myTable">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Hospital No.</th>
				<th>Sample No.</th>
				<th>Name</th>
				<th>Date</th>
				<th>Test</th>
				<th>Result Status</th>
				<th style="width: 15%;">
					<label><input type="checkbox" id="chkall" onchange="select_all()" /> Select All</label>
					<button type="button" class="btn btn-primary btn-mini" onclick="print_selected()">Print Selected</button>
				</th>
			</tr>
		</thead>
		<?php
		$sameID="";
		$i = 1;
		while ($test_det = mysqli_fetch_array($test_qry)) {
			$appr_doc = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_doctor` WHERE `id` = '$test_det[doc]'"));
			$patient_id = $test_det["patient_id"];
			$opd_id = $test_det["opd_id"];
			$ipd_id = $test_det["ipd_id"];
			$batch_no = $test_det["batch_no"];
			$testid = $test_det["testid"];
			
			$tr_id = str_replace("/", "", $pat_reg["opd_id"]) . $batch_no;
			
			$test_info=mysqli_fetch_assoc(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));
			
			if($sameID!=$opd_id)
			//if(1==1)
			{
				$zz=$testNum=mysqli_num_rows(mysqli_query($link, "SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no'"));
				//$testNum=1;
				$pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND (`opd_id`='$opd_id' OR `opd_id`='$ipd_id') "));

				$urgent_patient = 0;

				$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT `hosp_no`,`name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$patient_id' "));

				//$reg_date=$pat_reg["date"];
				$reg_date = $test_date;

				if ($pat_info["dob"] != "") {
					$age = age_calculator_date_only($pat_info["dob"], $reg_date);
				} else {
					$age = $pat_info["age"] . " " . $pat_info["age_type"];
				}

				$printClass = "not_printed";
				$printChk = mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `testreport_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no'"));
				if ($printChk) {
					$printClass = "printed";
				}
			}
			
			$provisional_print=0;
			$result_status_color="color:red;";
			$result_status="No Result";
			
			$authClass="Unverified";
			
			$result_chk=mysqli_fetch_assoc(mysqli_query($link, "SELECT `doc`,`tech`,`main_tech` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND (`doc`>0 OR `main_tech`>0)"));
			
			if($result_chk)
			{
				$result_status="Authenticated";
				$authClass="Authenticated";
				$result_status_color="color:green;";
			}else
			{
				$result_chk=mysqli_fetch_assoc(mysqli_query($link, "SELECT `result` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `result`!=''"));
				if($result_chk)
				{
					$result_status="Unverified";
					$result_status_color="color:#f89406;";
					
					$provisional_print++;
				}
				$zz--;
			}
			
			?>
			<tr class="trClass <?php echo $flag_class; ?> <?php echo $printClass; ?> <?php echo $authClass; ?> <?php echo $pat_reg["opd_id"]; ?>" id="<?php echo $tr_id; ?>">
		<?php
			if($sameID!=$opd_id)
			//if(1==1)
			{
		?>
				<td rowspan="<?php echo $testNum; ?>"><?php echo $i; ?></td>
				<td rowspan="<?php echo $testNum; ?>"><?php echo $pat_info['hosp_no']; ?></td>
				<td rowspan="<?php echo $testNum; ?>"><?= $pat_reg['type_prefix'] . $pat_reg['sample_serial'] ?></td>
				<td rowspan="<?php echo $testNum; ?>">
					<?php echo $pat_info['name']; ?>
					
					<input type="hidden" id="pid_<?php echo $i; ?>" value="<?php echo $patient_id; ?>" />
					<input type="hidden" id="opd_<?php echo $i; ?>" value="<?php echo $opd_id; ?>" />
					<input type="hidden" id="ipd_<?php echo $i; ?>" value="<?php echo $ipd_id; ?>" />
					<input type="hidden" id="batch_<?php echo $i; ?>" value="<?php echo $batch_no; ?>" />
				</td>
				<td rowspan="<?php echo $testNum; ?>"><?php echo $pat_reg['date']; ?></td>
		<?php
			}
		?>
				<td><?php echo $test_info["testname"]; ?></td>
				<td style="<?php echo $result_status_color; ?>">
					<?php echo "<b>".$result_status."</b>"; ?>
					
					<?php
					if($provisional_print>0){
					?>
						<a class="btn btn-link" style="float:right;padding: 0;font-size: 12px;">View Report</a>
					<?php
						}
					?>
				</td>
		<?php
			if($sameID!=$opd_id)
			//if(1==1)
			{
				$chkID="chk".str_replace("/","",$opd_id);
		?>
				<td rowspan="<?php echo $testNum; ?>">
					<label><input type="checkbox" class="checks" id="<?php echo $chkID; ?>" onchange="check_all_chks()" value="<?php echo $patient_id . "@" . $test_det['opd_id']; ?>" /> Select</label>
				</td>
		<?php
				$sameID=$opd_id;
				
				$i++;
			}
		?>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
}

?>
