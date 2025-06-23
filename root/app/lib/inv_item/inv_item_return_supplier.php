<?php
class inv_item_return_supplier {
	
	public function save($arr) {
		$arr['returnr_no']=$this->gen_ret_no();
		$arr['date']=date("Y-m-d");
		$arr['time']=date("H:i:s");
		$db = new Db_Save();
		
		$ins = $db->set_table($this->table_name)->set_columns($arr)->insert();
		return $arr['returnr_no'];
	}
	
	private function gen_ret_no() {
		$db = new Db_Loader();
		$cn= $db->count("returnr_no",$this->table_name)->fetch_row();
		$ret_no="RET".str_pad(($cn['TOTAL']+1), 6, 0, STR_PAD_LEFT);
		return $ret_no;
	}
	
	
	private $table_name = "inv_item_return_supplier_master";
}
?>
