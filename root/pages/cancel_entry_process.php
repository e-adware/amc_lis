<?php
$uhid=base64_decode($_GET["uhid"]);
$ipd=$opd_id=base64_decode($_GET["opd"]);

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$branch_id=$pat_reg["branch_id"];

$reg_date=$pat_reg["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
$pat_typ=$pat_typ_text['p_type'];

//// Bill Amount
if($pat_typ_text['type']==1)
{
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	$bill_amount=$pat_pay_det["tot_amount"];
}
if($pat_typ_text['type']==2)
{
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
	$bill_amount=$pat_pay_det["tot_amount"];
}
if($pat_typ_text['type']==3)
{
	$baby_serv_tot=0;
	$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
	if($delivery_check)
	{
		$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
		$baby_serv_tot=$baby_tot_serv["tots"];
		
		// OT Charge Baby
		$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
		$baby_ot_total=$baby_ot_tot_val["g_tot"];
		
	}
	
	$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
	$no_of_days=$no_of_days_val["ser_quantity"];
	
	$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
	$tot_serv_amt1=$tot_serv1["tots"];
	//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
	
	$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
	$tot_serv_amt2=$tot_serv2["tots"];
	
	$ot_total=0;
	if($pat_reg["type"]==3) // If Caualty or day care and has entry ot, skip ot
	{
		// OT Charge
		$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
		$ot_total=$ot_tot_val["g_tot"];
	}
	// Total Amount
	$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
}

$cancel_request_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request_entry[user]' "));

$request_sent_by="";
$request_sent_reason="";
if($cancel_request_entry["type"]==1)
{
	$request_sent_by="Payment cancel request is sent by ".$emp_info["name"]." on ".date("d-M-Y", strtotime($cancel_request_entry["date"]))." ".date("h:i A", strtotime($cancel_request_entry["time"]));
	$request_sent_reason=$cancel_request_entry["remark"];
}
if($cancel_request_entry["type"]==2)
{
	$request_sent_by="Patient cancel request is sent by ".$emp_info["name"]." on ".date("d-M-Y", strtotime($cancel_request_entry["date"]))." ".date("h:i A", strtotime($cancel_request_entry["time"]));
	$request_sent_reason=$cancel_request_entry["remark"];
}

$approve_request_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `approve_cancel_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
if($approve_request_entry)
{
	$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT name FROM `employee` WHERE `emp_id`='$approve_request_entry[user]' "));
}
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Cancel Request</span></div>
</div>
<!--End-header-->
<link rel="stylesheet" href="../css/loader.css" />
<div class="container-fluid">
	<div class="">
		<input type="hidden" id="uhid" value="<?php echo $uhid;?>"/>
		<input type="hidden" id="opd" value="<?php echo $opd_id;?>"/>
		<input type="hidden" id="branch_id" value="<?php echo $branch_id;?>"/>
		<table id="tab" class="table table-condensed" style="background:snow;">
			<tr>
				<th>UHID</th>
				<th><?php echo $prefix_det["prefix"]; ?></th>
				<th>Name</th>
				<th>Age</th>
				<th>Sex</th>
				<th>Phone</th>
				<th>Encounter</th>
				<th>Bill Amount</th>
			</tr>
			<tr>
				<td><?php echo $uhid; ?></td>
				<td><?php echo $opd_id; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $age; ?></td>
				<td><?php echo $pat_info["sex"]; ?></td>
				<td><?php echo $pat_info["phone"]; ?></td>
				<td><?php echo $pat_typ; ?></td>
				<td><?php echo $bill_amount; ?></td>
			</tr>
		</table>
<?php
		$payment_det_qry=mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `payment_mode`!='Credit' ORDER BY `pay_id` ASC");
		$payment_det_num=mysqli_num_rows($payment_det_qry);
		if($payment_det_num>0)
		{
	?>
			<table class="table table-condensed">
				<tr>
					<th>#</th>
					<!--<th>UHID</th>
					<th><?php echo $prefix_name; ?></th>-->
					<th>Transaction No</th>
					<th>Amount</th>
					<th>Discount</th>
					<th>Refund</th>
					<th>Payment Type</th>
					<th>Payment Mode</th>
					<th>Date-Time</th>
					<th>User</th>
				</tr>
			<?php
				$zz=1;
				while($payment_det=mysqli_fetch_array($payment_det_qry))
				{
					$pay_mode_type=mysqli_fetch_array(mysqli_query($link, "SELECT `operation` FROM `payment_mode_master` WHERE `p_mode_name`='$payment_det[payment_mode]'"));
					
					$user_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$payment_det[user]'"));
			?>
					<tr id="opd_trans<?php echo $zz; ?>">
						<td><?php echo $zz; ?></td>
						<!--<td><?php echo $payment_det["patient_id"]; ?></td>
						<td><?php echo $payment_det["opd_id"]; ?></td>-->
						<td><?php echo $payment_det["transaction_no"]; ?></td>
						<td><?php echo $payment_det["amount"]; ?></td>
						<td><?php echo $payment_det["discount_amount"]; ?></td>
						<td><?php echo $payment_det["refund_amount"]; ?></td>
						<td><?php echo $payment_det["payment_type"]; ?></td>
						<td><?php echo $payment_det["payment_mode"]; ?></td>
						<td><?php echo date("d-M-Y", strtotime($payment_det["date"])); ?> - <?php echo date("h:i A", strtotime($payment_det["time"])); ?></td>
						<td><?php echo $user_info["name"]; ?></td>
					</tr>
			<?php
					$zz++;
				}
			?>
			</table>
	<?php
		}
?>
		<br>
		<br>
		<div class="">
		<?php if($cancel_request_entry){ ?>
			<table class="table table-bordered">
				<tr>
					<th style="color:#B90E8C;"><?php echo $request_sent_by; ?></th>
					<th style="color:#B90E8C;">Reason: <?php echo $request_sent_reason; ?></th>
					<td>
					<?php if($approve_request_entry){ ?>
						Approved by <?php echo $emp_info["name"]; ?>
					<?php }else{ ?>
						<button class="btn btn-delete" onclick="delete_patient_cancel_request('<?php echo $uhid; ?>','<?php echo $opd_id; ?>')"><i class="icon-remove"></i> Delete Cancel Request</button>
					<?php } ?>
					</td>
				</tr>
			</table>
		<?php }else{
			// Check doctor payment
			$doc_pay_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `payment_settlement_doc` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
			if($doc_pay_num==0)
			{
		?>
			<center>
				<button class="btn btn-delete" onclick="patient_cancel_request('<?php echo $uhid; ?>','<?php echo $opd_id; ?>')"><i class="icon-bullhorn"></i> Send Cancel Request</button>
			</center>
		<?php } } ?>
		</div>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		
	});
	function patient_cancel_request(uhid,opd_id)
	{
		var msg="Are you sure want to send patient cancel request ?";
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>"+msg+"</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Send',
					className: "btn btn-danger",
					callback: function() {
						patient_cancel_request_confirm(uhid,opd_id);
					}
				}
			}
		});
	}
	function patient_cancel_request_confirm(uhid,opd_id)
	{
		bootbox.dialog({
			message: "Reason:<input type='text' id='note' autofocus />",
			title: "Patient Cancel Request",
			buttons: {
				main: {
				  label: "Save",
				  className: "btn-primary",
				  callback: function() {
					if($('#note').val()!='')
					{
						bootbox.dialog({ message: "<span id='discharge_text'><b>Sending request</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> </span>",closeButton: false});
						
						$.post("pages/cancel_request_data.php",
						{
							type:"patient_cancel_request",
							uhid:uhid,
							opd_id:opd_id,
							branch_id:$("#branch_id").val(),
							reason:$("#note").val(),
							user:$("#user").text().trim(),
						},
						function(data,status)
						{
							setTimeout(function(){
								bootbox.hideAll();
								bootbox.dialog({ message: "<h5>"+data+"</h5>",closeButton: false});
							},1500);
							setTimeout(function(){
								bootbox.hideAll();
								window.location.reload(true);
							},2500);
						})
					}else
					{
						bootbox.alert("Reason cannot blank");
					}
					
				  }
				}
			}
		});
	}
	function delete_patient_cancel_request(uhid,opd_id)
	{
		var msg="Are you sure want to delete patient cancel request ?";
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>"+msg+"</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						
						bootbox.dialog({ message: "<span id='discharge_text'><b>Deleting</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> </span>",closeButton: false});
						
						$.post("pages/cancel_request_data.php",
						{
							type:"patient_cancel_request_delete",
							uhid:uhid,
							opd_id:opd_id,
							user:$('#user').text().trim(),
						},
						function(data,status)
						{
							setTimeout(function(){
								bootbox.hideAll();
								bootbox.dialog({ message: "<h5>"+data+"</h5>",closeButton: false});
							},1500);
							setTimeout(function(){
								bootbox.hideAll();
								window.location.reload(true);
							},2500);
						})
					}
				}
			}
		});
	}
	
	function del_request_change(uhid,pin,slno,val)
	{
		var already_request=$("#already_request"+slno).val();
		//alert(already_request);
		if(val==1)
		{
			var msg="Are you sure want to send cancel request ?";
			bootbox.dialog({
				//title: "Patient Re-visit ?",
				message: "<h5>"+msg+"</h5>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> Cancel',
						className: "btn btn-inverse",
						callback: function() {
						  bootbox.hideAll();
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Send',
						className: "btn btn-danger",
						callback: function() {
							//check_result(uhid,opd_id,typ);
						}
					}
				}
			});
		}
		if(val==0)
		{
			$("#cancel_p").hide();
			if(already_request==0)
			{
				$("#send_cancel_request_btn").hide();
			}
			if(already_request==1)
			{
				$("#send_cancel_request_btn").show();
			}
		}
	}
</script>
<div id="loader" style="display:none;position:fixed;top:50%;left:50%;"></div>
<style>
	.table tr:hover
	{
		background:none;
	}
	#tab tr th, #tab tr td
	{
		padding:0px 5px;
	}
	.rad_lbl
	{
		display:inline-block;
		padding-left: 5px;
		padding-right: 5px;
		border: 1px solid #bbbbbb;
		border-radius: 5px;
		background: #fefefe;
	}
	.rad_lbl:hover
	{
		box-shadow: 1px 1px 5px 3px #bcbcbc;
		transition-duration: 0.5s;
	}
	.tst_lbl
	{
		padding-left: 5px;
	}
	.tst_lbl:hover
	{
		box-shadow: 1px 1px 5px 3px #bcbcbc;
		transition-duration: 0.1s;
	}
	label{
		display:inline;
	}
	.cancel_div
	{
		border: 1px solid #ddd;
		width: 15%;
		padding: 10px;
		box-shadow: 0px 0px 10px 0px #9DB19E;
	}
</style>
