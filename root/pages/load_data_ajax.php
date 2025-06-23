<?php
	include'../../includes/connection.php';
	$type=$_POST['type'];
	?>
<table class="table table-bordered">
	<?php
		if($type=="rpntv") ///for Represntive
		{
		 $slct=mysqli_query($GLOBALS["___mysqli_ston"], "select * from represntive_master where show_type='1' order by rp_name ");
		 while($slct1=mysqli_fetch_array($slct)){
		?>
	<tr>
		<td><?php echo $slct1['rp_id'];?></td>
		<td><a href='javascript:val_load_new("<?php echo $slct1['rp_id'];?> ")'><?php echo $slct1['rp_name']?></a></td>
		<td><a href="javascript:delete_data('<?php echo $slct1['rp_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else {return false;}"> <span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php
		;}
		}
		
		
		////////////////////////////////////////////////////////////	 
		elseif($type=="acslvl") ///For load access lavel
		{
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], "select * from access_level_details  order by access_lavel ");
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		    ?>
	<tr>
		<td><?php echo $qrpdct1['lavelid'];?> </td>
		<td><a href="javascript:val_load_new('<?php echo $qrpdct1['lavelid'];?>')"><?php  echo $qrpdct1['access_lavel'];?></a></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['lavelid'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		;}
		}	
		
		///////////////////////////////////////////////////////////	  
