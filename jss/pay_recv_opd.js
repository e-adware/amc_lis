//---Enter Key Event--------------

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
	else if(unicode==27)
	{
    hide_div();
	}
}

function hide_div()
{
    	$("#results").slideUp(500,function(){ $("#back").fadeOut(500); document.getElementById("precv2").focus()});
}

function print_mon(url)
{
	var uhid=$("#pinfo1").val();
	var visit=$("#pinfo3").val();
	var user=$("#user").text();
	url=url+"&uhid="+uhid+"&opdid="+visit+"&user="+user;
	wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');	
}
//-------------------Ends------------------------------------


//-------------Loading Balance Patient List OnLoad--------------------||
function load_pat(val)
{
        if(val.length>0)
        {
			$.post("pages/load_balance_pat.php",
			{
					val:val,
					chk:$("#chk").val(),
			},
			function(data,status)
			{
					document.getElementById("precv2").focus();
					$("#bal_pat").html(data);
					$("#bal_pat").slideDown(500);
					bl_tr=1;
					bl_sc=0;
					
			})
		}
        
}

function load_pat_bill(val)
{
	if(val.length>0)
	{
		$.post("pages/load_balance_pat_bill_opd.php",
		{
				val:val,
				chk:$("#chk").val(),
		},
		function(data,status)
		{
				
				//document.getElementById("precv2").focus();
				$("#bal_pat").html(data);
				$("#bal_pat").slideDown(500);
				bl_tr=1;
				bl_sc=0;
				
		})
	}
	else
	{
		load_pat_bill('000');
	}
}
//-------------Loading Balance Patient List OnLoad Ends--------------------||






//-------------Selecting Patient from the List--------------------------------||
var bl_tr=1;
var bl_sc=0;
function sel_pat(val,e)
{
    var unicode=e.keyCode? e.keyCode : e.charCode;
    if(unicode!=13 && unicode!=38 && unicode!=40 && unicode!=113 && unicode!=112)
    {
        if(val.length>3)
		{
				load_pat(val);
		}
    }
    else if(unicode==13)
    {
            var uhid=document.getElementById("pat_reg"+bl_tr).innerHTML;
            var vis=$("#pat_vis"+bl_tr).text();
            load_all_info(uhid,vis);
    }
    else if(unicode==40)
    {
            chk=bl_tr+1;	
            var cc=document.getElementById("bal_tr"+chk).innerHTML;
            if(cc)
            {
                bl_tr=bl_tr+1;
                $("#bal_tr"+bl_tr).css({'color': 'red','transform':'scale(0.95)','transition':'all .2s'});
                var bl_tr1=bl_tr-1;
                $("#bal_tr"+bl_tr1).css({'color': 'black','transform':'scale(1.0)','transition':'all .2s'});
                var z2=bl_tr%1;
                if(z2==0)
                {
                    $("#bal_pat").scrollTop(bl_sc)
                    bl_sc=bl_sc+38;
                }
    
            }	

    }
    else if(unicode==38)
    {
            chk=bl_tr-1;	
            var cc=document.getElementById("bal_tr"+chk).innerHTML;
            if(cc)
            {
                bl_tr=bl_tr-1;
                $("#bal_tr"+bl_tr).css({'color': 'red','transform':'scale(0.95)','transition':'all .2s'});
                var bl_tr1=bl_tr+1;
                $("#bal_tr"+bl_tr1).css({'color': 'black','transform':'scale(1.0)','transition':'all .2s'});
                var z2=bl_tr%1;
                if(z2==0)
                {
                    bl_sc=bl_sc-38;
                    $("#bal_pat").scrollTop(bl_sc)
                    
                }
    
            }	

    }
    else if(unicode==113)
    {
            $("#chk").val("1");
            load_pat(val);
    }
    else if(unicode==112)
    {
            $("#chk").val("0");
            load_pat(val);
    }


    


}

