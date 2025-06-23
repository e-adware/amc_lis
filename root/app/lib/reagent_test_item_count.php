<?php
class reagent_test_item_count {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['branch_id']		=$arr['branch_id'];
		$columns['item_id']			=$arr['item_id'];
		$columns['stock']			=$arr['stock'];
		$columns['no_of_test']		=$arr['no_of_test'];
		$columns['total_test']		=$arr['total_test'];
		$columns['test_count']		=$arr['test_count'];
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
	
	private $table_name = "reagent_test_item_count";
}
?>
