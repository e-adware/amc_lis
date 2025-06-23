<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header">Donor Registration</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<form id="form1" method="post">
			<table class="table table-bordered table-condensed" >
				<tr>
					<td>Type of Donor</td>
					<td>
						<select id="dtype" onkeyup="tab(this.id,event)" autofocus>
							<option value="0">Select</option>
							<?php
							$q=mysqli_query($link,"SELECT * FROM `blood_donor_type` ORDER BY `name`");
							while($r=mysqli_fetch_array($q))
							{
							?>
							<option value="<?php echo $r['type_id'];?>"><?php echo $r['name'];?></option>
							<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Name <input type="text" id="did" style="display:none;" /></td>
					<td><input type="text" id="dname" placeholder="Donor Name" /></td>
				</tr>
				<tr>
					<td>Weight</td>
					<td><input type="text" id="weight" class="span1" onkeyup="tab(this.id,event)" maxlength="2" placeholder="Weight" /> KG</td>
				</tr>
				<tr>
					<td>Age</td>
					<td><input type="text" id="age" class="span1" onkeyup="tab(this.id,event)" maxlength="2" placeholder="Age" /> Years</td>
				</tr>
				<tr>
					<td>Sex</td>
					<td>
						<select id="sex" onkeyup="tab(this.id,event)">
							<option value="0">Select</option>
							<option value="male">Male</option>
							<option value="female">Female</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Contact</td>
					<td><input type="text" id="contact" maxlength="10" onkeyup="tab(this.id,event)" placeholder="Contact Number" /></td>
				</tr>
				<tr>
					<td>Blood Group (ABO/Rh)</td>
					<td>
						<select id="abo" class="span2" onkeyup="tab(this.id,event)">
							<option value="0">Select</option>
							<option value="A">A</option>
							<option value="B">B</option>
							<option value="AB">AB</option>
							<option value="O">O</option>
						</select>
						<select id="rh" class="span2" onkeyup="tab(this.id,event)">
							<option value="0">Select</option>
							<option value="positive">Positive</option>
							<option value="negative">Negative</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Last Donating Date</td>
					<td><input type="text" id="ldt" onkeyup="tab(this.id,event)" placeholder="Last Donating Date" /></td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td><textarea id="remark" style="resize:none;width:90%;" onkeyup="tab(this.id,event)" placeholder="Remarks"></textarea></td>
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
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#dname").keyup(function(e)
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
					$("#weight").focus();
				}
			}
		});
		$("#ldt").datepicker({dateFormat: 'yy-mm-dd',maxDate:"0",changeYear:true});
	});
	function tab(id,e)
	{
		if(e.keyCode==13)
		{
			if(id=="dtype" && $("#"+id).val()!="0")
			{
				$("#dname").focus();
			}
			if(id=="weight" && $("#"+id).val()!="")
			{
				$("#age").focus();
			}
			if(id=="age" && $("#"+id).val()!="")
			{
				$("#sex").focus();
			}
			if(id=="sex" && $("#"+id).val()!="0")
			{
				$("#contact").focus();
			}
			if(id=="contact" && $("#"+id).val()!="" && ($("#"+id).val()).length==10)
			{
				$("#abo").focus();
			}
			if(id=="abo")
			{
				$("#rh").focus();
			}
			if(id=="rh")
			{
				$("#ldt").focus();
			}
			if(id=="ldt")
			{
				$("#remark").focus();
			}
			if(id=="remark")
			{
				$("#button").focus();
			}
		}
	}
	function save()
	{
		if($("#dtype").val()=="0")
		{
			$("#dtype").focus();
		}
		else if($("#dname").val()=="")
		{
			$("#dname").focus();
		}
		else if($("#weight").val()=="" || (parseInt($("#weight").val()))<=0)
		{
			$("#weight").focus();
		}
		else if($("#age").val()=="" || (parseInt($("#age").val()))<=0)
		{
			$("#age").focus();
		}
		else if($("#sex").val()=="0")
		{
			$("#sex").focus();
		}
		else if($("#contact").val()=="")
		{
			$("#contact").focus();
		}
		else if(($("#contact").val()).length!=10)
		{
			$("#contact").focus();
		}
		/*else if($("#abo").val()=="0")
		{
			$("#abo").focus();
		}
		else if($("#rh").val()=="0")
		{
			$("#rh").focus();
		}
		else if($("#ldt").val()=="")
		{
			$("#ldt").focus();
		}*/
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				did:$("#did").val(),
				dtype:$("#dtype").val(),
				dname:$("#dname").val(),
				weight:$("#weight").val(),
				age:$("#age").val(),
				sex:$("#sex").val(),
				contact:$("#contact").val(),
				abo:$("#abo").val(),
				rh:$("#rh").val(),
				ldt:$("#ldt").val(),
				remark:$("#remark").val().trim(),
				usr:$("#user").text().trim(),
				type:"save_donor_reg",
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
		$("#did").val('');
		$("#dtype").val('0');
		$("#dname").val('');
		$("#weight").val('');
		$("#age").val('');
		$("#sex").val('0');
		$("#contact").val('');
		$("#abo").val('0');
		$("#rh").val('0');
		$("#ldt").val('');
		$("#remark").val('');
		$("#srch").val('');
		$("#button").val('Save');
		$("#dtype").focus();
	}
</script>
