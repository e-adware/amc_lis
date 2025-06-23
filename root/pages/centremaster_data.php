<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$date = date("Y-m-d"); // important
$date1 = date("Y-m-d");
$time = date("H:i:s");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date);
	$new_date = date('y', $timestamp);
	return $new_date;
}

if ($_POST["type"] == "cntermaster_id") //// cntermaster_id
{
	echo $vid = nextId("C", "centremaster", "centreno", "100");
}
if ($_POST["type"] == "cntermaster") //// cntermaster
{
	$branch_id = $_POST['branch_id'];
	$val = $_POST['val'];
	if ($val) {
		$q = "SELECT * FROM `centremaster` WHERE `centrename` like '$val%' AND `branch_id`='$branch_id'";
	} else {
		$q = "SELECT * FROM `centremaster` where `branch_id`='$branch_id' order by `centrename`";
	}

	$qrpdct = mysqli_query($link, $q);
	$i = 1;
	?>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Address</th>
			<th>Phone</th>
			<th></th>
		</tr>
		<?php
		while ($qrpdct1 = mysqli_fetch_array($qrpdct)) {
			?>
			<tr style="cursor:pointer" onclick="javascript:val_load_new('<?php echo $qrpdct1['centreno']; ?>')"
				id="rad_test<?php echo $i; ?>">
				<td id="prod<?php echo $i; ?>">
					<?php echo $qrpdct1['centreno']; ?>
				</td>
				<td>
					<?php echo $qrpdct1['centrename']; ?>
				</td>
				<td>
					<?php echo $qrpdct1['add1']; ?>
				</td>
				<td>
					<?php echo $qrpdct1['phoneno']; ?>
				</td>
				<td><span onclick="delete_data('<?php echo $qrpdct1['centreno']; ?>')"> <img height="15" width="15"
							src="../images/delete.ico" /></a></td>
			</tr>
			<?php
			$i++;
		}
		?>
	</table>
	<?php
}


if ($_POST["type"] == "cntermaster_load") //// cntermaster_load
{
	$tid = $_POST['doid1'];
	$qrm = mysqli_fetch_array(mysqli_query($link, "SELECT * from centremaster where centreno='$tid' "));
	$val = $tid . '#' . $qrm["centrename"] . '#' . $qrm["short_name"]. '#' . $qrm["add1"] . '#' . $qrm["response_person"] . '#' . $qrm["phoneno"] . '#' . $qrm["e_mail"] . '#' . $qrm["credit_limit"] . '#' . $qrm["c_discount"] . '#' . $qrm["d_patho"] . "#". $qrm["d_ultra"]. '#' . $qrm["d_xray"]. '#' . $qrm["d_cardio"]. '#' . $qrm["d_spl"]. '#' . $qrm["d_ct"]. '#' . $qrm["c_patho"]. '#' . $qrm["c_ultra"]. '#' . $qrm["c_xray"]. '#' . $qrm["c_cardio"]. '#' . $qrm["c_spl"]. '#' . $qrm["c_ct"]. '#' . $qrm["s_patho"]. '#' . $qrm["s_ultra"]. '#' . $qrm["s_xray"]. '#' . $qrm["s_cardio"]. '#' . $qrm["s_spl"]. '#' . $qrm["s_ct"]. '#' . $qrm["vacu_charge"]. '#' . $qrm["delv_recp"]. '#' . $qrm["moneyreceipt"]. '#' . $qrm["not_required"]. '#' . $qrm["ful_cred"]. '#' . $qrm["discount_flag"]. '#' . $qrm["d_normal_com"]. '#' . $qrm["blnce_rpt_print"]. '#' . $qrm["onLine"]. '#' . $qrm["show_balance"]. '#' . $qrm["cash"]. '#' . $qrm["loginid"];

	echo $val; 
}

