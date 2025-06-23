<div id="content-header">
	<div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th colspan="2"></th>
		</tr>
		<tr>
			<th>
				<b>Include Test</b>
				<select multiple class="span5" id="inc_test">
					<?php
					$test_qry = mysqli_query($link, " SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' ORDER BY `testname` ");
					while ($test = mysqli_fetch_array($test_qry)) {
						echo "<option value='$test[testid]'>$test[testname]</option>";
					}
					?>
				</select>
			</th>
			<th>
				<b>Exclude Test</b>
				<select multiple class="span5" id="exc_test">
					<?php
					$test_qry = mysqli_query($link, " SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' ORDER BY `testname` ");
					while ($test = mysqli_fetch_array($test_qry)) {
						echo "<option value='$test[testid]'>$test[testname]</option>";
					}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;">
				Select TAT
				<select class="form-control" id="tat_minutes" style="width: 100px;">
					<option value="0">Select</option>
					<option value="1">&lt;1 Hour</option>
					<option value="60">&gt;1 Hour</option>
					<option value="120">&gt;2 Hours</option>
					<option value="180">&gt;3 Hours</option>
					<option value="240">&gt;4 Hours</option>
					<option value="300">&gt;5 Hours</option>
					<option value="360">&gt;6 Hours</option>
					<option value="420">&gt;7 Hours</option>
					<option value="480">&gt;8 Hours</option>
					<option value="540">&gt;9 Hours</option>
					<option value="600">&gt;10 Hours</option>
					<option value="660">&gt;11 Hours</option>
					<option value="720">&gt;12 Hours</option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<b>From</b>
				<input class="form-control datepicker span2" type="text" name="from" id="from"
					value="<?php echo date("Y-m-d"); ?>" <?php echo $dept_sel_dis; ?> readonly>
				<b>To</b>
				<input class="form-control datepicker span2" type="text" name="to" id="to"
					value="<?php echo date("Y-m-d"); ?>" <?php echo $dept_sel_dis; ?> readonly>

				<select id="urgent" style="width: 150px; margin-left: 50px;">
					<option value="">All</option>
					<option value="0">Routine</option>
					<option value="1">Emergency</option>
				</select>

				<select id="intime" style="width: 150px; margin-left: 50px;">
					<option value="">All</option>
					<option value="1">Within</option>
					<option value="2">Exceed</option>
				</select>
			</th>
		</tr>
		<tr>
			<th colspan="2" style="text-align: center;">
				<div class="row" style="margin-top: 5px;">
					<span class="input-group-btn">
						<button type="button" id="search_btn1" name="search_btn" class="btn btn-info"
							onClick="tat_calculate(1)">Patient Wise</button>
						<button type="button" id="search_btn2" name="search_btn" class="btn btn-info"
							onClick="tat_calculate(2)">Test Wise Summary</button>
					</span>
				</div>
			</th>
		</tr>
	</table>
</div>
<hr />
<div id="load_data" class="ScrollStyle"></div>
<div id="loader" style="margin-top:100px;display:none;"></div>

<!-- <div id="loader" style="margin-top:-10%;"></div> -->
<link rel="stylesheet" href="../css/loader.css" />
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script src="pages/js/bootbox.min.js"></script>
<link href="../css/loader.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function () {
		$("#inc_test").select2({ theme: "classic" });
		$("#exc_test").select2({ theme: "classic" });
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
	});

	function tat_calculate(val) {
		$("#loader").show();
		$("#search_btn").prop("disabled", true);
		$.post("pages/test_turnaround_time_main.php",
			{
				type: "tat_calculate",
				include_testid: $("#inc_test").val(),
				exclude_testid: $("#exc_test").val(),
				date1: $("#from").val().trim(),
				date2: $("#to").val().trim(),
				user: $("#user").text().trim(),
			},
			function (data, status) {
				//alert(data);
				if (val == 1) {
					patient_view();
				}
				if (val == 2) {
					test_wise_summary_view();
				}
			}
		)
	};
	function patient_view() {
		$("#loader").show();
		$.post("pages/test_turnaround_time_main.php",
			{
				type: "patient_view",
				include_test: $("#inc_test").val(),
				exclude_test: $("#exc_test").val(),
				tat_minutes: $("#tat_minutes").val(),
				urgent: $("#urgent").val(),
				intime: $("#intime").val(),
				user: $("#user").text().trim(),
			},
			function (data, status) {
				// alert(data);
				$("#search_btn").prop("disabled", false);
				$("#loader").hide();
				$("#load_data").html(data);
			}
		)
	};
	function test_wise_summary_view() {
		$("#loader").show();
		$.post("pages/test_turnaround_time_main.php",
			{
				type: "test_wise_summary_view",
				include_test: $("#inc_test").val(),
				exclude_test: $("#exc_test").val(),
				tat_minutes: $("#tat_minutes").val(),
				urgent: $("#urgent").val(),
				user: $("#user").text().trim(),
			},
			function (data, status) {
				// alert(data);
				$("#search_btn").prop("disabled", false);
				$("#loader").hide();
				$("#load_data").html(data);
			}
		)
	};
</script>