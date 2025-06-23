<?php
include("../../includes/connection.php");

$date = date("Y-m-d"); // important
$time = date("H:i:s");

$type = $_POST["type"];

if ($type == "load_center") {
	$branch_id = mysqli_real_escape_string($link, $_POST["branch_id"]);

	$qry = mysqli_query($link, "SELECT `centreno`,`centrename` FROM `centremaster` WHERE `branch_id`='$branch_id' ORDER BY `centrename` ASC");

	while ($data = mysqli_fetch_array($qry)) {
		echo "<option value='$data[centreno]'>$data[centrename]</option>";
	}
}

if ($type == "testsave") {
	// echo $_POST["alltestid"];
	$v = 0;
	$center = $_POST["center"];
	$alltestid = $_POST["alltestid"];
	$selfperc = $_POST["selfperc"];
	$selfcash = $_POST["selfcash"];
	$drperc = $_POST["drperc"];
	$drcash = $_POST["drcash"];
	$testids = explode("@@", $alltestid);
	foreach ($testids as $id) {

		mysqli_query($link, "DELETE FROM `testmaster_rate` WHERE `centreno`='$center' AND `testid`='$id'");

		if (mysqli_query($link, "INSERT INTO `testmaster_rate`(`centreno`, `testid`, `discount_rate`, `dis_perchant`, `doc_dis_rate`, `doc_dis_perchant`) VALUES ('$center','$id','$selfcash','$selfperc','$drcash','$drperc')")) {
			$v++;
		}
	}
	echo "Saved";

}


