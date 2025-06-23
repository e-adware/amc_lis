<!--header-->
<script src="../jss/ph_pay_rcv.js"></script>
<div id="content-header">
    <div class="header_div"> <span class="header">Credit From Customer</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<form id="form1" method="post">
		<div class="span10" style="margin-left:0px;">
			
			
			
			<table class="table table-bordered table-condensed">
				<tr>
					<td >Select
						<select id="ph_srch_type"  >
							<option value="1">Bill No</option>
							<option value="2">PIN</option>
							<option value="3">UHID</option>
							<option value="4">Name</option>
						</select>

						<input type="text" name="reg" id="reg" class="precv" onKeyUp="sel_pat_bill(this.value,event)" autofocus/>
					</td>
					 <td>Date
				        <input type="text" id="pydate" readonly="readonly" value="<?php echo date('Y-m-d');?>" />
				      </td>
				</tr>
				
				<tr>
					<td colspan="2">
						<div id="bal_pat" style="max-height:200px;overflow:scroll;overflow-x:hidden;display:none;" align="center">
						</div>
					</td>
				</tr>
			</table>
			
			
			
			
			
			
			
			<table class="table table-bordered table-condensed">
				<tr>
					<td width="20%">Bill No</td>
					<td><input type="text" id="selectbill" name="selectbill" readonly="readonly" class="imp intext"/></td>
				</tr>
				<tr>
					<td width="20%">Customer</td>
					<td><input type="text" id="txtcust" name="txtcust" readonly="readonly" class="imp intext span5"/></td>
				</tr>
				
				<tr>
					<td>Total Amount </td>
					<td><input type="text" id="txtttl" name="txtttl" readonly="readonly" class="imp intext"/></td>
				</tr>
				<tr>
					<td>Discount</td>
					<td><input type="text" id="txtdis" name="txtdis" readonly="readonly" class="intext"/></td>
				</tr>
				<tr>
					<td>Paid</td>
					<td><input type="text" id="txtpaid" name="txtpaid" readonly="readonly" class="intext"/></td>
				</tr>
				<tr>
					<td>Amount Balance </td>
					<td><input type="text" id="txtblnce" name="txtblnce" readonly="readonly" class="intext"/></td>
				</tr>
				<tr>
					<td>Current Paid </td>
					<td><input type="text" id="txtcrdtamt" name="txtcrdtamt" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" class="imp intext" /></td>
				</tr>
				

				<tr>
					<td>Payment Type </td>
					<td>
						<select name="select" id="selectpymtype" onchange="pay_type()">
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
					<td colspan="2" style="text-align:center;">
						<input type="button" id="button" value="Refresh" class="btn btn-default" style="width:80px" onclick="clearr()" />
						<input type="button" id="button1" value="Submit" class="btn btn-default" style="width:80px" onclick="insert_data()"  />
						<!--<input type="button" id="btn8" value="Print Bill" class="btn btn-success" style="width:70px" onclick="print_receipt()"  />-->
						
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>
<script>

$(document).ready(function()
{
	$("#selectbill").keyup(function(e)
	{
		if(e.keyCode==13 && $(this).val()!="0")
		$("#txtcrdtamt").focus();
	});
	$("#txtcrdtamt").keyup(function(e)
	{
		if(e.keyCode==13 && parseInt($(this).val())!="0" && $(this).val()!="")
		$("#selectpymtype").focus();
	});
	$("#selectpymtype").keyup(function(e)
	{
		if(e.keyCode==13)
		$("#button1").focus();
	});
});

function clearr()//For Clear the text fields
{
	/*var b=document.getElementsByClassName("intext");
	for(var j=0;j<b.length;j++)
	{
		b[j].value="";	
	}
	$("#selectbill").val('0');*/
	
	//setTimeout("location.reload(true);",1000);
	$("#selectbill").val('');
	$("#txtcust").val('');
	$("#txtttl").val('');
	$("#txtpaid").val('');
	$("#txtdis").val('');
	$("#txtcrdtamt").val('');
	$("#txtblnce").val('');
	$("#button1").attr("disabled", false);
} 


function insert_data()
{
	///////For Check Blank fields//
	var jj=1;
	var billno=document.getElementById("selectbill").value;
	 if(billno==0)
	 {
		 alert("Please select a Bill No..");
		 $("#selectbill").focus();
		 jj=0;
	 }
	 
	var chk=document.getElementsByClassName("imp");
	for(var i=0;i<chk.length;i++)
	if(chk[i].value=="")
	{
		document.getElementById(chk[i].id).placeholder="Can not be Blank";
		jj=0;
	}
	
	var pd=document.getElementById("txtcrdtamt").value;
	if(pd=="")
	{
		alert("Enter amount..");
		$("#txtcrdtamt").focus();
		jj=0;
	}
	
	var bl=parseInt(document.getElementById("txtblnce").value);
	var pd=parseInt(document.getElementById("txtcrdtamt").value);
	if(pd>bl)
	{
		alert("Cannot paid more than balance..");
		$("#txtcrdtamt").focus();
		jj=0;
	}
	
	if($("#selectpymtype").val()=="Cheque" && $("#chk_no").val()=="")
	{
		$("#chk_no").focus();
		jj=0;
	}
	
	if(jj==1)
	{
		$.post("pages/ph_insert_data.php",
		{
			type:"cust_credit",

			blno:$("#selectbill").val(),
			ptype:$("#selectpymtype").val(),
			amtpaid:$("#txtttl").val(),
			amtblnce:$("#txtblnce").val(),
			txtcrdtamt:$("#txtcrdtamt").val(),
			chk_no:$("#chk_no").val(),
			pymtdate:$("#pydate").val(),
		},
		function(data,status)
		{
			alert("Done");
			$("#button1").attr("disabled", true);
			$("#button").focus();
			clearr();
			/*bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				location.reload();
			}, 1000);*/
		})
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


	
function print_receipt()
	{
		var jj=1;
		if($("#selectbill").val()==0)
		{
			alert("Please select a Bill No from list ");
			$("#selectbill").focus();
			jj=0;
		}
		
		if($("#txtttl").val()=="0")
		{
			alert("Total Amount Can not be Zero(0)..");
			
			jj=0;
		}
		if(jj==1)
		{
		
			var billno=$("#selectbill").val();
			
			
			//url="pages/credit_receipt_zebra.php?billno="+billno;
			url="pages/dot_mtrx_ph_crdt_rpt.php?billno="+billno;
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
			$("#btn8").attr("disabled", true);
			//setTimeout("location.reload(true);",1000);
		}
}	
</script>

