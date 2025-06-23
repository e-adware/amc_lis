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
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
		$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
		$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
		if($pat_info["dob"]!='')
		{
			$dob=age_calculator($pat_info["dob"]);
			$dob=explode(" ",$dob);
		}else{
			$dob[0]=$pat_info["age"];
			$dob[1]=$pat_info["age_type"];
		}
?>
	<table id="padd_tbl" class="table table-condensed">
		<tr>
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
			<td><input type="text" class="span1" id="crno" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'');tab(this.id,event)" value="<?php echo $pat_info_rel["crno"]; ?>" ></td>
		</tr>
		<tr>
			<th>Name <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" class="span2" id="pat_name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" value="<?php echo $pat_info["name"]; ?>" >
			</td>
			<th>DOB (DD-MM-YYYY) </th>
			<td>
				<input type="text" id="mask-date" class="span2 dob" maxlength="10" placeholder="DD-MM-YYYY" onKeyup="cal_age_dob(this.value,event);tab(this.id,event)" value="<?php echo $pat_info["dob"]; ?>">
			</td>
			<th>Age <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" id="age" class="span1" onKeyup="age_check(this.value,event);tab(this.id,event)" onBlur="border_color_blur(this.id,this.value)" value="<?php echo $pat_info["age"]; ?>">
				<text id="year"><?php echo $pat_info["age_type"]; ?></text>
				<span id="g_id" style="display:none;"><input type="text" id="grdn_name" class="span3" placeholder="Enter Guardian Name" onKeyup="caps_it(this.value,this.id,event);tab(this.id,event)" onBlur="border_color_blur(this.id,this.value)"></span>
			</td>
		</tr>
		<tr>
			<th>Sex <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="sex" class="span2" onKeyup="sex(event);tab(this.id,event)">
					<option value="Male" <?php if($pat_info["sex"]=="Male"){ echo "selected"; } ?>>Male</option>
					<option value="Female" <?php if($pat_info["sex"]=="Female"){ echo "selected"; } ?>>Female</option>
					<option value="Other" <?php if($pat_info["sex"]=="Other"){ echo "selected"; } ?>>Other</option>
				</select>
			</td>
			<th>Phone</th>
			<td>
				<input type="text"class="span2" id="phone" maxlength="10" onKeyup="phone_check(this.value,event);tab(this.id,event)" value="<?php echo $pat_info["phone"]; ?>">
			</td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Guardian's Name</th>
			<td>
				<input type="text" class="span2" id="g_name" onKeyup="caps_it(this.value,this.id,event);tab(this.id,event)" value="<?php echo $pat_info["gd_name"]; ?>">
			</td>
			<th>Guardian's Contact</th>
			<td>
				<input type="text" class="span2" id="g_ph" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'');tab(this.id,event)" value="<?php echo $pat_info_rel["gd_phone"]; ?>">
			</td>
			<th>Address</th>
			<td>
				<textarea id="address" class="form-control" placeholder="Address" style="resize:none;" onkeyup="tab(this.id,event)"><?php echo $pat_info["address"]; ?></textarea>
			</td>
		</tr>
		<tr>
			<th>Pin Code</th>
			<td>
				<input type="text" class="span2" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'');tab(this.id,event)" id="pin" value="<?php echo $pat_info_rel["pin"]; ?>">
			</td>
			<th>Police Station</th>
			<td>
				<input type="text" class="span2" id="police" onkeyup="tab(this.id,event)" value="<?php echo $pat_info_rel["police"]; ?>" list="police_datalist">
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
				<input type="text" class="span2" onkeyup="if (/\w/.test(this.value))this.value = this.value.replace(/[^A-Za-z ]/,'');tab(this.id,event)" id="city" value="<?php echo $pat_info_rel["city"]; ?>" list="city_datalist">
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
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Referred By</th>
			<td>
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_doc_name["ref_name"]."-".$pat_info["refbydoctorid"]; ?>">
				<input type="text" name="doc_id" id="doc_id" style="display:none;" value="<?php echo $pat_info["refbydoctorid"]; ?>">
				<input type="button" name="new_doc" id="new_doc" value="New" class="btn btn-info btn-mini" onClick="load_new_ref_doc()" />
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
						<th>Doctor ID</th>
						<th>Doctor Name</th>
						<?php
							$d=mysqli_query($GLOBALS["___mysqli_ston"], "select * from refbydoctor_master where refbydoctorid='937' order by ref_name");
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
				<!--<input type="text" class="span3" id="address" onKeyup="caps_it(this.value,this.id,event)">-->
			</td>
			<th>OPD File No.</th>
			<td><input type="text" class="span2" id="fileno" onKeyup="tab(this.id,event)" value="<?php echo $pat_info_rel["file_no"]; ?>"></td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<td colspan="6">
				<button class="btn btn-info" id="save" onClick="save('update_opd_pat_info')">Save</button>
				<input type="hidden" id="patient_id" value="<?php echo $pat_info["patient_id"]; ?>">
				<input type="hidden" id="uhid" value="0"> <!-- For Same Patient -->
			</td>
		</tr>
	</table>
