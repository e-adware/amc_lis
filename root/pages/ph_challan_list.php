<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Challan List</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="text-align:center;">
		<select id="supplier" class="span4" autofocus>
			<option value="0">Select All Supplier</option>
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
		<input type="text" id="challan_no" list="browsrs" placeholder="Challan No" />
		<datalist id="browsrs">
		<?php
		$q=mysqli_query($link,"SELECT `challan_no` FROM `ph_challan_receipt_master` WHERE `stat`='0'");
		while($r=mysqli_fetch_assoc($q))
		{
			echo "<option value='$r[challan_no]'>$r[challan_no]";
		}
		?>
		</datalist>
		<div class="btn-group">
			<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
			<input type="text" id="fdate" value="<?php echo date("Y-m-d"); ?>" style="width:100px;" />
			<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
			<input type="text" id="tdate" value="<?php echo date("Y-m-d"); ?>" style="width:100px;" />
		</div><br/>
		<div class="btn-group">
			<input type="text" id="r_doc" class="span5" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="" placeholder="Item Name" style="margin:0px;" />
			<button type="button" class="btn btn-info" id="srch" onclick="item_search()"><i class="icon-search"></i></button>
		</div>
		<input type="text" id="doc_id" style="display:none;" />
		<div id="ref_doc" style="padding:8px;width:600px;z-index:99999;"></div>
		<br/>
		<button type="button" class="btn btn-info" onclick="srch()">Search</button>
		<!--<div class="checker" id=""><span class=""><input type="checkbox" class="" style="opacity: 0;"></span></div>-->
	</div>
	<input type="text" id="chk_val2" style="display:none;" value="0" />
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
	<!--modal-->
	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="myModal" role="dialog" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<!--<button type="button" class="btn btn-mini btn-danger" style="float:right;" data-dismiss="modal" aria-hidden="true"><b>Close</b></button>-->
					<div id="results">
					
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--modal end-->
<div id="loader" style="display:none;top:50%;position:fixed;z-index:99999;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<link rel="stylesheet" href="../css/uniform.css" />
<script src="../js/jquery.uniform.js"></script>

<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<script src="../js/jquery.peity.min.js"></script>

