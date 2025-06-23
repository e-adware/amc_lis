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
					<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
				</center>
			</td>
		</tr>
		<tr>
			<td>
				<center>	
					<button class="btn btn-success" onClick="ot_booking_list('')">Register List</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		<input type="text" id="d_id" value="0" />
		<input type="text" id="p_id" value="0" />
		<input type="text" id="g_id" value="0" />
		<input type="text" id="s_id" value="0" />
		<input type="text" id="as_id" value="0" />
		<input type="text" id="a_id" value="0" />
		<input type="text" id="pd_id" value="0" />
		<input type="text" id="pr_id" value="0" />
		<input type="text" id="o_typ" value="0" />
		<input type="text" id="a_typ" value="0" />
		<input type="text" id="rf_id" value="0" />
	</div>
<!--
	<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">
-->
</div>
<div id="loader" style="margin-top:-10%;"></div>
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
		$("#from").datepicker({dateFormat: 'yy-mm-dd'});
		$("#to").datepicker({dateFormat: 'yy-mm-dd'});
	});
	function ot_booking_list(id)
	{
		//alert($("#user").text());
		$("#loader").show();
		$.post("pages/ot_booking_list_ajax.php",
		{
			type:"ot_booking_list",
			date1:$("#from").val(),
			date2:$("#to").val(),
			d_id:$("#d_id").val(),
			p_id:$("#p_id").val(),
			g_id:$("#g_id").val(),
			s_id:$("#s_id").val(),
			as_id:$("#as_id").val(),
			a_id:$("#a_id").val(),
			pd_id:$("#pd_id").val(),
			pr_id:$("#pr_id").val(),
			o_typ:$("#o_typ").val(),
			a_typ:$("#a_typ").val(),
			rf_id:$("#rf_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
			if(id!="")
			{
				$("#"+id).focus();
			}
		})
	}
	function export_page()
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		var d_id=$("#d_id").val();
		var p_id=$("#p_id").val();
		var g_id=0;
		var s_id=$("#s_id").val();
		var as_id=$("#as_id").val();
		var a_id=$("#a_id").val();
		var pd_id=$("#pd_id").val();
		var pr_id=$("#pr_id").val();
		var o_typ=$("#o_typ").val();
		var a_typ=$("#a_typ").val();
		var rf_id=$("#rf_id").val();
		var url="pages/ot_booking_list_xls.php?date1="+date1+"&date2="+date2+"&d_id="+d_id+"&p_id="+p_id+"&g_id="+g_id+"&s_id="+s_id+"&as_id="+as_id+"&a_id="+a_id+"&pd_id="+pd_id+"&pr_id="+pr_id+"&o_typ="+o_typ+"&a_typ="+a_typ+"&rf_id="+rf_id;
		//alert(url);
		window.location=url;
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
@media print {
  body * {
    visibility: hidden;
  }
  #load_all, #load_all * {
    visibility: visible;
  }
  #load_all {
	  overflow:visible;
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
