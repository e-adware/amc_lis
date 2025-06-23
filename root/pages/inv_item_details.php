<!--header-->
<div id="content-header">
	<div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<td>
				<div class="btn-group">
					<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" id="fdate" class="span2" value="<?php echo date("Y-m-d"); ?>" >
					<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" id="tdate" class="span2" value="<?php echo date("Y-m-d"); ?>" >
				</div>
			</td>
			<td>
				<select id="itm">
					<option value="0">Select All Items</option>
					<?php
					$sub_category_id="6";
					$q=mysqli_query($link,"SELECT `item_id`,`item_name` FROM `item_master` WHERE `sub_category_id`='$sub_category_id' ORDER BY `item_name`");
					while($r=mysqli_fetch_assoc($q))
					{
					?>
					<option value="<?php echo $r['item_id'];?>"><?php echo $r['item_name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="btn btn-success" onclick="view_supp_wise()">View Summary</button>
				<button type="button" class="btn btn-success" onclick="view_pat_wise()">View Details</button>
			</td>
		</tr>
	</table>
</div>
<div id="res" style="max-height:400px;overflow-y:scroll;"></div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<style>
.greens td
{
	color: #005700;
}
.reds td
{
	color: #AB0000;
}
</style>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
$(document).ready(function()
{
	$("#fdate").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
	$("#tdate").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
});
function view_supp_wise()
{
	$("#loader").show();
	$.post("pages/inv_item_details_ajax.php",
	{
		fdate:$("#fdate").val(),
		tdate:$("#tdate").val(),
		itm:$("#itm").val(),
		type:1,
	},
	function(data,status)
	{
		$("#loader").hide();
		$("#res").html(data);
	});
}
function view_pat_wise()
{
	$("#loader").show();
	$.post("pages/inv_item_details_ajax.php",
	{
		fdate:$("#fdate").val(),
		tdate:$("#tdate").val(),
		itm:$("#itm").val(),
		type:2,
	},
	function(data,status)
	{
		$("#loader").hide();
		$("#res").html(data);
	});
}
</script>