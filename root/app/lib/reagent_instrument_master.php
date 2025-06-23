<?php
class reagent_instrument_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['name']			=$arr['name'];
		$columns['report_text']		=$arr['report_text'];
		$columns['short_name']		=$arr['shname'];
        return $columns;
    }
    
	public function save($arr) {
		$columns = $this->prepareColumns($arr);
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($columns)->insert();
		return $ins;
	}
	
	public function updates($arr, $where) {
		$columns = $this->prepareColumns($arr);
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($columns)->update($where);
		return $ins;
	}
	
	private $table_name = "reagent_instrument_master";
}
?>
