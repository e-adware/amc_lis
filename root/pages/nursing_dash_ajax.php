<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d");
$time=date('H:i:s');
//------------------------------------------------------------------------------------------------//

if($_POST["type"]=="medicine_list")
{
	$dname=$_POST['val'];
?>
	<table style="background-color:#FFF" border="1" class="sec_table table table-bordered" id="center_table" width="600px">
	<th>Drug Name</th>
	<?php
	if($dname)
	{
		$d=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `ph_item_master` where `item_name` like '$dname%' order by `item_name`");
	}
	else
	{
		$d=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `ph_item_master` order by `item_name`");
	}

	$i=1;
	while($d1=mysqli_fetch_array($d))
	{
		?>
		<tr onclick="select_med_ind('<?php echo $d1['item_code'];?>','<?php echo $d1['item_name'];?>')" style="cursor:pointer" <?php echo "id=ind".$i;?>>
			<td><?php echo $d1['item_name'];?>
				<div <?php echo "id=mdname".$i;?> style="display:none;">
				<?php echo "#".$d1['item_code']."#".$d1['item_name'];?>
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

if($_POST["type"]=="ipd_pat_medicine_indent")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$q=mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `type`='2'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
	?>
	<table class="table table-bordered table-condensed" id="" width="100%">
		<tr>
			<th rowspan="2">#</th>
			<th rowspan="2">Drug Name</th>
			<th colspan="2"><center>Quantity</center></th>
			<th rowspan="2"><center>Date Time</center></th>
			<th rowspan="2"><center>User</center></th>
		</tr>
		<tr>
			<th>Claimed</th>
			<th>Received</th>
		</tr>
		<?php
		$n=1;
		while($r=mysqli_fetch_array($q))
		{
			$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
			$dis_none="";
			if($r["status"]>0)
			{
				$dis_none="style='display:none;'";
			}
			$emp_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$usr' "));
		?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $m['item_name'];?></td><td><?php echo $r['quantity'];?> </td>
			<td><?php echo $r['status'];?></td>
			<td>
				<?php echo convert_date_g($r['date']);?>
				<?php echo convert_time($r['time']);?>
			</td>
			<td>
				<?php echo $emp_info['name'];?>
				<button class="btn btn-mini btn-danger text-right" onClick="del_indent_medicine('<?php echo $r["slno"]; ?>')" <?php echo $dis_none; ?>><i class="icon-remove-sign"></i></button>
			</td>
		</tr>
		<?php
			$n++;
		}
		?>
	</table>
	<button type="button" class="btn btn-info" id="indad" onclick="$('#hide_ind_list').show();$('#indad').hide();$('#ins_ind').hide()"><i class="icon-plus"></i> Add New</button>
	<?php
	}
	else
	{
	?>
	<button type="button" class="btn btn-info" id="indad" onclick="$('#hide_ind_list').show();$('#indad').hide();$('#ins_ind').hide()"><i class="icon-plus"></i> Add</button>
	<?php
	}
	?>
	<div id="hide_ind_list" style="display:none;">
		<table class="table table-condensed" id="">
			<tr>
				<td>
					Drug Name: <input type="text" class="span6" id="ind_med" onFocus="load_ind_medi()" onkeyup="load_ind_medi1(this.value,event)" onBlur="javascript:$('#ind_med_list').fadeOut(500)" />
					<input type="text" class="span6" id="mediid" style="display:none;" />
					<div id="ind_med_list">
					</div>
				</td>
			</tr>
			<tr id="ind_data" style="display:none;">
				<td>
					Quantity: <input type="text" class="span1" onkeyup="meditab(this.id,event)" id="qnt" placeholder="Quantity" />
					<button type="button" class="btn btn-primary" id="indsv" onclick="add_ind_data()"><i class="icon-plus"></i> Add</button>
					<button type="button" class="btn btn-danger" onclick="$('#ind_med').val('');$('#mediid').val('');$('#indad').show();$('#select_load').html('');$('#ind_data').hide(500);$('#hide_ind_list').hide(500)"><i class="icon-ban-circle"></i> Cancel</button>
				</td>
			</tr>
			<tr>
				<td id="select_load">
				
				</td>
			</tr>
			<tr>
				<td>
					<span class="text-right"><button type="button" class="btn btn-primary" id="ins_ind" onclick="insert_final_ind()"><i class="icon-file"></i> Save</button></span>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

if($_POST["type"]=="load_ind_medi_list")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$test=$_POST['test'];

	if($test=="")
	{
		$q="select * from ph_item_master order by item_name";
	}
	else
	{
		$q="select * from ph_item_master where item_name like '$test%' order by item_name";
	}

	$data=mysqli_query($link, $q);
	?>

	<table class="table   table-bordered table-condensed" border="1" id="test_table" width="100%">
		<tr>
			<th>Sl No</th>
			<th>Drug Name</th>
			<!--<th>Rate</th>--><div id="msgg" style="display:none;position:absolute;top:15%;left:45%;font-size:22px;color:#d00;"></div>
		</tr>
	<?php
	$i=1;
	while($d=mysqli_fetch_array($data))
	{
		$drate=$d['rate'];
		
		?>
		<tr <?php echo "id=td".$i;?> onclick="$('#ind_med').focus()" style="cursor:pointer">
			<td width="5%" class=ind<?php echo $i;?> id=ind<?php echo $i;?>>
				<?php echo $i;?><input type="hidden" class="ind<?php echo $i;?>" value="<?php echo $d['item_code'];?>"/>
			</td>
			<td style="text-align:left" width="35%" <?php echo "class=ind".$i;?>>
				<?php echo $d['item_name'];?>
			</td>
		</tr>
		<?php
		$i++;
	}
	?>
	<tr>
		<td colspan="2">
			<div id="select_med">
			
			</div>
		</td>
	</tr>
	</table>
	<?php
}

if($_POST["type"]=="insert_final_ind")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$det=$_POST['det'];
	$usr=$_POST['usr'];
	
	$type=2; // Nursning dashboard Indent
	
	$val=explode("#gg#",$det);
	$ind=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(indent_num) as max FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd'"));
	$in=$ind['max']+1;
	foreach($val as $dtt)
	{
		if($dtt)
		{
			$dt=explode("@@",$dtt);
			$med=$dt[0];
			$qnt=$dt[1];
			if($med && $qnt)
			{
				mysqli_query($link, "INSERT INTO `ipd_pat_medicine_indent`(`patient_id`, `ipd_id`, `indent_num`, `item_code`, `quantity`, `status`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$in','$med','$qnt','0','$date','$time','$usr')");
				
				mysqli_query($link," INSERT INTO `patient_medicine_detail`(`patient_id`, `pin`, `indent_num`, `item_code`, `dosage`, `instruction`, `quantity`, `status`, `date`, `time`, `user`, `type`) VALUES ('$uhid','$ipd','$in','$med','','0','$qnt','0','$date','$time','$usr','$type') ");
			}
		}
	}
}

if($_POST["type"]=="ipd_pat_admit")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid'"));
	$ward=$d['ward_id'];
	$bed=$d['bed_id'];
	
	mysqli_query($link,"insert into ipd_pat_bed_details(patient_id,ipd_id,ward_id,bed_id,user,time,date) values('$uhid','$ipd','$ward','$bed','$usr','$time','$date')");
	mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd','$ward','$bed','1','$usr','$time','$date')");
	mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid'");
	echo "Admited";
}

if($_POST["type"]=="load_bed_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid'"));
	echo $n;
}

if($_POST["type"]=="nursing_bed_transfer")
{
	$uhid=$_POST['uhid'];
	$val=$_POST['val'];
	?>
	<h3>Bed Details</h3>
	
	<?php
	$uhid=$_POST[uhid];
	$ward=mysqli_query($link,"select * from ward_master order by ward_id");
	while($w=mysqli_fetch_array($ward))
	{
		echo "<div class='ward'>";
		echo "<b>$w[name]</b> <br/>";
		
		
		$i=0;
		$beds=mysqli_query($link,"select distinct room_id,room_no from room_master where ward_id='$w[ward_id]' order by room_no");
		while($b=mysqli_fetch_array($beds))
		{
			echo "<div style='margin:10px 0px 0px 10px'>";
			echo "<b>Room No: $b[room_no]</b> <br/>";
			$room_det=mysqli_query($link,"select * from bed_master where room_id='$b[room_id]'");
			
			while($rd=mysqli_fetch_array($room_det))
			{
			
			$style="width:50px;margin-left:10px;";
			$chk_bd=mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
			if(mysqli_num_rows($chk_bd)>0)
			{
				if(mysqli_num_rows(mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]' and patient_id='$uhid'"))>0)
				{
					$style.="background-color:#5bc0de";
					echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"nursing_bed_asign('$w[ward_id]','$rd[bed_id]','$w[name]','$rd[bed_no]')\">$rd[bed_no]</span>";
				}
				else
				{
					$style.="background-color:#ff8a80";
					echo "<span class='btn' style='$style' id='$rd[bed_id]' >$rd[bed_no]</span>";
				}
			}
			else if($rd[status]==1)
			{
				$style.="background-color:#ffbb33";
				echo "<span class='btn' style='$style' id='$rd[bed_id]' >$rd[bed_no]</span>";
			}
			else
			{
				$chk_bd_main=mysqli_query($link,"select * from ipd_pat_bed_details where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
				
				if(mysqli_num_rows($chk_bd_main)>0)
				{
					$style.="background-color:#5cb85c";
					echo "<span class='btn' style='$style' id='$rd[bed_id]'>$rd[bed_no]</span>";
				}
				else
				{
					echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"nursing_bed_asign('$w[ward_id]','$rd[bed_id]','$w[name]','$rd[bed_no]')\">$rd[bed_no]</span>";
				}
			}
			
			if($i==10)
			{
				$i=0;
				echo "<br/>";
			}
			else
			{
				$i++;
			}
			}
			echo "</div>";
		}
		
		echo "</div> <hr/>";
	}
}

if($_POST["type"]=="nursing_bed_asign")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$w_id=$_POST['w_id'];
	$b_id=$_POST['b_id'];
	
	mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
	mysqli_query($link,"insert into ipd_bed_details_temp(patient_id,ipd_id,ward_id,bed_id) values('$uhid','$ipd','$w_id','$b_id')");
}

if($_POST["type"]=="bed_assign_temp")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$q=mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$v=mysqli_fetch_array($q);
		$ward=$v['ward_id'];
		$bed=$v['bed_id'];
		$wname=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$ward'"));
		$bname=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$bed'"));
		echo "<b>Selected</b><br/>Ward: ".$wname['name']."<br/>Bed No: ".$bname['bed_no']."<input type='hidden' id='ward_id' value=".$ward." /><input type='hidden' id='bed_id' value=".$bed." />";
	}
}

