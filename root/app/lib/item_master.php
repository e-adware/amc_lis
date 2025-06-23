<?php
class item_master {
	// Method to prepare columns array
    private function prepareColumns(array $arr) {
		$columns['item_id']			=$arr['item_id'];
		$columns['short_name']		=$arr['short_name'];
		$columns['item_name']		=$arr['item_name'];
		$columns['hsn_code']		=$arr['hsn_code'];
		$columns['category_id']		=$arr['category_id'];
		$columns['sub_category_id']	=$arr['sub_category_id'];
		$columns['item_type_id']	=$arr['item_type_id'];
		$columns['re_order']		=0;
		$columns['no_of_test']		=0;
		$columns['critical_stock']	=0;
		$columns['generic_name']	=$arr['generic_name'];
		$columns['rack_no']			=$arr['rack_no'];
		$columns['manufacturer_id']	=$arr['manufacturer_id'];
		$columns['mrp']				=0;
		$columns['gst']				=$arr['gst'];
		$columns['strength']		=0;
		$columns['strip_quantity']	=$arr['strip_quantity'];
		$columns['unit']			="";
		$columns['specific_type']	=$arr['specific_type'];
		$columns['class']			=$arr['class'];
		$columns['need']			=$arr['need'];
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
	
	private $table_name = "item_master";
}
?>
