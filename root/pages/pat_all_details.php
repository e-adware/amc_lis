<?php

$date=date('Y-m-d');

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$c_user=trim($_SESSION['emp_id']);
$uhid=base64_decode($_GET["uhid"]);
$opd_id=base64_decode($_GET["opd"]);
if(!$opd_id)
{
	$opd_id="0000";
}

$ncrypt_opd_id=base64_encode($opd_id);

$cat=base64_decode($_GET["cat"]);
$lab=base64_decode($_GET["lab"]);
$consult=base64_decode($_GET["consult"]);
$adv=base64_decode($_GET["adv"]);
$uhid=trim($uhid);

$catt=explode("@", $cat);
$reg_category=$catt[0];
$reg_dept=$catt[1];

$reg_header="";
if($reg_category=="1")
{
	$reg_header=": Pathology";
}
else
{
	$test_dept=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `test_department` WHERE `id`='$reg_dept' "));
	if($test_dept)
	{
		$reg_header=": ".$test_dept["name"];
	}
}

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
//if($pat_info["blood_group"]==""){ $blood_group="N/A"; }else{ $blood_group=$pat_info["blood_group"]; }

if($opd_id!="0000")
{
	$pin_double_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_double_check` WHERE `patient_id`='$uhid' AND `old_opd_id`='$opd_id' "));
	if($pin_double_check)
	{
		$opd_id=$pin_double_check["opd_id"];
	}
}

$lab_acs="";
$consult_acs="";
if($lab)
{
	if($lab==1)
	{
		$lab_acs="Yes";
	}
	if($cat==1)
	{
		$paramm=base64_encode(82);
	}
	else
	{
		if($reg_dept==128)
		{
			$paramm=base64_encode(840);
		}
		if($reg_dept==40)
		{
			$paramm=base64_encode(841);
		}
		if($reg_dept==121)
		{
			$paramm=base64_encode(842);
		}
		if($reg_dept==131)
		{
			$paramm=base64_encode(843);
		}
		if($reg_dept==126)
		{
			$paramm=base64_encode(844);
		}
	}
	
	$check_opd_id_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_id' "));
	if($check_opd_id_num>1)
	{
		$check_pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		$date=$check_pat_reg["date"];
		
		$opd_idds=100;
		
		$date_str=explode("-", $date);
		$dis_year=$date_str[0];
		$dis_month=$date_str[1];
		$dis_year_sm=convert_date_only_sm_year($date);
		
		$c_m_y=$dis_year."-".$dis_month;
		
		$current_month=date("Y-m");
		if($c_m_y<$current_month)
		{
			$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
			$opd_id_num=$opd_id_qry["tot"];
			
			$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
			$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
			
			$pat_tot_num=$opd_id_num+$opd_id_cancel_num;
			
			if($pat_tot_num==0)
			{
				$opd_idd=$opd_idds+1;
			}else
			{
				$opd_idd=$opd_idds+$pat_tot_num+1;
			}
			$opd_id_new=$opd_idd."/".$dis_month.$dis_year_sm;
		}else
		{
			$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
			if(!$c_data)
			{
				mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
			}

			mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$uhid','2','$user','$date','$time') ");
			
			$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$uhid' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
			
			$last_slno=$last_slno["slno"];
			$opd_idd=$opd_idds+$last_slno;
			$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
		}
		
		mysqli_query($link, " UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `invest_payment_detail` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `patient_test_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `phlebo_sample` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `test_sample_result` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `testresults` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `testresults_rad` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `patient_test_summary` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `widalresult` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `test_sample_result` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `invest_payment_refund` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `invest_payment_refund_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `invest_payment_free` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$opd_id=$opd_id_new;
		
		echo '<script>window.location="processing.php?param=3&uhid="'.$uhid.'"&lab=1&opd="'.$opd_id.';</script>';
		
	}
}
if($consult)
{
	if($consult==1)
	{
		$consult_acs="Yes";
	}
	$paramm=base64_encode(81);
	
	$check_opd_id_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_id' "));
	if($check_opd_id_num>1)
	{
		$opd_idds=100;
		
		$date_str=explode("-", $date);
		$dis_year=$date_str[0];
		$dis_month=$date_str[1];
		$dis_year_sm=convert_date_only_sm_year($date);
		
		//~ $dis_month=date("m");
		//~ $dis_year=date("Y");
		//~ $dis_year_sm=date("y");
		
		$c_m_y=$dis_year."-".$dis_month;
		
		$current_month=date("Y-m");
		if($c_m_y<$current_month)
		{
			$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
			$opd_id_num=$opd_id_qry["tot"];
			
			$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
			$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
			
			$pat_tot_num=$opd_id_num+$opd_id_cancel_num;
			
			if($pat_tot_num==0)
			{
				$opd_idd=$opd_idds+1;
			}else
			{
				$opd_idd=$opd_idds+$pat_tot_num+1;
			}
			$opd_id_new=$opd_idd."/".$dis_month.$dis_year_sm;
		}else
		{
			$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
			if(!$c_data)
			{
				mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
			}

			mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$uhid','1','$user','$date','$time') ");
			
			$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$uhid' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
			
			$last_slno=$last_slno["slno"];
			$opd_idd=$opd_idds+$last_slno;
			$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
		}
		
		mysqli_query($link, " UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `consult_patient_payment_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `consult_payment_detail` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `appointment_book` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `pat_regd_fee` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `cross_consultation` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `discount_approve` SET `pin`='$opd_id_new' WHERE `patient_id`='$uhid' and `pin`='$opd_id' ");
		mysqli_query($link, " UPDATE `consult_payment_refund_details` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `invest_payment_refund` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " UPDATE `invest_payment_free` SET `opd_id`='$opd_id_new' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$opd_id=$opd_id_new;
		
		echo '<script>window.location="processing.php?param=3&uhid="'.$uhid.'"&consult=1&opd="'.$opd_id.';</script>';
		
	}
}
if($lab_acs=="Yes")
{
	$lab_active="active";
	echo "<input type='hidden' id='default_dashboard' value='lab'>";
	echo "<input type='hidden' id='opd_pin' value='$opd_id'>";	
}else if($consult_acs=="Yes")
{
	$opd_active="active";
	echo "<input type='hidden' id='default_dashboard' value='opd'>";
	echo "<input type='hidden' id='opd_pin' value='$opd_id'>";
}else
{
	$cashier_access=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cashier_access` WHERE `emp_id`='$c_user' "));
	if($cashier_access["opd_cashier"]==1)
	{
		$opd_active="active";
		echo "<input type='hidden' id='default_dashboard' value='opd'>";
	}else
	{
		$opd_active="";
		if($cashier_access["ipd_cashier"]==1)
		{
			$ipd_active="active";
			echo "<input type='hidden' id='default_dashboard' value='ipd'>";
		}else
		{
			$ipd_active="";
			if($cashier_access["lab_cashier"]==1)
			{
				$lab_active="active";
				echo "<input type='hidden' id='default_dashboard' value='lab'>";
			}else
			{
				$lab_active="";
				if($cashier_access["bloodbank_cashier"]==1)
				{
					$bbank_active="active";
					echo "<input type='hidden' id='default_dashboard' value='bbank'>";
				}else
				{
					$bbank_active="";
					if($cashier_access["casuality_cashier"]==1)
					{
						$casuality_active="active";
						echo "<input type='hidden' id='default_dashboard' value='casuality'>";
					}else
					{
						$casuality_active="";
					}
				}
			}
		}
	}
}
if($adv)
{
	echo "<input type='hidden' value='$adv' id='adv_book_p'>";
}

$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);

if($param_str==3)
{
	$param_str=2;
}

$str="";
if($_GET["uhid_str"])
{
	$str.="&uhid_str=$uhid_str";
	$refresh_str.="&uhid_str=$uhid_str";
}

if($_GET["pin_str"])
{
	$str.="&pin_str=$pin_str";
	$refresh_str.="&pin_str=$pin_str";
}

if($_GET["fdate_str"])
{
	$str.="&fdate_str=$fdate_str";
	$refresh_str.="&fdate_str=$fdate_str";
}

if($_GET["tdate_str"])
{
	$str.="&tdate_str=$tdate_str";
	$refresh_str.="&tdate_str=$tdate_str";
}

if($_GET["name_str"])
{
	$str.="&name_str=$name_str";
	$refresh_str.="&name_str=$name_str";
}

if($_GET["phone_str"])
{
	$str.="&phone_str=$phone_str";
	$refresh_str.="&phone_str=$phone_str";
}

if($_GET["param_str"])
{
	$str.="&param=$param_str";
}

if($_GET["pat_type_str"])
{
	$str.="&pat_type_str=$pat_type_str";
	$refresh_str.="&pat_type_str=$pat_type_str";
}

echo "<input type='hidden' id='refresh_back_id' value='$refresh_str'>";
echo "<input type='hidden' id='prev_para' value='$param_str'>";

echo "<input type='hidden' id='cat' value='$cat'>";

echo "<input type='hidden' id='sel_uhid' value='$uhid'>";
echo "<input type='hidden' id='sel_pin' value='$opd_id'>";

$paramm_reg=base64_decode($paramm);
echo "<input type='hidden' id='new_reg_btn_param' value='$paramm_reg'>";

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Patient Dashboard</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<!--<p class="p_header">Visit Information</p>-->
	<!--<span class="uhid_dis"><b>UHID</b>: <text style="font-size:18px;"><?php echo $uhid; ?></text></span>-->
	
	<span style="float:right;">
		<button class="btn btn-back" id="add" onclick="window.location='processing.php?v=0<?php echo $str; ?>'"><i class="icon-backward"></i> Back</button>
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
		<input type="button" class="btn btn-inverse" id="add" value="Barcode" onclick="print_barcode_recp('<?php echo $uhid; ?>','<?php echo $opd_id; ?>')" style="" />
	</span>
	
	<table class="table" style="background: snow">
		<tr>
			<th>UHID</th>
		<?php //if($pat_info["uhid"]){ echo "<th>OPD Serial</th>"; } ?>
			<th>Name</th>
			<th>Age</th>
			<th colspan="">Sex</th>
			<!--<th>Blood Group</th>-->
			<th colspan="2">Phone</th>
			<!--<th>Address</th>-->
			<!--<th></th>-->
		</tr>
		<tr>
			<!--<td><?php echo $pat_info["uhid"]; ?></td>-->
			<td><?php echo $uhid; ?></td>
			<?php //if($pat_info["uhid"]){ echo "<td>".$pat_info["uhid"]."</td>"; } ?>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
			<!--<td><?php echo $blood_group; ?></td>-->
			<td><?php echo $pat_info["phone"]; ?></td>
			<td>
				<!--<?php echo $pat_info["address"]; ?> -->
			<?php if($p_info['edit_info']==1){ ?>
				<span class="text-right">
					<a class="btn btn-edit btn-mini" title="Edit Patient Details" href="index.php?param=<?php echo $paramm; ?>&uhid=<?php echo $_GET["uhid"]?>&pin=<?php echo $ncrypt_opd_id; ?>&cat=<?php echo $_GET["cat"]?>" ><i class="icon-edit"></i></a>
				</span>
			<?php } ?>
			</td>
			<!--<td>
				<button class="btn btn-mini btn-info" onClick="print_receipt('pages/print_regd_receipt.php?uhid=<?php echo $uhid; ?>')">Registration Receipt</button>
			</td>-->
		</tr>
	</table>
	<div class="widget-box">
		<div class="widget-title">
			<ul class="nav nav-tabs">
				<?php if($cashier_access["opd_cashier"]==1){ ?>
				<li class="active" onClick="check_appointment('')"><a data-toggle="tab" href="#tab1">Consultant OPD</a></li>
				<?php } ?>
				<?php if($cashier_access["lab_cashier"]==1){ ?>
				<li class="" onClick="check_investigation('','')"><a data-toggle="tab" href="#tab2">Investigation</a></li>
				<?php } ?>
				<?php if($lab==1){ ?>
				<li class="" onClick="check_investigation('','','<?php echo $opd_id; ?>')"><a data-toggle="tab" href="#tab2">Investigation<?php echo $reg_header; ?></a></li>
				<?php } ?>
				<?php if($consult==1){ ?>
				<li class="active" onClick="check_appointment('','','<?php echo $opd_id; ?>')"><a data-toggle="tab" href="#tab1">Consultant OPD</a></li>
					<?php if($opd_id){ ?>
						<!--<li class="" onClick="check_investigation('','','<?php echo $opd_id; ?>')"><a data-toggle="tab" href="#tab2">Investigation</a></li>-->
					<?php } ?>
				<?php } ?>
				<?php if($cashier_access["ipd_cashier"]==1){ ?>
				<!--<li class="" onclick="check_ipd();check_saved_ipd()"><a data-toggle="tab" href="#tab3">IPD</a></li>-->
				<?php } ?>
				<?php if($cashier_access["bloodbank_cashier"]==1){ ?>
				<li class="" onClick="blood_bank()"><a data-toggle="tab" href="#tab4">Blood Bank</a></li>
				<?php } ?>
				<?php if($cashier_access["casuality_cashier"]==1){ ?>
				<li class="" onClick="casuality()"><a data-toggle="tab" href="#tab5">Casuality</a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="widget-content tab-content">
			<div id="tab1" class="tab-pane <?php echo $opd_active; ?>">
				<div id="load_all">
					
				</div>
				<div id="print_div">
					
				</div>
				<div id="edit_div">
					
				</div>
				<div id="adv_book_div">
					
				</div>
			</div>
			<div id="tab2" class="tab-pane <?php echo $lab_active; ?>">
				<div id="load_investigation">
					
				</div>
				<div id="out_test_form">
					
				</div>
			</div>
			<div id="tab3" class="tab-pane  <?php echo $ipd_active; ?>">
				<div id="ipd_saved">
					
				</div>
				<div id="ipd_sect">
				
				</div>
			</div>
			<div id="tab4" class="tab-pane  <?php echo $bloodbank_cashier; ?>">
				<h4>Blood Bank</h4>
			</div>
			<div id="tab5" class="tab-pane  <?php echo $casuality_cashier; ?>">
				<h4>Casuality</h4>
			</div>
		</div>
	</div>
	<div id="loader" style="display:none;"></div>
</div>
<div id="img" style="display:none;position:fixed; top: 0px; width: 100%; height: 100%; text-align: center; vertical-align: middle; background: rgba(255,255,255,0.7);">
	<div id="dialog_msg"></div>
</div>
<input type="hidden" id="chk_val" value="0"/>
<input type="hidden" id="chk_val2" value="0"/>
<input type="hidden" id="chk_val3" value="0"/>
<input type="hidden" id="con_doc_id" value="0"/>
<input type="hidden" id="con_doc_fee"/>
<input type="hidden" id="con_doc_fee_master"/>
<input type="hidden" id="con_doc_validity"/>
<input type="hidden" id="reg_fee_master"/>
<span style="display:none;" id="uhid"><?php echo $uhid; ?></span>

<input type="button" data-toggle="modal" data-target="#myModal1" id="mod" style="display:none"/>
<input type="hidden" id="mod_chk" value="0"/>
<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
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
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script src="../jss/pat_all_details.js"></script>
<script>
	$(document).on('keyup', ".capital", function () {
		$(this).val(function (_, val) {
			return val.toUpperCase();
		});
	});
	$(document).ready(function(){
		check_dashboard();
		//check_appointment();
		check_adv_book();
		
	});
	$('.helpover').popover({ trigger: "hover" });
	
	function check_adv_book()
	{
		var adv=$("#adv_book_p").val();
		if(adv=='1')
		{
			advnc_book_save('<?php echo $uhid; ?>');
		}
	}
	function save_hc_no()
	{
		$.post("pages/global_insert_data.php",
		{
			type:"health_card_no",
			hc_no:$("#hc_no").val(),
			uhid:$("#uhid").text().trim(),
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<h5>Saved</h5>"});
			setTimeout(function(){
				bootbox.hideAll();
			 }, 500);
		})
	}
	
	function check_dashboard()
	{
		var default_dashboard=$("#default_dashboard").val();
		var pin=$("#opd_pin").val();
		if(pin=="")
		{
			pin="0000";
		}
		if(default_dashboard=="opd")
		{
			if(pin=="0000")
			{
				new_appointment();
			}else
			{
				check_appointment('','',pin);
			}
		}
		if(default_dashboard=="ipd")
		{
			check_ipd();
			check_saved_ipd();
		}
		if(default_dashboard=="lab")
		{
			check_investigation('','',pin);
		}
		if(default_dashboard=="bbank")
		{
			blood_bank();
		}
		if(default_dashboard=="casuality")
		{
			casuality();
		}
	}
	function insurance()
	{
		if($("#insurance").val()=='1')
		{
			$("#ins_det").slideDown();
		}
		else
		{
			$("#ins_det").hide();
		}
	}
	function con_state()
	{
		if($("#con_state").val()=='Other')
		{
			$("#pstate").show();
		}
		else
		{
			$("#pstate").hide();
		}
	}
	function clrr(id)
	{
		$("#"+id).css("border","");
		scrl(id);
	}
	function scrl(id)
	{
		if(id=="add_1")
		$("html,body").animate({scrollTop: '300px'},900);
		if(id=="postal")
		$("html,body").animate({scrollTop: '500px'},900);
		if(id=="con_name")
		$("html,body").animate({scrollTop: '700px'},900);
		if(id=="con_city")
		$("html,body").animate({scrollTop: '900px'},900);
		if(id=="con_phone")
		$("html,body").animate({scrollTop: '1200px'},900);
	}
	
	function load_new_reg()
	{
		var new_reg_btn_param=$("#new_reg_btn_param").val();
		//alert(new_reg_btn_param);
		window.location.href="?param="+btoa(new_reg_btn_param);;
	}
</script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
.p_header
{
	font-size: 16px;
	background-color: #e9f3ff;
	padding: 10px;
	font-weight: 600;
}
.uhid_dis
{
	float: right;
	padding: 10px;
	background-color: white;
	//border-radius: 10px;
	margin-bottom:5px;
}
.custom_table
{
	margin-bottom:0px;
	//background: snow;
}
.popover
{
	color:#000;
}
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
label
{
	display: inline;
	font-weight: bold;
}
</style>

