<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id=$emp_info["branch_id"];

$date=date("Y-m-d");

$pat_type=$_POST['pat_type'];
$from=$_POST['from'];
$to=$_POST['to'];
$pat_name=$_POST['pat_name'];
$category=$_POST['catagory'];
$var_id=$_POST['var_id'];
$user=$_POST['user'];

if($pat_type=="opd_id")
{
	$pin_str="OPD ID";
	$id_str="opd_id";
}
else if($pat_type=="ipd_id")
{
	$pin_str="IPD ID";
	$id_str="ipd_id";
}

if($pat_name!='')
{
	$str="select distinct a.$id_str from phlebo_sample a,testmaster b,patient_info c where a.patient_id=c.patient_id and a.testid=b.testid and b.category_id='1' and c.name like '%$pat_name%' and a.$id_str!=''";
}
else if($var_id!='')
{
	if($category=="pin")
	{
		if($pat_type=="opd_id")
		{
			$str="select distinct a.$id_str from phlebo_sample a,testmaster b where a.opd_id='$var_id' and a.testid=b.testid and b.category_id='1'  and a.$id_str!=''";
		}
		else
		{
			$str="select distinct a.$id_str from phlebo_sample a,testmaster b where a.ipd_id='$var_id' and a.testid=b.testid and b.category_id='1'  and a.$id_str!=''";
		}
	}
	else if($category=="uhid")
	{
		$str="select distinct a.$id_str from phlebo_sample a,testmaster b where a.patient_id='$var_id' and a.testid=b.testid and b.category_id='1' and a.$id_str!=''";
	}
}
else
{
	$str="select distinct a.$id_str from phlebo_sample a,testmaster b where a.testid=b.testid and b.category_id='1' and a.date between '$from' and '$to' and a.$id_str!=''";
}

$str.=" ORDER BY a.`slno` DESC";

//echo $str;

