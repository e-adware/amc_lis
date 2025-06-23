<?php
$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
$cer1=mysqli_fetch_array(mysqli_query($link, " SELECT `cer`,`gst` FROM `company_documents`  "));

$signature="For ".$company_info['name'];
$phon="";
if($company_info["phone1"])
$phon.=$company_info["phone1"];
if($company_info["phone2"])
$phon.=", ".$company_info["phone2"];
if($company_info["phone3"])
$phon.=", ".$company_info["phone3"];

$header2="".$company_info["address"].", ".$company_info["city"].", ".$company_info["state"]."-".$company_info["pincode"];
$header3="Phone Number(s): ".$phon." Email: ".$company_info["email"];

?>
<div class="row" style="">
	<div class="span2" style="margin-left:6px;">
		<img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png" style="width:80px;margin-top:0px;margin-bottom:-70px;" />
	</div>
	<div class="span10 text-center" style="margin-left:0px;">
	<h4>
		<?php echo $company_info["name"]; ?><br>
		<small><?php echo $company_info["address"].", ".$company_info["pincode"]; ?><br/>
		Phoned Number(s): <?php echo $phon; ?> Email: <?php echo $company_info["email"]; ?></br>
		GST NO. <?php echo $cer1['gst']; ?>
		<!--<br/><br/>-->
		<hr style="width:91.7%;margin-bottom: 1%;" />
		<?php if($page_name){echo $page_name;}?>
		
		<!--<br>
		Website: <?php echo $company_info["website"]; ?><br/>
		<b>Health Registration No(CER): <?php echo $cer["cer"]; ?></b>--></small>
	</h4>
	</div>
</div>
