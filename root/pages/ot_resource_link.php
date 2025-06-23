<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Resource Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<table class="table table-bordered table-condensed" >
			<tr style="display:none;">
				<th>Select Grade</th>
				<td>
					<input type="text" id="id" style="display:none;" readonly="readonly" />
					<select id="grade">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `ot_grade_master` ORDER BY `grade_name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['grade_id'];?>"><?php echo $r['grade_name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Select Resource Type</th>
				<td>
					<select id="type" onchange="load_emp()">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `ot_type_master` WHERE `link`>0 ORDER BY `type`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['type_id'];?>"><?php echo $r['type'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Select Employee</th>
				<td id="emp_list">
					<select id="emp" multiple="true">
						<option value="0">Select</option>
						<?php
						//$q=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`!='1' ORDER BY `name`");//WHERE `levelid`=''
						//while($r=mysqli_fetch_array($q))
						{
						?>
						<!--<option value="<?php echo $r['emp_id'];?>"><?php echo $r['name'];?></option>-->
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr style="display:none;">
				<th>Amount</th>
				<td><input type="text" id="fee" class="form-control" placeholder="Amount" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" name="intext7" id="button" value="Submit" onclick="save()" class="btn btn-default btn-info" style="width:100px"/>
					<input type="button" name="button2" id="button2" value="Reset" onclick="clrr()" class="btn btn-danger" style="width:100px"/> 
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<table class="table table-condensed table-bordered">
			<tr>
				<td>Search</td>
				<td> <input type="text" id="srch"  autocomplete="off" class="form-control span4" onkeyup="load_res()" placeholder="Search Type..." /></td>
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
<link rel="stylesheet" href="../css/select2.min.css" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function()
	{
		//$("#grade").focus();
		$("select").select2({ theme: "classic" });
		$("#grade").select2("focus");
		load_res();
		//load_emp();
		
		$("#grade").on("select2:close",function(e){$("#grade").siblings(".select2-container").css({'border-color':'','box-shadow':''});});
		$("#type").on("select2:close",function(e){$("#type").siblings(".select2-container").css({'border-color':'','box-shadow':''});});
	});
	function save()
	{
		if($("#type").val()=="0")
		{
			//$("#type").focus();
			$("#type").select2("focus");
			$("#type").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		}
		else if($("#emp").val()==null)
		{
			//$("#emp").focus();
			$("#emp").select2("focus");
		}
		else
		{
			//alert($("#emp").val())
			$("#button").attr("disabled",true);
			$.post("pages/ot_resource_ajax.php",
			{
				id:$("#id").val(),
				typ:$("#type").val(),
				emp:$("#emp").val(),
				user:$("#user").text().trim(),
				type:6,
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clrr();
					//load_emp();
				}, 1000);
			})
		}
	}
	function confirmm(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	function det(id)
	{
		$.post("pages/ot_resource_ajax.php",
		{
			id:id,
			type:2,
		},
		function(data,status)
		{
			var vl=data.split("#@#");
			$("#id").val(vl[0]);
			$("#grade").val(vl[1]).trigger("change");
			$("#type").val(vl[2]).trigger("change");
			//$("#dept").val(vl[3]);
			//$("#emp").val(vl[4]).trigger("change");
			$("#fee").val(vl[3]);
			$("#button").val('Update');
			$("#fee").focus();
			//$("#res").html(data);
		})
	}
	function load_res()
	{
		$.post("pages/ot_resource_ajax.php",
		{
			srch:$("#srch").val().trim(),
			type:7,
		},
		function(data,status)
		{
			$("#res").html(data);
			$("select").select2({ theme: "classic" });
		})
	}
	function del()
	{
		$.post("pages/ot_resource_ajax.php",
		{
			id:$("#idl").val(),
			type:8,
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				clrr();
				load_res();
				//load_emp();
			}, 1000);
		})
	}
	function load_emp()
	{
		$.post("pages/ot_resource_ajax.php",
		{
			type:5,
		},
		function(data,status)
		{
			$("#emp_list").html(data);
			$("select").select2({ theme: "classic" });
		})
	}
	function clrr()
	{
		$("#id").val('');
		$("#idl").val('');
		$("#type").val('0').trigger("change");
		//$("#dept").val('0');
		$("#emp").val('0').trigger("change");
		$("#srch").val('');
		$("#button").val('Submit');
		$("#button").attr("disabled",false);
		$("#type").select2("focus");
		load_res();
	}
</script>
<style>
	.nm:hover{color:#000099;}
</style>
