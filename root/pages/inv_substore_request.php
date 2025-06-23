<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<table class="table table-condensed">
			<tr>
				<td>
					<b>Select Department</b>
					<select id="dept" autofocus>
						<option value="0">Select All</option>
						<?php
						$qq=mysqli_query($link,"SELECT * FROM `inv_sub_store` order by `substore_name`");
						while($r=mysqli_fetch_array($qq))
						{
						?>
						<option value="<?php echo $r['substore_id']; ?>"><?php echo $r['substore_name']; ?></option>
						<?php
						}
						?>
					</select>
				</td>
				<td>
					<b>From</b>
					<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
					<b>To</b>
					<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="button" class="btn btn-info" onclick="srch()" style="width:130px" >Search <i class="icon-search"></i></button>
				</td>
			</tr>
		</table>
	
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
	
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal hide">
		  <div class="modal-body">
			  <input type="text" id="idl" style="display:none;" />
			<p>Are You Sure Want To Delete...?</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="del()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clearr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end-->
	  
</div>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	
	function srch()
	{
		$.post("pages/inv_substore_request_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			dept:$("#dept").val(),
			type:1,
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
	function view_order(ord)
	{
		url="pages/inv_substore_request_print.php?rXeStzT="+btoa(ord);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function edit_order(orderno)
	{
		
		bootbox.dialog({ message: "<b>Redirecting to Order Update</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		setTimeout(function(){
			window.location="processing.php?param=153&orderno="+orderno;
		 }, 1000);
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
