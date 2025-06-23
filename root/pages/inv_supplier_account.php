<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Supplier Ledger</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="">
		<table class="table table-condensed">
			<tr>
				<td>
					<b>Select Supplier</b>
					<select id="supplier" class="span4" autofocus>
						<option value="0">Select All</option>
						<?php
						$qq=mysqli_query($link,"SELECT id, `name` FROM `inv_supplier_master` order by `name`");
						while($r=mysqli_fetch_array($qq))
						{
						?>
						<option value="<?php echo $r['id']; ?>"><?php echo $r['name']; ?></option>
						<?php
						}
						?>
					</select>
				</td>
				<td style="text-align:center">
					<div class="btn-group">
						<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
						<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<b>Item Name</b>
					<input list="browsrs" type="text" name="txtcntrname"  id="txtcntrname"  autocomplete="off" class="intext span4" placeholder="Item Name" />
					<datalist id="browsrs">
					<?php
					$tstid=0; 
					$pid = mysqli_query($link," SELECT 	item_id,item_name FROM `item_master` order by `item_name` ");
					while($pat1=mysqli_fetch_array($pid))
					{
						echo "<option value='$pat1[item_name]-#$pat1[item_id]'>$pat1[item_name]";
					}
					?>
					</datalist>
				</td>
				<td>
					<b>Bill No</b>
					<input list="browsrs1" type="text" name="txtbillno"  id="txtbillno"  autocomplete="off" class="intext span4" placeholder="Bill No" />
					<datalist id="browsrs1">
					<?php
					$tstid=0; 
					$qbill = mysqli_query($link," SELECT 	slno,bill_no FROM `inv_main_stock_received_master` order by `slno` ");
					while($qbill1=mysqli_fetch_array($qbill))
					{
						echo "<option value='$qbill1[bill_no]'>";
					}
					?>
					</datalist>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<button type="button" class="btn btn-info" onclick="srch()"><b class="icon-search"></b> Bill Details</button>
					<button type="button" class="btn btn-info" onclick="srch_itemwise()"><b class="icon-search"></b> Item Search</button>
					<button type="button" class="btn btn-info" onclick="srch_return()"><b class="icon-search"></b>Item Return</button>
					<!--<button type="button" class="btn btn-info" onclick="srch_payment()"><b class="icon-search"></b>Payment Details</button>-->
					<!--<button type="button" class="btn btn-info" onclick="srch_Bill_cancel()"><b class="icon-search"></b>Cancel Bill</button>-->
					<button type="button" class="btn btn-info" onclick="srch_received_gst()"><b class="icon-search"></b>Received Gst</button>
					<button type="button" class="btn btn-info" onclick="srch_return_gst()"><b class="icon-search"></b>Return Gst</button>
					<button type="button" class="btn btn-primary btn_lite" onclick="supp_transaction()">Supplier Transaction</button>
					<button type="button" class="btn btn-success btn_lite" onclick="view_supp_wise()">Supplier wise</button>
				</td>
			</tr>
		</table>
	</div>
	
	<div style="max-height:400px;overflow-y:scroll;">
		<div id="res">
			
		</div>
	</div>
