<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

$discount_element_disable="";
if($emp_info["discount_permission"]==0)
{
	$discount_element_disable="readonly";
}

if($_POST["type"]=="load_district_pat")
{
	$val=$_POST["val"];
	$state_qry=mysqli_query($link, " SELECT * FROM `district` WHERE `state_id`='$val' ORDER BY `name` " );
	echo "<option value='0'>All</option>";
	while($state=mysqli_fetch_array($state_qry))
	{
		//if($state['state_id']=='4'){ $sel_state="selected"; }else{ $sel_state=""; }
		echo "<option value='$state[district_id]' $sel_state >$state[name]</option>";
	}
}
if($_POST["type"]=="load_all_pat")
{
	$pat_type=$_POST["pat_type"];
	$branch_id=$_POST["branch_id"];
	$fdate=$_POST["from"];
	$tdate=$_POST["to"];
	$pat_name=$_POST["pat_name"];
	$pat_uhid=$_POST["pat_uhid"];
	$pin=$_POST["pin"];
	$phone=$_POST["phone"];
	$state=$_POST["state"];
	$district=$_POST["district"];
	$ref_doc_id=$_POST["ref_doc_id"];
	$health_guide_id=$_POST["health_guide_id"];
	$list_start=$_POST["list_start"];
	
	$q=" SELECT * FROM `uhid_and_opdid` WHERE `slno`>0 ";
	
	$z=0;
	
	if($fdate && $tdate)
	{
		$q=" SELECT * FROM `uhid_and_opdid` WHERE `date` between '$fdate' and '$tdate' ";
		$z=1;
	}
	if(strlen($pat_name)>2)
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$pat_name%' ) ";
		$z=1;
	}
	
	if($state!='0' && $district!='0')
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `state`='$state' AND `district`='$district' ) ";
		$z=1;
	}else if($state!='0')
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `state`='$state' ) ";
		$z=1;
	}else if($district!='null')
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `district`='$district' ) ";
		$z=1;
	}
	if(strlen($phone)>3)
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `phone` like '$phone%' ) ";
		$z=1;
	}
	if(strlen($pat_uhid)>2)
	{
		$q.=" AND `patient_id` like '$pat_uhid%' ";
		$z=1;
	}
	if(strlen($pin)>2)
	{
		$q.=" AND `opd_id` like '$pin%' ";
		$z=1;
	}
	
	if($health_guide_id)
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `pat_health_guide` WHERE `hguide_id`='$health_guide_id' )";
		$z=1;
	}
	
	if($z==0)
	{
		$q=" SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' and `slno`>0 ";
	}
	
	if($pat_type>0)
	{
		$q.=" AND `type`='$pat_type' ";
	}
	
	$q.=" AND `branch_id`='$branch_id' order by `slno` DESC limit ".$list_start;
	//echo $q;
	$pat_reg_qry=mysqli_query($link, $q );
	
?>
	<table class="table table-bordered text-center">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No.</th>
				<th>Patient Name</th>
				<th>Age</th>
				<th>Sex</th>
				<th>Phone</th>
				<th>Date</th>
				<th>Type</th>
				<th>User</th>
			</tr>
		</thead>
	<?php
		$n=1;
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
			
			$reg_date=$pat_reg["date"];
			
			if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
			
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
			$pat_typ=$pat_typ_text['p_type'];
			
			$cashier_access_num=1;
			
			$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' AND `type`='2' "));
			if($cancel_request)
			{
				$td_function="";
				$td_style="";
				$tr_back_color="style='background-color: #ff000021'";
				
				$emp_info_del=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request[user]' "));
				
				$tr_title="title='Cancel request by $emp_info_del[name]' id='cancel_request_tr'";
			}
			else
			{
				$td_function="onclick=\"redirect_page('$pat_reg[patient_id]','$pat_reg[opd_id]','$pat_reg[type]','$cashier_access_num')\"";
				$td_style="style='cursor:pointer;'";
				$tr_back_color="";
				$tr_title="";
			}
	?>
			<tr <?php echo $tr_back_color." ".$tr_title; ?> >
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $n; ?>
				</td>
				<td><?php echo $pat_info["patient_id"]; ?></td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $pat_reg["opd_id"]; ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $pat_info["name"]; ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $age; ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $pat_info["sex"]; ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $pat_info["phone"]; ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo convert_date($pat_reg["date"]); ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $pat_typ; ?>
				</td>
				<td>
					<?php echo $user_info["name"]; ?>
				</td>
			</tr>
	<?php
			$n++;
		}
	?>
	</table>
	
<?php
}

?>
