<?php
$branch_str=" AND `branch_id`='$p_info[branch_id]'";
$element_style="display:none";
if($p_info["levelid"]==1)
{
	$branch_str="";
	//$element_style="";
}
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<select id="branch_id" class="span2" onchange="load_center()" style="<?php echo $element_style; ?>">
					<?php
						$qry=mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
						while($data=mysqli_fetch_array($qry))
						{
							if($data["branch_id"]==$p_info["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
						}
					?>
					</select>
					<span class="side_name">Group</span>
					<select id="group_id" onChange="group_change()" style="margin-left: 55px;">
						<option value="0">--Select Group--</option>
					<?php
						$qry=mysqli_query($link," SELECT `group_id`, `group_name` FROM `charge_group_master` WHERE `group_id`>0 ORDER BY `group_name` ASC ");
						while($data=mysqli_fetch_array($qry))
						{
					?>
						<option value="<?php echo $data['group_id']; ?>"><?php echo $data['group_name']; ?></option>
					<?php
						}
					?>
					</select>
					<span class="side_name">From</span>
					<input class="form-control datepicker span2" type="text" name="date1" id="date1" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 47px;">
					<span class="side_name">To</span>
					<input class="form-control datepicker span2" type="text" name="date2" id="date2" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 25px;">
					<br>
					<button class="btn btn-search" id="group_wise" onclick="view('group_wise')"><i class="icon-search"></i> Group Wise</button>
					<button class="btn btn-search" id="serive_wise" onclick="view('serive_wise')"><i class="icon-search"></i> Service Wise</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
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
	});
	function view(typ)
	{
		if(typ=="serive_wise" && $("#group_id").val()==0)
		{
			alert("Select Group");
			return false;
		}
		
		$("#loader").show();
		$.post("pages/revenue_reports_data.php",
		{
			type:typ,
			date1:$("#date1").val(),
			date2:$("#date2").val(),
			branch_id:$("#branch_id").val(),
			group_id:$("#group_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			//$("#load_all").slideUp(500,function(){ $("#load_all").html(data).slideDown(500); });
			$("#load_all").html(data).slideDown(500);
		})
	}
	
	function print_page(typ,branch_id,group_id,date1,date2)
	{
		var val=btoa(1234567890);
		var user=$("#user").text().trim();
		
		var url="pages/revenue_reports_print.php?val="+val+"&typ="+typ+"&bid="+branch_id+"&gid="+group_id+"&date1="+date1+"&date2="+date2+"&EpMl="+user;
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function excel_page(typ,branch_id,group_id,date1,date2)
	{
		var val=btoa(1234567890);
		var user=$("#user").text().trim();
		
		if(typ=="group_wise")
		{
			var url="pages/revenue_reports_group_xls.php?val="+val+"&bid="+branch_id+"&gid="+group_id+"&date1="+date1+"&date2="+date2+"&EpMl="+user;
		}
		if(typ=="serive_wise")
		{
			var url="pages/revenue_reports_serive_wise_xls.php?val="+val+"&bid="+branch_id+"&gid="+group_id+"&date1="+date1+"&date2="+date2+"&EpMl="+user;
		}
		document.location=url;
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
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
@media print {
  body * {
    visibility: hidden;
  }
  #load_all, #load_all * {
    visibility: visible;
  }
  #load_all {
	  overflow:visible;
    position: absolute;
    left: 0;
    top: 0;
  }
}
.ipd_serial
{
	display:none;
}
</style>
