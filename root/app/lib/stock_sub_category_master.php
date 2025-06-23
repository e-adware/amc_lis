<?php
class stock_sub_category_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['category_id']			=$arr['category_id'];
		$columns['sub_category_name']	=$arr['sub_category_name'];
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
	
	private $table_name = "stock_sub_category_master";
}
?>
