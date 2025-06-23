<html>
<head>
<title></title>

<style>
input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	/*font-size:15px; font-family: "Courier New", Courier, monospace; line-height: 18px; */
}
@media print{
 .noprint{
	 display:none;
 }
 td{font-size:10px}
 th{font-weight:bold;background-color:#CCCCCC;font-size:13px}

}
#last td{ border-top:2px dotted}
</style>
      <link href="../../css/bootstrap.css" type="text/css" rel="stylesheet"/>
      <link href="../../css/bootstrap-theme.css" type="text/css" rel="stylesheet"/>
      <link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body>

<?php
include'../../includes/connection.php';
//$date1 = $_GET['date1'];
$fdate=$_GET['fdate'];
$tdate=$_GET['tdate'];
$centr=$_GET['centr'];
$cmpny=mysql_fetch_array(mysql_query("select name from company where id='$centr'"));
//$date2 = $_GET['date2'];

?>

<!--<a href="all_center_excel_rpt.php?date1=<?php echo $fdate;?>&date2=<?php echo $tdate;?>">Export to Excel</a>-->
<span style="position:absolute;top:0px; right:0px">Print Date :<?php echo date('Y-m-d');?></span>
<table>

<tr><td style="font-weight:bold;font-size:14px">From : <?php echo $fdate;?>&nbsp;To : <?php echo $tdate;?></td></tr>
</table>
<?php

?>
<div class="container">
<div align="center">
  <form name="form1" id="form1" method="post" action="">
   <div class="row">
      <div class="col-md-12">
        <table width="100%">
      <tr>
       <div class="no_print bottom-margin"><a class="btn btn-success" href="all_center_excel_rpt.php?date1=<?php echo $fdate;?>&date2=<?php echo $tdate;?>">Export to Excel</a> <input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
      </tr>
      </table>
         <table class="table table-bordered table-condensed table-report">	
            <tr bgcolor="#EAEAEA">
                <td width="60">Date</td>
                <td width="80">P.Id</td>
                <td width="150">Name</td>
                 <td width="150">Ref.Doc</td>
                <td width="100" align="right">Total.Amt</td>
                <td width="96" align="right">Advace</td>
                <td width="87" align="right">Disc</td>
                <td width="90" align="right">Balance</td>
                <td width="90" align="right">User</td>
            </tr>
         <?php
          
            $qcenter=mysqli_query($link,"select distinct a.centreno,b.centrename from patient_details a,centremaster b where a.centreno=b.centreno and a.date between'$fdate' and'$tdate' order by b.centrename");
            while($qcenter1=mysqli_fetch_array($qcenter)){
         ?>
           <tr>
			    <th colspan="9" style="font-weight:bold;background-color:#CCCCCC;font-size:17px">Center Name : <?php echo $qcenter1['centrename'];?></th>
           </tr>
         <?php
			$tot_am=0;
			$tot_adv=0;
			$tot_disc=0;
			$tot_bal=0;
		   $qrslct=mysqli_query($link,"select a.*,b.reg_no,b.user,c.name from patient_details a,patient_reg_details b,patient_info c where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between'$fdate' and'$tdate' and a.centreno='$qcenter1[centreno]' and a.patient_id=c.patient_id order by a.date, b.reg_no ");
		   
		   while($qrslct1=mysqli_fetch_array($qrslct)){
			   
		   $qrch=mysqli_fetch_array(mysqli_query($link,"select * from  patient_payment_details where patient_id='$qrslct1[patient_id]' and  visit_no='$qrslct1[visit_no]'"));
		   
		   $qrdoc=mysqli_fetch_array(mysqli_query($link,"select ref_name from  refbydoctor_master where refbydoctorid='$qrslct1[refbydoctorid]'"));
		   $quser=mysqli_fetch_array(mysqli_query($link,"select Name from  Employee where ID='$qrslct1[user]'"));
		   	$ndate=explode("-",$qrslct1[date]);
                         
			$ndate1=$ndate[2]."-".$ndate[1]."-".$ndate[0][2].$ndate[0][3];
                      /*  $vtot=explode(".",$qrch['tot_amount']);
                        $vadv=explode(".",$qrch['advance']);
                        $vdst=explode(".",$qrch['discount']);
                        $vbl=explode(".",$qrch['balance']); */
                        $reg=explode("/",$qrslct1['reg_no']);
                        
            $tot_am=$tot_am+$qrch['tot_amount'];
			$tot_adv=$tot_adv+$qrch['advance'];
			$tot_disc=$tot_disc+$qrch['discount'];
			$tot_bal=$tot_bal+ $qrch['balance']; 
			
			$vttlamt=$vttlamt+$qrch['tot_amount'];
			$vttladv=$vttladv+$qrch['advance'];
			$vttldis=$vttldis+$qrch['discount'];
			$vttlbal=$vttlbal+$qrch['balance'];          
		 ?>
              <tr>
                    <td><?php echo $ndate1;?></td>
                    <td width="80"><?php echo $qrslct1['reg_no'];?></td>
                    <td><?php echo substr($qrslct1['name'],0,15);?></td>
                    <td width="110"><?php echo $qrdoc['ref_name'];?></td>
                    <td align="right"><?php echo $qrch['tot_amount'];?></td>
                    <td  align="right"><?php echo $qrch['advance'];?></td>
                    <td align="right"><?php echo $qrch['discount'];?></td>
                    <td align="right"><?php echo $qrch['balance'];?></td>
                    <td align="right"><?php echo $quser['Name'];?></td>
              </tr> 
              <?php
			     

			  ;}
			  ?>
         

<tr >
<td colspan="4" align="right"><b>Total: </b></td>
<td align="right"><b><?php echo number_format($tot_am,2);?></b></td>
<td align="right"><b><?php echo number_format($tot_adv,2);?></b></td>
<td align="right"><b><?php echo number_format($tot_disc,2);?></b></td>
<td  align="right"><b><?php echo number_format($tot_bal,2);?></b></td>
<td>&nbsp;</td>
</tr>

  <?php
 }
    ?>
    
 <tr >
<td colspan="4" align="right"><b>Grand Total: </b></td>
<td align="right"><b><?php echo number_format($vttlamt,2);?></b></td>
<td align="right"><b><?php echo number_format($vttladv,2);?></b></td>
<td align="right"><b><?php echo number_format($vttldis,2);?></b></td>
<td  align="right"><b><?php echo number_format($vttlbal,2);?></b></td>
<td>&nbsp;</td>
</tr>   
    
</table>           
      </div>
   </div>  
   </form>
  </div>
 </div>  
</body>
</html>
