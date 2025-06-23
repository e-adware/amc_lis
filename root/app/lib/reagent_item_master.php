<?php
class reagent_item_master {
	//~ public function item_master($itm,bch,$supp)
	//~ {
		//~ $filter "item_id = ? AND recept_batch = ? AND = SuppCode = ? ";
		//~ $db= new Db_Loader();
		//~ $row=$db->select(["recpt_mrp","recept_cost_price","expiry_date","dis_per","dis_amt","gst_per","gst_amount"],"inv_main_stock_received_detail")
				//~ ->filter($filter, [$itm,$bch,$supp])->fetch_row();
		//~ return $row;
	//~ }
	
	//~ private function gen_ret_no() {
		//~ $db = new Db_Loader();
		//~ $cn= $db->count("returnr_no",$this->table_name)->fetch_row();
		//~ $ret_no="RET".str_pad(($cn['TOTAL']+1), 6, 0, STR_PAD_LEFT);
		//~ return $ret_no;
	//~ }
	
	
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['item_code']		=$arr['item_code'];
		$columns['item_name']		=$arr['item_name'];
		$columns['department']		="";
		$columns['hsn_code']		=$arr['hsn_code'];
		$columns['gst_per']			=$arr['gst_per'];
		$columns['type_id']			=$arr['type_id'];
		$columns['pack_id']			=$arr['pack_id'];
		$columns['inst_id']			=$arr['inst_id'];
		$columns['no_of_test']		=$arr['no_of_test'];
		$columns['reorder_qnt']		=$arr['reorder_qnt'];
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
	
	private $table_name = "reagent_item_master";
}
?>
