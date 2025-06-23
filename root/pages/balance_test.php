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
} else {
	$branch_str = " AND branch_id='$p_info[branch_id]'";
	$branch_display = "display:none;";
}

$branch_id = $p_info["branch_id"];
?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-striped table-bordered" id="search_data">
		<tr>
			<td>
				<center>
					<select id="branch_id" class="span2" onChange="view_all()" style="<?php echo $branch_display; ?>">
						<option value="0">All Branch</option>
						<?php
						$branch_qry = mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while ($branch = mysqli_fetch_array($branch_qry)) {
							//if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
						?>
					</select>
					<b>From</b>
					<input class="span2 datepicker" type="text" name="txtfrom" id="txtfrom"
						value="<?php echo date("Y-m-d"); ?>">
					<b>To</b>
					<input class="span2 datepicker" type="text" name="txtto" id="txtto"
						value="<?php echo date("Y-m-d"); ?>">
				</center>
			</td>
		</tr>
		<tr>
			<td>
				<b>Select Test</b>
				<select multiple id="sel_test" style="width:100%;">
					<!--<option value="all">All</option>-->
					<?php
					$test_qry = mysqli_query($link, " SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' ORDER BY `testname` ");
					while ($test = mysqli_fetch_array($test_qry)) {
						echo "<option value='$test[testid]'>$test[testname]</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<center>
					<div id="loader" style="margin-top:-10%;"></div>
					<button class="btn btn-success " onClick="view_all('0')"><i class="icon-search"></i> All Ordered
						Test</button>
					<button class="btn btn-success " onClick="view_all('1')"><i class="icon-search"></i> Pending
						Test</button>
					<button class="btn btn-success " onClick="view_all('2')"><i class="icon-search"></i> Reported
						Test</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="" style="display:none;">

	</div>
	<!--<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">-->
</div>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function () {
		$("#loader").hide();
		//view_all('pending');
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		$("#sel_test").select2({ theme: "classic" });
	});
	function view_all(typ) {
		$("#loader").show();
		$.post("pages/balance_test_data.php",
			{
				type: "test_status",
				val: typ,
				branch_id: $("#branch_id").val(),
				sel_test: $("#sel_test").val(),
				date1: $("#txtfrom").val(),
				date2: $("#txtto").val(),
			},
			function (data, status) {
				$("#loader").hide();
				$("#load_all").show().html(data);
			})
	}
	function print_page() {
		//~ var val=0;
		//~ var branch_id=$("#branch_id").val();
		//~ var date1=$("#txtfrom").val();
		//~ var date2=$("#txtto").val();

		//~ var sel_test=$("#sel_test").val();
		//~ var sel_test = sel_test.toString(); 

		//~ url="pages/balance_test_print.php?val="+val+"&date1="+date1+"&date2="+date2+"&sel_test="+sel_test+"&branch_id="+branch_id;

		//~ window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1200');

		window.print();
	}
</script>
<style>
	.ScrollStyle {
		max-height: 380px;
		overflow-y: scroll;
	}

	@media print {

		#header,
		#search,
		.modal,
		#sidebar,
		#search_test,
		#search_data,
		#user-nav,
		#print_but,
		#footer_but,
		.print_btn {
			display: none;
		}

		.table tr td,
		.table tr th {
			font-size: 10px;
			padding: 0px;
			line-height: 15px;
		}

		.head {
			font-size: 12px;
		}

		#search_data {
			margin-left: -120px;
		}

		#print_header {
			display: block;
		}

		#data_table td {
			font-size: 10px;
		}

		#data_table {
			margin-left: 0px;
			width: 100%
		}
	}

	@page {
		margin-left: 0cm;
	}
</style>