<?php
$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='1' "));
?>
<script src="../jss/pay_recv.js"></script>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<form id='p_receive'>
		<input type="hidden" id="chk" value="0"/>
		<div id="sel_pat" class="span11">
			<table class="table table-bordered table-condensed">
				<tr>
					<th><label for="precv2">Enter <?php echo $prefix_det["prefix"]; ?></label></th>
					<td>
						<input type="text" name="reg" id="reg" class="precv" onKeyUp="sel_pat_bill(this.value,event)" autofocus />
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div id="bal_pat" style="max-height:200px;overflow:scroll;overflow-x:hidden;display:none;" align="center">
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div id="pay_info" class="span5">
			<table class="table table-bordered table-condensed">
				<tr>
					<th><label for="pay1">Total Payment</th>
					<td><input type="text" name="pay1" id="pay1" disabled="disabled"/></td>
				</tr>
				<tr>
					<th><label for="pay2">Already Discount</th>
					<td><input type="text" name="pay2" id="pay2" disabled="disabled"/></td>
				</tr>
				<tr>
					<th><label for="pay5">Already Paid</th>
					<td><input type="text" name="pay5" id="pay5" disabled="disabled"/></td>
				</tr>
				<tr>
					<th><label for="pay4">Balance</th>
					<td><input type="text" name="pay4" id="pay4" disabled="disabled"/></td>
				</tr>
				<tr style="display:none;">
					<th><label for="tax_deduct">Tax Deduction</th>
					<td><input type="text" name="tax_deduct" id="tax_deduct" onkeyup="tax_deduct_up(this.value,event)"/></td>
				</tr>
				<tr style="display:none;">
					<th><label for="discount_now">Discount</th>
					<td>
						<input type="text" name="discount_now" id="discount_now" onkeyup="discount_now_up(this.value,event)"/>
						<input type="text" name="discount_reason" id="discount_reason" placeholder="Reason" onkeyup="discount_reason_up(this.value,event)" style="display:none;" />
					</td>
				</tr>
				<tr>
					<th><label for="pay3">Now Paid</th>
					<td><input type="text" name="pay3" id="pay3" onkeyup="sel_payment(this.value,event)"/></td>
				</tr>
				<tr>
					<th><label for="pay6">Payment Mode</th>
					<td>
						<!-- onChange="select_pmode(this.value)" -->
						<select name="pay6" id="pay6" onkeyup="next_ev(event)" onChange="pay_mode_change(this.value)">
							<option value="Cash">Cash</option>
							<option value="Card">Card</option>
							<option value="Cheque">Cheque</option>
							<option value="NEFT">NEFT</option>
							<option value="RTGS">RTGS</option>
						</select>
						<br>
						<input type="hidden" class="" id="cheque_ref_no" placeholder="Cheque / Reference No" onKeyUp="cheque_ref_no_up(this.value,event)">
						<div id="p_mode_info" style="display:none"></div>
					</td>
				</tr>
			</table>
		</div>
		<div id="pat_info" class="span5">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th><label for="pinfo1"><?php echo $prefix_det["prefix"]; ?></th>
					<td><input type="text" id="n_reg" name="n_rage" disabled="disabled"/><input type="hidden" name="pinfo1" id="pinfo1" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="pinfo2">Received Date</th>
					<td><input type="text" name="pinfo2" id="pinfo2" disabled="disabled"/></td>
				</tr>
				<tr style="display:none">
					<th><label for="pinfo3">Visit No</th>
					<td><input type="text" name="pinfo3" id="pinfo3" disabled="disabled"/></td>
				</tr>
				<tr>
					<th><label for="pinfo4">Name</th>
					<td><input type="text" name="pinfo4" id="pinfo4" disabled="disabled"/></td>
				</tr>
				<tr>
					<th><label for="pinfo5">Age - Sex</th>
					<td><input type="text" name="pinfo5" id="pinfo5" disabled="disabled"/></td>
				</tr>
				<!--<tr>
					<th><label for="pinfo6">Referred By</th>
					<td><input type="text" name="pinfo6" id="pinfo6" size="35" disabled="disabled"/></td>
				</tr>
				<tr>
					<th><label for="pinfo7">Center</th>
					<td><input type="text" name="pinfo7" id="pinfo7" size="35" disabled="disabled"/></td>
				</tr>-->
			</table>
		</div>
		<div style="clear:both"></div>
		<br/>
		<div id="butts" align="center">
			<input type="button" name="new" id="new" value="New" class="btn btn-custom" onClick="load_new()"/>
			<input type="button" name="recv" id="recv" value="Recieve" class="btn btn-custom" disabled="disabled" onClick="recv_payment()"/>
			<input type="button" name="mrec" id="mrec" value="Cash Receipt" class="btn btn-custom" onClick="print_mon('pages/print_cash_receipt.php?id=0')" />
		</div>
	</form>
	<script>load_pat()</script>
	<div id="back">
	</div>
	<div id="results" onKeyPress="tab_next(event)">
	</div>
	<div id="msg" align="center"></div>
</div>
<script>
	$(document).ready(function(){
		load_pat_bill('000');
	});
</script>
