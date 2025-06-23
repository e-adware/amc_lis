<?php
$c_user=$_SESSION["emp_id"];

$branch_str=" AND `branch_id`='$p_info[branch_id]'";
$element_style="display:none";
$center_span="span3";
$center_col_span="5";
if($p_info["levelid"]==1)
{
	//$branch_str="";
	//$element_style="";
	//$center_span="span2";
	//$center_col_span="3";
}

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$p_type_id=9; // MISCELLANEOUS
$p_type_master=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));

$uhid=base64_decode($_GET["uhid"]);
$uhid=trim($uhid);

$pin=base64_decode($_GET["ipd"]);
$pin=trim($pin);

if(!$uhid){ $uhid=0; }
if(!$pin){ $pin=0; }

$patient_id=$uhid;
$ipd=$opd_id=$pin;

$bed_tr_str="";
$btn_name="Save";
if($opd_id!=0)
{
	$btn_name="Update";
	
	$bed_tr_str="display:none;";
}

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
$pat_info_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));

$pat_name_str=explode(". ",$pat_info["name"]);
$pat_name_title=trim($pat_name_str[0]);
$pat_name=trim($pat_name_str[1]." ".$pat_name_str[2]." ".$pat_name_str[3]." ".$pat_name_str[4]);

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `refbydoctorid`, `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));

$hguide_id=101;
if($pat_reg)
{
	$hguide_id=$pat_reg["hguide_id"];
}
//$health_guide=mysqli_fetch_array(mysqli_query($link, " SELECT `hguide_id`, `name` FROM `health_guide` WHERE `hguide_id`='$hguide_id' "));

$pat_visit_type_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));

$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);

$str="";
if($_GET["uhid_str"])
{
	$str.="&uhid_str=$uhid_str";
	$refresh_str.="&uhid_str=$uhid_str";
}

if($_GET["pin_str"])
{
	$str.="&pin_str=$pin_str";
	$refresh_str.="&pin_str=$pin_str";
}

if($_GET["fdate_str"])
{
	$str.="&fdate_str=$fdate_str";
	$refresh_str.="&fdate_str=$fdate_str";
}

if($_GET["tdate_str"])
{
	$str.="&tdate_str=$tdate_str";
	$refresh_str.="&tdate_str=$tdate_str";
}

if($_GET["name_str"])
{
	$str.="&name_str=$name_str";
	$refresh_str.="&name_str=$name_str";
}

if($_GET["phone_str"])
{
	$str.="&phone_str=$phone_str";
	$refresh_str.="&phone_str=$phone_str";
}

if($_GET["param_str"])
{
	$str.="&param=$param_str";
}

if($_GET["pat_type_str"])
{
	$str.="&pat_type_str=$pat_type_str";
	$refresh_str.="&pat_type_str=$pat_type_str";
}


?>
<!--header-->
<div id="content-header">
    <div class="header_div">
		<span class="header"> <?php echo $menu_info["par_name"]; ?></span>
		<br>
		<small>
			( <b style="color:#ff0000;">*</b> ) mark mandatory
		</small>
    </div>
</div>
<!--End-header  container-fluid -->
<div class="">
	<div>
		<span style="float:right;">
		<?php if($str){ ?>
			<button class="btn btn-back" id="add" onclick="window.location='processing.php?v=0<?php echo $str; ?>'"><i class="icon-backward"></i> Back</button>
		<?php } ?>
		</span>
	<?php if($uhid==0){ ?>
		<div class="search_div">
			<table id="padd_tbl" class="table table-condensed">
				<tr>
					<th>UHID</th>
					<td>
						<input type="text" class="span2" id="search_uhid" onkeyup="load_emp(this.value,event,'uhid')" placeholder="Type UHID" >
					</td>
					<th><?php echo $p_type_master["prefix"]; ?></th>
					<td>
						<input type="text" class="span2" id="search_pin" onkeyup="load_emp(this.value,event,'pin')" placeholder="Type Bill No" >
					</td>
					<th>Name</th>
					<td>
						<input type="text" class="span2" id="search_name" onkeyup="load_emp(this.value,event,'name')" placeholder="Type Name" >
					</td>
					<th>Phone No</th>
					<td>
						<input type="text" class="span2" class="span2" id="search_phone" onkeyup="load_emp(this.value,event,'phone')" placeholder="Type Phone Numebr" >
					</td>
				</tr>
				<tr>
					<td colspan="8">
						<div id="pateint_list" style="max-height:450px;overflow-y:scroll;">
							
						</div>
					</td>
				</tr>
			</table>
		</div>
	<?php } ?>
		<div class="patient_info_div" style="<?php echo $edit_info_style; ?>">
			<table id="patient_info_tbl" class="table table-condensed" style="background-color:#FFF">
				<tr>
					<th colspan="6" style="text-align:center;">
						<h4>Patient Information</h4>
					</th>
				</tr>
				<tr>
					<th>Name <b style="color:#ff0000;">*</b></th>
					<td>
						<select id="name_title" onChange="name_title_ch(this.value)" onKeyup="name_title_up(this,event)" class="span1 pat_info" autofocus>
						<?php
							$title_qry=mysqli_query($link, " SELECT * FROM `name_title` ORDER BY `title_id` ");
							while($val=mysqli_fetch_array($title_qry))
							{
								if($pat_name_title."."==$val['title']){ $title_sel="selected"; }else{ $title_sel=""; }
								echo "<option value='$val[title]' $title_sel>$val[title]</option>";
							}
						?>
						</select>
						<input type="text" class="capital pat_info" id="pat_name" onKeyup="pat_name_up(this,event)" value="<?php echo $pat_name; ?>" style="width: 180px">
					</td>
					<th>DOB (DD-MM-YYYY) </th>
					<td>
						<input type="text" id="dob" class="span2 dob pat_info" maxlength="10" placeholder="DD-MM-YYYY" onKeyup="dob_up(this,event)" value="<?php echo $pat_info["dob"]; ?>" onpaste="return false;" ondrop="return false;">
					</td>
					<th>Age <b style="color:#ff0000;">*</b></th>
					<td>
						<span>
							<input type="text" id="age_y" class="numericc pat_info" onKeyup="age_y_check(this,event)" placeholder="Years" title="Years" style="width: 50px;" onpaste="return false;" ondrop="return false;" maxlength="3">
							<input type="text" id="age_m" class="numericc pat_info" onKeyup="age_m_check(this,event)" placeholder="Months" title="Months" style="width: 50px;" onpaste="return false;" ondrop="return false;" maxlength="2">
							<input type="text" id="age_d" class="numericc pat_info" onKeyup="age_d_check(this,event)" placeholder="Days" title="Days" style="width: 50px;" onpaste="return false;" ondrop="return false;" maxlength="2">
						</span>
						<span style="display:none;">
							<input type="text" id="age" class="span1" onKeyup="age_check(this.value,event)" onBlur="border_color_blur(this.id,this.value)" value="<?php echo $pat_info["age"]; ?>">
							<text id="year"><?php echo $pat_info["age_type"]; ?></text>
							<span id="g_id" style="display:none;"><input type="text" id="grdn_name" class="span3" placeholder="Enter Guardian Name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)"></span>
						</span>
					</td>
				</tr>
				<tr>
					<th>Sex <b style="color:#ff0000;">*</b></th>
					<td>
						<select id="sex" class="span3 pat_info" onKeyup="sex_up(this,event)">
							<option value="Male" <?php if($pat_info["sex"]=="Male"){ echo "selected"; } ?>>Male</option>
							<option value="Female" <?php if($pat_info["sex"]=="Female"){ echo "selected"; } ?>>Female</option>
							<option value="Other" <?php if($pat_info["sex"]=="Other"){ echo "selected"; } ?>>Other</option>
						</select>
					</td>
					<th>Phone <b style="color:#ff0000;">*</b></th>
					<td>
						<input type="text"class="span2 numericc pat_info" id="phone" maxlength="10" onKeyup="phone_up(this,event)" value="<?php echo $pat_info["phone"]; ?>" onpaste="return false;" ondrop="return false;">
					</td>
					<th>Marital Status</th>
					<td>
						<select id="marital_status" class="pat_info" onkeyup="marital_status_up(this,event)">
						<?php
							$marry_qry=mysqli_query($link, " SELECT * FROM `marital_status` ORDER BY `status_id` ");
							while($marry=mysqli_fetch_array($marry_qry))
							{
								if($pat_info_other["marital_status"]==$marry["status_id"]){ $marry_sel="selected"; }else{ $marry_sel=""; }
								echo "<option value='$marry[status_id]' $marry_sel>$marry[status_name]</option>";
							}
						?>
						</select>
					</td>
					<th style="display:none;">Email</th>
					<td style="display:none;">
						<input type="text"class="pat_info" id="email" onKeyup="email_up(this,event)" value="<?php echo $pat_info["email"]; ?>">
					</td>
				</tr>
				<tr style="display:none;">
					<th>Father's Name</th>
					<td>
						<input type="text" class="span3 capital pat_info" id="father_name" onKeyup="father_name_up(this,event)" value="<?php echo $pat_info_rel["father_name"]; ?>">
					</td>
					<th>Mother's Name</th>
					<td>
						<input type="text" class="span2 capital pat_info" id="mother_name" onKeyup="mother_name_up(this,event)" value="<?php echo $pat_info_rel["mother_name"]; ?>">
					</td>
					<th>Guardian's Name <b style="color:#ff0000;">*</b></th>
					<td>
						<input type="text" class="capital pat_info" id="gd_name" onKeyup="gd_name_up(this,event)" value="<?php echo $pat_info["gd_name"]; ?>">
					</td>
				</tr>
				<tr style="display:none;">
					<th>Relation with Guardian <b style="color:#ff0000;">*</b></th>
					<td>
						<input type="text" class="span3 capital pat_info" id="g_relation" onKeyup="g_relation_up(this,event)" value="<?php echo $pat_info_other["relation"]; ?>" list="relation_datalist">
						<datalist id="relation_datalist">
						<?php
							$relation_datalist_qry=mysqli_query($link," SELECT DISTINCT `relation` FROM `patient_other_info` WHERE `relation`!='' ORDER BY `relation` ");
							while($relation_datalist=mysqli_fetch_array($relation_datalist_qry))
							{
								echo "<option value='$relation_datalist[relation]'></option>";
							}
						?>
						</datalist>
					</td>
					<th>Guardian's Contact <b style="color:#ff0000;">*</b></th>
					<td>
						<input type="text" class="span2 numericc pat_info" id="gd_phone" maxlength="10" onkeyup="gd_phone_up(this,event)" value="<?php echo $pat_info_rel["gd_phone"]; ?>">
					</td>
					<th>Income Group</th>
					<td>
						<select id="income_id" class="pat_info" onkeyup="income_up(this,event)">
						<?php
							$income_qry=mysqli_query($link, " SELECT `income_id`, `income` FROM `income_master` ORDER BY `income_id` ");
							while($income=mysqli_fetch_array($income_qry))
							{
								if($pat_info_other["income_id"]==$income["income_id"]){ $income_sel="selected"; }else{ $income_sel=""; }
								echo "<option value='$income[income_id]' $income_sel>$income[income]</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<th>State  <b style="color:#ff0000;">*</b></th>
					<td>
						<select class="span3 pat_info" id="state" onchange="state_change()" onkeyup="state_up(this,event)">
							<option value="0">Select</option>
						<?php
							$state_qry=mysqli_query($link, " SELECT * FROM `state` ORDER BY `name` ");
							while($state=mysqli_fetch_array($state_qry))
							{
								if($pat_info_rel["state"])
								{
									if($pat_info_rel["state"]==$state["state_id"]){ $state_sel="selected"; }else{ $state_sel=""; }
								}
								else
								{
									$company_detaill=mysqli_fetch_array(mysqli_query($link, " SELECT `state` FROM `company_name` "));
									if($company_detaill["state"]==$state['name']){ $state_sel="selected"; }else{ $state_sel=""; }
								}
								
								echo "<option value='$state[state_id]' $state_sel>$state[name]</option>";
							}
						?>
							<option value="999999">NRI</option>
						</select>
					</td>
					<th>District <b style="color:#ff0000;">*</b></th>
					<td id="dist_list">
						<select class="span2 pat_info" id="district" onkeyup="district_up(this,event)">
							<option value="0">Select</option>
						</select>
					</td>
					<th>City / Village <b style="color:#ff0000;">*</b></th>
					<td>
						<input type="text" class="capital pat_info" onkeyup="city_up(this,event)" id="city" value="<?php echo $pat_info_rel["city"]; ?>" list="city_datalist">
						<datalist id="city_datalist">
						<?php
							$city_datalist_qry=mysqli_query($link," SELECT DISTINCT `city` FROM `patient_info_rel` WHERE `city`!='' ORDER BY `city` ");
							while($city_datalist=mysqli_fetch_array($city_datalist_qry))
							{
								echo "<option value='$city_datalist[city]'></option>";
							}
						?>
						</datalist>
					</td>
				</tr>
				<tr>
					<th>Police Station</th>
					<td>
						<input type="text" class="span3 capital pat_info" id="police" onkeyup="police_up(this,event)" value="<?php echo $pat_info_rel["police"]; ?>" list="police_datalist">
						<datalist id="police_datalist">
						<?php
							$police_datalist_qry=mysqli_query($link," SELECT DISTINCT `police` FROM `patient_info_rel` WHERE `police`!='' ORDER BY `police` ");
							while($police_datalist=mysqli_fetch_array($police_datalist_qry))
							{
								echo "<option value='$police_datalist[police]'></option>";
							}
						?>
						</datalist>
					</td>
					<th>Post Office</th>
					<td>
						<input type="text" class="span2 capital pat_info" onkeyup="post_office_up(this,event)" id="post_office" value="<?php echo $pat_info_rel["post_office"]; ?>" list="post_office_datalist" />
						<datalist id="post_office_datalist">
						<?php
							$post_office_datalist_qry=mysqli_query($link," SELECT DISTINCT `post_office` FROM `patient_info_rel` WHERE `post_office`!='' ORDER BY `post_office` ");
							while($post_office_datalist=mysqli_fetch_array($post_office_datalist_qry))
							{
								echo "<option value='$post_office_datalist[post_office]'></option>";
							}
						?>
						</datalist>
					</td>
					<th>Pin Code</th>
					<td>
						<input type="text" class="numericc pat_info" onKeyup="pin_up(this,event)" id="pin" value="<?php echo $pat_info_rel["pin"]; ?>">
					</td>
				</tr>
				<tr id="address_tr" style="display:none;">
					<th>Full Address  <b style="color:#ff0000;">*</b></th>
					<td colspan="5">
						<textarea class="pat_info" id="address" onKeyup="address_up(this,event)" style="resize:none;width:98%;"><?php echo $pat_info["address"]; ?></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div class="doctor_info_div" style="<?php echo $edit_payment_style; ?>">
			<div id="load_patient_info_div" style="display:none;"></div>
			<table id="doctor_info_tbl" class="table table-condensed" style="background-color:#FFF">
				<tr>
					<th colspan="6" style="text-align:center;">
						<h4>Visiting Information</h4>
					</th>
				</tr>
				<tr>
					<th style="<?php echo $element_style; ?>">Branch <b style="color:#ff0000;">*</b></th>
					<td colspan="1" style="<?php echo $element_style; ?>">
						<select id="branch_id" class="span3 branch_id pat_info" onchange="branch_change(this,event)" onkeyup="branch_up(this,event)">
						<?php
							$qry=mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
							while($data=mysqli_fetch_array($qry))
							{
								if($pat_reg)
								{
									if($pat_reg["branch_id"]==$data["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
								}
								else
								{
									if($data["branch_id"]==$p_info["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
								}
								echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
							}
						?>
						</select>
					</td>
					<th>Center <b style="color:#ff0000;">*</b></th>
					<td colspan="<?php echo $center_col_span; ?>">
						<select id="center_no" class="<?php echo $center_span; ?> center_no pat_info" onchange="center_no_change(this,event)" onkeyup="center_no_up(this,event)">
							<option value="0">Select</option>
						</select>
					</td>
					<th style="display:none;">Patient Type</th>
					<td style="display:none;">
						<select class="" id="visit_type_id" onkeyup="visit_type_id(this.value,event)">
					<?php
						$visit_type_qry=mysqli_query($link, "SELECT `visit_type_id`, `visit_type_name` FROM `patient_visit_type_master` WHERE `p_type_id`='$p_type_id' ORDER BY `visit_type_id`");
						while($visit_type=mysqli_fetch_array($visit_type_qry))
						{
							if($pat_visit_type_det["visit_type_id"]==$visit_type["visit_type_id"]){ $visit_sel="selected"; }else{ $visit_sel=""; }
							echo "<option value='$visit_type[visit_type_id]' $visit_sel>$visit_type[visit_type_name]</option>";
						}
					?>
						</select>
					</td>
					<th style="display:none;">Health Agent <b style="color:#ff0000;">*</b></th>
					<td style="display:none;">
						<select class="span2 pat_info" id="hguide_id" onchange="hguide_change(this,event)" onKeyUp="hguide_up(this,event)">
					<?php
						$hguide_qry=mysqli_query($link, " SELECT `hguide_id`,`name` FROM `health_guide` WHERE `name`!='' AND `status`=0 ORDER BY `name` ASC ");
						while($hguide=mysqli_fetch_array($hguide_qry))
						{
							if($hguide["hguide_id"]==$hguide_id){ $hguide_sel="selected"; }else{ $hguide_sel=""; }
							echo "<option value='$hguide[hguide_id]' $hguide_sel>$hguide[name]</option>";
						}
					?>
						</select>
					</td>
				</tr>
				<tr>
					<th style="width: 14%;">Referred By <b style="color:#ff0000;">*</b></th>
					<td colspan="3">
						<input type="text" class="span3 pat_info" name="r_doc" id="r_doc" onFocus="ref_load_focus()" onKeyUp="ref_load_refdoc(this.value,event,'opd')" onBlur="javascript:$('#ref_doc').fadeOut(500)"  value="<?php echo $ref_doc_name["ref_name"]; ?>" /> <!-- style="width: 72%;" -->
						<input type="hidden" id="refbydoctorid" value="<?php echo $ref_doc_name["refbydoctorid"]; ?>">
						<!--<button class="btn btn-new btn-mini " name="new_doc" id="new_doc" value="New" onClick="load_new_ref_doc()"><i class="icon-edit"></i> New</button>-->
						<div id="doc_info"></div>
						<div id="ref_doc" align="center">
							<table style="background-color:#FFF;" class="table table-bordered table-condensed" id="center_table">
								<th>ID</th>
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
				<tr id="save_tr">
					<td colspan="7" style="text-align:center;">
					<button class="btn btn-save" id="pat_save_btn" onclick="pat_save()"><i class="icon-save"></i> <?php echo $btn_name; ?></button>
					<input type="hidden" class="span1" id="save_type" value="<?php echo $btn_name; ?>">
					<button class="btn btn-new" id="opd_new_reg_btn" onclick="new_registration()"><i class="icon-edit"></i> New Registration</button>
				</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<!-- Time -->
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
<script type="text/javascript" src="include/ui-1.10.0/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="include/jquery.ui.timepicker.js?v=0.3.3"></script>

<script src="../js/select2.min.js"></script>
<link rel="stylesheet" href="../css/select2.min.css" />

<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<input type="hidden" class="span1" id="p_type_id" value="<?php echo $p_type_id; ?>">
<input type="hidden" class="span1" id="patient_id" value="<?php echo $patient_id; ?>">
<input type="hidden" class="span1" id="opd_id" value="<?php echo $opd_id; ?>">
<input type="hidden" class="span1" id="pat_reg_type" value="0">

<!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"></button>
<input type="hidden" id="mod_chk" value="0"/>
<div id="myModal" class="modal fade" role="dialog" style="border-radius:0;display:none">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Select Bed</h4>
			</div>
			<div class="modal-body">
				<div id="result"> </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	
	$(document).ready(function(){
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		$('.timepicker').timepicker( {
			showAnim: 'blind'
		});
		
		$("#loader").hide();
		load_district();
		
		setTimeout(function(){
			
			if($("#patient_id").val()!="0")
			{
				cal_age_all('');
			}
			
			load_center();
			
			get_access_detail();
		},100);
		
		$("#pat_visit_type").select2({ theme: "classic" });
		$("#pat_visit_type").on("select2:close",function()
		{
			if($("#pat_visit_type").val()!="0")
			{
				var visit_type=$("#pat_visit_type").val();
				if(visit_type==9)
				{
					$(".police_detail").fadeIn(50);
					
					setTimeout(function()
					{
						$("#police_detail_station").focus();
					},100);
				}else
				{
					$(".police_detail").fadeOut(50);
					setTimeout(function()
					{
						$("#pat_save_btn").focus();
					},200);
				}
			}
		});
	});
	
	$(document).on('keyup', ".capital", function () {
		$(this).val(function (_, val) {
			return val.toUpperCase();
		});
	});
	
	$(document).on('keyup', ".numericc", function () {
		$(this).val(function (_, val) {
			var num=parseInt(val);
			return num;
		});
	});
	
	function load_center()
	{
		$.post("pages/new_service_registration_data.php",
		{
			type:"load_center",
			branch_id:$("#branch_id").val(),
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
		},
		function(data,status)
		{
			$("#center_no").html(data);
		})
	}
	function get_access_detail()
	{
		$.post("pages/new_service_registration_data.php",
		{
			type:"get_access_detail",
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			if($("#patient_id").val()!="0" || $("#opd_id").val()!="0")
			{
				var access=data.split("#");
				if(access[1]==0)
				{
					$(".pat_info").prop("disabled", true);
				}
				if(access[2]==0)
				{
					$(".doctor_info").prop("disabled", true);
				}
			}
		})
	}
	
	var emp_d=1;
	var emp_div=0;
	function load_emp(val,e,typ)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var eid=$("#e_id"+emp_d+"").val();
			eid=eid.split("@@");
			var tst=$("#testt").val();
			load_emp_details(eid[0],eid[1]);
		}
		else if(unicode==38)
		{
			var chk=emp_d-1;
			var cc=$("#row_id"+chk+"").html();
			if(cc)
			{
				$(".all_pat_row").css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				
				emp_d=emp_d-1;
				$("#row_id"+emp_d).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var emp_d1=emp_d+1;
				$("#row_id"+emp_d1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=emp_d%1;
				if(z2==0)
				{
					emp_div=emp_div-30;
					$("#pateint_list").scrollTop(emp_div)
					
				}
			}
		}
		else if(unicode==40)
		{
			var chk=emp_d+1;
			var cc=$("#row_id"+chk+"").html();
			if(cc)
			{
				$(".all_pat_row").css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				
				emp_d=emp_d+1;
				$("#row_id"+emp_d).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var emp_d1=emp_d-1;
				$("#row_id"+emp_d1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=emp_d%1;
				if(z2==0)
				{
					$("#pateint_list").scrollTop(emp_div)
					emp_div=emp_div+30;
				}
			}
		}
		else
		{
			if(val.length>0)
			{
				$.post("pages/new_service_registration_data.php",
				{
					val:val,
					type:"search_patients",
					typ:typ,
					p_type_id:$("#p_type_id").val(),
				},
				function(data,status)
				{
					$("#pateint_list").slideDown(400).html(data);
				})
			}else if(val.length==0)
			{
				$("#pateint_list").html("");
			}
		}
	}
	function load_emp_details(uhid,typ)
	{
		$("#patient_id").val(uhid);
		$("#pateint_list").slideUp(400);
		$(".patient_info_div").slideUp(500);
		
		$("#pat_reg_type").val("1");
		
		$("#refbydoctorid").val("0");
		
		$("#r_doc").val("").focus();
		$("#ad_doc").val("");
		
		scrollPage(180);
		
		load_patient_info();
		setTimeout(function(){
			load_payment_info();
		},50);
	}
	
	function load_patient_info()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_patient_info",
			patient_id:$("#patient_id").val(),
		},
		function(data,status)
		{
			$("#load_patient_info_div").html(data).show();
		})
	}
	
	function patient_type_change(dis,e)
	{
		$.post("pages/new_service_registration_data.php",
		{
			type:"load_centres",
			val:$(dis).val(),
		},
		function(data,status)
		{
			$("#sel_center").val(data);
		})
	}
	function patient_type_up(dis,e)
	{
		if(e.which==13)
		{
			$("#name_title").focus();
		}
	}
	
	function branch_change(dis,e)
	{
		load_center();
		$("#at_doc").val("");
		$("#ad_doc").val("");
		$("#addoc_id").val("0");
		$("#atdoc_id").val("0");
	}
	function branch_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="0")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#center_no").focus();
			}
		}
	}
	function center_no_change(dis,e)
	{
		//load_center();
		$("#at_doc").val("");
		$("#ad_doc").val("");
		$("#addoc_id").val("0");
		$("#atdoc_id").val("0");
	}
	function center_no_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="0")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#r_doc").focus();
			}
		}
	}
	
	function name_title_up(dis,e)
	{
		if(e.which==13)
		{
			$("#pat_name").focus();
		}
	}
	
	function pat_name_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#dob").focus();
			}
		}
	}
	
	function dob_up(dis,e)
	{
		$("#dob").css({"border-color":""});
		$("#age_y").css({"border-color":""});
		$("#age_m").css({"border-color":""});
		$("#age_d").css({"border-color":""});
		
		var val=$(dis).val();
		
		//alert(val[2]);
		
		//~ var txt2 = val.slice(0, 2) + "-" + val.slice(2);
		//~ alert(txt2);
		
		var len=val.length;
		if(len<=11)
		{
			if(len==2 || len==5)
			{
				$("#dob").val(val+"-");
			}
			if(len>9)
			{
				//~ cal_age(e);
				cal_age_all(e);
			}
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				if(len>=10)
				{
					if($("#age_y").val()=="" || $("#age_m").val()=="" || $("#age_d").val()=="")
					{
						$("#age_y").focus();
					}
					else
					{
						$("#sex").focus();
					}
				}else
				{
					$("#age_y").focus();
				}
			}
			var n=val.length;
			var numex=/^[0-9-]+$/;
			if(val[n-1].match(numex))
			{
				
			}
			else
			{
				val=val.slice(0,n-1);
				$("#dob").val(val);
			}
		}
	}
	function sex_up(dis,e)
	{
		if(e.which==13)
		{
			$("#phone").focus();
		}
	}
	function phone_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else if($(dis).val().length!=10)
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#marital_status").focus();
			}
		}
	}
	function marital_status_up(dis,e)
	{
		if(e.which==13)
		{
			//~ $("#father_name").focus();
			$("#state").focus();
		}
	}
	function email_up(dis,e)
	{
		if(e.which==13)
		{
			//$("#").focus();
		}
	}
	function father_name_up(dis,e)
	{
		if(e.which==13)
		{
			$("#mother_name").focus();
		}
	}
	function mother_name_up(dis,e)
	{
		if(e.which==13)
		{
			$("#gd_name").focus();
		}
	}
	function gd_name_up(dis,e)
	{
		//~ if(e.which==13)
		//~ {
			//~ $("#g_relation").focus();
		//~ }
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#g_relation").focus();
			}
		}
	}
	function g_relation_up(dis,e)
	{
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#gd_phone").focus();
			}
		}
	}
	function gd_phone_up(dis,e)
	{
		//~ if(e.which==13)
		//~ {
			//~ $("#income_id").focus();
		//~ }
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else if($(dis).val().length!=10)
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#income_id").focus();
			}
		}
	}
	function income_up(dis,e)
	{
		if(e.which==13)
		{
			$("#state").focus();
		}
	}
	function state_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if($(dis).val()=="")
		{
			$(dis).css({"border-color":"red"});
		}
		else
		{
			if(e.which==13)
			{
				if($(dis).val()=="999999")
				{
					$("#address").focus();
					scrollPage(350);
				}
				else if($(dis).val()=="0")
				{
					$(dis).css({"border-color":"red"});
				}
				else
				{
					$("#district").focus();
				}
			}
		}
	}
	function state_change()
	{
		if($("#state").val()=="999999")
		{
			$("#address_tr").fadeIn(200);
		}
		else
		{
			$("#address_tr").fadeOut(200);
		}
		load_district();
	}
	function load_district()
	{
		$.post("pages/new_service_registration_data.php",
		{
			type:"load_district",
			state:$("#state").val(),
		},
		function(data,status)
		{
			$("#district").html(data);
		})
	}
	function district_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if($(dis).val()=="")
		{
			$(dis).css({"border-color":"red"});
		}
		else
		{
			if(e.which==13)
			{
				if($(dis).val()=="0")
				{
					$(dis).css({"border-color":"red"});
				}
				else
				{
					$("#city").focus();
				}
			}
		}
	}
	function city_up(dis,e)
	{
		//~ if(e.which==13)
		//~ {
			//~ $("#police").focus();
		//~ }
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#police").focus();
			}
		}
	}
	function police_up(dis,e)
	{
		if(e.which==13)
		{
			$("#post_office").focus();
		}
	}
	function post_office_up(dis,e)
	{
		if(e.which==13)
		{
			$("#pin").focus();
		}
	}
	function pin_up(dis,e)
	{
		scrollPage(50);
		if(e.which==13)
		{
			if($("#branch_id").is(":visible"))
			{
				$("#branch_id").focus();
			}
			else if($("#center_no").is(":visible"))
			{
				$("#center_no").focus();
			}
			else
			{
				$("#r_doc").focus();
			}
			scrollPage(520);
		}
	}
	function address_up(dis,e)
	{
		scrollPage(250);
		if(e.which==13)
		{
			e.preventDefault();
			setTimeout(function(){
				$("#r_doc").focus();
				scrollPage(650);
			},100);
		}
	}
	
	function visit_type_change(val)
	{
		if(val==3)
		{
			$("#visit_fee").val("0");
			$("#regd_fee").val("0");
			$("#total").val("0");
			$("#opd_now_pay").val("0");
		}
		else
		{
			var visit_fee=parseInt($("#doc_visit_fee_master").val());
			if(!visit_fee){ visit_fee=0; }
			
			var regd_fee=parseInt($("#doc_regd_fee_master").val());
			if(!regd_fee){ regd_fee=0; }
			
			var total=parseInt(visit_fee)+parseInt(regd_fee);
			
			$("#visit_fee").val(visit_fee);
			$("#regd_fee").val(regd_fee);
			$("#total").val(total);
			$("#opd_now_pay").val(total);
		}
		load_payment(0);
	}
	function dept_change(dis,e)
	{
		$("#at_doc").val("");
		$("#ad_doc").val("");
		$("#addoc_id").val("0");
		$("#atdoc_id").val("0");
	}
	function dept_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="0")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#ad_doc").focus();
			}
		}
	}
	
	
	// Refer Doctor Start
	function ref_load_focus()
	{
		$("#ref_doc").fadeIn(500);
		$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
	}
	var doc_tr=1;
	var doc_sc=0;
	function ref_load_refdoc(val,e,typ)
	{
		$("#r_doc").css({"border-color":""});
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#ref_doc").fadeIn(500);
				$.post("pages/new_service_registration_data.php",
				{
					val:val,
					type:"load_ref_doctor",
				},
				function(data,status)
				{
					$("#ref_doc").html(data);
					doc_tr=1;
					doc_sc=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=doc_tr+1;
				var cc=document.getElementById("doc"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr+1;
					$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr-1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						$("#ref_doc").scrollTop(doc_sc)
						doc_sc=doc_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=doc_tr-1;
				var cc=document.getElementById("doc"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr-1;
					$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr+1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						doc_sc=doc_sc-30;
						$("#ref_doc").scrollTop(doc_sc)
					}
				}
			}
		}
		else
		{
			var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
			var doc_id=docs[1].trim();
			var doc_naam=docs[2].trim();
			//$("#r_doc").val(doc_naam+"-"+doc_id);
			var d_in=docs[3];
			//$("#doc_mark").val(docs[5]);
			$("#doc_info").html(d_in);
			$("#doc_info").fadeIn(500);
			
			doc_load(doc_id,doc_naam);
		}
	}
	function doc_load(doc_id,name)
	{
		$("#refbydoctorid").val(doc_id);
		$("#r_doc").val(name);
		$("#doc_info").html("");
		$("#ref_doc").fadeOut(500);
		$("#pat_save_btn").focus();
	}
	// Refer Doctor End
	
	// Save Patient Start
	function pat_save()
	{
		if($("#pat_name").val()=="" && $("#patient_id").val()=="0")
		{
			scrollPage(230);
			$("#pat_name").focus().css({"border-color":"red"});
			return false;
		}
		if($("#dob").val()=="" && $("#patient_id").val()=="0")
		{
			scrollPage(230);
			$("#dob").focus().css({"border-color":"red"});;
			return false;
		}
		if(($("#phone").val()=="" && $("#patient_id").val()=="0") || ($("#phone").val().length!=10 && $("#patient_id").val()=="0"))
		{
			scrollPage(230);
			$("#phone").focus().css({"border-color":"red"});;
			return false;
		}
		/*if($("#gd_name").val()=="" && $("#patient_id").val()=="0")
		{
			scrollPage(230);
			$("#gd_name").focus().css({"border-color":"red"});;
			return false;
		}
		if($("#g_relation").val()=="" && $("#patient_id").val()=="0")
		{
			scrollPage(230);
			$("#g_relation").focus().css({"border-color":"red"});;
			return false;
		}
		if(($("#gd_phone").val()=="" && $("#patient_id").val()=="0") || ($("#gd_phone").val().length!=10 && $("#patient_id").val()=="0"))
		{
			scrollPage(230);
			$("#gd_phone").focus().css({"border-color":"red"});;
			return false;
		}*/
		if($("#state").val()=="0" && $("#patient_id").val()=="0")
		{
			scrollPage(230);
			$("#state").focus().css({"border-color":"red"});;
			return false;
		}
		if($("#district").val()=="0" && $("#patient_id").val()=="0")
		{
			scrollPage(230);
			$("#district").focus().css({"border-color":"red"});;
			return false;
		}
		if($("#city").val()=="" && $("#patient_id").val()=="0")
		{
			scrollPage(230);
			$("#city").focus().css({"border-color":"red"});;
			return false;
		}
		
		if($(".patient_info_div").is(":visible"))
		{
			var scroll_val=550;
		}
		else
		{
			var scroll_val=150;
		}
		
		// Doctor Part
		if($("#refbydoctorid").val()=="0" || $("#refbydoctorid").val()=="")
		{
			scrollPage(scroll_val)
			$("#r_doc").val("").focus().css({"border-color":"red"});;
			return false;
		}
		if($("#hguide_id").val()=="0")
		{
			scrollPage(scroll_val)
			$("#hguide_id").focus().css({"border-color":"red"});;
			return false;
		}
		
		$("#loader").show();
		$("#save_tr").hide();
		
		bootbox.dialog({ message: "<b>Saving </b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		
		$.post("pages/new_service_registration_data.php",
		{
			type:"pat_save",
			branch_id:$("#branch_id").val(),
			center_no:$("#center_no").val(),
			save_type:$("#save_type").val(),
			p_type_id:$("#p_type_id").val(),
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			pat_reg_type:$("#pat_reg_type").val(),
			//patient_type:$("#patient_type").val(),
			name_title:$("#name_title").val(),
			pat_name:$("#pat_name").val(),
			dob:$("#dob").val(),
			sex:$("#sex").val(),
			phone:$("#phone").val(),
			marital_status:$("#marital_status").val(),
			email:$("#email").val(),
			father_name:$("#father_name").val(),
			mother_name:$("#mother_name").val(),
			gd_name:$("#gd_name").val(),
			g_relation:$("#g_relation").val(),
			gd_phone:$("#gd_phone").val(),
			income_id:$("#income_id").val(),
			state:$("#state").val(),
			district:$("#district").val(),
			city:$("#city").val(),
			police:$("#police").val(),
			post_office:$("#post_office").val(),
			pin:$("#pin").val(),
			address:$("#address").val(),
			refbydoctorid:$("#refbydoctorid").val(),
			hguide_id:$("#hguide_id").val(),
			visit_type_id:$("#visit_type_id").val(),
			
			user:$("#user").text().trim(),
			
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			$("#save_tr").show();
			
			//130@114/0920@#1#1#1#1#@Saved
			var res=data.split("@");
			$("#patient_id").val(res[0]);
			$("#opd_id").val(res[1]);
			
			bootbox.hideAll();
			bootbox.dialog({ message: "<h5>"+res[3]+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
				if(res[1]!="0")
				{
					window.location="processing.php?param=816&uhid="+res[0]+"&ipd="+res[1];
				}
			},2000);
		})
	}
	// Save Patient End
	
	
	function new_registration()
	{
		var param_id=$("#param_id").val();
		
		window.location.href="?param="+btoa(param_id);
	}
	
	function scrollPage(val)
	{
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:val}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	}
	
	
	function load_new_ref_doc()
	{
		$.post("pages/ref_doc_new.php",	{ 	},
		function(data,status)
		{
			$("#mod").click();
			$("#check_modal").val("1");
			$("#results").html(data);
			$("#results").slideDown(500,function(){ $("#ref_doc_new").fadeIn(200);});
			
			setTimeout(function(){
				$("#doc_name").focus();
			},1000);
			
		})
	}
	function save_new_doc()
	{
		if($("#doc_name").val()!="")
		{
			$.post("pages/ref_doc_new_save.php",
			{
				name:$("#doc_name").val(),
				qual:$("#doc_qual").val(),
				add:$("#doc_add").val(),
				phone:$("#doc_phone").val(),
				email:$("#doc_email").val(),
			},
			function(data,status)
			{
				var res=data.split("-");
				$("#r_doc").val(res[0]);
				$("#refbydoctorid").val(res[1]);
				//$("#ref_doc_new").fadeOut(200);
				$("#check_modal").val("0");
				$("#mod").click();
			})
		}else
		{
			$("#doc_name").focus();
		}
	}
</script>

<style>
label {
	display: inline;
}
#myModal
{
	left: 23%;
	width:95%;
	height: 500px;
}
.modal.fade.in {
    top: 1%;
}
.modal-body
{
	max-height: 350px;
}
</style>
