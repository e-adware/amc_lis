<?php
class reagent_purchase_details {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['rcv_no']			=$arr['rcv_no'];
		$columns['supp_id']			=$arr['supp_id'];
		$columns['item_id']			=$arr['item_id'];
		$columns['batch_no']		=$arr['batch_no'];
		$columns['expiry_date']		=$arr['expiry_date'];
		$columns['quantity']		=$arr['quantity'];
		$columns['free']			=$arr['free'];
		$columns['no_of_test']		=$arr['no_of_test'];
		$columns['cost']			=$arr['cost'];
		$columns['dis_per']			=$arr['dis_per'];
		$columns['dis_amt']			=$arr['dis_amt'];
		$columns['gst_per']			=$arr['gst_per'];
		$columns['gst_amt']			=$arr['gst_amt'];
		$columns['item_amt']		=$arr['item_amt'];
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
	
	private $table_name = "reagent_purchase_details";
}
?>
