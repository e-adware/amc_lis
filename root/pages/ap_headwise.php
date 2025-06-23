<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contribution Setup</title>
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>

<link rel="stylesheet" type="text/css" media="all" href="pages/Others/datepick/jsDatePick_ltr.css" />
<script type="text/javascript" src="pages/Others/datepick/jsDatePick.min.1.3.js"></script>
<script>

$(document).ready(function()
{
	$("#doctor").select2({ theme: "classic" });
	$("#doctor").select2("focus");
		
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
					type:"ap_load_testwise",
					docid:$("#doctor").val(),
				
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




function val_load_new(rid,sbstrid)///
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
	
	
	function data_saved(id,e)
	{
		var jj=1;
	     
	     if(jj==1)
	     {
			$.post("pages/ap_insert_data.php",
			{
			type:"ap_doc_com_catewis_save",
			docid:$("#doctor").val(),
			labper:$("#txtlab").val(),
			radioper:$("#txtradio").val(),
			cardioper:$("#txtcardio").val(),
			

			},
			function(data,status)
			{
			//alert(data);
			 alert("Done");
			 $("#txtlab").val('');
			 $("#txtradio").val('');
			 $("#txtcardio").val('');

			}
			)
         }
				
	}	

	

function sale_rep_det_prr()
	{
		var docid=$("#doctor").val();
		var date1=0;
		var date2=0
		
		
			url="pages/ap_ref_doc_com_rpt.php?docid="+docid+"&date1="+date1+"&date2="+date2;
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	  
	}
	

	
</script>
</head>

<body >
	
<div id="content-header">
    <div class="header_div"> <span class="header">Ref Doctor Contribution Setup</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<?php
			
		?>
		<form id="form1" method="post">
			<div class="span11" style="margin-left:0px;">
					<table class="table table-striped table-bordered" align="center">
						
					<tr>
						<td> Select Doctor
							<select id="doctor" onclick="load_pending_order()" autofocus >
							<option value="0">--All Doctor--</option>
							<?php
							$qq=mysqli_query($link,"SELECT refbydoctorid, `ref_name` FROM `refbydoctor_master` order by `ref_name`");
							while($r=mysqli_fetch_array($qq))
							{
							?>
							<option value="<?php echo $r['refbydoctorid']; ?>"><?php echo $r['ref_name']; ?></option>
							<?php
							}
							?>
							</select>
						</td>
						<td >  
						<input type="hidden" name="txtcid" id="txtcid" size="10" value="" readonly="readonly" class="imp intext"/>
						</td>
					</tr>
					
					<tr>
						<td colspan="2"> Pathology <input type="text" id="txtlab" style="width:100px"/> % &nbsp; &nbsp; Radiology <input type="text" id="txtradio" style="width:100px"/> % &nbsp; &nbsp; Cardiology <input type="text" id="txtcardio" style="width:100px"/> %</td>
					</tr>	
					
					<tr>
						<td colspan="2"> <input type="button" id="save" value="Save" class="btn btn-info" onclick="data_saved()"/>
						 <button type="button" class="btn btn-info" onclick="sale_rep_det_prr()"><b class="icon-search"></b> View</button>
						</td>
					</tr>	

<!--
					<tr>
						<td>Search   <input type="text" id="txtsplr" size="40" onkeypress="sel_pr(this.value,event);"/></td>
						
						
						<input type="text" name="txtcid" id="txtcid" size="10" value="" readonly="readonly" class="imp intext"/><input type="text" name="txtsbstrid" id="txtsbstrid" size="15" value="" readonly="readonly" class="imp intext"/> Date&nbsp; <input type="text" name="txtdate" id="txtdate" value="<?php echo date('Y-m-d');?>" onfocus="jsdate('txtdate')" />
						</td>
					</tr>
-->

					</table>
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
