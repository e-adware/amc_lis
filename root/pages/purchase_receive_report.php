<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="">
		<table class="table table-condensed">
			<tr>
				<th>Select Supplier</th>
				<td>
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
						<input type="text" id="fdate" value="<?php echo date("Y-m-d"); ?>" />
						<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
						<input type="text" id="tdate" value="<?php echo date("Y-m-d"); ?>" />
					</div>
				</td>
			</tr>
			<tr>
				<th>Item Name</th>
				<td>
					<input list="browsrs" type="text" id="txtcntrname" class="intext span4" onfocus="load_refdoc1()" onkeyup="load_refdoc(this.value,event)" onBlur="$('#ref_doc').fadeOut(500)" placeholder="Item Name" />
					<input type="hidden" name="r_doc" id="r_doc" />
					<input type="hidden" name="doc_id" id="doc_id" />
					<datalist id="browsrs">
						
					</datalist>
					<div id="doc_info"></div>
					<div id="ref_doc" align="center" style="padding:8px;width:600px;"></div>
				</td>
				<td>
					<b>Bill No</b>
					<input list="browsrs1" type="text" name="txtbillno"  id="txtbillno"  autocomplete="off" class="intext span4" placeholder="Bill No" />
					<datalist id="browsrs1">
					<?php
					$tstid=0;
					$qbill = mysqli_query($link," SELECT DISTINCT `bill_no` FROM `ph_purchase_receipt_master` WHERE `supp_code`!='0' order by `slno` DESC");
					while($qbill1=mysqli_fetch_array($qbill))
					{
						echo "<option value='$qbill1[bill_no]'>";
					}
					?>
					</datalist>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:center">
					<button type="button" class="btn btn-primary" onclick="srch()">Received Report</button>
					<button type="button" class="btn btn-primary" onclick="srch_received_gst()">Received GST</button>
					<button type="button" class="btn btn-primary" id="btn2" onclick="item_srch()">Purchase Item Search</button>
					<button type="button" class="btn btn-primary" onclick="return_srch()">Return Report</button>
					<!--<button type="button" class="btn btn-primary" onclick="payment_srch()">Supplier Transaction</button>-->
					<button type="button" class="btn btn-primary" id="btn3" onclick="ret_item_srch()">Return Item Search</button>
					<!--<button type="button" class="btn btn-primary" onclick="stock_entry_report()">Stock Entry Report</button>-->
				</td>
			</tr>
		</table>
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
	<input type="hidden" id="chk_val1" value="0" />
	<input type="hidden" id="chk_val2" value="0" />
</div>
<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd', maxDate:'0'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd', maxDate:'0'});
	});
	
	function stock_entry_report()
	{
		$("#loader").show();
		$.post("pages/ph_purchase_receive_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:9,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function srch()
	{
		$("#loader").show();
		$.post("pages/ph_purchase_receive_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			spplrid:$("#supplier").val(),
			user:$("#user").text().trim(),
			type:8,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function srch_received_gst()
	{
		$("#loader").show();
		$.post("pages/ph_purchase_receive_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			supp:$("#supplier").val(),
			user:$("#user").text().trim(),
			ph:1,
			type:10,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function return_srch()
	{
		$("#loader").show();
		$.post("pages/ph_purchase_receive_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			spplrid:$("#supplier").val(),
			type:5,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function ret_item_srch()
	{
		if($("#doc_id").val().trim()=="")
		{
			$("#txtcntrname").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/ph_purchase_receive_ajax.php",
			{
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				supp:$("#supplier").val(),
				itmid:$("#doc_id").val().trim(),
				ph:1,
				type:12,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			});
		}
	}
	function item_srch()
	{
		if($("#doc_id").val().trim()=="")
		{
			$("#txtcntrname").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/ph_purchase_receive_ajax.php",
			{
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				supp:$("#supplier").val(),
				itm:$("#doc_id").val().trim(),
				ph:1,
				type:11,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
	}
	function payment_srch()
	{
		if($("#supplier").val()=="0")
		{
			$("#supplier").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/inv_ajax.php"	,
			{
				fdate:$("#fdate").val(),
				tdate:$("#tdate").val(),
				supp:$("#supplier").val(),
				type:45,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
	}
	function rcv_item_edit(rcv)
	{
		bootbox.dialog({ message: "<b>Please wait while redirecting...</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		setTimeout(function(){
		window.location="index.php?param="+btoa(173)+"&ipd="+rcv;
		},500);
	}
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	function rcv_rep_prr(ord)
	{
		url="pages/purchase_receive_rep_print.php?rCv="+ord;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function ret_rep_prr(ord)
	{
		url="pages/ph_supp_item_return_print.php?orderno="+ord;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_supp_bal(supp,fdate,tdate)
	{
		url="pages/inv_supplier_balance_rpt.php?fdate="+btoa(fdate)+"&tdate="+btoa(tdate)+"&supp="+btoa(supp);
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
				else
				{
					$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
					$("#ref_doc").fadeIn(200);
					$.post("pages/sale_ajax.php",
					{
						type:"item",
						val:val,
						ph:1,
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
		$("#txtcntrname").val(name);
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
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
