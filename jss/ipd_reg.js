function name_title_up(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#pat_name").focus();
	}
}

function caps_it(val,id,e)
{
	$("#"+id).css({'border-color': '#000'});
	var nval=val.toUpperCase();
	$("#"+id).val(nval);
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		if(id=="pat_name")
		{
			if(val!="")
			{
				$(".dob").focus();
			}else
			{
				$("#"+id).css({'border-color': '#F00'}).focus();
			}
		}
		if(id=="grdn_name")
		{
			if(val!="")
			{
				$("#sex").focus();
			}else
			{
				$("#"+id).css({'border-color': '#F00'}).focus();
			}
		}
		if(id=="address")
		{
			$("#"+id).css({'border-color': '#CCC'});
			$("#email").focus();
		}
		if(id=="g_name")
		{
			$("#g_relation").focus();
		}
		if(id=="g_relation")
		{
			$("#g_ph").focus();
		}
	}
	var n=val.length;
	if(n>0)
	{
		var numex=/^[A-Za-z0-9 ]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			document.getElementById(id).value=val;
		}
	}
	
}
function cal_age_dob(val,e)
{
	if(val=="")
	{
		//~ $("#age").focus();
		$("#age_y").focus();
	}
	else
	{
		var len=val.length;
		if(len<=11)
		{
			if(len==2 || len==5)
			{
				$(".dob").val(val+"-");
			}
			if(len>9)
			{
				//~ cal_age(e);
				cal_age_all(e);
			}
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				if(len>=10)
				{
					$("#sex").focus();
				}else
				{
					$("#age").focus();
				}
			}
			var n=val.length;
			var numex=/^[0-9-]+$/;
			if(val[n-1].match(numex))
			{
				
			}
			else
			{
				val=val.slice(0,n-1);
				$(".dob").val(val);
			}
		}
	}
}
function cal_age(e)
{
	$.post("pages/global_check.php",
	{
		type:"age_calculator",
		dob:$(".dob").val(),
	},
	function(data,status)
	{
		data=data.split("@");
		var age=data[0];
		var age_type=data[1];
		$("#age").val(age);
		$("#year").text(age_type);
		
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			age_type=age_type.toString();
			if(age_type=="Months")
			{
				//$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			}else if(age_type=="Days")
			{
				//$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			}else
			{
				if(age<18)
				{
					//$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
				}
				else
				{
					$("#g_id").fadeOut(100);
					$("#sex").focus();
				}
			}
			$("#sex").focus();
		}
	})
}
function age_check(a,e)
{
	var n=a.length;
	if(n>0)
	{
		$("#age").css({'border-color': '#000'});
		$(".dob").val("");
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==89)
		{
			$("#year").text("Years");
		}
		if(unicode==68)
		{
			$("#year").text("Days");
		}
		if(unicode==77)
		{
			$("#year").text("Months");
		}
		
		if(unicode==13)
		{
			$("#sex").focus();
			var age=$("#year").text();
			age=age.toString();
			if(age=="Months")
			{
				//$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			}else if(age=="Days")
			{
				//$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			}else
			{
				if($("#age").val()<18)
				{
					//$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
				}
				else
				{
					$("#g_id").fadeOut(100);
					$("#sex").focus();
				}
			}
			if(age=="")
			{
				$("#year").text("Years");
			}
		}
		//var n=a.length;
		var numex=/^[0-9.]+$/;
		if(a[n-1].match(numex))
		{
			
		}
		else
		{
			a=a.slice(0,n-1);
			document.getElementById("age").value=a;
		}
	}
}
function sex(e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#phone").focus();
	}
}
function phone_check(a,e)
{
	$("#phone").css({'border-color': '#000'});
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		if(a)
		{
			var n=a.length;
			if(n<10)
			{
				$("#phone").css({'border-color': '#F00'}).focus();
			}else
			{
				$("#phone").css({'border-color': '#CCC'});
				$("#g_name").focus();
			}
		}else
		{
			$("#phone").css({'border-color': '#F00'});
			$("#phone").focus();
		}
	}
	var n=a.length;
	var numex=/^[0-9]+$/;
	if(a[n-1].match(numex))
	{
		
	}
	else
	{
		a=a.slice(0,n-1);
		document.getElementById("phone").value=a;
	}
}
function pin(a,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#police").focus();
	}
	var n=a.length;
	var numex=/^[0-9]+$/;
	if(a[n-1].match(numex))
	{
		
	}
	else
	{
		a=a.slice(0,n-1);
		document.getElementById("pin").value=a;
	}
}
function city_vill(a,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		//$("#at_doc").focus();
		$("#post_office").focus();
	}
	var n=a.length;
	var numex=/^[A-z0-9,.@-_() ]+$/;
	if(a[n-1].match(numex))
	{
		
	}
	else
	{
		a=a.slice(0,n-1);
		document.getElementById("city").value=a;
	}
	var a=a.toUpperCase();
	$("#city").val(a);
}
function post_office(a,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#at_doc").focus();
	}
	$("#post_office").val($("#post_office").val().toUpperCase());
}
function police_station(a,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#state").focus();
	}
	var n=a.length;
	var numex=/^[A-z0-9,.@-_() ]+$/;
	if(a[n-1].match(numex))
	{
		
	}
	else
	{
		a=a.slice(0,n-1);
		document.getElementById("police").value=a;
	}
	var a=a.toUpperCase();
	$("#police").val(a);
}
function border_color_blur(id,a)
{
	var n=a.length;
	if(n>0)
	{
		$("#"+id).css({'border-color': '#CCC'});
	}
	if(id="email")
	{
		$("#email").css({'border-color': '#CCC'});
	}
}
function email_check(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		if(val)
		{
			var regex_email=/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z]+$/;
			if(val.match(regex_email))
			{
				$("#email").css({'border-color': '#CCC'});
				$("#r_doc").focus();
			}else
			{
				$("#email").css({'border-color': '#F00'});
			}
		}else
		{
			$("#r_doc").focus();
		}
	}else
	{
		$("#email").css({'border-color': '#CCC'});
	}
}


