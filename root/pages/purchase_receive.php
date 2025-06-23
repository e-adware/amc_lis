<?php
	$fid=mysqli_fetch_array(mysqli_query($link,"select max(FID) as maxfid from ph_financialyear_master "));
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Purchase Receive</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<form id="form1" method="post">
			<div class="span6" style="margin-left:0px;">
				<input type="text" id="fid" name="fid" value="<?php echo $fid['maxfid'];?>" style="display:none;" />
				<table class="table table-bordered table-condensed" >
					<tr>
						<td>Order No </td>
						<td>
							<select name="selectorder" id="selectorder" class="span2" onchange="load_item()" autofocus>
								<option value="0">---Select Order No---</option>
								<?php
									$qsplr=mysqli_query($link,"select distinct order_no from ph_purchase_order_details where stat=0  order by order_no");
									
									while($qsplr1=mysqli_fetch_array($qsplr))
									{
										if($_POST['selectorder']==$qsplr1['order_no']){$ssel="Selected='selected'";} else { $ssel="";} 
									 ?>
								<option value="<?php echo $qsplr1['order_no'];?>"><?php echo $qsplr1['order_no'];?></option>
								<?php
									}?>
							</select>
							&nbsp;&nbsp;&nbsp;&nbsp;Id <input type="text" name="txtcid" id="txtcid" value="" readonly="readonly" class="imp intext span2" placeholder="Item Id"/>
						</td>
					</tr>
					<tr>
						<td>Supplier name</td>
						<td>
							<input type="text" id="txtsupplier" name="txtsupplier" class="span4" readonly="readonly" placeholder="Supplier Name"/>
						</td>
					</tr>
					<tr>
						<td>Name</td>
						<td>
							<input type="text" name="txtcntrname"  id="txtcntrname" value="" autocomplete="off" class="imp intext span4" readonly="readonly" placeholder="Name"/>
						</td>
					</tr>
					<tr>
						<td>Supplier Bill No</td>
						<td>
							<input type="text" id="txtspblno" name="txtspblno" placeholder="Supplier Bill No" />
						</td>
					</tr>
					<tr>
						<td>Expire</td>
						<td>
							<input type="text" name="txtexpire" id="txtexpire" maxlength="7" autocomplete="off" class="imp" placeholder="Expire" /> Format(yyyy-mm)
							<input type="text" id="bal" style="display:none;" />
						</td>
					</tr>
					<tr>
						<td>Batch No </td>
						<td>
							<input type="text" name="txtbatch" id="txtbatch"  autocomplete="off" class="imp intext span2" placeholder="Batch No" />
							Quantity <input type="text" name="txtqnt" id="txtqnt"  autocomplete="off" class="imp intext span1" onkeyup="numentry('txtqnt')" placeholder="Quantity" />
							Free <input type="text" name="txtfree" id="txtfree"  autocomplete="off" class="intext span1" onkeyup="numentry('txtfree')" placeholder="Free" />
						</td>
					</tr>
					
					<tr>
						<td>MRP </td>
						<td>
							<input type="text" name="txtmrp" id="txtmrp"  autocomplete="off" class="imp intext span2" placeholder="MRP" />
							&nbsp;&nbsp; Cost Price : <input type="text" name="txtcostprice" id="txtcostprice" autocomplete="off" class="imp intext span2" onkeyup="numentry('txtqnt')" placeholder="Cost Price" />
						</td>
					</tr>
					
					<tr>
						<td colspan="4" style="text-align:center">
							<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" /> 
							<input type="button" name="button" id="button" value= "Add" onclick="insert_data()" class="btn btn-default" />
							<input type="button" name="button4" id="button4" value= "Done" onclick="insert_data_final()" class="btn btn-default" />
							<!--<input type="button" name="button3" id="button3" value= "View" onclick="popitup1('pages/item_stock_rpt.php')" class="btn btn-success" /> -->
						</td>
					</tr>
				</table>
			</div>
			<div class="span5">
				<table class="table table-bordered table-condensed">
					<tr>
						<td>Search</td>
						<td> <input type="text" name="txtcustnm" size="30" id="txtcustnm"  autocomplete="off"  onkeyup="sel_pr(this.value,event)" placeholder="Search..." /></td>
					</tr>
				</table>
				<div id="load_materil" class="vscrollbar" style="max-height:300px;overflow-y:scroll;" >
					
				</div>
			</div>
			<div id="load_select" class="vscrollbar span11" style="max-height:250px;overflow-y:scroll;" >
				
			</div>
		</div>
