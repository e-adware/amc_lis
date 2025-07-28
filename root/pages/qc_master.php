<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
    <div class="row" id="qc_main_div">
        <div class="span5">
            <table class="table table-striped table-bordered table-condensed">
                <tr>
                    <th>Instrument Name:</th>
                    <td><?php $ins_qry = mysqli_query($link, "SELECT * FROM `lab_instrument_master` WHERE (`name` != NULL OR `name` != '') ORDER BY `id`");
                    ?>
                        <select id="instrument_name" class="form-control">
                            <?php
                            $n = 1;
                            while ($ins = mysqli_fetch_array($ins_qry)) {
                                echo "<option value='$ins[id]'>" . $ins['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Fluid Name:</th>
                    <td><?php $ins_qry = mysqli_query($link, "SELECT * FROM `qc_fluid`");
                    ?>
                        <select id="fluid_name" class="form-control">
                            <?php
                            while ($ins = mysqli_fetch_array($ins_qry)) {
                                echo "<option value='" . $ins['id'] . "'>" . $ins['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>QC Name: </th>
                    <td>
                        <input type="hidden" id="qc_id" value='0' />
                        <input type="text" id="qc_name" />

                    </td>
                </tr>
                <tr>
                    <th>Sample ID: </th>
                    <td><input type="text" id="qc_sample_name" /></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center"><button class="btn-mini btn btn-success"
                            onclick="save_qc_name()">Save</button>
                        <button class="btn-mini btn btn-warning" onclick="clearfield()">Reset</button>
                    </td>
                </tr>
            </table>

        </div>
        <div class="span5">
            <div id="qc_list"></div>
        </div>
    </div>
    <div class="row" id="qc_edit_div" style="display:none ; ">
        <div class="span12" id="qc_edit">

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
</style>
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<script src="../js/sweetalert2.all.min.js"></script>


<script>
    $(document).ready(function () {
        load_qc();

    });

    function show_selected_test() {

    }

    function hid_div(e) {

    }


    $(document).on('keydown', function (e) {
        if (e.key === "Escape" || e.keyCode === 27) {
            load_qc_home();
        }
    });

    function load_qc() {
        $.post("pages/qc_master_data.php",
            {
                type: 'load_list',
            },
            function (data, status) {
                $("#qc_list").html(data);
            })
    }
    function save_qc_name() {
        instrument = $("#instrument_name");
        fluid = $("#fluid_name");
        qc_name = $("#qc_name");
        if (qc_name.val() == '') {
            alert("Please enter QC Name");
            return false;
            qcname.focus();
        }
        if (instrument.val() == '0') {
            alert("Please Select Instrument");
            return false;
            instrument.focus();
        }
        if (fluid.val() == '0') {
            alert("Please Select Fluid");
            return false;
            fluid.focus();
        }
        else {
            $.post("pages/qc_master_data.php",
                {
                    type: 'save_qc_name',
                    instrument: instrument.val(),
                    fluid: fluid.val(),
                    qc_sample_name: $("#qc_sample_name").val(),
                    qc_name: qc_name.val(),
                    qc_id: $("#qc_id").val(),
                },
                function (data, status) {
                    if (data == 1)
                        alertmsg("QC Name saved successfully", 1);
                    else
                        alertmsg("Error Occured", 0);
                    load_qc();
                    clearfield();
                })
        }
    }

    function edit_qc_name(id) {
        $.post("pages/qc_master_data.php",
            {
                type: 'edit_qc_name',
                id: id,
            },
            function (data, status) {
                var val = data.split("@@");
                $("#qc_id").val(val[0]);
                $("#qc_name").val(val[1]);
                $("#qc_sample_name").val(val[2]);
                $("#instrument_name").val(val[3]);
                $("#fluid_name").val(val[4]);
            })
    }

    function qc_param_edit(id) {
        $("#qc_main_div").slideUp();

        $.post("pages/qc_master_data.php",
            {
                type: 'edit_qc_param',
                id: id,
            },
            function (data, status) {
                $("#qc_edit_div").slideDown();
                $("#qc_edit_div").css("display", "block");
                $("#qc_edit").html(data);
                load_reagent_list(id);
            })
    }

    function filter_reagent_list() {
        $("#reagent_search").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    }

    function load_reagent_list(qc_id) {
        $.post("pages/qc_master_data.php",
            {
                type: 'load_reagent_list',
                qc_id: qc_id,
                src_text: $("#reagent_search").val(),

            },
            function (data, status) {
                $("#reagent_list").html(data);
            })
    }

    function reload_test_master(qc_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete all the previous mappings and saved results...",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reload it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("pages/qc_master_data.php", {
                    type: 'reload_test_master',
                    qc_id: qc_id
                }, function (data, status) {
                    load_reagent_list(qc_id);
                    console.log(data);
                    Swal.fire('Reloaded!', 'Test master has been reloaded.', 'success');
                });
            }
        });
    }



    function save_qc_reagent(qc_id) {

        var checkboxValues = [];
        $('input[name=reagents_check]:checked').map(function () {
            checkboxValues.push($(this).val());
        });

        $.post("pages/qc_master_data.php",
            {
                type: 'save_qc_mapping',
                testids: checkboxValues,
                qcid: qc_id,

            },
            function (data, status) {
                alertmsg(data, 1);
            })
    }
    function load_qc_home() {
        $("#qc_main_div").slideDown();
        $("#qc_edit_div").slideUp();
        $("#qc_edit_div").css("display", "none");

    }

    function clearfield() {
        $("#qc_id").val('0');
        $("#qc_name").val('');
        $("#qc_sample_name").val('');
        $("#instrument_name").val('0');
        $("#fluid_name").val('0');

    }
    function alertmsg(msg, n) {
        $.gritter.add({
            //title:	'Normal notification',
            text: '<h5 style="text-align:center;">' + msg + '</h5>',
            time: 1000,
            sticky: false
        });
        if (n > 0) {
            $(".gritter-item").css("background", "#237438");
        }
    }
</script>