<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');

$now_time=date('H:i:s');
$now_date=date("Y-m-d");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$date=date("Y-m-d");

$type=$_POST["type"];

if($type=="load_patient_list")
{
	$uhid=mysqli_real_escape_string($link,$_POST["uhid"]);
	$ipd=mysqli_real_escape_string($link,$_POST["ipd"]);
	$name=mysqli_real_escape_string($link,$_POST["name"]);
	$fdate=mysqli_real_escape_string($link,$_POST["fdate"]);
	$tdate=mysqli_real_escape_string($link,$_POST["tdate"]);
	
	$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='8'";
	
	$zz=0;
	if($uhid)
	{
		$qry.=" and patient_id='$uhid'";
		$zz++;
	}
	if($ipd)
	{
		$qry.=" and opd_id='$ipd'";
		$zz++;
	}
	if($name)
	{
		$qry.=" and patient_id in(select patient_id from patient_info where name like '%$name%')";
		$zz++;
	}
	
	if($zz==0)
	{
		$qry="SELECT * FROM `uhid_and_opdid` WHERE `type`='8' AND `opd_id` IN(SELECT DISTINCT b.`baby_ipd_id` FROM `ipd_pat_bed_details` a, `ipd_pat_delivery_det` b WHERE a.`patient_id`=a.`patient_id` AND a.`ipd_id`=b.`ipd_id` AND b.`baby_ipd_id`!='')";
	}
	
	if($fdate && $tdate)
	{
		$qry.=" AND `date` between '$fdate' and '$tdate'";
	}
	
	$qry.=" ORDER BY `slno` DESC LIMIT 100";
	
	//echo $qry;
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='8' "));
?>
		<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>Unit No.</th>
			<th><?php echo $prefix_det["prefix"]; ?></th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Mother Unit No.</th>
			<th>Mother Bill No.</th>
			<th>Date Time</th>
		</tr>
	<?php
	$i=1;
	$qr=mysqli_query($link,$qry);
	while($pat_reg=mysqli_fetch_array($qr))
	{
		$delivery_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `baby_uhid`='$pat_reg[patient_id]' AND `baby_ipd_id`='$pat_reg[opd_id]'"));
		$mother_uhid=$delivery_det["patient_id"];
		$mother_ipd=$delivery_det["ipd_id"];
		
		$click="onclick=\"redirect_page('$pat_reg[patient_id]','$pat_reg[opd_id]')\"";
		$style="style='cursor:pointer;'";
		
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `opd_id`='$mother_ipd' AND `type`='2' "));
		if($cancel_request)
		{
			$click="";
			$style="";
			$tr_back_color="style='background-color: #ff000021'";
			
			$emp_info_del=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request[user]' "));
			
			$tr_title="title='Cancel request by $emp_info_del[name]'";
		}
		else
		{
			$click="onclick=\"redirect_page('$pat_reg[patient_id]','$pat_reg[opd_id]')\"";
			$style="style='cursor:pointer;'";
			
			$tr_back_color="";
			$tr_title="";
		}
		
		$date_time=convert_date($pat_reg['date']).", Time: ".convert_time($pat_reg['time']);
		
		$pat_info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pat_reg[patient_id]'"));
		
		if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".convert_date_g($pat_info["dob"]).")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
		
		echo "<tr $click $style $tr_back_color $tr_title><td>$i</td><td>$pat_info[patient_id]</td><td>$pat_reg[opd_id]</td><td>$pat_info[name]</td><td>$age</td><td>$pat_info[sex]</td><td>$mother_uhid</td><td>$mother_ipd</td><td>$date_time</td></tr>";
		
		$i++;
	}
}

if($_POST["type"]=="pat_ipd_discharge_request_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	$delivery_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `baby_uhid`='$uhid' AND `baby_ipd_id`='$ipd'"));
	
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `discharge_request` WHERE `patient_id`='$delivery_det[patient_id]' AND `ipd_id`='$delivery_det[ipd_id]'"));
	$m=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$delivery_det[patient_id]' AND `ipd_id`='$delivery_det[ipd_id]'"));
	
	$tot=$n+$m;
	
	echo $tot;
}

