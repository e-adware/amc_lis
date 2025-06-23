<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">OT Booking Process</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<?php
	$uhid=base64_decode($_GET['uhid']);
	$ipd=base64_decode($_GET['ipd']);
	$show=base64_decode($_GET['show']);
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($pat['dob'])
	$age=age_calculator($pat['dob'])." (".$pat['dob'].")";
	else
	$age=$pat['age']." ".$pat['age_type'];
	$adm=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
	$ot=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	if($ot['patient_id'])
	{
		$dis="disabled='disabled'";
		$n_dis="";
	}
	else
	{
		$dis="";
		$n_dis="disabled='disabled'";
	}
	?>
	<span style="float:right;"><input type="button" class="btn btn-info" id="add" value="Back to list" onclick="window.location='processing.php?param=239'" style="" /></span>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Admitted On</th>
			<th>Admitted Under</th>
		</tr>
		<tr>
			<td><?php echo $uhid;?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo convert_date_g($adm['date']);?></td>
			<td><?php echo $doc['Name'];?></td>
		</tr>
	</table>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
<script src="include/js/jquery-ui.js"></script>
<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->
	<table class="table table-condensed table-bordered" id="">
				<tr>
					<th>Date</th>
					<th><input type="text" id="ot_date" readonly="readonly" style="cursor:text;" value="<?php echo $ot['ot_date'];?>" placeholder="Date" /></th>
					<th>Requesting Doctor</th>
					<th>
						<select id="doc">
							<option value="0">Select</option>
							<?php
							//$qry=mysqli_query($link,"SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`='5'");
							$qry=mysqli_query($link,"SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` ORDER BY `Name`");
							while($rr=mysqli_fetch_array($qry))
							{
							?>
							<option value="<?php echo $rr['consultantdoctorid'];?>" <?php if($ot['consultantdoctorid']==$rr['consultantdoctorid']){echo "selected='selected'";}?>><?php echo $rr['Name'];?></option>
							<?php
							}
							?>
						</select>
					</th>
				</tr>
				<tr>
					<th style="display:none">Select OT</th>
					<th style="display:none">
						<select id="ot">
							<option value="0">Select</option>
							<?php
							$q=mysqli_query($link,"SELECT `ot_area_id`,`ot_area_name` FROM `ot_area_master` ORDER BY `ot_area_name`");
							while($r=mysqli_fetch_array($q))
							{
							?>
							<option value="<?php echo $r['ot_area_id'];?>"><?php echo $r['ot_area_name'];?></option>
							<?php
							}
							?>
						</select>
					</th>
					<th>Procedure</th>
					<th colspan="3">
						<input type="text" list="browsrs" class="span8" id="pr" value="<?php echo $ot['procedure_id'];?>" placeholder="Procedure" />
						<datalist id="browsrs">
						<?php
							$qq = mysqli_query($link,"SELECT `name` FROM `clinical_procedure` ORDER BY `name`");
							while($cc=mysqli_fetch_array($qq))
							{
								echo "<option value='$cc[name]'>";
							}
						?>
						</datalist>
						<!--<select id="pr">
							<option value="0">Select</option>
							<?php
							//$qr=mysqli_query($link,"SELECT `id`,`name` FROM `clinical_procedure` ORDER BY `name`");
							//while($rr=mysqli_fetch_array($qr))
							{
							?>
							<option value="<?php echo $rr['id'];?>"><?php echo $rr['name'];?></option>
							<?php
							}
							?>
						</select>-->
					</th>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;">
						<button type="button" id="sav_btn_new" style="display:none;" class="btn btn-success" onclick="save_ot_book('1')"><i class="icon icon-save"></i> Update</button>
						<button type="button" id="sav_btn" class="btn btn-primary" onclick="save_ot_book('0')" <?php echo $dis;?>><i class="icon icon-save"></i> Save</button>
						<button type="button" id="upd_btn" class="btn btn-info" onclick="upd_ot_book()" <?php echo $n_dis;?>><i class="icon icon-upload"></i> Update</button>
						<button type="button" id="can_btn" class="btn btn-danger" onclick="can_ot_book()" <?php echo $n_dis;?>><i class="icon icon-remove"></i> Cancel Book</button>
						<button type="button" id="can_btn_new" style="display:none;" class="btn btn-danger" onclick="can_upd()"><i class="icon icon-remove"></i> Cancel</button>
					</td>
				</tr>
			</table>
	<script>
		$("#ot_date").datepicker({dateFormat: 'yy-mm-dd',minDate:0,});
	</script>
</div>
<script>
	function upd_ot_book()
	{
		$("#sav_btn").hide();
		$("#upd_btn").hide();
		$("#can_btn").hide();
		$("#sav_btn_new").show();
		$("#can_btn_new").show();
	}
	function can_upd()
	{
		$("#sav_btn").show();
		$("#upd_btn").show();
		$("#can_btn").show();
		$("#sav_btn_new").hide();
		$("#can_btn_new").hide();
	}
	function can_ot_book()
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to cancel ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/ot_booking_ajax.php",
						{
							uhid:$("#uhid").val(),
							ipd:$("#ipd").val(),
							type:"cancel_ot_book",
						},
						function(data,status)
						{
							bootbox.dialog({ message: data});
							setTimeout(function()
							{
								window.location="processing.php?param=239";
							}, 1000);
						})
					}
				}
			}
		});
	}
	function save_ot_book(tp)
	{
		/*
		if($("#ot").val()=="0")
		{
			$("#ot").focus();
		}
		*/
		if($("#pr").val().trim()=="")
		{
			$("#pr").focus();
		}
		else if($("#ot_date").val()=="")
		{
			$("#ot_date").focus();
		}
		else if($("#doc").val()=="0")
		{
			$("#doc").focus();
		}
		else
		{
			$("#sav_btn").attr("disabled",true);
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				ipd:$("#ipd").val(),
				ot:$("#ot").val(),
				pr:$("#pr").val(),
				ot_date:$("#ot_date").val(),
				doc:$("#doc").val(),
				usr:$("#user").text().trim(),
				tp:tp,
				type:"save_ipd_ot_book",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
				}, 1000);
				if(tp=='1')
				{
					can_upd();
				}
				else if(tp=='0')
				{
					$("#upd_btn").attr("disabled",false);
					$("#can_btn").attr("disabled",false);
				}
			})
		}
	}
</script>
