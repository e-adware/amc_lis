<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Lab Daily Accounts</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" ><br>
					<p>
					<button class="btn btn-success " onClick="view_all('lab_account','0')">All Departments</button>
					<button class="btn btn-success " onClick="view_all('dept_wise_report','1')">Pathology Report</button>
					<button class="btn btn-success " onClick="view_all('dept_wise_report','2')">Radiology Report</button>
					<button class="btn btn-success " onClick="view_all('dept_wise_report','3')">Cardiology Report</button>
					<button class="btn btn-success " onClick="view_all('bal_received','')">View Balance Received</button>
					<button class="btn btn-success " onClick="view_all('user_summary','')">User Summary</button>
					<button class="btn btn-success " onClick="view_all('detail_report','')">Details Report</button>
					</p>
					<p>
					<button class="btn btn-success " onClick="view_all('discount_report','')">Discount Report</button>
					<button class="btn btn-success " onClick="view_all('daily_expense','')">Daily Expense</button>
					<button class="btn btn-success " onClick="view_all('cheque_payment','')">Cheque Payment</button>
					<button class="btn btn-success " onClick="view_all('card_payment','')">Card Payment</button>
					<button class="btn btn-success " onClick="view_all('credit','')">Credit</button>
					<button class="btn btn-success " onClick="view_all('cancel_report','')">Cancel Report</button>
					</p>
					<!--<input type="button" name="button13" id="button13" class="btn btn-success" value="User Summary" onclick="popitup('pages/user_summary_rpt.php')" />
					<input type="button" name="button4" id="button4" class="btn btn-success" value="Details Report" onclick="popitup('pages/daily_report.php')" />-->
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
	<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">
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
	$(document).ready(function(){
		$("#loader").hide();
		view_all('lab_account');
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	});
	function view_all(typ,val)
	{
		$("#loader").show();
		$.post("pages/account_details_all.php",
		{
			type:typ,
			val:val,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function print_doc_pat(el)
	{
		$("#excel_btn_hide").hide();
		var restorepage = $('body').html();
		var printcontent = $('#' + el).clone();
		$('body').empty().html(printcontent);
		window.print();
		$('body').html(restorepage);
	}
	function popitup(url)
	{
		var txtfrom=document.getElementById("from").value;
		var txtto=document.getElementById("to").value;
		url=url+"?date1="+txtfrom+"&date2="+txtto;
		newwindow=window.open(url,'window','left=10,top=10,height=600,witdh=600,menubar=1,resizeable=0,scrollbars=1');
	}
</script>
<style>
.ScrollStyle
{
    max-height: 340px;
    overflow-y: scroll;
}
.btn
{
	//padding: 3px 5px;
}
</style>
