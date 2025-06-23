<?php
class reagent_packing_master {
	
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['name']			=$arr['name'];
        return $columns;
    }
	
	public function save($arr) {
		$columns = $this->prepareColumns($arr);
		//$arr['date']=date("Y-m-d");
		//$arr['time']=date("H:i:s");
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
	
	private $table_name = "reagent_packing_master";
}
?>
