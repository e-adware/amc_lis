<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Moving Type Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<th>Type Name</th>
				<td>
					<input type="text" id="id" style="display:none;" />
					<input type="text" id="name" class="span3" placeholder="Type Name" autofocus />
				</td>
			</tr>
			<tr>
				<th>Quantity</th>
				<td>
					<input type="text" id="qnt" class="span3" placeholder="Quantity" />
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
		<b>Search</b> <input type="text" id="srch" onkeyup="load_types()" class="span4" placeholder="Search..." />
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
				<a data-dismiss="modal" onclick="delete_ward()" class="btn btn-primary" href="#">Confirm</a>
				<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
			  </div>
            </div>
          <!--modal end-->
</div>
<script>
	$(document).ready(function()
	{
		load_types();
	});
	
	function load_types()
	{
		$.post("pages/moving_types_ajax.php",
		{
			srch:$("#srch").val().trim(),
			type:"load_types",
		},
		function(data,status)
		{
			$("#res").html(data);
			//$("#name").focus();
		})
	}
	function edit_type(id)
	{
		$.post("pages/moving_types_ajax.php",
		{
			id:id,
			type:"edit_type",
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("#@#");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#qnt").val(vl[2]);
			$("#sav").val('Update');
			$("#name").focus();
		})
	}
	function save()
	{
		if($("#name").val().trim()=="")
		{
			$("#name").focus();
		}
		else if($("#qnt").val().trim()=="" || parseInt($("#qnt").val().trim())==0)
		{
			$("#qnt").focus();
		}
		else
		{
			$("#sav").attr('disabled',true);
			$.post("pages/moving_types_ajax.php",
			{
				id:$("#id").val(),
				name:$("#name").val().trim(),
				qnt:$("#qnt").val().trim(),
				type:"save_type",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					$("#sav").attr('disabled',false);
					clrr();
				}, 1000);
			})
		}
	}
	
	function del_type(id)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<b>Are you sure want to delete?<br/>This will also remove from moving item list.</b>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-success",
					callback: function()
					{
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function()
					{
						$.post("pages/moving_types_ajax.php",
						{
							id:id,
							type:"del_type",
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
			}
		});
	}
	
	function clrr()
	{
		$("#id").val('');
		$("#srch").val('');
		$("#qnt").val('');
		$("#name").val('').focus();
		$("#sav").val('Save');
		load_types();
	}
</script>
