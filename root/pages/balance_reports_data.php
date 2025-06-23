<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$date1=$_POST['date1'];
$date2=$_POST['date2'];
$encounter=$_POST['encounter'];
$branch_id=$_POST['branch_id'];
$uhid=$_POST['uhid'];
$bill_no=$_POST['bill_no'];
$pat_name=$_POST['pat_name'];
$address=$_POST['address'];
$val=$_POST['val'];

$rupees_symbol="";

if($_POST["type"]=="balance_patient")
{
	$all_pin="";
	
	$encounter_pay_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type=$pat_typ_text["type"];
	}
	
	$i=1;
	if($encounter==0 || $encounter_pay_type==1)
	{
		$str=" SELECT `opd_id` FROM `consult_patient_payment_details` WHERE `balance`>0";
		
		if($date1 && $date2)
		{
			$str.=" AND `date` between '$date1' AND '$date2'";
		}
		
		if($uhid)
		{
			$str.=" AND `patient_id`='$uhid'";
		}
		
		if($bill_no)
		{
			$str.=" AND `opd_id`='$bill_no'";
		}
		
		$con_pay_qry=mysqli_query($link, $str);
		while($con_pay=mysqli_fetch_array($con_pay_qry))
		{
			if($all_pin=="")
			{
				$all_pin.="'".$con_pay["opd_id"]."'";
			}
			else
			{
				$all_pin.=",'".$con_pay["opd_id"]."'";
			}
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==2)
	{
		$str=" SELECT `opd_id` FROM `invest_patient_payment_details` WHERE `balance`>0";
		
		if($date1 && $date2)
		{
			$str.=" AND `date` between '$date1' AND '$date2'";
		}
		
		if($uhid)
		{
			$str.=" AND `patient_id`='$uhid'";
		}
		
		if($bill_no)
		{
			$str.=" AND `opd_id`='$bill_no'";
		}
		
		$inv_pay_qry=mysqli_query($link, $str);
		while($inv_pay=mysqli_fetch_array($inv_pay_qry))
		{
			if($all_pin=="")
			{
				$all_pin.="'".$inv_pay["opd_id"]."'";
			}
			else
			{
				$all_pin.=",'".$inv_pay["opd_id"]."'";
			}
			$i++;
		}
	}
	if($encounter==0 || $encounter_pay_type==3)
	{
		$str=" SELECT `ipd_id` FROM `ipd_discharge_balance_pat` WHERE `bal_amount`>0";
		
		if($date1 && $date2)
		{
			$str.=" AND `date` between '$date1' AND '$date2'";
		}
		
		if($uhid)
		{
			$str.=" AND `patient_id`='$uhid'";
		}
		
		if($bill_no)
		{
			$str.=" AND `ipd_id`='$bill_no'";
		}
		
		$ipd_pay_qry=mysqli_query($link, $str);
		while($ipd_pay=mysqli_fetch_array($ipd_pay_qry))
		{
			if($all_pin=="")
			{
				$all_pin.="'".$ipd_pay["ipd_id"]."'";
			}
			else
			{
				$all_pin.=",'".$ipd_pay["ipd_id"]."'";
			}
			$i++;
		}
	}
	
	//echo $all_pin;
	
	$str=" SELECT a.* FROM `uhid_and_opdid` a, `patient_info` b WHERE a.`patient_id`=b.`patient_id` AND `opd_id` IN($all_pin) AND a.`type`='$encounter'";
	
	if(strlen($pat_name)>1)
	{
		$str.=" AND b.`name` LIKE '%$pat_name%'";
	}
	
	if(strlen($address)>1)
	{
		$str.=" AND b.`city` LIKE '%$address%'";
	}
	
	$str.=" AND a.`branch_id`='$branch_id'";
	
	$str.=" ORDER BY a.`slno` ASC";
	
	$qry=mysqli_query($link, $str);
?>
	<p style="margin-top: 2%;">
		<b>Patient Credit Reports </b>
		<?php
			if($date1 && $date2)
			{
				echo "from: ".date("d-m-Y",strtotime($date1))." <b>to</b> ".date("d-m-Y",strtotime($date2));
			}
		 ?>
		
		<button type="button" class="btn btn-excel btn-mini text-right print_btn" onclick="export_page('balance_patient','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $branch_id;?>','<?php echo $uhid;?>','<?php echo $bill_no;?>','<?php echo $pat_name;?>','<?php echo $address;?>')" style="margin-right: 1%;"><i class="icon-file"></i> Export</button>
		
		<button type="button" class="btn btn-info btn-mini text-right print_btn" onclick="print_page('balance_patient','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $branch_id;?>','<?php echo $uhid;?>','<?php echo $bill_no;?>','<?php echo $pat_name;?>','<?php echo $address;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-condensed table-bordered table-hover">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Unit No.</th>
				<th>Bill No.</th>
				<th>Patient Name</th>
				<th>Age</th>
				<th>Sex</th>
				<th>Phone</th>
				<th>Address</th>
				<th>Father Name</th>
				<!--<th>Admission Date</th>-->
				<th>D.O.D</th>
				<th style="text-align:center;">Bill Amount</th>
				<th style="text-align:center;">Paid Amount</th>
				<th style="text-align:center;">Credit Amount</th>
				<th>Remarks</th>
			</tr>
		</thead>
	<?php
		$n=1;
		$total_balance_amount=0;
		while($pat_reg=mysqli_fetch_array($qry))
		{
			$patient_id=$pat_reg["patient_id"];
			$opd_id=$pat_reg["opd_id"];
			$reg_date=$pat_reg["date"];
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name`,`sex`,`dob`,`phone`,`city`,`father_name` FROM `patient_info` WHERE `patient_id`='$patient_id' "));
			
			if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }
			
			$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
			
			$balance_reason="";
			
			$bill_amount=0;
			if($pat_typ["type"]==1)
			{
				$pat_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount`,`dis_amt`,`advance`,`balance`,`bal_reason` FROM `consult_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
				$bill_amount=$pat_bill["tot_amount"];
				$discount_amount=$pat_bill["dis_amt"];
				$paid_amount=$pat_bill["advance"];
				$balance_amount=$pat_bill["balance"];
				
				$balance_reason=$pat_bill["bal_reason"];
			}
			if($pat_typ["type"]==2)
			{
				$pat_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount`,`dis_amt`,`advance`,`balance`,`bal_reason` FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' "));
				$bill_amount=$pat_bill["tot_amount"];
				$discount_amount=$pat_bill["dis_amt"];
				$paid_amount=$pat_bill["advance"];
				$balance_amount=$pat_bill["balance"];
				
				$balance_reason=$pat_bill["bal_reason"];
			}
			if($pat_typ["type"]==3)
			{
				$serv_sum=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_pat_service_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id' "));
				$bill_amount=$serv_sum["tot"];
				
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' and `opd_id`='$opd_id' "));
				
				$already_paid      =$check_paid["paid"];
				$already_refund    =$check_paid["refund"];
				
				$paid_amount=$already_paid-$already_refund;
				
				$balance_amount=$bill_amount-$paid_amount;
				
				//$pat_bill=mysqli_fetch_array(mysqli_query($link, " SELECT `bal_amount` FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id' "));
				//$balance_amount=$pat_bill["bal_amount"];
				
				if($balance_amount!=$pat_bill["bal_amount"])
				{
					//echo $opd_id."<br>";
				}
				
				$pat_bal_res=mysqli_fetch_array(mysqli_query($link, " SELECT `balance_reason` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Final' AND `payment_mode`='Credit' "));
				
				$balance_reason=$pat_bal_res["balance_reason"];
			}
			
			$total_balance_amount+=$balance_amount;
			
			$discharge_info=mysqli_fetch_array(mysqli_query($link, " SELECT `date` FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id' "));
			
			$discharge_date="";
			if($discharge_info)
			{
				$discharge_date=date("d-M-Y",strtotime($discharge_info["date"]));
			}
	?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $patient_id; ?></td>
				<td><?php echo $opd_id; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $age; ?></td>
				<td><?php echo $pat_info["sex"]; ?></td>
				<td><?php echo $pat_info["phone"]; ?></td>
				<td><?php echo $pat_info["city"]; ?></td>
				<td><?php echo $pat_info["father_name"]; ?></td>
				<!--<td><?php echo date("d-M-Y",strtotime($pat_reg["date"])); ?></td>-->
				<td><?php echo $discharge_date; ?></td>
				<td style="text-align:right;"><?php echo indian_currency_format($bill_amount); ?></td>
				<td style="text-align:right;"><?php echo indian_currency_format($paid_amount); ?></td>
				<td style="text-align:right;"><?php echo indian_currency_format($balance_amount); ?></td>
				<td><?php echo $balance_reason; ?></td>
			</tr>
	<?php
			$n++;
		}
	?>
		<tr>
			<th style="text-align:right;" colspan="12">Total</th>
			<th style="text-align:right;"><?php echo indian_currency_format($total_balance_amount); ?></th>
			<th></th>
		</tr>
	</table>
<?php
}
