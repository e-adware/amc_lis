<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<th>From</th>
			<th>To</th>
			<th>Select Resource</th>
			<th>Select Employee</th>
		</tr>
		<tr>
			<td>
				<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" />
			</td>
			<td>
				<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" />
			</td>
			<td>
				<select id="res_id" onchange="load_emp_list()">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `ot_type_master` ORDER BY `type`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['type_id'];?>"><?php echo $r['type'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td id="emp_list">
				<select id="emp_id" onchange="search_rep()">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT DISTINCT `emp_id` FROM `ot_resource_master`");
					while($r=mysqli_fetch_array($q))
					{
						$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$r[emp_id]'"));
					?>
					<option value="<?php echo $r['emp_id'];?>"><?php echo $e['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="text-align:center;">
				<button type="button" onClick="search_rep()" class="btn btn-success"><i class="icon-search icon-large"></i> Search</button>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
<!--
	<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">
-->
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
	});
	function load_emp_list()
	{
		$("#loader").show();
		$.post("pages/ot_service_reports_ajax.php",
		{
			type:1,
			res_id:$("#res_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#emp_list").html(data);
			search_rep();
		})
	}
	function search_rep()
	{
		$("#loader").show();
		$.post("pages/ot_service_reports_ajax.php",
		{
			type:2,
			date1:$("#from").val(),
			date2:$("#to").val(),
			res_id:$("#res_id").val(),
			emp_id:$("#emp_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
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
