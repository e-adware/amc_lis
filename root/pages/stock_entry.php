<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<table class="table table-condensed table-report">
			<tr style="display:none;">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td colspan="2">
					<b>Select</b><br/>
					<select id="ph" class="span5" onkeyup="next_tab(this.id,event)" onchange="load_item('')" autofocus>
						<option value="0">Select</option>
						<?php
						$ids="1";
						$qsplr=mysqli_query($link,"select substore_id,substore_name from inv_sub_store where substore_id in ($ids) order by substore_name");
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
				<td colspan="2">
					<b>Name</b><br/>
					<input type="text" name="txtcntrname" id="txtcntrname" value="" autocomplete="off" class="span5" readonly="readonly" placeholder="Name" />
				</td>
			</tr>
			<tr>
				<td>
					<b>Item Code</b></br>
					<input type="text" name="txtcid" id="txtcid" class="span2" readonly="readonly" placeholder="Item Code" />
				</td>
				<td>
					<b>GST</b><br/>
					<select id="txtgst" class="span2">
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
					<!--<input type="text" name="txtgst" id="txtgst" autocomplete="off" class="span2" placeholder="GST" readonly /> %-->
				</td>
			</tr>
			<tr>
				<td>
					<b>Pack Qnt</b><br/>
					<input type="text" id="pack_qnt" class="span2" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Pack Qnt" />
				</td>
				<td>
					<b>Rack No</b><br/>
					<input type="text" id="rack_no" class="span2" placeholder="Rack No" />
				</td>
			</tr>
			<!--<tr>
				<th>Expire</th>
				<td>
					<input type="text" name="txtexpire" id="txtexpire"  autocomplete="off" class="imp intext" placeholder="Expire Date" /> 
				</td>
			</tr>
			
			<tr>
				<th>Batch No</th>
				<td>
					<input type="text" name="txtbatch" id="txtbatch"  autocomplete="off" class="imp intext span2" placeholder="Batch No" />
					<b>Quantity :</b> <input type="text" name="txtqnt" id="txtqnt" autocomplete="off" class="imp intext span2" onkeyup="chk_dec(this,event)" placeholder="Quantity" /> 
				</td>
			</tr>
			
			<tr>
				<th>MRP (Strip)</th>
				<td>
					<input type="text" name="txtmrp" id="txtmrp"  autocomplete="off" class="imp intext span2" placeholder="M R P" onkeyup="chk_dec(this,event);gst_calculate(this.value,event)" />
					<b>Sale Price :</b>
					<input type="text" name="txtsaleprice" id="txtsaleprice" autocomplete="off" class="imp intext span2" readonly="readonly" placeholder="Sale Price" />
				</td>
			</tr>
			<tr>
				<th>Cost Price (Strip)</th>
				<td>
					<input type="text" name="txtcostprice" id="txtcostprice" autocomplete="off" class="imp intext span2" onkeyup="chk_dec(this,event)" placeholder="Cost Price" />
				</td>
			</tr>-->
		</table>
		<table class="table table-condensed table-report" id="bch_table">
			<tr>
				<th>Expiry</th>
				<th>Batch No</th>
				<th>MRP (Strip)</th>
				<th>Cost Price (Strip)</th>
				<th>Quantity <button type="button" class="btn btn-primary btn-mini" onclick="add_tr()"><i class="icon-plus icon-large"></i></button></th>
			</tr>
			<?php
			for($i=1; $i<=5; $i++)
			{
			?>
			<tr class="all_tr">
				<td>
					<input type="text" id="txtexpire<?php echo $i;?>"  autocomplete="off" class="imp intext expdt" onkeyup="input_row(this,event)" placeholder="Expire Date" readonly />
				</td>
				<td>
					<input type="text" id="txtbatch<?php echo $i;?>"  autocomplete="off" class="imp intext" onkeyup="input_row(this,event)" placeholder="Batch No" />
				</td>
				<td>
					<input type="text" id="txtmrp<?php echo $i;?>" autocomplete="off" class="imp intext" placeholder="M R P" onkeyup="chk_dec(this,event);gst_calculate(this,event,'<?php echo $i;?>');input_row(this,event);" />
					<input type="hidden" id="txtsaleprice<?php echo $i;?>" autocomplete="off" class="imp intext" readonly="readonly" placeholder="Sale Price" />
				</td>
				<td>
					<input type="text" id="txtcostprice<?php echo $i;?>" autocomplete="off" class="imp intext" onkeyup="chk_dec(this,event);input_row(this,event)" placeholder="Cost Price" />
				</td>
				<td>
					<input type="text" id="txtqnt<?php echo $i;?>" autocomplete="off" class="imp intext" onkeyup="chk_dec(this,event);input_row(this,event)" placeholder="Quantity" />
				</td>
			</tr>
			<?php
			}
			?>
			<tr id="btn_tr">
				<td colspan="5" style="text-align:center">
					<input type="button" name="button2" id="button2" value="Reset" onclick="clearr()" class="btn btn-danger" /> 
					<input type="button" name="button4" id="button4" value="Done" onclick="insert_data_final()" class="btn btn-info" />
					<!--<input type="button" name="button3" id="button3" value= "View" onclick="popitup1('pages/item_stock_rpt.php')" class="btn btn-success" /> -->
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<input type="text" name="txtcustnm" id="txtcustnm"  autocomplete="off" class="intext span4" placeholder="Search..." />
		<div id="load_materil" style="max-height:400px;overflow-y:scroll;" >
			
		</div>
		<div id="view_data"></div>
	</div>
	<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
