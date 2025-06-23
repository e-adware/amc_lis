<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];

$type=$_POST['type'];
if($type==1)
{
	$pat_type=$_POST['pat_type'];
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$dep=$_POST['dep'];
	$tst=$_POST['tst'];
	
	$pin=$_POST['pin'];
	$name=$_POST['name'];
	$uhid=$_POST['uhid'];
	$barcode_id=$_POST['barcode_id'];
	
	$list_start=$_POST["list_start"];
	
	$dep1=0;
	
	$dep_str="";
	if($dep1>0)
	{
	
	}
	else
	{	
	
	if($_POST[hosp_chk])
	{
		?>
		<div align="right">
			<input type="button" class="btn btn-primary" value="Back to list" onclick="$('#ser').click()"/>
		</div>
		<?php
	}	
	?>
	<table class="table table-bordered table-condensed">
		<thead class="table_header_fix">
			<tr>
				<th>#</th><th>Date/Time</th> <th>Bill ID | Batch No</th> <th>UNIT NO.</th> <th>Name</th> <th>Age-Sex</th><th>Status</th><!--<th>Doctor</th>-->
			</tr>
		</thead>
		
<?php
        $vchkid=0;
		$str="SELECT DISTINCT a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`batch_no` FROM `patient_test_details` a , `uhid_and_opdid` b, `testmaster` c, `patient_info` d WHERE a.`patient_id`=b.`patient_id` AND a.`patient_id`=d.`patient_id`  AND (a.`opd_id`=b.`opd_id` OR a.`ipd_id`=b.`opd_id`) AND a.`testid`=c.`testid`";
		
		if($dep>0)
		{
			$str.=" AND c.`type_id`='$dep'";
		}
		
		if($tst>0)
		{
			$str.=" AND c.`testid`='$tst'";
		}
		
		$i=1;
		$no_req=0;
		$str_typ="";
		if($pat_type=="opd_id")
		{
			$str.=" AND a.`ipd_id`=''";
		}
		if($pat_type=="ipd_id")
		{
			$str.=" AND a.`opd_id`=''";
		}
		
		if($pin!='')
		{
			$vchkid=1;
			$str.=" AND b.`opd_id`='$pin'";
		}
		
		if($uhid!='')
		{
			$vchkid=1;
			$str.=" AND a.`patient_id`='$uhid'";
		}
		if($vchkid==0)
		{
			$str.=" AND a.date between '$fdate' and '$tdate'";
		}
		
		$str.="  AND c.`category_id`=1 ORDER BY a.`slno` DESC LIMIT ".$list_start;
		
		if($barcode_id!='')
		{
			//$str="select distinct a.`opd_id`,a.`ipd_id`,a.`batch_no` FROM `test_sample_result` where barcode_id='$barcode_id'";
		}
		
		$no_req=0;
		if($_POST["ser_chk"]!=1)
		{
			$no_req=1;
		}
		
		//echo $str;
		$qry=mysqli_query($link, $str );
		
		while($q=mysqli_fetch_array($qry))
		{
			$reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$q[patient_id]' AND (`opd_id`='$q[opd_id]' or `opd_id`='$q[ipd_id]') "));
			
			$info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$reg[patient_id]' "));
			
			$reg_date=$reg["date"];
			
			if($info["dob"]!=""){ $age=age_calculator_date($info["dob"],$reg_date); }else{ $age=$info["age"]." ".$info["age_type"]; }
			
			$chk_flag=0;
			if($dep>0)
			{
				$chk_flag=mysqli_num_rows(mysqli_query($link,"select * from patient_flagged_details where patient_id='$reg[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]' and dept_id='$dep'"));
			}
			
			$no_disp=0;
			if($no_req==1)
			{
				$chk_test=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from patient_test_details where patient_id='$reg[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]'"));
				$chk_res=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from testresults where patient_id='$reg[patient_id]' and opd_id='$q[opd_id]' and ipd_id='$q[ipd_id]' and batch_no='$q[batch_no]'"));
				
				if($chk_test[tot]==$chk_res[tot])
				{
					//$no_disp=1;
				}
				
				//----Flagged Pat---//
				
				if($chk_flag>0)
				{
					//$no_disp=1;
				}
				//-----------------//
			}
			
			if($no_disp==0)
			{
				$flag_class="";
				if($chk_flag>0)
				{
					$flag_class="class='flagged'";
				}
				
				$tr_id="";
				
			?>
			<tr <?php echo $flag_class;?> id="<?php echo $tr_id;?>";>
				<td><?php echo $i;?></td>
				<td><?php echo convert_date($reg[date])."/".convert_time($reg[time]);?></td>
				<td>
					<?php echo $reg[opd_id]." | ".$q[batch_no];?>
					
					<input type="hidden" id="pid_<?php echo $i;?>" value="<?php echo $reg['patient_id'];?>"/>
					<input type="hidden" id="opd_<?php echo $i;?>" value="<?php echo $q[opd_id];?>"/>
					<input type="hidden" id="ipd_<?php echo $i;?>" value="<?php echo $q[ipd_id];?>"/>
					<input type="hidden" id="batch_<?php echo $i;?>" value="<?php echo $q[batch_no];?>"/>
					
				</td>
				
				<td>
					<?php echo $reg[patient_id];?>
					
				</td>
				<td><?php echo $info['name'];?></td>
				<td><?php echo $age." , ".$info['sex'];?></td>
				<td>
					<div class="btn-group">
						
					<?php
						$j=1;
						$cls="";
						if($dep>0)
						{
							$dep_l=mysqli_query($link," SELECT DISTINCT `type_id` FROM `testmaster` WHERE `category_id`=1 AND type_id='$dep' and `testid` IN (SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]' ) ");
						}
						else
						{
							$dep_l=mysqli_query($link," SELECT DISTINCT `type_id` FROM `testmaster` WHERE `category_id`=1 AND `testid` IN (SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]' ) ");
						}
						
						while($d=mysqli_fetch_array($dep_l))
						{
							$cls="";
							$d_nm=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$d[type_id]'"));
							
							//-------No Tech Approve---//
							$tot_res_n=mysqli_fetch_array(mysqli_query($link," SELECT count(slno) as tot FROM `testresults` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `type_id`='$d[type_id]') and `main_tech`=0"));
							
							$tot_sum_n=mysqli_fetch_array(mysqli_query($link," SELECT count(*) as tot FROM `patient_test_summary` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `type_id`='$d[type_id]') AND `testid` NOT IN (SELECT `testid` FROM `testresults` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]' ) AND  `main_tech`=0"));
							
							$tot_wid_n[tot]=0;
							if($d['type_id']==32)
							{
								$tot_wid_n=mysqli_fetch_array(mysqli_query($link," SELECT count(*) as tot FROM `widalresult` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  AND  `main_tech`=0 limit 1 "));
							}
							$tot_lis_n=mysqli_fetch_array(mysqli_query($link," SELECT count(*) as tot FROM `test_sample_result` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  AND `result`!='' AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `type_id`='$d[type_id]') AND `paramid` NOT IN (SELECT `paramid` FROM `testresults` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]' ) "));
							
							
							//------Tech Approve---//
							$tot_res_n1=mysqli_fetch_array(mysqli_query($link," SELECT count(*) as tot FROM `testresults` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `type_id`='$d[type_id]') AND  `main_tech`>0 "));
							$tot_sum_n1=mysqli_fetch_array(mysqli_query($link," SELECT count(*) as tot FROM `patient_test_summary` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `type_id`='$d[type_id]') AND `testid` NOT IN (SELECT `testid` FROM `testresults` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]' ) AND `main_tech`>0 "));
							$tot_wid_n1[tot]=0;
							if($d['type_id']==32)
							{
								$tot_wid_n1=mysqli_fetch_array(mysqli_query($link," SELECT count(*) as tot FROM `widalresult` WHERE `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  AND  `main_tech`>0 limit 1 "));
							}
							
							
							$pat_test=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from patient_test_details where `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  and testid in(select testid from testmaster where type_id='$d[type_id]')"));
							
							$tot_chk=$tot_res_n1[tot]+$tot_sum_n1[tot]+$tot_wid_n1[tot];
							
							
							$cls="btn btn-success btn-mini";
							
							if($tot_chk==0)
							{
								$cls="btn btn-danger btn-mini";
							}
							if($tot_res_n[tot]>0 || $tot_sum_n[tot]>0 || $tot_wid_n[tot]>0 || $tot_lis_n[tot]>0)
							{
								$cls="btn btn-warning btn-mini";
							}
							if($pat_test[tot]>$tot_chk && $tot_chk>0)
							{
								//$cls="btn btn-primary btn-mini";
							}
							
							$chk_canc=mysqli_num_rows(mysqli_query($link,"select * from patient_test_details where `patient_id`='$reg[patient_id]' AND `opd_id`='$q[opd_id]' AND `ipd_id`='$q[ipd_id]'  AND `batch_no`='$q[batch_no]'  AND `testid` IN (SELECT `testid` FROM `testmaster` WHERE `type_id`='$d[type_id]')"));
							if($chk_canc==0)
							{
									$cls="btn btn-primary btn-mini";
							}
							
							
						?>
						
							<input type="button" value="<?php echo $d_nm['name'];?>" id="dep_<?php echo $d['type_id'];?>" class="<?php echo $cls;?>" onclick="load_pat_dep(<?php echo $i;?>,<?php echo $d['type_id'];?>)"/>
						<?php
						}
					?>
					</div>
				</td>
				
			</tr>
			<?php	
			$i++;
			}
		}
	}
	?>
	</table>
<?php
}
else if($type==3)
{
	$dep=$_POST['dep'];
	
	if($dep==0)
	{
		$test=mysqli_query($link,"select * from testmaster where category_id='1' order by testname");
	}
	else
	{
		$test=mysqli_query($link,"select * from testmaster where category_id='1' and type_id='$dep' order by testname");
	}
	?>
	<select id="tst_lst" name="tst_lst">
		<option value="0">--All(Test)--</option>
	<?php
		while($tst=mysqli_fetch_array($test))
		{
			echo "<option value='$tst[testid]'>$tst[testname]</option>";
		}
	?>
	</select>
	<?php
}
?>
