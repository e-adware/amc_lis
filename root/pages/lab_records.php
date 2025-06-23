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
					<button class="btn btn-success" onClick="view_all('lab_records_test_pat')">Laboratory record</button>
					<button class="btn btn-success" onClick="view_all('lab_gender_record')">Gender record</button>
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
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
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
		$.post("pages/lab_records_data.php",
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
	function print_page(val,date1,date2,service_id)
	{
		if(val=="lab_records_test_pat")
		{
			url="pages/lab_records_print.php?date1="+date1+"&date2="+date2+"&service_id="+service_id;
		}
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1200');
	}
	function export_excel()
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		var service_id=$("#service_id").val();
		
		var url="pages/lab_records_xls.php?date1="+date1+"&date2="+date2;
		document.location=url;
	}
	
	function mouse_over(val)
	{
		$("."+val+"_test").css({'color': '#F00'});
	}
	function mouse_out(val)
	{
		$("."+val+"_test").css({'color': '#666666'});
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
.table th, .table td
{
	text-align: center;
}
</style>
