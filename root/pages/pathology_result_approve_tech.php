<?php
if($glob_patient_type==0)
{
	$pat_typ="display:none";
}
?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div id="search_div">
		<table class="table table-bordered table-condensed text-center">
			<tr>
				<td colspan="5">
					<center>
						<span class="side_name">From</span>
						<input class="form-control datepicker span2" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 47px;">
						<span class="side_name">To</span>
						<input class="form-control datepicker span2" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 26px;">
						
						<select id="pat_type" name="" style="<?php echo $pat_typ;?>" class="span2">
							<option value="0">--All(TYPE)--</option>
							<option value="opd_id">OPD</option>
							<option value="ipd_id">IPD</option>
						</select>
						
						<select id="search_dept_id" name="search_dept_id" onchange="load_dep_test()" class="span2">
							<option value="0">--All(DEP)--</option>
						<?php
							//$dep=mysqli_query($link,"select distinct type_id from testmaster where category_id='1' order by type_id");
							$dept=mysqli_query($link,"SELECT DISTINCT a.`id` AS `dept_id`,a.`name` AS `type_name` FROM `test_department` a, `testmaster` b WHERE a.`category_id`=b.`category_id` AND a.`id`=b.`type_id` AND a.`category_id`=1 AND a.`id` NOT IN($non_reporting_test_dept_id) ORDER BY a.name ASC");
							while($dep=mysqli_fetch_array($dept))
							{
								echo "<option value='$dep[dept_id]'>$dep[type_name]</option>";
							}
						?>
						</select>
						<span id="dep_tst_lst">
							<select id="search_testid" name="search_testid" class="span2">
								<option value="0">--All(Test)--</option>
							<?php
								$test=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='1' ORDER BY `testname` ASC");
								while($tst=mysqli_fetch_array($test))
								{
									echo "<option value='$tst[testid]'>$tst[testname]</option>";
								}
							?>
							</select>
							<select id="approve_status" name="approve_status" class="span2">
								<option value="0">--Select(Validate)--</option>
								<option value="1">Validated</option>
								<option value="2">Not Validated</option>
							</select>
						</span>
						<button id="search_btn" value="Search" class="btn btn-search" onclick="$('#search_check').val('1');load_pat_ser(0);" style="margin-bottom: 10px;"><i class="icon-search"></i> Search</button>
						<input type="hidden" id="search_check" value="0"/>
					</center>
				</td>
			</tr>
					
			<tr>
				<td  style="text-align:center;font-weight:bold">
					<!--Cash Memo No.--> <br/> <input type="text" id="bill_no" style="<?php echo $pat_typ;?>" onkeyup="load_pat_event(event)"/>
					<select class="span2" id="patType" onchange="load_pat_ser(0)">
						<option value="0">Select All</option>
						<option value="OPD">OPD</option>
						<option value="IPD">IPD</option>
					</select>
				</td>
				<td  style="text-align:center;font-weight:bold">
					Hospital No. <br/> <input type="text" id="uhid" onkeyup="load_pat_event(event)"/> 
				</td>
				<td  style="text-align:center;font-weight:bold;<?php echo $bar_sty;?>">
					Barcode ID <br/> <input type="text" id="barcode_id" list="bar_list" onkeyup="load_pat_event(event)"/>
					<datalist id="bar_list">
					<?php
						$date=date('Y-m-d');
						$date1 = strtotime(date("Y-m-d", strtotime($date)) . " -3 days");
						$date_five=date("Y-m-d",$date1);
						//$barc=mysqli_query($link,"select distinct opd_id from test_sample_result where date between '$date1' and '$date'");
						while($bar=mysqli_fetch_array($barc))
						{
							echo "<option>$bar[opd_id]</option>";
						}
					?>
					</datalist>
				</td>
				<td  style="text-align:center;font-weight:bold">
					<br>
					<select id="dept_serial" class="span1" style="<?php echo $pat_typ;?>" onchange="load_pat_ser(1)">
						<option value="O">O</option>
						<option value="I">I</option>
						<option value="E">E</option>
					</select>
					<input type="text" id="dept_serial_no" class="span1" style="<?php echo $pat_typ;?>" onkeyup="dept_serial_no_up(event)">
				</td>
				<td  style="text-align:center;font-weight:bold">
					Name <br/> 
					<input type="text" placeholder="" id="name" onkeyup="load_pat_event(event)">
				</td>
			<?php
				$bar_sty='display:none';
				if($glob_barcode==1)
				{
					$bar_sty='display:block';
				}
			?>
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
	</div>
	<div id="pat_list" class="ScrollStyleY">
		
	</div>
	<div id="load_data" class="">
		
	</div>
