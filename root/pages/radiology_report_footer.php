
<div class="row report_footer">
	<table class="table table-condensed table-no-top-border">
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
	<table class="table table-condensed table-no-top-border">
	<?php
		
		$span_doc=0;
		$lab_doc_query=mysqli_query($link,"SELECT `id`,`name`,`desig`,`qual`,`sign_name` FROM `lab_doctor` WHERE `category`='$category_id' ORDER BY `sequence` ASC"); // AND `id`='$lab_doc_id'
		$lab_doc_num=mysqli_num_rows($lab_doc_query);
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
				echo "<tr>";
			}
	?>
			<th class="span_doc no_top_border" style="text-align:center;">
			
	<?php
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
			echo $lab_doc["name"].", ".$lab_doc["qual"].""."<br>".$lab_doc["desig"];
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
				echo "</tr>";
			}
		}
		if($tr<$doc_in_a_line)
		{
			echo "</tr>";
		}
	?>
	</table>
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
