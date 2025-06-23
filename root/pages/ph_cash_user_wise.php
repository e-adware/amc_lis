<html>
   <head>
   	  <title>Cash User Wise</title>
      <style>
         @media print
         {
         .n_print{display:none}
         }	
		 @media screen
         {
         body {padding: 20px 0;}
         }	 
      </style>
     
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
   </head>
   <body>
      <form method="post">
         <?php
            include("../../includes/connection.php");
            ?>
         <div class="container">
			 <?php include('page_header_ph.php'); ?>
            <div class="n_print">
               
            <table class="table table-bordered table-condensed">
               <tr>
                  <td>Change Date:<input type="text" name="date" id="date" value="<?php if($_POST){echo $_POST[date];} else { echo date('Y-m-d');}?>"/></td>
                  <td>Select User:
                     <select name="user">
						<option value='0'>Select All</option>";
                     <?php
                        $user=mysqli_query($GLOBALS["___mysqli_ston"],"select distinct user from ph_payment_details where user!=''");
                        while($us=mysqli_fetch_array($user))
                        {
                        	if($_POST[user]==$us[user]){ $sel='Selected="selected"';} else { $sel='';}			
                        
                        	$name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select name from employee where emp_id='$us[user]'"));
                        	echo "<option value='$us[user]' $sel>$name[name]</option>";
                        }
                        ?>
                     </select>	
                  </td>
                  <td>
                     <input class="btn btn-default" type="submit" name="enter" value="Enter"/>
                  </td>
               </tr>
            </table>
            </div>
            <?php
               if($_POST)
               {
               $date=$_POST[date];
               $user=$_POST[user];
               
               $n=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select levelid,name from employee where emp_id='$user'"));
               ?>
            <table class="table table-bordered table-condensed">
               <tr>
                  <td style="font-size:13px">
                     <p>Payment Received Report (<?php echo $date;?>):</p>
                     <strong><p>User: <?php echo $n[name];?></p></strong>
                  </td>
                  <td valign="top" style="font-size:13px">
                     <?php
                        if($n[levelid]=="A005")
                        {
                        	$qry=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select sum(amount) as tot from ph_payment_details where entry_date='$date' and typeofpayment!='R'"));
                        	
                        }
                        else
                        {
                        	$qry=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select sum(amount) as tot from ph_payment_details where user='$user' and entry_date='$date'"));    
                        	
                        }
                        $r_tot=$qry[tot];
                        ?>
                     
                  </td>
            </table>
            <div class="n_print">
               <p class="text-center">Print Date:<?php echo date("Y-m-d");?></p>
               
            </div>
            <table class="table table-bordered table-condensed">
				<tr>
                     <th width="10%" style="font-size:13px">Bill No</th>
                     <th width="12%" style="font-size:13px">Time</th>
                     <th width="30%" style="font-size:13px">Patient Name</th>
                     <th width="18%" style="font-size:13px">Bill Amount</th>
                     <th width="11%" style="font-size:13px">Disc.</th>
                     <th width="29%" style="font-size:13px">Amount Received</th>
                  </tr>
               <tr>
                  <td colspan="6" style="font-weight:bold;font-size:13px">Advance Reciept </td>
               </tr>
              
                  
               <?php
                  if($n[levelid]=="A005")
                  {
                  	$q="select * from ph_payment_details where entry_date='$date' and Type_of_Payment='A' order by bill_no";
                  	
                  	$us=1;
                  }
                  else
                  {
                  	$q="select * from ph_payment_details where user='$user'  and entry_date='$date' and type_of_payment='A' order by bill_no";
                  	
                  	
                  	$us=0;
                  }
                  
                  $tot_rec=0;
                  $data=mysqli_query($GLOBALS["___mysqli_ston"],$q);
                  while($p=mysqli_fetch_array($data))
                  {
                  	//$ck=mysql_num_rows(mysqli_query($GLOBALS["___mysqli_ston"],"select * from credit_payment where reg_no='$p[reg_no]' and date='$date'"));
                  	//if($ck==0)
                  	//{
                  	$pname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select name from patient_info where patient_id='$p[patient_id]'"));
                  	$paid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_sell_master where bill_no='$p[bill_no]' and user='$user' and entry_date='$date'"));
                  	
                  	$uname1="";
                  	if($us==1)
                  	{
                  		$uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select name from employee where emp_id='$p[user]'"));
                  		$uname1=" - ".$uname[name];
                  	}
                  	$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_sell_master where bill_no='$p[bill_no]' and user='$user'"));
                  	echo "<tr style='font-size:13px'><td width=10%>$reg[bill_no]</td><td width=12%>$reg[time]</td><td width=30%>$reg[customer_name]</td><td width=18%>$paid[total_amt]</td><td width=11%>$paid[discount_amt]</td><td width=29%>$p[amount] $uname1</td></tr>";
                  	$tot_rec=$tot_rec+$p[amount];
                  	//}
                  }
                  
                  ?>
               <tr>
                  <td colspan="6" style="font-weight:bold;font-size:13px;text-align:right" >Total Amount Receive:<?php echo number_format($tot_rec,2); ?></td>
                  
               </tr>
               
               
               
               
               
               
               <tr>
                  <td colspan="6" style="font-weight:bold;font-size:13px">Balance Reciept </td>
               </tr>
               <?php
                  if($n[levelid]=="A005")
                  {
                  	$q1="select * from ph_payment_details where entry_date='$date' and type_of_payment='B' order by bill_no";
                  	
                  	$us1=1;
                  }
                  else
                  {
                  	$q1="select * from ph_payment_details where user='$user'  and entry_date='$date' and type_of_payment='B' order by bill_no";
                  	
                  	$us1=0;
                  }
                  $tot_bal=0;
                  
                  $data=mysqli_query($GLOBALS["___mysqli_ston"],$q1);
                  while($p=mysqli_fetch_array($data))
                  {
                  
                  	$pname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select name from patient_info where patient_id='$p[patient_id]'"));
                  	$paid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from patient_payment_details where patient_id='$p[patient_id]'"));
                  	
                  	$uname2="";
                  	if($us1==1)
                  	{
                  		$uname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select Name from Employee where ID='$p[user]'"));
                  		$uname2=" - ".$uname[Name];
                  	}
                  	$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_sell_master where bill_no='$p[bill_no]' and user='$user'"));
                  	
                  	echo "<tr style='font-size:13px'><td width=22%>$reg[bill_no]</td><td width=22%>$p[time]</td><td width=30%>$reg[customer_name]</td><td width=18%>$reg[total_amt]</td><td width=11%>$reg[discount_amt]</td><td width=29%>$p[amount] $uname2</td></tr>";
                  	$tot_bal=$tot_bal+$p[amount];
                  
                  
                  }
                  ?>
               <tr>
                  <td colspan="6"  style="font-weight:bold;font-size:13px;text-align:right">Total Balance Recieve (Cash): <?php echo $tot_bal;?></td>
               </tr>
               
               
               
               
           
             </table>
            
            <table class="table table-condensed table-bordered">
               <tr>
                  <th colspan="4" style="font-size:13px">Payment Refund</th>
               </tr>
               <tr>
                  <th style="font-size:13px">Bill No</th>
                  <th style="font-size:13px">Name</th>
                  <th style="font-size:13px">Item</th>
                  <th style="font-size:13px">Amount Refunded</th>
               </tr>
               <?php
                  $tot_ref=0;
                  $ref=mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_item_return_master where  user='$user' and return_date='$date'");
                 
                  while($ref_m=mysqli_fetch_array($ref))
                  {
                  	$bill=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from payment_detail where patient_id='$ref_m[patient_id]' and visit_no='$ref_m[visit_no]' limit 0,1"));
                  	$reg=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"],"select * from ph_sell_master where bill_no='$ref_m[bill_no]' "));
                  	
                  	$qrate=mysqli_fetch_array(mysqli_query($link,"select a.recpt_mrp,b.item_name from ph_purchase_receipt_details a,item_master b where a.item_code='$ref_m[item_code]' and a.recept_batch='$ref_m[batch_no]' and a.item_code=b.item_id"));
                  	$vrtrnamt1=$ref_m['return_qnt']*$qrate['recpt_mrp'];
                  	$vrtrnamt=$vrtrnamt+$vrtrnamt1;
                  	
                  	echo "<tr style='font-size:13px'><td>$ref_m[bill_no]</td><td>$reg[customer_name]</td><td>$qrate[item_name]</td><td>$vrtrnamt1</td></tr>";
                  	
                  }
                  
                  $vttcashinhand=$r_tot-$vrtrnamt;
                  ?>
                <tr>
                  <td colspan="4"  style="font-weight:bold;font-size:13px;text-align:right">Total Refund : <?php echo number_format($vrtrnamt,2);?></td>
               </tr>
            </table>
            
            
             <table class="table table-condensed table-bordered">
               
                <tr>
                  <td colspan="4"  style="font-weight:bold;font-size:13px;text-align:right">Total Receipt : <?php echo number_format($r_tot,2);?></td>
               </tr>
                <tr>
                  <td colspan="4"  style="font-weight:bold;font-size:13px;text-align:right">Total Refund : <?php echo number_format($vrtrnamt,2);?></td>
               </tr>
               <tr>
                  <td colspan="4"  style="font-weight:bold;font-size:13px;text-align:right">Total Cash in Hand : <?php echo number_format($vttcashinhand,2);?></td>
               </tr>
            </table>
           
            <?php
               }
               
               ?>
      </form>
      </div>
   </body>
</html>
