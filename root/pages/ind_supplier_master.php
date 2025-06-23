<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Supplier Master </span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Name <input type="text" id="id" style="display:none;" /></td>
				<td><input type="text" id="name" class="span4" placeholder="Name" onkeyup="tab(this.id,event)" autofocus /> <b style="color:#e00;">*</b></td>
			</tr>
			<tr>
				<td>Contact No</td>
				<td><input type="text" id="contact" class="span4" maxlength="10"  placeholder="Contact" /> <b style="color:#e00;">*</b></td>
<!--
				<td><input type="text" id="contact" class="span4" maxlength="10" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" placeholder="Contact" /> <b style="color:#e00;">*</b></td>
-->
			</tr>
			<tr>
				<td>Contact Person</td>
				<td><input type="text" id="cperson" class="span4" onkeyup="tab(this.id,event)" placeholder="Contact Person" /> <b style="color:#e00;">*</b></td>
			</tr>
			<tr>
				<td>Email</td>
				<td><input type="text" id="email" class="span4" onkeyup="tab(this.id,event)" placeholder="Email" /></td>
			</tr>
			<tr>
				<td>FAX</td>
				<td><input type="text" id="fax" class="span4" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');tab(this.id,event)" placeholder="Fax" /></td>
			</tr>
			<tr>
				<td>Address</td>
				<td><textarea id="addr" placeholder="Address" class="span4" onkeyup="tab(this.id,event)" style="resize:none;"></textarea></td>
			</tr>
			<tr>
				<td>GST No</td>
				<td><input type="text" id="txtgstno" class="span4" onkeyup="tab(this.id,event)" placeholder="GST NO" /></b></td>
			</tr>
			
			<tr style="display:none;">
				<td>Bank Name</td>
				<td>
				<select name="selectsubstr" id="selectbank" class="span3" autofocus>
					<option value="0">Select Bank</option>
					<?php
						$qsplr=mysqli_query($link,"select bank_id,bank_name from banks order by bank_name");
						while($qsplr1=mysqli_fetch_array($qsplr))
						{
					?>
						<option value="<?php echo $qsplr1['bank_id'];?>"><?php echo $qsplr1['bank_name'];?></option>
					<?php
						}
					?>
				</select>
			</td>
			</tr>
			
			<tr style="display:none;">
				<td>A/C No</td>
				<td><input type="text" id="txtacno" class="span4"  placeholder="A/ C no" /></b></td>
			</tr>
			<tr style="display:none;">
				<td>Branch</td>
				<td><input type="text" id="txtbranch" class="span4"  placeholder="Branch" /></b></td>
			</tr>
			
			<tr style="display:none;">
				<td>IFSC Code</td>
				<td><input type="text" id="txtifsc" class="span4"  placeholder="IFSC Code" /></b></td>
			</tr>
			
			<tr style="display:none;">
				<td>Condition 1.</td>
				<td><input type="text" id="txtcondition" class="span4"  placeholder="Condition" /></b></td>
			</tr>
			<tr style="display:none;">
				<td>Condition 2.</td>
				<td><input type="text" id="txtcondition2" class="span4"  placeholder="Condition" /></b></td>
			</tr>
			
			<tr style="display:none;">
				<td colspan="2">
				   <label ><input type="checkbox" id="chkigst" >IGST</label>
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
	<div class="span5">
		<b>Search</b> <input type="text" id="srch" onkeyup="load_supp()" class="span4" placeholder="Search..." />
		<div id="res" style="max-height:400px;overflow-y:scroll;">
		
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
			<a data-dismiss="modal" onclick="dell()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end-->
