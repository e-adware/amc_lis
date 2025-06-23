<?php
class reagent_test_item_count_process {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['branch_id']		=$arr['branch_id'];
		$columns['testid']			=0;
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['process_no']		=$arr['process_no'];
		$columns['process_type']	=$arr['process_type'];
		$columns['opening']			=$arr['opening'];
		$columns['quantity']		=$arr['quantity'];
		$columns['closing']			=$arr['closing'];
		$columns['date']			=$arr['date'];
		$columns['time']			=$arr['time'];
		$columns['user']			=$arr['user'];
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
	
	private $table_name = "reagent_test_item_count_process";
}
?>
