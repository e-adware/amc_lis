<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");
$time_like=date("H:i");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

if($_POST["type"]=="ot_scheduling")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$schedule_id=$_POST['schedule_id'];
	$template_id=$_POST['template_id'];
	$shed=$_POST['show'];
	
	$save_btn_disable="";
	$patient_discharge=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($patient_discharge)
	{
		$save_btn_disable="disabled";
	}
	else
	{
		$patient_discharge_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		if($patient_discharge_request)
		{
			$save_btn_disable="disabled";
		}
	}
	
	$patient_ot_schedule=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$schedule_id'"));
	
	$ot_print_btn_display="display:none;";
	if($patient_ot_schedule)
	{
		$ot_print_btn_display="";
	}
	
	$patient_ot_resources_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_ot_resources` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$schedule_id'"));
	
	$resource_num=$patient_ot_resources_num+1;
	
	$patient_ot_schedule_template=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_ot_schedule_template` WHERE `template_id`='$template_id'"));
	if($patient_ot_schedule_template)
	{
		$patient_ot_schedule=$patient_ot_schedule_template;
	}
?>
	<table class="table table-condensed table-bordered" id="ot_tbl">
		<tr>
			<th>Select OT <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="ot_area_id" onchange="clear_error(this.id)">
					<option value="0">Select</option>
				<?php
					$qry=mysqli_query($link,"SELECT `ot_area_id`,`ot_area_name` FROM `ot_area_master` ORDER BY `ot_area_name`");
					while($data=mysqli_fetch_array($qry))
					{
						if($data['ot_area_id']==$patient_ot_schedule['ot_area_id']){ echo $sel="selected";}else{ $sel="";}
				?>
					<option value="<?php echo $data['ot_area_id'];?>" <?php echo $sel; ?>><?php echo $data['ot_area_name'];?></option>
				<?php
					}
				?>
				</select>
			</td>
			<th>Select OT Type <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="ot_type" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<option value="2" <?php if($patient_ot_schedule['ot_type']==2){echo "selected='selected'";}?>>Major</option>
					<option value="1" <?php if($patient_ot_schedule['ot_type']==1){echo "selected='selected'";}?>>Minor</option>
				</select>
			</td>
			<th>Select Department <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="ot_dept_id" onchange="clear_error(this.id)">
					<option value="0">Select</option>
				<?php
					$qry=mysqli_query($link,"SELECT * FROM `ot_dept_master` ORDER BY `ot_dept_name`");
					while($data=mysqli_fetch_array($qry))
					{
						if($data['ot_dept_id']==$patient_ot_schedule['ot_dept_id']){ echo $sel="selected";}else{ $sel="";}
				?>
					<option value="<?php echo $data['ot_dept_id'];?>" <?php echo $sel; ?>><?php echo $data['ot_dept_name'];?></option>
				<?php
					}
				?>
					<option value="others">Other</option>
				</select>
			</td>
		</tr>
		<tr id="tr_new_dept" style="display:none;">
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th>Department Name <b style="color:#ff0000;">*</b></th>
			<th colspan="3"><input type="text" id="new_dept" placeholder="Department Name" style="width:80%;"></th>
		</tr>
		<tr id="tr_sel_pr">
			<th>Select Procedure <b style="color:#ff0000;">*</b></th>
			<td id="proc_list" colspan="5">
			<?php
				$qry=mysqli_query($link,"SELECT `procedure_id`,`name` FROM `ot_clinical_procedure` WHERE `name`!='' ORDER BY `name` ASC");
			?>
				<select id="procedure_id" class="span8" onchange="clear_error(this.id)">
					<option value="0">Select</option>
			<?php
					while($data=mysqli_fetch_array($qry))
					{
						if($data['procedure_id']==$patient_ot_schedule['procedure_id']){ echo $sel="selected";}else{ $sel="";}
			?>
						<option value="<?php echo $data['procedure_id'];?>" <?php echo $sel; ?>><?php echo $data['name'];?></option>
			<?php
					}
			?>
					<option value="others">Other</option>
				</select>
			</td>
		</tr>
		<tr id="proc_list_new" style="display:none;">
			<th>Procedure Name <b style="color:#ff0000;">*</b></th>
			<td colspan="5">
				<input type="text" id="new_procedure" class="span8" placeholder="Procedure Name" />
			</td>
		</tr>
		<tr>
			<th>OT Date <b style="color:#ff0000;">*</b></th>
			<th><input type="text" id="ot_date" value="<?php echo $patient_ot_schedule['ot_date'];?>" placeholder="Date" /></th>
			<th>OT Start Time</th>
			<th><input type="text" id="start_time" value="<?php echo $patient_ot_schedule['start_time'];?>" placeholder="Start Time" /></th>
			<th>OT End Time</th>
			<th><input type="text" id="end_time" value="<?php echo $patient_ot_schedule['end_time'];?>" placeholder="End Time" /></th>
		</tr>
		<tr style="display:none;">
			<th>Requesting Doctor</th>
			<td colspan="5">
				<select id="request_doc_id" class="span8" onchange="clear_error(this.id)">
					<option value="0">Select</option>
				<?php
					$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `status`=0 AND `main_doc_id`=0 ORDER BY `Name`");
					while($data=mysqli_fetch_array($qry))
					{
						if($data['consultantdoctorid']==$patient_ot_schedule['request_doc_id']){ echo $sel="selected";}else{ $sel="";}
				?>
						<option value="<?php echo $data['consultantdoctorid'];?>" <?php echo $sel;?>><?php echo $data['Name'];?></option>
				<?php
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Anesthesia type <b style="color:#ff0000;">*</b></th>
			<td colspan="5">
				<select id="anesthesia_id" class="span8" onchange="clear_error(this.id)">
					<option value="0">Select</option>
				<?php
					$qry= mysqli_query($link,"SELECT * FROM `ot_anesthesia_types` ORDER BY `name`");
					while($data=mysqli_fetch_array($qry))
					{
						if($data['anesthesia_id']==$patient_ot_schedule['anesthesia_id']){ echo $sel="selected";}else{ $sel="";}
				?>
						<option value="<?php echo $data['anesthesia_id'];?>" <?php echo $sel;?>><?php echo $data['name'];?></option>
				<?php
					}
				?>
					<option value="others">Other</option>
				</select>
			</td>
		</tr>
		<tr id="tr_anas_new" style="display:none;">
			<th>Anesthesia Name <b style="color:#ff0000;">*</b></th>
			<td colspan="5"><input type="text" id="new_anesthesia" placeholder="Anesthesia Name" style="width:53%;"></td>
		</tr>
		<tr>
			<th>Diagnosis <b style="color:#ff0000;">*</b></th>
			<th colspan="5"><input type="text" id="diagnosis" value="<?php echo $patient_ot_schedule['diagnosis'];?>" placeholder="Diagnosis" style="width:53%;"></th>
		</tr>
		<tr>
			<th>OT Note</th>
			<th colspan="5"><textarea id="ot_note" placeholder="OT Notes" style="width:98%;resize:none;" rows="8"><?php echo $patient_ot_schedule['ot_note'];?></textarea></th>
		</tr>
		<tr>
			<th>OT Resources <b style="color:#ff0000;">*</b></th>
			<td colspan="5">
				<select id="resource_id" class="span4" onchange="ot_resource_change()">
					<option value="0">Select</option>
			<?php
				$qry=mysqli_query($link, "SELECT `resource_id`, `resource_name` FROM `ot_resource_master` WHERE `status`=0 ORDER BY `sequence` ASC");
				while($data=mysqli_fetch_array($qry))
				{
					echo "<option value='$data[resource_id]'>$data[resource_name]</option>";
				}
			?>
				</select>
				
				<select id="ot_staff_id" class="span4">
					<option value="0">Select</option>
				</select>
				
				<button type="button" class="btn btn-primary" onclick="add_res()"><i class="icon icon-plus"></i> Add</button>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<input type="hidden" name="tr_counter" id="tr_counter" class="form-control" value="<?php echo $resource_num; ?>"/>
				<div id="msg" align="center"></div>
				<div id="load_resources"></div>
			</td>
		</tr>
		<tr id="end_tr">
			<td colspan="6" style="text-align:center;">
				<button type="button" id="sav_shed" class="btn btn-primary" onclick="save_schedule()" <?php echo $save_btn_disable; ?>><i class="icon icon-save"></i> Save</button>
				
				<button class="btn btn-excel" onclick="save_schedule_template()"><i class="icon-file"></i> Save As Template</button>
				
				<button class="btn btn-print" id="ot_print_btn" onclick="ot_print()" style="<?php echo $ot_print_btn_display; ?>"><i class="icon-print"></i> Print</button>
			</td>
		</tr>
	</table>
	<script>
		$("#ot_date").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		$("#start_time").timepicker({minutes: {starts: 0,interval: 01,}});
		$("#end_time").timepicker({minutes: {starts: 0,interval: 01,}});
		$('#start_time').on('change', function()
		{
			$(this).css("border","");
		});
		$('#end_time').on('change', function()
		{
			$(this).css("border","");
		});
		$('#ot_date').on('change', function()
		{
			$(this).css("border","");
		});
		$("select").select2({ theme: "classic" });
	</script>
<?php
}
if($_POST["type"]=="load_ot_staff")
{
	$resource_id=mysqli_real_escape_string($link, $_POST["resource_id"]);
	
	$resource_info=mysqli_fetch_array(mysqli_query($link, "SELECT `levelid` FROM `ot_resource_master` WHERE `resource_id`='$resource_id'"));
	
	echo "<option value='0'>Select</option>";
	
	//~ if($resource_info["levelid"]==5)
	//~ {
		//~ $str="SELECT `consultantdoctorid` AS `emp_id`,`Name` AS `name` FROM `consultant_doctor_master` WHERE `status`=0 AND `main_doc_id`=0 ORDER BY `Name` ASC";
	//~ }
	//~ else
	//~ {
		//~ $str="SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='$resource_info[levelid]' AND `status`=0 ORDER BY `name` ASC";
	//~ }
	
	$str="SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='$resource_info[levelid]' AND `status`=0 ORDER BY `name` ASC";
	
	$qry=mysqli_query($link, $str);
	while($data=mysqli_fetch_array($qry))
	{
		echo "<option value='$data[emp_id]'>$data[name]</option>";
	}
}

