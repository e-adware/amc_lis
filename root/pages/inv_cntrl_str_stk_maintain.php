<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<style>
	label
	{display:inline-block;}
</style>
<div class="container-fluid">
	<form id="form1" method="post">
		<div class="span6" style="margin-left:0px;">
			<table class="table table-condensed table-bordered" >
				<tr>
					<th>Name</th>
					<td>
						<input type="text" name="txtcntrname"  id="txtcntrname" value="" autocomplete="off" class="imp intext span4" readonly="readonly" placeholder="Name" />
					</td>
				</tr>
				<tr>
					<th>Item Code</th>
					<td>
						<input type="text" name="txtcid" id="txtcid" class="imp intext" readonly="readonly" placeholder="Item Code" />
					</td>
				</tr>
				<tr>
					<th>Batch No</th>
					<td>
						<input type="text" name="txtbatchno" id="txtbatchno" autocomplete="off" class="imp intext span2"  placeholder="Batch No" /> 
					</td>
				</tr>
				
				<tr>
					<th>Expire</th>
					<td>
						<input type="text" name="txtexpire" id="txtexpire" autocomplete="off" class="imp intext" placeholder="Expire Date (YYYY-MM-DD)" /> 
					</td>
				</tr>
				
				<tr>
					<th>GST</th>
					<td>
						<input type="text" name="txtgst" id="txtgst" autocomplete="off" class="imp intext span2"  placeholder="GST" /> 
					</td>
				</tr>
				
				<tr>
					<th>MRP<br/><small><i>(Strip)</i></small></th>
					<td>
						<input type="text" name="txtmrp" id="txtmrp" autocomplete="off" class="imp intext span2"  placeholder="MRP" /> 
					</td>
				</tr>
				
				<tr>
					<th>Cost Price<br/><small><i>(Strip)</i></small></th>
					<td>
						<input type="text" name="txtcostprice" id="txtcostprice" autocomplete="off" class="imp intext span2"  placeholder="Cost Price" /> 
					</td>
				</tr>
				
				
				
				<tr>
					<th>Quantity<br/><small><i>(Strip)</i></small></th>
					<td>
						<input type="text" name="txtqnt" id="txtqnt" autocomplete="off" class="imp intext span2" onkeyup="numentry('txtqnt')" placeholder="Quantity" />
					</td>
				</tr>
				
				<tr>
					<th>Rack No</th>
					<td>
						<input type="text" id="rack_no" autocomplete="off" class="imp intext span2" placeholder="Rack No" />
					</td>
				</tr>
				
				<tr>
					<td colspan="4" style="text-align:center">
						<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" /> 
						<input type="button" name="button4" id="button4" value="Done" onclick="save()" class="btn btn-info" />
						<!--<input type="button" name="button3" id="button3" value= "View" onclick="popitup1('pages/item_stock_rpt.php')" class="btn btn-success" /> -->
					</td>
				</tr>
				
			</table>
		</div>
		<div class="span5">
			<table class="table table-condensed table-bordered">
				<tr>
					<th>Search</th>
					<td> <input type="text" name="txtcustnm" id="txtcustnm"  autocomplete="off" class="intext span4" onkeyup="sel_pr(this.value,event)" placeholder="Search..." autofocus /></td>
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
		</div>
	</form>
	<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
</div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<script src="../js/jquery.peity.min.js"></script>

