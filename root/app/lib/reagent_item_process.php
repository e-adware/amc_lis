<?php
class reagent_item_process {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['process_no']		=$arr['process_no'];
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['no_of_test']		=$arr['no_of_test'];
		$columns['testid']			=$arr['testid'];
		$columns['par_id']			=$arr['par_id'];
		$columns['opening']			=$arr['opening'];
		$columns['quantity']		=$arr['quantity'];
		$columns['closing']			=$arr['closing'];
		$columns['process_type']	=$arr['process_type'];
		$columns['date']			=$arr['date'];
		$columns['time']			=$arr['time'];
		$columns['emp_id']			=$arr['emp_id'];
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
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($arr)->update($where);
		return $ins;
	}
	
	private $table_name = "reagent_item_process";
}
?>
