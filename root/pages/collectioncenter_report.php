<?php
session_start();

include ("../../includes/connection.php");
require ('../../includes/global.function.php');
include ("../../includes/idgeneration.function.php");

$c_user = $_SESSION["emp_id"];
$date = date("Y-m-d");
$time = date("H:i:s");

$type = $_POST['type'];

if ($type == "collectionreport") {
	$cid = $_POST['cid'];
	$branch_id = $_POST['branch_id'];
	$fdate = $_POST['fdate'];
	$tdate = $_POST['tdate'];
	$rep = $_POST['rep'];

	$center_info = mysqli_fetch_array(mysqli_query($link, "SELECT `centreno`, `centrename` FROM `centremaster` WHERE `centreno`='$cid' "));

	if ($rep == 1) {
		?>
		<span class="text-right" id="print_div"><button type="button" class="btn btn-info"
				onclick="print_rep('1','<?php echo $cid; ?>','<?php echo $branch_id; ?>')">Print</button></span>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Date</th>
					<!--<th>UHID</th>-->
					<th>Bill No.</th>
					<th>Name</th>
					<th>Age/Sex</th>
					<th>Refer Doctor</th>
					<th style="text-align:right;">Total Amount</th>
					<th style="text-align:right;">Discount</th>
					<th style="text-align:right;">Paid</th>
					<th style="text-align:right;">Balance</th>
					<th>User</th>
				</tr>
			</thead>
			<?php
			//$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
			$center_str = "SELECT DISTINCT a.`centreno`, a.`centrename` FROM `centremaster` a, `uhid_and_opdid` b WHERE a.`centreno`=b.`center_no` AND b.`date` BETWEEN '$fdate' AND '$tdate'";
			if ($cid) {
				//$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
				$center_str .= " AND a.`centreno`='$cid'";
			}
			$center_str .= " AND b.`branch_id`='$branch_id' ORDER BY a.`centrename` ASC";
			//echo $center_str;
			$tot = $bal = $dis = $paid = 0;
			$center_qry = mysqli_query($link, $center_str);
			while ($center_info = mysqli_fetch_array($center_qry)) {
				echo "<tr><td colspan='10'><b>Centre Name : " . $center_info['centrename'] . "</b></td></tr>";

				$str = "SELECT * FROM `uhid_and_opdid` WHERE `center_no`='$center_info[centreno]' AND `date` BETWEEN '$fdate' AND '$tdate'";

				$qry = mysqli_query($link, $str);
				$i = 1;
				while ($reg_info = mysqli_fetch_array($qry)) {
					$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_info` WHERE `patient_id`='$reg_info[patient_id]'"));

					$rdoc = mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$reg_info[refbydoctorid]'"));

					$pat_pay_detail = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$reg_info[patient_id]' AND `opd_id`='$reg_info[opd_id]'"));
					$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$reg_info[user]' "));
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo convert_date_g($reg_info['date']); ?></td>
						<!--<td><?php echo $rr['uhid']; ?></td>-->
						<td><?php echo $reg_info['opd_id']; ?></td>
						<td><?php echo $pat_info['name']; ?></td>
						<td><?php echo $pat_info['age']; ?> 				<?php echo $pat_info['age_type']; ?>/<?php echo $pat_info['sex']; ?></td>
						<td><?php echo $rdoc['ref_name']; ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['tot_amount'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['dis_amt'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['advance'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['balance'], 2); ?></td>
						<td><?php echo $user_info['name']; ?></td>
					</tr>
					<?php
					$tot += $pat_pay_detail['tot_amount'];
					$bal += $pat_pay_detail['balance'];
					$dis += $pat_pay_detail['dis_amt'];
					$paid += $pat_pay_detail['advance'];
					$i++;
				}
				?>
				<?php
			}
			?>
			<tr>
				<th colspan="6" style="text-align:right;">Total Amount</th>
				<th style="text-align:right;"><?php echo number_format($tot, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($dis, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($paid, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($bal, 2); ?></th>
				<td></td>
			</tr>
		</table>
		<?php
	}
	if ($rep == 2) {
		//~ $center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
		$center_str = "SELECT DISTINCT a.`centreno`, a.`centrename` FROM `centremaster` a, `uhid_and_opdid` b WHERE a.`centreno`=b.`center_no` AND b.`date` BETWEEN '$fdate' AND '$tdate'";
		if ($cid) {
			//$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
			$center_str .= " AND a.`centreno`='$cid'";
		}
		$center_str .= " AND b.`branch_id`='$branch_id' ORDER BY a.`centrename` ASC";
		//echo $center_str;
		?>
		<span class="text-right" id="print_div"><button type="button" class="btn btn-info"
				onclick="print_rep('2','<?php echo $cid; ?>','<?php echo $branch_id; ?>')">Print</button></span>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Date</th>
					<th>Bill No.</th>
					<th>Patient Name</th>
					<th>Age/sex</th>
					<th>Refer Doctor</th>
					<th>Test Performed</th>
					<th style="text-align:right;">Test Rate</th>
					<th style="text-align:right;">Amount</th>
					<th style="text-align:right;">Discount</th>
					<th style="text-align:right;">Paid</th>
					<th style="text-align:right;">Balance</th>
					<th>User</th>
				</tr>
			</thead>
			<?php
			$tot_rate = $tot_dis = $tot_net = 0;
			$center_qry = mysqli_query($link, $center_str);
			while ($center_info = mysqli_fetch_array($center_qry)) {
				echo "<tr><td colspan='11'><b>Centre Name : " . $center_info['centrename'] . "</b></td></tr>";

				$str = "SELECT * FROM `uhid_and_opdid` WHERE `center_no`='$center_info[centreno]' AND `date` BETWEEN '$fdate' AND '$tdate'";

				$qry = mysqli_query($link, $str);
				$i = 1;
				while ($reg_info = mysqli_fetch_array($qry)) {
					$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_info` WHERE `patient_id`='$reg_info[patient_id]'"));

					$rdoc = mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$reg_info[refbydoctorid]'"));

					$pat_pay_detail = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$reg_info[patient_id]' AND `opd_id`='$reg_info[opd_id]'"));
					$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$reg_info[user]' "));

					$dis_per = round(($pat_pay_detail["dis_amt"] / $pat_pay_detail["tot_amount"]) * 100, 2);

					$pat_test_qry = mysqli_query($link, "SELECT * FROM `patient_test_details` WHERE `patient_id`='$reg_info[patient_id]' AND `opd_id`='$reg_info[opd_id]'");

					$ts = 1;
					$pat_test_num = mysqli_num_rows($pat_test_qry);
					while ($pat_test = mysqli_fetch_array($pat_test_qry)) {
						$test_info = mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$pat_test[testid]' "));

						$discount = round(($pat_test["test_rate"] * $dis_per) / 100, 2);

						$net_test_amount = $pat_test["test_rate"] - $discount;
						?>
						<tr>
							<?php
							if ($ts == 1) {
								?>
								<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $i; ?></td>
								<td rowspan="<?php echo $pat_test_num; ?>"><?php echo convert_date_g($reg_info['date']); ?></td>
								<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $reg_info['opd_id']; ?></td>
								<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $pat_info['name']; ?></td>
								<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $pat_info['age']; ?>
									<?php echo $pat_info['age_type']; ?>/<?php echo $pat_info['sex']; ?></td>
								<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $rdoc['ref_name']; ?></td>
							<?php } ?>
							<td><?php echo $test_info["testname"]; ?></td>
							<td style="text-align:right;"><?php echo $pat_test["test_rate"]; ?></td>
							<?php
							if ($ts == 1) {
								?>
								<td style="text-align:right;" rowspan="<?php echo $pat_test_num; ?>">
									<?php echo number_format($pat_pay_detail['tot_amount'], 2); ?></td>
								<td style="text-align:right;" rowspan="<?php echo $pat_test_num; ?>">
									<?php echo number_format($pat_pay_detail['dis_amt'], 2); ?></td>
								<td style="text-align:right;" rowspan="<?php echo $pat_test_num; ?>">
									<?php echo number_format($pat_pay_detail['advance'], 2); ?></td>
								<td style="text-align:right;" rowspan="<?php echo $pat_test_num; ?>">
									<?php echo number_format($pat_pay_detail['balance'], 2); ?></td>
								<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $user_info["name"]; ?></td>
							<?php } ?>
						</tr>
						<?php
						$ts++;
					}
					$i++;
					$tot_rate += $pat_pay_detail['tot_amount'];
					$tot_dis += $pat_pay_detail['dis_amt'];
					$tot_paid += $pat_pay_detail['advance'];
					$tot_bal += $pat_pay_detail['balance'];
				}

			}
			?>
			<tr>
				<th colspan="7" style="text-align:right;">Total</th>
				<th style="text-align:right;"><?php echo number_format($tot_rate, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($tot_rate, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($tot_dis, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($tot_paid, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($tot_bal, 2); ?></th>
				<td></td>
			</tr>
		</table>
		<?php
	}

	if ($rep == 3) {
		$i = 1;
		//~ $center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
		$center_str = "SELECT DISTINCT a.`centreno`, a.`centrename` FROM `centremaster` a, `uhid_and_opdid` b WHERE a.`centreno`=b.`center_no` AND b.`date` BETWEEN '$fdate' AND '$tdate'";
		if ($cid) {
			//$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
			$center_str .= " AND a.`centreno`='$cid'";
		}
		$center_str .= " AND b.`branch_id`='$branch_id' ORDER BY a.`centrename` ASC";
		//echo $center_str;
		?>
		<span class="text-right" id="print_div"><button type="button" class="btn btn-info"
				onclick="print_rep('3','<?php echo $cid; ?>','<?php echo $branch_id; ?>')">Print</button></span>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th style="text-align:right;">No.of Pt</th>
					<th style="text-align:right;">No.of Test</th>
					<th style="text-align:right;">Total Amount</th>
					<th style="text-align:right;">Discount</th>
					<th style="text-align:right;">Paid</th>
					<th style="text-align:right;">Balance</th>

				</tr>
			</thead>
			<?php
			$tot_rate = $tot_dis = $tot_net = 0;
			$center_qry = mysqli_query($link, $center_str);
			while ($center_info = mysqli_fetch_array($center_qry)) {
				$qpatient = mysqli_fetch_array(mysqli_query($link, "SELECT count(`opd_id`) as maxpatient FROM `uhid_and_opdid` WHERE `date` BETWEEN '$fdate' and '$tdate' and `center_no`='$center_info[centreno]'"));

				$qpatienttst = mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(count(a.`opd_id`),0) as maxtest FROM `patient_test_details` a,uhid_and_opdid b WHERE b.`date` BETWEEN '$fdate' and '$tdate' and b.`center_no`='$center_info[centreno]' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id`"));

				$qpatientttl = mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(sum(a.`tot_amount`),0) as maxtot,ifnull(sum(a.`dis_amt`),0) as maxdis,ifnull(sum(a.`advance`),0) as maxpaid,ifnull(sum(a.`balance`),0) as maxbal FROM `invest_patient_payment_details` a,uhid_and_opdid b WHERE b.`date` BETWEEN '$fdate' and '$tdate' and b.`center_no`='$center_info[centreno]' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id`"));

				$tot_amt += $qpatientttl['maxtot'];
				$tot_dis += $qpatientttl['maxdis'];
				$tot_paid += $qpatientttl['maxpaid'];
				$tot_bal += $qpatientttl['maxbal'];

				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $center_info['centrename']; ?></td>
					<td style="text-align:right;"><?php echo $qpatient['maxpatient']; ?></td>
					<td style="text-align:right;"><?php echo $qpatienttst['maxtest']; ?></td>
					<td style="text-align:right;"><?php echo $qpatientttl['maxtot']; ?></td>
					<td style="text-align:right;"><?php echo $qpatientttl['maxdis']; ?></td>
					<td style="text-align:right;"><?php echo $qpatientttl['maxpaid']; ?></td>
					<td style="text-align:right;"><?php echo $qpatientttl['maxbal']; ?></td>
				</tr>
				<?php


				$i++;
			}
			?>
			<tr>
				<th colspan="4" style="text-align:right;">Total</th>

				<th style="text-align:right;"><?php echo number_format($tot_amt, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($tot_dis, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($tot_paid, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($tot_bal, 2); ?></th>

			</tr>
		</table>
		<?php
	}

	if ($rep == 4) {
		?>
		<span class="text-right" id="print_div"><button type="button" class="btn btn-info"
				onclick="print_rep('4','<?php echo $cid; ?>','<?php echo $branch_id; ?>')">Print</button></span>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Date</th>
					<!--<th>UHID</th>-->
					<th>Bill No.</th>
					<th>Name</th>
					<th>Age/Sex</th>
					<th>Refer Doctor</th>
					<th style="text-align:right;">Total Amount</th>
					<th style="text-align:right;">Discount</th>
					<th style="text-align:right;">Paid</th>
					<th style="text-align:right;">Balance</th>
					<th>User</th>
				</tr>
			</thead>
			<?php
			//$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
			$center_str = "SELECT DISTINCT a.`centreno`, a.`centrename` FROM `centremaster` a, `uhid_and_opdid` b,invest_patient_payment_details c WHERE a.`centreno`=b.`center_no` AND b.`date` BETWEEN '$fdate' AND '$tdate' and b.`patient_id`=c.`patient_id` and b.`opd_id`=c.`opd_id` and c.`balance`>0 ";
			if ($cid) {
				//$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
				$center_str .= " AND a.`centreno`='$cid'";
			}
			$center_str .= " AND b.`branch_id`='$branch_id' ORDER BY a.`centrename` ASC";
			//echo $center_str;
			$tot = $bal = $dis = $paid = 0;
			$center_qry = mysqli_query($link, $center_str);
			while ($center_info = mysqli_fetch_array($center_qry)) {
				echo "<tr><td colspan='10'><b>Centre Name : " . $center_info['centrename'] . "</b></td></tr>";

				$str = "SELECT a.*,b.balance FROM `uhid_and_opdid` a,invest_patient_payment_details b WHERE a.`center_no`='$center_info[centreno]' AND a.`date` BETWEEN '$fdate' AND '$tdate' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.`balance`>0 order by a.slno";

				$qry = mysqli_query($link, $str);
				$i = 1;
				while ($reg_info = mysqli_fetch_array($qry)) {
					$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_info` WHERE `patient_id`='$reg_info[patient_id]'"));

					$rdoc = mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$reg_info[refbydoctorid]'"));

					$pat_pay_detail = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$reg_info[patient_id]' AND `opd_id`='$reg_info[opd_id]'"));
					$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$reg_info[user]' "));
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo convert_date_g($reg_info['date']); ?></td>
						<!--<td><?php echo $rr['uhid']; ?></td>-->
						<td><?php echo $reg_info['opd_id']; ?></td>
						<td><?php echo $pat_info['name']; ?></td>
						<td><?php echo $pat_info['age']; ?> 				<?php echo $pat_info['age_type']; ?>/<?php echo $pat_info['sex']; ?></td>
						<td><?php echo $rdoc['ref_name']; ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['tot_amount'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['dis_amt'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['advance'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['balance'], 2); ?></td>
						<td><?php echo $user_info['name']; ?></td>
					</tr>
					<?php
					$tot += $pat_pay_detail['tot_amount'];
					$bal += $pat_pay_detail['balance'];
					$dis += $pat_pay_detail['dis_amt'];
					$paid += $pat_pay_detail['advance'];
					$i++;
				}
				?>
				<?php
			}
			?>
			<tr>
				<th colspan="6" style="text-align:right;">Total Amount</th>
				<th style="text-align:right;"><?php echo number_format($tot, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($dis, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($paid, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($bal, 2); ?></th>
				<td></td>
			</tr>
		</table>
		<?php
	}

	if ($rep == 5) {
		if ($cid == "0") {
			echo "<div style='text-align:center;font-weight:bold;font-size:20px;'>Select Centre</div>";
			exit();
		}
		?>
		<!--<span class="text-right" id="print_div"><button type="button" class="btn btn-info" onclick="print_rep('4','<?php echo $cid; ?>','<?php echo $branch_id; ?>')">Print</button></span>-->
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Date</th>
					<!--<th>UHID</th>-->
					<th>Bill No.</th>
					<th>Name</th>
					<th>Age/Sex</th>
					<th>Refer Doctor</th>
					<th style="text-align:right;">Total Amount</th>
					<th style="text-align:right;">Discount</th>
					<th style="text-align:right;">Paid</th>
					<th style="text-align:right;">Balance</th>
					<th>User</th>
				</tr>
			</thead>
			<?php
			//$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
			$center_str = "SELECT DISTINCT a.`centreno`, a.`centrename` FROM `centremaster` a, `uhid_and_opdid` b,invest_patient_payment_details c WHERE a.`centreno`=b.`center_no` AND b.`date` BETWEEN '$fdate' AND '$tdate' and b.`patient_id`=c.`patient_id` and b.`opd_id`=c.`opd_id` and c.`balance`>0 ";
			if ($cid) {
				//$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
				$center_str .= " AND a.`centreno`='$cid'";
			}
			$center_str .= " AND b.`branch_id`='$branch_id' ORDER BY a.`centrename` ASC";
			//echo $center_str;
			$tot = $bal = $dis = $paid = 0;
			$center_qry = mysqli_query($link, $center_str);
			while ($center_info = mysqli_fetch_array($center_qry)) {
				echo "<tr><td colspan='10'><b>Centre Name : " . $center_info['centrename'] . "</b></td></tr>";

				$str = "SELECT a.*,b.balance FROM `uhid_and_opdid` a,invest_patient_payment_details b WHERE a.`center_no`='$center_info[centreno]' AND a.`date` BETWEEN '$fdate' AND '$tdate' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.`balance`>0 order by a.slno";

				$qry = mysqli_query($link, $str);
				$i = 1;
				while ($reg_info = mysqli_fetch_array($qry)) {
					$pat_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_info` WHERE `patient_id`='$reg_info[patient_id]'"));

					$rdoc = mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$reg_info[refbydoctorid]'"));

					$pat_pay_detail = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$reg_info[patient_id]' AND `opd_id`='$reg_info[opd_id]'"));
					$user_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$reg_info[user]' "));
					?>
					<tr>
						<td>
							<label name="chk<?php echo $i; ?>">
								<?php
								if ($cid && $pat_pay_detail['balance'] > 0) {
									?>
									<input type="checkbox" class="chk" id="chk<?php echo $i; ?>" value="<?php echo $i; ?>"
										onchange="chk_change('<?php echo $i; ?>')">
									<input type="hidden" class="balance" id="balance<?php echo $i; ?>"
										value="<?php echo $pat_pay_detail['balance']; ?>">
									<input type="hidden" class="patient_id" id="patient_id<?php echo $i; ?>"
										value="<?php echo $reg_info['patient_id']; ?>">
									<input type="hidden" class="opd_id" id="opd_id<?php echo $i; ?>"
										value="<?php echo $reg_info['opd_id']; ?>">
									<?php
								}
								?>
								<?php echo $i; ?>
							</label>
						</td>
						<td><?php echo convert_date_g($reg_info['date']); ?></td>
						<!--<td><?php echo $rr['uhid']; ?></td>-->
						<td><?php echo $reg_info['opd_id']; ?></td>
						<td><?php echo $pat_info['name']; ?></td>
						<td><?php echo $pat_info['age']; ?> 				<?php echo $pat_info['age_type']; ?>/<?php echo $pat_info['sex']; ?></td>
						<td><?php echo $rdoc['ref_name']; ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['tot_amount'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['dis_amt'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['advance'], 2); ?></td>
						<td style="text-align:right;"><?php echo number_format($pat_pay_detail['balance'], 2); ?></td>
						<td><?php echo $user_info['name']; ?></td>
					</tr>
					<?php
					$tot += $pat_pay_detail['tot_amount'];
					$bal += $pat_pay_detail['balance'];
					$dis += $pat_pay_detail['dis_amt'];
					$paid += $pat_pay_detail['advance'];
					$i++;
				}
				?>
				<?php
			}
			?>
			<tr>
				<th colspan="6" style="text-align:right;">Total Amount</th>
				<th style="text-align:right;"><?php echo number_format($tot, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($dis, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($paid, 2); ?></th>
				<th style="text-align:right;"><?php echo number_format($bal, 2); ?></th>
				<td></td>
			</tr>
			<?php
			if ($cid && $bal > 0) {
				?>
				<tr id="save_tr">
					<td colspan="11" style="text-align:center;">
						<table class="table table-condensed payment_table">
							<tr>
								<th>Payment Mode</th>
								<th>Balance Amount</th>
								<th>Receive Amount</th>
								<th>TDS Amount</th>
								<th>Cheque/Payment Date</th>
								<th>Cheque/Ref. No.</th>
								<th>Bank</th>
								<th>Branch</th>
							</tr>
							<tr>
								<td>
									<select id="payment_mode">
										<?php
										$qry = mysqli_query($link, "SELECT `p_mode_name` FROM `payment_mode_master` WHERE `operation`=1");
										while ($data = mysqli_fetch_array($qry)) {
											if ($data["p_mode_name"] == "Cheque") {
												$sel = "selected";
											} else {
												$sel = "";
											}
											echo "<option value='$data[p_mode_name]' $sel>$data[p_mode_name]</option>";
										}
										?>
									</select>
								</td>
								<td>
									<input type="text" id="total_balance_amount" value="0" disabled>
								</td>
								<td>
									<input type="text" id="total_receive_amount" value="0" onkeyup="total_receive_amount_up(this)">
								</td>
								<td>
									<input type="text" id="total_tax_amount" value="0" onkeyup="total_tax_amount_up(this)">
								</td>
								<td>
									<input type="text" id="cheque_date" value="<?php echo date("Y-m-d"); ?>" readonly>
								</td>
								<td>
									<input type="text" id="cheque_ref_no">
								</td>
								<td>
									<input type="text" id="bank_name">
								</td>
								<td>
									<input type="text" id="branch_name">
								</td>
							</tr>
						</table>
						<br>
						<br>
						<button class="btn btn-new" id="select_btn1" onclick="select(1)"><i class="icon-edit"></i> Select
							All</button>
						<button class="btn btn-new" id="select_btn2" onclick="select(2)" style="display:none;"><i
								class="icon-edit"></i> De-Select All</button>

						<button class="btn btn-save" onclick="save_balance()"><i class="icon-save"></i> Save Balance</button>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
		$pay_det_qry = mysqli_query($link, "SELECT * FROM `centre_balance_receive` WHERE `centreno`='$cid'");
		$pay_det_num = mysqli_num_rows($pay_det_qry);
		if ($pay_det_num > 0) {
			?>
			<div style="font-weight: bold;font-size: 14px;">Balance Receive records</div>
			<table class="table table-condensed table-bordered">
				<thead class="table_header_fix">
					<tr>
						<th>#</th>
						<th>Date From</th>
						<th>Date To</th>
						<th>Payment Date</th>
						<th>Payment Mode</th>
						<th style="text-align:right;">Total Amount</th>
						<th style="text-align:right;">Receive Amount</th>
						<th style="text-align:right;">TDS Amount</th>
						<th>Cheque/Ref. No.</th>
						<th>Bank</th>
						<th>Branch</th>
						<th>Entry User</th>
						<th>Entry Time</th>
						<th></th>
					</tr>
				</thead>
				<?php
				$n = 1;
				while ($pay_det = mysqli_fetch_array($pay_det_qry)) {
					$entry = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$pay_det[user]'"));
					?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo date("d-m-Y", strtotime($pay_det["date_from"])); ?></td>
						<td><?php echo date("d-m-Y", strtotime($pay_det["date_to"])); ?></td>
						<td><?php echo date("d-m-Y", strtotime($pay_det["payment_date"])); ?></td>
						<td><?php echo $pay_det["payment_mode"]; ?></td>
						<td style="text-align:right;"><?php echo $pay_det["balance_amount"]; ?></td>
						<td style="text-align:right;"><?php echo $pay_det["receive_amount"]; ?></td>
						<td style="text-align:right;"><?php echo $pay_det["tax_amount"]; ?></td>
						<td><?php echo $pay_det["cheque_ref_no"]; ?></td>
						<td><?php echo $pay_det["bank_name"]; ?></td>
						<td><?php echo $pay_det["branch_name"]; ?></td>
						<td><?php echo $entry["name"]; ?></td>
						<td><?php echo date("d-m-Y", strtotime($pay_det["date"])); ?>
							<?php echo date("h:i A", strtotime($pay_det["time"])); ?></td>
						<td>
							<button class="btn btn-print btn-mini"
								onclick="receipt_print('<?php echo $pay_det["slno"]; ?>','<?php echo $pay_det["centreno"]; ?>')"><i
									class="icon-print"></i> Print</button>
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
}

if ($rep == 6) {

	?>

	<p id="print_div" style="margin-top: 2%;">
		<b>Centre Bill from:</b> <?php echo convert_date_g($fdate) . " to " . convert_date_g($tdate); ?>

		<span class="text-right" id="print_div"><button type="button" class="btn btn-info"
				onclick="print_rep('6','<?php echo $cid; ?>','<?php echo $branch_id; ?>')">Print</button></span>

	</p>
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>Date</th>
				<th>Patient ID</th>
				<th>Name</th>
				<th>Total Amount</th>
				<th>Advance</th>
				<th>Discount</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$ctr_qr = "SELECT a.`patient_id`, a.`opd_id`, a.`date`, b.`patient_id`, b.`name`, c.`patient_id`, c.`opd_id`, c.`tot_amount`, c.`dis_amt`, c.`advance`, c.`balance` FROM `uhid_and_opdid` a, `patient_info` b, `invest_patient_payment_details` c WHERE a.`patient_id` = b.`patient_id` AND a.`patient_id` = c.`patient_id` AND a.`opd_id` = c.`opd_id` AND  a.`date` BETWEEN '$fdate' AND '$tdate' AND a.`branch_id`= '$branch_id'";

			if ($cid != '0') {
				$ctr_qr .= "AND a.`center_no`= '$cid'";
			}

			$ctr = mysqli_query($link, $ctr_qr);

			while ($centre_bill = mysqli_fetch_array($ctr)) {
				?>
				<tr>
					<td><?php echo $centre_bill["date"]; ?></td>
					<td><?php echo $centre_bill["opd_id"]; ?></td>
					<td><?php echo $centre_bill["name"]; ?></td>
					<td><?php echo $centre_bill["tot_amount"]; ?></td>
					<td><?php echo $centre_bill["advance"]; ?></td>
					<td><?php echo $centre_bill["dis_amt"]; ?></td>
					<td><?php echo $centre_bill["balance"]; ?></td>
				</tr>
				<?php
				$total_amount += $centre_bill['tot_amount'];
				$total_advance += $centre_bill['advance'];
				$total_discount += $centre_bill['dis_amt'];
				$total_balance += $centre_bill['balance'];
				$n++;
			}
			?>
			<tr>
				<td colspan="3">Bill Amount :</td>
				<td><?php echo number_format($total_amount, 2); ?></td>
				<td><?php echo number_format($total_advance, 2); ?></td>
				<td><?php echo number_format($total_discount, 2); ?></td>
				<td><?php echo number_format($total_balance, 2); ?></td>
			</tr>

			<tr>
				<td colspan="3">Pt. Discount (-) :</td>
				<td><?php echo number_format($total_discount, 2); ?></td>
				<td colspan="3"></td>

			</tr>
			<?php
			$vaccu_qry = mysqli_query($link, "SELECT SUM(a.`test_rate`) AS total FROM `patient_test_details` a, `testmaster` b WHERE a.`testid` = b.`testid` AND a.`testid` = '3375' AND a.`date` BETWEEN '$fdate' AND '$tdate'");

			$vaccu_tot = mysqli_fetch_assoc($vaccu_qry);
			?>

			<tr>
				<td colspan="3">Extra/Vaccu (-) :</td>
				<td><?php echo number_format($vaccu_tot['total'], 2); ?></td>
				<td colspan="3"></td>
			</tr>

			<?php
			$net_amount = $total_amount - $total_discount - $vaccu_tot['total'];
			?>

			<tr>
				<td colspan="3">Net Amount :</td>
				<td><?php echo number_format($net_amount, 2); ?></td>
				<td colspan="3"></td>
			</tr>

		</tbody>
	</table>

	<?php
}

if ($rep == 7) {

	?>
	<p id="print_div" style="margin-top: 2%;">
		<b>Centre Bill from:</b> <?php echo convert_date_g($fdate) . " to " . convert_date_g($tdate); ?>

		<span class="text-right" id="print_div"><button type="button" class="btn btn-info"
				onclick="print_rep('7','<?php echo $cid; ?>','<?php echo $branch_id; ?>')">Print</button></span>

	</p>
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>

				<th>Name</th>
				<th>Total Amount</th>
				<th>Advance</th>
				<th>Discount</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$centre_data = "SELECT DISTINCT a.`centreno`, a.`centrename` FROM `centremaster` a, `uhid_and_opdid` b WHERE a.`centreno` = b.`center_no` AND b.`date` BETWEEN '$fdate' AND '$tdate' AND b.`branch_id`= '$branch_id'";

			if ($cid != '0') {
				$centre_data .= "AND b.`center_no`= '$cid'";
			}

			$centrenm_qry = mysqli_query($link, $centre_data);

			$total_net_amt = 0;
			$total_net_dist_amt = 0;
			$total_net_adv_amt = 0;
			$total_net_bal_amt = 0;
			$counter = 1;

			while ($centrenm = mysqli_fetch_array($centrenm_qry)) {
				$centreno = $centrenm['centreno'];
				$net_amt_qry = mysqli_query($link, "SELECT ifnull(sum(a.tot_amount),0) as `net_amt`, ifnull(sum(a.dis_amt),0) as `net_dist_amt`, ifnull(sum(a.advance),0) as `net_adv_amt`, ifnull(sum(a.balance),0) as `net_bal_amt` FROM `invest_patient_payment_details` a, `uhid_and_opdid` b WHERE a.patient_id=b.patient_id AND a.opd_id=b.opd_id AND b.center_no='$centreno' AND b.branch_id='$branch_id' AND b.date BETWEEN '$fdate' AND '$tdate'");

				while ($net_amt = mysqli_fetch_array($net_amt_qry)) {
					$total_net_amt += $net_amt["net_amt"];
					$total_net_dist_amt += $net_amt["net_dist_amt"];
					$total_net_adv_amt += $net_amt["net_adv_amt"];
					$total_net_bal_amt += $net_amt["net_bal_amt"];
					?>
					<tr>
						<td><?php echo $counter++; ?></td>
						<td><?php echo $centrenm["centrename"]; ?></td>
						<td><?php echo $net_amt["net_amt"]; ?></td>
						<td><?php echo $net_amt["net_adv_amt"]; ?></td>
						<td><?php echo $net_amt["net_dist_amt"]; ?></td>
						<td><?php echo $net_amt["net_bal_amt"]; ?></td>
					</tr>
					<?php
				}
			}
			?>
			<tr>
				<td></td>
				<td><strong>Total</strong></td>
				<td><?php echo number_format($total_net_amt, 2); ?></td>
				<td><?php echo number_format($total_net_adv_amt, 2); ?></td>
				<td><?php echo number_format($total_net_dist_amt, 2); ?></td>
				<td><?php echo number_format($total_net_bal_amt, 2); ?></td>
			</tr>

		</tbody>

	</table>
	<?php


}

if ($type == "save_balance") {
	//~ print_r($_POST);
	//~ exit();

	$all_pat = mysqli_real_escape_string($link, $_POST['all_pat']);
	$payment_mode = mysqli_real_escape_string($link, $_POST['payment_mode']);
	$total_balance_amount = mysqli_real_escape_string($link, $_POST['total_balance_amount']);
	$total_receive_amount = mysqli_real_escape_string($link, $_POST['total_receive_amount']);
	$total_tax_amount = mysqli_real_escape_string($link, $_POST['total_tax_amount']);
	$cheque_date = mysqli_real_escape_string($link, $_POST['cheque_date']);
	$cheque_ref_no = mysqli_real_escape_string($link, $_POST['cheque_ref_no']);
	$bank_name = mysqli_real_escape_string($link, $_POST['bank_name']);
	$branch_name = mysqli_real_escape_string($link, $_POST['branch_name']);
	$centreno = mysqli_real_escape_string($link, $_POST['centreno']);
	$date1 = mysqli_real_escape_string($link, $_POST['date1']);
	$date2 = mysqli_real_escape_string($link, $_POST['date2']);

	$total_receive_amount = $total_balance_amount - $total_tax_amount;

	$total_pay = $total_receive_amount + $total_tax_amount;

	$tax_per = ($total_tax_amount / $total_balance_amount) * 100;

	if (!$tax_per) {
		$tax_per = 0;
	}

	if ($all_pat == "") {
		echo "Select Patient";
		exit();
	}
	if ($payment_mode == "") {
		echo "Select Payment Mode";
		exit();
	}
	if ($total_balance_amount == 0) {
		echo "Balance amount can't be zero.";
		exit();
	}
	if ($total_pay == 0) {
		echo "Payment can't be zero.";
		exit();
	}

	$zz = 0;

	if (mysqli_query($link, "INSERT INTO `centre_balance_receive`(`centreno`, `date_from`, `date_to`, `balance_amount`, `receive_amount`, `tax_amount`, `tax_per`, `payment_mode`, `cheque_ref_no`, `payment_date`, `bank_name`, `branch_name`, `user`, `date`, `time`) VALUES ('$centreno','$date1','$date2','$total_balance_amount','$total_receive_amount','$total_tax_amount','$tax_per','$payment_mode','$cheque_ref_no','$cheque_date','$bank_name','$branch_name','$c_user','$date','$time')")) {
		$all_pats = explode("@#@", $all_pat);
		foreach ($all_pats as $all_patz) {
			if ($all_patz) {
				$all_patx = explode("##", $all_patz);
				$patient_id = $all_patx[0];
				$opd_id = $all_patx[1];

				$pat_reg = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `center_no`='$centreno'"));

				$reg_type = mysqli_fetch_array(mysqli_query($link, "SELECT `type`,`bill_name` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
				if ($reg_type["type"] == 2) {
					$pat_pat_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
					if ($pat_pat_det["balance"] > 0) {
						$total = $pat_pat_det["tot_amount"];
						$advance = $pat_pat_det["advance"];

						$tax_amount_each = round(($pat_pat_det["balance"] * $tax_per) / 100);

						$now_pay = $pat_pat_det["balance"] - $tax_amount_each;

						$total_pay = $pat_pat_det["advance"] + $now_pay;

						$bill_no = generate_bill_no_new($reg_type["bill_name"], $pat_reg["type"]);

						$check_paid = mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
						$already_paid = $check_paid["paid"];

						if (mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$total','$already_paid','$now_pay','0','','0','','$tax_amount_each','TDS','0','','Balance','$payment_mode','$cheque_ref_no','$c_user','$date','$time','$pat_reg[type]') ")) {
							mysqli_query($link, "UPDATE `invest_patient_payment_details` SET `advance`='$total_pay',`tax_amount`='$tax_amount_each',`balance`='0' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");

							$zz++;
						}
					}
				}
			}
		}
	} else {
		//echo "Failed, try again later.";
	}

	if ($zz == 0) {
		echo "Failed, try again later";
	} else {
		echo "Saved";
	}
}

if ($type == "load_center") {
	$branch_id = $_POST['branch'];

	
	$q = mysqli_query($link, "SELECT `centreno`, `centrename` FROM `centremaster` WHERE `branch_id` = '$branch_id' ORDER BY `centrename`");
	$options = "<option value='0'>All Center</option>";
	while ($qrmkt1 = mysqli_fetch_array($q)) {
		$options .= "<option value='{$qrmkt1['centreno']}'>{$qrmkt1['centrename']}</option>";
	}
	echo $options;
}
?>