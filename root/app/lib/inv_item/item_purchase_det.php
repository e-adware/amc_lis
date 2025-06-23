<?php
class item_purchase_det {
	public function item_purchase_detail($itm,bch,$supp)
	{
		$filter "item_id = ? AND recept_batch = ? AND = SuppCode = ? ";
		$db= new Db_Loader();
		$row=$db->select(["recpt_mrp","recept_cost_price","expiry_date","dis_per","dis_amt","gst_per","gst_amount"],"inv_main_stock_received_detail")
				->filter($filter, [$itm,$bch,$supp])->fetch_row();
		return $row;
	}
}
?>