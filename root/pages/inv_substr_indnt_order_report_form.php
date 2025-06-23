<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<th>Select Substore</th>
			<td>
				<select id="substore">
					<option value="0">--Select--</option>
					<?php
					$q=mysqli_query($link,"SELECT substore_id,substore_name FROM `inv_sub_store` order by substore_name");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['substore_id'];?>"><?php echo $r['substore_name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<div class="btn-group">
					<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
					<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center">
				<button type="button" class="btn btn-info" onclick="srch1()">Search</button>
			</td>
		</tr>
	</table>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd',maxDate:"0"});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd',maxDate:"0"});
	});
	
	
	
	function srch1()
	{
		$("#loader").show();
		$.post("pages/inv_substr_indnt_order_report_form_ajax.php",
		{
			sbstorid:$("#substore").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:1,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	
	function edit_order(ord)
	{
		
		bootbox.dialog({ message: "<b>Redirecting to Order Update</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		setTimeout(function()
		{
			window.location="index.php?param="+btoa(153)+"&orderno="+btoa(ord);
		}, 1000);
	}
	function ord_rep_exp(ord)
	{
		var url="pages/purchase_order_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	function ord_rep_prr(ord)
	{
		url="pages/inv_indent_order_rpt.php?oRdr="+btoa(ord);
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
