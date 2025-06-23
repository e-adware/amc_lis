<?php
$branch_str = " AND `branch_id`='$p_info[branch_id]'";
$element_style = "display:none";
$branch_display = "display:none;";
if ($p_info["levelid"] == 1) {
    $branch_str = "";

    $branch_display = "display:none;";
    $branch_num = mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
    if ($branch_num > 1) {
        $branch_display = "display:;";
    }

    $dept_sel_dis = "";
} else {
    $branch_str = " AND branch_id='$p_info[branch_id]'";
    $branch_display = "display:none;";

    $dept_sel_dis = "disabled";
}

$branch_id = $p_info["branch_id"];
?>
<script src="../js/jquery.gritter.min.js"></script>
<script src="../js/bootbox.min.js"></script>
<link rel="stylesheet" href="../css/jquery.gritter.css" type="text/css" />
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span>
    </div>
</div>
<!--End-header-->
<div class="container-fluid">
    <center>
        <b>From</b>
        <input class="form-control datepicker" type="text" name="ex_fdate" id="ex_fdate"
            value="<?php echo date('Y-m-d'); ?>" readonly>
        <b>To</b>
        <input class="form-control datepicker" type="text" name="ex_tdate" id="ex_tdate"
            value="<?php echo date('Y-m-d'); ?>" readonly />
        <!-- <span class="side_name">Search</span>
        <input class="span2" type="text" id="exp_search" placeholder="Search Expense" style="margin-left: 60px;" onkeyup="expense_list()" autofocus> -->
        <select id="expense_id" class="span2" onchange="expense_list()">
            <option value="0">Select Expense</option>
            <?php
            $ledger_qry = mysqli_query($link, " SELECT * FROM `ledger_master` order by `ledger_name` ");
            while ($ledger = mysqli_fetch_array($ledger_qry)) {
                echo "<option value='$ledger[id]'>$ledger[ledger_name]</option>";
            }
            ?>
        </select>

        <select id="branch_id_main" class="span2" onchange="expense_list()" style="<?php echo $branch_display; ?>">
            <?php
            $qry = mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
            while ($data = mysqli_fetch_array($qry)) {
                if ($data["branch_id"] == $p_info["branch_id"]) {
                    $branch_sel = "selected";
                } else {
                    $branch_sel = "";
                }
                echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
            }
            ?>
        </select>
        <button class=" btn btn-new" onClick="expense_list()" style="margin-bottom: 10px;"><i class="icon-search"></i>
            Search</button>
        <button class="btn btn-primary" onClick="new_expense()" style="margin-bottom: 10px;"><i class="icon-edit"></i>
            New
            Expense</button>

    </center>
    <div id="doc_list"></div>
    <div id="doc_info"></div>
</div>

<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
$(document).ready(function() {
    $("#loader").hide();
    expense_list();
    datepicker();
});

function datepicker() {
    $(".datepicker").datepicker({

        dateFormat: 'yy-mm-dd',
        maxDate: '0',

    });
}

function save_exp() {
    var imp = $(".imp");
    var err = 0;
    for (var i = 0; i < imp.length; i++) {
        if ($(imp[i]).val().trim() == "" || $(imp[i]).val().trim() == "0") {
            $(imp[i]).css({
                'border': '1px solid red'
            })
            err++;
        } else {
            $(imp[i]).css({
                'border': '1px solid #ccc'
            })
        }
    }

    $.post("pages/daily_expense_data.php", {
            mode: $("#mode").val(),
            inv: $("#inv_no").val(),
            ledge: $("#ledge").val(),
            cheque_no: $("#cheque_no").val(),
            bank: $("#bank").val(),
            ex_date: $("#ex_date").val(),
            amount: $("#amount").val(),
            desc: $("#desc").val(),
            user: $("#user").text(),
            branch: $("#branch").val(),
            type: "save_exp",
        },
        function(data, status) {
            if (data == "success") {
                expense_list();
            }

        })


}

