<script>
	function load_cat()
	{
		$.post("pages/category_master_ajax.php",
		{
			srch:$("#srch").val(),
			type:"load_cat",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function save()
	{
		if($("#name").val()=="")
		{
			$("#name").focus();
		}
		else
		{
			$.post("pages/category_master_ajax.php",
			{
				id:$("#id").val(),
				name:$("#name").val(),
				type:"save_cat",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clrr();
				}, 1000);
			})
		}
	}
	function cat_det(id)
	{
		$.post("pages/category_master_ajax.php",
		{
			id:id,
			type:"edit_cat",
		},
		function(data,status)
		{
			var vl=data.split("@govin@");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#sav").val('Update');
			$("#name").focus();
		})
	}
	function del(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	function delete_level()
	{
		$.post("pages/category_master_ajax.php",
		{
			id:$("#idl").val(),
			type:"delete_cat",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				clrr();
			}, 1000);
		})
	}
	function clrr()
	{
		$("#id").val('');
		$("#name").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#name").focus();
		load_cat();
	}
</script>
<style>
	.table tr:hover
	{background:none;}
	.nm:hover
	{color:#0000FF;}
</style>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Category Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Category Name</td>
				<td>
					<input type="text" id="id" style="display:none;" />
					<input type="text" id="name" class="span3" placeholder="Category Name" autofocus />
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" id="" class="btn btn-danger" onclick="clrr()" value="Reset" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span6">
		<b>Search</b> <input type="text" id="srch" onkeyup="load_cat()" class="span4" placeholder="Search..." />
		<div id="res" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
		<!--modal-->
			<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
            <div id="myAlert" class="modal fade">
              <div class="modal-body">
				  <input type="text" id="idl" style="display:none;" />
                <p>Are You Sure Want To Delete...?</p>
              </div>
              <div class="modal-footer">
				<a data-dismiss="modal" onclick="delete_level()" class="btn btn-primary" href="#">Confirm</a>
				<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
			  </div>
            </div>
          <!--modal end-->
	<script>
		load_cat();
	</script>
</div>
