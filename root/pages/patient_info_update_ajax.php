<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
$date = date("Y-m-d");
$time = date("H:i:s");
$type = $_POST['type'];

//print_r($_POST);

if($type==1)
{
    $cMemo=$_POST['cMemo'];
    $patNo=$_POST['patNo'];
    $name=$_POST['name'];
    
    if($cMemo)
    {
		$qry="SELECT DISTINCT `patient_id` FROM `uhid_and_opdid` WHERE `cashMemoNo` like '$cMemo%' ORDER BY `slno` DESC LIMIT 20";
	}
    if($patNo)
    {
		$qry="SELECT DISTINCT `patient_id` FROM `uhid_and_opdid` WHERE `patient_id` like '$patNo%' ORDER BY `slno` DESC LIMIT 20";
	}
    if($name)
    {
		$qry="SELECT DISTINCT a.`patient_id` FROM `uhid_and_opdid` a, `patient_info` b WHERE a.`patient_id`=b.`patient_id` AND b.`name` like '$name%' ORDER BY a.`slno` DESC LIMIT 20";
	}
	
	//echo $qry;
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>#</th>
			<th>Hospital No</th>
			<th>Patient Name</th>
			<th>Age Sex</th>
		</tr>
		<?php
		$j=1;
		$q=mysqli_query($link, $qry);
		while($r=mysqli_fetch_array($q))
		{
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
		?>
		<tr class="nm" onclick="load_pat_det('<?php echo base64_encode($r['patient_id']);?>')">
			<td><?php echo $j;?></td>
			<td><?php echo $r['patient_id'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $pat['age']." ".$pat['age_type']." ".$pat['sex'];?></td>
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
}

if($type==2)
{
	$pid=base64_decode($_POST['pid']);
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$pid'"));
	$ages=age_calculator_all($pat['dob']);
	$age=explode("@#@",$ages);
	?>
	<table class="table table-condensed table-bordered table-report">
		<tr>
			<th>Hospital No</th>
			<th>Patient Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Phone</th>
		</tr>
		<tr>
			<td>
				<?php echo $pid;?>
			</td>
			<td>
				<input type="text" class="span2" id="patName" onkeyup="capsUp(this)" value="<?php echo $pat['name'];?>" placeholder="Patient Name" />
			</td>
			<td>
				<div class="btn-group">
					<input type="text" class="span1" id="year" onkeyup="checkNumber(this)" value="<?php echo $age[0];?>" maxlength="3" placeholder="Years" />
					<input type="text" value="Year(s)" style="width:50px;cursor:default;" disabled />
				</div>
				<div class="btn-group">
					<input type="text" class="span1" id="month" onkeyup="checkNumber(this)" value="<?php echo $age[1];?>" maxlength="2" placeholder="Months" />
					<input type="text" value="Month(s)" style="width:55px;cursor:default;" disabled />
				</div>
				<div class="btn-group">
					<input type="text" class="span1" id="days" onkeyup="checkNumber(this)" value="<?php echo $age[2];?>" maxlength="2" placeholder="Days" />
					<input type="text" value="Day(s)" style="width:50px;cursor:default;" disabled />
				</div>
			</td>
			<td>
				<select class="span2" id="sex">
					<option value="">Select</option>
					<option value="Male" <?php if($pat['sex']=="Male"){echo "selected";}?>>Male</option>
					<option value="Female" <?php if($pat['sex']=="Female"){echo "selected";}?>>Female</option>
					<option value="Others" <?php if($pat['sex']=="Others"){echo "selected";}?>>Others</option>
				</select>
			</td>
			<td>
				<input type="text" class="span2" id="phone" onkeyup="checkNumber(this)" value="<?php echo $pat['phone'];?>" maxlength="10" placeholder="Phone" />
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align:center;">
				<button type="button" class="btn btn-primary" onclick="pat_update('<?php echo base64_encode($pid);?>')">Done</button>
				<button type="button" class="btn btn-danger" onclick="go_back()">Back</button>
			</td>
		</tr>
	</table>
	<?php
}

if($type==3)
{
	//print_r($_POST);
	$pid	=base64_decode($_POST['pid']);
	$patName=$_POST['patName'];
	$years	=$_POST['year'];
	$months	=$_POST['month'];
	$days	=$_POST['days'];
	$sex	=$_POST['sex'];
	$phone	=$_POST['phone'];
	$user	=$_POST['user'];
	
	$today = new DateTime(); // Explicit date for demonstration
	$interval = new DateInterval(sprintf('P%dY%dM%dD', $years, $months, $days));
	$dob = clone $today;
	$dob->sub($interval);

	$DOB=$dob->format('Y-m-d');
	//echo $DOB;
	if($years)
	{
		$age=$years;
		$age_type="Years";
	}
	else if($months)
	{
		$age=$months;
		$age_type="Months";
	}
	else if($months)
	{
		$age=$months;
		$age_type="Days";
	}
	
	$pat	=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time` FROM `patient_info` WHERE `patient_id`='$pid'"));

	$query = "INSERT INTO patient_info_edit (`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time`) VALUES ('$pat[patient_id]', '$pat[uhid]', '$pat[name]', '$pat[gd_name]', '$pat[relation]', '$pat[sex]', '$pat[dob]', '$pat[age]', '$pat[age_type]', '$pat[phone]', '$pat[address]', '$pat[email]', '$pat[religion_id]', '$pat[blood_group]', '$pat[marital_status]', '$pat[occupation]', '$pat[gurdian_Occupation]', '$pat[income_id]', '$pat[education]', '$pat[gd_phone]', '$pat[pin]', '$pat[police]', '$pat[state]', '$pat[district]', '$pat[city]', '$pat[post_office]', '$pat[father_name]', '$pat[mother_name]', '$pat[file_create]', '$user', '$date', '$time')";
	
	$arr=array();
	if(mysqli_query($link,"UPDATE `patient_info` SET `name`='$patName', `sex`='$sex', `dob`='$DOB', `age`='$age', `age_type`='$age_type', `phone`='$phone' WHERE `patient_id`='$pid'"))
	{
		mysqli_query($link, $query);
		$arr['response']=1;
		$arr['msg']="Done";
	}
	else
	{
		$arr['response']=0;
		$arr['msg']="Error";
	}
	
	echo json_encode($arr);
}



mysqli_close($link);
?>
