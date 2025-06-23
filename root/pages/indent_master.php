<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Indent Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Category <input type="text" id="id" style="display:none;" /></td>
				<td>
					<select id="selectcategory" onchange="load_subcat()" autofocus>
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `inv_indent_type` ORDER BY `name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['inv_cate_id'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			
					
			
			<tr>
				<td>Sub Category</td>
				<td>
					<select name="select" id="selectsubcat"  onchange="load_ind()">
					<option value="0">--Select BatchNo--</option>
					</select>
				</td>
			</tr>	
						
			<tr>
				<td>ID</td>
				<td><input type="text" id="txtid" class="span3"  readonly /></td>
			</tr>
			
			<tr>
				<td>Name</td>
				<td><input type="text" id="name" class="span3" placeholder="Name" autocomplete="off" /></td>
			</tr>
			
			<tr>
				<td>Unit</td>
				<td><input type="text" id="unit" class="span3" placeholder="Unit" /></td>
			</tr>
			
			<tr>
				<td>Re-order Qnty.</td>
				<td><input type="text" id="txtreorder" class="span3" placeholder="Re Order Quantity" /></td>
			</tr>
			
			
			
			<tr>
				<td>Price.</td>
				<td><input type="text" id="txtprice" class="span3" placeholder="Price" /></td>
			</tr>
			<tr>
				<td>CGST+SGST.</td>
				<td><input type="text" id="selectgst" class="span3" placeholder="GST" /> %</td>
			</tr>
			
			
			
			<tr>
				<td>Specific Type</td>
				<td>
					<select id="sp_type">
						<option value="0">Select</option>
						<option value="1">Specific</option>
						<option value="2">Non-Specific</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" id="" class="btn btn-danger" onclick="clrr()" value="Reset" />
					<input type="button" id="" class="btn btn-success" onclick="view_item_list()" value="View" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span6">
		<b>Search</b> <input type="text" id="srch" onkeyup="load_ind()" class="span4" placeholder="Search..." />
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
		load_ind();
		
		$("#selectcategory").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				$("#selectsubcat").focus();
			}
		});
		
		$("#selectsubcat").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				$("#name").focus();
			}
		});
		
		
		$("#name").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#unit").focus();
			}
		});
		
		$("#unit").keyup(function(e)
		{
			if(e.keyCode==13 )
			{
				$("#txtreorder").focus(); 
			}
		});
		
		$("#txtreorder").keyup(function(e)
		{
			if(e.keyCode==13 )
			{
				$("#txtprice").focus();
			}
		});
		
		$("#txtprice").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#selectgst").focus();
			}
		});
		
		$("#selectgst").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#sp_type").focus();
			}
		});
		
		$("#sp_type").keyup(function(e)
		{
		if(e.keyCode==13 )
		{
		$("#sav").focus();
		}
		});
		
		
	});
	
	function save()
	{
		var jj=1;
		var catid=$("#selectcategory").val();
		var subcatid=$("#selectsubcat").val();
		var name=$("#name").val();
		if(catid==0)
		{
			alert("Please select a Category name..");
			jj=0;
		}
		
		if(subcatid==0)
		{
			alert("Please select a Sub Category name..");
			jj=0;
		}
		
		if(name==0)
		{
			alert("Please Enter the name..");
			jj=0;
		}
		
		if(jj==1)
		{
			$.post("pages/inv_insert_data.php",
			{
				type:"saveindnt",
				
				catid:$("#selectcategory").val(),
				subcatid:$("#selectsubcat").val(),
				indid:$("#txtid").val(),
				name:$("#name").val(),
				unit:$("#unit").val(),
				reorderqnty:$("#txtreorder").val(),
				stkinhnd:0,
				gstprcnt:$("#selectgst").val(),
				price:$("#txtprice").val(),
				vitmtype:$("#sp_type").val(),
				
			},
			function(data,status)
			{
				alert("Data saved");
				clrr();
			})
		}
	}
	
function load_id()
{
	$.post("pages/load_id.php",
	{
		type:"indmaster",
		},
	function(data,status)
	{
		
		$("#txtid").val(data);
		}
		
   )
}


	function load_ind()
	{
		$.post("pages/inv_load_data_ajax.php",
		{
			srch:$("#srch").val(),
			cateid:$("#selectcategory").val(),
			subcateid:$("#selectsubcat").val(),
			type:"indmaster",
		},
		function(data,status)
		{

			$("#res").html(data);
		})
	}
	
	function delete_data(id)
	{
		$.post("pages/inv_load_delete.php",
		{
			id:id,
			type:"indmaster",
		},
		function(data,status)
		{
			alert("Data Deleted");
			clrr();
			
		})
	}
	
	
	function val_load_new(id)
	{
		$.post("pages/inv_load_display.php",
		{
			id:id,
			type:"indmaster",
		},
		function(data,status)
		{
			var vl=data.split("@");
			$("#txtid").val(vl[0]);
			$("#selectcategory").val(vl[1]);
			$("#selectsubcat").val(vl[2]);
			$("#name").val(vl[3]);
			$("#unit").val(vl[4]);
			$("#txtreorder").val(vl[5]);
			$("#sp_type").val(vl[7]);
			$("#selectgst").val(vl[6]);
			$("#txtprice").val(vl[8])
			$("#sav").val("Update");
			$("#name").focus();
		})
	}
	
	
	function del(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	
	function clrr()
	{
		$("#id").val('');
		$("#ind_type").val('0');
		$("#name").val('');
		$("#unit").val('');
		$("#txtreorder").val('');
				
		$("#selectgst").val('0');
		$("#sp_type").val('0');
		$("#txtprice").val('');
		$("#srch").val('');
		$("#sav").val('Save');
		$("#name").focus();
		load_ind();
		load_id();
	}
	

function load_subcat()
	{
		$.post("pages/inv_load_display.php",
		{
			type:"subcatload",
			cateid:$("#selectcategory").val(),
		},
		function(data,status)
		{
			
			$("#chk").val("0");	
			document.getElementById("selectsubcat").options.length=1;
			var data=data.split("#");
			for(var i=0;i<data.length-1;i++)
			{
				var opt = document.createElement("option");
				var ip=document.getElementById("selectsubcat").options.add(opt);
				var dvalue=data[i].split("@");
				for(var j=0;j<dvalue.length;j++)
				{
					opt.value=dvalue[0];
					opt.text=dvalue[1];
				}
			}
		})
	}

function view_item_list(ord)
	{
		url="pages/inv_itm_list_rpt.php?orderno="+ord;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
			
</script>
<style>
	.nm:hover
	{
		color:#0000ff;
		cursor:pointer;
	}
</style>
