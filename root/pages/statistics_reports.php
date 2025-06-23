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
					<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m"); ?>" >
					<b>To</b>
					<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m"); ?>" >
					
					<br>
					<button class="btn btn-success" onClick="view_all('daily_volume')">Daily Volume Statistics</button>
					<button class="btn btn-success" onClick="view_all('daily_revenue')">Daily Revenue Statistics</button>
				</center>
			</td>
		</tr>
	</table>
	<br>
	<br>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
	<!--<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">-->
</div>
<div id="loader" style="margin-top:0%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/yyyy-mm/jquery-ui.css" />
<script src="include/yyyy-mm/jquery.js"></script>
<script src="include/yyyy-mm/jquery-ui.min.js"></script>
<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			maxDate: '0',
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			dateFormat: 'yy-mm',
			onClose: function(dateText, inst)
			{
				function isDonePressed(){
					return ($('#ui-datepicker-div').html().indexOf('ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover') > -1);
				}
				if (isDonePressed()){
					var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
					var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
					$(this).datepicker('setDate', new Date(year, month, 1));
					console.log('Done is pressed')
				}
			}
		});
	});
	function view_all(typ)
	{
		//~ $("#loader").show();
		//~ $.post("pages/statistics_reports_load.php",
		//~ {
			//~ type:typ,
			//~ date1:$("#from").val(),
			//~ date2:$("#to").val(),
		//~ },
		//~ function(data,status)
		//~ {
			//~ $("#loader").hide();
			//~ $("#load_all").show().html(data);
		//~ })
		excel_export(typ);
	}
	function excel_export(typ)
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		if(typ=="daily_volume")
		{
			var url="pages/statistics_reports_volume_xls.php?date1="+date1+"&date2="+date2;
		}
		if(typ=="daily_revenue")
		{
			var url="pages/statistics_reports_revenue_xls.php?date1="+date1+"&date2="+date2;
		}
		document.location=url;
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
.ui-datepicker-calendar {
	display: none;
}
.table th, .table td
{
	text-align: center;
}
</style>
