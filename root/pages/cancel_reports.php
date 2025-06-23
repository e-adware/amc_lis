<?php
$branch_str=" AND branch_id='$p_info[branch_id]'";
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
}

$branch_id=$p_info["branch_id"];
?>
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<td>
				<center>
					<select id="branch_id" class="span2" onChange="load_centres()" style="<?php echo $branch_display; ?>">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
					<input type="text" id="search_data" placeholder="Search(UHID or Bill No.)">
					<b>From</b><!-- <?php echo date("Y-m-d"); ?> -->
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
				</center>
			</td>
		</tr>
		<tr>
			<td>
				<center>
					<button class="btn btn-search" onclick="view('patient_cancel')"><i class="icon-search"></i> Patient Cancel</button>
					<button class="btn btn-search" onclick="view('payment_cancel')"><i class="icon-search"></i> Payment Cancel</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_data">

	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<input type="hidden" id="report_type">
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->
<script>
	$(document).ready(function()
	{
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
	});
	
	function view(val)
	{
		$("#loader").show();
		$.post("pages/cancel_reports_data.php",
		{
			type:val,
			branch_id:$("#branch_id").val(),
			search_data:$("#search_data").val(),
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		 function(data,status)
		 {
			 $("#load_data").html(data)
			 $("#loader").hide();
			 $("#search_data").show();
		 })
	}
	function print_page(type,date1,date2,search_data,branch_id)
	{
		var user=$("#user").text().trim();
		
		url="pages/cancel_reports_print.php?date1="+date1+"&date2="+date2+"&type="+type+"&search_data="+search_data+"&branch_id="+branch_id;
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
#load_data
{
    max-height: 400px;
    overflow-y: scroll;
}
</style>
