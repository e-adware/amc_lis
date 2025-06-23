<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Clinical Procedures</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left: 0;">
		<table class="table table-condensed" id="pro_tbl">
			<tr>
				<th colspan="2">Procedure Under <input type="text" id="id" readonly="readonly" style="display:none;" /></th>
			</tr>
			<tr>
				<td colspan="2">
					<select id="head" autofocus>
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `clinical_procedure_header` ORDER BY `name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['head_id'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th colspan="2">Procedure Name</th>
			</tr>
			<tr>
				<td colspan="2"><input type="text" id="name" style="width:90%;" placeholder="Procedure Name" /></td>
			</tr>
			<tr>
				<th colspan="2">Grade</th>
			</tr>
			<tr>
				<td colspan="2">
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
				<th colspan="2">Rate</th>
			</tr>
			<tr>
				<td colspan="2">
					<input type="text" id="rate" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Rate" />
					<span style="float:right;">
						<button type="button" class="btn btn-primary" onclick="add_resourse()">Add Resourse</button>
					</span>
				</td>
			</tr>
			<tr id="tr_add" style="display:none;">
				<th>Select Resource</th>
				<th>Amount</th>
			</tr>
			<!--<tr>
				<td id="res_list">
					<select id="res_typ">
						<option value="0">Select</option>
						<?php
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
				<td>
					<input type="text" id="res_amt" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Amount" />
				</td>
			</tr>-->
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" class="btn btn-danger" onclick="clrr()" value="Reset" />
					<input type="button" class="btn btn-success" onclick="view_list()" value="View" />
					<span id="msgg" style="display:none;position:absolute;left:20%;font-size:20px;color:#C71313;"></span>
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<input type="text" id="srch" style="width:90%;" onkeyup="load_clinical_procedures()" Placeholder="Search..." />
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
		load_clinical_procedures();
	});
	function add_resourse()
	{
		var r_row=$(".res_row");
		var all="";
		if(r_row.length>0)
		{
			for(var i=0; i<r_row.length; i++)
			{
				if($(".res_row:eq("+i+")").find('td:eq(0) select:first').val()==0)
				{
					$(".res_row:eq("+i+")").find('td:eq(0) select:first').focus();
					return true;
				}
				if($(".res_row:eq("+i+")").find('td:eq(1) input:first').val().trim()=="")
				{
					$(".res_row:eq("+i+")").find('td:eq(1) input:first').focus();
					return true;
				}
			}
			for(var i=0; i<r_row.length; i++)
			{
				all+=$(".res_row:eq("+i+")").find('td:eq(0) select:first').val()+"@@";
			}
			//alert(all);
		}
		$.post("pages/clinical_procedures_ajax.php",
		{
			all:all,
			type:"load_res_list",
		},
		function(data,status)
		{
			if(r_row.length==0)
			{
				$("#tr_add").show();
				$("#tr_add").closest("tr").after('<tr class="res_row"><td>'+data+'</td><td><input type="text" onkeyup="set_amt(this)" placeholder="Amount" /><span style="float:right;"><i class="icon-remove icon-large i_rem" style="color:#aa0000;cursor:pointer;" onclick="$(this).parent().parent().parent().remove()"></i></span></td></tr>');
			}
			else
			{
				$(".res_row:last").closest("tr").after('<tr class="res_row"><td>'+data+'</td><td><input type="text" onkeyup="set_amt(this)" placeholder="Amount" /><span style="float:right;"><i class="icon-remove icon-large i_rem" style="color:#aa0000;cursor:pointer;" onclick="$(this).parent().parent().parent().remove()"></i></span></td></tr>');
			}
		})
	}
	function set_amt(a)
	{
		var t_rate=0;
		var r_row=$(".res_row");
		var s_rate=0;
		var rt=0;
		if($("#rate").val()!="")
		{
			t_rate=parseInt($("#rate").val());
		}
		for(var i=0; i<r_row.length; i++)
		{
			if($(".res_row:eq("+i+")").find('td:eq(1) input:first').val()=="")
			{
				rt=0;
			}
			else
			{
				rt=parseInt($(".res_row:eq("+i+")").find('td:eq(1) input:first').val());
			}
			s_rate+=rt;
		}
		if(t_rate==s_rate)
		{
			$(".res_row").find('td:eq(1) input:first').css("border","");
		}
		else
		{
			$(".res_row").find('td:eq(1) input:first').css("border","1px solid #FF0000");
			//$(a).css("border","1px solid #FF0000");
		}
	}
	function load_clinical_procedures()
	{
		$.post("pages/clinical_procedures_ajax.php",
		{
			srch:$("#srch").val(),
			type:"load_clinical_procedures",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function det(id)
	{
		$.post("pages/clinical_procedures_ajax.php",
		{
			id:id,
			type:"load_clinical_procedures_det",
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("#ea#");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#head").val(vl[2]);
			$("#grade").val(vl[3]);
			$("#rate").val(vl[4]);
			$("#res_typ").val(vl[5]);
			var r_amt=vl[6];
			r_amt=r_amt.split(".");
			$("#res_amt").val(r_amt[0]);
			$("#sav").val('Update');
			$("#name").focus();
			//$("#res").html(data);
		})
		load_resources(id);
	}
	function load_resources(id)
	{
		$(".i_rem").click();
		$.post("pages/clinical_procedures_ajax.php",
		{
			id:id,
			type:"load_resources",
		},
		function(data,status)
		{
			//alert(data);
			if(data!="")
			{
				$("#tr_add").show();
				var vl=data.split("#@#");
				var trr="";
				for(var j=0; j<vl.length; j++)
				{
					trr=vl[j];
					if(trr!="")
					{
						$("#tr_add").closest("tr").after(trr);
					}
				}
			}
		})
	}
	function confirmm(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	function del()
	{
		$.post("pages/clinical_procedures_ajax.php",
		{
			id:$("#idl").val(),
			type:"delete_clinical_procedures",
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
		var r_row=$(".res_row");
		//alert(r_row.length);
		var chk=1;
		var all_res="";
		
		var t_rate=parseInt($("#rate").val());
		var r_row=$(".res_row");
		var s_rate=0;
		var rt=0;
		
		for(var i=0; i<r_row.length; i++)
		{
			if($(".res_row:eq("+i+")").find('td:eq(0) select:first').val()==0)
			{
				$(".res_row:eq("+i+")").find('td:eq(0) select:first').focus();
				return true;
			}
			if($(".res_row:eq("+i+")").find('td:eq(1) input:first').val().trim()=="")
			{
				$(".res_row:eq("+i+")").find('td:eq(1) input:first').focus();
				return true;
			}
			
			if($(".res_row:eq("+i+")").find('td:eq(1) input:first').val()=="")
			{
				rt=0;
			}
			else
			{
				rt=parseInt($(".res_row:eq("+i+")").find('td:eq(1) input:first').val());
			}
			s_rate+=rt;
		}
		if($("#head").val()=="0")
		{
			$("#head").focus();
		}
		else if($("#name").val().trim()=="")
		{
			$("#name").focus();
		}
		else if($("#grade").val()=="0")
		{
			$("#grade").focus();
		}
		else if($("#rate").val().trim()=="")
		{
			$("#rate").focus();
		}
		/*
		else if($("#res_typ").val()=="0")
		{
			$("#res_typ").focus();
		}
		else if($("#res_amt").val().trim()=="")
		{
			$("#res_amt").focus();
		}
		*/
		else if(r_row.length==0)
		{
			$("#pro_tbl").css({'opacity':'0.5'});
			$("#msgg").text("Select Resourses.");
			$("#msgg").fadeIn(500);
			setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#pro_tbl").css({'opacity':'1.0'});})},800);
		}
		else if(t_rate!=s_rate)
		{
			$(".res_row").find('td:eq(1) input:first').css("border","1px solid #FF0000");
		}
		else
		{
			//alert("ok");
			$(".res_row").find('td:eq(1) input:first').css("border","");
			for(var i=0; i<r_row.length; i++)
			{
				all_res+=$(".res_row:eq("+i+")").find('td:eq(0) select:first').val()+"@@"+$(".res_row:eq("+i+")").find('td:eq(1) input:first').val().trim()+"@@#@#";
			}
			//alert(all_res);
			$("#sav").attr("disabled",true);
			$.post("pages/clinical_procedures_ajax.php",
			{
				id:$("#id").val(),
				head:$("#head").val(),
				name:$("#name").val().trim(),
				grade:$("#grade").val(),
				rate:$("#rate").val().trim(),
				all_res:all_res,
				usr:$("#user").text().trim(),
				type:"save_clinical_procedures",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					$("#sav").attr("disabled",false);
					//$(".i_rem").css("display","none");
					$(".i_rem").click();
					$("#tr_add").hide();
					clrr();
				}, 1000);
			})
		}
	}
	function clrr()
	{
		$("#id").val('');
		$("#head").val('0');
		$("#grade").val('0');
		$("#name").val('');
		$("#rate").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$(".i_rem").click();
		$("#tr_add").hide();
		$("#head").focus();
		load_clinical_procedures();
	}
	function view_list()
	{
		url="pages/print_ot_procedure.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
