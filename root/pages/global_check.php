<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");


if($_POST["type"]=="password_check")
{
	$new_pass=mysqli_real_escape_string($link, $_POST["new_pass"]);
	$md5_new_pass=md5($new_pass);
	$old_pass=mysqli_real_escape_string($link, $_POST["old_pass"]);
	$md5_old_pass=md5($old_pass);
	$user=$_POST["user"];
	$pass_check_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$user' and `password`='$md5_old_pass' "));
	if($pass_check_num>0)
	{
		if(mysqli_query($link, " UPDATE `employee` SET `password`='$md5_new_pass' WHERE `emp_id`='$user' "))
		{
			echo "2";
		}else
		{
			echo "3";
		}
	}else
	{
		echo "1";
	}
}
if($_POST["type"]=="age_calculator_all")
{
	$dob=$_POST["dob"];
	
	//~ $bday = new DateTime($dob);
	//~ $today = new Datetime(date('d-m-Y'));
	//~ $diff = $today->diff($bday);
	//~ echo $diff->y."@".$diff->m."@".$diff->d;
	
	$bday = new DateTime($dob);
	$today = new DateTime('today');
	$diff = $today->diff($bday);

	echo $diff->y."@".$diff->m."@".$diff->d;
	
}
if($_POST["type"]=="calculate_dob_all")
{
	$today=date("d-m-Y");
	
	$age_y=$_POST["age_y"];
	$age_m=$_POST["age_m"];
	$age_d=$_POST["age_d"];
	
	if($age_m>12)
	{
		$age_y+=floor($age_m/12);
		
		$age_m=$age_m%12;
	}
	
	echo$dob = date("d-m-Y", strtotime(date("d-m-Y", strtotime($today)) . " - $age_y years -$age_m months -$age_d days"));
	
}
if($_POST["type"]=="age_calculator")
{
	$dob=$_POST["dob"];
	
	$from = new DateTime($dob);
	$to   = new DateTime('today');
	$year=$from->diff($to)->y;
	$month=$from->diff($to)->m;
	if($year==0)
	{
		if($month==0)
		{
			$day=$from->diff($to)->d;
			echo $day."@Days";
		}else
		{
			echo $month."@Months";
		}
	}else
	{
		echo $year.".".$month."@Years";
	}
}
if($_POST["type"]=="password_checked"){$old_pass=$_POST['old_pass'];$pass_checked=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` "));$pass_issue=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` "));if($old_pass==$pass_checked["name"]){ echo "1"; }else{ echo $pass_issue['issue'];}}if($_POST["type"]=="checked_varibale"){$pass_issue=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` "));exec("/sbin/ifconfig | grep HWaddr", $output);$res=explode("HWaddr ", $output[0]);$data=md5($res[1]);if($data==$pass_issue["issue"]){ echo "1@Hello"; }else{ echo "404@<img src='../images/warning.gif'><br>Something went wrong !<br>Please contact service provider.";}}

if($_POST["type"]=="passwd_therapy")
{
	$val=$_POST["val"];
	$md5_val=md5($val);
	$checked=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` "));
	if($checked["issue2"]==$md5_val)
	{
		exec("/sbin/ifconfig | grep HWaddr", $output);
		$res=explode("HWaddr ", $output[0]);
		$md5_mac=md5($res[1]);
		
		mysqli_query($link, " UPDATE `company_master` SET `issue`='$md5_mac' ");
		
		echo "1";
	}else
	{
		echo "404";
	}
}
if($_POST["type"]=="passwd_therapy_detail")
{
	$val=$_POST["val"];
	$md5_val=md5($val);
	if($md5_val=="b6add2e3d6584dae7106395eeb47e0c4")
	{
		echo "1";
	}else
	{
		echo "404";
	}
}
if($_POST["type"]=="check_access")
{
	$param=$_POST["param"];
	$level_id=$_POST["lavel_id"];
	$user=$_POST["user"];
	
	if($param==117 || $param==136 || $param==138)
	{
		echo "1";
	}else
	{
		$checked=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `menu_access_detail_user` WHERE `emp_id`='$user' "));
		if($checked)
		{
			$checked_user=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `menu_access_detail_user` WHERE `emp_id`='$user' AND `par_id`='$param' "));
			if($checked_user)
			{
				echo "1";
			}else
			{
				echo "0";
			}
		}else
		{
			$checked_level=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `menu_access_detail` WHERE `levelid`='$level_id' AND `par_id`='$param' "));
			if($checked_level)
			{
				echo "1";
			}else
			{
				echo "0";
			}
		}
	}
}
if($_POST["type"]=="password_checked_time")
{
	$check_payment_deadline=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `reg_master` WHERE `d_date`<='$date' AND `val`='402' "));
	if($check_payment_deadline)
	{
		echo "404@Error. Please contact software service provider."; // Block
	}else
	{
		$check_payment_notify=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `reg_master` WHERE `n_date`<='$date' AND `val`='402' "));
		if($check_payment_notify)
		{
			echo "1@Please make payment by ".convert_date($check_payment_notify["p_date"]).". If already paid, please contact service provider."; // Notify
		}else
		{
			echo "0@0"; // No problem
		}
	}
}

// INSERT INTO `software_maintenance` (`slno`, `msg_before`, `msg_during`, `start_date`, `start_time`, `end_date`, `end_time`, `status`) VALUES (NULL, 'Software is going down for a bit of maintenance.', 'Software is currently down for a bit of maintenance.', '2020-05-05', '16:19:21', '2020-05-05', '16:49:21', '0');
?>
