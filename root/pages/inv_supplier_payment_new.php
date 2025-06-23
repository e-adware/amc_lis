<script type="text/javascript">
	$(document).ready(function()
	{
		
		//$("#button").attr("disabled",false);
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd', maxDate:0});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd', maxDate:0});
		$("#pay_date").datepicker({dateFormat: 'yy-mm-dd', maxDate:0});
		$("#note_dt").datepicker({dateFormat: 'yy-mm-dd', maxDate:0});
		
		$("#selectspplr").keyup(function(e)
		{
			$(this).css("border","");
			//$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#srch_btn").focus();
					//$('#selectcategory').select2('focus');
				}
			}
		});
		
		
		
		$("#selectbill").keyup(function(e)
		{
			if(e.keyCode==27)
			$("#txtnowpaid").focus();
		});
		
		$("#txtnowpaid").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()!="" && parseFloat($(this).val())>0 && $("#chk_paid").val()=="0")
				$("#button").focus();
			}
		});
		
	});
	//--------------------------------------------------------------------------------------
	function chk_credit_dec(ths,e)
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
	function save_credit_amount()
	{
		if($("#credit_amount").val().trim()=="")
		{
			$("#credit_amount").focus();
		}
		else if(parseFloat($("#credit_amount").val().trim()) <= 0)
		{
			$("#credit_amount").focus();
		}
		else
		{
			$("#crd_btn").attr("disabled",true);
			$.post("pages/inv_ajax.php",
			{
				supp:$("#selectspplr").val(),
				credit_amount:$("#credit_amount").val(),
				user:$("#user").text().trim(),
				type:36,
			},
			function(data,status)
			{
				alert(data);
				$("#close_btn").click();
				load_prev_amount();
			})
		}
	}
	function show_credit()
	{
		var vl=$("#selectspplr").val();
		if(vl>0)
		{
			//alert(vl);
			$("#credit_opt").show();
		}
		else
		{
			$("#credit_opt").hide();
		}
		$("#bill_vals").empty();
		load_prev_amount();
	}
	function load_prev_amount()
	{
		var vl=$("#selectspplr").val();
		if(vl>0)
		{
			$("#loader").show();
			$.post("pages/inv_ajax.php",
			{
				type:37,
				supp:$("#selectspplr").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				if(data>0)
				{
					$("#chk_1").attr("checked",true);
					creditnote(1,0);
					$("#note_amt").val(data);
					$("#credit_amt").val(data);
				}
				else
				{
					$("#chk_0").attr("checked",true);
					creditnote(0,0);
					$("#note_amt").val(0);
					$("#credit_amt").val(0);
				}
			})
		}
		else
		{
			$("#chk_0").attr("checked",true);
			creditnote(0,0);
			$("#note_amt").val(0);
			$("#credit_amt").val(0);
		}
	}
	function credit_amount_det()
	{
		var vl=$("#selectspplr").val();
		if(vl>0)
		{
			//alert(vl);
			$("#loader").show();
			$.post("pages/inv_ajax.php",
			{
				type:35,
				supp:$("#selectspplr").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#results").html(data);
				//alert(data);
				$("#mod").click();
			})
		}
	}
	//--------------------------------------------------------------------------------------
	function numentry(id) //for Numeric value support in the text field
	{
		var num=document.getElementById(id);
		
		var numex=/^[0-9]+$/;
		//var nume=/a-z/
		if(!num.value.match(numex))
		{
			num.value="";
		}
	}
		 
	function clearr()//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";	
		}
		//$("#selectsupplr").val('0');
		
		
	}
	
	function reset_all()
	{
		
		setTimeout("location.reload(true);",1000);
	}
		
	function calc_balance(val,e) ///for calculation
	{
		//alert(val);
		var tot=parseFloat($("#txttotalamt").val().trim());
		var adj=$("#adjust").val().trim();
		var prev_bal=$("#prev_bal").val().trim();
		var adj_typ=$("input[name='adj_type']:checked").val();
		var paid=0;
		var bal=0;
		//alert(adj_typ);
		if(prev_bal=="")
		{
			prev_bal=0;
		}
		else
		{
			prev_bal=parseFloat(prev_bal);
		}
		
		if(adj=="")
		{
			adj=0;
		}
		else
		{
			adj=parseFloat(adj);
		}
		
		if(adj_typ=="0")
		{
			paid=tot+prev_bal-adj;
		}
		else
		{
			paid=tot+prev_bal+adj;
		}
		bal=paid-val;
		//paid=paid.toFixed(2);
		//$("#txtnowpaid").val(paid);
		if(bal<0)
		{
			$("#txtbalance").css({"border":"1px solid #D91212","box-shadow":"0px 0px 10px 0px #FD0B0B"});
		}
		else
		{
			$("#txtbalance").css({"border":"","box-shadow":""});
		}
		document.getElementById("txtbalance").value=bal;
	}
	
	function val_load_new(id)///for 
	{
		
		$.post("pages/inv_load_display.php",
		{
			type:"splr_bill_detail",
			splirid:$("#selectspplr").val(),
			billno:$("#selectbill").val(),
		},
		function(data,status)
		{
						
			var val=data.split("@");
			//$("#txtcid").val(val[0]);
			$("#txttotalamt").val(val[1]);	
			$("#txtalrdypaid").val(val[2]);
			$("#txtnowpaid").focus();
		})
	}
	
	
	function insert_data()
	{
		var chk=$(".bill_amt:checked");
		if($("#selectspplr").val()=="0")
		{
			$("#selectspplr").focus();
		}
		else if(chk.length==0)
		{
			alert("No bill selected");
		}
		else if($("#txtnowpaid").val().trim()=="")
		{
			$("#txtnowpaid").focus();
		}
		else if($("input[name='creditnote']:checked").val()=="0" && parseFloat($("#txtnowpaid").val().trim())<=0)
		{
			$("#txtnowpaid").focus();
		}
		else if($("#chk_paid").val().trim()=="1")
		{
			$("#txtnowpaid").focus();
		}
		else if(parseFloat($("#txtbalance").val().trim())<0)
		{
			$("#adjust").focus();
		}
		else if($("input[name='creditnote']:checked").val()=="1" && $("#note_no").val().trim()=="") // credit note
		{
			$("#note_no").focus();
		}
		else if($("input[name='creditnote']:checked").val()=="1" && $("#note_amt").val().trim()=="") // credit note
		{
			$("#note_amt").focus();
		}
		else if($("input[name='creditnote']:checked").val()=="1" && parseInt($("#note_amt").val().trim())<=0) // credit note
		{
			$("#note_amt").focus();
		}
		else if($("input[name='creditnote']:checked").val()=="1" && parseFloat($("#txtnowpaid").val().trim())<0) // credit note
		{
			$("#note_amt").focus();
		}
		else if($("#selectpymtype").val()=="0")
		{
			$("#selectpymtype").focus();
		}
		else if($("#selectpymtype").val()=="Cash" && $("#collect").val().trim()=="")
		{
			$("#collect").focus();
		}
		else if($("#selectpymtype").val()=="Cheque" && $("#chk_no").val().trim()=="")
		{
			$("#chk_no").focus();
		}
		else if($("#selectpymtype").val()=="Cheque" && $("#chk_bank").val()=="0")
		{
			$("#chk_bank").focus();
		}
		else
		{
			$("#button").attr("disabled",true);
			var bill="";
			for(var i=0; i<chk.length; i++)
			{
				bill+=chk[i].name+"@@"+chk[i].value+"#%#";
			}
			$.post("pages/inv_ajax.php",
			{
				type:3333,
				supplrid:$("#selectspplr").val(), 
				billno:bill,
				ttlamt:$("#txttotalamt").val(),
				alrdypaid:$("#txtalrdypaid").val(),
				adjst:$("#adjust").val(),
				nwpaid:$("#txtnowpaid").val(),
				balance:$("#txtbalance").val(),
				ptype:$("#selectpymtype").val(),
				collect:$("#collect").val().trim(),
				chqno:$("#chk_no").val(),
				chk_bank:$("#chk_bank").val(),
				pay_date:$("#pay_date").val().trim(),
				cr_note:$("input[name='creditnote']:checked").val(), // credit note
				note_no:$("#note_no").val().trim(), // credit note
				note_dt:$("#note_dt").val().trim(), // credit note
				note_amt:$("#note_amt").val().trim(), // credit note
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				//alert(data);
				var vl=data.split("@@");
				if(vl[0]=="1")
				{
					final_insert();
					$("#tr_missing").hide();
				}
				if(vl[0]=="2")
				{
					//alert("reason "+vl[1]);
					$("#button").attr("disabled",false);
					$("#missing_chq").html(vl[1]);
					$("#tr_missing").show();
					if($("#missing_reason").val().trim()=="")
					{
						$("#missing_reason").focus();
					}
					else
					{
						final_insert();
					}
				}
				//reset_all();
			})
		}
	}
	function final_insert()
	{
		$("#button").attr("disabled",true);
		$("#loader").show();
		var chk=$(".bill_amt:checked");
		var bill="";
		for(var i=0; i<chk.length; i++)
		{
			bill+=chk[i].name+"@@"+chk[i].value+"#%#";
		}
		$.post("pages/inv_ajax.php",
		{
			type:34,
			supplrid:$("#selectspplr").val(), 
			billno:bill,
			ttlamt:$("#txttotalamt").val(),
			alrdypaid:$("#txtalrdypaid").val(),
			adjst:$("#adjust").val(),
			adjust_type:$("input[name='adj_type']:checked").val(),
			nwpaid:$("#txtnowpaid").val(),
			balance:$("#txtbalance").val(),
			ptype:$("#selectpymtype").val(),
			collect:$("#collect").val().trim(),
			chqno:$("#chk_no").val(),
			chk_bank:$("#chk_bank").val(),
			pay_date:$("#pay_date").val().trim(),
			chk_reason:$("#missing_reason").val().trim(),
			cr_note:$("input[name='creditnote']:checked").val(), // credit note
			note_no:$("#note_no").val().trim(), // credit note
			note_dt:$("#note_dt").val().trim(), // credit note
			note_amt:$("#note_amt").val().trim(), // credit note
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			alert(data);
			reset_all();
		})
	}
	function popitup1(url)
	{
		var substrid=$("#selectsubstr").val();
		var orderno=$("#txtordo").val();
		var orderdate=$("#txtorddate").val();
		
		url=url+"?substrid="+substrid+"&orderno="+orderno+"&orderdate="+orderdate;
		newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
		//$("#button5").attr("disabled",true);
		$("#selectsubstr").attr("disabled",false);
		get_id();
	}
	
