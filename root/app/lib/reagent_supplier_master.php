<?php
class reagent_supplier_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['name']			=$arr['name'];
		$columns['contact']			=$arr['contact'];
		$columns['contact_person']	=$arr['contact_person'];
		$columns['email']			=$arr['email'];
		$columns['fax']				="";
		$columns['address']			=$arr['address'];
		$columns['gst_no']			=$arr['gst_no'];
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
	
	private $table_name = "reagent_supplier_master";
}
?>
