<?php
include("../../includes/connection.php");

// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}
$rupees_symbol="&#x20b9; ";
$type=$_POST['type'];
if($type==1111)
{
	$fdate=$_POST[fdate];
	$tdate=$_POST[tdate];
	$user=$_POST[user];
	$l=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$user'"));
	$level=$l['levelid'];
	
	if($level!="1")
	{
		echo "Select Doctor:";
		$doc_query="SELECT DISTINCT `consultantdoctorid` FROM `appointment_book` WHERE `user`='$user' AND `date` BETWEEN '$fdate' AND '$tdate'";
	//echo $doc_query;
	?> 
	<select id="doc_sel" class="span4" onchange="display_doc(this.value)">
		<option value="0">--ALL--</option>
		<?php
		$doc=mysqli_query($link,$doc_query);
		while($dc=mysqli_fetch_array($doc))
		{
			$doc_ar[]=$dc['consultantdoctorid'];
			$dname=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$dc[consultantdoctorid]'"));
			echo "<option value='$dc[consultantdoctorid]'>$dname[Name]</option>";
		}
		?>
	</select>
	<input type="button" id="summ" value="View Payment Summary" class="btn btn-info" onclick="payment_summary()" />
	<br/>
	
	<?php
	$n=1;
	$j=1;
	foreach($doc_ar as $dct)
	{
		?> <div id="doc_<?php echo $dct;?>" class="doctor_details"> <?php
			
				$dname=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$dct'"));
				echo "<h5>$n. $dname[Name]</h5>";
				//echo "select a.*, b.* from appointment_book a,consult_patient_payment_details b where a.consultantdoctorid='$dct' and a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.user='$user' and b.date between '$fdate' and '$tdate'";
				?> 
				<table class="table table-condensed table-bordered table-report"> 
					<tr><th></th><th>#</th><th>UHID</th><th>PIN</th><th>Name</th><th>Date</th><th>Consultation Fees</th></tr>
				<?php
				$i=1;
				$chk=0;
				$tot=0;
				$pay_done=0;
				
				$user_doc=mysqli_query($link,"select a.*, b.* from appointment_book a,consult_patient_payment_details b where a.consultantdoctorid='$dct' and a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.user='$user' and b.date between '$fdate' and '$tdate'");
				$num=mysqli_num_rows($user_doc);
				while($u_d=mysqli_fetch_array($user_doc))
				{
					$pinfo=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$u_d[patient_id]'"));
					//$bill=mysqli_fetch_array(mysqli_query($link,"SELECT `bill_no` FROM `invest_payment_detail` WHERE `patient_id`='$u_d[patient_id]' AND `opd_id`='$u_d[opd_id]'"));
					?>
					<tr>
						<td>
							<?php
							
							$chk_pay=mysqli_num_rows(mysqli_query($link,"select * from doctor_payment where consultantdoctorid='$dct' and patient_id='$u_d[patient_id]' and opd_id='$u_d[opd_id]'"));
							if($chk_pay>0)
							{
								?> <img src='../images/right.png' height='20px' width='20px'/> <?php
								$chk++;
							}
							else
							{
								?>
								<input type="checkbox" id="<?php echo $j;?>" class="checkbox_<?php echo $dct;?>" onclick="check_payment(this,<?php echo $dct;?>)"/>
					  <?php } ?>
						</td>
						<td><?php echo $i;?></td>
						<td><?php echo $pinfo['patient_id'];?></td>
						<td><?php echo $u_d['opd_id'];?></td>
						<td><?php echo $pinfo['name'];?></td>
						<td><?php echo convert_date($u_d['date']);?></td>
						<td>
							<?php echo $u_d['visit_fee'];?>
							
							<input type="hidden" id="pid_<?php echo $j;?>" value="<?php echo $u_d['patient_id'];?>"/>
							<input type="hidden" id="vis_<?php echo $j;?>" value="<?php echo $u_d['opd_id'];?>"/>
							<input type="hidden" id="doc_rate_<?php echo $j;?>" value="<?php echo $u_d['visit_fee'];?>"/>
						</td>
					</tr>
					<?php
					$tot=$tot+$u_d['visit_fee'];
					
					$paym=mysqli_fetch_array(mysqli_query($link,"select ifNull(amount,0) as tot from doctor_payment where patient_id='$u_d[patient_id]' and opd_id='$u_d[opd_id]' and user='$user'"));
					$pay_done=$pay_done+$paym['tot'];
					
					$i++;
					$j++;
				}
				?>
				
				<tr>
					<th colspan='5'>
						<input type='button' class='btn btn-info' id="sel_all_<?php echo $dct;?>" value='Select All' onclick="select_all(this.value,<?php echo $dct;?>)" <?php if($num==$chk){echo "disabled='disabled'";}?> />
						<input type='button' class='btn btn-info' value="Make Payment" id="make_payment_<?php echo $dct;?>" onclick="make_payment(<?php echo $dct;?>)" disabled />
					</th>
					<th style='text-align:right'>Total</th>
					<th><?php echo $tot;?></th>
				</tr>
				<tr>
					<th colspan="7" style="text-align:center">
						<?php
							//$pay_done=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as tot from doctor_payment where doc_id='$dct' and user='$user' and date between '$fdate' and '$tdate'"));
							$pay_due=$tot-$pay_done;
						?>
						<span style="width:200px;display:inline-block">Total Amount: <?php echo $tot;?></span>
						<span style="width:300px;display:inline-block">Total Payment Done: <?php echo $pay_done;?></span>
						<span style="width:200px;display:inline-block">Total Payment Due:  <?php echo $pay_due;?></span>
					</th>
				</tr>
				</table>
				<?php
		?> </div>
		<?php
		$n++;
		}
	}
	else
	{
		//$doc_query="SELECT DISTINCT `consultantdoctorid` FROM `appointment_book` WHERE `date` BETWEEN '$fdate' AND '$tdate'";
		$doc_query="SELECT DISTINCT `user` FROM `appointment_book` WHERE `date` BETWEEN '$fdate' AND '$tdate'";
		$doc=mysqli_query($link,$doc_query);
		//if(mysqli_num_rows($doc)>0)
		//{
			if($fdate==$tdate)
			{
				$dt="Date: ".convert_date($fdate)."<br/>";
			}
			else
			{
				$dt="From <i>".convert_date($fdate)."</i> to <i>".convert_date($tdate)."</i><br/>";
			}
			echo "<center><b>".$dt."</b></center>"."Select User :";
		?>
		<select id="usr_sel" onchange="display_user(this.value)" class="span4">
			<option value="0">--ALL--</option>
		<?php
		while($dc=mysqli_fetch_array($doc))
		{
			$usr_ar[]=$dc['user'];
			$dname=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$dc[user]'"));
			echo "<option value='$dc[user]'>$dname[name]</option>";
		}
		?>
		</select>
		<input type="button" id="summ" value="View Payment Summary" class="btn btn-info" onclick="payment_summary()"/><br/>
		<?php
		//}
		foreach($usr_ar as $usr)
		{
			?>
			<div id="usr_<?php echo $usr;?>" class="user_details">
			<?php
				$uname=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$usr'"));
				echo "<b>User : ".$uname['name']."</b><br/>";
			?>
			
			<table class="table table-condensed table-bordered" style="font-size:12px;">
			<tr>
				<th>#</th>
				<th>Doctor Name</th>
				<th>Total Amount</th>
				<th>Total Paid</th>
				<th>Total Due</th>
			</tr>
			 <?php
			$i=1;
			$all_amt=0;
			$all_paid=0;
			$all_due=0;
			$doc_query=mysqli_query($link,"SELECT DISTINCT `consultantdoctorid` FROM `appointment_book` WHERE `user`='$usr' AND `date` BETWEEN '$fdate' AND '$tdate'");
			while($dc=mysqli_fetch_array($doc_query))
			{
				//$dname=mysqli_fetch_array(mysqli_query($link,"select doctor_name from consult_doc where id='$dc[doc_id]'"));
				$dname=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$dc[consultantdoctorid]'"));
				//$tot_am=mysqli_fetch_array(mysqli_query($link,"select ifNull(sum(visit_fee),0) as tot from appointment_book where consultantdoctorid='$dc[consultantdoctorid]' and user='$usr' and date between '$fdate' and '$tdate'"));
				$tot_am=mysqli_fetch_array(mysqli_query($link,"select ifNull(sum(a.`visit_fee`),0) as tot from `consult_patient_payment_details` a, `appointment_book` b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`user`=b.`user` and b.`consultantdoctorid`='$dc[consultantdoctorid]' and b.`user`='$usr' and a.`date` between '$fdate' and '$tdate'"));
				//$tot_paid=mysqli_fetch_array(mysqli_query($link,"select ifNull(sum(amount),0) as tot from doctor_payment where consultantdoctorid='$dc[consultantdoctorid]' and user='$usr' and date between '$fdate' and '$tdate'"));
				$tot_paid=mysqli_fetch_array(mysqli_query($link,"SELECT ifNull(SUM(`amount`),0) as tot FROM `doctor_payment` WHERE `user`='$usr' AND `opd_id` IN(SELECT `opd_id` FROM `appointment_book` WHERE `consultantdoctorid`='$dc[consultantdoctorid]' AND `date` BETWEEN '$fdate' and '$tdate')"));
				$tot_due=$tot_am['tot']-$tot_paid[tot];
				$all_amt=$all_amt+$tot_am['tot'];
				$all_paid=$all_paid+$tot_paid['tot'];
				$all_due=$all_due+$tot_due;
				echo "<tr><td>$i</td><td>$dname[Name]</td><td>".$rupees_symbol.number_format($tot_am['tot'],2)."</td><td>".$rupees_symbol.number_format($tot_paid['tot'],2)."</td><td>".$rupees_symbol.number_format($tot_due,2)."</td></tr>";
				$i++;
			}
			?>
				<tr>
					<th colspan="2" style="text-align:right;">Total</th><th><?php echo $rupees_symbol.number_format($all_amt,2);?></th><th><?php echo $rupees_symbol.number_format($all_paid,2);?></th><th><?php echo $rupees_symbol.number_format($all_due,2);?></th>
				</tr>
			</table>
		</div>
		<?php
		}
	}
}

