<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
    <center id="search_data">
        <span class="side_name">From</span>
        <input class="span2 datepicker dt" type="text" id="date_from" value="<?php echo date('Y-m-d'); ?>" readonly>
        <span class="side_name">To</span>
        <input class="span2 datepicker dt" type="text" id="date_to" value="<?php echo date('Y-m-d'); ?>" readonly>

        <button class="btn btn-success" onclick="get_list()"><i class="icon-search"></i> View</button>
    </center>
    <div class="" id="result"></div>
</div>
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

<script>

    $(document).ready(function () {
        var dateTimeChart;
        $("#loader").hide();
        $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: '0',
        });

    });

    function get_list() {
        $.post("pages/vaccu_report_data.php",
            {
                type: 'load_vaccu_list',
                dateF: $("#date_from").val(),
                dateT: $("#date_to").val(),
            },
            function (data, status) {
                $("#result").html(data);
                $("#result").slideDown();
            });
    }
    function loadPatientVaccu(vaccId) {
        const resultElement = $("#result");
        resultElement.slideUp(); // Cached DOM lookup

        const dateFrom = $("#date_from").val();
        const dateTo = $("#date_to").val();

        $.post("pages/vaccu_report_data.php", {
            type: 'load_vacc_det',
            vacc_id: vaccId,
            dateF: dateFrom,
            dateT: dateTo
        },
            (data) => {
                setTimeout(() => {
                    resultElement.html(data).slideDown(); // Combined operations for efficiency
                }, 400);
            }
        );
    }

    function load_patient_vaccu(vacc_id) {
        const resultDiv = $("#result");
        resultDiv.slideUp();
        $.post("pages/vaccu_report_data.php",
            {
                type: 'load_vacc_det',
                vacc_id: vacc_id,
                dateF: $("#date_from").val(),
                dateT: $("#date_to").val(),
            },
            function (data, status) {
                setTimeout(function () {
                    resultDiv.slideDown().html(data);
                }, 400);
            });
    }

    function print_vaccu_list() {
        const dateFrom = $("#date_from").val();
        const dateTo = $("#date_to").val();
        url = 'pages/vaccu_report_print.php?dateFrom=' + dateFrom + '&dateTo=' + dateTo;
        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');

    }

    function export_vaccu_list() {

    }
</script>