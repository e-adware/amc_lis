<?php
$paramm=base64_encode(83);
$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$val=base64_decode($_GET['val']);
if($val==1)
{
	$back=221;
}else if($val==2)
{
	$back=218;
}else
{
	$back=51;
}
$date=date('Y-m-d'); // important
function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$process=base64_decode($_GET['process']);
if($process)
{
	echo "<input type='hidden' value='1' id='process'>";
}else
{
	echo "<input type='hidden' value='0' id='process'>";
}

$al_date=date("Y-m-d");
$al_time=date('H:i:s');

$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));

$pat_info=$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

//if($pat["dob"]!=""){ $age=age_calculator($pat["dob"]); }else{ $age=$pat["age"]." ".$pat["age_type"]; }

$reg_date=$pat_reg["date"];
if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

// IPD Registration Free
//~ $regd_fee_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='152' and service_id='1019' "));
//~ if(!$regd_fee_check)
//~ {
	//~ $charge_master_val=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where `group_id`='152' and charge_id='1019' "));
	
	//~ $first_bed=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and alloc_type='1' order by slno"));
	
	//~ mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`) VALUES ('$uhid','$ipd','152','1019','$charge_master_val[charge_name]','1','$charge_master_val[amount]','$charge_master_val[amount]','1','$_SESSION[emp_id]','$pat_reg[time]','$pat_reg[date]','$first_bed[bed_id]','0','0')");
//~ }


$pat_bed_alloc_time_val=mysqli_fetch_array(mysqli_query($link," SELECT `time` FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd' "));
$pat_bed_alloc_time=$pat_bed_alloc_time_val["time"];

// IPD Service Free
//~ $service_fee_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='173' and service_id='1354' "));

//~ $tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
//~ $tot_serv_amt=$tot_serv["tots"];

//~ $charge_master_val=mysqli_fetch_array(mysqli_query($link,"select * from charge_master where `group_id`='173' and charge_id='1354' "));

//~ $charge_amount=($tot_serv_amt/100)*$charge_master_val["amount"];

//~ if($charge_amount!=$service_fee_check["amount"])
//~ {
	//~ $first_bed=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and alloc_type='1' order by slno"));
	
	//~ mysqli_query($link,"DELETE FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and `group_id`='173' and service_id='1354' ");
	
	//~ mysqli_query($link,"INSERT INTO `ipd_pat_service_details`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`) VALUES ('$uhid','$ipd','173','1354','$charge_master_val[charge_name]','1','$charge_amount','$charge_amount','1','$_SESSION[emp_id]','$pat_reg[time]','$pat_reg[date]','$first_bed[bed_id]','0','0')");
//~ }

$dis_pat_qry=mysqli_query($link,"select * from ipd_pat_discharge_details where patient_id='$uhid' and ipd_id='$ipd'");
$dis_pat_num=mysqli_num_rows($dis_pat_qry);
$dis_pat_val=mysqli_fetch_array($dis_pat_qry);
$dis_date_time=convert_date_g($dis_pat_val['date'])." ".convert_time($dis_pat_val['time']);
$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));
$entry_date_time=convert_date_g($dt_tm['date'])." ".convert_time($dt_tm['time']);

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$ipd' "));

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt_tm[type]' "));


$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);

$str="";
if($_GET["uhid_str"])
{
	$str.="&uhid_str=$uhid_str";
}

if($_GET["pin_str"])
{
	$str.="&pin_str=$pin_str";
}

if($_GET["fdate_str"])
{
	$str.="&fdate_str=$fdate_str";
}

if($_GET["tdate_str"])
{
	$str.="&tdate_str=$tdate_str";
}

if($_GET["name_str"])
{
	$str.="&name_str=$name_str";
}

if($_GET["phone_str"])
{
	$str.="&phone_str=$phone_str";
}

if($_GET["param_str"])
{
	$str.="&param_str=$param_str";
}

if($_GET["pat_type_str"])
{
	$str.="&pat_type_str=$pat_type_str";
}

