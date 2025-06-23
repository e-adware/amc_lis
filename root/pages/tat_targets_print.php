<?php
include('../../includes/connection.php');

// Date format convert
function convert_date($date)
{
    if ($date) {
        $timestamp = strtotime($date);
        $new_date = date('d M Y', $timestamp);
        return $new_date;
    }
}
// Time format convert
function convert_time($time)
{
    $time = date("g:i A", strtotime($time));
    return $time;
}
$rupees_symbol = "&#x20b9; ";

$date1 = $_GET['date1'];
$date2 = $_GET['date2'];
$branch_id = $_GET['branch_id'];
$val = $_GET['val'];

$dept_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `test_category_master` WHERE `category_id`='$val' "));
if ($dept_info) {
    $dept = $dept_info["name"];
}
?>
<html>

<head>
    <title>Category Wise Test Report of <?php echo $dept; ?></title>
    <link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../../css/custom.css" type="text/css" rel="stylesheet" />
    <link href="../../css/loader.css" type="text/css" rel="stylesheet" />
    <script src="../../js/jquery.min.js"></script>
</head>

<body onkeyup="close_window(event)">
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <?php include('page_header.php'); ?>
            </div>
        </div>
        <hr>
        <center>
            <h4>TAT Targets (Defined by Laboratory)</h4>

            <br>

            <div class="noprint ">
                <input type="button" class="btn btn-info" id="Name1" value="Print" onclick="javascript:window.print()">
                <input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
            </div>
            <table class="table table-bordered table-condensed table-report">
                <tr>
                    <th>Slno.</th>
                    <th>Test Name</th>
                    <th>Routine TAT Target (DD:HH:MM)</th>
                    <th>Emergency TAT Target (DD:HH:MM)</th>
                </tr>
                <?php
                include('../../includes/connection.php');
                $n = 1;
                $tat_details = mysqli_query($link, "SELECT `testname`,`turn_around_time_routine_str`,`turn_around_time_urgent_str` FROM `testmaster` WHERE `testname`!='' ORDER BY `testname`");
                while ($tat = mysqli_fetch_array($tat_details)) {
                    $routine_tat_parts = explode("#", str_replace("@", "#", $tat["turn_around_time_routine_str"]));
                    $routine_days = $routine_tat_parts[0];
                    $routine_hours = $routine_tat_parts[1];
                    $routine_minutes = $routine_tat_parts[2];
                    $routine_tat = ($routine_days > 0 ? $routine_days . " Days " : "") .
                        ($routine_hours > 0 ? $routine_hours . " Hours " : "") .
                        ($routine_minutes > 0 ? $routine_minutes . " Minutes" : "");

                    $emergency_tat_parts = explode("#", str_replace("@", "#", $tat["turn_around_time_urgent_str"]));
                    $emergency_days = $emergency_tat_parts[0];
                    $emergency_hours = $emergency_tat_parts[1];
                    $emergency_minutes = $emergency_tat_parts[2];
                    $emergency_tat = ($emergency_days > 0 ? $emergency_days . " Days " : "") .
                        ($emergency_hours > 0 ? $emergency_hours . " Hours " : "") .
                        ($emergency_minutes > 0 ? $emergency_minutes . " Minutes" : "");
                    ?>
                    <tr>
                        <td><?php echo $n++; ?></td>
                        <td><?php echo $tat['testname']; ?></td>
                        <td><?php echo $routine_tat; ?></td>
                        <td><?php echo $emergency_tat; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

        </center>

    </div>


</body>

</html>
<script>

    function close_window(e) {
        var unicode = e.keyCode ? e.keyCode : e.charCode;

        if (unicode == 27) {
            window.close();
        }
    }
</script>
<style type="text/css" media="print">
    @page {
        size: potrait;
    }
</style>
<style>
    .txt_small {
        font-size: 10px;
    }

    .table {
        font-size: 11px;
    }

    .table th,
    .table td {
        padding: 1px;
    }

    @media print {
        .account_close_div {
            display: none;
        }

        .noprint {
            display: none;
        }
    }
</style>