<?php
class ph_purchase_receipt_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['branch_id']		=$arr['branch_id'];
		$columns['substore_id']		=$arr['substore_id'];
		$columns['order_no']		=$arr['order_no'];
		$columns['bill_date']		=$arr['bill_date'];
		$columns['recpt_date']		=$arr['recpt_date'];
		$columns['bill_amount']		=$arr['bill_amount'];
		$columns['gst_amt']			=$arr['gst_amt'];
		$columns['dis_amt']			=$arr['dis_amt'];
		$columns['net_amt']			=$arr['net_amt'];
		$columns['supp_code']		=$arr['supp_code'];
		$columns['bill_no']			=$arr['bill_no'];
		$columns['fid']				=0;
		$columns['user']			=$arr['user'];
		$columns['time']			=$arr['time'];
		$columns['adjust_type']		=$arr['adjust_type'];
		$columns['adjust_amt']		=$arr['adjust_amt'];
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
	
	private $table_name = "ph_purchase_receipt_master";
}
?>
