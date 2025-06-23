<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$time=date('H:i:s');
$date=date("Y-m-d");

$type=$_POST['type'];

$rupee_symbol="₹ ";

if(!$test_dept_exclude){ $test_dept_exclude=0; }

if($type=="dept_wise")
{
	//print_r($_POST);
	
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$refbydoctorid_search=$refbydoctorid=$_POST['refbydoctorid'];
	$encounter=$_POST['encounter'];
	
	$encounter_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_type=$pat_typ_text["type"];
	}
?>
	<b>Department Wise report from <?php echo date("d-M-Y",strtotime($date1)); ?> to <?php echo date("d-M-Y",strtotime($date1)); ?></b>
	
	<button class="btn btn-excel btn-mini text-right print_div" onclick="export_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>')"><i class="icon-file"></i> Excel</button>
	
	<button type="button" id="print_btn" class="btn btn-info btn-mini text-right print_div" onclick="print_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>')"><i class="icon-print icon-large"></i> Print</button>
<?php
	if($encounter==0 || $encounter_type==2)
	{
		$doc_str="SELECT b.`refbydoctorid`, b.`ref_name` FROM `uhid_and_opdid` a, `refbydoctor_master` b, `dal_com_setup` c WHERE a.`refbydoctorid`=b.`refbydoctorid` AND a.`refbydoctorid`=c.`refbydoctorid`";
		
		if($encounter==0)
		{
			$doc_str.=" AND a.`type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
		}
		else
		{
			$doc_str.=" AND a.`type`='$encounter'";
		}
		
		if($date1 && $date2)
		{
			$doc_str.=" AND a.`date` BETWEEN '$date1' AND '$date2'";
		}
		
		if($refbydoctorid)
		{
			//~ $doc_str.=" AND a.`refbydoctorid`='$refbydoctorid'";
			$doc_str.=" AND a.`refbydoctorid` IN($refbydoctorid)";
		}
		
		$doc_str.=" GROUP BY b.`refbydoctorid` ORDER BY b.`ref_name` ASC";
?>
		<table class="table table-condensed table-bordered table-hover">
			<thead class="table_header_fix">
				<tr>
					<th rowspan="2">#</th>
					<th rowspan="2">Doctor Name</th>
					<th colspan="3" style="text-align:center;">Pathology</th>
<?php
			$d=0;
			$dept_qry=mysqli_query($link, "SELECT a.`id`,a.`category_id`,a.`name` FROM `test_department` a, `testmaster` b, `patient_test_details` c WHERE a.`category_id`=b.`category_id` AND a.`id`=b.`type_id` AND b.`testid`=c.`testid` AND b.`category_id`!='1' GROUP BY a.`id`"); // AND c.`date` BETWEEN '$date1' AND '$date2'
			while($dept_info=mysqli_fetch_array($dept_qry))
			{
			?>
					<th colspan="3" style="text-align:center;"><?php echo $dept_info["name"]; ?></th>
			<?php
				$d++;
			}
?>
					<th rowspan="2" style="text-align:center;">Total Comm Amount</th>
				</tr>
				<tr>
			<?php
				for($i=0;$i<=$d;$i++)
				{
			?>
					<th style="text-align:center;">Test No.</th>
					<th style="text-align:right;">Test Amount</th>
					<th style="text-align:right;">Comm Amount</th>
			<?php
				}
			?>
				</tr>
			</thead>
<?php
		$all_doc_comm_amount=0;
		$n=1;
		$doc_qry=mysqli_query($link, $doc_str);
		while($doc_data=mysqli_fetch_array($doc_qry))
		{
			$refbydoctorid=$doc_data["refbydoctorid"];
			$each_doc_comm_amount=0;
			
			$pat_test_qry=mysqli_query($link, "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`testid`,a.`test_rate`,a.`test_discount`,b.`type_id`,b.`category_id`,c.`type`,c.`center_no`,c.`doc_discount` FROM `patient_test_details` a, `testmaster` b, `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND b.`category_id`='1' AND b.`type_id` NOT IN($test_dept_exclude) AND c.`refbydoctorid`='$refbydoctorid' AND c.`date` BETWEEN '$date1' AND '$date2' ");
			$pat_test_num=mysqli_num_rows($pat_test_qry);
			
			$each_test_num=$each_test_amount=$each_test_comm_amount=0;
			while($pat_test=mysqli_fetch_array($pat_test_qry))
			{
				$comm_amount_test=0;
				
				$patient_id =$pat_test["patient_id"];
				$opd_id     =$pat_test["opd_id"];
				$testid     =$pat_test["testid"];
				$test_amount=$pat_test["test_rate"];
				$doc_discount=$pat_test["doc_discount"];
				
				$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT `tot_amount`,`dis_amt` FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
				
				$bill_amount=$pat_pay_det["tot_amount"];
				$discount_amount=$pat_pay_det["dis_amt"];
				
				if($doc_discount==0)
				{
					$discount_amount=0;
				}
				
				$discount_per=round(($discount_amount/$bill_amount)*100,2);
				
				$test_disacount=round(($test_amount*$discount_per)/100);
				//$test_disacount=0;
				$test_amount_after_discount=$test_amount-$test_disacount;
				
				$pat_encounter=$pat_test["type"];
				$centreno=$pat_test["center_no"];
				
				$comm_amount_test=calculate_commission_test_wise("$refbydoctorid","$testid","$test_amount_after_discount","$pat_encounter","$centreno");
				
				if($comm_amount_test>0)
				{
					$each_test_num++;
					$each_test_amount+=$test_amount_after_discount;
					$each_test_comm_amount+=$comm_amount_test;
					$each_doc_comm_amount+=$comm_amount_test;
					$all_doc_comm_amount+=$comm_amount_test;
				}
				
			}
?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $doc_data["ref_name"]; ?></td>
				<td style="text-align:right;"><?php echo $each_test_num; ?></td>
				<td style="text-align:right;"><?php echo number_format($each_test_amount,2); ?></td>
				<td style="text-align:right;"><?php echo number_format($each_test_comm_amount,2); ?></td>
<?php
		$dept_qry=mysqli_query($link, "SELECT a.`id`,a.`category_id`,a.`name` FROM `test_department` a, `testmaster` b, `patient_test_details` c WHERE a.`category_id`=b.`category_id` AND a.`id`=b.`type_id` AND b.`testid`=c.`testid` AND b.`category_id`!='1' GROUP BY a.`id`"); // AND c.`date` BETWEEN '$date1' AND '$date2'
		while($dept_info=mysqli_fetch_array($dept_qry))
		{
			$pat_test_qry=mysqli_query($link, "SELECT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no`,a.`testid`,a.`test_rate`,a.`test_discount`,b.`type_id`,b.`category_id`,c.`type`,c.`center_no`,c.`doc_discount` FROM `patient_test_details` a, `testmaster` b, `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND b.`category_id`!='1' AND b.`type_id`='$dept_info[id]' AND c.`refbydoctorid`='$refbydoctorid' AND c.`date` BETWEEN '$date1' AND '$date2' ");
			$pat_test_num=mysqli_num_rows($pat_test_qry);
			
			$each_test_num=$each_test_amount=$each_test_comm_amount=0;
			while($pat_test=mysqli_fetch_array($pat_test_qry))
			{
				$comm_amount_test=0;
				
				$patient_id =$pat_test["patient_id"];
				$opd_id     =$pat_test["opd_id"];
				$testid     =$pat_test["testid"];
				$test_amount=$pat_test["test_rate"];
				$doc_discount=$pat_test["doc_discount"];
				
				$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT `tot_amount`,`dis_amt` FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
				
				$bill_amount=$pat_pay_det["tot_amount"];
				$discount_amount=$pat_pay_det["dis_amt"];
				
				if($doc_discount==0)
				{
					$discount_amount=0;
				}
				
				$discount_per=round(($discount_amount/$bill_amount)*100,2);
				
				$test_disacount=round(($test_amount*$discount_per)/100);
				//$test_disacount=0;
				$test_amount_after_discount=$test_amount-$test_disacount;
				
				$pat_encounter=$pat_test["type"];
				$centreno=$pat_test["center_no"];
				
				$comm_amount_test=calculate_commission_test_wise("$refbydoctorid","$testid","$test_amount_after_discount","$pat_encounter","$centreno");
				
				if($comm_amount_test>0)
				{
					$each_test_num++;
					$each_test_amount+=$test_amount_after_discount;
					$each_test_comm_amount+=$comm_amount_test;
					$each_doc_comm_amount+=$comm_amount_test;
					$all_doc_comm_amount+=$comm_amount_test;
				}
			}
?>
				<td style="text-align:right;"><?php echo $each_test_num; ?></td>
				<td style="text-align:right;"><?php echo number_format($each_test_amount,2); ?></td>
				<td style="text-align:right;"><?php echo number_format($each_test_comm_amount,2); ?></td>
<?php
		}
?>
				<th style="text-align:right;"><?php echo number_format($each_doc_comm_amount,2); ?></th>
			</tr>
<?php
			$n++;
		}
?>
		</table>
<?php
	}
}

