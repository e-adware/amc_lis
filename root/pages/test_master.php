<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Test Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Test Id</td>
				<td><input type="text" id="id" readonly="readonly" class="span2" /></td>
			</tr>
			<tr>
				<td>Test Name</td>
				<td><input type="text" id="name" class="span4" placeholder="Test Name" /></td>
			</tr>
			<tr>
				<td>Test Rate</td>
				<td><input type="text" id="rate" class="span2" placeholder="Test Rate" /></td>
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
		<b>Search</b> <input type="text" id="srch" onkeyup="load_test_master()" class="span4" placeholder="Search..." />
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
				<a data-dismiss="modal" onclick="delete_test()" class="btn btn-primary" href="#">Confirm</a>
				<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
			  </div>
            </div>
          <!--modal end-->
	<script>
		load_id();
		load_test_master();
	</script>
	<style>
		.edt:hover
		{
			color: #0000FF;
		}
	</style>
</div>
<script>
	function load_id()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_test_master_id",
		},
		function(data,status)
		{
			$("#id").val(data);
			$("#name").focus();
		})
	}
	function load_test_master()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_test_master",
			srch:$("#srch").val(),
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
		else if($("#rate").val()=="")
		{
			$("#rate").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				id:$("#id").val(),
				name:$("#name").val(),
				rate:$("#rate").val(),
				type:"save_test_master",
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
			type:"edit_test_master",
		},
		function(data,status)
		{
			var vl=data.split("@govin@");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#rate").val(vl[2]);
			$("#sav").val('Update');
			$("#name").focus();
		})
	}
	function del(i)
	{
		$("#dl").click();
		$("#idl").val(i);
	}
	function delete_test()
	{
		$.post("pages/global_delete_g.php",
		{
			id:$("#idl").val(),
			type:"delete_test_master",
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
		$("#name").val('');
		$("#rate").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		load_id();
		load_test_master();
	}
</script>
