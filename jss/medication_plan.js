var med_tr=1;
var med_sc=0;
function load_medi_list()
{
	//$("html,body").animate({scrollTop: '300px'},500);med_list
	$("#med_div").fadeIn(500);
	$("#medi").select();
	setTimeout(function(){$("#chk_val1").val(1)},1000);
	setTimeout(function(){$("#med_list").css('height','400px');},100);
	$("#p_ls").show();
}
function load_medi_list1(val,e)
{
		$("#med_dos").hide();
		setTimeout(function(){$("#med_list").css('height','400px');},300);
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#med_div").html("<img src='../images/ajax-loader.gif' />");
				$("#med_div").fadeIn(500);
				$.post("pages/load_medi.php"	,
				{
					val:val,
				},
				function(data,status)
				{
					$("#med_div").html(data);	
					med_tr=1;
					med_sc=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=med_tr+1;
				var cc=document.getElementById("med"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr+1;
					$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr-1;
					$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						$("#med_div").scrollTop(med_sc)
						med_sc=med_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=med_tr-1;
				var cc=document.getElementById("med"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr-1;
					$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr+1;
					$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						med_sc=med_sc-30;
						$("#med_div").scrollTop(med_sc)
					}
				}
			}
			
		}
		else
		{
			var cen_chk1=document.getElementById("chk_val1").value
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("mdname"+med_tr).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#medi").val(doc_naam);
				$("#medid").val(docs[1]);
				$("#unit").val(docs[3]);
				var d_in=docs[5];
				//$("#doc_mark").val(docs[5]);
				$("#med_info").html(d_in);
				$("#med_info").fadeIn(500);
				$("#g_name").show();
				select_medi();
				if($("#focus_chk").val()!="")
				{
					//$("html, body").animate({ scrollTop: 350 })	
					$("#dos").focus();
				}
				else
				{
					$("#dos").focus();	
				}
				load_generic(docs[4]);
			}
		}
}
function select_med(id,name,typ,gen)
{
	$("#medi").val(name);
	$("#medid").val(id);
	$("#med_info").html("");
	$("#med_div").fadeOut(500);
	$("#unit").val(typ);
	select_medi();
	load_generic(gen);
}
function load_generic(id)
{
	$.post("pages/global_load_g.php",
	{
		id:id,
		type:"load_generic",
	},
	function(data,status)
	{
		$("#generic").val(data);
	})
}
function select_medi()
{
	$("#med_dos").show();
	$("#g_name").show();
	$("#dos").focus();
}
function set_medi(batch)
{
	if($("#dos").val()=="0")
	{
		$("#dos").focus();
	}
	else if($("#freq").val()=="0")
	{
		$("#freq").focus();
	}
	else if($("#st_date").val()=="")
	{
		$("#st_date").focus();
	}
	else if($("#dur").val()=="0")
	{
		$("#dur").focus();
	}
	else if($("#unit_day").val()=="0")
	{
		$("#unit_day").focus();
	}
	else if($("#inst").val()=="")
	{
		$("#inst").focus();
	}
	else if($("#con_doc").val()=="0")
	{
		$("#con_doc").focus();
	}
	else
	{
		$("#medi_list").show();
		$("#ins_med").show();
		var fq="";
		var ins="";
		var m=$("#medi").val();
		var medid=$("#medid").val();
		var dos=$("#dos").val();
		var unit=$("#unit").val();
		var freq=$("#freq").val();
		var dur=$("#dur").val();
		var unit_day=$("#unit_day").val();
		var totl=$("#totl").val();
		var inst=$("#inst").val();
		var st_date=$("#st_date").val();
		var con_doc=$("#con_doc").val();
		if(freq=='1')
		fq="Immediately";
		else if(freq=='2')
		fq="Once a day";
		else if(freq=='3')
		fq="Twice a day";
		else if(freq=='4')
		fq="Thrice a day";
		else if(freq=='5')
		fq="Four times a day";
		else if(freq=='6')
		fq="Five times a day";
		else if(freq=='7')
		fq="Every hour";
		else if(freq=='8')
		fq="Every 2 hours";
		else if(freq=='9')
		fq="Every 3 hours";
		else if(freq=='10')
		fq="Every 4 hours";
		else if(freq=='11')
		fq="Every 5 hours";
		else if(freq=='12')
		fq="Every 6 hours";
		else if(freq=='13')
		fq="Every 7 hours";
		else if(freq=='14')
		fq="Every 8 hours";
		else if(freq=='15')
		fq="Every 10 hours";
		else if(freq=='16')
		fq="Every 12 hours";
		if(inst=="1")
		ins="As Directed";
		else if(inst=="2")
		ins="Before Meal";
		else if(inst=="3")
		ins="Empty Stomach";
		else if(inst=="4")
		ins="After Meal";
		else if(inst=="5")
		ins="In the Morning";
		else if(inst=="6")
		ins="In the Evening";
		else if(inst=="7")
		ins="At Bedtime";
		else if(inst=="8")
		ins="Immediately";
		var med_add= $('#medi_sel_list tr').length;
		if(med_add==0)
		{
			var md_add="<table class='table table-condensed table-bordered' style='style:none' id='medi_sel_list'>";
			md_add+="<tr><th style='width:3%;background-color:#cccccc'>SN<input type='hidden' id='batch' value='"+batch+"' class='m_val'/><span style='display:none;position:fixed;font-size:22px;top:30%;left:40%;color:#e00;' id='msgg'></span></th><th style='width:40%;background-color:#cccccc'>Drugs</th><th style='width:5%;background-color:#cccccc'>Dosage</th><th style='background-color:#cccccc'>Frequency</th><th style='width:8%;background-color:#cccccc'>Duration</th><th style='width:5%;background-color:#cccccc'>Total</th><th style='background-color:#cccccc'>Instruction</th><th style='width:3%;background-color:#cccccc'><i class='icon-trash icon-large'></i></span></th></tr>";
			md_add+="<tr id='"+medid+"'><td>1</td><td>"+m+"<input type='hidden' value='"+medid+"' class='m_val'/><input type='hidden' value='0' class='m_val'/></td><td>"+dos+"<input type='hidden' value='"+dos+"' class='m_val'/><input type='hidden' value='"+unit+"' class='m_val'/></td><td>"+fq+"<input type='hidden' value='"+freq+"' class='m_val'/></td><td>"+dur+" "+unit_day+"<input type='hidden' value='"+dur+"' class='m_val'/><input type='hidden' value='"+unit_day+"' class='m_val'/></td><td>"+totl+"<input type='hidden' value='"+totl+"' class='m_val'/></td><td>"+ins+"<input type='hidden' value='"+inst+"' class='m_val'/><input type='hidden' value='"+st_date+"' class='m_val'/></td><td><input value='"+con_doc+"' class='m_val' type='hidden'><span class='text-danger' onclick='$(this).parent().parent().remove()' style='cursor:pointer'><i class='icon-remove'></i></span></td></tr>";
			md_add+="</table>";
			
			$("#medi_list_data").html(md_add);
			med_add++;
			setTimeout(function(){$("#medi").focus()},500);
			$("#med_dos").hide();
		}
		else
		{
			var m=$("#medi").val();
			var medid=$("#medid").val();
			var t_ch=0;
			var med_l=$("#"+medid);
			if(med_l.length>0)
			{
				t_ch=1;
			}
			else
			{
				t_ch=0;
			}
			if(t_ch==1)
			{
				$("#medi_sel_list").css({'opacity':'0.5'});
				$("#msgg").text("Already Selected");
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#medi_sel_list").css({'opacity':'1.0'});
				})},600);	
			}			
			else
			{
				var tr=document.createElement("tr");
				tr.setAttribute("id",medid);
				var td=document.createElement("td");
				var td1=document.createElement("td");
				var td2=document.createElement("td");
				var td3=document.createElement("td");
				var td4=document.createElement("td");
				var td5=document.createElement("td");
				var td6=document.createElement("td");
				var td7=document.createElement("td");
				var tbody=document.createElement("tbody");
				td.innerHTML=med_add;
				td1.innerHTML=m+"<input type='hidden' value='"+medid+"' class=''/><input type='hidden' value='0' class=''/>";
				td2.innerHTML=dos+"<input type='hidden' value='"+dos+"' class=''/><input type='hidden' value='"+unit+"' class=''/>";
				td3.innerHTML=fq+"<input type='hidden' value='"+freq+"' class=''/>";
				td4.innerHTML=dur+" "+unit_day+"<input type='hidden' value='"+dur+"' class=''/><input type='hidden' value='"+unit_day+"' class='m_val'/>";
				td5.innerHTML=totl+"<input type='hidden' value='"+totl+"' class=''/>";
				td6.innerHTML=ins+"<input type='hidden' value='"+inst+"' class=''/><input type='hidden' value='"+st_date+"' class=''/>";
				td7.innerHTML="<input value='"+con_doc+"' class='m_val' type='hidden'><span class='text-danger' onclick='$(this).parent().parent().remove()' style='cursor:pointer'><i class='icon-remove'></i></span>";
				tr.appendChild(td);
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tr.appendChild(td4);
				tr.appendChild(td5);
				tr.appendChild(td6);
				tr.appendChild(td7);
				tbody.appendChild(tr);		
				document.getElementById("medi_sel_list").appendChild(tbody);
				setTimeout(function(){$("#medi").focus()},500);
				$("#med_dos").hide();
			}
		}
		$("#dos").val('0');
		$("#unit").val('');
		$("#freq").val('0');
		$("#st_date").val('');
		$("#dur").val('0');
		$("#unit_day").val('0');
		$("#totl").val('');
		$("#inst").val('1');
		$("#sos").attr("checked",false);
	}
}
function insert_medi()
{
	var det="";
	var ln= $('#medi_sel_list tr').length;
	for(var ii=1;ii<ln;ii++)
	{
		var medi=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(1) input:first').val();
		var plan=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(1) input:last').val();
		var dos=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(2) input:first').val();
		var unit=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(2) input:last').val();
		var freq=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(3) input:first').val();
		var dur=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(4) input:first').val();
		var unit_day=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(4) input:last').val();
		var tot=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(5) input:first').val();
		var inst=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(6) input:first').val();
		var st_date=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(6) input:last').val();
		var con_doc=$('#medi_sel_list').find('tr:eq('+ii+') td:eq(7) input:first').val();
		det+=medi+"@@"+plan+"@@"+dos+"@@"+unit+"@@"+freq+"@@"+dur+"@@"+unit_day+"@@"+tot+"@@"+inst+"@@"+st_date+"@@"+con_doc+"@@#@#";
	}
	$('#med_list').css('height','100px');
	$.post("pages/global_insert_data_g.php",
	{
		uhid:$("#uhid").val(),
		ipd:$("#ipd").val(),
		det:det,
		batch:$("#batch").val(),
		usr:$("#user").text().trim(),
		type:"insert_medi_ipd",
	},
	function(data,status)
	{
		//load_medi();
		if(plan=="0")
		{
			medication($("#batch").val());
		}
		else
		{
			med_admin();
		}				
		var uhid=$("#uhid").val();
		var ipd=$("#ipd").val();
		//window.location="processing.php?param=37&uhid="+uhid+"&ipd="+ipd+"&show=2";
	})
}

