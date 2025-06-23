<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Cabin Rate</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left: 0;">
		<table class="table table-condensed" id="pro_tbl">
			<tr>
				<th>
					Select Grade
					<input type="text" id="id" readonly="readonly" style="display:none;" />
				</th>
			</tr>
			<tr>
				<td>
					<select id="grade" class="span4" autofocus>
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT `grade_id`, `grade_name` FROM `ot_grade_master` ORDER BY `grade_name`");
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
			</tr>
			<tr>
				<td>
					<select id="cabin" class="span4">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT `ot_cabin_id`, `ot_cabin_name` FROM `ot_cabin_master` ORDER BY `ot_cabin_name`");
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
				<th>Amount</th>
			</tr>
			<tr>
				<td>
					<input type="text" id="amount" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Amount" />
				</td>
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
		<input type="text" id="srch" style="width:90%;" onkeyup="load_rates()" Placeholder="Search Cabin..." />
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

<link rel="stylesheet" href="../css/select2.min.css" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function()
	{
		load_rates();
		$("select").select2({ theme: "classic" });
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
	function load_rates()
	{
		$.post("pages/ot_cabin_rate_ajax.php",
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
		$.post("pages/ot_cabin_rate_ajax.php",
		{
			id:id,
			type:2,
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("#ea#");
			$("#id").val(vl[0]);
			$("#grade").val(vl[1]).trigger("change");
			$("#cabin").val(vl[2]).trigger("change");
			var r_amt=vl[3];
			r_amt=r_amt.split(".");
			$("#amount").val(r_amt[0]);
			$("#sav").val('Update');
			$("#amount").focus();
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
		$.post("pages/ot_cabin_rate_ajax.php",
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
			}, 1000);
		})
	}
	function save()
	{
		if($("#grade").val()=="0")
		{
			$("#grade").focus();
		}
		else if($("#cabin").val()=="")
		{
			$("#cabin").focus();
		}
		else if($("#amount").val().trim()=="")
		{
			$("#amount").focus();
		}
		else if(parseInt($("#amount").val().trim()==0))
		{
			$("#amount").focus();
		}
		else
		{
			$("#sav").attr("disabled",true);
			$.post("pages/ot_cabin_rate_ajax.php",
			{
				id:$("#id").val(),
				grade:$("#grade").val(),
				cabin:$("#cabin").val(),
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
		$("#grade").val('0').trigger("change");
		$("#cabin").val('0').trigger("change");
		$("#amount").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#grade").select2("focus");
		load_rates();
	}
	function view_list()
	{
		url="pages/print_ot_procedure.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
