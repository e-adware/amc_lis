<html>
<head>

</head>
<body>

<h2>Generating Barcode </h2>

<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$uhid=base64_decode($_GET['uhid']);
$opd_id=base64_decode($_GET['opd_id']);
$barcode_num=base64_decode($_GET['barcode_num']);
$user=base64_decode($_GET['user']);

$pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id'"));
$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$reg_date=$pat_reg["date"];
$paddrss=substr($pat_info['city'],0,30);
if($pat_info["dob"]!=""){ $age=age_calculator_date_only($pat_info["dob"],$reg_date); }
    
    $age=substr($age,0,4); 
    $pat_info["sex"]=substr($pat_info["sex"],0,1); 
    echo$target_file="../../js_print/patient_barcode.php?barcode_num=".$barcode_num."&name=".$pat_info["name"]."&age=".$age."&dob=".$pat_info["dob"]."&sex=".$pat_info["sex"]."&uhid=".$uhid."&opd_id=".$opd_id."&reg_time=".date("d-m-Y",strtotime($pat_reg["date"]))."&paddrss=".$paddrss;
?>
	<script>
		window.location="<?php echo $target_file;?>";
	</script>
</body>
</html>
