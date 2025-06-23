/////////////////// Start Consultation /////////////////
function check_appointment(q,opd_id,pin)
{
	$.post("pages/global_load.php",
	{
		type:"check_appointment_already",
		uhid:$("#uhid").text().trim(),
	},
	function(data,status)
	{
		$("#print_div").html('');
		if(data==0)
		{
			load_new_appointment();
		}else
		{
			show_print_details($("#uhid").text(),q,opd_id,pin);
		}
		$("#edit_div").html('');
	})
}
function load_new_appointment()
{
	$.post("pages/global_load.php",
	{
		type:"new_appointment",
		uhid:$("#uhid").text().trim(),
	},
	function(data,status)
	{
		$("#edit_div").html('');
		$("#load_all").html(data);
		$("#dept_id").focus();
	})
}
function dept_sel(qq)
{
	if(qq!='edit')
	{
		$.post("pages/global_load.php",
		{
			type:"load_dept_doc",
			dept_id:$("#dept_id").val(),
			uhid:$("#uhid").text().trim(),
		},
		function(data,status)
		{
			$("#load_all_form").hide().html(data).fadeIn('slow');
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:300}, {
				duration: 1000,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});
			$(".datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: '0',
			});
		})
	}else
	{
		$("#r_doc").val('');
		$("#ref_doc").html('');
	}
}
function dept_sel_Up(e)
{
	if(e.which==13)
	{
		if($("#dept_id").val()!=0)
		{
			$("#r_doc").focus();
		}
	}
}

