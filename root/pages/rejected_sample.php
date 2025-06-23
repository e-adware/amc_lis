<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
    <center id="search_data">
        <span class="side_name">From</span>
        <input class="span2 datepicker dt" type="text" id="date_from" value="<?php echo date('Y-m-d'); ?>" readonly>
        <span class="side_name">To</span>
        <input class="span2 datepicker dt" type="text" id="date_to" value="<?php echo date('Y-m-d'); ?>" readonly>

        <button class="btn btn-success" onclick="view()"><i class="icon-search"></i> View</button>
    </center>
    <div class="" id="result" style="height:90vh;overflow-y:scroll;"></div>
</div>

<div id="loader" style="margin-top:-10%;"></div>
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
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
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

    function view() {
        $.post("pages/rejected_sample_data.php",
            {
                date1: $("#date_from").val().trim(),
                date2: $("#date_to").val().trim(),
                type: 1
            },
            function (data, status) {
                $("#result").html(data);
                $("#result").slideDown();
            });
    }
    function report_export(dt1,dt2)
    {
		var url = 'pages/rejected_sample_export.php?date1=' + (dt1) + '&date2=' + (dt2);
		window.location=url;
	}
    function report_print(dt1,dt2)
    {
		var url = 'pages/rejected_sample_print.php?date1=' + dt1 + '&date2=' + dt2;
        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>