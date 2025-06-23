<?php
session_start();

include ("../../includes/connection.php");
include ("../../includes/global.function.php");
include ("../../includes/idgeneration.function.php");

$c_user = trim($_SESSION['emp_id']);

$emp_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level = $emp_info["levelid"];

$branch_id = $_POST['branch_id'];
if (!$branch_id) {
	$branch_id = $emp_info["branch_id"];
}

$branch_str=" AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
$branch_str_a=" AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
$branch_str_b=" AND b.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];

// important
$date11 = $_POST['date1'];
$date22 = $_POST['date2'];

if ($_POST["type"] == "load_users") {
	$not_accountant = array();
	array_push($not_accountant, 5, 6, 11, 12, 13, 20, 21);
	$not_accountant = join(',', $not_accountant);

	echo "<option value='0'>Select User</option>";

	$qry = mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` WHERE `emp_id`>0 AND `levelid` NOT IN ($not_accountant) AND `branch_id`='$branch_id' ORDER BY `name` ASC ");
	while ($data = mysqli_fetch_array($qry)) {
		if ($c_user == $data["emp_id"]) {
			$sel_this = "selected";
		} else {
			$sel_this = "";
		}

		echo "<option value='$data[emp_id]' $sel_this>$data[name]</option>";
	}
}

if ($_POST["type"] == "receipt_detail") {
	$encounter = trim($_POST['encounter']);
	$user_entry = trim($_POST['user_entry']);
	$pay_mode = trim($_POST['pay_mode']);
	$account_break = trim($_POST['account_break']);

	$close_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));
	if ($close_info) {
		$break_number = $close_info["counter"] . " on " . date("d-M-Y", strtotime($close_info["date"])) . " " . date("h:i A", strtotime($close_info["time"]));
	} else {
		$break_number = 0;
	}

	$pay_max_slno_str_less = "";
	$exp_max_slno_str_less = "";
	$ref_max_slno_str_less = "";
	$fre_max_slno_str_less = "";

	$pay_max_slno_str_grtr = "";
	$exp_max_slno_str_grtr = "";
	$ref_max_slno_str_grtr = "";
	$fre_max_slno_str_grtr = "";

	$date_str = "";
	$date_str_exp = "";
	$date_str_a = "";
	$date_str_b = "";
	$user = "";
	$cashier_str = "All user";
	if ($user_entry > 0) {
		$user = " AND `user`='$user_entry'";
		$user_a = " AND a.`user`='$user_entry'";

		$user_name = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$cashier_str = $user_name["name"];
	}

	$payment_mode_str = "";
	if ($pay_mode != "0") {
		$payment_mode_str = " AND `payment_mode`='$pay_mode'";
	}

	// Less
	if ($user_entry != $c_user) {
		// admin
		$date_str = " AND `date` BETWEEN '$date1' AND '$date2'";
		$date_str_exp = " AND `expense_date` BETWEEN '$date1' AND '$date2'";
		$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
		$date_str_b = " AND b.`date` BETWEEN '$date1' AND '$date2'";
	} else {
		if ($account_break == 0) {
			// Greater
			$check_close_account_last = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `user`='$user_entry' ORDER BY `slno` DESC limit 1 "));

			if ($check_close_account_last) {
				//$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));

				$first_date = $check_close_account_last['date'];

				$date_str = " AND date>='$first_date'";
				$date_str_a = " AND a.date>='$first_date'";

				$pay_max_slno_grtr = $check_close_account_last['pay_id'];
				$pay_max_slno_str_grtr = " AND `pay_id`>$pay_max_slno_grtr ";

				$exp_max_slno_grtr = $check_close_account_last['exp_slno'];
				$exp_max_slno_str_grtr = " AND `slno`>$exp_max_slno_grtr ";

				// Object a
				$ref_max_slno_grtr = $check_close_account_last['refund_slno'];
				$ref_max_slno_str_grtr = " AND a.`slno`>$ref_max_slno_grtr ";

				$fre_max_slno_grtr = $check_close_account_last['free_slno'];
				$fre_max_slno_str_grtr = " AND a.`slno`>$fre_max_slno_grtr ";

			} else {
				$first_date = "";
				$pay_max_slno_str_grtr = " AND `pay_id`>0 ";
				$exp_max_slno_str_grtr = " AND `slno`>0 ";
				$ref_max_slno_str_grtr = " AND a.`slno`>0 ";
				$fre_max_slno_str_grtr = " AND a.`slno`>0 ";
			}

		} else {
			$check_close_account_today = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));

			$last_date = $check_close_account_today["close_date"];

			$date_str = " AND date<='$last_date'";
			$date_str_a = " AND a.date<='$last_date'";

			$pay_max_slno_less = $check_close_account_today['pay_id'];
			$pay_max_slno_str_less = " AND `pay_id`<=$pay_max_slno_less ";

			$exp_max_slno_less = $check_close_account_today['exp_slno'];
			$exp_max_slno_str_less = " AND `slno`<=$exp_max_slno_less ";

			// Object a
			$ref_max_slno_less = $check_close_account_today['refund_slno'];
			$ref_max_slno_str_less = " AND a.`slno`<=$ref_max_slno_less ";

			$fre_max_slno_less = $check_close_account_today['free_slno'];
			$fre_max_slno_str_less = " AND a.`slno`<=$fre_max_slno_less ";

			// Greater
			$check_close_account_last = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`<'$account_break' AND `user`='$c_user' ORDER BY `slno` DESC limit 1 "));

			if ($check_close_account_last) {
				$first_date = $check_close_account_last["close_date"];

				$date_str .= " AND date>='$first_date'";
				$date_str_a .= " AND a.date>='$first_date'";

				$pay_max_slno_grtr = $check_close_account_last['pay_id'];
				$pay_max_slno_str_grtr = " AND `pay_id`>$pay_max_slno_grtr ";

				$exp_max_slno_grtr = $check_close_account_last['exp_slno'];
				$exp_max_slno_str_grtr = " AND `slno`>$exp_max_slno_grtr ";

				// Object a
				$ref_max_slno_grtr = $check_close_account_last['refund_slno'];
				$ref_max_slno_str_grtr = " AND a.`slno`>$ref_max_slno_grtr ";

				$fre_max_slno_grtr = $check_close_account_last['free_slno'];
				$fre_max_slno_str_grtr = " AND a.`slno`>$fre_max_slno_grtr ";

			} else {
				$first_date = "";
			}
		}
	}

	// No Break Wise Start
	$date_str = " AND `date` BETWEEN '$date1' AND '$date2'";
	$date_str_exp = " AND `expense_date` BETWEEN '$date1' AND '$date2'";
	$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
	$date_str_b = " AND b.`date` BETWEEN '$date1' AND '$date2'";

	$pay_max_slno_str_less = "";
	$exp_max_slno_str_less = "";
	$ref_max_slno_str_less = "";
	$fre_max_slno_str_less = "";

	$pay_max_slno_str_grtr = "";
	$exp_max_slno_str_grtr = "";
	$ref_max_slno_str_grtr = "";
	$fre_max_slno_str_grtr = "";
	// No Break Wise End

	$encounter_str = "";
	$encounter_pay_type = 0;
	if ($encounter > 0) {
		$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type = $pat_typ_text["type"];

		$encounter_str = " AND `encounter`='$encounter'";
	}

	$report_name = "Receipt Detail ";
	//~ if($user_entry!=$c_user)
	//~ {
	//~ $report_name.=" from ".convert_date($date1)." to ".convert_date($date2);
	//~ }
	//~ else
	//~ {
	//~ $report_name.=" of Break no. ".$break_number;
	//~ }
	$report_name .= " from " . convert_date($date1) . " to " . convert_date($date2);
	?>
	<p style="margin-top: 2%;" id="print_div">

		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini"
				href="pages/new_account_receipt_detail_xls.php?date1=<?php echo $date11; ?>&date2=<?php echo $date22; ?>&encounter=<?php echo $encounter; ?>&user_entry=<?php echo $user_entry; ?>&pay_mode=<?php echo $pay_mode; ?>&account_break=<?php echo $account_break; ?>&branch_id=<?php echo $branch_id; ?>"><i
					class="icon-file icon-large"></i> Excel</a></span>

		<button type="button" class="btn btn-info btn-mini text-right"
			onclick="print_page('receipt_detail','<?php echo $date11; ?>','<?php echo $date22; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $pay_mode; ?>','<?php echo $account_break; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<b>Cashier: <?php echo $cashier_str; ?></b>
	<br>
	<b><?php echo $report_name; ?></b>
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Bill No</th>
				<th>Transaction No</th>
				<th>Patient Name</th>
				<th style="text-align:center;">Amount</th>
				<th>User</th>
				<th>Encounter</th>
			</tr>
		</thead>
		<?php
		$payment_mode_array = array();
		$payment_mode_array_refund = array();
		$total_amount = 0;
		$pmode = 1;
		$trans = 1;
		$payment_mode_name = "";

		$payment_mode_qry = mysqli_query($link, "SELECT `p_mode_name`,`operation` FROM `payment_mode_master` WHERE `p_mode_name` IN(SELECT DISTINCT `payment_mode` FROM `payment_detail_all` WHERE `pay_id`>0 AND `payment_type`!='Refund' $payment_mode_str $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str ) ORDER BY `sequence` ASC");
		while ($payment_mode = mysqli_fetch_array($payment_mode_qry)) {
			$pay_str = " SELECT * FROM `payment_detail_all` WHERE `pay_id`>0 AND (`amount`>0 OR `balance_amount`>0) AND `payment_type`!='Refund' AND `payment_mode`='$payment_mode[p_mode_name]' $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str ORDER BY `pay_id` ASC ";

			//echo $pay_str."<br>";
	
			$pay_mode_amount = 0;
			$pay = 1;
			$pay_det_qry = mysqli_query($link, $pay_str);
			$pay_det_num = mysqli_num_rows($pay_det_qry);
			if ($pay_det_num > 0) {
				if ($payment_mode_name != $payment_mode["p_mode_name"]) {
					echo "<tr class='$payment_mode[p_mode_name]'><th colspan='8'>$payment_mode[p_mode_name]</th></tr>";
				} else {
					$payment_mode_name = $payment_mode["p_mode_name"];
				}

				while ($pay_det = mysqli_fetch_array($pay_det_qry)) {
					$patient_id = $pay_det["patient_id"];
					$opd_id = $pay_det["opd_id"];

					$tr_show = 1;

					$amount = $pay_det["amount"];
					if ($payment_mode["operation"] == 2) // Credit
					{
						$amount = $pay_det["balance_amount"];

						$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `pay_id`>$pay_det[pay_id] $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user "));

						$already_paid = $check_paid["paid"];
						$already_discount = $check_paid["discount"];
						$already_refund = $check_paid["refund"];
						$already_tax = $check_paid["tax"];

						$amount -= $already_paid + $already_discount + $already_tax + $already_refund;
					}
					if ($amount < 0) {
						$amount = 0;
					}

					$pat_reg = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));

					$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
					$encounter_name = $pat_typ_text['p_type'];

					$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$patient_id' "));

					$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_det[user]' "));

					$cheque_ref_no = "";
					if ($pay_det["cheque_ref_no"] != "") {
						$cheque_ref_no = "<br><small style='font-size: 9px;'>(Ref : $pay_det[cheque_ref_no])</small>";
					}
					if ($amount > 0) {
						?>
						<tr>
							<td><?php echo $trans; ?></td>
							<td><?php echo $pay_det["date"]; ?></td>
							<td><?php echo $pay_det["opd_id"]; ?></td>
							<td><?php echo $pay_det["transaction_no"] . $cheque_ref_no; ?></td>
							<td><?php echo $pat_info["name"]; ?></td>
							<td style="text-align:right;"><?php echo indian_currency_format($amount); ?></td>
							<td><?php echo $user_info["name"]; ?></td>
							<td><?php echo $encounter_name; ?></td>
						</tr>
						<?php
						$pay_mode_amount += $amount;
						$total_amount += $amount;
						$trans++;
						$pay++;
					}
				}

				if ($payment_mode_name != $payment_mode["p_mode_name"]) {
					$payment_mode_array[$payment_mode["p_mode_name"]] = $pay_mode_amount;
					?>
					<tr class="<?php echo $payment_mode["p_mode_name"]; ?>">
						<th colspan="4"></th>
						<th style="text-align:right">Total <?php echo $payment_mode["p_mode_name"]; ?></th>
						<th style="text-align:right;"><?php echo indian_currency_format($pay_mode_amount); ?></th>
						<th></th>
						<th></th>
					</tr>
					<?php
				}
				if ($pay_mode_amount == 0) {
					//echo "<script>remove_tr('$payment_mode[p_mode_name]')</script>";
					?>
					<style>
						.<?php echo $payment_mode["p_mode_name"]; ?> {
							display: none;
						}
					</style>
					<?php
				}
				$pmode++;
			}
		}

		// Refund
		$total_amount_refunded = 0;
		$payment_mode_qry = mysqli_query($link, "SELECT `p_mode_name`,`operation` FROM `payment_mode_master` WHERE `p_mode_name` IN(SELECT DISTINCT `payment_mode` FROM `payment_detail_all` WHERE `pay_id`>0 AND `payment_type`='Refund' $payment_mode_str $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str) ORDER BY `sequence` ASC");
		$payment_mode_num = mysqli_num_rows($payment_mode_qry);
		if ($payment_mode_num > 0) {
			while ($payment_mode = mysqli_fetch_array($payment_mode_qry)) {
				if ($payment_mode_name != $payment_mode["p_mode_name"]) {
					echo "<tr><th colspan='8' style='display:;'>$payment_mode[p_mode_name] Refund</th></tr>";
				} else {
					$payment_mode_name = $payment_mode["p_mode_name"];
				}

				$pay_str = " SELECT * FROM `payment_detail_all` WHERE `pay_id`>0 AND `refund_amount`>0 AND `payment_type`='Refund' AND `payment_mode`='$payment_mode[p_mode_name]' $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str ORDER BY `pay_id` ASC ";

				//echo $pay_str."<br>";
	
				$pay_mode_amount = 0;
				$pay = 1;
				$pay_det_qry = mysqli_query($link, $pay_str);
				$pay_det_num = mysqli_num_rows($pay_det_qry);

				if ($pay_det_num > 0) {
					while ($pay_det = mysqli_fetch_array($pay_det_qry)) {
						$patient_id = $pay_det["patient_id"];
						$opd_id = $pay_det["opd_id"];

						$amount = $pay_det["refund_amount"];

						$pat_reg = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));

						$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
						$encounter_name = $pat_typ_text['p_type'];

						$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$patient_id' "));

						$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_det[user]' "));

						$cheque_ref_no = "";
						if ($pay_det["cheque_ref_no"] != "") {
							$cheque_ref_no = "<br><small style='font-size: 9px;'>(Ref : $pay_det[cheque_ref_no])</small>";
						}
						if ($amount < 0) {
							$amount = 0;
						}
						?>
						<tr style="display:;">
							<td><?php echo $trans; ?></td>
							<td><?php echo $pay_det["date"]; ?></td>
							<td><?php echo $pay_det["opd_id"]; ?></td>
							<td><?php echo $pay_det["transaction_no"] . $cheque_ref_no; ?></td>
							<td><?php echo $pat_info["name"]; ?></td>
							<td style="text-align:right;"><?php echo indian_currency_format($amount); ?></td>
							<td><?php echo $user_info["name"]; ?></td>
							<td><?php echo $encounter_name; ?></td>
						</tr>
						<?php
						$total_amount_refunded += $amount;
						$pay_mode_amount += $amount;
						$total_amount += $amount;
						$trans++;
						$pay++;
					}

					if ($payment_mode_name != $payment_mode["p_mode_name"]) {
						$payment_mode_array_refund[$payment_mode["p_mode_name"]] = $pay_mode_amount;
						?>
						<tr style="display:;">
							<th colspan="4"></th>
							<th style="text-align:right">Total <?php echo $payment_mode["p_mode_name"]; ?> Refund</th>
							<th style="text-align:right;"><?php echo indian_currency_format($pay_mode_amount); ?></th>
							<th></th>
							<th></th>
						</tr>
						<?php
					}
					$pmode++;
				}
			}
		}
		$net_amount = $total_amount - $payment_mode_array["Credit"];
		?>
		<tr>
			<td colspan="8" style="border-top:2px solid #000;"></td>
		</tr>
		<?php
		$total_amount_received = 0;
		foreach ($payment_mode_array as $key => $value) {
			if ($key != "Credit") {
				$total_amount_received += $value;
				?>
				<tr>
					<th colspan="4"></th>
					<th style="text-align:right">Total <?php echo $key; ?> Received</th>
					<th style="text-align:right;"><?php echo indian_currency_format($value); ?></th>
					<th></th>
					<th></th>
				</tr>
				<?php
			}
		}
		$net_amount = $total_amount_received - $total_amount_refunded;
		?>
		<tr>
			<th colspan="4"></th>
			<th style="text-align:right">Total Amount Received</th>
			<th style="text-align:right;"><?php echo indian_currency_format($total_amount_received); ?></th>
			<th></th>
			<th></th>
		</tr>
		<?php
		if ($total_amount_refunded > 0) {
			?>
			<tr>
				<th colspan="4"></th>
				<th style="text-align:right">Total Amount Refunded</th>
				<th style="text-align:right;"><?php echo indian_currency_format($total_amount_refunded); ?></th>
				<th></th>
				<th></th>
			</tr>
			<?php
		}
		?>
		<tr>
			<th colspan="4"></th>
			<th style="text-align:right">Net
				Amount(<?php $z = 1;
				foreach ($payment_mode_array as $key => $value) {
					if ($key != "Credit") {
						if ($z != 1) {
							echo "+";
						}
						echo "$key";
						$z++;
					}
				} ?>)
			</th>
			<th style="text-align:right;"><?php echo indian_currency_format($net_amount); ?></th>
			<th></th>
			<th></th>
		</tr>
		<?php
		if ($payment_mode_array_refund["Cash"]) {
			$net_cash_amount = $payment_mode_array["Cash"] - $payment_mode_array_refund["Cash"];
			?>
			<tr>
				<th colspan="4"></th>
				<th style="text-align:right">Net Cash Amount</th>
				<th style="text-align:right;"><?php echo indian_currency_format($net_cash_amount); ?></th>
				<th></th>
				<th></th>
			</tr>
			<?php
		}
		if ($user_entry == 0) {
			$expense = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`Amount`),0) AS `amount` FROM `expensedetail` WHERE `expense_date` BETWEEN '$date11' AND '$date22' "));
		} else {
			$expense = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`Amount`),0) AS `amount` FROM `expensedetail` WHERE `expense_date` BETWEEN '$date11' AND '$date22' AND `user`='$user_entry' "));
		}

		$expense_amount = $expense["amount"];
		if ($expense_amount > 0) {
			?>
			<tr>
				<th colspan="4"></th>
				<th style="text-align:right">Expense Amount</th>
				<th style="text-align:right;"><?php echo indian_currency_format($expense_amount); ?></th>
				<th></th>
				<th></th>
			</tr>
			<tr>
				<th colspan="4"></th>
				<th style="text-align:right">Net Amount</th>
				<th style="text-align:right;"><?php echo indian_currency_format($net_amount - $expense_amount); ?></th>
				<th></th>
				<th></th>
			</tr>
			<?php
		}
		?>
	</table>
	<?php
}

