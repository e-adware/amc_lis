<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	
	<table class="table table-bordered table-condensed text-center">
		<tr>
			<td colspan="4">
				<center>
					<b>From</b>
					<input class="form-control datepicker" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
					<select id="dep" name="dep">
					<?php
						$dep=mysqli_query($link,"select distinct a.type_id, b.`name` from testmaster a, `test_department` b where a.`type_id`=b.`id` AND a.category_id='1' order by b.`name`");
						while($dp=mysqli_fetch_array($dep))
						{
							if($dp[type_id]==20){ $sel="Selected='selected'";} else { $sel='';}
							//$dnm=mysqli_fetch_array(mysqli_query($link,"select * from test_department where id='$dp[type_id]'"));
							echo "<option value='$dp[type_id]' $sel>$dp[name]</option>";
						}
					?>
					</select>
					<button type="button" id="search1" class="btn btn-info" onclick="view_report('1')">Summary</button>
					<button type="button" id="search2" class="btn btn-info" onclick="view_report('2')">Test Wise</button>
					<button type="button" id="search3" class="btn btn-info" onclick="view_report('3')">Test Report Details</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="loader" style="position:fixed;display:none;margin-top:1%;left:50%;"></div>
	<div id="res_data">
	
	</div>
	
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<style>
	#test_data
	{
		margin:0px;
		padding:0px;
	}
	#test_data tr td
	{
		border: 0px;
		border-bottom: 1px solid #666 !important;
		padding: 1px;
	}
</style>
<script>
	$(document).ready(function()
	{
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
	});
function view_report(typ)
{
	$("#loader").show();
	$.post("pages/report_dep_wise_ajax.php",
	{
		fdate:$("#fdate").val(),
		tdate:$("#tdate").val(),
		dep:$("#dep").val(),
		type:typ
	},
	function(data,status)
	{
		$("#loader").hide();
		$("#res_data").html(data);
	});
}
function report_export(fdt,tdt,dep,typ)
{
	var url="pages/report_dep_wise_export.php?fDt="+fdt+"&tDt="+tdt+"&dEp="+dep+"&tYp="+typ;
	window.location=url;
}
function report_print(fdt,tdt,dep,typ)
{
	var url="pages/report_dep_wise_print.php?fDt="+fdt+"&tDt="+tdt+"&dEp="+dep+"&tYp="+typ;
	window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}
</script>
