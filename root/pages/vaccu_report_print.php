<?php

include("../../includes/connection.php");
include("../../includes/global.function.php");

$date1 = $_GET['dateFrom'];
$date2 = $_GET['dateTo'];
?>
<html>

<head>
    <title>Vaccu Usage Report</title>
    <link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../../css/custom.css" type="text/css" rel="stylesheet" />
    <script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <?php include('page_header.php'); ?>
            </div>
        </div>
        <hr>
        <center>
            <h4>Vaccu Usage Report</h4>
            <span style="float:right;">Print Time : <?php echo date("d M Y h:i A"); ?></span>
        </center>
        <div id="result"></div>
    </div>
    <input type="hidden" id="from" value="<?php echo $date1; ?>">
    <input type="hidden" id="to" value="<?php echo $date2; ?>">
</body>

</html>
<style>
    .no-print {
        display: none !important;
    }

    * {
        font-size: 12px;
    }

    @media print {
        @page {
            size: A4 portrait;
            /* This forces landscape orientation */
            margin: 10mm;
            /* Adjust the margin if needed */
        }
    }
</style>

<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../../css/loader.css" />
<script>
    $(document).ready(function () {
        $("#loader").hide();
        view();

    });

    function view() {
        $.post("vaccu_report_data.php",
            {
                type: 'load_vaccu_list',
                dateF: $("#from").val(),
                dateT: $("#to").val(),
            },
            function (data, status) {
                $("#result").html(data);
                window.print();
            });
    }
</script>