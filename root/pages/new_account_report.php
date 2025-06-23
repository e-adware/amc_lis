<?php
$emp_id = trim($_SESSION["emp_id"]);
$branch_display = "display:none;";
if ($p_info["levelid"] == 1) {
	$branch_str = "";

	$branch_display = "display:none;";
	$branch_num = mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if ($branch_num > 1) {
		$branch_display = "display:;";
	}

	$dept_sel_dis = "";
} else {
	$branch_str = " AND branch_id='$p_info[branch_id]'";
	$branch_display = "display:none;";

	$dept_sel_dis = "disabled";
}

$dept_sel_dis = "";
$branch_id = $p_info["branch_id"];

$not_accountant = array();
array_push($not_accountant, 5, 6, 11, 12, 13, 20, 21);
$not_accountant = join(',', $not_accountant);

?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from"
						value="<?php echo date("Y-m-d"); ?>" <?php echo $dept_sel_dis; ?> readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to"
						value="<?php echo date("Y-m-d"); ?>" <?php echo $dept_sel_dis; ?> readonly>
					<select id="encounter" class="span2">
						<?php //if($level_id['levelid']=='1'){ 
						?>
						<option value="0">All Department</option>
						<?php //} 
						?>
						<?php
						$qq_qry = mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`=0 ORDER BY `p_type_id` ");
						while ($qq = mysqli_fetch_array($qq_qry)) {
							echo "<option value='$qq[p_type_id]'>$qq[p_type]</option>";
						}
						?>
					</select>
					<select id="user_entry" class="span2">
						<option value="0">All User</option>
						<?php

						$user_qry = mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` WHERE levelid NOT IN ($not_accountant) ORDER BY `name` ");
						while ($user = mysqli_fetch_array($user_qry)) {
							if ($emp_id == $user["emp_id"]) {
								$sel_this = "selected";
							} else {
								$sel_this = "";
							}
							echo "<option value='$user[emp_id]' $sel_this>$user[name]</option>";
						}
						?>
					</select>
					<select id="pay_mode" class="span2">
						<option value="0">All Payment Mode</option>
						<?php
						$pay_mode_qry = mysqli_query($link, " SELECT `p_mode_id`,`p_mode_name` FROM `payment_mode_master` WHERE `status`=0 ORDER BY `sequence` ");
						while ($pay_mode = mysqli_fetch_array($pay_mode_qry)) {
							echo "<option value='$pay_mode[p_mode_name]'>$pay_mode[p_mode_name]</option>";
						}
						?>
					</select>
					<select id="account_break" class="span1" style="display:none;">
						<option value="0">Now</option>
						<?php

						$user_qry = mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `user`='$emp_id' ORDER BY `slno` DESC ");
						$user_num = mysqli_num_rows($user_qry);
						if ($user_num == 0) {
							//echo "<option value='0'>Break 0</option>";
						}
						while ($user = mysqli_fetch_array($user_qry)) {
							$time_date_str = convert_date($user["date"]) . " at " . convert_time($user["time"]);
							echo "<option value='$user[slno]' >Break $user[counter] on $time_date_str</option>";
						}
						?>
					</select>
					<select id="branch_id" class="span1" style="<?php echo $branch_display; ?>">
						<?php
						$branch_qry = mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while ($branch = mysqli_fetch_array($branch_qry)) {
							if ($branch_id == $branch["branch_id"]) {
								$branch_sel = "selected";
							} else {
								$branch_sel = "";
							}
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
						?>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('receipt_detail')">Receipt Detail </button>
					<button class="btn btn-success" onClick="view_all('discount_report')">Discount Report</button>
					<button class="btn btn-success" onClick="view_all('refund_report')">Refund Report</button>
					<button class="btn btn-success" onClick="view_all('balance_received')">Balance Received</button>
					<br>

				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">

	</div>
	<!--<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">-->
</div>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function () {
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
	});

	function view_all(typ) {
		$("#loader").show();
		$.post("pages/new_account_report_data.php", {
			type: typ,
			date1: $("#from").val(),
			date2: $("#to").val(),
			encounter: $("#encounter").val(),
			user_entry: $("#user_entry").val(),
			pay_mode: $("#pay_mode").val(),
			account_break: $("#account_break").val(),
			branch_id: $("#branch_id").val(),
		},
			function (data, status) {
				$("#loader").hide();
				$("#load_all").slideUp(500, function () {
					$("#load_all").html(data).slideDown(500);
				});
			})
	}

	function print_page(val, date1, date2, encounter, user_entry, pay_mode, account_break, branch_id) {
		var user = $("#user").text().trim();
		if (val == "receipt_detail") {
			url = "pages/new_account_receipt_detail_print.php?date1=" + date1 + "&date2=" + date2 + "&encounter=" +
				encounter + "&user_entry=" + user_entry + "&pay_mode=" + pay_mode + "&EpMl=" + user + "&account_break=" +
				account_break + "&branch_id=" + branch_id;
		}
		if (val == "discount_report") {
			url = "pages/new_account_discount_report_print.php?date1=" + date1 + "&date2=" + date2 + "&encounter=" +
				encounter + "&user_entry=" + user_entry + "&pay_mode=" + pay_mode + "&EpMl=" + user + "&account_break=" +
				account_break + "&branch_id=" + branch_id;
		}
		if (val == "refund_report") {
			url = "pages/new_account_refund_report_print.php?date1=" + date1 + "&date2=" + date2 + "&encounter=" +
				encounter + "&user_entry=" + user_entry + "&pay_mode=" + pay_mode + "&EpMl=" + user + "&account_break=" +
				account_break + "&branch_id=" + branch_id;
		}
		if (val == "balance_received") {
			url = "pages/new_account_balance_received_report_print.php?date1=" + date1 + "&date2=" + date2 + "&encounter=" +
				encounter + "&user_entry=" + user_entry + "&pay_mode=" + pay_mode + "&EpMl=" + user + "&account_break=" +
				account_break + "&branch_id=" + branch_id;
		}
		wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}

	function close_account(c_date) {
		bootbox.dialog({
			message: "<h5>Are you sure want to close ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function () {
						bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Close',
					className: "btn btn-danger",
					callback: function () {
						$.post("pages/close_account_data.php", {
							type: "close_account",
							c_date: c_date,
							user: $("#user").text().trim(),
						},
							function (data, status) {
								bootbox.dialog({
									message: "<h5>" + data + "'s account is closed</h5>"
								});
								setTimeout(function () {
									bootbox.hideAll();
									window.location.reload(true);
								}, 3000);
							})
					}
				}
			}
		});
	}
</script>
<style>
	.ScrollStyle {
		max-height: 400px;
		overflow-y: scroll;
	}

	@media print {
		body * {
			visibility: hidden;
		}

		#load_all,
		#load_all * {
			visibility: visible;
		}

		#load_all {
			overflow: visible;
			position: absolute;
			left: 0;
			top: 0;
		}
	}

	.ipd_serial {
		display: none;
	}
</style>