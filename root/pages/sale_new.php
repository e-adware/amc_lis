<?php
	$userid=$_SESSION['emp_id'];
	//mysqli_query($link,"delete from ph_sell_details_temp where user='$userid'");
	$vbilno="";
	$bill_id="";
	$sub_stor="";
	if($_GET["billno"])
	{
		$vbilno=base64_decode($_GET["billno"]);
	}
	if($_GET["service_id"])
	{
		$sub_stor=base64_decode($_GET["service_id"]);
	}
	if($_GET["show"])
	{
		$show=base64_decode($_GET["show"]);
		if($show==20)
		{
			$btn_disable="";
			$insert_func="save_items()";
		}
		if($show==10)
		{
			$insert_func="";
			$btn_disable="disabled='disabled'";
		}
	}
	else
	{
		$btn_disable="";
		$insert_func="save_items()";
	}
	if($_GET["orderno"])
	{
		$bill_id=base64_decode($_GET["orderno"]);
	}
	if($_GET["orderno"])
	{
		echo "<input type='hidden' id='bilupdate' value='1'/>";
		
		$check_bill_no=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ph_sell_master` WHERE `bill_id`='$bill_id' AND `bill_no`='$vbilno'"));
		if($check_bill_no>1)
		{
			$crr_yrs=date('Y');
			$crr_yr=date('y');
			$crr_mn=date('m');
			$crr_dy=date('d');
			$srch=$crr_yrs."-".$crr_mn;
			$start_bill=100;
			$count_bill="SELECT COUNT(`slno`) AS cnt FROM `ph_sell_master` WHERE `entry_date` like '$srch-%'";
			$bill=mysqli_fetch_array(mysqli_query($link,$count_bill));
			$bill_no_new=($start_bill+$bill['cnt']+1);
			$bill_no_new.="/".$crr_mn.$crr_yr;
			$bill_no_new=trim($bill_no_new); // display bill no
			
			// ph_sell_master, ph_sell_master_edit, ph_sell_details, ph_sell_details_edit
			//mysqli_query($link,"UPDATE `ph_sell_master` SET `bill_no`='$bill_no_new' WHERE `bill_id`='$bill_id' AND `bill_no`='$vbilno'");
			//mysqli_query($link,"UPDATE `ph_sell_master_edit` SET `bill_no`='$bill_no_new' WHERE `bill_id`='$bill_id' AND `bill_no`='$vbilno'");
			//mysqli_query($link,"UPDATE `ph_sell_details` SET `bill_no`='$bill_no_new' WHERE `bill_id`='$bill_id' AND `bill_no`='$vbilno'");
			//mysqli_query($link,"UPDATE `ph_sell_details_edit` SET `bill_no`='$bill_no_new' WHERE `bill_id`='$bill_id' AND `bill_no`='$vbilno'");
			
			echo '<script>window.location="processing.php?param=20&bill_id="'.$bill_id.'"&billno="'.$vbilno.'"&show=10;</script>';
		}
	}
	else
	{
		echo "<input type='hidden' id='bilupdate' value='0'/>";
	}
	
	if($p_info['levelid']!=1)
	{	
		$ip_addr=$_SERVER["REMOTE_ADDR"];
		
		$ip_addr_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `access_ip` WHERE `ip_addr`='$ip_addr' "));
		if($ip_addr_check)
		{
			$entry_val=1; //echo "OK";
		}else
		{
			$entry_val=0; //echo " NOT OK";
		}
	}else
	{
		$entry_val=1; //echo "OK";
	}
	

if($vbilno)
{
	$bill_ids=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_id`,`substore_id` FROM `ph_sell_master` WHERE `bill_no`='$vbilno'"));
	echo "<input type='hidden' value='$vbilno' id='txtupdtid'>";
	echo "<input type='hidden' value='$bill_ids[bill_id]' id='bill_id'>";
	echo "<input type='hidden' value='$bill_ids[substore_id]' id='sub_stor'>";
	$btn_val="Update";
}
else
{
	echo "<input type='hidden' value='0' id='txtupdtid'>";
	echo "<input type='hidden' value='0' id='bill_id'>";
	echo "<input type='hidden' value='0' id='sub_stor'>";
	$btn_val="Done";
}
$patient_id=base64_decode($_GET["uhid"]);
$pin=base64_decode($_GET["ipd"]);
$type=base64_decode($_GET["type"]);
$ind_num=base64_decode($_GET["ind_num"]);
if($patient_id)
{
	$p_info=mysqli_fetch_assoc(mysqli_query($link,"SELECT a.`name` FROM `patient_info` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND b.`opd_id`='$pin'"));
	$p_name=$p_info['name'];
	echo "<input type='hidden' value='$patient_id' id='patient_id'>";
	echo "<input type='hidden' value='$pin' id='pin'>";
	echo "<input type='hidden' value='$type' id='type'>";
	echo "<input type='hidden' value='$ind_num' id='indno'>";
}else
{
	$p_name="";
	echo "<input type='hidden' value='0' id='patient_id'>";
	echo "<input type='hidden' value='0' id='pin'>";
	echo "<input type='hidden' value='0' id='type'>";
	echo "<input type='hidden' value='0' id='indno'>";
}

