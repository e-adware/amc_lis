<?php
session_start();
include'../../includes/connection.php';

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

$type=$_POST['type'];
$date=date('Y-m-d');

function convert_date($date)
{
$timestamp = strtotime($date); 
$new_date = date('d-m-Y', $timestamp);
return $new_date;
}

?>
 <table class="table table-striped table-bordered">
   
 <?php

if($type=="aa") ///For load purchase ordertemp item
	{
		$orderno=$_POST['orderno'];
		
		$qrpdct=mysqli_query($link,"select a.*,b.name from inv_indent_order_details_temp a,inv_indent_master b  where a.item_code=b.id and a.order_no='$orderno' order by b.name ");
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr>
             <td><?php echo $qrpdct1['order_no'];?></td>
             <td><?php echo $qrpdct1['item_id'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             <td><?php echo $qrpdct1['order_qnt'];?></td>
                   
           <td><a href="javascript:delete_data('<?php echo $qrpdct1['item_id'];?>','<?php echo $qrpdct1['order_no'];?>','<?php echo $qrpdct1['order_no'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
           
         </tr>	
         <?php	
		   ;}
	  }	
	  
 
///////////////////////////////////////////////////////////	  
elseif($type=="loadsubcatgry") ///For substore form
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from ph_category_master where ph_cate_name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from ph_category_master  order by ph_cate_name";
	 }
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ph_cate_id'];?>')" id="rad_test<?php echo $i;?>">
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ph_cate_id'];?></td>
             <td><?php echo $qrpdct1['ph_cate_name'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['ph_cate_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="icon-remove"></i></span></a></td>
         </tr>	
         <?php	
		   $i++;}
	  }


///////////////////////////////////////////////////////////	  
elseif($type=="ap_load_testwise") ///For test load
	{
		
	 ?>
	 <tr>
			<th>Test Id</th>
			<th>Name</th>
			<th>Rate</th>
			<th>Amount</th>
	
		</tr>
		<?php	
		
		
	 $val=$_POST['val'];
	 $docid=$_POST['docid'];
	 if($val)
	 {
		 $q="select testid,testname,rate from testmaster where  testname like '$val%'";
		 
	 }
	 else
	 {
	   	 $q="SELECT testid,testname,rate FROM `testmaster` where testname!='' order by testname";
	 }
	   
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		$vcper="";	
		$vcamt="";
		
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>','<?php echo $ph;?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
             <td><?php echo $qrpdct1['testname'];?></td>
             <td><?php echo $qrpdct1['rate'];?></td>
             

             <td><input type="text" style="width:40px" id="txtcomamount<?php echo $i;?>" value="<?php echo $vcamt;?>"  onkeyup="data_saved(<?php echo $i;?>,event)" onkeypress="numentry('txtcomamount<?php echo $i;?>')"></td>

         </tr>	
         <?php	
		   $i++;}
	  }



///////////////////////////////////////////////////////////	  
elseif($type=="ap_load_centre_testwise") ///For center test load
	{
		
	 ?>
	 <tr>
			<th>Test Id</th>
			<th>Name</th>
			<th>Rate</th>
			<th>Amount</th>
	
		</tr>
		<?php	
		
		
	 $val=$_POST['val'];
	 $centerid=$_POST['centerid'];
	 if($val)
	 {
		 $q="select testid,testname,rate from testmaster where testid in(select testid from testmaster_rate where centreno='$centerid') and  testname like '$val%'";
		 
	 }
	 else
	 {
	   	 $q="SELECT testid,testname,rate FROM `testmaster` where testid in(select testid from testmaster_rate where centreno='$centerid') and  testname!='' order by testname";
	 }
	   //echo $q;
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		$vcper="";	
		$vcamt="";
		$qcom=mysqli_fetch_array(mysqli_query($link,"select  rate from testmaster_rate where centreno='$centerid' and testid='$qrpdct1[testid]'"));	
		 if($qcom)
		 {
			 
		     $vcamt=$qcom['rate'];
		 }
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>','<?php echo $ph;?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
             <td><?php echo $qrpdct1['testname'];?></td>
             <td><?php echo $qrpdct1['rate'];?></td>
              <td><input type="text" style="width:40px" id="txtcomamount_cntre<?php echo $i;?>" value="<?php echo $vcamt;?>"  onkeyup="data_saved_centre(<?php echo $i;?>,event)" onkeypress="numentry('txtcomamount_cntre<?php echo $i;?>')"></td>
         </tr>	
         <?php	
		   $i++;}
	  }
	  //////////////////////////////////

    	  	  	    	  	 	  	    	  	  	     	      
?>
      
  </table>