var bl_tr1=1;
var bl_sc1=0;
function sel_pat_bill(val,e)
{
    var unicode=e.keyCode? e.keyCode : e.charCode;
    if(unicode!=13 && unicode!=38 && unicode!=40 && unicode!=113 && unicode!=112)
    {
        if(val.length>2)
		{
			load_pat_bill(val);
		}
    }
    else if(unicode==13)
    {
            var uhid=document.getElementById("pat_reg"+bl_tr1).innerHTML;
            var vis=$("#pat_vis"+bl_tr1).text();
            //var reg=$("#pat_reg1"+bl_tr1).text();
            load_all_info(uhid,vis);
    }
    else if(unicode==40)
    {
            chk=bl_tr1+1;	
            var cc=document.getElementById("bal_tr"+chk).innerHTML;
            if(cc)
            {
                bl_tr1=bl_tr1+1;
                $("#bal_tr"+bl_tr1).css({'color': '#419641','font-weight': 'bold','transform':'scale(0.95)','transition':'all .2s'});
                var bl_tr2=bl_tr1-1;
                $("#bal_tr"+bl_tr2).css({'color': 'black','font-weight': '100','transform':'scale(1.0)','transition':'all .2s'});
                var z2=bl_tr1%1;
                if(z2==0)
                {
                    $("#bal_pat").scrollTop(bl_sc1)
                    bl_sc1=bl_sc1+38;
                }
    
            }	

    }
    else if(unicode==38)
    {
            chk=bl_tr1-1;	
            var cc=document.getElementById("bal_tr"+chk).innerHTML;
            if(cc)
            {
                bl_tr1=bl_tr1-1;
                $("#bal_tr"+bl_tr1).css({'color': '#419641','font-weight': 'bold','transform':'scale(0.95)','transition':'all .2s'});
                var bl_tr2=bl_tr1+1;
                $("#bal_tr"+bl_tr2).css({'color': 'black','font-weight': '100','transform':'scale(1.0)','transition':'all .2s'});
                var z2=bl_tr1%1;
                if(z2==0)
                {
                    bl_sc1=bl_sc1-38;
                    $("#bal_pat").scrollTop(bl_sc1);
                    
                }
    
            }	

    }
    else if(unicode==113)
    {
            $("#chk").val("1");
            load_pat(val);
    }
    else if(unicode==112)
    {
            $("#chk").val("0");
            load_pat(val);
    }


}
//-------------Selecting Patient from the List  Ends--------------------------------||



//-----------------------Loading Patient Info-----------------------------------------||
function load_all_info(uhid,vis)
{
    $.post("pages/patient_balance_info_opd.php",
    {
        uhid:uhid,
        vis:vis
    },
    function(data,status)
    {
        var data=data.split("%");
        var pinf=data[0].split("$");
        $("#pay1").val(pinf[0]);
        $("#pay2").val(pinf[1]);
        $("#pay4").val(pinf[2]);
        $("#pay5").val(pinf[3]);

        var inf=data[1].split("$");
        $("#n_reg").val(vis);
        $("#pinfo1").val(uhid);
        $("#pinfo2").val(inf[0]);
        $("#pinfo3").val(inf[1]);
        $("#pinfo4").val(inf[2]);
        $("#pinfo5").val(inf[3]);
        $("#pinfo6").val(inf[4]);
        $("#pinfo7").val(inf[5]);
        $("html, body").animate({ scrollTop: 500 },"slow",function(){document.getElementById("pay3").focus()})

    })
}
//----------------------Loading Patient Info End----------------------------------||



