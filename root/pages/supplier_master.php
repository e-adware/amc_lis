<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Supplier Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Supplier Id</td>
				<td><input type="text" id="id" readonly="readonly" class="span3" /></td>
			</tr>
			<tr>
				<td>Supplier Name</td>
				<td><input type="text" id="name" class="span3" placeholder="Supplier Name" autofocus /></td>
			</tr>
			<tr>
				<td>Address</td>
				<td><textarea id="addr" placeholder="Address"></textarea></td>
			</tr>
			<tr>
				<td>Contact No</td>
				<td><input type="text" id="contact" class="span3" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" maxlength="10" placeholder="contact" /></td>
			</tr>
			<tr>
				<td>Email</td>
				<td><input type="text" id="email" class="span3" placeholder="Email Id" /></td>
			</tr>
			<tr>
				<td>GST No</td>
				<td><input type="text" id="txtgst" class="span3" placeholder="GST" /></td>
			</tr>
			<tr>
				<td>DL. No</td>
				<td><input type="text" id="txtdlno" class="span3" placeholder="DL No" /></td>
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
		<b>Search</b> <input type="text" id="srch" onkeyup="load_supp(this.value)" class="span4" placeholder="Search..." />
		<div id="res" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
	<!--modal-->
	<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
	<div id="myAlert" class="modal hide">
		<div class="modal-body">
			<input type="text" id="del_id" style="display:none;" />
			<p>Are You Sure Want To Delete...?</p>
		</div>
		<div class="modal-footer">
			<a data-dismiss="modal" onclick="delete_supp()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
		</div>
	</div>
	<!--modal end-->
</div>
<style>
	textarea
	{
		min-width: 260px;
		min-height: 60px;
		resize:none;
	}
</style>
<script>
	$(document).ready(function()
	{
		load_id();
		load_supp();
		
		$("#name").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				$(this).css("border","1px solid #f00");
				if($(this).val()=="")
				{
					$(this).css("border","1px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#addr").focus();
				}
			}
		});
		$("#addr").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#contact").focus();
			}
		});
		$("#contact").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#email").focus();
			}
		});
		$("#email").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#sav").focus();
			}
		});
	});
	
	function load_id()
	{
		$.post("pages/global_load_g.php"	,
		{
			type:"load_supp_id",
		},
		function(data,status)
		{
			$("#id").val(data);
		})
	}
	function load_supp(val)
	{
		$.post("pages/global_load_g.php"	,
		{
			val:val,
			type:"load_supp_list",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function val_load_new(id)
	{
		$.post("pages/global_load_g.php"	,
		{
			id:id,
			type:"load_supp_det",
		},
		function(data,status)
		{
			var vl=data.split("#gov#");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#addr").val(vl[2]);
			$("#contact").val(vl[3]);
			$("#email").val(vl[4]);
			$("#txtgst").val(vl[5]);
			$("#txtdlno").val(vl[6]);
			$("#sav").val("Update");
			$("#name").css("border","");
			$("#name").focus();
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
			$.post("pages/global_insert_data_g.php"	,
			{
				id:$("#id").val(),
				sname:$("#name").val().trim(),
				addr:$("#addr").val().trim(),
				contact:$("#contact").val(),
				email:$("#email").val(),
				gstno:$("#txtgst").val(),
				dlno:$("#txtdlno").val(),
				type:"insert_supplier",
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
		$("#id").val('');
		$("#name").val('');
		$("#addr").val('');
		$("#contact").val('');
		$("#email").val('');
		$("#srch").val('');
		$("#txtgst").val('');
		$("#txtdlno").val('');
		$("#sav").val("Save");
		$("#name").css("border","");
		$("#name").focus();
		load_id();
		load_supp();
	}
</script>
