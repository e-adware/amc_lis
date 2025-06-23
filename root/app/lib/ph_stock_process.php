<?php
class ph_stock_process {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['branch_id']		=$arr['branch_id'];
		$columns['substore_id']		=$arr['substore_id'];
		$columns['process_no']		=$arr['process_no'];
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['s_available']		=$arr['s_available'];
		$columns['added']			=$arr['added'];
		$columns['sell']			=$arr['sell'];
		$columns['return_cstmr']	=$arr['return_cstmr'];
		$columns['return_supplier']	=$arr['return_supplier'];
		$columns['s_remain']		=$arr['s_remain'];
		$columns['date']			=$arr['date'];
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
	
	private $table_name = "ph_stock_process";
}
?>