if($_POST["type"]=="load_item_table")
{
?>
	<div>
		<table class="table table-responsive table-bordered table-condensed" id="item_list">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 5%">#</th>
					<th>Resource</th>
					<th>Name</th>
					<th style="width: 20%;">
						<i class="icon-cogs"></i>
					</th>
				</tr>
			</thead>
			<tr id="item_footer"></tr>
		</table>
	</div>
<?php
}

if($_POST["type"]=="add_items")
{
	$resource_id=mysqli_real_escape_string($link, $_POST["resource_id"]);
	$ot_staff_id=mysqli_real_escape_string($link, $_POST["ot_staff_id"]);
	$tr_counter=mysqli_real_escape_string($link, $_POST["tr_counter"]);
	
	$resource_staff=$resource_id."@#@".$ot_staff_id;
	
	$resource_info=mysqli_fetch_array(mysqli_query($link, "SELECT `resource_name` FROM `ot_resource_master` WHERE `resource_id`='$resource_id'"));
	
	$staff_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ot_staff_id' "));
?>
	<tr id="tbl_tr<?php echo $tr_counter; ?>" class="item_cls">
		<td>
			<?php echo $tr_counter; ?>
			<input class="form-control each_row list_cls" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
			<input class="form-control resource_staff list_cls" type="hidden" name="resource_staff<?php echo $tr_counter; ?>" id="resource_staff<?php echo $tr_counter; ?>" value="<?php echo $resource_staff; ?>">
		</td>
		<td>
			<?php echo $resource_info["resource_name"]; ?>
			<input class="form-control resource_id list_cls" type="hidden" name="resource_id<?php echo $tr_counter; ?>" id="resource_id<?php echo $tr_counter; ?>" value="<?php echo $resource_id; ?>"disabled>
		</td>
		<td>
			<?php echo $staff_info["name"]; ?>
			<input class="form-control ot_staff_id list_cls" type="hidden" name="ot_staff_id<?php echo $tr_counter; ?>" id="ot_staff_id<?php echo $tr_counter; ?>" value="<?php echo $ot_staff_id; ?>"disabled>
		</td>
		<td>
			<button class="btn btn-danger btn-mini remove_btn_cls" id="remove_btn<?php echo $tr_counter; ?>" onclick="remove_tr('<?php echo $tr_counter; ?>')"><i class="icon-remove"></i></button>
		</td>
	</tr>
<?php
}
if($_POST["type"]=="load_saved_resources")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$ipd=mysqli_real_escape_string($link, $_POST["ipd"]);
	$schedule_id=mysqli_real_escape_string($link, $_POST["schedule_id"]);
	
	$save_btn_disable="";
	$patient_discharge=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($patient_discharge)
	{
		$save_btn_disable="disabled";
	}
	else
	{
		$patient_discharge_request=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		if($patient_discharge_request)
		{
			$save_btn_disable="disabled";
		}
	}
	
	$patient_ot_resources_qry=mysqli_query($link,"SELECT * FROM `patient_ot_resources` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$schedule_id'");
	$patient_ot_resources_num=mysqli_num_rows($patient_ot_resources_qry);
	if($patient_ot_resources_num>0)
	{
?>
	<div>
		<table class="table table-responsive table-bordered table-condensed" id="item_list">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 5%">#</th>
					<th>Resource</th>
					<th>Name</th>
					<th style="width: 20%;">
						<i class="icon-cogs"></i>
					</th>
				</tr>
			</thead>
<?php
		$tr_counter=1;
		while($patient_ot_resources=mysqli_fetch_array($patient_ot_resources_qry))
		{
			$resource_id=$patient_ot_resources["resource_id"];
			$ot_staff_id=$patient_ot_resources["emp_id"];
			
			$resource_staff=$resource_id."@#@".$ot_staff_id;
			
			$resource_info=mysqli_fetch_array(mysqli_query($link, "SELECT `resource_name` FROM `ot_resource_master` WHERE `resource_id`='$resource_id'"));
			
			$staff_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ot_staff_id' "));
?>
			<tr id="tbl_tr<?php echo $tr_counter; ?>" class="item_cls">
				<td>
					<?php echo $tr_counter; ?>
					<input class="form-control each_row list_cls" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
					<input class="form-control resource_staff list_cls" type="hidden" name="resource_staff<?php echo $tr_counter; ?>" id="resource_staff<?php echo $tr_counter; ?>" value="<?php echo $resource_staff; ?>">
				</td>
				<td>
					<?php echo $resource_info["resource_name"]; ?>
					<input class="form-control resource_id list_cls" type="hidden" name="resource_id<?php echo $tr_counter; ?>" id="resource_id<?php echo $tr_counter; ?>" value="<?php echo $resource_id; ?>"disabled>
				</td>
				<td>
					<?php echo $staff_info["name"]; ?>
					<input class="form-control ot_staff_id list_cls" type="hidden" name="ot_staff_id<?php echo $tr_counter; ?>" id="ot_staff_id<?php echo $tr_counter; ?>" value="<?php echo $ot_staff_id; ?>"disabled>
				</td>
				<td>
					<button class="btn btn-danger btn-mini remove_btn_cls" id="remove_btn<?php echo $tr_counter; ?>" onclick="remove_tr('<?php echo $tr_counter; ?>')" <?php echo $save_btn_disable; ?>><i class="icon-remove"></i></button>
				</td>
			</tr>
<?php
			$tr_counter++;
		}
?>
			<tr id="item_footer"></tr>
		</table>
	</div>
<?php
	}
}
if($_POST["type"]=="save_schedule")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$ipd=mysqli_real_escape_string($link, $_POST["ipd"]);
	$schedule_id=mysqli_real_escape_string($link, $_POST["schedule_id"]);
	
	$ot_area_id=mysqli_real_escape_string($link, $_POST["ot_area_id"]);
	$ot_type=mysqli_real_escape_string($link, $_POST["ot_type"]);
	$ot_dept_id=mysqli_real_escape_string($link, $_POST["ot_dept_id"]);
	$new_dept=mysqli_real_escape_string($link, $_POST["new_dept"]);
	$procedure_id=mysqli_real_escape_string($link, $_POST["procedure_id"]);
	$new_procedure=mysqli_real_escape_string($link, $_POST["new_procedure"]);
	$ot_date=mysqli_real_escape_string($link, $_POST["ot_date"]);
	$start_time=mysqli_real_escape_string($link, $_POST["start_time"]);
	$end_time=mysqli_real_escape_string($link, $_POST["end_time"]);
	$request_doc_id=mysqli_real_escape_string($link, $_POST["request_doc_id"]);
	$anesthesia_id=mysqli_real_escape_string($link, $_POST["anesthesia_id"]);
	$new_anesthesia=mysqli_real_escape_string($link, $_POST["new_anesthesia"]);
	$diagnosis=mysqli_real_escape_string($link, $_POST["diagnosis"]);
	$ot_note=mysqli_real_escape_string($link, $_POST["ot_note"]);
	$all_resources=mysqli_real_escape_string($link, $_POST["all_resources"]);
	
	if(!$ot_date){ $ot_date="0000-00-00"; }
	if(!$start_time){ $start_time="00:00:00"; }
	if(!$end_time){ $end_time="00:00:00"; }
	
	if($ot_dept_id==0)
	{
		if($new_dept=="")
		{
			echo "404@No department selected@".$schedule_id;
			exit;
		}
		else
		{
			$dept_info=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_id` FROM `ot_dept_master` WHERE `ot_dept_name`='$new_dept'"));
			if($dept_info)
			{
				$ot_dept_id=$dept_info["ot_dept_id"];
			}
			else
			{
				if(mysqli_query($link,"INSERT INTO `ot_dept_master`(`ot_dept_name`,`user`) VALUES ('$new_dept','$c_user')"))
				{
					$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_id` FROM `ot_dept_master` WHERE `user`='$c_user' ORDER BY `ot_dept_id` DESC"));
					$ot_dept_id=$last_row["ot_dept_id"];
				}
				else
				{
					echo "404@Department error@".$schedule_id;
					exit;
				}
			}
		}
	}
	if($procedure_id==0)
	{
		if($new_procedure=="")
		{
			echo "404@No procedure selected@".$schedule_id;
			exit;
		}
		else
		{
			$procedure_info=mysqli_fetch_array(mysqli_query($link,"SELECT `procedure_id` FROM `ot_clinical_procedure` WHERE `name`='$new_procedure'"));
			if($procedure_info)
			{
				$procedure_id=$procedure_info["procedure_id"];
			}
			else
			{
				if(mysqli_query($link,"INSERT INTO `ot_clinical_procedure`(`ot_dept_id`,`name`,`user`) VALUES ('0','$new_procedure','$c_user')"))
				{
					$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `procedure_id` FROM `ot_clinical_procedure` WHERE `user`='$c_user' ORDER BY `procedure_id` DESC"));
					$procedure_id=$last_row["procedure_id"];
				}
				else
				{
					echo "404@Procedure error@".$schedule_id;
					exit;
				}
			}
		}
	}
	if($anesthesia_id==0)
	{
		if($new_anesthesia=="")
		{
			echo "404@No anesthesia selected@".$schedule_id;
			exit;
		}
		else
		{
			$procedure_info=mysqli_fetch_array(mysqli_query($link,"SELECT `anesthesia_id` FROM `ot_anesthesia_types` WHERE `name`='$new_anesthesia'"));
			if($procedure_info)
			{
				$anesthesia_id=$procedure_info["anesthesia_id"];
			}
			else
			{
				if(mysqli_query($link,"INSERT INTO `ot_anesthesia_types`(`name`,`user`) VALUES ('$new_anesthesia','$c_user')"))
				{
					$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `anesthesia_id` FROM `ot_anesthesia_types` WHERE `user`='$c_user' ORDER BY `anesthesia_id` DESC"));
					$anesthesia_id=$last_row["anesthesia_id"];
				}
				else
				{
					echo "404@Anesthesia error@".$schedule_id;
					exit;
				}
			}
		}
	}
	
	$patient_ot_schedule=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$schedule_id'"));
	
	if($patient_ot_schedule)
	{
		if(mysqli_query($link, "UPDATE `patient_ot_schedule` SET `ot_area_id`='$ot_area_id',`ot_type`='$ot_type',`ot_dept_id`='$ot_dept_id',`procedure_id`='$procedure_id',`ot_date`='$ot_date',`start_time`='$start_time',`end_time`='$end_time',`request_doc_id`='$request_doc_id',`anesthesia_id`='$anesthesia_id',`diagnosis`='$diagnosis',`ot_note`='$ot_note' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$schedule_id'"))
		{
			mysqli_query($link, "DELETE FROM `patient_ot_resources` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$schedule_id'");
			
			$all_resourcez=explode("@$@", $all_resources);
			foreach($all_resourcez AS $all_resourcez)
			{
				if($all_resourcez)
				{
					$each_resource=explode("#",$all_resourcez);
					
					$resource_id=$each_resource[0];
					$emp_id=$each_resource[1];
					
					$consultantdoctorid=0;
					$doc_info=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$emp_id'"));
					if($doc_info)
					{
						$consultantdoctorid=$doc_info["consultantdoctorid"];
					}
					
					mysqli_query($link, "INSERT INTO `patient_ot_resources`(`patient_id`, `ipd_id`, `schedule_id`, `resource_id`, `emp_id`, `consultantdoctorid`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$schedule_id','$resource_id','$emp_id','$consultantdoctorid','$c_user','$date','$time')");
				}
			}
			
			echo "101@Updated@".$schedule_id;
		}
		else
		{
			echo "404@Failed, try again later.@".$schedule_id;
		}
	}
	else
	{
		$check_duplicate=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ot_area_id`='$ot_area_id' AND `procedure_id`='$procedure_id' AND `ot_date`='$ot_date' AND `anesthesia_id`='$anesthesia_id' AND `user`='$c_user' AND `date`='$date' AND `time` LIKE '$time_like%'"));
		if(!$check_duplicate)
		{
			if(mysqli_query($link, "INSERT INTO `patient_ot_schedule`(`schedule_no`, `patient_id`, `ipd_id`, `ot_area_id`, `ot_type`, `ot_dept_id`, `procedure_id`, `ot_date`, `start_time`, `end_time`, `request_doc_id`, `anesthesia_id`, `diagnosis`, `ot_note`, `user`, `date`, `time`) VALUES (NULL,'$uhid','$ipd','$ot_area_id','$ot_type','$ot_dept_id','$procedure_id','$ot_date','$start_time','$end_time','$request_doc_id','$anesthesia_id','$diagnosis','$ot_note','$c_user','$date','$time')"))
			{
				$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `schedule_id` FROM `patient_ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `ot_date`='$ot_date' AND `user`='$c_user' ORDER BY `schedule_id` DESC"));
				$schedule_id=$last_row["schedule_id"];
				
				mysqli_query($link, "DELETE FROM `patient_ot_resources` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$schedule_id'");
				
				$all_resourcez=explode("@$@", $all_resources);
				foreach($all_resourcez AS $all_resourcez)
				{
					if($all_resourcez)
					{
						$each_resource=explode("#",$all_resourcez);
						
						$resource_id=$each_resource[0];
						$emp_id=$each_resource[1];
						
						$consultantdoctorid=0;
						$doc_info=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$emp_id'"));
						if($doc_info)
						{
							$consultantdoctorid=$doc_info["consultantdoctorid"];
						}
						
						mysqli_query($link, "INSERT INTO `patient_ot_resources`(`patient_id`, `ipd_id`, `schedule_id`, `resource_id`, `emp_id`, `consultantdoctorid`, `user`, `date`, `time`) VALUES ('$uhid','$ipd','$schedule_id','$resource_id','$emp_id','$consultantdoctorid','$c_user','$date','$time')");
					}
				}
				
				$_SESSION["schedule_id"]=$schedule_id;
				
				echo "101@Saved@".$schedule_id;
			}
			else
			{
				echo "404@Failed, try again later@".$schedule_id;
			}
		}
		else
		{
			echo "404@Failed, try again later..@".$schedule_id;
		}
	}
}

