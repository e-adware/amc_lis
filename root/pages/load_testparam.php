<?php
include("../../includes/connection.php");

$type=$_POST["type"];

if($type=="load_param")
{
	$testid=$_POST["testid"];
	
	$str="SELECT `ID`,`Name`,`ResultType` FROM `Parameter_old` WHERE `Name`!='' ORDER BY `Name` ASC";
	
	if($testid>0)
	{
		$str="SELECT a.ID,a.Name,a.ResultType FROM `Parameter_old` a, `Testparameter` b WHERE a.ID=b.ParamaterId AND b.TestId='$testid' AND a.`Name`!='' ORDER BY b.`sequence` ASC";
	}
	
	$qry=mysqli_query($link, $str);
?>
	<div>
		<table class="table table-bordered table-condensed" id="tblData">
			<thead class="table_header_fix">
				<tr>
					<th>ID</th>
					<th>Parameter Name</th>
					<th>Interface</th>
					<th>Add</th>
				</tr>
			</thead>
<?php
		while($param_info=mysqli_fetch_assoc($qry))
		{
			$param_type=mysqli_fetch_assoc(mysqli_query($link, "SELECT `ResultType_name` FROM `ResultType` WHERE `ResultTypeId`='$param_info[ResultType]'"));
?>
			<tr>
				<td ondblclick="add_parameter('<?php echo $testid; ?>','<?php echo $param_info["ID"]; ?>')" style="cursor:pointer;" title="Duoble Click To Add"><?php echo $param_info["ID"]; ?></td>
				<td ondblclick="add_parameter('<?php echo $testid; ?>','<?php echo $param_info["ID"]; ?>')" style="cursor:pointer;" title="Duoble Click To Add"><?php echo $param_info["Name"]; ?></td>
				<td ondblclick="add_parameter('<?php echo $testid; ?>','<?php echo $param_info["ID"]; ?>')" style="cursor:pointer;" title="Duoble Click To Add"><?php echo $param_type["ResultType_name"]; ?></td>
				<td>
					<input type="hidden" class="testParamCls" value="<?php echo $testid."P".$param_info["ID"]; ?>">
					<button class="btn btn-default btn-mini" id="add_btn<?php echo $testid."P".$param_info["ID"]; ?>" onclick="add_parameter('<?php echo $testid; ?>','<?php echo $param_info["ID"]; ?>')"> <i class="icon-step-forward"></i></button>
				</td>
			</tr>
<?php
		}
?>
			<tr>
				<td colspan="4">
					<button class="btn btn-new" onCLick="add_all_param('<?php echo $testid; ?>')"><i class="icon-forward"></i> Add All Params</button>
				</td>
			</tr>
		</table>
	</div>
<?php
}
if($type=="testParam")
{
	$testid=$_POST["testid"];
	
?>
	<div style="height:420px;overflow:auto;overflow-x:hidden">
		<table class="table table-bordered table-condensed" id="TestParameterTable">
			<thead class="table_header_fix">
				<tr>
					<th>ID</th>
					<th>Parameter Name</th>
					<th style="width:180px;">Sample|Vaccu</th>
					<th style="width:150px;">Interface</th>
					<th style="width:100px;">Mandatory</th>
					<th style="width:90px;">Don't Print</th>
					<th style="width:60px;">Sequence</th>
					<th style="width:40px;"></th>
				</tr>
			</thead>
<?php
		$par_inc = 1;
		$tr_counter = 1;
		$qry=mysqli_query($link, "SELECT `ParamaterId`, `sequence`, `sample`, `vaccu`, `status` FROM `Testparameter` WHERE `TestId`='$testid' ORDER BY `sequence` ASC");
		while($data=mysqli_fetch_assoc($qry))
		{
			$param_id=$data["ParamaterId"];
			
			$param_info=mysqli_fetch_assoc(mysqli_query($link, "SELECT `ResultType`,`Name` FROM `Parameter_old` WHERE `ID`='$param_id'"));
			$param_type=mysqli_fetch_assoc(mysqli_query($link, "SELECT `ResultType_name` FROM `ResultType` WHERE `ResultTypeId`='$param_info[ResultType]'"));
			
			$cls = 0;
			if($param_info["ResultType"]==0)
			{
				$cls = 4;
			}
			if($param_info["ResultType"]==5)
			{
				$cls = 4;
				$sub_head_spaces="";
			}
?>
			<tr id="TestParameter<?php echo $param_id; ?>">
				<td>
					<?php echo $param_id; ?>
					<input class="form-control each_row" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
					<input type="hidden" class="form-control testid" id="selParamId<?php echo $tr_counter; ?>" value="<?php echo $param_id; ?>">
				</td>
				<td colspan="<?php echo $cls; ?>"><?php echo $sub_head_spaces.$param_info["Name"]; ?></td>
	<?php
			if ($cls == 0)
			{
				$mand_chk = "";
				$chk_mand = mysqli_fetch_array(mysqli_query($link, "select count(*) as tot from test_param_mandatory where testid='$testid' and paramid='$param_id'"));
				if ($chk_mand["tot"] > 0) {
					$mand_chk = "Checked";
				}
	?>
				<td>
					<select id="samp_<?php echo $param_id; ?>" class="samp" onchange="load_sample_vaccu('<?php echo $testid; ?>','<?php echo $param_id; ?>',this.value)" style="width:180px;">
						<option value="0">Select Sample</option>
			<?php
				$sampleQry=mysqli_query($link, "SELECT `ID`, `Name` FROM `Sample` WHERE `Name`!='' ORDER BY `Name` ASC");
				while($sampleData=mysqli_fetch_assoc($sampleQry))
				{
					if($sampleData["ID"]==$data["sample"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$sampleData[ID]' $sel>$sampleData[Name]</option>";
				}
			?>
					</select>
					<select id="vac_<?php echo $param_id; ?>" class="vacc" style="width:180px;">
						<option value="0">Select Vaccu</option>
			<?php
				$vaccuStr="SELECT a.id,a.type FROM `vaccu_master` a, `sample_vaccu` b WHERE a.id=b.vacc_id AND b.samp_id='$data[sample]' ORDER BY a.type ASC";
				$vaccuQry=mysqli_query($link, $vaccuStr);
				while($vaccuData=mysqli_fetch_assoc($vaccuQry))
				{
					if($vaccuData["id"]==$data["vaccu"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$vaccuData[id]' $sel>$vaccuData[type]</option>";
				}
			?>
					</select>
				</td>
				<td><?php echo $param_type["ResultType_name"]; ?></td>
				<td>
					<label style="padding: 15px 5px;">
						<input type="checkbox" id="paramMandatory<?php echo $param_id; ?>" <?php echo $mand_chk; ?>>
					</label>
				</td>
	<?php
			}
	?>
				<td>
					<label style="padding: 15px 5px;">
						<input type="checkbox" id="paramDontPrint<?php echo $param_id; ?>" class="dont_print_cls" id="dont_print_<?php echo $param_id; ?>" <?php if ($data['status'] == 1) { echo "checked"; } ?>>
					</label>
				</td>
				<td>
					<input type="text" class="sequence sequence<?php echo $tr_counter; ?>" id="paramSequence<?php echo $param_id; ?>" onkeyup="sequence_up('<?php echo $tr_counter; ?>',event)" value="<?php echo $data['sequence']; ?>" style="width:50px">
				</td>
				<td>
					<button class="btn btn-delete" onclick="$(this).closest('tr').remove();"><i class="icon-trash"></i></button>
				</td>
			</tr>
<?php
			$par_inc++;
			$tr_counter++;
			
			if($param_info["ResultType"]==0)
			{
				$sub_head_spaces=" &nbsp;&nbsp;&nbsp;&nbsp; ";
			}
		}
?>
			<tr id="testPramFooter">
				<td colspan="8"></td>
			</tr>
			<tr>
				<td colspan="4" style="text-align:center;">
					<input type="hidden" class="form-control" id="tr_counter" value="<?php echo $tr_counter; ?>">
					<button class="btn btn-save" onclick="saveMapParameter('<?php echo $testid; ?>')"><i class="icon-save"></i> Save</button>
				</td>
				<td colspan="4"></td>
			</tr>
		</table>
	</div>
<?php
}

if($type=="load_sample_vaccu")
{
	$testid		= $_POST["testid"];
	$param_id	= $_POST["param_id"];
	$samp_id	= $_POST["samp_id"];
	
	echo '<option value="0">Select Vaccu</option>';
	
	$vaccuStr="SELECT a.id,a.type FROM `vaccu_master` a, `sample_vaccu` b WHERE a.id=b.vacc_id AND b.samp_id='$samp_id' ORDER BY a.type ASC";
	$vaccuQry=mysqli_query($link, $vaccuStr);
	while($vaccuData=mysqli_fetch_assoc($vaccuQry))
	{
		echo "<option value='$vaccuData[id]'>$vaccuData[type]</option>";
	}
}

if($type=="add_parameter")
{
	$testid		= $_POST["testid"];
	$param_id	= $_POST["param_id"];
	$tr_counter	= $_POST["tr_counter"];
	
	$param_info=mysqli_fetch_assoc(mysqli_query($link, "SELECT `ResultType`,`Name`,`sample`, `vaccu` FROM `Parameter_old` WHERE `ID`='$param_id'"));
	$param_type=mysqli_fetch_assoc(mysqli_query($link, "SELECT `ResultType_name` FROM `ResultType` WHERE `ResultTypeId`='$param_info[ResultType]'"));
	
	$cls = 0;
	if($param_info["ResultType"]==0)
	{
		$cls = 4;
		
		$sub_head_spaces=" &nbsp;&nbsp;&nbsp;&nbsp; ";
	}
	if($param_info["ResultType"]==5)
	{
		$cls = 4;
		$sub_head_spaces="";
	}
?>
	<tr id="TestParameter<?php echo $param_id; ?>">
		<td>
			<?php echo $param_id; ?>
			<input class="form-control each_row" type="hidden" name="each_row<?php echo $tr_counter; ?>" id="each_row<?php echo $tr_counter; ?>" value="<?php echo $tr_counter; ?>">
			<input type="hidden" class="form-control testid" id="selParamId<?php echo $tr_counter; ?>" value="<?php echo $param_id; ?>">
		</td>
		<td colspan="<?php echo $cls; ?>"><?php echo $sub_head_spaces.$param_info["Name"]; ?></td>
<?php
	if ($cls == 0)
	{
		$mand_chk = "";
		$chk_mand = mysqli_fetch_array(mysqli_query($link, "select count(*) as tot from test_param_mandatory where testid='$testid' and paramid='$param_id'"));
		if ($chk_mand["tot"] > 0) {
			$mand_chk = "Checked";
		}
?>
		<td>
			<select id="samp_<?php echo $param_id; ?>" class="samp" onchange="load_sample_vaccu('<?php echo $testid; ?>','<?php echo $param_id; ?>',this.value)" style="width:180px;">
				<option value="0">Select Sample</option>
	<?php
		$sampleQry=mysqli_query($link, "SELECT `ID`, `Name` FROM `Sample` WHERE `Name`!='' ORDER BY `Name` ASC");
		while($sampleData=mysqli_fetch_assoc($sampleQry))
		{
			if($sampleData["ID"]==$param_info["sample"]){ $sel="selected"; }else{ $sel=""; }
			echo "<option value='$sampleData[ID]' $sel>$sampleData[Name]</option>";
		}
	?>
			</select>
			<select id="vac_<?php echo $param_id; ?>" class="vacc" style="width:180px;">
				<option value="0">Select Vaccu</option>
	<?php
		$vaccuStr="SELECT a.id,a.type FROM `vaccu_master` a, `sample_vaccu` b WHERE a.id=b.vacc_id AND b.samp_id='$param_info[sample]' ORDER BY a.type ASC";
		$vaccuQry=mysqli_query($link, $vaccuStr);
		while($vaccuData=mysqli_fetch_assoc($vaccuQry))
		{
			if($vaccuData["id"]==$param_info["vaccu"]){ $sel="selected"; }else{ $sel=""; }
			echo "<option value='$vaccuData[id]' $sel>$vaccuData[type]</option>";
		}
	?>
			</select>
		</td>
		<td><?php echo $param_type["ResultType_name"]; ?></td>
		<td>
			<label style="padding: 15px 5px;">
				<input type="checkbox" id="paramMandatory<?php echo $param_id; ?>" <?php echo $mand_chk; ?>>
			</label>
		</td>
<?php
	}
?>
		<td>
			<label style="padding: 15px 5px;">
				<input type="checkbox" id="paramDontPrint<?php echo $param_id; ?>" class="dont_print_cls" id="dont_print_<?php echo $param_id; ?>" <?php if ($data['status'] == 1) { echo "checked"; } ?>>
			</label>
		</td>
		<td>
			<input type="text" class="sequence sequence<?php echo $tr_counter; ?>" id="paramSequence<?php echo $param_id; ?>" onkeyup="sequence_up('<?php echo $tr_counter; ?>',event)" value="<?php echo $tr_counter; ?>" style="width:50px">
		</td>
		<td>
			<button class="btn btn-delete" onclick="$(this).closest('tr').remove();"><i class="icon-trash"></i></button>
		</td>
	</tr>
<?php
}

if($type=="saveMapParameter")
{
	$testid		= $_POST["testid"];
	$save_data	= $_POST["save_data"];
	
	$save_data = json_decode($save_data, true);
	
	// Delete Old Mapping
	mysqli_query($link, "DELETE FROM `Testparameter` WHERE `TestId`='$testid'");
	mysqli_query($link, "DELETE FROM `test_param_mandatory` WHERE `testid`='$testid'");
	
	$response["error"] = 0;
	
	if(sizeof($save_data) > 0)
	{
		foreach($save_data as $param_data)
		{
			if($param_data)
			{
				$param_id	 = $param_data["param_id"];
				$sample_id	 = $param_data["sample_id"];
				$vaccu_id	 = $param_data["vaccu_id"];
				$mandatory	 = $param_data["mandatory"];
				$status		 = $param_data["status"];
				$sequence	 = $param_data["sequence"];
				
				if(!$sample_id || $sample_id==""){ $sample_id=0; }
				if(!$vaccu_id || $vaccu_id==""){ $vaccu_id=0; }
				if(!$sequence || $sequence==""){ $sequence=0; }
				if(!$status || $status==""){ $status=0; }
				if(!$mandatory || $mandatory==""){ $mandatory=0; }
				
				if(mysqli_query($link, "INSERT INTO `Testparameter`(`TestId`, `ParamaterId`, `sequence`, `sample`, `vaccu`, `status`) VALUES ('$testid','$param_id','$sequence','$sample_id','$vaccu_id','$status')"))
				{
					mysqli_query($link, "UPDATE `Parameter_old` SET `vaccu`='$vaccu_id' WHERE `ID`='$param_id'");
					mysqli_query($link, "UPDATE `Parameter_old` SET `sample`='$sample_id' WHERE `ID`='$param_id'");
					
					mysqli_query($link, "UPDATE `Testparameter` SET `sample`='$sample_id',`vaccu`='$vaccu_id' WHERE `ParamaterId`='$param_id' AND `sample`='0' AND `vaccu`='0'");
					/*
					$param_chk=mysqli_fetch_assoc(mysqli_query($link, "SELECT `vaccu`,`sample` FROM `Parameter_old` WHERE `ID`='$param_id'"));
					if($param_chk["vaccu"]==0)
					{
						mysqli_query($link, "UPDATE `Parameter_old` SET `vaccu`='$vaccu_id' WHERE `ID`='$param_id'");
					}
					
					if($param_chk["sample"]==0)
					{
						mysqli_query($link, "UPDATE `Parameter_old` SET `sample`='$sample_id' WHERE `ID`='$param_id'");
					}
					*/
				}else
				{
					$response["error"] = 1;
				}
				
				if($mandatory>0)
				{
					mysqli_query($link, "INSERT INTO `test_param_mandatory`(`testid`, `paramid`) VALUES ('$testid','$param_id')");
				}
			}
		}
	}
	
	if($response["error"]==0)
	{
		$response["message"] = "Saved";
	}else
	{
		$response["message"] = "Failed, try again later.";
	}
	
	echo json_encode($response);
}
?>
