<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span4" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr style="display:none;">
				<td>Generic ID</td>
				<td>
					
					<input type="text" value="0" id="generic_id" readonly="readonly" class="span3" />
				</td>
			</tr>
			
			<tr>
				<td>Generic Name</td>
				<td><input type="text" id="generic_name" class="span3" placeholder="Type Name" autofocus /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" id="" class="btn btn-danger" onclick="reset()" value="Reset" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span7">
		<b>Search</b> <input type="text" id="srch" placeholder="Search" onkeyup="search(this.value)">
		<div id="load_data" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		load_category_list();
		$("#generic_name").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#sav").focus();
			}
		});
	});
	function reset()
	{
		$("#generic_id").val(0);
		$("#generic_name").val("").focus();
		
		$("#sav").val("Save");
	}
	function load_category_list()
	{
		$.post("pages/generic_master_data.php",
		{
			type:"load_generic_list",
			user:$("#user").text().trim(),
			lavel_id:$("#lavel_id").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			reset();
		})
	}
	function load_category_detail(generic_id)
	{
		$.post("pages/generic_master_data.php",
		{
			type:"load_item_generic_detail",
			generic_id:generic_id,
		},
		function(data,status)
		{
			var str=data.split("@#@");		
			$("#generic_id").val(str[0]);
			$("#generic_name").val(str[1]).focus();
			
			$("#sav").val("Update");
		})
	}
	function save()
	{
		$.post("pages/generic_master_data.php",
		{
			type:"save_generic_name",
			generic_id:$("#generic_id").val(),
			generic_name:$("#generic_name").val(),
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function(){
				bootbox.hideAll();
				load_category_list();
			},500);
		})
	}
	function delete_category(generic_id)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete ?</h5>",
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
						$.post("pages/generic_master_data.php",
						{
							type:"delete_generic_name",
							generic_id:generic_id,
						},
						function(data,status)
						{
							bootbox.dialog({ message: data});
							setTimeout(function(){
								bootbox.hideAll();
								load_category_list();
							},500);
						})
					}
				}
			}
		});
	}
	function search(inputVal)
	{
		var table = $('#tblData');
		table.find('tr').each(function(index, row)
		{
			var allCells = $(row).find('td');
			if(allCells.length > 0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
						var regExp = new RegExp(inputVal, 'i');
						if(regExp.test($(td).text()))
						{
								found = true;
								return false;
						}
				});
				if(found == true)
				{
						$("#no_record").text("");
						$(row).show();
				}else{
						$(row).hide();
						var n = $('tr:visible').length;
						if(n==1)
						{
								$("#no_record").text("No matching records found");
						}else
						{
								$("#no_record").text("");
						}
				}
				//if(found == true)$(row).show();else $(row).hide();
			}
		});
	}
</script>
