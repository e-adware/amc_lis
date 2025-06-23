<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">

    <center>
        <span class="side_name">Date</span>
        <input class="span2 datepicker dt" type="text" id="date" value="<?php echo date('Y-m-d'); ?>" readonly>

        <select class="span2" id="qc_name">
            <option value="0">Select QC</option>
            <?php $qc_list_qry = mysqli_query($link, "SELECT * FROM `qc_master`");
            while ($qc_list = mysqli_fetch_array($qc_list_qry)) { ?>
                ?>
                <option value="<?php echo $qc_list['qc_id'] ?>"><?php echo $qc_list['qc_name']; ?></option>
            <?php } ?>
        </select>
        <button class="btn btn-success" onclick="generate_report()"><i class="icon-search"></i> Generate
            Report</button>
    </center>
    <div class="res_div" id="results">

    </div>
    <div class="res_div" id="date_div"></div>
</div>
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<style>
    #date {
        cursor: pointer;
    }

    .res_div {
        margin-top: 10px;
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
        padding: 5px 10px;
        border-radius: 5px;
        display: none;
    }

    .side_name {
        font-weight: bold;
    }
</style>
<script>
    $(document).ready(function () {
        $("#loader").hide();
        $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: '0',
        });
    });

    function generate_report() {
        $("#loader").show();
        dateF = $("#date").val();
        qc_id = $("#qc_name").val();
        $("#date_div").css('display', 'none');
        if (qc_id == '0') {
            alertmsg("Please Select QC", 0);
            $("#qc_name").focus();
            $("#loader").hide();
            return false;
        } else {
            $.post("pages/generate_qc_data.php",
                {
                    type: 'get_data',
                    dateF: dateF,
                    qc_id: qc_id,
                },
                function (data, status) {
                    // alert(data);
                    $("#loader").hide();

                    $("#results").css('display', 'block');
                    $("#results").html(data);
                });
        }

    }
    function load_date_data(o_date) {
        $("#loader").show();
        $("#results").slideUp();
        $.post("pages/generate_qc_data.php",
            {
                type: 'date_data',
                order_date: o_date,
                qc_id: qc_id,
            },
            function (data, status) {
                // alert(data);
                $("#loader").hide();
                $("#date_div").css('display', 'block');
                $("#date_div").html(data);
            });
    }

    function print_report(o_date) {
        qc_id = $("#qc_id").val();
        url = 'pages/generate_qc_print.php?o_date=' + btoa(o_date) + '&qc_id=' + btoa(qc_id);
        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    }

    function excel_report(o_date) {
        qc_id = $("#qc_id").val();
        url = 'pages/generate_qc_excel.php?o_date=' + btoa(o_date) + '&qc_id=' + btoa(qc_id);
        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    }
    function save_report(qc, o_date) {
        var results = [];

        $('input[id^="res_"]').each(function () {
            var $input = $(this);
            results.push({
                indice_id: $input.data('id'),
                test_name: $input.data('name'),
                result: $input.val(),
                unit: $input.data('unit')
            });
        });


        $.post("pages/generate_qc_data.php",
            {
                type: 'save_qc',
                order_date: o_date,
                qc_name: qc,
                results: JSON.stringify(results),
            },
            function (data, status) {
                // alert(data);
                if (data > 0) {
                    alertmsg("Saved", 1);

                } else {
                    alertmsg("Lot not available for this QC", 0);
                }
                load_date_data(o_date);
            });
    }

    function alertmsg(msg, n) {
        $.gritter.add({
            text: '<h5 style="text-align:center;">' + msg + '</h5>',
            time: 1000,
            sticky: false
        });
        if (n > 0) {
            $(".gritter-item").css("background", "#237438");
        }
    }
</script>