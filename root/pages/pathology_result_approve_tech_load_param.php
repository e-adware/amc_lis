<?php
$test_str = "SELECT DISTINCT b.`testid`,b.`testname` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$dept_id'";

if ($barcode_id != "") {
	$test_str = "SELECT DISTINCT b.`testid`,b.`testname` FROM `patient_test_details` a, `testmaster` b, `test_sample_result` c WHERE a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`ipd_id`=c.`ipd_id` AND a.`batch_no`=c.`batch_no` AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' AND b.`type_id`='$dept_id'";

	$test_str .= " AND c.`barcode_id`='$barcode_id'";
}

if ($search_testid > 0) {
	$test_str .= " AND b.`testid`='$search_testid'";
}

$test_str .= " ORDER BY a.`testid` ASC";
//echo $test_str;
$test_qry = mysqli_query($link, $test_str);

$summary_result_types = [7, 27];
$page_breaker = "@@@@";

$flaggedStr="";
$flagEntry=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date`,`cause_user`,`cause`,`remarks` FROM `patient_flagged_records` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id' AND `flag`='1'"));
if($flagEntry)
{
	$empName=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$flagEntry[cause_user]'"));
	$flaggedStr="<i style='color:#C70000;'>[Flagged at : ".convert_date($flagEntry['date'])." ".convert_time($flagEntry['time'])." by ".$empName['name']."]</i>";
	
	$flagExit=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date`,`cause_user` FROM `patient_flagged_records` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id' AND `flag`='0' ORDER BY `slno` DESC LIMIT 1"));
	
	if($flagExit)
	{
		$empName=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$flagExit[cause_user]'"));
		$flaggedStr.=" &nbsp; &nbsp; <i style='color:#163D14;'>[Un-Flagged at : ".convert_date($flagExit['date'])." ".convert_time($flagExit['time'])." by ".$empName['name']."]</i>";
	}
	
	if($flagEntry["cause"]!="")
	{
		$flaggedStr.="<br/><i style='color:#C70000;'>[Cause : ".$flagEntry["cause"]."]</i>";
	}
	
	if($flagEntry["remarks"]!="")
	{
		$flaggedStr.="<br/><i style='color:#C70000;'>[Remarks : ".$flagEntry["remarks"]."]</i>";
	}
}
?>
<center><?php echo $flaggedStr;?></center>
<div id="pat_dept_test_params">
	<span style="font-weight:bold;"><b style='color:red;'>*</b> mark(s) are mandatory</span>
	<button class="btn btn-print btn-mini" style="float:right;"
		onclick="print_preview('2','<?php echo $dept_id; ?>','<?php echo $barcode_id; ?>')"><i class="icon-print"></i>
		Print Preview</button>
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix" id="pat_dept_test_params_header">
			<tr>
				<th style="width: 20%;">TEST</th>
				<th style="width: 40%;">RESULT</th>
				<th style="width: 5%;">UNIT</th>
				<th style="width: 12%;">REF. RANGE</th>
				<th style="width: 8%;">Validate</th>
				<th style="width: 7%;">Result Status</th>
				<th style="width: 8%;text-align:center;">Note</th>
			</tr>
		</thead>
		<?php
		
		// hemolysis
		$hemRes="";
		$Hemo=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$dept_id') AND `paramid`='639' AND `iso_no`='$iso_no'"));
		if($Hemo)
		{
			$hemRes=$Hemo['result'];
		}
		else
		{
			$Hemo=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$dept_id') AND `paramid`='639' AND `iso_no`=''"));
			$hemRes=$Hemo['result'];
		}
		$ictRes="";
		$Icte=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$dept_id') AND `paramid`='640' AND `iso_no`='$iso_no'"));
		if($Icte)
		{
			$ictRes=$Icte['result'];
		}
		else
		{
			$Icte=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$dept_id') AND `paramid`='640' AND `iso_no`=''"));
			$ictRes=$Icte['result'];
		}
		$turbRes="";
		$Turb=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$dept_id') AND `paramid`='641' AND `iso_no`='$iso_no'"));
		if($Turb)
		{
			$turbRes=$Turb['result'];
		}
		else
		{
			$Turb=mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$dept_id') AND `paramid`='641' AND `iso_no`=''"));
			$turbRes=$Turb['result'];
		}
		
		//if($hemRes!="" || $ictRes!="" || $turbRes!="")
		if(1==1)
		{
?>
			<tr>
				<td colspan="8"style="background:#EAEAEA;">
					<div class="noPrintPar"><i class="icon-circle icon-large hemolysis"></i> &nbsp;HEMOLYSIS : <b><i><?php echo $hemRes;?></i></b></div>
					<div class="noPrintPar"><i class="icon-circle icon-large icterus"></i> &nbsp;ICTERUS : <b><i><?php echo $ictRes;?></i></b></div>
					<div class="noPrintPar"><i class="icon-circle icon-large turbidity"></i> &nbsp;TURBIDITY : <b><i><?php echo $turbRes;?></i></b></div>
				</td>
			</tr>
<?php
		}
		
		$i = 1;
		$slno = 0;
		while ($test_info = mysqli_fetch_array($test_qry)) {
			$testid = $test_info["testid"];
			$testname = $test_info["testname"];

			$dispay_template_summary = 0;

			// Test Result Check
			$test_result_num = mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' LIMIT 1"));
			if ($test_result_num == 0) {
				$test_result_num = mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' LIMIT 1"));
				if ($test_result_num == 0) {
					$test_result_num = mysqli_num_rows(mysqli_query($link, "SELECT `sl_no` FROM `widalresult` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' LIMIT 1"));
				}
			}
			$test_note_disable = "";
			if ($test_result_num == 0) {
				$test_note_disable = "disabled";
			} else {
				$dispay_template_summary++;
			}

			$disabled_doc_approved = "";

			// Culture
			//if(strpos(strtolower($testname),'culture') !== false || strpos(strtolower($testname),'Culture') !== false || strpos(strtolower($testname),'CULTURE') !== false)
			if ($dept_id == 150) {
				$iso_no = "";

				$test_note_btn = "Add Note";
				$testresults_note = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `main_tech`>0"));
				if ($testresults_note["note"]) {
					$test_note_btn = "Update Note";
				}

				$iso_no_display = "display:none;";

				$test_iso_qry = mysqli_query($link, "SELECT DISTINCT `iso_no` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'");
				$test_iso_num = mysqli_num_rows($test_iso_qry);
				if ($test_iso_num > 0) {
					$result = mysqli_fetch_array(mysqli_query($link, "SELECT MAX(`iso_no`) AS `iso_no` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND testid='$testid' limit 1"));

					$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT MAX(`iso_no`) AS `iso_no` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' AND `result`!='' limit 1"));

					if ($test_sample_result["iso_no"] > $result["iso_no"]) {
						$result["iso_no"] = $test_sample_result["iso_no"];
					}
					if ($result["iso_no"] > 0) {
						$iso_no_display = "";
					}
				} else {
					$result = mysqli_fetch_array(mysqli_query($link, "SELECT MAX(`iso_no`) AS `iso_no` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' AND `result`!='' limit 1"));

					if ($result["iso_no"] > 0) {
						$iso_no_display = "";
					}
				}

				$disabled_doc_approved = "";
				$tc = mysqli_fetch_array(mysqli_query($link, "select * from testresults where `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
				if ($tc['doc'] > 0) {
					$disabled_doc_approved = "disabled";
				} else {
					$tc_s = mysqli_fetch_array(mysqli_query($link, "select * from patient_test_summary where `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
					if ($tc_s['doc'] > 0) {
						$disabled_doc_approved = "disabled";
					}
				}
				?>
				<tr>
					<th colspan="6" style="text-align:left;">
						<?php echo $testname; ?>
					</th>
					<td style="text-align:center;">
						<button class="btn btn-edit btn-mini test_note_btn" onclick="test_note('<?php echo $testid; ?>')" <?php echo $test_note_disable; ?> 		<?php echo $disabled_doc_approved; ?>><i class="icon-comment-alt"></i>
							<?php echo $test_note_btn; ?></button>
					</td>
				</tr>
				<tr>
					<td colspan="7" class="culture_td">
						<div>
							<span class="side_name">Select Growth</span>
							<select id="growth_val<?php echo $testid; ?>" onchange="load_culture_data('<?php echo $testid; ?>')"
								style="margin-left: 112px;" <?php echo $disabled_doc_approved; ?>>
								<option value="0" <?php if (!$result) {
									echo "selected";
								} ?>>---Select---</option>
								<option value="1" <?php if ($result["iso_no"] == 0) {
									echo "selected";
								} ?>>No Growth</option>
								<option value="2" <?php if ($result["iso_no"] > 0) {
									echo "selected";
								} ?>>Growth</option>
							</select>
							<span id="iso_field<?php echo $testid; ?>" style="<?php echo $iso_no_display; ?>">
								<span class="side_name">No. of ISO</span>
								<select class="span2" id="iso_no_total<?php echo $testid; ?>"
									onchange="load_culture_data('<?php echo $testid; ?>')" style="margin-left: 84px;" <?php echo $disabled_doc_approved; ?>>
									<?php
									for ($z = 0; $z <= 10; $z++) {
										if ($result["iso_no"] == $z) {
											$sel_iso = "selected";
										} else {
											$sel_iso = "";
										}
										echo "<option value='$z' $sel_iso>$z</option>";
									}
									?>
								</select>
							</span>
						</div>
						<div id="load_culture_data_div<?php echo $testid; ?>"></div>
						<script>
							setTimeout(function () {
								load_culture_data('<?php echo $testid; ?>');
							}, 300);
						</script>
					</td>
				</tr>
				<?php
			} else {
				$iso_no = "0";

				if ($testid == 1227) // Widal
				{
					$test_note_btn = "Add Note";
					$testresults_note = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `main_tech`>0"));
					if ($testresults_note["note"]) {
						$test_note_btn = "Update Note";
					}

					$w1 = mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno=1"));
					$w2 = mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno=2"));
					$w3 = mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno=3"));
					$w4 = mysqli_fetch_array(mysqli_query($link, "select * from widalresult where patient_id='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND slno=4"));

					if ($w1["main_tech"] > 0) {
						$test_result["main_tech"] = $w1["main_tech"];
					}
					if ($w2["main_tech"] > 0 && $test_result["main_tech"] == 0) {
						$test_result["main_tech"] = $w2["main_tech"];
					}
					if ($w3["main_tech"] > 0 && $test_result["main_tech"] == 0) {
						$test_result["main_tech"] = $w3["main_tech"];
					}
					if ($w4["main_tech"] > 0 && $test_result["main_tech"] == 0) {
						$test_result["main_tech"] = $w4["main_tech"];
					}

					$chk_box = "";
					if ($w1["main_tech"] > 0) {
						$chk_box = "checked='checked'";
					}

					$approve_function = "onclick=\"approve_widal_click('$testid','$dept_id')\"";

					$doc_approve_disabled = "";
					$approve_cls = "approve_test";
					$approve_id = "widal_chk";
					if ($w1["doc"] > 0) {
						$doc_approve_disabled = "disabled='disabled'";
						$approve_function = "";
						$approve_cls = "approve_test";
						$approve_id = "";
					}
					?>
					<tr>
						<th colspan="5" style="width: 500px;"><?php echo $testname; ?></th>
						<td>
							<label name="approve_test<?php echo $testid; ?>">
								<input type="checkbox" class="<?php echo $approve_cls; ?>" id="<?php echo $approve_id; ?>"
									value="<?php echo $testid; ?>" <?php echo $approve_function; ?> 			<?php echo $doc_approve_disabled; ?> 			<?php echo $chk_box; ?>>
								<?php
								if ($w1["main_tech"] > 0) {
									$tech_name = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$w1[main_tech]'"));
									echo "Validated<br><small>(" . $tech_name["name"] . ")</small>";
								} else {
									echo "Approve";
								}
								?>
							</label>
						</td>
						<td style="text-align:center;">
							<button class="btn btn-edit btn-mini test_note_btn" onclick="test_note('<?php echo $testid; ?>')" <?php echo $test_note_disable; ?> 			<?php echo $doc_approve_disabled; ?>><i class="icon-comment-alt"></i>
								<?php echo $test_note_btn; ?></button>
						</td>
					</tr>
					<tr>
						<td colspan="7">
							<div class="widal_result">
								<table class="table table-condensed table-bordered" style="width: 100% !important;">
									<tr>
										<th>Specimen</th>
										<td>
											<input type="text" id="specimen" value="<?php echo $w1["specimen"] ?>" <?php echo $doc_approve_disabled; ?>>
										</td>
										<th>Incubation Temperature(°C)</th>
										<td>
											<input type="text" id="incubation_temp" value="<?php echo $w1["incubation_temp"] ?>"
												<?php echo $doc_approve_disabled; ?>>
										</td>
										<th>Method</th>
										<td>
											<input type="text" id="method" value="<?php echo $w1["method"] ?>" list="method_list"
												<?php echo $doc_approve_disabled; ?>>
											<datalist id="method_list">
												<option>Slide Agglutination</option>
												<option>Tube Agglutination</option>
											</datalist>
										</td>
									</tr>
									<tr>
										<td colspan="6"></td>
									</tr>
								</table>
								<table class="table table-condensed table-bordered" style="width: 100% !important;">
									<tr>
										<th colspan="2">Dilution</th>
										<th>1:20</th>
										<th>1:40</th>
										<th>1:80</th>
										<th>1:160</th>
										<th>1:320</th>
										<th>1:640</th>
									</tr>
									<tr>
										<th rowspan="4">Anitgens</th>
										<th>"O"</th>
										<td><input type="text" name="t_par1" id="o0" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w1['F1'] ?>" maxlength='3'
												onkeyup="chk_widal1(1,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par2" id="o1" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w1['F2'] ?>" maxlength='3'
												onkeyup="chk_widal1(2,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par3" id="o2" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w1['F3'] ?>" maxlength='3'
												onkeyup="chk_widal1(3,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par4" id="o3" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w1['F4'] ?>" maxlength='3'
												onkeyup="chk_widal1(4,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par5" id="o4" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w1['F5'] ?>" maxlength='3'
												onkeyup="chk_widal1(5,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par6" id="o5" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w1['F6'] ?>" maxlength='3'
												onkeyup="chk_widal1(6,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
									</tr>
									<tr>
										<th style="text-align:left">"H"</th>
										<td><input type="text" name="t_par7" id="h0" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w2['F1'] ?>" maxlength='3'
												onkeyup="chk_widal1(7,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par8" id="h1" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w2['F2'] ?>" maxlength='3'
												onkeyup="chk_widal1(8,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par9" id="h2" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w2['F3'] ?>" maxlength='3'
												onkeyup="chk_widal1(9,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par10" id="h3" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w2['F4'] ?>" maxlength='3'
												onkeyup="chk_widal1(10,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par11" id="h4" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w2['F5'] ?>" maxlength='3'
												onkeyup="chk_widal1(11,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par12" id="h5" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w2['F6'] ?>" maxlength='3'
												onkeyup="chk_widal1(12,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
									</tr>
									<tr>
										<th style="text-align:left">"A(H)"</th>
										<td><input type="text" name="t_par13" id="ah0" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w3['F1'] ?>" maxlength='3'
												onkeyup="chk_widal1(13,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par14" id="ah1" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w3['F2'] ?>" maxlength='3'
												onkeyup="chk_widal1(14,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par15" id="ah2" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w3['F3'] ?>" maxlength='3'
												onkeyup="chk_widal1(15,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par16" id="ah3" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w3['F4'] ?>" maxlength='3'
												onkeyup="chk_widal1(16,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par17" id="ah4" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w3['F5'] ?>" maxlength='3'
												onkeyup="chk_widal1(17,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par18" id="ah5" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w3['F6'] ?>" maxlength='3'
												onkeyup="chk_widal1(18,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
									</tr>
									<tr>
										<th style="text-align:left">"B(H)"</th>
										<td><input type="text" name="t_par19" id="bh0" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w4['F1'] ?>" maxlength='3'
												onkeyup="chk_widal1(19,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par20" id="bh1" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w4['F2'] ?>" maxlength='3'
												onkeyup="chk_widal1(20,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par21" id="bh2" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w4['F3'] ?>" maxlength='3'
												onkeyup="chk_widal1(21,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par22" id="bh3" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w4['F4'] ?>" maxlength='3'
												onkeyup="chk_widal1(22,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par23" id="bh4" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w4['F5'] ?>" maxlength='3'
												onkeyup="chk_widal1(23,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
										<td><input type="text" name="t_par24" id="bh5" style="width: 80px !important;"
												class="td_widal" value="<?php echo $w4['F6'] ?>" maxlength='3'
												onkeyup="chk_widal1(24,this.id,event)" <?php echo $doc_approve_disabled; ?> /></td>
									</tr>
									<tr>
										<th colspan="2">IMPRESSION</th>
										<td colspan="7">
											<textarea id="impression" name="t_par25" style="width: 98%;resize: none;" <?php echo $doc_approve_disabled; ?>><?php if ($w4['DETAILS']) {
												   echo $w4['DETAILS'];
											   } else {
												   echo "Titre Insignificant";
											   } ?></textarea>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
					<?php
				} else {
					$iso_no = "0";
					
					$test_note_btn = "Add Note";
					$testresults_note = mysqli_fetch_array(mysqli_query($link, "SELECT `note` FROM `testresults_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `main_tech`>0"));
					if ($testresults_note["note"]) {
						$test_note_btn = "Update Note";
					}

					$test_param_qry = mysqli_query($link, "SELECT `ParamaterId`,`status` FROM `Testparameter` WHERE `TestId`='$testid' AND `ParamaterId` NOT IN(639,640,641) ORDER BY `sequence` ASC");
					$test_param_num = mysqli_num_rows($test_param_qry);

					if ($test_param_num > 1) {
						//echo "<tr><th colspan='4' style='font-size: 15px;'>$testname</th><th></th><th></th></tr>";
						?>
						<tr>
							<th><?php echo $testname; ?></th>
							<th colspan="3">
								<?php
								echo $repeat_param_btn_data = "<label id='repeat_test_label" . $testid . "' style='display: inline;margin-left: 26.5%;font-weight:bold;'> <input type='checkbox' id='repeat_test" . $testid . "' onclick=\"repeat_test_save('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','0','$testname','$dept_id')\"> Repeat All Parameters ($testname)</label>";
								?>
							</th>
							<td style="display:none;">
								<label name="approve_test<?php echo $testid; ?>">
									<input type="checkbox" id="approve_test<?php echo $testid; ?>">
									Validate
								</label>
							</td>
							<td></td>
							<td></td>
							<td style="text-align:center;">
								<button class="btn btn-edit btn-mini test_note_btn" onclick="test_note('<?php echo $testid; ?>')" <?php echo $test_note_disable; ?>><i class="icon-comment-alt"></i> <?php echo $test_note_btn; ?></button>
							</td>
						</tr>
						<?php
					}
					$test_main_tech = 0;
					while ($test_param = mysqli_fetch_array($test_param_qry)) {
						$paramid = $test_param["ParamaterId"];
						$dont_print = $test_param["status"];

						$dont_print_str = "";
						if ($dont_print == 1) {
							$dont_print_str = "<span style='color:red;'>(Don't print)</span>";
						}

						$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument` FROM `Parameter_old` WHERE `ID`='$paramid'"));

						if ($param_info["ResultType"] != 5) // Sub-Heading End
						{
							$unit_info = mysqli_fetch_array(mysqli_query($link, "SELECT `unit_name` FROM `Units` WHERE `ID`='$param_info[UnitsID]'"));

							$test_param_mandatory_str = "";
							$test_param_mandatory_attr = "mandatory=0";
							$test_param_mandatory = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `test_param_mandatory` WHERE `testid`='$testid' AND `paramid`='$paramid'"));
							if ($test_param_mandatory) {
								$test_param_mandatory_str = " <b style='color:red;'>*</b>";
								$test_param_mandatory_attr = "mandatory=1";
							}

							$result_text_style = "";

							$repeat_param_btn_display = 0;

							$instrument_name = "";
							$data_entry_name = "";
							$main_tech_name = "";
							$approve_result = "";
							$main_tech = 0;
							$instrument_id = 0;
							$result_hide = 0;
							$test_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`range_status`,`range_id`,`tech_note`,`doc_note`,`instrument_id`,`result_hide`,`doc`,`tech`,`main_tech`,`for_doc` FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no'"));
							if ($test_result) {
								$result = $test_result["result"];
								$instrument_id = $test_result["instrument_id"];
								$result_hide = $test_result["result_hide"];

								$main_tech = $test_result["main_tech"];
								$approve_result = $result;

								if ($test_result["range_status"] > 0) {
									$result_text_style = "color:red;";
								}

								if ($instrument_id > 0) {
									$instrument_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id`='$instrument_id'"));
									$instrument_name = $instrument_info["name"];
								} else {
									$data_entry_user = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[tech]'"));
									$data_entry_name = $data_entry_user["name"];
								}

								if ($test_result["main_tech"] > 0) {
									$main_tech_user = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[main_tech]'"));
									$main_tech_name = $main_tech_user["name"];

									$test_main_tech++;
								}
								if ($test_result["range_status"] > 0) {
									$result_text_style = "color:red;";
								}

								$dlc_check_str = "SELECT `slno`,`opd_id`,`ipd_id`,`batch_no`,`result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$patient_id' AND `paramid`='$paramid' AND `slno`<'$test_result[slno]' ORDER BY `slno` DESC";

								$repeat_param_btn_display++;
							} else {
								$test_sample_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`equip_name` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`=''"));
								if ($test_sample_result["result"])
								{
									$result = $test_sample_result["result"];
									$approve_result = $result;

									$lab_instrument = mysqli_fetch_array(mysqli_query($link, "SELECT `id` AS `instrument_id` FROM `lab_instrument_master` WHERE `name`='$test_sample_result[equip_name]'"));
									if ($lab_instrument) {
										$instrument_id = $lab_instrument["instrument_id"];
									}else
									{
										if($test_sample_result["equip_name"]=="" || !$lab_instrument)
										{
											if(mysqli_query($link, "INSERT INTO `lab_instrument_master`(`name`, `report_text`, `short_name`, `status`) VALUES ('$test_sample_result[equip_name]','','','0')"))
											{
												$lab_instrument = mysqli_fetch_array(mysqli_query($link, "SELECT `id` AS `instrument_id` FROM `lab_instrument_master` WHERE `name`='$test_sample_result[equip_name]'"));
												$instrument_id = $lab_instrument["instrument_id"];
											}
										}
									}
									$instrument_name = $test_sample_result["equip_name"];

									$repeat_param_btn_display++;

									$result_hide = $test_param["status"];
								} else {
									$param_fix_result = mysqli_fetch_array(mysqli_query($link, "SELECT `result` FROM `param_fix_result` WHERE `testid`='$testid' AND `paramid`='$paramid'"));

									$result = $param_fix_result["result"];
								}
								$dlc_check_str = "SELECT `slno`,`opd_id`,`ipd_id`,`batch_no`,`result`,`range_status`,`range_id` FROM `testresults` WHERE `patient_id`='$patient_id' AND `paramid`='$paramid' ORDER BY `slno` DESC";
							}

							//$result=htmlspecialchars($result);
		
							//$dlc_check_qry=mysqli_query($link, $dlc_check_str);
							//$dlc_check_num=mysqli_num_rows($dlc_check_qry);
		
							$approve_cls = "approve_param";
							$doc_approve_disabled = "";
							$approve_function = "onclick=\"approve_param_each('$testid','$paramid','0','$param_info[Name]','$dept_id')\"";

							if ($test_result["doc"] > 0) {
								$doc_approve_disabled = "disabled='disabled'";
								$approve_function = "";
								$approve_cls = "";

								$test_main_tech++;
							}

							$each_param_save_btn = "<button class='btn btn-save btn-mini' onclick=\"each_param_save('$testid','$paramid','0','$param_info[Name]','$dept_id')\"><i class='icon-save'></i></button>";
							if ($test_result["main_tech"] > 0) {
								$each_param_save_btn = "";

								$test_main_tech++;
							}

							if ($test_result["range_id"] && $test_result["range_id"] > 0) {
								$param_normal_range = mysqli_fetch_array(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `slno`='$test_result[range_id]'"));
								$param_range = nl2br($param_normal_range["normal_range"]);
							} else {
								$param_ranges = load_normal($patient_id, $paramid, $result, $instrument_id);

								$param_rangez = explode("#", $param_ranges);
								$param_range = $param_rangez[0];
								if ($param_rangez[1] == "Error") {
									$result_text_style = "color:red;";
								}
							}
							$td_str = "td";
							if ($param_info["ResultType"] == 0) {
								$td_str = "th";
							}
							$left_space_str = " &nbsp;&nbsp;&nbsp;&nbsp;";

							// Repeat Start
							$repeat_check = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `pathology_repeat_param_details` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and paramid='$paramid' ORDER BY `repeat_id` DESC LIMIT 1"));

							$repeat_param_btn_data = "<div style='display: inline;'>";
							if ($repeat_param_btn_display > 0) {
								$repeat_param_btn_data .= " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <label id='repeat_param_label" . $testid . "tst" . $paramid . "' style='display: inline;'> <input type='checkbox' id='repeat_param" . $testid . "tst" . $paramid . "' onclick=\"repeat_param_save('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0','$param_info[Name]','$dept_id')\" $doc_approve_disabled> Repeat </label>";

								$result_hide_chk = "";
								if ($result_hide == 1) {
									$result_hide_chk = "checked";
								}

								$repeat_param_btn_data .= " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <label id='dontPrint_param_label" . $testid . "tst" . $paramid . "' style='display: inline;'> <input type='checkbox' $result_hide_chk id='dontPrint_param" . $testid . "tst" . $paramid . "' onclick=\"dontPrint_param_save('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0','$param_info[Name]','$dept_id')\" $doc_approve_disabled> Don't Print </label>";
							} else {
								$repeat_param_btn_data .= " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}

							if ($repeat_check) {
								$repeat_param_btn_data .= " &nbsp;&nbsp;&nbsp;&nbsp; <a class='btn btn-link btn-mini' id='repeat_param_view_btn" . $testid . "tst" . $paramid . "' onclick=\"repeat_param_view('$patient_id','$opd_id','$ipd_id','$batch_no','$testid','$paramid','0')\" style='$repeat_btn_show'>View Repeat(s)</a>";
							} else {
								$repeat_param_btn_data .= " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
							}
							$repeat_param_btn_data .= "</div>";
							// Repeat End
		
							// Save
							$each_param_save_btn = "<button class='btn btn-save btn-mini' onclick=\"each_param_save('$testid','$paramid','0','$param_info[Name]','$dept_id')\"><i class='icon-save'></i></button>";
							if ($test_result["main_tech"] > 0) {
								$each_param_save_btn = "";
							}
							$each_param_save_btn .= $each_param_delete_btn;
							?>
							<tr id="param_tr<?php echo $i; ?>" class="<?php echo $paramid; ?>">
								<?php
								if ($test_param_num == 1) {
									echo "<th style='font-size: 13px;'>$param_info[Name]" . $dont_print_str . $test_param_mandatory_str . "</th>";
								} else {
									echo "<$td_str>" . $left_space_str . $param_info["Name"]. $dont_print_str . $test_param_mandatory_str . "</$td_str>";
								}

								if ($param_info["ResultType"] == 0) // Sub-Heading
								{
									echo "<td colspan='3'></td>";
									$i--;
								}
								if ($param_info["ResultType"] == 5) // Sub-Heading End
								{
									echo "<td colspan='3'></td>";
									$i--;
								}

								// Check result update
								$testresults_update_str = "";
								$testresults_update = mysqli_fetch_array(mysqli_query($link, "SELECT `result`,`edit_user`,`edit_date`,`edit_time` FROM `testresults_update` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `paramid`='$paramid' AND `iso_no`='$iso_no' ORDER BY `slno` DESC"));
								if ($testresults_update) {
									$ch_user_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$testresults_update[edit_user]'"));
									$testresults_update_str = "Last result: " . $testresults_update["result"] . ", changed by " . $ch_user_info["name"] . " on " . date("d-M-Y", strtotime($testresults_update["edit_date"])) . " " . date("h:i A", strtotime($testresults_update["edit_time"]));
								}

								if ($param_info["ResultType"] == 1) // Numeric only
								{
									$form_onkeyup = "";
									$form_sub = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `parameter_formula` WHERE `formula` LIKE '%p$paramid%'"));
									if ($form_sub["ParameterID"] > 0) {
										$form_onkeyup = "onkeyup=check_form('$testid','$form_sub[ParameterID]','$form_sub[formula]',$form_sub[res_dec])";
									}
									?>
									<td>
										<input type="text" class="t_par" name="t_par<?php echo $i; ?>"
											id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>"
											iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>"
											slno="<?php echo $i; ?>" <?php echo $test_param_mandatory_attr; ?> value="<?php echo $result; ?>"
											<?php echo $form_onkeyup; ?> style="<?php echo $result_text_style; ?>" <?php echo $doc_approve_disabled; ?>>

										<?php
										if ($each_param_save_btn) {
											echo $each_param_save_btn;
										}

										if ($repeat_param_btn_data) {
											echo $repeat_param_btn_data;
										}

										if ($dlc_check_num > 0) {
											?>
											<a class="btn btn-link btn-mini" style="font-size:10px;"
												onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta
												Check</a>
											<?php
										}
										?>

										<div style="color:red;display:;font-size: 10px;" id="last_result<?php echo $i; ?>">
											<?php
											echo $testresults_update_str;
											?>
										</div>
									</td>
									<td><?php echo $unit_info["unit_name"]; ?></td>
									<td><?php echo $param_range; ?></td>
									<?php
								}
								if ($param_info["ResultType"] == 6) // Formula
								{
									$form = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `parameter_formula` WHERE `ParameterID`='$paramid'"));
									if ($form) {
										if ($test_result["result"] == "") {
											$form_onfocus = "onfocus=check_form('$testid',this.id,'$form[formula]',$form[res_dec])";

											echo "<script>setTimeout(function(){ check_form('$testid','$paramid','$form[formula]','$form[res_dec]'); }),2000</script>";
										} else {
											$form_onfocus = "";
										}
									} else {
										$form_onfocus = "";
									}
									?>
									<td>
										<input type="text" class="t_par" name="t_par<?php echo $i; ?>"
											id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>"
											iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>"
											slno="<?php echo $i; ?>" <?php echo $test_param_mandatory_attr; ?> value="<?php echo $result; ?>"
											<?php echo $form_onfocus; ?> style="<?php echo $result_text_style; ?>" <?php echo $doc_approve_disabled; ?>>
										<?php
										if ($each_param_save_btn) {
											echo $each_param_save_btn;
										}

										if ($repeat_param_btn_data) {
											echo $repeat_param_btn_data;
										}

										if ($dlc_check_num > 0) {
											?>
											<a class="btn btn-link btn-mini" style="font-size:10px;"
												onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta
												Check</a>
											<?php
										}
										?>
										<div style="color:red;display:;font-size: 10px;" id="last_result<?php echo $i; ?>">
											<?php
											echo $testresults_update_str;
											?>
										</div>
									</td>
									<td><?php echo $unit_info["unit_name"]; ?></td>
									<td><?php echo $param_range; ?></td>
									<?php
								}
								if ($param_info["ResultType"] == 8) // Sub_param
								{
									$form_onkeyup = "";
									$form_sub = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `parameter_formula` WHERE `formula` LIKE '%p$paramid%'"));
									if ($form_sub["ParameterID"] > 0) {
										$form_onkeyup = "onkeyup=check_form('$testid','$form_sub[ParameterID]','$form_sub[formula]',$form[res_dec])";
									}
									?>
									<td>
										<input type="text" class="t_par" name="t_par<?php echo $i; ?>"
											id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>"
											iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>"
											slno="<?php echo $i; ?>" <?php echo $test_param_mandatory_attr; ?> value="<?php echo $result; ?>"
											<?php echo $form_onkeyup; ?> 						<?php echo $doc_approve_disabled; ?>>
										<?php
										if ($each_param_save_btn) {
											echo $each_param_save_btn;
										}

										if ($repeat_param_btn_data) {
											echo $repeat_param_btn_data;
										}

										if ($dlc_check_num > 0) {
											?>
											<a class="btn btn-link btn-mini" style="font-size:10px;"
												onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta
												Check</a>
											<?php
										}
										?>
										<div style="color:red;display:;font-size: 10px;" id="last_result<?php echo $i; ?>">
											<?php
											echo $testresults_update_str;
											?>
										</div>
									</td>
									<td><?php echo $unit_info["unit_name"]; ?></td>
									<td><?php echo $param_range; ?></td>
									<?php
								}
								if ($param_info["ResultType"] == 2) // List of Choices
								{
									?>
									<td>
										<input type="text" class="t_par" name="t_par<?php echo $i; ?>"
											id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>"
											iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>"
											slno="<?php echo $i; ?>" <?php echo $test_param_mandatory_attr; ?> value="<?php echo $result; ?>"
											list="list<?php echo $i; ?>" <?php echo $doc_approve_disabled; ?>>
										<datalist id="list<?php echo $i; ?>">
											<?php
											$ResultOptions_qry = mysqli_query($link, "SELECT a.`name` FROM `Options` a, `ResultOptions` b WHERE a.`id`=b.`optionid` AND b.`id`='$param_info[ResultOptionID]'");
											while ($ResultOptions = mysqli_fetch_array($ResultOptions_qry)) {
												echo "<option>$ResultOptions[name]</option>";
											}
											?>
										</datalist>
										<?php
										if ($each_param_save_btn) {
											echo $each_param_save_btn;
										}

										if ($repeat_param_btn_data) {
											echo $repeat_param_btn_data;
										}

										if ($dlc_check_num > 0) {
											?>
											<a class="btn btn-link btn-mini" style="font-size:10px;"
												onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta
												Check</a>
											<?php
										}
										?>
										<span style="color:green;display:none;"><i>(Double click for options)</i></span>

										<div style="color:red;display:;font-size: 10px;" id="last_result<?php echo $i; ?>">
											<?php
											echo $testresults_update_str;
											?>
										</div>
									</td>
									<td><?php echo $unit_info["unit_name"]; ?></td>
									<td><?php echo $param_range; ?></td>
									<?php
								}
								if ($param_info["ResultType"] == 3) // One Line Text
								{
									?>
									<td colspan="3">
										<input type="text" class="t_par" name="t_par<?php echo $i; ?>"
											id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>"
											iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>"
											slno="<?php echo $i; ?>" <?php echo $test_param_mandatory_attr; ?> value="<?php echo $result; ?>"
											style="" <?php echo $doc_approve_disabled; ?>>
										<?php
										if ($each_param_save_btn) {
											echo $each_param_save_btn;
										}

										if ($repeat_param_btn_data) {
											echo $repeat_param_btn_data;
										}

										if ($dlc_check_num > 0) {
											?>
											<a class="btn btn-link btn-mini" style="font-size:10px;"
												onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta
												Check</a>
											<?php
										}
										?>
										<div style="color:red;display:;font-size: 10px;" id="last_result<?php echo $i; ?>">
											<?php
											echo $testresults_update_str;
											?>
										</div>
									</td>
									<?php
								}
								if ($param_info["ResultType"] == 27) // Multiline Text
								{
									//$result=nl2br($result);
									?>
									<td colspan="3">
										<textarea rows="6" class="t_par" name="t_par<?php echo $i; ?>"
											id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>"
											iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>"
											slno="<?php echo $i; ?>" <?php echo $test_param_mandatory_attr; ?> style="resize:none;width:98%;"
											<?php echo $doc_approve_disabled; ?>><?php echo $result; ?></textarea>
										<?php
										if ($each_param_save_btn) {
											echo $each_param_save_btn;
										}

										if ($repeat_param_btn_data) {
											echo $repeat_param_btn_data;
										}

										if ($dlc_check_num > 0) {
											?>
											<a class="btn btn-link btn-mini" style="font-size:10px;"
												onclick="load_delta_check('<?php echo $patient_id; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>','<?php echo $testid; ?>','<?php echo $paramid; ?>')">Delta
												Check</a>
											<?php
										}
										?>
									</td>
									<?php
								}
								if ($param_info["ResultType"] == 7) // Pad
								{
									echo "<td colspan='6'></td></tr><tr>";
									$pad_summary_btn = "Edit Result";
									if ($result == "") {
										$test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `test_summary` WHERE `paramid`='$paramid'"));
										if ($test_summary["summary"]) {
											$result_sum = $test_summary["summary"];
										}
										$pad_summary_btn = "Add Result";

										$result = $result_sum;
									}
									?>
									<td colspan="4">
										<div id="padd_display_div<?php echo $i; ?>">
											<div style="text-align:right;">
												<button class="btn btn-new btn-mini" onclick="load_editor('<?php echo $i; ?>')"><i
														class="icon-edit"></i> <?php echo $pad_summary_btn; ?></button>
											</div>
											<div ondblclick="load_editor('<?php echo $i; ?>')" title="Double Click to edit">
												<?php
												$result_display = str_replace("@@@@", "<b>Page Break</b>", $result);
												echo "<div>" . $result_display . "</div>";
												?>
											</div>
										</div>
										<?php
										if ($testid == 83) {
											?>
											<script>
												setTimeout(function () {
													load_editor('<?php echo $i; ?>');
												}, 100);
											</script>
											<?php
										}
										?>
										<div id="padd_edit_div<?php echo $i; ?>" style="display:none;">
											<div id="padd<?php echo $testid; ?>PRM<?php echo $paramid; ?>">
												<textarea rows="6" class="t_par" name="t_par<?php echo $i; ?>"
													id="<?php echo $testid; ?>PRM<?php echo $paramid; ?>" param_testid="<?php echo $testid; ?>"
													iso_no="<?php echo $iso_no; ?>" instrument_id="<?php echo $instrument_id; ?>"
													slno="<?php echo $i; ?>" <?php echo $test_param_mandatory_attr; ?> padd="1"
													style="resize:none;width:98%;" <?php echo $doc_approve_disabled; ?>><?php echo $result; ?></textarea>
											</div>
											<center>
												<button class="btn btn-save"
													onclick="save_param_summary('<?php echo $testid; ?>','<?php echo $paramid; ?>','<?php echo $iso_no; ?>','<?php echo $instrument_id; ?>')"
													<?php echo $doc_approve_disabled; ?>><i class="icon-save"></i> Save &amp; Validate</button>
												<!--<button class="btn btn-back" onclick="back_editor('<?php echo $i; ?>')"><i class="icon-backward"></i> Back</button>-->
											</center>
										</div>
									</td>
									<?php
									//echo "<script>add_pad_param($i)</script>";
								}
								if ($param_info["ResultType"] == 0 || $param_info["ResultType"] == 5) // Sub-Heading
								{
									echo "<td></td>";
									echo "<td></td>";
									echo "<td></td>";
								} else {
									$chk_box = "";
									if ($test_result["main_tech"] > 0) {
										$chk_box = "checked='checked'";
									}
									?>
									<td>
										<label name="approve_param<?php echo $testid; ?>tst<?php echo $paramid; ?>">
											<input type="checkbox" class="<?php echo $approve_cls; ?> approve<?php echo $i; ?>"
												id="<?php echo $testid; ?>T<?php echo $paramid; ?>"
												value="<?php echo $testid; ?>TP<?php echo $paramid; ?>" <?php echo $approve_function; ?> 						<?php echo $doc_approve_disabled; ?> 						<?php echo $chk_box; ?>>
											<?php
											if ($test_result["main_tech"] > 0) {
												$tech_name = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$test_result[main_tech]'"));
												echo "Validated<br><small>(" . $tech_name["name"] . ")</small>";
											} else {
												echo "Validate";
											}
											?>
										</label>
									</td>
									<td>
										<?php
										if ($instrument_name) {
											echo "<div> Interfaced from <br><b>" . $instrument_name . "</b></div>";
										}
										if ($data_entry_name) {
											echo "<div>Data entry by <br><b>" . $data_entry_name . "</b></div>";
										}
										if ($main_tech_name) {
											echo "<div>Validated by <br><b>" . $main_tech_name . "</b></div>";
										}
										?>
									</td>
									<td style="text-align:center;">
										<?php
										if ($test_param_num == 1) {
											?>
											<button class="btn btn-edit btn-mini test_note_btn" onclick="test_note('<?php echo $testid; ?>')" <?php echo $test_note_disable; ?> 							<?php echo $doc_approve_disabled; ?>><i class="icon-comment-alt"></i>
												<?php echo $test_note_btn; ?></button>
											<?php
										}
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							$i++;
						}
					}
					if ($test_param_num > 1 && $test_main_tech == 0) {
						?>
						<tr id="save_result_tr<?php echo $i; ?>" class="<?php echo $paramid; ?>">
							<td></td>
							<td>
								<button class="btn btn-save t_par" name="t_par<?php echo $i; ?>" slno="<?php echo $i; ?>"
									id="each_test_param_result_save<?php echo $i; ?>"
									onclick="each_test_param_result_save('<?php echo $testid; ?>')"><i class="icon-save"></i> Save
									Result</button>
								<!--<input type="button" class="btn btn-save t_par" name="t_par<?php echo $i; ?>"  slno="<?php echo $i; ?>" id="each_test_param_result_save<?php echo $testid; ?>" value="Save Result" onclick="each_test_param_result_save('<?php echo $testid; ?>')">-->
							</td>
							<td colspan="5"></td>
						</tr>
						<?php
					}
				}
			}
			// Check Summary
			$approve_function = "";
			$chk_box = "";
			$approve_cls = "";
			$pat_test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary`,`doc`,`main_tech` FROM `patient_test_summary` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid'"));
			if ($pat_test_summary) {
				$approve_cls = "approve_test_summary";

				$approve_function = "onclick=\"approve_test_summ_each('$testid','$iso_no','$test_info[testname]','$dept_id')\"";

				$result = $pat_test_summary["summary"];
				$doc_approve_disabled = "";
				if ($pat_test_summary["doc"] > 0) {
					$doc_approve_disabled = "disabled='disabled'";

					$approve_cls = "";
					$approve_function = "";
				} else {
					$tc = mysqli_fetch_array(mysqli_query($link, "select * from testresults where `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
					if ($tc['doc'] > 0) {
						$disabled_doc_approved = "disabled";

						$approve_cls = "";
						$approve_function = "";
					}
				}

				$chk_box = "";
				if ($pat_test_summary["main_tech"] > 0) {
					$chk_box = "checked='checked'";
				}
				?>
				<tr id="pat_test_summ_tr<?php echo $i; ?>" class="<?php echo $paramid; ?>">
					<td colspan="4" style="width: 87%;">
						<div id="summary_display_div<?php echo $testid; ?>">
							<div style="text-align:right;">
								<button class="btn btn-excel btn-mini" onclick="load_summary_editor('<?php echo $testid; ?>')"
									<?php echo $doc_approve_disabled; ?>><i class="icon-edit"></i> Edit Summary</button>
							</div>
							<div ondblclick="load_summary_editor('<?php echo $testid; ?>')" title="Double Click to edit">
								<?php
								echo $result;
								?>
							</div>
						</div>
						<div id="summary_edit_div<?php echo $testid; ?>" style="display:none;">
							<div id="tpadd<?php echo $testid; ?>">
								<textarea rows="6" class="test_summ" name="test_summ<?php echo $testid; ?>"
									id="test_summ_id<?php echo $testid; ?>" padd="1"
									style="resize:none;width:98%;"><?php echo $result; ?></textarea>
							</div>
							<center>
								<button class="btn btn-save" onclick="save_test_summary('<?php echo $testid; ?>')" <?php echo $doc_approve_disabled; ?>><i class="icon-save"></i> Save &amp; Validate</button>
								<button class="btn btn-back" onclick="back_summary_editor('<?php echo $testid; ?>')"><i
										class="icon-backward"></i> Back</button>
							</center>
						</div>
					</td>
					<td>
						<label name="test_summary<?php echo $testid; ?>">
							<input type="checkbox" class="<?php echo $approve_cls; ?>"
								id="approve_test_summary<?php echo $testid; ?>" value="<?php echo $testid; ?>" <?php echo $approve_function; ?> 		<?php echo $doc_approve_disabled; ?> 		<?php echo $chk_box; ?>>
							<?php
							if ($pat_test_summary["main_tech"] > 0) {
								$tech_name = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$pat_test_summary[main_tech]'"));
								echo "Validated<br><small>(" . $tech_name["name"] . ")</small>";
							} else {
								echo "Validate";
							}
							?>
						</label>
					</td>
					<td></td>
					<td></td>
				</tr>
				<?php
			} else //if($dept_id!=30) // Histopathology
			{
				$test_sample_result_count["tot"] = 0;
				if ($dept_id == 150) // Culture
				{
					$test_sample_result_count = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`result`) AS `tot` FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `result`!=''"));
				}

				$test_summary = mysqli_fetch_array(mysqli_query($link, "SELECT `summary` FROM `test_summary` WHERE `testid`='$testid'"));
				$result = $test_summary["summary"];

				$approve_cls = "approve_test_summary";

				$approve_function = "onclick=\"approve_test_summ_each('$testid','$iso_no','$test_info[testname]','$dept_id')\"";

				$doc_approve_disabled = "";
				$tc = mysqli_fetch_array(mysqli_query($link, "select `doc` from testresults where `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
				if ($tc['doc'] > 0) {
					$doc_approve_disabled = "disabled";

					$approve_cls = "";
				} else {
					$tc_s = mysqli_fetch_array(mysqli_query($link, "select `doc` from patient_test_summary where `patient_id`='$patient_id' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid' and `doc`>0 limit 1"));
					if ($tc_s['doc'] > 0) {
						$doc_approve_disabled = "disabled";

						$approve_cls = "";
					}
				}

				$chk_box = "";
				if ($pat_test_summary["main_tech"] > 0) {
					$chk_box = "checked='checked'";
				}

				$btn_name = "Add Summary";

				$result_display = $result;
				if ($test_sample_result_count["tot"] > 0) {
					$btn_name = "Add Summary";

					$result_display = "";
				} else {
					if ($result && $dispay_template_summary == 0) {
						//$btn_name="Edit Summary";
					}
				}
				?>
				<tr id="test_summ_tr<?php echo $i; ?>" class="<?php echo $paramid; ?>">
					<td colspan="5" style="width: 87%">
						<div id="summary_display_div<?php echo $testid; ?>">
							<div style="text-align:right;">
								<button class="btn btn-excel btn-mini" onclick="load_summary_editor('<?php echo $testid; ?>')"
									<?php echo $doc_approve_disabled; ?>><i class="icon-edit"></i>
									<?php echo $btn_name; ?></button>
							</div>
							<div id="summary_display_sub_div<?php echo $testid; ?>"
								ondblclick="load_summary_editor('<?php echo $testid; ?>')" title="Double Click to edit">
								<?php
								if ($result_display) {
									echo $result_display;
								}
								?>
							</div>
						</div>
						<div id="summary_edit_div<?php echo $testid; ?>" style="display:none;">
							<div id="tpadd<?php echo $testid; ?>">
								<textarea rows="6" class="test_summ" name="test_summ<?php echo $testid; ?>"
									id="test_summ_id<?php echo $testid; ?>" padd="1"
									style="resize:none;width:98%;"><?php echo $result; ?></textarea>
							</div>
							<center>
								<button class="btn btn-save" onclick="save_test_summary('<?php echo $testid; ?>')" <?php echo $doc_approve_disabled; ?>><i class="icon-save"></i> Save &amp; Validate</button>
								<button class="btn btn-back" onclick="back_summary_editor('<?php echo $testid; ?>')"><i
										class="icon-backward"></i> Back</button>
							</center>
						</div>
					</td>
					<td>
						<?php
						if ($result_display) {
							?>
							<label name="test_summary<?php echo $testid; ?>" id="test_summary_label<?php echo $testid; ?>">
								<input type="checkbox" class="<?php echo $approve_cls; ?>"
									id="approve_test_summary<?php echo $testid; ?>" value="<?php echo $testid; ?>" <?php echo $approve_function; ?> 			<?php echo $doc_approve_disabled; ?> 			<?php echo $chk_box; ?>>Validate
							</label>
							<?php
						}
						?>
					</td>
					<td></td>
				</tr>
				<?php
				$i++;
			}

			if ($dept_id == 150) // Culture
			{
				//echo "<script>load_culture_data('$testid');</script>";
			}

			$slno++;
		}
		if ($slno == 1) // Only one test
		{
			if ($testid == 1227) // Widal
			{
				echo "<script>$('#pat_dept_test_params_header').hide();</script>";
			}
		}
		if ($dept_id == 150) // Culture
		{
			echo "<script>$('#pat_dept_test_params_header').hide();</script>";
		}
		?>
	</table>
	<input type="hidden" id="sel_dept_id" value="<?php echo $dept_id; ?>">

	<center>
		<?php
		// Flag
		$flag_btn_name = "Flag This Patient";
		$patient_flagged_details = mysqli_fetch_array(mysqli_query($link, "SELECT `cause` FROM `patient_flagged_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id'"));
		if ($patient_flagged_details)
		{
			if($flaggedPatient)
			{
			?>
			<button class="btn btn-success btn-mini" onclick="unflag_pat('<?php echo $patient_id;?>','<?php echo $opd_id;?>','<?php echo $ipd_id;?>','<?php echo $batch_no;?>','<?php echo $dept_id;?>')"><i class="icon-flag icon-large"></i> &nbsp; Un-Flag Patient</button>
			<?php
			}
			else
			{
			?>
			<button class="btn btn-delete btn-mini" onclick=""><i class="icon-flag"></i> Patient Flagged</button>
			<?php
			}
		} else {
			?>
			<button class="btn btn-new btn-mini" onclick="flag_patient()"><i class="icon-flag"></i> Flag This Patient</button>
			<?php
		}
		?>

		<button class="btn btn-back btn-mini" onclick="back_to_list()"><i class="icon-backward"></i> Back To List
			(ESC)</button>
	</center>
</div>
