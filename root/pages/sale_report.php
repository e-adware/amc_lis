<?php
//~ $qq=mysqli_query($link,"select bill_no, total_amt from ph_sell_master");
//~ while($r=mysqli_fetch_array($qq))
//~ {
	//~ mysqli_query($link,"update ph_item_return set prev_amt='$r[total_amt]' where bill_no='$r[bill_no]'");
//~ }
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Sale Report</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-bottom:10px;text-align:center;">
		<b>From</b>
		<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<b>To</b>
		<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<?php
		if($p_info['levelid']=="1")
		{
		?>
		<span style="float:right;">
			<button type="button" class="btn btn-mini btn-warning" onclick="all_setup()"><i class="icon-bar-chart icon-large"></i></button>
		</span>
		<?php
		}
		?>
	</div>
	<div class="" style="margin-bottom:10px;text-align:center;">
		<button type="button" class="btn btn-info" onclick="salerep()">Sale Report</button>
		<button type="button" class="btn btn-info" onclick="saledet()">Sale Report Details</button>
		<button type="button" class="btn btn-info" onclick="itemsalerep()">Item Sale Report</button>
		<button type="button" class="btn btn-info" onclick="disreport()">Discount Report</button>
		<!--<button type="button" class="btn btn-primary" onclick="itemsaledet()">Item Sale Details</button>-->
		<button type="button" class="btn btn-danger" onclick="itemret()">Return from Patient</button>
		<!--<button type="button" class="btn btn-danger" onclick="itemret_to_store()">Return To Store Report</button>-->
	</div>
	<div class="" style="margin-bottom:10px;text-align:center;">
		<input type="button" name="button5" id="button6" class="btn btn-info" value="Credit Bills" onclick="salecredit()" style="width:120px"/>
		<input type="button" name="button5" id="button6" class="btn btn-info" value="Cost Price" onclick="salecostprice()" style="width:120px"/>
		<!--<input type="button" name="button3" id="button13" class="btn btn-info" value="Bill Edit Report" onclick="billedit()"/>-->
	    <input type="button" name="button3" id="button12" class="btn btn-info" value="User Summary" onclick="usersummary()"/> 
		<!--<input type="button" name="button3" id="button12" class="btn btn-info" value="User wise" onclick="user_wise_show(1)"/>-->
		<!--<input type="button" name="button3" id="button12" class="btn btn-info" value="New User wise" onclick="user_wise_show(2)"/>-->
		<input type="button" name="button3" id="button12" class="btn btn-danger" value="Stock Expiry Report" onclick="itemexpiry()"/> 
	</div>
	<div id="res" style="margin-left:0px;max-height:500px;overflow-y:scroll;">
		<?php include("sales_graph.php");?>
	</div>
</div>

<!--modal-->
<input type="button" data-toggle="modal" data-target="#note_mod" id="nt_btn" style="display:none"/>
<input type="text" id="modtxt" value="0" style="display:none"/>
<div class="modal fade" id="note_mod" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<!--<button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true"><b>x</b></button>-->
				<div id="results">
				
				</div>
			</div>
		</div>
	</div>
</div>
<!--modal end-->

