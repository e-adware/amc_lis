<?php
include("../../includes/connection.php");
include("../../includes/connection_online_sync.php");

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="save_client_complaint")
{
	if($link_error=="" && $link2_error=="")
	{
	
		$complaint_title=$_POST['complaint_title'];
		$complaint_title= str_replace("'", "''", "$complaint_title");
		$complaint_text=$_POST['complaint_text'];
		$complaint_text= str_replace("'", "''", "$complaint_text");
		$user=$_POST['user'];
		
		$client=mysqli_fetch_array(mysqli_query($link," SELECT `client_id` FROM `company_name` "));
		$client_id=$client['client_id'];
		
		// Complaint ID
			$complaint_idd=100;
			$dis_month=date("m");
			$dis_year=date("Y");
			$dis_year_sm=date("y");
			$c_m_y=$dis_year."-".$dis_month;
			$complaint_tot_num_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`complaint_id`) as tot FROM `client_complaint_master` WHERE `date` like '$c_m_y%' "));
			$complaint_tot_num=$complaint_tot_num_qry["tot"];
			
			if($complaint_tot_num==0)
			{
				$complaint_ids=$complaint_idd+1;
			}else
			{
				$complaint_ids=$complaint_idd+$complaint_tot_num+1;
			}
		
		$complaint_id=$complaint_ids.$dis_month.$dis_year_sm.$client_id; // rowcount.month.year.client_id
		
		if(mysqli_query($link2," INSERT INTO `client_complaint_master`(`complaint_id`, `complaint_title`, `complaint_text`, `date`, `time`, `user`, `status`, `client_id`) VALUES ('$complaint_id','$complaint_title','$complaint_text','$date','$time','$user','0','$client_id') "))
		{
			mysqli_query($link," INSERT INTO `client_complaint_master`(`complaint_id`, `complaint_title`, `complaint_text`, `date`, `time`, `user`, `status`) VALUES ('$complaint_id','$complaint_title','$complaint_text','$date','$time','$user','0') ");
			echo "1@@@".$complaint_id; // Success
		}else
		{
			echo "4@@@0".$complaint_id; // Connection too slow
		}
	}else
	{
		echo "4@@@0"; // No Internet Error Code
	}
}

if($_POST["type"]=="load_all_complaints")
{
	$client=mysqli_fetch_array(mysqli_query($link," SELECT `client_id` FROM `company_name` "));
	$client_id=$client['client_id'];
	
	$from=$_POST['from'];
	$to=$_POST['to'];
	
	$qry="SELECT * FROM `client_complaint_master` WHERE `slno`>0";
	
	if($from && $to)
	{
		$qry.=" AND `date` BETWEEN '$from' AND '$to'";
	}else
	{
		$qry.=" ORDER BY `status`,`slno` DESC limit 0,20";
	}
	
	//echo $qry;
	
	$all_complaint_qry=mysqli_query($link, $qry);
	
?>
	<table class="table table-condensed">
		<tr>
			<th>#</th>
			<th>Token ID</th>
			<th>Tilte</th>
			<th>Date Time</th>
			<th>View</th>
			<th>Status</th>
		</tr>
<?php
	$n=1;
	while($all_complaint=mysqli_fetch_array($all_complaint_qry))
	{
		if($all_complaint["status"]=='0')
		{
			$status_str="Open";
			$style_str="";
		}else{
			$status_str="Closed";
			$style_str="background: #ddd;font-weight: bold;";
		}
	?>
		<tr style="<?php echo $style_str; ?>">
			<td><?php echo $n; ?></td>
			<td><?php echo $all_complaint['complaint_id']; ?></td>
			<td><?php echo $all_complaint['complaint_title']; ?></td>
			<td><?php echo convert_date($all_complaint['date']); ?> <?php echo convert_time($all_complaint['time']); ?></td>
			<td><button class="btn btn-info" onClick="view_each_complain('<?php echo $client_id; ?>','<?php echo $all_complaint["complaint_id"]; ?>')">View</button></td>
			<td><?php echo $status_str; ?></td>
		</tr>
	<?php
		$n++;
	}
?>
	</table>
<?php
}

if($_POST['type']=="save_conversation_client")
{
	$conversation_text=$_POST["conversation_text"];
	$conversation_text= str_replace("'", "''", "$conversation_text");
	$client_id=$_POST["client_id"];
	$complaint_id=$_POST["complaint_id"];
	$user=$_POST["user"];
	
	if($link_error=="" && $link2_error=="")
	{
	
		// replier = 0 for company
		if(mysqli_query($link2, " INSERT INTO `client_complaint_replies`(`complaint_id`, `reply_text`, `user`, `client_id`, `date`, `time`, `replier`) VALUES ('$complaint_id','$conversation_text','$user','$client_id','$date','$time','1') "))
		{
			mysqli_query($link, " INSERT INTO `client_complaint_replies`(`complaint_id`, `reply_text`, `user`, `date`, `time`, `replier`, `rel_slno`) VALUES ('$complaint_id','$conversation_text','$user','$date','$time','1','0') ");
			
			echo "1"; // Success
		}else
		{
			echo "404"; // Error
		}
	}else
	{
		echo "4"; // No Internet Error Code
	}
	
}

