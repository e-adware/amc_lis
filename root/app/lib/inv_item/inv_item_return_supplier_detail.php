<?php
class inv_item_return_supplier_detail {
	
	public function save($arr) {
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($arr)->insert();
		return $ins;
	}
	
	
	
	
	private $table_name = "inv_item_return_supplier_detail";
}
?>
