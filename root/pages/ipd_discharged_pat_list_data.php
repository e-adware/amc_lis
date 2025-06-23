<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$date=date("Y-m-d");

if($_POST["type"]=="search_patient_list_ipd_dis")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ipd_serial=$_POST['ipd_serial'];
	$from=$_POST['from'];
	$to=$_POST['to'];
	$usr=$_POST['usr'];
	$balance_discharge=$_POST['balance_discharge'];
	$list_start=$_POST['list_start'];
	
	$f_date=strtotime(date("Y-m-d"));
	$f_date1=date("Y-m-d");
	$t_date=date("Y-m-d",strtotime('-10 day',$f_date));
	
	$limit_str=" limit ".$list_start;
	
	$str="SELECT * FROM `ipd_pat_discharge_details` WHERE `slno`>0";
	
	if($uhid)
	{
		$str.=" AND `patient_id`='$uhid'";
	}
	
	if($ipd)
	{
		$str.=" AND `ipd_id`='$ipd'";
	}
	if(strlen($name)>1)
	{
		$str.=" AND `patient_id` in (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%')";
	}
	
	if($from && $to)
	{
		$str.=" AND `date` between '$from' AND '$to'";
		
		$limit_str="";
	}
	
	if($balance_discharge==1)
	{
		$str.=" AND `ipd_id` in (SELECT `ipd_id` FROM `ipd_discharge_balance_pat` WHERE `bal_amount`>0)";
	}
	
	if($balance_discharge==2)
	{
		$str.=" AND (`ipd_id` IN (SELECT `ipd_id` FROM `ipd_discharge_balance_pat` WHERE `bal_amount`=0) OR `ipd_id` NOT IN (SELECT `ipd_id` FROM `ipd_discharge_balance_pat`))";
	}
	
	$str.=" ORDER BY `slno` DESC".$limit_str;
	
	//echo $str;
	
	$num=mysqli_num_rows(mysqli_query($link,$str));
	if($num>0)
	{
		$qry=mysqli_query($link,$str);
?>
		<p style="margin-top: 2%;" id="print_div">
			<span class="text-right" id="excel_btn_hide"><a class="btn btn-info btn-mini" href="pages/ipd_discharged_pat_list_xls.php?uhid=<?php echo $uhid ?>&ipd=<?php echo $ipd ?>&ipd_serial=<?php echo $ipd_serial ?>&name=<?php echo $name ?>&from=<?php echo $from ?>&to=<?php echo $to ?>&balance_discharge=<?php echo $balance_discharge ?>"><i class="icon-file icon-large" style="line-height: 24px;"></i> Excel</a></span>
			
		</p>
		<table class="table table-condensed table-bordered">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>UHID</th>
					<th>IPD ID</th>
					<th>Name</th>
					<th>Sex</th>
					<th>Age (DOB)</th>
					<th>Bill Amount</th>
					<th>Admitted By</th>
					<!--<th>Contact</th>-->
					<th>Admission Date</th>
					<th>Admission Time</th>
					<th>Discharge Date</th>
					<th>Discharge Time</th>
					<th>Discharge User</th>
				</tr>
			</thead>
		<?php
			$i=1;
			while($data=mysqli_fetch_array($qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]'"));
				
				$tr_class="discharged";
					
				$admit_det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where `patient_id`='$data[patient_id]' AND `opd_id`='$data[ipd_id]' "));
				$dis_date=convert_date($data['date']);
				$dis_time=convert_time($data['time']);
				
				if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$dis_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
				
				$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$data[user]' "));
				
				$doc_id=mysqli_fetch_array(mysqli_query($link, " SELECT `attend_doc`, `admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$data[patient_id]' AND `ipd_id`='$data[ipd_id]' "));

				$attend_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$doc_id[admit_doc]' "));
				
				// Bill Details
				$uhid=$data["patient_id"];
				$ipd=$data["ipd_id"];
				
				$baby_serv_tot=0;
				$baby_ot_total=0;
				$delivery_check_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
				while($delivery_check=mysqli_fetch_array($delivery_check_qry))
				{
					//$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
					if($delivery_check)
					{
						$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
						$baby_serv_tot+=$baby_tot_serv["tots"];
						
						// OT Charge Baby
						$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(`amount`),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
						$baby_ot_total+=$baby_ot_tot_val["g_tot"];
					}
				}
				
				$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
				$no_of_days=$no_of_days_val["ser_quantity"];
				
				$tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
				$tot_serv_amt=$tot_serv["tots"];
				
				// OT Charge
				$grp_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(`amount`),0) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
				$grp_tot=$grp_tot_val["g_tot"];
				
				$tot_bill_amount=$tot_serv_amt+$baby_serv_tot+$baby_ot_total+$grp_tot;
				
			?>
				<tr onclick="redirect_page('<?php echo $data['patient_id'];?>','<?php echo $data['ipd_id'];?>')" style="cursor:pointer;" class="<?php //echo $tr_class;?>">
					<td><?php echo $i;?></td>
					<td><?php echo $pat_info['patient_id'];?></td>
					<td><?php echo $data['ipd_id'];?></td>
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo $pat_info['sex'];?></td>
					<td><?php echo $age;?></td>
					<td style="text-align:right;"><?php echo number_format($tot_bill_amount,2);?></td>
					<td><?php echo $attend_doc["Name"];?></td>
					<!--<td><?php echo $pat_info['phone'];?></td>-->
					<td><?php echo convert_date($admit_det['date']);?></td>
					<td><?php echo convert_time($admit_det['time']);?></td>
					<td><?php echo $dis_date;?></td>
					<td><?php echo $dis_time;?></td>
					<td><?php echo $emp_info["name"]; ?></td>
				</tr>
			<?php
				$i++;
			}
		?>
		</table>
		<?php
	}
}

?>
