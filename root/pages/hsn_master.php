<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">HSN Code Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left:0px;">
		<form id="form1" method="post">
			<table class="table table-bordered table-condensed">
				<tr>
					<td>HSN Code <input type="text" id="id" style="display:none;" /></td>
					<td>
						<input list="browsrs" type="text" name="intext2"  id="txthsn" value="" autocomplete="off" class="intext span4" autofocus />
						<datalist id="browsrs">
						<?php
							$pid = mysqli_query($link," SELECT `hsn_code` FROM `hsn_master` order by `hsn_code` DESC");
							while($pat_uid=mysqli_fetch_array($pid))
							{
								echo "<option value='$pat_uid[hsn_code]'>";
							}
						?>
						</datalist>
					</td>
				</tr>
				
				<tr>
					<td>Description</td>
					<td>
						<input type="text" name="intext3" id="desc" autocomplete="off" class="intex span4" />
					</td>
				</tr>
				<tr>
					<td>GST</td>
					<td>
						<input type="text" name="intext5" id="txtgst" autocomplete="off" class="intext" /> %
					</td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center">
						<input type="button" name="intext8" id="button" value= "Submit" onclick="save()" class="btn btn-default btn-info" style="width:100px"/>
						<input type="button" name="button2" id="button2" value="Reset" onclick="clrr()" class="btn btn-danger" style="width:100px"/> 
						
						<!--<input type="button" name="button1" id="button1" value= "View" onclick="popitup1('pages/itemlist_rpt.php')" class="btn btn-success" style="width:100px" />-->
					</td>
				</tr>
			</table>
		</div>
		<div class="span6">
			<table class="table table-condensed table-bordered">
				<tr>
					<td>Search</td>
					<td> <input type="text" name="txtcustnm" id="txtcustnm"  autocomplete="off" class="intext span4" onkeyup="load_item()" /></td>
				</tr>
			</table>
			<div id="load_materil" class="vscrollbar" style="max-height:400px;overflow-y:scroll;" >
				
			</div>
		<div id="back"></div>
		<div id="results"></div>
	</form>
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
	<!--modal-->
		<a href="#myAlert1" data-toggle="modal" id="cnf" class="btn" style="display:none;">A</a>
		<div id="myAlert1" class="modal fade">
		  <div class="modal-body">
			<p>HSN Code Already Exist</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="$('#txthsn').focus()" class="btn btn-info" href="#">Ok</a>
		  </div>
		</div>
	  <!--modal end-->
</div>
</div>
<style>
	.itname:hover
	{
		color:#0000FF;
	}
</style>
<script>
	$(document).ready(function()
	{
		// //$(this).val($this.val().replace(/[^\d.]/g, ''))
		//$("#gen_name").select2();
		load_item();
		
		$("#txtcntrname").keyup(function(e)
		{
			$(this).css("border","");
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#txthsn").focus();
					//$('#selectcategory').select2('focus');
				}
			}
		});
		
		$("#txtstrength").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#txtgst").focus();
			}
		});
		
	});
	
	function load_item()
	{
		$.post("pages/nursing_load_g.php",
		{
			val:$("#txtcustnm").val(),
			type:"load_item_hsn",
		},
		function(data,status)
		{
			$("#load_materil").html(data);
		})
	}
	
	
	function det(id)///for retrieve data against center
	{
	  $.post("pages/nursing_load_g.php",
		{
			type:"edit_hsn_master",
			id:id,
		},
		function(data,status)
		 {
			var val=data.split("@");
			$("#id").val(val[0]);
			$("#txthsn").val(val[1]);
			$("#desc").val(val[2]);
			$("#txtgst").val(val[3]);
			$("#button").val("Update");
			$("#desc").focus();
		 })
	}
	
	function save()
	{
		if($("#txthsn").val()=="")
		{
			$("#txthsn").focus();
		}
		else if($("#desc").val()=="")
		{
			$("#desc").focus();
		}
		else if($("#txtgst").val()=="")
		{
			$("#txtgst").focus();
		}
		else
		{
			$.post("pages/nursing_load_g.php",
			{
				id:$("#id").val(),
				hsn:$("#txthsn").val(),
				desc:$("#desc").val(),
				gst:$("#txtgst").val(),
				type:"save_hsn_master",
			},
			function(data,status)
			{
				if(data==0)
				{
					$("#cnf").click();
				}
				if(data==1)
				{
					bootbox.dialog({ message: 'Saved'});
					setTimeout(function()
					{
						bootbox.hideAll();
						clrr();
					}, 1000);
				}
				if(data==2)
				{
					bootbox.dialog({ message: 'Updated'});
					setTimeout(function()
					{
						bootbox.hideAll();
						clrr();
					}, 1000);
				}
			})
		}
	}
	function delete_data(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	
	function clrr()
	{
		$("#idl").val('');
		$("#id").val('');
		$("#txthsn").val('');
		$("#desc").val('');
		$("#txtgst").val('');
		$("#txtcustnm").val('');
		$("#button").val('Save');
		load_item();
		$("#txthsn").focus();
	}
	function del()
	{
		$.post("pages/nursing_load_g.php",
		{
			type:"hsn_master_delete",
			id:$("#idl").val(),
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
