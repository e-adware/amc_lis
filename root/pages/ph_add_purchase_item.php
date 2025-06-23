<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Purchase Received Bill</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<th width="30%">Select Supplier</th>
			<td>
				<select id="supp" class="span4" autofocus>
					<option value="0">Select Supplier</option>
					<?php
					$q=mysqli_query($link,"SELECT `id`,`name` FROM `inv_supplier_master` ORDER BY `name`");
					while($r=mysqli_fetch_assoc($q))
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
			<td colspan="2" style="text-align:center;">
				<div class="btn-group">
					<input type="text" value="From" style="width:60px;font-weight:bold;cursor:default;" disabled />
					<input class="form-control" type="text" class="span1" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
					<input type="text" value="To" style="width:60px;font-weight:bold;cursor:default;" disabled />
					<input class="form-control" type="text" class="span1" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button type="button" class="btn btn-primary" onclick="load_bill()">Search</button>
			</td>
		</tr>
	</table>
	<div id="res"></div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	function load_bill()
	{
		if($("#supp").val()=="0")
		{
			$("#supp").focus();
		}
		else
		{
			$.post("pages/ph_add_purchase_item_ajax.php",
			{
				supp:$("#supp").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				type:1,
			},
			function(data,status)
			{
				$("#res").html(data);
			})
		}
	}
	function add_to_bill(rcv)
	{
		//window.location="index.php?param="+btoa(176)+"&ipd="+btoa(rcv);
		window.location="index.php?param="+btoa(173)+"&ipd="+btoa(rcv);
	}
	function view_bill(ord)
	{
		//var url="pages/inv_supplier_ldger_rpt.php?oRd="+btoa(ord);
		var url="pages/purchase_receive_rep_print.php?rCv="+btoa(ord);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
