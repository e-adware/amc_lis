<?php
	if($doc_view>=0)
	{
		if($doc_view==1)
		{
			$doc_view_str="Doctor Preview";
		}
		if($doc_view==2)
		{
			$doc_view_str="Technician Preview";
		}
		
		$doc_view_str="CCL-".strtoupper($dept_info["name"]);
		//echo "<div class='doc_view_div'>".$doc_view_str."</div>";
	}
?>
<div class="row report_footer">
	<table class="table table-condensed table-no-top-border" style="display:none;">
	<?php
		$more_report_test_num=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc_id`='$doc_id' AND `page_no`>'$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC"));
		if($more_report_test_num>0)
		{
	?>
			<tr>
				<th colspan="5" class="no_top_border" style="text-align:center;">
					<br>
					---Continue to next page---
					<!--<span style="float:right;"><?php echo "Page ".$page." of ".$total_pages; ?></span>-->
				</th>
			</tr>
	<?php
		}
		else
		{
	?>
			<tr>
				<th colspan="5" class="no_top_border">
					<center>
						<br>
						---End of report---
					</center>
				</th>
			</tr>
	<?php
		}
	?>
	</table>
	<table class="table table-condensed table-no-top-border" style="display:none;">
		<tr>
	<?php
		
		//$footer_barcode_str="<td class='span_doc no_top_border'><img src='../../barcode-master/barcode.php?f=jpg&s=code-128&d=$bill_id&h=45ms=r&tc=white'></td>";
		
		//echo $footer_barcode_str;
		
	?>
		<!--<td class="span_doc no_top_border">
			<img src="<?php echo $qr_code_file_path;?>" style="width:80px;" alt="QR Code">
		</td>-->
	<?php
		
		$nabl_logo_str="<td class='span_doc no_top_border'> </td>";
		$nabl=mysqli_fetch_array(mysqli_query($link, "SELECT `nabl`, `text` FROM `nabl`"));
		if($nabl["nabl"]>0 && $nabl_true>0)
		{
			$nabl_logo_str="<td class='span_doc no_top_border'><img src='../../images/report_nabl.jpg' style='width: 70px;float:right;'></td>";
		}
		
		//echo $nabl_logo_str;
		
		//echo "<td class='span_doc no_top_border'> </td>";
		
		$span_doc=0;
		//$lab_doc_query=mysqli_query($link,"SELECT `id`,`name`,`desig`,`qual`,`sign_name` FROM `lab_doctor` WHERE `category`=1 AND `id` IN(1,$doc_id) ORDER BY `sequence` ASC"); // AND `id`='$lab_doc_id'
		$lab_doc_query=mysqli_query($link,"SELECT `id`,`name`,`desig`,`qual`,`sign_name` FROM `lab_doctor` WHERE `category`=1  ORDER BY `sequence` ASC"); // AND `id`='$lab_doc_id'
		$lab_doc_num=mysqli_num_rows($lab_doc_query);
		$lab_doc_num=5;
		if($lab_doc_num>=$doc_in_a_line)
		{
			$lab_doc_num=$doc_in_a_line;
		}
		
		$lab_doc_num+=1;
		
		$span_doc_width=100/$lab_doc_num;
		
		$tr=0;
		while($lab_doc=mysqli_fetch_array($lab_doc_query))
		{
			if($tr==0)
			{
				//echo "<tr>";
			}
	?>
			<th class="span_doc no_top_border" style="text-align:left;">
			
	<?php
			//if(in_array($lab_doc["id"],$docc) && file_exists("../../sign/".$lab_doc["sign_name"].""))
			if($doc_id==$lab_doc["id"] && file_exists("../../sign/".$lab_doc["sign_name"]."") && $lab_doc["sign_name"])
			{
	?>
				<img src="../../sign/<?php echo $lab_doc["sign_name"];?>" style="height: 50px;"/><br>
	<?php
			}
			else
			{
	?>
				<img src="../../sign/default.png" style="height: 50px;"><br>
	<?php
			}
			echo $lab_doc["name"];
			if($lab_doc["qual"])
			{
				echo ", ".$lab_doc["qual"];
			}
			if($lab_doc["desig"])
			{
				echo "<br>".$lab_doc["desig"];
			}
	?>
			</th>
	<?php
			$tr++;
			$span_doc++;
			
			if($tr>=$doc_in_a_line)
			{
				$tr=0;
			}
			
			if($tr==0)
			{
				//echo $nabl_logo_str."</tr>";
			}
		}
		if($tr<$doc_in_a_line)
		{
			//echo $nabl_logo_str."</tr>";
		}
	?>
		</tr>
	</table>
	
	


<?php
//$aprv_by=$doc_id;
?>
<div class="text-center">
<?php
if($apt==2814)
{
	?> HB Typing is performed using HPLC in Biorad D10 Analyzer <?php
}
else if($apt==2835)
{
?>
	HbA1c test performed using IO-RAD D-10
<?php
}
else
{
	?>

	Bio Chemistry tests performed using Ortho Clinical Diagnostic Vitros 5600 Fully Automated System

<?php } ?>
</div>

<br/><br/>
<div class="row">
<?php
$doc_det=mysqli_fetch_array(mysqli_query($link,"select * from lab_doctor where id='$aprv_by'"));
?>

<div class="span5 text-center">
<?php
	if($aprv_by>0 && $doc_det)
	{
?>
	<img src='../../sign/<?php echo $aprv_by;?>.jpg' style='height: 45px;' /> <br/>
<?php
	}
	else
	{
		echo "<div style='display: block; height: 35px; width: 20px;'></div>";
		echo "Biochemist";
	}
?>
<?php echo $doc_det['desig'];?>
</div>
<div class="span4 text-center">
<?php
	if($aprv_by==0)
	{
		//echo "SELECT `name` FROM `employee` WHERE `emp_id`='$analysis_by'";
		$tech_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$analysis_by'"));
		echo $tech_info["name"];
	}
	else
	{
?>
	<span style='display: block; height: 35px; width: 20px;'></span>
<?php
	}
?>
	<br/>
	Technician
</div>
</div>
	
	


<span style="float:right;"><?php echo "Page ".$page." of ".$total_pages; ?></span>
	<div>
<?php
	if($nabl["nabl"]>0 && $nabl_true>0)
	{
		echo "<center>".$nabl["text"]."</center>";
	}
	$nabl_true=0;
	
	$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT `nb_text` FROM `nb_text` WHERE `id`='1' "));
	if($nb_text_patho)
	{
		//echo "<center>".$nb_text_patho["nb_text"]."</center>";
	}
?>
	</div>
	<div class="checked_by" style="display:none;">
		<table class="table table-condensed table-no-top-border checked_by_table" style="border-top: 1.4px dotted #000;">
			<tr>
				<td style="width:30%;">Data entry by: <?php echo $data_entry_names; ?></td>
				<td style="width:30%;text-align: center;">Checked by: <?php echo $data_checked_names; ?></td>
				<td style="width:40%;text-align: right;">Printed by: <?php echo $emp_info["name"]; ?><?php echo "(".date("d-m-y h:i A").")"; ?></td>
			</tr>
		</table>
	</div>
</div>
