<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
//include("pathology_normal_range_new.php");

$top_line_break=10;
$doc_in_a_line=4;
$max_line_in_a_page=25;
$single_page_test_param_num=12;
$div_height="height: 620px;";
$method_max_characters=18;

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
if(!$c_user)
{
	exit();
}
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT `branch_id`,`name` FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

$uhid=mysqli_real_escape_string($link, base64_decode($_GET['uhid']));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET['opd_id']));
$ipd_id=mysqli_real_escape_string($link, base64_decode($_GET['ipd_id']));
$batch_no=mysqli_real_escape_string($link, base64_decode($_GET['batch_no']));
$testid=mysqli_real_escape_string($link, base64_decode($_GET['tstid']));
$category_id=mysqli_real_escape_string($link, base64_decode($_GET['category_id']));
$sign_doc_id=mysqli_real_escape_string($link, base64_decode($_GET['sign_doc_id']));
$user=mysqli_real_escape_string($link, base64_decode($_GET['user']));
$view=mysqli_real_escape_string($link, base64_decode($_GET['view']));

$page_breaker="@@@@";

$doc_sign=mysqli_real_escape_string($link, base64_decode($_GET['sel_doc']));
$docc=explode(",",$doc_sign);

$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));

$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND (`opd_id`='$opd_id' OR `opd_id`='$ipd_id')"));
$bill_id=$pat_reg["opd_id"];

$reg_date=$pat_reg["date"];

if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

if($pat_reg["type"]==3)
{
	$ipd_ref_test=mysqli_fetch_array(mysqli_query($link, "SELECT `consultantdoctorid`,`refbydoctorid` FROM `ipd_test_ref_doc` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no'"));
	
	$refbydoctorid=$ipd_ref_test["refbydoctorid"];
}
else
{
	$refbydoctorid=$pat_reg["refbydoctorid"];
}

