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
		$ipd=base64_decode($_GET["ipd"]);
		$ipd=trim($ipd);
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
		$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
		$pat_info_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
		
		$pat_card_info=mysqli_fetch_array(mysqli_query($link, " SELECT `card_id` FROM `patient_card_details` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' "));
		$sel_card_id=$pat_card_info["card_id"];
		
		$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' ) "));
		if(!$ref_doc_name)
		{
			$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
		}
		
		$health_guide=mysqli_fetch_array(mysqli_query($link, " SELECT `hguide_id`, `name` FROM `health_guide` WHERE `hguide_id` in ( SELECT `hguide_id` FROM `pat_health_guide` WHERE `patient_id`='$uhid' ) "));
		if($pat_info["dob"]!='')
		{
			$dob=age_calculator($pat_info["dob"]);
			$dob=explode(" ",$dob);
		}else{
			$dob[0]=$pat_info["age"];
			$dob[1]=$pat_info["age_type"];
		}
		$pat_type_data=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd' "));
		$pat_name_str=explode(". ",$pat_info["name"]);
		$pat_name_title=trim($pat_name_str[0]);
		$pat_name=trim($pat_name_str[1]." ".$pat_name_str[2]." ".$pat_name_str[3]." ".$pat_name_str[4]);
?>
	<table id="padd_tbl" class="table">
		<tr>
			<th>Patient Type</th>
			<td colspan="5">
				<select id="patient_type" class="span2" onkeyup="tab(this.id,event)">
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
		</tr>
		<tr style="display:none;">
			<th>Patient Type</th>
			<td colspan="5">
				<select id="pat_type" class="span2" onkeyup="tab(this.id,event)">
				<?php
					$pat_type_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`=15");
					while($pat_type=mysqli_fetch_array($pat_type_qry))
					{
						if($pat_type['p_type_id']==$pat_type_data["type"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$pat_type[p_type_id]' $sel>$pat_type[p_type]</option>";
					}
				?>
				</select>
			</td>
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
		<tr>
			<th>Guardian's Contact</th>
			<td>
				<input type="text" class="span2" id="g_ph" maxlength="10" onkeyup="tab(this.id,event)" value="<?php echo $pat_info_rel["gd_phone"]; ?>">
			</td>
			<th>Marital Status</th>
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
				<select id="dist" onkeyup="tab(this.id,event)">
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
		<tr>
			<th>Referred By</th>
			<td>
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'casualty')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_doc_name["ref_name"]."-".$ref_doc_name["refbydoctorid"]; ?>">
				<input type="text" name="doc_id" id="doc_id" style="display:none;" value="<?php echo $pat_info["refbydoctorid"]; ?>">
				<!--<input type="button" name="new_doc" id="new_doc" value="New" class="btn btn-info btn-mini" onClick="load_new_ref_doc()" />-->
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
			<th style="display:none;">Health Guide</th>
			<td colspan="3" style="display:none;">
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
			<th style="display:none;">Card Type</th>
			<td colspan="" style="display:none;">
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
		<tr>
			<td colspan="6">
				<!--<input type="hidden" id="uhid" value="0">
				<input type="hidden" id="ipd_id_dash" value="0">-->
				<!--<button class="btn btn-info" id="save" onClick="save_pat_info('save_pat_info')">Save</button>-->
				<br>
				<center>
					<button class="btn btn-save" id="save" onClick="save('update_opd_pat_info')"><i class="icon-save"></i> Save</button>
					<input type="hidden" id="patient_id" value="<?php echo $pat_info["patient_id"]; ?>">
					<input type="hidden" id="ipd_val" value="<?php echo $ipd; ?>">
					<input type="hidden" id="uhid" value="0">
					<!--<button class="btn btn-success" id="to_ipd_dashboard" onClick="to_ipd_dashboard()" style="display:none;">Go To IPD Dashboard</button>
					<button class="btn btn-danger" id="new" onClick="window.location.reload()" style="display:none;">New Registration</button>-->
				</center>
			</td>
		</tr>
	</table>
<?php
	}else
	{
?>
	<table id="padd_tbl" class="table table-condensed">
		<tr>
			<td colspan="6">
				<center>
					<h4>Search</h4>
				</center>
			</td>
		</tr>
		<tr>
			<th>UHID</th>
			<td>
				<input type="text" class="span2" id="search_uhid" onkeyup="load_emp(this.value,event,'uhid')" placeholder="Type UHID" >
			</td>
			<th>PIN</th>
			<td>
				<input type="text" class="span2" id="search_pin" onkeyup="load_emp(this.value,event,'pin')" placeholder="Type PIN" >
			</td>
			<th>Name</th>
			<td>
				<input type="text" class="span2" id="search_name" onkeyup="load_emp(this.value,event,'name')" placeholder="Type Name" >
			</td>
			<th>Phone No</th>
			<td>
				<input type="text" class="span2" id="search_phone" onkeyup="load_emp(this.value,event,'phone')" placeholder="Type Phone Numebr" >
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
		<tr>
			<th>Patient Type</th>
			<td colspan="5">
				<select id="patient_type" class="span2" onkeyup="tab(this.id,event)">
				<?php
					$type_qry=mysqli_query($link, " SELECT * FROM `patient_source_master` ORDER BY `source_id` ");
					while($type=mysqli_fetch_array($type_qry))
					{
						echo "<option value='$type[source_id]'>$type[source_type]</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr style="display:none;">
			<th>Registration Type</th>
			<td colspan="5">
				<select id="pat_type" class="span2" onkeyup="tab(this.id,event)">
				<?php
					$pat_type_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`=15 ");
					while($pat_type=mysqli_fetch_array($pat_type_qry))
					{
						echo "<option value='$pat_type[p_type_id]'>$pat_type[p_type]</option>";
					}
				?>
				</select>
			</td>
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
				<input type="text" id="mask-date" class="span2 dob" maxlength="10" placeholder="DD-MM-YYYY" onKeyup="cal_age_dob(this.value,event)">
			</td>
			<th>Age <b style="color:#ff0000;">*</b></th>
			<td>
				<span>
					<input type="text" id="age_y" class="span1" onKeyup="age_y_check(this,event)" placeholder="Years" title="Years">
					<input type="text" id="age_m" class="span1" onKeyup="age_m_check(this,event)" placeholder="Months" title="Months">
					<input type="text" id="age_d" class="span1" onKeyup="age_d_check(this,event)" placeholder="Days" title="Days">
				</span>
				<span style="display:none;">
					<input type="text" id="age" class="span1" onKeyup="age_check(this.value,event)" onBlur="border_color_blur(this.id,this.value)"><text id="year">Years</text>
					<span id="g_id" style="display:none;"><input type="text" id="grdn_name" class="span3" placeholder="Enter Guardian Name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)"></span>
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
			<th>Phone</th>
			<td>
				<input type="text"class="span2" id="phone" maxlength="10" onKeyup="phone_check(this.value,event)">
			</td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Guardian's Name</th>
			<td><input type="text" class="span2" id="g_name" onKeyup="caps_it(this.value,this.id,event)"></td>
			<th>Relation with Guardian</th>
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
		<tr>
			<th>Guardian's Contact</th>
			<td><input type="text" class="span2" id="g_ph" maxlength="10" onkeyup="tab(this.id,event)" /></td>
			<th>Marital Status</th>
			<td>
				<select id="marital_status" class="span2" onkeyup="tab(this.id,event)">
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
				<select id="dist" onkeyup="tab(this.id,event)">
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
		<tr>
			<th>Referred By</th>
			<td>
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'casualty')" onBlur="javascript:$('#ref_doc').fadeOut(500)" />
				<input type="text" name="doc_id" id="doc_id" style="display:none;" />
				<!--<input type="button" name="new_doc" id="new_doc" value="New" class="btn btn-info btn-mini" onClick="load_new_ref_doc()" />-->
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
			<th style="display:none;">Health Guide</th>
			<td style="display:none;">
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
			<th style="display:none;">Card Type</th>
			<td colspan="3" style="display:none;">
				<select class="span2" id="card_id" onkeyup="card_id_lab(this.value,event)">
			<?php
				$card_val_qry=mysqli_query($link, "SELECT `card_id`, `card_name` FROM `card_type_master` ORDER BY `card_id`");
				while($card_val=mysqli_fetch_array($card_val_qry))
				{
					echo "<option value='$card_val[card_id]'>$card_val[card_name]</option>";
				}
			?>
				</select>
			</td>
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
			<td colspan="6">
				<!--<input type="hidden" id="uhid" value="0">
				<input type="hidden" id="ipd_id_dash" value="0">-->
				<!--<button class="btn btn-info" id="save" onClick="save_pat_info('save_pat_info')">Save</button>-->
				<br>
				<center>
					<input type="hidden" id="uhid" value="0">
					<button class="btn btn-save" id="save" onClick="save('save_opd_pat_info')"><i class="icon-save"></i> Save</button>
					<input type="hidden" id="patient_id" value="0">
					<input type="hidden" id="ipd_val" value="0">
					<!--<button class="btn btn-success" id="to_ipd_dashboard" onClick="to_ipd_dashboard()" style="display:none;">Go To IPD Dashboard</button>
					<button class="btn btn-danger" id="new" onClick="window.location.reload()" style="display:none;">New Registration</button>-->
				</center>
			</td>
		</tr>
	</table>