if($_POST["type"]=="bed_assign_ok")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ward=$_POST['ward'];
	$bed=$_POST['bed'];
	$usr=$_POST['usr'];
	$edit=$_POST['edit'];
	
	if($edit=='0') // Transfer
	{
		$old=mysqli_fetch_array(mysqli_query($link,"SELECT `ward_id`, `bed_id` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		//mysqli_query($link,"insert into ipd_pat_bed_details(patient_id,ipd_id,ward_id,bed_id,user,time,date) values('$uhid','$ipd','$ward','$bed','$usr','$time','$date')");
		mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd','$old[ward_id]','$old[bed_id]','0','$usr','$time','$date')");
		mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd','$ward','$bed','1','$usr','$time','$date')");
		mysqli_query($link,"UPDATE `ipd_pat_bed_details` SET `ward_id`='$ward',`bed_id`='$bed' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
	}
	if($edit=='1')
	{
		// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' AND `type`='3' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
		
		$s_date_val=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_id`,`date` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$old_bed_id=$s_date_val["bed_id"];
		$start_date=$s_date_val["date"];
		//$start_date="2017-12-02";
		$end_date=date("Y-m-d");
		
		$diff=abs(strtotime($start_date)-strtotime($end_date));
		$diff=$diff/60/60/24;
		
		$charge_val=mysqli_fetch_array(mysqli_query($link,"SELECT `charge_id` FROM `bed_master` WHERE `bed_id`='$old_bed_id'"));
		$bed_charge_id=$charge_val["charge_id"];	
		
		for($i=0;$i<=$diff;$i++)
		{
			$n_date=date('Y-m-d', strtotime($start_date. ' + '.$i.' days'));
			
			// Edit Records
			$old_service=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$bed_charge_id' AND `date`='$n_date' "));
			
			mysqli_query($link," INSERT INTO `ipd_pat_service_details_edit`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `counter`, `bed_id`) VALUES ('$old_service[patient_id]','$old_service[ipd_id]','$old_service[group_id]','$old_service[service_id]','$old_service[service_text]','$old_service[ser_quantity]','$old_service[rate]','$old_service[amount]','$old_service[days]','$old_service[user]','$old_service[time]','$old_service[date]','$counter_num','$old_service[bed_id]') ");
			///////
			mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$bed_charge_id' AND `date`='$n_date'");
			
			$bed_other_charge_qry=mysqli_query($link,"SELECT `charge_id` FROM `bed_other_charge` WHERE `bed_id`='$old_bed_id'");
			while($bed_other_charge=mysqli_fetch_array($bed_other_charge_qry))
			{
				$bed_other_charge_id=$bed_other_charge["charge_id"];
				
				// Edit Records
				$old_service_other=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$bed_other_charge_id' AND `date`='$n_date' "));
				
				mysqli_query($link," INSERT INTO `ipd_pat_service_details_edit`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `counter`, `bed_id`) VALUES ('$old_service_other[patient_id]','$old_service_other[ipd_id]','$old_service_other[group_id]','$old_service_other[service_id]','$old_service_other[service_text]','$old_service_other[ser_quantity]','$old_service_other[rate]','$old_service_other[amount]','$old_service_other[days]','$old_service_other[user]','$old_service_other[time]','$old_service_other[date]','$counter_num','$old_service_other[bed_id]') ");
				///////
				
				mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `service_id`='$bed_other_charge_id' AND `date`='$n_date'");
				
			}
			
		}
		
		// Edit reports
		$ipd_bed_detail_details=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
		mysqli_query($link," INSERT INTO `ipd_pat_bed_details_edit`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`, `counter`) VALUES ('$ipd_bed_detail_details[patient_id]','$ipd_bed_detail_details[ipd_id]','$ipd_bed_detail_details[ward_id]','$ipd_bed_detail_details[bed_id]','$ipd_bed_detail_details[user]','$ipd_bed_detail_details[time]','$ipd_bed_detail_details[date]','$counter_num') ");
		///////
		
		$mx_slno=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(`slno`) AS `MX` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `alloc_type`='1'"));
		$slno=$mx_slno["MX"];
		
		mysqli_query($link," UPDATE `ipd_bed_alloc_details` SET `ward_id`='$ward',`bed_id`='$bed',`user`='$usr' WHERE `slno`='$slno' ");
		
		mysqli_query($link,"UPDATE `ipd_pat_bed_details` SET `ward_id`='$ward',`bed_id`='$bed',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		
		mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
		
		// edit counter record
			mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$uhid','$ipd','$date','$time','$usr','3','$counter_num') ");
		
	}
}

if($_POST["type"]=="clr_bed_assign")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
}

if($_POST["type"]=="ipd_disc_summary")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$admit_reason=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_admit_reason` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
	
	?>
	<table class="table table-condensed" id="hist_table">
		<tr>
			<th>Reason for admission</th>
			<td colspan="3"><textarea style="resize:none;width:96%" name="admit_reason" onkeyup="tab(this.id,event)" id="admit_reason"><?php echo $admit_reason["admit_reason"]; ?></textarea></td>
		</tr>
		<tr>
			<td colspan="4"><span style="float:right;"><input type="button" id="reason_save" class="btn btn-info" value="Save" onclick="reason_save_click()" ></span></td>
		</tr>
	<?php
		$q=mysqli_query($link,"SELECT * FROM `ipd_pat_complaints` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		$num=mysqli_num_rows($q);
		$nm=1;
		if($num>0)
		{
		while($r=mysqli_fetch_array($q))
		{
	?>
		<tr class="cc">
			<th>Chief Complaints</th>
			<td>
				<input type="text" id="chief<?php echo $nm;?>" list="chief_comp_list" value="<?php echo $r['comp_one']; ?>" onkeyup="sel_chief(<?php echo $nm;?>,event)" />
				<datalist id="chief_comp_list" style="height:0;">
			<?php
				$chief_comp_qry=mysqli_query($link," SELECT `complain` FROM `complain_master` ORDER BY `complain` ");
				while($chief_comp=mysqli_fetch_array($chief_comp_qry))
				{
					echo "<option value='$chief_comp[complain]'></option>";
				}
			?>
				</datalist>
			</td>
			<td>
				<b>For</b> 
				<select id="cc<?php echo $nm;?>" class="span2" onkeyup="sel_chief(<?php echo $nm;?>,event)">
					<option value="0">Select</option>
					<?php
					for($n=1;$n<=30;$n++)
					{
					?>
					<option value="<?php echo $n;?>" <?php if($n==$r['comp_two']){echo "selected='selected'";}?>><?php echo $n;?></option>
					<?php
					}
					?>
				</select>
				<select id="tim<?php echo $nm;?>" class="span2" onkeyup="sel_chief(<?php echo $nm;?>,event)">
					<option value="0">--Select--</option>
					<option value="Minutes" <?php if($r['comp_three']=="Minutes"){echo "selected='selected'";}?>>Minutes</option>
					<option value="Hours" <?php if($r['comp_three']=="Hours"){echo "selected='selected'";}?>>Hours</option>
					<option value="Days" <?php if($r['comp_three']=="Days"){echo "selected='selected'";}?>>Days</option>
					<option value="Week" <?php if($r['comp_three']=="Week"){echo "selected='selected'";}?>>Week</option>
					<option value="Month" <?php if($r['comp_three']=="Month"){echo "selected='selected'";}?>>Month</option>
					<option value="Year" <?php if($r['comp_three']=="Year"){echo "selected='selected'";}?>>Year</option>
				</select>
			</td>
			<td>
			<?php if($nm==1){ ?>
				<span style="float:right"><input type="button" id="addmore" class="btn btn-info" onclick="add_row(1)" value="Add More" <?php echo $dis_all; ?> ></span>
			<?php }else{?>
				<span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span>
				<?php } ?>
			</td>
		</tr>
	<?php
	$nm++;
		}
		}
		else
		{
		?>
		<tr class="cc">
			<th>Chief Complaints</th>
			<td>
				<input type="text" id="chief1" list="chief_comp_list" value="" onkeyup="sel_chief(1,event)" />
				<datalist id="chief_comp_list" style="height:0;">
			<?php
				$chief_comp_qry=mysqli_query($link," SELECT `complain` FROM `complain_master` ORDER BY `complain` ");
				while($chief_comp=mysqli_fetch_array($chief_comp_qry))
				{
					echo "<option value='$chief_comp[complain]'></option>";
				}
			?>
				</datalist>
			</td>
			<td>
				<b>For</b> 
				<select id="cc1" class="span2" onkeyup="sel_chief(1,event)">
					<option value="0">Select</option>
					<?php
					for($n=1;$n<=30;$n++)
					{
					?>
					<option value="<?php echo $n;?>"><?php echo $n;?></option>
					<?php
					}
					?>
				</select>
				<select id="tim1" class="span2" onkeyup="sel_chief(1,event)">
					<option value="0">--Select--</option>
					<option value="Minutes">Minutes</option>
					<option value="Hours">Hours</option>
					<option value="Days">Days</option>
					<option value="Week">Week</option>
					<option value="Month">Month</option>
					<option value="Year">Year</option>
				</select>
			</td>
			<td>
				<span style="float:right"><input type="button" id="addmore" class="btn btn-info" onclick="add_row(1)" value="Add More" <?php echo $dis_all; ?> ></span>
			</td>
		</tr>
		<?php
		}
		//$h_q=mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		$h_q=mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' ORDER BY 'slno' DESC limit 0,1 ");
		$e=mysqli_num_rows($h_q);
		if($e>0)
		{
			$h_e=mysqli_fetch_array($h_q);
			$hist=$h_e['history'];
			$exm=$h_e['examination'];
		}
		else
		{
			$hist="";
			$exm="";
		}
		$s_i=mysqli_query($link,"SELECT * FROM `ipd_pat_significant_investigation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		$ei=mysqli_num_rows($h_q);
		if($ei>0)
		{
			$s_i_val=mysqli_fetch_array($s_i);
			$sig=$s_i_val['significant_finding'];
			$inv=$s_i_val['investigation_result'];
		}
		else
		{
			$sig="";
			$inv="";
		}
	?>
		<tr id="hh">
			<th colspan="4"><span style="float:right"><input type="button" id="p" class="btn btn-info" onclick="save_comp()" value="Save" /></span></th>
		</tr>
		<tr>
			<th colspan="4" style="background:#dddddd;"></th>
		</tr>
		<tr>
			<th>Case Summary</th>
			<td colspan="3"><textarea style="resize:none;width:96%" name="history" onkeyup="tab(this.id,event)" id="history"><?php echo $hist; ?></textarea></td>
		</tr>
		<tr>
			<th>Examination</th>
			<td colspan="3"><textarea style="resize:none;width:96%" name="exam" onkeyup="tab(this.id,event)" id="exam"><?php echo $exm; ?></textarea></td>
		</tr>
		<tr>
			<th>Significant Finding</th>
			<td colspan="3"><textarea style="resize:none;width:96%" name="significant_finding" onkeyup="tab(this.id,event)" id="significant_finding"><?php echo $sig; ?></textarea></td>
		</tr>
		<tr>
			<th>Investigation Result</th>
			<td colspan="3"><textarea style="resize:none;width:96%" name="investigation_result" onkeyup="tab(this.id,event)" id="investigation_result"><?php echo $inv; ?></textarea></td>
		</tr>
		<tr>
			<td colspan="4"><span style="float:right;"><input type="button" id="sav" class="btn btn-info" value="Save" onclick="save_exam()" ></span></td>
		</tr>
		<tr>
			<th colspan="4" style="background:#dddddd;"></th>
		</tr>
	</table>
	<table id="diag_table" class="table table-condensed table-bordered">
		<tr>
			<th>Provisional Diagnosis</th>
			<th>Order</th>
			<th>Certainity</th>
			<th></th>
		</tr>
		<?php
		$d_q=mysqli_query($link,"SELECT * FROM `ipd_pat_diagnosis` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		$n_d=mysqli_num_rows($d_q);
		if($n_d>0)
		{
			while($det_q=mysqli_fetch_array($d_q))
			{
		?>
		<tr>
			<td><?php echo $det_q['diagnosis']; ?></td>
			<td><?php echo $det_q['order']; ?></td>
			<td><?php echo $det_q['certainity']; ?></td>
			<td>
				<button type="button" class="btn btn-success btn-mini" onclick="edt_diag('<?php echo $det_q['slno'];?>')">Edit</button>
				<button type="button" class="btn btn-danger btn-mini" onclick="delete_diag('<?php echo $det_q['slno'];?>')"><i class="icon-remove"></i></button>
			</td>
		</tr>
		<?php
			}
		}
		?>
		<tr class="diag">
			<td>
				<input type="text" id="diag" list="chief_diag_list" class="span3" placeholder="Diagnosis" />
				<datalist id="chief_diag_list" style="height:0;">
				<?php
					$diagnosis_qry=mysqli_query($link," SELECT `diagnosis` FROM `diagnosis_master` ORDER BY `diagnosis` ");
					while($diagnosis=mysqli_fetch_array($diagnosis_qry))
					{
						echo "<option value='$diagnosis[diagnosis]'></option>";
					}
				?>
					</datalist>
			</td>
			<td><select id="ord" class="span2"><option value="0">Select</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td>
			<td><select id="cert" class="span2"><option value="0">Select</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td>
			<th><input type="button" class="btn btn-mini btn-info" id="ad" value="Add" onclick="add_row(2)" style="" /></th>
		</tr>
		<tr id="addiagnosis">
			<td colspan="5"><span style="float:right;"><input type="button" id="dasav" class="btn btn-info" value="Save" onclick="save_diagno()" /></span></td>
		</tr>
		<tr>
			<td colspan="5" style="background:#dddddd;"></td>
		</tr>
	</table>
	<?php
	$qryy=mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$nds=mysqli_num_rows($qryy);
	if($nds>0)
	{
		$det=mysqli_fetch_array($qryy);
		$course=$det['course'];
		$fd=$det['final_diagnosis'];
		$procedure_with_date=$det['procedure_with_date'];
		$v_bp=$det['final_bp'];
		$v_pulse=$det['final_pulse'];
		$v_temp=$det['final_temp'];
		$v_weight=$det['final_weight'];
		$foll=$det['follow_up'];
		$report_hospital=$det['report_hospital'];
		$next_visit=$det['next_visit'];
		$dis="";
		$value="Update";
	}
	else
	{
		$course="";
		$fd="";
		$procedure_with_date="";
		$v_bp="";
		$v_pulse="";
		$v_temp="";
		$v_weight="";
		$foll="";
		$report_hospital="";
		$next_visit="";
		//$dis="disabled='disabled'";
		$value="Save";
	}
	?>
	<table class="table table-condensed" id="">
			<tr>
				<td><b>Course in hospital</b><br/>
					<textarea id="course" placeholder="Course in hospital" onkeyup="tab(this.id,event)" style="width:96%;height:100px;resize:none;"><?php echo $course; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Final diagnosis</b><br/>
					<textarea id="final_diag" placeholder="Final diagnosis" onkeyup="tab(this.id,event)" style="width:96%;height:100px;resize:none;"><?php echo $fd; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>	Lists of procedures performed with date </b><br/>
					<textarea id="procedure_with_date" placeholder="Procedure performed with date" onkeyup="tab(this.id,event)" style="width:96%;height:100px;resize:none;"><?php echo $procedure_with_date; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Vital at time of discharge</b><br/>
					<table class="table table-condensed table-bordered" style="margin-bottom:0;">
						<tr>
							<th>BP</th>
							<th>Pulse</th>
							<th>Temp.</th>
							<th>Weight</th>
						</tr>
						<tr>
							<td><input type="text" id="v_bp" class="span1" value="<?php echo $v_bp; ?>" /></td>
							<td><input type="text" id="v_pulse" class="span1" value="<?php echo $v_pulse; ?>" /></td>
							<td><input type="text" id="v_temp" class="span1" value="<?php echo $v_temp; ?>" placeholder="Celsius" /></td>
							<td><input type="text" id="v_weight" class="span1" value="<?php echo $v_weight; ?>" /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td><b>Follow up</b><br/>
					<textarea id="foll" placeholder="Follow up" onkeyup="tab(this.id,event)" style="width:96%;height:100px;resize:none;"><?php echo $foll; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><b>Report to Hospital if you have: </b><br/>
					<textarea id="report_hospital" placeholder="" onkeyup="tab(this.id,event)" style="width:96%;height:100px;resize:none;"><?php echo $report_hospital; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<b>Next visit</b><br>
					<input type="text" class="datepicker" id="next_visit" value="<?php echo $next_visit; ?>">
				</td>
			</tr>
			<tr>
				<td>
					<button type="button" id="summ_btn" class="btn btn-info" onclick="insert_disc_summ()">
						<i class="icon-file"></i> <?php echo $value; ?>
					</button>
				</td>
			</tr>
		<tr>
			<td colspan="2" style="background:#dddddd;"></td>
		</tr>
			<tr>
				<td><b>Post Discharge Medication Plan</b><br/>
				</td>
			</tr>
			<?php
			$old_medi_check=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
			$mdc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_pat_medicine_final` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
			$medicine=$mdc['medicine'];
			?>
			<tr>
				<td>
					<?php
					if($old_medi_check>0)
					{
					?>
					<textarea id="post_med_det" style="width:95%;height:100px;resize:none;" placeholder="Drug details"><?php echo $medicine;?></textarea>
					<?php
					}
					else
					{
					?>
					<b>Drug Name</b> : <input type="text" name="medi" id="medi" class="span8" onFocus="load_medi_list()" onKeyUp="load_medi_list1(this.value,event)" onBlur="javascript:$('#med_div').fadeOut(500)" placeholder="Drug Name" <?php echo $dis_all; ?> >
					<input type="text" id="new_medi" class="span8" onkeyup="tab(this.id,event);$(this).val($(this).val().toUpperCase())" style="display:none;" placeholder="New Drug Name" />
					<button type="button" class="btn" id="new_btn" onclick="new_medi()">New</button>
					<button type="button" class="btn btn-danger" id="can_btn" style="display:none;" onclick="can_medi()">Cancel</button>
					<input type="hidden" id="medid" />
					<input type="hidden" id="mediname" />
					<div id="med_info"></div>
					<div id="med_div" align="center" style="margin-left: 93px;">
						<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
							<th>Drug Name</th>
							<?php
								$d=mysqli_query($link, "SELECT * FROM `ph_item_master` order by `item_name`");
								$i=1;
								while($d1=mysqli_fetch_array($d))
								{
							?>
								<tr onclick="select_med('<?php echo $d1['item_code'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type'];?>','<?php echo $d1['generic'];?>')" style="cursor:pointer" <?php echo "id=med".$i;?>>
									<td><?php echo $d1['item_name'];?>
										<div <?php echo "id=mdname".$i;?> style="display:none;">
										<?php echo "#".$d1['item_code']."#".$d1['item_name']."#".$d1['item_type']."#".$d1['generic'];?>
										</div>
									</td>
								</tr>
							<?php
								$i++;
								}
							?>
						</table>
					</div>
					<b>Instruction</b> :&nbsp; <input type="text" id="dos" list="doses" class="span8" placeholder="Dosage / Instruction" onkeyup="add_dose(event)" >
					<input type="text" class="span1" id="ph_quantity" placeholder="Quantity" onkeyup="ph_quantity(event)">
					<datalist id="doses" style="height: 0;">
					<?php
						$doss=mysqli_query($link,"SELECT * FROM `dosage_master`");
						while($d=mysqli_fetch_array($doss))
						{
							echo "<option value='$d[dosage]'>";
						}
					?>
					</datalist>
					<?php
					}
					?>
					
				</td>
			</tr>
			<tr id="item_tr" style="display:none;">
				<td>
					<div id="temp_item">
					
					</div>
				</td>
			</tr>
			<tr>
				<td style="text-align:center;">
					<?php
					if($old_medi_check>0)
					{
					?>
					<button type="button" id="sav_medi" class="btn btn-primary" onclick="save_final_medi()"><i class="icon-save"></i> Save</button>
					<?php
					}
					else
					{
					?>
					<button type="button" id="sav_medi" class="btn btn-primary" onclick="save_all_medi()"><i class="icon-save"></i> Save</button>
					<?php
					}
					?>
				</td>
			</tr>			
			<?php
			if($old_medi_check==0)
			{
			$drug_qry=mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `type`='3'");
			if(mysqli_num_rows($drug_qry)>0)
			{
			?>
			<tr>
				<td>
					<table class="table table-condensed">
						<tr style="background:#bbbbbb;color:#444444;">
							<th>#</th><th>Drug Name</th><th>Dosage / Instruction</th><th width="5%">Quantity</th><th width="5%">Remove</th>
						</tr>
						<?php
						$p=1;
						while($drg=mysqli_fetch_array($drug_qry))
						{
							$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `ph_item_master` WHERE `item_code`='$drg[item_code]'"));
						?>
						<tr>
							<td><?php echo $p;?></td>
							<td><?php echo $itm['item_name'];?></td>
							<td><?php echo $drg['dosage'];?></td>
							<td><?php echo $drg['quantity'];?></td>
							<td><i class="icon-remove icon-large" style="color:#980000;cursor:pointer;" onclick="del_med('<?php echo $drg['slno'];?>')"></i></td>
						</tr>
						<?php
						$p++;
						}
						?>
					</table>
				</td>
			</tr>
			<?php
			}
			}
			$disqry=mysqli_query($link,"SELECT * FROM `ipd_dischage_type` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
			$ndm=mysqli_num_rows($disqry);
			if($ndm>0)
			{
				$dtyp=mysqli_fetch_array($disqry);
				$tp=$dtyp['type'];
				$d_by=$dtyp['diagnosed_by'];
			}
			else
			{
				$tp=0;
				$d_by=0;
			}
			?>
			<tr>
				<td>
					<b>Discharge Type</b>
					<select id="dtype">
						<option value="0" <?php if($tp==0){echo "selected='selected'";} ?>>Select</option>
					<?php
						$dis_typ_qry=mysqli_query($link," SELECT * FROM `discharge_master` ORDER BY `discharge_name` ");
						while($dis_typ=mysqli_fetch_array($dis_typ_qry))
						{
							if($tp==$dis_typ['discharge_id']){ $sel_typ="selected"; }else{ $sel_typ=""; }
							echo "<option value='$dis_typ[discharge_id]' $sel_typ >$dis_typ[discharge_name]</option>";
						}
					?>
						
<!--
						<option value="1" <?php if($tp==1){echo "selected='selected'";} ?>>Routine</option>
						<option value="2" <?php if($tp==2){echo "selected='selected'";} ?>>Transfer to other type of healthcare facility</option>
						<option value="3" <?php if($tp==3){echo "selected='selected'";} ?>>Home Health Care</option>
						<option value="4" <?php if($tp==4){echo "selected='selected'";} ?>>Transfer to short term hospital</option>
						<option value="5" <?php if($tp==5){echo "selected='selected'";} ?>>In hospital death</option>
						<option value="6" <?php if($tp==6){echo "selected='selected'";} ?>>Left against medical advice</option>
						<option value="7" <?php if($tp==7){echo "selected='selected'";} ?>>Discharged alive, destination unknown</option>
-->
					</select>
					<b>Diagnosed By</b>
					<select id="diagnosed">
						<option value="0">Select</option>
						<?php
						$doc=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
						while($dr=mysqli_fetch_array($doc))
						{
						?>
						<option value="<?php echo $dr['consultantdoctorid']?>" <?php if($d_by==$dr['consultantdoctorid']){echo "selected='selected'";} ?>><?php echo $dr['Name'];?></option>
						<?php
						}
						?>
					</select>
					<button type="button" class="btn btn-primary" onclick="save_dis_type()">
						<i class="icon-save"></i> Save
					</button>
					<?php
						$pdis=" disabled";
						$final_bill=mysqli_query($link,"select * from ipd_advance_payment_details where patient_id='$uhid' and ipd_id='$ipd' and pay_type='Final'");
						$chk_final=mysqli_num_rows($final_bill);
						if($chk_final>0)
						{
							$pdis="";
						}
						$pdis="";
					?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right;">
				<button type="button" class="btn btn-primary" onclick="print_disc_summary()" <?php echo $dis." ".$pdis; ?>>
					<i class="icon-print"></i> Print
				</button>
				</td>
			</tr>
		</table>
	<?php
}

if($_POST["type"]=="ipd_pat_doc_list")
{
	$d="";
	$q=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
	while($r=mysqli_fetch_array($q))
	{
		$d.="<option value='".$r['consultantdoctorid']."'>".$r['Name']."</option>";
	}
	echo $d;
}

if($_POST["type"]=="ipd_add_medicine_post")
{
	$batch=$_POST['batch'];
	?>
	<table class="table table-condensed">
		<tr>
			<th width="20%">Drug Name</th>
			<td colspan="5">
				<input type="text" name="mediname" id="mediname" class="span5" />
			</td>
		</tr>
		<tr>
			<td colspan="6">
					<table class="table table-condensed">
						<tr>
							<th>Dosage</th>
							<td>
								<input type="text" id="dos" />
							</td>
							<th></th>
							<td></td>
							<th>Frequency</th>
							<td>
								<select id="freq" onkeyup="meditab(this.id,event)" onchange="calc_totday()" class="span2">
									<option value="0">Select</option>
									<option value="1">Immediately</option>
									<option value="2">Once a day</option>
									<option value="3">Twice a day</option>
									<option value="4">Thrice a day</option>
									<option value="5">Four times a day</option>
									<option value="6">Five times a day</option>
									<option value="7">Every hour</option>
									<option value="8">Every 2 hours</option>
									<option value="9">Every 3 hours</option>
									<option value="10">Every 4 hours</option>
									<option value="11">Every 5 hours</option>
									<option value="12">Every 6 hours</option>
									<option value="13">Every 7 hours</option>
									<option value="14">Every 8 hours</option>
									<option value="15">Every 10 hours</option>
									<option value="16">Every 12 hours</option>
									<option value="17">SOS</option>
									<option value="18">IV</option>
									<option value="19">IM</option>
									<option value="20">SUS</option>
									<!--<option value="17">On alternate days</option>
									<option value="18">Once a week</option>
									<option value="19">Twice a week</option>
									<option value="20">Thrice a week</option>
									<option value="21">Every 2 weeks</option>
									<option value="22">Every 3 weeks</option>
									<option value="23">Once a month</option>-->
								</select>
							</td>
							<th>Start Date</th>
							<td><input type="text" id="st_date" style="width:100px;" onkeyup="meditab(this.id,event)" /></td>
						</tr>
						<tr>
							<th>Duration</th>
							<td>
								<select id="dur" onkeyup="meditab(this.id,event)" onchange="calc_totday()" style="width:80px;">
									<option value="0">select</option>
									<option value="999">Continue</option>
									<?php
									for($j=1;$j<=100;$j++)
									{
									?>
									<option value="<?php echo $j;?>"><?php echo $j;?></option>
									<?php
									}
									?>
								</select>
							</td>
							<th>Unit Days</th>
							<td>
								<select id="unit_day" style="width:80px;" onchange="calc_totday()" onkeyup="meditab(this.id,event)">
									<option value="0">select</option>
									<option value="Days">Days</option>
									<option value="Weeks">Weeks</option>
									<option value="Months">Months</option>
									<option value="SOS">SOS</option>
								</select>
							</td>
							<th>Total</th>
							<td><input type="text" id="totl" class="span2" readonly="readonly" /></td>
							<th>Instruction</th>
							<td>
								<select id="inst" style="width:120px;" onkeyup="meditab(this.id,event)">
									<option value="1">As Directed</option>
									<option value="2">Before Meal</option>
									<option value="3">Empty Stomach</option>
									<option value="4">After Meal</option>
									<option value="5">In the Morning</option>
									<option value="6">In the Evening</option>
									<option value="7">At Bedtime</option>
									<option value="8">Immediately</option>
									<option value="9">After Breakfast</option>
								</select>
							</td>
						</tr>
					</table>
					<center><input type="button" id="add_medi" class="btn btn-info" value="Add" onclick="set_medi()" /></center>
			</td>
		</tr>
		<tr id="medi_list_post" style="display:none;">
			<td id="medi_list_data" colspan="2">
			
			</td>
		</tr>
	</table>
	<style>
		.table tr:hover{background:none;}
	</style>
	<script>
		$("#st_date").datepicker(
		{
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
	</script>
	<?php
}

if($_POST["type"]=="save_comp")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$comp=$_POST['comp'];
	$usr=$_POST['usr'];
	$all=explode("#g#",$comp);
	mysqli_query($link,"DELETE FROM `ipd_pat_complaints` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	foreach($all as $al)
	{
		$a=explode("@",$al);
		if($a[0] && $a[1] && $a[2])
		mysqli_query($link,"INSERT INTO `ipd_pat_complaints`(`patient_id`, `ipd_id`, `comp_one`, `comp_two`, `comp_three`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$a[0]','$a[1]','$a[2]','$date','$time','$usr')");
	}
	//echo "Saved";
}

if($_POST["type"]=="save_exam")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$history=$_POST['history'];
	$history= str_replace("'", "''", "$history");
	$exam=$_POST['exam'];
	$exam= str_replace("'", "''", "$exam");
	$significant_finding=$_POST['significant_finding'];
	$significant_finding= str_replace("'", "''", "$significant_finding");
	$investigation_result=$_POST['investigation_result'];
	$investigation_result= str_replace("'", "''", "$investigation_result");
	$usr=$_POST['usr'];
	
	$e=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($e>0)
	{
		mysqli_query($link,"UPDATE `pat_examination` SET `history`='$history',`examination`='$exam',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `pat_examination`(`patient_id`, `opd_id`, `ipd_id`, `history`, `examination`, `date`, `time`, `user`) VALUES ('$uhid','','$ipd','$history','$exam','$date','$time','$usr')");
	}
	
	$q=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_significant_investigation` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($q>0)
	{
		mysqli_query($link,"UPDATE `ipd_pat_significant_investigation` SET `significant_finding`='$significant_finding',`investigation_result`='$investigation_result',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_significant_investigation`(`patient_id`, `ipd_id`, `significant_finding`, `investigation_result`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$significant_finding','$investigation_result','$date','$time','$usr')");
	}
}

if($_POST["type"]=="save_diagno")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$diagno=$_POST['diagno'];
	$diagno= str_replace("'", "''", "$diagno");
	$usr=$_POST['usr'];
	$all=explode("#g#",$diagno);
	//mysqli_query($link,"DELETE FROM `ipd_pat_diagnosis` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	foreach($all as $al)
	{
		$a=explode("@",$al);
		if($a[0] && $a[1] && $a[2])
		mysqli_query($link,"INSERT INTO `ipd_pat_diagnosis`(`patient_id`, `ipd_id`, `diagnosis`, `order`, `certainity`, `consultantdoctorid`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$a[0]','$a[1]','$a[2]','0','$date','$time','$usr')");
	}
}

