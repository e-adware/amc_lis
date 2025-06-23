<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" style="">
			<tr>
				<td style="text-align:center;" width="50%">
					<div class="btn-group">
						<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="fdate" style="width:150px;" value="<?php echo date("Y-m-d"); ?>" >
						<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="tdate" style="width:150px;" value="<?php echo date("Y-m-d"); ?>" >
					</div>
				</td>
			
				<td style="text-align:center">
					<button type="button" class="btn btn-success" onclick="srch()">Search</button>
				</td>
			</tr>
		</table>
	</div>
	<div class="highcharts-figure">
		<div style="max-height:400px;overflow-y:scroll;">
			<div id="res">
			
			</div>
		</div>
	</div>
</div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script src="../jss/highcharts.js"></script>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
	});
	
	function srch()
	{
		$("#loader").show();
		$.post("pages/inv_stock_entry_report_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:1,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		});
	}
	
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	
	function entry_print(fdt,tdt)
	{
		url="pages/inv_stock_entry_report_print.php?fdt="+btoa(fdt)+"&tdt="+btoa(tdt);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
<style>
.highcharts-figure, .highcharts-data-table table {
    width: 100%;
    margin: 10px auto;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
.highcharts-credits
{
	display:none !important;
}
</style>
