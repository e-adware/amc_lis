<?php
class ph_payment_details {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['branch_id']		=$arr['branch_id'];
		$columns['bill_no']			=$arr['bill_no'];
		$columns['substore_id']		=$arr['substore_id'];
		$columns['entry_date']		=$arr['entry_date'];
		$columns['amount']			=$arr['amount'] ?: 0; // $allPaid = ($paid ?: 0) + ($opaid ?: 0);
		$columns['payment_mode']	=$arr['payment_mode'];
		$columns['check_no']		=$arr['check_no'] ?? "";
		$columns['type_of_payment']	=$arr['type_of_payment'];
		$columns['user']			=$arr['user'];
		$columns['time']			=$arr['time'];
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
	
	private $table_name = "ph_payment_details";
}
?>
