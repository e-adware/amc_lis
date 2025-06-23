<?php
$uhid=base64_decode($_GET["uhid"]);
$opd_id=base64_decode($_GET["opd"]);
$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Payment Refund</span></div>
</div>
<!--End-header-->
<link rel="stylesheet" href="../css/loader.css" />
<div class="container-fluid">
	<div class="row">
		<div class="span12">
			<input type="hidden" id="uhid" value="<?php echo $uhid;?>"/>
			<input type="hidden" id="opd" value="<?php echo $opd_id;?>"/>
			<table id="tab" class="table table-condensed" style="background:snow;">
				<tr>
					<th>UHID</th>
					<th>OPD ID</th>
					<th>Name</th>
					<th>Age</th>
					<th>Sex</th>
				</tr>
				<tr>
					<td><?php echo $uhid; ?></td>
					<td><?php echo $opd_id; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $age; ?></td>
					<td><?php echo $pat_info["sex"]; ?></td>
				</tr>
			</table>
		</div>
		<div class="span2"></div>
		<div class="span8">
		<?php	
			$pat_refund_check=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
			if($pat_refund_check)
			{
				$all_dis="disabled";
			}
			
			$bill_amount=0;
			
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot_day_amount`, ifnull(SUM(`discount`),0) AS `tot_day_discount` FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id'"));	
			$pat_tot_paid=$pat_pay_det["tot_day_amount"];
			$pat_tot_discount=$pat_pay_det["tot_day_discount"];
			
		?>
			<table class="table table-condensed table-bordered">
				<tr>
					<th width="20%">Service Nanme</th>
					<td>
				<?php
					$z=1;
					$pat_serv_det_qry=mysqli_query($link,"SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id' ");	
					while($pat_serv_det=mysqli_fetch_array($pat_serv_det_qry))
					{
						echo $z.". ".$pat_serv_det["service_text"]."<br>";
						
						$bill_amount+=$pat_serv_det["amount"];
						$z++;
					}
				?>
					</td>
				</tr>
				<tr>
					<th width="20%">Total Amount</th>
					<td>
						<input type="hidden" id="tot_amt" value="<?php echo $bill_amount;?>" />
						<?php echo "&#8377; ".number_format($bill_amount,2);?>
					</td>
				</tr>
				<tr>
					<th>Paid</th>
					<td>
						<input type="hidden" id="paid_amount" value="<?php echo $pat_tot_paid;?>" />
						<?php echo "&#8377; ".number_format($pat_tot_paid,2);?>
					</td>
				</tr>
				<tr>
					<th>Discount</th>
					<td><input type="hidden" id="dis_amt" value="<?php echo $pat_tot_discount;?>" /><?php echo "&#8377; ".$pat_tot_discount;?></td>
				</tr>
				<tr>
					<th>Refund Amount</th>
					<td>
						<input type="text" name="refund_amount" id="refund_amount">
					</td>
				</tr>
				<tr>
					<th>Reason</th>
					<td>
						<input type="text" id="reason" class="span5" onkeyup="tab(this.id,event);clearr(this.id,event)" placeholder="Reason for refund" <?php echo $all_dis; ?> />
						<span id="res_err" style="display:none;color:#FF0000;position:relative;padding:4px;border:1px solid #FA9E9E;border-radius:4px;box-shadow:0px 1px 5px 1px #FA9E9E;">Enter reason</span>
					</td>
				</tr>
			<?php if($all_dis!="disabled"){ ?>
				<tr>
					<td colspan="2" style="text-align:center;">
						<button type="button" id="save" class="btn btn-primary" onclick="refund_new()">Save</button>
						<button type="button" id="ref_btn" class="btn btn-info"  onclick="print_ref()">Print</button>
						<a href="processing.php?param=133" class="btn btn-success">Back To List</a>
					</td>
				</tr>
			<?php }else
			{
			?>
				<tr>
					<td colspan="2" style="text-align:center;">
						<button type="button" id="ref_btn" class="btn btn-info"  onclick="print_ref()">Print</button>
						<a href="processing.php?param=133" class="btn btn-success">Back To List</a>
					</td>
				</tr>
			<?php	
			}?>
			</table>
			<div id="res">
			
			</div>
		</div>
	</div>
</div>
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
</style>
<script>
	$(document).ready(function()
	{
		$("#refund_amount").keyup(function(e)
		{
			if (/\D/g.test(this.value))
			{
				// Filter non-digits from input value.
				this.value = this.value.replace(/\D/g, '');
			}
			
			var paid_amount=parseInt($("#paid_amount").val());
			if(!paid_amount)
			{
				paid_amount=0;
			}
			var refund_amount=parseInt($("#refund_amount").val());
			if(!refund_amount)
			{
				refund_amount=0;
			}
			if(refund_amount>paid_amount)
			{
				$("#refund_amount").css({"border-color":"red"}).focus();
			}
			else
			{
				$("#refund_amount").css({"border-color":"#000"}).focus();
			}
		});
	});
	
	function service_change(val)
	{
		$.post("pages/daycare_payment_refund_ajax.php",
		{
			charge_id:val,
			type:2,
		},
		function(data,status)
		{
			$("#service_amount").val(data);
			
			var tot_amt=parseInt($("#tot_amt").val());
			var dis_amt=parseInt($("#dis_amt").val());
			
			$("#refund_amount").text(tot_amt-dis_amt-data);
			
		})
	}
	
	function mod_msg()
	{
		var v=$("#mod_type").val();
		//alert(v);
		if(v==1)
		{
			$("#modify_msg").show();
		}
		if(v==0)
		{
			$("#modify_msg").hide();
		}
	}
	function test_list()
	{
		$("#warn").fadeOut(500);
		var rad=$("input['type=radio']:checked").val();
		//alert(rad);
		if(rad==0)
		{
			$("#test_row").hide();
		}
		else if(rad==1)
		{
			$("#loader").show();
			$.post("pages/payment_refund_ajax.php",
			{
				uhid:$("#uhid").val().trim(),
				opd:$("#opd").val().trim(),
				user:$("#user").text().trim(),
				type:5,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#pat_all_tests").html(data);
				$("#test_row").slideDown(1000);
			})
		}
	}
	function test_all()
	{
		var c=$("#chk_all:checked").length;
		$(".strs").hide();
		if(c==0)
		{
			$(".tst_id").attr("checked",false);
		}
		else if(c==1)
		{
			$(".tst_id").attr("checked",true);
		}
	}
	function chk_if_all()
	{
		$(".strs").hide();
		var ch_on=$(".tst_id:checked").length;
		var ch_off=$(".tst_id").length;
		if(ch_on==ch_off)
		{
			$("#chk_all").attr("checked",true);
		}
		else
		{
			$("#chk_all").attr("checked",false);
		}
	}
	function refund_new()
	{
		var paid_amount=parseInt($("#paid_amount").val());
		if(!paid_amount)
		{
			paid_amount=0;
		}
		var refund_amount=parseInt($("#refund_amount").val());
		if(!refund_amount)
		{
			refund_amount=0;
		}
		if(refund_amount>paid_amount)
		{
			$("#refund_amount").css({"border-color":"red"}).focus();
			return false;
		}
		
		if($("#reason").val().trim()=="")
		{
			$("#res_err").show();
			$("#reason").focus();
		}
		else
		{
			bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to change service ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-danger",
					callback: function() {
						$("#save").attr("disabled",true);
						$("#loader").show();
						$.post("pages/daycare_payment_refund_ajax.php",
						{
							uhid:$("#uhid").val().trim(),
							opd_id:$("#opd").val().trim(),
							dis_amt:$("#dis_amt").val().trim(),
							charge_id:$("#charge_id").val().trim(),
							service_amount:$("#service_amount").val().trim(),
							reason:$("#reason").val().trim(),
							user:$("#user").text().trim(),
							type:3,
						},
						function(data,status)
						{
							window.location.reload(true);
						})
					}
				}
			}
		});
		}
	}
	function print_ref()
	{
		var uhid=$("#uhid").val().trim();
		var opd=$("#opd").val().trim();
		var user=$("#user").text().trim();
		url="pages/daycare_payment_refund_rpt.php?uhid="+uhid+"&opdid="+opd+"&user="+user;
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	//========================================================
	function tab(id,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(id=="refund" && $("#"+id).val()!="")
			{
				$("#reason").focus();
			}
			if(id=="reason" && $("#"+id).val()!="")
			{
				$("#sav").focus();
			}
		}
	}
	function clearr(id,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(id=="refund" && unicode!=13)
		{
			$("#ref_err").fadeOut(500);
		}
		if(id=="reason" && unicode!=13)
		{
			$("#res_err").fadeOut(500);
		}
	}
	function refund()
	{
		var ref=parseInt($("#refund").val().trim());
		var tot_amt=parseInt($("#tot_amt").val().trim());
		if($("#refund").val().trim()=="" || (parseInt($("#refund").val().trim()))==0)
		{
			$("#refund").focus();
		}
		else if(ref>tot_amt)
		{
			$("#ref_err").show(500);
			$("#refund").focus();
		}
		else if($("#reason").val().trim()=="")
		{
			$("#res_err").show(500);
			$("#reason").focus();
		}
		else
		{
			$("#loader").show();
			$.post("pages/payment_refund_ajax.php",
			{
				uhid:$("#uhid").val().trim(),
				opd:$("#opd").val().trim(),
				tot_amt:$("#tot_amt").val().trim(),
				ref:$("#refund").val().trim(),
				res:$("#reason").val().trim(),
				user:$("#user").text().trim(),
				type:2,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
				},1000);
				load_refund();
				clrr();
			})
		}
	}
	function load_refund()
	{
		$.post("pages/payment_refund_ajax.php",
		{
			uhid:$("#uhid").val().trim(),
			opd:$("#opd").val().trim(),
			type:3,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
			var rad_value=$("#rad_value").val().trim();
			if(rad_value!="")
			{
				if(rad_value=="0")
				{
					$("#rad_fre").attr("checked",true);
					$("#rad_ref").attr("disabled",true);
					
				}
				if(rad_value=="1")
				{
					$("#rad_ref").attr("checked",true);
					$("#rad_fre").attr("disabled",true);
					
					test_list();
				}
			}
		})
	}
	function clrr()
	{
		$("#refund").val('');
		$("#reason").val('');
		$("#refund").focus();
	}
</script>
