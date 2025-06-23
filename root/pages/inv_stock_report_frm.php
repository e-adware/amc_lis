<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Stock Report</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
			
			<tr>
				 <th>Item Name <input type="text" id="id" style="display:none;" /></th>
				<td>
					

			<input type="text" name="txtitemname"  id="txtitemname"  autocomplete="off" class="intext span3" onfocus="load_refdoc1()" onkeyup="load_refdoc(this.value,event)" onBlur="$('#ref_doc').fadeOut(500)" />
			<input type="hidden" name="r_doc" id="r_doc" />
			<input type="hidden" name="doc_id" id="doc_id" />
				<datalist id="browsrs">
				<?php
				//~ $tstid=0; 
				//~ $pid = mysqli_query($link," SELECT 	item_id,item_name FROM `item_master` where need=0 order  by `item_name` ");
				//~ while($pat1=mysqli_fetch_array($pid))
				//~ {
				   //~ echo "<option value='$pat1[item_name]-#$pat1[item_id]'>$pat1[item_name]";

				  
				//~ }
				?>
			</datalist>
					<div id="doc_info"></div>
					<div id="ref_doc" align="center" style="padding:8px;width:700px;"></div>
				</td>
			</tr>
			<tr style="display:none;">
				 <td>Item Category <input type="text" id="id" style="display:none;" /></td>
				<td>
					<select id="subcatid" autofocus>
						
						<option value="0">Select</option>
						<option value="1" selected>Main Store</option>
						<option value="2">Pharmacy</option>
						
					</select>
				</td>
			</tr>
			<?php
			$fdate=date('Y-m-d');
			$tdate=date('Y-m-d', strtotime($fdate. ' + 90 days'));
			?>
			<tr id="exp_tr" style="display:none;">
				<td colspan="2" style="text-align:center;">
					<div class="btn-group">
						<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="fdate" value="<?php echo $fdate; ?>" />
						<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="tdate" value="<?php echo $tdate; ?>" />
					</div>
				</td>
			</tr>
     </table>
     
	<div class="" style="margin-bottom:10px;text-align:center;">
		<button type="button" id="btn1" class="btn btn-info btn_disp" onclick="aval_item()">Available Item(s)</button>
		<button type="button" id="btn2" class="btn btn-info btn_disp" onclick="aval_itemwise()">Item Wise</button>
		<!--<button type="button" id="btn3" class="btn btn-info btn_disp" onclick="reorder_item()"> Re-Order Report</button>-->
		<button type="button" id="btn4" class="btn btn-info btn_disp" onclick="expiry_item()">Expiry Report</button>
		
		<!--<button type="button" id="btn5" class="btn btn-info btn_disp" onclick="shrtstk()">Sortage Item(s)</button>-->
		<!--<button type="button" id="btn6" class="btn btn-info btn_disp" onclick="expiry_item_supp()">Expiry Report</button>-->
		<button type="button" id="btn7" class="btn btn-info btn_disp" onclick="item_query()">Item Query</button>
		<!--<button type="button" id="btn8" class="btn btn-info btn_disp" onclick="dump_stock(30)">Dump Stock</button>
		<button type="button" id="btn9" class="btn btn-info btn_disp" onclick="transfer_report()">Transfer Report</button>-->
		<button type="button" id="btn10" class="btn btn-info btn_disp" onclick="transfer_details()">Item Query</button>
		<button type="button" class="btn btn-primary" onclick="stock_analysis()">Analysis</button>
		<!--<button type="button" class="btn btn-warning" onclick="expired_list()">Expired list</button>-->
		<!--<button type="button" class="btn btn-warning" onclick="ord_list()">Order list</button>-->
		<!--<button type="button" class="btn btn-warning" onclick="req_items()">Required Items</button>-->
		<input type="hidden" id="chk_val1" value="0" />
		<input type="hidden" id="chk_val2" value="0" />
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
	
	
</div>
<div id="loader" style="display:none;position:fixed;top:50%;left:50%;z-index:9999;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
	.td_box:hover
	{
		background:#DEEBFF;
	}
