<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span> </div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center table-condensed">
		<tr>
			<td>
				<center>
					<span class="side_name">Name</span>
					<input type="text" class="span2" id="pat_name" style="margin-left: 52px;">
					<span class="side_name">UHID</span>
					<input type="text" class="span2" id="uhid" style="margin-left: 47px;">
					<span class="side_name">From</span>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly style="margin-left: 46px;">
					<span class="side_name">To</span>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly style="margin-left: 27px;">
					<button class="btn btn-success" onClick="load_appointed_patients()" style="margin-top: -1%;" >View</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_data" class="ScrollStyle"></div>
	
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$("document").ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			yearRange: "-100:+100",
		});
		load_appointed_patients();
	});
	function load_appointed_patients()
	{
		$.post("pages/opd_advnc_book_ajax.php",
		{
			type:"load_appointed_patients",
			date1:$("#from").val(),
			date2:$("#to").val(),
			pat_name:$("#pat_name").val(),
			pat_uhid:$("#uhid").val(),
			monitor_id:$("#monitor_id").val(),
		},
		function(data,status)
		{
			$("#load_data").slideUp(500,function(){ $("#load_data").html(data).slideDown(1000); });
		})
	}
	function load_monitor(val)
	{
		$("#monitor_id").val(val);
		load_appointed_patients();
	}
	function status_change(uhid,bid)
	{
		var status=$("#status"+bid).val();
		
		var msg="";
		if(status==0)
		{
			msg="Are you sure want to chnage to pending ?";
		}
		if(status==1)
		{
			msg="Are you sure want to Process ?";
		}
		if(status==2)
		{
			msg="Are you sure want to cancel appointment ?";
		}
		if(status==3)
		{
			msg="Are you sure want to reschedule appointment ?";
		}
		
		bootbox.dialog({
			message: "<h5>"+msg+"</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					  load_appointed_patients();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						if(status==1) // Process
						{
							window.location="?param="+btoa(81)+"&uhid="+btoa(uhid)+"&bid="+btoa(bid);
						}
						if(status==3) // Reschedule
						{
							window.location="?param="+btoa(76)+"&uhid="+btoa(uhid)+"&bid="+btoa(bid);
						}
						else
						{
							$.post("pages/opd_advnc_book_ajax.php",
							{
								type:"status_change",
								uhid:uhid,
								bid:bid,
								status:status,
								user:$("#user").text().trim(),
							},
							function(data,status)
							{
								//alert(data);
								var res=data.split("@");
								bootbox.dialog({ message: res[1]});
								setTimeout(function(){
									bootbox.hideAll();
									load_appointed_patients();
								},2000);
							})
						}
					}
				}
			}
		});
	}
	
	
	
	
	
	
	function save_and_book(patient_id,uhid)
	{
		$.post("pages/opd_advnc_book_ajax.php",
		{
			type:"save_adv_book_pat",
			patient_id:patient_id,
			uhid:uhid,
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			redirect_page(data,'0');
		})
	}
	function redirect_page(uhid,opd_id)
	{
		bootbox.dialog({ message: "<b>Redirecting to Patient Dashboard</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		setTimeout(function(){
			window.location="processing.php?param=3&uhid="+uhid+"&consult=1&opd="+opd_id+"&adv=1";
		 }, 2000);
	}
</script>
<style>
.side_name
{
	border: 1px solid #ddd;
	background-color: #fff;
	padding: 4px;
	position: absolute;
	font-weight:bold;
}
.btn-monitor
{
	width: 24%;
}
</style>
