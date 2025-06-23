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
$docid=$_GET['docid'];

$filename ="patientwise_".$date1."_".$date2."_".$docid.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

//$date2 = $_GET['date2'];
function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-y', $timestamp);
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
$splr=mysqli_fetch_array(mysqli_query($link,"select * from inv_supplier_master where  id='$splrid'  "));

$qrcv=mysqli_fetch_array(mysqli_query($link,"SELECT * from inv_main_stock_receied_master WHERE supplier_code='$splrid' and supplier_bill_no='$billno' "));  
$compny=mysqli_fetch_array(mysqli_query($link,"select * from company_name  "));

?>
<div class="container">
			<div class="" style="">
				<?php // include('page_header_ph.php'); ?>
				<center><h5><u>Referral Doctor Case Details</u></h5></center>
				<p>From: <?php echo convert_date($date1);?> To: <?php echo convert_date($date2);?></p>
			</div>



      <table width="100%">
      <tr>
        <td colspan="6" style="text-align:right"><div class="noprint"><a class="btn btn-success" href="ref_doc_case_apollo_excel_rpt.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&docid=<?php echo $docid;?>">Export to Excel</a><input type="button" name="button" id="button" class="btn btn-default" value="Print" onClick="javascript:window.print()" />&nbsp;<input type="button" name="button" id="button" class="btn btn-success" value="Exit" onClick="javascript:window.close()" /></div></td>
        
      </tr>
      </table>
         <table width="100%" id="dly_rcp">
            <tr bgcolor="#EAEAEA" class="bline" >
				<td style="font-weight:bold;font-size:13px">Sl No</td>
				<td style="font-weight:bold;font-size:13px">Date</td>
				<td style="font-weight:bold;font-size:13px">Reg.No</td>
				<td style="font-weight:bold;font-size:13px">Patient Name</td>
				<td style="font-weight:bold;font-size:13px;text-align:right;">Total</td>
				<td style="font-weight:bold;font-size:13px;text-align:right;">Discount</td>
				<td style="font-weight:bold;font-size:13px;text-align:right;">Refund</td>
				<td style="font-weight:bold;font-size:13px;text-align:right;">Paid</td>
				<td style="font-weight:bold;font-size:13px;text-align:right;">Balance</td>
				<td style="font-weight:bold;font-size:13px;text-align:right;" >Center</td>
				
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
				$executivecase=0;	
             ?>
                <tr class="line">
					
                  <td colspan="10" style="font-size:13px;font-weight:bold">Executive Name :  <?php echo $qmark1['name'];?></td>
             </tr>  
        
			 <?php
			    $vttl=0;
			    $i=1;
				
				
				if($docid==0)
				{
				   $qdoc=mysqli_query($GLOBALS["___mysqli_ston"], "select distinct a.refbydoctorid,a.ref_name from refbydoctor_master a,marketing_master b,uhid_and_opdid c where a.refbydoctorid=b.refbydoctorid and a.refbydoctorid=c.refbydoctorid and c.date between '$date1' and'$date2'  and b.emp_id='$qmark1[emp_id]'  order by a.ref_name");
				}
				else
				{
					$qdoc=mysqli_query($GLOBALS["___mysqli_ston"], "select refbydoctorid,ref_name from refbydoctor_master where refbydoctorid='$docid' ");
				}						
				while($qrdate1=mysqli_fetch_array($qdoc)){
					
				$vdocttl=0;	$vdocdis=0;	$vdocrfnd=0;$vdocpaid=0;$vdocbalnce=0;		                  
		       ?>
             <tr class="line">
					
                  <td colspan="10" style="font-size:13px;font-weight:bold"><i>Ref Doctor Name : <?php echo $qrdate1['ref_name'];?></i></td>
             </tr>  
             
             <?php
                       
						$i=1;
						$qpatient=mysqli_query($link, "select distinct opd_id from  uhid_and_opdid where date between '$date1' and'$date2' and refbydoctorid='$qrdate1[refbydoctorid]' and `type` in(SELECT `p_type_id` FROM `patient_type_master` WHERE `type`=2) order by slno ");
						
												
						while($qpatient1=mysqli_fetch_array($qpatient)){
											
						$refdoc=mysqli_fetch_array(mysqli_query($link, "select a.patient_id,a.date,b.centrename from uhid_and_opdid a,centremaster b where a.opd_id='$qpatient1[opd_id]' and a.`center_no`=b. centreno "));
						$qpname=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id`='$refdoc[patient_id]'"));
						
						$qpayment=mysqli_fetch_array(mysqli_query($link, "SELECT `tot_amount`,`dis_amt`,`advance`,`refund_amount`,`balance` FROM `invest_patient_payment_details` WHERE `opd_id`='$qpatient1[opd_id]'"));
						
						$vdate=convert_date($refdoc['date']);
                        
                        $vdocttl+=$qpayment['tot_amount'];
                        $vdocdis+=$qpayment['dis_amt'];
                        $vdocrfnd+=$qpayment['refund_amount'];
                        $vdocpaid+=$qpayment['advance'];
                        $vdocbalnce+=$qpayment['balance'];
                        
                        $vttl+=$qpayment['tot_amount'];
                        $vdis+=$qpayment['dis_amt'];
                        $vrfnd+=$qpayment['refund_amount'];
                        $vpaid+=$qpayment['advance'];
                        $vbalnce+=$qpayment['balance'];
               ?>
				  <tr>
						<td ><?php echo $i;?></td>
						<td ><?php echo $vdate;?></td>
						<td ><?php echo $qpatient1['opd_id'];?></td>
						<td ><?php echo $qpname['name'];?></td>
						<td style="text-align:right;"><?php echo $qpayment['tot_amount'];?></td>
						<td style="text-align:right;"><?php echo $qpayment['dis_amt'];?></td>
						<td style="text-align:right;"><?php echo $qpayment['refund_amount'];?></td>
						<td style="text-align:right;"><?php echo $qpayment['advance'];?></td>
						<td style="text-align:right;"><?php echo $qpayment['balance'];?></td>
						<td style="text-align:right;"><?php echo $refdoc['centrename'];?></td>
				  </tr>
				  
				 
			  
			  
              
                <?php
                   $i++;}?>
             
				<tr class="line">
				   <td colspan="4" style="font-weight:bold; text-align:right">Doctor's Total : </td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vdocttl,2);?></td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vdocdis,2);?></td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vdocrfnd,2);?></td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vdocpaid,2);?></td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vdocbalnce,2);?></td>
				   <td>&nbsp;</td>
				</tr>
					
                 <?php
			 }?>
			 
			 <tr class="line">
				   <td colspan="4" style="font-weight:bold; text-align:right">Executive Total : </td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vttl,2);?></td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vdis,2);?></td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vrfnd,2);?></td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vpaid,2);?></td>
				   <td style="font-weight:bold" align="right"><?php echo number_format($vbalnce,2);?></td>
				   <td>&nbsp;</td>
				</tr>
			 
			 <?php
		 }?>
                    
           
             
      
      
</table>  
       
    
  
   </form>
 
 </div>  
</body>
</html>

