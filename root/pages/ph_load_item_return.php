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
elseif($type=="phitmrtrn")///for  unpaid bill
{
	$billno=$_POST['bilno'];
	$qpdtl=mysqli_fetch_array(mysqli_query($link,"select * from ph_sell_master where bill_no='$billno'"));
	?>
	  <tr>
		  <td colspan="8" style="font-size:14px;font-weight:bold">Customer Name : <?php echo $qpdtl['customer_name'];?> &nbsp; &nbsp; Bill No : <?php echo $billno;?> &nbsp;&nbsp; Sale Date : <?php echo $qpdtl['entry_date'];?> </td>
  	  </tr>
	  
	  <tr>
		  <td>#</td>
          <td>Date</td>
          <td>Bill No</td>
          <td>Item Code</td>
          <td>Name</td>
          <td>Batch No</td>
          <td>Sale Qnty</td>
          <td>Return. Qnty</td>
 	  </tr>
	  
	<?php
	 $i=1;
	    $class="pats";
	   
	    $qrlm=mysqli_query($link,"select a.*,b.item_name from ph_sell_details a,ph_item_master b  where a.bill_no='$billno'  and a.item_code=b.item_code ");
		while($qrlm1=mysqli_fetch_array($qrlm)){
		
		//$qalrdypaid=mysqli_fetch_array(mysqli_query($link,"select * from inv_substore_order_rcv_details  where order_no='$orderid' "));	
		$vnwpaid=$qrlm1['sale_qnt'];
		?>
           
            <tr style="cursor:pointer"  onclick="javascript:val_load_new1('<?php echo $billno;?>')">
                <td><input type="checkbox"  id="<?php echo $i;?>" class="<?php echo $class;?>" value="<?php echo $qrlm1['item_code'];?>" /><label><span></span></label></td>
                <td><?php echo $qrlm1['entry_date'];?></td>
                <td><?php echo $billno;?></td>
                <td><?php echo $qrlm1['item_code'];?></td>
                <td><?php echo $qrlm1['item_name'];?></td>
                <td id="btchno_<?php echo $i;?>"><?php echo $qrlm1['batch_no'];?></td>
                <td><?php echo $qrlm1['sale_qnt'];?></td>
                <td><input type="text" style="width:30px" id="txtnwpaid_<?php echo $i;?>" value="<?php echo $vnwpaid;?>" onkeyup="chk_quantity('<?php echo $billno;?>','<?php echo $qrlm1[item_code];?>',<?php echo $qrlm1[sale_qnt];?>,<?php echo $i;?>)" ></td>
                <!--<td><a href="javascript:delete_data('<?php echo $qrlm1['inv_no'];?>','<?php echo $qrlm1['inv_no'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it')){return true;} else{return false;}"><img height="15" width="15" src="../images/delete.ico"/></a></td>-->
               
           </tr>
       
	     
        <?php	
		$i++;}
		
		?>
		
		<tr>
			<td colspan="8" align="center">
				Reason For Return : <input type="text" id="txtreason"/>
			 </td>
		</tr>
		
		<tr>
			<td colspan="8" align="center">
				<input type="button" id="sel_all" value="Select All" class="btn btn-info" onclick="select_all(this.value)"/>
				<input type="button" id="button1" value="Done" class="btn btn-success" onclick="data_saved()"/> 
				<input type="button" id="button6" value="Print" class="btn btn-primary" style="width:70px" onclick="popitup('pages/ph_itm_rtn_crdt_rpt.php')" />
		   </td> 		
		</tr>
		
<?php	
		
}
?>
      
  </table>
  </div>

