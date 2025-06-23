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
	function load_id()
	{
		$.post("pages/ward_master_data.php",
		{
			type:"load_ward_id",
		},
		function(data,status)
		{
			$("#id").val(data);
			$("#name").focus();
		})
	}
	function load_ward()
	{
		$.post("pages/ward_master_data.php",
		{
			type:"load_ward",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#res").html(data);
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
			$.post("pages/ward_master_data.php",
			{
				sl:$("#sl").val(),
				branch_id:$("#branch_id").val(),
				id:$("#id").val(),
				name:$("#name").val(),
				floor_name:$("#floor_name").val(),
				type:"save_ward",
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
	function edt(sl)
	{
		$.post("pages/ward_master_data.php",
		{
			sl:sl,
			type:"edit_ward",
		},
		function(data,status)
		{
			var vl=data.split("@govin@");
			$("#sl").val(vl[0]);
			$("#id").val(vl[1]);
			$("#name").val(vl[2]);
			$("#floor_name").val(vl[3]);
			$("#branch_id").val(vl[4]);
			$("#sav").val('Update');
			$("#name").focus();
		})
	}
	function del(sl)
	{
		$("#dl").click();
		$("#idl").val(sl);
	}
	function delete_ward()
	{
		$.post("pages/ward_master_data.php",
		{
			sl:$("#idl").val(),
			type:"delete_ward",
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
	function srch()
	{
		$.post("pages/ward_master_data.php",
		{
			srch:$("#srch").val(),
			type:"search_ward",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function clrr()
	{
		$("#sl").val('');
		$("#name").val('');
		$("#floor_name").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		load_id();
		load_ward();
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
					<select id="branch_id" class="span3" onchange="load_ward()" style="<?php echo $branch_display; ?>">
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
				<td>Ward Id</td>
				<td>
					<input type="text" id="sl" style="display:none;" />
					<input type="text" id="id" readonly="readonly" class="span3" />
				</td>
			</tr>
			<tr>
				<td>Ward Name</td>
				<td><input type="text" id="name" class="span3" placeholder="Ward Name" /></td>
			</tr>
			<tr>
				<td>Floor Name</td>
				<td><input type="text" id="floor_name" class="span3" placeholder="Floor Name" /></td>
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
		<!--modal-->
			<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
            <div id="myAlert" class="modal hide">
              <div class="modal-body">
				  <input type="text" id="idl" style="display:none;" />
                <p>Are You Sure Want To Delete...?</p>
              </div>
              <div class="modal-footer">
				  <a data-dismiss="modal" onclick="clrr()" class="btn btn-inverse" href="#"><i class="icon-remove"></i> Cancel</a>
				<a data-dismiss="modal" onclick="delete_ward()" class="btn btn-danger" href="#"><i class="icon-ok"></i> Confirm</a>
			  </div>
            </div>
          <!--modal end-->
	<script>
		load_id();
		load_ward();
	</script>
</div>