$pat_info_edit_display="";
if($p_info["edit_info"]==0)
{
	$pat_info_edit_display="style='display:none;'";
}

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> IPD Dashboard</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	
	<span style="float:right;">
		<button class="btn btn-print" id="admission_sheet_btn" onclick="print_regd_receit(1)"><i class="icon-print"></i> Admission Sheet</button>
		<!--<button class="btn btn-print" id="consent_btn" onclick="print_regd_receit(2)"><i class="icon-print"></i> General Consent</button>-->
	<?php if($str){ ?>
		<button class="btn btn-back" id="back_btn" onclick="window.location='processing.php?v=0<?php echo $str; ?>'"><i class="icon-backward"></i> Back</button>
	<?php } ?>
	</span>
	
	<span style="float:right;" class="uhid_barcode">
		<select class="span1" id="barcode_no" style="margin-bottom: 0;">
		<?php
			for($x=1;$x<=10;$x++)
			{
				echo "<option value='$x'>$x</option>";
			}
		?>
		</select>
		<input type="button" class="btn btn-inverse" id="add" value="Barcode" onclick="print_barcode_recp('<?php echo $uhid; ?>','<?php echo $ipd; ?>')" style="" />
	</span>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th><?php echo $prefix_det["prefix"]; ?></th>
		<?php if($dt_tm["ipd_serial"]){ ?>
			<th style="display:none;">IPD Serial</th>
		<?php } ?>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>
				Outstanding Amount
				<span class="text-right" <?php echo $pat_info_edit_display; ?>>
					<a class="btn btn-edit btn-mini" title="Edit Patient Details" href="index.php?param=<?php echo $paramm; ?>=&uhid=<?php echo $_GET["uhid"]; ?>=&ipd=<?php echo $_GET["ipd"]; ?>" ><i class="icon-edit"></i></a>
				</span>
			</th>
		</tr>
		<tr>
			<td><?php echo $pat['patient_id'];?></td>
			<td><?php echo $ipd;?></td>
			<?php if($dt_tm["ipd_serial"]){ ?>
				<td style="display:none;"><?php echo $dt_tm["ipd_serial"]; ?></td>
			<?php } ?>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat['sex'];?></td>
			<td>
				<span id="out_amt"></span>
				<!--<span class="text-right"><button type="button" class="btn btn-success btn-mini" onclick="check_bill_print('<?php echo $_GET['uhid']; ?>','<?php echo $_GET['ipd']; ?>')"><i class="icon icon-print"></i></button></span>-->
			</td>
		</tr>
		<tr>
			<td colspan='3'><b>Admission Date Time : </b><?php echo $entry_date_time; ?></td>
			<td colspan='3'>
				<?php if($dis_pat_num>0){ ?>
				<b>Discharged Date Time : </b><?php echo $dis_date_time; ?>
				<?php } ?>
			</td>
		</tr>
	</table>
	
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
	<input type="hidden" id="chk_val1" value="0"/>
	<div class="" style="margin-left:0px;">
		<div class="accordion" id="collapse-group">
			
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse110" data-toggle="collapse" onclick="show_icon(110)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Service Entry</b><i class="icon-arrow-down" id="ard110"></i><i class="icon-arrow-up" id="aru110" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign110" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign110" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse110" style="height:0px;max-height:450px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl110" style="display:none;">
						
					</div>
				</div>
			</div>
			<!--
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse9" data-toggle="collapse" onclick="show_icon(9)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Pharmacy</b><i class="icon-arrow-down" id="ard9"></i><i class="icon-arrow-up" id="aru9" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign9" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign9" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse9" style="height:0px;max-height:300px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl9" style="display:none;">
						
					</div>
				</div>
			</div>
			
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse3" data-toggle="collapse" onclick="show_icon(3)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Investigation</b><i class="icon-arrow-down" id="ard3"></i><i class="icon-arrow-up" id="aru3" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign3" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign3" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse3" style="height:0px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl3" style="display:none;">
						<div id="load_investigation">
							
						</div>
						<div id="out_test_form">
							
						</div>
					</div>
				</div>
			</div>
			-->
			<div class="accordion-group widget-box"><!--box 1-->
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse1" data-toggle="collapse" onclick="show_icon(1)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Payment</b><i class="icon-arrow-down" id="ard1"></i><i class="icon-arrow-up" id="aru1" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign1" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign1" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse1" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl1" style="display:none;">
						
					</div>
				</div>
			</div>
		<?php
		$nicu_check=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$uhid' and ipd_id='$ipd' and ward_id='5'"));
		if($nicu_check)
		{
	?>
			<div class="accordion-group widget-box" style="display:none;">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse222" data-toggle="collapse" onclick="show_icon(222)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">NICU Discharge Summary</b><i class="icon-arrow-down" id="ard222"></i><i class="icon-arrow-up" id="aru222" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign222" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign222" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse222" style="height:0px;max-height:450px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl222" style="display:none;">
						
					</div>
				</div>
			</div>
	<?php	
		}
		else
		{
	?>
			<div class="accordion-group widget-box" style="display:none;">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse22" data-toggle="collapse" onclick="show_icon(22)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Discharge Summary</b><i class="icon-arrow-down" id="ard22"></i><i class="icon-arrow-up" id="aru22" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign22" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign22" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse22" style="height:0px;max-height:450px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl22" style="display:none;">
						
					</div>
				</div>
			</div>
	<?php	
		}
	?>
			<!--
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse9" data-toggle="collapse" onclick="show_icon(9)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Pharmacy</b><i class="icon-arrow-down" id="ard9"></i><i class="icon-arrow-up" id="aru9" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign9" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign9" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse9" style="height:0px;max-height:300px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl9" style="display:none;">
						
					</div>
				</div>
			</div>
			
			<!--
			<div class="accordion-group widget-box">
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse2" data-toggle="collapse" onclick="show_icon(2)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Medication</b><i class="icon-arrow-down" id="ard2"></i><i class="icon-arrow-up" id="aru2" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign1" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign1" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse2" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl2" style="display:none;">
						
					</div>
				</div>
			</div>
			
			
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse5" data-toggle="collapse" onclick="show_icon(5)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">IP Consultation</b><i class="icon-arrow-down" id="ard5"></i><i class="icon-arrow-up" id="aru5" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign5" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign5" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse5" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl5" style="display:none;">
						
					</div>
				</div>
			</div>
			
				
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse6" data-toggle="collapse" onclick="show_icon(6)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Room Status</b><i class="icon-arrow-down" id="ard6"></i><i class="icon-arrow-up" id="aru6" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign6" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign6" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse6" style="height: 0px;overflow:unset;">
					<div class="widget-content hidden_div" id="cl6" style="display:none;">
						
					</div>
				</div>
			</div>
			
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse7" data-toggle="collapse" onclick="show_icon(7)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Equipment</b><i class="icon-arrow-down" id="ard7"></i><i class="icon-arrow-up" id="aru7" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign7" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign7" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse7" style="height:0px;max-height:300px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl7" style="display:none;">
						
					</div>
				</div>
			</div>
			<div class="accordion-group widget-box"> 
				<div class="accordion-heading">
					<div class="widget-title">
						<a data-parent="#collapse-group" href="#collapse8" data-toggle="collapse" onclick="show_icon(8)">
							<span class="icon" style="width:90%;"><b style="padding:10px;font-size:16px;">Consumables</b><i class="icon-arrow-down" id="ard8"></i><i class="icon-arrow-up" id="aru8" style="display:none;"></i></span>
							<span class="text-right" style="padding:10px;font-size:18px;">
								<span class="iconp" id="plus_sign8" style="float:right;"><i class="icon-plus"></i></span>
								<span class="iconm" id="minus_sign8" style="float:right;display:none;"><i class="icon-minus"></i></span>
							</span>
						</a>
					</div>
				</div>
				<div class="accordion-body collapse" id="collapse8" style="height:0px;max-height:300px;overflow-y:scroll;">
					<div class="widget-content hidden_div" id="cl8" style="display:none;">
						
					</div>
				</div>
			</div>
			-->
		</div>
	</div>
	
	<input type="button" data-toggle="modal" data-target="#myModal1" id="mod" style="display:none"/>
	<input type="hidden" id="mod_chk" value="0"/>
	<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results">
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none"/>
	<input type="hidden" id="mod_chk2" value="0"/>
	<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results2"> </div>
				</div>
			</div>
		</div>
	</div>
	 <!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal modal-lg fade">
		  <div class="modal-body">
			<div id="post_medi">
				
			</div>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="save_disc_medi()" class="btn btn-primary" href="#">Save</a>
			<a data-dismiss="modal" onclick="" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end--> 
	  <!--modal-->
		<a href="#medplan" data-toggle="modal" id="med_upd" class="btn" style="display:none;">A</a>
		<div id="medplan" class="modal modal-lg fade">
		  <div class="modal-body">
			<div id="upd_med_plan_det">
				
			</div>
		  </div>
		</div>
	<!--modal end-->
	<!-- Modal -->
	<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal_edit" id="edit_payment_mode_btn" style="display:none;">Edit Payment Mode</button>
	<div id="myModal_edit" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Edit Payment Mode</h4>
				</div>
				<div class="modal-body">
					<div id="edit_payment_mode_data"></div>
				</div>
				<div class="modal-footer" style="display:none;">
					<button type="button" class="btn btn-inverse" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function()
	{
		out_amount('Default Load');
	});
	function show_icon(i)
	{
		$(".hidden_div").fadeOut();
		$(".iconp").show();
		$(".iconm").hide();
		$(".icon-arrow-down").show();
		$(".icon-arrow-up").hide();
		if($('#cl'+i+':visible').length)
		{
			$("#cl"+i).fadeOut();
			$("#plus_sign"+i).show();
			$("#minus_sign"+i).hide();
			$("#ard"+i).show();
			$("#aru"+i).hide();
		}
		else
		{
			$("#cl"+i).fadeIn();
			$("#plus_sign"+i).hide();
			$("#minus_sign"+i).show();
			$("#ard"+i).hide();
			$("#aru"+i).show();
			
			if(i==110)
			{
				load_services();
			}
			else if(i==1)
			{
				load_payment();
			}
			else if(i==2)
			{
				medication();
			}
			else if(i==3)
			{
				investigation();
			}
			else if(i==5)
			{
				ip_consult();
				$("html,body").animate({scrollTop: '300px'},800);
			}
			else if(i==6)
			{
				load_bed_stat();
			}
			else if(i==7)
			{
				equipment();
				$("html,body").animate({scrollTop: '380px'},800);
			}
			else if(i==8)
			{
				consumable();
				$("html,body").animate({scrollTop: '400px'},800);
			}
			else if(i==9)
			{
				medicine();
			}
			else if(i==22)
			{
				disc_summary();
			}
			else if(i==222)
			{
				nicu_disc_summary();
			}
		}
	}
	function show_sub_ser(i)
	{
		var s="_ser";
		//$(".hidden_div_ser:visible").fadeOut(200);
		$(".hidden_div_ser").fadeOut();
		$(".iconp"+s).show();
		$(".iconm"+s).hide();
		$(".icon-arrow-down"+s).show();
		$(".icon-arrow-up"+s).hide();
		if($('#cl'+i+s+':visible').length)
		{
			$("#cl"+i+s).fadeOut();
			$("#plus_sign"+i+s).show();
			$("#minus_sign"+i+s).hide();
			$("#ard"+i+s).show();
			$("#aru"+i+s).hide();
		}
		else
		{
			$("#cl"+i+s).fadeIn();
			$("#plus_sign"+i+s).hide();
			$("#minus_sign"+i+s).show();
			$("#ard"+i+s).hide();
			$("#aru"+i+s).show();
			
		}
	}
	
	function save_final_medi()
	{
		if($("#post_med_det").val().trim()=="")
		{
			$("#post_med_det").focus();
		}
		else
		{
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				medi:$("#post_med_det").val().trim(),
				usr:$("#user").text().trim(),
				type:6010,
			},
			function(data,status)
			{
				//alert(data);
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
				}, 1000);
				disc_summary();
				//$("#cl9").html(data);
				//$("html,body").animate({scrollTop: '400px'},800);
			})
		}
	}
	
	function medicine()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:34,
		},
		function(data,status)
		{
			$("#cl9").html(data);
			$("html,body").animate({scrollTop: '400px'},800);
		})
	}
	
	function consumable()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:22,
		},
		function(data,status)
		{
			$("#cl8").html(data);
			$("html,body").animate({scrollTop: '400px'},800);
		})
	}
	
	function equipment()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:20
		},
		function(data,status)
		{
			$("#cl7").html(data);
		})
	}
	function ip_consult()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			type:18,
		},
		function(data,status)
		{
			$("#cl5").html(data);
			$("html,body").animate({scrollTop: '300px'},800);
		})
	}
	
	
	function show_sub_icon(i,val)
	{
		$(".hidden_div_sub:visible").fadeOut(200);
		//$(".hidden_div_sub:visible").fadeOut(200,function(){
		$(".iconp_sub").show();
		$(".iconm_sub").hide();
		$(".icon-arrow-down").show();
		$(".icon-arrow-up").hide();
		if($('#cl_sub'+i+'').css('display')=="none")
		{
			$("#cl_sub"+i).fadeIn();
			$("#plus_sign_sub"+i).hide();
			$("#minus_sign_sub"+i).show();
			//$("#ard_sub"+i).hide();
			//$("#aru_sub"+i).show();
			
			//$("#cl_sub"+i).css({"height":"0px"});
			
			if(val==3)
			{
				view_batch(i);
			}
			else if(val==5)
			{
				ip_consult_details(i)
			}
			else if(val==7)
			{
				equipment_details(i)
			}
			else if(val==8)
			{
				consumable_details(i)
			}
			else if(val==9)
			{
				pharmacy_details(i)
			}
		}
		else
		{
			$("#collapse_sub"+i).animate({"height":"0px"},100);
		}
		
	}
	
	
	
	function pharmacy_details(i)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			val:$("#bill_"+i+"").val(),
			tab_id:i,
			type:35,
		},
		function(data,status)
		{
			$("#cl_sub"+i+"").html(data).fadeIn(200,function(){ $("#collapse_sub"+i+"").css({"height":"auto"})});
		})
	}
	
	function equipment_details(i)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			val:$("#equip_"+i+"").val(),
			tab:i,
			type:21,
		},
		function(data,status)
		{
			$("#cl_sub"+i+"").html(data).fadeIn(200,function(){ $("#collapse_sub"+i+"").css({"height":"auto"})});
		})	
	}
	
	function edit_equipment(sl)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			sl:sl,
			type:32
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'50%',"width":"500px","height":"350px",'margin':'auto'},"slow");
			
			$("#mod").click();
		})
	}
	
	function save_eqp_details(id)
	{
		bootbox.confirm("Do you really want to Update?",
	    function(result){ 
		if(result)
		{
			
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				id:id,
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				edit_h:$("#edit_hours").val(),
				edit_d:$("#edit_date").val(),
				amount:$("#edit_cost").val(),
				user:$("#user").text(),
				type:33,
			},
			function(data,status)
			{
				equipment_details($("#eq_tab_id").val());
			})	
		}
		})
		
	}
	
	function ip_consult_details(i)
	{
		
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			val:$("#date_"+i+"").val(),
			tab_id:i,
			type:19,
		},
		function(data,status)
		{
			$("#cl_sub"+i+"").html(data).fadeIn(200,function(){ $("#collapse_sub"+i+"").css({"height":"auto"})});
		})
	}
	function update_consult_details(uhid,ipd,doc)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:uhid,
			ipd:ipd,
			doc:doc,
			usr:$("#user").text(),
			tab:$("#cons_sub_id").val(),
			type:23,
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'35%',"width":"1000px","height":"500px",'margin':'auto'},"slow");
			
			if(!$('#myModal1').hasClass('in'))
			{
				$("#mod").click();
				
			}
		})
	}
	function add_bill_consult(uhid,ipd,doc)
	{
		bootbox.confirm({
				message: "Do you really want to add?",
				buttons: 
				{
					confirm: 
					{
						label: 'Yes',
						className: 'btn-success'
					},
					cancel: 
					{
						label: 'No',
						className: 'btn-danger'
					}
				},
			callback: function (result) 
			{
				if(result)
				{ 
					$.post("pages/ipd_dash_pat_ajax.php",
					{
						uhid:uhid,
						ipd:ipd,
						doc:doc,
						c_date:$("#con_date").val(),
						c_fees:$("#con_fees").val(),
						usr:$("#user").text(),
						type:24,
					},
					function(data,status)
					{
						ip_consult_details($("#cons_sub_id").val());
						update_consult_details(uhid,ipd,doc);
					})
					
				}
			}
		})
	}
	
	function remove_consult_details(uhid,ipd,doc,slno)
	{
		    bootbox.confirm({
				message: "Do you really want to delete?",
				buttons: 
				{
					confirm: 
					{
						label: 'Yes',
						className: 'btn-success'
					},
					cancel: 
					{
						label: 'No',
						className: 'btn-danger'
					}
				},
			callback: function (result) 
			{
           
				if(result)
				{ 
					$.post("pages/ipd_dash_pat_ajax.php",
					{
						slno:slno,
						type:25
					},
					function(data,status)
					{
						ip_consult_details($("#cons_sub_id").val());
						update_consult_details(uhid,ipd,doc);
					})
				}
			}
    });
	}
	
	function load_bed_stat()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			type:6,
		},
		function(data,status)
		{
			$("#cl6").html(data);
		})
	}
	function load_bed_details()
	{
		$('#foot').hide();
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			type:7,
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text()
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'35%',"width":"1000px","height":"500px",'margin':'auto'},"slow");
			if($("#mod_chk").val()==0)
			{
				$("#mod").click();
				chk_bed_assign();
			}
		})
	}
	
	function bed_asign(w_id,b_id,w_name,b_no)
	{
		bootbox.confirm("Do you really want to assign bed no "+b_no+" of ward "+w_name+" to this patient?",
	    function(result){ 
		if(result)
		{ 
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				type:8,
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				user:$("#user").text(),
				w_id:w_id,
				b_id:b_id
			},
			function(data,status)
			{
				load_bed_stat();
				/*
				var bed_info="Ward: "+w_name+"<br/> Bed No: "+b_no;
				bed_info+="<input type='hidden' id='ward_id' value="+w_id+" />";
				bed_info+="<input type='hidden' id='bed_id' value="+b_id+" /> <br/>";
				$("#bed_info").html(bed_info);
				*/
			})
		}
		});
		/*
		$(".ward .btn").css({'background-color':'white'})
		$("#"+b_id+"").css({'background-color':'#5bc0de'})
		*/
	}
	function chk_bed_assign()
	{
		setInterval(function()
		{
			if($('#cl6').css('display')=="block")
			{
				$.post("pages/ipd_dash_pat_ajax.php",
				{
					type:7,
					uhid:$("#uhid").val(),
					ipd:$("#ipd").val(),
					usr:$("#user").text()
				},
				function(data,status)
				{
					$("#results").html(data);
					
				})
			}
		},1500);
	}
	
	function allocate_bed()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			user:$("#user").text(),
			type:9,
		},
		function(data,status)
		{
			alert(data);
		})
	}
	
	function update_bed_details(id)
	{
		bootbox.confirm("Do you really want to Update?",
	    function(result){ 
		if(result)
		{
			
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				id:id,
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				edit_ward:$("#edit_ward").val(),
				edit_room:$("#edit_room").val(),
				edit_bed:$("#edit_bed").val(),
				date_f:$("#edit_date_from").val(),
				date_t:$("#edit_date_to").val(),
				amount:$("#edit_total_cost").val(),
				type:28,
			},
			function(data,status)
			{
				load_bed_stat();
			})	
		}
		})
	}
	
	function add_more_bed()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:29,
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'50%',"width":"500px","height":"450px",'margin':'auto'},"slow");
			$("#mod").click();
		})
	}
	
	function load_bed_cost()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			bed_id:$("#edit_bed").val(),
			occ:$("#edit_date_from").val(),
			vac:$("#edit_date_to").val(),
			type:30,
		},
		function(data,status)
		{
			$("#edit_total_cost").val(data);
		})
	}
	
	function save_bed_details()
	{
		
		var chk=0;
		var imp=$(".imp");
		for(var i=0;i<imp.length;i++)
		{
			if(!$.trim($(imp[i]).val()))
			{
				$(imp[i]).css({'border':'1px solid red'});
				chk++;
			}
			else
			{
				$(imp[i]).css({'border':'1px solid #ccc'});
			}
		}
		
		if(chk==0)
		{
			bootbox.confirm("Do you really want to Save?",
			function(result){ 
				if(result)
				{
					
					$.post("pages/ipd_dash_pat_ajax.php",
					{
						uhid:$("#uhid").val(),
						ipd:$("#ipd").val(),
						edit_ward:$("#edit_ward").val(),
						edit_room:$("#edit_room").val(),
						edit_bed:$("#edit_bed").val(),
						date_f:$("#edit_date_from").val(),
						date_t:$("#edit_date_to").val(),
						amount:$("#edit_total_cost").val(),
						type:31,
					},
					function(data,status)
					{
						load_bed_stat();
					})	
				}
			})
		}
		
	}
	
	function change_room_bed(val,chk)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			val:val,
			chk:chk,
			type:27,
		},
		function(data,status)
		{
			if(chk==1)
			{
				var res=data.split("@#@penguin#@#");
				$("#edit_room_th").html(res[0]);
				$("#edit_bed_th").html(res[1]);
			}
			else if(chk==2)
			{
				$("#edit_bed_th").html(data);
			}
		})
	}
	
	function bed_edit(id)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			id:id,
			user:$("#user").text(),
			type:26,
		},
		function(data,status)
		{
			
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'50%',"width":"500px","height":"450px",'margin':'auto'},"slow");
			$("#mod").click();
			
		})
	}
	function investigation(batch,typ)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			type:4,
		},
		function(data,status)
		{
			$("#cl3").html(data);
			if(batch!=0)
			$("#ad"+batch).click();
			else
			{				
				var b=document.getElementsByClassName("bt");
				$("#ad"+b.length).click();
			}
			$("html,body").animate({scrollTop: '200px'},500);
		})
	}
	function load_add_test_form(batch)
	{
		var uhid=$("#uhid").val();
		var ipd=$("#ipd").val();
		$.post("pages/ipd_pat_test_batch_new.php",
		{
			type:"load_test_form",
			batch_no:batch,
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
		},
		function(data,status)
		{
			$("#out_test_form").html(data);
			display_test_info(uhid,ipd,batch);
			if(batch=="0000")
			{
				$("#test").focus();
			}else
			{
				$({myScrollTop:window.pageYOffset}).animate({myScrollTop:450}, {
					duration: 1000,
					easing: 'swing',
					step: function(val){
						window.scrollTo(0, val);
					}
				});
				//$("#dis_per").focus();
				$("#print_receipt").focus();
			}
		})
	}
	function display_test_info(uhid,ipd,batch)
	{
		$.post("pages/ipd_pat_test_batch_new.php",
		{
			type:"load_test_details",
			uhid:uhid,
			ipd_id:ipd,
			batch_no:batch,
		},
		function(data,status)
		{
			$("#list_all_test").html(data);
			$("#list_all_test").css({'height':'200px','overflow':'auto'});
			load_cost(2);
			add_vaccu();
		})
	}
	////////// Test load /////////////
	function test_enable()
	{
		setTimeout(function(){ $("#chk_val").val(1)},500);	
	}
	var t_val=1;
	var t_val_scroll=0;
	function select_test_new(val,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var tst=document.getElementsByClassName("test"+t_val);
			load_test_new(''+tst[1].value+'',''+tst[2].innerHTML+'',''+tst[3].innerHTML+'');
			//$("#list_all_test").slideDown(400);
			$("#test").val("").focus();
		}
		else if(unicode==40)
		{
			var chk=t_val+1;
			var cc=document.getElementById("td"+chk).innerHTML;
			if(cc)
			{
				t_val=t_val+1;
				$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var t_val1=t_val-1;
				$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=t_val%1;
				if(z2==0)
				{
					$("#test_d").scrollTop(t_val_scroll)
					t_val_scroll=t_val_scroll+30;
				}
			}	
		}
		else if(unicode==38)
		{
			var chk=t_val-1;
			var cc=document.getElementById("td"+chk).innerHTML;
			if(cc)
			{
				t_val=t_val-1;
				$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var t_val1=t_val+1;
				$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=t_val%1;
				if(z2==0)
				{
					t_val_scroll=t_val_scroll-30;
					$("#test_d").scrollTop(t_val_scroll)
					
				}
			}	
		}
		else if(unicode==27)
		{
			$("#test").val("");
			$("#test_d").html("");
			$("#list_all_test").slideUp(300);
			
			$("html, body").animate({ scrollTop: 1500 })
			$("#dis_per").focus();
		}
		else
		{
			$.post("pages/load_test_ajax.php",
			{
				test:val,
			},
			function(data,status)
			{
				$("#test_d").html(data);
				t_val=1;
				t_val_scroll=0;
				$("#test_d").scrollTop(t_val_scroll)
			})
		}
	}
	function load_test_new(id,name,rate)
	{
		//$(".up_div").fadeIn(500);
		var test_chk= $('#test_list tr').length;
		if(test_chk==0)
		{	
			var test_add="<table class='table table-bordered' id='test_list'>";	
			test_add+="<tr><th colspan='3' style='background-color:#cccccc'>Tests</th><th style='text-align:right;background-color:#cccccc'>Total:<span id='test_total'></span></th></tr>";
			test_add+="<tr><td>1</td><td width='70%'>"+name+"<input type='hidden' value='"+id+"' class='test_id'/></td><td contentEditable='true' onkeyup='load_cost(2)'><span class='test_f'>"+rate+"</span></td><td onclick='delete_rows(this,2)'><span class='text-danger'><i class='icon-remove'></i></span></td></tr>";
			test_add+="</table>";
			//test_add+="<div style='display:none;text-align:center;background-color:#cccccc;font-weight:bold;font-size:15px;padding:5px' id='test_hidden_price'>Total:<span id='test_total_hidden'></span></div>";
			
			$("#ss_tests").html(test_add);
			test_chk++;
		
			var tot=0;
			var tot_ts=document.getElementsByClassName("test_f");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseInt(tot_ts[j].innerHTML);
			}
			$("#test_total").text(tot);
			$("#test").val("");
		}
		else
		{
			
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id");
			
			for(var i=0;i<test_l.length;i++)
			{
					if(test_l[i].value==id)
					{
						t_ch=1;
					}
			}
			if(t_ch)
			{

				$("#test_sel").css({'opacity':'0.5'});
				$("#msg").text("Already Selected");
				var x=$("#test_sel").offset();
				var w=$("#msg").width()/2;
				$("#msg").css({'top':x.top,'left':'50%','margin-left':-w+'px'});
				$("#msg").fadeIn(500);
				setTimeout(function(){$("#msg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'});
				})},600);
				
			}			
			else
			{
		
			var tr=document.createElement("tr");
			var td=document.createElement("td");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var tbody=document.createElement("tbody");
			
			td.innerHTML=test_chk;
			td1.innerHTML=name+"<input type='hidden' value='"+id+"' class='test_id'/>"
			td2.innerHTML="<span class='test_f'>"+rate+"</span>";
			td2.setAttribute("contentEditable","true");
			td2.setAttribute("onkeyup","load_cost(2)");
			td3.innerHTML="<span class='text-danger'><i class='icon-remove'></i></span>";
			td3.setAttribute("onclick","delete_rows(this,2)");
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tbody.appendChild(tr);		
			document.getElementById("test_list").appendChild(tbody);
			var tot=0;
			var tot_ts=document.getElementsByClassName("test_f");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseInt(tot_ts[j].innerHTML);
			}
				$("#test_total").text(tot);
			}
			
			/*
			if(test_chk>4)
			{
				$("#list_all_test").css({'height':'220px','overflow':'scroll','overflow-x':'hidden'})			
				$("#list_all_test").animate({ scrollTop: 2900 });
				$("#test_hidden_price").fadeIn(200);
				$("#test_total_hidden").text($("#test_total").text());
			}
			*/
			$("#test").val("");
		}
		add_vaccu();
		add_grtotal();
	}
	function add_vaccu()
	{
		var vac_chk=$("#vaccu_charge").val();
		
		if(vac_chk>0)
		{
			var test_id=$(".test_id");
			var test_all="";
			for(var i=0;i<test_id.length;i++)
			{
					test_all=test_all+"@"+test_id[i].value;
			}
			
			$.post("pages/pat_reg_vac.php",
			{
				test_all:test_all
			},
			function(data,status)
			{
				$("#list_all_extra").html(data);
				
				var tot=0;
				var tot_ts=$(".extra_price");
				for(var j=0;j<tot_ts.length;j++)
				{
					tot=tot+parseInt(tot_ts[j].innerHTML);
				}
				
				$("#grextra").text(tot);
				$("#extra_total").text(tot);
				
				add_grtotal();
			})
		}
		
	}
	function load_cost(chk)
	{
		if(chk==2)
		{
			var tot=0;
			var tot_ts=document.getElementsByClassName("test_f");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseInt(tot_ts[j].innerHTML);
			}
			$("#test_total").text(tot);
		}
		add_grtotal()
	}
	function load_tab(id,chk)
	{
		if(id==1)
		{
			$(".up_div").slideUp(500);
			if(!chk)
			{
				$("#test_sel").fadeOut(400);
				$("#h_pack").fadeOut(400);
				$("#serv").fadeOut(400);
				
				$("#consultation").fadeIn(400);
			}
			if($("#list_all_extra").css('display')=="none")
			{
				$("#list_all_extra").slideToggle(500);
			}
			$("#test").focus();
		}
		else if(id==2)
		{
			$(".up_div").slideUp(500);
			if(!chk)
			{
				$("#consultation").fadeOut(400);
				$("#h_pack").fadeOut(400);
				$("#serv").fadeOut(400);
				
				$("#test_sel").fadeIn(400);
			}

			
			if($("#list_all_test").css('display')=="none")
			{
				$("#list_all_test").slideDown(500,function(){ if(!chk){$("#test").focus(); } })	
			}
			else
			{
				$("#list_all_test").slideUp(500)
			}
			$("#test").focus();
		}
		else if(id==3)
		{
			$(".up_div").slideUp(500);
			if(!chk)
			{
				$("#consultation").fadeOut(400);
				$("#test_sel").fadeOut(400);	
				$("#serv").fadeOut(400);
				
				$("#h_pack").fadeIn(400);
			}
			$("#list_all_health").slideDown(500,function(){ if(!chk){ $("#pack").focus();} })
			
		}
		else if(id==4)
		{
			$(".up_div").slideUp(500);
			if(!chk)
			{
				$("#consultation").fadeOut(400);
				$("#test_sel").fadeOut(400);	
				$("#h_pack").fadeOut(400);
				
				$("#serv").fadeIn(400);
			}
			$("#list_all_serv").slideDown(500,function(){ if(!chk){ $("#service").focus(); } })
		}	
	}
	function delete_rows(tab,num)
	{
		$(tab).parent().remove()
		load_cost(num);
		$("#test").focus();
		add_vaccu();
	}
	function add_grtotal()
	{
		var extra=parseInt($("#extra_total").text());
		if(!extra) { extra=0;}
		
		var tst=parseInt($("#test_total").text());
		if(!tst) { tst=0;}
		
		var hlt=parseInt($("#hlt_total").text());
		if(!hlt) { hlt=0;}
		
		var serv=parseInt($("#serv_total").text());
		if(!serv) { serv=0;}
		
		var grtotal=extra+tst+hlt+serv
		
		$("#grextra").text(extra);
		$("#grtest").text(tst);
		$("#grhealth").text(hlt);
		$("#grserv").text(serv);
		$("#grtotal").text(grtotal);
		
		$("#list_all_grtotal").fadeIn(200);
		
		//var regd_fee=parseInt($("#regd_fee").val());
		var tot_amt=tst+extra;
		$("#total").val(tot_amt);
		if($("#save").val()!="Update")
		{
			$("#advance").val(tot_amt);
			$("#balance").val("0");	
		}
		else
		{
			var dis_amnt=$("#dis_amnt").val();
			if(dis_amnt>0)
			{
				var bal=(tot_amt-dis_amnt)-$("#advance").val();
				$("#balance").val(bal);	
			}else
			{
				var bal=tot_amt-$("#advance").val();
				$("#balance").val(bal);	
			}
		}
		
	}
	function dis_per(val,e)
	{
		var error=0;
		if(e.which==13)
		{
			$("#dis_amnt").focus();
		}
		var tot=$("#total").val();
		var dis_val=((tot*val)/100);
		
		$("#dis_amnt").val(dis_val);
		$("#advance").val(tot-dis_val);
		$("#balance").val("0");
		if(dis_val>tot)
		{
			$("#dis_per").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#dis_per").css({'border-color': '#CCC'});
		}
		if(dis_val>0)
		{
			$("#d_reason").fadeIn(500);
		}else
		{
			$("#d_reason").fadeOut(500);
		}
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#dis_per").val(val);
		}
	}
	function dis_amnt(val,e)
	{
		var tot=parseInt($("#total").val());
		var per=((val*100)/tot);
		$("#dis_per").val(per);
		$("#advance").val(tot-val);
		$("#balance").val("0");
		
		if(val>0)
		{
			if(val>tot)
			{
				$("#dis_amnt").css({'border-color': '#F00'}).focus();
			}else
			{
				$("#dis_amnt").css({'border-color': '#CCC'});
				if(e.which==13)
				{
					$("#dis_reason").focus();
				}
			}
			$("#d_reason").fadeIn(500);
		}else
		{
			$("#d_reason").fadeOut(500);
			if(e.which==13)
			{
				$("#dis_amnt").val("0");
				$("#advance").focus();
			}
		}
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#dis_amnt").val(val);
		}
	}
	function dis_reason(val,e)
	{
		if(e.which==13)
		{
			if(val=="")
			{
				$("#dis_reason").css({'border-color': '#F00'}).focus();
			}else
			{
				$("#dis_reason").css({'border-color': '#CCC'});
				$("#advance").focus();
			}
		}else
		{
			$("#dis_reason").css({'border-color': '#CCC'});
		}
	}
	function advance(val,e)
	{
		var tot=parseInt($("#total").val());
		var dis_amnt=parseInt($("#dis_amnt").val());
		var res=tot-dis_amnt;
		var bal=res-val;
		$("#balance").val(bal);
		if(bal<0)
		{
			$("#advance").css({'border-color': '#F00'}).focus();
		}else
		{
			$("#advance").css({'border-color': '#CCC'});
		}
		if(bal<0)
		{
			$("#b_reason").fadeOut();
		}else if(bal>0)
		{
			$("#b_reason").fadeIn();
			
		}else
		{
			$("#b_reason").fadeOut();
		}
		if(e.which==13)
		{
			if(bal<0)
			{
				$("#advance").focus();
			}else if(bal>0)
			{
				$("#bal_reason").focus();
			}else
			{
				$("#pay_mode").focus();
			}
		}
		var n=val.length;
		var numex=/^[0-9.]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#advance").val(val);
		}
	}
	function bal_reason(val,e)
	{
		if(e.which==13)
		{
			if(val=="")
			{
				$("#bal_reason").css({'border-color': '#F00'}).focus();
			}else
			{
				$("#bal_reason").css({'border-color': '#CCC'});
				$("#pay_mode").focus();
			}
		}else
		{
			$("#bal_reason").css({'border-color': '#CCC'});
		}
	}
	function pay_mode(val,e)
	{
		if(e.which==13)
		{
			$("#save").focus();	
		}
	}
	function save_test()
	{
		var tst=$("input.test_id").map(function()
		{
			return this.value;
		}).get().join(",");
		
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			batch:$("#batch").val(),
			tst:tst,
			usr:$("#user").text().trim(),
			tot:$("#total_amount").val(),
			disc:$("#discount").val(),
			adv:$("#advance").val(),
			bal:$("#balance").val(),
			type:11,
		},
		function(data,status)
		{
			investigation($("#batch").val());
		})
	}
	/*function load_test2(id,name,pr)
	{
		var tr=document.createElement("tr");
		var td=document.createElement("td");
		var td1=document.createElement("td");
		var td2=document.createElement("td");
		var td3=document.createElement("td");
		var td4=document.createElement("td");
		td.className="slno_chk";
		td1.className="testid";
		td3.className="test_price";
		var ts=document.getElementsByClassName("testid");
		for(var i=0;i<ts.length;i++)
		{
			if(id==ts[i].innerHTML)
			{
				var j=1;
				var msg="Already Selected";	
			}
		}
		if(id=="T1064" || id=="T204" || id=="T259")
		{
			if($("#pinfo2").val()=="M")
			{
				var k=1;		
				var msg="Please change gender";
			
			}
			if($("#pinfo2").val()=="F")
			{
				if($("#pinfo3").val()<18)
				{
					var k=1;		
					var msg="Age has to be more than 17";
				}
			}         
			
		}
		if(!j && !k)
		{
			var t=document.getElementById('t_det');
			var num = t.rows.length;
			td.innerHTML=num;
			td1.innerHTML=id;
			td2.innerHTML=name;

			//...........checking for test master rate for centers

			$.post("pages/test_rate_center.php",
			{
				test:id,
				prc:pr,
			},
			function(data,status)
			{
					$(td3).text(data);
					cal_cost();
			})
			
			td4.innerHTML="<span class='text-danger'><i class='fa fa-times-circle fa-lg'></i></span>";

			td4.onclick=function(){t.removeChild(tr); cal_cost();};
			td4.style.cursor="pointer";
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			t.appendChild(tr);
			$("#test").focus();
			if(num>2 && num<6)
			{
				var w=window.pageYOffset-50;
				$("html, body").animate({ scrollTop: w }, "slow");
			}
			else if(num>5)
			{
				var h=$("#test_res").height();
				$("#test_res").css({'max-height':h,'overflow':'scroll','overflow-x':'hidden'});
			}
			$("#test_up").slideDown("slow");
			$("#test").val("");
		
		}
		else
		{
			$("#test_sel").css({'opacity':'0.5'});
			$("#msg").text(msg);
			var x=$("#test_sel").offset();
			var w=$("#msg").width()/2;
			$("#msg").css({'top':x.top-50,'left':'50%','margin-left':-w+'px'});
			$("#msg").fadeIn(500);	
			setTimeout(function(){$("#msg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'});
			})},600);
		}
	}*/
	function cal_cost(val,t)
	{
		var c=document.getElementsByClassName('test_price');
		var tot2=0;
		for(var i=0;i<c.length;i++)
		{
				tot2=tot2+parseInt(c[i].innerHTML);	
		}

		$("#total_t").html(tot2+"/-");
		if(t=="upd")
		{
			$("#total").val(tot2);
			if($("#save_pay").val()=="Save")
			{
				$("#advance").val(tot2);
			}	
		}
		else if(t=="sav")
		{
			$("#total").val(tot2);
			$("#advance").val(tot2);
			$("#advance").val(tot2);
		}
		if(val)
		{
			document.getElementById("dis_amnt").disabled=true
			document.getElementById("advance").disabled=true
			document.getElementById("balance").disabled=true;
			//document.getElementById("hid_ptest").focus();
		}
		else
		{
		var totm=parseInt($("#dis_amnt").val())+parseInt($("#advance").val())
		var bal=parseInt($("#total").val())-totm;
		$("#balance").val(bal);
		
		
			var sln=document.getElementsByClassName("slno_chk");
			for(var i=0;i<=sln.length;i++)
			{
				sln[i].innerHTML=i+1;		
			}
		}
	}
	function ipd_print_receipt(url)
	{
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
	}
	function ipd_money_receipt(url)
	{
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
	}
	function load_test_print(uhid,ipd,batch)
	{
		$.post("pages/ipd_pat_reg_prints.php",
		{
			uhid:uhid,
			ipd_id:ipd,
			batch_no:batch,
		},
		function(data,status)
		{
			$("#results2").html(data);
			//$(".modal-dialog").css({'width':'500px'});		
			$("#mod2").click();
			//$("#mod_chk").val("1");
			$("#results").fadeIn(500);
		})
	}
	function print_indiv(uhid,ipd_id,batch_no)
	{
		var norm=$(".norm:checked");
		var norm_l=0;
		if(norm.length>0)
		{
			for(var i=0;i<norm.length;i++)
			{
				norm_l=norm_l+"@"+$(norm[i]).val();
			}
		}
		
		var path=$(".path:checked");
		var path_l=0;
		if(path.length>0)
		{
			for(var j=0;j<path.length;j++)
			{
				path_l=path_l+"@"+$(path[j]).val();
			}
		}
		
		
		var rad=$(".rad:checked");
		var rad_l=0;
		if(rad.length>0)
		{
		for(var k=0;k<rad.length;k++)
			{
				rad_l=rad_l+"@"+$(rad[k]).val();
			}
		}

		//var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opd_id="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
		var url="pages/ipd_print1_rpt_indv.php?uhid="+uhid+"&ipdid="+ipd_id+"&batch_no="+batch_no+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');		
	}
	/*function investigation(batch)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			type:4,
		},
		function(data,status)
		{
			$("#cl3").html(data);
			if(batch!=0)
			$("#ad"+batch).click();
			else
			{				
				var b=document.getElementsByClassName("bt");
				$("#ad"+b.length).click();
			}
			$("html,body").animate({scrollTop: '200px'},500);
		})
	}*/
	function view_batch(batch)
	{
		$(".bt").removeClass('btt');
		$("#ad"+batch).addClass('btt');
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd_id:$("#ipd").val(),
			batch_no:batch,
			user:$("#user").text().trim(),
			lavel:$("#lavel_id").val(),
			type:10,
		},
		function(data,status)
		{
			$("#cl_sub"+batch+"").html(data).fadeIn(200,function(){ $("#collapse_sub"+batch+"").css({"height":"auto"})});
			/*
			$("#batch_details").html(data);
			$("#foll_details").html('');
			*/
		})
	}
	function ad_tests(batch)
	{
		$("#mod").click();
		$("#foot").show();
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			batch:batch,
			type:5,
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'22%',"width":"1300px","height":"500px",'margin':'auto'},"slow",function(){ $("#test").focus();});
		})
	}
	function disc_calc(val)
	{
		if(!val)
		{
			val=0;	
		}
		var tot_v=$("#total_amount").val();
		var adv=$("#advance").val();
		var chk=adv-val;
		
			
		var advn=tot_v-val;
		var dis1=parseInt(advn)+parseInt(val);
		var dis=tot_v-dis1;
		$("#balance").val(dis);	
		$("#advance").val(advn);
			
	}
	function adv_calc(val)
	{
			var tot_v=$("#total_amount").val();
			var dis=$("#discount").val();
			var chk=tot_v-dis;
			var chk2=chk-val;
			
			
				$("#balance").val(chk2);	
			
				/*
				if(chk2>0)

				{
					$("#mon_rec1").attr("disabled",true);
					$("#bal_reas").fadeIn(200);
				}
				else
				{
					$("#mon_rec1").attr("disabled",false);
					$("#bal_reas").fadeOut(200);
				}
				
				
				if($("#money_receipt_check").val()>0)
				{
					$("#mon_rec1").attr("disabled",false);
				}
				*/
		
	}
	
	/*function delete_rows(tab,num)
	{
		$(tab).parent().remove();
		//$("#test").focus();	
			
		var tot=0;
		
		var tot_ts=document.getElementsByClassName("test_rate");
		for(var j=0;j<tot_ts.length;j++)
		{
			tot=tot+parseInt(tot_ts[j].value);
		}
		$("#total_amount").val(tot);
		
	}
	var t_val=1;
	var t_val_scroll=0;
	function select_test_new(val,e)
	{
		var z="";
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var tst=document.getElementsByClassName("test"+t_val);
			load_test_new(''+tst[1].value+'',''+tst[2].innerHTML+'',''+tst[3].innerHTML+'');
			$("#test_list_ab").slideUp(100);
			$("#test").val("").focus();
		}
		else if(unicode==40)
		{
			var chk=t_val+1;
			var cc=document.getElementById("td"+chk).innerHTML;
			if(cc)
			{
				t_val=t_val+1;
				$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var t_val1=t_val-1;
				$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=t_val%1;
				if(z2==0)
				{
					$("#test_d").scrollTop(t_val_scroll)
					t_val_scroll=t_val_scroll+30;
				}
			}	
		}
		else if(unicode==38)
		{
			var chk=t_val-1;
			var cc=document.getElementById("td"+chk).innerHTML;
			if(cc)
			{
				t_val=t_val-1;
				$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var t_val1=t_val+1;
				$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=t_val%1;
				if(z2==0)
				{
					t_val_scroll=t_val_scroll-30;
					$("#test_d").scrollTop(t_val_scroll)
					
				}
			}	
		}
		else if(unicode==27)
		{
			$("#test").val("");
			$("#test_d").html("");
			//$("#list_all_test").slideUp(300);
			
			$("html, body").animate({ scrollTop: 500 })
			$("#dis_per").focus();
		}
		else
		{
			$("#test_list_abs").slideUp(400);
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				batch:$("#batch").val(),
				test:val,
				type:12
			},
			function(data,status)
			{
				$("#test_d").html(data);
				t_val=1;
				t_val_scroll=0;
				$("#test_d").scrollTop(t_val_scroll)
			})
		}
	}
	function load_test_new(id,name,rate)
	{
		//$(".up_div").fadeIn(1);
		//$(".up_div").fadeOut(1);
		var test_chk= $('#test_list tr').length;
		if(test_chk==0)
		{
			var test_add="<table class='table table-condensed table-bordered' style='style:none' id='test_list'>";
			test_add+="<tr><th style='background-color:#cccccc'>Sl No</th><th style='background-color:#cccccc'>Tests</th><th style='background-color:#cccccc'>Rate</th><th style='background-color:#cccccc'>Remove</th></tr>";
			test_add+="<tr><td>1</td><td width='50%'>"+name+"<input type='hidden' value='"+id+"' class='test_id'/></td><td><input type='hidden' class='test_rate' value='"+rate+"'/>"+rate+"</td><td onclick='delete_rows(this,2)'><span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span></td></tr>";
			test_add+="</table>";
			//test_add+="<div style='display:none;text-align:center;background-color:#cccccc;font-weight:bold;font-size:15px;padding:5px' id='test_hidden_price'>Total:<span id='test_total_hidden'></span></div>";
			
			$("#ss_tests").html(test_add);
			test_chk++;
		
			var tot=0;
			var tot_ts=document.getElementsByClassName("test_rate");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseInt(tot_ts[j].value);
			}
			$("#total_amount").val(tot);
			$("#test").val("");
		}
		else
		{
			
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id");
			
			for(var i=0;i<test_l.length;i++)
			{
					if(test_l[i].value==id)
					{
						t_ch=1;
					}
			}
			if(t_ch)
			{

				$("#test_sel").css({'opacity':'0.5'});
				$("#msgg").text("Already Selected");
				var x=$("#test_sel").offset();
				var w=$("#msgg").width()/2;
				//$("#msgg").css({'top':'50%','left':'50%'});
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'});
				})},600);
				
			}			
			else
			{
		
			var tr=document.createElement("tr");
			var td=document.createElement("td");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var tbody=document.createElement("tbody");
			
			td.innerHTML=test_chk;
			td1.innerHTML=name+"<input type='hidden' value='"+id+"' class='test_id'/>"
			td2.innerHTML="<input type='hidden' class='test_rate' value='"+rate+"'/>"+rate;
			//td2.setAttribute("contentEditable","true");
			//td2.setAttribute("onkeyup","load_cost(2)");
			td3.innerHTML="<span class='text-danger' style='cursor:pointer'><i class='icon-remove'></i></span>";
			td3.setAttribute("onclick","delete_rows(this,2)");
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tbody.appendChild(tr);		
			document.getElementById("test_list").appendChild(tbody);
			var tot=0;
			var tot_ts=document.getElementsByClassName("test_rate");
			for(var j=0;j<tot_ts.length;j++)
			{
				tot=tot+parseInt(tot_ts[j].value);
			}
				$("#total_amount").val(tot);
			}
			
			if(test_chk>4)
			{
				$("#list_all_test").css({'height':'220px','overflow':'scroll','overflow-x':'hidden'})			
				$("#list_all_test").animate({ scrollTop: 2900 });
				$("#test_hidden_price").fadeIn(200);
				$("#test_total_hidden").text($("#test_total").text());
			}
			$("#test").val("");
		}
		
		//add_vaccu();
	}*/
	function load_payment()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			process:$("#process").val(),
			usr:$("#user").text().trim(),
			type:1,
		},
		function(data,status)
		{
			$("#cl1").html(data);
			/*bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);*/
		})
	}
	function medication()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			type:13,
		},
		function(data,status)
		{
			$("#cl2").html(data);
		})
	}
	function ad_med(batch,plan)
	{
		
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			batch:batch,
			plan:plan,
			type:14,
		},
		function(data,status)
		{
			//$("#med_list").html(data);
			$("#myModal1").animate({'top':'5%','left':'25%',"width":"1200px","height":"500px",'margin':'auto'},"slow");
			$("#mod").click();
			$("#results").html(data);
		})
	}
	function load_medi_list()
	{
		//$("html,body").animate({scrollTop: '300px'},500);med_list
		$("#med_div").fadeIn(500);
		$("#medi").select();
		setTimeout(function(){$("#chk_val1").val(1)},1000);
		//setTimeout(function(){$("#results").css('height','400px');},100);
	}
	var med_tr=1;
	var med_sc=0;
	function load_medi_list1(val,e)
	{
			$("#med_dos").hide();
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode!=13)
			{
				if(unicode!=40 && unicode!=38)
				{
					$("#med_div").html("<img src='../images/ajax-loader.gif' />");
					$("#med_div").fadeIn(500);
					$.post("pages/ipd_dash_pat_ajax.php"	,
					{
						val:val,
						type:15
					},
					function(data,status)
					{
						$("#med_div").html(data);	
						med_tr=1;
						med_sc=0;
					})	
				}
				else if(unicode==40)
				{
					var chk=med_tr+1;
					var cc=document.getElementById("med"+chk).innerHTML;
					if(cc)
					{
						med_tr=med_tr+1;
						$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var med_tr1=med_tr-1;
						$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
						var z3=med_tr%1;
						if(z3==0)
						{
							$("#med_div").scrollTop(med_sc)
							med_sc=med_sc+30;
						}
					}
				}
				else if(unicode==38)
				{
					var chk=med_tr-1;
					var cc=document.getElementById("med"+chk).innerHTML;
					if(cc)
					{
						med_tr=med_tr-1;
						$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var med_tr1=med_tr+1;
						$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
						var z3=med_tr%1;
						if(z3==0)
						{
							med_sc=med_sc-30;
							$("#med_div").scrollTop(med_sc)
						}
					}
				}
				
			}
			else
			{
				var cen_chk1=document.getElementById("chk_val1").value
				if(cen_chk1!=0)
				{
					var docs=document.getElementById("mdname"+med_tr).innerHTML.split("#");
					var doc_naam=docs[2].trim()
					$("#medi").val(doc_naam);
					$("#medid").val(docs[1]);
					$("#unit").val(docs[3]);
					var d_in=docs[5];
					//$("#doc_mark").val(docs[5]);
					$("#med_info").html(d_in);
					$("#med_info").fadeIn(500);
					$("#g_name").show();
					select_med(docs[1],docs[2],docs[3],docs[4]);
					if($("#focus_chk").val()!="")
					{
						//$("html, body").animate({ scrollTop: 350 })	
						$("#dos").focus();
					}
					else
					{
						$("#dos").focus();	
					}
					load_generic(docs[4]);
				}
			}
	}
	function select_med(id,name,typ,gen)
	{
		//alert(id+' '+name+' '+typ+' '+gen);
		$("#medi").val(name);
		$("#medid").val(id);
		$("#med_info").html("");
		$("#med_div").fadeOut(500);
		$("#unit").val(typ);
		select_medi();
		load_generic(gen);
	}
	function load_generic(id)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			id:id,
			type:16,
		},
		function(data,status)
		{
			$("#generic").val(data);
		})
	}
	function select_medi()
	{
		$("#med_dos").show();
		$("#g_name").show();
		$("#dos").focus();
	}
	function insert_medi()
	{
		if($("#dos").val()=="0")
		{
			$("#dos").focus();
		}
		else if($("#freq").val()=="0")
		{
			$("#freq").focus();
		}
		else if($("#st_date").val()=="")
		{
			$("#st_date").focus();
		}
		else if($("#dur").val()=="0")
		{
			$("#dur").focus();
		}
		else if($("#unit_day").val()=="0")
		{
			$("#unit_day").focus();
		}
		else
		{
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				uhid:$("#uhid").val(),
				opd:$("#opd").val(),
				medi:$("#medid").val(),
				dos:$("#dos").val(),
				unit:$("#unit").val(),
				freq:$("#freq").val(),
				st_date:$("#st_date").val(),
				dur:$("#dur").val(),
				unit_day:$("#unit_day").val(),
				totl:$("#totl").val(),
				inst:$("#inst").val(),
				sos:$('input[type=checkbox]:checked').val(),
				usr:$("#user").text(),
				type:17,
			},
			function(data,status)
			{
				$("#dos").val('0');
				$("#unit").val('');
				$("#freq").val('0');
				$("#st_date").val('');
				$("#dur").val('0');
				$("#unit_day").val('0');
				$("#totday").val('');
				$("#inst").val('1');
				$("#sos").attr("checked",false);
				$("#med_dos").hide();
				$("#g_name").hide();
				load_medi();
				setTimeout(function(){$('#medi').val('');$("#medi").focus();$("#med_div").fadeOut(10);},100);
			})
		}
	}
	function set_medi()
	{
		if($("#mediname").val()=="")
		{
			$("#mediname").focus();
		}
		else if($("#dos").val()=="")
		{
			$("#dos").focus();
		}
		else if($("#freq").val()=="0")
		{
			$("#freq").focus();
		}
		else if($("#st_date").val()=="")
		{
			$("#st_date").focus();
		}
		else if($("#dur").val()=="0")
		{
			$("#dur").focus();
		}
		else if($("#unit_day").val()=="0")
		{
			$("#unit_day").focus();
		}
		else if($("#inst").val()=="")
		{
			$("#inst").focus();
		}
		else
		{
			$("#medi_list_post").show();
			var fq="";
			var ins="";
			var m=$("#mediname").val();
			var medid=0;
			var dos=$("#dos").val();
			var unit=0;
			var freq=$("#freq").val();
			var dur=$("#dur").val();
			var unit_day=$("#unit_day").val();
			var totl=$("#totl").val();
			var inst=$("#inst").val();
			var st_date=$("#st_date").val();
			var con_doc=0;
			if(freq=='1')
			fq="Immediately";
			else if(freq=='2')
			fq="Once a day";
			else if(freq=='3')
			fq="Twice a day";
			else if(freq=='4')
			fq="Thrice a day";
			else if(freq=='5')
			fq="Four times a day";
			else if(freq=='6')
			fq="Five times a day";
			else if(freq=='7')
			fq="Every hour";
			else if(freq=='8')
			fq="Every 2 hours";
			else if(freq=='9')
			fq="Every 3 hours";
			else if(freq=='10')
			fq="Every 4 hours";
			else if(freq=='11')
			fq="Every 5 hours";
			else if(freq=='12')
			fq="Every 6 hours";
			else if(freq=='13')
			fq="Every 7 hours";
			else if(freq=='14')
			fq="Every 8 hours";
			else if(freq=='15')
			fq="Every 10 hours";
			else if(freq=='16')
			fq="Every 12 hours";
			if(inst=="1")
			ins="As Directed";
			else if(inst=="2")
			ins="Before Meal";
			else if(inst=="3")
			ins="Empty Stomach";
			else if(inst=="4")
			ins="After Meal";
			else if(inst=="5")
			ins="In the Morning";
			else if(inst=="6")
			ins="In the Evening";
			else if(inst=="7")
			ins="At Bedtime";
			else if(inst=="8")
			ins="Immediately";
			var med_add= $('#medi_sel_list tr').length;
			if(med_add==0)
			{
				var md_add="<table class='table table-condensed table-bordered' style='style:none' id='medi_sel_list'>";
				md_add+="<tr><th style='width:3%;background-color:#cccccc'>SN<input type='hidden' id='batch' value='' class='m_val'/><span style='display:none;position:fixed;font-size:22px;top:30%;left:40%;color:#e00;' id='msgg'></span></th><th style='width:40%;background-color:#cccccc'>Drugs</th><th style='width:5%;background-color:#cccccc'>Dosage</th><th style='background-color:#cccccc'>Frequency</th><th style='width:8%;background-color:#cccccc'>Duration</th><th style='width:5%;background-color:#cccccc'>Total</th><th style='background-color:#cccccc'>Instruction</th><th style='width:3%;background-color:#cccccc'><i class='icon-trash icon-large'></i></span></th></tr>";
				md_add+="<tr id='"+medid+"' class='disc_medi'><td>1</td><td>"+m+"<input type='hidden' value='"+m+"' class='m_val'/></td><td>"+dos+"<input type='hidden' value='"+dos+"' class='m_val'/></td><td>"+fq+"<input type='hidden' value='"+freq+"' class='m_val'/></td><td>"+dur+" "+unit_day+"<input type='hidden' value='"+dur+"' class='m_val'/><input type='hidden' value='"+unit_day+"' class='m_val'/></td><td>"+totl+"<input type='hidden' value='"+totl+"' class='m_val'/></td><td>"+ins+"<input type='hidden' value='"+inst+"' class='m_val'/></td><td><input type='hidden' value='"+st_date+"' class='m_val'/><span class='text-danger' onclick='$(this).parent().parent().remove()' style='cursor:pointer'><i class='icon-remove'></i></span></td></tr>";
				md_add+="</table>";
				
				$("#medi_list_data").html(md_add);
				med_add++;
			}
			else
			{
				/*if(t_ch==1)
				{
					$("#medi_sel_list").css({'opacity':'0.5'});
					$("#msgg").text("Already Selected");
					$("#msgg").fadeIn(500);
					setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#medi_sel_list").css({'opacity':'1.0'});
					})},600);	
				}*/			
				//else
				{
					var tr=document.createElement("tr");
					tr.setAttribute("id",medid);
					tr.setAttribute("class","disc_medi");
					var td=document.createElement("td");
					var td1=document.createElement("td");
					var td2=document.createElement("td");
					var td3=document.createElement("td");
					var td4=document.createElement("td");
					var td5=document.createElement("td");
					var td6=document.createElement("td");
					var td7=document.createElement("td");
					var tbody=document.createElement("tbody");
					td.innerHTML=med_add;
					td1.innerHTML=m+"<input type='hidden' value='"+m+"' class=''/>";
					td2.innerHTML=dos+"<input type='hidden' value='"+dos+"' class=''/>";
					td3.innerHTML=fq+"<input type='hidden' value='"+freq+"' class=''/>";
					td4.innerHTML=dur+" "+unit_day+"<input type='hidden' value='"+dur+"' class=''/><input type='hidden' value='"+unit_day+"' class='m_val'/>";
					td5.innerHTML=totl+"<input type='hidden' value='"+totl+"' class=''/>";
					td6.innerHTML=ins+"<input type='hidden' value='"+inst+"' class=''/>";
					td7.innerHTML="<input type='hidden' value='"+st_date+"' class=''/><span class='text-danger' onclick='$(this).parent().parent().remove()' style='cursor:pointer'><i class='icon-remove'></i></span>";
					tr.appendChild(td);
					tr.appendChild(td1);
					tr.appendChild(td2);
					tr.appendChild(td3);
					tr.appendChild(td4);
					tr.appendChild(td5);
					tr.appendChild(td6);
					tr.appendChild(td7);
					tbody.appendChild(tr);		
					document.getElementById("medi_sel_list").appendChild(tbody);
				}
			}
			$("#mediname").val('');
			$("#dos").val('');
			$("#unit").val('');
			$("#freq").val('0');
			$("#st_date").val('');
			$("#dur").val('0');
			$("#unit_day").val('0');
			$("#totl").val('');
			$("#inst").val('1');
			$("#mediname").focus();
		}
	}
	function load_medi()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			type:"load_medi_ipd",
		},
		function(data,status)
		{
			$("#medi_list").show();
			$("#medi_list_data").html(data);
		})
	}
	function calc_totday()
	{
		var tot=0;
		var freq=$("#freq").val();
		var unit=$("#unit_day").val();
		var dur=parseInt($("#dur").val());
		var dos=parseInt($("#dos").val());
		if(dos>0)
		{
			if(unit=="Days")
			tot=(dur*dos*1);
			else if(unit=="Weeks")
			tot=(dur*dos*7);
			else if(unit=="Months")
			tot=(dur*dos*30);
			if(freq=="1")
			tot=tot*1;
			else if(freq=="2")
			tot=tot*1;
			else if(freq=="3")
			tot=tot*2;
			else if(freq=="4")
			tot=tot*3;
			else if(freq=="5")
			tot=tot*4;
			else if(freq=="6")
			tot=tot*5;
			else if(freq=="7")
			tot=tot*24;
			else if(freq=="8")
			tot=tot*12;
			else if(freq=="9")
			tot=tot*8;
			else if(freq=="10")
			tot=tot*6;
			else if(freq=="11")
			tot=tot*5;
			else if(freq=="12")
			tot=tot*4;
			else if(freq=="13")
			tot=tot*3;
			else if(freq=="14")
			tot=tot*3;
			else if(freq=="15")
			tot=tot*2;
			else if(freq=="16")
			tot=tot*2;
			else
			tot=0;
			$("#totl").val(tot);
		}
		else
		$("#totl").val('');
	}
	function addd()
	{
		$("#dl").click();
		$.post("pages/global_load_g.php",
		{
			type:"ipd_pat_add_diag",
		},
		function(data,status)
		{
			//$("#cl1").html(data);
			//$("#dl").click();
			$("#add_opt").html(data);
			setTimeout(function(){$("#diag").focus();},500);
		})
	}
	function ad()
	{
		$.post("pages/global_load_g.php",
		{
			type:"ipd_pat_doc_list",
		},
		function(data,status)
		{
			var rr=document.getElementById("diag_table").rows.length;
			if($("#tr"+(rr-1)).find('td:first input:first').val() && $("#tr"+(rr-1)).find('td:eq(1) select:first').val()!="0" && $("#tr"+(rr-1)).find('td:eq(2) select:first').val()!="0" && $("#tr"+(rr-1)).find('td:eq(3) select:first').val()!="0")
			$('#diag_table').append('<tr id="tr'+rr+'"><td><input type="text" class="span4" onkeyup="diagtab(1,event)" id="diagnosis1" placeholder="Diagnosis" /></td><td><select id="order1" onkeyup="diagtab(1,event)" class="span2"><option value="0">--Select--</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td><td><select id="cert1" onkeyup="diagtab(1,event)" class="span2"><option value="0">--Select--</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td><td><select id="doc"><option value="0">Select</option>'+data+'</select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
		})
	}
	/*function save_test()
	{
		var tst=$("input.test_id").map(function()
		{
			return this.value;
		}).get().join(",");
		
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			batch:$("#batch").val(),
			tst:tst,
			usr:$("#user").text().trim(),
			tot:$("#total_amount").val(),
			disc:$("#discount").val(),
			adv:$("#advance").val(),
			bal:$("#balance").val(),
			type:11,
		},
		function(data,status)
		{
			alert(data);
			investigation($("#batch").val());
		})
	}*/
	function save()
	{
		var diag="";
		var rr=document.getElementById("diag_table").rows.length;
		for(var j=1;j<rr;j++)
		{
			if($("#tr"+j).find('td:first input:first').val() && $("#tr"+j).find('td:eq(1) select:first').val()!="0" && $("#tr"+j).find('td:eq(2) select:first').val()!="0" && $("#tr"+j).find('td:eq(3) select:first').val()!="0")
			diag+=$("#tr"+j).find('td:first input:first').val()+"@"+$("#tr"+j).find('td:eq(1) select:first').val()+"@"+$("#tr"+j).find('td:eq(2) select:first').val()+"@"+$("#tr"+j).find('td:eq(3) select:first').val()+"#g#";
		}
		/*$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			diag:diag,
			usr:$("#user").text(),
			type:"save_ipd_pat_diag_nurse",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				diagnosis();
			}, 1000);
		})*/
	}
	function bill_summary(val)
	{
		var uhid=btoa($("#uhid").val());
		var ipd=btoa($("#ipd").val());
		var usr=btoa($("#user").text().trim());
		
		if(val==1)
		{
			//url="pages/ipd_bill_summary.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&val="+val;
			url="pages/ipd_bill_summary_serv.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&val="+val;
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		}
		else if(val==2)
		{
			url="pages/ipd_bill_type_summary_new.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&val="+val;
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		}
		else if(val==3)
		{
			url="pages/ipd_bill_type_detail_new.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&val="+val+"&pro=0";
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		}
		else if(val==33)
		{
			url="pages/ipd_bill_type_detail_new.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&val="+val+"&pro=1";
			wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		}
	}
	function out_amount(val)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			val:val,
			type:5001,
		},
		function(data,status)
		{
			$("#out_amt").html(data);
			//~ if($("#process").val()==1)
			//~ {
				//~ load_payment();
			//~ }
		})
	}
	function save_ipd_payment(val)
	{
		if($("#adv_payment").val().trim())
		{
			bootbox.dialog({ message: "<span id='ipd_pay'>Saving</span>"});
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				pay_ad:$("#adv_payment").val(),
				mode:$("#p_mode_ad").val(),
				reference_no:$("#reference_no_adv").val(),
				user:$("#user").text(),
				val:val,
				bill:$("#adv_bill_id").val(),
				type:2
			},
			function(data,status)
			{
				if(data)
				{
					$("#adv_payment").css({'border':'1px solid #CCC'});
					$("#ipd_pay").text(val+"d");
					$("#adv_bill_id").val(data);
					$("#save_ipd_pay").val("Update");
					
					setTimeout(function()
					{
						bootbox.hideAll();
						load_payment();
						out_amount('Advance');
					}, 1000);
				}
			})
		}
		else
		{
			$("#pay_advance").css({'border':'1px solid red'});
		}
	}
	
	function save_ipd_payment_final(val)
	{
		var bal=parseInt(($("#pat_balance").val() ? $("#pat_balance").val() :'0'));
		var pay=parseInt(($("#pay_advance").val() ? $("#pay_advance").val() :'0'));
		var disc=parseInt(($("#pat_disc").val() ? $("#pat_disc").val() :'0'));
		var tot=parseInt(($("#pat_total").text() ? $("#pat_total").text() :'0'));
		var pat_refund_amt=parseInt(($("#pat_refund_amt").text() ? $("#pat_refund_amt").text() :'0'));
		
		var balance=parseInt($("#pat_balance").text());
		
		var now_balance=parseInt($("#now_balance_backend").val());
		
		var rest_amount=now_balance-pay-disc;
		
		if(disc>now_balance)
		{
			$("#pat_disc").css({"border-color":"red"}).focus();
			return false;
		}
		if(rest_amount<0)
		{
			$("#pay_advance").css({"border-color":"red"}).focus();
			return false;
		}
		
		if(balance>0 && $("#balance_reason_dischrg").val()=="")
		{
			$("#balance_tr").show();
			$("#balance_reason_dischrg").focus();
			return false;
		}
		
		var res=0;
		
		if(res==0)
		{
			if(disc>0)
			{
				if($("#pat_disc_res").val()=="")
				{
					$("#pat_disc_res").focus();
					return false;
				}
			}
			
			bootbox.dialog({
				//title: "Patient Re-visit ?",
				//message: "<h5>Are you sure want to receive payment ?</h5>",
				message: "<b>Do you really want to discharge this patient ?</b>",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> Cancel',
						className: "btn btn-inverse",
						callback: function() {
						  bootbox.hideAll();
						}
					},
					confirm: {
						label: '<i class="icon-ok"></i> Confirm',
						className: "btn btn-danger",
						callback: function() {
							//bootbox.dialog({ message: "<span id='ipd_pay'>Saving</span>"});
							bootbox.dialog({ message: "<span id='discharge_text'><b>Discharging</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> </span>",closeButton: false});
							$.post("pages/ipd_dash_pat_ajax.php",
							{
								uhid:$("#uhid").val(),
								ipd:$("#ipd").val(),
								tot:tot,
								disc:disc,
								pay:pay,
								pat_refund_amt:pat_refund_amt,
								balance:balance,
								mode:$("#p_mode").val(),
								user:$("#user").text().trim(),
								val:val,
								bill:$("#bill_id").val(),
								dis_reason:$("#pat_disc_res").val(),
								reference_no:$("#fn_bal_reference_no").val(),
								balance_reason:$("#balance_reason_dischrg").val(),
								p_type_id:3, // IPD
								type:"final_2"
							},
							function(data,status)
							{
								//~ $("#ipd_pay").text("Saved");
								//~ load_payment();
								//~ setTimeout(function()
								//~ {
									//~ bootbox.hideAll();
									//~ out_amount('Discharge');
									//~ load_payment_div('final_bill');
									//~ $("#gen_final_bill").css({'display':'inline-block'});
								//~ }, 1000);
								
								setTimeout(function()
								{
									//bootbox.hideAll();
									final_dicharge();
								}, 2000);
							})
						}
					}
				}
			});
		}
		else
		{
			bootbox.dialog({ message: "<span id='ipd_pay'>There must not be any balance</span>"});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
		}
		
	}
	
	function print_payment_receipt(bill,val)
	{
		var uhid=$("#uhid").val();
		var ipd=$("#ipd").val();
		var usr=$("#user").text().trim();
		
		if(val=='100')
		{		
			//url="pages/ipd_bill_summary.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&val="+val;
			url="pages/ipd_payment_receipt.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&bill="+bill;
		}
		if(val=='101')
		{		
			//url="pages/ipd_bill_summary.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&val="+val;
			url="pages/dot_matrix_ipd_bill_type_detail_ipd.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr+"&bill="+bill;
		}
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		
	}
	
	function print_regd_receit(val)
	{
		var uhid=btoa($("#uhid").val());
		var ipd=btoa($("#ipd").val());
		var usr=btoa($("#user").text().trim());
		if(val==1)
		{
			url="pages/admission_sheet.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		}
		if(val==2)
		{
			url="pages/print_regd_form.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		}
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		
	}
	function cancel_payment_receipt(bill)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to cancel this payment</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-danger",
					callback: function() {
						ok_cancel_payment_receipt(bill);
					}
				}
			}
		});
		
	}
	
	function ok_cancel_payment_receipt(bill)
	{
		bootbox.dialog({
			message: "Reason:<input type='text' id='note' autofocus />",
			title: "Payment Cancel",
			buttons: {
				main: {
				  label: "Save",
				  className: "btn-primary",
				  callback: function() {
					if($('#note').val()!='')
					{
						$.post("pages/ipd_dash_pat_ajax.php",
						{
							uhid:$("#uhid").val(),
							ipd:$("#ipd").val(),
							bill:bill,
							usr:$("#user").text().trim(),
							reason:$('#note').val(),
							type:6001
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<b>Payment Cancelled</b>"});
							setTimeout(function()
							{
								bootbox.hideAll();
								load_payment();
								out_amount('Payment Cancel');
							}, 2000);
						})
					}else
					{
						bootbox.alert("Reason cannot blank");
					}
					
				  }
				}
			}
		});
	}
	function view_ipd_bills()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:3
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'35%',"width":"1000px","height":"500px",'margin':'auto'},"slow");
			if($("#mod_chk").val()==0)
			{
				$("#mod").click();
				
			}
		})
	}
	
	function edit_med(sl)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			sl:sl,
			type:36
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'50%',"width":"500px","height":"400px",'margin':'auto'},"slow");
			
			$("#mod").click();
		})	
	}
	function edit_med_row(sl)
	{
		bootbox.confirm("Do you really want to Update?",
	    function(result){ 
		if(result)
		{
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				sl:sl,
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				item:$("#edit_item").val(),
				batch:$("#edit_batch").val(),
				quan:$("#edit_quan").val(),
				mrp:$("#edit_mrp").val(),
				gst:$("#edit_gst").val(),
				amount:$("#edit_amount").val(),
				type:37
			},
			function(data,status)
			{
				pharmacy_details($("#ph_sub_id").val());
			})
		}
		})
	}
	
	function add_more_pharmacy(bill)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			bill:bill,
			type:38
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'50%',"width":"500px","height":"400px",'margin':'auto'},"slow");
			
			$("#mod").click();
		})
	}
	
	function load_med_batch(val)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			val:val,
			type:39
		},
		function(data,status)
		{
			var data=data.split("@#koushik#@");
			$("#edit_batch").html(data[0]);
			$("#edit_mrp").val(data[1]);
			$("#edit_gst").val(data[2]);
			$("#edit_amount").val("");
			$("#edit_quan").val("");
		})
	}
	function calc_ph_item_cost(val)
	{
		var mrp=$("#edit_mrp").val();
		var tot=val*mrp;
		
		if($("#edit_gst").val()>0)
		{
			var gst_tot=tot*$("#edit_gst").val()/100;
			tot=tot+gst_tot;
		}
				
		$("#edit_amount").val(tot);
	}
	
	function save_ph_row(bill)
	{
		bootbox.confirm("Do you really want to Save?",
	    function(result){ 
		if(result)
		{
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				item:$("#edit_item").val(),
				batch:$("#edit_batch").val(),
				quan:$("#edit_quan").val(),
				mrp:$("#edit_mrp").val(),
				gst:$("#edit_gst").val(),
				amount:$("#edit_amount").val(),
				bill:bill,
				type:40
			},
			function(data,status)
			{
				pharmacy_details($("#ph_sub_id").val());
			})
		}
		})
	}
	
	function consumable_details(i)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text(),
			val:$("#consm_"+i+"").val(),
			tab_id:i,
			type:41,
		},
		function(data,status)
		{
			$("#cl_sub"+i+"").html(data).fadeIn(200,function(){ $("#collapse_sub"+i+"").css({"height":"auto"})});
			$("html,body").animate({scrollTop: '800px'},800);
		})	
	}
	
	function edit_consm(val)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			val:val,
			type:42
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'50%',"width":"550px","height":"500px",'margin':'auto'},"slow");
			$(".modal-body").css({'max-height':'450px'});
			$("#mod").click();
		})
	}
	function update_consm(val,type_id)
	{
		bootbox.confirm("Do you really want to Save?",
	    function(result){ 
		if(result)
		{
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				item:$("#gen_item_edit").val(),
				quan:$("#gen_quantity").val(),
				mrp:$("#gen_mrp").val(),
				gst:$("#gen_gst").val(),
				tot:$("#gen_tot_amount").val(),
				date:$("#gen_date").val(),
				time:$("#gen_time").val(),
				val:val,
				type_id:type_id,
				user:$("#user").text(),
				type:43
			},
			function(data,status)
			{
				consumable_details($("#consm_sub_tab").val());	
			})
		}
		})
	}
	function load_consm_quan(val)
	{
		var quan=$(val).val();
		var mrp=$("#gen_mrp").val();
		var gst=$("#gen_gst").val();
		
		var tot_1=quan*mrp;
		var tot_2=tot_1*gst/100;
		var tot_3=tot_1+tot_2;
		
		$("#gen_tot_amount").val(tot_3);
	}
	
	function load_consm_mrp(val)
	{
		var mrp=$(val).val();
		var quan=$("#gen_quantity").val();
		var gst=$("#gen_gst").val();
		
		var tot_1=quan*mrp;
		var tot_2=tot_1*gst/100;
		var tot_3=tot_1+tot_2;
		
		$("#gen_tot_amount").val(tot_3);
	}
	
	function load_consm_gst(val)
	{
		var gst=$(val).val();
		var quan=$("#gen_quantity").val();
		var mrp=$("#gen_mrp").val();
		
		var tot_1=quan*mrp;
		var tot_2=tot_1*gst/100;
		var tot_3=tot_1+tot_2;
		
		$("#gen_tot_amount").val(tot_3);
	}
	
	function load_services()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:110,
		},
		function(data,status)
		{
			$("#cl110").html(data).css({'height':'450px'});
			$("html,body").animate({scrollTop: '200px'},800);
			$("#group").select2({ theme: "classic" });
		})	
		
	}
	function load_service_list(val)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			serv:$(val).val(),
			type:109,
		},
		function(data,status)
		{
			$("#serv_det").html("");
			$("#serv_master_list").html(data);
			$("#services").select2({ theme: "classic" });
		})
	}
	function load_serv_det(val,typ)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			serv:$(val).val(),
			group_id:$("#group").val(),
			typ:typ,
			type:111
		},
		function(data,status)
		{
			$("#serv_det").html(data);
			load_datepicker_service('1');
			$("#consultantdoctorid").select2({ theme: "classic" });
		})
	}
	
	function save_services(val)
	{
		$(val).prop("disabled",true);
		if($("#consultantdoctorid").val()=='0')
		{
			bootbox.dialog({ message: "<b>Select doctor</b>"});
			setTimeout(function(){
				bootbox.hideAll();
				$(val).prop("disabled",false);
			},1000);
			return true;
		}
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			user:$("#user").text().trim(),
			group:$("#group").val(),
			serv:$("#services").val(),
			serv_text:$("#serv_text").val(),
			days:$("#days").val(),
			rate:$("#rate").val(),
			ser_entry_date:$("#ser_entry_date").val(),
			ser_quantity:$("#ser_quantity").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
			type:112,
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<b>Added</b>"});
			setTimeout(function()
			{
				bootbox.hideAll();
				$(val).prop("disabled",false);
				serv_list();
				out_amount('Service Add');
			}, 1000);
		})
	}
	
	function serv_list()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:113,
		},
		function(data,status)
		{
			$("#serv_list").html(data);
		})	
	}
	
	function load_edit(val)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			user:$("#user").text().trim(),
			val:val,
			type:114,
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'50%',"width":"550px","height":"450px",'margin':'auto'},"slow");
			$(".modal-body").css({'max-height':'450px'});
			$("#mod").click();
		})
	}
	
	
	function ot_load_edit(val)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			val:val,
			type:701,
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#myModal1").animate({'top':'5%','left':'50%',"width":"550px","height":"450px",'margin':'auto'},"slow");
			$(".modal-body").css({'max-height':'450px'});
			$("#mod").click();
		})
	}
	
	function service_edit(typ)
	{
		if(typ==1)
		{
			var msg="Do you really want to Update?";
		}
		else if(typ==2)
		{
			var msg="Do you really want to Delete?";
		}
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: msg,
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/ipd_dash_pat_ajax.php",
						{
							slno:$("#slno").val(),
							serv:$("#serv_edit").val(),
							serv_text:$("#edit_text").val(),
							consultantdoctorid:$("#consultantdoctorid_edit").val(),
							days:$("#edit_days").val(),
							ser_quantity:$("#ser_quantity_edit").val(),
							rate:$("#rate_edit").val(),
							time:$("#edit_time").val(),
							date:$("#edit_date").val(),
							typ:typ,
							type:115,
							user:$("#user").text().trim(),
						},
						function(data,status)
						{
							$('#mod').click();
							serv_list();
							out_amount('Update Remove');
							load_services();
						})
					}
				}
			}
		});
	}
	
	function pat_discount(val)
	{
		var disc=parseInt(($("#pat_disc").val() ? $("#pat_disc").val() :'0'));
		var now_balance=parseInt(($("#now_balance_backend").val() ? $("#now_balance_backend").val() :'0'));
		
		var pat_total=parseInt($("#pat_total").text());
		var pat_advance=parseInt($("#pat_advance").text());
		
		var now_balance=parseInt(pat_total-pat_advance);
		var rest_amout=parseInt(pat_total-pat_advance-disc);
		if(!rest_amout)
		{
			rest_amout=0;
		}
		if(!now_balance)
		{
			now_balance=0;
		}
		
		if(disc>0)
		{
			$("#discount_tr").fadeIn();
		}else
		{
			$("#discount_tr").fadeOut();
		}
		
		if(disc>now_balance)
		{
			$("#pat_disc").css({"border-color":"red"}).focus();
			return false;
		}else
		{
			$("#pat_disc").css({"border-color":"#000"});
		}
		
		var p_mode=$("#p_mode").val();
		if(p_mode=="Credit")
		{
			$("#pay_advance").prop("disabled", true);
			$("#pat_balance").text(rest_amout);
			$("#pay_advance").val('0');
		}
		else
		{
			$("#pay_advance").prop("disabled", false);
			$("#pat_balance").text('0');
			$("#pay_advance").val(rest_amout);
		}
		
		var refund=(-1)*rest_amout;
			
		if(refund>0)
		{
			$("#pay_advance").val('0');
			$("#pat_refund_amt").text(refund);
		}else
		{
			$("#pat_refund_amt").text('0');
		}
		
		var a=$(val).val();
		var n=a.length;
		var numex=/^[0-9]+$/;
		if(a[n-1].match(numex))
		{
			
		}
		else
		{
			a=a.slice(0,n-1);
			document.getElementById("pat_disc").value=a;
		}
		
	}
	function pat_discountbd(val)
	{
		var tot=parseInt($("#pat_totalbd").text()); //--21700
		var adv=parseInt(($("#pat_advancebd").text() ? $("#pat_advancebd").text() :'0'));
		var pay=0;
		var disc=parseInt(($(val).val()? $(val).val():'0') );
		
		//var tot_paid=adv+pay; //---10000
		var bal=tot-adv-pay; //----21700-10000=11000-pay
		
		var n_bl=parseInt(pay+disc); //------10000+1000=11000
		
		var n_bl1=bal-n_bl; //----11700-11000=700
		
		$("#pat_balancebd").text('0');
		$("#pay_advancebd").val(n_bl1);
		
	}
	
	function pat_advancebd(val)
	{
		$("#pay_advancebd").css({'border-color': 'rgba(82,168,236,0.8)'});
		
		var tot=parseInt($("#pat_totalbd").text());
		var adv=parseInt($("#pat_advancebd").text());
		var discnt=parseInt($("#pat_discbd").val());
		if(!discnt)
		{
			discnt=0;
		}
		var pay=parseInt($("#pay_advancebd").val());
		if(!pay)
		{
			pay=0;
		}
		var bal=tot-adv-discnt;
		
		var n_bl=bal-pay;
		
		$("#pat_balancebd").text(n_bl);
	}
	
	function pat_advance(val)
	{
		$("#pay_advance").css({"border-color":""});
		
		var tot=parseInt($("#pat_total").text());
		var adv=parseInt($("#pat_advance").text());
		var pat_balance=parseInt($("#pat_balance").text());
		var disc=parseInt($("#pat_disc").val());
		//var disc=parseInt(($("#pay_disc").val() ? $("#pay_disc").val():'0'));
		//var pay=parseInt(($(val).val()? $(val).val():'0') );
		var pay=parseInt($("#pay_advance").val());
		
		var now_balance=parseInt($("#now_balance_backend").val());
		
		if(!pay)
		{
			pay=0;
		}
		if(!disc)
		{
			disc=0;
		}
		if(!pat_balance)
		{
			pat_balance=0;
		}
		
		var bal=tot-adv-disc-pay;
		
		if(pay==0 && bal>0)
		{
			$("#p_mode").val("Credit").prop("disabled", true);
		}
		else
		{
			$("#p_mode").val("Cash").prop("disabled", false);
		}
		if(bal>0)
		{
			$("#balance_tr").show();
		}
		else
		{
			$("#balance_tr").hide();
		}
		
		if(bal<0)
		{
			$("#pay_advance").css({"border-color":"red"});
		}
		
		$("#pat_balance").text(bal);
	}
	
	//----------------------------------------//
	///------------------------------------------------------//
	function check_bill_print()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:102,
		},
		function(data,status)
		{
			var u=$("#uhid").val();
			var i=$("#ipd").val();
			if(data==0)
			{
				print_ipd_receipt(u,i);
			}
			else
			{
				print_receipt(u,i);
			}
			//$("#results").html(data);
		})
	}
	function save_reg_fees()
	{
		if($("#r_tot").val()=="")
		{
			$("#r_tot").focus();
		}
		else if($("#r_pay").val()=="")
		{
			$("#r_pay").focus();
		}
		else if($("#r_pmode").val()=="0")
		{
			$("#r_pmode").focus();
		}
		else
		{
			$.post("pages/ipd_dash_pat_ajax.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				r_tot:$("#r_tot").val(),
				r_disc:$("#r_disc").val(),
				r_pay:$("#r_pay").val(),
				r_pmode:$("#r_pmode").val(),
				usr:$("#user").text().trim(),
				type:101,
			},
			function(data,status)
			{
				$("#mod").click();
			})
				print_receipt($("#uhid").val(),$("#ipd").val());
		}
	}
	function print_receipt(uhid,ipd)
	{
		var uhid=btoa(uhid);
		var ipd=btoa(ipd);
		var url="pages/print_ipd_receipt.php?uhid="+uhid+"&ipd="+ipd;
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
	}
	function print_ipd_receipt(uhid,ipd)
	{
		$("#mod").click();
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:100,
		},
		function(data,status)
		{
			$("#results").html(data);
		})
		//var url="pages/print_ipd_receipt.php?uhid="+uhid+"&ipd="+ipd;
		//window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
	}
