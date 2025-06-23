<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<input type="hidden" id="chk_val1" value="0" />
	<input type="hidden" id="chk_val2" value="0" />
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Select Sub Store</th>
			<td>
				<select id="sub_dept" class="span3" autofocus>
					<option value="0">Select</option>
					<?php
						$qsplr=mysqli_query($link,"select substore_id,substore_name from inv_sub_store order by substore_name");
						while($qsplr1=mysqli_fetch_array($qsplr))
						{
					?>
						<option value="<?php echo $qsplr1['substore_id'];?>"><?php echo $qsplr1['substore_name'];?></option>
					<?php
						}
					?>
				</select>
			</td>
			<th>Issue To</th>
			<td>
				<input type="text" id="issue_to" class="span3" placeholder="Issue To" />
				<div id="deptPrevIssue">
					
				</div>
			</td>
		</tr>
		<tr style="display:none;">
			<th>Issue No</th>
			<td>
				<input type="text" id="issue_no" class="span3" placeholder="Issue No" />
			</td>
		</tr>
		<tr>
		   <th>Item Name</th>
			<td colspan="3">
				<input type="text" id="r_doc" class="span5" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onblur="$('#ref_doc').fadeOut(200)" value="" placeholder="Item Name" />
				<input type="text" id="doc_id" style="display:none;" value="">
				<input type="text" id="gst" style="display:none;" value="">
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
				<input type="text" id="txtavailstk" class="span2" onkeyup="numentry('txtqnt')" placeholder="avail Stock" disabled />
				<input type="text" id="txtmrp"  autocomplete="off" class="imp intext" onkeyup="" placeholder="MRP" style="width:100px" disabled />
				<input type="text" id="txtrate"  autocomplete="off" class="imp intext" onkeyup="" placeholder="Rate" style="width:100px" disabled />
			</td>
		</tr>
		<tr>
			<th>Issue Quantity</th>
			<td>
				<input type="text" id="txtqnt"  autocomplete="off" class="imp intext" onkeyup="numentry('txtqnt')" placeholder="Quantity" style="width:100px" />
			</td>
			<th>Expiry</th>
			<td>
				<input type="text" id="txtexpiry" class="span2" placeholder="Expiry" disabled />
			</td>
		</tr>
		
		<tr>
			<td colspan="4" style="text-align:center">
				<input type="button" name="button2" id="button2" value="Reset" onclick="reset_all()" class="btn btn-danger" /> 
				<input type="button" name="button" id="button" value= "Add" onclick="add_temp()" class="btn btn-default" />
				<input type="button" name="button4" id="button4" value= "Done" onclick="save_final()" class="btn btn-default" />
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
<style>
#deptPrevIssue
{
	background-color: #FFF;
	border: 1px solid;
	box-shadow: 0px 0px 12px 0px #888;
	display: none;
	max-height: 300px;
	position: absolute;
	width: 350px;
	top: 6%;
	right: 1%;
	z-index: 1000;
}
</style>
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#sub_dept").keyup(function(e)
		{
			//$(this).css("border","");
			//$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				{
					//$(this).css("border","1px solid #f00");
					$("#sub_dept").focus();
				}
				else
				{
					//$(this).css("border","");
					$("#issue_to").focus();
					//$('#selectcategory').select2('focus');
				}
			}
		});
		
		$("#issue_to").keyup(function(e)
		{
			if(e.keyCode==13 && $("#issue_to").val().trim()!="")
			{
				$("#r_doc").focus();
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
			if(e.keyCode==13)
			$("#button").focus();
		});
		
		$("#txtcntrname").keyup(function(e)
		{
			if(e.keyCode==27)
			$("#button4").focus();
		});
	});
	
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
	
	function val_load_new()/// 278242
	{
		$.post("pages/inv_main_str_itm_issue_ajax.php",
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
			$("#txtmrp").val(val[1]);
			$("#txtexpiry").val(val[2]);
			$("#txtrate").val(val[3]);
			//$("#txtqnt").focus();
		})
	}
	
	function next_tab(id,e)
	{
		if(id=="batch_no")
		{
			if(e.keyCode==13 && $("#"+id).val()!="0")
			{
				$("#txtqnt").focus();
			}
		}
		if(id=="txtqnt")
		{
			if(e.keyCode==13 && $("#"+id).val().trim()!="")
			{
				$("#button").focus();
			}
		}
	}
	function add_temp()
	{
		if($("#sub_dept").val()=="0")
		{
			$("#sub_dept").focus();
		}
		else if($("#issue_to").val().trim()=="")
		{
			$("#issue_to").focus();
		}
		/*
		else if($("#issue_no").val().trim()=="")
		{
			$("#issue_no").focus();
		}
		//*/
		else if($("#doc_id").val()=="")
		{
			$("#r_doc").focus();
		}
		else if($("#batch_no").val()=="0")
		{
			$("#batch_no").focus();
		}
		else if($("#txtqnt").val().trim()=="")
		{
			$("#txtqnt").focus();
		}
		else if(parseInt($("#txtqnt").val().trim())==0 || parseInt($("#txtqnt").val().trim())<0)
		{
			$("#txtqnt").focus();
		}
		else if(parseInt($("#txtqnt").val().trim()) > parseInt($("#txtavailstk").val().trim()))
		{
			$("#txtqnt").focus();
		}
		else
		{
			//alert();
			$("#deptPrevIssue").empty().hide();
			$("#btn_add").attr("disabled",false);
			add_item_temp($("#doc_id").val(),$("#r_doc").val().trim(),$("#batch_no").val(),$("#txtqnt").val(),$("#txtmrp").val().trim(),$("#gst").val().trim(),$("#txtexpiry").val().trim());
			doc_v=1;
			doc_sc=0;
		}
	}
	function add_item_temp(id,itm_name,bch,qnt,rate,gst_per,exp_dt)
	{
		var amt=(qnt*rate).toFixed(2);
		var tr_len=$('#mytable tr').length;
		var gst=0;
		gst_per=parseFloat(gst_per);
		gst=amt-(amt*(100/(100+gst_per)));
		gst=gst.toFixed(2);
		if(tr_len==0)
		{
			var test_add="<table class='table table-condensed table-bordered' id='mytable'>";
			test_add+="<tr style='background-color:#cccccc'><th>#</th><th>Description</th><th>Batch No</th><th>Quantity</th><th>Rate</th><th>Amount</th><th style='width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr "+id+bch+"'>";
			test_add+="<td>1</td>";
			test_add+="<td>"+itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' /></td>";
			test_add+="<td>"+bch+"<input type='hidden' value='"+bch+"' class='batch' /></td>";
			test_add+="<td>"+qnt+"<input type='hidden' value='"+qnt+"' class='qnt' /></td>";
			test_add+="<td>"+rate+"<input type='hidden' value='"+rate+"' class='mrp' /></td>";
			test_add+="<td>"+amt+"<input type='hidden' value='"+amt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' /></td>";
			test_add+="<td style='text-align:center;'><input type='hidden' value='"+gst+"' class='all_gst' /><input type='hidden' value='"+exp_dt+"' class='expdt' /><span onclick='$(this).parent().parent().remove();set_amt()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span><span></span></td>";
			test_add+="</tr>";
			test_add+="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
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
			//var tbody=document.createElement("tbody");
			var tbody="";
			
			td.innerHTML=tr_len;
			td1.innerHTML=itm_name+"<input type='hidden' value='"+id+"' class='itm' /><input type='hidden' value='"+id+bch+"' class='test_id' />";
			td2.innerHTML=bch+"<input type='hidden' value='"+bch+"' class='batch' />";
			td3.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' class='qnt' />";
			td4.innerHTML=rate+"<input type='hidden' value='"+rate+"' class='mrp' />";
			td5.innerHTML=amt+"<input type='hidden' value='"+amt+"' class='all_rate' /><input type='hidden' value='"+gst_per+"' class='gst_per' />";
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
			dis_amt=0;
			tot=tot-dis_amt;
			var new_tr="<tr id='new_tr'><td colspan='5' style='text-align:right;'>Total</td><td colspan='2' id='final_rate'></td></tr>";
			$("#new_tr").remove();
			$('#mytable tr:last').after(new_tr);
			}
		}
		set_amt();
	}
	function set_amt()
	{
		$("#doc_id").val('');
		$("#r_doc").val('');
		$("#batch_no").val('0');
		$("#txtqnt").val('');
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
			tot+=parseInt(tot_ts[i].value);
		}
		tot=tot.toFixed(2);
		$("#final_rate").html(tot);
		setTimeout(function(){$("#r_doc").focus();},500);
	}
	function save_final()
	{
		$("#deptPrevIssue").empty().hide();
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
			var items=[];
			for(var i=0; i<len; i++)
			{
				var item={
					"itm"			:$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val(),
					"bch"			:$(".all_tr:eq("+i+")").find('td:eq(2) input:first').val(),
					"qnt"			:$(".all_tr:eq("+i+")").find('td:eq(3) input:first').val(),
					"mrp"			:$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val(),
					"rate"			:$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val(),
					"amt"			:$(".all_tr:eq("+i+")").find('td:eq(5) input:first').val(),
					"gst_per"		:$(".all_tr:eq("+i+")").find('td:eq(5) input:last').val(),
					"gst_amt"		:$(".all_tr:eq("+i+")").find('td:eq(6) input:first').val(),
					"exp_dt"		:$(".all_tr:eq("+i+")").find('td:eq(6) input:last').val()
					};
				items.push(item);
			}
			//alert(all);
			$.post("pages/inv_main_str_itm_issue_ajax.php",
			{
				sub_dept:$("#sub_dept").val(),
				issue_to:$("#issue_to").val().trim(),
				issue_no:$("#issue_no").val().trim(),
				items:items,
				user:$("#user").text().trim(),
				type:4,
			},
			function(data,status)
			{
				$("#loader").hide();
				alert(data);
				for(var i=0; i<len; i++)
				{
					$(".all_tr:eq("+i+")").find('td:eq(6) span:first').text('');
					$(".all_tr:eq("+i+")").find('td:eq(6) span:last').html('<i class="icon-ok icon-large text-success"></i>');
				}
			})
		}
	}
	function insert_data()
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
			alert("Please select a Sub Store name..");
			$("#selectsubstr").focus();
			jj=0;
		}
		
		var stkqnt=parseInt($("#txtavailstk").val());
		var isuqnt=parseInt($("#txtqnt").val());
		if(isuqnt>stkqnt)
		{
			alert("Issue quantity Can not be greater than Stock quantity");
			$("#txtqnt").focus();
		    jj=0;	
		}
		if(jj==1)
		{   
		///////end ////////
		
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"invmainstritmissue",
			  issuedate:$("#txtorddate").val(), 
			  substoreid:$("#selectsubstr").val(), 
			  issueno:$("#txtordo").val(),
			  issueto:$("#tstissueto").val(),
			  itmcode:$("#txtcntrname").val(),
			  btchno:$("#selectbatch").val(),
			  isueqnt:$("#txtqnt").val(),
			 
		  },
		  function(data,status)
		   {
			 	   
			   alert("Item Added");
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
			  type:"invmainstritmissue_final",
			  issuedate:$("#txtorddate").val(), 
			  substoreid:$("#selectsubstr").val(), 
			  issueno:$("#txtordo").val(),
			  issueto:$("#tstissueto").val(),
			  
			 
		  },
		  function(data,status)
		   {
			 		  
			  alert ("Data Saved");
			  $("#selectsubstr").attr("disabled",true);
		      $("#button4").attr("disabled",true);
			  clearr();
			  load_selected_item();
			   get_id();
		   })
	}}
	
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
	
	
	function load_batchno()
	{
		$('#batch_no option:not(:first)').remove();
		$.post("pages/inv_main_str_itm_issue_ajax.php",
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
	
	function lod_batchno()
	{
		$.post("pages/inv_load_display.php",
		{
			type:"mainstrbatchload",
			prdctid:$("#doc_id").val().trim(),
		},
		function(data,status)
		{
			$("#chk").val("0");	
			document.getElementById("batch_no").options.length=1;
			var data=data.split("#");
			for(var i=0;i<data.length-1;i++)
			{
				var opt = document.createElement("option");
				var ip=document.getElementById("batch_no").options.add(opt);
				var dvalue=data[i].split("@");
				for(var j=0;j<dvalue.length;j++)
				{
					opt.value=dvalue[0];
					opt.text=dvalue[1];
				}
			}
		})	
		doc_v=1;
		doc_sc=0;
	}
	function sub_dept_stock(id)
	{
		$("#deptPrevIssue").empty().hide();
		$.post("pages/inv_main_str_itm_issue_ajax.php",
		{
			dept:$("#sub_dept").val(),
			itm:id,
			type:7
		},
		function(data,status)
		{
			if(data!="")
			{
				$("#deptPrevIssue").html(data).show();
			}
		});
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
				$.post("pages/inv_main_str_itm_issue_ajax.php",
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
			doc_load(docs[1],doc_naam,docs[3]);
		}
	}
}
function doc_load(id,name,gst)
{
	//alert(id);
	$("#r_doc").val(name);
	$("#doc_id").val(id);
	$("#gst").val(gst);
	//$("#doc_info").fadeIn(200);
	//$("#ref_doc").fadeOut(200);
	load_batchno();
	$("#batch_no").focus();
	doc_tr=1;
	doc_sc=0;
	sub_dept_stock(id);
}
//------------------------item search end---------------------------------//
</script>
