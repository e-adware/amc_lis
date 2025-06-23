<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Item Return To Supplier</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<form id="form1" method="post">
		<div class="span5" style="margin-left:0px;">
			<table class="table table-condensed table-bordered">
				<tr>
					<td>Date</td>
					<td><input type="text" id="txtdate" name="txtdate" value="<?php echo date('Y-m-d');?>" readonly="readonly" /></td>
				</tr>
				
				<tr>
						<td>Supplier </td>
						<td>
							<select name="selectspplr" id="selectspplr" class="span2"  autofocus>
								<option value="0">---Select Supplier---</option>
								<?php
									$qsplr=mysqli_query($link,"select id,name from ph_supplier_master order by 	name");
									
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
					<td>ID</td>
					<td>
						<input type="text" name="txtcid" id="txtcid" value="" readonly="readonly" class="imp intext"/>
					</td>
				</tr>
				<tr>
					<td>Name</td>
					<td>
						<input type="text" name="txtcntrname"  id="txtcntrname" value="" autocomplete="off" class="imp intext" readonly="readonly"/>
					</td>
				</tr>
				
				<tr>
					<td>Batch No</td>
					<td>
						<select name="select" id="selectbatch" Onchange="val_load_stok()" >
							<option value="0">--Select Batch No--</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>Stok in Hand</td>
					<td>
						<input type="text" name="txtstkinhnd"  id="txtstkinhnd"  class="imp intext" readonly="readonly"/>
					</td>
				</tr>
				
				<tr>
					<td>Return Quantity  </td>
					<td>
						<input type="text" name="txtqnt" id="txtqnt"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" />
					</td>
				</tr>
				
				<tr>
					<td>Reason  </td>
					<td>
						<input type="text" name="txtreason" id="txtreason"  autocomplete="off" class="imp intext"  />
					</td>
				</tr>
				
				
				<tr>
					<td colspan="4" style="text-align:center">
						<input type="button" name="button2" id="button2" value="Reset" onclick="clearr()" class="btn btn-danger" /> 
						
						<input type="button" name="button4" id="button4" value= "Done" onclick="insert_data_final()" class="btn btn-default" />
						<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/item_return_rpt.php')" class="btn btn-success"  />-->
					</td>
				</tr>
			</table>
		</div>
		<div class="span5">
			<table class="table table-condensed table-bordered">
				<tr>
					<td>Item Search</td>
					<td>
						<input type="text" name="txtcustnm" id="txtcustnm"  autocomplete="off" class="intext" onkeyup="sel_pr(this.value,event)" />
					</td>
				</tr>
			</table>
			<div id="res" style="max-height:400px;overflow-y:scroll;">
			
			</div>
		</div>
	</form>
</div>
<script>
	$(document).ready(function()
	{
		load_item();
		$("#selectspplr").focus();
		
		$("#selectspplr").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!=0)
			{
				$("#txtcustnm").focus();
				
			}
		});
		
		$("#selectbatch").keyup(function(e)
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
					$("#txtqnt").focus();
					
				}
			}
		});
		
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#txtreason").focus();
				
			}
		});
		
		$("#txtreason").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				$("#button4").focus();
				
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
					$("#rad_test"+doc_v).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
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
						$("#res").scrollTop(doc_sc)
					}
				}	
		
			}
			else
			{
				$.post("pages/ph_load_data_ajax.php",
				{
					val:val,
					type:"supplr_item_rtrn",
					
				},
				function(data,status)
				{
					//alert(data);
					$("#res").html(data);
				})
			}
	}
	
	function load_item()
	{
		$.post("pages/ph_load_data_ajax.php",
		{
			type:"supplr_item_rtrn",
			
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function numentry(id) ///only numeric value entry
	{
		num=document.getElementById(id);
		numx=/^[0-9]+$/;
		if(!num.value.match(numx))
		  {
			num.value="";
		 }
	}
	
	function val_load_new(itm)
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			itm:itm,
			type:"supplr_item_rtrn",
			
		},
		function(data,status)
		{
			var val=data.split("@");
			$("#txtcid").val(val[0]);
			$("#txtcntrname").val(val[1]);
			$("#selectbatch").focus();
			lod_batchno();
		})
	}
	
	function val_load_stok(itm)
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			itm:$("#txtcid").val(),
			btchno:$("#selectbatch").val(),
			type:"stkqnt_splr_rtrn",
			
		},
		function(data,status)
		{
			
			var val=data.split("@");
			$("#txtcid").val(val[0]);
			$("#txtstkinhnd").val(val[1]);		
			
		})
	}
	
	function lod_batchno()
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			type:"supplr_item_rtrn_btch",
			itm:$("#txtcid").val(),
			
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
	}
	
	function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
		var splr=$("#selectspplr").val();
		if(splr==0)
		{
			alert("Please enter the Suupplier..");
			jj=0;
			$("#selectspplr").focus();
		}
		
		if($("#selectbatch").val()==0)
		{
			alert("Please select batch name..");
			$("#selectbatch").focus();
			jj=0;
		}
		
		if($("#txtcid").val()=="")
		{
			document.getElementById("txtcid").placeholder="Cannot Blank";
			document.getElementById("txtcntrname").placeholder="Cannot Blank";
			jj=0;
		}
		
		if($("#txtqnt").val()=="")
		{
			document.getElementById("txtqnt").placeholder="Cannot Blank";
			$("#txtqnt").focus();
			jj=0;
		}
		
		 var stkinhndqnt=parseInt($("#txtstkinhnd").val());
		 var rtrnqnt=parseInt($("#txtqnt").val());
		 var stkqnt=stkinhndqnt-rtrnqnt;
		 
		 if(stkqnt<0)
		 {
			 jj=0;
			 alert("Return Quantity can not be greater than Stock Quantity..");
			 $("#txtqnt").focus();
		 }
		 
		if($("#txtreason").val()=="")
		{
		   document.getElementById("txtreason").placeholder="Cannot Blank";
		   $("#txtreason").focus();
		   jj=0;
		}
		 
		///////end ////////
		if(jj==1)
		{
		 $.post("pages/ph_insert_data.php",
		  {
			  type:"item_return_to_splr",
			  spplrid:$("#selectspplr").val(), 
			  batch:$("#selectbatch").val(), 
			  itmid:$("#txtcid").val(),
			  rtndate:$("#txtdate").val(), 
			  qnt:$("#txtqnt").val(), 
			  reason:$("#txtreason").val(),
		  },
		  function(data,status)
		   {
			   bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clearr();
				}, 1000);
		   })
		}
	}
	
	function clearr()
	{
		$("#selectbatch").val('0');
		$("#txtcid").val('');
		$("#txtcntrname").val('');
		$("#txtqnt").val('');
		$("#txtstkinhnd").val('');
		$("#txtreason").val('');
		$("#selectspplr").focus();
	}
</script>
