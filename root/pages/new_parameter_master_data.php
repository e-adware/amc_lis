<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date = date("Y-m-d");
$time = date("H:i:s");

$user = $_POST["user"];

$instrument_str = "display:none;";
if ($instrument_wise_normal_range == 1) {
	$instrument_str = "";
}

if ($_POST["type"] == "load_param_list") {
	$param_name = $_POST["param_name"];
	$testid = $_POST["testid"];
	$ResultTypeId = $_POST["ResultTypeId"];

	$str = "SELECT * FROM `Parameter_old` WHERE `Name`!=''";
	
	if($ResultTypeId!="")
	{
		$str .= " AND `ResultType` = '$ResultTypeId'";
	}
	
	if (strlen($param_name) > 1) {
		$str .= " AND `Name` LIKE '%$param_name%'";
	}
	$str .= " AND `ResultOptionID`!='68'"; // Anti-Biotics
	$str .= " ORDER BY `Name` ASC";

	if ($testid > 0) {
		$str = "SELECT a.* FROM `Parameter_old` a, `Testparameter` b WHERE a.ID=b.ParamaterId AND b.TestId='$testid' AND a.`ResultOptionID`!='68' ORDER BY b.sequence ASC";
	}

	$qry = mysqli_query($link, $str);
	?>
	<table class="table table-condensed table-hover table-bordered" style="background-color: white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>ID</th>
				<th>Name</th>
				<th>Sample</th>
				<th>Vaccu</th>
				<th>Unit</th>
				<th>Result Type</th>
				<th>Normal Range</th>
				<th style="width: 40% !important;">Test(s)</th>
				<th></th>
			</tr>
		</thead>
		<?php
		$n = 1;
		while ($data = mysqli_fetch_array($qry)) {
			$sample_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `Sample` WHERE `ID`='$data[sample]' "));
			$vaccu_info = mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `vaccu_master` WHERE `id`='$data[vaccu]' "));
			$unit_info = mysqli_fetch_array(mysqli_query($link, " SELECT `unit_name` FROM `Units` WHERE `ID`='$data[UnitsID]' "));
			$ResultType_info = mysqli_fetch_array(mysqli_query($link, " SELECT `ResultType_name` FROM `ResultType` WHERE `ResultTypeId`='$data[ResultType]' "));
			
			$ResultType_color="";
			if($data["ResultType"]==6) // Formula
			{
				$chk_formula=mysqli_fetch_assoc(mysqli_query($link, "SELECT `formula` FROM `parameter_formula` WHERE `ParameterID`='$data[ID]'"));
				if($chk_formula["formula"])
				{
					$ResultType_color="color:green;";
				}else
				{
					$ResultType_color="color:red;";
				}
			}
		?>
			<tr onclick="load_param('<?php echo $data["ID"] ?>')" style="cursor:pointer;">
				<td><?php echo $n; ?></td>
				<td><?php echo $data["ID"]; ?></td>
				<td><?php echo $data["Name"]; ?></td>
				<td><?php echo $sample_info["Name"]; ?></td>
				<td><?php echo $vaccu_info["type"]; ?></td>
				<td><?php echo $unit_info["unit_name"]; ?></td>
				<td style="<?php echo $ResultType_color; ?>"><?php echo $ResultType_info["ResultType_name"]; ?></td>
				<td style="text-align:center;">
			<?php
				$image_src="";
				// Normal Range
				$chk_range=mysqli_fetch_assoc(mysqli_query($link, "SELECT `normal_range` FROM `parameter_normal_check` WHERE `parameter_id`='$data[ID]' LIMIT 1"));
				if($chk_range)
				{
					$image_src="<img src='../images/right.png' style='width: 20px;'>";
				}
				
				echo $image_src;
			?>
				</td>
				<td>
					<?php
					$param_test_str = "SELECT a.testname FROM `testmaster` a, `Testparameter` b WHERE a.testid=b.TestId AND b.ParamaterId='$data[ID]'";
					if ($testid > 0) {
						$param_test_str .= " AND a.`testid`='$testid'";
					}
					$param_test_qry = mysqli_query($link, $param_test_str);
					while ($param_test = mysqli_fetch_array($param_test_qry)) {
						echo "<span style='background-color:#dddddd91;'>" . $param_test["testname"] . "</span> &nbsp; ";
					}
					?>
				</td>
			</tr>
			<?php
			$n++;
			
			// Test Param Sample Vaccu Chk
			/*$testParamChk=mysqli_fetch_assoc(mysqli_query($link, "SELECT `sample`,`vaccu` FROM `Testparameter` WHERE `ParamaterId`='$data[ID]' AND `sample`!='0' AND `vaccu`!='0' ORDER BY `slno` DESC"));
			if($testParamChk)
			{
				mysqli_query($link, "UPDATE `Parameter_old` SET `sample`='$testParamChk[sample]',`vaccu`='$testParamChk[vaccu]' WHERE `ID`='$data[ID]'");
			}*/
		}
		?>
	</table>
	<?php
}

