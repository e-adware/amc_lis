<?php
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
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<td>
				<div class="input-daterange input-group datepicker">
					<!--<span class="input-group-addon side_name">From</span>
							<input class="form-control datepicker span2" type="text" name="txtfrom" id="txtfrom" value="<?php echo date('Y-m-d'); ?>"/>
							<span class="input-group-addon side_name">To</span>
							<input class="form-control datepicker span2" type="text" name="txtto" id="txtto" value="<?php echo date('Y-m-d'); ?>"/>-->

					<span class="side_name">From</span>
					<input class="form-control datepicker span2" type="text" name="from" id="txtfrom"
						value="<?php echo date('Y-m-d'); ?>" style="margin-left: 47px;" readonly>
					<span class="side_name">To</span>
					<input class="form-control datepicker span2" type="text" name="to" id="txtto"
						value="<?php echo date('Y-m-d'); ?>" style="margin-left: 26px;" readonly>
				</div>
			</td>
			<td>
				<input class="form-control" type="text" id="txtcntr" name="txtcntr" onkeyup="sel_pr(this.value,event)"
					style="display:none;" />
				<select id="branch_id" class="span3" style="<?php echo $branch_display; ?>" onChange="view_all()">
					<?php
					$branch_qry = mysqli_query($link, "SELECT `branch_id`, `name` FROM `company_name` WHERE `branch_id` > 0 $branch_str ORDER BY `branch_id` ASC");
					while ($branch = mysqli_fetch_array($branch_qry)) {
						$branch_sel = ($branch_id == $branch["branch_id"]) ? "selected" : "";
						echo "<option value='{$branch['branch_id']}' $branch_sel>{$branch['name']}</option>";
					}
					?>
				</select>
				<select id="txtcntrid">
					<option value="0">Select Center</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<center>
					<input type="button" name="button" id="button1" value="Patient Wise" class="btn btn-default all"
						onclick="allreport('1')" />
					<input type="button" name="button" id="button2" value="Test Wise" class="btn btn-default all"
						onclick="allreport('2')" />
					<input type="button" name="button" id="button3" value="Summary" class="btn btn-default all"
						onclick="allreport('3')" />
					<input type="button" name="button" id="button4" value="Balance" class="btn btn-default all"
						onclick="allreport('4')" />
					<?php
					if ($p_info["levelid"] == 1) {
						?>
						<input type="button" name="button" id="button5" value="Balance Receive" class="btn btn-default all"
							onclick="allreport('5')" />
						<?php
					}
					?>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<center>
					<input type="button" name="button" id="button6" value="Centre Bill" class="btn btn-default all"
						onclick="allreport('6')" />

					<input type="button" name="button" id="button6" value="Balance Summary" class="btn btn-default all"
						onclick="allreport('7')" />

				</center>
			</td>


		</tr>
	</table>
	<div id="load_data">

	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<input type="hidden" id="report_type">
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->
<script>
	$(document).ready(function () {
		view_all();		
		$("#loader").hide();
		$("#txtfrom").datepicker(
			{
				dateFormat: 'yy-mm-dd',
				maxDate: '0',
			});
		$("#txtto").datepicker(
			{
				dateFormat: 'yy-mm-dd',
				maxDate: '0',
			});
	});

	function view_all() {
		$.post("pages/collectioncenter_report.php",
			{
				type: "load_center",
				branch: $("#branch_id").val(),
			},
			function (data, status) {
				$("#txtcntrid").html(data);
			});
	}

	function allreport(r) {
		$("#loader").show();
		$("#report_type").val(r);
		$(".all").removeClass('btn-default');
		$(".all").removeClass('btn-primary');
		$("#button" + r).addClass('btn-primary');
		$.post("pages/collectioncenter_report.php",
			{
				rep: $("#report_type").val(),
				cid: $("#txtcntrid").val(),
				branch_id: $("#branch_id").val(),
				fdate: $("#txtfrom").val(),
				tdate: $("#txtto").val(),
				type: "collectionreport",
			},
			function (data, status) {
				$("#load_data").html(data)
				$("#loader").hide();

				$("#cheque_date").datepicker(
					{
						dateFormat: 'yy-mm-dd',
						maxDate: '0',
					});
			})
	}
	function print_rep(f, cid, bid) {
		var url = "";
		var tp = btoa(f);
		var cid = btoa(cid);
		var fdate = btoa($("#txtfrom").val());
		var tdate = btoa($("#txtto").val());
		url = "pages/collectioncenter_reports_print.php?cid=" + cid + "&fdate=" + fdate + "&tdate=" + tdate + "&type=" + tp + "&bid=" + btoa(bid);

		wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1300');
	}

	function chk_change(n) {
		count_balance();
	}
	function select(val) {
		if (val == 1) {
			$("#select_btn1").hide();
			$("#select_btn2").show();

			$(".chk").prop("checked", true);
		}
		if (val == 2) {
			$("#select_btn2").hide();
			$("#select_btn1").show();

			$(".chk").prop("checked", false);
		}

		$("#total_tax_amount").val(0);

		count_balance();
	}
	function count_balance() {
		var balance = 0;

		var chk = $(".chk:checked");
		for (var i = 0; i < chk.length; i++) {
			var slno = $(chk[i]).val();

			balance = parseInt(balance) + parseInt($("#balance" + slno).val());
		}
		//$("#total_balance_text").text(balance.toFixed(2));
		$("#total_balance_amount").val(balance);
		$("#total_receive_amount").val(balance);

		//~ if(balance==0)
		//~ {
		//~ $(".payment_table").hide();
		//~ }
		//~ else
		//~ {
		//~ $(".payment_table").show();
		//~ }
	}

	function total_receive_amount_up(dis) {
		$(dis).css({ "border-color": "#ccc" });

		var total_balance_amount = parseInt($("#total_balance_amount").val());
		if (!total_balance_amount) { total_balance_amount = 0; }

		var total_receive_amount = parseInt($("#total_receive_amount").val());
		if (!total_receive_amount) { total_receive_amount = 0; }

		var res_amount = total_balance_amount - total_receive_amount;

		if (res_amount < 0) {
			$(dis).css({ "border-color": "red" });
			return false
		}

		$("#total_tax_amount").val(res_amount);
	}

	function total_tax_amount_up(dis) {
		$(dis).css({ "border-color": "#ccc" });

		var total_balance_amount = parseInt($("#total_balance_amount").val());
		if (!total_balance_amount) { total_balance_amount = 0; }

		var total_tax_amount = parseInt($("#total_tax_amount").val());
		if (!total_tax_amount) { total_tax_amount = 0; }

		var res_amount = total_balance_amount - total_tax_amount;

		if (res_amount < 0) {
			$(dis).css({ "border-color": "red" });
			return false
		}

		$("#total_receive_amount").val(res_amount);
	}
	function calc(dis) {
		var total_balance_amount = parseInt($("#total_balance_amount").val());
		if (!total_balance_amount) { total_balance_amount = 0; }

		var total_receive_amount = parseInt($("#total_receive_amount").val());
		if (!total_receive_amount) { total_receive_amount = 0; }

		var total_tax_amount = parseInt($("#total_tax_amount").val());
		if (!total_tax_amount) { total_tax_amount = 0; }

		var res_amount = total_balance_amount - total_receive_amount - total_tax_amount;
		if (res_amount != 0) {
			$(dis).css({ "border-color": "red" });
		}
	}

	function save_balance() {
		var chk = $(".chk:checked");

		if (chk.length == 0) {
			alert("Select Patient");
			return false;
		}

		var all_pat = "";
		for (var i = 0; i < chk.length; i++) {
			var slno = $(chk[i]).val();

			all_pat = all_pat + "@#@" + $("#patient_id" + slno).val() + "##" + $("#opd_id" + slno).val();
		}

		if (all_pat == "") {
			alert("Select Patient.");
			return false;
		}

		var total_balance_amount = parseInt($("#total_balance_amount").val());
		if (!total_balance_amount) { total_balance_amount = 0; }

		var total_receive_amount = parseInt($("#total_receive_amount").val());
		if (!total_receive_amount) { total_receive_amount = 0; }

		var total_tax_amount = parseInt($("#total_tax_amount").val());
		if (!total_tax_amount) { total_tax_amount = 0; }

		if (total_balance_amount == 0) {
			alert("Balance amount can't be zero.");
			return false;
		}

		if ($("#payment_mode").val() == "") {
			$("#payment_mode").focus();
			return false;
		}

		if ($("#cheque_ref_no").val() == "") {
			$("#cheque_ref_no").prop("placeholder", "Enter Cheque/Ref No.").focus();
			return false;
		}

		bootbox.dialog({
			//title: " ?",
			message: "<h5>Are you sure want to save balance ?</h5>",
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
						$("#save_tr").hide();
						$.post("pages/collectioncenter_report.php",
							{
								type: "save_balance",
								all_pat: all_pat,
								payment_mode: $("#payment_mode").val(),
								total_balance_amount: total_balance_amount,
								total_receive_amount: total_receive_amount,
								total_tax_amount: total_tax_amount,
								cheque_date: $("#cheque_date").val(),
								cheque_ref_no: $("#cheque_ref_no").val(),
								bank_name: $("#bank_name").val(),
								branch_name: $("#branch_name").val(),
								centreno: $("#txtcntrid").val(),
								date1: $("#txtfrom").val(),
								date2: $("#txtto").val(),
							},
							function (data, status) {
								alert(data);
								allreport('4');
							})
					}
				}
			}
		});
	}

	function receipt_print(slno, centreno) {
		url = "pages/collectioncenter_receipt_print.php?v=" + btoa(1234567890) + "&centreno=" + btoa(centreno) + "&slno=" + btoa(slno);

		wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1300');
	}
</script>
<style>
	#load_data {
		max-height: 400px;
		overflow-y: scroll;
	}

	.side_name {
		border: 1px solid #ddd;
		background-color: #fff;
		padding: 4px;
		position: absolute;
		font-weight: bold;
	}
</style>