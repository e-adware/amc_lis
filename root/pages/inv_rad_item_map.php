<style>
.parent_div
{
	margin-left:0px;
}
.child_div
{
	clear:both;
	margin-left:0px;
	padding:2px;
	padding-top:0px;
	display: inline-block;
	width: 48%;
}
.table-report
{
	background:#FFFFFF;
}
</style>
<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<th>
				Select Test
			</th>
			<td>
				<select id="tst" onchange="ungroup_load()" class="span6">
					<option value="0">Select</option>
					<?php
					$qrmkt=mysqli_query($link,"select testid,testname from testmaster where testname!='' order by testname");
					//$qrmkt=mysqli_query($link,"SELECT a.`ID`, a.`Name` FROM `Parameter_old` a, `ResultType` b WHERE a.`ResultType`=b.`ResultTypeId` AND b.`ResultTypeId`>'0' AND a.`Name`!='' ORDER BY a.`Name` ");
					while($qrmkt1=mysqli_fetch_array($qrmkt))
					{
					?>
					<option value="<?php echo $qrmkt1['testid'];?>"><?php echo $qrmkt1['testname'];?></option>
					<?php
					}
					?>
				</select>
				<button type="button" class="btn btn-primary" onclick="view_list()">View</button>
				<div id="test_details">
					<b><u>Primary Sample :</u></b>
					<div id="samp" class="test_details"></div>
					<b><u>Parameter(s) :</u></b>
					<div id="pars" class="test_details"></div>
				</div>
			</td>	
		</tr>
	</table>
<div id="loader" style="display:none;position:fixed;top:50%;left:50%;z-index:9999;"></div>
<div id="no_activity_div"></div>
<input type="hidden" id="no_activity" value="8:01">
<input type="hidden" id="fix_time_no_activity" value="8:01">


	<div class="parent_div">
		<div class="child_div">
			<input type="text" id="ungrp_srch" style="width:95%;" autocomplete="off" onKeyUp="ungroup_load()" placeholder="Search..." />
			<div style="height:400px;overflow-y:scroll;" id="ungroup_load">
				
			</div>
		</div>		
		<div class="child_div">
			<input type="text" id="grp_srch" style="width:95%;" autocomplete="off" onKeyUp="group_load()" placeholder="Search..." />
			<div style="height:400px;overflow-y:scroll;" id="group_load">
				
			</div>
		</div>
	</div>