//-----------------------------------------Load Doctor List Onfocus-------------------------------||
function load_refdoc1()
{
		$("#ref_doc").fadeIn(500);
		$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
}
//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
var doc_tr=1;
var doc_sc=0;
function load_refdoc(val,e,typ)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#ref_doc").fadeIn(500);
				$.post("pages/load_refdoc_ajax.php",
				{
					type:typ,
					val:val,
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
			$("#r_doc").css('border','');
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
					$("#doc_id").val(docs[1]);
					$("#bedbtn").focus();
					//alert(docs[1]);
				}
				else
				{
					$("#doc_id").val(docs[1]);
					$("#bedbtn").focus();	
				}
			}
		}
}
function doc_load(id,name)
{
	$("#r_doc").val(name+"-"+id);
	$("#doc_id").val(id);
	$("#doc_info").html("");
	$("#ref_doc").fadeOut(500);
	$("#bedbtn").focus();
}
//////////////////////////////////////////////////////////////////////////////////////
//-----------------------------------------Load Doctor List Onfocus-------------------------------||
function atload_refdoc1()
{
		$("#atref_doc").fadeIn(500);
		$("#at_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
}
//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
var doc_trt=1;
var doc_sct=0;
function atload_refdoc(val,e)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			$("#at_doc").css('border','');
			if(unicode!=40 && unicode!=38)
			{
				$("#atref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#atref_doc").fadeIn(500);
				$.post("pages/load_atdoc_ajax.php"	,
				{
					val:val,
				},
				function(data,status)
				{
					$("#atref_doc").html(data);	
					doc_trt=1;
					doc_sct=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=doc_trt+1;
				var cc=document.getElementById("atdoc"+chk).innerHTML;
				if(cc)
				{
					doc_trt=doc_trt+1;
					$("#atdoc"+doc_trt).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_trt-1;
					$("#atdoc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_trt%1;
					if(z3==0)
					{
						$("#atref_doc").scrollTop(doc_sct)
						doc_sct=doc_sct+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=doc_trt-1;
				var cc=document.getElementById("atdoc"+chk).innerHTML;
				if(cc)
				{
					doc_trt=doc_trt-1;
					$("#atdoc"+doc_trt).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_trt+1;
					$("#atdoc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_trt%1;
					if(z3==0)
					{
						doc_sct=doc_sct-30;
						$("#atref_doc").scrollTop(doc_sct)
					}
				}
			}
			
		}
		else
		{
			$("#at_doc").css('border','');
			var cen_chk1=document.getElementById("chk_val2").value
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("atdvdoc"+doc_trt).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#at_doc").val(doc_naam+"-"+docs[1]);
				var d_in=docs[3];
				//$("#doc_mark").val(docs[5]);
				$("#atdoc_info").html(d_in);
				$("#atdoc_info").fadeIn(500);
				
				if($("#focus_chk").val()!="")
				{
					//$("html, body").animate({ scrollTop: 350 })	
					$("#atdoc_id").val(docs[1]);
					$("#ad_doc").focus();
					//alert(docs[1]);
				}
				else
				{
					$("#atdoc_id").val(docs[1]);
					$("#ad_doc").focus();	
				}
			}
			atdoc_load(docs[1],doc_naam);
		}
}
function atdoc_load(id,name)
{
	$("#at_doc").val(name+"-"+id);
	$("#atdoc_id").val(id);
	$("#atdoc_info").html("");
	$("#atref_doc").fadeOut(500);
	$("#ad_doc").focus();
}
////////////////////////////////////////////////////////////////////////////////////////////
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
				$.post("pages/load_addoc_ajax.php"	,
				{
					val:val,
				},
				function(data,status)
				{
					$("#adref_doc").html(data);	
					doc_trr=1;
					doc_scr=0;
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
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("addvdoc"+doc_trr).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#ad_doc").val(doc_naam+"-"+docs[1]);
				var d_in=docs[3];
				//$("#doc_mark").val(docs[5]);
				$("#addoc_info").html(d_in);
				$("#addoc_info").fadeIn(500);
				
				if($("#focus_chk").val()!="")
				{
					//$("html, body").animate({ scrollTop: 350 })	
					$("#addoc_id").val(docs[1]);
					$("#r_doc").focus();
					//alert(docs[1]);
				}
				else
				{
					$("#addoc_id").val(docs[1]);
					$("#r_doc").focus();	
				}
			}
			addoc_load(docs[1],doc_naam);
		}
}
function addoc_load(id,name)
{
	$("#ad_doc").val(name+"-"+id);
	$("#addoc_id").val(id);
	$("#addoc_info").html("");
	$("#adref_doc").fadeOut(500);
	$("#r_doc").focus();
}
//----------------------------------
function payment_mode(e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#blood_group").focus();
	}
}
function blood_group(e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#save").focus();
	}
}
/*
function sel_center(e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#save").focus();
	}
}
*/

function save_pat_info(typ)
{
	var error=0;
	var pat_name=$("#pat_name").val();
	if(pat_name=="")
	{
		$("#pat_name").css({'border-color': '#F00'}).focus();
		error=1;
		return true;
	}
	var age=$("#age").val();
	if(age=="")
	{
		$("#age").css({'border-color': '#F00'}).focus();
		error=1;
		return true;
	}
	var age_type=$("#year").text();
	age_type=age_type.toString();
	var gd_name=$("#grdn_name").val();
	if(age_type=="Months")
	{
		if(gd_name=="")
		{
			$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			error=1;
			return true;
		}
	}else if(age_type=="Days")
	{
		if(gd_name=="")
		{
			$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			error=1;
			return true;
		}
	}else
	{
		if(age<18)
		{
			if(gd_name=="")
			{
				$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
				error=1;
				return true;
			}
		}
	}
	var r_doc=$("#r_doc").val();
	if(r_doc=="")
	{
		$("#r_doc").focus();
		error=1;
		return true;
	}
	r_doc=r_doc.split("-");
	var ref_doc_id=r_doc[1];
	if(error==0)
	{
		var dis_amnt=$("#dis_amnt").val();
		var balance=$("#balance").val();
		if(dis_amnt>0)
		{
			if($("#dis_reason").val()=="")
			{
				$("#dis_reason").css({'border-color': '#F00'}).focus();
				exit();
			}
		}
		if(balance>0)
		{
			if($("#bal_reason").val()=="")
			{
				$("#bal_reason").css({'border-color': '#F00'}).focus();
				exit();
			}
		}
		if(balance<0)
		{
			$("#advance").focus();
			exit();
		}
		$("#save").prop('disabled', true);
		$.post("pages/global_insert_data.php",
		{
			type:typ,
			pat_name:$("#pat_name").val(),
			grdn_name:$("#grdn_name").val(),
			dob:$(".dob").val(),
			age:age,
			age_type:age_type,
			sex:$("#sex").val(),
			phone:$("#phone").val(),
			address:$("#address").val(),
			email:$("#email").val(),
			ref_doc_id:ref_doc_id,
			//payment_mode:$("#payment_mode").val(),
			payment_mode:"Cash",
			blood_group:$("#blood_group").val(),
			//sel_center:$("#sel_center").val(),
			
			//regd_fee:$("#regd_fee").val(),
			
			user:$("#user").text(),
			patient_id:$("#patient_id").val(),
		},
		function(data,status)
		{
			bootbox.dialog({ message: "Saved. Redirecting to Patient Dashboard"});
			setTimeout(function(){
				window.location="processing.php?param=3&uhid="+data+"&consult=1";
			 }, 1000);
		})
	}
	
}
function load_new_ref_doc()
{
	$.post("pages/ref_doc_new.php",	{ 	},
	function(data,status)
	{
		//$(".modal-dialog").css({'width':'600px'});
		$("#mod2").click();
		//$("#check_modal").val("1");
		
		$("#results").html(data);
		//$("#results").css({'height':'auto','width':'auto'});
		//var x=$("#results").height()/2;
		//var y=$("#results").width()/2;
		//document.getElementById("results").style.cssText+="margin-left:-"+y+"px;margin-top:-"+x+"px";
		
		$("#results").slideDown(500,function(){ $("#ref_doc_new").fadeIn(200);});
		
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
			phone:$("#doc_phone").val()
		},
		function(data,status)
		{
			$("#r_doc").val(data);
			var doc=data.split('-');
			$("#doc_id").val(doc[1]);
			$("#ref_doc_new").fadeOut(200);
			$("#check_modal").val("0");
			$("#mod2").click();
		})
	}else
	{
		$("#doc_name").focus();
	}
}

