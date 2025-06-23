<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");

$div_height="height: 350px;";


$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));

$patient_id=$_GET['patient_id'];
$opd_id=$_GET['opd_id'];
$ipd_id=$_GET['ipd_id'];
$batch_no=$_GET['batch_no'];
$dep_id=$_GET['dep_id'];

$str="SELECT a.`testid`,a.`category_id`,a.`type_id`,a.`lineno` FROM `testmaster` a, `patient_test_details` b WHERE a.`testid`=b.`testid` AND b.`patient_id`='$patient_id' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no'";
if($dep_id>0)
{
	$str.=" AND a.`type_id`='$dep_id'";
}

$str.=" ORDER BY a.`type_id`,a.`testid` ASC";

$type_id=0;
$page_no=0;
$qry=mysqli_query($link, $str);
while($data=mysqli_fetch_array($qry))
{
	$page_break=0;
	
	$testid=$data["testid"];
	$category_id=$data["category_id"];
	
	if($type_id!=$data["type_id"])
	{
		$type_id=$data["type_id"];
		
		$page_no++;
	}
	if($data["lineno"]>0)
	{
		$page_no++;
		
		$page_break++;
	}
	else
	{
		$test_param=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(COUNT(`ParamaterId`),0) AS `total` FROM `Testparameter` WHERE `TestId`='$testid'"));
		$test_param_num=$test_param["total"];
		
		if($test_param_num>1)
		{
			$page_no++;
			
			$page_break++;
		}
	}
	
	mysqli_query($link, "INSERT INTO `trf_print`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `category_id`, `type_id`, `testid`, `page_no`, `user`, `ip_addr`) VALUES ('$patient_id','$opd_id','$ipd_id','$batch_no','$category_id','$type_id','$testid','$page_no','$c_user','$ip_addr')");
	
	if($page_break>0)
	{
		$page_no++;
	}
}

$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id'"));

$pat_reg=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND (`opd_id`='$opd_id' OR `opd_id`='$ipd_id')"));
$bill_id=$pat_reg["opd_id"];

$reg_date=$pat_reg["date"];

$centre_info=mysqli_fetch_array(mysqli_query($link, "SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]'"));

if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link,"select * from refbydoctor_master where refbydoctorid='$pat_reg[refbydoctorid]'"));

//$phlebo_sample_note=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `phlebo_sample_note` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no'"));
?>
<html>
<head>
	<title>Test Requisition Form(TRF)</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
</head>
<body onafterprint="window.close();" onkeyup="close_window(event)">
<?php
	$zz=1;
	$page_qry=mysqli_query($link, "SELECT DISTINCT `page_no` FROM `trf_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `page_no` ASC");
	while($pages=mysqli_fetch_array($page_qry))
	{
		if($zz>1)
		{
			echo "<div class='page_break'></div>";
		}
		
		$page_no=$pages["page_no"];
		
		$page_info=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `trf_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no'"));
		$type_id=$page_info["type_id"];
		
		$dept_info=mysqli_fetch_array(mysqli_query($link, "SELECT `id`,`name` FROM `test_department` WHERE `id`='$type_id'"));
		
		// Sample
		$sample_names="";
		
		$sample_qry=mysqli_query($link, "SELECT DISTINCT a.`Name` FROM `Sample` a, `patient_test_details` b WHERE a.`ID`=b.`sampleid` AND b.`patient_id`='$patient_id' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no' AND b.`testid` IN(SELECT DISTINCT `testid` FROM `trf_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `page_no`='$page_no')");
		
		while($samples=mysqli_fetch_array($sample_qry))
		{
			$sample_names.=$samples["Name"].",";
		}
		
		$phlb_receive=mysqli_fetch_array(mysqli_query($link,"SELECT b.* FROM `testmaster` a,`phlebo_sample` b WHERE b.`patient_id`='$patient_id' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no' AND a.`testid`=b.`testid` AND a.`type_id`='$type_id' ORDER BY `slno` ASC LIMIT 1"));
		$lab_receive=mysqli_fetch_array(mysqli_query($link,"SELECT b.* FROM `testmaster` a,`patient_test_details` b WHERE b.`patient_id`='$patient_id' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no' AND a.`testid`=b.`testid` AND a.`type_id`='$type_id' ORDER BY `slno` ASC LIMIT 1"));
		
		$phlb_user=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$phlb_receive[user]'"));
		$lab_user=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$lab_receive[user]'"));
