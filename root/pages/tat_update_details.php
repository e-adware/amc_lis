<?php
include("../../includes/connection.php");

$testid = $_POST["testid"];
$test_info = mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$testid'"));

$rprt_del = explode("@", $test_info["report_delivery"]);
$rprt_del_day = $rprt_del[0];
$rprt_del = explode("#", $rprt_del[1]);
$rprt_del_hour = $rprt_del[0];
$rprt_del_minute = $rprt_del[1];

if ($testid) {
    if ($test_info['category_id'] == 1) {
        $tr_style = "";
    } else {
        $tr_style = "display:none;";
    }
} else {
    $tr_style = "display:none;";

    $testid = 0;
}
?>
<div id="test_detail" style="padding:10px">
    <h4>Test Details</h4>
    <table class="table table-bordered table-condensed">
        <tr>
            <th>Test Name</th>
            <td><input type="text" id="testname" class="test_value span6"
                    value="<?php echo $test_info["testname"]; ?>" /></td>

            <th>Department</th>
            <td>
                <select id="type_id" class="test_value">
                    <option value="0">Select</option>
                    <?php
                    if ($testid) {
                        $dep = mysqli_query($link, "select id,name from test_department where `category_id`='$test_info[category_id]' order by name");
                        while ($dept = mysqli_fetch_array($dep)) {
                            if ($dept["id"] == $test_info["type_id"]) {
                                $sel1 = "Selected='selected'";
                            } else {
                                $sel1 = "";
                            }
                            echo "<option value='$dept[id]' $sel1>$dept[name]</option>";
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>Minimum Time</th>
            <td>
                <input type="time" id="minimum_time" class="test_value span6" value="<?php
                $tat_query = mysqli_query($link, "SELECT `minimum_time` FROM `tat_master` WHERE `test_id`='$testid'");
                if ($tat_row = mysqli_fetch_array($tat_query)) {
                    echo $tat_row["minimum_time"];
                }
                ?>" />
            </td>
            <th>Maximum Time</th>
            <td>
                <input type="time" id="maximum_time" class="test_value span6" value="<?php
                $tat_query = mysqli_query($link, "SELECT `maximum_time` FROM `tat_master` WHERE `test_id`='$testid'");
                if ($tat_row = mysqli_fetch_array($tat_query)) {
                    echo $tat_row["maximum_time"];
                }
                ?>" />
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:center">
                <input type="button" id="update" value="Update" class="btn btn-default"
                    onclick="save_test('<?php echo $testid; ?>')" />
                <input type="button" id="clse" value="Close" class="btn btn-danger" onclick="$('#mod').click()" />
            </td>
        </tr>
    </table>
    <style>
        label {
            display: inline-block;
        }
    </style>
</div>