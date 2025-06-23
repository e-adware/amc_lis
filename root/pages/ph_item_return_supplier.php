<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
		<div class="" style="margin-left:0px;">
			<input type="text" id="chk_val1" style="display:none;" value="0" />
			<input type="text" id="chk_val2" style="display:none;" value="0" />
			<table class="table table-bordered table-condensed">
				<tr>
					<th>Select Supplier</th>
					<td colspan="7">
						<select id="supp" class="span5" autofocus>
							<option value="0">Select</option>
							<?php
								$qsplr=mysqli_query($link,"select id,name from inv_supplier_master order by name");
								while($qsplr1=mysqli_fetch_array($qsplr))
								{
							?>
								<option value="<?php echo $qsplr1['id'];?>"><?php echo $qsplr1['name'];?></option>
							<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
				   <th>Item Name</th>
				   <td colspan="3">
					<input type="text" id="r_doc" class="span5" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'item')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="" placeholder="Item Name OR Code" />
					<input type="text" id="doc_id" style="display:none;" value="">
					<div id="doc_info"></div>
					<div id="ref_doc" align="center" style="padding:8px;width:600px;display:none;">
						
					</div>
					</td>
					<th>
						Batch No
						<div id="hguide_info"></div>
						<div id="hguide_div" align="center" style="margin-top:10px;padding:6px;width:400px;"></div>
					</th>
					<td>
						<input type="text" id="hguide" class="span2" onFocus="hguide_up(this.value,event,'batch')" onKeyUp="hguide_up(this.value,event,'batch')" onBlur="javascript:$('#hguide_div').fadeOut(500)" placeholder="Batch No" />
						<input type="text" id="hguide_id" style="display:none;" placeholder="Batch No" />
						<input type="text" id="bch_qnt" style="display:none;" placeholder="Batch Quantity" />
						<input type="text" id="bch_mrp" style="display:none;" placeholder="Batch MRP" />
						<input type="text" id="bch_gst" style="display:none;" placeholder="Batch GST" />
						<input type="text" id="bch_exp" style="display:none;" placeholder="Batch Exp Date" />
					</td>
					<th>MRP</th>
					<td>
						<input type="text" id="mrp"  autocomplete="off" class="imp intext" onkeyup="numentry('txtmrp')" placeholder="MRP" style="width:100px" readonly />
					</td>
				</tr>
				<tr>
					<th>Stock In Hand</th>
					<td>
						<input type="text" id="stock" class="imp" onkeyup="numentry('txtqnt')" placeholder="avail Quantity" style="width:100px" readonly />
					</td>
					<th>Quantity</th>
					<td>
						<input type="text" id="qnt" class="imp intext" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Quantity" style="width:100px" />
					</td>
					<th>Free</th>
					<td>
						<input type="text" id="free"  autocomplete="off" class="imp intext" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Free" style="width:100px" />
					</td>
					<th>Expiry</th>
					<td>
						<input type="text" id="expiry"  autocomplete="off" class="imp intext"  placeholder="Expiry" style="width:100px" readonly />
					</td>
				</tr>
				
				<tr>
					<td colspan="8" style="text-align:center">
						<input type="button" id="button2" value="Reset" onclick="reset_all()" class="btn btn-danger" /> 
						<input type="button" id="button" value="Add" onclick="add_item()" class="btn btn-default" />
						<input type="button" id="button4" value="Done" onclick="insert_data_final()" class="btn btn-default" disabled />
						<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/inv_indent_order_rpt.php')" class="btn btn-success" disabled />-->
					</td>
				</tr>
			</table>
		</div>
		
		
		<div class="">
			<div id="msgg" style="display:none;background:#FFFFFF;position:fixed;color:#990909;font-size:22px;left:35%;top: 20%;padding: 25px;border-radius: 3px;box-shadow: 0px 1px 15px 1px #c57676;z-index:1000;"></div>
			<div id="temp_item" class="ScrollStyle"></div>
		</div>
	<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
	<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
</div>
<link rel="stylesheet" href="../css/loader.css" />
<script src="../jss/ph_item_return_supplier.js"></script>