</div>
<style>
.margin_b
{
	margin-bottom: 0px !important;
}
#test_details
{
	width: 400px;
	max-height: 200px;
	overflow-y: scroll;
	border: 1px solid;
	z-index: 9999;
	position: fixed;
	bottom: 0px;
	right: 0px;
	background: #FFF;
	display: none;
	border-radius: 3px;
	padding: 5px;
}
.test_details
{
	font-style: italic;
}
</style>
<script>
	$(document).ready(function()
	{
		$("#tst").select2({ theme: "classic" });
		$("#tst").select2("focus");
		//~ var refreshTime_3 = 1000; // every 1 seconds in milliseconds
		//~ window.setInterval( function()
		//~ {
			//~ var timer2 = $("#no_activity").val();
			//~ var timer = timer2.split(':');
			//~ //by parsing integer, I avoid all extra string processing
			//~ var minutes = parseInt(timer[0], 10);
			//~ var seconds = parseInt(timer[1], 10);
			//~ seconds--;
			//~ minutes = (seconds < 0) ? --minutes : minutes;
			//~ if (minutes < 0) clearInterval(interval);
			//~ seconds = (seconds < 0) ? 59 : seconds;
			//~ seconds = (seconds < 10) ? '0' + seconds : seconds;
			//~ //minutes = (minutes < 10) ?  minutes : minutes;
			
			//~ if(minutes==0 && seconds<11 && seconds>0)
			//~ {
				//~ $("#no_activity_div").html("");
				//~ //$("#no_activity_div").html("<h4>Auto-logout in "+timer2+" minutes</h4>");
				//~ $("#no_activity").val(minutes + ':' + seconds);
				//~ timer2 = minutes + ':' + seconds;
			//~ }else if(minutes==0 && seconds==0)
			//~ {
				//~ //timeout_session();
			//~ }else
			//~ {
				//~ $("#no_activity_div").html("");
				//~ //$("#no_activity_div").html("<h4>Auto-logout in "+timer2+" minutes</h4>");
				//~ $("#no_activity").val(minutes + ':' + seconds);
				//~ timer2 = minutes + ':' + seconds;
			//~ }
			
		//~ }, refreshTime_3 );
	
	});
	$(document).keydown(function()
	{
		var NoActivityTime = $("#fix_time_no_activity").val().trim();
		$("#no_activity").val(NoActivityTime);
	});
	function mousemove(e)
	{
		var NoActivityTime = $("#fix_time_no_activity").val().trim();
		$("#no_activity").val(NoActivityTime);
	}
	function view_list()
	{
		url="pages/inv_radio_item_detail_all.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function load_test_dets()
	{
		$.post("pages/inv_rad_indent_map_ajax.php",
		{
			tst:$("#tst").val(),
			type:7,
		},
		function(data,status)
		{
			let vl=JSON.parse(data);
			$("#samp").html(vl['sample']);
			$("#pars").html(vl['params']);
			$("#test_details").show();
		});
	}
	function ungroup_load()
	{
		if($("#tst").val()=="0")
		{
			$("#group_load").empty();
			$("#ungroup_load").empty();
			$("#samp").empty();
			$("#pars").empty();
			$("#test_details").hide();
		}
		else
		{
			load_test_dets();
			$.post("pages/inv_rad_indent_map_ajax.php",
			{
				val:$("#ungrp_srch").val(),
				tst:$("#tst").val(),
				type:1,
			},
			function(data,status)
			{
				$("#ungroup_load").html(data);
				group_load();
			});
		}
	}
	function group_load()
	{
		if($("#tst").val()=="0")
		{
			$("#group_load").empty();
		}
		else
		{
			$.post("pages/inv_rad_indent_map_ajax.php",
			{
				val:$("#grp_srch").val(),
				tst:$("#tst").val(),
				type:2,
			},
			function(data,status)
			{
				$("#group_load").html(data);
				//$("#ungrp_srch").focus();
			});
		}
	}
	function add_map(id)
	{
		if($("#tst").val()=="0")
		{
			$("#tst").select2("focus");
		}
		else if($("#qnt"+id).val()=="0" || $("#qnt"+id).val()=="" || parseInt($("#qnt"+id).val())<=0)
		{
			$("#qnt"+id).focus();
		}
		else
		{
			$.post("pages/inv_rad_indent_map_ajax.php",
			{
				tst:$("#tst").val(),
				qnt:$("#qnt"+id).val(),
				id:id,
				type:3,
			},
			function(data,status)
			{
				var d="";
				if(data>0)
				{
					d="Mapped";
				}
				else
				{
					d="Already exists";
				}
				$.gritter.add({
					//title:	'Normal notification',
					text:	'<h5 style="text-align:center;">'+d+'</h5>',
					time: 1000,
					sticky: false
				});
				if(data>0)
				{
					$(".gritter-item").css("background","#237438");
				}
				ungroup_load();
			});
		}
	}
	function rem_map(id)
	{
		if($("#tst").val()=="0")
		{
			$("#tst").select2("focus");
		}
		else
		{
			$.post("pages/inv_rad_indent_map_ajax.php",
			{
				tst:$("#tst").val(),
				id:id,
				type:4,
			},
			function(data,status)
			{
				var d="";
				if(data>0)
				{
					d="Removed";
				}
				else
				{
					d="Error";
				}
				$.gritter.add({
					//title:	'Normal notification',
					text:	'<h5 style="text-align:center;">'+d+'</h5>',
					time: 1000,
					sticky: false
				});
				if(data>0)
				{
					$(".gritter-item").css("background","#237438");
				}
				ungroup_load();
			});
		}
	}
	function report_xls(g,f,t)
	{
		var url="pages/gst_rep_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	function report_print_billwise(g,f,t)
	{
		splrid=$("#supplier").val();
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		url="pages/inv_supplier_ldger_rpt.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splrid+"&billno="+g;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>