<?php
class ph_sell_details {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['bill_id']			=$arr['bill_id'];
		$columns['bill_no']			=$arr['bill_no'];
		$columns['branch_id']		=$arr['branch_id'];
		$columns['substore_id']		=$arr['substore_id'];
		$columns['entry_date']		=$arr['entry_date'];
		$columns['stock_sl']		=$arr['slno'];
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['expiry_date']		=$arr['expiry_date'];
		$columns['sale_qnt']		=$arr['sale_qnt'];
		$columns['free_qnt']		=$arr['free_qnt'];
		$columns['mrp']				=$arr['mrp'];
		$columns['disc_mrp']		=$arr['disc_mrp'];
		$columns['total_amount']	=$arr['total_amount'];
		$columns['net_amount']		=$arr['net_amount'];
		$columns['gst_percent']		=$arr['gst_percent'];
		$columns['gst_amount']		=$arr['gst_amount'];
		$columns['item_cost_price']	=$arr['item_cost_price'];
		$columns['sale_price']		=$arr['sale_price'];
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
	
	public function delete($where) {
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->delete($where);
		return $ins;
	}
	
	private $table_name = "ph_sell_details";
}
?>
