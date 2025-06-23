<html>
<head>
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
	<!--<link rel="stylesheet" href="../../css/fullcalendar.css" />-->
	
	<!--<link rel="stylesheet" href="../../css/jquery.gritter.css" />-->
	<!--<link rel="stylesheet" href="../../css/colorpicker.css" />-->
	<!--<link rel="stylesheet" href="../../css/datepicker.css" />-->
	<!--<link rel="stylesheet" href="../../css/uniform.css" />-->
	<!--<link rel="stylesheet" href="../../css/select2.css" />-->
	<!--<link rel="stylesheet" href="../../css/bootstrap-wysihtml5.css" />-->
	<link href="../../font-awesome/css/font-awesome.css" rel="stylesheet" />
	<link rel="stylesheet" href="../../css/custom.css" />
	
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<style>
		.sing_par{display:inline-block;padding:5px;width:200px;font-size:10px;}
		.sing_par_i{display:inline-block;padding:5px;width:220px;font-style:italic;font-size:10px;}
		.req_head{ font-size:15px;}
		.table td{font-size:12px;}
		//.req{ margin-bottom:40px;border-top:1px solid;border-bottom:1px solid}
		@media print
		{
			@page {
				margin: 0.2cm;
			}
		}
		.page_break{page-break-after: always;}
	</style>
</head>
<body onkeyup="close_window(event)">

<div class="container-fluid">
<?php
include("../../includes/connection.php");

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$pid=$_GET['patient_id'];	
$opd=$_GET['opd_id'];	

$dep_id=$_GET[dep];


$info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pid'"));
$det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$pid' and opd_id='$opd'"));
$ref=mysqli_fetch_array(mysqli_query($link,"select * from refbydoctor_master where refbydoctorid='$det[refbydoctorid]'"));

