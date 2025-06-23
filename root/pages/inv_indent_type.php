<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Indent Category</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Category Id</td>
				<td>
					
					<input type="text" id="id" readonly="readonly" class="span3" />
				</td>
			</tr>
			
			<tr>
				<td>Name</td>
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
		$.post("pages/load_id.php",
		{
			type:"invindntcatgry",
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
			$.post("pages/inv_insert_data.php",
			{
				
				id:$("#id").val(),
				name:$("#name").val(),
				type:"invindntcatgry",
			},
			function(data,status)
			{
				alert(data);
				load_type();
				clrr();
			})
		}
	}
	
	function clrr()
	{
		load_id();
		
		$("#name").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#name").focus();
		load_type();
	}
	
	function load_type()
	{
		$.post("pages/inv_load_data_ajax.php",
		{
			srch:$("#srch").val(),
			type:"invindntcatgry",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function val_load_new(id)///for retrieve 
	{
		
		$.post("pages/inv_load_display.php",
		{
			type:"invindntcatgry",
			id:id,
		},
		function(data,status)
		{
			
			var val=data.split("@");
			$("#id").val(val[0]);
			$("#name").val(val[1]);	
			$("#sav").val("Update");
			$("#name").focus();
		})
	}
	
	

		function delete_data(itmid)
		{

			$.post("pages/inv_load_delete.php",
			{
			type:"invindntcatgry",
			itmid:itmid,

			},
			function(data,status)
			{
			alert("Deleted");
			load_type();
			clrr();
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