if($_POST["type"]=="save_schedule_template")
{
	$uhid=mysqli_real_escape_string($link, $_POST["uhid"]);
	$ipd=mysqli_real_escape_string($link, $_POST["ipd"]);
	
	$template_id=mysqli_real_escape_string($link, $_POST["template_id"]);
	$template_name=mysqli_real_escape_string($link, $_POST["template_name"]);
	
	$ot_area_id=mysqli_real_escape_string($link, $_POST["ot_area_id"]);
	$ot_type=mysqli_real_escape_string($link, $_POST["ot_type"]);
	$ot_dept_id=mysqli_real_escape_string($link, $_POST["ot_dept_id"]);
	$new_dept=mysqli_real_escape_string($link, $_POST["new_dept"]);
	$procedure_id=mysqli_real_escape_string($link, $_POST["procedure_id"]);
	$new_procedure=mysqli_real_escape_string($link, $_POST["new_procedure"]);
	$request_doc_id=mysqli_real_escape_string($link, $_POST["request_doc_id"]);
	$anesthesia_id=mysqli_real_escape_string($link, $_POST["anesthesia_id"]);
	$new_anesthesia=mysqli_real_escape_string($link, $_POST["new_anesthesia"]);
	$diagnosis=mysqli_real_escape_string($link, $_POST["diagnosis"]);
	$ot_note=mysqli_real_escape_string($link, $_POST["ot_note"]);
	
	if($ot_dept_id==0)
	{
		if($new_dept=="")
		{
			echo "404@No department selected";
			exit;
		}
		else
		{
			$dept_info=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_id` FROM `ot_dept_master` WHERE `ot_dept_name`='$new_dept'"));
			if($dept_info)
			{
				$ot_dept_id=$dept_info["ot_dept_id"];
			}
			else
			{
				if(mysqli_query($link,"INSERT INTO `ot_dept_master`(`ot_dept_name`,`user`) VALUES ('$new_dept','$c_user')"))
				{
					$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_id` FROM `ot_dept_master` WHERE `user`='$c_user' ORDER BY `ot_dept_id` DESC"));
					$ot_dept_id=$last_row["ot_dept_id"];
				}
				else
				{
					echo "404@Department error";
					exit;
				}
			}
		}
	}
	if($procedure_id==0)
	{
		if($new_procedure=="")
		{
			echo "404@No procedure selected";
			exit;
		}
		else
		{
			$procedure_info=mysqli_fetch_array(mysqli_query($link,"SELECT `procedure_id` FROM `ot_clinical_procedure` WHERE `name`='$new_procedure'"));
			if($procedure_info)
			{
				$procedure_id=$procedure_info["procedure_id"];
			}
			else
			{
				if(mysqli_query($link,"INSERT INTO `ot_clinical_procedure`(`ot_dept_id`,`name`,`user`) VALUES ('0','$new_procedure','$c_user')"))
				{
					$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `procedure_id` FROM `ot_clinical_procedure` WHERE `user`='$c_user' ORDER BY `procedure_id` DESC"));
					$procedure_id=$last_row["procedure_id"];
				}
				else
				{
					echo "404@Procedure error";
					exit;
				}
			}
		}
	}
	if($anesthesia_id==0)
	{
		if($new_anesthesia=="")
		{
			echo "404@No anesthesia selected";
			exit;
		}
		else
		{
			$procedure_info=mysqli_fetch_array(mysqli_query($link,"SELECT `anesthesia_id` FROM `ot_anesthesia_types` WHERE `name`='$new_anesthesia'"));
			if($procedure_info)
			{
				$anesthesia_id=$procedure_info["anesthesia_id"];
			}
			else
			{
				if(mysqli_query($link,"INSERT INTO `ot_anesthesia_types`(`name`,`user`) VALUES ('$new_anesthesia','$c_user')"))
				{
					$last_row=mysqli_fetch_array(mysqli_query($link,"SELECT `anesthesia_id` FROM `ot_anesthesia_types` WHERE `user`='$c_user' ORDER BY `anesthesia_id` DESC"));
					$anesthesia_id=$last_row["anesthesia_id"];
				}
				else
				{
					echo "404@Anesthesia error";
					exit;
				}
			}
		}
	}
	
	$patient_ot_schedule_template=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_ot_schedule_template` WHERE `template_id`='$template_id' "));
	if($patient_ot_schedule_template)
	{
		if(mysqli_query($link, "UPDATE `patient_ot_schedule_template` SET `template_name`='$template_name',`ot_area_id`='$ot_area_id',`ot_type`='$ot_type',`ot_dept_id`='$ot_dept_id',`procedure_id`='$procedure_id',`anesthesia_id`='$anesthesia_id',`diagnosis`='$diagnosis',`ot_note`='$ot_note' WHERE `template_id`='$template_id'"))
		{
			$save_num=1;
		}
		else
		{
			$save_num=0;
		}
	}
	else
	{
		if(mysqli_query($link, "INSERT INTO `patient_ot_schedule_template`(`template_name`, `ot_area_id`, `ot_type`, `ot_dept_id`, `procedure_id`, `anesthesia_id`, `diagnosis`, `ot_note`, `user`, `date`, `time`) VALUES ('$template_name','$ot_area_id','$ot_type','$ot_dept_id','$procedure_id','$anesthesia_id','$diagnosis','$ot_note','$c_user','$date','$time')"))
		{
			$save_num=2;
		}
		else
		{
			$save_num=0;
		}
	}
	
	if($save_num==0)
	{
		echo "404@Failed, try again later.";
	}
	else if($save_num==1)
	{
		echo "101@Updated";
	}
	else
	{
		echo "101@Saved";
	}
}
?>
