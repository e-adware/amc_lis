<?php
class ph_sell_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['branch_id']			=$arr['branch_id'];
		$columns['bill_id']				=$arr['bill_id'];
		$columns['bill_no']				=$arr['bill_no'];
		$columns['substore_id']			=$arr['substore_id'];
		$columns['entry_date']			=$arr['entry_date'];
		$columns['customer_name']		=$arr['customer_name'];
		$columns['customer_phone']		=$arr['customer_phone'];
		$columns['address']				=$arr['address'];
		$columns['co']					=$arr['co'];
		$columns['total_amt']			=$arr['total_amt'];
		$columns['discount_perchant']	=$arr['discount_perchant'];
		$columns['discount_amt']		=$arr['discount_amt'];
		$columns['adjust_amt']			=$arr['adjust_amt'];
		$columns['paid_amt']			=$arr['paid_amt'];
		$columns['balance']				=$arr['balance'];
		$columns['bill_type_id']		=$arr['bill_type_id'];
		$columns['patient_id']			=$arr['patient_id'];
		$columns['opd_id']				=$arr['opd_id'];
		$columns['ipd_id']				=$arr['ipd_id'];
		$columns['patient_type']		=$arr['patient_type'];
		$columns['refbydoctorid']		=$arr['refbydoctorid'];
		$columns['pat_type']			=$arr['pat_type'];
		$columns['user']				=$arr['user'];
		$columns['time']				=$arr['time'];
		$columns['round_type']			=$arr['round_type'];
		$columns['round']				=$arr['round'];
		$columns['gst_amount']			=$arr['gst_amount'];
		$columns['return_amt']			=$arr['return_amt'];
		$columns['return_adjust']		=$arr['return_adjust'];
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
	
	private $table_name = "ph_sell_master";
}
?>