if($type==1)
{
	$fdate=$_POST['fdate'];
	$tdate=$_POST['tdate'];
	$doc=$_POST['doc'];
	$user=$_POST['user'];
	$usr=$user;
	$l=mysqli_fetch_array(mysqli_query($link,"SELECT `levelid` FROM `employee` WHERE `emp_id`='$user'"));
	$level=$l['levelid'];
	
	if($fdate==$tdate)
	{
		$dt="Date: ".convert_date($fdate)."<br/>";
	}
	else
	{
		$dt="From <i>".convert_date($fdate)."</i> to <i>".convert_date($tdate)."</i><br/>";
	}
	echo "<center><b>".$dt."</b></center>";
	
	?>
	<span style="float:right;">
		<input type="button" id="summ" value="View Payment Summary" class="btn btn-info" onclick="payment_summary()"/>
	</span>
		<table class="table table-condensed">
		<?php
		$i=1;
		$all_amt=0;
		$all_paid=0;
		$all_due=0;
		$doc_query="SELECT DISTINCT `consultantdoctorid` FROM `appointment_book` WHERE `appointment_date` BETWEEN '$fdate' AND '$tdate'";
		$doc=mysqli_query($link,$doc_query);
		while($dc=mysqli_fetch_array($doc))
		{
			$dname=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$dc[consultantdoctorid]'"));
			
			//$tot_am=mysqli_fetch_array(mysqli_query($link,"select ifNull(sum(a.`visit_fee`),0) as tot from `consult_patient_payment_details` a, `appointment_book` b where a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.`consultantdoctorid`='$usr' and a.`date` between '$fdate' and '$tdate'"));
			
			//$tot_paid=mysqli_fetch_array(mysqli_query($link,"SELECT ifNull(SUM(`amount`),0) as tot FROM `doctor_payment` WHERE `opd_id` IN(SELECT `opd_id` FROM `appointment_book` WHERE `consultantdoctorid`='$usr' AND `date` BETWEEN '$fdate' and '$tdate')"));
			
			//echo "<tr><td>$i</td><td>$dname[Name]</td><td>".$rupees_symbol.number_format($tot_am['tot'],2)."</td><td>".$rupees_symbol.number_format($tot_paid['tot'],2)."</td><td>".$rupees_symbol.number_format($tot_due,2)."</td></tr>";
			?>
			<tr style="background: linear-gradient(-90deg, #aaaaaa, #dddddd)">
				<th colspan="8"><?php echo str_replace(".",". ",$dname['Name']);?></th>
			</tr>
			<tr>
				<th></th>
				<th></th>
				<th>Patient Name</th>
				<th>UHID</th>
				<th>Bill No.</th>
				<th>Total Amount</th>
				<th>Total Paid</th>
				<th>Total Due</th>
			</tr>
		<?php
			$j=1;
			$tot=0;
			$chk=0;
			$due=0;
			$tot_pay=0;
			$tot_due=0;
			
			//$qq=mysqli_query($link,"select a.*, b.* from appointment_book a,consult_patient_payment_details b where a.consultantdoctorid='$dc[consultantdoctorid]' and a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.date between '$fdate' and '$tdate'");
			
			$qq=mysqli_query($link,"select a.consultantdoctorid,b.`patient_id`,b.`opd_id`,b.`visit_fee`,b.`regd_fee` from appointment_book a,consult_patient_payment_details b where a.consultantdoctorid='$dc[consultantdoctorid]' and a.patient_id=b.patient_id and a.opd_id=b.opd_id and b.date between '$fdate' and '$tdate'");
			$num=mysqli_num_rows($qq);
			$c=0;
			while($rr=mysqli_fetch_array($qq))
			{
				$chk_pay=mysqli_num_rows(mysqli_query($link,"select * from doctor_payment where consultantdoctorid='$dc[consultantdoctorid]' and patient_id='$rr[patient_id]' and opd_id='$rr[opd_id]'"));
				
				$pinfo=mysqli_fetch_array(mysqli_query($link,"select `name` from patient_info where patient_id='$rr[patient_id]'"));
				
				$tot_paid=mysqli_fetch_array(mysqli_query($link,"SELECT ifNull(SUM(`amount`),0) as tot FROM `doctor_payment` WHERE `user`='$usr' AND `opd_id` IN(SELECT `opd_id` FROM `appointment_book` WHERE `consultantdoctorid`='$dc[consultantdoctorid]' AND `date` BETWEEN '$fdate' and '$tdate')"));
				
				$tot_paid=mysqli_fetch_array(mysqli_query($link,"SELECT `amount` FROM `doctor_payment` WHERE `consultantdoctorid`='$dc[consultantdoctorid]' AND patient_id='$rr[patient_id]' and opd_id='$rr[opd_id]'"));
				
				if($tot_paid)
				{
					$paid=$tot_paid['amount'];
				}
				else
				{
					$paid=0;
				}
				$v_fee=($rr['visit_fee']-$rr['ref_amt']);
				$tot+=$v_fee;
				$tot_pay+=$paid;
				$due=$v_fee-$paid;
				$tot_due+=$due;
				
				$all_amt+=$v_fee;
				$all_paid+=$paid;
				$all_due+=$due;
			?>
			<tr class="doc_tr<?php echo $dc['consultantdoctorid'];?>">
				<td>
					<?php
					if($chk_pay>0)
					{
					?>
						<img src='../images/right.png' height='20px' width='20px'/>
					<?php
						$chk++;
						$tt="Select All";
					}
					else
					{
						$tt="Select All";
						$disb="";
					?>
						<input type="checkbox" id="<?php echo $c;?>" class="doc_pay<?php echo $dc['consultantdoctorid'];?>" onclick="select_one('<?php echo $dc['consultantdoctorid'];?>')" <?php echo $disb;?> />
					<?php
					}
					?>
					<input type="hidden" id="pid_<?php echo $dc['consultantdoctorid'];?>" value="<?php echo $rr['patient_id'];?>"/>
					<input type="hidden" id="vis_<?php echo $dc['consultantdoctorid'];?>" value="<?php echo $rr['opd_id'];?>"/>
					<input type="hidden" id="doc_rate_<?php echo $dc['consultantdoctorid'];?>" value="<?php echo $v_fee;?>"/>
				</td>
				<td><?php echo $j;?></td>
				<td><?php echo $pinfo['name'];?></td>
				<td><?php echo $rr['patient_id'];?></td>
				<td><?php echo $rr['opd_id'];?></td>
				<td><?php echo number_format($v_fee,2);?></td>
				<td><?php echo number_format($paid,2);?></td>
				<td><?php echo number_format($due,2);?></td>
			</tr>
			<?php
			$c++;
			$j++;
			}
			?>
			<tr>
				<th colspan='4'>
					<input type='button' class='btn btn-info' id="sel_all<?php echo $dc['consultantdoctorid'];?>" value='<?php echo $tt;?>' onclick="select_all(this.value,<?php echo $dc['consultantdoctorid'];?>)" <?php if($num==$chk){echo "disabled='disabled'";}?> <?php echo $disb;?> />
					<input type='button' class='btn btn-info' value="Make Payment" id="make_payment<?php echo $dc['consultantdoctorid'];?>" onclick="make_payment(<?php echo $dc['consultantdoctorid'];?>)" disabled />
				</th>
				<th style='text-align:right'>Total</th>
				<th><?php echo number_format($tot,2);?></th>
				<th><?php echo number_format($tot_pay,2);?></th>
				<th><?php echo number_format($tot_due,2);?></th>
			</tr>
			<?php
			$i++;
		}
		?>
		<tr>
			<th colspan="5" style="text-align:right;">Total</th><th><?php echo $rupees_symbol.number_format($all_amt,2);?></th><th><?php echo $rupees_symbol.number_format($all_paid,2);?></th><th><?php echo $rupees_symbol.number_format($all_due,2);?></th>
		</tr>
		</table>
	<?php
}

else if($type==2)
{
	$pay_st=$_POST['pay_st'];
	$user=$_POST['user'];
	$dct=$_POST['dct'];
	
	$date=date("Y-m-d");
	$time=date('H:i:s');
	
	$group_id=101;
	$charge_id=1;
	
	$pay=explode("govinda",$pay_st);
	foreach($pay as $py)
	{
		if($py)
		{
			$info=explode("#$#",$py);
			
			mysqli_query($link,"delete from doctor_payment where patient_id='$info[0]' and opd_id='$info[1]' and consultantdoctorid='$dct'");
			//echo "delete from doctor_payment where patient_id='$info[0]' and opd_id='$info[1]' and consultantdoctorid='$dct'";
			
			mysqli_query($link,"insert into doctor_payment(patient_id,opd_id,consultantdoctorid,group_id,charge_id,amount,user,time,date) values('$info[0]','$info[1]','$dct','$group_id','$charge_id','$info[2]','$user','$time','$date')");
			
			//echo "insert into doctor_payment(patient_id,opd_id,consultantdoctorid,amount,user,time,date) values('$info[0]','$info[1]','$dct','$info[2]','$user','$time','$date')";
		}
	}
	
}		
?>