</div>
<style>
	.intext
	{
		width:80px;
	}
	#view_data
	{
		position:absolute;
		display:none;
		border:1px solid #AAA;
		background:#FFF;
		box-shadow:0px 0px 10px 2px #AAA;
		padding:2px;
		cursor:move;
		height:100px;
		width:380px;
		max-height: 200px;
		max-width:400px;
		//overflow-y:scroll;
		z-index:999;
	}
</style>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<script type="text/javascript">
	$(function()
	{
		$( "#view_data" ).draggable({ containment: "body", scroll: false });
	});
	$(document).ready(function()
	{
		var doc_v=1;
		var doc_sc=0;
		
		var timeout = null; // Init a timeout variable to be used below // Listen for keystroke events
		
		$(".expdt").datepicker({dateFormat: 'yy-mm-dd',minDate:'0',changeYear:true,changeMonth:true,yearRange:'c-10:c+10'});
		//load_item('');
		
		var srch = document.getElementById('txtcustnm'); // Get the input box
		srch.onkeyup = function(e) // Init a timeout variable to be used below
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
			else if(unicode==27)
			{
				$("#ph").focus();
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
				clearTimeout(timeout);
				timeout = setTimeout(function()
				{
					//alert('Input Value : '+ srch.value);
					load_item(srch.value);
					doc_v=1;
					doc_sc=0;
				}, 500);
			}
		};
		
		$("#rack_no").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#txtexpire1").focus();
		});
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
			if(e.keyCode==13 && $(this).val()!="" && parseFloat($(this).val())!="0" && $(this).hasClass("err")==false)
			$("#txtmrp").focus();
		});
		$("#txtmrp").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" && parseFloat($(this).val())!="0" && $(this).hasClass("err")==false)
			$("#txtcostprice").focus();
		});
		$("#txtcostprice").keyup(function(e)
		{
			if(e.keyCode==13 && $(this).val()!="" && parseFloat($(this).val())!="0" && $(this).hasClass("err")==false)
			$("#button4").focus();
		});
		
	});
	
	function add_tr()
	{
		var len=$(".all_tr").length;
		var all_tr="<tr class='all_tr'>";
		all_tr+='<td><input type="text" id=\"txtexpire'+(len+1)+'\" autocomplete="off" class="imp intext expdt" onkeyup="input_row(this,event)" placeholder="Expire Date" readonly /></td>';
		all_tr+='<td><input type="text" id=\"txtbatch'+(len+1)+'\"  autocomplete="off" class="imp intext" onkeyup="input_row(this,event)" placeholder="Batch No" /></td>';
		all_tr+='<td><input type="text" id=\"txtmrp'+(len+1)+'\" autocomplete="off" class="imp intext" placeholder="M R P" onkeyup=\"chk_dec(this,event);gst_calculate(this,event,\''+(len+1)+'\');input_row(this,event);\" /><input type="hidden" id=\"txtsaleprice'+(len+1)+'\" autocomplete="off" class="imp intext" readonly="readonly" placeholder="Sale Price" /></td>';
		all_tr+='<td><input type="text" id=\"txtcostprice'+(len+1)+'\" autocomplete="off" class="imp intext" onkeyup="chk_dec(this,event);input_row(this,event)" placeholder="Cost Price" /></td>';
		all_tr+='<td><input type="text" id=\"txtqnt'+(len+1)+'\" autocomplete="off" class="imp intext" onkeyup="chk_dec(this,event);input_row(this,event)" placeholder="Quantity" /></td>';
		all_tr+="</tr>";
		$('#btn_tr').before(all_tr);
		$(".expdt").datepicker({dateFormat: 'yy-mm-dd',minDate:'0',changeYear:true,changeMonth:true,yearRange:'c-10:c+10'});
	}
	function input_row(ths,e)
	{
		var row=$(ths).closest("tr").index();
		var row_index = $(ths).parent().parent().index();
		var col_index = $(ths).parent().index();
		
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==27)
		{
			$("#button4").focus();
		}
		if(unicode==13)
		{
			//$("#txtcntrname").val($(ths).index(".imp")+" No");
			if($(ths).parent().next('td').find('input.imp').length>0)
			{
				$(ths).parent().next('td').find('input.imp').focus();
			}
			else if($(".all_tr:eq("+row+")").find("td:eq(0)").find("input.imp").length>0)
			{
				$(".all_tr:eq("+row+")").find("td:eq(0)").find("input.imp").focus();
			}
			else
			{
				$("#button4").focus();
			}
		}
	}
	function next_tab(id,e)
	{
		$("#"+id).css({"border":"","box-shadow":""});
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(id=="ph" && $("#"+id).val()!="0")
			{
				$("#txtcustnm").focus();
			}
			else
			{
				$("#"+id).css({"border":"1px solid #FF0000","box-shadow":"0px 0px 8px 2px #FF3131"});
			}
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
	
	function gst_calculate(ths,e,n)
	{
		var mrp=$(ths).val().trim();
		if(mrp=="" || $(ths).hasClass("err")==true)
		{
			mrp=0;
		}
		var a=parseFloat(mrp);
		var p=parseInt($("#pack_qnt").val().trim());
		var b=parseInt($("#txtgst").val().trim());
		a=(a/p);
		var c=0;
		var d=0;
		c=a-(a*(100/(100+b)));
		c=c.toFixed(2);
		d=a-c;
		$("#txtsaleprice"+n).val(d);
	}
	
	function grt_alr(msg,n)
	{
		$.gritter.add(
		{
			//title:	'Normal notification',
			text:	'<h5 style="text-align:center;">'+msg+'</h5>',
			time: 1000,
			sticky: false
		});
		if(n>0)
		{
			$(".gritter-item").css("background","#237438");
		}
	}
	function clearr()//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";	
		}
		$("#txtcustnm").focus();
		load_item();
	}
	
	function load_item(val)
	{
		if($("#ph").val()=="0")
		{
			$("#load_materil").empty();
		}
		else
		{
			$.post("pages/stock_entry_ajax.php",
			{
				ph:$("#ph").val(),
				val:val,
				type:1,
			},
			function(data,status)
			{
				$("#load_materil").html(data);
			})
		}
	}
	
	function val_load_new(id)
	{
		$.post("pages/stock_entry_ajax.php",
		{
			type:2,
			id:id,
			ph:$("#ph").val(),
		},
		function(data,status)
		{
			var val=data.split("#g#");
			$("#txtcid").val(val[0]);	
			$("#txtcntrname").val(val[1]);	
			$("#txtgst").val(val[2]);
			$("#rack_no").val(val[3]);
			$("#pack_qnt").val(val[4]);
			document.getElementById("rack_no").focus();
			load_stock_det(id);
		})
	}
	function load_stock_det(id)
	{
		$("#view_data").hide();
		$.post("pages/stock_entry_ajax.php",
		{
			type:5,
			id:id,
			ph:$("#ph").val(),
		},
		function(data,status)
		{
			$("#view_data").html(data).css("display","inline-block");
			$("#view_data").scrollTop(0);
		});
	}
	function chk_dec(ths,e)
	{
		var reg = /^\d+(?:\.\d{1,2})?$/;
		var val=$(ths).val();
		if(val=="")
		{
			$(ths).css({"border":"","box-shadow":""});
			$(ths).removeClass("err");
		}
		else
		{
			if(!reg.test(val))
			{
				$(ths).css({"border":"1px solid #FF0000","box-shadow":"0px 0px 8px 2px #FF3131"});
				$(ths).addClass("err");
				return true;
			}
			else
			{
				$(ths).css({"border":"","box-shadow":""});
				$(ths).removeClass("err");
			}
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
	function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
		//~ var chk1=document.getElementsByClassName("imp");
		//~ for(var k=0;k<chk1.length;k++) 
		//~ if(chk1[k].value=="")
		//~ {
			//~ jj=0;
			//~ document.getElementById(chk1[k].id).placeholder="Can not be blank";
		//~ }
		var all="";
		var all_tr=$(".all_tr");
		for(var i=0; i<all_tr.length; i++) 
		{
			if($(".all_tr:eq("+i+")").find('td:eq(0) input:first').val().trim()!="" && $(".all_tr:eq("+i+")").find('td:eq(1) input:first').val().trim()!="" && $(".all_tr:eq("+i+")").find('td:eq(2) input:first').val().trim()!="" && $(".all_tr:eq("+i+")").find('td:eq(4) input:first').val().trim()!="")
			{
				all+=$(".all_tr:eq("+i+")").find('td:eq(0) input:first').val().trim()+"@@@"+$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val().trim()+"@@@"+$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val().trim()+"@@@"+$(".all_tr:eq("+i+")").find('td:eq(2) input:last').val().trim()+"@@@"+$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val().trim()+"@@@"+$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val().trim()+"@@@#%#";
			}
		}
		//alert(all);
		if($("#ph").val()=="0")
		{
			grt_alr("Select Pharmacy",0);
			$("#ph").focus();
			jj=0;
		}
		if($("#pack_qnt").val().trim()=="" || $("#pack_qnt").val().trim()=="0" || parseInt($("#pack_qnt").val().trim())<=0)
		{
			grt_alr("Invalid packing quantity",0);
			$("#pack_qnt").focus();
			jj=0;
		}
		if($(".err").length>0)
		{
			$(".err:first").focus();
			grt_alr("Error",0);
			jj=0;
		}
		if(all=="")
		{
			grt_alr("No details entered",0);
			jj=0;
		}
		if(jj==1)
		{
			//grt_alr(all,1);
			$("#button4").attr("disabled",true);
			$("#loader").show();
			$.post("pages/stock_entry_ajax.php",
			{
				type:4,
				ph:$("#ph").val(),
				itmid:$("#txtcid").val().trim(),
				rack_no:$("#rack_no").val().trim(),
				//expiry:$("#txtexpire").val().trim(),
				//batch:$("#txtbatch").val().trim(),
				//qnt:$("#txtqnt").val().trim(),
				//mrp:$("#txtmrp").val().trim(),
				//cost:$("#txtcostprice").val(),
				gst:$("#txtgst").val().trim(),
				//saleprice:$("#txtsaleprice").val().trim(),
				pack_qnt:$("#pack_qnt").val().trim(),
				user:$("#user").text().trim(),
				all:all,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				var msg="Error";
				if(data>0)
				{
					msg="Done";
				}
				grt_alr(msg,data);
				clear_all();
				show_notify();
				$("#button4").attr("disabled",false);
				//~ bootbox.dialog({ message: data});
				//~ setTimeout(function()
				//~ {
					//~ bootbox.hideAll();
					//~ $("#button4").attr("disabled",false);
					//~ clearr();
					//~ show_notify();
					//~ $("#txtcustnm").focus();
				//~ }, 1000);
			})
		}
	}
	function clear_all()
	{
		$("#txtcntrname").val("");
		$("#txtcid").val("");
		$("#txtgst").val("");
		$("#pack_qnt").val("");
		$("#rack_no").val("");
		$(".imp").val("");
		$("#txtcustnm").val("").focus();
	}
</script>