</div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
<script src="../jss/highcharts.js"></script>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd', maxDate: '0'});
	});
	function supp_transaction()
	{
		if($("#supplier").val()=="0")
		{
			$("#supplier").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/inv_supplier_account_ajax.php",
			{
				supp:$("#supplier").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				type:3,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
	}
	function srch()
	{
		var jj=1;
		if($("#txtcntrname").val()!="")
		{
			alert("Please de-select the item");
			$("#txtcntrname").focus();
			jj=0;
			
		}
		
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/inv_supplier_account_ajax.php",
			{
				splrid:$("#supplier").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				billno:$("#txtbillno").val(),
				type:1,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
		
	}
	
	function srch_itemwise()
	{
		var jj=1;
		if($("#supplier").val()!=0)
		{
			alert("Please select All Option");
			$("#supplier").focus();
			jj=0;
			
		}
		
		if($("#txtcntrname").val()=="")
		{
			alert("Please select a item");
			$("#txtcntrname").focus();
			jj=0;
			
		}
		
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/inv_load_data_ajax.php"	,
			{
				itmid:$("#txtcntrname").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				type:"load_rcv_itm_wise",
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
		
	}
	
	function srch_return()
	{
		var jj=1;
		if($("#txtcntrname").val()!="")
		{
			alert("Please de-select the item");
			$("#txtcntrname").focus();
			jj=0;
			
		}
		
		if($("#txtbillno").val()!="")
		{
			alert("Please de-select the Bill No");
			$("#txtbillno").focus();
			jj=0;
			
		}
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/inv_supplier_account_ajax.php",
			{
				splrid:$("#supplier").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				type:4,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
		
	}
	
	function srch_payment()
	{
		var jj=1;
		if($("#txtcntrname").val()!="")
		{
			alert("Please de-select the item");
			$("#txtcntrname").focus();
			jj=0;
			
		}
		
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/inv_load_data_ajax.php"	,
			{
				splrid:$("#supplier").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				billno:$("#txtbillno").val(),
				type:"load_spplr_payment",
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
		
	}
	
	function srch_return_gst()
	{
		var jj=1;
		if($("#txtcntrname").val()!="")
		{
			alert("Please de-select the item");
			$("#txtcntrname").focus();
			jj=0;
			
		}
		
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/inv_supplier_account_ajax.php"	,
			{
				splrid:$("#supplier").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				billno:$("#txtbillno").val(),
				type:6,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
		
	}
	
	function srch_received_gst()
	{
		var jj=1;
		if($("#supplier").val()!=0)
		{
			alert("Please Select All option..");
			$("#supplier").focus();
			jj=0;
			
		}
		
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/inv_supplier_account_ajax.php"	,
			{
				splrid:$("#supplier").val(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				type:5,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
		
	}
	

function srch_Bill_cancel()
{
var jj=1;
if($("#txtcntrname").val()!="")
{
	alert("Please de-select the item");
	$("#txtcntrname").focus();
	jj=0;
	
}

if(jj==1)
{
	$("#loader").show();
	$.post("pages/inv_load_data_ajax.php"	,
	{
		splrid:$("#supplier").val(),
		fdate:$("#fdate").val(),
		tdate:$("#tdate").val(),
		
		type:"load_bill_cancel",
	},
	function(data,status)
	{
		$("#loader").hide();
		$("#res").html(data);
	})
}

}
	
	function ph_rcv_print(rcv)
	{
		var url="pages/purchase_receive_rep_print.php?rCv="+rcv;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function report_xls(g,f,t)
	{
		var url="pages/gst_rep_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	
	function bill_report_xls(b,s,rcptdate)
	{
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		var url="pages/inv_supplier_ldger_rpt_excel.php?rcptdate="+rcptdate+"&tdate="+tdate+"&splirid="+s+"&billno="+b;
		document.location=url;
	}
	
	function pay_detail_print()
	{
			
		splirid=$("#supplier").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		billno=$("#txtbillno").val();
		url="pages/inv_supplier_payment_rpt.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splirid+"&billno="+billno;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function supplier_rcvd_gst_print(supp,fdt,tdt)
	{
		url="pages/inv_supplr_rcvd_gst_rpt.php?fdate="+fdt+"&tdate="+tdt+"&splirid="+supp;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function supplier_return_gst_print()
	{
			
		
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		splirid=$("#supplier").val();
		
		url="pages/inv_supplr_return_gst_rpt.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splirid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	
	
	function report_print_return(g,splr)
	{
		
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		url="pages/inv_item_rtn_to_splr_rpt.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splr+"&billno="+g;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function report_print_billwise(ord,rcv)
	{
		url="pages/inv_supplier_ldger_rpt.php?oRd="+btoa(ord)+"&rCv="+btoa(rcv);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function srch_cancel_bill_print()
	{
			
		splirid=$("#supplier").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		billno=0;
		url="pages/inv_supplier_bill_cancel_rpt.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splirid+"&billno="+billno;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_supp_bal(supp,fdate,tdate)
	{
		url="pages/inv_supplier_balance_rpt.php?fdate="+btoa(fdate)+"&tdate="+btoa(tdate)+"&supp="+btoa(supp);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function supplier_summery_print()
	{
			
		splirid=$("#supplier").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		bltype=$("#bill_type").val();
		url="pages/inv_supplier_smry_rpt.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splirid+"&bltype="+bltype;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function supplier_summery_print_excel()
	{
			
		splirid=$("#supplier").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		bltype=$("#bill_type").val();
		url="pages/inv_supplier_smry_rpt_excel.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splirid+"&bltype="+bltype;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function view_supp_wise()
	{
		$("#loader").show();
		$.post("pages/inv_supplier_account_ajax.php",
		{
			supp:$("#supplier").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:2,
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			var val=data.split("@govinda@");
			var txt=val[0];
			var data=JSON.parse(val[1]);
			//----------------------------
			var array = [];
			for (var i=0;i<data.length;i++)
			{
				var obj = data[i];
				array[i] = new Array();
				for(var key in obj)
				{
					array[i].push(obj[key]);
				}
			}
			//-----------------------------
			plot_graph_supp_wise(txt,array);
		})
	}
	function plot_graph_supp_wise(txt,data)
	{
        // Create the chart
        chart = new Highcharts.Chart(
        {
            chart:
            {
                renderTo: 'res',
                type: 'pie'
            },
            title:
            {
                //text: 'Supplier wise from 2019 to 2020'
                text:txt
            },
            yAxis:
            {
                title:
                {
                    //text: 'Total percent market share'
                }
            },
            legend:
			{
				reversed: false
			},
            plotOptions:
            {
                pie:
                {
					showInLegend: true,
                    shadow:false,
				},
				series:
				{
					point:
					{
						events:
						{
							legendItemClick:function()
							{
								return false; // <== returning false will cancel the default action
							}
						}
					}
				}
            },
            tooltip:
            {
                formatter: function()
                {
                    return '<b>'+ this.point.name +'</b>: '+ this.y.toFixed(2);
                }
            },
            series: [{
                name: 'Browsers',
                //data: [["Firefox",6],["MSIE",4],["Chrome",7]],
                data: data,
                size: '100%',
                innerSize: '60%',
                dataLabels:
                {
                    enabled: true
                }
            }]
        });
        $(".highcharts-credits").hide();
	}
</script>
