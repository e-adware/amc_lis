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

//$test=mysqli_query($link, "select * from patient_test_details where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' order by testid");

$test=mysqli_query($link, "SELECT a.*,b.`type_id` from patient_test_details a, `testmaster` b WHERE a.`testid`=b.`testid` AND a.`patient_id`='$uhid' AND a.`opd_id`='$opd_id' AND a.`ipd_id`='$ipd_id' AND a.`batch_no`='$batch_no' ORDER BY b.`type_id`");

//////// ***** //////////
//$hlth=mysqli_query($link, "select * from patient_healthser_details where patient_id='$uhid' and visit_no='$visit'");	
?>
<table class="table table-bordered table-condensed">
<?php
$num_t=mysqli_num_rows($test);
if($num_t>0)
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
		while($tst=mysqli_fetch_array($test))
		{
			$phlebo_num=mysqli_num_rows(mysqli_query($link, " select * from phlebo_sample where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and testid='$tst[testid]'"));	
			if($phlebo_num==0)
			{
				$dis="disabled";
			}else
			{
				$dis="";
			}
			$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$tst[testid]'"));	
			if($tname[category_id]=="1" && $tname[type_id]!='132')
			{
				echo "<tr id='grp_tr$i'><td><input type='checkbox' value='$tst[testid]' name='grp_td$i' id='$tst[testid]_tst' class='tst' onclick='test_print_group(this.value)'/> <label><span></span> </label></td><td>$tname[testname]</td></tr>";
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
	
	
<?php
$num_h=mysqli_num_rows($hlth);
if($num_h>0)
{
?>
<tr>
	<th>Health Packages</th>
</tr>
<tr>
	<td>
		<table class="table table-bordered table-condensed">
		<?php
		while($hp=mysqli_fetch_array($hlth))
		{
			////// ***** ///////////
			$hname=mysqli_fetch_array(mysqli_query($link, "select health_package_name from health_package_details where hp_id='$hp[hp_id]'"));	
			echo "<tr><td><input type='checkbox' value='$hp[hp_id]' class='hlt'/></td><td>$hname[health_package_name]</td></tr>";
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
		<button class="btn btn-back" onclick="load_test_detail('<?php echo $uhid; ?>', '<?php echo $opd_id; ?>', '<?php echo $batch_no; ?>')" id="grp_print_rpt"><i class="icon-backward"></i> Back</button>
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