<?php
	}else
	{
?>
	<table id="padd_tbl" class="table table-condensed">
		<tr>
			<td colspan="8">
				<center>
					<h4>Search</h4>
				</center>
			</td>
		</tr>
		<tr>
			<th>UHID</th>
			<td>
				<input type="text" id="search_uhid" onkeyup="load_emp(this.value,event,'uhid')" placeholder="Type UHID" >
			</td>
<!--
			<th>PIN</th>
			<td>
				<input type="text" id="search_pin" onkeyup="load_emp(this.value,event,'pin')" placeholder="Type PIN" >
			</td>
-->
			<th>Name</th>
			<td>
				<input type="text" id="search_name" onkeyup="load_emp(this.value,event,'name')" placeholder="Type Name" >
			</td>
			<th>Phone</th>
			<td>
				<input type="text" id="search_phone" onkeyup="load_emp(this.value,event,'phone')" placeholder="Type Phone No." >
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
			<td><input type="text" class="span1" id="crno" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'');tab(this.id,event)" /></td>
		</tr>
		<tr>
			<th>Name <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" class="span2" id="pat_name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" autofocus />
			</td>
			<th>DOB (DD-MM-YYYY) </th>
			<td>
				<input type="text" id="mask-date" class="span2 dob" maxlength="10" placeholder="DD-MM-YYYY" onKeyup="cal_age_dob(this.value,event);">
			</td>
			<th>Age <b style="color:#ff0000;">*</b></th>
			<td>
				<input type="text" id="age" class="span1" onKeyup="age_check(this.value,event);tab(this.id,event)" onBlur="border_color_blur(this.id,this.value)"><text id="year">Years</text>
				<span id="g_id" style="display:none;"><input type="text" id="grdn_name" class="span3" placeholder="Enter Guardian Name" onKeyup="caps_it(this.value,this.id,event);tab(this.id,event)" onBlur="border_color_blur(this.id,this.value)"></span>
			</td>
		</tr>
		<tr>
			<th>Sex <b style="color:#ff0000;">*</b></th>
			<td>
				<select id="sex" class="span2" onKeyup="sex(event);tab(this.id,event)">
					<option value="Male">Male</option>
					<option value="Female">Female</option>
					<option value="Other">Other</option>
				</select>
			</td>
			<th>Phone</th>
			<td>
				<input type="text"class="span2" id="phone" maxlength="10" onKeyup="phone_check(this.value,event);tab(this.id,event)">
			</td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Guardian's Name</th>
			<td><input type="text" class="span2" id="g_name" onKeyup="caps_it(this.value,this.id,event);tab(this.id,event)"></td>
			<th>Guardian's Contact</th>
			<td><input type="text" class="span2" id="g_ph" maxlength="10" onkeyup="if(/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'');tab(this.id,event)" /></td>
			<!--<th>Address</th>
			<td><textarea id="address" class="form-control" placeholder="Address" style="resize:none;" onkeyup="tab(this.id,event)"></textarea></td>-->
		</tr>
		<tr>
			<th>Pin Code</th>
			<td><input type="text" class="span2" onkeyup="pin(this.value,event)" id="pin" /></td>
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
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Referred By</th>
			<td>
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event,'opd')" onBlur="javascript:$('#ref_doc').fadeOut(500)" />
				<input type="text" name="doc_id" id="doc_id" style="display:none;" />
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
			<th style="display:none;">Health Guide</th>
			<td style="display:none;">
				<input type="text" name="hguide" id="hguide" class="span2" size="25" onFocus="hguide_focus()" onKeyUp="hguide_up(this.value,event)" onBlur="javascript:$('#hguide_div').fadeOut(500)" />
				<input type="text" name="hguide_id" id="hguide_id" value="HG101" style="display:none;" />
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
		<tr>
			<th>Select Department</th>
			<td>
				<select id="dept_id" onChange="dept_sel()" onKeyUp="dept_sel_Up(event)">
					<option value="0">Select</option>
				<?php
				$dept_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `doctor_specialist_list` order by `name` ");
				while($dept=mysqli_fetch_array($dept_qry))
				{
					echo "<option value='$dept[speciality_id]'>$dept[name]</option>";
				}
				?>
				</select>
			</td>
			<th>Select Doctor</th>
			<td>
				<input type="text" name="ad_doc" id="ad_doc" class="span2 ad_doc" size="25" onFocus="adload_refdoc1()" onKeyUp="adload_refdoc(this.value,event)" onBlur="javascript:$('#adref_doc').fadeOut(500)" />
				<input type="text" name="addoc_id" id="addoc_id" style="display:none;" />
				<div id="addoc_info"></div>
				<div id="adref_doc" align="center">
					<b>Select Department</b>
				</div>
			</td>
			<th>Select Date</th>
			<td>
				<input class="datepicker" type="text" id="appoint_date" onKeyUp="appoint_date(event)" >
			</td>
		</tr>
		<tr>
			<th>Regd Fee</th>
			<td id="regd_fee_adv">0.</td>
			<th>Consultant Fee</th>
			<td id="consult_fee_adv">0</td>
			<th>Total Fee</th>
			<td id="total_fee_adv">0</td>
		</tr>
		<tr>
			<td colspan="6">
				<br>
				<center>
				<!--<button class="btn btn-info" id="save" onClick="save_pat_info('save_pat_info')">Save</button>-->
					<button class="btn btn-info" id="save" onClick="save('save_opd_pat_info')">Save</button>
					<button class="btn btn-success" id="new" onClick="window.location.reload(true)">New</button>
					<input type="hidden" id="patient_id" value="0"> <!-- For Update -->
					<input type="hidden" id="uhid" value="0"> <!-- For Same Patient like re-visit -->
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
				<div class="modal-body">
					<div id="results"> </div>
				</div>
			</div>
		</div>
	</div>
