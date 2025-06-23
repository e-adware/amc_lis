<?php
session_start();

include("../../includes/connection.php");
require("../../includes/global.function.php");

$c_user=$_SESSION["emp_id"];

$patient_id=$uhid=mysqli_real_escape_string($link, base64_decode($_GET["uhid"]));
$opd_id=mysqli_real_escape_string($link, base64_decode($_GET["opdid"]));
$ipd_id="";
$batch_no=1;
$user=mysqli_real_escape_string($link, base64_decode($_GET["user"]));

$pat_reg=$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$centre_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$pat_reg[center_no]' "));

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$pat_reg["date"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }


if($pat_reg["branch_id"])
{
	$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));
	$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));
	
	$branch_id=$pat_reg["branch_id"];
}
else
{
	if($branch_id)
	{
		$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$branch_id' limit 0,1 "));
		$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$branch_id' limit 0,1 "));
	}
	else
	{
		$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
		$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` limit 0,1 "));
	}
}

$signature="For ".$company_info['name'];
$phon="";
if($company_info["phone1"])
$phon.=$company_info["phone1"];
if($company_info["phone2"])
$phon.=", ".$company_info["phone2"];
if($company_info["phone3"])
$phon.=", ".$company_info["phone3"];

$header2="                       ".$company_info["address"].", ".$company_info["city"].", ".$company_info["state"]."-".$company_info["pincode"];
//$header3="     Phone Number(s): ".$phon." Email: ".$company_info["email"];
$header3="     Phone Number(s): ".$phon;

if($pat_info["address"])
{
	$address=$pat_info["address"];
}
else
{
	$address=$pat_info["city"];
}




if($reg['centreno']=="C100")
{
	$vcntrtype="Walk In";
}
else
{
	$vcntrtype="Home Collection";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo $page_head_name; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<style>
		.span6
		{
			width:450px;
		}
		.span3
		{
			width:250px;
		}
	</style>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="span1">
				<img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png" style="width:80px;margin-top:0px;margin-bottom:-70px;" />
			</div>
			<div class="span7 text-center" style="margin-left:0px;">
				<span style="font-size:12px;"><?php echo $page_head_line;; ?></span>
				<h4>
					<?php echo $company_info["name"]; ?><br>
					<small>
						<!--<?php echo $company_info["city"]."-".$company_info["pincode"].", ".$company_info["state"]; ?><br/>-->
					<?php echo $company_info["address"]; ?>
				<?php if($company_info["phone1"]){ ?>
					<br>
					Contact: <?php echo $company_info["phone1"]; ?>
				<?php } ?>
					<?php echo $company_info["phone2"]; ?>
					
				<?php if($company_info["email"]){ ?>
					<br>
					Email: <?php echo $company_info["email"]; ?>
				<?php } ?>
					</small>
				</h4>
			</div>
			<div class="span2" style="margin-left: 0 !important;">
				<span style="line-height: 16px;">
					LCD/Lab/PRF/Fr-14
					<br>
					Amend. No,: 00
					<br>
					Date: 01-09-2012
				</span>
			</div>
		</div>
		<div class="row">
			<div class="span6">
				<table class="table table-bordered table-condensed">
					<tr>
						<td>Patient ID</td>
						<td><?php echo $opd_id;?></td>
						<td>Date</td>
						<td><?php echo date("d-m-Y");?></td>
					</tr>
					<tr>
						<td>Patient's Name - Phone</td>
						<td colspan="3"><?php echo $pat_info["name"];?> - <?php echo $pat_info['phone'];?></td>
					</tr>
					<tr>
						<td>Age  </td><td><?php echo $age;?></td>
						<td>Sex  </td>
						<td>
							<?php echo $pat_info["sex"]; ?>
						</td>
					</tr>
					<tr>
						<td>Height</td><td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CM</td>
						<td>Weight</td><td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; KG</td>
					</tr>
					<tr>
						<td>Address</td>
						<td colspan="3"><?php echo $address;?></td>
					</tr>
					<tr>
						<td>Ref. Doctor</td>
						<td colspan="3"><?php echo $ref_doc["ref_name"]; ?></td>
					</tr>
				</table>
				 <table class="table table-bordered table-condensed">
					<tr>
						<th>Time of Sample Colection</th>
						<td style="width:50%;"></td>
					</tr>
					<tr>
						<th>Time of Sample Received in lab</th>
						<td style="width:50%;"></td>
					</tr>
				 </table>
				  

				 <table class="table table-bordered table-condensed">
					<tr>
						<th>Clinical History</th>
					</tr>
					<tr>
						<th >a) Clinical History / Diagnosis: </th>
					</tr>
					<tr>
						<th >b) Medication if any:</th>
					</tr>
				 </table>
			</div>
			<div class="span3">
				<table class="table table-bordered table-condensed">
					<tr>
						<th style="width: 40%;">Test Name</th>
						<th>Sample(s)</th>
					</tr>
				
				<?php
					$smp_name="";
					
					$typ=mysqli_query($link,"SELECT distinct a.`vac_id` FROM `test_vaccu` a,patient_test_details b WHERE a.`testid`=b.`testid` and b.patient_id='$uhid' and b.opd_id='$opd_id' order by b.slno   ");
					while($smp=mysqli_fetch_array($typ))
					{
						$sname=mysqli_fetch_array(mysqli_query($link,"select type from vaccu_master where id='$smp[vac_id]'"));
						$smp_name.="<label class='vac_name'>".$sname["type"]." <input class='chk' type='checkbox' style='margin-top: -4px;'></label>";
					}
				
					$i=1;
					$tests=mysqli_query($link,"select * from patient_test_details where patient_id='$uhid' and opd_id='$opd_id' order by slno");
					$t_rows=mysqli_num_rows($tests);
					$test_name="";
					while($t=mysqli_fetch_array($tests))
					{
						
						$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$t[testid]' and type_id !='132'"));
						if($tname["category_id"]==1)
						{
							$vnabl="*";
							$qnable=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `nabl_logo` WHERE `testid`='$t[testid]'"));
							if($qnable)
							{
								$vnabl="";
							}
							$test_name.=$tname["testname"].$vnabl."<br/>";
							$i++;
						}
					}
					echo "<tr><td>$test_name</td><td>$smp_name</td></tr>";
					//~ if($i<12)
					//~ {
						//~ $n_i=12-$i;
						
						//~ for($j=0;$j<=$n_i;$j++)
						//~ {
							//~ echo "<tr><td style='height:30px'></td></tr>";
						//~ }
					//~ }
				?>
					
				</table>
				(Tests marked with * are not under NABL accreditation)
			</div>
		</div>
		<div class="">
			<div>
				Deviations from this service agreement (will be informed immediately to the requester of the tests in the contact
				number provided)</br>
				a) Referral laboratory (If any test/examination is required to be referred to another NABL accredited lab)</br>
				Name of lab:-</br>
				Reason for referral:-</br>
				Expected date and time of report delivery:-</br>
				b) Delay in delivery of report (In the event of technical/managerial failures)</br>
				Reason for delay:-</br>
				Expected date and time </br>
			</div>
				
			<div class="row" >
				<div class="span6" style="text-align:center" >
					Signature of Requester
				</div>
				<div class="span3">
					
					Signature of Collection Staff
				</div>	
			</div>
		</div>
	</div>
</body>
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
h4
{
	margin: 0 0 15px 0 !important;
}
.table-condensed > thead > tr > th, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > tbody > tr > td, .table-condensed > tfoot > tr > td
{
	padding: 3px !important;
}
@page
{
	margin-left:0cm;
	margin-right:0.2cm;
}
</style>
