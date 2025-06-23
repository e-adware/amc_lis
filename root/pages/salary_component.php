<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Salary Component</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Component Name <input type="text" id="id" style="display:none;" /></td>
				<td><input type="text" id="name" class="span3" placeholder="Component Name" autofocus /></td>
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
		<b>Search</b> <input type="text" id="srch" onkeyup="load_component()" class="span4" placeholder="Search..." />
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
				<a data-dismiss="modal" onclick="delete_level()" class="btn btn-primary" href="#">Confirm</a>
				<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
			  </div>
            </div>
          <!--modal end-->
</div>
<script>
	$(document).ready(function(){load_component();});
	function load_component()
	{
		$.post("pages/salary_component_ajax.php",
		{
			srch:$("#srch").val(),
			type:"load_component",
		},
		function(data,status)
		{
			$("#res").html(data);
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
			$.post("pages/salary_component_ajax.php",
			{
				id:$("#id").val(),
				name:$("#name").val().trim(),
				type:"save_sal_component",
			},
			function(data,status)
			{
				$("#sav").attr("disabled",false);
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clrr();
				}, 1000);
			})
		}
	}
	function del(sl)
	{
		$("#dl").click();
		$("#idl").val(sl);
	}
	function clrr()
	{
		$("#id").val('');
		$("#name").val('');
		$("#srch").val('');
		$("#name").focus();
		$("#sav").val('Save');
		load_component();
	}
</script>
