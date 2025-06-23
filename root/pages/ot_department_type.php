<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">OT Department</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<table class="table table-bordered table-condensed" >
			<tr>
				<th>Department Name</th>
				<td>
					<input type="text" id="id" value="" style="display:none;" class="" />
					<input type="text"  id="dept" value="" autocomplete="off" class="" placeholder="Department Name" autofocus />
				</td>
			</tr>
			<tr style="display:none;">
				<th>Select Type</th>
				<td>
					<select id="typ">
						<option value="0">Select</option>
						<option value="1">Minor</option>
						<option value="2">Major</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" name="intext7" id="button" value="Submit" onclick="save()" class="btn btn-default btn-info" style="width:100px"/>
					<input type="button" name="button2" id="button2" value="Reset" onclick="clrr()" class="btn btn-danger" style="width:100px"/> 
				</td>
			</tr>
		</table>
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
		load_type();
	});
	function save()
	{
		if($("#dept").val()=="")
		{
			$("#dept").focus();
		}
		else
		{
			$.post("pages/ot_department_type_ajax.php",
			{
				id:$("#id").val(),
				dept:$("#dept").val(),
				type:3,
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
		$.post("pages/ot_department_type_ajax.php",
		{
			srch:$("#srch").val(),
			type:1,
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
		$.post("pages/ot_department_type_ajax.php",
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
		$.post("pages/ot_department_type_ajax.php",
		{
			id:id,
			type:2,
		},
		function(data,status)
		{
			var vl=data.split("#@#");
			$("#id").val(vl[0]);
			$("#dept").val(vl[1]);
			$("#button").val("Update");
			$("#dept").focus();
		})
	}
	function clrr()
	{
		$("#id").val('');
		$("#dept").val('');
		$("#srch").val('');
		$("#button").val('Submit');
		$("#dept").focus();
		load_type();
	}
</script>