//--------------------Checking Payment---------------------------------------||
function sel_payment(val,e)
{
    var unicode=e.keyCode? e.keyCode : e.charCode;
    if(unicode!=13)
    {
		var bill_amount=parseInt($("#pay1").val());
		if(!bill_amount)
		{
			bill_amount=0;
		}
		var past_discount=parseInt($("#pay2").val());
		if(!past_discount)
		{
			past_discount=0;
		}
		var past_paid=parseInt($("#pay5").val());
		if(!past_paid)
		{
			past_paid=0;
		}
		
		var discount=parseInt($("#discount_now").val());
		if(!discount)
		{
			discount=0;
		}
		var tax_deduct=parseInt($("#tax_deduct").val());
		if(!tax_deduct)
		{
			tax_deduct=0;
		}
		var now_pay=parseInt($("#pay3").val());
		if(!now_pay)
		{
			now_pay=0;
		}
		
        tot=bill_amount-past_discount;
        bal=tot-past_paid;
		
		bal=bal-discount-tax_deduct;
		
        bal1=bal-now_pay;
        $("#pay4").val(bal1);
        if(bal1<0)
        {
            $("#pay4").animate({marginLeft:'0px',},500);
            $("#pay4").css({'border-color':'#FE0002','box-shadow':'0 0 10px rgba(254, 0, 2, 0.5)'});
            document.getElementById("recv").disabled=true;
        }
        else
        {
            $("#pay4").animate({marginLeft:'0px',},500)
            $("#pay4").css({'border-color':'#cccccc'})
            document.getElementById("recv").disabled=false;
        }
    }
    else
    {
        if($("#pay3").val()!="" && $("#pay4").val()>=0)
        {
            //~ document.getElementById("pay6").focus();
            $("#pay6").focus();
        }
    }
}
function discount_now_up(val,e)
{
	var bill_amount=parseInt($("#pay1").val());
	if(!bill_amount)
	{
		bill_amount=0;
	}
	var past_discount=parseInt($("#pay2").val());
	if(!past_discount)
	{
		past_discount=0;
	}
	var past_paid=parseInt($("#pay5").val());
	if(!past_paid)
	{
		past_paid=0;
	}
	
	var discount=parseInt($("#discount_now").val());
	if(!discount)
	{
		discount=0;
	}
	var tax_deduct=parseInt($("#tax_deduct").val());
	if(!tax_deduct)
	{
		tax_deduct=0;
	}
	var now_pay=parseInt($("#pay3").val());
	if(!now_pay)
	{
		now_pay=0;
	}
	
	if(discount>0)
	{
		$("#discount_reason").fadeIn(500);
	}
	else
	{
		$("#discount_reason").fadeOut(500);
	}
	
	var unicode=e.keyCode? e.keyCode : e.charCode;
    if(unicode!=13)
    {
        tot=bill_amount-past_discount;
        bal=tot-past_paid;
		
		bal=bal-discount-tax_deduct;
		
        bal1=bal-now_pay;
        $("#pay4").val(bal1);
        if(bal1<0)
        {
            $("#pay4").animate({marginLeft:'0px',},500);
            $("#pay4").css({'border-color':'#FE0002','box-shadow':'0 0 10px rgba(254, 0, 2, 0.5)'});
            document.getElementById("recv").disabled=true;
        }
        else
        {
            $("#pay4").animate({marginLeft:'0px',},500)
            $("#pay4").css({'border-color':'#cccccc'})
            document.getElementById("recv").disabled=false;
        }
    }
    else
    {
		if(discount>0)
		{
			$("#discount_reason").focus();
		}
		else
		{
			$("#pay3").focus();
		}
    }
}
function discount_reason_up(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
    if(unicode==13)
    {
		if($("#discount_reason").val()!="")
		{
			$("#pay3").focus();
		}
	}
}
function tax_deduct_up(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
    if(unicode!=13)
    {
		var bill_amount=parseInt($("#pay1").val());
		if(!bill_amount)
		{
			bill_amount=0;
		}
		var past_discount=parseInt($("#pay2").val());
		if(!past_discount)
		{
			past_discount=0;
		}
		var past_paid=parseInt($("#pay5").val());
		if(!past_paid)
		{
			past_paid=0;
		}
		
		var discount=parseInt($("#discount_now").val());
		if(!discount)
		{
			discount=0;
		}
		var tax_deduct=parseInt($("#tax_deduct").val());
		if(!tax_deduct)
		{
			tax_deduct=0;
		}
		var now_pay=parseInt($("#pay3").val());
		if(!now_pay)
		{
			now_pay=0;
		}
		
        tot=bill_amount-past_discount;
        bal=tot-past_paid;
		
		bal=bal-discount-tax_deduct;
		
        bal1=bal-now_pay;
        $("#pay4").val(bal1);
        if(bal1<0)
        {
            $("#pay4").animate({marginLeft:'0px',},500);
            $("#pay4").css({'border-color':'#FE0002','box-shadow':'0 0 10px rgba(254, 0, 2, 0.5)'});
            document.getElementById("recv").disabled=true;
        }
        else
        {
            $("#pay4").animate({marginLeft:'0px',},500)
            $("#pay4").css({'border-color':'#cccccc'})
            document.getElementById("recv").disabled=false;
        }
    }
    else
    {
        $("#discount_now").focus();
    }
}
function next_ev(e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
    if(unicode==13)
    {
		if($("#cheque_ref_no").attr("type")=="text")
		{
			$("#cheque_ref_no").focus();
		}
		else
		{
			$("#recv").focus();
		}
	}
}
function cheque_ref_no_up(val,e)
{
	if(e.which==13)
	{
		$("#recv").focus();	
	}
}
//--------------------Checking Payment Ends---------------------------------------||