if ($_POST["type"] == "cntermaster_save") {

	$centreno = $_POST['id'];
	$branch_id = $_POST['branch'];
	$centrename = mysqli_real_escape_string($link,$_POST['name']);
	$short_name = mysqli_real_escape_string($link, $_POST['short_name']);
	$add1 = mysqli_real_escape_string($link, $_POST['address']);
	$response_person = mysqli_real_escape_string($link, $_POST['contactperson']);
	$phoneno = $_POST['phone'];
	$e_mail = $_POST['email'];
	$credit_limit = $_POST['creditlimit'];
	$c_discount = $_POST['testdiscount'];

	$d_patho = $_POST['mpatho'];
	$d_ultra = $_POST['multra'];
	$d_xray = $_POST['mxray'];
	$d_cardio = $_POST['mcardio'];
	$d_spl = $_POST['mspl'];
	$d_ct = $_POST['mct'];


	$c_patho = $_POST['cpatho'];
	$c_ultra = $_POST['cultra'];
	$c_xray = $_POST['cxray'];
	$c_cardio = $_POST['ccardio'];
	$c_spl = $_POST['cspl'];
	$c_ct = $_POST['cct'];

	$s_patho = $_POST['spatho'];
	$s_ultra = $_POST['sultra'];
	$s_xray = $_POST['sxray'];
	$s_cardio = $_POST['scardio'];
	$s_spl = $_POST['sspl'];
	$s_ct = $_POST['sct'];

	$vacu_charge = $_POST['vacucharge'];
	$delv_recp = $_POST['delvrecp'];
	$moneyreceipt = $_POST['moneyreceipt'];
	$not_required = $_POST['notrequired'];
	$ful_cred = $_POST['fullcredit'];
	$discount_flag = $_POST['discountflag'];
	$d_normal_com = $_POST['dnormal'];
	$blnce_rpt_print = $_POST['balanceprint'];

	//new modify

	$onLine = $_POST['online'];
	$show_balance = $_POST['showbalance'];
	$cash = $_POST['cash'];
	$loginid = $_POST['loginid'];

	

	if(!$credit_limit){ $credit_limit = 0; }
	if(!$c_discount){ $c_discount = 0; }
	
	if(!$d_patho){ $d_patho = 0; }
	if(!$d_ultra){ $d_ultra = 0; }
	if(!$d_xray){ $d_xray = 0; }
	if(!$d_cardio){ $d_cardio = 0; }
	if(!$d_spl){ $d_spl = 0; }
	if(!$d_ct){ $d_ct = 0; }

	if(!$c_patho){ $c_patho = 0; }
	if(!$c_ultra){ $c_ultra = 0; }
	if(!$c_xray){ $c_xray = 0; }
	if(!$c_cardio){ $c_cardio = 0; }
	if(!$c_spl){ $c_spl = 0; }
	if(!$c_ct){ $c_ct = 0; }

	if(!$s_patho){ $s_patho = 0; }
	if(!$s_ultra){ $s_ultra = 0; }
	if(!$s_xray){ $s_xray = 0; }
	if(!$s_cardio){ $s_cardio = 0; }
	if(!$s_spl){ $s_spl = 0; }
	if(!$s_ct){ $s_ct = 0; }
	if(!$loginid){ $loginid = 0; }
	if(!$onLine){ $onLine = 0; }
	if(!$show_balance){ $show_balance = 0; }
	if(!$cash){ $cash = 0; }



	$qr=mysqli_fetch_array(mysqli_query($link, "SELECT centreno from `centremaster` where centreno='$centreno'"));
	if($qr)
	{
		if(mysqli_query($link, "UPDATE `centremaster` SET `centrename`='$centrename', `short_name` = '$short_name' ,`add1`='$add1',`response_person`='$response_person',`phoneno`='$phoneno',`e_mail`='$e_mail',`credit_limit`='$credit_limit',`c_discount`='$c_discount',`vacu_charge`='$vacu_charge',`delv_recp`='$delv_recp',`moneyreceipt`='$moneyreceipt',`d_patho`='$d_patho',`d_ultra`='$d_ultra',`d_xray`='$d_xray',`d_cardio`='$d_cardio',`d_spl`='$d_spl',`not_required`='$not_required',`c_patho`='$c_patho',`c_ultra`='$c_ultra',`c_xray`='$c_xray',`c_cardio`='$c_cardio',`c_spl`='$c_spl',`ful_cred`='$ful_cred',`discount_flag`='$discount_flag',`d_normal_com`='$d_normal_com',`s_patho`='$s_patho',`s_ultra`='$s_ultra',`s_xray`='$s_xray',`s_cardio`='$s_cardio',`s_spl`='$s_spl',`d_ct`='$d_ct',`c_ct`='$c_ct',`s_ct`='$s_ct',`branch_id`='$branch_id',`blnce_rpt_print`='$blnce_rpt_print', `onLine` = '$onLine', `show_balance` = '$show_balance', `cash` = '$cash', `loginid` = '$loginid' WHERE `centreno`= '$centreno'"))
		{
			echo "Updated";
		}
		else{
			echo "Failed, try again later.";
		}
	}
	else
	{
		if(mysqli_query($link, "INSERT INTO `centremaster`(`centreno`, `centrename`, `short_name`, `add1`, `response_person`, `phoneno`, `e_mail`, `credit_limit`, `c_discount`, `vacu_charge`, `delv_recp`, `moneyreceipt`, `d_patho`, `d_ultra`, `d_xray`, `d_cardio`, `d_spl`, `not_required`, `c_patho`, `c_ultra`, `c_xray`, `c_cardio`, `c_spl`, `ful_cred`, `discount_flag`, `d_normal_com`, `s_patho`, `s_ultra`, `s_xray`, `s_cardio`, `s_spl`, `d_ct`, `c_ct`, `s_ct`, `branch_id`, `blnce_rpt_print`, `onLine`, `show_balance`, `cash`, `loginid` ) VALUES ('$centreno', '$centrename', '$short_name', '$add1', '$response_person', '$phoneno', '$e_mail', '$credit_limit', '$c_discount','$vacu_charge','$delv_recp','$moneyreceipt', '$d_patho', '$d_ultra', '$d_xray', '$d_cardio', '$d_spl','$not_required', '$c_patho', '$c_ultra', '$c_xray', '$c_cardio', '$c_spl','$ful_cred','$discount_flag','$d_normal_com', '$s_patho', '$s_ultra', '$s_xray', '$s_cardio', '$s_spl', '$d_ct','$c_ct','$s_ct','$branch_id','$blnce_rpt_print', '$onLine', '$show_balance', '$cash', '$loginid' )" ))
		{
			echo "Saved";
		}
		else{
			echo "Failed, try again later.";
		}
	}
} 
 

if ($_POST["type"] == "cntermaster_delete") {
	$subp = $_POST['subp'];

	$chk=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `center_no`='$subp'"));
	if($chk)
	{
		echo "Centre is in used";
	}
	else{
		mysqli_query($link, " DELETE FROM `centremaster` WHERE `centreno`='$subp' ");
		mysqli_query($link, " DELETE FROM `patient_source_master` WHERE `centreno`='$subp' ");

		echo "Deleted";
	}
	
}
?>
