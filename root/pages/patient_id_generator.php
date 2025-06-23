<?php
	
	$ip_addr=$_SERVER["REMOTE_ADDR"];
	
	//$patient_id=100;
			
	$date_str=explode("-", $date);
	$dis_year=$date_str[0];
	$dis_month=$date_str[1];
	$dis_year_sm=convert_date_only_sm_year($date);
	$c_m_y=date("Y-m");
	//~ $c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_id_generator` WHERE `date` LIKE '$c_m_y%' "));
	//~ if(!$c_data)
	//~ {
		//~ //mysqli_query($link, " TRUNCATE TABLE `patient_id_generator` ");
	//~ }
	
	$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `patient_id_generator` ORDER BY `slno` DESC LIMIT 1 "));
	if(!$c_data)
	{
		mysqli_query($link, " INSERT INTO `patient_id_generator` (`slno`, `user`, `date`, `time`, `ip_addr`) VALUES ('100', '0', '0000-00-00', '00:00:00', '') ");
	}

	mysqli_query($link, " INSERT INTO `patient_id_generator`(`user`, `date`, `time`, `ip_addr`) VALUES ('$user','$date','$time','$ip_addr') ");
	
	$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `patient_id_generator` WHERE `user`='$user' AND `ip_addr`='$ip_addr' ORDER BY `slno` DESC LIMIT 1 "));
	
	$last_slno=$last_slno["slno"];
	
	//mysqli_query($link, " DELETE FROM `patient_id_generator` WHERE `slno`='$last_slno' ");
	
	$new_patient_id="".$last_slno;
	
	//~ $patient_id=$patient_id+$last_slno;
	
	//~ $new_patient_id=$patient_id.$dis_month.$dis_year_sm.$user;
	//~ $new_patient_id=trim($new_patient_id);
	
	// Serial UHID
	//~ $uhid_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_serial_generator` "));
	//~ if(!$uhid_data)
	//~ {
		//~ //mysqli_query($link, " TRUNCATE TABLE `uhid_serial_generator` ");
	//~ }

	//~ mysqli_query($link, " INSERT INTO `uhid_serial_generator`(`patient_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$user','$date','$time') ");
	
	//~ $last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `uhid_serial_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
	
	//~ mysqli_query($link, " DELETE FROM `uhid_serial_generator` WHERE `slno`='$last_slno[slno]' ");
	
	//~ $uhid_serial=$last_slno["slno"];
	$uhid_serial="";
	
?>
