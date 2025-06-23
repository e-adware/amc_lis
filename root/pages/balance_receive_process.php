<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$discount_attribute="";
if($emp_access["discount_permission"]==0)
{
	$discount_attribute="readonly";
}

//if($emp_access["edit_payment"]==0)

$date=date("Y-m-d");
$time=date("H:i:s");

$uhid=mysqli_real_escape_string($link, base64_decode($_GET['uhid']));
$opd_id=$ipd=mysqli_real_escape_string($link, base64_decode($_GET['ipd']));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$reg_date=$pat_reg["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
$encounter=$pat_typ_text["p_type"];

if($pat_typ_text["type"]==1)
{
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
	
	$bill_amount=$pat_pay_det["tot_amount"];
	$discount_amount=$pat_pay_det["dis_amt"];
	$paid_amount=$pat_pay_det["advance"];
	$tax_amount=$pat_pay_det["tax_amount"];
	$refund_amount=$pat_pay_det["refund_amount"];
	$balance_amount=$pat_pay_det["balance"];
}

if($pat_typ_text["type"]==2)
{
	$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' "));
	
	$bill_amount=$pat_pay_det["tot_amount"];
	$discount_amount=$pat_pay_det["dis_amt"];
	$paid_amount=$pat_pay_det["advance"];
	$tax_amount=$pat_pay_det["tax_amount"];
	$refund_amount=$pat_pay_det["refund_amount"];
	$balance_amount=$pat_pay_det["balance"];
}

if($pat_typ_text["type"]==3)
{
	$uhid=$pat_reg["patient_id"];
	$ipd=$pat_reg["opd_id"];
	
	$baby_serv_tot=0;
	$baby_ot_total=0;
	$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
	while($delivery_check=mysqli_fetch_array($delivery_qry))
	{
		$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
		$baby_serv_tot+=$baby_tot_serv["tots"];
		
		// OT Charge Baby
		$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
		$baby_ot_total+=$baby_ot_tot_val["g_tot"];
		
	}
	
	$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
	$no_of_days=$no_of_days_val["ser_quantity"];
	
	$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
	$tot_serv_amt1=$tot_serv1["tots"];
	//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
	
	$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
	$tot_serv_amt2=$tot_serv2["tots"];
	
	// OT Charge
	$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
	$ot_total=$ot_tot_val["g_tot"];
	
	// Total
	$bill_amount=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
	
	$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));
	
	$paid_amount      =$check_paid["paid"];
	$discount_amount  =$check_paid["discount"];
	$refund_amount    =$check_paid["refund"];
	$tax_amount       =$check_paid["tax"];
	
	$settle_amount=$paid_amount+$discount_amount+$tax_amount-$refund_amount;
	
	$balance_amount=$bill_amount-$settle_amount;
}

?>

<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Receive Balance</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<th>UHID</th>
			<th><?php echo $pat_typ_text["prefix"]; ?></th>
			<th>Patient Name</th>
			<th>Phone Number</th>
			<th>Encounter</th>
			<th>Reg Date</th>
		</tr>
		<tr>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $pat_info["phone"]; ?></td>
			<td><?php echo $encounter; ?></td>
			<td><?php echo convert_date_g($pat_reg["date"]); ?></td>
		</tr>
	</table>
	<div id="load_transaction"></div>
