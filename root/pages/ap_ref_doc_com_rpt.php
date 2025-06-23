<html>
	<head>
		<title></title>
		<style>
			input[type="text"]
			{
			border:none;
			}
			.line td{border-top:1px solid;}
		</style>
		<link href="../../css/bootstrap.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/bootstrap-theme.css" type="text/css" rel="stylesheet"/>
		<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<?php
			include'../../includes/connection.php';
			
			$date1=$_GET['date1'];
			$date2=$_GET['date2'];
			$docid=$_GET['docid'];
			
			
			///for convert to month name
			$abc=substr($date2,5,2);
			$mnthnm=date('M', mktime(0, 0, 0, $abc, 10)); // March
			
			//////////////////////
			
			
			//$date2 = $_GET['date2'];
			function convert_date($date)
			{
			$timestamp = strtotime($date); 
			$new_date = date('d-m-y', $timestamp);
			return $new_date;
			}
			
			function convert_date_change($date)
			{
			$timestamp = strtotime($date); 
			$new_date = date('Y-m-d', $timestamp);
			return $new_date;
			}
			
			function val_con($val)
			{
				$nval=explode(".",$val);
				if(!$nval[1])
				{
					return $val.".00";	
				}
				else
				{
					if(!$nval[1][1])
					{
						return $val."0";	
					}
					else
					{
						return $val;	
					}
				}
			}
			
			
			
			?>
		
		<div style="text-align:center;">
         <h4>Referral Doctor Com Details</h4>
         
		<?php
			?>
				<form name="form1" id="form1" method="post" >
							 <div class="no_print bottom-margin"> <input class="btn btn-default" type="button" name="button" id="button" value="Print" onClick="javascript:window.print()" /> <input class="btn btn-default" type="button" name="button1" id="button1" value="Exit" onClick="javascript:window.close()" /></div>
							<table class="table table-bordered table-condensed table-report" style="font-size:11px" width="100%">
								<tr bgcolor="#EAEAEA" class="bline">
									<td>Sl No </td>
									<td>Doctor</td>
									<td>Patho %</td>
									<td>Radio % </td>
									<td>Cardio %</td>
									
								</tr>
								
								<?php
								  	$i=1;						
								
									if($docid==0)
									{
									  
									  $qdoc=mysqli_query($link,"select a.*,b.ref_name,b.refbydoctorid from doctor_discount a,refbydoctor_master b  where a.refdoc_id=b.refbydoctorid  order by b.ref_name");
									 
									}
									else
									{
									  									  
									  $qdoc=mysqli_query($link,"select a.*,b.ref_name,b.refbydoctorid from doctor_discount a,refbydoctor_master b  where a.refdoc_id=b.refbydoctorid and a.refdoc_id='$docid'  order by b.ref_name");
									  
									 
									}
									
									while($qdoc1=mysqli_fetch_array($qdoc)){
									$ttl=0;
									$netcomamt=0;
									?>
								<tr>
									<td style="font-size:12px"><?php echo $i;?> </td>
									<td style="font-size:12px"><?php echo $qdoc1['ref_name'];?> </td>
									<td style="font-size:12px"><?php echo $qdoc1['lab_per'];?> </td>
									<td style="font-size:12px"><?php echo $qdoc1['radio_per'];?> </td>
									<td style="font-size:12px"><?php echo $qdoc1['cardio_per'];?> </td>
								</tr>
								
								
								
								
								
								
								<?php
									$i++;}?>
							</table>
				</form>
			</div>
	</body>
</html>
