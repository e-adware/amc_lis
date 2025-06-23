<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST['type'];
//print_r($_POST);


if($type==1)
{
	$samp=$_POST['samp'];
	$qry = mysqli_query($link, "SELECT `id`, `type` FROM `vaccu_master` ORDER BY `type`");

	$count = 0;
	echo "<table class='table table-condensed table-report'>";
	echo "<tr><td colspan='4' style='padding:1px;'></td></tr>";
	if($samp)
	{
	while ($row = mysqli_fetch_assoc($qry)) {
		if ($count % 4 == 0) {
			echo "<tr>";
		}
		$checked="";
		$mapCheck=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `sample_vaccu` WHERE `samp_id`='$samp' AND `vacc_id`='$row[id]'"));
		if($mapCheck)
		{
			$checked="checked";
		}
		$name = strtoupper($row['type']);
		echo "<td> <label><input type='checkbox' class='checks' id='v$row[id]' value='$row[id]' $checked /> " . htmlspecialchars($name) . "</label></td>";
		$count++;

		if ($count % 4 == 0) {
			echo "</tr>";
		}
	}

	$remaining = $count % 4;
	if ($remaining > 0) {
		for ($i = 0; $i < 4 - $remaining; $i++) {
			echo "<td></td>";
		}
		echo "</tr>";
	}
	echo "<tr><td colspan='4' style='text-align:center;'><button type='button' class='btn btn-primary' id='sav' onclick='mapp_vaccu(\"$samp\")'>Mapp Vaccu</button></td></tr>";
	}
	else
	{
		echo "<tr><th colspan='4'>Please Select Sample</th></tr>";
	}
	echo "</table>";
}

if($type==2)
{
	$samp=$_POST['samp'];
	$vacc=$_POST['vacc'];
	
	mysqli_query($link,"DELETE FROM `sample_vaccu` WHERE `samp_id`='$samp'");
	$vac=explode("@", $vacc);
	foreach($vac as $vc)
	{
		if($vc)
		{
			$mapCheck=mysqli_fetch_array(mysqli_query($link,"SELECT `slno` FROM `sample_vaccu` WHERE `samp_id`='$samp' AND `vacc_id`='$vc'"));
			if(!$mapCheck)
			{
				mysqli_query($link,"INSERT INTO `sample_vaccu`(`samp_id`, `vacc_id`) VALUES ('$samp','$vc')");
			}
		}
	}
}

if($type==3)
{
	echo "<table class='table table-condensed'>";
	echo "<tr><th>Sample Name</th><th>Vaccu Name</th></tr>";

	$qry = mysqli_query($link, "SELECT DISTINCT a.`ID`, a.`Name` 
								 FROM `Sample` a, `sample_vaccu` b 
								 WHERE a.`ID` = b.`samp_id` 
								 ORDER BY a.`Name`");

	while ($row = mysqli_fetch_array($qry)) {
		$q = mysqli_query($link, "SELECT a.`id`, a.`type` 
								  FROM `vaccu_master` a, `sample_vaccu` b 
								  WHERE b.`samp_id` = '{$row['ID']}' AND a.`id` = b.`vacc_id`");
		$num = mysqli_num_rows($q);
		$first = true;

		while ($r = mysqli_fetch_array($q)) {
			echo "<tr>";
			if ($first && $num > 1) {
				echo "<td rowspan='$num' class='first-cell'>" . strtoupper($row['Name']) . "</td>";
				echo "<td class='first-cell'>" . strtoupper($r['type']) . "</td>";
				$first = false;
			} elseif ($num == 1) {
				echo "<td class='first-cell'>" . strtoupper($row['Name']) . "</td>";
				echo "<td class='first-cell'>" . strtoupper($r['type']) . "</td>";
			} else {
				// Only type cell shown; still needs class
				echo "<td>" . strtoupper($r['type']) . "</td>";
			}
			echo "</tr>";
		}
	}
	echo "</table>";
}
?>