$(document).ready(function()
{
	$("#view_data").draggable({ containment: "body", scroll: false });
	$("#billdate").datepicker({dateFormat: 'yy-mm-dd',maxDate: '0'});
	//$("#expiry").datepicker({dateFormat: 'yy-mm-dd',changeMonth:true,changeYear:true,yearRange:'c-10:c+10'});
	$("#supp").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="0")
			{
				$("#bill_no").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	})
	$("#bill_no").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#billdate").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	})
	$("#bill_amt").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="" && parseFloat($(this).val().trim())>0)
			{
				$("#r_doc").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			//$(this).val($(this).val().toUpperCase());
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#batch").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#expiry").focus();
				$("#expiry").val('');
				$("#mrp").val('');
				$("#c_price").val('');
				if($("#bchtr"+$("#batch").val().trim()).length>0)
				{
					$("#expiry").val($("#bchtr"+$("#batch").val().trim()).find('td:eq(4) input:first').val());
					$("#mrp").val($("#bchtr"+$("#batch").val().trim()).find('td:eq(7) input:first').val());
					$("#c_price").val($("#bchtr"+$("#batch").val().trim()).find('td:eq(8) input:first').val());
				}
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		if(e.keyCode==27)
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
			rem_old_list(0);
			$("#sav").focus();
		}
		else
		{
			//$(this).val($(this).val().toUpperCase());
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#qnt").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="" && parseInt($(this).val().trim())>0)
			{
				$("#free").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	})
	$("#free").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			$("#hsn").focus();
		}
	})
	$("#hsn").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			$("#gst").focus();
		}
	})
	$("#mrp").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="" && parseFloat($(this).val().trim())>0)
			{
				$("#c_price").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#gst").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#pkd_qnt").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#pkd_qnt").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="" && parseInt($(this).val().trim())>0)
			{
				$("#mrp").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#c_price").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#disc").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#disc").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			$("#rack_no").focus();
		}
	});
	$("#rack_no").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			$("#add").focus();
		}
	});
	$("#bill_no").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#billdate").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#billdate").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#r_doc").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			//$(this).val($(this).val().toUpperCase());
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	
});
function check_bill()
{
	if($("#supp").val()!="0" && $("#bill_no").val().trim()!="")
	{
		$.post("pages/ph_purchase_receive_ajax.php",
		{
			supp:$("#supp").val(),
			bill_no:$("#bill_no").val().trim(),
			type:5
		},
		function(data,status)
		{
			//alert(data);
			if(data>0)
			{
				$("#dup_bill").show();
				$("#bill_no").addClass("err");
			}
			else
			{
				$("#dup_bill").hide();
				$("#bill_no").removeClass("err");
			}
		});
	}
}
function calc_mrp(val,e) //for calculation
{
	$.post("pages/ph_purchase_receive_ajax.php",
	{
		type:1,
		rate:val,
		pktqnt:$("#pkd_qnt").val(),
		gst:$("#gst").val(),
	},
	function(data,status)
	{
		//alert(data);
		var val=data.split("@");
		$("#unit_sale").val(val['0']);
	})
}
function cal_costprice(val,e)
{
	if(val=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
	}
	var scst=val;
	var pkd=$("#pkd_qnt").val().trim();
	if(pkd=="")
	{
		pkd=0;
	}
	else
	{
		pkd=parseInt(pkd);
	}
	var gst=$("#gst").val().trim();
	if(gst=="")
	{
		gst=0;
	}
	else
	{
		gst=parseInt(gst);
	}
	var qnt=parseInt($("#qnt").val());
	var em=0;
	var amt=0;
	var gst_amt=0;
	em=scst/pkd;
	amt=scst*qnt;
	//gst_amt=(amt*gst/100);
	//amt=amt+gst_amt;
	em=em.toFixed(2);
	$("#unit_cost").val(em);
	$("#itm_amt").val(amt);
	$("#disc").val("0");
}
function exp_dt(id,vl,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	
	if(unicode==13)
	{
		var v=vl.split("-");
		if((vl.trim())=="" || (vl).length!=7 || parseInt(v[0])<2018 || parseInt(v[0])>2050 || parseInt(v[1])>12 || parseInt(v[1])==0 || v.length>2)
		{
			$("#"+id).css("border","1px solid #FF0000");
			$("#"+id).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			return true;
		}
		else
		{
			$("#qnt").focus();
		}
	}
	else
	{
		$("#"+id).css("border","");
		$("#"+id).css("box-shadow","");
		if(($("#"+id).val().trim()).length==4)
		{
			$("#"+id).val((vl)+"-");
		}
	}
}
function check_date_format(testDate)
{
	var date_regex = /([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/;
	return (date_regex.test(testDate));
}
function calc_disc(val,e)
{
	if(val=="")
	{
		val=0;
	}
	else
	{
		val=parseInt(val);
	}
	var cost=$("#c_price").val().trim();
	if(cost=="")
	{
		cost=0;
	}
	else
	{
		cost=parseFloat(cost);
	}
	var qnt=$("#qnt").val().trim();
	if(qnt=="")
	{
		qnt=0;
	}
	else
	{
		qnt=parseFloat(qnt);
	}
	var gst=$("#gst").val().trim();
	if(gst=="")
	{
		gst=0;
	}
	else
	{
		gst=parseFloat(gst);
	}
	var amt=cost*qnt;
	amt=amt+(amt*gst/100);
	var d_amt=(amt*val)/100;
	var res=amt-d_amt;
	//$("#itm_amt").val(res);
}
function new_entry()
{
	//window.location='processing.php?param=24';
	window.location='index.php?param='+btoa(24);
}
function add_data()
{
	exp_dt('expiry',$("#expiry").val().trim(),'');
	if($("#supp").val()=="0")
	{
		$("#supp").focus();
	}
	else if($("#bill_no").val().trim()=="")
	{
		$("#bill_no").focus();
	}
	else if($("#billdate").val().trim()=="")
	{
		$("#billdate").focus();
	}
	//~ else if($("#bill_amt").val().trim()=="")
	//~ {
		//~ $("#bill_amt").focus();
	//~ }
	//~ else if(parseInt($("#bill_amt").val().trim())==0)
	//~ {
		//~ $("#bill_amt").focus();
	//~ }
	else if($("#doc_id").val()=="")
	{
		$("#r_doc").focus();
	}
	else if($("#batch").val().trim()=="")
	{
		$("#batch").focus();
	}
	else if($("#expiry").val().trim()=="")
	{
		$("#expiry").focus();
	}
	else if($("#qnt").val().trim()=="")
	{
		$("#qnt").focus();
	}
	else if(parseInt($("#qnt").val().trim())==0)
	{
		$("#qnt").focus();
	}
	else if(parseInt($("#qnt").val().trim())<0)
	{
		$("#qnt").focus();
	}
	else if($("#gst").val().trim()=="")
	{
		$("#gst").focus();
	}
	else if(parseFloat($("#gst").val().trim())<0)
	{
		$("#gst").focus();
	}
	else if($("#pkd_qnt").val().trim()=="")
	{
		$("#pkd_qnt").focus();
	}
	else if(parseInt($("#pkd_qnt").val().trim())==0)
	{
		$("#pkd_qnt").focus();
	}
	else if(parseInt($("#pkd_qnt").val().trim())<0)
	{
		$("#pkd_qnt").focus();
	}
	else if($("#mrp").val().trim()=="")
	{
		$("#mrp").focus();
	}
	else if(parseFloat($("#mrp").val().trim())==0)
	{
		$("#mrp").focus();
	}
	else if(parseFloat($("#mrp").val().trim())<0)
	{
		$("#mrp").focus();
	}
	else if($("#c_price").val().trim()=="")
	{
		$("#c_price").focus();
	}
	else if(parseFloat($("#c_price").val().trim())==0)
	{
		$("#c_price").focus();
	}
	else if(parseFloat($("#c_price").val().trim())<0)
	{
		$("#c_price").focus();
	}
	else
	{
		add_item_temp();
	}
}
function add_item_temp()
{
	var itm_id=$("#doc_id").val().trim();
	var itm_name=$("#r_doc").val().trim();
	var bch=$("#batch").val().trim();
	var exp_dt=$("#expiry").val().trim();
	var qnt=$("#qnt").val().trim();
	var free=$("#free").val().trim();
	var gst=$("#gst").val().trim();
	var pkd_qnt=$("#pkd_qnt").val().trim();
	var mrp=$("#mrp").val().trim();
	var cost=$("#c_price").val().trim();
	var unit_sale=$("#unit_sale").val().trim();
	var unit_cost=$("#unit_cost").val().trim();
	var disc=$("#disc").val().trim();
	var hsn=$("#hsn").val().trim();
	var rack_no=$("#rack_no").val().trim();
	if(disc=="")
	{
		disc=0;
	}
	else
	{
		disc=parseInt(disc);
	}
	if(gst=="")
	{
		gst=0;
	}
	else
	{
		gst=parseInt(gst);
	}
	var tr_len=$('#mytable tr.all_tr').length;
	var amt=parseFloat(qnt)*parseFloat(cost);
	
	var gstamt=0;
    var vdisamt=0;
	//amt=amt+gstamt;
	var d_amt=((amt*disc)/100);
	
	amt=amt-d_amt;
	gstamt=(amt*gst/100);
	amt=amt.toFixed(2);
	d_amt=d_amt.toFixed(2);
	gstamt=gstamt.toFixed(2);
	if(tr_len==0)
	{
		var test_add="<table class='table table-condensed table-bordered table-report' id='mytable'>";
		test_add+="<tr>";
		test_add+="<th width='5%'>#</th>";
		test_add+="<th>Description</th>";
		test_add+="<th>Batch</th>";
		test_add+="<th>Expiry</th>";
		test_add+="<th style='text-align:right;'>Quantity</th>";
		test_add+="<th style='text-align:right;'>Free</th>";
		test_add+="<th style='text-align:right;'>GST %</th>";
		test_add+="<th style='text-align:right;'>Pkd Qnt</th>";
		test_add+="<th style='text-align:right;'>Strip MRP</th>";
		test_add+="<th style='text-align:right;'>Strip Cost</th>";
		test_add+="<th style='text-align:right;'>Discount %</th>";
		test_add+="<th style='text-align:right;'>Amount</th>";
		test_add+="<th width='5%'>Remove</th>";
		test_add+="</tr>";
		
		test_add+="<tr class='all_tr'>";
		test_add+="<td>1</td>";
		test_add+="<td>"+itm_name+"<input type='hidden' value='"+itm_id+"' class='itm_id'/><input type='hidden' value='"+itm_id+bch+"' class='test_id'/></td>";
		test_add+="<td>"+bch+"<input type='hidden' value='"+bch+"' class='bch' /></td>";
		test_add+="<td>"+exp_dt+"<input type='hidden' value='"+exp_dt+"' class='exp_dt' /></td>";
		test_add+="<td style='text-align:right;'>"+qnt+"<input type='hidden' value='"+qnt+"' class='qnt' /></td>";
		test_add+="<td style='text-align:right;'>"+free+"<input type='hidden' value='"+free+"' class='free' /><input type='hidden' value='"+hsn+"' class='hsn' /></td>";
		test_add+="<td style='text-align:right;'>"+gst+"<input type='hidden' value='"+gst+"' class='gst' /><input type='hidden' value='"+gstamt+"' class='all_gst' /></td>";
		test_add+="<td style='text-align:right;'>"+pkd_qnt+"<input type='hidden' value='"+pkd_qnt+"' class='pkd_qnt' /></td>";
		test_add+="<td style='text-align:right;'>"+mrp+"<input type='hidden' value='"+mrp+"' class='mrp'/><input type='hidden' value='"+unit_sale+"' class='unit_sale'/></td>";
		test_add+="<td style='text-align:right;'>"+cost+"<input type='hidden' value='"+cost+"' class='cost'/><input type='hidden' value='"+unit_cost+"' class='unit_cost'/></td>";
		test_add+="<td style='text-align:right;'>"+disc+"<input type='hidden' value='"+disc+"' class='disc'/><input type='hidden' value='"+d_amt+"' class='d_amt'/></td>";
		test_add+="<td style='text-align:right;'>"+amt+"<input type='hidden' value='"+amt+"' class='all_rate'/><input type='hidden' value='"+rack_no+"' class='rack_no' /></td>";
		test_add+="<td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_sl();set_amt();rem_old_list(1);' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td>";
		test_add+="</tr>";
		test_add+="<tr id='new_tr'><th colspan='10' style='text-align:right;'>Total</th><td style='text-align:right;' id='final_rate'>"+amt+"</td><td></td></tr>";
		test_add+="</table>";
		
		$("#temp_item").html(test_add);
		tr_len++;
	}
	else
	{
		var t_ch=0;
		var test_l=document.getElementsByClassName("test_id");
		
		for(var i=0;i<test_l.length;i++)
		{
			if(test_l[i].value==(itm_id+bch))
			{
				t_ch=1;
			}
		}
		if(t_ch)
		{
			$("#temp_item").css({'opacity':'0.5'});
			$("#msgg").text("Already selected same item and same batch no.");
			$("#msgg").fadeIn(500);
			setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#temp_item").css({'opacity':'1.0'});
			})},800);
		}
		else
		{
			tr_len++;
			var tr=document.createElement("tr");
			tr.setAttribute("class","all_tr");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var td4=document.createElement("td");
			var td5=document.createElement("td");
			var td6=document.createElement("td");
			var td7=document.createElement("td");
			var td8=document.createElement("td");
			var td9=document.createElement("td");
			var td10=document.createElement("td");
			var td11=document.createElement("td");
			var td12=document.createElement("td");
			var td13=document.createElement("td");
			
			td1.innerHTML=tr_len;
			td2.innerHTML=itm_name+"<input type='hidden' value='"+itm_id+"' class='itm_id' /><input type='hidden' value='"+itm_id+bch+"' class='test_id' />";
			td3.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='bch' />";
			td4.innerHTML=exp_dt+"<input type='hidden' value='"+exp_dt+"' class='exp_dt' />";
			td5.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' class='qnt' />";
			td5.setAttribute("style","text-align:right;");
			td6.innerHTML=free+"<input type='hidden' value='"+free+"' class='free' /><input type='hidden' value='"+hsn+"' class='hsn' />";
			td6.setAttribute("style","text-align:right;");
			td7.innerHTML=gst+"<input type='hidden' value='"+gst+"' class='gst' /><input type='hidden' value='"+gstamt+"' class='all_gst' />";
			td7.setAttribute("style","text-align:right;");
			td8.innerHTML=pkd_qnt+"<input type='hidden' value='"+pkd_qnt+"' class='pkd_qnt' />";
			td8.setAttribute("style","text-align:right;");
			td9.innerHTML=mrp+"<input type='hidden' value='"+mrp+"' class='mrp' /><input type='hidden' value='"+unit_sale+"' class='unit_sale' />";
			td9.setAttribute("style","text-align:right;");
			td10.innerHTML=cost+"<input type='hidden' value='"+cost+"' class='cost' /><input type='hidden' value='"+unit_cost+"' class='unit_cost' />";
			td10.setAttribute("style","text-align:right;");
			td11.innerHTML=disc+"<input type='hidden' value='"+disc+"' class='disc' /><input type='hidden' value='"+d_amt+"' class='d_amt' />";
			td11.setAttribute("style","text-align:right;");
			td12.innerHTML=amt+"<input type='hidden' value='"+amt+"' class='all_rate' /><input type='hidden' value='"+rack_no+"' class='rack_no' />";
			td12.setAttribute("style","text-align:right;");
			td13.innerHTML="<span onclick='$(this).parent().parent().remove();set_sl();set_amt();rem_old_list(1);' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span>";
			td13.setAttribute("style","text-align:center;");
			
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			tr.appendChild(td5);
			tr.appendChild(td6);
			tr.appendChild(td7);
			tr.appendChild(td8);
			tr.appendChild(td9);
			tr.appendChild(td10);
			tr.appendChild(td11);
			tr.appendChild(td12);
			tr.appendChild(td13);
			
			document.getElementById("mytable").appendChild(tr);
		}
	}
	$("#supp").attr('disabled',true);
	$("#bill_no").attr('disabled',true);
	$("#billdate").attr('disabled',true);
	$("#bill_amt").attr('disabled',true);
	$("#doc_id").val('');
	$("#r_doc").val('');
	$("#batch").val('');
	$("#expiry").val('');
	$("#free").val('');
	$("#mrp").val('');
	$("#c_price").val('');
	$("#gst").val('');
	$("#pkd_qnt").val('');
	$("#unit_sale").val('');
	$("#unit_cost").val('');
	$("#itm_amt").val('');
	$("#qnt").val('');
	$("#disc").val('');
	$("#hsn").val('');
	$("#rack_no").val('');
	set_amt();
	setTimeout(function()
	{
		$("#r_doc").focus();
	},500);
	rem_old_list(0);
}
function set_amt()
{
	var net_amt=0;
	var tot=0;
	var gstamt=0;
	var dis_amt=0;
	var tot_ts=document.getElementsByClassName("all_rate");
	for(var j=0;j<tot_ts.length;j++)
	{
		tot=tot+parseFloat(tot_ts[j].value);
	}
	var tot_gst=document.getElementsByClassName("all_gst");
	for(var j=0;j<tot_gst.length;j++)
	{
		gstamt=gstamt+parseFloat(tot_gst[j].value);
	}
	var tot_dis=document.getElementsByClassName("d_amt");
	for(var j=0;j<tot_dis.length;j++)
	{
		dis_amt=dis_amt+parseFloat(tot_dis[j].value);
	}
	net_amt=tot+gstamt;
	//net_amt=tot+gstamt-dis_amt;
	$("#total").val(tot);
	$("#bill_amt").val(tot);
	tot=tot.toFixed(2);
	gstamt=gstamt.toFixed(2);
	dis_amt=dis_amt.toFixed(2);
	net_amt=Math.round(net_amt);
	net_amt=net_amt.toFixed(2);
	$("#all_gst").val(gstamt);
	$("#net_total").val(net_amt);
	//$("#net_total").val(tot);
	$("#discount").val(dis_amt);
	var new_tr="<tr id='new_tr'><th colspan='11' style='text-align:right;'>Total</th><td style='text-align:right;' id='final_rate'>"+tot+"</td><td></td></tr>";
	$("#new_tr").remove();
	$('#mytable tr:last').after(new_tr);
}
function save_data_final()
{
	if($("#supp").val()=="0")
	{
		$("#supp").focus();
	}
	else if($("#bill_no").val().trim()=="")
	{
		$("#bill_no").focus();
	}
	else if($("#billdate").val().trim()=="")
	{
		$("#billdate").focus();
	}
	//~ else if($("#bill_amt").val().trim()=="")
	//~ {
		//~ $("#bill_amt").focus();
	//~ }
	//~ else if(parseInt($("#bill_amt").val().trim())==0)
	//~ {
		//~ $("#bill_amt").focus();
	//~ }
	else if($(".all_tr").length==0)
	{
		$("#msgg").text("No Item Selected.");
		$("#msgg").fadeIn(500);
		setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#r_doc").focus();})},800);
	}
	else
	{
		//alert();
		$("#loader").show();
		$("#add").attr("disabled",true);
		$("#sav").attr("disabled",true);
		var len=$(".all_tr").length;
		var all="";
		for(var i=0; i<len; i++)
		{
			all+=$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val(); // item_id
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val(); // bch
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val(); // exp_dt
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val(); // qnt
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(5) input:first').val(); // free
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(6) input:first').val(); // gst
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(6) input:last').val(); // gst_amt
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(7) input:first').val(); // pkd_qnt
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(8) input:first').val(); // strip mrp
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(8) input:last').val(); // unit_sale
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(9) input:first').val(); // strip cost
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(9) input:last').val(); // unit_cost
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(10) input:first').val(); // disc_per
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(10) input:last').val(); // d_amt
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(11) input:first').val(); // itm_amt
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(5) input:last').val(); // hsn
			all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(11) input:last').val(); // rack_no
			all+="@@#%#";
		}
		//alert(all);
		$.post("pages/ph_purchase_receive_ajax.php",
		{
			type:2,
			supp:$("#supp").val(),
			bill_no:$("#bill_no").val().trim(),
			billdate:$("#billdate").val().trim(),
			bill_amt:$("#bill_amt").val().trim(),
			total:$("#total").val().trim(),
			discount:$("#discount").val().trim(),
			all_gst:$("#all_gst").val().trim(),
			net_amt:$("#net_total").val().trim(),
			user:$("#user").text().trim(),
			btn_val:$("#sav").text().trim(),
			all:all,
		},
		function(data,status)
		{
			$("#loader").hide();
			alert(data);
			for(var i=0; i<len; i++)
			{
				$(".all_tr:eq("+i+")").find('td:eq(12)').html('<i class="icon-ok icon-large"></i>');
			}
		})
	}
}
function set_sl()
{
	var tot_ts=document.getElementsByClassName("all_tr");
	for(var i=0;i<tot_ts.length;i++)
	{
		$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
	}
}
function calulate_discount(val,e)
{
	if(val.trim()=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
	}
	var tot=parseFloat($("#total").val().trim());
	var all_gst=parseFloat($("#all_gst").val().trim());
	var disamt=0;
	disamt=tot+all_gst-val;
	$("#net_total").val(disamt);
}
function chk_dec(ths,e)
{
	var reg = /^\d+(?:\.\d{1,2})?$/;
	var val=$(ths).val();
	if(!reg.test(val))
	{
		$(ths).css("border","1px solid #FF0000");
		return true;
	}
	else
	{
		$(ths).css("border","");
	}
}
function chk_num(ths,e)
{
	var val=ths.value;
	if(/\D/g.test(val))
	{
		val=val.replace(/\D/g,'');
		$(ths).val(val);
	}
}
function change_bill(val)
{
	var tot=$("#total").val().trim();
	if(tot=="")
	{
		tot=0;
	}
	else
	{
		tot=parseInt(tot);
	}
	var paid=$("#paid").val().trim();
	if(paid=="")
	{
		paid=0;
	}
	else
	{
		paid=parseInt(paid);
	}
	var bal=tot-paid;
	
	if(val!="2")
	{
		$("#paid").val(tot);
		$("#bal").val("0");
		$("#paid").attr("disabled",false);
		if(val=="3")
		{
			$("#token_text").show();
			$("#token_div").show();
		}
		else
		{
			$("#token_text").hide();
			$("#token_div").hide();
		}
	}
	else
	{
		$("#paid").val("0");
		$("#bal").val(paid);
		$("#paid").attr("disabled",true);
		$("#token_text").hide();
		$("#token_div").hide();
	}
}
function bill_next(id,e)
{
	if(e.keyCode==13)
	{
		if($("#"+id).val()=="2")
		{
			$("#button4").focus();
		}
		else if($("#"+id).val()=="3")
		{
			$("#token").focus();
		}
		else
		{
			$("#paid").focus();
		}
	}
}
function search_pin(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		if((val.trim())!="")
		{
			$("#loader").show();
			$.post("pages/canteen_ajax.php",
			{
				type:6,
				user:$("#user").text().trim(),
				pin:val,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#cname").val(data).focus();
			})
		}
		else
		{
			$("#cname").val("").focus();
		}
	}
}
function rem_old_list(n)
{
	$("#old_item").empty().hide();
	$('#view_data').empty().hide();
	if(n>0)
	{
		$("#r_doc").focus();
	}
}
function load_prev_rates(id)
{
	$.post("pages/ph_purchase_receive_ajax.php",
	{
		type:11,
		itm:id,
	},
	function(data,status)
	{
		//alert(data);
		$("#old_item").html(data).show();
	})
}
function load_batch_list(id)
{
	$('#batch_list option').remove();
	$.post("pages/ph_purchase_receive_ajax.php",
	{
		type:10,
		id:id,
	},
	function(data,status)
	{
		//alert(data);
		if(data!="")
		{
			$("#batch_list").append(data);
		}
		load_prev_rates(id);
	})
}
function load_batch_stock(id)
{
	$('#view_data').empty().hide();
	$.post("pages/ph_purchase_receive_ajax.php",
	{
		type:12,
		id:id,
	},
	function(data,status)
	{
		//alert(data);
		if(data!="")
		{
			$('#view_data').html(data).show();
		}
	})
}
//=========================================================================
//------------------------item search---------------------------------//
function load_refdoc1()
{
		//$("#ref_doc").fadeIn(200);
		//$("#hguide").select();
		setTimeout(function(){ $("#chk_val2").val(1)},200);
}
var doc_tr=1;
var doc_sc=0;
function load_refdoc(val,e)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		//alert(unicode);
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				if(unicode==27)
				{
					rem_old_list(0);
					$("#sav").focus();
				}
				else
				{
					$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
					$("#ref_doc").fadeIn(200);
					$.post("pages/ph_purchase_receive_ajax.php",
					{
						type:4,
						val:val,
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
					$("#doc"+doc_tr).css({'color': '#419641;','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr-1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
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
					$("#doc"+doc_tr).css({'color': '#419641;','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr+1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						doc_sc=doc_sc-30;
						$("#ref_doc").scrollTop(doc_sc)
					}
				}
			}
			else if(unicode==27)
			{
				$("#sav").focus();
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
				var gst=docs[3].trim();
				var s_qnt=docs[4].trim();
				var hsn=docs[5].trim();
				var rack_no=docs[6].trim();
				$("#doc_info").fadeIn(200);
				doc_load(docs[1],doc_naam,gst,s_qnt,hsn,rack_no);
			}
		}
}
function doc_load(id,name,gst,s_qnt,hsn,rack_no)
{
	$("#r_doc").val(name);
	$("#doc_id").val(id);
	$("#gst").val(gst);
	$("#pkd_qnt").val(s_qnt);
	$("#hsn").val(hsn);
	$("#rack_no").val(rack_no);
	$("#ref_doc").fadeOut(200);
	$("#batch").val('');
	$("#batch").focus();
	doc_tr=1;
	doc_sc=0;
	load_batch_list(id);
	load_batch_stock(id);
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

function hguide_up(val,e)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				if(unicode==27)
				{
					$("#sav").focus();
				}
				else
				{
					$("#hguide_div").html("<img src='../images/ajax-loader.gif' />");
					$("#hguide_div").fadeIn(200);
					$.post("pages/ph_purchase_receive_ajax.php"	,
					{
						val:val,
						item_id:$("#doc_id").val().trim(),
						type:3,
						ph:1,
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
					$("#hg"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
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
					$("#hg"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
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
			else if(unicode==27)
			{
				$("#sav").focus();
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
//=========================================================================

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