//--------------------Receive payment-----------------------------------------------||
function recv_payment()
{
	var bill_amount=parseInt($("#pay1").val());
	if(!bill_amount)
	{
		bill_amount=0;
	}
	var past_discount=parseInt($("#pay2").val());
	if(!past_discount)
	{
		past_discount=0;
	}
	var past_paid=parseInt($("#pay5").val());
	if(!past_paid)
	{
		past_paid=0;
	}
	
	var discount=parseInt($("#discount_now").val());
	if(!discount)
	{
		discount=0;
	}
	var tax_deduct=parseInt($("#tax_deduct").val());
	if(!tax_deduct)
	{
		tax_deduct=0;
	}
	var now_pay=parseInt($("#pay3").val());
	if(!now_pay)
	{
		now_pay=0;
	}
	
	tot=bill_amount-past_discount;
	bal=tot-past_paid;
	
	bal=bal-discount-tax_deduct;
	
	bal1=bal-now_pay;
	
	if(discount>0 && $("#discount_reason").val()=="")
	{
		$("#discount_reason").focus();
		return false;
	}
	
	if(bal1<0)
	{
		$("#pay3").focus();
		return false;
	}
	
    document.getElementById("recv").disabled=true;
	
	$("#p_receive").css({'opacity':'0.5'});
	$("#msg").text("Receiving....");
	var w=$("#msg").width()/2;
	var h=$("#msg").height()/2
	$("#msg").css({'top':'80%','left':'50%','margin-top':-h,'margin-left':-w});
	$("#msg").fadeIn(500);

    $.post("pages/patient_balance_recv_opd.php",
    {
        discount:discount,
        tax_deduct:tax_deduct,
        now_pay:now_pay,
        discount_reason:$("#discount_reason").val(),
        cheque_ref_no:$("#cheque_ref_no").val(),
        uhid:$("#pinfo1").val(),
        opd:$("#pinfo3").val(),
        pay_mode:$("#pay6").val(),
		user:$("#user").text().trim(),
		
    },
    function(data,status)
    {
		if(data=="1")
		{
			$("#mrec").focus();
			$("#msg").text("Received");
			
			setTimeout(function(){$("#msg").fadeOut(500,function(){
				$("#p_receive").css({'opacity':'1.0'});
			})},1000);
			document.getElementById("mrec").focus();
			if($("#pay4").val()=="0")
			{
				document.getElementById("mrec").disabled=false;
			}
		}
		else
		{
			alert(data);
			//alert("Error. Try again later");
		}
    })
}
//--------------------Receive Payment End-----------------------------------------||

//--------------------Loading New---------------------------------------------------||
function load_new()
{
	window.location.reload(true);
}
//--------------------Loading New Ends---------------------------------------------------||

//-------------------Printing Reports-------------------------------------------------------||
function print_rep()
{
var reg=$("#pinfo1").val();
var pid=$("#pinfo3").val();
var p=pid.split("#");
var url="pages/pat_pay_recv.php?reg="+reg+"&center="+p[0]+"&pno="+p[1]
if(reg)
{
var wind=window.open(url,'Window','scrollbars=1,toolbar=0,height=670,width=1050');	
}

}
//--------------------Printing Ends---------------------------------------------------------||


//----------------------Extra Receipt--------------------------||
function load_xrec()
{
        $("#back").fadeIn(500);
        $("#results").css({'width':'650px','height':'380px'});
        var w=$("#results").width()/2;
        var h=$("#results").height()/2;
        document.getElementById("results").style.cssText+="margin-left:-"+w+"px;margin-top:-"+h+"px";
        $("#results").html("<img src='../images/ajax-loader.gif' />");
        $.post("pages/extra_rec.php",
        {
                
        },
        function(data,status)
        {
                $("#results").html(data);
                $("#results").slideDown(500);
                document.getElementById("pay").focus()
        })
}


function save_extra_rec()
{
    if($("#pay").val() && $("#desc").val())
    {
	    $.post("pages/save_extra_rec.php",
	    {
		    date:$("#date").val(),
		    pay:$("#pay").val(),
		    desc:$("#desc").val(),
		    user:$("#user").text(),
	    },
	    function(data,status)
	    {
		    $("#pay").val("");
		    $("#desc").val("");
		    $("#p_receive").css({'opacity':'0.5'});
		    $("#msg").text(data);
		    var w=$("#msg").width()/2;
		    var h=$("#msg").height()/2
		    $("#msg").css({'top':'80%','left':'50%','margin-top':-h,'margin-left':-w});
		    $("#msg").fadeIn(500);
		    setTimeout(function(){$("#msg").fadeOut(500,function(){$("#p_receive").css({'opacity':'1.0'});
					})},1000);
		    document.getElementById("cls").focus();
	    })
   }

}


