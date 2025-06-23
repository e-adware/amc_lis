<?php
include("../../includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");

if ($_POST["type"] == "load_all_test") {

    $sam = $_POST['ser_samp'];

    $limit_no = $_POST['limit_no'];
    $search_data = $_POST['search_data'];

    $branch_id = $_POST["branch_id"];
    $equipment = $_POST["equipment"];

    $user = $_POST["user"];

    $qry = " SELECT * FROM `testmaster` WHERE `testid`>0 ";

    if ($search_data) {
        $qry .= " AND `testname` LIKE '%$search_data%'";
    }

    if ($sam) {
        $qry .= " and testid in(select testid from testmaster where testid='$sam')";
    }

    if ($equipment > 0) {
        $qry .= " and equipment='$equipment'";
    }

    $qry .= " order by `testid` limit $limit_no";

    ?>
    <table class="table table-bordered data-table">
        <thead style="background: #ddd;">
            <tr>
                <th>ID</th>
                <th>Service Name</th>
                <th>Department</th>
                <th>Minimum Time</th>
                <th>Maximum Time</th>
                <th colspan="3"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $tst_qry = mysqli_query($link, $qry);
            while ($tst = mysqli_fetch_array($tst_qry)) {
                $cls = "";
                $txt_p = "Map Parameter";
                $par = mysqli_num_rows(mysqli_query($link, "select * from Testparameter where TestId='$tst[testid]'"));
                if ($par > 0) {
                    $txt_p = "Edit Parameter";
                    $cls = "btn btn-info btn-mini";
                } else {
                    $cls = "btn btn-default btn-mini";
                }
                $sub_bt = "Add-On";
                $sub_cls = "btn btn-default btn-mini";
                $chk_sub = mysqli_num_rows(mysqli_query($link, "select * from testmaster_sub where testid='$tst[testid]'"));
                if ($chk_sub > 0) {
                    $sub_cls = "btn btn-info btn-mini";
                }
                if ($tst['category_id'] == 1) {
                    $map_btn = "";
                } else {
                    $map_btn = "disabled='disabled'";
                }

                $test_rate = $tst['rate'];
                if ($branch_id == 1) {
                    $test_rate = $tst['rate'];
                }
                if ($branch_id == 2) {
                    $test_rate = $tst['rate_bc'];
                }

                $tat_query = "SELECT `minimum_time`, `maximum_time` FROM `tat_master` WHERE `test_id` = '{$tst['testid']}'";
                $tat_result = mysqli_query($link, $tat_query);
                $tat_data = mysqli_fetch_array($tat_result);
                $minimum_time = $tat_data['minimum_time'] ?? '';
                $maximum_time = $tat_data['maximum_time'] ?? '';
                ?>
                <tr class="gradeX" id="test<?php echo $i ?>" class="<?php echo $t['testname']; ?>">
                    <td id="test_id<?php echo $i ?>"><?php echo $tst["testid"]; ?></td>
                    <td>
                        <?php echo $tst["testname"]; ?>
                    </td>
                    <td><?php echo $tst['type_name']; ?></td>
                    <td><?php echo $minimum_time; ?></td>
                    <td><?php echo $maximum_time; ?></td>

                    <td colspan="4">
                        <div class="btn-group">
                            <?php

                            if ($tst['testid'] != 3) {
                                ?>
                                <button id='upd' class='btn btn-primary btn-mini'
                                    onclick="load_test_info('<?php echo $tst['testid']; ?>')"><i class="icon-edit"></i>
                                    Update</button>
                                <?php
                            }

                            ?>
                        </div>
                    </td>

                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
}

if ($_POST["type"] == "load_departments") {
    $category_id = mysqli_real_escape_string($link, $_POST['category_id']);

    $str = "SELECT `id`, `category_id`, `name` FROM `test_department` WHERE `id`>0";

    if ($category_id > 0) {
        $str .= " AND `category_id`='$category_id'";
    }

    $str .= " ORDER BY `name` ASC";

    echo '<option value="0">--Select Department--</option>';

    $qry = mysqli_query($link, $str);
    while ($data = mysqli_fetch_array($qry)) {
        echo "<option value='$data[id]'>$data[name]</option>";
    }

}
if ($_POST["type"] == "test_rate_change") {
    $val = mysqli_real_escape_string($link, $_POST['val']);
    $id = mysqli_real_escape_string($link, $_POST['id']);
    $branch_id = mysqli_real_escape_string($link, $_POST['branch_id']);

    if ($branch_id == 1) {
        mysqli_query($link, " UPDATE `testmaster` SET `rate`='$val' WHERE `testid`='$id' ");
    }
    if ($branch_id == 2) {
        mysqli_query($link, " UPDATE `testmaster` SET `rate_bc`='$val' WHERE `testid`='$id' ");
    }

    $val = mysqli_fetch_array(mysqli_query($link, " SELECT `rate` FROM `testmaster` WHERE `testid`='$id' "));

    echo $val['rate'];

}
if ($_POST["type"] == "test_name_change") {
    $val = mysqli_real_escape_string($link, $_POST['val']);
    $id = mysqli_real_escape_string($link, $_POST['id']);

    mysqli_query($link, " UPDATE `testmaster` SET `testname`='$test_name' WHERE `testid`='$id' ");

    $val = mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$id' "));

    echo $val['testname'];

}




// ------------------------------------------------------

if ($_POST["typ"] == "save") {
    $testid = mysqli_real_escape_string($link, $_POST["testid"]);
    $testname = mysqli_real_escape_string($link, $_POST["testname"]);

    $type_id = mysqli_real_escape_string($link, $_POST["type_id"]);
    $minimum_time = mysqli_real_escape_string($link, $_POST["min_time"]);
    $maximum_time = mysqli_real_escape_string($link, $_POST["max_time"]);
    $user = mysqli_real_escape_string($link, $_POST["user"]);


    if (mysqli_query($link, "UPDATE `tat_master` SET `minimum_time`='$minimum_time', `maximum_time`='$maximum_time' WHERE `test_id`='$testid'")) {
        echo "Updated";
    } else {
        echo "Failed, try again later";
    }
}

?>