function pay_type()
{
	if($("#selectpymtype").val()=="Cheque")
	{
		$("#cheque_typ1").show();
		$("#cheque_typ2").show();
		$("#cash_typ").hide();
		$("#chk_no").focus();
	}
	else if($("#selectpymtype").val()=="Cash")
	{
		$("#cash_typ").show();
		$("#cheque_typ1").hide();
		$("#cheque_typ2").hide();
		$("#collect").focus();
	}
	else
	{
		$("#cash_typ").hide();
		$("#cheque_typ1").hide();
		$("#cheque_typ2").hide();
	}
}

function lod_bill_no()
	{
		$.post("pages/inv_load_display.php",
		{
			type:"splr_bill_load",
			splirid:$("#selectspplr").val(),
		},
		function(data,status)
		{
			$("#chk").val("0");	
			document.getElementById("selectbill").options.length=1;
			var data=data.split("#");
			for(var i=0;i<data.length-1;i++)
			{
				var opt = document.createElement("option");
				var ip=document.getElementById("selectbill").options.add(opt);
				var dvalue=data[i].split("@");
				for(var j=0;j<dvalue.length;j++)
				{
					opt.value=dvalue[0];
					opt.text=dvalue[1];
				}
			}
		})	
		doc_v=1;
		doc_sc=0;
	}
	
	function load_bill_amt()
	{
		var tot=0;
		var chk=$(".bill_amt:checked");
		for(var i=0; i<chk.length; i++)
		{
			tot+=parseFloat(chk[i].value);
		}
		//alert(tot);
		//tot=tot.toFixed(2);
		$("#txttotalamt").val(tot);
		$("#txtnowpaid").val(tot);
		$("#adjust").val("0");
		$("#txtbalance").val("0");
		if(tot>0)
		{
			if(parseInt($("#credit_amt").val().trim())>tot)
			{
				var vtot=tot.split(".");
				$("#note_amt").val(vtot[0]);
			}
			else
			{
				$("#note_amt").val($("#credit_amt").val().trim());
			}
		}
		else
		{
			$("#note_amt").val($("#credit_amt").val().trim());
		}
		creditnote($("input[name='creditnote']:checked").val(),0);
	}
	function bill_vals()
	{
		if($("#selectspplr").val()=="0")
		{
			$("#selectspplr").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/inv_ajax.php",
			{
				type:33,
				fdate:$("#fdate").val().trim(),
				tdate:$("#tdate").val().trim(),
				supp:$("#selectspplr").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#bill_vals").html(data);
				//alert(data);
			})
		}
	}
	function view_bill_detail(rcv)
	{
		// central store
		//var url="pages/inv_supplier_ldger_rpt.php?rCv="+btoa(rcv);
		
		// pharmacy
		var url="pages/purchase_receive_rep_print.php?rCv="+btoa(rcv);
		window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
	}
	function adj_amt()
	{
		var tot=parseFloat($("#txttotalamt").val().trim());
		var adj=$("#adjust").val().trim();
		var paid=$("#txtnowpaid").val().trim();
		var prev_bal=$("#prev_bal").val().trim();
		var adj_typ=$("input[name='adj_type']:checked").val();
		//alert(adj_typ);
		if(prev_bal=="")
		{
			prev_bal=0;
		}
		else
		{
			prev_bal=parseFloat(prev_bal);
		}
		
		if(adj=="")
		{
			adj=0;
		}
		else
		{
			adj=parseFloat(adj);
		}
		
		if(paid=="")
		{
			paid=0;
		}
		else
		{
			paid=parseFloat(paid);
		}
		if(adj_typ=="0")
		{
			paid=tot+prev_bal-adj;
		}
		else
		{
			paid=tot+prev_bal+adj;
		}
		//paid=paid.toFixed(2);
		$("#txtnowpaid").val(paid);
		$("#note_amt").val("0");
		calc_balance(paid,'');
	}
	function chk_dec(ths,e)
	{
		var reg = /^\d+(?:\.\d{1,2})?$/;
		var val=$(ths).val();
		if(val=="")
		{
			val=0;
		}
		if(!reg.test(val))
		{
			$(ths).css("border","1px solid #FF0000");
			$("#chk_paid").val("1");
			return true;
		}
		else
		{
			$("#chk_paid").val("0");
			$(ths).css("border","");
		}
	}
	//--------------------------------------------------
	function creditnote(val,foc)
	{
		var tot=$("#txttotalamt").val().trim();
		if(tot=="")
		{
			tot=0;
		}
		else
		{
			tot=parseFloat(tot);
		}
		var prev_bal=$("#prev_bal").val().trim();
		if(prev_bal=="")
		{
			prev_bal=0;
		}
		else
		{
			prev_bal=parseFloat(prev_bal);
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
		var note_amt=$("#note_amt").val().trim();
		if(note_amt=="")
		{
			note_amt=0;
		}
		else
		{
			note_amt=parseFloat(note_amt);
		}
		
		var net=0;
		if(val==0)
		{
			$(".note_tr").hide();
			if(foc>0)
			{
				$("#selectpymtype").focus();
			}
			net=tot+prev_bal-adjust;
			//net=net.toFixed(2);
		}
		if(val==1)
		{
			$(".note_tr").slideDown();
			net=tot+prev_bal-adjust-note_amt;
			//net=net.toFixed(2);
			if(foc>0)
			{
				$("#note_no").focus();
			}
		}
		if(net>0)
		{
			$("#txtnowpaid").val(net);
		}
		else
		{
			$("#txtnowpaid").val(0);
		}
	}
	function note_amount(id,val)
	{
		if(/\D/g.test(val))
		{
			val=val.replace(/\D/g,'');
			$("#"+id).val(val);
		}
		if(val=="")
		{
			val=0;
		}
		else
		{
			val=parseInt(val);
		}
		//if($("input[name='creditnote']:checked").val()=="")
		var tot=$("#txttotalamt").val().trim();
		if(tot=="")
		{
			tot=0;
		}
		else
		{
			tot=parseFloat(tot);
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
		var net=tot-adjust-val;
		//net=net.toFixed(2);
		$("#txtnowpaid").val(net);
		if(parseFloat($("#txtnowpaid").val().trim())<=0)
		{
			$("#"+id).css("border","1px solid #FF0000");
		}
		else
		{
			$("#"+id).css("border","");
		}
	}
	//------------------------------------------------------------------
	function load_bill()
	{
		if($("#selectspplr").val()=="0")
		{
			$("#selectspplr").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/inv_ajax.php",
			{
				type:42,
				fdate:$("#fdate").val().trim(),
				tdate:$("#tdate").val().trim(),
				supp:$("#selectspplr").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
				//$("#bill_vals").html(data);
				//alert(data);
				$("#add_bill_no option:not(:first)").remove();
				if(data!="")
				{
					var vl=data.split("@%@");
					for(var l=0; l<vl.length; l++)
					{
						$('#add_bill_no').append("<option value='"+vl[l]+"'>"+vl[l]+"</option>");
					}
				}
				load_prev_balance();
			})
		}
	}
	function load_prev_balance()
	{
		$("#loader").show();
		$.post("pages/inv_ajax.php",
		{
			type:32,
			supp:$("#selectspplr").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#prev_bal").val(data);
		});
	}
	function save_credit_amount()
	{
		if($("#selectspplr").val()=="0")
		{
			$("#selectspplr").focus();
		}
		else if($("#credit_bill_no").val().trim()=="")
		{
			$("#credit_bill_no").focus();
		}
		else if($("#credit_amount").val().trim()=="")
		{
			$("#credit_amount").focus();
		}
		else if(parseFloat($("#credit_amount").val().trim())==0)
		{
			$("#credit_amount").focus();
		}
		else
		{
			$("#loader").show();
			$("#btn_cred").attr("disabled",true);
			$.post("pages/inv_ajax.php",
			{
				type:43,
				supp:$("#selectspplr").val(),
				credit_bill_no:$("#credit_bill_no").val().trim(),
				credit_amount:$("#credit_amount").val().trim(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				$("#loader").hide();
				alert(data);
				$("#credit_bill_no").val("");
				$("#credit_amount").val("");
				$("#btn_cred").attr("disabled",false);
			});
		}
	}
	function save_bal_amount()
	{
		if($("#selectspplr").val()=="0")
		{
			$("#selectspplr").focus();
		}
		else if($("#add_bill_no").val()=="0")
		{
			$("#add_bill_no").focus();
		}
		else if($("#add_bill_amt").val().trim()=="")
		{
			$("#add_bill_amt").focus();
		}
		else if(parseFloat($("#add_bill_amt").val().trim())==0)
		{
			$("#add_bill_amt").focus();
		}
		else
		{
			$("#loader").show();
			$("#btn_add_amt").attr("disabled",true);
			$.post("pages/inv_ajax.php",
			{
				type:44,
				supp:$("#selectspplr").val(),
				add_bill_no:$("#add_bill_no").val(),
				add_bill_amt:$("#add_bill_amt").val().trim(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				$("#loader").hide();
				alert(data);
				$("#add_bill_no").val("0");
				$("#add_bill_amt").val("");
				$("#btn_add_amt").attr("disabled",false);
			});
		}
	}
	function page_reload()
	{
		window.location='index.php?param='+btoa(168);
	}
</script>
<div id="content-header">
    <div class="header_div"> <span class="header">Supplier Payment</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr style="display:none;">
			<td> Date</td>
			<td>
				<input type="text" id="txtorddate" name="txtorddate" value="<?php echo date('Y-m-d');?>" onfocus="jsdate('txtorddate')" readonly />
			</td>
		</tr>
		<tr>
			<th>Select Date</th>
			<td>
				<div class="btn-group">
					<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" id="fdate" value="<?php //echo date("Y-m-d"); ?>" >
					<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" id="tdate" value="<?php //echo date("Y-m-d"); ?>" >
				</div>
			</td>
		</tr>
		<tr>
			<th style="width:20%">Select Supplier</th>
			<td>
				<!--<select name="selectspplr" id="selectspplr" onchange="lod_bill_no();bill_vals()"  autofocus>-->
				<select name="selectspplr" id="selectspplr" class="span3" onchange="load_prev_balance()" autofocus>
					<option value="0">Select Supplier</option>
					<?php
						$qsplr=mysqli_query($link,"select id,name from inv_supplier_master order by name");
						while($qsplr1=mysqli_fetch_array($qsplr))
						{if($_POST['id']==$qsplr1['id']){$ssel="Selected='selected'";} else { $ssel=" ";}
					?>
						<option value="<?php echo $qsplr1['id'];?>"><?php echo $qsplr1['name'];?></option>
					<?php
						}
					?>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<button type="button" id="srch_btn" class="btn btn-info" onclick="bill_vals()">Search</button>
				<span id="credit_opt" style="float:right;display:none;">
					<button type="button" class="btn btn-warning" onclick="credit_amount_det()">Credit Amount</button>
				</span>
			</td>
		</tr>
		
		<tr style="display:none;">
			<th>Bill No</th>
			<td>
				<select name="select" id="selectbill" onchange="val_load_new()" onkeyup="next_tab(this.id,event)">
					<option value="0">Select Bill</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="bill_vals" style="max-height:300px;overflow-y:scroll;">
				
				</div>
			</td>
		</tr>
	</table>
	
	<div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" style="display:none;">
              <li class="active"><a data-toggle="tab" href="#tab1">Payment</a></li>
              <li><a data-toggle="tab" href="#tab2">Credit Note</a></li>
              <li><a data-toggle="tab" href="#tab3">Add Balance</a></li>
            </ul>
          </div>
          <div class="widget-content tab-content">
            <div id="tab1" class="tab-pane active">
				<table class="table table-condensed table-bordered">
				   <tr>
						<th width="20%">Total Amount</th>
						<td>
							<input type="text" name="txttotalamt" id="txttotalamt" size="20" value="" class="imp" readonly="readonly" />
						</td>
					</tr>
					
					<tr style="display:none;">
						<th>Already paid</th>
						<td>
							<input type="text" name="txtalrdypaid" id="txtalrdypaid" size="20" value="" class="imp" readonly="readonly" />
						</td>
					</tr>
					<tr>
						<th>Previous Balance</th>
						<td>
							<input type="text" name="prev_bal" id="prev_bal" readonly="readonly" />
						</td>
					</tr>
					<tr>
						<th>Adjustment</th>
						<td>
							<input type="text" name="adjust" id="adjust" size="20" value="" onkeyup="chk_dec(this,event);adj_amt()" />
							<label class="chk_adj"><input type="radio" name="adj_type" onchange="adj_amt()" value="0" checked /> Deduct</label>
							<label class="chk_adj"><input type="radio" name="adj_type" onchange="adj_amt()" value="1" /> Add</label>
						</td>
					</tr>
					<tr>
						<th>Now Paid</th>
						<td>
							<input type="hidden" id="chk_paid" value="0" />
							<input type="text" name="txtnowpaid" id="txtnowpaid" size="20" value="" class="imp" onkeyup="chk_dec(this,event);calc_balance(this.value,event)" />
						</td>
					</tr>
					
					<tr>
						<th>Balance</th>
						<td>
							<input type="text" name="txtbalance" id="txtbalance" size="20" value="" class="imp" readonly="readonly" />
						</td>
					</tr>
					<tr style="display:none;">
						<th>Credit Note</th>
						<td>
							<label class="creditnote">
								<input type="radio" name="creditnote" id="chk_1" onchange="creditnote(this.value,1)" value="1" /> <b>Yes</b>
							</label>
							<label class="creditnote">
								<input type="radio" name="creditnote" id="chk_0" onchange="creditnote(this.value,1)" value="0" checked /> <b>No</b>
							</label>
						</td>
					</tr>
					<tr class="note_tr" style="display:none;">
						<th>Credit Note No</th>
						<td>
							<input type="text" id="note_no" class="" placeholder="Credit Note No." />
						</td>
					</tr>
					<tr class="note_tr" style="display:none;">
						<th>Credit Note Date</th>
						<td>
							<input type="text" id="note_dt" class="" placeholder="YYYY-MM-DD" value="<?php echo date('Y-m-d');?>" readonly />
						</td>
					</tr>
					<tr class="note_tr" style="display:none;">
						<th>Credit Amount</th>
						<td>
							<input type="hidden" id="credit_amt" value="0" />
							<input type="text" id="note_amt" value="0" onkeyup="note_amount(this.id,this.value)" placeholder="Credit Amount" />
						</td>
					</tr>
					<tr>
						<th>Payment Type</th>
						<td>
							<select name="select" id="selectpymtype" class="" onchange="pay_type()">
								<option value="0">Select</option>
								<option value="Cash">Cash</option>
								<option value="Card">Card</option>
								<option value="Cheque">Cheque</option>
							</select>
						</td>
					</tr>
					<tr id="cash_typ" style="display:none;">
						<th>Collected by</th>
						<td>
							<input type="text" id="collect" class="span3" placeholder="Collected By" />
						</td>
					</tr>
					<tr id="cheque_typ1" style="display:none;">
						<th>Cheque No</th>
						<td>
							<input type="text" id="chk_no" class="intext" placeholder="Cheque No" />
						</td>
					</tr>
					<tr id="cheque_typ2" style="display:none;">
						<th>Cheque Issuing Bank</th>
						<td>
							<select id="chk_bank" class="span3">
								<option value="0">Select</option>
								<?php
								$q=mysqli_query($link,"SELECT * FROM `banks` ORDER BY `bank_name`");
								while($r=mysqli_fetch_assoc($q))
								{
								?>
								<option value="<?php echo $r['bank_id'];?>"><?php echo $r['bank_name'];?></option>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr id="tr_missing" style="display:none;">
						<th>Enter reason for <span id="missing_chq"></span> missing cheque(s)</th>
						<td>
							<input type="text" id="missing_reason" class="intext" placeholder="Reason" />
						</td>
					</tr>
					<tr>
						<th>Payment Date</th>
						<td>
							<input type="text" id="pay_date" class="intext" placeholder="YYYY-MM-DD" value="<?php echo date('Y-m-d');?>" readonly />
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:center">
							<input type="button" name="button" id="button" value="Save" onclick="final_insert()" class="btn btn-success" />
							<input type="button" name="button2" id="button2" value="Reset" onclick="page_reload()" class="btn btn-danger" />
							<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/inv_purchase_ordr_rpt.php')"  />-->
						</td>
					</tr>
				</table>
            </div>
            
            <div id="tab2" class="tab-pane">
				<table class="table table-condensed table-bordered">
					<tr>
						<th width="20%">Enter Return No</th>
						<td>
							<input type="text" id="credit_bill_no" placeholder="Return Invoice No" />
						</td>
					</tr>
					<tr>
						<th>Enter Credit Amount</th>
						<td><input type="text" id="credit_amount" onkeyup="chk_dec(this,event)" placeholder="Amount" /></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center">
							<button type="button" class="btn btn-success" id="btn_cred" onclick="save_credit_amount()">Save</button>
							<button type="button" class="btn btn-danger" onclick="page_reload()">Reload</button>
						</td>
					</tr>
				</table>
            </div>
            
            <div id="tab3" class="tab-pane">
				<table class="table table-condensed table-bordered">
					<tr>
						<th width="20%">Select Bill</th>
						<td>
							<select id="add_bill_no">
								<option value="0">Select</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>Added Amount</th>
						<td><input type="text" id="add_bill_amt" onkeyup="chk_dec(this,event)" placeholder="Amount" /></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center">
							<button type="button" class="btn btn-success" id="btn_add_amt" onclick="save_bal_amount()">Save</button>
							<button type="button" class="btn btn-danger" onclick="page_reload()">Reload</button>
						</td>
					</tr>
				</table>
            </div>
           
           </div>
          </div>
	
	<!--modal-->
	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
	<div class="modal fade" id="myModal">
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
	
<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
label{display:inline-block;}
.creditnote, .chk_adj {width:90px; border:1px dotted #aaaaaa; padding:5px;margin-left:5;}
.creditnote:hover, .chk_adj:hover{border:1px solid #aaaaaa; box-shadow:0px 0px 6px 0px #aaaaaa;transition-duration:0.4s;}
.table tr:hover{background:none;}
.table
{margin-bottom:0px;}
#myModal
{
	left:40%;
	width:60%;
}
</style>
