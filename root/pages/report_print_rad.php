<html>
<head>
	<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../css/custom.css" />
		<script src="../../js/jquery.min.js"></script>
		<script src="../../js/bootstrap.min.js"></script>
		<link href="../../css/report.css" rel="stylesheet" type="text/css">
		<script>
			function close_window(e)
			{
				var unicode=e.keyCode? e.keyCode : e.charCode;
				
				if(unicode==27)
				{	
					window.close();
				}
			}
			function save_print_test(tst,opd_id,ipd_id,uhid,batch_no)
			{
				$.post("report_print_path_save.php",
				{
					tst:tst,
					opd_id:opd_id,
					ipd_id:ipd_id,
					uhid:uhid,
					batch_no:batch_no,
					type:"sing"
				},
				function(data,status)
				{
					//window.close();
				})
			}
		</script>
		<style>
		.custom-font{ font-size:12px;}
		.doc td{ font-size:14px;}
		
		@page
		{
			margin-right:0.5cm;
			margin-left:1cm;
		}
		</style>
</head>
<body onkeyup="close_window(event)" onafterprint="save_print_test('<?php echo $_GET['tstid'];?>','<?php echo $_GET['opd_id'];?>','<?php echo $_GET['ipd_id'];?>','<?php echo $_GET['uhid'];?>','<?php echo $_GET['batch_no'];?>');">

