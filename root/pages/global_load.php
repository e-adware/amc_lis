<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$discount_element_disable="";
if($emp_info["discount_permission"]==0)
{
	$discount_element_disable="readonly";
}

$date=date("Y-m-d");
$time=date("H:i:s");

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
	$no_counter=" SELECT * FROM `uhid_and_opdid` WHERE `slno`>0 ";
	
	$z=0;
	
	if($fdate && $tdate)
	{
		$q=" SELECT * FROM `uhid_and_opdid` WHERE `date` between '$fdate' and '$tdate' ";
		$no_counter=" SELECT * FROM `uhid_and_opdid` WHERE `date` between '$fdate' and '$tdate' ";
		$z=1;
	}
	if(strlen($pat_name)>2)
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$pat_name%' ) ";
		$no_counter.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$pat_name%' ) ";
		$z=1;
	}
	
	if($state!='0' && $district!='0')
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `state`='$state' AND `district`='$district' ) ";
		$no_counter.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `state`='$state' AND `district`='$district' ) ";
		$z=1;
	}else if($state!='0')
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `state`='$state' ) ";
		$no_counter.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `state`='$state' ) ";
		$z=1;
	}else if($district!='null')
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `district`='$district' ) ";
		$no_counter.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `district`='$district' ) ";
		$z=1;
	}
	if(strlen($phone)>3)
	{
		$q.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `phone` like '$phone%' ) ";
		$no_counter.=" AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `phone` like '$phone%' ) ";
		$z=1;
	}
	if(strlen($pat_uhid)>2)
	{
		$q.=" AND `patient_id` like '$pat_uhid%' ";
		$no_counter.=" AND `patient_id` like '$pat_uhid%' ";
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
		$no_counter=" SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' and `slno`>0 ";
	}
	
	if($pat_type>0)
	{
		$q.=" AND `type`='$pat_type' ";
	}
	
	$q.=" order by `slno` DESC limit ".$list_start;
	//echo $q;
	$qq_qry=mysqli_query($link, $q );
	
