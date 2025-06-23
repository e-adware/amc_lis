<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Resource Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<input type="text" id="id" style="display:none;" readonly="readonly" />
		<table class="table table-bordered table-condensed" >
			<tr>
				<th>Select Grade</th>
				<td>
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
				<th>Select Cabin</th>
				<td>
					<select id="cab">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `ot_cabin_master` ORDER BY `ot_cabin_name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['ot_cabin_id'];?>"><?php echo $r['ot_cabin_name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Select Resource Type</th>
				<td>
					<select id="type">
						<option value="0">Select</option>
						<?php
						//$q=mysqli_query($link,"SELECT * FROM `ot_type_master` WHERE `link`>0 ORDER BY `type`");
						$q=mysqli_query($link,"SELECT * FROM `ot_type_master` ORDER BY `type`");
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
			<tr style="display:none;">
				<th>Select Employee</th>
				<td id="emp_list">
					<select id="emp">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`!='1' ORDER BY `name`");//WHERE `levelid`=''
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['emp_id'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Amount</th>
				<td><input type="text" id="fee" class="form-control" placeholder="Amount" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" value="0" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" name="intext7" id="button" value="Submit" onclick="save()" class="btn btn-default btn-info" style="width:100px"/>
					<input type="button" name="button2" id="button2" value="Reset" onclick="clrr()" class="btn btn-danger" style="width:100px"/>
					<input type="button" name="button3" id="button3" onclick="popitup('pages/surgery_rate_print.php')" value="View" class="btn btn-info" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<table class="table table-condensed table-bordered">
			<tr>
				<td>Search</td>
				<td> <input type="text" id="srch"  autocomplete="off" class="form-control span4" onkeyup="searchTable(this.value)" /></td>
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
		if($("#grade").val()=="0")
		{
			//$("#grade").focus();
			$("#grade").select2("focus");
			$("#grade").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		}
		else if($("#type").val()=="0")
		{
			//$("#type").focus();
			$("#type").select2("focus");
			$("#type").siblings(".select2-container").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		}
		/*else if($("#dept").val()=="0")
		{
			$("#dept").focus();
		}
		else if($("#emp").val()=="0")
		{
			//$("#emp").focus();
			$("#emp").select2("focus");
		}*/
		else if($("#cab").val().trim()=="0")
		{
			$("#cab").select2("focus");
		}
		else if($("#fee").val().trim()=="")
		{
			$("#fee").focus();
		}
		else
		{
			//alert($("#emp").val())
			$("#button").attr("disabled",true);
			$.post("pages/ot_resource_ajax.php",
			{
				id:$("#id").val(),
				grade:$("#grade").val(),
				cab:$("#cab").val(),
				typ:$("#type").val(),
				dept:0,
				emp:$("#emp").val(),
				fee:$("#fee").val(),
				user:$("#user").text().trim(),
				type:1,
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
			$("#cab").val(vl[2]).trigger("change");
			//$("#dept").val(vl[3]);
			$("#type").val(vl[3]).trigger("change");
			$("#fee").val(vl[4]);
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
			user:$("#user").text().trim(),
			type:3,
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
			type:4,
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
		$("#grade").val('0').trigger("change");
		$("#cab").val('0').trigger("change");
		$("#type").val('0').trigger("change");
		//$("#dept").val('0');
		$("#fee").val('0');
		//$("#emp").val('0');
		$("#srch").val('');
		$("#button").val('Submit');
		$("#button").attr("disabled",false);
		$("#grade").select2("focus");
		load_res();
	}
	function searchTable(inputVal)
	{
		var table = $('#tblData');
		table.find('tr').each(function(index, row)
		{
			var allCells = $(row).find('td');
			if(allCells.length>0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
						var regExp = new RegExp(inputVal, 'i');
						if(regExp.test($(td).text()))
						{
							found = true;
							return false;
						}
				});
				if(found == true)$(row).show();else $(row).hide();
			}
		});
	}
	function popitup(url)
	{
		var txtfrom=0;
		var txtto=0;
		url=url+"?date1="+txtfrom+"&date2="+txtto;
		newwindow=window.open(url,'window','left=10,top=10,height=600,witdh=600,menubar=1,resizeable=0,scrollbars=1');
	}
</script>
<style>
	.nm:hover{color:#000099;}
</style>
