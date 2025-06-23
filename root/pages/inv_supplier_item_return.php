<div id="content-header">
    <div class="header_div"> <span class="header">Item Return to Supplier</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<input type="hidden" id="txtdate" name="txtdate" value="<?php echo date('Y-m-d');?>" readonly />
				<table class="table table-bordered table-condensed">
					<tr>
						<th>Select Supplier</th>
						<th colspan="4">
							Reason
							<div id="scheme_info" style="width:50%;"></div>
						</th>
					</tr>
					
					<tr>
						<td>
							<select name="selectspplr" id="selectspplr" class="span4">
								<option value="0">Select Supplier</option>
								<?php
									$qsplr=mysqli_query($link,"select id,name from inv_supplier_master order by name");
									while($qsplr1=mysqli_fetch_array($qsplr))
									{
								?>
									<option value="<?php echo $qsplr1['id'];?>"><?php echo $qsplr1['name'];?></option>
								<?php
									}
								?>
							</select>
						</td>
						<td colspan="4">
							<input type="text" name="txtreason" id="txtreason" class="span5 imp" placeholder="Reason" />
						</td>
					</tr>
									
					
					<tr>
					   <th>Item Name</th>
					   <th>Batch No</th>
					   <th>Return Qty</th>
					   <th>Free Qty</th>
					   <th>Available Qty</th>
					   <th>Expiry</th>
					</tr>
					
					<tr>
						<td>
							<input type="text" name="r_doc" id="r_doc" class="span5" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="$('#ref_doc').fadeOut(300)" value="" placeholder="Item Name OR Code OR Short Name" />
							<input type="text" name="doc_id" id="doc_id" style="display:none;" value="">
							<input type="text" name="gst_pr" id="gst_pr" style="display:none;" value="">
							<div id="doc_info"></div>
							<div id="ref_doc" align="center" style="padding:8px;width:600px;">
								
							</div>
						</td>
						<td>
							<input type="text" name="hguide" id="hguide" class="span2" size="25" onFocus="hguide_up(this.value,event,'batch')" onKeyUp="hguide_up(this.value,event,'batch')" onBlur="javascript:$('#hguide_div').fadeOut(500)" placeholder="Batch No" />
							<input type="text" name="hguide_id" id="hguide_id" style="display:none;" value="">
							<input type="text" name="costprice" id="costprice" style="display:none;" value="">
							<input type="text" name="bch_val" id="bch_val" style="display:none;" value="">
							<div id="hguide_info"></div>
							<div id="hguide_div" align="center" style="padding:8px;width:400px;"></div>
						</td>
						<td>
							<input type="text" name="txtqnt" id="qnt"  autocomplete="off" class="imp intext" onfocus="scheme_info()" onblur="scheme_info_hide()" onkeyup="numentry('txtqnt')" placeholder="Quantity" style="width:70px" />
						</td>
						<td>
							<input type="text" id="free"  autocomplete="off" class="imp intext" onkeyup="numentry('free')" placeholder="Free Qty" style="width:70px" />
						</td>
						<td>
							<input type="text" name="txtavailstk" id="txtavailstk"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" placeholder="Available Qty" style="width:100px" readonly />
						</td>
						<td>
							<input type="text" name="txtexpiry" id="txtexpiry"  autocomplete="off" class="imp intext"  placeholder="Expiry" style="width:100px" readonly />
						</td>
					</tr>
					<tr>
						<td colspan="6" style="text-align:center">
							<!--<input type="button" name="button2" id="button2" value="Reset" onclick="reset_all();" class="btn btn-danger" />-->
							<!--<input type="button" name="button" id="button" value="Add" onclick="insert_data()" class="btn btn-info" />-->
							<input type="button" name="button" id="button" value="Add" onclick="add_data()" class="btn btn-info" />
							<input type="button" name="button4" id="button4" value="Done" onclick="save_data_final()" class="btn btn-primary" />
							<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/inv_indent_order_rpt.php')" class="btn btn-success" disabled />-->
						</td>
					</tr>
				</table>
				
			<!--<div class="span11">
				<div id="load_select" class="vscrollbar" style="max-height:250px;overflow-y:scroll;" >
					
				</div>
			</div>-->
			<div id="temp_item">
			
			</div>
		<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
		<div id="msgg" style="display:none;top:8%;left:38%;background:#FFFFFF;padding:5px;border-radius:4px;box-shadow:0px 0px 8px 0px #F67379;position:fixed;font-size:20px;font-weight:bold;color:#D90913"></div>
		<div id="loader" style="display:none;top:50%;position:fixed;"></div>
