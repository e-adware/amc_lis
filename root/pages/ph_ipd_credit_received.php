<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Credit Receipt</title>
<link rel="stylesheet" type="text/css" media="all" href="pages/Others/datepick/jsDatePick_ltr.css" />
<script type="text/javascript" src="pages/Others/datepick/jsDatePick.min.1.3.js"></script>
<script>


var doc_v=1;
var doc_sc=0;
 function sel_pr(val,e) ///for load patient
 
 {
	    
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var prod=document.getElementById("prod"+doc_v).innerHTML;
			val_load_new(prod);
		}
		else if(unicode==40)
		{
			
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
			
			if(val)
			{			
				$.post("pages/ph_load_data_ajax.php",
				{
					val:val,
					type:"ph_ipd_credit",
				
				},
				function(data,status)
				{
				
					$("#load_materil").html(data)
				
				})
			}
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

	
} 


function load_pending_order() //load suppler
{
	
	$.post("pages/ph_load_data_ajax.php",
	{
		type:"ph_ipd_credit",
	},
	function(data,status)
	{
		$("#load_materil").html(data);
		
	})
}


function val_load_new(rid,sbstrid)///for retrieve data against Supplier
{
  $.post("pages/ph_load_display_ajax.php",
    {
		type:"ph_ipd_credit",
		orderid:rid,
		sbstrid:sbstrid,
	},
	function(data,status)
	 {
		
	    var val=data.split("#");
	    $("#txtcid").val(val[0]);
	    $("#txtsbstrid").val(val[1]);
	    
	   load_unpaidbill();
	 }
  )
}
function chkstockanble(val,clstk,i)
{
	if(val<=clstk)
	 {
		 $("#"+i+"").prop("disabled",false);
		 $("#"+i+"").prop("class","pats");
	 }
	 else
	 {
		 $("#"+i+"").prop("disabled",true);
		 $("#"+i+"").prop("checked",false);
		 $("#"+i+"").prop("class","n_pats");
	 }
}

function select_all(val)
	{
				
		if(val=="Select All")
		{
			var tst=$(".pats");
			for(var i=1;i<=tst.length;i++)
			{
				$("#"+i+"").prop("checked",true);
				
			}
			$("#sel_all").val("De-select All");
		}
		else
		{
			var tst=$(".pats");
			for(var i=1;i<=tst.length;i++)
			{
				$("#"+i+"").prop("checked",false);
				
			}
			$("#sel_all").val("Select All");
		}
		
	}

function calc_discount1(val,e) ///for calculation
{
	var unicode=e.keyCode? e.keyCode : e.charCode; 	
	if(unicode=="13")
	{
		$("#btn5").focus();
	}
	else
	{
		
		var a=$("#txtbalamt").val();
		var vadjust=$("#txtdiscount").val();	
		var res=a-vadjust;
		res = res.toFixed(2);
		document.getElementById("txtttlbal").value=res;
	}
}

	
function load_unpaidbill()
{
	$.post("pages/inv_load_unaprv_indnt.php",
	  {
		  type:"ph_ipd_credit",
		  orderid:$("#txtcid").val(),
	  },
	  function(data,status)
	  {
	  	//$("#results").css({'height':'500px' });	  
		 $("#results").html(data);
		 $("#myModal").animate({'top':'5%','left':'30%',"width":"85%","min-height":"400px",'margin':'auto'},"slow");
		 $("#mod").click();
		 $("#modid").val("1");
	  });
}


function add_netamt(billno,amt,slno)
{
		$.post("pages/load_display.php",
		{
			type:"addamount",
			
			billno:billno,
			amt:amt,
			slno:slno,
			prvamt:$("#txtttlnwpaid").val(),
			},
			function(data,status)
			{
				
				$("#txtttlnwpaid").val(data);
			}
			)
 			
}


function delete_data(rid)  ///for delete
{
	$.post("pages/load_delete.php",
	{
		type:"payment",
		rid:rid,
	},
	 function(data,status)
	  {
		  alert("Data Deleted");
		 
		  load_supplier();
	  }
	)
}

function sale_rep_det_prr(f,p,opd)
	{
		var ipdno=$("#txtcid").val();
		var p="";
		var opd="";
		url="pages/ph_ipd_crdt_rpt.php?ipdno="+ipdno+"&panme="+p+"&opdno="+opd;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
function data_saved()
{
	var blno="";
	var chk=$(".pats:checked");
	
	if(chk.length>0)
	{
		$("#btn_done").hide();
		for(var i=0;i<chk.length;i++)
		{
			//blno=blno+"@#"+$(chk[i]).val()+"%%"+$("#txtnwpaid_"+$(chk[i]).attr("id")+"").val();
			blno=blno+"@#"+$(chk[i]).val()+"%%"+$("#txtnwpaid_"+$(chk[i]).attr("id")+"").val()+"%%"+$("#txtbatchno_"+$(chk[i]).attr("id")+"").val();
		}
			
		$.post("pages/ph_insert_data.php",
		{
			type:"ph_ipd_credi_receipt",
			blno:blno,
			cid:$("#txtcid").val(),
			pamt:$("#txtttlnwpaid").val(),
			date1:$("#txtdate").val(),
			sbstrid:$("#txtsbstrid").val(),
			discount:$("#txtdiscount").val(),
		},
		function(data,status)
		{
			//alert(data);
			alert("Done");
			//load_unpaidbill();
			load_pending_order();

			$.post("pages/inv_load_unaprv_indnt.php",
			{
				type:"ph_ipd_credit",
				orderid:$("#txtcid").val(),
			},
			function(data,status)
			{
				$("#results").html(data);
			});
		});
	  }	
}



	
</script>
</head>

<body >
	
<div id="content-header">
    <div class="header_div"> <span class="header">Credit Receipt</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
			<div class="" style="margin-left:0px;">
					<table class="table table-striped table-bordered">
					<tr>
						<td>Search   <input type="text" id="txtsplr" size="40" onkeypress="sel_pr(this.value,event);"/></td>
						<td ></td>
						<td >  
						<input type="text" name="txtcid" id="txtcid" size="10" value="" readonly="readonly" class="imp intext"/><input type="text" name="txtsbstrid" id="txtsbstrid" size="15" value="" readonly="readonly" class="imp intext"/> Date&nbsp; <input type="text" name="txtdate" id="txtdate" value="<?php echo date('Y-m-d');?>" onfocus="jsdate('txtdate')" />
						</td>
					</tr>
					</table>
			</div>
						
			<div class="" style="margin-left:0px;">
					<div id="load_materil" style="overflow-y:scroll; height:555px" >
					<script>load_pending_order()</script>
					</div>
			</div>
		
		<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
		<input type="hidden" value="0" id="modid"/>
		<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
		<div class="modal-dialog">
		<div class="modal-content">

		<div class="modal-body" style="max-height:600px">
		<div id="results"> </div>
		</div>
		</div>
		</div>
		</div>
		
</body>
</html>