///---------------------------------------------------------------------------------------------//

	function load_payment_div(val)
	{
		$(".payment_class").slideUp(200);
		$("#"+val+"").slideDown(200);
		pay_mode_change_adv();
		pay_mode_change_fn_bal();
	}
	function discharge_balance()
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<b>Do you really want to discharge this patient ?</b>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Discharge',
					className: "btn btn-danger",
					callback: function() {
						if(parseInt($("#pat_balancebd").text())<0)
						{
							$("#pay_advancebd").css({'border-color': '#F00'}).focus();
							$("#pay_advancebd").focus();
							//return false;
						}else
						{
							bootbox.dialog({ message: "<span id='discharge_text'><b>Discharging</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> </span>",closeButton: false});
							$.post("pages/ipd_dash_pat_ajax.php",
							{
								uhid:$("#uhid").val(),
								ipd:$("#ipd").val(),
								pat_totalbd:$("#pat_totalbd").text(),
								pat_discbd:$("#pat_discbd").val(),
								pay_advancebd:$("#pay_advancebd").val(),
								pat_balancebd:$("#pat_balancebd").text(),
								p_modebd:$("#p_modebd").val(),
								user:$("#user").text(),
								type:6011,
							},
							function(data,status)
							{
								//$("#discharge_text").html("<b>Discharged</b>");
													
								setTimeout(function()
								{
									//bootbox.hideAll();
									final_dicharge();
								}, 2000);
							})
						}
					}
				}
			}
		});
	}
	function discharge_pat()
	{
		bootbox.confirm("Do you really want to Discharge this patient ?",
	    function(result){
			
			if(result)
			{
				bootbox.dialog({ message: "<span id='discharge_text'>Discharging</span>"});
				final_dicharge();
			}
		})
	}
	function final_dicharge()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			user:$("#user").text(),
			type:116,
		},
		function(data,status)
		{
			$("#discharge_text").html("<b>Discharged</b>");
			
			setTimeout(function()
			{
				bootbox.hideAll();
				load_payment();
				out_amount('Final Payment');
			}, 1000);
		})
	}
	
	function cancel_discharge_pat()
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to cancel discharged of this patient?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> OK',
					className: "btn btn-danger",
					callback: function() {
						bootbox.dialog({ message: "<b>Canceling</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/>"});
						$.post("pages/ipd_dash_pat_ajax.php",
						{
							uhid:$("#uhid").val(),
							ipd:$("#ipd").val(),
							user:$("#user").text(),
							type:117,
						},
						function(data,status)
						{
							if(data=='1')
							{
								setTimeout(function()
								{
									bootbox.hideAll();
									bootbox.dialog({ message: "<b>Already Canceled</b>"});
								}, 2000);
								setTimeout(function()
								{
									window.location.reload(true);
								}, 4000);
							}else if(data=='2')
							{
								setTimeout(function()
								{
									bootbox.hideAll();
									bootbox.dialog({ message: "<b>Canceled</b>"});
								}, 2000);
								setTimeout(function()
								{
									bootbox.hideAll();
									load_payment();
								}, 4000);
							}else if(data=='3')
							{
								bootbox.hideAll();
								bootbox.dialog({ message: "<b>Someone is already admitted to this bed.</b>"});
								setTimeout(function()
								{
									bootbox.hideAll();
								}, 3000);
							}
						})
					}
				}
			}
		});
	}
	//-------------------------------discharge--summary------------//
	function edt_diag(sl)
	{
		$.post("pages/nursing_load_g.php",
		{
			sl:sl,
			type:"ipd_edit_summary_diagnosis",
		},
		function(data,status)
		{
			$("#med_upd").click();
			$("#upd_med_plan_det").html(data);
			//$("html,body").animate({scrollTop: '300px'},800);
		})
	}
	function delete_diag(sl)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/nursing_load_g.php",
						{
							sl:sl,
							type:"ipd_delete_summary_diagnosis",
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<b>Deleted</b>"});
							setTimeout(function(){
								bootbox.hideAll();
								disc_summary();
							 }, 1000);
						})
					}
				}
			}
		});
	}
	function upd_diag(sl)
	{
		$.post("pages/nursing_load_g.php",
		{
			sl:sl,
			diag:$("#diagnosis").val().trim(),
			ord:$("#ordr").val(),
			cert:$("#certainity").val(),
			usr:$("#user").text().trim(),
			type:"ipd_update_summary_diagnosis",
		},
		function(data,status)
		{
			disc_summary();
		})
	}
	function death_date()
	{
		if($("#dtype").val()=="105")
		{
			$("#death_det").slideDown(600);
		}
		else
		{
			$("#death_det").slideUp();
		}
	}
	function print_death_certificate()
	{
		var uhid=btoa($("#uhid").val());
		var ipd=btoa($("#ipd").val());
		var usr=btoa($("#user").text().trim());
		url="pages/death_certificate.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function disc_summary()
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"ipd_disc_summary",
		},
		function(data,status)
		{
			$("#cl22").html(data);
			$("html,body").animate({scrollTop: '300px'},800);
			$("#next_visit").datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: '0',
			});
			$("#death_date").datepicker({
				dateFormat: 'yy-mm-dd',
				maxDate: '0',
			});
			$("#death_time").timepicker({minutes: {starts: 0,interval: 05,showSecond: true,showMillisec: true,}});
		})
	}
	function add_row(r)
	{
		if(r==1)
		{
			var rr=$('#hist_table tbody tr.cc').length;
			var i=1;
			var d="";
			for(i=1;i<=30;i++)
			{
				d+="<option value='"+i+"'>"+i+"</option>";
			}
			var s='<option value="Minutes">Minutes</option><option value="Hours">Hours</option><option value="Days">Days</option><option value="Week">Week</option><option value="Month">Month</option><option value="Year">Year</option>';
			
			$.post("pages/global_load_g.php"	,
			{
				no:rr,
				type:"complain_templates_list",
			},
			function(data,status)
			{
				$("#hh").closest("tr").before('<tr class="cc"><th>Chief Complaints</th><td><input type="text" list="browsrs'+rr+'" id="chief'+rr+'" class="" onkeyup="sel_chief('+rr+',event)" /><span id="com'+rr+'"></span></td><td><b>for</b> <select id="cc'+rr+'" class="span2" onkeyup="sel_chief('+rr+',event)"><option value="0">--Select--</option>'+d+'</select> <select id="tim'+rr+'" class="span2" onkeyup="sel_chief('+rr+',event)"><option value="0">--Select--</option>'+s+'</select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
				$("#com"+rr).html(data);
				$("#chief"+rr).focus();
			})
		}
		if(r==2)
		{
			var jj=$('#diag_table tbody tr.diag').length;
				
			$.post("pages/global_load_g.php"	,
			{
				no:jj,
				type:"diagnosis_templates_list",
			},
			function(data,status)
			{
				$("#addiagnosis").closest("tr").before('<tr id="diag'+jj+'"><td><input list="brows'+jj+'" type="text" name="diagnosis'+jj+'" class="span3" onkeyup="diagtab('+jj+',event)" id="diagnosis'+jj+'" /><span id="diagnos'+jj+'"></span></td><td><select id="order'+jj+'" onkeyup="diagtab('+jj+',event)" class="span2"><option value="0">--Select--</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td><td><select id="cert'+jj+'" onkeyup="diagtab('+jj+',event)" class="span2"><option value="0">--Select--</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
				$("#diagnos"+jj).html(data);
				$("#diagnosis"+jj).focus();
			})
		}
	}
	function save_comp()
	{
		var rr=$('#hist_table tbody tr.cc').length;
		var comp="";
		for(var j=0;j<rr;j++)
		{
			if($(".cc:eq("+j+")").find('td:first input:first').val() && $(".cc:eq("+j+")").find('td:eq(1) select:first').val()!="0" && $(".cc:eq("+j+")").find('td:eq(1) select:last').val()!="0")
			comp+=$(".cc:eq("+j+")").find('td:first input:first').val()+"@"+$(".cc:eq("+j+")").find('td:eq(1) select:first').val()+"@"+$(".cc:eq("+j+")").find('td:eq(1) select:last').val()+"#g#";
		}
		if(comp!="")
		{
			$.post("pages/nursing_load_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				usr:$("#user").text().trim(),
				comp:comp,
				type:"save_comp",
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<b>Saved</b>"});
				setTimeout(function(){
					bootbox.hideAll();
					disc_summary();
				 }, 1000);
				//$("#post_medi").html(data);
			})
		}
	}
	function save_exam()
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			history:$("#history").val().trim(),
			exam:$("#exam").val().trim(),
			significant_finding:$("#significant_finding").val().trim(),
			investigation_result:$("#investigation_result").val().trim(),
			usr:$("#user").text().trim(),
			type:"save_exam",
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<b>Saved</b>"});
			setTimeout(function(){
				bootbox.hideAll();
				disc_summary();
			 }, 1000);
		})
	}
	function save_diagno()
	{
		var rr=$('#diag_table tbody tr.diag').length;
		var diagno="";
		for(var j=0;j<=rr;j++)
		{
			if($(".diag:eq("+j+")").find('td:first input:first').val() && $(".diag:eq("+j+")").find('td:eq(1) select:first').val()!="0" && $(".diag:eq("+j+")").find('td:eq(2) select:first').val()!="0")
			diagno+=$(".diag:eq("+j+")").find('td:first input:first').val()+"@"+$(".diag:eq("+j+")").find('td:eq(1) select:first').val()+"@"+$(".diag:eq("+j+")").find('td:eq(2) select:first').val()+"#g#";
		}
		if(diagno!="")
		{
			$.post("pages/nursing_load_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				usr:$("#user").text().trim(),
				diagno:diagno,
				type:"save_diagno",
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<b>Saved</b>"});
				setTimeout(function(){
					bootbox.hideAll();
					disc_summary();
				 }, 1000);
				//$("#post_medi").html(data);
			})
		}
	}
	function insert_disc_summ()
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			course:$("#course").val().trim(),
			final_diag:$("#final_diag").val().trim(),
			procedure_with_date:$("#procedure_with_date").val().trim(),
			v_bp:$("#v_bp").val().trim(),
			v_pulse:$("#v_pulse").val().trim(),
			v_temp:$("#v_temp").val().trim(),
			v_weight:$("#v_weight").val().trim(),
			foll:$("#foll").val().trim(),
			report_hospital:$("#report_hospital").val().trim(),
			next_visit:$("#next_visit").val().trim(),
			usr:$("#user").text().trim(),
			type:"insert_disc_summ",
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<b>Saved</b>"});
			setTimeout(function(){
				bootbox.hideAll();
				disc_summary();
			 }, 1000);
		})
	}
	function save_disc_medi()
	{
		var rr=$('#medi_sel_list tbody tr.disc_medi').length;
		var all="";
		for(var j=0;j<=rr;j++)
		{
			var med=$(".disc_medi:eq("+j+")").find('td:eq(1) input:first').val();
			var dos=$(".disc_medi:eq("+j+")").find('td:eq(2) input:first').val();
			var freq=$(".disc_medi:eq("+j+")").find('td:eq(3) input:first').val();
			var dur=$(".disc_medi:eq("+j+")").find('td:eq(4) input:first').val();
			var unit=$(".disc_medi:eq("+j+")").find('td:eq(4) input:last').val();
			var totl=$(".disc_medi:eq("+j+")").find('td:eq(5) input:first').val();
			var inst=$(".disc_medi:eq("+j+")").find('td:eq(6) input:first').val();
			var st_date=$(".disc_medi:eq("+j+")").find('td:eq(7) input:first').val();
			if(med && dos && freq && dur && unit && totl && inst && st_date)
			all+=med+"@@"+dos+"@@"+freq+"@@"+dur+"@@"+unit+"@@"+totl+"@@"+inst+"@@"+st_date+"@@"+"#g#";
		}
		if(all!="")
		{
			$.post("pages/nursing_load_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				usr:$("#user").text().trim(),
				all:all,
				type:"save_disc_medi",
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<b>Saved</b>"});
				setTimeout(function(){
					bootbox.hideAll();
					disc_summary();
				 }, 1000);
				//$("#post_medi").html(data);
			})
		}
	}
	function save_dis_type()
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			diagnosed:$("#diagnosed").val(),
			dtype:$("#dtype").val(),
			death_date:$("#death_date").val(),
			death_time:$("#death_time").val(),
			death_cause:$("#death_cause").val().trim(),
			usr:$("#user").text().trim(),
			type:"save_dis_type",
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<b>Saved</b>"});
			setTimeout(function(){
				bootbox.hideAll();
				disc_summary();
				nicu_disc_summary
			 }, 1000);
		})
	}
	function post_drugs()
	{
		$("#dl").click();
		$.post("pages/nursing_load_g.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:"ipd_add_medicine_post",
		},
		function(data,status)
		{
			$("#post_medi").html(data);
			setTimeout(function(){$("#mediname").focus()},900);
		})
	}
	function print_disc_summary()
	{
		var uhid=btoa($("#uhid").val());
		var ipd=btoa($("#ipd").val());
		var usr=btoa($("#user").text().trim());
		url="pages/ipd_discharge_summary.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function reason_save_click()
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			admit_reason:$("#admit_reason").val().trim(),
			user:$("#user").text().trim(),
			type:"admit_reason_save",
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<b>"+data+"</b>"});
			setTimeout(function(){
				bootbox.hideAll();
			 }, 1000);
		})
	}
	function del_med(id)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to remove this medicine?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Ok',
					className: "btn btn-danger",
					callback: function()
					{
						$.post("pages/global_delete_g.php",
						{
							id:id,
							type:"ipd_pat_post_medi_del",
						},
						function(data,status)
						{
							disc_summary();
						})
					}
				}
			}
		});
	}
	function change_med(id)
	{
		//alert(id);
		$.post("pages/global_load_g.php",
		{
			id:id,
			type:"ipd_pat_post_medi_upd",
		},
		function(data,status)
		{
			$("#med_upd").click();
			$("#upd_med_plan_det").html(data);
		})
	}
	function update_plan(id)
	{
		$.post("pages/global_insert_data_g.php",
		{
			medi:$("#mediname").val(),
			freq:$("#freq").val(),
			st_date:$("#st_date").val(),
			dur:$("#dur").val(),
			unit_day:$("#unit_day").val(),
			total:$("#totl").val(),
			inst:$("#inst").val(),
			dos:$("#dos").val(),
			id:id,
			usr:$('#user').text().trim(),
			type:"pat_ipd_drug_update"
		},
		function(data,status)
		{
			disc_summary();
		})
	}
	function cancel_casual()
	{
		//alert(uhid+' '+opd_id)
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to cancel this patient ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-danger",
					callback: function() {
						cancel_note();
					}
				}
			}
		});
	}
	function cancel_note()
	{
		bootbox.dialog({
			message: "Reason:<input type='text' id='note' autofocus />",
			title: "Patient Cancel",
			buttons: {
				main: {
				  label: "Save",
				  className: "btn-primary",
				  callback: function() {
					if($('#note').val()!='')
					{
						$.post("pages/pat_cancel.php",
						{
							
							type:"ipd_pat_cancel",
							uhid:$("#uhid").val(),
							ipd:$("#ipd").val(),
							reason:$('#note').val(),
							user:$('#user').text().trim(),
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<b>Cancelled. Redirecting to Dashboard</b> "});
							setTimeout(function(){
								 window.location="index.php";
							},2000);
						})
					}else
					{
						bootbox.alert("<b>Reason can't be blank</b>");
						setTimeout(function(){
							 bootbox.hideAll();
							 cancel_note();
						},2000);
					}
					
				  }
				}
			}
		});
	}
