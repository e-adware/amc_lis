<!--header-->
<?php
$orderno="";
$orderno=base64_decode($_GET["orderno"]);

if($orderno)
{
	echo "<input type='hidden' value='$orderno' id='txtordrid'>";
}else
{
	echo "<input type='hidden' value='0' id='txtordrid'>";
}

?>

<div id="content-header">
    <div class="header_div"> <span class="header">Purchase Order</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed table-report">
		<tr style="display:none;">
			<th>Order Date</th>
			<td>
				<input type="text" id="fid" name="fid" value="<?php echo $fid['maxfid'];?>" style="display:none;" />
				<input type="text" id="txtorddate" name="txtorddate" value="<?php echo date('Y-m-d');?>" onfocus="jsdate('txtorddate')"/>
			</td>
		</tr>
		<tr>
			<th>Select Supplier</th>
			<td>
				<select name="selectsubstr" id="selectsubstr" class="span5" autofocus>
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
		</tr>
		
		<tr style="display:none;">
			<td>Order No</td>
			<td>
				<input type="text" name="txtordo" id="txtordo" size="20" value="" class="imp" readonly="readonly" />
			</td>
		</tr>
		
		<tr>
		   <th>Item Name </th>
		   <td>
				<select name="txtcntrname" id="txtcntrname" class="span5" autofocus>
					<option value="0">Select</option>
					<?php
						$pid = mysqli_query($link," SELECT `item_id`, `item_name` FROM `item_master` order by `item_name`");
						while($pat1=mysqli_fetch_array($pid))
						{
					?>
						<option value="<?php echo $pat1['item_id'];?>"><?php echo $pat1['item_name'];?></option>
					<?php
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Quantity</th>
			<td>
				<input type="text" name="txtqnt" id="txtqnt" autocomplete="off" class="imp intext span2" onkeyup="numentry('txtqnt')" placeholder="Quantity" />
				&nbsp;&nbsp;&nbsp;
				<input type="text" name="rate" id="rate" autocomplete="off" class="imp intext span2" onkeyup="chk_dec(this,event)" placeholder="Rate" />
				&nbsp;&nbsp;&nbsp;
				<input type="text" id="notes" class="span4" placeholder="Additional Notes" />
			</td>
		</tr>
		
		<tr>
			<td colspan="2" style="text-align:center">
				<!--<input type="button" name="button2" id="button2" value="Reset" onclick="window.location='processing.php?param=163'" class="btn btn-danger" /> -->
				<input type="button" id="button" value="Add" onclick="add_item()" class="btn btn-primary" />
				<!--<input type="button" name="button4" id="button4" value= "Done" onclick="insert_data_final()" class="btn btn-success" />-->
				<button type="button" id="btn_final" onclick="insert_final()" disabled="disabled" class="btn btn-success">Done</button>
				<button type="button" id="btn_upd" onclick="order_update()" style="display:none;" class="btn btn-success">Update</button>
				<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/inv_purchase_ordr_rpt.php')"  />-->
				<button type="button" class="btn btn-danger" onclick="clrr()">New Order</button>
			</td>
		</tr>
		
	</table>
	<div id="load_select" class="vscrollbar" style="max-height:250px;overflow-y:scroll;">
		
	</div>
	<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
	<div id="msgg" style="display:none;top:45%;left:43%;position:fixed;font-size:22px;color:#D62024"></div>
	<div id="loader" style="display:none;top:50%;position:fixed;"></div>
</div>
<!--modal-->
<input type="button" data-toggle="modal" data-target="#repmodal" id="rep" style="display:none"/>
<input type="text" id="modtxt" value="0" style="display:none"/>
<div class="modal fade" id="repmodal" role="dialog" aria-labelledby="repmodalLabel" aria-hidden="true" style="border-radius:0;border-radius: 0;width: 1000px;left: 35%;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="result" style="max-height:400px;overflow-y:scroll;">
				
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-mini" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!--modal end-->
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{
		get_id();
		//pending_order();
		//new_order();
		$("#button4").attr("disabled",true);
		//load_item();
		//load_selected_item();
		
		$("#selectsubstr").select2({ theme: "classic" });
		$("#txtcntrname").select2({ theme: "classic" });
		$("#selectsubstr").select2("focus");
		
		$("#selectsubstr").on("select2:close",function(e)
		{
			if($("#selectsubstr").val()!=0)
			{
				setTimeout(function()
				{
					//$("#txtcntrname").focus();
					$('#txtcntrname').select2('focus');
				},200);
			}
		});
		
		$("#txtcntrname").on("select2:close",function(e)
		{
			if($("#txtcntrname").val()!=0)
			{
				setTimeout(function()
				{
					$("#txtqnt").focus();
					//$('#txtqnt').select2('focus');
				},200);
			}
		});
		
		if($("#txtordrid").val().trim()!="0")
		{
		  //search_data();
		  edit_ord($("#txtordrid").val().trim());
		}
		
		$("#selectsubstr").keyup(function(e)
		{
			$(this).css("border","");
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#txtcntrname").focus();
					//$('#selectcategory').select2('focus');
				}
			}
		});
		
		$("#txtcntrname").keyup(function(e)
		{
			$(this).css("border","");
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#txtqnt").focus();
					//$('#selectcategory').select2('focus');
				}
			}
		});
		
		$("#txtcntrname").keyup(function(e)
		{
			if(e.keyCode==27)
			$("#button4").focus();
		});
		$("#notes").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#button").focus();
			if(e.keyCode==27)
			$("#button4").focus();
		});
		
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($("#txtqnt").val().trim()!="" && parseInt($("#txtqnt").val())>0)
				{
					//$("#button").focus();
					$("#rate").focus();
				}
			}
		});
		
		$("#rate").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($("#rate").val().trim()!="" && parseFloat($("#rate").val())>0)
				{
					//$("#button").focus();
					$("#notes").focus();
				}
			}
		});
	});
	
	//==================================================================
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
	function clrr()
	{
		//location.reload(true);
		//window.location="processing.php?param=163";
		window.location="index.php?param="+btoa(163);
	}
	function new_order()
	{
		$("#btn_pop").attr("disabled",true);
		$("#btn_list").attr("disabled",false);
		$("#new_order").slideDown();		
		$(".btn_call").removeClass("btn-primary");
		$("#btn_pop").addClass("btn-primary");
		$("#pend_ord").slideUp();
		$("#pend_ord").empty();
		$("#load_select").empty();
		$("#btn_final").show();
		$("#btn_upd").hide();
		setTimeout(function()
		{
			$("#selectsubstr").select2("focus");
		},500);
	}
	function canc_order()
	{		
		$("#new_order").slideUp();
		$("#load_select").empty();
		$("#selectsubstr").val("0").trigger("change");
		$("#selectsubstr").attr("disabled",false);
		$("#btn_pop").attr("disabled",false);
		pending_order();
	}
	function add_item()
	{
		if($("#selectsubstr").val()==0)
		{
			$("#selectsubstr").select2("focus");
		}
		else if($("#txtcntrname").val()==0)
		{
			$("#txtcntrname").select2("focus");
		}
		else if($("#txtqnt").val()=="")
		{
			$("#txtqnt").focus();
		}
		else if(parseInt($("#txtqnt").val())==0)
		{
			$("#txtqnt").focus();
		}
		else if(parseInt($("#txtqnt").val())<0)
		{
			$("#txtqnt").focus();
		}
		else
		{
			$.post("pages/inv_purchase_order_ajax.php",
			{
				id:$("#txtcntrname").val(),
				type:1,
			},
			function(data,status)
			{
				//alert(data);
				$("#selectsubstr").attr("disabled",true);
				//var vl=data.split("@@");
				add_item_temp($("#txtcntrname").val(),data,$("#txtqnt").val().trim(),$("#rate").val().trim(),$("#notes").val().trim());
			})
		}
	}
	function add_item_temp(id,name,qnt,rate,notes)
	{
		var bch="";
		var amt=(parseFloat(rate)*parseFloat(qnt)).toFixed(2);
		var tr_len=$('#mytable tr').length;
		if(tr_len==0)
		{
			var test_add="<table class='table table-condensed table-bordered table-report' id='mytable'>";
			test_add+="<tr><th>Sl No</th><th>Description</th><th>Quantity</th><th>Rate</th><th>Amount</th><th>Notes</th><th style='width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr'>";
			test_add+="<td>1</td>";
			test_add+="<td>"+name+"<input type='hidden' value='"+id+"' class=''/><input type='hidden' value='"+id+bch+"' class='test_id'/></td>";
			test_add+="<td>"+qnt+"<input type='hidden' value='"+qnt+"' /></td>";
			test_add+="<td>"+rate+"<input type='hidden' value='"+rate+"' /></td>";
			test_add+="<td>"+amt+"<input type='hidden' value='"+amt+"' /></td>";
			test_add+="<td>"+notes+"<input type='hidden' value='"+notes+"' /></td>";
			test_add+="<td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_sl_no()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td>";
			test_add+="</tr>";
			//test_add+="<tr id='new_tr'><td colspan='5' style='text-align:right;'><b>Total</b></td><td id='final_gst'></td><td colspan='3' id='final_rate'></td></tr>";
			test_add+="</table>";
			
			$("#load_select").html(test_add);
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

				$("#load_select").css({'opacity':'0.5'});
				$("#msgg").text("Already Selected same item.");
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#load_select").css({'opacity':'1.0'});
				})},800);
				
			}			
			else
			{
		   
			var tr=document.createElement("tr");
			tr.setAttribute("class","all_tr");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var td4=document.createElement("td");
			var td5=document.createElement("td");
			var td6=document.createElement("td");
			var td7=document.createElement("td");
			var tbody=document.createElement("tbody");
			
			td1.innerHTML=tr_len;
			td2.innerHTML=name+"<input type='hidden' value='"+id+"' class=''/><input type='hidden' value='"+id+bch+"' class='test_id'/>";
			td3.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' />";
			td4.innerHTML=rate+"<input type='hidden' value='"+rate+"' />";
			td5.innerHTML=amt+"<input type='hidden' value='"+amt+"' />";
			td6.innerHTML=notes+"<input type='hidden' value='"+notes+"' />";
			td7.innerHTML="<span onclick='$(this).parent().parent().remove();set_sl_no()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span>";
			td7.setAttribute("style","text-align:center;");
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			tr.appendChild(td5);
			tr.appendChild(td6);
			tr.appendChild(td7);
			document.getElementById("mytable").appendChild(tr);
			}
		}
		set_sl_no();
	}
	function set_sl_no()
	{
		$("#txtcntrname").val("0").trigger("change");
		$("#txtcntrname").select2("focus");
		$("#txtqnt").val("");
		$("#rate").val("");
		$("#notes").val("");
		
		var tot_ts=document.getElementsByClassName("all_tr");
		
		if(tot_ts.length>0)
		{
			$("#btn_final").attr("disabled",false);
		}
		else
		{
			$("#btn_final").attr("disabled",true);
		}
		
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
	}
	function set_amt()
	{
		var tot=0;
		var gst_tot=0;
		var tot_ts=document.getElementsByClassName("all_rate");
		var tot_gst_tr=document.getElementsByClassName("gst_amt");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseFloat(tot_ts[j].value);
		}
		for(var j=0;j<tot_gst_tr.length;j++)
		{
			gst_tot=gst_tot+parseFloat(tot_gst_tr[j].value);
		}
		var new_tr="<tr id='new_tr'><td colspan='6' style='text-align:right;'><b>Total</b></td><td id='final_gst'></td><td colspan='3' id='final_rate'></td></tr>";
		$("#new_tr").remove();
		$('#mytable tr:last').after(new_tr);
		$("#final_gst").text(gst_tot.toFixed(2));
		$("#final_rate").text(tot.toFixed(2));
		$("#txtcntrname").select2("focus");
		
		if(tot_ts.length>0)
		{
			$("#btn_final").attr("disabled",false);
		}
		else
		{
			$("#btn_final").attr("disabled",true);
		}
		
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
	}
	function insert_final()
	{
		var tot_ts=document.getElementsByClassName("all_tr");
		if(tot_ts.length==0)
		{
			$("#msgg").text("No item selected");
			$("#msgg").fadeIn(500);
			setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#txtcntrname").select2("focus");})},800);
		}
		else
		{
			$("#loader").show();
			$("#button").attr("disabled",true);
			$("#btn_final").attr("disabled",true);
			var all="";
			for(var i=0; i<tot_ts.length; i++)
			{
				all+=$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(5) input:first').val()+"@@#%#";
			}
			//alert(all);
			$.post("pages/inv_purchase_order_ajax.php",
			{
				supp:$("#selectsubstr").val(),
				all:all,
				user:$("#user").text().trim(),
				type:2,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				if(data=="1")
				{
					var all_tr=$(".all_tr");
					for(var j=0;j<all_tr.length;j++)
					{
						$(".all_tr:eq("+j+")").find('td:eq(6)').html("");
					}
					alert("Saved");
				}
				if(data=="2")
				{
					alert("Error");
				}
			})
		}
	}
	function pending_order()
	{
		$.post("pages/inv_ajax.php",
		{
			type:3,
		},
		function(data,status)
		{
			$("#pend_ord").html(data);
			$(".btn_call").removeClass("btn-primary");
			$("#btn_list").addClass("btn-primary");
			$("#pend_ord").slideDown();
			$("#new_order").slideUp();
			$("#btn_pop").attr("disabled",false);
			$("#btn_list").attr("disabled",true);
		})
	}
	function edit_ord(ord)
	{
		$("#loader").show();
		$.post("pages/inv_purchase_order_ajax.php",
		{
			ord:ord,
			type:4,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_select").html(data);
			$("#selectsubstr").val($("#supp_id").val().trim()).trigger("change");
			$("#selectsubstr").attr("disabled",true);
			
			//$("#btn_pop").attr("disabled",true);
			//$("#btn_list").attr("disabled",false);
			//$("#new_order").slideDown();		
			//$(".btn_call").removeClass("btn-primary");
			//$("#btn_pop").addClass("btn-primary");
			//$("#pend_ord").slideUp();
			//$("#pend_ord").empty();
			$("#btn_final").hide();
			$("#btn_upd").show();
			$("#txtcntrname").select2("focus");
		})
	}
	function order_update()
	{
		var tot_ts=document.getElementsByClassName("all_tr");
		if(tot_ts.length==0)
		{
			$("#msgg").text("No item selected");
			$("#msgg").fadeIn(500);
			setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#txtcntrname").select2("focus");})},800);
		}
		else
		{
			$("#loader").show();
			$("#button").attr("disabled",true);
			$("#btn_upd").attr("disabled",true);
			var all="";
			for(var i=0; i<tot_ts.length; i++)
			{
				all+=$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val()+"@@"+$(".all_tr:eq("+i+")").find('td:eq(5) input:first').val()+"@@#%#";
			}
			//alert(all);
			$.post("pages/inv_purchase_order_ajax.php",
			{
				ord_id:$("#ord_id").val().trim(),
				supp_id:$("#supp_id").val().trim(),
				all:all,
				user:$("#user").text().trim(),
				type:5,
			},
			function(data,status)
			{
				$("#loader").hide();
				if(data=="1")
				{
					var all_tr=$(".all_tr");
					for(var j=0;j<all_tr.length;j++)
					{
						$(".all_tr:eq("+j+")").find('td:eq(6)').html("");
					}
					alert("Updated");
				}
				if(data=="2")
				{
					alert("Error");
				}
				if(data=="3")
				{
					alert("Already Received");
				}
				if(data=="4")
				{
					alert("Order cancelled");
				}
			})
		}
	}
	function view_ord(ord)
	{
		$.post("pages/inv_ajax.php",
		{
			ord:ord,
			type:6,
		},
		function(data,status)
		{
			$("#result").html(data);
			$("#rep").click();
		})
	}
	function print_ord(ord)
	{
		//alert(ord);
		url="pages/print_purchase_order_approve.php?order="+btoa(ord);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	//==================================================================
	function jsdate(id)
	{
		new JsDatePick
		({
			useMode:2,
			target:id,
			dateFormat:"%Y/%m/%d"
		});
	}
	
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
						$("#load_materil").scrollTop(doc_sc)
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
						$("#load_materil").scrollTop(doc_sc);
					}
				}
			}
			else
			{
				$.post("pages/inv_load_data_ajax.php",
				{
					val:val,
					type:"indent_order",
				},
				function(data,status)
				{
					$("#load_materil").html(data);
				})
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
	
	
	function jsdate(id)
	 {
		new JsDatePick
		({
			useMode:2,
			target:id,
			dateFormat:"%Y/%m/%d"
		});
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
		clearr();
		$("#selectsubstr").attr("disabled",false);
		$("txtordrid").val('0');
		get_id();
		setTimeout("location.reload(true);",1000);
	}	
	function load_item()
	{
		$("#loader").show();
		$.post("pages/inv_load_data_ajax.php",
		{
			type:"indent_order",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_materil").html(data);
		})
	}
	
	
	function load_selected_item()
	{
		
		$.post("pages/inv_load_data_ajax.php",
		{
			type:"purchase_order_tmp",
			orderno:$("#txtordo").val(),
			spplrid:$("#selectsubstr").val(),
		},
		function(data,status)
		{
			
			$("#loader").hide();
			$("#load_select").html(data);
		})
	}
	
	function val_load_new(id)///for 
	{
		$("#loader").show();
		$.post("pages/inv_load_display.php",
		{
			type:"indent_order",
			id:id,
		},
		function(data,status)
		{
			$("#loader").hide();
			var val=data.split("@");
			$("#txtcid").val(val[0]);
			$("#txtcntrname").val(val[1]);	
			$("#txtqnt").focus();
		})
	}
	
	
	function delete_data(itmid,orderno,spplrid)
	{
		
		$.post("pages/inv_load_delete.php",
		{
			type:"purchase_order_tmp",
			itmid:itmid,
			orderno:orderno,
			spplrid:spplrid,
		},
		function(data,status)
		{
			alert(data);
			$("#button4").attr("disabled",false);
			load_selected_item();
		})
	}
	
	function insert_data_old()
	{
		///////For Check Blank fields//
		var jj=1;
		var chk=document.getElementsByClassName("imp")
		for(var i=0;i<chk.length;i++)
		if(chk[i].value=="")
		{
			jj=0;
			document.getElementById(chk[i].id).placeholder="Can not be Blank";	
		}
		var supplr=document.getElementById("selectsubstr").value;
		if(supplr==0)
		{
			alert("Please select a Supplier name..");
			$("#selectsubstr").focus();
			jj=0;
		}
		
		if(jj==1)
		{   
		///////end ////////
		
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"purchase_order_tmp",
			  ordrdate:$("#txtorddate").val(), 
			  supplrid:$("#selectsubstr").val(), 
			  orderno:$("#txtordo").val(),
			  itmcode:$("#txtcntrname").val(),
			  orqnt:$("#txtqnt").val(),
			 
		  },
		  function(data,status)
		   {
			 	   
			  
			   $("#selectsubstr").attr("disabled",true);
			   $("#button4").attr("disabled",false);
			   load_selected_item();
			   clearr();
			   $("#txtcntrname").focus();
		   })
	}}
	
	function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
		
		var supplr=$("#selectsubstr").val();
		if(supplr==0)
		{
			jj=0;
			alert("Please select a Sub Store  name..");
			$("#selectsubstr").focus();
		}
		
		if(jj==1)
		{   
		///////end ////////
		
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"purcahse_order_final",
			  supplrid:$("#selectsubstr").val(), 
			  orderno:$("#txtordo").val(),
			  ordrdate:$("#txtorddate").val(), 
			 
		  },
		  function(data,status)
		   {
			  
			   alert("Data saved");
			   $("#button4").attr("disabled",true);
			   $("#selectsubstr").attr("disabled",true);
			   //alert ("Data Saved");
			   $("#button5").focus();
			   clearr();
			   load_selected_item();
			   
		   })
	}}
	
	function get_id() //For Get Id
	{
		$.post("pages/load_id.php",
		{
			type:"purchase_order",
		},
		function(data,status)
		{
			$("#txtordo").val(data);
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
	
	
	function search_data()
	{
		$.post('pages/inv_load_display.php',
		{
			 type:"searchprcaseorder",
			 orderno:$("#txtordrid").val(),
		},
		function(data,status)
		{
		   
			var val=data.split("@");
			$("#txtordo").val(val[0]);
			$("#selectsubstr").val(val[1]);
			$("#txtorddate").val(val[2]);
			
			$("#btn5").val("Update");
			$("#btn8").attr("disabled", false);
			$("#btn8").focus();
			load_selected_item();
		})
	}
</script>
<style>
	.table-report
	{
		background:#FFFFFF;
	}
</style>
