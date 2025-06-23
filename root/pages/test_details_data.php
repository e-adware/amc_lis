<?php
session_start();
include("../../includes/connection.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("h:i:s");

//print_r($_POST);
$type=$_POST["type"];


if($type=="load_centres")
{
	$branch_id=$_POST["branch_id"];
	
	$str="SELECT `centreno`,`centrename` FROM `centremaster` WHERE `branch_id`='$branch_id' AND `centreno` NOT IN(SELECT `centreno` FROM `test_details_centre`) ORDER BY `centrename` ASC";
?>
	<table class="table table-condensed">
		<tr>
			<td>
				<select id="centreno">
					<option value="">Select Centre</option>
				<?php
					$centre_qry=mysqli_query($link, "$str");
					while($centre_info=mysqli_fetch_array($centre_qry))
					{
						echo "<option value='$centre_info[centreno]'>$centre_info[centrename]</option>";
					}
				?>
				</select>
				<button type="button" class="btn btn-info" onclick="add_centre()">Add</button>
			</td>
		</tr>
	</table>
<?php
}

if($type=="add_centre")
{
	$branch_id=$_POST["branch_id"];
	$centreno=$_POST["centreno"];
	
	if(mysqli_query($link, "INSERT INTO `test_details_centre`(`centreno`) VALUES ('$centreno')"))
	{
		echo "Added";
	}
	else
	{
		echo "Failed, try again later.";
	}
}

if($type=="load_added_centre")
{
	$branch_id=$_POST["branch_id"];
	
	$str="SELECT a.`centreno`,a.`centrename` FROM `centremaster` a, `test_details_centre` b WHERE a.`centreno`=b.`centreno` AND a.`branch_id`='$branch_id' ORDER BY a.`centrename` ASC";
	
	$centre_qry=mysqli_query($link, $str);
	
	$centre_num=mysqli_num_rows($centre_qry);
	if($centre_num>0)
	{
?>
		<table class="table table-condensed">
			<tr>
				<th>#</th>
				<th>Centre Name</th>
				<th></th>
			</tr>
<?php
		$n=1;
		while($centre_data=mysqli_fetch_array($centre_qry))
		{
?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $centre_data["centrename"]; ?></td>
				<td>
					<button type="button" class="btn btn-warning btn-mini" id="centre_remove_btn" onclick="centre_remove('<?php echo $centre_data["centreno"]; ?>')">Remove</button>
				</td>
			</tr>
<?php
			$n++;
		}
?>
		</table>
<?php
	}
}
if($type=="centre_remove")
{
	$centreno=$_POST["centreno"];
	
	if(mysqli_query($link, "DELETE FROM `test_details_centre` WHERE `centreno`='$centreno'"))
	{
		echo "Removed";
	}
	else
	{
		echo "Failed, try again later.";
	}
}

if($type=="load_dept")
{
	$category_id=$_POST["category_id"];
	
	echo "<option value='0'>All Department</option>";
	
	$str="SELECT a.`id`, a.`name` FROM `test_department` a, `testmaster` b, `patient_test_details` c WHERE a.`category_id`=b.`category_id` AND b.`category_id`='$category_id' AND b.`testid`=c.`testid` GROUP BY `id` ORDER BY a.`name` ASC";
	$qry=mysqli_query($link, $str);
	while($data=mysqli_fetch_array($qry))
	{
		echo "<option value='$data[id]'>$data[name]</option>";
	}
}

if($type=="load_test")
{
	$type_id=$_POST["type_id"];
	
	echo "<option value='0'>All Test</option>";
	
	$test_str="SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' AND `type_id`='$type_id' ORDER BY `testname` ASC";
	$test_qry=mysqli_query($link, $test_str);
	while($test=mysqli_fetch_array($test_qry))
	{
		echo "<option value='$test[testid]'>$test[testname]</option>";
	}
}

if($type=="load_all_test")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$category_id=$_POST["category_id"];
	$type_id=$_POST["type_id"];
	$testid=$_POST["testid"];
	$branch_id=$_POST["branch_id"];
	
	$str="SELECT DISTINCT b.`testid`,b.`testname` FROM `patient_test_details` a JOIN `testmaster` b JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`test_rate`>0 AND e.`center_no` IN(SELECT `centreno` FROM `test_details_centre`)";
	
	$del_test_str="";
	if($branch_id>0)
	{
		$str.=" AND e.`branch_id`='$branch_id'";
		$del_test_str.=" AND `branch_id`='$branch_id'";
	}
	
	if($category_id>0)
	{
		$str.=" AND b.`category_id`='$category_id'";
		$del_test_str.=" AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='$category_id')";
	}
	
	if($type_id>0)
	{
		$str.=" AND b.`type_id`='$type_id'";
		$del_test_str.=" AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$type_id')";
	}
	
	if($testid>0)
	{
		$str.=" AND b.`testid`='$testid'";
		$del_test_str.=" AND `testid`='$testid'";
	}
	
	$str.=" ORDER BY b.`testname` ASC";
	
	//echo $str;
	
	$test_qry=mysqli_query($link, $str);
	
	$del_test=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`slno`) AS `test_no`,ifnull(SUM(`test_rate`),0) AS `test_amount` FROM `test_details_data` WHERE `test_date` BETWEEN '$date1' AND '$date2' $del_test_str"));
	
	if($del_test["test_no"]>0)
	{
?>
	<b style="float:right;display:none;">No.: <?php echo $del_test["test_no"]; ?> &nbsp;&nbsp;&nbsp; Amount: <?php echo $del_test["test_amount"]; ?></b>
<?php
	}
