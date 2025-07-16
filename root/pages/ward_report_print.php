<?php
if ($glob_patient_type == 0) {
	$pat_typ = "display:none";
}
$c_user = $_SESSION['emp_id'];
?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div id="search_div">
		<table class="table table-bordered table-condensed text-center">
			<tr>
				<td colspan="5">
					<center>
						<span class="side_name">From</span>
						<input class="form-control datepicker span2" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 47px;">
						<span class="side_name">To</span>
						<input class="form-control datepicker span2" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 26px;">

						<select id="search_dept_id" name="search_dept_id" onchange="load_dep_test()" class="span2">
							<option value="0">--All(DEP)--</option>
							<?php
							//$dep=mysqli_query($link,"select distinct type_id from testmaster where category_id='1' order by type_id");
							$dept = mysqli_query($link, "SELECT DISTINCT a.`id` AS `dept_id`,a.`name` AS `type_name` FROM `test_department` a, `testmaster` b WHERE a.`category_id`=b.`category_id` AND a.`id`=b.`type_id` AND a.`category_id`=1 AND a.`id` NOT IN($non_reporting_test_dept_id) ORDER BY a.name ASC");
							while ($dep = mysqli_fetch_array($dept)) {
								echo "<option value='$dep[dept_id]'>$dep[type_name]</option>";
							}
							?>
						</select>
						
						<select id="search_testid" name="search_testid" class="span2">
							<option value="0">--All(Test)--</option>
							<?php
							$test = mysqli_query($link, "SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='1' ORDER BY `testname` ASC");
							while ($tst = mysqli_fetch_array($test)) {
								echo "<option value='$tst[testid]'>$tst[testname]</option>";
							}
							?>
						</select>
						<select id="search_ward" name="search_ward" class="span2" onchange="ward_change()">
							<!--<option value="0">--All(Ward)--</option>-->
							<?php
							$test = mysqli_query($link, "SELECT `id`, `ward_name` FROM `ward_master` WHERE `ward_name`!='' ORDER BY `ward_name` ASC");
							while ($tst = mysqli_fetch_array($test)) {
								echo "<option value='$tst[id]'>$tst[ward_name]</option>";
							}
							?>
						</select>
						
						<button id="search_btn" value="Search" class="btn btn-search" onclick="$('#search_check').val('1');load_pat_ser(0);" style="margin-bottom: 10px;"><i class="icon-search"></i> Search</button>
						<input type="hidden" id="search_check" value="0" />
					</center>
				</td>
			</tr>

			<tr>
				<td style="text-align:center;font-weight:bold">
					<!--Cash Memo No.--> <br /> <input type="text" id="bill_no" style="<?php echo $pat_typ; ?>" onkeyup="load_pat_event(event)" />
					Sample Type
					<select id="type_prefix" class="span2" onchange="load_pat_ser(0)">
						<option value="">All</option>
						<?php
						//$type_qry = mysqli_query($link, "SELECT DISTINCT `type_prefix` FROM `uhid_and_opdid`");
						$type_qry = mysqli_query($link, "SELECT `sample_prefix` FROM `sample_prefix_master` WHERE `status`=0");
						while ($type = mysqli_fetch_array($type_qry)) {
							$type_name = str_replace("/", "", $type['sample_prefix']);
							echo "<option value='$type[sample_prefix]'>$type_name</option>";
						}
						?>
					</select>
					<input onkeyup="load_pat_ser(0)" type="text" id="sample_serial" class="span1" />

				</td>
				<td style="text-align:center;font-weight:bold">
					Hospital No. <br /> <input type="text" id="uhid" onkeyup="load_pat_event(event)" />
				</td>
				<td style="text-align:center;font-weight:bold; display: none;<?php echo $bar_sty; ?>">
					Cashmemo No <br /> <input type="text" id="barcode_id" list="bar_list" onkeyup="load_pat_event(event)" />
					<datalist id="bar_list">
						<?php
						$date = date('Y-m-d');
						$date1 = strtotime(date("Y-m-d", strtotime($date)) . " -3 days");
						$date_five = date("Y-m-d", $date1);
						//$barc=mysqli_query($link,"select distinct opd_id from test_sample_result where date between '$date1' and '$date'");
						while ($bar = mysqli_fetch_array($barc)) {
							echo "<option>$bar[opd_id]</option>";
						}
						?>
					</datalist>
				</td>
				<td style="text-align:center; font-weight: bold;">
					Select Doctor: <br>
					<select id="sel_doctor" onchange="load_pat_ser()">
						<option value="0">--Select--</option>
						<?php
						$selected = "";

						$fdoc = mysqli_query($link, "select * from lab_doctor where category='1' ORDER BY name");
						while ($fd = mysqli_fetch_array($fdoc)) {
							if ($c_user == $fd['id']) {
								$selected = "selected";
							} else {
								$selected = "";
							}
							echo "<option $selected value='$fd[id]'>$fd[name]</option>";
						}
						?>
					</select>
				</td>
				
				<td style="text-align:center;font-weight:bold">
					Name <br />
					<input type="text" placeholder="" id="name" onkeyup="load_pat_event(event)">
				</td>
				<?php
				$bar_sty = 'display:none';
				if ($glob_barcode == 1) {
					$bar_sty = 'display:block';
				}
				?>
			</tr>
		</table>
		<input type="hidden" id="ser_type" value="0" class="ScrollStyle" />

		<table class="table table-bordered table-condensed" style="display:none">
			<tr>
				<td colspan="4">
					Select Doctor:
					<select id="for_doc">
						<option value="0">--Select--</option>
						<?php
						$fdoc = mysqli_query($link, "select * from lab_doctor where category='1'");
						while ($fd = mysqli_fetch_array($fdoc)) {
							echo "<option value='$fd[id]'>$fd[name]</option>";
						}
						?>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<select id="print_status" onchange="filterRows()">
		<option value="0">All</option>
		<option value="1">Not Printed</option>
		<option value="2">Printed</option>
	</select>
	<select id="print_authStatus" onchange="filterRows()">
		<option value="0">All</option>
		<option value="1">Authenticated</option>
		<option value="2">Unverified</option>
		<option value="3">No Result</option>
	</select>
	<div id="pat_list" class="ScrollStyleY">

	</div>
	<div id="load_data" class="">

	</div>
