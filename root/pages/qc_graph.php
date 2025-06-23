<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
    <center>
        <span class="side_name">Date To</span>
        <input class="span2 datepicker dt" type="text" id="date_from" value="<?php echo date('Y-m-d'); ?>" readonly>
        <span class="side_name">Date To</span>
        <input class="span2 datepicker dt" type="text" id="date_to" value="<?php echo date('Y-m-d'); ?>" readonly>
        <select class="span2" id="qc_sel" onchange="load_indice()">
            <option value="0">Select QC</option>
            <?php $qc_list_qry = mysqli_query($link, "SELECT * FROM `qc_master`");
            while ($qc_list = mysqli_fetch_array($qc_list_qry)) { ?>
                ?>
                <option value="<?php echo $qc_list['qc_id'] ?>"><?php echo $qc_list['qc_name']; ?></option>
            <?php } ?>
        </select>
        <select class="span2" id="indice_sel">
            <option value="0">Select Indice</option>

        </select>
        <button class="btn btn-success" onclick="get_graph()"><i class="icon-search"></i> Generate
            Report</button>
    </center>
    <div class="res_div" id="result">
        <button class="btn btn-info btn-mini no-print" style="float: right" onclick="print_graph()">Print</button>
        <div id="data" class="">

        </div>
        <div id="lineChart" style="width: 100%; height: 600px;"></div>
    </div>
