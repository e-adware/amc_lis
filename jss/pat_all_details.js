/////////////////// Start Consultation /////////////////
function check_appointment(q,opd_id,pin)
{
	//alert(q+" 1 "+opd_id+" 2 "+pin);
	$.post("pages/global_load.php",
	{
		type:"check_appointment_already",
		uhid:$("#uhid").text().trim(),
		pin:pin,
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
				maxDate: '0',
			});
		})
	}else
	{
		$("#ad_doc").val('');
		$("#adref_doc").html('');
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
		$("#con_ref_doc").fadeIn(500);
		$("#con_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},100);
}
function load_refdoc1_edit(edit_opd)
{
	$("#adref_doc").html("<img src='../images/ajax-loader.gif' />");
	$("#adref_doc").fadeIn(500);
	$.post("pages/global_load.php"	,
	{
		type:"show_con_doc",
		dept_id:$("#dept_id").val(),
		uhid:$("#uhid").text().trim(),
		edit_opd:edit_opd,
	},
	function(data,status)
	{
		$("#adref_doc").html(data);	
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
			$("#con_ref_doc").html("<img src='../images/ajax-loader.gif' />");
			$("#con_ref_doc").fadeIn(500);
			$.post("pages/global_load.php"	,
			{
				type:"show_con_doc",
				val:val,
				dept_id:$("#dept_id").val(),
				uhid:$("#uhid").text().trim(),
				edit_opd:"0",
			},
			function(data,status)
			{
				$("#con_ref_doc").html(data);	
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
					$("#con_ref_doc").scrollTop(doc_sc)
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
					$("#con_ref_doc").scrollTop(doc_sc)
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
			$("#con_doc").val(doc_naam);
			$("#con_doc_fee").val(con_doc_fee);
			$("#con_doc_validity").val(con_doc_validity);
			$("#con_ref_doc").fadeOut(500);
			
			//$("#appoint_date").focus();
			$("#dis_per").focus();
			load_payment(con_doc_id);
		}
	}
}
function con_doc_load(id,name,fee,validity,qq)
{
	$("#con_doc").css({'border-color': '#CCC'}).focus();
	$("#con_doc_id").val(id);
	$("#con_doc").val(name);
	$("#con_doc_fee").val(fee);
	$("#con_doc_validity").val(validity);
	$("#doc_info").html("");
	$("#con_ref_doc").fadeOut(500);
	//$("#appoint_date").focus();
	$("#dis_per").focus();
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
	
	var opd_allow_credit=$("#opd_allow_credit").val().trim();
	var opd_allow_credit_name=$("#opd_allow_credit_name").val().trim();
	
	//~ if($("#revisit_check").is(":checked"))
	if($("#revisit_check").val()==1)
	{
		$("#con_doc_fee").val(0);
		$("#visit_fee").val(0);
		$("#regd_fee").val(0);
	}
	else if($("#emergency_check").is(":checked"))
	{
		//~ $("#con_doc_fee").val($("#con_doc_fee_master").val());
		//~ $("#visit_fee").val($("#con_doc_fee_master").val());
		//~ $("#regd_fee").val($("#reg_fee_master").val());
		
		$("#con_doc_fee").val($("#visit_fee").val());
	}
	else
	{
		$("#visit_fee").val($("#con_doc_fee_master").val());
		$("#con_doc_fee").val($("#con_doc_fee_master").val());
		$("#regd_fee").val($("#reg_fee_master").val());
	}
	
	//$("#visit_fee").val($("#con_doc_fee").val());
	
	if($("#pat_emergency").is(":checked"))
	{
		var emerg_fee=parseInt($("#emerg_fee").val());
	}
	else
	{
		var emerg_fee=0;
	}
	if($("#pat_cross_consult").is(":checked"))
	{
		var cross_fee=parseInt($("#cross_fee").val());
	}
	else
	{
		var cross_fee=0;
	}
	
	if(!$("#con_doc_fee").val())
	{
		$("#visit_fee").val(0);
		$("#con_doc_fee").val(0);
	}
	if(!$("#regd_fee").val())
	{
		$("#regd_fee").val(0);
	}
	
	var tot=parseInt($("#con_doc_fee").val())+parseInt($("#regd_fee").val())+parseInt(emerg_fee)+parseInt(cross_fee);
	if(!tot)
	{
		tot=0;
	}
	//var tot=parseInt($("#regd_fee").val());
	
	$("#total").val(tot);
	
	var dis_amnt=parseInt($("#dis_amnt").val());
	var advance=parseInt($("#advance").val());
	if(advance>0)
	{
		//~ $("#advance").val(advance);
		//~ $("#balance").val(tot-advance-dis_amnt);
		
		$("#balance").val('0');
		$("#advance").val(tot);
	}else
	{
		if(opd_allow_credit==1)
		{
			$("#balance").val(tot);
			$("#advance").val('0');
			$("#bal_reason").val(opd_allow_credit_name);
			//~ $("#advance").prop('readonly', true);
			//~ $("#dis_per").prop('readonly', true);
			//~ $("#dis_amnt").prop('readonly', true);
		}
		else
		{
			$("#balance").val('0');
			$("#advance").val(tot);
			$("#bal_reason").val('');
			//~ $("#advance").prop('readonly', false);
			//~ $("#dis_per").prop('readonly', false);
			//~ $("#dis_amnt").prop('readonly', false);
		}
	}
}
function dis_per(val,e)
{
	var error=0;
	if(e.which==13)
	{
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:800}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
		$("#dis_amnt").focus();
	}
	var tot=$("#total").val();
	var dis_val=((tot*val)/100);
	dis_val=Math.round(dis_val);
	
	$("#dis_amnt").val(dis_val);
	
	if($("#pay_mode").val()=="Credit")
	{
		$("#balance").val(tot-dis_val);
		//$("#balance").val(tot);
		$("#advance").val("0");
	}else
	{
		$("#advance").val(tot-dis_val);
		//$("#advance").val(tot);
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
	per=Math.round(per);
	$("#dis_per").val(per);
	
	if($("#pay_mode").val()=="Credit")
	{
		$("#balance").val(tot-val);
		//$("#balance").val(tot);
		$("#advance").val("0");
	}else
	{
		$("#advance").val(tot-val);
		//$("#advance").val(tot);
		$("#balance").val("0");
	}
	
	if(val>0)
	{
		if(val>tot)
		{
			$("#dis_per").css({'border-color': '#F00'});
			$("#dis_amnt").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#dis_per").css({'border-color': '#CCC'});
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
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:800}, {
				duration: 1000,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});

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
	//var bal=tot-val;
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
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:800}, {
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
		if($("#cheque_ref_no").attr("type")=="text")
		{
			$("#cheque_ref_no").focus();
		}
		else
		{
			$("#visit_type_id").focus();
		}
	}
}
function cheque_ref_no_up(val,e)
{
	if(e.which==13)
	{
		$("#visit_type_id").focus();	
	}
}
function visit_type_change(val)
{
	if(val==3)
	{
		revisit_load_payment("0");
	}
	else
	{
		revisit_load_payment("1");
	}
}
function visit_type_id(val,e)
{
	if(e.which==13)
	{
		$("#save").focus();	
		//$("#card_id").focus();	
	}
}
function card_id(val,e)
{
	if(e.which==13)
	{
		$("#save").focus();	
	}
}
function pay_mode_lab(val,e)
{
	if(e.which==13)
	{
		if($("#cheque_ref_no_lab").attr("type")=="text")
		{
			$("#cheque_ref_no_lab").focus();
		}
		else
		{
			$("#save").focus();	
			//~ $("#card_id").focus();
		}
	}
}
function cheque_ref_no_lab_up(val,e)
{
	if(e.which==13)
	{
		//~ $("#card_id").focus();	
		$("#save").focus();	
	}
}
function card_id_lab(val,e)
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
	
	var r_doc=$("#r_doc").val();
	if(r_doc=="")
	{
		$("#r_doc").focus();
		return true;
	}
	r_doc=r_doc.split("-");
	var ref_doc_id=r_doc[1];
			
	var con_doc_id=$("#con_doc_id").val();
	if(con_doc_id==0)
	{
		$("#ad_doc").css({'border-color': '#F00'}).focus();
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
	var dis_amnt=parseInt($("#dis_amnt").val());
	if(dis_amnt>0)
	{
		if($("#dis_reason").val()=="")
		{
			$("#dis_reason").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
		var total=parseInt($("#total").val());
		if(total<dis_amnt)
		{
			$("#dis_amnt").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
	}
	
	var balance=$("#balance").val();
	if(balance>0)
	{
		if($("#bal_reason").val()=="")
		{
			$("#b_reason").fadeIn(300);
			$("#bal_reason").css({'border-color': '#F00'}).focus();
			error=1;
			return true;
		}
	}
	if(balance<0)
	{
		$("#advance").css({'border-color': '#F00'}).focus();
		error=1;
		return true;
	}
	var advance=$("#advance").val();
	if(advance<0)
	{
		$("#advance").css({'border-color': '#F00'}).focus();
		error=1;
		return true;
	}
	if($("#r_doc").val()=="")
	{
		$("#r_doc").css({'border-color': '#F00'}).focus();
		error=1;
		return true;
	}
	var emergency_check=$('#emergency_check:checked').val();
	if(emergency_check==1)
	{
		var emergency_check=1;
	}else
	{
		var emergency_check=0;
	}
	var pat_emergency=$('#pat_emergency:checked').val();
	if(pat_emergency==1)
	{
		var pat_emergency=1;
	}else
	{
		var pat_emergency=0;
	}
	var pat_cross_consult=$('#pat_cross_consult:checked').val();
	if(pat_cross_consult==1)
	{
		var cross_consult=1;
	}else
	{
		var cross_consult=0;
	}
	if($("#total").val()=="NaN" || $("#balance").val()=="NaN")
	{
		error=1;
		return true;
	}
	
	if($("#cheque_ref_no").attr("type")=="text" && $("#cheque_ref_no").val()=="")
	{
		$("#cheque_ref_no").focus();
		error=1;
		return true;
	}
	if(error==0)
	{
		//alert(typ);
		$("#save").hide();
		$("#save").prop("disabled",true);
		$("#loader").show();
		$.post("pages/global_insert_data.php",
		{
			type:"save_pat_appointment",
			mode:typ,
			ref_doc_id:ref_doc_id,
			dept_id:$("#dept_id").val(),
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
			emergency_check:emergency_check,
			pat_emergency:pat_emergency,
			emergency_fee:$("#emergency_fee").val(),
			cross_consult:cross_consult,
			cross_consult_fee:$("#cross_consult_fee").val(),
			pay_mode:$("#pay_mode").val(),
			sel_center:$("#sel_center").val(),
			doctor_session:$("#doctor_session").val(),
			visit_type_id:$("#visit_type_id").val(),
			card_id:$("#card_id").val(),
			cheque_ref_no:$("#cheque_ref_no").val(),
			
			user:$("#user").text().trim(),
			uhid:$("#uhid").text().trim(),
			opd_id:$("#opd_id_edit").val(),
		},
		function(data,status)
		{
			if(data==2)
			{
				$("#loader").hide();
				bootbox.alert("Already appointed");
				$("#save").prop("disabled",false);
				$("#save").show();
			}else if(data==4)
			{
				save_pat(typ);
			}else
			{
				//var param_str="&param_str="+$("#prev_para").val();
				var param_str="&param_str="+$("#param_id").val()+$("#refresh_back_id").val();
				
				$("#loader").hide();
				var q=data.split("@@");
				bootbox.dialog({ message: q[0]});
				setTimeout(function(){
					bootbox.hideAll();
					if(q[0]=="Saved")
					{
						if($("#adv_book_p").val()=='1')
						{
							$("#adv_book_div").hide();
							$("#load_all").html('').show();
							$("#print_div").html('').show();
							$("#edit_div").html('').show();
							$("#new_appointment_btn").hide();
						}
						//check_appointment('load_current',q[1]);
						window.location="processing.php?param=3&uhid="+$("#uhid").text().trim()+"&consult=1&opd="+q[1]+param_str;
					}
					if(q[0]=="Updated")
					{
						$("#edit_div").html('');
						//check_appointment('load_current',q[1]);
						window.location="processing.php?param=3&uhid="+$("#uhid").text().trim()+"&consult=1&opd="+q[1]+param_str;
					}
				},1000);
			}
		})
	}
}
//////// Save End /////////
function show_print_details(uhid,q,opd_id,pin)
{
	//alert(uhid+" 1 "+q+" 2 "+opd_id+" 3 "+pin);
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
		
		$("#doc_tab1").click();
		$("#print_con_receipt").focus();
		
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
	var user=$("#user").text().trim();
	url=url+"&user="+user;
	window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}
function money_receipt(url)
{
	var user=$("#user").text().trim();
	url=url+"&user="+user;
	window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
}
function show_tr_btn(val,opd)
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
	//alert(opd+'-_-'+typ+"---"+pin);
	if(pin=='0000')
	{
		load_add_test_form('0000','out_new');
	}else if(pin!='0000')
	{
		load_add_test_form(pin);
	}else
	{
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
			//$("#test").focus();
			$("#r_doc").focus();
		}else
		{
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:800}, {
				duration: 1000,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});
			//$("#dis_per").focus();
			$("#print_receipt").focus();
		}
		load_sel_center();
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
	$({myScrollTop:window.pageYOffset}).animate({myScrollTop:110}, {
		duration: 1000,
		easing: 'swing',
		step: function(val){
			window.scrollTo(0, val);
		}
	});
}
var t_val=1;
var t_val_scroll=0;

var _changeInterval = null;
function select_test_new(val,e)
{
	clearInterval(_changeInterval)
    _changeInterval = setInterval(function() {
        // Typing finished, now you can Do whatever after 2 sec
        clearInterval(_changeInterval);
        select_test_new_res(val,e);
    }, 500);
}
function select_test_new_res(val,e)
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
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:300}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
		//$("html, body").animate({ scrollTop: 250 })
		//$("#dis_per").focus();
		//$("#save").focus();
		
		if($("#dis_per").is('[readonly]'))
		{
			$("#advance").focus();
		}
		else
		{
			$("#dis_per").focus();
		}
	}
	else
	{
		$.post("pages/load_test_ajax.php",
		{
			test:val,
			uhid:$("#uhid").text().trim(),
			center_no:$("#sel_center").val(),
			cat:$("#cat").val(),
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
function load_test_click(id,name,rate)
{
	load_test_new(id,name,rate);
	$("#list_all_test").slideDown(400);
}
function load_test_new(id,name,rate)
{
	//$(".up_div").fadeIn(500);
	var test_chk= $('#test_list tr').length;
	if(test_chk==0)
	{	
		var test_add="<table class='table table-bordered' id='test_list'>";	
		test_add+="<tr><th colspan='3' style='background-color:#cccccc'>Tests</th><th style='text-align:right;background-color:#cccccc'>Total:<span id='test_total'></span></th></tr>";
		test_add+="<tr><td>1</td><td>"+name+"<input type='hidden' value='"+id+"' class='test_id'/></td><td contentEditable='false' onkeyup='load_cost(2)'><span class='test_f'>"+rate+"</span></td><td onclick='delete_rows(this,2)'><span class='text-danger'><i class='icon-remove'></i></span></td></tr>";
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
			var rate=parseInt(tot_ts[j].innerHTML);
			if(!rate)
			{
				rate=0;
			}
			tot=tot+rate;
		}
		$("#test_total").text(tot);
	}
	setTimeout(function(){add_grtotal();},100);
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
	//alert($("#center_discount").val());
	var center_discount=parseInt($("#center_discount").val());
	if(!center_discount)
	{
		center_discount=0;
	}
	$("#dis_per").val(center_discount);
	
	var regd_fee=parseInt($("#regd_fee").val());
	if(!regd_fee)
	{
		regd_fee=0;
	}
	var tot_amt=regd_fee+tst+extra;
	
	var dis_amount=((tot_amt*center_discount)/100);
	dis_amount=Math.round(dis_amount);
	
	$("#total").val(tot_amt);
	if($("#save").val()!="Update")
	{
		$("#dis_amnt").val(dis_amount);
		if($("#pay_mode").val()=="Credit")
		{
			$("#advance").val('0');
			$("#balance").val(tot_amt-dis_amount);	
		}else
		{
			$("#advance").val(tot_amt-dis_amount);
			$("#balance").val("0");	
		}
	}
	else
	{
		var discount_appr=$("#discount_appr").val();
		var dis_amnt=$("#dis_amnt").val();
		
		if(discount_appr>0)
		{
			if(dis_amnt>0)
			{
				var bal=(tot_amt-dis_amnt)-$("#advance").val();
				if(bal<0)
				{
					$("#balance").val('0');	
				}else
				{
					$("#balance").val(bal);	
				}
			}else
			{
				var bal=tot_amt-$("#advance").val();
				$("#balance").val(bal);	
			}
		}else
		{
			if(dis_amnt>0)
			{
				var bal=(tot_amt)-$("#advance").val();
				if(bal<0)
				{
					$("#balance").val('0');	
				}else
				{
					$("#balance").val(bal);	
				}
			}else
			{
				var bal=tot_amt-$("#advance").val();
				$("#balance").val(bal);	
			}
		}
	}
	
}
function pay_mode_change(val)
{
	var tot_amt=parseInt($("#total").val());
	if(!tot_amt)
	{
		tot_amt=0;
	}
	var dis_amnt=parseInt($("#dis_amnt").val());
	if(!dis_amnt)
	{
		dis_amnt=0;
	}
	
	$.post("pages/payment_load_data.php",
	{
		type:"payment_mode_change",
		val:val,
	},
	function(data,status)
	{
		var res=data.split("@#@");
		
		if(res[1]==2)
		{
			$("#cheque_ref_no").prop("type", "hidden");
			$("#advance").val('0').prop("disabled", true);
			var advance=parseInt($("#advance").val());
			$("#balance").val(tot_amt-dis_amnt-advance);
		}
		else
		{
			if(res[0]==0)
			{
				$("#cheque_ref_no").prop("type", "text");
			}
			else
			{
				$("#cheque_ref_no").prop("type", "hidden");
			}
			
			$("#advance").prop("disabled", false);
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
	})
	
	//~ if(val=="Credit")
	//~ {
		//~ $("#cheque_ref_no").prop("type", "hidden");
		//~ $("#advance").val('0').prop("disabled", true);
		//~ var advance=parseInt($("#advance").val());
		//~ $("#balance").val(tot_amt-dis_amnt-advance);	
	//~ }else
	//~ {
		 //~ if(val=="Card" || val=="Cheque" || val=="NEFT" || val=="RTGS")
		 //~ {
			 //~ $("#cheque_ref_no").prop("type", "text");
		 //~ }
		 //~ else
		 //~ {
			 //~ $("#cheque_ref_no").prop("type", "hidden");
		 //~ }
		
		//~ $("#advance").prop("disabled", false);
		//~ var advance=parseInt($("#advance").val());
		//~ if(advance>0)
		//~ {
			//~ $("#balance").val(tot_amt-dis_amnt-advance);
		//~ }else
		//~ {
			//~ $("#advance").val(tot_amt-dis_amnt);
			//~ $("#balance").val("0");	
		//~ }
	//~ }
}
function pay_mode_change_lab(val)
{
	var tot_amt=parseInt($("#total").val());
	var dis_amnt=parseInt($("#dis_amnt").val());
	
	$.post("pages/payment_load_data.php",
	{
		type:"payment_mode_change",
		val:val,
	},
	function(data,status)
	{
		var res=data.split("@#@");
		
		if(res[1]==2)
		{
			$("#cheque_ref_no_lab").prop("type", "hidden");
			$("#advance").val('0').prop("disabled", true);
			var advance=parseInt($("#advance").val());
			$("#balance").val(tot_amt-dis_amnt-advance);
		}
		else
		{
			if(res[0]==0)
			{
				$("#cheque_ref_no_lab").prop("type", "text");
			}
			else
			{
				$("#cheque_ref_no_lab").prop("type", "hidden");
			}
			
			$("#advance").prop("disabled", false);
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
	})
	
	//~ if(val=="Credit")
	//~ {
		//~ $("#cheque_ref_no_lab").prop("type", "hidden");
		//~ $("#advance").val('0').prop("disabled", true);
		//~ var advance=parseInt($("#advance").val());
		//~ $("#balance").val(tot_amt-dis_amnt-advance);
	//~ }else
	//~ {
		//~ if(val=="Card" || val=="Cheque" || val=="NEFT" || val=="RTGS")
		 //~ {
			 //~ $("#cheque_ref_no_lab").prop("type", "text");
		 //~ }
		 //~ else
		 //~ {
			 //~ $("#cheque_ref_no_lab").prop("type", "hidden");
		 //~ }
		
		//~ $("#advance").prop("disabled", false);
		//~ var advance=parseInt($("#advance").val());
		//~ if(advance>0)
		//~ {
			//~ $("#balance").val(tot_amt-dis_amnt-advance);
		//~ }else
		//~ {
			//~ $("#advance").val(tot_amt-dis_amnt);
			//~ $("#balance").val("0");	
		//~ }
	//~ }
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
	//alert(val+' '+opd);
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
			var advance=$("#advance").val();
			if(advance<0)
			{
				$("#advance").focus();
				return true;
			}
			
			var r_doc=$("#r_doc").val();
			if(r_doc=="")
			{
				$("#r_doc").focus();
				return true;
			}
			r_doc=r_doc.split("-");
			var ref_doc_id=r_doc[1];
			if(!ref_doc_id)
			{
				$("#r_doc").focus();
				return true;
			}
			$("#loader").show();
			$("#save").hide();
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
				ref_doc_id:ref_doc_id,
				sel_center:$("#sel_center").val(),
				cheque_ref_no:$("#cheque_ref_no_lab").val(),
				card_id:$("#card_id").val(),
				cat:$("#cat").val().trim(),
			},
			function(data,status)
			{
				var param_str="&param_str="+$("#param_id").val()+$("#refresh_back_id").val()+"&cat="+$("#cat").val().trim();
				
				var str=data.split('@');
				if(str[0]=='Saved' || str[0]=='Updated')
				{
					$("#loader").hide();
					bootbox.dialog({ message: "<h5>Saved</h5>"});
					setTimeout(function(){
						bootbox.hideAll();
						//check_investigation(str[1],'load_new_tt')
						window.location="processing.php?param=3&uhid="+str[2]+"&lab=1&opd="+str[1]+param_str;
					},1000);
				}else
				{
					save_test(val,opd);
				}
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
		//doc_load(id,name,fee,validity,'edit');
		//addoc_load(id,name,fee,validity,'edit');
		$("#dis_amnt").focus();
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
		load_master_payment(id);
	})
}
function load_master_payment(id)
{
	$.post("pages/global_load.php",
	{
		type:"load_master_payment",
		con_doc_id:id,
	},
	function(data,status)
	{
		var res=data.split("@@@");
		$("#con_doc_fee_master").val(res[0]);
		$("#regdd_fee").val(res[1]);
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
			var levelid=$("#lavel_id").val();
			if(levelid=='1' && typ=="test")
			{
				bootbox.dialog({
					message: "<h5>This patient's lab reporting has been done. If you cancelled, all lab reporting data will be lost. Are you sure want to cancel ?</h5>",
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
								cancel_note(uhid,opd_id,typ);
							}
						}
					}
				});
			}else
			{
				bootbox.alert("<h5>Patient can't be cancelled</h5>");
			}
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
								//check_investigation();
								bootbox.dialog({ message: "<b>Cancelled. Redirecting to Dashboard</b> "});
								setTimeout(function(){
									 window.location="index.php";
								},2000);
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
function pat_emergency(n)
{
	var regdd_fee=parseInt($("#regdd_fee").val());
	var emerg_fee=parseInt($("#emerg_fee").val());
	var cross_fee=parseInt($("#cross_fee").val());
	var advance=parseInt($("#advance").val());
	var dis_amnt=parseInt($("#dis_amnt").val());
	var balance=parseInt($("#balance").val());
	if($("#pat_emergency").is(":checked"))
	{
		$("#regd_fee").val('0');
		$("#emergency_fee").val(emerg_fee);
		var tot=parseInt($("#visit_fee").val())+emerg_fee;
		$("#total").val(tot);
		if(n==1)
		{
			$("#balance").val('0');
			$("#advance").val(tot-dis_amnt);
		}else if(n==2)
		{
			$("#balance").val(tot-advance-dis_amnt);
			$("#advance").val(advance);
		}
		$("#cross_consult_fee").val('0');
		$("#pat_cross_consult").prop('checked', false);
	}
	else
	{
		$("#regd_fee").val(regdd_fee);
		$("#emergency_fee").val(0);
		var tot=parseInt($("#visit_fee").val())+regdd_fee;
		$("#total").val(tot);
		if(n==1)
		{
			$("#balance").val('0');
			$("#advance").val(tot-dis_amnt);
		}else if(n==2)
		{
			$("#balance").val(tot-advance-dis_amnt);
			$("#advance").val(advance);
		}
	}
}

function advnc_book_save(uhid)
{
	$.post("pages/save_advance_book_data.php",
	{
		uhid:uhid,
	},
	function(data,status)
	{
		$("#load_all").html('').hide();
		$("#print_div").html('').hide();
		$("#edit_div").html('').hide();
		$("#adv_book_div").html(data);
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


//~ function adload_refdoc1()
//~ {
		//~ $("#adref_doc").fadeIn(500);
		//~ $("#ad_doc").select();
		//~ setTimeout(function(){ $("#chk_val2").val(1)},1000);
		
		//~ $({myScrollTop:window.pageYOffset}).animate({myScrollTop:500}, {
			//~ duration: 1000,
			//~ easing: 'swing',
			//~ step: function(val){
				//~ window.scrollTo(0, val);
			//~ }
		//~ });
//~ }
//~ //----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
//~ var doc_trr=1;
//~ var doc_scr=0;
//~ function adload_refdoc(val,e)
//~ {
		//~ var unicode=e.keyCode? e.keyCode : e.charCode;
		//~ if(unicode!=13)
		//~ {
			//~ $("#ad_doc").css('border','');
			//~ if(unicode!=40 && unicode!=38)
			//~ {
				//~ $("#adref_doc").html("<img src='../images/ajax-loader.gif' />");
				//~ $("#adref_doc").fadeIn(500);
				//~ $.post("pages/opd_advnc_book_ajax.php"	,
				//~ {
					//~ type:"load_dept_doc_adv_save",
					//~ val:val,
					//~ dept_id:$("#deptt_id").val(),
					//~ uhid:$("#uhid").text().trim(),
				//~ },
				//~ function(data,status)
				//~ {
					//~ $("#adref_doc").html(data);	
					//~ doc_trr=1;
					//~ doc_scr=0;
				//~ })	
			//~ }
			//~ else if(unicode==40)
			//~ {
				//~ var chk=doc_trr+1;
				//~ var cc=document.getElementById("addoc"+chk).innerHTML;
				//~ if(cc)
				//~ {
					//~ doc_trr=doc_trr+1;
					//~ $("#addoc"+doc_trr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					//~ var doc_tr21=doc_trr-1;
					//~ $("#addoc"+doc_tr21).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					//~ var z3=doc_trr%1;
					//~ if(z3==0)
					//~ {
						//~ $("#adref_doc").scrollTop(doc_scr)
						//~ doc_scr=doc_scr+30;
					//~ }
				//~ }
			//~ }
			//~ else if(unicode==38)
			//~ {
				//~ var chk=doc_trr-1;
				//~ var cc=document.getElementById("addoc"+chk).innerHTML;
				//~ if(cc)
				//~ {
					//~ doc_trr=doc_trr-1;
					//~ $("#addoc"+doc_trr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					//~ var doc_tr21=doc_trr+1;
					//~ $("#addoc"+doc_tr21).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					//~ var z3=doc_trr%1;
					//~ if(z3==0)
					//~ {
						//~ doc_scr=doc_scr-30;
						//~ $("#adref_doc").scrollTop(doc_scr)
					//~ }
				//~ }
			//~ }
			
		//~ }
		//~ else
		//~ {
			//~ $("#ad_doc").css('border','');
			//~ var cen_chk1=document.getElementById("chk_val2").value
			//~ if(cen_chk1!=0)
			//~ {
				//~ var docs=document.getElementById("addvdoc"+doc_trr).innerHTML.split("#");
				//~ var doc_naam=docs[2].trim()
				//~ $("#ad_doc").val(doc_naam+"-"+docs[1]);
				//~ var d_in=docs[3];
				//~ //$("#doc_mark").val(docs[5]);
				//~ $("#addoc_info").html(d_in);
				//~ $("#visit_fee").html(d_in);
				//~ $("#con_doc_fee").html(d_in);
				//~ $("#addoc_info").fadeIn(500);
				
				//~ if($("#focus_chk").val()!="")
				//~ {
					//~ //$("html, body").animate({ scrollTop: 350 })	
					//~ $("#con_doc_id").val(docs[1]);
					//~ $("#dis_per").focus();
					//~ //alert(docs[1]);
				//~ }
				//~ else
				//~ {
					//~ $("#con_doc_id").val(docs[1]);
					//~ $("#dis_per").focus();	
				//~ }
			//~ }
			//~ addoc_load(docs[1],doc_naam,d_in,docs[4]);
		//~ }
//~ }
//~ function addoc_load(id,name,vfee,validity)
//~ {
	//~ $("#ad_doc").val(name+"-"+id);
	//~ $("#con_doc_id").val(id);
	//~ $("#addoc_info").html("");
	//~ $("#adref_doc").fadeOut(500);
	//~ $("#dis_per").focus();
	//~ $("#con_doc_fee").val(vfee);
	//~ $("#visit_fee").val(vfee);
	//~ //$("#con_doc_validity").val(validity);
	//~ load_payment(id);
//~ }
function deptt_sel()
{
	$.post("pages/opd_advnc_book_ajax.php",
	{
		type:"load_dept_doc_adv_save",
		dept_id:$("#deptt_id").val(),
		uhid:$("#uhid").text().trim(),
	},
	function(data,status)
	{
		$("#ad_doc").val('');
		$("#adref_doc").html(data);
	})
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

//-----------------------------------------Load Doctor List Onfocus-------------------------------||
function ref_load_refdoc1()
{
		$("#ref_doc").fadeIn(500);
		$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
}
//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
var doc_tr=1;
var doc_sc=0;
function ref_load_refdoc(val,e,typ)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#ref_doc").fadeIn(500);
				$.post("pages/load_refdoc_ajax.php"	,
				{
					val:val,
					type:typ,
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
			var cen_chk1=document.getElementById("chk_val2").value
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#r_doc").val(doc_naam+"-"+docs[1]);
				var d_in=docs[3];
				//$("#doc_mark").val(docs[5]);
				$("#doc_info").html(d_in);
				$("#doc_info").fadeIn(500);
				
				if($("#focus_chk").val()!="")
				{
					//$("html, body").animate({ scrollTop: 350 })	
					$("#ad_doc").focus();
				}
				else
				{
					$("#ad_doc").focus();	
				}
				
			}
		}
}
function doc_load(id,name)
{
	$("#r_doc").val(name+"-"+id);
	$("#doc_info").html("");
	$("#ref_doc").fadeOut(500);
	$("#ad_doc").focus();
}
function load_new_ref_doc()
{
	$.post("pages/ref_doc_new.php",	{ 	},
	function(data,status)
	{
		//$(".modal-dialog").css({'width':'600px'});
		$("#mod").click();
		$("#check_modal").val("1");
		
		$("#results").html(data);
		//$("#results").css({'height':'auto','width':'auto'});
		//var x=$("#results").height()/2;
		//var y=$("#results").width()/2;
		//document.getElementById("results").style.cssText+="margin-left:-"+y+"px;margin-top:-"+x+"px";
		
		$("#results").slideDown(500,function(){ $("#ref_doc_new").fadeIn(200);});
		
		setTimeout(function(){
			$("#doc_name").focus();
		},1000);
		
	})
}
function save_new_doc()
{
	if($("#doc_name").val()!="")
	{
		$.post("pages/ref_doc_new_save.php",
		{
			name:$("#doc_name").val(),
			qual:$("#doc_qual").val(),
			add:$("#doc_add").val(),
			phone:$("#doc_phone").val(),
			email:$("#doc_email").val(),
		},
		function(data,status)
		{
			$("#r_doc").val(data);
			$("#ref_doc_new").fadeOut(200);
			$("#check_modal").val("0");
			$("#mod").click();
		})
	}else
	{
		$("#doc_name").focus();
	}
}
function sel_center(e,val)
{
	//~ var unicode=e.keyCode? e.keyCode : e.charCode;
	//~ if(unicode==13)
	//~ {
		if(val=="lab")
		{
			$.post("pages/vaccu_load.php",
			{
				type:"vaccu_center",
				sel_center:$("#sel_center").val(),
			},
			function(data,status)
			{
				var res=data.split("@@@");
				$("#vaccu_charge").val(res[0]);
				$("#center_discount").val(res[1]);
				$("#dis_reason").val(res[2]);
				$("#test").focus();
			})
		}
	//~ }
}

//-----------------------------------------Load Doctor List Onfocus-------------------------------||
function adload_refdoc1()
{
	$("#adref_doc").fadeIn(500);
	$("#ad_doc").select();
	setTimeout(function(){ $("#chk_val2").val(1)},1000);
}
//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
var doc_trr=1;
var doc_scr=0;
function adload_refdoc(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode!=13)
	{
		$("#ad_doc").css('border','');
		if(unicode!=40 && unicode!=38)
		{
			$("#adref_doc").html("<img src='../images/ajax-loader.gif' />");
			$("#adref_doc").fadeIn(500);
			$.post("pages/global_load.php"	,
			{
				type:"show_con_doc",
				val:val,
				dept_id:$("#dept_id").val(),
				uhid:$("#uhid").text().trim(),
				edit_opd:"0",
			},
			function(data,status)
			{
				$("#adref_doc").html(data);	
				doc_tr=1;
				doc_sc=0;
			})
		}
		else if(unicode==40)
		{
			var chk=doc_trr+1;
			var cc=document.getElementById("addoc"+chk).innerHTML;
			if(cc)
			{
				doc_trr=doc_trr+1;
				$("#addoc"+doc_trr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var doc_tr21=doc_trr-1;
				$("#addoc"+doc_tr21).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z3=doc_trr%1;
				if(z3==0)
				{
					$("#adref_doc").scrollTop(doc_scr)
					doc_scr=doc_scr+30;
				}
			}
		}
		else if(unicode==38)
		{
			var chk=doc_trr-1;
			var cc=document.getElementById("addoc"+chk).innerHTML;
			if(cc)
			{
				doc_trr=doc_trr-1;
				$("#addoc"+doc_trr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var doc_tr21=doc_trr+1;
				$("#addoc"+doc_tr21).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z3=doc_trr%1;
				if(z3==0)
				{
					doc_scr=doc_scr-30;
					$("#adref_doc").scrollTop(doc_scr)
				}
			}
		}
		
	}
	else
	{
		$("#ad_doc").css('border','');
		var cen_chk1=document.getElementById("chk_val2").value
		//if(cen_chk1!=0)
		//{
			//alert($("#addvdoc"+doc_trr).text());
			var docs=document.getElementById("addvdoc"+doc_trr).innerHTML.split("#");
			//var doc_naam=docs[2].trim();
			//$("#ad_doc").val(doc_naam+"-"+docs[1]);
			
			var con_doc_id=docs[1].trim();
			var doc_naam=docs[2].trim();
			var con_doc_fee=docs[3].trim();
			var con_doc_validity=docs[4].trim();
			var con_doc_reg_fee=docs[5].trim();
			var con_doc_reg_validity=0;
			var visit_type_id=docs[6].trim();
			
			$("#con_doc_id").val(con_doc_id);
			$("#ad_doc").val(doc_naam);
			$("#con_doc_fee").val(con_doc_fee);
			$("#con_doc_validity").val(con_doc_validity);
			$("#reg_fee_master").val(con_doc_reg_fee);
			$("#adref_doc").fadeOut(500);
			
			//$("#appoint_date").focus();
			//$("#dis_per").focus();
			//$("#save").focus();
			
			if($("#dis_per").is('[readonly]'))
			{
				$("#advance").focus();
			}
			else
			{
				$("#dis_per").focus();
			}
			
			//load_payment(con_doc_id);
			addoc_load(con_doc_id,doc_naam,con_doc_fee,con_doc_validity,con_doc_reg_fee,con_doc_reg_validity,visit_type_id);
		//}
	}
}
function addoc_load(id,name,fee,valid,reg_fee,reg_valid,visit_type_id)
{
	load_patient_visit_type(id,visit_type_id);
	if($("#opd_allow_credit").val()=='2')
	{
		fee=0;
		reg_fee=0;
	}
	$("#ad_doc").val(name+"-"+id);
	$("#con_doc_id").val(id);
	$("#con_doc_fee").val(fee);
	$("#con_doc_fee_master").val(fee);
	$("#regd_fee").val(reg_fee);
	$("#regdd_fee").val(reg_fee);
	$("#reg_fee_master").val(reg_fee);
	$("#con_doc_validity").val(valid);
	$("#addoc_info").html("");
	$("#adref_doc").fadeOut(500);
	//$("#dis_per").focus();
	//$("#save").focus();
	
	if($("#dis_per").is('[readonly]'))
	{
		$("#advance").focus();
	}
	else
	{
		$("#dis_per").focus();
	}
	
	load_payment(id);
}
function load_patient_visit_type(id,visit_type_id)
{
	$.post("pages/global_load.php"	,
	{
		type:"load_patient_visit_type",
		con_doc:id,
		visit_type_id:visit_type_id,
		uhid:$("#sel_uhid").val(),
		opd_id:$("#sel_pin").val(),
	},
	function(data,status)
	{
		$("#visit_type_id").html(data);
		$("#visit_type_id").val(visit_type_id);
	})
}
//-----------------------------------------Load Doctor List Onfocus-------------------------------||
function labref_load_refdoc1()
{
		$("#ref_doc").fadeIn(500);
		$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
}
//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
var doc_tr=1;
var doc_sc=0;
function labref_load_refdoc(val,e,typ)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#ref_doc").fadeIn(500);
				$.post("pages/load_refdoc_ajax.php"	,
				{
					val:val,
					type:typ,
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
			var cen_chk1=document.getElementById("chk_val2").value
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#r_doc").val(doc_naam+"-"+docs[1]);
				var d_in=docs[3];
				//$("#doc_mark").val(docs[5]);
				$("#doc_info").html(d_in);
				$("#doc_info").fadeIn(500);
				
				if($("#focus_chk").val()!="")
				{
					//$("html, body").animate({ scrollTop: 350 })	
					$("#test").focus();
				}
				else
				{
					$("#test").focus();	
				}
				
			}
		}
}
function labdoc_load(id,name)
{
	$("#r_doc").val(name+"-"+id);
	$("#doc_info").html("");
	$("#ref_doc").fadeOut(500);
	$("#test").focus();
}

function sel_center_change(typ,val)
{
	var regd_fee=parseInt($("#regdd_fee").val());
	if(val=="C100")
	{
		$("#regd_fee").val(regd_fee);
		$("#total").val(regd_fee);
	}else
	{
		$("#regd_fee").val('0');
		if(regd_fee>0)
		{
			$("#dis_amnt").val(regd_fee);
			$("#dis_reason").val('By system');
		}
		$("#total").val(regd_fee);
	}
}

function pat_cross_consult()
{
	var regdd_fee=parseInt($("#regdd_fee").val());
	var emerg_fee=parseInt($("#emerg_fee").val());
	var cross_fee=parseInt($("#cross_fee").val());
	if($("#pat_cross_consult").is(":checked"))
	{
		$("#regd_fee").val('0');
		$("#cross_consult_fee").val(cross_fee);
		var tot=parseInt($("#visit_fee").val())+cross_fee;
		$("#total").val(tot);
		$("#balance").val(tot);
		$("#advance").val('0');
		$("#emergency_fee").val('0');
		$("#pat_emergency").prop('checked', false);
	}
	else
	{
		$("#regd_fee").val(regdd_fee);
		$("#cross_consult_fee").val(0);
		var tot=parseInt($("#visit_fee").val())+regdd_fee;
		$("#total").val(tot);
		$("#balance").val(tot);
		$("#advance").val('0');
	}
}


function revisit_check_ch(val)
{
	revisit_load_payment(val);
}
function revisit_load_payment(val)
{
	var con_doc_fee=$("#con_doc_fee_master").val();
	var regdd_fee=$("#reg_fee_master").val();
	
	if(val==0)
	{
		$("#visit_fee").val(0);
		$("#con_doc_fee").val(0);
		$("#regd_fee").val(0);
		$("#advance").val(0);
		
		$("#revisit_check").val(1);
		
	}
	if(val==1)
	{
		$("#revisit_check").val(0);
		
		$("#visit_fee").val(con_doc_fee);
		$("#con_doc_fee").val(con_doc_fee);
		$("#regd_fee").val(regdd_fee);
		
	}
	if($("#emergency_check").is(":checked"))
	{
		$("#emergency_check").prop("checked", false)
		$("#emergency_check").val(0);
		$("#visit_fee").prop("readonly", true);
		$("#regd_fee").prop("readonly", true);
	}
	load_payment($("#con_doc_id").val());
}
function emergency_check_ch(val)
{	
	var con_doc_fee=$("#con_doc_fee_master").val();
	var regdd_fee=$("#reg_fee_master").val();
	
	if(val==0)
	{
		$("#visit_fee").prop("readonly", false);
		$("#regd_fee").prop("readonly", false);
		
		$("#emergency_check").val("1");
		
		$("#visit_type_id").val("1").prop("disabled", true);
		
	}
	if(val==1)
	{
		$("#emergency_check").val("0");
		
		$("#visit_type_id").prop("disabled", false);
		
		$("#visit_fee").val(con_doc_fee).prop("readonly", true);
		$("#regd_fee").val(regdd_fee).prop("readonly", true);
		
	}
	
	if($("#revisit_check").is(":checked"))
	{
		$("#revisit_check").prop("checked", false)
		$("#revisit_check").val(0);
	}
	load_payment($("#con_doc_id").val());
}
function visit_fee_ch()
{
	load_payment($("#con_doc_id").val());
}
function regd_fee_ch()
{
	load_payment($("#con_doc_id").val());
}

function load_sel_center()
{
	//if($("#dis_reason").val()=="")
	//{
		$.post("pages/global_load.php",
		{
			type:"load_sel_center_test",
			sel_center:$("#sel_center").val(),
		},
		function(data,status)
		{
			var res=data.split("@@@");
			$("#dis_reason").val(res[0]);
			$("#center_discount").val(res[1]);
			
			load_cost(2);
		})
	//}
}

function sel_center_lab_change(val)
{
	//alert(val.value);
	load_sel_center();
}
