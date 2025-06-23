<?php
$user_change_disabled="disabled";
if($p_info["levelid"]==1)
{
	$user_change_disabled="";
}
if($p_info["levelid"]==1)
{
	$branch_str="";
	$branch_display="display:none;";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
}
$branch_id=$p_info["branch_id"];
?>
<script>
	$(document).ready(function()
	{
		$("#ward").change(function()
		{
			$(this).css("border","");
		});
		$("#ward").keyup(function(e)
		{
			if($(this).val()=="0" && e.keyCode==13)
			{
				$(this).css("border","1px solid #f00");
			}
			else
			{
				$(this).css("border","");
				if(e.keyCode==13)
				$("#room").focus();
			}
		});
		$("#room").keyup(function(e)
		{
			if($(this).val()=="" && e.keyCode==13)
			{
				$(this).css("border","1px solid #f00");
			}
			else
			{
				$(this).css("border","");
				if(e.keyCode==13)
				$("#sav").focus();
			}
		});
	});
	function save()
	{
		if($("#ward").val()=="0")
		{
			$("#ward").focus();
		}
		else if($("#room").val()=="")
		{
			$("#room").focus();
		}
		else
		{
			$.post("pages/ward_master_data.php",
			{
				branch_id:$("#branch_id").val(),
				id:$("#id").val(),
				ward:$("#ward").val(),
				room:$("#room").val(),
				type:"save_room",
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
	function load_room()
	{
		$.post("pages/ward_master_data.php",
		{
			type:"load_room",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function edt(id)
	{
		$.post("pages/ward_master_data.php",
		{
			id:id,
			type:"edit_room",
		},
		function(data,status)
		{
			var vl=data.split("@govin@");
			$("#id").val(vl[0]);
			$("#ward").val(vl[1]);
			$("#room").val(vl[2]);
			$("#branch_id").val(vl[3]);
			$("#ward").css("border","");
			$("#room").css("border","");
			$("#sav").val("Update");
			$("#room").focus();
		})
	}
	function del(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	function delete_room()
	{
		$.post("pages/ward_master_data.php",
		{
			id:$("#idl").val(),
			type:"delete_room",
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
		$("#id").val('');
		$("#ward").val('0');
		$("#room").val('');
		$("#sav").val('Save');
		$("#ward").focus();
		load_room();
	}
</script>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr style="<?php echo $branch_display; ?>">
				<th>Branch</th>
				<td>
					<select id="branch_id" class="span3" onchange="load_room()" style="<?php echo $branch_display; ?>">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					Select Ward
					<input type="text" id="id" style="display:none;" />
				</td>
				<td>
					<select id="ward" autofocus>
						<option value="0">--Select--</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `ward_master` WHERE `ward_id`>0 $branch_str ORDER BY `name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['ward_id'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					Room No
				</td>
				<td>
					<input type="text" id="room" class="span3" placeholder="Room No" />
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
	<div class="span5" style="">
		<!--<input type="text" class="span4" id="srch" onkeyup="srch()" placeholder="Search..." />-->
		<div id="res" style="max-height:300px;overflow-y:scroll;">
		
		</div>
	</div>
	<script>load_room();</script>
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal hide">
		  <div class="modal-body">
			  <input type="text" id="idl" style="display:none;" />
			<p>Are You Sure Want To Delete...?</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="clrr()" class="btn btn-inverse" href="#"><i class="icon-remove"></i> Cancel</a>
			<a data-dismiss="modal" onclick="delete_room()" class="btn btn-danger" href="#"><i class="icon-ok"></i> Delete</a>
		  </div>
		</div>
	  <!--modal end-->
</div>
