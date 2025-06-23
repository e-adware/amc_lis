<link rel="stylesheet" href="../css/loader.css" />
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Item HSN Code</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span10">
		<table class="table table-bordered table-condensed" >
			<tr>
				<td>Name</td>
				<td>
					<select onchange="load_item()" id="item" autofocus>
						<option value="0">Select</option>
						<?php
						for($i=65; $i<=90; $i++)
						{
						?>
						<option value="<?php echo chr($i); ?>"><?php echo chr($i); ?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div id="res" class="span10">
	
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
<div id="loader" style="position:fixed;top:40%;left:50%;display:none;"></div>
<script>
	$(document).ready(function()
	{
		
	});
	
	function save_hsn(val,id,e)
	{
		if(e.keyCode==13)
		{
			$("#loader").show();
			$.post("pages/nursing_load_g.php",
			{
				id:id,
				val:val,
				type:"item_master_hsn_save",
			},
			function(data,status)
			{
				$("#loader").hide();
				if(data==1)
				$("#H"+id).css("border","2px solid #008800");
				else if(data==0)
				$("#H"+id).css("border","2px solid #aa0000");
			})
		}
	}
	
	function save_gst(val,id,e)
	{
		if(e.keyCode==13)
		{
			$("#loader").show();
			$.post("pages/nursing_load_g.php",
			{
				id:id,
				val:val,
				type:"item_master_gst_save",
			},
			function(data,status)
			{
				$("#loader").hide();
				if(data==1)
				$("#G"+id).css("border","2px solid #008800");
				else if(data==0)
				$("#G"+id).css("border","2px solid #aa0000");
			})
		}
	}
	function load_item()
	{
		$("#loader").show();
		$.post("pages/nursing_load_g.php",
		{
			item:$("#item").val(),
			type:"item_master_hsn",
		},
		function(data,status)
		{
			$("#loader").fadeOut();
			$("#res").html(data);
		})
	}
</script>
