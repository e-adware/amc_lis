<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST['type'];
$p_type_id=$_POST['p_type_id'];

if($type==1) // OPD
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	if($typ=="name")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `name` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="phone")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `phone` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="uhid")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` like '$val%' order by `slno` DESC ";
		}
	}
	if($typ=="opd_serial")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` IN ( SELECT `patient_id` FROM `uhid_and_opdid` WHERE `uhid` like '$val%' ) order by `slno` DESC ";
		}
		
	}
	if($typ=="pin")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` IN ( SELECT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id` like '$val%' ) order by `slno` DESC ";
		}
	}
	
	//echo $q;

	$qry=mysqli_query($link, $q);
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>UHID</th>
			<th>Phone</th>
		</tr>
	<?php
	$i=1;
	while($q=mysqli_fetch_array($qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','<?php echo $typ;?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@".$typ;?>"/></td>
			<td><?php echo $pat_info['phone'];?></td>
		</tr>
		<?php	
		$i++;
	}
	?>
	</table>
<?php
}
if($type==5) // LAB
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	if($typ=="name")
	{
		if(strlen($val)>2)
		{
			$z=1;
			$q="SELECT * FROM `patient_info` WHERE `name` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="phone")
	{
		if(strlen($val)>2)
		{
			$z=1;
			$q="SELECT * FROM `patient_info` WHERE `phone` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="uhid")
	{
		if(strlen($val)>2)
		{
			$z=1;
			$q="SELECT * FROM `patient_info` WHERE `patient_id` LIKE '$val%' order by `slno` DESC ";
		}
	}
	if($typ=="pin")
	{
		if(strlen($val)>2)
		{
			$z=2;
			$q=" SELECT DISTINCT a.`patient_id`, a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`opd_id` LIKE '$val%' ";
		}
	}
	//echo $q;
	$qry=mysqli_query($link, $q);
	if($z==1)
	{
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>UHID</th>
			<th>Phone</th>
		</tr>
		<?php
		$i=1;
		while($q=mysqli_fetch_array($qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
			
			$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
			
		?>
			<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','0','<?php echo $typ;?>')" style="cursor:pointer;">
				<td><?php echo $i;?></td>
				<td><?php echo $pat_info['name'];?></td>
				<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@0@@".$typ;?>"/></td>
				<td><?php echo $pat_info['phone'];?></td>
			</tr>
			<?php	
			$i++;
		}
	?>
	</table>
<?php
	}
	if($z==2)
	{
?>
	<table class="table table-condensed table-bordered">
		<tr><th>#</th><th>Name</th><th>UHID</th><th>PIN</th></tr>
		<?php
		$i=1;
		while($q=mysqli_fetch_array($qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
			
			$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
			
		?>
			<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','<?php echo $q['opd_id'];?>','<?php echo $typ;?>')" style="cursor:pointer;">
				<td><?php echo $i;?></td>
				<td><?php echo $pat_info['name'];?></td>
				<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@".$q['opd_id']."@@".$typ;?>"/></td>
				<td><?php echo $q['opd_id'];?></td>
			</tr>
			<?php	
			$i++;
		}
	?>
	</table>
<?php
	}
}
if($type==7) // IPD
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	if($typ=="name")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `name` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="phone")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `phone` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="uhid")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` LIKE '$val%' order by `slno` DESC ";
		}
	}
	if($typ=="pin")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` IN ( SELECT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id` like '$val%' ) order by `slno` DESC ";
		}
	}

	//echo $q;

	$qry=mysqli_query($link, $q);
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>UHID</th>
			<th>Phone</th>
		</tr>
	<?php
		$i=1;
		while($q=mysqli_fetch_array($qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
			
			$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
			
		?>
			<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','<?php echo $typ;?>')" style="cursor:pointer;">
				<td><?php echo $i;?></td>
				<td><?php echo $pat_info['name'];?></td>
				<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@".$typ;?>"/></td>
				<td><?php echo $pat_info['phone'];?></td>
			</tr>
		<?php	
			$i++;
		}
	?>
	</table>
<?php
}
if($type==8)
{
	$uhid=$_POST["uhid"];
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` LEFT JOIN `patient_info_rel` ON `patient_info`.`patient_id`=`patient_info_rel`.`patient_id` WHERE `patient_info`.`patient_id`='$uhid' "));
	
	$pat_info_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$uhid' "));
	
	//$ipd_visit_check=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `type`='3' "));
	$ipd_visit_check=1;
	if($ipd_visit_check>0)
	{
		$admit_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' "));
		if($admit_pat_num>0)
		{
			$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
			$at_ad_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ipd_id`,`attend_doc`, `admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' ORDER BY `slno` DESC "));
			
			$at_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$at_ad_doc[attend_doc]' "));
			$ad_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$at_ad_doc[admit_doc]' "));
			
			$bed=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' ORDER BY `slno` DESC "));
			
			$already_admitted="1###".$at_doc["Name"]."-".$at_doc["consultantdoctorid"]."###".$ad_doc["Name"]."-".$ad_doc["consultantdoctorid"]."###".$ref_doc["ref_name"]."-".$pat_info["refbydoctorid"]."###".$bed["bed_id"]."###".$at_ad_doc["ipd_id"];
		}else
		{
			$already_admitted="0###";
		}
	}else
	{
		$already_admitted="0###";
	}
	
	$visit_type_id=0;
	if($p_type_id==4)
	{
		$visit_type_id=2;
		$visit_validity=15;
		
		$group_id=186;
		$charge_id=251;
		$pat_casu_serv_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `group_id`='$group_id' AND `service_id`='$charge_id' AND `bed_id`='0' AND `amount`>0 ORDER BY `slno` DESC LIMIT 1 "));
		if($pat_casu_serv_det)
		{
			$dates_array = getDatesFromRange($pat_casu_serv_det["date"], $date);
			$visit_fee_day_diff=sizeof($dates_array);
			if($visit_fee_day_diff<=$visit_validity)
			{
				$visit_type_id=7; //free
			}else
			{
				$visit_type_id=8;
			}
		}
	}
	if($p_type_id==6)
	{
		$visit_type_id=5;
		$visit_validity=15;
		
		$group_id=188;
		$charge_id=253;
		$pat_casu_serv_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `group_id`='$group_id' AND `service_id`='$charge_id' AND `bed_id`='0' AND `amount`>0 ORDER BY `slno` DESC LIMIT 1 "));
		if($pat_casu_serv_det)
		{
			$dates_array = getDatesFromRange($pat_casu_serv_det["date"], $date);
			$visit_fee_day_diff=sizeof($dates_array);
			if($visit_fee_day_diff<=$visit_validity)
			{
				$free_service_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `group_id`='$group_id' AND `service_id`='$charge_id' AND `bed_id`='0' AND `slno`>'$pat_casu_serv_det[slno]' "));
				if($free_service_num>3)
				{
					$visit_type_id=15;
				}
				else
				{
					$visit_type_id=14;  //free
				}
			}else
			{
				$visit_type_id=15;
			}
		}
	}
	if($p_type_id==7)
	{
		$visit_type_id=4;
		$visit_validity=15;
		
		$group_id=187;
		$charge_id=252;
		$pat_casu_serv_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `group_id`='$group_id' AND `service_id`='$charge_id' AND `bed_id`='0' AND `amount`>0 ORDER BY `slno` DESC LIMIT 1 "));
		if($pat_casu_serv_det)
		{
			$dates_array = getDatesFromRange($pat_casu_serv_det["date"], $date);
			$visit_fee_day_diff=sizeof($dates_array);
			if($visit_fee_day_diff<=$visit_validity)
			{
				$free_service_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' AND `group_id`='$group_id' AND `service_id`='$charge_id' AND `bed_id`='0' AND `slno`>'$pat_casu_serv_det[slno]' "));
				if($free_service_num>3)
				{
					$visit_type_id=16;
				}
				else
				{
					$visit_type_id=9;  //free
				}
			}else
			{
				$visit_type_id=16;
			}
		}
	}
	
	echo $pat_info["name"]."@#@".$pat_info["gd_name"]."@#@".$pat_info["sex"]."@#@".$pat_info["dob"]."@#@".$pat_info["age"]."@#@".$pat_info["age_type"]."@#@".$pat_info["phone"]."@#@".$pat_info["address"]."@#@".$pat_info["gd_phone"]."@#@".$pat_info["pin"]."@#@".$pat_info["police"]."@#@".$pat_info["state"]."@#@".$pat_info["district"]."@#@".$pat_info["city"]."@#@".$pat_info["post_office"]."@#@".$uhid."@#@".$already_admitted."@#@".$pat_info_other["marital_status"]."@#@".$pat_info_other["relation"]."@#@".$pat_info_other["source_id"]."@#@".$pat_info_other["esi_ip_no"]."@#@".$pat_info_other["income_id"]."@#@".$visit_type_id;
	
}
if($type==9)
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	if($typ=="name")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `name` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="phone")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `phone` like '%$val%' order by `slno` DESC";
		}
	}
	if($typ=="uhid")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` LIKE '$val%' order by `slno` DESC ";
		}
	}
	if($typ=="pin")
	{
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` IN ( SELECT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id` like '$val%' ) order by `slno` DESC ";
		}
	}

	//echo $q;
	
	$qry=mysqli_query($link, $q);
?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>UHID</th>
			<th>Phone</th>
		</tr>
	<?php
	$i=1;
	while($q=mysqli_fetch_array($qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','<?php echo $typ;?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@".$typ;?>"/></td>
			<td><?php echo $pat_info['phone'];?></td>
		</tr>
		<?php	
		$i++;
	}
	?>
	</table>
<?php
}
if($type==10)
{
	$uhid=$_POST["uhid"];
	//$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` AS m JOIN `patient_info_rel` AS r ON m.`patient_id`=r.`patient_id` WHERE m.`patient_id`='$uhid' "));
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` LEFT JOIN `patient_info_rel` ON `patient_info`.`patient_id`=`patient_info_rel`.`patient_id` WHERE `patient_info`.`patient_id`='$uhid' "));
	
	//~ $ipd_visit_check=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `type`='3' "));
	//~ if($ipd_visit_check>0)
	//~ {
		//~ $bed_alloc_num=mysqli_num_rows(mysqli_query($link, " SELECT distinct `ipd_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' "));
		//~ $discharge_num=mysqli_num_rows(mysqli_query($link, " SELECT distinct `ipd_id` FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' "));
		//~ if($bed_alloc_num>$discharge_num)
		//~ {
			//~ $ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
			//~ $at_ad_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ipd_id`,`attend_doc`, `admit_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' ORDER BY `slno` DESC "));
			
			//~ $at_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$at_ad_doc[attend_doc]' "));
			//~ $ad_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$at_ad_doc[admit_doc]' "));
			
			//~ $bed=mysqli_fetch_array(mysqli_query($link, " SELECT `bed_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' ORDER BY `slno` DESC "));
			
			//~ //$already_admitted="1###".$at_doc["Name"]."-".$at_doc["consultantdoctorid"]."###".$ad_doc["Name"]."-".$ad_doc["consultantdoctorid"]."###".$ref_doc["ref_name"]."-".$pat_info["refbydoctorid"]."###".$bed["bed_id"]."###".$at_ad_doc["ipd_id"];
		//~ }else
		//~ {
			//~ //$already_admitted="0###";
		//~ }
	//~ }else
	//~ {
		//~ //$already_admitted="0###";
	//~ }
	
	echo $pat_info["name"]."@#@".$pat_info["gd_name"]."@#@".$pat_info["sex"]."@#@".$pat_info["dob"]."@#@".$pat_info["age"]."@#@".$pat_info["age_type"]."@#@".$pat_info["phone"]."@#@".$pat_info["address"]."@#@".$pat_info["gd_phone"]."@#@".$pat_info["pin"]."@#@".$pat_info["police"]."@#@".$pat_info["state"]."@#@".$pat_info["district"]."@#@".$pat_info["city"]."@#@".$pat_info["post_office"]."@#@".$uhid;
	
}

