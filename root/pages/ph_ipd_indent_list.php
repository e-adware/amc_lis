<?php
$fdate=base64_decode($_GET['fdate']);
$tdate=base64_decode($_GET['tdate']);
$ward=base64_decode($_GET['ward']);
$stat=base64_decode($_GET['stat']);
$pin=base64_decode($_GET['pin']);

if(!$fdate)
{
	$fdate=date("Y-m-d");
}
if(!$tdate)
{
	$tdate=date("Y-m-d");
}
?>
<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed table-report">
		<tr>
			<td colspan="6"></td>
		</tr>
		<tr>
			<td>
				<div class="btn-group">
					<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" id="fdate" style="width:100px;" value="<?php echo $fdate; ?>" >
					<input type="text" value="To" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" id="tdate" style="width:100px;" value="<?php echo $tdate; ?>" >
				</div>
			</td>
			<td>
				<input type="text"  id="pin" style="width:100px;" value="<?php echo $pin; ?>" placeholder="IPD ID" />
			</td>
			
			<td>
				<select class="span2" id="ward" onchange="load_order()">
				<option value="0">Select Ward</option>
				<?php
				$ph_qry=mysqli_query($link,"SELECT * FROM `ward_master` order by name");
				while($ph_r=mysqli_fetch_assoc($ph_qry))
				{
				?>
				<option value="<?php echo $ph_r['ward_id'];?>" <?php if($ph_r['ward_id']==$ward){echo "selected='selected'";}?>><?php echo $ph_r['name'];?></option>
				<?php
				}
				?>
				</select>
		</td>
			<td>
				<select class="span2" id="stat" onchange="load_order()">
					<option value="0" <?php if($stat==0){ echo "selected"; } ?>>Pending</option>
					<option value="1" <?php if($stat==1){ echo "selected"; } ?>>Approve</option>
				</select>
			</td>
			<td>
				<button type="button" class="btn btn-info" onclick="load_order()">Search</button>
			</td>
		</tr>
	</table>
	<div id="res">
	
	</div>
</div>
<style>
	tr.nm:hover td
	{
		background: #FFF9D3;
		color:#0B0083;
		transition:0.4s;
		text-shadow: 0px 0px 6px rgba(113, 210, 201, 0.7);
	}
</style>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<script src="../js/jquery.gritter.min.js"></script>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd',maxDate:"0"});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd',maxDate:"0"});
		
		var timeout					= null;
		var pin						= document.getElementById('pin');
		pin.onkeyup = function(e) // Init a timeout variable to be used below
		{
			clearTimeout(timeout);
			timeout = setTimeout(function()
			{
				load_order();
			}, 500);
		};
		
		load_order();
	});
	
	function load_order()
	{
		$("#loader").show();
		$.post("pages/ph_ipd_indent_list_ajax.php",
		{
			fdate:$("#fdate").val().trim(),
			tdate:$("#tdate").val().trim(),
			pin:$("#pin").val().trim(),
			stat:$("#stat").val(),
			ward:$("#ward").val(),
			type:1,
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			$("#res").html(data);
		});
	}
	function load_med_det(pid,opd,ino)
	{
		alert("Under Process");
		//window.location="index.php?param="+btoa(272)+"&pId="+btoa(pid)+"&oPd="+btoa(opd)+"&iNo="+btoa(ino)+"&fdate="+btoa($("#fdate").val())+"&tdate="+btoa($("#tdate").val())+"&ward="+btoa($("#ward").val())+"&pin="+btoa($("#pin").val())+"&stat="+btoa($("#stat").val());
	}
</script>
