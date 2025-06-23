<?php
include("../../includes/connection.php");

$type=$_POST["type"];

if($type==2)
{
	?>
		<input type="hidden" id="text_div_level" value="2"/>
		<table class="table table-bordered">
			<tr>
				<th>Ward</th><th>Total Rooms</th><th>Total Beds</th><th>Beds Available</th><th>Beds Occupied</th><th>Unavailable/Closed Beds</th><th>Temporary Blocked Beds</th>
			</tr>
	
		<?php
		$ward=mysqli_query($link,"select distinct ward_id,name from ward_master order by name asc");
		while($w=mysqli_fetch_array($ward))
		{
			$tot_room=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from room_master where ward_id='$w[ward_id]'"));
			$tot_bed=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]'"));
			$tot_avail=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]' and bed_id not in(select bed_id from ipd_pat_bed_details)"));
			$tot_occ=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_pat_bed_details where bed_id in(select bed_id from bed_master where ward_id='$w[ward_id]')"));
			$tot_cls=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$w[ward_id]' and status='1'"));
			$tot_temp=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_bed_details_temp where bed_id in(select bed_id from bed_master where ward_id='$w[ward_id]')"));
			
			$avail=$tot_avail["tot"]-$tot_temp["tot"]-$tot_cls["tot"];
			
			?>
				<tr onclick="load_room('<?php echo $w["ward_id"];?>','2')">
					<td><?php echo $w["name"];?></td>
					<td><?php echo $tot_room["tot"];?></td>
					<td><?php echo $tot_bed["tot"];?></td>
					<td><?php echo $avail;?></td>
					<td><?php echo $tot_occ["tot"];?></td>
					<td><?php echo $tot_cls["tot"];?></td>
					<td><?php echo $tot_temp["tot"];?></td>
				</tr>
			
			<?php
		}
		?>
		</table>
	<?php
}
else if($type==3)
{
	$ward=$_POST["ward"];
	$lev=$_POST["lev"];
	
	
	$ward_det=mysqli_fetch_array(mysqli_query($link,"select name from ward_master where ward_id='$ward'"));
	?>
		<table class="table table-bordered">
			<tr>
				<th colspan="6">Ward: <?php echo $ward_det["name"];?></th>
			</tr>
			<tr>
				<th>Rooms</th><th>Total Beds</th><th>Beds Available</th><th>Beds Occupied</th><th>Unavailable/Closed Beds</th><th>Temporary Blocked Beds</th>
			</tr>
		
		<?php
		
		$room=mysqli_query($link,"select * from room_master where ward_id='$ward'");
		while($rm=mysqli_fetch_array($room))
		{
			
			$tot_bed=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$ward' and room_id='$rm[room_id]'"));
			$tot_avail=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$ward' and room_id='$rm[room_id]' and bed_id not in(select bed_id from ipd_pat_bed_details)"));
			$tot_occ=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_pat_bed_details where bed_id in(select bed_id from bed_master where ward_id='$ward' and room_id='$rm[room_id]')"));
			$tot_cls=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where ward_id='$ward' and room_id='$rm[room_id]' and status='1'"));
			$tot_temp=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_bed_details_temp where bed_id in(select bed_id from bed_master where ward_id='$ward' and room_id='$rm[room_id]')"));
			
			$avail=$tot_avail["tot"]-$tot_temp["tot"]-$tot_cls["tot"];
			
		
		?>
				<tr onclick="load_room_details('<?php echo $ward;?>','<?php echo $rm["room_id"];?>')">
					<td><?php echo $rm["room_no"];?></td>
					<td><?php echo $tot_bed["tot"];?></td>
					<td><?php echo $avail;?></td>
					<td><?php echo $tot_occ["tot"];?></td>
					<td><?php echo $tot_cls["tot"];?></td>
					<td><?php echo $tot_temp["tot"];?></td>
				</tr>
		<?php
		}
		?>		
		</table>
		<?php
}
else if($type==4)
{
	$lev=$_POST["lev"]+1;
	$ward=$_POST["ward"];
	$room=$_POST["room"];
	
	$ward_det=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$ward'"));
	$room_det=mysqli_fetch_array(mysqli_query($link,"select * from room_master where room_id='$room'"));
	
	echo "<h3><u>$ward_det[name]</u></h3>";
	echo "<h4><u>Room No: $room_det[room_no]</u></h4>";
	
	?>
		<hr/>
		<input type="hidden" id="ward_id" value="<?php echo $ward;?>"/>
		<input type="hidden" id="room_id" value="<?php echo $room;?>"/>
		<div class="row">
	<?php
	$j=1;
	$i=1;
	$bed=mysqli_query($link,"select * from bed_master where ward_id='$ward' and room_id='$room'");
	
	while($b=mysqli_fetch_array($bed))
	{
		$chk_ipd=mysqli_fetch_array(mysqli_query($link,"select count(ward_id) as tot from ipd_pat_bed_details where ward_id='$ward' and bed_id='$b[bed_id]'"));
		$chk_temp=mysqli_fetch_array(mysqli_query($link,"select count(ward_id) as tot from ipd_bed_details_temp where ward_id='$ward' and bed_id='$b[bed_id]'"));
		
		$onclick="";
		$cls="span3 bed_box btn text-left";
		if($chk_ipd["tot"]>0)
		{
			$cls.=" occupy";
		}
		else if($chk_temp["tot"]>0)
		{
			$cls.=" other_selected";
		}
		else if($b["status"]==1)
		{
			$cls.=" blocked";
			$onclick="change_status($j)";
		}
		else
		{
			$cls.=" available";
			$onclick="change_status($j)";
		}
		?>
		
		<div class="<?php echo $cls;?>" onclick="<?php echo $onclick;?>">
			<div style="text-align:center;border-bottom:1px solid">Bed No: <?php echo $b["bed_no"];?></div>
			
			
			
			<div style="">
				
				<input type="hidden" value="<?php echo $b["bed_id"];?>" id="bed_<?php echo $j;?>"/>
				<?php
				if($chk_ipd["tot"]>0)
				{
					$pat=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where ward_id='$ward' and bed_id='$b[bed_id]'"));
					$p_info=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$pat[patient_id]'"));
					
					
					echo "<span class='text_big'>$p_info[name]</span> <br/>";
					echo "<span class='text_med'>Sex: $p_info[sex] &nbsp;&nbsp; Age: $p_info[age] $p_info[age_type] </span>";
					
					echo "<br/>";
					
					$last=date("Y-m-d");
					$diff=abs(strtotime($pat['date'])-strtotime($last));
					$diff=$diff/60/60/24;
					
					echo "<span class='text_med'>Occupied For: $diff Days</span> <br/>";
					
					$doc=mysqli_fetch_array(mysqli_query($link,"SELECT Name FROM consultant_doctor_master where consultantdoctorid in(select attend_doc from ipd_pat_doc_details where patient_id='$pat[patient_id]' and ipd_id='$pat[ipd_id]')"));
					
					echo "<span class='text_small'>Consulting Doctor:</span> <br/>";
					echo "<span class='text_small'>$doc[Name]</span> <br/>";
				}
				else if($b["status"]==1)
				{
					echo "<span class='text_med'>Status: <br/>Unavailable/Blocked</span> <br/>";
					
					echo "<span class='text_med'>Due to: $b[reason] <br/>";
					echo "<span class='text_med'> $b[remarks] <br/>";
				}
				else if($chk_temp["tot"]>0)
				{
					echo "<span class='text_med'>Status: Bed Selected</span> <br/><br/>";
				}	
				else
				{
					echo "<span class='text_med'>Status: Available</span> <br/><br/>";
					
				}
				?>
				
			</div>
		</div>
		
		<?php
		if($i==5)
		{
			 echo "<br/>";
			 $i=1;
		}
		else
		{
			$i++;
		}
		$j++;
	}
	
	?>
	</div>
	<?php
	echo "<br/><br/><br/><br/>";
}
else if($type==5)
{
	$bed=$_POST["bed"];
	
	$b_det=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed'"));
	
	if($b_det[status]==1)
	{
		//$sty="display:inline";
	}
	else
	{
		$sty="display:none";
	}
	?>
	<input type="hidden" value="<?php echo $bed;?>" id="bd_st"/>
	<table class="table table-bordered">
		<tr>
			<td>Status</td>
			<td>
				<select id="status" onchange="load_blck_reas(this.value)">
					<option value="0">Available</option>
					<option value="1" <?php if($b_det["status"]==1) { echo "Selected='selected'";}?> >Blocked</option>
				</select>
			</td>
		</tr>
		<tr style="<?php echo $sty;?>" id="blck_reason">
			<td>Due To</td>
			<td><input type="text" id="reason" value="<?php echo $b_det["reason"];?>"</td>
		</tr>
		<tr style="<?php echo $sty;?>" id="add_info">
			<td>Additional Info:</td>
			<td><input type="text" id="info" value="<?php echo $b_det["remarks"];?>"/></td>
		</tr>
	
	</table>
	<?php
}
else if($type==6)
{
	$bed=$_POST["bed"];
	$stat=$_POST["stat"];
	$reas=$_POST["reas"];
	$ad_info=$_POST["ad_info"];
	
	if($stat==0)
	{
		mysqli_query($link,"update bed_master set status='0',reason='',remarks='' where bed_id='$bed'");
	}
	else if($stat==1)
	{
		mysqli_query($link,"update bed_master set status='1',reason='$reas',remarks='$ad_info' where bed_id='$bed'");
	}
}
?>
