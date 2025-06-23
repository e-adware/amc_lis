<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$pid=$_POST['pid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch=$_POST['batch'];
$bill_id=$_POST['bill_id'];
$tr_serial_no=$_POST['tr_serial_no'];

// Cancel Request Check
$cancel_request_check=mysqli_fetch_array(mysqli_query($link, "select * from cancel_request where patient_id='$pid' AND `opd_id`='$bill_id' AND `type`='2' "));
if(!$cancel_request_check)
{
	$pat_info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pid'"));
	
	$pat_reg=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pid' AND `opd_id`='$bill_id'"));
	
	$reg_date=$pat_reg["date"];
	
	if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

?>
<div style="padding:10px" align="center">
<?php
	//$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pid'"));
	//$opd_det=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$pid' and opd_id='$opd'"));
	$doc=mysqli_fetch_array(mysqli_query($link, "select ref_name from refbydoctor_master where refbydoctorid='$pat_reg[refbydoctorid]'"));
	
?>

<table class="table table-bordered table-condensed">
<tr>
	<th>
		<input type="hidden" id="uhid_no" value="<?php echo $pid;?>"/>
		<input type="hidden" id="opd_id" value="<?php echo $opd_id;?>"/>
		<input type="hidden" id="ipd_id" value="<?php echo $ipd_id;?>"/>
		<input type="hidden" id="batch_no" value="<?php echo $batch;?>"/>
		<input type="hidden" id="bill_id" value="<?php echo $bill_id;?>"/>
		<input type="hidden" id="tr_serial_no" value="<?php echo $tr_serial_no;?>"/>
		
			<?php
				echo "Bill No.: ".$pat_reg["opd_id"];
			?>
	|| UHID: <?php echo $pid;?>
	|| Name: <?php echo $pat_info["name"];?>
	|| Age: <?php echo $age;?>
	|| Sex: <?php echo $pat_info["sex"];?>
	|| Phone: <?php echo $pat_info["phone"];?>
	</th>
</tr>

<tr>
	<td> <b>Patient Test Details</b> <br/>
		<table class="table   table-bordered table-condensed">
			<tr>
				<th colspan="9">Pathology</th>
			</tr>
			<tr>
				<th></th>
				<th>Test Name</th>
				<th></th>
				<th>Sample Received</th>
				<th>LIS Appr/Result Entry</th>
				<th>Approved-Tech</th>
				<th>Approved-Doc</th>
				<th>Printed</th>
		<?php
				if($reporting_delivery==1)
				{
			?>
					<th>Delivery</th>
			<?php
				}
			?>
			</tr>
			<tr>
	<?php
		$i=1;
		$dep=mysqli_query($link,"select * from test_department");
		
		$p_test=mysqli_query($link,"select * from patient_test_details where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid in(select testid from testmaster where category_id='1' and type_id!='132') order by slno");
		while($p_tst=mysqli_fetch_array($p_test))
		{
			echo "<tr>";
			$pos=0;
			$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$p_tst[testid]'"));
			
			$ent=mysqli_fetch_array(mysqli_query($link,"select * from testresults where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]'"));
			if(!$ent["testid"])
			{
				$ent=mysqli_fetch_array(mysqli_query($link,"select * from patient_test_summary where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]'"));
				if(!$ent["testid"])
				{
					$ent=mysqli_fetch_array(mysqli_query($link,"select *,`v_User` AS `user` from widalresult where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]' limit 1"));
				}
			}
			
			$print_disabled="disabled";
			$clss="tst_nt";
			//if($ent["doc"]>0)
			if($ent)
			{
				$print_disabled="";
				$clss="tst";
			}
			
			echo "<td><input type='checkbox' value='$p_tst[testid]' name='grp_td$i' id='$p_tst[testid]_tst' class='$clss' onclick='test_print_group(this.value)' $print_disabled><label><span></span></label></td>";
			
			echo "<td>$tname[testname]</td>";
			
			//echo "<td><input type='button' value='Print' class='btn btn-info' onclick='print_report($p_tst[testid],$pos)'/></td>";
			echo "<td><button class='btn btn-print' onclick='print_report($p_tst[testid],$pos)' $print_disabled><i class='icon-print'></i> Print</td>";
			
			/*----------Sample Receive----------*/
			$smp_rcv_img="<img src='../images/Delete.png' height='20' width='20'/>";
			$samp_rcv=mysqli_query($link,"select * from phlebo_sample where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]'");
			if(mysqli_num_rows($samp_rcv)>0)
			{
				$smp_data=mysqli_fetch_array($samp_rcv);
				$smp_user=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$smp_data[user]'"));
				$smp_rcv_img="<img src='../images/right.png' height='20' width='20'/> - ".$smp_user["name"];
			}
			echo "<td>$smp_rcv_img</td>";
			
			$ent_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			if($ent["user"]>0)
			{
				$e_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$ent[user]'"));
				$ent_sign="<img src='../images/right.png' height='20' width='20'/> - ".$e_name["name"];
			}
			else
			{
				if($ent["tech"]>0)
				{
					$e_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$ent[tech]'"));
					
					$ent_sign="<img src='../images/right.png' height='20' width='20'/> - ".$e_name["name"];
				}
			}
			echo "<td>$ent_sign</td>";
			
			$m_tech_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			if($ent["main_tech"]>0)
			{
				$tech_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$ent[main_tech]'"));
				$m_tech_sign="<img src='../images/right.png' height='20' width='20'/> - ".$tech_name["name"];
			}
			
			echo "<td>$m_tech_sign</td>";
			
			$doc_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			if($ent["doc"]>0)
			{
				$doc_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$ent[doc]'"));
				$doc_sign="<img src='../images/right.png' height='20' width='20'/> - ".$doc_name["name"];
			}
			echo "<td>$doc_sign</td>";
			
			
			$print_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			$chk_p=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]'"));
			if($chk_p>0)
			{
				$print_sign="<img src='../images/right.png' height='20' width='20'/>";
			}
			echo "<td>$print_sign</td>";
		
		if($reporting_delivery==1)
		{
			//Delivery
			$delv_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			$delv=mysqli_query($link,"select * from patient_report_delivery_details where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]' order by slno desc");
			if(mysqli_num_rows($delv)>0)
			{
				
				$delv_sign="<img src='../images/right.png' height='20' width='20'/>";
				$d_info=mysqli_fetch_array($delv);
				$delv_sign.=" <small>".$d_info["name"]."<br>".date("d-m-Y", strtotime($d_info["date"]))." ".date("h:i A", strtotime($d_info["time"]))."</small>";
			}
			echo "<td>$delv_sign</td>";
			
		}
			echo "</tr>";
			$i++;
		}
		
		?>
			<tr>
				<th colspan="9"> 
					<button id="sel_all" class="btn btn-info" onclick="select_all(this.value)" value='sel'><i class="icon-check-empty"></i> Select All</button>
					<button id="grp_print" class="btn btn-print" onclick="group_print_test_rep(1)"><i class="icon-print"></i> Group Print </button>
			<?php
				if($pdf_report==1)
				{
			?>
					<button class="btn btn-edit" onclick="group_print_test_rep(2)"><i class="icon-print"></i> PDF View/Download</button>
			<?php
				}
			?>
			<?php
				if($reporting_delivery==1)
				{
			?>
					<button class="btn btn-excel" onclick="report_delv()"><i class="icon-share"></i> Delivery</button>
			<?php
				}
			?>
					<button id="close" class="btn btn-close" onclick="$('#mod').click();$('#mod_chk').val(0)"><i class="icon-off"></i> Close</button> 
					<input type="hidden" id="test_print"/>
				</th>
			</tr>
		</table>
<?php
	$rad_test=mysqli_query($link,"select * from patient_test_details where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid in(select testid from testmaster where category_id='2' and type_id!='132') order by slno");
	$rad_test_num=mysqli_num_rows($rad_test);
	if($rad_test_num>0)
	{
?>
		<table class="table   table-bordered table-condensed">
			<tr>
				<th colspan="9">Radiology</th>
			</tr>
			<tr>
				<th></th>
				<th>Test Name</th>
				<th></th>
				<th>Result Entry</th>
				<th>Approved-Doc</th>
				<th>Printed</th>
		<?php
				if($reporting_delivery==1)
				{
			?>
					<th>Delivery</th>
			<?php
				}
			?>
			</tr>
			<tr>
	<?php
		while($p_tst=mysqli_fetch_array($rad_test))
		{
			echo "<tr>";
			$pos=0;
			$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$p_tst[testid]'"));
			
			$ent=mysqli_fetch_array(mysqli_query($link,"select * from testresults_rad where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]'"));
			
			$print_disabled="disabled";
			$clss="tst_nt rad_test";
			if($ent["doc"]>0)
			{
				$print_disabled="";
				$clss="tst rad_test";
			}
			
			echo "<td><input type='checkbox' value='$p_tst[testid]' name='grp_td$i' id='$p_tst[testid]_tst' class='$clss' onclick='test_print_group(this.value)' $print_disabled><label><span></span></label></td>";
			
			echo "<td>$tname[testname]</td>";
			
			echo "<td><button class='btn btn-print' onclick='print_report_rad($p_tst[testid],$pos)' $print_disabled><i class='icon-print'></i> Print</td>";
			
			$ent_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			if($ent["saved"])
			{
				$e_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$ent[saved]'"));
				$ent_sign="<img src='../images/right.png' height='20' width='20'/> - ".$e_name["name"];
			}
			
			echo "<td>$ent_sign</td>";
			
			$doc_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			if($ent["doc"]>0)
			{
				$doc_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$ent[doc]'"));
				$doc_sign="<img src='../images/right.png' height='20' width='20'/> - ".$doc_name["name"];
			}
			echo "<td>$doc_sign</td>";
			
			
			$print_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			$chk_p=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]'"));
			if($chk_p>0)
			{
				$print_sign="<img src='../images/right.png' height='20' width='20'/>";
			}
			echo "<td>$print_sign</td>";
		
		if($reporting_delivery==1)
		{
			//Delivery
			$delv_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			$delv=mysqli_query($link,"select * from patient_report_delivery_details where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]' order by slno desc");
			if(mysqli_num_rows($delv)>0)
			{
				
				$delv_sign="<img src='../images/right.png' height='20' width='20'/>";
				$d_info=mysqli_fetch_array($delv);
				$delv_sign.=" <small>".$d_info["name"]."<br>".date("d-m-Y", strtotime($d_info["date"]))." ".date("h:i A", strtotime($d_info["time"]))."</small>";
			}
			echo "<td>$delv_sign</td>";
			
		}
			echo "</tr>";
			$i++;
		}
		
		?>
			<tr>
				<th colspan="8">
			<?php
				if($reporting_delivery==1)
				{
			?>
					<button class="btn btn-excel" onclick="report_delv()"><i class="icon-share"></i> Delivery</button>
			<?php
				}
			?>
					<button id="close" class="btn btn-close" onclick="$('#mod').click();$('#mod_chk').val(0)"><i class="icon-off"></i> Close</button> 
					<input type="hidden" id="test_print"/>
				</th>
			</tr>
		</table>
<?php
	}
