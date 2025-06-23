<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">OT Charge Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<form id="form1" method="post">
			<table class="table table-bordered table-condensed" >
				<tr>
					<td>Charge Name</td>
					<td>
						<input type="text" id="id" value="" style="display:none;" readonly="readonly" class="intext span4"/>
						<input type="text"  id="type" value="" autocomplete="off" class="intext span4" placeholder="Charge Name" autofocus />
					</td>
				</tr>
				<tr>
					<td>Rate</td>
					<td>
						<input type="text"  id="rate" value="" autocomplete="off" class="intext span4" placeholder="Rate" />
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
		<input type="text" id="srch"  autocomplete="off" style="width:90%;" onkeyup="load_charge()" />
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
		//load_id();
		load_charge();
		// ot_charge_master_ajax
	});
	function save()
	{
		if($("#type").val().trim()=="")
		{
			$("#type").focus();
		}
		else if($("#rate").val().trim()=="")
		{
			$("#type").focus();
		}
		else if(parseInt($("#rate").val().trim())==0)
		{
			$("#rate").focus();
		}
		else
		{
			$("#button").attr("disabled",true);
			$.post("pages/ot_charge_master_ajax.php",
			{
				id:$("#id").val(),
				chname:$("#type").val().trim(),
				rate:$("#rate").val().trim(),
				type:3,
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clrr();
					$("#button").attr("disabled",false);
				}, 1000);
			})
		}
	}
	function load_charge()
	{
		$.post("pages/ot_charge_master_ajax.php",
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
		$.post("pages/ot_charge_master_ajax.php",
		{
			id:$("#idl").val(),
			type:4,
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
		$.post("pages/ot_charge_master_ajax.php",
		{
			id:id,
			type:2,
		},
		function(data,status)
		{
			var vl=data.split("#@#");
			$("#id").val(vl[0]);
			$("#type").val(vl[1]);
			$("#rate").val(vl[2]);
			$("#button").val("Update");
			$("#type").focus();
		})
	}
	function clrr()
	{
		$("#id").val('');
		$("#type").val('');
		$("#rate").val('');
		$("#srch").val('');
		$("#button").val('Submit');
		$("#type").focus();
		//load_id();
		load_charge();
	}
</script>
