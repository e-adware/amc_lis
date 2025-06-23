<?php
class reagent_indent_order_details {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['order_no']	=$arr['ord'];
		$columns['item_id']		=$arr['item_id'];
		$columns['quantity']	=$arr['qnt'];
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
		$columns = $this->prepareColumns($arr);
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($columns)->update($where);
		return $ins;
	}
	
	private $table_name = "reagent_indent_order_details";
}
?>
