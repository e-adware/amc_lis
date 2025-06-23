<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contribution</title>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});


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
				$.post("pages/ap_load_data_ajax.php",
				{
					val:val,
					type:"ap_load_doctor_contribution",
				
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


function load_doctor() //load suppler
{
	
	$.post("pages/ap_load_data_ajax.php",
	{
		type:"ap_load_doctor_contribution",
	},
	function(data,status)
	{
		
		$("#load_materil").html(data);
		
	})
}


function val_load_new(rid,sbstrid)///for retrieve data against Supplier
{
  $.post("pages/ap_load_display_ajax.php",
    {
		type:"ap_load_doc_detail",
		orderid:rid,
		
	},
	function(data,status)
	 {
		
	    var val=data.split("#");
	    $("#txtcid").val(val[0]);
	    $("#txtsplr").val(val[1]);
	    
	   
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
		var docid=0;
		var date1=$("#fdate").val();
		var date2=$("#tdate").val();
		var vdocdisprcnt=$("#txtpercent").val();
		var vdisamt=$("#txtdisamt").val();
		
			url="pages/ref_commision_rpt.php?docid="+docid+"&date1="+date1+"&date2="+date2+"&vdocdisprcnt="+vdocdisprcnt+"&vdisamt="+vdisamt;
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	   
	}

function sale_rep_det_prr_docwise(f,p,opd)
	{
		if($("#txtcid").val()=="")
		{
			alert("Please select a Doctor name..");
		}
		else
		{
			var docid=$("#txtcid").val();
			var date1=$("#fdate").val();
			var date2=$("#tdate").val();
			var vdocdisprcnt=$("#txtpercent").val();
			var vdisamt=$("#txtdisamt").val();
			
			url="pages/ref_commision_rpt.php?docid="+docid+"&date1="+date1+"&date2="+date2+"&vdocdisprcnt="+vdocdisprcnt+"&vdisamt="+vdisamt;
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
			
		}
	}	
	
		
function data_saved()
{
	var blno="";
	var chk=$(".pats:checked");
	
	
	if(chk.length>0)
	 {
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
						load_unpaidbill();
					 }
					)
	  }				
}



	
</script>
</head>

<body >
	
<div id="content-header">
    <div class="header_div"> <span class="header">Contribution Generate</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<?php
			
		?>
		<form id="form1" method="post">
			
			<div class="" style="text-align:center;">
				<b>From</b>
				<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
				<b>To</b>
				<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
			</div>
			
			<div class="span11" style="margin-left:0px;">
					<table class="table table-striped table-bordered">
					<tr>
						<td>Search   <input type="text" id="txtsplr" size="40" onkeypress="sel_pr(this.value,event);"/></td>
						<td ></td>
						<td >  
						<input type="text" name="txtcid" id="txtcid" size="10" value="" readonly="readonly" class="imp intext"/>
						</td>
					</tr>
					</table>
			</div>
						
			<div class="span11" style="margin-left:0px;">
					<div id="load_materil" style="overflow-y:scroll; height:355px" >
					<script>load_doctor()</script>
					</div>
			</div>

		<table class="table table-bordered table-condensed" >
			<tr class="text-center">
				<td style="text-align:center">
					 <input type="hidden" id="txtpercent" name="txtpercent"  autocomplate="off" style="max-width:110px"/> 
					&nbsp;&nbsp;&nbsp; <input type="hidden" id="txtdisamt" name="txtdisamt" size="15" autocomplate="off"/>
				</td>
			</tr>
			
		</table>

					
		<div class="" style="margin-bottom:10px;text-align:center;">
			<button type="button" class="btn btn-info" onclick="sale_rep_det_prr_docwise()"><b class="icon-search"></b> View</button>
			<button type="button" class="btn btn-info" onclick="sale_rep_det_prr()"><b class="icon-search"></b> All Doctor</button>
		
	</div>
	
		</form>
		
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
