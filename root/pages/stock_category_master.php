<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr style="display:none;">
				<td>Category ID</td>
				<td>
					
					<input type="text" value="0" id="category_id" readonly="readonly" class="span3" />
				</td>
			</tr>
			
			<tr>
				<td>Category Name</td>
				<td><input type="text" id="category_name" class="span3" placeholder="Type Name" autofocus /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" id="" class="btn btn-danger" onclick="reset()" value="Reset" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span6">
		<b>Search</b> <input type="text" id="srch" placeholder="Search" onkeyup="search(this.value)">
		<div id="load_data" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		load_category_list();
		$("#category_name").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#sav").focus();
			}
		});
	});
	function reset()
	{
		$("#category_id").val(0);
		$("#category_name").val("").focus();
		
		$("#sav").val("Save");
	}
	function load_category_list()
	{
		$.post("pages/stock_category_master_data.php",
		{
			type:"load_category_list",
			user:$("#user").text().trim(),
			lavel_id:$("#lavel_id").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			reset();
		})
	}
	function load_category_detail(category_id)
	{
		$.post("pages/stock_category_master_data.php",
		{
			type:"load_category_detail",
			category_id:category_id,
		},
		function(data,status)
		{
			var str=data.split("@#@");		
			$("#category_id").val(str[0]);
			$("#category_name").val(str[1]).focus();
			
			$("#sav").val("Update");
		})
	}
	function save()
	{
		$.post("pages/stock_category_master_data.php",
		{
			type:"save_category",
			category_id:$("#category_id").val(),
			category_name:$("#category_name").val(),
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
	function delete_category(category_id)
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
						$.post("pages/stock_category_master_data.php",
						{
							type:"delete_category",
							category_id:category_id,
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
