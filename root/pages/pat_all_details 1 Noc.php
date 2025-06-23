<?php
$c_user=trim($_SESSION['emp_id']);
$uhid=base64_decode($_GET["uhid"]);
$opd_id=base64_decode($_GET["opd"]);
$lab=base64_decode($_GET["lab"]);
$consult=base64_decode($_GET["consult"]);
$uhid=trim($uhid);
$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
//if($pat_info["blood_group"]==""){ $blood_group="N/A"; }else{ $blood_group=$pat_info["blood_group"]; }
$lab_acs="";
$consult_acs="";
if($lab)
{
	if($lab==1)
	{
		$lab_acs="Yes";
	}
	$paramm=base64_encode(82);
}
if($consult)
{
	if($consult==1)
	{
		$consult_acs="Yes";
	}
	$paramm=base64_encode(81);
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
	$cashier_access=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `cashier_access` WHERE `emp_id`='$c_user' "));
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
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Patient Dashboard</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<!--<p class="p_header">Visit Information</p>-->
	<!--<span class="uhid_dis"><b>UHID</b>: <text style="font-size:18px;"><?php echo $uhid; ?></text></span>-->
	<table class="table" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>Name</th>
			<th>Age</th>
			<th colspan="2">Sex</th>
			<!--<th>Blood Group</th>-->
			<!--<th>Phone</th>
			<th>Address</th>-->
			<!--<th></th>-->
		</tr>
		<tr>
			<td><?php echo $pat_info["uhid"]; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
			<!--<td><?php echo $blood_group; ?></td>-->
			<!--<td><?php echo $pat_info["phone"]; ?></td>-->
			<td>
				<!--<?php echo $pat_info["address"]; ?> -->
				<span class="text-right">
					<a href="index.php?param=<?php echo $paramm; ?>=&uhid=<?php echo $_GET["uhid"]; ?>" ><i class="icon-edit"></i></a>
				</span>
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
				<li class="" onClick="check_investigation('','','<?php echo $opd_id; ?>')"><a data-toggle="tab" href="#tab2">Investigation</a></li>
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
<input type="hidden" id="con_doc_id" value="0"/>
<input type="hidden" id="con_doc_fee"/>
<input type="hidden" id="con_doc_validity"/>
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
	$(document).ready(function(){
		check_dashboard();
		//check_appointment();
	});
	$('.helpover').popover({ trigger: "hover" });
	
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
		if(default_dashboard=="opd")
		{
			check_appointment('','',pin);
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

</style>

