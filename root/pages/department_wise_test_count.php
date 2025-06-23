<?php
include("app/init.php");
?>
<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<td>
				<div class="btn-group">
					<input type="text" value="From" style="width:50px;cursor:default;font-weight:bold;" disabled />
					<input type="text" class="datepicker" id="fdate" style="width:80px;cursor:pointer;" value="<?php echo date("Y-m-d"); ?>" readonly />
					<input type="text" value="To" style="width:40px;cursor:default;font-weight:bold;" disabled />
					<input type="text" class="datepicker" id="tdate" style="width:80px;cursor:pointer;" value="<?php echo date("Y-m-d"); ?>" readonly />
				</div>
			</td>
			<td>
				<select class="span2" id="dept" onchange="load_tests()">
					<option value="0">Select All Department</option>
					<?php
					$db= new Db_Loader();
					$row=$db->setQuery("SELECT DISTINCT a.`type_id`, a.`type_name` FROM `testmaster` a, `testresults` b WHERE a.`testid`=b.`testid` ORDER BY a.`type_name`")->fetch_all();
					foreach($row as $r)
					{
					?>
						<option value="<?php echo $r['type_id'];?>"><?php echo $r['type_name']." [".$r['type_id']."]";?></option>
					<?php
					}
					?>
				</select>
			</td>
			<td>
				<select class="span3" id="test">
					<option value="0">Select All Tests</option>
				</select>
			</td>
			<td>
				<select class="span3" id="ward">
					<option value="0">Select All Ward</option>
					<?php
					$db=new Db_Loader();
					$res=$db->select(['id','ward_name'],"ward_master")->order('ward_name','ASC')->fetch_all();
					foreach($res as $r)
					{
					?>
						<option value="<?php echo $r['id'];?>"><?php echo $r['ward_name'];?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="text-align:center;">
				<button type="button" class="btn btn-success" onclick="view_test_dept()">Test Department</button>
				<button type="button" class="btn btn-success" onclick="view_pat_dept()">Patient Department</button>
				<button type="button" class="btn btn-success" onclick="view_pat_count()">Patient Count By Ward</button>
			</td>
		</tr>
	</table>
	<div id="res" style="max-height:350px;overflow-y:scroll;"></div>
</div>
<div id="loader" style="display:none;top:50%;left:50%;position:fixed;"></div>

<link type="text/css" href="../css/loader.css" rel="stylesheet" />

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<script>
	$(document).ready(function()
	{
		$(".datepicker").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear:true, maxDate: '0'});
		
		load_tests();
	});
	
	function load_tests()
	{
		$("#loader").show();
		$.post("pages/department_wise_test_count_ajax.php",
		{
			dept	:$("#dept").val().trim(),
			type	:1
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			var vl=JSON.parse(data);
			$("#test option:not(:first)").remove();
			for(var i=0; i<(vl.length); i++)
			{
				$("#test").append("<option value='"+vl[i]['id']+"'>"+vl[i]['name']+"</option>");
			}
		});
	}
	function view_test_dept()
	{
		$("#loader").show();
		$.post("pages/department_wise_test_count_ajax.php",
		{
			fdate	:$("#fdate").val().trim(),
			tdate	:$("#tdate").val().trim(),
			dept	:$("#dept").val().trim(),
			test	:$("#test").val().trim(),
			ward	:$("#ward").val().trim(),
			user	:$("#user").text().trim(),
			type	:2
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		});
	}
	function view_pat_dept()
	{
		$("#loader").show();
		$.post("pages/department_wise_test_count_ajax.php",
		{
			fdate	:$("#fdate").val().trim(),
			tdate	:$("#tdate").val().trim(),
			dept	:$("#dept").val().trim(),
			test	:$("#test").val().trim(),
			ward	:$("#ward").val().trim(),
			user	:$("#user").text().trim(),
			type	:3
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		});
	}
	function view_pat_count()
	{
		$("#loader").show();
		$.post("pages/department_wise_test_count_ajax.php",
		{
			fdate	:$("#fdate").val().trim(),
			tdate	:$("#tdate").val().trim(),
			dept	:$("#dept").val().trim(),
			test	:$("#test").val().trim(),
			ward	:$("#ward").val().trim(),
			user	:$("#user").text().trim(),
			type	:4
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		});
	}
	
	function report_print(fDt,tDt,dEp,tSt,wRd,tYp)
	{
		var url="pages/department_wise_test_count_print.php?fDt="+fDt+"&tDt="+tDt+"&dEp="+dEp+"&tSt="+tSt+"&wRd="+wRd+"&tYp="+tYp;
		window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
	}
</script>