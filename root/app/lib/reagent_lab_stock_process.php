<?php
class reagent_lab_stock_process {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['no_of_test']		=$arr['no_of_test'];
		$columns['opening']			=$arr['opening'];
		$columns['received']		=$arr['received'];
		$columns['issue']			=$arr['issue'];
		$columns['quantity']		=$arr['quantity'];
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
		//$columns = $this->prepareColumns($arr);
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($arr)->update($where);
		return $ins;
	}
	
	private $table_name = "reagent_lab_stock_process";
}
?>
