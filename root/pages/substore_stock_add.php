<?php
// $vqnt	= quantity
// $itm		= item_id
// $bch		= batch_no
// $date	= current date

$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_substorestock_details` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm' AND `batch_no`='$bch' AND `date`='$date' ORDER BY `date` DESC"));
if($last_stock) // last stock of current date
{
	$txt.="\nSELECT * FROM `inv_substorestock_details` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm' AND `batch_no`='$bch' AND `date`='$date' ORDER BY `date` DESC";
	
	$opening=$last_stock['closing'];
	if(!$opening){$opening=0;}
	$add_qnt=($last_stock['receive']+$vqnt);
	$closing=($last_stock['closing']+$vqnt);

	mysqli_query($link,"UPDATE `inv_substorestock_details` SET `receive`='$add_qnt',`closing`='$closing' WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm' AND `batch_no`='$bch' AND `date`='$date'");
	$txt.="\nUPDATE `inv_substorestock_details` SET `receive`='$add_qnt',`closing`='$closing' WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm' AND `batch_no`='$bch' AND `date`='$date'";
}
else // last stock desc
{
	$last_stock=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `inv_substorestock_details` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm' AND `batch_no`='$bch' ORDER BY `date` DESC"));
	$txt.="\nSELECT * FROM `inv_substorestock_details` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm' AND `batch_no`='$bch' ORDER BY `date` DESC";
	
	$opening=$last_stock['closing'];
	if(!$opening){$opening=0;}
	$closing=$last_stock['closing']+$vqnt;

	mysqli_query($link,"INSERT INTO `inv_substorestock_details`(`branch_id`, `substore_id`, `item_id`, `batch_no`, `opening`, `receive`, `issue`, `closing`, `date`) VALUES ('$branch_id','$substore_id','$itm','$bch','$opening','$vqnt','0','$closing','$date')");
	$txt.="\nINSERT INTO `inv_substorestock_details`(`branch_id`, `substore_id`, `item_id`, `batch_no`, `opening`, `receive`, `issue`, `closing`, `date`) VALUES ('$branch_id','$substore_id','$itm','$bch','$opening','$vqnt','0','$closing','$date')";
}

$stk=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_substorestock_master` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm' AND `batch_no`='$bch'"));
if($stk)
{
	mysqli_query($link,"UPDATE `inv_substorestock_master` SET `closing`='$closing' WHERE `slno`='$stk[slno]'");
	$txt.="\nUPDATE `inv_substorestock_master` SET `closing`='$closing' WHERE `slno`='$stk[slno]'";
}
else
{
	mysqli_query($link,"INSERT INTO `inv_substorestock_master`(`branch_id`, `substore_id`, `item_id`, `batch_no`, `closing`, `exp_date`) VALUES ('$branch_id','$substore_id','$itm','$bch','$closing','$exp_dt')");
	$txt.="\nINSERT INTO `inv_substorestock_master`(`branch_id`, `substore_id`, `item_id`, `batch_no`, `closing`, `exp_date`) VALUES ('$branch_id','$substore_id','$itm','$bch','$closing','$exp_dt')";
}
?>