?>
	<table class="table table-bordered text-center">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>Bill No.</th>
				<th>Patient Name</th>
				<th style="width: 5%;">Age/Sex</th>
				<th>Phone</th>
				<!--<th>Ward &nbsp; Bed</th>-->
				<th>Date</th>
				<th>Type</th>
				<th>User</th>
			</tr>
		</thead>
	<?php
		$n=1;
		while($qq=mysqli_fetch_array($qq_qry))
		{
			$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$qq[user]' "));
			
			$ward_bed_str="";
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$qq[patient_id]' "));
			if($ref_doc_id)
			{
				if($ref_doc_id==$pat_info["refbydoctorid"])
				{
					$ref_doc_pat="Yes";
				}else
				{
					$ref_doc_pat="No";
				}
			}else
			{
				$ref_doc_pat="Yes";
			}
			if($ref_doc_pat=="Yes")
			{
			
				if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
				
				$cashier_access_num=0;
				$cashier_access=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cashier_access` WHERE `emp_id`='$c_user' "));
				if($qq["type"]==1)
				{
					//$pat_typ="OPD";
					if($cashier_access["opd_cashier"]>0)
					{
						$cashier_access_num=1;
					}
				}else if($qq["type"]==2)
				{
					//$pat_typ="LAB";
					if($cashier_access["lab_cashier"]>0)
					{
						$cashier_access_num=1;
					}
				}else if($qq["type"]==3)
				{
					//$pat_typ="IPD";
					if($cashier_access["ipd_cashier"]>0)
					{
						$cashier_access_num=1;
					}
					
				}else if($qq["type"]==4)
				{
					//$pat_typ="Casualty";
					if($cashier_access["casuality_cashier"]>0)
					{
						$cashier_access_num=1;
					}
				}
				else if($qq["type"]==5)
				{
					//$pat_typ="IPD LAB";
					$cashier_access_num=1;
				}
				else if($qq["type"]==8)
				{
					//$pat_typ="Radiology";
					$cashier_access_num=1;
				}
				$emp_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
				if($emp_level["levelid"]==2)
				{
					$cashier_access_num=1;
				}
				$cashier_access_num=1;
				
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$qq[type]' "));
				$pat_typ=$pat_typ_text['p_type'];
				
				$cashier_access_num=1;
				
				$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$qq[patient_id]' AND `opd_id`='$qq[opd_id]' AND `type`='2' "));
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
					$td_function="onclick=\"redirect_page('$qq[patient_id]','$qq[opd_id]','$qq[type]','$cashier_access_num')\"";
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
					<?php echo $qq["opd_id"]; ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $pat_info["name"]; ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $age."/".$pat_info["sex"]; ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $pat_info["phone"]; ?>
				</td>
				<!--<td><?php echo $ward_bed_str; ?></td>-->
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo convert_date($qq["date"]); ?>
				</td>
				<td <?php echo $td_function; ?> <?php echo $td_style; ?>>
					<?php echo $pat_typ; ?>
				</td>
				<td>
					<?php echo $user_info["name"]; ?>
				<?php if($approved==0 && ($emp_info["levelid"]==1 || $emp_info["levelid"]==2)){ ?>
					<!--<button class="btn <?php echo $btn_class; ?> btn-mini" onclick="delete_request_up('<?php echo $pat_info["patient_id"]; ?>','<?php echo $qq["opd_id"]; ?>')" style="float:right;"><i class="<?php echo $icon_name; ?>"></i></button>-->
				<?php } ?>
				</td>
			</tr>
	<?php
			$n++;
			
			}
		}
		
		// No encounter patient list
		$ref_doc_id_qry="";
		if($ref_doc_id)
		{
			$ref_doc_id_qry.=" a.`refbydoctorid`='$ref_doc_id'";
		}
		//$un_countr_qry=mysqli_query($link, " SELECT a.* FROM `patient_info` a WHERE a.`patient_id`>0 and NOT EXISTS ( SELECT * FROM `uhid_and_opdid` WHERE `patient_id`=a.`patient_id` ) and a.`date`='$date' $ref_doc_id_qry ORDER BY a.`slno` DESC " );
		
		//$un_countr_num=mysqli_num_rows($un_countr_qry);
		$un_countr_num=0;
		if($un_countr_num>0)
		{
			echo "<tr><td colspan='9'><b>No encounter</b></td></tr>";
			$m=1;
			while($un_countr=mysqli_fetch_array($un_countr_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$un_countr[patient_id]' "));
			
				if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
				
				$info_rel_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$un_countr[patient_id]' "));
			?>
			<tr>
				<td onClick="redirect_page_rel('<?php echo $un_countr["patient_id"]; ?>','<?php echo $info_rel_num; ?>')" style="cursor:pointer;"><?php echo $m; ?></td>
				<td onClick="redirect_page_rel('<?php echo $un_countr["patient_id"]; ?>','<?php echo $info_rel_num; ?>')" style="cursor:pointer;"><?php echo $pat_info["patient_id"]; ?></td>
				<td onClick="redirect_page_rel('<?php echo $un_countr["patient_id"]; ?>','<?php echo $info_rel_num; ?>')" style="cursor:pointer;"><?php echo $pat_info["name"]; ?></td>
				<td onClick="redirect_page_rel('<?php echo $un_countr["patient_id"]; ?>','<?php echo $info_rel_num; ?>')" style="cursor:pointer;"><?php echo $age."/".$pat_info["sex"]; ?></td>
				<td onClick="redirect_page_rel('<?php echo $un_countr["patient_id"]; ?>','<?php echo $info_rel_num; ?>')" style="cursor:pointer;"><?php echo $pat_info["phone"]; ?></td>
				<td onClick="redirect_page_rel('<?php echo $un_countr["patient_id"]; ?>','<?php echo $info_rel_num; ?>')" style="cursor:pointer;"><?php echo $pat_info["address"]; ?></td>
				<td onClick="redirect_page_rel('<?php echo $un_countr["patient_id"]; ?>','<?php echo $info_rel_num; ?>')" style="cursor:pointer;"><?php echo convert_date($pat_info["date"]); ?></td>
				<td><button class="btn btn-mini btn-danger" onClick="delete_no_encounter('<?php echo $un_countr["patient_id"]; ?>')">Delete</button></td>
			</tr>
			<?php
			$m++;
			}
		}
	?>
	</table>
	
<?php
}
if($_POST["type"]=="load_all_pat_revisit")
{
	$pat_name=$_POST["pat_name"];
	$pat_uhid=$_POST["pat_uhid"];
	
	if($pat_name=="" && $pat_uhid=="")
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' ";
	}else
	{
		$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id`>0 ";
	}
	if($pat_name)
	{
		$q.=" and `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$pat_name%' ) ";
	}
	if($pat_uhid)
	{
		$q.=" and `patient_id` like '$pat_uhid%' ";
	}
	$q.=" order by `slno` DESC";
	//echo $q;
	$qq_qry=mysqli_query($link, $q );
	$qq_num=mysqli_num_rows($qq_qry);
	if($qq_num>0)
	{
?>
	<table class="table table-bordered text-center">
		<tr>
			<th>Sl No</th>
			<th>UHID</th>
			<th>Patient Name</th>
			<th>Age/Sex</th>
			<th>Phone</th>
			<th>Address</th>
			<th>Date</th>
			<!--<td>Action</td>-->
		</tr>
	<?php
		$n=1;
		while($qq=mysqli_fetch_array($qq_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$qq[patient_id]' "));
			
			if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
	?>
		<tr onClick="redirect_page(<?php echo $qq["patient_id"]; ?>)" style="cursor:pointer;">
			<td><?php echo $n; ?></td>
			<td><?php echo $pat_info["patient_id"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age."/".$pat_info["sex"]; ?></td>
			<td><?php echo $pat_info["phone"]; ?></td>
			<td><?php echo $pat_info["address"]; ?></td>
			<td><?php echo convert_date($pat_info["date"]); ?></td>
			<!--<td>
				<button class="btn btn-default" onClick="update_patient(<?php echo $pat_info["patient_id"]; ?>)" title="Edit"><i class="icon-edit"></i> </button>
				<button class="btn btn-info" onClick="redirect_page(<?php echo $pat_info["patient_id"]; ?>)" title="Go"><i class="icon-share"></i> </button>
			</td>-->
		</tr>
	<?php
		$n++;
		}
	?>
	</table>
<?php
	}
}
if($_POST["type"]=="con_doc_average_time")
{
	$con_doc_id=$_POST["con_doc_id"];
	$con_doc_val=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `con_doc_available_time` WHERE `consultantdoctorid`='$con_doc_id' "));

?>
	<table class="table" style="border-bottom: 1px solid #ccc;">
		<tr>
			<th class="span4">Set Average Time Per Patient</th>
			<td class="span3">
				<input type="text" class="span1" id="average_time" value="<?php echo $con_doc_val['average_time']; ?>" onKeyup="average_time(this.value,event)" autofocus> (in minutes)
			</td>
			<td>
				<button class="btn btn-info" id="save" onClick="save_average_time()">Save</button>
			</td>
		</tr>
	</table>
<?php
	if($con_doc_val['average_time']>0)
	{
?>
		<div class="widget-box">
			<div class="widget-title">
				<ul class="nav nav-tabs">
					<li class="active" onClick="days(1)"><a data-toggle="tab" href="#tab1">Sun Day</a></li>
					<li class="" onClick="days(2)"><a data-toggle="tab" href="#tab2">Monday</a></li>
					<li class="" onClick="days(3)"><a data-toggle="tab" href="#tab3">Tuesday</a></li>
					<li class="" onClick="days(4)"><a data-toggle="tab" href="#tab4">Wednesday</a></li>
					<li class="" onClick="days(5)"><a data-toggle="tab" href="#tab5">Thursday</a></li>
					<li class="" onClick="days(6)"><a data-toggle="tab" href="#tab6">Friday</a></li>
					<li class="" onClick="days(7)"><a data-toggle="tab" href="#tab7">Saturday</a></li>
				</ul>
			</div>
			<div class="widget-content tab-content">
				<div id="tab" class="tab-pane active">
					
				</div>
			</div>
		</div>
<?php
	}
}
if($_POST["type"]=="average_time_picker")
{
	$day=$_POST["day"];
	$con_doc_id=$_POST["con_doc_id"];
	$con_doc_val=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `con_doc_available_time` WHERE `consultantdoctorid`='$con_doc_id' "));
	$week = array("","sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"); 
	$this_day=$week[$day];
	$val=$con_doc_val[$this_day];
?>
	<table class="table" id="time_range_table">
		<tr>
			<th colspan="2">Available Time Range</th>
		</tr>
<?php
	if($val!="")
	{
		$val=explode("##",$val);
		$n=1;
		$tot_pat=0;
		foreach($val as $val)
		{
			if($val)
			{
				$tim=explode("@@",$val);
				$datetime1 = new DateTime($tim[0]);
				$datetime2 = new DateTime($tim[1]);
				$interval = $datetime1->diff($datetime2);
				$time_diff=$interval->format('%h')." Hours ".$interval->format('%i')." Minutes";
				$d_minute=$interval->format('%i');
				$n_minute=round($d_minute/$con_doc_val["average_time"]);
				$d_hour=$interval->format('%h');
				$n_hour=round(($d_hour*60)/$con_doc_val["average_time"]);
				$tot_pat=$n_minute+$n_hour;
?>
			<tr id="row<?php echo $day.$n; ?>">
				<th style="width:9%;">Session <?php echo $n; ?></th>
				<td class="">
					<input type="text" class="timepicker tmp<?php echo $day; ?> span1 right_click" id="start_time<?php echo $day.$n; ?>" value="<?php echo $tim[0]; ?>" onkeyup="clear_input(this.id)">
					<input type="text" class="timepicker tmp<?php echo $day; ?> span1 right_click" id="end_time<?php echo $day.$n; ?>" value="<?php echo $tim[1]; ?>" onkeyup="clear_input(this.id)">
					= <?php echo $time_diff; ?>
					<?php //if($n!=1){ ?>
						<button class="btn btn-danger" onClick="del_session('<?php echo $n; ?>')"><i class="icon-remove"></i></button>
					<?php //} ?>
					<br><b>Maximum patient can be appointed: </b><?php echo $tot_pat; ?>
				</td>
			</tr>
<?php
			$n++;
			}
		}
	}else
	{
?>
		<tr id="row<?php echo $day; ?>1">
			<th style="width:9%;">Session 1</th>
			<td class="">
				<input type="text" class="timepicker tmp<?php echo $day; ?> span1 right_click" id="start_time<?php echo $day; ?>1" onkeyup="clear_input(this.id)">
				<input type="text" class="timepicker tmp<?php echo $day; ?> span1 right_click" id="end_time<?php echo $day; ?>1" onkeyup="clear_input(this.id)">
			</td>
		</tr>
<?php
	}
?>
	</table>
	<input type="hidden" id="this_day" value="<?php echo $day; ?>">
	<button class="btn btn-info" onClick="save_time_range('<?php echo $day; ?>')">Save</button>
	<button class="btn btn-warning" onClick="add_more_session('<?php echo $day; ?>')">Add More Session</button>
<?php
}
if($_POST["type"]=="check_appointment_already")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["pin"];
	
	$alrdy_apoitmnt_qry=mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
	$alrdy_apoitmnt_num=mysqli_num_rows($alrdy_apoitmnt_qry);
	echo $alrdy_apoitmnt_num;
}
if($_POST["type"]=="new_appointment")
{
	$uhid=$_POST["uhid"];
	
?>
	<table class="table custom_table">
		<tr>
			<th class="span3">Select Department</th>
			<td>
				<select id="dept_id" onChange="dept_sel()" onKeyUp="dept_sel_Up(event)">
					<option value="0">Select</option>
				<?php
				$dept_qry=mysqli_query($link, " SELECT * FROM `doctor_specialist_list` order by `name` ");
				while($dept=mysqli_fetch_array($dept_qry))
				{
					echo "<option value='$dept[speciality_id]'>$dept[name]</option>";
				}
				?>
				</select>
			</td>
			<th>Referred By</th>
			<td colspan="1">
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="ref_load_refdoc1()" onKeyUp="ref_load_refdoc(this.value,event,'opd')" onBlur="javascript:$('#ref_doc').fadeOut(500)" />
				
				<button class="btn btn-new btn-mini" name="new_doc" id="new_doc" value="New" onClick="load_new_ref_doc()"><i class="icon-edit"></i> New</button>
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
					<table style="background-color:#FFF;" class="table table-bordered table-condensed" id="center_table">
						<th>Doctor ID</th>
						<th>Doctor Name</th>
						<?php
							$d=mysqli_query($link, "select * from refbydoctor_master where refbydoctorid='101' order by ref_name");
							$i=1;
							while($d1=mysqli_fetch_array($d))
							{
						?>
							<tr onClick="doc_load('<?php echo $d1['refbydoctorid'];?>','<?php echo $d1['ref_name'];?>','<?php echo $mrk['name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
								<td>
									<?php echo $d1['refbydoctorid'];?>
								</td>
								<td>
									<?php echo $d1['ref_name'];?>
									<div <?php echo "id=dvdoc".$i;?> style="display:none;">
										<?php echo "#".$d1['refbydoctorid']."#".$d1['ref_name'];?>
									</div>
								</td>
							</tr>
						<?php
							$i++;
							}
						?>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<div id="load_all_form">
		
	</div>
<?php
}
if($_POST["type"]=="load_dept_doc")
{
	$dept_id=$_POST["dept_id"];
	$uhid=$_POST["uhid"];
	
	//~ $visit_type_id=1;
	//~ $visit_validity=15;
	
	//~ $last_paid_visit=mysqli_fetch_array(mysqli_query($link, " SELECT `appointment_date` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `visit_fee`>0 ORDER BY `slno` DESC LIMIT 1 "));
	//~ if($last_paid_visit)
	//~ {
		//~ $dates_array = getDatesFromRange($last_paid_visit["appointment_date"], $date);
		//~ $visit_fee_day_diff=sizeof($dates_array);
		//~ if($visit_fee_day_diff<=$visit_validity)
		//~ {
			//~ $visit_type_id=3;
		//~ }else
		//~ {
			//~ $visit_type_id=6;
		//~ }
	//~ }
	
	// Session Check
	$session_check=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(MAX(`doctor_session`),0) AS `sesssion` FROM `appointment_book` WHERE `date`='$date' "));
	if($session_check["sesssion"]==0)
	{
		$current_sesssion=1;
	}else
	{
		$current_sesssion=$session_check["sesssion"];
	}
	
	$pat_esi_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
	
	$pat_center=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_source_master` WHERE `source_id`='$pat_esi_check[source_id]' "));
	
	$center_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `centremaster` WHERE `centreno`='$pat_center[centreno]' "));
	
	$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `dept_id`='$dept_id' AND `status`='0' order by `Name` ");
	//$con_doc_qry=mysqli_query($link, " SELECT a.* FROM `consultant_doctor_master` a, `employee` b WHERE a.`emp_id`=b.`emp_id` AND a.`dept_id`='$dept_id' AND b.`status`='0' ORDER BY `Name` ");
	$con_doc_num=mysqli_num_rows($con_doc_qry);
	if($con_doc_num!=0)
	{
		//~ $check_last_regd_fee_qry=mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' and `regd_fee`>0 order by `slno` DESC limit 0,1 ");
		//~ $check_last_regd_fee_num=mysqli_num_rows($check_last_regd_fee_qry);
		//~ $check_regd_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_registration_fees` "));
		//~ if($check_last_regd_fee_num==0)
		//~ {
			//~ $regdd_fee=$check_regd_fee["regd_fee"];
		//~ }else
		//~ {
			//~ $check_last_regd_fee=mysqli_fetch_array($check_last_regd_fee_qry);
			//~ $dates_array = getDatesFromRange($check_last_regd_fee["date"], $date);
			//~ $day_diff=sizeof($dates_array);
			//~ if($day_diff<=$check_regd_fee["validity"])
			//~ {
				//~ $regdd_fee=0;
			//~ }else
			//~ {
				//~ $regdd_fee=$check_regd_fee["regd_fee"];
			//~ }
		//~ }
?>
	<table class="table custom_table">
		<input type="hidden" id="opd_allow_credit" value="<?php echo $center_info["allow_credit"]; ?>">
		<input type="hidden" id="opd_allow_credit_name" value="<?php echo $center_info["centrename"]; ?>">
		<tr>
			<th>Center</th>
			<td colspan="1">
				<select id="sel_center" onChange="sel_center_change('opd',this.value)" disabled>
				<?php
				$center_qry=mysqli_query($link, " SELECT * FROM `centremaster` WHERE `not_required`=0 order by `centreno` ");
				while($center=mysqli_fetch_array($center_qry))
				{
					if($pat_center["centreno"]==$center['centreno']){ $sel="selected"; }else{ $sel=""; }
					
					echo "<option value='$center[centreno]' $sel >$center[centrename]</option>";
				}
				?>
				</select>
			</td>
			<th>Session</th>
			<td colspan="1">
				<select id="doctor_session" onKeyUp="doctor_session_Up(event)">
					<option value="1" <?php if($current_sesssion==1){ echo "selected"; } ?> >Session 1</option>
					<option value="2" <?php if($current_sesssion==2){ echo "selected"; } ?> >Session 2</option>
					<option value="3" <?php if($current_sesssion==3){ echo "selected"; } ?> >Session 3</option>
				</select>
			</td>
		</tr>
		<tr>
			<th class="span3">Select Doctor</th>
			<td>
				<!--<input type="text" name="con_doc" id="con_doc" class="span3" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="" >-->
				<input type="text" name="ad_doc" id="ad_doc" class=" ad_doc" size="25" onFocus="adload_refdoc1()" onKeyUp="adload_refdoc(this.value,event)" onBlur="javascript:$('#adref_doc').fadeOut(500)" value="">
				<input type="text" name="con_doc_id" id="con_doc_id" style="display:none;" value="0">
				<div id="addoc_info"></div>
				<div id="adref_doc" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
						<th>Doctor ID</th>
						<th>Doctor Name</th>
						<?php
							$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `dept_id`='$dept_id' AND `status`='0' order by `Name` ");
							//$d=mysqli_query($link, " SELECT a.* FROM `consultant_doctor_master` a, `employee` b WHERE a.`emp_id`=b.`emp_id` AND a.`dept_id`='$dept_id' AND b.`status`='0' ORDER BY `Name` ");
							$i=1;
							while($d1=mysqli_fetch_array($d))
							{
								// Visit Fee
								$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `date` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$d1[consultantdoctorid]' AND `visit_fee`>0 order by `slno` DESC "));
								
								//$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `date` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `visit_fee`>0 order by `slno` DESC "));
								$check_last_visit_fee_date=$check_last_visit_fee["date"];
								if($check_last_visit_fee_date=="")
								{
									$visitt_fee=$d1["opd_visit_fee"];
									$visit_type_id=1;
								}else
								{
									$dates_array = getDatesFromRange($check_last_visit_fee_date, $date);
									$visit_fee_day_diff=sizeof($dates_array);
									if($visit_fee_day_diff<=$d1["opd_visit_validity"])
									{
										$visitt_fee=0;
										$visit_type_id=3;
									}else
									{
										$visitt_fee=$d1["opd_visit_fee"];
										$visit_type_id=6;
									}
								}
								
								// Regd Fee
								
								$check_last_regd_fee_qry=mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$d1[consultantdoctorid]' AND `regd_fee`>0 order by `slno` DESC limit 0,1 ");
								$check_last_regd_fee_num=mysqli_num_rows($check_last_regd_fee_qry);
								$check_regd_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_registration_fees` "));
								if($check_last_regd_fee_num==0)
								{
									$regdd_fee=$d1["opd_reg_fee"];
								}else
								{
									$check_last_regd_fee=mysqli_fetch_array($check_last_regd_fee_qry);
									$dates_array = getDatesFromRange($check_last_regd_fee["date"], $date);
									$day_diff=sizeof($dates_array);
									if($day_diff<=$d1["opd_reg_validity"])
									{
										$regdd_fee=0;
									}else
									{
										$regdd_fee=$d1["opd_reg_fee"];
									}
								}
								
								if($visitt_fee>0)
								{
									if($day_number==1) // Sunday=1, Monday=2,.....
									{
										$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_sunday_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]'"));
										if($check_extra)
										{
											$visitt_fee=$check_extra["opd_visit_fee"];
											$regdd_fee=$check_extra["opd_reg_fee"];
										}
									}
								}
								
								// Check Extra Visit Fee
								$not_session = array();
								$time_now=date("H:i:s");
								$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'>=`timer_one` AND '$time_now'<`timer_two` "));
								if($check_extra)
								{
									$visitt_fee=$check_extra["opd_visit_fee"];
								}else
								{
									array_push($not_session, 1); // Add Session One
									$not_session = join(',',$not_session);
									
									$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'>=`timer_one` AND `session` NOT IN($not_session) "));
									if($check_extra)
									{
										$visitt_fee=$check_extra["opd_visit_fee"];
									}else
									{
										$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'<=`timer_two` AND `session` NOT IN($not_session) "));
										if($check_extra)
										{
											$visitt_fee=$check_extra["opd_visit_fee"];
										}
									}
								}
								
						?>
							<tr onClick="addoc_load('<?php echo $d1['consultantdoctorid'];?>','<?php echo $d1['Name'];?>','<?php echo $visitt_fee;?>','<?php echo $d1['opd_visit_validity'];?>','<?php echo $regdd_fee;?>','<?php echo $d1['opd_reg_validity'];?>','<?php echo $visit_type_id;?>')" style="cursor:pointer" <?php echo "id=addoc".$i;?>>
								<td>
									<?php echo $d1['consultantdoctorid'];?>
								</td>
								<td>
									<?php echo $d1['Name'];?>
									<div <?php echo "id=addvdoc".$i;?> style="display:none;">
										<?php echo "#".$d1['consultantdoctorid']."#".$d1['Name']."#".$visitt_fee."#".$d1['opd_visit_validity']."#".$regdd_fee."#".$visit_type_id;?>
									</div>
								</td>
							</tr>
						<?php
							$i++;
							}
						?>
					</table>
				</div>
			</td>
			<th>Select Date</th>
			<td>
				<input class="datepicker" type="text" id="appoint_date" onKeyUp="appoint_date(event)" onChange="appoint_date_change()" value="<?php echo date("Y-m-d"); ?>" disabled >
			</td>
		</tr>
		<tr style="display:none;">
			<th>Emergency</th>
			<td>
				<label><input type="checkbox" id="pat_emergency" onChange="pat_emergency('1')" value="1"> Yes</label>
			</td>
			<th class="span3" style="display:;">Emergency Fee</th>
			<td style="display:;">
				<input type="hidden" id="emerg_fee" value="<?php echo $check_regd_fee["emerg_fee"]; ?>">
				<input type="text" id="emergency_fee" value="0" readonly>
			</td>
		</tr>
		<tr style="display:none;">
			<th>Cross Consultation</th>
			<td>
				<label><input type="checkbox" id="pat_cross_consult" onChange="pat_cross_consult()" value="1"> Yes</label>
			</td>
			<th class="span3" style="display:;">Cross Consultation Fee</th>
			<td style="display:;">
			<?php
				$current_hour=date("H");
				if($current_hour<2)
				{
					$cross_consult_fee="150";
				}else if($current_hour>2 || $current_hour<6)
				{
					$cross_consult_fee="200";
				}else
				{
					$cross_consult_fee="300";
				}
			?>
				<input type="hidden" id="cross_fee" value="<?php echo $cross_consult_fee; ?>">
				<input type="text" id="cross_consult_fee" value="0" readonly>
			</td>
		</tr>
		<tr style="display:;">
			<th class="span3">Visit Fee</th>
			<td>
				<input type="text" id="visit_fee" value="0" onKeyup="visit_fee_ch()" readonly>
				<br>
				<label style="display:none;">
					<input type="checkbox" id="revisit_check" onChange="revisit_check_ch(this.value)" value="0">
					Review
				</label>
				 &nbsp;&nbsp;
				<label>
					<input type="checkbox" id="emergency_check" onChange="emergency_check_ch(this.value)" value="0">
					Emergency
				</label>
			</td>
			<th>Regd Fee</th>
			<td>
				<input type="text" id="regd_fee" onKeyup="regd_fee_ch()" readonly value="<?php echo $regdd_fee; ?>">
				<input type="hidden" id="regdd_fee" value="<?php echo $regdd_fee; ?>">
			</td>
		</tr>
		<tr style="display:;">
			<th>Total</th>
			<td>
				<input type="text" id="total" readonly>
			</td>
			<th style="display:;">Discount</th>
			<td style="display:;">
				<input type="text" class="span1" id="dis_per" placeholder="%" onKeyUp="dis_per(this.value,event)" <?php echo $discount_element_disable; ?> >
				<input type="text" class="span1" id="dis_amnt" value="0" onKeyUp="dis_amnt(this.value,event)" <?php echo $discount_element_disable; ?> ><br>
				<span id="d_reason" style="display:none;"><input type="text" class="span2" id="dis_reason" placeholder="Reason for discount" onKeyUp="dis_reason(this.value,event)"></span>
			</td>
		</tr>
		<tr style="display:;">
			<th>Advance</th>
			<td>
				<input type="text" id="advance" onKeyUp="advance(this.value,event)"><br>
				<span id="b_reason" style="display:none;"><input type="text" id="bal_reason" placeholder="Reason for balance" onKeyUp="bal_reason(this.value,event)"></span>
			</td>
			<th>Balance</th>
			<td>
				<input type="text" id="balance" value="0" readonly>
			</td>
		</tr>
		<tr style="display:;">
			<th>Payment Mode</th>
			<td colspan="1">
				<select id="pay_mode" onkeyup="pay_mode(this.value,event)" onChange="pay_mode_change(this.value)">
				<?php
					$pay_mode_qry=mysqli_query($link, " SELECT `p_mode_id`,`p_mode_name` FROM `payment_mode_master` WHERE `status`=0 ORDER BY `sequence` ");
					while($pay_mode=mysqli_fetch_array($pay_mode_qry))
					{
						echo "<option value='$pay_mode[p_mode_name]' >$pay_mode[p_mode_name]</option>";
					}
				?>
				</select>
				<!--<select id="pay_mode" onkeyup="pay_mode(this.value,event)" onChange="pay_mode_change(this.value)">
					<option value="Cash">Cash</option>
					<option value="Card">Card</option>
					<option value="Cheque">Cheque</option>
					<option value="NEFT">NEFT</option>
					<option value="RTGS">RTGS</option>
					<option value="Credit">Credit</option>
				</select>-->
				<br>
				<input type="hidden" class="" id="cheque_ref_no" placeholder="Cheque / Reference No" onKeyUp="cheque_ref_no_up(this.value,event)">
			</td>
			<th>Patient Type</th>
			<td colspan="1">
				<select id="visit_type_id" onkeyup="visit_type_id(this.value,event)" onChange="visit_type_change(this.value)">
			<?php
				$visit_type_qry=mysqli_query($link, "SELECT `visit_type_id`, `visit_type_name` FROM `patient_visit_type_master` WHERE `p_type_id`=1 ORDER BY `visit_type_id`");
				while($visit_type=mysqli_fetch_array($visit_type_qry))
				{
					if($visit_type_id==$visit_type["visit_type_id"]){ $visit_sel="selected"; }else{ $visit_sel=""; }
					echo "<option value='$visit_type[visit_type_id]' $visit_sel>$visit_type[visit_type_name]</option>";
				}
			?>
				</select>
			</td>
		</tr>
		<tr style="display:none;">
			<th>Card Type</th>
			<td colspan="3">
				<select id="card_id" onkeyup="card_id(this.value,event)">
			<?php
				$card_val_qry=mysqli_query($link, "SELECT `card_id`, `card_name` FROM `card_type_master` ORDER BY `card_id`");
				while($card_val=mysqli_fetch_array($card_val_qry))
				{
					echo "<option value='$card_val[card_id]' $card_sel>$card_val[card_name]</option>";
				}
			?>
				</select>
			</td>
		</tr>
	</table>
	<center>
		<button class="btn btn-info" id="save" onClick="save_pat('save')">Save</button>
	</center>
<?php
	}
}
if($_POST["type"]=="show_con_doc")
{
	$uhid=$_POST["uhid"];
	$dept_id=$_POST["dept_id"];
	$dname=$_POST['val'];
	$edit_opd=$_POST['edit_payment'];
	
	$day_number=convert_date_to_day_num($date);
	
	$edit_consultantdoctorid=0;
	$edit_visit_fee=0;
	$edit_regd_fee=0;
	if($edit_opd>0)
	{
		$edit_appointment=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`visit_fee` FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$edit_opd' "));
		$edit_consultantdoctorid=$edit_appointment["consultantdoctorid"];
		
		$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$edit_opd' "));
		$edit_visit_fee=$con_pat_pay_detail["visit_fee"];
		$edit_regd_fee=$con_pat_pay_detail["regd_fee"];
	}
?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
		<th>Doctor Id</th><th>Doctor Name</th>
<?php
		if($dname)
		{
			$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `Name` like '%$dname%' and `dept_id`='$dept_id' AND `status`='0' order by `Name` ");
			//$d=mysqli_query($link, " SELECT a.* FROM `consultant_doctor_master` a, `employee` b WHERE a.`emp_id`=b.`emp_id` AND a.`dept_id`='$dept_id' AND b.`status`='0' AND a.`Name` like '%$dname%' ORDER BY `Name` ");
		}
		else
		{
			$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `dept_id`='$dept_id' AND `status`='0' order by `Name` ");
			//$d=mysqli_query($link, " SELECT a.* FROM `consultant_doctor_master` a, `employee` b WHERE a.`emp_id`=b.`emp_id` AND a.`dept_id`='$dept_id' AND b.`status`='0' ORDER BY `Name` ");
		}
		$i=1;
		while($d1=mysqli_fetch_array($d))
		{
			if($edit_consultantdoctorid==$d1['consultantdoctorid'])
			{
				$visitt_fee=$edit_visit_fee;
				$regdd_fee=$edit_regd_fee;
				
				$visit_type_info=mysqli_fetch_array(mysqli_query($link, " SELECT `visit_type_id` FROM `patient_visit_type_details` WHERE `patient_id`='$uhid' and `opd_id`='$edit_opd' "));
				
				$visit_type_id=$visit_type_info["visit_type_id"];
			}else
			{
				// Visit Fee
				$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$d1[consultantdoctorid]' AND `visit_fee`>0 order by `slno` DESC "));
				
				//$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `visit_fee`>0 order by `slno` DESC "));
				$check_last_visit_fee_date=$check_last_visit_fee["date"];
				if(!$check_last_visit_fee_date)
				{
					$visitt_fee=$d1["opd_visit_fee"];
					$visit_type_id=1;
				}else
				{
					$dates_array = getDatesFromRange($check_last_visit_fee_date, $date);
					$visit_fee_day_diff=sizeof($dates_array);
					if($visit_fee_day_diff<$d1["opd_visit_validity"])
					{
						$visitt_fee=0;
						$visit_type_id=0;
					}else
					{
						$visitt_fee=$d1["opd_visit_fee"];
						$visit_type_id=6;
					}
				}
				
				// Regd Fee
				$check_last_regd_fee_qry=mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' AND `consultantdoctorid`='$d1[consultantdoctorid]' AND `regd_fee`>0 order by `slno` DESC limit 0,1 "); // AND `dept_id`='$d1[dept_id]'
				$check_last_regd_fee_num=mysqli_num_rows($check_last_regd_fee_qry);
				$check_regd_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_registration_fees` "));
				if($check_last_regd_fee_num==0)
				{
					$regdd_fee=$d1["opd_reg_fee"];
				}else
				{
					$check_last_regd_fee=mysqli_fetch_array($check_last_regd_fee_qry);
					$dates_array = getDatesFromRange($check_last_regd_fee["date"], $date);
					$day_diff=sizeof($dates_array);
					if($day_diff<=$d1["opd_reg_validity"])
					{
						$regdd_fee=0;
					}else
					{
						$regdd_fee=$d1["opd_reg_fee"];
					}
				}
				
				if($visitt_fee>0)
				{
					if($day_number==1) // Sunday=1, Monday=2,.....
					{
						$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_sunday_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]'"));
						if($check_extra)
						{
							$visitt_fee=$check_extra["opd_visit_fee"];
							$regdd_fee=$check_extra["opd_reg_fee"];
						}
					}
				}
				
				// Check Extra Visit Fee
				$not_session = array();
				$time_now=date("H:i:s");
				$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'>=`timer_one` AND '$time_now'<`timer_two` "));
				if($check_extra)
				{
					$visitt_fee=$check_extra["opd_visit_fee"];
				}else
				{
					array_push($not_session, 1); // Add Session One
					$not_session = join(',',$not_session);
					
					$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'>=`timer_one` AND `session` NOT IN($not_session) "));
					if($check_extra)
					{
						$visitt_fee=$check_extra["opd_visit_fee"];
					}else
					{
						$check_extra=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_extra_fee` WHERE `consultantdoctorid`='$d1[consultantdoctorid]' AND '$time_now'<=`timer_two` AND `session` NOT IN($not_session) "));
						if($check_extra)
						{
							$visitt_fee=$check_extra["opd_visit_fee"];
						}
					}
				}
				
			}
		?>
			<tr onClick="addoc_load('<?php echo $d1['consultantdoctorid'];?>','<?php echo $d1['Name'];?>','<?php echo $visitt_fee;?>','<?php echo $d1['opd_visit_validity'];?>','<?php echo $regdd_fee;?>','<?php echo $d1['opd_reg_validity'];?>','<?php echo $visit_type_id;?>')" style="cursor:pointer" <?php echo "id=addoc".$i;?>>
				<td>
					<?php echo $d1['consultantdoctorid'];?>
				</td>
				<td>
					<?php echo $d1['Name'];?>
					<div <?php echo "id=addvdoc".$i;?> style="display:none;">
						<?php echo "#".$d1['consultantdoctorid']."#".$d1['Name']."#".$visitt_fee."#".$d1['opd_visit_validity']."#".$regdd_fee."#".$visit_type_id;?>
					</div>
				</td>
			</tr>
		<?php
			$i++;
		}
?>
	</table>
<?php
}
if($_POST["type"]=="show_print_details")
{
	$uhid=$_POST["uhid"];
	$pin=$_POST["pin"];
	$user=$_POST["user"];
	
	$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$pin' "));
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt_tm[type]' "));
	
	$edit_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_payment`,`cancel_pat` FROM `employee` WHERE `emp_id`='$user' "));
	
	$week = array("","Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"); 
	
	if($pin)
	{
		$appointment_info_qry=mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' ");
	}else
	{
		$appointment_info_qry=mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' order by `slno` DESC ");
	}
	if(!$pin)
	{
?>
	<!--<span class="text-right" style="margin-bottom: 5px;">
		<button class="btn btn-mini btn-success" onClick="new_appointment()"><i class="icon-calendar"></i> New Appointment</button>
	</span>-->
	<div class="accordion" id="collapse-group">
		
<?php
	}
	$tab=1;
	$tab_id=1;
	while($appointment_info=mysqli_fetch_array($appointment_info_qry))
	{
		$con_doc_info=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment_info[consultantdoctorid]' "));	
		$con_doc_avail_time_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `con_doc_available_time` WHERE `consultantdoctorid`='$appointment_info[consultantdoctorid]' "));
		$appoint_no=$appointment_info["appointment_no"];
		$appmnt_slno = str_pad($appoint_no,2,"0",STR_PAD_LEFT);
		
		$day_num=$appointment_info["appointment_day"];
		$day=$week[$day_num];
		
		$conslt=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$appointment_info[opd_id]' ) "));
		
		$conslt_pat_pay=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$appointment_info[opd_id]' "));
		
?>
		<div class="accordion-group widget-box">
			<div class="accordion-heading">
				<div class="widget-title">
					<a data-parent="#collapse-group" href="#collapseG<?php echo $tab; ?>" data-toggle="collapse" onClick="show_tr_btn('<?php echo $appointment_info['opd_id']; ?>','<?php echo $tab_id; ?>')" id="doc_tab<?php echo $tab_id; ?>">
						<span class="icon"><b><?php echo $prefix_det["prefix"]; ?>: <?php echo $appointment_info['opd_id']; ?></b></span> 
						<span class="icon"><b>Doctor: <?php echo $con_doc_info['Name']; ?></b></span> 
						<span class="icon"><b>Appointment Date : <?php echo convert_date($appointment_info["appointment_date"])."(".$day.")"; ?></b></span> 
						<span class="icon"><b>No: <?php echo $appmnt_slno; ?></b></span>
						<span class="text-right" style="padding:10px;font-size:18px;">
							<span class="iconp" id="plus_sign<?php echo $tab_id; ?>" style="float:right;"><i class="icon-plus"></i></span>
							<span class="iconm" id="minus_sign<?php echo $tab_id; ?>" style="float:right;display:none;"><i class="icon-minus"></i></span>
						</span>
					</a>
				</div>
			</div>
			<div class="collapse accordion-body" id="collapseG<?php echo $tab; ?>">
				<div class="widget-content hidden_div" id="<?php echo $tab_id; ?>" style="display:none;">
					<center>
						<!--<button class="btn btn-info" id="print_con_receipt" onClick="print_receipt('pages/print_rep.php?uhid=<?php echo $uhid."&visitid=".$appointment_info['opd_id']; ?>')">Print</button>
						<button class="btn btn-info" onClick="print_receipt('pages/patient_record_card.php?uhid=<?php echo $uhid."&visitid=".$appointment_info['opd_id']; ?>')">Patient Record Card</button>
						<button class="btn btn-info" onClick="print_receipt('pages/doctor_requisition_rep.php?uhid=<?php echo $uhid."&visitid=".$appointment_info['opd_id']; ?>')">Doctor Requisition Receipt</button>-->
						<button class="btn btn-print" id="print_con_receipt" onClick="print_receipt('pages/print_consulant_receipt.php?v=0&uhid=<?php echo $uhid."&opdid=".$appointment_info['opd_id']; ?>')"><i class="icon-print"></i> Consultation Receipt</button>
						<button class="btn btn-print" id="print_con_receipt" onClick="print_receipt('pages/print_consulant_receipt_bill.php?v=1&uhid=<?php echo $uhid."&opdid=".$appointment_info['opd_id']; ?>')"><i class="icon-print"></i> Bill</button>
						<button class="btn btn-print" onClick="print_receipt('pages/prescription_rpt.php?uhid=<?php echo $uhid."&opdid=".$appointment_info['opd_id']; ?>')"><i class="icon-print"></i> Prescription</button>
						<button class="btn btn-print print_assess lite" onClick="print_receipt('pages/opd_assessment.php?uhid=<?php echo $uhid."&opdid=".$appointment_info['opd_id']."&dept_id=".$con_doc_info['dept_id']; ?>')"><i class="icon-print"></i> Assessment Form</button>
						<?php if($edit_access["edit_payment"]==1)
						{
							$edit_btn_disable="";
							$title="";
							$not_approve_refund_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `refund_request` WHERE `patient_id`='$uhid'  and `opd_id`='$appointment_info[opd_id]' AND `status`='0' "));
							if($not_approve_refund_request)
							{
								$edit_btn_disable="disabled";
								$title="title='Refund request sent'";
							}
							$check_b_type_payment=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid'  and `opd_id`='$appointment_info[opd_id]' and `typeofpayment`='B' "));
							if($check_b_type_payment==0){
						?>
						<button class="btn btn-edit" onClick="edit_conslt('<?php echo $uhid; ?>','<?php echo $appointment_info['opd_id']; ?>','<?php echo $conslt['consultantdoctorid']; ?>','<?php echo $conslt['Name']; ?>','<?php echo $conslt_pat_pay['visit_fee']; ?>','<?php echo $conslt['opd_visit_validity']; ?>')" <?php echo $title." ".$edit_btn_disable ?>><i class="icon-edit"></i> Edit</button>
						<?php } } ?>
						<?php if($edit_access["cancel_pat"]=='1'){ ?>
						<!--<button id="cancel_pat" class="btn btn-delete" onClick="cancel_pat('<?php echo $uhid; ?>','<?php echo $appointment_info['opd_id']; ?>','doc')" ><i class="icon-remove"></i> Cancel</button>-->
						<?php } ?>
						<a href="processing.php?param=81" class="btn btn-new"><i class="icon-edit"></i> New Registration</a>
					</center>
				</div>
			</div>
		</div>
<?php
	$tab++;
	$tab_id++;
	}
?>
	</div>
<?php
}

/////////////////// Start Investigation /////////////////

if($_POST["type"]=="load_all_investigation")
{
	$uhid=$_POST["uhid"];
	$pin=$_POST["pin"];
	if($pin)
	{
		$this_pin_lab_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' AND `type`='2' "));
		if($this_pin_lab_num==0)
		{
			$q=" SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' ";
		}else
		{
			$q=" SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' AND `type`='2' ";
		}
	}else
	{
		$this_pin_lab_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `type`='2' "));
		if($this_pin_lab_num==0)
		{
			$q=" SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' ";
		}else
		{
			$q=" SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `type`='2' ";
		}
	}
	/*if($pin)
	{
		$q=" SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' ";
	}else
	{
		$q=" SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' order by `slno` DESC ";
	}*/
	$q.=" order by `slno` DESC ";
	//echo $q;
	$appointment_info_qry=mysqli_query($link, $q);
	if(!$pin)
	{
?>
<span class="text-right" style="margin-bottom: 5px;">
	<button class="btn btn-mini btn-success" onClick="load_add_test_form('0000','out_new')"><i class="icon-beaker"></i> New Test</button>
</span>
<?php } ?>
<div class="accordion" id="collapse-group">
<?php
	$tab=21;
	while($appointment_info=mysqli_fetch_array($appointment_info_qry))
	{
		$appmnt_book_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$appointment_info[opd_id]' "));
		
		$test_qry=mysqli_query($link, " SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$appointment_info[opd_id]' ");	
		$test_num=mysqli_num_rows($test_qry);
		$tot="";
		$appmnt_book_num=0;
		if($test_num>0 || $appmnt_book_num>0)
		{
			$all_test="";
			$n=1;
			while($test=mysqli_fetch_array($test_qry))
			{
				$tests=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$test[testid]' "));
				if($n==1)
				{
					$all_test=$tests["testname"];
				}else
				{
					$all_test.=", ".$tests["testname"];
				}
				$n++;
			}
		
?>

	<div class="accordion-group widget-box">
		<div class="accordion-heading">
			<div class="widget-title">
				<a data-parent="#collapse-group" href="#collapseG<?php echo $tab; ?>" data-toggle="collapse" onClick="load_add_test_form('<?php echo $appointment_info['opd_id']; ?>')">
					<span class="icon"><b>PIN: <?php echo $appointment_info['opd_id']; ?></b></span> 
					<span class="icon"><b>Test(s): <?php echo substr($all_test,0,100)."......"; ?></b></span>
				</a>
			</div>
		</div>
	</div>
<?php
		}
	$tab++;
	}
?>
</div>
<div id="msg" align="center"></div>
<?php
}
if($_POST["type"]=="load_test_details")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	
	$testresult_path_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	$testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_card` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	$testresult_radi_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_rad` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	$testresult_wild_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `widalresult` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	$testresult_summ_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	
	if($testresult_path_num>0 || $testresult_card_num>0 || $testresult_radi_num>0 || $testresult_wild_num>0 || $testresult_summ_num>0)
	{
		if($emp_info["levelid"]==1) // if admin
		{
			$test_del_btn="<td onclick='delete_rows(this,2)'>Remove</td>";
		}else
		{
			$test_del_btn="<td>Reporting Done</td>";
		}
	}else
	{
		$test_del_btn="<td onclick='delete_rows(this,2)'>Remove</td>";
	}
	
	//------------Loading Test Details---------------------
	$j=1;
	$test_add="<table class='table table-bordered' id='test_list'>";	
	$test_add.="<tr><th colspan='3' style='background-color:#cccccc'>Tests</th><th style='text-align:right;background-color:#cccccc'>Total:<span id='test_total'></span></th></tr>";
	$p_test=mysqli_query($link, " SELECT `testid`,`test_rate` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	while($t_tab=mysqli_fetch_array($p_test))
	{
		$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$t_tab[testid]'"));
		
		//$test_add.="<tr><td>$j</td><td>$tname[testname]<input type='hidden' value='$t_tab[testid]' class='test_id'/></td><td contentEditable='true' onkeyup='load_cost(2)'><span class='test_f'>$t_tab[test_rate]</span></td><td onclick='delete_rows(this,2)'>Remove</td></tr>";
		
		$test_add.="<tr><td>$j</td><td>$tname[testname]<input type='hidden' value='$t_tab[testid]' class='test_id'/></td><td contentEditable='true' onkeyup='load_cost(2)'><span class='test_f'>$t_tab[test_rate]</span></td>$test_del_btn</tr>";
		
		$j++;
	}
	$test_add.="</table>";

	//------------Loading Test Details End---------------------
	echo $test_add;
	//~ //$vaccu_charge=mysqli_fetch_array(mysqli_query($link, " SELECT `vaccu_charge` FROM `company_name` "));
	//~ $vaccu_charge_qry=mysqli_query($link, " SELECT `vacu_charge` FROM `centremaster` WHERE `centreno` in ( SELECT `center_no` FROM `patient_info` WHERE `patient_id`='$uhid' ) ");
	//~ //$vaccu_charge_num=mysqli_num_rows($vaccu_charge_qry);
	//~ $vaccu_charge_vl=mysqli_fetch_array($vaccu_charge_qry);
	//~ if($vaccu_charge_vl['vacu_charge']==0)
	//~ {
		//~ $vaccu_charge_val=mysqli_fetch_array(mysqli_query($link, " SELECT `vaccu_charge` FROM `company_name` "));
		//~ $vaccu_charge=$vaccu_charge_val["vaccu_charge"];
	//~ }else
	//~ {
		//~ $vaccu_charge=$vaccu_charge_vl["vacu_charge"];
	//~ }
	?> <!--<input type="" id="vaccu_charge" value="<?php echo $vaccu_charge; ?>"/>--> <?php
}
if($_POST["type"]=="load_test_form")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$user=$_POST["user"];
	
	
	$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt_tm[type]' "));
	
	$pat_card_info=mysqli_fetch_array(mysqli_query($link, " SELECT `card_id` FROM `patient_card_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
	$sel_card_id=$pat_card_info["card_id"];
	
	$ref_docs="";
	$centers="0";
	
	$if_credit_disable="";
	
	$discount_reason_hide="display:none;";
	$balance_reason_hide="display:none;";
	
	$check_last_regd_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' order by `slno` DESC limit 0,1 "));
	$check_last_regd_fee["date"];
	if($opd_id=="0000")
	{
		$tst="New";
		$btn_name="Save";
		$dis_amnt="0";
		$advance="0";
		$balance="0";
		
		$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centreno`,`credit_limit`,`allow_credit` FROM `centremaster` WHERE `centreno` IN( SELECT `centreno` FROM `patient_source_master` WHERE `source_id` IN(SELECT `source_id` FROM `patient_other_info` WHERE `patient_id`='$uhid') ) "));
		if($centre_info["allow_credit"]==1)
		{
			$dis_credit="";
			$sel_credit="selected";
			$credit_limit=$centre_info["credit_limit"];
		}else
		{
			$dis_credit="style='display:none;'";
			$sel_credit="";
			$credit_limit=0;
		}
		
		$regdd_fee=0;
		$center_discount=$centre_info["c_discount"];
		$centers=$centre_info["centreno"];
	}else
	{
		$tst=$prefix_det["prefix"].": ".$opd_id;
		$pat_pay_detail_qry=mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$pat_pay_detail_num=mysqli_num_rows($pat_pay_detail_qry);
		if($pat_pay_detail_num==0)
		{
			$btn_name="Save";
			$dis_amnt="0";
			$advance="0";
			$balance="0";
			
			$regdd_fee=0;
			$mr_dis="";
			
			$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centreno`,`credit_limit`,`allow_credit` FROM `centremaster` WHERE `centreno` IN( SELECT `centreno` FROM `patient_source_master` WHERE `source_id` IN(SELECT `source_id` FROM `patient_other_info` WHERE `patient_id`='$uhid') ) "));
			if($centre_info["allow_credit"]==1)
			{
				$dis_credit="";
				$sel_credit="selected";
				$credit_limit=$centre_info["credit_limit"];
			}else
			{
				$dis_credit="style='display:none;'";
				$sel_credit="";
				$credit_limit=0;
			}
			$center_discount=$centre_info["c_discount"];
			$centers=$centre_info["centreno"];
		}else
		{
			$btn_name="Update";
			$pat_pay_detail=mysqli_fetch_array($pat_pay_detail_qry);
			$dis_amnt=trim($pat_pay_detail["dis_amt"]);
			$advance=$pat_pay_detail["advance"];
			$balance=trim($pat_pay_detail["balance"]);
			$regdd_fee=$pat_pay_detail["regd_fee"];
			$center_discount=$pat_pay_detail["dis_per"];
			
			if($dis_amnt>0)
			{
				$discount_reason_hide="";
			}
			if($balance>0)
			{
				$balance_reason_hide="";
			}
			
			if(!$center_discount)
			{
				$center_discount=0;
			}
			if($pat_pay_detail["balance"]==0)
			{
				$mr_dis="";
			}else
			{
				$mr_dis="disabled";
			}
			//~ $p_mode=mysqli_fetch_array(mysqli_query($link, " SELECT `payment_mode` FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			$p_mode=mysqli_fetch_array(mysqli_query($link, " SELECT `payment_mode`,`cheque_ref_no` FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and payment_mode!='Credit' and `typeofpayment`='A' "));
			if(!$p_mode)
			{
				$p_mode=mysqli_fetch_array(mysqli_query($link, " SELECT `payment_mode` FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			}
			if($p_mode["payment_mode"]=="Credit")
			{
				$sel_credit="selected";
				$if_credit_disable="disabled";
			}else
			{
				$allow_credit=mysqli_fetch_array(mysqli_query($link, " SELECT `credit_limit`,`allow_credit` FROM `centremaster` WHERE `centreno` in ( SELECT `center_no` FROM `patient_info` WHERE `patient_id`='$uhid' ) "));
				if($allow_credit["allow_credit"]==1)
				{
					$dis_credit="";
					$sel_credit="selected";
					$credit_limit=$allow_credit["credit_limit"];
				}else
				{
					$dis_credit="style='display:none;'";
					$sel_credit="";
					$credit_limit=0;
				}
			}
		}
		$uhid_opdid=mysqli_fetch_array(mysqli_query($link, " SELECT a.`refbydoctorid`,a.`center_no`,b.`ref_name`,c.`centrename` FROM `uhid_and_opdid` a, `refbydoctor_master` b, `centremaster` c WHERE a.`refbydoctorid`=b.`refbydoctorid` AND a.`center_no`=c.`centreno` AND a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id' "));
		$ref_docs=$uhid_opdid["ref_name"]."-".$uhid_opdid["refbydoctorid"];
		$centers=$uhid_opdid["center_no"];
	}
	$check_edit_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_payment`,`cancel_pat` FROM `employee` WHERE `emp_id`='$user' "));
	if($btn_name=="Save")
	{
		$edit_dis="";	
	}else
	{
		//$check_edit_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_payment` FROM `employee` WHERE `emp_id`='$user' "));
		if($check_edit_access["edit_payment"]=='1')
		{
			$edit_dis="";
		}else
		{
			$edit_dis="disabled";
		}
	}
	//$check_b_type_payment=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' and `typeofpayment`='B' "));
	//if($check_b_type_payment!=0){ $edit_dis="disabled"; }
	$testresult_path_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	$testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_card` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	$testresult_radi_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_rad` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	$testresult_wild_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `widalresult` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' "));
	
	$dis_apprv=mysqli_fetch_array(mysqli_query($link, " SELECT `approve_by` FROM `discount_approve` WHERE `patient_id`='$uhid'  AND `pin`='$opd_id' "));
	if($dis_apprv["approve_by"]>0)
	{
		echo "<input type='hidden' value='1' id='discount_appr'>";
	}else
	{
		echo "<input type='hidden' value='1' id='discount_appr'>";
	}
	if($testresult_path_num>0 || $testresult_card_num>0 || $testresult_radi_num>0 || $testresult_wild_num>0 || $dis_apprv["approve_by"]>0)
	{
		//$edit_dis="disabled";
	}
	
	$vaccu_charge_qry=mysqli_query($link, " SELECT `vacu_charge` FROM `centremaster` WHERE `centreno`='C100' ");
	$vaccu_charge_vl=mysqli_fetch_array($vaccu_charge_qry);
	if($vaccu_charge_vl['vacu_charge']==0)
	{
		$vaccu_charge_val=mysqli_fetch_array(mysqli_query($link, " SELECT `vaccu_charge` FROM `company_name` "));
		$vaccu_charge=$vaccu_charge_val["vaccu_charge"];
	}else
	{
		$vaccu_charge=$vaccu_charge_vl["vacu_charge"];
	}
	
	$credit_limit=999999999;
	
	$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$uhid'  AND `opd_id`='$opd_id' AND `type`='2' "));
?>
	<input type="hidden" id="center_discount" value="<?php echo $center_discount; ?>">
	<div id="msg" align="center"></div>
	<input type="hidden" id="vaccu_charge" value="<?php echo $vaccu_charge; ?>"/>
	<div id="test_sel">
		<div id="list_all_test" class="up_div"></div>
		<div id="list_all_extra" class="up_div"></div>
		<h5 class="text-left" onClick="load_tab(2,'a')">Test Details For  <?php echo $tst; ?></h5><br>
		<table class="table">
			<tr>
				<th>Referred By</th>
				<td colspan="1">
					<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="labref_load_refdoc1()" onKeyUp="labref_load_refdoc(this.value,event,'lab')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_docs; ?>" />
					
					<button class="btn btn-new btn-mini" name="new_doc" id="new_doc" value="New" onClick="load_new_ref_doc()"><i class="icon-edit"></i> New</button>
					<div id="doc_info"></div>
					<div id="ref_doc" align="center">
						<table style="background-color:#FFF;" class="table table-bordered table-condensed" id="center_table">
							<th>Doctor ID</th>
							<th>Doctor Name</th>
							<?php
								$d=mysqli_query($link, "select * from refbydoctor_master where refbydoctorid='101' order by ref_name");
								$i=1;
								while($d1=mysqli_fetch_array($d))
								{
							?>
								<tr onClick="labdoc_load('<?php echo $d1['refbydoctorid'];?>','<?php echo $d1['ref_name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
									<td>
										<?php echo $d1['refbydoctorid'];?>
									</td>
									<td>
										<?php echo $d1['ref_name'];?>
										<div <?php echo "id=dvdoc".$i;?> style="display:none;">
											<?php echo "#".$d1['refbydoctorid']."#".$d1['ref_name'];?>
										</div>
									</td>
								</tr>
							<?php
								$i++;
								}
							?>
						</table>
					</div>
				</td>
				<th>Center</th>
				<td colspan="">
					<select id="sel_center" onKeyup="sel_center(event,'lab')" onchange="sel_center_lab_change(this)">
					<?php
					$center_qry=mysqli_query($link, " SELECT * FROM `centremaster` order by `centreno` ");
					while($center=mysqli_fetch_array($center_qry))
					{
						if($center['centreno']==$centers){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$center[centreno]' $sel >$center[centrename]</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="test">Select Test</label></th>
				<td colspan="3">
					<input type="text" name="test" id="test" onFocus="test_enable()" onKeyUp="select_test_new(this.value,event)"/>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div id="test_d">
						
					</div>
				</td>
			</tr>
		</table>
		
		<div id="list_all_grtotal" style="display:">
			<table class="table table-bordered table-condensed" id='grtotal_list'>
				<tr onClick="load_tab(2,'a')">
					<th>Diagnostics</th>
					<th><span id="grtest"></span></th>
				</tr>
				<tr onClick="load_tab(1,'a')">
					<th>Extra</th>
					<th><span id="grextra"></span></th>
				</tr>
				<tr class="g_tot">
					<th>Grand Total</th>
					<th><span id="grtotal"></span></th>
				</tr>
				
			</table>
		</div>
		<p style="display:;"><b>Payment Summary</b></p>
		<table class="table" style="display:;">
			<tr>
				<th style="display:none;">Regd Fee</th>
				<td style="display:none;">
					<input type="text" id="regd_fee" value="<?php echo $regdd_fee; ?>" readonly>
				</td>
				<th>Total</th>
				<td>
					<input type="text" id="total" readonly>
				</td>
				<th>Discount</th>
				<td>
					<input type="text" class="span1" id="dis_per" placeholder="%" onKeyUp="dis_per(this.value,event)" <?php echo $discount_element_disable; ?>>%
					<input type="text" class="span1" id="dis_amnt" value="<?php echo $dis_amnt; ?>" onKeyUp="dis_amnt(this.value,event)" <?php echo $discount_element_disable; ?>>INR<br>
					<span id="d_reason" style="<?php echo $discount_reason_hide; ?>"><input type="text" class="span2" id="dis_reason" placeholder="Reason for discount" onKeyUp="dis_reason(this.value,event)" value="<?php echo $pat_pay_detail["dis_reason"]; ?>"></span>
				</td>
			</tr>
			<tr>
				<th>Advance</th>
				<td>
					<input type="text" id="advance" onKeyUp="advance(this.value,event)" value="<?php echo $advance; ?>" <?php echo $if_credit_disable; ?> ><br>
					<span id="b_reason" style="<?php echo $balance_reason_hide; ?>"><input type="text" id="bal_reason" placeholder="Reason for balance" onKeyUp="bal_reason(this.value,event)" value="<?php echo $pat_pay_detail["bal_reason"]; ?>"></span>
				</td>
				<th>Balance</th>
				<td>
					<input type="text" id="balance" value="<?php echo $balance; ?>" readonly>
				</td>
			</tr>
			<tr>
				<th>Payment Mode</th>
				<td colspan="3">
					<select id="pay_mode" onkeyup="pay_mode_lab(this.value,event)" onChange="pay_mode_change_lab(this.value)">
				<?php
					$pay_mode_qry=mysqli_query($link, " SELECT `p_mode_id`,`p_mode_name` FROM `payment_mode_master` WHERE `status`=0 ORDER BY `sequence` ");
					while($pay_mode=mysqli_fetch_array($pay_mode_qry))
					{
						if($pay_mode["p_mode_name"]==$p_mode["payment_mode"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$pay_mode[p_mode_name]' $sel >$pay_mode[p_mode_name]</option>";
					}
				?>
					</select>
					<!--<select id="pay_mode" onkeyup="pay_mode_lab(this.value,event)" onChange="pay_mode_change_lab(this.value)">
						<option value="Cash" <?php if($p_mode["payment_mode"]=="Cash"){ echo "selected"; } ?> >Cash</option>
						<option value="Card" <?php if($p_mode["payment_mode"]=="Card"){ echo "selected"; } ?>>Card</option>
						<option value="Cheque" <?php if($p_mode["payment_mode"]=="Cheque"){ echo "selected"; } ?>>Cheque</option>
						<option value="NEFT" <?php if($p_mode["payment_mode"]=="NEFT"){ echo "selected"; } ?>>NEFT</option>
						<option value="RTGS" <?php if($p_mode["payment_mode"]=="RTGS"){ echo "selected"; } ?>>RTGS</option>
						<option value="Credit" <?php if($p_mode["payment_mode"]=="Credit"){ echo "selected"; } ?>>Credit</option>
					</select>-->
					<br>
				<?php
					$cheque_ref_type="hidden";
					$p_mode_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_master` WHERE `p_mode_name`='$p_mode[payment_mode]' "));
					if($p_mode_info && $p_mode_info["ref_field"]==0)
					{
						$cheque_ref_type="text";
					}
				?>
					<input type="<?php echo $cheque_ref_type; ?>" class="" id="cheque_ref_no_lab" value="<?php echo $p_mode["cheque_ref_no"]; ?>" placeholder="Cheque / Reference No" onKeyUp="cheque_ref_no_lab_up(this.value,event)">
					<input type="hidden" value="<?php echo $credit_limit; ?>" id="credit_limit">
				</td>
			</tr>
			<tr style="display:none;">
				<th>Card Type</th>
				<td colspan="3">
					<select id="card_id" onkeyup="card_id_lab(this.value,event)">
				<?php
					$card_val_qry=mysqli_query($link, "SELECT `card_id`, `card_name` FROM `card_type_master` ORDER BY `card_id`");
					while($card_val=mysqli_fetch_array($card_val_qry))
					{
						if($sel_card_id==$card_val["card_id"]){ $card_sel="selected"; }else{ $card_sel=""; }
						echo "<option value='$card_val[card_id]' $card_sel>$card_val[card_name]</option>";
					}
				?>
					</select>
				</td>
			</tr>
		</table>
<?php if(!$cancel_request){ ?>
		<center>
			
			<button class="btn btn-save" id="save" value="<?php echo $btn_name; ?>" onClick="save_test('<?php echo $btn_name; ?>','<?php echo $opd_id; ?>')" <?php echo $edit_dis; ?>><i class="icon-save"></i> <?php echo $btn_name; ?></button>
		
		<?php if($pat_pay_detail_num>0){ ?>
			<!--<button id="print_receipt" class="btn btn-print" onClick="print_receipt('pages/print_opd_only_test.php?uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')"><i class="icon-print"></i> Print</button>-->
			<!--<button id="print_receipt" class="btn btn-print" onClick="print_receipt('pages/print_opd_test.php?uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')">Print</button>-->
			<button id="print_receipt" class="btn btn-print" onClick="money_receipt('pages/cash_memo_lab.php?v=0&uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')" ><i class="icon-print"></i> Receipt</button>
			<button id="print_receipt" class="btn btn-print" onClick="money_receipt('pages/cash_memo_lab_bill.php?v=1&uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')" ><i class="icon-print"></i> Bill</button>
			<!--<button id="print_receipt" class="btn btn-print" onClick="money_receipt('pages/monyrecpt_rpt.php?uhid=<?php echo $uhid."&opdid=".$opd_id; ?>')" <?php echo $mr_dis; ?>><i class="icon-print"></i> Money Receipt</button>-->
		<?php } ?>
		<?php if($btn_name=="Update"){?>
		<!--<input type="button" id="print" class="btn btn-print" value="Print Ind." onClick="load_test_print('<?php echo $uhid; ?>','<?php echo $opd_id; ?>')"/>-->
		<?php } ?>
		<!--<a href="processing.php?param=82" class="btn btn-new"><i class="icon-edit"></i> New Registration</a>-->
		<button class="btn btn-new" onclick="load_new_reg()"><i class="icon-edit"></i> New Registration</button>
		<?php if($check_edit_access["cancel_pat"]=='1' && $btn_name=="Update"){ ?>
		<button id="cancel_pat" class="btn btn-delete" onClick="cancel_pat('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','test')" ><i class="icon-remove"></i> Cancel</button>
		<?php } ?>
		</center>
<?php }else{
	echo "<center><h4 id='lab_cancel_request_msg'>Cancel request has been sent.</h4></center>";
}
?>
	</div>
	<span style="display:none;" id="this_opd"><?php echo $opd_id; ?></span>
<?php
}

if($_POST["type"]=="labdoctor")
{
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from lab_doctor  where name like '$val%'";
	}
	else
	{
		$q="select * from lab_doctor  order by name";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
	<table class="table table-striped table-condensed table-bordered">
		<tr>
			<th class="span1">#</th>
			<th>Doctor Name</th>
			<th>Delete</th>
		</tr>
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td style="width: 85%;"><?php echo $qrpdct1['name'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><button class="btn btn-mini btn-danger"><i class="icon-remove"></i></button></a></td>
	</tr>
<?php	
	$i++;
	}
?>
	</table>
<?php
}
if($_POST["type"]=="labdoctor_load") ////for Lab Doctor Master
{
	$tid=$_POST['docid'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from lab_doctor where id='$tid' "));
	$val=$tid.'@'.$qrm['sequence'].'@'.$qrm['category'].'@'.$qrm['name'].'@'.$qrm['desig'].'@'.$qrm['qual'].'@'.$qrm['phn'].'@'.$qrm['password'];
	echo $val;
}
if($_POST["type"]=="vaccumaster") ////for Vaccu Master
{
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from vaccu_master  where type like '$val%'";
	}else
	{
		$q="select * from vaccu_master  order by type";
	}
	$qrpdct=mysqli_query($link, $q);
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
			<td><?php echo $qrpdct1['type'];?></td>
			<td><?php echo $qrpdct1['rate'];?></td>
			<td><span onClick="delete_data('<?php echo $qrpdct1['id'];?>')"><img height="15" width="15" src="../images/delete.ico"/></span></td>
			<!--<td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"> <img height="15" width="15" src="../images/delete.ico"/></a></td>-->
		</tr>	
<?php	
	$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="vaccumaster_load") ////for Vaccu Master Load
{
	$cid=$_POST['doid1'];
	 
	 $qttl=mysqli_fetch_array(mysqli_query($link, "select * from vaccu_master where id='$cid' "));
	 $val=$cid.'@'.$qttl['type'].'@'.$qttl['rate'];
	 echo $val;
}
if($_POST["type"]=="vaccumaster_load_id") ////for Vaccu Master Load ID
{
	echo $vid=nextId("","vaccu_master","id","1");
}
if($_POST["type"]=="samplemastr") ////for Sample Master
{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from Sample  where Name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from Sample  order by Name";
	 }
	$qrpdct=mysqli_query($link, $q);
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
	 <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ID'];?>')" id="rad_test<?php echo $i;?>">
         <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ID'];?></td>
		 <td style="width: 85%;"><?php echo $qrpdct1['Name'];?></td>
		 <td><span onClick="delete_data('<?php echo $qrpdct1['ID'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></span></td>
	 </tr>	
<?php	
   $i++;
   }
?>
</table>
<?php
}
if($_POST["type"]=="samplemastr_load") ////for Sample Master Load
{
	 $cid=$_POST['doid1'];
	 
	 $qttl=mysqli_fetch_array(mysqli_query($link, "select * from Sample where ID='$cid' "));
	 $val=$cid.'@'.$qttl['Name'];
	 echo $val;
}
if($_POST["type"]=="samplemastr_load_id") ////for Sample Master Load ID
{
	echo $vid=nextId("","Sample","ID","1");
}
if($_POST["type"]=="testmethod") //// testmethod
{
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from test_methods  where name like '$val%'";
	}
	else
	{
		$q="select * from test_methods  order by name";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
			<td style="width: 85%;"><?php echo $qrpdct1['name'];?></td>
			<td><span onclick="delete_data('<?php echo $qrpdct1['id'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
		</tr>	
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="testmethod_id") //// testmethod ID
{
	echo $vid=nextId("","test_methods","id","1");
}
if($_POST["type"]=="testmethod_load") //// testmethod Load
{
	$doid1=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from test_methods where id='$doid1' "));
	
	$val=$doid1.'@'.$qrm['name'];
	echo $val;
}
if($_POST["type"]=="resultoption") //// resultoption
{
	$mrid=$_POST['mkid'];	
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from ResultOption  where name like '$val%'";
	}
	else
	{
		$q="select * from ResultOption  order by name";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
			<td style="width: 85%;"><?php echo $qrpdct1['name'];?></td>
			<td><span onclick="delete_data('<?php echo $qrpdct1['id'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
		</tr>	
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="resultoption_id") //// resultoption ID
{
	echo $vid=nextId("","ResultOption","id","1");
}
if($_POST["type"]=="resultoption_load") //// resultoption Load
{
	$tid=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select name from ResultOption where id='$tid' "));
	
	$val=$tid.'@'.$qrm['name'];
	echo $val;
}
if($_POST["type"]=="option") //// option
{
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from Options  where name like '$val%'";
	}
	else
	{
		$q="select * from Options  order by name";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
			<td style="width: 85%;"><?php echo $qrpdct1['name'];?></td>
			<td><span onclick="delete_data('<?php echo $qrpdct1['id'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
		</tr>	
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="option_id") //// option ID
{
	echo $vid=nextId("","Options","id","1");
}
if($_POST["type"]=="option_load") //// option Load
{
	$tid=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$tid' "));
	$val=$tid.'@'.$qrm['name'];
	echo $val;
}
if($_POST["type"]=="ungrpoptionlist") //// Options Link Master
{
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from Options  where  name like '%$val%'";
	}
	else
	{
		$q="select * from Options  order by name";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
			<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['id'];?>"/></td>
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
			<td><?php echo $qrpdct1['name'];?></td>
		</tr>	
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="grpoptionlist") //// Options Link Master
{
	$mrid=$_POST['mkid'];
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from Options  where id in(select optionid from ResultOptions where id='$mrid') and  name like '$val%'";
	}
	else
	{
		$q="select * from Options where id in(select optionid from ResultOptions where id='$mrid') order by name";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
			<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['id'];?>"/></td>
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
			<td><?php echo $qrpdct1['name'];?></td>
		</tr>	
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="optionmasterlink_id") //// Options Link Master ID
{
	echo $vid=nextId("","Options","id","1");
}

if($_POST["type"]=="cleaning_item_id")
{
	echo $vid=nextId("","cleaning_item_master","item_id","101");
}
if($_POST["type"]=="cleaning_item")
{
	$val=$_POST['val'];
	if($val)
	{
		$q=" SELECT * FROM `cleaning_item_master` WHERE `item_name` like '$val%' order by `item_id` ";
	}else
	{
		$q=" SELECT * FROM `cleaning_item_master` order by `item_id` ";
	}
	$qrpdct=mysqli_query($link, $q);
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_id'];?></td>
			<td><?php echo $qrpdct1['item_name'];?></td>
			<td><span onClick="delete_data('<?php echo $qrpdct1['item_id'];?>')"><img height="15" width="15" src="../images/delete.ico"/></span></td>
		</tr>	
<?php	
	$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="cleaning_item_load")
{
	$item_id=$_POST['doid1'];
	 
	 $qttl=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cleaning_item_master` WHERE `item_id`='$item_id' "));
	 $val=$item_id.'@'.$qttl['item_name'];
	 echo $val;
}

if($_POST["type"]=="cleaning_material_id")
{
	echo $vid=nextId("","cleaning_material_master","item_mat_id","101");
}
if($_POST["type"]=="cleaning_material")
{
	$val=$_POST['val'];
	if($val)
	{
		$q=" SELECT * FROM `cleaning_material_master` WHERE `item_mat_name` like '$val%' order by `item_mat_id` ";
	}else
	{
		$q=" SELECT * FROM `cleaning_material_master` order by `item_mat_id` ";
	}
	$qrpdct=mysqli_query($link, $q);
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['item_mat_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['item_mat_id'];?></td>
			<td><?php echo $qrpdct1['item_mat_name'];?></td>
			<td><span onClick="delete_data('<?php echo $qrpdct1['item_mat_id'];?>')"><img height="15" width="15" src="../images/delete.ico"/></span></td>
		</tr>	
<?php	
	$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="cleaning_material_load")
{
	$item_id=$_POST['doid1'];
	 
	 $qttl=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cleaning_material_master` WHERE `item_mat_id`='$item_id' "));
	 $val=$item_id.'@'.$qttl['item_mat_name'];
	 echo $val;
}
if($_POST["type"]=="cleaning_area_master")
{
?>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th class="span2">Area Name</th>
			<td>
				<input type="text" id="area_name" class="span5">
			</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="1">
				<button class="btn btn-info" onClick="save_area()">Save</button>
				<button class="btn btn-danger" onClick="cancel_area()">Cancel</button>
			</td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="cleaning_area_load")
{
	$area_id=$_POST['area_id'];
	if($area_id>0)
	{
	
	$area_name=mysqli_fetch_array(mysqli_query($link, " SELECT `area_name` FROM `cleaning_area_master` WHERE `area_id`='$area_id' "));
?>
	<table class="table" id="cleaning_area_details">
		<tr>
			<th class="span2">Selected Area: </th>
			<td colspan="5">
				<input type="hidden" id="sel_area_id" value="<?php echo $area_id; ?>">
				<input class="span4" type="text" id="sel_area" value="<?php echo $area_name["area_name"]; ?>" onKeyup="update_area(this.value,event)">
			</td>
		</tr>
		<tr id="row" class="area_item_mat_row">
			<th class="span2">Item</th>
			<td>
				<select class="" id="item_id" name="item_id">
					<option value="0">Select</option>
					<?php
						$item_qry=mysqli_query($link," SELECT * FROM `cleaning_item_master` order by `item_id` ");
						while($item=mysqli_fetch_array($item_qry))
						{		
							echo "<option value='$item[item_id]'>$item[item_name]</option>";
						}
						?>
				</select>
			</td>
			<th>Material</th>
			<td>
				<select class="" id="item_mat_id" name="item_mat_id">
					<option value="0">Select</option>
					<?php
						$item_mat_qry=mysqli_query($link," SELECT * FROM `cleaning_material_master` order by `item_mat_id` ");
						while($item_mat=mysqli_fetch_array($item_mat_qry))
						{		
							echo "<option value='$item_mat[item_mat_id]'>$item_mat[item_mat_name]</option>";
						}
						?>
				</select>
			</td>
			<th>Frequency</th>
			<td>
				<select class="" id="frequency" name="frequency">
					<option value="0">Select</option>
					<option value="1">Once a day</option>
					<option value="2">Twice a day</option>
					<option value="3">Thice a day</option>
					<option value="7">Once a week</option>
					<option value="30">Once a month</option>
				</select>
				<button class="btn btn-info" onClick="save_area_info()">Save</button>
			</td>
		</tr>
	</table>
<?php
	}
}
if($_POST["type"]=="cleaning_area_added_data_load")
{
	$area_id=$_POST["area_id"];
	if($area_id==0)
	{
		$q_qry=mysqli_query($link, " SELECT * FROM `cleaning_area` order by `slno` DESC ");
	}else
	{
		$q_qry=mysqli_query($link, " SELECT * FROM `cleaning_area` WHERE `area_id`='$area_id' order by `slno` DESC ");
	}
	$q_num=mysqli_num_rows($q_qry);
	if($q_num>0)
	{
?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>Area Name</th>
			<th>Item Name</th>
			<th>Material Name</th>
			<th>Frequency</th>
		</tr>
<?php
	$i=1;
	while($q=mysqli_fetch_array($q_qry))
	{
		$area_name=mysqli_fetch_array(mysqli_query($link, " SELECT `area_name` FROM `cleaning_area_master` WHERE `area_id`='$q[area_id]' "));
		$item_name=mysqli_fetch_array(mysqli_query($link, " SELECT `item_name` FROM `cleaning_item_master` WHERE `item_id`='$q[item_id]' "));
		$item_mat_name=mysqli_fetch_array(mysqli_query($link, " SELECT `item_mat_name` FROM `cleaning_material_master` WHERE `item_mat_id`='$q[item_mat_id]' "));
		$freq="";
		if($q["frequency"]==1)
		{
			$freq="Once A day";
		}
		if($q["frequency"]==2)
		{
			$freq="Twice A day";
		}
		if($q["frequency"]==3)
		{
			$freq="Thice A day";
		}
		if($q["frequency"]==7)
		{
			$freq="Once A week";
		}
		if($q["frequency"]==30)
		{
			$freq="Once A month";
		}
?>
	<tr onClick="load_selected_area_data('<?php echo $q['item_id']; ?>','<?php echo $q['item_mat_id']; ?>','<?php echo $q['frequency']; ?>')" style="cursor:pointer;">
		<td><?php echo $i; ?></td>
		<td><?php echo $area_name["area_name"]; ?></td>
		<td><?php echo $item_name["item_name"]; ?></td>
		<td><?php echo $item_mat_name["item_mat_name"]; ?></td>
		<td>
			<?php echo $freq; ?>
			<button class="btn btn-mini btn-danger text-right" onClick="remove_selected_area(<?php echo $q["slno"]; ?>)"><i class="icon-remove"></i></button>
		</td>
	</tr>
<?php
	$i++;
	}
?>
	</table>
<?php
	}
}

if($_POST["type"]=="charge_group_id")
{
	echo $vid=nextId("","charge_group_master","group_id","101");
}
if($_POST["type"]=="charge_group")
{
	$val=$_POST['val'];
	if($val)
	{
		$q=" SELECT * FROM `charge_group_master` WHERE `group_name` like '$val%' order by `group_name` ";
	}else
	{
		$q=" SELECT * FROM `charge_group_master` order by `group_name` ";
	}
	$qrpdct=mysqli_query($link, $q);
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
		if($qrpdct1["group_id"]=='104' || $qrpdct1["group_id"]=='141' || $qrpdct1["group_id"]=='142' || $qrpdct1["group_id"]=='148' || $qrpdct1["group_id"]=='151' || $qrpdct1["group_id"]=='150')
		{
			$dis_del="disabled"; }else{ $dis_del="";
		}
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['group_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['group_id'];?></td>
			<td><?php echo $qrpdct1['group_name'];?></td>
			<td>
				<button class="btn btn-mini btn-default" onClick="delete_data('<?php echo $qrpdct1['group_id'];?>')" <?php echo $dis_del; ?>>
					<img height="15" width="15" src="../images/delete.ico"/>
				</button>
			</td>
		</tr>	
<?php	
	$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="charge_group_load")
{
	$group_id=$_POST['doid1'];
	$qttl=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `charge_group_master` WHERE `group_id`='$group_id' "));
	$val=$group_id.'@'.$qttl['group_name'];
	echo $val;
}

if($_POST["type"]=="charge_id")
{
	echo $vid=nextId("","charge_master","charge_id","101");
}
if($_POST["type"]=="charges")
{
	$val=$_POST['val'];
	$cat=$_POST['cat'];
	if($cat=='141' || $cat=='142' || $cat=='144'){ $dis_del="disabled"; }else{ $dis_del=""; }
	if($val)
	{
		if($cat==0)
		$q=" SELECT * FROM `charge_master` WHERE `charge_name` like '$val%' order by `charge_id` ";
		else
		$q=" SELECT * FROM `charge_master` WHERE `charge_name` like '$val%' AND `group_id`='$cat' order by `charge_id` ";
	}else
	{
		if($cat==0)
		$q=" SELECT * FROM `charge_master` order by `charge_id` ";
		else
		$q=" SELECT * FROM `charge_master` WHERE `group_id`='$cat' order by `charge_id` ";
	}
	$qrpdct=mysqli_query($link, $q);
?>
<table class="table table-striped table-bordered table-condensed">
	<tr>
		<th>Charge ID</th>
		<th>Charge Name</th>
		<th>Amount</th>
		<th>
			<!--<img height="15" width="15" src="../images/delete.ico"/>-->
		</th>
	</tr>
<?php
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['charge_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['charge_id'];?></td>
			<td><?php echo $qrpdct1['charge_name'];?></td>
			<td>&#x20b9; <?php echo $qrpdct1['amount'];?></td>
			<td>
				<button class="btn btn-mini btn-default" onClick="delete_data('<?php echo $qrpdct1['charge_id'];?>')" <?php echo $dis_del; ?>>
					<img height="15" width="15" src="../images/delete.ico"/>
				</button>
			</td>
		</tr>	
<?php	
	$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="load_daily_expense")
{
	$q_qry=mysqli_query($link, " SELECT * FROM `expense_detail` WHERE `date`='$date' order by `slno` DESC ");
	$q_num=mysqli_num_rows($q_qry);
	if($q_num>0)
	{
	?>
		<p style="margin-top: 2%;"><b>Daily expense of :</b> <?php echo convert_date_g($date); ?></p>
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>#</th>
				<!--<th>Details</th>-->
				<th>Description</th>
				<th>Amount</th>
				<th>User</th>
			</tr>
		<?php
			$n=1;
			$tot="";
			while($q=mysqli_fetch_array($q_qry))
			{
				$emp=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$q[user]' "));
				$c=mysqli_fetch_array(mysqli_query($link, " SELECT `cat_name` FROM `category_master` WHERE `cat_id`='$q[details]' "));
				$tot=$tot+$q["amount"];
			?>
			<tr>
				<td><?php echo $n; ?></td>
				<!--<td><?php echo $c["cat_name"]; ?></td>-->
				<td><?php echo $q["description"]; ?></td>
				<td>&#x20b9; <?php echo number_format($q["amount"],2); ?></td>
				<td>
					<?php echo $emp["name"]; ?>
					<span class="text-right"><button class="btn btn-danger btn-mini" onClick="delete_exp('<?php echo $q['slno'] ?>')"><i class="icon-remove"></i></button></span>
				</td>
			</tr>
			
			<?php
				$n++;
			}
		?>
		<tr>
			<td colspan="3"><span class="text-right"><b>Total</b></span></td>
			<td colspan="2"><span class="text-left"><b>&#x20b9; <?php echo number_format($tot,2); ?></b></span></td>
		</tr>
		</table>
	<?php	
	}
}
if($_POST["type"]=="charge_load")
{
	$charge_id=$_POST['doid1'];
	$qttl=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `charge_master` WHERE `charge_id`='$charge_id' "));
	$val=$charge_id.'@#$'.$qttl['charge_name'].'@#$'.$qttl['group_id'].'@#$'.$qttl['charge_type'].'@#$'.$qttl['amount'].'@#$'.$qttl['group_id'].'@#$'.$qttl['doc_link'];
	if($qttl['group_id']=='141')
	{
		$bd=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_no` FROM `bed_master` WHERE `charge_id`='$charge_id' "));
		$val.='@#$'.$bd['bed_no'];
	}
	echo $val;
}
if($_POST["type"]=="cntermaster_id") //// cntermaster_id
{
	echo $vid=nextId("C","centremaster","centreno","100");
}
if($_POST["type"]=="cntermaster") //// cntermaster
{
	$val=$_POST['val'];
	if($val)
	{
		$q="SELECT * FROM `centremaster` WHERE `centrename` like '$val%'";
	}
	else
	{
		$q="SELECT * FROM `centremaster` order by `centrename`";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['centreno'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['centreno'];?></td>
			<td><?php echo $qrpdct1['centrename'];?></td>
			<!--<td><a href="javascript:delete_data('<?php echo $qrpdct1['centreno'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>-->
			<td><span onclick="delete_data('<?php echo $qrpdct1['centreno'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
		</tr>
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="cntermaster_load") //// cntermaster_load
{
	$tid=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from centremaster where centreno='$tid' "));
	$val=$tid.'#'.$qrm[centrename].'#'.$qrm[add1].'#'.$qrm[phoneno].'#'.$qrm[onLine].'#'.$qrm[e_mail].'#'.$qrm[credit_limit].'#'.$qrm[c_discount].'#'.$qrm[vacu_charge].'#'.$qrm[d_patho].'#'.$qrm[d_ultra].'#'.$qrm[d_xray].'#'.$qrm[d_cardio].'#'.$qrm[d_spl].'#'.$qrm[not_required].'#'.$qrm[c_patho].'#'.$qrm[c_ultra].'#'.$qrm[c_xray].'#'.$qrm[c_cardio].'#'.$qrm[c_spl].'#'.$qrm[short_name].'#'.$qrm[allow_credit].'#'.$qrm[insurance].'#'.$qrm[backup];
	echo $val;
}
if($_POST["type"]=="testtarget")
{
	$val=$_POST['val'];
	if($val)
	{
		$q="select * from testmaster where  testname like '%$val%'";
	}
	else
	{
		$q="select * from testmaster  order by testname";
	}
	$qr=mysqli_query($link, $q);
	?>
	<table class="table table-bordered table-condensed">
	<?php
	$i=1;
	while($qr1=mysqli_fetch_array($qr))
	{
	?>
	<tr style="cursor:pointer" onclick="javascript:val_load_new('<?php echo $qr1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qr1['testid'];?></td>
		<td><?php echo $qr1['testname'];?></td>
		<td><?php echo $qr1['rate'];?></td>
	</tr>
	<?php  
	$i++;
	}
	?>
	</table>
	<?php
}
if($_POST["type"]=="spltest")
{
	$docid=$_POST["docid"];
	$qrsptst=mysqli_query($link, "select testid,testname from testmaster where testid ='$docid'");
	$qrsptst1=mysqli_fetch_array($qrsptst);
	$val=$docid.'@'.$qrsptst1['testname'];
	echo $val;
}

if($_POST["type"]=="opd_room_id") //// room_id
{
	echo $vid=nextId("","opd_doctor_room","room_id","101");
}
if($_POST["type"]=="opd_room") //// roommaster
{
	$val=$_POST['val'];
	if($val)
	{
		$q="SELECT * FROM `opd_doctor_room` WHERE `room_name` like '$val%'";
	}
	else
	{
		$q="SELECT * FROM `opd_doctor_room` order by `room_name`";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['room_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['room_id'];?></td>
			<td><?php echo $qrpdct1['room_name'];?></td>
			<!--<td><a href="javascript:delete_data('<?php echo $qrpdct1['centreno'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>-->
			<td><span onclick="delete_data('<?php echo $qrpdct1['room_id'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
		</tr>
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="opd_room_load") //// opd_room_load
{
	$tid=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from opd_doctor_room where room_id='$tid' "));
	$val=$tid.'#'.$qrm['room_name'];
	echo $val;
}


if($_POST["type"]=="ipd_discharge_id")
{
	echo $vid=nextId("","discharge_master","discharge_id","101");
}
if($_POST["type"]=="ipd_discharge")
{
	$val=$_POST['val'];
	if($val)
	{
		$q="SELECT * FROM `discharge_master` WHERE `discharge_name` like '$val%'";
	}
	else
	{
		$q="SELECT * FROM `discharge_master` order by `discharge_name`";
	}
	$qrpdct=mysqli_query($link, $q);
	$i=1;
?>
<table class="table table-striped table-bordered table-condensed">
<?php
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
?>
		<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['discharge_id'];?>')" id="rad_test<?php echo $i;?>">
			<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['discharge_id'];?></td>
			<td><?php echo $qrpdct1['discharge_name'];?></td>
			<td><span onclick="delete_data('<?php echo $qrpdct1['discharge_id'];?>')"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
		</tr>
<?php	
		$i++;
	}
?>
</table>
<?php
}
if($_POST["type"]=="ipd_discharge_load")
{
	$tid=$_POST['doid1'];
	$qrm=mysqli_fetch_array(mysqli_query($link, "select * from discharge_master where discharge_id='$tid' "));
	$val=$tid.'#'.$qrm['discharge_name'];
	echo $val;
}
if($_POST["type"]=="load_sel_center_test")
{
	$sel_center=$_POST['sel_center'];
	
	$center_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename`,`c_discount` FROM `centremaster` WHERE `centreno`='$sel_center' "));
	
	$dis_reason="";
	if($center_info["c_discount"]>0)
	{
		$dis_reason=$center_info["centrename"];
	}
	$val=$dis_reason."@@@".$center_info["c_discount"];
	echo $val;
}
if($_POST["type"]=="load_master_payment")
{
	$con_doc_id=$_POST["con_doc_id"];
	
	$doc_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `opd_visit_fee`,`opd_reg_fee` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$con_doc_id' "));
	
	echo $doc_fee["opd_visit_fee"]."@@@".$doc_fee["opd_reg_fee"];
}

if($_POST["type"]=="load_patient_visit_type")
{
	$con_doc_id=$_POST["con_doc"];
	$visit_type_id=$_POST["visit_type_id"];
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	
	if($visit_type_id==1)
	{
		$str=" AND `visit_type_id` NOT IN(3,6)";
	}
	else
	{
		$str=" AND `visit_type_id` NOT IN(1)";
	}
	
	$str="";
	
	$sql="SELECT `visit_type_id`,`visit_type_name` FROM `patient_visit_type_master` WHERE `p_type_id`=1 ".$str;
	
	$qry=mysqli_query($link, $sql);
	while($data=mysqli_fetch_array($qry))
	{
		echo "<option value='$data[visit_type_id]'>$data[visit_type_name]</option>";
	}
}


?>