</form>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<script type="text/javascript">
	$(document).ready(function()
	{
		
		$("#selectorder").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#txtcustnm").focus();
		});
		
		
		$("#txtspblno").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" )
			$("#txtexpire").focus();
		});
		
		$("#txtexpire").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" )
			$("#txtbatch").focus();
		});
		
		$("#txtbatch").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" )
			$("#txtqnt").focus();
		});
		
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" )
			$("#txtfree").focus();
		});
		
		
		$("#txtfree").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" )
			$("#txtmrp").focus();
		});
		
		$("#txtmrp").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" )
			$("#txtcostprice").focus();
		});
		
		$("#txtcostprice").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" )
			$("#button").focus();
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
						orderno:$("#selectorder").val(),
						type:"load_order_item",
					
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
	
	
	function clearr()//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";	
		} 
		$("#txtsupplier").val('');
		get_id();
	} 
	
	
	function load_item()
	{
		
			
			$.post("pages/ph_load_data_ajax.php",
			{
				type:"load_order_item",
				orderno:document.getElementById("selectorder").value,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#load_materil").html(data);
			})
		
	}
		
	function load_selected_item()
	{
		
		$.post("pages/global_load_g.php",
		{
			type:"load_purchase_rcpt_tmp",
			orderno:document.getElementById("selectorder").value,
		},
		function(data,status)
		{
			$("#load_select").html(data)
		})
	}
	
	function val_load_new(id)///for retrieve data against center
	{
		//alert(id);
		$("#loader").show();
		$.post("pages/global_load_g.php",
		{
			type:"load_purchase_det",
			id:id,
			ord:$("#selectorder").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			var val=data.split("@");
			document.getElementById("txtcid").value=val[0];
			document.getElementById("txtcntrname").value=val[1];
			document.getElementById("bal").value=val[3];
			document.getElementById("txtsupplier").value=val[4];
			if($("#txtspblno").val()=="")
				$("#txtspblno").focus();
			else
				$("#txtexpire").focus();
		})
	}
	
	function delete_data(itmid,btchno,orderno,bl)
	{
		$("#loader").show();
		$.post("pages/load_delete.php",
		{
			type:"purchasercpttmp",
			itmid:itmid,
			btchno:btchno,
			orderno:orderno,
			bl:bl,
		},
		function(data,status)
		{
			$("#loader").hide();
			alert(data);
			load_selected_item();
			document.getElementById("bal").value='';
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
		var ordno=document.getElementById("selectorder").value;
		if(ordno==0)
		{
			alert("Please select a Order no...");
			jj=0;
		}
		var qq=parseInt(document.getElementById("txtqnt").value);
		var bal=parseInt(document.getElementById("bal").value);
		if(qq>bal)
		{
			alert("Cannot received more than order/balance...");
			jj=0;
			$("#txtqnt").focus();
		}
		
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/ph_insert_data.php",
			{
				type:"purchase_rcv_tmp",
				fid:document.getElementById("fid").value,
				orderno:document.getElementById("selectorder").value,
				itmid:document.getElementById("txtcid").value,
				mnufctr:0,
				expiry:document.getElementById("txtexpire").value,
				batch:document.getElementById("txtbatch").value,
				qnt:document.getElementById("txtqnt").value,
				freeqnt:document.getElementById("txtfree").value, 
				mrp:document.getElementById("txtmrp").value,
				bal:document.getElementById("bal").value,
				vcstprice:document.getElementById("txtcostprice").value,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				$("#selectorder").attr("disabled",true);
				load_selected_item();
				clearr(); 
				$("#txtcustnm").focus();
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
		
		var ordrno=document.getElementById("selectorder").value;
		if(ordrno==0)
		{
			jj=0;
			alert("Please select a Order No..");
		}
		var splrblno=document.getElementById("txtspblno").value;
		if(splrblno=="")
		  {
			 jj=0;
			 alert("Please enter the supplier bill No..");
		 }
		
		if(jj==1)
		{   
		///////end ////////
		$("#loader").show();
		 $.post("pages/ph_insert_data.php",
		  {
			  type:"purchase_rcv_final",
			  orderno:document.getElementById("selectorder").value,
			  fid:document.getElementById("fid").value,
			  splrblno:document.getElementById("txtspblno").value,
			  itm:document.getElementById("txtcid").value,
		  },
		  function(data,status)
		   {
			   $("#loader").hide();
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
