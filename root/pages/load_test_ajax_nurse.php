<?php
session_start();

include("../../includes/connection.php");
require('../../includes/global.function.php');

date_default_timezone_set("Asia/Kolkata");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d"); // important
$time=date("H:i:s");

$type=$_POST["type"];

if($type=="search_test")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	$test=$_POST['test'];
	$centreno=$_POST['centreno'];
	
	$last_day_7=date ( 'Y-m-d' , strtotime ( $date . ' - 6 days' )); // Including today(7-1=6)
	$ent_procedure_ids = array("2970", "2971", "2972"); // ENT Procedure
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	
	//$centreno=$pat_reg["center_no"];

	if($test=="")
	{
		//$str="select * from testmaster order by testname";
	}
	else
	{
		$str="select * from testmaster where testname like '%$test%' order by testname";
	}

	$qry=mysqli_query($link, $str);
	$qry_num=mysqli_num_rows($qry);
	if($qry_num>0)
	{
?>

	<table class="table   table-bordered table-condensed" border="1" id="test_table" width="100%">
		<tr>
			<th>#</th>
			<th>Test Name</th>
			<th style="text-align:right;">Rate</th>
			<th>Department</th>
			<div id="msgg" style="display:none;position:absolute;top:15%;left:45%;font-size:22px;color:#d00;"></div>
		</tr>
<?php
		$i=1;
		while($data=mysqli_fetch_array($qry))
		{
			$dept_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `test_department` WHERE `id`='$data[type_id]' AND `category_id`='$data[category_id]'"));
			
			$rate=mysqli_fetch_array(mysqli_query($link, "select rate from testmaster_rate where testid='$data[testid]' and centreno='$centreno'"));
			if($rate['rate'])
			{
				$drate=$rate['rate'];
			}
			else
			{
				$drate=$data['rate'];
			}
			
			//$drate=$d['rate'];
			
			//if($d["type_id"]==148) // ENT
			if(in_array($data["testid"], $ent_procedure_ids))
			{
				$pat_last_procedure=mysqli_fetch_array(mysqli_query($link, " SELECT `date` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `testid` IN($data[testid]) AND `date`>='$last_day_7' ORDER BY `slno` DESC LIMIT 1 "));
				if($pat_last_procedure)
				{
					$drate=$drate/2;
				}
			}
		?>
			<tr <?php echo "id=td".$i;?> onclick="load_test_new('<?php echo $data['testid']; ?>','<?php echo mysqli_real_escape_string($link, $data['testname']); ?>','<?php echo $drate; ?>')" style="cursor:pointer">
				<td width="5%" class=test<?php echo $i;?> id=test<?php echo $i;?>>
					<?php echo $i;?><input type="hidden" class="test<?php echo $i;?>" value="<?php echo $data['testid'];?>"/>
				</td>
				<td style="text-align:left" width="35%" <?php echo "class=test".$i;?>>
					<?php echo $data['testname'];?>
				</td>
				<td style="text-align:right" width="35%" <?php echo "class=test".$i;?>>
					<?php echo $drate;?>
				</td>
				<td style="text-align:left" width="35%" <?php echo "class=test".$i;?>>
					<?php echo $dept_info["name"];?>
				</td>
			</tr>
			<?php
			$i++;
		}
?>
	</table>
<?php
	}
}
if($type=="load_item_table")
{
?>
	<div>
		<table class="table table-responsive table-bordered table-condensed" id="test_list">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 5%">#</th>
					<th>Test Name</th>
					<th style="width: 15%;">Rate</th>
					<th class="test_discount" style="width: 15%;display:none;">Discount</th>
					<th style="width: 20%;">
						<i class="icon-cogs"></i>
						<!--<b style="float:right;">Total: <span id="item_total_amount_tbl">0</span></b>-->
					</th>
				</tr>
			</thead>
			<tr id="item_footer"></tr>
		</table>
	</div>
<?php
}