?>
<?php
	$rad_test=mysqli_query($link,"select * from patient_test_details where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid in(select testid from testmaster where category_id='3' and type_id!='132') order by slno");
	$rad_test_num=mysqli_num_rows($rad_test);
	if($rad_test_num>0)
	{
?>
		<table class="table   table-bordered table-condensed">
			<tr>
				<th colspan="9">Cardiology</th>
			</tr>
			<tr>
				<th></th>
				<th>Test Name</th>
				<th></th>
				<th>Result Entry</th>
				<th>Approved-Doc</th>
				<th>Printed</th>
		<?php
				if($reporting_delivery==1)
				{
			?>
					<th>Delivery</th>
			<?php
				}
			?>
			</tr>
			<tr>
	<?php
		while($p_tst=mysqli_fetch_array($rad_test))
		{
			echo "<tr>";
			$pos=0;
			$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$p_tst[testid]'"));
			
			$ent=mysqli_fetch_array(mysqli_query($link,"select * from testresults_rad where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]'"));
			
			$print_disabled="disabled";
			$clss="tst_nt rad_test";
			if($ent["doc"]>0)
			{
				$print_disabled="";
				$clss="tst rad_test";
			}
			
			echo "<td><input type='checkbox' value='$p_tst[testid]' name='grp_td$i' id='$p_tst[testid]_tst' class='$clss' onclick='test_print_group(this.value)' $print_disabled><label><span></span></label></td>";
			
			echo "<td>$tname[testname]</td>";
			
			echo "<td><button class='btn btn-print' onclick='print_report_rad($p_tst[testid],$pos)' $print_disabled><i class='icon-print'></i> Print</td>";
			
			$ent_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			if($ent["saved"])
			{
				$e_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$ent[saved]'"));
				$ent_sign="<img src='../images/right.png' height='20' width='20'/> - ".$e_name["name"];
			}
			
			echo "<td>$ent_sign</td>";
			
			$doc_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			if($ent["doc"]>0)
			{
				$doc_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$ent[doc]'"));
				$doc_sign="<img src='../images/right.png' height='20' width='20'/> - ".$doc_name["name"];
			}
			echo "<td>$doc_sign</td>";
			
			
			$print_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			$chk_p=mysqli_num_rows(mysqli_query($link,"select * from testreport_print where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]'"));
			if($chk_p>0)
			{
				$print_sign="<img src='../images/right.png' height='20' width='20'/>";
			}
			echo "<td>$print_sign</td>";
		
		if($reporting_delivery==1)
		{
			//Delivery
			$delv_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			$delv=mysqli_query($link,"select * from patient_report_delivery_details where patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$p_tst[testid]' order by slno desc");
			if(mysqli_num_rows($delv)>0)
			{
				
				$delv_sign="<img src='../images/right.png' height='20' width='20'/>";
				$d_info=mysqli_fetch_array($delv);
				$delv_sign.=" <small>".$d_info["name"]."<br>".date("d-m-Y", strtotime($d_info["date"]))." ".date("h:i A", strtotime($d_info["time"]))."</small>";
			}
			echo "<td>$delv_sign</td>";
			
		}
			echo "</tr>";
			$i++;
		}
		
		?>
			<tr>
				<th colspan="8">
			<?php
				if($reporting_delivery==1)
				{
			?>
					<button class="btn btn-excel" onclick="report_delv()"><i class="icon-share"></i> Delivery</button>
			<?php
				}
			?>
					<button id="close" class="btn btn-close" onclick="$('#mod').click();$('#mod_chk').val(0)"><i class="icon-off"></i> Close</button> 
					<input type="hidden" id="test_print"/>
				</th>
			</tr>
		</table>
<?php
	}
