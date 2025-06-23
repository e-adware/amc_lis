<?php
include'../../includes/connection.php';
$type=$_POST['type'];

function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}


?>
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

////////////////////////////////////////////
elseif($type=="ph_ipd_credit")///for  ph bill
{
	$orderid=$_POST['orderid'];
	$ph=1;
	?>
	  <tr>
		  <td>#</td>
          <td>Date</td>
          <td>IPD No</td>
          <td>Bill No</td>
          <td>Name</td>
          <td>Bill Amount</td>
          <td>Now Paid</td>
          <td>Balance</td>
          
	  </tr>
	<?php
	 $i=1;
	    $class="pats";
	    	
	    $qrlm=mysqli_query($link,"select * from ph_sell_master where ipd_id='$orderid' and substore_id='$ph' and balance>0 order by slno ");
	    
		while($qrlm1=mysqli_fetch_array($qrlm)){
		
		$qttlchk=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(total_amt),0) as maxttl,ifnull(sum(balance),0) as maxttlbal from ph_sell_master where ipd_id='$orderid' and substore_id='$ph' and balance>0  "));
		
		//$qalrdypaid=mysqli_fetch_array(mysqli_query($link,"select * from inv_substore_order_rcv_details  where order_no='$orderid' "));	
		$vnwpaid=$qrlm1['balance'];
		$chk="";
		if($vnwpaid>$qrlm1[balance])
		{
			$chk="disabled";
			$class="n_pats";
		}
		?>
           
            <tr style="cursor:pointer"  onclick="javascript:val_load_new1('<?php echo $orderid;?>')">
                <td><input type="checkbox"  id="<?php echo $i;?>" class="<?php echo $class;?>" value="<?php echo $qrlm1['bill_no'];?>" onclick="add_netamt('<?php echo $orderid;?>','<?php echo $vnwpaid;?>','<?php echo $i;?>')" <?php echo $chk;?>/><label><span></span></label></td>
                
                <td><?php echo $qrlm1['entry_date'];?></td>
                <td><?php echo $orderid;?></td>
                <td><?php echo $qrlm1['bill_no'];?></td>
                <td><?php echo $qrlm1['customer_name'];?></td>
                <td><?php echo $qrlm1['total_amt'];?></td>
                <td><input type="text" style="width:60px" id="txtnwpaid_<?php echo $i;?>" value="<?php echo $vnwpaid;?>" onkeyup="chkstockanble(this.value,'<?php echo $qrlm1[balance];?>',<?php echo $i;?>)" readonly ></td>
                <td><input type="text" style="width:60px" id="txtcurstk<?php echo $i;?>" value="<?php echo $qrlm1[balance];?>" readonly ></td>
               <!-- <td><input type="text" style="width:40px" id="txtbatchno_<?php echo $i;?>" value="<?php echo $qcurstk1[batch_no];?>" readonly ></td>-->
                
                <!--<td><a href="javascript:delete_data('<?php echo $qrlm1['inv_no'];?>','<?php echo $qrlm1['inv_no'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it')){return true;} else{return false;}"><img height="15" width="15" src="../images/delete.ico"/></a></td>-->
               
           </tr>
       
	     
        <?php	
		$i++;}
		
		?>
		
		<tr>
			<td colspan="8" align="center">
				Total Bill Amount <input type="text"  style="width:100px" id="txtttl" value="<?php echo $qttlchk[maxttl];?>" readonly /><input type="hidden" id="txtbalamt" value="<?php echo $qttlchk[maxttlbal];?>" readonly /> <input type="hidden" style="width:100px" id="txtdiscount" onkeypress="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')"  onkeyup="calc_discount1(this.value,event)"   /> Total Balance <input type="text" style="width:100px" id="txtttlbal" value="<?php echo $qttlchk[maxttlbal];?>" readonly />
				
		</tr>
		
		<tr>
			<td colspan="8" align="center">
				<input type="button" id="sel_all" value="Select All" class="btn btn-info" onclick="select_all(this.value)"/>
				<input type="button" id="btn_done" value="Done" class="btn btn-success" onclick="data_saved()"/> 
				<input type="button" id="button" value="Print" class="btn btn-info" onclick="sale_rep_det_prr()"/>
				<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
			</td>
		</tr>
		
