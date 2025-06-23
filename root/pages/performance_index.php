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
					<div class="btn-group">
						<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
						<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
					</div>
					<br>
					<button class="btn btn-success" id="btn1" onClick="view_all(1)">View</button>
					
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
<!--
	<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">
-->
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
	$(document).ready(function()
	{
		$("#loader").hide();
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd',maxDate: '0',});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd',maxDate: '0',});
	});
	function view_all(typ)
	{
		$(".btn").removeClass("btn-inverse");
		$(".btn").addClass("btn-success");
		$("#btn"+typ).removeClass("btn-success");
		$("#btn"+typ).addClass("btn-inverse");
		$("#loader").show();
		$.post("pages/performance_index_ajax.php",
		{
			type:typ,
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	
	function print_report(fdt,tdt)
	{
		var url="pages/performance_rpt_print.php?fdate="+fdt+"&tdate="+tdt;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
.table-report
{
	background:#FFFFFF;
}
</style>
