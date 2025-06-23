<?php
include("../../includes/connection.php");

$testid = $_POST["testid"];
$test_info = mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$testid'"));

$rprt_del = explode("@", $test_info["turn_around_time_routine_str"]);
$rprt_del_day = $rprt_del[0];
$rprt_del = explode("#", $rprt_del[1]);
$rprt_del_hour = $rprt_del[0];
$rprt_del_minute = $rprt_del[1];


$rprt_del_max = explode("@", $test_info["turn_around_time_urgent_str"]);
$rprt_del_day_max = $rprt_del_max[0];
$rprt_del_max = explode("#", $rprt_del_max[1]);
$rprt_del_hour_max = $rprt_del_max[0];
$rprt_del_minute_max = $rprt_del_max[1];

if ($testid) {
	if ($test_info['category_id'] == 1) {
		$tr_style = "";
	} else {
		$tr_style = "display:none;";
	}
} else {
	$tr_style = "display:none;";

	$testid = 0;
}
?>
<div id="test_detail" style="padding:10px">
	<h4>Test Details</h4>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Test Name</th>
			<td><input type="text" id="testname" class="test_value span6"
					value="<?php echo $test_info["testname"]; ?>" /></td>
			<th>Test Code</th>
			<td>
				<input type="text" id="test_code" value="<?php echo $test_info["test_code"]; ?>">
			</td>
		</tr>
		<tr>
			<th>Category</th>
			<td>

				<select id="category_id" class="test_value" onchange="load_dept()">
					<?php
					$cat_qry = mysqli_query($link, "SELECT `category_id`, `name` FROM `test_category_master` WHERE `status`='0' ORDER BY `category_id` ASC");
					while ($cat = mysqli_fetch_array($cat_qry)) {
						if ($cat["category_id"] == $test_info["category_id"]) {
							$sel1 = "Selected='selected'";
						} else {
							$sel1 = "";
						}
						echo "<option value='$cat[category_id]' $sel1>$cat[name]</option>";
					}
					?>
				</select>
			</td>
			<th>Department</th>
			<td>
				<select id="type_id" class="test_value">
					<option value="0">Select</option>
					<?php
					if ($testid) {
						$dep = mysqli_query($link, "select id,name from test_department where `category_id`='$test_info[category_id]' order by name");
						while ($dept = mysqli_fetch_array($dep)) {
							if ($dept["id"] == $test_info["type_id"]) {
								$sel1 = "Selected='selected'";
							} else {
								$sel1 = "";
							}
							echo "<option value='$dept[id]' $sel1>$dept[name]</option>";
						}
					}
					?>
				</select>
			</td>

		</tr>
		<tr class="lab_tr" style="<?php echo $tr_style; ?>">
			<th>Instruction</th>
			<td colspan="1">
				<input type="text" size="70" id="instruction" value="<?php echo $test_info["instruction"]; ?>" />
			</td>
			<th>Instrument</th>
			<td>
				<select id="equipment">
					<?php
					$instrument_qry = mysqli_query($link, "SELECT `id`, `name` FROM `lab_instrument_master` WHERE `status`=0 ORDER  BY `name` ASC");
					while ($instrument = mysqli_fetch_array($instrument_qry)) {
						if ($instrument["id"] == $test_info["equipment"]) {
							$sel1 = "Selected='selected'";
						} else {
							$sel1 = "";
						}

						echo "<option value='$instrument[id]' $sel1>$instrument[name]</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr class="lab_tr" style="<?php echo $tr_style; ?>">
			<th>Turnaround Time</th>
			<th>TAT Routine</th> <!-- minimum -->
			<th colspan="2">TAT Emergency</th> <!-- maximum -->
		</tr>
		<tr class="lab_tr" style="<?php echo $tr_style; ?>">
			<td></td>
			<td>
				<select id="turn_day">
					<option value="0" selected="selected">Select Days</option>
					<option value="1" <?php if ($rprt_del_day == 1) {
						echo "selected";
					} ?>>1 Day</option>
					<option value="2" <?php if ($rprt_del_day == 2) {
						echo "selected";
					} ?>>2 Days</option>
					<option value="3" <?php if ($rprt_del_day == 3) {
						echo "selected";
					} ?>>3 Days</option>
					<option value="4" <?php if ($rprt_del_day == 4) {
						echo "selected";
					} ?>>4 Days</option>
					<option value="5" <?php if ($rprt_del_day == 5) {
						echo "selected";
					} ?>>5 Days</option>
					<option value="6" <?php if ($rprt_del_day == 6) {
						echo "selected";
					} ?>>6 Days</option>
					<option value="7" <?php if ($rprt_del_day == 7) {
						echo "selected";
					} ?>>7 Days</option>
				</select>
				<select id="turn_hour">
					<option value="0" selected="selected">Select Hours</option>
					<option value="1" <?php if ($rprt_del_hour == 1) {
						echo "selected";
					} ?>>1 Hour</option>
					<option value="2" <?php if ($rprt_del_hour == 2) {
						echo "selected";
					} ?>>2 Hours</option>
					<option value="3" <?php if ($rprt_del_hour == 3) {
						echo "selected";
					} ?>>3 Hours</option>
					<option value="4" <?php if ($rprt_del_hour == 4) {
						echo "selected";
					} ?>>4 Hours</option>
					<option value="5" <?php if ($rprt_del_hour == 5) {
						echo "selected";
					} ?>>5 Hours</option>
					<option value="6" <?php if ($rprt_del_hour == 6) {
						echo "selected";
					} ?>>6 Hours</option>
					<option value="7" <?php if ($rprt_del_hour == 7) {
						echo "selected";
					} ?>>7 Hours</option>
					<option value="8" <?php if ($rprt_del_hour == 8) {
						echo "selected";
					} ?>>8 Hours</option>
					<option value="9" <?php if ($rprt_del_hour == 9) {
						echo "selected";
					} ?>>9 Hours</option>
					<option value="10" <?php if ($rprt_del_hour == 10) {
						echo "selected";
					} ?>>10 Hours</option>
					<option value="11" <?php if ($rprt_del_hour == 11) {
						echo "selected";
					} ?>>11 Hours</option>
					<option value="12" <?php if ($rprt_del_hour == 12) {
						echo "selected";
					} ?>>12 Hours</option>
				</select>
				<select id="turn_minute">
					<option value="0" selected="selected">Select Minutes</option>
					<option value="10" <?php if ($rprt_del_minute == 10) {
						echo "selected";
					} ?>>10 Minutes</option>
					<option value="15" <?php if ($rprt_del_minute == 15) {
						echo "selected";
					} ?>>15 Minutes</option>
					<option value="20" <?php if ($rprt_del_minute == 20) {
						echo "selected";
					} ?>>20 Minutes</option>
					<option value="30" <?php if ($rprt_del_minute == 30) {
						echo "selected";
					} ?>>30 Minutes</option>
					<option value="45" <?php if ($rprt_del_minute == 45) {
						echo "selected";
					} ?>>45 Minutes</option>
					<option value="50" <?php if ($rprt_del_minute == 50) {
						echo "selected";
					} ?>>50 Minutes</option>
				</select>
			</td>

			<td colspan="2">
				<select id="max_turn_day">
					<option value="0" selected="selected">Select Days</option>
					<option value="1" <?php if ($rprt_del_day_max == 1) {
						echo "selected";
					} ?>>1 Day</option>
					<option value="2" <?php if ($rprt_del_day_max == 2) {
						echo "selected";
					} ?>>2 Days</option>
					<option value="3" <?php if ($rprt_del_day_max == 3) {
						echo "selected";
					} ?>>3 Days</option>
					<option value="4" <?php if ($rprt_del_day_max == 4) {
						echo "selected";
					} ?>>4 Days</option>
					<option value="5" <?php if ($rprt_del_day_max == 5) {
						echo "selected";
					} ?>>5 Days</option>
					<option value="6" <?php if ($rprt_del_day_max == 6) {
						echo "selected";
					} ?>>6 Days</option>
					<option value="7" <?php if ($rprt_del_day_max == 7) {
						echo "selected";
					} ?>>7 Days</option>
				</select>
				<select id="max_turn_hour">
					<option value="0" selected="selected">Select Hours</option>
					<option value="1" <?php if ($rprt_del_hour_max == 1) {
						echo "selected";
					} ?>>1 Hour</option>
					<option value="2" <?php if ($rprt_del_hour_max == 2) {
						echo "selected";
					} ?>>2 Hours</option>
					<option value="3" <?php if ($rprt_del_hour_max == 3) {
						echo "selected";
					} ?>>3 Hours</option>
					<option value="4" <?php if ($rprt_del_hour_max == 4) {
						echo "selected";
					} ?>>4 Hours</option>
					<option value="5" <?php if ($rprt_del_hour_max == 5) {
						echo "selected";
					} ?>>5 Hours</option>
					<option value="6" <?php if ($rprt_del_hour_max == 6) {
						echo "selected";
					} ?>>6 Hours</option>
					<option value="7" <?php if ($rprt_del_hour_max == 7) {
						echo "selected";
					} ?>>7 Hours</option>
					<option value="8" <?php if ($rprt_del_hour_max == 8) {
						echo "selected";
					} ?>>8 Hours</option>
					<option value="9" <?php if ($rprt_del_hour_max == 9) {
						echo "selected";
					} ?>>9 Hours</option>
					<option value="10" <?php if ($rprt_del_hour_max == 10) {
						echo "selected";
					} ?>>10 Hours</option>
					<option value="11" <?php if ($rprt_del_hour_max == 11) {
						echo "selected";
					} ?>>11 Hours</option>
					<option value="12" <?php if ($rprt_del_hour_max == 12) {
						echo "selected";
					} ?>>12 Hours</option>
				</select>
				<select id="max_turn_minute">
					<option value="0" selected="selected">Select Minutes</option>
					<option value="10" <?php if ($rprt_del_minute_max == 10) {
						echo "selected";
					} ?>>10 Minutes</option>
					<option value="15" <?php if ($rprt_del_minute_max == 15) {
						echo "selected";
					} ?>>15 Minutes</option>
					<option value="20" <?php if ($rprt_del_minute_max == 20) {
						echo "selected";
					} ?>>20 Minutes</option>
					<option value="30" <?php if ($rprt_del_minute_max == 30) {
						echo "selected";
					} ?>>30 Minutes</option>
					<option value="45" <?php if ($rprt_del_minute_max == 45) {
						echo "selected";
					} ?>>45 Minutes</option>
					<option value="50" <?php if ($rprt_del_minute_max == 50) {
						echo "selected";
					} ?>>50 Minutes</option>
				</select>
			</td>
		</tr>
		</tr>
		<tr class="lab_tr" style="<?php echo $tr_style; ?>">
			<th>Report Delivery</th>
			<th colspan='3'>
				<select id="report_delivery_2">
					<?php
					for ($i = 0; $i <= 30; $i++) {
						if ($i == 0) {
							$delv_opt = "Same Day";
						} else if ($i == 1) {
							$delv_opt = "2nd Day";
						} else if ($i == 2) {
							$delv_opt = "3rd Day";
						} else {
							$delv_opt = ($i + 1) . "th Day";
						}

						if ($i == $test_info["report_delivery_2"]) {
							$delv_sel = "selected";
						} else {
							$delv_sel = "";
						}

						echo "<option value='$i' $delv_sel>$delv_opt</option>";
					}
					?>
				</select>
			</th>
		</tr>

		<tr class="lab_tr" style="<?php echo $tr_style; ?>">
			<th style="display:none;">Sample</th>
			<td style="display:none;">
				<select id="sample_details">
					<?php
					$samp_pr = mysqli_fetch_array(mysqli_query($link, "select SampleId from TestSample where TestId='$testid'"));
					$sam = mysqli_query($link, "select * from  Sample order by Name");
					while ($s = mysqli_fetch_array($sam)) {
						if ($samp_pr["SampleId"] == $s["ID"]) {
							$sel = "Selected";
						} else {
							$sel = '';
						}
						echo "<option value='$s[ID]' $sel>$s[Name]</option>";
					}
					?>
				</select>
			</td>
			<th>Out Sample</th>
			<td colspan="3">
				<select id="out_sample">
					<option value="0" <?php if ($test_info["out_sample"] == 0) {
						echo "selected";
					} ?>>No</option>
					<option value="1" <?php if ($test_info["out_sample"] == 1) {
						echo "selected";
					} ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr class="lab_trXXX" style="display:none;">
			<th colspan="4">Vaccu</th>
		</tr>

		<?php
		$tot_vaccu = 1;
		//$vac=mysqli_query($link, "select * from vaccu_master");
		while ($v = mysqli_fetch_array($vac)) {
			$num_v = mysqli_num_rows(mysqli_query($link, "select * from test_vaccu where testid='$testid' and vac_id='$v[id]'"));
			if ($num_v > 0) {
				$chk_v = "checked='checked'";
			} else {
				$chk_v = "";
			}
			if ($tot_vaccu == 1) {
				echo "<tr class='lab_tr' style='" . $tr_style . "'>";
			}
			echo "<td><label><input type='checkbox' id='$v[id]' class='vaccu_cl' $chk_v>$v[type]</label></td>";
			if ($tot_vaccu == 4) {
				echo "</tr>";
				$tot_vaccu = 1;
			} else {
				$tot_vaccu++;
			}
		}
		?>
		<tr>
			<th>Rate</th>
			<td><input type="text" id="rate" class="test_value" value="<?php echo $test_info["rate"]; ?>" /></td>


			<th>Select Sex</th>
			<td>
				<select id="sex" class="test_value">
					<option value="all">All</option>
					<option value="M" <?php if ($test_info[sex] == "M") {
						echo "selected='selected'";
					} else {
						echo "";
					}
					; ?>>
						Male</option>
					<option value="F" <?php if ($test_info[sex] == "F") {
						echo "selected='selected'";
					} else {
						echo "";
					}
					; ?>>
						Female</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="text-align:center">
				<input type="button" id="save" value="<?php if ($testid > 0) {
					echo 'Update';
				} else {
					echo 'Save';
				} ?>" class="btn btn-default" onclick="save_test('<?php echo $testid; ?>')" />
				<input type="button" id="clse" value="Close" class="btn btn-danger" onclick="$('#mod').click()" />
			</td>

		</tr>
	</table>
	<style>
		label {
			display: inline-block;
		}
	</style>
</div>