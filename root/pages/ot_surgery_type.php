<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Surgery Type</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<form id="form1" method="post">
			<table class="table table-bordered table-condensed" >
				<tr>
					<td>Id</td>
					<td>
						<input type="text" id="id" value="" readonly="readonly" class="intext span4"/>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<input type="text"  id="type" value="" autocomplete="off" class="intext span4" autofocus />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center">
						<input type="button" name="button2" id="button2" value="Reset" onclick="clrr()" class="btn btn-danger" style="width:100px"/> 
						<input type="button" name="intext7" id="button" value="Submit" onclick="save()" class="btn btn-default btn-info" style="width:100px"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="span5">
		<table class="table table-condensed table-bordered">
			<tr>
				<td>Search</td>
				<td> <input type="text" id="srch"  autocomplete="off" class="form-control span4" onkeyup="load_type()" /></td>
			</tr>
		</table>
		<div id="res" style="max-height:400px;overflow-y:scroll;" >
			
		</div>
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
			<a data-dismiss="modal" onclick="del()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end-->
<style>
	.nm:hover{color:#000099;}
</style>
<script>
	$(document).ready(function()
	{
		load_id();
		load_type();
	});
	function load_id()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_ot_type_id",
		},
		function(data,status)
		{
			$("#id").val(data);
		})
	}
	function save()
	{
		if($("#type").val()=="")
		{
			$("#type").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				id:$("#id").val(),
				tname:$("#type").val(),
				type:"save_ot_resource_type",
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
	function load_type()
	{
		$.post("pages/global_load_g.php",
		{
			srch:$("#srch").val(),
			type:"load_ot_type",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function confirmm(id)
	{
		$("#dl").click();
		$("#idl").val(id)
	}
	function del()
	{
		$.post("pages/global_delete_g.php",
		{
			id:$("#idl").val(),
			type:"delete_ot_type",
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
	function det(id)
	{
		$.post("pages/global_load_g.php",
		{
			id:id,
			type:"load_ot_type_details",
		},
		function(data,status)
		{
			var vl=data.split("#@#");
			$("#id").val(vl[0]);
			$("#type").val(vl[1]);
			$("#button").val("Update");
			$("#type").focus();
		})
	}
	function clrr()
	{
		$("#type").val('');
		$("#srch").val('');
		$("#button").val('Submit');
		$("#type").focus();
		load_id();
		load_type();
	}
</script>
