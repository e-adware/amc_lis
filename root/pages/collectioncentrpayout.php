<!DOCTYPE html>
<html>
	<head>
		<title>Center Payout</title>
		<link href="../../css/bootstrap.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<?php
			include'../../includes/connection.php';
			//$date1 = $_GET['date1'];
			$fdate=$_GET['fdate'];
			$tdate=$_GET['tdate'];
			$centr=$_GET['centr'];
			$cenname=$_GET['cenname'];
			
			
			$qrtds=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select tds from centremaster where centreno='$centr'"));
			?>
            <div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="text-center">
						<h3>Commissions</h3>
						<p><strong>Centre ID: <?php echo $centr;?> | Centre Name: <?php echo $cenname;?></strong></p>
						<p>From: <?php echo $fdate;?> To: <?php echo $tdate;?> </p>
						<div class="no_print bottom-margin"><a class="btn btn-success" href="center_payout_excel_rpt.php?fdate=<?php echo $fdate;?>&tdate=<?php echo $tdate;?>&centr=<?php echo $centr;?>">Export to Excel</a> <input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
					</div>
            
		<?php
			?>
		<form name="form1" id="form1" method="post" action="">			
			<table class="table table-bordered table-condensed table-report">
				<tr>
					<td>Entry Date</td>
					<td>Patient ID</td>
					<td>Name</td>
					<td>Ref By</td>
					<td class="text-right">Pathology </td>
					<td class="text-right">Special </td>
					<td class="text-right">USG </td>
					<td class="text-right">Xray </td>
					<td class="text-right">Cardio </td>
					<td class="text-right">P % </td>
					<td class="text-right">S % </td>
					<td class="text-right">U % </td>
					<td class="text-right">X % </td>
					<td class="text-right">C % </td>
					<td class="text-right">Total </td>
				</tr>
				<?php
					$vnet=0;
					$vpatho1=0;
					$vradio1=0;
					$vp1=0;
					$vr1=0;
					$vnet2=0;
					$qrtest=mysqli_query($GLOBALS["___mysqli_ston"], "select * from dummycomdetails_center  order by date1,reg_no");
					
					while($qrtest1=mysqli_fetch_array($qrtest)) {
					$vp=0;
					
					$vr=0;
					
					$vpatho=$qrtest1['patho'];
					$vradio=$qrtest1['spl_head']; 
					$vp=$qrtest1['patho_amt'];
					$vr=$qrtest1['spl_amt'];
					
					$vp1=$vp1+$vp;
					$vr1=$vr1+$vr;
					$vttl=0;
					
					$vttl=$qrtest1['patho_amt']+$qrtest1['spl_amt'];
					$vttl1=$vttl1+$vttl;
					
					$pathottl=$pathottl+$qrtest1['patho'];
					$splttl=$splttl+$qrtest1['spl_head'];
					$usgttl=$usgttl+$qrtest1['ultra'];
					$xryttl=$xryttl+$qrtest1['xray'];
					$crdttl=$crdttl+$qrtest1['cardio'];
					
					$vnet=round($vttl1);
					
					
					
					$qrname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select a.patient_id,a.visit_no,a.centreno,b.name,c.short_name from patient_details a,patient_info b,centremaster c,patient_reg_details d where a.patient_id=b.patient_id and a.centreno=c.centreno  and a.patient_id=d.patient_id and a.visit_no=d.visit_no  and d.reg_no='$qrtest1[reg_no]'"));
					$qdoc=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ref_name from refbydoctor_master where refbydoctorid='$qrtest1[refbydoctorid]'"));
					?> 
				<tr>
					<td ><?php echo $qrtest1['date1'];?></td>
					<td><?php echo $qrtest1['reg_no'];?></td>
					<td> <?php echo $qrname['name'];?></td>
					<td> <?php echo $qdoc['ref_name'];?></td>
					<td class="text-right"><?php echo $qrtest1['patho'];?></td>
					<td class="text-right"><?php echo $qrtest1['spl_head'];?></td>
					<td class="text-right"><?php echo $qrtest1['ultra'];?></td>
					<td class="text-right"><?php echo $qrtest1['xray'];?></td>
					<td class="text-right"><?php echo $qrtest1['cardio'];?></td>
					<td class="text-right"><?php echo number_format($qrtest1['patho_rank'],0);?></td>
					<td class="text-right"><?php echo number_format($qrtest1['spl_rank'],0);?></td>
					<td class="text-right"><?php echo number_format($qrtest1['ultra_rank'],0);?></td>
					<td class="text-right"><?php echo number_format($qrtest1['xray_rank'],0);?></td>
					<td class="text-right"><?php echo number_format($qrtest1['cardio_rank'],0);?></td>
					<td class="text-right"><?php echo number_format($vttl,2);?></td>
				</tr>
				<?php
					; }
					?>
				<tr>
					
					<td colspan="4" class="text-right"><strong>Total</strong></td>
					<td class="text-right"><strong><?php echo $pathottl.'.00';?></strong></td>
					<td class="text-right"><strong><?php echo $splttl.'.00';?></strong></td>
					<td class="text-right"><strong><?php echo $usgttl.'.00';?></strong></td>
					<td class="text-right"><strong><?php echo $xryttl.'.00';?></strong></td>
					<td class="text-right"><strong><?php echo $crdttl.'.00';?></strong></td>
					<td class="text-right"></td>
					<td class="text-right"></td>
					<td class="text-right"></td>
					<td class="text-right"></td>
					<td class="text-right"></td>
					<td class="text-right"><strong><?php echo $vttl1.'.00';?></strong></td>
				</tr>
				
				
				
				<tr>
					<td colspan="4" class="text-right"><strong>Net Amount</strong></td>
					<td colspan="2" class="text-right"><strong><?php echo number_format($vnet,2);?></strong></td>
					<td colspan="2"></td>
				</tr>				
			</table>
            <br>
            <p class="text-right"><strong>Manager</strong></p>
		</form>
		</div></div>
		</div>  
	</body>
</html>