<?php	
		
}

//////////////////////////////////////////// 

////////////////////////////////////////////
elseif($type=="indent_pndng_order")///for  unpaid bill
{
	$orderid=$_POST['orderid'];
	?>
	  <tr>
		  <td>#</td>
          <td>Date</td>
          <td>Order No</td>
          <td>Item Code</td>
          <td>Name</td>
          <td>Order Qnty</td>
          <td>Issue.Qnty</td>
          <td>Avail.Qnty</td>
          <td>Batch</td>
	  </tr>
	<?php
	 $i=1;
	    $class="pats";
	    	
	    $qrlm=mysqli_query($link,"select a.*,b.item_name from inv_substore_order_details a,item_master b  where a.order_no='$orderid' and a.stat=0 and a.item_id=b.item_id ");
		while($qrlm1=mysqli_fetch_array($qrlm)){
		
		$qcurstk1=mysqli_fetch_array(mysqli_query($link,"select closing_stock,batch_no from inv_maincurrent_stock where item_id='$qrlm1[item_id]' order by closing_stock desc "));
		
		$qalrdypaid=mysqli_fetch_array(mysqli_query($link,"select * from inv_substore_order_rcv_details  where order_no='$orderid' "));	
		$vnwpaid=$qrlm1['order_qnt'];
		$chk="";
		if($vnwpaid>$qcurstk1[closing_stock])
		{
			$chk="disabled";
			$class="n_pats";
		}
		?>
           
            <tr style="cursor:pointer"  onclick="javascript:val_load_new1('<?php echo $orderid;?>')">
                <td><input type="checkbox"  id="<?php echo $i;?>" class="<?php echo $class;?>" value="<?php echo $qrlm1['item_id'];?>" onclick="add_netamt('<?php echo $orderid;?>','<?php echo $vnwpaid;?>','<?php echo $i;?>')" <?php echo $chk;?>/><label><span></span></label></td>
                
                <td><?php echo convert_date($qrlm1['order_date']);?></td>
                <td><?php echo $orderid;?></td>
                <td><?php echo $qrlm1['item_id'];?></td>
                <td><?php echo $qrlm1['item_name'];?></td>
                <td><?php echo $qrlm1['order_qnt'];?></td>
                <td><input type="text" style="width:40px" id="txtnwpaid_<?php echo $i;?>" value="<?php echo $vnwpaid;?>" onkeyup="chkstockanble(this.value,'<?php echo $qcurstk1[closing_stock];?>',<?php echo $i;?>)" ></td>
                <td><input type="text" style="width:40px" id="txtcurstk<?php echo $i;?>" value="<?php echo $qcurstk1[closing_stock];?>" readonly ></td>
                <td><input type="text" style="width:40px" id="txtbatchno_<?php echo $i;?>" value="<?php echo $qcurstk1[batch_no];?>" readonly ></td>
                
                <!--<td><a href="javascript:delete_data('<?php echo $qrlm1['inv_no'];?>','<?php echo $qrlm1['inv_no'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it')){return true;} else{return false;}"><img height="15" width="15" src="../images/delete.ico"/></a></td>-->
               
           </tr>
       
	     
        <?php	
		$i++;}
		
		?>
		
		<tr>
			<td colspan="8" align="center">
				<input type="button" id="sel_all" value="Select All" class="btn btn-info" onclick="select_all(this.value)"/>
				<input type="button" id="btn_done" value="Done" class="btn btn-success" onclick="data_saved()"/>
				<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
			</td>
		</tr>
		
<?php	
		
}