</div>

<div class="text-center"
	style="position: fixed; bottom: -20px; right: 0px; padding: 10px; background: #fff; color: #000; box-shadow: 0 0 10px rgba(0,0,0,0.5); z-index:100;">
	<table class="table table-bordered table-condensed">
		<tr>
			<td class=""><span class="btn_round_msg red"></span> No Data</td>
			<td class=""><span class="btn_round_msg yellow"></span> Not Approve</td>
			<td class=""><span class="btn_round_msg blue"></span> Partially Approve</td>
			<td class=""><span class="btn_round_msg green"></span> Approve</td>
		</tr>
	</table>
</div>

<button id="btn_modal_result" type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" style="display:none;">Open Modal Result</button>
<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<!--<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Modal Header</h4>
			</div>-->
			<div class="modal-body">
				<div id="load_data_result"></div>
			</div>
			<div class="modal-footer" style="display:none;">
				<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="list_start" value="50">

<div id="loader" style="margin-top:-10%;"></div>

<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />

<script src="../js/jquery.dataTables.min_all.js"></script>
<script>
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

		$("#bill_no").val("").focus();

		$("#search_dept_id").val(getCookie("tech_approve_dept_id"));
		$("#search_ward").val(getCookie("ward_id"));

		setTimeout(function () {
			load_pat_ser(2);
		}, 100);

		$('#pat_list').on('scroll', function () {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);

			if (div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start = $("#list_start").val().trim();
				list_start = parseInt(list_start) + 50;
				$("#list_start").val(list_start);
				//load_pat_ser(3);
			}
		});
	});
	
	function filterRows() {
		var printStatus = document.getElementById('print_status').value;
		var authStatus = document.getElementById('print_authStatus').value;

		var rows = document.querySelectorAll('#myTable tbody tr');
		rows.forEach(function(row) {
			// By default, show the row
			row.style.display = '';

			// Check print status
			if (printStatus == '1' && !row.classList.contains('not_printed')) {
			  row.style.display = 'none';
			}
			if (printStatus == '2' && !row.classList.contains('printed')) {
			  row.style.display = 'none';
			}

			// Check auth status
			if (authStatus == '1' && !row.classList.contains('Authenticated')) {
			  row.style.display = 'none';
			}
			if (authStatus == '2' && !row.classList.contains('Unverified')) {
			  row.style.display = 'none';
			}
			if (authStatus == '3' && !row.classList.contains('NoResult')) {
			  row.style.display = 'none';
			}
		});
		
		$("#chkall").prop("checked",false);
		$(".checks").prop("checked",false);
	}
	
	function hid_div(e) {
		if(e.which==27)
		{
			$('#myModal').modal('hide');
		}
	}
	function select_all() {
		var chkall = $("#chkall:checked").length;
		if (chkall > 0) {
			// Select only the first 10 visible checkboxes
			$(".checks:visible").slice(0, 10).prop("checked", true);
			// Optionally, uncheck the rest
			$(".checks:visible").slice(10).prop("checked", false);
		}
		else {
			//$(".checks").attr("checked", false);
			$(".checks:visible").prop("checked", false);
		}
	}
	function check_all_chks() {
		var all_checks = $(".checks").length;
		var all_checked = $(".checks:checked").length;

		if (all_checks == all_checked) {
			$("#chkall").attr("checked", true);
		}
		else {
			$("#chkall").attr("checked", false);
		}
	}
	function print_selected() {
		var chk = $(".checks:checked");
		if (chk.length == 0) {
			alertmsg("Nothing selected", 0);
		}
		else {
			//alertmsg("Selected "+chk.length, 1);
			var opds = [];
			for (var i = 0; i < (chk.length); i++) {
				opds.push(chk[i].value);
			}

			var idString = opds.join('$$');

			var user = $("#user").text().trim();

			var url = "pages/pathology_report_print_multiple.php?idString=" + idString + "&user=" + user;
			var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
		}
	}
	
	function dept_serial_no_up(e) {
		if (e.which == 13) {
			load_pat_ser(6);
		}
	}

	function load_pat_ser(val) {
		if (val == 0) {
			$("#list_start").val(50);
		}
		
		$("#print_status").val(0);
		$("#print_authStatus").val(0);
		
		$("#loader").show();
		$.post("pages/ward_report_print_data.php",
			{
				type: "load_pat_list",
				fdate: $("#fdate").val(),
				tdate: $("#tdate").val(),
				dept_id: $("#search_dept_id").val(),
				testid: $("#search_testid").val(),
				ward: $("#search_ward").val(),
				name: $("#name").val().trim(),
				bill_no: $("#bill_no").val().trim(),
				uhid: $("#uhid").val().trim(),
				barcode_id: $("#barcode_id").val().trim(),
				list_start: $("#list_start").val(),
				patType: $("#patType").val(),
				sample_type: $("#type_prefix").val(),
				sample_serial: $("#sample_serial").val(),
				sel_doc: $("#sel_doctor").val(),
			},
			function (data, status) {
				$("#load_data").slideUp(400);
				$("#pat_list").slideDown(600);

				$("#loader").hide();
				$("#pat_list").html(data);
				$("#ser_type").val("1");
				$("#search_check").val("0");

				resetCountdown();
			})
	}
	function load_pat_event(e) {
		if (e.which == 13) {
			$("#list_start").val(50);
			load_pat_ser(7);
		}
	}
	
	function load_dep_test() {
		setCookie("tech_approve_dept_id", $("#search_dept_id").val(), 300); // Assign cookie

		$.post("pages/ward_report_print_data.php",
		{
			type: "load_dept_tests",
			dept_id: $("#search_dept_id").val(),
		},
		function (data, status) {
			$("#search_testid").html(data);
		})
	}
	function ward_change() {
		setCookie("ward_id", $("#search_ward").val(), 300); // Assign cookie
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
	
	function view_report(patient_id,opd_id,ipd_id,batch_no,testid,val)
	{
		$.post("pages/ward_report_print_data.php",
		{
			type: "view_report",
			patient_id:patient_id,
			opd_id:opd_id,
			ipd_id:ipd_id,
			batch_no:batch_no,
			testid:testid,
			val:val,
		},
		function (data, status) {
			$("#load_data_result").html(data);
			
			$("#btn_modal_result").click();
		})
	}

</script>
<style>
	.ScrollStyleY {
		max-height: 600px;
		overflow-y: scroll;
	}

	.flagged td {
		color: red;
	}

	#bill_error td {
		font-weight: bold;
		color: blue;
	}

	.rep_hosp {
		font-weight: bold;
		cursor: pointer;
		text-decoration: underline;
		color: green
	}

	.side_name {
		border: 1px solid #ddd;
		background-color: #fff;
		padding: 4px;
		position: absolute;
		font-weight: bold;
	}

	#myModal {
		display: none;
		left: 35%;
		width: 50%;
	}

	.modal.fade.in {
		top: 0%;
	}

	.modal-body {
		max-height: 640px;
	}

	#myModal_repeat {
		left: 20%;
		width: 95%;
	}

	.widget-content {
		width: 99%;
	}

	#gritter-notice-wrapper {
		//top: 45% !important;
		//right: 44% !important;
		z-index: 999999 !important;
	}

	small {
		font-size: 60% !important;
	}

	tr.printed {
		background: #CEFFC8;
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
		background-color: #da4f49;
	}

	.green {
		background-color: #51a351;
	}

	.yellow {
		background-color: #f89406;
	}

	.blue {
		background-color: #04c;
	}

	.repeaters {
		-webkit-animation: spin 2s linear infinite;
		animation: spin 2s linear infinite;
	}

	@-webkit-keyframes spin {
		0% {
			-webkit-transform: rotate(0deg);
		}

		100% {
			-webkit-transform: rotate(360deg);
		}
	}

	@keyframes spin {
		0% {
			transform: rotate(0deg);
		}

		100% {
			transform: rotate(360deg);
		}
	}
</style>
