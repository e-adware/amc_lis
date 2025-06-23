<?php
$ord=base64_decode($_GET['val']);
$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_substore_indent_order_master` WHERE `order_no`='$ord'"));
$dept=mysqli_fetch_assoc(mysqli_query($link,"SELECT `substore_name` FROM `inv_sub_store` WHERE `substore_id`='$det[substore_id]'"));
$u=mysqli_fetch_assoc(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$det[user]'"));
?>
<!--header-->
<div id="content-header">
    <div class="header_div">
		<span class="header">Indent Approved</span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<input type="hidden" id="ordno" value="<?php echo $ord;?>" />
	<input type="hidden" id="chk_val1" value="0" />
	<input type="hidden" id="chk_val2" value="0" />
	<input type="hidden" id="issue_no" value="" />
	<div>
		<table class="table table-condensed table-report" style="margin-bottom:0px;">
			<tr><td colspan="5"></td></tr>
			<tr>
				<td>
					<b>Request No</b><br/>
					<?php echo $ord;?>
				</td>
				<td>
					<b>Department</b><br/>
					<?php echo $dept['substore_name'];?>
				</td>
				<td>
					<b>Order Date</b><br/>
					<?php echo convert_date_g($det['order_date']);?>
				</td>
				<td>
					<b>Order Time</b><br/>
					<?php echo date("h:i A", strtotime($det['time']));?>
				</td>
				<td>
					<b>User</b><br/>
					<?php echo $u['name'];?>
				</td>
			</tr>
		</table>
		<table class="table table-condensed table-report">
			<tr><td colspan="6"></td></tr>
			<tr>
				<td>
					<b>Issue Date : <?php echo convert_date_g($date);?></b>
				</td>
				<th>Issue To</th>
				<td colspan="2">
					<input type="text" id="issueto" class="span3" value="<?php echo $dept['substore_name'];?> INCHARGE" placeholder="Issue To" autofocus />
				</td>
				<th>Issue No</th>
				<td>
					<input type="text" id="issue_num" style="width:80px;" placeholder="Issue No" />
				</td>
			</tr>
			
			<tr style="display:none;">
				<td>
					<b>Issue Type : <select class="span2" id="issue_typ" onchange="" onkeyup="next_tab(this.id,event)">
					<option value="0">Issue Type</option>
					<option value="2">60 BD</option>
					<option value="3">SON</option>
					<option value="4">Hospital a/c</option>
					
				</select>
				</td>
				
				<th>Bed No</th>
				<td colspan="2">
					<input type="text" id="bed_no" class="span2" placeholder="Bed No" />
				</td>
				<th>&nbsp;</th>
				<td>
					&nbsp;
				</td>
			</tr>
			
			<tr>
				<td>
					<b>Item Name</b><br/>
					<input type="text" id="r_doc" class="span5" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="$('#ref_doc').fadeOut(500)" value="" placeholder="Item Name" />
					<input type="text" id="doc_id" style="display:none;" value="">
					<div id="doc_info"></div>
					<div id="ref_doc" align="center" style="padding:8px;width:600px;">
						
					</div>
				</td>
				<td>
					<b>Order</b><br/>
					<input type="text" id="ord_qnt" style="width:80px;" disabled />
				</td>
				<td>
					<b>Batch No</b><br/>
					<input type="text" id="hguide" class="span2" onFocus="hguide_up(this.value,event,'batch')" onKeyUp="hguide_up(this.value,event,'batch')" onBlur="$('#hguide_div').fadeOut(500)" placeholder="Batch No" />
					<input type="text" name="hguide_id" id="hguide_id" style="display:none;" placeholder="Batch No" />
					<input type="text" name="bch_qnt" id="bch_qnt" style="display:none;" placeholder="Batch Quantity" />
					<input type="text" name="bch_mrp" id="bch_mrp" style="display:none;" placeholder="Batch MRP" />
					<input type="text" name="bch_gst" id="bch_gst" style="display:none;" placeholder="Batch GST" />
					<input type="text" name="bch_exp" id="bch_exp" style="display:none;" placeholder="Batch Exp Date" />
					<div id="hguide_info"></div>
					<div id="hguide_div" align="center" style="padding:8px;width:400px;"></div>
				</td>
				<td>
					<b>Stock</b><br/>
					<input type="text" id="stock" style="width:80px;" disabled />
				</td>
				<td>
					<b>Quantity</b><br/>
					<input type="text" id="qnt" onkeyup="manage_qnt(this,event)" style="width:80px;" />
				</td>
				<td>
					<br/><button type="button" id="btn_add" class="btn btn-info" onclick="add_item()">Add</button>
				</td>
			</tr>
		</table>
	</div>
	<div id="msgg" style="display:none;background:#FFFFFF;position:fixed;color:#990909;font-size:22px;left:35%;top: 20%;padding: 25px;border-radius: 3px;box-shadow: 0px 1px 15px 1px #c57676;z-index:1000;"></div>
	<div id="temp_item" style="height:200px;max-height:300px;overflow-y:scroll;">
	
	</div>
	<div style="text-align:center;">
		<button type="button" class="btn btn-success" id="btn_save" onclick="save()" disabled>Done</button>
		<button type="button" class="btn btn-primary" id="btn_print" style="display:none;" onclick="bill_print()">Print</button>
		<button type="button" class="btn btn-danger" id="btn_canc" onclick="go_back()">Go Back</button>
	</div>
	<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function()
	{
		//$("#fdate").datepicker({dateFormat: 'yy-mm-dd',maxDate: '0',});
		//$("#tdate").datepicker({dateFormat: 'yy-mm-dd',maxDate: '0',});
		//load_data();
		$("#issueto").keyup(function(e)
		{
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13 && $(this).val().trim()!="")
			{
				$("#issue_num").focus();
			}
		});
		$("#issue_num").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#r_doc").focus();
			}
		});
	});
	function bill_print()
	{
		alert($("#issue_no").val().trim());
	}
	function go_back()
	{
		window.location="index.php?param="+btoa(180);
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
		var chk=0;
		var stk=parseInt($("#bch_qnt").val().trim());
		var ord_qnt=parseInt($("#ord_qnt").val().trim());
		if(val>stk)
		{
			$(ths).css({"border":"1px solid #C70000","box-shadow":"0px 0px 6px 0px #E71818"});
			chk=1;
		}
		else if(val>ord_qnt)
		{
			$(ths).css({"border":"1px solid #C70000","box-shadow":"0px 0px 6px 0px #E71818"});
			chk=1;
		}
		else
		{
			$(ths).css({"border":"","box-shadow":""});
			chk=0;
		}
		if(e.keyCode==13)
		{
			if(chk==0 && val!=0)
			{
				$("#btn_add").focus();
			}
		}
	}
	function add_item()
	{
		if($("#issueto").val().trim()=="")
		{
			$("#issueto").focus();
		}
		else if($("#doc_id").val().trim()=="")
		{
			$("#r_doc").focus();
		}
		else if($("#hguide_id").val().trim()=="")
		{
			$("#hguide").focus();
		}
		else if($("#qnt").val().trim()=="")
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val())==0)
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val()) > parseInt($("#bch_qnt").val()))
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val()) > parseInt($("#ord_qnt").val()))
		{
			$("#qnt").focus();
		}
		else
		{
			$("#btn_save").attr("disabled",false);
			$("#issueto").attr("disabled",true);
			add_item_temp($("#doc_id").val(),$("#r_doc").val(),$("#hguide_id").val(),$("#qnt").val().trim(),$("#bch_mrp").val().trim(),$("#bch_gst").val().trim(),$("#bch_exp").val().trim());
			doc_v=1;
			doc_sc=0;
		}
	}
	function add_item_temp(id,itm_name,bch,qnt,rate,gst_per,exp_dt)
	{
		//alert(id);
		var rt=(qnt*rate).toFixed(2);
		var tr_len=$('#mytable tr').length;
		var gst=0;
		gst_per=parseFloat(gst_per);
		gst=rt-(rt*(100/(100+gst_per)));
		gst=gst.toFixed(2);
		if(tr_len==0)
		{
			var test_add="<table class='table table-condensed table-bordered' id='mytable'>";
			test_add+="<tr style='background-color:#cccccc'><th>Sl No</th><th>Medicine</th><th>Batch No</th><th>Quantity</th><th>MRP</th><th>Amount</th><th style='width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr "+id+bch+"'>";
			test_add+="<td>1</td>";
			test_add+="<td>"+itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' /></td>";
			test_add+="<td>"+bch+"<input type='hidden' value='"+bch+"' class='batch' /></td>";
			test_add+="<td>"+qnt+"<input type='hidden' value='"+qnt+"' class='qnt span1' style='padding:0px 3px;margin-bottom:0px;' onkeyup='manage_qnt(this,event)' /></td>";
			test_add+="<td>"+rate+"<input type='hidden' value='"+rate+"' class='mrp' /></td>";
			test_add+="<td><span class='rate_str'>"+rt+"</span><input type='hidden' value='"+rt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' /></td>";
			test_add+="<td style='text-align:center;'><input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span></td>";
			test_add+="</tr>";
			test_add+="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
			test_add+="</table>";
			
			$("#temp_item").html(test_add);
			tr_len++;
		
			var tot=0;
			var gst_amt=0;
			var dis_amt=0;
			var tot_ts=document.getElementsByClassName("all_rate");
			var tot_gst=document.getElementsByClassName("all_gst");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseFloat(tot_ts[j].value);
				gst_amt=gst_amt+parseFloat(tot_gst[j].value);
			}
			dis_amt=0;
			tot=tot-dis_amt;
			$("#final_rate").text(tot.toFixed(2));
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
				$("#msgg").text("ALREADY SELECTED SAME ITEM AND BATCH NO.");
				//var x=$("#temp_item").offset();
				//var w=$("#msgg").width()/2;
				//$("#msgg").css({'top':'50%','left':'50%'});
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(800,function(){$("#temp_item").css({'opacity':'1.0'});$("#r_doc").select();$("#r_doc").focus();
				})},800);
				
			}			
			else
			{
		   
			var tr=document.createElement("tr");
			tr.setAttribute("class","all_tr "+id+bch);
			var td=document.createElement("td");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var td4=document.createElement("td");
			var td5=document.createElement("td");
			var td6=document.createElement("td");
			//var tbody=document.createElement("tbody");
			var tbody="";
			
			td.innerHTML=tr_len;
			td1.innerHTML=itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' />";
			td2.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='batch' />";
			td3.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' class='qnt span1' style='padding:0px 3px;margin-bottom:0px;' onkeyup='manage_qnt(this,event)' />";
			td4.innerHTML=rate+"<input type='hidden' value='"+rate+"' class='mrp' />";
			td5.innerHTML="<span class='rate_str'>"+rt+"</span><input type='hidden' value='"+rt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' />";
			td6.innerHTML="<input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span>";
			td6.setAttribute("style","text-align:center;");
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			tr.appendChild(td5);
			tr.appendChild(td6);
			//tbody.appendChild(tr);
			document.getElementById("mytable").appendChild(tr);
			var tot=0;
			var gst_amt=0;
			var dis_amt=0;
			var tot_ts=document.getElementsByClassName("all_rate");
			var tot_gst=document.getElementsByClassName("all_gst");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseFloat(tot_ts[j].value);
				gst_amt=gst_amt+parseFloat(tot_gst[j].value);
			}
			gst_amt=gst_amt.toFixed(2);
			dis_amt=0;
			tot=tot-dis_amt;
			var new_tr="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
			$("#new_tr").remove();
			$('#mytable tr:last').after(new_tr);
			//tot=Math.floor(tot);
			$("#final_rate").text(tot.toFixed(2));
			}
		}
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
		//-------------------------------------------------------------
		$("#doc_id").val('');
		$("#r_doc").val('');
		$("#hguide").val('');
		$("#hguide_id").val('');
		$("#bch_qnt").val('');
		$("#bch_mrp").val('');
		$("#bch_gst").val('');
		$("#stock").val('');
		$("#ord_qnt").val('');
		$("#bch_exp").val('');
		$("#qnt").val('');
		$("#ph").attr('disabled',true);
		setTimeout(function(){$("#r_doc").focus();},500);
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
		var new_tr="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
		$("#new_tr").remove();
		$('#mytable tr:last').after(new_tr);
		
		$("#final_rate").text(tot.toFixed(2));
		tot=Math.round(tot);
		$("#total").val(tot);
		$("#gst").val(gst_amt);
		$("#paid").val(tot);
		$("#discount").val("");
		$("#adjust").val("");
		$("#txtcustnm").focus();
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
	}
	function save()
	{
		var len=$(".all_tr").length;
		if(len<1)
		{
			$("html,body").animate({scrollTop: '10px'},500);
			$("#msgg").text("NO ITEM SELECTED");
			$("#msgg").fadeIn(500);
			setTimeout(function()
			{
				$("#msgg").fadeOut(800,function()
				{
					$("#r_doc").select();$("#r_doc").focus();
				}
			)},800);
		}
		
		//~ else if(parseFloat($("#final_rate").text())==0)
		//~ {
			//~ $("html,body").animate({scrollTop: '10px'},500);
			//~ $("#msgg").text("TOTAL AMOUNT CANNOT BE ZERO");
			//~ $("#msgg").fadeIn(500);
			//~ setTimeout(function()
			//~ {
				//~ $("#msgg").fadeOut(800,function()
				//~ {
					//~ $("#r_doc").select();$("#r_doc").focus();
				//~ }
			//~ )},800);
		//~ }
			//alert(len);
		else
		{
			$("#btn_add").attr("disabled",true);
			$("#btn_save").attr("disabled",true);
			$("#loader").show();
			var all="";
			for(var i=0; i<len; i++)
			{
				var itm=$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val();
				var bch=$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val();
				var qnt=$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val();
				var mrp=$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val();
				var amt=$(".all_tr:eq("+i+")").find('td:eq(5) input:first').val();
				var gst_per=$(".all_tr:eq("+i+")").find('td:eq(5) input:last').val();
				var gst_amt=$(".all_tr:eq("+i+")").find('td:eq(6) input:first').val();
				var expdt=$(".all_tr:eq("+i+")").find('td:eq(6) input:last').val();
				all+=itm+"@@"+bch+"@@"+qnt+"@@"+mrp+"@@"+amt+"@@"+gst_per+"@@"+gst_amt+"@@"+expdt+"@@#%#";
			}
			//alert(all);
			$.post("pages/inv_indent_approve_ajax.php",
			{
				type:4,
				ordno:$("#ordno").val().trim(),
				issueto:$("#issueto").val().trim(),
				issue_num:$("#issue_num").val().trim(),
				issue_typ:$("#issue_typ").val(),
				bed_no:$("#bed_no").val().trim(),
				user:$("#user").text().trim(),
				all:all,
			},
			function(data,status)
			{
				
				$("#loader").hide();
				var vl=data.split("#@#");
				if(vl[0]=="1")
				{
					$("#issue_no").val((vl[1]).trim());
					alert("Done");
					//$("#btn_print").show();
					//$("#btn_print").focus();
				}
				if(vl[0]=="0")
				{
					alert("Error");
					$("#btn_add").attr("disabled",false);
					$("#btn_save").attr("disabled",false);
				}
			})
		}
	 }
	//------------------------item search---------------------------------//
	function load_refdoc1()
	{
			load_refdoc('','')
			//$("#ref_doc").fadeIn(200);
			//$("#hguide").select();
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
						$("#btn_save").focus();
					}
					else
					{
						$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
						$("#ref_doc").fadeIn(200);
						$.post("pages/inv_indent_approve_ajax.php",
						{
							type:2,
							ordno:$("#ordno").val().trim(),
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
					var doc_id=docs[1].trim();
					var doc_naam=docs[2].trim();
					var ord_qnt=docs[3].trim();
					$("#doc_info").fadeIn(200);
					doc_load(doc_id,doc_naam,ord_qnt);
				}
			}
	}
	function doc_load(id,name,ord_qnt)
	{
		//alert(gst_pr);
		$("#r_doc").val(name);
		$("#doc_id").val(id);
		$("#ord_qnt").val(ord_qnt);
		//$("#doc_info").html("");
		$("#ref_doc").fadeOut(200);
		$("#hguide").val('');
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
						$("#btn_save").focus();
					}
					else
					{
						$("#hguide_div").html("<img src='../images/ajax-loader.gif' />");
						$("#hguide_div").fadeIn(200);
						$.post("pages/inv_indent_approve_ajax.php",
						{
							val:val,
							item_id:$("#doc_id").val().trim(),
							type:3,
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
</script>
<style>
.table-report
{
	background:#FFFFFF;
}
.table-report tr:hover
{
	background:none;
}
</style>
