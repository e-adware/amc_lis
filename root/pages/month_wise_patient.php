<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<td style="text-align:center;">
				<div class="btn-group">
					<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" class="datepicker" id="fdate" style="width:80px;cursor:pointer;" value="<?php echo date("Y-m"); ?>" readonly />
					<input type="text" value="To" style="width:40px;cursor:default;font-weight:bold;" disabled />
					<input type="text" class="datepicker" id="tdate" style="width:80px;cursor:pointer;" value="<?php echo date("Y-m"); ?>" readonly />
				</div>
			</td>
			<td>
				<button type="button" class="btn btn-success" onclick="view()">View</button>
			</td>
		</tr>
	</table>
	<div id="res" style="max-height:350px;overflow-y:scroll;"></div>
	<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
</div>
<link rel="stylesheet" href="../css/loader.css" />

<!-- Time -->
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
/* Hide day view */
.ui-datepicker-calendar {
    display: none;
}
</style>
<script>
	function view()
	{
		$("#loader").show();
		$.post("pages/month_wise_patient_ajax.php",
		{
			fdate	:$("#fdate").val().trim(),
			tdate	:$("#tdate").val().trim(),
			type	:1
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		});
	}
	function report_print(fdt,tdt)
	{
		var url="pages/month_wise_patient_print.php?fDt="+fdt+"&tDt="+tdt;
		window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
	}
	function setupMonthYearPicker(id) {
		$(id).datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			dateFormat: 'yy-mm',
			maxDate: 0,
			onClose: function(dateText, inst) {
				// Prevent day selection
				var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
				$(this).datepicker('setDate', new Date(year, month, 1));
			},
			beforeShow: function(input, inst) {
				var val = $(input).val();
				if (val) {
					var year = val.split('-')[0];
					var month = parseInt(val.split('-')[1], 10) - 1;
					$(input).datepicker('option', 'defaultDate', new Date(year, month, 1));
					$(input).datepicker('setDate', new Date(year, month, 1));
				}
				$(input).datepicker('widget').addClass('month-year-picker');
			}
		});
	}

	// Initialize both fields
	$(function() {
		setupMonthYearPicker("#fdate");
		setupMonthYearPicker("#tdate");
	});
</script>