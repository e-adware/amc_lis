<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Clinical Procedure Headed</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left: 0;">
		<table class="table table-condensed">
			<tr>
				<th>
					<input type="text" id="id" readonly="readonly" style="display:none;" />
					Procedure Header Name
				</th>
			</tr>
			<tr>
				<td><input type="text" id="name" style="width:90%;" placeholder="Procedure Header Name" /></td>
			</tr>
			<tr>
				<td style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" class="btn btn-danger" onclick="clrr()" value="Reset" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<input type="text" id="srch" style="width:90%;" onkeyup="load_header()" Placeholder="Search..." />
		<div id="res" style="max-height:500px;overflow-y:scroll;">
		
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
		load_header();
	});
	
	function load_header()
	{
		$.post("pages/procedure_header_ajax.php",
		{
			srch:$("#srch").val(),
			type:"load_header",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function det(id)
	{
		$.post("pages/procedure_header_ajax.php",
		{
			id:id,
			type:"load_header_det",
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("#ea#");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#sav").val('Update');
			$("#name").focus();
			//$("#res").html(data);
		})
	}
	function confirmm(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	function del()
	{
		$.post("pages/procedure_header_ajax.php",
		{
			id:$("#idl").val(),
			type:"delete_header_procedure",
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
	function save()
	{
		if($("#name").val().trim()=="")
		{
			$("#name").focus();
		}
		else
		{
			$("#sav").attr("disabled",true);
			$.post("pages/procedure_header_ajax.php",
			{
				id:$("#id").val(),
				name:$("#name").val().trim(),
				usr:$("#user").text().trim(),
				type:"save_clinical_procedures",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					$("#sav").attr("disabled",false);
					clrr();
				}, 1000);
			})
		}
	}
	function clrr()
	{
		$("#id").val('');
		$("#name").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#name").focus();
		load_header();
	}
</script>
