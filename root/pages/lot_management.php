<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
    <div class="row" id="lot_main_div">
        <div class="span5">
            <table class="table table-striped table-bordered table-condensed">
                <tr>
                    <th>QC Name: </th>
                    <td>
                        <select class="form-control" id="qc_id">
                            <option value="0">Select QC Name</option>
                            <?php
                            $qc_qry = mysqli_query($link, "SELECT * FROM `qc_master`");
                            while ($qc = mysqli_fetch_array($qc_qry)) { ?>
                                <option value="<?php echo $qc['qc_id']; ?>"><?php echo $qc['qc_name']; ?></option>
                                <?php
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Lot No.:</th>
                    <td><input type="text" id="lot_no" class="form-control" /></td>
                </tr>
                <tr>
                    <th>Control Name:</th>
                    <td><input type="text" class="form-control" id="control" /></td>
                </tr>
                <tr>
                    <th>Expiry Date:</th>
                    <td><input readonly type="text" id="exp_date" value="<?php echo date('Y-m-d'); ?>" class="date"
                            class="form-control" /></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center"><button class="btn btn-success"
                            onclick="save_lot()">Save</button>
                        <button class="btn btn-warning" onclick="clearfield()">Reset</button>
                    </td>
                </tr>
            </table>
        </div>
        <div class="span5">
            <div id="lot_list"></div>
        </div>
    </div>
    <div class="row" id="lot_baseline_div" style="display:none ; ">
        <div class="span12" id="baseline_edit">

        </div>
    </div>
</div>
<style>
    .container-fluid {
        margin: 10px;
        padding: 5px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
    }

    .search {
        margin-left: 30px;
        ;
    }

    .lot_inp {
        width: 100px;
    }
</style>
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
    $(document).ready(function () {
        $(".date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: "-100:+10",
        });
        load_lot();
    });

    function show_selected_test() {

    }

    function hid_div(e) {

    }


    $(document).on('keydown', function (e) {
        if (e.key === "Escape" || e.keyCode === 27) {
            load_lot_home();
        }
    });

    function save_lot() {
        qc_id = $("#qc_id");
        lot_no = $("#lot_no");
        control = $("#control");
        exp_date = $("#exp_date");
        if (qc_id.val() == '0') {
            qc_id.focus();
            alertmsg("Select QC", 0);
            return false();
        } if (lot_no.val() == '') {
            lot_no.focus();
            alertmsg("Enter Lot Number", 0);
            return false();
        } else {
            $.post("pages/lot_management_data.php",
                {
                    type: 'save_lot',
                    qc_id: qc_id.val(),
                    lot_no: lot_no.val(),
                    control: control.val(),
                    exp_date: exp_date.val(),

                },
                function (data, status) {
                    if (data == 1)
                        alertmsg("Lot Name saved successfully", 1);
                    else
                        alertmsg("Error Occured", 0);
                    clearfield();
                    load_lot();
                });
        }
    }

    function load_lot() {
        $.post("pages/lot_management_data.php",
            {
                type: 'load_lot',
            },
            function (data, status) {
                $("#lot_list").html(data);
            });
    }

    function edit_lot(id) {
        $.post("pages/lot_management_data.php",
            {
                type: 'edit_lot',
                id: id,

            },
            function (data, status) {
                var res = data.split("@@");
                $("#qc_id").val(res[0]);
                $("#lot_no").val(res[1]);
                $("#control").val(res[2]);
                $("#exp_date").val(res[3]);
                // alert(data);
            });
    }

    function base_def(id) {
        $("#lot_main_div").slideUp();

        $.post("pages/lot_management_data.php",
            {
                type: 'edit_baseline',
                id: id,
            },
            function (data, status) {
                $("#lot_baseline_div").slideDown();
                $("#lot_baseline_div").css("display", "block");
                $("#baseline_edit").html(data);
                load_baseline_data(id);
            });
    }

    function load_baseline_data(id) {
        $.post("pages/lot_management_data.php",
            {
                type: 'load_baseline_list',
                id: id,

            },
            function (data, status) {
                $("#reagent_list").html(data);
            });
    }

    function load_lot_home() {
        $("#lot_main_div").slideDown();
        $("#lot_baseline_div").slideUp();
        $("#lot_baseline_div").css("display", "none");
    }

    function calculate(id, e, val) {
        var lower = $("#ind_lower_" + id).val();
        var upper = $("#ind_upper_" + id).val();
        var unicode = e.keyCode ? e.keyCode : e.charCode;
        if (lower && upper == '' && unicode == 13) {
            $("#ind_upper_" + id).focus();
        }
        if (upper && lower == '' && unicode == 13) {
            $("#ind_lower_" + id).focus();
        }

        if (lower != '' && upper != '') {
            mean_result = ((parseFloat(lower) + parseFloat(upper)) / 2);
            sd_result = (parseFloat(upper) - parseFloat(lower)) / 6;
            $("#ind_sd_" + id).val(sd_result.toFixed(2));
            $("#ind_mean_" + id).val(mean_result.toFixed(2));
            if (mean_result && sd_result) {
                cv_result = (sd_result / mean_result) * 100;
                $("#ind_cv_" + id).val(cv_result.toFixed(2));
                if (lower != '' && upper != '' && $("#ind_sd_" + id).val() != '' && $("#ind_mean_" + id).val() != '') {

                    if (unicode == 13) {

                        save_baseline(id);
                    }
                }
            }
        }
    }

    function save_baseline(indice_id) {
        $(".row_" + indice_id).css('border', '1px solid green');
        $(".row_" + indice_id).css('background', '#BDEFBD');
        $.post("pages/lot_management_data.php",
            {
                type: 'save_baseline',
                indice_id: indice_id,
                lot_id: $("#lot_id").val(),
                lower: $("#ind_lower_" + indice_id).val(),
                upper: $("#ind_upper_" + indice_id).val(),
                mean: $("#ind_mean_" + indice_id).val(),
                sd: $("#ind_sd_" + indice_id).val(),
                cv: $("#ind_cv_" + indice_id).val(),

            },
            function (data, status) {
                if (data == '1') {
                    alertmsg("Saved", 1)
                } else {
                    alertmsg("Error Occured", 0);
                }
            });

    }

    function clearfield() {
        var cd = (new Date()).toISOString().split('T')[0];
        $("#qc_id").val('0');
        $("#lot_no").val('');
        $("#control").val('');
        $("#exp_date").val(cd);
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