?>
	<div class="container-fluid"> <!-- style="border: 2px solid #000;height: 1080px;" -->
		<div class="row">
			<div>
				<div style="font-weight: bold;text-align: center;font-size: 18px;"><u>Lab Slip</u></div>
				<div class="req_head" style="font-weight:bold;font-size:14px"> <?php echo $dept_info["name"];?> </div>
				<table class="table table-condensed table-bordered" style="margin-bottom: 10px;">
					<tr>
						<td>ULID</td>
						<td>: <?php echo $patient_id; ?></td>
						<td><?php echo $prefix_det["prefix"]; ?></td>
						<td>: <?php echo $opd_id; ?></td>
						<td>Reg. Time </td>
						<td>: <?php echo date("d-M-Y",strtotime($pat_reg["date"])); ?> <?php echo date("h:i A",strtotime($pat_reg["time"])); ?></td>
					</tr>
					<tr>
						<td>Name</td>
						<td>: <?php echo $pat_info["name"]; ?></td>
						<td>Age</td>
						<td>: <?php echo $age; ?></td>
						<td>Gender</td>
						<td>: <?php echo $pat_info["sex"]; ?></td>
					</tr>
					<tr>
						<td>Address</td>
						<td >: <?php echo $centre_info["centrename"]; ?></td>
						<td>Ref By</td>
						<td colspan="3">: <?php echo $ref_doc["ref_name"]; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row test_div" style="<?php echo $div_height; ?>">
<?php
		$test=mysqli_query($link,"SELECT a.`testname`,a.`testid`,b.* FROM `testmaster` a,`trf_print` b WHERE b.`patient_id`='$patient_id' AND b.`opd_id`='$opd_id' AND b.`ipd_id`='$ipd_id' AND b.`batch_no`='$batch_no' AND a.`testid`=b.`testid` AND b.`type_id`='$type_id' AND b.`page_no`='$page_no'");
		while($tst=mysqli_fetch_array($test))
		{
			$chk_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$tst[testid]'"));
			
			if($chk_par==1)	
			{
				echo "<div class='sing_par div1'><b>$tst[testname]: </b></div>";
			}
			else
			{
				echo "<div class='test_name'><b>$tst[testname]</b></div>";
				
				$culture=0;
				if(strpos($tst['testname'],'culture') !== false || strpos($tst['testname'],'CULTURE') !== false || strpos($tst['testname'],'Culture') !== false) 
				{
					$culture++;
				}
				if($culture==0)
				{
					$n=1;
					$par=mysqli_query($link,"select * from Testparameter where TestId='$tst[testid]' order by sequence");
					while($p=mysqli_fetch_array($par))
					{
						$pname=mysqli_fetch_array(mysqli_query($link,"select * from Parameter_old where ID='$p[ParamaterId]'"));
						
						$param_sample=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `Sample` WHERE `ID`='$pname[sample]'"));
						if($param_sample)
						{
							$sample_name=$param_sample["Name"];
						}
						else
						{
							$sample_name=$test_sample["Name"];
						}
						
						if($pname["ResultType"]==0)
						{
							if($tst['testid']=="806")
							{
								echo "<div class=''><b><i>$pname[Name] :</i></b></div>";
							}
							else
							{
								echo "<div class=''><b><i>$pname[Name] - $sample_name :</i></b></div>";
							}
							
							$n=1;
						}
						else
						{
							if($tst['testid']=="806")
							{
								echo "<div class='sing_par_i div$n'>$pname[Name] :</div>";
							}
							else
							{
								echo "<div class='sing_par_i div$n'>$pname[Name] - $sample_name: </div>";
							}
							
							$n++;
						}
						
						if($n>2)
						{
							$n=1;
						}
					}
				}
			}
		}
?>
		</div>
		<!--<div class="row" style="">
			<table class="table table-condensed table-no-top-border table_footer" style="margin-bottom: 0;">
				<tr>
					<td>
						Sample collected Time : <?php if($phlb_receive["time"]){ echo date("h:i A",strtotime($phlb_receive["time"])); } ?>
						<br>
						Signature : <?php if($phlb_user){ echo $phlb_user["name"]; } ?>
					</td>
					<td>
						Lab Received Time : <?php if($lab_receive["time"]){ echo date("h:i A",strtotime($lab_receive["time"])); } ?>
						<br>
						Signature : <?php if($lab_user){ echo $lab_user["name"]; } ?>
					</td>
					<td>
						Test Completed Time :
						<br>
						Signature :
					</td>
					<td>
						Data Reviewed Time :
						<br>
						Signature :
					</td>
				</tr>
			</table>
		</div>-->
	</div>
<?php
		$zz++;
	}
?>
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php //include('page_header.php');?>
			</div>
		</div>
		<div>
			
		</div>
	</div>
</html>
<script>
	window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
<style>
	.table th, .table td
	{
		font-size:12px;
	}
	.table-condensed th, .table-condensed td{ padding: 0px 5px; }
	.table_footer th, .table_footer td{ text-align: left;padding-right: 20px; }
	.div1{ width:400px; }
	.div2{ width:300px; }
	.sing_par{display:inline-block;padding:0px;font-size:12px;}
	.sing_par_i{display:inline-block;padding-bottom: 5px;font-style:italic;font-size:13px;}
	
	@page {
		margin: 0.2cm;
		margin-left: 0.5cm;
	}
</style>
<?php
	mysqli_query($link, "DELETE FROM `trf_print` WHERE `patient_id`='$patient_id' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr'");
?>
