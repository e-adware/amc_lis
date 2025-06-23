<?php
class ph_bill_generation {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['bill_id']			=$arr['bill_id'];
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
	
	private $table_name = "ph_bill_generation";
}
?>
