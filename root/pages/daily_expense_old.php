	<script src="include/js/jquery-ui.js"></script>
	<!-- Time -->
	<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
	<script>
		$(document).ready(function() {
			$(".datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				maxDate: '0',
			});
		})

		function load_pinfo_date(typ) {
			$("#main_ser_typ").val(typ);
			$.post("pages/expense_detail_ajax.php", {
					type: 1,
					en_fdate: $("#en_fdate").val(),
					en_tdate: $("#en_tdate").val(),
					ex_fdate: $("#ex_fdate").val(),
					ex_tdate: $("#ex_tdate").val(),
					s_typ: typ
				},
				function(data, status) {
					$("#ex_rep").html(data);
				})
		}

		function add_expense() {
			$.post("pages/expense_detail_ajax.php", {
					type: 2
				},
				function(data, status) {
					$("#results").html(data);
					//$("#myModal").css({"width":'1000px'});
					if ($("#mod_chk").val() == 0) {
						$("#mod_chk").val("1");
						$("#mod").click();
						$("#results").fadeIn(500);
					}
				})

		}

		function load_details(val) {
			$.post("pages/expense_detail_ajax.php", {
					type: 4,
					ledge: $("#ledge_" + val + "").val(),
					ex_fdate: $("#ex_from").val(),
					ex_tdate: $("#ex_to").val(),
				},
				function(data, status) {
					//alert(data);
					$("#ex_rep").html(data);
				})
		}

		function save_exp(b_val) {
			if (b_val == "Delete") {

			}
			var imp = $(".imp");
			var err = 0;
			for (var i = 0; i < imp.length; i++) {
				if ($(imp[i]).val().trim() == "" || $(imp[i]).val().trim() == "0") {
					$(imp[i]).css({
						'border': '2px solid red'
					})
					err++;
				} else {
					$(imp[i]).css({
						'border': '1px solid #ccc'
					})
				}
			}

			if (err == 0 || b_val == "Delete") {
				$.post("pages/expense_detail_ajax.php", {
						type: 3,
						mode: $("#mode").val(),
						inv: $("#inv_no").val(),
						ledge: $("#ledge").val(),
						cheque_no: $("#cheque_no").val(),
						bank: $("#bank").val(),
						ex_date: $("#ex_date").val(),
						amount: $("#amount").val(),
						desc: $("#desc").val(),
						b_val: b_val,
						ex_sl: $("#exp_sl").val(),
						user: $("#user").text()
					},
					function(data, status) {
						if (b_val == "Save") {
							alert("Saved");
							load_ledge_details(data);
							if ($("#upd_ledger_id").length > 0) {
								$.post("pages/expense_detail_ajax.php", {
										type: 4,
										ledge: $("#upd_ledger_id").val(),
										ex_fdate: $("#ex_from").val(),
										ex_tdate: $("#ex_to").val(),

									},
									function(data, status) {

										$("#ex_rep").html(data);

									})
							} else {
								add_expense();
								load_pinfo_date(1);
							}
						} else if (b_val == "Update") {
							alert("Updated");
							$.post("pages/expense_detail_ajax.php", {
									type: 4,
									ledge: $("#upd_ledger_id").val(),
									ex_fdate: $("#ex_from").val(),
									ex_tdate: $("#ex_to").val(),

								},
								function(data, status) {
									$("#ex_rep").html(data);
								})
						} else {
							load_ledge_details();
							$.post("pages/expense_detail_ajax.php", {
									type: 4,
									ledge: $("#upd_ledger_id").val(),
									ex_fdate: $("#ex_from").val(),
									ex_tdate: $("#ex_to").val(),

								},
								function(data, status) {
									$("#ex_rep").html(data);

								})
						}
					})
			}
		}

		function load_ledge_details(val) {
			$.post("pages/expense_detail_ajax.php", {
					type: 2,
					val: val
				},
				function(data, status) {
					$("#results").html(data);
					$(".modal-dialog").css({
						"width": '1000px'
					});
					if ($("#mod_chk").val() == 0) {
						$("#mod_chk").val("1");
						$("#mod").click();
						$("#results").fadeIn(500);
					}
				})
		}

		function print_voucher(val) {
			var url = "pages/print_vouchar.php?sln=" + val
			var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
		}

		function load_voucher(val, e) {
			var unicode = e.keyCode ? e.keyCode : e.charCode;
			if (unicode == 13) {
				$.post("pages/expense_detail_ajax.php", {
						type: 5,
						val: val
					},
					function(data, status) {
						if (data) {
							load_ledge_details(data);
							$("#serach_v").css({
								'border': '1px solid #ccc'
							});
						} else {
							$("#serach_v").css({
								'border': '1px solid red'
							});
						}
					})
			}
		}

		function hid_div(e) {
			var unicode = e.keyCode ? e.keyCode : e.charCode;
			if (unicode == 27) {
				if ($("#mod_chk").val() == 1) {
					$("#mod_chk").val("0");
					$("#results").slideUp(100, function() {
						$("#mod").click();
					});
				}
			}
		}
	</script>
	<style>
		.modal.fade.in {
			top: 1%;
			left: 32%;
		}

		.modal-body {
			max-height: 550px;
		}

		#myModal {
			width: 1000px
		}
	</style>
	<div id="content-header">
		<div class="header_div"> <span class="header">Expense Entry</span></div>
	</div>
	<!--End-header-->
	<div class="container-fluid" onkeypress="hid_div(event)">


		<input type="hidden" id="main_ser_typ" value="2" />
		<div id="search_d">
			<div class="">
				<b>From</b>
				<input class="form-control datepicker" type="text" name="ex_fdate" id="ex_fdate" value="<?php echo date('Y-m-d'); ?>" readonly>
				<b>To</b>
				<input class="form-control datepicker" type="text" name="ex_tdate" id="ex_tdate" value="<?php echo date('Y-m-d'); ?>" readonly />

				<button type="button" id="ser" name="ser" class="btn btn-primary" onClick="load_pinfo_date(2)">Search</button>
				<button type="button" class="btn btn-primary" onclick="add_expense()">Add New</button>

			</div>
			<div align="center">

			</div>
		</div>

		<br />

		<div id="ex_rep">

		</div>
		<script>
			load_pinfo_date(2)
		</script>


		<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
		<input type="hidden" id="mod_chk" value="0" />
		<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-body">
						<div id="results"> </div>
					</div>

				</div>
			</div>
		</div>