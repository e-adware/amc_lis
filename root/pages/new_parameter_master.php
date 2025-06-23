<?php
$branch_str = " AND `branch_id`='$p_info[branch_id]'";
$element_style = "display:none";
if ($p_info["levelid"] == 1) {
	$branch_str = "";

	$element_style = "display:none;";
	$branch_num = mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if ($branch_num > 1) {
		$element_style = "display:;";
	}
}
?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<center>
		<span class="side_name">Search</span>
		<input class="span4" type="text" id="search_param_name" placeholder="Search Parameter" style="margin-left: 56px;" onkeyup="search_param_name()" autofocus>
		<select id="search_testid" class="span5" onchange="load_param_list()">
			<option value="0">--Select Test--</option>
			<?php
			$tst = mysqli_query($link, "select * from testmaster WHERE `testname`!='' order by testname");
			while ($t = mysqli_fetch_array($tst)) {
				echo "<option value='$t[testid]'>$t[testname]</option>";
			}
			?>
		</select>
		<select id="search_ResultTypeId" class="span2" onchange="load_param_list()">
			<option value="">--Select ResultType--</option>
			<?php
			$tst = mysqli_query($link, "SELECT `ResultTypeId`, `ResultType_name` FROM `ResultType` WHERE `ResultType_name`!='' ORDER BY `ResultType_name` ASC");
			while ($t = mysqli_fetch_array($tst)) {
				echo "<option value='$t[ResultTypeId]'>$t[ResultType_name]</option>";
			}
			?>
		</select>
		<button class="btn btn-search" onclick="load_param_list()"><i class="icon-search"></i> Search</button>
		<button class="btn btn-new" onClick="load_param(0)" style="margin-bottom: 10px;"><i class="icon-edit"></i> New
			Parameter</button>
	</center>
	<div id="load_list"></div>
	<div id="load_info"></div>
</div>
<button type="button" class="btn btn-info" id="modal_btn" data-toggle="modal" data-target="#myModal"
	style="display:none;">Open Modal</button>
<div id="myModal" class="modal fade modal_main" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<!--<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"></h4>
			</div>-->
			<div class="modal-body" id="modal_load_data">
			</div>
			<div class="modal-footer" style="display:none;">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
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

<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>

<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>

