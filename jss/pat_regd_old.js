function caps_it(val,id,e)
{
	$("#"+id).css({'border-color': 'rgba(82,168,236,0.8)'});
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
		$("#age").css({'border-color': 'rgba(82,168,236,0.8)'});
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
		var numex=/^[0-9]+$/;
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
	$("#phone").css({'border-color': 'rgba(82,168,236,0.8)'});
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
				$("#address").focus();
			}
		}else
		{
			$("#phone").css({'border-color': '#CCC'});
			$("#address").focus();
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
					$("#payment_mode").focus();
				}
				else
				{
					$("#payment_mode").focus();	
				}
				
			}
		}
}
function doc_load(id,name)
{
	$("#r_doc").val(name+"-"+id);
	$("#doc_info").html("");
	$("#ref_doc").fadeOut(500);
	$("#payment_mode").focus();
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
			payment_mode:$("#payment_mode").val(),
			blood_group:$("#blood_group").val(),
			
			regd_fee:$("#regd_fee").val(),
			
			user:$("#user").text().trim(),
			patient_id:$("#patient_id").val(),
		},
		function(data,status)
		{
			bootbox.dialog({ message: "Saved"});
			data=data.split("@@");
			var uhid=data[0];
			var opd=data[1];
			setTimeout(function(){
				window.location="processing.php?param=3&uhid="+uhid;
				//window.location="processing.php?param=3&uhid="+uhid+"&opd="+opd;
			 }, 2000);
		})
	}
	
}
