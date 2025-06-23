<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">User Account</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-bottom:10px;text-align:center;">
		<b>From</b>
		<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<b>To</b>
		<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<button type="button" class="btn btn-info" onclick="usr_list()">Search</button>
		<?php
		//if($p_info["levelid"]==1)
		{
		?>
		<button type="button" class="btn btn-info" onclick="usr_summary()">User Summary</button>
		<?php
		}
		?>
	</div>
	<div class="" style="margin-bottom:10px;text-align:center;">
		<span id="usr_list">
		
		</span>
	</div>
	<div class="" style="margin-bottom:10px;text-align:center;">
		<!--<button type="button" class="btn btn-info" onclick="salerep()">Sale Report</button>
		<button type="button" class="btn btn-info" onclick="saledet()">Sale Report Details</button>
		<button type="button" class="btn btn-info" onclick="itemsalerep()">Item Sale Report</button>
		<button type="button" class="btn btn-info" onclick="disreport()">Discount Report</button>
		<button type="button" class="btn btn-primary" onclick="itemsaledet()">Item Sale Details</button>
		<button type="button" class="btn btn-danger" onclick="itemret()">Item Return Report</button>-->
	</div>
	<div class="" style="margin-bottom:10px;text-align:center;">
		<!--<input type="button" name="button5" id="button6" class="btn btn-info" value="Credit Bills" onclick="salecredit()" style="width:120px"/>
		<input type="button" name="button5" id="button6" class="btn btn-info" value="Cost Price" onclick="salecostprice()" style="width:120px"/>
		<input type="button" name="button3" id="button13" class="btn btn-info" value="Bill Edit Report" onclick="billedit()"/>    
	    <input type="button" name="button3" id="button12" class="btn btn-info" value="User Summary" onclick="usersummary()"/> 
		<input type="button" name="button3" id="button12" class="btn btn-info" value="User wise" onclick="user_wise_show()"/>   
		<input type="button" name="button3" id="button12" class="btn btn-danger" value="Stock Expiry Report" onclick="itemexpiry()"/>-->
		
		
	</div>
	<!--<div id="res" style="margin-left:0px;max-height:500px;overflow-y:scroll;">
		<?php //include("sales_graph.php");?>
	</div>-->
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	function usr_list()
	{
		$.post("pages/ph_user_account_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			user:$("#user").text().trim(),
			type:"usr_list",
		},
		function(data,status)
		{
			$("#usr_list").html(data);
		})
	}
	
	function usr_summary()
	{
		$.post("pages/ph_user_account_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"usr_summary",
		},
		function(data,status)
		{
			$("#usr_list").html(data);
		})
	}
	
	function view_user_det()
	{
		$.post("pages/ph_user_account_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			all_usr:$("#all_usr").val(),
			type:"view_user_det",
		},
		function(data,status)
		{
			$("#all_det").html(data);
		})
	}
	
	function print_user_summary(f,t)
	{
		url="pages/ph_user_payment_summary.php?fdate="+f+"&tdate="+t;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function salerep()
	{
		$.post("pages/global_load_g.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_sale_report",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function salecredit()
	{
		$.post("pages/global_load_g.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_sale_credit",
		},
		function(data,status)
		{
			
			$("#res").html(data);
		})
	}
	
	function salecostprice()
	{
		$.post("pages/global_load_g.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_sale_costprice",
		},
		function(data,status)
		{
			
			$("#res").html(data);
		})
	}
	
	function usersummary()
	{
		$.post("pages/ph_load_data_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"loadusersmry",
		},
		function(data,status)
		{
			
			$("#res").html(data);
		})
	}
	
	function itemexpiry()
	{
		$.post("pages/global_load_g.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_item_expiry",
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
		$.post("pages/global_load_g.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_sale_det",
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
		$.post("pages/global_load_g.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_sale_item_rep",
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
		$.post("pages/ph_load_data_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_return_item_report",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function disreport()
	{
		$.post("pages/ph_load_data_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"load_dis_report",
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
</script>
