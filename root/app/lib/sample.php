<?php
class sample_table_name {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
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
	
	private $table_name = "sample_table_name";
}
?>
