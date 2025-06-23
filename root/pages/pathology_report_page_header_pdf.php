<?php

if($pat_reg["branch_id"])
{
	$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));
	$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$pat_reg[branch_id]' limit 0,1 "));
	
	$branch_id=$pat_reg["branch_id"];
}
else
{
	if($branch_id)
	{
		$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$branch_id' limit 0,1 "));
		$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` WHERE `branch_id`='$branch_id' limit 0,1 "));
	}
	else
	{
		$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
		$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `cer` FROM `company_documents` limit 0,1 "));
	}
}

$signature="For ".$company_info['name'];
$phon="";
if($company_info["phone1"])
$phon.=$company_info["phone1"];
if($company_info["phone2"])
$phon.=", ".$company_info["phone2"];
if($company_info["phone3"])
$phon.=", ".$company_info["phone3"];

$header2="                       ".$company_info["address"].", ".$company_info["city"].", ".$company_info["state"]."-".$company_info["pincode"];
//$header3="     Phone Number(s): ".$phon." Email: ".$company_info["email"];
$header3="     Phone Number(s): ".$phon;
?>
<div class="row" >
	<div class="span2" >
		<img src="../../images/<?php echo $company_info["client_logo"]; ?>" type="image/jpg/png" style="width:80px;margin-top:0px;margin-bottom:-70px;" />
	</div>
	<div class="span10 text-center" style="margin-left:0px;">
		<span style="font-size:12px;"><?php echo $page_head_line;; ?></span>
		<h4>
			<?php echo $company_info["name"]; ?><br>
			<small>
				<!--<?php echo $company_info["city"]."-".$company_info["pincode"].", ".$company_info["state"]; ?><br/>-->
			<?php echo nl2br($company_info["address"]); ?>
		<?php if($company_info["phone1"]){ ?>
			<br>
			Contact: <?php echo $company_info["phone1"]; ?>
		<?php } ?>
			<?php echo $company_info["phone2"]; ?>
			
		<?php if($company_info["email"]){ ?>
			<br>
			Email: <?php echo $company_info["email"]; ?>
		<?php } ?>
			</small>
		</h4>
	</div>
</div>
<!--<span style="float:right;font-size: 7px;"><?php echo date("d-m-Y h:i:s A"); ?></span>-->
<br>
<style>
h4
{
	margin: 0 0 10px 0;
}
h4 small
{
	font-size: 13px !important;
}
</style>
