<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
//include("pathology_normal_range_new.php");
$order_date = base64_decode($_GET['o_date']);
$qc_id = base64_decode($_GET['qc_id']);
$filename = "qc_report" . $order_date . ".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename=' . $filename);
?>
<html>

<head>
    <title>QC Report</title>
    <link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="../../css/custom.css" type="text/css" rel="stylesheet" />
    <script src="../../js/jquery.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)" onunload="refreshParent()">
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <?php $br = 0;
                while ($br < $top_line_break) {
                    echo "<br>";
                    $br++;
                }
                $lot = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `qc_lot_master` WHERE `qc_id` = '$qc_id' AND `status` = '1'"));
                $qc_name = mysqli_fetch_array(mysqli_query($link, "SELECT a.*, b.name FROM `qc_master` a, `lab_instrument_master` b WHERE a.`qc_id` = '$qc_id' AND a.instrument_id = b.`id`"));


                // $baseline_date = mysqli_fetch_array(mysqli_query($link, "SELECT b.`order_date` FROM `qc_baseline` a, `qc_results` b WHERE `lot_id` = '$lot[id]' AND a.`indice_id` = b.`indice_id`"));
                

                ?>

                <table class="table table-condensed page_header">
                    <tr>
                        <th>QC Name: </th>
                        <td><?php echo $lot['control_name']; ?></td>
                        <th>Sample ID: </th>
                        <td><?php echo $qc_name['sample_id']; ?></td>
                    </tr>
                    <tr>
                        <th>Lot No: </th>
                        <td><?php echo $lot['lot_no']; ?></td>
                        <th>Expiry Date: </th>
                        <td><?php echo date('d-m-Y', strtotime($lot['exp_date'])); ?></td>
                    </tr>
                    <!-- <tr>
                        <th>Metering Date & Time: </th>
                        <th colspan="3">
                            <?php echo date('d-m-Y', strtotime($baseline_date['order_date'])) . " / " . date('h:i A', strtotime($baseline_date['order_date'])); ?>
                        </th>
                    </tr> -->
                    <tr>
                        <th>Fluid: </th>
                        <td><?php
                        $fluid_name = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `qc_fluid` WHERE `id` = '$qc_name[fluid_id]'"));
                        echo $fluid_name['name']; ?></td>
                        <th>Instrument Name: </th>
                        <td><?= $qc_name['name'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row ">
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>#</th>
                        <!-- <th>Indice ID</th> -->
                        <th>Indice Name</th>
                        <th>Result</th>
                        <th>Unit</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qc_res_qry = mysqli_query($link, "SELECT * FROM `qc_results` WHERE `order_date` = '$order_date'");
                    $n = 1;
                    while ($qc_res = mysqli_fetch_assoc($qc_res_qry)) {
                        ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $qc_res['indice_name']; ?></td>
                            <td><?= $qc_res['result']; ?></td>
                            <td><?= $qc_res['unit']; ?></td>
                            <td><?= date('d-M-Y / h:i A', strtotime($qc_res['order_date'])); ?></td>

                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</body>

</html>

<style type="text/css" media="print">
    @page {
        size: portrait;
    }

    .txt_small {
        font-size: 10px;
    }

    .table {
        font-size: 11px;
    }

    @media print {
        .noprint1 {
            display: none;
        }

        .noprint {
            display: none;
        }
    }

    * {
        line-height: 14px !important;
    }

    h3 {
        margin: 0;
    }

    h4 {
        margin: 0;
    }

    .page_header {
        font-size: 13px !important;
        border-bottom: 1.5px solid #000;
    }
</style>
<script>
    $(document).ready(function () {
        // window.print();
    });
</script>