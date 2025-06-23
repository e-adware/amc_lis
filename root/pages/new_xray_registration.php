<?php
$c_user=$_SESSION["emp_id"];

$branch_str=" AND `branch_id`='$p_info[branch_id]'";
$element_style="display:none";
$center_span="";
if($p_info["levelid"]==1)
{
	$branch_str="";
	//$element_style="";
	//$center_span="";
}

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$p_type_id=11; // XRAY
$category_id=2; // 0=all,1=patho,2=radio,3=cardio
$dept_id=40; // 0=all

$p_type_master=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));

$uhid=base64_decode($_GET["uhid"]);
$uhid=trim($uhid);

$pin=base64_decode($_GET["opd"]);
$pin=trim($pin);

if(!$uhid){ $uhid=0; }
if(!$pin){ $pin=0; }

$btn_name="Save";
if($uhid==0)
{
	$btn_name="Update";
}


$patient_id=$uhid;
$opd_id=$pin;

$test_readonly="";
if($opd_id!=0)
{
	if($emp_access["edit_payment"]==0)
	{
		//$test_readonly="readonly";
	}
}

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
$pat_info_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));

$pat_name_str=explode(". ",$pat_info["name"]);
$pat_name_title=trim($pat_name_str[0]);
$pat_name=trim($pat_name_str[1]." ".$pat_name_str[2]." ".$pat_name_str[3]." ".$pat_name_str[4]);

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `refbydoctorid`, `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));


$pat_appointment=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$current_sesssion=$pat_appointment["doctor_session"];

$con_doc_info=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$pat_appointment[consultantdoctorid]' "));

$pat_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$tot_amount=$pat_pay_det["tot_amount"];
$visit_fee=$pat_pay_det["visit_fee"];
$regd_fee=$pat_pay_det["regd_fee"];
$emergency_fee=$pat_pay_det["emergency_fee"];

$hguide_id=101;
if($pat_reg)
{
	$hguide_id=$pat_reg["hguide_id"];
}
//$health_guide=mysqli_fetch_array(mysqli_query($link, " SELECT `hguide_id`, `name` FROM `health_guide` WHERE `hguide_id`='$hguide_id' "));

$item_num=mysqli_num_rows(mysqli_query($link, "SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'"));
$item_num=$item_num+1;

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
		<?php if(!$str){ ?>
		<span style="float:right;">
			<button class="btn btn-search" id="token_list" onclick="estimate_receipt()"><i class="icon-search"></i> Estimate Receipt</button>
		</span>
		<?php } ?>
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
						<input type="text" class="capital pat_info" id="pat_name" onKeyup="pat_name_up(this,event)" value="<?php echo $pat_name; ?>" style="width: 180px;">
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
				<tr>
					<th>Father's Name</th>
					<td>
						<input type="text" class="span3 capital pat_info" id="father_name" onKeyup="father_name_up(this,event)" value="<?php echo $pat_info_rel["father_name"]; ?>">
					</td>
					<th>Mother's Name</th>
					<td>
						<input type="text" class="span2 capital pat_info" id="mother_name" onKeyup="mother_name_up(this,event)" value="<?php echo $pat_info_rel["mother_name"]; ?>">
					</td>
					<th>Guardian's Name</th>
					<td>
						<input type="text" class="capital pat_info" id="gd_name" onKeyup="gd_name_up(this,event)" value="<?php echo $pat_info["gd_name"]; ?>">
					</td>
				</tr>
				<tr>
					<th style="display:;">Relation with Guardian</th>
					<td style="display:;">
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
					<th>Guardian's Contact</th>
					<td>
						<input type="text" class="span2 numericc pat_info" id="gd_phone" maxlength="10" onkeyup="gd_phone_up(this,event)" value="<?php echo $pat_info_rel["gd_phone"]; ?>">
					</td>
					<th>Income Group</th>
					<td>
						<select id="income_id" class="span pat_info" onkeyup="income_up(this,event)">
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
		<div class="doctor_info_div" id="test_sel" style="<?php echo $edit_payment_style; ?>">
			<div id="list_all_test" class="up_div"></div>
			<div id="msg" align="center"></div>
			<div id="load_patient_info_div" style="display:none;"></div>
			<table id="doctor_info_tbl" class="table table-condensed" style="background-color:#FFF">
				<tr>
					<th colspan="6" style="text-align:center;">
						<h4>Investigation Information</h4>
					</th>
				</tr>
				<tr>
					<th style="width: 14%;">Referred By <b style="color:#ff0000;">*</b></th>
					<td colspan="1">
						<input type="text" class="" name="r_doc" id="r_doc" onFocus="ref_load_focus()" onKeyUp="ref_load_refdoc(this.value,event,'opd')" onBlur="javascript:$('#ref_doc').fadeOut(500)" style="width: 72%;" value="<?php echo $ref_doc_name["ref_name"]; ?>" />
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
					
					<th style="<?php echo $element_style; ?>">Branch <b style="color:#ff0000;">*</b></th>
					<td colspan="1" style="<?php echo $element_style; ?>">
						<select id="branch_id" class="span2 branch_id" onchange="branch_change(this,event)" onkeyup="branch_up(this,event)">
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
					<td></td>
					<td></td>
					<th>Center <b style="color:#ff0000;">*</b></th>
					<td colspan="1">
						<select id="center_no" class="<?php echo $center_span; ?> center_no" onchange="center_no_change_new(this,event)" onkeyup="center_no_up(this,event)">
							<option value="0">Select</option>
						</select>
					</td>
				</tr>
				<tr>
					<th style="display:none;">Health Agent <b style="color:#ff0000;">*</b></th>
					<td style="display:none;">
						<select class="span2 doctor_info" id="hguide_id" onchange="hguide_change(this,event)" onKeyUp="hguide_up(this,event)">
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
					<th style="display:none;">Select Category <b style="color:#ff0000;">*</b></th>
					<td style="display:none;">
						<select class="span2 doctor_info" id="category_id" onchange="category_change(this,event)" onKeyUp="category_up(this,event)">
					<?php
						$cat_qry=mysqli_query($link, " SELECT `category_id`, `name` FROM `test_category_master` WHERE `name`!='' ORDER BY `category_id` ASC ");
						while($cat=mysqli_fetch_array($cat_qry))
						{
							if($cat["category_id"]==$category_id){ $cat_sel="selected"; }else{ $cat_sel=""; }
							echo "<option value='$cat[category_id]' $cat_sel>$cat[name]</option>";
						}
					?>
						</select>
					</td>
					<th style="display:none;">Select Department <b style="color:#ff0000;">*</b></th>
					<td style="display:none;">
						<select class="span2 doctor_info" id="dept_id" onchange="dept_change(this,event)" onKeyUp="dept_up(this,event)">
							<option value="0">Select</option>
					<?php
						$dept_qry=mysqli_query($link, " SELECT `id`, `name` FROM `test_department` WHERE `name`!='' ORDER BY `name` ASC ");
						while($dept=mysqli_fetch_array($dept_qry))
						{
							if($dept_id==$dept["id"]){ $dept_sel="selected"; }else{ $dept_sel=""; }
							echo "<option value='$dept[id]' $dept_sel>$dept[name]</option>";
						}
					?>
						</select>
					</td>
				</tr>
				<tr>
					<th class="span3">Select Test <b style="color:#ff0000;">*</b></th>
					<td colspan="3">
						<input type="text" class="doctor_info" name="test" id="test" onKeyUp="select_test_new(this.value,event)" style="width:80%;" placeholder="Search Test Name Here" <?php echo $test_readonly; ?> />
						<button class="btn btn-search btn-mini" id="test_view_btn" onClick="show_selected_test()" style="margin-bottom: 1%;display:none;"><i class="icon-eye-open"></i> Test</button>
						<input type="hidden" id="test_ids" value="">
						<input type="hidden" name="tr_counter" id="tr_counter" class="form-control" value="<?php echo $item_num; ?>"/>
					</td>
					<th>Total</th>
					<td>
						<input type="text" id="total" value="<?php echo $tot_amount; ?>" readonly>
					</td>
				</tr>
				<tr>
					<td colspan="6">
						<div id="test_d">
							
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="payment_info_div" style="<?php echo $edit_payment_style; ?>">
			<table id="payment_info_tbl" class="table table-condensed" style="background-color:#FFF">
				<tr>
					<th colspan="6" style="text-align:center;">
						<h4>Payment Information</h4>
					</th>
				</tr>
				<tr>
					<td colspan="6">
						<div id="payment_info_div"></div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<input type="hidden" class="span1" id="p_type_id" value="<?php echo $p_type_id; ?>">
<input type="hidden" class="span1" id="patient_id" value="<?php echo $patient_id; ?>">
<input type="hidden" class="span1" id="opd_id" value="<?php echo $opd_id; ?>">
<input type="hidden" class="span1" id="pat_reg_type" value="0">

<input type="hidden" class="span1" id="credit_limit" value="0">
<input type="hidden" class="span1" id="c_discount" value="0">
<input type="hidden" class="span1" id="allow_credit" value="0">
<input type="hidden" class="span1" id="insurance" value="0">

<input type="button" data-toggle="modal" data-target="#myModal1" id="mod" style="display:none"/>
<input type="hidden" id="mod_chk" value="0"/>
<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>

<input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none"/>
<input type="hidden" id="mod_chk2" value="0"/>
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results2"> </div>
			</div>
		</div>
	</div>
</div>

<script>
	
	$(document).ready(function(){
		$("#loader").hide();
		load_district();
		
		setTimeout(function(){
			
			if($("#patient_id").val()!=0)
			{
				cal_age_all('');
			}
			
			load_center();
			
			get_access_detail();
			
			var item_chk=$("#test_list tr").length;
			if(item_chk>0)
			{
				$("#test_view_btn").show();
			}
			
		},100);
		setTimeout(function(){
			center_no_change('','');
		},300);
		setTimeout(function(){
			if($("#patient_id").val()!=0 && $("#opd_id").val()!=0)
			{
				load_saved_test_list();
			}
		},500);
		setTimeout(function(){
			load_payment_info();
		},700);
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
	$(document).on('keyup', ".numericfloat", function () {
		$(this).val(function (_, val)
		{
			if(val==0)
			{
				return val;
			}
			else if(val==".")
			{
				return "0.";
			}
			else
			{
				var n=val.length;
				var numex=/^[0-9.]+$/;
				if(val[n-1].match(numex))
				{
					return number;
				}
				else
				{
					val=val.slice(0,n-1);
					return val;
				}
			}
		});
	});
	
	function load_center()
	{
		$.post("pages/new_lab_registration_data.php",
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
		$.post("pages/new_lab_registration_data.php",
		{
			type:"get_access_detail",
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			var access=data.split("#");
			if($("#patient_id").val()!="0" || $("#opd_id").val()!="0")
			{
				if(access[1]==0)
				{
					$(".pat_info").prop("disabled", true);
				}
				if(access[2]==0)
				{
					$(".doctor_info").prop("disabled", true);
					$(".remove_btn_cls").hide();
				}
			}
			if(access[4]==0)
			{
				$("#opd_now_discount_per").prop("disabled", true);
				$("#opd_now_discount_amount").prop("disabled", true);
			}
			else
			{
				$("#opd_now_discount_per").prop("disabled", false);
				$("#opd_now_discount_amount").prop("disabled", false);
			}
			load_center_facility();
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
				$.post("pages/new_lab_registration_data.php",
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
	function load_payment_info()
	{
		$("#loader").show();
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_payment_info",
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			p_type_id:$("#p_type_id").val(),
		},
		function(data,status)
		{
			//$("#payment_info_div").html(data);
			
			$("#advance_paid_div").html("").slideUp(400);
			$("#payment_info_div").html(data).slideDown(900);
			
			if($("#opd_id").val()!="0")
			{
				setTimeout(function(){
					scrollPage(850);
				},100);
			}
			setTimeout(function(){
				$("#loader").hide();
				if($("#opd_id").val()!="0")
				{
					$("#opd_now_discount_per").val($("#discount_per_master").val()).prop("disabled", true);
					$("#opd_now_discount_amount").val($("#discount_amount_master").val()).prop("disabled", true);
					
					$("#print_con_receipt_btn").focus();
				}
			},2100);
		})
	}
	
	function patient_type_change(dis,e)
	{
		$.post("pages/new_lab_registration_data.php",
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
		$("#ad_doc").val("");
		$("#consultantdoctorid").val("0");
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
	function center_no_change_new(dis,e)
	{
		search_test();
		
		setTimeout(function(){
			load_test_list();
		},100);
		
		setTimeout(function(){
			center_no_change('','');
		},500);
	}
	function center_no_change(dis,e)
	{
		$("#loader").show();
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_center_facility",
			center_no:$("#center_no").val(),
			p_type_id:$("#p_type_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			var res=data.split("@");
			
			$("#credit_limit").val(res[0]);
			$("#c_discount").val(res[1]);
			$("#allow_credit").val(res[2]);
			$("#insurance").val(res[3]);
			
			setTimeout(function(){
				load_center_facility();
			},700);
		})
	}
	
	function load_center_facility()
	{
		if($("#opd_id").val()=="0")
		{
			$("#opd_now_discount_per").val($("#c_discount").val());
			
			if($("#c_discount").val()>0)
			{
				$("#opd_now_discount_reason").val("Centre discount");
				$(".discount_each").val("0").prop("readonly", true);
			}
			else
			{
				$("#opd_now_discount_reason").val("");
				
				if($("#discount_permission").val()==1)
				{
					$(".discount_each").prop("readonly", false);
				}
			}
			setTimeout(function(){
				opd_discount_per('');
			},100);
		}
		else
		{
			setTimeout(function(){
				//opd_discount_per('');
				opd_discount_per_old();
			},100);
			if($("#edit_payment").val()==0)
			{
				$(".test_rate").prop("readonly", true);
				$(".discount_each").prop("readonly", true);
			}
		}
		
		if($("#allow_credit").val()==0)
		{
			$("#opd_now_pay").prop("readonly", true);
			$("#opd_now_payment_mode option[value='Credit']").prop('disabled',true);
		}
		else
		{
			$("#opd_now_pay").prop("readonly", false);
			$("#opd_now_payment_mode option[value='Credit']").prop('disabled',false);
		}
		
		if($("#insurance").val()>0)
		{
			$("#opd_now_discount_per").val("0").prop("disabled", true);
			$("#opd_now_discount_amount").val("0").prop("disabled", true);
			$("#opd_now_pay").val("0").prop("readonly", true);
			
			$("#opd_now_payment_mode").val("Credit").prop("disabled", true);
			
			$("#now_balance_reason").val("Insurance");
			
			$(".discount_each").val("0").prop("readonly", true);
			
			//$("#opd_now_discount_reason").attr("type","hidden");
		}
		else
		{
			if($("#discount_permission").val()==1)
			{
				if($("#opd_id").val()=="0")
				{
					$("#opd_now_discount_per").prop("disabled", false);
					$("#opd_now_discount_amount").prop("disabled", false);
					
					$(".discount_each").val("0").prop("readonly", true);
				}
			}
			
			if($("#allow_credit").val()==0)
			{
				$("#opd_now_pay").prop("readonly", true);
				$("#opd_now_payment_mode option[value='Credit']").prop('disabled',true);
			}
			
			$("#opd_now_payment_mode").val("Cash").prop("disabled", false);
			
			$("#now_balance_reason").val("");
			
			//get_access_detail();
		}
		calculate_total("2");
	}
	
	function center_no_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			$("#test").focus();
			
			if($("#patient_id").val()==0)
			{
				scrollPage(380);
			}
			else
			{
				if($("#opd_id").val()==0)
				{
					scrollPage(40);
				}
				else
				{
					scrollPage(330);
				}
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
			$("#father_name").focus();
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
		if(e.which==13)
		{
			$("#g_relation").focus();
		}
		$(dis).css({"border-color":""});
		//~ if(e.which==13)
		//~ {
			//~ if($(dis).val()=="")
			//~ {
				//~ $(dis).css({"border-color":"red"});
			//~ }
			//~ else
			//~ {
				//~ $("#g_relation").focus();
			//~ }
		//~ }
	}
	function g_relation_up(dis,e)
	{
		if(e.which==13)
		{
			$("#gd_phone").focus();
			//~ if($(dis).val()=="")
			//~ {
				//~ $(dis).css({"border-color":"red"});
			//~ }
			//~ else
			//~ {
				//~ $("#gd_phone").focus();
			//~ }
		}
	}
	function gd_phone_up(dis,e)
	{
		if(e.which==13)
		{
			$("#income_id").focus();
		}
		$(dis).css({"border-color":""});
		//~ if(e.which==13)
		//~ {
			//~ if($(dis).val()=="")
			//~ {
				//~ $(dis).css({"border-color":"red"});
			//~ }
			//~ else if($(dis).val().length!=10)
			//~ {
				//~ $(dis).css({"border-color":"red"});
			//~ }
			//~ else
			//~ {
				//~ $("#income_id").focus();
			//~ }
		//~ }
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
		$.post("pages/new_lab_registration_data.php",
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
			$("#r_doc").focus();
			
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
	
	function dept_change(dis,e)
	{
		$("#ad_doc").val("");
		$("#consultantdoctorid").val("0");
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
				$.post("pages/new_lab_registration_data.php",
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
		//$("#dept_id").focus();
		
		if($("#branch_id").is(":visible"))
		{
			$("#branch_id").focus();
		}
		else if($("#center_no").is(":visible"))
		{
			$("#center_no").focus();
		}
		
	}
	// Refer Doctor End
	
	
	// Test Search Start
	function test_enable()
	{
		setTimeout(function(){ $("#chk_val").val(1)},500);	
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:110}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	}
	var t_val=1;
	var t_val_scroll=0;

	var _changeInterval = null;
	function select_test_new(val,e)
	{
		if($("#patient_id").val()==0)
		{
			scrollPage(380);
		}
		else
		{
			if($("#opd_id").val()==0)
			{
				scrollPage(40);
			}
			else
			{
				scrollPage(330);
			}
		}
		
		clearInterval(_changeInterval)
		_changeInterval = setInterval(function() {
			// Typing finished, now you can Do whatever after 2 sec
			clearInterval(_changeInterval);
			select_test_new_res(val,e);
		}, 100);
	}
	function select_test_new_res(val,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var tst=document.getElementsByClassName("test"+t_val);
			load_test_new(''+tst[1].value.trim()+'',''+tst[2].innerHTML.trim()+'',''+tst[3].innerHTML.trim()+'');
			$("#list_all_test").slideDown(400);
			$("#test").val("").focus();
		}
		else if(unicode==40)
		{
			var chk=t_val+1;
			var cc=document.getElementById("td"+chk).innerHTML;
			if(cc)
			{
				t_val=t_val+1;
				$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var t_val1=t_val-1;
				$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=t_val%1;
				if(z2==0)
				{
					$("#test_d").scrollTop(t_val_scroll)
					t_val_scroll=t_val_scroll+30;
				}
			}	
		}
		else if(unicode==38)
		{
			var chk=t_val-1;
			var cc=document.getElementById("td"+chk).innerHTML;
			if(cc)
			{
				t_val=t_val-1;
				$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var t_val1=t_val+1;
				$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=t_val%1;
				if(z2==0)
				{
					t_val_scroll=t_val_scroll-30;
					$("#test_d").scrollTop(t_val_scroll)
					
				}
			}	
		}
		else if(unicode==27)
		{
			setTimeout(function(){
				
				$("#list_all_test").slideUp(500)
				$("#test_d").html("");
				
				if($("#opd_now_discount_per").is('[readonly]'))
				{
					if($("#opd_now_pay").is('[readonly]'))
					{
						if($("#opd_now_payment_mode").is(":disabled"))
						{
							$("#pat_save_btn").focus();
						}
						else
						{
							$("#opd_now_payment_mode").focus();
						}
					}
					else
					{
						$("#opd_now_pay").focus();
					}
				}
				else
				{
					$("#opd_now_discount_per").focus();
				}
			},100);
		}
		else
		{
			search_test();
		}
	}
	
	function search_test()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"search_test",
			test:$("#test").val(),
			patient_id:$("#patient_id").text().trim(),
			opd_id:$("#opd_id").text().trim(),
			center_no:$("#center_no").val(),
			category_id:$("#category_id").val(),
			dept_id:$("#dept_id").val(),
		},
		function(data,status)
		{
			$("#test_d").html(data);
			t_val=1;
			t_val_scroll=0;
			$("#test_d").scrollTop(t_val_scroll)
		})
	}
	
	function load_test_click(id,name,rate)
	{
		load_test_new(id,name,rate);
		$("#list_all_test").slideDown(400);
	}
	
	
	function load_test_new(id,name,rate)
	{
		//alert(id+" "+name+" "+rate);
		var item_chk=$("#test_list tr").length;
		if(!item_chk){ item_chk=0; }
		
		if(item_chk==0)
		{
			load_table(id,name,rate);
		}
		else
		{
			load_items(id,name,rate);
		}
	}
	function load_table(id,name,rate)
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_item_table",
		},
		function(data,status)
		{
			$("#list_all_test").html(data);
			load_items(id,name,rate);
		})
	}
	
	function load_items(id,name,rate)
	{
		var each_row=$(".each_row");
		for(var i=0;i<each_row.length;i++)
		{
			var tr_counter=each_row[i].value;
			
			var testid=$("#testid"+tr_counter).val();
			
			if(testid==id)
			{
				$("#test_sel").css({'opacity':'0.5'});
				$("#msg").html("<span style='color:red;font-weight:bold;'>Already Selected</span>");
				var x=$("#test_sel").offset();
				var w=$("#msg").width()/2;
				$("#msg").css({'top':x.top,'left':'50%','margin-left':-w+'px'});
				$("#msg").fadeIn(500);
				setTimeout(function(){$("#msg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'}); })},2000);
				return false;
			}
		}
		
		var tr_counter=$("#tr_counter").val().trim();
		
		$.post("pages/new_lab_registration_data.php",
		{
			type:"add_items",
			testid:id,
			test_name:name,
			test_rate:rate,
			tr_counter:tr_counter,
			c_discount:$("#c_discount").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#item_footer").before(data);
			
			var next_tr_counter=parseInt($("#tr_counter").val())+1;
			$("#tr_counter").val(next_tr_counter);
			
			$("#list_all_test").animate({ scrollTop: 2900 });
			
			center_no_change('','');
			
			setTimeout(function(){
				//calculate_total("2");
				$("#test").focus();
			},100);
		})
	}
	function load_test_list()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_test_list",
			p_type_id:$("#p_type_id").val(),
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			center_no:$("#center_no").val(),
			test_ids:$("#test_ids").val(),
			c_discount:$("#c_discount").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#list_all_test").html(data);
			
			$("#list_all_test").animate({ scrollTop: 2900 });
			
			setTimeout(function(){
				calculate_total("2");
				$("#test").focus();
			},100);
		})
	}
	function load_saved_test_list()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_saved_test_list",
			p_type_id:$("#p_type_id").val(),
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			center_no:$("#center_no").val(),
			test_ids:$("#test_ids").val(),
			c_discount:$("#c_discount").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#list_all_test").html(data);
			
			$("#list_all_test").animate({ scrollTop: 2900 });
			$("#test_view_btn").show();
			
			setTimeout(function(){
				calculate_total("2");
				$("#test").focus();
			},100);
		})
	}
	
	// Test Search End
	
	// Payment Start
	function test_rate_each()
	{
		calculate_total("1");
		setTimeout(function(){
			if($("#c_discount").val()>0)
			{
				opd_discount_per_each();
			}
			else
			{
				opd_discount_amount_each();
			}
		},50);
	}
	function discount_each()
	{
		calculate_total("1");
		setTimeout(function(){
			opd_discount_amount_each();
		},50);
	}
	function remove_tr(val)
	{
		$("#tbl_tr"+val).remove();
		calculate_total("1");
		setTimeout(function(){
			if($("#opd_id").val()!="0")
			{
				$("#opd_now_discount_per").val($("#discount_per_master").val()).prop("disabled", true);
				$("#opd_now_discount_amount").val($("#discount_amount_master").val()).prop("disabled", true);
				
				opd_discount_per_old();
			}
			else
			{
				if($("#c_discount").val()>0)
				{
					opd_discount_per_each();
				}
				else
				{
					opd_discount_amount_each();
				}
			}
		},50);
	}
	
	function opd_discount_per_each()
	{
		var val=parseFloat($("#opd_now_discount_per").val());
		if(!val){ val=0; }
		
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var res_amount=total-disount_amount-paid_amount+refunded_amount;
		if(!res_amount){ res_amount=0; }
		
		//~ var tot=$("#total").val();
		var dis_val=((res_amount*val)/100);
		dis_val=Math.round(dis_val);
		
		$("#opd_now_discount_amount").val(dis_val);
		
		if(dis_val>0)
		{
			$("#opd_now_discount_reason").prop('type', 'text');
		}else
		{
			$("#opd_now_discount_reason").prop('type', 'hidden');
			$("#opd_now_discount_per").val("");
		}
		
		opd_after_discount_calc();
	}
	function opd_discount_amount_each()
	{
		var dis_val=parseInt($("#opd_now_discount_amount").val());
		if(!dis_val){ dis_val=0; }
		
		//~ var tot=parseInt($("#total").val());
		
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var res_amount=total-disount_amount-paid_amount+refunded_amount;
		if(!res_amount){ res_amount=0; }
		
		tot=res_amount;
		
		if(tot==0)
		{
			var per=0;
		}else
		{
			var per=((dis_val*100)/tot);
		}
		
		//~ per=Math.round(per);
		per=per.toFixed(2);
		
		$("#opd_now_discount_per").val(per);
		
		if(dis_val>0)
		{
			$("#opd_now_discount_reason").prop('type', 'text')
		}else
		{
			$("#opd_now_discount_reason").prop('type', 'hidden')
		}
		
		opd_after_discount_calc();
	}
	
	function calculate_total(val)
	{
		$("#loader").show();
		var each_row=$(".each_row");
		
		var total_amount=0;
		var discount_amount=0;
		var test_ids="";
		
		var discount_all=parseFloat($("#opd_now_discount_amount").val());
		if(!discount_all){ discount_all=0; }
		
		for(var i=0;i<each_row.length;i++)
		{
			var tr_counter=each_row[i].value;
			
			var testid=$("#testid"+tr_counter).val();
			
			var test_rate=parseFloat($("#test_rate"+tr_counter).val());
			if(!test_rate){ test_rate=0; }
			
			var discount_each=parseFloat($("#discount_each"+tr_counter).val());
			if(!discount_each){ discount_each=0; }
			
			if($("#c_discount").val()>0)
			{
				discount_each=0;
			}
			
			test_ids=test_ids+"##"+testid+"@"+discount_each;
			
			var each_total=test_rate-discount_each;
			
			discount_amount=parseFloat(discount_amount)+parseFloat(discount_each);
			total_amount=parseFloat(total_amount)+parseFloat(test_rate);
			
			if(each_total<0)
			{
				$("#save_tr").hide();
				$("#discount_each"+tr_counter).css({"color":"red"}).focus();
				$("#loader").hide();
				return false;
			}
			else
			{
				$("#discount_each"+tr_counter).css({"color":"black"});
				$("#save_tr").show();
			}
		}
		
		if(!discount_amount){ discount_amount=0; }
		if(!total_amount){ total_amount=0; }
		
		if(discount_amount>0)
		{
			$("#opd_now_discount_amount").val(discount_amount);
			$(".discount_cls1").prop("readonly", true);
		}
		else
		{
			if($("#insurance").val()==0 && $("#discount_permission").val()==1 && $("#opd_id").val()=="0")
			{
				$("#opd_now_discount_per").prop("disabled", false);
				$("#opd_now_discount_amount").prop("disabled", false);
			}
			//$("#opd_now_discount_amount").val("0");
		}
		
		$("#total").val(total_amount);
		$("#opd_bill_amount").val(total_amount);
		
		total_amount=total_amount.toFixed(2);
		$("#item_total_amount_tbl").text(total_amount);
		$("#opd_bill_amount_str").text(total_amount);
		
		$("#test_ids").val(test_ids);
		$("#loader").hide();
		
	}
	
	function opd_discount_per(e)
	{
		var val=parseFloat($("#opd_now_discount_per").val());
		if(!val){ val=0; }
		
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var res_amount=total-disount_amount-paid_amount+refunded_amount;
		if(!res_amount){ res_amount=0; }
		
		//~ var tot=$("#total").val();
		//~ var dis_val=((res_amount*val)/100);
		var dis_val=((total*val)/100);
		dis_val=Math.round(dis_val);
		
		$("#opd_now_discount_amount").val(dis_val);
		
		if(dis_val>0)
		{
			if($("#opd_id").val()=="0")
			{
				$("#opd_now_discount_reason").prop('type', 'text');
				$(".discount_each").prop("readonly", true);
			}
		}else
		{
			$("#opd_now_discount_reason").prop('type', 'hidden');
			$("#opd_now_discount_per").val("");
			
			if($("#discount_permission").val()==1)
			{
				$(".discount_each").prop("readonly", false);
			}
		}
		
		if(e.which==13)
		{
			$("#opd_now_discount_amount").focus();
		}
		
		opd_after_discount_calc();
	}
	function opd_discount_amount(e)
	{
		var dis_val=parseInt($("#opd_now_discount_amount").val());
		if(!dis_val){ dis_val=0; }
		
		//~ var tot=parseInt($("#total").val());
		
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var res_amount=total-disount_amount-paid_amount+refunded_amount;
		if(!res_amount){ res_amount=0; }
		
		//~ tot=res_amount;
		tot=total;
		
		if(tot==0)
		{
			var per=0;
		}else
		{
			var per=((dis_val*100)/tot);
		}
		
		//~ per=Math.round(per);
		per=per.toFixed(2);
		
		$("#opd_now_discount_per").val(per);
		
		if(dis_val>0 && $("#opd_id").val()=="0")
		{
			$("#opd_now_discount_reason").prop('type', 'text')
			
			$(".discount_each").prop("readonly", true);
		}else
		{
			$("#opd_now_discount_reason").prop('type', 'hidden')
			
			if($("#discount_permission").val()==1)
			{
				$(".discount_each").prop("readonly", false);
			}
		}
		
		if(e.which==13)
		{
			if(dis_val>0)
			{
				$("#opd_now_discount_reason").focus();
			}
			else if($("#opd_now_pay:disabled").length==0)
			{
				$("#opd_now_pay").focus();
			}
			else
			{
				$("#opd_now_payment_mode").focus();
			}
		}
		
		opd_after_discount_calc();
	}
	function opd_now_discount_reason(e)
	{
		$("#opd_now_discount_reason").css({"border-color":""});
		if(e.which==13)
		{
			if($("#opd_now_discount_reason").val()!="")
			{
				$("#opd_now_pay").focus();
			}
			else
			{
				$("#opd_now_discount_reason").css({"border-color":"red"});
			}
		}
	}
	
	function opd_after_discount_calc()
	{
		if($("#opd_id").val()=="0")
		{
			var total=parseInt($("#total").val());
			if(!total){ total=0; }
			
			var disount_amount=parseInt($("#opd_disount_amount").val());
			if(!disount_amount){ disount_amount=0; }
			
			var paid_amount=parseInt($("#opd_paid_amount").val());
			if(!paid_amount){ paid_amount=0; }
			
			var refunded_amount=parseInt($("#opd_refunded_amount").val());
			if(!refunded_amount){ refunded_amount=0; }
			
			$("#opd_bill_amount_str").text(total.toFixed(2));
			$("#opd_bill_amount").val(total);
			
			var res_amount=total-disount_amount-paid_amount+refunded_amount;
			if(!res_amount){ res_amount=0; }
			
			if(res_amount<0)
			{
				setTimeout(function(){
					refund_calc();
				},100);
				return false;
			}
			else
			{
				var now_discount=parseInt($("#opd_now_discount_amount").val());
				if(!now_discount){ now_discount=0; }
				
				res_amount=res_amount-now_discount;
				
				if(res_amount<0)
				{
					$("#opd_now_pay").val("0");
					$(".discount_cls").css({"border-color":"red"});
				}
				else
				{
					if($("#insurance").val()==0)
					{
						$("#opd_now_pay").val(res_amount);
					}
					else
					{
						$("#opd_now_pay").val("0");
					}
					$(".discount_cls").css({"border-color":""});
				}
				//$(".opd_now_balance_tr").hide();
				opd_now_pay('');
				
				setTimeout(function(){
					refund_calc();
				},100);
			}
		}
	}
	function opd_now_pay(e)
	{
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var now_discount=parseInt($("#opd_now_discount_amount").val());
		if(!now_discount){ now_discount=0; }
		
		var opd_now_pay=parseInt($("#opd_now_pay").val());
		if(!opd_now_pay){ opd_now_pay=0; }
		
		if($("#opd_id").val()!="0")
		{
			disount_amount=0;
		}
		
		//alert(total+"="+disount_amount+"="+paid_amount+"="+refunded_amount+"="+now_discount+"="+opd_now_pay);
		
		var res_amount=total-disount_amount-paid_amount+refunded_amount-now_discount-opd_now_pay;
		if(!res_amount){ res_amount=0; }
		
		if(res_amount<0)
		{
			$("#opd_now_pay").css({"border-color":"red"});
			
			$(".opd_now_balance_tr").hide();
		}
		else
		{
			$("#opd_now_pay").css({"border-color":""});
			
			if(res_amount==0)
			{
				$(".opd_now_balance_tr").hide();
			}
			else
			{
				res_amount=res_amount.toFixed(2);
			
				$("#opd_now_balance").text(res_amount);
				$(".opd_now_balance_tr").show();
			}
		}
		if(e.which==13)
		{
			if(res_amount>=0)
			{
				$("#opd_now_payment_mode").focus();
			}
		}
	}
	function opd_payment_mode_up(e)
	{
		if(e.which==13)
		{
			if($("#now_balance_reason").is(":visible"))
			{
				$("#now_balance_reason").focus();
			}
			else if($("#opd_now_cheque_ref_no").is(":visible"))
			{
				$("#opd_now_cheque_ref_no").focus();
			}
			else
			{
				$("#pat_save_btn").focus();
			}
		}
	}
	function opd_payment_mode_change(val)
	{
		$.post("pages/payment_load_data.php",
		{
			type:"payment_mode_change",
			val:val,
		},
		function(data,status)
		{
			var res=data.split("@#@");
			$("#opd_now_ref_field").val(res[0]);
			$("#opd_now_operation").val(res[1]);
			
			if($("#opd_now_ref_field").val()==0)
			{
				$("#opd_now_cheque_ref_no_tr").show();
			}
			else
			{
				$("#opd_now_cheque_ref_no_tr").hide();
			}
			
			if($("#opd_now_operation").val()==2)
			{
				$("#opd_now_balance_reason_str").show();
				$(".opd_now_balance_tr").show();
				
				$("#opd_now_discount_per").val("0").prop("disabled", true);
				$("#opd_now_discount_amount").val("0").prop("disabled", true);
				$("#opd_now_pay").val("0").prop("disabled", true);
				$("#opd_now_discount_reason").prop('type', 'hidden');
				opd_now_pay(event);
			}
			else
			{
				$("#opd_now_balance_reason_str").hide();
				$(".opd_now_balance_tr").hide();
				
				$("#opd_now_discount_per").prop("disabled", false);
				$("#opd_now_discount_amount").prop("disabled", false);
				$("#opd_now_pay").prop("disabled", false);
				opd_now_pay(event);
			}
		})
	}
	function now_balance_reason(e)
	{
		$("#now_balance_reason").css({"border-color":""});
		if(e.which==13)
		{
			if($("#now_balance_reason").val()=="")
			{
				$("#now_balance_reason").css({"border-color":"red"});
				return false;
			}
			if($("#opd_now_cheque_ref_no").is(":visible"))
			{
				$("#opd_now_cheque_ref_no").focus();
			}
			else
			{
				$("#pat_save_btn").focus();
			}
		}
	}
	function opd_now_cheque_ref_no(e)
	{
		$("#opd_now_cheque_ref_no").css({"border-color":""});
		if(e.which==13)
		{
			$("#pat_save_btn").focus();
		}
	}
	
	function refund_calc()
	{
		var bill_amount_old=parseInt($("#opd_bill_amount_old").val());
		if(!bill_amount_old){ bill_amount_old=0; }
		
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		$("#opd_bill_amount_str").text(total.toFixed(2));
		$("#opd_bill_amount").val(total);
		
		var disount_refund=0;
		var bill_diff_amount=bill_amount_old-total;
		if(bill_diff_amount>0)
		{
			var discount_per_all=(disount_amount/bill_amount_old)*100;
			disount_refund=(bill_diff_amount*discount_per_all)/100;
		}
		
		//alert(bill_amount_old+" "+total+" "+disount_amount+" "+paid_amount+" "+refunded_amount+" "+disount_refund);
		
		var res_amount=total-disount_amount-paid_amount+refunded_amount+disount_refund;
		if(!res_amount){ res_amount=0; }
		//alert(res_amount);
		if(res_amount<0)
		{
			$("#opd_now_refund_tr").show();
			
			var refund_amount_now=res_amount*(-1);
			
			$("#opd_now_refund").val(refund_amount_now);
			$("#opd_now_refund_str").text(refund_amount_now.toFixed(2));
			
			$("#opd_now_discount_per").val("0").prop("disabled", true);
			$("#opd_now_discount_amount").val("0").prop("disabled", true);
			$("#opd_now_pay").val("").prop("disabled", true);
			
			$(".opd_now_balance_tr").hide();
			$(".opd_now_cheque_ref_no_tr").hide();
			
			$("#opd_now_payment_mode").focus();
			
		}
		else
		{
			$("#opd_now_refund_tr").hide();
			
			$("#opd_now_refund").val("0");
			$("#opd_now_refund_str").text("0.00");
		}
		//scrollPage(280);
	}
	// Payment End
	
	// Save Patient Start
	function pat_save()
	{
		if($("#pat_name").val()=="" && $("#patient_id").val()=="0")
		{
			scrollPage(210);
			$("#pat_name").focus().css({"border-color":"red"});
			return false;
		}
		if($("#dob").val()=="" && $("#patient_id").val()=="0")
		{
			scrollPage(210);
			$("#dob").focus().css({"border-color":"red"});;
			return false;
		}
		if(($("#phone").val()=="" && $("#patient_id").val()=="0") || ($("#phone").val().length!=10 && $("#patient_id").val()=="0"))
		{
			scrollPage(230);
			$("#phone").focus().css({"border-color":"red"});;
			return false;
		}
		//~ if($("#gd_name").val()=="" && $("#patient_id").val()=="0")
		//~ {
			//~ scrollPage(230);
			//~ $("#gd_name").focus().css({"border-color":"red"});;
			//~ return false;
		//~ }
		//~ if($("#g_relation").val()=="" && $("#patient_id").val()=="0")
		//~ {
			//~ scrollPage(230);
			//~ $("#g_relation").focus().css({"border-color":"red"});;
			//~ return false;
		//~ }
		//~ if(($("#gd_phone").val()=="" && $("#patient_id").val()=="0") || ($("#gd_phone").val().length!=10 && $("#patient_id").val()=="0"))
		//~ {
			//~ scrollPage(230);
			//~ $("#gd_phone").focus().css({"border-color":"red"});;
			//~ return false;
		//~ }
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
		
		// Investigation Part
		if($("#refbydoctorid").val()=="0" || $("#refbydoctorid").val()=="")
		{
			scrollPage(scroll_val);
			$("#r_doc").val("").focus().css({"border-color":"red"});;
			return false;
		}
		if($("#center_no").val()=="")
		{
			scrollPage(scroll_val);
			$("#center_no").val("").focus().css({"border-color":"red"});;
			return false;
		}
		if($("#hguide_id").val()=="" || $("#hguide_id").val()==0)
		{
			scrollPage(scroll_val);
			$("#center_no").val("").focus().css({"border-color":"red"});;
			return false;
		}
		//~ if($("#test_ids").val()=="")
		//~ {
			//~ scrollPage(380);
			//~ $("#test").focus().css({"border-color":"red"});
			//~ return false;
		//~ }
		
		// Test selection
		var each_row=$(".each_row");
		var test_all="";
		for(var i=0;i<each_row.length;i++)
		{
			var tr_counter=each_row[i].value;
			
			var testid=$("#testid"+tr_counter).val();
			
			var test_rate=parseFloat($("#test_rate"+tr_counter).val());
			if(!test_rate){ test_rate=0; }
			
			var discount_each=parseFloat($("#discount_each"+tr_counter).val());
			if(!discount_each){ discount_each=0; }
			
			if(testid)
			{
				test_all=test_all+"##"+testid+"@"+test_rate+"@"+discount_each;
			}
		}
		if(test_all=="")
		{
			bootbox.dialog({ message: "<h4>None test selected</h4> ",size:"small"});
			setTimeout(function(){
				bootbox.hideAll();
				scrollPage(380);
				$("#test").focus();
			},2000);
			return false;
		}
		
		// Payment Part
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var disount_amount=parseInt($("#opd_disount_amount").val());
		if(!disount_amount){ disount_amount=0; }
		
		var paid_amount=parseInt($("#opd_paid_amount").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var now_discount=parseInt($("#opd_now_discount_amount").val());
		if(!now_discount){ now_discount=0; }
		
		if($("#opd_id").val()!="0")
		{
			disount_amount=0;
		}
		
		var yet_amount=total-disount_amount-paid_amount+refunded_amount;
		if(!yet_amount){ yet_amount=0; }
		
		var refund_val=0;
		
		var yet_amount=total-disount_amount-paid_amount+refunded_amount-now_discount;
		if(!yet_amount){ yet_amount=0; }
		
		if(yet_amount<0)
		{
			if($("#opd_id").val()!="0")
			{
				var refund_val=1;
			}
		}
		
		if(yet_amount<0 && refund_val==0)
		{
			$("#opd_now_discount_amount").css({"border-color":"red"}).focus();
			return false;
		}
		
		if(now_discount>0 && $("#opd_id").val()=="0")
		{
			if($("#opd_now_discount_reason").val()=="")
			{
				$("#opd_now_discount_reason").prop('type', 'text').css({"border-color":"red"}).focus();
				return false;
			}
		}
		
		var opd_now_pay=parseInt($("#opd_now_pay").val());
		if(!opd_now_pay){ opd_now_pay=0; }
		
		var res_amount=total-disount_amount-paid_amount+refunded_amount-now_discount-opd_now_pay;
		if(!res_amount){ res_amount=0; }
		
		if(res_amount<0 && refund_val==0)
		{
			$("#opd_now_pay").css({"border-color":"red"}).focus();
			return false;
		}
		
		if(res_amount>0 && refund_val==0)
		{
			if($("#now_balance_reason").val()=="")
			{
				$(".opd_now_balance_tr").show();
				$("#now_balance_reason").css({"border-color":"red"}).focus();
				return false;
			}
		}
		
		var now_discount=parseInt($("#opd_now_discount_amount").val());
		if(!now_discount){ now_discount=0; }
		
		$("#loader").show();
		$("#save_tr").hide();
		
		$.post("pages/new_lab_registration_data.php",
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
			test_all:test_all,
			total:total,
			now_discount:now_discount,
			opd_now_discount_reason:$("#opd_now_discount_reason").val(),
			opd_now_pay:opd_now_pay,
			opd_now_payment_mode:$("#opd_now_payment_mode").val(),
			now_balance_reason:$("#now_balance_reason").val(),
			opd_now_cheque_ref_no:$("#opd_now_cheque_ref_no").val(),
			
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
			
			bootbox.dialog({ message: "<h5>"+res[3]+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
			},2000);
			
			if(res[1]!="0")
			{
				load_payment_info();
			}
			
			var access=res[2].split("#");
			if(access[1]==0)
			{
				$(".pat_info").prop("disabled", true);
			}
			if(access[2]==0)
			{
				$(".doctor_info").prop("disabled", true);
				$(".remove_btn_cls").hide();
			}
		})
	}
	// Save Patient End
	
	function edit_receipt(pid)
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_paid_info",
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			pay_id:pid,
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#res_payment_div").html("").slideUp(400);
			$("#advance_paid_div").html(data).slideDown(500);
			
			scrollPage(750);
		})
	}
	function save_payment_edit(pid)
	{
		var total=parseInt($("#opd_bill_amount").val());
		if(!total){ total=0; }
		
		var now_discount=parseInt($("#opd_now_discount_amount").val());
		if(!now_discount){ now_discount=0; }
		
		if(now_discount>0)
		{
			if($("#opd_now_discount_reason").val()=="")
			{
				$("#opd_now_discount_reason").prop('type', 'text').css({"border-color":"red"}).focus();
				return false;
			}
		}
		
		var opd_now_pay=parseInt($("#opd_now_pay").val());
		if(!opd_now_pay){ opd_now_pay=0; }
		
		var res_amount=total-now_discount-opd_now_pay;
		if(!res_amount){ res_amount=0; }
		
		if(res_amount<0)
		{
			$("#opd_now_pay").css({"border-color":"red"}).focus();
			return false;
		}
		
		if(res_amount>0)
		{
			if($("#now_balance_reason").val()=="")
			{
				$(".opd_now_balance_tr").show();
				$("#now_balance_reason").css({"border-color":"red"}).focus();
				return false;
			}
		}
		
		$("#loader").show();
		$("#save_tr").hide();
		
		$.post("pages/new_lab_registration_data.php",
		{
			type:"save_payment_edit",
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			p_type_id:$("#p_type_id").val(),
			pay_id:pid,
			total:total,
			now_discount:now_discount,
			opd_now_discount_reason:$("#opd_now_discount_reason").val(),
			opd_now_pay:opd_now_pay,
			opd_now_payment_mode:$("#opd_now_payment_mode").val(),
			now_balance_reason:$("#now_balance_reason").val(),
			opd_now_cheque_ref_no:$("#opd_now_cheque_ref_no").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			$("#save_tr").show();
			
			var res=data.split("@");
			
			bootbox.dialog({ message: "<h5>"+res[0]+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
				load_payment_info();
			},2000);
		})
	}
	
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
	
	function estimate_receipt()
	{
		url="pages/estimate_receipt.php?type=1&bid="+$("#branch_id").val();
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function print_receipt(url)
	{
		var uhid=$("#patient_id").val();
		url=url+"&uhid="+btoa(uhid);
		
		var opd_id=$("#opd_id").val();
		url=url+"&opdid="+btoa(opd_id);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function load_test_print()
	{
		$.post("pages/pat_reg_prints.php",
		{
			uhid:$("#patient_id").val(),
			opd_id:$("#opd_id").val()
		},
		function(data,status)
		{
			$("#results2").html(data);
			//$(".modal-dialog").css({'width':'500px'});
			$("#mod2").click();
			//$("#mod_chk").val("1");
			$("#results").fadeIn(500);
		})
	}
	function print_indiv(uhid,visit)
	{
		var norm=$(".norm:checked");
		var norm_l=0;
		if(norm.length>0)
		{
			for(var i=0;i<norm.length;i++)
			{
				norm_l=norm_l+"@"+$(norm[i]).val();
			}
		}
		
		var path=$(".path:checked");
		var path_l=0;
		if(path.length>0)
		{
			for(var j=0;j<path.length;j++)
			{
				path_l=path_l+"@"+$(path[j]).val();
			}
		}
		
		
		var rad=$(".rad:checked");
		var rad_l=0;
		if(rad.length>0)
		{
		for(var k=0;k<rad.length;k++)
			{
				rad_l=rad_l+"@"+$(rad[k]).val();
			}
		}

		//var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opd_id="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
		var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opdid="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
	}
	function print_transaction(pid)
	{
		var url="pages/print_transaction_receipt.php?v="+btoa(2);
		
		var uhid=$("#patient_id").val();
		url=url+"&uhid="+btoa(uhid);
		
		var opd_id=$("#opd_id").val();
		url=url+"&opdid="+btoa(opd_id);
		
		url=url+"&pid="+btoa(pid);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function payment_mode_change_trans(pid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to change payment mode ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
						bootbox.hideAll();
						load_payment_info();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						payment_mode_change_trans_check(pid);
					}
				}
			}
		});
	}
	
	function payment_mode_change_trans_check(pid)
	{
		if($("#opd_payment_mode_trans"+pid).val()=="Credit")
		{
			load_payment_info();
			bootbox.alert("Failed, try again later.");
		}
		else if($("#opd_payment_mode_trans"+pid).val()=="Cash")
		{
			payment_mode_change_trans_ok(pid,"");
		}
		else
		{
			bootbox.dialog({
				message: "<input type='text' class='capital' id='cheque_ref_no_trans' autofocus />",
				title: "Cheque/Reference no",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> Cancel',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
							load_payment_info();
						}
					},
					main: {
						label: '<i class="icon-ok"></i> Change',
						className: "btn btn-danger",
						callback: function() {
							
							payment_mode_change_trans_ok(pid,$("#cheque_ref_no_trans").val());
							
						}
					}
				}
			});
		}
	}
	function payment_mode_change_trans_ok(pid,cheque_ref_no_trans)
	{
		$("#loader").show();
		$.post("pages/new_lab_registration_data.php",
		{
			type:"payment_mode_change",
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			p_type_id:$("#p_type_id").val(),
			pay_id:pid,
			payment_mode:$("#opd_payment_mode_trans"+pid).val(),
			cheque_ref_no:cheque_ref_no_trans,
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			bootbox.dialog({ message: "<h5>"+data+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
				load_payment_info();
			},2000);
		})
	}
	
	function delete_receipt(pid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						delete_receipt_ok(pid);
					}
				}
			}
		});
	}
	function delete_receipt_ok(pid)
	{
		bootbox.dialog({
			message: "Reason:<input type='text' id='del_reason' autofocus />",
			title: "Payment Delete",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				main: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						if($("#del_reason").val()!="")
						{
							$("#loader").show();
							$.post("pages/new_lab_registration_data.php",
							{
								type:"delete_payment",
								patient_id:$("#patient_id").val(),
								opd_id:$("#opd_id").val(),
								p_type_id:$("#p_type_id").val(),
								pay_id:pid,
								del_reason:$("#del_reason").val(),
								user:$("#user").text().trim(),
							},
							function(data,status)
							{
								//alert(data);
								$("#loader").hide();
								bootbox.dialog({ message: "<h5>"+data+"</h5> "});
								setTimeout(function(){
									bootbox.hideAll();
									load_payment_info();
								},2000);
							})
						}
						else
						{
							bootbox.alert("Reason cannot blank");
						}
					}
				}
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
	
	function show_selected_test()
	{
		if($("#list_all_test").css('display')=="none")
		{
			$("#list_all_test").slideDown();
			//$("#test_view_btn").hide();
		}
		else
		{
			$("#list_all_test").slideUp(500)
			$("#test_d").html("");
			
			var item_chk=$("#test_list tr").length;
			if(item_chk>0)
			{
				$("#test_view_btn").show();
			}
		}
	}
	
	function opd_discount_per_old()
	{
		var old_bill_amount=parseInt($("#opd_bill_amount_old").val());
		if(!old_bill_amount){ old_bill_amount=0; }
		
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		var discount_per_master=parseInt($("#discount_per_master").val());
		if(!discount_per_master){ discount_per_master=0; }
		
		var paid_amount=parseInt($("#paid_amount_master").val());
		if(!paid_amount){ paid_amount=0; }
		
		var refunded_amount=parseInt($("#opd_refunded_amount").val());
		if(!refunded_amount){ refunded_amount=0; }
		
		var bill_diff_amount=old_bill_amount-total;
		
		var discount_amount=((total*discount_per_master)/100);
		
		discount_amount=Math.round(discount_amount);
		
		var balance_amount=total-discount_amount-paid_amount;
		
		if(balance_amount>=0)
		{
			$("#opd_now_pay").val(balance_amount);
			
			$("#opd_now_refund_tr").hide();
			$("#opd_now_refund").val(0);
			$("#opd_now_refund_str").text("");
		}
		else
		{
			$("#opd_now_pay").val(0);
			
			var refund_amount=balance_amount*(-1);
			$("#opd_now_refund_tr").show();
			$("#opd_now_refund").val(refund_amount);
			$("#opd_now_refund_str").text(refund_amount.toFixed(2));
		}
		
		$("#opd_now_discount_amount").val(discount_amount);
		
	}
</script>

<style>
label {
	display: inline;
}
.table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th
{
	padding: 2px;
}
.list_cls
{
	padding: 2px 6px;
	height: 15px !important;
	border:0 !important;
}

.test_rate, .discount_each
{
	pointer-events: none;
}
.test_discount
{
	display:none;
}
</style>
