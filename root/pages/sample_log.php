<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
    <table class="table table-condensed">
        <tr>
            <th class="head" colspan="3">
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <label for="date">From:</label>
                    <input id="date" type="text" class="datepicker" value="<?php echo date('Y-m-d'); ?>" readonly />
                    <button class="btn btn-success btn " onclick="load_sample_data()">View</button>
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
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<style>
    label,
    .datepicker {
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
        max-height: 600px;
        overflow-y: scroll;

    }

    @media print {
        #no_print {
            display: none !important;
        }
    }
</style>
<script>
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
        load_sample_data()
    });

    function load_sample_data() {
        $("#loader").show();
        $.post("pages/sample_log_data.php",
            {
                type: "load_data",
                date: $("#date").val(),

            },
            function (data, status) {
                $("#loader").hide();

                $("#load_data").html(data);
            })
    }

    function exportTableToExcel(date) {
        var url = "pages/sample_log_excel.php?date=" + btoa(date);
        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');

    }

    function print_table(date) {
        $.post("pages/sample_log_data.php",
            {
                type: "load_data",
                date: $("#date").val(),

            },
            function (data, status) {
                var printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.write('<html><head><title>Vaccu Usage Data</title>');
                printWindow.document.write('<style>@media print {#no_print {display: none !important;}} table {width: 100%; border-collapse: collapse;} th, td {border: 2px solid #ccc; padding: 8px;}</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write('<h4 style="text-align: center;">Vaccu Usage Data Dated: ' + date + '</h4 > ');

                printWindow.document.write(data);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            })
    }

</script>