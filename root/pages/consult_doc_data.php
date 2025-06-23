<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date = date("Y-m-d");
$time = date("H:i:s");

if ($_POST["type"] == "load_consult_doc") {
	$doc = $_POST["doc"];
	if ($doc) {
		$dis = "disabled='disabled'";
	} else {
		$dis = "";
	}
	$dept_id = $_POST["dept_id"];
	$con_doc_qry = mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `dept_id`='$dept_id' ");
?>
	<th>Doctor List</th>
	<td>
		<select id="con_doc_id" onChange="doc_change(this.value)" <?php echo $dis; ?>>
			<option value="0">Select</option>
			<?php
			while ($con_doc = mysqli_fetch_array($con_doc_qry)) {
				if ($con_doc['consultantdoctorid'] == $doc) {
					$sel = "selected='selected'";
				} else {
					$sel = "";
				}

				echo "<option value='$con_doc[consultantdoctorid]' $sel>$con_doc[Name]</option>";
			}
			?>
		</select>
	</td>
<?php
}
if ($_POST["type"] == "save_consult_doc") {
	$con_doc_id = $_POST["con_doc_id"];
	$doc_type = $_POST["doc_type"];
	$vfee1 = $_POST["vfee1"];
	$vfee2 = $_POST["vfee2"];
	$valid = $_POST["valid"];
	$opd_reg_fee = $_POST["vfeer1"];
	$opd_reg_validity = $_POST["validr"];
	$opd_room = $_POST["opd_room"];
	$user = $_POST["user"];

	mysqli_query($link, " UPDATE `consultant_doctor_master` SET `doc_type`='$doc_type',`opd_visit_fee`='$vfee1',`ipd_visit_fee`='$vfee2',`validity`='$valid',`room_id`='$opd_room',`opd_reg_fee`='$opd_reg_fee',`opd_reg_validity`='$opd_reg_validity',`user`='$user',`date`='$date',`time`='$time' WHERE `consultantdoctorid`='$con_doc_id' ");
}
if ($_POST["type"] == "load_consult_doc_fee") {
	$con_doc_id = $_POST["con_doc_id"];
	$doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_doc_id' "));
	echo $doc_info["doc_type"] . "@#@" . $doc_info["opd_visit_fee"] . "@#@" . $doc_info["ipd_visit_fee"] . "@#@" . $doc_info["validity"] . "@#@" . $doc_info["room_id"] . "@#@" . $doc_info["opd_reg_fee"] . "@#@" . $doc_info["opd_reg_validity"];
}
if ($_POST["type"] == "edit_consult_doc") {
	$id = $_POST["id"];
	$doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$id' "));
	echo $doc_info["doc_type"] . "@#@" . $doc_info["dept_id"] . "@#@" . $doc_info["opd_visit_fee"] . "@#@" . $doc_info["ipd_visit_fee"] . "@#@" . $doc_info["validity"] . "@#@" . $id . "@#@" . $doc_info["room_id"] . "@#@" . $doc_info["opd_reg_fee"] . "@#@" . $doc_info["opd_reg_validity"];
}

if ($_POST["type"] == "doc_list") {
	$doc_name = $_POST["doc_name"];
	$speciality_id = $_POST["speciality_id"];
	$branch_id = $_POST["branch_id"];

	$str = "SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`>0 AND `branch_id`='$branch_id'";

	if (strlen($doc_name) > 2) {
		$str .= " AND `Name` LIKE '%$doc_name%'";
	}
	if ($speciality_id > 0) {
		$str .= " AND `dept_id`='$speciality_id'";
	}

	$str .= " ORDER BY `consultantdoctorid` ASC";

	$qry = mysqli_query($link, $str);
?>
	<table class="table table-condensed table-hover table-bordered" style="background-color: white;">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Contact</th>
				<th>Qualification</th>
				<th>Designation</th>
				<th>Department</th>
				<!--<th>Consult Fee</th>
				<th>Regd Fee</th>-->
			</tr>
		</thead>
		<?php
		$n = 1;
		while ($data = mysqli_fetch_array($qry)) {
			$emp_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$data[emp_id]' "));

			$dept_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$data[dept_id]' "));
		?>
			<tr onclick="load_doc_info('<?php echo $data["consultantdoctorid"] ?>','<?php echo $data["emp_id"] ?>')" style="cursor:pointer;">
				<td><?php echo $n; ?></td>
				<td><?php echo $data["Name"]; ?></td>
				<td><?php echo $data["phone"]; ?></td>
				<td><?php echo $data["qualification"]; ?></td>
				<td><?php echo $data["designation"]; ?></td>
				<td><?php echo $dept_info["name"]; ?></td>
				<!--<td><?php echo $data["opd_visit_fee"]; ?></td>
				<td><?php echo $data["opd_reg_fee"]; ?></td>-->
			</tr>
		<?php
			$n++;
		}
		?>
	</table>
<?php
}


