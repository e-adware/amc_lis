<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$branch_id=$emp_info["branch_id"];

$date=date("Y-m-d");
$time=date("H:i:s");

function currency($rs)
{
	setlocale(LC_MONETARY, 'en_IN');
	
	$amount = money_format('%!i', $rs);
	return $amount;
}

if($_POST["type"]=="user_master_emply_id")
{
	$user=$_POST["user"];
	$vid=nextId("","employee","emp_id","1");
	echo $code.$vid.$user;
}

if($_POST["type"]=="load_centres")
{
	$branch_id=$_POST["branch_id"];
	
	echo "<option value='0'>Select Centre</option>";
	
	$centre_qry=mysqli_query($link, " SELECT `centreno`, `centrename` FROM `centremaster` WHERE `branch_id`='$branch_id' ORDER BY `centrename` ASC ");
	while($centre=mysqli_fetch_array($centre_qry))
	{
		echo "<option value='$centre[centreno]'>$centre[centrename]</option>";
	}
	
}

/*
if($_POST["type"]=="employee_main_info")
{
	$emply_type=$_POST["emply_type"];
	
?>
	<table class="table table-striped table-bordered table-condensed">
	<?php if($emply_type=='5'){ ?>
		<tr class="doc">
			<th>Speciality</th>
			<td>
				<select id="emply_type" onChange="load_main_info()">
					<option value="0">Select</option>
					<option value="0">Anaesthesiology</option>
					<option value="0">Cardiology</option>
					<option value="0">Cardiothoracic Surgery</option>
					<option value="0">Dermatology</option>
					<option value="0">Diagnostic Radiology</option>
					<option value="0">Endocrinology</option>
					<option value="0">Gastroenterology</option>
					<option value="0">General Surgery</option>
					<option value="0">Haematology</option>
				</select>
			</td>
			<th>Is Surgeon</th>
			<td>
				<label><input type="checkbox" id="surgeon"> Yes</label>
			</td>
		</tr>
		<tr class="doc">
			<th>Qualification</th>
			<td>
				<input type="text" id="emply_edu">
			</td>
			<th>Designation</th>
			<td>
				<input type="text" id="emply_design">
			</td>
		</tr>
		<?php } if($emply_type=='5' || $emply_type=='11' ){ ?>
		<tr class="doc_nurse">
			<th>Registration No</th>
			<td colspan="3">
				<input type="text" id="emply_regd_no">
			</td>
		</tr>
		<?php } ?>
		<tr>
			<th>Name</th>
			<td>
				<input type="text" id="emply_name">
			</td>
			<th>Gender</th>
			<td>
				<input type="text" id="emply_sex">
			</td>
		</tr>
		<tr>
			<th>DOB</th>
			<td>
				<input type="text" class="datepicker" id="emply_dob">
			</td>
			<th>Email</th>
			<td>
				<input type="text" id="emply_email">
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<center><button class="btn btn-info">Save</button></center>
			</td>
		</tr>
	</table>
<?php
}*/
if($_POST["type"]=="Personal")
{
	$uhid=$_POST["uhid"];
	$val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee_personal` WHERE `emp_id`='$uhid' "));
	$rl=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee_family` WHERE `emp_id`='$uhid' "));
	if($val["marry_status"]=="Married")
	{
		$ani_date=$val["annvrsy_date"];
	}else
	{
		$ani_date="";
	}
?>
	<!--<center><h5>Personal Information</h5></center>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>Address1</th>
			<td>
				<input type="text" id="addr1" value="<?php echo $val["addr1"]; ?>">
			</td>
			<th>Address2</th>
			<td>
				<input type="text" id="addr2" value="<?php echo $val["addr2"]; ?>">
			</td>
		</tr>
		<tr>
			<th>City</th>
			<td>
				<input type="text" id="city" value="<?php echo $val["city"]; ?>">
			</td>
			<th>Mobile</th>
			<td>
				<input type="text" id="mobile" value="<?php echo $val["mobile"]; ?>">
			</td>
		</tr>
		<tr>
			<th>Emmergency Contact No</th>
			<td>
				<input type="text" id="emrgncy_fone_no" value="<?php echo $val["emr_contact"]; ?>">
			</td>
			<th>Marital Status</th>
			<td>
				<select id="marry_status" onChange="marry_status_ch(this.value)">
					<option value="0" <?php if($val["marry_status"]=="0"){ echo "selected"; } ?> >Select</option>
					<option value="Single" <?php if($val["marry_status"]=="Single"){ echo "selected"; } ?> >Single</option>
					<option value="Married" <?php if($val["marry_status"]=="Married"){ echo "selected"; } ?> >Married</option>
					<option value="Divorced" <?php if($val["marry_status"]=="Divorced"){ echo "selected"; } ?> >Divorced</option>
					<option value="Widowed" <?php if($val["marry_status"]=="Widowed"){ echo "selected"; } ?> >Widowed</option>
					<option value="Separated" <?php if($val["marry_status"]=="Separated"){ echo "selected"; } ?> >Separated</option>
				</select><br>
				<span id="anniversary_date_span" style="display:none;">
					<input type="text" class="datepicker" id="anniversary_date" placeholder="Anniversary Date" value="<?php echo $ani_date; ?>">
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<center>
					<button class="btn btn-info" id="comp_save_btn" onClick="com_save_user('Personal')">Save &amp; Next</button>
				</center>
			</td>
		</tr>
	</table>-->
	<style>
		.table > thead > tr:hover, .table > tbody > tr:hover
		{
			background:none;
		}
	</style>
	<center><h5>Personal Information</h5></center>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Permanent Address</th>
			<td>
				<textarea id="addr1" class="form-control" style="resize:none;width:80% !important;height:80px !important;" placeholder="Permanent Address"><?php echo $val["addr1"]; ?></textarea>
			</td>
			<th>Temporary Address</th>
			<td>
				<textarea id="addr2" class="form-control" style="resize:none;width:80% !important;height:80px !important;" placeholder="Temporary Address"><?php echo $val["addr2"]; ?></textarea>
			</td>
		</tr>
		<tr>
			<th>City</th>
			<td>
				<input type="text" id="city" value="<?php echo $val["city"]; ?>">
			</td>
			<th>Mobile</th>
			<td>
				<input type="text" id="mobile" value="<?php echo $val["mobile"]; ?>">
			</td>
		</tr>
		<tr>
			<th>Emmergency Contact No</th>
			<td>
				<input type="text" id="emrgncy_fone_no" value="<?php echo $val["emr_contact"]; ?>">
			</td>
			<th>Marital Status</th>
			<td>
				<select id="marry_status" onChange="marry_status_ch(this.value)">
					<option value="0" <?php if($val["marry_status"]=="0"){ echo "selected"; } ?> >Select</option>
					<option value="Single" <?php if($val["marry_status"]=="Single"){ echo "selected"; } ?> >Single</option>
					<option value="Married" <?php if($val["marry_status"]=="Married"){ echo "selected"; } ?> >Married</option>
					<option value="Divorced" <?php if($val["marry_status"]=="Divorced"){ echo "selected"; } ?> >Divorced</option>
					<option value="Widowed" <?php if($val["marry_status"]=="Widowed"){ echo "selected"; } ?> >Widowed</option>
					<option value="Separated" <?php if($val["marry_status"]=="Separated"){ echo "selected"; } ?> >Separated</option>
				</select><br>
				<span id="anniversary_date_span" style="display:none;">
					<input type="text" class="datepicker" id="anniversary_date" placeholder="Anniversary Date" value="<?php echo $ani_date; ?>">
				</span>
			</td>
		</tr>
		<tr>
			<th>Relation Name</th>
			<td>
				<input type="text" id="rel_name" value="<?php echo $val["rel_name"]; ?>">
			</td>
			<th>Relation Type</th>
			<td>
				<select id="rel_type" onChange="marry_status_ch(this.value)">
					<option value="0" <?php if($val["rel_type"]=="0"){ echo "selected"; } ?> >Select</option>
					<option value="Spouse" <?php if($val["rel_type"]=="Spouse"){ echo "selected"; } ?>>Spouse</option>
					<option value="Parent" <?php if($val["rel_type"]=="Parent"){ echo "selected"; } ?>>Parent</option>
					<option value="Child" <?php if($val["rel_type"]=="Child"){ echo "selected"; } ?>>Child</option>
					<option value="Partner" <?php if($val["rel_type"]=="Partner"){ echo "selected"; } ?>>Partner</option>
					<option value="Cousin" <?php if($val["rel_type"]=="Cousin"){ echo "selected"; } ?>>Cousin</option>
					<option value="Friend" <?php if($val["rel_type"]=="Friend"){ echo "selected"; } ?>>Friend</option>
					<option value="Neighbour" <?php if($val["rel_type"]=="Neighbour"){ echo "selected"; } ?>>Neighbour</option>
					<option value="Other" <?php if($val["rel_type"]=="Other"){ echo "selected"; } ?>>Other</option>
				</select><br>
				<span id="anniversary_date_span" style="display:none;">
					<input type="text" class="datepicker" id="anniversary_date" placeholder="Anniversary Date" value="<?php echo $ani_date; ?>">
				</span>
			</td>
		</tr>
		<tr>
			<th>Qualification</th>
			<td>
				<input type="text" id="quali" value="<?php echo $val["qualification"]; ?>">
			</td>
			<th></th>
			<td>
				<!--<b>Name of father</b><br/><input type="text" class="rel_det" id="rel_father" value="<?php echo $rl['father']; ?>" placeholder="Name of father" /><br/>
				<b>Name of mother</b><br/><input type="text" class="rel_det" id="rel_mother" value="<?php echo $rl['mother']; ?>" placeholder="Name of mother" /><br/>
				<b>Name of spouse</b><br/><input type="text" class="rel_det" id="rel_spouse" value="<?php echo $rl['spouse']; ?>" placeholder="Name of spouse" /><br/>
				<b>Name of child 1</b><br/><input type="text" class="rel_det" id="rel_child1" value="<?php echo $rl['child1']; ?>" placeholder="Name of child 1" /><br/>
				<b>Name of child 2</b><br/><input type="text" class="rel_det" id="rel_child2" value="<?php echo $rl['child2']; ?>" placeholder="Name of child 2" />-->
			</td>
		</tr>
		<tr>
			<td colspan="4" style="background:#eeeeee;"><center><b>Family Details</b></center></td>
		</tr>
		<tr>
			<td colspan="4">
				<table class="table table-condensed">
					<tr>
						<th>Father</th>
						<th>Mother</th>
						<th>Spouse</th>
						<th>Child1</th>
						<th>Child2</th>
					</tr>
					<tr>
						<td><input type="text" class="rel_det span2" id="rel_father" value="<?php echo $rl['father']; ?>" placeholder="Name of father" /></td>
						<td><input type="text" class="rel_det span2" id="rel_mother" value="<?php echo $rl['mother']; ?>" placeholder="Name of mother" /></td>
						<td><input type="text" class="rel_det span2" id="rel_spouse" value="<?php echo $rl['spouse']; ?>" placeholder="Name of spouse" /></td>
						<td><input type="text" class="rel_det span2" id="rel_child1" value="<?php echo $rl['child1']; ?>" placeholder="Name of child 1" /></td>
						<td><input type="text" class="rel_det span2" id="rel_child2" value="<?php echo $rl['child2']; ?>" placeholder="Name of child 2" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<center>
					<button class="btn btn-info" id="comp_save_btn" onClick="com_save_user('Personal')">Save &amp; Next</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="Official")
{
	$uhid=$_POST["uhid"];
	$user=$_POST["user"];
	$val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee_official` WHERE `emp_id`='$uhid' "));
?>
	<!--<center><h5>Official Information</h5></center>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>Date of Join</th>
			<td>
				<input type="text"  class="datepicker" id="join_date" value="<?php echo $val["join_date"]; ?>">
			</td>
			<th>Join Type</th>
			<td>
				<select id="join_type">
					<option value="0" <?php if($val["join_type"]=="0"){ echo "selected"; } ?> >Select</option>
					<option value="Permanent" <?php if($val["join_type"]=="Permanent"){ echo "selected"; } ?> >Permanent</option>
					<option value="Temporary" <?php if($val["join_type"]=="Temporary"){ echo "selected"; } ?> >Temporary</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Department</th>
			<td>
				<select id="department">
					<option value="0" <?php if($val["department"]=="0"){ echo "selected"; } ?> >Select</option>
					<option value="1" <?php if($val["department"]=="1"){ echo "selected"; } ?> >Accident and emergency (A&amp;E)</option>
					<option value="2" <?php if($val["department"]=="2"){ echo "selected"; } ?> >Admissions</option>
					<option value="3" <?php if($val["department"]=="3"){ echo "selected"; } ?> >Anesthetics</option>
					<option value="4" <?php if($val["department"]=="4"){ echo "selected"; } ?> >Breast Screening</option>
					<option value="5" <?php if($val["department"]=="5"){ echo "selected"; } ?> >Cardiology</option>
					<option value="6" <?php if($val["department"]=="6"){ echo "selected"; } ?> >Chaplaincy</option>
					<option value="7" <?php if($val["department"]=="7"){ echo "selected"; } ?> >Critical Care</option>
					<option value="8" <?php if($val["department"]=="8"){ echo "selected"; } ?> >Diagnostic Imaging</option>
				</select>
			</td>
			<th>Designation</th>
			<td>
				<input type="text" id="designation" value="<?php echo $val["designation"]; ?>">
			</td>
		</tr>
		<tr>
			<th>Reporting To</th>
			<td>
				<select id="reporting_to">
					<option value="0">Select</option>
				<?php
					$lvl_qry=mysqli_query($link," SELECT `emp_id`,`name` FROM `employee` WHERE `emp_id` in ( SELECT `emp_id` FROM `employee_official` WHERE `reporting_head`='1' ) order by `name` ");
					while($lvl=mysqli_fetch_array($lvl_qry))
					{
						if($val["reporting_to"]=="$lvl[emp_id]"){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$lvl[emp_id]' $sel >$lvl[name]</option>";
					}
				?>
				</select>
			</td>
			<th>As Reporting Head</th>
			<td>
				<label><input type="checkbox" id="report_head" value="1" <?php if($val["reporting_head"]==1){ echo "checked"; } ?> > Yes</label>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<center>
					<button class="btn btn-success" id="comp_prev_btn" onClick="com_prev_user('Official')">Previous</button>
					<button class="btn btn-info" id="comp_save_btn" onClick="com_save_user('Official')">Save &amp; Next</button>
				</center>
			</td>
		</tr>
	</table>-->
	<style>
		.table > thead > tr:hover, .table > tbody > tr:hover
		{
			background:none;
		}
	</style>
	<center><h5>Official Information</h5></center>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Date of Join</th>
			<td>
				<input type="text"  class="datepicker" id="join_date" value="<?php echo $val["join_date"]; ?>" placeholder="Date of Join" />
			</td>
			<th>Join Type</th>
			<td>
				<select id="join_type">
					<option value="0" <?php if($val["join_type"]=="0"){ echo "selected"; } ?> >Select</option>
					<option value="Permanent" <?php if($val["join_type"]=="Permanent"){ echo "selected"; } ?> >Permanent</option>
					<option value="Temporary" <?php if($val["join_type"]=="Temporary"){ echo "selected"; } ?> >Temporary</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Department</th>
			<td>
				<select id="department">
					<option value="0" <?php if($val["d_id"]=="0"){ echo "selected"; } ?> >Select</option>
					<?php
					$qq=mysqli_query($link,"SELECT * FROM `dept_master` ORDER BY `name`");
					while($r=mysqli_fetch_array($qq))
					{
					?>
					<option value="<?php echo $r['d_id']; ?>" <?php if($val["d_id"]==$r['d_id']){ echo "selected"; } ?> ><?php echo $r['name']; ?></option>
					<?php
					}
					?>
					<!--<option value="1" <?php if($val["department"]=="1"){ echo "selected"; } ?> >Accident and emergency (A&amp;E)</option>
					<option value="2" <?php if($val["department"]=="2"){ echo "selected"; } ?> >Admissions</option>
					<option value="3" <?php if($val["department"]=="3"){ echo "selected"; } ?> >Anesthetics</option>
					<option value="4" <?php if($val["department"]=="4"){ echo "selected"; } ?> >Breast Screening</option>
					<option value="5" <?php if($val["department"]=="5"){ echo "selected"; } ?> >Cardiology</option>
					<option value="6" <?php if($val["department"]=="6"){ echo "selected"; } ?> >Chaplaincy</option>
					<option value="7" <?php if($val["department"]=="7"){ echo "selected"; } ?> >Critical Care</option>
					<option value="8" <?php if($val["department"]=="8"){ echo "selected"; } ?> >Diagnostic Imaging</option>-->
				</select>
			</td>
			<th>Designation</th>
			<td>
				<input type="text" id="designation" value="<?php echo $val["designation"]; ?>" placeholder="Designation" />
			</td>
		</tr>
		<tr>
			<th>Pan Card No</th>
			<td>
				<input type="text" id="pan_no" value="<?php echo $val["pan_no"]; ?>" placeholder="Pan Card No" />
				<?php
				$panq=mysqli_query($sql." AND `type`='pan'");
				if(mysqli_num_rows($panq)==0)
				{
				?>
				<form id="fr_pan" method="post" enctype="multipart/form-data">
					<table class="table table-condensed">
						<tr>
							<td>
								<input type="file" id="pan_doc" name="pan_doc" class="btn btn-xs btn-default" />
							</td>
							<td><input type="button" id="psub" name="psub" class="btn btn-xs btn-default" style="" onclick="upd_doc('pan')" value="Upload" /></td>
						</tr>
					</table>
				</form>
				<form id="p_data" method="post" enctype="multipart/form-data">
					<input type="text" id="p_name" name="p_name" style="display:none;" />
					<input type="text" id="emp" name="emp" value="<?php echo $uhid."@".$user; ?>" style="display:none;" />
				</form>
				<?php
				}
				?>
			</td>
			<th>Voter Id No</th>
			<td>
				<input type="text" id="voter_no" value="<?php echo $val["voter_no"]; ?>" placeholder="Voter Id No" />
				<?php
				$votq=mysqli_query($sql." AND `type`='voter'");
				if(mysqli_num_rows($votq)==0)
				{
				?>
				<form id="fr_vot" method="post" enctype="multipart/form-data">
					<table class="table table-condensed">
						<tr>
							<td><input type="file" id="voter_doc" name="voter_doc" class="btn btn-xs btn-default" /></td>
							<td><input type="button" id="vsub" name="vsub" class="btn btn-xs btn-default" style="" onclick="upd_doc('voter')" value="Upload" /></td>
						</tr>
					</table>
				</form>
				<form id="v_data" method="post" enctype="multipart/form-data">
					<input type="text" id="v_name" name="v_name" style="display:none;" />
					<input type="text" id="emp" name="emp" value="<?php echo $uhid."@".$user; ?>" style="display:none;" />
				</form>
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<th>Residense Proof</th>
			<td>
				<textarea id="resi" class="form-control" style="resize:none;width:80% !important;height:80px !important;" placeholder="Residense Proof"><?php echo $val["residense_proof"]; ?></textarea>
				<?php
				$resiq=mysqli_query($sql." AND `type`='resi'");
				if(mysqli_num_rows($resiq)==0)
				{
				?>
				<form id="fr_res" action="#" method="post" enctype="multipart/form-data">
					<table class="table table-condensed">
						<tr>
							<td><input type="file" id="resi_doc" name="resi_doc" name="resi_doc" class="btn btn-xs btn-default" /></td>
							<td><input type="button" id="rsub" name="rsub" class="btn btn-xs btn-default" style="" onclick="upd_doc('resi')" value="Upload" /></td>
						</tr>
					</table>
				</form>
				<form id="r_data" method="post" enctype="multipart/form-data">
					<input type="text" id="r_name" name="r_name" style="display:none;" />
					<input type="text" id="emp" name="emp" value="<?php echo $uhid."@".$user; ?>" style="display:none;" />
				</form>
				<?php
				}
				?>
			</td>
			<th>ESIC Code</th>
			<td><input type="text" id="esic" value="<?php echo $val["esic"]; ?>" placeholder="ESIC Code" /></td>
		</tr>
		<tr>
			<th>PF VAN No</th>
			<td><input type="text" id="pf_no" value="<?php echo $val["pf_no"]; ?>" placeholder="PF VAN No" /></td>
			<th>Resignation / Termination Date</th>
			<td><input type="text" id="resign_date" class="datepicker" value="<?php echo $val["resign_date"]; ?>" placeholder="Resignation Date" /></td>
		</tr>
		<!--<tr>
			<th>Reporting To</th>
			<td>
				<select id="reporting_to">
					<option value="0">Select</option>
				<?php
					//$lvl_qry=mysqli_query($link," SELECT `emp_id`,`name` FROM `employee` WHERE `emp_id` in ( SELECT `emp_id` FROM `employee_official` WHERE `reporting_head`='1' ) order by `name` ");
					//while($lvl=mysqli_fetch_array($lvl_qry))
					{
						//if($val["reporting_to"]=="$lvl[emp_id]"){ $sel="selected"; }else{ $sel=""; }
						//echo "<option value='$lvl[emp_id]' $sel >$lvl[name]</option>";
					}
				?>
				</select>
			</td>
			<th>As Reporting Head</th>
			<td>
				<label><input type="checkbox" id="report_head" style="opacity:1;" value="1" <?php if($val["reporting_head"]==1){ echo "checked"; } ?> > Yes</label>
			</td>
		</tr>-->
		<tr>
			<td colspan="4">
				<center>
					<button class="btn btn-success" id="comp_prev_btn" onClick="com_prev_user('Official')">Previous</button>
					<button class="btn btn-info" id="comp_save_btn" onClick="com_save_user('Official')">Save &amp; Next</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="Salary")
{
	$uhid=$_POST["uhid"];
	$val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee_salary_mode` WHERE `emp_id`='$uhid' "));
	if($val["payment_mode"]=="3")
	{
		$style="";
	}else
	{
		$style="style='display:none;'";
	}//echo $style;
?>
	<style>
		.table > thead > tr:hover, .table > tbody > tr:hover
		{
			background:none;
		}
	</style>
	<center><h5>Salary Components</h5></center>
	<table class="table table-striped table-bordered table-condensed">
		<?php
		$qq=mysqli_query($link,"SELECT * FROM `salary_component`");
		while($rr=mysqli_fetch_array($qq))
		{
			$s=mysqli_fetch_array(mysqli_query($link,"SELECT `amount` FROM `employee_salary` WHERE `emp_id`='$uhid' AND `sal_id`='$rr[sal_id]'"));
		?>
		<tr>
			<th width="30%"><?php echo $rr['name']; ?></th>
			<td><input type="text" class="sal" id="sal<?php echo $rr['sal_id']; ?>" value="<?php echo $s['amount']; ?>"></td>
		</tr>
		<?php
		}
		?>
		<!--<tr>
			<th>Basic Salary</th>
			<td>
				<input type="text" id="basic_pay" value="<?php echo $val["basic_pay"]; ?>">
			</td>
			<th>Dearness Allowance</th>
			<td>
				<input type="text" id="da_pay" value="<?php echo $val["da_pay"]; ?>">
			</td>
		</tr>
		<tr>
			<th>House Rent Allowance</th>
			<td>
				<input type="text" id="hra_pay" value="<?php echo $val["hra_pay"]; ?>">
			</td>
			<th>Leave travel allowance</th>
			<td>
				<input type="text" id="lta_pay" value="<?php echo $val["lta_pay"]; ?>">
			</td>
		</tr>
		<tr>
			<th>Conveyance Allowance</th>
			<td>
				<input type="text" id="ca_pay" value="<?php echo $val["ca_pay"]; ?>">
			</td>
			<th>Medical Allowance</th>
			<td>
				<input type="text" id="medical_pay" value="<?php echo $val["ma_pay"]; ?>">
			</td>
		</tr>
		<tr>
			<th>Child Education Allowance</th>
			<td>
				<input type="text" id="cea_pay" value="<?php echo $val["cea_pay"]; ?>">
			</td>
			<th>Special Allowance</th>
			<td>
				<input type="text" id="sa_pay" value="<?php echo $val["sa_pay"]; ?>">
			</td>
		</tr>-->
		<tr>
			<th>Payment Mode</th>
			<td colspan="3">
				<select id="payment_mode" onChange="payment_mode_ch(this.value)">
					<option value="0" <?php if($val["payment_mode"]=="0"){ echo "selected"; } ?>>Select</option>
					<option value="1" <?php if($val["payment_mode"]=="1"){ echo "selected"; } ?>>Cash</option>
					<option value="2" <?php if($val["payment_mode"]=="2"){ echo "selected"; } ?>>Cheque</option>
					<option value="3" <?php if($val["payment_mode"]=="3"){ echo "selected"; } ?>>Online Transfer</option>
				</select><br>
				<span id="netbanking" <?php echo $style; ?>>
					<table class="table table-striped table-bordered table-condensed">
						<tr>
							<th>Bank Name</th>
							<td>
								<select id="bank_name" onChange="access_level_ch(this.value,this.id)">
									<option value="0">Select</option>
									<?php
										$lvl_qry=mysqli_query($link," SELECT * FROM `banks` order by `bank_name` ");
										while($lvl=mysqli_fetch_array($lvl_qry))
										{
											if($val["bank_id"]==$lvl['bank_id']){ $sel="selected"; }else{ $sel=""; }
											echo "<option value='$lvl[bank_id]' $sel>$lvl[bank_name]</option>";
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th>Branch</th>
							<td>
								<input type="text" id="bank_branch" value="<?php echo $val["branch"]; ?>" onKeyup="user_password(this.value,this.id)" placeholder="Branch" />
							</td>
						</tr>
						<tr>
							<th>Account No</th>
							<td>
								<input type="text" id="account_no" value="<?php echo $val["account_no"]; ?>" onKeyup="user_password(this.value,this.id)" placeholder="Account No" />
							</td>
						</tr>
						<tr>
							<th>IFSC Code</th>
							<td>
								<input type="text" id="ifsc_code" value="<?php echo $val["ifsc_code"]; ?>" onKeyup="user_password(this.value,this.id)" placeholder="IFSC Code" />
							</td>
						</tr>
					</table>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<center>
					<button class="btn btn-success" id="comp_prev_btn" onClick="com_prev_user('Salary')">Previous</button>
					<button class="btn btn-info" id="comp_save_btn" onClick="com_save_user('Salary')">Save &amp; Next</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="PFESI")
{
	$uid=$_POST["uhid"];
?>
	<center><h5>Deduction Components</h5></center>
	<!--<table class="table table-striped table-bordered table-condensed">
		<?php
		$q=mysqli_query($link,"SELECT * FROM `deduction_component`");
		while($r=mysqli_fetch_array($q))
		{
			$val=mysqli_fetch_array(mysqli_query($link,"SELECT `amount` FROM `employee_pfesi` WHERE `emp_id`='$uid' AND `ded_id`='$r[ded_id]'"));
		?>
		<tr>
			<th><?php echo $r['name']; ?></th>
			<td>
				<input type="text" class="ded" id="ded<?php echo $r['ded_id']; ?>" value="<?php echo $val['amount']; ?>">
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2">
				<center>
					<button class="btn btn-success" id="comp_prev_btn" onClick="com_prev_user('PFESI')">Previous</button>
					<button class="btn btn-info" id="comp_save_btn" onClick="com_save_user('PFESI')">Save &amp; Next</button>
				</center>
			</td>
		</tr>
	</table>-->
	<?php
	$q="SELECT * FROM `employee_salary` WHERE `emp_id`='$uid'";
	$bs=mysqli_fetch_array(mysqli_query($link,$q." AND `sal_id`='1'"));
	$da=mysqli_fetch_array(mysqli_query($link,$q." AND `sal_id`='2'"));
	$ha=mysqli_fetch_array(mysqli_query($link,$q." AND `sal_id`='3'"));
	$ca=mysqli_fetch_array(mysqli_query($link,$q." AND `sal_id`='4'"));
	$oa=mysqli_fetch_array(mysqli_query($link,$q." AND `sal_id`='5'"));
	$in=mysqli_fetch_array(mysqli_query($link,$q." AND `sal_id`='6'"));
	?>
	<table class="table table-condensed table-bordered">
		<?php
		
		$gross=$bs['amount']+$da['amount']+$ha['amount']+$ca['amount']+$oa['amount']+$in['amount'];
		$pff=($bs['amount']+$da['amount'])*12/100;
		if($pff>1800)
		$pf=1800;
		else
		$pf=$pff;
		//$tpf=($bs['amount']+$da['amount'])-$pf;
		$esi=$gross*(1.75/100);
		$tax=0;
		if($gross>=10500 && $gross<=15000)
		$tax=150;
		if($gross>15000 && $gross<=25000)
		$tax=180;
		if($gross>25000)
		$tax=208;
		$net=$gross-$pf-$esi-$tax;
		?>
		<tr>
			<th>PF</th><td>&#8377; <?php echo currency($pf); ?></td>
		</tr>
		<tr>
			<th>ESI</th><td>&#8377; <?php echo currency($esi); ?></td>
		</tr>
		<tr>
			<th>Professional Tax</th>
			<td>
				&#8377; <input type="text" id="tax" size="10" style="padding: 1px 1px 1px 6px;line-height: 0px;height: 30px;" value="<?php echo $tax; ?>" placeholder="&#8377; Tax" />
				<button type="button" class="btn btn-sm btn-default" onclick=""><b class="fa fa-save"></b> Save</button>
			</td>
		</tr>
	</table>
<?php
}
if($_POST["type"]=="salary_generation")
{
	$uhid=$_POST["uhid"];
	$user=$_POST["user"];
	
	echo "<input type='hidden' value='$uhid' id='emp_id'>";
?>
	<center><h5>Salary Generation</h5></center>
	<table class="table table-condensed table-bordered">
		<tr>
			<td>
				Select Month <span id="loadmon"></span>
				Year
					<?php
					$yr='<select id="year" name="year" onchange="load_month()">';
					 $starting_year='2015';
					 $ending_year = date('Y', strtotime('+10 year'));
					 $current_year = date('Y');
					 for($starting_year; $starting_year <= $ending_year; $starting_year++)
					 {
						 if($starting_year==$current_year)
						 $sell='selected="selected"';
						 else
						 $sell='';
						$yr.='<option value="'.$starting_year.'" '.$sell.'>'.$starting_year.'</option>';
					 }
					 $yr.='</select>';
					 echo $yr;
					?>
					
				<!--Working days : <span id="wdays"></span>-->
			</td>
		</tr>
	</table>
	<div id="load_salary_generation"></div>
	
<?php
}
if($_POST["type"]=="upd_data")
{
	$file=$_POST["file"];
	$fname=mysqli_real_escape_string($link, $_POST["fname"]);
	$n=101;
	$uid=$_POST["uid"];
	$user=$_POST["user"];
	$q=mysqli_query($link,"SELECT `file_name` FROM `employee_doc` WHERE `id`=(SELECT MAX(`id`) FROM `employee_doc` WHERE `emp_id`='$uid')");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$f=mysqli_fetch_array($q);
		$fe=explode("-",$f['file_name']);
		$fi=(int)$fe[2]+1;
		$fl="EMP-".$uid."-".$fi;
	}
	else
	{
		$fl="EMP-".$uid."-".$n;
	}
	mysqli_query($link,"INSERT INTO `employee_doc`(`emp_id`, `file_name`, `file_name_emp`, `type`, `user`) VALUES ('$uid','$fl','$fname','self','$user')");
	echo $uid."@".$fl;
}
if($_POST["type"]=="Documents")
{
	$uid=$_POST["uid"];
	$qry=mysqli_query($link,"SELECT * FROM `employee_doc` WHERE `emp_id`='$uid'");
	$num=mysqli_num_rows($qry);
?>
	<center><h5>Documents</h5></center>
	<table class="table table-condensed table-bordered table-striped">
		<?php
		if($num>0)
		{
			?>
			<tr>
				<th>SN</th>
				<th>File Name</th>
				<th></th>
			</tr>
			<?php
			$ii=1;
			while($r=mysqli_fetch_array($qry))
			{
			?>
			<tr>
				<td><?php echo $ii; ?></td>
				<td><?php echo $r['file_name_emp']; ?></td>
				<td><button type="button" class="btn btn-xs btn-primary" style="font-weight:bold;" onclick="view_emp_doc($(this).offset(),'<?php echo $r['id']; ?>')">View</button></td>
			</tr>
			<?php
			$ii++;
			}
		}
		?>
	</table>
	<form id="upd_frm" enctype="multipart/form-data">
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>Upload File</th>
				<td>
					<input type="file" name="file" onclick="$(this).css('border','')" id="file" class="btn btn-default" />
				</td>
			</tr>
			<tr>
				<th>File Name</th>
				<td>
					<input type="text" name="fname" onkeyup="$(this).css('border','')" id="fname" placeholder="File Name" />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="button" id="" class="btn btn-success" onClick="com_prev_user('Documents')" value="Previous" />
					<input type="button" id="" class="btn btn-info" onClick="upd_data('Documents')" value="Upload & Next" />
				</td>
			</tr>
		</table>
	</form>
	<form id="poData" enctype="multipart/form-data">
		<input type="text" name="filename" id="filename" style="display:none;" />
	</form>
<?php
}
if($_POST["type"]=="Application_Access")
{
	$emp_id=$uhid=$_POST["uhid"];
	
	$emp_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$uhid' "));
	if($emp_val["levelid"]==0)
	{
		$style="style='display:none'";
	}else
	{
		$style="";
	}
?>
	<style>
		.table > thead > tr:hover, .table > tbody > tr:hover
		{
			background:none;
		}
	</style>
	<center><h5>Application Access</h5></center>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th style="width: 25%;">Application Access</th>
			<td>
				<label class="radio-inline">
					<input type="radio" id="application_access" name="application_access" value="0" <?php if($emp_val["levelid"]==0){ echo "checked"; } ?> onClick="application_access(this.value)" > <span>No </span>
				</label>
				<label class="radio-inline">
					<input type="radio" id="application_access" name="application_access" value="1" <?php if($emp_val["levelid"]!=0){ echo "checked"; } ?> onClick="application_access(this.value)" > <span>Yes </span>
				</label>
			</td>
		</tr>
		<tr class="apl_access" <?php echo $style; ?>>
			<th>User Name</th>
			<td>
				<input type="text" id="user_name" readonly value="<?php echo $emp_val['name']; ?>">
			</td>
		</tr>
		<tr class="apl_access" <?php echo $style; ?>>
			<th>
				<?php if($emp_val['password']){ echo "Reset Password"; }else{ echo "Password"; } ?>
				<label class="radio-inline">
					<input type="radio" id="pass_reset" name="pass_reset" value="0" onchange="reset_pass(this.value)" checked > <span>No </span>
				</label>
				<label class="radio-inline">
					<input type="radio" id="pass_reset" name="pass_reset" value="1" onchange="reset_pass(this.value)" > <span>Yes </span>
				</label>
			</th>
			<td>
				<input type="password" id="user_password" value="" onKeyup="user_password(this.value,this.id)" disabled >
			</td>
		</tr>
		<tr class="apl_access" <?php echo $style; ?>>
			<th>Access Level</th>
			<td>
				<select id="access_level" onChange="access_level_ch(this.value,this.id)" disabled >
					<option value="0">Select</option>
				<?php
					$access_lvl_qry=mysqli_query($link," SELECT * FROM `level_master` order by `name` ");
					while($access_lvl=mysqli_fetch_array($access_lvl_qry))
					{
						if($emp_val["levelid"]==$access_lvl["levelid"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$access_lvl[levelid]' $sel>$access_lvl[name]</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr class="apl_access" style="display:none;">
			<th>Edit Patient Info</th>
			<td>
				<label class="radio-inline">
					<input type="radio" id="edit_info" name="edit_info" value="0" <?php if($emp_val["edit_info"]==0){ echo "checked"; } ?> > <span>No </span>
				</label>
				<label class="radio-inline">
					<input type="radio" id="edit_info" name="edit_info" value="1" <?php if($emp_val["edit_info"]!=0){ echo "checked"; } ?> > <span>Yes </span>
				</label>
			</td>
		</tr>
		<tr class="apl_access" style="display:none;">
			<th>Edit Payment Info</th>
			<td>
				<label class="radio-inline">
					<input type="radio" id="edit_payment" name="edit_payment" value="0" <?php if($emp_val["edit_payment"]==0){ echo "checked"; } ?> > <span>No </span>
				</label>
				<label class="radio-inline">
					<input type="radio" id="edit_payment" name="edit_payment" value="1" <?php if($emp_val["edit_payment"]!=0){ echo "checked"; } ?> > <span>Yes </span>
				</label>
			</td>
		</tr>
		<tr class="apl_access" style="display:none;">
			<th>Cancel Patient</th>
			<td>
				<label class="radio-inline">
					<input type="radio" id="cancel_pat" name="cancel_pat" value="0" <?php if($emp_val["cancel_pat"]==0){ echo "checked"; } ?> > <span>No </span>
				</label>
				<label class="radio-inline">
					<input type="radio" id="cancel_pat" name="cancel_pat" value="1" <?php if($emp_val["cancel_pat"]!=0){ echo "checked"; } ?> > <span>Yes </span>
				</label>
			</td>
		</tr>
		<tr class="apl_access" style="display:none;">
			<th>Discount Permission</th>
			<td>
				<label class="radio-inline">
					<input type="radio" id="discount_permission" name="discount_permission" value="0" <?php if($emp_val["discount_permission"]==0){ echo "checked"; } ?> > <span>No </span>
				</label>
				<label class="radio-inline">
					<input type="radio" id="discount_permission" name="discount_permission" value="1" <?php if($emp_val["discount_permission"]!=0){ echo "checked"; } ?> > <span>Yes </span>
				</label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<center>
					<!--<button class="btn btn-back" id="comp_prev_btn" onClick="com_prev_user('Application_Access')"><i class="icon-backward"></i> Previous</button>-->
					<button class="btn btn-save" id="comp_save_btn" onClick="com_save_user('Application_Access')"><i class="icon-save"></i> Save</button>
				</center>
			</td>
		</tr>
	</table>
<?php
}
// Save Start
if($_POST["type"]=="employee_main_info_save")
{
	$branch_id=$_POST["branch_id"];
	$emply_code=$_POST["emply_code"];
	$emply_type=$_POST["emply_type"];
	$emply_speclty=$_POST["emply_speclty"];
	$emply_edu=mysqli_real_escape_string($link, $_POST["emply_edu"]);
	$emply_design=mysqli_real_escape_string($link, $_POST["emply_design"]);
	$emply_regd_no=mysqli_real_escape_string($link, $_POST["emply_regd_no"]);
	$emply_name=mysqli_real_escape_string($link, $_POST["emply_name"]);
	$emply_sex=$_POST["emply_sex"];
	$emply_dob=mysqli_real_escape_string($link, $_POST["emply_dob"]);
	$emply_email=mysqli_real_escape_string($link, $_POST["emply_email"]);
	$emply_phone=$_POST["emply_phone"];
	$emply_address=mysqli_real_escape_string($link, $_POST["emply_address"]);
	$emply_status=$_POST["emply_status"];
	$centre_no=$_POST["centre_no"];
	$target_amount=mysqli_real_escape_string($link, $_POST["target_amount"]);
	$emply_password=mysqli_real_escape_string($link, $_POST["emply_password"]);
	$result_approve=$_POST["res_approve"];
	
	$m_fee=0;
	
	$user=$_POST["user"];
	
	$doc_type=1;
	
	if($centreno=="0" || !$centreno)
	{
		if($branch_id==1)
		{
			$centreno="C100";
		}
		else
		{
			$centreno="C286";
		}
	}
	
	if(!$target_amount){ $target_amount=0; }
	
	if($_POST["typ"]=="Save")
	{
		mysqli_query($link," INSERT INTO `employee`(`branch_id`, `name`, `sex`, `dob`, `email`, `speciality_id`, `surgeon`, `anaesthetist`, `qualification`, `designation`, `regd_no`, `password`, `levelid`, `user`, `emp_code`, `emp_type`, `edit_opd`, `edit_lab`, `cancel_pat`, `edit_ipd`, `edit_casualty`, `phone`, `status`) VALUES ('$branch_id','$emply_name','$emply_sex','$emply_dob','$emply_email','0','0','0','','','','','$emply_type','$user','$emply_code','$emply_type','0','0','0','0','0','$emply_phone','$emply_status') ");
		
		$last_id=mysqli_fetch_array(mysqli_query($link," SELECT `emp_id` FROM `employee` WHERE `emp_code`='$emply_code' AND `name`='$emply_name' AND `phone`='$emply_phone' AND `emp_type`='$emply_type' AND `user`='$user' ORDER BY `emp_id` DESC LIMIT 1 "));
		$emp_id=$last_id["emp_id"];
		
		mysqli_query($link," INSERT INTO `employee_alloc`(`emp_id`, `status`, `date`, `time`, `user`) VALUES ('$emp_id','$emply_status','$date','$time','$user') ");
		
		if($emply_type==5)
		{
			mysqli_query($link, "INSERT INTO `consultant_doctor_master`(`branch_id`, `Name`, `sex`, `dob`, `phone`, `email`, `address`, `qualification`, `designation`, `regd_no`, `doc_type`, `opd_visit_fee`, `opd_visit_validity`, `opd_reg_fee`, `opd_reg_validity`, `ipd_visit_fee`, `dept_id`, `average_time`, `signature`, `emp_id`, `room_id`, `status`, `user`, `date`, `time`, `main_doc_id`) VALUES ('$branch_id','$emply_name','$emply_sex','$emply_dob','$emply_phone','$emply_email','$emply_address','$emply_edu','$emply_design','$emply_regd_no','$doc_type','0','0','0','0','0','$emply_speclty','0','','$emp_id','0','$emply_status','$user','$date','$time','0')");
			
			$last_row=mysqli_fetch_array(mysqli_query($link," SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `Name`='$emply_name' AND `phone`='$emply_phone' AND `dept_id`='$emply_speclty' AND `user`='$user' ORDER BY `consultantdoctorid` DESC LIMIT 1 "));
			$doc_id=$last_row["consultantdoctorid"];
			
			mysqli_query($link," INSERT INTO `consultant_doctor_alloc`(`consultantdoctorid`, `status`, `date`, `time`, `user`) VALUES ('$doc_id','$emply_status','$date','$time','$user') ");
			
			mysqli_query($link," INSERT INTO `refbydoctor_master`(`ref_name`, `qualification`, `address`, `phone`, `email`, `consultantdoctorid`, `emp_id`, `branch_id`, `user`, `date`, `time`) VALUES ('$emply_name','$emply_edu','$emply_address','$emply_phone','$emply_email','$doc_id','$emp_id','$branch_id','$user','$date','$time') ");
			
		}
		if($emply_type==12 || $emply_type==13 || $emply_type==29)
		{
			if($emply_type==12)
			{
				$category=2;
			}
			if($emply_type==13)
			{
				$category=1;
			}
			if($emply_type==29)
			{
				$category=3;
			}
			
			$max_seq=mysqli_fetch_array(mysqli_query($link," SELECT max(`sequence`) as mx FROM `lab_doctor` WHERE `category`='$category' "));
			$max_seq_no=$max_seq["mx"]+1;
			
			mysqli_query($link," INSERT INTO `lab_doctor`(`id`, `sequence`, `category`, `name`, `desig`, `qual`, `phn`, `password`, `result_approve`, `status`, `dept_id`, `regd_no`, `sign_name`) VALUES ('$emp_id','$max_seq_no','$category','$emply_name','$emply_design','$emply_edu','','','$result_approve','$emply_status','$emply_speclty','$emply_regd_no','') ");
			
		}
		
		if($emply_type==8) // Collection
		{
			mysqli_query($link," INSERT INTO `collection_master`(`emp_id`, `branch_id`, `name`, `detail`, `centreno`, `status`) VALUES ('$emp_id','$branch_id','$emply_name','','$centre_no','$emply_status') ");
		}
		
		echo $last_id["emp_id"];
		
	}
	if($_POST["typ"]=="Update")
	{
		$emp_id=$uhid=$_POST["uhid"];
		
		$emp_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `employee` WHERE `emp_id`='$emp_id' "));
		
		mysqli_query($link, "INSERT INTO `employee_record`(`emp_id`, `user`, `date`, `time`, `ip_addr`) VALUES ('$emp_id','$c_user','$date','$time','$ip_addr')");
		
		mysqli_query($link," UPDATE `employee` SET `branch_id`='$branch_id',`name`='$emply_name',`sex`='$emply_sex',`dob`='$emply_dob',`phone`='$emply_phone',`email`='$emply_email',`address`='$emply_address',`levelid`='$emply_type',`emp_type`='$emply_type',`status`='$emply_status',`user`='$user',`centreno`='$centreno',`amount`='$target_amount' WHERE `emp_id`='$emp_id' ");
		
		$last_row_emp=mysqli_fetch_array(mysqli_query($link," SELECT `status` FROM `employee_alloc` WHERE `emp_id`='$emp_id' ORDER BY `slno` DESC LIMIT 1 "));
		if($last_row_emp["status"]!=$emply_status)
		{
			mysqli_query($link," INSERT INTO `employee_alloc`(`emp_id`, `status`, `date`, `time`, `user`) VALUES ('$emp_id','$emply_status','$date','$time','$user') ");
		}
		
		if($emply_type==5)
		{
			$con_entry_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `consultant_doctor_master` WHERE `emp_id`='$emp_id' "));
			if($con_entry_check)
			{
				$doc_id=$con_entry_check["consultantdoctorid"];
				mysqli_query($link, " UPDATE `consultant_doctor_master` SET `branch_id`='$branch_id',`Name`='$emply_name',`sex`='$emply_sex',`dob`='$emply_dob',`phone`='$emply_phone',`email`='$emply_email',`address`='$emply_address',`qualification`='$emply_edu',`designation`='$emply_design',`regd_no`='$emply_regd_no',`dept_id`='$emply_speclty',`user`='$user',`status`='$emply_status',`m_fee`='$m_fee' WHERE `consultantdoctorid`='$doc_id' ");
				
				$last_row=mysqli_fetch_array(mysqli_query($link," SELECT `status` FROM `consultant_doctor_alloc` WHERE `consultantdoctorid`='$doc_id' ORDER BY `slno` DESC LIMIT 1 "));
				if($last_row["status"]!=$emply_status)
				{
					mysqli_query($link," INSERT INTO `consultant_doctor_alloc`(`consultantdoctorid`, `status`, `date`, `time`, `user`) VALUES ('$doc_id','$emply_status','$date','$time','$user') ");
				}
				
				mysqli_query($link," UPDATE `refbydoctor_master` SET `ref_name`='$emply_name',`qualification`='$emply_edu',`address`='$emply_address',`phone`='$emply_phone',`email`='$emply_email' WHERE `consultantdoctorid`='$con_entry_check[consultantdoctorid]' ");
				
			}else
			{
				mysqli_query($link, "INSERT INTO `consultant_doctor_master`(`branch_id`, `Name`, `sex`, `dob`, `phone`, `email`, `address`, `qualification`, `designation`, `regd_no`, `doc_type`, `opd_visit_fee`, `opd_visit_validity`, `opd_reg_fee`, `opd_reg_validity`, `ipd_visit_fee`, `dept_id`, `average_time`, `signature`, `emp_id`, `user`, `room_id`, `status`, `m_fee`) VALUES ('$branch_id','$emply_name','$emply_sex','$emply_dob','$emply_phone','$emply_email','$emply_address','$emply_edu','$emply_design','$emply_regd_no','$doc_type','0','0','0','0','0','$emply_speclty','0','','$emp_id','$user','0','$emply_status','$m_fee')");
			
				$last_row=mysqli_fetch_array(mysqli_query($link," SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `Name`='$emply_name' AND `phone`='$emply_phone' AND `dept_id`='$emply_speclty' AND `user`='$user' ORDER BY `consultantdoctorid` DESC LIMIT 1 "));
				$doc_id=$last_row["consultantdoctorid"];
				
				mysqli_query($link," INSERT INTO `consultant_doctor_alloc`(`consultantdoctorid`, `status`, `date`, `time`, `user`) VALUES ('$doc_id','$emply_status','$date','$time','$user') ");
				
				mysqli_query($link," INSERT INTO `refbydoctor_master`(`ref_name`, `qualification`, `address`, `phone`, `email`, `consultantdoctorid`, `emp_id`, `branch_id`) VALUES ('$emply_name','$emply_edu','$emply_address','$emply_phone','$emply_email','$doc_id','$emp_id','$branch_id') ");
			}
		}else
		{
			//mysqli_query($link," DELETE FROM `consultant_doctor_master` WHERE `emp_id`='$uhid' ");
		}
		if($emply_type==12 || $emply_type==13 || $emply_type==29)
		{
			if($emply_type==12)
			{
				$category=2;
			}
			if($emply_type==13)
			{
				$category=1;
			}
			if($emply_type==29)
			{
				$category=3;
			}
			
			$lab_entry_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `lab_doctor` WHERE `id`='$emp_id' "));
			if($lab_entry_check)
			{
				mysqli_query($link," UPDATE `lab_doctor` SET `category`='$category',`name`='$emply_name',`desig`='$emply_design',`qual`='$emply_edu',`phn`='$emply_phone',`result_approve`='$result_approve',`status`='$emply_status',`dept_id`='$emply_speclty',`regd_no`='$emply_regd_no' WHERE `id`='$emp_id' ");
			}else
			{
				$max_seq=mysqli_fetch_array(mysqli_query($link," SELECT max(`sequence`) as mx FROM `lab_doctor` WHERE `category`='$category' "));
				$max_seq_no=$max_seq["mx"]+1;
				
				mysqli_query($link," INSERT INTO `lab_doctor`(`id`, `sequence`, `category`, `name`, `desig`, `qual`, `phn`, `password`, `result_approve`, `status`, `dept_id`, `regd_no`, `sign_name`) VALUES ('$emp_id','$max_seq_no','$category','$emply_name','$emply_design','$emply_edu','','','$result_approve','$emply_status','$emply_speclty','$emply_regd_no','') ");
			}
		}else
		{
			//mysqli_query($link," DELETE FROM `lab_doctor` WHERE `id`='$uhid' ");
		}
		
		if($emply_status==1)
		{
			$ip_addr=$_SERVER["REMOTE_ADDR"];
			$logged_in=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `login_activity` WHERE `emp_id`='$uhid' AND `status`='1' "));
			if($logged_in)
			{
				mysqli_query($link, " INSERT INTO `login_activity`(`emp_id`, `status`, `remark`, `date`, `time`, `ip_addr`) VALUES ('$emp_id','0','suspend','$date','$time','$ip_addr') ");
			}
		}
		
		if($emply_type==8) // Collection
		{
			$collection_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `collection_master` WHERE `emp_id`='$emp_id' "));
			if($collection_info)
			{
				mysqli_query($link," UPDATE `collection_master` SET `branch_id`='$branch_id',`name`='$emply_name',`centreno`='$centre_no',`status`='$emply_status' WHERE `emp_id`='$emp_id' ");
			}
			else
			{
				mysqli_query($link," INSERT INTO `collection_master`(`emp_id`, `branch_id`, `name`, `detail`, `centreno`, `status`) VALUES ('$emp_id','$branch_id','$emply_name','','$centre_no','$emply_status') ");
			}
		}
		else
		{
			mysqli_query($link," UPDATE `collection_master` SET `status`='1' WHERE `emp_id`='$emp_id' ");
		}
		
		echo $emp_id;
		
	}
}
if($_POST["type"]=="save_user_master_component")
{
	$uhid=$_POST["uhid"];
	$user=$_POST["user"];
	$all_val=$_POST["all_val"];
	$rel_det=$_POST["rel_det"];
	if($_POST["typ"]=="Personal")
	{
		// Delete
		mysqli_query($link," DELETE FROM `employee_personal` WHERE `emp_id`='$uhid' ");
		$all_vall=explode("@penguin@",$all_val);
		if($all_vall[9]=="")
		{
			$anvrsy_date="1111-11-11";
		}else
		{
			$anvrsy_date=$all_vall[9];
		}
		$anvrsy_date=trim($anvrsy_date);
		// Insert (`emp_id`, `addr1`, `addr2`, `city`, `mobile`, `emr_contact`, `marry_status`, `rel_name`, `rel_type`, `qualification`, `annvrsy_date`, `user`)
		mysqli_query($link," INSERT INTO `employee_personal`(`emp_id`, `addr1`, `addr2`, `city`, `mobile`, `emr_contact`, `marry_status`, `rel_name`, `rel_type`, `qualification`, `annvrsy_date`, `user`) VALUES ('$uhid','$all_vall[0]','$all_vall[1]','$all_vall[2]','$all_vall[3]','$all_vall[4]','$all_vall[5]','$all_vall[6]','$all_vall[7]','$all_vall[8]','$anvrsy_date','$user') ");
		//mysqli_query($link," UPDATE `lab_doctor` SET `phn`='$all_vall[3]' WHERE `id`='$uid' ");
		mysqli_query($link,"DELETE FROM `employee_family` WHERE `emp_id`='$uhid'");
		$rl=explode("##",$rel_det);
		if($rl)
		{
			mysqli_query($link,"INSERT INTO `employee_family`(`emp_id`, `father`, `mother`, `spouse`, `child1`, `child2`) VALUES ('$uhid','$rl[0]','$rl[1]','$rl[2]','$rl[3]','$rl[4]')");
		}
	}
	if($_POST["typ"]=="Official")
	{
		// Delete
		mysqli_query($link," DELETE FROM `employee_official` WHERE `emp_id`='$uhid' ");
		$all_vall=explode("@penguin@",$all_val);
		// Insert
		mysqli_query($link,"INSERT INTO `employee_official`(`emp_id`, `join_date`, `join_type`, `d_id`, `designation`, `pan_no`, `voter_no`, `residense_proof`, `esic`, `pf_no`, `resign_date`, `reporting_to`, `reporting_head`, `user`) VALUES ('$uhid','$all_vall[0]','$all_vall[1]','$all_vall[2]','$all_vall[3]','$all_vall[4]','$all_vall[5]','$all_vall[6]','$all_vall[7]','$all_vall[8]','$all_vall[9]','0','0','$user') ");
	}
	//~ if($_POST["typ"]=="Salary")
	//~ {
		//~ // Delete
		//~ mysqli_query($link," DELETE FROM `employee_salary` WHERE `emp_id`='$uhid' ");
		//~ $all_vall=explode("@penguin@",$all_val);
		//~ // Insert
		//~ mysqli_query($link," INSERT INTO `employee_salary`(`emp_id`, `basic_pay`, `da_pay`, `hra_pay`, `lta_pay`, `ca_pay`, `ma_pay`, `cea_pay`, `sa_pay`, `payment_mode`, `bank_id`, `account_no`, `ifsc_code`, `user`) VALUES ('$uhid','$all_vall[0]','$all_vall[1]','$all_vall[2]','$all_vall[3]','$all_vall[4]','$all_vall[5]','$all_vall[6]','$all_vall[7]','$all_vall[8]','$all_vall[9]','$all_vall[10]','$all_vall[11]','$user') ");
	//~ }
	if($_POST["typ"]=="Salary")
	{
		$month=date('m');
		$year=date('Y');
		// Delete
		mysqli_query($link,"DELETE FROM `employee_salary` WHERE `emp_id`='$uhid' ");
		//echo $all_val;
		$alll=explode("%",$all_val);
		$all_vall=explode("@",$alll[0]);
		// Insert
		foreach($all_vall as $all)
		{
			$al=explode("@",$all);
			foreach($al as $a)
			{
				$dl=explode("##",$a);
				
				$sal_id=str_replace("sal","",$dl[0]);
				$amt=$dl[1];
				if($sal_id && $amt)
				mysqli_query($link,"INSERT INTO `employee_salary`(`emp_id`, `sal_id`, `amount`, `user`) VALUES ('$uhid','$sal_id','$amt','$user')");
			}
		}
		$mid=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(`mod_id`) AS max FROM `employee_salary` WHERE `emp_id`='$uhid' AND `user`='$user'"));
		mysqli_query($link,"DELETE FROM `employee_salary_mode` WHERE `emp_id`='$uhid'");
		$ald=explode("@",$alll[1]);
		if($mid['max']>0)
		{
			mysqli_query($link,"INSERT INTO `employee_salary_mode`(`emp_id`, `mod_id`, `payment_mode`, `bank_id`, `branch`, `account_no`, `ifsc_code`) VALUES ('$uhid','$mid[max]','$ald[0]','$ald[1]','$ald[2]','$ald[3]','$ald[4]')");
		}
		
		$s=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `salary_generation` WHERE `emp_id`='$uhid' AND `month`='$month' AND `year`='$year'"));
		$gross=$s['basic']+$s['da']+$s['hra']+$s['conveyance']+$s['other_allowance']+$s['incentive'];
		$pftax=$s['pf_tax'];
		$pff=($s['basic']+$s['da'])*12/100;
		if($pff>1800)
		$pf=1800;
		else
		$pf=$pff;
		//$tpf=($bs['amount']+$da['amount'])-$pf;
		$esi=$gross*(1.75/100);
		$tax=0;
		if($gross>=10500 && $gross<=15000)
		$tax=150;
		if($gross>15000 && $gross<=25000)
		$tax=180;
		if($gross>25000)
		$tax=208;
		$deduct=$pf+$esi+$pftax+$tax;
		$net=$gross-$deduct;
		mysqli_query($link,"UPDATE `salary_generation` SET `pf`='$pf',`esi`='$esi',`pf_tax`='$pftax',`tds`='$tax' WHERE `emp_id`='$uid' AND `month`='$month' AND `year`='$year'");
	}
	if($_POST["typ"]=="PFESI")
	{
		//Delete
		mysqli_query($link," DELETE FROM `employee_pfesi` WHERE `emp_id`='$uhid' ");
		$al=explode("@",$all_val);
		foreach($al as $a)
		{
			$dl=explode("##",$a);
			
			$ded_id=str_replace("ded","",$dl[0]);
			$amt=$dl[1];
			if($ded_id && $amt)
			mysqli_query($link,"INSERT INTO `employee_pfesi`(`emp_id`, `ded_id`, `amount`, `user`) VALUES ('$uhid','$ded_id','$amt','$user')");
			//echo "INSERT INTO `employee_pfesi`(`emp_id`, `ded_id`, `amount`, `user`) VALUES ('$uid','$ded_id','$amt','$user')";
		}
	}
	if($_POST["typ"]=="Application_Access")
	{
		$all_vall=explode("@penguin@",$all_val);
		
		mysqli_query($link, "INSERT INTO `employee_record`(`emp_id`, `user`, `date`, `time`, `ip_addr`) VALUES ('$uhid','$c_user','$date','$time','$ip_addr')");
		
		if($all_vall[0]==0)
		{
			mysqli_query($link," UPDATE `employee` SET `password`='',`user`='$user',`edit_info`='0',`edit_payment`='0',`cancel_pat`='0',`discount_permission`='0' WHERE `emp_id`='$uhid' "); // ,`levelid`='0'
			if($all_vall[2]==12 || $all_vall[2]==13)
			{
				mysqli_query($link," UPDATE `lab_doctor` SET `password`='' WHERE `id`='$uhid' ");
			}
		}else
		{
			if($all_vall[1])
			{
				$md5_pass=md5($all_vall[1]);
				
				mysqli_query($link," UPDATE `employee` SET `password`='$md5_pass' WHERE `emp_id`='$uhid' ");
				
				mysqli_query($link," UPDATE `employee` SET `levelid`='$all_vall[2]',`emp_type`='$all_vall[2]',`user`='$user',`edit_info`='$all_vall[3]',`edit_payment`='$all_vall[4]',`cancel_pat`='$all_vall[5]',`discount_permission`='$all_vall[6]' WHERE `emp_id`='$uhid'");
				
			}else
			{
				mysqli_query($link," UPDATE `employee` SET `levelid`='$all_vall[2]',`emp_type`='$all_vall[2]',`user`='$user',`edit_info`='$all_vall[3]',`edit_payment`='$all_vall[4]',`cancel_pat`='$all_vall[5]',`discount_permission`='$all_vall[6]' WHERE `emp_id`='$uhid'");
			}
			
			if($all_vall[2]==12 || $all_vall[2]==13)
			{
				mysqli_query($link," UPDATE `lab_doctor` SET `password`='$all_vall[1]' WHERE `id`='$uhid' ");
			}
		}
	}
}

// Save End

if($_POST["type"]=="load_added_user_master")
{
	$branch_id=$_POST["branch_id"];
	$val=$_POST["val"];
	$typ=$_POST["typ"];
	
	if($typ=="pat_uhid")
	{
		if($val)
		{
			$qry=" SELECT * FROM `employee` WHERE `emp_code` like '$val%' AND `branch_id`='$branch_id'";
		}
	}
	if($typ=="pat_name")
	{
		if($val)
		{
			$qry=" SELECT * FROM `employee` WHERE `name` like '%$val%' AND `branch_id`='$branch_id' ";
		}
	}
?>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>#</th>
			<th>Employee Code</th>
			<th>Name</th>
			<th>Gender</th>
		</tr>
<?php
	$i=1;
	$q1=mysqli_query($link, $qry);
	while($q=mysqli_fetch_array($q1))
	{
	?>
		<tr id="bal_tr<?php echo $i;?>" onClick="load_all_info('<?php echo $q['emp_id'] ?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $q['emp_code'];?></td>
			<td><?php echo $q['name'];?></td>
			<td>
				<?php echo $q['sex'];?>
				<div id="pat_reg<?php echo $i;?>" style="display:none"><?php echo $q['emp_id'];?></div>
			</td>
		</tr>
	<?php
		$i++;
	}
?>
	</table>
<?php
}

if($_POST["type"]=="load_added_user_master_all")
{
	$uhid=$_POST["uhid"];
	
	$emp=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$uhid' "));
	
	$centre=mysqli_fetch_array(mysqli_query($link, " SELECT `centreno` FROM `collection_master` WHERE `emp_id`='$uhid' "));
	
	$emp_str=$uhid."@@".$emp["name"]."@@".$emp["sex"]."@@".$emp["dob"]."@@".$emp["phone"]."@@".$emp["email"]."@@".$emp["address"]."@@".$emp["emp_type"]."@@".$emp["status"]."@@".$emp["branch_id"]."@@".$emp["m_fee"]."@@".$centre["centreno"]."@@".$emp["amount"];
	
	$doc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `emp_id`='$uhid' "));
	
	$doc_str=$doc["dept_id"]."@@".$doc["qualification"]."@@".$doc["designation"]."@@".$doc["regd_no"];
	
	$lab=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `lab_doctor` WHERE `id`='$uhid' "));
	if($lab)
	{
		$result_approve=$lab["result_approve"];
	}
	else
	{
		$result_approve=0;
	}
	
	$lab_str=$result_approve."@@".$lab["qual"]."@@".$lab["desig"]."@@".$lab["dept_id"]."@@".$lab["regd_no"];
	
	$cashier_val=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cashier_access` WHERE `emp_id`='$uhid' "));
	
	$cashier_str=$cashier_val["opd_cashier"]."@@".$cashier_val["lab_cashier"]."@@".$cashier_val["ipd_cashier"]."@@".$cashier_val["pharmacy_cashier"]."@@".$cashier_val["bloodbank_cashier"]."@@".$cashier_val["casuality_cashier"];
	
	echo $emp_str."###".$doc_str."###".$lab_str."###".$cashier_str;
	
}

if($_POST["type"]=="load_month")
{
	$year=$_POST['year'];
	$cmon=date('m');
	$monthArray = range(1, 12);
	?>
	<select id="month" onchange="load_wdays()">
		<option value="0">Select</option>
		<?php
		foreach ($monthArray as $month)
		{
			$monthPadding = str_pad($month, 2, "0", STR_PAD_LEFT);
			$fdate = date("F", strtotime("$year-$monthPadding-01"));
			if($year<date('Y'))
			{
				//$dis1="disabled='disabled'";
				$dis1="";
				$dis2="";
				$sel="";
			}
			else
			{
				$dis1="";
				$dis2="";
			}
			if($monthPadding<$cmon && $year<=date('Y'))
			{
				//$dis2="disabled='disabled'";
				$dis2="";
				$sel="";
			}
			else
			{
				$dis2="";
				$sel="";
			}
			if($monthPadding==$cmon && $year==date('Y'))
			$sel='selected="selected"';
			else
			$sel="";
			echo '<option value="'.$monthPadding.'" '.$sel.' '.$dis1.' '.$dis2.'>'.$fdate.'</option>';
		}
		?>
	</select>
	<?php
}

if($_POST["type"]=="load_wdays")
{
	$month=$_POST['month'];
	$year=$_POST['year'];
	$myTime = date('Y-m-d');  // Use whatever date format you want
	$daysInMonth=cal_days_in_month(CAL_GREGORIAN,$month,$year);
	$workDays=0;
	while($daysInMonth > 0)
	{
		$day = date("D", $myTime); // Sun - Sat
		if($day != "Sun")
		{
			$workDays++;
		}
		$daysInMonth--;
		$myTime += 86400; // 86,400 seconds = 24 hrs.
	}
	echo $workDays;
	//~ 
	//~ $workdays = array();
	//~ $type = CAL_GREGORIAN;
	//~ $month = date('n'); // Month ID, 1 through to 12.
	//~ $year = date('Y'); // Year in 4 digit 2009 format.
	//~ $day_count = cal_days_in_month($type, $month, $year); // Get the amount of days
	//~ //print_r($day_count);
	//~ //loop through all days
	//~ for ($i = 1; $i <= $day_count; $i++) {
//~ 
			//~ $date = $year.'/'.$month.'/'.$i; //format date
			//~ $get_name = date('l', strtotime($date)); //get week day
			//~ $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars
			//~ //print_r($day_name);
			//~ //if not a weekend add day to array
			//~ if($day_name != 'Sun'){
				//~ $workdays[] = $i;
			//~ }
//~ 
	//~ }
//~ 
	//~ // look at items in the array uncomment the next line
	   //~ echo sizeof($workdays);
	//~ 
}

if($_POST["type"]=="view_salary")
{
	$id=$_POST['emp_id'];
	$month=$_POST['month'];
	$year=$_POST['year'];
	
	$myTime = date('Y-m-d');  // Use whatever date format you want
	$daysInMonth=cal_days_in_month(CAL_GREGORIAN,$month,$year);
	$workDays=0;
	while($daysInMonth > 0)
	{
		$day = date("D", $myTime); // Sun - Sat
		if($day != "Sun")
			$workDays++;

		$daysInMonth--;
		$myTime += 86400; // 86,400 seconds = 24 hrs.
	}
	
	$qry=mysqli_query($link,"SELECT * FROM `salary_generation` WHERE `emp_id`='$id' AND `month`='$month' AND `year`='$year'");
	$extra=mysqli_query($link,"SELECT `extra_leave` FROM `employee_extra_leave` WHERE `emp_id`='$id' AND `month`='$month' AND `year`='$year'");
	$extra_num=mysqli_num_rows($extra);
	
	$emp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$id'"));
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
		$examt=0;
		$s=mysqli_fetch_array($qry);
		if($s['basic'])
		$basic=$s['basic'];
		else
		$basic=0;
		
		if($s['da'])
		$da=$s['da'];
		else
		$da=0;
		
		if($s['hra'])
		$hra=$s['hra'];
		else
		$hra=0;
		
		if($s['conveyance'])
		$con=$s['conveyance'];
		else
		$con=0;
		
		if($s['other_allowance'])
		$oth=$s['other_allowance'];
		else
		$oth=0;
		
		if($s['incentive'])
		$inc=$s['incentive'];
		else
		$inc=0;
		
		if($s['overtime'])
		$over=$s['overtime'];
		else
		$over=0;
		
		if($s['tds'])
		$tax=$s['tds'];
		else
		$tax=0;
		$grs=$basic+$da+$hra+$con+$oth+$inc+$over;;
		$ext=mysqli_query($link,"SELECT `days` FROM `employee_overtime` WHERE `emp_id`='$id' AND `month`='$month' AND `year`='$year'");
		$ext_num=mysqli_num_rows($ext);
		if($ext_num>0)
		{
			$exd=mysqli_fetch_array($ext);
			$dayamt=$grs/$s['workdays'];
			$examt=$exd['days']*$dayamt;
			$exp=explode(".",$exd['days']);
			if($exp[1]>0)
			$incent=$exp[0]." and half days";
			else
			$incent=$exp[0]." days";
			$gross=$basic+$da+$hra+$con+$oth+$inc+$over+$incent;
		}
		else
		{
			$gross=$basic+$da+$hra+$con+$oth+$inc+$over;
		}
		
		$pf=$s['pf'];
		//$tpf=($bs['amount']+$da['amount'])-$pf;
		//$esi=$gross*(1.75/100);
		$pftax=$s['pf_tax'];
		$esi=$s['esi'];
		/*$pftax=0;
		if($gross>=10500 && $gross<=15000)
		$pftax=150;
		if($gross>15000 && $gross<=25000)
		$pftax=180;
		if($gross>25000)
		$pftax=208;*/
		$deduct=$pf+$esi+$pftax+$tax;
		//$net=$gross-$deduct;
		
		//$qr=mysqli_query($link,"SELECT * FROM `salary_component`");
		//background: linear-gradient(-90deg, #888888, #eeeeee);
		$adv=mysqli_query($link,"SELECT * FROM `employee_advance` WHERE `emp_id`='$id' AND `month`='$month' AND `year`='$year'");
		$adv_num=mysqli_num_rows($adv);
		?>
		<table class="table table-condensed table-bordered">
			<tr>
				<th style="text-align:center;" colspan="6"><?php echo $emp['name'];?></th>
			</tr>
			<tr>
				<th>Working days</th>
				<td><?php echo $workDays; ?> Days</td>
				<th width="20%">Number of days attended</th>
				<td>
					<?php
					$atn=explode(".",$s['attendance']);
					if($atn[1]>0)
					$attn=$atn[0]." and half";
					else
					$attn=$atn[0];
					echo $attn;
					?> Days
				</td>
				<?php
				if($s['attendance']>$s['workdays'])
				{
					$ovr=explode(".",($s['attendance']-$s['workdays']));
					if($ovr[1]>0)
					$ovrr=$ovr[0]." and half";
					else
					$ovrr=$ovr[0];
					$net_ded=$deduct;
				?>
				<th width="20%">Over time</th>
				<td><?php echo $ovrr;?> Days</td>
				<?php
				}
				else
				{
					//$cl=mysqli_fetch_array(mysqli_query($link,"SELECT `total` FROM `employee_casual_leave_total` WHERE `emp_id`='$id'"));
					$abs=explode(".",($s['workdays']-$s['attendance']));
					if($abs[1]>0)
					$abss=$abs[0]." and half";
					else
					$abss=$abs[0];
					if($extra_num>0)
					{
						$ex_leave=mysqli_fetch_array($extra);
						$dayamt=$gross/$s['workdays'];
						$abs_ded=$dayamt*($ex_leave['extra_leave']);
						$net_ded=$deduct+$abs_ded;
					}
					else
					{
						$net_ded=$deduct;
					}
				?>
				<th width="20%">Number of days absent</th>
				<td><?php echo $abss;?> Days</td>
				<?php
				}
				//echo $cl['total'];
				?>
			</tr>
		</table>
		<input type='text' id='emp_id' style='display:none;' value="<?php echo $id;?>" />
		<table id="sal" class="table table-condensed table-bordered table-report" style="">
			<tr>
				<th colspan="2" style="text-align:center;">Total Earnings</th>
				<th colspan="2" style="text-align:center;">Deduction</th>
			</tr>
			<tr>
				<th>Basic</th>
				<td>&#8377; <?php echo currency(round($basic)); ?></td>
				<th>PF</th>
				<td>&#8377; <?php echo "<input type='text' id='pf' style='display:none;' value='".$pf."' />".currency(round($pf)); ?></td>
			</tr>
			<tr>
				<th>Dearness Allowance</th>
				<td>&#8377; <?php echo currency(round($da)); ?></td>
				<th>ESI</th>
				<td>&#8377; <?php echo "<input type='text' id='esi' style='display:none;' value='".$esi."' />".currency(round($esi)); ?></td>
			</tr>
			<tr>
				<th>House Rent Allowance</th>
				<td>&#8377; <?php echo currency(round($hra)); ?></td>
				<th>Professional Tax</th>
				<td>&#8377; <?php if($pftax>200){?><input type="text" id="ptax" size="10" onkeyup='calc_salary(this.id,event)' style="padding: 1px 1px 1px 6px;line-height: 0px;height: 30px;" value="<?php echo currency(round($pftax)); ?>" placeholder="&#8377; Tax" /><?php }else{ echo '<input type="text" id="ptax" style="display:none;" value="'.currency(round($pftax)).'" /> '.currency(round($pftax));}?></td>
			</tr>
			<tr>
				<th>Conveyance Allowance</th>
				<td>&#8377; <?php if($con){echo "<input type='text' id='con' style='display:none;' value='0.00' />".currency(round($con));}else{echo "<input type='text' id='con' size='10' onkeyup='calc_salary(this.id,event)' style='padding: 1px 1px 1px 6px;line-height: 0px;height: 30px;' placeholder='Conveyance Allowance' value='".currency(round($con))."' />";} ?></td>
				<th>Tax Deduction at Source</th>
				<td>&#8377; <input type="text" id="tds" size="10" onkeyup='calc_salary(this.id,event)' style="padding: 1px 1px 1px 6px;line-height: 0px;height: 30px;" value="<?php echo currency(round($tax)); ?>" placeholder="&#8377; Tax" /></td>
			</tr>
			<tr>
				<th>Other Allowance</th>
				<td>&#8377; <?php if($oth){echo "<input type='text' id='oth' style='display:none;' value='0.00' />".currency(round($oth));}else{echo "<input type='text' id='oth' size='10' onkeyup='calc_salary(this.id,event)' style='padding: 1px 1px 1px 6px;line-height: 0px;height: 30px;' placeholder='Other Allowance' value='".currency(round($oth))."' />";} ?></td>
				<th style="border-top:2px solid;">Deduction</th>
				<td style="border-top:2px solid;">&#8377; <?php echo "<input type='text' id='deduct' style='display:none;' value='".round($deduct)."' /><span id='f_deduct'>".currency(round($deduct))."</span>"; ?></td>
			</tr>
				<?php
				$paylv="<span style='margin-left:20px;'>//</span>";
				$payamt="<span style='margin-left:20px;'>//</span>";
				$advtxt="<span style='margin-left:20px;'>//</span>";
				$advamount="<span style='margin-left:20px;'>//</span>";
				if($extra->num_rows>0)
				{$paylv="Leave without pay (".$ex_leave['extra_leave']." days)";$payamt='&#8377; '.currency(round($abs_ded));$nt_ded=$deduct+$abs_ded;}else{$nt_ded=$net_ded;}
				if($adv_num>0)
				{
					$advamt=mysqli_fetch_array($adv);
					$advamount=round($advamt['deduction']);
					$n_ded=$nt_ded+$advamount;$advtxt="Advance Deduction";
					$advamount="<label cursor:pointer;font-weight:initial;>&#8377; ".currency(round($advamt['deduction']))."<input type='checkbox' checked='checked' id='advchk' style='opacity:1.0;' /></label><span style='float:right;'><button type='button' class='btn btn-default btn-xs' onclick='upd_ded(".$advamt[batch_no].")'>Update</button></span>";
				}else
				{
					$n_ded=$nt_ded;
				}
				$net=$gross-$n_ded;
				?>
			<tr>
				<th>Incentive</th>
				<td>&#8377; <input type="text" id="inc" size="10" onkeyup='calc_salary(this.id,event)' style="padding: 1px 1px 1px 6px;line-height: 0px;height: 30px;" value="<?php echo currency(round($inc)); ?>" placeholder="&#8377; Incentive" /></td>
				<th><?php echo $paylv; ?></th>
				<td><?php echo $payamt; ?></td>
			</tr>
			<tr>
				<th>Overtime</th>
				<td>&#8377; <input type='text' id='over' size='10' onkeyup='calc_salary(this.id,event)' style='padding: 1px 1px 1px 6px;line-height: 0px;height: 30px;' value='<?php echo currency(round($over));?>' /></td>
				<th><?php echo $advtxt; ?></th>
				<td><?php echo $advamount;?></td>
			</tr>
			<tr>
				<th style="border-top:2px solid;">Gross Salary</th>
				<td style="border-top:2px solid;">&#8377; <?php echo "<input type='text' id='gross' style='display:none;' value='".round($gross)."' /><span id='f_gross'>".currency(round($gross))."</span>"; ?></td>
				<th style="border-top:2px solid;">Net Deduction</th>
				<td style="border-top:2px solid;">&#8377; <?php echo currency(round($n_ded));?></td>
			</tr>
			<tr>
				<td style="border-top:2px solid;"></td>
				<td style="border-top:2px solid;"></td>
				<th style="border-top:2px solid;">Net Salary</th>
				<td style="border-top:2px solid;">&#8377; <?php echo "<input type='text' id='net' style='display:none;' value='".round($net)."' /><span id='f_sal'>".currency(round($net))."</span>"; ?></td>
			</tr>
			<tr>
				<th colspan="4" style="text-align:right;">
					<!--<button type="button" class="btn btn-sm btn-default" onclick="save_sal('<?php echo $id?>','<?php echo $month?>','<?php echo $year?>')"><b class="fa fa-save"></b> Save</button>-->
					<button type="button" class="btn btn-sm btn-default" data-dismiss="modal" style="border:1px solid #ff0000;"><b class="fa fa-ban"></b> Close</button>
				</th>
			</tr>
		</table>
	<?php
	}
	else
	{
		$qr="SELECT `amount` FROM `employee_salary` WHERE `emp_id`='$id'";
		$bs=mysqli_fetch_array(mysqli_query($link,$qr." AND `sal_id`='1'"));
		$da=mysqli_fetch_array(mysqli_query($link,$qr." AND `sal_id`='2'"));
		$hra=mysqli_fetch_array(mysqli_query($link,$qr." AND `sal_id`='3'"));
		$ca=mysqli_fetch_array(mysqli_query($link,$qr." AND `sal_id`='4'"));
		$oa=mysqli_fetch_array(mysqli_query($link,$qr." AND `sal_id`='5'"));
		$inc=mysqli_fetch_array(mysqli_query($link,$qr." AND `sal_id`='6'"));
	?>
	<input type="text" id="bs" value="<?php echo $bs['amount']; ?>" style="display:none;" />
	<input type="text" id="da" value="<?php echo $da['amount']; ?>" style="display:none;" />
	<input type="text" id="hra" value="<?php echo $hra['amount']; ?>" style="display:none;" />
	<input type="text" id="ca" value="<?php echo $ca['amount']; ?>" style="display:none;" />
	<input type="text" id="oa" value="<?php echo $oa['amount']; ?>" style="display:none;" />
	<input type="text" id="inc" value="<?php echo $inc['amount']; ?>" style="display:none;" />
	<table class="table table-condensed table-bordered">
		<tr>
			<th style="text-align:center;" colspan="4"><?php echo $emp['name'];?></th>
		</tr>
		<tr>
			<th>Working days</th>
			<td><?php echo $workDays; ?> Days</td>
			<th>Number of days attend</th>
			<td>
				<!--<select id="attend" style="width:80px;" onchange="$(this).siblings('.select2-container').css('border','')">
					<option value="">Select</option>
					<?php
					for($l=0;$l<=31;$l++)
					{
					?>
					<option value="<?php echo $l;?>"><?php echo $l;?></option>
					<?php
					}
					// /[^0-9]/g,''
					?>
				</select>-->
				<input type="text" id="attend" class="form-control" onkeyup="filterme(this.id,event)" placeholder="Days" /> Days
				<span style="float:right;"><button type="button" class="btn btn-sm btn-default" onclick="update_attend('<?php echo $id;?>')">Save</button></span>
			</td>
		</tr>
	</table>
	<table id="sal" class="table table-condensed table-bordered table-report" style="opacity:0.4;">
		<tr>
			<th colspan="2" style="text-align:center;">Total Earnings</th><th colspan="2" style="text-align:center;">Deduction</th>
		</tr>
		<tr>
			<th>Basic</th><td>&#8377; <?php if($bs['amount']){echo currency($bs['amount']);}else{echo"-";} ?></td><th>PF</th><td>&#8377; -</td>
		</tr>
		<tr>
			<th>Dearness Allowance</th><td>&#8377; <?php if($da['amount']){echo currency($da['amount']);}else{echo"-";} ?></td><th>ESI</th><td>&#8377; -</td>
		</tr>
		<tr>
			<th>House Rent Allowance</th><td>&#8377; <?php if($hra['amount']){echo currency($hra['amount']);}else{echo"-";} ?></td><th>Professional Tax</th><td>&#8377; -</td>
		</tr>
		<tr>
			<th>Conveyance Allowance</th><td>&#8377; <?php if($ca['amount']){echo currency($ca['amount']);}else{echo"-";} ?></td><th>Tax Deduction at Source</th><td>&#8377; -</td>
		</tr>
		<tr>
			<th>Other Allowance</th><td>&#8377; <?php if($oa['amount']){echo currency($oa['amount']);}else{echo"-";} ?></td><th>Deduction</th><td>&#8377; -</td>
		</tr>
		<tr>
			<th>Incentive</th><td>&#8377; <?php if($inc['amount']){echo currency($inc['amount']);}else{echo"-";} ?></td><td></td><td></td>
		</tr>
		<tr>
			<th>Gross Salary</th><td>&#8377; -</td><td></td><td></td>
		</tr>
		<tr>
			<td></td><td></td><th>Net Salary</th><td>&#8377; -</td>
		</tr>
		<!--<tr>
			<th colspan="4" style="text-align:right;"><button type="button" class="btn btn-sm btn-default" onclick="save_sal('<?php echo $id?>','<?php echo $month?>','<?php echo $year?>')" disabled="disabled"><b class="fa fa-save"></b> Save</button></th>
		</tr>-->
	</table>
	<div style="text-align:right;">
		<button type="button" class="btn btn-sm btn-default" data-dismiss="modal" style="border:1px solid #ff0000;"><b class="fa fa-ban"></b> Close</button>
	</div>
	<?php
	}
	?>
	<style>
	#sal>tbody>tr, #sal>tbody>tr>th, #sal>tbody>tr>td
	{border: 1px solid #aaaaaa;}
	</style>
	<?php
}

if($_POST["type"]=="generate_salary")
{
	$id=$_POST['id'];
	$attend=$_POST['attend'];
	$month=$_POST['month'];
	$year=$_POST['year'];
	$bs=$_POST['bs'];
	$da=$_POST['da'];
	$hra=$_POST['hra'];
	$ca=$_POST['ca'];
	$oa=$_POST['oa'];
	$inc=$_POST['inc'];
	$usr=$_POST['usr'];
	
	$pftax=0;
	$adv=0;
	$tds=0;
	$gross=$bs+$da+$hra+$ca+$oa+$inc;
	$pff=($bs+$da)*12/100;
	if($pff>1800)
	$pf=1800;
	else
	$pf=$pff;
	$esi=$gross*(1.75/100);
	$pftax=0;
	if($gross>=10500 && $gross<=15000)
	$pftax=150;
	if($gross>15000 && $gross<=25000)
	$pftax=180;
	if($gross>25000)
	$pftax=208;
	$deduct=$pf+$esi+$pftax+$tds;
	$net=$gross-$deduct;
	$mxlv=$linko->query("SELECT `c_leave`, `m_leave` FROM `company_master`")->fetch_array(); // leave against company master
	
	$myTime = date('Y-m-d');  // Use whatever date format you want
	$daysInMonth=cal_days_in_month(CAL_GREGORIAN,$month,$year);
	$workDays=0;
	while($daysInMonth > 0)
	{
		$day = date("D", $myTime); // Sun - Sat
		if($day != "Sun")
			$workDays++;

		$daysInMonth--;
		$myTime += 86400; // 86,400 seconds = 24 hrs.
	}
	if($attend<$workDays)
	{
		$lv=$workDays-$attend;
		$qc=$linko->query("SELECT * FROM `employee_casual_leave_total` WHERE `emp_id`='$id'");
		$qm=$linko->query("SELECT * FROM `employee_medical_leave_total` WHERE `emp_id`='$id'");
		if($qc->num_rows>0)
		{
			$f=$qc->fetch_array();
			$l=$f['total']+$lv;
			if($l>$mxlv['c_leave'])
			{
				$ol=$l-$mxlv['c_leave'];
				if($ol>$mxlv['m_leave'])
				{
					if($qm->num_rows>0)
					{
						$am=$qm->fetch_array();
						$mml=$am['total']+$ol;
						if($mml>$mxlv['m_leave'])
						{
							$ex=$mml-$mxlv['m_leave'];
							$linko->query("UPDATE `employee_medical_leave_total` SET `total`='$mxlv[m_leave]' WHERE `emp_id`='$id'");
							$linko->query("INSERT INTO `employee_extra_leave`(`emp_id`, `month`, `extra_leave`, `year`, `date`, `time`, `user`) VALUES ('$id','$month','$ex','$year','$date','$time','$usr')");
						}
						else
						{
							$linko->query("UPDATE `employee_medical_leave_total` SET `total`='$mml' WHERE `emp_id`='$id'");
						}
					}
					else
					{
						$linko->query("INSERT INTO `employee_medical_leave_total`(`emp_id`, `total`) VALUES ('$id','$ol')");
					}
				}
				else
				{
					$linko->query("UPDATE `employee_casual_leave_total` SET `total`='$mxlv[c_leave]' WHERE `emp_id`='$id'");
					if($qm->num_rows>0)
					{
						$am=$qm->fetch_array();
						$mml=$am['total']+$ol;
						if($mml>$mxlv['m_leave'])
						{
							$ex=$mml-$mxlv['m_leave'];
							$linko->query("UPDATE `employee_medical_leave_total` SET `total`='$mxlv[m_leave]' WHERE `emp_id`='$id'");
							$linko->query("INSERT INTO `employee_extra_leave`(`emp_id`, `month`, `extra_leave`, `year`, `date`, `time`, `user`) VALUES ('$id','$month','$ex','$year','$date','$time','$usr')");
						}
						else
						{
							$linko->query("UPDATE `employee_medical_leave_total` SET `total`='$mml' WHERE `emp_id`='$id'");
						}
					}
					else
					{
						$linko->query("INSERT INTO `employee_medical_leave_total`(`emp_id`, `total`) VALUES ('$id','$ol')");
					}
				}
			}
			else
			{
				$linko->query("UPDATE `employee_casual_leave_total` SET `total`='$l' WHERE `emp_id`='$id'");
			}
		}
		else
		{
			if($lv>$mxlv['c_leave'])
			{
				$l=$lv-$mxlv['c_leave'];
				if($l>$mxlv['m_leave'])
				{
					$o=$l-$mxlv['m_leave'];
					if($o>$mxlv['m_leave'])
					{
						$ex=$o-$mxlv['m_leave'];
						$linko->query("INSERT INTO `employee_casual_leave_total`(`emp_id`, `total`) VALUES ('$id','$mxlv[c_leave]')");
						$linko->query("INSERT INTO `employee_medical_leave_total`(`emp_id`, `total`) VALUES ('$id','$mxlv[m_leave]')");
						if($linko->query("SELECT * FROM `employee_extra_leave` WHERE `emp_id`='$id' AND `year`='$year'")->num_rows>0)
						{
							$link0->query("UPDATE `employee_extra_leave` SET `month`='$month',`extra_leave`='$ex',`date`='$date',`time`='$time',`user`='$usr' WHERE `emp_id`='$id' AND `year`='$year'");
						}
						else
						{
							$linko->query("INSERT INTO `employee_extra_leave`(`emp_id`, `month`, `extra_leave`, `year`, `date`, `time`, `user`) VALUES ('$id','$month','$ex','$year','$date','$time','$usr')"); // new table
						}
					}
					else
					{
						$linko->query("INSERT INTO `employee_casual_leave_total`(`emp_id`, `total`) VALUES ('$id','$mxlv[c_leave]')");
						$linko->query("INSERT INTO `employee_medical_leave_total`(`emp_id`, `total`) VALUES ('$id','$o')");
					}
				}
				else
				{
					$linko->query("INSERT INTO `employee_casual_leave_total`(`emp_id`, `total`) VALUES ('$id','$mxlv[c_leave]')");
					$linko->query("INSERT INTO `employee_medical_leave_total`(`emp_id`, `total`) VALUES ('$id','$l')");
				}
			}
			else
			{
				$linko->query("INSERT INTO `employee_casual_leave_total`(`emp_id`, `total`) VALUES ('$id','$lv')");
			}
		}
	}
	if($attend>$workDays)
	{
		$over=$attend-$workDays;
		$linko->query("INSERT INTO `employee_overtime`(`emp_id`, `month`, `year`, `days`) VALUES ('$id','$month','$year','$over')");
	}
	if($linko->query("INSERT INTO `salary_generation`(`emp_id`, `month`, `year`, `workdays`, `attendance`, `basic`, `da`, `hra`, `conveyance`, `other_allowance`, `incentive`, `pf`, `esi`, `pf_tax`, `tds`, `advance`, `date`, `time`, `user`) VALUES ('$id','$month','$year','$workDays','$attend','$bs','$da','$hra','$ca','$oa','$inc','$pf','$esi','$pftax','$tds','$adv','$date','$time','$usr')"))
	echo "Saved";
	else
	echo "Error";
}

?>
