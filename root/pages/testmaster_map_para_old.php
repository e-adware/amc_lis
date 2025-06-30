<?php
include("../../includes/connection.php");
$id = $_POST['id'];
$name = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select testname from testmaster where testid='$id'"));
$tst = mysqli_query($GLOBALS["___mysqli_ston"], "select * from  testmaster where category_id='1' order by testname");
?>
<div style="font-size:12px !important">
	<table class="table table-bordered table-condensed">
		<tr>
			<td colspan="2">
				Test Name:<input type="text" value="<?php echo $name['testname']; ?>" readonly />
				<input type="text" id="searchh" onkeyup="search(this.value)" placeholder="Type to search">
				<select onChange="selected_test(this.value)">
					<option value='0'>Select Test</option>
					<?php
					while ($test = mysqli_fetch_array($tst)) {
						echo "<option value='$test[testid]'>$test[testname]</option>";
					}
					?>
				</select>
				<button class="btn btn-info" onclick="load_dlc_check(<?php echo $id; ?>)" style="margin-top:-10px">Add DLC Check</button>
			</td>
		</tr>
		<tr>
			<td width="35%">
				<div style="height:420px;overflow:auto;overflow-x:hidden" id="param_load">
					<!--<table class="table table-bordered table-condensed" id="tblData">
					<th>ID</th><th>Parameter Name</th><th>User Interface</th>
					<?php
					$par = mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old order by Name");
					while ($p = mysqli_fetch_array($par)) {
						$r_nm = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select ResultType_name from ResultType where ResultTypeId='$p[ResultType]'"));
						?>
							
							<tr onclick="add_para(<?php echo $p['ID']; ?>,'<?php echo ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $p['Name']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")); ?>','<?php echo $r_nm['ResultType_name']; ?>')">
							
							<?php
							echo "<td>$p[ID]</td><td width='60%'>$p[Name]</td><td>$r_nm[ResultType_name]</td></tr>";
					}
					?>
				</table>-->
					<span id="no_record"></span>
				</div>
			</td>

			<td width="65%">
				<div style="height:420px;overflow:auto;overflow-x:hidden">
					<?php
					$para = mysqli_query($GLOBALS["___mysqli_ston"], "select * from Testparameter where TestId='$id' order by sequence");
					$tot_par = mysqli_num_rows($para);
					?>
					<table class="table table-bordered table-condensed table-report" id="par">
						<th>ID</th>
						<th>Parameter Name</th>
						<th width="100px">Sample|Vaccu</th>
						<th>Interface</th><?php if ($tot_par > 1) { ?>
							<th>Mand.</th> <?php } ?>
						<th>Don't Print</th>
						<th>Seq.</th>
						<th></th>
						<?php
						$par_inc = 1;
						while ($par = mysqli_fetch_array($para)) {
							$pn = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old where ID='$par[ParamaterId]'"));
							$rt = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from ResultType where ResultTypeid='$pn[ResultType]'"));

							$cls = 0;
							if ($pn[ResultType] == 0) {
								$cls = 4;
							}

							if (!$par[sample] || $par[sample] == 0) {
								$par[sample] = $pn[sample];
							}
							if (!$par[vaccu] || $par[vaccu] == 0) {
								$par[vaccu] = $pn[vaccu];
							}
							?>

							<tr>
								<td><?php echo $par['ParamaterId']; ?></td>
								<td colspan="<?php echo $cls; ?>"><?php echo $pn['Name']; ?><input type="hidden" class="p_id"
										value="<?php echo $par['ParamaterId']; ?>" /></td>

								<?php if ($cls == 0) { ?>
									<td>
										<select id="samp_<?php echo $par['ParamaterId']; ?>" class="samp"
											onchange="update_sample('<?php echo $id; ?>','<?php echo $par[ParamaterId]; ?>',this.value)">
											<option value="0">--Select Sample--</option>
											<?php
											$sam = mysqli_query($GLOBALS["___mysqli_ston"], "select * from  Sample order by Name");
											while ($s = mysqli_fetch_array($sam)) {
												if ($par[sample] == $s[ID]) {
													echo $sel = "Selected='selected'";
												} else {
													$sel = "";
												}
												echo "<option value='$s[ID]' $sel>$s[Name]</option>";
											}
											?>
										</select>

										<select id="vac_<?php echo $par['ParamaterId']; ?>" class="vacc"
											onchange="update_vaccu('<?php echo $id; ?>','<?php echo $par[ParamaterId]; ?>',this.value)">
											<option value="0">--Select Vaccu--</option>
											<?php
											$vac = mysqli_query($GLOBALS["___mysqli_ston"], "select * from vaccu_master order by type");
											while ($v = mysqli_fetch_array($vac)) {
												if ($par[vaccu] == $v[id]) {
													echo $sel2 = "Selected='selected'";
												} else {
													$sel2 = "";
												}
												echo "<option value='$v[id]' $sel2>$v[type]</option>";
											}
											?>
										</select>
									</td>


									<td><?php echo $rt['ResultType_name']; ?></td>

									<?php
									if ($tot_par > 1) {
										$mand_chk = "";
										$chk_mand = mysqli_fetch_array(mysqli_query($link, "select count(*) as tot from test_param_mandatory where testid='$id' and paramid='$par[ParamaterId]'"));
										if ($chk_mand[tot] > 0) {
											$mand_chk = "Checked";
										}
										?>
										<td><input type="checkbox"
												onclick="save_mand(this,'<?php echo $id; ?>','<?php echo $par["ParamaterId"]; ?>')"
												<?php echo $mand_chk; ?> /></td> <?php
									}
								}
								?>
								<td><input type="checkbox"
										onclick="save_dont_print(this,'<?php echo $id; ?>','<?php echo $par["ParamaterId"]; ?>')"
										class="dont_print_cls" id="dont_print_<?php echo $par['ParamaterId']; ?>" <?php if ($par['status'] == 1) {
											  echo "checked";
										  } ?>></td>
								<td><input type="text" class="seq" id="par_seq_<?php echo $par_inc; ?>"
										name="<?php echo $par_inc; ?>" onkeyup='check_seq(<?php echo $par_inc; ?>,event)'
										value="<?php echo $par['sequence']; ?>" style="width:15px" /></td>
								<td onclick="$(this).closest('tr').remove()"><i class="icon-remove"></i></td>
							</tr>

							<?php
							$par_inc++;
						}
						?>
					</table>
				</div>


			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<input type="button" class="btn btn-default" value="Save" onclick="save_test_para(<?php echo $id; ?>)" />
				<input type="button" class="btn btn-danger" value="Close" onclick="$('#mod').click()" />
			</td>
		</tr>
	</table>
</div>
<script>
	function search(inputVal) {
		var table = $('#tblData');
		table.find('tr').each(function (index, row) {
			var allCells = $(row).find('td');
			if (allCells.length > 0) {
				var found = false;
				allCells.each(function (index, td) {
					var regExp = new RegExp(inputVal, 'i');
					if (regExp.test($(td).text())) {
						found = true;
						return false;
					}
				});
				if (found == true) {
					$("#no_record").text("");
					$(row).show();
				} else {
					$(row).hide();
					var n = $('tr:visible').length;
					if (n == 1) {
						$("#no_record").text("No matching records found");
					} else {
						$("#no_record").text("");
					}
				}
				//if(found == true)$(row).show();else $(row).hide();
			}
		});
	}
	function selected_test(id) {
		$("#searchh").val('');
		load_param(id);
	}
	function load_param(id) {
		$.post("pages/load_param.php",
			{
				id: id
			},
			function (data, status) {
				$("#param_load").html(data);
			})
	}
</script>
<?php
mysqli_close($link);
?>
