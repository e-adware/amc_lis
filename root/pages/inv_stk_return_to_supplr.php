<!--header-->
<html>
	<head>
		<script type="text/javascript">
	$(document).ready(function()
	{
		get_id();
		load_item();
		load_selected_item();
		
		$("#selectspplr").keyup(function(e)
		{
			$(this).css("border","");
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#txtreason").focus();
					//$('#selectcategory').select2('focus');
				}
			}
		});
		
		$("#txtreason").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#txtcntrname").focus();
		});
		
		$("#txtcntrname").keyup(function(e)
		{
			$(this).css("border","");
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					lod_batchno();
					$("#selectbatch").focus();
					
					
				}
			}
		});
		
		
		
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#button").focus();
		});
		
		$("#txtcntrname").keyup(function(e)
		{
			if(e.keyCode==27)
			$("#button4").focus();
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
			else if(unicode==27)
				{
					$("#button4").focus();
					//$("html,body").animate({scrollTop: '500px'},1000)
				}
					
			else
			{
				$.post("pages/inv_load_data_ajax.php",
				{
					val:val,
					type:"indent_order",
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
	
	function reset_all()
	{
		clearr();
		$("#selectsubstr").attr("disabled",false);
		$("#selectsubstr").focus();
		$("#tstissueto").focus();
	}	
	
	function load_item()
	{
		$("#loader").show();
		$.post("pages/inv_load_data_ajax.php",
		{
			type:"indent_order",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_materil").html(data);
		})
	}
	
	
	function load_selected_item()
	{
		
		$.post("pages/inv_load_data_ajax.php",
		{
			type:"invitemretruntospplr_tmp",
			rtrnno:$("#txtordo").val(),
			spplrid:$("#selectspplr").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_select").html(data);
		})
	}
	
	function val_load_new()///
	{
		
		$.post("pages/inv_load_display.php",
		{
			type:"invmainstritmissue",
			itmid:$("#txtcntrname").val(),
			batchno:$("#selectbatch").val(),
		},
		function(data,status)
		{
			
			var val=data.split("@");
			$("#txtavailstk").val(val[1]);
			$("#txtmrp").val(val[2]);
			$("#txtexpiry").val(val[3]);
			$("#txtqnt").focus();
		})
	}
	
	function delete_data(itmid,btchno,rtrnno,splrid)
	{
		
		$.post("pages/inv_load_delete.php",
		{
			type:"invitemretruntospplr_tmp",
			itmid:itmid,
			btchno:btchno,
			rtrnno:rtrnno,
			splrid:splrid,
		},
		function(data,status)
		{
			alert("Deleted");
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
		var supplr=document.getElementById("selectspplr").value;
		if(supplr==0)
		{
			alert("Please select a Supplier name..");
			$("#selectspplr").focus();
			jj=0;
		}
		
		var stkqnt=parseInt($("#txtavailstk").val());
		var isuqnt=parseInt($("#txtqnt").val());
		if(isuqnt>stkqnt)
		{
			alert("Issue quantity Can not be greater than Stock quantity");
			$("#txtqnt").focus();
		    jj=0;	
		}
		if(jj==1)
		{   
		///////end ////////
		
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"invitemretruntospplr_tmp",
			  date:$("#txtdate").val(), 
			  spplrid:$("#selectspplr").val(), 
			  returnno:$("#txtordo").val(),
			  reason:$("#txtreason").val(),
			  itmcode:$("#txtcntrname").val(),
			  btchno:$("#selectbatch").val(),
			  rtrnqnt:$("#txtqnt").val(),
			  expiry:$("#txtexpiry").val(),
			  
			 
		  },
		  function(data,status)
		   {
			 	   
			   alert("Item Added");
			   $("#selectsubstr").attr("disabled",true);
			   load_selected_item();
			   clearr();
			   $("#txtcntrname").focus();
		   })
	}}
	
	function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
		
		var supplr=$("#selectspplr").val();
		if(supplr==0)
		{
			jj=0;
			alert("Please select a Supplier name..");
			$("#selectspplr").focus();
		}
		
		if(jj==1)
		{   
		///////end ////////
		
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"invitemretruntospplr_final",
			  date:$("#txtdate").val(), 
			  spplrid:$("#selectspplr").val(), 
			  returnno:$("#txtordo").val(),
			  reason:$("#tstissueto").val(),
			  
			 
		  },
		  function(data,status)
		   {
			 		  
			  alert ("Data Saved");
			   $("#selectsubstr").attr("disabled",true);
		 
			   clearr();
			   load_selected_item();
			   get_id();
		   })
	}}
	
	function get_id() //For Get Id
	{
		$.post("pages/load_id.php",
		{
			type:"invitmrtrntospplr",
		},
		function(data,status)
		{
			//alert(data);
			$("#txtordo").val(data);
		})
	}
	
	function popitup1(url)
	{
		var substrid=$("#selectsubstr").val();
		var orderno=$("#txtordo").val();
		var orderdate=$("#txtorddate").val();
		
		url=url+"?substrid="+substrid+"&orderno="+orderno+"&orderdate="+orderdate;
		newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
		$("#button5").attr("disabled",true);
		get_id();
	}
	
	
	function lod_batchno()
	{
		$.post("pages/inv_load_display.php",
		{
			type:"mainstrbatchload",
			prdctid:$("#txtcntrname").val(),
		},
		function(data,status)
		{
			$("#chk").val("0");	
			document.getElementById("selectbatch").options.length=1;
			var data=data.split("#");
			for(var i=0;i<data.length-1;i++)
			{
				var opt = document.createElement("option");
				var ip=document.getElementById("selectbatch").options.add(opt);
				var dvalue=data[i].split("@");
				for(var j=0;j<dvalue.length;j++)
				{
					opt.value=dvalue[0];
					opt.text=dvalue[1];
				}
			}
		})	
		doc_v=1;
		doc_sc=0;
	}
	
	
</script>
</head>

<body >
	
<div id="content-header">
    <div class="header_div"> <span class="header"> Item Return to Supplier </span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		
		<form id="form1" method="post">
			<div class="span11" style="margin-left:0px;">
				<table class="table table-bordered table-condensed">
					<tr>
						<td> Date</td>
						<td>
							
							<input type="text" id="txtdate" name="txtdate" value="<?php echo date('Y-m-d');?>" onfocus="jsdate('txtorddate')" readonly/>
						</td>
					</tr>
					<tr>
						<td>Select Supplier</td>
						<td >
							<select name="selectspplr" id="selectspplr"  autofocus>
								<option value="0">---Select Supplier---</option>
								<?php
									$qsplr=mysqli_query($link,"select id,name from inv_supplier_master order by name");
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
						<td>Return No</td>
						<td>
							<input type="text" name="txtordo" id="txtordo" size="20" value="" class="imp" readonly="readonly" />
						</td>
					</tr>
					
					<tr>
						<td>Reason</td>
						<td>
							<input type="text" name="txtreason" id="txtreason"   class="imp"  />
						</td>
					</tr>
									
					
					<tr>
					   <td>Item Name </td>
					   <td colspan="2">
						<input list="browsrs" type="text" name="txtcntrname"  id="txtcntrname"  autocomplete="off" class="intext span4"/>
						<datalist id="browsrs">
						<?php
                        $tstid=0; 
						$pid = mysqli_query($link," SELECT 	item_id,item_name FROM `item_master` order by `item_name` ");
						while($pat1=mysqli_fetch_array($pid))
						{
						  echo "<option value='$pat1[item_name]-#$pat1[item_id]'>$pat1[item_name]";
						  
						  
						}
						?>
						</datalist>
						</td>
					</tr>
					
					<tr>
						<td>Batch No </td>
						<td>
							<select name="select" id="selectbatch" onchange="val_load_new();" onkeyup="next_tab(this.id,event)">
								<option value="0">--Select BatchNo--</option>
							</select>
						</td>
					</tr>

							
							
					
					<tr>
						<td>Quantity  </td>
						<td>
							<input type="text" name="txtqnt" id="txtqnt"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" placeholder="Quantity" style="width:100px" />
							 &nbsp;&nbsp; Stock In Hand .<input type="text" name="txtavailstk" id="txtavailstk"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" placeholder="avail Quantity" style="width:100px" readonly />
							  &nbsp;&nbsp;MRP .<input type="text" name="txtmrp" id="txtmrp"  autocomplete="off" class="imp intext" onkeyup="numentry('txtmrp')" placeholder="MRP" style="width:100px"  />
							   &nbsp;&nbsp;Expiry .<input type="text" name="txtexpiry" id="txtexpiry"  autocomplete="off" class="imp intext"  placeholder="Expiry" style="width:100px" readonly />
						</td>
					</tr>
					
					<tr>
						<td colspan="4" style="text-align:center">
							<input type="button" name="button2" id="button2" value="Reset" onclick="reset_all();" class="btn btn-danger" /> 
							<input type="button" name="button" id="button" value= "Add" onclick="insert_data()" class="btn btn-default" />
							<input type="button" name="button4" id="button4" value= "Done" onclick="insert_data_final()" class="btn btn-default" />
							<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/inv_indent_order_rpt.php')" class="btn btn-success" disabled />-->
						</td>
					</tr>
					
				</table>
			</div>
			
			
			<div class="span11">
				<div id="load_select" class="vscrollbar" style="max-height:250px;overflow-y:scroll;" >
					
				</div>
			</div>
		</form>
		<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
		<div id="loader" style="display:none;top:50%;position:fixed;"></div>
</div>
</body>
</html>
