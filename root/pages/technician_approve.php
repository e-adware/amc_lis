<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Technician Approval</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div>
		<b style="display:none;">Patient Type: </b>
		
	</div>
	<table class="table table-bordered table-condensed text-center">
		<tr>
			<td colspan="4">
				<center>
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
					
					<?php
						if($glob_patient_type==0)
						{
							$pat_typ="display:none";
						}
					?>
					
					<select id="pat_type" name="" style="<?php echo $pat_typ;?>" class="span2">
						<option value="0">--All(TYPE)--</option>
						<option value="opd_id">OPD</option>
						<option value="ipd_id">IPD</option>
					</select>
					
					<select id="dep" name="dep" onchange="load_dep_test()" class="span2">
						<option value="0">--All(DEP)--</option>
					<?php
						$dep=mysqli_query($link,"select distinct type_id from testmaster where category_id='1' order by type_id");
						while($dp=mysqli_fetch_array($dep))
						{
							$dnm=mysqli_fetch_array(mysqli_query($link,"select * from test_department where id='$dp[type_id]'"));
							echo "<option value='$dp[type_id]'>$dnm[name]</option>";
						}
					?>
					</select>
					<span id="dep_tst_lst">
							<select id="tst_lst" name="tst_lst" class="span2">
								<option value="0">--All(Test)--</option>
							<?php
								$test=mysqli_query($link,"select * from testmaster where category_id='1' order by testname");
								while($tst=mysqli_fetch_array($test))
								{
									echo "<option value='$tst[testid]'>$tst[testname]</option>";
								}
							?>
							</select>
					</span>
					<button id="ser" value="Search" class="btn btn-search" onclick="$('#search_check').val('1');load_pat_ser();" style="margin-bottom: 10px;"><i class="icon-search"></i> Search</button>
					<input type="hidden" id="search_check" value="0"/>
				</center>
			</td>
		</tr>
				
		<tr>
			<td  style="text-align:center;font-weight:bold">
				Bill No. <br/> <input type="text" id="pin" onkeyup="load_pat_event(event)"/>
			</td>
			<td  style="text-align:center;font-weight:bold">
				UHID <br/> <input type="text" id="uhid" onkeyup="load_pat_event(event)"/> 
			</td>
			<td  style="text-align:center;font-weight:bold">
				Name <br/> 
				<input type="text" placeholder="" id="name" onkeyup="load_pat_event(event)"/>
						
			</td>
			<?php
			$bar_sty='display:none';
			if($glob_barcode==1)
			{
				$bar_sty='display:block';	
			}
			?>
			<td  style="text-align:center;font-weight:bold;<?php echo $bar_sty;?>">
				Barcode ID <br/> <input type="text" id="barcode_id" list="bar_list" onkeyup="load_pat(event,'')"/>
				<datalist id="bar_list">
				<?php
					$date=date('Y-m-d');
					$date1 = strtotime(date("Y-m-d", strtotime($date)) . " -5 days");
					$date_five=date("Y-m-d",$date1);
					$barc=mysqli_query($link,"select distinct barcode_id from test_sample_result where date between '$date1' and '$date'");
					while($bar=mysqli_fetch_array($barc))
					{
						echo "<option>$bar[barcode_id]</option>";
					}
				?>
				</datalist>
			</td>
			
		</tr>
	</table>
	<input type="hidden" id="ser_type" value="0" class="ScrollStyle"/>

	<table class="table table-bordered table-condensed" style="display:none">
		<tr>
			<td colspan="4">
				Select Doctor:
				<select id="for_doc">
					<option value="0">--Select--</option>
					<?php
					$fdoc=mysqli_query($link,"select * from lab_doctor where category='1'");
					while($fd=mysqli_fetch_array($fdoc))
					{
						echo "<option value='$fd[id]'>$fd[name]</option>";
					}
					?>
				</select>
			</td>
		</tr>
	</table>
	<div id="pat_list" class="ScrollStyleY">
		
	</div>
</div>
<input type="hidden" id="list_start" value="50">

