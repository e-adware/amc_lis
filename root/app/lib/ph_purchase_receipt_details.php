<?php
class ph_purchase_receipt_details {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['branch_id']		=$arr['branch_id'];
		$columns['substore_id']		=$arr['substore_id'];
		$columns['order_no']		=$arr['order_no'];
		$columns['bill_no']			=$arr['bill_no'];
		$columns['stock_sl']		=$arr['stock_sl'];
		$columns['item_id']			=$arr['item_id'];
		$columns['expiry_date']		=$arr['expiry_date'];
		$columns['recpt_date']		=$arr['recpt_date'];
		$columns['recpt_quantity']	=$arr['recpt_quantity'];
		$columns['free_qnt']		=$arr['free_qnt'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['supp_code']		=$arr['supp_code'];
		$columns['strip_quantity']	=$arr['strip_quantity'];
		$columns['recpt_mrp']		=$arr['recpt_mrp'];
		$columns['cost_price']		=$arr['cost_price'];
		$columns['recept_cost_price']=$arr['recept_cost_price'];
		$columns['sale_price']		=$arr['sale_price'];
		$columns['fid']				=0;
		$columns['item_amount']		=$arr['item_amount'];
		$columns['dis_per']			=$arr['dis_per'];
		$columns['dis_amt']			=$arr['dis_amt'];
		$columns['gst_per']			=$arr['gst_per'];
		$columns['gst_amount']		=$arr['gst_amt'];
        return $columns;
    }
	
	public function save($arr) {
		$columns = $this->prepareColumns($arr);
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($columns)->insert();
		return $ins;
	}
	
	public function updates($arr, $where) {
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($arr)->update($where);
		return $ins;
	}
	
	private $table_name = "ph_purchase_receipt_details";
}
?>
