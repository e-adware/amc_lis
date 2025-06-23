<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Supplier Payment</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
			
			
			<tr>
				 <td>Supplier <input type="text" id="id" style="display:none;" /></td>
				<td>
					<select name="selectspplr" id="selectspplr" onchange="lod_bill_no()"  autofocus>
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
							Date  <input type="text" id="txtpaydate" name="txtpaydate" placeholder="Payment Date" class="span2 imp" />
							Amount <input type="text" id="txtpayamount" name="txtpayamount" placeholder="Amount" class="span2 imp" />
							Bill Amount <input type="text" id="txtamount" name="txtamount" placeholder="Amount" class="span2 imp" readonly />
				</td>
			</tr>
			
			<tr>
				 <td>A/C No </td>
				<td>
					
                  <select name="selectacno" id="selectacno"   autofocus>
								<option value="0">---Select A/C No---</option>
								<?php
									$qsplr=mysqli_query($link,"select account_no from bank_ac_no order by slno");
									while($qsplr1=mysqli_fetch_array($qsplr))
									{if($_POST['account_no']==$qsplr1['account_no']){$ssel="Selected='selected'";} else { $ssel=" ";}
								?>
									<option value="<?php echo $qsplr1['account_no'];?>"><?php echo $qsplr1['account_no'];?></option>
								<?php
									}
								?>
							</select>
							
                  Payment Type
                    <select name="select" id="selectpymtype" onchange="pay_type()">
							<option value="0">Select</option>
							<option value="Cash">Cash</option>
							<option value="Card">Card</option>
							<option value="Cheque">Cheque</option>
						</select> &nbsp;&nbsp;&nbsp;
						<span id="typ" style="display:none;">
							Cheque No : <input type="text" id="chk_no" class="intext" name="" />
						</span>
				</td>
			</tr>
			
			
     </table>			
     
	
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
	
	<div class="" style="margin-bottom:10px;text-align:center;">
		<button type="button" class="btn btn-info" onclick="data_saved()">Save</button>
		
	</div>
	
</div>

<link rel="stylesheet" href="include/css/jquery-ui.css" />

<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>

<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	
	$(document).ready(function()
	{
		$("#txtpaydate").datepicker({dateFormat: 'yy-mm-dd',changeYear:true,yearRange:'c-10:c+10'});
		
	});
		
	function lod_bill_no()
	{
		var jj=1;
		
		
		if(jj==1)
		{
			
		$.post("pages/inv_load_data_ajax.php"	,
		{
			type:"splr_bill_load",
			spplrid:$("#selectspplr").val(),
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	 }
   }


function chkstockanble(val,netamt,alrdypaid,i)
{
	var c=document.getElementById("txtamount").value;
	var a=parseInt(val);
	var b=parseInt(alrdypaid);
	var vttl=a+b;
	var res=c-a;
	//document.getElementById("txtamount").value=res;
	if(vttl<=netamt)
	 {
		 $("#"+i+"").prop("disabled",false);
		 $("#"+i+"").prop("class","pats");
	 }
	 else
	 {
		 $("#"+i+"").prop("disabled",true);
		 $("#"+i+"").prop("checked",false);
		 $("#"+i+"").prop("class","n_pats");
	 }
	
}
	


function add_netamt()
{
	var blno="";
	var chk=$(".pats:checked");
	if(chk.length>0)
	 {
		 
		for(var i=0;i<chk.length;i++)
		{
			//blno=blno+"@#"+$(chk[i]).val()+"%%"+$("#txtnwpaid_"+$(chk[i]).attr("id")+"").val();
			blno=blno+"@#"+$(chk[i]).val()+"%%"+$("#txtnwpaid_"+$(chk[i]).attr("id")+"").val()+"%%"+$("#txtnetamt"+$(chk[i]).attr("id")+"").val();
			
		}
		
			$.post("pages/inv_load_display.php",
			{
				type:"supplier_payment",
				
				blno:blno,
				spplirid:$("#selectspplr").val(),
				
				},
				function(data,status)
				{
					//alert(data);
					$("#txtamount").val(data);
				}
				)
 	}		
}	

	
function pay_type()
{
	if($("#selectpymtype").val()=="Cheque")
	{
		$("#typ").show();
		$("#chk_no").focus();
	}
	else
	{
		$("#typ").hide();
	}
}	
		



function data_saved()
{
	jj=1;
	var blno="";
	var chk=$(".pats:checked");
	
	var ptype=document.getElementById("selectpymtype").value;
		if(ptype==0)
		{
			alert("Please select a Payment type");
			$("#selectpymtype").focus();
			jj=0;
		}
		
		
	if($("#txtpaydate").val()=="")
	{
	
		alert("Please enter the Payment Date..");
		$("#txtpaydate").focus();
		jj=0;
	
	}
		
	if($("#selectspplr").val()==0)
		{
		
			alert("Please select the Supplier..");
			$("#selectspplr").focus();
			jj=0;
		
		}
		
	if($("#selectacno").val()==0)
		{
		
			alert("Please select a a/c no..");
			$("#selectacno").focus();
			jj=0;
		
		}			
		
	if($("#selectpymtype").val()=="Cheque")
	 {
		if($("#chk_no").val()=="")
		{
			alert("Please enter the cheque no..");
			$("#chk_no").focus();
			jj=0;
		}
	 }
	 if($("#txtamount").val()=="")
	 {
		alert("Please select a Bill..");
		jj=0;
	 }
	 
	 var chk_amt=$("#txtpayamount").val();
	 var chk_amt1=$("#txtamount").val();
	 if(chk_amt!=chk_amt1)
	 {
		 alert("Payment Amount and Bill Amount is mismatched");
		 $("#txtpayamount").focus();
		 jj=0;
	 }
	 
	if(jj==1)
	{
	
	if(chk.length>0)
	 {
		for(var i=0;i<chk.length;i++)
		{
			//blno=blno+"@#"+$(chk[i]).val()+"%%"+$("#txtnwpaid_"+$(chk[i]).attr("id")+"").val();
			blno=blno+"@#"+$(chk[i]).val()+"%%"+$("#txtnwpaid_"+$(chk[i]).attr("id")+"").val()+"%%"+$("#txtnetamt"+$(chk[i]).attr("id")+"").val();
			
		}
			
				$.post("pages/inv_insert_data.php",
				{
					type:"supplier_payment",
					blno:blno,
					spplirid:$("#selectspplr").val(),
					paydate:$("#txtpaydate").val(),
					ptype:$("#selectpymtype").val(),
			        chqno:$("#chk_no").val(),
			        acno:$("#selectacno").val(),
					
					},
					function(data,status)
					{
						
						alert("Done");
						clearr();
						lod_bill_no();
					 }
					)
	  }	
	} 			
}

	
function clearr()
{
	$("#txtpaydate").val('');
	$("#selectpymtype").val('0');
	$("#chk_no").val('');
	$("#selectacno").val('0');
	$("#txtpayamount").val('');
	$("#txtamount").val('');
	
}	
	
	
	function stk_prr()
	{
		catid=$("#subcatid").val();
		url="pages/inv_stock_rpt.php?catid="+catid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	
</script>
