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


//-------------Loading Balance Patient List OnLoad--------------------||


function load_pat_bill(val)
{
        if(val.length>0)
        {
			$.post("pages/ph_load_balance_pat_bill.php",
			{
					val:val,
					phserchtype:$("#ph_srch_type").val(),
					
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
	
    $.post("pages/global_load_g.php",
    {
        blno:uhid,
        type:"balance_receipt",
    },
    function(data,status)
    {
		
        var val=data.split("@");
     
        $("#selectbill").val(val[0]);
        $("#txtcust").val(val[1]);
		$("#txtdis").val(val[2]);
		$("#txtttl").val(val[3]);
		$("#txtpaid").val(val[4]);
		$("#txtblnce").val(val[5]);
		$("#txtcrdtamt").focus();
		$("html, body").animate({ scrollTop: 500 },"slow",function(){document.getElementById("pay3").focus()})

    })
}
//----------------------Loading Patient Info End----------------------------------||





//--------------------Loading New---------------------------------------------------||
function load_new()
{
	window.location.reload(true);
}
//--------------------Loading New Ends---------------------------------------------------||


