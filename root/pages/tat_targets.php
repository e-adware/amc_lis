<style>
    th {
        cursor: pointer;
        position: relative;
        padding-right: 20px !important;
    }

    th:hover {
        background-color: #f5f5f5;
    }

    th.sorted-asc::after {
        content: "↑";
        position: absolute;
        right: 8px;
        font-size: 12px;
    }

    th.sorted-desc::after {
        content: "↓";
        position: absolute;
        right: 8px;
        font-size: 12px;
    }
</style>
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?> (Defined by Laboratory)</span>
    </div>
</div>
<!--End-header-->
<div class="container-fluid">
    <!-- Search Bar -->
    <div style="margin-bottom: 15px;">
        <input type="text" class="form-control" id="searchInput" placeholder="Search test names..."
            oninput="filterTable()">
    </div>
    <div>
        <button class="btn btn-print btn-small text-right" onclick="print_page()">Print</button>
    </div>
    <div style="max-height:700px;overflow-y:scroll;background-color: white;">
        <table class="table table-bordered table-condensed" id="tatTable">
            <thead class="table_header_fix">
                <tr>
                    <th onclick="sortTable(0)">Slno.</th>
                    <th onclick="sortTable(1)">Test Name</th>
                    <th onclick="sortTable(2, true)">Routine TAT Target ( Days * Hours * Minutes )</th>
                    <th onclick="sortTable(3, true)">Emergency TAT Target ( Days * Hours * Minutes )</th>
                </tr>
            </thead>
            <tbody id="tatTableBody">
                <?php
                $n = 1;
                $tat_details = mysqli_query($link, "SELECT `testname`,`turn_around_time_routine_str`,`turn_around_time_urgent_str` FROM `testmaster`  WHERE `testname`!='' ORDER BY `testname`");
                while ($tat = mysqli_fetch_array($tat_details)) {
                    $routine_tat_parts = explode("#", str_replace("@", "#", $tat["turn_around_time_routine_str"]));
                    $routine_days = $routine_tat_parts[0];
                    $routine_hours = $routine_tat_parts[1];
                    $routine_minutes = $routine_tat_parts[2];
                    $routine_tat = ($routine_days > 0 ? $routine_days . " Days " : "") .
                        ($routine_hours > 0 ? $routine_hours . " Hours " : "") .
                        ($routine_minutes > 0 ? $routine_minutes . " Minutes" : "");
                    $routine_sort_value = ($routine_days * 1440) + ($routine_hours * 60) + $routine_minutes;

                    $emergency_tat_parts = explode("#", str_replace("@", "#", $tat["turn_around_time_urgent_str"]));
                    $emergency_days = $emergency_tat_parts[0];
                    $emergency_hours = $emergency_tat_parts[1];
                    $emergency_minutes = $emergency_tat_parts[2];
                    $emergency_tat = ($emergency_days > 0 ? $emergency_days . " Days " : "") .
                        ($emergency_hours > 0 ? $emergency_hours . " Hours " : "") .
                        ($emergency_minutes > 0 ? $emergency_minutes . " Minutes" : "");
                    $emergency_sort_value = ($emergency_days * 1440) + ($emergency_hours * 60) + $emergency_minutes;

                    ?>
                    <tr>
                        <td><?php echo $n++; ?></td>
                        <td class="searchable"><?php echo $tat['testname']; ?></td>
                        <td data-sort="<?php echo $routine_sort_value; ?>"><?php echo $routine_tat; ?></td>
                        <td data-sort="<?php echo $emergency_sort_value; ?>"><?php echo $emergency_tat; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<hr />
<div id="load_data" class="ScrollStyle"></div>
<div id="loader" style="margin-top:100px;display:none;"></div>
<script>
    function print_page() {
        const url = "pages/tat_targets_print.php";
        window.open(url, '_blank', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    }

    function sortTable(columnIndex, isTimeColumn = false) {
        const table = document.getElementById("tatTable");
        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        let direction = "asc";

        const header = table.querySelectorAll("th")[columnIndex];
        if (header.classList.contains("sorted-asc")) {
            direction = "desc";
            header.classList.remove("sorted-asc");
            header.classList.add("sorted-desc");
        } else if (header.classList.contains("sorted-desc")) {
            direction = "asc";
            header.classList.remove("sorted-desc");
            header.classList.add("sorted-asc");
        } else {
            table.querySelectorAll("th").forEach(th => {
                th.classList.remove("sorted-asc", "sorted-desc");
            });
            header.classList.add("sorted-asc");
        }

        rows.sort((a, b) => {
            let aValue, bValue;

            if (isTimeColumn) {
                aValue = parseFloat(a.cells[columnIndex].getAttribute("data-sort")) || 0;
                bValue = parseFloat(b.cells[columnIndex].getAttribute("data-sort")) || 0;
            } else {
                aValue = a.cells[columnIndex].textContent.trim();
                bValue = b.cells[columnIndex].textContent.trim();

                if (columnIndex === 0) {
                    aValue = parseInt(aValue);
                    bValue = parseInt(bValue);
                }
            }

            if (direction === "asc") {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });

        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }

        rows.forEach(row => {
            tbody.appendChild(row);
        });
    }

    function filterTable() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toUpperCase();
        const tbody = document.getElementById("tatTableBody");
        const rows = tbody.querySelectorAll("tr");

        for (let i = 0; i < rows.length; i++) {
            const testNameCell = rows[i].querySelector("td:nth-child(2)");
            if (testNameCell) {
                const txtValue = testNameCell.textContent || testNameCell.innerText;
                if (txtValue.toUpperCase().includes(filter)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
</script>