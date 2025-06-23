<?php
session_start();
include("../../includes/connection.php");
require("../../includes/global.function.php");
?>


<div style="padding:10px" align="center">
<h4>Receive Sample</h4>

<input type="hidden" id="glob_barcode" value="<?php echo $glob_barcode;?>"/>

<table class="table table-bordered table-condensed">

<?php


function sort_func( $x, $y) 
{    
	if ($x== $y) 
		return 0; 
  
	if ($x < $y) 
		return -1; 
	else
		return 1; 
} 



$pid=$_POST["uhid"];
$opd=$_POST["opd"];
$ipd=$_POST["ipd"];
$batch_no=$_POST["batch_no"];
//$glob_barcode=$_POST["glob_barcode"];
$lavel=$_POST['lavel'];
$ses=$_POST['user'];

if($opd!="")
{
	$pin=$opd;
}else if($ipd!="")
{
	$pin=$ipd;
}

$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pid'"));

$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$pid' and `opd_id`='$pin' "));

$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$dt_tm[type]' "));

$dis_id=$prefix_det["prefix"].": ".$pin;

if($pinfo["dob"]!=""){ $age=age_calculator($pinfo["dob"]); }else{ $age=$pinfo["age"]." ".$pinfo["age_type"]; }

echo "<tr style='display:none;'><th colspan='1'>UHID: <span id='h_no'>$pinfo[patient_id]</span><th>OPD ID: <span id='opd_id'>$opd</span></th><th>IPD ID: <span id='ipd_id'>$ipd</span></th><th>Batch No: <span id='batch_no'>$batch_no</span></th></tr>";

echo "<tr><th>UHID: $pinfo[patient_id]<th colspan='1'>Bill No.:$pin</th><th>Batch No: $batch_no</th></tr>";

echo "<tr><th>Name:$pinfo[name]</th><th colspan='2'>Age-Sex:$age - $pinfo[sex]</th></tr>";

echo "</table>";

echo "<table class='table table-bordered table-condensed table-report table-hover' id='samp_det_table'>";



$test=mysqli_query($link,"select * from phlebo_sample where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and batch_no='$batch_no'");
while($tst=mysqli_fetch_array($test))
{
	$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$tst[testid]'"));
	$culture=0;
	if (strpos($tname["testname"],'culture') !== false) 
	{
		$culture=1;
	}
	
	if (strpos($tname["testname"],'CULTURE') !== false) 
	{
		$culture=1;
	}
	
	if (strpos($tname["testname"],'Culture') !== false) 
	{
		$culture=1;
	}
	
	//if (strpos(strtolower($tname["testname"]),'culture') !== false)  //------------------Culture--------------//
	if($culture==1)
	{
		//~ mysqli_query($link,"delete from Testparameter where TestId='$tst[testid]'");
		$c_vac=mysqli_fetch_array(mysqli_query($link,"select vac_id from test_vaccu where testid='$tst[testid]'"));
		//~ $c_smp=mysqli_fetch_array(mysqli_query($link,"select SampleId from TestSample where TestId='$tst[testid]'"));
		//~ $parm=mysqli_query($link,"select * from Testparameter where TestId='3374'");
		//~ $parm=mysqli_query($link,"select * from Testparameter where TestId='$culture_setup_testid'");
		//~ while($par=mysqli_fetch_array($parm))
		//~ {
			//~ mysqli_query($link,"INSERT INTO `Testparameter`(`TestId`, `ParamaterId`, `sequence`, `sample`, `vaccu`, `status`) VALUES ('$tst[testid]','$par[ParamaterId]','$par[sequence]','$c_smp[SampleId]','$c_vac[vac_id]','$par[status]')");
		//~ }
		$vcc[]=$c_vac[vac_id];
	}
	else
	{
		$vaccu=mysqli_query($link,"select distinct vaccu from Testparameter where TestId='$tst[testid]' and vaccu>0");
		while($vac=mysqli_fetch_array($vaccu))
		{
			$vcc[]=$vac[vaccu];
		}	
	}
}

$vcc1=usort($vcc,"sort_func");
$vcc2=array_unique($vcc);

?>
<tr>
	<th>#</th> <th>Vaccu</th> <th>Test Name</th> <th></th>
</tr>
<?php