?>
	<td>
</tr>

<!---Radiology-->
<?php
$rad_test=mysqli_query($link,"select * from patient_test_details where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid in(select testid from testmaster where category_id='2222') order by slno");
if(mysqli_num_rows($rad_test)>0)
{
?>
<tr>
	<td>
		<table class="table table-bordered table-condensed">
		<tr>
			<th colspan="2">Radiology</th>
		</tr>
		<?php
		while($rad=mysqli_fetch_array($rad_test))
		{
			$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$rad[testid]'"));
			$rad_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			$chk_r=mysqli_num_rows(mysqli_query($link,"select * from testresults_rad where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$rad[testid]'"));
			if($chk_r>0)
			{
				$rad_sign="<img src='../images/right.png' height='20' width='20'/>";
			}
			?>
			<tr>
				<td><b><?php echo $tname[testname];?></b></td>
				<td><?php echo $rad_sign;?></td>
			</tr>
			
			<?php
		}
		?>
		</table>
	</td>
</tr>


<?php
}
?>



<!---Cardiology-->
<?php
$card_test=mysqli_query($link,"select * from patient_test_details where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid in(select testid from testmaster where category_id='3333') order by slno");
if(mysqli_num_rows($card_test)>0)
{
?>
<tr>
	<td>
		<table class="table table-bordered table-condensed">
		<tr>
			<th colspan="2">Cardiology</th>
		</tr>
		<?php
		while($card=mysqli_fetch_array($card_test))
		{
			$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$card[testid]'"));	
			$card_sign="<img src='../images/Delete.png' height='20' width='20'/>";
			$chk_cr=mysqli_num_rows(mysqli_query($link,"select * from testresults_rad where  patient_id='$pid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$card[testid]'"));
			if($chk_cr>0)
			{
				$card_sign="<img src='../images/right.png' height='20' width='20'/>";
			}
			?>
			<tr>
				<td><b><?php echo $tname[testname];?></b></td>
				<td><?php echo $card_sign;?></td>
			</tr>
			
			<?php
		}
		?>
		</table>
	</td>
</tr>


<?php
}
?>

<tr>
	<th>Patient Payment Details</th>
</tr>
<tr>
	<td>
		<?php
		$pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where  patient_id='$pid' and opd_id='$opd_id'"));
		?>
		<table class="table   table-bordered table-condensed">
			<tr>
				<td>Total Amount</td><td><?php echo $pay["tot_amount"];?></td>
				<td>Discount</td><td><?php echo $pay["dis_amt"]." (".$pay["dis_reason"].")";?></td>
			</tr>
			<tr>
				<td>Paid</td><td><?php echo $pay["advance"];?></td>
				<td>Balance</td><td><?php echo $pay["balance"];?></td>
			</tr>
		</table>
	</td>
</tr>
</table>
</div>
<?php
}
else
{
	$val=2;
	include("cancel_request_msg.php");
	
}
?>
