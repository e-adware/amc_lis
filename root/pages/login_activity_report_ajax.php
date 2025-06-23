<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");


$type=$_POST['type'];

if($type==1)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$ftime=$_POST['ftime'];
	$ttime=$_POST['ttime'];
	
	$user=$_POST['user'];
	
	?>
	<table class="table table-condesned table-report table-bordered">
	<tr>
		<th>User</th> <th>Login Time/Date</th> <th>Logout Time/Date</th>
	</tr>
	
	<?php
	if($fdate==$tdate)
	{
		
		$str=mysqli_query($link,"select distinct emp_id from login_activity where date='$fdate' and TIME(time) between '$ftime' and '$ttime'");
		while($q=mysqli_fetch_array($str))
		{
			$i=1;
			$ename=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$q[emp_id]'"));
			$tot=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from login_activity where emp_id='$q[emp_id]' and status='1' and date='$fdate' and TIME(time) between '$ftime' and '$ttime'"));
			
			if($i==1)
			{
				?>	
				<tr>
					<td rowspan="<?php echo $tot[tot];?>" ><?php echo $ename[name];?></td>
				<?php
			}
			
			$det=mysqli_query($link,"select * from login_activity where date='$fdate' and emp_id='$q[emp_id]' and status='1' and TIME(time) between '$ftime' and '$ttime' order by slno");	
			while($dt=mysqli_fetch_array($det))
			{
				if($i>1){ echo "<tr>";}
				
				$dt_out=mysqli_fetch_array(mysqli_query($link,"select * from login_activity where date='$fdate' and emp_id='$q[emp_id]' and status='0' and slno>$dt[slno] order by slno"));
				
				?>
				<td><?php echo convert_date($dt[date])." / ".convert_time($dt[time]); ?></td>
				<td><?php if($dt_out[date]){ echo convert_date($dt_out[date])." / ".convert_time($dt_out[time]); } ?></td>
				
				
				</tr>
				<?php
				$i++;
					
			}
			
			
		}
	}
	else
	{
		$d_date=1;
		$dates=mysqli_query($link,"select distinct date from login_activity where date between '$fdate' and '$tdate'");
		$tot_date=mysqli_num_rows($dates);
		while($dd=mysqli_fetch_array($dates))
		{
			
			?> <tr> <td style="text-align:center;background-color:#CCC" colspan="4"><b><?php echo convert_date($dd[date]);?></b></td> </tr> <?php
			
			if($d_date==1)
			{
				$str=mysqli_query($link,"select distinct emp_id from login_activity where date='$dd[date]' and time>'$ftime'");
			}
			else if($d_date==$tot_date)
			{
				$str=mysqli_query($link,"select distinct emp_id from login_activity where date='$dd[date]' and time<'$ttime'");
			}
			else
			{
				$str=mysqli_query($link,"select distinct emp_id from login_activity where date='$dd[date]'");
			}
			
			while($q=mysqli_fetch_array($str))
			{
				$i=1;
				$ename=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$q[emp_id]'"));
				
				if($d_date==1)
				{
					$tot=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from login_activity where emp_id='$q[emp_id]' and status='1' and date='$dd[date]' and time>'$ftime'"));
				}
				else if($d_date==$tot_date)
				{
					$tot=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from login_activity where emp_id='$q[emp_id]' and status='1' and date='$dd[date]' and time<'$ttime'"));
				}
				else
				{
					$tot=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from login_activity where emp_id='$q[emp_id]' and status='1' and date='$dd[date]'"));
				}
								
				if($tot[tot]>0)
				{
					if($i==1)
					{
						?>	
						<tr>
							<td rowspan="<?php echo $tot[tot];?>"><?php echo $ename[name];?></td>
						<?php
					}
					
					
					if($d_date==1)
					{
						$det=mysqli_query($link,"select * from login_activity where emp_id='$q[emp_id]' and status='1' and date='$dd[date]' and time>'$ftime' order by slno");	
					}
					else if($d_date==$tot_date)
					{
						$det=mysqli_query($link,"select * from login_activity where emp_id='$q[emp_id]' and status='1' and date='$dd[date]' and time<'$ttime' order by slno");	
					}
					else
					{
						$det=mysqli_query($link,"select * from login_activity where emp_id='$q[emp_id]' and status='1' and date='$dd[date]'");
					}
					
					while($dt=mysqli_fetch_array($det))
					{
						if($i>1){ echo "<tr>";}
						
					
						if($d_date==1)
						{
							$dt_out=mysqli_fetch_array(mysqli_query($link,"select * from login_activity where emp_id='$q[emp_id]' and status='0' and date='$dd[date]' and time>'$ftime' and slno>$dt[slno] order by slno"));
						}
						else if($d_date==$tot_date)
						{
							$dt_out=mysqli_fetch_array(mysqli_query($link,"select * from login_activity where emp_id='$q[emp_id]' and status='0' and date='$dd[date]' and slno>$dt[slno] order by slno"));
						}
						else
						{
							$dt_out=mysqli_fetch_array(mysqli_query($link,"select * from login_activity where emp_id='$q[emp_id]' and status='0' and date='$dd[date]' and slno>$dt[slno] order by slno"));
						}
						
						
						?>
						<td><?php echo convert_date($dt[date])." / ".convert_time($dt[time]); ?></td>
						<td><?php if($dt_out[date]) { echo convert_date($dt_out[date])." / ".convert_time($dt_out[time]); } ?></td>
						
						
						</tr>
						<?php
						$i++;
							
					}
					
					if($i==1)
					{
						echo "</tr>";
					}
				}
				
			}
			
			$d_date++;
		}
		
		
	}
	?>
	
	</table>
	
	<?php
}


?>