if ($_POST["type"] == "doc_info") {
	//print_r($_POST);

	$doc_id = $_POST["doc_id"];
	$emp_id = $_POST["emp_id"];
	$user = $_POST["user"];
	$branch_id = $_POST["branch_id"];

	$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$user' "));

	$branch_str = " AND `branch_id`='$user_info[branch_id]'";
	$element_style = "display:none";
	if ($user_info["levelid"] == 1) {
		$branch_str = "";

		$element_style = "display:none;";
		$branch_num = mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
		if ($branch_num > 1) {
			$element_style = "display:;";
		}
	}

	$element_dis = "disabled";
	if ($user_info["levelid"] == 1) {
		$element_dis = "";
	}

	$access_str = "";
	$main_con_doc_str = "";
	$btn_name = "Save";
	if ($doc_id > 0) {
		$btn_name = "Update";

		$doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_id' "));
		$sub_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `main_doc_id`='$doc_id' "));

		if ($sub_doc_info) {
			$doc_info["main_doc_id"] = 0;
			$main_con_doc_str = "disabled";
		}

		if ($doc_info["main_doc_id"] > 0) {
			$access_str = "display:none;";
		}

		$opd_visit_fee = $doc_info["opd_visit_fee"];
		$opd_visit_validity = $doc_info["opd_visit_validity"];
		$opd_reg_fee = $doc_info["opd_reg_fee"];
		$opd_reg_validity = $doc_info["opd_reg_validity"];
	} else {
		$opd_reg_fee = mysqli_fetch_array(mysqli_query($link, " SELECT `amount_validity` FROM `company_fees` WHERE `fees_id`='1' AND `branch_id`='$branch_id' "));
		$opd_reg_validity = mysqli_fetch_array(mysqli_query($link, " SELECT `amount_validity` FROM `company_fees` WHERE `fees_id`='2' AND `branch_id`='$branch_id' "));
		$opd_visit_fee = mysqli_fetch_array(mysqli_query($link, " SELECT `amount_validity` FROM `company_fees` WHERE `fees_id`='3' AND `branch_id`='$branch_id' "));
		$opd_visit_validity = mysqli_fetch_array(mysqli_query($link, " SELECT `amount_validity` FROM `company_fees` WHERE `fees_id`='4' AND `branch_id`='$branch_id' "));

		$opd_reg_fee = $opd_reg_fee["amount_validity"];
		$opd_reg_validity = $opd_reg_validity["amount_validity"];
		$opd_visit_fee = $opd_visit_fee["amount_validity"];
		$opd_visit_validity = $opd_visit_validity["amount_validity"];
	}

	if ($doc_info["dob"] == "0000-00-00") {
		$dob = "";
	} else {
		$dob = $doc_info["dob"];
	}

	$pass_td_str = "display:none;";
	$app_access_pass = "";
	$app_access = mysqli_fetch_array(mysqli_query($link, " SELECT `password`,`status` FROM `employee` WHERE `emp_id`='$emp_id' "));
	//if($app_access["status"]==0)
	if ($app_access["password"] != "") {
		$app_access_pass = $app_access["password"];
		$pass_td_str = "";
	}
?>
	<table class="table table-condensed table-bordered" style="background-color: white;">
		<tr>
			<th>Name <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" class="capital" id="name" value="<?php echo $doc_info["Name"]; ?>" onkeyup="name_up(event)">
			</td>
			<th>Gender <b style="color:#f00;">*</b></th>
			<td>
				<select id="sex" onkeyup="sex_up(event)">
					<option value="0">Select</option>
					<?php
					$qry = mysqli_query($link, " SELECT `gender_id`, `sex` FROM `gender_master` ");
					while ($data = mysqli_fetch_array($qry)) {
						if ($data["sex"] == $doc_info["sex"]) {
							$sex_sel = "selected";
						} else {
							$sex_sel = "";
						}
						echo "<option value='$data[sex]' $sex_sel>$data[sex]</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Phone</th>
			<td>
				<input type="text" class="" id="phone" value="<?php echo $doc_info["phone"]; ?>" onkeyup="phone_up(event)" maxlength=10>
			</td>
			<th>Email</th>
			<td>
				<input type="text" id="email" value="<?php echo $doc_info["email"]; ?>" onkeyup="email_up(event)">
			</td>
		</tr>
		<tr>
			<th>DOB</th>
			<td>
				<input type="text" class="datepicker" id="dob" onkeyup="dob_up(event)" value="<?php echo $dob; ?>" onchange="calculate_age(this.value)" readonly style="width: 80px;">
				<span class="side_name">Age</span>
				<input type="text" class="" id="age" onkeyup="age_up(event)" style="width: 70px;margin-left: 38px;">
			</td>
			<th>Address</th>
			<td>
				<input type="text" class="capital" id="address" value="<?php echo $doc_info["address"]; ?>" onkeyup="address_up(event)">
			</td>
		</tr>
		<tr>
			<th>Qualification</th>
			<td>
				<input type="text" class="capital" id="qualification" value="<?php echo $doc_info["qualification"]; ?>" onkeyup="qualification_up(event)">
			</td>
			<th>Designation</th>
			<td>
				<input type="text" class="capital" id="designation" value="<?php echo $doc_info["designation"]; ?>" onkeyup="designation_up(event)">
			</td>
		</tr>
		<tr>
			<th>Department <b style="color:#f00;">*</b></th>
			<td>
				<select id="dept_id" onkeyup="dept_id_up(event)">
					<option value="0">Select</option>
					<?php
					$spclt_qry = mysqli_query($link, " SELECT * FROM `doctor_specialist_list` order by `name` ");
					while ($spclt = mysqli_fetch_array($spclt_qry)) {
						if ($spclt["speciality_id"] == $doc_info["dept_id"]) {
							$dept_sel = "selected";
						} else {
							$dept_sel = "";
						}
						echo "<option value='$spclt[speciality_id]' $dept_sel>$spclt[name]</option>";
					}
					?>
				</select>
			</td>
			<th>Registration No</th>
			<td>
				<input type="text" class="capital" id="regd_no" value="<?php echo $doc_info["regd_no"]; ?>" onkeyup="regd_no_up(event)">
			</td>
		</tr>
		<tr style="display:;">
			<th>OPD Visit Fee <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" id="opd_visit_fee" class="" value="<?php echo $opd_visit_fee; ?>" onkeyup="opd_visit_fee_up(event)" placeholder="OPD Visit Fee" />
			</td>
			<th>OPD Validity(Days) <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" id="opd_visit_validity" class="" value="<?php echo $opd_visit_validity; ?>" onkeyup="opd_visit_validity_up(event)" placeholder="OPD Validity" />
			</td>
		</tr>
		<tr style="display:;">
			<th>OPD Regd Fee <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" id="opd_reg_fee" class="" value="<?php echo $opd_reg_fee; ?>" onkeyup="opd_reg_fee_up(event)" placeholder="OPD Registration Fee" />
			</td>
			<th>OPD Regd Validity(Days) <b style="color:#f00;">*</b></th>
			<td>
				<input type="text" id="opd_reg_validity" class="" value="<?php echo $opd_reg_validity; ?>" onkeyup="opd_reg_validity_up(event)" placeholder="OPD Registration Validity" />
			</td>
		</tr>
		<tr>
			<th>OPD Room</th>
			<td>
				<select id="opd_room" onkeyup="opd_room_up(event)">
					<option value="0">Select</option>
					<?php
					$opd_room_qry = mysqli_query($link, " SELECT * FROM `opd_doctor_room` ORDER BY `room_name` ");
					while ($opd_room = mysqli_fetch_array($opd_room_qry)) {
						if ($opd_room["room_id"] == $doc_info["room_id"]) {
							$room_sel = "selected";
						} else {
							$room_sel = "";
						}
						echo "<option value='$opd_room[room_id]' $room_sel>$opd_room[room_name]</option>";
					}
					?>
				</select>
			</td>
			<th>Status</th>
			<td>
				<select id="status" onkeyup="status_up(event)">
					<option value="0" <?php if ($doc_info["status"] == 0) {
											echo "selected";
										} ?>>Active</option>
					<option value="1" <?php if ($doc_info["status"] == 1) {
											echo "selected";
										} ?>>In-active</option>
				</select>
			</td>
		</tr>
		<tr style="<?php echo $element_style; ?>">
			<th>Branch <b style="color:#f00;">*</b></th>
			<td colspan="3">
				<select id="branch_id" onkeyup="branch_up(event)" <?php echo $element_dis; ?>>
					<?php
					$qry = mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
					while ($data = mysqli_fetch_array($qry)) {
						if ($doc_info["branch_id"] == 0) {
							if ($user_info["branch_id"] == $data["branch_id"]) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						} else {
							if ($doc_info["branch_id"] == $data["branch_id"]) {
								$sel = "selected";
							} else {
								$sel = "";
							}
						}
						echo "<option value='$data[branch_id]' $sel>$data[name]</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr style="display:none;">
			<th>Main Doctor</th>
			<td colspan="3">
				<select id="main_con_doc_id" onchange="main_con_doc_change(this.value)" <?php echo $main_con_doc_str; ?>>
					<option value="0">Select Main Doctor</option>
					<?php
					$doc_str = "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `branch_id`='$branch_id'";
					if ($doc_id > 0) {
						$doc_str .= " AND `consultantdoctorid`!='$doc_id'";
					}
					$doc_str .= " ORDER BY `Name` ASC";

					$qry = mysqli_query($link, $doc_str);
					while ($data = mysqli_fetch_array($qry)) {
						if ($doc_info["main_doc_id"] == $data["consultantdoctorid"]) {
							$sel = "selected";
						} else {
							$sel = "";
						}
						echo "<option value='$data[consultantdoctorid]' $sel>$data[Name]</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr style="<?php echo $access_str; ?>" id="access_tr">
			<th>Application Access</th>
			<td>
				<select id="access" onchange="access_change(this.value)">
					<option value="0">No</option>
					<option value="1" <?php if ($app_access["password"] != "") {
											echo "selected";
										} ?>>Yes</option>
				</select>
			</td>
			<th class="pass_td" style="<?php echo $pass_td_str; ?>">Password</th>
			<td class="pass_td" style="<?php echo $pass_td_str; ?>">
				<input type="password" id="password" value="<?php echo $app_access_pass; ?>">
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<center>
					<button class="btn btn-info" id="save_btn" onclick="save()"><i class="icon-save"></i>
						<?php echo $btn_name; ?></button>
					<button class="btn btn-inverse" id="back_btn" onclick="doc_list()"><i class="icon-backward"></i>
						Back</button>
				</center>
			</td>
		</tr>
	</table>
	<input type="hidden" id="doc_id" value="<?php echo $doc_id; ?>">
	<input type="hidden" id="emp_id" value="<?php echo $emp_id; ?>">
<?php
}

if ($_POST["type"] == "dob_to_age") {
	$dob = mysqli_real_escape_string($link, $_POST["dob"]);
	if ($dob) {
		echo age_calculator($dob);
	}
}

if ($_POST["type"] == "age_to_dob") {
	$age = mysqli_real_escape_string($link, $_POST["age"]);
	if ($age) {
		echo age_To_Birthday($age);
	}
}

if ($_POST["type"] == "save_info") {
	//print_r($_POST);

	$branch_id = mysqli_real_escape_string($link, $_POST["branch_id"]);
	$user = mysqli_real_escape_string($link, $_POST["user"]);
	$doc_id = mysqli_real_escape_string($link, $_POST["doc_id"]);
	$emp_id = mysqli_real_escape_string($link, $_POST["emp_id"]);
	$name = mysqli_real_escape_string($link, $_POST["name"]);
	$sex = mysqli_real_escape_string($link, $_POST["sex"]);
	$dob = mysqli_real_escape_string($link, $_POST["dob"]);
	$phone = mysqli_real_escape_string($link, $_POST["phone"]);
	$email = mysqli_real_escape_string($link, $_POST["email"]);
	$address = mysqli_real_escape_string($link, $_POST["address"]);
	$qualification = mysqli_real_escape_string($link, $_POST["qualification"]);
	$designation = mysqli_real_escape_string($link, $_POST["designation"]);
	$opd_visit_fee = mysqli_real_escape_string($link, $_POST["opd_visit_fee"]);
	$opd_visit_validity = mysqli_real_escape_string($link, $_POST["opd_visit_validity"]);
	$opd_reg_fee = mysqli_real_escape_string($link, $_POST["opd_reg_fee"]);
	$opd_reg_validity = mysqli_real_escape_string($link, $_POST["opd_reg_validity"]);
	$dept_id = mysqli_real_escape_string($link, $_POST["dept_id"]);
	$regd_no = mysqli_real_escape_string($link, $_POST["regd_no"]);
	$room_id = mysqli_real_escape_string($link, $_POST["room_id"]);
	$status = mysqli_real_escape_string($link, $_POST["status"]);
	$access = mysqli_real_escape_string($link, $_POST["access"]);
	$password = mysqli_real_escape_string($link, $_POST["password"]);

	$main_doc_id = mysqli_real_escape_string($link, $_POST["main_con_doc_id"]);

	$doc_type = 1;
	$ipd_visit_fee = 0;
	$average_time = 0;
	$signature = "";

	$emp_type = 2;
	$levelid = 5;

	if (!$branch_id) {
		$branch_id = 1;
	}
	if (!$emp_id) {
		$emp_id = 1;
	}

	if (!$dob) {
		$dob = "0000-00-00";
	}

	if ($doc_id == 0) {
		if (mysqli_query($link, "INSERT INTO `consultant_doctor_master`(`branch_id`, `Name`, `sex`, `dob`, `phone`, `email`, `address`, `qualification`, `designation`, `regd_no`, `doc_type`, `opd_visit_fee`, `opd_visit_validity`, `opd_reg_fee`, `opd_reg_validity`, `ipd_visit_fee`, `dept_id`, `average_time`, `signature`, `emp_id`, `room_id`, `status`, `user`, `date`, `time`, `main_doc_id`) VALUES ('$branch_id','$name','$sex','$dob','$phone','$email','$address','$qualification','$designation','$regd_no','$doc_type','$opd_visit_fee','$opd_visit_validity','$opd_reg_fee','$opd_reg_validity','$ipd_visit_fee','$dept_id','$average_time','$signature','$emp_id','$room_id','$status','$user','$date','$time','$main_doc_id')")) {
			$last_row = mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `Name`='$name' AND `phone`='$phone' AND `dept_id`='$dept_id' AND `user`='$user' ORDER BY `consultantdoctorid` DESC LIMIT 1 "));
			$doc_id = $last_row["consultantdoctorid"];

			mysqli_query($link, " INSERT INTO `consultant_doctor_alloc`(`consultantdoctorid`, `status`, `date`, `time`, `user`) VALUES ('$doc_id','$status','$date','$time','$user') ");

			$md5_pass = md5($password);

			$status = 0;
			if ($access == 0) {
				$access = 1;
				$md5_pass = "";
				$status = 1;
			}

			if ($access == 1 && $main_doc_id == 0) {
				mysqli_query($link, " INSERT INTO `employee`(`branch_id`, `emp_code`, `name`, `sex`, `dob`, `phone`, `email`, `address`, `password`, `levelid`, `emp_type`, `edit_info`, `edit_payment`, `cancel_pat`, `discount_permission`, `status`, `user`, `api_key`) VALUES ('$branch_id','','$name','$sex','$dob','$phone','$email','$address','$md5_pass','$levelid','$emp_type','0','0','0','0','$status','$user','') ");

				$last_row = mysqli_fetch_array(mysqli_query($link, " SELECT `emp_id` FROM `employee` WHERE `emp_code`='$emp_code' AND `name`='$name' AND `phone`='$phone' AND `levelid`='$levelid' AND `user`='$user' ORDER BY `emp_id` DESC LIMIT 1 "));
				$emp_id = $last_row["emp_id"];
				$emp_code = $code . $emp_id . $user;

				mysqli_query($link, " UPDATE `employee` SET `emp_code`='$emp_code' WHERE `emp_id`='$emp_id' ");

				mysqli_query($link, " UPDATE `consultant_doctor_master` SET `emp_id`='$emp_id' WHERE `consultantdoctorid`='$doc_id' ");
			}

			mysqli_query($link, " INSERT INTO `refbydoctor_master`(`ref_name`, `qualification`, `address`, `phone`, `email`, `consultantdoctorid`, `emp_id`, `branch_id`, `user`, `date`, `time`) VALUES ('$name','$qualification','$address','$phone','$email','$doc_id','$emp_id','$branch_id','$user','$date','$time') ");

			echo "Saved";
		} else {
			echo "Failed, try again later.";
		}
	} else {
		mysqli_query($link, " UPDATE `consultant_doctor_master` SET `branch_id`='$branch_id',`Name`='$name',`sex`='$sex',`dob`='$dob',`phone`='$phone',`email`='$email',`address`='$address',`qualification`='$qualification',`designation`='$designation',`doc_type`='$doc_type',`opd_visit_fee`='$opd_visit_fee',`opd_visit_validity`='$opd_visit_validity',`opd_reg_fee`='$opd_reg_fee',`opd_reg_validity`='$opd_reg_validity',`ipd_visit_fee`='$ipd_visit_fee',`dept_id`='$dept_id',`average_time`='$average_time',`signature`='$signature',`room_id`='$room_id',`status`='$status',`user`='$user',`date`='$date',`time`='$time',`main_doc_id`='$main_doc_id' WHERE `consultantdoctorid`='$doc_id' "); // ,`emp_id`='$emp_id'

		$last_row = mysqli_fetch_array(mysqli_query($link, " SELECT `status` FROM `consultant_doctor_alloc` WHERE `consultantdoctorid`='$doc_id' ORDER BY `slno` DESC LIMIT 1 "));
		if ($last_row["status"] != $status) {
			mysqli_query($link, " INSERT INTO `consultant_doctor_alloc`(`consultantdoctorid`, `status`, `date`, `time`, `user`) VALUES ('$doc_id','$status','$date','$time','$user') ");
		}

		if ($emp_id != 0) {
			$emp_info = mysqli_fetch_array(mysqli_query($link, " SELECT `password` FROM `employee` WHERE `emp_id`='$emp_id' "));

			mysqli_query($link, " UPDATE `employee` SET `branch_id`='$branch_id',`name`='$name',`sex`='$sex',`dob`='$dob',`phone`='$phone',`email`='$email',`address`='$address' WHERE `emp_id`='$emp_id' ");

			if ($access == 1 && $main_doc_id == 0) {
				if ($password != $emp_info["password"]) {
					$md5_pass = md5($password);

					mysqli_query($link, " UPDATE `employee` SET `password`='$md5_pass' WHERE `emp_id`='$emp_id' ");
				}
			} else {
				mysqli_query($link, " UPDATE `employee` SET `password`='' WHERE `emp_id`='$emp_id' ");
			}
		} else {
			if ($access == 1 && $main_doc_id == 0) {
				//$vid=nextId("","employee","emp_id","1");
				//$emp_code=$code.$vid.$user;

				$md5_pass = md5($password);

				mysqli_query($link, " INSERT INTO `employee`(`branch_id`, `emp_code`, `name`, `sex`, `dob`, `phone`, `email`, `address`, `password`, `levelid`, `emp_type`, `edit_info`, `edit_payment`, `cancel_pat`, `discount_permission`, `status`, `user`, `api_key`) VALUES ('$branch_id','$emp_code','$name','$sex','$dob','$phone','$email','$address','$md5_pass','$levelid','$emp_type','0','0','0','0','0','$user','') ");

				$last_row = mysqli_fetch_array(mysqli_query($link, " SELECT `emp_id` FROM `employee` WHERE `emp_code`='$emp_code' AND `name`='$name' AND `phone`='$phone' AND `levelid`='$levelid' AND `user`='$user' ORDER BY `emp_id` DESC LIMIT 1 "));
				$emp_id = $last_row["emp_id"];
				$emp_code = $code . $emp_id . $user;

				mysqli_query($link, " UPDATE `employee` SET `emp_code`='$emp_code' WHERE `emp_id`='$emp_id' ");

				mysqli_query($link, " UPDATE `consultant_doctor_master` SET `emp_id`='$emp_id' WHERE `consultantdoctorid`='$doc_id' ");
			} else {
				mysqli_query($link, " UPDATE `employee` SET `status`='1' WHERE `emp_id`='$emp_id' ");
			}
		}

		mysqli_query($link, " UPDATE `refbydoctor_master` SET `ref_name`='$name',`qualification`='$qualification',`address`='$address',`phone`='$phone',`email`='$email',`user`='$user',`date`='$date',`time`='$time' WHERE `consultantdoctorid`='$doc_id' ");

		echo "Updated";
	}
}





if ($_POST["type"] == "merge_doctor_div") {
?>
	<table class="table table-condensed">
		<tr>
			<th>Main Doctor</th>
			<td>
				<select id="main_doc" class="span5" onchange="main_doc_change()">
					<option value="0">Select</option>
					<?php
					$qry = mysqli_query($link, "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `Name`!='' ORDER BY `Name` ASC");
					while ($doc_info = mysqli_fetch_array($qry)) {
						echo "<option value='$doc_info[consultantdoctorid]'>$doc_info[Name]</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Duplicate Doctor(s)</th>
			<td>
				<select multiple id="duplicate_doc" class="span5">
					<!--<option value="0">Select</option>
			<?php
			$qry = mysqli_query($link, "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `Name`!='' ORDER BY `Name` ASC");
			while ($doc_info = mysqli_fetch_array($qry)) {
				echo "<option value='$doc_info[consultantdoctorid]'>$doc_info[Name]</option>";
			}
			?>-->
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<center>
					<button class="btn btn-info" onclick="save_merge()"><i class="icon-save"></i> Merge</button>
					<button class="btn btn-danger" id="modal_close_btn" data-dismiss="modal"><i class="icon-off"></i>
						Close</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}

if ($_POST["type"] == "load_duplicate_doc") {
	$main_doc = $_POST["main_doc"];

	$qry = mysqli_query($link, "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `Name`!='' AND `consultantdoctorid` NOT IN('$main_doc') ORDER BY `Name` ASC");
	while ($doc_info = mysqli_fetch_array($qry)) {
		echo "<option value='$doc_info[consultantdoctorid]'>$doc_info[Name]</option>";
	}
}

if ($_POST["type"] == "save_merge") {
	//print_r($_POST);
	//exit();
	$main_doc = $_POST["main_doc"];
	$duplicate_doc = $_POST["duplicate_doc"];

	$val = 0;

	foreach ($duplicate_doc as $dupli_doc) {
		if ($dupli_doc) {
			//echo $dupli_doc." , ";

			mysqli_query($link, " UPDATE `appointment_book` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");
			mysqli_query($link, " UPDATE `appointment_book_cancel` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");
			mysqli_query($link, " UPDATE `appointment_book_edit` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `doctor_fee_less` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `doctor_payment` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `doctor_payment_ipd` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `doctor_service_done` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `doctor_service_done_cancel` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `ipd_pat_doc_details` SET `attend_doc`='$main_doc' WHERE `attend_doc`='$dupli_doc' ");
			mysqli_query($link, " UPDATE `ipd_pat_doc_details` SET `admit_doc`='$main_doc' WHERE `admit_doc`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `ipd_test_ref_doc` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `ipd_ip_consultation` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");

			mysqli_query($link, " UPDATE `opd_doc_rate` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");
			mysqli_query($link, " UPDATE `payment_settlement_doc` SET `consultantdoctorid`='$main_doc' WHERE `consultantdoctorid`='$dupli_doc' ");


			mysqli_query($link, " INSERT INTO `con_doc_merge`(`main_doc`, `duplicate_doc`, `user`, `date`, `time`) VALUES ('$main_doc','$dupli_doc','$c_user','$date','$time') ");


			mysqli_query($link, " DELETE FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$dupli_doc' ");

			$val++;
		}
	}

	if ($val > 0) {
		echo "Successfully Merged";
	} else {
		echo "Failed, try again later.";
	}
}
?>
