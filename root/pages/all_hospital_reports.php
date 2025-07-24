<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<br>
<center>
    <div class="btn-group">
        <input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
        <input type="text" class="datepicker" id="fdate" value="<?php echo date("Y-m-d"); ?>">
        <input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
        <input type="text" class="datepicker" id="tdate" value="<?php echo date("Y-m-d"); ?>">
    </div>
</center>
<div class="widget-box">
    <div class="widget-title">
        <ul class="nav nav-tabs">
            <li onclick="tab_data(1)" class="active" id="tabs1"><a data-toggle="tab" href="#tab1">Patient Demographics
                    Analysis</a></li>
            <!-- <li onclick="tab_data(2)" id="tabs2"><a data-toggle="tab" href="#tab2">Compasionate Care Reports</a>
            </li> -->
            <!-- <li onclick="tab_data(3)" id="tabs3"><a data-toggle="tab" href="#tab3">Scheme Utilization Reports</a></li> -->
            <!-- <li onclick="tab_data(4)" id="tabs4"><a data-toggle="tab" href="#tab4">Investigation Reports</a></li> -->
            <!-- <li onclick="tab_data(5)" id="tabs5"><a data-toggle="tab" href="#tab5">Ward Occupancy Reports</a></li> -->
            <li onclick="tab_data(6)" id="tabs6"><a data-toggle="tab" href="#tab5">Audit Reports</a></li>
            <!-- <li onclick="tab_data(7)" id="tabs7"><a data-toggle="tab" href="#tab5">Total Registrations</a></li> -->

        </ul>
    </div>


    <div class="widget-content tab-content" id="tab_data" style="width: 98%;">

    </div>

    <input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
    <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="border-radius:0;display:none">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="results"> </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="loader" style="margin-top:-10%;"></div>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script src="../js/plotly-latest.js"></script>
<script src="../jss/moment.js"></script>
<!-- Loader -->

