<!--header-->
<?php
// Check patients from last month
$date2 = date('Y-m-d');
$date1 = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date2)) . " -3 months"));

?>
<div id="content-header">
    <div class="header_div"> <span class="header">Purchase Bill Cancel</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	
	
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" style="width:50%;margin:0 auto;">
			<tr>
				<td>Select Receipt No</td>
				<td>
						<input list="browsrs" type="text" name="txtrcptno" id="txtrcptno" class="" style="width:100px;"  />
						<datalist id="browsrs">
						<?php

							$pid = mysqli_query($link," SELECT distinct `receipt_no` FROM `inv_main_stock_received_master` where recpt_date between '$date1' and '$date2' order by slno  ");
							while($pat_uid=mysqli_fetch_array($pid))
							{
								echo "<option value='$pat_uid[receipt_no]'>";
							}
						?>
						</datalist>
				</td>
			</tr>
			
           
           <tr>
			   <td>Bill No </td>
			   <td><input type="text" id="txtbillno" readonly /></td>
			</tr>
			
			
			<tr>
			   <td>Supplier </td>
			   <td><input type="text" id="txtsupplier" class="span4" readonly/></td>
			   <td><input type="hidden" id="txtsupplierid" readonly/></td>
			</tr>
            
            <tr>
			   <td>Entry Date </td>
			   <td><input type="text" id="txtentrydate" readonly/></td>
			</tr>


			
			<tr>
					<td>Reason </td>
					<td><input type="text" id="txtreason" /></td>
					
				</tr>
			
			
				
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="button" class="btn btn-info" onclick="insert_data_final()">Save</button>
				</td>
			</tr>
		</table>
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<script>
	
$(document).ready(function()
{
	//$("#txtdate").datepicker({dateFormat: 'yy-mm-dd'});
	
	$("#txtrcptno").keyup(function(e)
	{
		if(e.keyCode==13)
		{
			
			srch();
		}
	});

});	
	function srch()
	{
		
			$.post("pages/inv_load_display.php"	,
			{
				
				rcptno:$("#txtrcptno").val(),
				type:"inv_load_bill_cancel",
			},
			function(data,status)
			{
				
				
				if(data==2)
				{
					alert("You cannot cancell this Bill due to current stock is less than received quantity..");
					$("#txtbillno").val('');
					$("#txtsupplierid").val('');
					$("#txtsupplier").val('');
					$("#txtentrydate").val('');
					$("#txtreason").val('');
				}
				else
				{
					var val=data.split("@");
					$("#txtbillno").val(val[0]);
					$("#txtsupplier").val(val[1]);
					$("#txtentrydate").val(val[2]);
					$("#txtsupplierid").val(val[3]);
			  }
			})

		
	}
	
	

function insert_data_final()
	{
		///////For Check Blank fields//
		var jj=1;
					 
		
	    if($("#txtrcptno").val()=="")
	    {
			alert("Please enter Receipt No..");
			jj=0;
			$("#txtrcptno").focus();
		}
	    
	    if($("#txtbillno").val()=="")
	    {
			alert("Bill No not found..");
			jj=0;
			$("#txtbillno").focus();
		}
	    if($("#txtsupplier").val()=="")
	    {
			alert("Supplier Name Not Found..");
			jj=0;
			$("#txtbillno").focus();
		}
		
		if($("#txtreason").val()=="")
	    {
			alert("Please enter the reason");
			jj=0;
			$("#txtreason").focus();
		}
		if(jj==1)
		{
		
		 $.post("pages/inv_insert_data.php",
		  {
			  type:"inv_bill_cancel",
			  rcptno:$("#txtrcptno").val(),
			  blno:$("#txtbillno").val(),
			  spllrid:$("#txtsupplierid").val(),
			  billentrydate:$("#txtentrydate").val(),
			  reason:$("#txtreason").val(),
			  
		  },
		  function(data,status)
		   {
			   
			    alert("Done");
				$("#txtrcptno").val('');
				$("#txtbillno").val('');
				$("#txtsupplierid").val('');
				$("#txtsupplier").val('');
				$("#txtentrydate").val('');
				$("#txtreason").val('');
			   
		   })
		}
	}	
	
</script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
