<?php
$itm_det		=mysqli_fetch_assoc(mysqli_query($link,"SELECT `no_of_test` FROM `item_master` WHERE `item_id`='$itm'"));
$no_of_test		=$itm_det['no_of_test'];

if($no_of_test>0)
{
$current_test	=($no_of_test*$vqnt);
$itm_chk		=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_test_item_count` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm'"));
if($itm_chk)
{
	$opening	=$itm_chk['total_test'];
	$stock		=($itm_chk['stock']+$vqnt);
	$quantity	=$current_test;
	$total_test	=($itm_chk['total_test']+$current_test);
	$closing	=$total_test;
	mysqli_query($link,"UPDATE `inv_test_item_count` SET `stock`='$stock', `no_of_test`='$no_of_test', `total_test`='$total_test' WHERE `slno`='$itm_chk[slno]'");
	$txt.="\nUPDATE `inv_test_item_count` SET `stock`='$stock', `no_of_test`='$no_of_test', `total_test`='$total_test' WHERE `slno`='$itm_chk[slno]'";
}
else
{
	$total_test	=$closing=$quantity=$current_test;
	$test_count	=0;
	$opening	=0;
	$stock		=$vqnt;
	mysqli_query($link,"INSERT INTO `inv_test_item_count`(`branch_id`, `substore_id`, `item_id`, `stock`, `no_of_test`, `total_test`, `test_count`) VALUES ('$branch_id','$substore_id','$itm','$stock','$no_of_test','$total_test','$test_count')");
	$txt.="\nINSERT INTO `inv_test_item_count`(`branch_id`, `substore_id`, `item_id`, `stock`, `no_of_test`, `total_test`, `test_count`) VALUES ('$branch_id','$substore_id','$itm','$stock','$no_of_test','$total_test','$test_count')";
}
mysqli_query($link,"INSERT INTO `inv_test_item_count_process`(`branch_id`, `substore_id`, `testid`, `item_id`, `process_no`, `process_type`, `opening`, `quantity`, `closing`, `date`, `time`, `user`) VALUES ('$branch_id','$substore_id','$testid','$itm','$process_no','$process_type','$opening','$quantity','$closing','$date','$time','$user')");
$txt.="\nINSERT INTO `inv_test_item_count_process`(`branch_id`, `substore_id`, `testid`, `item_id`, `process_no`, `process_type`, `opening`, `quantity`, `closing`, `date`, `time`, `user`) VALUES ('$branch_id','$substore_id','$testid','$itm','$process_no','$process_type','$opening','$quantity','$closing','$date','$time','$user')";
}
?>