<div id="content-header">
    <div class="header_div"> <span class="header">
            <?php echo $menu_info["par_name"]; ?>
        </span></div>
</div>
<div class="container-fluid">
    <table class="table table-condensed table-head ">
        <tr>
            <th class="head" colspan="3">
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <label for="from_date">From:</label>
                    <input id="from_date" type="text" class="datepicker span2" value="<?php echo date('Y-m-d'); ?>"
                        readonly />
                    <label for="to_date">To:</label>
                    <input id="to_date" type="text" class="datepicker span2" value="<?php echo date('Y-m-d'); ?>"
                        readonly />
                    <select class="span2" id="sel_instrument">
                        <?php
                        $all_ins_qry = mysqli_query($link, "SELECT DISTINCT(`instrument_id`) FROM `testresults`");
                        while ($all_ins = mysqli_fetch_array($all_ins_qry)) {
                            $i_name = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `lab_instrument_master` WHERE `id` = '$all_ins[instrument_id]' "));

                            echo "<option value='$all_ins[instrument_id]'>$i_name[name]</option>";
                            if ($all_ins['instrument_id'] == 0) {
                                echo "<option value='999'>TECHNICIAN</option>";

                            }
                        }
                        ?>
                    </select>


                </div>
            </th>
        </tr>
        <tr>

            <th>
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <select id="sel_time" name="time_slot" class="span2">
                        <option value="0">SELECT TIME</option>
                        <?php
                        for ($i = 0; $i < 24; $i++) {
                            $start = str_pad($i, 2, "0", STR_PAD_LEFT) . ":00";
                            $end = str_pad($i, 2, "0", STR_PAD_LEFT) . ":59";
                            $label = "$start - $end";
                            echo "<option value=\"$start@@$end\">$label</option>";
                        }
                        ?>
                    </select>
                    <select id="sel_ward" class="span2">
                        <option value="0">SELECT WARD</option>
                        <?php
                        $ward_qry = mysqli_query($link, "SELECT DISTINCT(wardName) FROM `uhid_and_opdid`");
                        while ($ward = mysqli_fetch_array($ward_qry)) {
                            echo "<option value='$ward[wardName]'>$ward[wardName]</option>";
                        }
                        ?>
                    </select>
                    <select id="sel_priority" class="span2">
                        <option value="0">SELECT PRIORITY</option>
                        <option value="1">ROUTINE</option>
                        <option value="2">EMERGENCY</option>

                    </select>
                    <select class="span3 sel2" id="sel_test">
                        <option value="0">All Tests</option>
                        <?php
                        $all_tests_qry = mysqli_query($link, "SELECT `testid`, `testname` FROM `testmaster` ORDER BY `testname`");
                        while ($all_test = mysqli_fetch_array($all_tests_qry)) {
                            echo "<option value='$all_test[testid]'>$all_test[testname]</option>";
                        }
                        ?>
                    </select>
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
        $.post("pages/test_volume_details_data.php",
            {
                type: "load_data",
                to_date: $("#to_date").val(),
                from_date: $("#from_date").val(),
                sel_test: $("#sel_test").val(),
                instrument: $("#sel_instrument").val(),
                time_period: $("#sel_time").val(),
                ward: $("#sel_ward").val(),
                priority: $("#sel_priority").val(),

            },
            function (data, status) {
                $("#loader").hide();

                $("#load_data").html(data);
            })
    }
</script>