<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
    <table class="table table-bordered table-condensed table-report">
        <tr>
            <th colspan="2"></th>
        </tr>
        <tr>
            <th style="padding-left: 33%;">
                <b>Include Test</b>
                <select multiple class="span5" id="inc_test">
                    <?php
                    $test_qry = mysqli_query($link, " SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' ORDER BY `testname` ");
                    while ($test = mysqli_fetch_array($test_qry)) {
                        echo "<option value='$test[testid]'>$test[testname]</option>";
                    }
                    ?>
                </select>
            </th>

        </tr>
        <tr>
            <th colspan="2" style="text-align:center;">

                <b>From</b>
                <input class="form-control datepicker span2" type="text" name="from" id="from"
                    value="<?php echo date("Y-m-d"); ?>" <?php echo $dept_sel_dis; ?> readonly>
                <b>To</b>
                <input class="form-control datepicker span2" type="text" name="to" id="to"
                    value="<?php echo date("Y-m-d"); ?>" <?php echo $dept_sel_dis; ?> readonly>

                <select id="urgent" style="width: 150px; margin-left: 50px;">
                    <option value="">All</option>
                    <option value="0">Routine</option>
                    <option value="1">Emergency</option>
                </select>


            </th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">
                <div class="row" style="margin-top: 5px;">
                    <span class="input-group-btn">
                        <button type="button" id="search_btn1" name="search_btn" class="btn btn-info"
                            onClick="tat_calculate(1)">Overall Summary Metrics</button>
                        <button type="button" id="search_btn2" name="search_btn" class="btn btn-info"
                            onClick="tat_calculate(2)">TAT Summary by Test</button>
                    </span>
                </div>
            </th>
        </tr>
    </table>
</div>
<hr />
<div id="load_data" class="ScrollStyle"></div>
<div id="loader" style="margin-top:100px;display:none;"></div>

<!-- <div id="loader" style="margin-top:-10%;"></div> -->
<link rel="stylesheet" href="../css/loader.css" />
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script src="pages/js/bootbox.min.js"></script>
<link href="../css/loader.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
    $(document).ready(function () {
        $("#inc_test").select2({ theme: "classic" });
        $("#exc_test").select2({ theme: "classic" });
        $("#loader").hide();
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            maxDate: '0',
            yearRange: "-150:+0",
        });
    });

    function tat_calculate(val) {
        $("#loader").show();
        $("#search_btn").prop("disabled", true);
        $.post("pages/test_turnaround_time_main_new.php",
            {
                type: "tat_calculate",
                include_testid: $("#inc_test").val(),
                exclude_testid: $("#exc_test").val(),
                date1: $("#from").val().trim(),
                date2: $("#to").val().trim(),
                user: $("#user").text().trim(),
            },
            function (data, status) {
                // alert(data);
                // console.log(data);
                if (val == 1) {
                    overall_summary_view();
                }
                if (val == 2) {
                    test_wise_summary_view();
                }
            }
        )
    };
    function overall_summary_view() {
        $("#loader").show();
        $.post("pages/test_turnaround_time_main_new.php",
            {
                type: "overall_summary_view",
                date1: $("#from").val().trim(),
                date2: $("#to").val().trim(),
                user: $("#user").text().trim(),
            },
            function (data, status) {
                // alert(data);
                $("#search_btn").prop("disabled", false);
                $("#loader").hide();
                $("#load_data").html(data);
            }
        )
    };
    function test_wise_summary_view() {
        if (!$("#inc_test").val()) {
            var inc_tests = "0";
        }
        else {
            var inc_tests = $("#inc_test").val().toString();
        }
        // alert(inc_tests);
        $("#loader").show();
        $.post("pages/test_turnaround_time_main_new.php",
            {
                type: "test_wise_summary_view",
                include_test: inc_tests,
                // exclude_test: $("#exc_test").val(),
                tat_minutes: $("#tat_minutes").val(),
                urgent: $("#urgent").val(),
                user: $("#user").text().trim(),
                date1: $("#from").val().trim(),
                date2: $("#to").val().trim(),
            },
            function (data, status) {
                // alert(data);
                $("#search_btn").prop("disabled", false);
                $("#loader").hide();
                $("#load_data").html(data);
            }
        )
    };

    function print_table() {
        $.post("pages/test_turnaround_time_main_new.php",
            {
                include_testid: $("#inc_test").val(),
                exclude_testid: $("#exc_test").val(),
                date1: $("#from").val().trim(),
                date2: $("#to").val().trim(),
                user: $("#user").text().trim(),
                type: "test_wise_summary_view",

            },
            function (data, status) {
                var printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.write('<html><head><title>Vaccu Usage Data</title>');
                printWindow.document.write('<style>@media print {#no_print {display: none !important;}} table {width: 100%; border-collapse: collapse;} th, td {border: 2px solid #ccc; padding: 8px;}</style>');
                printWindow.document.write('</head><body>');
                // printWindow.document.write('<h4 style="text-align: center;">Vaccu Usage Data Dated: ' + date + '</h4 > ');

                printWindow.document.write(data);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            })
    }

    function exportTableToExcel(date1, date2, urgent, include_testid) {
        // console.log(date1, date2, urgent, include_testids);
        var url = "pages/overall_tat_excell.php?date1=" + btoa(date1) + "&date2=" + btoa(date2) + "&urgent=" + btoa(urgent) + "&include_testid=" + btoa(include_testid);

        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');

    }
</script>