if($_POST["type"]=="pat_ipd_inv_det")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	
	$delivery_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `baby_uhid`='$uhid' AND `baby_ipd_id`='$ipd'"));
	
	$n=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `discharge_request` WHERE `patient_id`='$delivery_det[patient_id]' AND `ipd_id`='$delivery_det[ipd_id]'"));
	$m=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$delivery_det[patient_id]' AND `ipd_id`='$delivery_det[ipd_id]'"));
	
	$tot=$n+$m;
	
	if($tot>0)
	{
		$btndis="disabled='disabled'";
	}
	else
	{
		$btndis="";
	}
	
	//$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `type`='4' ORDER BY `batch_no` DESC");
	$ds=mysqli_query($link,"SELECT DISTINCT `batch_no` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ORDER BY `batch_no` DESC");
	//$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `type`='4'");
	$q=mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' ");
	$num=mysqli_num_rows($q);
	$no=mysqli_num_rows($ds);
?>
	<div class="span5" style="margin-left:0px;">
	<?php
	if($no>0)
	{
		while($res=mysqli_fetch_array($ds))
		{
			//$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' AND `type`='4'"));
			$nn=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]' "));
			$dt=mysqli_fetch_array(mysqli_query($link,"SELECT `date`,`time` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$res[batch_no]'"));
			echo "<button type='button' class='btn btn-default bt' id='ad".$res['batch_no']."' onclick='view_batch(".$res['batch_no'].")'><span class='sp'>Batch No=".$res['batch_no']."</span><span class='sp'>Tests=".$nn."</span><span class='sp'>Date: ".convert_date_g($dt['date'])."</span><span class='sp'>Time: ".convert_time($dt['time'])."</span></button><br/>";
		}
	}
	if($num>0 && $pat_discharge_num==0)
	{
	?>
		<button type="button" class="btn btn-info" id="adm" onclick="ad_tests()" style="" <?php echo $btndis; ?>><i class="icon-plus"></i> Add New Batch</button>
	<?php
	}
	else if($pat_discharge_num==0)
	{
	?>
	<button type="button" class="btn btn-info" id="ad" onclick="ad_tests()" style="" <?php echo $btndis; ?>><i class="icon-plus"></i> Add</button>
	<?php
	}
	
	if($num>1)
	{
?>
		<button class="btn btn-print" onclick="print_batch_bill('<?php echo $uhid; ?>','<?php echo $ipd; ?>','0')"><i class="icon-print"></i> All Batch</button>
<?php
	}
	?>
	</div>
	<div id="batch_details" class="span5" style="margin-left:-40px;max-width:550px;min-width:540px;"></div>
	<style>
		.widget-content{border-bottom:none;}
		.sp{margin-left:10px;}
		.bt{margin-bottom:5px;}
	</style>
	<?php
}

