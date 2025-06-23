<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");

$c_user=trim($_SESSION['emp_id']);

$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="load_all_pat")
{
	$fdate=$_POST["from"];
	$tdate=$_POST["to"];
	$bill=$_POST["bill"];
	
	$q="SELECT * FROM `ph_item_return` WHERE `date`='$date' AND `status`='0'";
	
	if($fdate && $tdate)
	{
		$q="SELECT * FROM `ph_item_return` WHERE `date` between '$fdate' and '$tdate'";
	}
	
	if($bill)
	{
		$q="SELECT * FROM `ph_item_return` WHERE `bill_no` like '$bill%'";
	}
	//$q.=" AND `type`='2' ";
	//$q.=" order by `sl_no` DESC";
	//echo $q;
	$qq_qry=mysqli_query($link, $q );
	$qq_num=mysqli_num_rows($qq_qry);
	
	if($qq_num>0)
	{
	?>
	<table class="table table-condensed table-bordered text-center">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Patient Type</th>
			<th>Return Amount</th>
			<th>Date Time</th>
			<th>Return</th>
		</tr>
	<?php
		$n=1;
		while($qq=mysqli_fetch_array($qq_qry))
		{
			$amt=$qq["total_amt"]-$qq["discount_amt"]-$qq["adjust_amt"];
			$cashier_access_num=0;
			$pat=mysqli_fetch_array(mysqli_query($link, " SELECT `customer_name`,`pat_type` FROM `ph_sell_master` WHERE `bill_no`='$qq[bill_no]' "));
			$p_typ=mysqli_fetch_array(mysqli_query($link, " SELECT `sell_name` FROM `ph_sell_type` WHERE `sell_id`='$pat[pat_type]'"));
			$typ=$p_typ["sell_name"];
			if($qq["status"]>0)
			{
				$dis="disabled='disabled'";
				$val="Returned";
				$clas="btn-danger";
				$func="";
			}
			else
			{
				$dis="";
				$val="Return";
				$clas="btn-info";
				$func="return_amt";
			}
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $qq["bill_no"]; ?></td>
				<td><?php echo $pat["customer_name"]; ?></td>
				<td><?php echo $typ; ?></td>
				<td><?php echo $rupees_symbol.$qq["amount"]; ?></td>
				<td><?php echo convert_date_g($qq["date"]); ?> <?php echo convert_time($qq["time"]); ?></td>
				<td><button type="button" class="btn <?php echo $clas;?>" <?php echo $dis;?> onclick="<?php echo $func;?>('<?php echo $qq['slno'];?>','<?php echo $qq['amount'];?>','<?php echo $qq['counter'];?>')"><?php echo $val;?></button></td>
			</tr>
		<?php
			$n++;
			
		}
	?>
	</table>
	<?php
	}
}

if($_POST["type"]=="ph_return_amt")
{
	$sl=$_POST["sl"];
	$cnt=$_POST["cnt"];
	$user=$_POST["user"];
	mysqli_query($link,"UPDATE `ph_item_return` SET `status`='1',`accp_user`='$user' WHERE `slno`='$sl'");
	echo "Amount Returned";
}