?>
	<table class="table table-condensed table-hover" id="keywords">
		<thead class="table_header_fix">
			<tr>
				<th><span>#</span></th>
				<th style="width: 350px;"><span>Test Name</span></th>
				<th><span>Test No.</span></th>
				
				<th>Single Test Patient</th>
				<th>Multiple Tests Patient</th>
				<th>Remove Test No.</th>
				<th>Removed Test No</th>
				<th>Removed Test Amount</th>
			</tr>
		</thead>
		<tbody>
<?php
	$n=1;
	while($test=mysqli_fetch_array($test_qry))
	{
		$testid=$test["testid"];
		$each_test_num_total=0;
		
		//~ $each_test_str="SELECT a.* FROM `patient_test_details` a JOIN `testmaster` b JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode`NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0 AND e.`center_no` IN(SELECT `centreno` FROM `test_details_centre`)";
		
		//~ if($branch_id>0)
		//~ {
			//~ $each_test_str.=" AND e.`branch_id`='$branch_id'";
		//~ }
		
		//~ $each_test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
		
		//~ $each_test_qry=mysqli_query($link, $each_test_str);
		//~ $each_test_num_total=mysqli_num_rows($each_test_qry);
		
		//
		$each_test_str="SELECT a.`patient_id`,a.`opd_id` FROM `patient_test_details` a JOIN `testmaster` b JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode`NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0 AND e.`center_no` IN(SELECT `centreno` FROM `test_details_centre`)";
		
		if($branch_id>0)
		{
			$each_test_str.=" AND e.`branch_id`='$branch_id'";
		}
		
		$each_test_str.=" GROUP BY a.`patient_id`,a.`opd_id`";
		
		
		$single_test_pat=0;
		$multi_test_pat=0;
		$pat_each_test_qry=mysqli_query($link, $each_test_str);
		while($pat_each_test=mysqli_fetch_array($pat_each_test_qry))
		{
			$patient_id=$pat_each_test["patient_id"];
			$opd_id=$pat_each_test["opd_id"];
			
			$non_cash=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Cash'"));
			if(!$non_cash)
			{
				$test_str="SELECT a.`slno` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`!='$testid' AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`test_rate`>0";
				
				if($branch_id>0)
				{
					$test_str.=" AND e.`branch_id`='$branch_id'";
					$del_test_str=" AND `branch_id`='$branch_id'";
				}
				$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
				//echo $test_str."<br>";
				
				$each_test_qry=mysqli_query($link, $test_str);
				$each_test_num=mysqli_num_rows($each_test_qry);
				
				if($each_test_num==0)
				{
					$single_test_pat++;
					$each_test_num_total++;
				}
				else
				{
					$multi_test_pat++;
					$each_test_num_total++;
				}
			}
		}
		
		$del_test=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`slno`) AS `test_no`,ifnull(SUM(`test_rate`),0) AS `test_amount` FROM `test_details_data` WHERE `testid`='$testid' AND `test_date` BETWEEN '$date1' AND '$date2' $del_test_str AND `testid`='$testid'"));
		
		$td_style="";
		$click_fn="";
		if($single_test_pat>0)
		{
			$td_style="cursor:pointer;background-color: #00000042;";
			
			$click_fn="load_replace('$testid','$date1','$date2','$branch_id')";
		}
		if($each_test_num_total>0)
		{
?>
		<tr>
			<td><?php echo $n; ?></td>
			<td><?php echo $test["testname"]; ?></td>
			<td><?php echo $each_test_num_total; ?></td>
			
			<td style="<?php echo $td_style; ?>" onclick="<?php echo $click_fn; ?>">
			<?php
				echo $single_test_pat;
			?>
			</td>
			<td><?php echo $multi_test_pat; ?></td>
			<td>
				<input type="hidden" id="testid<?php echo $testid; ?>" class="form-control tst" value="<?php echo $testid; ?>">
				<input type="hidden" id="multi_test_pat<?php echo $testid; ?>" class="form-control" value="<?php echo $multi_test_pat; ?>">
				<input type="tel" id="remove_test_no<?php echo $testid; ?>" class="form-control numericc" onkeyup="remove_test_no_up(event,this,'<?php echo $testid; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $branch_id; ?>')" style="width: 100px;">
				<!--<button class="btn btn-danger btn-sm" onClick="test_remove('<?php echo $testid; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $branch_id; ?>')">Remove</button>-->
				<span id="del_ammount_to<?php echo $testid; ?>" style="display: block;font-weight: bold;"></span>
			</td>
			<td><?php echo $del_test["test_no"]; ?></td>
			<td><?php echo $del_test["test_amount"]; ?></td>
		</tr>
<?php
			$n++;
		}
	}
	if($n>1)
	{
?>
		<tr>
			<td colspan="8">
				<center>
					<button class="btn btn-danger btn-sm" onClick="test_remove('','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $branch_id; ?>')">Remove</button>
				</center>
			</td>
		</tr>
<?php
	}
