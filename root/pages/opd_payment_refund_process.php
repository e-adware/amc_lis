<?php
$uhid=base64_decode($_GET["uhid"]);
$opd_id=base64_decode($_GET["opd"]);

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">OPD Payment Refund</span></div>
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
					<th><?php echo $prefix_det["prefix"]; ?></th>
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
			$det=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
			
			if($det["dis_amt"]>0)
			{
				$sav_func="";
				$disb="disabled='disabled'";
				$clas="btn-danger";
				$style="";
				$reg_fee_style="display:none;";
				$doc_fee_style="display:none;";
			}
			else
			{
				$ref=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `consult_payment_refund_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
				$free=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_payment_free` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
				
				$chk_ref==0;
				$chk_free==0;
				if($ref)
				{
					$rs=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
					$sav_func="";
					$disb="disabled='disabled'";
					$clas="btn-danger";
					$style="";
					if($ref['visit_fee']>0)
					{
						$ref_amount=$ref['visit_fee'];
						$doc_fee_style="";
						$reg_fee_style="display:none;";
					}
					if($ref['regd_fee']>0)
					{
						$ref_amount=$ref['regd_fee'];
						$doc_fee_style="display:none;";
						$reg_fee_style="";
					}
					$chk_ref=1;
					$ref_reason=$rs['reason'];
				}
				else if($free)
				{
					$sav_func="";
					$disb="disabled='disabled'";
					$clas="btn-danger";
					$style="display:none;";
					$doc_fee_style="display:none;";
					$reg_fee_style="display:none;";
					$ref_amount=$free['free_amount'];
					$chk_free=1;
					$ref_reason=$free['reason'];
				}
				else
				{
					$sav_func="refund_new()";
					$disb="";
					$clas="btn-primary";
					$style="display:none;";
					$doc_fee_style="display:none;";
					$reg_fee_style="display:none;";
					$ref_amount=0;
					$ref_reason="";
				}
			}
	?>
			<input type="hidden" id="mod_type" value="<?php echo $mod_type;?>" />
			<table class="table table-condensed table-bordered">
				<tr>
					<th width="20%">Total Amount</th>
					<td><input type="hidden" id="tot_amt" value="<?php echo $det['tot_amount'];?>" /><?php echo "&#8377; ".$det['tot_amount'];?></td>
				</tr>
				<tr>
					<th>Registration Fee</th>
					<td><input type="hidden" id="r_fee" value="<?php echo $det['regd_fee'];?>" /><?php echo "&#8377; ".$det['regd_fee'];?></td>
				</tr>
				<tr>
					<th>Doctor Fee</th>
					<td><input type="hidden" id="d_fee" value="<?php echo $det['visit_fee'];?>" /><?php echo "&#8377; ".$det['visit_fee'];?></td>
				</tr>
				<tr>
					<th>Advance</th>
					<td><input type="hidden" id="adv" value="<?php echo $det['advance'];?>" /><?php echo "&#8377; ".$det['advance'];?></td>
				</tr>
				<tr>
					<th>Discount</th>
					<td><input type="hidden" id="dis_amt" value="<?php echo $det['dis_amt'];?>" /><?php echo "&#8377; ".$det['dis_amt'];?></td>
				</tr>
				<tr>
					<th>Balance</th>
					<td><input type="hidden" id="bal" value="<?php echo $det['balance'];?>" /><?php echo "&#8377; ".$det['balance'];?></td>
				</tr>
				<tr>
					<th>Refund</th>
					<td><?php echo "&#8377; ".$ref_amount;?></td>
				</tr>
				<tr>
					<th>Select <b id="warn" style="display:none;float:right;color:#ED0A0A;" class="icon-warning-sign icon-large"></b></th>
					<td>
						<label class="rad_lbl" style="display:none;"><input type="radio" name="refn" class="refn" id="rad_fre" <?php if($chk_free>0){echo "checked";}?> onchange="ref_type()" <?php echo $disb;?> value="1" /> Free</label> &nbsp;&nbsp;&nbsp;
						<label class="rad_lbl"><input type="radio" name="refn" class="refn" id="rad_ref" <?php if($chk_ref>0){echo "checked";}?> onchange="ref_type()" <?php echo $disb;?> value="2" /> Refund</label>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<span id="modify_msg" style="color:#ED0A0A;display:none;"><i class="icon-warning-sign icon-large"></i> <i>Back date patient cannot modify</i></span>
					</td>
				</tr>
				<tr style="display:none;" id="test_row">
					<th>Tests</th>
					<td id="pat_all_tests"></td>
				</tr>
				<tr style="<?php echo $style;?>" id="ref_amt">
					<th>Refund From <b id="warn2" style="display:none;float:right;color:#ED0A0A;" class="icon-warning-sign icon-large"></b></th>
					<td>
						<input type="text" id="refund" placeholder="Refund Amount" value="<?php if($det['ref_amt']>0){echo $det['ref_amt'];}?>" <?php echo $disb;?> onkeyup="ref_process(this.value,event)" style="display:none;" autofocus />
						
						<label class="rad_lbl"><input type="radio" name="refund_from" id="reff_reg" value="1" <?php if($ref['regd_fee']>0){echo "checked='checked'";}?> <?php echo $disb;?> onchange="ref_amt_fee(this)" /> Registration Fee</label>
						<label class="rad_lbl"><input type="radio" name="refund_from" id="reff_doc" value="2" <?php if($ref['visit_fee']>0){echo "checked='checked'";}?> <?php echo $disb;?> onchange="ref_amt_fee(this)" /> Doctor Fee</label>
						
						<span id="ref_err" style="display:none;color:#FF0000;position:relative;padding:4px;border:1px solid #FA9E9E;border-radius:4px;box-shadow:0px 1px 5px 1px #FA9E9E;">Cannot refund more than total amount</span>
					</td>
				</tr>
				<tr style="<?php echo $style;?>" id="ref_amt_fee">
					<th>Refund Amount</th>
					<td>
						<input type="text" id="ref_opt1" class="span5 ref_opt" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');refund_amt_new(this.id,event)" style="<?php echo $reg_fee_style;?>" <?php echo $disb;?> value="<?php echo $ref['regd_fee'];?>" placeholder="Refund From Registration Fee" />
						<input type="text" id="ref_opt2" class="span5 ref_opt" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');refund_amt_new(this.id,event)" style="<?php echo $doc_fee_style;?>" <?php echo $disb;?> value="<?php echo $ref['visit_fee'];?>" placeholder="Refund From Doctor Fee" />
					</td>
				</tr>
				<tr>
					<th>Reason</th>
					<td>
						<input type="text" id="reason" class="span5" onkeyup="tab(this.id,event)" <?php echo $disb;?> value="<?php echo $ref_reason;?>" placeholder="Reason for refund" list="refund_reason_datalist" />
						
						<datalist id="refund_reason_datalist">
						<?php
							$reason_datalist_qry=mysqli_query($link," SELECT DISTINCT `reason` FROM `invest_payment_refund` WHERE `reason`!='.' ORDER BY `reason` ");
							while($reason_datalist=mysqli_fetch_array($reason_datalist_qry))
							{
								echo "<option value='$reason_datalist[reason]'></option>";
							}
						?>
						</datalist>
						
						<span id="res_err" style="display:none;color:#FF0000;position:relative;padding:4px;border:1px solid #FA9E9E;border-radius:4px;box-shadow:0px 1px 5px 1px #FA9E9E;">Enter reason</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;">
						<button type="button" id="sav" style="display:none;" class="btn btn-primary" onclick="refund()">Save</button>
						<button type="button" id="save" class="btn <?php echo $clas;?>" onclick="<?php echo $sav_func;?>" <?php echo $disb;?>>Save</button>
						<button type="button" id="ref_btn" class="btn btn-info"  onclick="print_ref()">Print</button>
						<button type="button" onclick="back_list()" class="btn btn-success">Back To List</button>
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
		border-radius: 8px;
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
	// onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')
	$(document).ready(function()
	{
		//load_refund();
		//mod_msg();
	});
	function ref_amt_fee(a)
	{
		var tmp="";
		if(a.value.length>0)
		{
			$("#warn2").hide();
			$("#ref_amt_fee").show();
			$(".ref_opt").hide();
			$("#ref_opt"+a.value).show();
			$(".ref_opt").focus();
			
			if(a.value==1)
			{
				tmp=$("#r_fee").val().trim();
				tmp=tmp.split(".");
				$("#ref_opt"+a.value).val(tmp[0].trim());
			}
			if(a.value==2)
			{
				tmp=$("#d_fee").val().trim();
				tmp=tmp.split(".");
				$("#ref_opt"+a.value).val(tmp[0].trim());
			}
			$("#ref_opt"+a.value).css({'border-color':'','box-shadow':''});
		}
		else
		{
			$("#ref_amt_fee").hide();
		}
	}
	function refund_amt_new(id,e)
	{
		if(id=="ref_opt1")
		{
			if(parseFloat($("#"+id).val().trim())>parseFloat($("#r_fee").val().trim()))
			{
				$("#"+id).css({'border-color':'#FE0002','box-shadow':'0 0 10px rgba(254, 0, 2, 0.5)'});
			}
			else
			{
				$("#"+id).css({'border-color':'','box-shadow':''});
			}
		}
		if(id=="ref_opt2")
		{
			if(parseFloat($("#"+id).val().trim())>parseFloat($("#d_fee").val().trim()))
			{
				$("#"+id).css({'border-color':'#FE0002','box-shadow':'0 0 10px rgba(254, 0, 2, 0.5)'});
			}
			else
			{
				$("#"+id).css({'border-color':'','box-shadow':''});
			}
		}
	}
	function back_list()
	{
		window.location='processing.php?param=245';
	}
	function ref_process(val,e)
	{
		var adv=parseInt($("#adv").val().trim());
		var fee=parseInt($("#doc_fee").val().trim());
		//if(/\D/g.test(val))
		val=val.replace(/\D/g,'');
		$("#refund").val(val);
		var unicode=e.keyCode? e.keyCode : e.charCode;
		
		if(val>fee || val>adv)
		{
			$("#refund").css({'border-color':'#FE0002','box-shadow':'0 0 10px rgba(254, 0, 2, 0.5)'});
		}
		else
		{
			$("#refund").css({'border-color':'','box-shadow':''});
			if(unicode==13 && val!="")
			{
				$("#reason").focus();
			}
		}
	}
	function ref_type()
	{
		var len=$("input['type=radio']:checked").length;
		var rad=$("input['type=radio']:checked").val();
		//alert(rad);
		if(len>0)
		{
			$("#warn").hide();
		}
		if(rad==1)
		{
			$("#ref_amt").hide();
			$("#ref_amt_fee").hide();
		}
		if(rad==2)
		{
			$("#ref_amt").show();
			//$("#refund").focus();
		}
		$("input[name='refund_from']").prop('checked', false);
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
		var len=$("input[name='refn']:checked").length;
		var rad=$("input[name='refn']:checked").val();
		var ref_opt=$("input[name='refund_from']:checked").length;
		var ref_val=$("input[name='refund_from']:checked").val();
		
		var regd_fee=0;
		var doc_fee=0;
		
		$("#res_err").hide();
		if(len==0)
		{
			$("#warn").fadeIn(500);
		}
		else if(rad==1 && $("#reason").val().trim()=="")
		{
			$("#res_err").show();
			$("#reason").focus();
		}
		else if(rad==2 && ref_opt==0)
		{
			$("#warn2").show();
		}
		else if(rad==2 && ref_val=="1" && $("#ref_opt1").val().trim()=="")
		{
			$("#ref_opt1").focus();
		}
		else if(rad==2 && ref_val=="1" && parseFloat($("#ref_opt1").val().trim())==0)
		{
			$("#ref_opt1").focus();
		}
		else if(rad==2 && ref_val=="1" && (parseFloat($("#ref_opt1").val().trim()))>(parseFloat($("#r_fee").val().trim())))
		{
			$("#ref_opt1").focus();
		}
		
		else if(rad==2 && ref_val=="2" && $("#ref_opt2").val().trim()=="")
		{
			$("#ref_opt2").focus();
		}
		else if(rad==2 && ref_val=="2" && parseFloat($("#ref_opt2").val().trim())==0)
		{
			$("#ref_opt2").focus();
		}
		else if(rad==2 && ref_val=="2" && (parseFloat($("#ref_opt2").val().trim()))>(parseFloat($("#d_fee").val().trim())))
		{
			$("#ref_opt2").focus();
		}
		else if($("#reason").val().trim()=="")
		{
			$("#res_err").show();
			$("#reason").focus();
		}
		else
		{
			if(ref_val=="1")
			{
				regd_fee=parseFloat($("#ref_opt1").val().trim());
				doc_fee=0;
			}
			if(ref_val=="2")
			{
				regd_fee=0;
				doc_fee=parseFloat($("#ref_opt2").val().trim());
			}
			$("#save").attr("disabled",true);
			//alert("OK");
			$("#loader").show();
			$.post("pages/opd_payment_refund_ajax.php",
			{
				uhid:$("#uhid").val().trim(),
				opd:$("#opd").val().trim(),
				tot_amt:$("#tot_amt").val().trim(),
				dis_amt:$("#dis_amt").val().trim(),
				adv:$("#adv").val().trim(),
				doc_fee:$("#bal").val().trim(),
				rad:rad,
				ref_doc_fee:doc_fee,
				ref_regd_fee:regd_fee,
				//refund:$("#refund").val().trim(),
				res:$("#reason").val().trim(),
				user:$("#user").text().trim(),
				type:6,
			},
			function(data,status)
			{
				$("#loader").hide();
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					location.reload(true);
				},1000);
			})
		}
	}
	function print_ref()
	{
		var uhid=$("#uhid").val().trim();
		var opd=$("#opd").val().trim();
		var user=$("#user").text().trim();
		url="pages/opd_payment_refund_rpt.php?uhid="+uhid+"&opdid="+opd+"&user="+user;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
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
			if(id=="reason" && $("#"+id).val().trim()!="")
			{
				$("#save").focus();
			}
		}
		else if(id=="reason" && $("#"+id).val().trim()!="")
		{
			$("#res_err").hide();
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