</div>
<input type="hidden" id="chk_val2"  />
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#selectspplr").select2({ theme: "classic" });
		$("#selectspplr").select2("focus");
		
		$("#selectspplr").on("select2:close",function(e)
		{
			if($("#selectspplr").val()!="0")
			{
				setTimeout(function(){$("#txtreason").focus();},200);
			}
		});
		
		$("#txtreason").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#r_doc").focus();
		});
				
		$("#qnt").keyup(function(e)
		{
			$("#qnt").css({"border":"","box-shadow":""});
			var val=$("#qnt").val();
			if(/\D/g.test(val))
			{
				val=val.replace(/\D/g,'');
				$("#qnt").val(val);
			}
			if(e.keyCode==13)
			{
				var free=$("#free").val();
				if(free=="")
				{
					free=0;
				}
				else
				{
					free=parseInt(free);
				}
				if($("#qnt").val().trim()!="" && parseInt($("#qnt").val())!=0 && (parseInt($("#qnt").val())+free)<=parseInt($("#txtavailstk").val().trim()))
				{
					$("#free").focus();
				}
				else
				{
					$("#qnt").css({"border":"1px solid #E91A1A","box-shadow":"0px 0px 5px 0px #FF4E4E"});
					$("#qnt").focus();
				}
			}
		});
		
		$("#free").keyup(function(e)
		{
			$("#free").css({"border":"","box-shadow":""});
			var val=$("#qnt").val();
			if(/\D/g.test(val))
			{
				val=val.replace(/\D/g,'');
				$("#free").val(val);
			}
			if(e.keyCode==13)
			{
				var qnt=$("#qnt").val();
				if(qnt=="")
				{
					qnt=0;
				}
				else
				{
					qnt=parseInt(qnt);
				}
				if($("#free").val().trim()!="" && (parseInt($("#free").val())+qnt)<=parseInt($("#txtavailstk").val().trim()))
				{
					$("#button").focus();
				}
				else
				{
					$("#free").css({"border":"1px solid #E91A1A","box-shadow":"0px 0px 5px 0px #FF4E4E"});
					$("#free").focus();
				}
			}
		});
		
		$("#txtcntrname").keyup(function(e)
		{
			if(e.keyCode==27)
			$("#button4").focus();
		});
		
	});
	function scheme_info()
	{
		$.post("pages/inv_supplier_item_return_ajax.php",
		{
			type:3,
			itm:$("#doc_id").val().trim(),
			bch:$("#hguide_id").val().trim(),
			supp:$("#selectspplr").val(),
		},
		function(data,status)
		{
			//alert(data);
			if(data!="")
			{
				$("#scheme_info").html(data);
				$("#scheme_info").show();
			}
			else
			{
				$("#scheme_info").empty();
				$("#scheme_info").hide();
			}
		})
	}
	function scheme_info_hide()
	{
		$("#scheme_info").hide();
	}
	function add_data()
	{
		var free=$("#free").val().trim();
		if(free=="")
		{
			free=0;
		}
		else
		{
			free=parseInt(free);
		}
		if($("#doc_id").val()=="")
		{
			$("#r_doc").focus();
		}
		else if($("#bch_val").val()=="1" && $("#hguide_id").val()=="")
		{
			$("#hguide").focus();
		}
		else if($("#qnt").val().trim()=="")
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val().trim())==0)
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val().trim())<0)
		{
			$("#qnt").focus();
		}
		else if((parseInt($("#qnt").val().trim()))+free > parseInt($("#txtavailstk").val().trim()))
		{
			$("#qnt").focus();
		}
		else
		{
			add_item_temp($("#doc_id").val(),$("#r_doc").val(),$("#hguide_id").val(),$("#qnt").val(),free,$("#costprice").val(),$("#txtexpiry").val(),$("#gst_pr").val());
		}
	}
	function add_item_temp(itm_id,itm_name,bch,qnt,free,cost,exp_dt,gst_pr)
	{
		var tr_len=$('#mytable tr.all_tr').length;
		if(cost=="")
		{
			cost=0;
		}
		if(gst_pr=="")
		{
			gst_pr=0;
		}
		var gst_amt=((parseFloat(cost)*parseFloat(gst_pr))/100)*parseFloat(qnt);
		var amt=parseFloat(qnt)*parseFloat(cost);
		amt=amt.toFixed(2);
		gst_amt=gst_amt.toFixed(2);
		if(tr_len==0)
		{
			var test_add="<table class='table table-condensed table-bordered table-report' id='mytable'>";
			test_add+="<tr><th width='5%'>SL</th><th>Description</th><th>Batch No</th><th>Quantity</th><th>Free</th><th>Expiry</th><th style='text-align:right;'>Rate</th><th style='text-align:right;'>Amount</th><th style='text-align:right;'>GST %</th><th style='text-align:right;'>GST Amount</th><th width='5%'>Remove</th></tr>";
			test_add+="<tr class='all_tr'><td>1</td><td>"+itm_name+"<input type='hidden' value='"+itm_id+"' class='itm_id'/><input type='hidden' value='"+itm_id+bch+"' class='test_id'/></td><td>"+bch+"<input type='hidden' value='"+bch+"' class='bch'/></td><td>"+qnt+"<input type='hidden' value='"+qnt+"' class='qnt' /></td><td>"+free+"<input type='hidden' value='"+free+"' class='free' /></td><td>"+exp_dt+"</td><td style='text-align:right;'>"+cost+"<input type='hidden' value='"+cost+"' class=''/></td><td style='text-align:right;'>"+amt+"<input type='hidden' value='"+amt+"' class='all_rate'/></td><td style='text-align:right;'>"+gst_pr+"<input type='hidden' value='"+gst_pr+"' class=''/></td><td style='text-align:right;'>"+gst_amt+"<input type='hidden' value='"+gst_amt+"' class='all_gst'/></td><td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_sl();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td></tr>";
			test_add+="<tr id='new_tr'><th colspan='6' style='text-align:right;'>Total</th><td colspan='2' id='final_rate'>"+amt+"</td><td colspan='2' id='final_gst'>"+gst_amt+"</td><td></td></tr>";
			test_add+="</table>";
			
			$("#temp_item").html(test_add);
			tr_len++;
		}
		else
		{
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id");
			
			for(var i=0;i<test_l.length;i++)
			{
				if(test_l[i].value==(itm_id+bch))
				{
					t_ch=1;
				}
			}
			if(t_ch)
			{
				$("#temp_item").css({'opacity':'0.5'});
				$("#msgg").text("Already selected same item and batch no.");
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#temp_item").css({'opacity':'1.0'});
				})},800);
			}
			else
			{
				tr_len++;
				var tr=document.createElement("tr");
				tr.setAttribute("class","all_tr");
				var td1=document.createElement("td");
				var td2=document.createElement("td");
				var td3=document.createElement("td");
				var td4=document.createElement("td");
				var td5=document.createElement("td");
				var td6=document.createElement("td");
				var td7=document.createElement("td");
				var td8=document.createElement("td");
				var td9=document.createElement("td");
				var td10=document.createElement("td");
				var td11=document.createElement("td");
				
				td1.innerHTML=tr_len;
				td2.innerHTML=itm_name+"<input type='hidden' value='"+itm_id+"' class='itm_id'/><input type='hidden' value='"+itm_id+bch+"' class='test_id'/>";
				td3.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='bch'/>";
				td4.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' class='qnt'/>";
				td5.innerHTML=free+"<input type='hidden' value='"+free+"' class='free'/>";
				td6.innerHTML=exp_dt;
				td7.innerHTML=cost+"<input type='hidden' value='"+cost+"' class=''/>";
				td7.setAttribute("style","text-align:right;");
				td8.innerHTML=amt+"<input type='hidden' value='"+amt+"' class='all_rate'/>";
				td8.setAttribute("style","text-align:right;");
				td9.innerHTML=gst_pr+"<input type='hidden' value='"+gst_pr+"' class=''/>";
				td9.setAttribute("style","text-align:right;");
				td10.innerHTML=gst_amt+"<input type='hidden' value='"+gst_amt+"' class='all_gst'/>";
				td10.setAttribute("style","text-align:right;");
				td11.innerHTML="<span onclick='$(this).parent().parent().remove();set_sl();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span>";
				td11.setAttribute("style","text-align:center;");
				
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tr.appendChild(td4);
				tr.appendChild(td5);
				tr.appendChild(td6);
				tr.appendChild(td7);
				tr.appendChild(td8);
				tr.appendChild(td9);
				tr.appendChild(td10);
				tr.appendChild(td11);
				
				document.getElementById("mytable").appendChild(tr);
				//~ var new_tr="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2'></td><td colspan='2' id='final_rate'></td><td></td></tr>";
				//~ $("#new_tr").remove();
				//~ $('#mytable tr:last').after(new_tr);
			}
		}
		$("#doc_id").val('');
		$("#r_doc").val('');
		$("#gst_pr").val('');
		$("#hguide_id").val('');
		$("#hguide").val('');
		$("#qnt").val('');
		$("#free").val('');
		$("#costprice").val('');
		$("#txtavailstk").val('');
		$("#txtexpiry").val('');
		set_amt();
		setTimeout(function()
		{
			$("#r_doc").focus();
		},500);
	}
	function set_amt()
	{
		var tot=0;
		var gst_amt=0;
		var tot_ts=document.getElementsByClassName("all_rate");
		var tot_gst=document.getElementsByClassName("all_gst");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseFloat(tot_ts[j].value);
			gst_amt=gst_amt+parseFloat(tot_gst[j].value);
		}
		gst_amt=gst_amt.toFixed(2);
		tot=tot.toFixed(2);
		var new_tr="<tr id='new_tr'><th colspan='6' style='text-align:right;'>Total</th><td colspan='2' id='final_rate' style='text-align:right;'>"+tot+"</td><td colspan='2' id='final_gst' style='text-align:right;'>"+gst_amt+"</td><td></td></tr>";
		$("#new_tr").remove();
		$('#mytable tr:last').after(new_tr);
		
		//~ $("#final_gst").text(gst_amt.toFixed(2));
		//~ $("#final_rate").text(tot.toFixed(2));
	}
	function save_data_final()
	{
		if($("#selectspplr").val()=="0")
		{
			$("#selectspplr").select2("focus");
			$(".select2-selection").css({"border":"1px solid #FF0000","box-shadow":"0px 0px 5px 0px #F85959"});
			setTimeout(function(){$(".select2-selection").css({"border":"","box-shadow":""});},800);
		}
		else if($("#txtreason").val().trim()=="")
		{
			$("#txtreason").focus();
			$("#txtreason").css({"border":"1px solid #FF0000","box-shadow":"0px 0px 5px 0px #F85959"});
			setTimeout(function(){$("#txtreason").css({"border":"","box-shadow":""});},800);
		}
		else if($(".all_tr").length==0)
		{
			$("#msgg").text("No Item Selected.");
			$("#msgg").fadeIn(500);
			setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#r_doc").focus();})},800);
		}
		else
		{
			//alert();
			$("#loader").show();
			$("#button").attr("disabled",true);
			$("#button4").attr("disabled",true);
			var len=$(".all_tr").length;
			var all="";
			for(var i=0; i<len; i++)
			{
				all+=$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val(); // item_id
				all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val(); // batch no
				all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val(); // qnt
				all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val(); // free
				all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(6) input:first').val(); // rate
				all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(7) input:first').val(); // amount
				all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(8) input:first').val(); // gst_per
				all+="@@"+$(".all_tr:eq("+i+")").find('td:eq(9) input:first').val(); // gst_amount
				all+="@@#%#";
				$(".all_tr:eq("+i+")").find('td:eq(10)').text('');
			}
			//alert(all);
			$.post("pages/inv_supplier_item_return_ajax.php",
			{
				type:2,
				supp:$("#selectspplr").val(),
				reason:$("#txtreason").val().trim(),
				final_gst:$("#final_gst").text().trim(),
				final_rate:$("#final_rate").text().trim(),
				user:$("#user").text().trim(),
				all:all,
			},
			function(data,status)
			{
				$("#loader").hide();
				alert(data);
			})
		}
	}
	function set_sl()
	{
		var tot_ts=document.getElementsByClassName("all_tr");
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
	}
	//=========================================================================
	//------------------------item search---------------------------------//
	function load_refdoc1()
	{
			//$("#ref_doc").fadeIn(200);
			//$("#hguide").select();
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
						$("#uhid").focus();
					}
					else
					{
						$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
						$("#ref_doc").fadeIn(200);
						$.post("pages/inv_supplier_item_return_ajax.php",
						{
							type:1,
							typ:typ,
							val:val,
							supp:$("#selectspplr").val(),
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
						$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var doc_tr1=doc_tr-1;
						$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
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
						$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var doc_tr1=doc_tr+1;
						$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
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
					var gst_pr=docs[3].trim();
					$("#doc_info").fadeIn(200);
					doc_load(docs[1],doc_naam,gst_pr);
				}
			}
	}
	function doc_load(id,name,gst_pr)
	{
		//alert(gst_pr);
		$("#r_doc").val(name);
		$("#doc_id").val(id);
		$("#gst_pr").val(gst_pr);
		//$("#doc_info").html("");
		$("#ref_doc").fadeOut(200);
		$("#qnt").val('');
		$("#free").val('');
		$("#bch_val").val('');
		$("#hguide").focus();
		doc_tr=1;
		doc_sc=0;
	}
	//------------------------item search end---------------------------------//
	//-----------------------------------------Load Doctor List Onfocus-------------------------------||
	function hguide_focus()
	{
		$("#hguide_div").fadeIn(200);
		$("#hguide").select();
		setTimeout(function(){ $("#chk_val2").val(1)},200);
	}
	//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||

	function hguide_up(val,e,typ)
	{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode!=13)
			{
				if(unicode!=40 && unicode!=38)
				{
					if(unicode==27)
					{
						$("#uhid").focus();
					}
					else
					{
						$("#hguide_div").html("<img src='../images/ajax-loader.gif' />");
						$("#hguide_div").fadeIn(200);
						$.post("pages/inv_supplier_item_return_ajax.php",
						{
							val:val,
							item_id:$("#doc_id").val().trim(),
							type:4,
							typ:typ,
							supp:$("#selectspplr").val(),
						},
						function(data,status)
						{
							$("#hguide_div").html(data);	
							doc_tr=1;
							doc_sc=0;
						})
					}
				}
				else if(unicode==40)
				{
					var chk=doc_tr+1;
					var cc=document.getElementById("hg"+chk).innerHTML;
					if(cc)
					{
						doc_tr=doc_tr+1;
						$("#hg"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var doc_tr1=doc_tr-1;
						$("#hg"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
						var z3=doc_tr%1;
						if(z3==0)
						{
							$("#hguide_div").scrollTop(doc_sc)
							doc_sc=doc_sc+30;
						}
					}
				}
				else if(unicode==38)
				{
					var chk=doc_tr-1;
					var cc=document.getElementById("hg"+chk).innerHTML;
					if(cc)
					{
						doc_tr=doc_tr-1;
						$("#hg"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var doc_tr1=doc_tr+1;
						$("#hg"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
						var z3=doc_tr%1;
						if(z3==0)
						{
							doc_sc=doc_sc-30;
							$("#hguide_div").scrollTop(doc_sc)
						}
					}
				}
				
			}
			else
			{
				var cen_chk1=document.getElementById("chk_val2").value;
				if(cen_chk1!=0)
				{
					var docs=document.getElementById("dvhguide"+doc_tr).innerHTML.split("#");
					var bch=docs[1].trim();
					var qnt=docs[2].trim();
					var cost=docs[3].trim();
					var exp_dt=docs[4].trim();
					var bch_val=docs[5].trim();
					//alert(bch+"-"+qnt+"-"+mrp+"-"+exp_dt);
					hguide_load(bch,qnt,cost,exp_dt,bch_val);
					$("#hguide_info").fadeIn(200);
					
				}
			}
	}
	function hguide_load(bch,qnt,cost,exp_dt,bch_val)
	{
		$("#hguide").val(bch);
		$("#hguide_id").val(bch);
		//~ $("#bch_qnt").val(qnt);
		$("#txtavailstk").val(qnt);
		$("#costprice").val(cost);
		//~ $("#bch_gst").val(gst);
		$("#txtexpiry").val(exp_dt);
		$("#bch_val").val(bch_val);
		$("#hguide_info").html("");
		$("#hguide_div").fadeOut(200);
		$("#qnt").focus();
		doc_tr=1;
		doc_sc=0;
	}
	//-----------------------------------------end-----------------------------------//
</script>
<style>
.table-report
{
	background:#FFFFFF;
}
</style>