<script type="text/javascript">
	$(document).ready(function()
	{
		$("#txtexpire").datepicker({dateFormat: 'yy-mm-dd',minDate:'0',changeMonth:true,changeYear:true,yearRange:'c-10:c+10'});
		
		load_item();
		
		$("#txtexpire").change(function(e)
		{
			$("#txtmrp").focus();
		});
		$("#txtexpire").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#txtbatch").focus();
		});
		$("#txtbatchno").keyup(function(e)
		{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(e.keyCode==13 && $(this).val().trim()!="")
			{
				$("#txtexpire").focus();
			}
			else if(e.keyCode==27)
			{
				$("#txtcustnm").focus();
			}
			else if(e.ctrlKey && unicode==56 || e.ctrlKey && unicode==106)
			{
				if($("#txtcid").val().trim()!="")
				{
					item_require();
				}
			}
		});
		$("#txtmrp").keyup(function(e)
		{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(e.keyCode==13 && $(this).val().trim()!="" && parseInt($(this).val())!="0")
			{
				$("#txtcostprice").focus();
			}
			else if(e.ctrlKey && unicode==56 || e.ctrlKey && unicode==106)
			{
				if($("#txtcid").val().trim()!="")
				{
					item_require();
				}
			}
		});
		$("#txtcostprice").keyup(function(e)
		{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(e.keyCode==13 && $(this).val().trim()!="" && parseInt($(this).val())!="0")
			{
				$("#txtqnt").focus();
			}
			else if(e.ctrlKey && unicode==56 || e.ctrlKey && unicode==106)
			{
				if($("#txtcid").val().trim()!="")
				{
					item_require();
				}
			}
		});
		
		$("#txtqnt").keyup(function(e)
		{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(e.keyCode==13 && $(this).val().trim()!="" && parseInt($(this).val())!="0")
			{
				$("#rack_no").focus();
			}
			else if(e.ctrlKey && unicode==56 || e.ctrlKey && unicode==106)
			{
				if($("#txtcid").val().trim()!="")
				{
					item_require();
				}
			}
		});
		
		$("#rack_no").keyup(function(e)
		{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(e.keyCode==13)
			{
				$("#button4").focus();
			}
			else if(e.ctrlKey && unicode==56 || e.ctrlKey && unicode==106)
			{
				if($("#txtcid").val().trim()!="")
				{
					item_require();
				}
			}
		});
	});
	//------------------------------------------------------------------
	function item_require()
	{
		if($("#txtcid").val().trim()!="")
		{
			$("#loader").show();
			$.post("pages/inv_cntrl_stk_ajax.php",
			{
				type:"item_require",
				itm:$("#txtcid").val().trim(),
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				$.gritter.add(
				{
					//title:	'Normal notification',
					text:	'<h5 style="text-align:center;">'+data+'</h5>',
					time: 1000,
					sticky: false
				});
				$(".gritter-item").css("background","#237438");
			});
		}
	}
	//------------------------------------------------------------------
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
			$.post("pages/inv_cntrl_stk_ajax.php",
			{
				val:val,
				type:"inv_item_stock_maintain",
			},
			function(data,status)
			{
				$("#load_materil").html(data);
				doc_v=1;
				doc_sc=0;
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
		$.post("pages/inv_cntrl_stk_ajax.php",
		{
			type:"inv_item_stock_maintain",
		},
		function(data,status)
		{
			
			$("#load_materil").html(data);
		})
	}
	
	function val_load_new(id)
	{
		$.post("pages/inv_cntrl_stk_ajax.php",
		{
			type:"inv_stock_item_load",
			id:id,
		},
		function(data,status)
		{
			var val=data.split("#g#");
			$("#txtcid").val(val[0]);
			$("#txtcntrname").val(val[1]);
			$("#txtgst").val(val[2]);
			$("#txtmrp").val(val[3]);
			$("#rack_no").val(val[4]);
			$("#txtexpire").val('');
			$("#txtbatchno").focus();
			doc_v=1;
			doc_sc=0;
			//load_bch(val[0]);
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
		$.post("pages/inv_cntrl_stk_ajax.php",
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
		if($("#txtbatchno").val().trim()=="")
		{
			alert("Batch No Can not be blank");
			$("#txtbatchno").focus();
		}
		else if($("#txtqnt").val().trim()=="")
		{
			$("#txtqnt").focus();
		}
		//~ else if($("#txtgst").val()==0)
		//~ {
			//~ alert("GST Can not be Zero");
			//~ $("#txtgst").focus();
		//~ }
		else if($("#txtmrp").val().trim()=="")
		{
			//alert("MRP not be Zero");
			$("#txtmrp").focus();
		}
		else if(parseFloat($("#txtmrp").val().trim())==0)
		{
			//alert("MRP not be Zero");
			$("#txtmrp").focus();
		}
		else if($("#txtcostprice").val().trim()=="")
		{
			//alert("MRP not be Zero");
			$("#txtcostprice").focus();
		}
		else if(parseFloat($("#txtcostprice").val().trim())==0)
		{
			//alert("MRP not be Zero");
			$("#txtcostprice").focus();
		}
		else
		{
			$("#button4").attr("disabled",true);
			$.post("pages/inv_cntrl_stk_ajax.php",
			{
				type:"inv_stock_item_update",
				id:$("#txtcid").val().trim(),
				bch:$("#txtbatchno").val().trim(),
				vgst:$("#txtgst").val().trim(),
				vmrp:$("#txtmrp").val().trim(),
				cost:$("#txtcostprice").val().trim(),
				expirydt:$("#txtexpire").val().trim(),
				
				qnt:$("#txtqnt").val(),
				rack_no:$("#rack_no").val().trim(),
				
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clrr();
					show_notify();
				}, 1000);
			})
		}
	}
	function clrr()
	{
		$("#txtcid").val('');
		$("#txtcntrname").val('');
		$("#txtbatchno").val('');
		$("#txtexpire").val('');
		$("#txtqnt").val('');
		$("#txtgst").val('');
		$("#txtmrp").val('');
		$("#txtcostprice").val('');
		$("#rack_no").val('');
		$("#txtcustnm").focus();
		$("#button4").attr("disabled",false);
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
</script>
