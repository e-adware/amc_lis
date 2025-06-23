<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$time=date('H:i:s');
$date=date("Y-m-d");

$type=$_POST['type'];

$rupees_symbol="&#x20b9; ";

if($type=="view")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$head_id=$_POST['head_id'];
	
	$test_dept_str="";
	if($head_id>0)
	{
		$test_dept_str=" AND c.`type_id`='$head_id'";
	}
	
?>
	<b>Patient Test Out Sample from <?php echo date("d-M-Y", strtotime($date1)); ?> to <?php echo date("d-M-Y", strtotime($date2)); ?></b>
	
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Bill No</th>
				<th>Name</th>
				<th>Tests</th>
			</tr>
		</thead>
	<?php
		$same_date="";
		$dates = getDatesFromRangeSTD($date1,$date2);
		foreach($dates as $c_date)
		{
			$test_entry=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `patient_test_details` b, `testmaster` c WHERE b.`testid`=c.`testid` AND b.`date`='$c_date' AND c.`category_id`=1 "));
			if($c_date && $test_entry)
			{
				$q="SELECT a.* FROM `uhid_and_opdid` a, `patient_test_details` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND b.`testid`=c.`testid` AND b.`date`='$c_date' $test_dept_str AND c.`category_id`=1";
				
				$q.=" GROUP BY a.`patient_id`";
				
				//echo "<br>".$q."<br>";
				
				if($same_date!=$c_date)
				{
					$same_date=$c_date;
					$same_date_str=convert_date($same_date);
					echo "<tr><th colspan='4'>Date: $same_date_str</th></tr>";
				}
				
				$n=1;
				$pat_reg_qry=mysqli_query($link,$q);
				while($pat_reg=mysqli_fetch_array($pat_reg_qry))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
					
					//~ if($pat_reg["type"]==2)
					//~ {
						//~ $pin=$pat_reg["opd_id"];
						//~ $opd_id=$pat_reg["opd_id"];
						//~ $ipd_id="";
					//~ }else
					//~ {
						//~ $pin=$pat_reg["opd_id"];
						//~ $opd_id="";
						//~ $ipd_id=$pat_reg["opd_id"];
					//~ }
					//$opd_id=$pat_reg["opd_id"];
					
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td>
						<table class="table table-condensed">
							<tr>
								<th style="width: 5%;">#</th>
								<th style="width: 76%;">Test Name</th>
								<th>Out Sample</th>
							</tr>
					<?php
						// Test Details
						$all_test="";
						$zz=1;
						$pat_test_qry=mysqli_query($link," SELECT b.*,c.`testname` FROM `testmaster` c, `patient_test_details` b WHERE c.`testid`=b.`testid` AND b.`patient_id`='$pat_reg[patient_id]' AND (b.`opd_id`='$opd_id' OR b.`ipd_id`='$opd_id') $test_dept_str AND b.`date`='$c_date' AND c.`category_id`=1 ");
						while($pat_test=mysqli_fetch_array($pat_test_qry))
						{
							$check_out=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_test_details_out` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
							if($check_out)
							{
								$radio_yes="checked";
								$radio_no="";
							}else
							{
								$radio_yes="";
								$radio_no="checked";
							}
					?>
							<tr>
								<td><?php echo $zz; ?></td>
								<td><?php echo $pat_test["testname"]; ?></td>
								<td>
									<label><input type="radio" name="each_test<?php echo $pat_test["slno"]; ?>" id="each_test<?php echo $pat_test["slno"]; ?>" value="1" onChange="each_test_change('<?php echo $pat_test["slno"]; ?>','1')" <?php echo $radio_yes; ?>> Yes</label>
									
									<label><input type="radio" name="each_test<?php echo $pat_test["slno"]; ?>" id="each_test<?php echo $pat_test["slno"]; ?>" value="0" onChange="each_test_change('<?php echo $pat_test["slno"]; ?>','0')" <?php echo $radio_no; ?>> No</label>
								</td>
							</tr>
					<?php
							$zz++;
						}
					?>
						</table>
					</td>
				</tr>
			<?php
					$n++;
				}
			}
		}
	?>
	</table>
	<?php
}

if($type=="check_outside")
{
	$testslno=$_POST['testslno'];
	$val=$_POST['val'];
	
	$test_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `slno`='$testslno' "));
	
	if($test_det["opd_id"])
	{
		$pin=$test_det["opd_id"];
	}
	if($test_det["ipd_id"])
	{
		$pin=$test_det["ipd_id"];
	}
	
	if($val==0)
	{
		mysqli_query($link, " DELETE FROM `patient_test_details_out` WHERE `patient_id`='$test_det[patient_id]' AND `opd_id`='$pin' AND `batch_no`='$test_det[batch_no]' AND `testid`='$test_det[testid]' ");
	}
	if($val==1)
	{
		mysqli_query($link, " INSERT INTO `patient_test_details_out`(`patient_id`, `opd_id`, `batch_no`, `testid`, `date`, `time`, `user`) VALUES ('$test_det[patient_id]','$pin','$test_det[batch_no]','$test_det[testid]','$test_det[date]','$test_det[time]','$test_det[user]') ");
	}
	
}


if($type=="report")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$head_id=$_POST['head_id'];
	
	$test_dept_str="";
	if($head_id>0)
	{
		$test_dept_str=" AND c.`type_id`='$head_id'";
	}
	
?>
	<b>Patient Test Out Sample Reports from <?php echo date("d-M-Y", strtotime($date1)); ?> to <?php echo date("d-M-Y", strtotime($date2)); ?></b>
	
	<div id="print_div">
		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('report','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $head_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</div>
	
	<table class="table table-condensed table-bordered">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Bill No</th>
				<th>Name</th>
				<th>Tests</th>
				<th>Amount</th>
			</tr>
		</thead>
	<?php
		$same_date="";
		$dates = getDatesFromRangeSTD($date1,$date2);
		foreach($dates as $c_date)
		{
			$test_entry=mysqli_fetch_array(mysqli_query($link, " SELECT a.`slno` FROM `patient_test_details_out` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND a.`date`='$c_date' "));
			if($c_date && $test_entry)
			{
				$q="SELECT DISTINCT a.`patient_id`, a.`opd_id` FROM `patient_test_details_out` a, `patient_test_details` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`testid`=c.`testid` AND a.`date`='$c_date' $test_dept_str AND c.`category_id`=1";
				
				$q.=" ORDER BY a.`slno`";
				
				//echo "<br>".$q."<br>";
				
				if($same_date!=$c_date)
				{
					$same_date=$c_date;
					$same_date_str=date("d-M-Y", strtotime($same_date));
					echo "<tr><th colspan='5'>Date: $same_date_str</th></tr>";
				}
				
				$n=1;
				$pat_reg_qry=mysqli_query($link,$q);
				while($pat_reg=mysqli_fetch_array($pat_reg_qry))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
					
					//~ if($pat_reg["type"]==2)
					//~ {
						//~ $pin=$pat_reg["opd_id"];
						//~ $opd_id=$pat_reg["opd_id"];
						//~ $ipd_id="";
					//~ }else
					//~ {
						//~ $pin=$pat_reg["opd_id"];
						//~ $opd_id="";
						//~ $ipd_id=$pat_reg["opd_id"];
					//~ }
					$opd_id=$pat_reg["opd_id"];
			?>
				<tr>
					<td style="vertical-align: middle;"><?php echo $n; ?></td>
					<td style="vertical-align: middle;"><?php echo $pat_reg["opd_id"]; ?></td>
					<td style="vertical-align: middle;"><?php echo $pat_info["name"]; ?></td>
					<td>
						<table class="table table-condensed">
							<tr>
								<th style="width: 5%;">#</th>
								<th style="width: 56%;">Test Name</th>
								<th>Amount</th>
								<!--<th>Date Time</th>-->
								<th>User</th>
							</tr>
					<?php
						// Test Details
						$all_test="";
						$zz=1;
						$pat_test_qry=mysqli_query($link," SELECT b.*,c.`testname` FROM `testmaster` c, `patient_test_details_out` b WHERE c.`testid`=b.`testid` AND b.`patient_id`='$pat_reg[patient_id]' AND b.`opd_id`='$pat_reg[opd_id]' $test_dept_str AND b.`date`='$c_date' ORDER BY b.`slno` ");
						
						$total_test_rate=0;
						while($pat_test=mysqli_fetch_array($pat_test_qry))
						{
							$test_rate=mysqli_fetch_array(mysqli_query($link, " SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$pat_test[patient_id]' AND (`opd_id`='$pat_test[opd_id]' OR `ipd_id`='$pat_test[opd_id]') AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
							
							$total_test_rate+=$test_rate["test_rate"];
							
							$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_test[user]' "));
					?>
							<tr>
								<td><?php echo $zz; ?></td>
								<td><?php echo $pat_test["testname"]; ?></td>
								<td><?php echo $test_rate["test_rate"]; ?></td>
								<!--<td><?php echo date("d-M-Y", strtotime($pat_test["date"])); ?> <?php echo date("h:i A", strtotime($pat_test["time"])); ?></td>-->
								<td><?php echo $user_info["name"]; ?></td>
							</tr>
					<?php
							$zz++;
						}
					?>
						</table>
					</td>
					<td style="vertical-align: middle;"><?php echo number_format($total_test_rate,2); ?></td>
				</tr>
			<?php
					$n++;
				}
			}
		}
	?>
	</table>
	<?php
}
?>
