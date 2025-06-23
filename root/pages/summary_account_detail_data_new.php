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

$branch_str = " AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
$branch_str_a = " AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
$branch_str_b = " AND b.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];

if ($_POST["type"] == "load_users") {
	$not_accountant = array();
	array_push($not_accountant, 5, 6, 11, 12, 13, 20, 21);
	$not_accountant = join(',', $not_accountant);

	echo "<option value='0'>Select User</option>";

	$qry = mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` WHERE `emp_id`>0 AND `levelid` NOT IN ($not_accountant) AND `branch_id`='$branch_id' ORDER BY `name` ASC ");
	while ($data = mysqli_fetch_array($qry)) {
		if ($c_user == $data["emp_id"]) {
			//$sel_this = "selected";
		} else {
			$sel_this = "";
		}

		echo "<option value='$data[emp_id]' $sel_this>$data[name]</option>";
	}
}

if ($_POST["type"] == "summary_account_detail") {
	$encounter = $_POST['encounter'];
	$user_entry = $_POST['user_entry'];

	$user_str = "";
	$user_str_a = "";
	$user_str_b = "";
	if ($user_entry > 0) {
		$user_str = " AND `user`='$user_entry'";
		$user_str_a = " AND a.`user`='$user_entry'";
		$user_str_b = " AND b.`user`='$user_entry'";

		$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$user_name = $user_info["name"];
	} else {
		$user_name = "All";
	}

	$encounter_pay_type = 0;
	if ($encounter > 0) {
		$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type = $pat_typ_text["type"];
	}

	if ($encounter == 0) {
		echo "<h5>Select Department</h5>";
	}
	if ($encounter_pay_type == 1) {
		$pat_typ = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter = $pat_typ["p_type"];
		?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info btn-mini text-right print_btn" id="det_excel" onclick="export_excel()"><i
					class="icon-file icon-large"></i> Excel</button>
			<button type="button" class="btn btn-info btn-mini text-right print_btn"
				onclick="print_page('summary_account_detail','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
				style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amout</th>
					<th>Visit Fees</th>
					<th>Regd Fees</th>
					<th>Discount</th>
					<th>Refund</th>
					<th>Paid</th>
					<th>Balance</th>
					<!--<th>User</th>-->
					<!--<th>Encounter</th>-->
					<th>Date</th>
				</tr>
			</thead>
			<?php
			$n = 1;
			$tot_bill = $tot_dis = $tot_paid = $tot_bal = $tot_visit_fee = $tot_regd_fee = $tot_refund_amount = $tot_tax_amount = 0;

			$pat_reg_qry = mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='$encounter' $branch_str $user_str ORDER BY `slno` ASC ");
			while ($pat_reg = mysqli_fetch_array($pat_reg_qry)) {
				$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pat_reg[patient_id]'"));

				$uhid_id = $pat_info["patient_id"];
				$patient_id = $pat_reg["patient_id"];
				$opd_id = $pat_reg["opd_id"];

				$pat_show = 0;
				$bill_amt = $discount = $paid = $balance = $visit_fee = $regd_fee = $refund_amount = $tax_amount = 0;

				$con_pat_pay = mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$pat_reg[patient_id]' and opd_id='$pat_reg[opd_id]'"));

				$bill_amt = $con_pat_pay['tot_amount'];
				$visit_fee = $con_pat_pay['visit_fee'];
				$regd_fee = $con_pat_pay['regd_fee'];

				$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' $user_str_a "));

				$paid = $check_paid["paid"];
				$discount = $check_paid["discount"];
				//$refund_amount  =$check_paid["refund"];
				$tax_amount = $check_paid["tax"];

				$check_refund = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`refund_amount`),0) AS `refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' "));
				$refund_amount_other = $check_refund["refund"];

				if ($refund_amount_other > 0) {
					$bill_amt += $refund_amount_other;
				}

				$settle_amount = $paid + $discount + $tax_amount - $refund_amount;

				$balance = $bill_amt - $settle_amount;

				$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				$encounter_name = $pat_typ_text['p_type'];
				?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($bill_amt); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($visit_fee); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($regd_fee); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($discount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($refund_amount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($paid); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($balance); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
				</tr>
				<?php
				$n++;

				$tot_bill += $bill_amt;
				$tot_visit_fee += $visit_fee;
				$tot_regd_fee += $regd_fee;
				$tot_dis += $discount;
				$tot_refund_amount += $refund_amount;
				$tot_tax_amount += $tax_amount;
				$tot_paid += $paid;
				$tot_bal += $balance;
			}
			?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_bill); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_visit_fee); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_regd_fee); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_dis); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_refund_amount); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_paid); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_bal); ?></td>
				<td></td>
			</tr>
			<?php

			$con_bal_qry = mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ASC ");
			$con_bal_num = mysqli_num_rows($con_bal_qry);
			$zz = 0;
			if ($con_bal_num > 0) {
				echo "<tr><th colspan='11'>Balance Received</th></tr>";
				$zz = 1;
				$n = 1;
				$tot_bal_bill_amt = $tot_bal_discount = $tot_bal_paid = $tot_bal_balance = $tot_bal_visit_fee = $tot_bal_regd_fee = $tot_bal_refund_amount = $tot_bal_tax_amount = 0;
				while ($con_bal = mysqli_fetch_array($con_bal_qry)) {
					$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));

					$uhid_id = $pat_info["patient_id"];
					$patient_id = $con_bal["patient_id"];
					$opd_id = $con_bal["opd_id"];

					$bal_pat_show = 0;
					$bal_bill_amt = $bal_discount = $bal_paid = $bal_balance = $bal_visit_fee = $bal_regd_fee = $bal_refund_amount = $bal_tax_amount = 0;

					$bal_paid = $con_bal['amount'];

					$con_pat_pay = mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));

					$bal_bill_amt = $con_pat_pay['tot_amount'];

					$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' AND a.`pay_id`='$con_bal[pay_id]' $user_str_a "));

					$bal_paid = $check_paid["paid"];
					$bal_discount = $check_paid["discount"];
					$bal_refund_amount = $check_paid["refund"];
					$bal_tax_amount = $check_paid["tax"];

					$bal_visit_fee = $con_pat_pay['visit_fee'];
					$bal_regd_fee = $con_pat_pay['regd_fee'];

					if ($bal_discount < 0) {
						$bal_discount = 0;
					}

					$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
					$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
					$encounter_name = $pat_typ_text['p_type'];
					?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $con_bal["opd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_bill_amt); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_visit_fee); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_regd_fee); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_discount); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_refund_amount); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_paid); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_balance); ?></td>
						<!--<td><?php echo $quser["name"]; ?></td>-->
						<!--<td><?php echo $encounter_name; ?></td>-->
						<td><?php echo convert_date($con_bal["date"]); ?></td>
					</tr>
					<?php
					$n++;

					//~ $tot_bal_bill_amt+=$bal_bill_amt;
					//~ $tot_bal_visit_fee+=$bal_visit_fee;
					//~ $tot_bal_regd_fee+=$bal_regd_fee;
					$tot_bal_discount += $bal_discount;
					$tot_dis += $bal_discount;
					$tot_bal_refund_amount += $bal_refund_amount;
					$tot_refund_amount += $bal_refund_amount;
					$tot_bal_tax_amount += $bal_tax_amount;
					$tot_bal_paid += $bal_paid;
					//~ $tot_bal_balance+=$bal_balance;
	
				}
				?>
				<tr>
					<th colspan="6"><span class="text-right">Total</span></th>
					<!--<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_bill_amt); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_visit_fee); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_regd_fee); ?></td>-->
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_discount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_refund_amount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_paid); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_balance); ?></td>
					<td></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<th colspan="6"><span class="text-right">Grand Total</span></th>
				<!--<th><?php echo $rupees_symbol . indian_currency_format($tot_bill); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_visit_fee); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_regd_fee); ?></th>-->
				<th><?php echo $rupees_symbol . indian_currency_format($tot_dis); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_refund_amount); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_paid + $tot_bal_paid); ?></th>
				<!--<th><?php echo $rupees_symbol . indian_currency_format($tot_bal); ?></th>-->
				<th></th>
				<th></th>
			</tr>
			<?php
			if ($tot_refund_amount > 0) {
				?>
				<tr>
					<th colspan="8" style="text-align:right;">Net Received Amount</th>
					<th><?php echo $rupees_symbol . indian_currency_format($tot_paid + $tot_bal_paid - $tot_refund_amount); ?></th>
					<th></th>
					<th></th>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
	if ($encounter_pay_type == 2) {
		$pat_typ = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter = $pat_typ["p_type"];
		?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info btn-mini text-right print_btn" id="det_excel" onclick="export_excel()"><i
					class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info btn-mini text-right print_btn"
				onclick="print_page('summary_account_detail','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
				style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amout</th>
					<th>Discount</th>
					<th>Refund</th>
					<th>Paid</th>
					<th>Balance</th>
					<!--<th>User</th>-->
					<!--<th>Encounter</th>-->
					<th>Date</th>
				</tr>
			</thead>
			<?php
			$n = 1;
			$tot_bill = $tot_dis = $tot_paid = $tot_bal = $tot_refund_amount = $tot_tax_amount = 0;

			$pat_reg_qry = mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='$encounter' $user_str $branch_str ORDER BY `slno` ASC ");
			while ($pat_reg = mysqli_fetch_array($pat_reg_qry)) {
				$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pat_reg[patient_id]'"));

				$uhid_id = $pat_info["patient_id"];
				$patient_id = $pat_reg["patient_id"];
				$opd_id = $pat_reg["opd_id"];

				$pat_show = 0;
				$bill_amt = $discount = $paid = $balance = $refund_amount = $tax_amount = 0;

				$inv_pat_pay = mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$pat_reg[patient_id]' and opd_id='$pat_reg[opd_id]'"));

				$bill_amt = $inv_pat_pay['tot_amount'];

				$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' $user_str_a "));

				$paid = $check_paid["paid"];
				$discount = $check_paid["discount"];
				$refund_amount = $check_paid["refund"];
				$tax_amount = $check_paid["tax"];

				$check_refund = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`refund_amount`),0) AS `refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' "));
				$refund_amount_other = $check_refund["refund"];

				$check_refund_discount = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`discount_amount`),0) AS `discount` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND `discount_amount`<0 "));
				$refund_discount_other = $check_refund_discount["discount"];

				$bill_amt += $refund_amount_other + abs($refund_discount_other);

				$settle_amount = $paid + $discount + $tax_amount - $refund_amount;

				$balance = $bill_amt - $settle_amount;

				$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				$encounter_name = $pat_typ_text['p_type'];
				?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($bill_amt); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($discount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($refund_amount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($paid); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($balance); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
				</tr>
				<?php
				$n++;

				$tot_bill += $bill_amt;
				$tot_dis += $discount;
				$tot_refund_amount += $refund_amount;
				$tot_tax_amount += $rax_amount;
				$tot_paid += $paid;
				$tot_bal += $balance;
			}
			?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_bill); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_dis); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_refund_amount); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_paid); ?></td>
				<td><?php echo $rupees_symbol . indian_currency_format($tot_bal); ?></td>
				<td></td>
			</tr>
			<?php

			$inv_bal_qry = mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ASC ");
			$inv_bal_num = mysqli_num_rows($inv_bal_qry);
			$zz = 0;
			if ($inv_bal_num > 0) {
				echo "<tr><th colspan='11'>Balance Received</th></tr>";
				$zz = 1;
				$n = 1;
				$tot_bal_bill_amt = $tot_bal_discount = $tot_bal_paid = $tot_bal_balance = $tot_bal_visit_fee = $tot_bal_regd_fee = $tot_bal_refund_amount = 0;
				while ($inv_bal = mysqli_fetch_array($inv_bal_qry)) {
					$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));

					$uhid_id = $pat_info["patient_id"];
					$patient_id = $inv_bal["patient_id"];
					$opd_id = $inv_bal["opd_id"];

					$bal_pat_show = 0;
					$bal_bill_amt = $bal_discount = $bal_paid = $bal_balance = $bal_refund_amount = $bal_tax_amount = 0;

					$bal_paid = $inv_bal['amount'];

					$inv_pat_pay = mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]'"));

					$bal_bill_amt = $inv_pat_pay['tot_amount'];

					$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' AND a.`pay_id`='$inv_bal[pay_id]' $user_str_a "));

					$bal_paid = $check_paid["paid"];
					$bal_discount = $check_paid["discount"];
					$bal_refund_amount = $check_paid["refund"];
					$bal_tax_amount = $check_paid["tax"];

					if ($bal_discount < 0) {
						$bal_discount = 0;
					}

					$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
					$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
					$encounter_name = $pat_typ_text['p_type'];
					?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $inv_bal["opd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_bill_amt); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_discount); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_refund_amount); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_paid); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_balance); ?></td>
						<!--<td><?php echo $quser["name"]; ?></td>-->
						<!--<td><?php echo $encounter_name; ?></td>-->
						<td><?php echo convert_date($inv_bal["date"]); ?></td>
					</tr>
					<?php
					$n++;

					//$tot_bal_bill_amt+=$bal_bill_amt;
					$tot_bal_discount += $bal_discount;
					$tot_dis += $bal_discount;
					$tot_bal_refund_amount += $bal_refund_amount;
					$tot_refund_amount += $bal_refund_amount;
					$tot_bal_paid += $bal_paid;
					//$tot_bal_balance+=$bal_balance;
	
				}
				?>
				<tr>
					<th colspan="4"><span class="text-right">Total</span></th>
					<!--<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_bill_amt); ?></td>-->
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_discount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_refund_amount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_paid); ?></td>
					<!--<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_balance); ?></td>-->
					<td></td>
					<td></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<th colspan="4"><span class="text-right">Grand Total</span></th>
				<!--<th><?php echo $rupees_symbol . indian_currency_format($tot_bill); ?></th>-->
				<th><?php echo $rupees_symbol . indian_currency_format($tot_dis); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_refund_amount); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_paid + $tot_bal_paid); ?></th>
				<!--<th><?php echo $rupees_symbol . indian_currency_format($tot_bal); ?></th>-->
				<th></th>
				<th></th>
			</tr>
			<?php
			if ($tot_refund_amount > 0) {
				?>
				<tr>
					<th colspan="6" style="text-align:right;">Net Received Amount</th>
					<th><?php echo $rupees_symbol . indian_currency_format($tot_paid + $tot_bal_paid - $tot_refund_amount); ?></th>
					<th></th>
					<th></th>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
	if ($encounter != 3 && $encounter_pay_type == 3) {
		$pat_typ = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter = $pat_typ["p_type"];
		?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info btn-mini text-right print_btn" id="det_excel" onclick="export_excel()"><i
					class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info btn-mini text-right print_btn"
				onclick="print_page('summary_account_detail','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
				style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amout</th>
					<th>Discount</th>
					<th>Refund</th>
					<th>Paid</th>
					<th>Balance</th>
					<!--<th>User</th>-->
					<!--<th>Encounter</th>-->
					<th>Date</th>
				</tr>
			</thead>
			<?php
			$n = 1;
			$tot_bill_amt = $tot_discount = $tot_paid = $tot_refund_amount = $tot_tax_amount = $tot_balance = 0;
			$pat_reg_qry = mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='$encounter' $user_str $branch_str ORDER BY `slno` ASC ");
			while ($pat_reg = mysqli_fetch_array($pat_reg_qry)) {
				$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pat_reg[patient_id]'"));

				$uhid_id = $pat_info["patient_id"];
				$patient_id = $pat_reg["patient_id"];
				$opd_id = $pat_reg["opd_id"];

				$pat_show = 0;
				$bill_amt = $discount = $paid = $refund_amount = $tax_amount = $balance = 0;

				$tot_serv = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[opd_id]'"));

				$bill_amt = $tot_serv['sum_tot_amt'];

				$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' $user_str_a "));

				$paid = $check_paid["paid"];
				$discount = $check_paid["discount"];
				$refund_amount = $check_paid["refund"];
				$tax_amount = $check_paid["tax"];

				$check_refund = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`refund_amount`),0) AS `refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' "));
				$refund_amount_other = $check_refund["refund"];

				if ($refund_amount_other > 0) {
					$bill_amt += $refund_amount_other;
				}

				$settle_amount = $paid + $discount + $tax_amount - $refund_amount;

				$balance = $bill_amt - $settle_amount;

				$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				$encounter_name = $pat_typ_text['p_type'];
				?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($bill_amt); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($discount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($refund_amount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($paid); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($balance); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
				</tr>
				<?php
				$n++;

				$tot_bill_amt += $bill_amt;
				$tot_dis += $discount;
				$tot_refund_amount += $refund_amount;
				$tot_tax_amount += $tax_amount;
				$tot_paid += $paid;
				$tot_balance += $balance;
			}
			?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_bill_amt); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_dis); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_refund_amount); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_paid); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_balance); ?></th>
				<th></th>
			</tr>
			<?php
			$inv_bal_qry = mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ASC ");
			$inv_bal_num = mysqli_num_rows($inv_bal_qry);
			$zz = 0;
			if ($inv_bal_num > 0) {
				echo "<tr><th colspan='11'>Balance Received</th></tr>";
				$zz = 1;
				$n = 1;
				$tot_bal_bill_amt = $tot_bal_discount = $tot_bal_paid = $tot_bal_balance = $tot_bal_visit_fee = $tot_bal_regd_fee = $tot_bal_refund_amount = 0;
				while ($inv_bal = mysqli_fetch_array($inv_bal_qry)) {
					$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));

					$uhid_id = $pat_info["patient_id"];
					$patient_id = $inv_bal["patient_id"];
					$opd_id = $inv_bal["opd_id"];

					$bal_pat_show = 0;
					$bal_bill_amt = $bal_discount = $bal_paid = $bal_balance = $bal_refund_amount = $bal_tax_amount = 0;

					$bal_paid = $inv_bal['amount'];

					$tot_serv = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id'"));

					$bal_bill_amt = $tot_serv['sum_tot_amt'];

					$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' AND a.`pay_id`='$inv_bal[pay_id]' $user_str_a "));

					$bal_paid = $check_paid["paid"];
					$bal_discount = $check_paid["discount"];
					$bal_refund_amount = $check_paid["refund"];
					$bal_tax_amount = $check_paid["tax"];

					$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
					$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
					$encounter_name = $pat_typ_text['p_type'];
					?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $inv_bal["opd_id"]; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_bill_amt); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_discount); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_refund_amount); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_paid); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_balance); ?></td>
						<!--<td><?php echo $quser["name"]; ?></td>-->
						<!--<td><?php echo $encounter_name; ?></td>-->
						<td><?php echo convert_date($inv_bal["date"]); ?></td>
					</tr>
					<?php
					$n++;

					//$tot_bal_bill_amt+=$bal_bill_amt;
					$tot_bal_discount += $bal_discount;
					$tot_dis += $bal_discount;
					$tot_bal_refund_amount += $bal_refund_amount;
					$tot_refund_amount += $bal_refund_amount;
					$tot_bal_paid += $bal_paid;
					//$tot_bal_balance+=$bal_balance;
	
				}
				?>
				<tr>
					<th colspan="4"><span class="text-right">Total</span></th>
					<!--<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_bill_amt); ?></td>-->
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_discount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_refund_amount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_paid); ?></td>
					<!--<td><?php echo $rupees_symbol . indian_currency_format($tot_bal_balance); ?></td>-->
					<td></td>
					<td></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<th colspan="4"><span class="text-right">Grand Total</span></th>
				<!--<th><?php echo $rupees_symbol . indian_currency_format($tot_bill_amt); ?></th>-->
				<th><?php echo $rupees_symbol . indian_currency_format($tot_dis); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_refund_amount); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_paid + $tot_bal_paid); ?></th>
				<!--<th><?php echo $rupees_symbol . indian_currency_format($tot_bal); ?></th>-->
				<th></th>
				<th></th>
			</tr>
			<?php
			if ($tot_refund_amount > 0) {
				?>
				<tr>
					<th colspan="6" style="text-align:right;">Net Received Amount</th>
					<th><?php echo $rupees_symbol . indian_currency_format($tot_paid + $tot_bal_paid - $tot_refund_amount); ?></th>
					<th></th>
					<th></th>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
	if ($encounter == 3) {
		$grand_tot_paid = $grand_tot_discount = 0;

		$pat_typ = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter = $pat_typ["p_type"];
		?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info btn-mini text-right print_btn" id="det_excel" onclick="export_excel()"><i
					class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info btn-mini text-right print_btn"
				onclick="print_page('summary_account_detail','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
				style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th colspan="4">Patient Name</th>
					<th>Advance Paid</th>
					<th></th>
					<th></th>
					<th>Date</th>
				</tr>
			</thead>
			<tr>
				<th colspan="10">Advance Payment</th>
			</tr>
			<?php
			$n = 1;
			$tot_advance_paid = 0;
			$adv_bal_qry = mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`payment_type`='Advance' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ASC ");
			while ($adv_bal = mysqli_fetch_array($adv_bal_qry)) {
				$pat_show = 0;
				$advance_paid = 0;

				$advance_paid = $adv_bal["amount"];

				$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));

				$uhid_id = $pat_info["patient_id"];

				$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter_name = $pat_typ_text['p_type'];
				?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["opd_id"]; ?></td>
					<td colspan="4"><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($advance_paid); ?></td>
					<td></td>
					<td></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
				<?php
				$n++;

				$tot_advance_paid += $advance_paid;
				$grand_tot_paid += $advance_paid;
			}
			?>
			<tr>
				<th colspan="6"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_advance_paid); ?></th>
				<th colspan="3"></th>
			</tr>
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amount</th>
					<th>Previous Payment</th>
					<th>Discount Amount</th>
					<th>Final Paid</th>
					<th>Balance</th>
					<th>Refund</th>
					<th>Date</th>
				</tr>
			</thead>
			<tr>
				<th colspan="10">Final Payment</th>
			</tr>
			<?php
			$n = 1;
			$tot_bill_amt = $tot_discount = $tot_final_pay = $tot_refund_amount = $tot_tax_amount = $tot_balance = 0;
			$adv_bal_qry = mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`payment_type`='Final' AND a.`payment_mode`!='Credit' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ");
			while ($adv_bal = mysqli_fetch_array($adv_bal_qry)) {
				$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));

				$uhid_id = $pat_info["patient_id"];

				$patient_id = $adv_bal["patient_id"];
				$ipd = $opd_id = $adv_bal["opd_id"];

				$pat_show = 0;
				$bill_amt = $discount = $final_pay = $refund_amount = $tax_amount = $balance = $prev_pay = 0;

				$baby_serv_tot = 0;
				$baby_ot_total = 0;
				$delivery_qry = mysqli_query($link, " SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$patient_id' and ipd_id='$ipd' ");
				while ($delivery_check = mysqli_fetch_array($delivery_qry)) {
					$baby_tot_serv = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_serv_tot += $baby_tot_serv["tots"];

					// OT Charge Baby
					$baby_ot_tot_val = mysqli_fetch_array(mysqli_query($link, "select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_ot_total += $baby_ot_tot_val["g_tot"];

				}

				$no_of_days_val = mysqli_fetch_array(mysqli_query($link, "select * from ipd_pat_service_details where patient_id='$patient_id' and ipd_id='$ipd' and `group_id`='141' "));
				$no_of_days = $no_of_days_val["ser_quantity"];

				$tot_serv1 = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$patient_id' and ipd_id='$ipd' and group_id='141' "));
				$tot_serv_amt1 = $tot_serv1["tots"];
				//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
	
				$tot_serv2 = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$patient_id' and ipd_id='$ipd' and group_id!='141' "));
				$tot_serv_amt2 = $tot_serv2["tots"];

				// OT Charge
				$ot_tot_val = mysqli_fetch_array(mysqli_query($link, "select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$patient_id' and ipd_id='$ipd' "));
				$ot_total = $ot_tot_val["g_tot"];

				// Total
				$bill_amt = $tot_serv_amt1 + $tot_serv_amt2 + $baby_serv_tot + $ot_total + $baby_ot_total;

				$discount = $adv_bal['discount_amount'];
				$tax_amount = $adv_bal['tax_amount'];
				$refund_amount = $adv_bal['refund_amount'];
				$final_pay = $adv_bal['amount'];

				$check_refund = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`refund_amount`),0) AS `refund` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd' AND `payment_type`='Refund' $user_str "));
				$refund_amount += $check_refund['refund'];

				$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd' AND `payment_type`='Advance' $user_str "));

				$prev_pay = $check_paid["paid"];

				$balance = ($bill_amt - $discount - $tax_amount - $final_pay - $prev_pay + $refund_amount);

				$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter_name = $pat_typ_text['p_type'];
				?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($bill_amt); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($prev_pay); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($discount); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($final_pay); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($balance); ?></td>
					<td><?php echo $rupees_symbol . indian_currency_format($refund_amount); ?></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
				<?php
				$n++;

				$tot_bill_amt += $bill_amt;
				$tot_discount += $discount;
				$grand_tot_discount += $discount;
				$tot_final_pay += $final_pay;
				$grand_tot_paid += $final_pay;
				$tot_refund_amount += $refund_amount;
				$tot_balance += $balance;
			}
			?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_bill_amt); ?></th>
				<th></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_discount); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_final_pay); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_balance); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($tot_refund_amount); ?></th>
				<th></th>
			</tr>
			<?php
			$adv_bal_qry = mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`payment_type`='Balance' AND b.`type`='$encounter' $user_str_a ORDER BY a.`pay_id` ASC ");
			$adv_bal_num = mysqli_num_rows($adv_bal_qry);
			if ($adv_bal_num > 0) {
				?>
				<thead class="table_header_fix">
					<tr>
						<th>#</th>
						<th>Bill No</th>
						<th colspan="3">Patient Name</th>
						<th>Discount Amount</th>
						<th>Balance Paid</th>
						<th></th>
						<th></th>
						<th>Date</th>
					</tr>
				</thead>
				<tr>
					<th colspan="10">Balance Payment</th>
				</tr>
				<?php
				$n = 1;
				$tot_balance_paid = $tot_bal_discount = 0;
				while ($adv_bal = mysqli_fetch_array($adv_bal_qry)) {
					$pat_show = 0;
					$advance_paid = 0;
					$bal_discount = 0;

					$balance_paid = $adv_bal["amount"];

					if ($adv_bal["discount_amount"] > 0) {
						$bal_discount = $adv_bal["discount_amount"];
					}

					$pat_info = mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));

					$uhid_id = $pat_info["patient_id"];

					$quser = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
					$pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
					$encounter_name = $pat_typ_text['p_type'];
					?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $adv_bal["opd_id"]; ?></td>
						<td colspan="3"><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($bal_discount); ?></td>
						<td><?php echo $rupees_symbol . indian_currency_format($balance_paid); ?></td>
						<td></td>
						<td></td>
						<td><?php echo convert_date($adv_bal["date"]); ?></td>
					</tr>
					<?php
					$n++;

					$tot_balance_paid += $balance_paid;
					$grand_tot_paid += $balance_paid;
					$tot_bal_discount += $bal_discount;
					$grand_tot_discount += $bal_discount;
				}
				?>
				<tr>
					<th colspan="5"><span class="text-right">Total</span></th>
					<th><?php echo $rupees_symbol . indian_currency_format($tot_discount); ?></th>
					<th><?php echo $rupees_symbol . indian_currency_format($tot_balance_paid); ?></th>
					<th colspan="3"></th>
				</tr>
				<?php
			}
			?>
			<tr>
				<th colspan="5"><span class="text-right">Grand Total Amount</span></th>
				<th><?php echo $rupees_symbol . indian_currency_format($grand_tot_discount); ?></th>
				<th><?php echo $rupees_symbol . indian_currency_format($grand_tot_paid); ?></th>
				<th colspan="3"></th>
			</tr>

			<tr>
				<th colspan="6"><span class="text-right">Net Received Amount</span></th>
				<th><?php echo $rupees_symbol . indian_currency_format($grand_tot_paid - $tot_refund_amount); ?></th>
				<th colspan="3"></th>
			</tr>
		</table>
		<?php
	}
}

