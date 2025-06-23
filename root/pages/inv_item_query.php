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
				<select id="itm" class="span4">
					<option value="0">Select Item</option>
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
				<button type="button" class="btn btn-success" onclick="view_details()">Search</button>
				<!--<button type="button" class="btn btn-success" onclick="view_pat_wise()">View Details</button>-->
			</td>
		</tr>
	</table>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<style>
tr:hover
{
	background:none;
}
</style>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
		
		$("#itm").select2({ theme: "classic" });
	});
	
	function view_details()
	{
		if($("#itm").val()=="0")
		{
			$("#itm").focus();
			
		}
		else
		{
			$("#loader").show();
			$.post("pages/inv_item_query_ajax.php",
			{
				itm:$("#itm").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				user:$("#user").text().trim(),
				type:1,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			});
		}
	}
	function srch()
	{
		var jj=1;
		if($("#itm").val()=="0")
		{
			//alert("Please select a item");
			$("#itm").focus();
			jj=0;
			
		}
		
		if(jj==1)
		{
		$.post("pages/ph_item_query_ajax.php",
		{
			itm:$("#itm").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"item_query",
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
	
	
	
	function report_print_return(g,splr)
	{
		
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		url="pages/inv_item_rtn_to_splr_rpt.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splr+"&billno="+g;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	
</script>
