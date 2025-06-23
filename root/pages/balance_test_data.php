<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$date=date("Y-m-d");
$time=date("H:i:s");

$date1=$_POST['date1'];
$date2=$_POST['date2'];
$sel_test=$_POST['sel_test'];
$val=$_POST['val'];
$branch_id=$_POST['branch_id'];
$all_sel_test=implode(",",$sel_test);

$branch_str="";
$branch_str_a="";
$branch_str_b="";
if($branch_id>0)
{
	$branch_str=" AND `branch_id`='$branch_id'";
	$branch_str_a=" AND a.`branch_id`='$branch_id'";
	$branch_str_b=" AND b.`branch_id`='$branch_id'";
}

//print_r($sel_test);

//echo $_POST["type"];
if($_POST["type"]=="test_status")
{
?>
	<b>Test status list from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
	
	<button type="button" class="btn btn-info btn-mini text-right print_btn" onclick="print_page()" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	
	<span style="float:right;">Printing time <?php echo date("d-M-Y h:i:s A"); ?></span>
	
	<table class="table table-condensed table-hover" id="data_table">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Hospital No</th>
				<th>Patient Name</th>
				<th>Reg Date Time</th>
				<th>Collection Time</th>
				<th>Status</th>
			</tr>
		</thead>
<?php
	$same_date="";
	if($all_sel_test)
	{
		$date_qry=mysqli_query($link, " SELECT DISTINCT a.`date` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`testid` IN ($all_sel_test) AND a.`date` BETWEEN '$date1' AND '$date2' ");
	}
	else
	{
		$date_qry=mysqli_query($link, " SELECT DISTINCT a.`date` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`date` BETWEEN '$date1' AND '$date2' ");
	}
	while($date=mysqli_fetch_array($date_qry))
	{
		if($same_date!=$date["date"])
		{
			echo "<tr><th></th><th colspan='5'>Date : ".date("d-M-Y", strtotime($date["date"]))."</th></tr>";
		}
		$same_date=$date["date"];
		
		if($sel_test=="null")
		{
			$sel_test=array();
			$test_str=" SELECT DISTINCT a.`testid` FROM `patient_test_details` a, `uhid_and_opdid` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid` AND a.`date`='$same_date' $branch_str_b";
			$test_qry=mysqli_query($link, $test_str);
			$t=0;
			while($test=mysqli_fetch_array($test_qry))
			{
				$sel_test[$t]=$test["testid"];
				$t++;
			}
			
		}
		
		foreach($sel_test as $testid)
		{
			if($testid)
			{
				$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$testid' "));
				
				$pat_test_num=mysqli_num_rows(mysqli_query($link, " SELECT a.`testid` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`='$testid' AND a.`date`='$same_date' $branch_str_b "));
				// (Total=$pat_test_num)
				echo "<tr id='tr$testid'><th colspan='6'>$test_info[testname]</th></tr>";
				
				$str="SELECT a.*,b.`batch_no`,b.`testid` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND b.`testid`='$testid' AND b.`date`='$same_date' $branch_str_a";
				
				$n=1;
				$pat_reg_qry=mysqli_query($link, $str);
				//$pat_reg_num=mysqli_num_rows($pat_reg_qry);
				
				while($pat_reg=mysqli_fetch_array($pat_reg_qry))
				{
					$branch_info=mysqli_fetch_array(mysqli_query($link, " SELECT `lab_id_suff` FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' "));
					
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name`,`hosp_no`,`phone` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
					
					$sampColl=mysqli_fetch_array(mysqli_query($link, "SELECT `time`,`date` FROM `phlebo_sample` WHERE `patient_id`='$pat_reg[patient_id]' AND (`opd_id`='$pat_reg[opd_id]' OR `ipd_id`='$pat_reg[opd_id]') AND `batch_no`='$pat_reg[batch_no]' AND `testid`='$pat_reg[testid]'"));
					
					$pat_testresult_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults` WHERE `patient_id`='$pat_reg[patient_id]' AND (`opd_id`='$pat_reg[opd_id]' OR `ipd_id`='$pat_reg[opd_id]') AND `batch_no`='$pat_reg[batch_no]' AND `testid`='$pat_reg[testid]' "));
					
					$pat_testresult_rad_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults_rad` WHERE `patient_id`='$pat_reg[patient_id]' AND (`opd_id`='$pat_reg[opd_id]' OR `ipd_id`='$pat_reg[opd_id]') AND `batch_no`='$pat_reg[batch_no]' AND `testid`='$pat_reg[testid]' "));
					
					$pat_testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults_card` WHERE `patient_id`='$pat_reg[patient_id]' AND (`opd_id`='$pat_reg[opd_id]' OR `ipd_id`='$pat_reg[opd_id]') AND `batch_no`='$pat_reg[batch_no]' AND `testid`='$pat_reg[testid]' "));
					
					$test_summary_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `patient_test_summary` WHERE `patient_id`='$pat_reg[patient_id]' AND (`opd_id`='$pat_reg[opd_id]' OR `ipd_id`='$pat_reg[opd_id]') AND `batch_no`='$pat_reg[batch_no]' AND `testid`='$pat_reg[testid]' "));
					
					$pat_widalresult_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `widalresult` WHERE `patient_id`='$pat_reg[patient_id]' AND (`opd_id`='$pat_reg[opd_id]' OR `ipd_id`='$pat_reg[opd_id]') AND `batch_no`='$pat_reg[batch_no]' "));
					
					$tot_num=$pat_testresult_num+$pat_testresult_rad_num+$pat_testresult_card_num+$test_summary_num+$pat_widalresult_num;
					
					$status_style="color:red !important;";
					$status="Pending";
					if($tot_num>0)
					{
						$status="Reported";
						$status_style="color:green !important;";
					}
					if($val==0 || ($val==1 && $tot_num==0) || ($val==2 && $tot_num>0))
					{
						$collDate="";
						if($sampColl["date"] && $sampColl["date"]!="0000-00-00")
						{
							$collDate=date("d-m-y", strtotime($sampColl["date"]))." ".date("h:i A", strtotime($sampColl["time"]));
						}
?>
				<tr class="td<?php echo $testid; ?>">
					<td><?php echo $n; ?></td>
					<td><?php echo $pat_info["hosp_no"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo date("d-m-y", strtotime($pat_reg["date"]))." ".date("h:i A", strtotime($pat_reg["time"])); ?></td>
					<td><?php echo $collDate; ?></td>
					<td style="<?php echo $status_style; ?>"><?php echo $status; ?></td>
				</tr>
<?php
						$n++;
					}
				}
				//echo "<script>alert(".$testid.");</script>";
				echo "<script>if($('.td".$testid."').length==0){ $('#tr".$testid."').hide(); }</script>";
			}
		}
	}
?>
	
	</table>
<?php
}
if($_POST["type"]=="pending")
{
	if($sel_test=="all")
	{
		$pat_test_qry=mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `date` between '$date1' AND '$date2' ");
	}else
	{
		$pat_test_qry=mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `date` between '$date1' AND '$date2' AND `testid`='$sel_test' ");
	}
	$pat_test_num=mysqli_num_rows($pat_test_qry);
	if($pat_test_num!=0)
	{
?>	<p style="margin-top: 2%;"><b>Pending Test from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
		<a class="btn btn-info btn-mini text-right" href="pages/balance_test_pending_report_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&sel_test=<?php echo $sel_test;?>" style="margin-right: 1%;"><i class="icon-file icon-large"></i> Excel</a>
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Test Name</th>
			<!--<th>Sample</th>-->
			<!--<th>Date Time</th>-->
		</tr>
	<?php
		$n=1;
		while($pat_test=mysqli_fetch_array($pat_test_qry))
		{
			$pat_testresult_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$pat_testresult_rad_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults_rad` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$pat_testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults_card` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$test_summary_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `patient_test_summary` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$pat_widalresult_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `widalresult` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$tot_num=$pat_testresult_num+$pat_testresult_rad_num+$pat_testresult_card_num+$test_summary_num+$pat_widalresult_num;
			if($tot_num==0)
			{
				if($pat_test["opd_id"]!='')
				{
					$v_id=$pat_test["opd_id"];
				}
				if($pat_test["ipd_id"]!='')
				{
					$v_id=$pat_test["ipd_id"];
				}
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `patient_info` WHERE `patient_id`='$pat_test[patient_id]' "));
				$testname=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$pat_test[testid]' "));
				if($pat_test["sample_id"]>0)
				{
					$phlebo_num=mysqli_num_rows(mysqli_query($link, " SELECT `patient_id` FROM `phlebo_sample` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
					if($phlebo_num==0)
					{
						$sample="Not Received";
					}else
					{
						$sample="Received";
					}
				}else
				{
					$sample="---";
				}
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $v_id; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $testname["testname"]; ?></td>
					<!--<td><?php echo $sample; ?></td>-->
					<!--<td><?php echo convert_date($pat_test["date"])." ".$pat_test["time"]; ?></td>-->
				</tr>
			<?php
				$n++;
			}
		}
	?>
	</table>
<?php
	}
}
if($_POST["type"]=="conduct")
{
	if($sel_test=="all")
	{
		$pat_test_qry=mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `date` between '$date1' AND '$date2' ");
	}else
	{
		$pat_test_qry=mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `date` between '$date1' AND '$date2' AND `testid`='$sel_test' ");
	}
	$pat_test_num=mysqli_num_rows($pat_test_qry);
	if($pat_test_num!=0)
	{
?>	<p style="margin-top: 2%;"><b>Conducted Test from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>

		<a class="btn btn-info btn-mini text-right" href="pages/balance_test_conducted_report_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&sel_test=<?php echo $sel_test;?>"style="margin-right: 1%;"><i class="icon-file icon-large"></i> Excel</a>
		
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Test Name</th>
			<!--<th>Sample</th>-->
			<!--<th>Date Time</th>-->
		</tr>
	<?php
		$n=1;
		while($pat_test=mysqli_fetch_array($pat_test_qry))
		{
			$pat_testresult_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$pat_testresult_rad_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults_rad` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$pat_testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `testresults_card` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$test_summary_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `patient_test_summary` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$pat_widalresult_num=mysqli_num_rows(mysqli_query($link, " SELECT `testid` FROM `widalresult` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
			$tot_num=$pat_testresult_num+$pat_testresult_rad_num+$pat_testresult_card_num+$test_summary_num+$pat_widalresult_num;
			if($tot_num>0)
			{
				if($pat_test["opd_id"]!='')
				{
					$v_id=$pat_test["opd_id"];
				}
				if($pat_test["ipd_id"]!='')
				{
					$v_id=$pat_test["ipd_id"];
				}
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `patient_info` WHERE `patient_id`='$pat_test[patient_id]' "));
				$testname=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$pat_test[testid]' "));
				if($pat_test["sample_id"]>0)
				{
					$phlebo_num=mysqli_num_rows(mysqli_query($link, " SELECT `patient_id` FROM `phlebo_sample` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
					if($phlebo_num==0)
					{
						$sample="Not Received";
					}else
					{
						$sample="Received";
					}
				}else
				{
					$sample="---";
				}
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $v_id; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $testname["testname"]; ?></td>
					<!--<td><?php echo $sample; ?></td>-->
					<!--<td><?php echo convert_date($pat_test["date"])." ".$pat_test["time"]; ?></td>-->
				</tr>
			<?php
				$n++;
			}
		}
	?>
	</table>
<?php
	}
}
?>
