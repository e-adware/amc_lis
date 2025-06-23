<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Pharmacy Payment List</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b><!-- <?php echo date("Y-m-d"); ?> -->
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="" >
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="" >
					<button class="btn btn-success" onClick="view_all()" style="margin-top: -1%;" >View</button>
				</center>
			</td>
		</tr>
		<tr>
			<td>
				<center>
					<b>Bill No : </b> &nbsp;
					<input list="browsr" type="text" class="span2" id="bill" onKeyup="view_all()" autofocus>
					<datalist id="browsr">
					<?php
						$bill_no= mysqli_query($link," SELECT `bill_no` FROM `ph_sell_master`");
						while($bil=mysqli_fetch_array($bill_no))
						{
							echo "<option value='$bil[bill_no]'>";
						}
					?>
					</datalist>
					<!--<b>PIN</b>
					<input list="browsr" type="text" class="span2" id="pin" onKeyup="view_all()" autofocus>
					<datalist id="browsr">
					<?php
						$oid= mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` ORDER BY `slno` DESC ");
						while($pat_oid=mysqli_fetch_array($oid))
						{
							echo "<option value='$pat_oid[opd_id]'>";
						}
					?>
					</datalist>
					<b>Name</b>
					<input type="text" class="span2" id="pat_name" onKeyup="view_all()">
					<b>UHID</b>
					<input list="browsrs" type="text" class="span2" id="uhid" onKeyup="view_all()" >
					<datalist id="browsrs">
					<?php
						$pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` order by `slno` DESC");
						while($pat_uid=mysqli_fetch_array($pid))
						{
							echo "<option value='$pat_uid[patient_id]'>";
						}
					?>
					</datalist>-->
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		view_all();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	});
	function view_all()
	{
		$("#loader").show();
		$.post("pages/ph_payment_receive_data.php",
		{
			type:"load_all_pat",
			from:$("#from").val(),
			to:$("#to").val(),
			bill:$("#bill").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
			setTimeout(function(){
				view_all();
			},5000);
		})
	}
	function redirect_page(bill,access)
	{
		if(access>0)
		{
			window.location="processing.php?param=229&billno="+bill;
		}else
		{
			bootbox.dialog({ message: "<h5>You don't have access to Pharmacy Payment</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
			},2000);
		}
	}
	function redirect_page_rel(uhid,rel)
	{
		if(rel==0)
		{
			window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
		}else
		{
			window.location="processing.php?param=3&uhid="+uhid+"&consult=1";
		}
	}
	function update_patient(uhid)
	{
		window.location="processing.php?param=1&uhid="+uhid;
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
</style>
