<?php
$user_access=array(101,102,103,154);
$calc_table="none";
if(in_array($p_info['emp_id'],$user_access))
{
	$calc_table="inline-block";
}
?>
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<input type="hidden" id="chk_val1" value="0" />
	<input type="hidden" id="chk_val2" value="0" />
	<input type="hidden" id="ino" value="0" />
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Select</th>
			<td colspan="3">
				<select id="sub_dept" class="span5" onblur="load_temp_item()" autofocus>
					<option value="0">Select</option>
					<?php
						$ids="1,43,44,45,46,3";
						$qsplr=mysqli_query($link,"select substore_id,substore_name from inv_sub_store where substore_id in ($ids) order by substore_name");
						while($qsplr1=mysqli_fetch_array($qsplr))
						{
					?>
						<option value="<?php echo $qsplr1['substore_id'];?>"><?php echo $qsplr1['substore_name'];?></option>
					<?php
						}
					?>
				</select>
				<div style="display:<?php echo $calc_table;?>;width:200px;position:absolute;right:2px;border:1px solid #AAA;">
					<table class="table table-condensed" style="margin-bottom:0px;">
						<tr>
							<th>GST %</th><td id="gst_str">0.00</td>
						</tr>
						<tr>
							<th>Discount %</th><td id="dis_str">0.00</td>
						</tr>
						<tr>
							<th>Supp Rate</th><td id="rate_str">0.00</td>
						</tr>
						<tr>
							<th>MRP</th><td id="mrp_str">0.00</td>
						</tr>
						<tr>
							<th>Orig Rate (+GST)</th><td id="orate_str">0.00</td>
						</tr>
						<tr>
							<th>P Sale %</th><td id="disp_str">0.00</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr style="display:none;">
			<th>Issue To</th>
			<td>
				<input type="text" id="issue_to" class="span3" placeholder="Issue To" />
			</td>
			<th>Issue No</th>
			<td>
				<input type="text" id="issue_no" class="span3" placeholder="Issue No" />
			</td>
		</tr>
		<tr>
		   <th>Item Name</th>
			<td colspan="3">
				<input type="text" id="r_doc" class="span5" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onblur="$('#ref_doc').fadeOut(200)" value="" placeholder="Item Name" />
				<input type="text" id="doc_id" style="display:none;" />
				<input type="text" id="gst" style="display:none;" />
				<div id="doc_info"></div>
				<div id="ref_doc" align="center" style="padding:8px;width:600px;display:none;">
					
				</div>
			</td>
		</tr>
		<tr>
			<th>Batch No</th>
			<td>
				<select id="batch_no" onchange="val_load_new()" onkeyup="next_tab(this.id,event)">
					<option value="0">Select BatchNo</option>
				</select>
			</td>
			<th>Stock</th>
			<td>
				<input type="hidden" id="dis_chk" value="0" />
				<input type="hidden" id="dis_per" class="span2" onkeyup="numentry('txtqnt')" placeholder="Dis Per" disabled />
				<input type="text" id="strip_stk" class="span2" onkeyup="numentry('txtqnt')" placeholder="Stock" disabled />
				<input type="hidden" id="txtavailstk" class="span2" onkeyup="numentry('txtqnt')" placeholder="Avail Stock" disabled />
			</td>
		</tr>
		<tr>
			<th>MRP</th>
			<td>
				<input type="text" id="txtmrp"  autocomplete="off" class="imp intext" onkeyup="chk_dec(this,event);next_tab(this.id,event)" placeholder="MRP" style="width:100px" />
			</td>
			<th>Pack size</th>
			<td>
				<input type="hidden" id="txtrate"  autocomplete="off" class="imp intext" onkeyup="chk_dec(this,event)" placeholder="Rate" style="width:100px" disabled />
				<input type="text" id="pkd"  autocomplete="off" class="imp intext" onkeyup="chk_num(this,event)" placeholder="Pack size" style="width:100px" />
			</td>
		</tr>
		<tr>
			<th>Issue Quantity</th>
			<td>
				<input type="text" id="txtqnt"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" placeholder="Quantity" style="width:100px" />
			</td>
			<th>Expiry</th>
			<td>
				<input type="text" id="txtexpiry" class="span2" onkeyup="check_date_format(this,this.value)" maxlength="7" placeholder="Expiry" />
			</td>
		</tr>
		
		<tr>
			<td colspan="4" style="text-align:center">
				<input type="button" name="button2" id="button2" value="Reset" onclick="reset_all()" class="btn btn-danger" /> 
				<input type="button" name="button" id="button" value= "Add" onclick="add_temp()" class="btn btn-default" />
				<input type="button" name="button4" id="button4" value="Done" onclick="save_final()" class="btn btn-default" />
				<input type="button" name="button5" id="button5" value="Print" onclick="print_list()" class="btn btn-success" disabled />
				<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/inv_indent_order_rpt.php')" class="btn btn-success" disabled />-->
			</td>
		</tr>
		
	</table>
	
	<div id="temp_item" class="vscrollbar" style="max-height:250px;overflow-y:scroll;" >
		
	</div>