</style>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
		//load_test_data();
		
		$("body").on("contextmenu",function(e)
		{
			//return false;
		});
		
		$("body").bind("copy paste",function(e)
		{
			//e.preventDefault();
		});
	});
	
	function load_test_data()
	{
		$("#loader").show();
		$.post("pages/inv_stock_report_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:7,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	//------------------------------------------------------//
	function button_css(n)
	{
		$(".btn_disp").removeClass("btn-inverse");
		$(".btn_disp").addClass("btn-info");
		$("#btn"+n).removeClass("btn-info");
		$("#btn"+n).addClass("btn-inverse");
	}
	function stock_analysis()
	{
		$(".btn_disp").removeClass("btn-inverse");
		$(".btn_disp").addClass("btn-info");
		$("#exp_tr").hide();
		$("#loader").show();
		$.post("pages/inv_stock_report_ajax.php",
		{
			user:$("#user").text().trim(),
			type:8,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function expired_list()
	{
		$("#exp_tr").hide();
		$("#loader").show();
		$.post("pages/inv_stock_report_ajax.php",
		{
			type:6,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function transfer_report()
	{
		button_css(9);
		$("#btn9").attr("disabled",true);
		$("#exp_tr").show();
		$("#loader").show();
		$.post("pages/inv_stock_report_ajax.php",
		{
			type:3,
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#btn9").attr("disabled",false);
			$("#res").html(data);
		})
	}
	function load_list(pno,ptyp)
	{
		var url="";
		if(ptyp=="1")
		{
			url="pages/transfer_details_print.php?iNo="+btoa(pno);
		}
		if(ptyp=="2")
		{
			url="pages/inv_supplier_ldger_rpt_new.php?rCv="+btoa(pno);
		}
		if(ptyp=="3")
		{
			url="pages/ph_challan_receive_print.php?oRd="+btoa(pno);
		}
		if(url!="")
		{
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		}
	}
	function transfer_details()
	{
		if($("#txtitemname").val().trim()=="")
		{
			$("#txtitemname").focus();
		}
		else
		{
			button_css(10);
			$("#btn10").attr("disabled",true);
			$("#exp_tr").show();
			$("#loader").show();
			$.post("pages/inv_stock_report_ajax.php",
			{
				type:5,
				itm:$("#doc_id").val().trim(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#btn10").attr("disabled",false);
				$("#res").html(data);
			})
		}
	}
	function dump_stock(d)
	{
		button_css(8);
		$("#btn8").attr("disabled",true);
		$("#exp_tr").hide();
		$("#loader").show();
		$.post("pages/inv_stock_report_ajax.php",
		{
			type:2,
			days:d,
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#btn8").attr("disabled",false);
			$("#res").html(data);
		})
	}
	function ord_list()
	{
		var url="pages/inv_ord_list.php";;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function req_items()
	{
		$("#loader").show();
		$.post("pages/inv_cntrl_stk_ajax.php",
		{
			type:"item_require_report",
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function remove_list(ths,sl)
	{
		bootbox.confirm("Do you really want to remove this required item?",
		function(result)
		{ 
			if(result)
			{
				$("#loader").show();
				$.post("pages/inv_cntrl_stk_ajax.php",
				{
					sl:sl,
					type:"remove_required_list",
				},
				function(data,status)
				{
					$("#loader").hide();
					//alert(data);
					$(ths).parent().parent().remove();
					var tr=$(".req_tr");
					for(var j=0; j<(tr.length); j++)
					{
						$(".req_tr:eq("+j+")").find('td:eq(0)').text(j+1);
					}
				})
			}
		});
	}
	function require_item_print()
	{
		url="pages/inv_require_item_print.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function item_query()
	{
		if($("#txtitemname").val().trim()=="")
		{
			$("#txtitemname").focus();
		}
		else
		{
			button_css(7);
			$("#btn7").attr("disabled",true);
			$("#exp_tr").show();
			$("#loader").show();
			$.post("pages/inv_stock_report_ajax.php",
			{
				type:1,
				itm:$("#doc_id").val().trim(),
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#btn7").attr("disabled",false);
				$("#res").html(data);
			})
		}
	}
	function expiry_item_supp()
	{
		$("#btn6").attr("disabled",true);
		$("#exp_tr").show();
		$("#loader").show();
		button_css(6);
		$.post("pages/inv_load_data_ajax.php",
		{
			type:"load_item_expiry_supplier",
			catid:$("#subcatid").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#btn6").attr("disabled",false);
			$("#res").html(data);
		})
	}
	function item_return(supp)
	{
		var all="";
		var rets=$(".rets"+supp);
		for(var i=0; i<(rets.length); i++)
		{
			all+=$(".rets"+supp+":eq("+i+")").find('td:eq(1)').text().trim()+"#@@#"; // item
			all+=$(".rets"+supp+":eq("+i+")").find('td:eq(5)').text().trim()+"#%%#"; // batch
		}
		//alert(all);
		window.location="index.php?param="+btoa(167)+"&sUpP="+btoa(supp);
	}
	function aval_item()
	{
		var jj=1;
		$("#doc_id").val('');
		$("#r_doc").val('');
		$("#txtitemname").val('');
		if($("#txtitemname").val().trim()!="")
		{
			alert("Please De-select the Item Name..");
			$("#txtitemname").focus();
			jj=0;
		}
		
		if(jj==1)
		{
			button_css(1);
			$("#btn1").attr("disabled",true);
			$("#exp_tr").hide();
			$("#loader").show();
			$.post("pages/inv_load_data_ajax.php",
			{
				type:"invmainstkavailrpt",
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#btn1").attr("disabled",false);
				$("#res").html(data);
			})
		 }
	}
	
	
	function aval_itemwise()
	{
		var jj=1;
		
		if($("#txtitemname").val().trim()=="")
		{
			//alert("Please select a Item Name..");
			$("#txtitemname").focus();
			jj=0;
		}
		
		if(jj==1)
		{
			button_css(2);
			$("#btn2").attr("disabled",true);
			$("#exp_tr").hide();
			$("#loader").show();
			$.post("pages/inv_load_data_ajax.php",
			{
				type:"invmainstkavailrpt_itemwise",
				itemid:$("#doc_id").val().trim(),
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#btn2").attr("disabled",false);
				$("#res").html(data);
			})
		}	
	}
	
	
		function reorder_item()
		{
		    var jj=1;
			button_css(3);
         $("#loader").show();
			$("#btn3").attr("disabled",true);
			$.post("pages/inv_load_data_ajax.php"	,
			{
				type:"itemreorder",
				catid:$("#subcatid").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#btn3").attr("disabled",false);
				$("#res").html(data);
			})

		}
  
	function expiry_item()
	{
		button_css(4);
		$("#exp_tr").show();
		$("#loader").show();
		$("#btn4").attr("disabled",true);
		$.post("pages/inv_load_data_ajax.php"	,
		{
			type:"load_item_expiry",
			catid:$("#subcatid").val(),
			fdate:$("#fdate").val().trim(),
			tdate:$("#tdate").val().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#btn4").attr("disabled",false);
			$("#res").html(data);
		})
	}
	
	function shrtstk()
	{
		button_css(5);
		$("#btn5").attr("disabled",true);
		$("#exp_tr").hide();
		$("#loader").show();
		$.post("pages/global_load_g.php"	,
		{
			type:"item_short_report",
		},
		function(data,status)
		{
			$("#btn5").attr("disabled",false);
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function transfer_details_print(ino)
	{
		url="pages/transfer_details_print.php?iNo="+btoa(ino);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function transfer_report_print(fdt,tdt)
	{
		url="pages/transfer_report_print.php?fDt="+btoa(fdt)+"&tDt="+btoa(tdt);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_dump_stock(days)
	{
		url="pages/inv_print_dump_stock.php?dY="+btoa(days);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function stk_exp()
	{
		catid=$("#subcatid").val();
		var url="pages/inv_stock_rpt_xl.php?catid="+catid;
		document.location=url;
	}
	
	function stk_reorder_exp()
	{
		catid=$("#subcatid").val();
		var url="pages/inv_stk_reorder_xl_rpt.php?catid="+catid;
		document.location=url;
	}
	
	
	function stk_prr()
	{
		catid=$("#subcatid").val();
		url="pages/inv_stock_rpt.php?catid="+catid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function item_expiry_rep()
	{
		catid=$("#subcatid").val();
		url="pages/inv_stk_expiry_rpt.php?catid="+catid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function stk_prr_reorder()
	{
		catid=$("#subcatid").val();
		url="pages/inv_stk_reorder_rpt.php?catid="+catid;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function shr_exp()
	{
		var url="pages/stock_report_short_xls.php";
		document.location=url;
	}
	function shr_prr()
	{
		url="pages/stock_report_short_print.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_item_expiry_supplier(fdt,tdt)
	{
		url="pages/print_item_expiry_supplier.php?fD="+btoa(fdt)+"&tD="+btoa(tdt);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function stock_analysis_print()
	{
		url="pages/stock_analysis_print.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	//------------------------item search---------------------------------//
	function load_refdoc1()
	{
			//$("#ref_doc").fadeIn(200);
			//$("#r_doc").select();
			setTimeout(function(){ $("#chk_val2").val(1)},200);
	}
	var doc_tr=1;
	var doc_sc=0;
	function load_refdoc(val,e)
	{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			//alert(unicode);
			if(unicode!=13)
			{
				if(unicode!=40 && unicode!=38)
				{
					if(unicode==27)
					{
						//$("#uhid").focus();
						$("#bill_typ").focus();
					}
					else if(e.ctrlKey && unicode==32)
					{
						if($('#mytable tr:eq(1) td:eq(3)').find('.qnt').length>0)
						{
							$('#mytable tr:eq(1) td:eq(3)').find('.qnt').focus().select();
						}
					}
					else
					{
						$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
						$("#ref_doc").fadeIn(200);
						$.post("pages/ph_stock_transfer_ajax.php",
						{
							type:9,
							val:val,
						},
						function(data,status)
						{
							$("#ref_doc").html(data);	
							doc_tr=1;
							doc_sc=0;
						})
					}
				}
				else if(unicode==40)
				{
					var chk=doc_tr+1;
					var cc=document.getElementById("doc"+chk).innerHTML;
					if(cc)
					{
						doc_tr=doc_tr+1;
						$("#doc"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','font-size':'15px','transition':'all .2s'});
						var doc_tr1=doc_tr-1;
						$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'13px','transition':'all .2s'});
						var doc_tr2=doc_tr1-1;
						//$("#doc"+doc_tr2).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'11px','transition':'all .2s'});
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
						$("#doc"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','font-size':'15px','transition':'all .2s'});
						var doc_tr1=doc_tr+1;
						$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'13px','transition':'all .2s'});
						var doc_tr2=doc_tr1+1;
						//$("#doc"+doc_tr2).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'11px','transition':'all .2s'});
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
				var cen_chk1=document.getElementById("chk_val2").value;
				if(cen_chk1!=0)
				{
					var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
					var doc_naam=docs[2].trim()
					$("#doc_info").fadeIn(200);
					doc_load(docs[1],doc_naam);
				}
			}
	}
	function doc_load(id,name)
	{
		$("#txtitemname").val(name);
		$("#r_doc").val(name);
		$("#doc_id").val(id);
		//$("#doc_info").html("");
		$("#ref_doc").fadeOut(200);
		//~ $("#hguide").val('');
		//~ $("#hguide_id").val('');
		//~ $("#bch_qnt").val('');
		//~ $("#bch_mrp").val('');
		//~ $("#bch_gst").val('');
		//~ $("#stock").val('');
		//~ $("#bch_exp").val('');
		//~ $("#qnt").val('');
		$("#btn2").focus();
		doc_tr=1;
		doc_sc=0;
	}
	//------------------------item search end---------------------------------//
</script>
