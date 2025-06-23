<?php
$emp_id=trim($_SESSION["emp_id"]);
$branch_display="display:none;";
if($p_info["levelid"]==1)
{
	$branch_str="";
	
	$branch_display="display:none;";
	$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if($branch_num>1)
	{
		$branch_display="display:;";
	}
	
	$dept_sel_dis="";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
	
	$dept_sel_dis="disabled";
}

$branch_id=$p_info["branch_id"];

?>
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
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
					<button class="btn btn-success" onClick="view_all()"><i class="icon-search"></i> Flagged Report</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
	
</div>
<div id="loader" style="margin-top:-10%;"></div>

<style>
#myModal {
    left: 33%;
    width: 75%;
}
td.green
{
	background: #CCFFCA !important;
}
td.red
{
	background: #FFCBCA !important;
}
</style>

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
		//view_all('head_wise_detail');
		//view_all('head_wise_detail_pat');
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
	});
	function view_all()
	{
		$("#loader").show();
		$.post("pages/flagged_pat_list_ajax.php",
		{
			date1:$("#from").val(),
			date2:$("#to").val(),
			user:$("#user").text().trim(),
			type:2
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		});
	}
	function report_export(dt1,dt2)
	{
		var url = 'pages/tat_non_conformity_report_export.php?date1=' + (dt1) + '&date2=' + (dt2);
		window.location=url;
	}
	function report_print(dt1,dt2)
	{
		var url = 'pages/tat_non_conformity_report_print.php?date1=' + (dt1) + '&date2=' + (dt2);
        window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>

