<?php
session_start();

include("../../includes/connection.php");

$c_user = trim($_SESSION['emp_id']);

$u_level = mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level = $u_level["levelid"];

if (!$branch_id) {
    $branch_id = $emp_info["branch_id"];
}


// important
$fdate = base64_decode($_GET['fdate']);
$tdate = base64_decode($_GET['tdate']);
$expense = base64_decode($_GET['exp']);

?>
<html>

<head>
    <title>Daily Expense Report</title>
    <link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../../css/custom.css" type="text/css" rel="stylesheet" />
    <script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)">
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <?php include('page_header.php'); ?>
            </div>
        </div>
        <hr>
        <center>
            <h4>Daily Expense </h4>
            <span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
            <div class="noprint ">
                <input type="button" class="btn btn-success" id="Name1" value="Print" onclick="javascript:window.print()">
                <input type="button" class="btn btn-danger" id="Name2" value="Exit" onclick="javascript:window.close()">
            </div>
        </center>
        <div id="load_data"></div>
    </div>
    <input type="hidden" id="fdate" value="<?php echo $fdate; ?>">
    <input type="hidden" id="tdate" value="<?php echo $tdate; ?>">
    <input type="hidden" id="expense_id" value="<?php echo $expense; ?>">
</body>

</html>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
    $(document).ready(function() {
        $("#loader").hide();
        view();
        //$(".noprint").hide();
    });

    function view() {
        $("#loader").show();
        $.post("daily_expense_data.php", {
                type: 'print_exp',
                expense_id: $("#expense_id").val(),
                fdate: $("#fdate").val(),
                tdate: $("#tdate").val(),
            },
            function(data, status) {
                $("#loader").hide();
                $("#load_data").html(data);
                $("#print_div").hide();
            })
    }

    function close_window(e) {
        var unicode = e.keyCode ? e.keyCode : e.charCode;

        if (unicode == 27) {
            window.close();
        }
    }

    function close_window_child() {
        window.close();
    }

    function refreshParent() {
        window.opener.location.reload(true);
    }
</script>
<style>
    .txt_small {
        font-size: 13px;
    }

    .table {
        font-size: 12px;
    }

    @media print {
        .noprint1 {
            display: none;
        }

        .noprint {
            display: none;
        }
    }

    .ipd_serial {
        display: none;
    }

    .table {
        margin-bottom: 0px;
    }

    .table-condensed th,
    .table-condensed td {
        padding: 0 10px 0 0;
    }
</style>