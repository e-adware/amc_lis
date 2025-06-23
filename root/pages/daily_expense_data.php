<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date = date("Y-m-d");
$time = date("H:i:s");

$user = $_POST['user'];
$type = $_POST['type'];

$rupees_symbol = "&#x20b9; ";

$branch = mysqli_fetch_array(mysqli_query($link, "SELECT `branch_id` FROM `employee` WHERE `emp_id` = '$user'"));




if ($type == 'new_expense') {
    $but_val = "Save"; ?>
<table class="table table-bordered table-report">
    <tr>
        <th colspan="4">Add Expense Detail</th>
    </tr>
    <tr>
        <th colspan="4">
            <select id="mode">
                <option value="Cash">Cash</option>
                <option value="Cheque">Cheque</option>
            </select>
        </th>
        <!-- <th><input type="text" placeholder="Enter Voucher No to Search" id="serach_v"
                onkeyup="load_voucher(this.value,event)" /></th> -->
    </tr>
    <tr>
        <th>Voucher No <span class="required">*</span></th>
        <th><input type="text" id="inv_no" class="imp" /></th>

        <th>Select Ledger <span class="required">*</span></th>
        <th>
            <select id="ledge" class="imp">
                <option value="0">--Select--</option>
                <?php
                    $ledge = mysqli_query($link, "select * from ledger_master order by ledger_name");
                    while ($ld = mysqli_fetch_array($ledge)) {

                        echo "<option value='$ld[id]'>$ld[ledger_name]</option>'";
                    }
                    ?>
            </select>
        </th>
    </tr>
    <tr>
        <th>Cheque No</th>
        <th><input type="text" id="cheque_no" /></th>

        <th>Bank Name</th>
        <th><input type="text" id="bank" /></th>
    </tr>
    <tr>
        <th>Expense Date <span class="required">*</span></th>
        <th>

            <input class="form-control datepicker" type="text" name="ex_date" id="ex_date"
                value="<?php echo date('Y-m-d'); ?>" readonly />
        </th>

        <th>Amount <span class="required">*</span></th>
        <th><input type="text" id="amount" class="imp" /></th>
    </tr>
    <tr>
        <th>Description <span class="required">*</span><br />
        <th colspan="3"><input type="text" id="desc" size="80" class="imp" />
        </th>
        <th style="display: none">Branch <br />
        <th style="display: none"><input type="text" id="branch" size="80" class="imp"
                value="<?php echo $branch['branch_id']; ?>" />
        </th>
    </tr>
    <tr>
        <th colspan="4" style="text-align:center">
            <input type="button" id="save" value="<?php echo $but_val; ?>" class="btn btn-primary"
                onclick="save_exp()" />
            <input type="button" id="close" value="Close" class="btn btn-danger" onclick="expense_list()" />
        </th>
    </tr>

</table>
<script>
$(".datepicker").datepicker({
    dateFormat: 'yy-mm-dd',
    maxDate: '0',
});
</script>
<?php
}
if ($type == 'expense_list') {
    $ex_fdate = $_POST['ex_fdate'];
    $ex_tdate = $_POST['ex_tdate'];
    $expense_id = $_POST['expense_id'];
    $exp_search = $_POST['exp_search'];
    $branch_id = $_POST['branch_id'];

    if ($expense_id == '0') {
        $exp_id_qry = "";
    } else {
        $exp_id_qry = "WHERE `id` = '$expense_id'";
    }
?>
<input type="hidden" id="ex_from" value="<?php echo $ex_fdate; ?>" / <input type="hidden" id="ex_to"
    value="<?php echo $ex_tdate; ?>" />
Expense Date From <b><i><?php echo $ex_fdate; ?> to <?php echo $ex_tdate; ?></i></b>
<button
    onclick="daily_report_print('<?php echo base64_encode($ex_fdate); ?>','<?php echo base64_encode($ex_tdate); ?>')"
    class="btn btn-success" style="float: right"><i class="icon-print"></i>Print</button>
<table class="table table-report table-bordered table-stripped">
    <tr>
        <th>#</th>
        <th>Ledger Name</th>
        <th>Total</th>
    </tr>
    <?php
        $i = 1;
        $total_am = 0;
        $ledg_qry = "SELECT * from `ledger_master` $exp_id_qry order by ledger_name";
        $ledg = mysqli_query($link, $ledg_qry);

        // echo $ledg_qry;
        while ($ld = mysqli_fetch_array($ledg)) {


            $qry = mysqli_query($link, "select * from expensedetail where ledger_id='$ld[id]' and expense_date between '$ex_fdate' and '$ex_tdate' AND `branch` = '$branch_id'");

            $id = mysqli_fetch_array($qry);

            if (mysqli_num_rows($qry) > 0) {
                $tot = mysqli_fetch_array(mysqli_query($link, "select sum(Amount) as tot from expensedetail where ledger_id='$ld[id]' and expense_date between '$ex_fdate' and '$ex_tdate'"));
        ?>
    <tr style="cursor:pointer" onclick="load_details(<?php echo $ld['id']; ?>)">
        <td><?php echo $i; ?></td>
        <td><?php echo $ld['ledger_name']; ?> <input type="hidden" id="ledge_<?php echo $i; ?>"
                value="<?php echo $ld['id']; ?>" /></td>
        <td><?php echo $rupees_symbol . $tot['tot']; ?></td>
    </tr>
    <?php
                $total_am += $tot['tot'];
                $i++;
            }
        }
        ?>

    <tr>
        <th colspan="2" style="text-align:right">Total</th>
        <th colspan="2"><?php echo $rupees_symbol . number_format($total_am, 2); ?></th>
    </tr>
</table>
<?php

}
if ($type == 'expense_detail') {

    $ledge = $_POST['ledge'];
    $ex_fdate = $_POST['ex_fdate'];
    $ex_tdate = $_POST['ex_tdate'];

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
            <b>Ledger: <?php echo $lname['ledger_name']; ?></b>
        </div>
        <div class="col-md-6 text-right">
            <input type="button" class="btn btn-success" value="Back to List" onclick="expense_list()" />
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
    <tr style="cursor:pointer" onclick="load_ledge_details(<?php echo $q['slno']; ?>)">
        <td><?php echo $i; ?></td>
        <td><?php echo $q['invoice_no']; ?></td>
        <td><?php echo $q['mode']; ?></td>
        <td width="40%"><?php echo $q['description']; ?></td>
        <td><?php echo $rupees_symbol . $q['Amount']; ?></td>
        <td><?php echo $q['cheque_no']; ?></td>
        <td><?php echo $q['bank_name']; ?></td>
        <td><?php echo $q['expense_date']; ?></td>
        <td><?php echo $q['entry_date']; ?></td>
    </tr>
    <?php
                    $total_val += $q['Amount'];
                    $i++;
                }
            }
        }
        ?>
    <tr>
        <th colspan="4" style="text-align:right">Total</th>
        <th colspan="5"><?php echo $rupees_symbol . number_format($total_val, 2); ?></th>
    </tr>
