<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Stock Update</span></div>
</div>
<!--End-header-->
<style>
	label
	{display:inline-block;}
	.table tr:hover
	{
		background:none;
	}
</style>
<div class="container-fluid">
	<form id="form1" method="post">
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
					<td colspan="2" id="load_bch">
						<input type='text' class='span2' disabled='disabled' value='BATCH NO' placeholder='Batch No' /> <input type='text' class='span2' disabled='disabled' placeholder='Quantity' value='QUANTITY' /> <input type='text' class='span2' disabled='disabled' placeholder='yyyy-mm' value='EXP DATE (yyyy-mm)' />
						<!--<select id="bch">
							<option value="0">Select</option>
						</select>-->
					</td>
				</tr>
				
				<tr style="display:none;">
					<td>Expire</td>
					<td>
						<input type="text" name="txtexpire" id="txtexpire" autocomplete="off" class="imp intext" placeholder="Expire Date" /> 
					</td>
				</tr>
				
				<tr style="display:none;">
					<td>Existing Quantity</td>
					<td>
						<input type="text" name="extqnt" id="extqnt" autocomplete="off" class="imp intext span2" readonly="readonly" placeholder="Existing Quantity" /> 
					</td>
				</tr>
				
				<tr style="display:none;">
					<td>Quantity</td>
					<td>
						<input type="text" name="txtqnt" id="txtqnt" autocomplete="off" class="imp intext span2" onkeyup="numentry('txtqnt')" placeholder="Quantity" />
						<label><input type="radio" name="sel" value="1" /> Add</label>
						<label><input type="radio" name="sel" value="0" /> Deduct</label>
					</td>
				</tr>
				
				<tr style="display:none;">
					<td colspan="4" style="text-align:center">
						<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" /> 
						<input type="button" name="button4" id="button4" value= "Done" onclick="save()" class="btn btn-info" />
						<!--<input type="button" name="button3" id="button3" value= "View" onclick="popitup1('pages/item_stock_rpt.php')" class="btn btn-success" /> -->
					</td>
				</tr>
			</table>
		</div>
		<div class="span5">
			<table class="table table-condensed table-bordered">
				<tr>
					<td>Search</td>
					<td> <input type="text" name="txtcustnm" id="txtcustnm"  autocomplete="off" class="intext span4" onkeyup="sel_pr(this.value,event)" placeholder="Search..." /></td>
				</tr>
			</table>
			<!--<table class="table table-condensed">
				<tr>
					<th>Item Code</th>
					<th width="70%">Item Name</th>
					<th>MRP</th>
				</tr>
			</table>-->
			<div id="load_materil" style="max-height:400px;overflow-y:scroll;" >
				
			</div>
			<script>
			load_item();
			</script>
		</div>
	</form>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script type="text/javascript">
	$(document).ready(function()
	{
		//$("#txtexpire").datepicker({dateFormat: 'yy-mm-dd',minDate:'0',changeYear:true,yearRange:'c-10:c+10'});
		
		load_item();
		
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
			type:"stock_update_load_batch",
			id:id,
		},
		function(data,status)
		{
			//var val=data.split("#g#");
			$("#load_bch").html(data);
		})
	}
	function upd_stk_qnt(sl,val,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			//alert(sl+"-"+val);
			$.post("pages/stock_ajax.php",
			{
				sl:sl,
				val:val,
				type:"upd_stk_qnt",
			},
			function(data,status)
			{
				val_load_new($("#txtcid").val());
				setTimeout(function()
				{
					$("#qnt"+sl).css("border","2px solid #1F6E31");
					$("#exp"+sl).focus();
				},100);
			})
		}
	}
	function upd_stk_exp(sl,val,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			//alert(val.indexOf('-'));
			//alert(sl+"-"+val);
			var dt=val.split("-");
			var mn=parseInt(dt[1]);
			if(val.length!=7)
			{
				$("#exp"+sl).css("border","2px solid #E0141E");
			}
			else if(val.indexOf('-')==(-1))
			{
				$("#exp"+sl).css("border","2px solid #E0141E");
			}
			else if(val.indexOf('-')!=4)
			{
				$("#exp"+sl).css("border","2px solid #E0141E");
			}
			else if(mn>12)
			{
				$("#exp"+sl).css("border","2px solid #E0141E");
			}
			else
			{
				$.post("pages/stock_ajax.php",
				{
					sl:sl,
					val:val,
					type:"upd_stk_exp",
				},
				function(data,status)
				{
					//val_load_new($("#txtcid").val());
					$("#exp"+sl).css("border","2px solid #1F6E31");
				})
			}
		}
	}
	function val_test(th)
	{
		if(/\D/g.test(th.value))th.value=th.value.replace(/\D/g,'');
	}
	function clean_all()
	{
		$(".qnt_txt").css('border','');
	}
	function load_exp()
	{
		$.post("pages/stock_ajax.php",
		{
			type:"stock_item_exdate_load",
			id:$("#txtcid").val(),
			bch:$("#bch").val(),
		},
		function(data,status)
		{
			var val=data.split("#g#");
			$("#txtexpire").val(val[0]);
			$("#extqnt").val(val[1]);
			$("#txtqnt").focus();
		})
	}
	function save()
	{
		if($("#bch").val()=="0")
		{
			$("#bch").focus();
		}
		else if($("#txtqnt").val()=="")
		{
			$("#txtqnt").focus();
		}
		else if($("input[type='radio']:checked").length==0)
		{
			//$("#txtqnt").focus();
			alert();
		}
		else if($("input[type='radio']:checked").val()=="0" && parseInt($("#txtqnt").val())>parseInt($("#extqnt").val()))
		{
			$("#txtqnt").focus();
		}
		else
		{
			$.post("pages/stock_ajax.php",
			{
				type:"stock_item_update",
				id:$("#txtcid").val(),
				bch:$("#bch").val(),
				extqnt:$("#extqnt").val(),
				qnt:$("#txtqnt").val(),
				opp:$("input[type='radio']:checked").val(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clrr();
				}, 1000);
			})
		}
	}
	function clrr()
	{
		$("#txtcid").val('');
		$("#txtcntrname").val('');
		$("#bch").val('0');
		$("#txtexpire").val('');
		$("#extqnt").val('');
		$("#txtqnt").val('');
		$("input[type='radio']").attr('checked',false);
	}
	function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
		var chk1=document.getElementsByClassName("imp");
		for(var k=0;k<chk1.length;k++) 
		if(chk1[k].value=="")
		{
			jj=0;
			document.getElementById(chk1[k].id).placeholder="Can not be blank";
		}
		
		if(jj==1)
		{
			$.post("pages/global_insert_data_g.php",
			{
				type:"save_stock_entry",
				itmid:$("#txtcid").val(),
				expiry:$("#txtexpire").val(),
				batch:$("#txtbatch").val(),
				qnt:$("#txtqnt").val(),
				mrp:$("#txtmrp").val(),
				cost:$("#txtcostprice").val(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clearr();
				}, 1000);
			})
		}
	}
	load_item();
</script>
