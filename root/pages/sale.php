<?php
	$userid=$_SESSION['emp_id'];
	//mysqli_query($link,"delete from ph_sell_details_temp where user='$userid'");
	$vbilno="";
	$vbilno=base64_decode($_GET["billno"]);
	
	
	
	if($p_info['levelid']!=1)
	{	
		$ip_addr=$_SERVER["REMOTE_ADDR"];
		
		$ip_addr_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `access_ip` WHERE `ip_addr`='$ip_addr' "));
		if($ip_addr_check)
		{
			$entry_val=1; //echo "OK";
		}else
		{
			$entry_val=0; //echo " NOT OK";
		}
	}else
	{
		$entry_val=1; //echo "OK";
	}

	
if($vbilno)
{
	echo "<input type='hidden' value='$vbilno' id='txtupdtid'>";
}else
{
	echo "<input type='hidden' value='0' id='txtupdtid'>";
}

$patient_id=base64_decode($_GET["uhid"]);
$pin=base64_decode($_GET["ipd"]);
$type=base64_decode($_GET["type"]);
$ind_num=base64_decode($_GET["ind_num"]);

mysqli_query($link,"UPDATE `ph_stock_master` SET `quantity`=0 WHERE `quantity`<0 "); 
mysqli_query($link,"UPDATE `ph_stock_process` SET `s_remain`=0 WHERE `s_remain`<0 "); 


$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
$vuhid=$pin;


if($patient_id)
{
	echo "<input type='hidden' value='$patient_id' id='patient_id'>";
	echo "<input type='hidden' value='$pin' id='pin'>";
	echo "<input type='hidden' value='$type' id='type'>";
	echo "<input type='hidden' value='$ind_num' id='indno'>";
}else
{
	echo "<input type='hidden' value='0' id='patient_id'>";
	echo "<input type='hidden' value='0' id='pin'>";
	echo "<input type='hidden' value='0' id='type'>";
	echo "<input type='hidden' value='0' id='indno'>";
}