if($_POST['type']=="close_complaint")
{
	$val=$_POST["val"];
	$client_id=$_POST["client_id"];
	$complaint_id=$_POST["complaint_id"];
	$user=$_POST["user"];
	
	if($link_error=="" && $link2_error=="")
	{
	
		$status=0;
		if($val>0)
		{
			$status=$user;
			
			// First check if already closed
			$complaint_status=mysqli_fetch_array(mysqli_query($link2," SELECT `status` FROM `client_complaint_master` WHERE `client_id`='$client_id' AND `complaint_id`='$complaint_id' "));
			$closed_token=$complaint_status['status'];
		}
		
		if($closed_token==0)
		{
			// replier = 0 for company
			if(mysqli_query($link2, " UPDATE `client_complaint_master` SET `status`='$status' WHERE `complaint_id`='$complaint_id' AND `client_id`='$client_id' "))
			{
				mysqli_query($link2, " INSERT INTO `client_complaint_close`(`complaint_id`, `client_id`, `user`, `date`, `time`, `type`, `closer`) VALUES ('$complaint_id','$client_id','$user','$date','$time','$val','1') ");
				
				mysqli_query($link, " UPDATE `client_complaint_master` SET `status`='$status' WHERE `complaint_id`='$complaint_id' ");
				
				mysqli_query($link, " INSERT INTO `client_complaint_close`(`complaint_id`, `user`, `date`, `time`, `type`, `rel_slno`, `closer`) VALUES ('$complaint_id','$user','$date','$time','$val','0','1') ");
				
				echo "1"; // Success
			}else
			{
				echo "404"; // Error
			}
		}else
		{
			echo "2"; // already closed
		}
	}else
	{
		echo "4"; // No Internet Error Code
	}
}

if($_POST["type"]=="synchronize_all_complaints")
{
	if($link_error=="" && $link2_error=="")
	{
		$date1=date("Y-m-d");
		$date1 = strtotime(date("Y-m-d", strtotime($date1)) . " -3 months");
		$previous_date=date("Y-m-d",$date1); // Date before 3 months
		
		$client=mysqli_fetch_array(mysqli_query($link," SELECT `client_id` FROM `company_name` "));
		$client_id=$client['client_id'];
		
		// replier=0 means company's reply
		$reply_qry=mysqli_query($link2," SELECT * FROM `client_complaint_replies` WHERE `client_id`='$client_id' AND `replier`='0' AND `date` BETWEEN '$previous_date' AND '$date' ");
		while($reply_val=mysqli_fetch_array($reply_qry))
		{
			$company_reply_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `client_complaint_replies` WHERE `rel_slno`='$reply_val[slno]' "));
			if($company_reply_num=='0')
			{
				$reply_text= str_replace("'", "''", $reply_val['reply_text']);
				
				mysqli_query($link," INSERT INTO `client_complaint_replies`(`complaint_id`, `reply_text`, `user`, `date`, `time`, `replier`, `rel_slno`) VALUES ('$reply_val[complaint_id]','$reply_text','$reply_val[user]','$reply_val[date]','$reply_val[time]','0','$reply_val[slno]') ");
				
			}
		}
		// closer=0 means closed by company
		$close_qry=mysqli_query($link2," SELECT * FROM `client_complaint_close` WHERE `client_id`='$client_id' AND `closer`='0' AND `date` BETWEEN '$previous_date' AND '$date' ");
		while($close_val=mysqli_fetch_array($close_qry))
		{
			$company_close_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `client_complaint_close` WHERE `rel_slno`='$close_val[slno]' "));
			if($company_close_num=='0')
			{
				mysqli_query($link," INSERT INTO `client_complaint_close`(`complaint_id`, `user`, `date`, `time`, `type`, `rel_slno`, `closer`) VALUES ('$close_val[complaint_id]','$close_val[user]','$close_val[date]','$close_val[time]','$close_val[val]','$close_val[slno]','$close_val[closer]') ");
				
			}
		}
		
		$complaint_status_qry=mysqli_query($link2," SELECT `complaint_id`,`status` FROM `client_complaint_master` WHERE `client_id`='$client_id' AND `date` BETWEEN '$previous_date' AND '$date' ");
		while($complaint_status=mysqli_fetch_array($complaint_status_qry))
		{
			mysqli_query($link," UPDATE `client_complaint_master` SET `status`='$complaint_status[status]' WHERE `complaint_id`='$complaint_status[complaint_id]' ");
		}
		
		echo "Synchronized";
		
	}else
	{
		echo "No Internet Connection, Try Again Later.";
	}
}
?>