<script src="../jss/opd_reg_adv.js"></script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<script>
	$(document).ready(function(){
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
		load_dist();
	});
	function load_dist()
	{
		$.post("pages/opd_reg_ajax.php",
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
			//$("#address").focus();
			//if(id=="address")
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
			$("#dept_id").focus();
			//$("#save").focus();
		}
	}
	function save(typ)
	{
		if($("#pat_name").val()=="")
		{
			$("#pat_name").css('border','1px solid #ff0000');
			$("#pat_name").focus();
		}
		else if($("#age").val()=="")
		{
			$("#age").css('border','1px solid #ff0000');
			$("#age").focus();
		}
		else if($("#doc_id").val()=="")
		{
			$("#r_doc").focus();
		}
		else if($("#hguide_id").val()=="")
		{
			$("#hguide").focus();
		}
		else if($("#dept_id").val()=="0")
		{
			$("#dept_id").focus();
		}
		else if($("#addoc_id").val()=="")
		{
			$("#ad_doc").focus();
		}
		else if($("#appoint_date").val()=="")
		{
			$("#appoint_date").focus();
		}
		else
		{
			$("#save").attr("disabled",true);
			var age_type=$("#year").text();
			age_type=age_type.toString();
			$.post("pages/opd_advnc_book_ajax.php",
			{
				typ:typ,
				type:"opd_advance_book_save",
				ptype:$("#ptype").val(),
				credit:$("#credit").val(),
				crno:$("#crno").val(),
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
				dept_id:$("#dept_id").val(),
				addoc_id:$("#addoc_id").val(),
				appoint_date:$("#appoint_date").val(),
				patient_id:$("#patient_id").val(),
				uhid:$("#uhid").val(),
				usr:$("#user").text().trim(),
				hguide_id:$("#hguide_id").val(),
			},
			function(data,status)
			{
				//bootbox.dialog({ message: "Saved. Redirecting to Patient Dashboard"});
				bootbox.dialog({ message: "<b>Saved</b>"});
				setTimeout(function(){
					bootbox.hideAll();
					$("#save").hide();
				},2000);
			})
		}
	}
</script>
<style>
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