if ($type == "load_check") {
	$branch_id = mysqli_real_escape_string($link, $_POST["branch_id"]);

	$dept = $_POST["dept_ids"];
	$center = mysqli_real_escape_string($link, $_POST["center"]);

	if ($dept) {
		if ($center == "C100") {
			$qry1 = "SELECT `testid`,`testname`,`rate` FROM `testmaster` WHERE `type_id` in ($dept) ORDER BY `testname` ";
		} else {
			$qry1 = "SELECT `testid`,`testname`,`rate_bc` AS `rate` FROM `testmaster` WHERE `type_id` in ($dept) ORDER BY `testname` ";
		}

		$qry = mysqli_query($link, $qry1);

		// echo $qry1; 
		?>
		<table class="table table-bordered" id="dynamicTable">
			<thead class="table_header_fix">
				<tr>
					<th>
						<input type="checkbox" id="selectAll" onchange="selectAllCheckboxes()">
					</th>
					<th>ID</th>
					<th>Name</th>
					<th>Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
				while ($data = mysqli_fetch_array($qry)) {
					?>
					<tr>
						<td><input type="checkbox" class="rowCheckbox" onchange="updateSelectedIds()"></td>
						<td>
							<?php echo "$data[testid]" ?>
						</td>
						<td onclick="toggleCheckbox(this)">
							<?php echo "$data[testname]" ?>
						</td>
						<td>
							<?php echo "$data[rate]" ?>
						</td>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>


		<?php
	}
}





if ($type == "load_master_test") {
	$val = mysqli_real_escape_string($link, $_POST["val"]);
	$group_id = mysqli_real_escape_string($link, $_POST["group_id"]);
	$branch_id = mysqli_real_escape_string($link, $_POST["branch_id"]);


	if ($group_id == "101") // OPD
	{
		$service_category = 3;

		$str = "SELECT `consultantdoctorid`,`Name`,`opd_visit_fee`,`opd_reg_fee` FROM `consultant_doctor_master` WHERE `Name`!='' AND `branch_id`='$branch_id'";

		if (strlen($val) > 1) {
			$str .= " AND `Name` LIKE '%$val%'";
		}

		$str .= " ORDER BY `Name` ASC";

		$qry = mysqli_query($link, $str);

		?>
		<table class="table table-condensed table-bordered table-hover">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<!--<th>ID</th>-->
					<th>Name</th>
					<th title="Master Rate">M Rate</th>
					<th title="Centre Rate">C Rate</th>
				</tr>
			</thead>
			<?php
			$n = 1;
			while ($data = mysqli_fetch_array($qry)) {
				?>
				<tr>
					<td rowspan="2">
						<?php echo $n; ?>
					</td>
					<!--<td rowspan="2"><?php echo $data["consultantdoctorid"]; ?></td>-->
					<td rowspan="2">
						<?php echo $data["Name"]; ?>
					</td>
					<td title="Master Visit Fee">V:
						<?php echo $data["opd_visit_fee"]; ?>
					</td>
					<td title="Centre Visit Fee">
						<input type="text" class="span1 cm_rate_opd_v" id="cm_rate_opd_v<?php echo $data["consultantdoctorid"]; ?>"
							onkeyup="cm_rate_opd_v_up('<?php echo $data["consultantdoctorid"]; ?>','<?php echo $service_category; ?>',event)"
							pattern="[0-9.]{1,4}">
					</td>
				</tr>
				<tr>
					<td title="Master Registraion Fee">R:
						<?php echo $data["opd_reg_fee"]; ?>
					</td>
					<td title="Centre Registraion Fee">
						<input type="text" class="span1 cm_rate_opd_r" id="cm_rate_opd_r<?php echo $data["consultantdoctorid"]; ?>"
							onkeyup="cm_rate_opd_r_up('<?php echo $data["consultantdoctorid"]; ?>','<?php echo $service_category; ?>',event)"
							pattern="[0-9.]{1,4}">
					</td>
				</tr>
				<?php
				$n++;
			}
			?>
		</table>
		<?php
	} else if ($group_id == "104" || $group_id == "150" || $group_id == "151") // Investigation
	{
		$service_category = 1;

		if ($group_id == "104") // LABORATORY CHARGES
		{
			$category_id = 1;
		}
		if ($group_id == "150") // CARDIOLOGY CHARGES
		{
			$category_id = 3;
		}
		if ($group_id == "151") // RADIOLOGY CHARGES
		{
			$category_id = 2;
		}

		$str = "SELECT * FROM `testmaster` WHERE `testname`!='' AND category_id='$category_id' "; //  AND `rate`>0

		if (strlen($val) > 1) {
			$str .= " AND `testname` LIKE '%$val%'";
		}

		$str .= " ORDER BY `testname` ASC";

		$qry = mysqli_query($link, $str);

		?>
			<table class="table table-condensed table-bordered table-hover">
				<thead class="table_header_fix">
					<tr>
						<th>#</th>
						<!--<th>ID</th>-->
						<th>Name</th>
						<th title="Master Rate">M Rate</th>
						<th title="Centre Rate">C Rate</th>
					</tr>
				</thead>
				<?php
				$n = 1;
				while ($data = mysqli_fetch_array($qry)) {
					?>
					<tr>
						<td>
						<?php echo $n; ?>
						</td>
						<!--<td><?php echo $data["testid"]; ?></td>-->
						<td>
						<?php echo $data["testname"]; ?>
						</td>
						<td title="Master Rate">
						<?php echo $data["rate"]; ?>
						</td>
						<td title="Centre Rate">
							<input type="text" class="span1 cm_rate" id="cm_rate<?php echo $data["testid"]; ?>"
								onkeyup="cm_rate_up('<?php echo $data["testid"]; ?>','<?php echo $service_category; ?>',event)"
								pattern="[0-9.]{1,8}">
						</td>
					</tr>
					<?php
					$n++;
				}
				?>
			</table>
		<?php
	} else if ($group_id > 0) {
		$service_category = 2;

		$str = "SELECT * FROM `charge_master` WHERE `charge_name`!='' AND `group_id`='$group_id'";

		if (strlen($val) > 1) {
			$str .= " AND `charge_name` LIKE '%$val%'";
		}

		$str .= " ORDER BY `charge_name` ASC";

		$qry = mysqli_query($link, $str);

		?>
				<table class="table table-condensed table-bordered table-hover">
					<thead class="table_header_fix">
						<tr>
							<th>#</th>
							<!--<th>ID</th>-->
							<th>Name</th>
							<th title="Master Rate">M Rate</th>
							<th title="Centre Rate">C Rate</th>
						</tr>
					</thead>
				<?php
				$n = 1;
				while ($data = mysqli_fetch_array($qry)) {
					?>
						<tr>
							<td>
						<?php echo $n; ?>
							</td>
							<!--<td><?php echo $data["charge_id"]; ?></td>-->
							<td>
						<?php echo $data["charge_name"]; ?>
							</td>
							<td title="Master Rate">
						<?php echo $data["amount"]; ?>
							</td>
							<td title="Centre Rate">
								<input type="text" class="span1 cm_rate" id="cm_rate<?php echo $data["charge_id"]; ?>"
									onkeyup="cm_rate_up('<?php echo $data["charge_id"]; ?>','<?php echo $service_category; ?>',event)"
									pattern="[0-9.]{1,8}">
							</td>
						</tr>
					<?php
					$n++;
				}
				?>
				</table>
		<?php
	}
}

if ($type == "load_centre_test") {
	$centreno = mysqli_real_escape_string($link, $_POST["centreno"]);
	$group_id = mysqli_real_escape_string($link, $_POST["group_id"]);
	echo $val = mysqli_real_escape_string($link, $_POST["val"]);
	$testid = mysqli_real_escape_string($link, $_POST["testid"]);
	$centre_test_sorter = mysqli_real_escape_string($link, $_POST["centre_test_sorter"]);

	if ($group_id == 101 && $centreno) {
		$service_category = 3;
		?>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<!--<th>ID</th>-->
					<th>Name</th>
					<th title="Centre Visit Fee">Visit Fee</th>
					<th title="Centre Regd Fee">Regd Fee</th>
				</tr>
			</thead>
			<?php
			$n = 1;

			$str = "SELECT a.`consultantdoctorid`, a.`Name`, b.`visit_fee`, b.`reg_fee` FROM `consultant_doctor_master` a, `opd_doc_rate` b WHERE a.`consultantdoctorid`=b.`consultantdoctorid` AND b.`centreno`='$centreno'";

			if (strlen($val) > 1) {
				$str .= " AND a.`Name` LIKE '%$val%'";
			}

			if ($centre_test_sorter == "ASC") {
				$str .= " ORDER BY a.`Name` " . $centre_test_sorter;
			}
			if ($centre_test_sorter == "DESC") {
				$str .= " ORDER BY b.`slno` " . $centre_test_sorter;
			}

			//echo $str;
	
			$qry = mysqli_query($link, $str);

			while ($data = mysqli_fetch_array($qry)) {
				?>
				<tr id="ctr<?php echo $data["consultantdoctorid"]; ?>">
					<td>
						<?php echo $n; ?>
					</td>
					<!--<td><?php echo $data["consultantdoctorid"]; ?></td>-->
					<td>
						<?php echo $data["Name"]; ?>
					</td>
					<td title="Centre Visit Fee">
						<input type="text" class="span1" id="cc_rate_opd_v<?php echo $data["consultantdoctorid"]; ?>"
							onkeyup="cc_rate_opd_v_up('<?php echo $data["consultantdoctorid"]; ?>','<?php echo $service_category; ?>',event)"
							value="<?php echo $data["visit_fee"]; ?>" pattern="[0-9.]{1,8}">
					</td>
					<td title="Centre Regd Fee">
						<input type="text" class="span1" id="cc_rate_opd_r<?php echo $data["consultantdoctorid"]; ?>"
							onkeyup="cc_rate_opd_r_up('<?php echo $data["consultantdoctorid"]; ?>','<?php echo $service_category; ?>',event)"
							value="<?php echo $data["reg_fee"]; ?>" pattern="[0-9.]{1,8}">

						<button class="btn btn-mini btn-danger"
							onclick="delete_centre_test('<?php echo $data["consultantdoctorid"]; ?>','<?php echo $service_category; ?>')"
							style="margin-bottom: 12px;float:right;"><i class="icon-remove"></i></button>
					</td>
				</tr>
				<?php
				$n++;
			}
			?>
		</table>
		<?php
	} else if ($centreno) {
		?>
			<table class="table table-condensed table-bordered">
				<thead class="table_header_fix">
					<tr>
						<th>#</th>
						<!--<th>ID</th>-->
						<th>Name</th>
						<!--<th>Code</th>-->
						<th>C Rate</th>
					</tr>
				</thead>
				<?php
				$n = 1;

				if ($group_id == "0" || $group_id == "104" || $group_id == "150" || $group_id == "151") // Investigation
				{
					$service_category = 1;

					$category_str = "";
					if ($group_id == "104") // LABORATORY CHARGES
					{
						$category_id = 1;
						$category_str = " AND a.`category_id`='$category_id'";
					}
					if ($group_id == "150") // CARDIOLOGY CHARGES
					{
						$category_id = 3;
						$category_str = " AND a.`category_id`='$category_id'";
					}
					if ($group_id == "151") // RADIOLOGY CHARGES
					{
						$category_id = 2;
						$category_str = " AND a.`category_id`='$category_id'";
					}

					$str = "SELECT a.`testid`, a.`testname`, b.`rate`, b.`test_code` FROM `testmaster` a, `testmaster_rate` b WHERE a.`testid`=b.`testid` AND b.`centreno`='$centreno' $category_str";

					if (strlen($val) > 1) {
						$str .= " AND a.`testname` LIKE '%$val%'";
					}

					if ($centre_test_sorter == "ASC") {
						$str .= " ORDER BY a.`testname` " . $centre_test_sorter;
					}
					if ($centre_test_sorter == "DESC") {
						$str .= " ORDER BY b.`slno` " . $centre_test_sorter;
					}

					//echo $str;
		
					$qry = mysqli_query($link, $str);

					while ($data = mysqli_fetch_array($qry)) {
						?>
						<tr id="ctr<?php echo $data["testid"]; ?>">
							<td>
							<?php echo $n; ?>
							</td>
							<!--<td><?php echo $data["testid"]; ?></td>-->
							<td>
							<?php echo $data["testname"]; ?>
							</td>
							<!--<td>
						<input type="text" class="span1" id="test_code<?php echo $data["testid"]; ?>" onkeyup="test_code_up('<?php echo $data["testid"]; ?>','<?php echo $service_category; ?>',event)" value="<?php echo $data["test_code"]; ?>">
					</td>-->
							<td>
								<input type="text" class="span1" id="cc_rate<?php echo $data["testid"]; ?>"
									onkeyup="cc_rate_up('<?php echo $data["testid"]; ?>','<?php echo $service_category; ?>',event)"
									value="<?php echo $data["rate"]; ?>" pattern="[0-9.]{1,8}">

								<button class="btn btn-mini btn-danger"
									onclick="delete_centre_test('<?php echo $data["testid"]; ?>','<?php echo $service_category; ?>')"
									style="margin-bottom: 12px;float:right;"><i class="icon-remove"></i></button>
							</td>
						</tr>
						<?php
						$n++;
					}
				}
				if ($group_id == "0" || $group_id != "101" || $group_id != "104" || $group_id != "150" || $group_id != "151") {
					$service_category = 2;

					$group_str = "";
					if ($group_id > 0) {
						$group_str = " AND a.`group_id`='$group_id'";
					}

					$str = "SELECT a.`charge_id`, a.`charge_name`, b.`rate`, b.`charge_code` FROM `charge_master` a, `service_rate` b WHERE a.`charge_id`=b.`charge_id` AND b.`centreno`='$centreno' $group_str";

					if (strlen($val) > 1) {
						$str .= " AND a.`charge_name` LIKE '%$val%'";
					}

					if ($centre_test_sorter == "ASC") {
						$str .= " ORDER BY a.`charge_name` " . $centre_test_sorter;
					}
					if ($centre_test_sorter == "DESC") {
						$str .= " ORDER BY b.`slno` " . $centre_test_sorter;
					}

					//echo $str;
		
					$qry = mysqli_query($link, $str);

					while ($data = mysqli_fetch_array($qry)) {
						?>
						<tr id="ctr<?php echo $data["charge_id"]; ?>">
							<td>
							<?php echo $n; ?>
							</td>
							<!--<td><?php echo $data["charge_id"]; ?></td>-->
							<td>
							<?php echo $data["charge_name"]; ?>
							</td>
							<!--<td>
						<input type="text" class="span1" id="test_code<?php echo $data["charge_id"]; ?>" onkeyup="test_code_up('<?php echo $data["charge_id"]; ?>','<?php echo $service_category; ?>',event)" value="<?php echo $data["test_code"]; ?>">
					</td>-->
							<td>
								<input type="text" class="span1" id="cc_rate<?php echo $data["charge_id"]; ?>"
									onkeyup="cc_rate_up('<?php echo $data["charge_id"]; ?>','<?php echo $service_category; ?>',event)"
									value="<?php echo $data["rate"]; ?>" pattern="[0-9.]{1,8}">

								<button class="btn btn-mini btn-danger"
									onclick="delete_centre_test('<?php echo $data["charge_id"]; ?>','<?php echo $service_category; ?>')"
									style="margin-bottom: 12px;float:right;"><i class="icon-remove"></i></button>
							</td>
						</tr>
						<?php
						$n++;
					}
				}
				?>
			</table>
		<?php
	}
}

if ($type == "save_centre_test") {
	$centreno = mysqli_real_escape_string($link, $_POST["centreno"]);
	$group_id = mysqli_real_escape_string($link, $_POST["group_id"]);
	$testid = mysqli_real_escape_string($link, $_POST["testid"]);
	$service_category = mysqli_real_escape_string($link, $_POST["service_category"]);
	$c_rate = mysqli_real_escape_string($link, $_POST["c_rate"]);

	if ($centreno) {
		if ($service_category == 3) // OPD
		{

		} else if ($service_category == 1) // Investigation
		{
			$same_data = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `testmaster_rate` WHERE `centreno`='$centreno' AND `testid`='$testid' "));
			if ($same_data) {
				mysqli_query($link, " UPDATE `testmaster_rate` SET `rate`='$c_rate' WHERE `centreno`='$centreno' AND `testid`='$testid' ");
			} else {
				mysqli_query($link, " INSERT INTO `testmaster_rate`(`centreno`, `testid`, `rate`, `test_code`) VALUES ('$centreno','$testid','$c_rate','') ");
			}
		} else if ($service_category == 2) // Services
		{
			$same_data = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `service_rate` WHERE `centreno`='$centreno' AND `charge_id`='$testid' "));
			if ($same_data) {
				mysqli_query($link, " UPDATE `service_rate` SET `rate`='$c_rate' WHERE `centreno`='$centreno' AND `charge_id`='$testid' ");
			} else {
				mysqli_query($link, " INSERT INTO `service_rate`(`centreno`, `charge_id`, `rate`, `charge_code`) VALUES ('$centreno','$testid','$c_rate','') ");
			}
		}
	}
}

if ($type == "save_centre_test_code") {
	$centreno = mysqli_real_escape_string($link, $_POST["centreno"]);
	$testid = mysqli_real_escape_string($link, $_POST["testid"]);
	$test_code = mysqli_real_escape_string($link, $_POST["test_code"]);

	if ($centreno) {
		mysqli_query($link, " UPDATE `testmaster_rate` SET `test_code`='$test_code' WHERE `centreno`='$centreno' AND `testid`='$testid' ");
	}
}

if ($type == "delete_centre_test") {
	$centreno = mysqli_real_escape_string($link, $_POST["centreno"]);
	$serv_id = mysqli_real_escape_string($link, $_POST["serv_id"]);
	$service_category = mysqli_real_escape_string($link, $_POST["service_category"]);

	if ($centreno) {
		if ($service_category == 1) {
			if (mysqli_query($link, " DELETE FROM `testmaster_rate` WHERE `centreno`='$centreno' AND `testid`='$serv_id' ")) {
				echo "1";
			} else {
				echo "2";
			}
		}
		if ($service_category == 2) {
			if (mysqli_query($link, " DELETE FROM `service_rate` WHERE `centreno`='$centreno' AND `charge_id`='$serv_id' ")) {
				echo "1";
			} else {
				echo "2";
			}
		}
		if ($service_category == 3) {
			if (mysqli_query($link, " DELETE FROM `opd_doc_rate` WHERE `centreno`='$centreno' AND `consultantdoctorid`='$serv_id' ")) {
				echo "1";
			} else {
				echo "2";
			}
		}
	} else {
		echo "3";
	}
}


if ($type == "save_centre_opd_v") {
	$centreno = mysqli_real_escape_string($link, $_POST["centreno"]);
	$group_id = mysqli_real_escape_string($link, $_POST["group_id"]);
	$docid = mysqli_real_escape_string($link, $_POST["docid"]);
	$service_category = mysqli_real_escape_string($link, $_POST["service_category"]);
	$c_rate = mysqli_real_escape_string($link, $_POST["c_rate"]);

	if ($centreno) {
		if ($service_category == 3) // OPD
		{
			$same_data = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_doc_rate` WHERE `centreno`='$centreno' AND `consultantdoctorid`='$docid' "));
			if ($same_data) {
				mysqli_query($link, " UPDATE `opd_doc_rate` SET `visit_fee`='$c_rate' WHERE `centreno`='$centreno' AND `consultantdoctorid`='$docid' ");
			} else {
				mysqli_query($link, " INSERT INTO `opd_doc_rate`(`centreno`, `consultantdoctorid`, `visit_fee`, `reg_fee`) VALUES ('$centreno','$docid','$c_rate','0') ");
			}
		}
	}
}

if ($type == "save_centre_opd_r") {
	$centreno = mysqli_real_escape_string($link, $_POST["centreno"]);
	$group_id = mysqli_real_escape_string($link, $_POST["group_id"]);
	$docid = mysqli_real_escape_string($link, $_POST["docid"]);
	$service_category = mysqli_real_escape_string($link, $_POST["service_category"]);
	$c_rate = mysqli_real_escape_string($link, $_POST["c_rate"]);

	if ($centreno) {
		if ($service_category == 3) // OPD
		{
			$same_data = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_doc_rate` WHERE `centreno`='$centreno' AND `consultantdoctorid`='$docid' "));
			if ($same_data) {
				mysqli_query($link, " UPDATE `opd_doc_rate` SET `reg_fee`='$c_rate' WHERE `centreno`='$centreno' AND `consultantdoctorid`='$docid' ");
			} else {
				mysqli_query($link, " INSERT INTO `opd_doc_rate`(`centreno`, `consultantdoctorid`, `visit_fee`, `reg_fee`) VALUES ('$centreno','$docid','0','$c_rate') ");
			}
		}
	}
}

?>