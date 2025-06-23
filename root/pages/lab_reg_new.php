<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span> ( <b style="color:#ff0000;">*</b> ) mark mandatory</div>
</div>
<!--End-header-->
<div class="container-fluid">
<style>
	#padd_tbl th, #padd_tbl td
	{padding:0px;}
</style>
	<!--<h2 class="alert_msg"></h2>-->
<?php
	if($_GET["uhid"])
	{
		$uhid=base64_decode($_GET["uhid"]);
		$uhid=trim($uhid);
		
		$pin=base64_decode($_GET["pin"]);
		$pin=trim($pin);
		
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
		$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
		$pat_info_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
		
		$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
		$health_guide=mysqli_fetch_array(mysqli_query($link, " SELECT `hguide_id`, `name` FROM `health_guide` WHERE `hguide_id` in ( SELECT `hguide_id` FROM `pat_health_guide` WHERE `patient_id`='$uhid' ) "));
		if($pat_info["dob"]!='')
		{
			$dob=age_calculator($pat_info["dob"]);
			$dob=explode(". ",$dob);
		}else{
			$dob[0]=$pat_info["age"];
			$dob[1]=$pat_info["age_type"];
		}
		$pat_name_str=explode(". ",$pat_info["name"]);
		$pat_name_title=trim($pat_name_str[0]);
		$pat_name=trim($pat_name_str[1]." ".$pat_name_str[2]." ".$pat_name_str[3]." ".$pat_name_str[4]);
		
		if($pat_info_other["source_id"]==2)
		{
			$esi_ip_no_show="";
		}else
		{
			$esi_ip_no_show="style='display:none;'";
		}
		$esi_ip_no_show="style='display:none;'";
?>
	<table id="padd_tbl" class="table table-condensed">
		<tr style="display:;">
			<th>Patient Type</th>
			<td colspan="3">
				<select id="patient_type" class="span2" onChange="patient_type_ch(this.value,event)">
				<?php
					$type_qry=mysqli_query($link, " SELECT * FROM `patient_source_master` ORDER BY `source_id` ");
					while($type=mysqli_fetch_array($type_qry))
					{
						if($pat_info_other["source_id"]==$type["source_id"]){ $pat_type_sel="selected"; }else{ $pat_type_sel=""; }
						echo "<option value='$type[source_id]' $pat_type_sel >$type[source_type]</option>";
					}
				?>
				</select>
			</td>
			<th class="esi_ip_no_sh" <?php echo $esi_ip_no_show; ?>>IP No</th>
			<td colspan="3" class="esi_ip_no_sh" <?php echo $esi_ip_no_show; ?>><input type="text" class="span2" id="esi_ip_no" value="<?php echo $pat_info_other["esi_ip_no"] ?>" onKeyup="esi_ip_no_up(event)" /></td>
		</tr>
		<tr style="display:none;">
			<th>Payment Type</th>
			<td>
				<select id="ptype" class="span2" onkeyup="tab(this.id,event)">
					<option value="1" <?php if($pat_info["payment_mode"]==1){ echo "selected"; } ?>>Cash</option>
					<option value="2" <?php if($pat_info["payment_mode"]==2){ echo "selected"; } ?>>Credit</option>
				</select>
			</td>
			<th>Credit</th>
			<td>
				<select id="credit" class="span2" onkeyup="tab(this.id,event)">
					<option value="0" <?php if($pat_info_rel["credit"]==0){ echo "selected"; } ?>>Select</option>
					<option value="1" <?php if($pat_info_rel["credit"]==1){ echo "selected"; } ?>>1</option>
					<option value="2" <?php if($pat_info_rel["credit"]==2){ echo "selected"; } ?>>2</option>
				</select>
			</td>
			<th>No</th>
			<td><input type="text" class="span1" id="crno" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" value="<?php echo $pat_info_rel["crno"]; ?>" ></td>
		</tr>
		<tr>
			<th>Name <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="name_title" onChange="name_title_ch(this.value)" onKeyup="name_title_up(this.value,event)" class="span1" autofocus>
				<?php
					$title_qry=mysqli_query($link, " SELECT * FROM `name_title` ORDER BY `title_id` ");
					while($val=mysqli_fetch_array($title_qry))
					{
						if($pat_name_title."."==$val['title']){ $title_sel="selected"; }else{ $title_sel=""; }
						echo "<option value='$val[title]' $title_sel>$val[title]</option>";
					}
				?>
				</select>
				<input type="text" class="span2" id="pat_name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" value="<?php echo $pat_name; ?>" >
			</td>
			<th>DOB (DD-MM-YYYY) </th>
			<td>
				<input type="text" id="mask-date" class="span2 dob" maxlength="10" placeholder="DD-MM-YYYY" onKeyup="cal_age_dob(this.value,event)" value="<?php echo $pat_info["dob"]; ?>">
			</td>
			<th>Age <b style="color:#ff0000;">*</b></th>
			<td>
				<span>
					<input type="text" id="age_y" class="span1" onKeyup="age_y_check(this,event)" placeholder="Years" title="Years">
					<input type="text" id="age_m" class="span1" onKeyup="age_m_check(this,event)" placeholder="Months" title="Months">
					<input type="text" id="age_d" class="span1" onKeyup="age_d_check(this,event)" placeholder="Days" title="Days">
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
				<select id="sex" class="span2" onKeyup="sex(event)">
					<option value="Male" <?php if($pat_info["sex"]=="Male"){ echo "selected"; } ?>>Male</option>
					<option value="Female" <?php if($pat_info["sex"]=="Female"){ echo "selected"; } ?>>Female</option>
					<option value="Other" <?php if($pat_info["sex"]=="Other"){ echo "selected"; } ?>>Other</option>
				</select>
			</td>
			<th>Phone</th>
			<td>
				<input type="text"class="span2" id="phone" maxlength="10" onKeyup="phone_check(this.value,event)" value="<?php echo $pat_info["phone"]; ?>">
			</td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Guardian's Name</th>
			<td>
				<input type="text" class="span2" id="g_name" onKeyup="caps_it(this.value,this.id,event)" value="<?php echo $pat_info["gd_name"]; ?>">
			</td>
			<th>Relation with Guardian</th>
			<td>
				<input type="text" class="span2" id="g_relation" onKeyup="caps_it(this.value,this.id,event)" value="<?php echo $pat_info_other["relation"]; ?>" list="relation_datalist">
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
		</tr>
		<tr style="display:;">
			<th>Guardian's Contact</th>
			<td>
				<input type="text" class="span2" id="g_ph" maxlength="10" onkeyup="tab(this.id,event)" value="<?php echo $pat_info_rel["gd_phone"]; ?>">
			</td>
			<th>Marital Status <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="marital_status" class="span2" onkeyup="tab(this.id,event)">
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
			<th>Income Group</th>
			<td>
				<select id="income_id" class="" onkeyup="tab(this.id,event)">
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
			<th style="display:none;">Address</th>
			<td style="display:none;">
				<textarea id="address" class="form-control" placeholder="Address" style="resize:none;" onkeyup="tab(this.id,event)"><?php echo $pat_info["address"]; ?></textarea>
			</td>
		</tr>
		<tr>
			<th>Pin Code</th>
			<td>
				<input type="text" class="span2" onKeyup="pin(this.value,event)" id="pin" value="<?php echo $pat_info_rel["pin"]; ?>">
			</td>
			<th>Police Station</th>
			<td>
				<input type="text" class="span2" id="police" onkeyup="police_station(this.value,event)" value="<?php echo $pat_info_rel["police"]; ?>" list="police_datalist">
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
			<th>State</th>
			<td>
				<select id="state" onchange="load_dist()" onkeyup="tab(this.id,event)">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `state` ORDER BY `name`");
					while($r=mysqli_fetch_array($q))
					{
						if($pat_info_rel["state"]==$r['state_id']){ $sel_state="selected"; }else{ $sel_state=""; }
					?>
					<option value="<?php echo $r['state_id'];?>" <?php echo $sel_state; ?>><?php echo $r['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>District</th>
			<td id="dist_list">
				<select class="span2" id="dist" onkeyup="tab(this.id,event)">
					<option value="0">Select</option>
				</select>
			</td>
			<th>City / Village</th>
			<td>
				<input type="text" class="span2" onkeyup="city_vill(this.value,event)" id="city" value="<?php echo $pat_info_rel["city"]; ?>" list="city_datalist">
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
			<th>Post Office</th>
			<td>
				<input type="text" class="" onkeyup="post_office(this.value,event)" id="post_office" value="<?php echo $pat_info_rel["post_office"]; ?>" list="post_office_datalist" />
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
		</tr>
		<tr style="display:none;">
			<th style="display:none;">Referred By</th>
			<td style="display:none;">
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_doc_name["ref_name"]."-".$pat_info["refbydoctorid"]; ?>">
				<input type="text" name="doc_id" id="doc_id" style="display:none;" value="<?php echo $pat_info["refbydoctorid"]; ?>">
				<input type="button" name="new_doc" id="new_doc" value="New" class="btn btn-info btn-mini" onClick="load_new_ref_doc()" />
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
						<th>Doctor ID</th>
						<th>Doctor Name</th>
						<?php
							$d=mysqli_query($GLOBALS["___mysqli_ston"], "select * from refbydoctor_master where refbydoctorid='101' order by ref_name");
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
			<th>Health Guide</th>
			<td>
				<input type="text" name="hguide" id="hguide" class="span2" size="25" onFocus="hguide_focus()" onKeyUp="hguide_up(this.value,event)" onBlur="javascript:$('#hguide_div').fadeOut(500)" value="<?php echo $health_guide["name"].'-'.$health_guide["hguide_id"] ?>" />
				<input type="text" name="hguide_id" id="hguide_id" style="display:none;" value="<?php echo $health_guide["hguide_id"] ?>" />
				<div id="hguide_info"></div>
				<div id="hguide_div" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
						<th>Health Guide ID</th>
						<th>Health Guide Name</th>
						<?php
							$q=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `health_guide` WHERE `hguide_id`='HG101' ");
							$i=1;
							while($val=mysqli_fetch_array($q))
							{
						?>
							<tr onClick="hguide_load('<?php echo $val['hguide_id'];?>','<?php echo $val['name'];?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
								<td>
									<?php echo $val['hguide_id'];?>
								</td>
								<td>
									<?php echo $val['name'];?>
									<div <?php echo "id=dvhguide".$i;?> style="display:none;">
										<?php echo "#".$val['hguide_id']."#".$val['name'];?>
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
			<th>OPD File No.</th>
			<td><input type="text" class="span2" id="fileno" onKeyup="tab(this.id,event)" value="<?php echo $pat_info_rel["file_no"]; ?>"></td>
		</tr>
		<tr>
			<td colspan="6" style="text-align:center;">
				<br>
				<button class="btn btn-save" id="save" onClick="save('update_lab_pat_info')"><i class="icon-save"></i> Save</button>
				<input type="hidden" id="patient_id" value="<?php echo $pat_info["patient_id"]; ?>">
				<input type="hidden" id="opd_id" value="<?php echo $pin; ?>">
			</td>
		</tr>
	</table>
<?php
	}else
	{
?>
	<span style="float:right;">
		<!--<input type="button" class="btn btn-info" id="token_list" value="View Token List" onclick="view_token_list()" style="" />-->
		<input type="button" class="btn btn-info" id="test_list" value="View Test Rate" onclick="view_test_list()" style="" />
	</span>
	<!--<h4>Search</h4>-->
	<table id="padd_tbl" class="table table-condensed">
		<tr>
			<td colspan="8">
				<center>
					<h4>Search</h4>
				</center>
			</td>
		</tr>
		<tr>
			<th>UHID <br/>
				<input type="text" class="span2" id="search_uhid" onkeyup="load_emp(this.value,event,'uhid')" placeholder="Type UHID" >
			</td>
			<!--<th>OPD Serial</th>
			<td>
				<input type="text" id="search_uhid" onkeyup="load_emp(this.value,event,'opd_serial')" placeholder="Type OPD Serial" >
			</td>-->
			<th>Bill No <br/>
				<input type="text" class="span2" id="search_pin" onkeyup="load_emp(this.value,event,'pin')" placeholder="Type Bill No" >
			</td>
			<th>Name<br/>
				<input type="text" class="span2" id="search_name" onkeyup="load_emp(this.value,event,'name')" placeholder="Type Name" >
			</td>
			<th>Phone No<br/>
				<input type="text" class="span2" id="search_phone" onkeyup="load_emp(this.value,event,'phone')" placeholder="Type Phone No" >
			</td>
			<th style="display:none;">File No</th>
			<td style="display:none;">
				<input type="text" id="search_fname" onkeyup="load_emp(this.value,event,'fname')" placeholder="Type File Name" >
			</td>
		</tr>
		<tr>
			<td colspan="8">
				<div id="emp_list" style="max-height:450px;overflow-y:scroll;">
					
				</div>
			</td>
		</tr>
	</table>
	<table id="padd_tbl" class="table table-condensed">
		<tr style="display:">
			<th>Patient Type</th>
			<td colspan="5">
				<select id="patient_type" class="span2" onChange="patient_type_ch(this.value,event)">
				<?php
					$type_qry=mysqli_query($link, " SELECT * FROM `patient_source_master` ORDER BY `source_id` ");
					while($type=mysqli_fetch_array($type_qry))
					{
						echo "<option value='$type[source_id]'>$type[source_type]</option>";
					}
				?>
				</select>
			</td>
			<th class="esi_ip_no_sh" style="display:none;">IP No</th>
			<td colspan="3" class="esi_ip_no_sh" style="display:none;"><input type="text" class="span2" id="esi_ip_no" onKeyup="esi_ip_no_up(event)" /></td>
		</tr>
		<tr style="display:none;">
			<th>Payment Type</th>
			<td>
				<select id="ptype" class="span2" onkeyup="tab(this.id,event)">
					<option value="1">Cash</option>
					<option value="2">Credit</option>
				</select>
			</td>
			<th>Credit</th>
			<td>
				<select id="credit" class="span2" onkeyup="tab(this.id,event)">
					<option value="0">Select</option>
					<option value="1">1</option>
					<option value="2">2</option>
				</select>
			</td>
			<th>No</th>
			<td><input type="text" class="span1" id="crno" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" /></td>
		</tr>
		<tr>
			<th>Name <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="name_title" onChange="name_title_ch(this.value)" onKeyup="name_title_up(this.value,event)" class="span1" autofocus>
				<?php
					$title_qry=mysqli_query($link, " SELECT * FROM `name_title` ORDER BY `title_id` ");
					while($val=mysqli_fetch_array($title_qry))
					{
						echo "<option value='$val[title]'>$val[title]</option>";
					}
				?>
				</select>
				<input type="text" class="span2" id="pat_name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" />
			</td>
			<th>DOB (DD-MM-YYYY)</th>
			<td>
				<input type="text" id="mask-date" class="span2 dob" maxlength="10" placeholder="DD-MM-YYYY" onKeyup="cal_age_dob(this.value,event);">
			</td>
			<th>Age <b style="color:#ff0000;">*</b></th>
			<td>
				<span>
					<input type="text" id="age_y" class="span1" onKeyup="age_y_check(this,event)" placeholder="Years" title="Years">
					<input type="text" id="age_m" class="span1" onKeyup="age_m_check(this,event)" placeholder="Months" title="Months">
					<input type="text" id="age_d" class="span1" onKeyup="age_d_check(this,event)" placeholder="Days" title="Days">
				</span>
				<span style="display:none;">
					<input type="text" id="age" class="span1" onKeyup="age_check(this.value,event)" onBlur="gen_birth_date()" placeholder="Age"><text id="year">Years</text>
				</span>
			</td>
		</tr>
		<tr>
			<th>Sex <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="sex" class="span2" onKeyup="sex(event)">
					<option value="Male">Male</option>
					<option value="Female">Female</option>
					<option value="Other">Other</option>
				</select>
			</td>
			<th>Phone <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" class="span2" id="phone" maxlength="10" onKeyup="phone_check(this.value,event)">
			</td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Guardian's Name</th>
			<td><input type="text" class="span2" id="g_name" onKeyup="caps_it(this.value,this.id,event)"></td>
			<th>Relation with Patient</th>
			<td>
				<input type="text" class="span2" id="g_relation" onKeyup="caps_it(this.value,this.id,event)" list="relation_datalist">
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
		</tr>
		<tr style="display:;">
			<th>Guardian's Contact</th>
			<td><input type="text" class="span2" id="g_ph" maxlength="10" onkeyup="tab(this.id,event)" /></td>
			<th>Marital Status</th>
			<td>
				<select id="marital_status" class="span2" onkeyup="tab(this.id,event)">
					<option value="0">Select</option>
				<?php
					$marry_qry=mysqli_query($link, " SELECT * FROM `marital_status` ORDER BY `status_id` ");
					while($marry=mysqli_fetch_array($marry_qry))
					{
						echo "<option value='$marry[status_id]'>$marry[status_name]</option>";
					}
				?>
				</select>
			</td>
			<th>Income Group</th>
			<td>
				<select id="income_id" class="" onkeyup="tab(this.id,event)">
				<?php
					$income_qry=mysqli_query($link, " SELECT `income_id`, `income` FROM `income_master` ORDER BY `income_id` ");
					while($income=mysqli_fetch_array($income_qry))
					{
						echo "<option value='$income[income_id]'>$income[income]</option>";
					}
				?>
				</select>
			</td>
			<th style="display:none;">Address</th>
			<td style="display:none;"><textarea id="address" class="form-control" placeholder="Address" style="resize:none;" onkeyup="tab(this.id,event)"></textarea></td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Pin Code</th>
			<td><input type="text" class="span2" onKeyup="pin(this.value,event)" id="pin" /></td>
			<th>Police Station</th>
			<td>
				<input type="text" class="span2" id="police" onkeyup="police_station(this.value,event)" list="police_datalist" />
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
			<th>State</th>
			<td>
				<select id="state" onchange="load_dist()" onkeyup="tab(this.id,event)">
					<option value="0">Select</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `state` ORDER BY `name`");
					while($r=mysqli_fetch_array($q))
					{
						$company_detaill=mysqli_fetch_array(mysqli_query($link, " SELECT `state` FROM `company_name` "));
						if($company_detaill["state"]==$r['name']){ $sel_state="selected"; }else{ $sel_state=""; }
					?>
					<option value="<?php echo $r['state_id'];?>" <?php echo $sel_state; ?>><?php echo $r['name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>District</th>
			<td id="dist_list">
				<select class="span2" id="dist" onkeyup="tab(this.id,event)">
					<option value="0">Select</option>
				</select>
			</td>
			<th>City / Village</th>
			<td>
				<input type="text" class="span2" onkeyup="city_vill(this.value,event)" id="city" list="city_datalist" />
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
			<th>Post Office</th>
			<td>
				<input type="text" class="" onkeyup="post_office(this.value,event)" id="post_office" value="" list="post_office_datalist" />
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
		</tr>
		<tr style="display:none;">
			<th style="display:none;">Referred By</th>
			<td style="display:none;">
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" />
				<input type="text" name="doc_id" id="doc_id" style="display:none;" value="101" />
				<input type="button" name="new_doc" id="new_doc" value="New" class="btn btn-info btn-mini" onClick="load_new_ref_doc()" />
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
						<th>Doctor ID</th>
						<th>Doctor Name</th>
						<?php
							$d=mysqli_query($GLOBALS["___mysqli_ston"], "select * from refbydoctor_master where refbydoctorid='101' order by ref_name");
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
			
			<th>Health Guide</th>
			<td>
				<input type="text" name="hguide" id="hguide" class="span2" size="25" onFocus="hguide_focus()" onKeyUp="hguide_up(this.value,event)" onBlur="javascript:$('#hguide_div').fadeOut(500)" />
				<input type="text" name="hguide_id" id="hguide_id" style="display:none;" />
				<div id="hguide_info"></div>
				<div id="hguide_div" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
						<th>Health Guide ID</th>
						<th>Health Guide Name</th>
						<?php
							$q=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `health_guide` WHERE `hguide_id`='HG101' ");
							$i=1;
							while($val=mysqli_fetch_array($q))
							{
						?>
							<tr onClick="hguide_load('<?php echo $val['hguide_id'];?>','<?php echo $val['name'];?>')" style="cursor:pointer" <?php echo "id=hg".$i;?>>
								<td>
									<?php echo $val['hguide_id'];?>
								</td>
								<td>
									<?php echo $val['name'];?>
									<div <?php echo "id=dvhguide".$i;?> style="display:none;">
										<?php echo "#".$val['hguide_id']."#".$val['name'];?>
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
			<th>OPD File No.</th>
			<td><input type="text" class="span2" id="fileno" onKeyup="tab(this.id,event)"></td>
		</tr>
		<tr style="display:none;">
			<th>Date</th>
			<td>
				<input type="text" id="entry_date" class="datepicker" value="<?php echo date('Y-m-d') ?>">
			</td>
			<th>Time</th>
			<td>
				<input type="text" id="entry_time" class="timepicker" value="<?php echo date('H:i:s') ?>">
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align:center;">
				<br>
				<!--<button class="btn btn-info" id="save" onClick="save_pat_info('save_pat_info')">Save</button>-->
				<button class="btn btn-save" id="save" onClick="save('save_lab_pat_info')"><i class="icon-save"></i> Save</button>
				<input type="hidden" id="patient_id" value="0">
				<input type="hidden" id="opd_id" value="0000">
			</td>
		</tr>
	</table>
<?php
	}
?>
	<input type="hidden" id="cat" value="1@0"/>
	<input type="hidden" id="chk_val2" value="0"/>
</div>
<!-- Loader -->
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<input type="hidden" id="mod_chk" value="0"/>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results"> </div>
				</div>
			</div>
		</div>
	</div>
<script src="../jss/lab_reg.js"></script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
<script type="text/javascript" src="include/ui-1.10.0/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="include/jquery.ui.timepicker.js?v=0.3.3"></script>
<style>
<!--
.alert_msg
{
	position: absolute;
	top: 20%;
	left: 40%;
	color: green;
}-->
#myModal
{
	left: 23%;
	width:95%;
}
.modal.fade.in
{
	top: 3%;
}
.modal-body
{
	max-height: 540px;
}
</style>
<script>
	$(document).ready(function(){
		$("#loader").hide();
		load_dist();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		$('.timepicker').timepicker( {
			showAnim: 'blind'
		} );
		
		if($("#patient_id").val()!=0)
		{
			cal_age_all(event);
		}
	});
	function view_token_list()
	{
		url="pages/view_opd_token_list.php?type=1";
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function view_test_list()
	{
		url="pages/view_test_list.php?type=1";
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function load_dist()
	{
		$.post("pages/lab_reg_ajax.php",
		{
			type:"load_district",
			state:$("#state").val(),
			patient_id:$("#patient_id").val().trim(),
		},
		function(data,status)
		{
			$("#dist_list").html(data);
		})
	}
	function tab(id,e)
	{
		var val=$("#"+id).val();
		var n=val.length;
		if(n>0)
		{
			var numex=/^[A-Za-z0-9 ,./()]+$/;
			if(val[n-1].match(numex))
			{
				
			}
			else
			{
				val=val.slice(0,n-1);
				document.getElementById(id).value=val;
			}	
		}
		
		
		$("#"+id).css('border','');
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(id=="ptype")
			$("#credit").focus();
			if(id=="credit")
			$("#crno").focus();
			if(id=="crno")
			$("#pat_name").focus();
			if(id=="g_name")
			$("#g_ph").focus();
			if(id=="g_ph")
			$("#marital_status").focus();
			if(id=="marital_status")
			$("#income_id").focus();
			if(id=="income_id")
			$("#pin").focus();
			if(id=="pin")
			$("#police").focus();
			if(id=="police")
			$("#state").focus();
			if(id=="state")
			$("#dist").focus();
			if(id=="dist")
			$("#city").focus();
			if(id=="city")
			$("#r_doc").focus();
			if(id=="r_doc")
			$("#fileno").focus();
			if(id=="fileno")
			$("#save").focus();
		}
	}
	function save(typ)
	{
		var opd_id=$("#opd_id").val();
		
		bootbox.hideAll();
		
		if($("#pat_name").val()=="")
		{
			$("#pat_name").css('border','2px solid #ff0000');
			$("#pat_name").focus();
		}
		//~ else if($("#age").val()=="")
		//~ {
			//~ $("#age").css('border','2px solid #ff0000');
			//~ $("#age").focus();
		//~ }
		else if($("#age_y").val()=="")
		{
			$("#age_y").css('border','2px solid #ff0000');
			$("#age_y").focus();
		}
		else if($("#phone").val()=="" || $("#phone").val().length<10)
		{
			$("#phone").css('border','2px solid #ff0000');
			$("#phone").focus();
		}
		//~ else if($("#doc_id").val()=="")
		//~ {
			//~ $("#r_doc").focus();
		//~ }
		//~ else if($("#hguide_id").val()=="")
		//~ {
			//~ $("#hguide").focus();
		//~ }
		else
		{
			$("#loader").show();
			$("#save").hide();
			$("#save").attr("disabled",true);
			var age_type=$("#year").text();
			age_type=age_type.toString();
			$.post("pages/lab_reg_ajax.php",
			{
				typ:typ,
				type:"lab_pat_insert",
				ptype:$("#ptype").val(),
				credit:$("#credit").val(),
				crno:$("#crno").val(),
				name_title:$("#name_title").val(),
				pat_name:$("#pat_name").val(),
				dob:$("#mask-date").val(),
				age:$("#age").val(),
				age_type:age_type,
				sex:$("#sex").val(),
				phone:$("#phone").val(),
				r_doc:$("#doc_id").val(),
				g_name:$("#g_name").val(),
				g_ph:$("#g_ph").val(),
				address:$("#address").val(),
				pin:$("#pin").val(),
				police:$("#police").val(),
				state:$("#state").val(),
				dist:$("#dist").val(),
				city:$("#city").val(),
				fileno:$("#fileno").val(),
				patient_id:$("#patient_id").val(),
				usr:$("#user").text().trim(),
				hguide_id:$("#hguide_id").val(),
				
				patient_type:$("#patient_type").val(),
				g_relation:$("#g_relation").val(),
				marital_status:$("#marital_status").val(),
				income_id:$("#income_id").val(),
				
				esi_ip_no:$("#esi_ip_no").val(),
				post_office:$("#post_office").val(),
				
				entry_date:$("#entry_date").val(),
				entry_time:$("#entry_time").val(),
			},
			function(data,status)
			{
				var param_str="&param_str="+$("#param_id").val()+"&cat="+$("#cat").val().trim();
				
				$("#loader").hide();
				var str=data.split("@@");
				if(str[0]=='0')
				{
					bootbox.dialog({ message: "<b>Saved. Redirecting to Patient Dashboard</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/>"});
					setTimeout(function()
					{
						window.location="processing.php?param=3&uhid="+str[1]+"&lab=1&opd="+opd_id+param_str;
					 }, 800);
				 }else
				 {
					 save(typ);
				 }
			})
		}
	}
	function patient_type_ch(val,e)
	{
		if(val==2)
		{
			//$(".esi_ip_no_sh").show();
		}else
		{
			$(".esi_ip_no_sh").hide();
		}
	}
	function esi_ip_no_up(e)
	{
		if(e.keyCode==13)
		{
			$("#name_title").focus();
		}
	}
</script>
