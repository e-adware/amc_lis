$(document).ready(function()
{
	$("#ref_by").select2({ theme: "classic" });
	//$("#ref_by").select2("focus");
	
	if($("#txtupdtid").val()!="0")
	{
		load_sale_bill();
	}
	
	$("#ref_by").on("select2:close",function(e)
	{
		//setTimeout(function(){$("#contact").focus();},200);
		setTimeout(function(){$("#addr").focus();},200);
	});
});

function load_sale_bill()
{
	$.post("pages/sale_ajax.php",
	{
		type:"load_sale_bill",
		bill:$("#txtupdtid").val().trim(),
		bill_id:$("#bill_id").val().trim()
	},
	function(data,status)
	{
		//alert(data);
		$("#temp_item").html(data);
		load_bill_det();
		set_amt();
	})
}

function load_bill_det()
{
	$.post("pages/sale_ajax.php",
	{
		type:"load_bill_det",
		bill:$("#txtupdtid").val().trim()
	},
	function(data,status)
	{
		//alert(data);
		var vl=data.split("@@");
		$("#uhid").val(vl[0]);
		$("#cust_name").val(vl[1]);
		$("#ref_by").val(vl[2]).trigger("change");
		$("#contact").val(vl[3]);
		$("#discount").val(vl[4]);
		$("#dis_amt").val(vl[5]);
		$("#adjust").val(vl[6]);
		$("#paid").val(vl[7]);
		$("#balance").val(vl[8]);
		$("#addr").val(vl[9]);
		$("#co").val(vl[10]);
		$("#bill_typ").val(vl[11]);
		if(vl[11]=="1")
		{
			$("#paid").attr("disabled",false);
		}
		else
		{
			$("#paid").attr("disabled",true);
		}
		$("#ph").val(vl[12]);
		$("#ph").attr("disabled",true);
	})
}
function reload_page()
{
	//window.location="processing.php?param=20";
	window.location="index.php?param="+btoa(20);
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
	//alert(id);
	var rt=(qnt*rate).toFixed(2);
	var tr_len=$('#mytable tr').length;
	var gst=0;
	gst_per=parseFloat(gst_per);
	gst=rt-(rt*(100/(100+gst_per)));
	gst=gst.toFixed(2);
	if(tr_len==0)
	{
		var test_add="<table class='table table-condensed table-bordered' id='mytable'>";
		test_add+="<tr style='background-color:#cccccc'><th>Sl No</th><th>Medicine</th><th>Batch No</th><th>Quantity</th><th>MRP</th><th>Amount</th><th style='width:5%;'>Remove</th></tr>";
		test_add+="<tr class='all_tr "+id+bch+"'>";
		test_add+="<td>1</td>";
		test_add+="<td>"+itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' /></td>";
		test_add+="<td>"+bch+"<input type='hidden' value='"+bch+"' class='batch' /></td>";
		test_add+="<td><input type='text' value='"+qnt+"' class='qnt span1' style='padding:0px 3px;margin-bottom:0px;' onkeyup='manage_qnt(this,event)' /></td>";
		test_add+="<td>"+rate+"<input type='hidden' value='"+rate+"' class='mrp' /></td>";
		test_add+="<td><span class='rate_str'>"+rt+"</span><input type='hidden' value='"+rt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' /></td>";
		test_add+="<td style='text-align:center;'><input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span></td>";
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
		tot=Math.round(tot);
		$("#total").val(tot);
		$("#gst").val(gst_amt);
		$("#paid").val(tot);
		$("#discount").val("");
		$("#adjust").val("");
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
		td6.innerHTML="<input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span>";
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
		tot=Math.round(tot);
		$("#total").val(tot);
		$("#gst").val(gst_amt);
		$("#paid").val(tot);
		$("#discount").val("");
		$("#adjust").val("");
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
	$("#hguide").val('');
	$("#hguide_id").val('');
	$("#bch_qnt").val('');
	$("#bch_mrp").val('');
	$("#bch_gst").val('');
	$("#stock").val('');
	$("#bch_exp").val('');
	$("#qnt").val('');
	$("#ph").attr('disabled',true);
	setTimeout(function(){$("#r_doc").focus();},500);
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
		$("#uhid").focus();
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
	$("#total").val(tot);
	$("#gst").val(gst_amt);
	$("#paid").val(tot);
	$("#discount").val("");
	$("#adjust").val("");
	$("#txtcustnm").focus();
	for(var i=0;i<tot_ts.length;i++)
	{
		$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
	}
}
function qnt_check(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==27)
	{
		$("#uhid").focus();
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
//--------------------------------------------------------------------//
function scroll_page()
{
	$("html,body").animate({scrollTop: '200px'},1000);
}
function change_p_type(val)
{
	//alert(val);
	if(val=="2")
	{
		$("#bill_typ").val("2");
		$("#bill_typ").attr("disabled",true);
	}
	else
	{
		$("#bill_typ").val("1");
		$("#bill_typ").attr("disabled",false);
	}
	change_bill(val);
}
function change_bill(val)
{
	if(val=="1" || val=="4" || val=="5" || val=="6")
	{
		$("#paid").attr("disabled",false);
		$("#paid").val($("#total").val());
		$("#balance").val("0");
	}
	if(val=="2" || val=="3")
	{
		$("#paid").attr("disabled",true);
		$("#paid").val("0");
		$("#balance").val($("#total").val());
	}
}
function chk_disc(id,val,e)
{
	if(/\D/g.test(val))
	{
		val=val.replace(/\D/g,'');
		$("#"+id).val(val);
	}
	if(val=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
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
	var adj=0;
	var paid=0;
	var dis_amt=0;
	var bal=0;
	dis_amt=(tot*val)/100;
	paid=tot-dis_amt;
	$("#paid").val(paid);
	$("#dis_amt").val(dis_amt);
	$("#adjust").val(adj);
	$("#balance").val(bal);
	
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#discount").val(val);
		$("#paid").val(paid);
		$("#dis_amt").val(dis_amt);
		$("#adjust").val(adj);
		$("#balance").val(bal);
		$("#adjust").focus();
	}
}
function chk_disc_amount(id,val,e)
{
	if(/\D/g.test(val))
	{
		val=val.replace(/\D/g,'');
		$("#"+id).val(val);
	}
	if(val=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
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
	var disc=0;
	disc=(val * 100 / tot);
	var adj=0;
	var paid=0;
	var dis_amt=val;
	var bal=0;
	paid=tot-dis_amt;
	$("#discount").val(disc);
	$("#paid").val(paid);
	$("#adjust").val(adj);
	$("#balance").val(bal);
	
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#dis_amt").val(dis_amt);
		$("#paid").val(paid);
		$("#adjust").val(adj);
		$("#balance").val(bal);
		$("#adjust").focus();
	}
}
function chk_adjust(id,val,e)
{
	//~ if(/\D/g.test(val))
	//~ {
		//~ val=val.replace(/\D/g,'');
		//~ $("#"+id).val(val);
	//~ }
	var reg = /^\d+(?:\.\d{1,2})?$/;
	if(!reg.test(val))
	{
		$("#"+id).css("border","1px solid #FF0000");
	}
	else
	{
		$("#"+id).css("border","");
	}
	if(val=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
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
	var dis_amt=$("#dis_amt").val().trim();
	if(dis_amt=="")
	{
		dis_amt=0;
	}
	else
	{
		dis_amt=parseFloat(dis_amt);
	}
	var adj=val;
	if(adj=="")
	{
		adj=0;
	}
	else
	{
		adj=parseFloat(adj);
	}
	var paid=0;
	var bal=0;
	paid=tot-dis_amt-adj;
	$("#paid").val(paid);
	$("#balance").val(bal);
	
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#adjust").val(adj);
		$("#"+id).css("border","");
		$("#paid").val(paid);
		$("#balance").val(bal);
		$("#paid").focus();
	}
}
function chk_paid(id,val,e)
{
	if(/\D/g.test(val))
	{
		val=val.replace(/\D/g,'');
		$("#"+id).val(val);
	}
	if(val=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
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
	var dis_amt=$("#dis_amt").val().trim();
	if(dis_amt=="")
	{
		dis_amt=0;
	}
	else
	{
		dis_amt=parseFloat(dis_amt);
	}
	var adj=$("#adjust").val().trim();
	if(adj=="")
	{
		adj=0;
	}
	else
	{
		adj=parseFloat(adj);
	}
	var paid=val;
	var bal=0;
	bal=tot-dis_amt-adj-paid;
	$("#balance").val(bal);
	
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		//$("#balance").val(bal);
		$("#btn_save").focus();
	}
}
function chk_discount(id,vl,e)
{
	if(/\D/g.test(vl))
	{
		vl=vl.replace(/\D/g,'');
		$("#"+id).val(vl);
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
	//tot=tot-gst;
	if(vl=="")
	{
		vl=0;
	}
	else
	{
		vl=parseFloat(vl);
	}
	
	var perc=(tot * vl / 100);
	var paid=(tot-perc);
	//var paid=(tot-perc+gst);
	paid=paid.toFixed(2);
	perc=perc.toFixed(2);
	$("#dis_amt").val(perc);

	if($("#bill_typ").val()=="1" || $("#bill_typ").val()=="4")
	{
		$("#paid").val(paid);
		$("#balance").val("0");
	}
	if($("#bill_typ").val()=="2" || $("#bill_typ").val()=="3")
	{
		$("#paid").val("0");
		$("#balance").val(paid);
	}
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		if(vl <= 100)
		{
			$("#"+id).css("border","");
			$("#"+id).val(vl);
			$("#adjust").focus();
		}
		else
		{
			$("#"+id).css("border","1px solid #FF0000");
		}
	}
	else
	{
		$("#"+id).css("border","");
	}
}
function chk_num(id,a,e)
{
	var val=a;
	checkError(id,val);
	//~ if(/\D/g.test(val))
	//~ {
		//~ val=val.replace(/\D/g,'');
		//~ $("#"+id).val(val);
	//~ }
	
	//~ var n=a.length;
	//~ var numex=/^[0-9.]+$/;
	//~ if(a[n-1].match(numex))
	//~ {
		
	//~ }
	//~ else
	//~ {
		//~ a=a.slice(0,n-1);
		//~ $("#"+id).val(a);
	//~ }
	if(id=="contact")
	{
		$("#contact").css("border","");
	}
    if(id=="discount")
    {
      var tot=$("#total").val().trim();
      perc=(tot * val / 100);
	  paid=(parseFloat(tot)-perc);
      $("#dis_amt").val(perc);

      if($("#bill_typ").val()=="1" || $("#bill_typ").val()=="4")
		{
			$("#paid").val(paid);
			$("#balance").val("0");
		}
		if($("#bill_typ").val()=="2" || $("#bill_typ").val()=="3")
		{
			$("#paid").val("0");
			$("#balance").val(paid);
		}

    }
	if(id=="dis_amt")
	{
		$("#dis_amt").css("border","");
		$("#discount").css("border","");
		$("#adjust").css("border","");
		var tot=$("#total").val().trim();
		var discount=0;
		var dis_amt=0;
		var paid=0;
		var perc=0;
		var bal=0;
		if(tot=="")
		{
			tot=0;
		}
		else
		{
			tot=parseFloat(tot);
		}
		if(val=="")
		{
			$("#dis_amt").val("0").select();
			checkError("dis_amt",0);
			val=0;
		}
		else
		{
			val=parseFloat(val);
		}
		perc=(val * 100 / tot);
		paid=(parseFloat(tot)-val);
		if(val<0 || val>tot)
		{
			$("#dis_amt").css("border","1px solid #FF0000");
			$("#err").val("dis_amt");
		}
		if($("#bill_typ").val()=="1" || $("#bill_typ").val()=="4")
		{
			$("#paid").val(paid);
			$("#balance").val("0");
		}
		if($("#bill_typ").val()=="2" || $("#bill_typ").val()=="3")
		{
			$("#paid").val("0");
			$("#balance").val(paid);
		}
		perc=perc.toFixed(2);
		$("#discount").val(perc);
		$("#adjust").val("0");
	}
	
	if(id=="adjust")
	{
		var tot=$("#total").val().trim();
		var dis=$("#dis_amt").val().trim();
		var paid=0
		var bal=0;
		
		$("#dis_amt").css("border","");
		$("#discount").css("border","");
		$("#adjust").css("border","");
		$("#err").val("");
		if(dis=="")
		{
			dis=0;
		}
		else
		{
			dis=parseFloat(dis);
		}
		if(val=="")
		{
			$("#adjust").val("0").select();
			checkError("adjust",0);
			val=0;
		}
		else
		{
			val=parseFloat(val);
		}
		var pd=tot-dis;
		paid=tot-dis-val;
		if(val<0 || val>pd)
		{
			$("#adjust").css("border","1px solid #FF0000");
			$("#err").val("adjust");
		}
		
		if($("#bill_typ").val()=="1" || $("#bill_typ").val()=="4")
		{
			$("#paid").val(paid);
			$("#balance").val("0");
		}
		if($("#bill_typ").val()=="2" || $("#bill_typ").val()=="3")
		{
			$("#paid").val("0");
			$("#balance").val(paid);
		}
	}
	if(id=="paid")
	{
		var tot=$("#total").val().trim();
		var dis=$("#discount").val().trim();
		var adj=$("#adjust").val().trim();
		var paid=$("#paid").val().trim();
		var discount=$("#dis_amt").val().trim();
		var dis_amt=0;
		var bal=0;
		$("#dis_amt").css("border","");
		$("#discount").css("border","");
		$("#adjust").css("border","");
		$("#paid").css("border","");
		$("#err").val("");
		
		if(dis=="")
		{
			dis=0;
		}
		else
		{
			dis=parseFloat(dis);
		}
		
		if(adj=="")
		{
			adj=0;
		}
		else
		{
			adj=parseFloat(adj);
		}
		
		if(discount=="")
		{
			discount=0;
		}
		else
		{
			discount=parseFloat(discount);
		}
		
		if(paid=="")
		{
			$("#paid").val("0").select();
			checkError("paid",0);
			paid=0;
		}
		else
		{
			paid=parseFloat(paid);
		}
		
		var pd=tot-discount-adj;
		
		if(dis<0 || dis>100)
		{
			$("#discount").css("border","1px solid #FF0000");
			$("#err").val("discount");
		}
		if(adj<0 || adj>pd)
		{
			$("#adjust").css("border","1px solid #FF0000");
			$("#err").val("adjust");
		}
		if(paid<0 || paid>pd)
		{
			$("#paid").css("border","1px solid #FF0000");
			$("#err").val("paid");
			$("#balance").val("");
		}
		bal=(tot-discount-adj-paid);
		$("#dis_amt").val(discount);
		$("#balance").val(bal);
	}
	
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		if(id=="contact")
		{
			if(val=="" || val.length==10)
			{
				$("#addr").focus();
			}
			else
			{
				$("#contact").css("border","1px solid #FF0000");
			}
		}
		if(id=="discount")
		{
			$("#adjust").focus();
		}
		if(id=="dis_amt")
		{
			$("#adjust").focus();
		}
		if(id=="adjust")
		{
			if($("#bill_typ").val()=="1" || $("#bill_typ").val()=="4")
			{
				$("#paid").focus();
			}
			if($("#bill_typ").val()=="2" || $("#bill_typ").val()=="3")
			{
				$("#btn_save").focus();
			}
		}
		if(id=="paid")
		{
			$("#btn_save").focus();
		}
	}
}
function checkError(id,val)
{
	var reg = /^\d+(?:\.\d{1,2})?$/;
	if(!reg.test(val))
	{
		$("#"+id).css({"border":"1px solid #FF0000","box-shadow":"0px 0px 8px 2px #FF3131"});
		$("#"+id).addClass("err");
	}
	else
	{
		$("#"+id).css({"border":"","box-shadow":""});
		$("#"+id).removeClass("err");
	}
}
function save_items()
{
	$("#err").val("");
	//chk_num('adjust',$("#adjust").val().trim(),'');
	//alert($("#err").val());
	var len=$(".all_tr").length;
	//alert(len);
	if($("#ph").val()=="0")
	{
		$("#ph").focus();
	}
	else if(len<1)
	{
		$("html,body").animate({scrollTop: '10px'},500);
		$("#msgg").text("NO ITEM SELECTED");
		//var x=$("#temp_item").offset();
		//var w=$("#msgg").width()/2;
		//$("#msgg").css({'top':'50%','left':'50%'});
		$("#msgg").fadeIn(500);
		setTimeout(function()
		{
			$("#msgg").fadeOut(800,function()
			{
				$("#r_doc").select();$("#r_doc").focus();
			}
		)},800);
	}
	else if($("#total").val().trim()=="0")
	{
		$("html,body").animate({scrollTop: '10px'},500);
		$("#msgg").text("TOTAL AMOUNT CANNOT BE ZERO");
		//var x=$("#temp_item").offset();
		//var w=$("#msgg").width()/2;
		//$("#msgg").css({'top':'50%','left':'50%'});
		$("#msgg").fadeIn(500);
		setTimeout(function()
		{
			$("#msgg").fadeOut(800,function()
			{
				$("#r_doc").select();$("#r_doc").focus();
			}
		)},800);
	}
	else if($("#err").val()=="")
	{
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
		if($("#cust_name").val().trim()=="")
		{
			$("#cust_name").focus();
		}
		else if($("#paid").val().trim()=="")
		{
			$("#paid").focus();
		}
		else
		{
			//alert(all);
			check_all_items(all);
		}
	}
	else
	{
		var err=$("#err").val();
		$("#"+err).focus();
	}
}
function check_all_items(all)
{
	$.post("pages/sale_ajax.php",
	{
		type:"check_all_items",
		btnvalue:$("#btn_save").text().trim(),
		ph:$("#ph").val(),
		all:all,
	},
	function(data,status)
	{
		//alert(data);
		$(".all_tr").css("background","");
		$(".all_tr").find('td:eq(6) span:last').text("");
		if(data=="")
		{
			//alert(all);
			sale_final(all);
		}
		else
		{
			var val=data.split("#@#");
			for(var j=0; j<(val.length); j++)
			{
				if(val[j])
				{
					var v=(val[j]).split("@@");
					var itm=v[0];
					var bch=v[1];
					if(itm!="" && bch!="")
					{
						//alert(itm+"--"+bch);
						$("."+itm+bch).css("background","#FFD0D0");
						$("."+itm+bch).find('td:eq(6) span:last').css({"margin-left":"20px","position":"absolute","color":"#f00"});
						$("."+itm+bch).find('td:eq(6) span:last').text("Item Sold");
					}
				}
			}
		}
	})
}
function sale_final(all)
{
	//alert(all);
	$("#btn_add").attr("disabled",true);
	$("#btn_save").attr("disabled",true);
	$("#loader").show();
	$.post("pages/sale_ajax.php",
	{
		type:"save_items",
		all:all,
		pin:$("#uhid").val().trim(),
		cust_name:$("#cust_name").val().trim(),
		ref_by:$("#ref_by").val(),
		contact:$("#contact").val().trim(),
		addr:$("#addr").val().trim(),
		co:$("#co").val().trim(),
		final_rate:$("#final_rate").text().trim(),
		total:$("#total").val().trim(),
		gst:$("#gst").val().trim(),
		discount:$("#discount").val().trim(),
		dis_amt:$("#dis_amt").val().trim(),
		adjust:$("#adjust").val().trim(),
		paid:$("#paid").val().trim(),
		pat_type:$("#pat_type").val().trim(),
		balance:$("#balance").val().trim(),
		updt_bill:$("#txtupdtid").val().trim(),
		bill_id:$("#bill_id").val().trim(),
		bill_typ:$("#bill_typ").val().trim(),
		btn_val:$("#btn_save").text().trim(),
		ph:$("#ph").val(),
		patient_id:$("#patient_id").val().trim(),
		ind_no:$("#indno").val().trim(),
		user:$("#user").text().trim(),
	},
	function(data,status)
	{
		$("#loader").hide();
		$(".all_tr").find('td:eq(6) span:first').html("");
		//alert(data);
		//~ $("#loader").hide();
		//~ if(data!="0")
		//~ {
			//~ alert("Saved");
			//~ $("#bill_no").val(data);
			//~ $("#btn_print").focus();
			//~ clear_all();
		//~ }
		//~ else
		//~ {
			//~ save_items();
		//~ }
		//alert(data);
		var q=data.split("@penguin@");
		if(q[0]=="0")
		{
			save_items();
		}
		else if(q[0]=="Less")
		{
			check_all_items(q[1]);
		}
		else
		{
			var vl=q[1];
			vl=vl.split("@@");
			$("#bill_no").val(vl[1]);
			bootbox.dialog({ message: q[0]});
			setTimeout(function()
			{
				bootbox.hideAll();
				//window.location="processing.php?param=20&show=10&orderno="+q[1]+"&billno="+q[2];
				$("#btn_print").attr("disabled", false);
				$("#btn_print").focus();
			},600);
		}
	})
}
function pat_det(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$.post("pages/sale_ajax.php",
		{
			type:"pat_det",
			val:val,
		},
		function(data,status)
		{
			//alert(data);
            var val=data.split('@#') 
			if(data!="")
			{
				$("#cust_name").val(val[0]);
                $("#ref_by").val(val[1]).trigger("change");
                //$("#ref_by").select2({ theme: "classic" });
                $("#contact").val(val[2]);
                $("#addr").val(val[3]);
                if(val[4]==1)
                {
                  $("#bill_typ").val('2'); 
                  $("#paid").attr("disabled",true);
                  $("#paid").val("0");
                  $("#balance").val($("#total").val());  
                } 
                else
                {
                   $("#bill_typ").val('1');
                   $("#paid").attr("disabled",false);
                   $("#paid").val($("#total").val());
                   $("#balance").val("0"); 
 
                }
				//$("#ref_by").select2("focus");
				if(val[0]=="")
				{
					$("#cust_name").focus();
				}
				else
				{
                  $("#co").focus();
			     }
			}
			else
			{
				$("#cust_name").focus();
			}
		})
	}
}
function clear_all()
{
	$("#doc_id").val('');
	$("#r_doc").val('');
	$("#hguide").val('');
	$("#hguide_id").val('');
	$("#bch_qnt").val('');
	$("#bch_mrp").val('');
	$("#bch_gst").val('');
	$("#stock").val('');
	$("#bch_exp").val('');
	$("#qnt").val('');
}
function print_bill()
{
	var billno=$("#bill_no").val().trim();
	billno=btoa(billno);
	var sub_id=$("#ph").val();
	sub_id=btoa(sub_id);
	url="pages/sale_bill_print.php?billno="+billno+"&sub_id="+sub_id;
	//url="pages/sale_bill_print_zebra.php?billno="+billno;
	wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	//$("#btn_print").attr("disabled", true);
	
	$("#btn_reload").focus();
	//setTimeout("location.reload(true);",1000);
	//setTimeout(function(){window.location="processing.php?param=20";},1000);
}
function next_tab(id,e)
{
	$("#"+id).css("border","");
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		if(id=="ph" && $("#"+id).val()!="0")
		{
			$("#r_doc").focus();
		}
		else if(id=="uhid")
		{
			$("#cust_name").focus();
		}
		else if(id=="cust_name" && $("#"+id).val().trim()!="")
		{
			$("#ref_by").select2("focus");
		}
		else if(id=="contact")
		{
			$("#addr").focus();
		}
		else if(id=="addr")
		{
			$("#co").focus();
		}
		else if(id=="co")
		{
			$("#bill_typ").focus();
		}
		else if(id=="bill_typ")
		{
              $("#pat_type").focus();
		}
		else if(id=="pat_type")
		{
              $("#discount").focus();
		}
		else if(id=="dis_amt")
		{
			$("#adjust").focus();
		}
		else
		{
			$("#"+id).css("border","1px solid #FF0000");
		}
		//--------------------------------------------------//
	}
	if(id=="cust_name" && $("#"+id).val().trim()!="")
	{
		$("#"+id).val($("#"+id).val().toUpperCase());
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
					$("#uhid").focus();
				}
				else
				{
					$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
					$("#ref_doc").fadeIn(200);
					$.post("pages/sale_ajax.php",
					{
						type:typ,
						val:val,
						ph:$("#ph").val(),
						patient_id:$("#patient_id").val().trim(),
						pin:$("#pin").val().trim(),
						indno:$("#indno").val().trim(),
						ind_type:$("#type").val().trim(),
					},
					function(data,status)
					{
						$("#ref_doc").html(data);	
						doc_tr=1;
						doc_sc=0;
					})
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
				var doc_naam=docs[2].trim()
				$("#doc_info").fadeIn(200);
				doc_load(docs[1],doc_naam);
			}
		}
}
function doc_load(id,name)
{
	$("#r_doc").val(name);
	$("#doc_id").val(id);
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
					$("#uhid").focus();
				}
				else
				{
					$("#hguide_div").html("<img src='../images/ajax-loader.gif' />");
					$("#hguide_div").fadeIn(200);
					$.post("pages/sale_ajax.php"	,
					{
						val:val,
						item_id:$("#doc_id").val().trim(),
						type:typ,
						ph:$("#ph").val(),
					},
					function(data,status)
					{
						$("#hguide_div").html(data);	
						doc_tr=1;
						doc_sc=0;
					})
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
	$("#hguide_info").html("");
	$("#hguide_div").fadeOut(200);
	$("#qnt").focus();
	doc_tr=1;
	doc_sc=0;
}
//-----------------------------------------end-----------------------------------//