if($type==11)
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	if($typ=="name")
	{
		$q="SELECT * FROM `patient_info` WHERE `name` like '%$val%' AND `patient_id` in ( SELECT `patient_id` FROM `ipd_pat_bed_details` ) order by `slno` DESC";
	}
	if($typ=="uhid")
	{
		$q=" SELECT * FROM `patient_info` WHERE `patient_id` like '$val%' AND `patient_id` in ( SELECT `patient_id` FROM `ipd_pat_bed_details` ) order by `slno` DESC ";
	}
	//echo $q;
	$qry=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
	<th>#</th><th>UHID</th><th>Name</th>
	<?php
	$i=1;
	while($pat_info=mysqli_fetch_array($qry))
	{
		//$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pat_info[patient_id]' ORDER BY `slno` DESC "));
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $pat_info['patient_id'];?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td>
				<?php echo $pat_info['patient_id'];?>
				<input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $pat_info['patient_id'];?>"/>
			</td>
			<td><?php echo $pat_info['name'];?></td>
		</tr>
		<?php	
		$i++;
	}
	?>
	</table>
<?php
}

if($type==12) // OPD
{
	$val=$_POST['val'];
	$typ=$_POST['typ'];
	if($typ=="name")
	{
		//$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$val%' ) order by `opd_id` DESC";
		$q="SELECT * FROM `patient_info` WHERE `name` like '%$val%' order by `slno` DESC";
	}
	if($typ=="fname")
	{
		//$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `file_no` like '$val%' ) AND `type`='1' ";
		$q="SELECT * FROM `patient_info_rel` WHERE `file_no` like '$val%' order by `slno` DESC";
	}
	if($typ=="uhid")
	{
		//$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id` like '$val%' order by `opd_id` DESC ";
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` like '$val%' order by `slno` DESC ";
		}
	}
	if($typ=="opd_serial")
	{
		//$q="SELECT * FROM `uhid_and_opdid` WHERE `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `uhid` like '$val%' ) AND `type`='1' order by `opd_id` DESC ";
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` IN ( SELECT `patient_id` FROM `uhid_and_opdid` WHERE `uhid` like '$val%' ) order by `slno` DESC ";
		}
		
	}
	if($typ=="pin")
	{
		//$q="SELECT * FROM `uhid_and_opdid` WHERE `opd_id` like '$val%' AND `type`='1' order by `opd_id` DESC ";
		if(strlen($val)>2)
		{
			$q="SELECT * FROM `patient_info` WHERE `patient_id` IN ( SELECT `patient_id` FROM `uhid_and_opdid` WHERE `opd_id` like '$val%' ) order by `slno` DESC ";
		}
	}
	//echo $q;
	$qry=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
		<tr><th>#</th><th>Name</th><th>UHID</th></tr>
	<?php
	$i=1;
	while($q=mysqli_fetch_array($qry))
	{
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$q[patient_id]' "));
		
		$pat_type=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND `opd_id`='$q[opd_id]' "));
		
	?>
		<tr id="row_id<?php echo $i;?>" class="all_pat_row" onClick="load_emp_details('<?php echo $q['patient_id'];?>','<?php echo $typ;?>')" style="cursor:pointer;">
			<td><?php echo $i;?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $pat_info['patient_id'];?><input type="hidden" id="e_id<?php echo $i;?>" value="<?php echo $q['patient_id']."@@".$typ;?>"/></td>
		</tr>
		<?php	
		$i++;
	}
	?>
	</table>
<?php
}
?>
