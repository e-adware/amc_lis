<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Components Received</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<table class="table table-bordered table-condensed">
			<tr>
				<th>Enter Barcode</th>
				<td>
					<input type="text" id="bar" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Bar Code Number" autofocus />
					<input type="button" id="srch" class="btn btn-info" onclick="show_donor()" value="Search" />
				</td>
			</tr>
			<tr>
				<th>Donor Name</th>
				<td>
					<input type="text" id="did" style="display:none;" />
					<input type="text" id="donor" placeholder="Donor Name" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<th>Bag Type</th>
				<td>
					<input type="text" id="bagid" style="display:none;" />
					<input type="text" id="bag" placeholder="Bag Type" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<th>Red blood cells</th>
				<td>
					<label><input type="checkbox" id="rbc" value="1" /> RBC</label>
				</td>
			</tr>
			<tr>
				<th>Finger Frozen Plasma</th>
				<td>
					<label><input type="checkbox" id="ffp" value="1" /> FFP</label>
				</td>
			</tr>
			<tr id="tr_plat" style="display:none;">
				<th>Platelets</th>
				<td>
					<label><input type="checkbox" id="plat" value="1" /> Platelets</label>
				</td>
			</tr>
			<tr id="tr_cpp" style="display:none;">
				<th>CPP</th>
				<td>
					<label><input type="checkbox" id="cpp" value="1" /> CPP</label>
				</td>
			</tr>
			<tr id="tr_cryo" style="display:none;">
				<th>Cryo</th>
				<td>
					<label><input type="checkbox" id="cryo" value="1" /> Cryo</label>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" class="btn btn-danger" value="Reset" onclick="clrr()" />
					<input id="sav" type="button" class="btn btn-info" value="Save" onclick="save()" />
				</td>
			</tr>
		</table>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		$("#ffp").change(function()
		{
			var c=$("#ffp:checked").length;
			if(c==1)
			{
				$("#tr_cpp").show();
				$("#tr_cryo").show();
			}
			else
			{
				$("#cpp").attr('checked',false);
				$("#cryo").attr('checked',false);
				$("#tr_cpp").hide();
				$("#tr_cryo").hide();
			}
		});
		$("#bar").keyup(function(e)
		{
			$("#did").val('');
			$("#donor").val('');
			$("#bagid").val('');
			$("#bag").val('');
			$("#donor").css("border","");
			$("#donor").attr("placeholder","Donor Name");
			if(e.keyCode==13)
			{
				if($(this).val()!="")
				$("#srch").focus();
			}
		});
	});
	function show_donor()
	{
		$.post("pages/global_load_g.php",
		{
			bar:$("#bar").val(),
			type:"blood_donor_components",
		},
		function(data,status)
		{
			if(data=="1")
			{
				alert("Already Received From Bar Code "+$("#bar").val());
				clrr();
			}
			else if(data=="2")
			{
				alert($("#bar").val()+" Bar Code Rejected");
				clrr();
			}
			else if(data=="")
			{
				alert("No data Found");
				clrr();
			}
			else
			{
				var vl=data.split("#@#");
				$("#did").val(vl[0]);
				$("#donor").val(vl[1]);
				$("#bagid").val(vl[2]);
				$("#bag").val(vl[3]);
				show_tr(vl[2]);
				$("#abo").focus();
			}
		})
	}
	function show_tr(bag)
	{
		if(bag==1)
		{
			$("#tr_plat").hide();
		}
		else if(bag==2 || bag==3 || bag==4)
		{
			$("#tr_plat").show();
		}
	}
	function save()
	{
		var rbc="";
		var ffp="";
		var plat="";
		var cpp="";
		var cryo="";
		
		if($("#rbc:checked").length==1)
		{
			rbc="rbc@1@1";
		}
		else
		{
			rbc="";
		}
		if($("#ffp:checked").length==1)
		{
			ffp="ffp@1@2";
		}
		else
		{
			ffp="";
		}
		if($("#plat:checked").length==1)
		{
			plat="plat@1@3";
		}
		else
		{
			plat="";
		}
		if($("#cpp:checked").length==1)
		{
			cpp="cpp@1@4";
		}
		else
		{
			cpp="";
		}
		if($("#cryo:checked").length==1)
		{
			cryo="cryo@1@5";
		}
		else
		{
			cryo="";
		}
		var all=rbc+"#@#"+ffp+"#@#"+plat+"#@#"+cpp+"#@#"+cryo+"#@#";
		//alert(all);
		if($("#did").val()=="")
		{
			$("#donor").css("border","1px solid #ff0000");
			$("#donor").attr("placeholder","Cannot Blank");
			$("#bar").focus();
		}
		else
		{
			$("#sav").attr('disabled',true);
			$.post("pages/global_insert_data_g.php",
			{
				did:$("#did").val(),
				bar:$("#bar").val(),
				bagid:$("#bagid").val(),
				all:all,
				usr:$("#user").text().trim(),
				type:"save_component_stock",
			},
			function(data,status)
			{
				$("#sav").attr('disabled',false);
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clrr();
				}, 1000);
			})
		}
	}
	function clrr()
	{
		$("#did").val('');
		$("#donor").val('');
		$("#bar").val('');
		$("#bagid").val('');
		$("#bag").val('');
		$("#rbc").attr('checked',false);
		$("#ffp").attr('checked',false);
		$("#plat").attr('checked',false);
		$("#cpp").attr('checked',false);
		$("#cryo").attr('checked',false);
		$("#tr_plat").hide();
		$("#tr_cpp").hide();
		$("#tr_cryo").hide();
		$("#bar").focus();
	}
</script>
