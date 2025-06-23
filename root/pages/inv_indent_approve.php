<!--header-->
<div id="content-header">
    <div class="header_div">
		<span class="header"> <?php echo $menu_info["par_name"]; ?></span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		 <tr>
			<th>Search</th>
			<td>
				<input type="text" class="span4" id="srch" onkeyup="load_data()" placeholder="Search Order No / Department Name" />
			</td>
			<td>
				<div class="btn-group">
					<input type="text" value="From" style="width:60px;font-weight:bold;cursor:default;" disabled />
					<input class="form-control" type="text" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
					<input type="text" value="To" style="width:60px;font-weight:bold;cursor:default;" disabled />
					<input class="form-control" type="text" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
				</div>
			</td>
		 </tr>
		 <tr>
			<td colspan="3" style="text-align:center;">
				<button type="button" class="btn btn-info" onclick="load_data()">Search <i class="icon-search"></i></button>
			</td>
		 </tr>
	</table>
	<div id="res">
	
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd',maxDate: '0',});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd',maxDate: '0',});
		load_data();
	});
	function load_data()
	{
		$.post("pages/inv_indent_approve_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			srch:$("#srch").val().trim(),
			usr:$("#user").text().trim(),
			type:1,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function redirect_page(ord)
	{
		window.location="index.php?param="+btoa(181)+"&val="+btoa(ord);
	}
</script>
<style>
.table-report
{
	background:#FFFFFF;
}
.order_list:hover
{
	cursor:pointer;
	color:#0A0752;
}
</style>
