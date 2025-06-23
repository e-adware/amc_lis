<?php
include("../../includes/connection.php");

$date1=$_POST['date1'];
$date2=$_POST['date2'];

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="opd_edit_report")
{
	$user_entry=$_POST['user_entry'];
	$user="";
	if($user_entry>0)
	{
		$user=" AND `user`='$user_entry'";
	}
	$counter_qry=mysqli_query($link, " SELECT * FROM `edit_counter` WHERE `date` between '$date1' AND '$date2' AND `type`='1' $user ORDER BY `opd_id`,`counter`");
	$counter_num=mysqli_num_rows($counter_qry);
	if($counter_num>0)
	{
?>
	<p style="margin-top: 2%;"><b>OPD Edit Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>Doctor</th>
			<th>Appointment Date</th>
			<th>Bill Amount</th>
			<th>Discount</th>
			<th>Advance</th>
			<th>Balance</th>
			<th>User</th>
			<th>Date Time</th>
		</tr>
		<?php
		$i=1;
		$same_pin="";
		while($r=mysqli_fetch_array($counter_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$r[patient_id]'"));
			$appointment=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book_edit` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
			
			$doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment[consultantdoctorid]' "));
			
			$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details_edit` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
			
			$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
			if($same_pin!=$r['opd_id'])
			{
				if($i!=1)
				{
					//echo "<tr><th colspan='11'>Now $same_pin</th></tr>";
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$same_uhid'"));
					$appointment=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$same_uhid' AND `opd_id`='$same_pin' "));
			
					$doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment[consultantdoctorid]' "));
					
					$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$same_uhid' AND `opd_id`='$same_pin' "));
					
					$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$same_user' "));
				?>
					<tr style="background-color: lightyellow;">
						<td><?php echo $i;?></td>
						<td><?php echo $pat_info["patient_id"]; ?></td>
						<td><?php echo $same_pin; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $doc["Name"]; ?></td>
						<td><?php echo convert_date($appointment["appointment_date"]); ?></td>
						<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["tot_amount"],2); ?></td>
						<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["dis_amt"],2); ?></td>
						<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["advance"],2); ?></td>
						<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["balance"],2); ?></td>
						<td><?php echo $emp["name"]; ?></td>
						<td><?php echo convert_date($same_date); ?> <?php echo convert_time($same_time); ?></td>
					</tr>
				<?php
					$i++;
				}
				$same_pin=$r['opd_id'];
				$same_uhid=$r['patient_id'];
				$same_user=$r['user'];
				$same_date=$r['date'];
				$same_time=$r['time'];
				//echo "<tr><th colspan='11'>Previous $r[opd_id]</th></tr>";
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$r[patient_id]'"));
				$appointment=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book_edit` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
				
				$doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment[consultantdoctorid]' "));
				
				$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details_edit` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
				
				$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
			}
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $r["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $doc["Name"]; ?></td>
			<td><?php echo convert_date($appointment["appointment_date"]); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["tot_amount"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["dis_amt"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["advance"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["balance"],2); ?></td>
			<td><?php echo $emp["name"]; ?></td>
			<td><?php echo convert_date($r["date"]); ?> <?php echo convert_time($r["time"]); ?></td>
		</tr>
		<?php
			$i++;
		}
		//echo "<tr><th colspan='11'>Now $same_pin</th></tr>";
		$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$same_uhid'"));
		$appointment=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$same_uhid' AND `opd_id`='$same_pin' "));
			
		$doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment[consultantdoctorid]' "));
		
		$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$same_uhid' AND `opd_id`='$same_pin' "));
		
		$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$same_user' "));
		?>
		<tr style="background-color: lightyellow;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $same_pin; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $doc["Name"]; ?></td>
			<td><?php echo convert_date($appointment["appointment_date"]); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["tot_amount"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["dis_amt"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["advance"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["balance"],2); ?></td>
			<td><?php echo $emp["name"]; ?></td>
			<td><?php echo convert_date($same_date); ?> <?php echo convert_time($same_time); ?></td>
		</tr>
	</table>
<?php
	}
}
if($_POST["type"]=="lab_edit_report")
{
	$user_entry=$_POST['user_entry'];
	$user="";
	if($user_entry>0)
	{
		$user=" AND `user`='$user_entry'";
	}
	$counter_qry=mysqli_query($link, " SELECT * FROM `edit_counter` WHERE `date` between '$date1' AND '$date2' AND `type`='2' $user ORDER BY `opd_id`,`counter`");
	$counter_num=mysqli_num_rows($counter_qry);
	if($counter_num>0)
	{
?>
	<p style="margin-top: 2%;"><b>Lab Edit Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>No. of test</th>
			<th>Bill Amount</th>
			<th>Discount</th>
			<th>Advance</th>
			<th>Balance</th>
			<th>User</th>
			<th>Date Time</th>
		</tr>
		<?php
		$i=1;
		$same_pin="";
		while($r=mysqli_fetch_array($counter_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$r[patient_id]'"));
			
			$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details_edit` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
			
			$test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details_edit` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
			
			$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
			if($same_pin!=$r['opd_id'])
			{
				if($i!=1)
				{
					//echo "<tr><th colspan='11'>Now $same_pin</th></tr>";
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$same_uhid'"));
					
					$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$same_uhid' AND `opd_id`='$same_pin' "));
					
					$test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$same_uhid' AND `opd_id`='$same_pin' "));
					
					$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$same_user' "));
				?>
					<tr style="background-color: lightyellow;">
						<td><?php echo $i;?></td>
						<td><?php echo $pat_info["patient_id"]; ?></td>
						<td><?php echo $same_pin; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $test_num; ?></td>
						<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["tot_amount"],2); ?></td>
						<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["dis_amt"],2); ?></td>
						<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["advance"],2); ?></td>
						<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["balance"],2); ?></td>
						<td><?php echo $emp["name"]; ?></td>
						<td><?php echo convert_date($same_date); ?> <?php echo convert_time($same_time); ?></td>
					</tr>
				<?php
					$i++;
				}
				$same_pin=$r['opd_id'];
				$same_uhid=$r['patient_id'];
				$same_user=$r['user'];
				$same_date=$r['date'];
				$same_time=$r['time'];
				//echo "<tr><th colspan='11'>Previous $r[opd_id]</th></tr>";
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$r[patient_id]'"));
				
				$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details_edit` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
				
				$test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details_edit` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
				
				$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
			}
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $r["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $test_num; ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["tot_amount"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["dis_amt"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["advance"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["balance"],2); ?></td>
			<td><?php echo $emp["name"]; ?></td>
			<td><?php echo convert_date($r["date"]); ?> <?php echo convert_time($r["time"]); ?></td>
		</tr>
		<?php
			$i++;
		}
		//echo "<tr><th colspan='11'>Now $same_pin</th></tr>";
		$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$same_uhid'"));
		
		$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$same_uhid' AND `opd_id`='$same_pin' "));
		
		$test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$same_uhid' AND `opd_id`='$same_pin' "));
		
		$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$same_user' "));
		?>
		<tr style="background-color: lightyellow;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $same_pin; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $test_num; ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["tot_amount"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["dis_amt"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["advance"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($con_pat_pay_detail["balance"],2); ?></td>
			<td><?php echo $emp["name"]; ?></td>
			<td><?php echo convert_date($same_date); ?> <?php echo convert_time($same_time); ?></td>
		</tr>
	</table>
<?php
	}
}

