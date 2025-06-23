<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Report</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-bottom:10px;text-align:center;">
		<b>From</b>
		<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<b>To</b>
		<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
	</div>
	<div class="" style="margin-bottom:10px;text-align:center;">
		<button type="button" id="bt1" class="btn b btn-info" onclick="reports(1)">Donor Inventory Report</button>
		<button type="button" id="bt2" class="btn b btn-info" onclick="reports(2)">Donor Registration Report</button>
		<button type="button" id="bt3" class="btn b btn-info" onclick="reports(3)">Donor Rejected Report</button>
		<button type="button" id="bt4" class="btn b btn-info" onclick="reports(4)">Blood Component Report</button><br/>
		<button type="button" id="bt5" class="btn b btn-info" onclick="reports(5)">Blood Receipt Report</button>
		<button type="button" id="bt6" class="btn b btn-info" onclick="reports(6)">Blood Request Report</button>
		<button type="button" id="bt7" class="btn b btn-info" onclick="reports(7)">Blood Issued Report</button>
		<button type="button" id="bt8" class="btn b btn-info" onclick="reports(8)">Expiring Today Report</button><br/>
		<button type="button" id="bt9" class="btn b btn-info" onclick="reports(9)">Expired Report</button>
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<style>
.b
{
	width:200px;
	margin-bottom:5px;
}
.clk, .clk:hover, .clk:focus
{
	background:#4A3F9E;
}
</style>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
		check_exp_date();
		//alert($("#user").text().trim());
	});
	function reports(type)
	{
		$(".b").removeClass("clk");
		$("#bt"+type).addClass("clk");
		$.post("pages/blood_all_reports_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:type,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function print_file(type)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var url="pages/blood_all_reports_print.php?fdate="+fdate+"&tdate="+tdate+"&type="+type;
		window.open(url,'window','left=10,top=10,height=600,width=1020,menubar=1,resizeable=0,scrollbars=1');
		//document.location=url;
	}
	function export_file(type)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var url="pages/blood_all_reports_csv.php?fdate="+fdate+"&tdate="+tdate+"&type="+type;
		window.open(url,'window','left=10,top=10,height=600,width=1020,menubar=1,resizeable=0,scrollbars=1');
	}
	function check_exp_date()
	{
		$.post("pages/global_load_g.php",
		{
			usr:$("#user").text().trim(),
			type:"check_exp_date",
		},
		function(data,status)
		{
			//alert(data);
		})
	}
</script>
