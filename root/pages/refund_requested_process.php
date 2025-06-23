<?php

$uhid=base64_decode($_GET["uhid"]);
$opd_id=base64_decode($_GET["opd_id"]);
$refund_request_id=base64_decode($_GET["rrid"]);

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

if($pat_info && $pat_reg)
{
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
	
	if($prefix_det["type"]==1)
	{
		$refund_type1_display="display:none;";
		$refund_type2_display="";
	}
	if($prefix_det["type"]==2)
	{
		$refund_type1_display="";
		$refund_type2_display="display:none;";
	}
	if($prefix_det["type"]==3)
	{
		$refund_type1_display="";
		$refund_type2_display="display:none;";
	}
	
	$ref_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `refund_request` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `refund_request_id`='$refund_request_id' "));
	if($ref_request)
	{
		$refund_request_id=$ref_request["refund_request_id"];
		$refund_type=$ref_request["refund_type"];
		$ref_reason=$ref_request["refund_reason"];
	
		echo "<input type='hidden' id='patient_id' value='$uhid'>";
		echo "<input type='hidden' id='opd_id' value='$opd_id'>";
		echo "<input type='hidden' id='refund_request_id' value='$refund_request_id'>";
		echo "<input type='hidden' id='refund_type_all' value='$refund_type'>";

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Approve Refund Request</span></div>
</div>
<!--End-header-->
<link rel="stylesheet" href="../css/loader.css" />
<div class="container-fluid">
	<span style="float:right;">
		<button class="btn btn-back" id="add" onclick="window.location='?param=<?php echo base64_encode(312); ?>'"><i class="icon-backward"></i> Back</button>
	</span>
	<div class="">
		<div class="">
			<input type="hidden" id="uhid" value="<?php echo $uhid;?>"/>
			<input type="hidden" id="opd" value="<?php echo $opd_id;?>"/>
			<table id="tab" class="table table-condensed" style="background:snow;">
				<tr>
					<th>UHID</th>
					<th><?php echo $prefix_det["prefix"]; ?></th>
					<th>Name</th>
					<th>Age</th>
					<th>Sex</th>
					<th>Encounter</th>
				</tr>
				<tr>
					<td><?php echo $uhid; ?></td>
					<td><?php echo $opd_id; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $age; ?></td>
					<td><?php echo $pat_info["sex"]; ?></td>
					<td><?php echo $prefix_det["p_type"]; ?></td>
				</tr>
			</table>
		</div>
		<div class="">
<?php
	
	if($cancel_request)
	{
		echo "<h4 style='color:red;'>$tr_title</h4>";
	}
	else
	{
		
		if($prefix_det["type"]==1) // OPD
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
			
			$bill_amount=$pat_pay_det["tot_amount"];
			$discount_amount=$pat_pay_det["dis_amt"];
			$paid_amount=$pat_pay_det["advance"];
			$refund_amount=$pat_pay_det["refund_amount"];
			$balance_amount=$pat_pay_det["balance"];
			
			$discount_per=($pat_pay_det["dis_amt"]*100)/$pat_pay_det["tot_amount"];
		}
		
		if($prefix_det["type"]==2) // Investigation
		{
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
			
			$bill_amount=$pat_pay_det["tot_amount"];
			$discount_amount=$pat_pay_det["dis_amt"];
			$paid_amount=$pat_pay_det["advance"];
			$refund_amount=$pat_pay_det["refund_amount"];
			$balance_amount=$pat_pay_det["balance"];
			
			$discount_per=round(($pat_pay_det["dis_amt"]*100)/$pat_pay_det["tot_amount"],2);
		}
		
		if($prefix_det["type"]==3) // Other
		{
			$baby_serv_tot=0;
			$baby_ot_total=0;
			$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$opd_id' ");
			while($delivery_check=mysqli_fetch_array($delivery_qry))
			{
				$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_serv_tot+=$baby_tot_serv["tots"];
				
				// OT Charge Baby
				$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
				$baby_ot_total+=$baby_ot_tot_val["g_tot"];
				
			}
			
			$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$opd_id' and `group_id`='141' "));
			$no_of_days=$no_of_days_val["ser_quantity"];
			
			$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id='141' "));
			$tot_serv_amt1=$tot_serv1["tots"];
			//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
			
			$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$opd_id' and group_id!='141' "));
			$tot_serv_amt2=$tot_serv2["tots"];
			
			// OT Charge
			$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$opd_id' "));
			$ot_total=$ot_tot_val["g_tot"];
			
			// Total
			$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
			
			$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			
			$paid_amount       =$check_paid["paid"];
			$discount_amount   =$check_paid["discount"];
			$refund_amount     =$check_paid["refund"];
			
			$pat_bal=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`bal_amount`),0) AS `bal` FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' "));
			
			$balance_amount=$pat_bal["bal"];
			
			$discount_per=round(($discount_amount*100)/$bill_amount,2);
		}
	?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th width="20%">Total Amount</th>
				<td><input type="hidden" id="bill_amount" value="<?php echo $bill_amount;?>" /><?php echo number_format($bill_amount,2);?></td>
			</tr>
			<tr>
				<th>Advance</th>
				<td><input type="hidden" id="paid_amount" value="<?php echo $paid_amount;?>" /><?php echo $paid_amount;?></td>
			</tr>
			<tr>
				<th>Discount</th>
				<td><input type="hidden" id="discount_amount" value="<?php echo $discount_amount;?>" /><?php echo $discount_amount;?> (<?php echo $discount_per; ?>%)</td>
			</tr>
			<tr style="display:none;">
				<th>Refund Amount</th>
				<td><input type="hidden" id="refund_amount" value="<?php echo $refund_amount;?>" /><?php echo $refund_amount;?></td>
			</tr>
			<tr>
				<th>Balance</th>
				<td><input type="hidden" id="balance_amount" value="<?php echo $balance_amount;?>" /><?php echo $balance_amount;?></td>
			</tr>
			<tr>
				<th>Select</th>
				<th>
					<label class="rad_lbl" style="<?php echo $refund_type1_display; ?>">
						<input type="radio" name="refund_type" class="refund_type" id="refund_type1" value="1" onchange="refund_type_change(this.value)" <?php if($refund_type==1){ echo "checked"; } ?> > Refund
					</label>
					<label class="rad_lbl" style="<?php echo $refund_type2_display; ?>">
						<input type="radio" name="refund_type" class="refund_type" id="refund_type2" value="2" onchange="refund_type_change(this.value)" <?php if($refund_type==2){ echo "checked"; } ?>> Refund
					</label>
				</th>
			</tr>
			<tr id="ref_service_list" style="display:none;">
				<th>Refund</th>
				<td id="ref_service_td"></td>
			</tr>
			<tr class="save_tr" style="display:none;">
				<th>Reason</th>
				<td>
					<input type="text" class="span5" id="ref_reason" value="<?php echo $ref_reason; ?>" placeholder="Enter Refund Reason">
				</td>
			</tr>
			<tr class="" style="display:none;">
				<th>Payment Mode</th>
				<td>
					<select class="" id="payment_mode">
					<?php
						$pay_mode_master_qry=mysqli_query($link, "SELECT `p_mode_name` FROM `payment_mode_master` WHERE `operation`='1' ORDER BY `sequence` ASC");
						while($pay_mode_master=mysqli_fetch_array($pay_mode_master_qry))
						{
							if($ref_request["payment_mode"]==$pay_mode_master["p_mode_name"]){ $sel="selected"; }else{ $sel=""; }
							echo "<option value='$pay_mode_master[p_mode_name]' $sel>$pay_mode_master[p_mode_name]</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr class="save_tr" style="display:none;">
				<td colspan="2">
					<center>
					<?php if($refund_request_id==0){ ?>
						<button class="btn btn-save" id="save_refund_btn" onclick="save_refund()"><i class="icon-save"></i> Save</button>
					<?php } ?>
					</center>
					<div id="ref_requested_data"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div id="old_refund_request"></div>
				</td>
			</tr>
		</table>
<?php
	}