if($type=="summary_wise")
{
	//print_r($_POST);
	
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$refbydoctorid_search=$refbydoctorid=$_POST['refbydoctorid'];
	$encounter=$_POST['encounter'];
	
	$encounter_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_type=$pat_typ_text["type"];
	}
?>
	<b>Doctor's summary report from <?php echo date("d-M-Y",strtotime($date1)); ?> to <?php echo date("d-M-Y",strtotime($date1)); ?></b>
	
	<button class="btn btn-excel btn-mini text-right print_div" onclick="export_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>')"><i class="icon-file"></i> Excel</button>
	
	<button type="button" id="print_btn" class="btn btn-info btn-mini text-right print_div" onclick="print_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>')"><i class="icon-print icon-large"></i> Print</button>
<?php
	if($encounter==0 || $encounter_type==2)
	{
		$doc_str="SELECT b.`refbydoctorid`, b.`ref_name` FROM `uhid_and_opdid` a, `refbydoctor_master` b, `dal_com_setup` c WHERE a.`refbydoctorid`=b.`refbydoctorid` AND a.`refbydoctorid`=c.`refbydoctorid`";
		
		if($encounter==0)
		{
			$doc_str.=" AND a.`type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
		}
		else
		{
			$doc_str.=" AND a.`type`='$encounter'";
		}
		
		if($date1 && $date2)
		{
			$doc_str.=" AND a.`date` BETWEEN '$date1' AND '$date2'";
		}
		
		if($refbydoctorid)
		{
			//~ $doc_str.=" AND a.`refbydoctorid`='$refbydoctorid'";
			$doc_str.=" AND a.`refbydoctorid` IN($refbydoctorid)";
		}
		
		$doc_str.=" GROUP BY b.`refbydoctorid` ORDER BY b.`ref_name` ASC";
?>
		<table class="table table-condensed table-bordered table-hover">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Name</th>
					<th style="text-align:right;">Bill Amount</th>
					<th style="text-align:right;">Discount Amount</th>
					<th style="text-align:right;">After Discount</th>
					<th style="text-align:right;">Comm Amount</th>
				</tr>
			</thead>
<?php
		$n=1;
		$j=1;
		$all_bill=0;
		$all_disc=0;
		$aft_disc=0;
		$all_com=0;
		$doc_qry=mysqli_query($link, $doc_str);
		while($doc_data=mysqli_fetch_array($doc_qry))
		{
			$refbydoctorid=$doc_data["refbydoctorid"];
			//echo "<tr style='display:none;' id='dr$refbydoctorid'><th colspan='9'>$doc_data[ref_name]</th></tr>";
			
			
			$bill_amount_each=0;
			$discount_amount_each=0;
			$bill_amount_after_discount_each=0;
			$comm_amount_each=0;
			
			$pat_str="SELECT * FROM `uhid_and_opdid` WHERE `refbydoctorid`='$refbydoctorid'";
			
			if($encounter==0)
			{
				$pat_str.=" AND `type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
			}
			else
			{
				$pat_str.=" AND `type`='$encounter'";
			}
			
			if($date1 && $date2)
			{
				$pat_str.=" AND `date` BETWEEN '$date1' AND '$date2'";
			}
			$pat_str.=" ORDER BY `slno` ASC";
			
			$pat_qry=mysqli_query($link, $pat_str);
			while($pat_reg=mysqli_fetch_array($pat_qry))
			{
				$patient_id=$pat_reg["patient_id"];
				$opd_id=$pat_reg["opd_id"];
				$pat_encounter=$pat_reg["type"];
				$centreno=$pat_reg["center_no"];
				$doc_discount=$pat_reg["doc_discount"];
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id`='$patient_id'"));
				
				$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
				
				$bill_amount=$pat_pay_det["tot_amount"];
				$discount_amount=$pat_pay_det["dis_amt"];
				
				if($doc_discount==0)
				{
					$discount_amount=0;
				}
				
				$bill_amount_after_discount=$bill_amount-$discount_amount;
				
				$batch_no=1;
				$comm_amount=calculate_commission_bill_wise("$refbydoctorid","$patient_id","$opd_id","$batch_no","$pat_encounter","$centreno");
				if($comm_amount>0)
				{
?>
				<!--<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo date("d-M-Y",strtotime($pat_reg["date"])); ?></td>
					<td><?php echo $pat_reg["patient_id"]; ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td style="text-align:right;"><?php echo $bill_amount; ?></td>
					<td style="text-align:right;"><?php echo $discount_amount; ?></td>
					<td style="text-align:right;"><?php echo number_format($bill_amount_after_discount,2); ?></td>
					<td style="text-align:right;"><?php echo number_format($comm_amount,2); ?></td>
				</tr>-->
<?php
				$bill_amount_each+=$bill_amount;
				$discount_amount_each+=$discount_amount;
				$bill_amount_after_discount_each+=$bill_amount_after_discount;
				$comm_amount_each+=$comm_amount;
				$n++;
				}
			}
			if($comm_amount_each>0)
			{
				//echo "<script>$('#dr$refbydoctorid').show();</script>";
?>
			<tr>
				<td><?php echo $j;?></td>
				<td><?php echo $doc_data['ref_name'];?></td>
				<td style="text-align:right;"><?php echo number_format($bill_amount_each,2); ?></td>
				<td style="text-align:right;"><?php echo number_format($discount_amount_each,2); ?></td>
				<td style="text-align:right;"><?php echo number_format($bill_amount_after_discount_each,2); ?></td>
				<td style="text-align:right;"><?php echo number_format($comm_amount_each,2); ?></td>
			</tr>
<?php
			$all_bill+=$bill_amount_each;
			$all_disc+=$discount_amount_each;
			$aft_disc+=$bill_amount_after_discount_each;
			$all_com+=$comm_amount_each;
			}
			$j++;
		}
		if($all_com>0)
		{
		?>
			<tr>
				<th></th>
				<th>Total</th>
				<th style="text-align:right;"><?php echo number_format($all_bill,2); ?></th>
				<th style="text-align:right;"><?php echo number_format($all_disc,2); ?></th>
				<th style="text-align:right;"><?php echo number_format($aft_disc,2); ?></th>
				<th style="text-align:right;"><?php echo number_format($all_com,2); ?></th>
			</tr>
		<?php
		}
?>
		</table>
<?php
	}
}

if($type=="bill_wise")
{
	//print_r($_POST);
	
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$refbydoctorid_search=$refbydoctorid=$_POST['refbydoctorid'];
	$encounter=$_POST['encounter'];
	
	$encounter_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_type=$pat_typ_text["type"];
	}
?>
	<b>Bill wise report from <?php echo date("d-M-Y",strtotime($date1)); ?> to <?php echo date("d-M-Y",strtotime($date1)); ?></b>
	
	<button class="btn btn-excel btn-mini text-right print_div" onclick="export_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>')"><i class="icon-file"></i> Excel</button>
	
	<button type="button" id="print_btn" class="btn btn-info btn-mini text-right print_div" onclick="print_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>')"><i class="icon-print icon-large"></i> Print</button>
<?php
	if($encounter==0 || $encounter_type==2)
	{
		$doc_str="SELECT b.`refbydoctorid`, b.`ref_name` FROM `uhid_and_opdid` a, `refbydoctor_master` b, `dal_com_setup` c WHERE a.`refbydoctorid`=b.`refbydoctorid` AND a.`refbydoctorid`=c.`refbydoctorid`";
		
		if($encounter==0)
		{
			$doc_str.=" AND a.`type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
		}
		else
		{
			$doc_str.=" AND a.`type`='$encounter'";
		}
		
		if($date1 && $date2)
		{
			$doc_str.=" AND a.`date` BETWEEN '$date1' AND '$date2'";
		}
		
		if($refbydoctorid)
		{
			//~ $doc_str.=" AND a.`refbydoctorid`='$refbydoctorid'";
			$doc_str.=" AND a.`refbydoctorid` IN($refbydoctorid)";
		}
		
		$doc_str.=" GROUP BY b.`refbydoctorid` ORDER BY b.`ref_name` ASC";
?>
		<table class="table table-condensed table-bordered table-hover">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Date</th>
					<th>UHID</th>
					<th>Bill No</th>
					<th>Name</th>
					<th style="text-align:right;">Bill Amount</th>
					<th style="text-align:right;">Discount Amount</th>
					<th style="text-align:right;">After Discount</th>
					<th style="text-align:right;">Comm Amount</th>
				</tr>
			</thead>
<?php
		$n=1;
		$doc_qry=mysqli_query($link, $doc_str);
		while($doc_data=mysqli_fetch_array($doc_qry))
		{
			$refbydoctorid=$doc_data["refbydoctorid"];
			echo "<tr style='display:none;' id='dr$refbydoctorid'><th colspan='9'>$doc_data[ref_name]</th></tr>";
			
			
			$bill_amount_each=0;
			$discount_amount_each=0;
			$bill_amount_after_discount_each=0;
			$comm_amount_each=0;
			
			$pat_str="SELECT * FROM `uhid_and_opdid` WHERE `refbydoctorid`='$refbydoctorid'";
			
			if($encounter==0)
			{
				$pat_str.=" AND `type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
			}
			else
			{
				$pat_str.=" AND `type`='$encounter'";
			}
			
			if($date1 && $date2)
			{
				$pat_str.=" AND `date` BETWEEN '$date1' AND '$date2'";
			}
			$pat_str.=" ORDER BY `slno` ASC";
			
			$pat_qry=mysqli_query($link, $pat_str);
			while($pat_reg=mysqli_fetch_array($pat_qry))
			{
				$patient_id=$pat_reg["patient_id"];
				$opd_id=$pat_reg["opd_id"];
				$pat_encounter=$pat_reg["type"];
				$centreno=$pat_reg["center_no"];
				$doc_discount=$pat_reg["doc_discount"];
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id`='$patient_id'"));
				
				$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
				
				$bill_amount=$pat_pay_det["tot_amount"];
				$discount_amount=$pat_pay_det["dis_amt"];
				
				if($doc_discount==0)
				{
					$discount_amount=0;
				}
				
				$bill_amount_after_discount=$bill_amount-$discount_amount;
				
				$batch_no=1;
				$comm_amount=calculate_commission_bill_wise("$refbydoctorid","$patient_id","$opd_id","$batch_no","$pat_encounter","$centreno");
				if($comm_amount>0)
				{
?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo date("d-M-Y",strtotime($pat_reg["date"])); ?></td>
					<td><?php echo $pat_reg["patient_id"]; ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td style="text-align:right;"><?php echo $bill_amount; ?></td>
					<td style="text-align:right;"><?php echo number_format($discount_amount,2); ?></td>
					<td style="text-align:right;"><?php echo number_format($bill_amount_after_discount,2); ?></td>
					<td style="text-align:right;"><?php echo number_format($comm_amount,2); ?></td>
				</tr>
<?php
				$bill_amount_each+=$bill_amount;
				$discount_amount_each+=$discount_amount;
				$bill_amount_after_discount_each+=$bill_amount_after_discount;
				$comm_amount_each+=$comm_amount;
				$n++;
				}
			}
			if($comm_amount_each>0)
			{
				echo "<script>$('#dr$refbydoctorid').show();</script>";
?>
			<tr>
				<th colspan="4"></th>
				<th style="text-align:right;">Total</th>
				<th style="text-align:right;"><?php echo number_format($bill_amount_each,2); ?></th>
				<th style="text-align:right;"><?php echo number_format($discount_amount_each,2); ?></th>
				<th style="text-align:right;"><?php echo number_format($bill_amount_after_discount_each,2); ?></th>
				<th style="text-align:right;"><?php echo number_format($comm_amount_each,2); ?></th>
			</tr>
<?php
			}
		}
?>
		</table>
<?php
	}
}

if($type=="bill_wise_detail")
{
	//print_r($_POST);
	
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$refbydoctorid_search=$refbydoctorid=$_POST['refbydoctorid'];
	$encounter=$_POST['encounter'];
	
	$encounter_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_type=$pat_typ_text["type"];
	}
?>
	<b>Bill wise detail report from <?php echo date("d-M-Y",strtotime($date1)); ?> to <?php echo date("d-M-Y",strtotime($date1)); ?></b>
	
	<button type="button" id="print_excel" class="btn btn-excel btn-mini text-right print_div" onclick="export_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>')"><i class="icon-file"></i> Excel</button>
	
	<button type="button" id="print_btn" class="btn btn-info btn-mini text-right print_div" onclick="print_page('<?php echo $type;?>','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $refbydoctorid;?>','<?php echo $encounter;?>')"><i class="icon-print icon-large"></i> Print</button>
	
<?php
	if($encounter==0 || $encounter_type==2)
	{
		$doc_str="SELECT b.`refbydoctorid`, b.`ref_name` FROM `uhid_and_opdid` a, `refbydoctor_master` b, `dal_com_setup` c WHERE a.`refbydoctorid`=b.`refbydoctorid` AND a.`refbydoctorid`=c.`refbydoctorid`";
		
		if($encounter==0)
		{
			$doc_str.=" AND a.`type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
		}
		else
		{
			$doc_str.=" AND a.`type`='$encounter'";
		}
		
		if($date1 && $date2)
		{
			$doc_str.=" AND a.`date` BETWEEN '$date1' AND '$date2'";
		}
		
		if($refbydoctorid)
		{
			//~ $doc_str.=" AND a.`refbydoctorid`='$refbydoctorid'";
			$doc_str.=" AND a.`refbydoctorid` IN($refbydoctorid)";
		}
		
		$doc_str.=" GROUP BY b.`refbydoctorid` ORDER BY b.`ref_name` ASC";
?>
		<table class="table table-condensed table-bordered table-hover">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Date</th>
					<th>UHID</th>
					<th>Bill No</th>
					<th>Name</th>
					<th style="text-align:right;">Bill Amount</th>
					<th style="text-align:right;">Discount Amount</th>
					<!--<th style="text-align:right;">After Discount</th>
					<th style="text-align:right;">Comm Amount</th>-->
					<th>Test Name</th>
					<th style="text-align:right;">Test Rate</th>
					<th style="text-align:right;">Comm Amount</th>
					<th style="text-align:right;">Total Comm</th>
				</tr>
			</thead>
<?php
		$n=1;
		$doc_qry=mysqli_query($link, $doc_str);
		while($doc_data=mysqli_fetch_array($doc_qry))
		{
			$refbydoctorid=$doc_data["refbydoctorid"];
			echo "<tr><th colspan='11' style='display:none;' id='dr$refbydoctorid'>$doc_data[ref_name]</th></tr>";
			
			
			$bill_amount_each=0;
			$discount_amount_each=0;
			$bill_amount_after_discount_each=0;
			$comm_amount_each=0;
			
			$pat_str="SELECT * FROM `uhid_and_opdid` WHERE `refbydoctorid`='$refbydoctorid'";
			
			if($encounter==0)
			{
				$pat_str.=" AND `type` IN(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2)";
			}
			else
			{
				$pat_str.=" AND `type`='$encounter'";
			}
			
			if($date1 && $date2)
			{
				$pat_str.=" AND `date` BETWEEN '$date1' AND '$date2'";
			}
			$pat_str.=" ORDER BY `slno` ASC";
			
			$pat_qry=mysqli_query($link, $pat_str);
			while($pat_reg=mysqli_fetch_array($pat_qry))
			{
				$patient_id=$pat_reg["patient_id"];
				$opd_id=$pat_reg["opd_id"];
				$pat_encounter=$pat_reg["type"];
				$centreno=$pat_reg["center_no"];
				$doc_discount=$pat_reg["doc_discount"];
				
				$comm_amount_bill=0;
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id`='$patient_id'"));
				
				$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
				
				$bill_amount=$pat_pay_det["tot_amount"];
				$discount_amount=$pat_pay_det["dis_amt"];
				
				if($doc_discount==0)
				{
					$discount_amount=0;
				}
				
				$bill_amount_after_discount=$bill_amount-$discount_amount;
				
				$batch_no=1;
				$comm_amount_bill=calculate_commission_bill_wise("$refbydoctorid","$patient_id","$opd_id","$batch_no","$pat_encounter","$centreno");
				
				$discount_per=round(($discount_amount/$bill_amount)*100,2);
				
				$pat_test_qry=mysqli_query($link, "SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
				$pat_test_num=mysqli_num_rows($pat_test_qry);
				
				$i=1;
				while($pat_test=mysqli_fetch_array($pat_test_qry))
				{
					$comm_amount_test=0;
					
					$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `testname`,`category_id`,`type_id` FROM `testmaster` WHERE `testid`='$pat_test[testid]'"));
					
					$testid   =$pat_test["testid"];
					$test_amount=$pat_test["test_rate"];
					
					$test_disacount=round(($test_amount*$discount_per)/100);
					//$test_disacount=0;
					$test_amount_after_discount=$test_amount-$test_disacount;
					
					$comm_amount_test=calculate_commission_test_wise("$refbydoctorid","$testid","$test_amount_after_discount","$pat_encounter","$centreno");
					$tr_display="";
					//if($pat_test_num==1 && $comm_amount_test==0 || $comm_amount_bill==0)
					if($pat_test_num==1 && $comm_amount_test==0 || $comm_amount_bill==0)
					{
						$tr_display="display:none;";
					}
					
					$td_cls="";
					if($comm_amount_test==0)
					{
						$td_cls="remove_td";
					}
					
					if($comm_amount_bill>0)
					{
?>
					<tr style="<?php echo $tr_display;?>">
					<?php if($i==1){ ?>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $n; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo date("d-M-Y",strtotime($pat_reg["date"])); ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $pat_reg["patient_id"]; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $pat_reg["opd_id"]; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>"><?php echo $pat_info["name"]; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>" style="text-align:right;"><?php echo $bill_amount; ?></td>
						<td rowspan="<?php echo $pat_test_num; ?>" style="text-align:right;"><?php echo number_format($discount_amount,2); ?></td>
						<!--<td rowspan="<?php echo $pat_test_num; ?>" style="text-align:right;"><?php echo number_format($bill_amount_after_discount,2); ?></td>-->
					<?php } ?>
						<td class="<?php echo $td_cls; ?>"><span style="<?php if($comm_amount_test==0){echo "display:none;";}?>"><?php echo $test_info["testname"]; ?></span></td>
						<td class="<?php echo $td_cls; ?>" style="text-align:right;"><span style="<?php if($comm_amount_test==0){echo "display:none;";}?>"><?php echo $test_amount; ?></span></td>
						<td class="<?php echo $td_cls; ?>" style="text-align:right;"><span style="<?php if($comm_amount_test==0){echo "display:none;";}?>"><?php echo number_format($comm_amount_test,2); ?></span></td>
					<?php if($i==1){ ?>
						<td rowspan="<?php echo $pat_test_num; ?>" style="text-align:right;"><?php echo number_format($comm_amount_bill,2); ?></td>
					<?php } ?>
					</tr>
<?php
						$i++;
					}
				}
				
				$bill_amount_each+=$bill_amount;
				$discount_amount_each+=$discount_amount;
				$bill_amount_after_discount_each+=$bill_amount_after_discount;
				$comm_amount_each+=$comm_amount_bill;
				if($tr_display=="")
				{
					$n++;
				}
			}
?>
			<tr style="<?php echo $tr_display;?>">
				<th colspan="4"></th>
				<th style="text-align:right;">Total</th>
				<th style="text-align:right;"><?php echo number_format($bill_amount_each,2); ?></th>
				<th style="text-align:right;"><?php echo number_format($discount_amount_each,2); ?></th>
				<!--<th style="text-align:right;"><?php echo number_format($bill_amount_after_discount_each,2); ?></th>-->
				<td></td>
				<td></td>
				<td></td>
				<th style="text-align:right;"><?php echo number_format($comm_amount_each,2); ?></th>
			</tr>
<?php
			if($comm_amount_each>0)
			{
				echo "<script>$('#dr$refbydoctorid').show();</script>";
			}
		}
?>
		</table>
<?php
	}
}

if($type=="load_doctors")
{
	$doc_name=$_POST['doc_name'];
	
	$str="SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `ref_name`!=''";
	
	if($doc_name)
	{
		$str.=" AND `ref_name` LIKE '%$doc_name%'";
	}
	
	$str.=" ORDER BY `ref_name` ASC";
	
	$qry=mysqli_query($link, $str);
?>
	<table class="table table-condensed">
		<thead class="table_header_fix">
			<th>#</th>
			<th>Name</th>
		</thead>
<?php
	$n=1;
	while($data=mysqli_fetch_array($qry))
	{
?>
		<tr style="cursor:pointer;" onclick="select_doc('<?php echo $data["refbydoctorid"]; ?>')" id="tr<?php echo $data["refbydoctorid"]; ?>">
			<td>
				<input type="hidden" class="chk" id="doc_id" value="<?php echo $data["refbydoctorid"]; ?>">
				<?php echo $n; ?>
			</td>
			<td><?php echo $data["ref_name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}

if($type=="load_sel_doctors")
{
	$sel_refbydoctorids=$_POST['sel_refbydoctorids'];
	
	$str="SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid` IN($sel_refbydoctorids)";
	
	//$str.=" ORDER BY `ref_name` ASC";
	
	$qry=mysqli_query($link, $str);
?>
	<table class="table table-condensed">
		<thead class="table_header_fix">
			<th>#</th>
			<th>Name</th>
		</thead>
<?php
	$n=1;
	while($data=mysqli_fetch_array($qry))
	{
?>
		<tr style="cursor:pointer;" onclick="remove_doc('<?php echo $data["refbydoctorid"]; ?>')" id="tr<?php echo $data["refbydoctorid"]; ?>">
			<td>
				<input type="hidden" class="chk" id="sel_doc_id" value="<?php echo $data["refbydoctorid"]; ?>">
				<?php echo $n; ?>
			</td>
			<td><?php echo $data["ref_name"]; ?></td>
		</tr>
<?php
		$n++;
	}
?>
	</table>
<?php
}
?>