if ($_POST["type"] == "userwise_account") {
	$encounter = $_POST['encounter'];
	$user_entry = $_POST['user_entry'];

	$str = " SELECT DISTINCT a.`user` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id`";

	if ($date1 && $date2) {
		$str .= " AND a.`date` BETWEEN '$date1' AND '$date2'";

		$date_str_a = " AND a.`date` BETWEEN '$date1' AND '$date2'";
	}

	if ($branch_id > 0) {
		$str .= " AND b.`branch_id`='$branch_id'";

		$branch_id_str_b = " AND b.`branch_id`='$branch_id'";
	}

	if ($encounter > 0) {
		$str .= " AND b.`type`='$encounter'";

		$encounter_str_b = " AND b.`type`='$encounter'";
	}

	if ($user_entry > 0) {
		$str .= " AND a.`user`='$user_entry'";
	}

	$str .= " ORDER BY a.`user` ASC";

	//echo $str;

	$qry = mysqli_query($link, $str);
	?>
	<p id="print_div" style="margin-top: 2%;">
		<b>Userwise account from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>

		<button type="button" class="btn btn-info btn-mini text-right print_btn"
			onclick="print_page('userwise_account','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th style="width:30px;">#</th>
				<th>Cashier Name</th>
				<th style="text-align:right;width: 15%;">Received Amount</th>
				<th style="text-align:right;width: 15%;">Refund Amount</th>
				<th style="text-align:right;width: 15%;">Net Amount</th>
			</tr>
		</thead>
		<?php
		$n = 1;
		$total_received_amount = $total_refund_amount = $total_net_amount = 0;
		while ($data = mysqli_fetch_array($qry)) {
			$user_name = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$data[user]' "));

			$pay_det = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_rcv`, ifnull(SUM(a.`refund_amount`),0) AS `tot_refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`user`='$data[user]' $date_str_a $branch_id_str_b $encounter_str_b "));

			$received_amount = $pay_det["tot_rcv"];
			$total_received_amount += $pay_det["tot_rcv"];

			$refund_amount = $pay_det["tot_refund"];
			$total_refund_amount += $pay_det["tot_refund"];

			$net_amount = $received_amount - $refund_amount;
			$total_net_amount += $received_amount - $refund_amount;
			?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $user_name["name"]; ?></td>
				<td style="text-align:right;"><?php echo indian_currency_format($received_amount); ?></td>
				<td style="text-align:right;"><?php echo indian_currency_format($refund_amount); ?></td>
				<td style="text-align:right;"><?php echo indian_currency_format($net_amount); ?></td>
			</tr>
			<?php
			$n++;
		}
		?>
		<tr>
			<th></th>
			<th style="text-align:right;">Total</th>
			<th style="text-align:right;"><?php echo indian_currency_format($total_received_amount); ?></th>
			<th style="text-align:right;"><?php echo indian_currency_format($total_refund_amount); ?></th>
			<th style="text-align:right;"><?php echo indian_currency_format($total_net_amount); ?></th>
		</tr>
	</table>
	<?php
}

