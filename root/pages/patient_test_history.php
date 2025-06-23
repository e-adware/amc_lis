<div id="content-header">
    <div class="header_div"> <span class="header">
            <?php echo $menu_info["par_name"]; ?>
        </span></div>
</div>
<div class="container-fluid">
    <table class="table table-bordered table-condensed text-center ">

        <tr>
            <th>
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <label for="hospital_id">Hospital No.</label>
                    <input type="text" class="span3" id="hospital_id" onkeyup="check_input()"
                        placeholder="Hospital No." />
                    <label for="barcode">Barcode No.</label>
                    <input type="text" class="span3" id="barcode" onkeyup="check_input()" placeholder="Barcode No." />
                    <label for="pat_name">Name</label>
                    <input type="text" class="span3" id="pat_name" onkeyup="check_input()" placeholder="Patient Name" />

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
    .datepicker {
        font-weight: bold;
    }

    .table .head {
        text-align: center;
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

        // Debounce utility
        function debounce(func, delay) {
            let timer;
            return function () {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, arguments), delay);
            };
        }

        const debouncedLoadData = debounce(load_data, 300);

        // Initial load on page load
        triggerLoadIfEmpty();

        // Trigger load on Enter key
        $(document).on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                debouncedLoadData();
            }
        });

        // Optional: Monitor inputs in real-time
        $("#hospital_id, #pat_name").on("input", function () {
            triggerLoadIfEmpty();
        });

        function triggerLoadIfEmpty() {
            const hospital_id = $("#hospital_id").val().trim();
            const pat_name = $("#pat_name").val().trim();

            if (hospital_id === "" && pat_name === "") {
                load_data(); // no debounce on initial load
            }
        }

    });
    function load_data() {
        $("#loader").show();

        $.post("pages/patient_test_history_data.php", {
            type: "load_data",
            to_date: $("#to_date").val(),
            from_date: $("#from_date").val(),
            hospital_id: $("#hospital_id").val(),
            barcode: $("#barcode").val(),
            pat_name: $("#pat_name").val(),
        }, function (data) {
            $("#loader").hide();
            $("#load_data").html(data);
        });
    }
    function print_report(uhid, opd_id, ipd_id, batch_no, iso, testid, type_id) {
        view = 2;
        var user = $("#user").text().trim();

        var url = "pages/pathology_report_print.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(ipd_id) + "&batch_no=" + btoa(batch_no) + "&tests=" + btoa(testid) + "&hlt=" + btoa(testid) + "&user=" + btoa(user) + "&sel_doc=" + btoa(0) + "&view=" + btoa(1) + "&iso_no=" + btoa(0) + "&doc_view=" + btoa(view) + "&dept_id=" + btoa(type_id);
        var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
    }
</script>