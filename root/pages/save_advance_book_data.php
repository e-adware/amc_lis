<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d"); // important
$time=date("H:i:s");

$uhid=$_POST["uhid"];

$pat_id=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id_adv` FROM `advance_book_link` WHERE `patient_id`='$uhid' "));
	
$adv_appnt_info=mysqli_fetch_array(mysqli_query($link, " SELECT `dept_id`, `consultantdoctorid`, `appointment_date` FROM `patient_info_adv` WHERE `patient_id`='$pat_id[patient_id_adv]' "));

$conslt=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$adv_appnt_info[consultantdoctorid]' "));

$check_regd_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_registration_fees` "));

// Registration Fees
$check_last_regd_fee_qry=mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' order by `slno` DESC limit 0,1 ");
$check_last_regd_fee_num=mysqli_num_rows($check_last_regd_fee_qry);
$check_regd_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_registration_fees` "));
if($check_last_regd_fee_num==0)
{
	$regdd_fee=$check_regd_fee["regd_fee"];
}else
{
	$check_last_regd_fee=mysqli_fetch_array($check_last_regd_fee_qry);
	$dates_array = getDatesFromRange($check_last_regd_fee["date"], $date);
	$day_diff=sizeof($dates_array);
	if($day_diff<$check_regd_fee["validity"])
	{
		$regdd_fee=0;
	}else
	{
		$regdd_fee=$check_regd_fee["regd_fee"];
	}
}
// Visit Fees
$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `date` FROM `appointment_book` WHERE `patient_id`='$uhid' AND  `consultantdoctorid`='$adv_appnt_info[consultantdoctorid]' AND `visit_fee`>0 order by `slno`,`opd_id` "));
$check_last_visit_fee_date=$check_last_visit_fee["date"];
if($check_last_visit_fee=="")
{
	$visitt_fee=$conslt["opd_visit_fee"];
}else
{
	$dates_array = getDatesFromRange($check_last_visit_fee_date, $date);
	$visit_fee_day_diff=sizeof($dates_array);
	if($visit_fee_day_diff<$conslt["validity"])
	{
		$visitt_fee=0;
	}else
	{
		$visitt_fee=$conslt["opd_visit_fee"];
	}
}
//echo $visitt_fee;
$tot_fees=$visitt_fee+$regdd_fee;

?>
<input type="hidden" id="opd_id_edit" value="0">
<table class="table custom_table">
	<tr>
		<th class="span3">Select Department</th>
		<td colspan="3">
			<select id="deptt_id" onChange="deptt_sel()" onKeyUp="deptt_sel_Up(event)">
				<option value="0">Select</option>
			<?php
			$dept_qry=mysqli_query($link, " SELECT * FROM `doctor_specialist_list` order by `name` ");
			while($dept=mysqli_fetch_array($dept_qry))
			{
				if($conslt["dept_id"]==$dept['speciality_id']){ $dep_sel="selected"; }else{ $dep_sel=""; }
				echo "<option value='$dept[speciality_id]' $dep_sel >$dept[name]</option>";
			}
			?>
			</select>
		</td>
		
	</tr>
</table>
<table class="table custom_table" id="loaded_pat_con">
	<tr>
		<th class="span3">Select Doctor</th>
		<td><!-- onFocus="load_refdoc1()" -->
			<input type="text" name="ad_doc" id="ad_doc" class="span3" size="25" onFocus="adload_refdoc1()" onKeyUp="adload_refdoc(this.value,event)" onBlur="javascript:$('#adref_doc').fadeOut(500)" value="<?php echo $conslt["Name"]."-".$adv_appnt_info['consultantdoctorid']; ?>" >
			<input type="text" name="con_doc_id" id="con_doc_id" style="display:none;" value="<?php echo $adv_appnt_info['consultantdoctorid']; ?>" >
			<div id="addoc_info"></div>
			<div id="adref_doc" align="center">
				<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
					<th>Doctor ID</th>
					<th>Doctor Name</th>
					<?php
						$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `dept_id`='$conslt[dept_id]' order by `Name` ");
						$i=1;
						while($d1=mysqli_fetch_array($d))
						{
							$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `date` FROM `appointment_book` WHERE `patient_id`='$uhid' AND  `consultantdoctorid`='$d1[consultantdoctorid]' AND `visit_fee`>0 order by `slno`,`opd_id` "));
							$check_last_visit_fee_date=$check_last_visit_fee["date"];
							if($check_last_visit_fee_date=="")
							{
								$visitt_fee=$d1["opd_visit_fee"];
							}else
							{
								$dates_array = getDatesFromRange($check_last_visit_fee_date, $date);
								$visit_fee_day_diff=sizeof($dates_array);
								if($visit_fee_day_diff<$d1["validity"])
								{
									$visitt_fee=0;
								}else
								{
									$visitt_fee=$d1["opd_visit_fee"];
								}
							}
					?>
						<tr onClick="addoc_load('<?php echo $d1['consultantdoctorid'];?>','<?php echo $d1['Name'];?>','<?php echo $visitt_fee;?>','<?php echo $d1['validity'];?>')" style="cursor:pointer" <?php echo "id=addoc".$i;?>>
							<td>
								<?php echo $d1['consultantdoctorid'];?>
							</td>
							<td>
								<?php echo $d1['Name'];?>
								<div <?php echo "id=addvdoc".$i;?> style="display:none;">
									<?php echo "#".$d1['consultantdoctorid']."#".$d1['Name']."#".$visitt_fee."#".$d1['validity'];?>
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
			<input class="datepicker" type="text" id="appoint_date" onKeyUp="appoint_date(event)" onChange="appoint_date_change()" value="<?php echo $adv_appnt_info["appointment_date"]; ?>" disabled>
		</td>
	</tr>
	<tr>
		<th>Emergency</th>
		<td>
			<label><input type="checkbox" id="pat_emergency" onChange="pat_emergency()" value="1" > Yes</label>
		</td>
		<th class="span3">Emergency Fee</th>
		<td>
			<input type="hidden" id="emerg_fee" value="<?php echo $check_regd_fee["emerg_fee"]; ?>" readonly>
			<input type="text" id="emergency_fee" value="0" readonly>
		</td>
	</tr>
	<tr>
		<th class="span3">Visit Fee</th>
		<td>
			<input type="text" id="visit_fee" value="<?php echo $visitt_fee; ?>" readonly>
		</td>
		<th>Regd Fee</th>
		<td>
			<input type="text" id="regd_fee" readonly value="<?php echo $regdd_fee; ?>">
			<input type="hidden" id="regdd_fee" readonly value="<?php echo $regdd_fee; ?>">
		</td>
	</tr>
	<tr>
		<th>Total</th>
		<td>
			<input type="text" id="total" value="<?php echo $tot_fees; ?>" readonly>
		</td>
		<th>Discount</th>
		<td>
			<input type="text" class="span1" id="dis_per" placeholder="%" onKeyUp="dis_per(this.value,event)" value="">
			<input type="text" class="span1" id="dis_amnt" onKeyUp="dis_amnt(this.value,event)" value="0"><br>			
			<span id="d_reason" style="display:none;">
				<input type="text" class="span2" id="dis_reason" placeholder="Reason for discount" onKeyUp="dis_reason(this.value,event)" >
			</span>
		</td>
	</tr>
	<tr>
		<th>Advance</th>
		<td>
			<input type="text" id="advance" onKeyUp="advance(this.value,event)" value="<?php echo $tot_fees; ?>"><br>
			<span id="b_reason" style="display:none;">
				<input type="text" id="bal_reason" placeholder="Reason for balance" onKeyUp="bal_reason(this.value,event)" >
			</span>
		</td>
		<th>Balance</th>
		<td>
			<input type="text" id="balance" value="0" readonly>
		</td>
	</tr>
	<tr>
		<th>Payment Mode</th>
		<td colspan="3">
			<select id="pay_mode" onkeyup="pay_mode(this.value,event)">
				<option>Cash</option>
				<option>Card</option>
				<option>Cheque</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>Referred By</th>
		<td colspan="1">
			<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="ref_load_refdoc1()" onKeyUp="ref_load_refdoc(this.value,event,'opd')" onBlur="javascript:$('#ref_doc').fadeOut(500)" />
			<input type="button" name="new_doc" id="new_doc" value="New" class="btn btn-info btn-mini" onClick="load_new_ref_doc()" />
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
		<th>Center</th>
		<td colspan="3">
			<select id="sel_center" onChange="sel_center_change('opd',this.value)">
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
</table>
<center>
	<button class="btn btn-info" id="save" onClick="save_pat('save')">Save</button>
</center>