if ($_POST["type"] == "deptwise_test") {
	$encounter = $_POST['encounter'];
	$user_entry = $_POST['user_entry'];
	?>
	<p id="print_div" style="margin-top: 2%;">
		<b>Dept Wise Test Report from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>

		<button type="button" class="btn btn-info btn-mini text-right print_btn"
			onclick="print_page('deptwise_test','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>Department Name</th>
				<th>Total Test Count</th>
				<th style="text-align:right;">Total Test Cost</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$tot_test = 0;
			$tot_amount = 0;

			$test_val = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`test_rate`),0) AS `tot_amount`,COUNT(a.`testid`) AS `tot_test` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`category_id`='1' AND a.`test_rate`>0 "));

			$tot_test += $test_val["tot_test"];
			$tot_amount += $test_val["tot_amount"];
			?>
			<tr onclick="load_test('1','0','<?php echo $date1; ?>','<?php echo $date2; ?>')" style="cursor:pointer;">
				<th>Pathology</th>
				<td><?php echo $test_val["tot_test"]; ?></td>
				<td style="text-align:right;"><?php echo indian_currency_format($test_val["tot_amount"]); ?></td>
			</tr>
			<?php
			$test_dept_str = "SELECT DISTINCT b.`category_id`,b.`type_id` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`category_id`!='1' AND a.`test_rate`>0";
			$test_dept_qry = mysqli_query($link, $test_dept_str);
			while ($test_dept = mysqli_fetch_array($test_dept_qry)) {
				$dept_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `test_department` WHERE `id`='$test_dept[type_id]' "));

				$test_val = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`test_rate`),0) AS `tot_amount`,COUNT(a.`testid`) AS `tot_test` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`category_id`!='1' AND b.`type_id`='$test_dept[type_id]' AND a.`test_rate`>0 "));

				$tot_test += $test_val["tot_test"];
				$tot_amount += $test_val["tot_amount"];
				?>
				<tr onclick="load_test('<?php echo $test_dept["category_id"]; ?>','<?php echo $test_dept["type_id"]; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>')"
					style="cursor:pointer;">
					<th><?php echo $dept_info["name"]; ?></th>
					<td><?php echo $test_val["tot_test"]; ?></td>
					<td style="text-align:right;"><?php echo indian_currency_format($test_val["tot_amount"]); ?></td>
				</tr>
				<?php
			}

			$discount_str = " SELECT ifnull(SUM(a.`discount_amount`),0) AS `tot_discount` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' ";

			$discount_val = mysqli_fetch_array(mysqli_query($link, $discount_str));
			$discount_amount = $discount_val["tot_discount"];

			$net_bill_amount = $tot_amount - $discount_amount;

			$same_date_pat_str = " SELECT ifnull(SUM(a.`amount`),0) AS `tot_paid`, ifnull(SUM(a.`refund_amount`),0) AS `tot_refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' ";

			$same_date_pat_val = mysqli_fetch_array(mysqli_query($link, $same_date_pat_str));
			$advance_paid_amount = $same_date_pat_val["tot_paid"];
			$same_refund_amount = $same_date_pat_val["tot_refund"];

			$back_date_pat_str = " SELECT ifnull(SUM(a.`amount`),0) AS `tot_paid`, ifnull(SUM(a.`refund_amount`),0) AS `tot_refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' ";

			$back_date_pat_val = mysqli_fetch_array(mysqli_query($link, $back_date_pat_str));
			$balance_paid_amount = $back_date_pat_val["tot_paid"];
			$back_refund_amount = $back_date_pat_val["tot_refund"];

			$total_refund_amount = $same_refund_amount + $back_refund_amount;

			$total_balance_amount = $net_bill_amount - $advance_paid_amount - $same_refund_amount;

			$total_receive_amount = $advance_paid_amount + $balance_paid_amount - $total_refund_amount;
			?>
		</tbody>
		<tfoot>
			<tr>
				<th style="text-align:right;">Total</th>
				<th><?php echo $tot_test; ?></th>
				<th style="text-align:right;"><?php echo indian_currency_format($tot_amount); ?></th>
			</tr>
			<tr>
				<th style="text-align:right;" colspan="2">Total Discount</th>
				<th style="text-align:right;"><?php echo indian_currency_format($discount_amount); ?></th>
			</tr>
			<tr>
				<th style="text-align:right;" colspan="2">Net Amount of Bill</th>
				<th style="text-align:right;"><?php echo indian_currency_format($net_bill_amount); ?></th>
			</tr>
			<tr>
				<th style="text-align:right;" colspan="2">Total Advance Received</th>
				<th style="text-align:right;"><?php echo indian_currency_format($advance_paid_amount); ?></th>
			</tr>
			<tr>
				<th style="text-align:right;" colspan="2">Total Balance Received</th>
				<th style="text-align:right;"><?php echo indian_currency_format($balance_paid_amount); ?></th>
			</tr>
			<tr>
				<th style="text-align:right;" colspan="2">Total Balance Due</th>
				<th style="text-align:right;"><?php echo indian_currency_format($total_balance_amount); ?></th>
			</tr>
			<?php
			if ($total_refund_amount > 0) {
				?>
				<tr>
					<th style="text-align:right;" colspan="2">Total Refund Amount</th>
					<th style="text-align:right;"><?php echo indian_currency_format($total_refund_amount); ?></th>
				</tr>
				<?php
			}
			?>
			<tr>
				<th style="text-align:right;" colspan="2">Total Receive Amount</th>
				<th style="text-align:right;"><?php echo indian_currency_format($total_receive_amount); ?></th>
			</tr>
		</tfoot>
	</table>
	<?php
}