</div>

<div class="text-center" style="position: fixed; bottom: -20px; right: 0px; padding: 10px; background: #fff; color: #000; box-shadow: 0 0 10px rgba(0,0,0,0.5); z-index:100;">
	<table class="table table-bordered table-condensed">
		<tr>
			<td class=""><span class="btn_round_msg red"></span>  No Data</td>
			<td class=""><span class="btn_round_msg yellow"></span> Not Approve</td>
			<td class=""><span class="btn_round_msg blue"></span> Partially Approve</td>
			<td class=""><span class="btn_round_msg green"></span> Approve</td>
		</tr>
	</table>
</div>

<input type="hidden" id="list_start" value="50">

<input type="hidden" id="need_refresh_patient_id" value="0">
<input type="hidden" id="need_refresh_opd_id" value="0">
<input type="hidden" id="need_refresh_ipd_id" value="0">
<input type="hidden" id="need_refresh_batch_no" value="0">

<button id="btn_modal_note" type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" style="display:none;">Open Modal Note</button>
<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<!--<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Modal Header</h4>
			</div>-->
			<div class="modal-body">
				<div id="load_data_note"></div>
			</div>
			<div class="modal-footer" style="display:none;">
				<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-lg" id="modal_btn_repeat" data-toggle="modal" data-target="#myModal_repeat" style="display:none;">Repeat Param</button>
<div id="myModal_repeat" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<!--<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Modal Header</h4>
			</div>-->
			<div class="modal-body">
				<div id="result_repeat"></div>
			</div>
			<div class="modal-footer" style="text-align:center;">
				<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<div id="loader" style="margin-top:-10%;"></div>

<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>

<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />

<script src="../js/jquery.dataTables.min_all.js"></script>
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
		
		$("#bill_no").val("").focus();
		
		$("#search_dept_id").val(getCookie("tech_approve_dept_id"));
		
		setTimeout(function(){
			load_pat_ser(2);
		},100);
		
		$('#pat_list').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);
			
			if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start=$("#list_start").val().trim();
				list_start=parseInt(list_start)+50;
				$("#list_start").val(list_start);
				load_pat_ser(3);
			}
		});
	});
	
	let timeLeft = 60;
	let countdownInterval;
	
	function startCountdown() {
		clearInterval(countdownInterval); // Clear existing interval to prevent multiple timers
		countdownInterval = setInterval(() => {
			$("#auto_refresh_div").html("<a class='btn btn-link' id='resetBtn' onclick='resetCountdown()' style='font-size:10px;'>Click to reset Auto-Refresh("+timeLeft+")</a>");
			if (timeLeft === 0) {
				if($("#pat_test_data :visible").length==0)
				{
					auto_refresh();
				}
				
				timeLeft = 60; // Reset countdown after refresh
			} else {
				if(timeLeft <=10)
				{
					$("#resetBtn").css({"color":"red"});
				}
				timeLeft--;
			}
		}, 1000);
	}
	
	function auto_refresh() {
		$("#auto_refresh_div").html("Refreshing...");
		load_pat_ser(4);
	}
	
	function resetCountdown() {
		timeLeft = 60;
		$("#auto_refresh_div").html("<a class='btn btn-link' id='resetBtn' onclick='resetCountdown()' style='font-size:10px;'>Click to reset Auto-Refresh("+timeLeft+")</a>");
		startCountdown(); // Restart countdown on reset
	}
	startCountdown(); // Start countdown on page load
	
	function hid_div(e)
	{
		if($("#approve_val").val()==1) // Approve data sent to server
		{
			return false;
		}
		if(e.which==13 && e.ctrlKey)
		{
			if($("#pat_test_data").is(":visible"))
			{
				if($(".modal.in :visible").length==0)
				{
					approve_data();
				}
			}
		}
		if(e.which==13)
		{
			var id = document.activeElement.id;
			var slno=$("#"+id).attr("slno");
			var next_slno=parseInt(slno)+1;
			
			if($("#each_test_param_result_save"+next_slno).is(":visible"))
			{
				$("#each_test_param_result_save"+next_slno).focus();
			}else
			{
				$('input[name="t_par'+next_slno+'"]').focus();
			}
		}
		if(e.which==27)
		{
			if($(".modal.in :visible").length==0)
			{
				back_to_list();
			}else
			{
				$(".bootbox").modal("hide");
				$(".modal").modal("hide");
			}
		}
	}
	function back_to_list()
	{
		if($("#search_btn:visible").length==0)
		{
			sendUpdates();
		}
		$("#load_data").slideUp(400);
		$("#search_div").slideDown(600);
		$("#pat_list").slideDown(800);
		$(".bootbox").modal("hide");
		$(".modal").modal("hide");
		
		$("#bill_no").val("").focus();
		
		//load_pat_ser(5);
		
		if($("#need_refresh_patient_id")!="0" && $("#need_refresh_opd_id")!="0" && $("#need_refresh_ipd_id")!="0" && $("#need_refresh_batch_no")!="0")
		{
			refresh_pat_dept_color($("#need_refresh_patient_id").val(),$("#need_refresh_opd_id").val(),$("#need_refresh_ipd_id").val(),$("#need_refresh_batch_no").val());
		}
		
		/*$({myScrollTop:window.pageYOffset}).animate({myScrollTop:0}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});*/
	}
	
	function refresh_pat_dept_color(uhid,opd_id,ipd_id,batch_no)
	{
		$("#loader").show();
		$.post("pages/pathology_result_approve_tech_data.php",
		{
			type:"refresh_pat_dept_color",
			uhid:uhid,
			opd_id:opd_id,
			ipd_id:ipd_id,
			batch_no:batch_no,
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			
			var res=JSON.parse(data);
			
			$.each(res, function(index, dept) {
				var deptId = dept.dept_id;
				var btn_cls = dept.btn_cls;
				var bill_id = dept.bill_id;
				
				$("#btn_"+bill_id+"_"+deptId).removeClass("btn-warning");
				$("#btn_"+bill_id+"_"+deptId).removeClass("btn-danger");
				$("#btn_"+bill_id+"_"+deptId).removeClass("btn-primary");
				$("#btn_"+bill_id+"_"+deptId).removeClass("btn-success");
				$("#btn_"+bill_id+"_"+deptId).removeClass("btn-default");
				
				$("#btn_"+bill_id+"_"+deptId).addClass(btn_cls);
			});
			
			$("#need_refresh_patient_id").val("0");
			$("#need_refresh_opd_id").val("0");
			$("#need_refresh_ipd_id").val("0");
			$("#need_refresh_batch_no").val("0");
		})
	}
	
	function dept_serial_no_up(e)
	{
		if(e.which==13)
		{
			load_pat_ser(6);
		}
	}
	
	function load_pat_ser(val)
	{
		if(val==0)
		{
			$("#list_start").val(50);
		}
		
		$("#loader").show();
		$.post("pages/pathology_result_approve_tech_data.php",
		{
			type:"load_pat_list",
			pat_type:$("#pat_type").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			dept_id:$("#search_dept_id").val(),
			testid:$("#search_testid").val(),
			approve_status:$("#approve_status").val(),
			name:$("#name").val().trim(),
			bill_no:$("#bill_no").val().trim(),
			uhid:$("#uhid").val().trim(),
			barcode_id:$("#barcode_id").val().trim(),
			list_start:$("#list_start").val(),
			dept_serial:$("#dept_serial").val(),
			dept_serial_no:$("#dept_serial_no").val().trim(),
			patType:$("#patType").val()
		},
		function(data,status)
		{
			$("#load_data").slideUp(400);
			$("#pat_list").slideDown(600);
			
			$("#loader").hide();
			$("#pat_list").html(data);
			$("#ser_type").val("1");
			$("#search_check").val("0");
			
			resetCountdown();
		})
	}
	function load_pat_event(e)
	{
		if(e.which==13)
		{
			$("#list_start").val(50);
			load_pat_ser(7);
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
				$.post("pages/pathology_result_approve_tech_data.php",
				{
					type:"load_pat_list",
					pat_type:$("#pat_type").val(),
					fdate:$("#fdate").val(),
					tdate:$("#tdate").val(),
					dept_id:$("#search_dept_id").val(),
					testid:$("#search_testid").val(),
					name:$("#name").val().trim(),
					bill_no:$("#bill_no").val().trim(),
					uhid:$("#uhid").val().trim(),
					barcode_id:$("#barcode_id").val().trim(),
				},
				function(data,status)
				{
					$("#load_data").slideUp(400);
					$("#pat_list").slideDown(600);
					
					$("#pat_list").html(data);
					$("#ser_type").val("2");
					$("#check_display").val("1");
				})
			}
			else
			{
				/*var dept_id=$("#search_dept_id").val();
				$("#dept_id_"+dept_id+"").click();
				$("#check_display").val("1");*/
			}
		}
		else
		{
			$("#check_display").val("0");
		}
	}
	
	function load_pat_dept_tests(val,dept_id) // New
	{
		$("#loader").show();
		$.post("pages/pathology_result_approve_tech_data.php",
		{
			type:"load_pat_dept_tests",
			uhid:$("#pid_"+val+"").val(),
			opd_id:$("#opd_"+val+"").val(),
			ipd_id:$("#ipd_"+val+"").val(),
			batch_no:$("#batch_"+val+"").val(),
			dept_id:dept_id,
			fdoc:$("#for_doc").val(),
			search_dept_id:$("#search_dept_id").val(),
			search_testid:$("#search_testid").val(),
			barcode_id:$("#barcode_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#search_div").slideUp(200);
			$("#pat_list").slideUp(400);
			$("#load_data").slideDown(600).html(data);
			
			setTimeout(function(){
				$("#pat_dept_test_params input:visible:first").focus();
			},1000);
			
			$("#need_refresh_patient_id").val($("#pid_"+val+"").val());
			$("#need_refresh_opd_id").val($("#opd_"+val+"").val());
			$("#need_refresh_ipd_id").val($("#ipd_"+val+"").val());
			$("#need_refresh_batch_no").val($("#batch_"+val+"").val());
			
			/*$({myScrollTop:window.pageYOffset}).animate({myScrollTop:0}, {
				duration: 1000,
				easing: 'swing',
				step: function(val){
					window.scrollTo(0, val);
				}
			});*/
		})
	}
	function load_pat_dept_tests_refresh(dept_id)
	{
		$("#loader").show();
		$.post("pages/pathology_result_approve_tech_data.php",
		{
			type:"load_pat_dept_tests",
			uhid:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			ipd_id:$("#ipd_id").val(),
			batch_no:$("#batch_no").val(),
			dept_id:dept_id,
			fdoc:$("#for_doc").val(),
			search_dept_id:$("#search_dept_id").val(),
			search_testid:$("#search_testid").val(),
			barcode_id:$("#barcode_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_data").slideDown(600).html(data);
			
			setTimeout(function(){
				$("#pat_dept_test_params input:visible:first").focus();
			},500);
		})
	}
	function load_pat_dep(val,dept_id) // Old
	{
		var pid=$("#pid_"+val+"").val();
		var opd_id=$("#opd_"+val+"").val();
		var ipd_id=$("#ipd_"+val+"").val();
		var batch_no=$("#batch_"+val+"").val();
		
		var dept_id=dept_id;
		var user=$("#user").text().trim();
		var fdoc=$("#for_doc").val();
		
		var url="pages/technician_approve_test.php?uhid="+pid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch="+batch_no+"&dept_id="+dept_id+"&user="+user+"&fdoc="+fdoc;
		
		window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
	}
	function load_dep_test()
	{
		setCookie("tech_approve_dept_id",$("#search_dept_id").val(),300); // Assign cookie
		
		$.post("pages/pathology_result_approve_tech_data.php",
		{
			type:"load_dept_tests",
			dept_id:$("#search_dept_id").val(),
		},
		function(data,status)
		{
			$("#search_testid").html(data);
		})
	}
	/*
	setInterval(function(){
		if($("#pat_test_data :visible").length==0)
		{
			$("#search_btn").click();
		}
	},30000); // Every Minute Re-Fresh
	*/
	function alertmsg(msg, n)
	{
		$.gritter.add({
			//title:	'Normal notification',
			text: '<h5 style="text-align:center;">' + msg + '</h5>',
			time: 1000,
			sticky: false
		});
		if (n > 0) {
			$(".gritter-item").css("background", "#237438");
		}
	}
	
	function pat_phone_display()
	{
		$("#pat_phone_display").toggle(500);
	}
	
	
	let socket;

	function connectWebSocket() {
		socket = new WebSocket('ws://192.168.1.110:8880');

		socket.addEventListener('open', () => {
			console.log("WebSocket connection established.");
			load_pat_ser(8); // Send data when the connection opens
		});
		
		socket.addEventListener('message', (event) => {
			console.log("Message received:", event.data);
			if($("#search_btn:visible").length>0)
			{
				load_pat_ser(9); // Call load_data() when a message is received
			}
		});
		
		socket.addEventListener('close', () => {
			console.warn("WebSocket closed. Attempting to reconnect...");
			setTimeout(connectWebSocket, 3000); // Reconnect after 3 seconds
		});

		socket.addEventListener('error', (err) => {
			console.error("WebSocket error:", err);
		});
	}
	
	function sendUpdates()
	{
		var currentdate = new Date(); 
		var datetime = "Last Sync: " + currentdate.getDate() + "/" + (currentdate.getMonth() + 1) + "/" + currentdate.getFullYear() + " @ " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
		
		let messageData = { sender: $("#user").text().trim(), message: datetime };

		// Only send if socket is open
		if (socket.readyState === WebSocket.OPEN) {
			socket.send(JSON.stringify(messageData));
			console.log("Message Send.");
		} else {
			console.warn("WebSocket is not open. Cannot send data.");
		}
	}

	// Start WebSocket connection
	connectWebSocket();
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

