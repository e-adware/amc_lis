<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");

include "../app/init.php";
try {
    $db = new Db_LoaderMS();
} catch (PDOException $e) {
    echo "<h4>Connection failed:<br/>" . $e->getMessage() . "</h4>";
    exit;
}

$type = $_POST['type'];

if ($type == 'save_qc_name') {
    $qc_id = $_POST['qc_id'];
    $qc_name = mysqli_real_escape_string($link, $_POST['qc_name']);
    $qc_sample_name = mysqli_real_escape_string($link, $_POST['qc_sample_name']);
    $instrument_id = $_POST['instrument'];
    $fluid = $_POST['fluid'];
    if ($qc_id == '0') {
        if (mysqli_query($link, "INSERT INTO `qc_master`(`instrument_id`, `fluid_id`, `qc_name`, `sample_id`) VALUES('$instrument_id', '$fluid', '$qc_name', '$qc_sample_name')"))
            echo "1";
        else {
            echo "0";
        }
    } else {
        if (mysqli_query($link, "UPDATE `qc_master` SET `qc_name` = '$qc_name', `sample_id` = '$qc_sample_name' WHERE `qc_id` = '$qc_id'"))
            echo '1';
        else
            echo '0';
    }
}

if ($type == 'load_list') {
    $qry = mysqli_query($link, "SELECT * FROM `qc_master` ORDER BY `qc_name`");
    ?>
    <table class="table table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th width="40%">Name</th>
                <th>Sample ID</th>
                <th>Indices</th>
            </tr>
        </thead>
        <?php $n = 1;
        while ($r = mysqli_fetch_array($qry)) { ?>
            <tr style="cursor: pointer">
                <th><?php echo $n++; ?></th>
                <td onclick="edit_qc_name('<?php echo $r['qc_id']; ?>')"><?php echo $r['qc_name']; ?></td>
                <td onclick="edit_qc_name('<?php echo $r['qc_id']; ?>')"><?php echo $r['sample_id']; ?></td>
                <td style="text-align: center"><button onclick="qc_param_edit('<?php echo $r['qc_id']; ?>')"
                        class="btn btn-mini btn-success"><i class="icon-edit"></i> Edit</button>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php
}

if ($type == 'edit_qc_name') {
    $res = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_master` WHERE `qc_id` = '$_POST[id]'"));
    echo $res['qc_id'] . '@@' . $res['qc_name'] . '@@' . $res['sample_id'] . "@@" . $res['instrument_id'] . "@@" . $res['fluid_id'];
}

if ($type == 'edit_qc_param') {
    $qc_name = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_master` WHERE `qc_id` = '$_POST[id]'"));
    ?>
    <div>
        <p style="display: inline;">Edit Indices For:
            <strong><?php echo $qc_name['qc_name'] . " (" . $qc_name['sample_id'] . ")"; ?></strong>
        </p>
        <input type="text" id="reagent_search" onkeyup="filter_reagent_list()" placeholder="Search Indices"
            class="search form-control" />
        <div style="float:right;">
            <button style=" display: inline;" class="btn btn-mini btn-primary" onclick="load_qc_home()"><i
                    class="icon-reply"></i>
                Back To
                List </button>
            <button style="display: inline;" class="btn btn-mini btn-danger"
                onclick="reload_test_master('<?= $qc_name['qc_id'] ?>')"><i class="icon-repeat"></i>
                Reload Test Master</button>
        </div>

    </div>
    <div id="reagent_list"></div>
    <div style="text-align: center">
        <button class="btn btn-success" onclick="save_qc_reagent('<?php echo $qc_name['qc_id']; ?>')"><i
                class="icon-save"></i>
            Save Indices Mapping</button>
    </div>

    <?php
}



if ($type == 'load_reagent_list') {

    $qc_id = $_POST['qc_id'];
    $str = "";
    if ($search_reagent = $_POST['src_text']) {
        $str = " WHERE `testname` LIKE '$search_reagent%'";
    }

    ?>
    <input type="hidden" value="<?php echo htmlspecialchars($qc_id); ?>" />

    <div style="max-height: 500px; overflow-y: scroll; margin-top: 20px;">
        <table class="table table-bordered">
            <thead style="position: sticky; top: 0; background: #eaeaea;">
                <tr>
                    <th>ID</th>
                    <th>Select Indices</th>
                    <th>Unit</th>
                    <th>
                        <input type="checkbox" id="selectAll" /> <label for="selectAll" style="display: inline;">
                            <strong>Select
                                All</strong></label>
                    </th>
                </tr>
            </thead>
            <tbody id="myTable">
                <?php
                $reagent_qry = mysqli_query($link, "SELECT * FROM `qc_testmaster` $str ORDER BY `testname`");

                while ($reagent = mysqli_fetch_assoc($reagent_qry)) {
                    $test_id = $reagent['test_id'];
                    $testname = htmlspecialchars($reagent['testname']);
                    $unit = htmlspecialchars($reagent['unit']);

                    $check_qry = mysqli_query($link, "SELECT 1 FROM `qc_mapping` WHERE `qc_id` = '$qc_id' AND `test_id` = '$test_id' LIMIT 1");
                    $checked = mysqli_num_rows($check_qry) > 0 ? 'checked' : '';
                    ?>
                    <tr>
                        <td><?php echo $test_id; ?></td>
                        <td><?php echo $testname; ?></td>
                        <td><?php echo $unit; ?></td>
                        <td>
                            <input type="checkbox" class="reagent-checkbox" name="reagents_check"
                                value="<?php echo $test_id; ?>" <?php echo $checked; ?> />
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        // Select/Deselect all checkboxes
        document.getElementById('selectAll').addEventListener('change', function () {
            const checked = this.checked;
            document.querySelectorAll('.reagent-checkbox').forEach(cb => cb.checked = checked);
        });
    </script>




    <?php
}

if ($type == "reload_test_master") {
    $qc_id = $_POST['qc_id'];
    mysqli_query($link, "TRUNCATE TABLE `qc_testmaster`");
    mysqli_query($link, "DELETE FROM `qc_mapping` WHERE `qc_id` = '$qc_id'");
    mysqli_query($link, "DELETE FROM `qc_results` WHERE `qc_id` = '$qc_id'");

    // <!----- Pull TestMaster For Debug -----!>
    $testmaster_host = $db->setQuery("SELECT DISTINCT(equip_test) AS test_id, test_name AS testname, unit FROM tpl_patient_orders WHERE result_type='CONTROL';")->fetch_all();

    foreach ($testmaster_host as $res) {
        mysqli_query($link, "INSERT INTO `qc_testmaster`(`test_id`, `testname`, `unit`) VALUES ('$res[test_id]','$res[testname]','$res[unit]')");
    }
}

if ($type == 'save_qc_mapping') {
    $qc_id = $_POST['qcid'];
    $testids = $_POST['testids'];

    if (mysqli_query($link, "DELETE FROM `qc_mapping` WHERE `qc_id` = '$qc_id'")) {
        foreach ($testids as $testid) {
            mysqli_query($link, "INSERT INTO `qc_mapping`(`qc_id`, `test_id`) VALUES ('$qc_id','$testid')");
        }
        echo "Mapping Success";
    } else
        echo "Error Occured";
}