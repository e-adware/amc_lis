<div class="row report_footer">
	<table class="table table-condensed table-no-top-border" style="">
	<?php
		$more_report_test_num=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `pathology_report_print` WHERE `patient_id`='$uhid' AND `opd_id`='$bill_id' AND `batch_no`='$batch_no' AND `testid`='$testid' AND `doc_id`='$doc_id' AND `page_no`>'$page_no' AND `user`='$c_user' AND `ip_addr`='$ip_addr' ORDER BY `slno` ASC"));
		if($more_report_test_num>0)
		{
	?>
			<tr>
				<th colspan="5" class="no_top_border" style="text-align:center;border-top: 1px solid #fff !important;">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					---Continue to next page---
					<?php //echo $nabl_logo_str; ?>
					<!--<span style="float:right;"><?php echo "Page ".$page." of ".$total_pages; ?></span>-->
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php
					if($nabl["nabl"]>0 && $nabl_true>0)
					{
						echo '<span class=""><img src="../../images/report_nabl.jpg" style="width: 60px;float:right;"></span>';
					}
					else
					{
						echo '<img src="../../sign/default.jpg" style="height: 80px;">';
					}
				?>
				</th>
			</tr>
	<?php
		}
		else
		{
	?>
			<tr>
				<th colspan="5" class="no_top_border" style="text-align:center;border-top: 1px solid #fff !important;">
					<center>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						
						---End of report---
						<?php //echo $nabl_logo_str; ?>
						<!--<span style="float:right;"><?php echo "Page ".$page." of ".$total_pages; ?></span>-->
						
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php
						if($nabl["nabl"]>0 && $nabl_true>0)
						{
							echo '<span class=""><img src="../../images/report_nabl.jpg" style="width: 60px;float:right;"></span>';
						}
						else
						{
							echo '<img src="../../sign/default.jpg" style="height: 80px;">';
						}
					?>
					</center>
				</th>
			</tr>
	<?php
		}
	?>
	</table>
	<table class="table table-condensed table-no-top-border">
		<tr>
	<?php
		$footer_barcode_str="<td class='span_doc no_top_border' style='border-top: none; !important;'><img src='../../barcode-master/barcode.php?f=jpg&s=code-128&d=$bill_id&h=45ms=r&tc=white'></td>";
		
		echo $footer_barcode_str;
		
	?>
		<td class="span_doc no_top_border" style="border-top: none; !important;">
			<img src="<?php echo $qr_code_file_path;?>" style="width:80px;" alt="QR Code">
		</td>
	<?php
		
		$nabl_logo_str="<td class='span_doc no_top_border' style='border-top: none; !important;'> </td>";
		$nabl=mysqli_fetch_array(mysqli_query($link, "SELECT `nabl`, `text` FROM `nabl`"));
		if($nabl["nabl"]>0 && $nabl_true>0)
		{
			$nabl_logo_str="<td class='span_doc no_top_border' style='border-top: 1px solid #fff !important;'><img src='../../images/report_nabl.jpg' style='width: 70px;float:right;'></td>";
		}
		
		echo $nabl_logo_str;
		
		echo "<td class='span_doc no_top_border' style='border-top: none; !important;'> </td>";
		
		$span_doc=0;
		$lab_doc_query=mysqli_query($link,"SELECT `id`,`name`,`desig`,`qual`,`sign_name` FROM `lab_doctor` WHERE `status`=0 AND `category`=1 AND `id`='$doc_id' ORDER BY `sequence` ASC");
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
			<td class="span_doc" style="border-top: 1px solid #fff !important;line-height: 16px !important;">
			
	<?php
			//if(in_array($lab_doc["id"],$docc) && file_exists("../../sign/".$lab_doc["sign_name"].""))
			if($doc_id==$lab_doc["id"] && file_exists("../../sign/".$lab_doc["sign_name"]."") && $lab_doc["sign_name"])
			{
	?>
				<img src='../../sign/<?php echo $lab_doc["sign_name"];?>' style="height: 40px;"/><br>
	<?php
			}
			else
			{
	?>
				<img src='../../sign/default.jpg' style="height: 40px;"><br>
	<?php
			}
			echo $lab_doc["name"]."".$lab_doc["qual"].""."<br>".$lab_doc["desig"];
	?>
			</td>
	<?php
			$tr++;
			$span_doc++;
			
			if($tr>=$doc_in_a_line)
			{
				$tr=0;
			}
			
			if($tr==0)
			{
				//echo "</tr>";
			}
		}
		if($tr<$doc_in_a_line)
		{
			//echo "</tr>";
		}
	?>
		</tr>
	</table>
	<div>
		<div style="text-align:right;font-weight:bold;font-size:10px;"><?php echo "Page ".$page." of ".$total_pages; ?></div>
<?php
	if($nabl["nabl"]>0 && $nabl_true>0)
	{
		//echo "<center>".$nabl["text"]."</center>";
	}
	$nabl_true=0;
	
	$nb_text_patho=mysqli_fetch_array(mysqli_query($link, " SELECT `nb_text` FROM `nb_text` WHERE `id`='1' "));
	if($nb_text_patho)
	{
		echo "<center>".$nb_text_patho["nb_text"]."</center>";
	}
?>
	</div>
	<div class="checked_by" style="display:;">
		<table class="table table-condensed table-no-top-border checked_by_table" style="border-top: 1.4px dotted #000;font-size:10px;">
			<tr>
				<td style="width:35%;">Data entry by: <?php echo $data_entry_names; ?></td>
				<td style="width:35%;text-align: center;">Checked by: <?php echo $data_checked_names; ?></td>
				<td style="width:30%;text-align: right;"><!--Printed by: <?php echo $emp_info["name"]; ?>--><?php echo "(".date("d-m-y h:i A").")"; ?></td>
			</tr>
		</table>
	</div>
</div>
<div class="row img_header"><img src="../../images/footer.jpg" style="width:100%;"/></div>
