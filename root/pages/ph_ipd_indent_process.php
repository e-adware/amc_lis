<?php
$pid	=base64_decode($_GET['pId']);
$opd	=base64_decode($_GET['oPd']);
$ino	=base64_decode($_GET['iNo']);

$fdate	=$_GET['fdate'];
$tdate	=$_GET['tdate'];
$ward	=$_GET['ward'];
$pin	=$_GET['pin'];
$stat	=$_GET['stat'];

$str="";
if($_GET["fdate"])
{
	$str.="&fdate=$fdate";
}
if($_GET["tdate"])
{
	$str.="&tdate=$tdate";
}
if($_GET["ward"])
{
	$str.="&ward=$ward";
}
if($_GET["pin"])
{
	$str.="&pin=$pin";
}
if($_GET["stat"])
{
	$str.="&stat=$stat";
}

$pat_info=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name`,`dob`,`age`,`age_type`,`sex` FROM `patient_info` WHERE `patient_id`='$pid'"));
if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pid' and `opd_id`='$opd' "));
$entry_date_time=convert_date_g($dt_tm['date'])." ".convert_time($dt_tm['time']);

$billNo="";
$bilNum=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_no` FROM `patient_medicine_detail` WHERE `patient_id`='$pid' AND `pin`='$opd' AND `indent_num`='$ino' AND `bill_no`!=''"));
if($bilNum)
{
	$billNo=$bilNum['bill_no'];
}

