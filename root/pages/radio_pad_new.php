
<div style="padding:10px;width:97%;">
<?php

include("../../includes/connection.php");


$tstinfo=$_POST['tinfo'];
$did=$_POST['did'];

$tst=explode("@",$tstinfo);

$uhid=trim($tst[4]);
$opd_id=trim($tst[5]);
$ipd_id=trim($tst[6]);
$batch_no=trim($tst[7]);
$category_id=$_POST['category_id'];


$name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name,sex,age,age_type,phone from patient_info where patient_id='$uhid'"));
$obsrv=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst[2]'"));



if($obsrv['observ']!='')
{
	$res=$obsrv['observ'];	
	$tname=trim($obsrv[testname]);
}
else if($obsrv[observ]=="<p><br></p>")
{
	$res=$obsrv[observ];
	$tname=$tst[3];
}
else
{
	$doc_normal=1;
	$norm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select normal from radiology_normal where testid='$tst[2]' and doctor='$did'"));	
	if(!$norm[normal] || $norm[normal]=="<p><br></p>")
	{
		$doc_normal=0;
		$norm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select normal from radiology_normal where testid='$tst[2]' and doctor='0' "));	
	}
	
	//~ if($category_id==2)
	//~ {
		//~ $norm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select normal from radiology_normal where testid='$tst[2]' and doctor='0'"));	
		//~ if(!$norm[normal])
		//~ {
			//~ $norm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select normal from radiology_normal where testid='$tst[2]' "));	
		//~ }
	//~ }
	//~ if($category_id==3)
	//~ {
		//~ $norm=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select normal from cardiology_normal where testid='$tst[2]' "));	
	//~ }
	
	$res=$norm[normal];
	$tname=$tst[3];
}

?>
			<div>
				<input type="hidden" id="tstid" name="tstid" size="8" value="<?php echo $tst[2];?>" readonly="readonly"/>
				
             <table class="table table-bordered table-condensed">
				 <tr>
                     
					<td id="rad_testname" contenteditable="true">
						
						<?php echo $tname;?>
					</td>
					 <td id="name_td"><?php echo $name[name]." / ".$name[age]." ".$name[age_type]." / ".$name[sex]." / ".$opd_id;?></td>
					 <td>
						<!--<input type="button" id="button2" name="button2" value="View Patient Bill" class="btn btn-success" onclick="view_bill('<?php echo $uhid;?>',<?php echo $visit;?>)"/>-->
						<!--<select id="doctor" onchange="load_normal(this.value,<?php echo $tst[2];?>)">
									<option value="0">-All-</option>
							<?php
									$qry=mysqli_query($GLOBALS["___mysqli_ston"], "select * from lab_doctor where category='2'");	
									while($q=mysqli_fetch_array($qry))
									{
										if($q[id]==$did && $doc_normal>0){ $seld="selected='selected'";}else{ $seld="";}
										echo "<option value='$q[id]' $seld>$q[name]</option>";	
									}
							?>
							</select>-->
							
							<select id="findid" onchange="load_findings(this.value)">
								<option value="0">--Default(Select Finding)--</option>
								<?php
									$find=mysqli_query($link,"select id,name from radiology_normal_finding order by name");
									while($f=mysqli_fetch_array($find))
									{
										echo "<option value='$f[id]'>$f[name]</option>";
									}
								?>
							</select>
					 </td> 
					 <td>
						 <select id="doctor" onchange="load_normal(this.value,<?php echo $tst[2];?>)">
									<option value="0">-All-</option>
							<?php
									$qry=mysqli_query($GLOBALS["___mysqli_ston"], "select * from lab_doctor where category='2'");	
									while($q=mysqli_fetch_array($qry))
									{
										if($q[id]==$did && $doc_normal>0){ $seld="selected='selected'";}else{ $seld="";}
										echo "<option value='$q[id]' $seld>$q[name]</option>";	
									}
							?>
							</select>
							<select id="tst_form" onchange="load_normal(<?php echo $did;?>,this.value)">
								<option value="0">Other Normal Formats</option>
								<?php
								$tst_f=mysqli_query($link,"select * from testmaster where category_id='2' order by testname");
								while($tf=mysqli_fetch_array($tst_f))
								{
									echo "<option value='$tf[testid]'>$tf[testname]</option>";
								}
								?>
							</select>
<!--
						 <input type="text" id="film_no" placeholder="Add Film No" value="<?php if($obsrv[film_no]){ echo $obsrv[film_no];}else{ echo '';}?>"/> 
-->
					</td>                
					 					 
						  <td rowspan="4" style="text-align:center" id="rad_button" valign="bottom">
							
							
							<?php
					
								$but="Save";
								if($vdetail)
								{
									$but="Update";
								}
								
								if($did==0)
								{
									$but_dis="disabled";
								}
							
							?>  
							  
						   <input type="hidden" id="pid" value="<?php echo $uhid;?>"/>
						   <input type="hidden" id="vis" value="<?php echo $visit;?>"/>
						   
						   <!--<input type="button" id="button1" name="button1" value="<?php echo $but;?>" style="width:60px" class="btn btn-default" onclick="save_data('<?php echo $uhid;?>',<?php echo $visit;?>)"/>-->
						   <button id="button1" name="button1" style="width:60px" class="btn btn-success" onclick="save_data('<?php echo $uhid;?>','<?php echo $opd_id;?>','<?php echo $ipd_id;?>',<?php echo $batch_no;?>)" <?php echo $but_dis;?> ><?php echo $but;?> </button> <br/>
						   <button id="button2" name="button2" style="width:60px" class="btn btn-success" onclick="print_report(<?php echo $tst[2];?>,'<?php echo $uhid;?>','<?php echo $opd_id;?>','<?php echo $ipd_id;?>',<?php echo $batch_no;?>)">Print</button><br/>
						   <button type="button" id="button3" name="button3" class="btn btn-danger" style="width:60px" onclick="$('.modal').modal('hide');">Exit</button>  
						   
						</td>
                 </tr>
                 
                 <tr>
					<td colspan="4" width="92%"><textarea name="article-body" id="txtdetail" cols="50" rows="10"><?php echo $res;?></textarea></td>
                </tr>
                               
                
                
		
             
                
             </table>
             
        </div>
</div> 

<script>
	//$("select").select2({ theme: "classic" });
	//$(".select2-selection").css({"z-index": "109001"});
	//~ $('select').select2({
        //~ dropdownParent: $('#myModal')
    //~ });
</script>
