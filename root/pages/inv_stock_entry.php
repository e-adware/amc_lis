<?php
	$fid=mysqli_fetch_array(mysqli_query($link,"select max(FID) as maxfid from ph_financialyear_master "));
	
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Stock Entry</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<form id="form1" method="post">
			<div class="span12" style="margin-left:0px;">
				<input type="text" id="fid" name="fid" value="<?php echo $fid['maxfid'];?>" style="display:none;" />
				<table class="table table-bordered table-condensed" >
					<tr>
						<td>Supplier </td>
						<td>
							<select name="selectspplr" id="selectspplr" class="span2"  autofocus>
								<option value="0">---Select Supplier---</option>
								<?php
									$qsplr=mysqli_query($link,"select id,name from inv_supplier_master order by name");
									
									while($qsplr1=mysqli_fetch_array($qsplr))
									{
										if($_POST['selectspplr']==$qsplr1['id']){$ssel="Selected='selected'";} else { $ssel="";} 
									 ?>
								<option value="<?php echo $qsplr1['id'];?>"><?php echo $qsplr1['name'];?></option>
								<?php
									}?>
							</select>
							
						</td>
					</tr>
					
					<tr>
						<td>Receipt No</td>
						<td>
							<input type="text" id="txtrcptno" name="txtrcptno" class="span4" readonly="readonly" />
						</td>
					</tr>
										
					
					<tr>
						<td>Supplier Bill No</td>
						<td>
							<input type="text" id="txtspblno" name="txtspblno" placeholder="Supplier Bill No" />
						</td>
					</tr>
					
					<tr>
					   <td>Item Name </td>
					   <td colspan="2">
						<input list="browsrs" type="text" name="txtcntrname"  id="txtcntrname"  autocomplete="off" class="intext span4"/>
						<datalist id="browsrs">
						<?php
                        $tstid=0; 
						$pid = mysqli_query($link," SELECT 	id,name FROM `inv_indent_master` order by `name` ");
						while($pat1=mysqli_fetch_array($pid))
						{
						  echo "<option value='$pat1[name]-$pat1[id]'>$pat1[name]";
						  
						  
						}
						?>
						</datalist>
						</td>
					</tr>
                    
                    <tr>
						<td>MRP </td>
						<td>
							 <input type="text" name="txtmrp" id="txtmrp"  autocomplete="off" class="imp intext span2" onkeyup="numentry('txtmrp')" placeholder="MRP" />
							 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; GST <input type="text" name="txtgst" id="txtgst"  autocomplete="off" class="imp intext span2" onkeyup="numentry('txtgst')" placeholder="GST" /> %
							 
						</td>
					</tr>
					
                    <tr>
						<td>Quantity</td>
						<td>
							 <input type="text" name="txtqnt" id="txtqnt"  autocomplete="off" class="imp intext span2"  placeholder="Quantity" onkeyup="cal_mrp(this.value,event)"  />
							 &nbsp; &nbsp; &nbsp; Amount <input type="text" name="txtitmamt" id="txtitmamt"  autocomplete="off" class="imp intext span2" onkeyup="numentry('txtitmamt')" placeholder="Item Amount" />
						</td>
					</tr>
                    
                    
					
					<tr>
						<td>GST Amount</td>
						<td>
							 <input type="text" name="txtgstamount" id="txtgstamount"  autocomplete="off" class="intext span2"  placeholder="GST Amount" /> 
							 Bill Amount <input type="text" name="txtbillamnt" id="txtbillamnt"  autocomplete="off" class="span2" onkeyup="numentry('txtbillamnt')" placeholder="Bill Amount" />
						</td>
					</tr>
					
					 
					
										
					<tr>
						<td colspan="4" style="text-align:center">
							<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" /> 
							<input type="button" name="button" id="button" value= "Add" onclick="insert_data()" class="btn btn-default" />
							<input type="button" name="button4" id="button4" value= "Done" onclick="insert_data_final()" class="btn btn-default" />
							
						</td>
					</tr>
					
				</table>
			</div>
		
			
			<div id="load_select" class="vscrollbar span11" style="max-height:250px;overflow-y:scroll;" >
				
			</div>
		</div>