//----------------------Daily Expenses--------------------------||

function load_daily_exp()
{
        $("#back").fadeIn(500);
        $("#results").css({'width':'900px','height':'800px'});
        var w=$("#results").width()/2;
        var h=$("#results").height()/2;
        document.getElementById("results").style.cssText+="margin-left:-"+w+"px;margin-top:-"+h+"px";
        $("#results").html("<img src='../images/ajax-loader.gif' />");
        $.post("pages/expense_detail.php",
        {
                
        },
        function(data,status)
        {
                $("#results").html(data);
                $("#results").slideDown(500,function(){document.getElementById("desc").focus()});
                load_exp_data();
        })
}


function load_exp_data()
{
    $.post("pages/expense_detail_load.php",
    {
            date:$("#date").val(),
    },
    function(data,status)
    {
            var data=data.split("#");
            $("#sl").val(data[1]);
            $("#exp_det").html(data[2]);

            
            var dt=$("#date").val();
            var dt1=data[0];
            if(dt1!=dt)
            {
                    document.getElementById("save").disabled=true;
            }
            else
            {
                    document.getElementById("save").disabled=false;
            }
            document.getElementById("save").value="Save";
    })

}


function load_exp_desc(val)
{
    $.post("pages/exp_desc.php",
    {
        val:val,
    },
    function(data,status)
    {
            $("#exp_dsc").html(data);
            $("#exp_dsc").slideDown(200);
    })
}

function hide_exp()
{
    $("#exp_dsc").slideUp(200);
}

var chk_f=0;
var ex_v=1;
var ex_sc=0;
function sel_exp_text(val,e)
{
    var unicode=e.keyCode? e.keyCode : e.charCode;
    if(unicode!=13 && unicode!=38 && unicode!=40)
    {
           var dsc=$("#desc").val();
           if(dsc!="")
           {
			   if(chk_f==0)
			   {
				load_exp_desc(val);
			   }
			   else
			   {
					hide_exp();
					ex_v=0;
					ex_sc=0;
			   }
		   }
		   else
		   {
				chk_f=0;
				load_exp_desc();
				ex_v=1;
		   }
    }
    else if(unicode==13)
    {
            if(chk_f==0)
            {
				var txt=$("#exp_txt"+ex_v).text();
				$("#desc").val(txt);
				 $("#exp_dsc").slideUp(200);
				chk_f=1;
			}
			else
			{
					document.getElementById("amount").focus();
			}
    }
     else if(unicode==40)
    {
        chk=ex_v+1;	
        var cc=document.getElementById("exp_d"+chk).innerHTML;
        if(cc)
        {
            ex_v=ex_v+1;
            $("#exp_d"+ex_v).css({'color': 'red','transform':'scale(0.95)','transition':'all .2s'});
            var ex_v1=ex_v-1;
            $("#exp_d"+ex_v1).css({'color': 'black','transform':'scale(1.0)','transition':'all .2s'});
            var z2=ex_v%1;
            if(z2==0)
            {
                $("#exp_dsc").scrollTop(ex_sc)
                ex_sc=ex_sc+38;
            }
        }	
        
    }	
        else if(unicode==38)
        {
             chk=ex_v-1;	
            var cc=document.getElementById("exp_d"+chk).innerHTML;
            if(cc)
            {
                ex_v=ex_v-1;
                $("#exp_d"+ex_v).css({'color': 'red','transform':'scale(0.95)','transition':'all .2s'});
                var ex_v1=ex_v+1;
                $("#exp_d"+ex_v1).css({'color': 'black','transform':'scale(1.0)','transition':'all .2s'});
                var z2=ex_v%1;
                if(z2==0)
                {
                         ex_sc=ex_sc-38;
                        $("#exp_dsc").scrollTop(ex_sc)
                   
                }
            }	
            
        }	


}

function desc_load(val)
{
    $("#desc").val(val);
    $("#exp_dsc").slideUp(200,function(){document.getElementById("desc").focus();});
}


