<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">GST Sale </span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="text-align:center;">
		<b>GST (%)</b>
		<select id="gst" autofocus>
			<option value="0">Select</option>
			<?php
			$qq=mysqli_query($link,"SELECT DISTINCT `gst` FROM `item_master` order by `gst`");
			while($r=mysqli_fetch_array($qq))
			{
			?>
			<option value="<?php echo $r['gst']; ?>"><?php echo $r['gst']; ?></option>
			<?php
			}
			?>
		</select>
	</div>
	
	
	<div class="" style="text-align:center;">
		<b> Pharmacy</b>
		<select id="ph" autofocus >
			<option value="0">--Select All--</option>
			<?php
			$ph_qry=mysqli_query($link,"SELECT * FROM `inv_sub_store` WHERE `substore_id` < 3");
			while($ph=mysqli_fetch_assoc($ph_qry))
			{
			?>
			<option value="<?php echo $ph['substore_id'];?>"><?php echo $ph['substore_name'];?></option>
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
		<!--<button type="button" class="btn btn-info" onclick="srch()"><b class="icon-search"></b> Search</button>-->
		<button type="button" class="btn btn-info" onclick="report_print_summry()">Sale Summary</button>
		<button type="button" class="btn btn-info" onclick="report_print_datewise()">Datewise(Sale)</button>
		<button type="button" class="btn btn-info" onclick="report_print_datewise_return_gst()">Datewise(Return)</button>
		<button type="button" class="btn btn-info" onclick="report_hsn_wise()">HSN Wise</button>
		<!--<button type="button" class="btn btn-info" onclick="report_print_rcv_gst_summry()">Received Summary</button>-->
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
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	function srch()
	{
		if($("#gst").val()=="0")
		{
			$("#gst").focus();
		}
		else
		{
			$.post("pages/ph_load_data_ajax.php"	,
			{
				gst:$("#gst").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				ph:$("#ph").val(),
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
		var ph=$("#ph").val();
		url="pages/gst_rep_print.php?fdate="+f+"&tdate="+t+"&gst="+g+"&ph="+ph;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function report_print_summry(g,f,t)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var ph=$("#ph").val();
		url="pages/ph_gst_summry_rpt.php?fdate="+fdate+"&tdate="+tdate+"&ph="+ph;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function report_print_rcv_gst_summry(g,f,t)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		url="pages/ph_gst_rcvd_summry_rpt.php?fdate="+fdate+"&tdate="+tdate;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function report_print_datewise_return_gst(g,f,t)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var gst=$("#gst").val();
		var ph=$("#ph").val();
		url="pages/ph_gst_rtrn_rpt.php?fdate="+fdate+"&tdate="+tdate+"&gst="+gst+"&ph="+ph;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function report_hsn_wise(g,f,t)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var gst=$("#gst").val();
		var ph=$("#ph").val();
		url="pages/ph_gst_hsnwise_rpt.php?fdate="+fdate+"&tdate="+tdate+"&gst="+gst+"&ph="+ph;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function report_print_datewise(g,f,t)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var gst=$("#gst").val();
		var ph=$("#ph").val();
		url="pages/ph_gst_datewise.php?fdate="+fdate+"&tdate="+tdate+"&gst="+gst+"&ph="+ph;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
</script>