if($_POST["type"]=="insert_disc_summ")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$course=$_POST['course'];
	$course= str_replace("'", "''", "$course");
	$final_diag=$_POST['final_diag'];
	$final_diag= str_replace("'", "''", "$final_diag");
	$procedure_with_date=$_POST['procedure_with_date'];
	$procedure_with_date= str_replace("'", "''", "$procedure_with_date");
	$v_bp=$_POST['v_bp'];
	$v_bp= str_replace("'", "''", "$v_bp");
	$v_pulse=$_POST['v_pulse'];
	$v_pulse= str_replace("'", "''", "$v_pulse");
	$v_temp=$_POST['v_temp'];
	$v_temp= str_replace("'", "''", "$v_temp");
	$v_weight=$_POST['v_weight'];
	$v_weight= str_replace("'", "''", "$v_weight");
	$foll=$_POST['foll'];
	$foll= str_replace("'", "''", "$foll");
	$usr=$_POST['usr'];
	$report_hospital=$_POST['report_hospital'];
	$next_visit=$_POST['next_visit'];
	$sn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($sn>0)
	{
		mysqli_query($link,"UPDATE `ipd_pat_discharge_summary` SET `course`='$course',`final_diagnosis`='$final_diag',`procedure_with_date`='$procedure_with_date',`final_bp`='$v_bp',`final_pulse`='$v_pulse',`final_temp`='$v_temp',`final_weight`='$v_weight',`follow_up`='$foll',`date`='$date',`time`='$time',`user`='$usr',`report_hospital`='$report_hospital',`next_visit`='$next_visit' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_discharge_summary`(`patient_id`, `ipd_id`, `course`, `final_diagnosis`, `procedure_with_date`, `final_bp`, `final_pulse`, `final_temp`, `final_weight`, `follow_up`, `date`, `time`, `user`, `report_hospital`, `next_visit`) VALUES ('$uhid','$ipd','$course','$final_diag','$procedure_with_date','$v_bp','$v_pulse','$v_temp','$v_weight','$foll','$date','$time','$usr','$report_hospital','$next_visit')");
	}
}

