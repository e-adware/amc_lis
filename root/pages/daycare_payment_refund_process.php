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
		<div class="span11">
			<input type="hidden" id="uhid" value="<?php echo $uhid;?>"/>
			<input type="hidden" id="opd" value="<?php echo $opd_id;?>"/>
			<table id="tab" class="table table-condensed" style="background:snow;">
				<tr>
					<th>UHID</th>
					<th>PIN</th>
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
			$pat_bill=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot_amount` FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id'"));
			$bill_amount=$pat_bill["tot_amount"];
			
			$pat_pay_det=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot_day_amount`, ifnull(SUM(`discount`),0) AS `tot_day_discount` FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id'"));
			$pat_tot_paid=$pat_pay_det["tot_day_amount"];
			$pat_tot_discount=$pat_pay_det["tot_day_discount"];
			
			$pat_bal=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`bal_amount`),0) AS `tot_bal` FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$opd_id'"));
			$bal_amount=$pat_bal["tot_bal"];
		?>
			<input type="hidden" id="mod_type" value="<?php echo $mod_type;?>" />
			<table class="table table-condensed table-bordered">
				<tr>
					<th width="20%">Total Amount</th>
					<td><input type="hidden" id="tot_amt" value="<?php echo $bill_amount;?>" /><?php echo $bill_amount;?></td>
				</tr>
				<tr>
					<th>Paid</th>
					<td>
						<?php echo $pat_tot_paid;?>
						<input type="hidden" id="paid_amount" value="<?php echo $pat_tot_paid;?>" />
					</td>
				</tr>
				<tr>
					<th>Discount</th>
					<td><input type="hidden" id="dis_amt" value="<?php echo $pat_tot_discount;?>" /><?php echo $pat_tot_discount;?></td>
				</tr>
				<tr>
					<th>Balance</th>
					<td><?php echo $bal_amount;?></td>
				</tr>
				<tr>
					<th>Select <b id="warn" style="display:none;float:right;color:#ED0A0A;" class="icon-warning-sign icon-large"></b></th>
					<td>
						<label class="rad_lbl" style="display:none;"><input type="radio" name="refn" class="refn" id="rad_fre" onchange="test_list()"  value="0" /> Free</label> &nbsp;&nbsp;&nbsp;
						
						<label class="rad_lbl" style="display:none;"><input type="radio" name="refn" class="refn" id="rad_ref" onchange="test_list()"  value="1" /> Refund Service</label>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						
						<label class="rad_lbl"><input type="radio" name="refn" class="refn" id="rad_ref_ser" onchange="test_list()"  value="2" /> Refund Amount</label>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						
						<span id="modify_msg" style="color:#ED0A0A;display:none;"><i class="icon-warning-sign icon-large"></i> <i>Back date patient cannot modify</i></span>
					</td>
				</tr>
				<tr style="display:none;" id="test_row">
					<th>Services</th>
					<td id="pat_all_tests"></td>
				</tr>
				<tr style="display:none;" id="refund_amount_row">
					<th>Refund Amound</th>
					<td>
						<input type="text" name="refund_amount" id="refund_amount">
					</td>
				</tr>
				<tr style="display:none;">
					<th>Refund</th>
					<td>
						<input type="text" id="refund" placeholder="Refund Amount" onkeyup="tab(this.id,event);clearr(this.id,event)" autofocus />
						<span id="ref_err" style="display:none;color:#FF0000;position:relative;padding:4px;border:1px solid #FA9E9E;border-radius:4px;box-shadow:0px 1px 5px 1px #FA9E9E;">Cannot refund more than total amount</span>
					</td>
				</tr>
				<tr>
					<th>Reason</th>
					<td>
						<input type="text" id="reason" class="span5" onkeyup="tab(this.id,event);clearr(this.id,event)" placeholder="Reason for refund" />
						<span id="res_err" style="display:none;color:#FF0000;position:relative;padding:4px;border:1px solid #FA9E9E;border-radius:4px;box-shadow:0px 1px 5px 1px #FA9E9E;">Enter reason</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;">
						<button type="button" id="sav" style="display:none;" class="btn btn-primary" onclick="refund()">Save</button>
						<button type="button" id="save" class="btn btn-primary" onclick="refund_new()">Save</button>
						<button type="button" id="ref_btn" class="btn btn-info"  onclick="print_ref()">Print</button>
						<a href="processing.php?param=133" class="btn btn-success">Back To List</a>
					</td>
				</tr>
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
		load_refund();
		mod_msg();
		
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
			$("#refund_amount_row").hide();
		}
		else if(rad==1)
		{
			$("#refund_amount_row").hide();
			$("#loader").show();
			$.post("pages/payment_refund_ajax.php",
			{
				uhid:$("#uhid").val().trim(),
				opd:$("#opd").val().trim(),
				user:$("#user").text().trim(),
				type:7,
			},
			function(data,status)
			{
				$("#loader").hide();
				$("#pat_all_tests").html(data);
				$("#test_row").slideDown(1000);
			})
		}
		else if(rad==2)
		{
			$("#test_row").hide();
			$("#refund_amount_row").show();
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
		var len=$("input['type=radio']:checked").length;
		var rad=$("input['type=radio']:checked").val();
		var tst=$(".tst_id:checked").length;
		//alert(len);
		$("#res_err").hide();
		if(len==0)
		{
			$("#warn").fadeIn(1000);
		}
		else if(rad==0 && $("#reason").val().trim()=="")
		{
			$("#res_err").show();
			$("#reason").focus();
		}
		else if(rad==1 && tst==0)
		{
			$(".strs").show();
			//$("#reason").focus();
		}
		else if(rad==1 && $("#reason").val().trim()=="")
		{
			$("#res_err").show();
			$("#reason").focus();
		}
		else if(rad==2 && $("#refund_amount").val().trim()=="")
		{
			$("#refund_amount").css({"border-color":"red"}).focus();
		}
		else if(rad==2 && $("#reason").val().trim()=="")
		{
			$("#res_err").show();
			$("#reason").focus();
		}
		else
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
			
			$("#save").attr("disabled",true);
			var rad=$("input['type=radio']:checked").val();
			var ck=$(".tst_id");
			var rt=$(".tst_rate");
			var all="";
			for(var i=0;i<ck.length;i++)
			{
				if(ck[i].checked)
				{
					all+=ck[i].value+"@"+rt[i].value+"##";
				}
			}
			//~ alert(rad);
			//~ return false;
			$("#loader").show();
			$.post("pages/payment_refund_ajax.php",
			{
				uhid:$("#uhid").val().trim(),
				opd:$("#opd").val().trim(),
				all:all,
				rad:rad,
				refund_amount:refund_amount,
				res:$("#reason").val().trim(),
				user:$("#user").text().trim(),
				type:8,
			},
			function(data,status)
			{
				window.location.reload(true);
				//~ $("#loader").hide();
				//~ $("#reason").val('');
				//~ //alert(data);
				//~ test_list();
				//~ load_refund();
				//~ bootbox.dialog({ message: data});
				//~ setTimeout(function()
				//~ {
					//~ bootbox.hideAll();
					//~ $("#save").attr("disabled",false);
				//~ },1000);
			})
		}
	}
	function print_ref()
	{
		var uhid=$("#uhid").val().trim();
		var opd=$("#opd").val().trim();
		var user=$("#user").text().trim();
		url="pages/payment_refund_serv.php?uhid="+uhid+"&opdid="+opd+"&user="+user;
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
			type:9,
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
					$("#rad_ref_ser").attr("checked",true);
					$("#rad_ref").attr("disabled",true);
					
				}
				if(rad_value=="2")
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
