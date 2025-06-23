<!--header-->
<?php
$orderno="";
$orderno=base64_decode($_GET["orderno"]);
echo $orderno;

if($orderno)
{
	echo "<input type='hidden' value='$orderno' id='txtordrid'>";
}else
{
	echo "<input type='hidden' value='0' id='txtordrid'>";
}

?>

<html>
	<head>
		<script type="text/javascript">
	$(document).ready(function()
	{
		
		//$("#button").attr("disabled",false);
		
		
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
					$("#selectbill").focus();
					//$('#selectcategory').select2('focus');
				}
			}
		});
		
		
		
		$("#selectbill").keyup(function(e)
		{
			if(e.keyCode==27)
			$("#txtnowpaid").focus();
		});
		
		$("#txtnowpaid").keyup(function(e)
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
		
		
	}
	
	function reset_all()
	{
		
		setTimeout("location.reload(true);",1000);
	}
		
	function calc_balance(val,e) ///for calculation
	{
		
			var a=document.getElementById("txttotalamt").value;
			var paid=document.getElementById("txtalrdypaid").value;
			var nwpaid=document.getElementById("txtnowpaid").value;
			
			var res=a-paid-nwpaid;
			res = res.toFixed(2);
			document.getElementById("txtbalance").value=res;
		
	}
	
	function val_load_new(id)///for 
	{
		
		$.post("pages/inv_load_display.php",
		{
			type:"splr_bill_detail",
			splirid:$("#selectspplr").val(),
			billno:$("#selectbill").val(),
		},
		function(data,status)
		{
						
			var val=data.split("@");
			//$("#txtcid").val(val[0]);
			$("#txttotalamt").val(val[1]);	
			$("#txtalrdypaid").val(val[2]);
			$("#txtnowpaid").focus();
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
		
		var billno=document.getElementById("selectbill").value;
		if(billno==0)
		{
			alert("Please select a bill no..");
			$("#selectbill").focus();
			jj=0;
		}
		var ptype=document.getElementById("selectpymtype").value;
		if(ptype==0)
		{
			alert("Please select a Payment type");
			$("#selectpymtype").focus();
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
		
		if($("#txtbalance").val()!=0)
		{
			alert("Please make full Payment of this bill..");
			$("#txtnowpaid").focus();
			jj=0;
	    }
		
		if(jj==1)
		{   
		///////end ////////
		
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"supplier_payment",
			  date:$("#txtorddate").val(), 
			  supplrid:$("#selectspplr").val(), 
			  billno:$("#selectbill").val(),
			  ttlamt:$("#txttotalamt").val(),
			  alrdypaid:$("#txtalrdypaid").val(),
			  nwpaid:$("#txtnowpaid").val(),
			  balance:$("#txtbalance").val(),
			  ptype:$("#selectpymtype").val(),
			  chqno:$("#chk_no").val(),
			 
		  },
		  function(data,status)
		   {
			 	   
			   alert("Done");
			   $("#button").attr("disabled",true);
			   reset_all();
			   $("#selectspplr").focus();
		   })
	}}
	
	
	
	
	function popitup1(url)
	{
		var substrid=$("#selectsubstr").val();
		var orderno=$("#txtordo").val();
		var orderdate=$("#txtorddate").val();
		
		url=url+"?substrid="+substrid+"&orderno="+orderno+"&orderdate="+orderdate;
		newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
		//$("#button5").attr("disabled",true);
		$("#selectsubstr").attr("disabled",false);
		get_id();
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

function lod_bill_no()
	{
		$.post("pages/inv_load_display.php",
		{
			type:"splr_bill_load",
			splirid:$("#selectspplr").val(),
		},
		function(data,status)
		{
			$("#chk").val("0");	
			document.getElementById("selectbill").options.length=1;
			var data=data.split("#");
			for(var i=0;i<data.length-1;i++)
			{
				var opt = document.createElement("option");
				var ip=document.getElementById("selectbill").options.add(opt);
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
    <div class="header_div"> <span class="header">Supplier Payment</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		
		<form id="form1" method="post">
			<div class="span11" style="margin-left:0px;">
				<table class="table table-bordered table-condensed">
					<tr>
						<td> Date</td>
						<td>
							
							<input type="text" id="txtorddate" name="txtorddate" value="<?php echo date('Y-m-d');?>" onfocus="jsdate('txtorddate')" readonly/>
						</td>
					</tr>
					<tr>
						<td>Select Supplier</td>
						<td >
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
						</td>
					</tr>
					
					<tr>
						<td>Bill No </td>
						<td>
							<select name="select" id="selectbill" onchange="val_load_new();" onkeyup="next_tab(this.id,event)">
								<option value="0">--Select Bill--</option>
							</select>
						</td>
					</tr>
					
			       <tr>
						<td>Total Amount</td>
						<td>
							<input type="text" name="txttotalamt" id="txttotalamt" size="20" value="" class="imp" readonly="readonly" />
						</td>
					</tr>
					
					<tr>
						<td>Already paid</td>
						<td>
							<input type="text" name="txtalrdypaid" id="txtalrdypaid" size="20" value="" class="imp" readonly="readonly" />
						</td>
					</tr>
					<tr>
						<td>Now Paid</td>
						<td>
							<input type="text" name="txtnowpaid" id="txtnowpaid" size="20" value="" class="imp" onkeyup="calc_balance(this.value,event)"  />
						</td>
					</tr>
					
					<tr>
						<td>Balance</td>
						<td>
							<input type="text" name="txtbalance" id="txtbalance" size="20" value="" class="imp" readonly="readonly" />
						</td>
					</tr>
					<tr>
					<td>Payment Type </td>
					<td>
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
				
					
					<tr>
						<td colspan="4" style="text-align:center">
							<input type="button" name="button2" id="button2" value="Reset" onclick="window.location='processing.php?param=168'" class="btn btn-danger" /> 
							<input type="button" name="button" id="button" value= "Save" onclick="insert_data()" class="btn btn-success" />
							<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/inv_purchase_ordr_rpt.php')"  />-->
						</td>
					</tr>
					
				</table>
			</div>
			
			
			
		</form>
		<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
		<div id="loader" style="display:none;top:50%;position:fixed;"></div>
</div>
</body>
</html>
