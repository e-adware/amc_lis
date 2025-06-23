<?php
$uhid=base64_decode($_GET['uhid']);
$ipd=base64_decode($_GET['ipd']);
$group_id=base64_decode($_GET['group_id']);
$service_id=base64_decode($_GET['service_id']);

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">IPD Package Split Dashboard</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
<?php
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	if($pat['dob'])
	{
		$age=age_calculator($pat['dob'])." (".$pat['dob'].")";
	}
	else
	{
		$age=$pat['age']." ".$pat['age_type'];
	}
	$adm=mysqli_fetch_array(mysqli_query($link,"SELECT `date` FROM `ipd_pat_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` IN (SELECT `attend_doc` FROM `ipd_pat_doc_details` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd')"));
	$d_name="";
	if($doc)
	{
		$d_name=$doc['Name'];
	}
	else
	{
		$doc=mysqli_fetch_array(mysqli_query($link,"SELECT b.`ref_name` FROM `uhid_and_opdid` a, `refbydoctor_master`b WHERE a.`patient_id`='$uhid' AND a.`opd_id`='$ipd' AND a.`refbydoctorid`=b.`refbydoctorid` "));
		$d_name=$doc['ref_name'];
	}
?>
	<span style="float:right;"><input type="button" class="btn btn-info" id="add" value="Back to list" onclick="window.location='processing.php?param=851'" style="" /></span>
	<table class="table table-condensed table-bordered" style="background: snow">
		<tr>
			<th>UHID</th>
			<th>IPD ID</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Consultant</th>
		</tr>
		<tr>
			<td><?php echo $uhid;?></td>
			<td><?php echo $ipd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo $d_name;?></td>
		</tr>
	</table>
	<input type="text" id="uhid" value="<?php echo $uhid;?>" style="display:none;" />
	<input type="text" id="ipd" value="<?php echo $ipd;?>" style="display:none;" />
	<input type="text" id="group_id" value="<?php echo $group_id;?>" style="display:none;" />
	<input type="text" id="service_id" value="<?php echo $service_id;?>" style="display:none;" />
	<div id="res">
	
	</div>
	<div id="msgg" style="position:fixed;display:none;top:40%;left:45%;font-size:30px;color:#ee0000;"></div>
	
<div id="loader" style="margin-top:-10%;"></div>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link rel="stylesheet" href="../css/select2.min.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../js/jquery.gritter.min.js"></script>
<script src="../js/select2.min.js"></script>
<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->
<script>
	$(document).ready(function()
	{
		scheduling();
	});

	function scheduling()
	{
		$("#loader").show();
		$.post("pages/ipd_package_split_data.php",
		{
			uhid:$("#uhid").val().trim(),
			ipd:$("#ipd").val().trim(),
			group_id:$("#group_id").val().trim(),
			service_id:$("#service_id").val().trim(),
			usr:$("#user").text().trim(),
			type:2,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
			$("select").select2({ theme: "classic" });
		})
	}
	function show_msg(txt)
	{
		$("#err_msg").text(txt);
		$("#err_msg").show();
		setTimeout(function(){$("#err_msg").fadeOut(2000)},1000);
	}
	function add_res_row()
	{
		var ln=$(".all_res");
		var stop=0;
		for(var i=0;i<ln.length;i++)
		{
			//var tt=$("#tr"+i).find('td:eq(1) input:first').val()+"@"+$("#tr"+i).find('td:eq(2) input:first').val();
			var tt=$(".all_res:eq("+i+")").find('td:eq(0) input:first').val()+"@"+$(".all_res:eq("+i+")").find('td:eq(1) input:first').val();
			if(tt==$("#res_id").val()+"@"+$("#emp").val())
			{
				stop=1;
			}
		}
		if($("#res_id").val()=="0")
		{
			$("#res_id").select2("focus");
			show_msg("Select Resourse");
		}
		else if($("#emp").val()=="0")
		{
			if($("#emp").val()!="")
			{
				$("#emp").select2("focus");
				show_msg("Select Doctor");
			}
		}
		else if(stop==1)
		{
			show_msg("Already selected");
		}
		else
		{
			$.post("pages/ipd_package_split_data.php",
			{
				res:$("#res_id").val(),
				emp:$("#emp").val(),
				type:4,
			},
			function(data,status)
			{
				var vl=data.split("@@");
				$("#last_tr").closest("tr").before('<tr class="all_res"><td><input type="hidden" value="'+$("#res_id").val()+'" />'+vl[0]+'</td><td><input type="hidden" value="'+$("#emp").val()+'" />'+vl[1]+'</td><td><input type="text" class="span2" onkeyup="sum_amt(this)" value="0" /></td><td style="text-align:center;"><i class="icon-remove  icon-large" style="color:#aa0000;cursor:pointer;" onclick="$(this).parent().parent().remove();check_all();"></i></td></tr>');
				$("#res_id").val("0").trigger("change");
				check_sum('');
			})
		}
	}
	function load_emp_list(id)
	{
		$.post("pages/ipd_package_split_data.php",
		{
			service_id:$("#res_id").val(),
			type:3,
		},
		function(data,status)
		{
			//alert(data);
			$("#emp_list").html(data);
			$("select").select2({ theme: "classic" });
		})
	}
	function check_all()
	{
		sum_amt('');
	}
	function sum_amt(a)
	{
		var amount=parseInt($("#amount").val().trim());
		if(($(a).val()*0)!=0)
		{
			$(a).val('');
			//return true;
		}
		var otr=$(".all_res");
		var tot=0;
		var vl=0;
		for(var j=0; j<(otr.length); j++)
		{
			vl=$(".all_res:eq("+j+")").find('td:eq(2) input:first').val();
			if(vl=="")
			{
				vl=0;
			}
			else
			{
				vl=parseFloat(vl);
			}
			tot+=vl;
		}
		$("#tot_val").val(tot);
		check_sum();
	}
	function check_sum()
	{
		var amount=parseInt($("#amount").val().trim());
		var tot_val=parseInt($("#tot_val").val().trim());
		if(tot_val!=amount)
		{
			$("#tot_val").css({'border-color':'#FE0002','box-shadow':'0 0 5px rgba(254, 0, 2)'});
		}
		else
		{
			$("#tot_val").css({'border-color':'','box-shadow':''});
		}
	}
	function save_all_res()
	{
		var ln=$(".all_res");
		var amount=parseInt($("#amount").val().trim());
		var tot_val=parseInt($("#tot_val").val().trim());
		
		if(ln.length==0)
		{
			show_msg("Select Resourse");
		}
		else if(tot_val==0)
		{
			show_msg("Zero amount cannot save");
		}
		else if(tot_val!=amount)
		{
			show_msg("Amount not matched");
		}
		else
		{
			$("#btn_done").attr("disabled",true);
			var all="";
			for(var i=0;i<ln.length;i++)
			{
				all+=$(".all_res:eq("+i+")").find('td:eq(0) input:first').val()+"@@"+$(".all_res:eq("+i+")").find('td:eq(1) input:first').val()+"@@"+$(".all_res:eq("+i+")").find('td:eq(2) input:first').val()+"@@#@#";
			}
			//alert(all);
			$("#loader").show();
			$.post("pages/ipd_package_split_data.php",
			{
				all:all,
				serv_slno:$("#serv_slno").val().trim(),
				usr:$("#user").text().trim(),
				type:5,
			},
			function(data,status)
			{
				$("#loader").hide();
				show_msg(data);
				$("#btn_done").attr("disabled",false);
				setTimeout(function(){scheduling();},1000);
			})
		}
	}
</script>