var emp_d=1;
var emp_div=0;
function load_emp(val,e,typ)
{
	$("#emp_list").slideDown(500);
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		var eid=$("#e_id"+emp_d+"").val();
		eid=eid.split("@@");
		var tst=$("#testt").val();
		load_emp_details(eid[0],eid[1]);
	}
	else if(unicode==38)
	{
		var chk=emp_d-1;
		var cc=$("#row_id"+chk+"").html();
		if(cc)
		{
			$(".all_pat_row").css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
			
			emp_d=emp_d-1;
			$("#row_id"+emp_d).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
			var emp_d1=emp_d+1;
			$("#row_id"+emp_d1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
			var z2=emp_d%1;
			if(z2==0)
			{
				emp_div=emp_div-30;
				$("#emp_list").scrollTop(emp_div)
				
			}
		}
	}
	else if(unicode==40)
	{
		var chk=emp_d+1;
		var cc=$("#row_id"+chk+"").html();
		if(cc)
		{
			$(".all_pat_row").css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
			
			emp_d=emp_d+1;
			$("#row_id"+emp_d).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
			var emp_d1=emp_d-1;
			$("#row_id"+emp_d1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
			var z2=emp_d%1;
			if(z2==0)
			{
				$("#emp_list").scrollTop(emp_div)
				emp_div=emp_div+30;
			}
		}
	}
	else
	{
		if(val.length>0)
		{
			$.post("pages/pat_regd_pat_master.php",
			{
				val:val,
				type:7,
				typ:typ,
				p_type_id:3,
			},
			function(data,status)
			{
				$("#emp_list").html(data);
			})
		}else if(val.length==0)
		{
			$("#emp_list").html("");
		}
	}
	
}
function load_emp_details(uhid,typ)
{
	//alert(uhid+' '+opd_id+' '+typ+' '+v_typ);
	$.post("pages/pat_regd_pat_master.php",
	{
		uhid:uhid,
		type:8,
		p_type_id:3,
	},
	function(data,status)
	{
		//alert(data);
		var val=data.split("@#@");
		
		const str = val[0];
		const arr = str.split(/ (.*)/);
		
		$("#name_title").val(arr[0]);
		$("#pat_name").val(arr[1]);
		
		$("#g_name").val(val[1]);
		$("#sex").val(val[2]);
		$(".dob").val(val[3]);
		$("#age").val(val[4]);
		$("#year").text(val[5]);
		$("#phone").val(val[6]);
		$("#address").val(val[7]);
		$("#g_ph").val(val[8]);
		$("#pin").val(val[9]);
		$("#police").val(val[10]);
		$("#state").val(val[11]);
		//$("#dist").val(val[12]);
		
		load_dist();
		setTimeout(function(){
			$("#dist").val(val[12]);
		},500);
		
		$("#city").val(val[13]);
		$("#post_office").val(val[14]);
		
		$("#uhid").val(val[15]);
		
		$("#marital_status").val(val[17]);
		$("#g_relation").val(val[18]);
		$("#patient_type").val(val[19]);
		//$("#esi_ip_no").val(val[20]);
		$("#income_id").val(val[21]);
		
		var admit=val[16];
		var admit=admit.split("###");
		if(admit[0]==1)
		{
			$("#save").fadeOut(500);
			$("#at_doc").val(admit[1]);
			$("#ad_doc").val(admit[2]);
			$("#r_doc").val(admit[3]);
			$("#bedbtn").text("Admitted Bed No "+admit[4]);
			$("#ipd_id_dash").val(admit[5]);
			
			$("#to_ipd_dashboard").fadeIn(500);
			$("#new").fadeIn(500);
		}else
		{
			$("#at_doc").val("");
			$("#ad_doc").val("");
			$("#r_doc").val("");
			$("#bedbtn").text("Click to view");
			$("#ipd_id_dash").val("");
			
			$("#save").fadeIn(500);
			$("#to_ipd_dashboard").fadeOut(500);
			$("#new").fadeIn(500);
			$("#at_doc").focus();
		}
		
		$("#emp_list").slideUp(500);
		
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:300}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
		
		cal_age_all();
	})
	
	
	/*bootbox.dialog({ message: "<b>Redirecting to Patient Dashboard</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
	
	setTimeout(function(){
		window.location="processing.php?param=52&uhid="+uhid+"&ipd="+opd_id;
	 }, 2000);*/
	
}


