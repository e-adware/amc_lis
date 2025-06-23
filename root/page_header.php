<?php
$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` limit 0,1 "));
$signature="For ".$company_info['name'];
$phon="";
if($company_info["phone1"])
$phon.=$company_info["phone1"];
if($company_info["phone2"])
$phon.=", ".$company_info["phone2"];
if($company_info["phone3"])
$phon.=", ".$company_info["phone3"];
?>
<div class="" style="">
	<div class="span2" style="margin-left:0px;">
		<img src="../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png" style="width:80px;margin-top:0px;margin-bottom:-70px;" />
	</div>
	<div class="span10 text-center" style="margin-left:0px;">
	<h4>
		<?php echo $company_info["name"]; ?><br>
		<small><?php echo $company_info["address"].", ".$company_info["city"].", ".$company_info["state"]."-".$company_info["pincode"]; ?><br/>
		Phone Number(s): <?php echo $phon; ?> Email: <?php echo $company_info["email"]; ?>
		<br/><br/>
		<hr style="width:91.7%;" />
		
		
		<!--<br>
		Website: <?php echo $company_info["website"]; ?><br/>
		<b>Health Registration No(CER): <?php echo $cer["cer"]; ?></b>--></small>
	</h4>
	</div>
</div>
