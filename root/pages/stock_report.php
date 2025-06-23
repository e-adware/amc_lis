<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Stock Report</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-bottom:10px;text-align:center;">
		<button type="button" class="btn btn-info" onclick="aval_item()">Available Item(s)</button>
		<!--<button type="button" class="btn btn-info" onclick="shrtstk()">Sortage Item(s)</button>
		<button type="button" class="btn btn-info" onclick="store_rcv_report()">Received Report</button>-->
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<div id="loader" style="display:none;margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script>
	function aval_item()
	{
		$("#loader").show();
		$.post("pages/stock_report_ajax.php",
		{
			type:1,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function shrtstk()
	{
		$("#loader").show();
		$.post("pages/stock_report_ajax.php",
		{
			type:2,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function store_rcv_report()
	{
		$("#loader").show();
		$.post("pages/stock_report_ajax.php",
		{
			type:3,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function stk_exp()
	{
		var url="pages/stock_report_stock_xls.php";
		document.location=url;
	}
	function stk_prr()
	{
		url="pages/stock_report_stock_print.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function shr_exp()
	{
		var url="pages/stock_report_short_xls.php";
		document.location=url;
	}
	function shr_prr()
	{
		url="pages/stock_report_short_print.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_ph_rcv_report()
	{
		url="pages/print_ph_rcv_report.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function issue_report(isu)
	{
		url="pages/inv_frm_sbstr_itm_issue_rpt.php?orderno="+isu;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
