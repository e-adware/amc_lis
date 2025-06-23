<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed table-report">
		<tr>
			<td colspan="4" style="padding:1px;"></td>
		</tr>
		<tr>
			<th>Select Sample</th>
			<td>
				<select class="span4" id="samp_id" onchange="load_vaccu()">
					<option value="0">Select Sample</option>
					<?php
					$q=mysqli_query($link,"SELECT * FROM `Sample` ORDER BY `Name`");
					while($r=mysqli_fetch_array($q))
					{
					?>
					<option value="<?php echo $r['ID'];?>"><?php echo $r['Name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="btn btn-info" onclick="view_mapped()">view</button>
			</td>
		</tr>
	</table>
	<div id="res"></div>
</div>
<div id="loader" style="position:fixed;top:50%;left:50%;display:none;z-index:10000;"></div>
<style>
.table-condensed
{
	margin:0px;
}
input[type="checkbox"]
{
	margin: 0px;
}
</style>
<link rel="stylesheet" href="../css/loader.css" />
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<script>
	function grit_msg(msg,n)
	{
		$.gritter.add(
		{
			//title:	'Normal notification',
			text:	'<h5 style="text-align:center;">'+msg+'</h5>',
			time: 1000,
			sticky: false
		});
		if(n>0)
		{
			$(".gritter-item").css("background","#237438");
		}
	}
	function load_vaccu()
	{
		$("#loader").show();
		$.post("pages/sample_vaccu_map_ajax.php",
		{
			samp:$("#samp_id").val(),
			type:1
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		});
	}
	function mapp_vaccu(samp)
	{
		$("#loader").show();
		$("#sav").attr("disabled", true);
		var vacc="";
		var chk=$(".checks:checked");
		for(var i=0;i<chk.length;i++)
		{
			vacc+="@"+chk[i].value;
		}
		//alert(vacc);
		
		$.post("pages/sample_vaccu_map_ajax.php",
		{
			samp:samp,
			vacc:vacc,
			type:2
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			grit_msg("Done",1);
			$("#sav").attr("disabled", false);
			//$("#res").html(data);
		});
	}
	function view_mapped()
	{
		var url="pages/sample_vaccu_map_print.php";
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>