elseif($type=="testmethod") ///For testmethod
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from test_methods  where name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from test_methods  order by name";
	 }
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
             <td><?php echo $qrpdct1['name'];?></td>
             
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
         </tr>	
         <?php	
		   $i++;}
	  }
	  
	  ///////////////////////////////////////////////////////////	  
		elseif($type=="alltst") ///For param not requird test form
		{
		$val=$_POST['val'];
		if($val)
		{
		  $q="select testid,testname from testmaster  where  testname like '$val%'";
		}
		else
		{
		 $q="select testid,testname from testmaster   order by testname";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['testid'];?>"/> <label><span></span></label> </td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo substr($qrpdct1['testname'],0,45);?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="notrqrdtstprm") ///For not required test param
		{
			
		$val=$_POST['val'];
		if($val)
		{
		$q="select a.testid,a.testname from testmaster a,not_required_test_param  where a.testid=b.testid and  a.testname like '$val%'";
		}
		else
		{
		 $q="select a.testid,a.testname from testmaster a,not_required_test_param b where a.testid=b.testid order by a.testname";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['testid'];?>"/> <label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
	</tr>
	<?php	
		$i++;}
		}
	  ///////////////////////////////////////////////////////////	  
		elseif($type=="ccdprmnttdscnt") ///For load test against dept in testwise commision lab doctor
		{
		
		$deptid=$_POST['deptid'];
		 $q="select testid,testname,rate from testmaster where type_id='$deptid' order by testname";
		
		$qrpdct=mysqli_query($link,$q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr >
		<td><input type="checkbox" id="sel[]" name="sel[]" class="check" value="<?php echo $qrpdct1['testid'];?>"/><label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
		<td><?php echo $qrpdct1['rate'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
	   ///////////////////////////////////////////////////////////	  
    elseif($type=="vaccumaster") ///For Vaccu
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from vaccu_master  where type like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from vaccu_master  order by type";
	 }
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
             <td><?php echo $qrpdct1['type'];?></td>
             <td><?php echo $qrpdct1['rate'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
             
         </tr>	
         <?php	
		   $i++;}
	  }  	
	  ///////////////////////////////////////////////////////////	  
    elseif($type=="samplemastr") ///For sample
	{
	 $val=$_POST['val'];
	 if($val)
	 {
		 $q="select * from Sample  where Name like '$val%'";
	 }
	 else
	 {
	   	 $q="select * from Sample  order by Name";
	 }
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
	     ?>
         <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ID'];?>')" id="rad_test<?php echo $i;?>">
         
             <td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ID'];?></td>
             <td><?php echo $qrpdct1['Name'];?></td>
             <td><a href="javascript:delete_data('<?php echo $qrpdct1['ID'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"> <img height="15" width="15" src="../images/delete.ico"/></a></td>
             
         </tr>	
         <?php	
		   $i++;}
	  } 
		///////////////////////////////////////////////////////////	  
		elseif($type=="executivemastr") ///For executive master
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from marketing_executive_master  where mr_name like '$val%'";
		}
		else
		{
		 $q="select * from marketing_executive_master  order by mr_name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['mr_id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['mr_id'];?></td>
		<td><?php echo $qrpdct1['mr_name'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['mr_id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="cntrungroup") ///For center group
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from centremaster where centreno not in(select centreno from 	center_group)  and centrename like '$val%'";
		}
		else
		{
		 $q="select * from centremaster where centreno not in(select centreno from 	center_group)  order by centrename";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['centreno'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['centreno'];?>"/><label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['centreno'];?></td>
		<td><?php echo $qrpdct1['centrename'];?></td>
		
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="cntrgroup") ///For center grouped
		{
			$mid=$_POST['mkid'];
		
		 $q="select * from centremaster where centreno  in(select centreno from center_group where id='$mid')  order by centrename";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['centreno'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['centreno'];?>"/><label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['centreno'];?></td>
		<td><?php echo $qrpdct1['centrename'];?></td>
		
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="ungroupnabl") ///For ungroup nabl logo
		{
		$val=$_POST['val'];
		if($val)
		{
		  $q="select testid,testname from testmaster  where category_id='1' and testid not in(select testid from nabl_logo ) and testname like '%$val%'";
		}
		else
		{
		 $q="select testid,testname from testmaster where category_id='1' and testid not in(select testid from nabl_logo )  order by testname";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['testid'];?>"/> <label><span></span></label> </td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="groupnabl") ///For group nabl
		{
			
		
		  $q="select testid,testname from testmaster where category_id='1' and testid  in(select testid from nabl_logo )  order by testname";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['testid'];?>"/> <label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="ungroup") ///For ungroup doctor
		{
		$val=$_POST['val'];
		if($val)
		{
		  $q="select * from refbydoctor_master  where refbydoctorid not in(select refbydoctorid from doctor_group ) and ref_name like '%$val%'";
		}
		else
		{
		 $q="select * from refbydoctor_master where refbydoctorid not in(select refbydoctorid from doctor_group )  order by ref_name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['refbydoctorid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['refbydoctorid'];?>"/> <label><span></span></label> </td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['refbydoctorid'];?></td>
		<td><?php echo $qrpdct1['ref_name'];?></td>
		<td><?php echo $qrpdct1['qualification'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="ungrouphealthpckge") ///For ungroup Health package test
		{
		$val=$_POST['val'];
		if($val)
		{
		  $q="select testid,testname from testmaster  where testname like '%$val%'";
		}
		else
		{
		 $q="select testid,testname from testmaster   order by testname";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['testid'];?>"/> <label><span></span></label> </td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo substr($qrpdct1['testname'],0,40);?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="grouphptest") ///For group Health packege test
		{
		$mrid=$_POST['mkid'];	
		$val=$_POST['val'];
		if($val)
		{
		$q="select a.testid,a.testname from testmaster a,health_package_master b where a.testid=b.testid and b.hp_id='$mrid' and  a.testname like '$val%'";
		}
		else
		{
		 
		 $q="select a.testid,a.testname from testmaster a,health_package_master b where a.testid=b.testid and b.hp_id='$mrid' order by a.testname";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['testid'];?>"/> <label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="ungrouptest") ///For ungroup normal test
		{
		$val=$_POST['val'];
		if($val)
		{
		  $q="select * from testmaster  where  testname like '$val%'";
		}
		else
		{
		 $q="select * from testmaster  order by testname";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['testid'];?>"/> <label><span></span></label> </td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo substr($qrpdct1['testname'],0,40);?></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="grpspltest") ///For special test
		{
		
		 $q="select a.testid,b.testname from special_test a,testmaster b where a.testid=b.testid order by b.testname";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['testid'];?>"/> <label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="ungroupparam") ///For ungroup Param
		{
		$val=$_POST['val'];
		$tstid=$_POST['tstid'];
		if($val)
		{
		  //$q="select ID,Name from Parameter_old where Name like '%$val%'";
		}
		else
		{
		
		 $q="select ID,Name from Parameter_old where ID in (select ParamaterId from Testparameter where Testid='$tstid')   order by Name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ID'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['ID'];?>"/> <label><span></span></label> </td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ID'];?></td>
		<td><?php echo $qrpdct1['Name'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="groupdoc") ///For ungroup doctor
		{
		$mrid=$_POST['mkid'];	
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from refbydoctor_master  where refbydoctorid in(select refbydoctorid from doctor_group where id='$mrid') and  ref_name like '$val%'";
		}
		else
		{
		 $q="select * from refbydoctor_master where refbydoctorid in(select refbydoctorid from doctor_group where id='$mrid') order by ref_name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['refbydoctorid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['refbydoctorid'];?>"/> <label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['refbydoctorid'];?></td>
		<td><?php echo $qrpdct1['ref_name'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="ungrpoptionlist") ///For making option group
		{
		$val=$_POST['val'];
		if($val)
		{
		 $q="select * from Options  where  name like '%$val%'";
		}
		else
		{
		 $q="select * from Options  order by name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['id'];?>"/><label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td><?php echo $qrpdct1['name'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="grpoptionlist") ///For group option list
		{
		$mrid=$_POST['mkid'];	
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from Options  where id in(select optionid from ResultOptions where id='$mrid') and  name like '$val%'";
		}
		else
		{
		 $q="select * from Options where id in(select optionid from ResultOptions where id='$mrid') order by name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['id'];?>"/><label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td><?php echo $qrpdct1['name'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="center") ///For Center in collection center account
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select centreno,centrename from centremaster where centrename like '$val%'";
		}
		else
		{
		 $q="select centreno,centrename from centremaster order by centrename";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:laod_patient('<?php echo $qrpdct1['centreno'];?>','<?php echo $qrpdct1['centrename'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['centreno'];?></td>
		<td><?php echo $qrpdct1['centrename'];?></td>
		
	</tr>
	<?php	
		$i++;}
		}
		//////////////////////////////
		elseif($type=="collpant")	////load patient against center
            {
			$cntrno=$_POST['val'];
			$fromd=$_POST['from'];
			$tod=$_POST['to'];	
	      
	         $qrpant=mysqli_query($GLOBALS["___mysqli_ston"], "select a.reg_no,b.patient_id,b.visit_no,c.name from patient_reg_details a,patient_details b,patient_info c where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.date between '$fromd' and '$tod' and b.centreno='$cntrno' and a.patient_id=c.patient_id order by a.reg_no ");
	        while($qrpant1=mysqli_fetch_array($qrpant)){
		 ?>
         <tr style="cursor:pointer" onclick="load_test('<?php echo $qrpant1[reg_no];?>','<?php echo $qrpant1[name];?>')">
            <td><?php echo $qrpant1['reg_no'];?></td>
            <td><?php echo $qrpant1['name'];?></td>
         </tr>
         <?php
		 ;}
	    }
		///////////////////////////////////////////////////////////	  
		elseif($type=="refdoc") ///For Refdoctor
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from refbydoctor_master  where  recp=0 and ref_name like '$val%'";
		}
		else
		{
		 $q="select * from refbydoctor_master where recp=0  order by ref_name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['refbydoctorid'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['refbydoctorid'];?></td>
		<td><?php echo $qrpdct1['ref_name']. ' , '.$qrpdct1['qualification'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['refbydoctorid'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="cntermaster") ///For center master
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from centremaster  where 	centrename like '$val%'";
		}
		else
		{
		 $q="select * from centremaster  order by 	centrename";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['centreno'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['centreno'];?></td>
		<td><?php echo $qrpdct1['centrename'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['centreno'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="testwisediscnt") ///For test wise discount
		{
		$val=$_POST['val'];
		if($val)
		{
		 $q="select testid,testname from testmaster  where testname like '$val%'";
		}
		else
		{
		 $q="select testid,testname from testmaster  order by testname";
		}
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
		
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="refdoccom") ///For Refdoctor in doc comm
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from refbydoctor_master  where ref_name like '$val%'";
		}
		else
		{
		 $q="select * from refbydoctor_master  order by ref_name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['refbydoctorid'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['refbydoctorid'];?></td>
		<td><?php echo $qrpdct1['ref_name'].' ,'.$qrpdct1['qualification'];?></td>
		<td><?php echo $qrpdct1['en_type'];?></td>
		
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="dailyexpnse") ///For expense
		{
		$val=$_POST['val'];
		$vdate=$_POST['vdate'];
		
		 $q="select * from expensedetail where date1='$vdate' order by SlNo";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['SlNo'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['SlNo'];?></td>
		<td><?php echo $qrpdct1['Description'];?></td>
		<td><?php echo $qrpdct1['Amount'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['date1'];?>','<?php echo $qrpdct1['SlNo'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="delccextrarcpt") ///For delete extrarcept
		{
		$val=$_POST['val'];
		$vdate=$_POST['vdate'];
		
		 $q="select a.*,b.centrename from center_extra_receipt a,centremaster b where a.centreno=b.centreno and a.date='$vdate' order by slNo";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['slno'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['slno'];?></td>
		<td><?php echo $qrpdct1['centrename'];?></td>
		<td><?php echo $qrpdct1['description'];?></td>
		<td><?php echo $qrpdct1['amount'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['date'];?>','<?php echo $qrpdct1['slno'];?>','<?php echo $qrpdct1['centreno'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="extrareceipt") ///For extra receipt
		{
		 $val=$_POST['val'];
		 $vdate=$_POST['vdate'];
		
		 $q="select * from extra_receipt where date1='$vdate' order by SlNo";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['slno'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['slno'];?></td>
		<td><?php echo $qrpdct1['desc'];?></td>
		<td><?php echo $qrpdct1['amount'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['date1'];?>','<?php echo $qrpdct1['slno'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="mrktng") ///For Marketing
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from marketingpersonal  where name like '$val%'";
		}
		else
		{
		 $q="select id,name from marketingpersonal order by name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td><?php echo $qrpdct1['name'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="refdoccase") ///For Refdoctor case
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from refbydoctor_master  where ref_name like '$val%'";
		}
		else
		{
		 $q="select * from refbydoctor_master  order by ref_name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['refbydoctorid'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['refbydoctorid'];?></td>
		<td><?php echo $qrpdct1['ref_name'].' ,'.$qrpdct1['qualification'];?></td>
		<td><?php echo $qrpdct1['address'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="item") ///For Item Master
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from item_master where ItemName like '$val%'";
		}
		else
		{
		 $q="select * from item_master  order by ItemName";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ItemCode'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ItemCode'];?></td>
		<td><?php echo $qrpdct1['ItemName'];?></td>
		<td><?php echo $qrpdct1['MRP'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['ItemCode'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}	
		
		
		
		//////////////////////////////////////////
		elseif($type=="crtuser") ///For Create User
		{
			
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from Employee where Name like '$val%'";
		}
		else
		{
		 $q="select * from Employee  order by ID";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"],$q);	
		
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr>
		<td><?php echo $qrpdct1['ID'];?> </td>
		<td><a href="javascript:val_load_new('<?php echo $qrpdct1['ID'];?>')"><?php  echo $qrpdct1['Name'];?></a></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['ID'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		;}
		}
		
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="ungroupitem") ///For  ungroup Item against supplierwise
		{
		
		 $q="select testid,testname from testmaster  order by testname";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr >
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['testid'];?>"/></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		//////////////////////////////////////////////////////////////////////
	  elseif($type=="testtarget")///for spltest target form
	  {
		  
		 $val=$_POST['val'];
		if($val)
		{
		$q="select testid,testname,rate from testmaster where  testname like '$val%'";
		}
		else
		{
		 $q="select * from testmaster  order by testname";
		}
		$qr=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		
		  //$qr=mysql_query("select testid,testname from testmaster where testid not in(select testid from marketing_test) order by testname");
		  while($qr1=mysqli_fetch_array($qr)){
			 
			?>
                 
              <tr style="cursor:pointer" onclick="javascript:val_load_new('<?php echo $qr1['testid'];?>')" id="rad_test<?php echo $i;?>">
              
                <td id="prod<?php echo $i;?>"><?php echo $qr1['testid'];?></td>
                <td><?php echo $qr1['testname'];?></td>
               <td><?php echo $qr1['rate'];?></td>
             </tr>
            <?php  
			$i++; }    
	  }
		
			//////////////////////////////////////////////////////////////////////
	  elseif($type=="alltestfrdiscnt")///for Discount Test form
	  {
		  
		 $val=$_POST['val'];
		if($val)
		{
		$q="select testid,testname,rate from testmaster where  testname like '$val%'";
		}
		else
		{
		 $q="select * from testmaster  order by testname";
		}
		$qr=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		
		  //$qr=mysql_query("select testid,testname from testmaster where testid not in(select testid from marketing_test) order by testname");
		  while($qr1=mysqli_fetch_array($qr)){
			 
			?>
                 
              <tr style="cursor:pointer" onclick="javascript:val_load_new('<?php echo $qr1['testid'];?>')" id="rad_test<?php echo $i;?>">
              
                <td id="prod<?php echo $i;?>"><?php echo $qr1['testid'];?></td>
                <td><?php echo $qr1['testname'];?></td>
                 <td><?php echo $qr1['rate'];?></td>
               
             </tr>
            <?php  
			$i++; }    
	  }
	  
		//////////////////////////////////////////////////////////////////////
	  elseif($type=="ungrptest")///for spltest form
	  {
		  
		 $val=$_POST['val'];
		if($val)
		{
		$q="select testid,testname from testmaster where testid not in(select testid from marketing_test) and testname like '$val%'";
		}
		else
		{
		 $q="select * from testmaster  order by testname";
		}
		$qr=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		
		  //$qr=mysql_query("select testid,testname from testmaster where testid not in(select testid from marketing_test) order by testname");
		  while($qr1=mysqli_fetch_array($qr)){
			 
			?>
                 
              <tr style="cursor:pointer" onclick="javascript:val_load_new('<?php echo $qr1['testid'];?>')" id="rad_test<?php echo $i;?>">
              
                <td id="prod<?php echo $i;?>"><?php echo $qr1['testid'];?></td>
                <td><?php echo $qr1['testname'];?></td>
               
             </tr>
            <?php  
			$i++; }    
	  }
	  
	  		//////////////////////////////////////////////////////////////////////
	  elseif($type=="disconttest")///for Discount Test form
	  {
		  
		  $qr=mysqli_query($GLOBALS["___mysqli_ston"], "select a.testid,a.Discount_Rate,b.testname from discount_test a,testmaster b where  a.testid=b.testid order by b.testname   ");
		  while($qr1=mysqli_fetch_array($qr)){
			 
			?>
                 
              <tr style="cursor:pointer" onclick="javascript:val_load_new('<?php echo $qr1['testid'];?>')">
              
                <td><?php echo $qr1['testid'];?></td>
                <td><?php echo $qr1['testname'];?></td>
                <td><?php echo $qr1['Discount_Rate'];?></td>
                 <td><a href="javascript:delete_data('<?php echo $qr1['testid'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
             </tr>
            <?php  
			; }    
	  }
		//////////////////////////////////////////////////////////////////////
	  elseif($type=="grpspl")///for spltest form
	  {
		  $qr=mysqli_query($GLOBALS["___mysqli_ston"], "select testid,testname from testmaster where testid  in(select testid from marketing_test) order by testname");
		  while($qr1=mysqli_fetch_array($qr)){
			 
			?>
                 
              <tr style="cursor:pointer" onclick="javascript:val_load_new('<?php echo $qr1['testid'];?>')">
              
                <td><?php echo $qr1['testid'];?></td>
                <td><?php echo $qr1['testname'];?></td>
                 <td><a href="javascript:delete_data('<?php echo $qr1['testid'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
             </tr>
            <?php  
			; }    
	  }
		///////////////////////////////////////////////////////////	  
		elseif($type=="groupitem") ///For  group Item against supplierwise
		{
		$mkid=$_POST['mkid'];
		
		 $q="select * from testmaster where testid in(select testid from testmaster_profile  where profile_id='$mkid') order by testname";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr >
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['testid'];?>"/></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
	</tr>
	<?php	
		$i++;}
		}	  	  
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="doctestwise") ///For load doctor in testwise commision
		{
		//echo "<tr><td colspan='3'><input type='checkbox' id='doc_sel_all' onclick='select_doc(this.id)'/> Select All</td></tr>";
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from ref_doc_master  where name like '$val%'";
		}
		else
		{
		 $q="select * from ref_doc_master order by name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['id'];?>" class="doc_check"/></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td><?php echo $qrpdct1['name'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="doctestwise1") ///For load doctor in testwise commision
		{
		//echo "<tr><td colspan='3'><input type='checkbox' id='doc_sel_all' name='doc_sel_all' onclick='select_doc(this.id)'/> Select All</td></tr>";
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from ref_doc_master  where name like '$val%'";
		}
		else
		{
		 $q="select * from ref_doc_master order by name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel[]" value="<?php echo $qrpdct1['id'];?>" class="doc_check" /></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td><?php echo $qrpdct1['name'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="depttest") ///For load test against dept in testwise commision
		{
		
		$deptid=$_POST['deptid'];
		 $q="select testid,testname from testmaster where type_id='$deptid' order by testname";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['testid'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['testid'];?>"/></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['testid'];?></td>
		<td><?php echo $qrpdct1['testname'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="resultoption") ///For Result options
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from ResultOption  where name like '$val%'";
		}
		else
		{
		 $q="select * from ResultOption  order by name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td><?php echo $qrpdct1['name'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="option") ///For options
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from Options  where name like '$val%'";
		}
		else
		{
		 $q="select * from Options  order by name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td><?php echo $qrpdct1['name'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		
		///////////////////////////////////////////////////////////	  
		elseif($type=="unitmaster") ///For Unit Master
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from Units  where unit_name like '$val%'";
		}
		else
		{
		 $q="select * from Units  order by unit_name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ID'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ID'];?></td>
		<td><?php echo $qrpdct1['unit_name'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['ID'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="resulttype") ///For Result Type Master
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from ResultType  where ResultType_name like '$val%'";
		}
		else
		{
		 $q="select * from ResultType  order by ResultType_name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ResultTypeId'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ResultTypeId'];?></td>
		<td><?php echo $qrpdct1['ResultType_name'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['ResultTypeId'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="groupparam") ///For Nable group param
		{
		$mrid=$_POST['mkid'];	
		
		 $q="select a.testid,a.param_id,b.ID,b.Name from nabl_param a,Parameter_old b where a.param_id=b.ID and a.testid='$mrid' order by b.Name";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	    <tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['ID'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['ID'];?>"/> <label><span></span></label></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['ID'];?></td>
		<td><?php echo $qrpdct1['Name'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="labdoctor") ///For Lab Doctor Master
		{
		$val=$_POST['val'];
		if($val)
		{
		$q="select * from lab_doctor  where name like '$val%'";
		}
		else
		{
		 $q="select * from lab_doctor  order by name";
		}
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['id'];?>')" id="rad_test<?php echo $i;?>">
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['id'];?></td>
		<td><?php echo $qrpdct1['name'];?></td>
		<td><a href="javascript:delete_data('<?php echo $qrpdct1['id'];?>')" onclick="javascript:if(confirm('Are you sure want to delete it..')){return true;} else{return false;}"><span class="text-danger"><i class="fa fa-times-circle fa-lg"></i></span></a></td>
	</tr>
	<?php	
		$i++;}
		}
		///////////////////////////////////////////////////////////	  
		elseif($type=="department") ///For department in  commision
		{
		
		$deptid=$_POST['deptid'];
		 $q="select distinct type_id,type_name FROM testmaster order by type_name ";
		
		$qrpdct=mysqli_query($GLOBALS["___mysqli_ston"], $q);
		$i=1;
		while($qrpdct1=mysqli_fetch_array($qrpdct)){
		 ?>
	<tr style="cursor:pointer"  onclick="javascript:val_load_new('<?php echo $qrpdct1['type_id'];?>')" id="rad_test<?php echo $i;?>">
		<td><input type="checkbox" name="sel1[]" value="<?php echo $qrpdct1['type_id'];?>"/></td>
		<td id="prod<?php echo $i;?>"><?php echo $qrpdct1['type_id'];?></td>
		<td><?php echo $qrpdct1['type_name'];?></td>
	</tr>
	<?php	
		$i++;}
		}
		 	    	  	 	  	    	  	  	     	      
		?>
</table>