/////--------------------------------------------------------//
	function add_dose(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13 && $("#dos").val().trim()!="")
		{
			//$("#sav_medi").focus();
			$("#ph_quantity").focus();
		}
	}
	function ph_quantity(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$("#sav_medi").focus();
		}
	}
	function add_item_temp(id,itm_name,dose)
	{
		//var net=100;
		$("#item_tr").show();
		var tr_len=$('#mytable tr').length;
		if(tr_len==0)
		{
			var test_add="<table class='table table-condensed table-bordered' style='style:none' id='mytable'>";
			test_add+="<tr><th style='background-color:#cccccc'>Sl No</th><th style='background-color:#cccccc'>Drug Name<span id='msgg' style='left:45%;position:absolute;display:none;color:#bb0000;font-size:18px;'></span></th><th style='background-color:#cccccc'>Dosage / Instruction</th><th style='background-color:#cccccc;width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr'><td>1</td><td>"+itm_name+"<input type='hidden' value='"+id+"' class='test_id'/></td><td>"+dose+"<input type='hidden' value='"+dose+"' class='all_rate' /></td><td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_btn()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td></tr>";
			test_add+="</table>";
			
			$("#temp_item").html(test_add);
			tr_len++;
		}
		else
		{
			
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id");
			
			for(var i=0;i<test_l.length;i++)
			{
				if(test_l[i].value==id)
				{
					t_ch=1;
				}
			}
			if(t_ch)
			{

				$("#temp_item").css({'opacity':'0.5'});
				$("#msgg").text("Already Selected");
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#temp_item").css({'opacity':'1.0'});
				})},600);
			}			
			else
			{
			var tr=document.createElement("tr");
			tr.setAttribute("class","all_tr");
			var td=document.createElement("td");
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var tbody=document.createElement("tbody");
			
			td.innerHTML=tr_len;
			td1.innerHTML=itm_name+"<input type='hidden' value='"+id+"' class='test_id'/>";
			td2.innerHTML=dose+"<input type='hidden' value='"+dose+"' />";
			td3.innerHTML="<span onclick='$(this).parent().parent().remove();set_btn()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span>";
			td3.setAttribute("style","text-align:center;");
			tr.appendChild(td);
			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tbody.appendChild(tr);		
			document.getElementById("mytable").appendChild(tbody);
			}
		}
		$("#sav_medi").attr("disabled",false);
		setTimeout(function(){$("#medi").val("").focus();},300);
	}
	function save_all_medi()
	{
		if(($("#medi").css("display",))=="inline-block" && $("#medi").val().trim()=="")
		{
			$("#medi").focus();
			return true;
		}
		else if(($("#new_medi").css("display",))=="inline-block" && $("#new_medi").val().trim()=="")
		{
			$("#new_medi").focus();
			return true;
		}
		else if($("#dos").val().trim()=="")
		{
			$("#dos").focus();
		}
		else
		{
			$("#sav_medi").attr("disabled",true);
			$.post("pages/global_insert_data_g.php",
			{				
				type:"insert_medicine_final",
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				itm:$("#medid").val(),
				new_medi:$("#new_medi").val(),
				dos:$("#dos").val(),
				ph_quantity:$("#ph_quantity").val(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				//alert(data);
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					$("#sav_medi").attr("disabled",false);
					bootbox.hideAll();
					 //window.location="index.php";
				},1000);
				disc_summary();
				nicu_disc_summary();
			})
		}
	}
	function new_medi()
	{
		$("#medi").hide();
		$("#new_btn").hide();
		$("#can_btn").show();
		$("#new_medi").show().val('').focus();
		$("#medid").val('');
	}
	function can_medi()
	{
		$("#new_medi").hide();
		$("#can_btn").hide();
		$("#new_btn").show();
		$("#medi").show().val('').focus();
		$("#medid").val('');
	}
	function tab(id,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13 && $("#new_medi").val().trim()!="")
		{
			$("#dos").focus();
		}
	}
	function edit_ipd_paymentmode(slno)
	{
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			slno:slno,
			type:"payment_mode_change",
		},
		function(data,status)
		{
			$("#edit_payment_mode_btn").click();
			$("#edit_payment_mode_data").html(data);
		})
	}
	function update_payment_mode(slno)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to change ? </h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-danger",
					callback: function()
					{
						$.post("pages/ipd_dash_pat_ajax.php",
						{
							slno:slno,
							payment_mode:$("#edit_payment_mode").val(),
							user:$('#user').text().trim(),
							type:"payment_mode_update",
						},
						function(data,status)
						{
							$("#edit_payment_mode_btn").click();
							bootbox.dialog({ message: "<h5>Updated</h5>"});
							setTimeout(function()
							{
								bootbox.hideAll();
								load_payment();
							},1000);
						})
					}
				}
			}
		});
	}
	function p_mode_final_change()
	{
		var disc=parseInt(($("#pat_disc").val() ? $("#pat_disc").val() :'0'));
		var now_balance=parseInt(($("#now_balance_backend").val() ? $("#now_balance_backend").val() :'0'));
		
		var pat_total=parseInt($("#pat_total").text());
		var pat_advance=parseInt($("#pat_advance").text());
		
		var rest_amout=parseInt(pat_total-pat_advance-disc);
		if(!rest_amout)
		{
			rest_amout=0;
		}
		
		var p_mode=$("#p_mode").val();
		if(p_mode=="Credit")
		{
			$("#pay_advance").prop("disabled", true);
			$("#pat_balance").text(rest_amout);
			$("#pay_advance").val('0');
			$("#balance_tr").show();
		}
		else
		{
			$("#pay_advance").prop("disabled", false);
			$("#pat_balance").text('0');
			$("#pay_advance").val(rest_amout);
			$("#balance_tr").hide();
		}
	}
	///////////// NICU
	function nicu_disc_summary()
	{
		$.post("pages/nicu_discharge_summary_data.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
			type:"nicu_disc_summary",
		},
		function(data,status)
		{
			$("#cl222").html(data);
			$("html,body").animate({scrollTop: '300px'},800);
			//~ $(".datepicker").datepicker({
				//~ dateFormat: 'yy-mm-dd',
				//~ minDate: '0',
			//~ });
			
			$("#death_date").datepicker({
				dateFormat: 'yy-mm-dd',
				maxDate: '0',
			});
			$("#death_time").timepicker({minutes: {starts: 0,interval: 05,showSecond: true,showMillisec: true,}});
		})
	}
	function save_nicu_summary()
	{
		$.post("pages/nicu_discharge_summary_data.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			gestations:$("#gestations").val().trim(),
			apgar_score_1m:$("#apgar_score_1m").val().trim(),
			apgar_score_5m:$("#apgar_score_5m").val().trim(),
			apgar_score_10m:$("#apgar_score_10m").val().trim(),
			abo_mother:$("#abo_mother").val().trim(),
			abo_baby:$("#abo_baby").val().trim(),
			immunization_date_bcg:$("#immunization_date_bcg").val().trim(),
			opv_birth_dose:$("#opv_birth_dose").val().trim(),
			hepatitis_b:$("#hepatitis_b").val().trim(),
			dicharge_weight:$("#dicharge_weight").val().trim(),
			treatment_in_hospital:$("#treatment_in_hospital").val().trim(),
			case_summary:$("#case_summary").val().trim(),
			course_in_hospital:$("#course_in_hospital").val().trim(),
			user:$("#user").text().trim(),
			type:"save_nicu_disc_summary",
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<b>Saved</b>"});
			setTimeout(function(){
				bootbox.hideAll();
				nicu_disc_summary();
			 }, 1000);
		})
	}
	function print_nicu_disc_summary()
	{
		var uhid=btoa($("#uhid").val());
		var ipd=btoa($("#ipd").val());
		var usr=btoa($("#user").text().trim());
		url="pages/ipd_discharge_summary_nicu.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function load_datepicker_service(val)
	{
		$(".datepicker").attr("readonly", true);
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			type:"datepicker_min_max"
		},
		function(data,status)
		{
			var res=data.split("@@");
			$(".datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: res[0],
				maxDate: res[1],
			});
		})
	}
	function pay_mode_change_adv()
	{
		$.post("pages/payment_load_data.php",
		{
			type:"payment_mode_change",
			val:$("#p_mode_ad").val(),
		},
		function(data,status)
		{
			var res=data.split("@#@");
			
			if(res[1]==2)
			{
				$("#adv_reference_no_tr").hide();
			}
			else
			{
				if(res[0]==0)
				{
					$("#adv_reference_no_tr").show();
				}
				else
				{
					$("#adv_reference_no_tr").hide();
				}
			}
		});
	}
	function pay_mode_change_fn_bal()
	{
		$.post("pages/payment_load_data.php",
		{
			type:"payment_mode_change",
			val:$("#p_mode").val(),
		},
		function(data,status)
		{
			var res=data.split("@#@");
			
			if(res[1]==2)
			{
				$("#fn_bal_reference_no_tr").hide();
			}
			else
			{
				if(res[0]==0)
				{
					$("#fn_bal_reference_no_tr").show();
				}
				else
				{
					$("#fn_bal_reference_no_tr").hide();
				}
			}
			p_mode_final_change();
		});
	}
	
	function payment_mode_change_trans(pid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to change payment mode ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
						bootbox.hideAll();
						load_payment();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						payment_mode_change_trans_check(pid);
					}
				}
			}
		});
	}
	
	function payment_mode_change_trans_check(pid)
	{
		if($("#ipd_payment_mode_trans"+pid).val()=="Credit")
		{
			load_payment();
			bootbox.alert("Failed, try again later.");
		}
		else if($("#ipd_payment_mode_trans"+pid).val()=="Cash")
		{
			payment_mode_change_trans_ok(pid,"");
		}
		else
		{
			bootbox.dialog({
				message: "<input type='text' class='capital' id='cheque_ref_no_trans' autofocus />",
				title: "Cheque/Reference no",
				buttons: {
					cancel: {
						label: '<i class="icon-remove"></i> Cancel',
						className: "btn btn-inverse",
						callback: function() {
							bootbox.hideAll();
							load_payment();
						}
					},
					main: {
						label: '<i class="icon-ok"></i> Change',
						className: "btn btn-danger",
						callback: function() {
							
							payment_mode_change_trans_ok(pid,$("#cheque_ref_no_trans").val());
							
						}
					}
				}
			});
		}
	}
	function payment_mode_change_trans_ok(pid,cheque_ref_no_trans)
	{
		$("#loader").show();
		$.post("pages/ipd_dash_pat_ajax.php",
		{
			type:"payment_mode_change_each",
			patient_id:$("#uhid").val(),
			opd_id:$("#ipd").val(),
			pay_id:pid,
			payment_mode:$("#ipd_payment_mode_trans"+pid).val(),
			cheque_ref_no:cheque_ref_no_trans,
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			bootbox.dialog({ message: "<h5>"+data+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
				load_payment();
			},2000);
		})
	}
	
	function delete_receipt(pid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						delete_receipt_ok(pid);
					}
				}
			}
		});
	}
	function delete_receipt_ok(pid)
	{
		bootbox.dialog({
			message: "Reason:<input type='text' id='del_reason' autofocus />",
			title: "Payment Delete",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				main: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						if($("#del_reason").val()!="")
						{
							$("#loader").show();
							$.post("pages/ipd_dash_pat_ajax.php",
							{
								type:"delete_payment_each",
								patient_id:$("#uhid").val(),
								opd_id:$("#ipd").val(),
								pay_id:pid,
								del_reason:$("#del_reason").val(),
								user:$("#user").text().trim(),
							},
							function(data,status)
							{
								//alert(data);
								$("#loader").hide();
								bootbox.dialog({ message: "<h5>"+data+"</h5> "});
								setTimeout(function(){
									bootbox.hideAll();
									load_payment();
									out_amount('Final Payment');
								},2000);
							})
						}
						else
						{
							bootbox.alert("Reason cannot blank");
						}
					}
				}
			}
		});
	}
	
	function print_transaction(pid)
	{
		var url="pages/print_transaction_receipt.php?v="+btoa(1);
		
		var uhid=$("#uhid").val();
		url=url+"&uhid="+btoa(uhid);
		
		var opd_id=$("#ipd").val();
		url=url+"&opdid="+btoa(opd_id);
		
		url=url+"&pid="+btoa(pid);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>

<script src="include/jquery.ui.timepicker.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->
<style>
	#myAlert, #medplan
	{
	    width: 80%;
		margin-left: -40%;
	}
	.btt,.btt:hover,.btt:focus, .clk, .clk:hover, .clk:focus
	{
		background:#708090;
		color:#ffffff;
	}
	.sub_tab_main
	{
		display:inline-block;
		min-width:500px;
	}
	.modal.fade.in {
		top: 1%;
	}
</style>
