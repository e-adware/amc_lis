<?php
include("../../includes/connection.php");

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}

$date=date('Y-m-d');
$time=date('H:i:s');
$type=$_POST['type'];

if($type==1)
{
	$sub_store=$_POST['sub_store'];
	$all=$_POST['all'];
	$user=$_POST['user'];
	
	
	$bill_no_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`order_no`) as tot FROM `inv_substore_indent_order_master`"));
	$bill_no=$bill_no_qry["tot"]+1;
	
	$vid="REQ".str_pad($bill_no, 6, 0, STR_PAD_LEFT);
	//echo $vid;
	//$approval=0; // required approval
	$approval=1; // bydefault approved
	if(mysqli_query($link,"INSERT INTO `inv_substore_indent_order_master`(`order_no`, `substore_id`, `order_date`, `stat`, `approve`, `user`, `time`) VALUES ('$vid','$sub_store','$date','0','$approval','$user','$time')"))
	{
		$al=explode("#%#",$all);
		foreach($al as $a)
		{
			$v=explode("@",$a);
			$itm=$v[0];
			$qnt=$v[1];
			if($itm && $qnt)
			{
				mysqli_query($link,"INSERT INTO `inv_substore_order_details`(`order_no`, `item_id`, `substore_id`, `order_qnt`, `bl_qnt`, `order_date`, `stat`) VALUES ('$vid','$itm','$sub_store','$qnt','$qnt','$date','0')");
			}
		}
		echo "1";
	}
	else
	{
		echo "2";
	}
} // 1

if($type==2)
{
	$ord_no=$_POST['ord_no'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_substore_indent_order_master` WHERE `order_no`='$ord_no'"));
	echo $det['substore_id'];
} // 2

if($type==3)
{
	$ord_no=$_POST['ord_no'];
	$user=$_POST['user'];
	
	?>
	<table class='table table-condensed table-bordered table-report' id='mytable'>
		<tr><th style='width:5%;'>#</th><th>Description</th><th style='width:8%;'>Quantity</th><th style='width:5%;'>Remove</th></tr>
	<?php
	$j=1;
	$q=mysqli_query($link,"SELECT * FROM `inv_substore_order_details` WHERE `order_no`='$ord_no'");
	while($r=mysqli_fetch_assoc($q))
	{
		$itm=mysqli_fetch_assoc(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_id]'"));
		?>
		<tr class='all_tr'>
			<td><?php echo $j;?></td>
			<td><?php echo $itm['item_name'];?><input type='hidden' value='<?php echo $r['item_id'];?>' class='test_id'/></td>
			<td><?php echo $r['order_qnt'];?><input type='hidden' value='<?php echo $r['order_qnt'];?>' /></td>
			<td style='text-align:center;'>
				<span onclick='$(this).parent().parent().remove();set_slno()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span>
			</td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<?php
} // 3

if($type==4)
{
	$ord_no=$_POST['ord_no'];
	$sub_store=$_POST['sub_store'];
	$all=mysqli_real_escape_string($link,$_POST['all']);
	$user=$_POST['user'];
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT `stat` FROM `inv_substore_indent_order_master` WHERE `order_no`='$ord_no' AND `substore_id`='$sub_store'"));
	$det_items=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `inv_substore_order_details` WHERE `order_no`='$ord_no' AND `substore_id`='$sub_store' AND `stat`='1'"));
	if($det['stat']=="0" && !$det_items)
	{
		if(mysqli_query($link,"DELETE FROM `inv_substore_order_details` WHERE `order_no`='$ord_no' AND `substore_id`='$sub_store'"))
		{
			$al=explode("#%#",$all);
			foreach($al as $a)
			{
				$v=explode("@",$a);
				$itm=$v[0];
				$qnt=$v[1];
				if($itm && $qnt)
				{
					mysqli_query($link,"INSERT INTO `inv_substore_order_details`(`order_no`, `item_id`, `substore_id`, `order_qnt`, `bl_qnt`, `order_date`, `stat`) VALUES ('$ord_no','$itm','$sub_store','$qnt','$qnt','$date','0')");
				}
			}
			echo "1";
		}
		else
		{
			echo "2";
		}
	}
	else if($det['stat']=="0" && $det_items)
	{
		echo "3"; // partially received
	}
	else if($det['stat']=="1")
	{
		echo "4"; // all received
	}
	else
	{
		echo "2";
	}
} // 4

if($type==9899)
{
	
}
?>