<div id="loader" style="display:none;position:fixed;top:50%;left:50%;z-index:9999;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
.modal
{
    top: 10%;
    left: 30%;
    width: 80%;
}
.modal.fade.in
{
    top: 10%;
    left: 25%;
    width: 90%;
}
</style>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	
	//=====================================================
	function all_setup()
	{
		$("#gsetup").hide();
		$("#btn_set_up").attr("disabled",true);
		$.post("pages/sales_graph_ajax.php"	,
		{
			type:"all_setup",
		},
		function(data,status)
		{
			$("#res").html(data);
			$("#gsetup").slideDown(600);
			load_colors();
		})
	}
	function load_colors()
	{
		$.post("pages/sales_graph_ajax.php"	,
		{
			colr_type:$(".colr_type:checked").val(),
			type:"load_colors",
		},
		function(data,status)
		{
			$("#load_colors").html(data);
		})
	}
	function close_graph_data()
	{
		$("#gsetup").slideUp(500);
		$("#btn_set_up").attr("disabled",false);
	}
	function save_graph_data()
	{
		var cl=$(".colors");
		var all_clr="";
		var lvs=$("#levels").val();
		var max_val=$("#max_val").val();
		
		for(var j=0; j<(cl.length); j++)
		{
			all_clr+=cl[j].value+"@@";
		}
				
		if($("#max_val").val().trim()=="")
		{
			$("#max_val").focus();
		}
		else if(parseInt($("#max_val").val().trim())==0)
		{
			$("#max_val").focus();
		}
		else if((parseInt($("#max_val").val().trim())*0)!=0)
		{
			$("#max_val").focus();
		}
		else
		{
			$("#btn_graph").attr("disabled",true);
			$.post("pages/sales_graph_ajax.php"	,
			{
				lvs:lvs,
				max_val:max_val,
				colr_type:$(".colr_type:checked").val(),
				all_clr:all_clr,
				user:$("#user").text().trim(),
				type:"save_graph_data",
			},
			function(data,status)
			{
				alert(data);
				location.reload(true);
			})
		}
	}
	//=====================================================
	
	function salerep()
	{
		$("#loader").show();
		$.post("pages/sale_report_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:1,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function pat_bill_edit(bill)
	{
		$("#loader").show();
		$.post("pages/sale_report_ajax.php",
		{
			bill:bill,
			type:2,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#nt_btn").click();
			//alert(data);
			$("#results").html(data);
		})
	}
	function calc_disc(val)
	{
		var bill_amt=$("#bill_amt").val().trim();
		if(bill_amt=="")
		{
			bill_amt=0;
		}
		else
		{
			bill_amt=parseFloat(bill_amt);
		}
		//------------------------------------
		if(val=="")
		{
			dis_per=0;
		}
		else
		{
			dis_per=parseInt(val);
		}
		//------------------------------------
		if(dis_per<0 || dis_per>100)
		{
			$("#dis_per").css("border","1px solid #FF0000");
		}
		else
		{
			$("#dis_per").css("border","");
		}
		//------------------------------------
		var adj_amt=0;
		var pay_amt=0;
		var bal_amt=0;
		var dis_amt=0;
		dis_amt=((bill_amt*dis_per)/100);
		pay_amt=(bill_amt-dis_amt);
		//------------------------------------
		$("#dis_amt").val(dis_amt);
		$("#adj_amt").val(adj_amt);
		$("#pay_amt").val(pay_amt);
		$("#bal_amt").val(bal_amt);
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
	function calc_adj(val)
	{
		var bill_amt=$("#bill_amt").val().trim();
		if(bill_amt=="")
		{
			bill_amt=0;
		}
		else
		{
			bill_amt=parseFloat(bill_amt);
		}
		//------------------------------------
		var dis_per=$("#dis_per").val().trim();
		if(dis_per=="")
		{
			dis_per=0;
		}
		else
		{
			dis_per=parseInt(dis_per);
		}
		//------------------------------------
		if(val=="")
		{
			adj_amt=0;
		}
		else
		{
			adj_amt=parseFloat(val);
		}
		//------------------------------------
		var pay_amt=0;
		var bal_amt=0;
		var dis_amt=0;
		dis_amt=((bill_amt*dis_per)/100);
		pay_amt=(bill_amt-dis_amt);
		pay_amt=(pay_amt-adj_amt);
		$("#dis_amt").val(dis_amt);
		$("#pay_amt").val(pay_amt);
		$("#bal_amt").val(bal_amt);
	}
	function calc_paid(val)
	{
		var bill_amt=$("#bill_amt").val().trim();
		if(bill_amt=="")
		{
			bill_amt=0;
		}
		else
		{
			bill_amt=parseFloat(bill_amt);
		}
		//------------------------------------
		var dis_per=$("#dis_per").val().trim();
		if(dis_per=="")
		{
			dis_per=0;
		}
		else
		{
			dis_per=parseInt(dis_per);
		}
		//------------------------------------
		var adj_amt=$("#adj_amt").val().trim();
		if(adj_amt=="")
		{
			adj_amt=0;
		}
		else
		{
			adj_amt=parseFloat(adj_amt);
		}
		//------------------------------------
		var pay_amt=0;
		if(val=="")
		{
			pay_amt=0;
		}
		else
		{
			pay_amt=parseFloat(val);
		}
		//------------------------------------
		var bal_amt=0;
		var dis_amt=0;
		var net_pay=0;
		dis_amt=((bill_amt*dis_per)/100);
		net_pay=(bill_amt-dis_amt);
		bal_amt=(net_pay-adj_amt-pay_amt);
		$("#bal_amt").val(bal_amt);
	}
	function pat_bill_update()
	{
		if($("#pat_name").val().trim()=="")
		{
			$("#pat_name").focus();
		}
		else if($("#dis_per").val().trim()!="" && parseInt($("#dis_per").val().trim()) < 0)
		{
			$("#dis_per").focus();
		}
		else if($("#dis_per").val().trim()!="" && parseInt($("#dis_per").val().trim()) > 100)
		{
			$("#dis_per").focus();
		}
		else
		{
			$("#sav").attr("disabled",true);
			$("#loader").show();
			$.post("pages/sale_ajax.php",
			{
				bill:$("#upd_bl_no").val().trim(),
				pat_name:$("#pat_name").val().trim(),
				bl_type:$("#bl_type").val(),
				dis_per:$("#dis_per").val().trim(),
				dis_amt:$("#dis_amt").val().trim(),
				adj_amt:$("#adj_amt").val().trim(),
				pay_amt:$("#pay_amt").val().trim(),
				bal_amt:$("#bal_amt").val().trim(),
				pat_type:$("#pat_type").val().trim(),
				bill_date:$("#bill_date").val().trim(),
				type:"pat_bill_update",
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#can").click();
				alert(data);
			})
		}
	}
	function salecredit()
	{
		$.post("pages/sale_report_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:7,
		},
		function(data,status)
		{
			
			$("#res").html(data);
		})
	}
	
	function salecostprice()
	{
		$.post("pages/sale_report_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:8,
		},
		function(data,status)
		{
			
			$("#res").html(data);
		})
	}
	
	function usersummary()
	{
		$.post("pages/sale_report_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:9,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function itemexpiry()
	{
		$.post("pages/sale_report_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:10,
		},
		function(data,status)
		{
			
			$("#res").html(data);
		})
	}
	
	function billedit()
	{
		$.post("pages/ph_load_data_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"loadeditbill",
		},
		function(data,status)
		{
			
			$("#res").html(data);
		})
	}
	
	function saledet()
	{
		$.post("pages/sale_report_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:3,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function popitup(url)
			{
				var txtfrom=document.getElementById("fdate").value;
				var txtto=document.getElementById("tdate").value;
				var fid=0;
				
				url=url+"?date1="+txtfrom+"&date2="+txtto+"&fid="+fid;
				newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
			}
			
			
	function itemsalerep()
	{
		$.post("pages/sale_report_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:4,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function itemsaledet()
	{
		$.post("pages/global_load_g.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_sale_item_det",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function itemret()
	{
		$.post("pages/sale_report_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:6,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function itemret_to_store()
	{
		$.post("pages/ph_load_data_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_return_to_store_report",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function disreport()
	{
		$.post("pages/sale_report_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:5,
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function sale_rep_exp(f,t)
	{
		var url="pages/sale_rep_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	function sale_rep_prr(f,t)
	{
		url="pages/sale_rep_print.php?fdate="+f+"&tdate="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function show_user_smry()
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		url="pages/ph_user_smry_rpt.php?fdate="+fdate+"&tdate="+tdate;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function sale_rep_credit(f,t)
	{
		url="pages/ph_credit_bill_rpt.php?date1="+f+"&date2="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function sale_rep_costprice(f,t)
	{
		url="pages/cost_pricewise_rpt.php?date1="+f+"&date2="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function item_expiry_rep(f,t)
	{
		url="pages/item_expiery_rpt.php?date1="+f+"&date2="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function sale_rep_det_exp(f,t)
	{
		var url="pages/sale_rep_det_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	function sale_rep_det_prr(f,t)
	{
		url="pages/sale_rep_det_print.php?fdate="+f+"&tdate="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function sale_item_rep_exp(f,t)
	{
		var url="pages/sale_item_rep_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	function sale_item_rep_prr(f,t)
	{
		url="pages/sale_item_rep_print.php?fdate="+f+"&tdate="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function sale_item_det_exp(f,t)
	{
		var url="pages/sale_item_det_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	function sale_item_det_prr(f,t)
	{
		url="pages/sale_item_det_print.php?fdate="+f+"&tdate="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function ret_rep_exp(f,t)
	{
		var url="pages/sale_return_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	
	function ret_dis_exl_exp(f,t)
	{
		var url="pages/ph_sale_dis_rpt_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	
	function ret_rep_prr(f,t)
	{
		url="pages/sale_return_print.php?fdate="+f+"&tdate="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function ret_rep_prr_to_store(f,t)
	{
		url="pages/ph_itm_retrn_store_rpt.php?fdate="+f+"&tdate="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function ret_dis_prr(f,t)
	{
		url="pages/ph_sale_rep_dis_rpt.php?fdate="+f+"&tdate="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function edit_bill_show(f,t)
	{
		url="pages/ph_edit_rpt.php?billno="+f;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function user_wise_show()
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		url="pages/ph_cash_user_wise.php?fdate="+fdate+"&tdate="+tdate;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function user_wise_show(n)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var user=$("#user").text().trim();
		if(n==1)
		{
			url="pages/ph_cash_user_wise.php?fdate="+fdate+"&tdate="+tdate+"&user="+user;
		}
		if(n==2)
		{
			url="pages/new_ph_cash_user_wise.php?fdate="+fdate+"&tdate="+tdate+"&user="+user;
		}
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