?>
<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header">IPD Patient Indent Process</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed table-bordered table-report" style="background: snow">
		<tr>
			<th>UNIT NO.</th>
			<th>IPD Bill No</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Admission Date Time</th>
		</tr>
		<tr>
			<td><?php echo $pid;?></td>
			<td><?php echo $opd;?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat_info['sex'];?></td>
			<td><?php echo $entry_date_time;?></td>
		</tr>
	</table>
	<table class="table table-condensed table-report">
		<tr>
			<td colspan="7" style="padding:1px;"></td>
		</tr>
		<tr style="display:none;">
			<th colspan="2">Item Name</th>
			<th>Batch No</th>
			<th colspan="3"><!--Barcode No--></th>
		</tr>
		<tr style="display:none;">
			<td colspan="2">
				<input type="text" id="r_doc" class="span5 intext" onfocus="load_refdoc1()" onkeyup="load_refdoc(this.value,event)" onblur="javascript:$('#ref_doc').fadeOut(500)" value="" placeholder="Item Name OR Item Code" />
				<input type="hidden" id="doc_id" />
				<div id="ref_doc" style="padding:8px;width:600px;" align="center">
					
				</div>
			</td>
			<td>
				
			</td>
			<td colspan="3">
				<input type="hidden" id="barcode_id" Placeholder="Barcode No" />
			</td>
		</tr>
		<tr>
			<th>Batch no</th>
			<th>Expiry Date</th>
			<th>Indent Qnt</th>
			<th>Stock</th>
			<th>Quantity</th>
			<th>MRP</th>
			<th></th>
		</tr>
		<tr>
			<td>
				<input type="text" id="hguide" class="span2 intext" onfocus="hguide_up(this.value,event,'batch')" onkeyup="hguide_up(this.value,event,'batch')" onblur="javascript:$('#hguide_div').fadeOut(500)" placeholder="Batch No" />
				<input type="hidden" id="hguide_id" placeholder="Batch No" />
				<input type="hidden" id="bch_qnt" placeholder="Batch Quantity" />
				<input type="hidden" id="bch_gst" placeholder="Batch GST" />
				<input type="hidden" id="bch_exp" placeholder="Batch Exp Date" />
				<div id="hguide_div" style="padding:8px;width:400px;" align="center">
					
				</div>
			</td>
			<td>
				<input type="text" class="span2" id="exp_dt" onkeyup="exp_date(this,event)" maxlength="7" placeholder="YYYY-MM" readonly />
			</td>
			<td>
				<input type="text" class="span2" id="indent" placeholder="Indent Qnt" readonly />
			</td>
			<td>
				<input type="text" id="stock" class="span1 intext" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');qnt_check(this.value,event)" placeholder="Stock" disabled="" />
			</td>
			<td>
				<input type="text" id="qnt" class="span1 intext" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');qnt_check(this.value,event)" placeholder="Quantity" />
			</td>
			<td>
				<input type="text" class="span1 bch_mrp intext" id="bch_mrp" onkeyup="checkDecimal(this,event)" placeholder="MRP" readonly />
			</td>
			<td>
				<button type="button" id="btn_add" class="btn btn-primary" onclick="add_item()">Add</button>
			</td>
		</tr>
		<tr>
			<td colspan="7" style="border-top:1px solid;padding:0px;max-height:350px;overflow-y:scroll;">
				<div id="temp_item"></div>
			</td>
		</tr>
		<tr>
			<th>Total</th>
			<th>Discount</th>
			<th colspan="2">Paid</th>
			<th colspan="2">Balance</th>
			<th>Pay Mode</th>
		</tr>
		<tr>
			<td>
				<input type="text" class="span2 intext" id="total" placeholder="Total" readonly />
			</td>
			<td>
				<input type="text" class="span2 intext" id="discount" onkeyup="checkDecimal(this,event);disc_amt(event)" placeholder="Discount" />
				<input type="hidden" class="span2 intext" id="disc_amt" value="0" placeholder="Discount Amount" />
			</td>
			<td colspan="2">
				<input type="text" class="span2 intext" id="paid" onkeyup="checkDecimal(this,event);paid_amt(event)" placeholder="Paid" />
			</td>
			<td colspan="2">
				<input type="text" class="span2 intext" id="balance" placeholder="Balance" readonly />
			</td>
			<td>
				<select class="span1" id="payMode">
					<option value="Cash">Cash</option>
					<option value="Card">Card</option>
					<option value="UPI">UPI</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="7" style="text-align:center;border-top:1px solid;">
				<button type="button" class="btn btn-primary" id="btn_save" onclick="save_final()">Save</button>
				<button type="button" class="btn btn-danger" onclick="window.location='?param=<?php echo base64_encode(172).$str; ?>'">Go Back</button>
				<button type="button" class="btn btn-success" onclick="print_receipt()" style="display:;">Print Bill</button>
				<button type="button" class="btn btn-info" onclick="print_indent()">Print Indent</button>
			</td>
		</tr>
	</table>
	<div id="msgg" style="display:none;background:#FFFFFF;position:fixed;color:#990909;font-size:22px;left:35%;top: 20%;padding: 25px;border-radius: 3px;box-shadow: 0px 1px 15px 1px #c57676;z-index:1000;"></div>
	<div id="loader" style="display:none;top:50%;position:fixed;"></div>
	<div id="loadMedication"></div>
	<input type="hidden" id="chk_val1" value="0" />
	<input type="hidden" id="chk_val2" value="0" />
	<input type="hidden" id="ph" value="1" />
	<input type="hidden" id="tot" value="0" />
	<input type="hidden" id="all_rate" value="0" />
	<input type="hidden" id="all_gst" value="0" />
	<input type="hidden" id="bill_no" value="<?php echo $billNo;?>" />
	<input type="hidden" id="pid" value="<?php echo base64_encode($pid);?>" />
	<input type="hidden" id="opd" value="<?php echo base64_encode($opd);?>" />
	<input type="hidden" id="ino" value="<?php echo base64_encode($ino);?>" />
</div>
<link rel="stylesheet" href="../css/loader.css" />
<style>
#loadMedication
{
	display: none;
	position: fixed;
	width: 450px;
	max-height: 200px;
	overflow-y: scroll;
	z-index: 99999;
	right: 0px;
	bottom: 2px;
	background: #FFF;
}
</style>

<script src="../js/jquery.gritter.min.js"></script>
<link rel="stylesheet" href="../css/jquery.gritter.css" />

<script src="../jss/globalFunctions.js"></script>

