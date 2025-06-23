<?php
// $vqnt	= quantity
// $itm		= item_id
// $bch		= batch_no
// $date	= current date

$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_mainstock_details` WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `batch_no`='$bch' AND `date`='$date' ORDER BY `date` DESC"));
if($last_stock) // last stock of current date
{
	$txt.="\nSELECT * FROM `inv_mainstock_details` WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `batch_no`='$bch' AND `date`='$date' ORDER BY `date` DESC";
	$opening=$last_stock['closing'];
	if(!$opening){$opening=0;}
	$issue=($last_stock['issue']+$vqnt);
	$closing=($last_stock['closing']-$vqnt);

	mysqli_query($link,"UPDATE `inv_mainstock_details` SET `issue`='$issue',`closing`='$closing' WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `batch_no`='$bch' AND `date`='$date'");
	$txt.="\nUPDATE `inv_mainstock_details` SET `issue`='$issue',`closing`='$closing' WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `batch_no`='$bch' AND `date`='$date'";
}
else // last stock desc
{
	$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_mainstock_details` WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `batch_no`='$bch' ORDER BY `date` DESC"));
	$txt.="\nSELECT * FROM `inv_mainstock_details` WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `batch_no`='$bch' ORDER BY `date` DESC";
	
	$opening=$last_stock['closing'];
	if(!$opening){$opening=0;}
	$issue=($last_stock['issue']+$vqnt);
	$closing=($last_stock['closing']-$vqnt);

	mysqli_query($link,"INSERT INTO `inv_mainstock_details`(`branch_id`, `item_id`, `batch_no`, `opening`, `receive`, `issue`, `closing`, `date`) VALUES ('$branch_id','$itm','$bch','$opening','0','$vqnt','$closing','$date')");
	$txt.="\nINSERT INTO `inv_mainstock_details`(`branch_id`, `item_id`, `batch_no`, `opening`, `receive`, `issue`, `closing`, `date`) VALUES ('$branch_id','$itm','$bch','$opening','0','$vqnt','$closing','$date')";
}

mysqli_query($link,"INSERT INTO `inv_item_process`(`branch_id`, `substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('$branch_id','$substore_id','$process_no','$itm','$bch','$opening','$vqnt','$closing','$process_type','$date','$time','$user')");
$txt.="\nINSERT INTO `inv_item_process`(`branch_id`, `substore_id`, `process_no`, `item_id`, `batch_no`, `opening`, `qnt`, `closing`, `process_type`, `date`, `time`, `user`) VALUES ('$branch_id','$substore_id','$process_no','$itm','$bch','$opening','$vqnt','$closing','$process_type','$date','$time','$user')";

$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_maincurrent_stock` WHERE `branch_id`='$branch_id' AND `item_id`='$itm' AND `batch_no`='$bch'"));
if($stk)
{
	mysqli_query($link,"UPDATE `inv_maincurrent_stock` SET `closing`='$closing' WHERE `slno`='$stk[slno]'");
	$txt.="\nUPDATE `inv_maincurrent_stock` SET `closing`='$closing' WHERE `slno`='$stk[slno]'";
}
else
{
	mysqli_query($link,"INSERT INTO `inv_maincurrent_stock`(`branch_id`, `item_id`, `batch_no`, `closing`, `exp_date`) VALUES ('$branch_id','$itm','$bch','$closing','$exp_dt')");
	$txt.="\nINSERT INTO `inv_maincurrent_stock`(`branch_id`, `item_id`, `batch_no`, `closing`, `exp_date`) VALUES ('$branch_id','$itm','$bch','$closing','$exp_dt')";
}
?>