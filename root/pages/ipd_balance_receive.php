<?php
$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$tot_serv_amt=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS tot_s FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));

$tot_amount=$tot_serv_amt["tot_s"];

$final_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as final,sum(`discount`) as discnt FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
$final_serv_amt=$final_serv["final"];
$adv_serv_dis=$final_serv["discnt"];

//$tot_balance=($tot_amount-$final_serv_amt-$adv_serv_dis);

$tot_bal_amt=mysqli_fetch_array(mysqli_query($link, " SELECT `bal_amount` FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));

$bal_bill=mysqli_fetch_array(mysqli_query($link," SELECT `bill_no` FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Balance' order by slno DESC "));

?>

<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Receive IPD Balance</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<th style="width: 20%;">UHDI | PIN</th>
			<td><?php echo $uhid; ?> | <?php echo $ipd; ?></td>
			<th>Patient Name</th>
			<td><?php echo $pat_info["name"]; ?></td>
		</tr>
		<tr>
			<th>Total Bill Amount</th>
			<td id="tot_bill_amt"><?php echo $tot_amount; ?></td>
			<th>Already Paid</th>
			<td><?php echo $final_serv_amt; ?></td>
		</tr>
		<tr>
			<th>Already Discount</th>
			<td>
				<?php echo $adv_serv_dis; ?>
			</td>
			<th>Balance</th>
			<td>
				<span id="already_balance"><?php echo $tot_bal_amt['bal_amount']; ?></span>
			</td>
		</tr>
		<tr>
			<th>Now Discount</th>
			<td>
				<input type="text" id="discount" onKeyUp="discount(this.value,event)" value="0" >
			</td>
			<th>Now Pay</th>
			<td>
				<input type="text" id="advance" onKeyUp="advance(this.value,event)" value="<?php echo $tot_bal_amt['bal_amount']; ?>" autofocus >
			</td>
		</tr>
		<tr>
			<th>Payment Mode</th>
			<td colspan="">
				<select id="pay_mode" onkeyup="pay_mode(this.value,event)" onChange="pay_mode_change(this.value)">
					<!--<option value="Cash">Cash</option>
					<option value="Card">Card</option>
					<option value="Cheque">Cheque</option>-->
				<?php
					$pay_mode_qry=mysqli_query($link, " SELECT `p_mode_id`,`p_mode_name` FROM `payment_mode_master` WHERE `status`=0 AND `operation`=1 ORDER BY `sequence` ");
					while($pay_mode=mysqli_fetch_array($pay_mode_qry))
					{
						if($pay_mode_center==$pay_mode["p_mode_name"]){ $sel_f="selected"; }else{ $sel_f=""; }
						echo "<option value='$pay_mode[p_mode_name]' $sel_f>$pay_mode[p_mode_name]</option>";
					}
				?>
				</select>
			</td>
			<th>Balance</th>
			<td>
				<span id="now_balance">0</span>
			</td>
		</tr>
		<tr id="reference_no_tr" style="display:none;">
			<th>Cheque/Reference No</th>
			<th colspan="3">
				<input type="text" class="span" id="reference_no">
			</th>
		</tr>
		<tr>
			<td colspan="4">
				<center>
				<?php if($tot_bal_amt['bal_amount']>0){ ?>
					<button class="btn btn-info" id="save" onClick="save_ipd_bal('<?php echo $uhid; ?>','<?php echo $ipd; ?>')">Save</button>
				<?php } ?>
				</center>
			</td>
		</tr>
	</table>
	<?php if($bal_bill){
		
		$bal_bill_list_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and pay_type='Balance' order by slno DESC ");
		
	?>
		<table class="table">
			<tr>
				<th>#</th>
				<th>Bill No.</th>
				<th>Paid Amoumt</th>
				<th>Bill Mode</th>
				<th>Date Time</th>
				<th>User</th>
				<th></th>
		<?php
			$n=1;
			while($bal_bill_list=mysqli_fetch_array($bal_bill_list_qry))
			{
				$user=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$bal_bill_list[user]'"));
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $bal_bill_list["bill_no"]; ?></td>
				<td><?php echo $bal_bill_list["amount"]; ?></td>
				<td><?php echo $bal_bill_list["pay_mode"]; ?></td>
				<td><?php echo $bal_bill_list["date"]; ?> <?php echo $bal_bill_list["time"]; ?></td>
				<td><?php echo $user["name"]; ?></td>
				<td>
					<button class="btn btn-success" id="print" onClick="print_payment_receipt('<?php echo $uhid; ?>','<?php echo $ipd; ?>','<?php echo $bal_bill_list['bill_no']; ?>','101')">Print</button>
				</td>
			</tr>
		<?php
				$n++;
			}
		?>
			</tr>
		</table>
	<?php } ?>
</div>
<script>
	function discount(a,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		
		var dis=parseInt($("#discount").val());
		if(!dis)
		{
			dis=0;
		}
		var alrdy_bal=parseInt($("#already_balance").text());
		
		var now_pay=alrdy_bal-dis;
		
		$("#advance").val(now_pay);
		
		if(now_pay<0)
		{
			$("#discount").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#discount").css({'border-color': '#CCC'});
		}
		
		if(unicode==13)
		{
			if(now_pay<0)
			{
				$("#discount").focus();
			}else
			{
				$("#advance").focus();
			}
		}
		
		$("#now_balance").text('0');
		
		var n=a.length;
		var numex=/^[0-9]+$/;
		if(a[n-1].match(numex))
		{
			
		}
		else
		{
			a=a.slice(0,n-1);
			$("#discount").val(a);
		}
	}
	function advance(a,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		
		var now_pay=parseInt($("#advance").val());
		if(!now_pay)
		{
			now_pay=0;
		}
		var dis=parseInt($("#discount").val());
		if(!dis)
		{
			dis=0;
		}
		var alrdy_bal=parseInt($("#already_balance").text());
		
		var now_bal=alrdy_bal-dis-now_pay;
		
		$("#now_balance").text(now_bal);
		
		if(now_bal<0)
		{
			$("#advance").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#advance").css({'border-color': '#CCC'});
		}
		
		if(unicode==13)
		{
			if(now_bal<0)
			{
				$("#advance").focus();
			}else
			{
				$("#save").focus();
			}
		}
		var n=a.length;
		var numex=/^[0-9]+$/;
		if(a[n-1].match(numex))
		{
			
		}
		else
		{
			a=a.slice(0,n-1);
			$("#advance").val(a);
		}
	}
	function save_ipd_bal(uhid,ipd)
	{
		var adv=parseInt($("#advance").val());
		if(!adv)
		{
			adv=0;
		}
		var dis=parseInt($("#discount").val());
		if(!dis)
		{
			dis=0;
		}
		
		var now_balance=parseInt($("#now_balance").text());
		if(now_balance<0)
		{
			$("#advance").focus();
			$("#advance").css({'border-color': '#F00'}).focus();
			return false;
		}
		if(adv>0)
		{
			$("#save").hide();
			$.post("pages/ipd_balance_receive_data.php",
			{
				type:"save_ipd_bal",
				uhid:uhid,
				ipd:ipd,
				discount:dis,
				advance:adv,
				pay_mode:$("#pay_mode").val(),
				reference_no:$("#reference_no").val(),
				tot_bill_amt:$("#tot_bill_amt").text().trim(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<h5>"+data+"</h5>"});
				setTimeout(function()
				{
					bootbox.hideAll();
					 window.location.reload(true);
				},2000);
			})
		}else
		{
			$("#advance").focus();
		}
	}
	function print_payment_receipt(uhid,ipd,bill,val)
	{
		//alert(uhid+" "+ipd+" "+bill);
		
		var user=$("#user").text().trim();
		
		if(val=='101')
		{
			//~ url="pages/dot_matrix_ipd_bill_type_detail_ipd.php?uhid="+uhid+"&ipd="+ipd+"&user="+user+"&bill="+bill;
			url="pages/ipd_payment_receipt.php?uhid="+uhid+"&ipd="+ipd+"&user="+user+"&bill="+bill;
		}
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function pay_mode_change(val)
	{
		$.post("pages/payment_load_data.php",
		{
			type:"payment_mode_change",
			val:val,
		},
		function(data,status)
		{
			var res=data.split("@#@");
			
			if(res[1]==2)
			{
				$("#reference_no_tr").hide;
			}
			else
			{
				if(res[0]==0)
				{
					$("#reference_no_tr").show();
				}
				else
				{
					$("#reference_no_tr").hide();
				}
			}
		})
	}
</script>