//-----------------------------------------Load Doctor List Onfocus-------------------------------||
function load_refdoc1()
{
		$("#ref_doc").fadeIn(500);
		$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},100);
}
function load_refdoc1_edit()
{
	$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
	$("#ref_doc").fadeIn(500);
	$.post("pages/global_load.php"	,
	{
		type:"show_con_doc",
		dept_id:$("#dept_id").val(),
		uhid:$("#uhid").text().trim(),
	},
	function(data,status)
	{
		$("#ref_doc").html(data);	
		doc_tr=1;
		doc_sc=0;
	})	
}
//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
var doc_tr=1;
var doc_sc=0;
function load_refdoc(val,e)
{
	//$("#r_doc").css({'border-color': '#CCC'}).focus();
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode!=13)
	{
		if(unicode!=40 && unicode!=38)
		{
			$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
			$("#ref_doc").fadeIn(500);
			$.post("pages/global_load.php"	,
			{
				type:"show_con_doc",
				val:val,
				dept_id:$("#dept_id").val(),
				uhid:$("#uhid").text().trim(),
			},
			function(data,status)
			{
				$("#ref_doc").html(data);	
				doc_tr=1;
				doc_sc=0;
			})	
		}
		else if(unicode==40)
		{
			var chk=doc_tr+1;
			var cc=document.getElementById("doc"+chk).innerHTML;
			if(cc)
			{
				doc_tr=doc_tr+1;
				$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
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
				$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
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
	}
	else
	{
		var cen_chk1=document.getElementById("chk_val2").value;
		if(cen_chk1!=0)
		{
			var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
			var con_doc_id=docs[1].trim();
			var doc_naam=docs[2].trim();
			var con_doc_fee=docs[3].trim();
			var con_doc_validity=docs[4].trim();
			$("#con_doc_id").val(con_doc_id);
			$("#r_doc").val(doc_naam);
			$("#con_doc_fee").val(con_doc_fee);
			$("#con_doc_validity").val(con_doc_validity);
			$("#ref_doc").fadeOut(500);
			
			$("#appoint_date").focus();
			load_payment(con_doc_id);
		}
	}
}
function doc_load(id,name,fee,validity,qq)
{
	$("#r_doc").css({'border-color': '#CCC'}).focus();
	$("#con_doc_id").val(id);
	$("#r_doc").val(name);
	$("#con_doc_fee").val(fee);
	$("#con_doc_validity").val(validity);
	$("#doc_info").html("");
	$("#ref_doc").fadeOut(500);
	$("#appoint_date").focus();
	if(qq!='edit')
	{
		load_payment(id);
	}
}
function appoint_date(e)
{
	var dt=$("#appoint_date").val();
	if(e.which==13)
	{
		if(dt!="")
		{
			$("#dis_per").focus();
		}
	}else
	{
		$("#appoint_date").val("");
	}
}
function appoint_date_change()
{
	$("#dis_per").focus();
}
function load_payment(con_doc_id)
{
	$({myScrollTop:window.pageYOffset}).animate({myScrollTop:300}, {
		duration: 1000,
		easing: 'swing',
		step: function(val){
			window.scrollTo(0, val);
		}
	});
	
	$("#visit_fee").val($("#con_doc_fee").val());
	
	if($("#pat_emergency").is(":checked"))
	{
		var emerg_fee=parseInt($("#emerg_fee").val());
	}
	else
	{
		var emerg_fee=0;
	}
	
	var tot=parseInt($("#con_doc_fee").val())+parseInt($("#regd_fee").val())+parseInt(emerg_fee);
	$("#total").val(tot);
	$("#advance").val(tot);
}
function dis_per(val,e)
{
	var error=0;
	if(e.which==13)
	{
		$("#dis_amnt").focus();
	}
	var tot=$("#total").val();
	var dis_val=((tot*val)/100);
	dis_val=Math.round(dis_val);
	
	$("#dis_amnt").val(dis_val);
	
	if($("#pay_mode").val()=="Credit")
	{
		$("#balance").val(tot-dis_val);
		$("#advance").val("0");
	}else
	{
		$("#advance").val(tot-dis_val);
		$("#balance").val("0");
	}
	
	if(dis_val>tot)
	{
		$("#dis_per").css({'border-color': '#F00'}).focus();
	}else
	{
		$("#dis_per").css({'border-color': '#CCC'});
	}
	if(dis_val>0)
	{
		$("#d_reason").fadeIn(500);
	}else
	{
		$("#d_reason").fadeOut(500);
	}
	var n=val.length;
	var numex=/^[0-9.]+$/;
	if(val[n-1].match(numex))
	{
		
	}
	else
	{
		val=val.slice(0,n-1);
		$("#dis_per").val(val);
	}
}
function dis_amnt(val,e)
{
	var tot=parseInt($("#total").val());
	if(tot==0)
	{
		var per=0;
	}else
	{
		var per=((val*100)/tot);
	}
	$("#dis_per").val(per);
	
	if($("#pay_mode").val()=="Credit")
	{
		$("#balance").val(tot-val);
		$("#advance").val("0");
	}else
	{
		$("#advance").val(tot-val);
		$("#balance").val("0");
	}
	
	if(val>0)
	{
		if(val>tot)
		{
			$("#dis_amnt").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#dis_amnt").css({'border-color': '#CCC'});
			if(e.which==13)
			{
				$("#dis_reason").focus();
			}
		}
		$("#d_reason").fadeIn(500);
	}else
	{
		$("#d_reason").fadeOut(500);
		if(e.which==13)
		{
			$("#dis_amnt").val("0");
			$("#advance").focus();
		}
	}
	var n=val.length;
	var numex=/^[0-9.]+$/;
	if(val[n-1].match(numex))
	{
		
	}
	else
	{
		val=val.slice(0,n-1);
		$("#dis_amnt").val(val);
	}
}
function dis_reason(val,e)
{
	if(e.which==13)
	{
		if(val=="")
		{
			$("#dis_reason").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#dis_reason").css({'border-color': '#CCC'});
			$("#advance").focus();
		}
	}else
	{
		$("#dis_reason").css({'border-color': '#CCC'});
	}
}
function advance(val,e)
{
	var tot=parseInt($("#total").val());
	var dis_amnt=parseInt($("#dis_amnt").val());
	var res=tot-dis_amnt;
	var bal=res-val;
	$("#balance").val(bal);
	if(bal<0)
	{
		$("#advance").css({'border-color': '#F00'}).focus();
	}else
	{
		$("#advance").css({'border-color': '#CCC'});
	}
	if(bal<0)
	{
		$("#b_reason").fadeOut();
	}else if(bal>0)
	{
		if($("#pay_mode").val()!=="Credit")
		{
			$("#b_reason").fadeIn();
		}
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:300}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	}else
	{
		$("#b_reason").fadeOut();
	}
	if(e.which==13)
	{
		if(bal<0)
		{
			$("#advance").focus();
		}else if(bal>0)
		{
			if($("#pay_mode").val()!=="Credit")
			{
				$("#bal_reason").val("");
				$("#bal_reason").focus();
			}else
			{
				$("#bal_reason").val("Credit");
				$("#pay_mode").focus();
			}
		}else
		{
			$("#pay_mode").focus();
		}
	}
	var n=val.length;
	var numex=/^[0-9.]+$/;
	if(val[n-1].match(numex))
	{
		
	}
	else
	{
		val=val.slice(0,n-1);
		$("#advance").val(val);
	}
}
function bal_reason(val,e)
{
	if(e.which==13)
	{
		if(val=="")
		{
			$("#bal_reason").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#bal_reason").css({'border-color': '#CCC'});
			$("#pay_mode").focus();
		}
	}else
	{
		$("#bal_reason").css({'border-color': '#CCC'});
	}
}
function pay_mode(val,e)
{
	if(e.which==13)
	{
		$("#save").focus();	
	}
}
///////// Save  ///////////////
function save_pat(typ)
{
	var error=0;
	var con_doc_id=$("#con_doc_id").val();
	if(con_doc_id==0)
	{
		$("#r_doc").css({'border-color': '#F00'}).focus();
		error=1;
		return true;
	}
	var appoint_date=$("#appoint_date").val();
	if(appoint_date==0)
	{
		$("#appoint_date").focus();
		error=1;
		return true;
	}
	var dis_amnt=$("#dis_amnt").val();
	if(dis_amnt>0)
	{
		if($("#dis_reason").val()=="")
		{
			$("#dis_reason").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
	}
	var balance=$("#balance").val();
	if(balance>0)
	{
		if($("#bal_reason").val()=="")
		{
			$("#bal_reason").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
	}
	if($("#r_doc").val()=="")
	{
		$("#r_doc").css({'border-color': '#F00'}).focus();
		error=1;
		return true;
	}
	var pat_emergency=$('#pat_emergency:checked').val();
	if(pat_emergency==1)
	{
		var pat_emergency=1;
	}else
	{
		var pat_emergency=0;
	}
	if(error==0)
	{
		//alert($("#opd_id_edit").val());
		$("#save").prop("disabled",true);
		$.post("pages/global_insert_data.php",
		{
			type:"save_pat_appointment",
			mode:typ,
			con_doc_id:con_doc_id,
			appoint_date:appoint_date,
			visit_fee:$("#visit_fee").val(),
			regd_fee:$("#regd_fee").val(),
			total:$("#total").val(),
			dis_per:$("#dis_per").val(),
			dis_amnt:dis_amnt,
			dis_reason:$("#dis_reason").val(),
			advance:$("#advance").val(),
			bal_reason:$("#bal_reason").val(),
			balance:balance,
			pat_emergency:pat_emergency,
			emergency_fee:$("#emergency_fee").val(),
			pay_mode:$("#pay_mode").val(),
			
			user:$("#user").text().trim(),
			uhid:$("#uhid").text().trim(),
			opd_id:$("#opd_id_edit").val(),
		},
		function(data,status)
		{
			if(data==2)
			{
				bootbox.alert("Already appointed");
				$("#save").prop("disabled",false);
			}else
			{
				var q=data.split("@@");
				
				bootbox.dialog({ message: q[0]});
				setTimeout(function(){
					bootbox.hideAll();
					if(q[0]=="Saved")
					{
						check_appointment('load_current',q[1]);
					}
					if(q[0]=="Updated")
					{
						$("#edit_div").html('');
						check_appointment('load_current',q[1]);
					}
				},1000);
			}
		})
	}
}
//////// Save End /////////
function show_print_details(uhid,q,opd_id,pin)
{
	$.post("pages/global_load.php",
	{
		type:"show_print_details",
		uhid:uhid,
		user:$("#user").text().trim(),
		pin:pin,
	},
	function(data,status)
	{
		$("#load_all").html("");
		$("#print_div").html(data);
		if(q=="load_current")
		{
			//show_tr_btn(opd_id);
			$("#doc_tab"+opd_id).click();
			$("#print_con_receipt").focus();
		}
	})
}
function new_appointment()
{
	$("#print_div").insertBefore( "#load_all" );
	load_new_appointment();
	$({myScrollTop:window.pageYOffset}).animate({myScrollTop:1000}, {
		duration: 1000,
		easing: 'swing',
		step: function(val){
			window.scrollTo(0, val);
		}
	});
}
function print_receipt(url)
{
	window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}
function money_receipt(url)
{
	window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
}
function show_tr_btn(opd)
{
	$("#load_all").html("");
	$(".hidden_div").fadeOut();
	$(".iconp").show();
	$(".iconm").hide();
	if($('#'+opd+':visible').length)
	{
		$("#"+opd).fadeOut('slow');
		$("#plus_sign"+opd).show();
		$("#minus_sign"+opd).hide();
	}else{
		$("#"+opd).fadeIn('slow');
		$("#plus_sign"+opd).hide();
		$("#minus_sign"+opd).show();
	}
	$("#edit_div").html('');
}
/////////////////// End Consultation /////////////////
/////////////////// Start Investigation /////////////////
function check_investigation(opd,typ,pin)
{
	//alert(opd+'-_-'+typ);
	$("#out_test_form").html("");
	$.post("pages/global_load.php",
	{
		type:"load_all_investigation",
		uhid:$("#uhid").text().trim(),
		pin:pin,
	},
	function(data,status)
	{
		$("#load_investigation").html(data);
		$("#test").focus();
		if(typ=="load_new_tt")
		{
			load_add_test_form(opd);
		}
	})
}
function load_add_test_form(opd)
{
	$.post("pages/global_load.php",
	{
		type:"load_test_form",
		opd_id:opd,
		uhid:$("#uhid").text().trim(),
		user:$("#user").text().trim(),
	},
	function(data,status)
	{
		$("#out_test_form").html(data);
		display_test_info(opd);
		if(opd=="0000")
		{
			$("#test").focus();
		}else
		{
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:350}, {
				duration: 1000,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});
			//$("#dis_per").focus();
			$("#print_receipt").focus();
		}
	})
}
function display_test_info(opd)
{
	$.post("pages/global_load.php",
	{
		type:"load_test_details",
		uhid:$("#uhid").text().trim(),
		opd_id:opd,
	},
	function(data,status)
	{
		$("#list_all_test").html(data);
		$("#list_all_test").css({'height':'200px','overflow':'auto'});
		load_cost(2);
		add_vaccu();
	})
}
////////// Test load /////////////
function test_enable()
{
	setTimeout(function(){ $("#chk_val").val(1)},500);	
}
var t_val=1;
var t_val_scroll=0;
function select_test_new(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		var tst=document.getElementsByClassName("test"+t_val);
		load_test_new(''+tst[1].value+'',''+tst[2].innerHTML+'',''+tst[3].innerHTML+'');
		$("#list_all_test").slideDown(400);
		$("#test").val("").focus();
	}
	else if(unicode==40)
	{
		var chk=t_val+1;
		var cc=document.getElementById("td"+chk).innerHTML;
		if(cc)
		{
			t_val=t_val+1;
			$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
			var t_val1=t_val-1;
			$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
			var z2=t_val%1;
			if(z2==0)
			{
				$("#test_d").scrollTop(t_val_scroll)
				t_val_scroll=t_val_scroll+30;
			}
		}	
	}
	else if(unicode==38)
	{
		var chk=t_val-1;
		var cc=document.getElementById("td"+chk).innerHTML;
		if(cc)
		{
			t_val=t_val-1;
			$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
			var t_val1=t_val+1;
			$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
			var z2=t_val%1;
			if(z2==0)
			{
				t_val_scroll=t_val_scroll-30;
				$("#test_d").scrollTop(t_val_scroll)
				
			}
		}	
	}
	else if(unicode==27)
	{
		$("#test").val("");
		$("#test_d").html("");
		$("#list_all_test").slideUp(300);
		
		$("html, body").animate({ scrollTop: 1500 })
		$("#dis_per").focus();
	}
	else
	{
		$.post("pages/load_test_ajax.php",
		{
			test:val,
			uhid:$("#uhid").text().trim(),
		},
		function(data,status)
		{
			$("#test_d").html(data);
			t_val=1;
			t_val_scroll=0;
			$("#test_d").scrollTop(t_val_scroll)
		})
	}
}
function load_test_new(id,name,rate)
{
	//$(".up_div").fadeIn(500);
	var test_chk= $('#test_list tr').length;
	if(test_chk==0)
	{	
		var test_add="<table class='table table-bordered' id='test_list'>";	
		test_add+="<tr><th colspan='3' style='background-color:#cccccc'>Tests</th><th style='text-align:right;background-color:#cccccc'>Total:<span id='test_total'></span></th></tr>";
		test_add+="<tr><td>1</td><td>"+name+"<input type='hidden' value='"+id+"' class='test_id'/></td><td contentEditable='true' onkeyup='load_cost(2)'><span class='test_f'>"+rate+"</span></td><td onclick='delete_rows(this,2)'><span class='text-danger'><i class='icon-remove'></i></span></td></tr>";
		test_add+="</table>";
		test_add+="<div style='display:none;text-align:center;background-color:#cccccc;font-weight:bold;font-size:15px;padding:5px' id='test_hidden_price'>Total:<span id='test_total_hidden'></span></div>";
		
		$("#list_all_test").html(test_add);
		test_chk++;
	
		var tot=0;
		var tot_ts=document.getElementsByClassName("test_f");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseInt(tot_ts[j].innerHTML);
		}
		$("#test_total").text(tot);
		$("#test").val("");
	}
	else
	{
		
		var t_ch=0;
		var test_l=document.getElementsByClassName("test_id");
		
		for(var i=0;i<test_l.length;i++)
		{
				if(test_l[i].value==id)
				{
					t_ch=1;
				}
		}
		if(t_ch)
		{

			$("#test_sel").css({'opacity':'0.5'});
			$("#msg").text("Already Selected");
			var x=$("#test_sel").offset();
			var w=$("#msg").width()/2;
			$("#msg").css({'top':x.top,'left':'50%','margin-left':-w+'px'});
			$("#msg").fadeIn(500);
			setTimeout(function(){$("#msg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'});
			})},600);
			
		}			
		else
		{
	
		var tr=document.createElement("tr");
		var td=document.createElement("td");
		var td1=document.createElement("td");
		var td2=document.createElement("td");
		var td3=document.createElement("td");
		var tbody=document.createElement("tbody");
		
		td.innerHTML=test_chk;
		td1.innerHTML=name+"<input type='hidden' value='"+id+"' class='test_id'/>"
		td2.innerHTML="<span class='test_f'>"+rate+"</span>";
		td2.setAttribute("contentEditable","true");
		td2.setAttribute("onkeyup","load_cost(2)");
		td3.innerHTML="<span class='text-danger'><i class='icon-remove'></i></span>";
		td3.setAttribute("onclick","delete_rows(this,2)");
		tr.appendChild(td);
		tr.appendChild(td1);
		tr.appendChild(td2);
		tr.appendChild(td3);
		tbody.appendChild(tr);		
		document.getElementById("test_list").appendChild(tbody);
		var tot=0;
		var tot_ts=document.getElementsByClassName("test_f");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseInt(tot_ts[j].innerHTML);
		}
			$("#test_total").text(tot);
		}
		
		if(test_chk>4)
		{
			$("#list_all_test").css({'height':'220px','overflow':'scroll','overflow-x':'hidden'})			
			$("#list_all_test").animate({ scrollTop: 2900 });
			$("#test_hidden_price").fadeIn(200);
			$("#test_total_hidden").text($("#test_total").text());
		}
		$("#test").val("");
	}
	add_vaccu();
	add_grtotal();
}
function add_vaccu()
{
	var vac_chk=$("#vaccu_charge").val();
	
	if(vac_chk>0)
	{
		var test_id=$(".test_id");
		var test_all="";
		for(var i=0;i<test_id.length;i++)
		{
				test_all=test_all+"@"+test_id[i].value;
		}
		
		$.post("pages/pat_reg_vac.php",
		{
			test_all:test_all
		},
		function(data,status)
		{
			$("#list_all_extra").html(data);
			
			var tot=0;
			var tot_ts=$(".extra_price");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseInt(tot_ts[j].innerHTML);
			}
			
			$("#grextra").text(tot);
			$("#extra_total").text(tot);
			
			add_grtotal();
		})
	}
	
}
function load_cost(chk)
{
	if(chk==2)
	{
		var tot=0;
		var tot_ts=document.getElementsByClassName("test_f");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseInt(tot_ts[j].innerHTML);
		}
		$("#test_total").text(tot);
	}
	add_grtotal();
}
function load_tab(id,chk)
{
	if(id==1)
	{
		$(".up_div").slideUp(500);
		if(!chk)
		{
			$("#test_sel").fadeOut(400);
			$("#h_pack").fadeOut(400);
			$("#serv").fadeOut(400);
			
			$("#consultation").fadeIn(400);
		}
		if($("#list_all_extra").css('display')=="none")
		{
			$("#list_all_extra").slideToggle(500);
		}
		$("#test").focus();
	}
	else if(id==2)
	{
		$(".up_div").slideUp(500);
		if(!chk)
		{
			$("#consultation").fadeOut(400);
			$("#h_pack").fadeOut(400);
			$("#serv").fadeOut(400);
			
			$("#test_sel").fadeIn(400);
		}

		
		if($("#list_all_test").css('display')=="none")
		{
			$("#list_all_test").slideDown(500,function(){ if(!chk){$("#test").focus(); } })	
		}
		else
		{
			$("#list_all_test").slideUp(500)
		}
		$("#test").focus();
	}
	else if(id==3)
	{
		$(".up_div").slideUp(500);
		if(!chk)
		{
			$("#consultation").fadeOut(400);
			$("#test_sel").fadeOut(400);	
			$("#serv").fadeOut(400);
			
			$("#h_pack").fadeIn(400);
		}
		$("#list_all_health").slideDown(500,function(){ if(!chk){ $("#pack").focus();} })
		
	}
	else if(id==4)
	{
		$(".up_div").slideUp(500);
		if(!chk)
		{
			$("#consultation").fadeOut(400);
			$("#test_sel").fadeOut(400);	
			$("#h_pack").fadeOut(400);
			
			$("#serv").fadeIn(400);
		}
		$("#list_all_serv").slideDown(500,function(){ if(!chk){ $("#service").focus(); } })
	}	
}
function delete_rows(tab,num)
{
	$(tab).parent().remove()
	load_cost(num);
	$("#test").focus();	
	add_vaccu();
}
function add_grtotal()
{
	var extra=parseInt($("#extra_total").text());
	if(!extra) { extra=0;}
	
	var tst=parseInt($("#test_total").text());
	if(!tst) { tst=0;}
	
	var hlt=parseInt($("#hlt_total").text());
	if(!hlt) { hlt=0;}
	
	var serv=parseInt($("#serv_total").text());
	if(!serv) { serv=0;}
	
	var grtotal=extra+tst+hlt+serv
	
	$("#grextra").text(extra);
	$("#grtest").text(tst);
	$("#grhealth").text(hlt);
	$("#grserv").text(serv);
	$("#grtotal").text(grtotal);
	
	$("#list_all_grtotal").fadeIn(200);
	
	var regd_fee=parseInt($("#regd_fee").val());
	var tot_amt=regd_fee+tst+extra;
	
	$("#total").val(tot_amt);
	if($("#save").val()!="Update")
	{
		if($("#pay_mode").val()=="Credit")
		{
			$("#advance").val('0');
			$("#balance").val(tot_amt);	
		}else
		{
			$("#advance").val(tot_amt);
			$("#balance").val("0");	
		}
	}
	else
	{
		var dis_amnt=$("#dis_amnt").val();
		if(dis_amnt>0)
		{
			var bal=(tot_amt-dis_amnt)-$("#advance").val();
			$("#balance").val(bal);	
		}else
		{
			var bal=tot_amt-$("#advance").val();
			$("#balance").val(bal);	
		}
	}
	
}
function pay_mode_change(val)
{
	var tot_amt=parseInt($("#total").val());
	var dis_amnt=parseInt($("#dis_amnt").val());
	if(val=="Credit")
	{
		$("#advance").val('0');
		$("#balance").val(tot_amt);	
	}else
	{
		var advance=parseInt($("#advance").val());
		if(advance>0)
		{
			$("#balance").val(tot_amt-dis_amnt-advance);
		}else
		{
			$("#advance").val(tot_amt-dis_amnt);
			$("#balance").val("0");	
		}
	}
}
function load_test2(id,name,pr)
{
	var tr=document.createElement("tr");
	var td=document.createElement("td");
	var td1=document.createElement("td");
	var td2=document.createElement("td");
	var td3=document.createElement("td");
	var td4=document.createElement("td");
	td.className="slno_chk";
	td1.className="testid";
	td3.className="test_price";
	var ts=document.getElementsByClassName("testid");
	for(var i=0;i<ts.length;i++)
	{
		if(id==ts[i].innerHTML)
		{
			var j=1;
			var msg="Already Selected";	
		}
	}
	if(id=="T1064" || id=="T204" || id=="T259")
	{
		if($("#pinfo2").val()=="M")
		{
			var k=1;		
			var msg="Please change gender";
		
		}
		if($("#pinfo2").val()=="F")
		{
			if($("#pinfo3").val()<18)
			{
				var k=1;		
				var msg="Age has to be more than 17";
			}
		}         
		
	}
	if(!j && !k)
	{
		var t=document.getElementById('t_det');
		var num = t.rows.length;
		td.innerHTML=num;
		td1.innerHTML=id;
		td2.innerHTML=name;

		//...........checking for test master rate for centers

		$.post("pages/test_rate_center.php",
		{
			test:id,
			prc:pr,
		},
		function(data,status)
		{
				$(td3).text(data);
				cal_cost();
		})
		
		td4.innerHTML="<span class='text-danger'><i class='fa fa-times-circle fa-lg'></i></span>";

		td4.onclick=function(){t.removeChild(tr); cal_cost();};
		td4.style.cursor="pointer";
		tr.appendChild(td);
		tr.appendChild(td1);
		tr.appendChild(td2);
		tr.appendChild(td3);
		tr.appendChild(td4);
		t.appendChild(tr);
		$("#test").focus();
		if(num>2 && num<6)
		{
			var w=window.pageYOffset-50;
			$("html, body").animate({ scrollTop: w }, "slow");
		}
		else if(num>5)
		{
			var h=$("#test_res").height();
			$("#test_res").css({'max-height':h,'overflow':'scroll','overflow-x':'hidden'});
		}
		$("#test_up").slideDown("slow");
		$("#test").val("");
	
	}
	else
	{
		$("#test_sel").css({'opacity':'0.5'});
		$("#msg").text(msg);
		var x=$("#test_sel").offset();
		var w=$("#msg").width()/2;
		$("#msg").css({'top':x.top-50,'left':'50%','margin-left':-w+'px'});
		$("#msg").fadeIn(500);	
		setTimeout(function(){$("#msg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'});
		})},600);
	}
}
function cal_cost(val,t)
{
	var c=document.getElementsByClassName('test_price');
	var tot2=0;
	for(var i=0;i<c.length;i++)
	{
            tot2=tot2+parseInt(c[i].innerHTML);	
	}

	$("#total_t").html(tot2+"/-");
	if(t=="upd")
	{
		$("#total").val(tot2);
		if($("#save_pay").val()=="Save")
		{
			$("#advance").val(tot2);
		}	
	}
	else if(t=="sav")
	{
		$("#total").val(tot2);
		$("#advance").val(tot2);
		$("#advance").val(tot2);
	}
	if(val)
	{
		document.getElementById("dis_amnt").disabled=true
		document.getElementById("advance").disabled=true
		document.getElementById("balance").disabled=true;
		//document.getElementById("hid_ptest").focus();
	}
	else
	{
	var totm=parseInt($("#dis_amnt").val())+parseInt($("#advance").val())
	var bal=parseInt($("#total").val())-totm;
	$("#balance").val(bal);
	
	
		var sln=document.getElementsByClassName("slno_chk");
		for(var i=0;i<=sln.length;i++)
		{
			sln[i].innerHTML=i+1;		
		}
	}
	
}

