<?php
include('../../includes/connection.php');

function convert_date($date)
{
	if ($date) {
		$timestamp = strtotime($date);
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

$type = $_POST['type'];

if ($type == 1) {
	$s_typ = $_POST['s_typ'];
	$en_fdate = $_POST[en_fdate];
	$en_tdate = $_POST[en_tdate];
	$ex_fdate = $_POST[ex_fdate];
	$ex_tdate = $_POST[ex_tdate];


?>
	<input type="hidden" id="s_typ" value="2" />
	<input type="hidden" id="ex_from" value="<?php echo $ex_fdate; ?>" />
	<input type="hidden" id="ex_to" value="<?php echo $ex_tdate; ?>" />
	Expense Date From <b><i><?php echo $ex_fdate; ?> and <?php echo $ex_tdate; ?></i></b>
	<table class="table table-report table-bordered table-stripped">
		<tr>
			<th>#</th>
			<th>Ledger Name</th>
			<th>Total</th>
		</tr>
		<?php
		$i = 1;
		$total_am = 0;
		$ledg = mysqli_query($link, "select * from ledger_master order by ledger_name");
		while ($ld = mysqli_fetch_array($ledg)) {


			$qry = mysqli_query($link, "select * from expensedetail where ledger_id='$ld[id]' and expense_date between '$ex_fdate' and '$ex_tdate'");

			if (mysqli_num_rows($qry) > 0) {
				$tot = mysqli_fetch_array(mysqli_query($link, "select sum(Amount) as tot from expensedetail where ledger_id='$ld[id]' and expense_date between '$ex_fdate' and '$ex_tdate'"));
		?>
				<tr style="cursor:pointer" onclick="load_details(<?php echo $i; ?>)">
					<td><?php echo $i; ?></td>
					<td><?php echo $ld[ledger_name]; ?> <input type="hidden" id="ledge_<?php echo $i; ?>" value="<?php echo $ld[id]; ?>" /></td>
					<td><?php echo $tot[tot]; ?></td>
				</tr>
		<?php
				$total_am += $tot[tot];
				$i++;
			}
		}
		?>

		<tr>
			<th colspan="2" style="text-align:right">Total</th>
			<th colspan="2"><?php echo $total_am; ?></th>
		</tr>
	</table>

<?php
} else if ($type == 2) {
	$val = $_POST['val'];

	$but_val = "Save";
	$det = mysqli_fetch_array(mysqli_query($link, "select * from expensedetail where slno='$val'"));
	if ($det[ledger_id]) {
		$but_val = "Update";
	}
?>
	<input type="hidden" id="exp_sl" value="<?php echo $val; ?>" />
	<table class="table table-bordered table-report">
		<tr>
			<th colspan="4">Add Expense Detail</th>
		</tr>
		<tr>
			<th colspan="3">
				<select id="mode">
					<option>Cash</option>
					<option <?php if ($det[mode] == "Cheque") {
								echo "Selected";
							}; ?>>Cheque</option>
				</select>
			</th>
			<th><input type="text" placeholder="Enter Voucher No to Search" id="serach_v" onkeyup="load_voucher(this.value,event)" /></th>
		</tr>
		<tr>
			<th>Voucher No</th>
			<th><input type="text" id="inv_no" class="imp" value="<?php echo $det[invoice_no]; ?>" /></th>

			<th>Select Ledger</th>
			<th>
				<select id="ledge" class="imp">
					<option value="0">--Select--</option>
					<?php
					$ledge = mysqli_query($link, "select * from ledger_master order by ledger_name");
					while ($ld = mysqli_fetch_array($ledge)) {
						if ($det[ledger_id] == $ld[id]) {
							$sel = "Selected='selected'";
						} else {
							$sel = '';
						}
						echo "<option value='$ld[id]' $sel>$ld[ledger_name]</option>'";
					}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<th>Cheque No</th>
			<th><input type="text" id="cheque_no" value="<?php echo $det[cheque_no]; ?>" /></th>

			<th>Bank Name</th>
			<th><input type="text" id="bank" value="<?php echo $det[bank_name]; ?>" /></th>
		</tr>
		<tr>
			<th>Expense Date</th>
			<th>
				<input class="datepicker imp" type="text" name="ex_date" id="ex_date" value="<?php if ($det[expense_date]) {
																									echo $det[expense_date];
																								} else {
																									echo date('Y-m-d');
																								} ?>" readonly />
			</th>

			<th>Amount</th>
			<th><input type="text" id="amount" class="imp" value="<?php echo $det[Amount]; ?>" /></th>
		</tr>
		<tr>
			<th>Description <br />
			<th colspan="3"><input type="text" id="desc" size="80" class="imp" value="<?php echo $det[description]; ?>" />
			</th>
		</tr>
		<tr>
			<th colspan="4" style="text-align:center">
				<input type="button" id="save" value="<?php echo $but_val; ?>" class="btn btn-primary" onclick="save_exp('<?php echo $but_val; ?>')" />
				<?php
				if ($but_val == "Update") {
				?>
					<input type="button" id="del" value="Delete" class="btn btn-primary" onclick="save_exp('Delete')" />
					<input type="button" id="print" value="Print" class="btn btn-primary" onclick="print_voucher('<?php echo $val; ?>')" />
				<?php
				}
				?>

				<input type="button" id="close" value="Close" class="btn btn-danger" onclick="$('#mod').click();$('#mod_chk').val('0')" />
			</th>
		</tr>

	</table>
	<script>
		$('.datepicker').datepicker();
	</script>
<?php
} else if ($type == 3) {
	$mode = $_POST[mode];
	$inv = $_POST[inv];
	$ledge = $_POST[ledge];
	$cheque_no = $_POST[cheque_no];
	$bank = $_POST[bank];
	$ex_date = $_POST[ex_date];
	$amount = $_POST[amount];
	$desc = $_POST[desc];
	$b_val = $_POST[b_val];
	$ex_sl = $_POST[ex_sl];
	$user = $_POST[user];

	$date = date("Y-m-d");
	$time = date('h:i:s A');

	if ($b_val == "Save") {
		if (mysqli_query($link, "INSERT INTO expensedetail(mode,invoice_no,ledger_id,cheque_no,bank_name, description, Amount, expense_date, entry_date, entry_time, user) values('$mode','$inv','$ledge','$cheque_no','$bank','$desc','$amount','$ex_date','$date','$time','$user')")) {
			$sln = mysqli_fetch_array(mysqli_query($link, "select slno from expensedetail where invoice_no='$inv'"));
			echo $sln[slno];
		}
	} else if ($b_val == "Update") {
		mysqli_query($link, "update expensedetail set mode='$mode',invoice_no='$inv',ledger_id='$ledge',cheque_no='$cheque_no',bank_name='$bank',description='$desc',Amount='$amount',expense_date='$ex_date' where slno='$ex_sl'");
	} else if ($b_val == "Delete") {
		mysqli_query($link, "delete from expensedetail where slno='$ex_sl'");
	}
} else if ($type == 4) {
	$ledge = $_POST[ledge];
	$ex_fdate = $_POST[ex_fdate];
	$ex_tdate = $_POST[ex_tdate];

	/*-----------Date Array--------------*/
	$array = array();
	$interval = new DateInterval('P1D');

	$realEnd = new DateTime($ex_tdate);
	$realEnd->add($interval);

	$period = new DatePeriod(new DateTime($ex_fdate), $interval, $realEnd);

	foreach ($period as $date) {
		$dd[] = $date->format('Y-m-d');
	}
	/*-----------Date Array--------------*/

	$lname = mysqli_fetch_array(mysqli_query($link, "select * from ledger_master where id='$ledge'"));
?>
	<input type="hidden" id="ex_from" value="<?php echo $ex_fdate; ?>" />
	<input type="hidden" id="ex_to" value="<?php echo $ex_tdate; ?>" />
	<div class="">
		<div class="col-md-12">
			<div class="col-md-6">
				<b>Ledger: <?php echo $lname[ledger_name]; ?></b>
			</div>
			<div class="col-md-6 text-right">
				<input type="button" class="btn btn-success" value="Back to List" onclick="load_pinfo_date($('#main_ser_typ').val())" />
			</div>
		</div>
	</div>
	<table class="table table-report table-bordered table-stripped">
		<th>#</th>
		<th>Voucher No</th>
		<th>Mode</th>
		<th>Description</th>
		<th>Amount</th>
		<th>Cheque No</th>
		<th>Bank</th>
		<th>Expense Date</th>
		<th>Entry Date</th>

		<input type="hidden" id="upd_ledger_id" value="<?php echo $ledge; ?>" />


		<?php
		$total_val = 0;
		foreach ($dd as $d) {

			$i = 1;
			$qry = mysqli_query($link, "select * from expensedetail where ledger_id='$ledge' and expense_date='$d'");
			if (mysqli_num_rows($qry) > 0) {
				$tot_ex = mysqli_fetch_array(mysqli_query($link, ""));
				echo "<tr><th colspan='9'>Date:" . convert_date($d) . "</th></tr>";
				while ($q = mysqli_fetch_array($qry)) {

		?>
					<tr style="cursor:pointer" onclick="load_ledge_details(<?php echo $q[slno]; ?>)">
						<td><?php echo $i; ?></td>
						<td><?php echo $q[invoice_no]; ?></td>
						<td><?php echo $q[mode]; ?></td>
						<td width="40%"><?php echo $q[description]; ?></td>
						<td><?php echo $q[Amount]; ?></td>
						<td><?php echo $q[cheque_no]; ?></td>
						<td><?php echo $q[bank_name]; ?></td>
						<td><?php echo $q[expense_date]; ?></td>
						<td><?php echo $q[entry_date]; ?></td>
					</tr>
		<?php
					$total_val += $q[Amount];
					$i++;
				}
			}
		}
		?>
		<tr>
			<th colspan="4" style="text-align:right">Total</th>
			<th colspan="5"><?php echo $total_val; ?></th>
		</tr>
	</table>
<?php
} else if ($type == 5) {
	$val = $_POST[val];

	$sln = mysqli_fetch_array(mysqli_query($link, "select * from expensedetail where invoice_no='$val'"));

	echo $sln[slno];
}
?>