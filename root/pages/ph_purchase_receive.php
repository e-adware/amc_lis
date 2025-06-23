<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<table class="table table-condensed entry_table">
			<tr>
				<th colspan="2">Select Supplier</th>
				<th colspan="2">Supplier Bill No <span id="dup_bill">Duplicate Bill No</span></th>
				<th colspan="3">Bill Date</th>
			</tr>
			<tr>
				<td colspan="2">
					<select id="supp" class="span5"  autofocus>
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
				<td colspan="2">
					<input type="text" id="bill_no" placeholder="Supplier Bill No" class="imp" onblur="check_bill()" />
				</td>
				<td colspan="3">
					<input type="text" id="billdate" placeholder="Bill Date" class="imp" />
					<input type="hidden" id="bill_amt" class="imp span2" onkeyup="chk_num(this,event)" placeholder="Bill Amount" />
				</td>
			</tr>
			<tr>
				<th>Item</th>
				<th>Batch</th>
				<th>Expiry</th>
				<th>Quantity</th>
				<th>Free</th>
				<th>HSN Code</th>
				<th>GST</th>
			</tr>
			<tr>
				<td>
					<input type="text" id="r_doc" class="span3" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="$('#ref_doc').fadeOut(500)" placeholder="Item Name" />
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
					<input type="text" id="hsn" class="span2" placeholder="HSN Code" />
				</td>
				<td>
					<!--<input type="text" id="gst" class="span1" onkeyup="chk_num(this,event)" placeholder="GST %" />-->
					<select id="gst" class="span1" onkeyup="chk_num(this,event)">
						<option value="">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `gst_percent_master`");
						while($r=mysqli_fetch_assoc($q))
						{
						?>
						<option value="<?php echo $r['gst_per'];?>"><?php echo $r['gst_per'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Pkd Qnt</th>
				<th>Strip/Vial/Others MRP</th>
				<th>Strip/Vial/Others Cost Price</th>
				<th>Discount %</th>
				<th>Item Amount</th>
				<th>Rack No</th>
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
					<input type="text" id="disc" class="span1" onkeyup="chk_num(this,event);calc_disc(this.value,event)" placeholder="Discount" />
				</td>
				<td>
					<input type="text" id="itm_amt" class="span2" disabled placeholder="Item Amount" />
				</td>
				<td>
					<input type="text" id="rack_no" class="span2" placeholder="Rack No" />
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
.err
{
  border: 1px solid #EA0000 !important;
  box-shadow: #FF2020 0px 0px 10px 0px !important;
}
#dup_bill
{
	display:none;
	float:right;
	color: #C00000;
	text-align: center;
	animation: blinker 0.8s linear infinite;
}
@keyframes blinker
{
	50% {
	opacity: 0;
	}
}
</style>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script src="../jss/ph_purchase_receive.js"></script>