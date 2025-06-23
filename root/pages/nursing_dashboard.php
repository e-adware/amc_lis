<?php
$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$show=base64_decode($_GET['show']);

// Delete Patient Temp Bed
mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");

$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));

$branch_id=$pat_reg["branch_id"];
$centreno=$pat_reg["center_no"];

$reg_date=$pat_reg["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

//$adm=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));

$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));

$pat_doc=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
if($pat_doc)
{
	$pat_doc_trans=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_doc_transfer` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `attend_doc`='$pat_doc[attend_doc]' AND `status`=1 "));
	if(!$pat_doc_trans)
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$pat_doc[attend_doc]','1','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]')");
	}
}

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

// Bill Details Start
$serv_sum=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
$bill_amount=$serv_sum["tot"];

$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));

$ward_info=mysqli_fetch_array(mysqli_query($link, " SELECT b.`name` FROM `ipd_pat_bed_details` a,ward_master b WHERE a.`patient_id`='$uhid' and a.ipd_id='$ipd' and a.ward_id =b.ward_id  "));

$already_paid      =$check_paid["paid"];
$already_refund    =$check_paid["refund"];

$paid_amount=$already_paid-$already_refund;

$balance_amount=$bill_amount-$paid_amount;
// Bill Details End
?>

<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Nursing Dashboard</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
    <span style="float:right;">
        <button class="btn btn-back" id="back_btn" onclick="window.location='processing.php?param=36'"><i
                class="icon-backward"></i> Back</button>
    </span>
    <span style="float:right;">
        <button class="btn btn-print" id="admission_sheet_btn" onclick="print_regd_receit(1)"><i class="icon-print"></i>
            Admission Sheet</button>
    </span>
    <?php
	if($p_info["levelid"]!=5)
	{
?>
    <select id="pat_centreno" onchange="pat_centreno_change()" style="float:right;display:none;"
        <?php if($centreno=="C104"){ echo "disabled"; } ?>>
        <?php
		$qry=mysqli_query($link, "SELECT `centreno`,`centrename` FROM `centremaster` WHERE `centreno` IN('C100','C101','C104')");
		while($data=mysqli_fetch_array($qry))
		{
			if($data["centreno"]=="C100" || $data["centreno"]=="C101"){ $centre_dis=""; }else{ $centre_dis="disabled"; }
			if($centreno==$data["centreno"]){ $sel="selected"; }else{ $sel=""; }
			echo "<option value='$data[centreno]' $sel $centre_dis>$data[centrename]</option>";
		}
?>
    </select>
    <?php
	}
?>
    <table class="table table-condensed table-bordered" style="background: snow">
        <tr>
            <th>UNIT NO.</th>
            <th><?php echo $prefix_det["prefix"]; ?></th>
            <th>Name</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Admitted On</th>
            <th>Admitted Under</th>
        </tr>
        <tr>
            <td><?php echo $pat_info['patient_id'];?></td>
            <td><?php echo $ipd;?></td>
            <td><?php echo $pat_info['name'];?></td>
            <td><?php echo $age;?></td>
            <td><?php echo $pat_info['sex'];?></td>
            <td><?php echo date("d-M-Y",strtotime($pat_reg['date']));?>
                <?php echo date("h:i A",strtotime($pat_reg['time']));?></td>
            <td><?php echo $doc['Name'];?></td>
        </tr>
        <tr>
            <th>Bill Amount</th>
            <th>Paid Amount</th>
            <th>Credit Amount</th>
            <th>Ward</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <td><?php echo number_format($bill_amount,2); ?></td>
            <td><?php echo number_format($paid_amount,2); ?></td>
            <td><?php echo number_format($balance_amount,2); ?></td>
            <td><?php echo $ward_info['name']; ?></td>

            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
    <input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
    <input type="text" id="branch_id" value="<?php echo $branch_id;?>" style="display:none;" />
    <input type="text" id="show" value="<?php echo $show;?>" style="display:none;" />
    <input type="hidden" id="chk_val" value="0" />
    <input type="hidden" id="chk_val1" value="0" />
    <div class="" style="">
        <div class="accordion" id="collapse-group">
            <?php
			if($p_info["levelid"]==5)
			{
				$accordian_qry=mysqli_query($link, "SELECT `accordian_id`, `accordian_name` FROM `nursing_dashboard_menu` WHERE `dashboard_id`=2 AND `access_doc`=1 AND `status`=0 ORDER BY `sequence` ASC");
			}
			else
			{
				$accordian_qry=mysqli_query($link, "SELECT `accordian_id`, `accordian_name` FROM `nursing_dashboard_menu` WHERE `dashboard_id`=2 AND `status`=0 ORDER BY `sequence` ASC");
			}
			while($accordian_info=mysqli_fetch_array($accordian_qry))
			{
				$display=0;
				if($pat_info["sex"]!="Female" && $accordian_info["accordian_id"]==16)
				{
					$display=1;
				}
				
				if($display==0)
				{
					$cls_name="";
					if($accordian_info["accordian_id"]!=6)
					{
						//$cls_name.=" non_bed";
					}
					if($accordian_info["accordian_id"]!=18)
					{
						$cls_name.=" non_dis_req";
					}
					if($accordian_info["accordian_id"]==13)
					{
						$cls_name.="";
					}
		?>
            <div class="accordion-group widget-box" style="display:;">
                <div class="accordion-heading">
                    <div class="widget-title">
                        <a data-parent="#collapse-group"
                            id="collapse_data<?php echo $accordian_info["accordian_id"]; ?>"
                            href="#collapse<?php echo $accordian_info["accordian_id"]; ?>" data-toggle="collapse"
                            onclick="show_icon(<?php echo $accordian_info["accordian_id"]; ?>)"
                            class="<?php echo $cls_name; ?>">
                            <span class="icon" style="width:90%;"><b
                                    style="padding:10px;font-size:16px;"><?php echo $accordian_info["accordian_name"]; ?></b><i
                                    class="icon-arrow-down"
                                    id="ard<?php echo $accordian_info["accordian_id"]; ?>"></i><i class="icon-arrow-up"
                                    id="aru<?php echo $accordian_info["accordian_id"]; ?>"
                                    style="display:none;"></i></span>
                            <span class="text-right" style="padding:10px;font-size:18px;">
                                <span class="iconp" id="plus_sign<?php echo $accordian_info["accordian_id"]; ?>"
                                    style="float:right;"><i class="icon-plus"></i></span>
                                <span class="iconm" id="minus_sign<?php echo $accordian_info["accordian_id"]; ?>"
                                    style="float:right;display:none;"><i class="icon-minus"></i></span>
                            </span>
                        </a>
                        <span class="icon" id="collapse_none<?php echo $accordian_info["accordian_id"]; ?>"
                            style="width:90%;display:none;cursor:not-allowed;"><b
                                style="padding:10px;font-size:16px;"><?php echo $accordian_info["accordian_name"]; ?></b>
                    </div>
                </div>
                <div class="accordion-body collapse" id="collapse<?php echo $accordian_info["accordian_id"]; ?>"
                    style="height:0px;max-height:700px;overflow-y:scroll;">
                    <div class="widget-content hidden_div"
                        id="accordian_data_load<?php echo $accordian_info["accordian_id"]; ?>" style="display:none;">

                    </div>
                </div>
            </div>
            <?php
				}
			}
		?>
        </div>
    </div>

    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#bed_modal" id="bed_modal_btn"
        style="display:none"></button>
    <div id="bed_modal" class="modal fade" role="dialog" style="border-radius:0;display:none">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Select Bed</h4>
                </div>
                <div class="modal-body">
                    <div id="bed_modal_data"> </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-search" style="float:left;">Available</button>
                    <button class="btn btn-danger" style="float:left;">Occupied</button>
                    <button class="btn btn-excel" style="float:left;">Selected</button>
                    <button class="btn btn-print" style="float:left;">Other Selected</button>
                    <button class="btn btn-reset" style="float:left;">Blocked</button>
                    <button class="btn btn-back" style="float:left;">Guardian Occupied</button>
                    <button type="button" id="modal_close_btn" class="btn btn-close" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--modal-->
    <a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
    <div id="myAlert" class="modal modal-lg fade">
        <div class="modal-body">
            <p id="add_opt">

            </p>
        </div>
        <div class="modal-footer">
            <a data-dismiss="modal" onclick="save()" class="btn btn-primary" href="#">Save</a>
            <a data-dismiss="modal" onclick="clearr()" class="btn btn-info" href="#">Cancel</a>
        </div>
    </div>
    <!--modal end-->
    <!--modal-->
    <a href="#myAlert1" data-toggle="modal" id="dl1" class="btn" style="display:none;">A</a>
    <div id="myAlert1" class="modal fade">
        <div class="modal-body">
            <div id="tests_lst" style="height: 460px;display:none;">

            </div>
        </div>
        <div class="modal-footer">
            <center>
                <button class="btn btn-save" onclick="save_test()"><i class="icon-save"></i> Save</button>
                <button class="btn btn-close" id="modal_test_close_btn" data-dismiss="modal"><i class="icon-off"></i>
                    Close</button>
            </center>
        </div>
    </div>
    <!--modal end-->
    <!--modal-->
    <input type="button" data-toggle="modal" data-target="#note_mod" id="nt_btn" style="display:none" />
    <input type="text" id="modtxt" value="0" style="display:none" />
    <div class="modal fade" id="note_mod" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="border-radius:0;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="bootbox-close-button close" data-dismiss="modal"
                        aria-hidden="true"><b>x</b></button>
                    <div id="res">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal end-->
    <!--modal-->
    <input type="button" data-toggle="modal" data-target="#repmodal" id="rep" style="display:none" />
    <input type="text" id="modtxt" value="0" style="display:none" />
    <div class="modal fade" id="repmodal" role="dialog" aria-labelledby="repmodalLabel" aria-hidden="true"
        style="border-radius:0;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="bootbox-close-button close" data-dismiss="modal"
                        aria-hidden="true"><b>x</b></button>
                    <div id="result">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal end-->
    <!--modal-->
    <input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
    <input type="text" id="modtxt" value="0" style="display:none" />
    <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="border-radius:0;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn btn-mini btn-danger" style="float:right;" data-dismiss="modal"
                        aria-hidden="true"><b>Close</b></button>
                    <div id="results">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal end-->
    <!--modal-->
    <input type="button" data-toggle="modal" data-target="#myModal_med" id="med_mod" style="display:none" />
    <input type="text" id="modtxt" value="0" style="display:none" />
    <div class="modal fade" id="myModal_med" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="border-radius:0;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="bootbox-close-button close"
                        onclick="$('#med_list').css('height','100px')" data-dismiss="modal"
                        aria-hidden="true"><b>x</b></button>
                    <div id="med_list" style="">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" id="ins_med" onclick="insert_medi();$('#med_list').css('height','100px')"
                    style="display:none;" class="btn btn-primary" href="#">Save</a>
                <a data-dismiss="modal" onclick="$('#med_list').css('height','100px')" class="btn btn-info"
                    href="#">Cancel</a>
            </div>
        </div>
    </div>
    <!--modal end-->
    <!--modal-->
    <input type="button" data-toggle="modal" data-target="#myModal_post" id="med_post" style="display:none" />
    <input type="text" id="modtxt" value="0" style="display:none" />
    <div class="modal fade" id="myModal_post" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="border-radius:0;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="bootbox-close-button close"
                        onclick="$('#med_list_post').css('height','100px')" data-dismiss="modal"
                        aria-hidden="true"><b>x</b></button>
                    <div id="med_list_post" style="">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" id="ins_med_post"
                    onclick="insert_medi_post();$('#med_list_post').css('height','100px')" style="display:none;"
                    class="btn btn-primary" href="#">Save</a>
                <a data-dismiss="modal" onclick="$('#med_list_post').css('height','100px')" class="btn btn-info"
                    href="#">Cancel</a>
            </div>
        </div>
    </div>
    <!--modal end-->
    <!--modal-->
    <a href="#medplan" data-toggle="modal" id="med_upd" class="btn" style="display:none;">A</a>
    <div id="medplan" class="modal modal-lg fade">
        <div class="modal-body">
            <div id="upd_med_plan_det">

            </div>
        </div>
    </div>
    <!--modal end-->
    <!--modal[Investigation Entry]-->
    <input type="button" data-toggle="modal" data-target="#myModal_inv_entry" id="inv_entry" style="display:none" />
    <input type="text" id="modtxt" value="0" style="display:none" />
    <div class="modal fade" id="myModal_inv_entry" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="border-radius:0;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="bootbox-close-button close"
                        onclick="$('#med_list_post').css('height','100px')" data-dismiss="modal"
                        aria-hidden="true"><b>x</b></button>
                    <div id="inv_entry_mod" style="">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" id="ins_med_post"
                    onclick="insert_medi_post();$('#med_list_post').css('height','100px')" style="display:none;"
                    class="btn btn-primary" href="#">Save</a>
                <a data-dismiss="modal" onclick="$('#med_list_post').css('height','100px')" class="btn btn-info"
                    href="#">Cancel</a>
            </div>
        </div>
    </div>
    <!--modal end-->
    <!--modal for delete-->
    <a href="#del_modal" data-toggle="modal" id="del_mod" class="btn" style="display:none;">A</a>
    <div id="del_modal" class="modal fade">
        <div class="modal-body">
            <input type="text" id="idl" style="display:none;" />
            <p id="msgtext"></p>
        </div>
        <div class="modal-footer">
            <a data-dismiss="modal" onclick="admit_pat()" class="btn btn-primary" href="#">Confirm</a>
            <a data-dismiss="modal" onclick="" class="btn btn-info" href="#">Cancel</a>
        </div>
    </div>
    <!--modal end-->
</div>
<!-- Edit Bed -->
<input type="hidden" value="0" id="ipd_pat_edit_bed">

<div id="loader" style="display:none;position:fixed;top:50%;left:50%;z-index:9999;"></div>
<div id="gter" class="gritter-item" style="display:none;width:200px;">
    <div class="gritter-close" style="display:block;" onclick="$('.a').removeClass('clk');$('#gter').fadeOut(500)">
    </div>
    <span class="gt-title" style="font-size: 12px;font-family: verdana;font-weight: bold;padding-left: 10px;">Medicine
        Administor</span>
    <p id='fol_med' style="padding:6px;font-size:12px;"></p>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../js/jquery.gritter.min.js"></script>
<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script src="../jss/globalFunctions.js"></script>

<script>
$(document).ready(function() {
    $(document).on('keyup', ".numericfloat", function() {
        $(this).val(function(_, val) {
            if (val == 0) {
                return val;
            } else if (val == ".") {
                return "0.";
            } else {
                var n = val.length;
                var numex = /^[0-9.]+$/;
                if (val[n - 1].match(numex)) {
                    return number;
                } else {
                    val = val.slice(0, n - 1);
                    return val;
                }
            }
        });
    });

    if (parseInt($("#show").val()) > 0) {
        $("#collapse_data2").click();
        //show_icon(2);
    }
    $(document).mouseup(function(e) {
        var container = $("#gter");
        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
            $('.a').removeClass('clk');
        }
    });
    load_bed_det();

    setTimeout(function() {
        add_auto_charge();
    }, 100);

    setTimeout(function() {
        if ($("#lavel_id").val() == 5) {
            $("#collapse_data13").click();
        }
    }, 200);
});

function pat_centreno_change() {
    $("#loader").show();
    $.post("pages/nursing_dashboard_data.php", {
            type: "pat_centreno_change",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            centreno: $("#pat_centreno").val(),
        },
        function(data, status) {
            $("#loader").hide();
            bootbox.dialog({
                message: "<h4>" + data + "</h4> "
            });
            setTimeout(function() {
                bootbox.hideAll();
            }, 2000);
        })
}

function add_auto_charge() {
    //return false;
    $("#loader").show();
    $.post("pages/service_entry_data.php", {
            type: "add_auto_charge",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
        },
        function(data, status) {
            $("#loader").hide();
        })
}

function load_bed_det() {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "load_bed_det",
        },
        function(data, status) {
            if (data == 0) {
                setTimeout(function() {
                    $(".non_bed").hide();
                }, 100);

                $(".non_bed").prop("disabled", true);
            } else {
                setTimeout(function() {
                    $(".non_bed").show();
                }, 100);

                $(".non_bed").prop("disabled", false);

                discharge_request_det();
            }
        })
}

function discharge_request_det() {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "pat_ipd_discharge_request_det",
        },
        function(data, status) {
            if (data == 1) {
                setTimeout(function() {
                    $(".non_dis_req").hide();
                }, 100);

                $(".non_dis_req").prop("disabled", true);
            } else {
                setTimeout(function() {
                    $(".non_dis_req").show();
                }, 100);

                $(".non_dis_req").prop("disabled", false);
            }
        })
}

function show_icon(i) {
    $(".hidden_div").fadeOut();
    $("#gter").fadeOut();
    $(".iconp").show();
    $(".iconm").hide();
    $(".icon-arrow-down").show();
    $(".icon-arrow-up").hide();
    if ($('#accordian_data_load' + i + ':visible').length) {
        $("#accordian_data_load" + i).fadeOut();
        $("#plus_sign" + i).show();
        $("#minus_sign" + i).hide();
        $("#ard" + i).show();
        $("#aru" + i).hide();
    } else {
        $("#accordian_data_load" + i).fadeIn();
        $("#plus_sign" + i).hide();
        $("#minus_sign" + i).show();
        $("#ard" + i).hide();
        $("#aru" + i).show();
        if (i == 1) {
            diagnosis();
        } else if (i == 2) {
            medication();
            //$("html,body").animate({scrollTop: '200px'},800);
        } else if (i == 3) {
            investigation();
            //setTimeout(function(){ $("#test").focus()},500);
            //$("html,body").animate({scrollTop: '380px'},800);
        } else if (i == 4) {
            vital();
            //$("html,body").animate({scrollTop: '200px'},900);
        } else if (i == 5) {
            ip_consult();
            //$("html,body").animate({scrollTop: '460px'},800);
        } else if (i == 6) {
            room_status();
            //$("html,body").animate({scrollTop: '370px'},800);
        } else if (i == 7) {
            equipment();
            //$("html,body").animate({scrollTop: '480px'},800);
        } else if (i == 8) {
            gen_consumable();
            //$("html,body").animate({scrollTop: '500px'},800);
        } else if (i == 9) {
            med_admin();
            //$("html,body").animate({scrollTop: '350px'},800);
        } else if (i == 10) {
            chief_complain();
            //$("html,body").animate({scrollTop: '150px'},800);
        } else if (i == 11) {
            past_history();
            //$("html,body").animate({scrollTop: '180px'},800);
        } else if (i == 12) {
            examination();
            //$("html,body").animate({scrollTop: '250px'},800);
        } else if (i == 13) {
            discharge_summ(0);
            //$("html,body").animate({scrollTop: '700px'},1000);
        } else if (i == 14) {
            medicine_indent();
            //$("html,body").animate({scrollTop: '520px'},1000);
        } else if (i == 15) {
            sur_consumable();
            //$("html,body").animate({scrollTop: '500px'},1000);
        } else if (i == 16) {
            delivery_det();
            //$("html,body").animate({scrollTop: '550px'},1000);
        } else if (i == 17) {
            ot_book();
            //$("html,body").animate({scrollTop: '600px'},1000);
        } else if (i == 18) {
            discharge_request();
            //$("html,body").animate({scrollTop: '600px'},1000);
        } else if (i == 19) {
            load_medical_case_sheet();
            //$("html,body").animate({scrollTop: '600px'},1000);
        } else if (i == 23) {
            shift_pat();
        } else if (i == 31) {
            investigation_entry();
        } else if (i == 34) {
            insulin();
        } else if (i == 35) {
            history_collection();
        } else if (i == 36) {
            admission_assessment();
        } else if (i == 40) {
            load_service_entry_form(i);
        } else if (i == 41) {
            load_pac_form(i);
        } else if (i == 42) {
            load_guardian_bed(i);
        }
    }
}

// Guardian Bed Start
function load_guardian_bed(slno) {
    $("#loader").show();
    $.post("pages/nursing_dashboard_data.php", {
            type: "load_guardian_bed",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            ipd_pat_edit_bed: $("#ipd_pat_edit_bed").val(),
        },
        function(data, status) {
            $("#loader").hide();
            $("#accordian_data_load" + slno).html(data);

            setTimeout(function() {
                back_date_bed_transfer_guardian()
            }, 100);
        })
}

function back_date_bed_transfer_guardian() {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "guardian_last_bed_date",
        },
        function(data, status) {
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
                minDate: data,
                maxDate: '0',
            });
        })
}

function clr_bed_assign_guardian() {
    $.post("pages/nursing_dashboard_data.php", {
            type: "clr_bed_assign",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
        },
        function(data, status) {
            load_guardian_bed(42);
        })
}

function save_guardian_bed(val) {
    $.post("pages/nursing_dashboard_data.php", {
            type: "save_guardian_bed",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            guardian_bed_add_date: $("#guardian_bed_add_date").val(),
            val: $("#ipd_pat_edit_bed").val(),
            user: $("#user").text().trim(),
        },
        function(data, status) {
            var res = data.split("@");
            $("#loader").hide();
            bootbox.dialog({
                message: "<b>" + res[1] + "</b>"
            });
            setTimeout(function() {
                bootbox.hideAll();
                load_guardian_bed(42);
            }, 2000);
        })
}

function delete_guardian_bed() {
    $.post("pages/nursing_dashboard_data.php", {
            type: "delete_guardian_bed",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            user: $("#user").text().trim(),
        },
        function(data, status) {
            var res = data.split("@");
            $("#loader").hide();
            bootbox.dialog({
                message: "<b>" + res[1] + "</b>"
            });
            setTimeout(function() {
                bootbox.hideAll();
                load_guardian_bed(42);
            }, 2000);
        })
}
// Guardian Bed END

// PAC Entry Start
function load_pac_form(slno) {
    $("#loader").show();
    $.post("pages/nursing_dashboard_data.php", {
            type: "load_pac_form",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
        },
        function(data, status) {
            $("#loader").hide();
            $("#accordian_data_load" + slno).html(data);

            setTimeout(function() {
                load_selected_services();
            }, 100);
        })
}
// PAC Entry END

// Seivice Entry Start
function load_service_entry_form(slno) {
    $("#loader").show();
    $.post("pages/service_entry_data.php", {
            type: "load_service_entry_form",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
        },
        function(data, status) {
            $("#loader").hide();
            $("#accordian_data_load" + slno).html(data);

            setTimeout(function() {
                load_selected_services();
            }, 100);
        })
}

function entry_group_change() {
    $("#loader").show();
    $.post("pages/service_entry_data.php", {
            type: "load_service_list",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            group_id: $("#entry_group_id").val(),
        },
        function(data, status) {
            $("#loader").hide();
            $("#entry_service_id").html(data);
            entry_service_change();
        })
}

function entry_service_change(entry_service_slno) {
    $("#loader").show();
    $.post("pages/service_entry_data.php", {
            type: "load_service_entry_fields",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            group_id: $("#entry_group_id").val(),
            service_id: $("#entry_service_id").val(),
            entry_service_slno: entry_service_slno,
        },
        function(data, status) {
            $("#loader").hide();
            $("#load_service_entry_fields").html(data);

            load_datepicker_service_entry();
        })
}

function load_datepicker_service_entry() {
    $(".datepicker").attr("readonly", true);
    $.post("pages/service_entry_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "datepicker_min_max"
        },
        function(data, status) {
            var res = data.split("@@");
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
                minDate: res[0],
                maxDate: res[1],
            });
        })
}

function service_entry_reset() {
    $("#entry_group_id").val("0");
    $("#entry_service_id").val("0");
    $("#entry_service_slno").val("0");

    entry_group_change();
}

function save_service_entry() {
    if ($("#entry_group_id").val() == 0) {
        $("#entry_group_id").focus();
        return false;
    }
    if ($("#entry_service_id").val() == 0) {
        $("#entry_service_id").focus();
        return false;
    }
    if ($("#entry_service_name").val() == "") {
        $("#entry_service_name").focus();
        return false;
    }
    if ($("#entry_service_rate").val() == 0) {
        $("#entry_service_rate").focus();
        return false;
    }
    if ($("#entry_service_quantity").val() == 0) {
        $("#entry_service_quantity").focus();
        return false;
    }
    if ($("#entry_service_doc_id").val() == 0 && $("#entry_service_doc_id").is(":visible")) {
        //$("#entry_service_doc_id").focus();
        //return false;
    }

    $("#loader").show();
    $.post("pages/service_entry_data.php", {
            type: "save_service_entry",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            service_slno: $("#entry_service_slno").val(),
            group_id: $("#entry_group_id").val(),
            service_id: $("#entry_service_id").val(),
            service_name: $("#entry_service_name").val(),
            service_rate: $("#entry_service_rate").val(),
            service_quantity: $("#entry_service_quantity").val(),
            service_date: $("#entry_service_date").val(),
            service_doc_id: $("#entry_service_doc_id").val(),
            service_id: $("#entry_service_id").val(),
            service_id: $("#entry_service_id").val(),
            service_id: $("#entry_service_id").val(),
        },
        function(data, status) {
            $("#loader").hide();

            var res = data.split("@#@");
            if (res[0] == 101) {
                load_selected_services();
                add_services_to_bill();
            }

            bootbox.dialog({
                message: "<h4>" + res[1] + "</h4> "
            });
            setTimeout(function() {
                bootbox.hideAll();
            }, 2000);
        })
}

function add_services_to_bill() {
    $("#loader").show();
    $.post("pages/service_entry_data.php", {
            type: "add_services_to_bill",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            service_slno: $("#entry_service_slno").val(),
            group_id: $("#entry_group_id").val(),
            service_id: $("#entry_service_id").val(),
        },
        function(data, status) {

        })
}

function load_selected_services() {
    $("#loader").show();
    $.post("pages/service_entry_data.php", {
            type: "load_selected_services",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
        },
        function(data, status) {
            $("#loader").hide();
            $("#load_selected_services").html(data);
        })
}

function load_service_entry_each(slno, group_id, service_id) {
    $("#entry_group_id").val(group_id);
    setTimeout(function() {
        entry_group_change();
    }, 100);
    setTimeout(function() {
        $("#entry_service_id").val(service_id);
    }, 200);
    setTimeout(function() {
        entry_service_change(slno);
    }, 300);
}

function service_entry_delete(slno) {
    // check if bill generated of this service
    bootbox.dialog({
        //title: "Patient Re-visit ?",
        message: "<h5>Are you sure want to delete this service</h5>",
        buttons: {
            cancel: {
                label: '<i class="icon-remove"></i> No',
                className: "btn btn-inverse",
                callback: function() {
                    bootbox.hideAll();
                }
            },
            confirm: {
                label: '<i class="icon-ok"></i> Yes',
                className: "btn btn-danger",
                callback: function() {
                    $("#loader").show();
                    $.post("pages/service_entry_data.php", {
                            type: "service_entry_delete",
                            uhid: $("#uhid").val(),
                            ipd: $("#ipd").val(),
                            group_id: $("#entry_group_id").val(),
                            service_id: $("#entry_service_id").val(),
                            slno: slno,
                        },
                        function(data, status) {
                            $("#loader").hide();
                            var res = data.split("@#@");
                            if (res[0] == 101) {
                                load_selected_services();
                                add_services_to_bill();
                            }

                            bootbox.dialog({
                                message: "<h4>" + res[1] + "</h4> "
                            });
                            setTimeout(function() {
                                bootbox.hideAll();
                            }, 2000);
                        })
                }
            }
        }
    });
}

// Seivice Entry End




//------------------------------------------------------//
function fat_value() {
    $("#fat_name").val($("#fat_name").val().toUpperCase());
}

function sel_chief(id, n, e) {
    if (e.keyCode == 13) {
        if (id == "chief" + n && $("#" + id).val() != "")
            $("#cc" + n).focus();
        if (id == "cc" + n)
            $("#tim" + n).focus();
        if (id == "tim" + n)
            $("#addmore").focus();
    }
}
//------------------------------------------------------//
function calc_totday() {
    var tot = 0;
    var freq = $("#freq").val();
    var unit = $("#unit_day").val();
    var dur = parseInt($("#dur").val());
    var dos = parseInt($("#dos").val());
    if (unit == "Days")
        tot = (dur * dos * 1);
    else if (unit == "Weeks")
        tot = (dur * dos * 7);
    else if (unit == "Months")
        tot = (dur * dos * 30);
    if (freq == "1")
        tot = tot * 1;
    else if (freq == "2")
        tot = tot * 1;
    else if (freq == "3")
        tot = tot * 2;
    else if (freq == "4")
        tot = tot * 3;
    else if (freq == "5")
        tot = tot * 4;
    else if (freq == "6")
        tot = tot * 5;
    else if (freq == "7")
        tot = tot * 24;
    else if (freq == "8")
        tot = tot * 12;
    else if (freq == "9")
        tot = tot * 8;
    else if (freq == "10")
        tot = tot * 6;
    else if (freq == "11")
        tot = tot * 5;
    else if (freq == "12")
        tot = tot * 4;
    else if (freq == "13")
        tot = tot * 3;
    else if (freq == "14")
        tot = tot * 3;
    else if (freq == "15")
        tot = tot * 2;
    else if (freq == "16")
        tot = tot * 2;
    else
        tot = 0;
    $("#totl").val(tot);
}

function meditab(id, e) {
    if (e.keyCode == 13) {
        if (id == "dos" && $("#" + id).val() != "0")
            $("#freq").focus();
        if (id == "freq" && $("#" + id).val() != "0")
            $("#st_date").focus();
        if (id == "st_date" && $("#" + id).val() != "")
            $("#dur").focus();
        if (id == "dur" && $("#" + id).val() != "0")
            $("#dur").focus();
        if (id == "dur" && $("#" + id).val() != "0")
            $("#unit_day").focus();
        if (id == "unit_day" && $("#" + id).val() != "0")
            $("#inst").focus();
        if (id == "inst")
            $("#add_medi").focus();
        if (id == "con_doc" && $("#" + id).val() != "0")
            $("#add_medi").focus();
        if (id == "qnt" && $("#" + id).val() != "" && (parseInt($("#" + id).val())) > 0)
            $("#indsv").focus();
    }
}

function tab(id, e) {
    if (e.keyCode == 13) {
        if (id == "weight")
            $("#height").focus();
        if (id == "height")
            $("#mid_cum").focus();
        if (id == "mid_cum")
            $("#hd_cum").focus();
        if (id == "hd_cum")
            $("#spo").focus();
        if (id == "spo")
            $("#pulse").focus();
        if (id == "pulse")
            $("#temp").focus();
        if (id == "temp")
            $("#pr").focus();
        if (id == "pr")
            $("#rr").focus();
        if (id == "rr")
            $("#systolic").focus();
        if (id == "systolic")
            $("#diastolic").focus();
        if (id == "diastolic")
            $("#vit_note").focus();
        if (id == "vit_note")
            $("#sav_vit").focus();
    }
    if (e.keyCode == 27) {
        if (id == "course")
            $("#final_diag").focus();
        if (id == "final_diag")
            $("#foll").focus();
        if (id == "foll")
            $("#summ_btn").focus();
    }
}
//-------------------------------------------------------------
function delivery_det() {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $('#user').text().trim(),
            type: "pat_ipd_delivery_num",
        },
        function(data, status) {
            $("#accordian_data_load16").html(data);
            $("html,body").animate({
                scrollTop: '550px'
            }, 1000);
        })
}

function edit_certficate(slno) {
    $(".baby_tr").hide();
    $(".baby_div").hide();
    $(".btn_cert").attr("disabled", false);
    $(".btn_edt_cert").attr("disabled", false);
    $("#btn_cert" + slno).attr("disabled", true);
    $("#btn_edt_cert" + slno).attr("disabled", true);
    $("#btn_del_cert" + slno).attr("disabled", true);
    $("#btn_edt_fat").attr("disabled", true);
    $("#btn_sav_fat").attr("disabled", true);
    $("#btn_can_fat").attr("disabled", true);
    $(".clk_tr").css({
        "background": "",
        "color": "#444"
    });
    $("#loader").show();
    $.post("pages/nursing_dashboard_data.php", {
            slno: slno,
            type: "edit_certficate",
        },
        function(data, status) {
            //alert(data);
            $("#loader").hide();
            $("#clk" + slno).css({
                "background": "#FBFBD4",
                "color": "#000"
            });
            $("#tr" + slno).show();
            $("#sl" + slno).html(data);
            $("#sl" + slno).slideDown(500);
            //$("html,body").animate({scrollTop: '550px'},1000);
        })
}

function close_cert_edit(sl) {
    //alert(sl);
    $(".btn_cert").attr("disabled", false);
    $(".btn_edt_cert").attr("disabled", false);
    $("#btn_edt_fat").attr("disabled", false);
    $("#btn_sav_fat").attr("disabled", false);
    $("#btn_can_fat").attr("disabled", false);
    $(".clk_tr").css({
        "background": "",
        "color": "#444"
    });
    $("#sl" + sl).slideUp(500);
    $("#sl" + sl).empty();
    $("#tr" + sl).hide();
}

function save_cert_edit(sl) {
    $("#save_cert_edit").attr("disabled", true);
    $("#close_cert_edit").attr("disabled", true);
    $("#loader").show();
    $.post("pages/nursing_dashboard_data.php", {
            sl: sl,
            edit_dob: $("#edit_dob").val(),
            //edit_time:$("#edit_time").val(),
            edit_time: $("#edit_hrs").val() + ":" + $("#edit_mins").val() + ":00 " + $("#edit_ampm").val(),
            edit_sex: $("#edit_sex").val(),
            edit_wt: $("#edit_wt").val(),
            edit_blood: $("#edit_blood").val(),
            edit_dmode: $("#edit_dmode").val(),
            edit_conducted: $("#edit_conducted").val(),
            edit_tag: $("#edit_tag").val(),
            type: "save_cert_edit",
        },
        function(data, status) {
            $("#loader").hide();
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
                delivery_det();
            }, 1000);
            //$("html,body").animate({scrollTop: '550px'},1000);
        })
}

function del_certficate(sl) {
    bootbox.confirm("Do you really want to delete this certificate?",
        function(result) {
            if (result) {
                $("#loader").show();
                $.post("pages/nursing_dashboard_data.php", {
                        sl: sl,
                        type: "del_certficate",
                        uhid: $("#uhid").val(),
                        ipd: $("#ipd").val(),
                    },
                    function(data, status) {
                        $("#loader").hide();
                        bootbox.dialog({
                            message: data
                        });
                        setTimeout(function() {
                            bootbox.hideAll();
                            delivery_det();
                        }, 1000);
                    })
            }
        });
}

function edt_fat_nam() {
    $("#btn_edt_fat").hide();
    $("#btn_sav_fat").show();
    $("#btn_can_fat").show();
    $(".btn_edt_cert").attr("disabled", true);
    $("#edit_fat").show(500).focus();
}

function canc_fat_nam() {
    $("#btn_edt_fat").show();
    $("#btn_sav_fat").hide();
    $("#btn_can_fat").hide();
    $(".btn_edt_cert").attr("disabled", false);
    $("#edit_fat").hide();
}

function sav_fat_nam() {
    $("#loader").show();
    $("#btn_sav_fat").attr("disabled", true);
    $("#btn_can_fat").attr("disabled", true);
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val().trim(),
            ipd: $("#ipd").val().trim(),
            edit_fat: $("#edit_fat").val().trim(),
            type: "sav_fat_nam",
        },
        function(data, status) {
            $("#loader").hide();
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
                delivery_det();
            }, 1000);
        })
}

function fat_upper() {
    $("#edit_fat").val($("#edit_fat").val().toUpperCase());
}

function show_deli_num() {
    $("#show_deli_num").slideDown();
    $("#no").val('0');
    $("#deli_add_btn").attr("disabled", true);
    $("#no").focus();
}

function rem_deli_det() {
    $("#show_deli_num").slideUp();
    $("#deli_add_btn").attr("disabled", false);
}

function add_deli_det() {
    if ($("#no").val() == "0") {
        $("#no").focus();
    } else if ($("#fat_name").val().trim() == "") {
        $("#fat_name").focus();
    } else {
        $.post("pages/nursing_dashboard_data.php", {
                uhid: $("#uhid").val(),
                no: $("#no").val(),
                fat_name: $("#fat_name").val(),
                type: "pat_ipd_delivery_det",
            },
            function(data, status) {
                $("#show_deli_num").slideUp();
                $("#add_deli_det").html(data);
                $("#add_deli_det").slideDown();
                $("html,body").animate({
                    scrollTop: '700px'
                }, 800);
                //alert(data);
            })
    }
}

function pat_ipd_delivery_save() {
    var no = $("#val").val();
    var fat_name = $("#fat_name").val();
    var b_time = "";
    var all = "";
    for (var i = 1; i <= no; i++) {
        if ($("#dob" + i).val() == "" || $("#dob" + i).val() == "0000-00-00") {
            $("#dob" + i).focus();
            return true;
        }
        /*if($("#time"+i).val()=="")
        {
        	$("#time"+i).focus();
        	return true;
        }*/
        if ($("#hrs" + i).val() == "0") {
            $("#hrs" + i).focus();
            return true;
        }
        if ($("#mins" + i).val() == "0") {
            $("#mins" + i).focus();
            return true;
        }
        if ($("#ampm" + i).val() == "0") {
            $("#ampm" + i).focus();
            return true;
        }
        if ($("#sex" + i).val() == "0") {
            $("#sex" + i).focus();
            return true;
        }
        if ($("#wt" + i).val() == "" || $("#wt" + i).val() == 0) {
            $("#wt" + i).focus();
            return true;
        }
        if ($("#dmode" + i).val() == "0") {
            $("#dmode" + i).focus();
            return true;
        }
        if ($("#conducted" + i).val() == "0") {
            $("#conducted" + i).focus();
            return true;
        }
        if ($("#tag" + i).val() == "0") {
            $("#tag" + i).focus();
            return true;
        }
        /*if($("#bed"+i).val()=="")
        {
        	//$("#b"+i).focus();
        	$("#b"+i).css("box-shadow","0px 0px 10px 3px #ff0000");
        	return true;
        }*/ //all+=$("#dob"+i).val()+"@"+$("#sex"+i).val()+"@"+$("#time"+i).val()+"@"+$("#wt"+i).val()+"@"+$("#blood"+i).val()+"@"+$("#ward"+i).val()+"@"+$("#bed"+i).val()+"@"+fat_name+"@##";
        b_time = $("#hrs" + i).val() + ":" + $("#mins" + i).val() + ":00 " + $("#ampm" + i).val();
        all += $("#dob" + i).val() + "@" + $("#sex" + i).val() + "@" + b_time + "@" + $("#wt" + i).val() + "@" + $(
            "#blood" + i).val() + "@" + $("#dmode" + i).val() + "@" + $("#conducted" + i).val() + "@" + $("#tag" +
            i).val() + "@" + fat_name + "@##";
    }
    $("#loader").show();
    $("#deli_sav").attr("disabled", true);
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            all: all,
            usr: $('#user').text().trim(),
            type: "pat_ipd_delivery_save",
        },
        function(data, status) {
            $("#loader").hide();
            bootbox.dialog({
                message: "<h4>" + data + "</h4>"
            });
            setTimeout(function() {
                bootbox.hideAll();
                delivery_det();
            }, 2000);
        })
}

function pr_certficate(uhid, baby) {
    var uhid = btoa(uhid);
    var baby = btoa(baby);
    url = "pages/baby_certificate.php?uhid=" + uhid + "&baby_id=" + baby;
    wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}

function clear_all_deli() {
    $("#add_deli_det").slideUp();
    $("#add_deli_det").html('');
    $("#deli_add_btn").attr("disabled", false);
}

function view_baby_bed(uhid, sn) {
    $("#res").empty();
    $("#b" + sn).css("box-shadow", "");
    $.post("pages/global_load_g.php", {
            uhid: uhid,
            sn: sn,
            type: "view_baby_bed",
        },
        function(data, status) {
            $("#nt_btn").click();
            $("#res").html(data);
            chk_bed_assign(sn);
        })
}

function baby_bed_assign(id, wr, rid, bid, bno, sn) {
    var bd = wr + "@" + rid + "@" + bid + "@" + bno + "@" + sn + "@#";
    $("#ward" + sn).val(wr);
    $("#bed" + sn).val(bid);
    $("#b" + sn).text(bno);
    $.post("pages/global_insert_data_g.php", {
            uhid: $("#uhid").val(),
            ward: wr,
            room: rid,
            bed: bid,
            bno: bno,
            sn: $("#hid").val(),
            type: "baby_bed_assign",
        },
        function(data, status) {
            chk_bed_assign($("#hid").val());
        })
}

function chk_bed_assign(sn) {
    setInterval(function() {
        if ($('#accordian_data_load16').css('display') == "block") {
            $.post("pages/global_load_g.php", {
                    uhid: $("#uhid").val(),
                    sn: $("#hid").val(),
                    type: "view_baby_bed",
                },
                function(data, status) {
                    $("#res").html(data);
                })
        }
    }, 1500);
}
//---------------------------------------------------------------
function ad_med_emer() {
    $.post("pages/global_load_g.php", {
            type: "pat_ipd_ad_med_emer",
        },
        function(data, status) {
            $("#med_upd").click();
            $("#upd_med_plan_det").html(data);
        })
}

function ad_med_emer_set() {
    $.post("pages/global_insert_data_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            medi: $("#drug").val(),
            freq: $("#freq").val(),
            dur: $("#dur").val(),
            unit_day: $("#unit_day").val(),
            inst: $("#inst").val(),
            dose: $("#dose").val(),
            usr: $('#user').text().trim(),
            type: "pat_ipd_med_emer_set"
        },
        function(data, status) {
            med_admin();
        })
}

function change_med(id) {
    //alert(sl);
    $.post("pages/global_load_g.php", {
            id: id,
            type: "pat_ipd_med_plan_upd",
        },
        function(data, status) {
            $("#med_upd").click();
            $("#upd_med_plan_det").html(data);
        })
}

function update_plan(id) {
    $.post("pages/global_insert_data_g.php", {
            medi: $("#drug").val(),
            freq: $("#freq").val(),
            st_date: $("#st_date_upd").val(),
            dur: $("#dur").val(),
            unit_day: $("#unit_day").val(),
            inst: $("#inst").val(),
            dose: $("#dose").val(),
            con_doc: $("#con_doc").val(),
            id: id,
            usr: $('#user').text().trim(),
            type: "pat_ipd_med_plan_update"
        },
        function(data, status) {
            medication();
            /*bootbox.dialog({ message: data});
            setTimeout(function()
            {
            	bootbox.hideAll();
            }, 1000);*/
        })
}

function rep_pop(uhid, ipd, batch, testid, category_id) {
    if (category_id == 1) {
        $.post("pages/nurs_report_patho.php", {
            uhid: uhid,
            ipd: ipd,
            batch: batch,
            testid: testid,
        }, function(data, status) {
            $("#result").html(data);
            $("#rep").click();
        });
    }
    if (category_id == 2) {
        $.post("pages/nurs_report_rad.php", {
            uhid: uhid,
            ipd: ipd,
            batch: batch,
            testid: testid,
        }, function(data, status) {
            $("#result").html(data);
            $("#rep").click();
        });
    }
    if (category_id == 3) {
        $.post("pages/nurs_report_card.php", {
            uhid: uhid,
            ipd: ipd,
            batch: batch,
            testid: testid,
        }, function(data, status) {
            $("#result").html(data);
            $("#rep").click();
        });
    }
}

function test_enable() {
    setTimeout(function() {
        $("#chk_val").val(1)
    }, 500);
}
var t_val = 1;
var t_val_scroll = 0;

function select_test_new(val, e) {
    var z = "";
    var unicode = e.keyCode ? e.keyCode : e.charCode;
    if (unicode == 13) {
        var tst = document.getElementsByClassName("test" + t_val);
        load_test_new('' + tst[1].value.trim() + '', '' + tst[2].innerHTML.trim() + '', '' + tst[3].innerHTML.trim() +
            '');
        //$("#list_all_test").slideDown(400);
        $("#test").val("").focus();
    } else if (unicode == 40) {
        var chk = t_val + 1;
        var cc = document.getElementById("td" + chk).innerHTML;
        if (cc) {
            t_val = t_val + 1;
            $("#td" + t_val).css({
                'color': '#419641',
                'transform': 'scale(0.95)',
                'font-weight': 'bold',
                'transition': 'all .2s'
            });
            var t_val1 = t_val - 1;
            $("#td" + t_val1).css({
                'color': 'black',
                'transform': 'scale(1.0)',
                'font-weight': 'normal',
                'transition': 'all .2s'
            });
            var z2 = t_val % 1;
            if (z2 == 0) {
                $("#test_d").scrollTop(t_val_scroll)
                t_val_scroll = t_val_scroll + 30;
            }
        }
    } else if (unicode == 38) {
        var chk = t_val - 1;
        var cc = document.getElementById("td" + chk).innerHTML;
        if (cc) {
            t_val = t_val - 1;
            $("#td" + t_val).css({
                'color': '#419641',
                'transform': 'scale(0.95)',
                'font-weight': 'bold',
                'transition': 'all .2s'
            });
            var t_val1 = t_val + 1;
            $("#td" + t_val1).css({
                'color': 'black',
                'transform': 'scale(1.0)',
                'font-weight': 'normal',
                'transition': 'all .2s'
            });
            var z2 = t_val % 1;
            if (z2 == 0) {
                t_val_scroll = t_val_scroll - 30;
                $("#test_d").scrollTop(t_val_scroll)

            }
        }
    } else if (unicode == 27) {
        $("#test").val("");
        $("#test_d").html("");
        //$("#list_all_test").slideUp(300);

        $("html, body").animate({
            scrollTop: 500
        })
        $("#dis_per").focus();
    } else {
        $.post("pages/load_test_ajax_nurse.php", {
                type: "search_test",
                uhid: $("#uhid").val(),
                ipd: $("#ipd").val(),
                batch: $("#batch").val(),
                centreno: $("#ipd_test_centreno").val(),
                test: val,
            },
            function(data, status) {
                $("#test_d").html(data);
                t_val = 1;
                t_val_scroll = 0;
                $("#test_d").scrollTop(t_val_scroll)
            })
    }
}

function load_test_new(id, name, rate) {
    var item_chk = $("#test_list tr").length;
    if (!item_chk) {
        item_chk = 0;
    }

    if (item_chk == 0) {
        load_table(id, name, rate);
    } else {
        load_items(id, name, rate);
    }
}

function load_table(id, name, rate) {
    $.post("pages/load_test_ajax_nurse.php", {
            type: "load_item_table",
        },
        function(data, status) {
            $("#ss_tests").html(data);
            load_items(id, name, rate);
        })
}

function load_items(id, name, rate) {
    var each_row = $(".each_row");
    for (var i = 0; i < each_row.length; i++) {
        var tr_counter = each_row[i].value;

        var testid = $("#testid" + tr_counter).val();

        if (testid == id) {
            $("#test_sel").css({
                'opacity': '0.5'
            });
            $("#msgg").html("<span style='color:red;font-weight:bold;'>Already Selected</span>");
            var x = $("#test_sel").offset();
            var w = $("#msgg").width() / 2;
            //$("#msgg").css({'top':'50%','left':'50%'});
            $("#msgg").fadeIn(500);
            setTimeout(function() {
                $("#msgg").fadeOut(500, function() {
                    $("#test_sel").css({
                        'opacity': '1.0'
                    });
                })
            }, 600);

            return false;
        }
    }

    var tr_counter = $("#tr_counter").val().trim();

    $.post("pages/load_test_ajax_nurse.php", {
            type: "add_items",
            testid: id,
            test_name: name,
            test_rate: rate,
            tr_counter: tr_counter,
            user: $("#user").text().trim(),
        },
        function(data, status) {
            $("#item_footer").before(data);

            var next_tr_counter = parseInt($("#tr_counter").val()) + 1;
            $("#tr_counter").val(next_tr_counter);

            $("#ss_tests").animate({
                scrollTop: 2900
            });

            setTimeout(function() {
                $("#test").focus();
            }, 100);
        })
}

function remove_tr(val) {
    $("#tbl_tr" + val).remove();
}

function save_test() {
    var each_row = $(".each_row");
    var test_all = "";
    for (var i = 0; i < each_row.length; i++) {
        var tr_counter = each_row[i].value;

        var testid = $("#testid" + tr_counter).val();

        var test_rate = parseFloat($("#test_rate" + tr_counter).val());
        if (!test_rate) {
            test_rate = 0;
        }

        if (test_rate == 0) {
            $("#list_all_test").slideDown();
            setTimeout(function() {
                $("#test_rate" + tr_counter).focus();
            }, 100);
            return false;
        }

        var discount_each = parseFloat($("#discount_each" + tr_counter).val());
        if (!discount_each) {
            discount_each = 0;
        }

        if (testid) {
            test_all = test_all + "##" + testid + "@" + test_rate + "@" + discount_each;
        }
    }
    if (test_all == "" && $("#batch").val() == 0) {
        bootbox.dialog({
            message: "<h4>None test selected</h4> ",
            size: "small"
        });
        setTimeout(function() {
            bootbox.hideAll();
            scrollPage(380);
            $("#test").focus();
        }, 2000);
        return false;
    }

    $.post("pages/load_test_ajax_nurse.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            batch: $("#batch").val(),
            refbydoctorid: $("#ipd_test_ref_doc").val(),
            centreno: $("#ipd_test_centreno").val(),
            ward_id: $("#ipd_test_ward_no").val(),
            tst: test_all,
            usr: $("#user").text().trim(),
            type: "save_ipd_pat_test",
        },
        function(data, status) {
            bootbox.dialog({
                message: "<h4>Saved</h4>"
            });
            setTimeout(function() {
                bootbox.hideAll();

                $("#modal_test_close_btn").click();
                investigation($("#batch").val());

                setTimeout(function() {
                    //print_batch_bill($("#uhid").val(),$("#ipd").val(),$("#batch").val());
                    $("#nurse_test_bill_print_btn").click();
                }, 100);

            }, 1000);
        })
}


function load_test_new_old(id, name, rate) {
    //alert(id+" "+name+" "+rate);
    //$(".up_div").fadeIn(1);
    //$(".up_div").fadeOut(1);
    var test_chk = $('#test_list tr').length;
    if (test_chk == 0) {
        var test_add = "<table class='table table-condensed table-bordered' style='style:none' id='test_list'>";
        test_add +=
            "<tr><th style='background-color:#cccccc'>#</th><th style='background-color:#cccccc'>Tests</th><th style='background-color:#cccccc'>Rate</th><th style='background-color:#cccccc'>Remove</th></tr>";
        test_add += "<tr><td>1</td><td width='80%'>" + name + "<input type='hidden' value='" + id +
            "' class='test_id'/></td><td><span class='test_f'>" + rate +
            "</span></td><td onclick='delete_rows(this,2)'><span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span></td></tr>";
        test_add += "</table>";
        //test_add+="<div style='display:none;text-align:center;background-color:#cccccc;font-weight:bold;font-size:15px;padding:5px' id='test_hidden_price'>Total:<span id='test_total_hidden'></span></div>";

        $("#ss_tests").html(test_add);
        test_chk++;

        var tot = 0;
        var tot_ts = document.getElementsByClassName("test_f");
        for (var j = 0; j < tot_ts.length; j++) {
            tot = tot + parseInt(tot_ts[j].innerHTML);
        }
        $("#test_total").text(tot);
        $("#test").val("");
    } else {

        var t_ch = 0;
        var test_l = document.getElementsByClassName("test_id");

        for (var i = 0; i < test_l.length; i++) {
            if (test_l[i].value == id) {
                t_ch = 1;
            }
        }
        if (t_ch) {

            $("#test_sel").css({
                'opacity': '0.5'
            });
            $("#msgg").text("Already Selected");
            var x = $("#test_sel").offset();
            var w = $("#msgg").width() / 2;
            //$("#msgg").css({'top':'50%','left':'50%'});
            $("#msgg").fadeIn(500);
            setTimeout(function() {
                $("#msgg").fadeOut(500, function() {
                    $("#test_sel").css({
                        'opacity': '1.0'
                    });
                })
            }, 600);

        } else {

            var tr = document.createElement("tr");
            var td = document.createElement("td");
            var td1 = document.createElement("td");
            var td2 = document.createElement("td");
            var td3 = document.createElement("td");
            var tbody = document.createElement("tbody");

            td.innerHTML = test_chk;
            td1.innerHTML = name + "<input type='hidden' value='" + id + "' class='test_id'/>"
            td2.innerHTML = "<span class='test_f'>" + rate + "</span>";
            //td2.setAttribute("contentEditable","true");
            //td2.setAttribute("onkeyup","load_cost(2)");
            td3.innerHTML = "<span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span>";
            td3.setAttribute("onclick", "delete_rows(this,2)");
            tr.appendChild(td);
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tbody.appendChild(tr);
            document.getElementById("test_list").appendChild(tbody);
            var tot = 0;
            var tot_ts = document.getElementsByClassName("test_f");
            for (var j = 0; j < tot_ts.length; j++) {
                tot = tot + parseInt(tot_ts[j].innerHTML);
            }
            $("#test_total").text(tot);
        }

        if (test_chk > 4) {
            $("#list_all_test").css({
                'height': '220px',
                'overflow': 'scroll',
                'overflow-x': 'hidden'
            })
            $("#list_all_test").animate({
                scrollTop: 2900
            });
            $("#test_hidden_price").fadeIn(200);
            $("#test_total_hidden").text($("#test_total").text());
        }
        $("#test").val("");
    }
    $("#test").focus();
    //add_vaccu();
}

function delete_rows(tab, num) {
    $(tab).parent().remove();
    $("#test").focus();
}
//------------------------------------------------------//
function save_test_old() {
    var tst = $("input.test_id").map(function() {
        return this.value;
    }).get().join(",");
    $.post("pages/global_insert_data_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            batch: $("#batch").val(),
            refbydoctorid: $("#ipd_test_ref_doc").val(),
            tst: tst,
            usr: $("#user").text().trim(),
            type: "save_ipd_pat_test",
        },
        function(data, status) {
            /*bootbox.dialog({ message: data});
            setTimeout(function()
            {
            	bootbox.hideAll();
            	investigation();
            }, 1000);*/
            investigation($("#batch").val());
        })
}

////-------------------------------------------------------------//
function admission_assessment() {
    $("#loader").show();
    $.post("pages/nursing_dash_ajax.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "admission_assessment",
        },
        function(data, status) {
            $("#loader").hide();
            $("#accordian_data_load36").html(data);
            //$("#weight").focus();
            $("html,body").animate({
                scrollTop: '200px'
            }, 500);
        });
}

function save_admission_assessment() {
    $("#sav_case").attr("disabled", true);
    $("#loader").show();
    $.post("pages/nursing_dash_ajax.php", {
            height: $("#height").val().trim(),
            weight: $("#weight").val().trim(),
            rr: $("#rr").val().trim(),
            bp: $("#bp").val().trim(),
            pulse: $("#pulse").val().trim(),
            temp: $("#temp").val().trim(),
            pallor: $("#pallor").val().trim(),
            cyanosis: $("#cyanosis").val().trim(),
            club: $("#club").val().trim(),
            saturation: $("#saturation").val().trim(),
            apvu: $("#apvu").val().trim(),
            gcs: $("#gcs").val().trim(),
            nervous: $("#nervous").val().trim(),
            respiratory: $("#respiratory").val().trim(),
            vascular: $("#vascular").val().trim(),
            abdomen: $("#abdomen").val().trim(),
            pr_diag: $("#pr_diag").val().trim(),
            rec_doc: $("#rec_doc").val().trim(),
            informant: $("#informant").val().trim(),
            patient: $("#patient").val().trim(),
            rel: $("#rel").val().trim(),
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "save_admission_assessment",
        },
        function(data, status) {
            $("#loader").hide();
            $("#sav_case").attr("disabled", false);
        });
}

function history_collection() {
    $("#loader").show();
    $.post("pages/nursing_dash_ajax.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "history_collection",
        },
        function(data, status) {
            $("#loader").hide();
            $("#accordian_data_load35").html(data);
            //$("#weight").focus();
            $("html,body").animate({
                scrollTop: '200px'
            }, 500);
        });
}

function save_history_collection() {
    $("#h_sav_btn").attr("disabled", true);
    $("#loader").show();
    $.post("pages/nursing_dash_ajax.php", {
            present_illness: $("#present_illness").val().trim(),
            past_illness: $("#past_illness").val().trim(),
            medical_history: $("#medical_history").val().trim(),
            family_history: $("#family_history").val().trim(),
            personal_history: $("#personal_history").val().trim(),
            menst_history: $("#menst_history").val().trim(),
            obst_history: $("#obst_history").val().trim(),
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "save_history_collection",
        },
        function(data, status) {
            $("#loader").hide();
            alert(data);
            history_collection();
        });
}
////-------------------------------------------------------------//
function tr_accid() {
    var chk = $("#accident:checked").length;
    var inj_chk = $(".check_injury:checked").length;
    //alert(chk);
    if (chk == 1) {
        $(".tr_accid").slideDown();
        if (inj_chk > 0) {
            $("#tr_injury").slideDown();
        } else {
            $("#tr_injury").hide();
        }
    } else {
        $(".tr_accid").hide();
        $("#tr_injury").hide();
    }
}

function check_injury() {
    var chk = $(".check_injury:checked").length;
    //alert(chk);
    if (chk > 0) {
        $("#tr_injury").slideDown();
    } else {
        $("#tr_injury").hide();
    }
}

function check_dur() {
    var chk = $(".check_dur:checked").length;
    //alert(chk);
    if (chk > 0) {
        $("#tr_dur").slideDown();
    } else {
        $("#tr_dur").hide();
    }
}

function check_drug() {
    var chk = $(".check_drug:checked").length;
    //alert(chk);
    if (chk > 0) {
        $("#tr_drug").slideDown();
    } else {
        $("#tr_drug").hide();
    }
}

function check_per() {
    var chk = $(".check_per:checked").length;
    //alert(chk);
    if (chk > 0) {
        $("#tr_per").slideDown();
    } else {
        $("#tr_per").hide();
    }
}

function check_family() {
    var chk = $(".check_family:checked").length;
    //alert(chk);
    if (chk > 0) {
        $("#tr_family").slideDown();
    } else {
        $("#tr_family").hide();
    }
}
////-------------------------------------------------------------//
function load_medical_case_sheet() {
    $.post("pages/nursing_dash_ajax.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "load_medical_case_sheet",
        },
        function(data, status) {
            $("#accordian_data_load19").html(data);
            $("html,body").animate({
                scrollTop: '200px'
            }, 1000);
            //alert(check_load_val("blunt"));
        })
}

function check_load_val(id) {
    if ($("#" + id).is(':checked')) {
        // Code in the case checkbox is checked.
        return 1;
    } else {
        // Code in the case checkbox is NOT checked.
        return 0;
    }
}

function save_case() {
    $("#sav_case").attr("disabled", true);
    $.post("pages/nursing_dash_ajax.php", {
            uhid: $("#uhid").val().trim(),
            ipd: $("#ipd").val().trim(),
            illness: $("#illness").val().trim(),
            inj_dt: $("#inj_dt").val().trim(),
            inj_tm: $("#inj_tm").val().trim(),
            accident: check_load_val("accident"),
            blunt: check_load_val("blunt"),
            penet: check_load_val("penet"),
            burns: check_load_val("burns"),
            inhal: check_load_val("inhal"),
            inj_oth: check_load_val("inj_oth"),
            injury_hist: $("#injury_hist").val().trim(),
            occur: $("#occur").val().trim(),
            mechan: $("#mechan").val().trim(),
            care: $("#care").val().trim(),
            no_past: check_load_val("no_past"),
            copd: check_load_val("copd"),
            cva: check_load_val("cva"),
            hyper: check_load_val("hyper"),
            unknown: check_load_val("unknown"),
            heart: check_load_val("heart"),
            cancer: check_load_val("cancer"),
            diabetes: check_load_val("diabetes"),
            seizure: check_load_val("seizure"),
            past_oth: check_load_val("past_oth"),
            dur_hist: $("#dur_hist").val().trim(),
            operation: $("#operation").val().trim(),
            steroid: check_load_val("steroid"),
            hormone: check_load_val("hormone"),
            drug: check_load_val("drug"),
            pills: check_load_val("pills"),
            analgesic: check_load_val("analgesic"),
            drug_hist: $("#drug_hist").val().trim(),
            regular: check_load_val("regular"),
            irregular: check_load_val("irregular"),
            mens_hist: $("#mens_hist").val().trim(),
            tran_hist: $("#tran_hist").val().trim(),
            allergy: $("#allergy").val().trim(),
            alcohol: check_load_val("alcohol"),
            smoking: check_load_val("smoking"),
            oth_addict: check_load_val("oth_addict"),
            per_hist: $("#per_hist").val().trim(),
            htn: check_load_val("htn"),
            dm: check_load_val("dm"),
            cva: check_load_val("cva"),
            ihd: check_load_val("ihd"),
            f_cancer: check_load_val("f_cancer"),
            asthma: check_load_val("asthma"),
            f_oth: check_load_val("f_oth"),
            fam_hist: $("#fam_hist").val().trim(),
            treat_hist: $("#treat_hist").val().trim(),
            past_inv: $("#past_inv").val().trim(),
            height: $("#height").val().trim(),
            weight: $("#weight").val().trim(),
            rr: $("#rr").val().trim(),
            bp: $("#bp").val().trim(),
            pulse: $("#pulse").val().trim(),
            temp: $("#temp").val().trim(),
            pallor: $("#pallor").val().trim(),
            cyanosis: $("#cyanosis").val().trim(),
            club: $("#club").val().trim(),
            saturation: $("#saturation").val().trim(),
            apvu: $("#apvu").val().trim(),
            gcs: $("#gcs").val().trim(),
            nervous: $("#nervous").val().trim(),
            respiratory: $("#respiratory").val().trim(),
            vascular: $("#vascular").val().trim(),
            abdomen: $("#abdomen").val().trim(),
            pr_diag: $("#pr_diag").val().trim(),
            rec_doc: $("#rec_doc").val().trim(),
            informant: $("#informant").val().trim(),
            patient: $("#patient").val().trim(),
            rel: $("#rel").val().trim(),
            usr: $("#user").text().trim(),
            type: "sav_case",
        },
        function(data, status) {
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
                load_medical_case_sheet();
            }, 1000);
        })
}

function chk_id() {
    var all = "";
    var c = "case_tbl";
    var l = $("#" + c + " input[type='text'], textarea");
    for (var i = 0; i < l.length; i++) {
        all += l[i].id + '\n';
    }
    alert(all);
}

function print_case_sheet() {
    var uhid = btoa($("#uhid").val());
    var ipd = btoa($("#ipd").val());
    var usr = btoa($("#user").text().trim());
    url = "pages/print_case_sheet.php?uhid=" + uhid + "&ipd=" + ipd + "&user=" + usr;
    wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}
////-------------------------------------------------------------//

function collapse_det(n) {
    if (n == 0) {
        for (; n <= 18; n++) {
            if (n != 1 && n != 6 && n != 10 && n != 11 && n != 12 && n != 16) {
                $("#collapse_data" + n).css("display", "none");
                $("#collapse_none" + n).css("display", "");
                $("#collapse_none" + n).css("opacity", "0.5");
            }
        }
    } else if (n != 0) {
        for (; n <= 17; n++) {
            $("#collapse_none" + n).css("opacity", "1.0");
            $("#collapse_none" + n).css("display", "none");
            $("#collapse_data" + n).css("display", "");
        }
    }
    auto_note();
}

function ipd_pat_bed_alloc() {
    $("#del_mod").click();
    $("#msgtext").html('Admit Patient?');
}

function admit_pat() {
    $.post("pages/nursing_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "ipd_pat_admit",
        },
        function(data, status) {
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
                load_bed_det();
                room_status();
            }, 1000);
        })
}

function nursing_bed_transfer(val) {
    $("#ipd_pat_edit_bed").val(val);
    $.post("pages/nursing_dashboard_data.php", {
            type: "nursing_bed_transfer",
            branch_id: $("#branch_id").val(),
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            val: val,
        },
        function(data, status) {
            $("#bed_modal_btn").click();
            $("#bed_modal_data").html(data);
            $("#bed_modal").animate({
                'top': '1%',
                'left': '23%',
                "width": "95%",
                'margin': 'auto'
            }, "slow");
            nursing_chk_bed_assign(val);
        })
}

function nursing_bed_asign(w_id, b_id, w_name, b_no, val) {
    bootbox.confirm("Do you really want to assign bed no " + b_no + " of ward " + w_name + " to this patient?",
        function(result) {
            if (result) {
                $.post("pages/nursing_dashboard_data.php", {
                        type: "nursing_bed_asign",
                        uhid: $("#uhid").val(),
                        ipd: $("#ipd").val(),
                        w_id: w_id,
                        b_id: b_id
                    },
                    function(data, status) {
                        bed_assign_temp(val);
                    })
            }
        });
}

function bed_assign_temp(val) {
    $("#bed_info").show();
    $.post("pages/nursing_dashboard_data.php", {
            type: "bed_assign_temp",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
        },
        function(data, status) {
            if (val > 2) // Guardian
            {
                //~ $("#guardian_add_bed_div").hide();
                //~ $("#guardian_save_bed_div").show();

                load_guardian_bed(42);

                $("#modal_close_btn").click();
            } else {
                $("#bed_info").html(data);
                $("#bed_btn_info").show();

                $("#modal_close_btn").click();
            }
        })
}

function bed_assign_ok() {
    $("#loader").show();
    $.post("pages/nursing_dashboard_data.php", {
            type: "bed_assign_ok",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            ward: $("#ward_id").val(),
            bed: $("#bed_id").val(),
            usr: $("#user").text().trim(),
            edit: $("#ipd_pat_edit_bed").val(),
            bed_transfer_date: $("#bed_transfer_date").val(),
        },
        function(data, status) {
            $("#loader").hide();
            bootbox.dialog({
                message: "<b>Saved</b>"
            });
            setTimeout(function() {
                bootbox.hideAll();
                room_status();
                load_bed_det();

                setTimeout(function() {
                    //add_auto_charge();
                    window.location.reload(true);
                }, 100);
            }, 2000);
        })
}

function clr_bed_assign() {
    $.post("pages/nursing_dashboard_data.php", {
            type: "clr_bed_assign",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
        },
        function(data, status) {
            $("#ward_id").val('');
            $("#bed_id").val('');
            $("#bed_info").hide(500);
            $("#bed_btn_info").hide(500);
        })
}

function nursing_chk_bed_assign(val) {
    setInterval(function() {
        if ($('#bed_modal').hasClass('in')) {
            $.post("pages/nursing_dashboard_data.php", {
                    type: "nursing_bed_transfer",
                    branch_id: $("#branch_id").val(),
                    uhid: $("#uhid").val(),
                    ipd: $("#ipd").val(),
                    val: $("#ipd_pat_edit_bed").val(),
                },
                function(data, status) {
                    $("#bed_modal_data").html(data);
                })
        }
    }, 2000);
}

// Discharge Summary Start
function discharge_summ(template_id) {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            template_id: template_id,
            type: "ipd_pat_discharge_summ",
        },
        function(data, status) {
            $("#accordian_data_load13").html(data);
            //$("html,body").animate({scrollTop: '700px'},1000);
            setTimeout(function() {
                $("#admission_reason").focus();

                $(".datepicker_max").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd',
                    maxDate: '0',
                    yearRange: "-10:+0",
                });
                $(".datepicker_min").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd',
                    minDate: '0',
                    yearRange: "-0:+10",
                });
                $(".timepicker").timepicker({
                    minutes: {
                        starts: 0,
                        interval: 1,
                        showSecond: true,
                        showMillisec: true,
                    }
                });

                load_datepicker_service();
            }, 500);
        })
}
// Discharge Summary End

function edit_ot_book() {
    $(".sh_data").hide();
    $(".ed_data").show();
}

function canc_ot_book() {
    $(".ed_data").hide();
    $(".sh_data").show();
}

function remove_ot_book() {
    bootbox.dialog({
        message: "<h5>Are you sure want to cancel OT booking?</h5>",
        buttons: {
            cancel: {
                label: '<i class="icon-remove"></i> No',
                className: "btn btn-inverse",
                callback: function() {
                    bootbox.hideAll();
                }
            },
            confirm: {
                label: '<i class="icon-ok"></i> Yes',
                className: "btn btn-danger",
                callback: function() {
                    $.post("pages/nursing_dash_ajax.php", {
                            uhid: $("#uhid").val(),
                            ipd: $("#ipd").val(),
                            usr: $("#user").text().trim(),
                            type: "remove_ot_book",
                        },
                        function(data, status) {
                            bootbox.dialog({
                                message: data
                            });
                            setTimeout(function() {
                                bootbox.hideAll();
                                ot_book();
                            }, 1000);
                        })
                }
            }
        }
    });
}

function upd_ot_book() {
    if ($("#pr").val().trim() == "") {
        $("#pr").focus();
    } else if ($("#ot_date").val() == "") {
        $("#ot_date").focus();
    } else if ($("#doc").val() == "0") {
        $("#doc").focus();
    } else {
        $("#ot_upd_btn").attr("disabled", true);
        $.post("pages/nursing_dash_ajax.php", {
                uhid: $("#uhid").val(),
                ipd: $("#ipd").val(),
                //ot:$("#ot").val(),
                pr: $("#pr").val(),
                ot_date: $("#ot_date").val(),
                doc: $("#doc").val(),
                usr: $("#user").text().trim(),
                type: "upd_ot_book",
            },
            function(data, status) {
                bootbox.dialog({
                    message: data
                });
                setTimeout(function() {
                    bootbox.hideAll();
                    ot_book();
                }, 1000);
            })
    }
}

function ot_book() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_ot_booking",
        },
        function(data, status) {
            $("#accordian_data_load17").html(data);
            $("html,body").animate({
                scrollTop: '600px'
            }, 1000);
        })
}

function ot_det_show() {
    $('#ot_det').slideDown();
    //$('#otad').attr('disabled',true);
    $('#otad').slideUp(1000);
    $("html,body").animate({
        scrollTop: '600px'
    }, 1000);
}

function ot_det_hide() {
    $('#ot_det').slideUp();
    //$('#otad').attr('disabled',false);
    $('#otad').slideDown(500);
}

function save_ot_book() {
    /*
    if($("#ot").val()=="0")
    {
    	$("#ot").focus();
    }
    */
    if ($("#pr").val().trim() == "") {
        $("#pr").focus();
    } else if ($("#ot_date").val() == "") {
        $("#ot_date").focus();
    } else if ($("#doc").val() == "0") {
        $("#doc").focus();
    } else {
        $.post("pages/global_insert_data_g.php", {
                uhid: $("#uhid").val(),
                ipd: $("#ipd").val(),
                ot: $("#ot").val(),
                pr: $("#pr").val(),
                ot_date: $("#ot_date").val(),
                doc: $("#doc").val(),
                usr: $("#user").text().trim(),
                type: "save_ipd_ot_book",
            },
            function(data, status) {
                bootbox.dialog({
                    message: data
                });
                setTimeout(function() {
                    bootbox.hideAll();
                    ot_book();
                }, 1000);
            })
    }
}

function sur_consumable() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_sur_consumable",
        },
        function(data, status) {
            $("#accordian_data_load15").html(data);
            $("html,body").animate({
                scrollTop: '500px'
            }, 800);
        })
}

function add_sur_consume() {
    $("#add_sur_consume").fadeIn(1000);
    $("#add_con").attr("disabled", true);
    $("html,body").animate({
        scrollTop: '500px'
    }, 800);
}

function close_sur_consumable() {
    $("#consume").val('0');
    $("#consume_qnt").val('');
    $("#add_sur_consume").hide(500);
    $("#add_con").attr("disabled", false);
}

function save_sur_consumable() {
    if ($("#consume1").val() == "0") {
        $("#consume1").focus();
    } else if ($("#consume_qnt1").val() == "") {
        $("#consume_qnt1").focus();
    } else {
        $.post("pages/global_insert_data_g.php", {
                uhid: $("#uhid").val(),
                ipd: $("#ipd").val(),
                consume: $("#consume1").val(),
                consume_qnt: $("#consume_qnt1").val(),
                usr: $("#user").text().trim(),
                type: "ipd_pat_save_sur_consumable",
            },
            function(data, status) {
                sur_consumable();
            })
    }
}

function medicine_indent() {
    /*---------------old_page=nursing_load_g, type=ipd_pat_medicine_indent--------------*/
    $.post("pages/ipd_pat_medicine_indent.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: 1,
        },
        function(data, status) {
            $("#accordian_data_load14").html(data);
            $("#accordian_data_load14").css({
                "min-height": "300px",
                "overflow-y": "scroll",
                "padding": "1px"
            });
            //$("html,body").animate({scrollTop: '520px'},1000);
        })
}

function chief_complain() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "ipd_pat_chief_complain",
        },
        function(data, status) {
            $("#accordian_data_load10").html(data);
            $("html,body").animate({
                scrollTop: '180px'
            }, 800);
        })
}

function past_history() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            type: "ipd_pat_past_history",
        },
        function(data, status) {
            $("#accordian_data_load11").html(data);
        })
}

function update_hist() {
    $.post("pages/global_insert_data_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            p_hist: $("#p_hist").val().trim(),
            usr: $("#user").text().trim(),
            type: "ipd_pat_update_hist",
        },
        function(data, status) {
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
            }, 1000);
            past_history();
        })
}

function examination() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "ipd_pat_examination",
        },
        function(data, status) {
            $("#accordian_data_load12").html(data);
            $("html,body").animate({
                scrollTop: '250px'
            }, 800);
        })
}

function save_exam() {
    $.post("pages/global_insert_data_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            exam: $("#exam").val().trim(),
            usr: $("#user").text().trim(),
            type: "ipd_pat_examination",
        },
        function(data, status) {
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
            }, 1000);
            examination();
        })
}

function post_drugs() {
    $("#med_post").click();
    $('#med_list_post').css('height', '100px');
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "ipd_add_medicine_post",
        },
        function(data, status) {
            $("#med_list_post").html(data);
        })
}

function print_disc_summary() {
    var uhid = btoa($("#uhid").val());
    var ipd = btoa($("#ipd").val());
    var usr = btoa($("#user").text().trim());
    url = "pages/ipd_discharge_summary.php?uhid=" + uhid + "&ipd=" + ipd + "&user=" + usr;
    wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}

function add_row(r) {
    var rr = $('#hist_table tbody tr.cc').length;
    var i = 1;
    var d = "";
    for (i = 1; i <= 30; i++) {
        d += "<option value='" + i + "'>" + i + "</option>";
    }
    var s =
        '<option value="Minutes">Minutes</option><option value="Hours">Hours</option><option value="Days">Days</option><option value="Week">Week</option><option value="Month">Month</option><option value="Year">Year</option>';
    $("#loader").show();
    $.post("pages/global_load_g.php", {
            no: rr,
            type: "complain_templates_list",
        },
        function(data, status) {
            $("#loader").hide();
            $("#hh").closest("tr").before('<tr class="cc" id="tr' + rr +
                '"><th>Chief Complaints</th><td><input list="browsrs' + rr + '" type="text" id="chief' + rr +
                '" class="" onkeyup="sel_chief(this.id,' + rr + ',event)" /><span id="comp_lst' + rr +
                '"></span></td><td><b>for</b> <select id="cc' + rr +
                '" class="span2" onkeyup="sel_chief(this.id,' + rr +
                ',event)"><option value="0">--Select--</option>' + d + '</select> <select id="tim' + rr +
                '" class="span2" onkeyup="sel_chief(this.id,' + rr +
                ',event)"><option value="0">--Select--</option>' + s +
                '</select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>'
                );
            $("#comp_lst" + rr).html(data);
            $("#chief" + rr).focus();
        })
}

function insert_complain() {
    var i = 0;
    var det = "";
    //var rr=document.getElementById("hist_table tbody tr.cc").rows.length;
    var rr = $('#hist_table tbody tr.cc').length;
    for (i = 0; i < rr; i++) {
        det += $("#tr" + i).find("input").val() + "@" + $("#tr" + i).find("select:first").val() + "@" + $("#tr" + i)
            .find("select:last").val() + "#govin#";
    }
    //alert(det);
    $.post("pages/global_insert_data_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            det: det,
            usr: $("#user").text().trim(),
            type: "ipd_pat_insert_complain",
        },
        function(data, status) {
            chief_complain();
        })
}

function show_tests(vl) {
    $("#selc_test").show();
    $.post("pages/load_test_ajax_nurse.php", {
            val: vl,
            type: "show_sel_tests_ipd",
        },
        function(data, status) {
            $("#selc_test").html(data);
        })
}

function ad_tests(batch) {
    $("#dl1").click();
    $.post("pages/load_test_ajax_nurse.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            batch: batch,
            type: "show_sel_tests_ipd",
        },
        function(data, status) {
            $("#tests_lst").show().html(data);
            setTimeout(function() {
                $("#test").focus()
            }, 500);
        })
}

function equipment() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_equipment",
        },
        function(data, status) {
            $("#accordian_data_load7").html(data);
            $("html,body").animate({
                scrollTop: '480px'
            }, 800);
        })
}

function add_equip() {
    $("#add_equip").fadeIn(1000);
    $("#add_eq").attr("disabled", true);
    $("html,body").animate({
        scrollTop: '480px'
    }, 800);
}

function close_equip() {
    $("#equip").val('0');
    $("#hour").val('0');
    $("#add_equip").hide(500);
    $("#add_eq").attr("disabled", false);
}

function save_equip() {
    if ($("#equip").val() == "0") {
        $("#equip").focus();
    } else if ($("#hour").val() == "0") {
        $("#hour").focus();
    } else {
        $.post("pages/global_insert_data_g.php", {
                uhid: $("#uhid").val(),
                ipd: $("#ipd").val(),
                equip: $("#equip").val(),
                hour: $("#hour").val(),
                usr: $("#user").text().trim(),
                type: "ipd_pat_save_equip",
            },
            function(data, status) {
                equipment();
            })
    }
}

function gen_consumable() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_consumable",
        },
        function(data, status) {
            $("#accordian_data_load8").html(data);
            $("html,body").animate({
                scrollTop: '500px'
            }, 800);
        })
}

function add_consume() {
    $("#add_consume").fadeIn(1000);
    $("#add_con").attr("disabled", true);
    $("html,body").animate({
        scrollTop: '500px'
    }, 800);
}

function close_consumable() {
    $("#consume").val('0');
    $("#consume_qnt").val('');
    $("#add_consume").hide(500);
    $("#add_con").attr("disabled", false);
}

function save_consumable() {
    if ($("#consume").val() == "0") {
        $("#consume").focus();
    } else if ($("#consume_qnt").val() == "") {
        $("#consume_qnt").focus();
    } else {
        $.post("pages/global_insert_data_g.php", {
                uhid: $("#uhid").val(),
                ipd: $("#ipd").val(),
                consume: $("#consume").val(),
                consume_qnt: $("#consume_qnt").val(),
                usr: $("#user").text().trim(),
                type: "ipd_pat_save_consumable",
            },
            function(data, status) {
                gen_consumable();
            })
    }
}

function room_status() {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            ipd_pat_edit_bed: $("#ipd_pat_edit_bed").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_room_status",
        },
        function(data, status) {
            $("#accordian_data_load6").html(data);
            $("html,body").animate({
                scrollTop: '160px'
            }, 800);
            back_date_bed_transfer();
        })
}

function back_date_bed_transfer() {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "pat_last_bed_date",
        },
        function(data, status) {
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
                minDate: data,
                maxDate: '0',
            });
            if ($("#lavel_id").val() != "1") {
                $("#bed_transfer_date").prop("disabled", true);
            }
        })
}

function med_admin() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            view: "1",
            usr: $("#user").text().trim(),
            type: "pat_ipd_med_admin",
        },
        function(data, status) {
            $("#accordian_data_load9").html(data);
        })
}

function view_medi(vl) {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            view: vl,
            usr: $("#user").text().trim(),
            type: "pat_ipd_med_admin",
        },
        function(data, status) {
            $("#accordian_data_load9").html(data);
        })
}

function diagnosis() {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_det",
        },
        function(data, status) {
            $("#accordian_data_load1").html(data);
            /*bootbox.dialog({ message: data});
            setTimeout(function()
            {
            	bootbox.hideAll();
            }, 1000);*/
        })
}

function view_batch(batch) {
    $(".bt").removeClass('btt');
    $("#ad" + batch).addClass('btt');
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd_id: $("#ipd").val(),
            batch_no: batch,
            user: $("#user").text().trim(),
            lavel: $("#lavel_id").val(),
            type: "ipd_batch_details",
        },
        function(data, status) {
            $("#batch_details").html(data);
            $("#foll_details").html('');
        })
}

function hid_div(e) {
    var unicode = e.keyCode ? e.keyCode : e.charCode;
    if (unicode == 27) {
        //$("#mod").click();
        $('.modal').modal('hide');
    }
}

function view_all(val) {
    $("#loader").show();
    $.post("pages/page.php", // phlebo_load_pat
        {
            type: val,
            pat_type: $("#pat_type").val(),
            from: $("#from").val(),
            to: $("#to").val(),
            pat_name: $("#pat_name").val(),
            catagory: $("#catagory").val(),
            var_id: $("#var_id").val(),
            user: $("#user").text().trim(),
        },
        function(data, status) {
            $("#loader").hide();
            $("#load_all").show().html(data);
        })
}

function load_sample(uhid, opd, ipd) {
    $.post("pages/page.php", //phlebo_load_sample
        {
            uhid: uhid,
            opd: opd,
            ipd: ipd,
            lavel: $("#lavel_id").val()
        },
        function(data, status) {
            $("#results").html(data);
            $("#mod").click();
            $("#results").fadeIn(500, function() {
                load_vaccu();
            })
        })
}

function select_sample(id) {
    if ($("#" + id).is(":checked")) {
        $("." + id).click();
    } else {
        if ($("#val_" + id).val() == "0") {
            $("." + id).attr('checked', false);
        } else {
            $("#" + id).attr('checked', true);
            alert("Sample already accepted by Lab. Action cannot be completed");
        }
    }

    load_vaccu();
}

function load_vaccu() {
    var tst = "";
    var samp = $(".samp:checked")
    for (var i = 0; i < samp.length; i++) {
        var test = $("." + $(samp[i]).attr("id"));
        for (var j = 0; j < test.length; j++) {
            tst = tst + "@" + $(test[j]).val();
        }
    }

    $(".vac").attr("checked", false);
    $.post("pages/phlebo_load_vaccu.php", // phlebo_load_vaccu
        {
            tst: tst
        },
        function(data, status) {
            var vc = data.split("@");
            for (var k = 0; k < vc.length; k++) {
                if (vc[k]) {
                    $("#vac_" + vc[k] + "").click();
                }
            }
        })
}

function note(a, batch) {
    $.post("pages/global_load_g.php", // page_name
        {
            test_id: a,
            batch: batch,
            uhid: $('#uhid').val(),
            ipd: $('#ipd').val(),
            usr: $('#user').text().trim(),
            type: "update_note_ipd",
        },
        function(data, status) {
            bootbox.dialog({
                message: "Note:<input type='text' value='" + data + "' id='note' />",
                title: "Note",
                buttons: {
                    main: {
                        label: "Save",
                        className: "btn-primary",
                        callback: function() {
                            if ($('#note').val() != '') {
                                $.post("pages/global_insert_data_g.php", {
                                        test_id: a,
                                        uhid: $('#uhid').val(),
                                        ipd: $('#ipd').val(),
                                        note: $('#note').val(),
                                        batch: batch,
                                        usr: $('#user').text().trim(),
                                        type: "ipd_pat_notes"
                                    },
                                    function(data, status) {
                                        bootbox.dialog({
                                            message: data
                                        });
                                        setTimeout(function() {
                                            bootbox.hideAll();
                                        }, 1000);
                                    })
                            } else {
                                alert("Note cannot blank");
                            }
                        }
                    }
                }
            });
            $("#note").focus();
            setTimeout(function() {
                $("#note").focus()
            }, 500);
        })
}
/*
function sample_accept(pid,opd,ipd,batch_no)
{
	var all="";
	var samp=$(".samp");
	for(var i=0;i<samp.length;i++)
	{
		if($(samp[i]).is(":checked"))
		{
			all=all+"#"+samp[i].id+"$";
			var tst=$("."+samp[i].id);
			
			for(var j=0;j<tst.length;j++)
			{
				if(tst[j].checked)
				{		
					all=all+"@"+tst[j].value;
				}
				
			}
		}		
	}
	$.post("pages/phlebo_save_sample.php",
	{
		type:"save_sample",
		pid:pid,
		ipd_id:ipd,
		batch_no:batch_no,
		all:all,
		user:$("#user").text().trim(),
		sample_date:$("#sample_date").val(),
		sample_time:$("#sample_time").val(),
	},
	function(data,status)
	{
		bootbox.dialog({ message: "Saved"});
		setTimeout(function(){
			bootbox.hideAll();
			var stype=$("#search_type").val();			
			if(stype=="date")
			{
				view_all('date');
			}else if(stype=="name")
			{
				view_all('name');
			}else if(stype=="ids")
			{
				view_all('ids');
			}
			$("#rep").click();
		},1000);
	})
}
*/
function medication(batch) {
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_med_det",
        },
        function(data, status) {
            $("#accordian_data_load2").html(data);
            $("html,body").animate({
                scrollTop: '200px'
            }, 800);
            if (batch != 0)
                $("#batch" + batch).click();
            else {
                var b = document.getElementsByClassName("mbt");
                $("#batch" + b.length).click();
            }
            load_medi_det();
        })
}

function ad_med(batch, plan) {
    $("#med_mod").click();
    $("#ins_med").hide();
    $('#med_list').css('height', '100px');
    $.post("pages/global_load_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            batch: batch,
            plan: plan,
            type: "ipd_add_medicine",
        },
        function(data, status) {
            $("#med_list").html(data);
        })
}

function folow(c, id, j) {
    var top = c.top - 10;
    $("html,body").animate({
        scrollTop: '350px'
    }, 1000);
    $("#gter").fadeIn(500);
    $('#gter').css("top", top);
    $(".a").removeClass("clk");
    $("#a" + id + j).addClass("clk");
    $.post("pages/global_load_g.php", {
            id: id,
            sl: j,
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "ipd_pat_med_folow",
        },
        function(data, status) {
            $("#fol_med").html(data);
        })
}

function medi_given(stat, id, sl) {
    $.post("pages/global_insert_data_g.php", {
            id: id,
            sl: sl,
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            stat: stat,
            usr: $("#user").text().trim(),
            type: "ipd_pat_medi_given",
        },
        function(data, status) {
            med_admin();
            $("#fol_med").html(data);
            $("#gter").fadeOut(500);
        })
}

function investigation(batch) {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_inv_det",
        },
        function(data, status) {
            $("#accordian_data_load3").html(data);
            if (batch != 0)
                view_batch(batch);
            else {
                var b = document.getElementsByClassName("bt");
                $(".bt:first").click();
            }
            $("html,body").animate({
                scrollTop: '200px'
            }, 800);
        })
}

function load_datepicker_service() {
    $(".datepicker").attr("readonly", true);
    $.post("pages/ipd_dash_pat_ajax.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "datepicker_min_max"
        },
        function(data, status) {
            var res = data.split("@@");
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd',
                minDate: res[0],
                maxDate: res[1],
            });
        })
}
// IPD Consultation Note Start
function ip_consult() {
    $.post("pages/nursing_dashboard_consult_note_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text(),
            type: "pat_ipd_ip_consult",
        },
        function(data, status) {
            $("#accordian_data_load5").html(data);
            $("html,body").animate({
                scrollTop: '300px'
            }, 800);
        })
}

function ipd_save_new_note() {
    if ($("#con_doc").val() == 0) {
        alert("Select Doctor");
        return false;
    }
    $.post("pages/nursing_dashboard_consult_note_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            con_note_id: $("#con_note_id").val(),
            note_date: $("#con_doc_note_date").val(),
            ip_note: $("#ip_note").val().trim(),
            con_doc: $("#con_doc").val(),
            usr: $("#user").text().trim(),
            type: "ipd_save_note",
        },
        function(data, status) {
            alert(data);
            $("#close_btn_doc_note").click();
            ip_consult();
        })
}

function ipd_save_note() {
    $("#nt_btn").click();
    $.post("pages/nursing_dashboard_consult_note_data.php", {
            //type:"ipd_ip_add_doc",
            type: "ipd_ip_add_doc_new",
            id: "0",
        },
        function(data, status) {
            $("#res").html(data);
            $("#note_mod").css({
                "width": "90%",
                "left": "25%"
            });
            $('.textarea_editor').wysihtml5();
            $(".widget-content_con_doc_note").css({
                "padding": "0"
            });

            load_datepicker_service();
        })
}

function auto_note() {
    $.post("pages/nursing_dashboard_consult_note_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "ipd_auto_save_ip_note",
        },
        function(data, status) {

        })
}

function ip_note(id) {
    $("#nt_btn").click();
    $.post("pages/nursing_dashboard_consult_note_data.php", {
            id: id,
            //type:"ipd_ip_note_edit",
            type: "ipd_ip_add_doc_new",
        },
        function(data, status) {
            $("#res").html(data);
            $("#note_mod").css({
                "width": "90%",
                "left": "25%"
            });
            $('.textarea_editor').wysihtml5();
            $(".widget-content_con_doc_note").css({
                "padding": "0"
            });

            load_datepicker_service();
        })
}
// IPD Consultation Note End

//~ function save_ip_note(id)
//~ {
//~ $.post("pages/global_insert_data_g.php",
//~ {
//~ id:id,
//~ ip_note:$("#ip_note").val().trim(),
//~ usr:$("#user").text().trim(),
//~ type:"ipd_save_ip_note",
//~ },
//~ function(data,status)
//~ {
//~ ip_consult();
//~ })
//~ }

// Vital
function vital() {
    $.post("pages/nursing_dashboard_vital_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            view: "1",
            usr: $("#user").text().trim(),
            type: "pat_ipd_vital_det",
        },
        function(data, status) {
            $("#accordian_data_load4").html(data);
            $("#weight").focus();
            $("html,body").animate({
                scrollTop: '200px'
            }, 900);
        })
}

function add_vitals(val) {
    $.post("pages/nursing_dashboard_vital_data.php", {
            type: "add_vitals",
            id: val,
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
        },
        function(data, status) {
            setTimeout(function() {
                $("#vital_data").html(data);
                //$('.textarea_editor').wysihtml5();
                //$(".widget-content_con_doc_note").css({"padding":"0"});

                load_datepicker_service();

                $('.timepicker').timepicker({
                    showAnim: 'blind',
                });

                $("#vital_data").scrollTop(1000);

            }, 100);
        })
}

function view_vital(v) {
    $.post("pages/nursing_dashboard_vital_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            view: v,
            usr: $("#user").text().trim(),
            type: "pat_ipd_vital_det",
        },
        function(data, status) {
            $("#accordian_data_load4").html(data);
            $("html,body").animate({
                scrollTop: '350px'
            }, 900);
            if (v == 1)
                $("#weight").focus();
        })
}

function save_vital() {
    if ($("#record_date").val() == "") {
        alert("Select record date");
        $("#record_date").focus();
        return false;
    }
    if ($("#record_time").val() == "") {
        alert("Select record time");
        $("#record_time").focus();
        return false;
    }
    if ($("#record_by").val() == 0) {
        alert("Select record by");
        $("#record_by").focus();
        return false;
    }
    $.post("pages/nursing_dashboard_vital_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            vital_id: $("#vital_id").val(),
            weight: $("#weight").val(),
            height: $("#height").val(),
            mid_cum: $("#mid_cum").val(),
            hd_cum: $("#hd_cum").val(),
            bmi1: $("#bmi1").val(),
            bmi2: $("#bmi2").val(),
            spo: $("#spo").val(),
            pulse: $("#pulse").val(),
            temp: $("#temp").val(),
            pr: $("#pr").val(),
            rr: $("#rr").val(),
            systolic: $("#systolic").val(),
            diastolic: $("#diastolic").val(),
            vit_note: $("#vit_note").val(),
            intake_output_record: $("#intake_output_record").val(),
            record_by: $("#record_by").val(),
            record_date: $("#record_date").val(),
            record_time: $("#record_time").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_vital_save",
        },
        function(data, status) {
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
                view_vital("1");
            }, 1000);
            //$("html,body").animate({scrollTop: '350px'},900);
        })
}
//


// Insulin start
function insulin() {
    $.post("pages/nursing_dashboard_insuline_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_insulin_det",
        },
        function(data, status) {
            $("#accordian_data_load34").html(data);
            $("#weight").focus();
            $("html,body").animate({
                scrollTop: '200px'
            }, 900);
        })
}

function add_insulin(val) {
    $.post("pages/nursing_dashboard_insuline_data.php", {
            type: "add_insulin",
            slno: val,
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
        },
        function(data, status) {
            setTimeout(function() {
                $("#insulin_data").html(data);
                //$('.textarea_editor').wysihtml5();
                //$(".widget-content_con_doc_note").css({"padding":"0"});

                load_datepicker_service();

                $('.timepicker').timepicker({
                    showAnim: 'blind',
                });

                $("#insulin_data").scrollTop(1000);

            }, 100);
        })
}

function view_insulin() {
    insulin();
}

function save_insulin() {
    if ($("#insulin_id").val() == 0) {
        alert("Select insulin");
        $("#insulin_id").focus();
        return false;
    }
    if ($("#insulin_dosage").val() == "") {
        alert("Enter Dosage");
        $("#insulin_dosage").focus();
        return false;
    }
    if ($("#insulin_consultantdoctorid").val() == 0) {
        alert("Enter Doctor");
        $("#insulin_consultantdoctorid").focus();
        return false;
    }
    if ($("#insulin_given_by").val() == 0) {
        alert("Select given by");
        $("#insulin_given_by").focus();
        return false;
    }
    if ($("#insulin_given_date").val() == "") {
        alert("Select given date");
        $("#insulin_given_date").focus();
        return false;
    }
    if ($("#insulin_given_time").val() == "") {
        alert("Select given time");
        $("#insulin_given_time").focus();
        return false;
    }

    $.post("pages/nursing_dashboard_insuline_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            insulin_id: $("#insulin_id").val(),
            insulin_dosage: $("#insulin_dosage").val(),
            insulin_note: $("#insulin_note").val(),
            consultantdoctorid: $("#insulin_consultantdoctorid").val(),
            insulin_given_by: $("#insulin_given_by").val(),
            insulin_given_date: $("#insulin_given_date").val(),
            insulin_given_time: $("#insulin_given_time").val(),
            insulin_slno: $("#insulin_slno").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_insulin_save",
        },
        function(data, status) {
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
                view_insulin();
            }, 1000);
            //$("html,body").animate({scrollTop: '350px'},900);
        })
}
// Insulin end

function physical(val, e) {
    var ht = $("#height").val();
    if (ht != '' && val != '') {
        var ht = ht / 100;
        var bmi = (val / (ht * ht));
        var bmi = bmi.toFixed(2);
        var bmi = bmi.split(".");
        $("#bmi1").val(bmi[0]);
        $("#bmi2").val(bmi[1]);
    } else {
        $("#bmi1").val("");
        $("#bmi2").val("");
    }

    var unicode = e.keyCode ? e.keyCode : e.charCode;
    if (unicode == 13) {
        $("#height").focus();
    }
}

function physical1(val, e) {
    var wt = $("#weight").val();
    if (wt != '' && val != '') {
        var val = val / 100;
        var bmi = (wt / (val * val));
        var bmi = bmi.toFixed(2);
        var bmi = bmi.split(".");
        $("#bmi1").val(bmi[0]);
        $("#bmi2").val(bmi[1]);
    } else {
        $("#bmi1").val("");
        $("#bmi2").val("");
    }

    var unicode = e.keyCode ? e.keyCode : e.charCode;
    if (unicode == 13) {
        $("#mid_cum").focus();
    }
}

function addd() {
    $("#dl").click();
    $("#loader").show();
    $.post("pages/global_load_g.php", {
            type: "ipd_pat_add_diag",
        },
        function(data, status) {
            //$("#accordian_data_load1").html(data);
            //$("#dl").click();
            $("#loader").hide();
            $("#add_opt").html(data);
            setTimeout(function() {
                $("#diag").focus();
            }, 500);
        })
}

function ad() {
    var rr = document.getElementById("diag_table").rows.length;
    $("#loader").show();
    $.post("pages/global_load_g.php", {
            no: rr,
            type: "ipd_pat_doc_list",
        },
        function(data, status) {
            $("#loader").hide();
            var vl = data.split("@");
            if ($("#tr" + (rr - 1)).find('td:first input:first').val() && $("#tr" + (rr - 1)).find(
                    'td:eq(1) select:first').val() != "0" && $("#tr" + (rr - 1)).find('td:eq(2) select:first')
            .val() != "0" && $("#tr" + (rr - 1)).find('td:eq(3) select:first').val() != "0")
                $('#diag_table').append('<tr id="tr' + rr + '"><td><input list="browsr' + rr +
                    '" type="text" class="span4" onkeyup="diagtab(1,event)" id="diagnosis1" placeholder="Diagnosis" /><span id="diagn' +
                    rr + '">' + vl[0] +
                    '</span></td><td><select id="order1" onkeyup="diagtab(1,event)" class="span2"><option value="0">--Select--</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td><td><select id="cert1" onkeyup="diagtab(1,event)" class="span2"><option value="0">--Select--</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td><td><select id="doc"><option value="0">Select</option>' +
                    vl[1] +
                    '</select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>'
                    );
        })
}

function rcv_sample(uhid, ipd, batch) {
    $("#loader").show();
    $("#rep").click();
    $.post("pages/phlebo_load_sample.php", {
            uhid: uhid,
            ipd: ipd,
            opd: '',
            batch_no: batch,
            lavel: $("#lavel_id").val(),
            user: $("#user").text().trim(),
        },
        function(data, status) {
            $("#loader").hide();
            $("#result").html(data);
            load_vaccu();
            //$("#mod").click();
            //$("#results").fadeIn(500,function(){ load_vaccu(); })
        })
}

function save() {
    var diag = "";
    var rr = document.getElementById("diag_table").rows.length;
    for (var j = 1; j < rr; j++) {
        if ($("#tr" + j).find('td:first input:first').val() && $("#tr" + j).find('td:eq(1) select:first').val() !=
            "0" && $("#tr" + j).find('td:eq(2) select:first').val() != "0" && $("#tr" + j).find('td:eq(3) select:first')
            .val() != "0")
            diag += $("#tr" + j).find('td:first input:first').val() + "@" + $("#tr" + j).find('td:eq(1) select:first')
            .val() + "@" + $("#tr" + j).find('td:eq(2) select:first').val() + "@" + $("#tr" + j).find(
                'td:eq(3) select:first').val() + "#g#";
    }
    $("#loader").show();
    $.post("pages/global_insert_data_g.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            diag: diag,
            usr: $("#user").text().trim(),
            type: "save_ipd_pat_diag_nurse",
        },
        function(data, status) {
            $("#loader").hide();
            bootbox.dialog({
                message: data
            });
            setTimeout(function() {
                bootbox.hideAll();
                diagnosis();
            }, 1000);
        })
}

function discharge_request() {
    $.post("pages/discharge_request_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "discharge_req",
        },
        function(data, status) {
            $("#accordian_data_load18").html(data);
            discharge_request_det();
            $("html,body").animate({
                scrollTop: '600px'
            }, 1000);
        })
}

function dis_req() {
    bootbox.dialog({
        message: "<h5>Are you sure want to send discharge request</h5>",
        buttons: {
            cancel: {
                label: '<i class="icon-remove"></i> Cancel',
                className: "btn btn-inverse",
                callback: function() {
                    bootbox.hideAll();
                }
            },
            confirm: {
                label: '<i class="icon-ok"></i> Send',
                className: "btn btn-danger",
                callback: function() {
                    $.post("pages/discharge_request_data.php", {
                            uhid: $("#uhid").val(),
                            ipd: $("#ipd").val(),
                            usr: $("#user").text().trim(),
                            type: "discharge_req_send",
                        },
                        function(data, status) {
                            discharge_request();
                        })
                }
            }
        }
    });
}

function dis_req_cancel() {
    bootbox.dialog({
        message: "<h5>Are you sure want to cancel discharge request</h5>",
        buttons: {
            cancel: {
                label: '<i class="icon-remove"></i> Cancel',
                className: "btn btn-inverse",
                callback: function() {
                    bootbox.hideAll();
                }
            },
            confirm: {
                label: '<i class="icon-ok"></i> OK',
                className: "btn btn-danger",
                callback: function() {
                    $.post("pages/discharge_request_data.php", {
                            uhid: $("#uhid").val(),
                            ipd: $("#ipd").val(),
                            usr: $("#user").text().trim(),
                            type: "discharge_req_cancel",
                        },
                        function(data, status) {
                            discharge_request();
                        })
                }
            }
        }
    });
}

function lab_copy(uhid, ipd, batch) {
    var user = $("#user").text().trim();
    url = "pages/ipd_lab_copy.php?uhid=" + uhid + "&ipd=" + ipd + "&batch=" + batch + "&user=" + user;
    window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}

function print_batch_bill(uhid, ipd, batch) {
    var user = $("#user").text().trim();
    url = "pages/ipd_batch_wise_test_bill.php?uhid=" + btoa(uhid) + "&ipdid=" + btoa(ipd) + "&batch=" + btoa(batch) +
        "&user=" + btoa(user);
    window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}

function weight_ui_div() {
    $("#ui-timepicker-div").css("left", "257px");
}

function pr_vaccine(uhid, baby) {
    var uhid = btoa(uhid);
    var baby = btoa(baby);
    url = "pages/vaccine_details_rpt.php?uhid=" + uhid + "&baby_id=" + baby;
    wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}

///////// Consultant Doctor Transfer Start
function pat_consultant_doctor_change(dis) {
    $.post("pages/nursing_dashboard_data.php", {
            type: "pat_consultant_doctor_change",
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            attend_doc: $(dis).val(),
            user: $("#user").text().trim(),
        },
        function(data, status) {
            bootbox.dialog({
                message: "<b>Saved</b>"
            });
            setTimeout(function() {
                bootbox.hideAll();

                setTimeout(function() {
                    window.location.reload(true);
                }, 100);
            }, 2000);
        })
}

function upd_doc() {
    if ($("#adm_doc").val() == 0) {
        $("#adm_doc").focus();
    } else {
        $.post("pages/nursing_dashboard_data.php", {
                uhid: $("#uhid").val(),
                ipd: $("#ipd").val(),
                adm_doc: $("#adm_doc").val(),
                usr: $("#user").text().trim(),
                type: 902,
            },
            function(data, status) {
                bootbox.dialog({
                    message: data
                });
                setTimeout(function() {
                    bootbox.hideAll();
                    //shift_pat();
                    window.location.reload(true);
                }, 2000);
            })
    }
}

function shift_pat() {
    $.post("pages/nursing_dashboard_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: 901,
        },
        function(data, status) {
            $("#accordian_data_load23").html(data);
        })
}

function delete_ipd_con_doc(slno, attend_doc) {
    bootbox.dialog({
        //title: "Patient Re-visit ?",
        message: "<h5>Are you sure want to delete ?</h5>",
        buttons: {
            cancel: {
                label: '<i class="icon-remove"></i> Cancel',
                className: "btn btn-inverse",
                callback: function() {
                    bootbox.hideAll();
                }
            },
            confirm: {
                label: '<i class="icon-ok"></i> Delete',
                className: "btn btn-danger",
                callback: function() {
                    $.post("pages/nursing_dashboard_data.php", {
                            slno: slno,
                            attend_doc: attend_doc,
                            uhid: $("#uhid").val(),
                            ipd: $("#ipd").val(),
                            usr: $("#user").text().trim(),
                            type: 903,
                        },
                        function(data, status) {
                            bootbox.dialog({
                                message: data
                            });
                            setTimeout(function() {
                                bootbox.hideAll();
                                //shift_pat();
                                window.location.reload(true);
                            }, 2000);
                        })
                }
            }
        }
    });
}

function print_regd_receit(val) {
    var uhid = $("#uhid").val();
    var ipd = $("#ipd").val();
    var usr = $("#user").text().trim();
    if (val == 1) {
        url = "pages/admission_sheet.php?uhid=" + btoa(uhid) + "&ipd=" + btoa(ipd) + "&user=" + btoa(usr);
    }
    if (val == 2) {
        url = "pages/print_regd_form.php?uhid=" + uhid + "&ipd=" + ipd + "&user=" + usr;
    }
    wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}
///////// Consultant Doctor Transfer End

function edit_bed_pat() {
    alert();
}

//----Koushik---//
function select_all() {
    if ($("#sel_all").val() == "Select All") {
        $(".icon-check-empty").prop("class", "icon-check");
        $("#sel_all").val("De-Select All");
        $("#sel_all").html("<i class='icon-list-ul'></i> De-Select All");
        $(".icon-check").parent().css({
            'background-color': 'rgb(146, 217, 146)'
        });
    } else if ($("#sel_all").val() == "De-Select All") {
        $(".icon-check:not('[name=vacc_done]')").prop("class", "icon-check-empty");
        $("#sel_all").val("Select All");
        $("#sel_all").html("<i class='icon-list-ul'></i> Select All");
        $(".icon-check-empty").parent().css({
            'background-color': 'rgb(234, 164, 130)'
        });
    }
}

function check_vac(elem, val) {
    if ($("#" + val + "").prop("class") == "icon-check") {
        $("#" + val + "").prop("class", "icon-check-empty")
        $("#smp_td_" + elem + "").css({
            'background-color': 'rgb(234, 164, 130)'
        });
    } else {
        $("#" + val + "").prop("class", "icon-check")
        $("#smp_td_" + elem + "").css({
            'background-color': 'rgb(146, 217, 146)'
        });
    }
}

function check_vac_err(name) {
    bootbox.dialog({
        message: "<b style='color:red'>" + name + " is already processed. Can not be removed</b>"
    });
    setTimeout(function() {
        bootbox.hideAll();
    }, 2500)
}

function sample_accept(pid, opd, ipd, batch_no) {
    bootbox.dialog({
        message: "<p id='phlb_msg'><b>Saving...</b></p>"
    });

    var vac = "";
    var vac_l = $(".icon-check");
    for (var i = 0; i < vac_l.length; i++) {
        if ($(vac_l[i]).prop("id")) {
            vac += "@@" + $(vac_l[i]).prop("id");
        }
    }

    var vac_n = "";
    var vac_l_n = $(".icon-check-empty");
    for (var j = 0; j < vac_l_n.length; j++) {
        if ($(vac_l_n[j]).prop("id")) {
            vac_n += "@@" + $(vac_l_n[j]).prop("id");
        }
    }

    $.post("pages/phlebo_save_sample.php", {
            pid: pid,
            opd_id: opd,
            ipd_id: ipd,
            batch_no: batch_no,
            vac: vac,
            vac_n: vac_n,
            user: $("#user").text()
        },
        function(data, status) {
            $("#phlb_msg").html("<b>Saved. Redirecting to Barcode Generation</b>");
            setTimeout(function() {
                //view_all();
                bootbox.hideAll();
                var user = $("#user").text();
                var url = "pages/barcode_generate.php?pid=" + pid + "&opd_id=" + opd + "&ipd_id=" + ipd +
                    "&batch_no=" + batch_no + "&user=" + user + "&vac=" + vac;
                window.open(url, '', 'fullscreen=yes,scrollbars=yes');

            }, 1000);
        })
}

function hid_mod() {
    $("#rep").click();
}

function barcode_single(pid, opd, ipd, batch_no, vc) {
    var user = $("#user").text();
    var url = "pages/barcode_generate.php?pid=" + pid + "&opd_id=" + opd + "&ipd_id=" + ipd + "&batch_no=" + batch_no +
        "&user=" + user + "&vac=" + vc + "&sing=" + 1;
    window.open(url, '', 'fullscreen=yes,scrollbars=yes');
}

function vac_note(pid, opd, ipd, batch_no, vac, vname) {
    bootbox.dialog({
        title: 'Add Note For ' + vname,
        message: "<p><input type='text' id='note_text_" + vac + "' style='width:90%'/></p>",
        size: 'large',
        buttons: {
            save: {
                label: "<i class='icon-save'></i> Save",
                className: 'btn-success',
                callback: function() {
                    $.post("pages/phlebo_sample_note.php", {
                            pid: pid,
                            opd_id: opd,
                            ipd_id: ipd,
                            batch_no: batch_no,
                            vac: vac,
                            note: $("#note_text_" + vac + "").val(),
                            user: $("#user").text()
                        },
                        function(data, status) {
                            if (data == 1) {
                                $("#note_" + vac + "").prop("class", "btn btn-success btn-mini");
                                $("#note_" + vac + "").val("view");
                                $("#note_" + vac + "").html("<i class='icon-comments-alt'></i> View");

                                $("#vac_saved_note_" + vac + "").val($("#note_text_" + vac + "").val());
                            } else {
                                $("#note_" + vac + "").prop("class", "btn btn-info btn-mini");
                                $("#note_" + vac + "").val("note");
                                $("#note_" + vac + "").html("<i class='icon-comments-alt'></i> Note");

                                $("#vac_saved_note_" + vac + "").val("");
                            }
                        })
                }
            },
            close: {
                label: "<i class='icon-off'></i> Close",
                className: 'btn-danger',
                callback: function() {
                    console.log('Custom button clicked');

                }
            }
        }
    });

    var nt = $("#vac_saved_note_" + vac + "").val();
    $("#note_text_" + vac + "").val(nt).focus();

}
//****Koushik****//

function investigation_entry(batch) {
    $.post("pages/nursing_dashboard_invest_entry_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "pat_ipd_inv_det_entry",
        },
        function(data, status) {
            $("#accordian_data_load31").html(data);
            if (batch != 0)
                view_batch(batch);
            else {
                var b = document.getElementsByClassName("bt");
                $(".bt:first").click();
            }
            $("html,body").animate({
                scrollTop: '250px'
            }, 800);
            load_entry_tab();
        })
}

function add_lab_entry() {
    $.post("pages/nursing_dashboard_invest_entry_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            usr: $("#user").text().trim(),
            type: "inv_det_entry_div",
        },
        function(data, status) {
            $("#inv_entry_mod").html(data);
        })
    $("#inv_entry").click();
}

function add_para(tst) {
    $.post("pages/nursing_dashboard_invest_entry_data.php", {
            tst: tst,
            type: "inv_det_tst_par",
        },
        function(data, status) {
            $("#test_data").html(data);
            $(".inv_entry_tst_" + tst + "")[0].focus();
        })
}

function save_test_entry(tst) {
    var par = "";
    var par_t = $(".inv_entry_tst_" + tst + "");
    for (var i = 0; i < par_t.length; i++) {
        par = par + "@@koushik@@" + $(par_t[i]).attr("id") + "##koushik##" + $(par_t[i]).val();
    }

    var nhour = parseInt($("#hour").val());
    if ($("#time_mer").val() == "PM") {
        nhour = parseInt(nhour + 12);
    }
    var time = nhour + ":" + $("#min").val() + ":10";

    $.post("pages/nursing_dashboard_invest_entry_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            user: $("#user").text().trim(),
            tst: tst,
            par: par,
            date: $("#entry_date").val(),
            time: time,
            type: "inv_det_tst_result",
        },
        function(data, status) {
            load_entry_tab();
            $("#inv_entry").click();
        })
}

function load_entry_tab() {
    $.post("pages/nursing_dashboard_invest_entry_data.php", {
            uhid: $("#uhid").val(),
            ipd: $("#ipd").val(),
            type: "inv_tst_result_data",
        },
        function(data, status) {
            $("#lab_entry_data").html(data);
        })
}

function print_req(uhid, ipd, batch, dep) {
    var user = $("#user").text().trim();
    url = "pages/phlebo_gen_req_ipd.php?uhid=" + uhid + "&ipd=" + ipd + "&batch=" + batch + "&dep=" + dep + "&user=" +
        user;
    window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}
</script>
<script src="../jss/medication_plan.js"></script>
<script src="../jss/post_medicine.js"></script>
<style>
#myAlert,
#myAlert1,
#myModal_med,
#medplan,
#myModal_post,
#repmodal {
    width: 80%;
    margin-left: -40%;
}

#myModal {
    left: 33%;
    width: 75%;
}

.ScrollStyle {
    max-height: 400px;
    overflow-y: scroll;
}

.btn_round_msg {
    color: #000;
    padding: 2px;
    border-radius: 7em;
    padding-right: 10px;
    padding-left: 10px;
    box-shadow: inset 1px 1px 0 rgba(0, 0, 0, 0.6);
    transition: all ease-in-out 0.2s;
}

.red {
    background-color: #d59a9a;
}

.green {
    background-color: #9dcf8a;
}

.yellow {
    background-color: #f6e8a8;
}

input[type="checkbox"]:not(old)+label,
input[type="radio"]:not(old)+label {
    display: inline-block;
    margin-left: 0;
    line-height: 1.5em;
}

input[type=checkbox] {
    margin: -3px 0 0;
}

.btt,
.btt:hover,
.btt:focus,
.clk,
.clk:hover,
.clk:focus {
    background: #708090;
    color: #ffffff;
}

#gter {
    background: #ffffff;
    color: #000000;
    box-shadow: 2px 2px 5px #000;
    padding: 5px 0px 5px 0px;
    font-size: 11px;
    font-family: verdana;
    width: 300px;
    position: absolute;
    left: 70%;
}

.modal.fade.in {
    top: 3%;
}

.modal-body {
    max-height: 540px;
}

.emer,
.emer:hover {
    background: #f8dcdc;
}

.txt {
    width: 100px;
}

table td .icon-check,
table td .icon-check-empty {
    display: block !important;
    transform: scale(1.5) !important;
    margin-top: 10px !important;
}

.tests_phlebo {
    display: inline-block;
    border-bottom: 1px solid #CCC;
    width: 100%;
}

#test_list th,
#test_list td {
    padding: 0;
}

.side_name {
    border: 1px solid #ddd;
    background-color: #fff;
    padding: 4px;
    position: absolute;
    font-weight: bold;
}

.err {
    border: 1px solid #FF0000;
}
</style>