<script>
	function ValidateEmail(email)
    {
        var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        return expr.test(email);
	}
	function save()
	{
		if($("#name").val()=="")
		{
			$("#name").focus();
		}
		else if($("#staff").val()=="0")
		{
			$("#staff").focus();
		}
		else if($("#pass").val()=="")
		{
			$("#pass").focus();
		}
		else
		{
			//alert($('input[name=edit_opd]:checked').val());
			$.post("pages/global_insert_data_g.php",
			{
				id:$("#id").val(),
				name:$("#name").val(),
				quali:$("#quali").val(),
				staff:$("#staff").val(),
				add:$("#add").val(),
				contact:$("#contact").val(),
				pass:$("#pass").val(),
				edit_opd:$('input[name=edit_opd]:checked').val(),
				edit_lab:$('input[name=edit_lab]:checked').val(),
				cancel_pat:$('input[name=cancel_pat]:checked').val(),
				usr:$("#user").text(),
				type:"save_user",
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
	function edt(i)
	{
		$.post("pages/global_load_g.php",
		{
			id:i,
			type:"edit_user",
		},
		function(data,status)
		{
			var vl=data.split("@govin@");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#contact").val(vl[2]);
			$("#add").val(vl[3]);
			$("#quali").val(vl[4]);
			$("#staff").val(vl[5]);
			$("#pass").val(vl[6]);
			if(vl[7]==1)
			{
				$('input:radio[name=edit_opd]').filter('[value=1]').prop('checked', true);
			}else
			{
				$('input:radio[name=edit_opd]').filter('[value=0]').prop('checked', true);
			}
			if(vl[8]==1)
			{
				$('input:radio[name=edit_lab]').filter('[value=1]').prop('checked', true);
			}else
			{
				$('input:radio[name=edit_lab]').filter('[value=0]').prop('checked', true);
			}
			if(vl[9]==1)
			{
				$('input:radio[name=cancel_pat]').filter('[value=1]').prop('checked', true);
			}else
			{
				$('input:radio[name=cancel_pat]').filter('[value=0]').prop('checked', true);
			}
			$("#sav").val('Update');
			$("#sav").removeClass("btn-info");
			$("#sav").addClass("btn-success");
			$("#name").focus();
		})
	}
	function del(i)
	{
		$("#dl").click();
		$("#idl").val(i);
	}
	function delete_user()
	{
		$.post("pages/global_delete_g.php",
		{
			id:$("#idl").val(),
			type:"delete_user",
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
	function load_user()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_user",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function srch()
	{
		$.post("pages/global_load_g.php",
		{
			srch:$("#srch").val(),
			type:"search_user",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function clrr()
	{
		/*$("#id").val('');
		$("#name").val('');
		$("#quali").val('');
		$("#staff").val('0');
		$("#add").val('');
		$("#contact").val('');
		$("#pass").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#sav").removeClass("btn-success");
		$("#sav").addClass("btn-info");
		$("#name").focus();
		load_user();*/
		document.location.reload(true);
	}
</script>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">User Master</span><br/>
    <b>Note:</b> ( <b style="color:#f00;">*</b> ) Required fields
    </div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>
					Name
					<input type="text" id="id" style="display:none;" />
				</td>
				<td><input type="text" id="name" class="span3" placeholder="Name" autofocus /> <b style="color:#f00;">*</b></td>
			</tr>
			<tr>
				<td>Staff</td>
				<td>
					<select id="staff">
						<option value="0">--Select--</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `level_master` ORDER BY `name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['levelid'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select> <b style="color:#f00;">*</b>
				</td>
			</tr>
			<tr>
				<td>Qualification</td>
				<td><input type="text" id="quali" class="span3" placeholder="Qualification" /></td>
			</tr>
			<tr>
				<td>Address</td>
				<td><input type="text" id="add" class="span3" placeholder="Address" /></td>
			</tr>
			<tr>
				<td>Contact Number</td>
				<td><input type="text" id="contact" class="span3" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" placeholder="Contact Number" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" id="pass" class="span3" placeholder="Password" /> <b style="color:#f00;">*</b></td>
			</tr>
			<tr>
				<td>OPD Edit</td>
				<td>
					<label class="radio-inline">
					  <input type="radio" id="edit_opd" name="edit_opd" value="0" checked > <span>No </span>
					</label>
					<label class="radio-inline">
					  <input type="radio" id="edit_opd" name="edit_opd" value="1" > <span>Yes </span>
					</label>
				</td>
			</tr>
			<tr>
				<td>Lab Edit</td>
				<td>
					<label class="radio-inline">
					  <input type="radio" id="edit_lab" name="edit_lab" value="0" checked > <span>No </span>
					</label>
					<label class="radio-inline">
					  <input type="radio" id="edit_lab" name="edit_lab" value="1" > <span>Yes </span>
					</label>
				</td>
			</tr>
			<tr>
				<td>Cancel Patient</td>
				<td>
					<label class="radio-inline">
					  <input type="radio" id="cancel_pat" name="cancel_pat" value="0" checked > <span>No </span>
					</label>
					<label class="radio-inline">
					  <input type="radio" id="cancel_pat" name="cancel_pat" value="1" > <span>Yes </span>
					</label>
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
		<b>Search</b> <input type="text" id="srch" onkeyup="srch()" class="span4" placeholder="Search..." />
		<div id="res" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
	<script>load_user();</script>
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal hide">
		  <div class="modal-body">
			  <input type="text" id="idl" style="display:none;" />
			<p>Are You Sure Want To Delete...?</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="delete_user()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end-->
</div>
<script src="../js/jquery.uniform.js"></script>
<link rel="stylesheet" href="../css/uniform.css" type="text/css" />
