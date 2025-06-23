<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="search_patient_list_ipd")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['date'];
	$usr=$_POST['usr'];
	$list_start=$_POST["list_start"];
	
	$zz=0;
	
	$str=" SELECT * FROM `uhid_and_opdid` WHERE `type`='3'";
	
	if($uhid)
	{
		if(strlen($uhid)>2)
		{
			$str.=" AND `patient_id`='$uhid' ";
			$zz++;
		}
	}
	if($ipd)
	{
		if(strlen($ipd)>2)
		{
			$str.=" AND `opd_id`='$ipd' ";
			$zz++;
		}
	}
	if($name)
	{
		if(strlen($name)>3)
		{
			$str.=" AND `patient_id` IN(SELECT `patient_id` FROM `patient_info` WHERE `name` LIKE '%$name%') ";
			$zz++;
		}
	}
	if($dat)
	{
		$str.=" AND `date`='$dat' ";
		$zz++;
	}
	
	if($zz==0)
	{
		$str.=" AND `opd_id` IN(SELECT `ipd_id` FROM `ipd_pat_bed_details`) ";
	}
	$str.=" ORDER BY `slno` DESC limit ".$list_start;
	
	//echo $str;
	
	$num=mysqli_num_rows(mysqli_query($link,$str));
	if($num>0)
	{
		$qry=mysqli_query($link,$str);
?>
		<table class="table table-condensed table-bordered" style="background-color:white;">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Unit No.</th>
					<th>Bill No.</th>
					<th>Patient Name</th>
					<th>Sex</th>
					<th>Age</th>
					<th>Reg Date</th>
				</tr>
			</thead>
<?php
			$n=1;
			while($data=mysqli_fetch_array($qry))
			{
				$display=1;
				if($display==1)
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$data[patient_id]'"));
					
					$reg_date=$data["date"];
					
					if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
?>
				<tr onclick="redirect_page('<?php echo $data['patient_id'];?>','<?php echo $data['opd_id'];?>')" style="cursor:pointer;">
					<td><?php echo $n;?></td>
					<td><?php echo $data['patient_id'];?></td>
					<td><?php echo $data['opd_id'];?></td>
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo $pat_info['sex'];?></td>
					<td><?php echo $age;?></td>
					<td><?php echo date("d-m-Y",strtotime($data["date"]));?></td>
				</tr>
<?php
					$n++;
				}
			}
?>
		</table>
<?php
    }
}


if($_POST["type"]=="ot_schedule_reason")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$reason=$_POST['reason'];
	$reason= str_replace("'", "''", "$reason");
	$usr=$_POST['usr'];
	mysqli_query($link,"INSERT INTO `ot_pac_status`(`patient_id`, `ipd_id`, `reason`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$reason','$date','$time','$usr')");
}

