<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left: 0;">
		<table class="table table-condensed">
			<tr>
				<th>
					<input type="text" id="id" readonly="readonly" style="display:none;" />
					Select Department
				</th>
			</tr>
			<tr>
				<td>
					<select id="dept_id" class="span4">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `ot_dept_master` ORDER BY `ot_dept_name`");
						while($r=mysqli_fetch_assoc($q))
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
				<th>
					Package Name
				</th>
			</tr>
			<tr>
				<td><input type="text" id="name" style="width:90%;" placeholder="Package Name" /></td>
			</tr>
			<tr>
				<th>Amount</th>
			</tr>
			<tr>
				<td><input type="text" id="amount" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" style="" placeholder="Amount" /></td>
			</tr>
			<tr>
				<td style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" class="btn btn-danger" onclick="clrr()" value="Reset" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<input type="text" id="srch" style="width:90%;" onkeyup="load_header()" Placeholder="Search..." />
		<div id="res" style="max-height:500px;overflow-y:scroll;">
		
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
		load_header();
		$("#name").keyup(function(e)
		{
			$(this).val(($(this).val()).toUpperCase());
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#amount").focus();
			}
		});
		$("#amount").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#sav").focus();
			}
		});
	});
	
	function load_header()
	{
		$.post("pages/ot_package_master_ajax.php",
		{
			srch:$("#srch").val(),
			type:1,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function det(id)
	{
		$.post("pages/ot_package_master_ajax.php",
		{
			id:id,
			type:2,
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("#ea#");
			$("#id").val(vl[0]);
			$("#dept_id").val(vl[1]);
			$("#name").val(vl[2]);
			$("#amount").val(vl[3]);
			$("#sav").val('Update');
			$("#name").focus();
			//$("#res").html(data);
		})
	}
	function confirmm(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	function del()
	{
		$.post("pages/ot_package_master_ajax.php",
		{
			id:$("#idl").val(),
			type:"delete_header_procedure",
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
	function save()
	{
		if($("#dept_id").val()=="0")
		{
			$("#dept_id").focus();
		}
		else if($("#name").val().trim()=="")
		{
			$("#name").focus();
		}
		else if($("#amount").val().trim()=="")
		{
			$("#amount").focus();
		}
		else if(parseInt($("#amount").val().trim())<=0)
		{
			$("#amount").focus();
		}
		else
		{
			$("#sav").attr("disabled",true);
			$.post("pages/ot_package_master_ajax.php",
			{
				id:$("#id").val(),
				dept_id:$("#dept_id").val(),
				name:$("#name").val().trim(),
				amount:$("#amount").val().trim(),
				usr:$("#user").text().trim(),
				type:3,
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					$("#sav").attr("disabled",false);
					clrr();
				}, 1000);
			})
		}
	}
	function clrr()
	{
		$("#id").val('');
		$("#dept_id").val('0');
		$("#name").val('');
		$("#amount").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#dept_id").focus();
		load_header();
	}
</script>
