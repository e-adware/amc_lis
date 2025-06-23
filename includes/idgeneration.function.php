<?php
	function generate_uhid($user)
	{
		include("connection.php");
		
		$pat_tot_num_qry="";
		
		$patient_id=100;
	
		$dis_month=date("m");
		$dis_year=date("Y");
		$dis_year_sm=date("y");
		$c_m_y=$dis_year."-".$dis_month;
		$pat_tot_num_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`patient_id`) as tot FROM `patient_info` WHERE `date` like '$c_m_y%' "));
		$pat_tot_num=$pat_tot_num_qry["tot"];
		
		if($pat_tot_num==0)
		{
			$patient_id=$patient_id+1;
		}else
		{
			$patient_id=$patient_id+$pat_tot_num+1;
		}
		
		$new_patient_id=$patient_id.$dis_month.$dis_year_sm.$user;
		return $new_patient_id;
	}
	function generate_pin($user)
	{
		include("connection.php");
		
		$pat_tot_num_qry="";
		
		$opd_idds=100;
			
		$dis_month=date("m");
		$dis_year=date("Y");
		$dis_year_sm=date("y");
		$c_m_y=$dis_year."-".$dis_month;
		$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
		$opd_id_num=$opd_id_qry["tot"];
		
		$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
		$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
		
		$pat_tot_num=$opd_id_num+$opd_id_cancel_num;
		
		if($pat_tot_num==0)
		{
			$opd_idd=$opd_idds+1;
		}else
		{
			$opd_idd=$opd_idds+$pat_tot_num+1;
		}
		$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
		return $opd_id;
	}
	function generate_bill_no($bill_type,$val)
	{
		include("connection.php");
		
		$pat_tot_num_qry="";
		
		$bill_no=101;
		$date2=date("Y-m-d");
		$date1=explode("-",$date2);	
		$c_var=$date1[0]."-".$date1[1];
		if($val==1)
		{
			$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from consult_payment_detail where date like '$c_var%'"));
		}
		if($val==2)
		{
			$chk=mysqli_fetch_array(mysqli_query($link, "select count(patient_id) as tot_bill from invest_payment_detail where date like '$c_var%'"));
		}
		if($chk['tot_bill']>0)
		{
			$bill_no=$bill_no+$chk['tot_bill'];
		}
		$date4=date("y-m-d");
		$date3=explode("-",$date4);
		
		$random_no=rand(1,9);
		
		$bill_no=trim($bill_no).$random_no."/".trim($date3[1])."/".trim($date3[0]).trim()."/".$bill_type;
		
		return $bill_no;
	}
	function generate_serial_lab($opd_serial)
	{
		include("connection.php");
		
		$pat_tot_num_qry="";
		
		$today=date("Y-m-d");
		$opd_serial_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `ipd_serial`='$opd_serial' AND `date`='$today' "));
		
		if($opd_serial_check)
		{
			$serial=0;
			$dis_day=date("d");
			$dis_month=date("m");
			$dis_year=date("Y");
			$dis_year_sm=date("y");
			$c_m_y=$dis_year."-".$dis_month."-".$dis_day;
			$opd_serial_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`ipd_serial`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
			$opd_serial_num=$opd_serial_qry["tot"];
			
			$opd_serial_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`ipd_serial`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
			$opd_serial_cancel_num=$opd_serial_qry_cancel["tot"];
			
			$pat_serial_num=$opd_serial_num+$opd_serial_cancel_num;
			
			if($pat_serial_num==0)
			{
				$opd_serial=$serial+1;
			}else
			{
				$opd_serial=$serial+$pat_serial_num+1;
			}
		}
		
		return $opd_serial;
	}
	
	function generate_bill_no_new($bill_type,$val)
	{
		include("connection.php");
		
		$pat_tot_num_qry="";
		
		$bill_no=101;
		$today=date("Y-m-d");
		
		$chk=mysqli_fetch_array(mysqli_query($link, "select count(pay_id) as tot_bill from payment_detail_all where date like '$today'"));
		
		if($chk['tot_bill']>0)
		{
			$bill_no=$bill_no+$chk['tot_bill'];
		}
		
		$random_no=rand(1,9);
		
		$bill_no=trim($bill_no).$random_no."/".date("d/m/Y")."/".$bill_type;
		
		return $bill_no;
	}
?>