.side_name
{
	border: 1px solid #ddd;
	background-color: #fff;
	padding: 4px;
	position: absolute;
	font-weight:bold;
}

#myModal
{
	display:none;
	left: 35%;
	width:50%;
}
.modal.fade.in
{
	top: 0%;
}
.modal-body
{
	max-height: 640px;
}

#myModal_repeat
{
	left: 20%;
	width: 95%;
}

.widget-content{
	width: 99%;
}

#gritter-notice-wrapper
{
	//top: 45% !important;
	//right: 44% !important;
	z-index: 999999 !important;
}
small {
	font-size: 60% !important;
}

tr.printed
{
	background: #CEFFC8;
}

.btn_round_msg
{
	color:#000;
	padding:2px;
	border-radius: 7em;
	padding-right:10px;
	padding-left:10px;
	box-shadow: inset 1px 1px 0 rgba(0,0,0,0.6);
	transition: all ease-in-out 0.2s;
}
.red
{
	background-color: #da4f49;
}
.green
{
	background-color: #51a351;
}
.yellow
{
	background-color: #f89406;
}
.blue
{
	background-color: #04c;
}
.repeaters
{
	-webkit-animation: spin 2s linear infinite;
	animation: spin 2s linear infinite;
}
.noPrintPar
{
	display: inline-block;
	width: 200px;
	font-size:11px;
}
.hemolysis
{
	border: 1px solid #000;
	border-radius: 50px;
	color: #FF0000;
}
.icterus
{
	border: 1px solid #000;
	border-radius: 50px;
	color: #FFFF00;
}
.turbidity
{
	border: 1px solid #000;
	border-radius: 50px;
	color: #7F7F7F;
}
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