if($type=="add_items")
{
	$testid=mysqli_real_escape_string($link, $_POST["testid"]);
	$test_name=mysqli_real_escape_string($link, $_POST["test_name"]);
	$test_rate=mysqli_real_escape_string($link, $_POST["test_rate"]);
	$tr_counter=mysqli_real_escape_string($link, $_POST["tr_counter"]);
	$c_discount=mysqli_real_escape_string($link, $_POST["c_discount"]);
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$user' "));
	
	$test_det=mysqli_fetch_array(mysqli_query($link, " SELECT `testid`,`testname`,`rate`,`type_id` FROM `testmaster` WHERE `testid`='$testid' "));
	
	$rate_attribute="disabled";
	if($test_det["type_id"]=="147")
	{
		$rate_attribute="";
	}
	
	$discount_attribute="";
	if($emp_access["discount_permission"]==0)
	{
		$discount_attribute="readonly";
	}
	if($c_discount>0)
	{
		$discount_attribute="readonly";
	}
	
	$discount_each=0;
?>
	<tr id="tbl_tr<?php echo $tr_counter; ?>" class="item_cls">
		<td>
			<?php echo $tr_counter; ?>
			<input class="form-control each_row list_cls" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
		</td>
		<td>
			<?php echo $test_name; ?>
			<input class="form-control test_name list_cls" type="hidden" name="test_name<?php echo $tr_counter; ?>" id="test_name<?php echo $tr_counter; ?>" value="<?php echo $test_name; ?>" onkeyup="test_name_each(event,'<?php echo $tr_counter; ?>')" disabled>
			
			<input type="hidden" class="form-control testid" id="testid<?php echo $tr_counter; ?>" value="<?php echo $testid; ?>">
		</td>
		<td>
			<input class="form-control span1 numericc test_rate list_cls" type="text" name="test_rate<?php echo $tr_counter; ?>" id="test_rate<?php echo $tr_counter; ?>" value="<?php echo $test_rate; ?>" onkeyup="test_rate_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $rate_attribute; ?> style="padding: 2px;margin-bottom: 0;text-align:right;">
		</td>
		<td class="test_discount" style="display:none;">
			<input class="form-control span1 numericc discount_each list_cls" type="text" name="discount_each<?php echo $tr_counter; ?>" id="discount_each<?php echo $tr_counter; ?>" value="<?php echo $discount_each; ?>" onkeyup="discount_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $discount_attribute; ?>>
		</td>
		<td>
			<button class="btn btn-danger btn-mini remove_btn_cls" id="remove_btn<?php echo $tr_counter; ?>" onclick="remove_tr('<?php echo $tr_counter; ?>')"><i class="icon-remove"></i></button>
		</td>
	</tr>
<?php
}

