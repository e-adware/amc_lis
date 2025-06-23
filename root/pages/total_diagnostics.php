<div id="content-header">
	<div class="header_div"> <span class="header"> Total Diagnostics (Pathology)</span></div>
</div>
<div class="container-fluid" onkeypress="hide_div(event)" id="rep_delv">
	<table class="table table-bordered text-center">
		<tr>
			<td style="text-align:center" colspan="2">
				<b>Branch</b>
				<select id="branch_id" class="span2" style="margin-right: 10px;" onChange="view_all()">
					<?php
					$result = mysqli_query($link, "SELECT `name`, `branch_id` FROM `company_name`");
					while ($brnch = mysqli_fetch_array($result)) {
						echo "<option value='" . $brnch['branch_id'] . "'>" . $brnch['name'] . "</option>";
					}
					?>
				</select>
				<b>From</b>
				<input class="form-control datepicker" type="text" name="fdate" id="fdate"
					value="<?php echo date("Y-m-d"); ?>">
				<b>To</b>
				<input class="form-control datepicker" type="text" name="tdate" id="tdate"
					value="<?php echo date("Y-m-d"); ?>">
				<button class="btn btn-search" onClick="load_pat_rep()" style="margin-bottom: 10px;">
					<i class="icon-search"></i> Search
				</button>
			</td>
		</tr>
	</table>

	<div id="cent_pat">

	</div>
	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
	<input type="hidden" id="mod_chk" value="0" />
	<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
		data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results"> </div>
				</div>
			</div>
		</div>
	</div>

	<input type="button" data-toggle="modal" data-target="#myModal1" id="mod1" style="display:none" />
	<input type="hidden" id="mod_chk1" value="0" />
	<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
		data-keyboard="false">
		<div class="modal-dialog" id="modal_dial">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results1"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />

<script>

	$(document).ajaxStop(function () {
		$("#loader").hide();
	});

	$(document).ajaxStart(function () {
		// $("#loader").show();
	});

	$(document).ready(function () {
		load_pat_rep();
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
	})

	function load_pat_rep() {
    $("#loader").show();
    $.post("pages/total_diagnostic_ajax.php",
        {
            fdate: $("#fdate").val(),
            tdate: $("#tdate").val(),
            branch_id: $("#branch_id").val(),  // Include branch_id in the POST data
            type: 1
        },
        function (data, status) {
            $("#cent_pat").html(data);
            $("#loader").hide();
        })
}


	function load_pat_rep_event(e) {
		if (e.which == 13) {
			load_pat_rep();
		}
	}


	// function print_report(tst,pos)
	// {
	// 	//var tstid=tid;

	// 	var uhid=$("#uhid_no").val();
	// 	var opd_id=$("#opd_id").val();
	// 	var ipd_id=$("#ipd_id").val();
	// 	var batch_no=$("#batch_no").val();
	// 	var user=$("#user").text();

	// 	var doc="";
	// 	var doc_tot=$(".lab_doc_check:checked");
	// 	for(var i=0;i<doc_tot.length;i++)
	// 	{
	// 		doc=doc+","+$(doc_tot[i]).val();
	// 	}

	// 	var url="pages/pathology_report_print.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&ipd_id="+btoa(ipd_id)+"&batch_no="+btoa(batch_no)+"&tests="+btoa(tst)+"&hlt="+btoa(tst)+"&user="+btoa(user)+"&sel_doc="+btoa(doc)+"&view="+btoa(0);

	// 	var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
	// }
	// function print_report_rad(tst,pos)
	// {
	// 	//var tstid=tid;

	// 	var uhid=$("#uhid_no").val();
	// 	var opd_id=$("#opd_id").val();
	// 	var ipd_id=$("#ipd_id").val();
	// 	var batch_no=$("#batch_no").val();
	// 	var user=$("#user").text();

	// 	var doc="";
	// 	var doc_tot=$(".lab_doc_check:checked");
	// 	for(var i=0;i<doc_tot.length;i++)
	// 	{
	// 		doc=doc+","+$(doc_tot[i]).val();
	// 	}

	// 	var url="pages/radiology_report_print.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&ipd_id="+btoa(ipd_id)+"&batch_no="+btoa(batch_no)+"&tstid="+btoa(tst)+"&category_id="+btoa(2)+"&user="+btoa(user)+"&sel_doc="+btoa(doc)+"&view="+btoa(0);

	// 	var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
	// }
	function select_all(val) {
		var tst = $(".tst");

		if (val == "sel") {
			for (var i = 0; i < tst.length; i++) {
				if ($(tst[i]).prop("checked", false)) {
					//$(tst[i]).click();
					$(tst[i]).prop("checked", true);
					test_print_group($(tst[i]).val());
				}
			}
			$("#sel_all").val("sel_u");
			$("#sel_all").html("<i class='icon-check'></i> De-Select All");
		}
		else {
			for (var i = 0; i < tst.length; i++) {
				if ($(tst[i]).prop("checked", true)) {
					//$(tst[i]).click();
					$(tst[i]).prop("checked", false);
					test_print_group($(tst[i]).val());
				}
			}
			$("#sel_all").val("sel");
			$("#sel_all").html("<i class='icon-check-empty'></i> Select All");
		}
		$(".rad_test").prop("checked", false);
	}

	function sort_reports(sort) {
		$.post("pages/total_diagnostic_ajax.php", {
			tst: $("#test_print").val(),
			uhid: $("#uhid_no").val(),
			opd_id: $("#opd_id").val(),
			ipd_id: $("#ipd_id").val(),
			batch_no: $("#batch_no").val(),
			user: $("#user").text(),
			type: 'sort',
		},
			function (data, status) {
				alert(data);
			});
	}




</script>

<style>
	.modal.fade.in {
		top: 1%;
	}

	.modal-body {
		max-height: 550px;
	}

	#myModal {
		left: 23%;
		width: 95%;
		height: auto;
	}

	#myModal1 {
		left: 40%;
		width: 50%;
		height: auto;
	}

	.table-report tr:first-child th {
		background: #666 !important;

		color: #fff;
		font-weight: bold;
	}

	.table-report tr td {
		background: white;
	}
</style>


<div id="loader" style="display:none;position:fixed;top:50%"></div>