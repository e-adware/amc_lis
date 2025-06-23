<?php
$uhid=base64_decode($_GET["uhid"]);
$uhid=(int)$uhid;
//$opd=base64_decode($_GET["opd"]);
//$opd=(int)$opd;

//$pat_opd=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `opd_id` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' order by`slno` DESC limit 0,1 "));
//$opd=$pat_opd["opd_id"];
$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
if($pat_info["blood_group"]==""){ $blood_group="N/A"; }else{ $blood_group=$pat_info["blood_group"]; }
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
			<th>Sex</th>
			<th>Blood Group</th>
			<th>Phone</th>
			<th>Address</th>
		</tr>
		<tr>
			<td><?php echo $uhid; ?></td>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
			<td><?php echo $blood_group; ?></td>
			<td><?php echo $pat_info["phone"]; ?></td>
			<td>
				<?php echo $pat_info["address"]; ?> 
				<span class="text-right">
					<a href="index.php?param=MQ==&uhid=<?php echo $_GET["uhid"]; ?>" ><i class="icon-edit"></i></a>
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
				<li class="active" onClick="check_appointment()"><a data-toggle="tab" href="#tab1">Consultation</a></li>
				<li class="" onClick="check_investigation('','')"><a data-toggle="tab" href="#tab2">Investigation</a></li>
				<li class="" onclick="check_ipd();check_saved_ipd()"><a data-toggle="tab" href="#tab3">IPD</a></li>
			</ul>
		</div>
		<div class="widget-content tab-content">
			<div id="tab1" class="tab-pane active">
				<div id="load_all">
					
				</div>
				<div id="print_div">
					
				</div>
				<div id="edit_div">
					
				</div>
			</div>
			<div id="tab2" class="tab-pane">
				<div id="load_investigation">
					
				</div>
				<div id="out_test_form">
					
				</div>
			</div>
			<div id="tab3" class="tab-pane">
				<div id="ipd_saved">
					
				</div>
				<div id="ipd_sect">
				
				</div>
			</div>
		</div>
	</div>
	<div id="loader" style="display:none;"></div>
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
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script src="../jss/pat_all_details.js"></script>
<script>
$(document).ready(function(){
	
	check_appointment();
});
$('.helpover').popover({ trigger: "hover" });

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
</style>

