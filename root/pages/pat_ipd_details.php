<?php
include("../../includes/connection.php");

$type=$_POST[type];

function convert_date($date)
{
	 if($date)
	 {
		 $timestamp = strtotime($date); 
		 $new_date = date('d-M-Y', $timestamp);
		 return $new_date;
	 }
}


if($type==1)  /*----- Add button on IPD section----------*/
{
	?>
		<input type="button" class="btn btn-info"  value="Add New IPD Details" onclick="add_ipd_form()"/>
	<?php
}
else if($type==2)
{
	$uhid=$_POST['uhid'];
	$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$uhid'"));
	?>
	<label>Enter More Details</label>
	<b>Note (<i style="color:#ee0000;">*</i>) Mandatory</b>
	
	<input type="hidden" id="ipd_id"/>
	<table class="table table-condensed">
		<tr>
			<th width="40%">Patient Occupation <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="Occupation" id="occup" /></th>
		</tr>
		<tr>
			<th>Patient Address Type <i style="color:#ee0000;">*</i></th>
			<th>
				<select id="add_typ" onchange="clrr(this.id)">
					<option value="0">Select</option>
					<option value="1">Current</option>
					<option value="2">Permanent</option>
					<option value="3">Previous</option>
				</select>
			</th>
		</tr>
		<tr>
			<th>Address Line 1 <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="Address Line 1" id="add_1" />
		</tr>
		<tr>
			<th>Address Line 2</th>
			<th><input type="text" class="txt" placeholder="Address Line 2" id="add_2"  />
		</tr>
		<tr>
			<th>City / Town <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="City / Town" id="city"/></th>
		</tr>
		<tr>
			<th>State <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" value="Assam" placeholder="State" id="state"/></th>
		</tr>
		<tr>
			<th>Postal Code <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');clrr(this.id)" placeholder="Postal Code" id="postal"/></th>
		</tr>
		<tr>
			<th>Country <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="Country" id="country" value="India"/></th>
		</tr>
		<tr>
			<th>Phone Type <i style="color:#ee0000;">*</i></th>
			<th>
				<select id="phone_typ" onchange="clrr(this.id)">
					<option value="0">Select</option>
					<option value="1">Landline</option>
					<option value="2">Mobile</option>
					<option value="3">Neighbour</option>
					<option value="4">Relation</option>
					<option value="5">Neighbour Mobile</option>
					<option value="6">Relation Mobile</option>
				</select>
			</th>
		</tr>
		<tr>
			<th>Phone Number <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');clrr(this.id)" placeholder="Phone Number" id="phone" /></th>
		</tr>
		<tr>
			<th>Email Id</th>
			<th><input type="text" class="txt" placeholder="Email Id" id="email" /></th>
		</tr>
		<tr>
			<td colspan="2" style="background:#dddddd;"></td>
		</tr>
		<tr>
			<th>Emergency Contact Person Name <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="Contact Person" id="con_name" /></th>
		</tr>
		<tr>
			<th>Emergency Contact Person Relationship <i style="color:#ee0000;">*</i></th>
			<th>
				<select id="con_rel" onchange="clrr(this.id)">
					<option value="Spouse">Spouse</option>
					<option value="Parent">Parent</option>
					<option value="Child">Child</option>
					<option value="Partner">Partner</option>
					<option value="Cousin">Cousin</option>
					<option value="Friend">Friend</option>
					<option value="Neighbour">Neighbour</option>
					<option value="Other">Other</option>
				</select>
			</th>
		</tr>
		<tr>
			<th>Contact Person Address Type <i style="color:#ee0000;">*</i></th>
			<th>
				<select id="con_add_type" onchange="clrr(this.id)">
					<option value="0">Select</option>
					<option value="1">Current</option>
					<option value="2">Permanent</option>
					<option value="3">Previous</option>
				</select>
			</th>
		</tr>
		<tr>
			<th>Contact Person Address Line 1 <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="Address Line 1" id="con_add_1" />
		</tr>
		<tr>
			<th>Contact Person Address Line 2</th>
			<th><input type="text" class="txt" placeholder="Address Line 2" id="con_add_2"  />
		</tr>
		<tr>
			<th>Contact Person City / Town <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="City / Town" id="con_city"/></th>
		</tr>
		<tr>
			<th>Contact Person State <i style="color:#ee0000;">*</i></th>
			<th>
				<select id="con_state" onchange="con_state();clrr(this.id)">
					<option value="Assam">Assam</option>
					<option value="Other">Other</option>
				</select>
				<input type="text" class="txt" onkeyup="clrr(this.id)" id="pstate" placeholder="State" style="display:none;" />
			</th>
		</tr>
		<tr>
			<th>Contact Person Postal Code <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');clrr(this.id)" placeholder="Postal Code" id="con_postal"/></th>
		</tr>
		<tr>
			<th>Contact Person Country <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="Country" id="con_country" value="India"/></th>
		</tr>
		<tr>
			<th>Contact Person Phone Number <i style="color:#ee0000;">*</i></th>
			<th><input type="text" class="txt" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');clrr(this.id)" placeholder="Phone Number" id="con_phone" /></th>
		</tr>
		<tr>
			<th>Contact Person Email Id</th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="Email Id" id="con_email" /></th>
		</tr>
		<tr>
			<td colspan="2" style="background:#dddddd;"></td>
		</tr>
		<tr>
			<th>Insurance Status <i style="color:#ee0000;">*</i></th>
			<th>
				<select onchange="insurance();clrr(this.id)" id="insurance">
					<option value="0">Select</option>
					<option value="1">Insured</option>
					<option value="2">Uninsured</option>
				</select>
			</th>
		</tr>
		<tr style="display:none;" id="ins_det">
			<th>Insurance Id</th>
			<th><input type="text" class="txt" onkeyup="clrr(this.id)" placeholder="Insurance Id" id="ins_id" /></th>
		</tr>
		<tr>
			<th>Encounter Type <i style="color:#ee0000;">*</i></th>
			<th>
				<select id="enc_typ" onchange="clrr(this.id)">
					<option value="1">Inpatient</option>
					<option value="2">Outpatient</option>
					<option value="3">Emergency</option>
					<option value="4">Investigation</option>
				</select>
			</th>
		</tr>
		<tr>
			<td colspan="2" style="background:#dddddd;"></td>
		</tr>
	<!--</table>
	
	<br/>
	
	<table class="table table-condensed">-->
		<!--<tr>
			<th>Relationship</th>
			<th width="70%" id="relation_info">
				<div class="relation_div">
				<select id="relation1" class="relation" onchange="load_det(this.value,1)">
					<option>Self</option>
					<option>Father</option>
					<option>Mother</option>
					<option>Son</option>
					<option>Daughter</option>
					<option>Wife</option>
					<option>Husband</option>
					<option>Brother</option>
					<option>Sister</option>
					<option>Other</option>
				</select>
				<div id="relation_details1" style="display:none"></div>
				
				<span class="btn btn-info" onclick="add_relation()"><i>Add More</i></span>
				
				</div>
				
			</th>
		</tr>
		<tr>
			<th>
				Emergency Contact
			</th>
			<th id="em_contact">
				Name  : <input type="text" id="emer_name"/> <br/>
				Phone : <input type="text" id="emer_phone"/> 
			</th>
		</tr>
		<tr>
			<th>Chief Complain</th>
			<th><textarea id="chf_complain" style="height:50px;width:500px"></textarea></th>
		</tr>-->
		<tr>
			<th>Attending Doctor <i style="color:#ee0000;">*</i></th>
			<th>
				<select id="attend_doctor" onchange="clrr(this.id)">
					<option value="0">--Select--</option>
					<?php
						$at_doc=mysqli_query($link,"select * from consultant_doctor_master order by Name");
						while($at=mysqli_fetch_array($at_doc))
						{
							echo "<option value='$at[consultantdoctorid]'>$at[Name]</option>";
						}
					?>
				</select>
			</th>
		</tr>
		<tr>
			<th>Admitting Doctor <i style="color:#ee0000;">*</i></th>
			<th>
				<select id="admit_doctor" onchange="clrr(this.id)">
					<option value="0">--Select--</option>
					<?php
						$ad_doc=mysqli_query($link,"select * from consultant_doctor_master order by Name");
						while($ad=mysqli_fetch_array($ad_doc))
						{
							echo "<option value='$ad[consultantdoctorid]'>$ad[Name]</option>";
						}
					?>
				</select>
			</th>
		</tr>
		<!--
		<tr>
			<th>Contact Number</th>
			<th><input type="number" class="contact_no"/> <span class="btn" onclick="add_phone()"><i>Add More</i></span> </th>
		</tr>
		-->
		<tr>
			<th>Assign Bed <i style="color:#ee0000;">*</i></th>
			<th>
				<span id="bed_info"></span>
				<span class="btn btn-info" id="bed_btn" onclick="load_bed_details()"><i>Click to view</i></span>
			</th>
		</tr>
		<tr>
			<th colspan="2" style="text-align:center">
				<input type="button" value="Save" id="ipd_save" class="btn btn-info btn-large" onclick="save_ipd_details(this.value)"/>
				<input type="button" value="Cancel" id="ipd_cancel" class="btn btn-danger btn-large" onclick="ipd_cancel()"/>
			</th>
		</tr>
	</table>
	<style>
		.txt
		{width:300px;}
	</style>
	<?php
}
else if($type==3)
{
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
					echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$rd[bed_no]</span>";
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
					echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$rd[bed_no]</span>";
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
	?>
	
	
	<?php
}
else if($type==4)
{
	$uhid=$_POST[uhid];
	$w_id=$_POST[w_id];
	$b_id=$_POST[b_id];
	
	mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid'");
	mysqli_query($link,"insert into ipd_bed_details_temp(patient_id,ward_id,bed_id) values('$uhid','$w_id','$b_id')");
}
else if($type==5)
{
	$uhid=$_POST['uhid'];
	$occup=$_POST['occup'];
	$add_typ=$_POST['add_typ'];
	$add_1=mysqli_real_escape_string($link,$_POST['add_1']);
	$add_2=mysqli_real_escape_string($link,$_POST['add_2']);
	$city=mysqli_real_escape_string($link,$_POST['city']);
	$state=mysqli_real_escape_string($link,$_POST['state']);
	$zip=mysqli_real_escape_string($link,$_POST['postal']);
	$country=mysqli_real_escape_string($link,$_POST['country']);
	
	$phone_typ=$_POST['phone_typ'];
	$phone=$_POST['phone'];
	$email=$_POST['email'];
	
	$con_name=$_POST['con_name'];
	$con_rel=$_POST['con_rel'];
	$con_add_type=$_POST['con_add_type'];
	$con_add_1=$_POST['con_add_1'];
	$con_add_2=$_POST['con_add_2'];
	$con_city=$_POST['con_city'];
	$con_state=$_POST['con_state'];
	$pstate=$_POST['pstate'];
	$con_postal=$_POST['con_postal'];
	$con_country=$_POST['con_country'];
	$con_phone=$_POST['con_phone'];
	$con_email=$_POST['con_email'];
	
	$insurance=$_POST['insurance'];
	$ins_id=$_POST['ins_id'];
	$enc_typ=$_POST['enc_typ'];
	$attend_doc=$_POST['attend_doc'];
	$admit_doc=$_POST['admit_doc'];
	
	$ward_id=$_POST['ward_id'];
	$bed_id=$_POST['bed_id'];
	$val=$_POST['val'];
	
	if($val=="Save")
	{
		$user=$_POST['user'];
		$date=date("Y-m-d");
		$time=date('H:i:s');
		
		$npid=1;
		$p_date=explode("-",$date);
		$p_date1=$p_date[0]."-".$p_date[1];
				
		$pid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select count(*) as tot from ipd_pat_details where date like '%$p_date1%'"));
		if($pid[tot]>0)
		{
			$npid=$pid[tot]+1;	
		}
		
		$p_date2=date("y-m-d");
		$p_date3=explode("-",$p_date2);
		$ipd_id="IPD".$npid.$p_date3[1].$p_date3[0].$user;
		$ipd_id=trim($ipd_id);
		
		mysqli_query($link,"insert into ipd_pat_details(patient_id,ipd_id,user,time,date) values('$uhid','$ipd_id','$user','$time','$date')");
		mysqli_query($link,"INSERT INTO `ipd_pat_info`(`patient_id`, `ipd_id`, `occupation`, `address_type`, `add_1`, `add_2`, `city`, `state`, `zip`, `country`, `phone_type`, `phone`, `email`, `insurance`) VALUES ('$uhid','$ipd_id','$occup','$add_typ','$add_1','$add_2','$city','$state','$zip','$country','$phone_typ','$phone','$email','$insurance')");
		//---------------bed----------------------------//
		/*
		mysqli_query($link,"insert into ipd_pat_bed_details(patient_id,ipd_id,ward_id,bed_id,user,time,date) values('$uhid','$ipd_id','$ward_id','$bed_id','$user','$time','$date')");
		mysqli_query($link,"insert into ipd_bed_alloc_details(patient_id,ipd_id,ward_id,bed_id,alloc_type,user,time,date) values('$uhid','$ipd_id','$ward_id','$bed_id','1','$user','$time','$date')");
		mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid'");
		*/
		mysqli_query($link,"UPDATE `ipd_bed_details_temp` SET `ipd_id`='$ipd_id' WHERE `patient_id`='$uhid'");
		//-----------------------bed------------------//
		mysqli_query($link,"insert into ipd_pat_doc_details(patient_id,ipd_id,attend_doc,admit_doc) values('$uhid','$ipd_id','$attend_doc','$admit_doc')");
		mysqli_query($link,"INSERT INTO `ipd_pat_relation`(`patient_id`, `ipd_id`, `person_type`, `name`, `address_type`, `add_1`, `add_2`, `city`, `state`, `zip`, `country`, `phone`, `email`) VALUES ('$uhid','$ipd_id','$con_rel','$con_name','$con_add_type','$con_add_1','$con_add_2','$con_city','$con_state','$con_postal','$con_country','$con_phone','$con_email')");
		if($insurance==1)
		{
			mysqli_query($link,"INSERT INTO `ipd_pat_insurance_det`(`patient_id`, `ipd_id`, `insurance_id`) VALUES ('$uhid','$ipd_id','$ins_id')");
		}
		//mysqli_query($link,"insert into ipd_pat_relation(patient_id,ipd_id,relation_type,name,phone) values('$uhid','$ipd_id','$con_rel','$con_name','$con_phone')");
		
		/*$rel=explode("#@",$relation);
		foreach($rel as $r)
		{
			if($r)
			{
				$det=explode("%",$r);
				mysqli_query($link,"insert into ipd_pat_relation(patient_id,ipd_id,relation_type,name,phone) values('$uhid','$ipd_id','$det[0]','$det[1]','$det[2]')");
			}
		}
		*/
		echo $ipd_id;
	}
	else
	{
		$ipd_id=$_POST['ipd'];
		$ipd_info=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_details where ipd_id='$ipd_id'"));
		mysqli_query($link,"delete from ipd_pat_info where ipd_id='$ipd_id'");	
		mysqli_query($link,"delete from ipd_pat_bed_details where ipd_id='$ipd_id'");	
		mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid'");
		
				
		mysqli_query($link,"insert into ipd_pat_info(patient_id,ipd_id,add_1,add_2,city,state,zip,country,relation,contact_no) values('$uhid','$ipd_id','$add_1','$add_2','$city','$state','$zip','$country','$relation','$cno')");
		mysqli_query($link,"insert into ipd_pat_bed_details(patient_id,ipd_id,ward_id,bed_id) values('$uhid','$ipd_id','$ward_id','$bed_id')");
		mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid'");
	}
}

else if($type==6)
{
	$uhid=$_POST[uhid];
	
	$ipd_det=mysqli_query($link,"select * from ipd_pat_details where patient_id='$uhid'");
	$ipd_num_chk=mysqli_num_rows($ipd_det);
	
	if($ipd_num_chk>0)
	{
		echo "<label>Previous Visits</label>";
		echo "<table class='table table-condensed'>";
		while($ipd_d=mysqli_fetch_array($ipd_det))
		{
			$ipd=trim($ipd_d[ipd_id]);
			echo "<tr><th>IPD ID : $ipd</th><th>Date: ".convert_date($ipd_d[date])."</th><th><input type='button' id='ipd_view' value='View' class='btn btn-info' onclick=\"ipd_dash($uhid,'$ipd')\" /></th></tr>";	
		}
		echo "</table>";
		echo "<hr/>";
	}
}
else if($type==7)
{
	$ward=$_POST[ward];
	$bed=$_POST[bed];
	
	mysqli_query($link,"delete from ipd_bed_details_temp where ward_id='$ward' and bed_id='$bed'");
}
?>
