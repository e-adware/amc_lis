<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Pharmacy Generic Name </span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			
				
			<tr>
				<td> Id</td>
				<td>
					<input type="text" id="txtid" readonly="readonly" class="span3" />
				</td>
			</tr>
			
			<tr>
				<td>Generic Name</td>
				<td><input type="text" id="name" class="span3" placeholder="category" autocomplete="off" autofocus /></td>
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
		<b>Search</b> <input type="text" id="srch" onkeyup="sel_pr(this.value,event)" class="span4" placeholder="Search..." />
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
<script>
	$(document).ready(function()
	{
		load_id();
		load_type();
		
		$("#name").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#sav").focus();
			}
		});
	});
	
	function load_id()
	{
		$.post("pages/ph_load_id.php",
		{
			type:"phcategory",
		},
		function(data,status)
		{
			
			$("#txtid").val(data);
		})
	}
	
	function save()
	{
		var jj=1;
		var catid=$("#subcatid").val();
		var name=$("#name").val();
		if(catid==0)
		{
			alert("Please select a category Name..");
			jj=0;
		}
		
		if(name=="")
		{
			alert("Please Enter the Name..");
			$("#name").focus();
			jj=0;
		}
		if(jj==1)
		{
			$.post("pages/ph_insert_data.php",
			{
				type:"phcategory",
				
				id:$("#txtid").val(),
				name:$("#name").val(),
			
			},
			function(data,status)
			{
				alert("Data Saved");
				clrr();
				load_type();
				
			})
		}
	}
	
	function clrr()
	{
		load_id();
		
		$("#name").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#name").focus();
		load_type();
	}
	
	function load_type()
	{
		$.post("pages/ph_load_data_ajax.php",
		{
			srch:$("#srch").val(),
			type:"loadsubcatgry",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function val_load_new(id)
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			phid:id,
			type:"phcategory",
		},
		function(data,status)
		{
			var vl=data.split("@");
			$("#txtid").val(vl[0]);
			$("#name").val(vl[1]);
			
			
			$("#sav").val("Update");
			$("#name").focus();
		})
	}
	
	function del(sl)
	{
		$("#dl").click();
		$("#idl").val(sl);
	}
	
	function delete_data(catid)
	{
		$.post("pages/ph_load_delete.php",
		{
			phcatid:catid,
			type:"phcategory",
		},
		function(data,status)
		{
			alert("Data Deleted");
			clrr();
			
		})
	}
	
	
	var doc_v=1;
	var doc_sc=0;
	function sel_pr(val,e) ///for load patient
	 
	 {
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				var chk=$("#chk").val();
				if(chk!="0")
				{
				var prod=document.getElementById("prod"+doc_v).innerHTML;
				val_load_new(prod);
				}
			}
			else if(unicode==40)
			{
				$("#chk").val("1");
				var chk=doc_v+1;
				var cc=document.getElementById("rad_test"+chk).innerHTML;
				if(cc)
				{
					doc_v=doc_v+1;
					$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'red','transform':'scale(0.95)','transition':'all .2s'});
					var doc_v1=doc_v-1;
					$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
					var z2=doc_v%3;
					if(z2==0)
					{
						$("#res").scrollTop(doc_sc)
						doc_sc=doc_sc+90;
					}
				}	
				
			}
			else if(unicode==38)
			{
				$("#chk").val("1");
				var chk=doc_v-1;
				var cc=document.getElementById("rad_test"+chk).innerHTML;
				if(cc)
				{
					doc_v=doc_v-1;
					$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'red','transform':'scale(0.95)','transition':'all .2s'});
					var doc_v1=doc_v+1;
					$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
					var z2=doc_v%3;
					if(z2==0)
					{
						doc_sc=doc_sc-90;					
						$("#res").scrollTop(doc_sc);
					}
				}
			}
			else
			{
				$.post("pages/inv_load_data_ajax.php",
				{
					val:val,
					type:"loadsubcatgry",
				},
				function(data,status)
				{
					$("#res").html(data);
				})
			}
	}
	
	
</script>
<style>
	.nm:hover
	{
		color:#0000ff;
		cursor:pointer;
	}
</style>
