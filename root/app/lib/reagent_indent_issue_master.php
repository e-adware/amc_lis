<?php
class reagent_indent_issue_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['issue_no']	=$arr['issue_no'];
		$columns['date']		=$arr['date'];
		$columns['time']		=$arr['time'];
		$columns['emp_id']		=$arr['emp_id'];
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
	
	private $table_name = "reagent_indent_issue_master";
}
?>