</table>
<?php
}

if ($type == "save_exp") {
    $mode = $_POST['mode'];
    $inv = $_POST['inv'];
    $ledge = $_POST['ledge'];
    $cheque_no = $_POST['cheque_no'];
    $bank = $_POST['bank'];
    $ex_date = $_POST['ex_date'];
    $amount = $_POST['amount'];
    $desc = $_POST['desc'];
    $time = date('h:i:s A');
    $branch = $_POST['branch'];

    if (mysqli_query($link, "INSERT INTO expensedetail(branch, mode,invoice_no,ledger_id,cheque_no,bank_name, description, Amount, expense_date, entry_date, entry_time, user) values('$branch','$mode','$inv','$ledge','$cheque_no','$bank','$desc','$amount','$ex_date','$date','$time','$user')")) {
        echo "success";
    }
}

if ($type == "ledger_edit") {

    $ledger_det = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `expensedetail` WHERE `slno` = '$_POST[exp_id]'"));
    $dis = "";
    $del_btn = "";
    if ($ledger_det['expense_date'] == $date) {
        $dis = " ";
        $del_btn = " ";
        $readonly = " ";
    } else {
        $readonly = "readonly";
        $dis = "disabled ";
        $del_btn = "style='display: none' ";
    }
?>

<table class="table table-bordered table-report">
    <tr>
        <th colspan="4">Edit Expense Detail</th>
    </tr>
    <tr>
        <th colspan="4">
            <select id="mode">
                <option value="Cash">Cash</option>
                <option <?php if ($ledger_det['mode'] == "Cheque") {
                                echo "Selected";
                            }; ?> value="Cheque">Cheque</option>
            </select>
        </th>
        <!-- <th><input type="text" placeholder="Enter Voucher No to Search" id="serach_v"
                onkeyup="load_voucher(this.value,event)" /></th> -->
    </tr>
    <tr>
        <th>Voucher No *</th>
        <th><input type="text" id="inv_no" class="imp" value="<?php echo $ledger_det['invoice_no']; ?>" /></th>

        <th>Select Ledger *</th>
        <th>
            <select id="ledge" class="imp">
                <option value="0">--Select--</option>
                <?php
                    $ledge = mysqli_query($link, "select * from ledger_master order by ledger_name");
                    while ($ld = mysqli_fetch_array($ledge)) {
                        if ($ledger_det['ledger_id'] == $ld['id']) {
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
        <th><input type="text" id="cheque_no" value="<?php echo $ledger_det['cheque_no']; ?>" /></th>

        <th>Bank Name</th>
        <th><input type="text" id="bank" value="<?php echo $ledger_det['bank_name']; ?>" /></th>
    </tr>
    <tr>
        <th>Expense Date *</th>
        <th>

            <input class="form-control datepicker" type="text" name="ex_date" id="ex_date"
                value="<?php echo $ledger_det['expense_date']; ?>" <?php echo $dis; ?>readonly />
        </th>
        <th>Amount *</th>
        <th><input <?php echo $readonly; ?> type="text" id="amount" class="imp"
                value="<?php echo $ledger_det['Amount']; ?>" /></th>
    </tr>
    <tr>
        <th>Description <br />
        <th colspan="3"><input type="text" value="<?php echo $ledger_det['description']; ?>" id="desc" size="80"
                class="imp" />
        </th>
    </tr>
    <tr>
        <th colspan="4" style="text-align:center">
            <input type="button" id="save" value="<?php echo "Update"; ?>" class="btn btn-warning"
                onclick="update_exp('<?php echo $ledger_det['slno']; ?>')" />
            <input type="button" <?php echo $del_btn; ?> id="save" value="<?php echo "Delete"; ?>"
                class="btn btn-danger" onclick="delete_exp('<?php echo $ledger_det['slno']; ?>')" />
            <input type="button" id="close" value="Close" class="btn btn-primary"
                onclick="load_details(<?php echo $ledger_det['ledger_id'] ?>)" />
        </th>
    </tr>

</table>
<script>
$(".datepicker").datepicker({
    dateFormat: 'yy-mm-dd',
    maxDate: '0',
});
</script>
<?php
}
if ($type == "update_exp") {
    $mode = $_POST['mode'];
    $inv = $_POST['inv'];
    $ledge = $_POST['ledge'];
    $cheque_no = $_POST['cheque_no'];
    $bank = $_POST['bank'];
    $ex_date = $_POST['ex_date'];
    $amount = $_POST['amount'];
    $desc = $_POST['desc'];
    $user = $_POST['user'];
    $time = date('h:i:s A');

    if (mysqli_query($link, "UPDATE `expensedetail` SET `mode`= '$mode',`invoice_no`= '$inv',`ledger_id`='$ledge',`cheque_no`='$cheque_no',`bank_name`='$bank',`description`='$desc',`Amount`='$amount',`expense_date`='$ex_date' WHERE `slno` = '$_POST[exp_id]'")) {
        echo "success";
    }
}
if ($type == "delete_exp") {
    $result = array();
    if (mysqli_query($link, "DELETE FROM `expensedetail` WHERE `slno` = '$_POST[exp_id]'")) {
        $result["msg"] = "Expense deleted successfully";
        $result["response"] = 1;
    } else {
        $result["msg"] = "Expense deleted failed";
        $result["response"] = 0;
    }

    echo json_encode($result);
}

if ($type == 'print_exp') {
    $fdate = $_POST['fdate'];
    $tdate = $_POST['tdate'];
    $expense_id = $_POST['expense_id'];
    if ($expense_id == 0) {
        $exp_qry = "";
    } else {
        $exp_qry = "AND a.`ledger_id` = '$expense_id'";
    }
    $tot = 0;
?>
<table class="table table-condensed">
    <tr>
        <th>#</th>
        <th>Voucher No</th>
        <th>Mode</th>
        <th>Description</th>
        <th>Amount</th>
        <th>Cheque No</th>
        <th>Bank</th>
    </tr>
    <?php
        $q = mysqli_query($link, "SELECT DISTINCT a.`ledger_id`, b.`ledger_name` FROM `expensedetail` a, `ledger_master` b WHERE a.`ledger_id`=b.`id` AND a.`expense_date` BETWEEN '$fdate' AND '$tdate' $exp_qry");
        while ($r = mysqli_fetch_assoc($q)) {
        ?>
    <tr>
        <th colspan="7"><?php echo $r['ledger_name']; ?></th>
    </tr>
    <?php
            $qq = mysqli_query($link, "SELECT DISTINCT `expense_date` FROM `expensedetail` WHERE `ledger_id`='$r[ledger_id]' AND `expense_date` BETWEEN '$fdate' AND '$tdate'");
            while ($rr = mysqli_fetch_assoc($qq)) {
            ?>
    <tr>
        <td></td>
        <th colspan="6"><?php echo $rr['expense_date']; ?></th>
    </tr>
    <?php
                $j = 1;
                $qqq = mysqli_query($link, "SELECT * FROM `expensedetail` WHERE `ledger_id`='$r[ledger_id]' AND `expense_date`='$rr[expense_date]'");
                while ($rrr = mysqli_fetch_assoc($qqq)) {
                ?>
    <tr>
        <td><?php echo $j; ?></td>
        <td><?php echo $rrr['invoice_no']; ?></td>
        <td><?php echo $rrr['mode']; ?></td>
        <td><?php echo $rrr['description']; ?></td>
        <td><?php echo $rrr['Amount']; ?></td>
        <td><?php echo $rrr['cheque_no']; ?></td>
        <td><?php echo $rrr['bank_name']; ?></td>
    </tr>
    <?php
                    $j++;
                    $tot += $rrr['Amount'];
                }
            }
        }
        ?>
    <tr>
        <th colspan="3"></th>
        <th>Total</th>
        <th><?php echo number_format($tot, 2); ?></th>
        <th></th>
        <th></th>
    </tr>
</table>
<?php
}