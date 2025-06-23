<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$type=$_POST['type'];

if($type==1)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	
	?>
	<table class="table">
		<tr>
			<th>Patient Id</th>
			<th> OPD Id </th>
			<th> Patient Name </th>
			<th> Medicine </th>
			<th></th>
		</tr>
		<?php
		
		$q=mysqli_query($link,"select distinct patient_id,opd_id from medicine_check ");
		while($q1=mysqli_fetch_array($q)){
		$qname=mysqli_fetch_array(mysqli_query($link,"select name from patient_info where patient_id='$q1[patient_id]'"));
		$vtest="";
		$qmedicin=mysqli_query($link,"select a.item_code,b.item_name from medicine_check a,ph_item_master b where a.item_code=b.item_code and a.patient_id='$q1[patient_id]' and a.opd_id='$q1[opd_id]'");
		while($qmedicin1=mysqli_fetch_array($qmedicin)){
			if($vtest)
			{
			$vtest.=', '.$qmedicin1['item_name'];
			}
			else
			{
			$vtest=$qmedicin1['item_name'];
			}
		}
		?>
		
		<tr>
			<td><?php echo $q1['patient_id'];?></td>
			<td><?php echo $q1['opd_id'];?></td>
			<td><?php echo $qname['name'];?></td>
			<td><?php echo $vtest;?></td>
			<td><input type="button" id="save_ipd_pay" value="Process" class="btn btn-info" /></td>
		</tr>
		<?php
	}?>
	</table>
	<?php
}

else if($type==2)
{
	?>
	<table class="table">
		<tr>
			<th>Patient Id</th>
			<th> IPD Id </th>
			<th> Patient Name </th>
			<th> Medicine </th>
			<th></th>
		</tr>
		<?php
		
		  $qipd=mysqli_query($link,"select distinct patient_id,ipd_id,indent_num from ipd_pat_medicine_indent ");
		while($qipd1=mysqli_fetch_array($qipd)){
		$qname=mysqli_fetch_array(mysqli_query($link,"select name from patient_info where patient_id='$qipd1[patient_id]'"));
		$vtest="";
		$qmedicin=mysqli_query($link,"select a.item_code,b.item_name from ipd_pat_medicine_indent a,ph_item_master b where a.item_code=b.item_code and a.patient_id='$qipd1[patient_id]' and indent_num='$qipd1[indent_num]'");	
		while($qmedicin1=mysqli_fetch_array($qmedicin)){
			$vtest=$vtest.' ,'.$qmedicin1['item_name'];
		}
		
		?>
		<tr>
			<td><?php echo $qipd1['patient_id'];?></td>
			<td><?php echo $qipd1['ipd_id'];?></td>
			<td><?php echo $qname['name'];?></td>
			<td><?php echo $vtest;?></td>
			<td><input type="button" id="save_ipd_pay" value="Process" class="btn btn-info" /></td>
		</tr>
		
		<?php
	  }?>
	  
	</table>
	<?php
}

?>
