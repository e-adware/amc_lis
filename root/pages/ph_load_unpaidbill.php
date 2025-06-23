<?php
include'../../includes/connection.php';
$type=$_POST['type'];
?>

<div class="span11" style="margin-left:0px;padding:10px">
 <table class="table table-striped table-bordered"  >
   
    <?php
	 if($type=="itemtype") ///for  Type Master
	 {
	  $slct=mysqli_query($link,"select * from  mattypemaster order by type_name ");
	  while($slct1=mysqli_fetch_array($slct)){
	?>
		<tr>

			<td><?php echo $slct1['type_id'];?></td>
			<td><a href='javascript:val_load_new("<?php echo $slct1['type_id'];?> ")'><?php echo $slct1['type_name']?></a></td>
			<td><a href="javascript:delete_data('<?php echo $slct1['type_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else {return false;}"><img height="15" width="15" src="../images/delete.ico"/></a></td>

		</tr>
    <?php
	 ;}
	 }
////////////////////////////////////////////////////////////	 

////////////////////////////////////////////
elseif($type=="phpendngbill")///for  unpaid bill
{
	$orderid=$_POST['orderid'];
	?>
	  <tr>
		  <td>#</td>
          <td>Date</td>
          <td>IPD No</td>
          <td>Bll No</td>
          <td>Total Amount</td>
          <td>Discount</td>
          <td>Paid</td>
          <td>Balance</td>
       
	  </tr>
	<?php
	 $i=1;
	    $class="pats";
	    	
	    $qrlm=mysqli_query($link,"select * from ph_sell_master where ipd_id='$orderid' and balance>0  ");
		while($qrlm1=mysqli_fetch_array($qrlm)){
		
		//$qalrdypaid=mysqli_fetch_array(mysqli_query($link,"select * from inv_substore_order_rcv_details  where order_no='$orderid' "));	
		$vnwpaid=$qrlm1['balance'];
		?>
           
            <tr style="cursor:pointer"  onclick="javascript:val_load_new1('<?php echo $orderid;?>')">
                <td><input type="checkbox"  id="<?php echo $i;?>" class="<?php echo $class;?>" value="<?php echo $qrlm1['item_code'];?>" onclick="add_netamt('<?php echo $orderid;?>','<?php echo $vnwpaid;?>','<?php echo $i;?>')"/><label><span></span></label></td>
                
                <td><?php echo $qrlm1['entry_date'];?></td>
                <td><?php echo $orderid;?></td>
                <td><?php echo $qrlm1['bill_no'];?></td>
                <td><?php echo $qrlm1['total_amt'];?></td>
                <td><?php echo $qrlm1['discount_amt'];?></td>
                <td><?php echo $qrlm1['paid_amt'];?></td>
                <td><input type="text" id="txtnwpaid_<?php echo $i;?>" value="<?php echo $vnwpaid;?>" ></td>
                <!--<td><a href="javascript:delete_data('<?php echo $qrlm1['inv_no'];?>','<?php echo $qrlm1['inv_no'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it')){return true;} else{return false;}"><img height="15" width="15" src="../images/delete.ico"/></a></td>-->
               
           </tr>
       
        <?php	
		$i++;}
		
		?>
		
		<tr>
			<td colspan="8" align="center">
				<input type="button" id="sel_all" value="Select All" class="btn btn-info" onclick="select_all(this.value)"/>
				<input type="button" id="sel_all" value="Done" class="btn btn-success" onclick="data_saved()"/> </td>
		</tr>
		
<?php	
		
}
?>
      
  </table>
  </div>

