<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=$_GET["uhid"];
$opd_id=$_GET["opdid"];

$norm=$_GET['norm'];
$path=$_GET['path'];
$rad=$_GET['rad'];

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$adv_paid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));

$inv_pat_test_detail_qry=mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid` in ( SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) ");
$all_test="";
$n=1;
while($inv_pat_test_detail=mysqli_fetch_array($inv_pat_test_detail_qry))
{
	if($n==1)
	{
		$all_test=$inv_pat_test_detail["testname"];
	}else
	{
		$all_test.=" , ".$inv_pat_test_detail["testname"];
	}
	$n++;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Lab Receipt,Office Copy,Requisition Slip</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body onafterprint="window.close();" onkeypress="close_window(event)">
<?php
	$norm=explode("@",$norm);
	foreach($norm as $n)
	{
		if($n)
		{
			if($n==1)
			{
?>
	<div class="container-fluid">
		<div class="text-center">
			<!--<img src="../../images/header_logo.jpg" style="width: 100%;">-->
			<?php include('page_header.php'); ?>
		</div>
		<?php include('patient_header.php'); ?>
		<hr>
		<center><b>Laboratory Delivery Receipt</b></center>
		<table class="table">
			<tr>
				<td><b>Total Amount</b> <span class="text-right"><?php echo $pat_pay_detail["tot_amount"]; ?>.00</span></td>
			</tr>
			<?php if($pat_pay_detail["dis_amt"]>0){ ?>
			<tr>
				<td><b>Discount Amount</b> <span class="text-right"><?php echo $pat_pay_detail["dis_amt"]; ?>.00</span></td>
			</tr>
			<?php } ?>
			<tr>
				<td><b>Advance Amount</b> <span class="text-right"><?php echo $adv_paid["amount"]; ?>.00</span></td>
			</tr>
			<tr>
				<td><b>Balance</b> <span class="text-right"><?php echo (($pat_pay_detail["tot_amount"])-($adv_paid["amount"])-($pat_pay_detail["dis_amt"])); ?>.00</span></td>
			</tr>
			<tr>
				<td>
					<b>Test(s): </b><?php echo $all_test; ?>
				</td>
			</tr>
		</table>
		<p>Indian Rupees <?php echo convert_number($adv_paid["amount"]); ?> Only</p>
		<div class="span8"></div>
		<div class="span4 text-right" style="margin-top: 6%;">
			<?php echo $signature; ?>
		</div>
	</div>
	<br>
	<center>
		<p>------------------------------------------------------------ This is a computer generated receipt --------------------------------------------------------</p>
	</center>
	<br><br>
	
	<?php
		}
		if($n==2)
		{
	?>
	
	<div class="container-fluid">
		<center><h5><u>Laboratory Office Copy</u></h5></center>
		<hr/>
		<?php include('patient_header.php'); ?>
		<hr>
		<!--<center><b>Delivery Receipt</b></center>-->
		<table class="table">
			<tr>
				<td><b>Total Amount</b> <span class="text-right"><?php echo $pat_pay_detail["tot_amount"]; ?>.00</span></td>
			</tr>
			<?php if($pat_pay_detail["dis_amt"]>0){ ?>
			<tr>
				<td><b>Discount Amount</b> <span class="text-right"><?php echo $pat_pay_detail["dis_amt"]; ?>.00</span></td>
			</tr>
			<?php } ?>
			<tr>
				<td><b>Advance Amount</b> <span class="text-right"><?php echo $adv_paid["amount"]; ?>.00</span></td>
			</tr>
			<tr>
				<td><b>Balance</b> <span class="text-right"><?php echo (($pat_pay_detail["tot_amount"])-($adv_paid["amount"])-($pat_pay_detail["dis_amt"])); ?>.00</span></td>
			</tr>
			<tr>
				<td>
					<b>Test(s): </b><?php echo $all_test; ?>
				</td>
			</tr>
		</table>
	</div>
	<br><br>
<?php
			}
		}
	}
	
?>
	<div class="container-fluid">
	<?php
	if($path!="0")
	{
		$uname=mysqli_fetch_array(mysqli_query($link,"select Name from Employee where ID='$dt_tm[user]'"));
			
		$user1=explode(" ",$uname['Name']);
		$user=$user1[0];
		
		$path_chk=0;
						
		$class="req_slip";
				
	?>
		<div class="<?php echo $class;?>">
			<center>
				<h5><?php echo $center; ?></h5>
				<b>Laboratory Requisition Slip</b>
			</center>
			<hr>
			<?php include('patient_header.php'); ?>
			<hr>
		<?php
			echo "Requisition Slip - <b>Pathology</b>";
			echo "<br/><br/>";
		?>
			<table class="table table-bordered large_text">
				<tr>
					<th>#</th>
					<th>Testname</th>
				</tr>
				<?php
					
					$i=1;
					$path=explode("@",$path);	
					foreach($path as $p)
					{
						if($p!=0)
						{
							$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$p'"));
							echo "<tr><td>$i</td><td>$tname[testname]</td></tr>";	
							$i++;
						}
					}
				?>
			</table>
			<table width="100%" class="req_bot">
				<tr>
					<td width="20%">Primary Sample</td><td width="30%">:</td> <td width="20%">Sample Collection Time:</td><td width="30%">:</td>
				</tr>
				<tr>
					<td>Lab Receiving Time</td><td width="20%">:</td> <td>Test Completion Time:</td><td width="20%">:</td>
				</tr>
				<tr>
					<td>Clicnical History:</td><td colspan="3">:</td>
				</tr>
			</table>
		</div>
			
		<!--- Requisition Slip(Pathology) End--->	
		<?php
		if($i>15)
		{
			$path_chk++;
		}
	}
	?>
	
	<!--- Requisition Slip(Radiology)--->
<?php
	if($rad!="0")
	{
		$slno=1;
		$rad=explode("@",$rad);	
		foreach($rad as $rd)
		{
			if($rd!=0)
			{
				$j=1;
				
				$rad_chk=mysqli_query($link,"select distinct(type_id) from testmaster where category_id>'1' and testid in(select testid from patient_test_details where patient_id='$uhid' and opd_id='$opd_id')");
				$rad_num=mysqli_num_rows($rad_chk);
				
				$tot_page=$path_chk+$rad_num;
				
				$pg_chk=$tot_page%2;
				
				if($rad_num>0)
				{
					//while($rad_tst=mysqli_fetch_array($rad_chk))
					//{
					?>
						<div class="rad_req_slip">
							<center>
								<h5><?php echo $center; ?></h5>
								<b>Lab Requisition Slip</b>
							</center>
							<hr>
							<?php include('patient_header.php'); ?>
							<hr>
						<?php
							echo "Requisition Slip - <b>Radiology</b>";
							echo "<br/><br/>";
						?>
							<table class="table table-bordered large_text">
								<tr>
									<th>#</th>
									<th>Testname</th>
								</tr>
								<?php
								
								//$rad_dep=mysqli_query($link,"select * from patient_test_details where patient_id='$uhid' and opd_id='$opd_id' and testid in(select testid from testmaster where type_id='$rad_tst[type_id]')");
							//	while($r_d=mysqli_fetch_array($rad_dep))
								//{
									$r_name=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$rd'"));
									echo "<tr><td>$slno</td><td>$r_name[testname]</td></tr>";
									$slno++;
								//}
							?>
							</table>
						</div>
					<?php
						$j++;
					//}
				}
			}
		}
	}
		
	?>
	
	
	
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
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-bottom:5px;
}
hr
{
	margin:0;
	border-bottom:0;
	border-top: 1px solid #000;
}

.page_break {page-break-before: always; padding-top: 5px;}
.req_slip{ min-height:520px;}
.f_req_slip{ min-height:670px;}
.rad_req_slip{ min-height:300px;}

*
{
	font-size:13px;
}
</style>
