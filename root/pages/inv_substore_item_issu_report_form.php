<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Substore Item Issue Report </span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" style="">
			<tr>
				<td colspan="2" style="text-align:center;">
					<div class="btn-group">
						<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="fdate" style="width:100px;" value="<?php echo date("Y-m-d"); ?>" >
						<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="tdate" style="width:100px;" value="<?php echo date("Y-m-d"); ?>" >
					</div>
				</td>
			</tr>
			<tr>
				 <td>
					 <b>Item Name</b> <input type="text" id="id" style="display:none;" /><br/>
					 <input list="browsrs" type="text" name="txtitemname"  id="txtitemname"  autocomplete="off" class="intext span5" placeholder="Item Name" />
						<datalist id="browsrs">
						<?php
						$tstid=0; 
						$pid = mysqli_query($link," SELECT 	item_id,item_name FROM `item_master` order  by `item_name` ");
						while($pat1=mysqli_fetch_array($pid))
						{
						   echo "<option value='$pat1[item_name]-@$pat1[item_id]'>$pat1[item_name]";

						  
						}
						?>
					</datalist>
				 </td>
				<td>
					<b>Select Substore</b><br/>
					<select id="slectsplr" class="span3">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT substore_id,substore_name FROM inv_sub_store order by substore_name");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['substore_id'];?>"><?php echo $r['substore_name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="button" class="btn btn-success" onclick="srch()">Search</button>
					<!--<button type="button" class="btn btn-success" onclick="srch_itemwise()">Item wise</button>-->
					<button type="button" class="btn btn-success btn_lite" onclick="srch_dept_wise()">Dept wise</button>
				</td>
			</tr>
		</table>
	</div>
	<div class="highcharts-figure">
		<div style="max-height:400px;overflow-y:scroll;">
			<div id="res">
			
			</div>
		</div>
	</div>
</div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script src="../jss/highcharts.js"></script>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	//------------------------------------------------------------------
	function srch_dept_wise()
	{
		$("#loader").show();
		$.post("pages/inv_main_str_itm_issue_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			sbstrid:$("#slectsplr").val(),
			itmid:$("#txtitemname").val(),
			type:6,
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			//$("#res").html(data);
			var data=JSON.parse(data);
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
			var dept=[];
			var amount=[];
			var gst=[];
			for (var i=0;i<array.length;i++)
			{
				//alert(array[i]);
				dept.push(array[i][0]);
				amount.push(parseFloat(array[i][1]));
				gst.push(parseFloat(array[i][2]));
			}
			//---------------------------------
			//alert(dept);
			//~ alert(amount);
			//~ alert(gst);
			plot_data(dept,amount,gst);
		})
	}
	function plot_data(dept,amount,gst)
	{
		Highcharts.chart('res',
		{
			chart:
			{
				type: 'column'
			},
			exporting:
			{
				enabled: false
			},
			title:
			{
				text: 'Monthly Issue Report'
			},
			xAxis:
			{
				//categories: ['A', 'B', 'C', 'D', 'E', 'F']
				categories: dept
			},
			yAxis:
			{
				min: 0,
				//max: mx,
				title:
				{
					text: 'Total revenue consumption'
				}
			},
			tooltip:
			{
				pointFormat: '<span style="color:{series.color}">{series.name}</span>: Rs <b>{point.y:.2f}</b><br/>',
				shared: true
			},
			legend:
			{
				reversed: true
			},
			plotOptions:
			{
				series:
				{
					stacking: 'normal',
					events:
					{
						legendItemClick: function()
						{
							return false;
						}
					}
				}
			},
			series:
			[{
				name: 'Amount',
				//data: [50, 30, 40, 70, 20.3, 62],
				data: amount,
				color:'#266E39'
			},
			{
				name: 'GST',
				//data: [20, 20, 30, 20, 10.95, 12],
				data: gst,
				color:'#215483'
			}]
		});
	}
	//------------------------------------------------------------------
	function srch()
	{
		
		var jj=1;
		
		if($("#txtitemname").val() !="")
		{
			if($("#slectsplr").val()!=0)
			{
				alert("Please De-select the Item Name ..");
				$("#txtitemname").focus();
				jj=0;
			}
		}
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/inv_main_str_itm_issue_ajax.php",
			{
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				sbstrid:$("#slectsplr").val(),
				itmid:$("#txtitemname").val(),
				type:5,
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
		
		if($("#txtitemname").val()=="")
		{
			
				alert("Please select a item Name");
				$("#txtitemname").focus();
				jj=0;
			
		}
		if(jj==1)
		{
			$("#loader").show();
			$.post("pages/inv_load_data_ajax.php"	,
			{
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				sbstrid:$("#slectsplr").val(),
				itmid:$("#txtitemname").val(),
				type:"load_sbtr_itmissue_itemwise",
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
			
		}
		
	}
	
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	
	function issue_print(ord)
	{
		url="pages/inv_frm_sbstr_itm_issue_rpt.php?orderno="+btoa(ord);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function issue_print_excel(ord)
	{
		url="pages/inv_frm_sbstr_item_issue_rpt_excel.php?orderno="+btoa(ord);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function dept_issue_summery_print()
	{
			
		sbstrid=$("#slectsplr").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		url="pages/inv_deptwise_all_item_issue_rpt.php?fdate="+fdate+"&tdate="+tdate+"&sbstrid="+sbstrid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function dept_issue_summery_print_excel()
	{
			
		sbstrid=$("#slectsplr").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		url="pages/inv_deptwise_all_item_issue_rpt_excel.php?fdate="+fdate+"&tdate="+tdate+"&sbstrid="+sbstrid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function issue_summery_print()
	{
			
		sbstrid=$("#slectsplr").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		itmid=$("#txtitemname").val();
		url="pages/inv_item_issue_rpt.php?fdate="+fdate+"&tdate="+tdate+"&sbstrid="+sbstrid+"&itmid="+itmid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function issue_summery_print_excel()
	{
			
		sbstrid=$("#slectsplr").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		itmid=$("#txtitemname").val();
		url="pages/inv_item_issue_rpt_excel.php?fdate="+fdate+"&tdate="+tdate+"&sbstrid="+sbstrid+"&itmid="+itmid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
</script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
<style>
.highcharts-figure, .highcharts-data-table table {
    width: 100%;
    margin: 10px auto;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
.highcharts-credits
{
	display:none !important;
}
</style>
