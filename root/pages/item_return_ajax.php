<?php
include("../includes/connection.php");

$date=date('Y-m-d');
$time=date('H:i:s');

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d M Y', $timestamp);
		return $new_date;
	}
}

$type=$_POST['type'];

if($type==1)
{
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Item Name</th>
			<th>Batch No</th>
			<th>Stock</th>
			<th>Quantity</th>
			<th></th>
		</tr>
		<tr>
			<td>
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="" placeholder="Item Name" />
				<input type="text" name="doc_id" id="doc_id" style="display:none;" value="">
				<div id="doc_info"></div>
				<div id="ref_doc" align="center" style="padding:8px;width:600px;">
					
				</div>
			</td>
			<td>
				<input type="text" id="hguide" style="width:80px;" onFocus="hguide_up(this.value,event,'batch')" onKeyUp="hguide_up(this.value,event,'batch')" onBlur="javascript:$('#hguide_div').fadeOut(500)" placeholder="Batch No" />
				<input type="text" name="hguide_id" id="hguide_id" style="display:none;" placeholder="Batch No" />
				<input type="text" name="bch_qnt" id="bch_qnt" style="display:none;" placeholder="Batch Quantity" />
				<input type="text" name="bch_mrp" id="bch_mrp" style="display:none;" placeholder="Batch MRP" />
				<input type="text" name="bch_gst" id="bch_gst" style="display:none;" placeholder="Batch GST" />
				<input type="text" name="bch_exp" id="bch_exp" style="display:none;" placeholder="Batch Exp Date" />
				<div id="hguide_info"></div>
				<div id="hguide_div" align="center" style="padding:8px;width:400px;">
					
				</div>
			</td>
			<td>
				<input type="text" id="stock" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');qnt_check(this.value,event)" placeholder="Stock" disabled />
			</td>
			<td>
				<input type="text" id="qnt" class="span1" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');qnt_check(this.value,event)" placeholder="Quantity" />
			</td>
			<td>
				<button type="button" id="btn_add" class="btn btn-primary" onclick="add_item_alt()">Add</button>
			</td>
		</tr>
	</table>
	<?php
}

if($type==999)
{
	
}
