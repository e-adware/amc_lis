<?php
include("../../includes/connection.php");

if($reporting_without_sample_receive==1)
{
	//$table_name="phlebo_sample";
	$table_name="lab_sample_receive";
	$sample_id="sampleid";
}
else
{
	$table_name="patient_test_details";
	$sample_id="sample_id";
}

$type=$_POST['type'];
if($type==1)
{
	$fdate=$_POST['fdate'];	
	$tdate=$_POST['tdate'];	
	$pat_type=$_POST['pat_type'];
	$dept=$_POST['dept'];
	
	$dep_str="";
	if($dept>0)
	{
		$dep_str="and b.type_id='$dept'";
	}
	
	 $str="select distinct a.opd_id,a.ipd_id from $table_name a,testmaster b where a.date between '$fdate' and '$tdate' and a.testid=b.testid and b.category_id='1' $dep_str and a.$pat_type!='' order by a.slno";
	
	$qry=mysqli_query($link, $str);
}
else
{
	$val=$_POST['val'];
	$typ=$_POST['s_typ'];
	$pat_type=$_POST['pat_type'];
	
	$b_sty="";
	$u_sty="";
	
	if($pat_type=="opd_id")
	{
		$pin_str="OPD ID";
	}
	if($pat_type=="ipd_id")
	{
		$pin_str="IPD ID";
	}
	
	if($typ=="uhid")
	{
		if($pat_type=="opd_id")
		{
			$q=" SELECT distinct `opd_id` FROM `$table_name` WHERE `patient_id` like '$val%' and `ipd_id`='' ";
		}else if($pat_type=="ipd_id")
		{
			$q=" SELECT distinct `ipd_id` FROM `$table_name` WHERE `patient_id` like '$val%' and `opd_id`='' ";
		}
	}
	if($typ=="pin")
	{
		if($pat_type=="opd_id")
		{
			$q=" SELECT distinct `opd_id` FROM `$table_name` WHERE `opd_id` like '$val%' and `ipd_id`='' ";
		}else if($pat_type=="ipd_id")
		{
			$q=" SELECT distinct `ipd_id` FROM `$table_name` WHERE `ipd_id` like '$val%' and `opd_id`='' ";
		}
	}
	$q.=" and `$sample_id`!=0 order by `slno` DESC ";
	
	if($typ=="name")
	{
		if(strlen($val)>3)
		{
			if($pat_type=="opd_id")
			{
				$q=" SELECT distinct a.opd_id FROM $table_name a,patient_info b WHERE a.patient_id=b.patient_id and b.name like '%$val%'";
			}else if($pat_type=="ipd_id")
			{
				$q=" SELECT distinct a.ipd_id FROM $table_name a,patient_info b WHERE a.patient_id=b.patient_id and b.name like '%$val%'";
			}
			$q.=" and a.$sample_id!=0 order by a.slno DESC ";
		}
	}
	
	$u_sty="style='font-weight:bold'";
	
	$qry=mysqli_query($link, $q);
}
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d-M-y', $timestamp);
	return $new_date;
}

function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

