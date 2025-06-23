<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
$c_user = $_SESSION["emp_id"];
$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

if ($type == 'load_data') {
    $sel_date = $_POST['date'];
    $vac_qry = "SELECT DISTINCT `vaccu` FROM `phlebo_sample` WHERE `date` = '$sel_date'";
    $vacc = mysqli_query($link, $vac_qry);

    $vaccine_types = [];
    $vaccine_ids = [];

    while ($v_name = mysqli_fetch_array($vacc)) {
        $vaccu_id = $v_name['vaccu'];
        $vaccine_ids[] = $vaccu_id;

        $vaccu_result = mysqli_query($link, "SELECT `type` FROM `vaccu_master` WHERE `id` = '$vaccu_id'");
        $vaccu = mysqli_fetch_array($vaccu_result);
        $vaccine_types[$vaccu_id] = $vaccu['type'];
    }

    $count_data = [];
    $count_qry = "
    SELECT vaccu, HOUR(time) AS hour_slot, COUNT(*) AS v_ctr
    FROM phlebo_sample
    WHERE date = '$sel_date'
    GROUP BY vaccu, hour_slot
";
    $result = mysqli_query($link, $count_qry);

    while ($row = mysqli_fetch_assoc($result)) {
        $count_data[$row['hour_slot']][$row['vaccu']] = $row['v_ctr'];
    }
    ?>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div><strong>Report Generated On : </strong> <?php echo date('d-M-y') . " / " . date('h:i A'); ?></div>
        <div id="no_print" style="margin-left: auto;">
            <button class="btn btn-mini btn-primary" onclick="print_table('<?php echo $sel_date; ?>')">
                <i class="icon-print icon-large"></i> Print
            </button>
            <button class="btn btn-mini btn-success" onclick="exportTableToExcel('<?php echo $sel_date; ?>')">
                <i class="icon-file icon-large"></i> Excel
            </button>
        </div>
    </div>

    <table id="reportTable" class="table table-condensed table-hover">
        <thead>
            <tr>
                <th>Hour Slot</th>
                <?php foreach ($vaccine_ids as $vaccu_id): ?>
                    <th><?= htmlspecialchars($vaccine_types[$vaccu_id]) ?></th>
                <?php endforeach; ?>
                <th><strong>Total</strong></th> <!-- Row total -->
            </tr>
        </thead>
        <tbody>
            <?php
            $column_totals = array_fill_keys($vaccine_ids, 0);
            $grand_total = 0;

            for ($hour = 0; $hour < 24; $hour++):
                $start = str_pad($hour, 2, '0', STR_PAD_LEFT) . ":00";
                $end = str_pad($hour, 2, '0', STR_PAD_LEFT) . ":59";
                $row_total = 0;
                ?>
                <tr>
                    <td><strong><?= "$start - $end" ?></strong></td>
                    <?php foreach ($vaccine_ids as $vaccu_id): ?>
                        <?php
                        $count = $count_data[$hour][$vaccu_id] ?? 0;
                        $row_total += $count;
                        $column_totals[$vaccu_id] += $count;
                        $grand_total += $count;
                        $cell_class = $count > 0 ? 'style="background-color:#dff0d8;"' : '';
                        ?>
                        <td <?= $cell_class ?>><?= $count ?></td>
                    <?php endforeach; ?>
                    <td><strong><?= $row_total ?></strong></td>
                </tr>
            <?php endfor; ?>
        </tbody>
        <tfoot>
            <tr>
                <th style="text-align: right;"><strong>Total: </strong></th>
                <?php foreach ($vaccine_ids as $vaccu_id): ?>
                    <th><strong><?= $column_totals[$vaccu_id] ?></strong></th>
                <?php endforeach; ?>
                <th><strong><?= $grand_total ?></strong></th>
            </tr>
        </tfoot>
    </table>

    <?php
}