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
			//~ if(val!="")
			//~ {
				//~ $("#sex").focus();
			//~ }else
			//~ {
				//~ $("#"+id).css({'border-color': '#F00'}).focus();
			//~ }
			$("#sex").focus();
		}
		if(id=="address")
		{
			$("#"+id).css({'border-color': '#CCC'});
			$("#email").focus();
		}
	}
	var n=val.length;
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
function cal_age_dob(val,e)
{
	var len=val.length;
	if(len<11)
	{
		if(len==2 || len==5)
		{
			$(".dob").val(val+"-");
		}
		if(len>9)
		{
			cal_age(e);
		}
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(len>0)
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
				$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			}else if(age_type=="Days")
			{
				$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			}else
			{
				if(age<18)
				{
					$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
				}
				else
				{
					$("#g_id").fadeOut(100);
					$("#sex").focus();
				}
			}
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
			var age=$("#year").text();
			age=age.toString();
			if(age=="Months")
			{
				$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			}else if(age=="Days")
			{
				$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			}else
			{
				if($("#age").val()<18)
				{
					$("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
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
		//$("#r_doc").focus();
		$("#save").focus();
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
				$("#name_title").focus();
			}
		}else
		{
			$("#phone").css({'border-color': '#CCC'});
			$("#name_title").focus();
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
function load_pat_detail_by_phone(val)
{
	var n=val.length;
	if(n==10)
	{
		$.post("pages/load_pat_detail_phone.php"	,
		{
			type:"lab_by_phone",
			val:val,
		},
		function(data,status)
		{
			if(data)
			{
				var val=data.split("@#@");
				bootbox.dialog({
					//title: "Patient Re-visit ?",
					message: "<h5>This number is already registered with <i>"+val[0]+"</i>. Are you sure want to use this number</h5>",
					buttons: {
						cancel: {
							label: '<i class="icon-remove"></i> Cancel',
							className: "btn btn-inverse",
							callback: function() {
							  bootbox.hideAll();
							  $("#pat_name").focus();
							}
						},
						confirm: {
							label: '<i class="icon-ok"></i> Use',
							className: "btn btn-info",
							callback: function() {
								user_phone_number(data);
							}
						}
					}
				});
			}else
			{
				$("#pat_name").focus();
				//~ $("#pat_name").val('');
				//~ $(".dob").val('');
				//~ $("#age").val('');
				//~ $("#year").text('Years');
				
				//~ $("#sex").val('');
				//~ //$("#r_doc").val('');
				//~ //$("#sel_center").val('C100');
				$("#patient_id").val('0');
				$("#save_type").val("save_pat_info");
			}
		})	
	}
}
function user_phone_number(data)
{
	var val=data.split("@#@");
	$("#pat_name").val(val[0]);
	$(".dob").val(val[1]);
	$("#age").val(val[2]);
	$("#year").text(val[3]);
	if(val[4])
	{
		$("#g_id").show();
		$("#grdn_name").val(val[4]);
	}
	$("#sex").val(val[5]);
	//$("#r_doc").val(val[7]+'-'+val[6]);
	//$("#sel_center").val(val[8]);
	$("#patient_id").val(val[9]);
	$("#save_type").val("revisit_pat_info");
	$("#save").focus();
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
function load_refdoc(val,e)
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
					$("#sel_center").focus();
				}
				else
				{
					$("#sel_center").focus();	
				}
				
			}
		}
}
function doc_load(id,name)
{
	$("#r_doc").val(name+"-"+id);
	$("#doc_info").html("");
	$("#ref_doc").fadeOut(500);
	$("#sel_center").focus();
}

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

function sel_center(e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		$("#hguide").focus();
	}
}


