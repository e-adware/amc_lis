<?php
$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);
$balance_discharge_str=base64_decode($_GET['balance_discharge_str']);
?>
<!--header-->
<div id="content-header">
    <div class="header_div">
		<span class="header"> <?php echo $menu_info["par_name"]; ?></span>
		</span>
	</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<span class="noprint" style="float: right;"><input type="button" class="btn btn-default" id="Name1" value="Print" onclick="print_discharged_list()"></span>
	<table class="table table_field">
		 <tr>
			 
			<td class="">
				<b>UNIT NO.:</b>
				<input list="browsrs" type="text" name="pin1" id="pin1" class="" style="width:100px;" value="<?php echo $uhid_str; ?>" onkeyup="search_patient_list()" autofocus />
				<datalist id="browsrs">
				<?php
					//~ $pid = mysqli_query($link,"SELECT `patient_id` FROM `ipd_pat_discharge_details` order by `slno` DESC");
					//~ while($pat_uid=mysqli_fetch_array($pid))
					//~ {
						//~ echo "<option value='$pat_uid[patient_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			
			<td class="">
				<b>Bill No:</b>
				<input list="browsr" type="text" name="pin2" id="pin2" class="" style="width:100px;" value="<?php echo $pin_str; ?>" onkeyup="search_patient_list()" />
				<datalist id="browsr">
				<?php
					//~ $oid= mysqli_query($link," SELECT `ipd_id` FROM `ipd_pat_discharge_details` order by `slno` DESC");
					//~ while($pat_oid=mysqli_fetch_array($oid))
					//~ {
						//~ echo "<option value='$pat_oid[ipd_id]'>";
					//~ }
				?>
				</datalist>
			</td>
			
			<td class="" style="display:none">
				<b>IPD Serial :</b>
				<input list="browsrr" type="text" name="pin3" id="pin3" class="" style="width:100px;" onkeyup="search_patient_list()" />
				<datalist id="browsrr">
				<?php
					//~ $ipd_serial_qry= mysqli_query($link," SELECT `ipd_serial` FROM `uhid_and_opdid` WHERE `ipd_serial`>0 AND `opd_id` in ( SELECT `ipd_id` FROM `ipd_pat_discharge_details` ) order by `slno` DESC");
					//~ while($ipd_serial=mysqli_fetch_array($ipd_serial_qry))
					//~ {
						//~ echo "<option value='$ipd_serial[ipd_serial]'>";
					//~ }
				?>
				</datalist>
			</td>
			<th class="">
				From <input type="text" name="pin4" id="pin4" class="pin input-group datepicker span2" style="width:80px;"   value="<?php echo $fdate_str; ?>" /> 
				To <input type="text" name="pin5" id="pin5" class="pin input-group datepicker span2" style="width:80px;"  value="<?php echo $tdate_str; ?>" />
			</th>
			<td class="">
				<select id="balance_discharge"  onchange="search_patient_list()">
					<option value="0" <?php if($balance_discharge_str==0){ echo "selected"; } ?>>All Discharged</option>
					<option value="1" <?php if($balance_discharge_str==1){ echo "selected"; } ?>>Balance Discharged</option>
					<option value="2" <?php if($balance_discharge_str==2){ echo "selected"; } ?>>Full Paid Discharged</option>
				</select>
			</td>
			<td>
				<input type="button"  name="search" id="srch" value="Search" class="btn btn-primary btn-sm" onClick="search_patient_list()"/>
			</td>
		  </tr>
	</table>
	<div id="load_all" class="ScrollStyle">
	
	</div>
</div>
<input type="hidden" id="list_start" value="50">
<!-- Loader -->
<div id="loader" style="margin-top:-5%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
	.discharged{ background-color:#9dcf8a; }

@media print
{
	#header ,#search,.modal,#sidebar,#search_test {display:none;} 
	table tr td,table tr td{ font-size:10px;}
	.head{font-size:12px;}
	#search_data{ margin-left:-50px;}
	#ipd_ser{display:none;}
	#user-nav{display:none;}
	#load_all{display:block;}
	.table_field{display:none;}
	.noprint{
		display:none;
	}
}
@page
{
	margin:0.3cm;
	margin-left:0cm;
}
</style>
<script>
	$(document).ready(function()
	{
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		search_patient_list();
		$("#pin1").keyup(function(e)
		{
			$("#pin2").val('');
			$("#pin3").val('');
			$("#pin4").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pin2").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#pin2").keyup(function(e)
		{
			$("#pin1").val('');
			$("#pin3").val('');
			$("#pin4").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pin3").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#pin3").keyup(function(e)
		{
			$("#pin1").val('');
			$("#pin2").val('');
			$("#pin4").val('');
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pin4").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		$("#pin4").keyup(function(e)
		{
			$("#pin1").val('');
			$("#pin2").val('');
			$("#pin3").val('');
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$("#pin1").focus();
				}
				else
				{
					$("#srch").focus();
				}
			}
		});
		
		$('#load_all').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);
			
			if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start=$("#list_start").val().trim();
				list_start=parseInt(list_start)+50;
				$("#list_start").val(list_start);
				search_patient_list();
			}
		});
	});
	function search_patient_list()
	{
		$("#loader").show();
		$.post("pages/ipd_discharged_pat_list_data.php",
		{
			uhid:$("#pin1").val(),
			ipd:$("#pin2").val(),
			ipd_serial:$("#pin3").val(),
			from:$("#pin4").val(),
			to:$("#pin5").val(),
			balance_discharge:$("#balance_discharge").val(),
			list_start:$("#list_start").val(),
			usr:$("#user").text().trim(),
			type:"search_patient_list_ipd_dis",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").html(data);
			if($("#pin1").val()!='')
			{
				$("#pin1").focus();
			}
			if($("#pin2").val()!='')
			{
				$("#pin2").focus();
			}
			if($("#pin3").val()!='')
			{
				$("#pin3").focus();
			}
			// Every 5 second load
			//~ setTimeout(function()
			//~ {
				//~ search_patient_list();
			//~ }, 2000);
		})
	}
	function redirect_page(uhid,ipd)
	{
		var date_str="&param_str=221&fdate_str="+$("#pin4").val()+"&tdate_str="+$("#pin5").val()+"&uhid_str="+$("#pin1").val()+"&pin_str="+$("#pin2").val()+"&balance_discharge_str="+$("#balance_discharge").val();
		
		window.location="processing.php?param=52&uhid="+uhid+"&ipd="+ipd+"&val="+1+date_str;
	}
	function print_discharged_list()
	{
		var uhid=$("#pin1").val();
		var ipd=$("#pin2").val();
		var ipd_serial=$("#pin3").val();
		var from=$("#pin4").val();
		var to=$("#pin5").val();
		var balance_discharge=$("#balance_discharge").val();
		var list_start=$("#list_start").val();
		var usr=$("#user").text().trim();
		
		url="pages/ipd_discharged_pat_list_print.php?from="+from+"&to="+to+"&balance_discharge="+balance_discharge+"&uhid="+uhid+"&ipd="+ipd+"&ipd_serial="+ipd_serial+"&list_start="+list_start+"&usr="+usr;
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1300');
	}
</script>
