<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Purchase Order</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<?php
			//$fid=mysqli_fetch_array(mysqli_query($link,"select max(FID) as maxfid from ph_financialyear_master "));
		?>
		<form id="form1" method="post">
			<div class="span5" style="margin-left:0px;">
				<table class="table table-bordered table-condensed">
					<tr>
						<td>Order Date</td>
						<td>
							<input type="text" id="fid" name="fid" value="<?php //echo $fid['maxfid'];?>" style="display:none;" />
							<input type="text" id="txtorddate" name="txtorddate" value="<?php echo date('Y-m-d');?>" onfocus="jsdate('txtorddate')"/>
						</td>
					</tr>
					<tr>
						<td>Supplier</td>
						<td >
							<select name="selectsupplr" id="selectsupplr" autofocus>
								<option value="0">---Select Supplier---</option>
								<?php
									$qsplr=mysqli_query($link,"select id,name from ph_supplier_master order by name");
									while($qsplr1=mysqli_fetch_array($qsplr))
									{if($_POST['id']==$qsplr1['id']){$ssel="Selected='selected'";} else { $ssel=" ";}
								?>
									<option value="<?php echo $qsplr1['id'];?>"><?php echo $qsplr1['name'];?></option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Order No</td>
						<td>
							<input type="text" name="txtordo" id="txtordo" size="20" value="" class="imp" readonly="readonly" />
							<input type="text" name="prord" id="prord" style="display:none;" />
						</td>
					</tr>
					<tr>
						<td>ID</td>
						<td>
							<input type="text" name="txtcid" id="txtcid" size="15" value="" readonly="readonly" class="imp intext" placeholder="Item Id"/>
						</td>
					</tr>
					<tr>
						<td>Name</td>
						<td>
							<input type="text" name="txtcntrname"  id="txtcntrname" value="" size="40" autocomplete="off" class="imp intext" readonly="readonly" placeholder="Name"/>
						</td>
					</tr>
					<tr>
						<td>Quantity  </td>
						<td>
							<input type="text" name="txtqnt" id="txtqnt"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" placeholder="Quantity" />
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:center">
							<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" /> 
							<input type="button" name="button" id="button" value= "Add" onclick="insert_data()" class="btn btn-default" />
							<input type="button" name="button4" id="button4" value= "Done" onclick="insert_data_final()" class="btn btn-default" />
							<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/purchase_order_rpt.php')" class="btn btn-success" disabled />
						</td>
					</tr>
				</table>
			</div>
			<div class="span5">
				<table class="table table-bordered table-condensed">
					<tr>
						<td>Item Search</td>
						<td> <input type="text" name="txtcustnm" size="30" id="txtcustnm"  autocomplete="off" class="intext" onkeyup="sel_pr(this.value,event)" placeholder="Search..." /></td>
					</tr>
				</table>
				<div id="load_materil" class="vscrollbar" style="max-height:280px;overflow-y:scroll;" >
					
				</div>
			</div>
			<div class="span11">
				<div id="load_select" class="vscrollbar" style="max-height:250px;overflow-y:scroll;" >
					
				</div>
			</div>
		</form>
		<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
		<div id="loader" style="display:none;top:50%;position:fixed;"></div>
</div>
<script type="text/javascript">
	$(document).ready(function()
	{
		get_id();
		load_item();
		load_selected_item
		
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#button").focus();
		});
	});
	function jsdate(id)
	{
		new JsDatePick
		({
			useMode:2,
			target:id,
			dateFormat:"%Y/%m/%d"
		});
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
						$("#load_materil").scrollTop(doc_sc)
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
						$("#load_materil").scrollTop(doc_sc);
					}
				}
			}
			else
			{
				$.post("pages/global_load_g.php",
				{
					val:val,
					type:"purchase_item_list",
				},
				function(data,status)
				{
					$("#load_materil").html(data);
				})
			}
	}
	
	
	function numentry(id) //for Numeric value support in the text field
	{
		var num=document.getElementById(id);
		
		var numex=/^[0-9]+$/;
		//var nume=/a-z/
		if(!num.value.match(numex))
		{
			num.value="";
		}
	}
	
	
	function jsdate(id)
	 {
		new JsDatePick
		({
			useMode:2,
			target:id,
			dateFormat:"%Y/%m/%d"
		});
	 }
			 
	function clearr()//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";	
		}
		//$("#selectsupplr").val('0');
		get_id();
	}
		
	function load_item()
	{
		$("#loader").show();
		$.post("pages/global_load_g.php",
		{
			type:"purchase_item_list",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_materil").html(data);
		})
	}
	
	
	function load_selected_item()
	{
		$("#loader").show();
		$.post("pages/global_load_g.php",
		{
			type:"purchse_ord_tmp",
			orderno:document.getElementById("txtordo").value,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_select").html(data);
		})
	}
	
	function val_load_new(id)///for retrieve data against center
	{
		$("#loader").show();
		$.post("pages/global_load_g.php",
		{
			type:"purchase_load_item",
			id:id,
		},
		function(data,status)
		{
			$("#loader").hide();
			var val=data.split("@");
			document.getElementById("txtcid").value=val[0];
			document.getElementById("txtcntrname").value=val[1];	
			document.getElementById("txtqnt").focus();
		})
	}
	
	function delete_data(itmid,orderno)
	{
		$("#loader").show();
		$.post("pages/global_delete_g.php",
		{
			type:"purchase_order_temp_del",
			itmid:itmid,
			orderno:orderno,
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			load_selected_item();
		})
	}
	
	function insert_data()
	{
		///////For Check Blank fields//
		var jj=1;
		var chk=document.getElementsByClassName("imp")
		for(var i=0;i<chk.length;i++)
		if(chk[i].value=="")
		{
			jj=0;
			document.getElementById(chk[i].id).placeholder="Can not be Blank";	
		}
		var supplr=document.getElementById("selectsupplr").value;
		if(supplr==0)
		{
			alert("Please select a supplier name..");
			document.getElementById("selectsupplr").focus();
			jj=0;
		}
		
		if(jj==1)
		{   
		///////end ////////
		$("#loader").show();
		 $.post("pages/ph_insert_data.php",
		  {
			  type:"purchse_ord_temp",
			  ordrdate:document.getElementById("txtorddate").value, 
			  supplr:document.getElementById("selectsupplr").value, 
			  orderno:document.getElementById("txtordo").value,
			  itmcode:document.getElementById("txtcid").value,
			  orqnt:document.getElementById("txtqnt").value,
			  fid:0,
		  },
		  function(data,status)
		   {
			   $("#loader").hide();
			   //alert(data);
			   $("#selectsupplr").attr("disabled",true);
			   load_selected_item();
			   load_item();
			   clearr();
		   })
	}}
	
	function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
		
		var supplr=document.getElementById("selectsupplr").value;
		if(supplr==0)
		{
			jj=0;
			alert("Please select a supplier name..");
			document.getElementById("selectsupplr").focus();
		}
		
		if(jj==1)
		{   
		///////end ////////
		$("#prord").val($("#txtordo").val());
		$("#loader").show();
		 $.post("pages/ph_insert_data.php",
		  {
			  type:"purchse_ord_final",
			  supplr:document.getElementById("selectsupplr").value, 
			  orderno:document.getElementById("txtordo").value,
			  ordrdate:document.getElementById("txtorddate").value, 
			  fid:0,
		  },
		  function(data,status)
		   {
			   $("#loader").hide();
			   alert(data);
			   $("#button5").attr("disabled",false);
			   $("#selectsupplr").attr("disabled",false);
			   //alert ("Data Saved");
			   load_selected_item();
			   get_id();
			   //setTimeout("location.reload(true);",1000);
			   //location.reload(1000);
		   })
	}}
	
	function get_id() //For Get Id
	{
		$.post("pages/global_load_g.php",
		{
			type:"purchase_order_id",
		},
		function(data,status)
		{
			$("#txtordo").val(data);
		})
	}
	
	function popitup1(url)
	{
		var supplr=document.getElementById("selectsupplr").value;
		var orderno=document.getElementById("prord").value;
		
		url=url+"?supplr="+supplr+"&orderno="+orderno;
		newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
		$("#button5").attr("disabled",true);
	}
	
</script>

