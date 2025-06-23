<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Sales Bill Print</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-bottom:10px;text-align:center;">
		<b>From</b>
		<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<b>To</b>
		<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
	</div>
	
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" style="width:50%;margin:0 auto;">
			<tr>
				<td>Select Bill No</td>
				<td>
						<input list="browsrs" type="text" name="txtbillno" id="txtbillno" class="" style="width:100px;"  />
						<datalist id="browsrs">
						<?php

							$pid = mysqli_query($link," SELECT `bill_no` FROM `ph_sell_master` ");
							while($pat_uid=mysqli_fetch_array($pid))
							{
								echo "<option value='$pat_uid[bill_no]'>";
							}
						?>
						</datalist>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="button" class="btn btn-info" onclick="srch()">Search</button>
					<button type="button" class="btn btn-info" onclick="srch_payment()">View Payment</button>
				</td>
			</tr>
		</table>
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	
	function srch()
	{
		$("#loader").show();
		$.post("pages/ph_load_data_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			billno:$("#txtbillno").val(),
			type:"loadsalesbill",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	
	function srch_payment()
	{
		   var jj=1;
		   if($("#txtbillno").val()=="")
		   {
			   alert("Please select a bill no..");
			   $("#txtbillno").focus();
			   jj=0;
		   }
		   if(jj==1)
		   {
			   $("#loader").show();
			$.post("pages/ph_load_data_ajax.php"	,
			{
				
				billno:$("#txtbillno").val(),
				type:"ph_load_payment",
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#res").html(data);
			})
		}
		
	}
	
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	function rcv_rep_prr(billno,sub_id)
	{
		url="pages/sale_bill_print.php?billno="+btoa(billno)+"&sub_id="+btoa(sub_id);
		//url="pages/sale_bill_print_zebra.php?billno="+billno;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
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
