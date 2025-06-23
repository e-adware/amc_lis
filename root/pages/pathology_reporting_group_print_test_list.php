<div style="padding:10px" tabindex="0" id="grp_print_div" onkeyup="select_test_grp(event)">

<?php
include("../../includes/connection.php");

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];

// Cancel Request Check
$cancel_request_check=mysqli_fetch_array(mysqli_query($link, "select * from cancel_request where patient_id='$uhid' AND (`opd_id`='$opd_id' OR `opd_id`='$ipd_id') AND `type`='2' "));
if(!$cancel_request_check)
{

	$test_qry=mysqli_query($link, "SELECT a.*,b.`testname`,b.`category_id`,b.`type_id` from patient_test_details a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' ORDER BY b.`type_id`,a.`testid` ASC");
?>
	<table class="table table-bordered table-condensed">
<?php
	$test_num=mysqli_num_rows($test_qry);
	if($test_num>0)
	{
?>
	<tr style="display:none;">
		<td>
			<table width="100%">
				<tr>
			<?php
				$lab_doc=mysqli_query($link,"select * from lab_doctor where category='1' order by sequence");
				while($lb=mysqli_fetch_array($lab_doc))
				{
			?>
					<td><label><input type="checkbox" value="<?php echo $lb["id"];?>" class="lab_doc_check"/> <span></span><?php echo $lb["name"];?> </label></td>
			<?php
				}
			?>
				</tr>
			</table>
		
		</td>
	</tr>
	<tr>
		<th>Tests</th>
	</tr>
	<tr>
		<td>
			<table class="table table-bordered table-condensed">
			<?php
			$i=1;
			echo "<tr><td colspan='2'><label><input type='checkbox' id='select_all' onClick='select_all()'> Select All (Ctrl+Space)</label></td></tr>";
			while($test_det=mysqli_fetch_array($test_qry))
			{
				$phlebo_num=mysqli_num_rows(mysqli_query($link, " select * from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$test_det[testid]'"));
				if($phlebo_num==0)
				{
					$dis="disabled";
				}else
				{
					$dis="";
				}
				$test_dept=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `test_department` WHERE `id`='$test_det[type_id]'"));
				
				if($test_det["category_id"]=="1" && $test_det["type_id"]!='132')
				{
					echo "<tr id='grp_tr$i'><td><label name='chk$i'><input type='checkbox' value='$test_det[testid]' name='grp_td$i' id='$test_det[testid]_tst' class='tst' onclick='test_print_group(this.value)'/></label></td><td>$test_det[testname]</td><td>$test_dept[name]</td></tr>";
					$i++;
				}
				
			}
			?>
			</table>
		</td>
	</tr>
	<?php
	}
?>
	<tr>
		<td style="text-align:center">
			<button class="btn btn-print" onclick="group_print_test()" id="grp_print_rpt"><i class="icon-print"></i> Print (Ctrl+X)</button>
		<?php
			if($pdf_report==1)
			{
		?>
			<button class="btn btn-edit" onclick="group_print_test_pdf()" id="grp_print_rpt"><i class="icon-file"></i> PDF View/Download</button>
		<?php
			}
		?>
			<button class="btn btn-process" onclick="group_view_test()" id="grp_print_rpt"><i class="icon-eye-open"></i> View</button>
			<button class="btn btn-back" onclick="load_test_detail('<?php echo $uhid; ?>','<?php echo $opd_id; ?>','<?php echo $ipd_id; ?>','<?php echo $batch_no; ?>')" id="grp_print_rpt"><i class="icon-backward"></i> Back</button>
			<!--<input type="button" value="Select All" class="btn btn-custom" onclick="group_print_all()" id="select_all"/>-->
			<!--<input type="button" value="Print [CTRL+H]" class="btn btn-custom" onclick="group_print_test()" id="grp_print_rpt"/>-->
			<!--<input type="button" value="PDF View/Download" class="btn btn-custom" onclick="group_print_test_pdf()" id="grp_print_rpt"/>-->
			<!--<input type="button" value="View" class="btn btn-custom" onclick="group_view_test()" id="grp_print_rpt"/>-->
		</td>
	</tr>
		
	</table>
	<input type="hidden" id="test_print"/>
</div>
<?php
}
else
{
	$val=2;
	include("cancel_request_msg.php");
}

mysqli_close($link);
?>
