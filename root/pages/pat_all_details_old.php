<?php
$uhid=base64_decode($_GET["uhid"]);
$uhid=(int)$uhid;
//$opd=base64_decode($_GET["opd"]);
//$opd=(int)$opd;

$pat_opd=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `opd_id` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' order by`slno` DESC limit 0,1 "));
$opd=$pat_opd["opd_id"];
$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"])." (".$pat_info["dob"].")"; }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }
if($pat_info["blood_group"]==""){ $blood_group="N/A"; }else{ $blood_group=$pat_info["blood_group"]; }
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Patient Details</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<p class="p_header">Visit Information</p>
	<span class="uhid_dis"><b>UHID</b>: <text style="font-size:18px;"><?php echo $uhid; ?></text></span>
	<table class="table" style="background: snow">
		<tr>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Blood Group</th>
			<th>Phone</th>
			<th>Address</th>
		</tr>
		<tr>
			<td><?php echo $pat_info["name"]; ?></td>
			<td><?php echo $age; ?></td>
			<td><?php echo $pat_info["sex"]; ?></td>
			<td><?php echo $blood_group; ?></td>
			<td><?php echo $pat_info["phone"]; ?></td>
			<td><?php echo $pat_info["address"]; ?></td>
		</tr>
	</table>
	<div class="widget-box">
		<div class="widget-title">
			<ul class="nav nav-tabs">
				<li class="active" onClick="check_appointment()"><a data-toggle="tab" href="#tab1">Consultation</a></li>
				<li class=""><a data-toggle="tab" href="#tab2">Investigation</a></li>
				<li class=""><a data-toggle="tab" href="#tab3">Tab3</a></li>
			</ul>
		</div>
		<div class="widget-content tab-content">
			<div id="tab1" class="tab-pane active">
				<div id="default" style="display:none;">
					<table class="table custom_table">
						<tr>
							<th class="span3">Select Department</th>
							<td>
								<select id="dept_id" onChange="dept_sel()">
									<option value="0">Select</option>
								<?php
								$dept_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `department` order by `dept_id` ");
								while($dept=mysqli_fetch_array($dept_qry))
								{
									echo "<option value='$dept[dept_id]'>$dept[name]</option>";
								}
								?>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div id="load_doc">
					
				</div>
				<div id="load_all">
					
				</div>
				<div id="print_div">
					
				</div>
			</div>
			<div id="tab2" class="tab-pane">
				<p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.</p>
			</div>
			<div id="tab3" class="tab-pane">
				<p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p>
			</div>
		</div>
	</div>
