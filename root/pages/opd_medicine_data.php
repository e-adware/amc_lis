<?php
$uhid=base64_decode($_GET["uhid"]);
$uhid=trim($uhid);
$opd=base64_decode($_GET["opd"]);
$opd=trim($opd);
$pat=mysqli_fetch_array(mysqli_query($link,"select * from `patient_info` where `patient_id`='$uhid'"));
$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$opd')"));
$qry=mysqli_query($link," SELECT `item_code`,`total_days` FROM `medicine_check` WHERE `patient_id`='$uhid' AND `opd_id`='$opd' ");
$all_medi="";
while($val=mysqli_fetch_array($qry))
{
	$all_medi.="###".$val["item_code"]."@@".$val["total_days"];
}

?>
<!--header-->
<div id="content-header">
    <div class="header_div">
		<span class="header">OPD Medicine</span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<span style="float:right"><button type="button" class="btn btn-info" onclick="back_page()">Back to list</button></span>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="opd" value="<?php echo $opd;?>" style="display:none;" />
	<table class="table table-condensed table-bordered table-report" style="background:snow;">
		<tr>
			<th>UHDI / PIN</th>
			<th>Patient Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Consultant Doctor</th>
		</tr>
		<tr>
			<td><?php echo $pat['uhid'];?> / <?php echo $opd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php if($pat['dob']){echo age_calculator($pat['dob'])." (".$pat['dob'].")";}else{echo $pat['age']." ".$pat['age_type'];}?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo $doc['Name'];?></td>
		</tr>
	</table>
	<table class="table table-condensed">
		<tr>
			<th>Drug Name</th>
			<td>
				<input type="text" name="medi" id="medi" class="span5" onFocus="load_medi_list()" onKeyUp="load_medi_list1(this.value,event)" onBlur="javascript:$('#med_div').fadeOut(500)" >
				<span id="med_dos" style="display:none;">
					Quantity: <input type="text" id="medi_quantity" onKeyup="add_medi_upp(this.value,event)" >
					<button class="btn btn-info" style="margin-top: -1%;" id="add_medicine_btn" onClick="add_medicine()">Add</button>
				</span>
				<input type="hidden" id="medid" />
				<div id="med_info"></div>
				<div id="med_div" align="center">
					<table style="background-color:#FFF" class="table table-bordered table-condensed" id="center_table" width="600px">
						<th>Drug Name</th>
						<?php
							$d=mysqli_query($link, "SELECT * FROM `ph_item_master` order by `item_name`");
							$i=1;
							while($d1=mysqli_fetch_array($d))
							{
						?>
							<tr onclick="select_med('<?php echo $d1['item_code'];?>','<?php echo $d1['item_name'];?>','<?php echo $d1['item_type'];?>','<?php echo $d1['generic'];?>')" style="cursor:pointer" <?php echo "id=med".$i;?>>
								<td><?php echo $d1['item_name'];?>
									<div <?php echo "id=mdname".$i;?> style="display:none;">
									<?php echo "#".$d1['item_code']."#".$d1['item_name']."#".$d1['item_type']."#".$d1['generic'];?>
									</div>
								</td>
							</tr>
						<?php
							$i++;
							}
						?>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<div id="display_selected_medicine"></div>
	<center><button class="btn btn-info" id="save_medicine_btn" onClick="save_medicine()">Save</button></center>
