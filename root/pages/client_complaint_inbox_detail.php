<?php
include("../../includes/global.function.php");
include("../includes/connection_online_sync.php");

$client_id=base64_decode($_GET['clnt']);
$complaint_id=base64_decode($_GET['cmplnt']);

if($link2_error=="")
{

// Online Synchronize
	// replier=0 means company's reply
	$reply_qry=mysqli_query($link2," SELECT * FROM `client_complaint_replies` WHERE `client_id`='$client_id' AND `complaint_id`='$complaint_id' AND `replier`='0' ");
	while($reply_val=mysqli_fetch_array($reply_qry))
	{
		$company_reply_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `client_complaint_replies` WHERE `rel_slno`='$reply_val[slno]' "));
		if($company_reply_num=='0')
		{
			$reply_text= str_replace("'", "''", $reply_val['reply_text']);
			
			mysqli_query($link," INSERT INTO `client_complaint_replies`(`complaint_id`, `reply_text`, `user`, `date`, `time`, `replier`, `rel_slno`) VALUES ('$reply_val[complaint_id]','$reply_text','$reply_val[user]','$reply_val[date]','$reply_val[time]','0','$reply_val[slno]') ");
			
		}
	}
	// closer=0 means closed by company
	$close_qry=mysqli_query($link2," SELECT * FROM `client_complaint_close` WHERE `client_id`='$client_id' AND `complaint_id`='$complaint_id' AND `closer`='0' ");
	while($close_val=mysqli_fetch_array($close_qry))
	{
		$company_close_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `client_complaint_close` WHERE `rel_slno`='$close_val[slno]' "));
		if($company_close_num=='0')
		{
			mysqli_query($link," INSERT INTO `client_complaint_close`(`complaint_id`, `user`, `date`, `time`, `type`, `rel_slno`, `closer`) VALUES ('$close_val[complaint_id]','$close_val[user]','$close_val[date]','$close_val[time]','$close_val[val]','$close_val[slno]','$close_val[closer]') ");
			
		}
	}
	
	$complaint_status=mysqli_fetch_array(mysqli_query($link2," SELECT `status` FROM `client_complaint_master` WHERE `client_id`='$client_id' AND `complaint_id`='$complaint_id' "));
	mysqli_query($link," UPDATE `client_complaint_master` SET `status`='$complaint_status[status]' WHERE `complaint_id`='$complaint_id' ");
	
}
	
$complaints_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `client_complaint_master` WHERE `complaint_id`='$complaint_id' "));
$user_name=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$complaints_val[user]' "));
$client_name=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `company_name` WHERE `client_id`='$client_id' "));

$complaint_text=str_replace("\n","<br>", $complaints_val['complaint_text']);

if($complaints_val['status']=='0')
{
	$dis_none="";
	$re_dis_none="style='display:none;'";
	$close_text="";
	
}else
{
	$dis_none="style='display:none;'";
	$re_dis_none="";
	
	$close_val=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `client_complaint_close` WHERE `complaint_id`='$complaint_id' ORDER BY `slno` DESC LIMIT 0,1 "));
	$user_name=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$close_val[user]' "));
	$closer_name=$user_name['name'];
	if($close_val['closer']=='0')
	{
		$closer_name="E-adware";
	}
	
	$close_text="<h5 id='closed_text_id'>Closed on ".convert_date($close_val['date'])." ".convert_time($close_val['time'])."(".$closer_name.")</h5>";
}

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Inbox</span></div>
</div>
<!--End-header-->
<div id="loader" style="margin-top:-10%;"></div>
<div class="container-fluid" style="max-height:500px;overflow-y:scroll;">
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Token ID</th>
			<td><?php echo $complaint_id; ?></td>
			<th>User</th>
			<td><?php echo $user_name['name']; ?></td>
			<th>Date Time</th>
			<td><?php echo convert_date($complaints_val['date']); ?> <?php echo convert_time($complaints_val['time']); ?></td>
		</tr>
		<tr>
			<th>Tilte</th>
			<td colspan="5"><?php echo $complaints_val['complaint_title']; ?></td>
		</tr>
		<tr>
			<th style="width: 12%;">Description</th>
			<td colspan="5" style="text-align: justify;"><?php echo $complaint_text; ?></td>
		</tr>
	</table>
	<div id="all_conversation" class="span12">
		<?php
			$all_convers_qry=mysqli_query($link, " SELECT * FROM `client_complaint_replies` WHERE `complaint_id`='$complaint_id' ");
			while($all_convers=mysqli_fetch_array($all_convers_qry))
			{
				$pid="client";
				if($all_convers['replier']=='0')
				{
					$pid="company";
					
					$replier_name="E-adware";
				}else
				{
					$user_name=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$all_convers[user]' "));
					$replier_name=$client_name['name']." (".$user_name['name'].")";
				}
				$date_time_text=" on ".convert_date($all_convers['date'])." ".convert_time($all_convers['time']);
				$header_text=$replier_name.$date_time_text;
				
				$conversation_text= str_replace("\n", "<br>", $all_convers['reply_text']);
				
			?>
			<div id="each_conversation" class="span12" style="padding: 0;">
				<p id="<?php echo $pid; ?>">
					<span><b><?php echo $header_text; ?></b></span><br>
					<span id="<?php echo $pid; ?>span">
						<?php echo $conversation_text; ?>
					</span>
				</p>
			</div>
			<?php
			}
		?>
	</div>
	<div id="conversation" class="">
		<input type="hidden" value="<?php echo $client_id; ?>" id="client_id">
		<input type="hidden" value="<?php echo $complaint_id; ?>" id="complaint_id">
		<textarea class="form-control" id="conversation_text" rows="7" onkeyup="complaint_text_up(this,event)" <?php echo $dis_none; ?>></textarea><br>
		<button class="btn btn-info" onClick="save_conversation()" <?php echo $dis_none; ?>>Submit</button>
		<button class="btn btn-danger" onClick="close_conversation(1)" <?php echo $dis_none; ?>>Close Token</button>
		
		<?php  echo $close_text; ?>
		
		<button class="btn btn-danger" onClick="close_conversation(0)" <?php echo $re_dis_none; ?>>Re-open Token</button>
	</div>
