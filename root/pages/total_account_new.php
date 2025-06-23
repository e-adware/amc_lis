<?php
$emp_id=trim($_SESSION["emp_id"]);

$level_id=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$emp_id' "));

if($level_id['levelid']=='1')
{
	$dept_sel_dis="";
}else
{
	//$dept_sel_dis="disabled";
	$dept_sel_dis="";
}

$not_accountant = array();
array_push($not_accountant, 5, 6, 11, 12, 13, 20, 21);
$not_accountant = join(',',$not_accountant);

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Daily Accounts Reports</span></div>
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
					<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
				</center>
			</td>
		</tr>
		<tr>	
			<td>
				<center>
					<button class="btn btn-success" onClick="view_all('daily_detail')">Daily Details</button>
					<button class="btn btn-success" onClick="view_all('daily_details')">Daily Summary</button>
					<button class="btn btn-success" onClick="view_all('cabin')">Cabin Report</button>
					<button class="btn btn-success" onClick="view_all('esi')">ESI Report</button>
					<button class="btn btn-success" onClick="view_all('insurance')">Insurance Report</button>
					<button class="btn btn-success" onClick="view_all('pending_bill')">Pending Bill</button>
					<button class="btn btn-success" onClick="view_all('freepatient')">Free Patient</button>
					<button class="btn btn-success" onClick="view_all('centre_summary')">Centre Summary</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
	<!--<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">-->
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	});
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/daily_account_details_all.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function pending_remark_up(e,uhid,opd_id,n)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$.post("pages/daily_account_details_all.php",
			{
				type:"save_pending_remark",
				uhid:uhid,
				opd_id:opd_id,
				remark:$("#pending_remark"+n).val(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				$(".pending_remark").fadeOut(50);
				$("#remark_val"+n).fadeIn(500).text($("#pending_remark"+n).val());
			})
		}
	}
	function pending_remark_blur(uhid,opd_id,n)
	{
		$(".pending_remark").fadeOut(50);
		$("#remark_val"+n).fadeIn(500);
	}
	function pending_remark(uhid,opd_id,n)
	{
		$("#remark_val"+n).fadeOut(50);
		$(".pending_remark").fadeOut(50);
		$("#pending_remark"+n).fadeIn(500).focus().val($("#remark_val"+n).text().trim());
		$("#pending_remark"+n).css({"position": "relative", "z-index": "10000"});
	}
	function view_exp()
	{
		var fdate=$("#from").val();
		var tdate=$("#to").val();
		url="pages/daily_expense_rep.php?fdate="+fdate+"&tdate="+tdate;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
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
	function print_page(val,date1,date2,encounter,user_entry,pay_mode)
	{
		if(val=="detail")
		{
			url="pages/detail_account_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&pay_mode="+pay_mode;
		}
		if(val=="summary")
		{
			url="pages/summary_account_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="discount")
		{
			url="pages/discount_account_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="expense")
		{
			url="pages/expense_account_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		
		if(val=="free_pat")
		{
			url="pages/lab_free_patient_rpt.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		
		if(val=="cancel_pat")
		{
			url="pages/cancel_pat_account_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="payment_cancel")
		{
			url="pages/payment_cancel_account_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="pay_refund")
		{
			url="pages/pay_refund_rpt.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="all_patient")
		{
			var from=$("#sl_frm").val();
			var to=$("#sl_to").val();
			url="pages/all_patient_rpt.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&from="+from+"&to="+to;
		}
		if(val=="view_summry")
		{
			url="pages/summry_rpt.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="centre_summary")
		{
			url="pages/centre_summary_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="pending_bill")
		{
			url="pages/pending_bill_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="insurance")
		{
			url="pages/insurance_report_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="esi")
		{
			url="pages/esi_report_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="cabin")
		{
			url="pages/cabin_report_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="daily_details")
		{
			url="pages/daily_details_report_print.php?date1="+date1+"&date2="+date2;
		}
		if(val=="daily_detail")
		{
			url="pages/daily_detail_report_print.php?date1="+date1+"&date2="+date2;
		}
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function export_page(val,date1,date2,encounter,user_entry,pay_mode)
	{
		if(val=="insurance")
		{
			url="pages/insurance_report_xls.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="esi")
		{
			url="pages/esi_report_xls.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="cabin")
		{
			url="pages/cabin_report_xls.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry;
		}
		if(val=="daily_details")
		{
			url="pages/daily_details_report_xls.php?date1="+date1+"&date2="+date2;
		}
		if(val=="daily_detail")
		{
			url="pages/daily_detail_report_xls.php?date1="+date1+"&date2="+date2;
		}
		window.location=url;
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
@media print {
  body * {
    visibility: hidden;
  }
  #load_all, #load_all * {
    visibility: visible;
  }
  #load_all {
	  overflow:visible;
    position: absolute;
    left: 0;
    top: 0;
  }
}
.btn
{
	
}
.ui-autocomplete {
     z-index: 9999 !important;
}
</style>

<?php

//You can also use $stamp = strtotime ("now"); But I think date("Ymdhis") is easier to understand.
$stamp = date("Ymdhis");
$ip = $_SERVER['REMOTE_ADDR'];
$orderid = "$stamp-$ip";
$orderid = str_replace(".", "", "$orderid");
echo($orderid);
echo "<br>";

?>
