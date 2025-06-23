<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

$date=date("Y-m-d");

$patient_id=$uhid=base64_decode($_GET["uhid"]);
$opd_id=base64_decode($_GET["opd"]);

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
$btn_dis="";
if($pat_pay_detail["advance"]>0 || $pat_pay_detail["dis_amt"]>0)
{
	$discount=$pat_pay_detail["dis_per"];
	$discount_amount=$pat_pay_detail["dis_amt"];
	$advance=$pat_pay_detail["advance"];
	$balance=$pat_pay_detail["balance"];
	$dis_reason_str=$pat_pay_detail["dis_reason"];
	
	$btn_name="Update";
	
	$p_mode=mysqli_fetch_array(mysqli_query($link, " SELECT `payment_mode` FROM `invest_payment_detail` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' and `typeofpayment`='A' "));
	$payment_mode_sel=$p_mode["payment_mode"];
	
	$update_btn="";
	$pay_refund=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_refund` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
	if($pay_refund)
	{
		$update_btn="disabled";
	}else
	{
		$pay_free=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_free` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
		if($pay_free)
		{
			$update_btn="disabled";
		}
	}
	
}else
{
	$uhid_opdid=mysqli_fetch_array(mysqli_query($link, " SELECT `center_no` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
	$center=mysqli_fetch_array(mysqli_query($link, " SELECT `c_discount` FROM `centremaster` WHERE `centreno`='$uhid_opdid[center_no]' "));
	
	$discount=$center["c_discount"];
	$discount_amount=round(($pat_pay_detail["tot_amount"]*$discount)/100);
	$advance=$pat_pay_detail["tot_amount"]-$discount_amount;
	$balance=0;
	
	$dis_reason_str="";
	if($discount_amount>0)
	{
		$dis_reason_str="By Center";
	}
	
	$btn_name="Save";
	
	$payment_mode_sel="Cash";
	
	$update_btn="";
	
	
	if($pat_pay_detail["date"]!=$date)
	{
		//$btn_dis="disabled";
	}
	
}

if($update_btn=="disabled")
{
	$edit_dis="disabled";
}else
{
	$check_edit_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_lab`,`cancel_pat` FROM `employee` WHERE `emp_id`='$c_user' "));
	if($btn_name=="Save")
	{
		$edit_dis="";	
	}else
	{
		if($check_edit_access["edit_lab"]=='1')
		{
			$edit_dis="";
		}else
		{
			$edit_dis="disabled";
		}
	}
}
if($pat_pay_detail["balance"]==0)
{
	$mr_dis="";
}else
{
	$mr_dis="disabled";
}
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Receive LAB Payment</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<th>PIN</th>
			<td><?php echo $opd_id; ?></td>
			<th>Patient Name</th>
			<td><?php echo $pat_info["name"]; ?></td>
		</tr>
		<tr>
			<th style="display:none;">Regd Fee</th>
			<td style="display:none;">
				<input type="text" id="regd_fee" value="0" readonly>
			</td>
			<th>Total</th>
			<td>
				<input type="text" id="total" value="<?php echo $pat_pay_detail["tot_amount"]; ?>" readonly>
			</td>
			<th>Discount</th>
			<td>
				<input type="text" class="span1" id="dis_per" placeholder="%" onKeyUp="dis_per(this.value,event)" value="<?php echo $discount; ?>" autofocus >
				<input type="text" class="span1" id="dis_amnt" value="<?php echo $discount_amount; ?>" onKeyUp="dis_amnt(this.value,event)"><br>
				<span id="d_reason" style="display:none;"><input type="text" class="span2" id="dis_reason" placeholder="Reason for discount" onKeyUp="dis_reason(this.value,event)" value="<?php echo $dis_reason_str; ?>"></span>
			</td>
		</tr>
		<tr>
			<th>Advance</th>
			<td>
				<input type="text" id="advance" onKeyUp="advance(this.value,event)" value="<?php echo $advance; ?>" ><br>
				<span id="b_reason" style="display:none;"><input type="text" id="bal_reason" placeholder="Reason for balance" onKeyUp="bal_reason(this.value,event)"></span>
			</td>
			<th>Balance</th>
			<td>
				<input type="text" id="balance" value="<?php echo $balance; ?>" readonly>
			</td>
		</tr>
		<tr>
			<th>Payment Mode</th>
			<td colspan="3">
				<select id="pay_mode" onkeyup="pay_mode(this.value,event)" onChange="pay_mode_change(this.value)">
					<option value="Cash" <?php if($payment_mode_sel=="Cash"){ echo "selected"; } ?> >Cash</option>
					<option value="Card" <?php if($payment_mode_sel=="Card"){ echo "selected"; } ?>>Card</option>
					<option value="Cheque" <?php if($payment_mode_sel=="Cheque"){ echo "selected"; } ?>>Cheque</option>
				</select>
				<input type="hidden" value="<?php echo $credit_limit; ?>" id="credit_limit">
			</td>
		</tr>
	</table>
	<center>
		<input type="button" class="btn btn-info" id="save" value="<?php echo $btn_name; ?>" onClick="save_pat(this.value)" <?php echo $edit_dis.$btn_dis; ?> >
		<input type="hidden" id="uhid" value="<?php echo $patient_id; ?>">
		<input type="hidden" id="opd_id" value="<?php echo $opd_id; ?>">
		
	<?php
		if($pat_pay_detail["advance"]>0 || $pat_pay_detail["dis_amt"]>0)
		{
		?>
			<button id="print_receipt" class="btn btn-info" onClick="print_receipt('pages/print_opd_test.php?uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')">Print</button>
			<button id="money_receipt" class="btn btn-info" onClick="money_receipt('pages/monyrecpt_rpt.php?uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')" <?php echo $mr_dis; ?>>Money Receipt</button>
			<input type="button" id="print" class="btn btn-info" value="Print Ind." onClick="load_test_print('<?php echo $uhid; ?>','<?php echo $opd_id; ?>')"/>
			<a href="processing.php?param=106" class="btn btn-success">Back To List</a>
		<?php
			if($check_edit_access["cancel_pat"]=='1' && $btn_name=="Update"){?>
				<!--<button id="cancel_pat" class="btn btn-danger" onClick="cancel_pat('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','test')" >Cancel</button>-->
			<?php }
		}
	?>
		
	</center>
</div>

<input type="button" data-toggle="modal" data-target="#myModal1" id="mod" style="display:none"/>
<input type="hidden" id="mod_chk" value="0"/>
<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>
<input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none"/>
<input type="hidden" id="mod_chk2" value="0"/>
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results2"> </div>
			</div>
		</div>
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
		var btn_name=$("#save").val();
		if(btn_name=="Update")
		{
			$("#print_receipt").focus();
		}
	});
	
	function dis_per(val,e)
	{
		var error=0;
		if(e.which==13)
		{
			$("#dis_amnt").focus();
		}
		var tot=$("#total").val();
		var dis_val=((tot*val)/100);
		dis_val=Math.round(dis_val);
		
		$("#dis_amnt").val(dis_val);
		
		if($("#pay_mode").val()=="Credit")
		{
			$("#balance").val(tot-dis_val);
			//$("#balance").val(tot);
			$("#advance").val("0");
		}else
		{
			$("#advance").val(tot-dis_val);
			//$("#advance").val(tot);
			$("#balance").val("0");
		}
		
		if(dis_val>tot)
		{
			$("#dis_per").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#dis_per").css({'border-color': '#CCC'});
		}
		if(dis_val>0)
		{
			$("#d_reason").fadeIn(500);
		}else
		{
			$("#d_reason").fadeOut(500);
		}
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#dis_per").val(val);
		}
	}
	function dis_amnt(val,e)
	{
		var tot=parseInt($("#total").val());
		if(tot==0)
		{
			var per=0;
		}else
		{
			var per=((val*100)/tot);
		}
		per=Math.round(per);
		$("#dis_per").val(per);
		
		if($("#pay_mode").val()=="Credit")
		{
			$("#balance").val(tot-val);
			//$("#balance").val(tot);
			$("#advance").val("0");
		}else
		{
			$("#advance").val(tot-val);
			//$("#advance").val(tot);
			$("#balance").val("0");
		}
		
		if(val>0)
		{
			if(val>tot)
			{
				$("#dis_amnt").css({'border-color': '#F00'}).focus();
			}else
			{
				$("#dis_amnt").css({'border-color': '#CCC'});
				if(e.which==13)
				{
					$("#dis_reason").focus();
				}
			}
			$("#d_reason").fadeIn(500);
		}else
		{
			$("#d_reason").fadeOut(500);
			if(e.which==13)
			{
				$({myScrollTop:window.pageYOffset}).animate({myScrollTop:800}, {
					duration: 1000,
					easing: 'swing',
					step: function(val){
						window.scrollTo(0, val);
					}
				});

				$("#dis_amnt").val("0");
				$("#advance").focus();
			}
		}
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#dis_amnt").val(val);
		}
	}
	function dis_reason(val,e)
	{
		if(e.which==13)
		{
			if(val=="")
			{
				$("#dis_reason").css({'border-color': '#F00'}).focus();
			}else
			{
				$("#dis_reason").css({'border-color': '#CCC'});
				$("#advance").focus();
			}
		}else
		{
			$("#dis_reason").css({'border-color': '#CCC'});
		}
	}
	function advance(val,e)
	{
		var tot=parseInt($("#total").val());
		var dis_amnt=parseInt($("#dis_amnt").val());
		var res=tot-dis_amnt;
		var bal=res-val;
		//var bal=tot-val;
		$("#balance").val(bal);
		if(bal<0)
		{
			$("#advance").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#advance").css({'border-color': '#CCC'});
		}
		if(bal<0)
		{
			$("#b_reason").fadeOut();
		}else if(bal>0)
		{
			if($("#pay_mode").val()!=="Credit")
			{
				$("#b_reason").fadeIn();
			}
			$({myScrollTop:window.pageYOffset}).animate({myScrollTop:800}, {
				duration: 1000,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});
		}else
		{
			$("#b_reason").fadeOut();
		}
		if(e.which==13)
		{
			if(bal<0)
			{
				$("#advance").focus();
			}else if(bal>0)
			{
				if($("#pay_mode").val()!=="Credit")
				{
					$("#bal_reason").val("");
					$("#bal_reason").focus();
				}else
				{
					$("#bal_reason").val("Credit");
					$("#pay_mode").focus();
				}
			}else
			{
				$("#pay_mode").focus();
			}
		}
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#advance").val(val);
		}
	}
	function bal_reason(val,e)
	{
		if(e.which==13)
		{
			if(val=="")
			{
				$("#bal_reason").css({'border-color': '#F00'}).focus();
			}else
			{
				$("#bal_reason").css({'border-color': '#CCC'});
				$("#pay_mode").focus();
			}
		}else
		{
			$("#bal_reason").css({'border-color': '#CCC'});
		}
	}
	function pay_mode(val,e)
	{
		if(e.which==13)
		{
			$("#save").focus();	
		}
	}
	///////// Save  ///////////////
	function save_pat(typ)
	{
		var error=0;
		var dis_amnt=$("#dis_amnt").val();
		if(dis_amnt>0)
		{
			if($("#dis_reason").val()=="")
			{
				$("#dis_reason").css({'border-color': '#F00'}).focus();
				error=1;
				return true;
			}
		}
		var balance=$("#balance").val();
		if(balance>0)
		{
			if($("#bal_reason").val()=="")
			{
				$("#bal_reason").css({'border-color': '#F00'}).focus();
				error=1;
				return true;
			}
		}
		if(error==0)
		{
			$("#save").prop("disabled",true);
			$("#loader").show();
			$.post("pages/lab_payment_receive_data.php",
			{
				type:"save_pat_payment",
				mode:typ,
				regd_fee:$("#regd_fee").val(),
				total:$("#total").val(),
				dis_per:$("#dis_per").val(),
				dis_amnt:dis_amnt,
				dis_reason:$("#dis_reason").val(),
				advance:$("#advance").val(),
				bal_reason:$("#bal_reason").val(),
				balance:balance,
				pay_mode:$("#pay_mode").val(),
				
				user:$("#user").text().trim(),
				uhid:$("#uhid").val().trim(),
				opd_id:$("#opd_id").val().trim(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<h5>"+typ+"d</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					window.location.reload(true);
				},2000);
			})
		}
	}
	function print_receipt(url)
	{
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function money_receipt(url)
	{
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
	}
	function load_test_print(uhid,opd)
	{
		$.post("pages/pat_reg_prints.php",
		{
			uhid:uhid,
			opd_id:opd
		},
		function(data,status)
		{
			$("#results2").html(data);
			//$(".modal-dialog").css({'width':'500px'});		
			$("#mod2").click();
			//$("#mod_chk").val("1");
			$("#results").fadeIn(500);
		})
	}
	function print_indiv(uhid,visit)
	{
		var norm=$(".norm:checked");
		var norm_l=0;
		if(norm.length>0)
		{
			for(var i=0;i<norm.length;i++)
			{
				norm_l=norm_l+"@"+$(norm[i]).val();
			}
		}
		
		var path=$(".path:checked");
		var path_l=0;
		if(path.length>0)
		{
			for(var j=0;j<path.length;j++)
			{
				path_l=path_l+"@"+$(path[j]).val();
			}
		}
		
		
		var rad=$(".rad:checked");
		var rad_l=0;
		if(rad.length>0)
		{
		for(var k=0;k<rad.length;k++)
			{
				rad_l=rad_l+"@"+$(rad[k]).val();
			}
		}

		//var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opd_id="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
		var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opdid="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');		
	}
	function cancel_pat(uhid,opd_id,typ)
	{
		//alert(uhid+' '+opd_id)
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to cancel</h5>",
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
						check_result(uhid,opd_id,typ);
					}
				}
			}
		});
	}
	function check_result(uhid,opd_id,typ)
	{
		$.post("pages/pat_cancel.php",
		{
			type:"pat_test_cancel_check",
			uhid:uhid,
			opd_id:opd_id,
			typ:typ,
		},
		function(data,status)
		{
			if(data==0)
			{
				cancel_note(uhid,opd_id,typ);
			}else
			{
				bootbox.alert("<h5>Patient can't be cancelled</h5>");
			}
		})
	}
	function cancel_note(uhid,opd_id,typ)
	{
		bootbox.dialog({
			message: "Reason:<input type='text' id='note' autofocus />",
			title: "Patient Cancel",
			buttons: {
				main: {
				  label: "Save",
				  className: "btn-primary",
				  callback: function() {
					if($('#note').val()!='')
					{
						//$("#img").show();
						//$("#dialog_msg").show().html("Cancelling...");
						$.post("pages/pat_cancel.php",
						{
							type:"pat_test_opd",
							uhid:uhid,
							opd_id:opd_id,
							typ:typ,
							reason:$('#note').val(),
							user:$('#user').text().trim(),
						},
						function(data,status)
						{
							$("#dialog_msg").show().html("Cancelled");
							setTimeout(function(){
								$("#img").hide();
								if(typ=="test")
								{
									check_investigation();
								}
								if(typ=="doc")
								{
									check_appointment();
									//show_print_details(uhid,q,opd_id);
								}
							},500);
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
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
</style>
