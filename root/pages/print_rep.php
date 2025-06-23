<?php
session_start();
$emp_id=$_SESSION["emp_id"];
$date=date("Y-m-d");

include('../../includes/connection.php');
include('../../includes/global.function.php');

$user=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$emp_id' "));

$uhid=$_GET['uhid'];
$pin=$opd_id=$_GET['visitid'];
$cmpny=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select centrename from centremaster where centreno='$centr'"));
$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$pat_bill=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `bill_no`,`date`,`time` FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' AND `typeofpayment`='A' "));
$ref_doc=mysqli_fetch_array(mysqli_query($link," SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid` in ( SELECT `refbydoctorid` FROM `patient_info` WHERE `patient_id`='$uhid' ) "));
$appointment_qry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' "));
$appointment_no=$appointment_qry["appointment_no"];
$appointment_no = str_pad($appointment_no,2,"0",STR_PAD_LEFT);

$app_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `Name`,`dept_id`,`room_id` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment_qry[consultantdoctorid]' "));
$doc_dept=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$app_doc[dept_id]' "));
$opd_room=mysqli_fetch_array(mysqli_query($link, " SELECT `room_name` FROM `opd_doctor_room` WHERE `room_id`='$app_doc[room_id]' "));
$adv_paid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));
$visit_fee=mysqli_fetch_array(mysqli_query($link, " SELECT `visit_fee` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' "));
if($visit_fee["visit_fee"]==0)
{
	$visit_fee=0;
	$visit_type="Free";
}else
{
	$visit_fee=$visit_fee["visit_fee"];
	$visit_fee_num=mysqli_num_rows(mysqli_query($link, " SELECT `visit_fee` FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' AND `visit_fee`>0 "));
	//~ if($visit_fee_num==1)
	//~ {
		//~ $visit_type="First visit";
	//~ }
	//~ if($visit_fee_num==2)
	//~ {
		//~ $visit_type="Second visit";
	//~ }
	//~ if($visit_fee_num==3)
	//~ {
		//~ $visit_type="Third visit";
	//~ }
	function numToOrdinalWord($num)
	{
		$first_word = array('eth','First','Second','Third','Fouth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Elevents','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
		$second_word =array('','','Twenty','Thirthy','Forty','Fifty');

		if($num <= 20)
			return $first_word[$num];

		$first_num = substr($num,-1,1);
		$second_num = substr($num,-2,1);

		return $string = str_replace('y-eth','ieth',$second_word[$second_num].'-'.$first_word[$first_num]);
	}
	//echo $visit_fee_num;
	$visit_type=numToOrdinalWord($visit_fee_num)." visit";
}

$check_last_regd_fee_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `date` FROM `appointment_book` WHERE `patient_id`='$uhid' order by `slno` DESC limit 0,1 ");
$check_last_regd_fee_num=mysqli_num_rows($check_last_regd_fee_qry);

$dates_array = getDatesFromRange("2017-09-01", $date);
$day_diff=sizeof($dates_array);

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` limit 0,1 "));
$signature="For ".$company_info['name'];
$phon="";
if($company_info["phone1"])
$phon.=$company_info["phone1"];
if($company_info["phone2"])
$phon.=", ".$company_info["phone2"];
if($company_info["phone3"])
$phon.=", ".$company_info["phone3"];

$header2="                       ".$company_info["address"].", ".$company_info["city"].", ".$company_info["state"]."-".$company_info["pincode"];
$header3="     Phone Number(s): ".$phon."Email: ".$company_info["email"];
$consult_name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` in ( SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ) "));
?>
<html>
	<head>
	<title>Report Print</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<body>
		<b><i>Close this page when printing is done</i></b>
		
<?php
$age=$pat_info["age"]." ".$pat_info["age_type"];
$line="------------------------------------------------------------------------------";

$company_name="                         ".$company_info["name"];

$uhid_len=strlen($pat_info["uhid"]);
$uhid_len1=9-$uhid_len;
$uhid_space="";
while($uhid_len1>0)
{
	$uhid_space.=" ";
	$uhid_len1--;
}
$pin_len=strlen($pin);
$pin_len1=8-$pin_len;
$pin_space="";
while($pin_len1>0)
{
	$pin_space.=" ";
	$pin_len1--;
}
$bill_no_len=strlen($pat_bill["bill_no"]);
$bill_no_len1=14-$bill_no_len;
$bill_no_space="";
while($bill_no_len1>0)
{
	$bill_no_space.=" ";
	$bill_no_len1--;
}
$name_sub=substr($pat_info["name"],0,20);
$pat_name_len=strlen($name_sub);
$pat_name_len1=22-$pat_name_len;
$pat_name_space="";
while($pat_name_len1>0)
{
	$pat_name_space.=" ";
	$pat_name_len1--;
}

$doc_dept_sub=substr($doc_dept["name"],0,20);
$doc_dept_len=strlen($doc_dept_sub);
$doc_dept_len1=32-$doc_dept_len;
$doc_dept_space="";
while($doc_dept_len1>0)
{
	$doc_dept_space.=" ";
	$doc_dept_len1--;
}
$app_doc_sub=substr($app_doc["Name"],0,20);
$app_doc_len=strlen($app_doc_sub);
$app_doc_len1=35-$app_doc_len;
$app_doc_space="";
while($app_doc_len1>0)
{
	$app_doc_space.=" ";
	$app_doc_len1--;
}
if($opd_room["room_name"])
{
	$top_user="Room: ".$opd_room["room_name"]."\t\t\t\t\t   User: ".$user["name"];
}else
{
	$top_user="\t\t\t\t\t\t\t   User: ".$user["name"];
}

$pat_info1="UHID: ".$pat_info["uhid"].$uhid_space."PIN: ".$pin.$pin_space." Bill No: ".$pat_bill["bill_no"].$bill_no_space." Date Time".convert_date($pat_bill["date"])." ".$pat_bill["time"];
$pat_info2="Name: ".$name_sub.$pat_name_space." Age/Sex: ".$age."/".$pat_info["sex"]."\tRef By: ".substr($ref_doc["ref_name"],0,22);

$pat_header1="Department\t\t\tConsultant\t\t\tSerial No";
$pat_header2=$doc_dept["name"].$doc_dept_space.$app_doc["Name"].$app_doc_space.$appointment_no;
$pat_header3="Visit Type: ".$visit_type."\t\tVisit Fee: ".number_format($visit_fee,2);


$rupee_text="Indian Rupees ".convert_number($adv_paid["amount"])." Only";

$age=$pat_info["age"]." ".$pat_info["age_type"];
$line="------------------------------------------------------------------------------";
$end_text="-------------------- This is a computer generated receipt -------------------";

$company_name="                         ".$company_info["name"];

$uhid_len=strlen($pat_info["uhid"]);
$uhid_len1=9-$uhid_len;
$uhid_space="";
while($uhid_len1>0)
{
	$uhid_space.=" ";
	$uhid_len1--;
}
$pin_len=strlen($opd_id);
$pin_len1=8-$pin_len;
$pin_space="";
while($pin_len1>0)
{
	$pin_space.=" ";
	$pin_len1--;
}
$bill_no_len=strlen($pat_bill["bill_no"]);
$bill_no_len1=14-$bill_no_len;
$bill_no_space="";
while($bill_no_len1>0)
{
	$bill_no_space.=" ";
	$bill_no_len1--;
}
$name_sub=substr($pat_info["name"],0,20);
$pat_name_len=strlen($name_sub);
$pat_name_len1=22-$pat_name_len;
$pat_name_space="";
while($pat_name_len1>0)
{
	$pat_name_space.=" ";
	$pat_name_len1--;
}


$app_doc_sub=substr($consult_name["Name"],0,20);
$app_doc_len=strlen($app_doc_sub);
$app_doc_len1=53-$app_doc_len;
$app_doc_space="";
while($app_doc_len1>0)
{
	$app_doc_space.=" ";
	$app_doc_len1--;
}


$consult_fee=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
$pat_info11="UHID: ".$pat_info["uhid"].$uhid_space."PIN: ".$opd_id.$pin_space." Bill No: ".$pat_bill["bill_no"].$bill_no_space." Date Time".convert_date($pat_bill["date"])." ".$pat_bill["time"];
$pat_info21="Name: ".$name_sub.$pat_name_space." Age/Sex: ".$age."/".$pat_info["sex"]."\tRef By: ".substr($ref_doc["ref_name"],0,22);

$pat_confee="Consultation Fee (".$consult_name["Name"].")".$app_doc_space.number_format($consult_fee["visit_fee"],2);

$reg_check=0;
if($consult_fee["regd_fee"]>0)
{
	$reg_check=1;
	$pat_regfee="Registration Fee\t\t\t\t\t\t\t".number_format($consult_fee["regd_fee"],2);
}
$emr_check=0;
if($consult_fee["emergency_fee"]>0)
{
	$pat_emrfee="Emergency Fee\t\t\t\t\t\t\t\t".number_format($consult_fee["emergency_fee"],2);
	$emr_check=1;
}
$pat_tot="Total\t\t\t\t\t\t\t\t\t".number_format($consult_fee["tot_amount"],2);
$dis_check=0;
if($consult_fee["dis_amt"]>0)
{
	$dis_check=1;
	$pat_dis="Discount\t\t\t\t\t\t\t\t".number_format($consult_fee["dis_amt"],2);	
}
//$pat_adv="Advance\t\t\t\t\t\t\t\t\t".number_format(($consult_fee["tot_amount"])-($adv_paid["amount"])-($consult_fee["dis_amt"]),2);
$pat_adv="Advance\t\t\t\t\t\t\t\t\t".number_format(($consult_fee["advance"]),2);
$pat_bal="Balance\t\t\t\t\t\t\t\t\t".number_format($adv_paid["balance"],2);
?>	
		
		
		<script>
			
			function printt()
			{
				var applet=document.jzebra;
				applet.append("\x1B\x45");
				applet.append("<?php echo $company_name;?>"+"\n");
				applet.append("\x1B\x40");
				
				applet.append("<?php echo $header2;?>"+"\n");
				applet.append("<?php echo $header3;?>"+"\n");
				applet.append("<?php echo $line;?>"+"\n");
				applet.append("<?php echo "\t\t\t     Doctor Requisition Report";?>"+"\n");
				applet.append("<?php echo "\t\t         This slip is for internal use only";?>"+"\n\n");
				applet.append("<?php echo $top_user;?>"+"\n");
				applet.append("<?php echo $line;?>"+"\n");
				applet.append("<?php echo $pat_info1;?>"+"\n");
				applet.append("<?php echo $pat_info2;?>"+"\n");
				applet.append("<?php echo $line;?>"+"\n");
				applet.append("<?php echo $pat_header1;?>"+"\n");
				applet.append("<?php echo $pat_header2;?>"+"\n");
				applet.append("<?php echo $pat_header3;?>"+"\n");
				
				applet.append("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n");
				
				
				
				applet.append("\x1B\x45");
				applet.append("<?php echo $company_name;?>"+"\n");
				applet.append("\x1B\x40");
				
				applet.append("<?php echo $header2;?>"+"\n");
				applet.append("<?php echo $header3;?>"+"\n");
				applet.append("<?php echo $line;?>"+"\n");
				applet.append("<?php echo "\t\t\t     OPD Consultation Receipt";?>"+"\n");
				
				applet.append("<?php echo $line;?>"+"\n");
				applet.append("<?php echo $pat_info11;?>"+"\n");
				applet.append("<?php echo $pat_info21;?>"+"\n");
				applet.append("<?php echo $line;?>"+"\n");
				var reg=<?php echo $reg_check; ?>;
				if(reg=='1')
				{
					applet.append("<?php echo $pat_regfee;?>"+"\n");
				}
				var emr=<?php echo $emr_check; ?>;
				if(emr=='1')
				{
					applet.append("<?php echo $pat_emrfee;?>"+"\n");
				}
				
				applet.append("<?php echo $pat_confee;?>"+"\n");
				applet.append("<?php echo $pat_tot;?>"+"\n");
				var dis=<?php echo $dis_check; ?>;
				if(dis=='1')
				{
					applet.append("<?php echo $pat_dis;?>"+"\n");
				}
				applet.append("<?php echo $pat_adv;?>"+"\n");
				applet.append("<?php echo $pat_bal;?>"+"\n\n");
				applet.append("<?php echo $line;?>"+"\n");
				applet.append("<?php echo $rupee_text;?>"+"\n\n\n\n\n");
				//applet.append("<?php echo "\t\t\t\t\t".$signature;?>"+"\n\n");
				applet.append("<?php echo $end_text;?>"+"\n");
				
				applet.append("\n\n\n\n\n\n\n\n\n\n\n\n");
				
				var dis=<?php echo $dis_check; ?>;
				if(dis=='0')
				{
					applet.append("\n");
				}
				var reg=<?php echo $reg_check; ?>;
				if(reg=='0')
				{
					applet.append("\n");
				}
				var emr=<?php echo $emr_check; ?>;
				if(emr=='0')
				{
					applet.append("\n");
				}
				
				applet.print();
				window.close();
			}
			</script>	
			
		
		<applet name="jzebra" code="jzebra.PrintApplet.class" archive="./jzebra.jar" width="50px" height="50px">
		  <param name="printer" value="zebra">
	   </applet>
	   
	   <script>printt()</script>
	</body>
</html>
