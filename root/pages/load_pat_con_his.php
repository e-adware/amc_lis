<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$discount_element_disable="";
if($emp_info["discount_permission"]==0)
{
	$discount_element_disable="readonly";
}

$day_number=convert_date_to_day_num($date);

$uhid=$_POST["uhid"];
$opd_id=$_POST["opd_id"];

$pat_center=mysqli_fetch_array(mysqli_query($link, " SELECT `center_no` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$center_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `centremaster` WHERE `centreno`='$pat_center[center_no]' "));

$pat_card_info=mysqli_fetch_array(mysqli_query($link, " SELECT `card_id` FROM `patient_card_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));
$sel_card_id=$pat_card_info["card_id"];

$conslt=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) "));
$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `refbydoctorid`, `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) "));

$apnmt_bk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$emergency_check="";
$emergency_val=0;
if($apnmt_bk["emergency"]==1)
{
	$emergency_check="checked";
	$emergency_val=1;
}

$paymt=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`!='Credit' "));
if(!$pay_det)
{
	$pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
}

$visitt_fee=$paymt['visit_fee'];

$review_check="";
$review_val=0;
if($visitt_fee==0)
{
	$review_check="checked";
	$review_val=1;
}

if($paymt["emergency_fee"]>0)
{
	$emr_check="checked";
}else
{
	$emr_check="";
}

$cross_consult=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cross_consultation` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
if($cross_consult)
{
	$cross_consult_check="checked";
}else
{
	$cross_consult_check="";
}
//~ $check_last_regd_fee_qry=mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' and `regd_fee`>0 and `date` like '$this_year%' order by `slno` DESC limit 0,1 ");
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

$pat_visit_type_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

?>
<input type="hidden" id="opd_id_edit" value="<?php echo $opd_id; ?>">
<table class="table custom_table">
	<tr>
		<th class="span3">Select Department</th>
		<td colspan="">
			<select id="dept_id" onChange="dept_sel('edit')" onKeyUp="dept_sel_Up(event)">
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
		<th>Referred By</th>
		<td colspan="1">
			<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="ref_load_refdoc1()" onKeyUp="ref_load_refdoc(this.value,event,'opd')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_doc['ref_name'].'-'.$ref_doc['refbydoctorid']; ?>" />
			<button class="btn btn-new btn-mini" name="new_doc" id="new_doc" onClick="load_new_ref_doc()"><i class="icon-edit"></i> New</button>
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
<table class="table custom_table" id="loaded_pat_con">
	<input type="hidden" id="opd_allow_credit" value="<?php echo $center_info["allow_credit"]; ?>">
	<input type="hidden" id="opd_allow_credit_name" value="<?php echo $center_info["centrename"]; ?>">
	<tr>
		<th>Center</th>
		<td colspan="1">
			<select id="sel_center" onChange="sel_center_change('opd',this.value)" >
			<?php
			$center_qry=mysqli_query($link, " SELECT * FROM `centremaster` order by `centreno` ");
			while($center=mysqli_fetch_array($center_qry))
			{
				if($center['centreno']==$pat_center["center_no"]){ $sel="selected"; }else{ $sel=""; }
				echo "<option value='$center[centreno]' $sel >$center[centrename]</option>";
			}
			?>
			</select>
		</td>
		<th>Session</th>
		<td colspan="1">
			<select id="doctor_session" onKeyUp="doctor_session_Up(event)">
				<option value="1" <?php if($apnmt_bk["doctor_session"]==1){ echo "selected"; } ?> >Session 1</option>
				<option value="2" <?php if($apnmt_bk["doctor_session"]==2){ echo "selected"; } ?> >Session 2</option>
				<option value="3" <?php if($apnmt_bk["doctor_session"]==3){ echo "selected"; } ?> >Session 3</option>
			</select>
		</td>
	</tr>
	<tr>
		<th class="span3">Select Doctor</th>
		<td>
			<input type="text" name="ad_doc" id="ad_doc" class="span2 ad_doc" size="25" onFocus="load_refdoc1_edit('<?php echo $opd_id ?>')" onKeyUp="adload_refdoc(this.value,event)" onBlur="javascript:$('#adref_doc').fadeOut(500)" value="<?php echo $conslt["Name"]."-".$conslt["consultantdoctorid"]; ?>">
			<input type="text" name="con_doc_id" id="con_doc_id" style="display:none;" value="<?php echo $conslt["consultantdoctorid"]; ?>">
			<div id="addoc_info"></div>
			<div id="adref_doc" align="center">
				<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
					<th>Doctor ID</th>
					<th>Doctor Name</th>
					<?php
						$d=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `dept_id`='$conslt[dept_id]' AND `status`='0' order by `Name` ");
						//$d=mysqli_query($link, " SELECT a.* FROM `consultant_doctor_master` a, `employee` b WHERE a.`emp_id`=b.`emp_id` AND a.`dept_id`='$conslt[dept_id]' AND b.`status`='0' ORDER BY `Name` ");
						$i=1;
						while($d1=mysqli_fetch_array($d))
						{
							// Visit Fee
							$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `date` FROM `appointment_book` WHERE `patient_id`='$uhid' AND  `consultantdoctorid`='$d1[consultantdoctorid]' AND `visit_fee`>0 order by `slno`,`opd_id` "));
							
							//$check_last_visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `date` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `visit_fee`>0 order by `slno`,`opd_id` "));
							$check_last_visit_fee_date=$check_last_visit_fee["date"];
							if($check_last_visit_fee_date=="")
							{
								$visitt_fee=$d1["opd_visit_fee"];
							}else
							{
								$dates_array = getDatesFromRange($check_last_visit_fee_date, $date);
								$visit_fee_day_diff=sizeof($dates_array);
								if($visit_fee_day_diff<=$d1["validity"])
								{
									$visitt_fee=0;
								}else
								{
									$visitt_fee=$d1["opd_visit_fee"];
								}
							}
							
							// Regd Fee
							//$check_last_regd_fee_qry=mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' AND `dept_id`='$d1[dept_id]' AND `regd_fee`>0 order by `slno` DESC limit 0,1 ");
							
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
					?>
						<tr onClick="addoc_load('<?php echo $d1['consultantdoctorid'];?>','<?php echo $d1['Name'];?>','<?php echo $visitt_fee;?>','<?php echo $d1['validity'];?>','<?php echo $regdd_fee;?>','<?php echo $d1['opd_reg_validity'];?>')" style="cursor:pointer" <?php echo "id=addoc".$i;?>>
							<td>
								<?php echo $d1['consultantdoctorid'];?>
							</td>
							<td>
								<?php echo $d1['Name'];?>
								<div <?php echo "id=addvdoc".$i;?> style="display:none;">
									<?php echo "#".$d1['consultantdoctorid']."#".$d1['Name']."#".$visitt_fee."#".$d1['validity']."#".$regdd_fee."#".$d1['opd_reg_validity'];?>
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
			<input class="datepicker" type="text" id="appoint_date" onKeyUp="appoint_date(event)" onChange="appoint_date_change()" value="<?php echo $apnmt_bk["appointment_date"]; ?>" >
		</td>
	</tr>
	<tr style="display:none;">
		<th>Emergency</th>
		<td>
			<label><input type="checkbox" id="pat_emergency" onChange="pat_emergency('2')" value="1" <?php echo $emr_check; ?>> Yes</label>
		</td>
		<th class="span3" style="display:;">Emergency Fee</th>
		<td style="display:;">
			<input type="hidden" id="emerg_fee" value="<?php echo $check_regd_fee["emerg_fee"]; ?>" readonly>
			<input type="text" id="emergency_fee" value="<?php echo $paymt["emergency_fee"]; ?>" readonly>
		</td>
	</tr>
	<tr style="display:none;">
		<th>Cross Consultation</th>
		<td>
			<label><input type="checkbox" id="pat_cross_consult" onChange="pat_cross_consult()" value="1" <?php echo $cross_consult_check; ?>> Yes</label>
		</td>
		<th class="span3" style="display:;">Cross Consultation Fee</th>
		<td style="display:;">
			<input type="hidden" id="cross_fee" value="150">
			<input type="text" id="cross_consult_fee" value="<?php echo $cross_consult["amount"]; ?>" readonly>
		</td>
	</tr>
	<tr style="display:;">
		<th class="span3">Visit Fee</th>
		<td>
			<input type="text" id="visit_fee" value="<?php echo $paymt["visit_fee"]; ?>" onKeyup="visit_fee_ch()" readonly>
			<br>
			<label>
				<input type="checkbox" id="revisit_check" onChange="revisit_check_ch(this.value)" value="<?php echo $review_val; ?>" <?php echo $review_check; ?> >
				Review
			</label>
			 &nbsp;&nbsp;
			<label>
				<input type="checkbox" id="emergency_check" onChange="emergency_check_ch(this.value)" value="<?php echo $emergency_val; ?>" <?php echo $emergency_check; ?> >
				Emergency
			</label>
		</td>
		<th>Regd Fee</th>
		<td>
			<input type="text" id="regd_fee" readonly value="<?php echo $paymt["regd_fee"]; ?>" onKeyup="regd_fee_ch()" >
			<input type="hidden" id="regdd_fee" value="<?php echo $paymt["regd_fee"]; ?>">
		</td>
	</tr>
	<tr style="display:;">
		<th>Total</th>
		<td colspan="">
			<input type="text" id="total" value="<?php echo $paymt["tot_amount"]; ?>" readonly>
		</td>
		<th style="display:;">Discount</th>
		<td style="display:;">
			<input type="text" class="span1" id="dis_per" placeholder="%" onKeyUp="dis_per(this.value,event)" value="<?php echo $paymt["dis_per"]; ?>" <?php echo $discount_element_disable; ?> >%
			<input type="text" class="span1" id="dis_amnt" onKeyUp="dis_amnt(this.value,event)" value="<?php echo $paymt["dis_amt"]; ?>" <?php echo $discount_element_disable; ?> >INR<br>
			<?php if($paymt["dis_reason"]!=''){ ?>
			<span id="d_reason" style="display:;">
				<input type="text" class="span2" id="dis_reason" placeholder="Reason for discount" onKeyUp="dis_reason(this.value,event)" value="<?php echo $paymt["dis_reason"]; ?>">
			</span>
			<?php }else{ ?>
				<span id="d_reason" style="display:none;">
				<input type="text" class="span2" id="dis_reason" placeholder="Reason for discount" onKeyUp="dis_reason(this.value,event)" >
			</span>
			<?php } ?>
		</td>
	</tr>
	<tr style="display:;">
		<th>Advance</th>
		<td>
			<input type="text" id="advance" onKeyUp="advance(this.value,event)" value="<?php echo $paymt["advance"]; ?>"><br>
			<?php if($paymt["bal_reason"]!=''){ ?>
			<span id="b_reason" style="display:;">
				<input type="text" id="bal_reason" placeholder="Reason for balance" onKeyUp="bal_reason(this.value,event)" value="<?php echo $paymt["bal_reason"]; ?>" >
			</span>
			<?php }else{ ?>
			<span id="b_reason" style="display:none;">
				<input type="text" id="bal_reason" placeholder="Reason for balance" onKeyUp="bal_reason(this.value,event)" >
			</span>
			<?php } ?>
		</td>
		<th>Balance</th>
		<td>
			<input type="text" id="balance" value="<?php echo $paymt["balance"]; ?>" readonly>
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
					if($pay_det["payment_mode"]==$pay_mode["p_mode_name"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$pay_mode[p_mode_name]' $sel>$pay_mode[p_mode_name]</option>";
				}
			?>
			</select>
			<!--<select id="pay_mode" onkeyup="pay_mode(this.value,event)" onChange="pay_mode_change(this.value)">
				<option value="Cash" <?php if($pay_det["payment_mode"]=="Cash"){ echo "selected"; } ?>>Cash</option>
				<option value="Card" <?php if($pay_det["payment_mode"]=="Card"){ echo "selected"; } ?>>Card</option>
				<option value="Cheque" <?php if($pay_det["payment_mode"]=="Cheque"){ echo "selected"; } ?>>Cheque</option>
				<option value="NEFT" <?php if($pay_det["payment_mode"]=="NEFT"){ echo "selected"; } ?>>NEFT</option>
				<option value="RTGS" <?php if($pay_det["payment_mode"]=="RTGS"){ echo "selected"; } ?>>RTGS</option>
				<option value="Credit" <?php if($pay_det["payment_mode"]=="Credit"){ echo "selected"; } ?>>Credit</option>
			</select>-->
			<br>
		<?php
			$cheque_ref_type="hidden";
			$p_mode_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_master` WHERE `p_mode_name`='$pay_det[payment_mode]' "));
			if($p_mode_info["ref_field"]==0)
			{
				$cheque_ref_type="text";
			}
		?>
			<input type="<?php echo $cheque_ref_type; ?>" class="" id="cheque_ref_no" value="<?php echo $pay_det["cheque_ref_no"]; ?>" placeholder="Cheque / Reference No" onKeyUp="cheque_ref_no_up(this.value,event)">
		</td>
		<th>Patient Type</th>
		<td colspan="1">
			<select id="visit_type_id" onkeyup="visit_type_id(this.value,event)">
		<?php
			$visit_type_qry=mysqli_query($link, "SELECT `visit_type_id`, `visit_type_name` FROM `patient_visit_type_master` WHERE `p_type_id`=1 ORDER BY `visit_type_id`");
			while($visit_type=mysqli_fetch_array($visit_type_qry))
			{
				if($pat_visit_type_det["visit_type_id"]==$visit_type["visit_type_id"]){ $visit_sel="selected"; }else{ $visit_sel=""; }
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
				if($sel_card_id==$card_val["card_id"]){ $card_sel="selected"; }else{ $card_sel=""; }
				echo "<option value='$card_val[card_id]' $card_sel>$card_val[card_name]</option>";
			}
		?>
			</select>
		</td>
	</tr>
</table>
<table class="table custom_table" id="load_pat_con" style="display:none;">
	
</table>
<center>
	<button class="btn btn-save" id="save" onclick="save_pat('update')"> <i class="icon-save"></i> Update</button>
	<button class="btn btn-back" onclick="check_appointment('','','<?php echo $opd_id; ?>')"><i class="icon-backward"></i> Back</button>
</center>
