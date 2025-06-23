<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<table class="table table-condensed entry_table">
			<tr>
				<th>Order No</th>
				<td>
					<input type="text" id="ord_no" list="ord_list" placeholder="Order No" onblur="load_supp()" class="imp" autofocus />
					<datalist id="ord_list">
					<?php
					$ords = mysqli_query($link,"SELECT `order_no` FROM `inv_purchase_order_master` WHERE `stat`='0' AND `del`='0' ORDER BY `order_no`");
					while($or=mysqli_fetch_array($ords))
					{
						echo "<option value='$or[order_no]'>";
					}
					?>
					</datalist>
				</td>
				<th>Select Supplier</th>
				<td colspan="4">
					<select id="supp" class="span5" disabled>
						<option value="0">Select Supplier</option>
						<?php
						$qsplr=mysqli_query($link,"select id,name from inv_supplier_master order by name");
						while($qsplr1=mysqli_fetch_array($qsplr))
						{
						?>
						<option value="<?php echo $qsplr1['id'];?>"><?php echo $qsplr1['name'];?></option>
						<?php
						}?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Supplier Bill No</th>
				<th colspan="3">Bill Date</th>
				<th colspan="2">Bill Amount &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  Received No</th>

			</tr>
			<tr>
				<td>
					<input type="text" id="bill_no" placeholder="Supplier Bill No" class="imp" />
				</td>
				<td colspan="3">
					<input type="text" id="billdate" placeholder="Bill Date" class="imp" />
				</td>
				<td colspan="2">
					<input type="text" id="bill_amt" class="imp span2" onkeyup="chk_num(this,event)" placeholder="Bill Amount" />&nbsp;<input type="text" id="txtgdsrcvno" class="imp span2"  placeholder="Received No" />
				</td>
				
			</tr>
			<tr>
				<th>Item</th>
				<th>Batch</th>
				<th>Expiry</th>
				<th>Quantity</th>
				<th>Free</th>
				<th><span id="gst_text">GST</span></th>
			</tr>
			<tr>
				<td>
					<input type="text" name="r_doc" id="r_doc" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="" placeholder="Item Name" />
					<input type="text" name="doc_id" id="doc_id" style="display:none;" value="">
					<div id="doc_info"></div>
					<div id="ref_doc" align="center" style="padding:8px;width:400px;">
						
					</div>
				</td>
				<td>
					<input type="text" id="batch" class="span2" placeholder="Batch No" />
				</td>
				<td>
					<input type="text" id="expiry" class="span2" maxlength="7" onkeyup="exp_dt(this.id,this.value,event)" placeholder="YYYY-MM" />
				</td>
				<td>
					<input type="text" id="qnt" class="span1" onkeyup="chk_num(this,event)" placeholder="Quantity" />
				</td>
				<td>
					<input type="text" id="free" class="span1" onkeyup="chk_num(this,event)" placeholder="Free" />
				</td>
				<td>
					<input type="text" id="gst" class="span1" onkeyup="chk_num(this,event)" placeholder="GST %" maxlength="2" />
				</td>
			</tr>
			<tr>
				<th>Pkd Qnt</th>
				<th>Strip MRP</th>
				<th>Strip Cost Price</th>
				<th>Discount %</th>
				<th>Item Amount</th>
				<th>Add</th>
			</tr>
			<tr>
				<td>
					<input type="text" id="pkd_qnt" class="span1" onkeyup="chk_num(this,event)" placeholder="Pkd Qnt" />
				</td>
				<td>
					<input type="text" id="mrp" class="span2" onkeyup="chk_dec(this,event);calc_mrp(this.value,event)" placeholder="MRP" />
					<input type="hidden" id="unit_sale" class="span2" disabled placeholder="Sale Price" />
				</td>
				<td>
					<input type="text" id="c_price" class="span2" onkeyup="chk_dec(this,event);cal_costprice(this.value,event)" placeholder="Cost Price" />
					<input type="hidden" id="unit_cost" class="span2" placeholder="Cost Price" />
				</td>
				<td>
					<input type="text" id="disc" class="span1" onkeyup="calc_disc(this.value,event)" placeholder="Discount" />
				</td>
				<td>
					<input type="text" id="itm_amt" class="span2" disabled placeholder="Item Amount" />
				</td>
				<td>
					<button type="button" id="add" class="btn btn-info" onclick="add_data()">Add</button>
				</td>
			</tr>
		</table>
		<div id="temp_item" style="min-height:200px;max-height:250px;overflow-y:scroll;">
		
		</div>
		<table class="table table-condensed entry_table">
			<tr>
				<th>Total</th>
				<td>
					<input type="text" id="total" class="imp span2" disabled placeholder="Total" />
				</td>
				<th>Discount</th>
				<td>
					<input type="text" id="discount" class="imp span2" onkeyup="chk_dec(this,event);calulate_discount(this.value,event)" placeholder="Discount" disabled />
				</td>
				<th>GST Amount</th>
				<td>
					<input type="text" id="all_gst" class="imp span2" disabled placeholder="GST Amount" />
				</td>
				<th>Net Amount</th>
				<td>
					<input type="text" id="net_total" class="imp span2" disabled placeholder="Net Amount" />
					<input type="hidden" id="net_total_n" class="imp span2"  />
				</td>
			</tr>
			
			<tr>
				<th>Transport Charge</th>
				<td colspan="7">
					<input type="text" id="txttransport" class="imp span2" placeholder="Transport Charge" value=0 onkeyup="calulate_delivry(this.value.event)" />
				</td>
						
			</tr>
			
			<tr>
				<td colspan="8" style="text-align:center;">
					<button type="button" id="sav" class="btn btn-primary" onclick="save_data_final()">Done</button>
					<button type="button" id="refres" class="btn btn-danger" onclick="new_entry()">Refresh</button>
				</td>
			</tr>
		</table>
		<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
		<div id="msgg" style="display:none;top:8%;left:40%;background:#FFFFFF;padding:5px;border-radius:4px;box-shadow:0px 0px 8px 0px #F67379;position:fixed;font-size:20px;font-weight:bold;color:#D90913"></div>
		<div id="loader" style="display:none;top:50%;left:50%;position:fixed;"></div>
</div>
<input type="hidden" id="chk_val1" />
<input type="hidden" id="chk_val2" />
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script src="../jss/inv_purchase_receive.js"></script>
<style>
.entry_table tr th, .entry_table tr td
{
	padding : 0px 4px;
}
input[type="text"]
{
	margin-bottom:0px;
}
.table-report
{
	background:#FFFFFF;
}
input[disabled]
{
    background: #FAFAFA;
}
</style>