$ref_doc=mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name`,`qualification` FROM `refbydoctor_master` WHERE `refbydoctorid`='$refbydoctorid'"));

$pat_pay_det=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));

$barcode_data=$uhid."-".$bill_id."-".$batch_no."-".$pat_info["name"];

if($category_id==2 || $category_id==3)
{
	$result_doc=mysqli_fetch_array(mysqli_query($link,"select `doc` from testresults_rad where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'"));
	
	if($result_doc["doc"]=="" || !$result_doc["doc"])
	{
		$result_doc["doc"]=0;
	}
	
	$doctors[]=$result_doc["doc"];
}

//~ if($category_id==3)
//~ {
	//~ $result_doc=mysqli_fetch_array(mysqli_query($link,"select `doc` from testresults_card where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$testid'"));
	
	//~ if($result_doc["doc"]=="" || !$result_doc["doc"])
	//~ {
		//~ $result_doc["doc"]=0;
	//~ }
	
	//~ $doctors[]=$result_doc["doc"];
//~ }

mysqli_query($link, "INSERT INTO `pathology_report_print_sequence`(`testid`, `user`, `ip_addr`) VALUES ('$testid','$c_user','$ip_addr')");

$doctors=array_unique($doctors);
//print_r($doctors);
//echo sizeof($doctors);

$page=1;
$part=1;
foreach($doctors AS $doctor)
{
	if($doctor>=0)
	{
		//break;
		
		if($category_id==2 || $category_id==3)
		{
			$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `type_id` FROM `testmaster` WHERE `testid`='$testid'"));
			
			$type_id=$test_info["type_id"];
			
			$test_sum_qry=mysqli_query($link,"SELECT * FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");
			
			$test_sum=mysqli_fetch_array($test_sum_qry);
			$summary_text=$test_sum["observ"];
			
			if(strpos($summary_text, $page_breaker) !== false)
			{
				$part=1;
				$summary_texts=explode($page_breaker,$summary_text);
				foreach($summary_texts AS $summary_parts)
				{
					if($summary_parts)
					{
						mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','$part','0','$doctor','$page','3','$c_user','$ip_addr','1')");
						
						$part++;
						$page++;
					}
				}
			}
			else
			{
				mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','3','$c_user','$ip_addr','1')");
				
				$page++;
			}
		}
		//~ if($category_id==3)
		//~ {
			//~ $test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `type_id` FROM `testmaster` WHERE `testid`='$testid'"));
			
			//~ $type_id=$test_info["type_id"];
			
			//~ $test_sum_qry=mysqli_query($link,"SELECT * FROM `testresults_card` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doctor'");
			
			//~ $test_sum=mysqli_fetch_array($test_sum_qry);
			//~ $summary_text=$test_sum["observ"];
			
			//~ if(strpos($summary_text, $page_breaker) !== false)
			//~ {
				//~ $part=1;
				//~ $summary_texts=explode($page_breaker,$summary_text);
				//~ foreach($summary_texts AS $summary_parts)
				//~ {
					//~ if($summary_parts)
					//~ {
						//~ mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','$part','0','$doctor','$page','3','$c_user','$ip_addr','1')");
						
						//~ $part++;
						//~ $page++;
					//~ }
				//~ }
			//~ }
			//~ else
			//~ {
				//~ mysqli_query($link, "INSERT INTO `pathology_report_print`(`patient_id`, `opd_id`, `batch_no`, `type_id`, `testid`, `param_id`, `part`, `tech_id`, `doc_id`, `page_no`, `result_table`, `user`, `ip_addr`, `nabl`) VALUES ('$uhid','$bill_id','$batch_no','$type_id','$testid','0','0','0','$doctor','$page','3','$c_user','$ip_addr','1')");
				
				//~ $page++;
			//~ }
		//~ }
	}
}
/*
// QR Code Start
include('../../phpqrcode/qrlib.php');
$tempDir = '../../phpqrcode/temp/'; 

$filename = $c_user.str_replace("/", "", $pat_reg["opd_id"]).'.png';

$target_file="../../phpqrcode/temp/".$c_user."*.*";

foreach (glob($target_file) as $filename_del) {
	unlink($filename_del);
}

$codeContents="UHID : ".$uhid."\n";
$codeContents.="Bill No : ".$pat_reg["opd_id"]."\n";
$codeContents.="Bill Date : ".date("jS F Y", strtotime($pat_reg["date"]))."\n";
$codeContents.="Patient Name : ".$pat_info["name"]."\n";

QRcode::png($codeContents, $tempDir.''.$filename, QR_ECLEVEL_S, 8);
// QR Code End
*/
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Radiology Report-<?php echo $bill_id."-".$batch_no; ?></title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
		<script src="../../js/jquery.min.js"></script>
		<script src="../../jss/color_print_script.js"></script>
		<!--<link href="../../css/report.css" rel="stylesheet" type="text/css">-->
		<link href="../../css/loader.css" rel="stylesheet" type="text/css">
		<script>
			$(document).on("contextmenu",function(e){
				if($("#user").text().trim()!='102' || $("#user").text().trim()!='102')
				{
					e.preventDefault();
				}
			});
			$(document).ajaxStop(function()
			{
				$("#loader").hide();
			});
			$(document).ajaxStart(function()
			{
				$("#loader").show();
			});
			
		</script>
		<style>
			
		</style>
	</head>
	<body onkeyup="close_window(event)">
<?php
	$page=1;
	
	$total_pages=mysqli_num_rows(mysqli_query($link, "SELECT DISTINCT `page_no` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testid) AND `user`='$c_user' AND `ip_addr`='$ip_addr'"));
	
	// Only Test summary
	
	$result_table="3";
	
	$report_doc_qry=mysqli_query($link, "SELECT DISTINCT `doc_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testid) AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");
	
	while($report_doc=mysqli_fetch_array($report_doc_qry))
	{
		$doc_id=$report_doc["doc_id"];
		
		$report_page_qry=mysqli_query($link, "SELECT DISTINCT `page_no` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testid) AND `doc_id`='$doc_id' AND `user`='$c_user' AND `ip_addr`='$ip_addr' AND `result_table` IN($result_table) ORDER BY `slno` ASC");
		while($report_page=mysqli_fetch_array($report_page_qry))
		{
			$page_no=$report_page["page_no"];
			
			$dept_info=mysqli_fetch_array(mysqli_query($link, "SELECT `id`,`name` FROM `test_department` WHERE `id` IN(SELECT `type_id` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no')"));
			$type_id=$dept_info["type_id"];
			
			if($page>1)
			{
				echo '<div class="pagebreak"></div>';
			}
			
			if($category_id==2 || $category_id==3)
			{
				// Reporting Time
				$report_time=mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `testresults_rad` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
				
				// Report entry by
				$data_entry_names="";
				
				$report_entry_qry=mysqli_query($link, "SELECT DISTINCT a.`saved` FROM `testresults_rad` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
				while($report_entry=mysqli_fetch_array($report_entry_qry))
				{
					$tech_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$report_entry[saved]'"));
					
					$data_entry_names.=$tech_info["name"].",";
				}
			}
			//~ if($category_id==3)
			//~ {
				//~ // Reporting Time
				//~ $report_time=mysqli_fetch_array(mysqli_query($link, "SELECT DISTINCT a.`time`,a.`date` FROM `testresults_card` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'"));
				
				//~ // Report entry by
				//~ $data_entry_names="";
				
				//~ $report_entry_qry=mysqli_query($link, "SELECT DISTINCT a.`saved` FROM `testresults_card` a, `pathology_report_print` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`batch_no`=b.`batch_no` AND a.`testid`=b.`testid` AND b.`patient_id`='$uhid' AND b.`opd_id`='$bill_id' AND b.`batch_no`='$batch_no' AND b.`doc_id`='$doc_id' AND b.`page_no`='$page_no'");
				//~ while($report_entry=mysqli_fetch_array($report_entry_qry))
				//~ {
					//~ $tech_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$report_entry[saved]'"));
					
					//~ $data_entry_names.=$tech_info["name"].",";
				//~ }
			//~ }
		?>
		<?php
			$br=0;
			while($br<$top_line_break)
			{
				echo "<br>";
				$br++;
			}
		?>
			<div class="container-fluid"> <!-- style="border: 2px solid #000;height: 1080px;" -->
				<div class="row">
				<?php
					//include("pathology_report_page_header.php");
					include("radiology_report_header.php");
				?>
				</div>
				<div class="row report_div" style="<?php echo $div_height; ?>">
					<table class="table table-condensed table-no-top-border report_table">
				<?php
					$report_test_qry=mysqli_query($link, "SELECT DISTINCT `testid` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid` IN($testid) AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
					while($report_test=mysqli_fetch_array($report_test_qry))
					{
						$testid=$report_test["testid"];
						
						// Record print
						if($view==0)
						{
							mysqli_query($link, "INSERT INTO `testreport_print`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `date`, `time`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$testid','$date','$time')");
						}
						
						$test_info=mysqli_fetch_array(mysqli_query($link, "SELECT `testname`,`type_id` FROM `testmaster` WHERE `testid`='$testid'"));
						$type_id=$test_info["type_id"];
						
				?>
						<!--<tr>
							<th colspan="6" class="test_name no_top_border" style="text-align:center;"><?php echo $test_info["testname"]; ?></th>
						</tr>-->
				<?php
						$report_qry=mysqli_query($link, "SELECT * FROM `pathology_report_print` WHERE `testid`='$testid' AND `param_id`=0 AND `doc_id`='$doc_id' AND `page_no`='$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC");
						
						while($report=mysqli_fetch_array($report_qry))
						{
							$test_summary_text="";
							
							if($category_id==2)
							{
								$pat_test_summary=mysqli_fetch_array(mysqli_query($link,"SELECT `observ` FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id'"));
							}
							if($category_id==3)
							{
								//$pat_test_summary=mysqli_fetch_array(mysqli_query($link,"SELECT `observ` FROM `testresults_card` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id'"));
								
								$pat_test_summary=mysqli_fetch_array(mysqli_query($link,"SELECT `observ` FROM `testresults_rad` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc`='$doc_id'"));
							}
							
							$test_summary_text=$pat_test_summary["observ"];
							
							if($report["part"]>0)
							{
								$position=$report["part"]-1;
								
								$summary_texts=explode($page_breaker,$test_summary_text);
								
								$test_summary_text=$summary_texts[$position];
							}
							
							$summary_image_qry=mysqli_query($link,"SELECT * FROM `image_temp` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `testid`='$testid' ORDER BY `img_no`");
							$summary_image_num=mysqli_num_rows($summary_image_qry);
							$summary_image=mysqli_fetch_array($summary_image_qry);
							
							if($test_summary_text && $summary_image_num>0)
							{
								echo "<tr><td colspan='3' class='no_top_border'><br>$test_summary_text</td>";
								echo "<td colspan='2' class='no_top_border'>";
						?>
								<img src="../<?php echo $summary_image["Path"];?>" style="border:1px solid #CCC;height:170px;"/> <br/>
						<?php
								echo "</td></tr>";
							}
							else if($test_summary_text && $summary_image_num==0)
							{
								echo "<tr><td colspan='5' class='no_top_border'>$test_summary_text</td></tr>";
							}
							else
							{
								echo "<tr><td colspan='5' class='no_top_border'>";
						?>
								<img src="../<?php echo $summary_image["Path"];?>" style="border:1px solid #CCC;height:170px;"/> <br/>
						<?php
								echo "</td></tr>";
							}
						}
					}
				?>
					</table>
				</div>
				<?php
					include("radiology_report_footer.php");
				?>
			</div>
		<?php
			$page++;
		} // End page_no
	} // End doc
?>
		<span id="user" style="display:none;"><?php echo $c_user; ?></span>
		<div id="loader"></div>
	</body>
<input type="hidden" id="uhid" value="<?php echo $uhid; ?>">
<input type="hidden" id="opd_id" value="<?php echo $opd_id; ?>">
<input type="hidden" id="ipd_id" value="<?php echo $ipd_id; ?>">
<input type="hidden" id="batch_no" value="<?php echo $batch_no; ?>">
<input type="hidden" id="tests" value="<?php echo $tests; ?>">
<input type="hidden" id="bill_id" value="<?php echo $bill_id; ?>">
<input type="hidden" id="view" value="<?php echo $view; ?>">
</html>
<style>
	body
	{
		line-height: 16px !important;
	}
	h3 {
		margin: 0;
	}
	h4 {
		margin: 0;
	}
	.patient_header
	{
		font-size: 13px !important;
		border-bottom: 1.5px solid #000;
	}
	.span_doc
	{
		margin-left: 0  !important;
		width: <?php echo $span_doc_width; ?>% !important;
		font-size: 10px !important;
	}
	.report_footer
	{
		//position: fixed;
		//bottom: 50px;
		width: 100%;
		
		//position: relative;
		//top: 700px;
	}
	.table
	{
		margin-bottom: 0 !important;
	}
	.report_table th, .report_table td
	{
		padding: 1px 1px !important;
		font-size: 13px !important;
	}
	.report_header
	{
		border-bottom: 1.5px solid #000 !important;
	}
	.checked_by
	{
		font-size: 10px !important;
	}
	.checked_by_table th, .checked_by_table td, .patient_header th, .patient_header td
	{
		padding: 0px !important;
	}
	.table-no-top-border th, .table-no-top-border td
	{
		//border-top: 1px solid #000;
	}
	.no_top_border
	{
		border-top: 1px solid #fff !important;
	}
	@page
	{
		margin:0.2cm;
		margin-left:0.8cm;
		//margin-right:0.5cm;
	}
	.test_method
	{
		//display:none;
	}
	
	@media print {
		.pagebreak {
			clear: both;
			page-break-after: always;
		}
	<?php
		if($view>0)
		{
	?>
		*{ display:none; }
	<?php
		}
	?>
		*{
			color-adjust: exact !important;
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
		}
	}
</style>
<script>
	
	$(document).ready(function(){
		$("#loader").hide();
		//$(".test_method").remove();
		
		if($("#view").val()==0)
		{
			window.print();
		}
		
		setColors('body *');
		setIconColors('span');
		//print();
		setTimeout(function () {
			resetColors('body *');
			resetIconColors();
		}, 100);
	});
	
	function save_print_test(tst,uhid,opd_id,ipd_id,batch_no,bill_id)
	{
		window.opener.load_test_detail(uhid,bill_id,batch_no);
		setTimeout(function(){
			window.close();
		},100);
	}
	function close_window(e)
	{
		if(e.which==27)
		{
			window.close();
		}
	}
</script>
<?php
	// Delete from Temp
	mysqli_query($link, "DELETE FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr'");
?>