?>
	</table>
<?php
}

if($type=="load_test_det")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$testid=$_POST["testid"];
	$branch_id=$_POST["branch_id"];
	
	$each_test_str="SELECT a.`patient_id`,a.`opd_id` FROM `patient_test_details` a JOIN `testmaster` b JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode`NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0 AND e.`center_no` IN(SELECT `centreno` FROM `test_details_centre`)";
	
	if($branch_id>0)
	{
		$each_test_str.=" AND e.`branch_id`='$branch_id'";
	}
	
	$each_test_str.=" GROUP BY a.`patient_id`,a.`opd_id`";
	
	$single_test_pat=0;
	$multi_test_pat=0;
	$pat_each_test_qry=mysqli_query($link, $each_test_str);
	while($pat_each_test=mysqli_fetch_array($pat_each_test_qry))
	{
		$patient_id=$pat_each_test["patient_id"];
		$opd_id=$pat_each_test["opd_id"];
		
		$non_cash=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Cash'"));
		if(!$non_cash)
		{
			$test_str="SELECT a.`slno` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`!='$testid' AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`test_rate`>0";
			
			if($branch_id>0)
			{
				$test_str.=" AND e.`branch_id`='$branch_id'";
				$del_test_str=" AND `branch_id`='$branch_id'";
			}
			$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
			//echo $test_str."<br>";
			
			$each_test_qry=mysqli_query($link, $test_str);
			$each_test_num=mysqli_num_rows($each_test_qry);
			
			if($each_test_num==0)
			{
				$single_test_pat++;
			}
			else
			{
				$multi_test_pat++;
			}
		}
	}
	
	$del_test=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`slno`) AS `test_no`,ifnull(SUM(`test_rate`),0) AS `test_amount` FROM `test_details_data` WHERE `testid`='$testid' AND `test_date` BETWEEN '$date1' AND '$date2' $del_test_str"));
	
	$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));
	
?>
	<b>Test Name: <?php echo $test_info["testname"]; ?></b>
	<table class="table table-condensed table-hover">
		<tr>
			<th>Single Test Patient</th>
			<th>Multiple Tests Patient</th>
			<th>Remove Test No.</th>
	<?php
		if($del_test["test_no"]>0)
		{
	?>
			<th>Deleted Test No</th>
			<th>Deleted Test Amount</th>
	<?php
		}
	?>
		</tr>
		<tr>
			<td><?php echo $single_test_pat; ?></td>
			<td><?php echo $multi_test_pat; ?></td>
			<td>
				<input type="hidden" id="multi_test_pat" class="form-control" value="<?php echo $multi_test_pat; ?>">
				<input type="tel" id="remove_test_no" class="form-control numericc span1" onkeyup="remove_test_no_up(event,this,'<?php echo $testid; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $branch_id; ?>')">
				<button class="btn btn-delete" onClick="test_remove('<?php echo $testid; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $branch_id; ?>')" style="margin-bottom: 10px;"><i class="icon-remove"></i> Remove</button>
				<span id="del_ammount_to" style="display: block;font-weight: bold;"></span>
			</td>
	<?php
		if($del_test["test_no"]>0)
		{
	?>
			<td><?php echo $del_test["test_no"]; ?></td>
			<td><?php echo $del_test["test_amount"]; ?></td>
	<?php
		}
	?>
		</tr>
	</table>
	<br>
	<center>
		<button class="btn btn-back" onClick="back_to_main()"><i class="icon-backward"></i> Back</button>
	</center>