if($type=="save_ipd_pat_test")
{
	//print_r($_POST);
	
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	$consultantdoctorid=0;
	$refbydoctorid=$_POST['refbydoctorid'];
	$centreno=$_POST['centreno'];
	$ward_id=$_POST['ward_id'];
	$usr=$_POST['usr'];
	$tst=$_POST['tst'];
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	$center_no=$pat_reg["center_no"];
	
	//~ $test=explode(",",$tst);
	//~ $ar=sizeof($test);
	
	if($batch>0)
	{
		$bch=$batch;
	}
	else
	{
		$bt=mysqli_fetch_array(mysqli_query($link,"SELECT MAX(batch_no) as max FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		$bch=$bt['max']+1;
	}
	
	$test_entry_date=$date;
	$test_entry_time=$time;
	
	$del_test_qry=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' ");
	while($del_test=mysqli_fetch_array($del_test_qry))
	{
		$test_entry_date=$del_test["date"];
		$test_entry_time=$del_test["time"];
		
		$slno_service=mysqli_fetch_array(mysqli_query($link," SELECT `service_slno` FROM `link_test_service` WHERE `test_slno`='$del_test[slno]' "));
		
		mysqli_query($link," DELETE FROM `patient_test_details` WHERE `slno`='$del_test[slno]'");
		mysqli_query($link," DELETE FROM `ipd_pat_service_details_sub` WHERE `test_slno`='$del_test[slno]'");
		mysqli_query($link," DELETE FROM `link_test_service` WHERE `slno_service`='$slno_service[service_slno]'");
	}
	
	// Ref Doctor
	$ref_doc_val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' "));
	if($ref_doc_val)
	{
		mysqli_query($link," UPDATE `ipd_test_ref_doc` SET `refbydoctorid`='$refbydoctorid',`date`='$test_entry_date',`time`='$test_entry_time',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' ");
	}else
	{
		mysqli_query($link," DELETE FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' ");
		
		mysqli_query($link," INSERT INTO `ipd_test_ref_doc`(`patient_id`, `ipd_id`, `batch_no`, `consultantdoctorid`, `refbydoctorid`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$consultantdoctorid','$refbydoctorid','$test_entry_date','$test_entry_time','$usr') ");
	}
	
	//mysqli_query($link," DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch'");
	
	$test_all=explode("##",$tst	);
	foreach($test_all AS $test)
	{
		if($test)
		{
			$test=explode("@",$test);
			$test_id=$test[0];
			$test_rate=$test[1];
			$test_discount=$test[2];
			
			if($test_id)
			{
				if(!$test_discount)
				{
					$test_discount=round((($test_rate*$dis_per)/100),2);
				}
				
				if(!$test_rate){ $test_rate=0; }
				if(!$test_discount){ $test_discount=0; }
				
				$sam=mysqli_fetch_array(mysqli_query($link,"SELECT `SampleId` FROM `TestSample` WHERE `TestId`='$test_id'"));
				$rt=mysqli_fetch_array(mysqli_query($link,"SELECT `testname`,`rate`,`category_id` FROM `testmaster` WHERE `testid`='$test_id'"));
				
				$ser_name=mysqli_real_escape_string($link, $rt["testname"]);
				
				$group_id=104;
				if($rt["category_id"]==2)
				{
					$group_id=151;
				}
				if($rt["category_id"]==3)
				{
					$group_id=150;
				}
				
				$sample_id=$sam["SampleId"];
				if(!$sample_id){ $sample_id=0; }
				
				if(mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$bch','$test_id','$sample_id','$test_rate','0','$test_rate','0','$test_entry_date','$test_entry_time','$usr','4')")) // 4 = nursing dashboard
				{
					$last_slno_test=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `testid`='$test_id' AND `user`='$usr' ORDER BY `slno` DESC "));
					
					// Insert in Sub `ipd_pat_service_details`
					mysqli_query($link," INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$uhid','$ipd','$group_id','$test_id','$ser_name','1','$test_rate','$test_rate','0','$usr','$test_entry_time','$test_entry_date','0','$consultantdoctorid','$refbydoctorid','$last_slno_test[slno]') ");
					
					//$last_slno_service=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `ipd_pat_service_details_sub` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `group_id`='104' AND `service_id`='$test' ORDER BY `slno` DESC "));
					
					//mysqli_query($link," INSERT INTO `link_test_service`(`test_slno`, `service_slno`) VALUES ('$last_slno_test[slno]','$last_slno_service[slno]') ");
					
					// Add On Test
					$add_on=0;
					$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test_id' ");
					while($s_t=mysqli_fetch_array($sub_tst))
					{
						$testmaster_rate=mysqli_fetch_array(mysqli_query($link, " SELECT `rate` FROM `testmaster` WHERE `testid`='$s_t[sub_testid]' "));
						
						$rate=$testmaster_rate["rate"];
						
						$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
						
						$sample_id=$samp_sb["SampleId"];
						if(!$sample_id){ $sample_id=0; }
						
						if(mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `amount`, `addon_testid`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$bch','$s_t[sub_testid]','$sample_id','$rate','0','$rate','$test_id','$date','$time','$user','4') "))
						{
							$add_on++;
						}
					}
				}
			}
		}
	}
}

if($type=="show_sel_tests_ipd")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	
	if(!$batch){ $batch=0; }
	
	$ref_doc_val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' "));
	
	if(!$ref_doc_val)
	{
		$ref_doc_consult=mysqli_fetch_array(mysqli_query($link,"SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'  "));
		$attend_doc=$ref_doc_consult["attend_doc"];
		if($attend_doc==0)
		{
			$ref_doc_val['refbydoctorid']=101;
		}
		else
		{
			$ref_doc_consult=mysqli_fetch_array(mysqli_query($link, "SELECT `refbydoctorid`  FROM `refbydoctor_master` WHERE `consultantdoctorid` = '$attend_doc'"));
			
			$ref_doc_val['refbydoctorid']=$ref_doc_consult["refbydoctorid"];
		}
	}
?>
	<div id="test_sel">
		<!--<div id="list_all_test" style="" class="up_div"></div>-->
		<!--<h5 class="text-left" onClick="load_tab(2,'a')">Test Details For</h5>-->
		<table class="table">
			<tr>
				<th><label for="test">Select Test</label></th>
				<td>
					<input type="text" name="test" id="test" class="span6" onFocus="test_enable()" onKeyUp="select_test_new(this.value,event)" />
					<input type="text" name="batch" id="batch" style="display:none;" value="<?php echo $batch;?>" />
				</td>
				<td>
					<select id="ipd_test_centreno" style="display:none;">
						<!--<option value="0">Select</option>-->
				<?php
					$qry=mysqli_query($link, "SELECT `centreno`,`centrename` FROM `centremaster` WHERE `centreno`='C100' OR `centreno` IN(SELECT DISTINCT `centreno` FROM `testmaster_rate`) ORDER BY `centreno` ASC"); // AND `centreno` IN('C100','C103','C104')
					while($data=mysqli_fetch_array($qry))
					{
						if($ref_doc_val["centreno"]==$data["centreno"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$data[centreno]' $sel>$data[centrename]</option>";
					}
				?>
					</select>
				</td>
				<td style="display:none">
					<select id="ipd_test_ward_no">
						<option value="0">Select Ward</option>
				<?php
					$qry=mysqli_query($link, "SELECT `ward_id`, `ward_name` FROM `ipd_test_ward_master` WHERE `ward_name`!='' ORDER BY `ward_name` ASC");
					while($data=mysqli_fetch_array($qry))
					{
						if($ref_doc_val["ward_id"]==$data["ward_id"]){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$data[ward_id]' $sel>$data[ward_name]</option>";
					}
				?>
					</select>
				</td>
				<th><label for="test">Doctor</label></th>
				<td>
					<select id="ipd_test_ref_doc">
						<!--<option value="0">Select</option>-->
				<?php
					$ipd_test_ref_qry=mysqli_query($link," SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` ORDER BY `ref_name` ");
					while($ipd_test_ref=mysqli_fetch_array($ipd_test_ref_qry))
					{
						if(!$ref_doc_val)
						{
							$ref_doc_val['refbydoctorid']=101;
						}
						if($ref_doc_val['refbydoctorid']==0)
						{
							$ref_doc_val['refbydoctorid']=101;
						}
						if($ipd_test_ref['refbydoctorid']==$ref_doc_val['refbydoctorid']){ $sel="selected"; }else{ $sel=""; }
						echo "<option value='$ipd_test_ref[refbydoctorid]' $sel >$ipd_test_ref[ref_name]</option>";
					}
				?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<div id="test_d">
						
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?php
		$qry=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' ");
		$item_num=mysqli_num_rows($qry);
		$item_num=$item_num+1;
	?>
	<input type="hidden" name="tr_counter" id="tr_counter" class="form-control" value="<?php echo $item_num; ?>"/>
	<div id="ss_tests">
<?php
	$num=mysqli_num_rows($qry);
	if($num>0)
	{
?>
		<table class="table table-responsive table-bordered table-condensed" id="test_list">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 5%">#</th>
					<th>Test Name</th>
					<th style="width: 15%;">Rate</th>
					<th class="test_discount" style="width: 15%;display:none;">Discount</th>
					<th style="width: 20%;">
						<i class="icon-cogs"></i>
						<!--<b style="float:right;">Total: <span id="item_total_amount_tbl">0</span></b>-->
					</th>
				</tr>
			</thead>
		<?php
			$i=1;
			$tr_counter=1;
			while($test_val=mysqli_fetch_array($qry))
			{
				$testid=$test_val["testid"];
				$test_rate=$test_val["test_rate"];
				$discount_each=$test_val["test_discount"];
				
				$test_det=mysqli_fetch_array(mysqli_query($link, " SELECT `testid`,`testname`,`rate`,`type_id` FROM `testmaster` WHERE `testid`='$testid' "));
				
				$rate_attribute="disabled";
				if($test_det["type_id"]=="147")
				{
					$rate_attribute="";
				}
				
				$test_name=$test_det["testname"];
				
				if($opd_id=="0" && $opd_clinic_test>0)
				{
					$test_rate=$test_det["rate"];
					
					$test_centre=mysqli_fetch_array(mysqli_query($link, " SELECT `testid`,`rate` FROM `testmaster_rate` WHERE `testid`='$testid' AND `centreno`='$center_no' "));
					if($test_centre)
					{
						$test_rate=$test_centre["rate"];
					}
					$discount_each=0;
				}
				
				$report_done=0;
				$testresult_path_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$testid' "));
				$testresult_card_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_card` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$testid' "));
				$testresult_radi_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$testid' "));
				if($test_val["testid"]==1227)
				{
					$testresult_wild_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `widalresult` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$testid' "));
				}
				$testresult_summ_num=mysqli_num_rows(mysqli_query($link, " SELECT `slno` FROM `patient_test_summary` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' AND `testid`='$testid' "));
				
				if($testresult_path_num>0 || $testresult_card_num>0 || $testresult_radi_num>0 || $testresult_wild_num>0 || $testresult_summ_num>0)
				{
					$report_done=1;
				}
			?>
			<tr id="tbl_tr<?php echo $tr_counter; ?>" class="item_cls">
				<td>
					<?php echo $tr_counter; ?>
					<input class="form-control each_row list_cls" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
				</td>
				<td>
					<?php echo $test_name; ?>
					<input class="form-control test_name list_cls" type="hidden" name="test_name<?php echo $tr_counter; ?>" id="test_name<?php echo $tr_counter; ?>" value="<?php echo $test_name; ?>" onkeyup="test_name_each(event,'<?php echo $tr_counter; ?>')" disabled>
					
					<input type="hidden" class="form-control testid" id="testid<?php echo $tr_counter; ?>" value="<?php echo $testid; ?>">
				</td>
				<td>
					<input class="form-control span1 numericc test_rate list_cls" type="text" name="test_rate<?php echo $tr_counter; ?>" id="test_rate<?php echo $tr_counter; ?>" value="<?php echo $test_rate; ?>" onkeyup="test_rate_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $rate_attribute; ?> style="padding: 2px;margin-bottom: 0;text-align:right;">
				</td>
				<td class="test_discount" style="display:none;">
					<input class="form-control span1 numericc discount_each list_cls" type="text" name="discount_each<?php echo $tr_counter; ?>" id="discount_each<?php echo $tr_counter; ?>" value="<?php echo $discount_each; ?>" onkeyup="discount_each(event,'<?php echo $tr_counter; ?>')" onpaste="return false;" ondrop="return false;" <?php echo $discount_attribute; ?>>
				</td>
				<td>
			<?php
				if($report_done==0)
				{
			?>
					<button class="btn btn-danger btn-mini remove_btn_cls" id="remove_btn<?php echo $tr_counter; ?>" onclick="remove_tr('<?php echo $tr_counter; ?>')"><i class="icon-remove"></i></button>
			<?php
				}else{
			?>
					<b style="color:green">Reported</b>
			<?php
				}
			?>
				</td>
			</tr>
			<tr id="item_footer"></tr>
		<?php
				$tr_counter++;
			}
		?>
		</table>
<?php
	}
?>
	</div>
	<script>
		$("#ipd_test_ref_doc").select2({ theme: "classic" });
	</script>
	<style>
		.select2-dropdown
		{
			z-index:99999 !important;
		}
	</style>
	<?php
}
?>