function save_test(val,opd)
{
	//$("#save").prop("disabled",true);
	var test_id=document.getElementsByClassName("test_id");
	var test_p=document.getElementsByClassName("test_f");
	var test_all="";
	for(var i=0;i<test_id.length;i++)
	{
			test_all=test_all+"@"+test_id[i].value+"-"+test_p[i].innerHTML.trim();
	}
	//-------------Vaccu---------------
	var ex_id=document.getElementsByClassName("extra_id");
	var ex_p=document.getElementsByClassName("extra_price");
	var ex_all="";
	for(var i=0;i<ex_id.length;i++)
	{
			ex_all=ex_all+"@"+ex_id[i].value+"-"+ex_p[i].innerHTML.trim();
	}
	if(test_all!="")
	{
		if($("#pay_mode").val()=="Credit" && parseInt($("#total").val())>parseInt($("#credit_limit").val()))
		{
			bootbox.dialog({ message: "<h5>Credit limit exceeded</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
			},1500);
			return true;
		}else
		{
			var dis_amnt=$("#dis_amnt").val();
			if(dis_amnt>0)
			{
				if($("#dis_reason").val()=="")
				{
					$("#d_reason").show();
					$("#dis_reason").css({'border-color': '#F00'}).focus();
					return true;
				}
			}
			var balance=$("#balance").val();
			if(balance>0)
			{
				if($("#pay_mode").val()=="Credit")
				{
					$("#bal_reason").val("Credit");
				}else
				{
					$("#b_reason").show();
					if($("#bal_reason").val()=="")
					{
						$("#bal_reason").css({'border-color': '#F00'}).focus();
						return true;
					}
				}
			}
			if(balance<0)
			{
				$("#advance").focus();
				return true;
			}
			$("#loader").show();
			$("#save").prop("disabled",true);
			$.post("pages/global_insert_data.php",
			{
				type:"save_pat_test",
				mode:val,
				regd_fee:$("#regd_fee").val(),
				total:$("#total").val(),
				dis_per:$("#dis_per").val(),
				dis_amnt:$("#dis_amnt").val(),
				dis_reason:$("#dis_reason").val(),
				advance:$("#advance").val(),
				bal_reason:$("#bal_reason").val(),
				balance:$("#balance").val(),
				pay_mode:$("#pay_mode").val(),
				test_all:test_all,
				ex_all:ex_all,
				user:$("#user").text().trim(),
				uhid:$("#uhid").text().trim(),
				opd_id:opd,
			},
			function(data,status)
			{
				$("#loader").hide();
				bootbox.dialog({ message: "<h5>Saved</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					check_investigation(data,'load_new_tt')
				},1000);
			})
		}
	}else
	{
		bootbox.dialog({ message: "<h5>No Test Selected</h5>"});
		setTimeout(function(){
			bootbox.hideAll();
		},1500);
	}
}
function edit_conslt(uhid,opd_id,id,name,fee,validity)
{
	$.post("pages/load_pat_con_his.php",
	{
		uhid:uhid,
		opd_id:opd_id
	},
	function(data,status)
	{
		$("#load_all").html('');
		$("#edit_div").html(data);
		doc_load(id,name,fee,validity,'edit');
		$("#dis_per").focus();
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:500}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
	})
}
function load_test_print(uhid,opd)
{
	$.post("pages/pat_reg_prints.php",
	{
		uhid:uhid,
		opd_id:opd
	},
	function(data,status)
	{
		$("#results2").html(data);
		//$(".modal-dialog").css({'width':'500px'});		
		$("#mod2").click();
		//$("#mod_chk").val("1");
		$("#results").fadeIn(500);
	})
}
function print_indiv(uhid,visit)
{
	var norm=$(".norm:checked");
	var norm_l=0;
	if(norm.length>0)
	{
		for(var i=0;i<norm.length;i++)
		{
			norm_l=norm_l+"@"+$(norm[i]).val();
		}
	}
	
	var path=$(".path:checked");
	var path_l=0;
	if(path.length>0)
	{
		for(var j=0;j<path.length;j++)
		{
			path_l=path_l+"@"+$(path[j]).val();
		}
	}
	
	
	var rad=$(".rad:checked");
	var rad_l=0;
	if(rad.length>0)
	{
	for(var k=0;k<rad.length;k++)
		{
			rad_l=rad_l+"@"+$(rad[k]).val();
		}
	}

	//var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opd_id="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
	var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opdid="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
	wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');		
}
function cancel_pat(uhid,opd_id,typ)
{
	//alert(uhid+' '+opd_id)
	bootbox.dialog({
		//title: "Patient Re-visit ?",
		message: "<h5>Are you sure want to cancel</h5>",
		buttons: {
			cancel: {
				label: '<i class="icon-remove"></i> Cancel',
				className: "btn btn-inverse",
				callback: function() {
				  bootbox.hideAll();
				}
			},
			confirm: {
				label: '<i class="icon-ok"></i> Confirm',
				className: "btn btn-danger",
				callback: function() {
					check_result(uhid,opd_id,typ);
				}
			}
		}
	});
}
function check_result(uhid,opd_id,typ)
{
	$.post("pages/pat_cancel.php",
	{
		type:"pat_test_cancel_check",
		uhid:uhid,
		opd_id:opd_id,
		typ:typ,
	},
	function(data,status)
	{
		if(data==0)
		{
			cancel_note(uhid,opd_id,typ);
		}else
		{
			bootbox.alert("<h5>Patient can't be cancelled</h5>");
		}
	})
}
function cancel_note(uhid,opd_id,typ)
{
	bootbox.dialog({
		message: "Reason:<input type='text' id='note' autofocus />",
		title: "Patient Cancel",
		buttons: {
			main: {
			  label: "Save",
			  className: "btn-primary",
			  callback: function() {
				if($('#note').val()!='')
				{
					//$("#img").show();
					//$("#dialog_msg").show().html("Cancelling...");
					$.post("pages/pat_cancel.php",
					{
						type:"pat_test_opd",
						uhid:uhid,
						opd_id:opd_id,
						typ:typ,
						reason:$('#note').val(),
						user:$('#user').text().trim(),
					},
					function(data,status)
					{
						$("#dialog_msg").show().html("Cancelled");
						setTimeout(function(){
							$("#img").hide();
							if(typ=="test")
							{
								check_investigation();
							}
							if(typ=="doc")
							{
								check_appointment();
								//show_print_details(uhid,q,opd_id);
							}
						},500);
					})
				}else
				{
					bootbox.alert("Reason cannot blank");
				}
				
			  }
			}
		}
	});
}
function pat_emergency()
{
	var emerg_fee=parseInt($("#emerg_fee").val());
	if($("#pat_emergency").is(":checked"))
	{
		$("#emergency_fee").val(emerg_fee);
		var tot=parseInt($("#total").val())+emerg_fee;
		$("#total").val(tot);
		var advance=parseInt($("#advance").val())+emerg_fee;
		$("#advance").val(advance);
	}
	else
	{
		$("#emergency_fee").val(0);
		var tot=parseInt($("#total").val()-emerg_fee);
		$("#total").val(tot);
		var advance=parseInt($("#advance").val()-emerg_fee);
		$("#advance").val(advance);
	}
}