<?php
	include("../../includes/connection.php");
	$uhid=$_GET['uhid'];
	$opd_id=$_GET['opd_id'];
	$ipd_id=$_GET['ipd_id'];
	$batch_no=$_GET['batch_no'];
	$tst=$_GET['tstid'];

	$tname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testmaster where testid='$tst'"));
	if($tname[category_id]==2)
	{
		$rep_header="Department of Radiology";
		$doc_cat=2;
	}
	elseif($tname[category_id]==3)
	{
		$rep_header="Department of Cardiology";
		$doc_cat=3;
	}
	$t_dept=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `test_department` WHERE `id` IN(select type_id from testmaster where testid='$tst')"));
	
	$rep_header="Report : ".$t_dept["name"];
	//$doc_cat=2;
	
	
	$test_name=$tname[testname];
	$chk_name=mysqli_fetch_array(mysqli_query($link,"select testname from testresults_rad where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and testid='$tst'"));
	
	if($chk_name[testname]!='')
	{
		$test_name=$chk_name[testname];
	}
	
	
	$pinfo=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from patient_info where patient_id='$uhid'"));
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
	$v_text="Bill No";
	
	$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
	
	
	$doc=mysqli_fetch_array(mysqli_query($link, "select refbydoctorid,ref_name,qualification from refbydoctor_master where refbydoctorid in( SELECT `refbydoctorid` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$v_id' )"));
    
   // $dname="Dr. ".$doc['ref_name']." , ".$doc['qualification'];
    
	if($doc['refbydoctorid']!="101")
	{
		$dname="Dr. ".$doc['ref_name']." , ".$doc['qualification'];
	}
	else
	{
		$dname="SELF";
	}
	
	$cent=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select centrename from  centremaster where centreno in(select centreno from patient_details where patient_id='$uhid' and visit_no='$visit')"));
	
	$res=mysqli_fetch_array(mysqli_query($link, "select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));
	
	
	
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
	
	
	$i=1;
	$res1=$res[observ];
	$res_s=explode("@@nextpage@@",$res1);
	$tot=sizeof($res_s);
	foreach($res_s as $rs)
	{
?>



	
	<br/><br/><br/><br/><br/>
	<div class="container-fluid">
			<div class="row">
				<?php
					//include("page_header.php");
				?>
				<div class="span10">
				<?php if($t_dept['id']==136){ echo "<br>"; } ?>
					<h3><?php echo $rep_header;?></h3>
					
					<div style="border-top:1px solid;border-bottom:1px solid;margin-top:-15px;">
					<table class="table borderless custom-font">
						<tr>
							<td>UHID</td>
							<td style="font-size: 13px;">: <?php echo $pinfo['patient_id'];?></td>
							<td style="font-size: 13px;"><?php echo $v_text; ?></td>
							<td style="font-size: 13px;">: <?php echo $v_id;?></td>
							
						</tr>
						<tr>
							<td width="15%"  style="font-size: 13px;">Name</td>
							<td  style="font-size: 13px;">: <b><?php echo $pinfo[name];?></b></td>
							<td  style="font-size: 13px;" class="text-left" >Reg. Date/Time</td>
							<td  style="font-size: 13px;">: <?php echo convert_date($reg['date'])."/".convert_time($reg['time']);?></td>
							<input type="hidden" id="online_cen" value="<?php echo $cname[onLine];?>"/>
						</tr>
						<tr>
							<td  style="font-size: 13px;">Age/Gender</td>
							<td  style="font-size: 13px;">: <?php echo $pinfo[age]." ".$pinfo[age_type];?> / <?php echo$pinfo['sex']; ?></td>
							<td  style="font-size: 13px;" class="text-left">Printing Date/Time</td>
							<td  style="font-size: 13px;" contenteditable="true">: <?php echo convert_date(date('Y-m-d'))."/".convert_time(date('H:i:s'));?></td>
						</tr>
						<tr>
							<td  style="font-size: 13px;">Address</td>
							<td  style="font-size: 13px;" colspan="3">: <?php echo $add_info["city"];?></td>
						</tr>
						<tr>
							<td  style="font-size: 13px;">Ref. By</td>
							<td  style="font-size: 13px;" colspan="3">: <?php echo $dname;?></td>
						</tr>
						<tr style="border: 1px solid #000;">
							<td colspan="4">
								<div>
									<script src="../../JsBarcode/dist/JsBarcode.all.min.js"></script>
									<script src="../../JsBarcode/dist/JsBarcode.all.js"></script>
									<center>
										<div style="margin-left: -1%;">
											
											<svg id="barcode3"></svg>
											<script>
												
												var val="<?php echo $uhid.'-'.$v_id.'-'.$pinfo['name']; ?>";
												JsBarcode("#barcode3", val, {
													format:"CODE128",
													displayValue:false,
													fontSize:10,
													width:1,
													height:20,
												});
											</script>
										</div>
									</center>
								</div>
							</td>
						</tr>
					</table>
					</div>
		<!-- border: 1px solid #867070; -->
		<div style="font-size:24px;text-align:center;margin-top:3px;margin-bottom:5px;width: 98%;margin-left: 1%;" contenteditable="true">
			<b>
				<?php
					
					echo $test_name;
					
				?>
			</b>
		</div>
		<div class="result_contents" style="min-height:670px;text-align:left;" id="rad_res">
				
				<?php 
				echo html_entity_decode($rs);
				
				if($tot==$i)	
				{
					echo "<br/><div style='text-align:center'><b>*** End of report ***</b></div>";
				}
				else
				{
					echo "<br/><div style='text-align:center'><b>*** Cont. on to next page ***</b></div>";
					
				}
				?>
		</div>	
		<?php
		if($tot==$i)	
		{
			
		}
		else
		{
			
			echo "<div class='page_break'></div>";
		}
		?>
		


<?php
$i++;
}

if($tot>1)
{
	//$sty="width:100%;text-align:center;";
	$sty="position:fixed;bottom:85px;left:0;width:100%;text-align:center;";
}
else
{
	$sty="position:fixed;bottom:85px;left:0;width:100%;text-align:center;";
}

if($t_dept['id']==136)
{
	$sty.="display:none;";
}

?>


<div id="doctors1" style="<?php echo $sty;?>">
                            
                                <?php
                                
                                	$doc=mysqli_query($GLOBALS["___mysqli_ston"], "select * from lab_doctor where category='$doc_cat' order by sequence");	
									$zz=1;
                                    while($d=mysqli_fetch_array($doc))
                                    {
                                        ?>
                                            <table class="doc" id="lab_doc<?php echo $zz; ?>">
                                                <tr>
                                                    <td><?php echo $d[name]." , ".$d[qual];?></td>
                                                    <!--<td><b><?php echo $d[name];?></b></td>-->
                                                </tr>	
<!--
                                                <tr>
                                                    <td><?php echo $d[qual];?></td>
                                                </tr>	
-->
                                                <tr>
                                                    <td><?php echo $d[desig];?></td>
                                                </tr>	
                                               
                                            </table>
                                        <?php
                                        $zz++;
                                    }
                                ?>
                           <div style="clear:both"></div>     
                          
<!--
                        <table width="100%">
                        <tr>
                            <td style="font-size:13px;font-style:italic;text-align:center">
                              (NB: Radiological/sonological diagnosis is not always confirmatory. It must be <br/> correlated clinically and with other investigations whenever applicable)
                            </td>
                        </tr>
                        </table>  
-->
            </div>
<script>
	
	
	
	
	/*
	$("#rad_res p span").each( function () {
        if(parseInt($(this).css("fontSize"))<13)
        {
			$("*").css({'font-size':'13px'});	
		}   
    }*/
);
</script>

<script>$("#rad_res table").attr("class","");</script>
</body>
</html>
<script>//window.print();window.close()</script>
<style>
	#lab_doc1
	{
		float:left;
	}
	#lab_doc2
	{
		float:right;
	}
</style>
