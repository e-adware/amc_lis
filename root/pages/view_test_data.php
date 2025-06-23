<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

if($_POST["type"]=="out_pat_test_rate")
{
	$all_tst=$_POST['all_tst'];
	if($all_tst!='')
	{
		$all_tst=explode("###",$all_tst);
?>
		<h5>Selected Test(s)</h5>
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>Test Name</th>
				<th>Test Rate</th>
			</tr>
<?php
		$tot="";
		foreach($all_tst as $tst)
		{
			if($tst)
			{
				$tst=explode("@@",$tst);
				echo "<tr><td>$tst[0]</td><td>&#x20b9; $tst[1]</td></tr>";
				$tot=$tot+$tst[1];
			}
		}
	?>
			<tr><th>Total</th><th>&#x20b9; <?php echo $tot; ?>.00</th></tr>
		</table>
		<button class="btn btn-mini btn-danger text-right" onClick="clear_cart()">Clear</button>
	<?php
	}
}

?>
