<div id="content-header">
    <div class="header_div"> <span class="header"> Laboratory Status Report</span></div>
</div>

<div class="container-fluid">

<table class="table table-bordered text-center">
		<tr>
			<td>
				<b>From</b>
				<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
				<b>To</b>
				<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" ><br>
				<button class="btn btn-success" onClick="load_data_det()" style="margin-left: 47%;">View</button>
			</td>
			<td>
				<b>Name</b>
				<input type="text" id="pat_name" onKeyup="load_data_event(event)">
			</td>
			
			<td>
				<b>ID</b>
				<input type="text" class="span2" id="var_id" onKeyup="load_data_event(event)">
			</td>
		</tr>
	</table>

	<div id="data_det">

	</div>
</div>
<div id="loader" style="margin-top:-10%;display:none"></div>	
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
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
		
		load_data_det();
	});
	
	function load_data_det()
	{
		$("#loader").show();
		$.post("pages/lab_stat_report_ajax.php",
		{
			from:$("#from").val(),
			to:$("#to").val(),
			name:$("#pat_name").val(),
			vid:$("#var_id").val(),
			type:"load_data_bar"
		},
		function(data,status)
		{
			$("#data_det").html(data);
			$("#loader").hide();
		})
	}
	
	function load_data_event(e)
	{
		if(e.which==13)
		{
			load_data_det();
		}
	}
</script>
<style>
.table-report tr:first-child th
{
  background:#666 !important;
  
  color:#fff;
  font-weight:bold;
}
.table-report tr td{
	  background: white;
}



#data_tab td,#data_tab th{ font-size:10px;}
</style>

