<?php
session_start();
if(!isset($_SESSION["emp_id"]))
{
	echo "<script>window.location.href='../'</script>";
}
include("../includes/connection.php");
$company=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master`"));
$p_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$_SESSION[emp_id]' "));

$emp_id=base64_encode($_SESSION["emp_id"]);

$menu_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `menu_master` WHERE `par_id`='$para' "));

if($menu_info)
{
	$page_name=$menu_info["par_name"];
}else
{
	$page_name=$company["name"];
}

$menu_para_rows=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `menu_master` WHERE `par_id`='$para' AND `header`!='0' "));
if($menu_para_rows)
{
	$menu_access_info=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `menu_access_detail` WHERE `levelid`='$p_info[levelid]' AND `par_id`='$para'"));
	//$menu_access_info=1;
}
else
{
	$menu_access_info=1;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_name." | ".$company["name"]; ?></title><meta charset="UTF-8" />
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="icon" type="image/x-icon" href="../images/penguin.ico">
	<link rel="stylesheet" href="../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
	<!--<link rel="stylesheet" href="../css/fullcalendar.css" />-->
	<link rel="stylesheet" href="../css/matrix-style.css" />
	<link rel="stylesheet" href="../css/matrix-media.css" />
	<!--<link rel="stylesheet" href="../css/jquery.gritter.css" />-->
	<!--<link rel="stylesheet" href="../css/colorpicker.css" />-->
	<!--<link rel="stylesheet" href="../css/datepicker.css" />-->
	<!--<link rel="stylesheet" href="../css/uniform.css" />-->
	<!--<link rel="stylesheet" href="../css/select2.css" />-->
	<!--<link rel="stylesheet" href="../css/bootstrap-wysihtml5.css" />-->
	<link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
	<link rel="stylesheet" href="../css/custom.css" />
	
	<script src="../js/jquery.min.js"></script>
	
	
	<script>
		var timeout;
		function show_menu()
		{
			$("[class*='submenu'] a").tooltip('hide');
			//$("#sidebar").animate({width: "19%"},"200",function(){ $("#sidebar").css({'overflow':'scroll'}); });
			$("#sidebar").animate({width: "270px"},"200",function(){ $("#sidebar").css({'overflow':'scroll'}); });
			$("[class*='submenu'] a").tooltip('disable');
		}
		function hide_menu()
		{
			//$("#sidebar").animate({width: "3%"},"200",function(){ $("#sidebar").css({'overflow':'hidden'});  });
			$("#sidebar").animate({width: "45px"},"200",function(){ $("#sidebar").css({'overflow':'hidden'});  });
			$("#sidebar li[class*='open'] ul").slideUp();$("#sidebar li[class*='open']").attr("class","submenu");
			
			$("[class*='submenu'] a").tooltip('enable');
		}
		
		function show_menu_scroll()
		{
			$("#sidebar").css({'overflow':'scroll','overflow-x':'hidden','scrollbar-color': ' #49afcd #333','scrollbar-width': 'thin'});
		}
		
		function show_header(elem,menu)
		{
			
			var x=$(elem).offset();
			
			$(".menu_head_name").css({'position':'absolute','top':'100px','left':'200px','color':'blue','font-weight':'bold'})
			$(".menu_head_name").text(menu);
		}
		
		$(document).ready(function(){

		   $(".container-fluid").mousemove(function(e){
			   //hide_menu();
			    $('[data-toggle="tooltip"]').tooltip();
			   
		   });
		   
		   somethingChanged = false;
		})
		
		$(document).keydown(function (event)
		{
			//alert(event.keyCode);
			
			var user=$("#user").text().trim();
			var level_id=$("#lavel_id").val();
			
			if (event.ctrlKey && event.keyCode ==120) //Therapy
			{
				if(user==101|| user==102)
				{
					redirect_shortcut(117);
				}
			}
			if (event.ctrlKey && event.keyCode ==112) //Therapy Timer
			{
				if(user==101|| user==102)
				{
					redirect_shortcut(136);
				}
			}
			if (event.ctrlKey && event.shiftKey && event.keyCode == 48) //Therapy Timer Client
			{
				if(level_id==1)
				{
					//redirect_shortcut(138);
				<?php
					if($data_mp==1)
					{
				?>
						window.location="?param="+btoa(967);
				<?php
					}
				?>
				}
			}
			if (event.ctrlKey && event.keyCode ==49)
			{
				redirect_shortcut(81);
			}
			if (event.ctrlKey && event.keyCode ==50)
			{
				redirect_shortcut(82);
			}
			if (event.ctrlKey && event.keyCode ==51)
			{
				redirect_shortcut(83);
			}
			if (event.ctrlKey && event.keyCode ==52)
			{
				redirect_shortcut(84);
			}
			if (event.ctrlKey && event.keyCode ==53)
			{
				redirect_shortcut(130);
			}
			
			if (event.ctrlKey && event.shiftKey && event.keyCode ==76)
			{
				redirect_shortcut(2);
			}
			// Comsn
	<?php
		if($doc_comm==1)
		{
	?>
			if (event.ctrlKey && event.shiftKey && event.keyCode == 38)
			{
				if(level_id==1)
				{
					window.location="?param="+btoa(965);
				}
			}
			
			if (event.ctrlKey && event.shiftKey && event.keyCode == 40)
			{
				if(level_id==1)
				{
					window.location="?param="+btoa(966);
				}
			}
	<?php
		}
	?>
			//
			if(event.keyCode == 27)
			{
				$("#notification_close").click();
				
				// Lab reg
				show_selected_test();
			}
			
			if (event.keyCode == 123 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent F12
				return false;
			} else if (event.ctrlKey && event.shiftKey && event.keyCode == 73 && $("#user").text().trim()!='101' && $("#user").text().trim()!='102') { // Prevent Ctrl+Shift+I        
				return false;
			}
		});
		$(document).on("contextmenu",function(e){
			if($("#user").text().trim()!='101' && $("#user").text().trim()!='102')
			{
				//e.preventDefault();
			}
		});
		var refreshTime = 600000; // every 10 minutes in milliseconds
		window.setInterval( function() {
			$.post("refreshSession.php",
			{
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				//alert(data);
			});
		}, refreshTime );
		var refreshTime_2 = 2000; // every 2 seconds in milliseconds
		window.setInterval( function() {
			//~ $.post("destroy_session.php",
			//~ {
				//~ user:$("#user").text().trim(),
			//~ },
			//~ function(data,status)
			//~ {
				//~ if(data=='1')
				//~ {
					//~ window.location.href='../';
				//~ }
			//~ });
			checkCookie();
		}, refreshTime_2 );
		function redirect_shortcut(param)
		{
			$.post("pages/global_check.php",
			{
				type:"check_access",
				param:param,
				lavel_id:$("#lavel_id").val(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				if(data==1)
				{
					//window.location="processing.php?param="+param;
					window.location="?param="+btoa(param);
				}
			})
		}
		function pass_change_popup()
		{
			$("#close").hide();
			$("#chng_pass").prop("disabled", true);
			$("#old_pass").prop("disabled", true);
			$("#pass_mod").click();
			$("#new_pass").focus().val("");
			$("#confrm_pass").val("");
			$("#old_pass").val("");
			$("#con_error").text("");
			$("#old_error").text("");
			$("#old_success").text("");
		}
		function new_pass(e)
		{
			$("#close").hide();
			$("#confrm_pass").val("");
			$("#old_pass").val("");
			$("#con_error").text("");
			$("#old_error").text("");
			$("#old_success").text("");
			$("#old_pass").prop("disabled", true);
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				if($("#new_pass").val()!="")
				{
					$("#confrm_pass").focus();
				}
			}
		}
		function confrm_pass(val,e)
		{
			if(val!="")
			{
				if($("#new_pass").val()==val)
				{
					$("#con_error").text("");
					$("#old_error").text("");
					$("#old_success").text("");
					$("#old_pass").prop("disabled", false);
					var unicode=e.keyCode? e.keyCode : e.charCode;
					if(unicode==13)
					{
						$("#old_pass").focus();
					}
				}else
				{
					$("#old_pass").prop("disabled", true);
					$("#con_error").text("Password not matched !");
				}
			}
		}
		function old_pass(val,e)
		{
			if(val!="")
			{
				$("#chng_pass").prop("disabled", false);
				$("#old_error").text("");
				var unicode=e.keyCode? e.keyCode : e.charCode;
				if(unicode==13)
				{
					change_password();
				}
			}else
			{
				$("#chng_pass").prop("disabled", true);
			}
		}
		function change_password()
		{
			if($("#new_pass").val()=="")
			{
				$("#new_pass").focus();
			}else if($("#confrm_pass").val()=="")
			{
				$("#confrm_pass").focus();
			}else
			{
				$.post("pages/global_check.php",
				{
					type:"password_check",
					old_pass:$("#old_pass").val(),
					new_pass:$("#new_pass").val(),
					user:$("#user").text().trim(),
				},
				function(data,status)
				{
					if(data=="1")
					{
						$("#old_pass").focus();
						$("#old_error").text("Old passowrd incorrect !");
						$("#old_success").text("");
					}
					if(data=="2")
					{
						$("#old_success").html("<b>Password Changed</b>");
						$("#new_pass").val("");
						$("#confrm_pass").val("");
						$("#old_pass").val("").prop("disabled", true);;
						$("#close").show().focus();
					}
					if(data=="3")
					{
						$("#old_error").text("Error, please try again later");
					}
				})
			}
		}
		function name_title_ch(val)
		{
			if(val=="MR." || val=="MASTER." || val=="FR." || val=="BABY." || val=="MD." || val=="SRI.")
			{
				$("#sex").val("Male");
			}
			if(val=="MISS." || val=="MRS." || val=="SR." || val=="KUMARI.")
			{
				$("#sex").val("Female");
			}
		}
		//~ history.pushState(null, null, 'index.php');
		//~ window.addEventListener('popstate', function(event) {
			//~ history.pushState(null, null, 'index.php');
		//~ });
		
		function print_barcode_recp(uhid,pin)
		{
			var barcode_no=$("#barcode_no").val();
			var user=$("#user").text().trim();
			var url="pages/barcode_generate_recp.php?uhid="+uhid+"&pin="+pin+"&user="+user+"&barcode_no="+barcode_no;
			window.open(url,'','fullscreen=yes,scrollbars=yes');
		}
		function print_demographic(uhid)
		{
			var user=$("#user").text().trim();
			var url="pages/patient_demographic_details.php?uhid="+btoa(uhid)+"&user="+btoa(user);
			window.open(url,'','fullscreen=yes,scrollbars=yes');
		}
		var myEvent = window.attachEvent || window.addEventListener;
		var chkevent = window.attachEvent ? 'onbeforeunload' : 'beforeunload'; /// make IE7, IE8 compitable
		myEvent(chkevent, function(e) { // For >=IE7, Chrome, Firefox
			if(somethingChanged)
			{
				var confirmationMessage = 'Are you sure to leave the page?';  // a space
				(e || window.event).returnValue = confirmationMessage;
				return confirmationMessage;
			}
		});
	</script>
	<style>
		#content{ margin-top:5.5%;}
		#sidebar > ul {
			width: 270px !important;
		}
		.tooltip{ position:fixed; left:45px !important}
		.submenu a
		{
			color: #FFF !important;
		}
		.shortName
		{
			color: #FFF !important;
			display:inline-block;
			width: 16px;
			text-size: 4px;
		}
		#sidebar > ul > li > a
		{
			padding: 10px 0 10px 8px;
		}
	</style>
</head>
<body onkeyup="hid_div(event)" onload="checkCookie()">


<!--Header-part-->
<div id="header" style="position:fixed;z-index:1000000;height:70px;display:none">
  <span class="company_name_aside"><?php echo $company["name"]; ?></span>
</div>



<!--close-Header-part--> 

<input type="hidden" id="lavel_id" value="<?php echo $p_info['levelid'];?>"/>
<input type="hidden" id="param_id" value="<?php echo $para;?>"/>

<!--top-Header-menu -->

<div id="user-nav" class="navbar navbar-inverse" style="display:none;left:auto;">
	<ul class="nav" style="width: auto; margin: 0px;">
		<li class="dropdown" id="alr_dropdown">
			<a title="" href="#" data-toggle="dropdown" data-target="#alr_dropdown" class="dropdown-toggle">
				<i class="icon icon-bell"></i> <b>Notification</b>
				<span class="badge badge-important" id="notify_head"></span>
				<b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<li onclick="notify_alert()" id="li_exp" style="display:none;"><a href="#"><i class="icon-warning-sign"></i> Expiry Notification <span class="badge badge-important" id="not_exp"></span></a></li>
				<li onclick="notify_low_alr()" id="li_stk" style="display:none;"><a href="#"><i class="icon-exclamation-sign"></i> Low Stock <span class="badge badge-important" id="not_stk"></span></a></li>
			</ul>
		</li>
	</ul>
</div>

<!--<div id="search" class="navbar-inverse">
	<ul class="nav">
    <li  class="dropdown" id="profile-messages" ><a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle"><i class="icon icon-user"></i>  <span class="text" style="font-size:14px;">Welcome <?php echo $p_info["name"]; ?></span><span id="user" style="display:none;"><?php echo $_SESSION["emp_id"]; ?></span><b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="#" onCLick="pass_change_popup()"><i class="icon-check"></i> Change Password</a></li>
        <a href="#myModal_pass_header" data-toggle="modal" id="pass_mod"></a>
        <li class="divider"></li>
        <li><a href="../logout.php?Piuoi87yL8jhjUyl=<?php echo $emp_id; ?>"><i class="icon-key"></i> Log Out</a></li>
      </ul>
    </li>
  </ul>
</div>-->

<div id="search" class="dropdown">
	<b class="dropbtn"><span class="text" style="font-size:14px;">Welcome <?php echo $p_info["name"]; ?></span><span id="user" style="display:none;"><?php echo $_SESSION["emp_id"]; ?></span></b>
	<div class="dropdown-content">
		<a href="#" onCLick="pass_change_popup()"><i class="icon-check"></i> Change Password</a>
		<a href="../logout.php?Piuoi87yL8jhjUyl=<?php echo $emp_id; ?>"><i class="icon-key"></i> Log Out</a>
	</div>
</div>
<a href="#myModal_pass_header" data-toggle="modal" id="pass_mod"></a>

<div id="myModal_pass_header" class="modal hide">
	<div class="modal-header">
		<button data-dismiss="modal" class="close" type="button">×</button>
		<h3>Change Password</h3>
	</div>
	<div class="modal-body">
		<table class="table table-no-top-border">
			<tr>
				<th>New password <span style="float: right;">:</span></th>
				<td><input type="password" name="new_pass" id="new_pass" onKeyUp="new_pass(event)"></td>
			</tr>
			<tr>
				<th>Confirm password <span style="float: right;">:</span></th>
				<td>
					<input type="password" name="confrm_pass" id="confrm_pass" onKeyUp="confrm_pass(this.value,event)"><br>
					<span id="con_error" class="error"></span>
				</td>
			</tr>
			<tr>
				<th>Old password <span style="float: right;">:</span></th>
				<td>
					<input type="password" name="old_pass" id="old_pass" onKeyUp="old_pass(this.value,event)"><br>
					<span id="old_error" class="error"></span>
					<span id="old_success" class="success"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center;">
					<button class="btn btn-info" id="chng_pass" onClick="change_password()">Change</button>
					<button class="btn btn-danger" data-dismiss="modal" class="close" id="close">Close</button>
				</td>
			</tr>
		</table>
	</div>
</div>
<!--close-top-Header-menu-->
<!--sidebar-menu-->
<?php
if($para!=0)
{
	$main_header=mysqli_fetch_array(mysqli_query($link, " SELECT distinct(`header`) FROM `menu_master` WHERE `par_id`='$para' "));
}else
{
	$active_class="active";
}// numex=/^[0-9-]+$/ onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
?>
<div id="sidebar" onmousemove="show_menu_scroll()" onclick="show_menu()" onmouseleave="hide_menu()" style="position:fixed;top:0%;overflow:hidden;width:45px;z-index:99998;height:100%;background-color:#1e242b">
	
	<a href="./" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
	<ul class="sidemenu">
		<li class="licls <?php echo $active_class; ?>"><a href="./" data-toggle="tooltip" data-placement="right" title="Dashboard"><?php echo "<div class='shortName'>D</div>";?> <i class="icon icon-home"></i> <span>Dashboard</span></a> </li>
		
    <?php
		$menu_header_qry=mysqli_query($link, " SELECT * FROM `menu_header_master` WHERE `id` IN (SELECT `header` FROM `menu_master` WHERE `hidden`='0' AND `par_id` IN (SELECT `par_id` FROM `menu_access_detail_user` WHERE `emp_id`='$_SESSION[emp_id]')) order by `sequence` ");
		$menu_header_user_num=mysqli_num_rows($menu_header_qry);
		if($menu_header_user_num==0)
		{
			$menu_header_qry=mysqli_query($link, " SELECT * FROM `menu_header_master` WHERE `id` IN (SELECT `header` FROM `menu_master` WHERE `hidden`='0' AND `par_id` IN (SELECT `par_id` FROM `menu_access_detail` WHERE `levelid`='$p_info[levelid]')) order by `sequence` ");
		}
		while($menu_header=mysqli_fetch_array($menu_header_qry))
		{
			if($main_header["header"]==$menu_header["id"]){ $active_class="active"; }else{ $active_class=""; }
			$words = explode(" ", $menu_header["name"]);
			$words = preg_replace('/[^A-Za-z0-9\-]/', '', $words);
			$acronym = "";
			foreach ($words as $w)
			{
			  $acronym.= mb_substr($w, 0, 1);
			}
			if(strlen($acronym)==1)
			{
				$acronym="&nbsp;".$acronym;
			}
    ?>
		 <li class="licls submenu <?php echo $active_class; ?>"><a href="#" data-toggle="tooltip" data-placement="right" title="<?php echo $menu_header["name"]; ?>" ><?php echo "<div class='shortName'>".$acronym."</div>";?> <i class="icon icon-arrow-right icon-mini"></i> <span><?php echo $menu_header["name"]; ?></span> </a>
			<ul>
			<?php
				$menu_sub_header_qry=mysqli_query($link, " SELECT * FROM `menu_master` WHERE `header`='$menu_header[id]' AND `hidden`='0' order by `sequence` ");
				while($menu_sub_header=mysqli_fetch_array($menu_sub_header_qry))
				{
					if($menu_sub_header["par_id"]==4 || $menu_sub_header["par_id"]==10)
					{
						if($_SESSION["emp_id"]==101 || $_SESSION["emp_id"]==102)
						{
							$display="Yes";
						}else
						{
							$display="No";
						}
					}else
					{
						$display="Yes";
					}
					if($display=="Yes")
					{
						if($menu_header_user_num>0)
						{
							$m_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `menu_access_detail_user` WHERE `emp_id`='$_SESSION[emp_id]' AND `par_id`='$menu_sub_header[par_id]'"));
						}else
						{
							$m_num=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `menu_access_detail` WHERE `levelid`='$p_info[levelid]' AND `par_id`='$menu_sub_header[par_id]'"));
						}
						if($m_num>0)
						{
							$enc_para=base64_encode($menu_sub_header["par_id"]);
							$enc_para=base64_encode($menu_sub_header["par_id"]);
							if($menu_sub_header["par_id"]==$para)
							{
								$menu_active="active";
							}
							else
							{
								$menu_active="";
							}
					?>
						<li class="<?php echo $menu_active; ?>"><a href="?param=<?php echo $enc_para; ?>"><?php echo $menu_sub_header["par_name"]; ?></a></li>
					<?php
						}
					}
				}
			?>
			</ul>
		</li>
    <?php
		}
		if($_GET["branch_id"])
		{
			$branch_id=$_GET["branch_id"];
		}
		else if($p_info["branch_id"])
		{
			$branch_id=$p_info["branch_id"];
		}
		if($branch_id)
		{
			$our_client=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` WHERE `branch_id`='$branch_id' limit 0,1 "));
		}
		else
		{
			$our_client=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name`"));
		}
		$today=date("Y-m-d");$last_day=$our_client["c_date"];$diff=date("Y", strtotime($today))-date("Y", strtotime($last_day));if($diff>1){$s_time=rand(($diff*2),($diff*5));sleep($s_time);}if($our_client["mc_det"]){$mc_val=$our_client["mc_det"];$mc_str=explode("@#$@",$mc_val);if($mc_str[3]!=$_SERVER['SERVER_ADDR']){session_destroy();}} // ip link | awk '{print $2}'
    ?>
  </ul>
</div>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content" style="width:97%;margin-left:45px;margin-right:0" onmouseenter="hide_menu()">
	<span style="top:-30px;position: absolute;left: 45%;color:#eeeeee;font-weight:bold;"><?php echo $our_client['name'];?></span>
<?php if($our_client["n_date"]!="0000-00-00"){ include("pages/payment_received_ncheck.php"); } ?>
	