<?php
	}
?>
	<input type="hidden" id="chk_val2" value="0"/>
</div>
<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<input type="hidden" id="mod_chk" value="0"/>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<div class="modal-body">
					<div id="result"> </div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
<script src="../jss/service_reg.js"></script>
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
	left: 25%;
	width:90%;
}
.modal.fade.in
{
	top: 3%;
}
.modal-body
{
	max-height: 500px;
}
</style>
<script>
	$(document).ready(function()
	{
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		$('.timepicker').timepicker( {
			showAnim: 'blind'
		} );
		load_dist();
		
		if($("#patient_id").val()!=0)
		{
			cal_age_all(event);
		}
	});
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
			$("#save").focus();
		}
	}
	function save(typ)
	{
		bootbox.hideAll();
		
		if($("#pat_name").val()=="")
		{
			$("#pat_name").css('border','1px solid #ff0000');
			$("#pat_name").focus();
		}
		else if($("#age_y").val()=="")
		{
			$("#age_y").css('border','2px solid #ff0000');
			$("#age_y").focus();
		}
		//~ else if($("#age").val()=="") // edit
		//~ {
			//~ $("#age").css('border','1px solid #ff0000');
			//~ $("#age").focus();
		//~ }
		else if($("#doc_id").val()=="")
		{
			$("#r_doc").focus();
		}
		//~ else if($("#hguide_id").val()=="")
		//~ {
			//~ $("#hguide").focus();
		//~ }
		else
		{
			var age_type=$("#year").text();
			age_type=age_type.toString();
			$("#save").hide();
			$("#save").attr("disabled",true);
			$.post("pages/casualty_reg_ajax.php",
			{
				typ:typ,
				type:"casualty_pat_insert",
				pat_type:$("#pat_type").val(),
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
				patient_id:$("#patient_id").val().trim(),
				ipd_val:$("#ipd_val").val().trim(),
				uhid:$("#uhid").val().trim(),
				usr:$("#user").text().trim(),
				r_doc:$("#doc_id").val(),
				hguide_id:$("#hguide_id").val(),
				
				patient_type:$("#patient_type").val(),
				g_relation:$("#g_relation").val(),
				marital_status:$("#marital_status").val(),
				income_id:$("#income_id").val(),
				
				card_id:$("#card_id").val(),
				
				entry_date:$("#entry_date").val(),
				entry_time:$("#entry_time").val(),
			},
			function(data,status)
			{
				var vl=data.split("@");
				if(vl[2]==0)
				{
					$("#bed_id").val('');
					//$("#save").show();
					$("#save").attr("disabled",false);
					bootbox.dialog({ message: "<b>Saved. Redirecting to Patient Dashboard</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
					setTimeout(function()
					{
						if(vl[3]==15)
						{
							window.location="processing.php?param=129&uhid="+vl[0]+"&ipd="+vl[1];
						}
					}, 1000);
				}else
				{
					save(typ);
				}
			})
		}
	}
	function load_dist()
	{
		$("#loader").show();
		$.post("pages/casualty_reg_ajax.php",
		{
			type:"load_district",
			state:$("#state").val(),
			patient_id:$("#patient_id").val().trim(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#dist_list").html(data);
		})
	}
	function load_bed_details() // edit
	{
		//$('#foot').hide();
		$("#myModal1").animate({"width":"400px",'margin':'auto'},"fast");
		$.post("pages/ipd_reg_ajax.php",
		{
			type:"load_bed_details",
			usr:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#result").html(data);
			$("#myModal1").animate({'top':'5%','left':'35%',"width":"1000px",'margin':'auto'},"slow");
			$("#mod").click();
			chk_bed_assign();
		})
	}
	function chk_bed_assign()
	{
		setInterval(function()
		{
			$.post("pages/ipd_reg_ajax.php",
			{
				type:"load_bed_details",
				usr:$("#user").text().trim(),
			},
			function(data,status)
			{
				$("#result").html(data);
			})
		},1500);
	}
	function bed_asign(w_id,b_id,w_name,b_no)
	{
		bootbox.confirm("Do you really want to assign bed no "+b_no+" of ward "+w_name+" to this patient?",
	    function(result)
	    { 
			if(result)
			{
				$("#bed_id").val(b_id);
				$("#bedbtn").removeClass('btn-info');
				$("#bedbtn").addClass('btn-success');
				$("#bedbtn").text("Assigned Bed No "+b_no);
				$.post("pages/ipd_reg_ajax.php",
				{
					type:"ipd_bed_asign",
					usr:$("#user").text().trim(),
					w_id:w_id,
					b_id:b_id
				},
				function(data,status)
				{
					$("#bedbtn").focus();
					//load_bed_stat();
				})
			}
		});
	}
	function load_bed_stat()
	{
		$.post("pages/ipd_reg_ajax.php",
		{
			usr:$("#user").text(),
			type:"load_bed_stat",
		},
		function(data,status)
		{
			$("#cl6").html(data);
		})
	}
	function to_ipd_dashboard()
	{
		var uhid=$("#uhid").val().trim();
		var ipd_id_dash=$("#ipd_id_dash").val().trim();
		window.location="processing.php?param=52&uhid="+uhid+"&ipd="+ipd_id_dash;
	}
</script>