if ($_POST["type"] == "load_param") {
	$paramid = $_POST["paramid"];
	if (!$paramid) {
		$paramid = 0;
	}

	$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument` FROM `Parameter_old` WHERE `ID`='$paramid'"));

	if (!$param_info["ResultOptionID"]) {
		$param_info["ResultOptionID"] = 0;
	}
	$option_info = mysqli_fetch_array(mysqli_query($link, "SELECT `id`, `name` FROM `ResultOption` WHERE `id`='$param_info[ResultOptionID]' "));

	$td_display = "";
	if ($paramid == 0) {
		$td_display = "display:none;";
	}
	?>
	<br>
	<br>
	<table class="table table-condensed table-hover table-bordered" style="background-color: white;">
		<tr>
			<th>Parameter Name</th>
			<td>
				<input type="text" id="Name" name="Name" value="<?php echo $param_info["Name"]; ?>">
			</td>
			<th>Result Type</th>
			<td>
				<select id="ResultType" name="ResultType">
					<?php
					$qry = mysqli_query($link, "SELECT `ResultTypeId`, `ResultType_name` FROM `ResultType` WHERE `ResultType_name`!='' ORDER BY `ResultTypeId` ASC");
					while ($data = mysqli_fetch_array($qry)) {
						if ($param_info && $paramid > 0) {
							if ($data["ResultTypeId"] == $param_info["ResultType"]) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						} else {
							if ($data["ResultTypeId"] == 1) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						}
						echo "<option value='$data[ResultTypeId]' $sel>$data[ResultType_name]</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Unit</th>
			<td>
				<select id="UnitsID" name="UnitsID">
					<?php
					$qry = mysqli_query($link, "SELECT `ID`, `unit_name` FROM `Units` ORDER BY `unit_name` ASC");
					while ($data = mysqli_fetch_array($qry)) {
						if ($param_info) {
							if ($data["ID"] == $param_info["UnitsID"]) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						} else {
							if ($data["ID"] == 0) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						}
						echo "<option value='$data[ID]' $sel>$data[unit_name]</option>";
					}
					?>
				</select>
				<button class="btn btn-new btn-mini" onclick="new_unit()"><i class="icon-plus"></i> New</button>
			</td>
			<th>Options</th>
			<td>
				<input type="hidden" id="ResultOptionID" name="ResultOptionID"
					value="<?php echo $param_info["ResultOptionID"]; ?>">
				<input type="text" id="optionName" name="optionName" value="<?php echo $option_info["name"]; ?>"
					onclick="load_option_list('<?php echo $paramid; ?>','<?php echo $param_info["ResultOptionID"]; ?>')"
					placeholder="Click to view/set" readonly>
			</td>
		</tr>
		<tr>
			<th>Sample</th>
			<td>
				<select id="sample" name="sample" onchange="load_sample_vaccu(this)">
					<option value="0">Select</option>
					<?php
					$qry = mysqli_query($link, "SELECT `ID`, `Name` FROM `Sample` ORDER BY `Name` ASC");
					while ($data = mysqli_fetch_array($qry)) {
						if ($param_info) {
							if ($data["ID"] == $param_info["sample"]) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						} else {
							if ($data["ID"] == 1) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						}
						echo "<option value='$data[ID]' $sel>$data[Name]</option>";
					}
					?>
				</select>
			</td>
			<th>Vaccu</th>
			<td>
				<select id="vaccu" name="vaccu">
					<option value="0">Select</option>
					<?php
					if($param_info)
					{
						$q=mysqli_query($link,"SELECT `vacc_id` FROM `sample_vaccu` WHERE `samp_id`='$param_info[sample]'");
						while($r=mysqli_fetch_array($q))
						{
							$vacName=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `vaccu_master` WHERE `id`='$r[vacc_id]'"));
							$sel="";
							if ($r["vacc_id"] == $param_info["vaccu"])
							{
								$sel = "selected";
							}
							echo "<option value='$r[vacc_id]' $sel>$vacName[type]</option>";
						}
					}
					//~ $qry = mysqli_query($link, "SELECT `id`, `type` FROM `vaccu_master` ORDER BY `type` ASC");
					//~ while ($data = mysqli_fetch_array($qry)) {
						//~ if ($param_info) {
							//~ if ($data["id"] == $param_info["vaccu"]) {
								//~ $sel = "selected";
							//~ } else {
								//~ $sel = "";
							//~ }
						//~ } else {
							//~ if ($data["id"] == 0) {
								//~ $sel = "selected";
							//~ } else {
								//~ $sel = "";
							//~ }
						//~ }
						//~ echo "<option value='$data[id]' $sel>$data[type]</option>";
					//~ }
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Method</th>
			<td>
				<select id="method" name="method">
					<option value="0">Select</option>
					<?php
					$qry = mysqli_query($link, "SELECT `id`, `name` FROM `test_methods` ORDER BY `name` ASC");
					while ($data = mysqli_fetch_array($qry)) {
						if ($param_info) {
							if ($data["id"] == $param_info["method"]) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						} else {
							if ($data["id"] == 0) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						}
						echo "<option value='$data[id]' $sel>$data[name]</option>";
					}
					?>
				</select>
			</td>
			<th style="<?php echo $td_display; ?>">Formula</th>
			<td style="<?php echo $td_display; ?>">
				<a class="btn btn-link" onclick="load_formula_div('<?php echo $paramid; ?>')">Click to set/view</a>
			</td>
			<?php
			if ($td_display != "") {
				echo "<td></td><td></td>";
			}
			?>
		</tr>
		<tr style="<?php echo $td_display; ?>">
			<th>Normal Range</th>
			<td>
				<a class="btn btn-link" onclick="load_normal_range_div('<?php echo $paramid; ?>',1)">Click to set/view</a>
			</td>
			<th style="display:none;">Critical Value</th>
			<td style="display:none;">
				<a class="btn btn-link" onclick="load_formula_div('<?php echo $paramid; ?>')">Click to set/view</a>
			</td>
		</tr>
		<tr>
			<th>Decimal Value</th>
			<td colspan="3">
				<select id="deci_val" name="deci_val">
					<option value="0" <?php if ($param_info["deci_val"] == 0) {
						echo "selected";
					} ?>>0</option>
					<option value="1" <?php if ($param_info["deci_val"] == 1) {
						echo "selected";
					} ?>>1</option>
					<option value="2" <?php if ($param_info["deci_val"] == 2) {
						echo "selected";
					} ?>>2</option>
					<option value="3" <?php if ($param_info["deci_val"] == 3) {
						echo "selected";
					} ?>>3</option>
					<option value="4" <?php if ($param_info["deci_val"] == 4) {
						echo "selected";
					} ?>>4</option>
					<option value="5" <?php if ($param_info["deci_val"] == 5) {
						echo "selected";
					} ?>>5</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="text-align:center;">
				<button class="btn btn-save" onclick="save_param('<?php echo $paramid; ?>')"><i class="icon-save"></i>
					Save</button>

				<button class="btn btn-reset" onClick="load_param('<?php echo $paramid; ?>')"><i class="icon-refresh"></i>
					Reset</button>
				
				<button class="btn btn-delete" onclick="delete_param('<?php echo $paramid; ?>')"><i class="icon-trash"></i> Delete</button>
				
				<button class="btn btn-back" onclick="back_to_list()"><i class="icon-backward"></i> Back</button>
			</td>
		</tr>
	</table>
	<?php
}

// Unit Start
if ($_POST["type"] == "new_unit") {
	?>
	<table class="table table-condensed table-hover table-bordered" style="background-color: white;">
		<tr>
			<th>Unit Name</th>
			<td>
				<input type="text" id="unit_name" name="unit_name">
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<button class="btn btn-save" id="unit_save_btn" onclick="unit_save()"><i class="icon-save"></i>
					Save</button>
				<button class="btn btn-close" id="unit_close_btn" data-dismiss="modal"><i class="icon-off"></i>
					Close</button>
			</td>
		</tr>
	</table>
	<?php
}
if ($_POST["type"] == "unit_save") {
	$unit_name = mysqli_real_escape_string($link, $_POST["unit_name"]);

	$options = "";

	if ($unit_name) {
		if (mysqli_query($link, "INSERT INTO `Units`(`unit_name`) VALUES ('$unit_name')")) {
			$last_entry = mysqli_fetch_array(mysqli_query($link, "SELECT `ID` FROM `Units` WHERE `unit_name`='$unit_name' ORDER BY `ID` DESC"));

			$qry = mysqli_query($link, "SELECT `ID`, `unit_name` FROM `Units` ORDER BY `unit_name` ASC");
			while ($data = mysqli_fetch_array($qry)) {
				if ($data["ID"] == $last_entry["ID"]) {
					$sel = "selected";
				} else {
					$sel = "";
				}
				$options .= "<option value='$data[ID]' $sel>$data[unit_name]</option>";
			}
		}
	}

	echo $options;
}
// Unit End

// Option Start
if ($_POST["type"] == "load_option_list") {
	$paramid = $_POST["paramid"];
	if (!$paramid) {
		$paramid = 0;
	}

	$ResultOptionID = $_POST["ResultOptionID"];
	if (!$ResultOptionID) {
		$ResultOptionID = 0;
	}

	$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name`,`ResultOptionID` FROM `Parameter_old` WHERE `ID`='$paramid'"));

	$ResultOptionID = $param_info["ResultOptionID"];
	?>
	<div>
		<b>Parameter Name: </b>
		<?php
		echo $param_info["Name"];
		?>
	</div>
	<table class="table table-condensed table-hover table-bordered" style="background-color: white;">
		<tr>
			<th>Option Name</th>
			<td>
				<select id="optionList" class="span4" onchange="show_option(this.value)">
					<option value="0">Select</option>
					<?php
					$qry = mysqli_query($link, "SELECT `id`, `name` FROM `ResultOption` WHERE `name`!='' ORDER BY `name` ASC");
					while ($data = mysqli_fetch_array($qry)) {
						?>
						<option value="<?php echo $data['id']; ?>" <?php if ($data['id'] == $ResultOptionID) {
							   echo "selected";
						   } ?>>
							<?php echo $data['name']; ?>
						</option>
						<?php
					}
					?>
				</select>
			</td>
			<td>
				<div style="" id="option_val"></div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align:center;">
				<button class="btn btn-save" id="result_option_save_btn"
					onclick="result_option_save('<?php echo $paramid; ?>')"><i class="icon-save"></i> Save</button>
				<button class="btn btn-close" id="result_option_close_btn" data-dismiss="modal"><i class="icon-off"></i>
					Close</button>
			</td>
			<td></td>
		</tr>
	</table>
	<?php
}
if ($_POST["type"] == "show_option") {
	$ResultOptionID = $_POST["ResultOptionID"];
	if (!$ResultOptionID) {
		$ResultOptionID = 0;
	}

	$opts = mysqli_query($link, "select * from ResultOptions where id='$ResultOptionID'");
	$opts_num = mysqli_num_rows($opts);
	if ($opts_num > 0) {
		echo "<div style='font-size:13px;font-weight:bold;text-decoration: underline;'>Option list</div>";
	}
	while ($opt = mysqli_fetch_array($opts)) {
		$nm = mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$opt[optionid]'"));
		echo "<div id='$opt[optionid]' class='options'>$nm[name]</div>";
	}
}

