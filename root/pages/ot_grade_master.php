<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">OT Grade Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<form id="form1" method="post">
			<table class="table table-bordered table-condensed" >
				<tr>
					<th>Select Department</th>
					<td>
						<select id="dept" onchange="$(this).siblings('.select2-container').css({'border-color':'','box-shadow':''})">
							<option value="0">Select</option>
							<?php
							$q=mysqli_query($link,"SELECT `ot_dept_id`, `ot_dept_name` FROM `ot_dept_master` ORDER BY `ot_dept_name`");
							while($r=mysqli_fetch_array($q))
							{
							?>
							<option value="<?php echo $r['ot_dept_id'];?>"><?php echo $r['ot_dept_name'];?></option>
							<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th>Grade Name</th>
					<td>
						<input type="text" id="id" value="" style="display:none;" readonly="readonly" class="intext span4"/>
						<input type="text"  id="grade" value="" autocomplete="off" class="intext span4" autofocus />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center">
						<input type="button" name="intext7" id="button" value="Submit" onclick="save()" class="btn btn-default btn-info" style="width:100px"/>
						<input type="button" name="button2" id="button2" value="Reset" onclick="clrr()" class="btn btn-danger" style="width:100px"/> 
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="span5">
		<table class="table table-condensed table-bordered">
			<tr>
				<td>Search</td>
				<td> <input type="text" id="srch"  autocomplete="off" class="form-control span4" onkeyup="load_list()" /></td>
			</tr>
		</table>
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
<link rel="stylesheet" href="../css/select2.min.css" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function()
	{
		load_list();
		$("select").select2({ theme: "classic" });
	});
	function save()
	{
		if($("#dept").val()=="0")
		{
			$("#dept").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
			$("#dept").select2("focus");
		}
		else if($("#grade").val().trim()=="")
		{
			$("#grade").focus();
		}
		else
		{
			$("#button").attr("disabled",true);
			$.post("pages/ot_grade_master_ajax.php",
			{
				id:$("#id").val(),
				dept:$("#dept").val(),
				grade:$("#grade").val(),
				type:"save_ot_grade",
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
	function load_list()
	{
		$.post("pages/ot_grade_master_ajax.php",
		{
			srch:$("#srch").val(),
			type:"load_ot_grade",
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
		$.post("pages/ot_grade_master_ajax.php",
		{
			id:$("#idl").val(),
			type:"delete_ot_grade",
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
		$.post("pages/ot_grade_master_ajax.php",
		{
			id:id,
			type:"load_ot_grade_details",
		},
		function(data,status)
		{
			var vl=data.split("#@#");
			$("#id").val(vl[0]);
			$("#dept").val(vl[1]).trigger("change");
			$("#grade").val(vl[2]);
			$("#button").val("Update");
			$("#grade").focus();
		})
	}
	function clrr()
	{
		$("#id").val('');
		$("#dept").val('0').trigger('change');
		$("#grade").val('');
		$("#srch").val('');
		$("#button").val('Submit');
		$("#grade").focus();
		load_list();
	}
</script>
