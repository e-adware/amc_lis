<?php
	if($val==2)
	{			
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$cancel_request_check[user]' "));
		
		echo "<h5>Cancel request has been sent by <span style='color:red;'>".$user_info["name"]."</span> on ".date("d-M-Y", strtotime($cancel_request_check["date"]))." ".date("h:i A", strtotime($cancel_request_check["time"]))."</h5>";
		
		echo "<br>";
		
		echo "<h5>Reason : ".$cancel_request_check["remark"]."</h5>";
	}
?>
