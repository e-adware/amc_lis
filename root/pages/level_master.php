<script>
	function load_id()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_level_id",
		},
		function(data,status)
		{
			$("#id").val(data);
			$("#name").focus();
		})
	}
	function load_level()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_level",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function save()
	{
		var ch="";
		var c=$(".chk:checked");
		for(var j=0;j<(c.length);j++)
		{
			ch=ch+$('#s'+$(c[j]).val()).val()+"@";
		}
		if($("#name").val()=="")
		{
			$("#name").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				sl:$("#sl").val(),
				id:$("#id").val(),
				name:$("#name").val(),
				ch:ch,
				type:"save_level",
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
		$.post("pages/global_load_g.php",
		{
			sl:sl,
			type:"edit_level",
		},
		function(data,status)
		{
			var vl=data.split("@govin@");
			$("#sl").val(vl[0]);
			$("#id").val(vl[1]);
			$("#name").val(vl[2]);
			var ck=vl[3].split("@");
			$(".chk").attr('checked',false);
			for(var l=0; l<ck.length; l++)
			{
				if(ck[l])
				$("#s"+ck[l]).attr('checked',true);
			}
			$("#sav").val('Update');
			$("#name").focus();
		})
	}
	function del(sl)
	{
		$("#dl").click();
		$("#idl").val(sl);
	}
	function delete_level()
	{
		$.post("pages/global_delete_g.php",
		{
			sl:$("#idl").val(),
			type:"delete_level",
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
	function load_user_counts()
	{
		$.post("pages/level_master_ajax.php",
		{
			type:1,
		},
		function(data,status)
		{
			$("#load_user_counts").html(data);
		})
	}
	function srch()
	{
		$.post("pages/global_load_g.php",
		{
			srch:$("#srch").val(),
			type:"search_level",
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
		$("#srch").val('');
		$(".chk").attr('checked',false);
		$("#sav").val('Save');
		load_id();
		load_level();
	}
</script>
<style>
	.table tr:hover
	{background:none;}
	label
	{margin-right:20px;padding:3px;background:#ffffff;border-radius:5px;}
	label:hover
	{margin-right:20px;padding:3px;background:#ffffff;border-radius:5px;box-shadow:1px 1px 1px 1px #aaaaaa;}
</style>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Level Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span4" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>
					<b>Level Id</b><br/>
					<input type="text" id="sl" style="display:none;" />
					<input type="text" id="id" readonly="readonly" class="span3" />
				</td>
			</tr>
			<tr>
				<td>
					<b>Level Name</b><br/>
					<input type="text" id="name" class="span3" placeholder="Level Name" />
				</td>
			</tr>
			<tr>
				<td>
					<b>Select Snippets</b><br/>
					<label><input type="checkbox" class="chk" id="s1" value="1" /> OPD Snippet</label> <!-- 1 for OPD -->
					<label><input type="checkbox" class="chk" id="s3" value="3" /> IPD Snippet</label> <!-- 2 for IPD -->
					<label><input type="checkbox" class="chk" id="s2" value="2" /> LAB Snippet</label> <!-- 3 for LAB -->
					<!--<label><input type="checkbox" class="chk" id="s4" value="4" /> Pharmacy Snippet</label>  4 for Pharmacy -->
					<label><input type="checkbox" class="chk" id="s4" value="4" /> Casualty Snippet</label> <!-- 7 for Casualty -->
					<label style="display:none;"><input type="checkbox" class="chk" id="s5" value="5" /> Daycare Snippet</label> <!-- 8 for Dental -->
					<label style="display:none;"><input type="checkbox" class="chk" id="s6" value="6" /> Dental Snippet</label> <!-- 8 for Dental -->
					<label style="display:none;"><input type="checkbox" class="chk" id="s7" value="7" /> Dialysis Snippet</label> <!-- 9 for Emergency -->
					<label style="display:none;"><input type="checkbox" class="chk" id="s9" value="9" /> Miscellaneous Snippet</label> <!-- 10 for Physiotherapy -->
					<label style="display:none;"><input type="checkbox" class="chk" id="s14" value="14" /> Ambulance Snippet</label> <!-- 10 for Physiotherapy -->
					<label style="display:none;"><input type="checkbox" class="chk" id="s15" value="15" /> Procedure Snippet</label> <!-- 10 for Physiotherapy -->
					<!--<label><input type="checkbox" class="chk" id="s10" value="10" /> Radiology Snippet</label>  10 for Physiotherapy -->
					<label><input type="checkbox" class="chk" id="s11" value="11" /> Bed Snippet</label> <!-- 11 for Bed status -->
				</td>
			</tr>
			<tr>
				<td style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" id="" class="btn btn-danger" onclick="clrr()" value="Reset" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<b>Search</b> <input type="text" id="srch" onkeyup="srch()" class="span4" placeholder="Search..." />
		<div id="res" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
	<div class="span3">
		<div id="load_user_counts">
			
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
	<script>
		load_id();
		load_level();
		load_user_counts();
	</script>
</div>
