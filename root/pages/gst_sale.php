<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">GST Sale</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="text-align:center;">
		<b>GST (%)</b>
		<select id="gst" autofocus>
			<option value="0">Select</option>
			<?php
			$qq=mysqli_query($link,"SELECT DISTINCT `gst_percent` FROM `ph_item_master` where `gst_percent`>0 order by `gst_percent`");
			while($r=mysqli_fetch_array($qq))
			{
			?>
			<option value="<?php echo $r['gst_percent']; ?>"><?php echo $r['gst_percent']; ?></option>
			<?php
			}
			?>
		</select>
	</div>
	<div class="" style="text-align:center;">
		<b>From</b>
		<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<b>To</b>
		<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
	</div>
	<div class="" style="margin-bottom:10px;text-align:center;">
		<button type="button" class="btn btn-info" onclick="srch()"><b class="icon-search"></b> Search</button>
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd',maxDate:"0"});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd',minDate:"0"});
	});
	function srch()
	{
		if($("#gst").val()=="0")
		{
			$("#gst").focus();
		}
		else
		{
			$.post("pages/nursing_load_g.php"	,
			{
				gst:$("#gst").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				type:"load_gst_sale_report",
			},
			function(data,status)
			{
				$("#res").html(data);
			})
		}
	}
	function report_xls(g,f,t)
	{
		var url="pages/gst_rep_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	function report_print(g,f,t)
	{
		url="pages/gst_rep_print.php?fdate="+f+"&tdate="+t+"&gst="+g;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
