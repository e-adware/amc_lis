<?php
include("../inc/header.php");

	$para=base64_encode($_GET["param"]);
	$str="index.php?param=$para";
	
	if($_GET["uhid"])
	{
		$uhid=base64_encode($_GET["uhid"]);
		$str.="&uhid=$uhid";
	}
	
	if($_GET["opd"])
	{
		$opd=base64_encode($_GET["opd"]);
		$str.="&opd=$opd";
	}
	
	if($_GET["ipd"])
	{
		$ipd=base64_encode($_GET["ipd"]);
		$str.="&ipd=$ipd";
	}
	
	if($_GET["show"])
	{
		$show=base64_encode($_GET["show"]);
		$str.="&show=$show";
	}
	
	if($_GET["lab"])
	{
		$lab=base64_encode($_GET["lab"]);
		$str.="&lab=$lab";
	}
	
	if($_GET["consult"])
	{
		$consult=base64_encode($_GET["consult"]);
		$str.="&consult=$consult";
	}
	
	if($_GET["user"])
	{
		$user=base64_encode($_GET["user"]);
		$str.="&user=$user";
	}
	
	if($_GET["adv"])
	{
		$adv=base64_encode($_GET["adv"]);
		$str.="&adv=$adv";
	}
	
	if($_GET["process"])
	{
		$process=base64_encode($_GET["process"]);
		$str.="&process=$process";
	}
	
	if($_GET["billno"])
	{
		$billno=base64_encode($_GET["billno"]);
		$str.="&billno=$billno";
	}

	if($_GET["client_id"])
	{
		$client_id=base64_encode($_GET["client_id"]);
		$str.="&clnt=$client_id";
	}

	if($_GET["complaint_id"])
	{
		$complaint_id=base64_encode($_GET["complaint_id"]);
		$str.="&cmplnt=$complaint_id";
	}
	
	if($_GET["type"])
	{
		$type=base64_encode($_GET["type"]);
		$str.="&type=$type";
	}
	if($_GET["ind_num"])
	{
		$ind_num=base64_encode($_GET["ind_num"]);
		$str.="&ind_num=$ind_num";
	}
	
	if($_GET["orderno"])
	{
		$orderno=base64_encode($_GET["orderno"]);
		$str.="&orderno=$orderno";
	}
	
	if($_GET["val"])
	{
		$val=base64_encode($_GET["val"]);
		$str.="&val=$val";
	}
	
	if($_GET["service_id"])
	{
		$service_id=base64_encode($_GET["service_id"]);
		$str.="&service_id=$service_id";
	}
	
	echo "<script>window.location='$str'</script>";
	
	
	
include("../inc/footer.php");
?>