if($_POST["type"]=="ot_scheduling")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['show'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ot_process` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($n==0)
	{
		$o=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id` NOT IN (SELECT DISTINCT `schedule_id` FROM `ot_room_leaved` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
		?>
	<table class="table table-condensed table-bordered" id="ot_tbl">
		<tr>
			<!--<th>Select OT</th>
			<td>
				<select id="ot" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `ot_area_id`,`ot_area_name` FROM `ot_area_master` ORDER BY `ot_area_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ot_area_id'];?>" <?php if($r['ot_area_id']==$o['ot_area_id']){echo "selected='selected'";}?>><?php echo $r['ot_area_name'];?></option>
					<?php
					}
					?>
				</select>
			</td>-->
			<th>Select OT Type</th>
			<td>
				<input type="hidden" id="ot" value="0" />
				<select id="ot_typ" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<option value="2">Major</option>
					<option value="1">Minor</option>
				</select>
			</td>
			<th>Select Department</th>
			<td>
				<select id="ot_dept" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `ot_dept_master` ORDER BY `ot_dept_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ot_dept_id'];?>" <?php if($r['ot_dept_id']==$o['ot_dept_id']){echo "selected='selected'";}?>><?php echo $r['ot_dept_name'];?></option>
					<?php
					}
					?>
					<option value="others">Others</option>
				</select>
			</td>
		</tr>
		<tr id="tr_new_dept" style="display:none;">
			<th>Department Name</th>
			<th colspan="3"><input type="text" id="new_dept" placeholder="Department Name" style="width:80%;"></th>
		</tr>
		<tr id="tr_sel_pr">
			<th>Select Procedure</th>
			<td id="proc_list" colspan="3">
				<select id="pr" class="span6">
					<option value="0">Select</option>
				</select>
			</td>
		</tr>
		<tr id="proc_list_new" style="display:none;">
			<th>Procedure Name</th>
			<td colspan="3">
				<input type="text" id="pr_name" class="span6" placeholder="Procedure Name" />
			</td>
		</tr>
		<tr>
			<th id="grade_text">Select Grade</th>
			<td id="grade_list">
				<select id="grade" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					//$qq = mysqli_query($link,"SELECT `grade_id`, `grade_name` FROM `ot_grade_master` ORDER BY `grade_name`");
					//while($cc=mysqli_fetch_array($qq))
					{
						?>
						<!--<option value="<?php echo $cc['grade_id'];?>"><?php echo $cc['grade_name'];?></option>-->
						<?php
					}
					?>
				</select>
			</td>
			<th>Cabin</th>
			<td id="cabin_list">
				<select id="cabin" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					//$q=mysqli_query($link,"SELECT * FROM `ot_cabin_master` ORDER BY `ot_cabin_name`");
					//while($r=mysqli_fetch_array($q))
					{
					?>
						<!--<option value="<?php echo $r['ot_cabin_id'];?>"><?php echo $r['ot_cabin_name'];?></option>-->
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Date</th>
			<th><input type="text" id="ot_date" value="<?php echo $o['ot_date'];?>" placeholder="Date" /></th>
			<th>Refer Doctor</th>
			<td>
				<select id="doc" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					//$qry=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5'");
					$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($rrr=mysqli_fetch_array($qry))
					{
					?>
					<option value="<?php echo $rrr['consultantdoctorid'];?>" <?php if($rrr['consultantdoctorid']==$o['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $rrr['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Start Time</th>
			<th><input type="text" id="st_time" placeholder="Start Time" /></th>
			<th>End Time</th>
			<th><input type="text" id="en_time" placeholder="End Time" /></th>
		</tr>
		<tr>
			<th>Anesthesia type</th>
			<td colspan="3">
				<select id="anas" class="span3" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					$qq = mysqli_query($link,"SELECT * FROM `ot_anesthesia_types` ORDER BY `name`");
					while($cc=mysqli_fetch_array($qq))
					{
						?>
						<option value="<?php echo $cc['anesthesia_id'];?>"><?php echo $cc['name'];?></option>
						<?php
					}
					?>
					<option value="others">Others</option>
				</select>
			</td>
		</tr>
		<tr id="tr_anas_new" style="display:none;">
			<th>Anesthesia Name</th>
			<td colspan="3"><input type="text" id="new_anas" placeholder="Anesthesia Name" style="width:80%;"></td>
		</tr>
		<tr>
			<th>Diagnosis</th>
			<th colspan="3"><input type="text" id="diag" placeholder="Diagnosis" style="width:80%;"></th>
		</tr>
		<tr>
			<th>Remarks</th>
			<th colspan="3"><textarea id="rem" placeholder="Remarks" style="width:80%;resize:none;"></textarea></th>
		</tr>
		<tr>
			<th>OT Resources</th>
			<td id="ot_type_list">
				<select id="ot_type" onchange="ot_resource_list()">
					<option value="0">Select</option>
				</select>
			</td>
			<td id="resource_list">
				<select id="rs">
					<option value="0">Select</option>
				</select>
			</td>
			<th>
				<input type="text" id="sel_val" style="display:none;" />
				<span class="text-right"><button type="button" class="btn btn-primary" onclick="add_res()"><i class="icon icon-plus"></i> Add</button></span>
			</th>
		</tr>
		<tr id="end_tr">
			<td colspan="4" style="text-align:center;"><button type="button" id="sav_shed" class="btn btn-primary" onclick="save_shed()"><i class="icon icon-save"></i> Save</button></td>
		</tr>
	</table>
	<script>
		$("#ot_date").datepicker({dateFormat: 'yy-mm-dd'});
		$("#st_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$("#en_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$('#st_time').on('change', function()
		{
			$(this).css("border","");
		});
		$('#en_time').on('change', function()
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
	else
	{
		if($shed)
		{
		$upd_book=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'"));
		$upd_shed=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `schedule_id`='$shed' AND `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		
		?>
	<table class="table table-condensed table-bordered" id="ot_tbl">
		<tr>
			<th>Select OT Type</th>
			<td>
				<input type="hidden" id="ot" value="0" />
				<select id="ot_typ" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<option value="2" <?php if($upd_shed['ot_type']=="2"){echo "selected='selected'";}?>>Major</option>
					<option value="1" <?php if($upd_shed['ot_type']=="1"){echo "selected='selected'";}?>>Minor</option>
				</select>
			</td>
			<th>Select Department</th>
			<td>
				<select id="ot_dept" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `ot_dept_master` ORDER BY `ot_dept_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ot_dept_id'];?>" <?php if($r['ot_dept_id']==$upd_shed['ot_dept_id']){echo "selected='selected'";}?>><?php echo $r['ot_dept_name'];?></option>
					<?php
					}
					?>
					<option value="others">Others</option>
				</select>
			</td>
		</tr>
		<tr id="tr_new_dept" style="display:none;">
			<th>Department Name</th>
			<th colspan="3"><input type="text" id="new_dept" placeholder="Department Name" style="width:80%;"></th>
		</tr>
		<tr id="tr_sel_pr">
			<th>Select Procedure</th>
			<td id="proc_list" colspan="3">
				<?php
				$qry="SELECT `procedure_id`,`name` FROM `ot_clinical_procedure` WHERE `ot_dept_id`='$upd_shed[ot_dept_id]' ORDER BY `name`";
				$qr=mysqli_query($link,$qry);
				?>
				<select id="pr" onchange="clear_error(this.id)" class="span6">
					<option value="0">Select</option>
					<?php
					while($rr=mysqli_fetch_array($qr))
					{
					?>
					<option value="<?php echo $rr['procedure_id'];?>" <?php if($rr['procedure_id']==$upd_shed['procedure_id']){echo "selected='selected'";}?>><?php echo $rr['name'];?></option>
					<?php
					}
					?>
					<option value="others">Others</option>
				</select>
				<?php
				?>
			</td>
		</tr>
		<tr id="proc_list_new" style="display:none;">
			<th>Procedure Name</th>
			<td colspan="3">
				<input type="text" id="pr_name" class="span6" placeholder="Procedure Name" />
			</td>
		</tr>
		<tr>
			<th id="grade_text">Select Grade</th>
			<td id="grade_list">
				<!--<select id="grade" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					$qq = mysqli_query($link,"SELECT `grade_id`, `grade_name` FROM `ot_grade_master` ORDER BY `grade_name`");
					while($cc=mysqli_fetch_array($qq))
					{
						?>
						<option value="<?php echo $cc['grade_id'];?>" <?php if($cc['grade_id']==$upd_shed['grade_id']){echo "selected='selected'";}?>><?php echo $cc['grade_name'];?></option>
						<?php
					}
					?>
				</select>-->
			</td>
			<th>Cabin</th>
			<td id="cabin_list">
				<!--<select id="cabin" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					//$qq = mysqli_query($link,"SELECT b.`ot_cabin_id`, b.`ot_cabin_name` FROM `ot_cabin_rate` a, `ot_cabin_master` b WHERE a.`ot_cabin_id`=b.`ot_cabin_id` AND a.`grade_id`='8' ORDER BY b.`ot_cabin_name` ");
					$qq = mysqli_query($link,"SELECT * FROM `ot_cabin_master` ORDER BY `ot_cabin_name` ");
					while($cc=mysqli_fetch_array($qq))
					{
						?>
						<option value="<?php echo $cc['ot_cabin_id'];?>" <?php if($cc['ot_cabin_id']==$upd_shed['ot_cabin_id']){echo "selected='selected'";}?>><?php echo $cc['ot_cabin_name'];?></option>
						<?php
					}
					?>
				</select>-->
			</td>
		</tr>
		<tr>
			<th>Date</th>
			<th><input type="text" id="ot_date" value="<?php echo $upd_shed['ot_date'];?>" placeholder="Date" /></th>
			<th>Refer Doctor</th>
			<td>
				<select id="doc" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($rrr=mysqli_fetch_array($qry))
					{
					?>
					<option value="<?php echo $rrr['consultantdoctorid'];?>" <?php if($rrr['consultantdoctorid']==$upd_shed['requesting_doc']){echo "selected='selected'";}?>><?php echo $rrr['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<?php
		if($upd_shed['start_time']!="00:00:00")
		{
			$stm=explode(":",$upd_shed['start_time']);
			$st_tim=$stm[0].":".$stm[1];
		}
		else
		{
			$st_tim="";
		}
		if($upd_shed['end_time']!="00:00:00")
		{
			$etm=explode(":",$upd_shed['end_time']);
			$en_tim=$etm[0].":".$etm[1];
		}
		else
		{
			$en_tim="";
		}
		?>
		<tr>
			<th>Start Time</th>
			<th><input type="text" id="st_time" value="<?php echo $st_tim;?>" placeholder="Start Time" /></th>
			<th>End Time</th>
			<th><input type="text" id="en_time" value="<?php echo $en_tim;?>" placeholder="End Time" /></th>
		</tr>
		<tr>
			<th>Anesthesia type</th>
			<td colspan="3">
				<select id="anas" class="span3" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					$qq = mysqli_query($link,"SELECT * FROM `ot_anesthesia_types` ORDER BY `name`");
					while($cc=mysqli_fetch_array($qq))
					{
						?>
						<option value="<?php echo $cc['anesthesia_id'];?>" <?php if($cc['anesthesia_id']==$upd_shed['anesthesia_id']){echo "selected='selected'";}?>><?php echo $cc['name'];?></option>
						<?php
					}
					?>
					<option value="others">Others</option>
				</select>
			</td>
		</tr>
		<tr id="tr_anas_new" style="display:none;">
			<th>Anesthesia Name</th>
			<td colspan="3"><input type="text" id="new_anas" placeholder="Anesthesia Name" style="width:80%;"></td>
		</tr>
		<tr>
			<th>Diagnosis</th>
			<th colspan="3"><input type="text" id="diag" placeholder="Diagnosis" value="<?php echo $upd_shed['diagnosis'];?>" style="width:80%;"></th>
		</tr>
		<tr>
			<th>Remarks</th>
			<th colspan="3"><textarea id="rem" placeholder="Remarks" style="width:80%;resize:none;"><?php echo $upd_shed['remarks'];?></textarea></th>
		</tr>
		<tr>
			<th>OT Resources</th>
			<td id="ot_type_list">
				<select id="ot_type" onchange="ot_resource_list()">
					<option value="0">Select</option>
				</select>
			</td>
			<td id="resource_list">
				<select id="rs">
					<option value="0">Select</option>
				</select>
			</td>
			<th>
				<input type="text" id="sel_val" style="display:none;" />
				<span class="text-right"><button type="button" class="btn btn-primary" onclick="add_res()"><i class="icon icon-plus"></i> Add</button></span>
			</th>
		</tr>
		<?php
		$rs_qry=mysqli_query($link,"SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed' AND `emp_id`>0");
		while($rs_row=mysqli_fetch_array($rs_qry))
		{
			$res=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$rs_row[resourse_id]'"));
			$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rs_row[emp_id]'"));
		?>
		<tr class="cc cc<?php echo $rs_row['resourse_id'];?> cc<?php echo $rs_row['emp_id'];?>">
			<td></td>
			<td><input type="hidden" value="<?php echo $rs_row['resourse_id'];?>" /><?php echo $res['type'];?></td>
			<td><input type="hidden" value="<?php echo $rs_row['emp_id'];?>" /><?php echo $emp['name'];?></td>
			<td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td>
		</tr>
		<?php
		}
		?>
		<!--<tr class="cc cc'+$("#ot_type").val()+' cc'+$("#rs").val()+'" id="tr'+row+'">
			<td></td>
			<td><input type="hidden" value="'+$("#ot_type").val()+'" />'+vl[0]+'</td>
			<td><input type="hidden" value="'+$("#rs").val()+'" />'+vl[1]+'</td>
			<td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td>
		</tr>-->
		<tr id="end_tr">
			<td colspan="4" style="text-align:center;"><button type="button" id="sav_shed" class="btn btn-primary" onclick="upd_shed()"><i class="icon icon-save"></i> Update</button></td>
		</tr>
	</table>
	<input type="text" id="pr_val" value="<?php echo $upd_shed['procedure_id'];?>" style="display:none;" />
	<input type="text" id="gr_val" value="<?php echo $upd_shed['grade_id'];?>" style="display:none;" />
	<input type="text" id="cab_val" value="<?php echo $upd_shed['ot_cabin_id'];?>" style="display:none;" />
	<script>
		$("#ot_date").datepicker({dateFormat: 'yy-mm-dd'});
		$("#st_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$("#en_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$('#st_time').on('change', function()
		{
			$(this).css("border","");
		});
		$('#en_time').on('change', function()
		{
			$(this).css("border","");
		});
		$('#ot_date').on('change', function()
		{
			$(this).css("border","");
		});
		$("select").select2({ theme: "classic" });
		clear_error('ot_dept');
		setTimeout(function()
		{
			$("#pr").val($("#pr_val").val()).trigger("change");
			$("#grade").val($("#gr_val").val()).trigger("change");
			$("#cabin").val($("#cab_val").val()).trigger("change");
		},200);
		setTimeout(function(){$("#cabin").val($("#cab_val").val()).trigger("change");},400);
	</script>
		<?php
		}
	}
}

if($_POST["type"]=="ot_scheduling_old")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$r=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$p=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name`,`sex`,`dob`,`age`,`age_type` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($r['scheduled']=="0")
	$sh="Not scheduled";
	if($r['scheduled']=="1")
	$sh="Scheduled";
	$j=1;
	$qry=mysqli_query($link,"SELECT * FROM `ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		while($r=mysqli_fetch_array($qry))
		{
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th colspan="5" style="background:#cccccc;"><?php if($num>1){echo "Schedule ".$j;}?></th>
			</tr>
			<tr>
				<th>Schedule No</th>
				<th>OT Date</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>OT No</th>
			</tr>
			<tr>
				<td><?php echo $r['schedule_id'];?></td>
				<td><?php echo $r['ot_date'];?></td>
				<td><?php echo $r['start_time'];?></td>
				<td><?php echo $r['end_time'];?></td>
				<td><?php echo $r['ot_no'];?></td>
			</tr>
			<?php
			if($r['remarks']!="")
			{
			?>
			<tr>
				<th>Remarks</th>
				<td colspan="4"><?php echo $r['remarks'];?></td>
			</tr>
			<?php
			}
			$qq=mysqli_query($link,"SELECT * FROM `ot_resource` WHERE `schedule_id`='$r[schedule_id]'");
			$nn=mysqli_num_rows($qq);
			if($nn>0)
			{
			?>
			<tr>
				<td colspan="5" style="background:#dddddd;"></td>
			</tr>
			<tr>
				<th colspan="5" style="text-align:center;">OT Resources</th>
			</tr>
			<tr>
				<th>SN</th>
				<th>Resource</th>
				<th>Employee</th>
				<th></th>
				<th></th>
			</tr>
			<?php
			$i=1;
			while($rr=mysqli_fetch_array($qq))
			{
				$res=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$rr[resourse_id]'"));
				$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
			?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $res['type'];?></td>
				<td><?php echo $emp['name'];?></td>
				<td></td>
				<td></td>
			</tr>
			<?php
			$i++;
			}
			?>
			<tr>
				<th colspan="5" style="background:#dddddd;"></th>
			</tr>
			<?php
			}
			?>
			
		</table>
		<?php
		$j++;
		}
	}
	else
	{
		$o=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	?>
	<table class="table table-condensed table-bordered" id="ot_tbl">
		<tr>
			<th>Select OT</th>
			<th>
				<select id="ot" onchange="clear_error(this.id)">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT `ot_area_id`,`ot_area_name` FROM `ot_area_master` ORDER BY `ot_area_name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ot_area_id'];?>" <?php if($r['ot_area_id']==$o['ot_area_id']){echo "selected='selected'";}?>><?php echo $r['ot_area_name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th>Select Procedure</th>
			<th>
				<input type="text" list="browsrs" class="span5" id="pr" value="<?php echo $o['procedure_id'];?>" placeholder="Procedure" />
				<datalist id="browsrs">
				<?php
					$qq = mysqli_query($link,"SELECT `name` FROM `clinical_procedure` ORDER BY `name`");
					while($cc=mysqli_fetch_array($qq))
					{
						echo "<option value='$cc[name]'>";
					}
				?>
				</datalist>
				<!--<select id="pr">
					<option value="0">Select</option>
					<?php
					//$qr=mysqli_query($link,"SELECT `id`,`name` FROM `clinical_procedure` ORDER BY `name`");
					//while($rr=mysqli_fetch_array($qr))
					{
					?>
					<option value="<?php echo $rr['id'];?>" <?php if($rr['id']==$o['procedure_id']){echo "selected='selected'";}?>><?php echo $rr['name'];?></option>
					<?php
					}
					?>
				</select>-->
			</th>
		</tr>
		<tr>
			<th>Date</th>
			<th><input type="text" id="ot_date" value="<?php echo $o['ot_date'];?>" placeholder="Date" /></th>
			<th>Requesting Doctor</th>
			<th>
				<select id="doc">
					<option value="0">Select</option>
					<?php
					//$qry=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5'");
					$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					while($rrr=mysqli_fetch_array($qry))
					{
					?>
					<option value="<?php echo $rrr['consultantdoctorid'];?>" <?php if($rrr['consultantdoctorid']==$o['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $rrr['Name'];?></option>
					<?php
					}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<th>Start Time</th>
			<th><input type="text" id="st_time" placeholder="Start Time" /></th>
			<th>End Time</th>
			<th><input type="text" id="en_time" placeholder="End Time" /></th>
		</tr>
		<tr>
			<th>Diagnosis</th>
			<th colspan="3"><input type="text" id="diag" placeholder="Diagnosis" style="width:80%;"></th>
		</tr>
		<tr>
			<th>Remarks</th>
			<th colspan="3"><textarea id="rem" placeholder="Remarks" style="width:80%;resize:none;"></textarea></th>
		</tr>
		<tr>
			<th>OT Resources</th>
			<th>
				<select id="ot_type" onchange="ot_resource_list()">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `ot_type_master` ORDER BY `type`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['type_id'];?>"><?php echo $r['type'];?></option>
					<?php
					}
					?>
				</select>
			</th>
			<th id="resource_list">
				<select id="rs">
					<option value="0">Select</option>
				</select>
			</th>
			<th>
				<input type="text" id="sel_val" style="display:none;" />
				<span class="text-right"><button type="button" class="btn btn-primary" onclick="add_res()"><i class="icon icon-plus"></i> Add</button></span>
			</th>
		</tr>
		<tr id="end_tr">
			<td colspan="4" style="text-align:center;"><button type="button" id="sav_shed" class="btn btn-primary" onclick="save_shed()"><i class="icon icon-save"></i> Save</button></td>
		</tr>
	</table>
	<script>
		$("#ot_date").datepicker({dateFormat: 'yy-mm-dd'});
		$("#st_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$("#en_time").timepicker({minutes: {starts: 0,interval: 05,}});
		$('#st_time').on('change', function()
		{
			$(this).css("border","");
		});
		$('#en_time').on('change', function()
		{
			$(this).css("border","");
		});
		$('#ot_date').on('change', function()
		{
			$(this).css("border","");
		});
	</script>
	<?php
	}
}

if($_POST["type"]=="ot_resource_list")
{
	$ot_type=$_POST['ot_type'];
	$lk=mysqli_fetch_array(mysqli_query($link,"SELECT `link` FROM `ot_type_master` WHERE `type_id`='$ot_type'"));
	if($lk['link']>0)
	{
	?>
	<select id="rs">
		<option value="0">Select</option>
		<?php
		$q=mysqli_query($link,"SELECT `emp_id` FROM `ot_resource_link` WHERE `type_id`='$ot_type'");
		while($rr=mysqli_fetch_array($q))
		{
			$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$rr[emp_id]'"));
		?>
		<option value="<?php echo $rr['emp_id'];?>"><?php echo $e['name'];?></option>
		<?php
		}
		?>
	</select>
	<?php
	}
	else
	{
		?>
		<input type="hidden" id="rs" value="" />
		<?php
	}
}

if($_POST["type"]=="res_type")
{
	$typ=$_POST['typ'];
	$emp=$_POST['emp'];
	$o=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$typ'"));
	$e=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$emp'"));
	//$amt=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_id` FROM `ot_resource_master` WHERE `type_id`='$typ' AND `emp_id`='$emp'"));
	echo $o['type']."@".$e['name']."@";
}

if($_POST["type"]=="load_procedure_list")
{
	$dept=$_POST['dept'];
	$grade=$_POST['grade'];
	
	if($dept=="others")
	{
		?>
		
		<?php
	}
	else
	{
	$qry="SELECT `procedure_id`,`name` FROM `ot_clinical_procedure` WHERE `ot_dept_id`='$dept' ";
	$qry.="ORDER BY `name`";
	
	$qr=mysqli_query($link,$qry);
	?>
	<select id="pr" onchange="clear_error(this.id)" class="span6">
		<option value="0">Select</option>
		<?php
		while($rr=mysqli_fetch_array($qr))
		{
		?>
		<option value="<?php echo $rr['procedure_id'];?>"><?php echo $rr['name'];?></option>
		<?php
		}
		?>
		<option value="others">Others</option>
	</select>
	<?php
	}
}

if($_POST["type"]=="load_resourse_list")
{
	$grade=$_POST['grade'];
	$cabin=$_POST['cabin'];
	?>
	<select id="ot_type" onchange="ot_resource_list()">
		<option value="0">Select</option>
	<?php
		//$q=mysqli_query($link,"SELECT * FROM `ot_type_master` WHERE `link`>0 ORDER BY `seq`");
		//$q=mysqli_query($link,"SELECT b.* FROM `ot_resource_master` a, `ot_type_master` b WHERE a.`type_id`=b.`type_id` AND a.`grade_id`='$grade' AND b.`link`>'0' ORDER BY b.`type`");
		$q=mysqli_query($link,"SELECT DISTINCT a.`type_id` FROM `ot_resource_master` a, `ot_type_master` b WHERE a.`type_id`=b.`type_id` AND a.`grade_id`='$grade' AND a.`ot_cabin_id`='$cabin' AND b.`link`>'0' ORDER BY b.`seq`");
		while($r=mysqli_fetch_array($q))
		{
			$nm=mysqli_fetch_array(mysqli_query($link,"SELECT `type` FROM `ot_type_master` WHERE `type_id`='$r[type_id]'"));
		?>
		<option value="<?php echo $r['type_id'];?>"><?php echo $nm['type'];?></option>
		<?php
		}
		?>
	</select>
	<?php
}

if($_POST["type"]=="ot_upd_shed")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$shed=$_POST['show'];
	$ot=$_POST['ot'];
	$ot_typ=$_POST['ot_typ'];
	$ot_dept=$_POST['ot_dept'];
	$new_dept=$_POST['new_dept'];
	$grade=$_POST['grade'];
	$anas=$_POST['anas'];
	$new_anas=$_POST['new_anas'];
	$cabin=$_POST['cabin'];
	$pr=$_POST['pr'];
	$pr_name=$_POST['pr_name'];
	$ot_date=$_POST['ot_date'];
	$st_time=$_POST['st_time'];
	$en_time=$_POST['en_time'];
	$diag=mysqli_real_escape_string($link,$_POST['diag']);
	$rem=mysqli_real_escape_string($link,$_POST['rem']);
	$doc=$_POST['doc'];
	$usr=$_POST['usr'];
	$det=$_POST['det'];
	$det=explode("#@#",$det);
	
	if($ot_dept=="others")
	{
		mysqli_query($link,"INSERT INTO `ot_dept_master`(`ot_dept_name`) VALUES ('$new_dept')");
		$dpt=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_id` FROM `ot_dept_master` WHERE `ot_dept_name`='$new_dept' ORDER BY `ot_dept_id` DESC"));
		$ot_dept=$dpt['ot_dept_id'];
		mysqli_query($link,"INSERT INTO `ot_clinical_procedure`(`ot_dept_id`, `name`, `user`) VALUES ('$ot_dept','$pr_name','$usr')");
		$prr=mysqli_fetch_array(mysqli_query($link,"SELECT `procedure_id` FROM `ot_clinical_procedure` WHERE `ot_dept_id`='$ot_dept' AND `user`='$usr' ORDER BY `procedure_id` DESC"));
		$pr=$prr['procedure_id'];
	}
	if($pr=="others")
	{
		mysqli_query($link,"INSERT INTO `ot_clinical_procedure`(`ot_dept_id`, `name`, `user`) VALUES ('$ot_dept','$pr_name','$usr')");
		$prr=mysqli_fetch_array(mysqli_query($link,"SELECT `procedure_id` FROM `ot_clinical_procedure` WHERE `ot_dept_id`='$ot_dept' AND `user`='$usr' ORDER BY `procedure_id` DESC"));
		$pr=$prr['procedure_id'];
	}
	if($anas=="others")
	{
		$new_anas=strtolower($new_anas);
		$new_anas=ucwords($new_anas);
		mysqli_query($link,"INSERT INTO `ot_anesthesia_types`(`name`) VALUES ('$new_anas')");
		$ans=mysqli_fetch_array(mysqli_query($link,"SELECT `anesthesia_id` FROM `ot_anesthesia_types` WHERE `name`='$new_anas' DESC"));
		$anas=$ans['anesthesia_id'];
	}
	mysqli_query($link,"UPDATE `ot_schedule` SET `ot_date`='$ot_date',`start_time`='$st_time',`end_time`='$en_time',`ot_no`='$ot',`ot_type`='$ot_typ',`ot_dept_id`='$ot_dept',`grade_id`='$grade',`anesthesia_id`='$anas',`diagnosis`='$diag',`remarks`='$rem',`requesting_doc`='$doc',`procedure_id`='$pr',`ot_cabin_id`='$cabin' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	mysqli_query($link,"UPDATE `ot_book` SET `procedure_id`='$pr',`consultantdoctorid`='$doc',`ot_date`='$ot_date',`ot_cabin_id`='$ot_cabin_id' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `schedule_id`='$shed'");
	mysqli_query($link,"DELETE FROM `ot_resource` WHERE `schedule_id`='$shed'");
	mysqli_query($link,"DELETE FROM `ot_pat_service_details` WHERE `schedule_id`='$shed'");
	mysqli_query($link,"DELETE FROM `doctor_service_done` WHERE `schedule_id`='$shed'");
	foreach($det as $dtt)
	{
		if($dtt)
		{
			$dt=explode("@",$dtt);
			$rss=$dt[0];
			$rdoc=$dt[1];
			if($rss)
			{
				mysqli_query($link,"INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$shed','$rss','$rdoc')");
				//echo "INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$i[max]','$rss','$rdoc')";
			}
		}
	}
	echo $shed."@@@Updated";
}


if($_POST["type"]=="ot_save_shed")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ot=$_POST['ot'];
	$ot_typ=$_POST['ot_typ'];
	$ot_dept=$_POST['ot_dept'];
	$new_dept=$_POST['new_dept'];
	$grade=$_POST['grade'];
	$anas=$_POST['anas'];
	$new_anas=$_POST['new_anas'];
	$cabin=$_POST['cabin'];
	$pr=$_POST['pr'];
	$pr_name=$_POST['pr_name'];
	$ot_date=$_POST['ot_date'];
	$st_time=$_POST['st_time'];
	$en_time=$_POST['en_time'];
	$diag=mysqli_real_escape_string($link,$_POST['diag']);
	$rem=mysqli_real_escape_string($link,$_POST['rem']);
	$doc=$_POST['doc'];
	$usr=$_POST['usr'];
	$det=$_POST['det'];
	$det=explode("#@#",$det);
	
	if($ot_dept=="others")
	{
		mysqli_query($link,"INSERT INTO `ot_dept_master`(`ot_dept_name`) VALUES ('$new_dept')");
		$dpt=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_dept_id` FROM `ot_dept_master` WHERE `ot_dept_name`='$new_dept' ORDER BY `ot_dept_id` DESC"));
		$ot_dept=$dpt['ot_dept_id'];
		mysqli_query($link,"INSERT INTO `ot_clinical_procedure`(`ot_dept_id`, `name`, `user`) VALUES ('$ot_dept','$pr_name','$usr')");
		$prr=mysqli_fetch_array(mysqli_query($link,"SELECT `procedure_id` FROM `ot_clinical_procedure` WHERE `ot_dept_id`='$ot_dept' AND `user`='$usr' ORDER BY `procedure_id` DESC"));
		$pr=$prr['procedure_id'];
	}
	if($pr=="others")
	{
		mysqli_query($link,"INSERT INTO `ot_clinical_procedure`(`ot_dept_id`, `name`, `user`) VALUES ('$ot_dept','$pr_name','$usr')");
		$prr=mysqli_fetch_array(mysqli_query($link,"SELECT `procedure_id` FROM `ot_clinical_procedure` WHERE `ot_dept_id`='$ot_dept' AND `user`='$usr' ORDER BY `procedure_id` DESC"));
		$pr=$prr['procedure_id'];
	}
	if($anas=="others")
	{
		$new_anas=strtolower($new_anas);
		$new_anas=ucwords($new_anas);
		mysqli_query($link,"INSERT INTO `ot_anesthesia_types`(`name`) VALUES ('$new_anas')");
		$ans=mysqli_fetch_array(mysqli_query($link,"SELECT `anesthesia_id` FROM `ot_anesthesia_types` WHERE `name`='$new_anas' DESC"));
		$anas=$ans['anesthesia_id'];
	}

	mysqli_query($link,"INSERT INTO `ot_schedule`(`patient_id`, `ipd_id`, `ot_date`, `start_time`, `end_time`, `ot_no`, `ot_type`, `ot_dept_id`, `grade_id`, `anesthesia_id`, `diagnosis`, `remarks`, `requesting_doc`, `procedure_id`, `date`, `time`, `user`, `leaved`, `ot_cabin_id`) VALUES ('$uhid','$ipd','$ot_date','$st_time','$en_time','$ot','$ot_typ','$ot_dept','$grade','$anas','$diag','$rem','$doc','$pr','$date','$time','$usr','0','$cabin')");
	$i=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(schedule_id) as max FROM `ot_schedule` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `user`='$usr'"));
	foreach($det as $dtt)
	{
		if($dtt)
		{
			$dt=explode("@",$dtt);
			$rss=$dt[0];
			$rdoc=$dt[1];
			if($rss)
			{
				mysqli_query($link,"INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$i[max]','$rss','$rdoc')");
				//echo "INSERT INTO `ot_resource`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$i[max]','$rss','$rdoc')";
			}
		}
	}
	//mysqli_query($link,"UPDATE `ot_book` SET `scheduled`='1', `schedule_id`='$i[max]' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	mysqli_query($link,"INSERT INTO `ot_book`(`patient_id`, `ipd_id`, `ot_area_id`, `procedure_id`, `consultantdoctorid`, `ot_date`, `scheduled`, `pac_status`, `date`, `time`, `user`, `schedule_id`, `ot_cabin_id`) VALUES ('$uhid','$ipd','$ot','$pr','$doc','$ot_date','1','0','$date','$time','$usr','$i[max]','$cabin')");
	mysqli_query($link,"INSERT INTO `ot_process`(`patient_id`, `ipd_id`, `schedule_id`, `user`, `time`, `date`) VALUES ('$uhid','$ipd','$i[max]','$usr','$time','$date')");
	echo $i['max']."@@@Saved";
}

if($_POST["type"]=="load_cabin_list")
{
	$grade=$_POST['grade'];
	?>
	<select id="cabin" onchange="clear_error(this.id)">
		<option value="0">Select</option>
	<?php
	//$q=mysqli_query($link,"SELECT b.* FROM `ot_cabin_rate` a,`ot_cabin_master` b WHERE a.`ot_cabin_id`=b.`ot_cabin_id` AND a.`grade_id`='$grade'");
	$q=mysqli_query($link,"SELECT DISTINCT `ot_cabin_id` FROM `ot_resource_master` WHERE `grade_id`='$grade' AND `ot_cabin_id`>0");
	while($r=mysqli_fetch_array($q))
	{
		$cab=mysqli_fetch_array(mysqli_query($link,"SELECT `ot_cabin_name` FROM `ot_cabin_master` WHERE `ot_cabin_id`='$r[ot_cabin_id]'"));
	?>
		<option value="<?php echo $r['ot_cabin_id'];?>"><?php echo $cab['ot_cabin_name'];?></option>
	<?php
	}
	?>
	</select>
	<?php
}

if($_POST["type"]=="load_grade_list")
{
	$ot_dept=$_POST['ot_dept'];
	?>
	<select id="grade" onchange="clear_error(this.id)">
		<option value="0">Select</option>
	<?php
	$q=mysqli_query($link,"SELECT * FROM `ot_grade_master` WHERE `ot_dept_id`='$ot_dept' ORDER BY `grade_name`");
	while($r=mysqli_fetch_array($q))
	{
	?>
		<option value="<?php echo $r['grade_id'];?>"><?php echo $r['grade_name'];?></option>
	<?php
	}
	?>
	</select>
	<?php
}

if($_POST["type"]=="ooooo")
{
	
}
?>
