<div id="content-header">
    <div class="header_div"> <span class="header">
            <?php echo $menu_info["par_name"]; ?>
        </span></div>
</div>
<div class="container-fluid">
    <table class="table ">
        <tr>
            <th>
                <div
                    style="display: flex; justify-content: center; align-items: center; gap: 10px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                    <label for="from_date">From:</label>
                    <input id="from_date" type="text" class="datepicker span2" value="<?php echo date('Y-m-d'); ?>"
                        readonly />
                    <label for="to_date">To:</label>
                    <input id="to_date" type="text" class="datepicker span2" value="<?php echo date('Y-m-d'); ?>"
                        readonly />

                    <button class="btn btn-success btn " onclick="load_data()">View</button>

                </div>
                <div
                    style="display: flex; justify-content: center; align-items: center; gap: 20px; background-color: #f8f9fa; padding: 5px; border-radius: 5px;">

                    <select id="entryTypeFilter" class="form-select" onchange="filterEntryType()"
                        style="width: 200px; display: inline-block; margin-left: 10px;">
                        <option value="All">All</option>
                        <option value="Manual">Manual</option>
                        <option value="Edited">Edited</option>
                        <option value="Machine">Machine</option>
                    </select>

                    <input type="text" id="cashMemoSearch" placeholder="Type Cash Memo no. here..."
                        onkeyup="filterByCashMemo()" style="padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
                </div>
                <!-- <div style="margin-bottom: 15px; text-align: center;">
                    <label for="entryTypeFilter"><strong>Filter by Entry Type:</strong></label>
                    <select id="entryTypeFilter" class="form-select" onchange="filterEntryType()"
                        style="width: 200px; display: inline-block; margin-left: 10px;">
                        <option value="All">All</option>
                        <option value="Manual">Manual</option>
                        <option value="Edited">Edited</option>
                        <option value="Machine">Machine</option>
                    </select>
                </div> -->

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
    .table-container {
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid #ddd;
    }

    .table-container thead th {
        position: sticky;
        top: 0;
        background-color: rgb(146, 145, 145);
        color: white;
        /* z-index: 2; */
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
        $.post("pages/result_entry_audit_data.php",
            {
                type: "load_data",
                to_date: $("#to_date").val(),
                from_date: $("#from_date").val(),

            },
            function (data, status) {
                $("#loader").hide();

                $("#load_data").html(data);
            })
    }

    function filterEntryType() {
        var selected = document.getElementById("entryTypeFilter").value;
        var rows = document.querySelectorAll("#resultTable tbody tr");

        rows.forEach(function (row) {
            var type = row.getAttribute("data-entry-type");
            if (selected === "All" || type === selected) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }


    function filterByCashMemo() {
        const input = document.getElementById("cashMemoSearch").value.toLowerCase().trim();
        const rows = document.querySelectorAll("#resultTable tbody tr");

        rows.forEach(row => {
            const cashMemoCell = row.cells[2]; // 3rd column: Cash Memo No.
            if (cashMemoCell) {
                const text = cashMemoCell.textContent.toLowerCase();
                if (text.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    }
    // function exportTableToExcel(fdate, tdate, test, priority, time, ward) {
    //     var url = "pages/ward_test_volume_excel.php?from_date=" + btoa(fdate) + "&to_date=" + btoa(tdate) + "&sel_test=" + btoa(test) + "&priority=" + btoa(priority) + "&time_period=" + btoa(time) + "&ward=" + btoa(ward);
    //     window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');

    // }
</script>