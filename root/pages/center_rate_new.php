<?php
$branch_display = "display:none;";
if ($p_info["levelid"] == 1) {
	$branch_str = "";

	$branch_display = "display:none;";
	$branch_num = mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if ($branch_num > 1) {
		$branch_display = "display:;";
	}
} else {
	$branch_str = " AND branch_id='$p_info[branch_id]'";
	$branch_display = "display:none;";

	$dept_sel_dis = "disabled";
}

$branch_id = $p_info["branch_id"];
?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header">
			<?php echo $menu_info["par_name"]; ?>
		</span></div>
</div>
<!--End-header-->
<div style="padding-left:50px;">

	<div class="row" style="padding-top:10px;">
		<div class="span3">
			<label for="branch"><strong>Branch:</strong></label>
		</div>
		<div class="span8">
			<select id="branch" onchange="load_center()">
				<?php
				$branch_qry = mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
				while ($branch = mysqli_fetch_array($branch_qry)) {
					if ($branch_id == $branch["branch_id"]) {
						$branch_sel = "selected";
					} else {
						$branch_sel = "";
					}
					echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
				}
				?>
			</select>
		</div>
	</div>

	<div class="row" style="padding-top:5px;">
		<div class="span3">
			<label for="select"><strong>Center:</strong></label>
		</div>
		<div class="span8">
			<select id="center" onchange="load_check()">

			</select>
		</div>
	</div>

	<div class="row" style="padding-top:8px;">
		<div class="span3">
			<label for="selectdepartment"><strong>Select Department:</strong></label>
		</div>
		<div class="span7">
			<table class="table table-condensed">
				<?php
				$i = 1;
				$n = 1;
				$qrydp = mysqli_query($link, "SELECT * FROM `test_department`");

				while ($rows = mysqli_fetch_array($qrydp)) {
					if ($i == 1) {
						echo '<tr>';
					}
					echo '<td>';
					?>
					<label class="checkbox" style="margin-right: 70px; font-size: 14px;">
						<input type="checkbox" class="sel_dep" id="dept_id<?php echo $n; ?>" name="check"
							value="<?php echo $rows['id']; ?>" onchange="load_check()">
						<?php echo $rows['name']; ?>
					</label>
					<?php
					echo '</td>';

					if ($i == 7) {
						echo '</tr>';
						$i = 0;
					}
					$i++;
					$n++;
				}

				if ($i != 1) {
					echo '</tr>';
				}
				?>
			</table>
		</div>
	</div>



	<div class="ScrollStyle" id="rowdata" style="padding-right:40px; height:300px;">

	</div>


	<input type="hidden" id="testcount" name="tcount" style="width: 100px;" readonly>


	<div style="margin-top: 10px; display: flex; flex-wrap: wrap; justify-content: center;">
		<div style="margin-right: 100px; margin-bottom: 10px; display: flex; align-items: center;">
			<label style="margin-right: 5px;"><strong>Self.Test Discount (in perc):</strong></label>
			<input type="text" value="0" name="selfperc" id="selfperc" onkeyup="chk_dec(this, event)"
				style="width: 50px;">
		</div>
		<div style="margin-bottom: 10px; display: flex; align-items: center;">
			<label style="margin-right: 5px;"><strong>Self.Test Discount (in cash):</strong></label>
			<input type="text" value="0" name="selfcash" id="selfcash" onkeyup="chk_dec(this, event)"
				style="width: 50px;">
		</div>
	</div>
	<div style="margin-top: 10px; display: flex; flex-wrap: wrap; justify-content: center;">
		<div style="margin-right: 100px; margin-bottom: 10px; display: flex; align-items: center;">
			<label style="margin-right: 5px;"><strong>Dr.Test Discount (in perc):</strong></label>
			<input type="text" value="0" name="drperc" id="drperc" onkeyup="chk_dec(this, event)" style="width: 50px;">
		</div>
		<div style="margin-bottom: 10px; display: flex; align-items: center;">
			<label style="margin-right: 5px;"><strong>Dr.Test Discount (in cash):</strong></label>
			<input type="text" value="0" name="drcash" id="drcash" onkeyup="chk_dec(this, event)" style="width: 50px;">
		</div>
	</div>


	<div style="margin-top: 20px; display: flex; justify-content: center;">
		<button class="btn btn-reset" style="margin-right: 10px; width: 100px;" onclick="saveData()"><i
				class="icon-refresh"></i>
			Save</button>
		<button class="btn btn-save" style="width: 100px;"><i class="icon-save"></i> Preview</button>
	</div>



