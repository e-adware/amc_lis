<html>
	<head>
		<title></title>
		<style>
			input[type="text"]
			{
			border:none;
			}
			.line td{border-top:1px solid;}
		</style>
		<link href="../../css/bootstrap.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/bootstrap-theme.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<?php
			include'../../includes/connection.php';
			
			$date1=$_GET['date1'];
			$date2=$_GET['date2'];
			$docid=$_GET['docid'];
			$vdocdisprcnt=$_GET['vdocdisprcnt'];
		   
			$vdisamt=$_GET['vdisamt'];
			
			///for convert to month name
			$abc=substr($date2,5,2);
			$mnthnm=date('M', mktime(0, 0, 0, $abc, 10)); // March
			
			//////////////////////
			
			
			//$date2 = $_GET['date2'];
			function convert_date($date)
			{
			$timestamp = strtotime($date); 
			$new_date = date('d-m-y', $timestamp);
			return $new_date;
			}
			
			function convert_date_change($date)
			{
			$timestamp = strtotime($date); 
			$new_date = date('Y-m-d', $timestamp);
			return $new_date;
			}
			
			function val_con($val)
			{
				$nval=explode(".",$val);
				if(!$nval[1])
				{
					return $val.".00";	
				}
				else
				{
					if(!$nval[1][1])
					{
						return $val."0";	
					}
					else
					{
						return $val;	
					}
				}
			}
			
			
			
			
			////////Check Discount////
			$q1=mysqli_query($link,"select distinct  bill_no from patient_details where date between '$date1' and '$date2' and discount>0 and dis_per=0");
			while($q2=mysqli_fetch_array($q1))
			{
				$dispatient=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(discount),0) as disamt,ifnull(sum(rate),0) as tot_amount from  patient_details  where bill_no='$q2[bill_no]' and date between '$date1' and'$date2'   "));	
				$vdisprchnt=round(($dispatient['disamt']/$dispatient['tot_amount'])*100);
				mysqli_query($link,"update patient_details set dis_per='$vdisprchnt' where bill_no='$q2[bill_no]' and date between '$date1' and'$date2' ");
		    }
			////////end//////////
			
			
			
			?>
		
		<div style="text-align:center;">
         <h4>Referral Doctor Case Details</h4>
         <p>From: <?php echo convert_date($date1);?> To: <?php echo convert_date($date2);?></p>
		<?php
			?>
				<form name="form1" id="form1" method="post" >
							 <div class="no_print bottom-margin"><a class="btn btn-success" href="ref_commision_excel_rpt.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&docid=<?php echo $docid;?>&vdocdisprcnt=<?php echo $vdocdisprcnt;?>&vdisamt=<?php echo $vdisamt;?>">Export to Excel</a> <input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
							<table class="table table-bordered table-condensed table-report" style="font-size:11px" width="100%">
								<?php
								  					  							  
								
								
									if($docid==0)
									{
									  
									  $qdoc=mysqli_query($link,"select distinct a.refbydoctorid,b.ref_name from uhid_and_opdid a,refbydoctor_master b  where a.date between '$date1' and'$date2' and a.center_no='C100' and a.refbydoctorid!='101'  and a.refbydoctorid=b.refbydoctorid and a.type in(2,10,11,12,13) order by b.ref_name");
									 
									}
									else
									{
									  									  
									  $qdoc=mysqli_query($link,"select distinct a.refbydoctorid,b.ref_name from uhid_and_opdid a,refbydoctor_master b  where a.date between '$date1' and'$date2' and a.center_no='C100' and a.refbydoctorid=b.refbydoctorid and a.refbydoctorid='$docid' and a.type in(2,10,11,12,13) order by b.ref_name");
									  
									}
									
									while($qdoc1=mysqli_fetch_array($qdoc)){
									$ttl=0;
									$netcomamt=0;
									$refcase=0;
									$refcase1=0;
									$vpathottl=0;
									$vradiottl=0;
									$vcardiottl=0;
									?>
								<tr>
									<td colspan="10" style="font-weight:bold"><?php echo $qdoc1['ref_name'];?> </td>
								</tr>
								<tr>
									<td colspan="10">From:<?php echo convert_date($date1);?> &nbsp; &nbsp; &nbsp;To :<?php echo convert_date($date2);?> </td>
								</tr>
								<tr bgcolor="#EAEAEA" class="bline">
									<td>Date </td>
									<td>Bill No</td>
									<td>Patient Name</td>
									<td align="right">Patho </td>
									<td align="right">Radio</td>
									<td align="right">Cardio</td>
									<td align="right">P. %</td>
									<td align="right">R. %</td>
									<td align="right">C. %</td>
									<td align="right">Amount</td>
								</tr>
								<?php
									$refcase=0;
									$i=1;
									
									$qpatient=mysqli_query($link,"select distinct opd_id from uhid_and_opdid  where date between '$date1' and'$date2' and refbydoctorid='$qdoc1[refbydoctorid]' and type in(2,10,11,12,13) order by date,slno");
																																								
									while($qpatient1=mysqli_fetch_array($qpatient)){
																										
									$dispatient=mysqli_fetch_array(mysqli_query($link,"select patient_id,dis_per,dis_amt,tot_amount,date from  invest_patient_payment_details  where opd_id='$qpatient1[opd_id]' and date between '$date1' and'$date2'  "));	
									
									$qpatientname=mysqli_fetch_array(mysqli_query($link,"select name from  patient_info  where patient_id='$dispatient[patient_id]'"));	
									
									//$vdisprchnt=round(($dispatient['disamt']/$dispatient['tot_amount'])*100);
									$vdisprchnt=$dispatient['dis_per'];
									$Pntdiscnt=$dispatient['dis_amt'];
									if($dispatient['dis_amt']==0)
									{
										$vdisprchnt=0;
									}	
									
									$q2=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select ifnull(Sum(a.test_rate),0)as maxspl From patient_test_details a,testmaster b where a.opd_id='$qpatient1[opd_id]'  and a.test_rate>0 and a.testid=b.testid and  b.category_id='1' and  b.type_id !='132' "));
									$pathoamount=$q2['maxspl'];
									
									$q3=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select ifnull(Sum(a.test_rate),0)as maxradio From patient_test_details a,testmaster b where a.opd_id='$qpatient1[opd_id]'  and a.test_rate>0 and a.testid=b.testid and  b.category_id='2' and  b.type_id !='132' "));
									$radio=$q3['maxradio'];
									
									$q4=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select ifnull(Sum(a.test_rate),0)as maxcardio From patient_test_details a,testmaster b where a.opd_id='$qpatient1[opd_id]'  and a.test_rate>0 and a.testid=b.testid and  b.category_id='3' and  b.type_id !='132' "));
									$cardio=$q4['maxcardio'];
									
									
									if ($pathoamount > $vdisprchnt) 
									{
									$pathoamount = ($pathoamount - $Pntdiscnt);
									}
									else If ($radio > $Pntdiscnt)
									{
									$radio = ($radio - $Pntdiscnt);
									}
									else If ($cardio > $Pntdiscnt)
									{
									$cardio = ($cardio - $Pntdiscnt);
									}
									
									$rnk=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select lab_per,radio_per,cardio_per from doctor_discount where refdoc_id='$qdoc1[refbydoctorid]'")); 
									
									$pathornk=$rnk['lab_per'];
									$radiornk=$rnk['radio_per'];
									$cardiornk=$rnk['cardio_per'];
									
									$Vpatho =($pathoamount*$pathornk) / 100;
									$Vradio =($radio*$radiornk) / 100;
									$Vcardio =($cardio*$cardiornk) / 100;
									
									$vpttl=0;
									$vpttl=$Vpatho+$Vradio+$Vcardio;
									$vpathottl=$vpathottl+$pathoamount;
									$vradiottl=$vradiottl+$radio;
									$vcardiottl=$vcardiottl+$cardio;
												
									$refcase1=$refcase1+$vpttl;
									$refcase=round($refcase1);
									$blno=$qpatient1['opd_id'];
																		
									$vdate=convert_date($dispatient['date']);
									$vdscnt=$qpatient1['discount']; 
									
									//$vdisprchnt=round(($qpatient1['discount']/$qpatient1['tot_amount'])*100);
									
									?>
								<tr>
									<td ><?php echo $vdate;?></td>
									<td ><?php echo $blno;?></td>
									<td ><?php echo $qpatientname['name'];?></td>
									<td align="right"><?php echo number_format($pathoamount,2);?></td>
									<td align="right"><?php echo number_format($radio,2);?></td>
									<td align="right"><?php echo number_format($cardio,2);?></td>
									<td align="right"><?php echo number_format($pathornk,0);?></td>
									<td align="right"><?php echo number_format($radiornk,0);?></td>
									<td align="right"><?php echo number_format($cardiornk,0);?></td>
									<td align="right"><?php echo number_format($vpttl,2);?></td>
									<!--<td style="font-weight:bold"><?php echo $refdoc['name'];?></td>-->
								</tr>
								
								<?php
									
									;}?>
								<tr style="font-size:11px" class="line" >
									<td colspan="3" align="right" style="font-weight:bold;">Total</td>
									<td align="right" style="font-weight:bold"><?php echo number_format($vpathottl,2);?></td>
									<td align="right" style="font-weight:bold"><?php echo number_format($vradiottl,2);?></td>
									<td align="right" style="font-weight:bold"><?php echo number_format($vcardiottl,2);?></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right" style="font-weight:bold"><?php echo number_format($refcase,2);?></td>
									
									
								</tr>
								
								
								<tr style="font-size:11px" class="line" >
									<td colspan="4" align="right" style="font-weight:bold;">Net Amount</td>
									<td align="right" style="font-weight:bold"><?php echo number_format($refcase,2);?></td>
									<td colspan="5">&nbsp;</td>
																	
								</tr>
								
								<tr>
									<td colspan="10" align="left" height="50px" ></td>
								</tr>
								
<!--
								<tr>
									<td colspan="7" align="left" >Received the letter for the month of  <?php echo $mnthnm;?></td>
								</tr>
								<tr>
									<td colspan="7" align="left" style="font-weight:bold"><?php echo $qdoc1['ref_name'];?></td>
								</tr>
								<tr>
									<td colspan="7" align="left" >Please do not accept if found any temperd</td>
								</tr>
								<tr>
									<td colspan="7" align="left" height="50px" ></td>
								</tr>
-->
								<?php
									;}?>
							</table>
				</form>
			</div>
	</body>
</html>