if($type=="show_sel_tests_ipd")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$batch=$_POST['batch'];
	
	if(!$batch){ $batch=0; }
	
	$ref_doc_val=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$batch' "));
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
				<td style="display:none;">
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
		mysqli_query($link," UPDATE `ipd_test_ref_doc` SET `refbydoctorid`='$refbydoctorid',`centreno`='$centreno',`ward_id`='$ward_id',`date`='$test_entry_date',`time`='$test_entry_time',`user`='$usr' WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' ");
	}else
	{
		mysqli_query($link," DELETE FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' ");
		
		mysqli_query($link," INSERT INTO `ipd_test_ref_doc`(`patient_id`, `ipd_id`, `batch_no`, `consultantdoctorid`, `refbydoctorid`, `centreno`, `ward_id`, `date`, `time`, `user`) VALUES ('$uhid','$ipd','$bch','$consultantdoctorid','$refbydoctorid','$centreno','$ward_id','$test_entry_date','$test_entry_time','$usr') ");
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
				
				if(mysqli_query($link,"INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$bch','$test_id','$sample_id','$test_rate','0','$test_entry_date','$test_entry_time','$usr','8')")) // 8 = baby dashboard
				{
					$last_slno_test=mysqli_fetch_array(mysqli_query($link," SELECT `slno` FROM `patient_test_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' AND `batch_no`='$bch' AND `testid`='$test_id' AND `user`='$usr' ORDER BY `slno` DESC "));
					
					// Insert in Sub `ipd_pat_service_details_sub`
					mysqli_query($link," INSERT INTO `ipd_pat_service_details_sub`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$uhid','$ipd','$group_id','$test_id','$ser_name','1','$test_rate','$test_rate','0','$usr','$test_entry_time','$test_entry_date','0','$consultantdoctorid','$refbydoctorid','$last_slno_test[slno]') ");
					
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
						
						if(mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','','$ipd','$bch','$s_t[sub_testid]','$sample_id','$rate','0','$date','$time','$user','8') "))
						{
							$add_on++;
						}
					}
					
					if($add_on>0)
					{
						//mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' and `batch_no`='$bch' and `testid`='$test_id' ");
						mysqli_query($link, " UPDATE `patient_test_details` SET `test_rate`='0',`test_discount`='0' WHERE `patient_id`='$uhid' and `ipd_id`='$ipd' and `batch_no`='$bch' and `testid`='$test_id' ");
					}
				}
			}
		}
	}
	
	// Bill Add to mother's Bill
	
	// Amount Update in Main `ipd_pat_service_details`
	// Laboratory
	
	// IDs of Mother and Baby's
	$delivery_det=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `baby_uhid`='$uhid' AND `baby_ipd_id`='$ipd'"));
	$mother_uhid=$delivery_det["patient_id"];
	$mother_ipd=$delivery_det["ipd_id"];

	$uhid_str="'$mother_uhid'";
	$ipd_str="'$mother_ipd'";
	$baby_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$mother_uhid' AND `ipd_id`='$mother_ipd'");
	while($baby_info=mysqli_fetch_array($baby_qry))
	{
		$uhid_str.=",'$baby_info[baby_uhid]'";
		$ipd_str.=",'$baby_info[baby_ipd_id]'";
	}
	
	$test_sum=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`test_rate`),0) AS `tot` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id` IN($uhid_str) and a.`ipd_id` IN($ipd_str) AND b.`category_id`=1"));
	$test_amount=$test_sum["tot"];
	if($test_amount>=0)
	{
		$group_id=104;
		$service_id=534;
		
		$charge_info=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$service_id'"));
		$service_name=mysqli_real_escape_string($link, $charge_info["charge_name"]);
		
		$check_entry=mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `ipd_pat_service_details` WHERE `patient_id`='$mother_uhid' AND `ipd_id`='$mother_ipd' AND `group_id`='$group_id' AND `service_id`='$service_id'"));
		if($check_entry)
		{
			mysqli_query($link," UPDATE `ipd_pat_service_details` SET `rate`='$test_amount',`amount`='$test_amount' WHERE `patient_id`='$mother_uhid' AND `ipd_id`='$mother_ipd' AND `group_id`='$group_id' AND `service_id`='$service_id' ");
		}
		else
		{
			mysqli_query($link," INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$mother_uhid','$mother_ipd','$group_id','$service_id','$service_name','1','$test_amount','$test_amount','0','$c_user','$test_entry_time','$test_entry_date','0','0','0',NULL) ");
		}
	}
	// Other
	$test_qry=mysqli_query($link, "SELECT DISTINCT a.`type_id` FROM `testmaster` a, `patient_test_details` b WHERE a.`testid`=b.`testid` AND a.`category_id` NOT IN(1) AND b.`patient_id` IN($uhid_str) and b.`ipd_id` IN($ipd_str)");
	while($test_dept=mysqli_fetch_array($test_qry))
	{
		$dept_info=mysqli_fetch_array(mysqli_query($link, "SELECT `id`, `category_id`, `name`, `group_id`, `service_id` FROM `test_department` WHERE `id`='$test_dept[type_id]'"));
		
		$category_id=$dept_info["category_id"];
		$type_id=$dept_info["id"];
		
		$group_id=$dept_info["group_id"];
		$service_id=$dept_info["service_id"];
		
		$charge_info=mysqli_fetch_array(mysqli_query($link, " SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$service_id'"));
		$service_name=mysqli_real_escape_string($link, $charge_info["charge_name"]);
		
		$test_sum=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`test_rate`),0) AS `tot` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id` IN($uhid_str) and a.`ipd_id` IN($ipd_str) AND b.`category_id`='$category_id' AND b.`type_id`='$type_id'"));
		$test_amount=$test_sum["tot"];
		
		$check_entry=mysqli_fetch_array(mysqli_query($link, "SELECT `slno` FROM `ipd_pat_service_details` WHERE `patient_id`='$mother_uhid' AND `ipd_id`='$mother_ipd' AND `group_id`='$group_id' AND `service_id`='$service_id'"));
		if($check_entry)
		{
			mysqli_query($link," UPDATE `ipd_pat_service_details` SET `rate`='$test_amount',`amount`='$test_amount' WHERE `patient_id`='$mother_uhid' AND `ipd_id`='$mother_ipd' AND `group_id`='$group_id' AND `service_id`='$service_id' ");
		}
		else
		{
			mysqli_query($link," INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`, `test_slno`) VALUES ('$mother_uhid','$mother_ipd','$group_id','$service_id','$service_name','1','$test_amount','$test_amount','0','$c_user','$test_entry_time','$test_entry_date','0','0','0',NULL) ");
		}
	}
}
?>
