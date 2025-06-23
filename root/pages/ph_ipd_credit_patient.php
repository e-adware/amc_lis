<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">IPD Pharmacy Bill</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	
	
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" style="width:50%;margin:0 auto;">
			<tr>
				<td>Select IPD No</td>
				<td>
						<input list="browsrs" type="text" name="txtbillno" id="txtbillno" class="" style="width:100px;"  />
						<datalist id="browsrs">
						<?php

							$pid = mysqli_query($link," SELECT distinct `ipd_id` FROM `ph_sell_master` order by ipd_id  ");
							while($pat_uid=mysqli_fetch_array($pid))
							{
								echo "<option value='$pat_uid[ipd_id]'>";
							}
						?>
						</datalist>
				</td>
			</tr>
			
<!--
			<tr>
				<td>Select OPD No</td>
				<td>
						<input list="browsrs2" type="text" name="txtopdno" id="txtopdno" class="" style="width:100px;"  />
						<datalist id="browsrs2">
						<?php

							$pid = mysqli_query($link," SELECT distinct `opd_id` FROM `ph_sell_master` order by opd_id  ");
							while($pat_uid=mysqli_fetch_array($pid))
							{
								echo "<option value='$pat_uid[opd_id]'>";
							}
						?>
						</datalist>
				</td>
			</tr>
-->
			
			<tr>
				<td>Select Patient</td>
				<td>
						<input list="browsrs1" type="text" name="txtpatient" id="txtpatient" class="span4"   />
						<datalist id="browsrs1">
						<?php

							$pid = mysqli_query($link," SELECT distinct `customer_name` FROM `ph_sell_master` order by customer_name  ");
							while($pat_uid=mysqli_fetch_array($pid))
							{
								echo "<option value='$pat_uid[customer_name]'>";
							}
						?>
						</datalist>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="button" class="btn btn-info" onclick="srch()">Search</button>
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
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	
	function srch()
	{
		var jj=1;
		var kk=1;
		var ll=1;
		if($("#txtbillno").val()!="")
		{
			ll=0;
		}
		
		if($("#txtpatient").val()!="")
		{
			kk=0;
		}
		
		if($("#txtopdno").val()!="")
		{
			jj=1;
		}
		
		/*if(ll==kk)
		{
			alert("Please select either IPD No or Patient Name..");
			jj=0;
			
		}*/
		
		if(jj==1)
		{
			$.post("pages/ph_load_data_ajax.php"	,
			{
				
				ipdno:$("#txtbillno").val(),
				opdno:$("#txtopdno").val(),
				pname:$("#txtpatient").val(),
				type:"loadipdcrdt",
			},
			function(data,status)
			{
				$("#res").html(data);
			})
		}	
		
	}
	
	
	function sale_rep_det_prr(f,p,opd)
	{
		url="pages/ph_ipd_crdt_rpt.php?ipdno="+f+"&panme="+p+"&opdno="+opd;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
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
