<?php
 include'../../includes/connection.php';
 
 $date1=$_POST['date1'];
 $date2=$_POST['date2'];
 $centr=$_POST['centr'];

 
 mysqli_query($GLOBALS["___mysqli_ston"], "delete from dummycomdetails_center");
 
$i=1;


$q=mysqli_query($GLOBALS["___mysqli_ston"], "select a.patient_id,a.visit_no,a.centreno,b.reg_no,a.refbydoctorid,a.date  from patient_details a,patient_reg_details b where a.patient_id=b.patient_id and a.visit_no=b.visit_no and a.centreno='$centr' and a.date between'$date1' and '$date2'  ");
 while($q1=mysqli_fetch_array($q)){
	    
		 	  
			    $slrnk=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select c_patho,c_ultra,c_xray,c_cardio,c_spl,s_patho,s_ultra,s_xray,s_cardio,s_spl from centremaster where centreno='$centr'"));
			    $pathornk=$slrnk['c_patho'];
			    $usgrnk=$slrnk['c_ultra'];
			    $xrayrnk=$slrnk['c_xray'];	
			    $crdornk=$slrnk['c_cardio'];
			    $splrank=$slrnk['c_spl'];
			    
			   

               if($q1['refbydoctorid']=='937')
               {
				   $pathornk=$slrnk['s_patho'];
				   $usgrnk=$slrnk['s_ultra'];
			       $xrayrnk=$slrnk['s_xray'];	
			       $crdornk=$slrnk['s_cardio'];
				   $splrank=$slrnk['s_spl'];
			   }
			
			
			$q2=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select Sum(a.test_rate)as maxspl From patient_test_details a,testmaster b where a.patient_id='$q1[patient_id]' and a.visit_no='$q1[visit_no]' and a.testid=b.testid and  b.category_id='1' and b.type_id not in(select type_id from special_dept) and b.type_id !='132' "));
			$pathoamount=$q2['maxspl'];
			
			$ultra=0;			
			$q3=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select Sum(a.test_rate)as maxultra from patient_test_details a,testmaster b where a.patient_id='$q1[patient_id]' and a.visit_no='$q1[visit_no]' and a.testid=b.testid and   b.type_id ='128' and b.category_id='2'")); ///01 ulta
			$ultra = $q3['maxultra'];
			
			
			$q5=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select Sum(a.test_rate)as maxxray from patient_test_details a,testmaster b where a.patient_id='$q1[patient_id]' and a.visit_no='$q1[visit_no]' and a.testid=b.testid  and b.type_id ='40' and b.category_id='2'")); ///01 for xray
			$xray=0;
			$xray = $q5['maxxray'];
			
			$cardio=0;
			$q6=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select Sum(a.test_rate)as maxcardio from patient_test_details a,testmaster b where a.patient_id='$q1[patient_id]' and a.visit_no='$q1[visit_no]' and a.testid=b.testid   and b.type_id ='131' ")); ///for cardio
			$cardio = $q6['maxcardio'];
			
			$splamt=0;
			$q4=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "Select Sum(a.test_rate)as maxspl from patient_test_details a,testmaster b,special_dept c where a.patient_id='$q1[patient_id]' and a.visit_no='$q1[visit_no]' and a.testid=b.testid and  b.type_id =c.type_id  ")); ///for spl test
			$splamt = $q4['maxspl'];
			
			
						
			///For Discount////////////////
			$qrdiscnt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select discount from patient_payment_details where patient_id='$q1[patient_id]' and visit_no='$q1[visit_no]' "));
			$Pntdiscnt = $qrdiscnt['discount'];
			
			If ($pathoamount > $Pntdiscnt) 
			{
			   $pathoamount = ($pathoamount - $Pntdiscnt);
			}
			elseIf ($splamt > $Pntdiscnt)
			{
			  $splamt = ($splamt - $Pntdiscnt);
			}
			
			
			$Vpatho = 0;
			$Vultra = 0;
			$Vxray=0;
			$Vcardio=0;
			$Vspl=0;
			
			
			
			$Vpatho = ($pathoamount*$pathornk) / 100;
			$Vultra = ($ultra*$usgrnk) / 100;
			$Vxray = ($xray*$xrayrnk) / 100;
			$Vcardio = ($cardio*$crdornk) / 100;
			$Vspl = ($splamt*$splrank) / 100;
			
					
			///////End//
			
	        mysqli_query($GLOBALS["___mysqli_ston"], "insert into dummycomdetails_center values( '$i','$q1[refbydoctorid]','$q1[reg_no]','$q1[centreno]','$pathoamount','$ultra','$xray','$cardio','$splamt','$pathornk','$ultrarnk','$xrayrnk','$cardiornk','$splrank','$Vpatho','$Vultra','$Vxray','$Vcardio','$Vspl','$q1[date]') ");
	
 $i++;}


  echo"Done ";
?>