/////////////////// End Investigation /////////////////

/*------------------I-P-D---------------------*/

function check_ipd()
{
	$.post("pages/pat_ipd_details.php",
	{
		type:1
	},
	function(data,status)
	{
		$("#ipd_sect").html(data);
	})
}

function add_ipd_form()
{
	$.post("pages/pat_ipd_details.php",
	{
		type:2,
		uhid:$("#uhid").text().trim(),
	},
	function(data,status)
	{
		$("#ipd_sect").slideUp(300,function(){ $("#ipd_sect").html(data); $("#ipd_sect").slideDown(300);})
	})
}

function add_phone()
{
	$(".contact_no").parent().append("<br/><input type='number' class='contact_no'/>")	;
}

function load_bed_details()
{
	$("#bed_btn").removeClass("btn-danger");
	$("#bed_btn").addClass("btn-info");
	$("#bed_btn").css("border","");
	$.post("pages/pat_ipd_details.php",
	{
		type:3,
		uhid:$("#uhid").text().trim(),
	},
	function(data,status)
	{
		$("#results").html(data);
		$("#myModal1").animate({'top':'5%','left':'35%',"width":"1000px",'margin':'auto'},"slow");
		if($("#mod_chk").val()==0)
		{
			$("#mod").click();
			chk_bed_assign();
		}
	})
}

