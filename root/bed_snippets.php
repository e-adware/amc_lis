<?php
	//$oq=mysqli_query($link,"SELECT * FROM `consult_patient_payment_details` WHERE `date`='$date'");
	$bq=mysqli_query($link,"SELECT * FROM `bed_master`");
	$b_num=mysqli_num_rows($bq);
	$bed_occu=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `ipd_pat_bed_details`"));
	$bed_bloc=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `bed_master` WHERE `status`='1'"));
	$bed_avail=$b_num-$bed_occu-$bed_bloc;
	$space="&nbsp;&nbsp;&nbsp;";
?>
<div class="snip">
<div class="child">
	<button class="btn btn-info" disabled style="width:100%;">BED STATUS</button>
</div>
<div class="child_snip">
	<table class="table bed table-condensed table-report">
		<tr>
			<th colspan="5">Total Beds : <?php echo $b_num;?> <?php echo $space."Occupied : ".$bed_occu.$space." Blocked : ".$bed_bloc.$space." Available : ".$bed_avail;?></th>
		</tr>
		<tr>
			<th>Ward</th><th>Total Beds</th><th>Available</th><th>Occupied</th><th>Blocked</th>
		</tr>
	
		<?php
		$ward=mysqli_query($link,"select distinct ward_id,name from ward_master");
		while($w=mysqli_fetch_array($ward))
		{
			$tot_room=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from room_master where ward_id='$w[ward_id]'"));
			$tot_bed=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]'"));
			$tot_avail=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]' and bed_id not in(select bed_id from ipd_pat_bed_details)"));
			$tot_occ=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_pat_bed_details where bed_id in(select bed_id from bed_master where ward_id='$w[ward_id]')"));
			$tot_cls=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]' and status='1'"));
			$tot_temp=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_bed_details_temp where bed_id in(select bed_id from bed_master where ward_id='$w[ward_id]')"));
			
			$avail=$tot_avail[tot]-$tot_temp[tot]-$tot_cls[tot];
			
			?>
				<tr>
					<td><?php echo $w[name];?></td>
					<td><?php echo $tot_bed[tot];?></td>
					<td><?php echo $avail;?></td>
					<td><?php echo $tot_occ[tot];?></td>
					<td><?php echo $tot_cls[tot];?></td>
				</tr>
			
			<?php
		}
		?>
		</table>
		<span style="float:right;"><button type="button" class="btn btn-success btn-mini" onclick="print_bed_list(1)"><i class="icon-print"></i> Print </button></span> 
		<span style="float:right;"><button type="button" class="btn btn-success btn-mini" onclick="print_bed_list(2)"><i class="icon-print"></i> Bed Status </button></span>
</div>
</div>
<script>
	function print_bed_list(n)
	{
		if(n==2)
		{
			var url="pages/print_bed_list.php";
		}
		if(n==1)
		{
			var url="pages/print_bed_status.php";
		}
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
