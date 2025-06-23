<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table">
		<tr>
			<th class="span2">Select Room</th>
			<td>
				<select class="span4" id="ot_area_id" name="ot_area_id" onChange="area_change()">
					<option value="0">Select</option>
					<?php
						$area_qry=mysqli_query($link," SELECT * FROM `ot_area_master` order by `ot_area_name` ");
						while($ar=mysqli_fetch_array($area_qry))
						{
							echo "<option value='$ar[ot_area_id]' >$ar[ot_area_name]</option>";
						}
						?>
				</select>
				<button class="btn btn-success" onClick="create_area()">Create New Room</button>
			</td>
		</tr>
	</table>
	
	<div id="load_data">
		
	</div>
	<div id="area_added_data">
		
	</div>
</div>
<script>
	$(document).ready(function()
	{
		load_added_data();
	});
	function create_area()
	{
		$.post("pages/ot_area_data.php",
		{
			type:"ot_area_master",
		},
		function(data,status)
		{
			$("#load_data").html(data);
			$("#ot_area_name").focus();
			$("#ot_area_id").val('0');
		})
	}
	function ot_area_name_up(val,e)
	{
		$("#ot_area_name").css("border","");
		if(e.keyCode==13)
		{
			if(val=="" || val==0)
			{
				$("#ot_area_name").css("border","1px solid #f00");
			}else
			{
				$("#ot_type").focus();
			}
		}
	}
	function ot_type_up(val,e)
	{
		$("#ot_type").css("border","");
		if(e.keyCode==13)
		{
			if(val=="" || val==0)
			{
				$("#ot_type").css("border","1px solid #f00");
			}else
			{
				$("#ot_rate").focus();
			}
		}
	}
	function ot_rate_up(val,e)
	{
		$("#ot_rate").css("border","");
		if(e.keyCode==13)
		{
			if(val=="" || val==0)
			{
				$("#ot_rate").css("border","1px solid #f00");
			}else
			{
				$("#ot_area_save").focus();
			}
		}
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#ot_rate").val(val);
		}
	}
	function ot_save_area()
	{
		var error=0;
		var ot_area_name=$("#ot_area_name").val();
		if(ot_area_name=='')
		{
			$("#ot_area_name").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
		var ot_type=$("#ot_type").val();
		if(ot_type=='0')
		{
			$("#ot_type").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
		var ot_rate=$("#ot_rate").val();
		if(ot_rate=='')
		{
			$("#ot_rate").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
		if(error==0)
		{
			$.post("pages/ot_area_data.php",
			{
				type:"save_ot_area_master",
				ot_area_name:ot_area_name,
				ot_type:ot_type,
				ot_rate:ot_rate,
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<h5>Saved</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					window.location.reload(true);
				},1000);
			})
		}
	}
	function cancel_area()
	{
		$("#load_data").html('');
	}
	function area_change()
	{
		$.post("pages/ot_area_data.php",
		{
			type:"ot_area_load",
			ot_area_id:$("#ot_area_id").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			load_added_data();
			//$("select").select2({ theme: "classic" });
		})
	}
	function update_area(val,e)
	{
		$.post("pages/ot_area_data.php",
		{
			type:"ot_area_master_update",
			sel_ot_area_id:$("#sel_ot_area_id").val(),
			sel_ot_area_name:$("#sel_ot_area_name").val(),
			sel_ot_type:$("#sel_ot_type").val(),
			sel_ot_rate:$("#sel_ot_rate").val(),
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<h5>Saved</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
				load_added_data();
			},1000);
		})
	}
	function save_area_info()
	{
		if($("#item_id").val()==0)
		{
			$("#item_id").focus();
			exit();
		}
		if($("#item_mat_id").val()==0)
		{
			$("#item_mat_id").focus();
			exit();
		}
		if($("#frequency").val()==0)
		{
			$("#frequency").focus();
			exit();
		}
		$.post("pages/ot_area_data.php",
		{
			type:"ot_area_data_save",
			sel_ot_area_id:$("#sel_ot_area_id").val(),
			item_id:$("#item_id").val(),
			item_mat_id:$("#item_mat_id").val(),
			frequency:$("#frequency").val(),
		},
		function(data,status)
		{
			if(data==11)
			{
				bootbox.dialog({ message: "<h5>Saved</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					$("#item_id").val('');
					$("#item_mat_id").val('');
					$("#frequency").val('');
					load_added_data();
				},1000);
			}else if(data==22)
			{
				bootbox.dialog({ message: "<h5>Already added</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
				},1000);
			}
		})
	}
	function load_added_data()
	{
		$.post("pages/ot_area_data.php",
		{
			type:"ot_area_added_data_load",
			ot_area_id:$("#ot_area_id").val(),
		},
		function(data,status)
		{
			$("#area_added_data").html(data);
		})
	}
	function remove_selected_area(slno)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to remove</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/ot_area_data.php",
						{
							type:"delete_selected_area_data",
							slno:slno,
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<h5>Deleted</h5>"});
							setTimeout(function(){
								bootbox.hideAll();
								load_added_data();
							},1000);
						})
					}
				}
			}
		});
	}
	function load_selected_area_data(a,b,c)
	{
		//alert(a+' '+b+' '+c);
		$("#item_id").val(a);
		$("#item_mat_id").val(b);
		$("#frequency").val(c);
	}
</script>