?>
		</div>
	</div>
</div>
<?php
	}
	else
	{
		echo "<br><center><h3>Refund request is not found.</h3></center>";
	}
}
else
{
	echo "<center><img src='../emoji/oops.png'></center>";
}
?>
<script>
	$(document).on('keyup', ".numericc", function () {
		$(this).val(function (_, val) {
			var num=parseInt(val);
			return num;
		});
	});
	$(document).ready(function()
	{
		setTimeout(function(){
			check_refund_request();
			//old_refund_request();
		},100);
		
		setTimeout(function(){
			$(".refund_select").prop("disabled", true);
		},1000);
	});
	
	function check_refund_request()
	{
		if($("#refund_request_id").val()!=0)
		{
			var refund_type=$("#refund_type_all").val();
			refund_type_change(refund_type);
			
			$(".refund_type").prop("disabled", true);
			$(".refund_select").prop("disabled", true);
			$("#ref_reason").prop("disabled", true);
			$(".save_tr").show();
			$("#save_refund_btn").hide();
			
			load_approve_details();
		}
	}
	
	function old_refund_request()
	{
		$.post("pages/refund_request_ajax.php",
		{
			type:"old_refund_request",
			uhid:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
		},
		function(data,status)
		{
			$("#old_refund_request").html(data);
		})
	}
	function load_approve_details()
	{
		$.post("pages/refund_request_ajax.php",
		{
			type:"load_requested_details",
			uhid:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			refund_request_id:$("#refund_request_id").val(),
		},
		function(data,status)
		{
			$("#ref_requested_data").html(data);
		})
	}
	
	function refund_type_change(val)
	{
		$.post("pages/refund_request_ajax.php",
		{
			type:"service_list",
			uhid:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			refund_request_id:$("#refund_request_id").val(),
			val:val,
		},
		function(data,status)
		{
			$("#ref_service_td").html(data);
			$("#ref_service_list").show();
			
			$(".r_type").hide();
			$(".r_type"+val).show();
			
			scroll_me_top(300);
			service_reset();
		})
	}
	function service_reset()
	{
		$(".ref_amount_each").prop("readonly", true);
		show_save_tr();
	}
	function refund_check_change(dis,rel_id,fix_id)
	{
		if($(".refund_type:checked").val()==2)
		{
			if($(dis).is(":checked"))
			{
				$("#"+rel_id).prop("readonly", false);
			}
			else
			{
				$("#"+rel_id).prop("readonly", true);
				$("#"+rel_id).val('0');
			}
		}
		show_save_tr();
	}
	function show_save_tr()
	{
		if($(".refund_select:checked").length>0)
		{
			$(".save_tr").show();
		}
		else
		{
			$(".save_tr").hide();
		}
	}
	function ref_amount_each_up(dis,rel_id,slno)
	{
		var own_val=parseInt($(dis).val());
		if(!own_val){ own_val=0; }
		
		var rel_val=parseInt($("#"+rel_id).val());
		if(!rel_val){ rel_val=0; }
		
		var res_val=rel_val-own_val;
		
		if(own_val>rel_val)
		{
			$(dis).css({"border-color":"red"}).focus();
			$("#save_refund_btn").hide();
			return false;
		}
		else
		{
			$(dis).css({"border-color":""});
			$("#save_refund_btn").show();
		}
		res_val=res_val.toFixed(2);
		$("#res_val2"+slno).text(res_val);
	}
	
	function reject_refund_request()
	{
		$("#reject_refund_btn").hide();
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to reject ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					  $("#reject_refund_btn").show();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						reject_refund_request_ok();
					}
				}
			}
		});
	}
	function reject_refund_request_ok()
	{
		bootbox.dialog({
			message: "Reason:<input type='text' id='reason' autofocus />",
			title: "Payment Delete",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					  $("#reject_refund_btn").show();
					}
				},
				main: {
					label: '<i class="icon-ok"></i> Reject',
					className: "btn btn-danger",
					callback: function() {
						if($("#reason").val()!="")
						{
							bootbox.dialog({ message: "<b>Please wait...</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
							
							$.post("pages/refund_request_ajax.php",
							{
								type:"reject_refund_request",
								uhid:$("#patient_id").val(),
								opd_id:$("#opd_id").val(),
								refund_request_id:$("#refund_request_id").val(),
								reason:$("#reason").val(),
								user:$("#user").text().trim(),
							},
							function(data,status)
							{
								bootbox.hideAll();
								alert(data);
								//$("#reject_refund_btn").show();
								check_refund_request();
							})
						}
						else
						{
							bootbox.alert("Reason cannot blank");
							$("#reject_refund_btn").show();
						}
					}
				}
			}
		});
	}
	function approve_refund_request()
	{
		if($(".refund_type:checked").length==0)
		{
			alert("Select Refund");
			return false;
		}
		if($(".refund_select:checked").length==0)
		{
			alert("Select Service To Refund");
			return false;
		}
		
		var refund_type_val=$(".refund_type:checked").val();
		
		var ref_ser=$(".refund_select:checked");
		var sel_services="";
		for(var i=0;i<ref_ser.length;i++)
		{
			var ser_id=parseInt(ref_ser[i].value);
			
			var ref_amount=parseFloat($("#ref_amount_each"+ser_id).val());
			if(!ref_amount){ ref_amount=0; }
				
			var fix_amount=parseFloat($("#ref_amount_each_fix"+ser_id).val());
			if(!fix_amount){ fix_amount=0; }
				
			var actual_amount=parseFloat($("#ref_amount_each_actual"+ser_id).val());
			if(!actual_amount){ actual_amount=0; }
			
			if(refund_type_val==1) // Service Refund
			{
				ref_amount=fix_amount;
			}
			if(refund_type_val==2) // Payment Refund
			{
				if(ref_amount>fix_amount || ref_amount==0)
				{
					$("#ref_amount_each"+ser_id).css({"border-color":"red"}).focus();
					return false
				}
			}
			sel_services=sel_services+"@@"+ser_id+"$$"+ref_amount+"$$"+fix_amount+"$$"+actual_amount;
		}
		if($("#ref_reason").val()=="")
		{
			//alert("Enter Refund Reason");
			$("#ref_reason").focus()
			return false;
		}
		
		$("#approve_refund_btn").hide();
		
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to approve refund request ? Once you approved, can not undo.</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					  $("#approve_refund_btn").show();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						bootbox.dialog({ message: "<b>Please wait...</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
						
						$.post("pages/refund_request_ajax.php",
						{
							type:"approve_refund_request",
							uhid:$("#patient_id").val(),
							opd_id:$("#opd_id").val(),
							refund_request_id:$("#refund_request_id").val(),
							refund_type:$(".refund_type:checked").val(),
							sel_services:sel_services,
							refund_reason:$("#ref_reason").val(),
							payment_mode:$("#payment_mode").val(),
							user:$("#user").text().trim(),
						},
						function(data,status)
						{
							//alert(data);
							var res=data.split("@@");
							check_refund_request();
							
							setTimeout(function(){
								bootbox.hideAll();
								bootbox.dialog({ message: "<h4>"+res[0]+"</h4>"});
							},1000);
							
							setTimeout(function(){
								bootbox.hideAll();
							},2000);
						})
					}
				}
			}
		});
	}
	
	function print_refund_request(refund_request_id)
	{
		var uhid=$("#patient_id").val();
		var opd_id=$("#opd_id").val();
		//var refund_request_id=$("#refund_request_id").val();
		var user=$("#user").text().trim();
		
		url="pages/refund_request_print_new.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&rrid="+btoa(refund_request_id)+"&EpMl="+btoa(user);
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function print_refund_receipt(refund_request_id)
	{
		var uhid=$("#patient_id").val();
		var opd_id=$("#opd_id").val();
		//var refund_request_id=$("#refund_request_id").val();
		var user=$("#user").text().trim();
		
		url="pages/refund_receipt_print_new.php?uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&rrid="+btoa(refund_request_id)+"&EpMl="+btoa(user);
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
.rad_lbl
{
	display:inline-block;
	padding-left: 5px;
	padding-right: 5px;
	border: 1px solid #bbbbbb;
	border-radius: 8px;
	background: #fefefe;
}
.rad_lbl:hover
{
	box-shadow: 1px 1px 5px 3px #bcbcbc;
	transition-duration: 0.5s;
}

.sub_table1 th, .sub_table1 td
{
	border-left: 0;
}
</style>
