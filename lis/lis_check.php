<?php
include'../includes/connection.php';
//SELECT * FROM `patient_info` WHERE `patient_id` NOT IN (SELECT DISTINCT `patient_id` FROM `test_sample_result_data`) 
$entryDate="2023-05-31";
$time=date('H:i:s', strtotime('-10 minutes'));
$j=1;
$qry="SELECT DISTINCT `barcode_id` FROM `a_patient_test_details` WHERE `date`='$entryDate' AND `barcode_id` NOT IN (SELECT DISTINCT `barcode_id` FROM `test_sample_result_data` WHERE `date`='$entryDate')";
//echo $qry;
//$q=mysqli_query($link,"SELECT DISTINCT `barcode_id` FROM `test_sample_result_data` WHERE `date`='$entryDate'");
//echo "SELECT DISTINCT `barcode_id` FROM `a_patient_test_details` WHERE `date`='$entryDate' AND `time`<'$time'";
$barcodes="";
$q=mysqli_query($link,$qry);
while($r=mysqli_fetch_assoc($q))
{
	$dt=mysqli_fetch_assoc(mysqli_query($link,"SELECT DISTINCT `date`,`time` FROM `test_sample_result_data` WHERE `barcode_id`='$r[barcode_id]'"));
	//echo "<br/>".$j.":--".$r['barcode_id'];
	//echo "<br/>".$j."..".$r['barcode_id']." SELECT `time` FROM `uhid_and_opdid` WHERE `patient_id`='$r[patient_id]' AND `date`='$entryDate'";
	//mysqli_query($link,"UPDATE `a_patient_test_details` SET `date`='$dt[date]', `time`='$dt[time]' WHERE `barcode_id`='$r[barcode_id]'");
	//echo "<br/>----- UPDATE `a_patient_test_details` SET `date`='$entryDate', `time`='$dt[time]' WHERE `patient_id`='$r[patient_id]'";
	if($barcodes)
	{
		$barcodes.=",".$r['barcode_id'];
	}
	else
	{
		$barcodes=$r['barcode_id'];
	}
	$j++;
}
//echo $barcodes;
?>
<!DOCTYPE html>
<html lang="en">
	<head><title>Penguin HIS</title></head>
	<body>
		<input type="hidden" id="barcodes" value="<?php echo $barcodes;?>" />
		<div id="res"></div>
	</body>
	<script src="../js/jquery.min.js"></script>
	<script>
		$(document).ready(function()
		{
			if($("#barcodes").val().trim()!="")
			{
				checkBarcodes();
			}
		});
		
		async function checkBarcodes()
		{
			var barcodes=$("#barcodes").val().trim();
			var allCodes=barcodes.split(",");
			for(var i=0; i<(allCodes.length); i++)
			{
				var x=await fetchBarcode(allCodes[i]);
			}
		}
		function fetchBarcode(bCode)
		{
			$.post("lis_check_ajax.php",
			{
				type:1,
				bCode:bCode,
				barcodes:$("#barcodes").val().trim()
			},
			function(data,status)
			{
				//alert("AJAX-"+data);
				$("#res").append("<br/>"+data);
				return data;
			});
		}
	</script>
</html>