</div>
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> -->
<script src="../js/plotly-latest.min.js"></script>
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


    @media print {
        @page {
            size: A4 landscape;
            /* This forces landscape orientation */
            margin: 10mm;
            /* Adjust the margin if needed */
        }

        body {
            font-family: Arial, sans-serif;
        }

        /* Ensure only the content gets printed */
        #result {
            width: 100%;
            height: 100%;
            visibility: visible;
        }

        /* Hide other elements (like the button) during print */
        body * {
            visibility: hidden;
        }

        #result,
        #result * {
            visibility: visible;
        }

        .no-print {
            visibility: hidden;
        }

        #result {
            position: absolute;
            left: 0;
            top: 0;
        }
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
        window.onafterprint = function () {
            location.reload(); // Refresh the page after print dialog closes
        };
    });

    function print_graph() {
        var printContents = $('#result').html();
        var originalContents = $('body').html();
        $('body').html(printContents);
        window.print();
        if ($('body').html(originalContents)) {
            location.reload();
        }
    }

    function load_indice() {
        $("#indice_sel").empty();
        $.post("pages/qc_graph_data.php",
            {
                type: 'load_indice',
                qc_id: $("#qc_sel").val(),
                dateF: $("#date_from").val(),
                dateT: $("#date_to").val(),
            },
            function (data, status) {
                $("#indice_sel").append(data);
            });
    }
    function get_graph() {
        if ($("#indice_sel").val() == '0') {
            alertmsg("Please Select Indice", 0);
            return false;
        }
        if ($("#qc_sel").val() == '0') {
            alertmsg("Please Select QC", 0);
            return false;
        } else {

            $.post("pages/qc_graph_data.php",
                {
                    type: 'get_graph',
                    indice_sel: $("#indice_sel").val(),
                    qc_sel: $("#qc_sel").val(),
                    dateF: $("#date_from").val(),
                    dateT: $("#date_to").val(),
                },
                function (data, status) {
                    var response = data.split("@@");
                    var result = response[0];
                    var date = response[1];
                    var mean = response[2];
                    var sd = response[3];
                    var i_name = response[4];
                    // alert(data);
                    gph(result, date, mean, sd, i_name);
                    get_details();

                });
        }
    }

    function get_details() {
        $.post("pages/qc_graph_data.php",
            {
                type: 'get_details',
                indice_sel: $("#indice_sel").val(),
                qc_sel: $("#qc_sel").val(),
                dateF: $("#date_from").val(),
                dateT: $("#date_to").val(),
            },
            function (data, status) {
                $("#data").html(data);

            });
    }

    function gph(result, date, m, s, i_name) {
        $("#result").slideDown();

        // Mean and SD
        const mean = parseFloat(m);
        const sd = parseFloat(s);

        // Convert result into numerical dataPoints
        let plots = [];
        let plotres = result.split(",");
        for (let i = 0; i < plotres.length; i++) {
            plots.push(parseFloat(plotres[i]));  // Ensure data points are numeric
        }
        const dataPoints = plots;

        // Y-axis specific points (mean, SD multiples, and data points)
        const yAxisPoints = [
            mean + (3 * sd),
            mean + (2 * sd),
            mean + (1 * sd),
            mean,
            mean - (1 * sd),
            mean - (2 * sd),
            mean - (3 * sd),
            // ...dataPoints  // Include the actual data points in y-axis ticks
        ];

        // Example date/time values for the x-axis
        let xlabel = [];
        let vxres = date.split(",");
        for (let i = 0; i < vxres.length; i++) {
            xlabel.push(vxres[i]);  // Ensure the x-axis labels are properly split
        }
        const xValues = xlabel;

        // Create the line chart data with text annotations
        const trace1 = {
            x: xValues,
            y: dataPoints,
            mode: 'lines+markers+text',  // Include text mode
            type: 'scatter',
            name: 'Data Points',
            line: { color: 'rgba(75, 192, 192, 1)' },
            marker: { color: 'rgba(75, 192, 192, 1)' },
            text: dataPoints.map(p => p.toFixed(2)),  // Display formatted values
            textposition: 'top center'  // Position of the text
        };

        // Create the mean line
        const meanLine = {
            x: xValues,
            y: Array(xValues.length).fill(mean),  // Horizontal line for the mean
            mode: 'lines',
            name: 'Mean',
            line: { color: 'rgba(255, 0, 0, 1)', width: 2, dash: 'dash' }
        };

        // Create ticktext for y-axis (including actual data points)
        const yTickText = [
            `+ 3 SD (${(mean + 3 * sd).toFixed(2)})`,
            `+ 2 SD (${(mean + 2 * sd).toFixed(2)})`,
            `+ 1 SD (${(mean + 1 * sd).toFixed(2)})`,
            `Mean (${mean.toFixed(2)})`,
            `- 1 SD (${(mean - 1 * sd).toFixed(2)})`,
            `- 2 SD (${(mean - 2 * sd).toFixed(2)})`,
            `- 3 SD (${(mean - 3 * sd).toFixed(2)})`,
            // ...dataPoints.map(p => `${p.toFixed(2)}`)  // Add labels for actual data points
        ];

        // Calculate min and max for y-axis range
        const yMin = Math.min(mean - 3 * sd, ...dataPoints);
        const yMax = Math.max(mean + 3 * sd, ...dataPoints);

        // Layout configuration with y-axis range to ensure all values are visible
        const layout = {
            title: 'LJ Chart for ' + i_name,
            dragmode: 'pan',
            xaxis: {
                title: 'Date and Time',
            },
            yaxis: {
                title: 'Value',
                tickvals: yAxisPoints,
                ticktext: yTickText,
                range: [yMin, yMax],  // Ensure all values are displayed
                automargin: true,
                ticklabelposition: 'outside',
                pad: {
                    t: 10, r: 10, b: 10, l: 10
                }
            }
        };

        // Create the plot
        Plotly.newPlot('lineChart', [trace1, meanLine], layout);
    }



    // function lj_graph(result, date, mean) {
    //     // Date and time labels for x-axis
    //     $("#results").slideDown();

    //     xlabel = [];
    //     var xres = date.split(",");
    //     for (i = 0; i < xres.length; i++) {
    //         xlabel.push(xres[i]);
    //     }

    //     const timeLabels = xlabel;

    //     plots = [];
    //     var plotres = result.split(",");
    //     for (i = 0; i < plotres.length; i++) {
    //         plots.push(plotres[i]);
    //     }


    //     // Corresponding y-axis values
    //     const yValues = plots;

    //     // Mean value
    //     const meanValue = mean;

    //     // Custom plugin to draw a horizontal line for the mean value
    //     const meanLinePlugin = {
    //         id: 'meanLine',
    //         afterDatasetsDraw(chart) {
    //             const { ctx, chartArea: { top, right, bottom, left }, scales: { y } } = chart;
    //             ctx.save();
    //             ctx.strokeStyle = 'red';
    //             ctx.lineWidth = 2;
    //             ctx.setLineDash([5, 5]); // Dashed line
    //             ctx.beginPath();
    //             ctx.moveTo(left, y.getPixelForValue(meanValue));
    //             ctx.lineTo(right, y.getPixelForValue(meanValue));
    //             ctx.stroke();
    //             ctx.font = '14px Arial';
    //             ctx.fillStyle = 'red';
    //             ctx.fillText(`Mean: ${meanValue}`, left + 10, y.getPixelForValue(meanValue) - 10);
    //             ctx.restore();
    //         }
    //     };

    //     // Create the Chart.js line chart
    //     const ctx = document.getElementById('dateTimeChart').getContext('2d');
    //     const dateTimeChart = new Chart(ctx, {
    //         type: 'line',
    //         data: {
    //             labels: timeLabels,
    //             datasets: [{
    //                 label: 'Y-Axis Values',
    //                 data: yValues,
    //                 borderColor: 'rgba(75, 192, 192, 1)',
    //                 borderWidth: 2,
    //                 fill: false,
    //                 pointRadius: 5, tension: .3,
    //                 pointBackgroundColor: 'rgba(255, 99, 132, 1)'
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             plugins: {
    //                 title: {
    //                     display: true,
    //                     text: 'Date and Time vs Y-Axis Values with Mean'
    //                 }
    //             },
    //             scales: {
    //                 x: {
    //                     type: 'category',
    //                     title: {
    //                         display: true,
    //                         text: 'Date and Time'
    //                     }
    //                 },
    //                 y: {
    //                     title: {
    //                         display: true,
    //                         text: 'Y-Axis Values'
    //                     },
    //                     min: 260,
    //                     max: 290
    //                 }
    //             }
    //         },
    //         plugins: [meanLinePlugin] // Add the mean line plugin
    //     });
    // }

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