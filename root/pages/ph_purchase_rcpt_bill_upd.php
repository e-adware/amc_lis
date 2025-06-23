<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Purchase Receipt Bill Update</span></div>
</div>
<!--End-header-->
<style>
	label
	{display:inline-block;}
</style>

<div class="container-fluid">
	<!--<form id="form1" method="post">
		<div class="span6" style="margin-left:0px;">
			<table class="table table-condensed table-bordered" >
				<tr>
					<td>Name</td>
					<td>
						<input type="text" name="txtcntrname"  id="txtcntrname" value="" autocomplete="off" class="imp intext span5" readonly="readonly" placeholder="Name" />
					</td>
				</tr>
				<tr>
					<td>Item Code</td>
					<td>
						<input type="text" name="txtcid" id="txtcid" class="imp intext" readonly="readonly" placeholder="Item Code" />
					</td>
				</tr>
				<tr>
					<td>Batch No </td>
					<td id="load_bch">
						<select id="bch">
							<option value="0">Select</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>GST</td>
					<td>
						<input type="text" name="txtgst" id="txtgst" autocomplete="off" class="imp intext" placeholder="GST" readonly /> %
					</td>
				</tr>
				
				<tr>
					<td>Expire</td>
					<td>
						<input type="text" name="txtexpire" id="txtexpire" autocomplete="off" class="imp intext" placeholder="Expire Date" /> (yyyy-mm-dd)
					</td>
				</tr>
				
				<tr>
					<td>MRP</td>
					<td>
						<input type="text" name="txtmrp" id="txtmrp" autocomplete="off" class="imp intext" placeholder="MRP" onkeyup="gst_calculate(this.value,event)" /> 
					</td>
				</tr>
				
				<tr>
					<td>Sale Price</td>
					<td>
						<input type="text" name="txtsaleprice" id="txtsaleprice" autocomplete="off" class="imp intext" placeholder="Sale Price" readonly /> 
					</td>
				</tr>
				
				<tr>
					<td colspan="4" style="text-align:center">
						<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" /> 
						<input type="button" name="button4" id="button4" value= "Done" onclick="save()" class="btn btn-info" />
					</td>
				</tr>
			</table>
		</div>
		<div class="span6">
			<div id="load_materil" style="max-height:400px;overflow-y:scroll;" >
				
			</div>
		</div>
	</form>-->
	<div class="span11">
		<table class="table table-condensed table-bordered" >
			<tr>
				<th>Select Supplier</th>
				<td>
					<select id="supp" class="span5" onchange="load_supp_bill()" autofocus>
						<option value="0">Select</option>
						<?php
						$sup=mysqli_query($link,"SELECT `id`, `name` FROM `ph_supplier_master` ORDER BY `name`");
						while($sp=mysqli_fetch_array($sup))
						{
						?>
						<option value="<?php echo $sp['id'];?>"><?php echo $sp['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Bill No</th>
				<td>
					<input type="text" list="browsrs" id="bill" class="span4" placeholder="Bill No." />
					<span id="bill_list"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" id="srch" value="Search" onclick="srch_bill()" class="btn btn-info" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span11">
		<div id="res" style="max-height:300px;overflow-y:scroll;" >
			
		</div>
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#txtexpire").datepicker({dateFormat: 'yy-mm-dd',minDate:'0',changeYear:true,yearRange:'c-10:c+10'});
		
		//load_item();
		
		$("#txtexpire").change(function(e)
		{
			$("#txtbatch").focus();
		});
		$("#txtexpire").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#txtbatch").focus();
		});
		$("#txtbatch").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			$("#txtqnt").focus();
		});
		$("#txtqnt").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" && parseInt($(this).val())!="0")
			$("#txtmrp").focus();
		});
		$("#txtmrp").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" && parseInt($(this).val())!="0")
			$("#txtcostprice").focus();
		});
		$("#txtcostprice").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" && parseInt($(this).val())!="0")
			$("#button4").focus();
		});
	//-------------------------------------------------------------------------------------
		$("#supp").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="0")
			$("#bill").focus();
		});
		$("#bill").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="")
			$("#srch").focus();
		});
	});
	
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
				$("#rad_test"+doc_v).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
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
					$("#load_materil").scrollTop(doc_sc)	
				}
			}
		}
		else
		{
			$.post("pages/global_load_g.php",
			{
				val:val,
				type:"stock_entry",
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
		
	function clearr()//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";	
		}
		load_item();
	}
	
	function gst_calculate(val,c)
	{
		var a=parseInt($("#txtmrp").val());
		var b=parseInt($("#txtgst").val());
		var c=0;
		var d=0;
		c=a-(a*(100/(100+b)));
		c=c.toFixed(2);
		d=a-c;
		$("#txtsaleprice").val(d);
	}
	function load_item()
	{
		$.post("pages/stock_ajax.php",
		{
			type:"item_stock_maintain",
		},
		function(data,status)
		{
			$("#load_materil").html(data);
		})
	}
	
	function val_load_new(id)
	{
		$.post("pages/stock_ajax.php",
		{
			type:"stock_item_load",
			id:id,
		},
		function(data,status)
		{
			var val=data.split("#g#");
			$("#txtcid").val(val[0]);
			$("#txtcntrname").val(val[1]);
			$("#txtexpire").val('');
			$("#extqnt").val('');
			load_bch(val[0]);
		})
	}
	
	function load_bch(id)
	{
		$.post("pages/stock_ajax.php",
		{
			type:"stock_item_bch_load",
			id:id,
		},
		function(data,status)
		{
			//var val=data.split("#g#");
			$("#load_bch").html(data);
		})
	}
	function load_exp()
	{
		$.post("pages/stock_ajax.php",
		{
			type:"purchs_bil_edit",
			id:$("#txtcid").val(),
			bch:$("#bch").val(),
		},
		function(data,status)
		{
			var val=data.split("#g#");
			$("#txtexpire").val(val[0]);
			$("#txtmrp").val(val[1]);
			$("#txtsaleprice").val(val[2]);
			$("#txtgst").val(val[3]);
			$("#txtmrp").focus();
		})
	}
	function save()
	{
		var jj=1;
		if($("#bch").val()=="0")
		{
			alert("Please Select a Batch No..");
			jj=0;
			$("#bch").focus();
		}
		 if($("#txtmrp").val()=="")
		{
			alert("MRP Can not Be Blank..");
			jj=0;
			$("#txtmrp").focus();
		}
		
		 if($("#txtexpire").val()=="")
		{
			alert("Please enter the expiry date..");
			jj=0;
			$("#txtexpire").focus();
		}
		 if($("#txtsaleprice").val()=="")
		{
			alert("Sale Price Can not Be Blank..");
			jj=0;
			$("#txtsaleprice").focus();
		}
		
		if(jj==1)
		{
			$.post("pages/stock_ajax.php",
			{
				type:"purchs_bil_edit_save",
				id:$("#txtcid").val(),
				bch:$("#bch").val(),
				vexpirydat:$("#txtexpire").val(),
				vmrp:$("#txtmrp").val(),
				vsaleprice:$("#txtsaleprice").val(),
				
			},
			function(data,status)
			{
			   alert("Done");
			   clrr();
			})
		}
	}
	
	function clrr()
	{
		$("#txtcid").val('');
		$("#txtcntrname").val('');
		$("#bch").val('0');
		$("#txtexpire").val('');
		$("#txtmrp").val('');
		$("#txtsaleprice").val('');
		
	}
	
	function srch_bill()
	{
		$.post("pages/ph_purchase_bill_upd_ajax.php",
		{
			supp:$("#supp").val(),
			bill:$("#bill").val(),
			type:"srch_bill",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function calc_tot_qnt(val,typ,num,e)
	{
		if(val=="")
		{
			val=0;
		}
		var tot=0;
		if(typ=="qnt")
		{
			var f=$("#free"+num).val().trim();
			if(f=="")
			{
				f=0;
			}
			var qnt=parseInt(val);
			var fre=parseInt(f);
			tot=qnt+fre;
		}
		if(typ=="free")
		{
			var q=$("#qnt"+num).val().trim();
			if(q=="")
			{
				q=0;
			}
			var qnt=parseInt(q);
			var fre=parseInt(val);
			tot=qnt+fre;
		}
		$("#tot"+num).val(tot);
	}
	
	function load_supp_bill()
	{
		$.post("pages/ph_purchase_bill_upd_ajax.php",
		{
			supp:$("#supp").val(),
			type:"load_supp_bill",
		},
		function(data,status)
		{
			$("#bill_list").html(data);
		})
	}
	
	function upd_purchase_all(num,stk)
	{
		//alert(num+"-"+stk);
		$("#u_btn"+num).attr("disabled",true);
		$.post("pages/ph_purchase_bill_upd_ajax.php",
		{
			supp:$("#suppid").val().trim(),
			bill:$("#billno").val().trim(),
			itm:$("#itm"+num).val().trim(),
			bch:$("#bch"+num).val().trim(),
			expiry:$("#exp"+num).val().trim(),
			cost:$("#cost"+num).val().trim(),
			mrp:$("#mrp"+num).val().trim(),
			sale:$("#sale"+num).val().trim(),
			disc:$("#disc"+num).val().trim(),
			gst:$("#gst"+num).val().trim(),
			qnt:$("#qnt"+num).val().trim(),
			free:$("#free"+num).val().trim(),
			stk:stk,
			type:"upd_purchase_all",
		},
		function(data,status)
		{
			alert(data);
			srch_bill();
			//$("#bill_list").html(data);
		})
	}
	
	function calc_gst(val,num,e)
	{
		//alert(val);
		if(val=="")
		{
			a=0;
		}
		else
		{
			a=parseFloat(val);
		}
		var gst=parseFloat($("#gst"+num).val());
		var c=0;
		var d=0;
		c=a-(a*(100/(100+gst)));
		c=c.toFixed(2);
		d=a-c;
		d=d.toFixed(2);
		$("#sale"+num).val(d);
	}
</script>
