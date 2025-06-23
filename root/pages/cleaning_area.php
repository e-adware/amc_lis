<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Cleaning Area</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table">
		<tr>
			<th class="span2">Select Area</th>
			<td>
				<select class="span4" id="area_id" name="area_id" onChange="area_change()">
					<option value="0">Select</option>
					<?php
						$area_qry=mysqli_query($link," SELECT * FROM `cleaning_area_master` order by `area_name` ");
						while($ar=mysqli_fetch_array($area_qry))
						{
							echo "<option value='$ar[area_id]' >$ar[area_name]</option>";
						}
						?>
				</select>
				<button class="btn btn-success" onClick="create_area()">Create New Area</button>
			</td>
		</tr>
	</table>
	
	<div id="load_data">
		
	</div>
	<div id="area_added_data">
		
	</div>
</div>
<!--<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script src="../js/jquery.uniform.js"></script>
<link rel="stylesheet" href="../css/uniform.css" type="text/css" />-->
<script>
	$(document).ready(function()
	{
		load_added_data();
		//$("select").select2({ theme: "classic" });
		//enble_but($("#item_id").val());
	});
	function create_area()
	{
		$.post("pages/global_load.php",
		{
			type:"cleaning_area_master",
		},
		function(data,status)
		{
			$("#load_data").html(data);
			$("#area_name").focus();
			$("#area_id").val('0');
		})
	}
	function save_area()
	{
		if($("#area_name").val()!='')
		{
			$.post("pages/global_insert_data.php",
			{
				type:"cleaning_area_master",
				area_name:$("#area_name").val(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<h5>Saved</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					window.location.reload(true);
				},1000);
			})
		}else
		{
			bootbox.dialog({ message: "<h5>Input Area Name</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					$("#area_name").focus();
				},1000);
		}
	}
	function cancel_area()
	{
		$("#load_data").html('');
	}
	function area_change()
	{
		$.post("pages/global_load.php",
		{
			type:"cleaning_area_load",
			area_id:$("#area_id").val(),
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
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$.post("pages/global_insert_data.php",
			{
				type:"cleaning_area_master_update",
				area_id:$("#sel_area_id").val(),
				area_name:val,
			},
			function(data,status)
			{
				$("#sel_area").css({'border':'2px solid green'});
				load_added_data();
			})
		}
	}
	/*function add_more()
	{
		var len=$(".area_item_mat_row").length;
		if($("#item_id"+len).val()==0)
		{
			$("#item_id"+len).focus();
			exit();
		}
		if($("#item_mat_id"+len).val()==0)
		{
			$("#item_mat_id"+len).focus();
			exit();
		}
		if($("#frequency"+len).val()==0)
		{
			$("#frequency"+len).focus();
			exit();
		}
		$.post("pages/global_load.php",
		{
			type:"cleaning_area_load_rows",
			len:len,
		},
		function(data,status)
		{
			$('#cleaning_area_details').append(data);
		})
	}
	function remove_tr(len)
	{
		$("#row"+len).remove();
	}*/
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
		$.post("pages/global_insert_data.php",
		{
			type:"cleaning_area_data_save",
			area_id:$("#sel_area_id").val(),
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
		$.post("pages/global_load.php",
		{
			type:"cleaning_area_added_data_load",
			area_id:$("#area_id").val(),
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
						$.post("pages/global_delete.php",
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