<?php
}


if($type=="test_remove")
{
	//~ print_r($_POST);
	//~ exit();
	
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$testid=$_POST["testid"];
	$branch_id=$_POST["branch_id"];
	$all_test=$_POST["all_test"];
	
	$all_tests=explode("@$@", $all_test);
	
	$del_test_num=0;
	$max=1;
	
	foreach($all_tests AS $all_test)
	{
		if($all_test)
		{
			$each_test=explode("#", $all_test);
			
			$testid=$each_test[0];
			$remove_test_no=$each_test[1];
			$multi_test_pat=$each_test[2];
			
			if($multi_test_pat>=$remove_test_no)
			{
				$each_test_str="SELECT a.`patient_id`,a.`opd_id`,c.`tot_amount` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0";
				
				if($branch_id>0)
				{
					$each_test_str.=" AND e.`branch_id`='$branch_id'";
				}
				
				$each_test_str.=" GROUP BY a.`patient_id`,a.`opd_id` ORDER BY c.`tot_amount` DESC";
				
				$n=0;
				$single_test_pat=0;
				$multi_test_pat=0;
				$pat_each_test_qry=mysqli_query($link, $each_test_str);
				while($pat_each_test=mysqli_fetch_array($pat_each_test_qry))
				{
					$patient_id=$pat_each_test["patient_id"];
					$opd_id=$pat_each_test["opd_id"];
					
					$non_cash=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Cash'"));
					if(!$non_cash)
					{
						$test_str="SELECT a.`slno` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`!='$testid' AND a.`test_rate`>0  AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id'";
						
						if($branch_id>0)
						{
							$test_str.=" AND e.`branch_id`='$branch_id'";
						}
						$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
						//echo $test_str."<br>";
						
						$each_test_qry=mysqli_query($link, $test_str);
						$each_test_num=mysqli_num_rows($each_test_qry);
						
						if($each_test_num>0)
						{
							if($n<$remove_test_no)
							{
								$test_det=mysqli_fetch_array(mysqli_query($link, "SELECT `test_rate`,`date` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'"));
								
								if($test_det)
								{
									if(mysqli_query($link, "INSERT INTO `test_details_data`(`branch_id`, `patient_id`, `opd_id`, `testid`, `test_rate`, `test_date`, `user`, `date`, `time`) VALUES ('$branch_id','$patient_id','$opd_id','$testid','$test_det[test_rate]','$test_det[date]','$c_user','$date','$time')"))
									{
										
										mysqli_query($link, "DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `phlebo_sample` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_update` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_cancel` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_delete` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_card` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_card_cancel` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_rad` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_rad_cancel` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_rad_delete` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_sample_stat` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `test_sample_result_repeat` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testreport_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `patient_test_summary` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										if($testid==1227)
										{
											mysqli_query($link, "DELETE FROM `widalresult` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
											mysqli_query($link, "DELETE FROM `widalresult_cancel` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
										}
										
										// Accounts
										$test_amount=0;
										$vaccu_amount=0;
										$tot_amount=0;
										$test_det=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`test_rate`),0) AS `tot_test` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
										$test_amount=$test_det["tot_test"];
										
										$vaccu_det=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`rate`),0) AS `tot_vaccu` FROM `patient_vaccu_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
										$vaccu_amount=$vaccu_det["tot_vaccu"];
										//~ if($vaccu_amount>0)
										//~ {
											//~ $vaccu_amount=20;
										//~ }
										$tot_amount=$test_amount+$vaccu_amount;
										
										mysqli_query($link, "UPDATE `invest_patient_payment_details` SET `tot_amount`='$tot_amount',`advance`='$tot_amount' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
										
										mysqli_query($link, "DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`!='Advance'");
										
										mysqli_query($link, "DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit'");
										
										$pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT `pay_id` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Advance'"));
										if($pay_det)
										{
											mysqli_query($link, "UPDATE `payment_detail_all` SET `amount`='$tot_amount' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Advance'");
										}
										else
										{
											$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
											
											$p_type_id=$pat_reg["type"];

											$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));
											$bill_name=$pat_typ_text["bill_name"];
											
											$bill_no=generate_bill_no_new($bill_name,$p_type_id);
											
											mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$tot_amount','0','$tot_amount','0','','0','','0','','0','','Advance','Cash','','$pat_reg[user]','$pat_reg[date]','$pat_reg[time]','$p_type_id') ");
										}
										$del_test_num++;
										$n++;
									}
								}
							}
							else
							{
								break;
							}
						}
						$max++;
						if($max>2000)
						{
							break;
						}
					}
				}
			}
		}
	}
	if($del_test_num==0)
	{
		echo "404@0 Test Deleted";
	}
	else
	{
		echo "101@".$del_test_num." Test(s) Deleted";
	}
}