</div>
<span style="display:none;" id="uhid"><?php echo $uhid; ?></span>
<span style="display:none;" id="opd_id"><?php echo $opd; ?></span>
<script>
/*$( "body" ).on( "keydown", function( event ) {
	$( "#print_div" ).html( event.type + ": " +  event.which );
});*/
$(document).ready(function(){
	
	check_appointment();
});
function new_appointment()
{
	$("#opd_id").text("00000");
	 setTimeout(function(){
		 $("#print_div").insertBefore( "#default" );
		 //$("#print_div").hide();
		 $("html, body").animate({ scrollTop: 350 });
		 check_appointment();
	 }, 10);
}
function check_appointment()
{
	$.post("pages/global_load.php",
	{
		type:"check_appointment_already",
		uhid:$("#uhid").text(),
		opd_id:$("#opd_id").text(),
	},
	function(data,status)
	{
		if(data==0)
		{
			$("#default").fadeIn();
			$("#dept_id").focus().val("0");
		}else
		{
			//show_print_div();
			show_print_details($("#uhid").text());
		}
	})
}
function dept_sel()
{
	$.post("pages/global_load.php",
	{
		type:"load_dept_doc",
		dept_id:$("#dept_id").val(),
	},
	function(data,status)
	{
		$("#load_doc").hide().html(data).fadeIn('slow');
		$("#load_all").html("");
		$("#con_doc_id").focus();
		$("html, body").animate({ scrollTop: 200 });
	})
}
function doc_sel()
{
	$.post("pages/global_load.php",
	{
		type:"load_con_doc_avail_dates",
		con_doc_id:$("#con_doc_id").val(),
	},
	function(data,status)
	{
		$("#load_all").hide().html(data).fadeIn('slow');
		load_time_from_date($("#c_day").val(),$("#c_date").val());
		//$("html, body").animate({ scrollTop: 600 });
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:800}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	})
}
function load_time_from_date(day,dte)
{
	$.post("pages/global_load.php",
	{
		type:"load_con_doc_avail_times",
		uhid:$("#uhid").text(),
		opd_id:$("#opd_id").text(),
		con_doc_id:$("#con_doc_id").val(),
		day:day,
		dte:dte,
	},
	function(data,status)
	{
		$("#tab11").html(data);
		 setTimeout(function(){
			 load_time_from_date($("#ap_day").text(),$("#ap_date").text());
		 }, 10000);
	})
}
function book_appointment(id,n,i)
{
	var ap_no=$("#appoint_no").text();
	if(ap_no=="")
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to appoint</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-info",
					callback: function() {
						$("#appoint_no").text(n+'@'+i);
						$("#"+id).css({'background-color': '#df8505'});
						save_consult();
					}
				}
			}
		});
	}else if(ap_no==n+"@"+i)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Cancel appointment</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-info",
					callback: function() {
						$.post("pages/global_insert_data.php",
						{
							type:"cancel_con_doc_appointment",
							uhid:$("#uhid").text(),
							opd_id:$("#opd_id").text(),
							con_doc_id:$("#con_doc_id").val(),
							ap_date:$("#ap_date").text(),
						},
						function(data,status)
						{
							$("#appoint_no").text("");
							$("#"+id).css({'background-color': '#5bb75b'});
							//alert("Cancelled");
							bootbox.alert({
								message: "Appointment has been cancelled",
								size: 'small'
							});
						})
					}
				}
			}
		});
	}else
	{
		bootbox.alert({
			message: "Already appointed",
			size: 'small'
		});
	}
}
function save_consult()
{
	var apnt_val=$("#appoint_no").text();
	if(apnt_val!="")
	{
		var uhid=$("#uhid").text();
		var opd_id=$("#opd_id").text();
		var con_doc_id=$("#con_doc_id").val();
		var ap_date=$("#ap_date").text();
		var ap_day=$("#ap_day").text();
		var appoint_no=$("#appoint_no").text();
		
		$.post("pages/global_insert_data.php",
		{
			type:"insert_con_doc_appointment",
			uhid:uhid,
			opd_id:opd_id,
			con_doc_id:con_doc_id,
			ap_date:ap_date,
			ap_day:ap_day,
			appoint_no:appoint_no,
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			//alert("Saved");
			bootbox.dialog({ message: "Saved"});
			setTimeout(function(){
				bootbox.hideAll();
				show_print_details(uhid);
				//show_print_details(uhid,opd_id,con_doc_id,ap_date,ap_day,appoint_no);
			 }, 1000);
		})
	}else
	{
		bootbox.alert({
			message: "Appointment is not given",
			size: 'small'
		});
		//alert("Appointment is not given");
	}
}
function show_print_details(uhid)
{
	$.post("pages/global_load.php",
	{
		type:"show_print_details",
		uhid:uhid,
	},
	function(data,status)
	{
		$("#default").fadeOut();
		$("#load_doc").html("");
		$("#load_all").html("");
		$("#print_div").html(data);
	})
}
function print_receipt(url)
{
	window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
}
function show_tr_btn(opd)
{
	$(".hidden_tr").fadeOut();
	$(".iconp").show();
	$(".iconm").hide();
	if($('#'+opd+':visible').length)
	{
		$("#"+opd).hide();
		$("#plus_sign"+opd).show();
		$("#minus_sign"+opd).hide();
	}else{
		$("#"+opd).show();
		$("#plus_sign"+opd).hide();
		$("#minus_sign"+opd).show();
	}
}
$('.helpover').popover({ trigger: "hover" });
</script>
<style>
.p_header
{
	font-size: 16px;
	background-color: #e9f3ff;
	padding: 10px;
	font-weight: 600;
}
.uhid_dis
{
	float: right;
	padding: 10px;
	background-color: white;
	//border-radius: 10px;
	margin-bottom:5px;
}
.custom_table
{
	margin-bottom:0px;
	//background: snow;
}
.popover
{
	color:#000;
}
</style>
