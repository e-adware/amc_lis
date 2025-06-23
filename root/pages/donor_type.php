<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header">Donor Type Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<form id="form1" method="post">
			<table class="table table-bordered table-condensed" >
				<tr>
					<td>Id</td>
					<td>
						<input type="text" id="did" value="" readonly="readonly" class="intext span4"/>
					</td>
				</tr>
				<tr>
					<td>Name</td>
					<td>
						<input type="text"  id="dname" value="" autocomplete="off" class="intext span4" autofocus />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center">
						<input type="button" name="button2" id="button2" value="Reset" onclick="clrr()" class="btn btn-danger" style="width:100px"/> 
						<input type="button" name="intext7" id="button" value= "Submit" onclick="save()" class="btn btn-default btn-info" style="width:100px"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="span5">
		<table class="table table-condensed table-bordered">
			<tr>
				<td>Search</td>
				<td> <input type="text" id="srch"  autocomplete="off" class="form-control span4" onkeyup="display_type()" /></td>
			</tr>
		</table>
		<div id="load_type" style="max-height:400px;overflow-y:scroll;" >
			
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
		$("#dname").keyup(function(e)
		{
			$(this).css("border","");
			//$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#button").focus();
				}
			}
		});
	});
	
	function load_id()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_donor_type_id",
		},
		function(data,status)
		{
			$("#did").val(data);
			display_type();
		})
	}
	function display_type()
	{
		$.post("pages/global_load_g.php",
		{
			srch:$("#srch").val(),
			type:"display_donor_type",
		},
		function(data,status)
		{
			$("#load_type").html(data);
		})
	}
	function det(id)
	{
		$.post("pages/global_load_g.php",
		{
			id:id,
			type:"load_details_donor_type",
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("#ea#");
			$("#did").val(vl[0]);
			$("#dname").val(vl[1]);
			$("#dname").focus();
			$("#button").val('Update');
		})
	}
	function save()
	{
		if($("#dname").val()=="")
		{
			$("#dname").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				did:$("#did").val(),
				dname:$("#dname").val(),
				usr:$("#user").text().trim(),
				type:"save_donor_type",
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
	function confirmm(id)
	{
		//alert(id);
		$("#dl").click();
		$("#idl").val(id);
	}
	function del()
	{
		$.post("pages/global_delete_g.php",
		{
			id:$("#idl").val(),
			type:"delete_donor_type",
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
		$("#dname").val('');
		$("#srch").val('');
		$("#button").val('Save');
		$("#dname").focus();
		load_id();
	}
</script>