//-----------------------------------------Load Doctor List Onfocus-------------------------------||
function hguide_focus()
{
		$("#hguide_div").fadeIn(500);
		$("#hguide").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
}
//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
var doc_tr=1;
var doc_sc=0;
function hguide_up(val,e)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#hguide_div").html("<img src='../images/ajax-loader.gif' />");
				$("#hguide_div").fadeIn(500);
				$.post("pages/load_hguide_ajax.php"	,
				{
					val:val,
				},
				function(data,status)
				{
					$("#hguide_div").html(data);	
					doc_tr=1;
					doc_sc=0;
				})	
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
			
		}
		else
		{
			var cen_chk1=document.getElementById("chk_val2").value
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("dvhguide"+doc_tr).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#hguide").val(doc_naam+"-"+docs[1]);
				hguide_load(docs[1],doc_naam);
				$("#hguide_info").fadeIn(500);
				
				if($("#focus_chk").val()!="")
				{
					$("#bedbtn").focus();
				}
				else
				{
					$("#bedbtn").focus();	
				}
				
			}
		}
}
function hguide_load(id,name)
{
	$("#hguide").val(name+"-"+id);
	$("#hguide_id").val(id);
	$("#hguide_info").html("");
	$("#hguide_div").fadeOut(500);
	$("#bedbtn").focus();
}