if($type=="load_test_amount")
{
	//~ print_r($_POST);
	//~ exit();
	
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$testid=$_POST["testid"];
	$branch_id=$_POST["branch_id"];
	$multi_test_pat=$_POST["multi_test_pat"];
	$remove_test_no=$_POST["remove_test_no"];
	
	if($multi_test_pat>=$remove_test_no)
	{
		$each_test_str="SELECT a.`patient_id`,a.`opd_id`,c.`tot_amount` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0";
		
		if($branch_id>0)
		{
			$each_test_str.=" AND e.`branch_id`='$branch_id'";
		}
		
		$each_test_str.=" GROUP BY a.`patient_id`,a.`opd_id` ORDER BY c.`tot_amount` DESC";
		
		$test_amount_to=0;
		$max=1;
		$n=0;
		$single_test_pat=0;
		$multi_test_pat=0;
		$pat_each_test_qry=mysqli_query($link, $each_test_str);
		while($pat_each_test=mysqli_fetch_array($pat_each_test_qry))
		{
			$patient_id=$pat_each_test["patient_id"];
			$opd_id=$pat_each_test["opd_id"];
			
			$non_cash=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Cash'"));
			if(!$non_cash)
			{
				$test_str="SELECT a.`slno` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`!='$testid' AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`test_rate`>0";
				
				if($branch_id>0)
				{
					$test_str.=" AND e.`branch_id`='$branch_id'";
				}
				$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
				//echo $test_str."<br>";
				
				$each_test_qry=mysqli_query($link, $test_str);
				$each_test_num=mysqli_num_rows($each_test_qry);
				
				if($each_test_num>0)
				{
					if($n<$remove_test_no)
					{
						$test_det=mysqli_fetch_array(mysqli_query($link, "SELECT `test_rate`,`date` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'"));
						
						if($test_det)
						{
							$test_amount_to+=$test_det["test_rate"];
							$n++;
						}
					}
					else
					{
						break;
					}
				}
				$max++;
				if($max>2000)
				{
					break;
				}
			}
		}
		if($test_amount_to==0)
		{
			echo "";
		}
		else
		{
			echo number_format($test_amount_to,2);
		}
	}
	else
	{
		echo "";
	}
}

if($type=="load_replace")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$testid=$_POST["testid"];
	$branch_id=$_POST["branch_id"];
	
	$each_test_str="SELECT a.`patient_id`,a.`opd_id` FROM `patient_test_details` a JOIN `testmaster` b JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode`NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0 AND e.`center_no` IN(SELECT `centreno` FROM `test_details_centre`)";
	
	if($branch_id>0)
	{
		$each_test_str.=" AND e.`branch_id`='$branch_id'";
	}
	
	$each_test_str.=" GROUP BY a.`patient_id`,a.`opd_id`";
	
	$single_test_pat=0;
	$multi_test_pat=0;
	$pat_each_test_qry=mysqli_query($link, $each_test_str);
	while($pat_each_test=mysqli_fetch_array($pat_each_test_qry))
	{
		$patient_id=$pat_each_test["patient_id"];
		$opd_id=$pat_each_test["opd_id"];
		
		$non_cash=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Cash'"));
		if(!$non_cash)
		{
			$test_str="SELECT a.`slno` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`!='$testid' AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`test_rate`>0";
			
			if($branch_id>0)
			{
				$test_str.=" AND e.`branch_id`='$branch_id'";
				$del_test_str=" AND `branch_id`='$branch_id'";
			}
			$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
			//echo $test_str."<br>";
			
			$each_test_qry=mysqli_query($link, $test_str);
			$each_test_num=mysqli_num_rows($each_test_qry);
			
			if($each_test_num==0)
			{
				$single_test_pat++;
			}
			else
			{
				$multi_test_pat++;
			}
		}
	}
	
	$replace_test=mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`slno`) AS `test_no`,ifnull(SUM(`test_rate_diff`),0) AS `test_amount` FROM `test_details_data_replace` WHERE `testid`='$testid' AND `test_date` BETWEEN '$date1' AND '$date2' $del_test_str"));
	
	$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));
	