// Check patients from last month
$date2 = date('Y-m-d');
$date1 = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date2)) . " -3 months"));
?>
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<script>
$(document).ready(function()
{
	if($("#bilupdate").val()==1)
	{
		$("#btn_print").focus();
	}else
	{
		//$("#ph").focus();
		$("#r_doc").focus();
		$("#ph").val(1);
	}
});
</script>
<style>
.table tr:hover{background:none;}

.ScrollStyle
{
	min-height:100px;
	max-height: 300px;
	overflow-y: scroll;
}
.inp_sm
{
	width:100px;
}
#mytable th, #mytable td
{
    padding: 1px 3px;
}
</style>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Sales Entry</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<input type="text" id="chk_val2" style="display:none;" value="0" />
	<input type="text" id="bill_no" style="display:none;" value="<?php echo $vbilno;?>" />
	<input type="text" id="bill_id" style="display:none;" value="<?php echo $bill_id;?>" />
	<div class="row"></div>
	<div class="">
		<div class="">
			<table class="table table-condensed table-bordered">
				<tr style="display:none;">
					<td>
						<b>Select Pharmacy</b>
						<select id="ph" onkeyup="next_tab(this.id,event)">
							<option value="1">Select</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Item Name</th>
					<th>Batch No</th>
					<th>Stock</th>
					<th>Quantity</th>
					<th></th>
				</tr>
				<tr>
					<td>
						<input type="text" name="r_doc" id="r_doc" class="span5" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="" placeholder="Item Name OR Code OR Short Name" />
						<input type="text" name="doc_id" id="doc_id" style="display:none;" value="">
						<div id="doc_info"></div>
						<div id="ref_doc" align="center" style="padding:8px;width:600px;">
							
						</div>
					</td>
					<td>
						<input type="text" name="hguide" id="hguide" class="span2" size="25" onFocus="hguide_up(this.value,event,'batch')" onKeyUp="hguide_up(this.value,event,'batch')" onBlur="javascript:$('#hguide_div').fadeOut(500)" placeholder="Batch No" />
						<input type="text" name="hguide_id" id="hguide_id" style="display:none;" placeholder="Batch No" />
						<input type="text" name="bch_qnt" id="bch_qnt" style="display:none;" placeholder="Batch Quantity" />
						<input type="text" name="bch_mrp" id="bch_mrp" style="display:none;" placeholder="Batch MRP" />
						<input type="text" name="bch_gst" id="bch_gst" style="display:none;" placeholder="Batch GST" />
						<input type="text" name="bch_exp" id="bch_exp" style="display:none;" placeholder="Batch Exp Date" />
						<div id="hguide_info"></div>
						<div id="hguide_div" align="center" style="padding:8px;width:400px;">
							<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
								<tr>
								<th>Batch No</th><th>Stock</th><th>Expiry</th>
								</tr>
								<?php
									$q=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `health_guide` WHERE `hguide_id`='HG101' ");
									$i=1;
									while($val=mysqli_fetch_array($q))
									{
								?>
									<tr onClick="hguide_load('<?php echo $val['hguide_id'];?>','<?php echo $val['name'];?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
										<td>
											<?php echo $val['hguide_id'];?>
										</td>
										<td>
											<?php echo $val['name'];?>
											<div <?php echo "id=dvhguide".$i;?> style="display:none;">
												<?php echo "#".$val['hguide_id']."#".$val['name'];?>
											</div>
										</td>
									</tr>
								<?php
									$i++;
									}
								?>
							</table>
						</div>
					</td>
					<td>
						<input type="text" id="stock" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');qnt_check(this.value,event)" placeholder="Stock" disabled />
					</td>
					<td>
						<input type="text" id="qnt" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');qnt_check(this.value,event)" placeholder="Quantity" />
					</td>
					<td>
						<button type="button" id="btn_add" class="btn btn-primary" onclick="add_item()">Add</button>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<div id="msgg" style="display:none;background:#FFFFFF;position:fixed;color:#990909;font-size:22px;left:35%;top: 20%;padding: 25px;border-radius: 3px;box-shadow: 0px 1px 15px 1px #c57676;z-index:1000;"></div>
						<div id="temp_item" class="ScrollStyle"></div>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<table class="table table-condensed table-bordered" style="background:none;">
							<tr>
								<th>PIN</th>
								<th>Customer name</th>
								<th colspan="2">Prescribed by</th>
								<th>Phone</th>
							</tr>
							<tr>
								<td>
									<input type="text" id="uhid" onfocus="scroll_page()" onkeyup="pat_det(this.value,event)" style="width:100px;" placeholder="PIN No." list="pin_no_list" value="<?php if($pin){echo $pin;}?>" <?php if($pin){echo "readonly";}?> />
									<datalist id="pin_no_list">
									<?php
										$pin_qry=mysqli_query($link," SELECT `opd_id` FROM `uhid_and_opdid` ORDER BY `slno` DESC LIMIT 0,9000");
										while($pin=mysqli_fetch_array($pin_qry))
										{
											echo "<option value='$pin[opd_id]'></option>";
										}
									?>
									</datalist>
								</td>
								<td>
									<input type="text" class="span4" id="cust_name" onkeyup="next_tab(this.id,event)" placeholder="Customer Name" value="<?php if($p_name){echo $p_name;}?>" />
								</td>
								<td colspan="2">
									<select id="ref_by" class="">
										<option value="0">Select</option>
										<?php
										$ref=mysqli_query($link,"SELECT `consultantdoctorid`,`name` FROM `consultant_doctor_master` ORDER BY `name`");
										while($rf=mysqli_fetch_array($ref))
										{
										?>
										<option value="<?php echo $rf['consultantdoctorid'];?>"><?php echo $rf['name'];?></option>
										<?php
										}
										?>
									</select>
								</td>
								<td>
									<input type="text" class="span2" id="contact" maxlength="10" onkeyup="chk_num(this.id,this.value,event)" placeholder="Contact" />
								</td>
							</tr>
							<tr>
								<th colspan="2">Address</th>
								<th>Care Of</th>
								<th>Bill Type</th>
								<th>Patient Type</th>
							</tr>
							<tr>
								<td colspan="2">
									<input type="text" class="span5" id="addr" onkeyup="next_tab(this.id,event)" placeholder="Address" />
								</td>
								<td>
									<input type="text" class="span2" id="co" onkeyup="next_tab(this.id,event)" placeholder="Care Of" />
								</td>
								<td>
									<select class="span2" id="bill_typ" onchange="change_bill(this.value)" onkeyup="next_tab(this.id,event)">
										<option value="1">Cash</option>
										<option value="2">Credit</option>
										<!--<option value="3">ESI</option>-->
										<option value="4">Card</option>
									</select>
								</td>
								<td>
									<select class="span2" id="pat_type" onchange="change_p_type(this.value)" onkeyup="next_tab(this.id,event)">
										<option value="1">General</option>
										<option value="2">ESIC</option>
										<option value="3">In House</option>
										<option value="4">Ayushman</option>
										<option value="5">Staff</option>
										<!--<option value="6">Donor</option>-->
									</select>
								</td>
							</tr>
						</table>
						<table class="table table-condensed table-bordered" style="background:none;">
							<tr>
								<th>Total (Round)</th>
								<th>GST Amount</th>
								<th>Discount %</th>
								<th>Discount Amount</th>
								<th>Adjust</th>
								<th>Paid</th>
								<th>Balance</th>
							</tr>
							<tr>
								<td>
									<input type="text" class="inp_sm" id="total" onkeyup="" placeholder="Total" disabled />
								</td>
								<td>
									<input type="text" class="inp_sm" id="gst" onkeyup="" placeholder="GST Amount" disabled />
								</td>
								<td>
									<input type="text" class="inp_sm" id="discount" onkeyup="chk_discount(this.id,this.value,event)" placeholder="Discount %"  />
								</td>
								<td>
									<input type="text" class="inp_sm" id="dis_amt" onkeyup="chk_num(this.id,this.value,event)" placeholder="Discount Rs" />
								</td>
								<td>
									<input type="text" class="inp_sm" id="adjust" onkeyup="chk_num(this.id,this.value,event)" placeholder="Adjust" />
								</td>
								<td>
									<input type="text" class="inp_sm" id="paid" onkeyup="chk_num(this.id,this.value,event)" placeholder="Paid" />
								</td>
								<td>
									<input type="text" class="inp_sm" id="balance" onkeyup="" placeholder="Balance" disabled />
								</td>
							</tr>
							<tr>
								<td colspan="7" style="text-align:center;">
									<button type="button" class="btn btn-danger" id="btn_reload" onclick="reload_page()">Refresh</button>
									<button type="button" class="btn btn-info" id="btn_save" onclick="<?php echo $insert_func;?>" <?php echo $btn_disable;?>><?php echo $btn_val;?></button>
									<button type="button" class="btn btn-primary" id="btn_print" onclick="print_bill()" disabled >Print</button>
									<input type="text" id="err" style="display:none" value="" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
</div>
<script src="../jss/sale_new.js"></script>
