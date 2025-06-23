<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$uhid=$_GET["uhid"];
$opd_id=$_GET["opdid"];

$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master` limit 0,1 "));

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$add_info[district]' "));
$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$add_info[state]' "));
$address=$add_info["city"].", ".$dist_info["name"].", ".$st_info["name"];

$guadian_name=$pat_info["gd_name"];
$guadian_phone=$add_info["gd_phone"];

$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$dt_tm[refbydoctorid]' "));
if(!$ref_doc)
{
	$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_info[refbydoctorid]' "));
}

$consult_fee=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$appointment_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$consult_name=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$appointment_info[consultantdoctorid]' "));

$consult_dept=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `doctor_specialist_list` WHERE `speciality_id`='$consult_name[dept_id]' "));

$dept_name=$consult_dept['name'];

$token = str_pad($appointment_info['appointment_no'],2,"0",STR_PAD_LEFT);

$adv_paid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' "));

$rupee_text="Indian Rupees ".convert_number($adv_paid["amount"])." Only";

$age=$pat_info["age"]." ".$pat_info["age_type"];
$line="------------------------------------------------------------------------------";
$end_text="-------------------- This is a computer generated receipt -------------------";

$company_name="                         ".$company_info["name"];

$uhid_len=strlen($pat_info["patient_id"]);
$uhid_len1=12-$uhid_len;
$uhid_space="";
while($uhid_len1>0)
{
	$uhid_space.=" ";
	$uhid_len1--;
}
$pin_len=strlen($opd_id);
$pin_len1=10-$pin_len;
$pin_space="";
while($pin_len1>0)
{
	$pin_space.=" ";
	$pin_len1--;
}
$bill_no_len=strlen($adv_paid["bill_no"]);
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


$address_sub=substr($address,0,30);
$address_len=strlen($address_sub);
$address_len1=53-$address_len;
$address_space="";
while($address1>0)
{
	$address_space.=" ";
	$address_len1--;
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

$guadian_name_len=strlen($guadian_name);
$guadian_name_rest_len=(39-$guadian_name_len);
$guadian_name_space="";
while($guadian_name_rest_len>0)
{
	$guadian_name_space.=" ";
	$guadian_name_rest_len--;
}


//$pat_info1="UHID: ".$pat_info["patient_id"].$uhid_space."PIN: ".$opd_id.$pin_space." Bill No: ".$adv_paid["bill_no"].$bill_no_space." Date Time".convert_date($adv_paid["date"])." ".$adv_paid["time"];

$pat_info1="UHID     : ".$pat_info["patient_id"].$uhid_space."PIN : ".$opd_id.$pin_space;
$pat_info2="Name     : ".$name_sub.$pat_name_space." Age/Sex: ".$age."/".$pat_info["sex"];

$pat_address="Address  : ".$address.$address_space."Phone : ".$pat_info["phone"];
$pat_guardian="Guardian : ".$guadian_name.$guadian_name_space."Phone : ".$guadian_phone;

$con_docc="Doctor :".$consult_name["Name"].$app_doc_space."Department : ".$dept_name;
$token_no="Token No :".$token;

$reg_check=0;
if($consult_fee["regd_fee"]>0)
{
	$reg_check=1;
	$pat_regfee="Registration Fee\t\t\t\t\t\t\t".$consult_fee["regd_fee"];
}
$emr_check=0;
if($consult_fee["emergency_fee"]>0)
{
	$pat_emrfee="Emergency Fee\t\t\t\t\t\t\t".$consult_fee["emergency_fee"];
	$emr_check=1;
}

$dis_check=0;
if($consult_fee["dis_amt"]>0)
{
	$dis_check=1;
	$pat_dis="Discount\t\t\t\t\t\t\t\t\t".$consult_fee["dis_amt"];
}

$cross_consult=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cross_consultation` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

$cross_check=0;
if($cross_consult)
{
	$pat_cross_fee="Cross Consultation Fee\t\t\t\t\t\t\t".$cross_consult["amount"];
	$cross_check=1;
}

$pat_tot="Total amount\t\t\t\t\t\t\t\t\t".$consult_fee["tot_amount"];

$pat_adv="Paid amount\t\t\t\t\t\t\t\t\t".$consult_fee["advance"];
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
				applet.append("<?php echo "\t\t\t     Money Receipt";?>"+"\n");
				
				applet.append("<?php echo $line;?>"+"\n");
				applet.append("<?php echo $pat_info1;?>"+"\n");
				applet.append("<?php echo $pat_info2;?>"+"\n");
				applet.append("<?php echo $pat_address;?>"+"\n");
				applet.append("<?php echo $pat_guardian;?>"+"\n");
				applet.append("<?php echo $con_docc;?>"+"\n");
				applet.append("<?php echo $token_no;?>"+"\n");
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
				var cross=<?php echo $cross_check; ?>;
				if(cross=='1')
				{
					applet.append("<?php echo $pat_cross_fee;?>"+"\n");
				}
				
				applet.append("<?php echo $line;?>"+"\n");
				applet.append("<?php echo $pat_tot;?>"+"\n");
				var dis=<?php echo $dis_check; ?>;
				if(dis=='1')
				{
					applet.append("<?php echo $pat_dis;?>"+"\n");
				}
				applet.append("<?php echo $pat_adv;?>"+"\n");
				applet.append("\n\n\n\n\n\n\n\n\n\n");
				
				var dis=<?php echo $dis_check; ?>;
				if(dis=='0')
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
<script>//window.print()</script>
<style>

.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-top:20px;
	margin-bottom:10px;
}
hr
{
	margin:0;
	padding:0;
	border-bottom:0;
	border-top: 1px solid #000;
}
</style>