$qryy=mysqli_query($link, $str );
//$qryy_num=mysqli_num_rows($qryy);
$qryy_num=1;
if($qryy_num!=0)
{
?>
<input type="hidden" id="search_type" value="<?php echo $type;?>"/>
<table class="table table-bordered table-condensed table-report">
	<th width="32">#</th>
	<th>BILL ID</th>
	<th class="serial_num">Serial</th>
	<th>Name-Phone</th>
	<th>Age/Sex</th>
	<th>Batch</th>
	<th>Date</th>
	<th>Time</th>
<?php
	$n=1;
	while($valu=mysqli_fetch_array($qryy))
	{
		$opd_id="---";
		$ipd_id="---";
		if($valu["opd_id"]!="")
		{
			$pin=$opd_id=$valu["opd_id"];
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id` in ( SELECT distinct `patient_id` FROM `phlebo_sample` WHERE `opd_id`='$opd_id' ) "));
			
			$pat_pay_detail_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$pat_info[patient_id]' and `opd_id`='$opd_id' and advance='0' and dis_amt='0'"));
			if($pat_pay_detail_num==0)
			{
				$display="Yes";
			}else
			{
				//$display="No";
				$display="Yes";
			}
		}
		if($valu["ipd_id"])
		{
			$pin=$ipd_id=$valu["ipd_id"];
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id` in ( SELECT distinct `patient_id` FROM `phlebo_sample` WHERE `ipd_id`='$ipd_id' ) "));
			$display="Yes";
		}
		if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
		
		
		$cls="grey";
		if($display=="Yes")
		{
			// For different batch No
			$batch_qry=mysqli_query($link, " SELECT distinct `batch_no` FROM `phlebo_sample` WHERE `patient_id`='$pat_info[patient_id]' and `opd_id`='$valu[opd_id]' and `ipd_id`='$valu[ipd_id]' and `sampleid`!=0 order by `slno` DESC ");
			$batch_num=mysqli_num_rows($batch_qry);
			//$batch_num=1;
			$b_num=1;
			while($batch_val=mysqli_fetch_array($batch_qry))
			{
				
				// phlebo_sample
				//$pat_tot_num=mysqli_num_rows(mysqli_query($link, "select distinct(testid) from phlebo_sample where `patient_id`='$pat_info[patient_id]' and `opd_id`='$valu[opd_id]' and `ipd_id`='$valu[ipd_id]' and `batch_no`='$batch_val[batch_no]' and sampleid>1 and sampleid!='76'"));
				
						
				$pat_tot_num=mysqli_num_rows(mysqli_query($link, "select distinct b.vaccu from phlebo_sample a,Testparameter b where a.patient_id='$pat_info[patient_id]' and a.opd_id='$valu[opd_id]' and a.ipd_id='$valu[ipd_id]' and a.batch_no='$batch_val[batch_no]' and b.vaccu>0 and a.testid=b.TestId"));
				
							
				// phlebo_sample
				$phlebo_num=mysqli_num_rows(mysqli_query($link, "select distinct(vaccu) from lab_sample_receive where `patient_id`='$pat_info[patient_id]' and `opd_id`='$valu[opd_id]' and `ipd_id`='$valu[ipd_id]' and `batch_no`='$batch_val[batch_no]' "));
				
				$tot_test_diff=($pat_tot_num-$phlebo_num);
				if($phlebo_num==0)
				{
					// Not received at all
					$style_span="background-color: #d59a9a;";
				}else if($pat_tot_num==$phlebo_num)
				{
					// All received
					$style_span="background-color: #9dcf8a;";
				}else
				{
					// Partially received
					$style_span="background-color: #f6e8a8;";
				}
				$img="";
				$pat_reg=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where `patient_id`='$pat_info[patient_id]' and `opd_id`='$valu[opd_id]'"));
				if($pat_reg["urgent"]==1)
				{
					$cls=" urgent";
					$img="<img src='../images/blinking_dot.gif' style='width:10px;' />";
				}
				
				$batch_test_time=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `phlebo_sample` WHERE `patient_id`='$pat_info[patient_id]' and `opd_id`='$valu[opd_id]' and `ipd_id`='$valu[ipd_id]' and `batch_no`='$batch_val[batch_no]' "));
				if($b_num==1)
				{
		?>
			<tr class="<?php echo $cls; ?>" onClick="load_sample('<?php echo $pat_info["patient_id"]; ?>','<?php echo $valu["opd_id"]; ?>','<?php echo $valu["ipd_id"]; ?>','<?php echo $batch_val["batch_no"]; ?>')" style="cursor:pointer;">
				<!--<td rowspan="<?php echo $batch_num; ?>"><span class="btn_round" style="<?php echo $style_span; ?>"><?php echo $n; ?></span></td>-->
				<td rowspan="<?php echo $batch_num; ?>"><?php echo $n; ?></td>
				<td rowspan="<?php echo $batch_num; ?>"><?php echo $pin; ?></td>
				<td class="serial_num" rowspan="<?php echo $batch_num; ?>"><?php echo $pat_reg['ipd_serial']; ?></td>
				<td rowspan="<?php echo $batch_num; ?>"><?php echo $pat_info["name"]." - ".$pat_info["phone"]; ?><span style="float:right;"><?php echo $img;?></span></td>
				<td rowspan="<?php echo $batch_num; ?>"><?php echo $age."/".$pat_info["sex"]; ?></td>
				<td><span class="btn_round" style="<?php echo $style_span; ?>"><?php echo $batch_test_time["batch_no"]; ?></span></td>
				<td><?php echo convert_date_g($batch_test_time["date"]); ?></td>
				<td><?php echo convert_time($batch_test_time["time"]); ?></td>
			</tr>
		<?php
				$b_num++;
				}else
				{
				?>
					<tr class="<?php echo $cls; ?>" onClick="load_sample('<?php echo $pat_info["patient_id"]; ?>','<?php echo $valu["opd_id"]; ?>','<?php echo $valu["ipd_id"]; ?>','<?php echo $batch_val["batch_no"]; ?>')" style="cursor:pointer;">
						<td><span class="btn_round" style="<?php echo $style_span; ?>"><?php echo $batch_test_time["batch_no"]; ?></span></td>
						<td><?php echo $batch_test_time["date"]; ?></td>
						<td><?php echo $batch_test_time["time"]; ?></td>
					</tr>
				<?php
				}
			}
			$n++;
		}
	}
}
?>
</table>
<style>
	.serial_num
	{
		display:none;
	}
	.btn_round
	{
		color:#000;
		padding:3px;
		border-radius: 7em;
		//background-color: #d59a9a; #9dcf8a;
		padding-right: 7px;
		padding-left: 7px;
		box-shadow: inset 1px 1px 0 rgba(0,0,0,0.6);
		transition: all ease-in-out 0.2s;
	}
	tr.green:hover td span,
	tr.red:hover td span,
	tr.yellow:hover td span,
	tr.grey:hover td span
	{
		padding:8px;
		padding-right:12px;
		padding-left:12px;
	}
