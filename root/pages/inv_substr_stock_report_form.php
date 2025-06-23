<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-bottom:10px;text-align:center;">
		<b>Select Substore</b>
		<select id="substore_id" class="span3" autofocus>
			<option value="0">Substore</option>
			<?php
			$q=mysqli_query($link,"SELECT substore_id,substore_name FROM inv_sub_store order by substore_name");
			while($r=mysqli_fetch_array($q))
			{
			?>
			<option value="<?php echo $r['substore_id'];?>"><?php echo $r['substore_name'];?></option>
			<?php
			}
			?>
		</select>
		<button type="button" class="btn btn-info" onclick="aval_item()">Available Item(s)</button>
		<!--<button type="button" class="btn btn-info" onclick="shrtstk()">Sortage Item(s)</button>-->
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script>
	function aval_item()
	{
		if($("#substore_id").val()=="0")
		{
			$("#substore_id").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/inv_indent_approve_ajax.php",
			{
				substore_id:$("#substore_id").val(),
				type:5,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
	}
	
	function shrtstk()
	{
		$.post("pages/global_load_g.php"	,
		{
			type:"item_short_report",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function stk_exp()
	{
		var url="pages/inv_stock_rpt_xl.php";
		document.location=url;
	}
	
	function stk_prr(sub_id)
	{
		url="pages/inv_substore_stock_print.php?sU61d="+sub_id;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function shr_exp()
	{
		var url="pages/stock_report_short_xls.php";
		document.location=url;
	}
	function shr_prr()
	{
		url="pages/stock_report_short_print.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