</div>



<input type="hidden" id="centre_test_sorter" value="ASC">
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function () {
		$("#centreno").select2({ theme: "classic" });
		$("#centreno").select2("focus");

		//load_center();
		search_master_test('');

		setTimeout(function () {
			load_centre_test('', '');
		}, 100);
		load_center();
		// load_check();
	});


	function selectAllCheckboxes() {
		var checkboxes = document.getElementsByClassName('rowCheckbox');
		var selectAllCheckbox = document.getElementById('selectAll');

		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = selectAllCheckbox.checked;
		}

		updateSelectedIds();
	}



	function updateSelectedIds() {
		var checkboxes = document.getElementsByClassName('rowCheckbox');
		var selectedIds = [];

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].checked) {
				var row = checkboxes[i].closest('tr');
				var id = row.cells[1].innerText;
				selectedIds.push(id);
			}
		}

		document.getElementById('testcount').value = selectedIds.join('@@');
	}


	function toggleCheckbox(cell) {
        var checkbox = cell.parentNode.querySelector('.rowCheckbox');
        checkbox.checked = !checkbox.checked;

        updateSelectedIds();
    }


	function saveData() {
		if ($("#testcount").val() < 1) {
			alert("No test selected. Please select at least 1 test.");
			return;
		}

		if ($(".err").length > 0) {
			$(".err:first").focus();

		} else {
			$.post("pages/center_rate_data.php", {
				type: "testsave",
				center: $("#center").val(),
				alltestid: $("#testcount").val(),
				selfperc: $("#selfperc").val(),
				selfcash: $("#selfcash").val(),
				drperc: $("#drperc").val(),
				drcash: $("#drcash").val(),
			},
				function (data, status) {
					alert(data);
				});
		}
	}



	function chk_dec(ths, e) {
		var reg = /^\d+(?:\.\d{1,2})?$/;
		var val = $(ths).val();
		if (!reg.test(val)) {
			$(ths).css({ "border": "1px solid #FF0000", "box-shadow": "0px 0px 10px 2px #FD0B0B" });
			$(ths).addClass("err");
		}
		else {
			$(ths).css({ "border": "", "box-shadow": "" });
			$(ths).removeClass("err");
		}
	}



	function checkboxes() {
		var selectAllCheckbox = document.getElementById("selectAll");
		var rowCheckboxes = document.getElementsByClassName("rowCheckbox");

		var allChecked = true;
		for (var i = 0; i < rowCheckboxes.length; i++) {
			if (!rowCheckboxes[i].checked) {
				allChecked = false;
				break;
			}
		}

		selectAllCheckbox.checked = allChecked;
	}







	function load_check() {
		var dept_ids = "";
		var chk = $(".sel_dep:checked");
		for (var i = 0; i < chk.length; i++) {
			if (dept_ids) {
				dept_ids += ",'" + chk[i].value + "'";
			}
			else {
				dept_ids = "'" + chk[i].value + "'";
			}
		}
		// alert(dept_ids);

		$.post("pages/center_rate_data.php",
			{
				type: "load_check",
				branch_id: $("#branch").val(),
				center: $("#center").val(),
				dept_ids: dept_ids,
			},
			function (data, status) {
				$("#rowdata").html(data);
				// alert(data);
			})
	}



	function load_center() {
		$.post("pages/center_rate_data.php",
			{
				type: "load_center",
				branch_id: $("#branch").val(),
			},
			function (data, status) {
				$("#center").html(data);
				// alert(data);
			})
	}





	function centre_change() {
		load_centre_test('', '');
	}
	function group_change() {
		search_master_test('');
		load_centre_test('', '');
	}
	function search_master_test(val, e) {
		$.post("pages/center_rate_data.php",
			{
				type: "load_master_test",
				val: val,
				group_id: $("#group_id").val(),
				branch_id: $("#branch_id").val(),
			},
			function (data, status) {
				$("#load_master_test_list").html(data);
			})
	}
	function search_centre_test(val, e) {
		load_centre_test(val, '')
	}
	function load_centre_test(val, testid) {
		$.post("pages/center_rate_data.php",
			{
				type: "load_centre_test",
				centreno: $("#centreno").val(),
				group_id: $("#group_id").val(),
				val: val,
				testid: testid,
				centre_test_sorter: $("#centre_test_sorter").val().trim(),
			},
			function (data, status) {
				$("#load_centre_test_list").html(data);
				if ($("#centreno").val() == 0) {
					$(".centre_test_span").hide();
				}
				else {
					$(".centre_test_span").show();
				}
			})
	}
	function cm_rate_up(testid, service_category, e) {
		$("#centre_test_sorter").val("DESC");
		if (e.which == 13) {
			if ($("#centreno").val() == 0) {
				alert("Select Centre");
				$("#centreno").select2("focus");
				return false;
			}

			centre_rate_change($("#centreno").val(), testid, service_category, $("#cm_rate" + testid).val(), "M");

			$(".cm_rate").css({ "border": "3px solid #ccc" });
			$("#cm_rate" + testid).css({ "border": "3px solid green" });
		}

		var a = $("#cm_rate" + testid).val();
		var n = a.length;
		var numex = /^[0-9.]+$/;
		if (a[n - 1].match(numex)) {

		}
		else {
			a = a.slice(0, n - 1);
			$("#cm_rate" + testid).val(a)
		}
	}
	function cc_rate_up(testid, service_category, e) {
		if (e.which == 13) {
			if ($("#centreno").val() == 0) {
				alert("Select Centre");
				$("#centreno").select2("focus");
				return false;
			}

			centre_rate_change($("#centreno").val(), testid, service_category, $("#cc_rate" + testid).val(), "C");

			$("#cc_rate" + testid).css({ "border": "2px solid green" });
		}

		var a = $("#cc_rate" + testid).val();
		var n = a.length;
		var numex = /^[0-9.]+$/;
		if (a[n - 1].match(numex)) {

		}
		else {
			a = a.slice(0, n - 1);
			$("#cc_rate" + testid).val(a)
		}
	}
	function centre_rate_change(centreno, testid, service_category, c_rate, frm) {
		//alert(centreno+' '+testid+" "+c_rate);
		$.post("pages/center_rate_data.php",
			{
				type: "save_centre_test",
				group_id: $("#group_id").val(),
				centreno: centreno,
				testid: testid,
				service_category: service_category,
				c_rate: c_rate,
			},
			function (data, status) {
				load_centre_test('', testid);

				setTimeout(function () {

					if (frm == "M") {
						$("#ctr" + testid).css({ "background": "#00ff003d none repeat scroll 0% 0%" });
						$("#cm_rate" + testid).focus();
					}
					if (frm == "C") {
						$("#cc_rate" + testid).css({ "border": "3px solid green" });
						$("#cc_rate" + testid).focus();
					}
				}, 200);
				setTimeout(function () {
					//$("#ctr"+testid).css({"background":"#F5F5F5"});
				}, 5000);
			})
	}

	function test_code_up(testid, e) {
		if (e.which == 13) {
			if ($("#centreno").val() == 0) {
				alert("Select Centre");
				$("#centreno").select2("focus");
				return false;
			}

			$.post("pages/center_rate_data.php",
				{
					type: "save_centre_test_code",
					centreno: $("#centreno").val(),
					testid: testid,
					test_code: $("#test_code" + testid).val(),
				},
				function (data, status) {
					load_centre_test('', testid);

					setTimeout(function () {
						//$("#ctr"+testid).css({"background":"#00ff003d none repeat scroll 0% 0%"});
						$("#test_code" + testid).focus().css({ "border": "3px solid green" });;
					}, 200);
					setTimeout(function () {
						//$("#ctr"+testid).css({"background":"#F5F5F5"});
					}, 5000);
				})
		}
	}
	function delete_centre_test(serv_id, service_category) {
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this from " + $("#centreno").find('option:selected').text() + "</h5>",
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
						$.post("pages/center_rate_data.php",
							{
								type: "delete_centre_test",
								centreno: $("#centreno").val(),
								serv_id: serv_id,
								service_category: service_category,
							},
							function (data, status) {
								if (data == 1) {
									var msg = "Deleted";
									load_centre_test('', serv_id);
								}
								if (data == 2 || data == 3) {
									var msg = "Error, Try again";
								}
								bootbox.dialog({ message: "<h5>" + msg + "</h5>" });

								setTimeout(function () {
									bootbox.hideAll();
								}, 2000);
							})
					}
				}
			}
		});
	}
	function show_test_list() {
		var centreno = $("#centreno").val();
		var group_id = $("#group_id").val();

		if (centreno == 0) {
			alert("Select Centre");
			return false;
		}

		var url = "pages/center_rate_test_list.php?v=0&centreno=" + centreno + "&group=" + group_id;
		window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}

	function cm_rate_opd_v_up(docid, service_category, e) {
		$("#centre_test_sorter").val("DESC");
		if (e.which == 13) {
			if ($("#centreno").val() == 0) {
				alert("Select Centre");
				$("#centreno").select2("focus");
				return false;
			}

			centre_rate_opd_v_change($("#centreno").val(), docid, service_category, $("#cm_rate_opd_v" + docid).val(), "M");

			$(".cm_rate_opd_v").css({ "border": "3px solid #ccc" });
			$("#cm_rate_opd_v" + docid).css({ "border": "3px solid green" });
		}

		var a = $("#cm_rate_opd_v" + docid).val();
		var n = a.length;
		var numex = /^[0-9.]+$/;
		if (a[n - 1].match(numex)) {

		}
		else {
			a = a.slice(0, n - 1);
			$("#cm_rate_opd_v" + docid).val(a)
		}
	}
	function centre_rate_opd_v_change(centreno, docid, service_category, c_rate, frm) {
		//alert(centreno+' '+testid+" "+c_rate);
		$.post("pages/center_rate_data.php",
			{
				type: "save_centre_opd_v",
				group_id: $("#group_id").val(),
				centreno: centreno,
				docid: docid,
				service_category: service_category,
				c_rate: c_rate,
			},
			function (data, status) {
				load_centre_test('', docid);

				setTimeout(function () {

					if (frm == "M") {
						$("#ctr" + docid).css({ "background": "#00ff003d none repeat scroll 0% 0%" });
						$("#cm_rate_opd_v" + docid).focus();
					}
					if (frm == "C") {
						$("#cc_rate_opd_v" + docid).css({ "border": "3px solid green" });
						$("#cc_rate_opd_v" + docid).focus();
					}
				}, 200);
				setTimeout(function () {
					//$("#ctr"+testid).css({"background":"#F5F5F5"});
				}, 5000);
			})
	}

	function cm_rate_opd_r_up(docid, service_category, e) {
		$("#centre_test_sorter").val("DESC");
		if (e.which == 13) {
			if ($("#centreno").val() == 0) {
				alert("Select Centre");
				$("#centreno").select2("focus");
				return false;
			}

			centre_rate_opd_r_change($("#centreno").val(), docid, service_category, $("#cm_rate_opd_r" + docid).val(), "M");

			$(".cm_rate_opd_r").css({ "border": "3px solid #ccc" });
			$("#cm_rate_opd_r" + docid).css({ "border": "3px solid green" });
		}

		var a = $("#cm_rate_opd_r" + docid).val();
		var n = a.length;
		var numex = /^[0-9.]+$/;
		if (a[n - 1].match(numex)) {

		}
		else {
			a = a.slice(0, n - 1);
			$("#cm_rate_opd_r" + docid).val(a)
		}
	}
	function centre_rate_opd_r_change(centreno, docid, service_category, c_rate, frm) {
		//alert(centreno+' '+testid+" "+c_rate);
		$.post("pages/center_rate_data.php",
			{
				type: "save_centre_opd_r",
				group_id: $("#group_id").val(),
				centreno: centreno,
				docid: docid,
				service_category: service_category,
				c_rate: c_rate,
			},
			function (data, status) {
				load_centre_test('', docid);

				setTimeout(function () {

					if (frm == "M") {
						$("#ctr" + docid).css({ "background": "#00ff003d none repeat scroll 0% 0%" });
						$("#cm_rate_opd_r" + docid).focus();
					}
					if (frm == "C") {
						$("#cc_rate_opd_r" + docid).css({ "border": "3px solid green" });
						$("#cc_rate_opd_r" + docid).focus();
					}
				}, 200);
				setTimeout(function () {
					//$("#ctr"+testid).css({"background":"#F5F5F5"});
				}, 5000);
			})
	}

	function cc_rate_opd_v_up(docid, service_category, e) {
		if (e.which == 13) {
			if ($("#centreno").val() == 0) {
				alert("Select Centre");
				$("#centreno").select2("focus");
				return false;
			}

			centre_rate_opd_v_change($("#centreno").val(), docid, service_category, $("#cc_rate_opd_v" + docid).val(), "C");

			$("#cc_rate_opd_v" + docid).css({ "border": "2px solid green" });
		}

		var a = $("#cc_rate_opd_v" + docid).val();
		var n = a.length;
		var numex = /^[0-9.]+$/;
		if (a[n - 1].match(numex)) {

		}
		else {
			a = a.slice(0, n - 1);
			$("#cc_rate_opd_v" + docid).val(a)
		}
	}

	function centre_rate_opd_v_change(centreno, docid, service_category, c_rate, frm) {
		//alert(centreno+' '+docid+" "+service_category+" "+c_rate);
		$.post("pages/center_rate_data.php",
			{
				type: "save_centre_opd_v",
				group_id: $("#group_id").val(),
				centreno: centreno,
				docid: docid,
				service_category: service_category,
				c_rate: c_rate,
			},
			function (data, status) {
				load_centre_test('', docid);

				setTimeout(function () {

					if (frm == "M") {
						$("#ctr" + docid).css({ "background": "#00ff003d none repeat scroll 0% 0%" });
						$("#cm_rate_opd_v" + docid).focus();
					}
					if (frm == "C") {
						$("#cc_rate_opd_v" + docid).css({ "border": "3px solid green" });
						$("#cc_rate_opd_v" + docid).focus();
					}
				}, 200);
				setTimeout(function () {
					//$("#ctr"+docid).css({"background":"#F5F5F5"});
				}, 5000);
			})
	}

	function cc_rate_opd_r_up(docid, service_category, e) {
		if (e.which == 13) {
			if ($("#centreno").val() == 0) {
				alert("Select Centre");
				$("#centreno").select2("focus");
				return false;
			}

			centre_rate_opd_r_change($("#centreno").val(), docid, service_category, $("#cc_rate_opd_r" + docid).val(), "C");

			$("#cc_rate_opd_r" + docid).css({ "border": "2px solid green" });
		}

		var a = $("#cc_rate_opd_r" + docid).val();
		var n = a.length;
		var numex = /^[0-9.]+$/;
		if (a[n - 1].match(numex)) {

		}
		else {
			a = a.slice(0, n - 1);
			$("#cc_rate_opd_r" + docid).val(a)
		}
	}

	function centre_rate_opd_r_change(centreno, docid, service_category, c_rate, frm) {
		//alert(centreno+' '+docid+" "+service_category+" "+c_rate);
		$.post("pages/center_rate_data.php",
			{
				type: "save_centre_opd_r",
				group_id: $("#group_id").val(),
				centreno: centreno,
				docid: docid,
				service_category: service_category,
				c_rate: c_rate,
			},
			function (data, status) {
				load_centre_test('', docid);

				setTimeout(function () {

					if (frm == "M") {
						$("#ctr" + docid).css({ "background": "#00ff003d none repeat scroll 0% 0%" });
						$("#cm_rate_opd_r" + docid).focus();
					}
					if (frm == "C") {
						$("#cc_rate_opd_r" + docid).css({ "border": "3px solid green" });
						$("#cc_rate_opd_r" + docid).focus();
					}
				}, 200);
				setTimeout(function () {
					//$("#ctr"+docid).css({"background":"#F5F5F5"});
				}, 5000);
			})
	}

</script>
<style>
	.scroll_y {
		overflow-y: scroll;
		height: 450px;
	}

	.side_name {
		border: 1px solid #ddd;
		background-color: #fff;
		padding: 4px;
		position: absolute;
		font-weight: bold;
	}

	.side_name_centre {
		border: 1px solid #ddd;
		background-color: #fff;
		padding: 4px;
		position: absolute;
		font-weight: bold;
		margin-left: 0%;
	}

	.table-condensed th,
	.table-condensed td {
		padding: 0;
	}
</style>