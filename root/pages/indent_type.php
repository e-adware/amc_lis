<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Indent Type</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Type Id</td>
				<td>
					<input type="text" id="sl" style="display:none;" />
					<input type="text" id="id" readonly="readonly" class="span3" />
				</td>
			</tr>
			<tr>
				<td>Type Name</td>
				<td><input type="text" id="name" class="span3" placeholder="Type Name" autofocus /></td>
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
		<b>Search</b> <input type="text" id="srch" onkeyup="load_type()" class="span4" placeholder="Search..." />
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
			<a data-dismiss="modal" onclick="dell()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end-->
</div>
<script>
	$(document).ready(function()
	{
		load_id();
		load_type();
		
		$("#name").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#sav").focus();
			}
		});
	});
	
	function load_id()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_ind_type_id",
		},
		function(data,status)
		{
			$("#id").val(data);
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
				sl:$("#sl").val(),
				id:$("#id").val(),
				name:$("#name").val(),
				type:"save_ind_type",
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
	function clrr()
	{
		load_id();
		$("#sl").val('');
		$("#name").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#name").focus();
		load_type();
	}
	function load_type()
	{
		$.post("pages/global_load_g.php",
		{
			srch:$("#srch").val(),
			type:"load_ind_type",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function edit(sl)
	{
		$.post("pages/global_load_g.php",
		{
			sl:sl,
			type:"load_ind_type_details",
		},
		function(data,status)
		{
			var vl=data.split("#gov#");
			$("#sl").val(vl[0]);
			$("#id").val(vl[1]);
			$("#name").val(vl[2]);
			$("#sav").val("Update");
			$("#name").focus();
		})
	}
	function del(sl)
	{
		$("#dl").click();
		$("#idl").val(sl);
	}
	function dell()
	{
		$.post("pages/global_delete_g.php",
		{
			sl:$("#idl").val(),
			type:"delete_ind_type",
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
</script>
<style>
	.nm:hover
	{
		color:#0000ff;
		cursor:pointer;
	}
</style>
