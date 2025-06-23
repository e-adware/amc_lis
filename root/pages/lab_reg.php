<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<!--<h2 class="alert_msg"></h2>-->
<?php
	if($_GET["uhid"])
	{
		$uhid=base64_decode($_GET["uhid"]);
		$uhid=trim($uhid);
		
		$pin=base64_decode($_GET["pin"]);
		$pin=trim($pin);
		
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
		
		$pat_info_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
		
		$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
		if($pat_info["dob"]!='')
		{
			$dob=age_calculator($pat_info["dob"]);
			$dob=explode(" ",$dob);
		}else{
			$dob[0]=$pat_info["age"];
			$dob[1]=$pat_info["age_type"];
		}
		$health_guide=mysqli_fetch_array(mysqli_query($link, " SELECT `hguide_id`, `name` FROM `health_guide` WHERE `hguide_id` in ( SELECT `hguide_id` FROM `pat_health_guide` WHERE `patient_id`='$uhid' ) "));
		$pat_name_str=explode(". ",$pat_info["name"]);
		$pat_name_title=trim($pat_name_str[0]);
		$pat_name=trim($pat_name_str[1]." ".$pat_name_str[2]." ".$pat_name_str[3]." ".$pat_name_str[4]);
?>

	<table class="table">
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
		<tr>
			<th>Phone</th>
			<td colspan="5">
				<input type="text" class="span3" id="phone" maxlength="10" value="<?php echo $pat_info["phone"]; ?>" autocomplete="off" />
			</td>
		</tr>
		<tr>
			<th>Name</th>
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
				<input type="text" class="span3" id="pat_name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" autofocus value="<?php echo $pat_name; ?>">
			</td>
			<th>DOB</th>
			<td>
				<input type="text" id="mask-date" class="span2 dob" maxlength="10" onKeyup="cal_age_dob(this.value,event)" value="<?php echo $pat_info["dob"]; ?>">(DD-MM-YYYY)
			</td>
			<th>Age</th>
			<td>
				<input type="text" id="age" class="span1" onKeyup="age_check(this.value,event)" onBlur="border_color_blur(this.id,this.value)" value="<?php echo $dob[0]; ?>"><text id="year"><?php echo $dob[1]; ?></text>
				<span id="g_id" style="display:none;"><input type="text" id="grdn_name" class="span3" placeholder="Enter Guardian Name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" value="<?php echo $pat_info["gd_name"]; ?>"></span>
			</td>
		</tr>
		<tr>
			<th>Sex</th>
			<td>
				<select id="sex" onKeyup="sex(event)">
					<option value="Male" <?php if($pat_info["sex"]=="Male"){ echo "selected"; } ?> >Male</option>
					<option value="Female" <?php if($pat_info["sex"]=="Female"){ echo "selected"; } ?> >Female</option>
					<option value="Other"  <?php if($pat_info["sex"]=="Other"){ echo "selected"; } ?> >Other</option>
				</select>
			</td>
			<!--<th class="span2">Ref By</th>
			<td>
				<input type="text" name="r_doc" id="r_doc" class="span3" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_doc_name["ref_name"]."-".$pat_info["refbydoctorid"]; ?>" >
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table">
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
			<td colspan="5">
				<select id="sel_center" onKeyup="sel_center(event)">
				<?php
				$center_qry=mysqli_query($link, " SELECT * FROM `centremaster` order by `centrename` ");
				while($center=mysqli_fetch_array($center_qry))
				{
					if($center['centreno']==$pat_info["center_no"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$center[centreno]' $sel >$center[centrename]</option>";
				}
				?>
				</select>
			</td>-->
		</tr>
		<!--<tr>
			<th>Health Guide</th>
			<td colspan="5">
				<input type="text" name="hguide" id="hguide" class="span3" size="25" onFocus="hguide_focus()" onKeyUp="hguide_up(this.value,event)" onBlur="javascript:$('#hguide_div').fadeOut(500)" value="<?php echo $health_guide["name"].'-'.$health_guide["hguide_id"] ?>" />
				<input type="text" name="hguide_id" id="hguide_id" style="display:none;" value="<?php echo $health_guide["hguide_id"] ?>" />
				<div id="hguide_info"></div>
				<div id="hguide_div" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
						<th>Health Guide ID</th>
						<th>Health Guide Name</th>
						<?php
							$q=mysqli_query($link, " SELECT * FROM `health_guide` WHERE `hguide_id`='HG101' ");
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
		</tr>-->
		<tr>
			<td colspan="6">
				<input type="hidden" id="save_type" value="update_pat_info">
				<button class="btn btn-info" id="save" onClick="save_pat_info()">Save</button>
				<input type="hidden" id="patient_id" value="<?php echo $pat_info["patient_id"]; ?>">
				<input type="hidden" id="pin" value="<?php echo $pin; ?>">
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
				<input type="text" id="search_uhid" onkeyup="load_emp(this.value,event,'uhid')" placeholder="Type UHID" >
			</td>
			<th>PIN</th>
			<td>
				<input type="text" id="search_pin" onkeyup="load_emp(this.value,event,'pin')" placeholder="Type PIN" >
			</td>
			<th>Name</th>
			<td>
				<input type="text" id="search_name" onkeyup="load_emp(this.value,event,'name')" placeholder="Type Name" >
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<div id="emp_list" style="max-height:450px;overflow-y:scroll;">
					
				</div>
			</td>
		</tr>
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
		<tr>
			<th>Phone</th>
			<td colspan="5">
				<input type="text" class="span3" id="phone" onKeyup="phone_check(this.value,event)" onBlur="load_pat_detail_by_phone(this.value)" maxlength="10" autofocus autocomplete="off" />
			</td>
		</tr>
		<tr>
			<th>Name</th>
			<td>
				<select id="name_title" onChange="name_title_ch(this.value)" onKeyup="name_title_up(this.value,event)" class="span1" >
				<?php
					$title_qry=mysqli_query($link, " SELECT * FROM `name_title` ORDER BY `title_id` ");
					while($val=mysqli_fetch_array($title_qry))
					{
						echo "<option value='$val[title]'>$val[title]</option>";
					}
				?>
				</select>
				<input type="text" class="span2 all_info" id="pat_name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" />
			</td>
			<th>DOB (DD-MM-YYYY)</th>
			<td>
				<input type="text" id="mask-date" class="span2 dob all_info" maxlength="10" placeholder="DD-MM-YYYY" onKeyup="cal_age_dob(this.value,event)">
			</td>
			<th>Age</th>
			<td>
				<input type="text" id="age" class="span1 all_info" onKeyup="age_check(this.value,event)" onBlur="border_color_blur(this.id,this.value)"><text id="year">Years</text>
				<span id="g_id" style="display:none;"><input type="text" id="grdn_name" class="span3" placeholder="Enter Guardian Name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)"></span>
			</td>
		</tr>
		<tr>
			<th>Sex</th>
			<td>
				<select id="sex" class="span2 all_info" onKeyup="sex(event)">
					<option value="Male">Male</option>
					<option value="Female">Female</option>
					<option value="Other">Other</option>
				</select>
			</td>
			<!--<th>Referred By</th>
			<td colspan="1">
				<input type="text" name="r_doc" id="r_doc" class="span2" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" />
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
			<td colspan="">
				<select id="sel_center" onKeyup="sel_center(event)">
				<?php
				$center_qry=mysqli_query($link, " SELECT * FROM `centremaster` order by `centrename` ");
				while($center=mysqli_fetch_array($center_qry))
				{
					if($center['centreno']==$pat_info["center_no"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$center[centreno]' $sel >$center[centrename]</option>";
				}
				?>
				</select>
			</td>-->
		</tr>
		<!--<tr>
			<th>Health Guide</th>
			<td colspan="5">
				<input type="text" name="hguide" id="hguide" class="span3" size="25" onFocus="hguide_focus()" onKeyUp="hguide_up(this.value,event)" onBlur="javascript:$('#hguide_div').fadeOut(500)" />
				<input type="text" name="hguide_id" id="hguide_id" style="display:none;" />
				<div id="hguide_info"></div>
				<div id="hguide_div" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="300px">
						<th>Health Guide ID</th>
						<th>Health Guide Name</th>
						<?php
							$q=mysqli_query($link, " SELECT * FROM `health_guide` WHERE `hguide_id`='HG101' ");
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
		</tr>-->
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
				<center>
					<input type="hidden" id="save_type" value="save_pat_info">
					<button class="btn btn-info" id="save" onClick="save_pat_info()">Save</button>
					<input type="hidden" id="patient_id" value="0">
					<input type="hidden" id="pin" value="0000">
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
<script src="../jss/pat_regd_lab.js"></script>
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
#ref_doc
{
	width:450px;
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
	});
</script>
