
<html>
	<head>
		<script type="text/javascript">
	$(document).ready(function()
	{
		get_id();
		load_item();
	
	});
	
	function get_focus()
	{
		$("#txtname").focus();
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
						$("#load_materil").scrollTop(doc_sc);
					}
				}
			}
			else
			{
				$.post("pages/inv_load_data_ajax.php",
				{
					val:val,
					type:"subtore",
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
	
	
	function jsdate(id)
	 {
		new JsDatePick
		({
			useMode:2,
			target:id,
			dateFormat:"%Y/%m/%d"
		});
	 }
			 
	function clearr()//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";	
		}
		$("#txtname").focus();
		get_id();
	}
		
	function load_item()
	{
		$("#loader").show();
		$.post("pages/inv_load_data_ajax.php",
		{
			type:"subtore",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_materil").html(data);
		})
	}
	
	
	
	function val_load_new(id)///for retrieve data against center
	{
		
		$.post("pages/inv_load_display.php",
		{
			type:"subtore",
			id:id,
		},
		function(data,status)
		{
			
			var val=data.split("@");
			$("#txtcid").val(val[0]);
			$("#txtname").val(val[1]);	
			
		})
	}
	
	function delete_data(itmid)
	{
		
		$.post("pages/inv_load_delete.php",
		{
			type:"subtore",
			itmid:itmid,
			
		},
		function(data,status)
		{
			alert("Deleted");
			load_item();
			clearr();
		})
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
		var vname=$("#txtname").val();
		if(vname==0)
		{
			alert("Please enter the sub Store name..");
			$("#txtname").focus();
			jj=0;
		}
		
		if(jj==1)
		{   
		///////end ////////
		$("#loader").show();
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"subtore",
			  
			  vid:$("#txtcid").val(), 
			  name:$("#txtname").val(), 
			 
		  },
		  function(data,status)
		   {
			  
			  
			   alert("Data Saved");
			   load_item();
			   clearr();
		   })
	}}
	
	
	
	function get_id() //For Get Id
	{
		$.post("pages/load_id.php",
		{
			type:"subtore",
		},
		function(data,status)
		{
			
			$("#txtcid").val(data);
		})
	}
	
	function popitup1(url)
	{
		var supplr=document.getElementById("selectsupplr").value;
		var orderno=document.getElementById("prord").value;
		
		url=url+"?supplr="+supplr+"&orderno="+orderno;
		newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
		$("#button5").attr("disabled",true);
	}
	
	
function tab_next(e) 
{ 	
var unicode=e.keyCode? e.keyCode : e.charCode; 	
if(unicode==13) 
	{ 		
	var act=document.activeElement.id; 		
	if(!act) 		
	{ 			
	document.getElementById("info1").focus();	 		
	} 	
		else 
	{    
	   var clsn=$("#"+act).attr("class");    
	   var nam=$("#"+act).attr("name"); 
	   var val=nam.replace( /^\D+/g, ''); 
	   val=parseInt(val)+1; 
	   document.getElementsByName(clsn+val)[0].focus(); 
	}
	}
}

</script>
</head>

<body onload="get_focus()" onkeypress="tab_next(event)">
	
<div id="content-header">
    <div class="header_div"> <span class="header">Indent Sub Store</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		
		<form id="form1" method="post">
			<div class="span6" style="margin-left:0px;">
				<table class="table table-bordered table-condensed">
					
										
					<tr>
						<td>ID</td>
						<td>
							<input type="text" name="txtcid" id="txtcid" size="15" value="" readonly="readonly" class="imp intext" />
						</td>
					</tr>
					
					<tr>
						<td>Name</td>
						<td>
							<input type="text" name="intext1"  id="txtname" value="" size="40" autocomplete="off" class="intext" placeholder="Name"/>
						</td>
					</tr>
					
					
					<tr>
						<td colspan="4" style="text-align:center">
							<input type="button" name="button" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" /> 
							<input type="button" name="intext2" id="button" value= "Save" onclick="insert_data()" class="btn btn-success" />
							<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/indent_order_rpt.php')" class="btn btn-success" disabled />-->
						</td>
					</tr>
					
				</table>
			</div>
			<div class="span5">
				<table class="table table-bordered table-condensed">
					<tr>
						<td>Item Search</td>
						<td> <input type="text" name="txtcustnm" size="30" id="txtcustnm"  autocomplete="off" class="intext" onkeyup="sel_pr(this.value,event)" placeholder="Search..." /></td>
					</tr>
				</table>
				<div id="load_materil" class="vscrollbar" style="max-height:260px;overflow-y:scroll;" >
					
				</div>
			</div>
	
		</form>
		
</div>
</body>
</html>
