<?php echo $patient_type;?>
<div id="content-header">
    <div class="header_div"> <span class="header">Worksheet</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	
	<table class="table table-bordered text-center" id="search_data">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control datepicker" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
					<input type="text" id="ftime" class="timepicker" value="06:00" placeholder="HH:MM" style="width:50px" />
					<b>To</b>
					<input class="form-control datepicker" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
					<input type="text" id="ttime" class="timepicker" value="23:59" placeholder="HH:MM" style="width:50px" />
				</center>
			</td>
			
			<td>
				<select id="dept">
					<option value="0">--All Dept--</option>
					<?php
					$dept=mysqli_query($link,"select distinct type_id,type_name from testmaster where category_id='1' order by type_name");
					while($dep=mysqli_fetch_array($dept))
					{
						echo "<option value='$dep[type_id]'>$dep[type_name]</option>";
					}
					?>
				</select>
				<?php
				if($glob_patient_type==0)
				{
					$pat_typ="display:none";
				}
				?>
				<select id="pat_type" style="<?php echo $pat_typ;?>">
					<option value='1'>OPD</option>
					<option value='2'>IPD</option>
				</select>
			</td>
			
			<td>
				<button class="btn btn-search" onclick="load_worksheet()"><i class="icon-search"></i> Search</button>
			</td>
		</tr>
	</table>
	
	<div id="sheet_data"></div>
</div>

<div id="loader" style="position:fixed;top:50%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>

<script src="include/jquery.ui.timepicker.js"></script><!-- Timepicker js -->
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="include/jquery.ui.timepicker.css" /><!-- Timepicker css -->

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
		$(".timepicker").timepicker({minutes: {starts: 0,interval: 05,showSecond: true,showMillisec: true,}});
		
		load_worksheet()
	});
	
	function load_worksheet()
	{
		$.post("pages/worksheet_new_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			ftime:$("#ftime").val(),
			ttime:$("#ttime").val(),
			dept:$("#dept").val(),
			pat_type:$("#pat_type").val(),
			type:1
		},
		function(data,status)
		{
			$("#sheet_data").html(data);
		})
	}
	
	function worklist_print(uhid,opd_id,ipd_id,batch_no)
	{
		var fdate=$("#fdate").val();
		var tdate=$("#tdate").val();
		var ftime=$("#ftime").val();
		var ttime=$("#ttime").val();
		var dept=$("#dept").val();
		var pat_type=$("#pat_type").val();
		
		url="pages/worksheet_print.php?type=0&uhid="+uhid+"&opd="+opd_id+"&ipd="+ipd_id+"&batch="+batch_no+"&fdate="+fdate+"&tdate="+tdate+"&ftime="+ftime+"&ttime="+ttime+"&dept="+dept+"&pat_type="+pat_type;
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
.test_tab { font-size:8px;}
.test_tab hr { margin: 2px 0;}
.td_date { background-color:#CCC !important; font-weight:bold;}
#print_but{position:fixed;right:-5px;}
@media print
{
	#header ,#search,.modal,#sidebar,#search_test,#search_data,#user-nav,#print_but,#footer_but {display:none;} 
	.table tr td,.table tr th{ font-size:10px; padding: 0px; line-height: 15px;}
	.head{font-size:12px;}
	#search_data{ margin-left:-120px;}
	#print_header{display:block;}
	#work_tab td { font-size:8px;}
	#work_tab{ margin-left:-50px;width:100%}
	
}
@page
{
	margin:0.5cm;
}

.tst_green{ background:#5bb75b;display:inline-block;color:white;padding-left:5px;padding-right:5px; }
.tst_gray{ background:#757575;display:inline-block;color:white;padding-left:5px;padding-right:5px; }
</style>