<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>

    $(document).ready(function () {
        $("#loader").hide();

        $("#tabs1").click();
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            maxDate: '0',
            yearRange: "-150:+0",
            //defaultDate:'2000-01-01',
        });
        $(view_all_opd_report(2));
    });

    function tab_data(val) {
        $.post("pages/all_hospital_reports_set_page_ajax.php",
            {

                val: val,
                type: 1
            },
            function (data, status) {
                //$("#tab_data").html("data"+val);
                $("#tab_data").html(data);
                // alert(data);

            })
    }

    function view_all(typ) {
        $(".btn").removeClass("btn-inverse");
        $(".btn").addClass("btn-success");
        $("#btn" + typ).removeClass("btn-success");
        $("#btn" + typ).addClass("btn-inverse");
        $("#loader").show();
        $.post("pages/overall_hospital_reports_ajax.php",
            {
                type: typ,
                fdate: $("#fdate").val(),
                tdate: $("#tdate").val(),

            },
            function (data, status) {
                $("#loader").hide();
                $("#load_data").show().html(data);
            })
    }

    // function print_report(fdt, tdt, type) {
    //     var url = "";

    //     if (type == 1) {
    //         url = "pages/overall_registration_summary_print_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
    //     } else if (type == 2) {
    //         url = "pages/overall_registration_by_dept_print_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
    //     } else if (type == 3) {
    //         url = "pages/overall_registration_by_age_group_print_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
    //     } else if (type == 4) {
    //         url = "pages/overall_registration_by_doctor_print_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
    //     } else {
    //         return;
    //     }

    //     window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    // }

    // function print_scheme_report(fdt, tdt, type, s_typ) {
    //     var url = "";

    //     if (type == 1) {
    //         url = "pages/scheme_utilization_print_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type + "&free_pat=" + s_typ;
    //     }

    //     window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    // }

    // function print_report_opd(fdt, tdt, type) {
    //     var url = "";

    //     if (type == 1) {
    //         url = "pages/total_reg_by_department_opd_print_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
    //     } else if (type == 2) {
    //         url = "pages/total_reg_by_age_opd_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
    //     } else if (type == 3) {
    //         url = "pages/total_reg_by_doctors_opd_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
    //     } else {
    //         return;
    //     }

    //     window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    // }

    function print_inv_report(fdt, tdt, type) {
        var url = "";

        if (type == 1) {
            url = "pages/total_patient_audit_print.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
        } else if (type == 2) {
            url = "pages/total_parameter_wise_audit_print.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
        } else {
            return;
        }

        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    }


    function print_total_reg_report(fdt, tdt, type) {
        var url = "";

        if (type == 1) {
            url = "pages/total_registrations_print.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
        } else {
            return;
        }

        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    }



    // function print_ward_report(fdt, tdt, type) {
    //     var url = "";

    //     if (type == 1) {
    //         url = "pages/total_ward_occupancy_print_report.php?fdate=" + fdt + "&tdate=" + tdt + "&type=" + type;
    //     } else {
    //         return;
    //     }

    //     window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
    // }


    function view_all_scheme(typ) {
        $(".btn").removeClass("btn-inverse");
        $(".btn").addClass("btn-success");
        $("#btn" + typ).removeClass("btn-success");
        $("#btn" + typ).addClass("btn-inverse");
        $("#loader").show();
        $.post("pages/scheme_utilization_reports_ajax.php",
            {
                type: typ,
                fdate: $("#fdate").val(),
                tdate: $("#tdate").val(),
                free_typ: $("#free_patients").val(),

            },
            function (data, status) {
                $("#loader").hide();
                $("#load_data").show().html(data);
            })
    }

    function view_all_opd_report(typ) {
        $(".btn").removeClass("btn-inverse");
        $(".btn").addClass("btn-success");
        $("#btn" + typ).removeClass("btn-success");
        $("#btn" + typ).addClass("btn-inverse");
        $("#loader").show();
        $.post("pages/overall_opd_reg_reports_ajax.php",
            {
                type: typ,
                fdate: $("#fdate").val(),
                tdate: $("#tdate").val(),

            },
            function (data, status) {
                $("#loader").hide();
                $("#load_data").show().html(data);
            })
    }

    function view_inv_report(typ) {
        $(".btn").removeClass("btn-inverse");
        $(".btn").addClass("btn-success");
        $("#btn" + typ).removeClass("btn-success");
        $("#btn" + typ).addClass("btn-inverse");
        $("#loader").show();
        $.post("pages/overall_investigation_reports_ajax.php",
            {
                type: typ,
                fdate: $("#fdate").val(),
                tdate: $("#tdate").val(),

            },
            function (data, status) {
                $("#loader").hide();
                $("#load_data").show().html(data);
            })
    }


    function ward_occ(typ) {
        $(".btn").removeClass("btn-inverse");
        $(".btn").addClass("btn-success");
        $("#btn" + typ).removeClass("btn-success");
        $("#btn" + typ).addClass("btn-inverse");
        $("#loader").show();
        $.post("pages/overall_ward_reports_ajax.php",
            {
                type: typ,
                fdate: $("#fdate").val(),
                tdate: $("#tdate").val(),

            },
            function (data, status) {
                $("#loader").hide();
                $("#load_data").show().html(data);
            })
    }



    // function view_balance_xl() {
    //     var from = $("#txtfrom").val();
    //     var to = $("#txtto").val();
    //     var url = "pages/test_wise_collection_xls_report.php?from=" + from + "&to=" + to;
    //     newwindow = window.open(url, 'window', 'left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
    // }

    function audit_reports(typ) {
        $(".btn").removeClass("btn-inverse");
        $(".btn").addClass("btn-success");
        $("#btn" + typ).removeClass("btn-success");
        $("#btn" + typ).addClass("btn-inverse");
        $("#loader").show();
        $.post("pages/audit_reports_ajax.php",
            {
                type: typ,
                fdate: $("#fdate").val(),
                tdate: $("#tdate").val(),

            },
            function (data, status) {
                $("#loader").hide();
                $("#load_data").show().html(data);
            })
    }

    function total_reg_reports(typ) {
        $(".btn").removeClass("btn-inverse");
        $(".btn").addClass("btn-success");
        $("#btn" + typ).removeClass("btn-success");
        $("#btn" + typ).addClass("btn-inverse");
        $("#loader").show();
        $.post("pages/Total_registration_reports_ajax.php",
            {
                type: typ,
                fdate: $("#fdate").val(),
                tdate: $("#tdate").val(),

            },
            function (data, status) {
                $("#loader").hide();
                $("#load_data").show().html(data);
            })
    }





</script>
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>


<style>
    .pspan {
        display: block;
        width: 150px;
        margin-left: -2px;
    }

    .tab_list_table {
        max-height: 400px;
        overflow: scroll;
        overflow-x: hidden
    }

    #myModal {
        left: 30%;
        width: 80%;
        height: 400px;
    }

    .select2-container--open {
        z-index: 9999999
    }

    .div_size {
        display: inline-block;
        margin-top: 0px;
        width: 430px;
        margin-left: -10px;
    }

    .div_size .btn {
        margin-bottom: 10px;
    }

    .cke_textarea_inline {
        padding: 10px;
        height: 380px;
        overflow: auto;
        border: 1px solid gray;
        -webkit-appearance: textfield;
    }
</style>