?>
	<b>Test Name: <?php echo $test_info["testname"]; ?></b>
	<table class="table table-condensed table-hover">
		<tr>
			<th>Single Test Patient</th>
			<!--<th>Multiple Tests Patient</th>-->
			<th>Replace Test With</th>
			<th>Replace Test No.</th>
		</tr>
		<tr>
			<td><?php echo $single_test_pat; ?></td>
			<!--<td><?php echo $multi_test_pat; ?></td>-->
			<td>
				<select id="testid_replace" onchange="replace_test_no_up_chk('','','<?php echo $testid; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $branch_id; ?>')">
					<option value="0">Select</option>
			<?php
				$qry=mysqli_query($link, "SELECT `testid`,`testname` FROM `testmaster` WHERE `testname`!='' AND `testid`!='$testid' ORDER BY `testname` ASC");
				while($data=mysqli_fetch_array($qry))
				{
					echo "<option value='$data[testid]'>$data[testname]</option>";
				}
			?>
				</select>
			</td>
			<td>
				<input type="hidden" id="single_test_pat" class="form-control" value="<?php echo $single_test_pat; ?>">
				<input type="tel" id="replace_test_no" class="form-control numericc span1" onkeyup="replace_test_no_up(event,this,'<?php echo $testid; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $branch_id; ?>')">
				<button class="btn btn-delete" id="test_replace_btn" onClick="test_replace('<?php echo $testid; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $branch_id; ?>')" style="margin-bottom: 10px;"><i class="icon-refresh"></i> Replace</button>
				<span id="replace_ammount_to" style="display: block;font-weight: bold;"></span>
			</td>
		</tr>
	</table>
	<br>
	<center>
		<button class="btn btn-back" onClick="back_to_main()"><i class="icon-backward"></i> Back</button>
	</center>