////////////////////////////////////////////
elseif($type=="ph_ipd_item_return")///for  ph ipd item return
{
	$orderid=$_POST['orderid'];
	$ph=1;
	$qpatient=mysqli_fetch_array(mysqli_query($link,"select customer_name from ph_sell_master where ipd_id='$orderid' and substore_id='$ph' and balance>0 order by slno "));
	
	?>
	  <tr>
		  <td colspan="8"> Patient : <?php echo $qpatient['customer_name'];?></td>
          
          
	  </tr>
	  
	  <tr>
		  <td>#</td>
          <td>Date</td>
          <td>Bill No</td>
          <td>Name</td>
          <td>Batch</td>
          <td>Sale qnty</td>
          <td>Return qnty</td>
          <td>Item ID</td>
          
	  </tr>
	<?php
	 $i=1;
	 
	    $class="pats";
	    	
	   	    
	    $qrlm=mysqli_query($link,"SELECT a.`bill_no`,a.`substore_id`,a.`entry_date`,a.`item_code`,a.`batch_no`,a.`sale_qnt`,c.item_name FROM `ph_sell_details` a,ph_sell_master b,item_master c where a.bill_no=b.bill_no and a.`substore_id`=b.`substore_id` and a.item_code=c.item_id and b.ipd_id='$orderid' and b.`substore_id`='$ph' and b.balance>0 order by a.entry_date,a.bill_no ");
	   
	    
		while($qrlm1=mysqli_fetch_array($qrlm)){
		
		$qttlchk=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(return_qnt),0) as maxttlrtrn from ph_item_return_master where bill_no='$qrlm1[bill_no]' and substore_id='$ph' and item_code='$qrlm1[item_code]' and batch_no='$qrlm1[batch_no]'"));
					
		$vslqnty=$qrlm1['sale_qnt']-$qttlchk['maxttlrtrn']; 
			
		$vnwpaid=$vslqnty;
		$chk="";
		if($vnwpaid>$vslqnty)
		{
			$chk="disabled";
			$class="n_pats";
		}
		?>
           
            <tr style="cursor:pointer"  onclick="javascript:val_load_new1('<?php echo $orderid;?>')">
				<td><input type="checkbox"  id="<?php echo $i;?>" class="<?php echo $class;?>" value="<?php echo $qrlm1['bill_no'];?>" onclick="add_netamt('<?php echo $qrlm1['bill_no'];?>','<?php echo $vnwpaid;?>','<?php echo $i;?>')" <?php echo $chk;?>/><label><span></span></label></td>

				<td><?php echo $qrlm1['entry_date'];?></td>
				<td><?php echo $qrlm1['bill_no'];?></td>
				<td><?php echo $qrlm1['item_name'];?></td>
				
				<td><input type="text" style="width:40px" id="txtbatchno_<?php echo $i;?>" value="<?php echo $qrlm1[batch_no];?>" readonly ></td>
				<td><input type="text" style="width:60px" id="txtcurstk<?php echo $i;?>" value="<?php echo $vslqnty;?>" readonly ></td>
				<td><input type="text" style="width:60px" id="txtnwpaid_<?php echo $i;?>"  onkeyup="chkstockanble(this.value,'<?php echo $vslqnty;?>',<?php echo $i;?>)"  ></td>
                <td><input type="text" style="width:60px" id="txtitmid<?php echo $i;?>" value="<?php echo $qrlm1[item_code];?>" readonly ></td>              
                <!--<td><a href="javascript:delete_data('<?php echo $qrlm1['inv_no'];?>','<?php echo $qrlm1['inv_no'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it')){return true;} else{return false;}"><img height="15" width="15" src="../images/delete.ico"/></a></td>-->
               
           </tr>
       
	     
        <?php	
		$i++;}
		
		?>
		
		<tr>
			<td colspan="8" align="center">
				Reason <input type="text"   id="txtreason"  /></td>
				
		</tr>
				
		<tr>
			<td colspan="8" align="center">
				<!--<input type="button" id="sel_all" value="Select All" class="btn btn-info" onclick="select_all(this.value)"/>-->
				<input type="button" id="sel_all" value="Done" class="btn btn-success" onclick="data_saved()"/> 
				<!--<input type="button" id="button" value="Print" class="btn btn-info" onclick="sale_rep_det_prr()"/>--> 
				</td>
		</tr>
		
<?php	
		
}
?>

      
  </table>
