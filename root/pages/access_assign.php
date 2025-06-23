<?php
$not_accountant = array();
array_push($not_accountant, 6, 11, 12, 13, 20, 21);
$not_accountant = join(',',$not_accountant);

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Access Assign</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div id="view_data"></div>
	<table class="table table-striped table-bordered">
		<tr>
			<td>Select Access Level</td>
			<td>
				<select id="level" name="level" onChange="form_sub()">
					<option value="0">--Select--</option>
					<?php
						$lv=mysqli_query($link,"select * from level_master");
						while($l=mysqli_fetch_array($lv))
						{
							//if($_POST['level']==$l['levelid']){$sel="selected='selected'";} else{ $sel="";} 		
							echo "<option value='$l[levelid]' $sel>$l[name]</option>";
						}
					?>
				</select>
			</td>
			<td>Select Access User</td>
			<td>
				<select id="level_user" name="level_user" onChange="level_user()">
					<option value="0">--Select--</option>
					<?php
					
						$user_qry=mysqli_query($link, " SELECT `emp_id`,`name` FROM `employee` WHERE levelid NOT IN ($not_accountant) ORDER BY `name` ");
						while($user=mysqli_fetch_array($user_qry))
						{
							//if($emp_id==$user["emp_id"]){ $sel_this="selected"; }else{ $sel_this=""; }
							echo "<option value='$user[emp_id]' $sel_this>$user[name]</option>";
						}
					?>
				</select>
			</td>
		</tr>
	</table>
	<div id="load_data">
		
	</div>
	<div id="msg" style="top:40%;position:fixed;left:50%;display:none;background:#ffffff;border:1px solid #BFBFBF;padding:20px;font-size:16pt;border-radius:5px;box-shadow: 2px 2px 8px 5px;"></div>
</div>
<script src="../js/jquery.ui.custom.js"></script>
<script src="../js/jquery.uniform.js"></script>
<link rel="stylesheet" href="../css/uniform.css" type="text/css" />

<script>
	$(function()
	{
		$( "#view_data" ).draggable({ containment: "body", scroll: false });
	})
	function form_sub()
	{
		$("#level_user").val("0");
		$("#view_data").hide();
		$.post("pages/access_assign_data.php",
		{
			type:"level_access",
			level:$("#level").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			
			var val=$("#level").val();
			if(val=="0")
			{
				document.getElementById("acc").disabled=true;
			}
			else
			{
				document.getElementById("acc").disabled=false;
			}
		})
	}
	function level_user()
	{
		$("#level").val("0");
		$("#view_data").hide();
		$.post("pages/access_assign_data.php",
		{
			type:"level_access_user",
			level_user:$("#level_user").val(),
		},
		function(data,status)
		{
			$("#load_data").html(data);
			
			var val=$("#level_user").val();
			if(val=="0")
			{
				document.getElementById("acc").disabled=true;
			}
			else
			{
				document.getElementById("acc").disabled=false;
			}
			load_access_level_data();
		})
	}
	function load_access_level_data()
	{
		if($("#level_user").val()=="0")
		{
			$("#view_data").hide();
		}
		else
		{
			$.post("pages/access_assign_data.php",
			{
				type:"load_access_level_data",
				level_user:$("#level_user").val(),
			},
			function(data,status)
			{
				$("#view_data").html(data).css("display","inline-block");
				$("#view_data").scrollTop(0);
			});
		}
	}
	function save_acc(typ)
	{
		$("#acc_asign").css({'opacity':'0.5'});
		$("#msg").text("Assigning.....");
		var x=$("#butts").offset();
		var w=$("#msg").width()/2;
		//$("#msg").css({'top':x.top-800,'right':'60%'});
		$("#msg").fadeIn(500);	
	
		var ck_box=document.getElementsByClassName("chk");
		var sel_b="";
		for(var i=0;i<ck_box.length;i++)
		{
			if(ck_box[i].checked)
			{
				sel_b=sel_b+"$"+ck_box[i].value;
			}
		}
		
		$.post("pages/access_assign_save.php",
		{
			level:$("#level").val(),
			emp_id:$("#level_user").val(),
			menu:sel_b,
			type:typ,
		},
		function(data,status)
		{
			$("#msg").text("Access Assigned");
			setTimeout(function(){$("#msg").fadeOut(500,function(){$("#acc_asign").css({'opacity':'1.0'})});$("html, body").animate({ scrollTop: 20 }, "slow");},1000);
		})
	}
	function checkall(n)
	{
		if($("#chk_val"+n).is(":checked"))
		{
			$("#chk_val"+n).prop('checked', true);
			$("input[name='chk_name"+n+"']").prop('checked', true);
		}
		else
		{
			$("#chk_val"+n).prop('checked', false);
			$("input[name='chk_name"+n+"']").prop('checked', false);
		}
	}
	function chk_name_click(n)
	{
		var chk=$("input[name='chk_name"+n+"']:checked").length;
		var tot_val=parseInt($("#chk_val_num"+n).val());
		
		if(tot_val==chk)
		{
			$("#chk_val"+n).prop("checked", true);
		}else
		{
			$("#chk_val"+n).prop("checked", false);
		}
	}
</script>
<style>
.select2-dropdown
{
	z-index:999 !important;
}
.select2
{
	margin-bottom: 1%;
}
#view_data
{
	position:absolute;
	display:none;
	border:1px solid #AAA;
	background:#FFF;
	box-shadow:0px 0px 10px 2px #AAA;
	padding:2px;
	cursor:move;
	height:400px;
	width:350px;
	max-height: 500px;
	max-width:400px;
	overflow-y:scroll;
}
.menu_hr
{
	margin:0px;
}
</style>