if ($_POST["type"] == "balance_patient") {
	$str = "SELECT `patient_id`,`opd_id`,`tot_amount`,`dis_amt`,`advance`,`balance`,`date`,`time`,`user` FROM `invest_patient_payment_details` WHERE `balance`>0 AND `date` BETWEEN '$date1' AND '$date2' ORDER BY `slno` ASC";

	$qry = mysqli_query($link, $str);
	?>
	<p id="print_div" style="margin-top: 2%;">
		<b>Balance patient list from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>

		<button type="button" class="btn btn-info btn-mini text-right print_btn"
			onclick="print_page('balance_patient','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No.</th>
				<th>Patient Name</th>
				<th>Phone</th>
				<th style="text-align:right;">Bill Amount</th>
				<th style="text-align:right;">Discount Amount</th>
				<th style="text-align:right;">Paid Amount</th>
				<th style="text-align:right;">Balance Amount</th>
				<th>Reg Date</th>
				<th>Entry User</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$n = 1;
			$total_balance = 0;
			while ($data = mysqli_fetch_array($qry)) {
				$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name`,`sex`,`dob`,`phone` FROM `patient_info` WHERE `patient_id`='$data[patient_id]'"));

				$entry_user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$data[user]' "));
				?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $data["patient_id"]; ?></td>
					<td><?php echo $data["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $pat_info["phone"]; ?></td>
					<td style="text-align:right;"><?php echo indian_currency_format($data["tot_amount"]); ?></td>
					<td style="text-align:right;"><?php echo indian_currency_format($data["dis_amt"]); ?></td>
					<td style="text-align:right;"><?php echo indian_currency_format($data["advance"]); ?></td>
					<td style="text-align:right;"><?php echo indian_currency_format($data["balance"]); ?></td>
					<td><?php echo date("d-m-Y", strtotime($data["date"])); ?></td>
					<td><?php echo $entry_user_info["name"]; ?></td>
				</tr>
				<?php
				$total_balance += $data["balance"];
				$n++;
			}
			?>
		</tbody>
		<tbody>
			<tr>
				<th style="text-align:right;" colspan="8">Total</th>
				<th style="text-align:right;"><?php echo indian_currency_format($total_balance); ?></th>
				<th></th>
				<th></th>
			</tr>
		</tbody>
	</table>
	<?php
}

if ($_POST["type"] == "details_report") {

	$encounter = $_POST['encounter'];
	$user_entry = $_POST['user_entry'];

	?>
	<p id="print_div" style="margin-top: 2%;">
		<b>Details Report from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>

		<button type="button" class="btn btn-info btn-mini text-right print_btn"
			onclick="print_page('details_report','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed" id="det_border">
		<thead class="table_header_fix">
			<tr>
				<th>Date</th>
				<th>Patient ID</th>
				<th>Patient Name</th>
				<th>Doctor Name</th>
				<th>Exe. Name</th>
				<th>Center Name</th>
				<th>Test Rate</th>
				<th>Amount</th>
				<th>Discount</th>
				<th>Paid</th>
				<th>Balance</th>
				<th>User</th>
			</tr>
		</thead>
		<?php
		$dt = mysqli_query($link, "SELECT DISTINCT `date` from `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2' AND `branch_id`='$branch_id'");
		?>
		<tbody>
			<?php
			$total_test_rate = 0;
			$total_amount = 0;
			$total_discount = 0;
			$total_paid = 0;
			$total_balance = 0;

			while ($rw = mysqli_fetch_array($dt)) {

				$info_str = "SELECT a.*, b.`name` AS `patient_name`, b.`patient_id`, c.`refbydoctorid`, c.`ref_name`, d.`centreno`, d.`centrename`, e.`patient_id`, e.`opd_id`, e.`tot_amount`, e.`dis_amt`, e.`advance`, e.`balance`, f.`emp_id`, f.`name`  FROM `uhid_and_opdid` a, `patient_info` b, `refbydoctor_master` c, `centremaster` d, `invest_patient_payment_details` e, `employee` f  WHERE a.`date` = '$rw[date]' AND a.`patient_id` = b.`patient_id` AND a.`refbydoctorid` = c.`refbydoctorid` AND a.`center_no` = d.`centreno`AND  a.`patient_id` = e.`patient_id` AND a.`opd_id` = e.`opd_id` AND a.`user` = f.`emp_id`";

				if ($branch_id > 0) {
					$info_str .= " AND a.`branch_id`='$branch_id'";
				}

				if ($user_entry > 0) {
					$info_str .= " AND e.`user`='$user_entry'";
				}

				if ($encounter > 0) {
					$info_str .= " AND a.`type` = '$encounter'";
				}

				$info_str .= " ORDER BY a.`date`, a.`slno` ASC";

				$info = mysqli_query($link, $info_str);

				$info_num = mysqli_num_rows($info);
				if ($info_num > 0) {
					?>
					<tr style="background-color: #d0d0d0;">
						<th colspan="12"><?php echo $rw['date'] ?></th>
					</tr>
					<?php

				}
				while ($inf = mysqli_fetch_array($info)) {

					$exe = mysqli_query($link, "SELECT a.`refbydoctorid`, a.`ref_name`, b.*, c.`id`, c.`name` 
                            FROM `refbydoctor_master` a, `doctor_group` b, `marketingpersonal` c 
                            WHERE a.`refbydoctorid` = b.`refbydoctorid` AND a.`refbydoctorid`= '$inf[refbydoctorid]' 
                            AND b.`id` = c.`id`");

					$exe_nm = mysqli_fetch_array($exe);
					?>
					<tr>
						<td></td>
						<td><?php echo $inf['opd_id'] ?></td>
						<td><?php echo $inf['patient_name'] ?></td>
						<td><?php echo $inf['ref_name'] ?></td>

						<td><?php echo $exe_nm['name'] ?></td>
						<td><?php echo $inf['centrename'] ?></td>
						<td></td>
						<td><?php echo $inf['tot_amount'] ?></td>
						<td><?php echo $inf['dis_amt'] ?></td>
						<td><?php echo $inf['advance'] ?></td>
						<td><?php echo $inf['balance'] ?></td>
						<td><?php echo $inf['name'] ?></td>
					</tr>
					<?php
					$test_det = mysqli_query($link, "SELECT b.`test_rate`,c.`testid`, c.`testname` FROM `patient_test_details` b, `testmaster` c WHERE b.`testid` = c.`testid` AND b.`patient_id` = '$inf[patient_id]' AND  b.`opd_id` = '$inf[opd_id]'");

					while ($test_info = mysqli_fetch_array($test_det)) {
						?>
						<tr>
							<td colspan="2"></td>
							<td colspan="4" style="padding-left: 40px; font-style: italic;"><?php echo $test_info['testname'] ?></td>
							<td><?php echo $test_info['test_rate'] ?></td>
							<td colspan="5"></td>
						</tr>
						<?php
						$total_test_rate += $test_info['test_rate'];
					}
					$total_amount += $inf['tot_amount'];
					$total_discount += $inf['dis_amt'];
					$total_paid += $inf['advance'];
					$total_balance += $inf['balance'];
				}
			}
			?>
			<tr style="font-weight: bold;">
				<td colspan="6" style="text-align: end;">Total</td>
				<td><?php echo $total_test_rate; ?></td>
				<td><?php echo $total_amount; ?></td>
				<td><?php echo $total_discount; ?></td>
				<td><?php echo $total_paid; ?></td>
				<td><?php echo $total_balance; ?></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	<?php
}


if ($_POST["type"] == "monthly_accounts") {
	$encounter = $_POST['encounter'];
	$user_entry = $_POST['user_entry'];


	$pmode_str = "SELECT DISTINCT a.`payment_mode` FROM `payment_detail_all` a, `payment_mode_master` b WHERE a.`payment_mode` = b.`p_mode_name` AND a.`date` BETWEEN '$date1' AND '$date2'";

	$str = "SELECT DISTINCT a.`date` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id`";

	if ($date1 && $date2) {
		$str .= " AND a.`date` BETWEEN '$date1' AND '$date2'";
	}

	if ($branch_id > 0) {
		$str .= " AND b.`branch_id` = '$branch_id'";
	}

	if ($encounter > 0) {
		$str .= " AND b.`type` = '$encounter'";
	}
	$user_a = "";
	if ($user_entry > 0) {
		$str .= " AND a.`user` = '$user_entry'";
		$user_a = " AND a.`user` = '$user_entry'";
		$pmode_str .= " AND a.`user` = '$user_entry'";
	}

	$pmode_str .= " AND a.`payment_mode` != 'Credit' ORDER BY b.`sequence` ASC";
	$str .= " ORDER BY a.`date` ASC";

	$qry = mysqli_query($link, $str);
	?>
	<p id="print_div" style="margin-top: 2%;">
		<b>Monthly Details from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>

		<button type="button" class="btn btn-info btn-mini text-right print_btn"
			onclick="print_page('monthly_accounts','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th style="width:30px;">#</th>
				<th>Date</th>
				<th style="text-align:right;">Case Amount</th>
				<th style="text-align:right;">Sale Recpt</th>
				<th style="text-align:right;">Due Recpt</th>
				<th style="text-align:right;">Sale Refund</th>
				<th style="text-align:right;">Due Refund</th>
				<th style="text-align:right;">Expense Amount</th>
				<th style="text-align:right;">Net Recpt</th>
				<th style="text-align:right;">Cash Amount</th>
				<th style="text-align:right;">Card Amount</th>
				<th style="text-align:right;">Cheque Amount</th>
				<th style="text-align:right;">Digital Wallet Amount</th>
				<th style="text-align:right;">Credit Amount</th>
			</tr>
		</thead>
		<?php
		$n = 1;
		$cash_total = 0;
		$card_total = 0;
		$cheque_total = 0;
		$wallet_total = 0;
		$credit_total = 0;
		$total_expense_amount = 0;
		$daily_case_amount_total = 0;
		$total_received_amount = $total_refund_amount = $total_net_amount = 0;
		$total_sale_received_amount = $total_sale_refund_amount = 0;
		$total_due_received_amount = $total_due_refund_amount = 0;

		while ($date_row = mysqli_fetch_array($qry)) {
			$date = $date_row['date'];

			$daily_case = mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(a.`tot_amount`),0) AS `total` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date`= b.`date` AND a.`date` = '$date' AND b.`branch_id` = '$branch_id' $user_a"));
			$daily_case_amount = $daily_case["total"];

			$daily_case_amount_total += $daily_case_amount;

			// Fetch data grouped by date
			$sale_pay_det = mysqli_fetch_array(mysqli_query($link, "SELECT IFNULL(SUM(a.`amount`),0) AS `tot_rcv`, IFNULL(SUM(a.`refund_amount`),0) AS `tot_refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date`= b.`date` AND a.`date` = '$date' AND b.`branch_id` = '$branch_id' $user_a"));

			$total_sale_received_amount += $sale_pay_det["tot_rcv"];
			$total_sale_refund_amount += $sale_pay_det["tot_refund"];

			$due_pay_det = mysqli_fetch_array(mysqli_query($link, "SELECT IFNULL(SUM(a.`amount`),0) AS `tot_rcv`, IFNULL(SUM(a.`refund_amount`),0) AS `tot_refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date`!= b.`date` AND a.`date` = '$date' AND b.`branch_id` = '$branch_id' $user_a"));

			$total_due_received_amount += $due_pay_det["tot_rcv"];
			$total_due_refund_amount += $due_pay_det["tot_refund"];

			// Expenses
			$expense_det = mysqli_fetch_array(mysqli_query($link, "SELECT IFNULL(SUM(`Amount`),0) AS `total` FROM `expensedetail` WHERE `entry_date` = '$date' AND `branch`='$branch_id'"));
			$expense_amount = $expense_det["total"];

			$total_expense_amount += $expense_amount;

			$net_amount = $sale_pay_det["tot_rcv"] + $due_pay_det["tot_rcv"] - $sale_pay_det["tot_refund"] - $due_pay_det["tot_refund"] - $expense_amount;
			$total_net_amount += $net_amount;

			$tot_sale_due_refund = $sale_pay_det["tot_refund"] + $due_pay_det["tot_refund"];

			$pay_det_cash = mysqli_fetch_array(mysqli_query($link, "SELECT IFNULL(SUM(a.`amount`),0) AS `tot_rcv` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date` = '$date' AND b.`branch_id` = '$branch_id' $user_a AND a.`payment_mode` = 'Cash'"));
			$pay_det_cash["tot_rcv"] = $pay_det_cash["tot_rcv"] - $tot_sale_due_refund - $expense_amount;
			$cash_total += $pay_det_cash["tot_rcv"];

			$pay_det_card = mysqli_fetch_array(mysqli_query($link, "SELECT IFNULL(SUM(a.`amount`),0) AS `tot_rcv` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date` = '$date' AND b.`branch_id` = '$branch_id' $user_a AND a.`payment_mode` = 'Card'"));
			$card_total += $pay_det_card["tot_rcv"];

			$pay_det_cheque = mysqli_fetch_array(mysqli_query($link, "SELECT IFNULL(SUM(a.`amount`),0) AS `tot_rcv` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date` = '$date' AND b.`branch_id` = '$branch_id' $user_a AND a.`payment_mode` = 'Cheque'"));
			$cheque_total += $pay_det_cheque["tot_rcv"];

			$pay_det_wallet = mysqli_fetch_array(mysqli_query($link, "SELECT IFNULL(SUM(a.`amount`),0) AS `tot_rcv` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date` = '$date' AND b.`branch_id` = '$branch_id' $user_a AND a.`payment_mode` NOT IN('Cash','Card','Cheque')"));
			$wallet_total += $pay_det_wallet["tot_rcv"];

			// Credit
			$credit_amount = 0;
			$credit_pat_qry = mysqli_query($link, "SELECT a.* FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date` = '$date' AND b.`branch_id` = '$branch_id' $user_a AND a.`payment_mode` = 'Credit'");
			while ($credit_pat = mysqli_fetch_array($credit_pat_qry)) {
				$bal_amount = $credit_pat["balance_amount"];

				$pat_pay_bal = mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount` FROM `payment_detail_all` WHERE `patient_id`='$credit_pat[patient_id]' and `opd_id`='$credit_pat[opd_id]' AND `payment_type`='Balance' AND `date` BETWEEN '$date1' AND '$date2' AND `pay_id`>$credit_pat[pay_id] "));

				$bal_paid_amount = $pat_pay_bal["paid"] + $pat_pay_bal["discount"];

				$net_balance_amount = $bal_amount - $bal_paid_amount;
				if ($net_balance_amount < 0) {
					$net_balance_amount = 0;
				}

				$credit_amount += $net_balance_amount;
				$credit_total += $net_balance_amount;
			}
			?>
			<tbody>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $date; ?></td>
					<td style="text-align:right;"><?php echo number_format($daily_case_amount, 2) ?></td>
					<td style="text-align:right;"><?php echo number_format($sale_pay_det["tot_rcv"], 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($due_pay_det["tot_rcv"], 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($sale_pay_det["tot_refund"], 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($due_pay_det["tot_refund"], 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($expense_amount, 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($net_amount, 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($pay_det_cash["tot_rcv"], 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($pay_det_card["tot_rcv"], 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($pay_det_cheque["tot_rcv"], 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($pay_det_wallet["tot_rcv"], 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($credit_amount, 2); ?></td>
				</tr>
				<?php $n++; ?>
				<?php
		}
		?>
		<tfoot>
			<tr>
				<td></td>
				<td></td>
				<td style="text-align:right;"><b><?php echo number_format($daily_case_amount_total, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($total_sale_received_amount, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($total_due_received_amount, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($total_sale_refund_amount, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($total_due_refund_amount, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($total_expense_amount, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($total_net_amount, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($cash_total, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($card_total, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($cheque_total, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($wallet_total, 2); ?></b></td>
				<td style="text-align:right;"><b><?php echo number_format($credit_total, 2); ?></b></td>
			</tr>
		</tfoot>
	</table>
	<?php
}

if ($_POST["type"] == "summary_accounts") {

	$encounter = $_POST['encounter'];
	$user_entry = $_POST['user_entry'];
	?>
	<p id="print_div" style="margin-top: 2%;">
		<b>Userwise account from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>

		<button type="button" class="btn btn-info btn-mini text-right print_btn"
			onclick="print_page('summary_accounts','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed" id="det_border">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Patient ID</th>
				<th>Name</th>
				<th>Center</th>
				<th style="text-align:right;">Total Amount</th>
				<th style="text-align:right;">Advance</th>
				<th style="text-align:right;">Discount</th>
				<th style="text-align:right;">Balance</th>
				<th>User</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sum_accounts_qry = "
            SELECT DISTINCT a.`patient_id`, a.`opd_id`, a.`tot_amount`, a.`dis_amt`, a.`advance`, a.`balance`, a.`user`, a.`date`, b.`patient_id`, b.`name`, d.`centrename`, e.`name` AS `emp_name` 
            FROM `invest_patient_payment_details` a 
            JOIN `patient_info` b ON a.`patient_id` = b.`patient_id`
            JOIN `uhid_and_opdid` c ON a.`opd_id` = c.`opd_id`
            JOIN `centremaster` d ON c.`center_no` = d.`centreno`
            JOIN `employee` e ON a.`user` = e.`emp_id`
            WHERE a.`date` BETWEEN '$date1' AND '$date2' AND c.`branch_id` = '$branch_id'
        ";
		

			if ($user_entry > 0) {
				$sum_accounts_qry .= " AND c.`user`='$user_entry'";
			}

			if ($encounter > 0) {
				$sum_accounts_qry .= " AND c.`type` = '$encounter'";
			}

			$sum_accounts_qry .= " ORDER BY c.`date`, c.`slno` ASC";

			$sum_acc_qr = mysqli_query($link, $sum_accounts_qry);
			$n = 1;

			$total_amount = 0;
			$total_advance = 0;
			$total_discount = 0;
			$total_balance = 0;

			while ($sum_qry = mysqli_fetch_array($sum_acc_qr)) {
				?>
				<tr>
					<td><?php echo $n ?></td>
					<td><?php echo $sum_qry['opd_id'] ?></td>
					<td><?php echo $sum_qry['name'] ?></td>
					<td><?php echo $sum_qry['centrename'] ?></td>
					<td style="text-align:right;"><?php echo $sum_qry['tot_amount'] ?></td>
					<td style="text-align:right;"><?php echo $sum_qry['advance'] ?></td>
					<td style="text-align:right;"><?php echo $sum_qry['dis_amt'] ?></td>
					<td style="text-align:right;"><?php echo $sum_qry['balance'] ?></td>
					<td><?php echo $sum_qry['emp_name'] ?></td>
				</tr>
				<?php
				$total_amount += $sum_qry['tot_amount'];
				$total_advance += $sum_qry['advance'];
				$total_discount += $sum_qry['dis_amt'];
				$total_balance += $sum_qry['balance'];
				$n++;
			}
			?>
			<tr style="font-weight:bold;">
				<td colspan="4" style="text-align: end;">Total</td>
				<td style="text-align:right;"><?php echo number_format($total_amount, 2); ?></td>
				<td style="text-align:right;"><?php echo number_format($total_advance, 2); ?></td>
				<td style="text-align:right;"><?php echo number_format($total_discount, 2); ?></td>
				<td style="text-align:right;"><?php echo number_format($total_balance, 2); ?></td>
				<td></td>
			</tr>
		</tbody>
	</table>

	<table class="table table-bordered table-condensed" id="det_border">
		<thead class="table_header_fix">
			<tr>
				<th>Advance Received</th>
				<th>Balance Received</th>
				<th>Card</th>
				<th>UPI</th>
				<th>Total Received</th>
				<th>Expense</th>
				<th>Total Expense</th>
				<th>Cash in Hand (RS)</th>
			</tr>
		</thead>
		<tbody>
			<?php

			$query_payments = "SELECT a.* FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND b.`branch_id` = '$branch_id' $user_a AND a.`date` BETWEEN '$date1' AND '$date2' ";

			if ($user_entry > 0) {
				$query_payments .= " AND b.`user`='$user_entry'";
			}

			if ($encounter > 0) {
				$query_payments .= " AND b.`type` = '$encounter'";
			}

			$result_payments = mysqli_query($link, $query_payments);

			if (mysqli_num_rows($result_payments) > 0) {
				$advance_received = 0;
				$balance_received = 0;
				$card_received = 0;
				$upi_received = 0;
				$cash_received = 0;

				while ($row = mysqli_fetch_assoc($result_payments)) {
					if ($row['payment_type'] == 'Advance') {
						$advance_received += $row['amount'];
					} elseif ($row['payment_type'] == 'Balance') {
						$balance_received += $row['amount'];
					}

					if ($row['payment_mode'] == 'Card') {
						$card_received += $row['amount'];
					} elseif ($row['payment_mode'] == 'UPI') {
						$upi_received += $row['amount'];
					} elseif ($row['payment_mode'] == 'Cash') {
						$cash_received += $row['amount'];
					}
				}

				$total_received = $advance_received + $balance_received;
			} else {
				$total_received = $advance_received = $balance_received = $card_received = $upi_received = 0;
			}

			// SQL query for expenses
			$query_expenses = "SELECT `Amount`, `expense_date`, `user` FROM `expensedetail` WHERE `expense_date` BETWEEN '$date1' AND '$date2' AND `branch`='$branch_id'";
			$result_expenses = mysqli_query($link, $query_expenses);

			if (mysqli_num_rows($result_expenses) > 0) {
				$total_expense = 0;

				while ($row = mysqli_fetch_assoc($result_expenses)) {
					$total_expense += $row['Amount'];
				}
			} else {
				$total_expense = 0;
			}

			// Calculate cash in hand
			$cash_in_hand = $cash_received - $total_expense;

			// Display the results in the table
			echo "<tr>";
			echo "<td>" . number_format($advance_received, 2) . "</td>";
			echo "<td>" . number_format($balance_received, 2) . "</td>";
			echo "<td>" . number_format($card_received, 2) . "</td>";
			echo "<td>" . number_format($upi_received, 2) . "</td>";
			echo "<td>" . number_format($total_received, 2) . "</td>";
			echo "<td>" . number_format($total_expense, 2) . "</td>";
			echo "<td>" . number_format($total_expense, 2) . "</td>";
			echo "<td>" . number_format($cash_in_hand, 2) . "</td>";
			echo "</tr>";

			?>
		</tbody>
	</table>

	<?php
}


if ($_POST["type"] == "print_summary") {

	?>

	<p id="print_div" style="margin-top: 2%;">
		<b>Summary from:</b> <?php echo convert_date_g($date1) . " to " . convert_date_g($date2); ?>

		<button type="button" class="btn btn-info btn-mini text-right print_btn"
			onclick="print_page('print_summary','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $encounter; ?>','<?php echo $user_entry; ?>','<?php echo $branch_id; ?>')"
			style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>

	<table class="table table-bordered table-condensed" id="det_border">
		<thead class="table_header_fix">
			<tr>
				<th>Patient ID</th>
				<th>Name</th>
				<th>Amount</th>
				<th>Advance</th>
				<th>Discount</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$summary_print_qry = mysqli_query($link, "SELECT a.`slno`, a.`patient_id`, a.`opd_id`, b.`patient_id`, b.`opd_id`, b.`tot_amount`, b.`dis_amt`, b.`advance`, b.`balance`, c.`name` FROM `uhid_and_opdid` a, `invest_patient_payment_details` b, `patient_info` c WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`patient_id` = c.`patient_id` AND a.`branch_id` = '$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2' ORDER BY a.`slno` DESC LIMIT 1");
			while ($print_sum = mysqli_fetch_array($summary_print_qry)) {
				?>
				<tr>
					<td><?php echo $print_sum['opd_id'] ?></td>
					<td><?php echo $print_sum['name'] ?></td>
					<td><?php echo $print_sum['tot_amount'] ?></td>
					<td><?php echo $print_sum['advance'] ?></td>
					<td><?php echo $print_sum['dis_amt'] ?></td>
					<td><?php echo $print_sum['balance'] ?></td>
				</tr>
				<?php

				$daily_case = mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(a.`tot_amount`),0) AS `total`,ifnull(SUM(a.`dis_amt`),0) AS `discount`,ifnull(SUM(a.`advance`),0) AS `advance` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id` = b.`patient_id` AND a.`opd_id` = b.`opd_id` AND a.`date`= b.`date` AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`branch_id` = '$branch_id' $user_a"));

				$balance = $daily_case['total'] - $daily_case['advance'] - $daily_case['discount'];
				?>
				<tr>
					<td colspan="2"></td>
					<td><?php echo number_format($daily_case['total'], 2); ?></td>
					<td><?php echo number_format($daily_case['advance'], 2); ?></td>
					<td><?php echo number_format($daily_case['discount'], 2); ?></td>
					<td><?php echo number_format($balance, 2); ?></td>
				</tr>

				<?php

			}
			?>
		</tbody>
	</table>
	<?php
}



?>
