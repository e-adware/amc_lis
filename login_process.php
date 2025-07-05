<?php
session_start();
include('includes/connection.php');

$date=date("Y-m-d");
$time=date("H:i:s");

if($_SESSION['emp_id'])
{
	echo "3"; // Already login in another tab
}else
{
	$id=$_POST['user_id'];
	
	$pword=mysqli_real_escape_string($link, $_POST['pword']);
	
	$uname=$_POST['uname'];
	
	if($pword)
	{
		$md5_pass=md5($pword);
		
		$data=mysqli_query($link, "select * from employee where emp_id='$id' and password='$md5_pass' ");
		$data1=mysqli_num_rows($data);
		if($data1>0)
		{
			$l=mysqli_fetch_array($data);
		}
		else
		{
			$data_name=mysqli_query($link,"select * from employee where emp_id='$id' and password='$pword'");
			$data1=mysqli_num_rows($data_name);
			$l=mysqli_fetch_array($data_name);
			
			if($data1>0)
			{
				mysqli_query($link," UPDATE `employee` SET `password`='$md5_pass' WHERE `emp_id`='$id' ");
			}
		}
		
		if($data1>0)
		{
			$last_login=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `login_activity` WHERE `emp_id`='$id' ORDER BY `slno` DESC limit 0,1 "));
			if($last_login)
			{
				if($last_login['status']=='0')
				{
					$access="Yes";
				}else
				{
					$access="No";
				}
			}else
			{
				$access="Yes";
			}
			if($access=="Yes")
			{
				if($l['status']==1)
				{
					echo "404"; // Account In-active
				}else
				{
					$_SESSION['emp_id']=$l["emp_id"];
					$_SESSION['emp_code']=$l["emp_code"];
					$_SESSION['levelid']=$l["levelid"];
					$_SESSION['branch_id']=$l["branch_id"];
					
					$cookie_name = "91E03M03P6I1D";
					$cookie_value = $l["emp_id"];
					setcookie($cookie_name, $cookie_value, time() + (86400 * 365 * 10), "/"); // 86400 = 1 day
					
					$cookie_name = "91E03M03P6C1D";
					$cookie_value = $l["emp_code"];
					setcookie($cookie_name, $cookie_value, time() + (86400 * 365 * 10), "/"); // 86400 = 1 day
					
					$cookie_name = "91E03M03P6PSS";
					$cookie_value = $l["password"];
					setcookie($cookie_name, $cookie_value, time() + (86400 * 365 * 10), "/"); // 86400 = 1 day
					
					// Login record
					$ip_addr=$_SERVER["REMOTE_ADDR"];
					mysqli_query($link, " INSERT INTO `login_activity`(`emp_id`, `status`, `remark`, `date`, `time`, `ip_addr`) VALUES ('$id','1','normal','$date','$time','$ip_addr') ");
					
					if($l['levelid']==13)
					{
						echo "13"; // Patho Doctor
					}
					else if($l['levelid']==5)
					{
						echo "5"; // Doctor
					}
					else if($l['levelid']==2)
					{
						echo "1"; // Reception
					}
					else
					{
						echo "1"; // Normal user
					}
				}
			}else
			{
				echo "4"; // Not logout properly or login in another PC
			}
		}
		else
		{
			echo "2"; // Error invalid inputs
		}
	}else
	{
		echo "2"; // Error invalid inputs
	}
}
?>
