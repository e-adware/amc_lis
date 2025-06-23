<?php
include("../../includes/connection.php");
$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST["type"];

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}

if($type=="load_prev_data")
{
	$p_date=date("Y-m-d", strtotime('-7 days'));
	
	//echo "uytyut";
	$dt1=("2018-04-10");
	$dt2=("2018-04-04");
	$dt_diff=abs(strtotime($dt1)-strtotime($dt2));
	$num=$dt_diff/(60*60*24);
	$vdate=$p_date;
	$tot="";
	for($i=0; $i<=$num; $i++)
	{
		$ddate=strtotime('+1 days', strtotime($vdate));
		$ndt=date("Y-m-d",$ddate);
		$vdate=$ndt;
		$amt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`total_amt`),0) AS max_sell FROM `ph_sell_master` WHERE `entry_date`='$vdate'"));
		$rs=$amt['max_sell'];
		$per=(($amt['max_sell']/50000)*100);
		$per=number_format($per,2);
		$per=$per/2;
		$vdate=convert_date($vdate);
		$tot.="&#x20b9; ".$rs."@".$per."%@".$vdate."#@#";
	}
	echo $tot;
	//$q=mysqli_query($link,"SELECT DISTINCT `entry_date` FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$p_date' AND '$date'");
	//~ $tot="";
	//~ while($r=mysqli_fetch_array($q))
	//~ {
		//~ $amt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`total_amt`),0) AS max_sell FROM `ph_sell_master` WHERE `entry_date`='$r[entry_date]'"));
		//~ $tot.=$amt['max_sell']."@@";
	//~ }
	//~ echo $tot;
}
?>