function new_expense() {
    $("#loader").show();
    $.post("pages/daily_expense_data.php", {
            type: "new_expense",
            user: $("#user").text(),

        },
        function(data, status) {
            $("#loader").hide();
            $("#doc_info").slideUp(300);
            $("#doc_list").slideDown(300).html(data);
        })
}

function expense_list() {
    $("#loader").show();
    $.post("pages/daily_expense_data.php", {
            type: "expense_list",
            expense_id: $("#expense_id").val(),
            ex_fdate: $("#ex_fdate").val(),
            ex_tdate: $("#ex_tdate").val(),
            branch_id: $("#branch_id_main").val(),
        },
        function(data, status) {
            $("#loader").hide();
            $("#doc_info").slideUp(300);
            $("#doc_list").slideDown(300).html(data);
        })
}

function load_details(id) {
    $("#loader").show();
    $.post("pages/daily_expense_data.php", {
            type: "expense_detail",
            ex_fdate: $("#ex_fdate").val(),
            ex_tdate: $("#ex_tdate").val(),
            ledge: id,
        },
        function(data, status) {
            $("#loader").hide();
            $("#doc_list").slideUp(300);
            $("#doc_info").slideDown(300).html(data);
        })
}

function load_ledge_details(exp_id) {
    $("#loader").show();
    $.post("pages/daily_expense_data.php", {
            type: "ledger_edit",
            exp_id: exp_id,
        },
        function(data, status) {
            $("#loader").hide();
            $("#doc_list").slideUp(300);
            $("#doc_info").slideDown(300).html(data);
        })

}

function update_exp(exp_id) {
    var imp = $(".imp");
    var err = 0;
    for (var i = 0; i < imp.length; i++) {
        if ($(imp[i]).val().trim() == "" || $(imp[i]).val().trim() == "0") {
            $(imp[i]).css({
                'border': '1px solid red'
            })
            err++;
        } else {
            $(imp[i]).css({
                'border': '1px solid #ccc'
            })
        }
    }

    $.post("pages/daily_expense_data.php", {
            mode: $("#mode").val(),
            inv: $("#inv_no").val(),
            ledge: $("#ledge").val(),
            cheque_no: $("#cheque_no").val(),
            bank: $("#bank").val(),
            ex_date: $("#ex_date").val(),
            amount: $("#amount").val(),
            desc: $("#desc").val(),
            user: $("#user").text(),
            type: "update_exp",
            exp_id: exp_id,
        },
        function(data, status) {
            if (data == "success") {
                expense_list();
            }

        })
}

function delete_exp(exp_id) {
    bootbox.confirm({
        message: 'Are you sure want to delete this expense?',
        buttons: {
            confirm: {
                label: 'Yes',
                className: 'btn-success'

            },
            cancel: {
                label: 'Cancel',
                className: 'btn-danger'
            }
        },
        callback: function(result) {

            if (result == true) {
                $.post("pages/daily_expense_data.php", {
                        type: "delete_exp",
                        exp_id: exp_id,
                    },
                    function(data, status) {
                        var msg = JSON.parse(data);
                        gritAlert(msg['msg'], msg['response']);
                        expense_list();

                    })
            }
        }
    });
}

function gritAlert(msg, n) {
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

function daily_report_print(fdate, tdate) {
    var exp = btoa($("#expense_id").val());
    var url = "pages/daily_expense_print.php?fdate=" + fdate + "&tdate=" + tdate + "&exp=" + exp;
    window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}
</script>
<style>
.side_name {
    border: 1px solid #ddd;
    background-color: #fff;
    padding: 4px;
    position: absolute;
    font-weight: bold;
}

#doc_list {
    max-height: 400px;
    overflow-y: scroll;
}

.modal.fade.in {
    top: 0%;
}

.modal_main {
    width: 90%;
    left: 22%;
    z-index: 999 !important;
}

.modal-backdrop {
    z-index: 990 !important;
}

.required {
    color: #ff0000;
}
</style>