$i=1;
foreach($vcc2 as $vc)
{
	if($vc)
	{
		$vname=mysqli_fetch_array(mysqli_query($link,"select type from vaccu_master where id='$vc'"));
	?>
	<tr>
		<?php
			$single_barc="disabled";
			$vc_class="icon-check-empty";
			$bc_col="rgb(234, 164, 130)";
			$vac_chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from lab_sample_receive where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and batch_no='$batch_no' and `vaccu`='$vc'"));
			if($vac_chk["tot"]>0)
			{
				$vc_class="icon-check";
				$single_barc="";
				$bc_col="rgb(146, 217, 146)";
			}
			
			$chk_tot=0;
			
			$chk_lis=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from test_sample_result where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and batch_no='$batch_no' and `vaccus`='$vc' and result!=''"));
			
			
			$chk_res=mysqli_fetch_array(mysqli_query($link,"select count(a.testid) as tot from testresults a,Testparameter b where a.testid=b.TestId and a.paramid=b.ParamaterId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no'"));
			
			$chk_sum=mysqli_fetch_array(mysqli_query($link,"select count(a.testid) as tot from patient_test_summary a,Testparameter b,testresults c where a.testid=b.TestId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no' and c.patient_id!='$pid' and c.opd_id!='$opd' and c.ipd_id!='$ipd' and c.batch_no!='$batch_no'"));
			
			$chk_tot=$chk_lis["tot"]+$chk_res["tot"]+$chk_sum["tot"];
			
			if($chk_tot>0)
			{
				$onclick="check_vac_err('$vname[type]')";
				$icon_id="vacc_done";
			}
			else
			{
				$onclick="check_vac($i,$vc)";
				$icon_id="";
			}
			
		?>
			<td width="50px" onclick="<?php echo $onclick;?>" style="text-align:center;background-color:<?php echo $bc_col;?>" id="smp_td_<?php echo $i;?>">
			
		
			<i name="<?php echo $icon_id;?>" class="<?php echo $vc_class;?>" id="<?php echo $vc;?>" ></i>
		</td>
		<td onclick="<?php echo $onclick;?>" > <b><?php echo $vname["type"];?> </b></td>
		<td>
			<?php
				$tid=mysqli_query($link,"select distinct a.testid from phlebo_sample a,Testparameter b where a.testid=b.TestId and b.vaccu='$vc' and a.patient_id='$pid' and a.opd_id='$opd' and a.ipd_id='$ipd' and a.batch_no='$batch_no'");
				while($td=mysqli_fetch_array($tid))
				{
					$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$td[testid]'"));
				
					echo "<div class='tests_phlebo'>";
					
					$test_chk_str="";
					$test_chk=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from phlebo_sample where `patient_id`='$pid' and `opd_id`='$opd' and testid='$td[testid]'"));
					if($test_chk[tot]>0)
					{
						$test_chk_str="checked";
					}
				?>
				<label onclick="checked_test('<?php echo $i; ?>','<?php echo $vc; ?>','<?php echo $td["testid"]; ?>')">
					<input type="checkbox" id="test_check<?php echo $td["testid"]; ?>" value="<?php echo $td["testid"]; ?>" class="tst_vac test_vac_cls<?php echo $vc; ?>" <?php echo $test_chk_str; ?>>
				<?php
					echo $tname["testname"]."</label></div><br/>";
				}
				
				if(mysqli_num_rows($tid)==0)
				{
					
				}
			?>
		</td>
		<td>
		<?php
			if($glob_barcode==1)
			{
		?>
			<button class="btn btn-primary" <?php echo $single_barc;?> onclick="barcode_single('<?php echo $pid;?>','<?php echo $opd;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>','<?php echo $vc;?>')"><i class="icon-barcode"></i> Barcode</button> 
		<?php
			}
		?>
			<?php
				$but_vl="<i class='icon-comments-alt'></i> Clinical Note";
				$bt_class="btn btn-info";
				$bt_val="note";
				$nv=mysqli_fetch_array(mysqli_query($link,"select * from phlebo_sample_note where `patient_id`='$pid' and `opd_id`='$opd' and `ipd_id`='$ipd' and batch_no='$batch_no' and `vaccu`='$vc'"));
				if($nv)
				{
					//$but_vl="<i class='icon-comments-alt'></i> View";
					$bt_class="btn btn-success";
					$bt_val="view";
				}
			?>
			<button class="<?php echo $bt_class;?>" id="note_<?php echo $vc;?>" onclick="vac_note('<?php echo $pid;?>','<?php echo $opd;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>',<?php echo $vc;?>,'<?php echo $vname["type"];?>')" value="<?php echo $bt_val;?>" > <?php echo $but_vl;?> </button>
			<input type="hidden" id="vac_saved_note_<?php echo $vc;?>" value="<?php echo $nv["note"];?>" />
		</td>
		
	</tr>
	<?php
	$i++;
	}
}


echo "</table>";
?>
<br/>


<button id="sel_all" name="all_sel" value="Select All" class="btn btn-primary" onclick="select_all()"><i class="icon-list-ul"></i> Select All</button>


<button id="ack" name="ack" value="Receive" class="btn btn-info" onclick="sample_accept('<?php echo $pid;?>','<?php echo $opd;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>')" ><i class="icon-download-alt"></i> Receive</button>

<!--<button class="btn btn-print" onclick="print_trf('<?php echo $pid;?>','<?php echo $opd;?>','<?php echo $ipd;?>','<?php echo $batch_no;?>')"><i class="icon-print"></i> TRF</button>-->

<button" id="close" name="close" value="Close" class="btn btn-danger"  onclick="hid_mod()"><i class="icon-off"></i> Close</button>

</div>
