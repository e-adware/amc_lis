<?php
// Check patients from last month
$date2 = date('Y-m-d');
$date1 = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date2)) . " -1 months"));
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> LAB Payment List</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<span id="pat_typ_span">
						<b>Payment Type</b>
						<select id="pat_type" onChange="view_all()" class="span2">
							<option value="1">Pending</option>
							<option value="2">Paid</option>
							<option value="3">All</option>
						</select>
					</span>
				
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					<button class="btn btn-success" onClick="view_all()" style="margin-top: -1%;" >View</button>
				</center>
			</td>
		</tr>
		<tr>
			<td>
				<center>
					<b>PIN</b>
					<input list="browsr" type="text" class="span2" id="pin" onKeyup="view_all()" autofocus>
					<datalist id="browsr">
					<?php
						$oid= mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2' ORDER BY `slno` DESC ");
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
						$pid = mysqli_query($link," SELECT `patient_id` FROM `patient_info` WHERE `patient_id`>0 AND `date` BETWEEN '$date1' AND '$date2' order by `slno` DESC");
						while($pat_uid=mysqli_fetch_array($pid))
						{
							echo "<option value='$pat_uid[patient_id]'>";
						}
					?>
					</datalist>
					<!--<b>Hos Reg No</b>
					<input list="browsrhos" type="text" class="span2" id="hos_reg_no" onKeyup="view_all()" >
					<datalist id="browsrhos">
					<?php
						$pat_hos_reg_qry = mysqli_query($link," SELECT `hos_reg_no` FROM `patient_cabin` order by `slno` DESC");
						while($pat_hos_reg=mysqli_fetch_array($pat_hos_reg_qry))
						{
							echo "<option value='$pat_hos_reg[hos_reg_no]'>";
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
		$.post("pages/lab_payment_receive_data.php",
		{
			type:"load_all_lab_pat",
			from:$("#from").val(),
			to:$("#to").val(),
			pat_name:$("#pat_name").val(),
			pat_uhid:$("#uhid").val(),
			pin:$("#pin").val(),
			pat_type:$("#pat_type").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function redirect_page(uhid,pin,access)
	{
		if(access>0)
		{
			window.location="processing.php?param=107&uhid="+uhid+"&opd="+pin;
		}else
		{
			bootbox.dialog({ message: "<h5>You don't have access to LAB Payment</h5>"});
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
	function print_rep()
	{
		var fdate=$("#from").val();
		var tdate=$("#to").val();
		var pat_name=$("#pat_name").val().trim();
		var pat_uhid=$("#uhid").val().trim();
		var pin=$("#pin").val().trim();
		var hos_reg_no=$("#hos_reg_no").val().trim();
		
		url="pages/lab_payment_print.php?fdate="+fdate+"&tdate="+tdate+"&pat_name="+pat_name+"&pat_uhid="+pat_uhid+"&pin="+pin+"&hos_reg_no="+hos_reg_no;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
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