//echo $q;
?>
	<table class="table table-bordered table-condensed">
	<th>#</th><th>UHID No</th><th>Bill No.</th><th>Name</th><th>Batch No</th><th>Date Time</th>
	<th>Collection Center</th>
	<th>User</th>
	<?php
		$i=1;
		while($qr=mysqli_fetch_array($qry))
		{
			$cls="";
			//$name=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$qr[patient_id]'"));
			$opd_id="---";
			$ipd_id="---";
			if($qr["opd_id"]!=="")
			{
				$pin_str="OPD ID";
				$pin=$opd_id=$qr["opd_id"];
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id` in ( SELECT distinct `patient_id` FROM `patient_test_details` WHERE `opd_id`='$opd_id' ) "));
			}
			if($qr["ipd_id"])
			{
				$pin_str="IPD ID";
				$pin=$ipd_id=$qr["ipd_id"];
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id` in ( SELECT distinct `patient_id` FROM `patient_test_details` WHERE `ipd_id`='$ipd_id' ) "));
			}
			
			// For different batch No
			$batch_qry=mysqli_query($link, " SELECT distinct `batch_no` FROM `patient_test_details` WHERE `patient_id`='$pat_info[patient_id]' and `opd_id`='$qr[opd_id]' and `ipd_id`='$qr[ipd_id]' and `sample_id`!=0 order by `slno` DESC ");
			$batch_num=mysqli_num_rows($batch_qry);
			while($batch_val=mysqli_fetch_array($batch_qry))
			{
				$dt_usr=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `date`,`time`,`user` FROM `patient_test_details` WHERE `patient_id`='$pat_info[patient_id]' and `opd_id`='$qr[opd_id]' and `ipd_id`='$qr[ipd_id]' and `batch_no`='$batch_val[batch_no]' "));
				$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$dt_usr[user]' "));
				
				//------Status------//
				$tot_path=mysqli_fetch_array(mysqli_query($link,"select count(distinct a.testid) as tot from patient_test_details a,testmaster b where a.patient_id='$pat_info[patient_id]' and a.opd_id='$qr[opd_id]' and a.ipd_id='$qr[ipd_id]' and a.batch_no='$batch_val[batch_no]' and a.testid=b.testid and b.category_id='1' and b.type_id!='132'"));
				
				$tot_path_res=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from testresults where patient_id='$pat_info[patient_id]' and opd_id='$qr[opd_id]' and ipd_id='$qr[ipd_id]' and batch_no='$batch_val[batch_no]'"));
				
				$tot_path_sum=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from patient_test_summary where patient_id='$pat_info[patient_id]' and opd_id='$qr[opd_id]' and ipd_id='$qr[ipd_id]' and batch_no='$batch_val[batch_no]' and testid not in(select testid from testresults where patient_id='$pat_info[patient_id]' and opd_id='$qr[opd_id]' and ipd_id='$qr[ipd_id]' and batch_no='$batch_val[batch_no]')"));
				
				$tot_path_wid=mysqli_fetch_array(mysqli_query($link,"select * from widalresult where patient_id='$pat_info[patient_id]' and opd_id='$qr[opd_id]' and ipd_id='$qr[ipd_id]' and batch_no='$batch_val[batch_no]' limit 1"));
				
				$tot_path_pls=$tot_path_res[tot]+$tot_path_sum[tot]+$tot_path_wid[tot];
				
				$style_span="background-color: #d59a9a;"; //--RED--//
				if($tot_path_pls==$tot_path[tot])
				{
					$style_span="background-color:#9dcf8a;"; //--Green--//
					
					$tot_path_print=mysqli_fetch_array(mysqli_query($link,"select count(distinct testid) as tot from testreport_print where patient_id='$pat_info[patient_id]' and opd_id='$qr[opd_id]' and ipd_id='$qr[ipd_id]' and batch_no='$batch_val[batch_no]'"));
					if($tot_path_print[tot]==$tot_path[tot])
					{
						$style_span="background-color:#89898D;"; //--Grey--//
					}
				}
				if($tot_path[tot]>$tot_path_pls && $tot_path_pls>0)
				{
					$style_span="background-color:yellow;"; //----Yellow--//
				}
				//----------------- //
				$img="";
				$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT a.`centrename`, b.`urgent` FROM `centremaster` a, `uhid_and_opdid` b WHERE a.`centreno`=b.`center_no` AND b.`patient_id`='$pat_info[patient_id]' AND b.`opd_id`='$qr[opd_id]' "));
				if($pat_reg["urgent"]==1)
				{
					$cls=" urgent";
					$img="<img src='../images/blinking_dot.gif' style='width:10px;' />";
				}
		?>
			<tr class="<?php echo $cls; ?>" id="path_tr<?php echo $i;?>" onClick="load_test_detail('<?php echo $pat_info['patient_id'];?>','<?php echo $pin;?>','<?php echo $batch_val[batch_no];?>')" style="cursor:pointer;">
				<td><span class="btn_round" style="<?php echo $style_span;?>"><?php echo $i;?></span></td>
				<td><?php echo $pat_info['patient_id'];?></td>
				<td><?php echo $pin;?></td>
				<td><?php echo $pat_info['name'];?><span style="float:right;"><?php echo $img;?></span></td>
				<td><?php echo $batch_val['batch_no'];?></td>
				<td><?php echo convert_date($dt_usr['date'])." ".convert_time($dt_usr['time']);?></td>
				<td><?php echo $pat_reg['centrename'];?></td>
				<td><?php echo $user_info["name"];?>
				<div id="path_pat<?php echo $i;?>" style="display:none">
					<?php echo "@".$pat_info['patient_id']."@".$pin."@".$batch_val['batch_no'];?>
				</div>
				</td>
			</tr>
		<?php	
				$i++;
			}
		}
	
	
	
	?>
	</table>
	
	



