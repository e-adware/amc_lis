<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Item Return Report (To Supplier)</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-bottom:10px;text-align:center;">
		<b>From</b>
		<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<b>To</b>
		<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
	</div>
	
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" style="width:50%;margin:0 auto;">
			<tr>
				<td>Select Supplier</td>
				<td>
					<select id="slectsplr">
						<option value="0">--Select--</option>
						<?php
						$q=mysqli_query($link,"SELECT id,name FROM ph_supplier_master order by name");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['id'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="button" class="btn" onclick="srch()">Search</button>
				</td>
			</tr>
		</table>
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd',maxDate:"0"});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd',minDate:"0"});
	});
	
	function srch()
	{
		
			$.post("pages/ph_load_data_ajax.php"	,
			{
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				splr:$("#slectsplr").val(),
				type:"item_rtrn_to_splr",
			},
			function(data,status)
			{
				$("#res").html(data);
			})
		
	}
	
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	
	function rcv_rep_prr(ord)
	{
		url="pages/purchase_receive_rep_print.php?orderno="+ord;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