if($dep_id==0)
{
	$dep=mysqli_query($link,"select distinct a.category_id,a.type_id from testmaster a,patient_test_details b where b.patient_id='$pid' and b.opd_id='$opd' and a.testid=b.testid  order by a.category_id,a.type_id");
}
else
{
	$dep=mysqli_query($link,"select distinct a.category_id,a.type_id from testmaster a,patient_test_details b where b.patient_id='$pid' and b.opd_id='$opd' and a.testid=b.testid and a.type_id='$dep_id'  order by a.category_id,a.type_id");
}
$tot_req=mysqli_num_rows($dep);
$i=1;
$line_no=0;
$urine_re_page_break=0;
while($dp=mysqli_fetch_array($dep))
{
	if($line_no>0 && $dp["type_id"]==144)
	{
		echo "<div class='page_break'></div>";
		$line_no=0;
	}
	
	if($line_no==0 && $urine_re_page_break==0)
	{
		echo "<p style='font-weight: bold;text-align: center;font-size: 18px;'><u>Test Requisition Form(TRF)</u></p>";
	}
	
	$dname=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$dp[type_id]'"));
	
	$phlb=mysqli_fetch_array(mysqli_query($link,"select b.* from testmaster a,phlebo_sample b where b.patient_id='$pid' and b.opd_id='$opd' and a.testid=b.testid and a.type_id='$dp[type_id]'"));
	
	$lab_id=$opd;
	if($ipd!='')
	{
		$lab_id=$ipd;
	}
	
	if($i>1)
	{
		if($category_id!=$dp["category_id"] && $line_no>0)
		{
			echo "<div class='page_break'></div>";
		}
	}
	$category_id=$dp["category_id"];
	
	$line_no+=7;
?>
	<div class="req" id="<?php echo $dp["type_id"];?>">
		<span class="req_head" style="font-weight:bold;font-size:14px"> <?php echo $dname["name"];?> </span>
		<table class="table table-condensed table-bordered">
			<tr>
				<td>UHID: <?php echo $pid;?></td> <td>Bill No : <?php echo $lab_id;?></td><td>Reg Date: <?php echo convert_date($det["date"])." / ".convert_time($det["time"]);?></td>
			</tr>
			<tr>
				<td>Name: <?php echo $info["name"]." (".$info["phone"].")";?></td><td>Age / Sex:  <?php echo $info["age"]." ".$info["age_type"]." / ".$info["sex"];?></td>
				<td>Ref By : <?php echo $ref["ref_name"];?></td>
			</tr>
			<tr>
				<td colspan="3">Address : <?php echo $info["city"];?></td>
			</tr>
		</table>
<?php
	
	$test=mysqli_query($link,"select a.testname,a.testid,b.* from testmaster a,patient_test_details b where b.patient_id='$pid' and b.opd_id='$opd' and a.testid=b.testid and a.type_id='$dp[type_id]'");
	while($tst=mysqli_fetch_array($test))
	{
		$chk_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$tst[testid]'"));
		
		if($line_no>0 && (($dp["type_id"]==144 && $tst["testid"]==3442) || $tst["testid"]==806))
		{
			echo "<div class='page_break'></div>";
			$line_no=0;
			echo "<p style='font-weight: bold;text-align: center;font-size: 18px;'><u>Test Requisition Form(TRF)</u></p>";
?>
		<span class="req_head" style="font-weight:bold;font-size:14px"> <?php echo $dname["name"];?> </span>
		<table class="table table-condensed table-bordered">
			<tr>
				<td>UHID: <?php echo $pid;?></td> <td>Bill No : <?php echo $lab_id;?></td><td>Reg Date: <?php echo convert_date($det["date"])." / ".convert_time($det["time"]);?></td>
			</tr>
			<tr>
				<td>Name: <?php echo $info["name"]." (".$info["phone"].")";?></td><td>Age / Sex:  <?php echo $info["age"]." ".$info["age_type"]." / ".$info["sex"];?></td>
				<td>Ref By : <?php echo $ref["ref_name"];?></td>
			</tr>
			<tr>
				<td colspan="3">Address : <?php echo $info["city"];?></td>
			</tr>
		</table>
<?php
		}
		
		if($chk_par==1)	
		{
			echo "<div class='sing_par'><b>$tst[testname]: </b></div>";
			$line_no++;
		}
		else
		{
			echo "<div class='test_name'><b>$tst[testname]</b></div>";
			
			$line_no++;
			
			$urine_re_page_break=0;
			if(strpos($tst['testname'],'URINE R') !== false)
			{
				$urine_re_page_break=1;
			}
			if(strpos($tst['testname'],'culture') !== false || strpos($tst['testname'],'CULTURE') !== false || strpos($tst['testname'],'Culture') !== false) 
			{
				//echo "<br/><div><b><i>Antibiotic :</i></b></div>";
				//echo "<br/><div><b><i>History :</i></b></div>";
				$line_no++;
			}
			
			$p=1;
			$par=mysqli_query($link,"select * from Testparameter where TestId='$tst[testid]' order by sequence");
			while($p=mysqli_fetch_array($par))
			{
				$pname=mysqli_fetch_array(mysqli_query($link,"select * from Parameter_old where ID='$p[ParamaterId]'"));
				if($pname["ResultType"]==0)
				{
					echo "<div><b><i>$pname[Name]</i></b></div>";
				}
				else
				{
					echo "<div class='sing_par_i'>$pname[Name]: </div>";
				}
				
				$p++;
				
				if($p>3)
				{
					$p=1;
					$line_no++;
				}
			}
			if($urine_re_page_break>0)
			{
				echo "</div>";
				
				$line_no=0;
				echo "<div class='page_break'></div>";
				echo "<p style='font-weight: bold;text-align: center;font-size: 18px;'><u>Test Requisition Form(TRF)</u></p>";
		?>
				<span class="req_head" style="font-weight:bold;font-size:14px"> <?php echo $dname["name"];?> </span>
				<table class="table table-condensed table-bordered">
					<tr>
						<td>UHID: <?php echo $pid;?></td> <td>Bill No : <?php echo $lab_id;?></td><td>Reg Date: <?php echo convert_date($det["date"])." / ".convert_time($det["time"]);?></td>
					</tr>
					<tr>
						<td>Name: <?php echo $info["name"]." (".$info["phone"].")";?></td><td>Age / Sex:  <?php echo $info["age"]." ".$info["age_type"]." / ".$info["sex"];?></td>
						<td>Ref By : <?php echo $ref["ref_name"];?></td>
					</tr>
					<tr>
						<td colspan="3">Address : <?php echo $info["city"];?></td>
					</tr>
				</table>
		<?php
			}
		}
	}
	
	echo "</div>";
	
	$i++;
	
	if($line_no>30 && $dp["category_id"]==1)
	{
		$line_no=0;
		echo "<div class='page_break'></div>";
	}
	else if($dp["category_id"]!=1)
	{
		$line_no=0;
		echo "<div class='page_break'></div>";
	}
	else
	{
		echo "<br>";
	}
	
	//~ if($i!=$tot_req)
	//~ {
		//~ echo "<div class='page_break'></div>";
		//~ $i++;
	//~ }
}
?>

</div>


</body>
</html>
<script>
	$(document).keydown(function (event) {
		if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
			return false;
		} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent Ctrl+Shift+I        
			return false;
		}
	});
	$(document).on("contextmenu",function(e){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			//e.preventDefault();
		}
	});
	$(document).ready(function(){
		if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
		{
			window.print();
		}
	});
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
