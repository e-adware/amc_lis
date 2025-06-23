<?php
class inv_indent_master {
    public function __construct() {
        parent::__construct();
    }
    
    public function return_supplier() {
		$arr = filter_input_array(INPUT_POST);
		//var_dump($arr);
		$columns=array(
			//"`returnr_no`, `supplier_id`, `amount`, `gst_amount`, `net_amount`, `date`, `stat`, `del`, `user`, `time`"
			"supplier_id"	=>$arr['supp'],
			"amount"		=>$arr['final_rate'],
			"gst_amount"	=>$arr['final_gst'],
			"net_amount"	=>($arr['final_rate']+$arr['final_gst']),
			"stat"			=>0,
			"del"			=>0,
			"user"			=>$arr['user']
		);
		$master=$this->lib("inv_item/inv_item_return_supplier");
		$ret_no=$master->save($columns);
				
		$details=$this->lib("inv_item/inv_item_return_supplier_detail");
		$purc_det=$this->lib("inv_item/item_purchase_det");
		foreach($arr[items] as $itms)
		{
			$p_det=$purc_det->item_purchase_detail($itms['itm'],$itms['bch'],$arr['supp']);
			$arr=array(
				"returnr_no"		=>$ret_no,
				"reason"			=>$arr['reason'],
				"item_id"			=>$itms['itm'],
				"expiry_date"		=>$p_det['expiry_date'],
				"date"				=>date("Y-m-d"),
				"quantity"			=>$itms['qnt'],
				"free_qnt"			=>$itms['free'],
				"batch_no"			=>$itms['bch'],
				"supplier_id"		=>$arr['supp'],
				"recpt_mrp"			=>$itms['mrp'],
				"recept_cost_price"	=>$itms['rate'],
				"item_amount"		=>$itms['amount'],
				"dis_per"			=>$p_det['dis_per'],
				"dis_amt"			=>0,
				"gst_per"			=>$itms['gst_per'],
				"gst_amount"		=>$itms['gst_amount'],
				"bill_no"			=>$itms['bill_no']
			);
			$details->save($itms);
		}
		
	}
    
}
?>