if($_POST["type"]=="save_disc_medi")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$all=$_POST['all'];
	$usr=$_POST['usr'];
	$alll=explode("#g#",$all);
	//mysqli_query($link,"DELETE FROM `ipd_pat_diagnosis` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	foreach($alll as $al)
	{
		$a=explode("@@",$al);
		$med=$a[0];
		$dose=$a[1];
		$freq=$a[2];
		$dur=$a[3];
		$unit=$a[4];
		$tot=$a[5];
		$inst=$a[6];
		$st_date=$a[7];
		if($med && $dose && $freq && $dur && $unit && $tot && $inst && $st_date)
		{
			if($unit=="Days")
			{
				$dd=$dur*1;
			}
			if($unit=="Weeks")
			{
				$dd=$dur*7;
			}
			if($unit=="Months")
			{
				$dd=$dur*30;
			}
			if($dur==1)
			{
				$ed=$st_date;
			}
			else
			{
				for($jj=1;$jj<$dd;$jj++)
				$ed=date('Y-m-d', strtotime($st_date . ' +'.$jj.' days'));
			}
			mysqli_query($link,"INSERT INTO `ipd_pat_medicine_final_discharge`(`patient_id`, `ipd_id`, `medicine`, `dosage`, `frequency`, `start_date`, `end_date`, `total_drugs`, `duration`, `unit_days`, `instruction`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$med','$dose','$freq','$st_date','$ed','$tot','$dur','$unit','$inst','$date','$time','$usr')");
		}
	}
}

if($_POST["type"]=="save_dis_type")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$dtype=$_POST['dtype'];
	$diagnosed=$_POST['diagnosed'];
	$usr=$_POST['usr'];
	$sn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_dischage_type` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($sn>0)
	{
		mysqli_query($link,"UPDATE `ipd_dischage_type` SET `type`='$dtype',`diagnosed_by`='$diagnosed',`date`='$date',`time`='$time',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_dischage_type`(`patient_id`, `ipd_id`, `type`, `diagnosed_by`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$dtype','$diagnosed','$date','$time','$usr')");
	}
}

if($_POST["type"]=="labdoctor_load")
{
	$srch=$_POST['srch'];
	if($srch)
	{
		//$q="select * from lab_doctor  where name like '$srch%'";
		$q=mysqli_query($link,"select * from lab_doctor  where name like '$srch%'");
	}
	else
	{
		//$q="select * from lab_doctor  order by name";
		$q=mysqli_query($link,"SELECT DISTINCT `category` FROM `lab_doctor`");
	}
	while($r=mysqli_fetch_array($q))
	{
	//$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
	$qq=mysqli_query($link,"SELECT * FROM `lab_doctor` WHERE `category`='$r[category]'");
	$i=1;
	$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$r[category]'"));
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th style="text-align:center;" colspan="3"><?php echo $c['name']; ?></th>
		</tr>
		<tr>
			<th class="span1">Id</th>
			<th>Doctor Name</th>
			<th>Sequence</th>
		</tr>
<?php
	while($qrpdct1=mysqli_fetch_array($qq))
	{
 ?>
	<!--<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">-->
	<tr>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td style="width: 70%;"><?php echo $qrpdct1['name'];?></td>
		<td><?php echo $qrpdct1['sequence'];?><span class="text-right"><button type="button" class="btn btn-mini" onclick="edt('<?php echo $qrpdct1['id'];?>')" style="color:#0000ee;"><b class="icon-edit icon-large"></b></button> <button type="button" class="btn btn-mini" onclick="del('<?php echo $qrpdct1['id'];?>')" style="color:#dd0000;"><b class="icon-trash icon-large"></b></button></span></td>
		<!--<td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><button class="btn btn-mini btn-danger"><i class="icon-remove"></i></button></a></td>-->
	</tr>
<?php	
	$i++;
	}
?>
	</table>
<?php
	}
}

if($_POST["type"]=="labdoctor_seq")
{
	$doc=$_POST['doc'];
	$sq=mysqli_fetch_array(mysqli_query($link,"SELECT `sequence` FROM `lab_doctor` WHERE `id`='$doc'"));
	echo $sq['sequence'];
}

if($_POST["type"]=="labdoctor_update")
{
	$cate=$_POST['cate'];
	$doc=$_POST['doc'];
	$id=$_POST['id'];
	$seq=$_POST['seq'];
	$o=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `employee` WHERE `emp_id`='$doc'"));
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `lab_doctor` WHERE `id`='$doc'"));
	if($n>0)
	{
		mysqli_query($link,"UPDATE `lab_doctor` SET `category`='$cate',`name`='$o[name]',`desig`='$o[designation]',`qual`='$o[qualification]' WHERE `id`='$doc'");
	}
	else
	{
		$max=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(`sequence`) AS max FROM `lab_doctor` WHERE `category`='$cate'"));
		$m=$max['max']+1;
		mysqli_query($link,"INSERT INTO `lab_doctor`(`id`, `sequence`, `category`, `name`, `desig`, `qual`, `phn`, `password`) VALUES ('$doc','$m','$cate','$o[name]','$o[designation]','$o[qualification]','','')");
	}
	echo "Saved";
	/*
	$o=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `lab_doctor` WHERE `id`='$id'"));
	$q=mysqli_query($link,"SELECT * FROM `lab_doctor` WHERE `category`='$cate' AND `sequence`='$seq'");
	$n=mysqli_num_rows($q);
	if($n>0)
	{
		$a=mysqli_fetch_array($q);
		$i=$a['id'];
		$s=$o['sequence'];
		if($s==$seq)
		{
			$max=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(`sequence`) AS max FROM `lab_doctor` WHERE `category`='$cate'"));
			$s=$max['max']+1;;
		}
		mysqli_query($link,"UPDATE `lab_doctor` SET `sequence`='$s' WHERE `id`='$i'");
	}
	if(mysqli_query($link,"UPDATE `lab_doctor` SET `sequence`='$seq' WHERE `id`='$id'"))
	{
		echo "Updated";
	}
	else
	{
		echo "Error";
	}
	*/
}