<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	
	function add_challan_items(ord)
	{
		//alert(ord);
		window.location="index.php?param="+btoa(189)+"&oRd="+ord;
	}
	//---------------------------------------------------------------------
	function calc_amount(ths,i)
	{
		
		var amt=0;
		var d_amt=0;
		var g_amt=0;
		
		var qnt=$("#qnt"+i).val();
		var gst=$("#gst"+i).val();
		var pack=$("#pack"+i).val();
		qnt=parseFloat(qnt);
		gst=parseFloat(gst);
		pack=parseFloat(pack);
		var cost=$("#cost"+i).val().trim();
		if(cost=="")
		{
			cost=0;
		}
		else
		{
			cost=parseFloat(cost);
		}
		var dis_per=$("#dis_per"+i).val().trim();
		if(dis_per=="")
		{
			dis_per=0;
		}
		else
		{
			dis_per=parseFloat(dis_per);
		}
		cost=(cost/pack);
		amt=(qnt*cost);
		d_amt=((amt*dis_per)/100);
		amt=(amt-d_amt);
		g_amt=((amt*gst)/100);
		
		$("#cost_amt"+i).val(amt);
		$("#amt"+i).html(amt);
		$("#gst_amt"+i).val(g_amt);
		$("#dis_amt"+i).val(d_amt);
		calc_net();
	}
	function calc_net()
	{
		tot=0;
		dis=0;
		gst=0;
		var tr=$(".all_tr");
		for(var i=0; i<tr.length; i++)
		{
			tot+=parseFloat($(".all_tr:eq("+i+")").find('td:eq(7) input:last').val());
			dis+=parseFloat($(".all_tr:eq("+i+")").find('td:eq(8) input:last').val());
			gst+=parseFloat($(".all_tr:eq("+i+")").find('td:eq(6) input:first').val());
		}
		var credit=$("#credit").val().trim();
		if(credit=="")
		{
			credit=0;
		}
		else
		{
			credit=parseFloat(credit);
		}
		var tcs=$("#tcs").val().trim();
		if(tcs=="")
		{
			tcs=0;
		}
		else
		{
			tcs=parseFloat(tcs);
		}
		var adjust=$("#adjust").val().trim();
		if(adjust=="")
		{
			adjust=0;
		}
		else
		{
			adjust=parseFloat(adjust);
		}
		$("#all_tot").html(tot.toFixed(2));
		$("#all_dis").html(dis.toFixed(2));
		$("#all_gst").html(gst.toFixed(2));
		//$("#all_net").html((tot+gst).toFixed(2));
		$("#all_net").html((tot+gst-credit+tcs-adjust).toFixed(2));
	}
	function chk_dec(val,id)
	{
		var reg = /^\d+(?:\.\d{1,2})?$/;
		if(!reg.test(val))
		{
			$("#"+id).css("border","1px solid #FF0000");
			$("#"+id).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			$("#"+id).addClass("err");
		}
		else
		{
			$("#"+id).css("border","");
			$("#"+id).css("box-shadow","");
			$("#"+id).removeClass("err");
		}
	}
	function exp_dt(id,vl)
	{
		var year1=parseInt($("#years").val().trim());
		var year2=(year1+10);
		var v=vl.split("-");
		if((vl.trim())=="" || (vl).length!=7 || parseInt(v[0])<year1 || parseInt(v[0])>year2 || parseInt(v[1])>12 || parseInt(v[1])==0 || v.length>2)
		{
			$("#"+id).css("border","1px solid #FF0000");
			$("#"+id).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			$("#"+id).addClass("err");
		}
		else
		{
			$("#"+id).css("border","");
			$("#"+id).css("box-shadow","");
			$("#"+id).removeClass("err");
		}
	}
	function item_update()
	{
		var all="";
		var len=$(".all_tr").length;
		var err=$(".err").length;
		if(err>0)
		{
			$(".err:first").focus();
		}
		else
		{
			$("#sav").attr("disabled",true);
			$("#loader").show();
			for(var i=0; i<len; i++)
			{
				var sl=$(".all_tr:eq("+i+")").find('td:eq(0) input:first').val();
				var expdt=$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val();
				var mrp=$(".all_tr:eq("+i+")").find('td:eq(5) input:first').val();
				var gst_per=$(".all_tr:eq("+i+")").find('td:eq(6) select:first').val();
				var gst_amt=$(".all_tr:eq("+i+")").find('td:eq(6) input:first').val();
				var cost=$(".all_tr:eq("+i+")").find('td:eq(7) input:first').val();
				var itm_amt=$(".all_tr:eq("+i+")").find('td:eq(7) input:last').val();
				var dis_per=$(".all_tr:eq("+i+")").find('td:eq(8) input:first').val();
				var dis_amt=$(".all_tr:eq("+i+")").find('td:eq(8) input:last').val();
				all+=sl+"@@"+expdt+"@@"+mrp+"@@"+gst_per+"@@"+gst_amt+"@@"+cost+"@@"+itm_amt+"@@"+dis_per+"@@"+dis_amt+"@@#_#";
			}
			//alert(all);
			$.post("pages/inv_supplier_account_ajax.php",
			{
				ord:$("#ord").val().trim(),
				rcv:$("#rcv").val().trim(),
				credit:$("#credit").val().trim(),
				tcs:$("#tcs").val().trim(),
				adjust:$("#adjust").val().trim(),
				all:all,
				type:10,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				//$("#results").html(data);
				$.gritter.add(
				{
					//title:	'Normal notification',
					text:	'<h5 style="text-align:center;">'+data+'</h5>',
					time: 1000,
					sticky: false
				});
				$(".gritter-item").css("background","#237438");
				$("#canc").click();
			})
		}
	}
	//---------------------------------------------------------------------
	function generate_bill()
	{
		if($(".checker:checked").length==0)
		{
			$.gritter.add(
			{
				//title:	'Normal notification',
				text:	'<h5 style="text-align:center;">Challan No not selected</h5>',
				time: 1000,
				sticky: false
			});
		}
		else
		{
			$("#loader").show();
			var ch=$(".checker:checked");
			var all="";
			for(var i=0; i<ch.length; i++)
			{
				//alert(ch[i].id);
				if(all=="")
				{
					all=(ch[i].id);
				}
				else
				{
					all+="@_@"+(ch[i].id);
				}
			}
			
			$.post("pages/ph_challan_list_ajax.php",
			{
				all:all,
				type:6,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#results").html(data);
				$("#mod").click();
				setTimeout(function(){$("#bill_no").focus();},500);
			});
		}
	}
	
	function generate_bill_confirm(all)
	{
		if($("#bill_no").val().trim()=="")
		{
			$("#bill_no").focus();
		}
		else if($("#bill_dt").val().trim()=="")
		{
			$("#bill_dt").focus();
		}
		else
		{
			$("#sav").attr("disabled",true);
			$("#loader").show();
			$.post("pages/ph_challan_list_ajax.php",
			{
				all:all,
				bill_no:$("#bill_no").val().trim(),
				bill_dt:$("#bill_dt").val().trim(),
				supp:$("#chk_supp").val().trim(),
				user:$("#user").text().trim(),
				type:7,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				$("#mod").click();
				var v=data.split("@_@");
				if(v[0]=="0")
				{
					$.gritter.add(
					{
						//title:	'Normal notification',
						text:	'<h5 style="text-align:center;">'+v[1]+' challan already received.</h5>',
						time: 1500,
						sticky: false
					});
				}
				if(v[0]=="1")
				{
					$.gritter.add(
					{
						//title:	'Normal notification',
						text:	'<h5 style="text-align:center;">Done.</h5>',
						time: 1000,
						sticky: false
					});
					$(".gritter-item").css("background","#237438");
					srch();
					setTimeout(function(){view_items(btoa(v[2]),btoa(v[2]));},1000);
				}
				if(v[0]=="2")
				{
					$.gritter.add(
					{
						//title:	'Normal notification',
						text:	'<h5 style="text-align:center;">Error.</h5>',
						time: 1500,
						sticky: false
					});
				}
				//alert(data);
			});
		}
	}
	function view_items(ord,rcv)
	{
		//alert(rcv);
		$("#loader").show();
		$.post("pages/inv_supplier_account_ajax.php",
		{
			ord:ord,
			rcv:rcv,
			user:$("#user").text().trim(),
			type:9,
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			$("#results").html(data);
			//$("#canc").click();
			$("#mod").click();
			setTimeout(function(){$(".all_tr:eq(0)").find('td:eq(3) input:first').focus();},300);
		})
	}
	//---------------------------------------------------------------------
	function srch()
	{
		$("#loader").show();	
		$.post("pages/ph_challan_list_ajax.php"	,
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			supp:$("#supplier").val(),
			challan_no:$("#challan_no").val().trim(),
			user:$("#user").text().trim(),
			type:1,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})	
	}
	function dt_val()
	{
		$("#n_billdt").val('');
	}
	function challan_received(ord)
	{
		$("#loader").show();
		$.post("pages/ph_challan_list_ajax.php",
		{
			ord:ord,
			type:2,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#results").html(data);
			$("#mod").click();
			setTimeout(function(){$("#bill_no").focus();},500);
		});
	}
	function edit_challan(ord)
	{
		//alert(ord);
		$("#loader").show();
		$.post("pages/ph_challan_list_ajax.php",
		{
			ord:ord,
			type:4,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#results").html(data);
			$("#mod").click();
			setTimeout(function(){$("#challan_no").focus();},500);
		});
	}
	function save_challan_edit(ord)
	{
		//alert(ord);
		if($("#challan_no").val().trim()=="")
		{
			$("#challan_no").focus();
		}
		else if($("#supp").val()=="0")
		{
			$("#supp").focus();
		}
		else if($("#challan_dt").val().trim()=="")
		{
			$("#challan_dt").focus();
		}
		else
		{
			$("#sav").attr("disabled",true);
			$("#loader").show();
			$.post("pages/ph_challan_list_ajax.php",
			{
				ord:ord,
				challan_no:$("#challan_no").val().trim(),
				supp:$("#supp").val(),
				challan_dt:$("#challan_dt").val().trim(),
				user:$("#user").text().trim(),
				type:5,
			},
			function(data,status)
			{
				$("#loader").hide();
				//$("#canc").click();
				//alert(data);
				$.gritter.add(
				{
					//title:	'Normal notification',
					text:	'<h5 style="text-align:center;">Done.</h5>',
					time: 1000,
					sticky: false
				});
				$(".gritter-item").css("background","#237438");
				//~ bootbox.dialog({ message: data});
				//~ setTimeout(function()
				//~ {
					//~ bootbox.hideAll();
				//~ }, 1000);
				srch();
			})
		}
	}
	function save_bill(ord)
	{
		if($("#bill_no").val().trim()=="")
		{
			$("#bill_no").focus();
		}
		else if($("#bill_dt").val().trim()=="")
		{
			$("#bill_dt").focus();
		}
		else
		{
			$("#sav").attr("disabled",true);
			$("#loader").show();
			$.post("pages/ph_challan_list_ajax.php",
			{
				ord:ord,
				bill_no:$("#bill_no").val().trim(),
				bill_dt:$("#bill_dt").val().trim(),
				user:$("#user").text().trim(),
				type:3,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#canc").click();
				//alert(data);
				var v=data.split("@_@");
				//~ bootbox.dialog({ message: v[0]});
				//~ setTimeout(function()
				//~ {
					//~ bootbox.hideAll();
				//~ }, 1000);
				srch();
				if(v[0]=="1")
				{
					//view_items(v[1],v[1]);
					setTimeout(function(){view_items((v[1]),(v[1]));},1000);
				}
				$.gritter.add(
				{
					//title:	'Normal notification',
					text:	'<h5 style="text-align:center;">Done.</h5>',
					time: 1000,
					sticky: false
				});
				$(".gritter-item").css("background","#237438");
			})
		}
	}
	function rcv_challan(ord)
	{
		//alert(ch+"-"+bill+"-"+billdt);
		var values="<table class='table table-condensed'><tr><th colspan='2'>Are you sure want to receive this bill?</th></tr><tr><th>OR Change Bill No.</th><td><input type='text' id='n_bill' value='"+bill+"' /></td></tr><tr><th>Bill Date</th><td><input type='text' onkeyup='dt_val()' id='n_billdt' value='"+billdt+"' /></td></tr></table>";
		bootbox.dialog(
		{
			//message: "<h5>Are you sure want to receive this bill? <br/> OR <br/> Change Bill No.</h5><input type='text' id='n_bill' value='"+bill+"' />",
			message: values,
			buttons:
			{
				cancel:
				{
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function()
					{
					  bootbox.hideAll();
					}
				},
				confirm:
				{
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-info",
					callback: function()
					{
						//alert($("#n_bill").val());
						$("#btn"+ch).attr("disabled",true);
						$.post("pages/ph_challan_list_ajax.php"	,
						{
							supp:supp,
							ch:ch,
							bill:bill,
							n_bill:$("#n_bill").val().trim(),
							n_billdt:$("#n_billdt").val().trim(),
							type:3,
						},
						function(data,status)
						{
							//alert(data);
							bootbox.dialog({ message: data});
							setTimeout(function()
							{
								bootbox.hideAll();
							}, 1000);
							srch();
						})
					}
				}
			}
		});
		$("#n_billdt").datepicker({dateFormat: 'yy-mm-dd', maxDate:0});
	}
	function challan_print_billwise(ord)
	{
		url="pages/ph_challan_receive_print.php?oRd="+btoa(ord);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	function rcv_rep_prr(billno)
	{
		//url="pages/sale_bill_print.php?billno="+billno;
		url="pages/item_rertn_zbra_rpt.php?billno="+billno;
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
function load_refdoc(val,e,typ)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		//alert(unicode);
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				if(unicode==27)
				{
					$("#btn").focus();
				}
				else
				{
					$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
					$("#ref_doc").fadeIn(200);
					$.post("pages/ph_challan_list_ajax.php",
					{
						type:8,
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
				var doc_naam=docs[2].trim();
				//$("#doc_info").fadeIn(200);
				item_load(docs[1],doc_naam);
			}
		}
}
function item_load(id,name)
{
	$("#r_doc").val(name);
	$("#doc_id").val(id);
	$("#ref_doc").fadeOut(200);
	//$("#hguide").focus();
	item_search();
	doc_tr=1;
	doc_sc=0;
}
//------------------------item search end---------------------------------//
function item_search()
{
	$("#loader").show();
	$.post("pages/ph_challan_list_ajax.php",
	{
		itm:$("#doc_id").val().trim(),
		fdate:$("#fdate").val(),
		tdate:$("#tdate").val(),
		supp:$("#supplier").val(),
		type:9,
	},
	function(data,status)
	{
		$("#loader").hide();
		$("#res").html(data);
	});
}
</script>
<style>
tr:hover
{
	background:none;
}
.btn_edit
{
	border-radius: 5%;
	padding-left: 2px;
	padding-right: 2px;
}
.btn_edit:hover
{
	cursor: pointer;
	box-shadow: 0px 0px 7px 2px #A7A7A7;
	transition: 0.5s;
}
label
{
	display:inline-block;
}
.chalan
{
	cursor:pointer;
}
.chalan:hover
{
	text-decoration:underline;
}
</style>
