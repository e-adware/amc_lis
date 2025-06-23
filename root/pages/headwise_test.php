<style>
	#patientDetailsTable th {
		cursor: pointer;
		position: relative;
		padding-right: 20px !important;
	}

	#patientDetailsTable th:hover {
		background-color: #f5f5f5;
	}

	#patientDetailsTable th.sorted-asc::after {
		content: "↑";
		position: absolute;
		right: 8px;
		font-size: 12px;
	}

	#patientDetailsTable th.sorted-desc::after {
		content: "↓";
		position: absolute;
		right: 8px;
		font-size: 12px;
	}
</style>
<?php
$emp_id = trim($_SESSION["emp_id"]);
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
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from"
						value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to"
						value="<?php echo date("Y-m-d"); ?>" readonly>
					<select class="span3" id="ward" onchange="view_all('head_wise_detail_pat')">
						<option value="">Select Ward</option>
						<?php
						$q = mysqli_query($link, "SELECT DISTINCT `wardName` FROM `uhid_and_opdid` ORDER BY `wardName`");
						while ($r = mysqli_fetch_array($q)) {
							echo "<option value='$r[wardName]'>$r[wardName]</option>";
						}
						?>
					</select>
					<select class="span2" id="head_id" style="display:none;">
						<!--<option value="0">Select Department</option>-->
						<?php
						//$head_qry=mysqli_query($link, " select distinct type_id,type_name from testmaster where type_name!='' and type_name!='0' order by type_name ");
						$head_qry = mysqli_query($link, " select distinct a.type_id,a.type_name from testmaster a, patient_test_details b where a.testid=b.testid and a.type_name!='' and a.type_name!='0' order by a.type_name ");
						while ($head = mysqli_fetch_array($head_qry)) {
							echo "<option value='$head[type_id]'>$head[type_name]</option>";
						}
						?>
					</select>
					<select id="branch_id" class="span2" style="<?php echo $branch_display; ?>"
						onChange="view_all('cat_test_detail')">
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
					<select id="encounter" class="span2" style="display:none;">
						<!--<option value="0">All Department</option>-->
						<option value="2">Laboratory</option>
						<!--<option value="3">IPD</option>-->
						<option value="10">Radiology</option>
						<?php
						//~ $qq_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`=2 OR `p_type_id`=3 ORDER BY `p_type_id` ");
						//~ while($qq=mysqli_fetch_array($qq_qry))
						//~ {
						//~ echo "<option value='$qq[p_type_id]'>$qq[p_type]</option>";
						//~ }
						?>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('head_wise_detail_pat')"><i
							class="icon-search"></i> Patient wise Details</button>
					<!--<button class="btn btn-success" onClick="view_all('head_wise_test_detail')"><i class="icon-search"></i> Test wise Details</button>-->
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">

	</div>
	<!--<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">-->
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function () {
		$("#loader").hide();
		//view_all('head_wise_detail');
		view_all('head_wise_detail_pat');
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
	});
	function view_all(typ, dep) {
		$("#loader").show();
		$.post("pages/headwise_test_data.php",
			{
				type: typ,
				date1: $("#from").val(),
				date2: $("#to").val(),
				branch_id: $("#branch_id").val(),
				head_id: $("#head_id").val(),
				ward: $("#ward").val(),
				encounter: $("#encounter").val(),
			},
			function (data, status) {
				$("#loader").hide();
				$("#load_all").show().html(data);
			})
	}
	function print_page(val, date1, date2, head_id, encounter, branch_id, ward) {
		//~ if(val=="head_wise_detail_pat")
		//~ {
		//~ url="pages/head_wise_detail_pat_print.php?date1="+date1+"&date2="+date2+"&head_id="+head_id+"&encounter="+encounter+"&val="+val;
		//~ }

		url = "pages/head_wise_detail_pat_print.php?date1=" + date1 + "&date2=" + date2 + "&head_id=" + head_id + "&encounter=" + encounter + "&branch_id=" + branch_id + "&val=" + val + "&ward=" + ward;

		wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}

	function print_doc_pat(el) {
		var restorepage = $('body').html();
		var printcontent = $('#' + el).clone();
		$('body').empty().html(printcontent);
		window.print();
		$('body').html(restorepage);
	}

	function sortTable(columnIndex, tableId, isNumeric = false, dataType = 'text') {
		const table = document.getElementById(tableId);
		const tbody = table.querySelector("tbody");
		const rows = Array.from(tbody.querySelectorAll("tr"));
		let direction = "asc";

		const header = table.querySelectorAll("th")[columnIndex];
		if (header.classList.contains("sorted-asc")) {
			direction = "desc";
			header.classList.remove("sorted-asc");
			header.classList.add("sorted-desc");
		} else if (header.classList.contains("sorted-desc")) {
			direction = "asc";
			header.classList.remove("sorted-desc");
			header.classList.add("sorted-asc");
		} else {
			table.querySelectorAll("th").forEach(th => {
				th.classList.remove("sorted-asc", "sorted-desc");
			});
			header.classList.add("sorted-asc");
		}

		rows.sort((a, b) => {
			let aValue, bValue;
			const aCell = a.cells[columnIndex];
			const bCell = b.cells[columnIndex];

			if (dataType === 'date' || dataType === 'datetime') {
				aValue = parseFloat(aCell.getAttribute("data-sort")) || 0;
				bValue = parseFloat(bCell.getAttribute("data-sort")) || 0;
			} else if (isNumeric) {
				aValue = parseFloat(aCell.textContent.trim()) || 0;
				bValue = parseFloat(bCell.textContent.trim()) || 0;
			} else {
				aValue = aCell.textContent.trim().toLowerCase();
				bValue = bCell.textContent.trim().toLowerCase();

				if (columnIndex === 0) {
					aValue = parseInt(aValue);
					bValue = parseInt(bValue);
				}
			}

			if (direction === "asc") {
				return aValue > bValue ? 1 : -1;
			} else {
				return aValue < bValue ? 1 : -1;
			}
		});

		while (tbody.firstChild) {
			tbody.removeChild(tbody.firstChild);
		}

		rows.forEach(row => {
			tbody.appendChild(row);
		});
	}
</script>