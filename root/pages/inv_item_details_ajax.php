<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$type=$_POST['type'];

if($type==1)
{
	$itm=$_POST['itm'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$billno=$_POST['billno'];
	$user=$_POST['user'];

	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Item Name</th>
			<th>Stock</th>
			<th>No of Tests (per unit)</th>
			<th>Total Test</th>
			<th>Test Done</th>
			<th>Pending Tests</th>
		</tr>
	<?php
	$j=1;
	$bch="A";
	$p_type="2,3";
	if($itm)
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_test_item_count` WHERE `item_id`='$itm'");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `inv_test_item_count`");
	}
	while($r=mysqli_fetch_assoc($q))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $r['stock'];?></td>
			<td><?php echo $r['no_of_test'];?></td>
			<td><?php echo $r['stock']*$r['no_of_test'];?></td>
			<td><?php echo $r['test_count'];?></td>
			<td><?php echo $r['total_test'];?></td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<?php
}

if($type==2)
{
	$itm=$_POST['itm'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$billno=$_POST['billno'];
	$user=$_POST['user'];

	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Description</th>
			<th>Bill No</th>
			<th>Test</th>
			<th>Opening</th>
			<th>Quantity</th>
			<th>Close</th>
			<th>Process</th>
			<th>Date Time</th>
		</tr>
	<?php
	$bch="A";
	$p_type="2,3";
	if($itm)
	{
		$q=mysqli_query($link,"SELECT DISTINCT `item_id` FROM `inv_test_item_count_process` WHERE `item_id`='$itm' AND `date` BETWEEN '$fdate' AND '$tdate'");
	}
	else
	{
		$q=mysqli_query($link,"SELECT DISTINCT `item_id` FROM `inv_test_item_count_process` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	}
	while($r=mysqli_fetch_assoc($q))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]' "));
		?>
		<tr>
			<th></th>
			<th colspan="9"><?php echo $itm['item_name'];?></th>
		</tr>
		<?php
		$j=1;
		$qq=mysqli_query($link,"SELECT * FROM `inv_test_item_count_process` WHERE `item_id`='$r[item_id]' AND `date` BETWEEN '$fdate' AND '$tdate'");
		$num=mysqli_num_rows($qq);
		while($rr=mysqli_fetch_assoc($qq))
		{
			$pat=mysqli_fetch_assoc(mysqli_query($link,"SELECT a.`name`,a.`sex`,a.`age`,a.`age_type` FROM `patient_info` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND b.`opd_id`='$rr[process_no]'"));
			if(!$pat)
			{
				$pat=mysqli_fetch_assoc(mysqli_query($link,"SELECT a.`name`,a.`sex`,a.`age`,a.`age_type` FROM `patient_info` a, `uhid_and_opdid_cancel` b WHERE a.`patient_id`=b.`patient_id` AND b.`opd_id`='$rr[process_no]'"));
			}
			$process_no=$rr['process_no'];
			if($pat['sex']=="Male")
			{
				$sex="M";
			}
			if($pat['sex']=="Female")
			{
				$sex="F";
			}
			if($pat['sex']=="Other")
			{
				$sex="O";
			}
			if($pat['age_type']=="Years")
			{
				$age_type="Y";
			}
			if($pat['age_type']=="Months")
			{
				$age_type="M";
			}
			if($pat['age_type']=="Days")
			{
				$age_type="D";
			}
			$desc=$pat['name']." ".$pat['age']." ".$age_type." / ".$sex;
			$tst_name="";
			$tr_class="";
			if($rr['process_type']==1)
			{
				$process_type="Issue";
				$desc="Store Issue";
				$tst_name="-";
				$tr_class="greens";
				//$bl=mysqli_fetch_assoc(mysqli_query($link,"SELECT `bill_no` FROM `inv_main_stock_received_master` WHERE `receipt_no`='$process_no'"));
				//$process_no=$bl['bill_no'];
			}
			if($rr['process_type']==2)
			{
				$process_type="Test Entry";
				$tst=mysqli_fetch_assoc(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$rr[testid]'"));
				$tst_name=$tst['testname'];
				$tr_class="reds";
			}
			if($rr['process_type']==3)
			{
				$process_type="Patient Cancelled";
				$tst=mysqli_fetch_assoc(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$rr[testid]'"));
				$tst_name=$tst['testname'];
				$tr_class="greens";
			}
			if($rr['process_type']==4)
			{
				$process_type="Test Refund";
				$tst=mysqli_fetch_assoc(mysqli_query($link,"SELECT `testname` FROM `testmaster` WHERE `testid`='$rr[testid]'"));
				$tst_name=$tst['testname'];
				$tr_class="greens";
			}
			if($rr['process_type']==5)
			{
				$process_type="Add";
			}
			if($rr['process_type']==6)
			{
				$process_type="Deduct";
			}
			//~ if($rr['process_type']==7)
			//~ {
				//~ $process_type="Issue";
				//~ $desc="Store Issue";
			//~ }
			//~ if($rr['process_type']==8)
			//~ {
				//~ $process_type="Return";
				//~ $desc="Store Return";
			//~ }
		?>
		<tr class="<?php echo $tr_class;?>">
			<td><?php echo $j;?></td>
			<td><?php echo $desc;?></td>
			<td><?php echo $process_no;?></td>
			<td><?php echo $tst_name;?></td>
			<td><?php echo $rr['opening'];?></td>
			<td><?php echo $rr['quantity'];?></td>
			<td><?php echo $rr['closing'];?></td>
			<td><?php echo $process_type;?></td>
			<td><?php echo convert_date($rr['date'])." ".convert_time($rr['time']);?></td>
		</tr>
		<?php
		$j++;
		}
		?>
		<tr>
			<td colspan="10" style="padding:1px;background:#7F7F7F;"></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}

if($type==3)
{
	$pid=$_POST['pid'];
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Test Name</th>
			<th>Item Name</th>
			<th>No of test</th>
		</tr>
	<?php
	$j=1;
	$q=mysqli_query($link,"SELECT DISTINCT a.`testid`,b.`testname` FROM `radiology_maping` a, `testmaster` b WHERE a.`testid`=b.`testid` ORDER BY b.`testname`");
	while($r=mysqli_fetch_assoc($q))
	{
		$it=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_id`,`quantity` FROM `radiology_maping` WHERE `testid`='$r[testid]'"));
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name`,`no_of_test` FROM `item_master` WHERE `item_id`='$it[item_id]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['testname'];?></td>
			<td><?php echo $itm['item_name'];?></td>
			<td><?php echo $itm['no_of_test'];?></td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<?php
}

if($type==999)
{
	$user=$_POST['user'];
}
?>