<?php
include("../../includes/connection.php");
$date=date("Y-m-d");
$time=date("H:i:s");

$type=$_POST["type"];

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

if($type=="all_setup")
{
	$grph_val=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_graph_set_up` ORDER BY `slno` DESC"));
	?>
	<div style="padding:8px;display:none;" id="gsetup">
	<table class="table table-condensed table-bordered" style="background:#FFFFFF;box-shadow:0px 2px 7px 4px #999999;">
		<tr>
			<th colspan="4" style="text-align:center;">Graph Settings</th>
		</tr>
		<tr>
			<th width="20%">Levels</th>
			<td>
				<select id="levels" class="span1">
					<option value="4" <?php if($grph_val['levels']=="4"){echo "selected='selected'";}?>>4</option>
					<option value="5" <?php if($grph_val['levels']=="5"){echo "selected='selected'";}?>>5</option>
					<option value="6" <?php if($grph_val['levels']=="6"){echo "selected='selected'";}?>>6</option>
				</select>
			</td>
			<th width="20%">Maximum Value</th>
			<td>
				<input type="text" id="max_val" value="<?php echo $grph_val['max_value'];?>" placeholder="Maximum Sale Value" />
			</td>
		</tr>
		<tr>
			<th>
				Colors<br/>
			</th>
			<td width="20%">
				<label><input type="radio" name="colr" class="colr_type" value="1" onchange="load_colors()" <?php if($grph_val['color_type']=="1"){echo "checked='checked'";}?> /> Single</label>
				<label><input type="radio" name="colr" class="colr_type" value="2" onchange="load_colors()" <?php if($grph_val['color_type']=="2"){echo "checked='checked'";}?> /> Multiple</label>
			</td>
			<td colspan="3" id="load_colors"></td>
		</tr>
		<tr>
			<td colspan="4" style="text-align:center;">
				<button type="button" class="btn btn-success" id="btn_graph" onclick="save_graph_data()">Save</button>
				<button type="button" class="btn btn-danger" id="" onclick="close_graph_data()">Close</button>
			</td>
		</tr>
	</table>
	</div>
	<?php
}

if($type=="load_colors")
{
	$colr_type=$_POST["colr_type"];
	$grph_val=mysqli_fetch_assoc(mysqli_query($link,"SELECT `colors` FROM `ph_graph_set_up` ORDER BY `slno` DESC"));
	$all_color=array("#185405","#185405","#185405","#185405","#185405","#185405","#185405");
	if($grph_val)
	{
		$all_color=array();
		$cl=explode("@@",$grph_val['colors']);
		foreach($cl as $c)
		{
			array_push($all_color,$c);
		}
	}
	if($colr_type=="1")
	{
	?>
		<input type="color" class="colors span1" value="<?php echo $all_color[0];?>" />
	<?php
	}
	if($colr_type=="2")
	{
		for($i=0; $i<=6; $i++)
		{
	?>		
		<input type="color" class="colors span1" value="<?php echo $all_color[$i];?>" />
	<?php
		}
	}
	?>
	<style>
		input[type="color"]
		{
			height:30px;
		}
	</style>
	<?php
}

if($type=="save_graph_data")
{
	$lvs=$_POST["lvs"];
	$max_val=$_POST["max_val"];
	$colr_type=$_POST["colr_type"];
	$all_clr=$_POST["all_clr"];
	$user=$_POST["user"];
	if($colr_type=="1")
	{
		for($j=0; $j<7; $j++)
		{
			$all_clr.=$all_clr;
		}
	}
	
	$q="INSERT INTO `ph_graph_set_up`(`levels`, `max_value`, `color_type`, `colors`, `date`, `time`, `user`) VALUES ('$lvs','$max_val','$colr_type','$all_clr','$date','$time','$user')";
	//echo $q;
	if(mysqli_query($link,$q))
	{
		echo "Saved";
	}
	else
	{
		echo "Error";
	}
}

if($type=="load_prev_data")
{
	$grph_val=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `ph_graph_set_up` ORDER BY `slno` DESC"));
	if($grph_val)
	{
		$max_val=$grph_val['max_value'];
	}
	else
	{
		$max_val=100000;
	}
	$p_date=date("Y-m-d", strtotime('-7 days'));
	
	//echo "uytyut";
	$dt1=$date;
	$dt2=$p_date;
	
	$dt_diff=abs(strtotime($dt1)-strtotime($dt2));
	$num=$dt_diff/(60*60*24);
	$vdate=$p_date;
	$tot="";
	for($i=0; $i<=$num; $i++)
	{
		$ddate=strtotime('+1 days', strtotime($vdate));
		$ndt=date("Y-m-d",$ddate);
		$vdate=$ndt;
		$amt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`total_amt`),0) AS max_sell FROM `ph_sell_master` WHERE `entry_date`='$vdate'"));
		$rs=$amt['max_sell'];
		$per=(($amt['max_sell']/$max_val)*100);
		$per=number_format($per,2);
		//$per=$per/2;
		$vdate=convert_date($vdate);
		$tot.="&#x20b9; ".$rs."@".$per."%@".$vdate."#@#";
	}
	echo $tot;
	//$q=mysqli_query($link,"SELECT DISTINCT `entry_date` FROM `ph_sell_master` WHERE `entry_date` BETWEEN '$p_date' AND '$date'");
	//~ $tot="";
	//~ while($r=mysqli_fetch_array($q))
	//~ {
		//~ $amt=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`total_amt`),0) AS max_sell FROM `ph_sell_master` WHERE `entry_date`='$r[entry_date]'"));
		//~ $tot.=$amt['max_sell']."@@";
	//~ }
	//~ echo $tot;
}
?>
