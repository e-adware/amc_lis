<?php
$substore_id=3; // laboratory id
$allTests="";
$txt.="\nSELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$process_no'";
$q=mysqli_query($link,"SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$process_no'");
while($rchk=mysqli_fetch_assoc($q))
{
	$testid=$rchk['testid'];
	$txt.="\nSELECT * FROM `inv_test_item_count_process` WHERE `testid`='$testid' AND `process_no`='$process_no' AND `process_type`='2'";
	$chk_count=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_test_item_count_process` WHERE `testid`='$testid' AND `process_no`='$process_no' AND `process_type`='2'"));
	if($chk_count)
	{
		//
	}
	else
	{
		$process_type=2;
		include("test_count_deduct.php");
	}
	if($allTests)
	{
		$allTests.=",".$testid;
	}
	else
	{
		$allTests=$testid;
	}
}

$txt.="\nSELECT DISTINCT `testid` FROM `inv_test_item_count_process` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `process_no`='$process_no' AND `testid` NOT IN ($allTests)";
$removeQry=mysqli_query($link,"SELECT DISTINCT `testid` FROM `inv_test_item_count_process` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `process_no`='$process_no' AND `testid` NOT IN ($allTests)");
while($rr=mysqli_fetch_assoc($removeQry))
{
	$testid=$rr['testid'];
	$txt.="\nSELECT `slno` FROM `inv_test_item_count_process` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `process_no`='$process_no' AND `testid`='$testid' AND `process_type`!='2'";
	$checkRemove=mysqli_fetch_assoc(mysqli_query($link,"SELECT `slno` FROM `inv_test_item_count_process` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `process_no`='$process_no' AND `testid`='$testid' AND `process_type`!='2'"));
	if(!$checkRemove)
	{
		$process_type=4;
		include("test_count_increase.php");
	}
}


/*
$txt.="\nSELECT * FROM `radiology_maping` WHERE `testid`='$testid'";
$q=mysqli_query($link,"SELECT * FROM `radiology_maping` WHERE `testid`='$testid'");
while($r=mysqli_fetch_assoc($q))
{
	$itm		= $r['item_id'];
	$quantity	= $r['quantity'];

	$tst_count=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_test_item_count` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm'"));
	$txt.="\nSELECT * FROM `inv_test_item_count` WHERE `branch_id`='$branch_id' AND `substore_id`='$substore_id' AND `item_id`='$itm'";
	if($tst_count)
	{
		if($tst_count['test_count']>0 && $tst_count['no_of_test']==$tst_count['test_count'])
		{
			mysqli_query($link,"UPDATE `inv_test_item_count` SET `test_count`='0' WHERE `slno`='$tst_count[slno]'");
			$txt.="\nUPDATE `inv_test_item_count` SET `test_count`='0' WHERE `slno`='$tst_count[slno]'";
			
			if($tst_count['stock']>0)
			{
				mysqli_query($link,"UPDATE `inv_test_item_count` SET `stock`=`stock`-1 WHERE `slno`='$tst_count[slno]'");
				$txt.="\nUPDATE `inv_test_item_count` SET `stock`=`stock`-1 WHERE `slno`='$tst_count[slno]'";
			}
			$tst_count['test_count']=0;
		}
		$opening		= $tst_count['total_test'];
		$total_test		= $tst_count['total_test']-$quantity;
		$test_count		= $tst_count['test_count']+$quantity;
		$closing		= $total_test;
		//$substore_id	= $tst_count['substore_id'];
		mysqli_query($link,"UPDATE `inv_test_item_count` SET `total_test`='$total_test', `test_count`='$test_count' WHERE `slno`='$tst_count[slno]'");
		$txt.="\nUPDATE `inv_test_item_count` SET `total_test`='$total_test', `test_count`='$test_count' WHERE `slno`='$tst_count[slno]'";
	}
	mysqli_query($link,"INSERT INTO `inv_test_item_count_process`(`branch_id`, `substore_id`, `testid`, `item_id`, `process_no`, `process_type`, `opening`, `quantity`, `closing`, `date`, `time`, `user`) VALUES ('$branch_id','$substore_id','$testid','$itm','$process_no','$process_type','$opening','$quantity','$closing','$date','$time','$user')");
	$txt.="\nINSERT INTO `inv_test_item_count_process`(`branch_id`, `substore_id`, `testid`, `item_id`, `process_no`, `process_type`, `opening`, `quantity`, `closing`, `date`, `time`, `user`) VALUES ('$branch_id','$substore_id','$testid','$itm','$process_no','$process_type','$opening','$quantity','$closing','$date','$time','$user')";
}
//*/
?>