if($_POST["type"]=="bed_edit_report")
{
	$user_entry=$_POST['user_entry'];
	$user="";
	if($user_entry>0)
	{
		$user=" AND `user`='$user_entry'";
	}
	$counter_qry=mysqli_query($link, " SELECT * FROM `edit_counter` WHERE `date` between '$date1' AND '$date2' AND `type`='3' $user ORDER BY `opd_id`,`counter`");
	$counter_num=mysqli_num_rows($counter_qry);
	if($counter_num>0)
	{
	?>
	<p style="margin-top: 2%;"><b>Bed Edit Reports from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>Bed Details</th>
			<th>Bed Charge</th>
			<th>Bed Other Charge</th>
			<th>User</th>
			<th>Date Time</th>
		</tr>
	<?php
		$i=1;
		$same_pin="";
		while($r=mysqli_fetch_array($counter_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$r[patient_id]'"));
			
			$bed_id=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_id` FROM `ipd_pat_bed_details_edit` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
			$bed_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `bed_master` WHERE `bed_id`='$bed_id[bed_id]' "));
			
			$ward_detail=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$bed_detail[ward_id]' "));
			
			$other_bed_tot=0;
			$bed_other_qry=mysqli_query($link, " SELECT `charge_id` FROM `bed_other_charge` WHERE `bed_id`='$bed_id[bed_id]' ");
			while($bed_other=mysqli_fetch_array($bed_other_qry))
			{
				$other_charge=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `charge_master` WHERE `charge_id`='$bed_other[charge_id]' "));
				$other_bed_tot+=$other_charge["amount"];
			}
			
			$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
			if($same_pin!=$r['opd_id'])
			{
				if($i!=1)
				{
					//echo "<tr><th colspan='11'>Now $same_pin</th></tr>";
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$same_uhid'"));
					
					//$bed_id=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_id` FROM `ipd_pat_bed_details_edit` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
					$bed_id=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$same_uhid' AND `ipd_id`='$same_pin' ORDER BY `slno` DESC "));
					$bed_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `bed_master` WHERE `bed_id`='$bed_id[bed_id]' "));
					
					$ward_detail=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$bed_detail[ward_id]' "));
					
					$other_bed_tot=0;
					$bed_other_qry=mysqli_query($link, " SELECT `charge_id` FROM `bed_other_charge` WHERE `bed_id`='$bed_id[bed_id]' ");
					while($bed_other=mysqli_fetch_array($bed_other_qry))
					{
						$other_charge=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `charge_master` WHERE `charge_id`='$bed_other[charge_id]' "));
						$other_bed_tot+=$other_charge["amount"];
					}
					
					$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$same_user' "));
				?>
					<tr style="background-color: lightyellow;">
						<td><?php echo $i;?></td>
						<td><?php echo $pat_info["patient_id"]; ?></td>
						<td><?php echo $same_pin; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $ward_detail["name"]." ".$bed_detail["bed_no"]; ?></td>
						<td><?php echo $rupees_symbol.number_format($bed_detail["charges"],2); ?></td>
						<td><?php echo $rupees_symbol.number_format($other_bed_tot,2); ?></td>
						<td><?php echo $emp["name"]; ?></td>
						<td><?php echo convert_date($same_date); ?> <?php echo convert_time($same_time); ?></td>
					</tr>
				<?php
					$i++;
				}
				$same_pin=$r['opd_id'];
				$same_uhid=$r['patient_id'];
				$same_user=$r['user'];
				$same_date=$r['date'];
				$same_time=$r['time'];
				//echo "<tr><th colspan='11'>Previous $r[opd_id]</th></tr>";
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$r[patient_id]'"));
				
				$bed_id=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_id` FROM `ipd_pat_bed_details_edit` WHERE `patient_id`='$r[patient_id]' AND `ipd_id`='$r[opd_id]' AND `counter`='$r[counter]' "));
				$bed_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `bed_master` WHERE `bed_id`='$bed_id[bed_id]' "));
				
				$ward_detail=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$bed_detail[ward_id]' "));
				
				$other_bed_tot=0;
				$bed_other_qry=mysqli_query($link, " SELECT `charge_id` FROM `bed_other_charge` WHERE `bed_id`='$bed_id[bed_id]' ");
				while($bed_other=mysqli_fetch_array($bed_other_qry))
				{
					$other_charge=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `charge_master` WHERE `charge_id`='$bed_other[charge_id]' "));
					$other_bed_tot+=$other_charge["amount"];
				}

				
				$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
			}
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $r["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $ward_detail["name"]." ".$bed_detail["bed_no"]; ?></td>
			<td><?php echo $rupees_symbol.number_format($bed_detail["charges"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($other_bed_tot,2); ?></td>
			<td><?php echo $emp["name"]; ?></td>
			<td><?php echo convert_date($r["date"]); ?> <?php echo convert_time($r["time"]); ?></td>
		</tr>
		<?php
			$i++;
		}
		//echo "<tr><th colspan='11'>Now $same_pin</th></tr>";
		$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$same_uhid'"));
		
		//$bed_id=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$same_uhid' AND `ipd_id`='$same_pin' "));
		$bed_id=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$same_uhid' AND `ipd_id`='$same_pin' ORDER BY `slno` DESC "));
		$bed_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `bed_master` WHERE `bed_id`='$bed_id[bed_id]' "));
		
		$ward_detail=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$bed_detail[ward_id]' "));
		
		$other_bed_tot=0;
		$bed_other_qry=mysqli_query($link, " SELECT `charge_id` FROM `bed_other_charge` WHERE `bed_id`='$bed_id[bed_id]' ");
		while($bed_other=mysqli_fetch_array($bed_other_qry))
		{
			$other_charge=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `charge_master` WHERE `charge_id`='$bed_other[charge_id]' "));
			$other_bed_tot+=$other_charge["amount"];
		}

		
		$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$same_user' "));
		?>
		<tr style="background-color: lightyellow;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $same_pin; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $ward_detail["name"]." ".$bed_detail["bed_no"]; ?></td>
			<td><?php echo $rupees_symbol.number_format($bed_detail["charges"],2); ?></td>
			<td><?php echo $rupees_symbol.number_format($other_bed_tot,2); ?></td>
			<td><?php echo $emp["name"]; ?></td>
			<td><?php echo convert_date($same_date); ?> <?php echo convert_time($same_time); ?></td>
		</tr>
	</table>
	<?php
	}
}

if($_POST["type"]=="ipd_service_delete")
{
	$user_entry=$_POST['user_entry'];
	$user="";
	if($user_entry>0)
	{
		$user=" AND `user`='$user_entry'";
	}
	$ipd_serv_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_service_delete` WHERE `date` between '$date1' AND '$date2' $user ORDER BY `slno` DESC ");
	$ipd_serv_num=mysqli_num_rows($ipd_serv_qry);
	if($ipd_serv_num>0)
	{
	?>
	<p style="margin-top: 2%;"><b>IPD Service Delete from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Service Name</th>
			<th>Amount</th>
			<th>User</th>
			<th>Date Time</th>
		</tr>
	<?php
		$i=1;
		while($ipd_serv=mysqli_fetch_array($ipd_serv_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$ipd_serv[patient_id]'"));
			
			$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_serv[user]' "));
	?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $pat_info["patient_id"]; ?></td>
				<td><?php echo $ipd_serv["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $ipd_serv["service_text"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($ipd_serv["amount"],2); ?></td>
				<td><?php echo $emp["name"]; ?></td>
				<td><?php echo convert_date($ipd_serv["date"]); ?> <?php echo convert_time($ipd_serv["time"]); ?></td>
			</tr>
	<?php
		}
		?>
	</table>
	<?php
	}
}
