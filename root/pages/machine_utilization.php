<?php
session_start();
include("../../includes/connection.php");


include("app/init.php");
try {
    $db = new Db_LoaderMS();
} catch (PDOException $e) {
    echo "<h4>Connection failed:<br/>" . $e->getMessage() . "</h4>";
    exit;
}
?>
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
    <table class="table table-condensed">
        <tr>
            <th class="head">
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <label for="from_date">From:</label>
                    <input id="from_date" type="text" class="datepicker" value="<?php echo date('Y-m-d'); ?>"
                        readonly />
                    <label for="to_date">To:</label>
                    <input id="to_date" type="text" class="datepicker" value="<?php echo date('Y-m-d'); ?>" readonly />
                    <label style="display: none;" for="res_type">Result Type</label>
                    <select onchange="load_data()" class="span2" id="res_type" style="display: none">
                        <option value="0">All Results</option>
                        <?php
                        $res_type = $db->setQuery("SELECT DISTINCT(result_type) FROM tpl_patient_orders ORDER BY result_type;")->fetch_all();
                        foreach ($res_type as $r_type) {
                            ?>
                            <option value="<?= $r_type['result_type']; ?>"><?= $r_type['result_type']; ?></option>
                        <?php } ?>
                    </select>
                    <button class="btn btn-success btn " onclick="load_data()">View</button>
                </div>
            </th>
        </tr>

    </table>
</div>
<div id="load_data" class="ScrollStyleY">

</div>
<div id="loader" style="margin-top:-10%;"></div>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../js/select2.min.js"></script>
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
    label,
    .datepicker,
    .head-text {
        font-weight: bold;
    }

    .table .head {
        text-align: center;
    }

    #load_data {
        margin: 4px 8px;
        padding: 2px 4px;
        background: #fff;
        box-shadow: rgba(255, 255, 255, 0.1) 0px 1px 1px 0px inset, rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;
        max-height: 650px;
        overflow-y: scroll;

    }

    @media print {
        #no_print {
            display: none !important;
        }
    }
</style>

<script>

    $('.sel2').select2();
    $(document).ready(function () {
        $("#loader").hide();
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            maxDate: '0',
            yearRange: "-150:+0",
            //defaultDate:'2000-01-01',
        });
        load_data();
    });
    function load_data() {
        $("#loader").show();

        $.post("pages/machine_utilization_data.php",
            {
                type: 'load_data',
                fdate: $("#from_date").val(),
                tdate: $("#to_date").val(),
                res_type: $("#res_type").val()
            },
            function (data, status) {
                $("#loader").hide();

                $("#load_data").html(data);
            })
    }



</script>