<script>
	$(document).ready(function()
	{
		load_item_details();
	});
	function load_item_details()
	{
		$.post("pages/ph_ipd_indent_list_ajax.php",
		{
			ph:$("#ph").val().trim(),
			pid:$("#pid").val().trim(),
			opd:$("#opd").val().trim(),
			ino:$("#ino").val().trim(),
			type:2
		},
		function(data,status)
		{
			//alert(data);
			$("#loadMedication").slideDown(400).html(data);
		});
	}
	function qnt_check(val,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==27)
		{
			$("#pat_name").focus();
		}
		else if(unicode==13)
		{
			if(val!="")
			{
				if(parseInt($("#qnt").val())==0 || parseInt($("#qnt").val())<0)
				{
					$("#qnt").focus();
					$("#qnt").css('border','1px solid #FF0000');
				}
				else if(parseInt($("#qnt").val())>parseInt($("#bch_qnt").val()))
				{
					$("#qnt").focus();
					$("#qnt").css('border','1px solid #FF0000');
				}
				else
				{
					$("#btn_add").focus();
					//$("#bch_mrp").focus().select();
					$("#qnt").css('border','');
				}
			}
			else
			{
				$("#qnt").css('border','1px solid #FF0000');
			}
		}
		else
		{
			$("#qnt").css('border','');
		}
	}
	function load_page(pid,opd,ino,fdate,tdate,ward)
	{
		window.location="index.php?param="+btoa(172)+"&pId="+btoa(pid)+"&oPd="+btoa(opd)+"&iNo="+btoa(ino)+"&fdate="+btoa(fdate)+"&tdate="+btoa(tdate)+"&ward="+btoa(ward);
	}
	function print_receipt()
	{
		//var url="pages/ph_indent_bill_print.php?pId="+btoa($("#pid").val().trim())+"&oPd="+btoa($("#opd").val().trim())+"&iNo="+btoa($("#ino").val().trim());
		var url="pages/sale_bill_print.php?billno="+btoa($("#bill_no").val().trim())+"&sub_id="+btoa($("#ph").val().trim());
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_indent()
	{
		var url="pages/ph_indent_bill_print.php?pId="+btoa($("#pid").val().trim())+"&oPd="+btoa($("#opd").val().trim())+"&iNo="+btoa($("#ino").val().trim());
		//var url="pages/sale_bill_print.php?billno="+btoa($("#bill_no").val().trim())+"&sub_id="+btoa($("#ph").val().trim());
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function disc_amt(e)
	{
		var dis=$("#discount").val().trim();
		if(dis=="")
		{
			dis=0;
		}
		else
		{
			dis=parseFloat(dis);
		}
		var tot=$("#total").val().trim();
		if(tot=="")
		{
			tot=0;
		}
		else
		{
			tot=parseFloat(tot);
		}
		var dis_amt=((tot*dis)/100);
		$("#dis_amt").val(dis_amt);
		
		var bal=(tot-dis_amt);
		$("#paid").val("0");
		$("#balance").val(bal);
	}
	function paid_amt(e)
	{
		var tot=$("#total").val().trim();
		if(tot=="")
		{
			tot=0;
		}
		else
		{
			tot=parseFloat(tot);
		}
		
		var dis_amt=$("#disc_amt").val().trim();
		if(dis_amt=="")
		{
			dis_amt=0;
		}
		else
		{
			dis_amt=parseFloat(dis_amt);
		}
		
		var paid=$("#paid").val().trim();
		if(paid=="")
		{
			paid=0;
		}
		else
		{
			paid=parseFloat(paid);
		}
		
		var bal=(tot-dis_amt-paid);
		$("#balance").val(bal);
		//alert(bal);
	}
	function save_final()
	{
		var len=$(".all_tr").length;
		//alert(len);
		if(len<1)
		{
			$("html,body").animate({scrollTop: '10px'},500);
			$("#msgg").text("NO ITEM SELECTED");
			$("#msgg").fadeIn(500);
			setTimeout(function()
			{
				$("#msgg").fadeOut(800,function()
				{
					$("#r_doc").select();$("#r_doc").focus();
				}
			)},800);
			gritAlert("NO ITEM SELECTED",0);
		}
		if(parseFloat($("#discount").val().trim())<0 || $("#discount").hasClass("err")==true)
		{
			$("#discount").focus();
		}
		if(parseFloat($("#paid").val().trim()) > parseFloat($("#total").val().trim())  || $("#paid").hasClass("err")==true)
		{
			$("#paid").focus();
		}
		if(parseFloat($("#balance").val().trim())<0)
		{
			$("#paid").focus();
		}
		else
		{
			$("#btn_save").attr("disabled",true);
			$("#loader").show();
			var all="";
			for(var i=0; i<len; i++)
			{
				var itm=$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val();
				var bch=$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val();
				var qnt=$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val();
				var mrp=$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val();
				var amt=$(".all_tr:eq("+i+")").find('td:eq(5) input:first').val();
				var gst_per=$(".all_tr:eq("+i+")").find('td:eq(5) input:last').val();
				var gst_amt=$(".all_tr:eq("+i+")").find('td:eq(6) input:first').val();
				var expdt=$(".all_tr:eq("+i+")").find('td:eq(6) input:last').val();
				all+=itm+"@@"+bch+"@@"+qnt+"@@"+mrp+"@@"+amt+"@@"+gst_per+"@@"+gst_amt+"@@"+expdt+"@@#@#";
			}
			//alert(all);
			$.post("pages/ph_ipd_indent_list_ajax.php",
			{
				type:4,
				all:all,
				ph:$("#ph").val().trim(),
				pid:$("#pid").val().trim(),
				opd:$("#opd").val().trim(),
				ino:$("#ino").val().trim(),
				total:$("#total").val().trim(),
				discount:$("#discount").val().trim(),
				disc_amt:$("#disc_amt").val().trim(),
				paid:$("#paid").val().trim(),
				balance:$("#balance").val().trim(),
				payMode:$("#payMode").val().trim(),
				user:$("#user").text().trim()
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				let vl=JSON.parse(data);
				if(vl['response']=="1")
				{
					$("#loadMedication").empty();
					$("#bill_no").val(vl['bill_no']);
					print_receipt();
				}
				gritAlert(vl['msg'],vl['response']);
			});
		}
	}
	function add_item()
	{
		if($("#doc_id").val()=="")
		{
			$("#r_doc").focus();
		}
		else if($("#hguide_id").val()=="")
		{
			$("#hguide").focus();
		}
		else if($("#exp_dt").hasClass("err")==true)
		{
			$("#exp_dt").focus();
		}
		else if($("#qnt").val()=="")
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val())==0 || parseInt($("#qnt").val())<0)
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val())>parseInt($("#bch_qnt").val()))
		{
			$("#qnt").focus();
		}
		else
		{
			//alert("ok");
			$("#btn_add").attr("disabled",false);
			add_item_temp($("#doc_id").val(),$("#r_doc").val(),$("#hguide_id").val(),$("#qnt").val().trim(),$("#bch_mrp").val().trim(),$("#bch_gst").val().trim(),$("#bch_exp").val().trim());
			doc_v=1;
			doc_sc=0;
		}
	}
	function add_item_temp(id,itm_name,bch,qnt,rate,gst_per,exp_dt)
	{
		$("#itemm"+id).removeClass("boldred");
		$("#itemm"+id).addClass("boldgreen");
		//alert(id);
		var setColor='setColor("'+id+'")';
		var rt=(qnt*rate).toFixed(2);
		var tr_len=$('#mytable tr').length;
		var gst=0;
		if(gst_per=="")
		{
			gst_per=0;
		}
		else
		{
			gst_per=parseFloat(gst_per);
		}
		gst=rt-(rt*(100/(100+gst_per)));
		gst=gst.toFixed(2);
		if(tr_len==0)
		{
			var test_add="<table class='table table-condensed table-report' id='mytable'>";
			test_add+="<tr style='background-color:#cccccc'><th>Sl No</th><th>Medicine</th><th>Batch No</th><th>Quantity</th><th>MRP</th><th>Amount</th><th style='width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr "+id+bch+"'>";
			test_add+="<td>1</td>";
			test_add+="<td>"+itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' /></td>";
			test_add+="<td>"+bch+"<input type='hidden' value='"+bch+"' class='batch' /></td>";
			test_add+="<td><input type='text' value='"+qnt+"' class='qnt span1' style='padding:0px 3px;margin-bottom:0px;' onkeyup='manage_qnt(this,event)' /></td>";
			test_add+="<td>"+rate+"<input type='hidden' value='"+rate+"' class='mrp' /></td>";
			test_add+="<td><span class='rate_str'>"+rt+"</span><input type='hidden' value='"+rt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' /></td>";
			test_add+="<td style='text-align:center;'><input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt();"+setColor+"' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span></td>";
			test_add+="</tr>";
			test_add+="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
			test_add+="</table>";
			//test_add+="<div style='display:none;text-align:center;background-color:#cccccc;font-weight:bold;font-size:15px;padding:5px' id='test_hidden_price'>Total:<span id='test_total_hidden'></span></div>";
			
			$("#temp_item").html(test_add);
			tr_len++;
		
			var tot=0;
			var gst_amt=0;
			var dis_amt=0;
			var tot_ts=document.getElementsByClassName("all_rate");
			var tot_gst=document.getElementsByClassName("all_gst");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseFloat(tot_ts[j].value);
				gst_amt=gst_amt+parseFloat(tot_gst[j].value);
			}
			dis_amt=0;
			tot=tot-dis_amt;
			//tot=Math.floor(tot);
			$("#final_rate").text(tot.toFixed(2));
			//tot=Math.round(tot);
			$("#pay_cash").val('0');
			$("#balance").val(tot);
			$("#tot").val(tot);
			$("#total").val(tot);
			//$("#gst").val(gst_amt);
			$("#paid").val("0");
			$("#discount").val("0");
			//$("#adjust").val("0");
			//$("#test").val("");
		}
		else
		{
			
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id");
			
			for(var i=0;i<test_l.length;i++)
			{
				if(test_l[i].value==id+bch)
				{
					t_ch=1;
				}
			}
			if(t_ch)
			{

				$("#temp_item").css({'opacity':'0.5'});
				$("#msgg").text("ALREADY SELECTED SAME ITEM AND BATCH NO.");
				//var x=$("#temp_item").offset();
				//var w=$("#msgg").width()/2;
				//$("#msgg").css({'top':'50%','left':'50%'});
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(800,function(){$("#temp_item").css({'opacity':'1.0'});$("#r_doc").select();$("#r_doc").focus();
				})},800);
				
			}			
			else
			{
		   
			var tr=document.createElement("tr");
			tr.setAttribute("class","all_tr "+id+bch);
			var td=document.createElement("td");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var td4=document.createElement("td");
			var td5=document.createElement("td");
			var td6=document.createElement("td");
			//var tbody=document.createElement("tbody");
			var tbody="";
			
			td.innerHTML=tr_len;
			td1.innerHTML=itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' />";
			td2.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='batch' />";
			td3.innerHTML="<input type='text' value='"+qnt+"' class='qnt span1' style='padding:0px 3px;margin-bottom:0px;' onkeyup='manage_qnt(this,event)' />";
			td4.innerHTML=rate+"<input type='hidden' value='"+rate+"' class='mrp' />";
			td5.innerHTML="<span class='rate_str'>"+rt+"</span><input type='hidden' value='"+rt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' />";
			td6.innerHTML="<input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt();"+setColor+"' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span>";
			td6.setAttribute("style","text-align:center;");
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			tr.appendChild(td5);
			tr.appendChild(td6);
			//tbody.appendChild(tr);
			document.getElementById("mytable").appendChild(tr);
			var tot=0;
			var gst_amt=0;
			var dis_amt=0;
			var tot_ts=document.getElementsByClassName("all_rate");
			var tot_gst=document.getElementsByClassName("all_gst");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseFloat(tot_ts[j].value);
				gst_amt=gst_amt+parseFloat(tot_gst[j].value);
			}
			gst_amt=gst_amt.toFixed(2);
			dis_amt=0;
			tot=tot-dis_amt;
			var new_tr="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
			$("#new_tr").remove();
			$('#mytable tr:last').after(new_tr);
			//tot=Math.floor(tot);
			$("#final_rate").text(tot.toFixed(2));
			//tot=Math.round(tot);
			$("#pay_cash").val('0');
			$("#balance").val(tot);
			$("#tot").val(tot);
			$("#total").val(tot);
			//$("#gst").val(gst_amt);
			$("#paid").val("0");
			$("#discount").val("0");
			//$("#adjust").val("0");
			}
		}
		//$("#txtbillno").attr("disabled",true);
		//$("#txtreason").attr("disabled",true);
		//$("#selectbatch").val("");
		//$("#txtcntrname").val("");
		//$("#txtqnt").val("");
		//setTimeout(function(){$("#txtcustnm").val("").focus();},300);
		//alert(disc);
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
		//-------------------------------------------------------------
		$("#doc_id").val('');
		$("#r_doc").val('');
		$("#indent").val('');
		$("#hguide").val('');
		$("#hguide_id").val('');
		$("#bch_qnt").val('');
		$("#bch_mrp").val('');
		$("#bch_gst").val('');
		$("#stock").val('');
		$("#bch_exp").val('');
		$("#exp_dt").val('');
		$("#qnt").val('');
		$("#ph").attr('disabled',true);
		//setTimeout(function(){$("#r_doc").focus();},500);
		//change_bill(2);
	}
	function setColor(id)
	{
		$("#itemm"+id).removeClass("boldgreen");
		$("#itemm"+id).addClass("boldred");
	}
	function set_amt()
	{
		var tot=0;
		var gst_amt=0;
		var tot_ts=document.getElementsByClassName("all_rate");
		var tot_gst=document.getElementsByClassName("all_gst");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseFloat(tot_ts[j].value);
			gst_amt=gst_amt+parseFloat(tot_gst[j].value);
		}
		gst_amt=gst_amt.toFixed(2);
		var new_tr="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
		$("#new_tr").remove();
		$('#mytable tr:last').after(new_tr);
		
		$("#final_rate").text(tot.toFixed(2));
		tot=Math.round(tot);
		$("#tot").val(tot);
		$("#total").val(tot);
		//$("#gst").val(gst_amt);
		$("#paid").val("0");
		$("#discount").val("0");
		//$("#adjust").val("0");
		$("#balance").val(tot);
		//$("#txtcustnm").focus();
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
	}
	function manage_qnt(ths,e)
	{
		var val=$(ths).val();
		if(/\D/g.test(val))
		{
			val=val.replace(/\D/g,'');
			$(ths).val(val);
		}
		if(val=="")
		{
			val=0;
		}
		else
		{
			val=parseInt(val);
		}
		
		var mrp=parseFloat($(ths).closest('tr').find('.mrp').val().trim());
		var gst_per=parseFloat($(ths).closest('tr').find('.gst_per').val().trim());
		var amt=val*mrp;
		amt=amt.toFixed(2);
		
		var gst=0;
		gst=amt-(amt*(100/(100+gst_per)));
		gst=gst.toFixed(2);
		
		$(ths).closest('tr').find('.all_rate').val(amt);
		$(ths).closest('tr').find('.rate_str').text(amt);
		$(ths).closest('tr').find('.all_gst').val(gst);
		set_amt();
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==27)
		{
			$("#pat_name").focus();
		}
		if(unicode==13)
		{
			var rowIndex=$('#mytable tr').index($(ths).closest('tr'));
			rowIndex++;
			//alert(rowIndex);
			if($('#mytable tr:eq('+rowIndex+') td:eq(3)').find('.qnt').length>0)
			{
				$('#mytable tr:eq('+rowIndex+') td:eq(3)').find('.qnt').focus().select();
			}
			else
			{
				$("#uhid").focus();
			}
		}
	}
	//------------------------item search---------------------------------//
	function load_refdoc1()
	{
		//$("#ref_doc").fadeIn(200);
		//$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},200);
	}
	var doc_tr=1;
	var doc_sc=0;
	function load_refdoc(val,e,typ)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		//alert(unicode);
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				if(unicode==27)
				{
					if($("#pat_name:visible").length>0)
					{
						$("#pat_name").focus();
					}
					else
					{
						$("#pat_save_btn").focus();
					}
				}
				else
				{
					$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
					$("#ref_doc").fadeIn(200);
					$.post("pages/ph_ipd_indent_list_ajax.php",
					{
						type:2,
						ph:$("#ph").val().trim(),
						pid:$("#pid").val().trim(),
						opd:$("#opd").val().trim(),
						ino:$("#ino").val().trim(),
						val:val,
					},
					function(data,status)
					{
						$("#ref_doc").html(data);	
						doc_tr=1;
						doc_sc=0;
					});
				}
			}
			else if(unicode==40)
			{
				var chk=doc_tr+1;
				var cc=document.getElementById("doc"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr+1;
					$("#doc"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','font-size':'15px','transition':'all .2s'});
					var doc_tr1=doc_tr-1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'13px','transition':'all .2s'});
					var doc_tr2=doc_tr1-1;
					//$("#doc"+doc_tr2).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'11px','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						$("#ref_doc").scrollTop(doc_sc)
						doc_sc=doc_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=doc_tr-1;
				var cc=document.getElementById("doc"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr-1;
					$("#doc"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','font-size':'15px','transition':'all .2s'});
					var doc_tr1=doc_tr+1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'13px','transition':'all .2s'});
					var doc_tr2=doc_tr1+1;
					//$("#doc"+doc_tr2).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'11px','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						doc_sc=doc_sc-30;
						$("#ref_doc").scrollTop(doc_sc)
					}
				}
			}
		}
		else
		{
			$("#r_doc").css('border','');
			var cen_chk1=document.getElementById("chk_val2").value;
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
				var doc_naam=docs[2].trim();
				var ind=docs[3].trim();
				$("#doc_info").fadeIn(200);
				doc_load(docs[1],doc_naam,ind);
			}
		}
	}
	function doc_load(id,name,ind)
	{
		$("#r_doc").val(name);
		$("#doc_id").val(id);
		$("#indent").val(ind);
		//$("#doc_info").html("");
		$("#ref_doc").fadeOut(200);
		$("#hguide").val('');
		$("#hguide_id").val('');
		$("#bch_qnt").val('');
		$("#bch_mrp").val('');
		$("#bch_gst").val('');
		$("#stock").val('');
		$("#bch_exp").val('');
		$("#qnt").val('');
		$("#hguide").focus();
		$("#chk_val2").val(1);
		doc_tr=1;
		doc_sc=0;
	}
	//------------------------item search end---------------------------------//
	//-----------------------------------------Load Doctor List Onfocus-------------------------------||
	function hguide_focus()
	{
		$("#hguide_div").fadeIn(200);
		$("#hguide").select();
		setTimeout(function(){ $("#chk_val2").val(1)},200);
	}
	//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||

	function hguide_up(val,e,typ)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				if(unicode==27)
				{
					$("#pat_name").focus();
				}
				else
				{
					$("#hguide_div").html("<img src='../images/ajax-loader.gif' />");
					$("#hguide_div").fadeIn(200);
					$.post("pages/ph_ipd_indent_list_ajax.php",
					{
						val:val,
						item_id:$("#doc_id").val().trim(),
						type:3,
						ph:$("#ph").val().trim(),
					},
					function(data,status)
					{
						$("#hguide_div").html(data);	
						doc_tr=1;
						doc_sc=0;
					});
				}
			}
			else if(unicode==40)
			{
				var chk=doc_tr+1;
				var cc=document.getElementById("hg"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr+1;
					$("#hg"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr-1;
					$("#hg"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						$("#hguide_div").scrollTop(doc_sc)
						doc_sc=doc_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=doc_tr-1;
				var cc=document.getElementById("hg"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr-1;
					$("#hg"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr+1;
					$("#hg"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						doc_sc=doc_sc-30;
						$("#hguide_div").scrollTop(doc_sc)
					}
				}
			}
			
		}
		else
		{
			var cen_chk1=document.getElementById("chk_val2").value;
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("dvhguide"+doc_tr).innerHTML.split("#");
				var bch=docs[1].trim();
				var qnt=docs[2].trim();
				var mrp=docs[3].trim();
				var gst=docs[4].trim();
				var exp_dt=docs[5].trim();
				//alert(bch+"-"+qnt+"-"+mrp+"-"+exp_dt);
				hguide_load(bch,qnt,mrp,gst,exp_dt);
				$("#hguide_info").fadeIn(200);
				
			}
		}
	}
	function hguide_load(bch,qnt,mrp,gst,exp_dt)
	{
		$("#hguide").val(bch);
		$("#hguide_id").val(bch);
		$("#bch_qnt").val(qnt);
		$("#stock").val(qnt);
		$("#bch_mrp").val(mrp);
		$("#bch_gst").val(gst);
		$("#bch_exp").val(exp_dt);
		let expdt=exp_dt.split("-");
		$("#exp_dt").val(expdt[0]+"-"+expdt[1]);
		$("#hguide_info").html("");
		$("#hguide_div").fadeOut(200);
		//$("#exp_dt").focus().select().addClass("err");
		$("#qnt").focus();
		doc_tr=1;
		doc_sc=0;
	}
	//-----------------------------------------end-----------------------------------//
</script>
<style>
.boldgreen
{
	font-weight:bold;
	color:green;
}
.boldred
{
	font-weight:bold;
	color:red;
}
</style>