if ($_POST["type"] == "discount_report") {
	$encounter = trim($_POST['encounter']);
	$user_entry = trim($_POST['user_entry']);
	$pay_mode = trim($_POST['pay_mode']);
	$account_break = trim($_POST['account_break']);

	$close_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));
	if ($close_info) {
		$break_number = $close_info["counter"] . " on " . date("d-M-Y", strtotime($close_info["date"])) . " " . date("h:i A", strtotime($close_info["time"]));
	} else {
		$break_number = 0;
	}

	$pay_max_slno_str_less = "";
	$exp_max_slno_str_less = "";
	$ref_max_slno_str_less = "";
	$fre_max_slno_str_less = "";

	$pay_max_slno_str_grtr = "";
	$exp_max_slno_str_grtr = "";
	$ref_max_slno_str_grtr = "";
	$fre_max_slno_str_grtr = "";

	$date_str = "";
	$date_str_exp = "";
	$date_str_a = "";
	$date_str_b = "";
	$user = "";
	$cashier_str = "All user";
	if ($user_entry > 0) {
		$user = " AND `user`='$user_entry'";
		$user_a = " AND a.`user`='$user_entry'";

		$user_name = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$cashier_str = $user_name["name"];
	}

	$payment_mode_str = "";
	if ($pay_mode != "0") {
		//$payment_mode_str=" AND `payment_mode`='$pay_mode'";
	}

	// Less
	if ($user_entry != $c_user) {
		// admin
		$date_str = " AND `date` BETWEEN '$date1' AND '$date2'";
		$date_str_exp = " AND `expense_date` BETWEEN '$date1' AND '$date2'";
		$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
		$date_str_b = " AND b.`date` BETWEEN '$date1' AND '$date2'";
	} else {
		if ($account_break == 0) {
			// Greater
			$check_close_account_last = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `user`='$user_entry' ORDER BY `slno` DESC limit 1 "));

			if ($check_close_account_last) {
				//$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));

				$first_date = $check_close_account_last['date'];

				$date_str = " AND date>='$first_date'";
				$date_str_a = " AND a.date>='$first_date'";

				$pay_max_slno_grtr = $check_close_account_last['pay_id'];
				$pay_max_slno_str_grtr = " AND `pay_id`>$pay_max_slno_grtr ";

				$exp_max_slno_grtr = $check_close_account_last['exp_slno'];
				$exp_max_slno_str_grtr = " AND `slno`>$exp_max_slno_grtr ";

				// Object a
				$ref_max_slno_grtr = $check_close_account_last['refund_slno'];
				$ref_max_slno_str_grtr = " AND a.`slno`>$ref_max_slno_grtr ";

				$fre_max_slno_grtr = $check_close_account_last['free_slno'];
				$fre_max_slno_str_grtr = " AND a.`slno`>$fre_max_slno_grtr ";

			} else {
				$first_date = "";
				$pay_max_slno_str_grtr = " AND `pay_id`>0 ";
				$exp_max_slno_str_grtr = " AND `slno`>0 ";
				$ref_max_slno_str_grtr = " AND a.`slno`>0 ";
				$fre_max_slno_str_grtr = " AND a.`slno`>0 ";
			}

		} else {
			$check_close_account_today = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));

			$last_date = $check_close_account_today["close_date"];

			$date_str = " AND date<='$last_date'";
			$date_str_a = " AND a.date<='$last_date'";

			$pay_max_slno_less = $check_close_account_today['pay_id'];
			$pay_max_slno_str_less = " AND `pay_id`<=$pay_max_slno_less ";

			$exp_max_slno_less = $check_close_account_today['exp_slno'];
			$exp_max_slno_str_less = " AND `slno`<=$exp_max_slno_less ";

			// Object a
			$ref_max_slno_less = $check_close_account_today['refund_slno'];
			$ref_max_slno_str_less = " AND a.`slno`<=$ref_max_slno_less ";

			$fre_max_slno_less = $check_close_account_today['free_slno'];
			$fre_max_slno_str_less = " AND a.`slno`<=$fre_max_slno_less ";

			// Greater
			$check_close_account_last = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`<'$account_break' AND `user`='$c_user' ORDER BY `slno` DESC limit 1 "));

			if ($check_close_account_last) {
				$first_date = $check_close_account_last["close_date"];

				$date_str .= " AND date>='$first_date'";
				$date_str_a .= " AND a.date>='$first_date'";

				$pay_max_slno_grtr = $check_close_account_last['pay_id'];
				$pay_max_slno_str_grtr = " AND `pay_id`>$pay_max_slno_grtr ";

				$exp_max_slno_grtr = $check_close_account_last['exp_slno'];
				$exp_max_slno_str_grtr = " AND `slno`>$exp_max_slno_grtr ";

				// Object a
				$ref_max_slno_grtr = $check_close_account_last['refund_slno'];
				$ref_max_slno_str_grtr = " AND a.`slno`>$ref_max_slno_grtr ";

				$fre_max_slno_grtr = $check_close_account_last['free_slno'];
				$fre_max_slno_str_grtr = " AND a.`slno`>$fre_max_slno_grtr ";

			} else {
				$first_date = "";
			}
		}
	}

	// No Break Wise Start
	$date_str = " AND `date` BETWEEN '$date1' AND '$date2'";
	$date_str_exp = " AND `expense_date` BETWEEN '$date1' AND '$date2'";
	$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
	$date_str_b = " AND b.`date` BETWEEN '$date1' AND '$date2'";

	$pay_max_slno_str_less = "";
	$exp_max_slno_str_less = "";
	$ref_max_slno_str_less = "";
	$fre_max_slno_str_less = "";

	$pay_max_slno_str_grtr = "";
	$exp_max_slno_str_grtr = "";
	$ref_max_slno_str_grtr = "";
	$fre_max_slno_str_grtr = "";
	// No Break Wise End

	$encounter_str = "";
	$encounter_pay_type = 0;
	if ($encounter > 0) {
		$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type = $pat_typ_text["type"];

		$encounter_str = " AND `encounter`='$encounter'";
	}

	$report_name = "Discount report ";
	//~ if($user_entry!=$c_user)
	//~ {
	//~ $report_name.=" from ".convert_date($date1)." to ".convert_date($date2);
	//~ }
	//~ else
	//~ {
	//~ $report_name.=" of Break no. ".$break_number;
	//~ }
	$report_name .= " from " . convert_date($date1) . " to " . convert_date($date2);
	?>
	<p style="margin-top: 2%;" id="print_div">

		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini"
				href="pages/new_account_discount_report_xls.php?date1=<?php echo $date11; ?>&date2=<?php echo $date22; ?>&encounter=<?php echo $encounter; ?>&user_entry=<?php echo $user_entry; ?>&pay_mode=<?php echo $pay_mode; ?>&account_break=<?php echo $account_break; ?>&branch_id=<?php echo $branch_id; ?>"><i
					class="icon-file icon-large"></i> Excel</a></span>

		<button type="button" class="btn btn-info btn-mini text-right"
			onclick="print_page('discount_report','<?php echo $date11; ?>','<?php echo $date22; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $pay_mode; ?>','<?php echo $account_break; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<b>Cashier: <?php echo $cashier_str; ?></b>
	<br>
	<b><?php echo $report_name; ?></b>
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Bill No</th>
				<th>Transaction No</th>
				<th>Patient Name</th>
				<th style="text-align:center;">Discount Amount</th>
				<th>Discount Reason</th>
				<th>User</th>
				<th>Encounter</th>
			</tr>
		</thead>
		<?php
		$total_amount = 0;
		$pmode = 1;
		$trans = 1;

		//$payment_mode_qry=mysqli_query($link, "SELECT `p_mode_name`,`operation` FROM `payment_mode_master` WHERE `p_mode_name` IN(SELECT DISTINCT `payment_mode` FROM `payment_detail_all` WHERE `pay_id`>0 AND (`discount_amount`>0 OR `discount_amount`<0) $payment_mode_str $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str) ORDER BY `sequence` ASC");
		//while($payment_mode=mysqli_fetch_array($payment_mode_qry))
		//{
		$pay_str = " SELECT * FROM `payment_detail_all` WHERE `pay_id`>0 AND (`discount_amount`>0 OR `discount_amount`<0) $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str ORDER BY `pay_id` ASC "; //  AND `payment_mode`='$payment_mode[p_mode_name]'
	
		//echo $pay_str."<br>";
	
		$pay_mode_amount = 0;
		$pay = 1;
		$pay_det_qry = mysqli_query($link, $pay_str);
		while ($pay_det = mysqli_fetch_array($pay_det_qry)) {
			$patient_id = $pay_det["patient_id"];
			$opd_id = $pay_det["opd_id"];

			$amount = $pay_det["discount_amount"];

			$pat_reg = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));

			$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
			$encounter_name = $pat_typ_text['p_type'];

			$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$patient_id' "));

			$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_det[user]' "));

			$cheque_ref_no = "";
			?>
			<tr>
				<td><?php echo $trans; ?></td>
				<td><?php echo $pay_det["date"]; ?></td>
				<td><?php echo $pay_det["opd_id"]; ?></td>
				<td><?php echo $pay_det["transaction_no"] . $cheque_ref_no; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td style="text-align:right;"><?php echo indian_currency_format($amount); ?></td>
				<td><?php echo $pay_det["discount_reason"]; ?></td>
				<td><?php echo $user_info["name"]; ?></td>
				<td><?php echo $encounter_name; ?></td>
			</tr>
			<?php
			$total_amount += $amount;
			$trans++;
			$pay++;
		}
		$pmode++;
		//}
		?>
		<tr>
			<td colspan="9"></td>
		</tr>
		<tr>
			<th colspan="4"></th>
			<th style="text-align:right;">Total Discount Amount</th>
			<th style="text-align:right;"><?php echo indian_currency_format($total_amount); ?></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?php
}

if ($_POST["type"] == "refund_report") {
	$encounter = trim($_POST['encounter']);
	$user_entry = trim($_POST['user_entry']);
	$pay_mode = trim($_POST['pay_mode']);
	$account_break = trim($_POST['account_break']);

	$close_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));
	if ($close_info) {
		$break_number = $close_info["counter"] . " on " . date("d-M-Y", strtotime($close_info["date"])) . " " . date("h:i A", strtotime($close_info["time"]));
	} else {
		$break_number = 0;
	}

	$pay_max_slno_str_less = "";
	$exp_max_slno_str_less = "";
	$ref_max_slno_str_less = "";
	$fre_max_slno_str_less = "";

	$pay_max_slno_str_grtr = "";
	$exp_max_slno_str_grtr = "";
	$ref_max_slno_str_grtr = "";
	$fre_max_slno_str_grtr = "";

	$date_str = "";
	$date_str_exp = "";
	$date_str_a = "";
	$date_str_b = "";
	$user = "";
	$cashier_str = "All user";
	if ($user_entry > 0) {
		$user = " AND `user`='$user_entry'";
		$user_a = " AND a.`user`='$user_entry'";

		$user_name = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$cashier_str = $user_name["name"];
	}

	$payment_mode_str = "";
	if ($pay_mode != "0") {
		$payment_mode_str = " AND `payment_mode`='$pay_mode'";
	}

	// Less
	if ($user_entry != $c_user) {
		// admin
		$date_str = " AND `date` BETWEEN '$date1' AND '$date2'";
		$date_str_exp = " AND `expense_date` BETWEEN '$date1' AND '$date2'";
		$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
		$date_str_b = " AND b.`date` BETWEEN '$date1' AND '$date2'";
	} else {
		if ($account_break == 0) {
			// Greater
			$check_close_account_last = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `user`='$user_entry' ORDER BY `slno` DESC limit 1 "));

			if ($check_close_account_last) {
				//$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));

				$first_date = $check_close_account_last['date'];

				$date_str = " AND date>='$first_date'";
				$date_str_a = " AND a.date>='$first_date'";

				$pay_max_slno_grtr = $check_close_account_last['pay_id'];
				$pay_max_slno_str_grtr = " AND `pay_id`>$pay_max_slno_grtr ";

				$exp_max_slno_grtr = $check_close_account_last['exp_slno'];
				$exp_max_slno_str_grtr = " AND `slno`>$exp_max_slno_grtr ";

				// Object a
				$ref_max_slno_grtr = $check_close_account_last['refund_slno'];
				$ref_max_slno_str_grtr = " AND a.`slno`>$ref_max_slno_grtr ";

				$fre_max_slno_grtr = $check_close_account_last['free_slno'];
				$fre_max_slno_str_grtr = " AND a.`slno`>$fre_max_slno_grtr ";

			} else {
				$first_date = "";
				$pay_max_slno_str_grtr = " AND `pay_id`>0 ";
				$exp_max_slno_str_grtr = " AND `slno`>0 ";
				$ref_max_slno_str_grtr = " AND a.`slno`>0 ";
				$fre_max_slno_str_grtr = " AND a.`slno`>0 ";
			}

		} else {
			$check_close_account_today = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));

			$last_date = $check_close_account_today["close_date"];

			$date_str = " AND date<='$last_date'";
			$date_str_a = " AND a.date<='$last_date'";

			$pay_max_slno_less = $check_close_account_today['pay_id'];
			$pay_max_slno_str_less = " AND `pay_id`<=$pay_max_slno_less ";

			$exp_max_slno_less = $check_close_account_today['exp_slno'];
			$exp_max_slno_str_less = " AND `slno`<=$exp_max_slno_less ";

			// Object a
			$ref_max_slno_less = $check_close_account_today['refund_slno'];
			$ref_max_slno_str_less = " AND a.`slno`<=$ref_max_slno_less ";

			$fre_max_slno_less = $check_close_account_today['free_slno'];
			$fre_max_slno_str_less = " AND a.`slno`<=$fre_max_slno_less ";

			// Greater
			$check_close_account_last = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`<'$account_break' AND `user`='$c_user' ORDER BY `slno` DESC limit 1 "));

			if ($check_close_account_last) {
				$first_date = $check_close_account_last["close_date"];

				$date_str .= " AND date>='$first_date'";
				$date_str_a .= " AND a.date>='$first_date'";

				$pay_max_slno_grtr = $check_close_account_last['pay_id'];
				$pay_max_slno_str_grtr = " AND `pay_id`>$pay_max_slno_grtr ";

				$exp_max_slno_grtr = $check_close_account_last['exp_slno'];
				$exp_max_slno_str_grtr = " AND `slno`>$exp_max_slno_grtr ";

				// Object a
				$ref_max_slno_grtr = $check_close_account_last['refund_slno'];
				$ref_max_slno_str_grtr = " AND a.`slno`>$ref_max_slno_grtr ";

				$fre_max_slno_grtr = $check_close_account_last['free_slno'];
				$fre_max_slno_str_grtr = " AND a.`slno`>$fre_max_slno_grtr ";

			} else {
				$first_date = "";
			}
		}
	}

	// No Break Wise Start
	$date_str = " AND `date` BETWEEN '$date1' AND '$date2'";
	$date_str_exp = " AND `expense_date` BETWEEN '$date1' AND '$date2'";
	$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
	$date_str_b = " AND b.`date` BETWEEN '$date1' AND '$date2'";

	$pay_max_slno_str_less = "";
	$exp_max_slno_str_less = "";
	$ref_max_slno_str_less = "";
	$fre_max_slno_str_less = "";

	$pay_max_slno_str_grtr = "";
	$exp_max_slno_str_grtr = "";
	$ref_max_slno_str_grtr = "";
	$fre_max_slno_str_grtr = "";
	// No Break Wise End

	$encounter_str = "";
	$encounter_pay_type = 0;
	if ($encounter > 0) {
		$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type = $pat_typ_text["type"];

		$encounter_str = " AND `encounter`='$encounter'";
	}

	$report_name = "Refund report ";
	//~ if($user_entry!=$c_user)
	//~ {
	//~ $report_name.=" from ".convert_date($date1)." to ".convert_date($date2);
	//~ }
	//~ else
	//~ {
	//~ $report_name.=" of Break no. ".$break_number;
	//~ }
	$report_name .= " from " . convert_date($date1) . " to " . convert_date($date2);
	?>
	<p style="margin-top: 2%;" id="print_div">

		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini"
				href="pages/new_account_refund_report_xls.php?date1=<?php echo $date11; ?>&date2=<?php echo $date22; ?>&encounter=<?php echo $encounter; ?>&user_entry=<?php echo $user_entry; ?>&pay_mode=<?php echo $pay_mode; ?>&account_break=<?php echo $account_break; ?>&branch_id=<?php echo $branch_id; ?>"><i
					class="icon-file icon-large"></i> Excel</a></span>

		<button type="button" class="btn btn-info btn-mini text-right"
			onclick="print_page('refund_report','<?php echo $date11; ?>','<?php echo $date22; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $pay_mode; ?>','<?php echo $account_break; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<b>Cashier: <?php echo $cashier_str; ?></b>
	<br>
	<b><?php echo $report_name; ?></b>
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Bill No</th>
				<th>Transaction No</th>
				<th>Patient Name</th>
				<th>Amount</th>
				<th>Reason</th>
				<th>User</th>
				<th>Encounter</th>
			</tr>
		</thead>
		<?php
		$payment_mode_array_refund = array();
		$total_amount = 0;
		$pmode = 1;
		$trans = 1;
		$payment_mode_name = "";

		$payment_mode_qry = mysqli_query($link, "SELECT `p_mode_name`,`operation` FROM `payment_mode_master` WHERE `p_mode_name` IN(SELECT DISTINCT `payment_mode` FROM `payment_detail_all` WHERE `pay_id`>0 AND `payment_type`='Refund' $payment_mode_str $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str) ORDER BY `sequence` ASC");
		$payment_mode_num = mysqli_num_rows($payment_mode_qry);
		if ($payment_mode_num > 0) {
			while ($payment_mode = mysqli_fetch_array($payment_mode_qry)) {
				if ($payment_mode_name != $payment_mode["p_mode_name"]) {
					echo "<tr><th colspan='8'>$payment_mode[p_mode_name] Refund</th></tr>";
				} else {
					$payment_mode_name = $payment_mode["p_mode_name"];
				}

				$pay_str = " SELECT * FROM `payment_detail_all` WHERE `pay_id`>0 AND `payment_type`='Refund' AND `payment_mode`='$payment_mode[p_mode_name]' $encounter_str $date_str $pay_max_slno_str_less $pay_max_slno_str_grtr $user $branch_str ORDER BY `pay_id` ASC ";

				//echo $pay_str."<br>";
	
				$pay_mode_amount = 0;
				$pay = 1;
				$pay_det_qry = mysqli_query($link, $pay_str);
				while ($pay_det = mysqli_fetch_array($pay_det_qry)) {
					$patient_id = $pay_det["patient_id"];
					$opd_id = $pay_det["opd_id"];

					$amount = $pay_det["refund_amount"];

					$pat_reg = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));

					$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
					$encounter_name = $pat_typ_text['p_type'];

					$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$patient_id' "));

					$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_det[user]' "));

					$cheque_ref_no = "";
					if ($pay_det["cheque_ref_no"] != "") {
						$cheque_ref_no = "<br><small style='font-size: 9px;'>(Ref : $pay_det[cheque_ref_no])</small>";
					}
					?>
					<tr>
						<td><?php echo $trans; ?></td>
						<td><?php echo $pay_det["date"]; ?></td>
						<td><?php echo $pay_det["opd_id"]; ?></td>
						<td><?php echo $pay_det["transaction_no"] . $cheque_ref_no; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo indian_currency_format($amount); ?></td>
						<td><?php echo $pay_det["refund_reason"]; ?></td>
						<td><?php echo $user_info["name"]; ?></td>
						<td><?php echo $encounter_name; ?></td>
					</tr>
					<?php
					$pay_mode_amount += $amount;
					$total_amount += $amount;
					$trans++;
					$pay++;
				}

				if ($payment_mode_name != $payment_mode["p_mode_name"]) {
					$payment_mode_array_refund[$payment_mode["p_mode_name"]] = $pay_mode_amount;
					?>
					<tr>
						<th colspan="4"></th>
						<th style="text-align:right">Total <?php echo $payment_mode["p_mode_name"]; ?> Refund</th>
						<th><?php echo indian_currency_format($pay_mode_amount); ?></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
					<?php
				}
				$pmode++;
			}
		}
		?>
		<tr>
			<td colspan="9"></td>
		</tr>
		<tr>
			<th colspan="4"></th>
			<th style="text-align:right">Total Refund Amount</th>
			<th><?php echo indian_currency_format($total_amount); ?></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?php
}

