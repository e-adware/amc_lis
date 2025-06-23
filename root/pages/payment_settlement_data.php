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

$branch_id=$_POST['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

if($_POST["type"]=="load_users")
{
	echo "<option value='0'>Select User</option>";
	
	$qry=mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` WHERE `emp_id`>0 AND `levelid`='8' AND `branch_id`='$branch_id' ORDER BY `name` ASC ");
	while($data=mysqli_fetch_array($qry))
	{
		echo "<option value='$data[emp_id]'>$data[name]</option>";
	}
}

if($_POST["type"]=="load_patients")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$user_entry=$_POST['user_entry'];
	$val=$_POST['val'];
	
	$qry=" SELECT * FROM `uhid_and_opdid` WHERE `slno`>0 AND `branch_id`='$branch_id' AND `user`='$user_entry' ";
	
	if($date1 && $date2)
	{
		$qry.=" AND `date` BETWEEN '$date1' AND '$date2' ";
	}
	
	if($val==1)
	{
		$qry.=" AND `opd_id` NOT IN(SELECT DISTINCT a.`opd_id` FROM `user_pay_settlement_details` a, `user_pay_settlement_master` b WHERE a.`pay_id`=b.`pay_id` AND b.`date` BETWEEN '$date1' AND '$date2') ";
	}
	
	if($val==2)
	{
		$qry.=" AND `opd_id` IN(SELECT DISTINCT a.`opd_id` FROM `user_pay_settlement_details` a, `user_pay_settlement_master` b WHERE a.`pay_id`=b.`pay_id` AND b.`date` BETWEEN '$date1' AND '$date2') ";
	}
	
	//echo $qry;
	$pat_reg_qry=mysqli_query($link, $qry );
	
?>	<p style="margin-top: 2%;" id="print_div">
		<b>Patient from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<!--<span class="text-right" id="excel_btn_hide">
			<a class="btn btn-info btn-mini" href="pages/opd_account_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&user_entry=<?php echo $user_entry;?>&branch_id=<?php echo $branch_id;?>"><i class="icon-file icon-large"></i> Excel</a>
		</span>
		<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('opd_account','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $user_entry;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>-->
	</p>
	<table class="table table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<!--<th>UHID</th>-->
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Bill Amount</th>
				<th>Discount Amount</th>
				<th>Net Amount</th>
				<th>Date</th>
				<th>User</th>
			</tr>
		</thead>
	<?php
		$n=1;
		$total_net_amount=0;
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name`,`phone` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
			$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
			
			$bill_amount=$pat_pay_detail["tot_amount"];
			$discount_amount=$pat_pay_detail["dis_amt"];
			$net_amount=$bill_amount-$discount_amount;
			
			$total_net_amount+=$net_amount;
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
	?>
		<tr>
			<td>
			<?php
				echo $n;
				
				$check_payment=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `user_pay_settlement_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
				if($check_payment)
				{
					echo '<i class="icon-ok"></i>';
				}
				else if($val==1)
				{
			?>
				<input type="checkbox" class="each_pat" id="each_pat<?php echo $n; ?>" value="<?php echo $n; ?>" onchange="each_pat_change('<?php echo $n; ?>')">
			<?php
				}
			?>
				<input type="hidden" class="opd_id" id="opd_id<?php echo $n; ?>" value="<?php echo $pat_reg["opd_id"]; ?>">
			</td>
			<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo number_format($bill_amount,2); ?></td>
			<td><?php echo number_format($discount_amount,2); ?></td>
			<td>
				<?php echo number_format($net_amount,2); ?>
				<input type="hidden" class="each_amount" id="each_amount<?php echo $n; ?>" value="<?php echo $net_amount; ?>">
			</td>
			<td><?php echo convert_date($pat_reg["date"]); ?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
		<?php
				$n++;
			}
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total</span></th>
			<td><?php echo number_format($total_net_amount,2); ?></td>
			<td colspan="2"></td>
		</tr>
<?php
	if($val==1 && $n>1)
	{
?>
		<tr class="footer_tr" id="amount_to_pay_tr" style="display:;">
			<td colspan="8" style="text-align:center;">
				<b>Amount to pay : <span id="amount_to_pay">0.00</span></b>
			</td>
		</tr>
		<tr class="footer_tr">
			<td colspan="8" style="text-align:center;">
				<button class="btn btn-new" id="select_btn" onclick="select_all(1)"><i class="icon-edit"></i> Select All</button>
				<button class="btn btn-new" id="de_select_btn" onclick="select_all(2)" style="display:none;"><i class="icon-edit"></i> De-Select All</button>
				
				<button class="btn btn-save" id="save_btn" onclick="save_payment()" style="display:none;"><i class="icon-save"></i> Save Payment</button>
			</td>
		</tr>
<?php
	}
?>
	</table>
<?php
}

if($_POST["type"]=="save_payment")
{
	//print_r($_POST);
	
	$all_pat=$_POST['all_pat'];
	$user=$_POST['user'];
	
	$payment_date=$date;
	
	if(mysqli_query($link, "INSERT INTO `user_pay_settlement_master`(`amount`, `payment_date`, `date`, `time`, `user`) VALUES ('0','$payment_date','$date','$time','$user')"))
	{
		$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `pay_id` FROM `user_pay_settlement_master` WHERE `date`='$date' AND `time`='$time' AND `user`='$user' "));
		$pay_id=$last_row["pay_id"];
		
		$total_amount=0;
		
		$all_pats=explode("@@", $all_pat);
		foreach($all_pats as $each_pat)
		{
			if($each_pat)
			{
				$each_pats=explode("##", $each_pat);
				
				$opd_id=$each_pats[0];
				$amount=$each_pats[1];
				
				$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_id' "));
				if($pat_reg)
				{
					mysqli_query($link, " INSERT INTO `user_pay_settlement_details`(`pay_id`, `patient_id`, `opd_id`, `amount`) VALUES ('$pay_id','$pat_reg[patient_id]','$opd_id','$amount') ");
					
					$total_amount+=$amount;
				}
			}
		}
		
		$check_pay_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `user_pay_settlement_details` WHERE `pay_id`='$pay_id' "));
		if($check_pay_num>0)
		{
			mysqli_query($link, " UPDATE `user_pay_settlement_master` SET `amount`='$total_amount' WHERE `pay_id`='$pay_id' ");
			
			echo "Saved";
		}
		else
		{
			mysqli_query($link, " DELETE FROM `user_pay_settlement_master` WHERE `pay_id`='$pay_id' ");
			
			echo "Failed, try again later..";
		}
	}
	else
	{
		echo "Failed, try again later.";
	}
}
?>
