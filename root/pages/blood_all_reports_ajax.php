<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");

$fdate=$_POST['fdate'];
$tdate=$_POST['tdate'];

if($_POST["type"]==1)
{
	if($fdate=="" && $tdate=="")
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_inventory`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_inventory` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<b style="font-size:16px;">Donor Inventory Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(1)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(1)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Donor Name</th><th>Blood Group</th><th>Reg Date</th>
		</tr>
	<?php
	$n=1;
	while($r=mysqli_fetch_array($q))
	{
		$d=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_donor_reg` WHERE `donor_id`='$r[donor_id]'"));
		$bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_screwing_details` WHERE `donor_id`='$r[donor_id]'"));
	?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $d['name'];?></td><td><?php echo $bl['abo']." ".$bl['rh'];?></td><td><?php echo convert_date_g($r['entry_date']);?></td>
		</tr>
	<?php
	$n++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]==2)
{
	if($fdate=="" && $tdate=="")
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_reg`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_reg` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<b style="font-size:16px;">Donor Registration Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(2)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(2)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Donor Name</th><th>Blood Group</th><th>Reg Date</th><th>Status</th>
		</tr>
	<?php
	$n=1;
	while($r=mysqli_fetch_array($q))
	{
		$bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_screwing_details` WHERE `donor_id`='$r[donor_id]'"));
		$s=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `blood_donor_inventory` WHERE `donor_id`='$r[donor_id]'"));
		if($s>0)
		{
			$stat="<span style='display:block;color:#000;background:#C1F3C4;'><i class='icon icon-large icon-ok'></i> Accepted</span>";
		}
		else
		{
			$stat="<span style='display:block;color:#000;background:#FFD2CE;'><i class='icon icon-large icon-remove'></i> Rejected</span>";
		}
	?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $r['name'];?></td><td><?php echo $bl['abo']." ".$bl['rh'];?></td><td><?php echo convert_date_g($r['date']);?></td><td><?php echo $stat;?></td>
		</tr>
	<?php
	$n++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]==3)
{
	if($fdate=="" && $tdate=="")
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_rejected`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_donor_rejected` WHERE `entry_date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<b style="font-size:16px;">Donor Rejected Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(3)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(3)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Donor Name</th><th>Blood Group</th><th>Report Date</th><th>Tested Positive For</th>
		</tr>
	<?php
	$n=1;
	while($r=mysqli_fetch_array($q))
	{
		$tst="";
		$d=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_donor_reg` WHERE `donor_id`='$r[donor_id]'"));
		$bl=mysqli_fetch_array(mysqli_query($link,"SELECT `abo`,`rh` FROM `blood_screwing_details` WHERE `donor_id`='$r[donor_id]'"));
		$t=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `blood_screwing_details` WHERE `donor_id`='$r[donor_id]'"));
		if($t['hiv']=="positive")
		$tst.="HIV, ";
		if($t['hep_b']=="positive")
		$tst.="Hep-B, ";
		if($t['hep_c']=="positive")
		$tst.="Hep-C, ";
		if($t['mp']=="positive")
		$tst.="MP, ";
		if($t['vdrl']=="positive")
		$tst.="VDRL";
	?>
		<tr>
			<td><?php echo $n;?></td><td><?php echo $d['name'];?></td><td><?php echo $bl['abo']." ".$bl['rh'];?></td><td><?php echo convert_date_g($r['entry_date']);?></td><td><?php echo $tst;?></td>
		</tr>
	<?php
	$n++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]==4)
{
	$q=mysqli_query($link,"SELECT * FROM `blood_component_master`");
	?>
	<b style="font-size:16px;">Blood Component Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(4)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(4)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th width="80%">Component</th><th>Quantity</th>
		</tr>
	<?php
	$n=1;
	while($r=mysqli_fetch_array($q))
	{
		$count=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) AS tot FROM `blood_component_stock` WHERE `component_id`='$r[component_id]'"));
	?>
	<tr>
		<td><?php echo $n;?></td><td><?php echo $r['name'];?></td><td><?php echo $count['tot'];?></td>
	</tr>
	<?php
	}
	?>
	</table>
	<?php
}

if($_POST["type"]==5)
{
	if($fdate=="" && $tdate=="")
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_receipt`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_receipt` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<b style="font-size:16px;">Blood Receipt Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(5)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(5)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Donor Name</th><th>Pack Name</th><th>Volume</th><th>Bar Code</th><th>Date</th>
		</tr>
	<?php
	$n=1;
	while($r=mysqli_fetch_array($q))
	{
		$d=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_donor_reg` WHERE `donor_id`='$r[donor_id]'"));
		$p=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_pack_master` WHERE `pack_id`='$r[pack_id]'"));
		//$count=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) AS tot FROM `blood_component_stock` WHERE `component_id`='$r[component_id]'"));
	?>
	<tr>
		<td><?php echo $n;?></td><td><?php echo $d['name'];?></td><td><?php echo $p['name'];?></td><td><?php echo $r['volume'];?></td><td><?php echo $r['bar_code'];?></td><td><?php echo convert_date_g($r['date']);?></td>
	</tr>
	<?php
	}
	?>
	</table>
	<?php
}

if($_POST["type"]==6)
{
	if($fdate=="" && $tdate=="")
	{
		$qry=mysqli_query($link,"SELECT DISTINCT `patient_id` FROM `blood_request`");
		//$q=mysqli_query($link,"SELECT * FROM `blood_request`");
	}
	else
	{
		$qry=mysqli_query($link,"SELECT DISTINCT `patient_id` FROM `blood_request` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<style>
	.table tr:hover
	{background:none;}
	.hv:hover
	{background:#ffffff;}
	</style>
	<b style="font-size:16px;">Blood Request Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(6)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(6)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>UHID</th><th>Patient Name</th><th>Date</th><th>Component</th><th>Units</th>
		</tr>
	<?php
	$n=1;
	while($res=mysqli_fetch_array($qry))
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_request` WHERE `patient_id`='$res[patient_id]'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$d=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_donor_reg` WHERE `donor_id`='$r[donor_id]'"));
			$p=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_pack_master` WHERE `pack_id`='$r[pack_id]'"));
			$nm=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_component_master` WHERE `component_id`='$r[component_id]'"));
			//$count=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) AS tot FROM `blood_component_stock` WHERE `component_id`='$r[component_id]'"));
		?>
		<tr>
			<?php
			if($num>0)
			{
			?>
			<td rowspan="<?php echo $num;?>"><?php echo $n;?></td>
			<td rowspan="<?php echo $num;?>"><?php echo $nm['uhid'];?></td>
			<td rowspan="<?php echo $num;?>"><?php echo $nm['name'];?></td>
			<td rowspan="<?php echo $num;?>"><?php echo convert_date_g($r['date']);?></td>
			<?php
			}
			?>
			<td class="hv"><?php echo $c['name'];?></td>
			<td class="hv"><?php echo $r['units'];?></td>
		</tr>
		<?php
		$num=0;
		}
		$n++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]==7)
{
	if($fdate=="" && $tdate=="")
	{
		$qry=mysqli_query($link,"SELECT DISTINCT `patient_id` FROM `blood_issue`");
	}
	else
	{
		$qry=mysqli_query($link,"SELECT DISTINCT `patient_id` FROM `blood_issue` WHERE `date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<style>
	.table tr:hover
	{background:none;}
	.hv:hover
	{background:#ffffff;}
	</style>
	<b style="font-size:16px;">Blood Issued Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(7)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(7)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>UHID</th><th>Patient Name</th><th>Date</th><th>Component</th>
		</tr>
	<?php
	$n=1;
	while($res=mysqli_fetch_array($qry))
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_issue` WHERE `patient_id`='$res[patient_id]'");
		$num=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$d=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_donor_reg` WHERE `donor_id`='$r[donor_id]'"));
			$p=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_pack_master` WHERE `pack_id`='$r[pack_id]'"));
			$nm=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_component_master` WHERE `component_id`='$r[component_id]'"));
			//$count=mysqli_fetch_array(mysqli_query($link,"SELECT COUNT(*) AS tot FROM `blood_component_stock` WHERE `component_id`='$r[component_id]'"));
		?>
		<tr>
			<?php
			if($num>0)
			{
			?>
			<td rowspan="<?php echo $num;?>"><?php echo $n;?></td>
			<td rowspan="<?php echo $num;?>"><?php echo $nm['uhid'];?></td>
			<td rowspan="<?php echo $num;?>"><?php echo $nm['name'];?></td>
			<td rowspan="<?php echo $num;?>"><?php echo convert_date_g($r['date']);?></td>
			<?php
			}
			?>
			<td class="hv"><?php echo $c['name'];?></td>
		</tr>
		<?php
		$num=0;
		}
	$n++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]==8)
{	
	$q=mysqli_query($link,"SELECT * FROM `blood_component_stock` WHERE `expiry_date`='$date'");
	?>
	<b style="font-size:16px;">Expiring Today Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(8)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(8)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Component</th><th>Bar Code</th><th>Date</th>
		</tr>
	<?php
	$n=1;
	while($r=mysqli_fetch_array($q))
	{
		$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_component_master` WHERE `component_id`='$r[component_id]'"));
	?>
	<tr>
		<td><?php echo $n;?></td><td><?php echo $c['name'];?></td><td><?php echo $r['bar_code'];?></td><td><?php echo convert_date_g($r['expiry_date']);?></td>
	</tr>
	<?php
	$n++;
	}
	?>
	</table>
	<?php
}

if($_POST["type"]==9)
{
	if($fdate=="" && $tdate=="")
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_component_expired`");
	}
	else
	{
		$q=mysqli_query($link,"SELECT * FROM `blood_component_expired` WHERE `expiry_date` BETWEEN '$fdate' AND '$tdate'");
	}
	?>
	<b style="font-size:16px;">Expired Report</b><span class="text-right" style="margin-right:20px;"><button type="button" class="btn btn-mini btn-primary" onclick="print_file(9)"><i class="icon-print icon-large"></i> Print</button> <button type="button" class="btn btn-mini btn-primary" onclick="export_file(9)"><i class="icon-file icon-large"></i> Export</button></span>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th><th>Component</th><th>Bar Code</th><th>Date</th>
		</tr>
	<?php
	$n=1;
	while($r=mysqli_fetch_array($q))
	{
		$c=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `blood_component_master` WHERE `component_id`='$r[component_id]'"));
	?>
	<tr>
		<td><?php echo $n;?></td><td><?php echo $c['name'];?></td><td><?php echo $r['bar_code'];?></td><td><?php echo convert_date_g($r['expiry_date']);?></td>
	</tr>
	<?php
	$n++;
	}
	?>
	</table>
	<?php
}
?>
