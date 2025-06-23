<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Health Guide Reports</span></div>
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
					<select id="sguide_id" class="span2" onChange="load_hguide()" style="display:none;">
						<option value="0">All Executive</option>
					<?php
						$sguide_qry=mysqli_query($link, " SELECT `sguide_id`, `name` FROM `super_health_guide` ORDER BY `name` ");
						while($sguide=mysqli_fetch_array($sguide_qry))
						{
							echo "<option value='$sguide[sguide_id]'>$sguide[name]</option>";
						}
					?>
					</select>
					<select id="hguide_id" class="span2" onChange="view_all()">
						<option value="0">Select Agent</option>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all()">View</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
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
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		load_hguide();
	});
	function load_hguide()
	{
		$("#loader").show();
		$.post("pages/health_guide_reports_load.php",
		{
			type:"load_hguide",
			sguide_id:$("#sguide_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#hguide_id").show().html(data);
			view_all();
		})
	}
	function view_all()
	{
		$("#loader").show();
		$.post("pages/health_guide_reports_load.php",
		{
			type:"health_guide_reports",
			date1:$("#from").val(),
			date2:$("#to").val(),
			sguide_id:$("#sguide_id").val(),
			hguide_id:$("#hguide_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function print_page(dt1,dt2,sguide,hguide)
	{
		url="pages/health_guide_reports_print.php?fdate="+dt1+"&tdate="+dt2+"&sguide="+hguide+"&hguide="+hguide;
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
</style>
