<div id="content-header">
    <div class="header_div"> <span class="header">
            <?php echo $menu_info["par_name"]; ?>
        </span></div>
</div>
<div class="container-fluid">
    <table class="table ">
        <tr>
            <th>
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <label for="from_date">From:</label>
                    <input id="from_date" type="text" class="datepicker span2" value="<?php echo date('Y-m-d'); ?>"
                        readonly />
                    <label for="to_date">To:</label>
                    <input id="to_date" type="text" class="datepicker span2" value="<?php echo date('Y-m-d'); ?>"
                        readonly />
                    <select onchange="load_data()" id="sel_ward" class="span2">
                        <option value="0">---ALL WARDS---</option>
                        <?php
                        $ward_qry = mysqli_query($link, "SELECT * FROM  ward_master ORDER BY `ward_name`");


                        while ($ward = mysqli_fetch_array($ward_qry)) {
                            echo "<option value='$ward[id]'>$ward[ward_name]</option>";
                        }
                        ?>
                    </select>
                    <input type="text" onkeyup="search_data(event)" class="span2 form-control" id="hospital_id"
                        placeholder="HOSPITAL ID" />
                    <button class="btn btn-success" onclick="load_data()"><i class="icon-search"></i> Search</button>
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
    .datepicker {
        font-weight: bold;
    }

    .table .head {
        text-align: center;
        background: #ddd;
        height: 30px;

    }

    .table-head {
        background-color: #fff;
        padding: 2px 4px;
    }

    .table-head th {
        border-top: 0px;
        padding: 5px 0px
    }

    #load_data {
        margin: 4px 8px;
        padding: 2px 4px 10px 4px;
        background: #fff;
        box-shadow: rgba(255, 255, 255, 0.1) 0px 1px 1px 0px inset, rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px;
        max-height: 600px;
        overflow-y: scroll;
        border-radius: 4px;

    }

    .table-header-fix {
        position: sticky;
        top: 0;
        background: #ddd;
        z-index: 1;
        height: 40px;
    }

    @media print {
        #no_print {
            display: none !important;
        }
    }

    .table-container {
        overflow-x: scroll;
        /* Always show horizontal scrollbar */
        max-width: 100%;
        padding-bottom: 1rem;
        /* Ensures scroll doesn't overlap content */
        scrollbar-width: auto;
        /* Firefox */
        scrollbar-color: #ccc #f8f9fa;
    }

    /* Chrome, Edge, Safari */
    .table-container::-webkit-scrollbar {
        height: 12px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f8f9fa;
    }

    .table-container::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 6px;
        border: 3px solid #f8f9fa;
    }

    .highlight {
        background-color: #00ff3233;
    }


    .table-responsive {
        width: max-content;
        /* table-layout: fixed; */
        border-collapse: collapse;
    }

    .table-responsive th,
    .table-responsive td {
        white-space: nowrap;
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        min-width: 80px;
        text-align: center;
    }

    .head-text {
        font-size: 14px;

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
        load_data();
    });
    function search_data() {
        if (event.key === "Enter" && ($("#hospital_id").val().trim().length > 4 || $("#hospital_id").val().trim().length == 0)) {
            load_data();
        }
    }

    function load_data() {
        $("#loader").show();
        $.post("pages/ward_wise_patient_report_data.php",
            {
                type: "load_data",
                to_date: $("#to_date").val(),
                from_date: $("#from_date").val(),
                ward: $("#sel_ward").val(),
                hospital_id: $("#hospital_id").val(),
            },
            function (data, status) {
                $("#loader").hide();
                $("#load_data").html(data);
            })
    }

    function print_report(fdate, tdate, ward, patient_id) {
        $.post("pages/ward_wise_patient_report_data.php",
            {
                type: "load_data",
                to_date: tdate,
                from_date: fdate,
                ward: ward,
                hospital_id: patient_id,

            },
            function (data, status) {
                var printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.write('<html><head><title>Ward Patient Report</title>');
                printWindow.document.write('<style>@media print {.no_print {display: none !important;}} table {width: 100%; border-collapse: collapse;} th, td {border: 2px solid #ccc; padding: 4px;}</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(data);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            })
    }

</script>