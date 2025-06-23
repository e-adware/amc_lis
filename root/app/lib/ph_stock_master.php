<?php
class ph_stock_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['branch_id']		=$arr['branch_id'];
		$columns['substore_id']		=$arr['substore_id'];
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['quantity']		=$arr['quantity'];
		$columns['exp_date']		=$arr['exp_date'];
		$columns['recpt_mrp']		=$arr['mrp'];
		$columns['recept_cost_price']=$arr['cost'];
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
	
	private $table_name = "ph_stock_master";
}
?>
