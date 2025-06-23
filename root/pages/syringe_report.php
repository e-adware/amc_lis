<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
    <center id="search_data">
        <span class="side_name">Date To</span>
        <input class="span2 datepicker dt" type="text" id="date_from" value="<?php echo date('Y-m-d'); ?>" readonly>
        <span class="side_name">Date To</span>
        <input class="span2 datepicker dt" type="text" id="date_to" value="<?php echo date('Y-m-d'); ?>" readonly>

        <button class="btn btn-success" onclick="get_report()"><i class="icon-search"></i> View</button>
    </center>
    <div class="res_div" id="result"></div>
</div>
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> -->
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<style>
    #date_from,
    #date_to {
        cursor: pointer;
    }

    .side_name {
        font-weight: bold;
    }

    .res_div {
        margin-top: 10px;
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
        padding: 5px 10px;
        border-radius: 5px;
        display: none;
    }
</style>
<script>

    $(document).ready(function () {
        var dateTimeChart;
        $("#loader").hide();
        $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: '0',
        });

    });
    function get_report() {
        $.post("pages/syringe_report_data.php",
            {
                type: 'load_report',
                dateF: $("#date_from").val(),
                dateT: $("#date_to").val(),
            },
            function (data, status) {
                $("#result").html(data);
                $("#result").slideDown();
            });
    }

</script>