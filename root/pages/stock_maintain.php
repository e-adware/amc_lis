<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Stock Maintainance</span></div>
</div>
<!--End-header-->
<style>
	label
	{
		display:inline-block;
		border: 1px dotted #AAA;
		padding:5px;
		padding-left:10px;
		padding-right:10px;
	}
	label:hover
	{
		cursor: pointer;
		border: 1px solid #888;
		box-shadow: 0px 0px 10px 0px #999;
		transition: 0.5s;
	}
</style>
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" >
			<tr>
				<td >Select</td>
					<td>
					<select id="ph" class="span3" onkeyup="next_tab(this.id,event)" onchange="load_item()" autofocus>
						<option value="0">Select</option>
						<?php
						$ids="1,2";
						$qsplr=mysqli_query($link,"select substore_id,substore_name from ph_sub_store where substore_id in ($ids) order by substore_name");
						while($qsplr1=mysqli_fetch_array($qsplr))
						{
					?>
						<option value="<?php echo $qsplr1['substore_id'];?>"><?php echo $qsplr1['substore_name'];?></option>
					<?php
						}
					?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th>Name</th>
				<td>
					<input type="text" name="txtcntrname"  id="txtcntrname" value="" autocomplete="off" class="imp intext span5" readonly="readonly" placeholder="Name" />
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
				<td id="load_bch">
					<select id="bch" onchange="load_exp()">
						<option value="0">Select</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<th>Expire</th>
				<td>
					<input type="text" name="txtexpire" id="txtexpire" autocomplete="off" class="imp intext" placeholder="Expire Date" readonly /> 
				</td>
			</tr>
			
			<tr>
				<th>Existing Quantity</th>
				<td>
					<input type="text" name="extqnt" id="extqnt" autocomplete="off" class="imp intext span2" readonly="readonly" placeholder="Existing Quantity" /> 
				</td>
			</tr>
			
			<tr>
				<th>Quantity</th>
				<td>
					<input type="text" name="txtqnt" id="txtqnt" autocomplete="off" class="imp intext span2" onkeyup="numentry('txtqnt')" placeholder="Quantity" />
					<label><input type="radio" name="sel" value="1" /> Add</label>
					<label><input type="radio" name="sel" value="0" /> Deduct</label>
				</td>
			</tr>
			
			<tr>
				<td colspan="4" style="text-align:center">
					<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" /> 
					<input type="button" name="button4" id="button4" value= "Done" onclick="save()" class="btn btn-info" />
					<!--<input type="button" name="button3" id="button3" value= "View" onclick="popitup1('pages/item_stock_rpt.php')" class="btn btn-success" /> -->
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<input type="text" name="txtcustnm" id="txtcustnm"  autocomplete="off" class="intext span4" onkeyup="sel_pr(this.value,event)" placeholder="Search..." />
		<div id="load_materil" style="max-height:400px;overflow-y:scroll;" >
			
		</div>
	</div>
</div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#txtexpire").datepicker({dateFormat: 'yy-mm-dd',minDate:'0',changeYear:true,yearRange:'c-10:c+10'});
		
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
			$("#loader").show();
			$.post("pages/stock_ajax.php",
			{
				val:val,
				ph:$("#ph").val(),
				type:"item_stock_maintain",
			},
			function(data,status)
			{
				$("#loader").hide();
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
		$("#bch option:not(:first)").remove();
		load_item();
	}
	
	function load_item()
	{
		$("#loader").show();
		$.post("pages/stock_ajax.php",
		{
			ph:$("#ph").val(),
			type:"item_stock_maintain",
		},
		function(data,status)
		{
			$("#loader").hide();
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
		$("#loader").show();
		$("#bch option:not(:first)").remove();
		$.post("pages/stock_ajax.php",
		{
			ph:$("#ph").val(),
			type:"stock_item_bch_load",
			id:id,
		},
		function(data,status)
		{
			$("#loader").hide();
			var val=data.split("@govinda@");
			for(var j=0; j<(val.length); j++)
			{
				$("#bch").append("<option value='"+val[j]+"'>"+val[j]+"</option>");
			}
			//$("#load_bch").html(data);
		})
	}
	function load_exp()
	{
		$("#loader").show();
		$.post("pages/stock_ajax.php",
		{
			type:"stock_item_exdate_load",
			id:$("#txtcid").val(),
			bch:$("#bch").val(),
			ph:$("#ph").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
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
			$("#button4").attr("disabled",true);
			$("#loader").show();
			$.post("pages/stock_ajax.php",
			{
				type:"stock_item_update",
				id:$("#txtcid").val(),
				bch:$("#bch").val(),
				extqnt:$("#extqnt").val(),
				qnt:$("#txtqnt").val(),
				opp:$("input[type='radio']:checked").val(),
				user:$("#user").text().trim(),
				ph:$("#ph").val(),
			},
			function(data,status)
			{
				$("#loader").hide();
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
		$("#bch").val('0');
		$("#txtexpire").val('');
		$("#extqnt").val('');
		$("#txtqnt").val('');
		$("#bch option:not(:first)").remove();
		$("input[type='radio']").attr('checked',false);
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
