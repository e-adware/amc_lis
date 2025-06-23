<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$branch_id=$_POST['branch_id'];
if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

$branch_str=" AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
$branch_str_a=" AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
$branch_str_b=" AND b.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";

$date1=$_POST['date1'];
$date2=$_POST['date2'];

if($_POST["type"]=="load_users")
{
	$not_accountant = array();
	array_push($not_accountant, 5, 6, 11, 12, 13, 20, 21);
	$not_accountant = join(',',$not_accountant);
	
	echo "<option value='0'>Select User</option>";
	
	$qry=mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` WHERE `emp_id`>0 AND `levelid` NOT IN ($not_accountant) AND `branch_id`='$branch_id' ORDER BY `name` ASC ");
	while($data=mysqli_fetch_array($qry))
	{
		if($c_user==$data["emp_id"]){ $sel_this="selected"; }else{ $sel_this=""; }
		
		echo "<option value='$data[emp_id]' $sel_this>$data[name]</option>";
	}
}

if($_POST["type"]=="opd_account")
{
	$con_cod_id=$_POST['con_cod_id'];
	$payment_mode=$_POST['payment_mode'];
	$dept_id=$_POST['dept_id'];
	$patient_type=$_POST['patient_type'];
	$visit_type=$_POST['visit_type'];
	$user_entry=$_POST['user_entry'];
	
	$qry="SELECT a.* FROM `appointment_book` a, `uhid_and_opdid` b, `payment_detail_all` c WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND c.`patient_id`=b.`patient_id` AND c.`opd_id`=b.`opd_id` AND b.`type`=1 AND b.`branch_id`='$branch_id' AND c.`payment_type`='Advance' AND a.`date` between '$date1' and '$date2'";
	
	if($con_cod_id>0)
	{
		$qry.=" AND a.`consultantdoctorid`='$con_cod_id'";
	}
	
	$user_str="";
	if($user_entry>0)
	{
		$qry.=" AND b.`user`='$user_entry'";
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$user_name=$user_info["name"];
	}
	else
	{
		$user_name="All";
	}
	
	$payment_mode_str_a="";
	if($payment_mode!="")
	{
		$payment_mode_str_a=" AND a.`payment_mode`='$payment_mode'";
		
		$qry.=" AND c.`payment_mode`='$payment_mode'";
	}
	
	$qry.=" GROUP BY a.`opd_id`";
	
	//echo $qry;
	
	//~ $all_pay_mode=array();
	//~ $p_mode_qry=mysqli_query($link, "SELECT DISTINCT a.`payment_mode` FROM `payment_detail_all` a, `payment_mode_master` b WHERE a.`payment_mode`=b.`p_mode_name` AND b.`operation`=1 AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='1' AND `branch_id`='$branch_id') $payment_mode_str_a ORDER BY b.`sequence` ASC");
	//~ $p_mode_num=mysqli_num_rows($p_mode_qry);
	//~ while($p_mode=mysqli_fetch_array($p_mode_qry))
	//~ {
		//~ $all_pay_mode[]=$p_mode["payment_mode"];
	//~ }
	
	//~ $all_pay_modes=implode(",",$all_pay_mode);
	
	$pat_reg_qry=mysqli_query($link, $qry );
	
	$pat_reg_num=mysqli_num_rows($pat_reg_qry);
	if($pat_reg_num!=0)
	{
?>	<p style="margin-top: 2%;" id="print_div">
		<b>OPD Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide">
			<a class="btn btn-info btn-mini" href="pages/opd_account_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&con_cod_id=<?php echo $con_cod_id;?>&dept_id=<?php echo $dept_id;?>&visit_type=<?php echo $visit_type;?>&patient_type=<?php echo $patient_type;?>&user_entry=<?php echo $user_entry;?>&branch_id=<?php echo $branch_id;?>&payment_mode=<?php echo $payment_mode;?>"><i class="icon-file icon-large"></i> Excel</a>
		</span>
		<button type="button" class="btn btn-info btn-mini text-right" id="print_btn" onclick="print_page('opd_account','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $con_cod_id;?>','<?php echo $dept_id;?>','<?php echo $visit_type;?>','<?php echo $patient_type;?>','<?php echo $user_entry;?>','<?php echo $branch_id;?>','<?php echo $payment_mode;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<!--<th>UHID</th>-->
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Phone No</th>
			<th>Consultant Doctor</th>
			<!--<th>Department</th>-->
			<th>Consultant Fee</th>
			<th>Registration Fee</th>
			<!--<th>Emergency Fee</th>-->
			<!--<th>Cross Consultation Fee</th>-->
			<th>Discount</th>
			<th>Date</th>
			<th>User</th>
		</tr>
	<?php
		$n=1;
		$tot_con="";
		$tot_reg="";
		$tot_emr="";
		$tot_dis="";
		$tot_cross="";
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name`,`phone` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
			$con_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_reg[consultantdoctorid]' "));
			
			$dept_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$con_doc[dept_id]' "));
			
			$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
			//if($pat_reg["emergency"]>0)
			//{
				$emrgncy_fee=$pat_pay_detail["emergency_fee"];
			//~ }else
			//~ {
				//~ $emrgncy_fee=0;
			//~ }
			//~ $cross_consult=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cross_consultation` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' "));
			//~ if($cross_consult["amount"]>0)
			//~ {
				//~ $cross_consult_fee=$cross_consult["amount"];
			//~ }else
			//~ {
				//~ $cross_consult_fee=0;
			//~ }
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_pay_detail[user]' "));
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<!--<td><?php echo $pat_info["patient_id"]; ?></td>-->
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $pat_info["phone"]; ?></td>
			<td><?php echo $con_doc["Name"]; ?></td>
			<!--<td><?php echo $dept_name["name"]; ?></td>-->
			<td><?php echo "&#x20b9; ".number_format($pat_pay_detail["visit_fee"],2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_pay_detail["regd_fee"],2); ?></td>
			<!--<td><?php echo "&#x20b9; ".number_format($emrgncy_fee,2); ?></td>-->
			<!--<td><?php echo "&#x20b9; ".number_format($cross_consult_fee,2); ?></td>-->
			<td><?php echo "&#x20b9; ".number_format($pat_pay_detail["dis_amt"],2); ?></td>
			<td><?php echo convert_date($pat_reg["date"]); ?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
		<?php
				$tot_con=$tot_con+$pat_pay_detail["visit_fee"];
				$tot_reg=$tot_reg+$pat_pay_detail["regd_fee"];
				$tot_dis=$tot_dis+$pat_pay_detail["dis_amt"];
				$tot_emr=$tot_emr+$emrgncy_fee;
				//$tot_cross=$tot_cross+$cross_consult_fee;
				$n++;
			}
		?>
		<tr>
			<th colspan="5"><span class="text-right">Total</span></th>
			<td><?php echo "&#x20b9; ".number_format($tot_con,2); ?></td>
			<td colspan=""><?php echo "&#x20b9; ".number_format($tot_reg,2); ?></td>
			<!--<td colspan=""><?php echo "&#x20b9; ".number_format($tot_emr,2); ?></td>-->
			<!--<td colspan=""><?php echo "&#x20b9; ".number_format($tot_emr,2); ?></td>-->
			<td colspan="3"><?php echo "&#x20b9; ".number_format($tot_dis,2); ?></td>
		</tr>
		<tr>
			<th colspan="5"><span class="text-right">Grand total</span></th>
			<td colspan="7"><?php echo "&#x20b9; ".number_format(($tot_con+$tot_reg+$tot_emr+$tot_cross-$tot_dis),2); ?></td>
		</tr>
	</table>