// Check patients from last month
$date2 = date('Y-m-d');
$date1 = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date2)) . " -1 months"));
?>
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<style>
#table_top > thead > tr > th, #table_top > tbody > tr > th, #table_top > tfoot > tr > th, #table_top > thead > tr > td, #table_top > tbody > tr > td, #table_top > tfoot > tr > td, #table_det > thead > tr > th, #table_det > tbody > tr > th, #table_det > tfoot > tr > th, #table_det > thead > tr > td, #table_det > tbody > tr > td, #table_det > tfoot > tr > td
{
	padding: 0 0 0 0;
}
#table_top tr:hover{background:none;}
#table_det tr:hover{background:none;}
</style>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Sales Entry</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<form id="form1" name="form1" method="post" action="">
				<div class="span11" style="margin-left:0;">
					<input type="hidden" id="chk" value="0" />
					<table  class="table table-condensed" id="table_top" >
						<tr>
							<th>Bill Type <br/>
								<select id="bill_type" onchange="paidamnt_disable()" class="span2" autofocus>
									<option value="1">Cash Bill</option>
									<option value="2">Credit Bill</option>
									<!--<option value="3">In House</option>-->
								</select>
							</th>
							<th>
								Bill No<br/>
								<input type="text" id="txtbilno" name="txtbilno"  class="imp intext" readonly  /> &nbsp; <input type="hidden" id="button8" value="Search" class="btn btn-default" onclick="search_data()"/>
							</th>
							
							<th colspan="2">
								Referred by<br/>
								<select id="ref_by" class="span3">
									<!--<option value="0">Select</option>-->
									<?php
									$ref=mysqli_query($link,"SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` ORDER BY `ref_name`");
									while($rf=mysqli_fetch_array($ref))
									{
									?>
									<option value="<?php echo $rf['refbydoctorid'];?>"><?php echo $rf['ref_name'];?></option>
									<?php
									}
									?>
								</select>
							</th>
							<th colspan="2" style="display:none">
								Patient Type<br/>
								<select id="pat_typ" class="">
									<?php
									$s_typ=mysqli_query($link,"SELECT * FROM `ph_sell_type`");
									while($st=mysqli_fetch_array($s_typ))
									{
									?>
									<option value="<?php echo $st['sell_id'];?>"><?php echo $st['sell_name'];?></option>
									<?php
									}
									?>
								</select>
							</th>							
						</tr>
						
						<tr>
							<th><span id="pnm"> PIN </span>
								<?php if($pin){ echo "<input type='text' value='$pin' id='uhid' class='uhid' readonly>"; }else{ ?>
								<input list="browsrs" type="text" name="uhid" id="uhid" class="" style="width:100px;"  />
								<datalist id="browsrs">
								<?php
								
									$pid = mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2' ");
									while($pat_uid=mysqli_fetch_array($pid))
									{
										echo "<option value='$pat_uid[opd_id]'>";
									}
								?>
								</datalist>
							<?php } ?>
							</th>
							
							
							<th colspan="3">Customer
								<input type="text" id="txtcustnm" name="intext1" class="intext span4" <?php if($patient_id){echo "disabled='disabled'";}?> value="<?php echo $pat_info['name']; ?>" />
							</th>
							<th colspan="2">
								Phone <input type="text" id="txtphone" name="intext2" maxlength="10" onkeyup="numentry('txtphone')" class="intext " style="width:150px" <?php if($patient_id){echo "disabled='disabled'";}?> value="<?php echo $pat_info['phone']; ?>" />
								Date
								<input type="text" id="txtdate" name="txtdate" readonly="readonly" class="imp" style="width:90px;" value="<?php echo date('Y-m-d');?>" />
							</th>
						</tr>
						<tr>
							<th>ItemCode<br/><input type="text" id="txtitmcode" name="txtitmcode" readonly="readonly" class="intext span2"/></th>
							<th>MRP<br/><input type="text" id="txtrate" name="txtrate" readonly="readonly" class="intext span1"/></th>
							<th>Stock<br/><input type="text" id="txtstock" name="txtstock" readonly="readonly" class="intext span1" /></th>
							<th>Mf. Date <br/><input type="text" id="txtmanufactrdate" name="txtmanufactrdate" readonly="readonly" class="intext span2" /></th>
							<th>Expire Date<br/><input type="text" id="txtexpiry" name="txtexpiry" readonly="readonly" class="intext span2"/></th>
							<th></th>
						</tr>
						
						<tr>
							<th colspan="3">
								Item<br/>
								<input type="text" id="txtitmname" name="intext3" class="intext span4" onfocus="chk_focus()" onkeyup="sel_pr(this.value,event)" />
							</th>
							<th>
								Batch No<br/>
								<select name="select" id="selectbatch" onchange="load_manufctrdate()" onkeyup="next_tab(this.id,event)">
									<option value="0">--Select BatchNo--</option>
								</select>
							</th>
							
							<th>Quantity<br/>
							<input type="text" id="txtqnt" name="intext12"  class="intext span1" onkeyup="numentry('txtqnt')"/>
							
						<?php if($entry_val==1){ ?>
							<td>
								<br/>
								<input type="button" id="button3" name="intext13" value="Add" class="btn btn-default" onclick="insert_data_temp()" />
							</td>
						<?php } ?>
						</tr>
						
					</table>
				</div>
						<!-------------for lod product---------------------> 
					<div class="span5" style="margin-left:0px;">
						<div style="max-height:200px;overflow-y:scroll;" id="loadproduct">
							<script>load_prdct()</script>
						</div>
					</div>
					<div class="span6" style="">
						<div style="max-height:200px; overflow-y:scroll;" id="loadrw">
							<script>load_selectedproduct();</script>
						</div>
					</div>
					<!------------end------------------------------->
					<div class="span11" style="margin-left:0px;">
						<table id="table_det" class="table table-bordered table-condensed">
							<tr>
								<th>Total Amount<br/>
									<input type="text" id="txttlamnt" name="txttlamnt" readonly="readonly" class="intext"/>
								</th>
								<th>GST (Rs)<br/>
									<input type="text" id="gst" name="gst" class="intext" value="0" readonly="readonly" />
								</th>
								<th>Net Amount<br/>
									<input type="text" id="net" name="net" class="intext" value="0" readonly="readonly" />
								</th>
								<th>Discount<br/>
									<input type="text" id="txtdiscount" name="txtdiscount" class="intext" <?php if($p_info['levelid']!='1'){echo "readonly='readonly'";}?> onkeyup="calc_discount(this.value,event)"/>
								</th>
								<th>After Discount<br/>
									<input type="text" id="txtafterdis" name="txtafterdis" class="intext" readonly="readonly" />
								</th>
								<th>Adjustment<br/>
									<input type="text" id="txtadjust" name="txtadjust" class="intext"  onkeyup="calc_adjustment(this.value,event)"  />
								</th>
								
								<th>Paid Amount<br/>
									<input type="text" id="txtpaid" name="txtpaid" class="intext"  onkeyup="calc_discount1(this.value,event)" />
								</th>
								<th>Balance<br/>
									<input type="text" id="txtbalnce" name="txtbalnce" class="intext" readonly="readonly" />
								</th>
							</tr>
							<tr>
								<td colspan="7" style="text-align:center;">
									<input type="button" id="button4" value="Refresh" class="btn btn-danger" style="width:70px" onclick="window.location='processing.php?param=20'"/>
									<input type="button" id="btn5" value="Done" class="btn btn-info" style="width:70px" onclick="check_sale_qnty()" />
									<input type="button" id="btn8" value="Print" class="btn btn-success" style="width:70px" onclick="print_bill()" disabled="disabled" />
									<!--<input type="button" id="button6" value="Print" class="btn btn-success" style="width:70px" onclick="popitup1('pages/invoice_print_applet.php')" />-->
								</td>
							</tr>
						</table>
					</div>
			<div id="back"></div>
			<div id="results"></div>
		</form>
		<div id="loader" style="display:none;top:50%;position:fixed;"></div>
		<style>
			.intext
			{
				width:80px;
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function()
			{
				load_id();
				
				if($("#patient_id").val()!=0)
				{
					$("#bill_type").attr("disabled",false);
				}
				else
				{
					$("#bill_type").attr("disabled",true);
				}
				
				$("#txtpaid").attr("disabled",true);
				
				//$("#ref_by").select2({ theme: "classic" });
				
				//$("#bill_type").select2("focus");
				
				$("#bill_type").keyup(function(e)
				{
					//alert($(this).val());
					if(e.keyCode==13)
					$("#ref_by").select2("focus");
				});
				$("#ref_by").on("select2:close",function(e)
				{
					setTimeout(function(){$("#uhid").focus();},200);
				});
				$("#pat_typ").keyup(function(e)
				{
					if(e.keyCode==13)
					$("#uhid").focus();
				});
				$("#selectbatch").keyup(function(e)
				{
					if(e.keyCode==13 && $(this).val()!=0)
					{
						$("#txtqnt").focus();
					}
				});
				
				if($("#txtupdtid").val()!=0)
				{
				  search_data();
			    }
				$("#uhid").keyup(function(e)
				{
					
					if(e.keyCode==13 && $(this).val()=="")
					{
						//$("#txtcustnm").val('Cash');
						$("#txtcustnm").focus();
					}
					//~ else if(e.keyCode==13 && $(this).val()==2)
					//~ {
						//~ $(this).css("border","");
						//~ $("#uhid").focus();
					//~ }
				});
				
				
				$("#pat_type").keyup(function(e)
				{
					if(e.keyCode==13 && $(this).val()==0)
					{
						$(this).css("border","1px solid #f00");
					}
					else if(e.keyCode==13 && $(this).val()==1)
					{
						$(this).css("border","");
						$("#txtcustnm").focus();
					}
					else if(e.keyCode==13 && $(this).val()==2)
					{
						$(this).css("border","");
						$("#uhid").focus();
					}
					else if(e.keyCode==13 && $(this).val()==3)
					{
						$(this).css("border","");
						$("#uhid").focus();
					}
				});
				
				
				$("#txtbilno").keyup(function(e)
				{
					if(e.keyCode==13)
					{
						$("#button8").focus();
					}
				});
				$("#txtcustnm").keyup(function(e)
				{
					$(this).val($(this).val().toUpperCase());
					if(e.keyCode==13)
					{
						if($(this).val().trim()!="")
						$("#txtphone").focus();
						else
						$(this).css('border','1px solid #f00');
					}
					else
					{
						$(this).css('border','');
					}
				});
				$("#txtphone").keyup(function(e)
				{
					if(e.keyCode==13)
					{
						$("#txtitmname").focus();
					}
				});
				
				
				
				$("#txtqnt").keyup(function(e)
				{
					if(e.keyCode==13 && $(this).val()!="" && parseInt($(this).val())!="0")
					{
						$("#button3").focus();
					}
				});
				
				$("#uhid").keyup(function(e)
				{
					if(e.keyCode==13)
					{
						$.post("pages/ph_load_display_ajax.php",
						{
							uhid:$(this).val(),
							pat_type:$("#pat_type").val(),
							type:"load_patient_sale",
						},
						function(data,status)
						{
							
							var val=data.split("@");
							$("#txtcustnm").val(val[0]);
							$("#txtphone").val(val[1]);
							$("#opd_id").val(val[2]);
							$("#ipd_id").val(val[3]);
							$("#ref_by").val(val[3]);
							if(val[4]=="3")
							{
								$("#bill_type").attr("disabled",false);
							}
							else
							{
								$("#bill_type").attr("disabled",true);
							}
							
							$("#txtcustnm").focus();
						})
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
								$("#loadproduct").scrollTop(doc_sc);
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
								$("#loadproduct").scrollTop(doc_sc);
							}
						}
					}
					else if(unicode==27)
					{
						$("#txtdiscount").focus();
						$("html,body").animate({scrollTop: '500px'},1000)
					}
					else
					{
						$.post("pages/ph_load_data_ajax.php",
						{
							val:val,
							type:"loadstockproduct",
							patient_id:$("#patient_id").val(),
							pin:$("#pin").val(),
							typ:$("#type").val(),				
						},
						function(data,status)
						{
							$("#loadproduct").html(data);
						})
					}
			}
			
	function popitup1(url)
	{
		var blno=document.getElementById("txtbilno").value;
		var date1=document.getElementById("txtdate").value;

		url=url+"?blno="+blno+"&fid="+fid+"&date1="+date1;
		newwindow=window.open(url,'window','left=10,top=10,height=600,width=1020,menubar=1,resizeable=0,scrollbars=1');
	}
	
	function load_prdct() //for load
	{
		$.post("pages/ph_load_data_ajax.php",
		{
			type:"loadstockproduct",
			patient_id:$("#patient_id").val(),
			pin:$("#pin").val(),
			indno:$("#indno").val(),
			typ:$("#type").val(),
		},
		function(data,status)
		 {
			 $("#loadproduct").html(data);
		 })
	}
	
	
	function paidamnt_disable()
	{
		var bltype=document.getElementById("bill_type").value;
		if(bltype==2)
		{
			$("#txtpaid").attr("disabled",true);
			$("#txtbalnce").val($("#txtpaid").val());
			$("#txtpaid").val('0');
			
		}
		else
		{
			var a=document.getElementById("txtafterdis").value;
			var vadjus=document.getElementById("txtadjust").value;
			var res=a-vadjus;
			
			$("#txtpaid").attr("disabled",true);
			$("#txtpaid").val(res);
			$("#txtbalnce").val('0');
		}
	}
			
	function chk_focus()
	{
		$("html,body").animate({scrollTop: '250px'},1000)
		setTimeout(function(){ $("#chk").val("1");},2000);
	}

	function tab_next(e) 
	{ 	
		var unicode=e.keyCode? e.keyCode : e.charCode; 	
		if(unicode==13) 
		{ 		
			var act=document.activeElement.id; 		
			if(!act) 		
			{ 			
				document.getElementById("info1").focus();	 		
			} 	
			else 
			{    
				var clsn=$("#"+act).attr("class");    
				var nam=$("#"+act).attr("name"); 
				var val=nam.replace( /^\D+/g, ''); 
				val=parseInt(val)+1; 
				document.getElementsByName(clsn+val)[0].focus();
			}
		}
	}
	
	function load_selectedproduct() //for load product against bill no
	{
		$.post('pages/ph_load_data_ajax.php',
		{
			type:"showselectedsale_product",
			billno:$("#txtbilno").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		 {
			 $("#loadrw").html(data);
		 })
	}
	
	function change_text()
	{
		var ptype=document.getElementById("pat_type").value;
		if(ptype=="1")
		{
			$("#pnm").text("PIN");
		}
		else if(ptype=="2")
		{
			$("#pnm").text("PIN");
		}
	}
			
	function val_load_new(pid)  ///For load  Details
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			pid:pid,
			type:"item_name",
		},
		function(data,status)
		{
			var val=data.split("@");
			  $("#txtitmcode").val(val[0]);
			  $("#txtitmname").val(val[1]);
			  $("#txtrate").val(val[2]);
							  
			  $("#txtqnt").html="";
			  $("#selectbatch").focus();
			  //$("#selectbatch").select2("focus");
			  lod_batchno();
		})
	}
			
	function load_id()
	{
		$.post("pages/ph_load_id.php",
		{
			type:"load_sale_id",
		},
		function(data,status)
		{
		  $("#txtbilno").val(data);
		  load_prdct();
		})
	}
			
	function change(val,val1)
	{
		document.getElementById(val).value=val1;
	}
			
	function clearr(date)//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";	
		} 
	   load_id()
	   delete_all();
			   
	   $("#txtdate").val(date);
	   $("#txtupdtid").val('');
	} 
			
			
	function lod_batchno()
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			type:"batchload",
			prdctid:document.getElementById("txtitmcode").value,
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
			
	
			
	function load_manufctrdate()
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			type:"manufactre",
			itmcode:document.getElementById("txtitmcode").value,
			btchno:document.getElementById("selectbatch").value,
			
		},
		function(data,status)
		{
			/*var val=data.split("@");
			document.getElementById("txtmanufactrdate").value=val[0];
			document.getElementById("txtexpiry").value=val[1];
			document.getElementById("txtstock").value=val[2];
			document.getElementById("txtrate").value=val[3];
			*/
			var val=data.split("@");
			
			var dy=parseInt(val[4]);
			document.getElementById("txtmanufactrdate").value=val[0];
			document.getElementById("txtexpiry").value=val[1];
			document.getElementById("txtstock").value=val[2];
			document.getElementById("txtrate").value=val[3];
			$("#txtexpiry").css("background","");
			if(dy>0 && dy<31)
			{
				//alert(dy);
				$("#txtexpiry").css("background","#FEA793");
			}
		})
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
			
	
	function numentry(id) ///only numeric value entry
	{
		num=document.getElementById(id);
		numx=/^[0-9]+$/;
		if(!num.value.match(numx))
		  {
			num.value="";
		 }
	}
			
	function calc(val) ///for calculation
	{
		var a=document.getElementById("txtcrdtamt").value;
		var res=a-val
		document.getElementById("txtblnce").value=res;
	}
			
			
	function insert_data_temp()
	{
		///////For Check Blank fields//
		var jj=1;
		
		var pat_type=document.getElementById("bill_type").value;
		if(pat_type==0)
		{
			alert("Please select a Bill Type..");
			jj=0;
			$("#bill_type").focus();
		}
		var billno=document.getElementById("txtbilno").value;
		 if(billno==0)
		 {
			 jj=0;
			 alert("Please select a Bill No ");
			 document.getElementById("txtbilno").focus();
		 }
	    
	    var panme=document.getElementById("txtcustnm").value;
	    if(panme=="")
	    {
			alert("Please enter the Customer name..");
			jj=0;
			$("#txtcustnm").focus();
		}
		
		 var prdtct=document.getElementById("txtitmname").value;
		 if(prdtct=='')
		 {
			 jj=0;
			 alert("Please select a Item Name");
			 document.getElementById("txtitmname").focus();
		 }
		 var btch=document.getElementById("selectbatch").value;
		 if(btch=='0')
		 {
			 jj=0;
			 alert("Please select a Batch Name");
			 document.getElementById("selectbatch").focus();
		 } 
		 var mrp=document.getElementById("txtrate").value;
		 if(mrp=='')
		 {
			 jj=0;
			 alert("MRP Can not be Blank..");
			 $("#txtrate").focus();
		 }
		 
		 var qnt=document.getElementById("txtqnt").value;
		 if(qnt=='')
		 {
			 jj=0;
			 alert("Please enter the Quantity...");
			 $("#txtqnt").focus();
		 }
		 
		 var qntonhand=parseInt(document.getElementById("txtstock").value);
		 var qnt=parseInt(document.getElementById("txtqnt").value);
		 var slqnt=qntonhand-qnt;
		 
		 if(slqnt<0)
		 {
			 jj=0;
			 alert("Given Quantity can not be greater than Stock..");
			 $("#txtqnt").focus();
		 }
		 
		  if(qnt<1)
		 {
			  jj=0;
			  alert("Sale Quantity Can not be zero");
			  $("#txtqnt").focus();
		 }
		 
		var chk=document.getElementsByClassName("imp")
		for(var i=0;i<chk.length;i++)
		if(chk[i].value=="")
		{
			jj=0;
			document.getElementById(chk[i].id).placeholder="Can not be Blank";	
		}
		///////end ////////
		
		if(jj==1)
		{
			//$("#loader").show();
			$.post("pages/ph_insert_data.php",
			{
				type:"saletemp",

				billno:document.getElementById("txtbilno").value,
				entrydate:document.getElementById("txtdate").value,
				itmcode:document.getElementById("txtitmcode").value,
				batchno:document.getElementById("selectbatch").value,
				expiry:document.getElementById("txtexpiry").value,
				quantity:document.getElementById("txtqnt").value,
				rate:document.getElementById("txtrate").value,
			},
			function(data,status)
			{
				//$("#loader").hide();
				//alert(data);
				
				if(data>0)
				{
				  alert("Not sufficient stock for sale");
				  $("#txtstock").val(data);
			    }
			    
				load_selectedproduct();
				calculate_amount();
				calculate_vat();
				document.getElementById("txtqnt").value='';
				document.getElementById("txtitmname").value='';
				document.getElementById("txtitmname").focus();
				//$("#bill_type").attr("disabled",true);
			})
		}
	}
			
	function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
		
					 
		var ttlamt=document.getElementById("txttlamnt").value;
		 if(ttlamt==0 || ttlamt=='')
		 {
			 jj=0;
			 alert("Total Amount Can not be Zero(0)..");
			 document.getElementById("txtcustnm").focus();
		 }
	
		var chk=document.getElementsByClassName("imp");
		for(var i=0;i<chk.length;i++)
		if(chk[i].value=="")
		{
			jj=0;
			document.getElementById(chk[i].id).placeholder="Can not be Blank";	
		}
		if(parseInt($("#txtpaid").val())>parseInt($("#txtafterdis").val()))
		{
			jj=0;
			$("#txtpaid").focus();
		}
		if(parseInt($("#txtpaid").val())<0)
		{
			alert("Paid Amount Can not  be less than Zero(0)..");
			jj=0;
			$("#txtpaid").focus();
		}
		
		
		
		
		
		if(jj==1)
		{
		///////end ////////
		$("#btn5").attr("disabled",true);
		//calc_adjustment($("#txtadjust").val(),'');
		 $.post("pages/ph_insert_data.php",
		  {
			  type:"salefinal",
			  billtype:$("#bill_type").val(),
			  ref_by:$("#ref_by").val(),
			  pat_typ:$("#pat_typ").val(),
			  blno:$("#txtbilno").val(),
			  entrydate:$("#txtdate").val(),
			  net:$("#net").val(),
			  discountprcnt:$("#txtdiscount").val(),
			  aftrdscnt:$("#txtafterdis").val(),
			  paidamont:$("#txtpaid").val(),
			  balance:$("#txtbalnce").val(),
			  customername:$("#txtcustnm").val(),
			  custphone:$("#txtphone").val(),
			  uhid:$("#uhid").val(),
			  btnvalue:$("#btn5").val(),
			  adjustmt:$("#txtadjust").val(),
			  ind_num:$("#indno").val(),
		  },
		  function(data,status)
		   {
			  
				   $("#txtbilno").val(data);
				   alert("Data Saved");
				   document.getElementById("txtqnt").value='';
				   $("#btn8").attr("disabled",false);
				  // $("#button3").attr("disabled",true);
				   $("#btn8").focus();
				  
		   })
		}
	}
	function calculate_amount()
	{
		$.post('pages/global_load_g.php',
		{
			pat_typ:1,
			user:$("#user").text().trim(),
			type:"calculateamt",
			billno:document.getElementById("txtbilno").value,
			
		},
		function(data,status)
		{
			//alert(data);
			var val=data.split("@@");
			document.getElementById("txttlamnt").value=val[0];
			document.getElementById("net").value=val[1];
			document.getElementById("txtafterdis").value=val[1];
			document.getElementById("txtpaid").value=val[1];
			document.getElementById("txtbalnce").value=0;
			document.getElementById("txtdiscount").value=val[2];
			setTimeout(function(){show_crdtamt();},100);
		})
	}
	function show_crdtamt()
	{
		var bltype=document.getElementById("bill_type").value;
		var crdt=document.getElementById("txtafterdis").value;
		//alert(bltype);
		if(bltype==2)
		{
			document.getElementById("txtpaid").value=0;
			document.getElementById("txtbalnce").value=crdt;
		}
		
	}
	
	
	function check_sale_qnty()
	{
		$.post('pages/ph_load_display_ajax.php',
		{
			type:"chk_sale_qnty",
			blno:$("#txtbilno").val(),
			batchno:0,
			
		},
		function(data,status)
		{
			//alert(data);
			
			if(data==1)
			{
				alert("Red mark Items have not sufficient stock..Please check again");
				load_selectedproduct();
			}
			else
			{
				insert_data_final();
			}
			
		})
	}
	
	function calculate_vat(bill)
	{
		$.post('pages/ph_load_display_ajax.php',
		{
			type:"calc_vat",
			bill:$("#txtbilno").val(),
		},
		function(data,status)
		{
			document.getElementById("gst").value=data;
			calc_discount($("#txtdiscount").val(),'');
		})
	}
	
	function calc_discount(val,e) ///for calculation
	{
		var bltype=document.getElementById("bill_type").value;
		var unicode=e.keyCode? e.keyCode : e.charCode; 	
		if(unicode=="13")
		{
			
			if(bltype==2)
			{
			 //$("#btn5").focus();
			 $("#txtadjust").focus();
		    }
		    else
		    {
				//$("#txtpaid").focus();
				$("#txtadjust").focus();
			}
		}
		else
		{
			/*
			var a=document.getElementById("net").value;
			var vdamt=(a*val)/100;
			var res=a-vdamt;
			res=Math.ceil(res);
			res = res.toFixed(2);
			document.getElementById("txtafterdis").value=res;
			if(bltype==2)
			{
				$("#txtpaid").attr("disabled",true);
				document.getElementById("txtpaid").value=0;
				document.getElementById("txtbalnce").value=res;
			}
			else
			{
				$("#txtpaid").attr("disabled",false);
				document.getElementById("txtpaid").value=0;
				document.getElementById("txtbalnce").value=res;
			}
			*/
			if(val=="")
			{
				val=0;
			}
			var a=document.getElementById("net").value;
			var res=(parseInt(a)-parseInt(val));
			if(bltype==1)
			{
				document.getElementById("txtafterdis").value=res;
				document.getElementById("txtpaid").value=res;
				document.getElementById("txtbalnce").value=0;
			}
			else if(bltype==2)
			{
				document.getElementById("txtafterdis").value=res;
				document.getElementById("txtpaid").value=0;
				document.getElementById("txtbalnce").value=res;
			}
		}
	}
			
	function calc_discount1(val,e) ///for calculation
	{
		var unicode=e.keyCode? e.keyCode : e.charCode; 	
		if(unicode=="13")
		{
			$("#btn5").focus();
		}
		else
		{
			var a=document.getElementById("txtafterdis").value;
			var vdamt=document.getElementById("txtpaid").value;
			var vadjust=document.getElementById("txtadjust").value;
			
			var res=a-vdamt-vadjust;
			res = res.toFixed(2);
			document.getElementById("txtbalnce").value=res;
		}
	}
	
	function calc_adjustment(val,e) ///for calculation
	{
		var unicode=e.keyCode? e.keyCode : e.charCode; 	
		var bltype=document.getElementById("bill_type").value;
		
		var a=document.getElementById("txtafterdis").value;
		var vadjus=document.getElementById("txtadjust").value;
		var res=a-vadjus;
		var bal=0;
		res = res.toFixed(2);
		bal=a-vadjus-res;
		
		if(bltype==1)
		{
			document.getElementById("txtpaid").value=res;
			document.getElementById("txtbalnce").value=bal;
		}
		else if(bltype==2)
		{
			document.getElementById("txtpaid").value=bal;
			document.getElementById("txtbalnce").value=res;
		}
		if(unicode=="13")
		{
			$("#btn5").focus();
		}
	}		
			  
	function search_data()
	{
		
		$.post('pages/ph_load_display_ajax.php',
		{
			 type:"searchbill",
			 blno:$("#txtupdtid").val(),
		},
		function(data,status)
		{
		   
			var val=data.split("@");
			$("#txtbilno").val(val[0]);
			$("#txtdate").val(val[1]);
			$("#txtcustnm").val(val[2]);
			$("#txtphone").val(val[3]);
			$("#txttlamnt").val(val[4]);
			$("#gst").val(val[5]);
			$("#net").val(val[6]);
			$("#txtdiscount").val(val[7]);
			$("#txtafterdis").val(val[8]);
			$("#txtpaid").val(val[9]); 
			$("#txtbalnce").val(val[10]);
			$("#txtcabin").val(val[11]);
			$("#btn5").val("Update");
			$("#btn8").attr("disabled", false);
			$("#btn8").focus();
			load_selectedproduct();
		})
	}
			
	function delete_data(itmcode,btchno)
	{
		$.post('pages/ph_load_delete.php',
		{
			type:"sale_item_delete",
			itmcode:itmcode,
			btchno:btchno,
			billno:document.getElementById("txtbilno").value,
		},
		function(data,status)
		{
			load_selectedproduct();
			calculate_amount();
			calculate_vat();
		})
	}
	
	function delete_all()
	{
	   $.post('pages/load_delete.php',
		{
		   type:"delall",
		},
		function(data,status)
		{
			load_selectedproduct();
		}) 
	}
	
	function print_bill()
	{
		if($("#txttlamnt").val()=="0")
		{
			alert("Total Amount Can not be Zero(0)..");
			$("#btn8").attr("disabled", true);
		}
		else
		{
			
				//load_id();
				var billno=$("#txtbilno").val();
				url="pages/sale_bill_print.php?billno="+billno;
				//url="pages/sale_bill_print_zebra.php?billno="+billno;
				wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
				$("#btn8").attr("disabled", true);
				setTimeout("location.reload(true);",1000);
			
		}
	}
	
	
	function next_tab(id,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13) 
		{
			if($("#"+id).val()!="0")
			{
				$("#txtqnt").focus();
			}
			else
			{
				//$("#button3").focus();
			}
		}
	}	
	</script>
</div>
