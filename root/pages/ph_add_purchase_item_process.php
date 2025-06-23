<?php
$rcv=base64_decode($_GET['ipd']);
$v=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_purchase_receipt_master` WHERE `order_no`='$rcv'"));
?>
<style>
.entry_table tr th, .entry_table tr td
{
	padding : 0px 4px;
}
input[type="text"]
{
	margin-bottom:0px;
}
.table-report
{
	background:#FFFFFF;
}
input[disabled]
{
    background: #FAFAFA;
}
</style>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Purchase Received Bill</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<input type="hidden" id="rcv_no" value="<?php echo $rcv;?>" />
	<div>
		<table class="table table-condensed entry_table">
			<tr>
				<th colspan="2">Select Supplier</th>
				<th colspan="2">Supplier Bill No</th>
				<th colspan="3">Bill Date</th>
			</tr>
			<tr>
				<td colspan="2">
					<select id="supp" class="span5" disabled>
						<option value="0">Select Supplier</option>
						<?php
							$qsplr=mysqli_query($link,"select id,name from inv_supplier_master order by name");
							while($qsplr1=mysqli_fetch_array($qsplr))
							{
							 ?>
						<option value="<?php echo $qsplr1['id'];?>" <?php if($qsplr1['id']==$v['supp_code']){ echo "selected='selected'";}?>><?php echo $qsplr1['name'];?></option>
						<?php
							}?>
					</select>
				</td>
				<td colspan="2">
					<input type="text" id="bill_no" placeholder="Supplier Bill No" class="imp" value="<?php echo $v['bill_no'];?>" disabled />
				</td>
				<td colspan="3">
					<input type="text" id="billdate" placeholder="Bill Date" class="imp" value="<?php echo $v['bill_date'];?>" disabled />
					<input type="hidden" id="bill_amt" class="imp span2" onkeyup="chk_num(this,event)" placeholder="Bill Amount" value="<?php echo $v['bill_amount'];?>" disabled />
				</td>
			</tr>
			<tr>
				<th>Item</th>
				<th>Batch</th>
				<th>Expiry</th>
				<th>Quantity</th>
				<th>Free</th>
				<th>HSN Code</th>
				<th>GST</th>
			</tr>
			<tr>
				<td>
					<input type="text" id="r_doc" class="span3" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="$('#ref_doc').fadeOut(500)" placeholder="Item Name" />
					<input type="text" name="doc_id" id="doc_id" style="display:none;" value="">
					<div id="doc_info"></div>
					<div id="ref_doc" align="center" style="padding:8px;width:400px;">
						
					</div>
				</td>
				<td>
					<input type="text" id="batch" class="span2" placeholder="Batch No" />
				</td>
				<td>
					<input type="text" id="expiry" class="span2" maxlength="7" onkeyup="exp_dt(this.id,this.value,event)" placeholder="YYYY-MM" />
				</td>
				<td>
					<input type="text" id="qnt" class="span1" onkeyup="chk_num(this,event)" placeholder="Quantity" />
				</td>
				<td>
					<input type="text" id="free" class="span1" onkeyup="chk_num(this,event)" placeholder="Free" />
				</td>
				<td>
					<input type="text" id="hsn" class="span2" placeholder="HSN Code" />
				</td>
				<td>
					<!--<input type="text" id="gst" class="span1" onkeyup="chk_num(this,event)" placeholder="GST %" />-->
					<select id="gst" class="span1" onkeyup="chk_num(this,event)">
						<option value="">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `gst_percent_master`");
						while($r=mysqli_fetch_assoc($q))
						{
						?>
						<option value="<?php echo $r['gst_per'];?>"><?php echo $r['gst_per'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Pkd Qnt</th>
				<th>Strip MRP</th>
				<th>Strip Cost Price</th>
				<th>Discount %</th>
				<th>Item Amount</th>
				<th>Rack No</th>
				<th>Add</th>
			</tr>
			<tr>
				<td>
					<input type="text" id="pkd_qnt" class="span1" onkeyup="chk_num(this,event)" placeholder="Pkd Qnt" />
				</td>
				<td>
					<input type="text" id="mrp" class="span2" onkeyup="chk_dec(this,event);calc_mrp(this.value,event)" placeholder="MRP" />
					<input type="hidden" id="unit_sale" class="span2" disabled placeholder="Sale Price" />
				</td>
				<td>
					<input type="text" id="c_price" class="span2" onkeyup="chk_dec(this,event);cal_costprice(this.value,event)" placeholder="Cost Price" />
					<input type="hidden" id="unit_cost" class="span2" placeholder="Cost Price" />
				</td>
				<td>
					<input type="text" id="disc" class="span1" onkeyup="chk_dec(this,event);calc_disc(this.value,event)" placeholder="Discount" />
				</td>
				<td>
					<input type="text" id="itm_amt" class="span2" disabled placeholder="Item Amount" />
				</td>
				<td>
					<input type="text" id="rack_no" class="span2" placeholder="Rack No" />
				</td>
				<td>
					<button type="button" id="add" class="btn btn-info" onclick="add_data()">Add</button>
				</td>
			</tr>
		</table>
	</div>
	<div id="temp_item" style="min-height:200px;max-height:250px;overflow-y:scroll;">
	
	</div>
	<table class="table table-condensed entry_table">
		<tr>
			<th>Total</th>
			<td>
				<input type="text" id="total" class="imp span2" disabled placeholder="Total" />
			</td>
			<th>Discount</th>
			<td>
				<input type="text" id="discount" class="imp span2" onkeyup="chk_dec(this,event);calulate_discount(this.value,event)" disabled placeholder="Discount" />
			</td>
			<th>GST Amount</th>
			<td>
				<input type="text" id="all_gst" class="imp span2" disabled placeholder="GST Amount" />
			</td>
			<th>Net Amount</th>
			<td>
				<input type="text" id="net_total" class="imp span2" disabled placeholder="Net Amount" />
			</td>
		</tr>
		<tr>
			<td colspan="8" style="text-align:center;">
				<button type="button" id="sav" class="btn btn-primary" onclick="save_data_final()">Done</button>
				<button type="button" id="refres" class="btn btn-danger" onclick="new_entry()">Refresh</button>
			</td>
		</tr>
	</table>
	<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
	<div id="msgg" style="display:none;top:8%;left:40%;background:#FFFFFF;padding:5px;border-radius:4px;box-shadow:0px 0px 8px 0px #F67379;position:fixed;font-size:20px;font-weight:bold;color:#D90913"></div>
</div>
<div id="loader" style="position:fixed;display:none;top:50%;left:50%;"></div>

<input type="hidden" id="chk_val1" />
<input type="hidden" id="chk_val2" />
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
$(document).ready(function()
{
	$("#billdate").datepicker({dateFormat: 'yy-mm-dd'});
	//$("#expiry").datepicker({dateFormat: 'yy-mm-dd',changeMonth:true,changeYear:true,yearRange:'c-10:c+10'});
	load_bill();
	$("#supp").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="0")
			{
				$("#bill_no").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	})
	$("#bill_no").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#r_doc").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	})
	$("#bill_amt").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="" && parseFloat($(this).val().trim())>0)
			{
				$("#r_doc").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			//$(this).val($(this).val().toUpperCase());
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#batch").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#expiry").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			//$(this).val($(this).val().toUpperCase());
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#qnt").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="" && parseInt($(this).val().trim())>0)
			{
				$("#free").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	})
	$("#free").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			$("#gst").focus();
		}
	})
	$("#mrp").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="" && parseFloat($(this).val().trim())>0)
			{
				$("#c_price").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#gst").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#pkd_qnt").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#pkd_qnt").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="" && parseInt($(this).val().trim())>0)
			{
				$("#mrp").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#c_price").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#disc").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#disc").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			$("#add").focus();
		}
	});
	$("#bill_no").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#billdate").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	$("#billdate").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			if($(this).val().trim()!="")
			{
				$("#bill_amt").focus();
			}
			else
			{
				$(this).css("border","1px solid #FD0B0B");
				$(this).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			}
		}
		else
		{
			//$(this).val($(this).val().toUpperCase());
			$(this).css("border","");
			$(this).css("box-shadow","");
		}
	});
	
});
function load_bill()
{
	$("#loader").show();
	$.post("pages/ph_add_purchase_item_ajax.php",
	{
		rcv:$("#rcv_no").val().trim(),
		type:2,
	},
	function(data,status)
	{
		$("#loader").hide();
		$("#temp_item").html(data);
		$("#r_doc").focus();
		set_amt();
	})
}
function calc_mrp(val,e) //for calculation
{
	$.post("pages/ph_purchase_receive_ajax.php",
	{
		type:1,
		rate:val,
		pktqnt:$("#pkd_qnt").val(),
		gst:$("#gst").val(),
	},
	function(data,status)
	{
		//alert(data);
		var val=data.split("@");
		$("#unit_sale").val(val['0']);
	})
}
function cal_costprice(val,e)
{
	if(val=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
	}
	var scst=val;
	var pkd=parseInt($("#pkd_qnt").val());
	var qnt=parseInt($("#qnt").val());
	var em=0;
	var amt=0;
	em=scst/pkd;
	amt=scst*qnt;
	em=em.toFixed(2);
	$("#unit_cost").val(em);
	$("#itm_amt").val(amt);
	$("#disc").val("0");
}
function exp_dt(id,vl,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	
	if(unicode==13)
	{
		var v=vl.split("-");
		if((vl.trim())=="" || (vl).length!=7 || parseInt(v[0])<2018 || parseInt(v[0])>2050 || parseInt(v[1])>12 || parseInt(v[1])==0 || v.length>2)
		{
			$("#"+id).css("border","1px solid #FF0000");
			$("#"+id).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			return true;
		}
		else
		{
			$("#qnt").focus();
		}
	}
	else if(e=="")
	{
		var v=vl.split("-");
		if((vl.trim())=="" || (vl).length!=7 || parseInt(v[0])<2018 || parseInt(v[0])>2050 || parseInt(v[1])>12 || parseInt(v[1])==0 || v.length>2)
		{
			$("#"+id).css("border","1px solid #FF0000");
			$("#"+id).css("box-shadow","0px 0px 10px 0px #FD0B0B");
			return true;
		}
		else
		{
			//$("#qnt").focus();
		}
	}
	else
	{
		$("#"+id).css("border","");
		$("#"+id).css("box-shadow","");
		if(($("#"+id).val().trim()).length==4)
		{
			$("#"+id).val((vl)+"-");
		}
	}
}
function check_date_format(testDate)
{
	var date_regex = /([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/;
	return (date_regex.test(testDate));
}
function calc_disc(val,e)
{
	if(val=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
	}
	var cost=$("#c_price").val().trim();
	if(cost=="")
	{
		cost=0;
	}
	else
	{
		cost=parseFloat(cost);
	}
	var qnt=$("#qnt").val().trim();
	if(qnt=="")
	{
		qnt=0;
	}
	else
	{
		qnt=parseFloat(qnt);
	}
	var amt=cost*qnt;
	var d_amt=(amt*val)/100;
	var res=amt-d_amt;
	$("#itm_amt").val(res);
}
function new_entry()
{
	window.location='index.php?param='+btoa(24);
}
function add_data()
{
	exp_dt('expiry',$("#expiry").val().trim(),'');
	if($("#supp").val()=="0")
	{
		$("#supp").focus();
	}
	else if($("#bill_no").val().trim()=="")
	{
		$("#bill_no").focus();
	}
	else if($("#billdate").val().trim()=="")
	{
		$("#billdate").focus();
	}
	//~ else if($("#bill_amt").val().trim()=="")
	//~ {
		//~ $("#bill_amt").focus();
	//~ }
	//~ else if(parseInt($("#bill_amt").val().trim())==0)
	//~ {
		//~ $("#bill_amt").focus();
	//~ }
	else if($("#doc_id").val()=="")
	{
		$("#r_doc").focus();
	}
	else if($("#batch").val().trim()=="")
	{
		$("#batch").focus();
	}
	else if($("#expiry").val().trim()=="")
	{
		$("#expiry").focus();
	}
	else if($("#qnt").val().trim()=="")
	{
		$("#qnt").focus();
	}
	else if(parseInt($("#qnt").val().trim())<0)
	{
		$("#qnt").focus();
	}
	//~ else if(parseInt($("#qnt").val().trim())==0)
	else if(parseInt($("#qnt").val().trim())==0 && (parseInt($("#qnt").val().trim()) + parseInt($("#free").val().trim()))==0 )
	{
		$("#free").focus();
	}
	else if($("#gst").val().trim()=="")
	{
		$("#gst").focus();
	}
	else if(parseFloat($("#gst").val().trim())<0)
	{
		$("#gst").focus();
	}
	else if($("#pkd_qnt").val().trim()=="")
	{
		$("#pkd_qnt").focus();
	}
	else if(parseInt($("#pkd_qnt").val().trim())==0)
	{
		$("#pkd_qnt").focus();
	}
	else if(parseInt($("#pkd_qnt").val().trim())<0)
	{
		$("#pkd_qnt").focus();
	}
	else if($("#mrp").val().trim()=="")
	{
		$("#mrp").focus();
	}
	else if(parseFloat($("#mrp").val().trim())==0)
	{
		$("#mrp").focus();
	}
	else if(parseFloat($("#mrp").val().trim())<0)
	{
		$("#mrp").focus();
	}
	else if($("#c_price").val().trim()=="")
	{
		$("#c_price").focus();
	}
	else if(parseFloat($("#c_price").val().trim())==0)
	{
		$("#c_price").focus();
	}
	else if(parseFloat($("#c_price").val().trim())<0)
	{
		$("#c_price").focus();
	}
	else
	{
		add_item_temp();
	}
}
function add_item_temp()
{
	var itm_id=$("#doc_id").val().trim();
	var itm_name=$("#r_doc").val().trim();
	var bch=$("#batch").val().trim();
	var exp_dt=$("#expiry").val().trim();
	var qnt=$("#qnt").val().trim();
	var free=$("#free").val().trim();
	if(free=="")
	{
		free=0;
	}
	var gst=$("#gst").val().trim();
	var pkd_qnt=$("#pkd_qnt").val().trim();
	var mrp=$("#mrp").val().trim();
	var cost=$("#c_price").val().trim();
	var unit_sale=$("#unit_sale").val().trim();
	var unit_cost=$("#unit_cost").val().trim();
	var hsn=$("#hsn").val().trim();
	var rack_no=$("#rack_no").val().trim();
	var disc=$("#disc").val().trim();
	if(disc=="")
	{
		disc=0;
	}
	else
	{
		disc=parseFloat(disc);
	}
	if(gst=="")
	{
		gst=0;
	}
	else
	{
		gst=parseInt(gst);
	}
	var tr_len=$('#mytable tr.all_tr').length;
	var amt=parseFloat(qnt)*parseFloat(cost);
	
	var gstamt=0;
    var vdisamt=0;
	//amt=amt+gstamt;
	var d_amt=((amt*disc)/100);
	
	amt=amt-d_amt;
	gstamt=(amt*gst/100);
	amt=amt.toFixed(2);
	d_amt=d_amt.toFixed(2);
	gstamt=gstamt.toFixed(2);
	if(tr_len==0)
	{
		var test_add="<table class='table table-condensed table-bordered table-report' id='mytable'>";
		test_add+="<tr>";
		test_add+="<th width='5%'>#</th>";
		test_add+="<th>Description</th>";
		test_add+="<th>Batch</th>";
		test_add+="<th>Expiry</th>";
		test_add+="<th style='text-align:right;'>Quantity</th>";
		test_add+="<th style='text-align:right;'>Free</th>";
		test_add+="<th style='text-align:right;'>GST %</th>";
		test_add+="<th style='text-align:right;'>Pkd Qnt</th>";
		test_add+="<th style='text-align:right;'>Strip MRP</th>";
		test_add+="<th style='text-align:right;'>Strip Cost</th>";
		test_add+="<th style='text-align:right;'>Discount %</th>";
		test_add+="<th style='text-align:right;'>Amount</th>";
		test_add+="<th width='5%'>Remove</th>";
		test_add+="</tr>";
		
		test_add+="<tr class='all_tr tr_vals'>";
		test_add+="<td>1</td>";
		test_add+="<td>"+itm_name+"<input type='hidden' value='"+itm_id+"' class='itm_id'/><input type='hidden' value='"+itm_id+bch+"' class='test_id'/></td>";
		test_add+="<td>"+bch+"<input type='hidden' value='"+bch+"' class='bch' /></td>";
		test_add+="<td>"+exp_dt+"<input type='hidden' value='"+exp_dt+"' class='exp_dt' /></td>";
		test_add+="<td style='text-align:right;'>"+qnt+"<input type='hidden' value='"+qnt+"' class='qnt' /></td>";
		test_add+="<td style='text-align:right;'>"+free+"<input type='hidden' value='"+free+"' class='free' /><input type='hidden' value='"+hsn+"' class='hsn' /></td>";
		test_add+="<td style='text-align:right;'>"+gst+"<input type='hidden' value='"+gst+"' class='gst' /><input type='hidden' value='"+gstamt+"' class='all_gst' /></td>";
		test_add+="<td style='text-align:right;'>"+pkd_qnt+"<input type='hidden' value='"+pkd_qnt+"' class='pkd_qnt' /></td>";
		test_add+="<td style='text-align:right;'>"+mrp+"<input type='hidden' value='"+mrp+"' class='mrp'/><input type='hidden' value='"+unit_sale+"' class='unit_sale'/></td>";
		test_add+="<td style='text-align:right;'>"+cost+"<input type='hidden' value='"+cost+"' class='cost'/><input type='hidden' value='"+unit_cost+"' class='unit_cost'/></td>";
		test_add+="<td style='text-align:right;'>"+disc+"<input type='hidden' value='"+disc+"' class='disc'/><input type='hidden' value='"+d_amt+"' class='d_amt'/></td>";
		test_add+="<td style='text-align:right;'>"+amt+"<input type='hidden' value='"+amt+"' class='all_rate'/><input type='hidden' value='"+rack_no+"' class='rack_no' /></td>";
		test_add+="<td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_sl();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td>";
		test_add+="</tr>";
		test_add+="<tr id='new_tr'><th colspan='10' style='text-align:right;'>Total</th><td style='text-align:right;' id='final_rate'>"+amt+"</td><td></td></tr>";
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
			$("#msgg").text("Already selected same item and same batch no.");
			$("#msgg").fadeIn(500);
			setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#temp_item").css({'opacity':'1.0'});
			})},800);
		}
		else
		{
			tr_len++;
			var tr=document.createElement("tr");
			tr.setAttribute("class","all_tr tr_vals");
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
			var td12=document.createElement("td");
			var td13=document.createElement("td");
			
			td1.innerHTML=tr_len;
			td2.innerHTML=itm_name+"<input type='hidden' value='"+itm_id+"' class='itm_id' /><input type='hidden' value='"+itm_id+bch+"' class='test_id' />";
			td3.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='bch' />";
			td4.innerHTML=exp_dt+"<input type='hidden' value='"+exp_dt+"' class='exp_dt' />";
			td5.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' class='qnt' />";
			td5.setAttribute("style","text-align:right;");
			td6.innerHTML=free+"<input type='hidden' value='"+free+"' class='free' /><input type='hidden' value='"+hsn+"' class='hsn' />";
			td6.setAttribute("style","text-align:right;");
			td7.innerHTML=gst+"<input type='hidden' value='"+gst+"' class='gst' /><input type='hidden' value='"+gstamt+"' class='all_gst' />";
			td7.setAttribute("style","text-align:right;");
			td8.innerHTML=pkd_qnt+"<input type='hidden' value='"+pkd_qnt+"' class='pkd_qnt' />";
			td8.setAttribute("style","text-align:right;");
			td9.innerHTML=mrp+"<input type='hidden' value='"+mrp+"' class='mrp' /><input type='hidden' value='"+unit_sale+"' class='unit_sale' />";
			td9.setAttribute("style","text-align:right;");
			td10.innerHTML=cost+"<input type='hidden' value='"+cost+"' class='cost' /><input type='hidden' value='"+unit_cost+"' class='unit_cost' />";
			td10.setAttribute("style","text-align:right;");
			td11.innerHTML=disc+"<input type='hidden' value='"+disc+"' class='disc' /><input type='hidden' value='"+d_amt+"' class='d_amt' />";
			td11.setAttribute("style","text-align:right;");
			td12.innerHTML=amt+"<input type='hidden' value='"+amt+"' class='all_rate' /><input type='hidden' value='"+rack_no+"' class='rack_no' />";
			td12.setAttribute("style","text-align:right;");
			td13.innerHTML="<span onclick='$(this).parent().parent().remove();set_sl();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span>";
			td13.setAttribute("style","text-align:center;");
			
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
			tr.appendChild(td12);
			tr.appendChild(td13);
			
			document.getElementById("mytable").appendChild(tr);
		}
	}
	$("#supp").attr('disabled',true);
	$("#bill_no").attr('disabled',true);
	$("#billdate").attr('disabled',true);
	$("#bill_amt").attr('disabled',true);
	$("#doc_id").val('');
	$("#r_doc").val('');
	$("#batch").val('');
	$("#expiry").val('');
	$("#free").val('');
	$("#mrp").val('');
	$("#c_price").val('');
	$("#gst").val('');
	$("#pkd_qnt").val('');
	$("#unit_sale").val('');
	$("#unit_cost").val('');
	$("#itm_amt").val('');
	$("#qnt").val('');
	$("#disc").val('');
	$("#hsn").val('');
	$("#rack_no").val('');
	set_amt();
	setTimeout(function()
	{
		$("#r_doc").focus();
	},500);
}
function set_amt()
{
	var net_amt=0;
	var tot=0;
	var gstamt=0;
	var dis_amt=0;
	var tot_ts=document.getElementsByClassName("all_rate");
	for(var j=0;j<tot_ts.length;j++)
	{
		tot=tot+parseFloat(tot_ts[j].value);
	}
	var tot_gst=document.getElementsByClassName("all_gst");
	for(var j=0;j<tot_gst.length;j++)
	{
		gstamt=gstamt+parseFloat(tot_gst[j].value);
	}
	var tot_dis=document.getElementsByClassName("d_amt");
	for(var j=0;j<tot_dis.length;j++)
	{
		dis_amt=dis_amt+parseFloat(tot_dis[j].value);
	}
	net_amt=tot+gstamt;
	$("#total").val(tot);
	$("#bill_amt").val(tot);
	tot=tot.toFixed(2);
	gstamt=gstamt.toFixed(2);
	dis_amt=dis_amt.toFixed(2);
	$("#all_gst").val(gstamt);
	$("#net_total").val(net_amt);
	$("#discount").val(dis_amt);
	var new_tr="<tr id='new_tr'><th colspan='11' style='text-align:right;'>Total</th><td style='text-align:right;' id='final_rate'>"+tot+"</td><td></td></tr>";
	$("#new_tr").remove();
	$('#mytable tr:last').after(new_tr);
}
function save_data_final()
{
	if($("#supp").val()=="0")
	{
		$("#supp").focus();
	}
	else if($("#bill_no").val().trim()=="")
	{
		$("#bill_no").focus();
	}
	else if($("#billdate").val().trim()=="")
	{
		$("#billdate").focus();
	}
	//~ else if($("#bill_amt").val().trim()=="")
	//~ {
		//~ $("#bill_amt").focus();
	//~ }
	//~ else if(parseInt($("#bill_amt").val().trim())==0)
	//~ {
		//~ $("#bill_amt").focus();
	//~ }
	else if($(".tr_vals").length==0)
	{
		$("#msgg").text("No Item Added.");
		$("#msgg").fadeIn(500);
		setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#r_doc").focus();})},800);
	}
	else
	{
		//alert();
		$("#loader").show();
		$("#add").attr("disabled",true);
		$("#sav").attr("disabled",true);
		var len=$(".tr_vals").length;
		var all="";
		for(var i=0; i<len; i++)
		{
			all+=$(".tr_vals:eq("+i+")").find('td:eq(1) input:first').val(); // item_id
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(2) input:first').val(); // bch
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(3) input:first').val(); // exp_dt
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(4) input:first').val(); // qnt
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(5) input:first').val(); // free
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(6) input:first').val(); // gst
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(6) input:last').val(); // gst_amt
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(7) input:first').val(); // pkd_qnt
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(8) input:first').val(); // strip mrp
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(8) input:last').val(); // unit_sale
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(9) input:first').val(); // strip cost
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(9) input:last').val(); // unit_cost
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(10) input:first').val(); // disc_per
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(10) input:last').val(); // d_amt
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(11) input:first').val(); // itm_amt
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(5) input:last').val(); // hsn
			all+="@@"+$(".tr_vals:eq("+i+")").find('td:eq(11) input:last').val(); // rack_no
			all+="@@#%#";
		}
		//alert(all);
		$.post("pages/ph_add_purchase_item_ajax.php",
		{
			type:3,
			rcv_no:$("#rcv_no").val(),
			billdate:$("#billdate").val().trim(),
			bill_amt:$("#bill_amt").val().trim(),
			total:$("#total").val().trim(),
			discount:$("#discount").val().trim(),
			all_gst:$("#all_gst").val().trim(),
			net_amt:$("#net_total").val().trim(),
			user:$("#user").text().trim(),
			btn_val:$("#sav").text().trim(),
			all:all,
		},
		function(data,status)
		{
			$("#loader").hide();
			alert(data);
			for(var i=0; i<len; i++)
			{
				$(".tr_vals:eq("+i+")").find('td:eq(12)').html('<i class="icon-ok icon-large"></i>');
			}
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
function calulate_discount(val,e)
{
	if(val.trim()=="")
	{
		val=0;
	}
	else
	{
		val=parseFloat(val);
	}
	var tot=parseFloat($("#total").val().trim());
	var all_gst=parseFloat($("#all_gst").val().trim());
	var disamt=0;
	disamt=tot+all_gst-val;
	$("#net_total").val(disamt);
}
function chk_dec(ths,e)
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
function chk_num(ths,e)
{
	var val=ths.value;
	if(/\D/g.test(val))
	{
		val=val.replace(/\D/g,'');
		$(ths).val(val);
	}
}
function change_bill(val)
{
	var tot=$("#total").val().trim();
	if(tot=="")
	{
		tot=0;
	}
	else
	{
		tot=parseInt(tot);
	}
	var paid=$("#paid").val().trim();
	if(paid=="")
	{
		paid=0;
	}
	else
	{
		paid=parseInt(paid);
	}
	var bal=tot-paid;
	
	if(val!="2")
	{
		$("#paid").val(tot);
		$("#bal").val("0");
		$("#paid").attr("disabled",false);
		if(val=="3")
		{
			$("#token_text").show();
			$("#token_div").show();
		}
		else
		{
			$("#token_text").hide();
			$("#token_div").hide();
		}
	}
	else
	{
		$("#paid").val("0");
		$("#bal").val(paid);
		$("#paid").attr("disabled",true);
		$("#token_text").hide();
		$("#token_div").hide();
	}
}
function bill_next(id,e)
{
	if(e.keyCode==13)
	{
		if($("#"+id).val()=="2")
		{
			$("#button4").focus();
		}
		else if($("#"+id).val()=="3")
		{
			$("#token").focus();
		}
		else
		{
			$("#paid").focus();
		}
	}
}
function search_pin(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		if((val.trim())!="")
		{
			$("#loader").show();
			$.post("pages/canteen_ajax.php",
			{
				type:6,
				user:$("#user").text().trim(),
				pin:val,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#cname").val(data).focus();
			})
		}
		else
		{
			$("#cname").val("").focus();
		}
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
					$("#sav").focus();
				}
				else
				{
					$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
					$("#ref_doc").fadeIn(200);
					$.post("pages/ph_purchase_receive_ajax.php",
					{
						type:4,
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
				var doc_naam=docs[2].trim();
				var gst=docs[3].trim();
				var s_qnt=docs[4].trim();
				var hsn=docs[5].trim();
				var rack_no=docs[6].trim();
				$("#doc_info").fadeIn(200);
				doc_load(docs[1],doc_naam,gst,s_qnt,hsn,rack_no);
			}
		}
}
function doc_load(id,name,gst,s_qnt,hsn,rack_no)
{
	$("#r_doc").val(name);
	$("#doc_id").val(id);
	$("#gst").val(gst);
	$("#pkd_qnt").val(s_qnt);
	$("#hsn").val(hsn);
	$("#rack_no").val(rack_no);
	$("#ref_doc").fadeOut(200);
	$("#batch").val('');
	$("#batch").focus();
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

function hguide_up(val,e)
{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				if(unicode==27)
				{
					$("#sav").focus();
				}
				else
				{
					$("#hguide_div").html("<img src='../images/ajax-loader.gif' />");
					$("#hguide_div").fadeIn(200);
					$.post("pages/ph_purchase_receive_ajax.php"	,
					{
						val:val,
						item_id:$("#doc_id").val().trim(),
						type:3,
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
//=========================================================================

function popitup1(url)
{
	var substrid=$("#selectsubstr").val();
	var orderno=$("#txtordo").val();
	var orderdate=$("#txtorddate").val();
	
	url=url+"?substrid="+substrid+"&orderno="+orderno+"&orderdate="+orderdate;
	newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
	$("#button5").attr("disabled",true);
	get_id();
}

</script>
