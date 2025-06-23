<html>
<head>
<title>Order List</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/custom.css" />
<link rel="stylesheet" href="../../css/loader.css" />
</head>
<body onkeyup="close_window(event)" oncopy="return false;" oncut="return false;" onpaste="return false;" oncontextmenu="return false;">
<?php
//include'../../includes/connection.php';

?>
<div class="container-fluid">
	Store short item list.
	<select id="days" class="span2 noprint" onchange="load_data()">
		<option value="7">7 Days</option>
		<option value="10">10 Days</option>
		<option value="15">15 Days</option>
		<option value="20">20 Days</option>
		<option value="25">25 Days</option>
		<option value="30">30 Days</option>
		<option value="45">45 Days</option>
		<option value="60">60 Days</option>
		<option value="90">90 Days</option>
	</select>
	<input type="text" id="item_name" class="span5 noprint" style="height:30px;" placeholder="Item Name (Search)" /><br/>
	<div id="res"></div>
	<div id="loader" style="display:none;margin-top:-10%;"></div>
</div>
<script src="../../js/jquery.min.js"></script>
<script>
	$(document).ready(function()
	{
		load_data();
		$('#item_name').keyup(item_search(function(e){ },500));
	});
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
	function item_search(callback, ms)
	{
		var timer = 0;
		return function()
		{
			var context = this, args = arguments;
			clearTimeout(timer);
			timer = setTimeout(function()
			{
				//aval_item(context.value);
				load_data();
			}, ms || 0);
		};
	}
	function rem_list(ths,itm)
	{
		$(ths).parent().parent().remove();
		sl();
		//alert(itm);
		//$("#loader").show();
		$.post("inv_ord_list_ajax.php",
		{
			itm:itm,
			type:2
		},
		function(data,status)
		{
			//$("#loader").hide();
			//alert(data);
			//$("#res").html(data);
		})
	}
	function sl()
	{
		var tr=$(".tr");
		for(var i=0; i<(tr.length); i++)
		{
			$(".tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
	}
	function load_data()
	{
		$("#loader").show();
		$.post("inv_ord_list_ajax.php",
		{
			name:$("#item_name").val().trim(),
			days:$("#days").val(),
			type:1,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
</script>
<style>
@page
{
	margin-left:20px;
	margin-right:20px;
}
.table tr th, .table tr td
{
	padding:0px 1px 0px 1px;
	font-size:12px;
}
@media print{
 .noprint{
	 display:none !important;
 }
}
</style>
</body>
</html>