function chk_bed_assign()
{
	setInterval(function()
	{
		if($('#myModal1').hasClass('in'))
		{
			$.post("pages/pat_ipd_details.php",
			{
				type:3,
				uhid:$("#uhid").text().trim(),
			},
			function(data,status)
			{
				$("#results").html(data);
				
			})
		}
	},1500);
}

function bed_asign(w_id,b_id,w_name,b_no)
{
	$.post("pages/pat_ipd_details.php",
	{
		type:4,
		uhid:$("#uhid").text().trim(),
		w_id:w_id,
		b_id:b_id
	},
	function(data,status)
	{
		var bed_info="Ward: "+w_name+"<br/> Bed No: "+b_no;
		bed_info+="<input type='hidden' id='ward_id' value="+w_id+" />";
		bed_info+="<input type='hidden' id='bed_id' value="+b_id+" /> <br/>";
		$("#bed_info").html(bed_info);
	})
	
	/*
	$(".ward .btn").css({'background-color':'white'})
	$("#"+b_id+"").css({'background-color':'#5bc0de'})
	*/
}

function save_ipd_details(val)
{
	var foc="";
	
	/*
	var relation="";
	var rel=$(".relation_div");
	for(var i=1;i<=rel.length;i++)
	{
		relation=relation+"#@"+$("#relation"+i+"").val()+"%"+$("#rel_name"+i+"").val()+"%"+$("#rel_cno"+i+"").val();
	}
	*/
	if($("#occup").val()=="")
	{
		$("#occup").focus();
		foc="occup";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#add_typ").val()=="0")
	{
		$("#add_typ").focus();
		foc="add_typ";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#add_1").val()=="")
	{
		$("#add_1").focus();
		foc="add_1";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#city").val()=="")
	{
		$("#city").focus();
		foc="city";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#state").val()=="")
	{
		$("#state").focus();
		foc="state";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#postal").val()=="")
	{
		$("#postal").focus();
		foc="postal";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#country").val()=="")
	{
		$("#country").focus();
		foc="country";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#phone_typ").val()=="0")
	{
		$("#phone_typ").focus();
		foc="phone_typ";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#phone").val()=="")
	{
		$("#phone").focus();
		foc="phone";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#con_name").val()=="")
	{
		$("#con_name").focus();
		foc="con_name";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#con_add_type").val()=="0")
	{
		$("#con_add_type").focus();
		foc="con_add_type";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#con_add_1").val()=="")
	{
		$("#con_add_1").focus();
		foc="con_add_1";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#con_city").val()=="")
	{
		$("#con_city").focus();
		foc="con_city";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#con_state").val()=="Other" && $("#pstate").val()=="")
	{
		$("#pstate").focus();
		foc="pstate";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#con_postal").val()=="")
	{
		$("#con_postal").focus();
		foc="con_postal";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#con_country").val()=="")
	{
		$("#con_country").focus();
		foc="con_country";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#con_phone").val()=="")
	{
		$("#con_phone").focus();
		foc="con_phone";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#insurance").val()=="0")
	{
		$("#insurance").focus();
		foc="insurance";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#insurance").val()=="1" && $("#ins_id").val()=="")
	{
		$("#ins_id").focus();
		foc="ins_id";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#enc_typ").val()=="0")
	{
		$("#enc_typ").focus();
		foc="enc_typ";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#attend_doctor").val()=="0")
	{
		$("#attend_doctor").focus();
		foc="attend_doctor";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if($("#admit_doctor").val()=="0")
	{
		$("#admit_doctor").focus();
		foc="admit_doctor";
		$("#"+foc).css("border","1px solid #ff0000");
	}
	if(($("#bed_id").length)==0)
	{
		$("#bed_btn").removeClass("btn-info");
		$("#bed_btn").addClass("btn-danger");
		setTimeout(function(){$("#bed_btn").removeClass("btn-danger");$("#bed_btn").addClass("btn-info");},900)
		foc="bed_btn";
	}
	
	if(foc!="")
	{
		$("#"+foc).css("border","1px solid #ff0000");
		$("#"+foc).focus();
	}
	else
	{
		var dialog = bootbox.dialog({
		message: "<p id='bt_msg'><i class='fa fa-spin fa-spinner'></i> Saving...</p>"
		});
		$.post("pages/pat_ipd_details.php",
		{
			uhid:$("#uhid").text().trim(),
			occup:$("#occup").val(),
			add_typ:$("#add_typ").val(),
			add_1:$("#add_1").val(),
			add_2:$("#add_2").val(),
			city:$("#city").val(),
			state:$("#state").val(),
			postal:$("#postal").val(),
			country:$("#country").val(),
			phone_typ:$("#phone_typ").val(),
			phone:$("#phone").val(),
			email:$("#email").val(),
			con_name:$("#con_name").val(),
			con_rel:$("#con_rel").val(),
			con_add_type:$("#con_add_type").val(),
			con_add_1:$("#con_add_1").val(),
			con_add_2:$("#con_add_2").val(),
			con_city:$("#con_city").val(),
			con_state:$("#con_state").val(),
			pstate:$("#pstate").val(),
			con_postal:$("#con_postal").val(),
			con_country:$("#con_country").val(),
			con_phone:$("#con_phone").val(),
			con_email:$("#con_email").val(),
			insurance:$("#insurance").val(),
			ins_id:$("#ins_id").val(),
			enc_typ:$("#enc_typ").val(),
			attend_doc:$("#attend_doctor").val(),
			admit_doc:$("#admit_doctor").val(),	
			ward_id:$("#ward_id").val(),
			bed_id:$("#bed_id").val(),
			user:$("#user").text().trim(),
			val:val,
			ipd:$("#ipd_id").val(),
			type:5
		},
		function(data,status)
		{
			var ipd=data;
			var uhid=$("#uhid").text().trim();
			$("#bt_msg").html('<i class="fa fa-spin fa-spinner"></i> Saved. Redirecting to IPD Dashboard');
				
			setTimeout(function(){
						window.location="processing.php?param=52&uhid="+uhid+"&ipd="+ipd;
				 }, 2000);
			
			/*
			$("#ipd_id").val(data);
			$("#ipd_save").val("Update");
			bootbox.dialog({ message: "Saved"});
			$("#ipd_sect").slideUp(300,function(){ $("#ipd_sect").html('<input type="button" class="btn btn-info"  value="Add New IPD Details" onclick="add_ipd_form()"/>'); $("#ipd_sect").slideDown(300);});
			check_saved_ipd();
			*/
		})
	}
}

function load_det(val,i)
{
	if(val!="Self")
	{
		var det="<b>Name : </b> <input type='text' id='rel_name"+i+"' value=''/> <b>Contact No: </b><input type='text' id='rel_cno"+i+"'/>";
		$("#relation_details"+i+"").html(det).slideDown(200);
	}
	else
	{
		$("#relation_details"+i+"").slideUp(200,function(){ $("#rel_name"+i+"").val("");$("#rel_cno"+i+"").val(""); });
	}
}

function add_em_contact()
{
	var em_info="<hr/>";
	em_info+="Name  : <input type='text' class='emer_name'/> <br/>";
	em_info+="Phone  : <input type='text' class='emer_phone'/> <br/>";
	$(".emer_name").parent().append(em_info);
}

function add_relation()
{
	var rel_div=$(".relation_div").length;
	var chk=rel_div+1;
	var rel_data="<hr/> <div class='relation_div'><select id='relation"+chk+"' class='relation' onchange='load_det(this.value,"+chk+")'>	<option>Self</option><option>Father</option><option>Mother</option><option>Son</option><option>Daughter</option><option>Wife</option><option>Husband</option><option>Brother</option><option>Sister</option><option>Other</option></select>";
	var rel_data=rel_data+" <div id='relation_details"+chk+"' style='display:none'></div></div>";
	
	$("#relation_info").append(rel_data);
}

function check_saved_ipd()
{
	$.post("pages/pat_ipd_details.php",
	{
		type:6,
		uhid:$("#uhid").text().trim(),
	},
	function(data,status)
	{
		$("#ipd_saved").html(data);
	})		
}

function ipd_dash(uhid,ipd)
{
	window.location="processing.php?param=52&uhid="+uhid+"&ipd="+ipd;
}

function ipd_cancel()
{
	var ward=$("#ward_id").val();
	var bed=$("#bed_id").val();
	
	$.post("pages/pat_ipd_details.php",
	{
		type:7,
		ward:ward,
		bed:bed
	},
	function(data,status)
	{
		$("#bed_info").empty();
	})
	
}
/********************************************************/