if($_POST["type"]=="labdoctor_delete")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `lab_doctor` WHERE `id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="labdoctor_edit_seq")
{
	$id=$_POST['id'];
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `lab_doctor` WHERE `id`='$id'"));
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<td>Name : <?php echo $f['name']; ?><input type="text" id="edtid" style="display:none;" value="<?php echo $id; ?>" /></td>
		</tr>
		<tr>
			<td>Sequence : <input type="text" id="edseq" value="<?php echo $f['sequence']; ?>" /></td>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="labdoctor_edit_seq_save")
{
	$id=$_POST['id'];
	$seq=$_POST['seq'];
	$f=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `lab_doctor` WHERE `id`='$id'"));
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `lab_doctor` WHERE `sequence`='$seq' AND `category`='$f[category]' AND `id`!='$id'"));
	if($n>0)
	{
		echo "This sequence is already exists";
	}
	else
	{
		mysqli_query($link,"UPDATE `lab_doctor` SET `sequence`='$seq' WHERE `id`='$id'");
		echo "Saved";
	}
}

if($_POST["type"]=="item_master_hsn")
{
	$item=$_POST['item'];
	if($item)
	{
	$qq=mysqli_query($link,"SELECT `item_code`,`item_name`,`gst_percent`,`hsn_code` FROM `ph_item_master` WHERE `item_name` like '$item%' ORDER BY `item_name`");
	$i=1;
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>HSN</th>
			<th>GST (%)</th>
		</tr>
	<?php
	while($r=mysqli_fetch_array($qq))
	{
	?>
	<tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $r['item_name']; ?></td>
		<td>
			<input list="browsr" type="text" id="H<?php echo $r['item_code']; ?>" onfocus="$(this).css('border','')" onfocusout="$(this).css('border','')" value="<?php echo $r['hsn_code']; ?>" onkeyup="save_hsn(this.value,'<?php echo $r['item_code']; ?>',event)" placeholder="HSN Code" />
			<datalist id="browsr">
			<?php
				$hsn= mysqli_query($link,"SELECT DISTINCT `hsn_code` FROM `hsn_master`");
				while($hh=mysqli_fetch_array($hsn))
				{
					echo "<option value='$hh[hsn_code]'>";
				}
			?>
			</datalist>
		</td>
		<td>
			<input type="text" id="G<?php echo $r['item_code']; ?>" onfocus="$(this).css('border','')" onfocusout="$(this).css('border','')" value="<?php echo $r['gst_percent']; ?>" onkeyup="save_gst(this.value,'<?php echo $r['item_code']; ?>',event)" placeholder="GST %" />
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

if($_POST["type"]=="item_master_hsn_save")
{
	$id=$_POST['id'];
	$val=$_POST['val'];
	$val=str_replace("'", "''", "$val");
	$gst=mysqli_fetch_array(mysqli_query($link,"SELECT `gst_percent` FROM `hsn_master` WHERE `hsn_code`='$val'"));
	if(mysqli_query($link,"UPDATE `ph_item_master` SET `gst_percent`='$gst[gst_percent]',`hsn_code`='$val' WHERE `item_code`='$id'"))
	echo "1";
	else
	echo "0";
}

if($_POST["type"]=="item_master_gst_save")
{
	$id=$_POST['id'];
	$val=$_POST['val'];
	$val=str_replace("'", "''", "$val");
	//$gst=mysqli_fetch_array(mysqli_query($link,"SELECT `gst_percent` FROM `hsn_master` WHERE `hsn_code`='$val'"));
	if(mysqli_query($link,"UPDATE `ph_item_master` SET `gst_percent`='$val' WHERE `item_code`='$id'"))
	echo "1";
	else
	echo "0";
}

if($_POST["type"]=="load_item_hsn")
{
	$val=$_POST['val'];
	if($val)
	{
	 $q="SELECT * FROM `hsn_master` WHERE `hsn_descripton` like '$val%' order by `hsn_descripton`";
	}
	else
	{
	 $q="SELECT * FROM `hsn_master` order by `hsn_descripton`";
	}
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>HSN Code</th>
			<th width="60%">Description</th>
			<th>GST (%)</th>
			<th></th>
		</tr>
	<?php
	$qrpdct=mysqli_query($link,$q);
	$i=1;
	while($qrpdct1=mysqli_fetch_array($qrpdct))
	{
	 ?>
	<tr style="cursor:pointer" onclick="det('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['hsn_code'];?></td>
		<td class="itname"><?php echo $qrpdct1['hsn_descripton'];?></td>
		<td><?php echo $qrpdct1['gst_percent'];?></td>
		<td>
			<button type="button" class="btn btn-danger btn-mini" onclick="delete_data('<?php echo $qrpdct1['id'];?>')" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-remove"></i></button>
		</td>
	</tr>
	 <?php	
	   $i++;
	}
	?>
	 </table>
	<?php
}

if($_POST["type"]=="save_hsn_master")
{
	$id=$_POST['id'];
	$hsn=$_POST['hsn'];
	$desc=$_POST['desc'];
	$desc=str_replace("'", "''", "$desc");
	$gst=$_POST['gst'];
	if($id>0)
	{
		$nm=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `hsn_master` WHERE `hsn_code`='$hsn' AND `id`!='$id'"));
		if($nm>0)
		{
			echo "0";
		}
		else
		{
			mysqli_query($link,"UPDATE `hsn_master` SET `hsn_code`='$hsn',`hsn_descripton`='$desc',`gst_percent`='$gst' WHERE `id`='$id'");
			echo "2";
		}
	}
	else
	{
		$num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `hsn_master` WHERE `hsn_code`='$hsn'"));
		if($num>0)
		{
			echo "0";
		}
		else
		{
			mysqli_query($link,"INSERT INTO `hsn_master`(`hsn_code`, `hsn_descripton`, `gst_percent`) VALUES ('$hsn','$desc','$gst')");
			echo "1";
		}
	}
}

if($_POST["type"]=="hsn_master_delete")
{
	$id=$_POST['id'];
	mysqli_query($link,"DELETE FROM `hsn_master` WHERE `id`='$id'");
	echo "Deleted";
}

if($_POST["type"]=="edit_hsn_master")
{
	$id=$_POST['id'];
	$v=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `hsn_master` WHERE `id`='$id'"));
	echo $id."@".$v['hsn_code']."@".$v['hsn_descripton']."@".$v['gst_percent']."@";
}

if($_POST["type"]=="load_gst_sale_report")
{
	$gst=$_POST['gst'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	if($fdate=="" && $tdate=="")
	{
	 $q="SELECT * FROM `ph_sell_details` WHERE `gst_percent`='$gst'";
	}
	else
	{
	 $q="SELECT * FROM `ph_sell_details` WHERE `gst_percent`='$gst' AND `entry_date` BETWEEN '$fdate' AND '$tdate'";
	}
	?>
	<!--<button type="button" class="btn btn-default" onclick="report_xls('<?php echo $gst; ?>','<?php echo $fdate; ?>','<?php echo $tdate; ?>')"><b class="icon-save"></b> Export to excel</button>-->
	<button type="button" class="btn btn-default" onclick="report_print('<?php echo $gst; ?>','<?php echo $fdate; ?>','<?php echo $tdate; ?>')"><b class="icon-print"></b> Print</button>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Sl No</th>
			<th>Item Details</th>
			<th>Rate</th>
			<th>Quantity</th>
			<th>Total Amount</th>
			<th>Total Cost Price</th>
			<th>GST Amount</th>
			<th>Date</th>
		</tr>
	<?php
	$qry=mysqli_query($link,$q);
	$i=1;
	while($r=mysqli_fetch_array($qry))
	{
		$itm=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name`,`item_mrp` FROM `ph_item_master` WHERE `item_code`='$r[item_code]'"));
	 ?>
	 <tr>
		<td><?php echo $i; ?></td>
		<td><?php echo $itm['item_name']; ?></td>
		<td><?php echo $r['mrp']; ?></td>
		<td><?php echo $r['sale_qnt']; ?></td>
		<td><?php echo ($r['mrp']*$r['sale_qnt']); ?></td>
		<td><?php echo ($r['item_cost_price']*$r['sale_qnt']); ?></td>
		<td><?php echo $r['gst_amount']; ?></td>
		<td><?php echo convert_date_g($r['entry_date']); ?></td>
	 </tr>
	 <?php	
	   $i++;
	}
	?>
	 </table>
	<?php
}

if($_POST["type"]=="ipd_edit_summary_diagnosis")
{
	$sl=$_POST['sl'];
	$d=mysqli_fetch_array(mysqli_query($link,"SELECT `diagnosis`,`order`,`certainity` FROM `ipd_pat_diagnosis` WHERE `slno`='$sl'"));
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>Diagnosis</th><th>Order</th><th>Certainity</th>
		</tr>
		<tr>
			<td>
				<input type="text" id="diagnosis" class="span3" value="<?php echo $d['diagnosis'];?>" />
			</td>
			<td>
				<select id="ordr" class="span2">
					<option value="0">Select</option>
					<option value="Primary" <?php if($d['order']=="Primary"){echo "selected='selected'";}?>>Primary</option>
					<option value="Secondary" <?php if($d['order']=="Secondary"){echo "selected='selected'";}?>>Secondary</option>
				</select>
			</td>
			<td>
				<select id="certainity" class="span2">
					<option value="0">Select</option>
					<option value="Confirmed" <?php if($d['certainity']=="Confirmed"){echo "selected='selected'";}?>>Confirmed</option>
					<option value="Presumed" <?php if($d['certainity']=="Presumed"){echo "selected='selected'";}?>>Presumed</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center;">
				<button type="button" class="btn btn-info" onclick="upd_diag('<?php echo $sl;?>')" data-dismiss="modal">Update</button>
			</td>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="ipd_delete_summary_diagnosis")
{
	$sl=$_POST['sl'];
	mysqli_query($link,"DELETE FROM `ipd_pat_diagnosis` WHERE `slno`='$sl'");
	
}