</div>

<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	//~ $(document).ready(function(){
		//~ $("#loader").hide();
	//~ });
	document.onreadystatechange = function () {
	  var state = document.readyState
	  if (state == 'interactive') {
		   //document.getElementById('contents').style.visibility="hidden";
	  } else if (state == 'complete') {
		  setTimeout(function(){
			 $("#loader").hide();
		  },1000);
	  }
	}
	
	function save_conversation()
	{
		if($("#conversation_text").val()=="")
		{
			$("#conversation_text").focus();
			return true;
		}
		$("#loader").show();
		$.post("pages/client_complaint_compose_data.php",
		{
			user:$("#user").text().trim(),
			client_id:$("#client_id").val(),
			complaint_id:$("#complaint_id").val(),
			conversation_text:$("#conversation_text").val(),
			type:"save_conversation_client",
		},
		function(data,status)
		{
			$("#loader").hide();
			if(data=='1')
			{
				var dialog = bootbox.dialog
				({
					message: '<h5>Successfully submitted</h5>',
					size: 'small',
					closeButton: false
				});
				setTimeout(function(){
					window.location.reload(true);
				},3000);
			}
			if(data=='4')
			{
				bootbox.alert("<h5>No Internet Connection !<br>Try again later</h5>");
			}
			if(data=='404')
			{
				bootbox.alert("<h5>Time out. Network is too slow.<br>Try again later</h5>");
			}
		})
	}
	function close_conversation(val)
	{
		var str="re-open";
		if(val>0)
		{
			str="close";
			str1="clos";
		}
		bootbox.confirm(
		{
			size: "small",
			message: "Are you sure want to "+str+" this token ?",
			closeButton: false,
			callback: function(result)
			{
				if(result)
				{
					$("#loader").show();
					$.post("pages/client_complaint_compose_data.php",
					{
						type:"close_complaint",
						val:val,
						user:$("#user").text().trim(),
						client_id:$("#client_id").val(),
						complaint_id:$("#complaint_id").val(),
					},
					function(data,status)
					{
						$("#loader").hide();
						if(data=='1')
						{
							var dialog = bootbox.dialog
							({
								message: '<h5>Token '+str1+'ed</h5>',
								size: 'small',
								closeButton: false
							});
							setTimeout(function(){
								window.location.reload(true);
							},3000);
						}
						if(data=='2')
						{
							var dialog = bootbox.dialog
							({
								message: '<h5>This token is already closed</h5>',
								size: 'small',
								closeButton: false
							});
							setTimeout(function(){
								window.location.reload(true);
							},3000);
						}
						if(data=='4')
						{
							bootbox.alert("<h5>No Internet Connection !<br>Try again later</h5>");
						}
						if(data=='404')
						{
							bootbox.alert("<h5>Time out. Network is too slow.<br>Try again later</h5>");
						}
					});
				}
			}
		});
	}
</script>
<style>
textarea {
    resize: none;
    width: 50%;
}
#company
{
	float:right;
	width:48%;
	text-align: justify;
	background: cornsilk;
	padding: 5px;
}
#client
{
	float:left;
	width:50%;
	text-align: justify;
	background: aliceblue;
	padding: 5px;
}
[class*="span"]
{
	width:100%;
	margin-left: 0;
}
#closed_text_id
{
	
}
</style>
