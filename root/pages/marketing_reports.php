<?php
session_start();

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

 
 //~ $qdocmerge=mysqli_query($link,"select distinct merge_doc_id from merge_doctor_master order by merge_doc_id");
 //~ while($qdocmerge1=mysqli_fetch_array($qdocmerge))
 //~ {
	 //~ $qdocmerge2=mysqli_fetch_array(mysqli_query($link,"select distinct refbydoctorid from merge_doctor_master where merge_doc_id='$qdocmerge1[merge_doc_id]'"));
	 //~ mysqli_query($link,"update patient_details set refbydoctorid='$qdocmerge2[refbydoctorid]' where refbydoctorid='$qdocmerge1[merge_doc_id]'");
 //~ }
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contribution</title>
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="pages/Others/datepick/jsDatePick_ltr.css" />

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		
		$("#selectmk").select2({ theme: "classic" });
		$("#selectmk").select2("focus");
		
		$("#selectdoctor").select2({ theme: "classic" });
		$("#selectdoctor").select2("focus");
		
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



function sale_rep_det_prr(f,p,opd)
	{
		var docid=0;
		var date1=$("#fdate").val();
		var date2=$("#tdate").val();
		
		
			url="pages/ref_doc_case_apollo_rpt.php?docid="+docid+"&date1="+date1+"&date2="+date2;
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	  
	}


	
	function popitupdoctr(url)
	{
		var txtfrom=$("#fdate").val();
		var txtto=$("#tdate").val();
		var mkid=$("#selectmk").val();
		var docid=$("#selectdoctor").val();
		
		url=url+"?date1="+txtfrom+"&date2="+txtto+"&mkid="+mkid+"&docid="+docid;
		newwindow=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');

	}
	
	function popitupdoctr_chk(url)
	{
		var txtfrom=$("#fdate").val();
		var txtto=$("#tdate").val();
		var mkid=$("#selectmk").val();
		var docid=$("#selectdoctor").val();
		if(mkid==0)
		{
			alert("Please select a Name..");
		}
		else
		{
			url=url+"?date1="+txtfrom+"&date2="+txtto+"&mkid="+mkid+"&docid="+docid;
			newwindow=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
        }
	}
			
 function popitup(url)
	{
		var txtfrom=$("#fdate").val();
		var txtto=$("#tdate").val();
		var docid=0;
    	
		url=url+"?date1="+txtfrom+"&date2="+txtto+"&docid="+docid;
		newwindow=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');

	}


function load_doctor()
	{
		$("#loader").show();
		$.post("pages/marketing_data_ajax.php",
		{
			mrkid:$("#selectmk").val(),
			typ:"load_doctor",
		},
		function(data,status)
		{
			
			$("#loader").hide();
			//alert(data);
			$("#selectdoctor").find("option:not(:first)").remove();
			if(data!="")
			{
				var vl=data.split("#%#");
				for(var j=0; j<vl.length; j++)
				{
					var v=vl[j];
					var d=v.split("@@");
					$("#selectdoctor").append("<option value='"+d[0]+"'>"+d[1]+"</option>");
				}
			}
			
		});
	}

	
</script>
</head>

<body >
	
<div id="content-header">
    <div class="header_div"> <span class="header">Marketing Details </span></div>
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
			
			<div class="" style="margin-left:0px;">
					<table class="table table-striped table-bordered">
					<tr >
						<td style="text-align:center"> Select Group
							<select id="selectmk" onchange="load_doctor()" autofocus >
							<option value="0">--Select All--</option>
							<?php
								if($qsl['levelid']=="1")
								{
								$qq=mysqli_query($link,"SELECT `emp_id`, `name` FROM `employee` WHERE `levelid`='31' order by `name`");
								}
								else
								{
								$qq=mysqli_query($link, "SELECT `emp_id`, `name` FROM `employee` WHERE `levelid`='31' and emp_id='$userid' order by name ");
								}
			   
							
							while($r=mysqli_fetch_array($qq)){if($_POST['selectmk']==$r['emp_id']){$ssel="selected='selected'";} else { $ssel=" ";}  
							?>
							<option value="<?php echo $r['emp_id']; ?>"><?php echo $r['name']; ?></option>
							<?php
							}
							?>
							</select>
						</td>
						
						<td style="text-align:center"> Select Doctor
							<select id="selectdoctor" >
							<option value="0">--Select All--</option>

							</select>
						</td>
												  
					</tr>
					
					
					</table>
			</div>
						
			
		
					
<!--
		<div class="" style="margin-bottom:10px;text-align:center;">
			<button type="button" class="btn btn-info" onclick="sale_rep_det_prr()"><b class="icon-search"></b> View</button>
			<button type="button" class="btn btn-info" onclick="sale_rep_det_prr()"><b class="icon-search"></b> All Doctor</button>
	   </div>
-->
	   
		<div class="" style="margin-bottom:10px;text-align:center;">
		<input type="button" name="button" id="button" value="Ref.Details" class="btn btn-info" onclick="popitupdoctr_chk('pages/ap_marketing_doc_details.php')"  style="width:180px" />
		<input type="button" name="button" id="button" value="Patient Wise" class="btn btn-info" onclick="popitupdoctr_chk('pages/ap_marketing_patientwise_details.php')"  style="width:180px" />
		<input type="button" name="button" id="button" value="Summary" class="btn btn-info" onclick="popitupdoctr_chk('pages/ap_marketing_doc_summary.php')"  style="width:180px" />
		<input type="button" name="button" id="button" value="Datewise" class="btn btn-info" onclick="popitupdoctr_chk('pages/ap_marketing_datewise_rpt.php')"  style="width:180px" />
		<input type="button" name="button" id="button" value="Month wise" class="btn btn-info" onclick="popitupdoctr_chk('pages/ap_marketing_monthwise_rpt.php')"  style="width:180px" />
		
		<!--<input type="button" id="button7" name="button7" class="btn btn-info" value="Investigation" style="width:150px" onclick="popitup('pages/ap_investigatuin_smry_rpt.php')"/> -->
		<!--<input type="button" id="button8" name="button8" class="btn btn-default" value="Ref Detail" style="width:150px" onclick="popitupdoctr('pages/ref_doc_cs_details_rpt.php')"/> -->
		
	</div>
	
		</form>
		
		
		
</body>
</html>