<?php
}
if($type=="load_test_amount_replace")
{
	//~ print_r($_POST);
	//~ exit();
	
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$testid=$_POST["testid"];
	$branch_id=$_POST["branch_id"];
	$single_test_pat=$_POST["single_test_pat"];
	$replace_test_no=$_POST["replace_test_no"];
	$testid_replace=$_POST["testid_replace"];
	
	if($single_test_pat>=$replace_test_no)
	{
		$each_test_str="SELECT a.`patient_id`,a.`opd_id`,c.`tot_amount` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0";
		
		if($branch_id>0)
		{
			$each_test_str.=" AND e.`branch_id`='$branch_id'";
		}
		
		$each_test_str.=" GROUP BY a.`patient_id`,a.`opd_id` ORDER BY c.`tot_amount` DESC";
		
		$test_amount_to=0;
		$max=1;
		$n=0;
		$single_test_pat=0;
		$pat_each_test_qry=mysqli_query($link, $each_test_str);
		while($pat_each_test=mysqli_fetch_array($pat_each_test_qry))
		{
			$patient_id=$pat_each_test["patient_id"];
			$opd_id=$pat_each_test["opd_id"];
			
			$non_cash=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Cash'"));
			if(!$non_cash)
			{
				$test_str="SELECT a.`slno` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`test_rate`>0";
				
				if($branch_id>0)
				{
					$test_str.=" AND e.`branch_id`='$branch_id'";
				}
				$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
				//echo $test_str."<br>";
				
				$each_test_qry=mysqli_query($link, $test_str);
				$each_test_num=mysqli_num_rows($each_test_qry);
				
				if($each_test_num>0)
				{
					if($n<$replace_test_no)
					{
						$test_det=mysqli_fetch_array(mysqli_query($link, "SELECT `test_rate`,`date` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'"));
						
						if($test_det)
						{
							$test_amount_to+=$test_det["test_rate"];
							$n++;
						}
					}
					else
					{
						break;
					}
				}
				$max++;
				if($max>2000)
				{
					break;
				}
			}
		}
		if($test_amount_to==0)
		{
			echo "";
		}
		else
		{
			$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `rate` FROM `testmaster` WHERE `testid`='$testid_replace'"));
			
			$new_test_amount=$test_info["rate"]*$replace_test_no;
			
			$save_amount=$test_amount_to-$new_test_amount;
			
			echo "Test amount = ".number_format($test_amount_to,2)."<br>Replace test amouont = ".number_format($new_test_amount,2)."<br>-------------------------------------------<br>Save amount = ".number_format($save_amount,2);
		}
	}
	else
	{
		echo "";
	}
}
if($type=="test_replace")
{
	//~ print_r($_POST);
	//~ exit();
	
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$testid=$_POST["testid"];
	$branch_id=$_POST["branch_id"];
	$single_test_pat=$_POST["single_test_pat"];
	$replace_test_no=$_POST["replace_test_no"];
	$testid_replace=$_POST["testid_replace"];
	
	if($multi_test_pat>=$remove_test_no)
	{
		$each_test_str="SELECT a.`patient_id`,a.`opd_id`,c.`tot_amount` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0";
		
		if($branch_id>0)
		{
			$each_test_str.=" AND e.`branch_id`='$branch_id'";
		}
		
		$each_test_str.=" GROUP BY a.`patient_id`,a.`opd_id` ORDER BY c.`tot_amount` DESC";
		
		$max=1;
		$n=0;
		$single_test_pat=0;
		$pat_each_test_qry=mysqli_query($link, $each_test_str);
		while($pat_each_test=mysqli_fetch_array($pat_each_test_qry))
		{
			$patient_id=$pat_each_test["patient_id"];
			$opd_id=$pat_each_test["opd_id"];
			
			$non_cash=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`!='Cash'"));
			if(!$non_cash)
			{
				$test_str="SELECT a.`slno` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`!='$testid' AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id' AND a.`test_rate`>0";
				
				if($branch_id>0)
				{
					$test_str.=" AND e.`branch_id`='$branch_id'";
					$del_test_str=" AND `branch_id`='$branch_id'";
				}
				$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
				//echo $test_str."<br>";
				
				$each_test_qry=mysqli_query($link, $test_str);
				$each_test_num=mysqli_num_rows($each_test_qry);
				
				if($each_test_num==0)
				{
					$test_str="SELECT a.`slno` FROM `patient_test_details` a JOIN `invest_patient_payment_details` c JOIN `payment_detail_all` d JOIN `uhid_and_opdid` e ON a.`patient_id`=c.`patient_id` AND a.`opd_id`=c.`opd_id` AND a.`patient_id`=d.`patient_id` AND a.`opd_id`=d.`opd_id` AND a.`patient_id`=e.`patient_id` AND a.`opd_id`=e.`opd_id` WHERE c.`balance`=0 AND c.`dis_amt`=0 AND d.`payment_mode` NOT IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `p_mode_name`!='Cash') AND c.`date` BETWEEN '$date1' AND '$date2' AND a.`testid`='$testid' AND a.`test_rate`>0  AND a.`patient_id`='$patient_id' AND a.`opd_id`='$opd_id'";
					
					if($branch_id>0)
					{
						$test_str.=" AND e.`branch_id`='$branch_id'";
					}
					$test_str.=" GROUP BY a.`patient_id`,a.`opd_id`,a.`testid`";
					//echo $test_str."<br>";
					
					$each_test_qry=mysqli_query($link, $test_str);
					$each_test_num=mysqli_num_rows($each_test_qry);
					
					if($each_test_num>0)
					{
						if($n<$replace_test_no)
						{
							$test_det=mysqli_fetch_array(mysqli_query($link, "SELECT `test_rate`,`date` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'"));
							
							if($test_det)
							{
								$test_info_new=mysqli_fetch_array(mysqli_query($link, "SELECT `rate` FROM `testmaster` WHERE `testid`='$testid_replace'"));
								
								$test_rate_diff=$test_det["test_rate"]-$test_info_new["rate"];
								if($test_rate_diff>=0)
								{
									if(mysqli_query($link, "INSERT INTO `test_details_data_replace`(`branch_id`, `patient_id`, `opd_id`, `testid`, `test_rate`, `testid_new`, `test_rate_new`, `test_rate_diff`, `test_date`, `user`, `date`, `time`) VALUES ('$branch_id','$patient_id','$opd_id','$testid','$test_det[test_rate]','$testid_replace','$test_info_new[rate]','$test_rate_diff','$test_det[date]','$c_user','$date','$time')"))
									{
										
										mysqli_query($link, "UPDATE `patient_test_details` SET `testid`='$testid_replace',`test_rate`='$test_info_new[rate]' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										
										mysqli_query($link, "DELETE FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `phlebo_sample` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_update` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_cancel` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_delete` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_card` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_card_cancel` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_rad` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_rad_cancel` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_rad_delete` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testresults_sample_stat` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `test_sample_result` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `test_sample_result_repeat` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `approve_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `testreport_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										mysqli_query($link, "DELETE FROM `patient_test_summary` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `testid`='$testid'");
										if($testid==1227)
										{
											mysqli_query($link, "DELETE FROM `widalresult` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
											mysqli_query($link, "DELETE FROM `widalresult_cancel` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
										}
										
										// Accounts
										$test_amount=0;
										$vaccu_amount=0;
										$tot_amount=0;
										$test_det=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`test_rate`),0) AS `tot_test` FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
										$test_amount=$test_det["tot_test"];
										
										$vaccu_det=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`rate`),0) AS `tot_vaccu` FROM `patient_vaccu_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
										$vaccu_amount=$vaccu_det["tot_vaccu"];
										
										$tot_amount=$test_amount+$vaccu_amount;
										
										mysqli_query($link, "UPDATE `invest_patient_payment_details` SET `tot_amount`='$tot_amount',`advance`='$tot_amount' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'");
										
										mysqli_query($link, "DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`!='Advance'");
										
										mysqli_query($link, "DELETE FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_mode`='Credit'");
										
										$pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT `pay_id` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Advance'"));
										if($pay_det)
										{
											mysqli_query($link, "UPDATE `payment_detail_all` SET `payment_mode`='Cash',`amount`='$tot_amount' WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `payment_type`='Advance'");
										}
										else
										{
											$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id'"));
											
											$p_type_id=$pat_reg["type"];

											$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));
											$bill_name=$pat_typ_text["bill_name"];
											
											$bill_no=generate_bill_no_new($bill_name,$p_type_id);
											
											mysqli_query($link, " INSERT INTO `payment_detail_all`(`patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$patient_id','$opd_id','$bill_no','$tot_amount','0','$tot_amount','0','','0','','0','','0','','Advance','Cash','','$pat_reg[user]','$pat_reg[date]','$pat_reg[time]','$p_type_id') ");
										}
										$n++;
									}
								}
							}
						}
						else
						{
							break;
						}
					}
					$max++;
					if($max>2000)
					{
						break;
					}
				}
			}
		}
		if($n==0)
		{
			echo "404@0 Test Replaced";
		}
		else
		{
			echo "101@".$n." Test(s) Replaced";
		}
	}
	else
	{
		echo "404@Can't exceed than Single Test Patient";
	}
}
if($type=="load_deleted_record")
{
	$date1=$_POST["date1"];
	$date2=$_POST["date2"];
	$branch_id=$_POST["branch_id"];
	
	$str="SELECT COUNT(`slno`) AS `test_no`,ifnull(SUM(`test_rate`),0) AS `test_amount` FROM `test_details_data` WHERE `test_date` BETWEEN '$date1' AND '$date2'";
	if($branch_id>0)
	{
		$str.=" AND `branch_id`='$branch_id'";
	}
	
	$del_test=mysqli_fetch_array(mysqli_query($link, $str));
	
	$str="SELECT ifnull(SUM(`test_rate_diff`),0) AS `test_amount` FROM `test_details_data_replace` WHERE `test_date` BETWEEN '$date1' AND '$date2'";
	if($branch_id>0)
	{
		$str.=" AND `branch_id`='$branch_id'";
	}
	
	$replace_test=mysqli_fetch_array(mysqli_query($link, $str));
	
	$replace_str="";
	if($replace_test["test_amount"])
	{
		$replace_str="Replace Test Save Amount: ".$replace_test["test_amount"];
		
		$total_amount_str=" Total amount: ".number_format($del_test["test_amount"]+$replace_test["test_amount"],2);
	}
	
	echo "<b>Delete Test No.: ".$del_test["test_no"]." &nbsp;&nbsp;&nbsp;&nbsp; Delete Test Amount: ".$del_test["test_amount"]." &nbsp;&nbsp;&nbsp;&nbsp; ".$replace_str." &nbsp;&nbsp;&nbsp;&nbsp; ".$total_amount_str."</b>";
}
?>
