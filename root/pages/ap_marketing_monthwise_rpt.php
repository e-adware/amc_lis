<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<style>
input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	/*font-size:15px; font-family: "Courier New", Courier, monospace; line-height: 18px;*/
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
.line td{border-top:1px dotted;}

#dly_rcp tr td{ font-size:12px;}
</style>

</head>
<body>
<?php
include'../../includes/connection.php';


$date1=$_GET['date1'];
$date2=$_GET['date2'];
$mkid=$_GET['mkid'];

//$date2 = $_GET['date2'];
	//$date2 = $_GET['date2'];
			function convert_date($date)
			{
			$timestamp = strtotime($date); 
			$new_date = date('d-m-y', $timestamp);
			return $new_date;
			}
			
			function convert_date1($date)
			{
			$timestamp = strtotime($date); 
			$new_date = date('m-d-y', $timestamp);
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


	///////Date difference in php
	
	 $vendat=strtotime($date1);
	 $vpaydat=strtotime($date2);	
	 $vda=$vpaydat-$vendat;
	 $vdifrnt=floor($vda/3600/24); 
	 	
	//////end///////////////
	////For Month convert/////////
	$vdt1=convert_date1($date1);
	$vdt2=convert_date1($date2);
    $date3 = mktime(0,0,0,$vdt1); // m d y, use 0 for day
    $date4 = mktime(0,0,0,$vdt2); // m d y, use 0 for day
    $vmnthdif=round(($date4-$date3) / 60 / 60 / 24 / 30);

  
  
$dmnth=substr($date1,5,2)  ;
$vy=substr($date1,0,4);
$vy1=substr($date2,0,4);

$monthNum  = $vmnthdif;
$dateObj   = DateTime::createFromFormat('!m', $monthNum);
$monthName = $dateObj->format('F'); // March
/////////////end///////////////////////
	 

?>
<div class="container">
			<div class="" style="">
				<?php // include('page_header_ph.php'); ?>
				<center><h5><u>Referral Doctor Daywise Summary</u></h5></center>
				<p>From: <?php echo convert_date($date1);?> To: <?php echo convert_date($date2);?></p>
			</div>



      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><a class="btn btn-success" href="ap_marketing_monthwise_excel_rpt.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&mkid=<?php echo $mkid;?>">Export to Excel</a><input type="button" name="button" id="button" class="btn btn-success" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
        
      </tr>
      </table>
         <table width="100%" id="dly_rcp" border='1'>
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:12px">#</td>
				<td style="font-weight:bold;font-size:12px">Doctor</td>
				<?php
					for($i=0;$i<=$vmnthdif;$i++)
	                 {
		              $dateObj   = DateTime::createFromFormat('!m', $dmnth+$i);
                      $monthName = $dateObj->format('M'); // March  M for 3 letter month and F for full name
		                             
					?>
					  <td align="right"><?php echo $monthName;?> </td>
					
					<?php
					}?>
					
				
				<td style="font-weight:bold;font-size:12px" align="right">Total </td>
				
				
           </tr>
              <?php
				if($mkid==0)
				{
				$qmark=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT `emp_id`, `name` FROM `employee` WHERE `levelid`='31' order by name");
				}
				else
				{
				$qmark=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT `emp_id`, `name` FROM `employee` WHERE `emp_id`='$mkid' ");
									  
				}
				while($qmark1=mysqli_fetch_array($qmark))
				{
				$mrttl=0;	
				
             ?>
                <tr class="line">
					
                  <td colspan="<?php echo $vdifrnt;?>" style="font-size:13px;font-weight:bold">Executive Name :  <?php echo $qmark1['name'];?></td>
             </tr>  
            
        
			 <?php
			    $vttl=0;
			   $j=1;
			   
			   $qdoc=mysqli_query($GLOBALS["___mysqli_ston"], "select distinct a.refbydoctorid,a.ref_name from refbydoctor_master a,marketing_master b,uhid_and_opdid c where a.refbydoctorid=b.refbydoctorid and a.refbydoctorid=c.refbydoctorid and c.date between '$date1' and'$date2'  and b.emp_id='$qmark1[emp_id]'  order by a.ref_name");
			   
			   while($qrdate1=mysqli_fetch_array($qdoc)){
				
				$vttlamt=0;	
								
						                 
		       ?>
             <tr class="line">
				<td ><?php echo $j;?></td>	
				<td > <?php echo $qrdate1['ref_name'];?></td>
					<?php
					for($i=0;$i<=$vmnthdif;$i++)
					{
						$vmid=$dmnth+$i;

						$mnid = DateTime::createFromFormat('!m', $dmnth+$i);
						$monthName = $mnid->format('M'); // March


						$ts = strtotime("$monthName y");
						$dt=date('t', $ts);

						$v1stday=date("$vy/$vmid/01");//for firstday of the month
						$vlstdy=date("$vy/$vmid/$dt");//for last day of the month
						 
					 //$qpatho=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ifnull(sum(rate),0) as maxpatho from patient_details where refbydoctorid='$qrdate1[refbydoctorid]' and date between'$v1stday' and '$vlstdy' and rate>0  "));  
					 $qpatho=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ifnull(sum(a.test_rate),0) as maxpatho from patient_test_details a,uhid_and_opdid b where b.refbydoctorid='$qrdate1[refbydoctorid]' and a.date between'$v1stday' and '$vlstdy'  and a.patient_id=b.patient_id and a.opd_id=b.opd_id "));  
					           
					 $vttlamt=$vttlamt+$qpatho['maxpatho'];
					 $mrttl=$mrttl+$qpatho['maxpatho'];
					 $vgrndttlamt=$vgrndttlamt+$qpatho['maxpatho'];
					?>
					 <td align="right"><?php echo number_format($qpatho['maxpatho'],2);?> </td>

					<?php
					}?>
				
				  <td align="right"><?php echo number_format($vttlamt,2);?></td>
				
             </tr>  
             
             
               <?php
			 $j++;}?>
			 
			  <tr>
				   <td colspan="<?php echo $vdifrnt;?>" style="font-weight:bold;font-size:12px;text-align:right">Total : <?php echo number_format($mrttl,2);?></td>
			  </tr>
				
			 
			 <?php
		 }?>
             
<!--
				<tr class="line">
					<td colspan="2" style="font-weight:bold; text-align:right">Grand Total Case : </td>
					<td style="font-weight:bold" align="right"><?php echo number_format($vttllab,2);?></td>
					<td style="font-weight:bold" align="right"><?php echo number_format($vttlradio,2);?></td>
					<td style="font-weight:bold" align="right"><?php echo number_format($vttlsrvice,2);?></td>
					<td style="font-weight:bold" align="right"><?php echo number_format($refcase,2);?></td>
					<td style="font-weight:bold" align="right"><?php echo number_format($vttlpatient,0);?></td>
				</tr>
-->
		
</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

