<?php
include("../../includes/connection.php");

$date1=$_POST['date1'];
$date2=$_POST['date2'];
$branch_id=$_POST['branch_id'];

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}


if($_POST["type"]=="cat_test_detail")
{
	$val=$_POST["cat_test"];
	
	$dept_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `test_category_master` WHERE `category_id`='$val' "));
	if($dept_info)
	{
		$dept=$dept_info["name"];
	}
	
	$str=" SELECT distinct a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`date` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`testid`in ( SELECT `testid` FROM `testmaster` WHERE `category_id`='$val' ) and b.`branch_id`='$branch_id' order by a.`slno` ASC ";
	
	$pat_reg_qry=mysqli_query($link, $str);
	
	$pat_reg_num=mysqli_num_rows($pat_reg_qry);
	if($pat_reg_num!=0)
	{
?>
	<div style="margin-top: 2%;" id="print_div">
		<b>Test Details of <?php echo $dept; ?> from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
		<button class="btn btn-excel btn-mini text-right" href="pages/catwise_test_report_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&branch_id=<?php echo $branch_id;?>&val=<?php echo $val;?>&typ=<?php echo $typ;?>"><i class="icon-file"></i> Excel</button>
		
		<button class="btn btn-print btn-mini text-right" onclick="print_page('<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $branch_id;?>','<?php echo $val;?>')" style="margin-right: 1%;"><i class="icon-print"></i> Print</button>
		
	</div>
	<table class="table table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>UHID</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Amount</th>
			</tr>
		</thead>
	<?php
		$n=1;
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
			$pat_test_qry=mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `category_id`='$val' and `testid` in ( SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' and `ipd_id`='$pat_reg[ipd_id]' ) ");
			
			$all_test="";
			while($pat_test=mysqli_fetch_array($pat_test_qry))
			{
				$all_test.=$pat_test["testname"]." , ";
			}
			if( $pat_reg["opd_id"])
			{
				$pin= $pat_reg["opd_id"];
			}
			if( $pat_reg["ipd_id"])
			{
				$pin= $pat_reg["ipd_id"];
			}
			$pat_test_amount=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`test_rate`) as tot_amt FROM `patient_test_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' and `ipd_id`='$pat_reg[ipd_id]'  and `testid`in ( SELECT `testid` FROM `testmaster` WHERE `category_id`='$val' )"));
			$test_tot_amt=$pat_test_amount['tot_amt'];
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_reg["date"]; ?></td>
			<td><?php echo $pat_reg["patient_id"]; ?></td>
			<td><?php echo $pin; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $test_tot_amt; ?></td>
		</tr>
		<tr>
			<th colspan="3"><span class="text-right">Tests: </span></th>
			<td colspan="3"><i><?php echo $all_test; ?></i></td>
		</tr>
<?php
	$n++;
	}
?>
	</table>
<?php
	}
}

?>
