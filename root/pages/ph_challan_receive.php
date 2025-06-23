<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<table class="table table-condensed entry_table">
			<tr>
				<th>Select Supplier</th>
				<td colspan="6">
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
			</tr>
			<tr>
				<th>Challan No</th>
				<th></th>
				<th colspan="2">Challan Date</th>
				<th colspan="3"><!--Bill Amount--></th>
			</tr>
			<tr>
				<td>
					<input type="text" id="bill_no" onblur="load_temp_item()" placeholder="Challan No" class="imp" />
				</td>
				<td>
					<span id="dup" style="display:none;color:#EA100C;text-shadow: 0px 0px 6px rgba(230, 22, 22, 0.7);">Duplicate Challan No</span>
				</td>
				<td colspan="2">
					<input type="text" id="billdate" placeholder="Challan Date" class="imp" />
				</td>
				<td colspan="3">
					<input type="hidden" id="bill_amt" class="imp span2" onkeyup="chk_num(this,event)" placeholder="Bill Amount" />
				</td>
			</tr>
			<tr>
				<th>Item</th>
				<th>Batch</th>
				<th>Expiry</th>
				<th>Quantity</th>
				<th>Free</th>
				<th>GST</th>
			</tr>
			<tr>
				<td>
					<input type="text" name="r_doc" id="r_doc" class="span4" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="" placeholder="Item Name" />
					<input type="text" name="doc_id" id="doc_id" style="display:none;" value="">
					<div id="doc_info"></div>
					<div id="ref_doc" align="center" style="padding:8px;width:400px;">
						
					</div>
				</td>
				<td>
					<input type="text" id="batch" list="batch_list" class="span2" placeholder="Batch No" />
					<datalist id="batch_list">
					
					</datalist>
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
				<th>Pkd Qnt <span style="float:right;">HSN Code &nbsp;&nbsp;&nbsp;&nbsp;</span></th>
				<th>MRP</th>
				<th>Cost Price</th>
				<th>Discount %</th>
				<th>Item Amount</th>
				<th>Add</th>
			</tr>
			<tr>
				<td>
					<input type="text" id="pkd_qnt" class="span1" onkeyup="chk_num(this,event)" placeholder="Pkd Qnt" />
					<span style="float:right;"><input type="text" id="hsn_code" class="span2" placeholder="HSN Code" /></span>
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
					<input type="text" id="total" class="imp text_sm" disabled placeholder="Total" />
				</td>
				<th>Discount</th>
				<td>
					<input type="text" id="discount" class="imp text_sm" onkeyup="chk_dec(this,event);calulate_discount(this.value,event)" placeholder="Discount" disabled />
				</td>
				<th>GST</th>
				<td>
					<input type="text" id="all_gst" class="imp text_sm" disabled placeholder="GST Amount" />
				</td>
				<th>Adjust</th>
				<td>
					<input type="text" id="adjust" class="imp text_sm" onkeyup="chk_dec(this,event);adjust(this,event)" placeholder="Adjust Amount" />
				</td>
				<th>Net Amount</th>
				<td>
					<input type="text" id="net_total" class="imp text_sm" disabled placeholder="Net Amount" />
				</td>
			</tr>
			<tr>
				<td colspan="10" style="text-align:center;">
					<button type="button" id="sav" class="btn btn-primary" onclick="save_data_final()">Done</button>
					<button type="button" id="refres" class="btn btn-danger" onclick="new_entry()">Refresh</button>
				</td>
			</tr>
		</table>
		<div id="old_item" style="display:none;width:99%;position:fixed;background: rgba(200, 210, 245, 0.6);left:1%;bottom:2%;max-height:200px;overflow-y:scroll;z-index:999;"></div>
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
<script src="../jss/ph_challan_receive.js"></script>
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
.text_sm
{
	width:120px !important;
}
</style>