</div>
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function()
	{
		load_supp();
		
		
		//$("#selectbank").select2({ theme: "classic" });
		//$("#selectbank").select2("focus");
		
	});
	function load_supp()
	{
		$.post("pages/global_load_g.php",
		{
			srch:$("#srch").val(),
			type:"load_ind_supplier",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function ValidateEmail(email)
    {
        var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        return expr.test(email);
	}
	function tab(id,e)
	{
		$("#name").val($("#name").val().toUpperCase());
		$("#"+id).css("border","");
		if(e.keyCode==13)
		{
			if(id=="name" && $("#"+id).val()!="")
			{
				$("#contact").focus();
			}
			else if(id=="contact" && $("#"+id).val()!="" && ($("#"+id).val()).length==10)
			{
				$("#cperson").focus();
			}
			else if(id=="cperson" && $("#"+id).val()!="")
			{
				$("#email").focus();
			}
			else if(id=="email")
			{
				$("#fax").focus();
			}
			else if(id=="fax")
			{
				$("#addr").focus();
			}
			else if(id=="addr")
			{
				$("#sav").focus();
			}
			else
			{
				$("#"+id).css("border","1px solid #e00");
			}
		}
	}
	function save()
	{
		var foc="";
		if($("#name").val()=="")
		{
			document.getElementById("name").placeholder="Cannot blank";
			$("#name").css("border","1px solid #f00");
			$("#name").focus();
			foc="name";
		}
		if($("#contact").val()=="")
		{
			$("#contact").css("border","1px solid #f00");
			$("#contact").focus();
			foc="contact";
		}
		if($("#cperson").val()=="")
		{
			$("#cperson").css("border","1px solid #f00");
			$("#cperson").focus();
			foc="cperson";
		}
		if($("#contact").val())
		{
			if(($("#contact").val().length)<10)
			{
				$("#contact").focus();
				$("#contact").css("border","1px solid #f00");
				if(foc=="")
				{
					foc="contact";
				}
			}
		}
		if($("#email").val())
		{
			if(!ValidateEmail($("#email").val()))
			{
				$("#email").focus();
				$("#email").css("border","1px solid #f00");
				if(foc=="")
				{
					foc="email";
				}
			}
		}
		if($("#fax").val())
		{
			if(($("#fax").val()).length<=10)
			{
				$("#fax").focus();
				$("#fax").css("border","1px solid #f00");
				if(foc=="")
				{
					foc="fax";
				}
			}
		}
		
		
		if($("#chkigst").prop("checked"))
		{
			var vigst=1;
		}else
		{
			var vigst=0;
		}
			
			
		if(foc!="")
		{
			$("#"+foc+"").focus();
		}
		else
		{
			$.post("pages/inv_insert_data.php",
			{
				id:$("#id").val(),
				name:$("#name").val(),
				contact:$("#contact").val(),
				cperson:$("#cperson").val(),
				email:$("#email").val(),
				fax:$("#fax").val(),
				addr:$("#addr").val().trim(),
				gstno:$("#txtgstno").val(),
				bnkid:$("#selectbank").val(),
				bnkacno:$("#txtacno").val(),
				branch:$("#txtbranch").val(),
				ifsc:$("#txtifsc").val(),
				condition:$("#txtcondition").val(),
				condition2:$("#txtcondition2").val(),
				vigst:vigst,
				
				type:"save_supplier_master",
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
	function edit(id)
	{
		$.post("pages/global_load_g.php",
		{
			id:id,
			type:"load_ind_supp_details",
		},
		function(data,status)
		{
			var vl=data.split("#gov#");
			$("input[type='text']").css("border","");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#contact").val(vl[2]);
			$("#cperson").val(vl[3]);
			$("#email").val(vl[4]);
			$("#fax").val(vl[5]);
			$("#addr").val(vl[6]);
			$("#txtgstno").val(vl[7]);
			$("#selectbank").val(vl[8]);
			$("#txtacno").val(vl[9]);
			$("#txtbranch").val(vl[10]);
			$("#txtifsc").val(vl[11]);
			$("#txtcondition").val(vl[12]);
			$("#txtcondition2").val(vl[13]);
			if(vl[14]>0)
			{
				document.getElementById("chkigst").checked=true;
			}
			else
			{
				document.getElementById("chkigst").checked=false;
			}
			
			$("#sav").val("Update");
			$("#name").focus();
		})
	}
	function del(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	function dell()
	{
		$.post("pages/global_delete_g.php",
		{
			id:$("#idl").val(),
			type:"delete_inv_supp_master",
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
		$("input[type='text']").css("border","");
		$("#id").val('');
		$("#name").val('');
		$("#contact").val('');
		$("#cperson").val('');
		$("#email").val('');
		$("#fax").val('');
		$("#addr").val('');
		$("#srch").val('');
		$("#txtgstno").val('');
		$("#selectbank").val('0');
		$("#txtacno").val('');
		$("#txtbranch").val('');
		$("#txtifsc").val('');
		$("#txtcondition").val('');
		$("#txtcondition2").val('');
		document.getElementById("chkigst").checked=false;
		
		
		$("#sav").val('Save');
		$("#name").focus();
		load_supp();
	}
</script>
<style>
	.nm:hover
	{
		color:#0000ff;
		cursor:pointer;
	}
</style>
