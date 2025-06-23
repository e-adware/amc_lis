<?php
class reagent_indent_issue_details {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['issue_no']	=$arr['issue_no'];
		$columns['order_no']	=$arr['order_no'];
		$columns['item_id']		=$arr['item_id'];
		$columns['batch_no']	=$arr['batch_no'];
		$columns['quantity']	=$arr['quantity'];
		$columns['stock_sl']	=$arr['stock_sl'];
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
	
	private $table_name = "reagent_indent_issue_details";
}
?>