</style>
<!--
<input type="hidden" id="search_type" value="<?php echo $typ;?>"/>


<table class="table table-bordered table-condensed">
<th>#</th><th>UHID No</th><th>Visit No</th><th>Reg ID</th><th>Name-Phone</th><th>Age</th><th>Sex</th><th>Time</th><th>Date</th>
<?php
/*

if(!$val)
{
	if($typ=="date")
	{
		$fdate=$_POST[fdate];
		$tdate=$_POST[tdate];
		
		$qry=mysqli_query($link, "select * from patient_reg_details where date between '$fdate' and '$tdate'");
		$chk_v=1;
	}
	else
	{
		
		$date=strtotime(date('Y-m-d'));
		$date_1=date("Y-m-d");
		$date_2=date("Y-m-d",strtotime('-3 day',$date));
		$qry=mysqli_query($link, "select * from patient_reg_details where date='$date_1' order by slno desc");
		$chk_v=1;
	}
}
else
{
	if($typ=="vid")
	{
		$qry=mysqli_query($link, "select * from patient_reg_details where reg_no like '%$val%' order by slno desc");
		
	}
	else if($typ=="name")
	{
		$qry=mysqli_query($link, "select * from patient_reg_details where patient_id in(select patient_id from patient_info where name like '$val%')");	
	}
	else if($typ=="lid")
	{
		$qry=mysqli_query($link, "select * from patient_reg_details where reg_no like '$val%' order by slno desc");
		
	}
	$chk_v=1;
}




	$i=1;
	while($tst=mysqli_fetch_array($qry))
	{
		$chk=mysqli_query($link, "select * from phlebo_sample where patient_id='$tst[patient_id]' and visit_no='$tst[visit_no]' and sampleid>1 and sampleid!='76'");	
		
		$num=mysqli_num_rows($chk);
		$num1=mysqli_num_rows(mysqli_query($link, "select distinct testid from testresults where patient_id='$tst[patient_id]' and visit_no='$tst[visit_no]'"));
		
		if($chk_v)
		{
			$num1=0;
		}		
		$num3=$num-$num1;
		

		if($num>0 && $num3!=0)
		{
			$bill=mysqli_fetch_array(mysqli_query($link, "select * from payment_detail where patient_id='$tst[patient_id]' and visit_no='$tst[visit_no]' and typeofpayment='A'"));
			$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$tst[patient_id]'"));
			
			$num1=mysqli_num_rows(mysqli_query($link, "select * from phlebo_sample where patient_id='$tst[patient_id]' and visit_no='$tst[visit_no]' and sampleid>1 and sampleid!=76"));
			$num2=mysqli_num_rows(mysqli_query($link, "select * from phlebo_sample where patient_id='$tst[patient_id]' and visit_no='$tst[visit_no]' and sampleid>1"));
			
			$rp_print=mysqli_num_rows(mysqli_query($link, "select * from testreport_print where patient_id='$tst[patient_id]' and visit_no='$tst[visit_no]' and testid in(select testid from phlebo_sample where patient_id='$tst[patient_id]' and visit_no='$tst[visit_no]' and sampleid>1)"));
			
			$num3=$num1-$num2;
			
			$cls="";
			if($num3=="0")
			{
				$cls="green";
			}
			else if($num3>0)
			{
				$cls="yellow";
			}
			
			if($num2=="0")
			{
				$cls="red";
			}
			
			$cls_span="";
			if($tst[urgent]>0)
			{
				$cls_span=" animated infinite bounceIn";	
			}
			
			if($num2==$rp_print && $cls!="red")
			{
				$cls="grey";
				$cls_span="";	
			}
			
			echo "<tr class='$cls' id='samp$i' onclick=load_sample('$tst[patient_id]','$tst[visit_no]')><td><span class='$cls_span'>$i</span></td><td>$id_pref[pid_prefix] $tst[patient_id]</td><td>$tst[visit_no]</td><td>$tst[reg_no]</td><td>$pinfo[name]-$pinfo[phone]</td><td>$pinfo[age] $pinfo[age_type]</td><td>$pinfo[sex]</td><td>$tst[time]</td><td>$tst[date]</td></tr>";			
			$i++;
			
		}
		 
		
	}
/*	
}
*/
?>
</table>