if($_POST["type"]=="ipd_update_summary_diagnosis")
{
	$sl=$_POST['sl'];
	$diag=$_POST['diag'];
	$diag= str_replace("'", "''", "$diag");
	$ord=$_POST['ord'];
	$cert=$_POST['cert'];
	$usr=$_POST['usr'];
	if($diag && $ord && $cert)
	{
		mysqli_query($link,"UPDATE `ipd_pat_diagnosis` SET `diagnosis`='$diag',`order`='$ord',`certainity`='$cert',`date`='$date',`time`='$time',`user`='$usr' WHERE `slno`='$sl'");
	}
}
if($_POST["type"]=="delete_indent_medicine")
{
	$slno=$_POST['slno'];
	
	mysqli_query($link," DELETE FROM `patient_medicine_detail` WHERE `slno`='$slno' ");
	
}
if($_POST["type"]=="load_medical_case_sheet")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$u_lev=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$usr'"));
	
	$sex=mysqli_fetch_array(mysqli_query($link,"SELECT `sex` FROM `patient_info` WHERE `patient_id`='$uhid'"));
	
	$f1=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_AB` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$f2=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_CF` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$f3=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_GL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$f4=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_LL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	?>
	<table class="table table-condensed" id="case_tbl">
		<tr>
			<th colspan="2" style="text-align:center;background:#dddddd;">Clinical notes &amp; Case summary</th>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;">History</th>
		</tr>
		<tr>
			<td colspan="2">
				<b>A. Complaints with duration / Illness or injury</b><br/>
				<textarea id="illness" style="resize:none;width:95%" placeholder="Complaints with duration / Illness or injury"><?php echo $f1['illness'];?></textarea>
			</td>
		</tr>
		<tr>
			<th colspan="2">B. <label>Accident <input type="checkbox" id="accident" onchange="tr_accid()" <?php if($f1['accident']==1){echo "checked='checked'";}?> /></label></th>
		</tr>
		<?php
		if($f1['accident']>0)
		{
			$tr_accid="";
		}
		else
		{
			$tr_accid="display:none;";
		}
		?>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td>
				<b>Date of injury : </b>
				<input type="text" id="inj_dt" class="datepicker" value="<?php if($f1['inj_dt']!='0000-00-00'){echo $f1['inj_dt'];}?>" placeholder="YYYY-MM-DD" />
			</td>
			<td>
				<b>Time of injury : </b>
				<input type="text" id="inj_tm" class="timepicker" value="<?php if($f1['inj_tm']!='00:00:00'){echo $f1['inj_tm'];}?>" placeholder="HH:MM" />
			</td>
		</tr>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td colspan="2">
				<b>Type of injury :</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label><input type="checkbox" class="check_injury" onchange="check_injury()" id="blunt" <?php if($f1['blunt']==1){echo "checked='checked'";}?> /> Blunt</label>
				<label><input type="checkbox" class="check_injury" onchange="check_injury()" id="penet" <?php if($f1['penet']==1){echo "checked='checked'";}?> /> Penetrating</label>
				<label><input type="checkbox" class="check_injury" onchange="check_injury()" id="burns" <?php if($f1['burns']==1){echo "checked='checked'";}?> /> Burns</label>
				<label><input type="checkbox" class="check_injury" onchange="check_injury()" id="inhal" <?php if($f1['inhal']==1){echo "checked='checked'";}?> /> Inhalation Injury</label>
				<label><input type="checkbox" class="check_injury" onchange="check_injury()" id="inj_oth" <?php if($f1['inj_oth']==1){echo "checked='checked'";}?> /> Others</label>
			</td>
		</tr>
		<?php
		if($f1['accident']>0)
		{
			if($f1['blunt']>0 || $f1['penet']>0 || $f1['burns']>0 || $f1['inhal']>0 || $f1['inj_oth']>0)
			{
				$tr_injury="";
			}
			else
			{
				$tr_injury="display:none;";
			}
		}
		else
		{
			$tr_injury="display:none;";
		}
		?>
		<tr id="tr_injury" style="<?php echo $tr_injury;?>">
			<td colspan="2">
				<input type="text" id="injury_hist" style="width:95%;" value="<?php echo $f1['injury_hist'];?>" placeholder="Injury" />
			</td>
		</tr>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td colspan="2">
				<b>Place of Occurence :</b><br/>
				<textarea id="occur" style="resize:none;width:95%" placeholder="Place of Occurence"><?php echo $f1['occur'];?></textarea>
			</td>
		</tr>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td colspan="2">
				<b>Mechanism of Injury :</b><br/>
				<textarea id="mechan" style="resize:none;width:95%" placeholder="Mechanism of Injury"><?php echo $f1['mechan'];?></textarea>
			</td>
		</tr>
		<tr class="tr_accid" style="<?php echo $tr_accid;?>">
			<td colspan="2">
				<b>Pre-Hospital Care :</b><br/>
				<textarea id="care" style="resize:none;width:95%" placeholder="Pre-Hospital Care"><?php echo $f1['care'];?></textarea>
			</td>
		</tr>
		<tr>
			<th colspan="2">C. Past Medical History with Duration</th>
		</tr>
		<tr>
			<td colspan="2">
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="no_past" <?php if($f2['no_past']==1){echo "checked='checked'";}?> /> No past history</label>&nbsp;&nbsp;
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="copd" <?php if($f2['copd']==1){echo "checked='checked'";}?> /> COPD or Lung Disorder</label>
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="cva" <?php if($f2['cva']==1){echo "checked='checked'";}?> /> CVA / Stroke</label>
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="hyper" <?php if($f2['hyper']==1){echo "checked='checked'";}?> /> Hypertension</label>
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="unknown" <?php if($f2['unknown']==1){echo "checked='checked'";}?> /> Unknown</label><br/><br/>
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="heart" <?php if($f2['heart']==1){echo "checked='checked'";}?> /> Heart Condition</label>&nbsp;
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="cancer" <?php if($f2['cancer']==1){echo "checked='checked'";}?> /> Cancer</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="diabetes" <?php if($f2['diabetes']==1){echo "checked='checked'";}?> /> Diabetes</label>&nbsp;
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="seizure" <?php if($f2['seizure']==1){echo "checked='checked'";}?> /> Seizures</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label><input type="checkbox" class="check_dur" onchange="check_dur()" id="past_oth" <?php if($f2['past_oth']==1){echo "checked='checked'";}?> /> Others</label>
			</td>
		</tr>
		<?php
		if($f2['no_past']>0 || $f2['copd']>0 || $f2['cva']>0 || $f2['hyper']>0 || $f2['unknown']>0 || $f2['heart']>0 || $f2['cancer']>0 || $f2['diabetes']>0 || $f2['seizure']>0 || $f2['past_oth']>0)
		{
			$tr_dur="";
		}
		else
		{
			$tr_dur="display:none;";
		}
		?>
		<tr id="tr_dur" style="<?php echo $tr_dur;?>">
			<td colspan="2">
				<input type="text" id="dur_hist" style="width:95%;" value="<?php echo $f2['dur_hist'];?>" placeholder="Past Medical History" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>D. Any Operation :</b><br/>
				<textarea id="operation" style="resize:none;width:95%" placeholder="Any Operation"><?php echo $f2['operation'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>E. Drug History :</b><br/>
				<label><input type="checkbox" class="check_drug" onchange="check_drug()" id="steroid" <?php if($f2['steroid']==1){echo "checked='checked'";}?> /> Steroids</label>
				<label><input type="checkbox" class="check_drug" onchange="check_drug()" id="hormone" <?php if($f2['hormone']==1){echo "checked='checked'";}?> /> Hormones</label>
				<label><input type="checkbox" class="check_drug" onchange="check_drug()" id="drug" <?php if($f2['drug']==1){echo "checked='checked'";}?> /> Thyroid Drugs</label>
				<label><input type="checkbox" class="check_drug" onchange="check_drug()" id="pills" <?php if($f2['pills']==1){echo "checked='checked'";}?> /> Contraceptive Pills</label>
				<label><input type="checkbox" class="check_drug" onchange="check_drug()" id="analgesic" <?php if($f2['analgesic']==1){echo "checked='checked'";}?> /> Analgesics</label>
			</td>
		</tr>
		<?php
		if($f2['steroid']>0 || $f2['hormone']>0 || $f2['drug']>0 || $f2['pills']>0 || $f2['analgesic']>0)
		{
			$tr_drug="";
		}
		else
		{
			$tr_drug="display:none;";
		}
		?>
		<tr id="tr_drug" style="<?php echo $tr_drug;?>">
			<td colspan="2">
				<input type="text" id="drug_hist" style="width:95%;" value="<?php echo $f2['drug_hist'];?>" placeholder="Drug History" />
			</td>
		</tr>
		<?php
		if($sex['sex']=="Male")
		{
			$sex_disb="disabled='disabled'";
			$cursr="cursor: not-allowed;";
		}
		if($sex['sex']=="Female")
		{
			$sex_disb="";
			$cursr="";
		}
		?>
		<tr>
			<td colspan="2">
				<b>F. Menstrual :</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label style="<?php echo $cursr;?>"><input type="checkbox" id="regular" <?php echo $sex_disb;?> <?php if($f2['regular']==1){echo "checked='checked'";}?> /> Regular</label>
				<label style="<?php echo $cursr;?>"><input type="checkbox" id="irregular" <?php echo $sex_disb;?> <?php if($f2['irregular']==1){echo "checked='checked'";}?> /> Irregular</label>
				<b>History : </b>
				<input type="text" id="mens_hist" class="span5" <?php echo $sex_disb;?> value="<?php echo $f2['mens_hist'];?>" placeholder="History" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>G. Transfussion History :</b><br/>
				<textarea id="tran_hist" style="resize:none;width:95%" placeholder="Transfussion History"><?php echo $f3['tran_hist'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>H. Known Allergy (if any) :</b><br/>
				<textarea id="allergy" style="resize:none;width:95%" placeholder="Known Allergy"><?php echo $f3['allergy'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>I. Personal History :</b><br/>
				<label><input type="checkbox" class="check_per" onchange="check_per()" id="alcohol" <?php if($f3['alcohol']==1){echo "checked='checked'";}?> /> Alcohol</label>
				<label><input type="checkbox" class="check_per" onchange="check_per()" id="smoking" <?php if($f3['smoking']==1){echo "checked='checked'";}?> /> Smoking</label>
				<label><input type="checkbox" class="check_per" onchange="check_per()" id="oth_addict" <?php if($f3['oth_addict']==1){echo "checked='checked'";}?> /> Other Addiction</label>
			</td>
		</tr>
		<?php
		if($f3['alcohol']>0 || $f3['smoking']>0 || $f3['oth_addict']>0)
		{
			$tr_per="";
		}
		else
		{
			$tr_per="display:none;";
		}
		?>
		<tr id="tr_per" style="<?php echo $tr_per;?>">
			<td colspan="2">
				<input type="text" id="per_hist" style="width:95%;" value="<?php echo $f3['per_hist'];?>" placeholder="Personal History" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>J. Family History :</b><br/>
				<label><input type="checkbox" class="check_family" onchange="check_family()" id="htn" <?php if($f3['htn']==1){echo "checked='checked'";}?> /> HTN</label>
				<label><input type="checkbox" class="check_family" onchange="check_family()" id="dm" <?php if($f3['dm']==1){echo "checked='checked'";}?> /> DM</label>
				<label><input type="checkbox" class="check_family" onchange="check_family()" id="cva" <?php if($f3['cva']==1){echo "checked='checked'";}?> /> CVA</label>
				<label><input type="checkbox" class="check_family" onchange="check_family()" id="ihd" <?php if($f3['ihd']==1){echo "checked='checked'";}?> /> CA / IHD</label>
				<label><input type="checkbox" class="check_family" onchange="check_family()" id="f_cancer" <?php if($f3['f_cancer']==1){echo "checked='checked'";}?> /> Cancer</label>
				<label><input type="checkbox" class="check_family" onchange="check_family()" id="asthma" <?php if($f3['asthma']==1){echo "checked='checked'";}?> /> Br.Asthma</label>
				<label><input type="checkbox" class="check_family" onchange="check_family()" id="f_oth" <?php if($f3['f_oth']==1){echo "checked='checked'";}?> /> Others</label>
			</td>
		</tr>
		<?php
		if($f3['htn']>0 || $f3['dm']>0 || $f3['cva']>0 || $f3['ihd']>0 || $f3['f_cancer']>0 || $f3['asthma']>0 || $f3['f_oth']>0)
		{
			$tr_family="";
		}
		else
		{
			$tr_family="display:none;";
		}
		?>
		<tr id="tr_family" style="<?php echo $tr_family;?>">
			<td colspan="2">
				<input type="text" id="fam_hist" style="width:95%;" value="<?php echo $f3['fam_hist'];?>" placeholder="Family History" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>K. Treatment History :</b><br/>
				<textarea id="treat_hist" style="resize:none;width:95%" placeholder="Treatment History"><?php echo $f3['treat_hist'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>L. Past Investigation :</b><br/>
				<textarea id="past_inv" style="resize:none;width:95%" placeholder="Past Investigation"><?php echo $f3['past_inv'];?></textarea>
			</td>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;">Physical Examination</th>
		</tr>
		<tr>
			<th>General Examination</th>
			<th>Local Examination</th>
		</tr>
		<tr>
			<td>Height (cm)</td>
			<td><input type="text" id="height" value="<?php echo $f4['height'];?>" placeholder="Height" /></td>
		</tr>
		<tr>
			<td>Weight (kg)</td>
			<td><input type="text" id="weight" value="<?php echo $f4['weight'];?>" placeholder="Weight" /></td>
		</tr>
		<tr>
			<td>Respiratory Rate/min : </td>
			<td><input type="text" id="rr" value="<?php echo $f4['rr'];?>" placeholder="Respiratory Rate" /></td>
		</tr>
		<tr>
			<td>Blood Pressure : </td>
			<td><input type="text" id="bp" value="<?php echo $f4['bp'];?>" placeholder="Blood Pressure" /></td>
		</tr>
		<tr>
			<td>Pulse/min : </td>
			<td><input type="text" id="pulse" value="<?php echo $f4['pulse'];?>" placeholder="Pulse" /></td>
		</tr>
		<tr>
			<td>Temperature : </td>
			<td><input type="text" id="temp" value="<?php echo $f4['temp'];?>" placeholder="Temperature" /></td>
		</tr>
		<tr>
			<td>Pallor : </td>
			<td><input type="text" id="pallor" value="<?php echo $f4['pallor'];?>" placeholder="Pallor" /></td>
		</tr>
		<tr>
			<td>Cyanosis : </td>
			<td><input type="text" id="cyanosis" value="<?php echo $f4['cyanosis'];?>" placeholder="Cyanosis" /></td>
		</tr>
		<tr>
			<td>Clubbing : </td>
			<td><input type="text" id="club" value="<?php echo $f4['club'];?>" placeholder="Clubbing" /></td>
		</tr>
		<tr>
			<th colspan="2">Others</th>
		</tr>
		<tr>
			<td>O2 Saturation : </td>
			<td><input type="text" id="saturation" value="<?php echo $f4['saturation'];?>" placeholder="O2 Saturation" /></td>
		</tr>
		<tr>
			<td>APVU : </td>
			<td><input type="text" id="apvu" value="<?php echo $f4['apvu'];?>" placeholder="APVU" /></td>
		</tr>
		<tr>
			<td>GCS : </td>
			<td><input type="text" id="gcs" value="<?php echo $f4['gcs'];?>" placeholder="GCS" /></td>
		</tr>
		<tr>
			<th colspan="2">Systemic Examination :</th>
		</tr>
		<tr>
			<td colspan="2">
				<b>CENTRAL NERVOUS SYSTEM :</b><br/>
				<textarea id="nervous" style="resize:none;width:95%" placeholder="CENTRAL NERVOUS SYSTEM"><?php echo $f4['nervous'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>RESPIRATORY SYSTEM :</b><br/>
				<textarea id="respiratory" style="resize:none;width:95%" placeholder="RESPIRATORY SYSTEM"><?php echo $f4['respiratory'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>CARDIO VASCULAR SYSTEM :</b><br/>
				<textarea id="vascular" style="resize:none;width:95%" placeholder="CARDIO VASCULAR SYSTEM"><?php echo $f4['vascular'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>ABDOMEN AND GENITALIA :</b><br/>
				<textarea id="abdomen" style="resize:none;width:95%" placeholder="ABDOMEN AND GENITALIA"><?php echo $f4['abdomen'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>PROVISIONAL DIAGNOSIS:</b><br/>
				<textarea id="pr_diag" style="resize:none;width:95%" placeholder="PROVISIONAL DIAGNOSIS"><?php echo $f4['pr_diag'];?></textarea>
			</td>
		</tr>
		<tr>
			<td rowspan="2"><b>Case history recorded by : Dr</b>
				<select id="rec_doc" class="3">
					<option value="0">Select</option>
					<?php
					if($u_lev['levelid']==5)
					{
						$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `emp_id`='$usr'");
					}
					else
					{
						$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					}
					while($rr=mysqli_fetch_array($qry))
					{
					?>
					<option value="<?php echo $rr['consultantdoctorid'];?>" <?php if($f4['rec_doc']==$rr['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $rr['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				History Informant : Patient/Attendant<br/>
				<input type="text" id="informant" style="width:95%" value="<?php echo $f4['informant'];?>" placeholder="History Informant" />
			</td>
		</tr>
		<tr>
			<td>
				Name of the patient or attendand<br/>
				<input type="text" id="patient" style="width:95%" value="<?php echo $f4['patient'];?>" placeholder="Name of the patient or attendand" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				Relation with patient (in case of attendant)<br/>
				<input type="text" id="rel" style="width:95%" value="<?php echo $f4['rel'];?>" placeholder="Relation with patient" />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button type="button" class="btn btn-info" id="sav_case" onclick="save_case()">Save</button>
				<button type="button" class="btn btn-primary" onclick="print_case_sheet()">Print</button>
			</td>
		</tr>
	</table>
	<style>
		.table tr:hover
		{background:none;}
		label{display:inline-block;margin-bottom:0px;font-weight:bold;margin-right:10px;}
		label:hover{color:#222222;box-shadow: -3px 3px 6px 0px #ccc;}
		input[type="checkbox"]{margin:0px 0px 0px;}
	</style>
	<script>
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd',maxDate: 0,});
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,showSecond: true,showMillisec: true,}});
	</script>
	<?php
}

if($_POST["type"]=="sav_case")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$illness=$_POST['illness'];
	$inj_dt=$_POST['inj_dt'];
	$inj_tm=$_POST['inj_tm'];
	$accident=$_POST['accident'];
	$blunt=$_POST['blunt'];
	$penet=$_POST['penet'];
	$burns=$_POST['burns'];
	$inhal=$_POST['inhal'];
	$inj_oth=$_POST['inj_oth'];
	$injury_hist=$_POST['injury_hist'];
	$occur=$_POST['occur'];
	$mechan=$_POST['mechan'];
	$care=$_POST['care'];
	
	$no_past=$_POST['no_past'];
	$copd=$_POST['copd'];
	$cva=$_POST['cva'];
	$hyper=$_POST['hyper'];
	$unknown=$_POST['unknown'];
	$heart=$_POST['heart'];
	$cancer=$_POST['cancer'];
	$diabetes=$_POST['diabetes'];
	$seizure=$_POST['seizure'];
	$past_oth=$_POST['past_oth'];
	$dur_hist=$_POST['dur_hist'];
	$operation=$_POST['operation'];
	$steroid=$_POST['steroid'];
	$hormone=$_POST['hormone'];
	$drug=$_POST['drug'];
	$pills=$_POST['pills'];
	$analgesic=$_POST['analgesic'];
	$drug_hist=$_POST['drug_hist'];
	$regular=$_POST['regular'];
	$irregular=$_POST['irregular'];
	$mens_hist=$_POST['mens_hist'];
	
	$tran_hist=$_POST['tran_hist'];
	$allergy=$_POST['allergy'];
	$alcohol=$_POST['alcohol'];
	$smoking=$_POST['smoking'];
	$oth_addict=$_POST['oth_addict'];
	$per_hist=$_POST['per_hist'];
	$htn=$_POST['htn'];
	$dm=$_POST['dm'];
	$cva=$_POST['cva'];
	$ihd=$_POST['ihd'];
	$f_cancer=$_POST['f_cancer'];
	$asthma=$_POST['asthma'];
	$f_oth=$_POST['f_oth'];
	$fam_hist=$_POST['fam_hist'];
	$treat_hist=$_POST['treat_hist'];
	$past_inv=$_POST['past_inv'];
	
	$height=$_POST['height'];
	$weight=$_POST['weight'];
	$rr=$_POST['rr'];
	$bp=$_POST['bp'];
	$pulse=$_POST['pulse'];
	$temp=$_POST['temp'];
	$pallor=$_POST['pallor'];
	$cyanosis=$_POST['cyanosis'];
	$club=$_POST['club'];
	$saturation=$_POST['saturation'];
	$apvu=$_POST['apvu'];
	$gcs=$_POST['gcs'];
	$nervous=$_POST['nervous'];
	$respiratory=$_POST['respiratory'];
	$vascular=$_POST['vascular'];
	$abdomen=$_POST['abdomen'];
	$pr_diag=$_POST['pr_diag'];
	$rec_doc=$_POST['rec_doc'];
	$informant=$_POST['informant'];
	$patient=$_POST['patient'];
	$rel=$_POST['rel'];
	$usr=$_POST['usr'];
	
	$n1=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_AB` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$n2=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_CF` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$n3=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_GL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$n4=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_LL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($n1>0)
	{
		mysqli_query($link,"UPDATE `ipd_case_sheet_AB` SET `illness`='$illness',`inj_dt`='$inj_dt',`inj_tm`='$inj_tm',`accident`='$accident',`blunt`='$blunt',`penet`='$penet',`burns`='$burns',`inhal`='$inhal',`inj_oth`='$inj_oth',`injury_hist`='$injury_hist',`occur`='$occur',`mechan`='$mechan',`care`='$care' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_case_sheet_AB`(`patient_id`, `ipd_id`, `illness`, `inj_dt`, `inj_tm`, `accident`, `blunt`, `penet`, `burns`, `inhal`, `inj_oth`, `injury_hist`, `occur`, `mechan`, `care`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$illness','$inj_dt','$inj_tm','$accident','$blunt','$penet','$burns','$inhal','$inj_oth','$injury_hist','$occur','$mechan','$care','$date','$time','$usr')");
	}
	
	if($n2>0)
	{
		mysqli_query($link,"UPDATE `ipd_case_sheet_CF` SET `no_past`='$no_past',`copd`='$copd',`cva`='$cva',`hyper`='$hyper',`unknown`='$unknown',`heart`='$heart',`cancer`='$cancer',`diabetes`='$diabetes',`seizure`='$seizure',`past_oth`='$past_oth',`dur_hist`='$dur_hist',`operation`='$operation',`steroid`='$steroid',`hormone`='$hormone',`drug`='$drug',`pills`='$pills',`analgesic`='$analgesic',`drug_hist`='$drug_hist',`regular`='$regular',`irregular`='$irregular',`mens_hist`='$mens_hist' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_case_sheet_CF`(`patient_id`, `ipd_id`, `no_past`, `copd`, `cva`, `hyper`, `unknown`, `heart`, `cancer`, `diabetes`, `seizure`, `past_oth`,`dur_hist`, `operation`, `steroid`, `hormone`, `drug`, `pills`, `analgesic`, `drug_hist`, `regular`, `irregular`, `mens_hist`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$no_past','$copd','$cva','$hyper','$unknown','$heart','$cancer','$diabetes','$seizure','$past_oth','$dur_hist','$operation','$steroid','$hormone','$drug','$pills','$analgesic','$drug_hist','$regular','$irregular','$mens_hist','$date','$time','$usr')");
	}
	
	if($n3>0)
	{
		mysqli_query($link,"UPDATE `ipd_case_sheet_GL` SET `tran_hist`='$tran_hist',`allergy`='$allergy',`alcohol`='$alcohol',`smoking`='$smoking',`oth_addict`='$oth_addict',`per_hist`='$per_hist',`htn`='$htn',`dm`='$dm',`cva`='$cva',`ihd`='$ihd',`f_cancer`='$f_cancer',`asthma`='$asthma',`f_oth`='$f_oth',`fam_hist`='$fam_hist',`treat_hist`='$treat_hist',`past_inv`='$past_inv' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_case_sheet_GL`(`patient_id`, `ipd_id`, `tran_hist`, `allergy`, `alcohol`, `smoking`, `oth_addict`, `per_hist`, `htn`, `dm`, `cva`, `ihd`, `f_cancer`, `asthma`, `f_oth`, `fam_hist`, `treat_hist`, `past_inv`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$tran_hist','$allergy','$alcohol','$smoking','$oth_addict','$per_hist','$htn','$dm','$cva','$ihd','$f_cancer','$asthma','$f_oth','$fam_hist','$treat_hist','$past_inv','$date','$time','$usr')");
	}
	
	if($n4>0)
	{
		mysqli_query($link,"UPDATE `ipd_case_sheet_LL` SET `height`='$height',`weight`='$weight',`rr`='$rr',`bp`='$bp',`pulse`='$pulse',`temp`='$temp',`pallor`='$pallor',`cyanosis`='$cyanosis',`club`='$club',`saturation`='$saturation',`apvu`='$apvu',`gcs`='$gcs',`nervous`='$nervous',`respiratory`='$respiratory',`vascular`='$vascular',`abdomen`='$abdomen',`pr_diag`='$pr_diag',`rec_doc`='$rec_doc',`informant`='$informant',`patient`='$patient',`rel`='$rel' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_case_sheet_LL`(`patient_id`, `ipd_id`, `height`, `weight`, `rr`, `bp`, `pulse`, `temp`, `pallor`, `cyanosis`, `club`, `saturation`, `apvu`, `gcs`, `nervous`, `respiratory`, `vascular`, `abdomen`, `pr_diag`, `rec_doc`, `informant`, `patient`, `rel`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$height','$weight','$rr','$bp','$pulse','$temp','$pallor','$cyanosis','$club','$saturation','$apvu','$gcs','$nervous','$respiratory','$vascular','$abdomen','$pr_diag','$rec_doc','$informant','$patient','$rel','$date','$time','$usr')");
	}
	echo "Saved";
}

if($_POST["type"]=="load_rmo_notes")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$mode=$_POST['mode'];
	$usr=$_POST['usr'];
	?>
	
	<select id="mode" onchange="load_rmo_notes(this.value)">
		<option value="0">Current</option>
		<option value="1" <?php if($mode==1){echo "selected='selected'";}?>>All</option>
	</select>
	<?php
	if($mode==0)
	{
		$f=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_rmo_notes` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'"));
		$note=$f['notes'];
		
	?>
	<table class="table table-condensed" id="note_tbl">
		<tr>
			<td>
				<b>Note</b><br/>
				<textarea id="rmo_note" style="width:90%;resize:none;"><?php echo $note;?></textarea>
			</td>
		</tr>
		<tr>
			<td style="text-align:center">
				<button type="button" class="btn btn-info" id="rmo_btn" onclick="save_rmo_notes()">Save</button>
			</td>
		</tr>
	</table>
	<?php
	}
	if($mode==1)
	{
		$qq=mysqli_query($link,"SELECT * FROM `ipd_rmo_notes` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `slno` DESC");
	?>
	<table class="table table-condensed" id="note_tbl">
		<tr>
			<th>Note</th>
			<th width="20%">Date</th>
		</tr>
		<?php
		while($rr=mysqli_fetch_array($qq))
		{
			$note=str_replace("\n","<br/>",$rr['notes']);
		?>
		<tr>
			<td><?php echo $note;?></td>
			<td><?php echo convert_date_g($rr['date'])." ".convert_time($rr['time']);?></td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
	}
	?>
	<style>
		#note_tbl tr:hover{background:none;}
	</style>
	<?php
}

if($_POST["type"]=="save_rmo_notes")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$rmo_note=$_POST['rmo_note'];
	$usr=$_POST['usr'];
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_rmo_notes` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'"));
	if($n>0)
	{
		mysqli_query($link,"UPDATE `ipd_rmo_notes` SET `notes`='$rmo_note' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `date`='$date'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_rmo_notes`(`patient_id`, `ipd_id`, `notes`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$rmo_note','$date','$time','$usr')");
	}
	echo "Saved";
}

if($_POST["type"]=="upd_ot_book")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	//$ot=$_POST['ot'];
	$pr=$_POST['pr'];
	$ot_date=$_POST['ot_date'];
	$doc=$_POST['doc'];
	$usr=$_POST['usr'];
	
	mysqli_query($link,"UPDATE `ot_book` SET `procedure_id`='$pr',`consultantdoctorid`='$doc',`ot_date`='$ot_date' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	echo "Updated";
}

if($_POST["type"]=="remove_ot_book")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$s=mysqli_fetch_array(mysqli_query($link,"SELECT `scheduled` FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($s['scheduled']==0)
	{
		mysqli_query($link,"DELETE FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		echo "Cancelled";
	}
	else
	{
		echo "Already Shedulled";
	}
}

if($_POST["type"]=="history_collection")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ipd_pat_history_collection` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$male_texts="";
	$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
	if($pat_info['sex']=="Male")
	{
		$male_texts="disabled";
	}
	?>
	<table class="table table-condensed table-bordered" style="width:98%;margin:0px;">
		<tr>
			<td>
				<b>History of present illness</b><br/>
				<textarea id="present_illness" style="width:98%;resize:none;" placeholder="History of present illness"><?php echo $det['present_illness'];?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<b>History of past illness</b><br/>
				<textarea id="past_illness" style="width:98%;resize:none;" placeholder="History of past illness"><?php echo $det['past_illness'];?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<b>Medical History</b><br/>
				<textarea id="medical_history" style="width:98%;resize:none;" placeholder="Medical History"><?php echo $det['medical_history'];?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<b>Family History</b><br/>
				<textarea id="family_history" style="width:98%;resize:none;" placeholder="Family History"><?php echo $det['family_history'];?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<b>Personal History</b><br/>
				<textarea id="personal_history" style="width:98%;resize:none;" placeholder="Personal History"><?php echo $det['personal_history'];?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<b>Menstrul History</b><br/>
				<textarea id="menst_history" style="width:98%;resize:none;" placeholder="Menstrul History" <?php echo $male_texts;?>><?php echo $det['menst_history'];?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<b>Past Obstetric History</b><br/>
				<textarea id="obst_history" style="width:98%;resize:none;" placeholder="Past Obstetric History" <?php echo $male_texts;?>><?php echo $det['obst_history'];?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<button id="h_sav_btn" class="btn btn-primary" onclick="save_history_collection()">Save</button>
			</td>
		</tr>
	</table>
	<?php
}

if($_POST["type"]=="save_history_collection")
{
	$present_illness=mysqli_real_escape_string($link,$_POST['present_illness']);
	$past_illness=mysqli_real_escape_string($link,$_POST['past_illness']);
	$medical_history=mysqli_real_escape_string($link,$_POST['medical_history']);
	$family_history=mysqli_real_escape_string($link,$_POST['family_history']);
	$personal_history=mysqli_real_escape_string($link,$_POST['personal_history']);
	$menst_history=mysqli_real_escape_string($link,$_POST['menst_history']);
	$obst_history=mysqli_real_escape_string($link,$_POST['obst_history']);
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$det=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ipd_pat_history_collection` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($det)
	{
		mysqli_query($link,"UPDATE `ipd_pat_history_collection` SET `present_illness`='$present_illness',`past_illness`='$past_illness',`medical_history`='$medical_history',`family_history`='$family_history',`personal_history`='$personal_history',`menst_history`='$menst_history',`obst_history`='$obst_history' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		echo "Updated";
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_pat_history_collection`(`patient_id`, `ipd_id`, `present_illness`, `past_illness`, `medical_history`, `family_history`, `personal_history`, `menst_history`, `obst_history`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$present_illness','$past_illness','$medical_history','$family_history','$personal_history','$menst_history','$obst_history','$date','$time','$usr')");
		echo "Saved";
	}
}

if($_POST["type"]=="admission_assessment")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$f4=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_LL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	?>
	<table class="table table-condensed">
		<tr>
			<th colspan="2" style="text-align:center;">Physical Examination</th>
		</tr>
		<tr>
			<th width="40%">General Examination</th>
			<th>Local Examination</th>
		</tr>
		<tr>
			<td>Height (cm)</td>
			<td><input type="text" id="height" value="<?php echo $f4['height'];?>" placeholder="Height" /></td>
		</tr>
		<tr>
			<td>Weight (kg)</td>
			<td><input type="text" id="weight" value="<?php echo $f4['weight'];?>" placeholder="Weight" /></td>
		</tr>
		<tr>
			<td>Respiratory Rate/min : </td>
			<td><input type="text" id="rr" value="<?php echo $f4['rr'];?>" placeholder="Respiratory Rate" /></td>
		</tr>
		<tr>
			<td>Blood Pressure : </td>
			<td><input type="text" id="bp" value="<?php echo $f4['bp'];?>" placeholder="Blood Pressure" /></td>
		</tr>
		<tr>
			<td>Pulse/min : </td>
			<td><input type="text" id="pulse" value="<?php echo $f4['pulse'];?>" placeholder="Pulse" /></td>
		</tr>
		<tr>
			<td>Temperature : </td>
			<td><input type="text" id="temp" value="<?php echo $f4['temp'];?>" placeholder="Temperature" /></td>
		</tr>
		<tr>
			<td>Pallor : </td>
			<td><input type="text" id="pallor" value="<?php echo $f4['pallor'];?>" placeholder="Pallor" /></td>
		</tr>
		<tr>
			<td>Cyanosis : </td>
			<td><input type="text" id="cyanosis" value="<?php echo $f4['cyanosis'];?>" placeholder="Cyanosis" /></td>
		</tr>
		<tr>
			<td>Clubbing : </td>
			<td><input type="text" id="club" value="<?php echo $f4['club'];?>" placeholder="Clubbing" /></td>
		</tr>
		<tr>
			<th colspan="2">Others</th>
		</tr>
		<tr>
			<td>O2 Saturation : </td>
			<td><input type="text" id="saturation" value="<?php echo $f4['saturation'];?>" placeholder="O2 Saturation" /></td>
		</tr>
		<tr>
			<td>APVU : </td>
			<td><input type="text" id="apvu" value="<?php echo $f4['apvu'];?>" placeholder="APVU" /></td>
		</tr>
		<tr>
			<td>GCS : </td>
			<td><input type="text" id="gcs" value="<?php echo $f4['gcs'];?>" placeholder="GCS" /></td>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center;">Systemic Examination :</th>
		</tr>
		<tr>
			<td colspan="2">
				<b>CENTRAL NERVOUS SYSTEM :</b><br/>
				<textarea id="nervous" style="resize:none;width:95%" placeholder="CENTRAL NERVOUS SYSTEM"><?php echo $f4['nervous'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>RESPIRATORY SYSTEM :</b><br/>
				<textarea id="respiratory" style="resize:none;width:95%" placeholder="RESPIRATORY SYSTEM"><?php echo $f4['respiratory'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>CARDIO VASCULAR SYSTEM :</b><br/>
				<textarea id="vascular" style="resize:none;width:95%" placeholder="CARDIO VASCULAR SYSTEM"><?php echo $f4['vascular'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>ABDOMEN AND GENITALIA :</b><br/>
				<textarea id="abdomen" style="resize:none;width:95%" placeholder="ABDOMEN AND GENITALIA"><?php echo $f4['abdomen'];?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>PROVISIONAL DIAGNOSIS:</b><br/>
				<textarea id="pr_diag" style="resize:none;width:95%" placeholder="PROVISIONAL DIAGNOSIS"><?php echo $f4['pr_diag'];?></textarea>
			</td>
		</tr>
		<tr>
			<td rowspan="2"><b>Case history recorded by :<br/>Dr</b>
				<select id="rec_doc" class="">
					<option value="0">Select</option>
					<?php
					if($u_lev['levelid']==5)
					{
						$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `emp_id`='$usr'");
					}
					else
					{
						$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
					}
					while($rr=mysqli_fetch_array($qry))
					{
					?>
					<option value="<?php echo $rr['consultantdoctorid'];?>" <?php if($f4['rec_doc']==$rr['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $rr['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				History Informant : Patient/Attendant<br/>
				<input type="text" id="informant" style="width:95%" value="<?php echo $f4['informant'];?>" placeholder="History Informant" />
			</td>
		</tr>
		<tr>
			<td>
				Name of the patient or attendand<br/>
				<input type="text" id="patient" style="width:95%" value="<?php echo $f4['patient'];?>" placeholder="Name of the patient or attendand" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				Relation with patient (in case of attendant)<br/>
				<input type="text" id="rel" style="width:95%" value="<?php echo $f4['rel'];?>" placeholder="Relation with patient" />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<button type="button" class="btn btn-info" id="sav_case" onclick="save_admission_assessment()">Save</button>
				<button type="button" class="btn btn-primary" onclick="print_case_sheet()">Print</button>
			</td>
		</tr>
	</table>
	<style>
		.table tr:hover
		{background:none;}
		label{display:inline-block;margin-bottom:0px;font-weight:bold;margin-right:10px;}
		label:hover{color:#222222;box-shadow: -3px 3px 6px 0px #ccc;}
		input[type="checkbox"]{margin:0px 0px 0px;}
	</style>
	<?php
}

if($_POST["type"]=="save_admission_assessment")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$height=$_POST['height'];
	$weight=$_POST['weight'];
	$rr=$_POST['rr'];
	$bp=$_POST['bp'];
	$pulse=$_POST['pulse'];
	$temp=$_POST['temp'];
	$pallor=$_POST['pallor'];
	$cyanosis=$_POST['cyanosis'];
	$club=$_POST['club'];
	$saturation=$_POST['saturation'];
	$apvu=$_POST['apvu'];
	$gcs=$_POST['gcs'];
	$nervous=$_POST['nervous'];
	$respiratory=$_POST['respiratory'];
	$vascular=$_POST['vascular'];
	$abdomen=$_POST['abdomen'];
	$pr_diag=$_POST['pr_diag'];
	$rec_doc=$_POST['rec_doc'];
	$informant=$_POST['informant'];
	$patient=$_POST['patient'];
	$rel=$_POST['rel'];
	$usr=$_POST['usr'];
	
	$n4=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_case_sheet_LL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($n4>0)
	{
		mysqli_query($link,"UPDATE `ipd_case_sheet_LL` SET `height`='$height',`weight`='$weight',`rr`='$rr',`bp`='$bp',`pulse`='$pulse',`temp`='$temp',`pallor`='$pallor',`cyanosis`='$cyanosis',`club`='$club',`saturation`='$saturation',`apvu`='$apvu',`gcs`='$gcs',`nervous`='$nervous',`respiratory`='$respiratory',`vascular`='$vascular',`abdomen`='$abdomen',`pr_diag`='$pr_diag',`rec_doc`='$rec_doc',`informant`='$informant',`patient`='$patient',`rel`='$rel' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	}
	else
	{
		mysqli_query($link,"INSERT INTO `ipd_case_sheet_LL`(`patient_id`, `ipd_id`, `height`, `weight`, `rr`, `bp`, `pulse`, `temp`, `pallor`, `cyanosis`, `club`, `saturation`, `apvu`, `gcs`, `nervous`, `respiratory`, `vascular`, `abdomen`, `pr_diag`, `rec_doc`, `informant`, `patient`, `rel`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$height','$weight','$rr','$bp','$pulse','$temp','$pallor','$cyanosis','$club','$saturation','$apvu','$gcs','$nervous','$respiratory','$vascular','$abdomen','$pr_diag','$rec_doc','$informant','$patient','$rel','$date','$time','$usr')");
	}
	echo "Saved";
}

if($_POST["type"]=="oo")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
}
?>