</div>
<div id="msgg" style="display:none;background:#FFFFFF;position:fixed;color:#990909;font-size:22px;left:35%;top: 20%;padding: 25px;border-radius: 3px;box-shadow: 0px 1px 15px 1px #c57676;z-index:1000;"></div>
<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script> 
<script src="../js/jquery.peity.min.js"></script>

<script type="text/javascript">
	$(document).ready(function()
	{
		$("#sub_dept").keyup(function(e)
		{
			$(this).css("border","");
			//$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				{
					$(this).css("border","1px solid #f00");
					$("#sub_dept").focus();
				}
				else
				{
					$(this).css("border","");
					//$("#issue_to").focus();
					//$('#selectcategory').select2('focus');
					$("#r_doc").focus();
				}
			}
		});
		
		$("#issue_to").keyup(function(e)
		{
			if(e.keyCode==13 && $("#issue_to").val().trim()!="")
			{
				$("#issue_no").focus();
			}
		});
		$("#issue_no").keyup(function(e)
		{
			if(e.keyCode==13 && $("#issue_no").val().trim()!="")
			{
				$("#r_doc").focus();
			}
		});
		
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13 && $("#txtqnt").val().trim()!="")
			$("#txtexpiry").focus();
		});
		
		$("#txtexpiry").keyup(function(e)
		{
			if(e.keyCode==13 && $("#txtexpiry").val().trim()!="" && $("#txtexpiry").hasClass("err")==false)
			{
				$("#button").focus();
			}
		});
		$("#txtcntrname").keyup(function(e)
		{
			if(e.keyCode==27)
			$("#button4").focus();
		});
		
	});
	function print_list()
	{
		var no=$("#ino").val().trim();
		url="pages/transfer_details_print.php?iNo="+btoa(no);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function check_date_format(ths,testDate)
	{
		//var date_regex = /([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/;
		var date_regex = /([12]\d{3}-(0[1-9]|1[0-2]))/;
		if(date_regex.test(testDate)==true)
		{
			$(ths).removeClass("err");
			$(ths).css({"box-shadow":""});
		}
		else
		{
			$(ths).addClass("err");
			$(ths).css({"box-shadow":"0px 0px 10px 2px #FC5659"});
		}
	}
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
	
	function reset_all()
	{
		//~ clearr();
		//~ $("#selectsubstr").attr("disabled",false);
		//~ $("#selectsubstr").focus();
		//~ $("#tstissueto").focus();
		//~ $("#button4").attr("disabled",true);
		location.reload(true);
	}
	
	function val_load_new()
	{
		$.post("pages/ph_stock_transfer_ajax.php",
		{
			type:3,
			itmid:$("#doc_id").val().trim(),
			batchno:$("#batch_no").val(),
		},
		function(data,status)
		{
			//alert(data);
			var val=data.split("@");
			$("#txtavailstk").val(val[0]);
			$("#txtrate").val(val[1]);
			$("#txtexpiry").val(val[2]);
			$("#txtmrp").val(val[3]);
			$("#dis_per").val(val[4]);
			$("#dis_str").text(val[4]);
			if($("#batch_no").val()=="0")
			{
				$("#strip_stk").val("");
				$("#dis_str").text("0.00");
				$("#rate_str").text("0.00");
				$("#mrp_str").text("0.00");
				$("#orate_str").text("0.00");
				$("#disp_str").text("0.00");
			}
			else
			{
				var v=((parseInt($("#txtavailstk").val().trim())) / (parseInt($("#pkd").val().trim())));
				var txtrate=0;
				if($("#txtrate").val().trim()!="")
				{
					txtrate=((parseFloat($("#txtrate").val().trim())) * (parseInt($("#pkd").val().trim())));
				}
				var mrp=0;
				if($("#txtmrp").val().trim()!="")
				{
					mrp=((parseFloat($("#txtmrp").val().trim())) * (parseInt($("#pkd").val().trim())));
				}
				$("#strip_stk").val(v);
				$("#txtrate").val(txtrate);
				$("#rate_str").text(txtrate);
				$("#txtmrp").val(mrp);
				$("#mrp_str").text(mrp.toFixed(2));
				
				var rate=txtrate;
				//var mrp=parseFloat($("#txtmrp").val().trim());
				var dis_per=parseFloat($("#dis_per").val().trim());
				var gst_per=parseFloat($("#gst").val().trim());
				
				var disc=((rate*dis_per)/100);
				var d_rate=(rate-disc);
				var o_rate=(d_rate+((d_rate*gst_per)/100));
				
				var dis=(((mrp-o_rate)/mrp)*100);
				$("#orate_str").text(o_rate.toFixed(2));
				$("#disp_str").text(dis.toFixed(2));
			}
			//$("#txtqnt").focus();
		})
	}
	function chk_dec(ths,e)
	{
		var reg = /^\d+(?:\.\d{1,2})?$/;
		var val=$(ths).val();
		if(!reg.test(val))
		{
			$(ths).css("border","1px solid #FF0000");
			$(ths).addClass("err");
			return true;
		}
		else
		{
			$(ths).css("border","");
			$(ths).removeClass("err");
		}
	}
	function chk_num(ths,e)
	{
		var val=ths.value;
		if(/\D/g.test(val))
		{
			val=val.replace(/\D/g,'');
			$(ths).val(val);
		}
	}
	function next_tab(id,e)
	{
		if(id=="batch_no")
		{
			if(e.keyCode==13 && $("#"+id).val()!="0")
			{
				$("#txtmrp").focus();
			}
		}
		if(id=="txtmrp")
		{
			if(e.keyCode==13 && $("#"+id).val().trim()!="" && $("#"+id).hasClass("err")==false)
			{
				$("#txtqnt").focus();
			}
		}
		if(id=="txtqnt")
		{
			if(e.keyCode==13)
			{
				$("#button").focus();
			}
		}
	}
	function alr_msg(msg,id)
	{
		$.gritter.add(
		{
			//title:	'Normal notification',
			text:	'<h5 style="text-align:center;">'+msg+'</h5>',
			time: 1000,
			sticky: false
		});
		setTimeout(function(){$("#"+id).focus();},300);
	}
	function add_temp()
	{
		var v=((parseInt($("#txtavailstk").val().trim())) / (parseInt($("#pkd").val().trim())));
		var txtrate=0;
		if($("#txtrate").val().trim()!="")
		{
			txtrate=((parseFloat($("#txtrate").val().trim())) * (parseInt($("#pkd").val().trim())));
		}
		var mrp=0;
		if($("#txtmrp").val().trim()!="")
		{
			mrp=((parseFloat($("#txtmrp").val().trim())) * (parseInt($("#pkd").val().trim())));
		}
		
		var rate=txtrate;
		//var mrp=parseFloat($("#txtmrp").val().trim());
		var dis_per=parseFloat($("#dis_per").val().trim());
		var gst_per=parseFloat($("#gst").val().trim());
		
		var disc=((rate*dis_per)/100);
		var d_rate=(rate-disc);
		var o_rate=(d_rate+((d_rate*gst_per)/100));
		
		var dis=(((mrp-o_rate)/mrp)*100);
		
		if($("#sub_dept").val()=="0")
		{
			$("#sub_dept").focus();
		}
		//~ else if($("#issue_to").val().trim()=="")
		//~ {
			//~ $("#issue_to").focus();
		//~ }
		//~ else if($("#issue_no").val().trim()=="")
		//~ {
			//~ $("#issue_no").focus();
		//~ }
		else if($("#doc_id").val()=="")
		{
			$("#r_doc").focus();
		}
		else if($("#batch_no").val()=="0")
		{
			$("#batch_no").focus();
		}
		else if($("#txtmrp").val().trim()=="")
		{
			alr_msg("MRP cannot blank","txtmrp");
		}
		else if(parseFloat($("#txtmrp").val().trim())<=0 || $("#txtmrp").hasClass("err")==true)
		{
			alr_msg("Enter valid MRP value","txtmrp");
		}
		else if($("#pkd").val().trim()=="")
		{
			alr_msg("Pack size cannot blank","pkd");
		}
		else if(parseInt($("#pkd").val().trim())==0 || parseInt($("#pkd").val().trim())<0 || $("#pkd").hasClass("err")==true)
		{
			alr_msg("Enter valid pack size","pkd");
		}
		else if($("#txtqnt").val().trim()=="")
		{
			alr_msg("Quantity cannot blank","txtqnt");
		}
		else if(parseInt($("#txtqnt").val().trim())==0 || parseInt($("#txtqnt").val().trim())<0 || $("#txtqnt").hasClass("err")==true)
		{
			alr_msg("Enter a valid quantity","txtqnt");
		}
		//else if(parseInt($("#txtqnt").val().trim()) > parseInt($("#txtavailstk").val().trim()))
		else if(parseInt($("#txtqnt").val().trim()) > parseInt($("#strip_stk").val().trim()))
		{
			alr_msg("Quantity is greater than stock","txtqnt");
		}
		else if($("#txtexpiry").val().trim()=="" || $("#txtexpiry").hasClass("err")==true)
		{
			$("#txtexpiry").focus();
		}
		//~ else if($("#sub_dept").val()=="45" && Math.floor(v)!=Math.ceil(v))
		//~ {
			//~ //alert(v);
			//~ //alert(Math.floor(v));
			//~ //alert(Math.ceil(v));
			//~ alr_msg("Enter in packing quantity","txtqnt");
		//~ }
		else if($("#sub_dept").val()=="43" && dis < 20 && $("#dis_chk").val()=="0")
		{
			alr_msg("Less Discount","batch_no");
			setTimeout(function()
			{
				bootbox.confirm("Do you really want to proceed with this item?",
				function(result)
				{ 
					if(result)
					{
						$("#dis_chk").val("1");
						alr_msg("Ok","batch_no");
					}
				});
			},700);
		}
		else
		{
			//alert();
			$("#sub_dept").attr("disabled",true);
			$("#btn_add").attr("disabled",false);
			add_item_temp($("#doc_id").val(),$("#r_doc").val().trim(),$("#batch_no").val(),$("#txtqnt").val(),$("#txtrate").val().trim(),$("#gst").val().trim(),$("#txtexpiry").val().trim(),$("#txtmrp").val().trim());
			$("#dis_chk").val("0");
			$("#gst_str").text("0.00");
			$("#dis_str").text("0.00");
			$("#rate_str").text("0.00");
			$("#mrp_str").text("0.00");
			$("#orate_str").text("0.00");
			$("#disp_str").text("0.00");
			doc_v=1;
			doc_sc=0;
		}
	}
	function add_item_temp(id,itm_name,bch,qnt,rate,gst_per,exp_dt,mrp)
	{
		rate=parseFloat(rate);
		qnt=parseInt(qnt);
		var amt=(qnt*rate);
		var tr_len=$('#mytable tr').length;
		var gst=0;
		var pkd=$("#pkd").val().trim();
		gst_per=parseFloat(gst_per);
		gst=amt-(amt*(100/(100+gst_per)));
		//gst=gst.toFixed(2);
		var itm_rem='itm_rem(this)';
		if(tr_len==0)
		{
			var test_add="<table class='table table-condensed table-bordered' id='mytable'>";
			test_add+="<tr style='background-color:#cccccc'><th>#</th><th>Description</th><th>Batch No</th><th>Quantity</th><th>MRP</th><th>Pack size</th><th></th><th style='width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr "+id+bch+"'>";
			test_add+="<td>1</td>";
			test_add+="<td>"+itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' /></td>";
			test_add+="<td>"+bch+"<input type='hidden' value='"+bch+"' class='batch' /></td>";
			test_add+="<td>"+qnt+"<input type='hidden' value='"+qnt+"' class='qnt' /></td>";
			test_add+="<td>"+mrp+"<input type='hidden' value='"+mrp+"' class='mrp' /></td>";
			test_add+="<td>"+pkd+"<input type='hidden' value='"+rate+"' class='rate' /><input type='hidden' value='"+pkd+"' class='pkd' /></td>";
			test_add+="<td><input type='hidden' value='"+amt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' /></td>";
			test_add+="<td style='text-align:center;'><input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='"+itm_rem+";$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span></td>";
			test_add+="</tr>";
			test_add+="<tr id='new_tr' style='display:none;'><td colspan='6' style='text-align:right;'></td><td colspan='2' id='final_rate'></td></tr>";
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
			var td7=document.createElement("td");
			//var tbody=document.createElement("tbody");
			var tbody="";
			
			td.innerHTML=tr_len;
			td1.innerHTML=itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' />";
			td2.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='batch' />";
			td3.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' class='qnt' />";
			td4.innerHTML=mrp+"<input type='hidden' value='"+mrp+"' class='mrp' />";
			td5.innerHTML=pkd+"<input type='hidden' value='"+rate+"' class='rate' /><input type='hidden' value='"+pkd+"' class='pkd' />";
			td6.innerHTML="<input type='hidden' value='"+amt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' />";
			td7.innerHTML="<input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='"+itm_rem+";$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span>";
			td7.setAttribute("style","text-align:center;");
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			tr.appendChild(td5);
			tr.appendChild(td6);
			tr.appendChild(td7);
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
			dis_amt=0;
			tot=tot-dis_amt;
			var new_tr="<tr id='new_tr' style='display:none;'><td colspan='6' style='text-align:right;'></td><td colspan='2' id='final_rate'></td></tr>";
			$("#new_tr").remove();
			$('#mytable tr:last').after(new_tr);
			}
		}
		set_amt();
		insert_temp(id,bch,exp_dt,qnt,gst_per,gst,pkd,mrp,rate,amt);
	}
	function set_amt()
	{
		$("#doc_id").val('');
		$("#r_doc").val('');
		$("#batch_no").val('0');
		$("#pkd").val('');
		$("#txtqnt").val('');
		$("#strip_stk").val('');
		$("#txtavailstk").val('');
		$("#txtmrp").val('');
		$("#txtrate").val('');
		$("#gst").val('');
		$("#txtexpiry").val('');
		var tot=0;
		var tot_ts=document.getElementsByClassName("all_rate");
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
			tot+=parseFloat(tot_ts[i].value);
		}
		tot=tot.toFixed(2);
		$("#final_rate").html(tot);
		setTimeout(function(){$("#r_doc").focus();},500);
	}
	
	//------------------------------------------------------------------------------------------//
	function insert_temp(itm_id,bch,exp_dt,qnt,gst,gstamt,pkd_qnt,mrp,unit_cost,amt)
	{
		$.post("pages/ph_stock_transfer_ajax.php",
		{
			itm_id:itm_id,
			bch:bch,
			exp_dt:exp_dt,
			qnt:qnt,
			gst:gst,
			gstamt:gstamt,
			pkd_qnt:pkd_qnt,
			mrp:mrp,
			unit_cost:unit_cost,
			amt:amt,
			sub_dept:$("#sub_dept").val(),
			user:$("#user").text().trim(),
			type:10,
		},
		function(data,status)
		{
			//alert(data);
			//$("#hguide_div").html(data);	
		})
	}
	function load_temp_item()
	{
		$.post("pages/ph_stock_transfer_ajax.php",
		{
			sub_dept:$("#sub_dept").val(),
			user:$("#user").text().trim(),
			type:12,
		},
		function(data,status)
		{
			//alert(data);
			if(data!="")
			{
				$("#temp_item").html(data);
				set_amt();
			}
		})
	}
	function itm_rem(ths)
	{
		//alert(ths);
		$.post("pages/ph_stock_transfer_ajax.php",
		{
			itm:$(ths).closest('tr').find('.itm').val().trim(),
			bch:$(ths).closest('tr').find('.batch').val().trim(),
			sub_dept:$("#sub_dept").val(),
			user:$("#user").text().trim(),
			type:11,
		},
		function(data,status)
		{
			//alert(data);
		})
	}
	//------------------------------------------------------------------------------------------//
	
	function save_final()
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
		else
		{
			$("#button").attr("disabled",true);
			$("#button4").attr("disabled",true);
			$("#loader").show();
			var all="";
			for(var i=0; i<len; i++)
			{
				var itm=$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val();
				var bch=$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val();
				var qnt=$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val();
				var mrp=$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val();
				var rate=$(".all_tr:eq("+i+")").find('td:eq(5) input:first').val();
				var amt=$(".all_tr:eq("+i+")").find('td:eq(6) input:first').val();
				var gst_per=$(".all_tr:eq("+i+")").find('td:eq(6) input:last').val();
				var gst_amt=$(".all_tr:eq("+i+")").find('td:eq(7) input:first').val();
				var expdt=$(".all_tr:eq("+i+")").find('td:eq(7) input:last').val();
				var pkd=$(".all_tr:eq("+i+")").find('td:eq(5) input:last').val();
				all+=itm+"@@"+bch+"@@"+qnt+"@@"+mrp+"@@"+rate+"@@"+amt+"@@"+gst_per+"@@"+gst_amt+"@@"+expdt+"@@"+pkd+"@@#%#";
			}
			//alert(all);
			$.post("pages/ph_stock_transfer_ajax.php",
			{
				sub_dept:$("#sub_dept").val(),
				issue_to:$("#issue_to").val().trim(),
				issue_no:$("#issue_no").val().trim(),
				all:all,
				user:$("#user").text().trim(),
				type:4,
			},
			function(data,status)
			{
				$("#loader").hide();
				var v=data.split("@_@");
				var msg="Error";
				var n=0;
				if(v[0]==0)
				{
					msg="Error";
					$("#button").attr("disabled",false);
					$("#button4").attr("disabled",false);
				}
				if(v[0]==1)
				{
					msg="Done";
					n=1;
				}
				if(v[0]==2)
				{
					msg="Low stock";
					var vl=v[2];
					var chk=vl.split("@ead@");
					//alert(chk.length);
					for(var i=0; i<(chk.length); i++)
					{
						$("."+chk[i]).css("background","#FFA5A5");
					}
					$("#button").attr("disabled",false);
					$("#button4").attr("disabled",false);
				}
				alr(msg,n);
				$("#button5").focus();
				$("#ino").val(v[1]);
			})
		}
	}
	
	function load_batchno()
	{
		$('#batch_no option:not(:first)').remove();
		$.post("pages/ph_stock_transfer_ajax.php",
		{
			type:2,
			itm:$("#doc_id").val().trim(),
		},
		function(data,status)
		{
			//alert(data);
			var vl=data.split("@@");
			//alert(vl.length);
			for(var i=0; i<(vl).length; i++)
			{
				$('#batch_no').append('<option value="'+vl[i]+'">'+vl[i]+'</option>');
			}
		})
		$('#ref_doc').fadeOut(500);
	}
	function alr(msg,d)
	{
		if(d>0)
		{
			var len=$(".all_tr").length;
			for(var i=0; i<len; i++)
			{
				$(".all_tr:eq("+i+")").find('td:eq(7) span:first').text('');
				$(".all_tr:eq("+i+")").find('td:eq(7) span:last').html('<i class="icon-ok icon-large text-success"></i>');
			}
			$("#button5").attr("disabled",false);
		}
		$.gritter.add(
		{
			//title:	'Normal notification',
			text:	'<h5 style="text-align:center;">'+msg+'</h5>',
			time: 1000,
			sticky: false
		});
		if(d>0)
		{
			$(".gritter-item").css("background","#237438");
		}
	}
//------------------------item search---------------------------------//
function load_refdoc1()
{
		//$("#ref_doc").fadeIn(200);
		//$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},100);
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
					$("#button4").focus();
				}
				else
				{
					$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
					//$("#ref_doc").fadeIn(100);
					$.post("pages/ph_stock_transfer_ajax.php",
					{
						type:1,
						val:val.trim(),
					},
					function(data,status)
					{
						if(data!="")
						{
							$("#ref_doc").html(data);
							$("#ref_doc").fadeIn(100);
						}
						else
						{
							$("#ref_doc").slideUp(100);
						}
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
				$("#doc_info").fadeIn(200);
				doc_load(docs[1],doc_naam,docs[3],docs[4]);
			}
		}
}
function doc_load(id,name,gst,pkd)
{
	//alert(id);
	$("#r_doc").val(name);
	$("#doc_id").val(id);
	$("#gst").val(gst);
	$("#gst_str").text(gst);
	$("#pkd").val(pkd);
	//$("#doc_info").fadeIn(200);
	//$("#ref_doc").fadeOut(200);
	load_batchno();
	$("#batch_no").focus();
	doc_tr=1;
	doc_sc=0;
}
//------------------------item search end---------------------------------//
</script>
