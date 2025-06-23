<style>
	#ret_tbl > thead > tr > th, #ret_tbl > tbody > tr > th, #ret_tbl > tfoot > tr > th, #ret_tbl > thead > tr > td, #ret_tbl > tbody > tr > td, #ret_tbl > tfoot > tr > td
	{
		padding:1px 1px 1px 5px;
	}
	.child_div
	{
		display:inline-block;
		width:49%;
	}
	.alt_text
	{
		text-align: center !important;
		font-size: 20px;
		line-height: 20px !important;
	}
	#repmodal
	{
		left:23%;
		width:50%;
		z-index: 9999999999 !important;
	}
	#temp_item_alt
	{
		max-height:350px;
		overflow-y:scroll;
	}
	.modal-backdrop, .modal-backdrop.fade.in
	{
		opacity: 0.2;
	}
	.alt_text {
  animation: blinker 1s linear infinite;
  //animation: blinker 1s step-start infinite;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
</style>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Item Return From Patient</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="">
		<div class="">
			<table class="table table-condensed table-bordered" id="ret_tbl">
				<tr>
					<th>
						Patient Name<br/>
						<input type="text" class="span3" name="patname"  id="patname" value="" autocomplete="off" class="" readonly="readonly" />
					</th>
					<th>
						Bill Amount<br/>
						<input type="text" class="span2" name="bill_amt"  id="bill_amt" value="" autocomplete="off" class="" readonly="readonly" />
					</th>
					<th>
						Date<br/>
						<input type="text" class="span2" id="txtdate" name="txtdate" value="<?php echo date('Y-m-d');?>" readonly="readonly" />
					</th>
					<th>
						Bill No.<br/>
						<input type="text" class="span2" name="txtbillno"  id="txtbillno" value="" autocomplete="off" class="imp intext" />
						<input type="text" class="span2" name="counter" style="display:none;" id="counter" />
					</th>
					<th>
						Reason<br/>
						<input type="text" class="span2" name="txtreason" id="txtreason"  autocomplete="off" class="imp intext"  />
					</th>
				</tr>
				
				<tr>
					<th>
						Paid<br/>
						<input type="text" class="span3" name="txtpaid"  id="txtpaid" value="" autocomplete="off" class="" readonly="readonly" />
					</th>
					<th>
						Discount<br/>
						<input type="text" class="span2" name="txtdis"  id="txtdis" value="" autocomplete="off" class="" readonly="readonly" />
						<input type="hidden" id="dis_per" value="" />
					</th>
					<th>
						Balance<br/>
						<input type="text" class="span2" name="balance"  id="balance" value="" autocomplete="off" class="" readonly="readonly" />
					</th>
					<th>
						<div id="alt_text" class="alt_text"></div>
					</th>
					<th>
						<div id="alt_amt" class="alt_text"></div>
					</th>
				</tr>
				
				
				<tr>
					<th>
						Item Name<br/>
						<input type="text" class="span3" name="txtcntrname"  id="txtcntrname" value="" autocomplete="off" class="imp intext" readonly="readonly"/>
					</th>
					<th>
						MRP<br/>
						<input type="text" class="span2" name="itmrate" id="itmrate" value="" readonly="readonly" class="imp intext"/>
					</th>
					<th>
						Sale Qnt<br/>
						<input type="text" class="span2" name="txtsaleqnty" id="txtsaleqnty"  autocomplete="off" class="imp intext" onkeyup="numentry('txtsaleqnty')" readonly />
					</th>
					<th>
						Batch No<br/>
						<select name="select" id="selectbatch"  onkeyup="next_tab(1,event)" onchange="val_load_qnty()">
							<option value="0">--Select Batch No--</option>
						</select>
					</th>
					<th>
						Quantity<br/>
						<input type="text" class="span2" name="txtqnt" id="txtqnt"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" />
					</th>
				</tr>
				<tr>
					<td colspan="5" style="text-align:center">
						
						<input type="button" name="button2" id="button2" value="Reset" onclick="location.reload(true);" class="btn btn-danger" /> 
						<input type="button" name="button3" id="button3" value="Add" onclick="add_item()" class="btn btn-default" /> 
						
						<input type="button" name="button4" id="button4" value="Done" onclick="insert_data_final()" class="btn btn-default" disabled="disabled" />
						<input type="button" id="btn8" value="Print" class="btn btn-success" onclick="print_bill()"  />
						<div id="msgg" style="display:none;position:absolute;color:#e00;font-size:22px;left:35%;"></div>
						<span style="float:right;margin-right:30px;margin-left:-35px;"><button type="button" class="btn btn-info" id="btn_alt" onclick="return_alt()" disabled>Alter Item <small>(Ctrl+Space)</small></button></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div style="width:100%;">
		<div class="child_div">
			Item Search <input type="text" name="txtcustnm" id="txtcustnm"  autocomplete="off" class="intext" onkeyup="sel_pr(this.value,event)" />
			<div id="res" style="max-height:350px;overflow-y:scroll;">
			
			</div>
		</div>
		<div id="temp_item" class="child_div" style="max-height:300px;overflow-y:scroll;"></div>
		<!---------------------------------------------------------------------------------->
		<div style="display:none;">
			<table class="table table-condensed table-bordered" id="ret_tbl" style="display:none;">
				<tr>
					<th>Date</th>
					<td><input type="text" id="txtdate" name="txtdate" value="<?php echo date('Y-m-d');?>" readonly="readonly" /></td>
				</tr>
				
				<tr>
					<th>Bill No</th>
					<td>
						<input type="text" name="txtbillno"  id="txtbillno" value="" autocomplete="off" class="imp intext" />
						<input type="text" name="counter" style="display:none;" id="counter" />
					</td>
				</tr>
				
				<tr>
					<th>Customer Name</th>
					<td>
						<input type="text" name="patname"  id="patname" value="" autocomplete="off" class="" readonly="readonly" />
					</td>
				</tr>
				
				<tr>
					<th>Bill Amount</th>
					<td>
						<input type="text" name="bill_amt"  id="bill_amt" value="" autocomplete="off" class="" readonly="readonly" />
					</td>
				</tr>
				
				<tr>
					<th>Batch No</th>
					<td>
						<select name="select" id="selectbatch"  onkeyup="next_tab(1,event)">
							<option value="0">--Select Batch No--</option>
						</select>
					</td>
				</tr>
				
				<tr style="display:none;">
					<th>ID</th>
					<td>
						<input type="text" name="txtcid" id="txtcid" value="" readonly="readonly" class="imp intext"/>
						
					</td>
				</tr>
				<tr>
					<th>Item Name</th>
					<td>
						<input type="text" name="txtcntrname"  id="txtcntrname" value="" autocomplete="off" class="imp intext" readonly="readonly"/>
					</td>
				</tr>
				
				<tr>
					<th>MRP</th>
					<td>
						<input type="text" name="itmrate" id="itmrate" value="" readonly="readonly" class="imp intext"/>
					</td>
				</tr>
				
				<tr>
					<th>Sale Qnty.  </th>
					<td>
						<input type="text" name="txtsaleqnty" id="txtsaleqnty"  autocomplete="off" class="imp intext" onkeyup="numentry('txtsaleqnty')" readonly />
					</td>
				</tr>
				
				<tr>
					<th>Quantity  </th>
					<td>
						<input type="text" name="txtqnt" id="txtqnt"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" />
					</td>
				</tr>
				
				<tr>
					<th>Reason  </th>
					<td>
						<input type="text" name="txtreason" id="txtreason"  autocomplete="off" class="imp intext"  />
					</td>
				</tr>
				
				
				
				
				<tr>
					<td colspan="4" style="text-align:center">
						<!--<input type="button" name="button2" id="button2" value="Reset" onclick="clearr()" class="btn btn-danger" /> -->
						<input type="button" name="button2" id="button2" value="Reset" onclick="clearr()" class="btn btn-danger" />
						<input type="button" name="button3" id="button3" value="Add" onclick="add_item()" class="btn btn-default" /> 
						
						<input type="button" name="button4" id="button4" value= "Done" onclick="insert_data_final()" class="btn btn-default" disabled="disabled" />
						<input type="button" id="btn8" value="Print" class="btn btn-success" style="width:70px" onclick="print_bill()"  />
						<div id="msgg" style="display:none;position:absolute;color:#e00;font-size:22px;left:40%;"></div>
					</td>
				</tr>
			</table>
		</div>
		<!---------------------------------------------------------------------------------->
	</div>
</div>

<input type="hidden" id="chk_val1" value="" />
<input type="hidden" id="chk_val2" value="" />
	<!--modal-->
	<input type="button" data-toggle="modal" data-target="#repmodal" id="rep" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="repmodal" role="dialog" aria-labelledby="repmodalLabel" aria-hidden="true" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body" id="modal_alt" style="height:0px;">
					<div id="result">
						
					</div>
					<div id="temp_item_alt">
						
					</div>
					<div id="msgg_alt" style="display:none;position:absolute;color:#e00;font-size:22px;left:40%;"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger bootbox-close-button" data-dismiss="modal" aria-hidden="true" onclick="">Close <small>(Esc)</small></button>
			</div>
		</div>
	</div>
	<!--modal end-->
	
<script>
	$(document).ready(function()
	{
		
		$("#txtbillno").focus();
		
		
		$("#txtbillno").keyup(function(e)
		{
			if(e.keyCode==13 )
			{
				load_pat_det_chk();
				load_pat_det();
				load_item();
			}
		});
		
		$("#selectbatch").keyup(function(e)
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
					$("#txtqnt").focus();
					
				}
			}
		});
		
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				if($("#txtreason").attr("disabled"))
				{
					$("#button3").focus();
				}
				else
				{
					//$("#txtreason").focus();
					$("#button3").focus();
				}
			}
		});
		
		$("#txtreason").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			{
				if($("#button4").attr("disabled"))
				{
					//$("#button3").focus();
					$("#txtcustnm").focus();
				}
				else
				{
					//$("#button4").focus();
					$("#txtcustnm").focus();
				}
			}
		});
		
		$(document).keyup(function(e)
		{
			if(e.ctrlKey && e.keyCode==32)
			{
				$("#btn_alt").click();
			}
		});
		
		$("#repmodal").on("hidden.bs.modal", function()
		{
			$("#txtcustnm").focus();
		});
	});
	//---------------------------------------------------------------
	function return_alt()
	{
		if($("#patname").val().trim()=="")
		{
			$("#txtbillno").focus();
		}
		else
		{
			$.post("pages/item_return_ajax.php",
			{
				type:1,
			},
			function(data,status)
			{
				$("#result").html(data);
				$("#rep").click();
				$("#modal_alt").animate({"height":"400px"},"slow");
				setTimeout(function(){$("#r_doc").focus();},500);
			})
		}
	}
	function qnt_check(val,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(val!="")
			{
				if(parseInt($("#qnt").val())==0 || parseInt($("#qnt").val())<0)
				{
					$("#qnt").focus();
					$("#qnt").css('border','1px solid #FF0000');
				}
				else if(parseInt($("#qnt").val())>parseInt($("#bch_qnt").val()))
				{
					$("#qnt").focus();
					$("#qnt").css('border','1px solid #FF0000');
				}
				else
				{
					$("#btn_add").focus();
					$("#qnt").css('border','');
				}
			}
			else
			{
				$("#qnt").css('border','1px solid #FF0000');
			}
		}
		else
		{
			$("#qnt").css('border','');
		}
	}
	function add_item_alt()
	{
		if($("#doc_id").val()=="")
		{
			$("#r_doc").focus();
		}
		else if($("#hguide_id").val()=="")
		{
			$("#hguide").focus();
		}
		else if($("#qnt").val()=="")
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val())==0 || parseInt($("#qnt").val())<0)
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val())>parseInt($("#bch_qnt").val()))
		{
			$("#qnt").focus();
		}
		else
		{
			//alert("ok");
			add_item_alt_temp($("#doc_id").val(),$("#r_doc").val(),$("#hguide_id").val(),$("#qnt").val().trim(),$("#bch_mrp").val().trim(),$("#bch_gst").val().trim(),$("#bch_exp").val().trim());
			doc_v=1;
			doc_sc=0;
		}
	}
	function add_item_alt_temp(id,itm_name,bch,qnt,rate,gst_per,exp_dt)
	{
		var rt=(qnt*rate).toFixed(2);
		var tr_len_alt=$('#mytable_alt tr').length;
		var gst=0;
		var dis_per=parseFloat($("#dis_per").val().trim());
		gst_per=parseFloat(gst_per);
		gst=rt-(rt*(100/(100+gst_per)));
		gst=gst.toFixed(2);
		if(tr_len_alt==0)
		{
			var test_add="<table class='table table-condensed table-bordered' id='mytable_alt'>";
			test_add+="<tr style='background-color:#cccccc'><th>Sl No</th><th>Medicine</th><th>Batch No</th><th>Quantity</th><th>MRP</th><th>Amount</th><th style='width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr_alt "+id+bch+"'>";
			test_add+="<td>1</td>";
			test_add+="<td>"+itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id_alt' /></td>";
			test_add+="<td>"+bch+"<input type='hidden' value='"+bch+"' class='batch' /></td>";
			test_add+="<td><input type='text' value='"+qnt+"' class='qnt span1' style='padding:0px 3px;margin-bottom:0px;' onkeyup='manage_qnt(this,event)' /></td>";
			test_add+="<td>"+rate+"<input type='hidden' value='"+rate+"' class='mrp' /></td>";
			test_add+="<td><span class='rate_str'>"+rt+"</span><input type='hidden' value='"+rt+"' class='all_rate_alt' /><input type='hidden' value='"+gst_per+"' class='gst_per_alt' /></td>";
			test_add+="<td style='text-align:center;'><input type='hidden' value='"+gst+"' class='all_gst_alt' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt_alt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span></td>";
			test_add+="</tr>";
			test_add+="<tr id='new_tr_alt'><td></td><td>Discount %</td><td>"+dis_per+"</td><td colspan='2' style='text-align:right;'>Total</td><td colspan='2' id='final_rate_alt'></td></tr>";
			test_add+="</table>";
			//test_add+="<div style='display:none;text-align:center;background-color:#cccccc;font-weight:bold;font-size:15px;padding:5px' id='test_hidden_price'>Total:<span id='test_total_hidden'></span></div>";
			
			$("#temp_item_alt").html(test_add);
			//tr_len_alt++;
		
			var tot=0;
			var gst_amt=0;
			var dis_amt=0;
			var tot_ts=document.getElementsByClassName("all_rate_alt");
			var tot_gst=document.getElementsByClassName("all_gst_alt");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseFloat(tot_ts[j].value);
				gst_amt=gst_amt+parseFloat(tot_gst[j].value);
			}
			var dis_amt=((tot*dis_per)/100);
			tot=tot-dis_amt;
			//tot=Math.floor(tot);
			$("#final_rate_alt").text(tot.toFixed(2));
			//tot=Math.round(tot);
			//~ $("#total").val(tot);
			//~ $("#gst").val(gst_amt);
			//~ $("#paid").val(tot);
			//~ $("#discount").val("");
			//~ $("#adjust").val("");
			//$("#test").val("");
		}
		else
		{
			
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id_alt");
			
			for(var i=0;i<test_l.length;i++)
			{
				if(test_l[i].value==id+bch)
				{
					t_ch=1;
				}
			}
			if(t_ch)
			{

				$("#temp_item_alt").css({'opacity':'0.5'});
				$("#msgg_alt").text("ALREADY SELECTED SAME ITEM AND BATCH NO.");
				//var x=$("#temp_item").offset();
				//var w=$("#msgg").width()/2;
				//$("#msgg").css({'top':'50%','left':'50%'});
				$("#msgg_alt").fadeIn(500);
				setTimeout(function(){$("#msgg_alt").fadeOut(800,function(){$("#temp_item_alt").css({'opacity':'1.0'});$("#r_doc").select();$("#r_doc").focus();
				})},800);
				
			}			
			else
			{
		   
			var tr=document.createElement("tr");
			tr.setAttribute("class","all_tr_alt "+id+bch);
			var td=document.createElement("td");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var td4=document.createElement("td");
			var td5=document.createElement("td");
			var td6=document.createElement("td");
			//var tbody=document.createElement("tbody");
			var tbody="";
			
			td.innerHTML=tr_len_alt;
			td1.innerHTML=itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id_alt' />";
			td2.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='batch' />";
			td3.innerHTML="<input type='text' value='"+qnt+"' class='qnt span1' style='padding:0px 3px;margin-bottom:0px;' onkeyup='manage_qnt(this,event)' />";
			td4.innerHTML=rate+"<input type='hidden' value='"+rate+"' class='mrp' />";
			td5.innerHTML="<span class='rate_str'>"+rt+"</span><input type='hidden' value='"+rt+"' class='all_rate_alt' /><input type='hidden' value='"+gst_per+"' class='gst_per_alt' />";
			td6.innerHTML="<input type='hidden' value='"+gst+"' class='all_gst_alt' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt_alt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span>";
			td6.setAttribute("style","text-align:center;");
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			tr.appendChild(td5);
			tr.appendChild(td6);
			//tbody.appendChild(tr);
			document.getElementById("mytable_alt").appendChild(tr);
			var tot=0;
			var gst_amt=0;
			var dis_amt=0;
			var tot_ts=document.getElementsByClassName("all_rate_alt");
			var tot_gst=document.getElementsByClassName("all_gst_alt");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseFloat(tot_ts[j].value);
				gst_amt=gst_amt+parseFloat(tot_gst[j].value);
			}
			gst_amt=gst_amt.toFixed(2);
			var dis_amt=((tot*dis_per)/100);
			tot=tot-dis_amt;
			var new_tr="<tr id='new_tr_alt'><td></td><td>Discount %</td><td>"+dis_per+"</td><td colspan='2' style='text-align:right;'>Total</td><td colspan='2' id='final_rate_alt'></td></tr>";
			$("#new_tr_alt").remove();
			$('#mytable_alt tr:last').after(new_tr);
			//tot=Math.floor(tot);
			$("#final_rate_alt").text(tot.toFixed(2));
			//tot=Math.round(tot);
			//~ $("#total").val(tot);
			//~ $("#gst").val(gst_amt);
			//~ $("#paid").val(tot);
			//~ $("#discount").val("");
			//~ $("#adjust").val("");
			}
		}
		//$("#txtbillno").attr("disabled",true);
		//$("#txtreason").attr("disabled",true);
		//$("#selectbatch").val("");
		//$("#txtcntrname").val("");
		//$("#txtqnt").val("");
		//setTimeout(function(){$("#txtcustnm").val("").focus();},300);
		//alert(disc);
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr_alt:eq("+i+")").find('td:eq(0)').text(i+1);
		}
		alt_text();
		//-------------------------------------------------------------
		$("#doc_id").val('');
		$("#r_doc").val('');
		$("#hguide").val('');
		$("#hguide_id").val('');
		$("#bch_qnt").val('');
		$("#bch_mrp").val('');
		$("#bch_gst").val('');
		$("#stock").val('');
		$("#bch_exp").val('');
		$("#qnt").val('');
		//$("#ph").attr('disabled',true);
		setTimeout(function(){$("#r_doc").focus();},500);
	}
	function manage_qnt(ths,e)
	{
		var val=$(ths).val();
		if(/\D/g.test(val))
		{
			val=val.replace(/\D/g,'');
			$(ths).val(val);
		}
		if(val=="")
		{
			val=0;
		}
		else
		{
			val=parseInt(val);
		}
		
		var mrp=parseFloat($(ths).closest('tr').find('.mrp').val().trim());
		var gst_per=parseFloat($(ths).closest('tr').find('.gst_per_alt').val().trim());
		var amt=val*mrp;
		amt=amt.toFixed(2);
		
		var gst=0;
		gst=amt-(amt*(100/(100+gst_per)));
		gst=gst.toFixed(2);
		
		$(ths).closest('tr').find('.all_rate_alt').val(amt);
		$(ths).closest('tr').find('.rate_str').text(amt);
		$(ths).closest('tr').find('.all_gst_alt').val(gst);
		set_amt_alt();
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==27)
		{
			$("#uhid").focus();
		}
		if(unicode==13)
		{
			var rowIndex=$('#mytable_alt tr').index($(ths).closest('tr'));
			rowIndex++;
			//alert(rowIndex);
			if($('#mytable_alt tr:eq('+rowIndex+') td:eq(3)').find('.qnt').length>0)
			{
				$('#mytable_alt tr:eq('+rowIndex+') td:eq(3)').find('.qnt').focus().select();
			}
			else
			{
				$("#uhid").focus();
			}
		}
	}
	function set_amt_alt()
	{
		var tot=0;
		var tot_ts=document.getElementsByClassName("all_rate_alt");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseFloat(tot_ts[j].value);
		}
		var dis_per=parseFloat($("#dis_per").val().trim());
		var alt_dis=((tot*dis_per)/100);
		tot=tot-alt_dis;
		var new_tr="<tr id='new_tr_alt'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate_alt'></td></tr>";
		$("#new_tr_alt").remove();
		$('#mytable_alt tr:last').after(new_tr);
		//tot=Math.floor(tot);
		$("#final_rate_alt").text(tot.toFixed(2));
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr_alt:eq("+i+")").find('td:eq(0)').text(i+1);
		}
		alt_text();
	}
	function alt_text() // check disc
	{
		var dis_per=parseFloat($("#dis_per").val().trim());
		var ret_tot=0;
		var tot_ret_itm=document.getElementsByClassName("all_rate");
		for(var j=0;j<tot_ret_itm.length;j++)
		{
			ret_tot=ret_tot+parseFloat(tot_ret_itm[j].value);
		}
		var ret_dis=((ret_tot*dis_per)/100);
		ret_tot=ret_tot-ret_dis;
		//----------------//
		var alt_tot=0;
		var tot_alt_itm=document.getElementsByClassName("all_rate_alt");
		for(var j=0;j<tot_alt_itm.length;j++)
		{
			alt_tot=alt_tot+parseFloat(tot_alt_itm[j].value);
		}
		var alt_dis=((alt_tot*dis_per)/100);
		alt_tot=alt_tot-alt_dis;
		//----------------//
		var alt_text="";
		var alt_amt=0;
		if(ret_tot>alt_tot)
		{
			alt_amt=ret_tot-alt_tot;
			alt_amt=alt_amt.toFixed(2);
			alt_text="Refund Amount";
			$(".alt_text").parent().css({"background":"#F6E0E0"});
		}
		else if(alt_tot>ret_tot)
		{
			alt_amt=alt_tot-ret_tot;
			alt_amt=alt_amt.toFixed(2);
			alt_text="Payable Amount";
			$(".alt_text").parent().css({"background":"#DAFFE2"});
		}
		else
		{
			alt_amt="";
			alt_text="";
			$(".alt_text").parent().css({"background":""});
		}
		$("#alt_text").html(alt_text);
		$("#alt_amt").html(alt_amt);
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
						$("#uhid").focus();
					}
					else
					{
						$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
						$("#ref_doc").fadeIn(200);
						$.post("pages/sale_ajax.php",
						{
							type:typ,
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
						$("#doc"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
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
						$("#doc"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
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
					var doc_naam=docs[2].trim()
					$("#doc_info").fadeIn(200);
					doc_load(docs[1],doc_naam);
				}
			}
	}
	function doc_load(id,name)
	{
		$("#r_doc").val(name);
		$("#doc_id").val(id);
		//$("#doc_info").html("");
		$("#ref_doc").fadeOut(200);
		$("#hguide").val('');
		$("#hguide_id").val('');
		$("#bch_qnt").val('');
		$("#bch_mrp").val('');
		$("#bch_gst").val('');
		$("#stock").val('');
		$("#bch_exp").val('');
		$("#qnt").val('');
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
						$.post("pages/sale_ajax.php"	,
						{
							val:val,
							item_id:$("#doc_id").val().trim(),
							type:typ,
							ph:1,
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
						$("#hg"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
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
						$("#hg"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
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
					var mrp=docs[3].trim();
					var gst=docs[4].trim();
					var exp_dt=docs[5].trim();
					//alert(bch+"-"+qnt+"-"+mrp+"-"+exp_dt);
					hguide_load(bch,qnt,mrp,gst,exp_dt);
					$("#hguide_info").fadeIn(200);
					
				}
			}
	}
	function hguide_load(bch,qnt,mrp,gst,exp_dt)
	{
		$("#hguide").val(bch);
		$("#hguide_id").val(bch);
		$("#bch_qnt").val(qnt);
		$("#stock").val(qnt);
		$("#bch_mrp").val(mrp);
		$("#bch_gst").val(gst);
		$("#bch_exp").val(exp_dt);
		$("#hguide_info").html("");
		$("#hguide_div").fadeOut(200);
		$("#qnt").focus();
		doc_tr=1;
		doc_sc=0;
	}
	//-----------------------------------------end-----------------------------------//
	//---------------------------------------------------------------
	function val_load_qnty()
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			itm:$("#txtcid").val(),
			type:"val_load_sell_qnty",
			billno:$("#txtbillno").val(),
			btchno:$("#selectbatch").val(),
		},
		function(data,status)
		{
			//alert(data);
			var val=data.split("@");
			
			$("#txtsaleqnty").val(val[3]);		
						
		})
	}
	//---------------------------------------------------------------
	var doc_v=1;
	var doc_sc=0;
	 function sel_pr(val,e) ///for load patient
	 {
			
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				var chk=$("#chk").val();
				if(chk!="0")
				{
					var prod=document.getElementById("prod"+doc_v).innerHTML;
					$("#rad_test"+doc_v).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
					val_load_new(prod);
				}
			}
			else if(unicode==40)
			{
				$("#chk").val("1");
				var chk=doc_v+1;
				var cc=document.getElementById("rad_test"+chk).innerHTML;
				if(cc)
				{
					doc_v=doc_v+1;
					$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'red','transform':'scale(0.95)','transition':'all .2s'});
					var doc_v1=doc_v-1;
					$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
					var z2=doc_v%3;
					if(z2==0)
					{
						$("#res").scrollTop(doc_sc)
						doc_sc=doc_sc+90;
					}
				}	
				
			}
			else if(unicode==38)
			{
				$("#chk").val("1");
				var chk=doc_v-1;
				var cc=document.getElementById("rad_test"+chk).innerHTML;
				if(cc)
				{
					doc_v=doc_v-1;
					$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'red','transform':'scale(0.95)','transition':'all .2s'});
					var doc_v1=doc_v+1;
					$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
					var z2=doc_v%3;
					if(z2==0)
					{
						doc_sc=doc_sc-90;					
						$("#res").scrollTop(doc_sc)
					}
				}	
		
			}
			else if(unicode==27)
			{
				$("#button4").focus();
			}
			else
			{
				$.post("pages/ph_load_data_ajax.php",
				{
					val:val,
					type:"load_return_item",
					billno:$("#txtbillno").val(),
				},
				function(data,status)
				{
					//alert(data);
					$("#res").html(data);
				})
			}
	}
	
	function add_item()
	{
		var jj=1;
		var blno=$("#txtbillno").val().trim();
		if(blno=="")
		{
			//alert("Please enter the patient UHID no..");
			jj=0;
			$("#txtbillno").focus();
			return true;
		}
		
		
		if($("#patname").val()=="")
		{
			alert("Please enter the customer name..");
			jj=0;
		}
		
		if($("#selectbatch").val()==0)
		{
			//alert("Please select batch name..");
			$("#selectbatch").focus();
			jj=0;
			return true;
		}
		
		if($("#txtcid").val()=="")
		{
			//document.getElementById("txtcid").placeholder="Cannot Blank";
			//document.getElementById("txtcntrname").placeholder="Cannot Blank";
			$("#txtcid").focus();
			//$("#txtcntrname").focus();
			jj=0;
			return true;
		}
		
		if($("#txtqnt").val().trim()=="")
		{
			//document.getElementById("txtqnt").placeholder="Cannot Blank";
			$("#txtqnt").focus();
			jj=0;
			return true;
		}
		
		 var slqnt=parseInt(document.getElementById("txtsaleqnty").value);
		 var rtrnqnt=parseInt(document.getElementById("txtqnt").value);
		 var stkqnt=slqnt-rtrnqnt;
		 
		 if(stkqnt<0)
		 {
			 jj=0;
			 alert("Return Quantity can not be greater than Sale Quantity..");
			 $("#txtqnt").focus();
		 }
		 
		 
		if($("#txtreason").val().trim()=="")
		{
		   //document.getElementById("txtreason").placeholder="Cannot Blank";
		   $("#txtreason").focus();
		   jj=0;
		   return true;
		}
		 
		///////end ////////
		if(jj==1)
		{
			//alert();
			$("#button4").attr("disabled",false);
			add_item_temp($("#txtcid").val(),$("#txtcntrname").val(),$("#selectbatch").val(),$("#txtqnt").val().trim(),$("#itmrate").val().trim());
			doc_v=1;
			doc_sc=0;
		}
	}
	
	function add_item_temp(id,itm_name,bch,qnt,rate)
	{
		//var disc=parseInt($('#disc').val().trim());
		var rt=(qnt*rate).toFixed(2);
		var tr_len=$('#mytable tr').length;
		if(tr_len==0)
		{
			var dis_per=parseFloat($("#dis_per").val().trim());
			var test_add="<table class='table table-condensed table-bordered' style='style:none' id='mytable'>";
			test_add+="<tr><th style='background-color:#cccccc'>Sl No</th><th style='background-color:#cccccc'>Medicine</th><th style='background-color:#cccccc'>Batch No</th><th style='background-color:#cccccc'>Quantity</th><th style='background-color:#cccccc'>MRP</th><th style='background-color:#cccccc'>Rate</th><th style='background-color:#cccccc;width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr'><td>1</td><td>"+itm_name+"<input type='hidden' value='"+id+"' class=''/><input type='hidden' value='"+id+bch+"' class='test_id'/></td><td>"+bch+"<input type='hidden' value='"+bch+"' /></td><td>"+qnt+"<input type='hidden' value='"+qnt+"' /></td><td>"+rate+"</td><td>"+rt+"<input type='hidden' value='"+rt+"' class='all_rate' /></td><td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td></tr>";
			test_add+="<tr id='new_tr'><td></td><td>Discount %</td><td>"+dis_per+"</td><td colspan='2' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
			test_add+="</table>";
			//test_add+="<div style='display:none;text-align:center;background-color:#cccccc;font-weight:bold;font-size:15px;padding:5px' id='test_hidden_price'>Total:<span id='test_total_hidden'></span></div>";
			
			$("#temp_item").html(test_add);
			tr_len++;
		
			var tot=0;
			var dis_amt=0;
			var tot_ts=document.getElementsByClassName("all_rate");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseFloat(tot_ts[j].value);
			}
			var dis_amt=((tot*dis_per)/100);
			tot=tot-dis_amt;
			tot=Math.round(tot);
			$("#final_rate").text(tot.toFixed(2));
			//$("#test").val("");
		}
		else
		{
			
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id");
			
			for(var i=0;i<test_l.length;i++)
			{
				if(test_l[i].value==id+bch)
				{
					t_ch=1;
				}
			}
			if(t_ch)
			{

				$("#temp_item").css({'opacity':'0.5'});
				$("#msgg").text("Already Selected same item and batch no.");
				//var x=$("#temp_item").offset();
				//var w=$("#msgg").width()/2;
				//$("#msgg").css({'top':'50%','left':'50%'});
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#temp_item").css({'opacity':'1.0'});
				})},800);
				
			}			
			else
			{
		   
			var tr=document.createElement("tr");
			tr.setAttribute("class","all_tr");
			var td=document.createElement("td");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var td4=document.createElement("td");
			var td5=document.createElement("td");
			var td6=document.createElement("td");
			var tbody=document.createElement("tbody");
			
			td.innerHTML=tr_len;
			td1.innerHTML=itm_name+"<input type='hidden' value='"+id+"' class=''/><input type='hidden' value='"+id+bch+"' class='test_id'/>";
			td2.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='itm_bch'/>";
			td3.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' />";
			td4.innerHTML=rate;
			td5.innerHTML=rt+"<input type='hidden' value='"+rt+"' class='all_rate' />";
			td6.innerHTML="<span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span>";
			td6.setAttribute("style","text-align:center;");
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			tr.appendChild(td5);
			tr.appendChild(td6);
			tbody.appendChild(tr);		
			document.getElementById("mytable").appendChild(tbody);
			var tot=0;
			var dis_per=parseFloat($("#dis_per").val().trim());
			var tot_ts=document.getElementsByClassName("all_rate");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseFloat(tot_ts[j].value);
			}
			var dis_amt=((tot*dis_per)/100);
			tot=tot-dis_amt;
			var new_tr="<tr id='new_tr'><td></td><td>Discount %</td><td>"+dis_per+"</td><td colspan='2' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
			$("#new_tr").remove();
			$('#mytable tr:last').after(new_tr);
			tot=Math.round(tot);
			$("#final_rate").text(tot.toFixed(2));
			}
		}
		$("#txtbillno").attr("disabled",true);
		$("#txtreason").attr("disabled",true);
		$("#selectbatch").val("");
		$("#txtcntrname").val("");
		$("#txtqnt").val("");
		setTimeout(function(){$("#txtcustnm").val("").focus();},300);
		//alert(disc);
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
		alt_text();
	}
	function set_amt()
	{
		var tot=0;
		var dis_per=parseFloat($("#dis_per").val().trim());
		var tot_ts=document.getElementsByClassName("all_rate");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseFloat(tot_ts[j].value);
		}
		var dis_amt=((tot*dis_per)/100);
		tot=tot-dis_amt;
		var new_tr="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
		$("#new_tr").remove();
		$('#mytable tr:last').after(new_tr);
		tot=Math.round(tot);
		$("#final_rate").text(tot.toFixed(2));
		$("#txtcustnm").focus();
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
		alt_text();
	}
	
	function load_pat_det()
	{
		var jj=1;
		
		if($("#chkid").val()==1)
		{
			alert("Already Items are Return against this Bill..");
			//jj=0;
			jj=1;
		}
		
		if(jj==1)
		{
			$.post("pages/ph_load_display_ajax.php",
			{
				type:"load_pat_det",
				billno:$("#txtbillno").val(),
			},
			function(data,status)
			{
				//alert(data);
				var vl=data.split("@@@");
				$("#bill_amt").val(vl[0]);
				$("#patname").val(vl[1]);
				$("#txtpaid").val(vl[2]);
				$("#txtdis").val(vl[3]);
				$("#balance").val(vl[4]);
				$("#dis_per").val(vl[5]);
				if(data!="")
				{
					//$("#txtcustnm").focus();
					$("#txtreason").focus();
				}
				else
				{
					//$("#txtcustnm").focus();
					$("#txtreason").focus();
				}
				$("#btn_alt").attr("disabled",false);
			})
		}
	}
	
	function load_pat_det_chk()
	{
		//alert();
		$.post("pages/ph_load_display_ajax.php",
		{
			type:"load_pat_det_chk",
			billno:$("#txtbillno").val(),
		},
		function(data,status)
		{
			
			
			if(data==1)
			{
				alert("Already Items are Return against this Bill..");
				load_pat_det();
				$("#txtbillno").focus();
			}
		})
	}
	
	function load_item()
	{
		$.post("pages/ph_load_data_ajax.php",
		{
			type:"load_return_item",
			billno:$("#txtbillno").val(),
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function numentry(id) ///only numeric value entry
	{
		num=document.getElementById(id);
		numx=/^[0-9]+$/;
		if(!num.value.match(numx))
		  {
			num.value="";
		 }
	}
	
	function val_load_new(itm)
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			itm:itm,
			type:"load_quant_return_item",
			billno:$("#txtbillno").val(),
		},
		function(data,status)
		{
			//alert(data);
			var val=data.split("@");
			$("#txtcid").val(val[0]);
			$("#txtcntrname").val(val[1]);
			$("#itmrate").val(val[2]);
			$("#txtsaleqnty").val(val[3]);		
			$("#selectbatch").focus();
			lod_batchno();
		})
	}
	
	function lod_batchno()
	{
		$.post("pages/ph_load_display_ajax.php",
		{
			type:"lod_batchno",
			itm:$("#txtcid").val(),
			billno:$("#txtbillno").val(),
		},
		function(data,status)
		{
			$("#chk").val("0");
			document.getElementById("selectbatch").options.length=1;
			var data=data.split("#");
			for(var i=0;i<data.length-1;i++)
			{
				var opt = document.createElement("option");
				var ip=document.getElementById("selectbatch").options.add(opt);
				var dvalue=data[i].split("@");
				for(var j=0;j<dvalue.length;j++)
				{
					opt.value=dvalue[0];
					opt.text=dvalue[1];
				}
			}
		})
	}
	function insert_data_final()
	{
		var len=$(".all_tr").length;
		var all="";
		for(var i=0; i<len; i++)
		{
			//all+=$("#mytable")
			all+=$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val()+"@@#%#";
		}
		//alert(all);
		var len_alt=$(".all_tr_alt").length;
		var all_alt="";
		for(var i=0; i<len_alt; i++) // for new items
		{
			var itm=$(".all_tr_alt:eq("+i+")").find('td:eq(1) input:first').val();
			var bch=$(".all_tr_alt:eq("+i+")").find('td:eq(2) input:first').val();
			var qnt=$(".all_tr_alt:eq("+i+")").find('td:eq(3) input:first').val();
			var mrp=$(".all_tr_alt:eq("+i+")").find('td:eq(4) input:first').val();
			var amt=$(".all_tr_alt:eq("+i+")").find('td:eq(5) input:first').val();
			var gst_per=$(".all_tr_alt:eq("+i+")").find('td:eq(5) input:last').val();
			var gst_amt=$(".all_tr_alt:eq("+i+")").find('td:eq(6) input:first').val();
			var expdt=$(".all_tr_alt:eq("+i+")").find('td:eq(6) input:last').val();
			all_alt+=itm+"@@"+bch+"@@"+qnt+"@@"+mrp+"@@"+amt+"@@"+gst_per+"@@"+gst_amt+"@@"+expdt+"@@#%#";
		}
		if(all=="")
		{
			alert("No return item selected");
		}
		else
		{
			$("#button3").attr("disabled",true);
			$("#button4").attr("disabled",true);
			$.post("pages/ph_insert_data.php",
			{
				type:"itemreturn",
				all:all,
				all_alt:all_alt,
				billno:$("#txtbillno").val(),
				rtndate:$("#txtdate").val(),
				reason:$("#txtreason").val(),
				disc:$("#disc").val(),
				ref_amt:$("#final_rate").text().trim(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				var vl=data.split("@penguin@");
				if(vl[0]>0)
				{
					$("#counter").val(vl[0]);
					alert("Done");
					for(var i=0; i<len_alt; i++)
					{
						$(".all_tr_alt:eq("+i+")").find('td:eq(6) span:first').html("");
					}
					$("#btn8").focus();
					/*bootbox.dialog({ message: data});
					setTimeout(function()
					{
						bootbox.hideAll();
						clearr();
					},1000);*/
				}
				else
				{
					var less=vl[1];
					var les=less.split("#%#");
					for(var j=0; j<(les.length); j++)
					{
						if(les[j]!="")
						{
							var ls=les[j];
							var vl=ls.split("@@");
							var itm=vl[0];
							var bch=vl[1];
							if(itm!="" && bch!="")
							{
								$(".all_tr_alt ."+itm+bch).css("background","#FFD0D0");
							}
							$("#rep").click();
						}
					}
				}
			})
		}
	}
	
	
	
	function clearr()
	{
		$("#txtbillno").val('');
		$("#patname").val('');
		$("#selectbatch").val('0');
		$("#txtcid").val('');
		$("#txtcntrname").val('');
		$("#txtqnt").val('');
		//$("#txtsaleqnty").val('');
		$("#txtreason").val('');
		$("#temp_item").empty();
		$("#txtbillno").attr("disabled",false);
		$("#txtreason").attr("disabled",false);
		$("#button4").attr("disabled",true);
		$("#txtbillno").focus();
	}
	
	function print_bill()
	{
		if($("#txtbillno").val()=="0")
		{
			alert("Please enter the bill no..");
			
		}
		else
		{
			var billno=$("#txtbillno").val().trim();
			var cnt=$("#counter").val().trim();
			//url="pages/sale_bill_print.php?billno="+billno;
			//url="pages/item_rertn_zbra_rpt.php?billno="+billno+"&counter="+cnt;
			url="pages/print_return_bill.php?billno="+billno+"&counter="+cnt;
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
			$("#btn8").attr("disabled", true);
			setTimeout("location.reload(true);",1000);
		}
	}
</script>