if ($_POST["type"] == "result_option_save") {
	$paramid = $_POST["paramid"];
	$ResultOptionID = $_POST["optionList"];

	if ($paramid > 0) {
		if (mysqli_query($link, "UPDATE `Parameter_old` SET `ResultOptionID`='$ResultOptionID' WHERE `ID`='$paramid'")) {
			echo "Saved";
		} else {
			echo "Failed, try again later.";
		}
	} else {
		echo "Added";
	}
}
// Option End

if ($_POST["type"] == "save_param") {
	$paramid = mysqli_real_escape_string($link, $_POST["paramid"]);
	$Name = mysqli_real_escape_string($link, $_POST["Name"]);
	$ResultType = mysqli_real_escape_string($link, $_POST["ResultType"]);
	$UnitsID = mysqli_real_escape_string($link, $_POST["UnitsID"]);
	$ResultOptionID = mysqli_real_escape_string($link, $_POST["ResultOptionID"]);
	$sample = mysqli_real_escape_string($link, $_POST["sample"]);
	$vaccu = mysqli_real_escape_string($link, $_POST["vaccu"]);
	$method = mysqli_real_escape_string($link, $_POST["method"]);
	$deci_val = mysqli_real_escape_string($link, $_POST["deci_val"]);

	if (!$ResultType) {
		$ResultType = 0;
	}
	if (!$UnitsID) {
		$UnitsID = 0;
	}
	if (!$ResultOptionID) {
		$ResultOptionID = 0;
	}
	if (!$sample) {
		$sample = 0;
	}
	if (!$vaccu) {
		$vaccu = 0;
	}
	if (!$method) {
		$method = 0;
	}
	if (!$deci_val) {
		$deci_val = 0;
	}
	if (!$sub_title) {
		$sub_title = 0;
	}
	if (!$instrument) {
		$instrument = 0;
	}

	$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument` FROM `Parameter_old` WHERE `ID`='$paramid'"));

	if ($param_info && $paramid > 0) {
		$old = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `Parameter_old` WHERE `ID`='$paramid'"));

		if (mysqli_query($link, "UPDATE `Parameter_old` SET `ResultType`='$ResultType',`Name`='$Name',`ResultOptionID`='$ResultOptionID',`UnitsID`='$UnitsID',`sample`='$sample',`vaccu`='$vaccu',`method`='$method',`deci_val`='$deci_val' WHERE `ID`='$paramid'")) {
			mysqli_query($link, "UPDATE `Testparameter` SET `sample`='$sample',`vaccu`='$vaccu' WHERE `ParamaterId`='$paramid'");

			echo $paramid . "@101@Updated";

			if ($master_changes_record == 1) {
				mysqli_query($link, "INSERT INTO `Parameter_old_changes`(`ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument`, `process`, `date`, `time`, `user`) VALUES ('$old[ID]', '$old[ResultType]', '$old[Name]', '$old[ResultOptionID]', '$old[UnitsID]', '$old[sample]', '$old[vaccu]', '$old[method]', '$old[deci_val]', '$old[sub_title]', '$old[instrument]', 'UPDATE', '$date', '$time', '$user')");

			}
		} else {
			echo $paramid . "@102@Failed, try again later.";
		}
	} else {

		if (mysqli_query($link, "INSERT INTO `Parameter_old`(`ID`,`ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument`) VALUES ('$paramid','$ResultType','$Name','$ResultOptionID','$UnitsID','$sample','$vaccu','$method','$deci_val','$sub_title','$instrument')")) {
			$last_entry = mysqli_fetch_array(mysqli_query($link, "SELECT `ID` FROM `Parameter_old` WHERE `ResultType`='$ResultType' AND `Name`='$Name' AND `UnitsID`='$UnitsID' AND `sample`='$sample' AND `vaccu`='$vaccu' ORDER BY `ID` DESC LIMIT 1"));

			$paramid = $last_entry["ID"];

			echo $paramid . "@101@Saved";

			if ($master_changes_record == 1) {
				// $last_ids = mysqli_fetch_array(mysqli_query($link, "SELECT `ID` FROM `Parameter_old` ORDER BY `ID` DESC LIMIT 1"));
				// $last_id = $last_ids['ID'];

				mysqli_query($link, "INSERT INTO `Parameter_old_changes`(`ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument`, `process`, `date`, `time`, `user`) VALUES ('$paramid','$ResultType','$Name','$ResultOptionID','$UnitsID','$sample','$vaccu','$method','$deci_val','$sub_title','$instrument','NEW ENTRY','$date','$time','$user')");
			}

		} else {
			echo $paramid . "@102@Failed, try again later";
		}
	}
}

// Formula Start
if ($_POST["type"] == "load_formula_div") {
	$paramid = $_POST["paramid"];

	$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument` FROM `Parameter_old` WHERE `ID`='$paramid'"));
	?>
	<div>
		<b>Set Formula for: </b>
		<?php
		echo $param_info["Name"];
		?>
	</div>
	<table class="table table-condensed table-hover table-bordered" style="background-color: white;">
		<tr>
			<td>
				<b>Select Parameter</b><br>
				<select id="parm">
					<option value="0">--Select--</option>
					<?php
					$qry = mysqli_query($link, "SELECT `ID`,`Name` FROM `Parameter_old` WHERE `ID`!='$paramid' ORDER BY `Name` ASC");
					while ($data = mysqli_fetch_array($qry)) {
						echo "<option value='$data[ID]'>$data[Name]  = $data[ID]</option>";
					}
					?>
				</select>
				<button id="add_p" value="Add" class="btn btn-primary btn-sm" onclick="add_element(this.id)">Add</button>
			</td>
			<td>
				<b>Add Operator</b><br>
				<input type="text" id="opr" onkeyup="check_op(this)" />
				<button id="add_op" value="Add" class="btn btn-primary btn-sm" onclick="add_element(this.id)">Add</button>
			</td>
			<td>
				<b>Add Numeric Value </b><br>
				<input type="text" id="num" onkeyup="check_num(this)" />
				<button id="add_num" value="Add" class="btn btn-primary btn-sm" onclick="add_element(this.id)">Add</button>
				</th>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<b>Formula</b><br />
				<div style="min-height:50px;max-height:60px;overflow-y:scroll;" id="formula_text">
					<?php
					$dec = 0;
					$form = mysqli_fetch_array(mysqli_query($link, "select * from parameter_formula where ParameterID='$paramid'"));

					if ($form["formula"]) {
						$formula = "";
						$val = explode("@", $form["formula"]);

						foreach ($val as $v) {
							$chk_par = explode("p", $v);
							if ($chk_par[1]) {
								ob_start();
								?>
								<select class="formula" id="" name="param">
									<option value="0">--Select--</option>
									<?php
									$qry = mysqli_query($link, "SELECT `ID`,`Name` FROM `Parameter_old` WHERE `ID`!='$paramid' ORDER BY `Name` ASC");
									while ($data = mysqli_fetch_array($qry)) {
										if ($data["ID"] == $chk_par[1]) {
											$sel = "Selected='selected'";
										} else {
											$sel = "";
										}
										echo "<option value='$data[ID]' $sel>$data[Name]  = $data[ID]</option>";
									}
									?>
								</select>
								<?php
								$formula .= ob_get_clean();
							} else {
								if (!is_numeric($v)) {
									$formula .= "<input type='text' value='$v' name='operator' class='formula span1' maxlength='1' size='1'/>";
								} else {
									$formula .= "<input type='text' value='$v' name='numeric' class='formula span1' size='3'/>";
								}
							}

						}

						echo $formula;
						$dec = $form["res_dec"];
					}
					?>
				</div>
				Value After Decimal Point <input type="text" id="dec" value="<?php echo $dec; ?>" size="3" /> <br /><br />
			</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align:center;">
				<button class="btn btn-save" id="result_option_save_btn"
					onclick="save_formula('<?php echo $paramid; ?>')"><i class="icon-save"></i> Save</button>
				<button id="clear" class="btn btn-reset btn-sm"
					onclick="$('#formula_text').html('');$('[value=Add]').attr('disabled',false)"><i
						class="icon-remove"></i> Clear</button>
				<button class="btn btn-close" id="result_option_close_btn" data-dismiss="modal"><i class="icon-off"></i>
					Close</button>
			</td>
			<td></td>
		</tr>
	</table>
	<?php
}
if ($_POST["type"] == "save_formula") {
	$paramid = $_POST["paramid"];
	$formula = $_POST["formula"];
	$res_dec = $_POST["res_dec"];

	$parameter_formula = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `parameter_formula` WHERE `ParameterID`='$paramid'"));

	mysqli_query($link, "DELETE FROM `parameter_formula` WHERE ParameterID='$paramid'");

	if ($formula) {
		if (mysqli_query($link, "INSERT INTO `parameter_formula`(`ParameterID`, `formula`, `res_dec`) VALUES ('$paramid','$formula','$res_dec')")) {
			if ($parameter_formula) {
				echo "101@Updated";
			} else {
				echo "101@Saved";
			}
		} else {
			echo "102@Failed, try again later.";
		}
	} else {
		if ($parameter_formula) {
			echo "102@Deleted";
		} else {
			echo "102@Nothing to save";
		}
	}
}
if ($_POST["type"] == "delete_formula") {
	$paramid = $_POST["paramid"];

	mysqli_query($link, "DELETE FROM `parameter_formula` WHERE ParameterID='$paramid'");
}

