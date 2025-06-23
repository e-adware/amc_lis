<link rel="stylesheet" href="../css/loader.css" />
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Moving Items</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span10">
		<table class="table table-bordered table-condensed" >
			<tr>
				<th>Name</th>
				<td>
					<select onchange="load_item()" id="item" class="span2" autofocus>
						<option value="0">Select</option>
						<?php
						for($i=1; $i<=9; $i++)
						{
						?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php
						}
						for($i=65; $i<=90; $i++)
						{
						?>
						<option value="<?php echo chr($i); ?>"><?php echo chr($i); ?></option>
						<?php
						}
						?>
					</select>
				</td>
				<th>Moving Type</th>
				<td>
					<select id="move" class="span2" onchange="load_qnt();load_moving_item()">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `ph_moving_item_master` ORDER BY `name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['move_id']; ?>"><?php echo $r['name']; ?></option>
						<?php
						}
						?>
					</select>
				</td>
				<th>Quantity</th>
				<td><input type="text" class="span2" id="quantity" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" placeholder="Moving Item Quantity" disabled="disabled" /></td>
			</tr>
		</table>
	</div>
	<div id="res" class="span5" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
	
	</div>
	<div id="moving_list" class="span6" style="max-height:400px;overflow-y:scroll;">
	
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
	
	function load_item()
	{
		$("#loader").show();
		$.post("pages/moving_item_ajax.php",
		{
			item:$("#item").val(),
			type:"moving_item_list",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	
	function add_item(itm)
	{
		if($("#move").val()=="0")
		{
			$("#move").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/moving_item_ajax.php",
			{
				move_id:$("#move").val(),
				qnt:$("#quantity").val(),
				item:itm,
				type:"add_moving_item",
			},
			function(data,status)
			{
				$("#loader").hide();
				load_item();
				load_moving_item();
				$("#item").focus();
			})
		}
	}
	
	function load_qnt()
	{
		$.post("pages/moving_item_ajax.php",
		{
			move_id:$("#move").val(),
			type:"load_qnt",
		},
		function(data,status)
		{
			$("#quantity").val(data);
		})
	}
	function load_moving_item()
	{
		$("#loader").show();
		$.post("pages/moving_item_ajax.php",
		{
			move_id:$("#move").val(),
			type:"load_moving_item",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#moving_list").html(data);
		})
	}
	
	function remove_move_item(sl)
	{
		$("#loader").show();
		$.post("pages/moving_item_ajax.php",
		{
			sl:sl,
			type:"remove_move_item",
		},
		function(data,status)
		{
			$("#loader").hide();
			load_item();
			load_moving_item();
			//$("#moving_list").html(data);
		})
	}
</script>
