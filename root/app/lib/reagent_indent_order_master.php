<?php
class reagent_indent_order_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['order_no']	=$arr['ord'];
		$columns['date']		=$arr['date'];
		$columns['time']		=$arr['time'];
		$columns['user']		=$arr['user'];
		$columns['stat']		=$arr['stat'];
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
	
	private $table_name = "reagent_indent_order_master";
}
?>