<?php
	}
}
if($_POST["type"]=="opd_cancel_report")
{
?>
	<p style="margin-top: 2%;"><b>OPD Cancel Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<button type="button" class="btn btn-info text-right" onclick="print_page('opd_cancel_report','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $con_cod_id;?>','<?php echo $dept_id;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>UHID / Bill No</th>
			<th> Name</th>
			<th>Cancel date</th>
			<th><span class="text-right">Bill Amount</span></th>
			<th><span class="text-right">Reason</span></th>
			<th><span class="text-right">User</span></th>
		</tr>
		<?php
				$i=1;
				$cashamt=0;
				$patientdel=mysqli_query($link, "SELECT * FROM `patient_cancel_reason` WHERE `type`='1' and `date` between '$date1' and'$date2' and `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `patient_id`>0 ) order by `date`  ");
				
				while($d=mysqli_fetch_array($patientdel))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
					
					$pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details_cancel where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
					$cashamt=$cashamt+$pay['tot_amount'];
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $pat_info['uhid']." / ".$d['opd_id'];?></td>
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo convert_date($d['date']);?></td>
					<td><span class="text-right"><?php echo "&#x20b9; ".number_format($pay['tot_amount'],2);?></span></td>
					<td><span class="text-right"><?php echo $d['reason'];?></span></td>
					<td><span class="text-right"><?php echo $quser['name'];?></span></td>
				</tr>
		<?php
					$i++;
				}
			?>
		<tr>
		  <td colspan="4"><span class="text-right"><strong>Total Cancel Amount :</strong></span></td>
		  <td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($cashamt,2);?> </strong></span></td>
		  <td colspan="2">&nbsp;</td>
		</tr>
	</table>
<?php
}
?>
