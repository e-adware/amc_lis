<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Backup Database</span></div>
</div>
<!--End-header-->
<div class="container-fluid" onkeypress="anykey_press(event)">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b><!-- <?php echo date("Y-m-d"); ?> -->
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					<br>
					<!--<input type="button" name="button4" id="button4" class="btn btn-success" value="Backup Whole Database" onclick="popitup('pages/data_export_data_whole.php?typ=1')" />-->
					<input type="button" name="button" id="button" class="btn btn-info" value="Backup Selected Date" onclick="popitup('pages/data_export_data_date.php?typ=2')" />
				</center>
			</td>
		</tr>
	</table>
</div>
<div id="loader" style="margin-top:0%;"></div>
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
	function popitup(url)
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		
		url=url+"&date1="+date1+"&date2="+date2;
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=400,width=500');
	}
	function hid_div(e)
	{
		if(e.ctrlKey==1)
		{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode=="119")
			{
				window.location.href="processing.php?param=402";
			}
		}
	}
</script>