// Formula End


// Normal Range Start
if ($_POST["type"] == "load_normal_range_div") {
	$paramid = $_POST["paramid"];

	$param_info = mysqli_fetch_array(mysqli_query($link, "SELECT `ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`, `sub_title`, `instrument` FROM `Parameter_old` WHERE `ID`='$paramid'"));
	?>
	<div>
		<b>Parameter Name : </b>
		<?php
		echo $param_info["Name"];
		?>
		<div style="text-align:right;">
			<button id="new_range" class="btn btn-new btn-mini" onclick="normal_add('<?php echo $paramid; ?>','0')"><i
					class="icon-plus"></i> Add New</button>

			<button class="btn btn-edit btn-mini" id="in_active_range_btn" onclick="show_all_ranges(1)"><i
					class="icon-eye-open"></i> Show In-Active Range(s)</button>
			<button class="btn btn-edit btn-mini" id="active_range_btn" onclick="show_all_ranges(0)"
				style="display:none;"><i class="icon-eye-close"></i> Show Active Range(s)</button>
		</div>
	</div>
	<div id="normal_range_form_div">

	</div>
	<div id="normal_range_list_div">
		<table class="table table-bordered table-condensed">
			<tr>
				<th>#</th>
				<th style="<?php echo $instrument_str; ?>">Instrument</th>
				<th>Depends On</th>
				<th>Age From</th>
				<th>Age To</th>
				<th>Sex</th>
				<th>Min Value</th>
				<th>Max Value</th>
				<th>Display</th>
				<th></th>
			</tr>
			<?php
			$i = 1;
			$normal_check_qry = mysqli_query($link, "select * from parameter_normal_check where parameter_id='$paramid'");
			while ($normal_check = mysqli_fetch_array($normal_check_qry)) {
				$dep = mysqli_fetch_array(mysqli_query($link, "select name from DependentType where id='$normal_check[dep_id]'"));

				$instrument_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id`!=0 AND `id`='$normal_check[instrument_id]'"));

				//if ($normal_check["dep_id"] == 1 || $normal_check["dep_id"] == 2) {
				if (1==1) {
					if ($normal_check["age_from"] < 30) {
						$age_f = $normal_check["age_from"] . " Days";
					} else if ($normal_check["age_from"] >= 30 && $normal_check["age_from"] < 365) {
						$age_f = round($normal_check["age_from"] / 30);
						$age_f = $age_f . " Months";
					} else if ($normal_check["age_from"] >= 365) {
						$age_f = round($normal_check["age_from"] / 365);
						$age_f = $age_f . " Years";
					}

					if ($normal_check["age_to"] < 30) {
						$age_t = $normal_check["age_to"] . " Days";
					} else if ($normal_check["age_to"] >= 30 && $normal_check["age_to"] < 365) {
						$age_t = round($normal_check["age_to"] / 30);
						$age_t = $age_t . " Months";
					} else if ($normal_check["age_to"] >= 365) {
						$age_t = round($normal_check["age_to"] / 365);
						$age_t = $age_t . " Years";
					}
				}

				$tr_color = "";
				if ($normal_check["status"] == 1) {
					$tr_color = "color:red;";
				}
				?>
				<tr class="norm_stat_<?php echo $normal_check["status"]; ?>" style="<?php echo $tr_color; ?>">
					<td><?php echo $i; ?></td>
					<td style="<?php echo $instrument_str; ?>"><?php echo $instrument_info["name"]; ?></td>
					<td><?php echo $dep["name"]; ?></td>
					<td><?php echo $age_f; ?></td>
					<td><?php echo $age_t; ?></td>
					<td><?php if ($normal_check["sex"] != '0') {
						echo $normal_check["sex"];
					} ?></td>
					<td><?php echo $normal_check["value_from"]; ?></td>
					<td><?php echo $normal_check["value_to"]; ?></td>
					<?php
					$nr = nl2br($normal_check["normal_range"]);
					?>
					<td><?php echo $nr; ?></td>
					<td>
						<?php
						$n_stat = "Inactive";
						if ($normal_check["status"] == 1) {
							$n_stat = "Active";
						}
						?>
						<select id="norm_opt" class="span2"
							onchange="normal_update_opt('<?php echo $normal_check["slno"]; ?>',<?php echo $paramid; ?>,this)">
							<?php
							if ($normal_check["status"] == 0) {
								echo "<option value='0'>Active</option>";
								echo "<option value='1'>In-Active</option>";
							} else {
								echo "<option value='1'>In-Active</option>";
								echo "<option value='0'>Active</option>";
							}
							?>
							<option value="2">Update</option>
							<option value="3">Remove</option>
						</select>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>

		</table>
	</div>
	<div style="text-align:center;">
		<button class="btn btn-close" id="result_option_close_btn" data-dismiss="modal"><i class="icon-off"></i>
			Close</button>
	</div>
	<?php
}
if ($_POST["type"] == "normal_add") {
	$paramid = $_POST["paramid"];
	$slno = $_POST["slno"];

	$parameter_normal = mysqli_fetch_array(mysqli_query($link, "select * from parameter_normal_check WHERE `slno`='$slno' AND `parameter_id`='$paramid'"));

	$save_btn_name = "Save";
	if ($parameter_normal) {
		$save_btn_name = "Update";
	}

	if ($parameter_normal["age_from"] > 0) {
		if ($parameter_normal["age_from"] > 30 && $parameter_normal["age_from"] < 365) {
			$age_from = $parameter_normal["age_from"] / 30;
			$mon_f = "Selected='selected'";
		} elseif ($parameter_normal["age_from"] > 0 && $parameter_normal["age_from"] < 30) {
			$age_from = $parameter_normal["age_from"];
			$day_f = "Selected='selected'";
		} else {
			$age_from = $parameter_normal["age_from"] / 365;
			$years_f = "Selected='selected'";
		}
	}

	if ($parameter_normal["age_to"] > 0) {
		if ($parameter_normal["age_to"] > 30 && $parameter_normal["age_to"] < 365) {
			$age_to = $parameter_normal["age_to"] / 30;
			$mon_t = "Selected='selected'";
		} elseif ($parameter_normal["age_to"] > 0 && $parameter_normal["age_to"] < 30) {
			$age_to = $parameter_normal["age_to"];
			$day_t = "Selected='selected'";
		} else {
			$age_to = $parameter_normal["age_to"] / 365;
			$years_t = "Selected='selected'";
		}
	}
	?>
	<div>
		<table class="table table-bordered table-condensed">
			<tr style="<?php echo $instrument_str; ?>">
				<th>Instrument</th>
				<td>
					<select id="instrument_id">
						<?php
						$qry = mysqli_query($link, "SELECT `id`, `name` FROM `lab_instrument_master` WHERE `status`=0 ORDER BY `id` ASC");
						while ($data = mysqli_fetch_array($qry)) {
							if ($parameter_normal["instrument_id"] == $data["id"]) {
								$sel = "selected";
							} else {
								$sel = "";
							}
							echo "<option value='$data[id]' $sel>$data[name]</option>";
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Depends On</td>
				<td>
					<select id="dep_id">
						<?php
						$qry = mysqli_query($link, "select * from DependentType");
						while ($data = mysqli_fetch_array($qry)) {
							if ($parameter_normal) {
								if ($parameter_normal["dep_id"] == $data["id"]) {
									$sel = "Selected='selected'";
								} else {
									$sel = "";
								}
							} else {
								if ($data["id"] == 7) {
									$sel = "Selected='selected'";
								} else {
									$sel = "";
								}
							}
							echo "<option value='$data[id]' $sel>$data[name]</option>";
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Sex</td>
				<td>
					<select id="sex">
						<option value="0">--Select--</option>
						<option <?php if ($parameter_normal["sex"] == "MALE") {
							echo "Selected='selected'";
						} ?>>MALE</option>
						<option <?php if ($parameter_normal["sex"] == "FEMALE") {
							echo "Selected='selected'";
						} ?>>FEMALE
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Age From</td>
				<td>
					<input type="text" id="a_from" value="<?php echo round($age_from); ?>" />
					<select id="a_from_typ">
						<option value="1" <?php echo $day_f; ?>>Days</option>
						<option value="30" <?php echo $mon_f; ?>>Months</option>
						<option value="365" <?php echo $years_f; ?>>Years</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Age to</td>
				<td>
					<input type="text" id="a_to" value="<?php echo round($age_to); ?>" />
					<select id="a_to_typ" style="">
						<option value="1" <?php echo $day_t; ?>>Days</option>
						<option value="30" <?php echo $mon_t; ?>>Months</option>
						<option value="365" <?php echo $years_t; ?>>Years</option>
					</select>
				</td>
			<tr>
				<td>Value from</td>
				<td>
					<input type="text" id="val_f" value="<?php echo $parameter_normal["value_from"]; ?>" />
				</td>
			</tr>
			<tr>
				<td>Value to</td>
				<td>
					<input type="text" id="val_t" value="<?php echo $parameter_normal["value_to"]; ?>" />
				</td>
			</tr>
			<tr>
				<td>Normal Range</td>
				<td>
					<textarea id="n_range" style="width: 430px;"><?php echo $parameter_normal["normal_range"]; ?></textarea>
				</td>
			</tr>
			</tr>
			<tr>
				<td></td>
				<td>
					<button class="btn btn-save" onclick="normal_save('<?php echo $paramid; ?>','<?php echo $slno; ?>')"><i
							class="icon-save"></i> <?php echo $save_btn_name; ?></button>

					<button class="btn btn-back" onclick="load_normal_range_div('<?php echo $paramid; ?>',0)"><i
							class="icon-backward"></i> Back</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

if ($_POST["type"] == "normal_save") {
	$paramid = $_POST["paramid"];
	$slno = $_POST["slno"];

	$instrument_id = mysqli_real_escape_string($link, $_POST["instrument_id"]);
	$dep_id = mysqli_real_escape_string($link, $_POST["dep_id"]);
	$sex = mysqli_real_escape_string($link, $_POST["sex"]);
	$a_from = mysqli_real_escape_string($link, $_POST["a_from"]);
	$a_from_typ = mysqli_real_escape_string($link, $_POST["a_from_typ"]);
	$a_to = mysqli_real_escape_string($link, $_POST["a_to"]);
	$a_to_typ = mysqli_real_escape_string($link, $_POST["a_to_typ"]);
	$val_f = mysqli_real_escape_string($link, $_POST["val_f"]);
	$val_t = mysqli_real_escape_string($link, $_POST["val_t"]);
	$n_range = mysqli_real_escape_string($link, $_POST["n_range"]);
	
	$a_from=$a_from*$a_from_typ;
	$a_to=$a_to*$a_to_typ;
	
	$parameter_normal = mysqli_fetch_array(mysqli_query($link, "select * from parameter_normal_check WHERE `slno`='$slno' AND `parameter_id`='$paramid'"));
	if ($parameter_normal) {
		if (mysqli_query($link, "UPDATE `parameter_normal_check` SET `dep_id`='$dep_id',`age_from`='$a_from',`age_to`='$a_to',`age_type`='$a_from_typ',`sex`='$sex',`value_from`='$val_f',`value_to`='$val_t',`normal_range`='$n_range',`instrument_id`='$instrument_id' WHERE `slno`='$slno' AND `parameter_id`='$paramid'")) {
			echo "101@Updated";
		} else {
			echo "102@Failed, try again later";
		}
	} else {
		if (mysqli_query($link, "INSERT INTO `parameter_normal_check`(`parameter_id`, `dep_id`, `age_from`, `age_to`, `age_type`, `sex`, `value_from`, `value_to`, `normal_range`, `sign`, `status`, `instrument_id`) VALUES ('$paramid','$dep_id','$a_from','$a_to','$a_from_typ','$sex','$val_f','$val_t','$n_range','0','0','$instrument_id')")) {
			echo "101@Saved";
		} else {
			echo "103@Failed, try again later.";
		}
	}
}

if ($_POST["type"] == "update_normal_stat") {
	$paramid = $_POST["paramid"];
	$slno = $_POST["slno"];
	$status = $_POST["status"];

	if (mysqli_query($link, "UPDATE `parameter_normal_check` SET `status`='$status' WHERE `slno`='$slno' AND `parameter_id`='$paramid'")) {
		echo "101@Updated";
	} else {
		echo "102@Failed, try again later";
	}
}

if ($_POST["type"] == "load_sample_vaccu") {
	$samp = $_POST["samp"];
	$val=array();
	$q=mysqli_query($link,"SELECT `vacc_id` FROM `sample_vaccu` WHERE `samp_id`='$samp'");
	while($r=mysqli_fetch_array($q))
	{
		$vacName=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `vaccu_master` WHERE `id`='$r[vacc_id]'"));
		$tmp=array();
		$tmp['id']=$r['vacc_id'];
		$tmp['name']=$vacName['type'];
		
		array_push($val, $tmp);
	}
	echo json_encode($val);
}

if ($_POST["type"] == "remove_normal_range") {
	$paramid = $_POST["paramid"];
	$slno = $_POST["slno"];

	$chech_use = mysqli_fetch_array(mysqli_query($link, "SELECT `range_id` FROM `testresults` WHERE `range_id`='$slno' LIMIT 1"));
	if ($chech_use) {
		echo "103@Already used";
	} else {
		if (mysqli_query($link, "DELETE FROM `parameter_normal_check` WHERE `slno`='$slno' AND `parameter_id`='$paramid'")) {
			echo "101@Removed";
		} else {
			echo "102@Failed, try again later";
		}
	}
}
// Normal Range End

if ($_POST["type"] == "delete_param") {
	$paramid = $_POST["paramid"];
	if (!$paramid) {
		$paramid = 0;
	}
	
	$param_used_num=mysqli_num_rows(mysqli_query($link, "SELECT `paramid` FROM `testresults` WHERE `paramid`='$paramid'"));
	if($param_used_num==0)
	{
		$param_used_num=mysqli_num_rows(mysqli_query($link, "SELECT `paramid` FROM `test_sample_result` WHERE `paramid`='$paramid'"));
	}
	
	if($param_used_num==0)
	{
		if(mysqli_query($link, "DELETE FROM `Parameter_old` WHERE `ID`='$paramid'"))
		{
			mysqli_query($link, "DELETE FROM `Testparameter` WHERE `ParamaterId`='$paramid'");
			
			echo "@101@Deleted";
		}else
		{
			echo "@201@Failed, try again later(1).";
		}
	}else
	{
		echo "@202@This parameter is being used and cannot be deleted.";
	}
}
?>