if ($_POST["type"] == "balance_received") {
	$encounter = trim($_POST['encounter']);
	$user_entry = trim($_POST['user_entry']);
	$pay_mode = trim($_POST['pay_mode']);
	$account_break = trim($_POST['account_break']);

	$close_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));
	if ($close_info) {
		$break_number = $close_info["counter"] . " on " . date("d-M-Y", strtotime($close_info["date"])) . " " . date("h:i A", strtotime($close_info["time"]));
	} else {
		$break_number = 0;
	}

	$pay_max_slno_str_less = "";
	$pay_max_slno_str_less_a = "";
	$exp_max_slno_str_less = "";
	$ref_max_slno_str_less = "";
	$fre_max_slno_str_less = "";

	$pay_max_slno_str_grtr = "";
	$pay_max_slno_str_grtr_a = "";
	$exp_max_slno_str_grtr = "";
	$ref_max_slno_str_grtr = "";
	$fre_max_slno_str_grtr = "";

	$date_str = "";
	$date_str_exp = "";
	$date_str_a = "";
	$date_str_b = "";
	$user = "";
	$cashier_str = "All user";
	if ($user_entry > 0) {
		$user = " AND `user`='$user_entry'";
		$user_a = " AND a.`user`='$user_entry'";

		$user_name = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$cashier_str = $user_name["name"];
	}

	$payment_mode_str_a = "";
	if ($pay_mode != "0") {
		$payment_mode_str_a = " AND a.`payment_mode`='$pay_mode'";
	}

	// Less
	if ($user_entry != $c_user) {
		// admin
		$date_str = " AND `date` BETWEEN '$date1' AND '$date2'";
		$date_str_exp = " AND `expense_date` BETWEEN '$date1' AND '$date2'";
		$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
		$date_str_b = " AND b.`date` BETWEEN '$date1' AND '$date2'";
	} else {
		if ($account_break == 0) {
			// Greater
			$check_close_account_last = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `user`='$user_entry' ORDER BY `slno` DESC limit 1 "));

			if ($check_close_account_last) {
				//$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));

				$first_date = $check_close_account_last['date'];

				$date_str = " AND date>='$first_date'";
				$date_str_a = " AND a.date>='$first_date'";

				$pay_max_slno_grtr = $check_close_account_last['pay_id'];
				$pay_max_slno_str_grtr = " AND `pay_id`>$pay_max_slno_grtr ";
				$pay_max_slno_str_grtr_a = " AND a.`pay_id`>$pay_max_slno_grtr ";

				$exp_max_slno_grtr = $check_close_account_last['exp_slno'];
				$exp_max_slno_str_grtr = " AND `slno`>$exp_max_slno_grtr ";

				// Object a
				$ref_max_slno_grtr = $check_close_account_last['refund_slno'];
				$ref_max_slno_str_grtr = " AND a.`slno`>$ref_max_slno_grtr ";

				$fre_max_slno_grtr = $check_close_account_last['free_slno'];
				$fre_max_slno_str_grtr = " AND a.`slno`>$fre_max_slno_grtr ";

			} else {
				$first_date = "";
				$pay_max_slno_str_grtr = " AND `pay_id`>0 ";
				$pay_max_slno_str_grtr_a = " AND a.`pay_id`>0 ";
				$exp_max_slno_str_grtr = " AND `slno`>0 ";
				$ref_max_slno_str_grtr = " AND a.`slno`>0 ";
				$fre_max_slno_str_grtr = " AND a.`slno`>0 ";
			}

		} else {
			$check_close_account_today = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`='$account_break' "));

			$last_date = $check_close_account_today["close_date"];

			$date_str = " AND date<='$last_date'";
			$date_str_a = " AND a.date<='$last_date'";

			$pay_max_slno_less = $check_close_account_today['pay_id'];
			$pay_max_slno_str_less = " AND `pay_id`<=$pay_max_slno_less ";
			$pay_max_slno_str_less_a = " AND a.`pay_id`<=$pay_max_slno_less ";

			$exp_max_slno_less = $check_close_account_today['exp_slno'];
			$exp_max_slno_str_less = " AND `slno`<=$exp_max_slno_less ";

			// Object a
			$ref_max_slno_less = $check_close_account_today['refund_slno'];
			$ref_max_slno_str_less = " AND a.`slno`<=$ref_max_slno_less ";

			$fre_max_slno_less = $check_close_account_today['free_slno'];
			$fre_max_slno_str_less = " AND a.`slno`<=$fre_max_slno_less ";

			// Greater
			$check_close_account_last = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close_new` WHERE `slno`<'$account_break' AND `user`='$c_user' ORDER BY `slno` DESC limit 1 "));

			if ($check_close_account_last) {
				$first_date = $check_close_account_last["close_date"];

				$date_str .= " AND date>='$first_date'";
				$date_str_a .= " AND a.date>='$first_date'";

				$pay_max_slno_grtr = $check_close_account_last['pay_id'];
				$pay_max_slno_str_grtr = " AND `pay_id`>$pay_max_slno_grtr ";
				$pay_max_slno_str_grtr_a = " AND a.`pay_id`>$pay_max_slno_grtr ";

				$exp_max_slno_grtr = $check_close_account_last['exp_slno'];
				$exp_max_slno_str_grtr = " AND `slno`>$exp_max_slno_grtr ";

				// Object a
				$ref_max_slno_grtr = $check_close_account_last['refund_slno'];
				$ref_max_slno_str_grtr = " AND a.`slno`>$ref_max_slno_grtr ";

				$fre_max_slno_grtr = $check_close_account_last['free_slno'];
				$fre_max_slno_str_grtr = " AND a.`slno`>$fre_max_slno_grtr ";

			} else {
				$first_date = "";
			}
		}
	}

	// No Break Wise Start
	$date_str = " AND `date` BETWEEN '$date1' AND '$date2'";
	$date_str_exp = " AND `expense_date` BETWEEN '$date1' AND '$date2'";
	$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
	$date_str_b = " AND b.`date` BETWEEN '$date1' AND '$date2'";

	$pay_max_slno_str_less = "";
	$exp_max_slno_str_less = "";
	$ref_max_slno_str_less = "";
	$fre_max_slno_str_less = "";

	$pay_max_slno_str_grtr = "";
	$exp_max_slno_str_grtr = "";
	$ref_max_slno_str_grtr = "";
	$fre_max_slno_str_grtr = "";
	// No Break Wise End

	$encounter_str = "";
	$encounter_str_a = "";
	$encounter_pay_type = 0;
	if ($encounter > 0) {
		$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type = $pat_typ_text["type"];

		$encounter_str = " AND `encounter`='$encounter'";
		$encounter_str_a = " AND a.`encounter`='$encounter'";
	}

	$report_name = "Balance received report ";
	//~ if($user_entry!=$c_user)
	//~ {
	//~ $report_name.=" from ".convert_date($date1)." to ".convert_date($date2);
	//~ }
	//~ else
	//~ {
	//~ $report_name.=" of Break no. ".$break_number;
	//~ }
	$report_name .= " from " . convert_date($date1) . " to " . convert_date($date2);
	?>
	<p style="margin-top: 2%;" id="print_div">

		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini"
				href="pages/new_account_balance_received_report_xls.php?date1=<?php echo $date11; ?>&date2=<?php echo $date22; ?>&encounter=<?php echo $encounter; ?>&user_entry=<?php echo $user_entry; ?>&pay_mode=<?php echo $pay_mode; ?>&account_break=<?php echo $account_break; ?>&branch_id=<?php echo $branch_id; ?>"><i
					class="icon-file icon-large"></i> Excel</a></span>

		<button type="button" class="btn btn-info btn-mini text-right"
			onclick="print_page('balance_received','<?php echo $date11; ?>','<?php echo $date22; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $pay_mode; ?>','<?php echo $account_break; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<b>Cashier: <?php echo $cashier_str; ?></b>
	<br>
	<b><?php echo $report_name; ?></b>
	<table class="table table-hover table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Bill No</th>
				<th>Transaction No</th>
				<th>Patient Name</th>
				<th>Amount</th>
				<th>User</th>
				<th>Encounter</th>
			</tr>
		</thead>
		<?php
		$payment_mode_array = array();
		$payment_mode_array_refund = array();
		$total_amount = 0;
		$pmode = 1;
		$trans = 1;
		$payment_mode_name = "";

		$payment_mode_qry = mysqli_query($link, "SELECT `p_mode_name`,`operation` FROM `payment_mode_master` WHERE `p_mode_name` IN(SELECT DISTINCT a.`payment_mode` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`payment_type`='Balance' $payment_mode_str_a $encounter_str_a $date_str_a $pay_max_slno_str_less_a $pay_max_slno_str_grtr_a $user_a $branch_str_b ) ORDER BY `sequence` ASC");
		while ($payment_mode = mysqli_fetch_array($payment_mode_qry)) {
			if ($payment_mode_name != $payment_mode["p_mode_name"]) {
				echo "<tr><th colspan='8'>$payment_mode[p_mode_name]</th></tr>";
			} else {
				$payment_mode_name = $payment_mode["p_mode_name"];
			}

			$pay_str = " SELECT a.* FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`payment_type`='Balance' AND a.`payment_mode`='$payment_mode[p_mode_name]' $encounter_str_a $date_str_a $pay_max_slno_str_less_a $pay_max_slno_str_grtr_a $user_a $branch_str_b ORDER BY a.`pay_id` ASC ";

			//echo $pay_str."<br>";
	
			$pay_mode_amount = 0;
			$pay = 1;
			$pay_det_qry = mysqli_query($link, $pay_str);
			while ($pay_det = mysqli_fetch_array($pay_det_qry)) {
				$patient_id = $pay_det["patient_id"];
				$opd_id = $pay_det["opd_id"];

				$amount = $pay_det["amount"];

				$pat_reg = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));

				$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				$encounter_name = $pat_typ_text['p_type'];

				$pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$patient_id' "));

				$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_det[user]' "));

				$cheque_ref_no = "";
				if ($pay_det["cheque_ref_no"] != "") {
					$cheque_ref_no = "<br><small style='font-size: 9px;'>(Ref : $pay_det[cheque_ref_no])</small>";
				}
				?>
				<tr>
					<td><?php echo $trans; ?></td>
					<td><?php echo $pay_det["date"]; ?></td>
					<td><?php echo $pay_det["opd_id"]; ?></td>
					<td><?php echo $pay_det["transaction_no"] . $cheque_ref_no; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo indian_currency_format($amount); ?></td>
					<td><?php echo $user_info["name"]; ?></td>
					<td><?php echo $encounter_name; ?></td>
				</tr>
				<?php
				$pay_mode_amount += $amount;
				$total_amount += $amount;
				$trans++;
				$pay++;
			}

			if ($payment_mode_name != $payment_mode["p_mode_name"]) {
				$payment_mode_array[$payment_mode["p_mode_name"]] = $pay_mode_amount;
				?>
				<tr>
					<th colspan="4"></th>
					<th style="text-align:right">Total <?php echo $payment_mode["p_mode_name"]; ?></th>
					<th><?php echo indian_currency_format($pay_mode_amount); ?></th>
					<th></th>
					<th></th>
				</tr>
				<?php
			}
			$pmode++;
		}
		?>
		<tr>
			<td colspan="8"></td>
		</tr>
		<tr>
			<th colspan="4"></th>
			<th style="text-align:right">Total Amount</th>
			<th><?php echo indian_currency_format($total_amount); ?></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?php
}
?>