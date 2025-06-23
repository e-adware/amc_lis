<?php
class reagent_purchase_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['rcv_no']			=$arr['rcv_no'];
		$columns['supp_id']			=$arr['supp_id'];
		$columns['bill_no']			=$arr['bill_no'];
		$columns['bill_date']		=$arr['bill_date'];
		$columns['bill_amount']		=$arr['bill_amount'];
		$columns['gst_amount']		=$arr['gst_amount'];
		$columns['net_amount']		=$arr['net_amount'];
		$columns['date']			=$arr['date'];
		$columns['time']			=$arr['time'];
		$columns['emp_id']			=$arr['emp_id'];
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
		//$arr['rcv_no']			=$arr['rcv_no'];
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($arr)->update($where);
		return $ins;
	}
	
	private $table_name = "reagent_purchase_master";
}
?>
