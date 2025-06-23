<?php
include("../inc/header.php");

	$para=base64_encode($_GET["param"]);
	$str="index.php?param=$para";
	
	if($_GET["param_str"])
	{
		$param_str=base64_encode($_GET["param_str"]);
		//$str.="&param_str=$param_str";
		
		if(!$para)
		{
			$str="index.php?param=$param_str";
		}else
		{
			$str.="&param_str=$param_str";
		}
	}
	
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
	
	if($_GET["cat"])
	{
		$cat=base64_encode($_GET["cat"]);
		$str.="&cat=$cat";
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
	
	if($_GET["uhid_str"])
	{
		$uhid_str=base64_encode($_GET["uhid_str"]);
		$str.="&uhid_str=$uhid_str";
	}
	
	if($_GET["pin_str"])
	{
		$pin_str=base64_encode($_GET["pin_str"]);
		$str.="&pin_str=$pin_str";
	}
	
	if($_GET["fdate_str"])
	{
		$fdate_str=base64_encode($_GET["fdate_str"]);
		$str.="&fdate_str=$fdate_str";
	}
	
	if($_GET["tdate_str"])
	{
		$tdate_str=base64_encode($_GET["tdate_str"]);
		$str.="&tdate_str=$tdate_str";
	}
	
	if($_GET["name_str"])
	{
		$name_str=base64_encode($_GET["name_str"]);
		$str.="&name_str=$name_str";
	}
	
	if($_GET["phone_str"])
	{
		$phone_str=base64_encode($_GET["phone_str"]);
		$str.="&phone_str=$phone_str";
	}
	
	if($_GET["pat_type_str"])
	{
		$pat_type_str=base64_encode($_GET["pat_type_str"]);
		$str.="&pat_type_str=$pat_type_str";
	}
	
	//echo $str;
	
	echo "<script>window.location='$str'</script>";
	
	
	
include("../inc/footer.php");
?>
