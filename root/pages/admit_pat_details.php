<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Admit Patient</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<?php
	$paramm=base64_encode(83);
	$uhid=base64_decode($_GET['uhid']);
	$ipd=base64_decode($_GET['ipd']);
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	$dch_qry=mysqli_query($link,"select * from ipd_pat_discharge_details where patient_id='$uhid' AND `ipd_id`='$ipd'");
	$disch=mysqli_num_rows($dch_qry);
	if($pat['dob'])
	$age=age_calculator($pat['dob'])." (".$pat['dob'].")";
	else
	$age=$pat['age']." ".$pat['age_type'];
	?>
	<span style="float:right;"><input type="button" class="btn btn-info" id="add" value="Back to list" onclick="window.location='processing.php?param=125'" style="" /></span>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>Age (DOB)</th>
			<th>Sex</th>
		</tr>
		<tr>
			<td><?php echo $pat['uhid'];?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat['sex'];?></td>
		</tr>
	</table>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
	<div id="results" style="max-height:300px;overflow-y:scroll;">
	
	</div>
	<div id="bed_info" style="display:none;">
		
	</div>
	<div id="bed_btn_info" style="display:none;">
		<button type="button" id="btn_assign" class="btn btn-primary" onclick="bed_assign_ok()">Assign</button>
		<button type="button" class="btn btn-danger" onclick="clr_bed_assign()">Cancel</button>
	</div>
	<input type="hidden" id="chk_val2" value="0"/>
	
	<input type="button" data-toggle="modal" data-target="#myModal1" id="mod" style="display:none"/>
	<input type="hidden" id="mod_chk" value="0"/>
	<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="ress">
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none"/>
	<input type="hidden" id="mod_chk2" value="0"/>
	<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results2"> </div>
				</div>
			</div>
		</div>
	</div>
	 <!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal modal-lg fade">
		  <div class="modal-body">
			<div id="post_medi">
				
			</div>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="save_disc_medi()" class="btn btn-primary" href="#">Save</a>
			<a data-dismiss="modal" onclick="" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end--> 
</div>
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		load_bed_info();
		nursing_bed_transfer();
	});
	
	function load_bed_info()
	{
		$.post("pages/admit_req_list_ajax.php",
		{
			type:"load_bed_info",
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
		},
		function(data,status)
		{
			//alert(data);
			if(data!="")
			{
				$("#bed_info").html(data);
				$("#bed_info").show();
				$("#bed_btn_info").show();
			}
			else
			{
				$("#bed_info").empty();
				$("#bed_info").hide();
				$("#bed_btn_info").hide();
			}
			//alert(data);
			//$("#mod").click();
		})
	}
	function clr_bed_assign()
	{
		$.post("pages/nursing_load_g.php",
		{
			type:"clr_bed_assign",
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			usr:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#ward_id").val('');
			$("#bed_id").val('');
			$("#bed_info").hide(500);
			$("#bed_btn_info").hide(500);
		})
	}
	function bed_assign_ok()
	{
		$("#btn_assign").attr("disabled",true);
		$.post("pages/admit_req_list_ajax.php",
		{
			type:"bed_assign_ok",
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			ward:$("#ward_id").val(),
			bed:$("#bed_id").val(),
			usr:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert(data);
			window.location='processing.php?param=52&uhid='+$("#uhid").val().trim()+'&ipd='+data;
			//window.location='processing.php?param=238';
		})
	}
	function nursing_bed_transfer()
	{
		$.post("pages/admit_req_list_ajax.php",
		{
			type:"nursing_bed_transfer",
			uhid:$("#uhid").val(),
			wrd:$("#wrd").val(),
		},
		function(data,status)
		{
			//$("#mod").click();
			$("#results").html(data);
			//$("#myModal").animate({'top':'5%','left':'35%',"width":"1000px",'margin':'auto'},"slow");
			nursing_chk_bed_assign();
			load_bed_info();
		})
	}
	function nursing_chk_bed_assign()
	{
		setInterval(function()
		{
			$.post("pages/admit_req_list_ajax.php",
			{
				type:"nursing_bed_transfer",
				uhid:$("#uhid").val(),
				wrd:$("#wrd").val(),
			},
			function(data,status)
			{
				$("#results").html(data);
				load_bed_info();
			})
		},1500);
	}
	function nursing_bed_asign(w_id,b_id,w_name,b_no)
	{
		$.post("pages/admit_req_list_ajax.php",
		{
			type:"nursing_bed_asign",
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			w_id:w_id,
			b_id:b_id
		},
		function(data,status)
		{
			load_bed_info();
		})
	}
	function bed_det()
	{
		
	}
</script>
