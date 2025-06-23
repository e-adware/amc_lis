<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

$bill=base64_decode($_GET["billno"]);

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ph_sell_master` WHERE `bill_no`='$bill' "));
$pat_pay_qry=mysqli_query($link, " SELECT * FROM `ph_payment_details` WHERE `bill_no`='$bill' ORDER BY sl_no DESC");
$pat_pay_num=mysqli_num_rows($pat_pay_qry);

if($pat_pay_num>0)
{
	$pat_pay_detail=mysqli_fetch_array($pat_pay_qry);
	$tot=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(sum(amount),0) as amount FROM `ph_payment_details` WHERE `bill_no`='$bill' "));
	$discount=$pat_pay_detail["dis_per"];
	$discount_amount=$pat_info["discount_amt"];
	$advance=$tot["amount"];
	$all_paid=$pat_info["total_amt"]-$pat_info["adjust_amt"]-$pat_info["discount_amt"]-$pat_info["balance"];
	$all_paid=number_format($all_paid,2);
	$balance=$pat_info["balance"];
	$dis_reason_str=$pat_pay_detail["dis_reason"];
	$bal_reason_str=$pat_pay_detail["bal_reason"];
	
	$btn_name="Update";
	
	$payment_mode_sel=$pat_pay_detail["payment_mode"];
}else
{	
	$discount=0;
	$advance=$pat_info["total_amt"]-$pat_info["discount_amt"]-$pat_info["adjust_amt"];
	$discount_amount=$pat_info["discount_amt"];
	$balance=$pat_info["balance"];
	$all_paid=$pat_info["paid_amt"];
	$dis_reason_str="";
	$bal_reason_str="";
	
	$btn_name="Save";
	
	$payment_mode_sel="Cash";
}
if($pat_info["balance"]==0)
{
	$mr_dis="";
}else
{
	$mr_dis="disabled";
}
if($p_info['levelid']==1)
{
	$dis_input="";
}else
{
	$dis_input="readonly";
}
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Receive Pharmacy Payment</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<th>BILL No</th>
			<td><?php echo $bill; ?></td>
			<th>Patient Name</th>
			<td><?php echo $pat_info["customer_name"]; ?></td>
		</tr>
		<tr>
			<th style="display:none;">Regd Fee</th>
			<td style="display:none;">
				<input type="text" id="regd_fee" value="0" readonly>
			</td>
			<th>Total</th>
			<td>
				<?php
				$amt=$pat_info["total_amt"];
				//$amt=number_format($amt,2);
				?>
				<input type="text" id="total" value="<?php echo $amt; ?>" readonly>
			</td>
			<th>Adjustment <input type="text" class="span1" id="adj" placeholder="" value="<?php echo $pat_info["adjust_amt"]; ?>" readonly="readonly" /></th>
			<th>
				Discount <input type="text" class="span1" id="disc" placeholder="" value="<?php echo $pat_info["discount_amt"]; ?>" readonly="readonly" />
				<input type="hidden" class="span1" id="dis_per" placeholder="%" onKeyUp="dis_per(this.value,event)" value="<?php echo $discount; ?>" autofocus <?php echo $dis_input; ?> >
				<input type="hidden" class="span1" id="dis_amnt" value="<?php echo $discount_amount; ?>" onKeyUp="dis_amnt(this.value,event)" <?php echo $dis_input; ?>><br>
				<span id="d_reason" style="display:none;"><input type="text" class="span2" id="dis_reason" placeholder="Reason for discount" onKeyUp="dis_reason(this.value,event)" value="<?php echo $dis_reason_str; ?>"></span>
			</th>
		</tr>
		<tr>
			<th>Paid</th>
			<td>
				<input type="text" id="all_paid" value="<?php echo $all_paid; ?>" readonly="readonly" />
			</td>
			<th>Balance <span id="warn" style="float:right;color:#DB0F0F;display:none;"><b class="icon-warning-sign icon-large"></b></span></th>
			<td>
				<input type="text" id="balance" value="<?php echo $balance; ?>" readonly>
			</td>
		</tr>
		<tr>
			<th>Current Payment</th>
			<td>
				<input type="text" id="advance" onKeyUp="advance(this.value,event)" value="<?php echo $balance; ?>" autofocus /><br>
				<span id="b_reason" style="display:none;"><input type="text" id="bal_reason" value="<?php echo $bal_reason_str; ?>" placeholder="Reason for balance" onKeyUp="bal_reason(this.value,event)"></span>
			</td>
			<th>Payment Mode</th>
			<td>
				<select id="pay_mode" onkeyup="pay_mode(this.value,event)" onChange="pay_mode_change(this.value)">
					<option value="Cash" <?php if($payment_mode_sel=="Cash"){ echo "selected"; } ?> >Cash</option>
					<option value="Card" <?php if($payment_mode_sel=="Card"){ echo "selected"; } ?>>Card</option>
					<option value="Cheque" <?php if($payment_mode_sel=="Cheque"){ echo "selected"; } ?>>Cheque</option>
				</select>
				<input type="hidden" value="<?php echo $credit_limit; ?>" id="credit_limit">
				<input type="hidden" value="<?php echo $pat_info['pat_type']; ?>" id="pat_typ">
			</td>
		</tr>
	</table>
	<?php
	$save_dis="";
	if($btn_name=="Update")
	{
	if($balance==0)
	{
		$save_dis="disabled='disabled'";
	}
	else
	{
		$save_dis="";
	}
	}
	?>
	<center>
		<input type="button" class="btn btn-info" id="save" value="<?php echo $btn_name; ?>" onClick="save_pat(this.value)" <?php echo $edit_dis; ?> <?php echo $save_dis; ?> >
		<input type="hidden" id="uhid" value="<?php echo $patient_id; ?>">
		<input type="hidden" id="opd_id" value="<?php echo $opd_id; ?>">
		<input type="hidden" id="bill_no" value="<?php echo $bill; ?>">
		
	<?php
		if($pat_pay_detail["amount"]>0)
		{
		?>
			<!--<button id="print_receipt" class="btn btn-info" onClick="print_receipt('pages/print_opd_test.php?uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')">Print</button>
			<button id="money_receipt" class="btn btn-info" onClick="money_receipt('pages/monyrecpt_rpt.php?uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')" <?php echo $mr_dis; ?>>Money Receipt</button>
			<input type="button" id="print" class="btn btn-info" value="Print Ind." onClick="load_test_print('<?php echo $uhid; ?>','<?php echo $opd_id; ?>')"/>
			-->
			<!--<button id="print_receipt" class="btn btn-info" onClick="money_receipt('pages/dot_matrix_monyrecpt_rpt.php?uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')" <?php echo $mr_dis; ?>>Dotmatrix Money Receipt</button>-->
			
			<a href="processing.php?param=228" class="btn btn-success">Back To List</a>
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
		var tot=parseFloat($("#total").val());
		var adj=parseFloat($("#adj").val());
		var dis_amnt=parseFloat($("#dis_amnt").val());
		var all_paid=parseFloat($("#all_paid").val());
		var res=tot-dis_amnt-adj-all_paid;
		var bal=res-val;
		//var bal=tot-val;
		//alert(tot);
		$("#balance").val(bal);
		
		if(bal<0)
		{
			$("#advance").css({'border-color': '#F00'}).focus();
			$("#balance").css({'border-color': '#F00'});
			$("#warn").show();
		}else
		{
			$("#advance").css({'border-color': '#CCC'});
			$("#warn").fadeOut(500);
			$("#balance").css({'border-color': ''});
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
			//~ $({myScrollTop:window.pageYOffset}).animate({myScrollTop:800}, {
				//~ duration: 1000,
				//~ easing: 'swing',
				//~ step: function(val){
					//~ window.scrollTo(0, val);
				//~ }
			//~ });
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
					//$("#bal_reason").val("");
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
		advance($("#advance").val().trim(),'');
		var error=0;
		var dis_amnt=$("#dis_amnt").val();
		/*if(dis_amnt>0)
		{
			if($("#dis_reason").val()=="" && $("#pat_typ").val()==1)
			{
				$("#dis_reason").css({'border-color': '#F00'}).focus();
				error=1;
				return true;
			}
		}
		*/
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
		if(balance<0)
		{
			$("#balance").css({'border-color': '#F00'});
			$("#warn").show();
			$("#advance").focus();
			error=1;
			return true;
		}
		if(error==0)
		{
			var bal=parseInt($("#balance").val());
			var bal_reason="";
			if(bal>0)
			{
				bal_reason=$("#bal_reason").val();
			}
			else
			{
				bal_reason="";
			}
			$("#save").prop("disabled",true);
			$("#loader").show();
			//alert(bal_reason);
			$.post("pages/ph_payment_receive_data.php",
			{
				type:"save_pat_payment",
				mode:typ,
				regd_fee:$("#regd_fee").val(),
				total:$("#total").val(),
				dis_per:$("#dis_per").val(),
				dis_amnt:dis_amnt,
				dis_reason:$("#dis_reason").val(),
				advance:$("#advance").val(),
				bal_reason:bal_reason,
				balance:balance,
				pay_mode:$("#pay_mode").val(),
				
				bill_no:$("#bill_no").val(),
				
				user:$("#user").text().trim(),
				uhid:$("#uhid").val().trim(),
				opd_id:$("#opd_id").val().trim(),
			},
			function(data,status)
			{
				//alert(data);
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
