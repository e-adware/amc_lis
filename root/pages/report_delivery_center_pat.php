<?php
include("../../includes/connection.php");
$user=$_POST[user];
$date=date("Y-m-d");


function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-Y', $timestamp);
	return $new_date;
}
?>


	<?php
		$i=1;
		
				
		if($_POST[type]=="date")
		{
			$fdate=$_POST[fdate];
			$tdate=$_POST[tdate];
			
			$q="select distinct a.patient_id,a.opd_id,b.batch_no,a.date from uhid_and_opdid a,patient_test_details b where a.patient_id=b.patient_id and (a.opd_id=b.opd_id or a.opd_id=b.ipd_id) and a.date between '$fdate' and '$tdate'";	
			
		}
		else if($_POST[type]=="reg" && $_POST[reg]!='')
		{
			$reg=$_POST[reg];
			
			$q="select distinct a.patient_id,a.opd_id,b.batch_no,a.date from uhid_and_opdid a,patient_test_details b where a.patient_id=b.patient_id and (a.opd_id=b.opd_id or a.opd_id=b.ipd_id) and b.opd_id like '$reg%'";	
			
		}
		else if($_POST[type]=="name")
		{
			$name=$_POST[name];
			
			$q="select a.patient_id,a.opd_id,b.batch_no,a.date from uhid_and_opdid a,patient_test_details b,patient_info c where a.patient_id=b.patient_id and b.patient_id=c.patient_id and c.name like '%$name%'";	
			
		}
		else
		{
			$q="select distinct a.patient_id,a.opd_id,b.batch_no,a.date from uhid_and_opdid a,patient_test_details b where a.patient_id=b.patient_id and (a.opd_id=b.opd_id or a.opd_id=b.ipd_id) and a.date='$date'";
		}
		
		
		?>
			
			<table class="table table-bordered table-condensed table-report table-white" id="pat">
			<tr>
				<td>#</td>
				<td>Date</td>
				<td>PIN</td>
				<td>Name</td>
				<td>Test</td>
				<td style="text-align:right">Approved by Doctor</td>
				<td>Printed</td>
				<td></td>
			</tr>
			
		
		
		<?php
		$qrtest=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		while($qrtest1=mysqli_fetch_array($qrtest))
		{
			$qtest=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select count(testid) as maxtst from patient_test_details where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]'"));
			 if($qtest['maxtst']>0)
			 {	
				 $pname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from patient_info where patient_id='$qrtest1[patient_id]'"));	
				 
				 $qsmpl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select count(testid) as maxsmplrcvd from phlebo_sample where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]'"));
				
			  $vttltecharpd=0;
			  $qtechrcvdtstrslt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select  count(Distinct testid) as maxtchrcvtstrslt from testresults where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and 	main_tech>0 "));		
			  $qtechrcvdtstsmry=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select count(Distinct testid) as maxtchrcvtstsmry from patient_test_summary where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and 	main_tech>0 "));		 
			  $qtechrcvdtstwidal=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_histo_summary where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and tech>0 "));		 
				
				
				  if($qtechrcvdtstwidal)
				  {
					$maxtchrcvtstwidal='1';  
				  }
				  else
				  {
					  $maxtchrcvtstwidal='0';
				  }
				 $vttltecharpd=$qtechrcvdtstrslt['maxtchrcvtstrslt']+$qtechrcvdtstrslt['maxtchrcvtstsmry']+$maxtchrcvtstwidal; 
				
				$vttltdocarpd=0;
			  $qdocrcvdtstrslt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select count(Distinct testid) as maxdocrcvtstrslt from testresults where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and doc>0 "));		
			  $qdocrcvdtstsmry=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select count(Distinct testid) as maxdocrcvtstsmry from patient_test_summary where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and doc>0 "));		 
			  $qdocrcvdtstwidal=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_histo_summary where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and	doc>0 "));		 
				
				if($qdocrcvdtstwidal)
				  {
					$maxdocrcvtstwidal='1';  
				  }
				  else
				  {
					  $maxdocrcvtstwidal='0';
				  } 
				$vttltdocarpd=$qdocrcvdtstrslt['maxdocrcvtstrslt']+$qdocrcvdtstsmry['maxdocrcvtstsmry']+$maxdocrcvtstwidal; 
				
				$qtestprint=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select count(testid) as maxtstprnt from testreport_print where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and testid in(select testid from testmaster where category_id='1') "));
				
				if($qtest['maxtst']==$vttltecharpd)
				{
					if($qsmpl['maxsmplrcvd']==$vttltdocarpd)
					{
						$vclr="green";
					}
					else
					{
						$vclr="yellow";
					}
				 }	
				 else
				 {
				   $vclr="yellow";
				  }
				  
				 // $reg=mysqli_fetch_array(mysqli_query($link,"select * from patient_reg_details where patient_id='$qrtest1[patient_id]' and visit_no='$qrtest1[visit_no]'"));
				  
				  $print_rep=mysqli_num_rows(mysqli_query($link,"select distinct testid from testreport_print where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]'"));
			?> 
			
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo convert_date($qrtest1['date']);?></td>
				<td>
					<?php echo $qrtest1[opd_id];?>
					<input type="hidden" value="<?php echo $qrtest1[patient_id];?>" id="pid<?php echo $i;?>"/>
					<input type="hidden" value="<?php echo $qrtest1[opd_id];?>" id="opd<?php echo $i;?>"/>
					<input type="hidden" value="<?php echo $qrtest1[batch_no];?>" id="batch<?php echo $i;?>"/>
				</td>
				<td><?php echo $pname['name'];?></td>
				<td><b><i>
					<?php
					$path=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and testid in(select testid from testmaster where category_id='1')"));
					if($path>0)
					{
						echo "Pathology : ". $path."<br/>";
					} 
					
					$rad=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and testid in(select testid from testmaster where category_id='2')"));
					if($rad>0)
					{
						echo "Radiology : ". $rad."<br/>";
					}
					
					$card=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where patient_id='$qrtest1[patient_id]' and (opd_id='$qrtest1[opd_id]' or ipd_id='$qrtest1[ipd_id]') and batch_no='$qrtest1[batch_no]' and testid in(select testid from testmaster where category_id='3')"));
					if($card>0)
					{
						echo "Cardiology : ". $card;
					}
					?>
				</i></b></td>
				<td  style="text-align:right"> <?php echo $vttltdocarpd;?> </td>
				
				<td id="upl<?php echo $i;?>"><?php echo $print_rep;?></td>
				
				<td><input type="button" class="btn btn-info" id="view_<?php echo $i;?>" value="View" onclick="load_pat_data(<?php echo $i;?>)"/></td>
		</tr>
		   <?php
		   $i++;
			  }
	 
		
		
		}
		?>
	
</table>
