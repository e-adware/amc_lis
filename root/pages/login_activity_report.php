<div id="content-header">
    <div class="header_div"> <span class="header">Login Activity Report</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control datepicker" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
					<input type="text" id="ftime" class="timepicker" value="09:00" placeholder="HH:MM" style="width:50px" />
					<b>To</b>
					<input class="form-control datepicker" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
					<input type="text" id="ttime" class="timepicker" value="14:00" placeholder="HH:MM" style="width:50px" />
				</center>
			</td>
			
			<td>
				<select id="user">
					<option value="0">--All User--</option>
					<?php
					$user=mysqli_query($link,"select * from employee order by name");
					while($us=mysqli_fetch_array($user))
					{
						echo "<option value='$us[emp_id]'>$us[name]</option>";
					}
					?>
				</select>
			</td>
			
			<td>
				<button class="btn btn-info" onclick="search_user()"><i class="icon-search"></i> Search</button>
			</td>
		</tr>
	</table>
	
	<div id="user_data"></div>
</div>

<div id="loader" style="position:fixed;top:50%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>

<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->

<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />

<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,showSecond: true,showMillisec: true,}});
	});
	
	
	function search_user()
	{
		$("#loader").show();
		$.post("pages/login_activity_report_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			ftime:$("#ftime").val(),
			ttime:$("#ttime").val(),
			user:$("#user").val(),
			type:1
		},
		function(data,status)
		{
			$("#user_data").html(data);
			$("#loader").hide();
		})
		
	}
	
</script>
