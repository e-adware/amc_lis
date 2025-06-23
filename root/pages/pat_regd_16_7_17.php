<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Patient Registration Form</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<!--<h2 class="alert_msg"></h2>-->
<?php
	if($_GET["uhid"])
	{
		$uhid=base64_decode($_GET["uhid"]);
		$uhid=(int)$uhid;
		$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
		$ref_doc_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
		if($pat_info["dob"]!='')
		{
			$dob=age_calculator($pat_info["dob"]);
			$dob=explode(" ",$dob);
		}else{
			$dob[0]=$pat_info["age"];
			$dob[1]=$pat_info["age_type"];
		}
?>
	<table class="table">
		<tr>
			<th>Name</th>
			<td>
				<input type="text" class="span3" id="pat_name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" autofocus value="<?php echo $pat_info["name"]; ?>">
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
			<th>Phone</th>
			<td>
				<input type="text"class="span3" id="phone" maxlength="10" onKeyup="phone_check(this.value,event)" value="<?php echo $pat_info["phone"]; ?>">
			</td>
			<th>Address</th>
			<td>
				<input type="text" class="span3" id="address" onKeyup="caps_it(this.value,this.id,event)" value="<?php echo $pat_info["address"]; ?>">
			</td>
		</tr>
		<tr>
			<th>Email</th>
			<td>
				<input type="text" class="span3" id="email" onKeyup="email_check(this.value,event)" onBlur="border_color_blur(this.id,this.value)" value="<?php echo $pat_info["email"]; ?>">
			</td>
			<th class="span2">Ref By</th>
			<td>
				<input type="text" name="r_doc" id="r_doc" class="span3" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_doc_name["ref_name"]."-".$pat_info["refbydoctorid"]; ?>" >
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
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
			</td>
			<!--<th>Payment Mode</th>
			<td>
				<select id="payment_mode" onKeyup="payment_mode(event)">
					<option value="Cash" <?php if($pat_info["payment_mode"]=="Cash"){ echo "selected"; } ?> >Cash</option>
					<option value="Credit" <?php if($pat_info["payment_mode"]=="Credit"){ echo "selected"; } ?> >Credit</option>
					<option value="TPA" <?php if($pat_info["payment_mode"]=="TPA"){ echo "selected"; } ?> >TPA</option>
				</select>
			</td>-->
			<th>Blood Group</th>
			<td>
				<select id="blood_group" onKeyup="blood_group(event)">
					<option value="">Select</option>
					<option value="O Positive" <?php if($pat_info["blood_group"]=="O Positive"){ echo "selected"; } ?> >O Positive</option>
					<option value="O Negative" <?php if($pat_info["blood_group"]=="O Negative"){ echo "selected"; } ?> >O Negative</option>
					<option value="A Positive"> <?php if($pat_info["blood_group"]=="A Positive"){ echo "selected"; } ?> A Positive</option>
					<option value="A Negative"> <?php if($pat_info["blood_group"]=="A Negative"){ echo "selected"; } ?> A Negative</option>
					<option value="B Positive"> <?php if($pat_info["blood_group"]=="B Positive"){ echo "selected"; } ?> B Positive</option>
					<option value="B Negative"> <?php if($pat_info["blood_group"]=="B Negative"){ echo "selected"; } ?> B Negative</option>
					<option value="AB Positive"> <?php if($pat_info["blood_group"]=="AB Positive"){ echo "selected"; } ?> AB Positive</option>
					<option value="AB Negative"> <?php if($pat_info["blood_group"]=="AB Negative"){ echo "selected"; } ?> AB Negative</option>
				</select>
			</td>
		</tr>
		<!--<tr>
			
			<th>Regd Fees</th>
			<td colspan="4">
				<input type="text" id="regd_fee" value="100" readonly>
			</td>
		</tr>-->
		<tr>
			<td colspan="6">
				<button class="btn btn-info" id="save" onClick="save_pat_info('update_pat_info')">Save</button>
				<input type="hidden" id="patient_id" value="<?php echo $pat_info["patient_id"]; ?>">
			</td>
		</tr>
	</table>
<?php
	}else
	{
?>
	<table class="table">
		<tr>
			<th>Name</th>
			<td>
				<input type="text" class="span3" id="pat_name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)" autofocus>
			</td>
			<th>DOB</th>
			<td>
				<input type="text" id="mask-date" class="span2 dob" maxlength="10" onKeyup="cal_age_dob(this.value,event)">(DD-MM-YYYY)
			</td>
			<th>Age</th>
			<td>
				<input type="text" id="age" class="span1" onKeyup="age_check(this.value,event)" onBlur="border_color_blur(this.id,this.value)"><text id="year">Years</text>
				<span id="g_id" style="display:none;"><input type="text" id="grdn_name" class="span3" placeholder="Enter Guardian Name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)"></span>
			</td>
		</tr>
		<tr>
			<th>Sex</th>
			<td>
				<select id="sex" onKeyup="sex(event)">
					<option value="Male">Male</option>
					<option value="Female">Female</option>
					<option value="Other">Other</option>
				</select>
			</td>
			<th>Phone</th>
			<td>
				<input type="text"class="span3" id="phone" maxlength="10" onKeyup="phone_check(this.value,event)">
			</td>
			<th>Address</th>
			<td>
				<input type="text" class="span3" id="address" onKeyup="caps_it(this.value,this.id,event)">
			</td>
		</tr>
		<tr>
			<th>Email</th>
			<td>
				<input type="text" class="span3" id="email" onKeyup="email_check(this.value,event)" onBlur="border_color_blur(this.id,this.value)">
			</td>
			<th class="span2">Ref By</th>
			<td>
				<input type="text" name="r_doc" id="r_doc" class="span3" size="25" onFocus="load_refdoc1()" onKeyUp="load_refdoc(this.value,event)" onBlur="javascript:$('#ref_doc').fadeOut(500)" />
				<div id="doc_info"></div>
				<div id="ref_doc" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
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
			</td>
			<!--<th>Payment Mode</th>
			<td>
				<select id="payment_mode" onKeyup="payment_mode(event)">
					<option value="Cash">Cash</option>
					<option value="Credit">Credit</option>
					<option value="TPA">TPA</option>
				</select>
			</td>-->
			<th>Blood Group</th>
			<td colspan="5">
				<select id="blood_group" onKeyup="blood_group(event)">
					<option value="">Select</option>
					<option value="O Positive">O Positive</option>
					<option value="O Negative">O Negative</option>
					<option value="A Positive">A Positive</option>
					<option value="A Negative">A Negative</option>
					<option value="B Positive">B Positive</option>
					<option value="B Negative">B Negative</option>
					<option value="AB Positive">AB Positive</option>
					<option value="AB Negative">AB Negative</option>
				</select>
			</td>
		</tr>
		<!--<tr>
			
			<!--<th>Regd Fees</th>
			<td colspan="4">
				<input type="text" id="regd_fee" value="100" readonly>
			</td>
		</tr>-->
		<tr>
			<td colspan="6">
				<button class="btn btn-info" id="save" onClick="save_pat_info('save_pat_info')">Save</button>
			</td>
		</tr>
	</table>
<?php
	}
?>
	<input type="hidden" id="chk_val2" value="0"/>
</div>
<script src="../jss/pat_regd.js"></script>
<style>
<!--
.alert_msg
{
	position: absolute;
	top: 20%;
	left: 40%;
	color: green;
}-->
</style>
