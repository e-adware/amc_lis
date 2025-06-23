<html>
<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="../../css/report.css" rel="stylesheet" type="text/css">
		<script src="../../js/jquery.js"></script> 
		<script>
			function save_print_test(tst,opd,ipd,uhid,batch_no)
			{
				$.post("report_print_path_save.php",
				{
					tst:tst,
					opd_id:opd,
					ipd_id:ipd,
					batch_no:batch_no,
					uhid:uhid,
					type:"sing"
				},
				function(data,status)
				{
					window.opener.load_test_info2(data);
					window.opener.load_pat_print();
				})
			}
			function close_window(e)
			{
				var unicode=e.keyCode? e.keyCode : e.charCode;

				if(unicode==27)
				{
						window.close();
				}
			}
			
		</script>
		<style>
			*{font-size:12px}
		</style>
</head>
<body onafterprint="save_print_test('<?php echo $_GET[tstid];?>','<?php echo $_GET[opd_id];?>','<?php echo $_GET[ipd_id];?>','<?php echo $_GET[batch_no];?>','<?php echo $_GET[uhid];?>')" onkeypress="close_window(event)">
	<?php
			include("../../includes/connection.php");
			$uhid=$_GET['uhid'];
			$opd_id=$_GET['opd_id'];
			$ipd_id=$_GET['ipd_id'];
			$batch_no=$_GET['batch_no'];
			$tst=$_GET['tstid'];
			
			$nb_text_card=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='3' "));
			
			$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$tst'"));
			
			include("page_header.php");
			
	?>
	
	<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<h3>CARDIOLOGY REPORT</h3>

				<?php
				$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));
				//$reg=mysqli_fetch_array(mysqli_query($link, "select * from patient_reg_details where patient_id='$uhid' and visit_no='$visit'"));
				
				if($opd_id!="")
				{
					$v_text="OPD ID";
					$v_id=$opd_id;
					$reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				}else if($ipd_id!="")
				{
					$v_text="IPD ID";
					$v_id=$ipd_id;
					$reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_details` WHERE `patient_id`='$uhid' and `ipd_id`='$ipd_id' "));
				}
				$v_text="PIN";
				$doc=mysqli_fetch_array(mysqli_query($link, "select refbydoctorid,ref_name,qualification from refbydoctor_master where refbydoctorid in( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$v_id' )"));
    
				if($doc['refbydoctorid']!="101")
				{
					$dname="Dr. ".$doc['ref_name']." , ".$doc['qualification'];
				}
				else
				{
					$dname="Self";
				}



						function convert_date($date)
						{
							$timestamp = strtotime($date); 
							$new_date = date('d-M-Y', $timestamp);
							return $new_date;
						}
						// Time format convert
						function convert_time($time)
						{
							$time = date("g:i A", strtotime($time));
							return $time;
						}
					$id_pref=mysqli_fetch_array(mysqli_query($link,"select * from id_setup limit 1"));
?>
					<table class="table borderless bordert-top-bottom">
						<tr>
							<td><b>UHID</b></td>
							<td><b>: <?php echo $pinfo['patient_id'];?></b></td>
							<td><b><?php echo $v_text; ?></b></td>
							<td><b>: <?php echo $v_id;?></b></td>
							
						</tr>
						<tr>
							<td width="15%"><b>Name</b></td>
							<td><b>: <?php echo $pinfo[name].' / '.$pinfo[phone];?></b></td>
							<td class="text-left" >Reg. Date/Time</td>
							<td>: <?php echo convert_date($reg[date])."/".convert_time($reg[time]);?></td>
							<input type="hidden" id="online_cen" value="<?php echo $cname[onLine];?>"/>
						</tr>
						<tr>
							<td>Age/Sex</td>
							<td>: <?php echo $pinfo[age]." ".$pinfo[age_type];?> / <?php echo$pinfo['sex']; ?></td>
							<td class="text-left">Printing Date/Time</td>
							<td contenteditable="true">: <?php echo convert_date(date('Y-m-d'))."/".convert_time(date('H:i:s'));?></td>
						</tr>
						<!--<tr>
							<td>Ref. By</td>
							<td>: <?php echo $dname;?></td>
							<td class="text-left"></td>
							<td ><?php echo $vcntr;?></td>
						</tr>-->
					</table>

<?php
$res=mysqli_fetch_array(mysqli_query($link, "select * from testresults_card where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));
?>

<div style="font-size:15px;text-align:center;" contenteditable="true">
	<b><u><?php echo $tname['testname'];?></u></b></div><br/>
<div style="min-height:600px;text-align:left;font-size:14px;width:100%">


<?php 
$res1=$res[observ];
$res_s=explode("@",$res1);
if($res_s[1])
{
	?>
	<div style="min-height:520px;text-align:left;font-size:14px">
	<?php
		echo $res_s[0];
	?>
	</div>
	<?php
	//echo "<br/><b><i>Continued in next page...</i></b>";
	echo "<div id='page_break' style='page-break-after: always;'></div>";
	?>
	
	<br/><br/><br/>
	<h3>CARDIOLOGY REPORT</h3>
	
	<table class="table borderless bordert-top-bottom">
		<tr>
			<td><b>UHID</b></td>
			<td><b>: <?php echo $pinfo['patient_id'];?></b></td>
			<td><b><?php echo $v_text; ?></b></td>
			<td><b>: <?php echo $v_id;?></b></td>
			
		</tr>
		<tr>
			<td width="15%"><b>Name</b></td>
			<td><b>: <?php echo $pinfo[name].' / '.$pinfo[phone];?></b></td>
			<td class="text-left" >Reg. Date/Time</td>
			<td>: <?php echo convert_date($reg[date])."/".convert_time($reg[time]);?></td>
			<input type="hidden" id="online_cen" value="<?php echo $cname[onLine];?>"/>
		</tr>
		<tr>
			<td>Age/Sex</td>
			<td>: <?php echo $pinfo[age]." ".$pinfo[age_type];?> / <?php echo $pinfo['sex']; ?></td>
			<td class="text-left">Printing Date/Time</td>
			<td contenteditable="true">: <?php echo convert_date(date('Y-m-d'))."/".convert_time(date('H:i:s'));?></td>
		</tr>
		<!--<tr>
			<td>Ref. By</td>
			<td>: <?php echo $dname;?></td>
			<td class="text-left"></td>
			<td ><?php echo $vcntr;?></td>
		</tr>-->
	</table>
	
	
	<?php
	echo $res_s[1];
}
else
{
	?>
	<div style="min-height:480px;text-align:left;font-size:14px;">
	<?php
		echo $res1;
	?>
	</div>
	<?php
}

?>

</div></div></div>
				<div id="doctors1" style="position:fixed;bottom:0;left:0;width:100%;text-align:center;font-size:12px;">
                            
                                <?php
									//$doc=mysqli_query($link, "select * from lab_doctor where category='3'");	
									$doc=mysqli_query($link, "select * from lab_doctor where category='2'");	
									
                                    while($d=mysqli_fetch_array($doc))
                                    {
                                        ?>
                                            <table class="doc">
                                                <tr>
                                                    <td><?php echo $d[name]." , ".$d[qual];?></td>
                                                </tr>	
                                                <tr>
                                                    <td><?php echo $d[desig];?></td>
                                                </tr>
                                            </table>
                                        <?php
                                    }
                                ?>
                           <div style="clear:both"></div>     
                          
                        <table width="100%">
                        <tr>
                            <td style="font-size:12px;font-style:italic;text-align:center">
                                <p><?php echo $nb_text_card["nb_text"]; ?></p>
                            </td>
                        </tr>
                        </table>  
            </div>
			
			
</div>
	
</body>

</html>
<style>
h3 {
	margin: 0;
}
h4 {
    margin: 0;
}
</style>
