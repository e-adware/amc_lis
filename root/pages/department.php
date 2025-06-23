<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Department Id</td>
				<td><input type="text" id="id" readonly="readonly" class="span3" /></td>
			</tr>
			<tr>
				<td>Department Name</td>
				<td><input type="text" id="name" class="span3" placeholder="Department Name" /></td>
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
		<b>Search</b> <input type="text" id="srch" onkeyup="srch()" class="span4" placeholder="Search..." />
		<div id="res" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
		<!--modal-->
			<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
            <div id="myAlert" class="modal hide">
              <div class="modal-body">
				  <input type="text" id="idl" style="display:none;" />
                <p>Are You Sure Want To Delete...?</p>
              </div>
              <div class="modal-footer">
				<a data-dismiss="modal" onclick="delete_dept()" class="btn btn-primary" href="#">Confirm</a>
				<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
			  </div>
            </div>
          <!--modal end-->
</div>
<script>
	function load_id()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_dept_id",
		},
		function(data,status)
		{
			$("#id").val(data);
			$("#name").focus();
		})
	}
	function load_dept()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_dept",
			user:$("#user").text().trim(),
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
			$.post("pages/global_insert_data_g.php",
			{
				id:$("#id").val(),
				name:$("#name").val(),
				type:"save_dept",
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
	function edt(i)
	{
		$.post("pages/global_load_g.php",
		{
			id:i,
			type:"edit_dept",
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
	function del(i)
	{
		$("#dl").click();
		$("#idl").val(i);
	}
	function delete_dept()
	{
		$.post("pages/global_delete_g.php",
		{
			id:$("#idl").val(),
			type:"delete_dept",
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
	function srch()
	{
		$.post("pages/global_load_g.php",
		{
			srch:$("#srch").val(),
			type:"search_dept",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function clrr()
	{
		$("#name").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		load_id();
		load_dept();
	}
	load_id();
	load_dept();
</script>
