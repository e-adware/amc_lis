<?php
session_start();
include("includes/connection.php");
$date=date("Y-m-d");
$company=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master`"));
$name=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name`"));
if($name["s_date"]!="0000-00-00"){if($date>=$name["s_date"]){include("root/pages/payment_received_check.php");exit();}}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
        <title><?php echo $company["name"]; ?> | <?php echo $name['name']; ?></title><meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" type="image/x-icon" href="images/penguin.ico">
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="css/matrix-login.css" />
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet" />
    </head>
    <body onkeypress="hid_div(event)" style="margin-top:0px;">
		<div class="row">
			<div class="span9">
			</div>
			<div class="span4" style="margin-top:20px;margin-left:80px;">
				<table>
					<tr>
						<td><img src="images/<?php echo $name["client_logo"]; ?>" type="image/jpg" style="width:90px;" /></td>
						<td style="color:#ffffff;font-size:14px;text-align:center;"><?php echo $name['name']; ?><br/>
					<?php echo $name['city'].", ".$name['state']; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div id="loginbox">            
			<!--<form id="loginform" class="form-vertical" action="index.html">-->
				<div class="control-group normal_text"> <h3><?php echo $company["name"]; ?></h3></div>
				<div class="control-group">
					<div class="controls">
						<div class="main_input_box">
							<span class="add-on bg_lg"><i class="icon-user"> </i></span><input type="text" placeholder="Username" autocomplete="off" onkeyup="load_user(this.value,event)" name="uname" id="uname" autofocus />
							<input type="hidden" id="user_id" name="user_id"/>
							<div id="update_p_sim" style="position:absolute; width: 82%; background: #fff;margin-left: 4.5%;margin-top: -3px;"></div>
						</div>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<div class="main_input_box">
							<span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" placeholder="Password" name="pword" id="pword" onkeyup="chng_focus(this.id,event)" />
						</div>
					</div>
				</div>
				<center><span id="error_mgs"></span></center>
				<div class="form-actions text-center">
					<button class="btn btn-success" type="button" onclick="submit_form()" id="subm">Login</button>
				</div>
			<!--</form>-->
			<p class="text-center" style="color: cadetblue;font-family: initial;margin-top:30%;"><span>Coded &amp; designed with &hearts; by <span><a href="http://www.e-adware.com" target="_blank" style="color: cadetblue;">e-adware</a></span></span></p>
		</div>     
        
        <script src="js/jquery.min.js"></script>
        <script src="js/matrix.login.js"></script> 
    </body>
</html>
<script>
	$(document).keydown(function (event) {
		if (event.ctrlKey && event.keyCode == 53 ){
			destroy_session();
		}
	});
	function hid_div(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==46)
		{
			if(e.shiftKey==1)
			{
				window.location.href="clear_db.php";
			}
		}
	}
	
	var sel_pser=1;
	var sel_divser=0;
	function load_user(val,e)
	{
		$("#error_mgs").text("");
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var user=document.getElementById("log_det"+sel_pser).innerHTML;	
			get_user_detail(user);
			sel_pser=1;
			sel_divser=0;
		}
		else if(unicode==40)
		{
			var chk=sel_pser+1;
			var cc=document.getElementById("upd_psim"+chk).innerHTML;
			if(cc)
			{
				sel_pser=sel_pser+1;
				$("#upd_psim"+sel_pser).css({'color': '#419641','font-weight':'bold','transition':'all .2s'});
				var sel_pser1=sel_pser-1;
				$("#upd_psim"+sel_pser1).css({'color': 'black','font-weight':'normal','transition':'all .2s'});
				var z2=sel_pser%1;
				if(z2==0)
				{
					$("#update_p_sim").scrollTop(sel_divser)
					sel_divser=sel_divser+38;
				}
			}	
		}
		else if(unicode==38)
		{
			var chk=sel_pser-1;
			var cc=document.getElementById("upd_psim"+chk).innerHTML;
			if(cc)
			{
				sel_pser=sel_pser-1;
				$("#upd_psim"+sel_pser).css({'color': '#419641','font-weight':'bold','transition':'all .2s'});
				var sel_pser1=sel_pser+1;
				$("#upd_psim"+sel_pser1).css({'color': 'black','font-weight':'normal','transition':'all .2s'});
				var z2=sel_pser%1;
				if(z2==0)
				{
					sel_divser=sel_divser-38;
					$("#update_p_sim").scrollTop(sel_divser)
					
				}
			}	
		}
		else
		{
			if(val.length>2)
			{
				$.post("load_login_user.php",
				{
					val:val,
				},
				function(data,status)
				{
					$("#update_p_sim").html(data);
					$("#update_p_sim").slideDown(500);
				})
			}
			else
			{
				//$("#update_p_sim").html("");
				$("#update_p_sim").slideUp(500);
			}
		}
	}
	function get_user_detail(user)
	{
		var user=user.split("@");
		$("#uname").val(user[1]);
		$("#user_id").val(user[2]);
		$("#update_p_sim").slideUp(500);
		$("#pword").focus();
	}
	function chng_focus(id,e)
	{
		$("#error_mgs").text("");
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			submit_form();
		}
	}
	function submit_form()
	{
		$.post("login_process.php",
		{
			user_id:$("#user_id").val(),
			uname:$("#uname").val(),
			pword:$("#pword").val(),
		},
		function(data,status)
		{
			if(data=="1")
			{
				window.location.href="root/";
			}else if(data=="2")
			{
				$("#error_mgs").html("Username or password is invalid");
				$("#pword").val("").focus();
			}else if(data=="3")
			{
				$("#error_mgs").html("Someone has already logged-in in another tab. To log in anyway press <b>Ctrl+5</b>");
				$("#pword").val("").focus();
			}
			else if(data=="5")
			{
				//window.location.href="root/processing.php?param=15";
				window.location.href="root/";
			}else if(data=="404")
			{
				$("#error_mgs").html("<b>User account doesn't have permission to access</b>");
				$("#pword").val("").focus();
			}else if(data=="4")
			{
				$("#subm").hide();
				$("#error_mgs").html("You either are already logged-in in another device or not properly log-out. To log-out from all devices click the button<br><br><button class='btn btn-warning' onClick='properly_logout()'>Logout</button>");
			}
		})
	}
	function properly_logout()
	{
		$("#error_mgs").html('');
		$("#subm").show();

		$.post("all_device_logout.php",
		{
			type:"all_device_logout",
			emp_id:$("#user_id").val(),
		},
		function(data,status)
		{
			if(data=='1')
			{
				$("#error_mgs").html("Successfully logged-out from all devices. Now log-in");
				$("#pword").val("").focus();
			}else
			{
				$("#error_mgs").html("Error");
			}
		})
	}
	function destroy_session()
	{
		$.post("all_device_logout.php",
		{
			type:"destroy_session",
		},
		function(data,status)
		{
			
		})
	}
</script>
<style>
#upd_psim1
{
  color:#419641;
  font-weight:bold;
}
#error_mgs
{
	color: #FFF;
	//margin-left: 25%;
}
</style>
