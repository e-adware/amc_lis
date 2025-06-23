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
					<b>From</b>
					<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					<br>
					<!--<button class="btn btn-success" onClick="view_all('summary_reports')">View Summary </button>
					<button class="btn btn-success" onClick="view_all('doctor_wise_summary')">Doctor Wise Summary </button>
					<button class="btn btn-success" onClick="view_all('service_wise_summary')">Service Wise Summary </button>-->
					<button class="btn btn-success" onClick="view_all('service_summary')">Service Summary </button>
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
		$.post("pages/summary_report_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			//$("#load_all").slideUp(500,function(){ $("#load_all").html(data).slideDown(500); });
			$("#load_all").html(data).slideDown(500);
		})
	}
	
	function print_page(val,date1,date2)
	{
		var user=$("#user").text().trim();
		
		if(val=="summary_reports")
		{
			var url="pages/summary_report_print.php?date1="+date1+"&date2="+date2+"&EpMl="+user;
		}
		if(val=="doctor_wise_summary")
		{
			var url="pages/summary_report_doctor_print.php?date1="+date1+"&date2="+date2+"&EpMl="+user;
		}
		if(val=="service_summary")
		{
			var url="pages/service_summary_report_print.php?date1="+date1+"&date2="+date2+"&EpMl="+user;
		}
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
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
.ipd_serial
{
	display:none;
}
</style>
