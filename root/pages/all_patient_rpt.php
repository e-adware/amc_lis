<?php
include('../../includes/connection.php');

	// Date format convert
	function convert_date($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d M Y', $timestamp);
			return $new_date;
		}
	}
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$encounter=$_GET['encounter'];
	$user_entry=$_GET['user_entry'];
	
	$from=$_GET[from];
	$to=$_GET[to];
	
	$user="";
	if($user_entry>0)
	{
		$user=" and `user`='$user_entry'";
	}
	$type="";
	if($encounter>0)
	{
		$type=" and `type`='$encounter'";
	}
	
?>
<html>
<head>
	<title>Payment  Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script> 
	<script>
		function load_sl(from,to)
		{
			if(from!='')
			{
				for(var i=1;i<from;i++)
				{
					$("#"+i+"").hide();				
				}
			}
			
			if(to!='')
			{
				var lst=$("#all_pat tr:last").attr("id");
				
				for(var j=to+1;j<=lst;j++)
				{
					$("#"+j+"").hide();	
				}
			}
		}
	
	</script>
	<style>
		*{font-size:12px}
	</style>
</head>

<body>
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Patient Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-bordered table-condensed table-report" id="all_pat">
			<tr>
					<th width="5%">#</th>
					<th width="10%">PIN</th>
					<th width="10%">Name</th>
					<th width="10%">Doctor</th>
					<th width="10%">date</th>
					<th width="50%">Test Details</th>
			</tr>
			<?php
					$i=1;
					$cashamt=0;
					$disamt=0;
					$patientdel=mysqli_query($link, "SELECT * FROM `invest_patient_payment_details` WHERE `date` between '$date1' and '$date2' order by `date`  ");
					
					while($d=mysqli_fetch_array($patientdel))
					{
						$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$d[patient_id]'"));
						
						$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$d[type]' "));
						$encounter=$pat_typ_text['p_type'];
						
						$vtst="";
						$qtest=mysqli_query($link,"select a.testid,b.testname from patient_test_details a,testmaster b where a.patient_id='$d[patient_id]' and a.opd_id='$d[opd_id]' and a.testid=b.testid");
						while($qtest1=mysqli_fetch_array($qtest))
						{
						$vtst=$vtst.' ,'.$qtest1['testname'];
						}
						$qdoc=mysqli_fetch_array(mysqli_query($link,"select a.refbydoctorid,b.ref_name from uhid_and_opdid a,refbydoctor_master b where a.patient_id='$d[patient_id]' and a.opd_id='$d[opd_id]' and a.refbydoctorid=b.refbydoctorid"));


						$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
						
						
						$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$d[user]' "));
			?>
					<tr id="<?php echo $i;?>">
						<td><?php echo $i;?></td>
						<td><?php echo $d['opd_id'];?></td>
						<td><?php echo $pat_info['name'];?></td>
						<td><?php echo $qdoc['ref_name'];?></td>
						<td><?php echo convert_date($d['date']);?></td>
						<td><?php echo $vtst;?></td>
					</tr>
			<?php
						$i++;
					}
				?>
			
		</table>
		<?php
		if($from!='' || $to!='')
		{
			?><script>load_sl(<?php echo $from;?>,<?php echo $to;?>)</script><?php		
		}
		
		?>
	</div>
</body>
</html>
<script>window.print();</script>
