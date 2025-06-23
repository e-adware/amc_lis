<table class="table table-bordered table-condensed">
	<!--<tr>
		<th colspan="8" class=""><input type="button" id="sync" onclick="sync_list()" class="btn btn-custom text-right" value="Sync"/></th>
	</tr>-->
	<tr id="tr_head">
		<th style="width:5%;">#</th>
		<th>UHID</th>
		<th>PIN</th>
		<th>Name - Phone</th>
		<th>Age-Sex</th>
		<th>Time</th>
		<th>Date</th>
	</tr>
	<?php
		include("../../includes/connection.php");
		$date=date('Y-m-d');
		$date1 = strtotime(date("Y-m-d", strtotime($date)) . " -5 days");
		$date_five=date("Y-m-d",$date1);
		$date_c=date("Y-m-d");
		$type=$_POST['type'];
		
		// Date format convert
		function convert_date($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d-M-y', $timestamp);
			return $new_date;
		}
		// Time format convert
		function convert_time($time)
		{
			$time = date("g:i A", strtotime($time));
			return $time;
		}	 
		 	$val=$_POST['val'];
		 	$id=$_POST['id'];
		 	$category_id=$_POST['category_id'];
		 	
		 	
		 	if($type=="radiodate")
		 	{
				$date1=$_POST['fdate'];
				$date2=$_POST['tdate'];
			    $qry=mysqli_query($link, "select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and date between '$date1' and '$date2' order by slno desc");	
			    
			    $s_by="date"; 
			}
			else
			{
				if($id=="name")
				{
					$s_by="name";
					if($val)
					{
							$qry=mysqli_query($link, "select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and patient_id in(select patient_id from patient_info where name like '%$val%') order by slno desc");
					}	
					else
					{
							$qry=mysqli_query($link, "select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and date between '$date_five' and '$date_c' order by slno desc");	
					}
				}else if($id=="bill")
				{
					$qry=mysqli_query($link, "select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and patient_id in(select patient_id from patient_info where patient_id like '%$val%') order by slno desc");
				}
				else if($id=="p_id")
				{
					$qry=mysqli_query($link, "select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and (opd_id like '$val%' or ipd_id like'$val%') order by slno desc");
				}
				else
				{
					$qry=mysqli_query($link, "select distinct patient_id,opd_id,ipd_id,batch_no,date,time from patient_test_details where testid in(select testid from testmaster where category_id='$category_id') and date between '$date_five' and '$date_c' order by slno desc");	
					$s_by="all";
				}
		 }	
		echo "<input type='hidden' id='search_by' value='$s_by'/>";	
		$i=1;
		while($q=mysqli_fetch_array($qry))
		{
			if($q['opd_id'])
			{
				$pin=$q['opd_id'];
			}
			if($q['ipd_id'])
			{
				$pin=$q['ipd_id'];
			}
			
			$name=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$q[patient_id]'"));
			
			
			$num1=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='$category_id') "));
			
			if($category_id==2)
			{
				$num2=mysqli_num_rows(mysqli_query($link, "select * from testresults_rad where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and observ!=''"));
			}
			if($category_id==3)
			{
				$num2=mysqli_num_rows(mysqli_query($link, "select * from testresults_card where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and observ!=''"));
			}
			
			$num3=$num1-$num2;
			
			if($num2==0)
			{
				// Not received at all
				$style_span="background-color: #d59a9a;";
				$cls="red";
			}else if($num1==$num2)
			{
				// All received
				$style_span="background-color: #9dcf8a;";
				$cls="green";
			}else
			{
				// Partially received
				$style_span="background-color: #f6e8a8;";
				$cls="yellow";
			}
			
			$num4=mysqli_num_rows(mysqli_query($link, "select distinct testid from testreport_print where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where category_id='$category_id')"));
			
			if($num2!=0)
			{
				if($num2==$num4)
				{
					// Partially received
					$style_span="background-color: #666666;";
					$cls="gray";
				}		
			}
			
			
			$cen="";
		
			$xr=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where type_id='40')"));
			if($xr>0)
			{
				$cls=$cls." xr";	
			}
			
			$ultr=mysqli_num_rows(mysqli_query($link, "select * from patient_test_details where patient_id='$q[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and testid in(select testid from testmaster where type_id='128')"));
			if($ultr>0)
			{
				$cls=$cls." ultr";	
			}
			?>	
		<tr id="path_tr<?php echo $i;?>" onclick="load_test_info(<?php echo $i;?>)" class="<?php echo $cls;?>" style="cursor:pointer;">
			<td><span class="btn_round" style="<?php echo $style_span; ?>"><?php echo $i; ?></span></td>
			<td><?php echo $name['patient_id'];?></td>
			<td><?php echo $pin;?></td>
			<td><?php echo $name['name'];?></td>
			<td><?php echo $name['age']." ".$name['age_type']." ".$name['sex'];?></td>
			<td><?php echo convert_time($q['time']);?></td>
			<td>
				<?php echo convert_date($q['date']);?>
				<div id="path_pat<?php echo $i;?>" style="display:none">
					<?php echo "@".$q['patient_id']."@".$q['opd_id']."@".$q['ipd_id']."@".$q['batch_no'];?>
				</div>
			</td>
		</tr>
		<?php
			$i++;
		
		}
		?>
</table>
<style>
	.btn_round
	{
		color:#000;
		padding:3px;
		border-radius: 7em;
		//background-color: #d59a9a; #9dcf8a;
		padding-right: 7px;
		padding-left: 7px;
		box-shadow: inset 1px 1px 0 rgba(0,0,0,0.6);
		transition: all ease-in-out 0.2s;
	}
	tr.green:hover td span,
	tr.red:hover td span,
	tr.yellow:hover td span,
	tr.gray:hover td span
	{
		padding:8px;
		padding-right:12px;
		padding-left:12px;
	}
</style>
