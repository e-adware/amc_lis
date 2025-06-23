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

if($_POST["type"]=="opd_account")
{
	$con_cod_id=$_POST['con_cod_id'];
	$dept_id=$_POST['dept_id'];
	$patient_type=$_POST['patient_type'];
	$visit_type=$_POST['visit_type'];
	
	if($patient_type=='0')
	{
		$patient_type_str="";
	}else
	{
		$patient_type_str=" and `opd_id` in ( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `center_no`='$patient_type' ) ";
	}
	
	if($con_cod_id=='0')
	{
		$qry=" SELECT * FROM `appointment_book` WHERE `date` between '$date1' and '$date2' $patient_type_str ";
	}else
	{
		$qry=" SELECT * FROM `appointment_book` WHERE `date` between '$date1' and '$date2' and `consultantdoctorid`='$con_cod_id' $patient_type_str ";
	}
	$dept_str="";
	//~ if($dept_id>0)
	//~ {
		//~ $dept_str=" AND `consultantdoctorid` in (SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `dept_id`='$dept_id')";
	//~ }
	//~ $qry.=$dept_str;
	
	//~ if($visit_type=='1')
	//~ {
		//~ $visit_type_str=" and `opd_id` in ( SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `regd_fee`>0 )";
	//~ }
	//~ if($visit_type=='2')
	//~ {
		//~ $visit_type_str=" and `opd_id` in ( SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `regd_fee`<150 )";
	//~ }
	$qry.=$visit_type_str;
	
	//echo $qry;
	$pat_reg_qry=mysqli_query($link, $qry );
	
	$pat_reg_num=mysqli_num_rows($pat_reg_qry);
	if($pat_reg_num!=0)
	{
?>	<p style="margin-top: 2%;"><b>OPD Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/opd_account_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&con_doc=<?php echo $con_cod_id;?>&dept_id=<?php echo $dept_id;?>&visit_type=<?php echo $visit_type;?>">Export to Excel</a></span>
		
		<button type="button" class="btn btn-info text-right" onclick="print_page('opd_account','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $con_cod_id;?>','<?php echo $dept_id;?>','<?php echo $visit_type;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<!--<th>UHID</th>-->
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Consultant Doctor</th>
			<th>Department</th>
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
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
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
			<td><?php echo $con_doc["Name"]; ?></td>
			<td><?php echo $dept_name["name"]; ?></td>
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
		<button type="button" class="btn btn-info text-right" onclick="print_page('opd_cancel_report','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $con_cod_id;?>','<?php echo $dept_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
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
if($_POST["type"]=="lab_account")
{
	$pat_reg_qry=mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `date` between '$date1' and '$date2' order by `slno` DESC ");
	$pat_reg_num=mysqli_num_rows($pat_reg_qry);
	if($pat_reg_num!=0)
	{
?>	<p style="margin-top: 2%;"><b>All Departments<br>Lab Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/lab_account_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>">Export to Excel</a></span>
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Total Amount</th>
			<th>Discount</th>
			<th>Paid Amount</th>
			<th>Balance</th>
			<th>Date</th>
			<th>User</th>
		</tr>
	<?php
		$n=1;
		$tot="";
		$dis="";
		$paid="";
		$bal="";
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_info["uhid"]; ?></td>
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_reg["tot_amount"],2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_reg["dis_amt"],2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_reg["advance"],2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($pat_reg["balance"],2); ?></td>
			<td><?php echo convert_date($pat_reg["date"]); ?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
		<?php
				$tot=$tot+$pat_reg["tot_amount"];
				$dis=$dis+$pat_reg["dis_amt"];
				$paid=$paid+$pat_reg["advance"];
				$bal=$bal+$pat_reg["balance"];
				$n++;
			}
		?>
		<tr>
			<!--<td></td><td></td><td></td>-->
			<th colspan="4"><span class="text-right">Total</span></th>
			<td><?php echo "&#x20b9; ".number_format($tot,2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($dis,2); ?></td>
			<td><?php echo "&#x20b9; ".number_format($paid,2); ?></td>
			<td colspan="3"><?php echo "&#x20b9; ".number_format($bal,2); ?></td>
		</tr>
	</table>
<?php
	}
}
if($_POST["type"]=="dept_wise_report")
{
	$val=$_POST["val"];
	if($val==1)
	{
		$pat_reg_qry=mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `opd_id` in ( SELECT `opd_id` FROM `patient_test_details` WHERE `testid` in ( SELECT `testid` FROM `testmaster` WHERE `category_id`='1' ) ) and `date` between '$date1' and '$date2' order by `slno` DESC ");
		$dept="Pathology";
	}
	if($val==2)
	{
		$pat_reg_qry=mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `opd_id` in ( SELECT `opd_id` FROM `patient_test_details` WHERE `testid` in ( SELECT `testid` FROM `testmaster` WHERE `category_id`='2' ) ) and `date` between '$date1' and '$date2' order by `slno` DESC ");
		$dept="Radiology";
	}
	if($val==3)
	{
		$pat_reg_qry=mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `opd_id` in ( SELECT `opd_id` FROM `patient_test_details` WHERE `testid` in ( SELECT `testid` FROM `testmaster` WHERE `category_id`='3' ) ) and `date` between '$date1' and '$date2' order by `slno` DESC ");
		$dept="Cardiology";
	}
	
	$pat_reg_num=mysqli_num_rows($pat_reg_qry);
	if($pat_reg_num!=0)
	{
?>	<p style="margin-top: 2%;"><b><?php echo $dept; ?></b><br><b>Lab Account from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/lab_account_export_to_excel_wise.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&val=<?php echo $val; ?>">Export to Excel</a></span>
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Total Amount</th>
			<!--<th>Discount</th>
			<th>Paid Amount</th>
			<th>Balance</th>-->
			<th>Date</th>
			<th>User</th>
		</tr>
	<?php
		$n=1;
		$tot="";
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
			
			$pat_rate_qry=mysqli_query($link, " SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' and `testid` in (SELECT `testid` FROM `testmaster` WHERE `category_id`='$val') ");
			$qqq="";
			while($pat_rate=mysqli_fetch_array($pat_rate_qry))
			{
				$qqq=$qqq+$pat_rate["test_rate"];
			}
			$vaccu_charge_qry=mysqli_query($link, " SELECT `rate` FROM `patient_vaccu_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' ");
			$vaccu_charge_num=mysqli_num_rows($vaccu_charge_qry);
			if($vaccu_charge_num>0)
			{
				$tot_vaccu="";
				while($vaccu_charge=mysqli_fetch_array($vaccu_charge_qry))
				{
					$tot_vaccu=$tot_vaccu+$vaccu_charge["rate"];
				}
			}
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_info["uhid"]; ?></td>
			<td><?php echo $pat_reg["opd_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo "&#x20b9; ".number_format(($qqq+$tot_vaccu),2); ?></td>
			<td><?php echo convert_date($pat_reg["date"]); ?></td>
			<td><?php echo $user_name["name"]; ?></td>
		</tr>
		<?php
				$tot=$tot+$qqq;
				$n++;
			}
		?>
		<tr>
			<!--<td></td><td></td><td></td>-->
			<th colspan="4"><span class="text-right">Total</span></th>
			<td><?php echo "&#x20b9; ".number_format($tot,2); ?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
<?php
	}
}
if($_POST["type"]=="bal_received")
{
	$pay_detail_qry=mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `typeofpayment`='B' and `date` between '$date1' and '$date2' ");
	$pay_detail_num=mysqli_num_rows($pay_detail_qry);
	if($pay_detail_num!=0)
	{
?>	<p style="margin-top: 2%;"><b>Balance received from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Paid Amount</th>
			<th>Date</th>
			<th>User</th>
		</tr>
	<?php
		$n=1;
		$amount="";
		while($pay_detail=mysqli_fetch_array($pay_detail_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$pay_detail[patient_id]' "));
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pay_detail[user]' "));
	?>
	<tr>
		<td><?php echo $n; ?></td>
		<td><?php echo $pat_info["uhid"]; ?></td>
		<td><?php echo $pay_detail["opd_id"]; ?></td>
		<td><?php echo $pat_info["name"]; ?></td>
		<td><?php echo "&#x20b9; ".number_format($pay_detail["amount"],2); ?></td>
		<td><?php echo convert_date($pay_detail["date"]); ?></td>
		<td><?php echo $user_name["name"]; ?></td>
	</tr>
	<?php
			$amount=$amount+$pay_detail["amount"];
			$n++;
		}
	?>
	<tr>
		<th colspan="4"><span class="text-right">Total</span></th>
		<td colspan="3"><?php echo "&#x20b9; ".number_format($amount,2); ?></td>
	</tr>
	</table>
<?php
	}
}
if($_POST["type"]=="user_summary")
{
?>
	<p style="margin-top: 2%;"><b>Userwise Summary Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/user_wise_summary_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>">Export to Excel</a></span>
	</p>
	<table class="table table-bordered table-condensed">						
		<tr>
			<th>#</th>
			<th>User Name</th>
			<th><span class="text-right">Amount</span></th>
		</tr>
		<?php
			$vttl=0;
			$i=1;	
			$qrtest=mysqli_query($link, "SELECT distinct(a.user),b.name  FROM invest_patient_payment_details a,employee b WHERE a.user=b.emp_id and a.date between '$date1' and '$date2' order by a.slno ");
			while($qrtest1=mysqli_fetch_array($qrtest))
			{
				$qamt=mysqli_fetch_array(mysqli_query($link, "select ifnull(sum(amount),0) as maxamt from invest_payment_detail where date between'$date1' and '$date2' and user='$qrtest1[user]' "));	
				$vttl=$vttl+$qamt['maxamt'];
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $qrtest1['name'];?></td>
					<td><span class="text-right"><?php echo "&#x20b9; ".number_format($qamt['maxamt'],2);?></span></td>
				</tr>
			<?php
				$i++;
			}
			?>
				<tr>
					<td colspan="2" class="">Total Cash Collection</td>							
					<td><span class="text-right"><?php echo "&#x20b9; ".number_format($vttl,2);?></span></td>
				</tr>
				<!--<tr>
					<td colspan="3">Extra Receipt</td>
				</tr>-->
		<?php
			$vttlextra=0;
			$i=1;
			$qrexp=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as maxexp from expense_detail where date between '$date1' and '$date2'"));	
			  $qrtest=mysqli_query($link, "SELECT distinct userid FROM center_extra_receipt WHERE date between'$date1' and '$date2'  order by userid ");
				
			while($qrtest1=mysqli_fetch_array($qrtest)){
			//$qamt=mysqli_fetch_array(mysqli_query($link, "select ifnull(sum(amount),0) as maxextraamt from extra_receipt where date1 between'$date1' and '$date2' and user='$qrtest1[user]' "));	
			$qamt=mysqli_fetch_array(mysqli_query($link, "select ifnull(sum(amount),0) as maxextraamt from center_extra_receipt where date between'$date1' and '$date2' and userid='$qrtest1[userid]' "));	
			$vttlextra=$vttlextra+$qamt['maxextraamt'];
			?>
		<tr>
			<td><?php echo $i;?></td>
			<td>
				<?php 
					
					$uname=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID='$qrtest1[userid]'"));
					echo $uname[Name];
				?>
			</td>
			<td><span class="text-right"><?php echo "&#x20b9; ".number_format($qamt['maxextraamt'],2);?></span></td>
		</tr>
		<?php
			$i++;}
			?>
		<!--<tr>
			<td colspan="2" class="" >Total Extra Receipt Collection</td>
			<td><span class="text-right"><?php echo $vttlextra.'.00';?></span></td>
		</tr>-->
		<?php
			$vtlcash=$vttlextra+$vttl-$qrexp['maxexp'];
			?>
		<tr>
			<td colspan="2" class="">Expense</td>
			<td><span class="text-right"><?php echo "&#x20b9; ".number_format($qrexp['maxexp'],2);?></span></td>
		</tr>
		<tr class="">
			<td colspan="2"><strong>Net Collection</strong></td>
			<td><span class="text-right"><strong> <?php echo "&#x20b9; ".number_format($vtlcash,2);?></strong></span></td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="detail_report")
{
?>
	<p style="margin-top: 2%;"><b>Details Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
		<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/detail_report_export_to_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>">Export to Excel</a></span>
	</p>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Date </th>
			<th>UHID / Bill No</th>
			<th>Patient Name</th>
			<th>Ref Doctor</th>
			<th>Center Name</th>
			<th>Test Rate</th>
			<th>Amount</th>
			<th>Discount</th>
			<th>Paid</th>
			<th>Balance</th>
			<th>User</th>
		</tr>
	<?php
		$tamt=0;
		$dscnt=0;
		$advnc=0;
		$blnc=0;
		$i=1;
		$qrtest=mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' and'$date2' and `type`=2 order by `date` ");
		while($qrtest1=mysqli_fetch_array($qrtest))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$qrtest1[patient_id]'"));
			$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
			$center_name=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_info[center_no]' "));
			$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$qrtest1[patient_id]' and `opd_id`='$qrtest1[opd_id]'  "));
			
			if ($pat_pay_detail['dis_amt']>0)
			{
				$vdscnt=$pat_pay_detail['dis_amt'];
			}
			else
			{
				$vdscnt='';
			}
			
			$tamt=$tamt+$pat_pay_detail['tot_amount'];
			$dscnt=$dscnt+$vdscnt;
			$advnc=$advnc+$pat_pay_detail['advance'];
			$blnc=$blnc+$pat_pay_detail['balance'];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$qrtest1[user]' "));
			
			if($dummyDate!=$qrtest1['date'])
			{
				echo '<tr><td align="left" colspan="11"><b>'.convert_date($qrtest1['date']).'</b></td></tr>';
				$dummyDate=$qrtest1['date'];
				
			}
		?>
			<tr>
				<th><?php echo $i;?></th>
				<th><?php echo $pat_info["uhid"]."/".$qrtest1["opd_id"];?></th>
				<th><?php echo $pat_info["name"]."/".$pat_info["age"]." ".$pat_info["age_type"]."/".$pat_info["sex"];?></th>
				<th><?php echo $ref_doc["ref_name"];?></th>
				<th><?php echo $center_name["centrename"];?></th>
				<th><?php echo "";?></th>
				<th><?php echo number_format($pat_pay_detail['tot_amount'],2);?></th>
				<th><?php echo number_format($pat_pay_detail['dis_amt'],2);?></th>
				<th><?php echo number_format($pat_pay_detail['advance'],2);?></th>
				<th><?php echo number_format($pat_pay_detail['balance'],2);?></th>
				<th><?php echo $quser["name"];?></th>
			</tr>
		<?php
			$qtest=mysqli_query($link,"select a.testid,a.test_rate,b.testname from patient_test_details a,testmaster b where a.patient_id='$qrtest1[patient_id]' and a.opd_id='$qrtest1[opd_id]' and a.testid=b.testid  order by b.testname");
			while($qtest1=mysqli_fetch_array($qtest))
			{
			?>
				<tr>
					<td colspan="2"></td>
					<td colspan="3"><i><?php echo $qtest1['testname'];?></i></td>
					<td align="right"><i><?php echo number_format($qtest1['test_rate'],2);?></i></td>
					<td colspan="6">&nbsp;</td>
				</tr>
			<?php
			}
			$vaccu_charge_qry=mysqli_query($link, " SELECT `rate` FROM `patient_vaccu_details` WHERE `patient_id`='$qrtest1[patient_id]' and `opd_id`='$qrtest1[opd_id]' ");
			$vaccu_charge_num=mysqli_num_rows($vaccu_charge_qry);
			if($vaccu_charge_num>0)
			{
				$tot_vaccu="";
				while($vaccu_charge=mysqli_fetch_array($vaccu_charge_qry))
				{
					$tot_vaccu=$tot_vaccu+$vaccu_charge["rate"];
				}
			}
			?>
				<tr>
					<td colspan="2"></td>
					<td colspan="3"><i>Vaccu Charge</i></td>
					<td align="right"><i><?php echo number_format($tot_vaccu,2);?></i></td>
					<td colspan="6">&nbsp;</td>
				</tr>
			<?php
			$i++;
		}
	?>
	</table>
<?php
}
if($_POST["type"]=="cheque_payment")
{
?>
	<p style="margin-top: 2%;"><b>Cheque Payment Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th><span class="text-right">Amount</span></th>
			<th><span class="text-right">Balance</span></th>
		</tr>
	<?php
		$tot=0;
		$data=mysqli_query($link, "select * from invest_payment_detail where payment_mode='Cheque' and date between '$date1' and '$date2'");
		$n=1;
		while($d=mysqli_fetch_array($data))
		{
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
			
			$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
			$tamt=$tamt+$d['amount'];
			$dscnt=$dscnt+$pay['dis_amt'];
			$advnc=$advnc+$d['advance'];
			$balnce=$balnce+$pay['balance'];
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
			?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $pat_info['uhid'];?></td>
			<td><?php echo $d['opd_id'];?></td>
			<td><?php echo $pat_info["name"]."/".$pat_info["age"]." ".$pat_info["age_type"]."/".$pat_info["sex"];?></td>
			<td><span class="text-right"><?php echo number_format($d['amount'],2);?></span></td>
			<td><span class="text-right"><?php echo number_format($pay['balance'],2);?></span></td>
			<td><?php echo $user_name['name'];?></td>
		</tr>
	<?php
		$n++;
		}
		?>
		<tr>
			<td colspan="4"><span class="text-right"><strong>Total</strong></span></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($tamt,2);?></strong></span></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($balnce,2);?></strong></span></td>
			<td></td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="card_payment")
{
?>
	<p style="margin-top: 2%;"><b>Card Payment Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th><span class="text-right">Amount</span></th>
			<!--<th><span class="text-right">Discount</span></th>
			<th><span class="text-right">Paid</span></th>-->
			<th><span class="text-right">Balance</span></th>
			<th>User</th>
		</tr>
		<?php
			$tot=0;
			$data=mysqli_query($link, "select * from invest_payment_detail where payment_mode='Card' and date between '$date1' and '$date2'");
			while($d=mysqli_fetch_array($data))
			{
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
			
			$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
			$tamt=$tamt+$d['amount'];
			$dscnt=$dscnt+$pay['dis_amt'];
			$advnc=$advnc+$d['advance'];
			$balnce=$balnce+$pay['balance'];
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
			?>
		<tr>
			<td><?php echo $pat_info['uhid'];?></td>
			<td><?php echo $d['opd_id'];?></td>
			<td><?php echo $pat_info["name"]."/".$pat_info["age"]." ".$pat_info["age_type"]."/".$pat_info["sex"];?></td>
			<td><span class="text-right"><?php echo number_format($d['amount'],2);?></span></td>
			<!--<td><span class="text-right"><?php echo number_format($pay['dis_amt'],2);?></span></td>
			<td><span class="text-right"><?php echo number_format($d['advance'],2);?></span></td>-->
			<td><span class="text-right"><?php echo number_format($pay['balance'],2);?></span></td>
			<td><?php echo $user_name['name'];?></td>
		</tr>
		<?php	
			}
			?>
		<tr>
			<td colspan="3"><span class="text-right"><strong>Total</strong></span></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($tamt,2);?></strong></span></td>
			<!--<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($dscnt,2);?></strong></span></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($advnc,2);?></strong></span></td>-->
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($balnce,2);?></strong></span></td>
			<td></td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="credit")
{
?>
	<p style="margin-top: 2%;"><b>Credit Patient Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th><span class="text-right">Credit Amount</span></th>
			<th>User</th>
		</tr>
		<?php
			$tot=0;
			$data=mysqli_query($link, "select * from invest_payment_detail where payment_mode='Credit' and date between '$date1' and '$date2'");
			while($d=mysqli_fetch_array($data))
			{
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
			
			$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
			$tamt=$tamt+$pay['tot_amount'];
			
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
			
			?>
		<tr>
			<td><?php echo $pat_info['uhid'];?></td>
			<td><?php echo $d['opd_id'];?></td>
			<td><?php echo $pat_info["name"]."/".$pat_info["age"]." ".$pat_info["age_type"]."/".$pat_info["sex"];?></td>
			<td><span class="text-right"><?php echo number_format($pay['tot_amount'],2);?></span></td>
			<td><?php echo $user_name['name'];?></td>
		</tr>
		<?php	
			}
			?>
		<tr>
			<td colspan="3"><span class="text-right"><strong>Total</strong></span></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($tamt,2);?></strong></span></td>
			<td></td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="cancel_report")
{
?>
	<p style="margin-top: 2%;"><b>Cancel Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>UHDI / Bill No</th>
			<th> Name</th>
			<th>Cancel date</th>
			<th><span class="text-right">Bill Amount</span></th>
			<th><span class="text-right">Reason</span></th>
			<th><span class="text-right">User</span></th>
		</tr>
		<?php
				$i=1;
				$cashamt=0;
				$patientdel=mysqli_query($link, "SELECT * FROM `patient_cancel_reason` WHERE `type`='2' and `date` between '$date1' and'$date2' order by `date`  ");
				
				while($d=mysqli_fetch_array($patientdel))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
					
					$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details_cancel where patient_id='$d[patient_id]' and opd_id='$d[opd_id]'"));
					$cashamt=$cashamt+$pay['tot_amount'];
					$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $d['patient_id']." / ".$d['opd_id'];?></td>
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo convert_date($d['date'],2);?></td>
					<td><span class="text-right"><?php echo number_format($pay['tot_amount'],2);?></span></td>
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
if($_POST["type"]=="discount_report")
{
	$filename ="balance_".$date1."_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
?>
	<p style="margin-top: 2%;"><b>Discount Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Name</th>
			<th>Ref Doctor</th>
			<!--<th>Center</th>-->
			<th><span class="text-right">Amount</span></th>
			<th><span class="text-right">Discount</span></th>
			<th><span class="text-right">Net Paid</span></th>
			<th><span class="text-right">Balance</span></th>
			<th>Discount Reason</th>
			<th>User</th>
		</tr>
		<?php
			$i=1;
			$qry=mysqli_query($link, "select * from invest_patient_payment_details where dis_amt>0 and date between '$date1' and '$date2'");
						 
			while($q=mysqli_fetch_array($qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$q[patient_id]'"));
				
				$vttlamt=$vttlamt+$q['tot_amount'];
				$vttldis=$vttldis+$q['dis_amt'];
				$vttlblnc=$vttlblnc+$q['balance'];
				$vttladvnce=$vttladvnce+$q['advance'];
				
				$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
				
				$center_name=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_info[center_no]' "));
				
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$q[user]' "));
				
				?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['uhid'];?></td>
			<td><?php echo $q['opd_id'];?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $ref_doc["ref_name"];?></td>
			<!--<td><?php echo $center_name['centrename'];?></td>-->
			<td><span class="text-right"><?php echo number_format($q['tot_amount'],2);?></span></td>
			<td><span class="text-right"><?php echo number_format($q['dis_amt'],2);?></span></td>
			<td><span class="text-right"><?php echo number_format($q['advance'],2);?></span></td>
			<td><span class="text-right"><?php echo number_format($q['balance'],2);?></span></td>
			<td><?php echo $q['dis_reason'];?></td>
			<td><?php echo $user_name['name'];?></td>
		</tr>
		<?php
			$i++;	
			}
					?>
		<tr>
			<td colspan="5"><span class="text-right"><strong>Total </strong></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($vttlamt,2);?></strong></span></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($vttldis,2);?></strong></span></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($vttladvnce,2);?></strong></span></td>
			<td><span class="text-right"><strong><?php echo "&#x20b9; ".number_format($vttlblnc,2);?></strong></span></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	
<?php
}
if($_POST["type"]=="daily_expense")
{
?>
	<p style="margin-top: 2%;"><b>Daily Expense from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Description</th>
			<th>User</th>
			<th><span class="text-right">Amount</span></th>
		</tr>
	<?php
		$qdate=mysqli_query($link, " SELECT distinct `date` FROM `expense_detail` WHERE `date` between '$date1' and'$date2' order by `slno` ");
		while($qdate1=mysqli_fetch_array($qdate))
		{
			$c_d=convert_date($qdate1['date']);
			echo "<tr><th colspan='4'>$c_d</th></tr>";
			$qrslct=mysqli_query($link, "select * from expense_detail where date='$qdate1[date]' order by `slno` ");
			$n=1;
			while($qrslct1=mysqli_fetch_array($qrslct))
			{
				$vcash=$vcash+$qrslct1['amount'];
				$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$qrslct1[user]' "));
			?>
			<tr>
				<td><?php echo $n;?></td>
				<td><?php echo $qrslct1['details'];?></td>
				<td><?php echo $user_name['name'];?></td>
				<td><span class="text-right">&#x20b9; <?php echo $qrslct1['amount'];?>.00</span></td>
			</tr>
			<?php
				$n++;
			}
		}
	?>
	<tr>
		<td colspan="3"><strong class="text-right">Total</strong></td>
		<td><strong class="text-right">&#x20b9; <?php echo number_format($vcash,2);?></strong></td>
	</tr>
	</table>
<?php
}
if($_POST["type"]=="ipd_advance")
{
?>
	<p style="margin-top: 2%;"><b>IPD Advance Payment Details from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Name</th>
			<th>Amount</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
	<?php
		$qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `pay_type`='Advance' AND `date` between '$date1' and '$date2' order by `slno` DESC ");
		$n=1;
		$tot_advance=0;
		$advance=0;
		while($adv_pat=mysqli_fetch_array($qry))
		{
			$advance=$adv_pat["amount"];
			$tot_advance+=$advance;
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_pat[patient_id]'"));
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_pat[user]' "));
			
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $pat_info["uhid"]; ?></td>
				<td><?php echo $adv_pat["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo "&#x20b9; ".number_format($advance,2); ?></td>
				<td><?php echo convert_date($adv_pat["date"])." ".$adv_pat["time"]; ?></td>
				<td><?php echo $user_name["name"]; ?></td>
			</tr>
			<?php
				$n++;
		}
	?>
		<tr>
			<td colspan="4"><b class="text-right">Total</b></td>
			<td colspan="3"><?php echo "&#x20b9; ".number_format($tot_advance,2); ?></td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="ipd_final_bill")
{
?>
	<p style="margin-top: 2%;"><b>IPD Final Payment Details from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Name</th>
			<th>Amount</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
	<?php
		$qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` between '$date1' and '$date2' order by `slno` DESC ");
		$n=1;
		$tot_advance=0;
		$advance=0;
		while($adv_pat=mysqli_fetch_array($qry))
		{
			$advance=$adv_pat["amount"];
			$tot_advance+=$advance;
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_pat[patient_id]'"));
			$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_pat[user]' "));
			
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $pat_info["uhid"]; ?></td>
				<td><?php echo $adv_pat["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo "&#x20b9; ".number_format($advance,2); ?></td>
				<td><?php echo convert_date($adv_pat["date"])." ".$adv_pat["time"]; ?></td>
				<td><?php echo $user_name["name"]; ?></td>
			</tr>
			<?php
				$n++;
		}
	?>
		<tr>
			<td colspan="4"><b class="text-right">Total</b></td>
			<td colspan="3"><?php echo "&#x20b9; ".number_format($tot_advance,2); ?></td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="head_wise_detail")
{
	//SELECT a.*, b.`opd_id`, b.`ipd_id`, c.`type_id` FROM `uhid_and_opdid` a, `patient_test_details` b, `testmaster` c WHERE 1
	
	$head_id=$_POST["head_id"];
	$head=mysqli_fetch_array(mysqli_query($link, " select type_name from testmaster where type_id='$head_id' "));
?>
	<p style="margin-top: 2%;"><b>Head Wise Test Details from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
	<br>
	<b>Name: <?php echo $head["type_name"]; ?></b>
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>Test ID</th>
			<th>Test Name</th>
			<th>No. of Test</th>
			<th>Total Amount</th>
		</tr>
	<?php
		$n=1;
		$qrslct=mysqli_query($link, "select distinct (a.testid),b.rate,b.testname from  patient_test_details a,testmaster b  where b.type_id='$head_id' and a.testid=b.testid and a.date between'$date1'and'$date2'  order by b.testname ");
		while($qrslct1=mysqli_fetch_array($qrslct))
		{
			$vtst=0;
			$vamt=0;
			$qrnmtst=mysqli_num_rows(mysqli_query($link, "select testid  from patient_test_details where testid='$qrslct1[testid]' and date between '$date1' and '$date2' ")); 
			$vamt=$qrnmtst*$qrslct1['rate'];
			$vtot_amt=$vtot_amt+$vamt;
			$vttltst=$vttltst+$qrnmtst;
		?>
			<tr>
				<td><?php echo $n;?></td>
				<td><?php echo $qrslct1['testid'];?></td>
				<td><?php echo $qrslct1['testname'];?></td>
				<td><?php echo $qrnmtst;?></td>
				<td><?php echo "&#x20b9; ".number_format($vamt,2);?></td>
			</tr>
		<?php
			$n++;
		}
	?>
		<tr>
			<th colspan="3"><span class="text-right">Grand Total: </span></th>
			<td><?php echo $vttltst; ?></td>
			<td><?php echo "&#x20b9; ".number_format($vtot_amt,2); ?></td>
		</tr>
	</table>
<?php
}

if($_POST["type"]=="cat_test_detail")
{
	$val=$_POST["cat_test"];
	
	if($val==1)
	{
		$dept="Pathology";
	}
	if($val==2)
	{
		$dept="Radiology";
	}
	if($val==3)
	{
		$dept="Cardiology";
	}
	
	$pat_reg_qry=mysqli_query($link, " SELECT distinct `patient_id`,`opd_id`,`ipd_id`,`date` FROM `patient_test_details` WHERE `date` between '$date1' and '$date2' and `testid`in ( SELECT `testid` FROM `testmaster` WHERE `category_id`='$val' ) order by `slno` ASC ");
	
	$pat_reg_num=mysqli_num_rows($pat_reg_qry);
	if($pat_reg_num!=0)
	{
?>
	<p style="margin-top: 2%;"><b>Test Details of <?php echo $dept; ?> from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
		
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>Date</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Amount</th>
		</tr>
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
			$pat_test_amount=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`test_rate`) as tot_amt FROM `patient_test_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' and `ipd_id`='$pat_reg[ipd_id]' "));
			$test_tot_amt=$pat_test_amount['tot_amt'];
	?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_reg["date"]; ?></td>
			<td><?php echo $pat_reg["patient_id"]; ?></td>
			<td><?php echo $pin; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $test_tot_amt; ?>.00</td>
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

if($_POST["type"]=="head_wise_detail_pat")
{
	$encounter=$_POST["encounter"];
	$head_id=$_POST["head_id"];
	$head=mysqli_fetch_array(mysqli_query($link, " select type_name from testmaster where type_id='$head_id' "));
	
	$encounter_str="";
	if($encounter>0)
	{
		$encounter_str=" WHERE `type`='$encounter'";
	}
	
?>
	<p style="margin-top: 2%;"><b>Head Wise Test Details from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	<br>
	<b>Name: <?php echo $head["type_name"]; ?></b>
	<button type="button" class="btn btn-info text-right" onclick="print_page('head_wise_detail_pat','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $head_id;?>','<?php echo $encounter;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Patient Name</th>
			<th>Test Name</th>
			<th>Total Amount</th>
			<th>Encounter</th>
		</tr>
	<?php
		$n=1;
		$grand_tot=0;
		$qry=mysqli_query($link, " SELECT distinct(`ipd_id`) FROM `patient_test_details` WHERE `testid` in ( SELECT `testid` FROM `testmaster` WHERE `type_id`='$head_id' )  AND `date` BETWEEN '$date1' AND '$date2' AND `ipd_id` in ( SELECT `opd_id` FROM `uhid_and_opdid` $encounter_str ) ");
		while($dis_ipd=mysqli_fetch_array($qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id` in( SELECT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id`='$dis_ipd[ipd_id]' ) "));
			
			$uhid_opd_id=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$pat_info[patient_id]' AND `opd_id`='$dis_ipd[ipd_id]' "));
			
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$uhid_opd_id[type]' "));
			$Encounter=$pat_typ_text['p_type'];
			
			$all_test="";
			$tot_test=0;
			$test_qry=mysqli_query($link, " SELECT a.`test_rate`, b.`testname` FROM `patient_test_details` a, `testmaster` b WHERE a.`patient_id`='$pat_info[patient_id]' AND a.`ipd_id`='$dis_ipd[ipd_id]' AND b.`type_id`='$head_id' AND a.`testid`=b.`testid` ");
			while($test=mysqli_fetch_array($test_qry))
			{
				$all_test=$test["testname"]."<br>";
				$tot_test+=$test["test_rate"];
			}
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $pat_info['patient_id']; ?></td>
				<td><?php echo $dis_ipd['ipd_id']; ?></td>
				<td><?php echo $pat_info['name']; ?></td>
				<td><?php echo $all_test; ?></td>
				<td><?php echo number_format($tot_test,2); ?></td>
				<td><?php echo $Encounter; ?></td>
			</tr>
		<?php
			$n++;
			$grand_tot+=$tot_test;
		}
	?>
		<tr>
			<th colspan="5"><span class="text-right">Grand Total: </span></th>
			<td><?php echo number_format($grand_tot,2); ?></td>
			<td></td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="qwqwqwqwqwq")
{
?>
	<p style="margin-top: 2%;"><b>Details from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?></p>
	
<?php
}
?>