function save_exp_det(val)
{
    $.post("pages/exp_det_save.php",
    {
            date:$("#date").val(),
            sl:$("#sl").val(),
            desc:$("#desc").val(),
            amount:$("#amount").val(),
            type:val,
    },
    function(data,status)
    {
            $("#p_receive").css({'opacity':'0.5'});
            $("#msg").text(data);
            var w=$("#msg").width()/2;
            var h=$("#msg").height()/2
            $("#msg").css({'top':'80%','left':'50%','margin-top':-h,'margin-left':-w});
            $("#msg").fadeIn(500);
            setTimeout(function(){$("#msg").fadeOut(500,function(){$("#p_receive").css({'opacity':'1.0'});$("#desc").val("");$("#amount").val("");document.getElementById("desc").focus();load_exp_data();})},1000);
            
    })

}


function load_exp_info(sl,des,am)
{
        $("#sl").val(sl),
        $("#desc").val(des),
        $("#amount").val(am),
        $("#save").val("Update");
}

function prev_page(url)
{
    var date=$("#date").val();
    var url=url+"?date="+date;
    var win=window.open(url,'','fullscreen=yes,scrollbars=yes');
}


function load_date(val)
{
        var nextDay;
        var example = $("#date").val();
        nextDay = new Date(example);
        if(val=="nxt")
        {
            nextDay.setDate(nextDay.getDate() + 1);
        }
        else
        {
            nextDay.setDate(nextDay.getDate() - 1);
        }
        var day=nextDay.getDate();
        if(day<10){day="0"+day;}
        var mon=nextDay.getMonth()+1;
        if(mon<10){mon="0"+mon;}
        var yr=nextDay.getFullYear();
        $("#date").val(yr+"-"+mon+"-"+day);
        load_exp_data()        
}

function new_exrec()
{
	document.getElementById("pay").value="";
	document.getElementById("desc").value="";
	document.getElementById("pay").focus();

}


function select_pmode(val)
{
	var p_tab="";
	if(val=="Card")
	{
		p_tab+="<table class='table table-bordered'>";
		p_tab+="<tr><th>Card Type</th><th><select name='c_type' id='pinfo18' class='pinfo'><option>Credit</option><option>Debit</option></select></th></tr>"	;
		p_tab+="<tr><th>Card No</th><th><input type='text' name='c_no' id='pinfo19' class='pinfo'/></th></tr>";
		p_tab+="<tr><th>Approval No</th><th><input type='text' name='c_apprv' id='pinfo20' class='pinfo'/></th></tr>";
		p_tab+="<tr><th>Bank Name</th><th id='bn'>";
		
				
		p_tab+="</th></tr>";
		p_tab+="</table>";
				
		$.post("pages/bank_name.php",{	},function(data,status)	{ $("#bn").html(data);	})
		
		var tot=$("#total").val();
		var dis=$("#pinfo15").val();
		var adv=tot-dis;
		$("#pinfo16").val(adv);
		$("#bal").val("0.00");
		
	}
	else if(val=="Cheque")
	{
		p_tab+="<table class='table table-bordered'>";
		p_tab+="<tr><th>Cheque No</th><th><input type='text' name='chq_no' id='pinfo18' class='pinfo'/></th></tr>"	;
		p_tab+="<tr><th>Cheque Date</th><th><input type='text' name='chq_date' id='pinfo19' class='pinfo'/></th></tr>";
		p_tab+="<tr><th>Bank Name</th><th><input type='text' name='chq_bank' id='pinfo20' class='pinfo'/></th></tr>";
		p_tab+="<tr><th>City</th><th><input type='text' name='chq_city' id='pinfo21' class='pinfo' onkeyup='pay_focs(event)'/></th></tr>";
		p_tab+="</table>";
	}
	else if(val=="DD")
	{
		p_tab+="<table class='table table-bordered'>";
		p_tab+="<tr><th>DD No</th><th><input type='text' name='dd_no' id='pinfo18' class='pinfo'/></th></tr>"	;
		p_tab+="<tr><th>DD Date</th><th><input type='text' name='dd_date' id='pinfo19' class='pinfo'/></th></tr>";
		p_tab+="<tr><th>Bank Name</th><th><input type='text' name='dd_bank' id='pinfo20' class='pinfo'/></th></tr>";
		p_tab+="<tr><th>City</th><th><input type='text' name='dd_city' id='pinfo21' class='pinfo' onkeyup='pay_focs(event)'/></th></tr>";
		p_tab+="</table>";
	}
	
	$("#p_mode_info").html(p_tab).fadeIn(400);
}
function pay_mode_change(val)
{
	if(val=="Card" || val=="Cheque" || val=="NEFT" || val=="RTGS")
	{
		$("#cheque_ref_no").prop("type", "text");
	}
	else
	{
		$("#cheque_ref_no").prop("type", "hidden");
	}
}