<script>
	$(document).ready(function () {
		$("#loader").hide();
		$("#search_testid").select2({ theme: "classic" });
		$("#search_ResultTypeId").select2({ theme: "classic" });

		load_param_list();
	});

	var _changeInterval = null;
	function search_param_name() {
		clearInterval(_changeInterval)
		_changeInterval = setInterval(function () {
			// Typing finished, now you can Do whatever after 2 sec
			clearInterval(_changeInterval);
			load_param_list();
		}, 500);
	}
	function load_sample_vaccu(ths)
	{
		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
		{
			samp:$(ths).val(),
			type:"load_sample_vaccu",
		},
		function(data, status)
		{
			$("#loader").hide();
			//alert(data);
			$("#vaccu option:not(:first)").remove();
			var vl=JSON.parse(data);
			for(var i=0; i<(vl.length); i++)
			{
				$("#vaccu").append("<option value='"+vl[i]['id']+"'>"+vl[i]['name']+"</option>");
			}
		});
	}

	function load_param_list() {
		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "load_param_list",
				param_name: $("#search_param_name").val(),
				testid: $("#search_testid").val(),
				ResultTypeId: $("#search_ResultTypeId").val(),
			},
			function (data, status) {
				$("#loader").hide();
				$("#load_info").slideUp(300);
				$("#load_list").slideDown(500).html(data);
			})
	}

	function load_param(paramid) {
		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "load_param",
				paramid: paramid,
			},
			function (data, status) {
				$("#loader").hide();
				$("#load_list").slideUp(300);
				$("#load_info").slideDown(500).html(data);

				$("#Name").focus();
			})
	}

	function back_to_list() {
		//~ $("#load_info").slideUp(300);
		//~ $("#load_list").slideDown(500);

		load_param_list();
	}

	function new_unit() {
		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "new_unit",
			},
			function (data, status) {
				$("#loader").hide();
				$("#modal_load_data").html(data);

				$("#modal_btn").click();

				setTimeout(function () {
					$("#unit_name").focus();
				}, 100);
			})
	}

	function unit_save() {
		if ($("#unit_name").val().trim() == "") {
			return false;
		}

		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "unit_save",
				unit_name: $("#unit_name").val().trim()
			},
			function (data, status) {
				$("#loader").hide();

				if (data != "") {
					alertmsg("Saved", 1);

					$("#UnitsID").html(data);
				} else {
					alertmsg("Failed, try again later.", 1);
				}
			})
	}

	function load_option_list(paramid, ResultOptionID) {
		if ($("#ResultType").val() != 2) // List of Choice
		{
			return false;
		}

		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "load_option_list",
				paramid: paramid,
				ResultOptionID: ResultOptionID,
			},
			function (data, status) {
				$("#loader").hide();

				$("#modal_load_data").html(data);

				$("#modal_btn").click();

				setTimeout(function () {
					show_option(ResultOptionID);
				}, 500);
			})
	}
	function show_option(ResultOptionID) {
		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "show_option",
				ResultOptionID: ResultOptionID,
			},
			function (data, status) {
				$("#loader").hide();

				$("#option_val").html(data);
			})
	}

	function result_option_save(paramid) {
		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "result_option_save",
				paramid: paramid,
				optionList: $("#optionList").val(),
			},
			function (data, status) {
				$("#loader").hide();

				alertmsg(data, 1);

				setTimeout(function () {
					if (data == "Saved" || data == "Added") {
						$("#optionName").val($("#optionList option:selected").text());
						$("#ResultOptionID").val($("#optionList").val());
					}

					$("#result_option_close_btn").click();
				}, 1000);
			})
	}

	// Formula Start
	function load_formula_div(paramid) {
		if ($("#ResultType").val() != 6) // Formula
		{
			return false;
		}

		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "load_formula_div",
				paramid: paramid,
			},
			function (data, status) {
				$("#loader").hide();

				$("#modal_load_data").html(data);

				$("#modal_btn").click();
			})
	}

	function add_element(val) {
		$("button").attr("disabled", false);
		if (val == "add_p") {
			$("#" + val + "").attr("disabled", true);
			var b = $('#parm').clone().attr({
				"name": "param",
				"id": "",
				"class": "formula"
			}).val($("#parm").val());
			$("#formula_text").append(b);
		} else if (val == "add_op") {
			$("#" + val + "").attr("disabled", true);
			$("#formula_text").append("<input type='text' value='" + $("#opr").val() + "' name='operator' class='formula span1' maxlength='1' size='1'/>");
		} else if (val == "add_num") {
			$("#" + val + "").attr("disabled", true);
			$("#formula_text").append("<input type='text' value='" + $("#num").val() + "' name='numeric' size='3' class='formula span1' onkeyup='check_num(this)'/>");
		}
		$('#formula_text').animate({ scrollTop: $('#formula_text').prop("scrollHeight") }, 500);
	}
	function save_formula(paramid) {
		var formula = "";
		var elem = $("#formula_text .formula");
		for (var i = 0; i < elem.length; i++) {
			if ($(elem[i]).attr("name") == "param") {
				var param = $(elem[i]).val();
				formula += "p" + param;
			}
			if ($(elem[i]).attr("name") == "operator") {
				var opr = $(elem[i]).val();
				formula += "@" + opr + "@";
			}
			if ($(elem[i]).attr("name") == "numeric") {
				var num = $(elem[i]).val();
				formula += num;
			}
		}
		$.post("pages/new_parameter_master_data.php", {
			type: "save_formula",
			paramid: paramid,
			formula: formula,
			res_dec: $("#dec").val(),
		},
			function (data, status) {
				//alert(data);
				var res = data.split("@");
				if (res[0] == 101) {
					alertmsg(res[1], 1);
				}
				else {
					alertmsg(res[1], 0);
				}
			})
	}
	// Formula End

	// Normal Range Start
	function load_normal_range_div(paramid, val) {
		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "load_normal_range_div",
				paramid: paramid,
			},
			function (data, status) {
				$("#loader").hide();

				$("#modal_load_data").html(data);

				if (val == 1) {
					$("#modal_btn").click();
				}
			})
	}
	function normal_update_opt(slno, paramid, elem) {
		var val = $(elem).val();
		if (val == 2) {
			normal_add(paramid, slno);
		} else if (val == 0 | val == 1) {
			update_normal_stat(paramid, slno, elem);
		} else if (val == 3) {
			remove_normal_range(paramid, slno);
		}
	}
	function normal_add(paramid, slno) {
		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "normal_add",
				paramid: paramid,
				slno: slno,
			},
			function (data, status) {
				$("#loader").hide();
				//$("#normal_range_list_div").slideUp(300);
				$("#normal_range_form_div").slideDown(500).html(data);

				$("#Name").focus();
			})
	}
	function normal_save(paramid, slno) {
		if ($("#n_range").val().trim() == "") {
			$("#n_range").focus();
			return false;
		}

		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "normal_save",
				paramid: paramid,
				slno: slno,
				instrument_id: $("#instrument_id").val(),
				dep_id: $("#dep_id").val(),
				sex: $("#sex").val(),
				a_from: $("#a_from").val().trim(),
				a_from_typ: $("#a_from_typ").val(),
				a_to: $("#a_to").val().trim(),
				a_to_typ: $("#a_to_typ").val(),
				val_f: $("#val_f").val().trim(),
				val_t: $("#val_t").val().trim(),
				n_range: $("#n_range").val().trim(),
			},
			function (data, status) {
				$("#loader").hide();
				//alert(data);
				var res = data.split("@");
				if (res[0] == 101) {
					alertmsg(res[1], 1);
					load_normal_range_div(paramid, 0);
				}
				else {
					alertmsg(res[1], 0);
				}
			})
	}
	function update_normal_stat(paramid, slno, dis) {
		if ($(dis).val() == 0) {
			var msg = "Are you sure want to Active ?";
			var status = 0;
		} else {
			var msg = "Are you sure want to In-active ?";
			var status = 1;
		}

		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>" + msg + "</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function () {
						bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function () {
						$("#loader").show();
						$.post("pages/new_parameter_master_data.php",
							{
								type: "update_normal_stat",
								paramid: paramid,
								slno: slno,
								status: status,
							},
							function (data, status) {
								$("#loader").hide();
								var res = data.split("@");
								if (res[0] == 101) {
									alertmsg(res[1], 1);
								}
								else {
									alertmsg(res[1], 0);
								}
								load_normal_range_div(paramid, 0);
							})
					}
				}
			}
		});
	}

	function remove_normal_range(paramid, slno) {
		var msg = "Are you sure want to remove ?";

		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>" + msg + "</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function () {
						bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function () {
						$("#loader").show();
						$.post("pages/new_parameter_master_data.php",
							{
								type: "remove_normal_range",
								paramid: paramid,
								slno: slno,
							},
							function (data, status) {
								$("#loader").hide();
								var res = data.split("@");
								if (res[0] == 101) {
									alertmsg(res[1], 1);
								}
								else {
									alertmsg(res[1], 0);
								}
								load_normal_range_div(paramid, 0);
							})
					}
				}
			}
		});
	}

	function show_all_ranges(val) {
		if (val == 1) {
			$("#in_active_range_btn").hide();
			$("#active_range_btn").show();

			$(".norm_stat_1").show();
		}
		if (val == 0) {
			$("#active_range_btn").hide();
			$("#in_active_range_btn").show();

			$(".norm_stat_1").hide();
		}
	}
	// Normal Range End

	// Save
	function save_param(paramid) {
		if ($("#Name").val().trim() == "") {
			$("#Name").focus();
			return false;
		}

		$("#loader").show();
		$.post("pages/new_parameter_master_data.php",
			{
				type: "save_param",
				paramid: paramid,
				Name: $("#Name").val().trim(),
				ResultType: $("#ResultType").val(),
				UnitsID: $("#UnitsID").val(),
				ResultOptionID: $("#ResultOptionID").val(),
				sample: $("#sample").val(),
				vaccu: $("#vaccu").val(),
				method: $("#method").val(),
				deci_val: $("#deci_val").val(),
				user: $("#user").text().trim(),

			},
			function (data, status) {
				$("#loader").hide();
				// alert(data);

				var res = data.split("@");
				if (res[1] == 101) {
					alertmsg(res[2], 1);

					load_param(res[0]);
				}
				else {
					alertmsg(res[2], 0);
				}
			})
	}
	
	function delete_param(paramid) {
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h4>Are you sure want to delete?</h4>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function () {
						bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-danger",
					callback: function () {
						$("#loader").show();
						$.post("pages/new_parameter_master_data.php",
						{
							type: "delete_param",
							paramid: paramid,
						},
						function (data, status) {
							$("#loader").hide();
							// alert(data);

							var res = data.split("@");
							if (res[1] == 101) {
								alertmsg(res[2], 1);

								load_param_list();
							}
							else {
								alertmsg(res[2], 0);
							}
						})
					}
				}
			}
		});
	}

	function alertmsg(msg, n) {
		$.gritter.add({
			//title:'Normal notification',
			text: '<h5 style="text-align:center;">' + msg + '</h5>',
			time: 2000,
			sticky: false
		});
		if (n > 0) {
			$(".gritter-item").css("background", "#237438");
		}
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

	#load_list {
		max-height: 600px;
		overflow-y: scroll;
	}

	#myModal1 {
		height: 600px;
	}

	.modal.fade.in {
		top: 0%;
	}

	.modal_main {
		width: 65%;
		left: 32%;
		z-index: 999 !important;
	}

	.modal-body {
		max-height: 650px;
	}

	.modal-backdrop {
		z-index: 990 !important;
	}

	#gritter-notice-wrapper {
		position: fixed;
		top: 50px;
		right: 45%;
		width: 301px;
		z-index: 99999;
	}

	.norm_stat_1 {
		display: none;
	}
</style>
