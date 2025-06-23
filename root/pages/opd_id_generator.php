<?php
	
	// OPD ID Generator
	
	$date_str=explode("-", $date);
	$dis_year=$date_str[0];
	$dis_month=$date_str[1];
	$dis_year_sm=convert_date_only_sm_year($date);
	
	$c_y_m=$dis_year."-".$dis_month;
	
	$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_y_m%' "));
	if(!$c_data)
	{
		mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
		
		mysqli_query($link, " INSERT INTO `pin_generator`(`slno`, `patient_id`, `type`, `user`, `date`, `time`) VALUES ('100','0','$patient_reg_type','0','$date','$time') ");
	}

	mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$patient_id','$patient_reg_type','$user','$date','$time') ");
	
	$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
	
	$last_slno=$last_slno["slno"];
	
	//~ $last_slno = str_pad($last_slno,3,"0",STR_PAD_LEFT);
	
	//~ $alphabets = range('A', 'Z');
	
	//~ $month_slno=intval(date("m",strtotime($date))-1);
	//~ $month_character=$alphabets[$month_slno];
	
	//~ $opd_id=$last_slno."/".$month_character."/".date("y",strtotime($date));
	
	$opd_id=$last_slno."/".$dis_month.$dis_year_sm;
	
	// OPD Serial Generator
	
	$table_name="serial_generator_".$patient_reg_type;
	
	$opd_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `$table_name` WHERE `date`='$date'"));
	if(!$opd_data)
	{
		mysqli_query($link, " TRUNCATE TABLE `$table_name` ");
	}

	mysqli_query($link, " INSERT INTO `$table_name`(`patient_id`, `user`, `date`, `time`) VALUES ('$patient_id','$user','$date','$time') ");
	
	$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `$table_name` WHERE `patient_id`='$patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
	
	//$serial_no=$last_slno["slno"];
	
	$serial_no = $serial_no_prefix.str_pad($last_slno["slno"],3,"0",STR_PAD_LEFT);
	
	
	//$serial_no="";
?>