</div>
<input type="hidden" id="patient_id" value="<?php echo $uhid; ?>">
<input type="hidden" id="opd_id" value="<?php echo $opd_id; ?>">
<input type="hidden" id="p_type_id" value="<?php echo $pat_reg["type"]; ?>">
<!-- Loader -->
<div id="loader" style="margin-top:-15%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		load_payment_info()
	});
	
	function load_payment_info()
	{
		$("#loader").show();
		$.post("pages/balance_receive_data.php",
		{
			type:"load_payment_info",
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_transaction").html(data);
			
			setTimeout(function(){
				$("#opd_now_discount_per").focus();
			},100);
		})
	}
	
	// Payment Start
	function opd_discount_per(e)
	{
		var val=parseFloat($("#opd_now_discount_per").val());
		if(!val){ val=0; }
		
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var res_amount=total-disount_amount-paid_amount-refunded_amount;
		if(!res_amount){ res_amount=0; }
		
		var opd_balance_amount=parseInt($("#opd_balance_amount").val());
		if(!opd_balance_amount){ opd_balance_amount=0; }
		
		//~ var tot=$("#total").val();
		//~ var dis_val=((res_amount*val)/100);
		var dis_val=((opd_balance_amount*val)/100);
		dis_val=Math.round(dis_val);
		
		$("#opd_now_discount_amount").val(dis_val);
		
		if(dis_val>0)
		{
			$("#opd_now_discount_reason").prop('type', 'text');
		}else
		{
			$("#opd_now_discount_reason").prop('type', 'hidden');
			$("#opd_now_discount_per").val("");
		}
		
		if(e.which==13)
		{
			$("#opd_now_discount_amount").focus();
		}
		
		opd_after_discount_calc();
	}
	function opd_discount_amount(e)
	{
		var dis_val=parseInt($("#opd_now_discount_amount").val());
		if(!dis_val){ dis_val=0; }
		
		//~ var tot=parseInt($("#total").val());
		
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var opd_balance_amount=parseInt($("#opd_balance_amount").val());
		if(!opd_balance_amount){ opd_balance_amount=0; }
		
		var res_amount=total-disount_amount-paid_amount-refunded_amount;
		if(!res_amount){ res_amount=0; }
		
		//~ tot=res_amount;
		tot=opd_balance_amount;
		
		if(tot==0)
		{
			var per=0;
		}else
		{
			var per=((dis_val*100)/tot);
		}
		
		//~ per=Math.round(per);
		per=per.toFixed(2);
		
		$("#opd_now_discount_per").val(per);
		
		if(dis_val>0)
		{
			$("#opd_now_discount_reason").prop('type', 'text')
		}else
		{
			$("#opd_now_discount_reason").prop('type', 'hidden')
		}
		
		if(e.which==13)
		{
			if(dis_val>0)
			{
				$("#opd_now_discount_reason").focus();
			}
			else if($("#opd_now_pay:disabled").length==0)
			{
				$("#opd_now_pay").focus();
			}
			else
			{
				$("#opd_now_payment_mode").focus();
			}
		}
		
		opd_after_discount_calc();
	}
	function opd_now_discount_reason(e)
	{
		$("#opd_now_discount_reason").css({"border-color":""});
		if(e.which==13)
		{
			if($("#opd_now_discount_reason").val()!="")
			{
				$("#opd_now_pay").focus();
			}
			else
			{
				$("#opd_now_discount_reason").css({"border-color":"red"});
			}
		}
	}
	
	function opd_after_discount_calc()
	{
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var opd_balance_amount=parseInt($("#opd_balance_amount").val());
		if(!opd_balance_amount){ opd_balance_amount=0; }
		
		//~ $("#opd_bill_amount_str").text(total.toFixed(2));
		//~ $("#opd_bill_amount").val(total);
		
		var res_amount=total-disount_amount-paid_amount-refunded_amount;
		if(!res_amount){ res_amount=0; }
		
		if(res_amount<0)
		{
			refund_calc();
			return false;
		}
		else
		{
			var now_discount=parseInt($("#opd_now_discount_amount").val());
			if(!now_discount){ now_discount=0; }
			
			//~ res_amount=res_amount-now_discount;
			res_amount=opd_balance_amount-now_discount;
			
			if(res_amount<0)
			{
				$("#opd_now_pay").val("0");
				$(".discount_cls").css({"border-color":"red"});
			}
			else
			{
				$("#opd_now_pay").val(res_amount);
				$(".discount_cls").css({"border-color":""});
			}
			$(".opd_now_balance_tr").hide();
			
			refund_calc();
		}
	}
	function opd_now_pay(e)
	{
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var now_discount=parseInt($("#opd_now_discount_amount").val());
		if(!now_discount){ now_discount=0; }
		
		var opd_now_pay=parseInt($("#opd_now_pay").val());
		if(!opd_now_pay){ opd_now_pay=0; }
		
		var opd_balance_amount=parseInt($("#opd_balance_amount").val());
		if(!opd_balance_amount){ opd_balance_amount=0; }
		
		//~ var res_amount=total-disount_amount-paid_amount-refunded_amount-now_discount-opd_now_pay;
		var res_amount=opd_balance_amount-now_discount-opd_now_pay;
		if(!res_amount){ res_amount=0; }
		
		if(res_amount<0)
		{
			$("#opd_now_pay").css({"border-color":"red"});
			
			$(".opd_now_balance_tr").hide();
		}
		else
		{
			$("#opd_now_pay").css({"border-color":""});
			
			if(res_amount==0)
			{
				$(".opd_now_balance_tr").hide();
			}
			else
			{
				res_amount=res_amount.toFixed(2);
			
				$("#opd_now_balance").text(res_amount);
				$(".opd_now_balance_tr").show();
			}
		}
		if(e.which==13)
		{
			if(res_amount>=0)
			{
				$("#opd_now_payment_mode").focus();
			}
		}
	}
	function opd_payment_mode_up(e)
	{
		if(e.which==13)
		{
			if($("#now_balance_reason").is(":visible"))
			{
				$("#now_balance_reason").focus();
			}
			else if($("#opd_now_cheque_ref_no").is(":visible"))
			{
				$("#opd_now_cheque_ref_no").focus();
			}
			else
			{
				$("#pat_save_btn").focus();
			}
		}
	}
	function opd_payment_mode_change(val)
	{
		$.post("pages/payment_load_data.php",
		{
			type:"payment_mode_change",
			val:val,
		},
		function(data,status)
		{
			var res=data.split("@#@");
			$("#opd_now_ref_field").val(res[0]);
			$("#opd_now_operation").val(res[1]);
			
			if($("#opd_now_ref_field").val()==0)
			{
				$("#opd_now_cheque_ref_no_tr").show();
			}
			else
			{
				$("#opd_now_cheque_ref_no_tr").hide();
			}
			
			if($("#opd_now_operation").val()==2)
			{
				$("#opd_now_balance_reason_str").show();
				$(".opd_now_balance_tr").show();
				
				$("#opd_now_discount_per").val("0").prop("disabled", true);
				$("#opd_now_discount_amount").val("0").prop("disabled", true);
				$("#opd_now_pay").val("0").prop("disabled", true);
				$("#opd_now_discount_reason").prop('type', 'hidden');
				opd_now_pay(event);
			}
			else
			{
				$("#opd_now_balance_reason_str").hide();
				$(".opd_now_balance_tr").hide();
				
				$("#opd_now_discount_per").prop("disabled", false);
				$("#opd_now_discount_amount").prop("disabled", false);
				$("#opd_now_pay").prop("disabled", false);
				opd_now_pay(event);
			}
		})
	}
	function now_balance_reason(e)
	{
		$("#now_balance_reason").css({"border-color":""});
		if(e.which==13)
		{
			if($("#now_balance_reason").val()=="")
			{
				$("#now_balance_reason").css({"border-color":"red"});
				return false;
			}
			if($("#opd_now_cheque_ref_no").is(":visible"))
			{
				$("#opd_now_cheque_ref_no").focus();
			}
			else
			{
				$("#pat_save_btn").focus();
			}
		}
	}
	function opd_now_cheque_ref_no(e)
	{
		$("#opd_now_cheque_ref_no").css({"border-color":""});
		if(e.which==13)
		{
			$("#pat_save_btn").focus();
		}
	}
	
	function refund_calc()
	{
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		//~ $("#opd_bill_amount_str").text(total.toFixed(2));
		//~ $("#opd_bill_amount").val(total);
		
		var res_amount=total-disount_amount-(paid_amount-refunded_amount);
		if(!res_amount){ res_amount=0; }
		
		if(res_amount<0)
		{
			$("#opd_now_refund_tr").show();
			
			var refund_amount_now=res_amount*(-1);
			
			$("#opd_now_refund").val(refund_amount_now);
			$("#opd_now_refund_str").text(refund_amount_now.toFixed(2));
			
			$("#opd_now_discount_per").val("0").prop("disabled", true);
			$("#opd_now_discount_amount").val("0").prop("disabled", true);
			$("#opd_now_pay").val("").prop("disabled", true);
			
			$(".opd_now_balance_tr").hide();
			$(".opd_now_cheque_ref_no_tr").hide();
			
			$("#opd_now_payment_mode").focus();
			
		}
		else
		{
			$("#opd_now_refund_tr").hide();
			
			$("#opd_now_refund").val("0");
			$("#opd_now_refund_str").text("0.00");
		}
		//scrollPage(280);
	}
	
	function save_payment()
	{
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var opd_balance_amount=parseInt($("#opd_balance_amount").val());
		if(!opd_balance_amount){ opd_balance_amount=0; }
		
		var now_discount=parseInt($("#opd_now_discount_amount").val());
		if(!now_discount){ now_discount=0; }
		
		var yet_amount=total-disount_amount-paid_amount-refunded_amount;
		if(!yet_amount){ yet_amount=0; }
		
		var refund_val=0;
		
		if(yet_amount<0)
		{
			var refund_val=1;
		}
		
		//~ var yet_amount=total-disount_amount-paid_amount-refunded_amount-now_discount;
		var yet_amount=opd_balance_amount-now_discount;
		if(!yet_amount){ yet_amount=0; }
		
		if(yet_amount<0 && refund_val==0)
		{
			$("#opd_now_discount_amount").css({"border-color":"red"}).focus();
			return false;
		}
		
		if(now_discount>0)
		{
			if($("#opd_now_discount_reason").val()=="")
			{
				$("#opd_now_discount_reason").prop('type', 'text').css({"border-color":"red"}).focus();
				return false;
			}
		}
		
		var opd_now_pay=parseInt($("#opd_now_pay").val());
		if(!opd_now_pay){ opd_now_pay=0; }
		
		//~ var res_amount=total-disount_amount-paid_amount-refunded_amount-now_discount-opd_now_pay;
		var res_amount=opd_balance_amount-now_discount-opd_now_pay;
		if(!res_amount){ res_amount=0; }
		
		if(res_amount<0 && refund_val==0)
		{
			$("#opd_now_pay").css({"border-color":"red"}).focus();
			return false;
		}
		/*
		if(res_amount>0 && refund_val==0)
		{
			if($("#now_balance_reason").val()=="")
			{
				$(".opd_now_balance_tr").show();
				$("#now_balance_reason").css({"border-color":"red"}).focus();
				return false;
			}
		}*/
		
		$("#loader").show();
		$("#save_tr").hide();
		
		$.post("pages/balance_receive_data.php",
		{
			type:"save_payment",
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			p_type_id:$("#p_type_id").val(),
			total:total,
			now_discount:now_discount,
			opd_now_discount_reason:$("#opd_now_discount_reason").val(),
			opd_now_pay:opd_now_pay,
			opd_now_payment_mode:$("#opd_now_payment_mode").val(),
			now_balance_reason:$("#now_balance_reason").val(),
			opd_now_cheque_ref_no:$("#opd_now_cheque_ref_no").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			bootbox.dialog({ message: "<h5>"+data+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
				load_payment_info();
			},2000);
		})
	}
	// Payment End
	
	function print_transaction(pid)
	{
		var url="pages/print_transaction_receipt.php?v="+btoa(1);
		
		var uhid=$("#patient_id").val();
		url=url+"&uhid="+btoa(uhid);
		
		var opd_id=$("#opd_id").val();
		url=url+"&opdid="+btoa(opd_id);
		
		url=url+"&pid="+btoa(pid);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function payment_mode_change_trans(pid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to change payment mode ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
						bootbox.hideAll();
						load_payment_info();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						payment_mode_change_trans_check(pid);
					}
				}
			}
		});
	}
	
	function payment_mode_change_trans_check(pid)
	{
		if($("#opd_payment_mode_trans"+pid).val()=="Credit")
		{
			load_payment_info();
			bootbox.alert("Failed, try again later.");
		}
		else if($("#opd_payment_mode_trans"+pid).val()=="Cash")
		{
			payment_mode_change_trans_ok(pid,"");
		}
		else
		{
			bootbox.dialog({
				message: "<input type='text' class='capital' id='cheque_ref_no_trans' autofocus />",
				title: "Cheque/Reference no",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> Cancel',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
							load_payment_info();
						}
					},
					main: {
						label: '<i class="icon-ok"></i> Change',
						className: "btn btn-danger",
						callback: function() {
							
							payment_mode_change_trans_ok(pid,$("#cheque_ref_no_trans").val());
							
						}
					}
				}
			});
		}
	}
	function payment_mode_change_trans_ok(pid,cheque_ref_no_trans)
	{
		$("#loader").show();
		$.post("pages/balance_receive_data.php",
		{
			type:"payment_mode_change",
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			p_type_id:$("#p_type_id").val(),
			pay_id:pid,
			payment_mode:$("#opd_payment_mode_trans"+pid).val(),
			cheque_ref_no:cheque_ref_no_trans,
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			bootbox.dialog({ message: "<h5>"+data+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
				load_payment_info();
			},2000);
		})
	}
	
	function delete_receipt(pid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete ?</h5>",
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
						delete_receipt_ok(pid);
					}
				}
			}
		});
	}
	function delete_receipt_ok(pid)
	{
		bootbox.dialog({
			message: "Reason:<input type='text' id='del_reason' autofocus />",
			title: "Payment Delete",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				main: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						if($("#del_reason").val()!="")
						{
							$("#loader").show();
							$.post("pages/balance_receive_data.php",
							{
								type:"delete_payment",
								patient_id:$("#patient_id").val(),
								opd_id:$("#opd_id").val(),
								p_type_id:$("#p_type_id").val(),
								pay_id:pid,
								del_reason:$("#del_reason").val(),
								user:$("#user").text().trim(),
							},
							function(data,status)
							{
								//alert(data);
								$("#loader").hide();
								bootbox.dialog({ message: "<h5>"+data+"</h5> "});
								setTimeout(function(){
									bootbox.hideAll();
									load_payment_info();
								},2000);
							})
						}
						else
						{
							bootbox.alert("Reason cannot blank");
						}
					}
				}
			}
		});
	}
	
	function print_receipt(url)
	{
		var uhid=$("#patient_id").val();
		url=url+"&uhid="+btoa(uhid);
		
		var opd_id=$("#opd_id").val();
		url=url+"&opdid="+btoa(opd_id);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function back_page()
	{
		var param_id=114;
		
		window.location.href="?param="+btoa(param_id);
	}
</script>
