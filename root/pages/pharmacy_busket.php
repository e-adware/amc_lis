<?php
// Check patients from last 7 days
$date2 = date('Y-m-d');
$date1 = date('Y-m-d', strtotime(date("Y-m-d", strtotime($date2)) . " -7 days"));
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Pharmacy Basket</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="">
		<div class=" ScrollStyle"> <!-- custom_span5 -->
			<table class="table table-hover table-condensed">
				<tr>
					<th colspan="5"><center>IPD Patients</center></th>
				</tr>
				<tr>
					<!--<th>UHID</th>-->
					<th>IPD ID</th>
					<th>Name</th>
					<th>Ward</th>
					<th>Bed No</th>
					<th>Drugs</th>
				</tr>
			<?php
				$n=1;
				
				$qry=mysqli_query($link," SELECT * FROM `patient_medicine_detail` WHERE `type`='2' AND `status`='0' AND `date` BETWEEN '$date1' AND '$date2' GROUP BY `patient_id`,`pin`,`type` ");
				while($ipd_basket=mysqli_fetch_array($qry))
				{
					$qno=mysqli_query($link," SELECT distinct indent_num FROM `patient_medicine_detail` WHERE `type`='$ipd_basket[type]' AND `status`='0' AND `date` BETWEEN '$date1' AND '$date2' and  `patient_id`='$ipd_basket[patient_id]' AND `pin`='$ipd_basket[pin]' ");
					while($qno1=mysqli_fetch_array($qno))
					{
					
						$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$ipd_basket[patient_id]' "));
						$all_drugs="";
						$drug_qry=mysqli_query($link," SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$ipd_basket[patient_id]' AND `pin`='$ipd_basket[pin]' AND `type`='$ipd_basket[type]' AND `status`='0' and indent_num='$qno1[indent_num]' ");
						while($drugs=mysqli_fetch_array($drug_qry))
						{
							$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$drugs[item_code]'"));
							$all_drugs.=$m["item_name"].", ";
						}
						
						$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_bed_alloc_details where patient_id='$ipd_basket[patient_id]' and ipd_id='$ipd_basket[pin]' ORDER BY `slno` DESC limit 0,1"));
						
						$ward=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_det[ward_id]'"));
						$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed_det[bed_id]'"));
						
				?>
					<tr style="cursor:pointer;" onClick="opd_medicine_process('<?php echo $ipd_basket["patient_id"]; ?>','<?php echo $ipd_basket["pin"]; ?>','<?php echo $ipd_basket["type"]; ?>','<?php echo $qno1["indent_num"]; ?>')">
						<!--<td><?php echo $pat_info['patient_id']; ?></td>-->
						<td><?php echo $ipd_basket['pin']; ?></td>
						<td><?php echo $pat_info['name']; ?></td>
						<td><?php echo $ward["name"]; ?></td>
						<td><?php echo $bed_det["bed_no"]; ?></td>
						<td><?php echo $all_drugs; ?></td>
					</tr>
				<?php
					$n++;
					}
			   }
			?>
			</table>
		</div>
		<div class="custom_span5 ScrollStyle" style="display:none;margin-left: 5px;">
			<table class="table table-hover table-condensed">
				<tr>
					<th colspan="4"><center>OPD</center></th>
				</tr>
				<tr>
					<!--<th>UHID</th>-->
					<th>OPD ID</th>
					<th>Name</th>
					<th>Drugs</th>
				</tr>
			<?php
				$n=1;
				$qry=mysqli_query($link," SELECT * FROM `patient_medicine_detail` WHERE `type`='1' AND `status`='0' AND `date` BETWEEN '$date1' AND '$date2' GROUP BY `patient_id`,`pin` ");
				while($ipd_basket=mysqli_fetch_array($qry))
				{
					$pat_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info` WHERE `patient_id`='$ipd_basket[patient_id]' "));
					$all_drugs="";
					$drug_qry=mysqli_query($link," SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$ipd_basket[patient_id]' AND `pin`='$ipd_basket[pin]' AND `type`='$ipd_basket[type]' AND `status`='0' ");
					while($drugs=mysqli_fetch_array($drug_qry))
					{
						$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$drugs[item_code]'"));
						$all_drugs.=$m["item_name"].", ";
					}
				?>
					<tr style="cursor:pointer;" onClick="opd_medicine_process('<?php echo $ipd_basket["patient_id"]; ?>','<?php echo $ipd_basket["pin"]; ?>','<?php echo $ipd_basket["type"]; ?>',1)">
						<!--<td><?php echo $pat_info['patient_id']; ?></td>-->
						<td><?php echo $ipd_basket['pin']; ?></td>
						<td><?php echo $pat_info['name']; ?></td>
						<td><?php echo $all_drugs; ?></td>
					</tr>
				<?php
					$n++;
				}
			?>
			</table>
		</div>
	</div>
</div>
<script>
	function opd_medicine_process(uhid,pin,type,ind_num)
	{
		bootbox.dialog({ message: "<b>Redirecting to Pharmacy Sale</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		setTimeout(function(){
			window.location="processing.php?param=20&uhid="+uhid+"&ipd="+pin+"&type="+type+"&ind_num="+ind_num;
		 }, 500);
	}
</script>
<style>
.custom_span5
{
	width:48%;
}
</style>