<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		load_pat_ser();
		
		$('#pat_list').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);
			
			if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start=$("#list_start").val().trim();
				list_start=parseInt(list_start)+50;
				$("#list_start").val(list_start);
				load_pat_ser();
			}
		});
	});
	function load_pat_ser()
	{
		$("#loader").show();
		$.post("pages/technician_approve_pat.php",
		{
			pat_type:$("#pat_type").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			dep:$("#dep").val(),
			tst:$("#tst_lst").val(),
			name:$("#name").val(),
			pin:$("#pin").val(),
			uhid:$("#uhid").val(),
			barcode_id:$("#barcode_id").val(),
			list_start:$("#list_start").val(),
			type:1
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#pat_list").html(data);
			$("#ser_type").val("1");
			$("#search_check").val("0")
		})	
	}
	function load_pat_event(e)
	{
		if(e.which==13)
		{
			load_pat_ser();
		}
	}
	function load_pat(e,chk)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13 || chk==1)
		{
			var chk_dis=0;
			if(chk_dis==0)
			{
				$.post("pages/technician_approve_pat.php",
				{
					pat_type:$("#pat_type").val(),
					bill_no:$("#bill_no").val(),
					pref:$("#samp_prefix").val(),
					pno:$("#patient_no").val(),
					hosp_no:$("#hosp_no").val(),
					barcode_id:$("#barcode_id").val(),
					fdate:$("#fdate").val(),
					tdate:$("#tdate").val(),
					type:1
				},
				function(data,status)
				{
					
					$("#pat_list").html(data);
					$("#ser_type").val("2");
					$("#check_display").val("1");
				})
			}
			else
			{
				/*var dep=$("#dep").val();
				$("#dep_"+dep+"").click();
				$("#check_display").val("1");*/
			}
		}
		else
		{
			$("#check_display").val("0");
		}
	}
	
	function load_pat_dep(val,dep)
	{
		var pid=$("#pid_"+val+"").val();
		var opd_id=$("#opd_"+val+"").val();
		var ipd_id=$("#ipd_"+val+"").val();
		var batch_no=$("#batch_"+val+"").val();
		
		var dep=dep;
		var user=$("#user").text().trim();
		var fdoc=$("#for_doc").val();
		
		var url="pages/technician_approve_test.php?uhid="+pid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch="+batch_no+"&dep="+dep+"&user="+user+"&fdoc="+fdoc;
		
		window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');		
		
	}
		
	//~ function load_tests(val)
	//~ {
		
			//~ var pid=$("#pid_"+val+"").val();
			//~ var opd_id=$("#opd_"+val+"").val();
			//~ var ipd_id=$("#ipd_"+val+"").val();
			//~ var batch_no=$("#batch_"+val+"").val();
			//~ var dep=$("#dep").val();
			//~ var user=$("#user").text();
			//~ var fdoc=$("#for_doc").val();
			
			//~ var url="pages/technician_approve_test.php?uhid="+pid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&dep="+dep+"&user="+user+"&fdoc="+fdoc;
			
			//~ var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');		
		
	//~ }
	
	function load_search()
	{
		if($("#ser_type").val()==1)
		{
			load_pat_ser();
		}
		else
		{
			load_pat('',1)
		}
	}
	
	function load_hosp_det(hosp)
	{
		$.post("pages/technician_approve_pat.php",
		{
			pat_type:$("#pat_type").val(),
			//id_typ:1,
			//reg:$("#reg").val(),
			pref:$("#samp_prefix").val(),
			sno:$("#sample_no").val(),
			hosp_no:hosp,
			barcode_id:$("#barcode_id").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			hosp_chk:1,
			type:1
		},
		function(data,status)
		{
			
			$("#pat_list").html(data);
			$("#ser_type").val("2");
			$("#check_display").val("1");
			//$("#hosp_no").val(hosp);
		})
	}
	
	function load_dep_test()
	{
		$.post("pages/technician_approve_pat.php",
		{
			dep:$("#dep").val(),
			type:3
		},
		function(data,status)
		{
			$("#dep_tst_lst").html(data);
		})
	}
	
	setInterval(function(){ $("#ser").click();},60000);
</script>
<style>
.ScrollStyleY
{
    max-height: 450px;
    overflow-y: scroll;
}

.flagged td{ color:red;}

#bill_error td{font-weight:bold; color:blue;}

.rep_hosp{ font-weight:bold;cursor:pointer;text-decoration:underline;color:green}
</style>