function save_pat_info(typ)
{
	var pin=$("#pin").val();
	
	bootbox.hideAll();
	
	var typ=$("#save_type").val();
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
	//~ if(age_type=="Months")
	//~ {
		//~ if(gd_name=="")
		//~ {
			//~ $("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			//~ error=1;
			//~ return true;
		//~ }
	//~ }else if(age_type=="Days")
	//~ {
		//~ if(gd_name=="")
		//~ {
			//~ $("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
			//~ error=1;
			//~ return true;
		//~ }
	//~ }else
	//~ {
		//~ if(age<18)
		//~ {
			//~ if(gd_name=="")
			//~ {
				//~ $("#g_id").fadeIn(100,function() { $("#grdn_name").focus()});
				//~ error=1;
				//~ return true;
			//~ }
		//~ }
	//~ }
	
	//~ var r_doc=$("#r_doc").val();
	//~ if(r_doc=="")
	//~ {
		//~ $("#r_doc").focus();
		//~ error=1;
		//~ return true;
	//~ }
	//~ var hguide_id=$("#hguide_id").val();
	//~ if(hguide_id=="")
	//~ {
		//~ $("#hguide").focus();
		//~ error=1;
		//~ return true;
	//~ }
	//~ r_doc=r_doc.split("-");
	//~ var ref_doc_id=r_doc[1];
	if(error==0)
	{
		$("#save").prop('disabled', true);
		$.post("pages/global_insert_data.php",
		{
			type:typ,
			category:"lab_reg",
			patient_type:$("#patient_type").val(),
			name_title:$("#name_title").val(),
			pat_name:$("#pat_name").val(),
			grdn_name:$("#grdn_name").val(),
			dob:$(".dob").val(),
			age:age,
			age_type:age_type,
			sex:$("#sex").val(),
			phone:$("#phone").val(),
			//address:$("#address").val(),
			//email:$("#email").val(),
			//ref_doc_id:ref_doc_id,
			//payment_mode:$("#payment_mode").val(),
			//payment_mode:"Cash",
			//blood_group:$("#blood_group").val(),
			//sel_center:$("#sel_center").val(),
			
			//regd_fee:$("#regd_fee").val(),
			
			user:$("#user").text().trim(),
			patient_id:$("#patient_id").val(),
			//hguide_id:hguide_id,
			
			entry_date:$("#entry_date").val(),
			entry_time:$("#entry_time").val(),
		},
		function(data,status)
		{
			var param_str="&param_str="+$("#param_id").val();
			
			var str=data.split("@@");
			if(str[0]=='0')
			{
				bootbox.dialog({ message: "<b>Saved. Redirecting to Patient Dashboard</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/>"});
				setTimeout(function(){
					window.location="processing.php?param=3&uhid="+str[1]+"&lab=1&opd="+pin+param_str;
					//window.location="processing.php?param=3&uhid="+data;
				 }, 800);
			 }else
			 {
				 save_pat_info(typ);
			 }
		})
	}
	
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
			$("#ref_doc_new").fadeOut(200);
			$("#check_modal").val("0");
			$("#mod").click();
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
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		var eid=$("#e_id"+emp_d+"").val();
		eid=eid.split("@@");
		var tst=$("#testt").val();
		load_emp_details(eid[0],eid[1],eid[2]);
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
				type:5,
				typ:typ,
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
function load_emp_details(uhid,pin,typ)
{
	//alert(uhid+' '+pin+' '+typ);
	
	var param_str="&param_str="+$("#param_id").val();
	
	bootbox.dialog({ message: "<b>Redirecting to Patient Dashboard</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
	
	if(typ=="uhid" || typ=="name")
	{
		setTimeout(function(){
			window.location="processing.php?param=3&uhid="+uhid+"&lab=1"+param_str;
		 }, 1000);
	}
	if(typ=="pin")
	{
		setTimeout(function(){
			window.location="processing.php?param=3&uhid="+uhid+"&lab=1&opd="+pin+param_str;
			//window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
		 }, 1000);
	}
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
					$("#save").focus();
				}
				else
				{
					$("#save").focus();	
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
	$("#save").focus();
}