</form>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<script type="text/javascript">
	$(document).ready(function()
	{
		load_id();
		load_item();
		$("#selectspplr").focus();
		
		$("#selectspplr").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#txtspblno").focus();
				}
			}
		});
		
		$("#txtspblno").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#txtcntrname").focus();
			}
		});
		
		$("#txtcntrname").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#txtqnt").focus();
				val_load_new();
			}
		});
		
		
		$("#txtcntrname").keyup(function(e)
		{
			if(e.keyCode==27 )
			{
				$("#button4").focus();
							
			}
		});
		
		
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#button").focus();
							
			}
		});
		
		$("#txtitmamt").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#txtbillamnt").focus();
				
				
			}
		});
		
		$("#txtbillamnt").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#button").focus();
				calc_qnty();
				
			}
		});
		
		
	});
	
	
	
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
						$("#load_materil").scrollTop(doc_sc)
						
					}
				}	
		
			}
			else
			{
					$.post("pages/ph_load_data_ajax.php",
					{
						val:val,
						type:"loadphitmdirct",
					
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
		if(!num.value.match(numex))
		{
			num.value="";
		}
	}
	
	function cal_mrp(val,e)
	{
		
		var scst=$("#txtmrp").val();
		var b=$("#txtqnt").val();
		var e=0;
		var c=scst*b;
		c=c.toFixed(2);
        e=c;
        
        
		$("#txtitmamt").val(e);
		
	}
	function load_id()
	{
		$.post("pages/load_id.php",
		{
			type:"invstkentry",
		},
		function(data,status)
		{			
			$("#txtrcptno").val(data);
		})
	}
	
	
	
	
  function calc_totalamount() ///for calculation
	{
		
			
		$.post("pages/inv_load_display.php",
		{
			type:"invloadrcvdttlamt",
			orderno:$("#txtrcptno").val(),
			billno:$("#txtspblno").val(),
			
		},
		function(data,status)
		{
			//alert(data);
			var val=data.split("@");
			$("#txtgstamount").val(val['0']);
			$("#txtbillamnt").val(val['1']);
			
			
		})
	}
	
	
	
	function clearr()//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";	
		} 
		
		$("#txtcustnm").val("");
		$("#txtcntrname").focus();
		
	} 

	
	function load_item()
	{
				
			$.post("pages/ph_load_data_ajax.php",
			{
				type:"loadphitmdirct",
				//orderno:document.getElementById("selectorder").value,
			},
			function(data,status)
			{				
				$("#load_materil").html(data);
			})
		
	}
		
	function load_selected_item()
	{
		
		$.post("pages/inv_load_data_ajax.php",
		{
			type:"load_mainstkentry_tmp",
			orderno:$("#txtrcptno").val(),
			splrid:$("#selectspplr").val(),
			billno:$("#txtspblno").val(),
		},
		function(data,status)
		{
			$("#load_select").html(data);
		})
	}
	
	function val_load_new(id)///for retrieve data against center
	{
		
		
		$.post("pages/inv_load_display.php",
		{
			type:"invloadphitmdirct",
			id:$("#txtcntrname").val(),
			
		},
		function(data,status)
		{
           //alert(data);
			$("#loader").hide();
			var val=data.split("@");
				
			$("#txtmrp").val(val['1']);
			$("#txtgst").val(val['2']);
			$("#txtqnt").focus();
			
		})
	}
	
	function delete_data(orderno,billno,itmid)
	{
		
		$.post("pages/inv_load_delete.php",
		{
			type:"mainstkentry_tmp",
			orderno:orderno,
			billno:billno,
			itmid:itmid,
			
		},
		function(data,status)
		{
			
			alert(data);
			load_selected_item();
			calc_totalamount();
			
		})
	}
	
	function insert_data()
	{
		///////For Check Blank fields//
		var jj=1;
		var chk=document.getElementsByClassName("imp");
		for(var i=0;i<chk.length;i++)
		if(chk[i].value=="")
		{
			jj=0;
			document.getElementById(chk[i].id).placeholder="Can not be Blank";	
		}
		
		var spplr=document.getElementById("selectspplr").value;
		if(spplr==0)
		{
			alert("Please select a Supplier...");
			jj=0;
			$("#selectspplr").focus();
		}
		
		var blno=document.getElementById("txtspblno").value;
		if(blno=="")
		  {
			  alert("Please enter the Bill No..");
			  jj=0;
			  
		  }
		  
		if($("#txtitmamt").val()=="")
		{
		   alert("Please enter the Item Amount");
		   jj=0;
		}
		
		if(jj==1)
		{
			
			$.post("pages/inv_insert_data.php",
			{
					type:"mainstkentry_tmp",
					fid:0,
					spplrid:$("#selectspplr").val(),
					orderno:$("#txtrcptno").val(),
					billno:$("#txtspblno").val(),
					itmid:$("#txtcntrname").val(),

					qnt:$("#txtqnt").val(),
					freeqnt:0, 
					itmamt:$("#txtitmamt").val(),
					mrp:$("#txtmrp").val(),
					gst:$("#txtgst").val(),
					rate:0,
				
			},
			function(data,status)
			{
				
				alert("Item Added");
				//$("#selectorder").attr("disabled",true);
				load_selected_item();
				calc_totalamount();
				clearr(); 
				
			})
		}
	}
	
	function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
		var chk1=document.getElementsByClassName("imp1");
		for(var k=0;k<chk1.length;k++) 
		if(chk1[k].value=="")
		{
			jj=0;
			document.getElementById(chk1[k].id).placeholder="Can not be blank";
		}
		
		var spplr=document.getElementById("txtrcptno").value;
		if(spplr==0)
		{
			jj=0;
			alert("Please select a Supplier Name..");
		}
		var splrblno=document.getElementById("txtspblno").value;
		if(splrblno=="")
		  {
			 jj=0;
			 alert("Please enter the supplier bill No..");
		 }
		
		if($("#txtbillamnt").val()=="")
		{
			alert("Please enter the Total Bill Amount");
			jj=0;
			$("#txtbillamnt").focus();
		}
		
		if(jj==1)
		{   
		///////end ////////
		
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"mainstkentry_final",
			  spplrid:$("#selectspplr").val(),
			  orderno:$("#txtrcptno").val(),
			  fid:0,
			  splrblno:$("#txtspblno").val(),
			  billamt:$("#txtbillamnt").val(),
			  gstamt:$("#txtgstamount").val(),
			  
		  },
		  function(data,status)
		   {
			   alert("Done");
			   location.reload(1000);
		   })
		}
	}
	
	function popitup1(url)
	{
		var shopcode=document.getElementById("selectshop").value;
		
		url=url+"?shopcode="+shopcode;
		newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
	}
	
</script>
