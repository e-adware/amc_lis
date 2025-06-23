<?php
class reagent_stock_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['no_of_test']		=$arr['no_of_test'];
		$columns['expiry_date']		=$arr['expiry_date'];
		$columns['quantity']		=$arr['quantity'];
        return $columns;
    }
	
	public function save($arr) {
		$columns = $this->prepareColumns($arr);
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($columns)->insert();
		return $ins;
	}
	
	public function updates($arr, $where) {
		//$columns = $this->prepareColumns($arr);
		//$arr['quantity']		=$arr['quantity'];
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($arr)->update($where);
		return $ins;
	}
	
	private $table_name = "reagent_stock_master";
}
?>