</div>
<input type="hidden" id="all_medi" value="<?php echo $all_medi; ?>">
<input type="hidden" id="chk_val1" value="0"/>
<script>
	$(document).ready(function(){
		load_sel_medicine($("#all_medi").val());
	});
	function back_page()
	{
		window.location="processing.php?param=102";
	}
	//-----------------------------------------Load Medicine List Onfocus-----------------------------||
	function load_medi_list()
	{
		$("html,body").animate({scrollTop: '500px'},500);
		$("#med_div").fadeIn(500);
		$("#medi").select();
		setTimeout(function(){ $("#chk_val1").val(1)},1000);
	}
	var med_tr=1;
	var med_sc=0;
	function load_medi_list1(val,e)
	{
		$("#med_dos").hide();
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#med_div").html("<img src='../images/ajax-loader.gif' />");
				$("#med_div").fadeIn(500);
				$.post("pages/load_medi.php"	,
				{
					val:val,
				},
				function(data,status)
				{
					$("#med_div").html(data);	
					med_tr=1;
					med_sc=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=med_tr+1;
				var cc=document.getElementById("med"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr+1;
					$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr-1;
					$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						$("#med_div").scrollTop(med_sc)
						med_sc=med_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=med_tr-1;
				var cc=document.getElementById("med"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr-1;
					$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr+1;
					$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						med_sc=med_sc-30;
						$("#med_div").scrollTop(med_sc)
					}
				}
			}
			
		}
		else
		{
			var cen_chk1=document.getElementById("chk_val1").value
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("mdname"+med_tr).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#medi").val(doc_naam);
				$("#medid").val(docs[1]);
				$("#med_info").fadeIn(500);
			}
			show_quantity();
		}
	}
	function select_med(id,name,typ,gen)
	{
		$("#medi").val(name);
		$("#medid").val(id);
		$("#med_info").html("");
		$("#med_div").fadeOut(500);
		show_quantity();
	}
	function show_quantity()
	{
		if($("#medi").val()=="")
		{
			$("#medi").focus();
			$("#med_dos").hide();
		}else
		{
			$("#med_dos").show();
			$("#medi_quantity").focus().val('');
		}
	}
	function add_medi_upp(val,e)
	{
		var n=val.length;
		var numex=/^[0-9]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#medi_quantity").val(val);
		}
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			//$("#add_medicine_btn").click();
			add_medicine();
		}
	}
	function add_medicine()
	{
		var medi_id=$("#medid").val();
		var medi_qn=$("#medi_quantity").val();
		var all_medi=$("#all_medi").val();
		if(medi_qn>0)
		{
			if (all_medi.indexOf(medi_id) > -1)
			{
				bootbox.dialog({ message: "Aleady added"});
				setTimeout(function(){
					bootbox.hideAll();
					$("#medi").focus();
					$("#med_dos").hide();
				 }, 1000);
				return true;
			}
			
			all_medi=all_medi+'###'+medi_id+'@@'+medi_qn;
			$("#all_medi").val(all_medi);
			
			// Clear
			$("#medi").val('');
			$("#medid").val('');
			$("#medi_quantity").val('');
			
			$("#medi").focus();
			$("#med_dos").hide();
			
			load_sel_medicine(all_medi);
			
		}else
		{
			$("#medi_quantity").focus();
		}
	}
	function load_sel_medicine(val)
	{
		$.post("pages/opd_medicine_ajax.php",
		{
			val:val,
			type:"load_sel_medicine",
		},
		function(data,status)
		{
			$("#display_selected_medicine").html(data);
		})
		if(val)
		{
			$("#save_medicine_btn").show();
		}else
		{
			$("#save_medicine_btn").hide();
		}
	}
	function delete_sel_item(itm_id,qn)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-danger",
					callback: function() {
						var qq="###"+itm_id+'@@'+qn;
						var ww=$("#all_medi").val();
						ww = ww.replace(qq,'');
						$("#all_medi").val(ww);
						load_sel_medicine(ww);
					}
				}
			}
		});
	}
	function save_medicine()
	{
		$.post("pages/opd_medicine_ajax.php",
		{
			val:$("#all_medi").val(),
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			user:$("#user").text().trim(),
			type:"save_medicine",
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<b>Saved</b>"});
			setTimeout(function(){
				bootbox.hideAll();
				var ww=$("#all_medi").val();
				load_sel_medicine(ww);
			 }, 2000);
		})
	}
</script>
